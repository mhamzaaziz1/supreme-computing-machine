<?php

namespace App\Http\Controllers\Ops;

use App\Services\Ops\Approvals;
use App\Services\Ops\CreditPosition;
use App\Services\Ops\SaleGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Credit hold: the pre-flight check the sale screens run before posting,
 * and the two ways past a hold — a manager's PIN typed on the spot, or a
 * request that waits in the approvals inbox.
 */
class CreditController extends OpsController
{
    public function __construct(
        private CreditPosition $credit,
        private Approvals $approvals,
    ) {}

    public function check(Request $request): JsonResponse
    {
        $data = $request->validate([
            'contact_id' => ['required', 'integer'],
            'amount_due' => ['required', 'numeric'],
        ]);

        $this->outlet((int) $data['contact_id']);

        $evaluation = $this->credit->evaluate($this->businessId(), (int) $data['contact_id'], (float) $data['amount_due']);

        return response()->json([
            'ok' => $evaluation['ok'],
            'hold' => $evaluation['ok'] ? null : SaleGuard::holdPayload((int) $data['contact_id'], (float) $data['amount_due'], $evaluation),
        ]);
    }

    public function override(Request $request): JsonResponse
    {
        $data = $request->validate([
            'contact_id' => ['required', 'integer'],
            'amount_due' => ['required', 'numeric', 'min:0.01'],
            'pin' => ['required', 'string', 'max:20'],
            'reason' => ['nullable', 'string', 'max:190'],
        ]);

        $contact = $this->outlet((int) $data['contact_id'], ['name', 'supplier_business_name']);
        $approver = $this->approvals->approverByPin($this->businessId(), $data['pin']);

        if (! $approver) {
            return response()->json(['message' => 'That PIN does not belong to a manager who can approve overrides.'], 422);
        }

        $id = $this->createRequest($contact, (float) $data['amount_due'], $data['reason'] ?? null, 'pin');
        $row = $this->approvals->decide($this->businessId(), $id, true, $approver->id, 'Approved on the spot with staff PIN');

        return response()->json([
            'token' => $this->approvals->token($row),
            'approver' => trim($approver->first_name.' '.$approver->last_name),
        ]);
    }

    public function request(Request $request): JsonResponse
    {
        $data = $request->validate([
            'contact_id' => ['required', 'integer'],
            'amount_due' => ['required', 'numeric', 'min:0.01'],
            'reason' => ['nullable', 'string', 'max:190'],
        ]);

        $contact = $this->outlet((int) $data['contact_id'], ['name', 'supplier_business_name']);
        $id = $this->createRequest($contact, (float) $data['amount_due'], $data['reason'] ?? null, 'inbox');

        return response()->json(['approval_id' => $id, 'message' => 'Sent to the approvals inbox.']);
    }

    private function createRequest(object $contact, float $amount, ?string $reason, string $via): int
    {
        $evaluation = $this->credit->evaluate($this->businessId(), (int) $contact->id, $amount);

        return $this->approvals->request($this->businessId(), 'credit_override', sprintf(
            'Credit override for %s — order adds %s on credit',
            self::outletName($contact),
            number_format($amount, 0),
        ), [
            'contact_id' => (int) $contact->id,
            'amount' => $amount,
            'payload' => [
                'via' => $via,
                'reason' => $reason,
                'reasons' => $evaluation['reasons'],
                'outstanding' => $evaluation['position']['outstanding'],
                'limit' => $evaluation['position']['limit'],
            ],
        ]);
    }
}
