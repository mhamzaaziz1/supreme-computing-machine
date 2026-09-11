<?php

namespace App\Http\Controllers\Ops;

use App\Services\Ops\ApprovalEffects;
use App\Services\Ops\Approvals;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * The approvals inbox: one bell for every exception a manager decides.
 */
class ApprovalController extends OpsController
{
    private const LABELS = [
        'credit_override' => 'Credit override',
        'van_variance' => 'Van variance',
        'field_return' => 'Field return',
    ];

    public function __construct(private Approvals $approvals) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorizeApprover();

        $rows = DB::table('ops_approvals AS a')
            ->leftJoin('users AS r', 'r.id', '=', 'a.requested_by')
            ->leftJoin('users AS d', 'd.id', '=', 'a.decided_by')
            ->leftJoin('contacts AS c', 'c.id', '=', 'a.contact_id')
            ->where('a.business_id', $this->businessId())
            ->when(
                $request->input('status') === 'decided',
                fn ($q) => $q->where('a.status', '!=', 'pending')->orderByDesc('a.decided_at'),
                fn ($q) => $q->where('a.status', 'pending')->orderBy('a.created_at'),
            )
            ->limit(40)
            ->get(['a.*', 'r.first_name AS requester', 'd.first_name AS decider',
                'c.name AS contact_name', 'c.supplier_business_name']);

        return response()->json([
            'items' => $rows->map(fn ($r) => [
                'id' => (int) $r->id,
                'type' => $r->type,
                'type_label' => self::LABELS[$r->type] ?? ucfirst(str_replace('_', ' ', $r->type)),
                'status' => $r->status,
                'summary' => $r->summary,
                'amount' => $r->amount !== null ? (float) $r->amount : null,
                'contact_id' => $r->contact_id ? (int) $r->contact_id : null,
                'contact' => trim($r->supplier_business_name ?: $r->contact_name) ?: null,
                'requester' => $r->requester,
                'decider' => $r->decider,
                'note' => $r->decision_note,
                'payload' => $r->payload ? json_decode($r->payload, true) : null,
                'ago' => Carbon::parse($r->created_at)->diffForHumans(),
                'consumed' => $r->consumed_at !== null,
            ])->all(),
            'pending' => $this->approvals->pendingCount($this->businessId()),
        ]);
    }

    public function count(): JsonResponse
    {
        return response()->json([
            'pending' => Approvals::canApprove(auth()->user()) ? $this->approvals->pendingCount($this->businessId()) : 0,
        ]);
    }

    /**
     * A requester polling for the answer to their own request.
     */
    public function status(int $id): JsonResponse
    {
        $row = $this->approvals->find($this->businessId(), $id);

        if ((int) $row->requested_by !== (int) auth()->id() && ! Approvals::canApprove(auth()->user())) {
            abort(403);
        }

        $token = null;
        if ($row->status === 'approved' && $row->type === 'credit_override' && $row->consumed_at === null
            && (int) $row->requested_by === (int) auth()->id()) {
            $token = $this->approvals->token($row);
        }

        return response()->json([
            'status' => $row->status,
            'note' => $row->decision_note,
            'token' => $token,
        ]);
    }

    public function decide(Request $request, int $id, ApprovalEffects $effects): JsonResponse
    {
        $this->authorizeApprover();

        $data = $request->validate([
            'approve' => ['required', 'boolean'],
            'note' => ['nullable', 'string', 'max:190'],
        ]);

        $row = DB::transaction(function () use ($id, $data, $effects) {
            $row = $this->approvals->decide($this->businessId(), $id, (bool) $data['approve'], (int) auth()->id(), $data['note'] ?? null);
            $effects->apply($row);

            return $row;
        });

        return response()->json([
            'message' => $row->status === 'approved' ? 'Approved.' : 'Rejected.',
            'pending' => $this->approvals->pendingCount($this->businessId()),
        ]);
    }

    private function authorizeApprover(): void
    {
        if (! Approvals::canApprove(auth()->user())) {
            abort(403, 'Only managers can see the approvals inbox.');
        }
    }
}
