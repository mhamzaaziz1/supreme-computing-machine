<?php

namespace App\Services\Ops;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * How an outlet usually buys, derived from its own sell lines.
 *
 * Three questions a seller has twelve minutes to answer at each stop:
 *   - is this outlet due an order? (rhythm: typical days between orders)
 *   - what does it usually take? (the usual basket)
 *   - what has it stopped taking? (a SKU missing for twice its own interval,
 *     which in lubricants usually means a competitor's can is on the shelf)
 *
 * Every figure is relative to the outlet's own history rather than a global
 * threshold, because a station that orders weekly and one that orders
 * monthly are both healthy.
 */
class BuyingPattern
{
    /** Orders considered for rhythm and basket. */
    private const LOOKBACK_ORDERS = 12;

    /** A SKU needs this many orders before its absence means anything. */
    private const MIN_ORDERS_FOR_STOPPED = 3;

    /**
     * @return array<string, mixed>
     */
    public function forContact(int $businessId, int $contactId, ?Carbon $today = null): array
    {
        $today ??= Carbon::today();

        $orders = DB::table('transactions')
            ->where('business_id', $businessId)
            ->where('contact_id', $contactId)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->where('transaction_date', '>=', $today->copy()->subDays(365))
            ->orderByDesc('transaction_date')
            ->limit(60)
            ->get(['id', 'transaction_date', 'final_total']);

        $lines = $orders->isEmpty() ? collect() : DB::table('transaction_sell_lines AS l')
            ->join('products AS p', 'p.id', '=', 'l.product_id')
            ->join('variations AS v', 'v.id', '=', 'l.variation_id')
            ->whereIn('l.transaction_id', $orders->pluck('id'))
            ->whereNull('l.parent_sell_line_id')
            ->get(['l.transaction_id', 'l.product_id', 'l.variation_id', 'l.quantity', 'p.name AS product',
                'v.name AS variation', 'v.sub_sku AS sku']);

        $dateOf = $orders->mapWithKeys(fn ($o) => [$o->id => substr((string) $o->transaction_date, 0, 10)]);

        $history = [];
        foreach ($lines as $l) {
            $key = (int) $l->variation_id;
            $history[$key] ??= [
                'product_id' => (int) $l->product_id,
                'variation_id' => $key,
                'name' => trim($l->product.(($l->variation && $l->variation !== 'DUMMY') ? ' ('.$l->variation.')' : '')),
                'sku' => $l->sku,
                'dates' => [],
                'qty' => [],
            ];
            $history[$key]['dates'][] = $dateOf[$l->transaction_id];
            $history[$key]['qty'][$l->transaction_id] = ($history[$key]['qty'][$l->transaction_id] ?? 0) + (float) $l->quantity;
        }

        $orderDates = $orders->pluck('transaction_date')->map(fn ($d) => substr((string) $d, 0, 10))->all();
        $recent = array_slice($orders->pluck('id')->all(), 0, 6);

        $interval = self::rhythm(array_slice($orderDates, 0, self::LOOKBACK_ORDERS));
        $last = $orderDates[0] ?? null;
        $since = $last ? (int) Carbon::parse($last)->diffInDays($today) : null;

        return [
            'rhythm' => [
                'interval_days' => $interval,
                'last_order_on' => $last,
                'days_since' => $since,
                'due' => $interval !== null && $since !== null && $since >= $interval,
                'orders_90d' => $orders->filter(fn ($o) => Carbon::parse($o->transaction_date)->gte($today->copy()->subDays(90)))->count(),
                'avg_order_value' => $orders->count() ? round((float) $orders->take(self::LOOKBACK_ORDERS)->avg('final_total'), 2) : null,
            ],
            'stopped' => self::stopped($history, $today),
            'basket' => self::basket($history, $recent),
        ];
    }

    /**
     * Typical days between orders: the median gap between distinct order
     * dates. Median, not mean, so one long holiday doesn't skew it.
     *
     * @param  array<int, string>  $dates  Y-m-d, any order
     */
    public static function rhythm(array $dates): ?float
    {
        $days = array_values(array_unique($dates));
        sort($days);

        if (count($days) < 2) {
            return null;
        }

        $gaps = [];
        for ($i = 1; $i < count($days); $i++) {
            $gaps[] = Carbon::parse($days[$i - 1])->diffInDays(Carbon::parse($days[$i]));
        }

        return round(self::median($gaps), 1);
    }

    /**
     * SKUs this outlet used to buy regularly and has now gone quiet on.
     *
     * @param  array<int, array<string, mixed>>  $history  keyed by variation id, each with 'dates'
     * @return array<int, array<string, mixed>>
     */
    public static function stopped(array $history, Carbon $today): array
    {
        $out = [];

        foreach ($history as $sku) {
            $dates = array_values(array_unique($sku['dates']));
            if (count($dates) < self::MIN_ORDERS_FOR_STOPPED) {
                continue;
            }

            $interval = self::rhythm($dates);
            if (! $interval) {
                continue;
            }

            rsort($dates);
            $since = (int) Carbon::parse($dates[0])->diffInDays($today);

            if ($since > 2 * $interval) {
                $out[] = [
                    'product_id' => $sku['product_id'],
                    'variation_id' => $sku['variation_id'],
                    'name' => $sku['name'],
                    'sku' => $sku['sku'],
                    'interval_days' => $interval,
                    'days_since' => $since,
                    'last_on' => $dates[0],
                ];
            }
        }

        usort($out, fn ($a, $b) => ($b['days_since'] / $b['interval_days']) <=> ($a['days_since'] / $a['interval_days']));

        return array_slice($out, 0, 6);
    }

    /**
     * The usual order: SKUs that appear in at least 40% of the recent orders,
     * at the median quantity they were bought in.
     *
     * @param  array<int, array<string, mixed>>  $history  each with 'qty' keyed by transaction id
     * @param  array<int, int>  $recentOrderIds
     * @return array<int, array<string, mixed>>
     */
    public static function basket(array $history, array $recentOrderIds): array
    {
        if (! $recentOrderIds) {
            return [];
        }

        $threshold = max(1, (int) ceil(count($recentOrderIds) * 0.4));
        $out = [];

        foreach ($history as $sku) {
            $qtys = array_values(array_intersect_key($sku['qty'], array_flip($recentOrderIds)));
            if (count($qtys) < $threshold) {
                continue;
            }

            $out[] = [
                'product_id' => $sku['product_id'],
                'variation_id' => $sku['variation_id'],
                'name' => $sku['name'],
                'sku' => $sku['sku'],
                'quantity' => round(self::median($qtys), 2),
                'frequency' => count($qtys).'/'.count($recentOrderIds),
            ];
        }

        usort($out, fn ($a, $b) => strcmp($b['frequency'], $a['frequency']) ?: strcmp($a['name'], $b['name']));

        return $out;
    }

    /**
     * Outlets whose last 30 days fell more than 30% below their own average
     * month over the 90 days before that.
     *
     * @return array<int, array<string, mixed>>
     */
    public function atRisk(int $businessId, ?Carbon $today = null, int $limit = 25): array
    {
        $today ??= Carbon::today();
        $recentFrom = $today->copy()->subDays(30);
        $baseFrom = $today->copy()->subDays(120);

        $rows = DB::table('transactions AS t')
            ->join('contacts AS c', 'c.id', '=', 't.contact_id')
            ->leftJoin('customer_routes AS cr', 'cr.id', '=', 'c.customer_route_id')
            ->where('t.business_id', $businessId)
            ->where('t.type', 'sell')
            ->where('t.status', 'final')
            ->where('c.is_default', 0)
            ->where('t.transaction_date', '>=', $baseFrom)
            ->groupBy('c.id', 'c.name', 'c.supplier_business_name', 'cr.name')
            ->selectRaw('c.id, c.name, c.supplier_business_name, cr.name AS route,
                SUM(IF(t.transaction_date >= ?, t.final_total, 0)) AS recent,
                SUM(IF(t.transaction_date < ?, t.final_total, 0)) AS base', [$recentFrom, $recentFrom])
            ->get();

        $out = [];
        foreach ($rows as $r) {
            $baseMonthly = (float) $r->base / 3;
            if ($baseMonthly <= 0) {
                continue;
            }

            $drop = 1 - ((float) $r->recent / $baseMonthly);
            if ($drop > 0.3) {
                $out[] = [
                    'id' => (int) $r->id,
                    'name' => trim($r->supplier_business_name ?: $r->name),
                    'route' => $r->route,
                    'recent' => round((float) $r->recent, 2),
                    'usual' => round($baseMonthly, 2),
                    'drop_pct' => (int) round($drop * 100),
                ];
            }
        }

        usort($out, fn ($a, $b) => ($b['usual'] - $b['recent']) <=> ($a['usual'] - $a['recent']));

        return array_slice($out, 0, $limit);
    }

    /**
     * @param  array<int, float|int>  $values
     */
    private static function median(array $values): float
    {
        sort($values);
        $n = count($values);
        $mid = intdiv($n, 2);

        return $n % 2 ? (float) $values[$mid] : ($values[$mid - 1] + $values[$mid]) / 2;
    }
}
