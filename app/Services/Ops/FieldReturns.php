<?php

namespace App\Services\Ops;

use App\Exceptions\OpsException;
use App\Transaction;
use App\Utils\TransactionUtil;
use Illuminate\Support\Facades\DB;

/**
 * Returns claimed at the outlet, posted only once a manager approves.
 *
 * A seller picks the invoice and the lines coming back; that becomes an
 * approval request. On approval it goes through TransactionUtil::addSellReturn,
 * the same code the legacy return screen uses, so stock goes back to the
 * location the sale came from (the van, for a van sale) and is reconciled
 * at the van's settlement like everything else.
 *
 * addSellReturn keeps one return per sale and SETS each line's
 * quantity_returned from what it is given, so the posting re-sends every
 * previously returned line with its running total plus the new quantity.
 */
class FieldReturns
{
    public function __construct(
        private Approvals $approvals,
        private TransactionUtil $transactionUtil,
    ) {}

    /**
     * Recent invoices for an outlet with what can still come back.
     *
     * @return array<int, array<string, mixed>>
     */
    public function returnable(int $businessId, int $contactId, int $limit = 10): array
    {
        $sells = DB::table('transactions')
            ->where('business_id', $businessId)->where('contact_id', $contactId)
            ->where('type', 'sell')->where('status', 'final')
            ->orderByDesc('transaction_date')->limit($limit)
            ->get(['id', 'invoice_no', 'transaction_date', 'final_total']);

        $lines = $this->lines($sells->pluck('id')->all())->groupBy('transaction_id');

        return $sells->map(fn ($s) => [
            'id' => (int) $s->id,
            'invoice_no' => $s->invoice_no,
            'date' => substr((string) $s->transaction_date, 0, 10),
            'total' => (float) $s->final_total,
            'lines' => ($lines[$s->id] ?? collect())->filter(fn ($l) => $l['returnable'] > 0)->values()->all(),
        ])->filter(fn ($s) => count($s['lines']) > 0)->values()->all();
    }

    /**
     * @param  array<int, float>  $quantities  sell line id => quantity to return
     */
    public function request(int $businessId, int $contactId, int $transactionId, array $quantities, string $reason, int $userId): int
    {
        $sell = DB::table('transactions')->where('business_id', $businessId)->where('id', $transactionId)
            ->where('contact_id', $contactId)->where('type', 'sell')->where('status', 'final')
            ->first(['id', 'invoice_no']);
        if (! $sell) {
            throw new OpsException('That invoice is not one of this outlet\'s sales.');
        }

        $available = $this->lines([$transactionId])->keyBy('sell_line_id');
        $claimed = [];
        $value = 0;

        foreach ($quantities as $lineId => $qty) {
            $qty = (float) $qty;
            if ($qty <= 0) {
                continue;
            }
            $line = $available[(int) $lineId] ?? null;
            if (! $line) {
                throw new OpsException('One of those lines is not on the invoice.');
            }
            if ($qty > $line['returnable'] + 0.0001) {
                throw new OpsException(sprintf('Only %s of %s can still be returned.', rtrim(rtrim(number_format($line['returnable'], 2), '0'), '.'), $line['name']));
            }
            $claimed[] = ['sell_line_id' => (int) $lineId, 'variation_id' => $line['variation_id'], 'name' => $line['name'],
                'quantity' => $qty, 'unit_price_inc_tax' => $line['unit_price_inc_tax']];
            $value += $qty * $line['unit_price_inc_tax'];
        }

        if (! $claimed) {
            throw new OpsException('Enter a quantity for at least one line.');
        }

        $name = DB::table('contacts')->where('id', $contactId)->value(DB::raw("COALESCE(NULLIF(TRIM(supplier_business_name), ''), name)"));

        return $this->approvals->request($businessId, 'field_return', sprintf(
            'Return from %s on %s: %s',
            $name, $sell->invoice_no, implode(', ', array_map(fn ($c) => rtrim(rtrim(number_format($c['quantity'], 2), '0'), '.').' × '.$c['name'], $claimed)),
        ), [
            'contact_id' => $contactId,
            'subject_type' => 'sell',
            'subject_id' => $transactionId,
            'amount' => round($value, 2),
            'payload' => ['transaction_id' => $transactionId, 'lines' => $claimed, 'reason' => $reason],
            'requested_by' => $userId,
        ]);
    }

    public function postApproved(object $approval): void
    {
        $payload = json_decode($approval->payload, true);
        $sell = Transaction::where('business_id', $approval->business_id)->with('sell_lines')->find($payload['transaction_id']);
        if (! $sell) {
            throw new OpsException('The invoice for this return no longer exists.');
        }

        $multipliers = $this->multipliers($sell->id);
        $requested = array_column($payload['lines'], 'quantity', 'sell_line_id');

        $products = [];
        foreach ($sell->sell_lines as $line) {
            $already = (float) $line->quantity_returned / ($multipliers[$line->id] ?? 1);
            $total = $already + (float) ($requested[$line->id] ?? 0);

            if ($total > (float) $line->quantity + 0.0001) {
                throw new OpsException('Part of this return has already been returned another way. Ask the seller to raise it again.');
            }
            if ($total > 0) {
                $products[] = ['sell_line_id' => $line->id, 'quantity' => $total, 'unit_price_inc_tax' => (float) $line->unit_price_inc_tax];
            }
        }

        $return = $this->transactionUtil->addSellReturn([
            'transaction_id' => $sell->id,
            'products' => $products,
            'discount_type' => 'fixed',
            'discount_amount' => 0,
            'tax_id' => null,
        ], (int) $approval->business_id, (int) $approval->decided_by, false);

        $this->approvals->consume((int) $approval->id, 'sell_return:'.$return->id);
    }

    public function rejected(object $approval): void
    {
        // Nothing moved when it was requested, so nothing to undo.
    }

    /**
     * @param  array<int, int>  $transactionIds
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    private function lines(array $transactionIds)
    {
        if (! $transactionIds) {
            return collect();
        }

        return DB::table('transaction_sell_lines AS l')
            ->join('products AS p', 'p.id', '=', 'l.product_id')
            ->leftJoin('variations AS v', 'v.id', '=', 'l.variation_id')
            ->leftJoin('units AS u', 'u.id', '=', 'l.sub_unit_id')
            ->whereIn('l.transaction_id', $transactionIds)
            ->whereNull('l.parent_sell_line_id')
            ->get(['l.id', 'l.transaction_id', 'l.variation_id', 'l.quantity', 'l.quantity_returned', 'l.unit_price_inc_tax',
                'p.name', 'v.name AS variation', 'u.base_unit_multiplier'])
            ->map(function ($l) {
                $returned = (float) $l->quantity_returned / ((float) $l->base_unit_multiplier ?: 1);

                return [
                    'transaction_id' => (int) $l->transaction_id,
                    'sell_line_id' => (int) $l->id,
                    'variation_id' => (int) $l->variation_id,
                    'name' => trim($l->name.(($l->variation && $l->variation !== 'DUMMY') ? ' ('.$l->variation.')' : '')),
                    'sold' => (float) $l->quantity,
                    'returned' => round($returned, 4),
                    'returnable' => round(max(0, (float) $l->quantity - $returned), 4),
                    'unit_price_inc_tax' => (float) $l->unit_price_inc_tax,
                ];
            });
    }

    /**
     * @return array<int, float>  sell line id => base unit multiplier
     */
    private function multipliers(int $transactionId): array
    {
        return DB::table('transaction_sell_lines AS l')->leftJoin('units AS u', 'u.id', '=', 'l.sub_unit_id')
            ->where('l.transaction_id', $transactionId)
            ->selectRaw('l.id, COALESCE(u.base_unit_multiplier, 1) AS multiplier')
            ->pluck('multiplier', 'id')
            ->map(fn ($m) => (float) $m ?: 1.0)
            ->all();
    }
}
