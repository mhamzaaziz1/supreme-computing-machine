<?php

namespace App\Services\Ops;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * What each route earns against what it costs to run.
 *
 * A route that looks busy can still lose money once its van is paid for.
 * Per route, over a period: what its outlets bought, the gross margin on
 * it (sell price less the purchase cost of the stock actually mapped to
 * each sale), the van's expenses and kilometres, collections, and what is
 * still owed — ending in net margin per unit and, where products carry a
 * pack size, per litre.
 */
class RouteEconomics
{
    /**
     * @return array<string, mixed>
     */
    public function forPeriod(int $businessId, Carbon $from, Carbon $to): array
    {
        $routes = DB::table('customer_routes')->where('business_id', $businessId)->orderBy('name')->get(['id', 'name']);
        $hasLitres = \Illuminate\Support\Facades\Schema::hasColumn('products', 'pack_litres');

        $range = [$from->copy()->startOfDay(), $to->copy()->endOfDay()];

        $sales = DB::table('transaction_sell_lines AS l')
            ->join('transactions AS t', 't.id', '=', 'l.transaction_id')
            ->join('contacts AS c', 'c.id', '=', 't.contact_id')
            ->join('products AS p', 'p.id', '=', 'l.product_id')
            ->where('t.business_id', $businessId)->where('t.type', 'sell')->where('t.status', 'final')
            ->whereBetween('t.transaction_date', $range)->whereNotNull('c.customer_route_id')
            ->whereNull('l.parent_sell_line_id')
            ->groupBy('c.customer_route_id')
            ->selectRaw('c.customer_route_id AS route_id,
                SUM((l.quantity - l.quantity_returned) * l.unit_price) AS revenue,
                SUM(l.quantity - l.quantity_returned) AS units,
                '.($hasLitres ? 'SUM((l.quantity - l.quantity_returned) * COALESCE(p.pack_litres, 0))' : '0').' AS litres,
                COUNT(DISTINCT t.id) AS invoices,
                COUNT(DISTINCT t.contact_id) AS buyers')
            ->get()->keyBy('route_id');

        $cost = DB::table('transaction_sell_lines_purchase_lines AS m')
            ->join('transaction_sell_lines AS l', 'l.id', '=', 'm.sell_line_id')
            ->join('purchase_lines AS pl', 'pl.id', '=', 'm.purchase_line_id')
            ->join('transactions AS t', 't.id', '=', 'l.transaction_id')
            ->join('contacts AS c', 'c.id', '=', 't.contact_id')
            ->where('t.business_id', $businessId)->where('t.type', 'sell')->where('t.status', 'final')
            ->whereBetween('t.transaction_date', $range)->whereNotNull('c.customer_route_id')
            ->groupBy('c.customer_route_id')
            ->selectRaw('c.customer_route_id AS route_id, SUM((m.quantity - m.qty_returned) * pl.purchase_price) AS cost')
            ->pluck('cost', 'route_id');

        $vanCost = DB::table('supply_chain_vehicle_expenses AS e')
            ->join('supply_chain_vehicles AS v', 'v.id', '=', 'e.supply_chain_vehicle_id')
            ->where('e.business_id', $businessId)->whereBetween('e.date', [$from->toDateString(), $to->toDateString()])
            ->whereNotNull('v.customer_route_id')
            ->groupBy('v.customer_route_id')->selectRaw('v.customer_route_id AS route_id, SUM(e.amount) AS amount')
            ->pluck('amount', 'route_id');

        $km = DB::table('supply_chain_vehicle_mileage AS m')
            ->join('supply_chain_vehicles AS v', 'v.id', '=', 'm.supply_chain_vehicle_id')
            ->where('m.business_id', $businessId)->whereBetween('m.date', [$from->toDateString(), $to->toDateString()])
            ->whereNotNull('v.customer_route_id')
            ->groupBy('v.customer_route_id')->selectRaw('v.customer_route_id AS route_id, SUM(GREATEST(m.end_mileage - m.start_mileage, 0)) AS km')
            ->pluck('km', 'route_id');

        $collected = DB::table('transaction_payments AS tp')
            ->leftJoin('transactions AS t', 't.id', '=', 'tp.transaction_id')
            ->join('contacts AS c', 'c.id', '=', DB::raw('COALESCE(tp.payment_for, t.contact_id)'))
            ->where('tp.business_id', $businessId)->whereNull('tp.parent_id')->where('tp.is_return', 0)
            ->whereBetween('tp.paid_on', $range)->whereNotNull('c.customer_route_id')
            ->groupBy('c.customer_route_id')->selectRaw('c.customer_route_id AS route_id, SUM(tp.amount) AS amount')
            ->pluck('amount', 'route_id');

        $outstanding = DB::table('transactions AS t')->join('contacts AS c', 'c.id', '=', 't.contact_id')
            ->where('t.business_id', $businessId)->whereIn('t.type', ['sell', 'opening_balance'])->where('t.status', 'final')
            ->whereIn('t.payment_status', ['due', 'partial'])->whereNotNull('c.customer_route_id')
            ->groupBy('c.customer_route_id')
            ->selectRaw('c.customer_route_id AS route_id, SUM(t.final_total - COALESCE((SELECT SUM(IF(tp.is_return = 1, -tp.amount, tp.amount))
                FROM transaction_payments tp WHERE tp.transaction_id = t.id), 0)) AS due')
            ->pluck('due', 'route_id');

        $outlets = DB::table('contacts')->where('business_id', $businessId)->whereNotNull('customer_route_id')
            ->groupBy('customer_route_id')->selectRaw('customer_route_id AS route_id, COUNT(*) AS n')->pluck('n', 'route_id');

        $visits = DB::table('route_visit_logs')->where('business_id', $businessId)->whereBetween('visit_time', $range)
            ->groupBy('customer_route_id')->selectRaw('customer_route_id AS route_id, COUNT(*) AS n')->pluck('n', 'route_id');

        $rows = $routes->map(function ($r) use ($sales, $cost, $vanCost, $km, $collected, $outstanding, $outlets, $visits) {
            $s = $sales[$r->id] ?? null;
            $revenue = (float) ($s->revenue ?? 0);
            $gross = $revenue - (float) ($cost[$r->id] ?? 0);
            $van = (float) ($vanCost[$r->id] ?? 0);
            $net = $gross - $van;
            $units = (float) ($s->units ?? 0);
            $litres = (float) ($s->litres ?? 0);
            $kms = (float) ($km[$r->id] ?? 0);

            return [
                'id' => (int) $r->id,
                'name' => $r->name,
                'revenue' => round($revenue, 2),
                'gross' => round($gross, 2),
                'gross_pct' => $revenue > 0 ? round($gross / $revenue * 100, 1) : null,
                'van_cost' => round($van, 2),
                'net' => round($net, 2),
                'units' => round($units, 2),
                'litres' => round($litres, 2),
                'net_per_unit' => $units > 0 ? round($net / $units, 2) : null,
                'net_per_litre' => $litres > 0 ? round($net / $litres, 2) : null,
                'km' => round($kms),
                'cost_per_km' => $kms > 0 ? round($van / $kms, 2) : null,
                'collected' => round((float) ($collected[$r->id] ?? 0), 2),
                'outstanding' => round((float) ($outstanding[$r->id] ?? 0), 2),
                'invoices' => (int) ($s->invoices ?? 0),
                'buyers' => (int) ($s->buyers ?? 0),
                'outlets' => (int) ($outlets[$r->id] ?? 0),
                'visits' => (int) ($visits[$r->id] ?? 0),
            ];
        })->values()->all();

        $sum = fn ($k) => round(array_sum(array_column($rows, $k)), 2);

        return [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'has_litres' => $hasLitres,
            'routes' => $rows,
            'totals' => [
                'revenue' => $sum('revenue'), 'gross' => $sum('gross'), 'van_cost' => $sum('van_cost'), 'net' => $sum('net'),
                'units' => $sum('units'), 'litres' => $sum('litres'), 'collected' => $sum('collected'), 'outstanding' => $sum('outstanding'),
            ],
        ];
    }
}
