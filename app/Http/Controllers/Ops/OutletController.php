<?php

namespace App\Http\Controllers\Ops;

use App\Http\Controllers\ContactController;
use App\Services\Ops\BuyingPattern;
use App\Services\Ops\CreditPosition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Outlet 360: everything needed to decide what to do with one outlet, in
 * one response, for the drawer that opens from any customer name.
 */
class OutletController extends OpsController
{
    public function __construct(
        private CreditPosition $credit,
        private BuyingPattern $pattern,
    ) {}

    public function show(int $id): JsonResponse
    {
        $businessId = $this->businessId();
        $c = $this->outlet($id);
        $today = Carbon::today();

        $route = $c->customer_route_id
            ? DB::table('customer_routes')->where('id', $c->customer_route_id)->first(['id', 'name'])
            : null;

        $stop = null;
        if ($route) {
            $seq = DB::table('route_outlet_sequence')->where('customer_route_id', $route->id)
                ->orderBy('sequence_number')->pluck('contact_id')->all();
            $at = array_search($c->id, $seq);
            $stop = $at === false ? null : ['number' => $at + 1, 'of' => count($seq)];
        }

        $group = $c->customer_group_id
            ? DB::table('customer_groups')->where('id', $c->customer_group_id)->value('name')
            : null;

        $pattern = $this->pattern->forContact($businessId, $id, $today);

        return response()->json([
            'outlet' => [
                'id' => (int) $c->id,
                'name' => self::outletName($c),
                'person' => trim((string) $c->name) ?: null,
                'code' => $c->contact_id,
                'mobile' => $c->mobile,
                'address' => trim(implode(', ', array_filter([$c->address_line_1 ?? null, $c->city ?? null]))) ?: null,
                'group' => $group,
                'route' => $route ? ['id' => (int) $route->id, 'name' => $route->name] : null,
                'stop' => $stop,
                'has_location' => $c->latitude !== null && $c->longitude !== null,
                'status' => $c->contact_status,
            ],
            'credit' => $this->credit->for($businessId, $id, $today),
            'rhythm' => $pattern['rhythm'],
            'stopped' => $pattern['stopped'],
            'basket' => $pattern['basket'],
            'schemes' => app()->bound(\App\Services\Ops\SchemeEngine::class)
                ? app(\App\Services\Ops\SchemeEngine::class)->progressFor($businessId, $id)
                : [],
            'followups' => $this->followups($businessId, $id, $today),
            'vehicles' => $this->vehicles($businessId, $id),
            'timeline' => $this->timeline($businessId, $id),
            'links' => [
                'sell' => route('sales.create', ['contact_id' => $id]),
                'contact' => action([ContactController::class, 'show'], [$id]),
                'statement' => url('contacts/ledger').'?'.http_build_query([
                    'contact_id' => $id,
                    'start_date' => $today->copy()->subDays(90)->toDateString(),
                    'end_date' => $today->toDateString(),
                    'format' => 'format_1',
                    'action' => 'pdf',
                ]),
            ],
            'can' => [
                'edit_credit' => $this->isAdmin() || auth()->user()->can('customer.update'),
                'collect' => $this->isAdmin() || auth()->user()->can('sell.payments'),
                'sell' => $this->isAdmin() || auth()->user()->can('direct_sell.access'),
            ],
        ]);
    }

    /**
     * Just the buying pattern, for the sale form: the usual order to add in
     * one click and the SKUs to ask about.
     */
    public function pattern(int $id): JsonResponse
    {
        $this->outlet($id);

        return response()->json($this->pattern->forContact($this->businessId(), $id));
    }

    /**
     * Credit controls, edited from the popover on the drawer.
     */
    public function updateCredit(Request $request, int $id): JsonResponse
    {
        if (! $this->isAdmin() && ! auth()->user()->can('customer.update')) {
            abort(403, 'You do not have permission to change credit terms.');
        }

        $this->outlet($id);

        $data = $request->validate([
            'credit_limit' => ['nullable', 'numeric', 'min:0'],
            'max_overdue_days' => ['nullable', 'integer', 'min:0', 'max:3650'],
            'credit_hold' => ['boolean'],
            'credit_hold_reason' => ['nullable', 'string', 'max:190'],
        ]);

        $hold = (bool) ($data['credit_hold'] ?? false);

        DB::table('contacts')->where('id', $id)->update([
            'credit_limit' => $data['credit_limit'] ?? null,
            'max_overdue_days' => $data['max_overdue_days'] ?? null,
            'credit_hold' => $hold,
            'credit_hold_reason' => $hold ? ($data['credit_hold_reason'] ?? 'Put on hold by '.auth()->user()->first_name) : null,
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Credit terms saved.',
            'credit' => $this->credit->for($this->businessId(), $id),
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function followups(int $businessId, int $id, Carbon $today): array
    {
        return DB::table('route_followups AS f')
            ->leftJoin('users AS u', 'u.id', '=', 'f.user_id')
            ->where('f.business_id', $businessId)
            ->where('f.contact_id', $id)
            ->where('f.followup_date', '>=', $today->copy()->subDays(14))
            ->orderBy('f.followup_date')
            ->limit(5)
            ->get(['f.id', 'f.notes', 'f.followup_date', 'u.first_name'])
            ->map(fn ($f) => [
                'id' => (int) $f->id,
                'notes' => $f->notes,
                'date' => $f->followup_date,
                'by' => $f->first_name,
                'overdue' => Carbon::parse($f->followup_date)->lt($today),
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function vehicles(int $businessId, int $id): array
    {
        $vehicles = DB::table('customer_vehicles')
            ->where('business_id', $businessId)
            ->where('contact_id', $id)
            ->get(['id', 'make', 'model', 'license_plate']);

        if ($vehicles->isEmpty()) {
            return [];
        }

        $due = app(\App\Services\Ops\ServiceDue::class)->forVehicles($businessId, $vehicles->pluck('id')->all());

        return $vehicles->map(fn ($v) => [
            'id' => (int) $v->id,
            'plate' => $v->license_plate,
            'name' => trim($v->make.' '.$v->model) ?: null,
            'service' => $due[$v->id] ?? null,
        ])->all();
    }

    /**
     * The last dozen things that happened with this outlet, newest first.
     *
     * @return array<int, array<string, mixed>>
     */
    private function timeline(int $businessId, int $id): array
    {
        $events = collect();

        DB::table('route_visit_logs AS v')->leftJoin('users AS u', 'u.id', '=', 'v.user_id')
            ->where('v.business_id', $businessId)->where('v.contact_id', $id)
            ->orderByDesc('v.visit_time')->limit(8)
            ->get(['v.visit_type', 'v.visit_time', 'v.notes', 'u.first_name'])
            ->each(fn ($v) => $events->push([
                'at' => (string) $v->visit_time,
                'kind' => 'visit',
                'text' => ucfirst(str_replace('_', ' ', $v->visit_type)).($v->first_name ? ' · '.$v->first_name : '').($v->notes ? ' — '.$v->notes : ''),
            ]));

        DB::table('transactions AS t')->leftJoin('users AS u', 'u.id', '=', 't.created_by')
            ->where('t.business_id', $businessId)->where('t.contact_id', $id)
            ->where('t.type', 'sell')->where('t.status', 'final')
            ->orderByDesc('t.transaction_date')->limit(8)
            ->get(['t.id', 't.invoice_no', 't.transaction_date', 't.final_total', 't.payment_status', 'u.first_name'])
            ->each(fn ($t) => $events->push([
                'at' => (string) $t->transaction_date,
                'kind' => 'sale',
                'amount' => (float) $t->final_total,
                'text' => 'Invoice '.$t->invoice_no.' · '.$t->payment_status.($t->first_name ? ' · '.$t->first_name : ''),
                'sell_id' => (int) $t->id,
            ]));

        DB::table('transaction_payments AS tp')->leftJoin('transactions AS t', 't.id', '=', 'tp.transaction_id')
            ->where('tp.business_id', $businessId)->whereNull('tp.parent_id')->where('tp.is_return', 0)
            ->where(fn ($q) => $q->where('tp.payment_for', $id)->orWhere('t.contact_id', $id))
            ->orderByDesc('tp.paid_on')->limit(8)
            ->get(['tp.amount', 'tp.paid_on', 'tp.method', 'tp.cheque_number', 'tp.cheque_date'])
            ->each(fn ($p) => $events->push([
                'at' => (string) $p->paid_on,
                'kind' => 'payment',
                'amount' => (float) $p->amount,
                'text' => ucfirst((string) $p->method).($p->cheque_number ? ' #'.$p->cheque_number : '').($p->cheque_date ? ' · due '.Carbon::parse($p->cheque_date)->format('d M') : ''),
            ]));

        DB::table('route_followups')->where('business_id', $businessId)->where('contact_id', $id)
            ->orderByDesc('created_at')->limit(5)
            ->get(['notes', 'created_at'])
            ->each(fn ($f) => $events->push([
                'at' => (string) $f->created_at,
                'kind' => 'followup',
                'text' => 'Follow-up: '.($f->notes ?: 'no notes'),
            ]));

        DB::table('geofence_violation_logs')->where('business_id', $businessId)->where('contact_id', $id)
            ->orderByDesc('created_at')->limit(5)
            ->get(['violation_type', 'distance_from_valid', 'created_at', 'details'])
            ->each(fn ($g) => $events->push([
                'at' => (string) $g->created_at,
                'kind' => 'violation',
                'text' => ucfirst(str_replace('_', ' ', $g->violation_type)).($g->distance_from_valid ? ' · '.round($g->distance_from_valid).' m away' : ''),
            ]));

        return $events->sortByDesc('at')->take(12)->values()->all();
    }
}
