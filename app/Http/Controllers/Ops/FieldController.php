<?php

namespace App\Http\Controllers\Ops;

use App\Services\Ops\FieldCheckIn;
use App\Services\Ops\RoutePlan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The field app: one phone screen for a seller's day.
 *
 * Everything the seller needs to keep working without signal — the route's
 * stops, balances, the price list and the stock on their van — arrives in
 * this one response. The service worker keeps the last copy, so the page
 * opens offline, and actions queue on the phone until they can sync.
 */
class FieldController extends OpsController
{
    public function index(Request $request, FieldCheckIn $checkIn, RoutePlan $plan): Response
    {
        abort_unless($this->canViewCustomers(), 403);

        $b = $this->businessId();
        $userId = (int) auth()->id();
        $isField = $checkIn->isFieldUser($b, $userId);

        // A seller sees their own routes; office staff can open any route
        // (to cover for someone, or to try the screen).
        $routes = DB::table('customer_routes AS r')
            ->where('r.business_id', $b)->where('r.is_active', 1)
            ->when($isField, fn ($q) => $q->whereIn('r.id', DB::table('route_seller_assignments')
                ->where('user_id', $userId)->where('is_active', 1)->pluck('customer_route_id')))
            ->orderBy('r.name')->get(['r.id', 'r.name'])
            ->map(fn ($r) => ['id' => (int) $r->id, 'name' => $r->name])->all();

        $routeId = (int) $request->query('route', $routes[0]['id'] ?? 0);
        if ($routeId && ! in_array($routeId, array_column($routes, 'id'), true)) {
            $routeId = $routes[0]['id'] ?? 0;
        }

        $location = $this->sellingLocation($b, $routeId);
        $business = DB::table('business')->where('id', $b)->first(['date_format', 'time_format']);

        return Inertia::render('Field/Index', [
            'date' => Carbon::today()->toDateString(),
            'fieldUser' => $isField,
            'routes' => $routes,
            'routeId' => $routeId ?: null,
            'stops' => $routeId ? $plan->stops($b, $routeId, Carbon::today()) : [],
            'location' => $location,
            'products' => $location ? $this->priceList($b, $location['id']) : [],
            'methods' => [
                ['value' => 'cash', 'label' => 'Cash'],
                ['value' => 'cheque', 'label' => 'Cheque'],
                ['value' => 'bank_transfer', 'label' => 'Bank transfer'],
            ],
            'outcomes' => collect(VisitController::OUTCOMES)->map(fn ($label, $value) => compact('value', 'label'))->values(),
            'dateFormat' => $business->date_format ?: 'm/d/Y',
            'timeFormat' => (int) ($business->time_format ?: 24),
            'syncedAt' => now()->toIso8601String(),
        ]);
    }

    /**
     * Where this route sells from: its van if it has one set up, otherwise
     * the first location this user may sell at.
     *
     * @return array{id: int, name: string, is_van: bool}|null
     */
    private function sellingLocation(int $b, int $routeId): ?array
    {
        $permitted = auth()->user()->permitted_locations();

        $van = $routeId ? DB::table('supply_chain_vehicles AS v')->join('business_locations AS l', 'l.id', '=', 'v.location_id')
            ->where('v.business_id', $b)->where('v.customer_route_id', $routeId)
            ->when($permitted !== 'all', fn ($q) => $q->whereIn('l.id', $permitted))
            ->first(['l.id', 'l.name']) : null;

        if ($van) {
            return ['id' => (int) $van->id, 'name' => $van->name, 'is_van' => true];
        }

        $loc = DB::table('business_locations')->where('business_id', $b)->where('is_active', 1)->whereNull('supply_chain_vehicle_id')
            ->when($permitted !== 'all', fn ($q) => $q->whereIn('id', $permitted))
            ->orderBy('id')->first(['id', 'name']);

        return $loc ? ['id' => (int) $loc->id, 'name' => $loc->name, 'is_van' => false] : null;
    }

    /**
     * Sellable products with price and stock at the selling location.
     *
     * @return array<int, array<string, mixed>>
     */
    private function priceList(int $b, int $locationId): array
    {
        return DB::table('variations AS v')
            ->join('products AS p', 'p.id', '=', 'v.product_id')
            ->leftJoin('variation_location_details AS d', fn ($j) => $j->on('d.variation_id', '=', 'v.id')->where('d.location_id', $locationId))
            ->leftJoin('units AS u', 'u.id', '=', 'p.unit_id')
            ->where('p.business_id', $b)
            ->where('p.is_inactive', 0)
            ->where('p.not_for_selling', 0)
            ->whereIn('p.type', ['single', 'variable'])
            ->whereNull('v.deleted_at')
            ->orderBy('p.name')
            ->limit(800)
            ->get(['v.id', 'p.name', 'v.name AS variation', 'v.sub_sku', 'p.enable_stock', 'u.short_name AS unit',
                'v.sell_price_inc_tax', 'd.qty_available'])
            ->map(fn ($r) => [
                'variation_id' => (int) $r->id,
                'name' => trim($r->name.(($r->variation && $r->variation !== 'DUMMY') ? ' '.$r->variation : '')),
                'sku' => $r->sub_sku,
                'unit' => $r->unit,
                'price' => round((float) $r->sell_price_inc_tax, 2),
                'stock' => (int) $r->enable_stock ? round((float) ($r->qty_available ?? 0), 2) : null,
            ])->all();
    }
}
