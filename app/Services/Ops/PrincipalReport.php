<?php

namespace App\Services\Ops;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Secondary sales in the shape a lubricant principal asks for, and progress
 * against the principal's targets.
 *
 * "Secondary" sales are the distributor's sales to outlets (primary being
 * the principal's sales to the distributor). Net of returns throughout,
 * because that is what incentives are paid on.
 */
class PrincipalReport
{
    public const MEASURES = ['qty', 'litres', 'value'];

    /**
     * One row per invoice line.
     *
     * @return \Generator<int, array<string, mixed>>
     */
    public function lines(int $businessId, Carbon $from, Carbon $to): \Generator
    {
        $query = DB::table('transaction_sell_lines AS l')
            ->join('transactions AS t', 't.id', '=', 'l.transaction_id')
            ->join('products AS p', 'p.id', '=', 'l.product_id')
            ->leftJoin('variations AS v', 'v.id', '=', 'l.variation_id')
            ->leftJoin('units AS u', 'u.id', '=', 'p.unit_id')
            ->leftJoin('brands AS b', 'b.id', '=', 'p.brand_id')
            ->leftJoin('categories AS cat', 'cat.id', '=', 'p.category_id')
            ->leftJoin('contacts AS c', 'c.id', '=', 't.contact_id')
            ->leftJoin('customer_routes AS r', 'r.id', '=', 'c.customer_route_id')
            ->where('t.business_id', $businessId)->where('t.type', 'sell')->where('t.status', 'final')
            ->whereBetween('t.transaction_date', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->whereNull('l.parent_sell_line_id')
            ->orderBy('t.transaction_date')->orderBy('t.id')
            ->select('t.transaction_date', 't.invoice_no', 'r.name AS route', 'c.contact_id AS outlet_code', 'c.name AS outlet_person',
                'c.supplier_business_name AS outlet_business', 'c.city', 'b.name AS brand', 'cat.name AS category', 'v.sub_sku AS sku',
                'p.name AS product', 'v.name AS variation', 'u.short_name AS unit', 'p.pack_litres',
                DB::raw('(l.quantity - l.quantity_returned) AS qty'), 'l.unit_price', 'l.unit_price_inc_tax', 'l.item_tax');

        foreach ($query->cursor() as $r) {
            $qty = (float) $r->qty;
            if (abs($qty) < 0.00001) {
                continue;
            }

            yield [
                'Month' => Carbon::parse($r->transaction_date)->format('Y-m'),
                'Invoice date' => substr((string) $r->transaction_date, 0, 10),
                'Invoice no' => $r->invoice_no,
                'Route' => $r->route,
                'Outlet code' => $r->outlet_code,
                'Outlet' => trim($r->outlet_business ?: $r->outlet_person),
                'City' => $r->city,
                'Brand' => $r->brand,
                'Category' => $r->category,
                'SKU' => $r->sku,
                'Product' => trim($r->product.(($r->variation && $r->variation !== 'DUMMY') ? ' '.$r->variation : '')),
                'Qty' => round($qty, 4),
                'Unit' => $r->unit,
                'Pack litres' => $r->pack_litres !== null ? (float) $r->pack_litres : null,
                'Litres' => $r->pack_litres !== null ? round($qty * (float) $r->pack_litres, 3) : null,
                'Net value' => round($qty * (float) $r->unit_price, 2),
                'Tax' => round($qty * (float) $r->item_tax, 2),
                'Gross value' => round($qty * (float) $r->unit_price_inc_tax, 2),
            ];
        }
    }

    /**
     * Month totals by brand, for the preview before downloading.
     *
     * @return array<int, array<string, mixed>>
     */
    public function byBrand(int $businessId, Carbon $from, Carbon $to): array
    {
        return DB::table('transaction_sell_lines AS l')
            ->join('transactions AS t', 't.id', '=', 'l.transaction_id')
            ->join('products AS p', 'p.id', '=', 'l.product_id')
            ->leftJoin('brands AS b', 'b.id', '=', 'p.brand_id')
            ->where('t.business_id', $businessId)->where('t.type', 'sell')->where('t.status', 'final')
            ->whereBetween('t.transaction_date', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->whereNull('l.parent_sell_line_id')
            ->groupBy('b.id', 'b.name')
            ->selectRaw("COALESCE(b.name, 'No brand') AS brand,
                SUM(l.quantity - l.quantity_returned) AS qty,
                SUM((l.quantity - l.quantity_returned) * COALESCE(p.pack_litres, 0)) AS litres,
                SUM((l.quantity - l.quantity_returned) * l.unit_price) AS value,
                SUM(p.pack_litres IS NULL) AS missing_pack")
            ->orderByDesc('value')
            ->get()
            ->map(fn ($r) => [
                'brand' => $r->brand,
                'qty' => round((float) $r->qty, 2),
                'litres' => round((float) $r->litres, 2),
                'value' => round((float) $r->value, 2),
                'missing_pack' => (int) $r->missing_pack > 0,
            ])->all();
    }

    /**
     * Every target that has not ended more than a quarter ago, with progress.
     *
     * @return array<int, array<string, mixed>>
     */
    public function targets(int $businessId, ?Carbon $today = null): array
    {
        $today ??= Carbon::today();

        return DB::table('principal_targets')->where('business_id', $businessId)
            ->where('ends_on', '>=', $today->copy()->subMonths(3))
            ->orderBy('ends_on')->get()
            ->map(function ($t) use ($businessId, $today) {
                $scope = json_decode($t->scope ?? 'null', true) ?: [];
                $achieved = $this->achieved($businessId, Carbon::parse($t->starts_on), Carbon::parse($t->ends_on), $t->measure, $scope);
                $target = (float) $t->target;

                $start = Carbon::parse($t->starts_on);
                $end = Carbon::parse($t->ends_on);
                $elapsed = $today->lt($start) ? 0 : min(1, ($start->diffInDays($today) + 1) / max(1, $start->diffInDays($end) + 1));

                return [
                    'id' => (int) $t->id,
                    'name' => $t->name,
                    'starts_on' => $t->starts_on,
                    'ends_on' => $t->ends_on,
                    'measure' => $t->measure,
                    'scope' => $scope,
                    'target' => $target,
                    'achieved' => $achieved,
                    'percent' => $target > 0 ? round($achieved / $target * 100, 1) : 0,
                    // Where the run-rate would land by the end date.
                    'projected_percent' => $elapsed > 0 && $target > 0 ? round($achieved / $elapsed / $target * 100, 1) : null,
                    'elapsed_percent' => round($elapsed * 100, 1),
                    'days_left' => max(0, (int) $today->diffInDays($end, false)),
                    'ended' => $end->lt($today),
                ];
            })->all();
    }

    /**
     * @param  array<string, array<int, int>>  $scope
     */
    private function achieved(int $businessId, Carbon $from, Carbon $to, string $measure, array $scope): float
    {
        $q = DB::table('transaction_sell_lines AS l')
            ->join('transactions AS t', 't.id', '=', 'l.transaction_id')
            ->join('products AS p', 'p.id', '=', 'l.product_id')
            ->where('t.business_id', $businessId)->where('t.type', 'sell')->where('t.status', 'final')
            ->whereBetween('t.transaction_date', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->whereNull('l.parent_sell_line_id');

        $filters = array_filter([
            'p.brand_id' => $scope['brand_ids'] ?? [],
            'p.category_id' => $scope['category_ids'] ?? [],
            'l.product_id' => $scope['product_ids'] ?? [],
        ]);
        if ($filters) {
            $q->where(function ($w) use ($filters) {
                foreach ($filters as $col => $ids) {
                    $w->orWhereIn($col, $ids);
                }
            });
        }

        $expr = match ($measure) {
            'litres' => '(l.quantity - l.quantity_returned) * COALESCE(p.pack_litres, 0)',
            'value' => '(l.quantity - l.quantity_returned) * l.unit_price',
            default => 'l.quantity - l.quantity_returned',
        };

        return round((float) $q->sum(DB::raw($expr)), 2);
    }
}
