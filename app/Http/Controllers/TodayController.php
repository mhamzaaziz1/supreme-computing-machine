<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The operational board for the current day.
 *
 * Deliberately not a chart wall. Charts answer "how did last quarter go";
 * this screen answers "what needs me before I go home" — what sold, what
 * was collected, which routes are still running, and which exceptions are
 * waiting. Every figure links to the screen where you act on it.
 */
class TodayController extends Controller
{
    public function index(): Response
    {
        $businessId = request()->session()->get('user.business_id');
        $today = Carbon::today();

        return Inertia::render('Today/Index', [
            'date' => $today->toDateString(),
            'metrics' => $this->metrics($businessId, $today),
            'routes' => $this->routesToday($businessId, $today),
            'exceptions' => $this->exceptions($businessId, $today),
            'recentSales' => $this->recentSales($businessId),
            // Not "ops": that name is the shared prop the top bar reads, and a
            // page prop of the same name would replace it.
            'board' => $this->ops($businessId, $today),
            'links' => [
                'sells' => action([SellController::class, 'index']),
                'pos' => action([SellPosController::class, 'create']),
                'visitLogs' => action([RouteVisitLogController::class, 'index']),
                'violations' => action([GeofenceViolationLogController::class, 'index']),
                'contacts' => action([ContactController::class, 'index'], ['type' => 'customer']),
                'routes' => action([CustomerRouteController::class, 'index']),
                'field' => route('field'),
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function metrics(int $businessId, Carbon $today): array
    {
        $sales = DB::table('transactions')
            ->where('business_id', $businessId)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->whereDate('transaction_date', $today)
            ->selectRaw('COUNT(*) AS invoices, COALESCE(SUM(final_total), 0) AS total')
            ->first();

        $collected = DB::table('transaction_payments')
            ->where('business_id', $businessId)
            ->where('is_return', 0)
            ->whereDate('paid_on', $today)
            ->sum('amount');

        // Everything billed but not yet paid, across all time — the number
        // that actually decides whether a route is worth running tomorrow.
        $receivable = DB::table('transactions AS t')
            ->where('t.business_id', $businessId)
            ->where('t.type', 'sell')
            ->where('t.status', 'final')
            ->whereIn('t.payment_status', ['due', 'partial'])
            ->selectRaw(
                'COALESCE(SUM(t.final_total - COALESCE((
                    SELECT SUM(tp.amount) FROM transaction_payments tp
                    WHERE tp.transaction_id = t.id AND tp.is_return = 0
                ), 0)), 0) AS due'
            )
            ->value('due');

        $visits = DB::table('route_visit_logs')
            ->where('business_id', $businessId)
            ->whereDate('visit_time', $today)
            ->selectRaw('COUNT(*) AS visits, COUNT(DISTINCT contact_id) AS customers')
            ->first();

        return [
            'sales_total' => (float) ($sales->total ?? 0),
            'sales_count' => (int) ($sales->invoices ?? 0),
            'collected' => (float) $collected,
            'receivable' => (float) $receivable,
            'visits' => (int) ($visits->visits ?? 0),
            'customers_visited' => (int) ($visits->customers ?? 0),
        ];
    }

    /**
     * The field-operations queue: what needs doing today beyond the sales
     * figures. Each block is only computed for someone allowed to act on it.
     *
     * @return array<string, mixed>
     */
    private function ops(int $businessId, Carbon $today): array
    {
        $user = auth()->user();
        $admin = app(\App\Utils\Util::class)->is_admin($user, $businessId);
        $can = fn (string ...$perms) => $admin || $user->hasAnyPermission($perms);

        $cheques = null;
        if ($can('sell.payments')) {
            $row = DB::table('transaction_payments')->where('business_id', $businessId)->whereNull('parent_id')
                ->where('cheque_status', 'pending')->whereDate('cheque_date', '<=', $today->copy()->addDays(2))
                ->selectRaw('COUNT(*) AS n, COALESCE(SUM(amount), 0) AS amount')->first();
            $cheques = ['count' => (int) $row->n, 'amount' => (float) $row->amount];
        }

        $vans = null;
        if ($can('purchase.create', 'purchase.view')) {
            $list = app(\App\Services\Ops\VanStock::class)->vans($businessId, $today);
            $vans = [
                'total' => count($list),
                'loaded' => count(array_filter($list, fn ($v) => $v['loaded_today'] > 0)),
                'settled' => count(array_filter($list, fn ($v) => $v['settlement'] !== null)),
                'differences' => count(array_filter($list, fn ($v) => ($v['settlement']['status'] ?? null) === 'pending_approval')),
                'stock_value' => round(array_sum(array_column($list, 'value')), 2),
            ];
        }

        $customers = $can('customer.view', 'customer.view_own');
        $reports = $can('purchase_n_sell_report.view', 'sell.view');

        return [
            'cheques' => $cheques,
            'vans' => $vans,
            'service_due' => $customers ? count(app(\App\Services\Ops\ServiceDue::class)->dueList($businessId, 7, $today)) : null,
            'at_risk' => $customers ? app(\App\Services\Ops\BuyingPattern::class)->atRisk($businessId, $today, 6) : [],
            'targets' => $reports
                ? array_slice(array_values(array_filter(app(\App\Services\Ops\PrincipalReport::class)->targets($businessId, $today), fn ($t) => ! $t['ended'])), 0, 4)
                : [],
            'schemes_running' => DB::table('trade_schemes')->where('business_id', $businessId)->where('is_active', 1)
                ->whereDate('starts_on', '<=', $today)
                ->where(fn ($q) => $q->whereNull('ends_on')->orWhereDate('ends_on', '>=', $today))->count(),
            'can' => [
                'map' => $customers,
                'economics' => $can('profit_loss_report.view'),
                'principal' => $reports,
                'schemes' => $can('discount.access'),
            ],
        ];
    }

    /**
     * Routes with an active seller assignment, and how far each has got today.
     *
     * @return array<int, array<string, mixed>>
     */
    private function routesToday(int $businessId, Carbon $today): array
    {
        return DB::table('route_seller_assignments AS rsa')
            ->join('customer_routes AS cr', 'cr.id', '=', 'rsa.customer_route_id')
            ->leftJoin('users AS u', 'u.id', '=', 'rsa.user_id')
            ->where('rsa.business_id', $businessId)
            ->where('rsa.is_active', 1)
            ->where('cr.is_active', 1)
            ->selectRaw("
                cr.id,
                cr.name,
                TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) AS seller,
                (SELECT COUNT(*) FROM route_visit_logs rvl
                   WHERE rvl.customer_route_id = cr.id
                     AND DATE(rvl.visit_time) = ?) AS visits,
                (SELECT COUNT(*) FROM geofence_violation_logs gvl
                   WHERE gvl.customer_route_id = cr.id
                     AND DATE(gvl.created_at) = ?) AS violations
            ", [$today->toDateString(), $today->toDateString()])
            ->orderByDesc('visits')
            ->orderBy('cr.name')
            ->limit(12)
            ->get()
            ->map(fn ($r) => [
                'id' => (int) $r->id,
                'name' => $r->name,
                'seller' => $r->seller ?: null,
                'visits' => (int) $r->visits,
                'violations' => (int) $r->violations,
            ])
            ->all();
    }

    /**
     * Things that should not have happened today.
     *
     * @return array<int, array<string, mixed>>
     */
    private function exceptions(int $businessId, Carbon $today): array
    {
        return DB::table('geofence_violation_logs AS g')
            ->leftJoin('users AS u', 'u.id', '=', 'g.user_id')
            ->leftJoin('contacts AS c', 'c.id', '=', 'g.contact_id')
            ->leftJoin('customer_routes AS cr', 'cr.id', '=', 'g.customer_route_id')
            ->where('g.business_id', $businessId)
            ->whereDate('g.created_at', $today)
            ->selectRaw("
                g.id,
                g.violation_type,
                g.attempted_action,
                g.distance_from_valid,
                g.created_at,
                g.contact_id,
                TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) AS user,
                c.name AS customer,
                cr.name AS route
            ")
            ->orderByDesc('g.created_at')
            ->limit(8)
            ->get()
            ->map(fn ($r) => [
                'id' => (int) $r->id,
                'type' => $r->violation_type,
                'action' => $r->attempted_action,
                'distance' => $r->distance_from_valid !== null ? (float) $r->distance_from_valid : null,
                'at' => Carbon::parse($r->created_at)->format('H:i'),
                'user' => $r->user ?: null,
                'contact_id' => $r->contact_id ? (int) $r->contact_id : null,
                'customer' => $r->customer,
                'route' => $r->route,
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function recentSales(int $businessId): array
    {
        return DB::table('transactions AS t')
            ->leftJoin('contacts AS c', 'c.id', '=', 't.contact_id')
            ->where('t.business_id', $businessId)
            ->where('t.type', 'sell')
            ->where('t.status', 'final')
            ->select(
                't.id',
                't.invoice_no',
                't.final_total',
                't.payment_status',
                't.transaction_date',
                't.contact_id',
                'c.name AS customer',
            )
            ->orderByDesc('t.transaction_date')
            ->limit(8)
            ->get()
            ->map(fn ($r) => [
                'id' => (int) $r->id,
                'invoice_no' => $r->invoice_no,
                'total' => (float) $r->final_total,
                'payment_status' => $r->payment_status,
                'contact_id' => $r->contact_id ? (int) $r->contact_id : null,
                'customer' => $r->customer,
                'at' => Carbon::parse($r->transaction_date)->format('d M, H:i'),
            ])
            ->all();
    }
}
