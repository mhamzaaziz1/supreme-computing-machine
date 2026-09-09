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
            'links' => [
                'sells' => action([SellController::class, 'index']),
                'pos' => action([SellPosController::class, 'create']),
                'visitLogs' => action([RouteVisitLogController::class, 'index']),
                'violations' => action([GeofenceViolationLogController::class, 'index']),
                'contacts' => action([ContactController::class, 'index'], ['type' => 'customer']),
                'routes' => action([CustomerRouteController::class, 'index']),
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
                'customer' => $r->customer,
                'at' => Carbon::parse($r->transaction_date)->format('d M, H:i'),
            ])
            ->all();
    }
}
