<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LabelsController;
use App\Http\Controllers\OpeningStockController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The product catalogue, rebuilt on Inertia.
 *
 * The legacy list selects the twenty product_custom_field columns, five
 * joined lookups and four price aggregates so DataTables can render six of
 * them, then re-runs it on every sort, filter and page step. This asks for
 * what the table shows: name, SKU, category, brand, price band and stock.
 *
 * Stock is the reason this screen gets opened, so it is a first-class column
 * with the alert threshold applied, rather than a number you go elsewhere to
 * check. Permission scoping is ported from ProductController@index: the same
 * 403 gate and the same restriction to permitted locations.
 */
class ProductListController extends Controller
{
    private const PER_PAGE = [25, 50, 100];

    public function index(Request $request): Response
    {
        if (! auth()->user()->can('product.view') && ! auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        $businessId = $request->session()->get('user.business_id');
        $filters = $this->filters($request);

        $rows = $this->query($businessId, $filters)
            ->orderBy('products.name')
            ->paginate($filters['per_page'])
            ->withQueryString();

        $can = [
            'view' => auth()->user()->can('product.view'),
            'update' => auth()->user()->can('product.update'),
            'delete' => auth()->user()->can('product.delete'),
            'create' => auth()->user()->can('product.create'),
            'opening_stock' => auth()->user()->can('product.opening_stock'),
        ];

        return Inertia::render('Products/Index', [
            'filters' => $filters,
            'summary' => $this->summary($businessId, $filters),
            'categories' => $this->lookup('categories', $businessId, ['category_type' => 'product']),
            'brands' => $this->lookup('brands', $businessId),
            'locations' => $this->locations($businessId),
            'products' => [
                'data' => collect($rows->items())->map(fn ($r) => $this->present($r, $can))->all(),
                'from' => $rows->firstItem(),
                'to' => $rows->lastItem(),
                'total' => $rows->total(),
                'current_page' => $rows->currentPage(),
                'last_page' => $rows->lastPage(),
            ],
            'perPageOptions' => self::PER_PAGE,
            'links' => [
                'add' => $can['create'] ? action([ProductController::class, 'create']) : null,
                'labels' => action([LabelsController::class, 'show']),
                'import' => $can['create'] ? url('import-products') : null,
            ],
        ]);
    }

    /**
     * One product, as JSON for the detail drawer.
     *
     * The legacy "View" action renders product.view-modal, a Bootstrap
     * fragment meant to be dropped into a modal by jQuery. The Inertia shell
     * ships neither, so linking to it hands the user raw unstyled markup.
     * This returns data and the drawer renders it.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        if (! auth()->user()->can('product.view') && ! auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        $businessId = $request->session()->get('user.business_id');
        $permitted = auth()->user()->permitted_locations();

        $product = DB::table('products')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            ->join('units', 'units.id', '=', 'products.unit_id')
            ->leftJoin('categories AS c', 'c.id', '=', 'products.category_id')
            ->leftJoin('categories AS sc', 'sc.id', '=', 'products.sub_category_id')
            ->leftJoin('tax_rates AS t', 't.id', '=', 'products.tax')
            ->where('products.business_id', $businessId)
            ->where('products.id', $id)
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                'products.type',
                'products.enable_stock',
                'products.alert_quantity',
                'products.is_inactive',
                'products.not_for_selling',
                'products.product_description',
                'products.weight',
                'products.pack_litres',
                'c.name AS category',
                'sc.name AS sub_category',
                'brands.name AS brand',
                'units.actual_name AS unit',
                't.name AS tax',
            )
            ->first();

        if (! $product) {
            abort(404);
        }

        $variations = DB::table('variations AS v')
            ->leftJoin('product_variations AS pv', 'pv.id', '=', 'v.product_variation_id')
            ->where('v.product_id', $id)
            ->whereNull('v.deleted_at')
            ->orderBy('v.id')
            ->get(['v.id', 'v.name', 'v.sub_sku', 'v.sell_price_inc_tax', 'v.dpp_inc_tax', 'pv.name AS group_name'])
            ->map(fn ($v) => [
                'id' => (int) $v->id,
                // "DUMMY" is how a single product's only variation is stored.
                'name' => $v->name === 'DUMMY' ? null : trim(($v->group_name ? $v->group_name.': ' : '').$v->name),
                'sku' => $v->sub_sku,
                'purchase_price' => (float) $v->dpp_inc_tax,
                'sell_price' => (float) $v->sell_price_inc_tax,
            ])
            ->all();

        // Stock per location, restricted to what this user may see.
        $stock = DB::table('variation_location_details AS vld')
            ->join('business_locations AS bl', 'bl.id', '=', 'vld.location_id')
            ->join('variations AS v', 'v.id', '=', 'vld.variation_id')
            ->where('v.product_id', $id)
            ->whereNull('v.deleted_at')
            ->when($permitted !== 'all', fn ($q) => $q->whereIn('vld.location_id', $permitted))
            ->groupBy('bl.id', 'bl.name')
            ->orderBy('bl.name')
            ->get(['bl.name', DB::raw('SUM(vld.qty_available) AS qty')])
            ->map(fn ($r) => ['location' => $r->name, 'qty' => (float) $r->qty])
            ->all();

        return response()->json([
            'product' => [
                'id' => (int) $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'type' => $product->type,
                'category' => $product->category,
                'sub_category' => $product->sub_category,
                'brand' => $product->brand,
                'unit' => $product->unit,
                'tax' => $product->tax,
                'tracked' => (int) $product->enable_stock === 1,
                'alert_quantity' => $product->alert_quantity === null ? null : (float) $product->alert_quantity,
                'inactive' => (int) $product->is_inactive === 1,
                'not_for_selling' => (int) $product->not_for_selling === 1,
                'description' => $product->product_description,
                'weight' => $product->weight,
                'pack_litres' => $product->pack_litres !== null ? (float) $product->pack_litres : null,
                'can_edit_pack' => auth()->user()->can('product.update'),
            ],
            'variations' => $variations,
            'stock' => $stock,
            'total_stock' => array_sum(array_column($stock, 'qty')),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function filters(Request $request): array
    {
        $perPage = (int) $request->input('per_page', self::PER_PAGE[0]);

        return [
            'search' => trim((string) $request->input('search', '')) ?: null,
            'category_id' => $request->filled('category_id') ? (int) $request->input('category_id') : null,
            'brand_id' => $request->filled('brand_id') ? (int) $request->input('brand_id') : null,
            'location_id' => $request->filled('location_id') ? (int) $request->input('location_id') : null,
            'type' => in_array($request->input('type'), ['single', 'variable', 'combo'], true) ? $request->input('type') : null,
            'stock' => in_array($request->input('stock'), ['low', 'out'], true) ? $request->input('stock') : null,
            'active_state' => in_array($request->input('active_state'), ['active', 'inactive'], true)
                ? $request->input('active_state')
                : 'active',
            'per_page' => in_array($perPage, self::PER_PAGE, true) ? $perPage : self::PER_PAGE[0],
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function query(int $businessId, array $filters): \Illuminate\Database\Query\Builder
    {
        $permitted = auth()->user()->permitted_locations();

        $products = DB::table('products')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            ->join('units', 'units.id', '=', 'products.unit_id')
            ->leftJoin('categories AS c', 'c.id', '=', 'products.category_id')
            ->join('variations AS v', 'v.product_id', '=', 'products.id')
            ->leftJoin('variation_location_details AS vld', function ($join) use ($permitted, $filters) {
                $join->on('vld.variation_id', '=', 'v.id');

                // Stock is only counted where this user may see it, and
                // narrows further when a location filter is applied.
                if ($permitted !== 'all') {
                    $join->whereIn('vld.location_id', $permitted);
                }

                if ($filters['location_id']) {
                    $join->where('vld.location_id', $filters['location_id']);
                }
            })
            ->whereNull('v.deleted_at')
            ->where('products.business_id', $businessId)
            ->where('products.type', '!=', 'modifier')
            ->groupBy('products.id')
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                'products.type',
                'products.enable_stock',
                'products.is_inactive',
                'products.not_for_selling',
                'products.alert_quantity',
                'products.image',
                'c.name AS category',
                'brands.name AS brand',
                'units.actual_name AS unit',
                DB::raw('SUM(vld.qty_available) AS current_stock'),
                DB::raw('MIN(v.sell_price_inc_tax) AS min_price'),
                DB::raw('MAX(v.sell_price_inc_tax) AS max_price'),
            );

        // A product is only visible where it is assigned to a permitted location.
        if ($permitted !== 'all') {
            $products->whereExists(fn ($q) => $q
                ->select(DB::raw(1))
                ->from('product_locations')
                ->whereColumn('product_locations.product_id', 'products.id')
                ->whereIn('product_locations.location_id', $permitted));
        }

        if ($filters['location_id']) {
            $products->whereExists(fn ($q) => $q
                ->select(DB::raw(1))
                ->from('product_locations')
                ->whereColumn('product_locations.product_id', 'products.id')
                ->where('product_locations.location_id', $filters['location_id']));
        }

        if ($filters['search']) {
            $term = '%'.$filters['search'].'%';
            $products->where(fn ($q) => $q
                ->where('products.name', 'like', $term)
                ->orWhere('products.sku', 'like', $term)
                ->orWhere('v.sub_sku', 'like', $term));
        }

        if ($filters['category_id']) {
            $products->where('products.category_id', $filters['category_id']);
        }

        if ($filters['brand_id']) {
            $products->where('products.brand_id', $filters['brand_id']);
        }

        if ($filters['type']) {
            $products->where('products.type', $filters['type']);
        }

        $products->where('products.is_inactive', $filters['active_state'] === 'inactive' ? 1 : 0);

        // Stock comparisons are aggregates, so they belong in HAVING.
        if ($filters['stock'] === 'out') {
            $products->having(DB::raw('COALESCE(SUM(vld.qty_available), 0)'), '<=', 0);
        } elseif ($filters['stock'] === 'low') {
            $products->havingRaw('COALESCE(SUM(vld.qty_available), 0) <= COALESCE(products.alert_quantity, 0)')
                ->where('products.enable_stock', 1);
        }

        return $products;
    }

    /**
     * Counts for the filtered set. Wrapped rather than aggregated inline
     * because the list groups by product, and counting groups needs the
     * grouping to happen first.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, int>
     */
    private function summary(int $businessId, array $filters): array
    {
        // The tiles double as stock filters, so they count against everything
        // *except* the stock filter. Counting the filtered set instead would
        // make clicking "Low stock" zero the other two and report itself as
        // the total — the numbers would move every time you used them.
        $base = [...$filters, 'stock' => null];

        $count = fn (array $f) => DB::query()->fromSub($this->query($businessId, $f), 'p')->count();

        return [
            'products' => $count($base),
            'low_stock' => $count([...$base, 'stock' => 'low']),
            'out_of_stock' => $count([...$base, 'stock' => 'out']),
        ];
    }

    /**
     * @param  array<string, mixed>  $where
     * @return array<int, array<string, mixed>>
     */
    private function lookup(string $table, int $businessId, array $where = []): array
    {
        return DB::table($table)
            ->where('business_id', $businessId)
            ->where($where)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($r) => ['id' => (int) $r->id, 'name' => $r->name])
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function locations(int $businessId): array
    {
        $permitted = auth()->user()->permitted_locations();

        return DB::table('business_locations')
            ->where('business_id', $businessId)
            ->where('is_active', 1)
            ->when($permitted !== 'all', fn ($q) => $q->whereIn('id', $permitted))
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($l) => ['id' => (int) $l->id, 'name' => $l->name])
            ->all();
    }

    /**
     * @param  array<string, bool>  $can
     * @return array<string, mixed>
     */
    private function present(object $row, array $can): array
    {
        $stock = $row->current_stock === null ? null : (float) $row->current_stock;
        $alert = $row->alert_quantity === null ? null : (float) $row->alert_quantity;
        $tracked = (int) $row->enable_stock === 1;

        $actions = [
            'show' => $can['view'] ? route('catalog.show', [$row->id]) : null,
            'edit' => $can['update'] ? action([ProductController::class, 'edit'], [$row->id]) : null,
            'delete' => $can['delete'] ? action([ProductController::class, 'destroy'], [$row->id]) : null,
            'activate' => $can['update'] && (int) $row->is_inactive === 1
                ? action([ProductController::class, 'activate'], [$row->id])
                : null,
            'openingStock' => $can['opening_stock'] && $tracked
                ? action([OpeningStockController::class, 'add'], ['product_id' => $row->id])
                : null,
            'history' => $can['view'] ? action([ProductController::class, 'productStockHistory'], [$row->id]) : null,
            'sellingPrices' => $can['create'] ? action([ProductController::class, 'addSellingPrices'], [$row->id]) : null,
            'duplicate' => $can['create'] ? action([ProductController::class, 'create'], ['d' => $row->id]) : null,
        ];

        return [
            'id' => (int) $row->id,
            'name' => $row->name,
            'sku' => $row->sku,
            'type' => $row->type,
            'category' => $row->category,
            'brand' => $row->brand,
            'unit' => $row->unit,
            'tracked' => $tracked,
            'stock' => $stock,
            'alert_quantity' => $alert,
            // Precomputed so the table does not re-derive the rule per row.
            'low' => $tracked && $alert !== null && $alert > 0 && ($stock ?? 0) <= $alert,
            'out' => $tracked && ($stock ?? 0) <= 0,
            'min_price' => (float) $row->min_price,
            'max_price' => (float) $row->max_price,
            'inactive' => (int) $row->is_inactive === 1,
            'not_for_selling' => (int) $row->not_for_selling === 1,
            'actions' => array_filter($actions),
        ];
    }
}
