<?php

namespace App\Services\Ops;

use App\Business;
use App\BusinessLocation;
use App\Events\StockAdjustmentCreatedOrModified;
use App\Events\StockTransferCreatedOrModified;
use App\Exceptions\OpsException;
use App\Transaction;
use App\User;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

/**
 * Van stock: load out in the morning, settle at night.
 *
 * A van is a business location, so its stock is ordinary location stock.
 * Loading and unloading are stock transfers built the same way
 * StockTransferController@store builds them (sell_transfer out, purchase_transfer
 * in, purchase-sell mapping), and a shortage found at settlement is written
 * off with a stock adjustment — but only once a manager approves it, so a
 * seller's count never moves stock on its own.
 */
class VanStock
{
    public function __construct(
        private ProductUtil $productUtil,
        private TransactionUtil $transactionUtil,
        private Approvals $approvals,
    ) {}

    // ------------------------------------------------------------------
    // Vans and their locations
    // ------------------------------------------------------------------

    /**
     * @return array<int, array<string, mixed>>
     */
    public function vans(int $businessId, Carbon $date): array
    {
        $vans = DB::table('supply_chain_vehicles AS v')
            ->leftJoin('customer_routes AS r', 'r.id', '=', 'v.customer_route_id')
            ->where('v.business_id', $businessId)
            ->orderBy('v.license_plate')
            ->get(['v.id', 'v.license_plate', 'v.make', 'v.model', 'v.location_id', 'v.customer_route_id', 'r.name AS route']);

        $stock = DB::table('variation_location_details AS d')
            ->join('variations AS va', 'va.id', '=', 'd.variation_id')
            ->whereIn('d.location_id', $vans->pluck('location_id')->filter())
            ->groupBy('d.location_id')
            ->selectRaw('d.location_id, SUM(d.qty_available) AS units, SUM(d.qty_available * va.dpp_inc_tax) AS value')
            ->get()->keyBy('location_id');

        $settled = DB::table('van_settlements')->where('business_id', $businessId)->whereDate('settlement_date', $date)
            ->orderByDesc('id')->get(['supply_chain_vehicle_id', 'status', 'id'])->unique('supply_chain_vehicle_id')->keyBy('supply_chain_vehicle_id');

        $loaded = DB::table('transactions')->where('business_id', $businessId)->where('type', 'purchase_transfer')
            ->whereIn('location_id', $vans->pluck('location_id')->filter())->whereDate('transaction_date', $date)
            ->groupBy('location_id')->selectRaw('location_id, COUNT(*) AS n')->pluck('n', 'location_id');

        return $vans->map(fn ($v) => [
            'id' => (int) $v->id,
            'plate' => $v->license_plate ?: 'Van #'.$v->id,
            'name' => trim($v->make.' '.$v->model) ?: null,
            'route' => $v->route,
            'route_id' => $v->customer_route_id ? (int) $v->customer_route_id : null,
            'sellers' => $this->sellers((int) $v->customer_route_id)->pluck('name')->all(),
            'location_id' => $v->location_id ? (int) $v->location_id : null,
            'units' => round((float) ($stock[$v->location_id]->units ?? 0), 2),
            'value' => round((float) ($stock[$v->location_id]->value ?? 0), 2),
            'loaded_today' => (int) ($loaded[$v->location_id] ?? 0),
            'settlement' => isset($settled[$v->id]) ? ['id' => (int) $settled[$v->id]->id, 'status' => $settled[$v->id]->status] : null,
        ])->all();
    }

    public function vehicle(int $businessId, int $vehicleId): object
    {
        $v = DB::table('supply_chain_vehicles')->where('business_id', $businessId)->where('id', $vehicleId)->first();

        if (! $v) {
            abort(404, 'That van does not exist.');
        }

        return $v;
    }

    /**
     * The van's stock location, created on first use.
     */
    public function ensureLocation(int $businessId, object $vehicle): int
    {
        if ($vehicle->location_id && BusinessLocation::where('id', $vehicle->location_id)->exists()) {
            return (int) $vehicle->location_id;
        }

        $template = BusinessLocation::where('business_id', $businessId)->whereNull('supply_chain_vehicle_id')->orderBy('id')->firstOrFail();
        $refCount = $this->transactionUtil->setAndGetReferenceCount('business_location', $businessId);

        $location = BusinessLocation::create([
            'business_id' => $businessId,
            'name' => 'Van · '.($vehicle->license_plate ?: '#'.$vehicle->id),
            'location_id' => $this->transactionUtil->generateReferenceNumber('business_location', $refCount, $businessId),
            'landmark' => 'Mobile stock',
            'country' => $template->country,
            'state' => $template->state,
            'city' => $template->city,
            'zip_code' => $template->zip_code,
            'mobile' => $template->mobile,
            'invoice_scheme_id' => $template->invoice_scheme_id,
            'invoice_layout_id' => $template->invoice_layout_id,
            'sale_invoice_scheme_id' => $template->sale_invoice_scheme_id,
            'sale_invoice_layout_id' => $template->sale_invoice_layout_id,
            'selling_price_group_id' => $template->selling_price_group_id,
            'default_payment_accounts' => $template->default_payment_accounts,
            'supply_chain_vehicle_id' => $vehicle->id,
            'is_active' => 1,
        ]);

        Permission::firstOrCreate(['name' => 'location.'.$location->id, 'guard_name' => 'web']);
        DB::table('supply_chain_vehicles')->where('id', $vehicle->id)->update(['location_id' => $location->id]);
        $this->grantSellers((int) $vehicle->customer_route_id, $location->id);

        return $location->id;
    }

    /**
     * Sellers on the van's route may sell from the van's stock.
     */
    public function grantSellers(int $routeId, int $locationId): void
    {
        foreach ($this->sellers($routeId) as $s) {
            $user = User::find($s->id);
            if ($user && ! $user->can('access_all_locations') && ! $user->can('location.'.$locationId)) {
                $user->givePermissionTo('location.'.$locationId);
            }
        }
    }

    /**
     * @return \Illuminate\Support\Collection<int, object>
     */
    public function sellers(int $routeId)
    {
        if (! $routeId) {
            return collect();
        }

        return DB::table('route_seller_assignments AS a')->join('users AS u', 'u.id', '=', 'a.user_id')
            ->where('a.customer_route_id', $routeId)->where('a.is_active', 1)
            ->get(['u.id', DB::raw("TRIM(CONCAT(COALESCE(u.first_name,''),' ',COALESCE(u.last_name,''))) AS name")]);
    }

    /**
     * Stock at a location, with enough product detail to count it.
     *
     * @return array<int, array<string, mixed>>
     */
    public function stockAt(int $locationId, bool $onlyPositive = true): array
    {
        return DB::table('variation_location_details AS d')
            ->join('variations AS v', 'v.id', '=', 'd.variation_id')
            ->join('products AS p', 'p.id', '=', 'v.product_id')
            ->leftJoin('units AS u', 'u.id', '=', 'p.unit_id')
            ->where('d.location_id', $locationId)
            ->where('p.enable_stock', 1)
            ->when($onlyPositive, fn ($q) => $q->where('d.qty_available', '>', 0), fn ($q) => $q->where('d.qty_available', '!=', 0))
            ->orderBy('p.name')
            ->get(['v.id AS variation_id', 'p.id AS product_id', 'p.name AS product', 'v.name AS variation', 'v.sub_sku AS sku',
                'u.short_name AS unit', 'd.qty_available', 'v.dpp_inc_tax'])
            ->map(fn ($r) => [
                'variation_id' => (int) $r->variation_id,
                'product_id' => (int) $r->product_id,
                'name' => trim($r->product.(($r->variation && $r->variation !== 'DUMMY') ? ' ('.$r->variation.')' : '')),
                'sku' => $r->sku,
                'unit' => $r->unit,
                'qty' => round((float) $r->qty_available, 4),
                'cost' => round((float) $r->dpp_inc_tax, 4),
            ])
            ->all();
    }

    /**
     * What went on the van at its most recent load, to pre-fill today's.
     *
     * @return array<int, array{variation_id: int, quantity: float}>
     */
    public function lastLoad(int $locationId): array
    {
        $last = DB::table('transactions')->where('type', 'purchase_transfer')->where('location_id', $locationId)
            ->orderByDesc('transaction_date')->value('id');

        if (! $last) {
            return [];
        }

        return DB::table('purchase_lines')->where('transaction_id', $last)
            ->groupBy('variation_id')->selectRaw('variation_id, SUM(quantity) AS quantity')->get()
            ->map(fn ($l) => ['variation_id' => (int) $l->variation_id, 'quantity' => (float) $l->quantity])->all();
    }

    // ------------------------------------------------------------------
    // Load / unload
    // ------------------------------------------------------------------

    /**
     * @param  array<int, float>  $lines  variation id => quantity
     */
    public function load(int $businessId, object $vehicle, int $fromLocationId, array $lines, int $userId, ?string $note = null): Transaction
    {
        $vanLocation = $this->ensureLocation($businessId, $vehicle);

        return $this->transfer($businessId, $fromLocationId, $vanLocation, $lines, $userId,
            trim('Van load · '.($vehicle->license_plate ?: '#'.$vehicle->id).($note ? ' — '.$note : '')));
    }

    /**
     * A completed stock transfer, built as StockTransferController@store
     * builds one, so it lists, prints and reports like any other transfer.
     *
     * @param  array<int, float>  $lines  variation id => quantity
     */
    public function transfer(int $businessId, int $from, int $to, array $lines, int $userId, string $note): Transaction
    {
        $lines = array_filter($lines, fn ($q) => (float) $q > 0);
        if (! $lines) {
            throw new OpsException('Add at least one product with a quantity.');
        }
        if ($from === $to) {
            throw new OpsException('The source and destination are the same location.');
        }

        $locations = BusinessLocation::where('business_id', $businessId)->whereIn('id', [$from, $to])->pluck('name', 'id');
        if ($locations->count() !== 2) {
            throw new OpsException('One of those locations does not belong to this business.');
        }

        $variations = DB::table('variations AS v')->join('products AS p', 'p.id', '=', 'v.product_id')
            ->where('p.business_id', $businessId)->whereIn('v.id', array_keys($lines))
            ->get(['v.id', 'v.product_id', 'v.dpp_inc_tax', 'p.enable_stock', 'p.unit_id', 'p.name', 'p.type'])->keyBy('id');

        $available = DB::table('variation_location_details')->where('location_id', $from)
            ->whereIn('variation_id', array_keys($lines))->pluck('qty_available', 'variation_id');

        $sellLines = [];
        $purchaseLines = [];
        $total = 0;

        foreach ($lines as $variationId => $qty) {
            $v = $variations[$variationId] ?? null;
            if (! $v || ! in_array($v->type, ['single', 'variable'], true)) {
                throw new OpsException('One of the products cannot be transferred.');
            }

            $qty = (float) $qty;
            if ($v->enable_stock && $qty > (float) ($available[$variationId] ?? 0) + 0.0001) {
                throw new OpsException(sprintf('Only %s of %s at %s.',
                    rtrim(rtrim(number_format((float) ($available[$variationId] ?? 0), 2), '0'), '.'), $v->name, $locations[$from]));
            }

            $price = (float) $v->dpp_inc_tax;
            $total += $price * $qty;

            $sellLines[] = [
                'product_id' => $v->product_id, 'variation_id' => $v->id, 'quantity' => $qty,
                'item_tax' => 0, 'tax_id' => null, 'product_unit_id' => $v->unit_id,
                'unit_price' => $price, 'unit_price_inc_tax' => $price,
            ];
            $purchaseLines[] = [
                'product_id' => $v->product_id, 'variation_id' => $v->id, 'quantity' => $qty,
                'item_tax' => 0, 'tax_id' => null, 'purchase_price' => $price, 'purchase_price_inc_tax' => $price,
            ];
        }

        $refCount = $this->productUtil->setAndGetReferenceCount('stock_transfer', $businessId);
        $data = [
            'business_id' => $businessId,
            'location_id' => $from,
            'type' => 'sell_transfer',
            'status' => 'final',
            'payment_status' => 'paid',
            'ref_no' => $this->productUtil->generateReferenceNumber('stock_transfer', $refCount, $businessId),
            'transaction_date' => now()->toDateTimeString(),
            'additional_notes' => mb_substr($note, 0, 250),
            'shipping_charges' => 0,
            'final_total' => $total,
            'total_before_tax' => $total,
            'created_by' => $userId,
        ];

        $sellTransfer = Transaction::create($data);
        $purchaseTransfer = Transaction::create(array_merge($data, [
            'type' => 'purchase_transfer',
            'location_id' => $to,
            'transfer_parent_id' => $sellTransfer->id,
            'status' => 'received',
        ]));

        $this->transactionUtil->createOrUpdateSellLines($sellTransfer, $sellLines, $from, false, null, [], false);
        $purchaseTransfer->purchase_lines()->createMany($purchaseLines);

        foreach ($lines as $variationId => $qty) {
            $v = $variations[$variationId];
            if ($v->enable_stock) {
                $this->productUtil->decreaseProductQuantity($v->product_id, $v->id, $from, (float) $qty);
                $this->productUtil->updateProductQuantity($to, $v->product_id, $v->id, (float) $qty, 0, null, false);
            }
        }

        $this->productUtil->adjustStockOverSelling($purchaseTransfer);
        $this->transactionUtil->mapPurchaseSell($this->businessArray($businessId, $from), $sellTransfer->sell_lines, 'purchase');
        $this->transactionUtil->activityLog($sellTransfer, 'added');
        event(new StockTransferCreatedOrModified($sellTransfer, 'added'));

        return $sellTransfer;
    }

    // ------------------------------------------------------------------
    // Settlement
    // ------------------------------------------------------------------

    /**
     * Everything the settle modal needs: what the system says is on the van,
     * what came on and went off today, and the money the sellers took.
     *
     * @return array<string, mixed>
     */
    public function draft(int $businessId, object $vehicle, Carbon $date): array
    {
        $location = (int) $vehicle->location_id;
        $sellers = $this->sellers((int) $vehicle->customer_route_id);
        $sellerIds = $sellers->pluck('id')->all();

        $loaded = DB::table('purchase_lines AS pl')->join('transactions AS t', 't.id', '=', 'pl.transaction_id')
            ->where('t.type', 'purchase_transfer')->where('t.location_id', $location)->whereDate('t.transaction_date', $date)
            ->groupBy('pl.variation_id')->selectRaw('pl.variation_id, SUM(pl.quantity) AS q')->pluck('q', 'variation_id');

        $sold = DB::table('transaction_sell_lines AS l')->join('transactions AS t', 't.id', '=', 'l.transaction_id')
            ->where('t.type', 'sell')->where('t.status', 'final')->where('t.location_id', $location)->whereDate('t.transaction_date', $date)
            ->groupBy('l.variation_id')->selectRaw('l.variation_id, SUM(l.quantity - l.quantity_returned) AS q')->pluck('q', 'variation_id');

        $lines = array_map(fn ($s) => $s + [
            'loaded_today' => round((float) ($loaded[$s['variation_id']] ?? 0), 4),
            'sold_today' => round((float) ($sold[$s['variation_id']] ?? 0), 4),
        ], $this->stockAt($location, false));

        $payments = $sellerIds ? DB::table('transaction_payments')
            ->where('business_id', $businessId)->whereNull('parent_id')->where('is_return', 0)
            ->whereIn('created_by', $sellerIds)->whereDate('paid_on', $date)
            ->groupBy('method')->selectRaw('method, SUM(amount) AS total')->pluck('total', 'method') : collect();

        $sales = DB::table('transactions')->where('business_id', $businessId)->where('type', 'sell')->where('status', 'final')
            ->where('location_id', $location)->whereDate('transaction_date', $date)
            ->selectRaw('COUNT(*) AS n, COALESCE(SUM(final_total), 0) AS total')->first();

        $odometer = DB::table('supply_chain_vehicle_mileage')->where('supply_chain_vehicle_id', $vehicle->id)
            ->whereDate('date', $date)->orderByDesc('id')->first(['start_mileage', 'end_mileage']);

        $previous = DB::table('van_settlements')->where('supply_chain_vehicle_id', $vehicle->id)->whereDate('settlement_date', $date)
            ->orderByDesc('id')->first(['id', 'status', 'created_at']);

        return [
            'van' => ['id' => (int) $vehicle->id, 'plate' => $vehicle->license_plate ?: 'Van #'.$vehicle->id, 'location_id' => $location],
            'date' => $date->toDateString(),
            'sellers' => $sellers->pluck('name')->all(),
            'lines' => $lines,
            'money' => [
                'cash' => round((float) ($payments['cash'] ?? 0), 2),
                'cheque' => round((float) ($payments['cheque'] ?? 0), 2),
                'other' => round((float) $payments->except(['cash', 'cheque'])->sum(), 2),
            ],
            'sales' => ['count' => (int) $sales->n, 'total' => round((float) $sales->total, 2)],
            'odometer' => $odometer ? ['start' => (int) $odometer->start_mileage, 'end' => (int) $odometer->end_mileage] : null,
            'previous' => $previous ? ['id' => (int) $previous->id, 'status' => $previous->status] : null,
        ];
    }

    /**
     * Record the count. Counted stock can be unloaded to the warehouse on the
     * spot; any shortage, overage or cash difference waits for a manager.
     *
     * @param  array<string, mixed>  $data  date, lines [variation_id => counted], counted_cash, counted_cheques, unload, to_location_id, odometer_end, notes
     * @return array<string, mixed>
     */
    public function settle(int $businessId, object $vehicle, array $data, int $userId): array
    {
        $date = Carbon::parse($data['date'] ?? 'today');
        $draft = $this->draft($businessId, $vehicle, $date);

        $rows = [];
        $shortValue = 0.0;
        $overValue = 0.0;
        $unload = [];

        foreach ($draft['lines'] as $line) {
            $counted = (float) ($data['lines'][$line['variation_id']] ?? $line['qty']);
            $diff = round($counted - $line['qty'], 4);

            if ($diff < 0) {
                $shortValue += -$diff * $line['cost'];
            } elseif ($diff > 0) {
                $overValue += $diff * $line['cost'];
            }

            $rows[] = $line + ['counted' => $counted, 'diff' => $diff];

            if (! empty($data['unload']) && $counted > 0) {
                $unload[$line['variation_id']] = min($counted, $line['qty']);
            }
        }

        $countedCash = round((float) ($data['counted_cash'] ?? 0), 2);
        $countedCheques = round((float) ($data['counted_cheques'] ?? 0), 2);
        $cashDiff = round($countedCash - $draft['money']['cash'], 2);
        $chequeDiff = round($countedCheques - $draft['money']['cheque'], 2);

        $hasVariance = $shortValue > 0.004 || $overValue > 0.004 || abs($cashDiff) > 0.004 || abs($chequeDiff) > 0.004;

        $transferId = null;
        if ($unload) {
            if (empty($data['to_location_id'])) {
                throw new OpsException('Pick the warehouse the stock is going back to.');
            }
            $transferId = $this->transfer($businessId, $draft['van']['location_id'], (int) $data['to_location_id'], $unload, $userId,
                'Van unload · '.$draft['van']['plate'])->id;
        }

        $settlementId = DB::table('van_settlements')->insertGetId([
            'business_id' => $businessId,
            'supply_chain_vehicle_id' => $vehicle->id,
            'location_id' => $draft['van']['location_id'],
            'settlement_date' => $date->toDateString(),
            'status' => $hasVariance ? 'pending_approval' : 'settled',
            'expected_cash' => $draft['money']['cash'],
            'counted_cash' => $countedCash,
            'expected_cheques' => $draft['money']['cheque'],
            'counted_cheques' => $countedCheques,
            'stock_short_value' => round($shortValue, 4),
            'stock_over_value' => round($overValue, 4),
            'lines' => json_encode($rows),
            'sellers' => json_encode($draft['sellers']),
            'unload_transfer_id' => $transferId,
            'odometer_end' => $data['odometer_end'] ?? null,
            'notes' => isset($data['notes']) ? mb_substr((string) $data['notes'], 0, 500) : null,
            'created_by' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $approvalId = null;
        if ($hasVariance) {
            $parts = array_filter([
                $shortValue > 0.004 ? 'stock short '.number_format($shortValue, 0) : null,
                $overValue > 0.004 ? 'stock over '.number_format($overValue, 0) : null,
                abs($cashDiff) > 0.004 ? 'cash '.($cashDiff < 0 ? 'short ' : 'over ').number_format(abs($cashDiff), 0) : null,
                abs($chequeDiff) > 0.004 ? 'cheques '.($chequeDiff < 0 ? 'short ' : 'over ').number_format(abs($chequeDiff), 0) : null,
            ]);

            $approvalId = $this->approvals->request($businessId, 'van_variance',
                sprintf('%s settlement %s: %s', $draft['van']['plate'], $date->format('d M'), implode(', ', $parts)), [
                    'subject_type' => 'van_settlement',
                    'subject_id' => $settlementId,
                    'amount' => round($shortValue + max(0, -$cashDiff) + max(0, -$chequeDiff), 2),
                    'payload' => ['reason' => $data['notes'] ?? null, 'sellers' => $draft['sellers']],
                    'requested_by' => $userId,
                ]);

            DB::table('van_settlements')->where('id', $settlementId)->update(['approval_id' => $approvalId]);
        }

        return [
            'settlement_id' => $settlementId,
            'status' => $hasVariance ? 'pending_approval' : 'settled',
            'short_value' => round($shortValue, 2),
            'over_value' => round($overValue, 2),
            'cash_diff' => $cashDiff,
            'cheque_diff' => $chequeDiff,
            'unloaded' => (bool) $transferId,
            'approval_id' => $approvalId,
        ];
    }

    /**
     * A manager accepted the variance: write the missing stock off the van.
     */
    public function postApprovedVariance(object $approval): void
    {
        $s = DB::table('van_settlements')->where('id', $approval->subject_id)->first();
        if (! $s || $s->status !== 'pending_approval') {
            return;
        }

        $short = [];
        foreach (json_decode($s->lines, true) as $line) {
            if ($line['diff'] < 0) {
                $short[$line['variation_id']] = -$line['diff'];
            }
        }

        $adjustmentId = $short ? $this->writeOff((int) $s->business_id, (int) $s->location_id, $short, (int) $approval->decided_by,
            'Van settlement #'.$s->id.' shortage, approved')->id : null;

        DB::table('van_settlements')->where('id', $s->id)->update([
            'status' => 'approved', 'adjustment_id' => $adjustmentId, 'updated_at' => now(),
        ]);
    }

    public function rejectVariance(object $approval): void
    {
        DB::table('van_settlements')->where('id', $approval->subject_id)->where('status', 'pending_approval')
            ->update(['status' => 'rejected', 'updated_at' => now()]);
    }

    /**
     * A stock adjustment built as StockAdjustmentController@store builds one.
     *
     * @param  array<int, float>  $lines  variation id => quantity to remove
     */
    private function writeOff(int $businessId, int $locationId, array $lines, int $userId, string $note): Transaction
    {
        $variations = DB::table('variations')->whereIn('id', array_keys($lines))->get(['id', 'product_id', 'dpp_inc_tax'])->keyBy('id');
        $refCount = $this->productUtil->setAndGetReferenceCount('stock_adjustment', $businessId);

        $adjustmentLines = [];
        $total = 0;
        foreach ($lines as $variationId => $qty) {
            $v = $variations[$variationId];
            $adjustmentLines[] = ['product_id' => $v->product_id, 'variation_id' => $v->id, 'quantity' => $qty, 'unit_price' => $v->dpp_inc_tax];
            $total += $qty * $v->dpp_inc_tax;
            $this->productUtil->decreaseProductQuantity($v->product_id, $v->id, $locationId, $qty);
        }

        $adjustment = Transaction::create([
            'business_id' => $businessId,
            'location_id' => $locationId,
            'type' => 'stock_adjustment',
            'adjustment_type' => 'abnormal',
            'transaction_date' => now()->toDateTimeString(),
            'additional_notes' => $note,
            'total_amount_recovered' => 0,
            'final_total' => $total,
            'ref_no' => $this->productUtil->generateReferenceNumber('stock_adjustment', $refCount, $businessId),
            'created_by' => $userId,
        ]);
        $adjustment->stock_adjustment_lines()->createMany($adjustmentLines);

        $this->transactionUtil->mapPurchaseSell($this->businessArray($businessId, $locationId), $adjustment->stock_adjustment_lines, 'stock_adjustment');
        event(new StockAdjustmentCreatedOrModified($adjustment, 'added'));
        $this->transactionUtil->activityLog($adjustment, 'added', null, [], false);

        return $adjustment;
    }

    /**
     * @return array<string, mixed>
     */
    private function businessArray(int $businessId, int $locationId): array
    {
        $business = Business::find($businessId);

        return ['id' => $businessId, 'accounting_method' => $business->accounting_method, 'location_id' => $locationId];
    }
}
