<?php

namespace App\Services\Ops;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Where an outlet stands on credit, and whether the next sale may go ahead.
 *
 * The legacy check (TransactionUtil::isCustomerCreditLimitExeeded) only
 * compared the running balance against credit_limit. A route business also
 * needs to stop selling to an outlet whose oldest invoice is too old, and to
 * a holder of a bounced cheque, so this adds those two rules and exposes the
 * aging behind them so the screen can show the maths rather than a refusal.
 *
 * "Outstanding" here is the same figure the legacy check used: unpaid final
 * sells plus opening balance. Returns are not netted off, matching it.
 */
class CreditPosition
{
    /** Aging bucket upper bounds in days, by invoice date. */
    public const BUCKETS = ['0_30' => 30, '31_60' => 60, '61_90' => 90, '90_plus' => PHP_INT_MAX];

    /**
     * @return array<string, mixed>
     */
    public function for(int $businessId, int $contactId, ?Carbon $today = null): array
    {
        $today ??= Carbon::today();

        $contact = DB::table('contacts')
            ->where('business_id', $businessId)
            ->where('id', $contactId)
            ->first(['id', 'credit_limit', 'max_overdue_days', 'credit_hold', 'credit_hold_reason',
                'pay_term_number', 'pay_term_type', 'balance']);

        if (! $contact) {
            abort(404);
        }

        $invoices = $this->openInvoices($businessId, $contactId);

        // A limit of 0 means "no limit", exactly as the legacy check read it
        // (it compared with == null). "No credit at all" is the hold flag.
        $position = self::summarise(
            $invoices,
            (float) $contact->credit_limit > 0 ? (float) $contact->credit_limit : null,
            $contact->max_overdue_days !== null ? (int) $contact->max_overdue_days : null,
            [(int) ($contact->pay_term_number ?? 0), $contact->pay_term_type],
            $today,
        );

        $position['hold'] = [
            'flag' => (bool) $contact->credit_hold,
            'reason' => $contact->credit_hold_reason,
        ];
        $position['advance'] = (float) $contact->balance;
        $position['pdc'] = $this->chequesInHand($businessId, $contactId);
        $position['last_payment'] = $this->lastPayment($businessId, $contactId);

        return $position;
    }

    /**
     * Whether a sale that adds $additionalDue to the balance may go ahead.
     *
     * @return array{ok: bool, reasons: array<int, array<string, mixed>>, position: array<string, mixed>}
     */
    public function evaluate(int $businessId, int $contactId, float $additionalDue): array
    {
        $position = $this->for($businessId, $contactId);

        return [
            'ok' => ! ($reasons = self::reasons($position, $additionalDue)),
            'reasons' => $reasons,
            'position' => $position,
        ];
    }

    /**
     * The rules, as a pure function so they can be tested without a database.
     *
     * @param  array<string, mixed>  $position
     * @return array<int, array<string, mixed>>
     */
    public static function reasons(array $position, float $additionalDue): array
    {
        $reasons = [];

        // A paid-in-full sale never changes the exposure, so no rule applies.
        if ($additionalDue <= 0.004) {
            return [];
        }

        if (! empty($position['hold']['flag'])) {
            $reasons[] = [
                'code' => 'on_hold',
                'message' => $position['hold']['reason'] ?: 'This outlet is on credit hold.',
            ];
        }

        $limit = $position['limit'];
        $after = round($position['outstanding'] + $additionalDue, 4);

        if ($limit !== null && $after > $limit + 0.004) {
            $reasons[] = [
                'code' => 'over_limit',
                'message' => 'This order takes the outlet over its credit limit.',
                'outstanding' => $position['outstanding'],
                'order_due' => round($additionalDue, 4),
                'limit' => $limit,
                'over_by' => round($after - $limit, 4),
            ];
        }

        $maxDays = $position['max_overdue_days'];
        if ($maxDays !== null && $position['oldest_overdue_days'] > $maxDays) {
            $late = array_values(array_filter(
                $position['invoices'],
                fn ($i) => $i['overdue_days'] > $maxDays,
            ));

            $reasons[] = [
                'code' => 'overdue',
                'message' => sprintf('%d %s more than %d days overdue.',
                    count($late), count($late) === 1 ? 'invoice is' : 'invoices are', $maxDays),
                'max_overdue_days' => $maxDays,
                'invoices' => $late,
                'overdue_amount' => round(array_sum(array_column($late, 'due')), 4),
            ];
        }

        return $reasons;
    }

    /**
     * Aging and overdue figures from a list of open invoices.
     *
     * @param  array<int, array<string, mixed>>  $invoices  each: id, invoice_no, date (Y-m-d), total, paid, pay_term_number, pay_term_type
     * @param  array{0: int, 1: ?string}  $contactTerm  fallback payment term
     * @return array<string, mixed>
     */
    public static function summarise(array $invoices, ?float $limit, ?int $maxOverdueDays, array $contactTerm, Carbon $today): array
    {
        $buckets = array_fill_keys(array_keys(self::BUCKETS), 0.0);
        $rows = [];
        $outstanding = 0.0;
        $oldestOverdue = 0;

        foreach ($invoices as $inv) {
            $due = round((float) $inv['total'] - (float) $inv['paid'], 4);
            if ($due <= 0.004) {
                continue;
            }

            $date = Carbon::parse($inv['date'])->startOfDay();
            $age = (int) $date->diffInDays($today, false);

            [$termNumber, $termType] = ! empty($inv['pay_term_number'])
                ? [(int) $inv['pay_term_number'], $inv['pay_term_type']]
                : $contactTerm;

            $dueDate = $termNumber > 0
                ? ($termType === 'months' ? $date->copy()->addMonthsNoOverflow($termNumber) : $date->copy()->addDays($termNumber))
                : $date->copy();

            $overdue = max(0, (int) $dueDate->diffInDays($today, false));

            foreach (self::BUCKETS as $key => $upper) {
                if ($age <= $upper) {
                    $buckets[$key] += $due;
                    break;
                }
            }

            $outstanding += $due;
            $oldestOverdue = max($oldestOverdue, $overdue);

            $rows[] = [
                'id' => (int) $inv['id'],
                'invoice_no' => $inv['invoice_no'],
                'date' => $date->toDateString(),
                'due_date' => $dueDate->toDateString(),
                'total' => (float) $inv['total'],
                'due' => $due,
                'age_days' => max(0, $age),
                'overdue_days' => $overdue,
            ];
        }

        $outstanding = round($outstanding, 4);

        return [
            'outstanding' => $outstanding,
            'limit' => $limit,
            'available' => $limit !== null ? round($limit - $outstanding, 4) : null,
            'max_overdue_days' => $maxOverdueDays,
            'oldest_overdue_days' => $oldestOverdue,
            'aging' => array_map(fn ($v) => round($v, 4), $buckets),
            'invoices' => $rows,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function openInvoices(int $businessId, int $contactId): array
    {
        return DB::table('transactions AS t')
            ->where('t.business_id', $businessId)
            ->where('t.contact_id', $contactId)
            ->whereIn('t.type', ['sell', 'opening_balance'])
            ->where('t.status', 'final')
            ->whereIn('t.payment_status', ['due', 'partial'])
            ->orderBy('t.transaction_date')
            ->select(
                't.id',
                't.invoice_no',
                't.ref_no',
                't.type',
                't.transaction_date',
                't.final_total',
                't.pay_term_number',
                't.pay_term_type',
                DB::raw('COALESCE((SELECT SUM(IF(tp.is_return = 1, -1 * tp.amount, tp.amount))
                    FROM transaction_payments tp WHERE tp.transaction_id = t.id), 0) AS paid'),
            )
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'invoice_no' => $r->invoice_no ?: ($r->type === 'opening_balance' ? 'Opening balance' : $r->ref_no),
                'date' => substr((string) $r->transaction_date, 0, 10),
                'total' => (float) $r->final_total,
                'paid' => (float) $r->paid,
                'pay_term_number' => $r->pay_term_number,
                'pay_term_type' => $r->pay_term_type,
            ])
            ->all();
    }

    /**
     * Post-dated cheques received from this outlet and not yet cleared.
     *
     * A cheque paid against the contact (payContact) is one parent row with
     * payment_for set and children per invoice; one paid on an invoice has no
     * parent. Counting parents plus parentless rows counts each cheque once.
     *
     * @return array<string, mixed>
     */
    private function chequesInHand(int $businessId, int $contactId): array
    {
        $rows = DB::table('transaction_payments AS tp')
            ->leftJoin('transactions AS t', 't.id', '=', 'tp.transaction_id')
            ->where('tp.business_id', $businessId)
            ->whereNull('tp.parent_id')
            ->whereIn('tp.cheque_status', ['pending', 'deposited'])
            ->where(fn ($q) => $q->where('tp.payment_for', $contactId)->orWhere('t.contact_id', $contactId))
            ->orderBy('tp.cheque_date')
            ->get(['tp.id', 'tp.amount', 'tp.cheque_number', 'tp.cheque_bank', 'tp.cheque_date', 'tp.cheque_status']);

        return [
            'count' => $rows->count(),
            'amount' => round((float) $rows->sum('amount'), 4),
            'items' => $rows->map(fn ($r) => [
                'id' => (int) $r->id,
                'amount' => (float) $r->amount,
                'number' => $r->cheque_number,
                'bank' => $r->cheque_bank,
                'date' => $r->cheque_date,
                'status' => $r->cheque_status,
            ])->all(),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function lastPayment(int $businessId, int $contactId): ?array
    {
        $p = DB::table('transaction_payments AS tp')
            ->leftJoin('transactions AS t', 't.id', '=', 'tp.transaction_id')
            ->where('tp.business_id', $businessId)
            ->whereNull('tp.parent_id')
            ->where('tp.is_return', 0)
            ->where(fn ($q) => $q->where('tp.payment_for', $contactId)->orWhere('t.contact_id', $contactId))
            ->orderByDesc('tp.paid_on')
            ->first(['tp.amount', 'tp.paid_on', 'tp.method']);

        return $p ? ['amount' => (float) $p->amount, 'date' => $p->paid_on, 'method' => $p->method] : null;
    }
}
