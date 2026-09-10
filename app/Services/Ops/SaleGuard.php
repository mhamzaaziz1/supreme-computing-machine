<?php

namespace App\Services\Ops;

use App\Utils\TransactionUtil;
use Illuminate\Support\Facades\DB;

/**
 * The checks a sale must pass before SellPosController@store creates it.
 *
 * Replaces the store's credit-limit-only check with the full credit rules
 * (limit, overdue ceiling, hold flag). The only way past a failed check is a
 * manager-approved override token, which is redeemed once and only covers
 * the amount the manager saw.
 *
 * A blocked sale is answered with the legacy {success: 0, msg} shape so
 * every existing caller still shows an error, plus a structured
 * `credit_hold` payload the new screens use to offer a way forward.
 */
class SaleGuard
{
    public function __construct(
        private CreditPosition $credit,
        private Approvals $approvals,
        private TransactionUtil $transactionUtil,
        private FieldCheckIn $checkIn,
    ) {}

    /**
     * @param  array<string, mixed>  $input  the store's request input
     * @return array{blocked: ?array, approval: ?object}
     */
    public function inspect(array $input, int $businessId): array
    {
        $result = ['blocked' => null, 'approval' => null];

        $status = $input['status'] ?? 'final';
        if ($status !== 'final' || ($input['type'] ?? null) === 'sales_order' || empty($input['contact_id'])) {
            return $result;
        }

        $orderDue = $this->orderDue($input);
        if ($orderDue <= 0.004) {
            return $result;
        }

        $contactId = (int) $input['contact_id'];

        // On an enforced route, a field seller must have checked in at this
        // outlet within the last few minutes before billing it.
        $userId = (int) auth()->id();
        if ($this->checkIn->requiresToken($businessId, $userId, $contactId)
            && ! $this->checkIn->verify($input['checkin_token'] ?? null, $businessId, $userId, $contactId)) {
            $result['blocked'] = [
                'success' => 0,
                'msg' => 'Check in at the outlet before billing it.',
                'checkin' => [
                    'contact_id' => $contactId,
                    'contact_name' => DB::table('contacts')->where('id', $contactId)
                        ->value(DB::raw("COALESCE(NULLIF(TRIM(supplier_business_name), ''), name)")),
                    'action' => 'place_order',
                ],
            ];

            return $result;
        }

        if (! empty($input['credit_override_token'])) {
            $approval = $this->approvals->redeemable($input['credit_override_token'], $businessId, $contactId, 'credit_override');
            if ($approval && (float) $approval->amount + 0.01 >= $orderDue) {
                $result['approval'] = $approval;

                return $result;
            }
        }

        $evaluation = $this->credit->evaluate($businessId, $contactId, $orderDue);
        if ($evaluation['ok']) {
            return $result;
        }

        $result['blocked'] = [
            'success' => 0,
            'msg' => $evaluation['reasons'][0]['message'],
            'credit_hold' => self::holdPayload($contactId, $orderDue, $evaluation),
        ];

        return $result;
    }

    /**
     * After the sale exists: mark any override as used, and record which
     * trade schemes the invoice benefited from.
     *
     * @param  array{blocked: ?array, approval: ?object}  $inspection
     * @param  array<string, mixed>  $input
     */
    public function settle(array $inspection, object $transaction, array $input = []): void
    {
        if (! empty($inspection['approval'])) {
            $this->approvals->consume((int) $inspection['approval']->id, 'sell:'.$transaction->id);
        }

        if (($transaction->type ?? null) === 'sell' && ($transaction->status ?? null) === 'final') {
            try {
                app(SchemeEngine::class)->recordFor((int) $transaction->business_id, $transaction, $input);
            } catch (\Throwable $e) {
                // Reporting only: never lose a saved sale over it.
                \Log::warning('Scheme recording failed for sell '.$transaction->id.': '.$e->getMessage());
            }
        }
    }

    /**
     * What the credit-hold modal needs to show the maths and the ways out.
     *
     * @param  array<string, mixed>  $evaluation
     * @return array<string, mixed>
     */
    public static function holdPayload(int $contactId, float $orderDue, array $evaluation): array
    {
        $p = $evaluation['position'];
        $name = DB::table('contacts')->where('id', $contactId)
            ->value(DB::raw("COALESCE(NULLIF(TRIM(supplier_business_name), ''), name)"));

        $minimumPayment = $orderDue;
        $codes = array_column($evaluation['reasons'], 'code');
        if ($codes === ['over_limit']) {
            // Only the limit is in the way: paying the excess clears it.
            $minimumPayment = $evaluation['reasons'][0]['over_by'];
        }

        return [
            'contact_id' => $contactId,
            'contact_name' => $name,
            'order_due' => round($orderDue, 2),
            'outstanding' => $p['outstanding'],
            'limit' => $p['limit'],
            'available' => $p['available'],
            'aging' => $p['aging'],
            'reasons' => $evaluation['reasons'],
            'minimum_payment' => round($minimumPayment, 2),
        ];
    }

    /**
     * What this sale would add to the outlet's balance: the invoice total
     * less what is being paid now. Mirrors the legacy check, including
     * is_credit_sale meaning "ignore the payment rows".
     *
     * @param  array<string, mixed>  $input
     */
    private function orderDue(array $input): float
    {
        $finalTotal = $this->transactionUtil->num_uf($input['final_total'] ?? 0);
        $isCreditSale = ! empty($input['is_credit_sale']) && (int) $input['is_credit_sale'] === 1;

        $paid = 0.0;
        if (! $isCreditSale && ! empty($input['payment']) && is_array($input['payment'])) {
            foreach ($input['payment'] as $payment) {
                if (($payment['method'] ?? null) === 'advance') {
                    continue;
                }
                $paid += $this->transactionUtil->num_uf($payment['amount'] ?? 0);
            }
        }

        return round($finalTotal - $paid, 4);
    }
}
