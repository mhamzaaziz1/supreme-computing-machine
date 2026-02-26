<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Variation;
use Illuminate\Support\Facades\DB;

use App\Utils\Util;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\BusinessUtil;
use App\Utils\ContactUtil;
use App\Utils\TransactionUtil;
use App\Utils\CashRegisterUtil;
use App\BusinessLocation;
use App\Category;
use App\Brands;
use Inertia\Inertia;

class PosV2Controller extends Controller
{
    protected $moduleUtil;
    protected $productUtil;
    protected $businessUtil;
    protected $contactUtil;
    protected $transactionUtil;
    protected $cashRegisterUtil;
    protected $commonUtil;

    public function __construct(
        ModuleUtil $moduleUtil,
        ProductUtil $productUtil,
        BusinessUtil $businessUtil,
        ContactUtil $contactUtil,
        TransactionUtil $transactionUtil,
        CashRegisterUtil $cashRegisterUtil,
        Util $commonUtil
    ) {
        $this->moduleUtil = $moduleUtil;
        $this->productUtil = $productUtil;
        $this->businessUtil = $businessUtil;
        $this->contactUtil = $contactUtil;
        $this->transactionUtil = $transactionUtil;
        $this->cashRegisterUtil = $cashRegisterUtil;
        $this->commonUtil = $commonUtil;
    }

    public function index()
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || auth()->user()->can('sell.create') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module') && auth()->user()->can('repair.create')))) {
            abort(403, 'Unauthorized action.');
        }

        // Check if there is a open register
        if ($this->cashRegisterUtil->countOpenedRegister() == 0) {
            return redirect()->action([\App\Http\Controllers\CashRegisterController::class, 'create']);
        }

        $register_details = $this->cashRegisterUtil->getCurrentCashRegister(auth()->user()->id);
        $default_location = !empty($register_details->location_id) ? BusinessLocation::findOrFail($register_details->location_id) : null;

        $business_locations = BusinessLocation::forDropdown($business_id, false, true);
        $bl_attributes = $business_locations['attributes'];
        $business_locations = $business_locations['locations'];

        // If brands, category are enabled then send else false.
        $categories = (request()->session()->get('business.enable_category') == 1) ? Category::catAndSubCategories($business_id) : [];
        $brands = (request()->session()->get('business.enable_brand') == 1) ? Brands::forDropdown($business_id)->prepend(__('lang_v1.all_brands'), 'all') : [];

        Inertia::setRootView('app');
        return Inertia::render('Pos/Index', [
            'locations' => $business_locations,
            'default_location' => $default_location,
            'categories' => $categories,
            'brands' => $brands,
            'user' => auth()->user(),
        ]);
    }

    public function getProducts(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $location_id = $request->get('location_id');
        $category_id = $request->get('category_id');
        $brand_id = $request->get('brand_id');
        $term = $request->get('term');

        $products = Variation::join('products as p', 'variations.product_id', '=', 'p.id')
            ->join('product_locations as pl', 'pl.product_id', '=', 'p.id')
            ->join('units as u', 'p.unit_id', '=', 'u.id')
            ->leftjoin(
                'variation_location_details AS VLD',
                function ($join) use ($location_id) {
                    $join->on('variations.id', '=', 'VLD.variation_id');
                    if (!empty($location_id)) {
                        $join->where(function ($query) use ($location_id) {
                            $query->where('VLD.location_id', '=', $location_id);
                            $query->orWhereNull('VLD.location_id');
                        });
                    }
                }
            )
            ->where('p.business_id', $business_id)
            ->where('p.type', '!=', 'modifier')
            ->where('p.is_inactive', 0)
            ->where('p.not_for_selling', 0)
            ->where(function ($q) use ($location_id) {
                if (!empty($location_id)) {
                     $q->where('pl.location_id', $location_id);
                }
            });

        if (!empty($term)) {
            $products->where(function ($query) use ($term) {
                $query->where('p.name', 'like', '%' . $term . '%');
                $query->orWhere('p.sku', 'like', '%' . $term . '%');
                $query->orWhere('variations.sub_sku', 'like', '%' . $term . '%');
            });
        }

        if (!empty($category_id) && $category_id != 'all') {
            $products->where(function ($query) use ($category_id) {
                $query->where('p.category_id', $category_id);
                $query->orWhere('p.sub_category_id', $category_id);
            });
        }

        if (!empty($brand_id) && $brand_id != 'all') {
            $products->where('p.brand_id', $brand_id);
        }

        $products = $products->select(
            'p.id as product_id',
            'p.name',
            'p.type',
            'p.enable_stock',
            'p.image as product_image',
            'variations.id as variation_id',
            'variations.name as variation',
            'VLD.qty_available',
            'variations.default_sell_price as selling_price',
            'variations.sub_sku',
            'u.short_name as unit'
        )
        ->groupBy('variations.id')
        ->paginate(20);

        return response()->json($products);
    }
}
