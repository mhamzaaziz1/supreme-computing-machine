<?php

namespace App\Http\Controllers\Ops;

use App\TransactionPayment;
use App\Utils\TransactionUtil;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * The post-dated cheque register.
 *
 * A cheque moves pending → deposited → cleared. If it bounces, the payment
 * it created is reversed (deleted through the same TransactionPayment path
 * the legacy screen uses, so invoice statuses and account entries unwind),
 * a record is kept in bounced_cheques, and the outlet goes on credit hold.
 */
class ChequeController extends OpsController
{
    public function __construct(private TransactionUtil $transactionUtil) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorizeCheques();

        $status = in_array($request->input('status'), ['pending', 'deposited', 'cleared', 'bounced', 'open'], true)
            ? $request->input('status') : 'open';
        $today = Carbon::today();

        if ($status === 'bounced') {
            $items = DB::table('bounced_cheques AS b')
                ->join('contacts AS c', 'c.id', '=', 'b.contact_id')
                ->where('b.business_id', $this->businessId())
                ->orderByDesc('b.created_at')->limit(100)
                ->get(['b.id', 'b.amount', 'b.cheque_number', 'b.cheque_bank', 'b.cheque_date', 'b.created_at', 'b.note',
                    'c.id AS contact_id', 'c.name', 'c.supplier_business_name'])
                ->map(fn ($r) => [
                    'id' => (int) $r->id,
                    'amount' => (float) $r->amount,
                    'number' => $r->cheque_number,
                    'bank' => $r->cheque_bank,
                    'date' => $r->cheque_date,
                    'status' => 'bounced',
                    'status_at' => (string) $r->created_at,
                    'note' => $r->note,
                    'contact_id' => (int) $r->contact_id,
                    'contact' => trim($r->supplier_business_name ?: $r->name),
                ]);
        } else {
            $items = DB::table('transaction_payments AS tp')
                ->leftJoin('transactions AS t', 't.id', '=', 'tp.transaction_id')
                ->leftJoin('contacts AS c', 'c.id', '=', DB::raw('COALESCE(tp.payment_for, t.contact_id)'))
                ->where('tp.business_id', $this->businessId())
                ->whereNull('tp.parent_id')
                ->when(
                    $status === 'open',
                    fn ($q) => $q->whereIn('tp.cheque_status', ['pending', 'deposited']),
                    fn ($q) => $q->where('tp.cheque_status', $status),
                )
                ->orderBy('tp.cheque_date')->limit(200)
                ->get(['tp.id', 'tp.amount', 'tp.cheque_number', 'tp.cheque_bank', 'tp.cheque_date', 'tp.cheque_status',
                    'tp.cheque_status_at', 'tp.payment_ref_no', 'c.id AS contact_id', 'c.name', 'c.supplier_business_name'])
                ->map(fn ($r) => [
                    'id' => (int) $r->id,
                    'amount' => (float) $r->amount,
                    'number' => $r->cheque_number,
                    'bank' => $r->cheque_bank,
                    'date' => $r->cheque_date,
                    'days' => $r->cheque_date ? (int) $today->diffInDays(Carbon::parse($r->cheque_date), false) : null,
                    'status' => $r->cheque_status,
                    'status_at' => (string) $r->cheque_status_at,
                    'ref' => $r->payment_ref_no,
                    'contact_id' => $r->contact_id ? (int) $r->contact_id : null,
                    'contact' => trim(($r->supplier_business_name ?: $r->name) ?? '') ?: null,
                ]);
        }

        return response()->json([
            'status' => $status,
            'items' => $items->values(),
            'totals' => [
                'count' => $items->count(),
                'amount' => round((float) $items->sum('amount'), 2),
                'due_now' => round((float) $items->filter(fn ($i) => isset($i['days']) && $i['days'] <= 0 && $i['status'] === 'pending')->sum('amount'), 2),
            ],
        ]);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $this->authorizeCheques();

        $data = $request->validate([
            'status' => ['required', 'in:deposited,cleared,bounced'],
            'note' => ['nullable', 'string', 'max:190'],
        ]);

        $payment = TransactionPayment::where('business_id', $this->businessId())
            ->whereNull('parent_id')
            ->whereNotNull('cheque_status')
            ->findOrFail($id);

        if ($payment->cheque_status === 'cleared' && $data['status'] !== 'bounced') {
            abort(409, 'This cheque has already cleared.');
        }

        if ($data['status'] !== 'bounced') {
            $payment->forceFill(['cheque_status' => $data['status'], 'cheque_status_at' => now()])->save();

            return response()->json(['message' => $data['status'] === 'deposited' ? 'Marked as deposited.' : 'Marked as cleared.']);
        }

        $contactId = $payment->payment_for ?: optional($payment->transaction)->contact_id;

        DB::transaction(function () use ($payment, $contactId, $data) {
            DB::table('bounced_cheques')->insert([
                'business_id' => $this->businessId(),
                'contact_id' => $contactId,
                'cheque_number' => $payment->cheque_number,
                'cheque_bank' => $payment->cheque_bank,
                'cheque_date' => $payment->cheque_date,
                'amount' => $payment->amount,
                'payment_ref_no' => $payment->payment_ref_no,
                'note' => $data['note'] ?? null,
                'recorded_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Same reversal as TransactionPaymentController@destroy.
            if (! empty($payment->transaction_id)) {
                TransactionPayment::deletePayment($payment);
            } else {
                $children = TransactionPayment::where('parent_id', $payment->id)->get();
                $advanceShare = $payment->amount - $children->sum('amount');
                if ($advanceShare > 0) {
                    $this->transactionUtil->updateContactBalance($payment->payment_for, $advanceShare, 'deduct');
                }
                foreach ($children as $child) {
                    $child->parent_id = null;
                    TransactionPayment::deletePayment($child);
                }
                TransactionPayment::deletePayment($payment);
            }

            DB::table('contacts')->where('id', $contactId)->update([
                'credit_hold' => true,
                'credit_hold_reason' => sprintf('Cheque #%s for %s bounced on %s',
                    $payment->cheque_number ?: '?', number_format((float) $payment->amount, 0), now()->format('d M Y')),
                'updated_at' => now(),
            ]);
        });

        return response()->json([
            'message' => 'Cheque recorded as bounced. The payment was reversed and the outlet is on credit hold.',
        ]);
    }

    private function authorizeCheques(): void
    {
        if (! $this->isAdmin() && ! auth()->user()->can('sell.payments')) {
            abort(403, 'You do not have permission to manage cheques.');
        }
    }
}
