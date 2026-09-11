<?php

namespace App\Http\Controllers\Ops;

use App\Services\Ops\FieldCheckIn;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * A route's day plan (stops in order, which are due, which were visited),
 * its rules (geofence mode, order and time limits), and the live map.
 */
class RouteController extends OpsController
{
    public function plan(Request $request, int $id): JsonResponse
    {
        abort_unless($this->canViewCustomers(), 403);

        $b = $this->businessId();
        $route = DB::table('customer_routes')->where('business_id', $b)->where('id', $id)->first(['id', 'name', 'description']);
        abort_unless($route, 404, 'That route does not exist.');

        $date = Carbon::parse($request->query('date', 'today'));
        $stops = app(\App\Services\Ops\RoutePlan::class)->stops($b, $id, $date);

        $rules = DB::table('route_zone_restrictions')->where('customer_route_id', $id)->first();
        $vehicle = DB::table('supply_chain_vehicles')->where('business_id', $b)->where('customer_route_id', $id)->first(['id', 'license_plate']);

        return response()->json([
            'route' => ['id' => (int) $route->id, 'name' => $route->name, 'description' => $route->description],
            'date' => $date->toDateString(),
            'sellers' => DB::table('route_seller_assignments AS a')->join('users AS u', 'u.id', '=', 'a.user_id')
                ->where('a.customer_route_id', $id)->where('a.is_active', 1)
                ->pluck(DB::raw("TRIM(CONCAT(COALESCE(u.first_name,''),' ',COALESCE(u.last_name,'')))"))->all(),
            'van' => $vehicle ? ['id' => (int) $vehicle->id, 'plate' => $vehicle->license_plate] : null,
            'rules' => [
                'geofence_mode' => in_array($rules->geofence_mode ?? null, FieldCheckIn::MODES, true) ? $rules->geofence_mode : 'off',
                'minimum_order_value' => $rules && $rules->minimum_order_value !== null ? (float) $rules->minimum_order_value : null,
                'enable_collections' => $rules ? (bool) $rules->enable_collections : true,
                'enable_returns' => $rules ? (bool) $rules->enable_returns : true,
                'allowed_start_time' => $rules && $rules->allowed_start_time ? substr((string) $rules->allowed_start_time, -8, 5) : null,
                'allowed_end_time' => $rules && $rules->allowed_end_time ? substr((string) $rules->allowed_end_time, -8, 5) : null,
            ],
            'stops' => $stops,
            'summary' => [
                'stops' => count($stops),
                'visited' => count(array_filter($stops, fn ($s) => $s['visited_today'])),
                'due' => count(array_filter($stops, fn ($s) => $s['due'])),
                'pinned' => count(array_filter($stops, fn ($s) => $s['lat'] !== null)),
                'outstanding' => round(array_sum(array_column($stops, 'outstanding')), 2),
            ],
            'can_edit' => $this->canEdit(),
        ]);
    }

    public function sequence(Request $request, int $id): JsonResponse
    {
        abort_unless($this->canEdit(), 403, 'You do not have permission to change routes.');

        $data = $request->validate(['contact_ids' => ['required', 'array'], 'contact_ids.*' => ['integer']]);
        $b = $this->businessId();

        $onRoute = DB::table('contacts')->where('business_id', $b)->where('customer_route_id', $id)->pluck('id')->map(fn ($x) => (int) $x)->all();
        $existing = DB::table('route_outlet_sequence')->where('customer_route_id', $id)->get()->keyBy('contact_id');
        $ids = array_values(array_unique(array_map('intval', $data['contact_ids'])));

        if (array_diff($ids, array_merge($onRoute, $existing->keys()->map(fn ($x) => (int) $x)->all()))) {
            abort(422, 'Only outlets on this route can be put in its order.');
        }

        DB::transaction(function () use ($id, $b, $ids, $existing) {
            DB::table('route_outlet_sequence')->where('customer_route_id', $id)->delete();
            foreach ($ids as $i => $contactId) {
                $old = $existing[$contactId] ?? null;
                DB::table('route_outlet_sequence')->insert([
                    'business_id' => $b,
                    'customer_route_id' => $id,
                    'contact_id' => $contactId,
                    'sequence_number' => $i + 1,
                    'expected_start_time' => $old->expected_start_time ?? null,
                    'expected_end_time' => $old->expected_end_time ?? null,
                    'min_visit_duration' => $old->min_visit_duration ?? null,
                    'notes' => $old->notes ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return response()->json(['message' => 'Visit order saved.']);
    }

    public function rules(Request $request, int $id): JsonResponse
    {
        abort_unless($this->canEdit(), 403, 'You do not have permission to change routes.');
        abort_unless(DB::table('customer_routes')->where('business_id', $this->businessId())->where('id', $id)->exists(), 404);

        $data = $request->validate([
            'geofence_mode' => ['required', 'in:'.implode(',', FieldCheckIn::MODES)],
            'minimum_order_value' => ['nullable', 'numeric', 'min:0'],
            'enable_collections' => ['boolean'],
            'enable_returns' => ['boolean'],
            'allowed_start_time' => ['nullable', 'date_format:H:i'],
            'allowed_end_time' => ['nullable', 'date_format:H:i', 'required_with:allowed_start_time'],
        ]);

        DB::table('route_zone_restrictions')->updateOrInsert(['customer_route_id' => $id], [
            'business_id' => $this->businessId(),
            'geofence_mode' => $data['geofence_mode'],
            'minimum_order_value' => $data['minimum_order_value'] ?? null,
            'enable_collections' => (bool) ($data['enable_collections'] ?? true),
            'enable_returns' => (bool) ($data['enable_returns'] ?? true),
            'allowed_start_time' => $data['allowed_start_time'] ?? null,
            'allowed_end_time' => $data['allowed_end_time'] ?? null,
            'updated_at' => now(),
            'created_at' => now(),
        ]);

        return response()->json(['message' => 'Route rules saved.']);
    }

    /**
     * Everything the Today map shows: every routed outlet with a location,
     * each seller's latest position today, and today's violations.
     */
    public function map(Request $request): JsonResponse
    {
        abort_unless($this->canViewCustomers(), 403);

        $b = $this->businessId();
        $date = Carbon::parse($request->query('date', 'today'));

        $visited = DB::table('route_visit_logs')->where('business_id', $b)->whereDate('visit_time', $date)
            ->pluck('contact_id')->filter()->map(fn ($x) => (int) $x)->unique()->flip();

        $outlets = DB::table('contacts AS c')->join('customer_routes AS r', 'r.id', '=', 'c.customer_route_id')
            ->where('c.business_id', $b)->whereNotNull('c.latitude')->whereNotNull('c.longitude')
            ->get(['c.id', 'c.name', 'c.supplier_business_name', 'c.latitude', 'c.longitude', 'r.id AS route_id', 'r.name AS route'])
            ->map(fn ($c) => [
                'id' => (int) $c->id,
                'name' => self::outletName($c),
                'lat' => (float) $c->latitude,
                'lng' => (float) $c->longitude,
                'route_id' => (int) $c->route_id,
                'route' => $c->route,
                'visited' => isset($visited[(int) $c->id]),
            ]);

        $sellers = DB::table('route_visit_logs AS v')->join('users AS u', 'u.id', '=', 'v.user_id')
            ->where('v.business_id', $b)->whereDate('v.visit_time', $date)->where('v.latitude', '!=', 0)
            ->whereRaw('v.id = (SELECT MAX(v2.id) FROM route_visit_logs v2 WHERE v2.user_id = v.user_id AND DATE(v2.visit_time) = ?)', [$date->toDateString()])
            ->get(['u.id', 'u.first_name', 'v.latitude', 'v.longitude', 'v.visit_time'])
            ->map(fn ($s) => ['id' => (int) $s->id, 'name' => $s->first_name, 'lat' => (float) $s->latitude, 'lng' => (float) $s->longitude,
                'at' => Carbon::parse($s->visit_time)->format('H:i')]);

        $violations = DB::table('geofence_violation_logs')->where('business_id', $b)->whereDate('created_at', $date)
            ->where('latitude', '!=', 0)->limit(100)
            ->get(['id', 'violation_type', 'latitude', 'longitude', 'contact_id', 'created_at'])
            ->map(fn ($v) => ['id' => (int) $v->id, 'type' => $v->violation_type, 'lat' => (float) $v->latitude, 'lng' => (float) $v->longitude,
                'contact_id' => $v->contact_id ? (int) $v->contact_id : null, 'at' => Carbon::parse($v->created_at)->format('H:i')]);

        return response()->json(['outlets' => $outlets, 'sellers' => $sellers, 'violations' => $violations]);
    }

    private function canEdit(): bool
    {
        return $this->isAdmin() || auth()->user()->can('customer.update');
    }
}
