<?php

namespace App\Services\Ops;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * When a vehicle is next due an oil change.
 *
 * The bay already records the odometer at each change and the mileage the
 * next one is due at. What it never had was a date: owners don't know their
 * odometer, they know "about three months". So each vehicle's own km/day is
 * estimated from the gaps between its past changes, and the remaining
 * kilometres are turned into a predicted due date. A vehicle with only one
 * visit falls back to a business-wide default rate until it has history.
 */
class ServiceDue
{
    public const DEFAULT_KM_PER_DAY = 40.0;

    /** "Due soon" window, in days. */
    public const SOON = 7;

    /**
     * @param  array<int, int>  $vehicleIds
     * @return array<int, array<string, mixed>>  keyed by vehicle id
     */
    public function forVehicles(int $businessId, array $vehicleIds, ?Carbon $today = null): array
    {
        if (! $vehicleIds) {
            return [];
        }

        $today ??= Carbon::today();

        $records = DB::table('vehicle_mileage_records')
            ->where('business_id', $businessId)
            ->whereIn('vehicle_id', $vehicleIds)
            ->orderBy('created_at')
            ->get(['vehicle_id', 'invoice_id', 'oil_change_mileage', 'next_mileage', 'created_at'])
            ->groupBy('vehicle_id');

        $out = [];
        foreach ($records as $vehicleId => $rows) {
            $prediction = self::predict(
                $rows->map(fn ($r) => [
                    'date' => substr((string) $r->created_at, 0, 10),
                    'reading' => $r->oil_change_mileage !== null ? (int) $r->oil_change_mileage : null,
                    'next' => $r->next_mileage !== null ? (int) $r->next_mileage : null,
                    'invoice_id' => $r->invoice_id ? (int) $r->invoice_id : null,
                ])->all(),
                $today,
            );

            if ($prediction) {
                $out[(int) $vehicleId] = $prediction;
            }
        }

        return $out;
    }

    /**
     * @param  array<int, array{date: string, reading: ?int, next: ?int, invoice_id?: ?int}>  $records  oldest first
     * @return array<string, mixed>|null
     */
    public static function predict(array $records, Carbon $today, float $defaultKmPerDay = self::DEFAULT_KM_PER_DAY): ?array
    {
        $records = array_values(array_filter($records, fn ($r) => $r['reading'] !== null));
        if (! $records) {
            return null;
        }

        $last = $records[count($records) - 1];
        if ($last['next'] === null || $last['next'] <= $last['reading']) {
            return null;
        }

        // Rate over (at most) the last four intervals that actually moved.
        $km = 0;
        $days = 0;
        $pairs = count($records) > 1 ? array_slice(range(1, count($records) - 1), -4) : [];
        foreach ($pairs as $i) {
            $dKm = $records[$i]['reading'] - $records[$i - 1]['reading'];
            $dDays = Carbon::parse($records[$i - 1]['date'])->diffInDays(Carbon::parse($records[$i]['date']));
            if ($dKm > 0 && $dDays > 0) {
                $km += $dKm;
                $days += $dDays;
            }
        }

        $basis = $days > 0 ? 'history' : 'default';
        $rate = $days > 0 ? $km / $days : $defaultKmPerDay;
        $rate = max(1.0, $rate);

        $lastDate = Carbon::parse($last['date'])->startOfDay();
        $remainingKm = $last['next'] - $last['reading'];
        $dueOn = $lastDate->copy()->addDays((int) round($remainingKm / $rate));
        $daysUntil = (int) $today->diffInDays($dueOn, false);
        $sinceLast = (int) $lastDate->diffInDays($today);

        return [
            'last_service_on' => $lastDate->toDateString(),
            'last_reading' => $last['reading'],
            'next_mileage' => $last['next'],
            'km_per_day' => round($rate, 1),
            'estimated_reading' => (int) round($last['reading'] + $rate * $sinceLast),
            'due_on' => $dueOn->toDateString(),
            'days_until' => $daysUntil,
            'status' => $daysUntil < 0 ? 'overdue' : ($daysUntil <= self::SOON ? 'due_soon' : 'ok'),
            'basis' => $basis,
            'last_invoice_id' => $last['invoice_id'] ?? null,
            'visits' => count($records),
        ];
    }

    /**
     * Vehicles due within $withinDays (or overdue by up to 60 days — older
     * than that and the owner has gone elsewhere; chasing them is noise).
     *
     * @return array<int, array<string, mixed>>
     */
    public function dueList(int $businessId, int $withinDays = self::SOON, ?Carbon $today = null): array
    {
        $today ??= Carbon::today();

        $vehicles = DB::table('customer_vehicles AS v')
            ->join('contacts AS c', 'c.id', '=', 'v.contact_id')
            ->where('v.business_id', $businessId)
            ->whereExists(fn ($q) => $q->from('vehicle_mileage_records AS r')->whereColumn('r.vehicle_id', 'v.id'))
            ->get(['v.id', 'v.make', 'v.model', 'v.license_plate', 'c.id AS contact_id', 'c.name', 'c.supplier_business_name', 'c.mobile'])
            ->keyBy('id');

        $predictions = $this->forVehicles($businessId, $vehicles->keys()->all(), $today);

        $lastReminders = DB::table('service_reminders')
            ->where('business_id', $businessId)
            ->whereIn('vehicle_id', $vehicles->keys())
            ->groupBy('vehicle_id')
            ->selectRaw('vehicle_id, MAX(sent_at) AS sent_at')
            ->pluck('sent_at', 'vehicle_id');

        $out = [];
        foreach ($predictions as $id => $p) {
            if ($p['days_until'] > $withinDays || $p['days_until'] < -60) {
                continue;
            }

            $v = $vehicles[$id];
            $out[] = [
                'vehicle_id' => $id,
                'plate' => $v->license_plate,
                'vehicle' => trim($v->make.' '.$v->model) ?: null,
                'contact_id' => (int) $v->contact_id,
                'owner' => trim($v->supplier_business_name ?: $v->name) ?: 'Unnamed',
                'mobile' => $v->mobile,
                'reminded_at' => $lastReminders[$id] ?? null,
                ...$p,
            ];
        }

        usort($out, fn ($a, $b) => $a['days_until'] <=> $b['days_until']);

        return $out;
    }
}
