<?php

namespace App\Http\Controllers\Ops;

use App\Exceptions\OpsException;
use App\Exceptions\PurchaseSellMismatch;
use App\Services\Ops\VanStock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Load van in the morning, settle van at night.
 */
class VanController extends OpsController
{
    public function __construct(private VanStock $vans) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorizeView();

        return response()->json([
            'date' => Carbon::today()->toDateString(),
            'vans' => $this->vans->vans($this->businessId(), Carbon::today()),
            'can_move_stock' => $this->canMoveStock(),
        ]);
    }

    public function loadForm(Request $request, int $id): JsonResponse
    {
        $this->authorizeMove();

        $vehicle = $this->vans->vehicle($this->businessId(), $id);
        $warehouses = $this->warehouses();
        $from = (int) ($request->query('from') ?: ($warehouses[0]['id'] ?? 0));

        $suggested = $vehicle->location_id ? $this->vans->lastLoad((int) $vehicle->location_id) : [];

        return response()->json([
            'van' => [
                'id' => (int) $vehicle->id,
                'plate' => $vehicle->license_plate ?: 'Van #'.$vehicle->id,
                'sellers' => $this->vans->sellers((int) $vehicle->customer_route_id)->pluck('name')->all(),
                'has_location' => (bool) $vehicle->location_id,
            ],
            'warehouses' => $warehouses,
            'from' => $from,
            'suggested' => $this->describe(array_column($suggested, 'quantity', 'variation_id'), $from),
            'on_van' => $vehicle->location_id ? $this->vans->stockAt((int) $vehicle->location_id) : [],
        ]);
    }

    public function load(Request $request, int $id): JsonResponse
    {
        $this->authorizeMove();

        $data = $request->validate([
            'from_location_id' => ['required', 'integer'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.variation_id' => ['required', 'integer'],
            'lines.*.quantity' => ['required', 'numeric', 'gt:0'],
            'note' => ['nullable', 'string', 'max:190'],
        ], ['lines.required' => 'Add at least one product to load.']);

        $this->assertWarehouse((int) $data['from_location_id']);
        $vehicle = $this->vans->vehicle($this->businessId(), $id);

        $lines = [];
        foreach ($data['lines'] as $l) {
            $lines[(int) $l['variation_id']] = ($lines[(int) $l['variation_id']] ?? 0) + (float) $l['quantity'];
        }

        try {
            $transfer = DB::transaction(fn () => $this->vans->load($this->businessId(), $vehicle, (int) $data['from_location_id'],
                $lines, (int) auth()->id(), $data['note'] ?? null));
        } catch (OpsException|PurchaseSellMismatch $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => sprintf('Loaded %s units on %s (%s).', rtrim(rtrim(number_format(array_sum($lines), 2), '0'), '.'),
                $vehicle->license_plate ?: 'the van', $transfer->ref_no),
            'ref_no' => $transfer->ref_no,
        ]);
    }

    public function settleForm(Request $request, int $id): JsonResponse
    {
        $this->authorizeMove();

        $vehicle = $this->vans->vehicle($this->businessId(), $id);
        if (! $vehicle->location_id) {
            return response()->json(['message' => 'This van has never been loaded, so there is nothing to settle.'], 422);
        }

        $date = Carbon::parse($request->query('date', 'today'));

        return response()->json($this->vans->draft($this->businessId(), $vehicle, $date) + ['warehouses' => $this->warehouses()]);
    }

    public function settle(Request $request, int $id): JsonResponse
    {
        $this->authorizeMove();

        $data = $request->validate([
            'date' => ['required', 'date', 'before_or_equal:today'],
            'lines' => ['array'],
            'lines.*.variation_id' => ['required', 'integer'],
            'lines.*.counted' => ['required', 'numeric', 'min:0'],
            'counted_cash' => ['required', 'numeric', 'min:0'],
            'counted_cheques' => ['nullable', 'numeric', 'min:0'],
            'unload' => ['boolean'],
            'to_location_id' => ['nullable', 'integer', 'required_if:unload,true'],
            'odometer_end' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], ['to_location_id.required_if' => 'Pick the warehouse the stock is going back to.']);

        if (! empty($data['to_location_id'])) {
            $this->assertWarehouse((int) $data['to_location_id']);
        }

        $vehicle = $this->vans->vehicle($this->businessId(), $id);
        if (! $vehicle->location_id) {
            return response()->json(['message' => 'This van has never been loaded.'], 422);
        }

        $payload = $data;
        $payload['lines'] = collect($data['lines'] ?? [])->mapWithKeys(fn ($l) => [(int) $l['variation_id'] => (float) $l['counted']])->all();

        try {
            $result = DB::transaction(fn () => $this->vans->settle($this->businessId(), $vehicle, $payload, (int) auth()->id()));
        } catch (OpsException|PurchaseSellMismatch $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json($result + [
            'message' => $result['status'] === 'settled'
                ? 'Van settled with no differences.'
                : 'Van settled. The differences are waiting for a manager to approve.',
            'slip_url' => route('ops.vans.slip', [$result['settlement_id']]),
        ]);
    }

    /**
     * The printable settlement slip the seller signs.
     */
    public function slip(int $settlementId)
    {
        $this->authorizeView();

        $s = DB::table('van_settlements AS s')
            ->join('supply_chain_vehicles AS v', 'v.id', '=', 's.supply_chain_vehicle_id')
            ->leftJoin('users AS u', 'u.id', '=', 's.created_by')
            ->where('s.business_id', $this->businessId())
            ->where('s.id', $settlementId)
            ->first(['s.*', 'v.license_plate', 'u.first_name', 'u.last_name']);

        abort_unless($s, 404);

        return view('ops.van_slip', [
            's' => $s,
            'lines' => json_decode($s->lines, true),
            'sellers' => json_decode($s->sellers ?? '[]', true),
            'business' => session('business.name'),
            'money' => fn ($v) => app(\App\Utils\TransactionUtil::class)->num_f($v, true),
        ]);
    }

    /**
     * @param  array<int, float>  $quantities  variation id => quantity
     * @return array<int, array<string, mixed>>
     */
    private function describe(array $quantities, int $locationId): array
    {
        if (! $quantities) {
            return [];
        }

        return DB::table('variations AS v')->join('products AS p', 'p.id', '=', 'v.product_id')
            ->leftJoin('units AS u', 'u.id', '=', 'p.unit_id')
            ->leftJoin('variation_location_details AS d', fn ($j) => $j->on('d.variation_id', '=', 'v.id')->where('d.location_id', $locationId))
            ->where('p.business_id', $this->businessId())
            ->whereIn('v.id', array_keys($quantities))
            ->get(['v.id', 'p.name', 'v.name AS variation', 'v.sub_sku', 'u.short_name', 'd.qty_available'])
            ->map(fn ($r) => [
                'variation_id' => (int) $r->id,
                'name' => trim($r->name.(($r->variation && $r->variation !== 'DUMMY') ? ' ('.$r->variation.')' : '')),
                'sku' => $r->sub_sku,
                'unit' => $r->short_name,
                'available' => round((float) ($r->qty_available ?? 0), 4),
                'quantity' => $quantities[$r->id],
            ])->values()->all();
    }

    /**
     * @return array<int, array{id: int, name: string}>
     */
    private function warehouses(): array
    {
        $permitted = auth()->user()->permitted_locations();

        return DB::table('business_locations')
            ->where('business_id', $this->businessId())
            ->where('is_active', 1)
            ->whereNull('supply_chain_vehicle_id')
            ->when($permitted !== 'all', fn ($q) => $q->whereIn('id', $permitted))
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($l) => ['id' => (int) $l->id, 'name' => $l->name])
            ->all();
    }

    private function assertWarehouse(int $locationId): void
    {
        if (! in_array($locationId, array_column($this->warehouses(), 'id'), true)) {
            abort(422, 'You cannot move stock through that location.');
        }
    }

    private function canMoveStock(): bool
    {
        return $this->isAdmin() || auth()->user()->can('purchase.create');
    }

    private function authorizeMove(): void
    {
        if (! $this->canMoveStock()) {
            abort(403, 'You do not have permission to move stock.');
        }
    }

    private function authorizeView(): void
    {
        if (! $this->canMoveStock() && ! auth()->user()->can('purchase.view')) {
            abort(403, 'You do not have permission to see van stock.');
        }
    }
}
