<?php

namespace App\Http\Controllers\Ops;

use App\Services\Ops\CreditPosition;
use App\Services\Ops\Whatsapp;
use App\Utils\TransactionUtil;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Collect a payment from an outlet, oldest invoice first.
 *
 * The allocation itself is TransactionUtil::payContact — the same code the
 * legacy "Pay due" screen runs — so ledgers, payment statuses, account
 * transactions and advance balances behave exactly as they always have.
 * What this adds is the post-dated cheque detail (bank, date, status) and a
 * receipt the collector can send on WhatsApp.
 */
class CollectController extends OpsController
{
    public function __construct(
        private CreditPosition $credit,
        private TransactionUtil $transactionUtil,
    ) {}

    public function openItems(int $id): JsonResponse
    {
        $this->authorizeCollect();

        $c = $this->outlet($id, ['name', 'supplier_business_name', 'mobile']);
        $position = $this->credit->for($this->businessId(), $id);

        $methods = collect($this->transactionUtil->payment_types(null, false, $this->businessId()))
            ->map(fn ($label, $value) => ['value' => (string) $value, 'label' => $label])
            ->values();

        return response()->json([
            'outlet' => ['id' => $id, 'name' => self::outletName($c), 'mobile' => $c->mobile],
            'outstanding' => $position['outstanding'],
            'invoices' => $position['invoices'],
            'pdc' => $position['pdc'],
            'methods' => $methods,
            'now' => now()->format('Y-m-d\TH:i'),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorizeCollect();

        $methods = array_keys($this->transactionUtil->payment_types(null, false, $this->businessId()));

        $data = $request->validate([
            'contact_id' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'method' => ['required', 'string', 'in:'.implode(',', $methods)],
            'paid_on' => ['nullable', 'date'],
            'note' => ['nullable', 'string', 'max:190'],
            'cheque_number' => ['required_if:method,cheque', 'nullable', 'string', 'max:60'],
            'cheque_bank' => ['nullable', 'string', 'max:80'],
            'cheque_date' => ['required_if:method,cheque', 'nullable', 'date'],
            'bank_account_number' => ['nullable', 'string', 'max:60'],
        ], [
            'cheque_number.required_if' => 'Enter the cheque number.',
            'cheque_date.required_if' => 'Enter the date written on the cheque.',
        ]);

        $contact = $this->outlet((int) $data['contact_id'], ['name', 'supplier_business_name', 'mobile']);

        $result = DB::transaction(function () use ($data) {
            $sub = new Request([
                'contact_id' => (int) $data['contact_id'],
                'amount' => (float) $data['amount'],
                'method' => $data['method'],
                'note' => $data['note'] ?? null,
                'paid_on' => ! empty($data['paid_on']) ? date('Y-m-d H:i:s', strtotime($data['paid_on'])) : now()->toDateTimeString(),
                'cheque_number' => $data['cheque_number'] ?? null,
                'bank_account_number' => $data['bank_account_number'] ?? null,
                'due_payment_type' => 'sell',
            ]);

            $parent = $this->transactionUtil->payContact($sub, false);

            if ($data['method'] === 'cheque') {
                DB::table('transaction_payments')->where('id', $parent->id)->update([
                    'cheque_bank' => $data['cheque_bank'] ?? null,
                    'cheque_date' => $data['cheque_date'],
                    'cheque_status' => 'pending',
                    'cheque_status_at' => now(),
                ]);
            }

            $allocations = DB::table('transaction_payments AS tp')
                ->join('transactions AS t', 't.id', '=', 'tp.transaction_id')
                ->where('tp.parent_id', $parent->id)
                ->orderBy('t.transaction_date')
                ->get(['t.invoice_no', 't.ref_no', 't.type', 'tp.amount'])
                ->map(fn ($a) => [
                    'invoice_no' => $a->invoice_no ?: ($a->type === 'opening_balance' ? 'Opening balance' : $a->ref_no),
                    'amount' => (float) $a->amount,
                ])
                ->all();

            return ['parent' => $parent, 'allocations' => $allocations];
        });

        $amount = (float) $data['amount'];
        $allocated = array_sum(array_column($result['allocations'], 'amount'));
        $name = self::outletName($contact);
        $remaining = $this->credit->for($this->businessId(), (int) $contact->id)['outstanding'];

        $receipt = sprintf(
            "%s\nReceived %s from %s by %s%s on %s.\nRef %s. Balance now %s.\nThank you.",
            session('business.name'),
            $this->transactionUtil->num_f($amount, true),
            $name,
            $data['method'],
            $data['method'] === 'cheque' ? ' #'.$data['cheque_number'].' dated '.date('d M Y', strtotime($data['cheque_date'])) : '',
            date('d M Y', strtotime($result['parent']->paid_on)),
            $result['parent']->payment_ref_no,
            $this->transactionUtil->num_f($remaining, true),
        );

        return response()->json([
            'message' => sprintf('%s collected from %s.', $this->transactionUtil->num_f($amount, true), $name),
            'payment_ref' => $result['parent']->payment_ref_no,
            'allocations' => $result['allocations'],
            'advance' => round($amount - $allocated, 4),
            'outstanding' => $remaining,
            'whatsapp' => Whatsapp::link($contact->mobile, $receipt),
        ]);
    }

    private function authorizeCollect(): void
    {
        if (! $this->isAdmin() && ! auth()->user()->can('sell.payments')) {
            abort(403, 'You do not have permission to record payments.');
        }
    }
}
