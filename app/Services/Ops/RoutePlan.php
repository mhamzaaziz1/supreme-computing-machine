<?php

namespace App\Services\Ops;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * A route's stops for one day, in visit order, with what a seller needs at
 * each: is it due an order, was it visited, what does it owe.
 *
 * Shared by the route drawer and the field app so both show the same day.
 */
class RoutePlan
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function stops(int $businessId, int $routeId, Carbon $date): array
    {
        $b = $businessId;

        $sequence = DB::table('route_outlet_sequence')->where('customer_route_id', $routeId)->orderBy('sequence_number')
            ->get(['contact_id', 'sequence_number', 'expected_start_time'])->keyBy('contact_id');

        $contacts = DB::table('contacts')->where('business_id', $b)
            ->where(fn ($q) => $q->where('customer_route_id', $routeId)->orWhereIn('id', $sequence->keys()))
            ->get(['id', 'name', 'supplier_business_name', 'mobile', 'address_line_1', 'city', 'latitude', 'longitude', 'credit_hold'])
            ->keyBy('id');

        $ids = $contacts->keys()->all();
        if (! $ids) {
            return [];
        }

        $orderDays = DB::table('transactions')->where('business_id', $b)->where('type', 'sell')->where('status', 'final')
            ->whereIn('contact_id', $ids)->where('transaction_date', '>=', $date->copy()->subDays(180))
            ->selectRaw('contact_id, DATE(transaction_date) AS d')->groupBy('contact_id', 'd')->get()
            ->groupBy('contact_id')->map(fn ($rows) => $rows->pluck('d')->all());

        $due = DB::table('transactions AS t')->where('t.business_id', $b)->whereIn('t.contact_id', $ids)
            ->whereIn('t.type', ['sell', 'opening_balance'])->where('t.status', 'final')->whereIn('t.payment_status', ['due', 'partial'])
            ->groupBy('t.contact_id')
            ->selectRaw('t.contact_id, SUM(t.final_total - COALESCE((SELECT SUM(IF(tp.is_return = 1, -tp.amount, tp.amount))
                FROM transaction_payments tp WHERE tp.transaction_id = t.id), 0)) AS due')
            ->pluck('due', 'contact_id');

        $visits = DB::table('route_visit_logs')->whereIn('contact_id', $ids)
            ->groupBy('contact_id')
            ->selectRaw('contact_id, MAX(visit_time) AS last_visit, SUM(DATE(visit_time) = ?) AS today', [$date->toDateString()])
            ->get()->keyBy('contact_id');

        $ordersToday = DB::table('transactions')->where('business_id', $b)->where('type', 'sell')->where('status', 'final')
            ->whereIn('contact_id', $ids)->whereDate('transaction_date', $date)
            ->groupBy('contact_id')->selectRaw('contact_id, SUM(final_total) AS total')->pluck('total', 'contact_id');

        $name = fn ($c) => trim((string) $c->supplier_business_name) ?: (trim((string) $c->name) ?: 'Unnamed customer');

        $ordered = $sequence->keys()->map(fn ($x) => (int) $x)->filter(fn ($x) => $contacts->has($x))->values()->all();
        $rest = $contacts->keys()->map(fn ($x) => (int) $x)->diff($ordered)
            ->sortBy(fn ($x) => $name($contacts[$x]))->values()->all();

        $out = [];
        foreach ([...$ordered, ...$rest] as $cid) {
            $c = $contacts[$cid];
            $days = $orderDays[$cid] ?? [];
            rsort($days);
            $interval = BuyingPattern::rhythm($days);
            $since = $days ? (int) Carbon::parse($days[0])->diffInDays($date) : null;

            $out[] = [
                'id' => $cid,
                'name' => $name($c),
                'mobile' => $c->mobile,
                'address' => trim(implode(', ', array_filter([$c->address_line_1, $c->city]))) ?: null,
                'lat' => $c->latitude !== null ? (float) $c->latitude : null,
                'lng' => $c->longitude !== null ? (float) $c->longitude : null,
                'sequence' => isset($sequence[$cid]) ? (int) $sequence[$cid]->sequence_number : null,
                'expected_at' => isset($sequence[$cid]) && $sequence[$cid]->expected_start_time ? substr((string) $sequence[$cid]->expected_start_time, 0, 5) : null,
                'interval_days' => $interval,
                'days_since_order' => $since,
                'due' => $interval !== null && $since !== null && $since >= $interval,
                'outstanding' => round((float) ($due[$cid] ?? 0), 2),
                'on_hold' => (bool) $c->credit_hold,
                'last_visit' => isset($visits[$cid]) ? substr((string) $visits[$cid]->last_visit, 0, 10) : null,
                'visited_today' => isset($visits[$cid]) && (int) $visits[$cid]->today > 0,
                'ordered_today' => round((float) ($ordersToday[$cid] ?? 0), 2),
            ];
        }

        return $out;
    }
}
