<?php

namespace App\Http\Controllers;

use App\Brands;
use App\BusinessLocation;
use App\CashRegister;
use App\Category;
use App\Charts\CommonChart;
use App\Contact;
use App\CustomerGroup;
use App\CustomerRoute;
use App\ExpenseCategory;
use App\Product;
use App\PurchaseLine;
use App\Restaurant\ResTable;
use App\RouteFollowup;
use App\SellingPriceGroup;
use App\TaxRate;
use App\Transaction;
use App\TransactionPayment;
use App\TransactionSellLine;
use App\TransactionSellLinesPurchaseLines;
use App\Unit;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Variation;
use Datatables;
use DB;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ReportController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $transactionUtil;

    protected $productUtil;

    protected $moduleUtil;

    protected $businessUtil;

    protected $geofenceUtil;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, BusinessUtil $businessUtil, \App\Utils\GeofenceUtil $geofenceUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->businessUtil = $businessUtil;
        $this->geofenceUtil = $geofenceUtil;
    }

    public function getStockBySellingPrice(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $location_id = $request->get('location_id');

        $day_before_start_date = \Carbon::createFromFormat('Y-m-d', $start_date)->subDay()->format('Y-m-d');

        $permitted_locations = auth()->user()->permitted_locations();

        $opening_stock_by_sp = $this->transactionUtil->getOpeningClosingStock($business_id, $day_before_start_date, $location_id, true, true, $permitted_locations);

        $closing_stock_by_sp = $this->transactionUtil->getOpeningClosingStock($business_id, $end_date, $location_id, false, true, $permitted_locations);

        return [
            'opening_stock_by_sp' => $opening_stock_by_sp,
            'closing_stock_by_sp' => $closing_stock_by_sp,
        ];
    }

    /**
     * Shows profit\loss of a business
     *
     * @return \Illuminate\Http\Response
     */
    public function getProfitLoss(Request $request)
    {
        if (! auth()->user()->can('profit_loss_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $fy = $this->businessUtil->getCurrentFinancialYear($business_id);

            $location_id = ! empty($request->get('location_id')) ? $request->get('location_id') : null;
            $start_date = ! empty($request->get('start_date')) ? $request->get('start_date') : $fy['start'];
            $end_date = ! empty($request->get('end_date')) ? $request->get('end_date') : $fy['end'];

            $user_id = request()->input('user_id') ?? null;

            $permitted_locations = auth()->user()->permitted_locations();

            // Create a cache key based on the parameters
            $cache_key = "profit_loss_{$business_id}_{$location_id}_{$start_date}_{$end_date}_{$user_id}_" . implode('_', $permitted_locations);

            // Get data from cache or compute it if not cached
            $data = \Cache::remember($cache_key, 60 * 60, function () use ($business_id, $location_id, $start_date, $end_date, $user_id, $permitted_locations) {
                return $this->transactionUtil->getProfitLossDetails($business_id, $location_id, $start_date, $end_date, $user_id, $permitted_locations);
            });

            // $data['closing_stock'] = $data['closing_stock'] - $data['total_sell_return'];

            return view('report.partials.profit_loss_details', compact('data'))->render();
        }

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('report.profit_loss', compact('business_locations'));
    }

    /**
     * Shows product report of a business
     *
     * @return \Illuminate\Http\Response
     */
    public function getPurchaseSell(Request $request)
    {
        if (! auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');

            $location_id = $request->get('location_id');

            // Create a cache key based on the parameters
            $cache_key = "purchase_sell_{$business_id}_{$start_date}_{$end_date}_{$location_id}";

            // Get data from cache or compute it if not cached
            return \Cache::remember($cache_key, 60 * 60, function () use ($business_id, $start_date, $end_date, $location_id) {
                $purchase_details = $this->transactionUtil->getPurchaseTotals($business_id, $start_date, $end_date, $location_id);

                $sell_details = $this->transactionUtil->getSellTotals(
                    $business_id,
                    $start_date,
                    $end_date,
                    $location_id
                );

                $transaction_types = [
                    'purchase_return', 'sell_return',
                ];

                $transaction_totals = $this->transactionUtil->getTransactionTotals(
                    $business_id,
                    $transaction_types,
                    $start_date,
                    $end_date,
                    $location_id
                );

                $total_purchase_return_inc_tax = $transaction_totals['total_purchase_return_inc_tax'];
                $total_sell_return_inc_tax = $transaction_totals['total_sell_return_inc_tax'];

                $difference = [
                    'total' => $sell_details['total_sell_inc_tax'] - $total_sell_return_inc_tax - ($purchase_details['total_purchase_inc_tax'] - $total_purchase_return_inc_tax),
                    'due' => $sell_details['invoice_due'] - $purchase_details['purchase_due'],
                ];

                return ['purchase' => $purchase_details,
                    'sell' => $sell_details,
                    'total_purchase_return' => $total_purchase_return_inc_tax,
                    'total_sell_return' => $total_sell_return_inc_tax,
                    'difference' => $difference,
                ];
            });
        }

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('report.purchase_sell')
                    ->with(compact('business_locations'));
    }

    /**
     * Shows report for Supplier
     *
     * @return \Illuminate\Http\Response
     */
    public function getCustomerSuppliers(Request $request)
    {
        if (! auth()->user()->can('contacts_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $contacts = Contact::where('contacts.business_id', $business_id)
                ->join('transactions AS t', 'contacts.id', '=', 't.contact_id')
                ->active()
                ->groupBy('contacts.id')
                ->select(
                    DB::raw("SUM(IF(t.type = 'purchase', final_total, 0)) as total_purchase"),
                    DB::raw("SUM(IF(t.type = 'purchase_return', final_total, 0)) as total_purchase_return"),
                    DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', final_total, 0)) as total_invoice"),
                    DB::raw("SUM(IF(t.type = 'purchase', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as purchase_paid"),
                    DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as invoice_received"),
                    DB::raw("SUM(IF(t.type = 'sell_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as sell_return_paid"),
                    DB::raw("SUM(IF(t.type = 'purchase_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as purchase_return_received"),
                    DB::raw("SUM(IF(t.type = 'sell_return', final_total, 0)) as total_sell_return"),
                    DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
                    DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid"),
                    DB::raw("SUM(IF(t.type = 'ledger_discount' AND sub_type='sell_discount', final_total, 0)) as total_ledger_discount_sell"),
                    DB::raw("SUM(IF(t.type = 'ledger_discount' AND sub_type='purchase_discount', final_total, 0)) as total_ledger_discount_purchase"),
                    'contacts.supplier_business_name',
                    'contacts.name',
                    'contacts.id',
                    'contacts.type as contact_type'
                );
            $permitted_locations = auth()->user()->permitted_locations();

            if ($permitted_locations != 'all') {
                $contacts->whereIn('t.location_id', $permitted_locations);
            }

            if (! empty($request->input('customer_group_id'))) {
                $contacts->where('contacts.customer_group_id', $request->input('customer_group_id'));
            }

            if (! empty($request->input('location_id'))) {
                $contacts->where('t.location_id', $request->input('location_id'));
            }

            if (! empty($request->input('contact_id'))) {
                $contacts->where('t.contact_id', $request->input('contact_id'));
            }

            if (! empty($request->input('contact_type'))) {
                $contacts->whereIn('contacts.type', [$request->input('contact_type'), 'both']);
            }

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (! empty($start_date) && ! empty($end_date)) {
                $contacts->where('t.transaction_date', '>=', $start_date)
                    ->where('t.transaction_date', '<=', $end_date);
            }

            return Datatables::of($contacts)
                ->editColumn('name', function ($row) {
                    $name = $row->name;
                    if (! empty($row->supplier_business_name)) {
                        $name .= ', '.$row->supplier_business_name;
                    }

                    return '<a href="'.action([\App\Http\Controllers\ContactController::class, 'show'], [$row->id]).'" target="_blank" class="no-print">'.
                            $name.
                        '</a>';
                })
                ->editColumn(
                    'total_purchase',
                    '<span class="total_purchase" data-orig-value="{{$total_purchase}}">@format_currency($total_purchase)</span>'
                )
                ->editColumn(
                    'total_purchase_return',
                    '<span class="total_purchase_return" data-orig-value="{{$total_purchase_return}}">@format_currency($total_purchase_return)</span>'
                )
                ->editColumn(
                    'total_sell_return',
                    '<span class="total_sell_return" data-orig-value="{{$total_sell_return}}">@format_currency($total_sell_return)</span>'
                )
                ->editColumn(
                    'total_invoice',
                    '<span class="total_invoice" data-orig-value="{{$total_invoice}}">@format_currency($total_invoice)</span>'
                )

                ->addColumn('due', function ($row) {
                    $total_ledger_discount_purchase = $row->total_ledger_discount_purchase ?? 0;
                    $total_ledger_discount_sell = $total_ledger_discount_sell ?? 0;
                    $due = ($row->total_invoice - $row->invoice_received - $total_ledger_discount_sell) - ($row->total_purchase - $row->purchase_paid - $total_ledger_discount_purchase) - ($row->total_sell_return - $row->sell_return_paid) + ($row->total_purchase_return - $row->purchase_return_received);

                    if ($row->contact_type == 'supplier') {
                        $due -= $row->opening_balance - $row->opening_balance_paid;
                    } else {
                        $due += $row->opening_balance - $row->opening_balance_paid;
                    }

                    $due_formatted = $this->transactionUtil->num_f($due, true);

                    return '<span class="total_due" data-orig-value="'.$due.'">'.$due_formatted.'</span>';
                })
                ->addColumn(
                    'opening_balance_due',
                    '<span class="opening_balance_due" data-orig-value="{{$opening_balance - $opening_balance_paid}}">@format_currency($opening_balance - $opening_balance_paid)</span>'
                )
                ->removeColumn('supplier_business_name')
                ->removeColumn('invoice_received')
                ->removeColumn('purchase_paid')
                ->removeColumn('id')
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('contacts.name', 'like', "%{$keyword}%")
                        ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['total_purchase', 'total_invoice', 'due', 'name', 'total_purchase_return', 'total_sell_return', 'opening_balance_due'])
                ->make(true);
        }

        $customer_group = CustomerGroup::forDropdown($business_id, false, true);
        $types = [
            '' => __('lang_v1.all'),
            'customer' => __('report.customer'),
            'supplier' => __('report.supplier'),
        ];

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        $contact_dropdown = Contact::contactDropdown($business_id, false, false);

        return view('report.contact')
        ->with(compact('customer_group', 'types', 'business_locations', 'contact_dropdown'));
    }

    /**
     * Shows product stock report
     *
     * @return \Illuminate\Http\Response
     */
    public function getStockReport(Request $request)
    {
        if (! auth()->user()->can('stock_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $selling_price_groups = SellingPriceGroup::where('business_id', $business_id)
                                                ->get();
        $allowed_selling_price_group = false;
        foreach ($selling_price_groups as $selling_price_group) {
            if (auth()->user()->can('selling_price_group.'.$selling_price_group->id)) {
                $allowed_selling_price_group = true;
                break;
            }
        }
        if ($this->moduleUtil->isModuleInstalled('Manufacturing') && (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'manufacturing_module'))) {
            $show_manufacturing_data = 1;
        } else {
            $show_manufacturing_data = 0;
        }
        if ($request->ajax()) {
            $filters = request()->only(['location_id', 'category_id', 'sub_category_id', 'brand_id', 'unit_id', 'tax_id', 'type',
                'only_mfg_products', 'active_state',  'not_for_selling', 'repair_model_id', 'product_id', 'active_state', ]);

            $filters['not_for_selling'] = isset($filters['not_for_selling']) && $filters['not_for_selling'] == 'true' ? 1 : 0;

            $filters['show_manufacturing_data'] = $show_manufacturing_data;

            //Return the details in ajax call
            $for = request()->input('for') == 'view_product' ? 'view_product' : 'datatables';

            // Create a cache key based on the business ID and filters
            $cache_key = "stock_report_{$business_id}_" . md5(json_encode($filters) . $for);

            // Get data from cache or compute it if not cached (30 minute cache)
            $products = \Cache::remember($cache_key, 30 * 60, function () use ($business_id, $filters, $for) {
                return $this->productUtil->getProductStockDetails($business_id, $filters, $for);
            });
            //To show stock details on view product modal
            if ($for == 'view_product' && ! empty(request()->input('product_id'))) {
                $product_stock_details = $products;

                return view('product.partials.product_stock_details')->with(compact('product_stock_details'));
            }

            $datatable = Datatables::of($products)
                ->editColumn('stock', function ($row) {
                    if ($row->enable_stock) {
                        $stock = $row->stock ? $row->stock : 0;

                        return  '<span class="current_stock" data-orig-value="'.(float) $stock.'" data-unit="'.$row->unit.'"> '.$this->transactionUtil->num_f($stock, false, null, true).'</span>'.' '.$row->unit;
                    } else {
                        return '--';
                    }
                })
                ->editColumn('product', function ($row) {
                    $name = $row->product;

                    return $name;
                })
                ->addColumn('action', function ($row) {
                    return '<a class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-info tw-w-max " href="'.action([\App\Http\Controllers\ProductController::class, 'productStockHistory'], [$row->product_id]).
                    '?location_id='.$row->location_id.'&variation_id='.$row->variation_id.
                    '"><i class="fas fa-history"></i> '.__('lang_v1.product_stock_history').'</a>';
                })
                ->addColumn('variation', function ($row) {
                    $variation = '';
                    if ($row->type == 'variable') {
                        $variation .= $row->product_variation.'-'.$row->variation_name;
                    }

                    return $variation;
                })
                ->editColumn('total_sold', function ($row) {
                    $total_sold = 0;
                    if ($row->total_sold) {
                        $total_sold = (float) $row->total_sold;
                    }

                    return '<span data-is_quantity="true" class="total_sold" data-orig-value="'.$total_sold.'" data-unit="'.$row->unit.'" >'.$this->transactionUtil->num_f($total_sold, false, null, true).'</span> '.$row->unit;
                })
                ->editColumn('total_transfered', function ($row) {
                    $total_transfered = 0;
                    if ($row->total_transfered) {
                        $total_transfered = (float) $row->total_transfered;
                    }

                    return '<span class="total_transfered" data-orig-value="'.$total_transfered.'" data-unit="'.$row->unit.'" >'.$this->transactionUtil->num_f($total_transfered, false, null, true).'</span> '.$row->unit;
                })

                ->editColumn('total_adjusted', function ($row) {
                    $total_adjusted = 0;
                    if ($row->total_adjusted) {
                        $total_adjusted = (float) $row->total_adjusted;
                    }

                    return '<span class="total_adjusted" data-orig-value="'.$total_adjusted.'" data-unit="'.$row->unit.'" >'.$this->transactionUtil->num_f($total_adjusted, false, null, true).'</span> '.$row->unit;
                })
                ->editColumn('unit_price', function ($row) use ($allowed_selling_price_group) {
                    $html = '';
                    if (auth()->user()->can('access_default_selling_price')) {
                        $html .= $this->transactionUtil->num_f($row->unit_price, true);
                    }

                    if ($allowed_selling_price_group) {
                        $html .= ' <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-primary tw-w-max btn-modal no-print" data-container=".view_modal" data-href="'.action([\App\Http\Controllers\ProductController::class, 'viewGroupPrice'], [$row->product_id]).'">'.__('lang_v1.view_group_prices').'</button>';
                    }

                    return $html;
                })
                ->editColumn('stock_price', function ($row) {
                    $html = '<span class="total_stock_price" data-orig-value="'
                        .$row->stock_price.'">'.
                        $this->transactionUtil->num_f($row->stock_price, true).'</span>';

                    return $html;
                })
                ->editColumn('stock_value_by_sale_price', function ($row) {
                    $stock = $row->stock ? $row->stock : 0;
                    $unit_selling_price = (float) $row->group_price > 0 ? $row->group_price : $row->unit_price;
                    $stock_price = $stock * $unit_selling_price;

                    return  '<span class="stock_value_by_sale_price" data-orig-value="'.(float) $stock_price.'" > '.$this->transactionUtil->num_f($stock_price, true).'</span>';
                })
                ->addColumn('potential_profit', function ($row) {
                    $stock = $row->stock ? $row->stock : 0;
                    $unit_selling_price = (float) $row->group_price > 0 ? $row->group_price : $row->unit_price;
                    $stock_price_by_sp = $stock * $unit_selling_price;
                    $potential_profit = (float) $stock_price_by_sp - (float) $row->stock_price;

                    return  '<span class="potential_profit" data-orig-value="'.(float) $potential_profit.'" > '.$this->transactionUtil->num_f($potential_profit, true).'</span>';
                })
                ->setRowClass(function ($row) {
                    return $row->enable_stock && $row->stock <= $row->alert_quantity ? 'bg-danger' : '';
                })
                ->filterColumn('variation', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(pv.name, ''), '-', COALESCE(variations.name, '')) like ?", ["%{$keyword}%"]);
                })
                ->removeColumn('enable_stock')
                ->removeColumn('unit')
                ->removeColumn('id');

            $raw_columns = ['unit_price', 'total_transfered', 'total_sold',
                'total_adjusted', 'stock', 'stock_price', 'stock_value_by_sale_price',
                'potential_profit', 'action', ];

            if ($show_manufacturing_data) {
                $datatable->editColumn('total_mfg_stock', function ($row) {
                    $total_mfg_stock = 0;
                    if ($row->total_mfg_stock) {
                        $total_mfg_stock = (float) $row->total_mfg_stock;
                    }

                    return '<span data-is_quantity="true" class="total_mfg_stock"  data-orig-value="'.$total_mfg_stock.'" data-unit="'.$row->unit.'" >'.$this->transactionUtil->num_f($total_mfg_stock, false, null, true).'</span> '.$row->unit;
                });
                $raw_columns[] = 'total_mfg_stock';
            }

            return $datatable->rawColumns($raw_columns)->make(true);
        }

        $categories = Category::forDropdown($business_id, 'product');
        $brands = Brands::forDropdown($business_id);
        $units = Unit::where('business_id', $business_id)
                            ->pluck('short_name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('report.stock_report')
            ->with(compact('categories', 'brands', 'units', 'business_locations', 'show_manufacturing_data'));
    }

    // // this function copy of above get route becouse of large size parameter 
    // public function postStockReport(Request $request){
    //     if ($request->ajax()) {
    //         $filters = request()->only(['location_id', 'category_id', 'sub_category_id', 'brand_id', 'unit_id', 'tax_id', 'type',
    //             'only_mfg_products', 'active_state',  'not_for_selling', 'repair_model_id', 'product_id', 'active_state', ]);

    //         $filters['not_for_selling'] = isset($filters['not_for_selling']) && $filters['not_for_selling'] == 'true' ? 1 : 0;

    //         $filters['show_manufacturing_data'] = $show_manufacturing_data;

    //         //Return the details in ajax call
    //         $for = request()->input('for') == 'view_product' ? 'view_product' : 'datatables';

    //         $products = $this->productUtil->getProductStockDetails($business_id, $filters, $for);
    //         //To show stock details on view product modal
    //         if ($for == 'view_product' && ! empty(request()->input('product_id'))) {
    //             $product_stock_details = $products;

    //             return view('product.partials.product_stock_details')->with(compact('product_stock_details'));
    //         }

    //         $datatable = Datatables::of($products)
    //             ->editColumn('stock', function ($row) {
    //                 if ($row->enable_stock) {
    //                     $stock = $row->stock ? $row->stock : 0;

    //                     return  '<span class="current_stock" data-orig-value="'.(float) $stock.'" data-unit="'.$row->unit.'"> '.$this->transactionUtil->num_f($stock, false, null, true).'</span>'.' '.$row->unit;
    //                 } else {
    //                     return '--';
    //                 }
    //             })
    //             ->editColumn('product', function ($row) {
    //                 $name = $row->product;

    //                 return $name;
    //             })
    //             ->addColumn('action', function ($row) {
    //                 return '<a class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-info" href="'.action([\App\Http\Controllers\ProductController::class, 'productStockHistory'], [$row->product_id]).
    //                 '?location_id='.$row->location_id.'&variation_id='.$row->variation_id.
    //                 '"><i class="fas fa-history"></i> '.__('lang_v1.product_stock_history').'</a>';
    //             })
    //             ->addColumn('variation', function ($row) {
    //                 $variation = '';
    //                 if ($row->type == 'variable') {
    //                     $variation .= $row->product_variation.'-'.$row->variation_name;
    //                 }

    //                 return $variation;
    //             })
    //             ->editColumn('total_sold', function ($row) {
    //                 $total_sold = 0;
    //                 if ($row->total_sold) {
    //                     $total_sold = (float) $row->total_sold;
    //                 }

    //                 return '<span data-is_quantity="true" class="total_sold" data-orig-value="'.$total_sold.'" data-unit="'.$row->unit.'" >'.$this->transactionUtil->num_f($total_sold, false, null, true).'</span> '.$row->unit;
    //             })
    //             ->editColumn('total_transfered', function ($row) {
    //                 $total_transfered = 0;
    //                 if ($row->total_transfered) {
    //                     $total_transfered = (float) $row->total_transfered;
    //                 }

    //                 return '<span class="total_transfered" data-orig-value="'.$total_transfered.'" data-unit="'.$row->unit.'" >'.$this->transactionUtil->num_f($total_transfered, false, null, true).'</span> '.$row->unit;
    //             })

    //             ->editColumn('total_adjusted', function ($row) {
    //                 $total_adjusted = 0;
    //                 if ($row->total_adjusted) {
    //                     $total_adjusted = (float) $row->total_adjusted;
    //                 }

    //                 return '<span class="total_adjusted" data-orig-value="'.$total_adjusted.'" data-unit="'.$row->unit.'" >'.$this->transactionUtil->num_f($total_adjusted, false, null, true).'</span> '.$row->unit;
    //             })
    //             ->editColumn('unit_price', function ($row) use ($allowed_selling_price_group) {
    //                 $html = '';
    //                 if (auth()->user()->can('access_default_selling_price')) {
    //                     $html .= $this->transactionUtil->num_f($row->unit_price, true);
    //                 }

    //                 if ($allowed_selling_price_group) {
    //                     $html .= ' <button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-primary btn-modal no-print" data-container=".view_modal" data-href="'.action([\App\Http\Controllers\ProductController::class, 'viewGroupPrice'], [$row->product_id]).'">'.__('lang_v1.view_group_prices').'</button>';
    //                 }

    //                 return $html;
    //             })
    //             ->editColumn('stock_price', function ($row) {
    //                 $html = '<span class="total_stock_price" data-orig-value="'
    //                     .$row->stock_price.'">'.
    //                     $this->transactionUtil->num_f($row->stock_price, true).'</span>';

    //                 return $html;
    //             })
    //             ->editColumn('stock_value_by_sale_price', function ($row) {
    //                 $stock = $row->stock ? $row->stock : 0;
    //                 $unit_selling_price = (float) $row->group_price > 0 ? $row->group_price : $row->unit_price;
    //                 $stock_price = $stock * $unit_selling_price;

    //                 return  '<span class="stock_value_by_sale_price" data-orig-value="'.(float) $stock_price.'" > '.$this->transactionUtil->num_f($stock_price, true).'</span>';
    //             })
    //             ->addColumn('potential_profit', function ($row) {
    //                 $stock = $row->stock ? $row->stock : 0;
    //                 $unit_selling_price = (float) $row->group_price > 0 ? $row->group_price : $row->unit_price;
    //                 $stock_price_by_sp = $stock * $unit_selling_price;
    //                 $potential_profit = (float) $stock_price_by_sp - (float) $row->stock_price;

    //                 return  '<span class="potential_profit" data-orig-value="'.(float) $potential_profit.'" > '.$this->transactionUtil->num_f($potential_profit, true).'</span>';
    //             })
    //             ->setRowClass(function ($row) {
    //                 return $row->enable_stock && $row->stock <= $row->alert_quantity ? 'bg-danger' : '';
    //             })
    //             ->filterColumn('variation', function ($query, $keyword) {
    //                 $query->whereRaw("CONCAT(COALESCE(pv.name, ''), '-', COALESCE(variations.name, '')) like ?", ["%{$keyword}%"]);
    //             })
    //             ->removeColumn('enable_stock')
    //             ->removeColumn('unit')
    //             ->removeColumn('id');

    //         $raw_columns = ['unit_price', 'total_transfered', 'total_sold',
    //             'total_adjusted', 'stock', 'stock_price', 'stock_value_by_sale_price',
    //             'potential_profit', 'action', ];

    //         if ($show_manufacturing_data) {
    //             $datatable->editColumn('total_mfg_stock', function ($row) {
    //                 $total_mfg_stock = 0;
    //                 if ($row->total_mfg_stock) {
    //                     $total_mfg_stock = (float) $row->total_mfg_stock;
    //                 }

    //                 return '<span data-is_quantity="true" class="total_mfg_stock"  data-orig-value="'.$total_mfg_stock.'" data-unit="'.$row->unit.'" >'.$this->transactionUtil->num_f($total_mfg_stock, false, null, true).'</span> '.$row->unit;
    //             });
    //             $raw_columns[] = 'total_mfg_stock';
    //         }

    //         return $datatable->rawColumns($raw_columns)->make(true);
    //     }
    // }

    /**
     * Shows product stock details
     *
     * @return \Illuminate\Http\Response
     */
    public function getStockDetails(Request $request)
    {
        //Return the details in ajax call
        if ($request->ajax()) {
            $business_id = $request->session()->get('user.business_id');
            $product_id = $request->input('product_id');
            $query = Product::leftjoin('units as u', 'products.unit_id', '=', 'u.id')
                ->join('variations as v', 'products.id', '=', 'v.product_id')
                ->join('product_variations as pv', 'pv.id', '=', 'v.product_variation_id')
                ->leftjoin('variation_location_details as vld', 'v.id', '=', 'vld.variation_id')
                ->where('products.business_id', $business_id)
                ->where('products.id', $product_id)
                ->whereNull('v.deleted_at');

            $permitted_locations = auth()->user()->permitted_locations();
            $location_filter = '';
            if ($permitted_locations != 'all') {
                $query->whereIn('vld.location_id', $permitted_locations);
                $locations_imploded = implode(', ', $permitted_locations);
                $location_filter .= "AND transactions.location_id IN ($locations_imploded) ";
            }

            if (! empty($request->input('location_id'))) {
                $location_id = $request->input('location_id');

                $query->where('vld.location_id', $location_id);

                $location_filter .= "AND transactions.location_id=$location_id";
            }

            $product_details = $query->select(
                'products.name as product',
                'u.short_name as unit',
                'pv.name as product_variation',
                'v.name as variation',
                'v.sub_sku as sub_sku',
                'v.sell_price_inc_tax',
                DB::raw('SUM(vld.qty_available) as stock'),
                DB::raw("(SELECT SUM(IF(transactions.type='sell', TSL.quantity - TSL.quantity_returned, -1* TPL.quantity) ) FROM transactions 
                        LEFT JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id

                        LEFT JOIN purchase_lines AS TPL ON transactions.id=TPL.transaction_id

                        WHERE transactions.status='final' AND transactions.type='sell' $location_filter 
                        AND (TSL.variation_id=v.id OR TPL.variation_id=v.id)) as total_sold"),
                DB::raw("(SELECT SUM(IF(transactions.type='sell_transfer', TSL.quantity, 0) ) FROM transactions 
                        LEFT JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id
                        WHERE transactions.status='final' AND transactions.type='sell_transfer' $location_filter 
                        AND (TSL.variation_id=v.id)) as total_transfered"),
                DB::raw("(SELECT SUM(IF(transactions.type='stock_adjustment', SAL.quantity, 0) ) FROM transactions 
                        LEFT JOIN stock_adjustment_lines AS SAL ON transactions.id=SAL.transaction_id
                        WHERE transactions.status='received' AND transactions.type='stock_adjustment' $location_filter 
                        AND (SAL.variation_id=v.id)) as total_adjusted")
                // DB::raw("(SELECT SUM(quantity) FROM transaction_sell_lines LEFT JOIN transactions ON transaction_sell_lines.transaction_id=transactions.id WHERE transactions.status='final' $location_filter AND
                //     transaction_sell_lines.variation_id=v.id) as total_sold")
            )
                        ->groupBy('v.id')
                        ->get();

            return view('report.stock_details')
                        ->with(compact('product_details'));
        }
    }

    /**
     * Shows tax report of a business
     *
     * @return \Illuminate\Http\Response
     */
    public function getTaxDetails(Request $request)
    {
        if (! auth()->user()->can('tax_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $business_id = $request->session()->get('user.business_id');
            $taxes = TaxRate::forBusiness($business_id);
            $type = $request->input('type');

            $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);

            $sells = Transaction::leftJoin('tax_rates as tr', 'transactions.tax_id', '=', 'tr.id')
                            ->leftJoin('contacts as c', 'transactions.contact_id', '=', 'c.id')
                ->where('transactions.business_id', $business_id)
                ->with(['payment_lines'])
                ->select('c.name as contact_name',
                        'c.supplier_business_name',
                        'c.tax_number',
                        'transactions.ref_no',
                        'transactions.invoice_no',
                        'transactions.transaction_date',
                        'transactions.total_before_tax',
                        'transactions.tax_id',
                        'transactions.tax_amount',
                        'transactions.id',
                        'transactions.type',
                        'transactions.discount_type',
                        'transactions.discount_amount'
                    );
            if ($type == 'sell') {
                $sells->where('transactions.type', 'sell')
                    ->where('transactions.status', 'final')
                    ->where(function ($query) {
                        $query->whereHas('sell_lines', function ($q) {
                            $q->whereNotNull('transaction_sell_lines.tax_id');
                        })->orWhereNotNull('transactions.tax_id');
                    })
                    ->with(['sell_lines' => function ($q) {
                        $q->whereNotNull('transaction_sell_lines.tax_id');
                    }, 'sell_lines.line_tax']);
            }
            if ($type == 'purchase') {
                $sells->where('transactions.type', 'purchase')
                    ->where('transactions.status', 'received')
                    ->where(function ($query) {
                        $query->whereHas('purchase_lines', function ($q) {
                            $q->whereNotNull('purchase_lines.tax_id');
                        })->orWhereNotNull('transactions.tax_id');
                    })
                    ->with(['purchase_lines' => function ($q) {
                        $q->whereNotNull('purchase_lines.tax_id');
                    }, 'purchase_lines.line_tax']);
            }

            if ($type == 'expense') {
                $sells->where('transactions.type', 'expense')
                        ->whereNotNull('transactions.tax_id');
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }

            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (! empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }

            if (request()->has('contact_id')) {
                $contact_id = request()->get('contact_id');
                if (! empty($contact_id)) {
                    $sells->where('transactions.contact_id', $contact_id);
                }
            }

            if (! empty(request()->start_date) && ! empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $sells->whereDate('transactions.transaction_date', '>=', $start)
                                ->whereDate('transactions.transaction_date', '<=', $end);
            }
            $datatable = Datatables::of($sells);
            $raw_cols = ['total_before_tax', 'discount_amount', 'contact_name', 'payment_methods'];
            $group_taxes_array = TaxRate::groupTaxes($business_id);
            $group_taxes = [];
            foreach ($group_taxes_array as $group_tax) {
                foreach ($group_tax['sub_taxes'] as $sub_tax) {
                    $group_taxes[$group_tax->id]['sub_taxes'][$sub_tax->id] = $sub_tax;
                }
            }
            foreach ($taxes as $tax) {
                $col = 'tax_'.$tax['id'];
                $raw_cols[] = $col;
                $datatable->addColumn($col, function ($row) use ($tax, $type, $col, $group_taxes) {
                    $tax_amount = 0;
                    if ($type == 'sell') {
                        foreach ($row->sell_lines as $sell_line) {
                            if ($sell_line->tax_id == $tax['id']) {
                                $tax_amount += ($sell_line->item_tax * ($sell_line->quantity - $sell_line->quantity_returned));
                            }

                            //break group tax
                            if ($sell_line->line_tax->is_tax_group == 1 && array_key_exists($tax['id'], $group_taxes[$sell_line->tax_id]['sub_taxes'])) {
                                $group_tax_details = $this->transactionUtil->groupTaxDetails($sell_line->line_tax, $sell_line->item_tax);

                                $sub_tax_share = 0;
                                foreach ($group_tax_details as $sub_tax_details) {
                                    if ($sub_tax_details['id'] == $tax['id']) {
                                        $sub_tax_share = $sub_tax_details['calculated_tax'];
                                    }
                                }

                                $tax_amount += ($sub_tax_share * ($sell_line->quantity - $sell_line->quantity_returned));
                            }
                        }
                    } elseif ($type == 'purchase') {
                        foreach ($row->purchase_lines as $purchase_line) {
                            if ($purchase_line->tax_id == $tax['id']) {
                                $tax_amount += ($purchase_line->item_tax * ($purchase_line->quantity - $purchase_line->quantity_returned));
                            }

                            //break group tax
                            if ($purchase_line->line_tax->is_tax_group == 1 && array_key_exists($tax['id'], $group_taxes[$purchase_line->tax_id]['sub_taxes'])) {
                                $group_tax_details = $this->transactionUtil->groupTaxDetails($purchase_line->line_tax, $purchase_line->item_tax);

                                $sub_tax_share = 0;
                                foreach ($group_tax_details as $sub_tax_details) {
                                    if ($sub_tax_details['id'] == $tax['id']) {
                                        $sub_tax_share = $sub_tax_details['calculated_tax'];
                                    }
                                }

                                $tax_amount += ($sub_tax_share * ($purchase_line->quantity - $purchase_line->quantity_returned));
                            }
                        }
                    }
                    if ($row->tax_id == $tax['id']) {
                        $tax_amount += $row->tax_amount;
                    }

                    //break group tax
                    if (! empty($group_taxes[$row->tax_id]) && array_key_exists($tax['id'], $group_taxes[$row->tax_id]['sub_taxes'])) {
                        $group_tax_details = $this->transactionUtil->groupTaxDetails($row->tax_id, $row->tax_amount);

                        $sub_tax_share = 0;
                        foreach ($group_tax_details as $sub_tax_details) {
                            if ($sub_tax_details['id'] == $tax['id']) {
                                $sub_tax_share = $sub_tax_details['calculated_tax'];
                            }
                        }

                        $tax_amount += $sub_tax_share;
                    }

                    if ($tax_amount > 0) {
                        return '<span class="display_currency '.$col.'" data-currency_symbol="true" data-orig-value="'.$tax_amount.'">'.$tax_amount.'</span>';
                    } else {
                        return '';
                    }
                });
            }

            $datatable->editColumn(
                    'total_before_tax',
                    function ($row) {
                        return '<span class="total_before_tax" 
                        data-orig-value="'.$row->total_before_tax.'">'.
                        $this->transactionUtil->num_f($row->total_before_tax, true).'</span>';
                    }
                )->editColumn('discount_amount', function ($row) {
                    $d = '';
                    if ($row->discount_amount !== 0) {
                        $symbol = $row->discount_type != 'percentage';
                        $d .= $this->transactionUtil->num_f($row->discount_amount, $symbol);

                        if ($row->discount_type == 'percentage') {
                            $d .= '%';
                        }
                    }

                    return $d;
                })
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn('contact_name', '@if(!empty($supplier_business_name)) {{$supplier_business_name}},<br>@endif {{$contact_name}}')
                ->addColumn('payment_methods', function ($row) use ($payment_types) {
                    $methods = array_unique($row->payment_lines->pluck('method')->toArray());
                    $count = count($methods);
                    $payment_method = '';
                    if ($count == 1) {
                        $payment_method = $payment_types[$methods[0]];
                    } elseif ($count > 1) {
                        $payment_method = __('lang_v1.checkout_multi_pay');
                    }

                    $html = ! empty($payment_method) ? '<span class="payment-method" data-orig-value="'.$payment_method.'" data-status-name="'.$payment_method.'">'.$payment_method.'</span>' : '';

                    return $html;
                });

            return $datatable->rawColumns($raw_cols)
                            ->make(true);
        }
    }

    /**
     * Shows tax report of a business
     *
     * @return \Illuminate\Http\Response
     */
    public function getTaxReport(Request $request)
    {
        if (! auth()->user()->can('tax_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $location_id = $request->get('location_id');
            $contact_id = $request->get('contact_id');

            $input_tax_details = $this->transactionUtil->getInputTax($business_id, $start_date, $end_date, $location_id, $contact_id);

            $output_tax_details = $this->transactionUtil->getOutputTax($business_id, $start_date, $end_date, $location_id, $contact_id);

            $expense_tax_details = $this->transactionUtil->getExpenseTax($business_id, $start_date, $end_date, $location_id, $contact_id);

            $module_output_taxes = $this->moduleUtil->getModuleData('getModuleOutputTax', ['start_date' => $start_date, 'end_date' => $end_date]);

            $total_module_output_tax = 0;
            foreach ($module_output_taxes as $key => $module_output_tax) {
                $total_module_output_tax += $module_output_tax;
            }

            $total_output_tax = $output_tax_details['total_tax'] + $total_module_output_tax;

            $tax_diff = $total_output_tax - $input_tax_details['total_tax'] - $expense_tax_details['total_tax'];

            return [
                'tax_diff' => $tax_diff,
            ];
        }

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        $taxes = TaxRate::forBusiness($business_id);

        $tax_report_tabs = $this->moduleUtil->getModuleData('getTaxReportViewTabs');

        $contact_dropdown = Contact::contactDropdown($business_id, false, false);

        return view('report.tax_report')
            ->with(compact('business_locations', 'taxes', 'tax_report_tabs', 'contact_dropdown'));
    }

    /**
     * Shows trending products
     *
     * @return \Illuminate\Http\Response
     */
    public function getTrendingProducts(Request $request)
    {
        if (! auth()->user()->can('trending_product_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $filters = request()->only(['category', 'sub_category', 'brand', 'unit', 'limit', 'location_id', 'product_type']);

        $date_range = request()->input('date_range');

        if (! empty($date_range)) {
            $date_range_array = explode('~', $date_range);
            $filters['start_date'] = $this->transactionUtil->uf_date(trim($date_range_array[0]));
            $filters['end_date'] = $this->transactionUtil->uf_date(trim($date_range_array[1]));
        }

        $products = $this->productUtil->getTrendingProducts($business_id, $filters);

        $values = [];
        $labels = [];
        foreach ($products as $product) {
            $values[] = (float) $product->total_unit_sold;
            $labels[] = $product->product.' - '.$product->sku.' ('.$product->unit.')';
        }

        $chart = new CommonChart;
        $chart->labels($labels)
            ->dataset(__('report.total_unit_sold'), 'column', $values);

        $categories = Category::forDropdown($business_id, 'product');
        $brands = Brands::forDropdown($business_id);
        $units = Unit::where('business_id', $business_id)
                            ->pluck('short_name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('report.trending_products')
                    ->with(compact('chart', 'categories', 'brands', 'units', 'business_locations'));
    }

    public function getTrendingProductsAjax()
    {
        $business_id = request()->session()->get('user.business_id');
    }

    /**
     * Shows expense report of a business
     *
     * @return \Illuminate\Http\Response
     */
    public function getExpenseReport(Request $request)
    {
        if (! auth()->user()->can('expense_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $filters = $request->only(['category', 'location_id']);

        $date_range = $request->input('date_range');

        if (! empty($date_range)) {
            $date_range_array = explode('~', $date_range);
            $filters['start_date'] = $this->transactionUtil->uf_date(trim($date_range_array[0]));
            $filters['end_date'] = $this->transactionUtil->uf_date(trim($date_range_array[1]));
        } else {
            $filters['start_date'] = \Carbon::now()->startOfMonth()->format('Y-m-d');
            $filters['end_date'] = \Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        $expenses = $this->transactionUtil->getExpenseReport($business_id, $filters);

        $values = [];
        $labels = [];
        foreach ($expenses as $expense) {
            $values[] = (float) $expense->total_expense;
            $labels[] = ! empty($expense->category) ? $expense->category : __('report.others');
        }

        $chart = new CommonChart;
        $chart->labels($labels)
            ->title(__('report.expense_report'))
            ->dataset(__('report.total_expense'), 'column', $values);

        $categories = ExpenseCategory::where('business_id', $business_id)
                            ->pluck('name', 'id');

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('report.expense_report')
                    ->with(compact('chart', 'categories', 'business_locations', 'expenses'));
    }

    /**
     * Shows stock adjustment report
     *
     * @return \Illuminate\Http\Response
     */
    public function getStockAdjustmentReport(Request $request)
    {
        if (! auth()->user()->can('stock_adjustment_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $query = Transaction::where('business_id', $business_id)
                            ->where('type', 'stock_adjustment');

            //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('location_id', $permitted_locations);
            }

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (! empty($start_date) && ! empty($end_date)) {
                $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            }
            $location_id = $request->get('location_id');
            if (! empty($location_id)) {
                $query->where('location_id', $location_id);
            }

            $stock_adjustment_details = $query->select(
                DB::raw('SUM(final_total) as total_amount'),
                DB::raw('SUM(total_amount_recovered) as total_recovered'),
                DB::raw("SUM(IF(adjustment_type = 'normal', final_total, 0)) as total_normal"),
                DB::raw("SUM(IF(adjustment_type = 'abnormal', final_total, 0)) as total_abnormal")
            )->first();

            return $stock_adjustment_details;
        }
        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('report.stock_adjustment_report')
                    ->with(compact('business_locations'));
    }

    /**
     * Shows register report of a business
     *
     * @return \Illuminate\Http\Response
     */
    public function getRegisterReport(Request $request)
    {
        if (! auth()->user()->can('register_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {

        $start_date = request()->input('start_date');
        $end_date = request()->input('end_date');
        $user_id = request()->input('user_id');

        $permitted_locations = auth()->user()->permitted_locations();

            $registers = $this->transactionUtil->registerReport($business_id, $permitted_locations, $start_date, $end_date, $user_id);

            return Datatables::of($registers)
                ->editColumn('total_card_payment', function ($row) {
                    return '<span data-orig-value="'.$row->total_card_payment.'" >'.$this->transactionUtil->num_f($row->total_card_payment, true).' ('.$row->total_card_slips.')</span>';
                })
                ->editColumn('total_cheque_payment', function ($row) {
                    return '<span data-orig-value="'.$row->total_cheque_payment.'" >'.$this->transactionUtil->num_f($row->total_cheque_payment, true).' ('.$row->total_cheques.')</span>';
                })
                ->editColumn('total_cash_payment', function ($row) {
                    return '<span data-orig-value="'.$row->total_cash_payment.'" >'.$this->transactionUtil->num_f($row->total_cash_payment, true).'</span>';
                })
                ->editColumn('total_bank_transfer_payment', function ($row) {
                    return '<span data-orig-value="'.$row->total_bank_transfer_payment.'" >'.$this->transactionUtil->num_f($row->total_bank_transfer_payment, true).'</span>';
                })
                ->editColumn('total_other_payment', function ($row) {
                    return '<span data-orig-value="'.$row->total_other_payment.'" >'.$this->transactionUtil->num_f($row->total_other_payment, true).'</span>';
                })
                ->editColumn('total_advance_payment', function ($row) {
                    return '<span data-orig-value="'.$row->total_advance_payment.'" >'.$this->transactionUtil->num_f($row->total_advance_payment, true).'</span>';
                })
                ->editColumn('total_custom_pay_1', function ($row) {
                    return '<span data-orig-value="'.$row->total_custom_pay_1.'" >'.$this->transactionUtil->num_f($row->total_custom_pay_1, true).'</span>';
                })
                ->editColumn('total_custom_pay_2', function ($row) {
                    return '<span data-orig-value="'.$row->total_custom_pay_2.'" >'.$this->transactionUtil->num_f($row->total_custom_pay_2, true).'</span>';
                })
                ->editColumn('total_custom_pay_3', function ($row) {
                    return '<span data-orig-value="'.$row->total_custom_pay_3.'" >'.$this->transactionUtil->num_f($row->total_custom_pay_3, true).'</span>';
                })
                ->editColumn('total_custom_pay_4', function ($row) {
                    return '<span data-orig-value="'.$row->total_custom_pay_4.'" >'.$this->transactionUtil->num_f($row->total_custom_pay_4, true).'</span>';
                })
                ->editColumn('total_custom_pay_5', function ($row) {
                    return '<span data-orig-value="'.$row->total_custom_pay_5.'" >'.$this->transactionUtil->num_f($row->total_custom_pay_5, true).'</span>';
                })
                ->editColumn('total_custom_pay_6', function ($row) {
                    return '<span data-orig-value="'.$row->total_custom_pay_6.'" >'.$this->transactionUtil->num_f($row->total_custom_pay_6, true).'</span>';
                })
                ->editColumn('total_custom_pay_7', function ($row) {
                    return '<span data-orig-value="'.$row->total_custom_pay_7.'" >'.$this->transactionUtil->num_f($row->total_custom_pay_7, true).'</span>';
                })
                ->editColumn('closed_at', function ($row) {
                    if ($row->status == 'close') {
                        return $this->productUtil->format_date($row->closed_at, true);
                    } else {
                        return '';
                    }
                })
                ->editColumn('created_at', function ($row) {
                    return $this->productUtil->format_date($row->created_at, true);
                })
                ->addColumn('total', function ($row) {
                    $total = $row->total_card_payment + $row->total_cheque_payment + $row->total_cash_payment + $row->total_bank_transfer_payment + $row->total_other_payment + $row->total_advance_payment + $row->total_custom_pay_1 + $row->total_custom_pay_2 + $row->total_custom_pay_3 + $row->total_custom_pay_4 + $row->total_custom_pay_5 + $row->total_custom_pay_6 + $row->total_custom_pay_7;

                    return '<span data-orig-value="'.$total.'" >'.$this->transactionUtil->num_f($total, true).'</span>';
                })
                ->addColumn('action', '<button type="button" data-href="{{action(\'App\Http\Controllers\CashRegisterController@show\', [$id])}}" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-info tw-w-max btn-modal" 
                    data-container=".view_register"><i class="fas fa-eye" aria-hidden="true"></i> @lang("messages.view")</button> @if($status != "close" && auth()->user()->can("close_cash_register"))<button type="button" data-href="{{action(\'App\Http\Controllers\CashRegisterController@getCloseRegister\', [$id])}}" class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-error tw-w-max btn-modal" 
                        data-container=".view_register"><i class="fas fa-window-close"></i> @lang("messages.close")</button> @endif')
                ->filterColumn('user_name', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, ''), '<br>', COALESCE(u.email, '')) like ?", ["%{$keyword}%"]);
                })
                ->rawColumns(['action', 'user_name', 'total_card_payment', 'total_cheque_payment', 'total_cash_payment', 'total_bank_transfer_payment', 'total_other_payment', 'total_advance_payment', 'total_custom_pay_1', 'total_custom_pay_2', 'total_custom_pay_3', 'total_custom_pay_4', 'total_custom_pay_5', 'total_custom_pay_6', 'total_custom_pay_7', 'total'])
                ->make(true);
        }

        $users = User::forDropdown($business_id, false);
        $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);

        return view('report.register_report')
                    ->with(compact('users', 'payment_types'));
    }

    /**
     * Shows sales representative report
     *
     * @return \Illuminate\Http\Response
     */
    public function getSalesRepresentativeReport(Request $request)
    {
        if (! auth()->user()->can('sales_representative.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $users = User::allUsersDropdown($business_id, false);
        $business_locations = BusinessLocation::forDropdown($business_id, true);

        $business_details = $this->businessUtil->getDetails($business_id);
        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

        return view('report.sales_representative')
                ->with(compact('users', 'business_locations', 'pos_settings'));
    }

    /**
     * Shows sales representative total expense
     *
     * @return json
     */
    public function getSalesRepresentativeTotalExpense(Request $request)
    {
        if (! auth()->user()->can('sales_representative.view')) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $business_id = $request->session()->get('user.business_id');

            $filters = $request->only(['expense_for', 'location_id', 'start_date', 'end_date']);

            $total_expense = $this->transactionUtil->getExpenseReport($business_id, $filters, 'total');

            return $total_expense;
        }
    }

    /**
     * Shows sales representative total sales
     *
     * @return json
     */
    public function getSalesRepresentativeTotalSell(Request $request)
    {
        if (! auth()->user()->can('sales_representative.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');

            $location_id = $request->get('location_id');
            $created_by = $request->get('created_by');

            $sell_details = $this->transactionUtil->getSellTotals($business_id, $start_date, $end_date, $location_id, $created_by);

            //Get Sell Return details
            $transaction_types = [
                'sell_return',
            ];
            $sell_return_details = $this->transactionUtil->getTransactionTotals(
                $business_id,
                $transaction_types,
                $start_date,
                $end_date,
                $location_id,
                $created_by
            );

            $total_sell_return = ! empty($sell_return_details['total_sell_return_exc_tax']) ? $sell_return_details['total_sell_return_exc_tax'] : 0;
            $total_sell = $sell_details['total_sell_exc_tax'] - $total_sell_return;

            return [
                'total_sell_exc_tax' => $sell_details['total_sell_exc_tax'],
                'total_sell_return_exc_tax' => $total_sell_return,
                'total_sell' => $total_sell,
            ];
        }
    }

    /**
     * Shows sales representative total commission
     *
     * @return json
     */
    public function getSalesRepresentativeTotalCommission(Request $request)
    {
        if (! auth()->user()->can('sales_representative.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');

            $location_id = $request->get('location_id');
            $commission_agent = $request->get('commission_agent');

            $business_details = $this->businessUtil->getDetails($business_id);
            $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

            $commsn_calculation_type = empty($pos_settings['cmmsn_calculation_type']) || $pos_settings['cmmsn_calculation_type'] == 'invoice_value' ? 'invoice_value' : $pos_settings['cmmsn_calculation_type'];

            $commission_percentage = User::find($commission_agent)->cmmsn_percent;

            if ($commsn_calculation_type == 'payment_received') {
                $payment_details = $this->transactionUtil->getTotalPaymentWithCommission($business_id, $start_date, $end_date, $location_id, $commission_agent);

                //Get Commision
                $total_commission = $commission_percentage * $payment_details['total_payment_with_commission'] / 100;

                return ['total_payment_with_commission' => $payment_details['total_payment_with_commission'] ?? 0,
                    'total_commission' => $total_commission,
                    'commission_percentage' => $commission_percentage,
                ];
            }

            $sell_details = $this->transactionUtil->getTotalSellCommission($business_id, $start_date, $end_date, $location_id, $commission_agent);

            //Get Commision
            $total_commission = $commission_percentage * $sell_details['total_sales_with_commission'] / 100;

            return ['total_sales_with_commission' => $sell_details['total_sales_with_commission'],
                'total_commission' => $total_commission,
                'commission_percentage' => $commission_percentage,
            ];
        }
    }

    /**
     * Shows product stock expiry report
     *
     * @return \Illuminate\Http\Response
     */
    public function getStockExpiryReport(Request $request)
    {
        if (! auth()->user()->can('stock_expiry_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //TODO:: Need to display reference number and edit expiry date button

        //Return the details in ajax call
        if ($request->ajax()) {
            $query = PurchaseLine::leftjoin(
                'transactions as t',
                'purchase_lines.transaction_id',
                '=',
                't.id'
            )
                            ->leftjoin(
                                'products as p',
                                'purchase_lines.product_id',
                                '=',
                                'p.id'
                            )
                            ->leftjoin(
                                'variations as v',
                                'purchase_lines.variation_id',
                                '=',
                                'v.id'
                            )
                            ->leftjoin(
                                'product_variations as pv',
                                'v.product_variation_id',
                                '=',
                                'pv.id'
                            )
                            ->leftjoin('business_locations as l', 't.location_id', '=', 'l.id')
                            ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                            ->where('t.business_id', $business_id)
                            //->whereNotNull('p.expiry_period')
                            //->whereNotNull('p.expiry_period_type')
                            //->whereNotNull('exp_date')
                            ->where('p.enable_stock', 1);
            // ->whereRaw('purchase_lines.quantity > purchase_lines.quantity_sold + quantity_adjusted + quantity_returned');

            $permitted_locations = auth()->user()->permitted_locations();

            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            if (! empty($request->input('location_id'))) {
                $location_id = $request->input('location_id');
                $query->where('t.location_id', $location_id)
                        //If filter by location then hide products not available in that location
                        ->join('product_locations as pl', 'pl.product_id', '=', 'p.id')
                        ->where(function ($q) use ($location_id) {
                            $q->where('pl.location_id', $location_id);
                        });
            }

            if (! empty($request->input('category_id'))) {
                $query->where('p.category_id', $request->input('category_id'));
            }
            if (! empty($request->input('sub_category_id'))) {
                $query->where('p.sub_category_id', $request->input('sub_category_id'));
            }
            if (! empty($request->input('brand_id'))) {
                $query->where('p.brand_id', $request->input('brand_id'));
            }
            if (! empty($request->input('unit_id'))) {
                $query->where('p.unit_id', $request->input('unit_id'));
            }
            if (! empty($request->input('exp_date_filter'))) {
                $query->whereDate('exp_date', '<=', $request->input('exp_date_filter'));
            }

            $only_mfg_products = request()->get('only_mfg_products', 0);
            if (! empty($only_mfg_products)) {
                $query->where('t.type', 'production_purchase');
            }

            $report = $query->select(
                'p.name as product',
                'p.sku',
                'p.type as product_type',
                'v.name as variation',
                'v.sub_sku',
                'pv.name as product_variation',
                'l.name as location',
                'mfg_date',
                'exp_date',
                'u.short_name as unit',
                DB::raw('SUM(COALESCE(quantity, 0) - COALESCE(quantity_sold, 0) - COALESCE(quantity_adjusted, 0) - COALESCE(quantity_returned, 0)) as stock_left'),
                't.ref_no',
                't.id as transaction_id',
                'purchase_lines.id as purchase_line_id',
                'purchase_lines.lot_number'
            )
            ->having('stock_left', '>', 0)
            ->groupBy('purchase_lines.variation_id')
            ->groupBy('purchase_lines.exp_date')
            ->groupBy('purchase_lines.lot_number');

            return Datatables::of($report)
                ->editColumn('product', function ($row) {
                    if ($row->product_type == 'variable') {
                        return $row->product.' - '.
                        $row->product_variation.' - '.$row->variation.' ('.$row->sub_sku.')';
                    } else {
                        return $row->product.' ('.$row->sku.')';
                    }
                })
                ->editColumn('mfg_date', function ($row) {
                    if (! empty($row->mfg_date)) {
                        return $this->productUtil->format_date($row->mfg_date);
                    } else {
                        return '--';
                    }
                })
                // ->editColumn('exp_date', function ($row) {
                //     if (!empty($row->exp_date)) {
                //         $carbon_exp = \Carbon::createFromFormat('Y-m-d', $row->exp_date);
                //         $carbon_now = \Carbon::now();
                //         if ($carbon_now->diffInDays($carbon_exp, false) >= 0) {
                //             return $this->productUtil->format_date($row->exp_date) . '<br><small>( <span class="time-to-now">' . $row->exp_date . '</span> )</small>';
                //         } else {
                //             return $this->productUtil->format_date($row->exp_date) . ' &nbsp; <span class="label label-danger no-print">' . __('report.expired') . '</span><span class="print_section">' . __('report.expired') . '</span><br><small>( <span class="time-from-now">' . $row->exp_date . '</span> )</small>';
                //         }
                //     } else {
                //         return '--';
                //     }
                // })
                ->editColumn('ref_no', function ($row) {
                    return '<button type="button" data-href="'.action([\App\Http\Controllers\PurchaseController::class, 'show'], [$row->transaction_id])
                            .'" class="btn btn-link btn-modal" data-container=".view_modal"  >'.$row->ref_no.'</button>';
                })
                ->editColumn('stock_left', function ($row) {
                    return '<span data-is_quantity="true" class="display_currency stock_left" data-currency_symbol=false data-orig-value="'.$row->stock_left.'" data-unit="'.$row->unit.'" >'.$row->stock_left.'</span> '.$row->unit;
                })
                ->addColumn('edit', function ($row) {
                    $html = '<button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-primary stock_expiry_edit_btn" data-transaction_id="'.$row->transaction_id.'" data-purchase_line_id="'.$row->purchase_line_id.'"> <i class="fa fa-edit"></i> '.__('messages.edit').
                    '</button>';

                    if (! empty($row->exp_date)) {
                        $carbon_exp = \Carbon::createFromFormat('Y-m-d', $row->exp_date);
                        $carbon_now = \Carbon::now();
                        if ($carbon_now->diffInDays($carbon_exp, false) < 0) {
                            $html .= ' <button type="button" class="btn btn-warning btn-xs remove_from_stock_btn" data-href="'.action([\App\Http\Controllers\StockAdjustmentController::class, 'removeExpiredStock'], [$row->purchase_line_id]).'"> <i class="fa fa-trash"></i> '.__('lang_v1.remove_from_stock').
                            '</button>';
                        }
                    }

                    return $html;
                })
                ->rawColumns(['exp_date', 'ref_no', 'edit', 'stock_left'])
                ->make(true);
        }

        $categories = Category::forDropdown($business_id, 'product');
        $brands = Brands::forDropdown($business_id);
        $units = Unit::where('business_id', $business_id)
                            ->pluck('short_name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        $view_stock_filter = [
            \Carbon::now()->subDay()->format('Y-m-d') => __('report.expired'),
            \Carbon::now()->addWeek()->format('Y-m-d') => __('report.expiring_in_1_week'),
            \Carbon::now()->addDays(15)->format('Y-m-d') => __('report.expiring_in_15_days'),
            \Carbon::now()->addMonth()->format('Y-m-d') => __('report.expiring_in_1_month'),
            \Carbon::now()->addMonths(3)->format('Y-m-d') => __('report.expiring_in_3_months'),
            \Carbon::now()->addMonths(6)->format('Y-m-d') => __('report.expiring_in_6_months'),
            \Carbon::now()->addYear()->format('Y-m-d') => __('report.expiring_in_1_year'),
        ];

        return view('report.stock_expiry_report')
                ->with(compact('categories', 'brands', 'units', 'business_locations', 'view_stock_filter'));
    }

    /**
     * Shows product stock expiry report
     *
     * @return \Illuminate\Http\Response
     */
    public function getStockExpiryReportEditModal(Request $request, $purchase_line_id)
    {
        if (! auth()->user()->can('stock_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $purchase_line = PurchaseLine::join(
                'transactions as t',
                'purchase_lines.transaction_id',
                '=',
                't.id'
            )
                                ->join(
                                    'products as p',
                                    'purchase_lines.product_id',
                                    '=',
                                    'p.id'
                                )
                                ->where('purchase_lines.id', $purchase_line_id)
                                ->where('t.business_id', $business_id)
                                ->select(['purchase_lines.*', 'p.name', 't.ref_no'])
                                ->first();

            if (! empty($purchase_line)) {
                if (! empty($purchase_line->exp_date)) {
                    $purchase_line->exp_date = date('m/d/Y', strtotime($purchase_line->exp_date));
                }
            }

            return view('report.partials.stock_expiry_edit_modal')
                ->with(compact('purchase_line'));
        }
    }

    /**
     * Update product stock expiry report
     *
     * @return \Illuminate\Http\Response
     */
    public function updateStockExpiryReport(Request $request)
    {
        if (! auth()->user()->can('stock_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');

            //Return the details in ajax call
            if ($request->ajax()) {
                DB::beginTransaction();

                $input = $request->only(['purchase_line_id', 'exp_date']);

                $purchase_line = PurchaseLine::join(
                    'transactions as t',
                    'purchase_lines.transaction_id',
                    '=',
                    't.id'
                )
                                    ->join(
                                        'products as p',
                                        'purchase_lines.product_id',
                                        '=',
                                        'p.id'
                                    )
                                    ->where('purchase_lines.id', $input['purchase_line_id'])
                                    ->where('t.business_id', $business_id)
                                    ->select(['purchase_lines.*', 'p.name', 't.ref_no'])
                                    ->first();

                if (! empty($purchase_line) && ! empty($input['exp_date'])) {
                    $purchase_line->exp_date = $this->productUtil->uf_date($input['exp_date']);
                    $purchase_line->save();
                }

                DB::commit();

                $output = ['success' => 1,
                    'msg' => __('lang_v1.updated_succesfully'),
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Shows product stock expiry report
     *
     * @return \Illuminate\Http\Response
     */
    public function getCustomerGroup(Request $request)
    {
        if (! auth()->user()->can('customer_group_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $query = Transaction::leftjoin('customer_groups AS CG', 'transactions.customer_group_id', '=', 'CG.id')
                        ->where('transactions.business_id', $business_id)
                        ->where('transactions.type', 'sell')
                        ->where('transactions.status', 'final')
                        ->groupBy('transactions.customer_group_id')
                        ->select(DB::raw('SUM(final_total) as total_sell'), 'CG.name');

            $group_id = $request->get('customer_group_id', null);
            if (! empty($group_id)) {
                $query->where('transactions.customer_group_id', $group_id);
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('transactions.location_id', $permitted_locations);
            }

            $location_id = $request->get('location_id', null);
            if (! empty($location_id)) {
                $query->where('transactions.location_id', $location_id);
            }

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');

            if (! empty($start_date) && ! empty($end_date)) {
                $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            }

            return Datatables::of($query)
                ->editColumn('total_sell', function ($row) {
                    return '<span class="display_currency" data-currency_symbol = true>'.$row->total_sell.'</span>';
                })
                ->rawColumns(['total_sell'])
                ->make(true);
        }

        $customer_group = CustomerGroup::forDropdown($business_id, false, true);
        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('report.customer_group')
            ->with(compact('customer_group', 'business_locations'));
    }

    /**
     * Shows product purchase report
     *
     * @return \Illuminate\Http\Response
     */
    public function getproductPurchaseReport(Request $request)
    {
        if (! auth()->user()->can('product_purchase_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        if ($request->ajax()) {
            $variation_id = $request->get('variation_id', null);
            $query = PurchaseLine::join(
                'transactions as t',
                'purchase_lines.transaction_id',
                '=',
                't.id'
                    )
                    ->join(
                        'variations as v',
                        'purchase_lines.variation_id',
                        '=',
                        'v.id'
                    )
                    ->join('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
                    ->join('contacts as c', 't.contact_id', '=', 'c.id')
                    ->join('products as p', 'pv.product_id', '=', 'p.id')
                    ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                    ->where('t.business_id', $business_id)
                    ->where('t.type', 'purchase')
                    ->select(
                        'p.name as product_name',
                        'p.type as product_type',
                        'pv.name as product_variation',
                        'v.name as variation_name',
                        'v.sub_sku',
                        'c.name as supplier',
                        'c.supplier_business_name',
                        't.id as transaction_id',
                        't.ref_no',
                        't.transaction_date as transaction_date',
                        'purchase_lines.purchase_price_inc_tax as unit_purchase_price',
                        DB::raw('(purchase_lines.quantity - purchase_lines.quantity_returned) as purchase_qty'),
                        'purchase_lines.quantity_adjusted',
                        'u.short_name as unit',
                        DB::raw('((purchase_lines.quantity - purchase_lines.quantity_returned - purchase_lines.quantity_adjusted) * purchase_lines.purchase_price_inc_tax) as subtotal')
                    )
                    ->groupBy('purchase_lines.id');
            if (! empty($variation_id)) {
                $query->where('purchase_lines.variation_id', $variation_id);
            }
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (! empty($start_date) && ! empty($end_date)) {
                $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            $location_id = $request->get('location_id', null);
            if (! empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }

            $supplier_id = $request->get('supplier_id', null);
            if (! empty($supplier_id)) {
                $query->where('t.contact_id', $supplier_id);
            }

            $brand_id = $request->get('brand_id', null);
            if (! empty($brand_id)) {
                $query->where('p.brand_id', $brand_id);
            }

            return Datatables::of($query)
                ->editColumn('product_name', function ($row) {
                    $product_name = $row->product_name;
                    if ($row->product_type == 'variable') {
                        $product_name .= ' - '.$row->product_variation.' - '.$row->variation_name;
                    }

                    return $product_name;
                })
                 ->editColumn('ref_no', function ($row) {
                     return '<a data-href="'.action([\App\Http\Controllers\PurchaseController::class, 'show'], [$row->transaction_id])
                            .'" href="#" data-container=".view_modal" class="btn-modal">'.$row->ref_no.'</a>';
                 })
                 ->editColumn('purchase_qty', function ($row) {
                     return '<span data-is_quantity="true" class="display_currency purchase_qty" data-currency_symbol=false data-orig-value="'.(float) $row->purchase_qty.'" data-unit="'.$row->unit.'" >'.(float) $row->purchase_qty.'</span> '.$row->unit;
                 })
                 ->editColumn('quantity_adjusted', function ($row) {
                     return '<span data-is_quantity="true" class="display_currency quantity_adjusted" data-currency_symbol=false data-orig-value="'.(float) $row->quantity_adjusted.'" data-unit="'.$row->unit.'" >'.(float) $row->quantity_adjusted.'</span> '.$row->unit;
                 })
                 ->editColumn('subtotal', function ($row) {
                     return '<span class="row_subtotal"  
                     data-orig-value="'.$row->subtotal.'">'.
                     $this->transactionUtil->num_f($row->subtotal, true).'</span>';
                 })
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('unit_purchase_price', function ($row) {
                    return $this->transactionUtil->num_f($row->unit_purchase_price, true);
                })
                ->editColumn('supplier', '@if(!empty($supplier_business_name)) {{$supplier_business_name}},<br>@endif {{$supplier}}')
                ->rawColumns(['ref_no', 'unit_purchase_price', 'subtotal', 'purchase_qty', 'quantity_adjusted', 'supplier'])
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $suppliers = Contact::suppliersDropdown($business_id);
        $brands = Brands::forDropdown($business_id);

        return view('report.product_purchase_report')
            ->with(compact('business_locations', 'suppliers', 'brands'));
    }

    /**
     * Shows product purchase report
     *
     * @return \Illuminate\Http\Response
     */
    public function getproductSellReport(Request $request)
    {
        if (! auth()->user()->can('product_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $custom_labels = json_decode(session('business.custom_labels'), true);

        $product_custom_field1 = !empty($custom_labels['product']['custom_field_1']) ? $custom_labels['product']['custom_field_1'] : '';
        $product_custom_field2 = !empty($custom_labels['product']['custom_field_2']) ? $custom_labels['product']['custom_field_2'] : '';

        if ($request->ajax()) {
            $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);

            $variation_id = $request->get('variation_id', null);
            $query = TransactionSellLine::join(
                'transactions as t',
                'transaction_sell_lines.transaction_id',
                '=',
                't.id'
            )
                ->join(
                    'variations as v',
                    'transaction_sell_lines.variation_id',
                    '=',
                    'v.id'
                )
                ->join('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
                ->join('contacts as c', 't.contact_id', '=', 'c.id')
                ->join('products as p', 'pv.product_id', '=', 'p.id')
                ->leftjoin('tax_rates', 'transaction_sell_lines.tax_id', '=', 'tax_rates.id')
                ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final')
                ->with('transaction.payment_lines')
                ->whereNull('parent_sell_line_id')
                ->select(
                    'p.name as product_name',
                    'p.type as product_type',
                    'p.product_custom_field1 as product_custom_field1',
                    'p.product_custom_field2 as product_custom_field2',
                    'pv.name as product_variation',
                    'v.name as variation_name',
                    'v.sub_sku',
                    'c.name as customer',
                    'c.mobile as contact_no',
                    'c.supplier_business_name',
                    'c.contact_id',
                    't.id as transaction_id',
                    't.invoice_no',
                    't.transaction_date as transaction_date',
                    'transaction_sell_lines.unit_price_before_discount as unit_price',
                    'transaction_sell_lines.unit_price_inc_tax as unit_sale_price',
                    DB::raw('(transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) as sell_qty'),
                    'transaction_sell_lines.line_discount_type as discount_type',
                    'transaction_sell_lines.line_discount_amount as discount_amount',
                    'transaction_sell_lines.item_tax',
                    'tax_rates.name as tax',
                    'u.short_name as unit',
                    'transaction_sell_lines.parent_sell_line_id',
                    DB::raw('((transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) * transaction_sell_lines.unit_price_inc_tax) as subtotal')
                )
                ->groupBy('transaction_sell_lines.id');

            if (! empty($variation_id)) {
                $query->where('transaction_sell_lines.variation_id', $variation_id);
            }
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (! empty($start_date) && ! empty($end_date)) {
                $query->where('t.transaction_date', '>=', $start_date)
                    ->where('t.transaction_date', '<=', $end_date);
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            $location_id = $request->get('location_id', null);
            if (! empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }

            $customer_id = $request->get('customer_id', null);
            if (! empty($customer_id)) {
                $query->where('t.contact_id', $customer_id);
            }

            $customer_group_id = $request->get('customer_group_id', null);
            if (! empty($customer_group_id)) {
                $query->leftjoin('customer_groups AS CG', 'c.customer_group_id', '=', 'CG.id')
                ->where('CG.id', $customer_group_id);
            }

            $category_id = $request->get('category_id', null);
            if (! empty($category_id)) {
                $query->where('p.category_id', $category_id);
            }

            $brand_id = $request->get('brand_id', null);
            if (! empty($brand_id)) {
                $query->where('p.brand_id', $brand_id);
            }

            return Datatables::of($query)
                ->editColumn('product_name', function ($row) {
                    $product_name = $row->product_name;
                    if ($row->product_type == 'variable') {
                        $product_name .= ' - '.$row->product_variation.' - '.$row->variation_name;
                    }

                    return $product_name;
                })
                 ->editColumn('invoice_no', function ($row) {
                     return '<a data-href="'.action([\App\Http\Controllers\SellController::class, 'show'], [$row->transaction_id])
                            .'" href="#" data-container=".view_modal" class="btn-modal">'.$row->invoice_no.'</a>';
                 })
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn('unit_sale_price', function ($row) {
                    return '<span class="unit_sale_price" data-orig-value="'.$row->unit_sale_price.'">'.
                    $this->transactionUtil->num_f($row->unit_sale_price, true).'</span>';
                })
                ->editColumn('sell_qty', function ($row) {
                    //ignore child sell line of combo product
                    $class = is_null($row->parent_sell_line_id) ? 'sell_qty' : '';

                    return '<span class="'.$class.'"  data-orig-value="'.$row->sell_qty.'" 
                    data-unit="'.$row->unit.'" >'.
                    $this->transactionUtil->num_f($row->sell_qty, false, null, true).'</span> '.$row->unit;
                })
                 ->editColumn('subtotal', function ($row) {
                     //ignore child sell line of combo product
                     $class = is_null($row->parent_sell_line_id) ? 'row_subtotal' : '';

                     return '<span class="'.$class.'"  data-orig-value="'.$row->subtotal.'">'.
                    $this->transactionUtil->num_f($row->subtotal, true).'</span>';
                 })
                ->editColumn('unit_price', function ($row) {
                    return '<span class="unit_price" data-orig-value="'.$row->unit_price.'">'.
                    $this->transactionUtil->num_f($row->unit_price, true).'</span>';
                })
                ->editColumn('discount_amount', '
                    @if($discount_type == "percentage")
                        {{@num_format($discount_amount)}} %
                    @elseif($discount_type == "fixed")
                        {{@num_format($discount_amount)}}
                    @endif
                    ')
                ->editColumn('tax', function ($row) {
                    return $this->transactionUtil->num_f($row->item_tax, true)
                     .'<br>'.'<span data-orig-value="'.$row->item_tax.'" 
                     class="tax" data-unit="'.$row->tax.'"><small>('.$row->tax.')</small></span>';
                })
                ->addColumn('payment_methods', function ($row) use ($payment_types) {
                    $methods = array_unique($row->transaction->payment_lines->pluck('method')->toArray());
                    $count = count($methods);
                    $payment_method = '';
                    if ($count == 1) {
                        $payment_method = $payment_types[$methods[0]] ?? '';
                    } elseif ($count > 1) {
                        $payment_method = __('lang_v1.checkout_multi_pay');
                    }

                    $html = ! empty($payment_method) ? '<span class="payment-method" data-orig-value="'.$payment_method.'" data-status-name="'.$payment_method.'">'.$payment_method.'</span>' : '';

                    return $html;
                })
                ->editColumn('customer', '@if(!empty($supplier_business_name)) {{$supplier_business_name}},<br>@endif {{$customer}}')
                ->rawColumns(['invoice_no', 'unit_sale_price', 'subtotal', 'sell_qty', 'discount_amount', 'unit_price', 'tax', 'customer', 'payment_methods'])
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $customers = Contact::customersDropdown($business_id);
        $categories = Category::forDropdown($business_id, 'product');
        $brands = Brands::forDropdown($business_id);
        $customer_group = CustomerGroup::forDropdown($business_id, false, true);

        return view('report.product_sell_report')
            ->with(compact('business_locations', 'customers', 'categories', 'brands',
                'customer_group', 'product_custom_field1', 'product_custom_field2'));
    }

    /**
     * Shows product purchase report with purchase details
     *
     * @return \Illuminate\Http\Response
     */
    public function getproductSellReportWithPurchase(Request $request)
    {
        if (! auth()->user()->can('product_sell_report_with_purchase.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        if ($request->ajax()) {
            $variation_id = $request->get('variation_id', null);
            $query = TransactionSellLine::join(
                'transactions as t',
                'transaction_sell_lines.transaction_id',
                '=',
                't.id'
            )
                ->join(
                    'transaction_sell_lines_purchase_lines as tspl',
                    'transaction_sell_lines.id',
                    '=',
                    'tspl.sell_line_id'
                )
                ->join(
                    'purchase_lines as pl',
                    'tspl.purchase_line_id',
                    '=',
                    'pl.id'
                )
                ->join(
                    'transactions as purchase',
                    'pl.transaction_id',
                    '=',
                    'purchase.id'
                )
                ->leftjoin('contacts as supplier', 'purchase.contact_id', '=', 'supplier.id')
                ->join(
                    'variations as v',
                    'transaction_sell_lines.variation_id',
                    '=',
                    'v.id'
                )
                ->join('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
                ->leftjoin('contacts as c', 't.contact_id', '=', 'c.id')
                ->join('products as p', 'pv.product_id', '=', 'p.id')
                ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final')
                ->select(
                    'p.name as product_name',
                    'p.type as product_type',
                    'pv.name as product_variation',
                    'v.name as variation_name',
                    'v.sub_sku',
                    'c.name as customer',
                    'c.mobile as contact_no',
                    'c.supplier_business_name',
                    't.id as transaction_id',
                    't.invoice_no',
                    't.transaction_date as transaction_date',
                    'tspl.quantity as purchase_quantity',
                    'u.short_name as unit',
                    'supplier.name as supplier_name',
                    'purchase.ref_no as ref_no',
                    'purchase.type as purchase_type',
                    'pl.lot_number'
                );

            if (! empty($variation_id)) {
                $query->where('transaction_sell_lines.variation_id', $variation_id);
            }
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (! empty($start_date) && ! empty($end_date)) {
                $query->where('t.transaction_date', '>=', $start_date)
                    ->where('t.transaction_date', '<=', $end_date);
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            $location_id = $request->get('location_id', null);
            if (! empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }

            $customer_id = $request->get('customer_id', null);
            if (! empty($customer_id)) {
                $query->where('t.contact_id', $customer_id);
            }
            $customer_group_id = $request->get('customer_group_id', null);
            if (! empty($customer_group_id)) {
                $query->leftjoin('customer_groups AS CG', 'c.customer_group_id', '=', 'CG.id')
                ->where('CG.id', $customer_group_id);
            }

            $category_id = $request->get('category_id', null);
            if (! empty($category_id)) {
                $query->where('p.category_id', $category_id);
            }

            $brand_id = $request->get('brand_id', null);
            if (! empty($brand_id)) {
                $query->where('p.brand_id', $brand_id);
            }

            return Datatables::of($query)
                ->editColumn('product_name', function ($row) {
                    $product_name = $row->product_name;
                    if ($row->product_type == 'variable') {
                        $product_name .= ' - '.$row->product_variation.' - '.$row->variation_name;
                    }

                    return $product_name;
                })
                 ->editColumn('invoice_no', function ($row) {
                     return '<a data-href="'.action([\App\Http\Controllers\SellController::class, 'show'], [$row->transaction_id])
                            .'" href="#" data-container=".view_modal" class="btn-modal">'.$row->invoice_no.'</a>';
                 })
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn('unit_sale_price', function ($row) {
                    return '<span class="display_currency" data-currency_symbol = true>'.$row->unit_sale_price.'</span>';
                })
                ->editColumn('purchase_quantity', function ($row) {
                    return '<span data-is_quantity="true" class="display_currency purchase_quantity" data-currency_symbol=false data-orig-value="'.(float) $row->purchase_quantity.'" data-unit="'.$row->unit.'" >'.(float) $row->purchase_quantity.'</span> '.$row->unit;
                })
                ->editColumn('ref_no', '
                    @if($purchase_type == "opening_stock")
                        <i><small class="help-block">(@lang("lang_v1.opening_stock"))</small></i>
                    @else
                        {{$ref_no}}
                    @endif
                    ')
                ->editColumn('customer', '@if(!empty($supplier_business_name)) {{$supplier_business_name}},<br>@endif {{$customer}}')
                ->rawColumns(['invoice_no', 'purchase_quantity', 'ref_no', 'customer'])
                ->make(true);
        }
    }

    /**
     * Shows product lot report
     *
     * @return \Illuminate\Http\Response
     */
    public function getLotReport(Request $request)
    {
        if (! auth()->user()->can('lot_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $query = Product::where('products.business_id', $business_id)
                    ->leftjoin('units', 'products.unit_id', '=', 'units.id')
                    ->join('variations as v', 'products.id', '=', 'v.product_id')
                    ->join('purchase_lines as pl', 'v.id', '=', 'pl.variation_id')
                    ->leftjoin(
                        'transaction_sell_lines_purchase_lines as tspl',
                        'pl.id',
                        '=',
                        'tspl.purchase_line_id'
                    )
                    ->join('transactions as t', 'pl.transaction_id', '=', 't.id');

            $permitted_locations = auth()->user()->permitted_locations();
            $location_filter = 'WHERE ';

            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);

                $locations_imploded = implode(', ', $permitted_locations);
                $location_filter = " LEFT JOIN transactions as t2 on pls.transaction_id=t2.id WHERE t2.location_id IN ($locations_imploded) AND ";
            }

            if (! empty($request->input('location_id'))) {
                $location_id = $request->input('location_id');
                $query->where('t.location_id', $location_id)
                    //If filter by location then hide products not available in that location
                    ->ForLocation($location_id);

                $location_filter = "LEFT JOIN transactions as t2 on pls.transaction_id=t2.id WHERE t2.location_id=$location_id AND ";
            }

            if (! empty($request->input('category_id'))) {
                $query->where('products.category_id', $request->input('category_id'));
            }

            if (! empty($request->input('sub_category_id'))) {
                $query->where('products.sub_category_id', $request->input('sub_category_id'));
            }

            if (! empty($request->input('brand_id'))) {
                $query->where('products.brand_id', $request->input('brand_id'));
            }

            if (! empty($request->input('unit_id'))) {
                $query->where('products.unit_id', $request->input('unit_id'));
            }

            $only_mfg_products = request()->get('only_mfg_products', 0);
            if (! empty($only_mfg_products)) {
                $query->where('t.type', 'production_purchase');
            }

            $products = $query->select(
                'products.name as product',
                'v.name as variation_name',
                'sub_sku',
                'pl.lot_number',
                'pl.exp_date as exp_date',
                DB::raw("( COALESCE((SELECT SUM(quantity - quantity_returned) from purchase_lines as pls $location_filter variation_id = v.id AND lot_number = pl.lot_number), 0) - 
                    SUM(COALESCE((tspl.quantity - tspl.qty_returned), 0))) as stock"),
                // DB::raw("(SELECT SUM(IF(transactions.type='sell', TSL.quantity, -1* TPL.quantity) ) FROM transactions
                //         LEFT JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id

                //         LEFT JOIN purchase_lines AS TPL ON transactions.id=TPL.transaction_id

                //         WHERE transactions.status='final' AND transactions.type IN ('sell', 'sell_return') $location_filter
                //         AND (TSL.product_id=products.id OR TPL.product_id=products.id)) as total_sold"),

                DB::raw('COALESCE(SUM(IF(tspl.sell_line_id IS NULL, 0, (tspl.quantity - tspl.qty_returned)) ), 0) as total_sold'),
                DB::raw('COALESCE(SUM(IF(tspl.stock_adjustment_line_id IS NULL, 0, tspl.quantity ) ), 0) as total_adjusted'),
                'products.type',
                'units.short_name as unit'
            )
            ->whereNotNull('pl.lot_number')
            ->groupBy('v.id')
            ->groupBy('pl.lot_number');

            return Datatables::of($products)
                ->editColumn('stock', function ($row) {
                    $stock = $row->stock ? $row->stock : 0;

                    return '<span data-is_quantity="true" class="display_currency total_stock" data-currency_symbol=false data-orig-value="'.(float) $stock.'" data-unit="'.$row->unit.'" >'.(float) $stock.'</span> '.$row->unit;
                })
                ->editColumn('product', function ($row) {
                    if ($row->variation_name != 'DUMMY') {
                        return $row->product.' ('.$row->variation_name.')';
                    } else {
                        return $row->product;
                    }
                })
                ->editColumn('total_sold', function ($row) {
                    if ($row->total_sold) {
                        return '<span data-is_quantity="true" class="display_currency total_sold" data-currency_symbol=false data-orig-value="'.(float) $row->total_sold.'" data-unit="'.$row->unit.'" >'.(float) $row->total_sold.'</span> '.$row->unit;
                    } else {
                        return '0'.' '.$row->unit;
                    }
                })
                ->editColumn('total_adjusted', function ($row) {
                    if ($row->total_adjusted) {
                        return '<span data-is_quantity="true" class="display_currency total_adjusted" data-currency_symbol=false data-orig-value="'.(float) $row->total_adjusted.'" data-unit="'.$row->unit.'" >'.(float) $row->total_adjusted.'</span> '.$row->unit;
                    } else {
                        return '0'.' '.$row->unit;
                    }
                })
                ->editColumn('exp_date', function ($row) {
                    if (! empty($row->exp_date)) {
                        $carbon_exp = \Carbon::createFromFormat('Y-m-d', $row->exp_date);
                        $carbon_now = \Carbon::now();
                        if ($carbon_now->diffInDays($carbon_exp, false) >= 0) {
                            return $this->productUtil->format_date($row->exp_date).'<br><small>( <span class="time-to-now">'.$row->exp_date.'</span> )</small>';
                        } else {
                            return $this->productUtil->format_date($row->exp_date).' &nbsp; <span class="label label-danger no-print">'.__('report.expired').'</span><span class="print_section">'.__('report.expired').'</span><br><small>( <span class="time-from-now">'.$row->exp_date.'</span> )</small>';
                        }
                    } else {
                        return '--';
                    }
                })
                ->removeColumn('unit')
                ->removeColumn('id')
                ->removeColumn('variation_name')
                ->rawColumns(['exp_date', 'stock', 'total_sold', 'total_adjusted'])
                ->make(true);
        }

        $categories = Category::forDropdown($business_id, 'product');
        $brands = Brands::forDropdown($business_id);
        $units = Unit::where('business_id', $business_id)
                            ->pluck('short_name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('report.lot_report')
            ->with(compact('categories', 'brands', 'units', 'business_locations'));
    }

    /**
     * Shows purchase payment report
     *
     * @return \Illuminate\Http\Response
     */
    public function purchasePaymentReport(Request $request)
    {
        if (! auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        if ($request->ajax()) {
            $supplier_id = $request->get('supplier_id', null);
            $contact_filter1 = ! empty($supplier_id) ? "AND t.contact_id=$supplier_id" : '';
            $contact_filter2 = ! empty($supplier_id) ? "AND transactions.contact_id=$supplier_id" : '';

            $location_id = $request->get('location_id', null);

            $parent_payment_query_part = empty($location_id) ? 'AND transaction_payments.parent_id IS NULL' : '';

            $query = TransactionPayment::leftjoin('transactions as t', function ($join) use ($business_id) {
                $join->on('transaction_payments.transaction_id', '=', 't.id')
                    ->where('t.business_id', $business_id)
                    ->whereIn('t.type', ['purchase', 'opening_balance']);
            })
                ->where('transaction_payments.business_id', $business_id)
                ->where(function ($q) use ($business_id, $contact_filter1, $contact_filter2, $parent_payment_query_part) {
                    $q->whereRaw("(transaction_payments.transaction_id IS NOT NULL AND t.type IN ('purchase', 'opening_balance')  $parent_payment_query_part $contact_filter1)")
                        ->orWhereRaw("EXISTS(SELECT * FROM transaction_payments as tp JOIN transactions ON tp.transaction_id = transactions.id WHERE transactions.type IN ('purchase', 'opening_balance') AND transactions.business_id = $business_id AND tp.parent_id=transaction_payments.id $contact_filter2)");
                })

                ->select(
                    DB::raw("IF(transaction_payments.transaction_id IS NULL, 
                                (SELECT c.name FROM transactions as ts
                                JOIN contacts as c ON ts.contact_id=c.id 
                                WHERE ts.id=(
                                        SELECT tps.transaction_id FROM transaction_payments as tps
                                        WHERE tps.parent_id=transaction_payments.id LIMIT 1
                                    )
                                ),
                                (SELECT CONCAT(COALESCE(c.supplier_business_name, ''), '<br>', c.name) FROM transactions as ts JOIN
                                    contacts as c ON ts.contact_id=c.id
                                    WHERE ts.id=t.id 
                                )
                            ) as supplier"),
                    'transaction_payments.amount',
                    'method',
                    'paid_on',
                    'transaction_payments.payment_ref_no',
                    'transaction_payments.document',
                    't.ref_no',
                    't.id as transaction_id',
                    'cheque_number',
                    'card_transaction_number',
                    'bank_account_number',
                    'transaction_no',
                    'transaction_payments.id as DT_RowId'
                )
                ->groupBy('transaction_payments.id');

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (! empty($start_date) && ! empty($end_date)) {
                $query->whereBetween(DB::raw('date(paid_on)'), [$start_date, $end_date]);
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            if (! empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }

            $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);

            return Datatables::of($query)
                 ->editColumn('ref_no', function ($row) {
                     if (! empty($row->ref_no)) {
                         return '<a data-href="'.action([\App\Http\Controllers\PurchaseController::class, 'show'], [$row->transaction_id])
                            .'" href="#" data-container=".view_modal" class="btn-modal">'.$row->ref_no.'</a>';
                     } else {
                         return '';
                     }
                 })
                ->editColumn('paid_on', '{{@format_datetime($paid_on)}}')
                ->editColumn('method', function ($row) use ($payment_types) {
                    $method = ! empty($payment_types[$row->method]) ? $payment_types[$row->method] : '';
                    if ($row->method == 'cheque') {
                        $method .= '<br>('.__('lang_v1.cheque_no').': '.$row->cheque_number.')';
                    } elseif ($row->method == 'card') {
                        $method .= '<br>('.__('lang_v1.card_transaction_no').': '.$row->card_transaction_number.')';
                    } elseif ($row->method == 'bank_transfer') {
                        $method .= '<br>('.__('lang_v1.bank_account_no').': '.$row->bank_account_number.')';
                    } elseif ($row->method == 'custom_pay_1') {
                        $method .= '<br>('.__('lang_v1.transaction_no').': '.$row->transaction_no.')';
                    } elseif ($row->method == 'custom_pay_2') {
                        $method .= '<br>('.__('lang_v1.transaction_no').': '.$row->transaction_no.')';
                    } elseif ($row->method == 'custom_pay_3') {
                        $method .= '<br>('.__('lang_v1.transaction_no').': '.$row->transaction_no.')';
                    }

                    return $method;
                })
                ->editColumn('amount', function ($row) {
                    return '<span class="paid-amount" data-orig-value="'.$row->amount.'">'.
                    $this->transactionUtil->num_f($row->amount, true).'</span>';
                })
                ->addColumn('action', '<button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-primary view_payment" data-href="{{ action([\App\Http\Controllers\TransactionPaymentController::class, \'viewPayment\'], [$DT_RowId]) }}">@lang("messages.view")
                    </button> @if(!empty($document))<a href="{{asset("/uploads/documents/" . $document)}}" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-accent" download=""><i class="fa fa-download"></i> @lang("purchase.download_document")</a>@endif')
                ->rawColumns(['ref_no', 'amount', 'method', 'action', 'supplier'])
                ->make(true);
        }
        $business_locations = BusinessLocation::forDropdown($business_id);
        $suppliers = Contact::suppliersDropdown($business_id, false);

        return view('report.purchase_payment_report')
            ->with(compact('business_locations', 'suppliers'));
    }

    /**
     * Shows sell payment report
     *
     * @return \Illuminate\Http\Response
     */
    public function sellPaymentReport(Request $request)
    {
        if (! auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);
        if ($request->ajax()) {
            $customer_id = $request->get('supplier_id', null);
            $contact_filter1 = ! empty($customer_id) ? "AND t.contact_id=$customer_id" : '';
            $contact_filter2 = ! empty($customer_id) ? "AND transactions.contact_id=$customer_id" : '';

            $location_id = $request->get('location_id', null);
            $parent_payment_query_part = empty($location_id) ? 'AND transaction_payments.parent_id IS NULL' : '';

            $query = TransactionPayment::leftjoin('transactions as t', function ($join) use ($business_id) {
                $join->on('transaction_payments.transaction_id', '=', 't.id')
                    ->where('t.business_id', $business_id)
                    ->whereIn('t.type', ['sell', 'opening_balance']);
            })
                ->leftjoin('contacts as c', 't.contact_id', '=', 'c.id')
                ->leftjoin('customer_groups AS CG', 'c.customer_group_id', '=', 'CG.id')


            //     DB::raw("IF(transaction_payments.transaction_id IS NULL, 
            //     (SELECT c.name FROM transactions as ts
            //     JOIN contacts as c ON ts.contact_id=c.id 
            //     WHERE ts.id=(
            //             SELECT tps.transaction_id FROM transaction_payments as tps
            //             WHERE tps.parent_id=transaction_payments.id LIMIT 1
            //         )
            //     ),
            //     (SELECT CONCAT(COALESCE(CONCAT(c.supplier_business_name, '<br>'), ''), c.name) FROM transactions as ts JOIN
            //         contacts as c ON ts.contact_id=c.id
            //         WHERE ts.id=t.id 
            //     )
            // ) as customer")
            // remove above line from select and below join becouse customer search not work

                ->leftJoin(DB::raw("(
                    SELECT 
                        tp.id as payment_id, 
                        IF(tp.transaction_id IS NULL, 
                            (SELECT c.name 
                             FROM transactions as ts
                             JOIN contacts as c ON ts.contact_id = c.id 
                             WHERE ts.id = (
                                SELECT tps.transaction_id 
                                FROM transaction_payments as tps 
                                WHERE tps.parent_id = tp.id 
                                LIMIT 1
                             )
                            ), 
                            CONCAT(COALESCE(CONCAT(c.supplier_business_name, '<br>'), ''), c.name)
                        ) as customer_name
                    FROM transaction_payments tp
                    LEFT JOIN transactions t ON tp.transaction_id = t.id
                    LEFT JOIN contacts c ON t.contact_id = c.id
                ) as customer_subquery"), 'transaction_payments.id', '=', 'customer_subquery.payment_id')              
                ->where('transaction_payments.business_id', $business_id)
                ->where(function ($q) use ($business_id, $contact_filter1, $contact_filter2, $parent_payment_query_part) {
                    $q->whereRaw("(transaction_payments.transaction_id IS NOT NULL AND t.type IN ('sell', 'opening_balance') $parent_payment_query_part $contact_filter1)")
                        ->orWhereRaw("EXISTS(SELECT * FROM transaction_payments as tp JOIN transactions ON tp.transaction_id = transactions.id WHERE transactions.type IN ('sell', 'opening_balance') AND transactions.business_id = $business_id AND tp.parent_id=transaction_payments.id $contact_filter2)");
                })
                ->select(
                    'customer_subquery.customer_name as customer',
                    'transaction_payments.amount',
                    'transaction_payments.is_return',
                    'method',
                    'paid_on',
                    'transaction_payments.payment_ref_no',
                    'transaction_payments.document',
                    'transaction_payments.transaction_no',
                    't.invoice_no',
                    'c.contact_id',
                    't.id as transaction_id',
                    'cheque_number',
                    'card_transaction_number',
                    'bank_account_number',
                    'transaction_payments.id as DT_RowId',
                    'CG.name as customer_group'
                )
                ->groupBy('transaction_payments.id');

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (! empty($start_date) && ! empty($end_date)) {
                $query->whereBetween(DB::raw('date(paid_on)'), [$start_date, $end_date]);
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            if (! empty($request->get('customer_group_id'))) {
                $query->where('CG.id', $request->get('customer_group_id'));
            }

            if (! empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }
            if (! empty($request->has('commission_agent'))) {
                $query->where('t.commission_agent', $request->get('commission_agent'));
            }

            if (! empty($request->get('payment_types'))) {
                $query->where('transaction_payments.method', $request->get('payment_types'));
            }

            return Datatables::of($query)
                 ->editColumn('invoice_no', function ($row) {
                     if (! empty($row->transaction_id)) {
                         return '<a data-href="'.action([\App\Http\Controllers\SellController::class, 'show'], [$row->transaction_id])
                            .'" href="#" data-container=".view_modal" class="btn-modal">'.$row->invoice_no.'</a>';
                     } else {
                         return '';
                     }
                 })
                ->editColumn('paid_on', '{{@format_datetime($paid_on)}}')
                ->editColumn('method', function ($row) use ($payment_types) {
                    $method = ! empty($payment_types[$row->method]) ? $payment_types[$row->method] : '';
                    if ($row->method == 'cheque') {
                        $method .= '<br>('.__('lang_v1.cheque_no').': '.$row->cheque_number.')';
                    } elseif ($row->method == 'card') {
                        $method .= '<br>('.__('lang_v1.card_transaction_no').': '.$row->card_transaction_number.')';
                    } elseif ($row->method == 'bank_transfer') {
                        $method .= '<br>('.__('lang_v1.bank_account_no').': '.$row->bank_account_number.')';
                    } elseif ($row->method == 'custom_pay_1') {
                        $method .= '<br>('.__('lang_v1.transaction_no').': '.$row->transaction_no.')';
                    } elseif ($row->method == 'custom_pay_2') {
                        $method .= '<br>('.__('lang_v1.transaction_no').': '.$row->transaction_no.')';
                    } elseif ($row->method == 'custom_pay_3') {
                        $method .= '<br>('.__('lang_v1.transaction_no').': '.$row->transaction_no.')';
                    }
                    if ($row->is_return == 1) {
                        $method .= '<br><small>('.__('lang_v1.change_return').')</small>';
                    }

                    return $method;
                })
                ->editColumn('amount', function ($row) {
                    $amount = $row->is_return == 1 ? -1 * $row->amount : $row->amount;

                    return '<span class="paid-amount" data-orig-value="'.$amount.'" 
                    >'.$this->transactionUtil->num_f($amount, true).'</span>';
                })
                ->addColumn('action', '<button type="button" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-primary view_payment" data-href="{{ action([\App\Http\Controllers\TransactionPaymentController::class, \'viewPayment\'], [$DT_RowId]) }}">@lang("messages.view")
                    </button> @if(!empty($document))<a href="{{asset("/uploads/documents/" . $document)}}" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-accent" download=""><i class="fa fa-download"></i> @lang("purchase.download_document")</a>@endif')
                ->rawColumns(['invoice_no', 'amount', 'method', 'action', 'customer'])
                ->make(true);
        }
        $business_locations = BusinessLocation::forDropdown($business_id);
        $customers = Contact::customersDropdown($business_id, false);
        $customer_groups = CustomerGroup::forDropdown($business_id, false, true);

        return view('report.sell_payment_report')
            ->with(compact('business_locations', 'customers', 'payment_types', 'customer_groups'));
    }

    /**
     * Shows tables report
     *
     * @return \Illuminate\Http\Response
     */
    public function getTableReport(Request $request)
    {
        if (! auth()->user()->can('table_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $query = ResTable::leftjoin('transactions AS T', 'T.res_table_id', '=', 'res_tables.id')
                        ->where('T.business_id', $business_id)
                        ->where('T.type', 'sell')
                        ->where('T.status', 'final')
                        ->groupBy('res_tables.id')
                        ->select(DB::raw('SUM(final_total) as total_sell'), 'res_tables.name as table');

            $location_id = $request->get('location_id', null);
            if (! empty($location_id)) {
                $query->where('T.location_id', $location_id);
            }

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');

            if (! empty($start_date) && ! empty($end_date)) {
                $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            }

            return Datatables::of($query)
                ->editColumn('total_sell', function ($row) {
                    return '<span class="display_currency" data-currency_symbol="true">'.$row->total_sell.'</span>';
                })
                ->rawColumns(['total_sell'])
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('report.table_report')
            ->with(compact('business_locations'));
    }

    /**
     * Shows service staff report
     *
     * @return \Illuminate\Http\Response
     */
    public function getServiceStaffReport(Request $request)
    {
        if (! auth()->user()->can('service_staff_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        $waiters = $this->transactionUtil->serviceStaffDropdown($business_id);

        return view('report.service_staff_report')
            ->with(compact('business_locations', 'waiters'));
    }

    /**
     * Shows product sell report grouped by date
     *
     * @return \Illuminate\Http\Response
     */
    public function getproductSellGroupedReport(Request $request)
    {
        if (! auth()->user()->can('product_sell_grouped_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $location_id = $request->get('location_id', null);

        $vld_str = '';
        if (! empty($location_id)) {
            $vld_str = "AND vld.location_id=$location_id";
        }

        if ($request->ajax()) {
            $variation_id = $request->get('variation_id', null);
            $query = TransactionSellLine::join(
                'transactions as t',
                'transaction_sell_lines.transaction_id',
                '=',
                't.id'
            )
                ->join(
                    'variations as v',
                    'transaction_sell_lines.variation_id',
                    '=',
                    'v.id'
                )
                ->join('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
                ->join('products as p', 'pv.product_id', '=', 'p.id')
                ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final')
                ->select(
                    'p.name as product_name',
                    'p.enable_stock',
                    'p.type as product_type',
                    'pv.name as product_variation',
                    'v.name as variation_name',
                    'v.sub_sku',
                    't.id as transaction_id',
                    't.transaction_date as transaction_date',
                    'transaction_sell_lines.parent_sell_line_id',
                    DB::raw('DATE_FORMAT(t.transaction_date, "%Y-%m-%d") as formated_date'),
                    DB::raw("(SELECT SUM(vld.qty_available) FROM variation_location_details as vld WHERE vld.variation_id=v.id $vld_str) as current_stock"),
                    DB::raw('SUM(transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) as total_qty_sold'),
                    'u.short_name as unit',
                    DB::raw('SUM((transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) * transaction_sell_lines.unit_price_inc_tax) as subtotal')
                )
                ->groupBy('v.id')
                ->groupBy('formated_date');

            if (! empty($variation_id)) {
                $query->where('transaction_sell_lines.variation_id', $variation_id);
            }
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (! empty($start_date) && ! empty($end_date)) {
                $query->where('t.transaction_date', '>=', $start_date)
                    ->where('t.transaction_date', '<=', $end_date);
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            if (! empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }

            $customer_id = $request->get('customer_id', null);
            if (! empty($customer_id)) {
                $query->where('t.contact_id', $customer_id);
            }

            $customer_group_id = $request->get('customer_group_id', null);
            if (! empty($customer_group_id)) {
                $query->leftjoin('contacts AS c', 't.contact_id', '=', 'c.id')
                    ->leftjoin('customer_groups AS CG', 'c.customer_group_id', '=', 'CG.id')
                ->where('CG.id', $customer_group_id);
            }

            $category_id = $request->get('category_id', null);
            if (! empty($category_id)) {
                $query->where('p.category_id', $category_id);
            }

            $brand_id = $request->get('brand_id', null);
            if (! empty($brand_id)) {
                $query->where('p.brand_id', $brand_id);
            }

            return Datatables::of($query)
                ->editColumn('product_name', function ($row) {
                    $product_name = $row->product_name;
                    if ($row->product_type == 'variable') {
                        $product_name .= ' - '.$row->product_variation.' - '.$row->variation_name;
                    }

                    return $product_name;
                })
                ->editColumn('transaction_date', '{{@format_date($formated_date)}}')
                ->editColumn('total_qty_sold', function ($row) {
                    return '<span data-is_quantity="true" class="display_currency sell_qty" data-currency_symbol=false data-orig-value="'.(float) $row->total_qty_sold.'" data-unit="'.$row->unit.'" >'.(float) $row->total_qty_sold.'</span> '.$row->unit;
                })
                ->editColumn('current_stock', function ($row) {
                    if ($row->enable_stock) {
                        return '<span data-is_quantity="true" class="display_currency current_stock" data-currency_symbol=false data-orig-value="'.(float) $row->current_stock.'" data-unit="'.$row->unit.'" >'.(float) $row->current_stock.'</span> '.$row->unit;
                    } else {
                        return '';
                    }
                })
                 ->editColumn('subtotal', function ($row) {
                     $class = is_null($row->parent_sell_line_id) ? 'row_subtotal' : '';

                     return '<span class="'.$class.'" data-orig-value="'.$row->subtotal.'">'.
                     $this->transactionUtil->num_f($row->subtotal, true).'</span>';
                 })

                ->rawColumns(['current_stock', 'subtotal', 'total_qty_sold'])
                ->make(true);
        }
    }

    /**
     * Shows product sell report grouped by date
     *
     * @return \Illuminate\Http\Response
     */
    public function productSellReportBy(Request $request)
    {
        if (! auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $location_id = $request->get('location_id', null);
        $group_by = $request->get('group_by', null);

        $vld_str = '';
        if (! empty($location_id)) {
            $vld_str = "AND vld.location_id=$location_id";
        }

        if ($request->ajax()) {
            $query = TransactionSellLine::join(
                'transactions as t',
                'transaction_sell_lines.transaction_id',
                '=',
                't.id'
            )
                ->leftjoin(
                    'products as p',
                    'transaction_sell_lines.product_id',
                    '=',
                    'p.id'
                )
                ->leftjoin('categories as cat', 'p.category_id', '=', 'cat.id')
                ->leftjoin('brands as b', 'p.brand_id', '=', 'b.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final')
                ->select(
                    'b.name as brand_name',
                    'cat.name as category_name',
                    DB::raw("(SELECT SUM(vld.qty_available) FROM variation_location_details as vld WHERE vld.variation_id=transaction_sell_lines.variation_id $vld_str) as current_stock"),
                    DB::raw('SUM(transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) as total_qty_sold'),
                    DB::raw('SUM((transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) * transaction_sell_lines.unit_price_inc_tax) as subtotal'),
                    'transaction_sell_lines.parent_sell_line_id'
                );

            if ($group_by == 'category') {
                $query->groupBy('cat.id');
            } elseif ($group_by == 'brand') {
                $query->groupBy('b.id');
            }

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (! empty($start_date) && ! empty($end_date)) {
                $query->where('t.transaction_date', '>=', $start_date)
                    ->where('t.transaction_date', '<=', $end_date);
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            if (! empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }

            $customer_id = $request->get('customer_id', null);
            if (! empty($customer_id)) {
                $query->where('t.contact_id', $customer_id);
            }

            $customer_group_id = $request->get('customer_group_id', null);
            if (! empty($customer_group_id)) {
                $query->leftjoin('contacts AS c', 't.contact_id', '=', 'c.id')
                    ->leftjoin('customer_groups AS CG', 'c.customer_group_id', '=', 'CG.id')
                ->where('CG.id', $customer_group_id);
            }

            $category_id = $request->get('category_id', null);
            if (! empty($category_id)) {
                $query->where('p.category_id', $category_id);
            }

            $brand_id = $request->get('brand_id', null);
            if (! empty($brand_id)) {
                $query->where('p.brand_id', $brand_id);
            }

            return Datatables::of($query)
                ->editColumn('category_name', '{{$category_name ?? __("lang_v1.uncategorized")}}')
                ->editColumn('brand_name', '{{$brand_name ?? __("lang_v1.no_brand")}}')
                ->editColumn('total_qty_sold', function ($row) {
                    return '<span data-is_quantity="true" class="display_currency sell_qty" data-currency_symbol=false data-orig-value="'.(float) $row->total_qty_sold.'" data-unit="" >'.(float) $row->total_qty_sold.'</span> '.$row->unit;
                })
                ->editColumn('current_stock', function ($row) {
                    return '<span data-is_quantity="true" class="display_currency current_stock" data-currency_symbol=false data-orig-value="'.(float) $row->current_stock.'" data-unit="">'.(float) $row->current_stock.'</span> ';
                })
                 ->editColumn('subtotal', function ($row) {
                     $class = is_null($row->parent_sell_line_id) ? 'row_subtotal' : '';

                     return '<span class="'.$class.'" data-orig-value="'.$row->subtotal.'">'
                    .$this->transactionUtil->num_f($row->subtotal, true).'</span>';
                 })

                ->rawColumns(['current_stock', 'subtotal', 'total_qty_sold', 'category_name'])
                ->make(true);
        }
    }

    /**
     * Shows product stock details and allows to adjust mismatch
     *
     * @return \Illuminate\Http\Response
     */
    public function productStockDetails()
    {
        if (! auth()->user()->can('report.stock_details')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $variation_id = request()->get('variation_id', null);
        $location_id = request()->input('location_id');

        $location = null;
        $stock_details = [];

        if (! empty(request()->input('location_id'))) {
            $location = BusinessLocation::where('business_id', $business_id)
                                        ->where('id', $location_id)
                                        ->first();
            $stock_details = $this->productUtil->getVariationStockMisMatch($business_id, $variation_id, $location_id);
        }

        $business_locations = BusinessLocation::forDropdown($business_id);

        return view('report.product_stock_details')
            ->with(compact('stock_details', 'business_locations', 'location'));
    }

    /**
     * Adjusts stock availability mismatch if found
     *
     * @return \Illuminate\Http\Response
     */
    public function adjustProductStock()
    {
        if (! auth()->user()->can('report.stock_details')) {
            abort(403, 'Unauthorized action.');
        }

        if (! empty(request()->input('variation_id'))
            && ! empty(request()->input('location_id'))
            && request()->has('stock')) {
            $business_id = request()->session()->get('user.business_id');

            $this->productUtil->fixVariationStockMisMatch($business_id, request()->input('variation_id'), request()->input('location_id'), request()->input('stock'));
        }

        return redirect()->back()->with(['status' => [
            'success' => 1,
            'msg' => __('lang_v1.updated_succesfully'),
        ]]);
    }

    /**
     * Retrieves line orders/sales
     *
     * @return obj
     */
    public function serviceStaffLineOrders()
    {
        $business_id = request()->session()->get('user.business_id');

        $query = TransactionSellLine::leftJoin('transactions as t', 't.id', '=', 'transaction_sell_lines.transaction_id')
                ->leftJoin('variations as v', 'transaction_sell_lines.variation_id', '=', 'v.id')
                ->leftJoin('products as p', 'v.product_id', '=', 'p.id')
                ->leftJoin('units as u', 'p.unit_id', '=', 'u.id')
                ->leftJoin('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
                ->leftJoin('users as ss', 'ss.id', '=', 'transaction_sell_lines.res_service_staff_id')
                ->leftjoin(
                    'business_locations AS bl',
                    't.location_id',
                    '=',
                    'bl.id'
                )
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final')
                ->whereNotNull('transaction_sell_lines.res_service_staff_id');

        if (! empty(request()->service_staff_id)) {
            $query->where('transaction_sell_lines.res_service_staff_id', request()->service_staff_id);
        }

        if (request()->has('location_id')) {
            $location_id = request()->get('location_id');
            if (! empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }
        }

        if (! empty(request()->start_date) && ! empty(request()->end_date)) {
            $start = request()->start_date;
            $end = request()->end_date;
            $query->whereDate('t.transaction_date', '>=', $start)
                        ->whereDate('t.transaction_date', '<=', $end);
        }

        $query->select(
            'p.name as product_name',
            'p.type as product_type',
            'v.name as variation_name',
            'pv.name as product_variation_name',
            'u.short_name as unit',
            't.id as transaction_id',
            'bl.name as business_location',
            't.transaction_date',
            't.invoice_no',
            'transaction_sell_lines.quantity',
            'transaction_sell_lines.unit_price_before_discount',
            'transaction_sell_lines.line_discount_type',
            'transaction_sell_lines.line_discount_amount',
            'transaction_sell_lines.item_tax',
            'transaction_sell_lines.unit_price_inc_tax',
            DB::raw('CONCAT(COALESCE(ss.first_name, ""), COALESCE(ss.last_name, "")) as service_staff')
        );

        $datatable = Datatables::of($query)
            ->editColumn('product_name', function ($row) {
                $name = $row->product_name;
                if ($row->product_type == 'variable') {
                    $name .= ' - '.$row->product_variation_name.' - '.$row->variation_name;
                }

                return $name;
            })
            ->editColumn(
                'unit_price_inc_tax',
                '<span class="display_currency unit_price_inc_tax" data-currency_symbol="true" data-orig-value="{{$unit_price_inc_tax}}">{{$unit_price_inc_tax}}</span>'
            )
            ->editColumn(
                'item_tax',
                '<span class="display_currency item_tax" data-currency_symbol="true" data-orig-value="{{$item_tax}}">{{$item_tax}}</span>'
            )
            ->editColumn(
                'quantity',
                '<span class="display_currency quantity" data-unit="{{$unit}}" data-currency_symbol="false" data-orig-value="{{$quantity}}">{{$quantity}}</span> {{$unit}}'
            )
            ->editColumn(
                'unit_price_before_discount',
                '<span class="display_currency unit_price_before_discount" data-currency_symbol="true" data-orig-value="{{$unit_price_before_discount}}">{{$unit_price_before_discount}}</span>'
            )
            ->addColumn(
                'total',
                '<span class="display_currency total" data-currency_symbol="true" data-orig-value="{{$unit_price_inc_tax * $quantity}}">{{$unit_price_inc_tax * $quantity}}</span>'
            )
            ->editColumn(
                'line_discount_amount',
                function ($row) {
                    $discount = ! empty($row->line_discount_amount) ? $row->line_discount_amount : 0;

                    if (! empty($discount) && $row->line_discount_type == 'percentage') {
                        $discount = $row->unit_price_before_discount * ($discount / 100);
                    }

                    return '<span class="display_currency total-discount" data-currency_symbol="true" data-orig-value="'.$discount.'">'.$discount.'</span>';
                }
            )
            ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')

            ->rawColumns(['line_discount_amount', 'unit_price_before_discount', 'item_tax', 'unit_price_inc_tax', 'item_tax', 'quantity', 'total'])
                  ->make(true);

        return $datatable;
    }

    /**
     * Lists profit by product, category, brand, location, invoice and date.
     *
     * This method calculates and returns the gross profit for sales based on different
     * grouping criteria specified by the $by parameter. It handles various product types
     * including combo products and considers stock settings.
     *
     * @param string|null $by The grouping criteria for the profit report. 
     *                        Possible values: 'product', 'category', 'brand', 'location',
     *                        'invoice', 'date', 'day', 'customer', 'service_staff', or null.
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse Returns a view for 'day' grouping,
     *                                                             or JSON response for other groupings.
     * @throws \Exception When an invalid grouping criteria is provided
     */
    public function getProfit($by = null)
    {
        $business_id = request()->session()->get('user.business_id');

        // Build the base query for profit calculation
        // We join multiple tables to get all the necessary data for profit calculation:
        // - transactions (aliased as 'sale'): Contains the main sale information
        // - transaction_sell_lines: Contains the individual line items in a sale
        // - transaction_sell_lines_purchase_lines (TSPL): Links sell lines to their purchase lines
        // - purchase_lines (PL): Contains the purchase information, including cost
        // - products (P): Contains product information
        $query = TransactionSellLine::join('transactions as sale', 'transaction_sell_lines.transaction_id', '=', 'sale.id')
            ->leftjoin('transaction_sell_lines_purchase_lines as TSPL', 'transaction_sell_lines.id', '=', 'TSPL.sell_line_id')
            ->leftjoin(
                'purchase_lines as PL',
                'TSPL.purchase_line_id',
                '=',
                'PL.id'
            )
            // Only include finalized sales (not drafts, quotations, etc.)
            ->where('sale.type', 'sell')
            ->where('sale.status', 'final')
            ->join('products as P', 'transaction_sell_lines.product_id', '=', 'P.id')
            ->where('sale.business_id', $business_id)
            // Exclude combo child products to avoid double counting
            ->where('transaction_sell_lines.children_type', '!=', 'combo');

        // Calculate gross profit using complex conditional logic
        // The profit calculation varies based on product type and stock settings:
        //
        // CASE 1: For combo products (products composed of multiple items)
        //   - We need to calculate profit based on the child products
        //   - We use a subquery to sum the profit of all child products
        //   - For each child: (quantity - returned) * (selling price - purchase price)
        //
        // CASE 2: For non-stock products (services, digital goods, etc.)
        //   - Profit is simply (quantity - returned) * selling price
        //   - This is because there's no purchase cost for non-stock items
        //
        // CASE 3: For regular stock products
        //   - Profit is (quantity - returned) * (selling price - purchase price)
        //   - This is the standard profit calculation (revenue - cost)
        $query->select(DB::raw('SUM(IF (TSPL.id IS NULL AND P.type="combo", ( 
            SELECT Sum((tspl2.quantity - tspl2.qty_returned) * (tsl.unit_price_inc_tax - pl2.purchase_price_inc_tax)) AS total
                FROM transaction_sell_lines AS tsl
                    JOIN transaction_sell_lines_purchase_lines AS tspl2
                ON tsl.id=tspl2.sell_line_id 
                JOIN purchase_lines AS pl2 
                ON tspl2.purchase_line_id = pl2.id 
                WHERE tsl.parent_sell_line_id = transaction_sell_lines.id), IF(P.enable_stock=0,(transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) * transaction_sell_lines.unit_price_inc_tax,   
                (TSPL.quantity - TSPL.qty_returned) * (transaction_sell_lines.unit_price_inc_tax - PL.purchase_price_inc_tax)) )) AS gross_profit')
            );

        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->whereIn('sale.location_id', $permitted_locations);
        }

        if (! empty(request()->location_id)) {
            $query->where('sale.location_id', request()->location_id);
        }

        if (! empty(request()->start_date) && ! empty(request()->end_date)) {
            $start = request()->start_date;
            $end = request()->end_date;
            $query->whereDate('sale.transaction_date', '>=', $start)
                        ->whereDate('sale.transaction_date', '<=', $end);
        }

        if ($by == 'product') {
            $query->join('variations as V', 'transaction_sell_lines.variation_id', '=', 'V.id')
                ->leftJoin('product_variations as PV', 'PV.id', '=', 'V.product_variation_id')
                ->addSelect(DB::raw("IF(P.type='variable', CONCAT(P.name, ' - ', PV.name, ' - ', V.name, ' (', V.sub_sku, ')'), CONCAT(P.name, ' (', P.sku, ')')) as product"))
                ->groupBy('V.id');
        }

        if ($by == 'category') {
            $query->join('variations as V', 'transaction_sell_lines.variation_id', '=', 'V.id')
                ->leftJoin('categories as C', 'C.id', '=', 'P.category_id')
                ->addSelect('C.name as category')
                ->groupBy('C.id');
        }

        if ($by == 'brand') {
            $query->join('variations as V', 'transaction_sell_lines.variation_id', '=', 'V.id')
                ->leftJoin('brands as B', 'B.id', '=', 'P.brand_id')
                ->addSelect('B.name as brand')
                ->groupBy('B.id');
        }

        if ($by == 'location') {
            $query->join('business_locations as L', 'sale.location_id', '=', 'L.id')
                ->addSelect('L.name as location')
                ->groupBy('L.id');
        }

        if ($by == 'invoice') {
            $query->addSelect(
                'sale.invoice_no',
                'sale.id as transaction_id',
                'sale.discount_type',
                'sale.discount_amount',
                'sale.total_before_tax'
            )
                ->groupBy('sale.invoice_no');
        }

        if ($by == 'date') {
            $query->addSelect('sale.transaction_date')
                ->groupBy(DB::raw('DATE(sale.transaction_date)'));
        }

        if ($by == 'day') {
            $results = $query->addSelect(DB::raw('DAYNAME(sale.transaction_date) as day'))
                ->groupBy(DB::raw('DAYOFWEEK(sale.transaction_date)'))
                ->get();

            $profits = [];
            foreach ($results as $result) {
                $profits[strtolower($result->day)] = $result->gross_profit;
            }
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

            return view('report.partials.profit_by_day')->with(compact('profits', 'days'));
        }

        if ($by == 'customer') {
            $query->join('contacts as CU', 'sale.contact_id', '=', 'CU.id')
            ->addSelect('CU.name as customer', 'CU.supplier_business_name')
                ->groupBy('sale.contact_id');
        }

        if ($by == 'service_staff') {
            $query->join('users as U', function ($join) {
                $join->on(DB::raw("COALESCE(transaction_sell_lines.res_service_staff_id, sale.res_waiter_id)"), '=', 'U.id');
            })
            ->where('U.is_enable_service_staff_pin', 1)
            ->addSelect(
                "U.first_name as f_name",
                "U.last_name as l_name",
                "U.surname as surname"
            )
            ->groupBy('U.id');
        }        

        $datatable = Datatables::of($query);

        if (in_array($by, ['invoice'])) {
            $datatable->editColumn('gross_profit', function ($row) {
                $discount = $row->discount_amount;
                if ($row->discount_type == 'percentage') {
                    $discount = ($row->discount_amount * $row->total_before_tax) / 100;
                }

                $profit = $row->gross_profit - $discount;
                $html = '<span class="gross-profit" data-orig-value="'.$profit.'" >'.$this->transactionUtil->num_f($profit, true).'</span>';

                return $html;
            });
        } else {
            $datatable->editColumn(
                'gross_profit',
                function ($row) {
                    return '<span class="gross-profit" data-orig-value="'.$row->gross_profit.'">'.$this->transactionUtil->num_f($row->gross_profit, true).'</span>';
                });
        }

        if ($by == 'category') {
            $datatable->editColumn(
                'category',
                '{{$category ?? __("lang_v1.uncategorized")}}'
            );
        }
        if ($by == 'brand') {
            $datatable->editColumn(
                'brand',
                '{{$brand ?? __("report.others")}}'
            );
        }

        if ($by == 'date') {
            $datatable->editColumn('transaction_date', '{{@format_date($transaction_date)}}');
        }

        if ($by == 'product') {
            $datatable->filterColumn(
                 'product',
                 function ($query, $keyword) {
                     $query->whereRaw("IF(P.type='variable', CONCAT(P.name, ' - ', PV.name, ' - ', V.name, ' (', V.sub_sku, ')'), CONCAT(P.name, ' (', P.sku, ')')) LIKE '%{$keyword}%'");
                 });
        }
        $raw_columns = ['gross_profit'];

        if ($by == 'customer') {
            $datatable->editColumn('customer', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif {{$customer}}');
            $raw_columns[] = 'customer';
        }

        if($by == 'service_staff'){
            $datatable->editColumn('staff_name', '{{$surname}} {{$f_name}} {{$l_name}}');
            $raw_columns[] = 'staff_name';
        }

        if ($by == 'invoice') {
            $datatable->editColumn('invoice_no', function ($row) {
                return '<a data-href="'.action([\App\Http\Controllers\SellController::class, 'show'], [$row->transaction_id])
                            .'" href="#" data-container=".view_modal" class="btn-modal">'.$row->invoice_no.'</a>';
            });
            $raw_columns[] = 'invoice_no';
        }

        return $datatable->rawColumns($raw_columns)
                  ->make(true);
    }

    /**
     * Shows items report from sell purchase mapping table
     *
     * @return \Illuminate\Http\Response
     */
    public function itemsReport()
    {
        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            $query = TransactionSellLinesPurchaseLines::leftJoin('transaction_sell_lines 
                    as SL', 'SL.id', '=', 'transaction_sell_lines_purchase_lines.sell_line_id')
                ->leftJoin('stock_adjustment_lines 
                    as SAL', 'SAL.id', '=', 'transaction_sell_lines_purchase_lines.stock_adjustment_line_id')
                ->leftJoin('transactions as sale', 'SL.transaction_id', '=', 'sale.id')
                ->leftJoin('transactions as stock_adjustment', 'SAL.transaction_id', '=', 'stock_adjustment.id')
                ->join('purchase_lines as PL', 'PL.id', '=', 'transaction_sell_lines_purchase_lines.purchase_line_id')
                ->join('transactions as purchase', 'PL.transaction_id', '=', 'purchase.id')
                ->join('business_locations as bl', 'purchase.location_id', '=', 'bl.id')
                ->join(
                    'variations as v',
                    'PL.variation_id',
                    '=',
                    'v.id'
                    )
                ->join('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
                ->join('products as p', 'PL.product_id', '=', 'p.id')
                ->join('units as u', 'p.unit_id', '=', 'u.id')
                ->leftJoin('contacts as suppliers', 'purchase.contact_id', '=', 'suppliers.id')
                ->leftJoin('contacts as customers', 'sale.contact_id', '=', 'customers.id')
                ->where('purchase.business_id', $business_id)
                ->select(
                    'v.sub_sku as sku',
                    'p.type as product_type',
                    'p.name as product_name',
                    'v.name as variation_name',
                    'pv.name as product_variation',
                    'u.short_name as unit',
                    'purchase.transaction_date as purchase_date',
                    'purchase.ref_no as purchase_ref_no',
                    'purchase.type as purchase_type',
                    'purchase.id as purchase_id',
                    'suppliers.name as supplier',
                    'suppliers.supplier_business_name',
                    'PL.purchase_price_inc_tax as purchase_price',
                    'sale.transaction_date as sell_date',
                    'stock_adjustment.transaction_date as stock_adjustment_date',
                    'sale.invoice_no as sale_invoice_no',
                    'stock_adjustment.ref_no as stock_adjustment_ref_no',
                    'customers.name as customer',
                    'customers.supplier_business_name as customer_business_name',
                    'transaction_sell_lines_purchase_lines.quantity as quantity',
                    'SL.unit_price_inc_tax as selling_price',
                    'SAL.unit_price as stock_adjustment_price',
                    'transaction_sell_lines_purchase_lines.stock_adjustment_line_id',
                    'transaction_sell_lines_purchase_lines.sell_line_id',
                    'transaction_sell_lines_purchase_lines.purchase_line_id',
                    'transaction_sell_lines_purchase_lines.qty_returned',
                    'bl.name as location',
                    'SL.sell_line_note',
                    'PL.lot_number'
                );

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('purchase.location_id', $permitted_locations);
            }

            if (! empty(request()->purchase_start) && ! empty(request()->purchase_end)) {
                $start = request()->purchase_start;
                $end = request()->purchase_end;
                $query->whereDate('purchase.transaction_date', '>=', $start)
                            ->whereDate('purchase.transaction_date', '<=', $end);
            }
            if (! empty(request()->sale_start) && ! empty(request()->sale_end)) {
                $start = request()->sale_start;
                $end = request()->sale_end;
                $query->where(function ($q) use ($start, $end) {
                    $q->where(function ($qr) use ($start, $end) {
                        $qr->whereDate('sale.transaction_date', '>=', $start)
                           ->whereDate('sale.transaction_date', '<=', $end);
                    })->orWhere(function ($qr) use ($start, $end) {
                        $qr->whereDate('stock_adjustment.transaction_date', '>=', $start)
                           ->whereDate('stock_adjustment.transaction_date', '<=', $end);
                    });
                });
            }

            $supplier_id = request()->get('supplier_id', null);
            if (! empty($supplier_id)) {
                $query->where('suppliers.id', $supplier_id);
            }

            $customer_id = request()->get('customer_id', null);
            if (! empty($customer_id)) {
                $query->where('customers.id', $customer_id);
            }

            $location_id = request()->get('location_id', null);
            if (! empty($location_id)) {
                $query->where('purchase.location_id', $location_id);
            }

            $only_mfg_products = request()->get('only_mfg_products', 0);
            if (! empty($only_mfg_products)) {
                $query->where('purchase.type', 'production_purchase');
            }

            return Datatables::of($query)
                ->editColumn('product_name', function ($row) {
                    $product_name = $row->product_name;
                    if ($row->product_type == 'variable') {
                        $product_name .= ' - '.$row->product_variation.' - '.$row->variation_name;
                    }

                    return $product_name;
                })
                ->editColumn('purchase_date', '{{@format_datetime($purchase_date)}}')
                ->editColumn('purchase_ref_no', function ($row) {
                    $html = $row->purchase_type == 'purchase' ? '<a data-href="'.action([\App\Http\Controllers\PurchaseController::class, 'show'], [$row->purchase_id])
                            .'" href="#" data-container=".view_modal" class="btn-modal">'.$row->purchase_ref_no.'</a>' : $row->purchase_ref_no;
                    if ($row->purchase_type == 'opening_stock') {
                        $html .= '('.__('lang_v1.opening_stock').')';
                    }

                    return $html;
                })
                ->editColumn('purchase_price', function ($row) {
                    return '<span 
                    class="purchase_price" data-orig-value="'.$row->purchase_price.'">'.
                    $this->transactionUtil->num_f($row->purchase_price, true).'</span>';
                })
                ->editColumn('sell_date', '@if(!empty($sell_line_id)) {{@format_datetime($sell_date)}} @else {{@format_datetime($stock_adjustment_date)}} @endif')

                ->editColumn('sale_invoice_no', function ($row) {
                    $invoice_no = ! empty($row->sell_line_id) ? $row->sale_invoice_no : $row->stock_adjustment_ref_no.'<br><small>('.__('stock_adjustment.stock_adjustment').'</small)>';

                    return $invoice_no;
                })
                ->editColumn('quantity', function ($row) {
                    $html = '<span data-is_quantity="true" class="display_currency quantity" data-currency_symbol=false data-orig-value="'.(float) $row->quantity.'" data-unit="'.$row->unit.'" >'.(float) $row->quantity.'</span> '.$row->unit;

                    if (empty($row->sell_line_id)) {
                        $html .= '<br><small>('.__('stock_adjustment.stock_adjustment').'</small)>';
                    }
                    if ($row->qty_returned > 0) {
                        $html .= '<small><i>(<span data-is_quantity="true" class="display_currency" data-currency_symbol=false>'.(float) $row->quantity.'</span> '.$row->unit.' '.__('lang_v1.returned').')</i></small>';
                    }

                    return $html;
                })
                 ->editColumn('selling_price', function ($row) {
                     $selling_price = ! empty($row->sell_line_id) ? $row->selling_price : $row->stock_adjustment_price;

                     return '<span class="row_selling_price" data-orig-value="'.$selling_price.
                      '">'.$this->transactionUtil->num_f($selling_price, true).'</span>';
                 })

                 ->addColumn('subtotal', function ($row) {
                     $selling_price = ! empty($row->sell_line_id) ? $row->selling_price : $row->stock_adjustment_price;
                     $subtotal = $selling_price * $row->quantity;

                     return '<span class="row_subtotal" data-orig-value="'.$subtotal.'">'.
                     $this->transactionUtil->num_f($subtotal, true).'</span>';
                 })
                 ->editColumn('supplier', '@if(!empty($supplier_business_name))
                 {{$supplier_business_name}},<br> @endif {{$supplier}}')
                 ->editColumn('customer', '@if(!empty($customer_business_name))
                 {{$customer_business_name}},<br> @endif {{$customer}}')
                ->filterColumn('sale_invoice_no', function ($query, $keyword) {
                    $query->where('sale.invoice_no', 'like', ["%{$keyword}%"])
                          ->orWhere('stock_adjustment.ref_no', 'like', ["%{$keyword}%"]);
                })

                ->rawColumns(['subtotal', 'selling_price', 'quantity', 'purchase_price', 'sale_invoice_no', 'purchase_ref_no', 'supplier', 'customer'])
                ->make(true);
        }

        $suppliers = Contact::suppliersDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);
        $business_locations = BusinessLocation::forDropdown($business_id);

        return view('report.items_report')->with(compact('suppliers', 'customers', 'business_locations'));
    }

    /**
     * Shows purchase report
     *
     * @return \Illuminate\Http\Response
     */
    public function purchaseReport()
    {
        if ((! auth()->user()->can('purchase.view') && ! auth()->user()->can('purchase.create') && ! auth()->user()->can('view_own_purchase')) || empty(config('constants.show_report_606'))) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);
            $purchases = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                    ->join(
                        'business_locations AS BS',
                        'transactions.location_id',
                        '=',
                        'BS.id'
                    )
                    ->leftJoin(
                        'transaction_payments AS TP',
                        'transactions.id',
                        '=',
                        'TP.transaction_id'
                    )
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'purchase')
                    ->with(['payment_lines'])
                    ->select(
                        'transactions.id',
                        'transactions.ref_no',
                        'contacts.name',
                        'contacts.contact_id',
                        'final_total',
                        'total_before_tax',
                        'discount_amount',
                        'discount_type',
                        'tax_amount',
                        DB::raw('DATE_FORMAT(transaction_date, "%Y/%m") as purchase_year_month'),
                        DB::raw('DATE_FORMAT(transaction_date, "%d") as purchase_day')
                    )
                    ->groupBy('transactions.id');

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $purchases->whereIn('transactions.location_id', $permitted_locations);
            }

            if (! empty(request()->supplier_id)) {
                $purchases->where('contacts.id', request()->supplier_id);
            }
            if (! empty(request()->location_id)) {
                $purchases->where('transactions.location_id', request()->location_id);
            }
            if (! empty(request()->input('payment_status')) && request()->input('payment_status') != 'overdue') {
                $purchases->where('transactions.payment_status', request()->input('payment_status'));
            } elseif (request()->input('payment_status') == 'overdue') {
                $purchases->whereIn('transactions.payment_status', ['due', 'partial'])
                    ->whereNotNull('transactions.pay_term_number')
                    ->whereNotNull('transactions.pay_term_type')
                    ->whereRaw("IF(transactions.pay_term_type='days', DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number DAY) < CURDATE(), DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number MONTH) < CURDATE())");
            }

            if (! empty(request()->status)) {
                $purchases->where('transactions.status', request()->status);
            }

            if (! empty(request()->start_date) && ! empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $purchases->whereDate('transactions.transaction_date', '>=', $start)
                            ->whereDate('transactions.transaction_date', '<=', $end);
            }

            if (! auth()->user()->can('purchase.view') && auth()->user()->can('view_own_purchase')) {
                $purchases->where('transactions.created_by', request()->session()->get('user.id'));
            }

            return Datatables::of($purchases)
                ->removeColumn('id')
                ->editColumn(
                    'final_total',
                    '<span class="display_currency final_total" data-currency_symbol="true" data-orig-value="{{$final_total}}">{{$final_total}}</span>'
                )
                ->editColumn(
                    'total_before_tax',
                    '<span class="display_currency total_before_tax" data-currency_symbol="true" data-orig-value="{{$total_before_tax}}">{{$total_before_tax}}</span>'
                )
                ->editColumn(
                    'tax_amount',
                    '<span class="display_currency tax_amount" data-currency_symbol="true" data-orig-value="{{$tax_amount}}">{{$tax_amount}}</span>'
                )
                ->editColumn(
                    'discount_amount',
                    function ($row) {
                        $discount = ! empty($row->discount_amount) ? $row->discount_amount : 0;

                        if (! empty($discount) && $row->discount_type == 'percentage') {
                            $discount = $row->total_before_tax * ($discount / 100);
                        }

                        return '<span class="display_currency total-discount" data-currency_symbol="true" data-orig-value="'.$discount.'">'.$discount.'</span>';
                    }
                )
                ->addColumn('payment_year_month', function ($row) {
                    $year_month = '';
                    if (! empty($row->payment_lines->first())) {
                        $year_month = \Carbon::parse($row->payment_lines->first()->paid_on)->format('Y/m');
                    }

                    return $year_month;
                })
                ->addColumn('payment_day', function ($row) {
                    $payment_day = '';
                    if (! empty($row->payment_lines->first())) {
                        $payment_day = \Carbon::parse($row->payment_lines->first()->paid_on)->format('d');
                    }

                    return $payment_day;
                })
                ->addColumn('payment_method', function ($row) use ($payment_types) {
                    $methods = array_unique($row->payment_lines->pluck('method')->toArray());
                    $count = count($methods);
                    $payment_method = '';
                    if ($count == 1) {
                        $payment_method = $payment_types[$methods[0]];
                    } elseif ($count > 1) {
                        $payment_method = __('lang_v1.checkout_multi_pay');
                    }

                    $html = ! empty($payment_method) ? '<span class="payment-method" data-orig-value="'.$payment_method.'" data-status-name="'.$payment_method.'">'.$payment_method.'</span>' : '';

                    return $html;
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can('purchase.view')) {
                            return  action([\App\Http\Controllers\PurchaseController::class, 'show'], [$row->id]);
                        } else {
                            return '';
                        }
                    }, ])
                ->rawColumns(['final_total', 'total_before_tax', 'tax_amount', 'discount_amount', 'payment_method'])
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $suppliers = Contact::suppliersDropdown($business_id, false);
        $orderStatuses = $this->productUtil->orderStatuses();

        return view('report.purchase_report')
            ->with(compact('business_locations', 'suppliers', 'orderStatuses'));
    }

    /**
     * Shows sale report
     *
     * @return \Illuminate\Http\Response
     */
    public function saleReport()
    {
        if ((! auth()->user()->can('sell.view') && ! auth()->user()->can('sell.create') && ! auth()->user()->can('direct_sell.access') && ! auth()->user()->can('view_own_sell_only')) || empty(config('constants.show_report_607'))) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);

        return view('report.sale_report')
            ->with(compact('business_locations', 'customers'));
    }

    /**
     * Calculates stock values
     *
     * @return array
     */
    public function getStockValue()
    {
        $business_id = request()->session()->get('user.business_id');
        $end_date = \Carbon::now()->format('Y-m-d');
        $location_id = request()->input('location_id');
        $filters = request()->only(['category_id', 'sub_category_id', 'brand_id', 'unit_id']);

        $permitted_locations = auth()->user()->permitted_locations();
        //Get Closing stock
        $closing_stock_by_pp = $this->transactionUtil->getOpeningClosingStock(
            $business_id,
            $end_date,
            $location_id,
            false,
            false,
            $filters,
            $permitted_locations
        );
        $closing_stock_by_sp = $this->transactionUtil->getOpeningClosingStock(
            $business_id,
            $end_date,
            $location_id,
            false,
            true,
            $filters,
            $permitted_locations
        );
        $potential_profit = $closing_stock_by_sp - $closing_stock_by_pp;
        $profit_margin = empty($closing_stock_by_sp) ? 0 : ($potential_profit / $closing_stock_by_sp) * 100;

        return [
            'closing_stock_by_pp' => $closing_stock_by_pp,
            'closing_stock_by_sp' => $closing_stock_by_sp,
            'potential_profit' => $potential_profit,
            'profit_margin' => $profit_margin,
        ];
    }

    public function activityLog()
    {
        $business_id = request()->session()->get('user.business_id');
        $transaction_types = [
            'contact' => __('report.contact'),
            'user' => __('report.user'),
            'sell' => __('sale.sale'),
            'purchase' => __('lang_v1.purchase'),
            'sales_order' => __('lang_v1.sales_order'),
            'purchase_order' => __('lang_v1.purchase_order'),
            'sell_return' => __('lang_v1.sell_return'),
            'purchase_return' => __('lang_v1.purchase_return'),
            'sell_transfer' => __('lang_v1.stock_transfer'),
            'stock_adjustment' => __('stock_adjustment.stock_adjustment'),
            'expense' => __('lang_v1.expense'),
        ];

        if (request()->ajax()) {
            $activities = Activity::with(['subject'])
                                ->leftjoin('users as u', 'u.id', '=', 'activity_log.causer_id')
                                ->where('activity_log.business_id', $business_id)
                                ->select(
                                    'activity_log.*',
                                    DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as created_by")
                                );

            if (! empty(request()->start_date) && ! empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $activities->whereDate('activity_log.created_at', '>=', $start)
                            ->whereDate('activity_log.created_at', '<=', $end);
            }

            if (! empty(request()->user_id)) {
                $activities->where('causer_id', request()->user_id);
            }

            $subject_type = request()->subject_type;
            if (! empty($subject_type)) {
                if ($subject_type == 'contact') {
                    $activities->where('subject_type', Contact::class);
                } elseif ($subject_type == 'user') {
                    $activities->where('subject_type', User::class);
                } elseif (in_array($subject_type, ['sell', 'purchase',
                    'sales_order', 'purchase_order', 'sell_return', 'purchase_return', 'sell_transfer', 'expense', 'purchase_order', ])) {
                    $activities->where('subject_type', Transaction::class);
                    $activities->whereHasMorph('subject', Transaction::class, function ($q) use ($subject_type) {
                        $q->where('type', $subject_type);
                    });
                }
            }

            $sell_statuses = Transaction::sell_statuses();
            $sales_order_statuses = Transaction::sales_order_statuses(true);
            $purchase_statuses = $this->transactionUtil->orderStatuses();
            $shipping_statuses = $this->transactionUtil->shipping_statuses();

            $statuses = array_merge($sell_statuses, $sales_order_statuses, $purchase_statuses);

            return Datatables::of($activities)
                            ->editColumn('created_at', '{{@format_datetime($created_at)}}')
                            ->addColumn('subject_type', function ($row) use ($transaction_types) {
                                $subject_type = '';
                                if ($row->subject_type == Contact::class) {
                                    $subject_type = __('contact.contact');
                                } elseif ($row->subject_type == User::class) {
                                    $subject_type = __('report.user');
                                } elseif ($row->subject_type == Transaction::class && ! empty($row->subject->type)) {
                                    $subject_type = isset($transaction_types[$row->subject->type]) ? $transaction_types[$row->subject->type] : '';
                                } elseif (($row->subject_type == TransactionPayment::class)) {
                                    $subject_type = __('lang_v1.payment');
                                }

                                return $subject_type;
                            })
                            ->addColumn('note', function ($row) use ($statuses, $shipping_statuses) {
                                $html = '';
                                if (! empty($row->subject->ref_no)) {
                                    $html .= __('purchase.ref_no').': '.$row->subject->ref_no.'<br>';
                                }
                                if (! empty($row->subject->invoice_no)) {
                                    $html .= __('sale.invoice_no').': '.$row->subject->invoice_no.'<br>';
                                }
                                if ($row->subject_type == \App\Transaction::class && ! empty($row->subject) && in_array($row->subject->type, ['sell', 'purchase'])) {
                                    $html .= view('sale_pos.partials.activity_row', ['activity' => $row, 'statuses' => $statuses, 'shipping_statuses' => $shipping_statuses])->render();
                                } else {
                                    $update_note = $row->getExtraProperty('update_note');
                                    if (! empty($update_note) && ! is_array($update_note)) {
                                        $html .= $update_note;
                                    }
                                }

                                if ($row->description == 'contact_deleted') {
                                    $html .= $row->getExtraProperty('supplier_business_name') ?? '';
                                    $html .= '<br>';
                                }

                                if (! empty($row->getExtraProperty('name'))) {
                                    $html .= __('user.name').': '.$row->getExtraProperty('name').'<br>';
                                }

                                if (! empty($row->getExtraProperty('id'))) {
                                    $html .= 'id: '.$row->getExtraProperty('id').'<br>';
                                }
                                if (! empty($row->getExtraProperty('invoice_no'))) {
                                    $html .= __('sale.invoice_no').': '.$row->getExtraProperty('invoice_no');
                                }

                                if (! empty($row->getExtraProperty('ref_no'))) {
                                    $html .= __('purchase.ref_no').': '.$row->getExtraProperty('ref_no');
                                }

                                return $html;
                            })
                            ->filterColumn('created_by', function ($query, $keyword) {
                                $query->whereRaw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) like ?", ["%{$keyword}%"]);
                            })
                            ->editColumn('description', function ($row) {
                                return __('lang_v1.'.$row->description);
                            })
                            ->rawColumns(['note'])
                            ->make(true);
        }

        $users = User::allUsersDropdown($business_id, false);

        return view('report.activity_log')->with(compact('users', 'transaction_types'));
    }

    public function gstSalesReport(Request $request)
    {
        if (! auth()->user()->can('tax_report.view') || empty(config('constants.enable_gst_report_india'))) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $taxes = TaxRate::where('business_id', $business_id)
                        ->where('is_tax_group', 0)
                        ->select(['id', 'name', 'amount'])
                        ->get()
                        ->toArray();

        if ($request->ajax()) {
            $query = TransactionSellLine::join(
                'transactions as t',
                'transaction_sell_lines.transaction_id',
                '=',
                't.id'
            )
                ->join('contacts as c', 't.contact_id', '=', 'c.id')
                ->join('products as p', 'transaction_sell_lines.product_id', '=', 'p.id')
                ->leftjoin('categories as cat', 'p.category_id', '=', 'cat.id')
                ->leftjoin('tax_rates as tr', 'transaction_sell_lines.tax_id', '=', 'tr.id')
                ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final')
                ->whereNull('parent_sell_line_id')
                ->select(
                    'c.name as customer',
                    'c.supplier_business_name',
                    'c.contact_id',
                    'c.tax_number',
                    'cat.short_code',
                    't.id as transaction_id',
                    't.invoice_no',
                    't.transaction_date as transaction_date',
                    'transaction_sell_lines.unit_price_before_discount as unit_price',
                    'transaction_sell_lines.unit_price as unit_price_after_discount',
                    DB::raw('(transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) as sell_qty'),
                    'transaction_sell_lines.line_discount_type as discount_type',
                    'transaction_sell_lines.line_discount_amount as discount_amount',
                    'transaction_sell_lines.item_tax',
                    'tr.amount as tax_percent',
                    'tr.is_tax_group',
                    'transaction_sell_lines.tax_id',
                    'u.short_name as unit',
                    'transaction_sell_lines.parent_sell_line_id',
                    DB::raw('((transaction_sell_lines.quantity- transaction_sell_lines.quantity_returned) * transaction_sell_lines.unit_price_inc_tax) as line_total'),
                )
                ->groupBy('transaction_sell_lines.id');

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (! empty($start_date) && ! empty($end_date)) {
                $query->whereDate('t.transaction_date', '>=', $start_date)
                    ->whereDate('t.transaction_date', '<=', $end_date);
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            $location_id = $request->get('location_id', null);
            if (! empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }

            $customer_id = $request->get('customer_id', null);
            if (! empty($customer_id)) {
                $query->where('t.contact_id', $customer_id);
            }

            $datatable = Datatables::of($query);

            $raw_cols = ['invoice_no', 'taxable_value', 'discount_amount', 'unit_price', 'tax', 'customer', 'line_total'];
            $group_taxes_array = TaxRate::groupTaxes($business_id);
            $group_taxes = [];
            foreach ($group_taxes_array as $group_tax) {
                foreach ($group_tax['sub_taxes'] as $sub_tax) {
                    $group_taxes[$group_tax->id]['sub_taxes'][$sub_tax->id] = $sub_tax;
                }
            }
            foreach ($taxes as $tax) {
                $col = 'tax_'.$tax['id'];
                $raw_cols[] = $col;
                $datatable->addColumn($col, function ($row) use ($tax, $col, $group_taxes) {
                    $sub_tax_share = 0;
                    if ($row->is_tax_group == 1 && array_key_exists($tax['id'], $group_taxes[$row->tax_id]['sub_taxes'])) {
                        $sub_tax_share = $this->transactionUtil->calc_percentage($row->unit_price_after_discount, $group_taxes[$row->tax_id]['sub_taxes'][$tax['id']]->amount) * $row->sell_qty;
                    }

                    if ($sub_tax_share > 0) {
                        //ignore child sell line of combo product
                        $class = is_null($row->parent_sell_line_id) ? $col : '';

                        return '<span class="'.$class.'" data-orig-value="'.$sub_tax_share.'">'.$this->transactionUtil->num_f($sub_tax_share).'</span>';
                    } else {
                        return '';
                    }
                });
            }

            return $datatable->addColumn('taxable_value', function ($row) {
                $taxable_value = $row->unit_price_after_discount * $row->sell_qty;
                //ignore child sell line of combo product
                $class = is_null($row->parent_sell_line_id) ? 'taxable_value' : '';

                return '<span class="'.$class.'"data-orig-value="'.$taxable_value.'">'.$this->transactionUtil->num_f($taxable_value).'</span>';
            })
                 ->editColumn('invoice_no', function ($row) {
                     return '<a data-href="'.action([\App\Http\Controllers\SellController::class, 'show'], [$row->transaction_id])
                            .'" href="#" data-container=".view_modal" class="btn-modal">'.$row->invoice_no.'</a>';
                 })
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn('sell_qty', function ($row) {
                    return $this->transactionUtil->num_f($row->sell_qty, false, null, true).' '.$row->unit;
                })
                ->editColumn('unit_price', function ($row) {
                    return '<span data-orig-value="'.$row->unit_price.'">'.$this->transactionUtil->num_f($row->unit_price).'</span>';
                })
                ->editColumn('line_total', function ($row) {
                    return '<span data-orig-value="'.$row->line_total.'">'.$this->transactionUtil->num_f($row->line_total).'</span>';
                })
                ->editColumn(
                    'discount_amount',
                    function ($row) {
                        $discount = ! empty($row->discount_amount) ? $row->discount_amount : 0;

                        if (! empty($discount) && $row->discount_type == 'percentage') {
                            $discount = $row->unit_price * ($discount / 100);
                        }

                        return $this->transactionUtil->num_f($discount);
                    }
                )
                ->editColumn('tax_percent', '@if(!empty($tax_percent)){{@num_format($tax_percent)}}% @endif
                    ')
                ->editColumn('customer', '@if(!empty($supplier_business_name)) {{$supplier_business_name}},<br>@endif {{$customer}}')
                ->rawColumns($raw_cols)
                ->make(true);
        }

        $customers = Contact::customersDropdown($business_id);

        return view('report.gst_sales_report')->with(compact('customers', 'taxes'));
    }

    public function gstPurchaseReport(Request $request)
    {
        if (! auth()->user()->can('tax_report.view') || empty(config('constants.enable_gst_report_india'))) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $taxes = TaxRate::where('business_id', $business_id)
                        ->where('is_tax_group', 0)
                        ->select(['id', 'name', 'amount'])
                        ->get()
                        ->toArray();

        if ($request->ajax()) {
            $query = PurchaseLine::join(
                'transactions as t',
                'purchase_lines.transaction_id',
                '=',
                't.id'
            )
                ->join('contacts as c', 't.contact_id', '=', 'c.id')
                ->join('products as p', 'purchase_lines.product_id', '=', 'p.id')
                ->leftjoin('categories as cat', 'p.category_id', '=', 'cat.id')
                ->leftjoin('tax_rates as tr', 'purchase_lines.tax_id', '=', 'tr.id')
                ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'purchase')
                ->where('t.status', 'received')
                ->select(
                    'c.name as supplier',
                    'c.supplier_business_name',
                    'c.contact_id',
                    'c.tax_number',
                    'cat.short_code',
                    't.id as transaction_id',
                    't.ref_no',
                    't.transaction_date as transaction_date',
                    'purchase_lines.pp_without_discount as unit_price',
                    'purchase_lines.purchase_price as unit_price_after_discount',
                    DB::raw('(purchase_lines.quantity - purchase_lines.quantity_returned) as purchase_qty'),
                    'purchase_lines.discount_percent',
                    'purchase_lines.item_tax',
                    'tr.amount as tax_percent',
                    'tr.is_tax_group',
                    'purchase_lines.tax_id',
                    'u.short_name as unit',
                    DB::raw('((purchase_lines.quantity- purchase_lines.quantity_returned) * purchase_lines.purchase_price_inc_tax) as line_total')
                )
                ->groupBy('purchase_lines.id');

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (! empty($start_date) && ! empty($end_date)) {
                $query->where('t.transaction_date', '>=', $start_date)
                    ->where('t.transaction_date', '<=', $end_date);
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            $location_id = $request->get('location_id', null);
            if (! empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }

            $supplier_id = $request->get('supplier_id', null);
            if (! empty($supplier_id)) {
                $query->where('t.contact_id', $supplier_id);
            }

            $datatable = Datatables::of($query);

            $raw_cols = ['ref_no', 'taxable_value', 'discount_amount', 'unit_price', 'tax', 'supplier', 'line_total'];
            $group_taxes_array = TaxRate::groupTaxes($business_id);
            $group_taxes = [];
            foreach ($group_taxes_array as $group_tax) {
                foreach ($group_tax['sub_taxes'] as $sub_tax) {
                    $group_taxes[$group_tax->id]['sub_taxes'][$sub_tax->id] = $sub_tax;
                }
            }
            foreach ($taxes as $tax) {
                $col = 'tax_'.$tax['id'];
                $raw_cols[] = $col;
                $datatable->addColumn($col, function ($row) use ($tax, $group_taxes) {
                    $sub_tax_share = 0;
                    if ($row->is_tax_group == 1 && array_key_exists($tax['id'], $group_taxes[$row->tax_id]['sub_taxes'])) {
                        $sub_tax_share = $this->transactionUtil->calc_percentage($row->unit_price_after_discount, $group_taxes[$row->tax_id]['sub_taxes'][$tax['id']]->amount) * $row->purchase_qty;
                    }

                    if ($sub_tax_share > 0) {
                        return '<span data-orig-value="'.$sub_tax_share.'">'.$this->transactionUtil->num_f($sub_tax_share).'</span>';
                    } else {
                        return '';
                    }
                });
            }

            return $datatable->addColumn('taxable_value', function ($row) {
                $taxable_value = $row->unit_price_after_discount * $row->purchase_qty;

                return '<span class="taxable_value"data-orig-value="'.$taxable_value.'">'.$this->transactionUtil->num_f($taxable_value).'</span>';
            })
                 ->editColumn('ref_no', function ($row) {
                     return '<a data-href="'.action([\App\Http\Controllers\PurchaseController::class, 'show'], [$row->transaction_id])
                            .'" href="#" data-container=".view_modal" class="btn-modal">'.$row->ref_no.'</a>';
                 })
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->editColumn('purchase_qty', function ($row) {
                    return $this->transactionUtil->num_f($row->purchase_qty, false, null, true).' '.$row->unit;
                })
                ->editColumn('unit_price', function ($row) {
                    return '<span data-orig-value="'.$row->unit_price.'">'.$this->transactionUtil->num_f($row->unit_price).'</span>';
                })
                ->editColumn('line_total', function ($row) {
                    return '<span data-orig-value="'.$row->line_total.'">'.$this->transactionUtil->num_f($row->line_total).'</span>';
                })
                ->addColumn(
                    'discount_amount',
                    function ($row) {
                        $discount = ! empty($row->discount_percent) ? $row->discount_percent : 0;

                        if (! empty($discount)) {
                            $discount = $row->unit_price * ($discount / 100);
                        }

                        return $this->transactionUtil->num_f($discount);
                    }
                )
                ->editColumn('tax_percent', '@if(!empty($tax_percent)){{@num_format($tax_percent)}}% @endif
                    ')
                ->editColumn('supplier', '@if(!empty($supplier_business_name)) {{$supplier_business_name}},<br>@endif {{$supplier}}')
                ->rawColumns($raw_cols)
                ->make(true);
        }

        $suppliers = Contact::suppliersDropdown($business_id);

        return view('report.gst_purchase_report')->with(compact('suppliers', 'taxes'));
    }

    /**
     * Display route coverage report
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getRouteCoverageReport(Request $request)
    {
        if (!auth()->user()->can('route_coverage_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        // Get all routes
        $routes = CustomerRoute::forDropdown($business_id, false);

        $report_data = null;

        if ($request->ajax() && !empty($request->input('route_id')) && !empty($request->input('date'))) {
            $route_id = $request->input('route_id');
            $date = $request->input('date');

            // Get route coverage report
            $report_data = $this->geofenceUtil->getRouteCoverageReport($business_id, $route_id, null, $date);

            return view('report.partials.route_coverage_details')->with(compact('report_data'));
        }

        return view('report.route_coverage_report')->with(compact('routes', 'report_data'));
    }
    /**
     * Display route followup report
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getRouteFollowupReport(Request $request)
    {
        if (!auth()->user()->can('route_followup_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        // Get all routes
        $routes = CustomerRoute::forDropdown($business_id, false, false);

        // Set default dates if not provided
        $start_date = $request->input('start_date', \Carbon\Carbon::now()->startOfMonth()->format('m/d/Y'));
        $end_date = $request->input('end_date', \Carbon\Carbon::now()->endOfMonth()->format('m/d/Y'));
        $route_ids = $request->input('route_ids', []);

        $followed_customers = [];
        $not_followed_customers = [];
        $stats = [];

        if ($request->ajax() || (!empty($start_date) && !empty($end_date))) {
            try {
                $start_date = $this->transactionUtil->uf_date($start_date);
                $end_date = $this->transactionUtil->uf_date($end_date);
            } catch (\Exception $e) {
                $start_date = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
                $end_date = \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');
            }

            // Get customers who have been followed up
            $query = RouteFollowup::where('route_followups.business_id', $business_id)
                ->whereBetween('route_followups.followup_date', [$start_date, $end_date])
                ->join('contacts', 'route_followups.contact_id', '=', 'contacts.id')
                ->join('customer_routes', 'route_followups.customer_route_id', '=', 'customer_routes.id')
                ->select(
                    'contacts.id as contact_id',
                    'contacts.name as contact_name',
                    'contacts.supplier_business_name',
                    'contacts.mobile',
                    'contacts.address_line_1',
                    'contacts.address_line_2',
                    'contacts.city',
                    'contacts.state',
                    'contacts.country',
                    'customer_routes.id as route_id',
                    'customer_routes.name as route_name',
                    'route_followups.notes',
                    'route_followups.followup_date'
                );

            if (!empty($route_ids)) {
                $query->whereIn('route_followups.customer_route_id', $route_ids);
            }

            $followed_customers = $query->get();

            // Get customers who have not been followed up
            $not_followed_query = Contact::where('contacts.business_id', $business_id)
                ->where('contacts.type', 'customer')
                ->where('contacts.customer_route_id', '!=', null)
                ->leftJoin('route_followups', function ($join) use ($start_date, $end_date) {
                    $join->on('contacts.id', '=', 'route_followups.contact_id')
                        ->whereBetween('route_followups.followup_date', [$start_date, $end_date]);
                })
                ->whereNull('route_followups.id')
                ->join('customer_routes', 'contacts.customer_route_id', '=', 'customer_routes.id')
                ->select(
                    'contacts.id as contact_id',
                    'contacts.name as contact_name',
                    'contacts.supplier_business_name',
                    'contacts.mobile',
                    'contacts.address_line_1',
                    'contacts.address_line_2',
                    'contacts.city',
                    'contacts.state',
                    'contacts.country',
                    'customer_routes.id as route_id',
                    'customer_routes.name as route_name'
                );

            if (!empty($route_ids)) {
                $not_followed_query->whereIn('contacts.customer_route_id', $route_ids);
            }

            $not_followed_customers = $not_followed_query->get();

            // Calculate statistics by route
            $route_stats = [];

            // If specific routes are selected, only calculate stats for those routes
            $route_query = CustomerRoute::where('business_id', $business_id);
            if (!empty($route_ids)) {
                $route_query->whereIn('id', $route_ids);
            }
            $all_routes = $route_query->get();

            foreach ($all_routes as $route) {
                // Count total assigned customers for this route
                $total_assigned = Contact::where('business_id', $business_id)
                    ->where('type', 'customer')
                    ->where('customer_route_id', $route->id)
                    ->count();

                // Count followed customers for this route
                $followed = $followed_customers->where('route_id', $route->id)->count();

                // Calculate not followed
                $not_followed = $total_assigned - $followed;

                // Calculate coverage percentage
                $coverage_percentage = $total_assigned > 0 ? round(($followed / $total_assigned) * 100, 2) : 0;

                $route_stats[$route->id] = [
                    'route_name' => $route->name,
                    'total_assigned' => $total_assigned,
                    'followed' => $followed,
                    'not_followed' => $not_followed,
                    'coverage_percentage' => $coverage_percentage
                ];
            }

            // Calculate overall statistics
            $total_assigned = array_sum(array_column($route_stats, 'total_assigned'));
            $total_followed = array_sum(array_column($route_stats, 'followed'));
            $total_not_followed = array_sum(array_column($route_stats, 'not_followed'));
            $overall_coverage = $total_assigned > 0 ? round(($total_followed / $total_assigned) * 100, 2) : 0;

            $stats = [
                'total_assigned' => $total_assigned,
                'followed' => $total_followed,
                'not_followed' => $total_not_followed,
                'coverage_percentage' => $overall_coverage,
                'route_stats' => $route_stats
            ];

            if ($request->ajax()) {
                return view('report.partials.route_followup_details')
                    ->with(compact('followed_customers', 'not_followed_customers', 'stats', 'start_date', 'end_date'));
            }
        }

        // If not AJAX but dates are provided, render the full page with data
        if (!empty($start_date) && !empty($end_date)) {
            return view('report.route_followup_report')
                ->with(compact('routes', 'followed_customers', 'not_followed_customers', 'stats'));
        }

        // Default view with just the form
        return view('report.route_followup_report')
            ->with(compact('routes', 'start_date', 'end_date'));
    }
    /**
     * Shows customer advance analytics report
     *
     * @return \Illuminate\Http\Response
     */
    public function getCustomerAdvanceAnalytics(Request $request)
    {
        if (! auth()->user()->can('profit_loss_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $fy = $this->businessUtil->getCurrentFinancialYear($business_id);

            $location_id = ! empty($request->input('location_id')) ? $request->input('location_id') : null;
            $start_date = ! empty($request->input('start_date')) ? $request->input('start_date') : $fy['start'];
            $end_date = ! empty($request->input('end_date')) ? $request->input('end_date') : $fy['end'];

            // If no customers are selected, consider all customers
            $customer_ids = $request->input('customer_ids');
            if ($customer_ids === null || $customer_ids === '' || (is_array($customer_ids) && count($customer_ids) === 0)) {
                $customer_ids = [];
            }

            // Create a cache key based on the parameters
            $customer_ids_key = is_array($customer_ids) ? implode('_', $customer_ids) : 'all';
            $cache_key = "customer_analytics_{$business_id}_{$location_id}_{$start_date}_{$end_date}_{$customer_ids_key}";

            // Get data from cache or compute it if not cached (1 hour cache)
            return \Cache::remember($cache_key, 60 * 60, function () use ($request, $business_id, $location_id, $start_date, $end_date, $customer_ids) {

            $permitted_locations = auth()->user()->permitted_locations();

            // Base query for transactions
            $base_query = Transaction::where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final');

            if (!empty($start_date) && !empty($end_date)) {
                $base_query->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $base_query->where('transactions.location_id', $location_id);
            }

            if (!empty($customer_ids)) {
                $base_query->whereIn('transactions.contact_id', $customer_ids);
            }

            if ($permitted_locations != 'all') {
                $base_query->whereIn('transactions.location_id', $permitted_locations);
            }

            // Sales Trend Analytics - clone the base query to avoid modifying it
            $sales_trends_query = clone $base_query;
            $sales_trends = $sales_trends_query->select(
                DB::raw('DATE(transaction_date) as date'),
                DB::raw('SUM(final_total) as total_sales')
            )
            ->groupBy(DB::raw('DATE(transaction_date)'))
            ->orderBy(DB::raw('DATE(transaction_date)'))
            ->get();

            // Calculate moving average for sales trends (7-day moving average)
            $sales_trends_array = $sales_trends->toArray();
            $moving_avg_sales = [];
            foreach ($sales_trends_array as $index => $trend) {
                $sum = 0;
                $count = 0;
                for ($i = max(0, $index - 3); $i <= min(count($sales_trends_array) - 1, $index + 3); $i++) {
                    $sum += $sales_trends_array[$i]['total_sales'];
                    $count++;
                }
                $moving_avg_sales[] = [
                    'date' => $trend['date'],
                    'moving_avg' => $count > 0 ? $sum / $count : 0
                ];
            }

            // Monthly sales - clone the base query again
            $monthly_sales_query = clone $base_query;
            $monthly_sales = $monthly_sales_query->select(
                DB::raw('MONTH(transaction_date) as month'),
                DB::raw('YEAR(transaction_date) as year'),
                DB::raw('SUM(final_total) as total_sales')
            )
            ->groupBy(DB::raw('MONTH(transaction_date)'), DB::raw('YEAR(transaction_date)'))
            ->orderBy(DB::raw('YEAR(transaction_date)'))
            ->orderBy(DB::raw('MONTH(transaction_date)'))
            ->get();

            // Calculate year-over-year growth for monthly sales
            $monthly_sales_by_year = [];
            foreach ($monthly_sales as $sale) {
                $year = $sale->year;
                $month = $sale->month;
                if (!isset($monthly_sales_by_year[$year])) {
                    $monthly_sales_by_year[$year] = [];
                }
                $monthly_sales_by_year[$year][$month] = $sale->total_sales;
            }

            $yoy_growth = [];
            foreach ($monthly_sales_by_year as $year => $months) {
                foreach ($months as $month => $sales) {
                    if (isset($monthly_sales_by_year[$year-1][$month])) {
                        $prev_year_sales = $monthly_sales_by_year[$year-1][$month];
                        $growth_rate = $prev_year_sales > 0 ? (($sales - $prev_year_sales) / $prev_year_sales) * 100 : 0;
                        $yoy_growth[] = [
                            'year' => $year,
                            'month' => $month,
                            'growth_rate' => $growth_rate
                        ];
                    }
                }
            }

            // Calculate quarterly sales
            $quarterly_sales = [];
            foreach ($monthly_sales as $sale) {
                $year = $sale->year;
                $month = $sale->month;
                $quarter = ceil($month / 3);
                $key = $year . '-Q' . $quarter;

                if (!isset($quarterly_sales[$key])) {
                    $quarterly_sales[$key] = [
                        'year' => $year,
                        'quarter' => $quarter,
                        'total_sales' => 0
                    ];
                }

                $quarterly_sales[$key]['total_sales'] += $sale->total_sales;
            }
            $quarterly_sales = array_values($quarterly_sales);

            // Product Mix & Performance
            $product_performance_query = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final');

            if (!empty($start_date) && !empty($end_date)) {
                $product_performance_query->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $product_performance_query->where('transactions.location_id', $location_id);
            }

            if (!empty($customer_ids)) {
                $product_performance_query->whereIn('transactions.contact_id', $customer_ids);
            }

            if ($permitted_locations != 'all') {
                $product_performance_query->whereIn('transactions.location_id', $permitted_locations);
            }

            $product_performance = $product_performance_query->select(
                'products.name as product_name',
                DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity'),
                DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) as total_amount')
            )
            ->groupBy('products.id')
            ->orderBy('total_amount', 'desc')
            ->limit(10)
            ->get();

            // Product Sales Trend Over Time
            $product_trend_query = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final');

            if (!empty($start_date) && !empty($end_date)) {
                $product_trend_query->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $product_trend_query->where('transactions.location_id', $location_id);
            }

            if (!empty($customer_ids)) {
                $product_trend_query->whereIn('transactions.contact_id', $customer_ids);
            }

            if ($permitted_locations != 'all') {
                $product_trend_query->whereIn('transactions.location_id', $permitted_locations);
            }

            // Get top 5 products
            $top_products_query = clone $product_trend_query;
            $top_products = $top_products_query->select(
                'products.id',
                'products.name'
            )
            ->groupBy('products.id')
            ->orderBy(DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax)'), 'desc')
            ->limit(5)
            ->pluck('name', 'id');

            // Get monthly sales for top 5 products
            $product_monthly_sales = [];
            foreach ($top_products as $product_id => $product_name) {
                $product_sales_query = clone $product_trend_query;
                $product_sales = $product_sales_query->select(
                    DB::raw('DATE_FORMAT(transactions.transaction_date, "%Y-%m") as month'),
                    DB::raw('SUM(transaction_sell_lines.quantity) as quantity'),
                    DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) as amount')
                )
                ->where('products.id', $product_id)
                ->groupBy(DB::raw('DATE_FORMAT(transactions.transaction_date, "%Y-%m")'))
                ->orderBy(DB::raw('DATE_FORMAT(transactions.transaction_date, "%Y-%m")'))
                ->get();

                $product_monthly_sales[$product_id] = [
                    'name' => $product_name,
                    'sales' => $product_sales
                ];
            }

            // Product Category Performance
            $category_performance_query = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final');

            if (!empty($start_date) && !empty($end_date)) {
                $category_performance_query->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $category_performance_query->where('transactions.location_id', $location_id);
            }

            if (!empty($customer_ids)) {
                $category_performance_query->whereIn('transactions.contact_id', $customer_ids);
            }

            if ($permitted_locations != 'all') {
                $category_performance_query->whereIn('transactions.location_id', $permitted_locations);
            }

            $category_performance = $category_performance_query->select(
                'categories.name as category_name',
                DB::raw('COALESCE(categories.name, "Uncategorized") as category_name'),
                DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity'),
                DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) as total_amount'),
                DB::raw('COUNT(DISTINCT products.id) as product_count')
            )
            ->groupBy('categories.id')
            ->orderBy('total_amount', 'desc')
            ->get();

            // Customer Behavior (RFM Analysis)
            $customers = Contact::leftJoin('transactions as t', function ($join) use ($start_date, $end_date) {
                $join->on('contacts.id', '=', 't.contact_id')
                    ->where('t.type', 'sell')
                    ->where('t.status', 'final');

                if (!empty($start_date) && !empty($end_date)) {
                    $join->whereBetween(DB::raw('date(t.transaction_date)'), [$start_date, $end_date]);
                }
            })
            ->where('contacts.business_id', $business_id)
            ->whereIn('contacts.type', ['customer', 'both']);

            if (!empty($location_id)) {
                $customers->where('t.location_id', $location_id);
            }

            if (!empty($customer_ids)) {
                $customers->whereIn('contacts.id', $customer_ids);
            }

            if ($permitted_locations != 'all') {
                $customers->whereIn('t.location_id', $permitted_locations);
            }

            $customers = $customers->select(
                'contacts.id',
                'contacts.name',
                DB::raw('MAX(t.transaction_date) as last_purchase_date'),
                DB::raw('COUNT(t.id) as frequency'),
                DB::raw('SUM(t.final_total) as total_spent')
            )
            ->groupBy('contacts.id')
            ->orderBy('last_purchase_date', 'desc')
            ->limit(20)
            ->get();

            // Customer Retention Analysis
            // First, get the total number of customers who made a purchase in each month
            $total_customers_by_month_query = clone $base_query;
            $total_customers_by_month = $total_customers_by_month_query->select(
                DB::raw('DATE_FORMAT(transaction_date, "%Y-%m") as month'),
                DB::raw('COUNT(DISTINCT contact_id) as total_customers')
            )
            ->groupBy(DB::raw('DATE_FORMAT(transaction_date, "%Y-%m")'))
            ->orderBy(DB::raw('DATE_FORMAT(transaction_date, "%Y-%m")'))
            ->get()
            ->keyBy('month');

            // Then, get the number of returning customers in each month
            // Create a fresh query for returning customers to avoid table alias issues
            $returning_customers_by_month = DB::table('transactions as t1')
            ->select(
                DB::raw('DATE_FORMAT(t1.transaction_date, "%Y-%m") as month'),
                DB::raw('COUNT(DISTINCT t1.contact_id) as returning_customers')
            )
            ->join(DB::raw('(SELECT t2.contact_id, MIN(DATE_FORMAT(t2.transaction_date, "%Y-%m")) as first_month FROM transactions as t2 
                WHERE t2.business_id = ' . $business_id . ' AND t2.type = "sell" AND t2.status = "final" 
                GROUP BY t2.contact_id) as t2'), function($join) {
                $join->on('t1.contact_id', '=', 't2.contact_id')
                    ->whereRaw('DATE_FORMAT(t1.transaction_date, "%Y-%m") > t2.first_month');
            })
            ->where('t1.business_id', $business_id)
            ->where('t1.type', 'sell')
            ->where('t1.status', 'final');

            // Add the same conditions that were in the base query
            if (!empty($start_date) && !empty($end_date)) {
                $returning_customers_by_month->whereBetween(DB::raw('date(t1.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $returning_customers_by_month->where('t1.location_id', $location_id);
            }

            if (!empty($customer_ids)) {
                $returning_customers_by_month->whereIn('t1.contact_id', $customer_ids);
            }

            if ($permitted_locations != 'all') {
                $returning_customers_by_month->whereIn('t1.location_id', $permitted_locations);
            }

            $returning_customers_by_month = $returning_customers_by_month
            ->groupBy(DB::raw('DATE_FORMAT(t1.transaction_date, "%Y-%m")'))
            ->orderBy(DB::raw('DATE_FORMAT(t1.transaction_date, "%Y-%m")'))
            ->get()
            ->keyBy('month');

            // Calculate retention rate for each month
            $retention_rate = [];
            foreach ($total_customers_by_month as $month => $data) {
                $returning = isset($returning_customers_by_month[$month]) ? $returning_customers_by_month[$month]->returning_customers : 0;
                $total = $data->total_customers;
                $rate = $total > 0 ? ($returning / $total) * 100 : 0;

                $retention_rate[] = [
                    'month' => $month,
                    'total_customers' => $total,
                    'returning_customers' => $returning,
                    'retention_rate' => $rate
                ];
            }

            // Customer Lifetime Value Trend
            // Get average CLV by month of first purchase
            $clv_trend_query = Contact::leftJoin('transactions as t', function ($join) {
                $join->on('contacts.id', '=', 't.contact_id')
                    ->where('t.type', 'sell')
                    ->where('t.status', 'final');
            })
            ->where('contacts.business_id', $business_id)
            ->whereIn('contacts.type', ['customer', 'both']);

            if (!empty($location_id)) {
                $clv_trend_query->where('t.location_id', $location_id);
            }

            if (!empty($customer_ids)) {
                $clv_trend_query->whereIn('contacts.id', $customer_ids);
            }

            if ($permitted_locations != 'all') {
                $clv_trend_query->whereIn('t.location_id', $permitted_locations);
            }

            // First, get the minimum transaction date for each customer
            $min_dates_query = clone $clv_trend_query;
            $min_dates = $min_dates_query->select(
                'contacts.id',
                DB::raw('MIN(t.transaction_date) as min_date')
            )
            ->groupBy('contacts.id')
            ->get()
            ->keyBy('id');

            // Then, get the total spent for each customer
            $customer_totals = $clv_trend_query->select(
                'contacts.id',
                DB::raw('SUM(t.final_total) as total_spent')
            )
            ->groupBy('contacts.id')
            ->get();

            // Combine the results
            foreach ($customer_totals as $customer) {
                if (isset($min_dates[$customer->id])) {
                    $min_date = $min_dates[$customer->id]->min_date;
                    $customer->first_purchase_month = date('Y-m', strtotime($min_date));
                } else {
                    $customer->first_purchase_month = null;
                }
            }

            // Then, calculate the average CLV by month
            $clv_by_month = [];
            foreach ($customer_totals as $customer) {
                $month = $customer->first_purchase_month;
                if (!isset($clv_by_month[$month])) {
                    $clv_by_month[$month] = [
                        'total' => 0,
                        'count' => 0
                    ];
                }
                $clv_by_month[$month]['total'] += $customer->total_spent;
                $clv_by_month[$month]['count']++;
            }

            // Convert to collection format expected by the view
            $clv_trend = collect();
            foreach ($clv_by_month as $month => $data) {
                $avg_clv = $data['count'] > 0 ? $data['total'] / $data['count'] : 0;
                $clv_trend->push((object)[
                    'first_purchase_month' => $month,
                    'avg_clv' => $avg_clv
                ]);
            }

            // Sort by month
            $clv_trend = $clv_trend->sortBy('first_purchase_month')->values();

            // Cross-sell & Basket Analysis
            $cross_sell_products = DB::table('transaction_sell_lines as tsl1')
                ->join('transaction_sell_lines as tsl2', function ($join) {
                    $join->on('tsl1.transaction_id', '=', 'tsl2.transaction_id')
                        ->where('tsl1.product_id', '!=', 'tsl2.product_id');
                })
                ->join('transactions as t', 't.id', '=', 'tsl1.transaction_id')
                ->join('products as p1', 'p1.id', '=', 'tsl1.product_id')
                ->join('products as p2', 'p2.id', '=', 'tsl2.product_id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final');

            if (!empty($start_date) && !empty($end_date)) {
                $cross_sell_products->whereBetween(DB::raw('date(t.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $cross_sell_products->where('t.location_id', $location_id);
            }

            if (!empty($customer_ids)) {
                $cross_sell_products->whereIn('t.contact_id', $customer_ids);
            }

            if ($permitted_locations != 'all') {
                $cross_sell_products->whereIn('t.location_id', $permitted_locations);
            }

            $cross_sell_products = $cross_sell_products->select(
                'p1.name as product_1',
                'p2.name as product_2',
                DB::raw('COUNT(*) as frequency')
            )
            ->groupBy('p1.id', 'p2.id')
            ->orderBy('frequency', 'desc')
            ->limit(10)
            ->get();

            // Payment Behavior
            $payment_behavior = TransactionPayment::join('transactions as t', 't.id', '=', 'transaction_payments.transaction_id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final');

            if (!empty($start_date) && !empty($end_date)) {
                $payment_behavior->whereBetween(DB::raw('date(t.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $payment_behavior->where('t.location_id', $location_id);
            }

            if (!empty($customer_ids)) {
                $payment_behavior->whereIn('t.contact_id', $customer_ids);
            }

            if ($permitted_locations != 'all') {
                $payment_behavior->whereIn('t.location_id', $permitted_locations);
            }

            $payment_behavior = $payment_behavior->select(
                'transaction_payments.method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(transaction_payments.amount) as total_amount')
            )
            ->groupBy('transaction_payments.method')
            ->orderBy('total_amount', 'desc')
            ->get();

            // Payment Method Trends Over Time
            $payment_trends_query = TransactionPayment::join('transactions as t', 't.id', '=', 'transaction_payments.transaction_id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final');

            if (!empty($start_date) && !empty($end_date)) {
                $payment_trends_query->whereBetween(DB::raw('date(t.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $payment_trends_query->where('t.location_id', $location_id);
            }

            if (!empty($customer_ids)) {
                $payment_trends_query->whereIn('t.contact_id', $customer_ids);
            }

            if ($permitted_locations != 'all') {
                $payment_trends_query->whereIn('t.location_id', $permitted_locations);
            }

            $payment_trends = $payment_trends_query->select(
                'transaction_payments.method',
                DB::raw('DATE_FORMAT(t.transaction_date, "%Y-%m") as month'),
                DB::raw('SUM(transaction_payments.amount) as amount')
            )
            ->groupBy('transaction_payments.method', DB::raw('DATE_FORMAT(t.transaction_date, "%Y-%m")'))
            ->orderBy(DB::raw('DATE_FORMAT(t.transaction_date, "%Y-%m")'))
            ->get();

            // Organize payment trends by method and month
            $payment_methods = $payment_trends->pluck('method')->unique();
            $payment_months = $payment_trends->pluck('month')->unique()->sort();

            $payment_trends_data = [];
            foreach ($payment_methods as $method) {
                $payment_trends_data[$method] = [];
                foreach ($payment_months as $month) {
                    $payment_trends_data[$method][$month] = 0;
                }
            }

            foreach ($payment_trends as $trend) {
                $payment_trends_data[$trend->method][$trend->month] = $trend->amount;
            }

            // Price & Discount Analytics
            $discount_analytics_query = Transaction::where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final');

            if (!empty($start_date) && !empty($end_date)) {
                $discount_analytics_query->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $discount_analytics_query->where('transactions.location_id', $location_id);
            }

            if (!empty($customer_ids)) {
                $discount_analytics_query->whereIn('transactions.contact_id', $customer_ids);
            }

            if ($permitted_locations != 'all') {
                $discount_analytics_query->whereIn('transactions.location_id', $permitted_locations);
            }

            $discount_analytics = $discount_analytics_query->select(
                'transactions.contact_id',
                'contacts.name as customer_name',
                DB::raw('SUM(discount_amount) as total_discount'),
                DB::raw('SUM(final_total) as total_sales'),
                DB::raw('CASE WHEN SUM(final_total + discount_amount) > 0 THEN (SUM(discount_amount) / SUM(final_total + discount_amount)) * 100 ELSE 0 END as discount_percentage')
            )
            ->join('contacts', 'transactions.contact_id', '=', 'contacts.id')
            ->groupBy('transactions.contact_id')
            ->orderBy('total_discount', 'desc')
            ->limit(10)
            ->get();

            // Discount Trends Over Time
            $discount_trends_query = Transaction::where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final')
                ->where('transactions.discount_amount', '>', 0);

            if (!empty($start_date) && !empty($end_date)) {
                $discount_trends_query->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $discount_trends_query->where('transactions.location_id', $location_id);
            }

            if (!empty($customer_ids)) {
                $discount_trends_query->whereIn('transactions.contact_id', $customer_ids);
            }

            if ($permitted_locations != 'all') {
                $discount_trends_query->whereIn('transactions.location_id', $permitted_locations);
            }

            $discount_trends = $discount_trends_query->select(
                DB::raw('DATE_FORMAT(transactions.transaction_date, "%Y-%m") as month'),
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(discount_amount) as total_discount'),
                DB::raw('SUM(final_total) as total_sales'),
                DB::raw('CASE WHEN SUM(final_total + discount_amount) > 0 THEN (SUM(discount_amount) / SUM(final_total + discount_amount)) * 100 ELSE 0 END as discount_percentage')
            )
            ->groupBy(DB::raw('DATE_FORMAT(transactions.transaction_date, "%Y-%m")'))
            ->orderBy(DB::raw('DATE_FORMAT(transactions.transaction_date, "%Y-%m")'))
            ->get();

            // Customer Lifetime Value (CLV) Analysis
            $clv_analysis = Contact::leftJoin('transactions as t', function ($join) use ($start_date, $end_date) {
                $join->on('contacts.id', '=', 't.contact_id')
                    ->where('t.type', 'sell')
                    ->where('t.status', 'final');

                if (!empty($start_date) && !empty($end_date)) {
                    $join->whereBetween(DB::raw('date(t.transaction_date)'), [$start_date, $end_date]);
                }
            })
            ->where('contacts.business_id', $business_id)
            ->whereIn('contacts.type', ['customer', 'both']);

            if (!empty($location_id)) {
                $clv_analysis->where('t.location_id', $location_id);
            }

            if (!empty($customer_ids)) {
                $clv_analysis->whereIn('contacts.id', $customer_ids);
            }

            if ($permitted_locations != 'all') {
                $clv_analysis->whereIn('t.location_id', $permitted_locations);
            }

            $clv_analysis = $clv_analysis->select(
                'contacts.id',
                'contacts.name',
                DB::raw('SUM(t.final_total) as total_spent'),
                DB::raw('COUNT(t.id) as transaction_count'),
                DB::raw('MIN(t.transaction_date) as first_purchase_date'),
                DB::raw('MAX(t.transaction_date) as last_purchase_date'),
                DB::raw('CASE WHEN COUNT(t.id) > 0 THEN SUM(t.final_total) / COUNT(t.id) ELSE 0 END as average_purchase_value')
            )
            ->groupBy('contacts.id')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();

            // Calculate CLV metrics
            foreach ($clv_analysis as $customer) {
                if ($customer->first_purchase_date && $customer->last_purchase_date) {
                    $first_date = \Carbon\Carbon::parse($customer->first_purchase_date);
                    $last_date = \Carbon\Carbon::parse($customer->last_purchase_date);
                    $days_as_customer = $first_date->diffInDays($last_date) + 1; // Add 1 to include the first day

                    $customer->customer_age_days = $days_as_customer;
                    $customer->purchase_frequency = $days_as_customer > 0 ? $customer->transaction_count / ($days_as_customer / 30) : 0; // Purchases per month
                    $customer->clv = $customer->average_purchase_value * $customer->purchase_frequency * 12; // Yearly CLV
                } else {
                    $customer->customer_age_days = 0;
                    $customer->purchase_frequency = 0;
                    $customer->clv = 0;
                }
            }

            // Customer Segmentation by Purchase Frequency
            // First, get the count of transactions for each customer
            $customer_transaction_counts = DB::table('contacts')
                ->leftJoin('transactions as t', function ($join) use ($start_date, $end_date) {
                    $join->on('contacts.id', '=', 't.contact_id')
                        ->where('t.type', 'sell')
                        ->where('t.status', 'final');

                    if (!empty($start_date) && !empty($end_date)) {
                        $join->whereBetween(DB::raw('date(t.transaction_date)'), [$start_date, $end_date]);
                    }
                })
                ->where('contacts.business_id', $business_id)
                ->whereIn('contacts.type', ['customer', 'both']);

            if (!empty($location_id)) {
                $customer_transaction_counts->where('t.location_id', $location_id);
            }

            if (!empty($customer_ids)) {
                $customer_transaction_counts->whereIn('contacts.id', $customer_ids);
            }

            if ($permitted_locations != 'all') {
                $customer_transaction_counts->whereIn('t.location_id', $permitted_locations);
            }

            $customer_transaction_counts = $customer_transaction_counts
                ->select('contacts.id', DB::raw('COUNT(t.id) as transaction_count'))
                ->groupBy('contacts.id')
                ->get();

            // Now categorize customers based on their transaction count
            $segments = [
                'No Purchase' => 0,
                'One-time' => 0,
                'Occasional' => 0,
                'Regular' => 0,
                'Loyal' => 0
            ];

            foreach ($customer_transaction_counts as $customer) {
                $count = $customer->transaction_count;

                if ($count == 0) {
                    $segments['No Purchase']++;
                } elseif ($count == 1) {
                    $segments['One-time']++;
                } elseif ($count >= 2 && $count <= 5) {
                    $segments['Occasional']++;
                } elseif ($count >= 6 && $count <= 12) {
                    $segments['Regular']++;
                } else {
                    $segments['Loyal']++;
                }
            }

            // Convert to collection format expected by the view
            $customer_segments = collect();
            foreach ($segments as $segment => $count) {
                $customer_segments->push((object)[
                    'segment' => $segment,
                    'customer_count' => $count
                ]);
            }

            // Customer Growth Trend (new vs returning)
            $customer_growth = Transaction::where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final');

            if (!empty($start_date) && !empty($end_date)) {
                $customer_growth->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $customer_growth->where('transactions.location_id', $location_id);
            }

            if (!empty($customer_ids)) {
                $customer_growth->whereIn('transactions.contact_id', $customer_ids);
            }

            if ($permitted_locations != 'all') {
                $customer_growth->whereIn('transactions.location_id', $permitted_locations);
            }

            $customer_growth = $customer_growth->select(
                DB::raw('DATE_FORMAT(transactions.transaction_date, "%Y-%m") as month'),
                DB::raw('COUNT(DISTINCT CASE WHEN transactions.id = (
                    SELECT MIN(t2.id) FROM transactions t2 
                    WHERE t2.contact_id = transactions.contact_id 
                    AND t2.type = "sell" 
                    AND t2.status = "final"
                ) THEN transactions.contact_id ELSE NULL END) as new_customers'),
                DB::raw('COUNT(DISTINCT CASE WHEN transactions.id != (
                    SELECT MIN(t2.id) FROM transactions t2 
                    WHERE t2.contact_id = transactions.contact_id 
                    AND t2.type = "sell" 
                    AND t2.status = "final"
                ) THEN transactions.contact_id ELSE NULL END) as returning_customers')
            )
            ->groupBy(DB::raw('DATE_FORMAT(transactions.transaction_date, "%Y-%m")'))
            ->orderBy(DB::raw('DATE_FORMAT(transactions.transaction_date, "%Y-%m")'))
            ->get();

            // Customer Purchase Time Analysis
            $purchase_time_analysis = Transaction::where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final');

            if (!empty($start_date) && !empty($end_date)) {
                $purchase_time_analysis->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $purchase_time_analysis->where('transactions.location_id', $location_id);
            }

            if (!empty($customer_ids)) {
                $purchase_time_analysis->whereIn('transactions.contact_id', $customer_ids);
            }

            if ($permitted_locations != 'all') {
                $purchase_time_analysis->whereIn('transactions.location_id', $permitted_locations);
            }

            // Time of day analysis
            $time_of_day = $purchase_time_analysis->clone()
                ->select(
                    DB::raw('CASE 
                        WHEN HOUR(transaction_date) BETWEEN 0 AND 5 THEN "Night (12AM-6AM)"
                        WHEN HOUR(transaction_date) BETWEEN 6 AND 11 THEN "Morning (6AM-12PM)"
                        WHEN HOUR(transaction_date) BETWEEN 12 AND 17 THEN "Afternoon (12PM-6PM)"
                        ELSE "Evening (6PM-12AM)"
                    END as time_of_day'),
                    DB::raw('COUNT(*) as transaction_count'),
                    DB::raw('SUM(final_total) as total_amount')
                )
                ->groupBy(DB::raw('CASE 
                    WHEN HOUR(transaction_date) BETWEEN 0 AND 5 THEN "Night (12AM-6AM)"
                    WHEN HOUR(transaction_date) BETWEEN 6 AND 11 THEN "Morning (6AM-12PM)"
                    WHEN HOUR(transaction_date) BETWEEN 12 AND 17 THEN "Afternoon (12PM-6PM)"
                    ELSE "Evening (6PM-12AM)"
                END'))
                ->orderBy('transaction_count', 'desc')
                ->get();

            // Day of week analysis
            $day_of_week = $purchase_time_analysis->clone()
                ->select(
                    DB::raw('DAYNAME(transaction_date) as day_of_week'),
                    DB::raw('COUNT(*) as transaction_count'),
                    DB::raw('SUM(final_total) as total_amount')
                )
                ->groupBy(DB::raw('DAYNAME(transaction_date)'))
                ->orderBy(DB::raw('DAYOFWEEK(MIN(transaction_date))'))
                ->get();

            // Day of month analysis
            $day_of_month = $purchase_time_analysis->clone()
                ->select(
                    DB::raw('DAY(transaction_date) as day_of_month'),
                    DB::raw('COUNT(*) as transaction_count'),
                    DB::raw('SUM(final_total) as total_amount')
                )
                ->groupBy(DB::raw('DAY(transaction_date)'))
                ->orderBy(DB::raw('DAY(transaction_date)'))
                ->get();

            // Month of year analysis
            $month_of_year = $purchase_time_analysis->clone()
                ->select(
                    DB::raw('MONTHNAME(transaction_date) as month_of_year'),
                    DB::raw('COUNT(*) as transaction_count'),
                    DB::raw('SUM(final_total) as total_amount')
                )
                ->groupBy(DB::raw('MONTHNAME(transaction_date)'))
                ->orderBy(DB::raw('MONTH(MIN(transaction_date))'))
                ->get();

            // Predictive Sales Forecasting
            $sales_forecast = [];
            $forecast_months = 3; // Forecast for next 3 months

            if (count($monthly_sales) > 0) {
                // Get the last 12 months of sales data or all available if less than 12
                $recent_monthly_sales = $monthly_sales->sortByDesc(function($item) {
                    return $item->year * 100 + $item->month;
                })->take(12)->sortBy(function($item) {
                    return $item->year * 100 + $item->month;
                });

                // Calculate average monthly growth rate
                $growth_rates = [];
                $prev_sales = null;

                foreach ($recent_monthly_sales as $sale) {
                    if ($prev_sales !== null) {
                        $growth_rate = $prev_sales > 0 ? (($sale->total_sales - $prev_sales) / $prev_sales) : 0;
                        $growth_rates[] = $growth_rate;
                    }
                    $prev_sales = $sale->total_sales;
                }

                // Calculate average growth rate
                $avg_growth_rate = count($growth_rates) > 0 ? array_sum($growth_rates) / count($growth_rates) : 0;

                // Get the last month's sales
                $last_sale = $recent_monthly_sales->last();
                $last_month = $last_sale->month;
                $last_year = $last_sale->year;
                $last_sales = $last_sale->total_sales;

                // Generate forecast for next months
                for ($i = 1; $i <= $forecast_months; $i++) {
                    $forecast_month = $last_month + $i;
                    $forecast_year = $last_year;

                    if ($forecast_month > 12) {
                        $forecast_month = $forecast_month - 12;
                        $forecast_year++;
                    }

                    // Apply growth rate to forecast
                    $forecast_sales = $last_sales * (1 + $avg_growth_rate);

                    // Apply seasonal adjustment if we have data from the same month last year
                    $same_month_last_year = $monthly_sales->first(function($item) use ($forecast_month, $forecast_year) {
                        return $item->month == $forecast_month && $item->year == ($forecast_year - 1);
                    });

                    if ($same_month_last_year) {
                        // Find the average month-to-month ratio
                        $month_ratio = $same_month_last_year->total_sales / $last_sales;
                        $forecast_sales = $forecast_sales * $month_ratio;
                    }

                    $sales_forecast[] = [
                        'year' => $forecast_year,
                        'month' => $forecast_month,
                        'forecast_sales' => max(0, $forecast_sales) // Ensure no negative forecasts
                    ];

                    $last_sales = $forecast_sales; // Update for next iteration
                }
            }

            // Customer Churn Prediction
            $churn_predictions = [];

            if (count($clv_analysis) > 0) {
                foreach ($clv_analysis as $customer) {
                    // Calculate days since last purchase
                    $days_since_last_purchase = 0;
                    if ($customer->last_purchase_date) {
                        $last_purchase = \Carbon\Carbon::parse($customer->last_purchase_date);
                        $days_since_last_purchase = $last_purchase->diffInDays(\Carbon\Carbon::now());
                    }

                    // Calculate churn probability based on days since last purchase and purchase frequency
                    $churn_probability = 0;

                    if ($customer->purchase_frequency > 0) {
                        // Expected days between purchases
                        $expected_days_between_purchases = 30 / $customer->purchase_frequency;

                        // If days since last purchase is more than 2x the expected days between purchases, high churn risk
                        if ($days_since_last_purchase > 2 * $expected_days_between_purchases) {
                            $churn_probability = min(0.9, $days_since_last_purchase / ($expected_days_between_purchases * 3));
                        } else {
                            $churn_probability = max(0.1, $days_since_last_purchase / ($expected_days_between_purchases * 4));
                        }
                    } else {
                        // If no purchase frequency, high churn risk
                        $churn_probability = 0.9;
                    }

                    // Categorize churn risk
                    $churn_risk = 'Low';
                    if ($churn_probability >= 0.7) {
                        $churn_risk = 'High';
                    } elseif ($churn_probability >= 0.4) {
                        $churn_risk = 'Medium';
                    }

                    $churn_predictions[] = [
                        'customer_id' => $customer->id,
                        'customer_name' => $customer->name,
                        'days_since_last_purchase' => $days_since_last_purchase,
                        'purchase_frequency' => $customer->purchase_frequency,
                        'churn_probability' => $churn_probability,
                        'churn_risk' => $churn_risk
                    ];
                }

                // Sort by churn probability (highest first)
                usort($churn_predictions, function($a, $b) {
                    return $b['churn_probability'] <=> $a['churn_probability'];
                });

                // Take top 10 at-risk customers
                $churn_predictions = array_slice($churn_predictions, 0, 10);
            }

            // Product Recommendations
            $product_recommendations = [];

            if (count($cross_sell_products) > 0) {
                // Group products by their co-occurrence
                $product_pairs = [];
                foreach ($cross_sell_products as $pair) {
                    $key1 = $pair->product_1 . '|' . $pair->product_2;
                    $key2 = $pair->product_2 . '|' . $pair->product_1;

                    if (!isset($product_pairs[$key1]) && !isset($product_pairs[$key2])) {
                        $product_pairs[$key1] = [
                            'product_1' => $pair->product_1,
                            'product_2' => $pair->product_2,
                            'frequency' => $pair->frequency,
                            'confidence' => 0 // Will calculate later
                        ];
                    }
                }

                // Calculate confidence for each pair
                foreach ($product_pairs as $key => &$pair) {
                    // Find total sales of product_1
                    $product_1_sales = 0;
                    foreach ($product_performance as $product) {
                        if ($product->product_name == $pair['product_1']) {
                            $product_1_sales = $product->total_quantity;
                            break;
                        }
                    }

                    // Calculate confidence (probability of buying product_2 given product_1)
                    if ($product_1_sales > 0) {
                        $pair['confidence'] = $pair['frequency'] / $product_1_sales;
                    }
                }

                // Sort by confidence
                usort($product_pairs, function($a, $b) {
                    return $b['confidence'] <=> $a['confidence'];
                });

                // Take top 10 recommendations
                $product_recommendations = array_slice($product_pairs, 0, 10);
            }

            // Seasonal Trend Prediction
            $seasonal_predictions = [];

            if (count($month_of_year) > 0) {
                // Calculate average sales by month
                $monthly_averages = [];
                $total_sales = 0;
                $total_count = 0;

                foreach ($month_of_year as $month_data) {
                    $monthly_averages[$month_data->month_of_year] = $month_data->total_amount;
                    $total_sales += $month_data->total_amount;
                    $total_count++;
                }

                $overall_average = $total_count > 0 ? $total_sales / $total_count : 0;

                // Calculate seasonal index for each month
                $seasonal_indices = [];
                foreach ($monthly_averages as $month => $average) {
                    $seasonal_indices[$month] = $overall_average > 0 ? $average / $overall_average : 1;
                }

                // Predict next year's monthly sales
                $current_month = date('F'); // Current month name
                $current_year = date('Y');

                for ($i = 1; $i <= 12; $i++) {
                    $month_name = date('F', mktime(0, 0, 0, date('n') + $i, 1, date('Y')));
                    $year = date('Y', mktime(0, 0, 0, date('n') + $i, 1, date('Y')));

                    // If we have a seasonal index for this month, use it
                    $seasonal_index = isset($seasonal_indices[$month_name]) ? $seasonal_indices[$month_name] : 1;

                    // Predict sales using overall average and seasonal index
                    $predicted_sales = $overall_average * $seasonal_index;

                    $seasonal_predictions[] = [
                        'month' => $month_name,
                        'year' => $year,
                        'predicted_sales' => $predicted_sales,
                        'seasonal_index' => $seasonal_index
                    ];
                }

                // Take only next 6 months
                $seasonal_predictions = array_slice($seasonal_predictions, 0, 6);
            }

            $data = [
                'sales_trends' => $sales_trends,
                'moving_avg_sales' => $moving_avg_sales,
                'monthly_sales' => $monthly_sales,
                'yoy_growth' => $yoy_growth,
                'quarterly_sales' => $quarterly_sales,
                'product_performance' => $product_performance,
                'product_monthly_sales' => $product_monthly_sales,
                'category_performance' => $category_performance,
                'customers' => $customers,
                'retention_rate' => $retention_rate,
                'clv_trend' => $clv_trend,
                'cross_sell_products' => $cross_sell_products,
                'payment_behavior' => $payment_behavior,
                'payment_trends_data' => $payment_trends_data,
                'payment_months' => $payment_months,
                'discount_analytics' => $discount_analytics,
                'discount_trends' => $discount_trends,
                'clv_analysis' => $clv_analysis,
                'customer_segments' => $customer_segments,
                'customer_growth' => $customer_growth,
                'time_of_day' => $time_of_day,
                'day_of_week' => $day_of_week,
                'day_of_month' => $day_of_month,
                'month_of_year' => $month_of_year,
                'sales_forecast' => $sales_forecast,
                'churn_predictions' => $churn_predictions,
                'product_recommendations' => $product_recommendations,
                'seasonal_predictions' => $seasonal_predictions
            ];

            return view('report.partials.customer_advance_analytics_details', compact('data'))->render();
            });
        }

        $business_locations = BusinessLocation::forDropdown($business_id, true);
        $customers = Contact::where('business_id', $business_id)
                        ->whereIn('type', ['customer', 'both'])
                        ->select('id', DB::raw("IF(contacts.contact_id IS NULL OR contacts.contact_id='', name, CONCAT(name, ' - ', contacts.contact_id)) as name"))
                        ->orderBy('name')
                        ->pluck('name', 'id');

        return view('report.customer_advance_analytics', compact('business_locations', 'customers'));
    }
    /**
     * Shows product advance analytics report
     *
     * @return \Illuminate\Http\Response
     */
    public function getProductAdvanceAnalytics(Request $request)
    {
        if (! auth()->user()->can('profit_loss_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $fy = $this->businessUtil->getCurrentFinancialYear($business_id);

            $location_id = ! empty($request->get('location_id')) ? $request->get('location_id') : null;
            $start_date = ! empty($request->get('start_date')) ? $request->get('start_date') : $fy['start'];
            $end_date = ! empty($request->get('end_date')) ? $request->get('end_date') : $fy['end'];
            $product_ids = ! empty($request->get('product_ids')) ? $request->get('product_ids') : [];

            // Check if request wants JSON response for dashboard
            $return_json = $request->get('dataType') === 'json';

            $permitted_locations = auth()->user()->permitted_locations();

            // 1. Sales Analytics
            // Sales Trends
            $sales_query = Transaction::where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final');

            if (!empty($start_date) && !empty($end_date)) {
                $sales_query->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $sales_query->where('transactions.location_id', $location_id);
            }

            if ($permitted_locations != 'all') {
                $sales_query->whereIn('transactions.location_id', $permitted_locations);
            }

            // Sales Trends - Revenue & volume by month/quarter/year
            $sales_trends = $sales_query->select(
                DB::raw('DATE(transactions.transaction_date) as date'),
                DB::raw('SUM(transactions.final_total) as total_sales'),
                DB::raw('COUNT(transactions.id) as transaction_count')
            )
            ->groupBy(DB::raw('DATE(transactions.transaction_date)'))
            ->orderBy(DB::raw('DATE(transactions.transaction_date)'))
            ->get();

            // Monthly sales
            $monthly_sales = $sales_query->select(
                DB::raw('DATE(transactions.transaction_date) as date'),
                DB::raw('MONTH(transactions.transaction_date) as month'),
                DB::raw('YEAR(transactions.transaction_date) as year'),
                DB::raw('SUM(transactions.final_total) as total_sales'),
                DB::raw('COUNT(transactions.id) as transaction_count')
            )
            ->groupBy(DB::raw('DATE(transactions.transaction_date)'), DB::raw('MONTH(transactions.transaction_date)'), DB::raw('YEAR(transactions.transaction_date)'))
            ->orderBy(DB::raw('DATE(transactions.transaction_date)'))
            ->orderBy(DB::raw('YEAR(transactions.transaction_date)'))
            ->orderBy(DB::raw('MONTH(transactions.transaction_date)'))
            ->get();

            // Product Mix - Top-selling lubricants
            $product_mix_query = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final');

            if (!empty($start_date) && !empty($end_date)) {
                $product_mix_query->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $product_mix_query->where('transactions.location_id', $location_id);
            }

            if (!empty($product_ids)) {
                $product_mix_query->whereIn('transaction_sell_lines.product_id', $product_ids);
            }

            if ($permitted_locations != 'all') {
                $product_mix_query->whereIn('transactions.location_id', $permitted_locations);
            }

            $product_mix = $product_mix_query->select(
                'products.name as product_name',
                'categories.name as category_name',
                DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity'),
                DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) as total_amount')
            )
            ->groupBy('products.id')
            ->orderBy('total_amount', 'desc')
            ->limit(10)
            ->get();

            // Customer Behavior (RFM)
            $customer_behavior = Contact::leftJoin('transactions as t', function ($join) use ($start_date, $end_date) {
                $join->on('contacts.id', '=', 't.contact_id')
                    ->where('t.type', 'sell')
                    ->where('t.status', 'final');

                if (!empty($start_date) && !empty($end_date)) {
                    $join->whereBetween(DB::raw('date(t.transaction_date)'), [$start_date, $end_date]);
                }
            })
            ->leftJoin('transaction_sell_lines as tsl', 't.id', '=', 'tsl.transaction_id')
            ->where('contacts.business_id', $business_id)
            ->whereIn('contacts.type', ['customer', 'both']);

            if (!empty($location_id)) {
                $customer_behavior->where('t.location_id', $location_id);
            }

            if (!empty($product_ids)) {
                $customer_behavior->whereIn('tsl.product_id', $product_ids);
            }

            if ($permitted_locations != 'all') {
                $customer_behavior->whereIn('t.location_id', $permitted_locations);
            }

            $customer_behavior = $customer_behavior->select(
                'contacts.id',
                'contacts.name',
                DB::raw('MAX(t.transaction_date) as last_purchase_date'),
                DB::raw('COUNT(DISTINCT t.id) as frequency'),
                DB::raw('SUM(t.final_total) as total_spent')
            )
            ->groupBy('contacts.id')
            ->orderBy('frequency', 'desc')
            ->limit(10)
            ->get();

            // Cross-sell & Bundle Analysis
            $cross_sell_query = DB::table('transaction_sell_lines as tsl1')
                ->join('transaction_sell_lines as tsl2', function ($join) {
                    $join->on('tsl1.transaction_id', '=', 'tsl2.transaction_id')
                        ->where('tsl1.product_id', '!=', 'tsl2.product_id');
                })
                ->join('transactions as t', 't.id', '=', 'tsl1.transaction_id')
                ->join('products as p1', 'p1.id', '=', 'tsl1.product_id')
                ->join('products as p2', 'p2.id', '=', 'tsl2.product_id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->where('t.status', 'final');

            if (!empty($start_date) && !empty($end_date)) {
                $cross_sell_query->whereBetween(DB::raw('date(t.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $cross_sell_query->where('t.location_id', $location_id);
            }

            if (!empty($product_ids)) {
                $cross_sell_query->where(function ($query) use ($product_ids) {
                    $query->whereIn('tsl1.product_id', $product_ids)
                        ->orWhereIn('tsl2.product_id', $product_ids);
                });
            }

            if ($permitted_locations != 'all') {
                $cross_sell_query->whereIn('t.location_id', $permitted_locations);
            }

            $cross_sell_products = $cross_sell_query->select(
                'p1.id as product_1_id',
                'p1.name as product_1',
                'p2.id as product_2_id',
                'p2.name as product_2',
                DB::raw('COUNT(*) as frequency'),
                DB::raw('COUNT(*) * 100.0 / (SELECT COUNT(DISTINCT transaction_id) FROM transaction_sell_lines WHERE product_id = tsl1.product_id) as confidence_percentage')
            )
            ->groupBy('p1.id', 'p2.id')
            ->orderBy('frequency', 'desc')
            ->limit(20)
            ->get();

            // Enhanced Cross-sell Analysis - Find product bundles (groups of products frequently bought together)
            $product_bundles = [];
            $processed_pairs = [];

            // First, identify strong product pairs (high frequency and confidence)
            $strong_pairs = [];
            foreach ($cross_sell_products as $pair) {
                if ($pair->frequency >= 3 && $pair->confidence_percentage >= 30) {
                    $strong_pairs[] = [
                        'product_1_id' => $pair->product_1_id,
                        'product_2_id' => $pair->product_2_id,
                        'product_1' => $pair->product_1,
                        'product_2' => $pair->product_2,
                        'frequency' => $pair->frequency,
                        'confidence' => $pair->confidence_percentage
                    ];

                    $pair_key = min($pair->product_1_id, $pair->product_2_id) . '_' . max($pair->product_1_id, $pair->product_2_id);
                    $processed_pairs[$pair_key] = true;
                }
            }

            // Then, build bundles from strong pairs
            foreach ($strong_pairs as $i => $pair1) {
                $bundle = [
                    'products' => [
                        ['id' => $pair1['product_1_id'], 'name' => $pair1['product_1']],
                        ['id' => $pair1['product_2_id'], 'name' => $pair1['product_2']]
                    ],
                    'frequency' => $pair1['frequency'],
                    'avg_confidence' => $pair1['confidence']
                ];

                $extended = false;

                // Try to extend the bundle with other products
                foreach ($strong_pairs as $j => $pair2) {
                    if ($i == $j) continue;

                    // Check if pair2 shares a product with the current bundle
                    $shared_product = false;
                    $new_product = null;
                    $new_product_id = null;

                    foreach ($bundle['products'] as $product) {
                        if ($product['id'] == $pair2['product_1_id']) {
                            $shared_product = true;
                            $new_product = $pair2['product_2'];
                            $new_product_id = $pair2['product_2_id'];
                            break;
                        } elseif ($product['id'] == $pair2['product_2_id']) {
                            $shared_product = true;
                            $new_product = $pair2['product_1'];
                            $new_product_id = $pair2['product_1_id'];
                            break;
                        }
                    }

                    // If there's a shared product and the new product is not already in the bundle
                    if ($shared_product && !in_array($new_product_id, array_column($bundle['products'], 'id'))) {
                        // Check if all products in the bundle have strong connections with the new product
                        $all_connected = true;
                        foreach ($bundle['products'] as $product) {
                            $check_key = min($product['id'], $new_product_id) . '_' . max($product['id'], $new_product_id);
                            if (!isset($processed_pairs[$check_key])) {
                                $all_connected = false;
                                break;
                            }
                        }

                        if ($all_connected) {
                            $bundle['products'][] = ['id' => $new_product_id, 'name' => $new_product];
                            $bundle['avg_confidence'] = ($bundle['avg_confidence'] + $pair2['confidence']) / 2;
                            $extended = true;
                        }
                    }
                }

                // Only add bundles with 2 or more products
                if (count($bundle['products']) >= 2) {
                    // Sort products by name for consistent display
                    usort($bundle['products'], function($a, $b) {
                        return strcmp($a['name'], $b['name']);
                    });

                    // Generate a unique key for the bundle
                    $bundle_key = implode('_', array_column($bundle['products'], 'id'));

                    // Only add if this exact bundle doesn't exist yet
                    if (!isset($product_bundles[$bundle_key])) {
                        $product_bundles[$bundle_key] = $bundle;
                    }
                }
            }

            // Convert to indexed array and sort by frequency
            $product_bundles = array_values($product_bundles);
            usort($product_bundles, function($a, $b) {
                return $b['frequency'] <=> $a['frequency'];
            });

            // Limit to top 5 bundles
            $product_bundles = array_slice($product_bundles, 0, 5);

            // Calculate lift (how much more likely products are bought together vs. separately)
            foreach ($cross_sell_products as $pair) {
                // Get individual product frequencies
                $product1_freq = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell')
                    ->where('transactions.status', 'final')
                    ->where('transaction_sell_lines.product_id', $pair->product_1_id);

                $product2_freq = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell')
                    ->where('transactions.status', 'final')
                    ->where('transaction_sell_lines.product_id', $pair->product_2_id);

                if (!empty($start_date) && !empty($end_date)) {
                    $product1_freq->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
                    $product2_freq->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
                }

                if (!empty($location_id)) {
                    $product1_freq->where('transactions.location_id', $location_id);
                    $product2_freq->where('transactions.location_id', $location_id);
                }

                if ($permitted_locations != 'all') {
                    $product1_freq->whereIn('transactions.location_id', $permitted_locations);
                    $product2_freq->whereIn('transactions.location_id', $permitted_locations);
                }

                $product1_count = $product1_freq->count(DB::raw('DISTINCT transaction_sell_lines.transaction_id'));
                $product2_count = $product2_freq->count(DB::raw('DISTINCT transaction_sell_lines.transaction_id'));

                // Get total transaction count
                $total_transactions = Transaction::where('business_id', $business_id)
                    ->where('type', 'sell')
                    ->where('status', 'final');

                if (!empty($start_date) && !empty($end_date)) {
                    $total_transactions->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
                }

                if (!empty($location_id)) {
                    $total_transactions->where('location_id', $location_id);
                }

                if ($permitted_locations != 'all') {
                    $total_transactions->whereIn('location_id', $permitted_locations);
                }

                $total_count = $total_transactions->count();

                // Calculate expected co-occurrence if products were independent
                $expected_frequency = ($product1_count / $total_count) * ($product2_count / $total_count) * $total_count;

                // Calculate lift (actual co-occurrence / expected co-occurrence)
                $lift = $expected_frequency > 0 ? $pair->frequency / $expected_frequency : 0;

                $pair->lift = $lift;
                $pair->product1_count = $product1_count;
                $pair->product2_count = $product2_count;
            }

            // Sort by lift for the top recommendations
            $cross_sell_recommendations = clone $cross_sell_products;
            $cross_sell_recommendations = $cross_sell_recommendations->sortByDesc('lift')->take(10);

            // Discount Impact
            $discount_impact = Transaction::where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final');

            if (!empty($start_date) && !empty($end_date)) {
                $discount_impact->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $discount_impact->where('transactions.location_id', $location_id);
            }

            if ($permitted_locations != 'all') {
                $discount_impact->whereIn('transactions.location_id', $permitted_locations);
            }

            $discount_impact = $discount_impact->select(
                DB::raw('SUM(transactions.final_total) as total_sales'),
                DB::raw('SUM(transactions.discount_amount) as total_discount'),
                DB::raw('(SUM(transactions.discount_amount) / (SUM(transactions.final_total) + SUM(transactions.discount_amount))) * 100 as discount_percentage')
            )
            ->first();

            // Profitability by Product
            $profitability_query = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
                ->leftJoin('transaction_sell_lines_purchase_lines as tspl', 'transaction_sell_lines.id', '=', 'tspl.sell_line_id')
                ->leftJoin('purchase_lines', 'tspl.purchase_line_id', '=', 'purchase_lines.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final');

            if (!empty($start_date) && !empty($end_date)) {
                $profitability_query->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $profitability_query->where('transactions.location_id', $location_id);
            }

            if (!empty($product_ids)) {
                $profitability_query->whereIn('transaction_sell_lines.product_id', $product_ids);
            }

            if ($permitted_locations != 'all') {
                $profitability_query->whereIn('transactions.location_id', $permitted_locations);
            }

            $profitability = $profitability_query->select(
                'products.name as product_name',
                DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) as total_sales'),
                DB::raw('SUM(transaction_sell_lines.quantity * purchase_lines.purchase_price_inc_tax) as total_cost'),
                DB::raw('SUM(transaction_sell_lines.quantity * (transaction_sell_lines.unit_price_inc_tax - purchase_lines.purchase_price_inc_tax)) as gross_profit'),
                DB::raw('(SUM(transaction_sell_lines.quantity * (transaction_sell_lines.unit_price_inc_tax - purchase_lines.purchase_price_inc_tax)) / SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax)) * 100 as profit_margin')
            )
            ->groupBy('products.id')
            ->orderBy('gross_profit', 'desc')
            ->limit(10)
            ->get();

            // 2. Purchase Analytics
            // Purchase Trends
            $purchase_query = Transaction::where('transactions.business_id', $business_id)
                ->where('transactions.type', 'purchase')
                ->where('transactions.status', 'received');

            if (!empty($start_date) && !empty($end_date)) {
                $purchase_query->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $purchase_query->where('transactions.location_id', $location_id);
            }

            if ($permitted_locations != 'all') {
                $purchase_query->whereIn('transactions.location_id', $permitted_locations);
            }

            $purchase_trends = $purchase_query->select(
                DB::raw('DATE(transactions.transaction_date) as date'),
                DB::raw('SUM(transactions.final_total) as total_purchase'),
                DB::raw('COUNT(transactions.id) as transaction_count')
            )
            ->groupBy(DB::raw('DATE(transactions.transaction_date)'))
            ->orderBy(DB::raw('DATE(transactions.transaction_date)'))
            ->get();

            // Monthly purchases
            $monthly_purchases = $purchase_query->select(
                DB::raw('DATE(transactions.transaction_date) as date'),
                DB::raw('MONTH(transactions.transaction_date) as month'),
                DB::raw('YEAR(transactions.transaction_date) as year'),
                DB::raw('SUM(transactions.final_total) as total_purchase'),
                DB::raw('COUNT(transactions.id) as transaction_count')
            )
            ->groupBy(DB::raw('DATE(transactions.transaction_date)'), DB::raw('MONTH(transactions.transaction_date)'), DB::raw('YEAR(transactions.transaction_date)'))
            ->orderBy(DB::raw('DATE(transactions.transaction_date)'))
            ->orderBy(DB::raw('YEAR(transactions.transaction_date)'))
            ->orderBy(DB::raw('MONTH(transactions.transaction_date)'))
            ->get();

            // Supplier Performance
            $supplier_performance = Transaction::where('transactions.business_id', $business_id)
                ->where('transactions.type', 'purchase')
                ->where('transactions.status', 'received')
                ->join('contacts', 'transactions.contact_id', '=', 'contacts.id');

            if (!empty($start_date) && !empty($end_date)) {
                $supplier_performance->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $supplier_performance->where('transactions.location_id', $location_id);
            }

            if ($permitted_locations != 'all') {
                $supplier_performance->whereIn('transactions.location_id', $permitted_locations);
            }

            $supplier_performance = $supplier_performance->select(
                'contacts.name as supplier_name',
                DB::raw('SUM(transactions.final_total) as total_purchase'),
                DB::raw('COUNT(transactions.id) as transaction_count'),
                DB::raw('AVG(DATEDIFF(transactions.transaction_date, transactions.created_at)) as avg_lead_time')
            )
            ->groupBy('contacts.id')
            ->orderBy('total_purchase', 'desc')
            ->limit(10)
            ->get();

            // Product-Level Purchase
            $product_purchase_query = PurchaseLine::join('transactions', 'purchase_lines.transaction_id', '=', 'transactions.id')
                ->join('products', 'purchase_lines.product_id', '=', 'products.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'purchase')
                ->where('transactions.status', 'received');

            if (!empty($start_date) && !empty($end_date)) {
                $product_purchase_query->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $product_purchase_query->where('transactions.location_id', $location_id);
            }

            if (!empty($product_ids)) {
                $product_purchase_query->whereIn('purchase_lines.product_id', $product_ids);
            }

            if ($permitted_locations != 'all') {
                $product_purchase_query->whereIn('transactions.location_id', $permitted_locations);
            }

            $product_purchases = $product_purchase_query->select(
                'products.name as product_name',
                DB::raw('SUM(purchase_lines.quantity) as total_quantity'),
                DB::raw('SUM(purchase_lines.quantity * purchase_lines.purchase_price_inc_tax) as total_amount')
            )
            ->groupBy('products.id')
            ->orderBy('total_amount', 'desc')
            ->limit(10)
            ->get();

            // 3. Sales vs. Purchase Alignment
            // Demand vs. Supply Gap
            $demand_supply_gap = [];
            if (!empty($product_ids)) {
                $demand_supply_query = Product::whereIn('id', $product_ids)
                    ->where('business_id', $business_id);
            } else {
                $demand_supply_query = Product::where('business_id', $business_id);
            }

            $products_for_gap = $demand_supply_query->select('id', 'name')->limit(10)->get();

            if (isset($products_for_gap) && !empty($products_for_gap)) {
                foreach ($products_for_gap as $product) {
                // Get sales for this product
                $sales = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell')
                    ->where('transactions.status', 'final')
                    ->where('transaction_sell_lines.product_id', $product->id);

                if (!empty($start_date) && !empty($end_date)) {
                    $sales->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
                }

                if (!empty($location_id)) {
                    $sales->where('transactions.location_id', $location_id);
                }

                if ($permitted_locations != 'all') {
                    $sales->whereIn('transactions.location_id', $permitted_locations);
                }

                $total_sales = $sales->sum('transaction_sell_lines.quantity');

                // Get purchases for this product
                $purchases = PurchaseLine::join('transactions', 'purchase_lines.transaction_id', '=', 'transactions.id')
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'purchase')
                    ->where('transactions.status', 'received')
                    ->where('purchase_lines.product_id', $product->id);

                if (!empty($start_date) && !empty($end_date)) {
                    $purchases->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
                }

                if (!empty($location_id)) {
                    $purchases->where('transactions.location_id', $location_id);
                }

                if ($permitted_locations != 'all') {
                    $purchases->whereIn('transactions.location_id', $permitted_locations);
                }

                $total_purchases = $purchases->sum('purchase_lines.quantity');

                $demand_supply_gap[] = [
                    'product_name' => $product->name,
                    'total_sales' => $total_sales,
                    'total_purchases' => $total_purchases,
                    'gap' => $total_purchases - $total_sales
                ];
                }
            }

            // Stock Turnover Rate
            $stock_turnover = [];
            if (isset($products_for_gap) && !empty($products_for_gap)) {
                foreach ($products_for_gap as $product) {
                // Get average inventory
                $opening_stock = Variation::where('variations.product_id', $product->id)
                    ->join('products', 'variations.product_id', '=', 'products.id')
                    ->join('variation_location_details', 'variations.id', '=', 'variation_location_details.variation_id')
                    ->where('products.business_id', $business_id)
                    ->select(DB::raw('SUM(variation_location_details.qty_available) as stock'))
                    ->first();

                $opening_stock_value = $opening_stock ? $opening_stock->stock : 0;

                // Get sales for this product
                $sales = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell')
                    ->where('transactions.status', 'final')
                    ->where('transaction_sell_lines.product_id', $product->id);

                if (!empty($start_date) && !empty($end_date)) {
                    $sales->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
                }

                if (!empty($location_id)) {
                    $sales->where('transactions.location_id', $location_id);
                }

                if ($permitted_locations != 'all') {
                    $sales->whereIn('transactions.location_id', $permitted_locations);
                }

                $total_sales = $sales->sum('transaction_sell_lines.quantity');

                // Calculate turnover rate (if average inventory is 0, set turnover to 0 to avoid division by zero)
                $turnover_rate = $opening_stock_value > 0 ? $total_sales / $opening_stock_value : 0;

                $stock_turnover[] = [
                    'product_name' => $product->name,
                    'average_inventory' => $opening_stock_value,
                    'total_sales' => $total_sales,
                    'turnover_rate' => $turnover_rate
                ];
                }
            }

            // 4. Margin & Profitability Analytics
            // Already covered in profitability section above

            // 5. Category & Brand Performance
            // Sales by Category
            $category_performance = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final');

            if (!empty($start_date) && !empty($end_date)) {
                $category_performance->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $category_performance->where('transactions.location_id', $location_id);
            }

            if ($permitted_locations != 'all') {
                $category_performance->whereIn('transactions.location_id', $permitted_locations);
            }

            $category_performance = $category_performance->select(
                'categories.name as category_name',
                DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) as total_amount'),
                DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity'),
                DB::raw('COUNT(DISTINCT products.id) as product_count')
            )
            ->groupBy('categories.id')
            ->orderBy('total_amount', 'desc')
            ->get();

            // Sales by Brand
            $brand_performance = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
                ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final');

            if (!empty($start_date) && !empty($end_date)) {
                $brand_performance->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $brand_performance->where('transactions.location_id', $location_id);
            }

            if ($permitted_locations != 'all') {
                $brand_performance->whereIn('transactions.location_id', $permitted_locations);
            }

            $brand_performance = $brand_performance->select(
                'brands.name as brand_name',
                DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) as total_amount'),
                DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity'),
                DB::raw('COUNT(DISTINCT products.id) as product_count')
            )
            ->groupBy('brands.id')
            ->orderBy('total_amount', 'desc')
            ->get();

            // 6. Inventory Aging Analysis
            $inventory_aging = Variation::join('products', 'variations.product_id', '=', 'products.id')
                ->join('variation_location_details', 'variations.id', '=', 'variation_location_details.variation_id')
                ->leftJoin('purchase_lines', 'variations.id', '=', 'purchase_lines.variation_id')
                ->where('products.business_id', $business_id)
                ->where('variation_location_details.qty_available', '>', 0);

            if (!empty($location_id)) {
                $inventory_aging->where('variation_location_details.location_id', $location_id);
            }

            if (!empty($product_ids)) {
                $inventory_aging->whereIn('products.id', $product_ids);
            }

            if ($permitted_locations != 'all') {
                $inventory_aging->whereIn('variation_location_details.location_id', $permitted_locations);
            }

            // Get the inventory aging data
            $inventory_aging_data = $inventory_aging->select(
                'products.id as product_id',
                'variations.id as variation_id',
                'products.name as product_name',
                'variations.name as variation_name',
                'variation_location_details.qty_available',
                DB::raw('MAX(purchase_lines.created_at) as last_purchased_date'),
                DB::raw('DATEDIFF(CURRENT_DATE, MAX(purchase_lines.created_at)) as days_in_inventory')
            )
            ->groupBy('variations.id', 'variation_location_details.location_id')
            ->orderBy('days_in_inventory', 'desc')
            ->limit(20)
            ->get();

            // Calculate days to stock out for each product/variation
            foreach ($inventory_aging_data as $item) {
                // Get average daily sales for this product/variation over the last 30 days (or date range if specified)
                $sales_query = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell')
                    ->where('transactions.status', 'final')
                    ->where('transaction_sell_lines.variation_id', $item->variation_id);

                if (!empty($start_date) && !empty($end_date)) {
                    $sales_query->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
                    $date_range = \Carbon\Carbon::parse($end_date)->diffInDays(\Carbon\Carbon::parse($start_date)) + 1;
                } else {
                    // Default to last 30 days if no date range specified
                    $sales_query->where('transactions.transaction_date', '>=', \Carbon\Carbon::now()->subDays(30));
                    $date_range = 30;
                }

                if (!empty($location_id)) {
                    $sales_query->where('transactions.location_id', $location_id);
                }

                if ($permitted_locations != 'all') {
                    $sales_query->whereIn('transactions.location_id', $permitted_locations);
                }

                $total_sales = $sales_query->sum('transaction_sell_lines.quantity');

                // Calculate average daily sales
                $avg_daily_sales = $date_range > 0 ? $total_sales / $date_range : 0;

                // Calculate days to stock out (if avg_daily_sales is 0, set to null to avoid division by zero)
                $item->days_to_stock_out = $avg_daily_sales > 0 ? ceil($item->qty_available / $avg_daily_sales) : null;
            }

            $inventory_aging = $inventory_aging_data;

            // 7. Seasonal Trends Analysis
            $seasonal_trends = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final');

            if (!empty($start_date) && !empty($end_date)) {
                $seasonal_trends->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $seasonal_trends->where('transactions.location_id', $location_id);
            }

            if (!empty($product_ids)) {
                $seasonal_trends->whereIn('transaction_sell_lines.product_id', $product_ids);
            }

            if ($permitted_locations != 'all') {
                $seasonal_trends->whereIn('transactions.location_id', $permitted_locations);
            }

            $seasonal_trends = $seasonal_trends->select(
                DB::raw('MONTH(transactions.transaction_date) as month'),
                DB::raw('YEAR(transactions.transaction_date) as year'),
                DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity'),
                DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) as total_amount')
            )
            ->groupBy(DB::raw('MONTH(transactions.transaction_date)'), DB::raw('YEAR(transactions.transaction_date)'))
            ->orderBy(DB::raw('YEAR(transactions.transaction_date)'))
            ->orderBy(DB::raw('MONTH(transactions.transaction_date)'))
            ->get();

            // 8. Product Performance by Location
            $location_performance = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
                ->join('business_locations', 'transactions.location_id', '=', 'business_locations.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final');

            if (!empty($start_date) && !empty($end_date)) {
                $location_performance->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($product_ids)) {
                $location_performance->whereIn('transaction_sell_lines.product_id', $product_ids);
            }

            if ($permitted_locations != 'all') {
                $location_performance->whereIn('transactions.location_id', $permitted_locations);
            }

            $location_performance = $location_performance->select(
                'business_locations.name as location_name',
                DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity'),
                DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) as total_amount'),
                DB::raw('COUNT(DISTINCT products.id) as product_count')
            )
            ->groupBy('business_locations.id')
            ->orderBy('total_amount', 'desc')
            ->get();

            // 9. Predictive Analytics
            // Sales Forecasting - Predict future sales based on historical data
            $sales_forecast = [];
            $product_forecasts = [];

            // Get top products for forecasting
            $top_products = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final');

            if (!empty($start_date) && !empty($end_date)) {
                $top_products->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $top_products->where('transactions.location_id', $location_id);
            }

            if ($permitted_locations != 'all') {
                $top_products->whereIn('transactions.location_id', $permitted_locations);
            }

            $top_products = $top_products->select(
                'products.id',
                'products.name',
                DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity')
            )
            ->groupBy('products.id')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->get();

            // Overall sales forecast
            if (isset($monthly_sales) && !$monthly_sales->isEmpty()) {
                // Group sales by month-year
                $sales_by_month = [];
                foreach ($monthly_sales as $sale) {
                    $month_year = $sale->year . '-' . str_pad($sale->month, 2, '0', STR_PAD_LEFT);
                    if (!isset($sales_by_month[$month_year])) {
                        $sales_by_month[$month_year] = 0;
                    }
                    $sales_by_month[$month_year] += $sale->total_sales;
                }

                // Sort by month-year
                ksort($sales_by_month);

                // Get the last 12 months of data (or less if not available)
                $recent_sales = array_slice($sales_by_month, -12, 12, true);

                // Calculate average monthly growth rate
                $growth_rates = [];
                $prev_sales = null;
                foreach ($recent_sales as $month => $sales) {
                    if ($prev_sales !== null && $prev_sales > 0) {
                        $growth_rates[] = ($sales - $prev_sales) / $prev_sales;
                    }
                    $prev_sales = $sales;
                }

                // Calculate average growth rate
                $avg_growth_rate = !empty($growth_rates) ? array_sum($growth_rates) / count($growth_rates) : 0;

                // Get the last month's sales
                $last_month_sales = end($recent_sales);
                $last_month_key = key($recent_sales);

                // Generate forecast for next 3 months
                $forecast_months = [];
                $forecast_values = [];

                list($year, $month) = explode('-', $last_month_key);
                $current_sales = $last_month_sales;

                for ($i = 1; $i <= 3; $i++) {
                    $month++;
                    if ($month > 12) {
                        $month = 1;
                        $year++;
                    }

                    // Apply growth rate to forecast
                    $forecast_sales = $current_sales * (1 + $avg_growth_rate);
                    $forecast_month_key = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);

                    $forecast_months[] = \Carbon\Carbon::createFromDate($year, $month, 1)->format('M Y');
                    $forecast_values[] = $forecast_sales;

                    $sales_forecast[] = [
                        'month' => \Carbon\Carbon::createFromDate($year, $month, 1)->format('M Y'),
                        'forecasted_sales' => $forecast_sales,
                        'growth_rate' => $avg_growth_rate * 100
                    ];

                    $current_sales = $forecast_sales;
                }

                // Add confidence intervals
                if (!empty($growth_rates)) {
                    $std_dev = $this->standardDeviation($growth_rates);
                    foreach ($sales_forecast as &$forecast) {
                        $forecast['lower_bound'] = $forecast['forecasted_sales'] * (1 - $std_dev);
                        $forecast['upper_bound'] = $forecast['forecasted_sales'] * (1 + $std_dev);
                    }
                }
            }

            // Product-level forecasting
            foreach ($top_products as $product) {
                // Get monthly sales for this product
                $product_monthly_sales = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell')
                    ->where('transactions.status', 'final')
                    ->where('transaction_sell_lines.product_id', $product->id);

                if (!empty($start_date) && !empty($end_date)) {
                    $product_monthly_sales->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
                }

                if (!empty($location_id)) {
                    $product_monthly_sales->where('transactions.location_id', $location_id);
                }

                if ($permitted_locations != 'all') {
                    $product_monthly_sales->whereIn('transactions.location_id', $permitted_locations);
                }

                $product_monthly_sales = $product_monthly_sales->select(
                    DB::raw('YEAR(transactions.transaction_date) as year'),
                    DB::raw('MONTH(transactions.transaction_date) as month'),
                    DB::raw('SUM(transaction_sell_lines.quantity) as quantity'),
                    DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) as amount')
                )
                ->groupBy(DB::raw('YEAR(transactions.transaction_date)'), DB::raw('MONTH(transactions.transaction_date)'))
                ->orderBy(DB::raw('YEAR(transactions.transaction_date)'))
                ->orderBy(DB::raw('MONTH(transactions.transaction_date)'))
                ->get();

                // Group sales by month-year
                $product_sales_by_month = [];
                foreach ($product_monthly_sales as $sale) {
                    $month_year = $sale->year . '-' . str_pad($sale->month, 2, '0', STR_PAD_LEFT);
                    if (!isset($product_sales_by_month[$month_year])) {
                        $product_sales_by_month[$month_year] = 0;
                    }
                    $product_sales_by_month[$month_year] += $sale->quantity;
                }

                // Sort by month-year
                ksort($product_sales_by_month);

                // Get the last 6 months of data (or less if not available)
                $recent_product_sales = array_slice($product_sales_by_month, -6, 6, true);

                // Calculate average monthly growth rate
                $product_growth_rates = [];
                $prev_product_sales = null;
                foreach ($recent_product_sales as $month => $sales) {
                    if ($prev_product_sales !== null && $prev_product_sales > 0) {
                        $product_growth_rates[] = ($sales - $prev_product_sales) / $prev_product_sales;
                    }
                    $prev_product_sales = $sales;
                }

                // Calculate average growth rate
                $avg_product_growth_rate = !empty($product_growth_rates) ? array_sum($product_growth_rates) / count($product_growth_rates) : 0;

                // Get the last month's sales
                $last_month_product_sales = end($recent_product_sales);
                $last_month_product_key = key($recent_product_sales);

                // Generate forecast for next 3 months
                $product_forecast = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'forecasts' => []
                ];

                list($year, $month) = explode('-', $last_month_product_key);
                $current_product_sales = $last_month_product_sales;

                for ($i = 1; $i <= 3; $i++) {
                    $month++;
                    if ($month > 12) {
                        $month = 1;
                        $year++;
                    }

                    // Apply growth rate to forecast
                    $forecast_product_sales = $current_product_sales * (1 + $avg_product_growth_rate);

                    $product_forecast['forecasts'][] = [
                        'month' => \Carbon\Carbon::createFromDate($year, $month, 1)->format('M Y'),
                        'forecasted_quantity' => $forecast_product_sales,
                        'growth_rate' => $avg_product_growth_rate * 100
                    ];

                    $current_product_sales = $forecast_product_sales;
                }

                $product_forecasts[] = $product_forecast;
            }

            // Product Lifecycle Analysis
            $lifecycle_analysis = [];

            // Get products with sufficient sales history
            $products_with_history = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final');

            if (!empty($start_date) && !empty($end_date)) {
                $products_with_history->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $products_with_history->where('transactions.location_id', $location_id);
            }

            if (!empty($product_ids)) {
                $products_with_history->whereIn('transaction_sell_lines.product_id', $product_ids);
            }

            if ($permitted_locations != 'all') {
                $products_with_history->whereIn('transactions.location_id', $permitted_locations);
            }

            $products_with_history = $products_with_history->select(
                'products.id',
                'products.name',
                DB::raw('MIN(transactions.transaction_date) as first_sale_date'),
                DB::raw('MAX(transactions.transaction_date) as last_sale_date'),
                DB::raw('COUNT(DISTINCT DATE(transactions.transaction_date)) as sales_days'),
                DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity')
            )
            ->groupBy('products.id')
            ->having('sales_days', '>=', 5) // Require at least 5 days of sales data
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get();

            foreach ($products_with_history as $product) {
                // Get monthly sales for this product
                $product_monthly_trend = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell')
                    ->where('transactions.status', 'final')
                    ->where('transaction_sell_lines.product_id', $product->id);

                if (!empty($start_date) && !empty($end_date)) {
                    $product_monthly_trend->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
                }

                if (!empty($location_id)) {
                    $product_monthly_trend->where('transactions.location_id', $location_id);
                }

                if ($permitted_locations != 'all') {
                    $product_monthly_trend->whereIn('transactions.location_id', $permitted_locations);
                }

                $product_monthly_trend = $product_monthly_trend->select(
                    DB::raw('YEAR(transactions.transaction_date) as year'),
                    DB::raw('MONTH(transactions.transaction_date) as month'),
                    DB::raw('SUM(transaction_sell_lines.quantity) as quantity')
                )
                ->groupBy(DB::raw('YEAR(transactions.transaction_date)'), DB::raw('MONTH(transactions.transaction_date)'))
                ->orderBy(DB::raw('YEAR(transactions.transaction_date)'))
                ->orderBy(DB::raw('MONTH(transactions.transaction_date)'))
                ->get();

                // Calculate growth rates between consecutive months
                $monthly_growth_rates = [];
                $prev_quantity = null;
                foreach ($product_monthly_trend as $trend) {
                    if ($prev_quantity !== null && $prev_quantity > 0) {
                        $monthly_growth_rates[] = ($trend->quantity - $prev_quantity) / $prev_quantity;
                    }
                    $prev_quantity = $trend->quantity;
                }

                // Determine lifecycle stage based on growth rates
                $lifecycle_stage = 'Unknown';
                $avg_growth_rate = !empty($monthly_growth_rates) ? array_sum($monthly_growth_rates) / count($monthly_growth_rates) : 0;
                $recent_growth_rates = array_slice($monthly_growth_rates, -3, 3); // Last 3 months
                $recent_avg_growth = !empty($recent_growth_rates) ? array_sum($recent_growth_rates) / count($recent_growth_rates) : 0;

                if ($avg_growth_rate > 0.1) {
                    $lifecycle_stage = 'Growth';
                } elseif ($avg_growth_rate >= -0.05 && $avg_growth_rate <= 0.1) {
                    $lifecycle_stage = 'Maturity';
                } elseif ($avg_growth_rate < -0.05) {
                    $lifecycle_stage = 'Decline';
                }

                // If product is new (less than 3 months of data), mark as Introduction
                $first_sale = \Carbon\Carbon::parse($product->first_sale_date);
                $last_sale = \Carbon\Carbon::parse($product->last_sale_date);
                $product_age_months = $first_sale->diffInMonths($last_sale) + 1;

                if ($product_age_months <= 3) {
                    $lifecycle_stage = 'Introduction';
                }

                $lifecycle_analysis[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'first_sale_date' => $product->first_sale_date,
                    'last_sale_date' => $product->last_sale_date,
                    'product_age_months' => $product_age_months,
                    'total_quantity' => $product->total_quantity,
                    'avg_growth_rate' => $avg_growth_rate * 100,
                    'recent_growth_rate' => $recent_avg_growth * 100,
                    'lifecycle_stage' => $lifecycle_stage
                ];
            }

            // Trend Detection - Identify emerging product trends
            $trend_detection = [];

            // Get products with sufficient sales history
            $products_for_trend = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final');

            if (!empty($start_date) && !empty($end_date)) {
                $products_for_trend->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $products_for_trend->where('transactions.location_id', $location_id);
            }

            if (!empty($product_ids)) {
                $products_for_trend->whereIn('transaction_sell_lines.product_id', $product_ids);
            }

            if ($permitted_locations != 'all') {
                $products_for_trend->whereIn('transactions.location_id', $permitted_locations);
            }

            $products_for_trend = $products_for_trend->select(
                'products.id',
                'products.name',
                DB::raw('COUNT(DISTINCT DATE(transactions.transaction_date)) as sales_days')
            )
            ->groupBy('products.id')
            ->having('sales_days', '>=', 5) // Require at least 5 days of sales data
            ->orderBy('sales_days', 'desc')
            ->limit(20)
            ->get();

            // For each product, get monthly sales data and calculate trend
            foreach ($products_for_trend as $product) {
                $monthly_trend = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell')
                    ->where('transactions.status', 'final')
                    ->where('transaction_sell_lines.product_id', $product->id);

                if (!empty($start_date) && !empty($end_date)) {
                    $monthly_trend->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
                }

                if (!empty($location_id)) {
                    $monthly_trend->where('transactions.location_id', $location_id);
                }

                if ($permitted_locations != 'all') {
                    $monthly_trend->whereIn('transactions.location_id', $permitted_locations);
                }

                $monthly_trend = $monthly_trend->select(
                    DB::raw('YEAR(transactions.transaction_date) as year'),
                    DB::raw('MONTH(transactions.transaction_date) as month'),
                    DB::raw('SUM(transaction_sell_lines.quantity) as quantity'),
                    DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) as amount')
                )
                ->groupBy(DB::raw('YEAR(transactions.transaction_date)'), DB::raw('MONTH(transactions.transaction_date)'))
                ->orderBy(DB::raw('YEAR(transactions.transaction_date)'))
                ->orderBy(DB::raw('MONTH(transactions.transaction_date)'))
                ->get();

                // Need at least 3 months of data for trend analysis
                if (count($monthly_trend) >= 3) {
                    // Calculate month-over-month growth rates
                    $growth_rates = [];
                    $monthly_data = [];
                    $prev_quantity = null;

                    foreach ($monthly_trend as $month_data) {
                        $month_year = $month_data->year . '-' . str_pad($month_data->month, 2, '0', STR_PAD_LEFT);
                        $monthly_data[$month_year] = [
                            'quantity' => $month_data->quantity,
                            'amount' => $month_data->amount
                        ];

                        if ($prev_quantity !== null && $prev_quantity > 0) {
                            $growth_rates[] = ($month_data->quantity - $prev_quantity) / $prev_quantity;
                        }
                        $prev_quantity = $month_data->quantity;
                    }

                    // Calculate trend metrics
                    $avg_growth_rate = !empty($growth_rates) ? array_sum($growth_rates) / count($growth_rates) : 0;

                    // Get recent growth (last 3 months or less)
                    $recent_growth_rates = array_slice($growth_rates, -min(3, count($growth_rates)));
                    $recent_avg_growth = !empty($recent_growth_rates) ? array_sum($recent_growth_rates) / count($recent_growth_rates) : 0;

                    // Calculate trend acceleration (is growth rate increasing or decreasing?)
                    $trend_acceleration = 0;
                    if (count($growth_rates) >= 2) {
                        $first_half = array_slice($growth_rates, 0, floor(count($growth_rates) / 2));
                        $second_half = array_slice($growth_rates, floor(count($growth_rates) / 2));

                        $first_half_avg = !empty($first_half) ? array_sum($first_half) / count($first_half) : 0;
                        $second_half_avg = !empty($second_half) ? array_sum($second_half) / count($second_half) : 0;

                        $trend_acceleration = $second_half_avg - $first_half_avg;
                    }

                    // Calculate trend strength using linear regression
                    $months = [];
                    $quantities = [];
                    $i = 0;
                    foreach ($monthly_data as $month => $data) {
                        $months[] = $i++;
                        $quantities[] = $data['quantity'];
                    }

                    // Calculate linear regression
                    $n = count($months);
                    $sum_x = array_sum($months);
                    $sum_y = array_sum($quantities);
                    $sum_xy = 0;
                    $sum_xx = 0;

                    for ($i = 0; $i < $n; $i++) {
                        $sum_xy += $months[$i] * $quantities[$i];
                        $sum_xx += $months[$i] * $months[$i];
                    }

                    $slope = 0;
                    if (($n * $sum_xx - $sum_x * $sum_x) != 0) {
                        $slope = ($n * $sum_xy - $sum_x * $sum_y) / ($n * $sum_xx - $sum_x * $sum_x);
                    }

                    // Calculate R-squared (coefficient of determination)
                    $mean_y = $sum_y / $n;
                    $ss_tot = 0;
                    $ss_res = 0;

                    for ($i = 0; $i < $n; $i++) {
                        $predicted_y = $slope * $months[$i] + ($sum_y - $slope * $sum_x) / $n;
                        $ss_tot += ($quantities[$i] - $mean_y) * ($quantities[$i] - $mean_y);
                        $ss_res += ($quantities[$i] - $predicted_y) * ($quantities[$i] - $predicted_y);
                    }

                    $r_squared = 0;
                    if ($ss_tot != 0) {
                        $r_squared = 1 - ($ss_res / $ss_tot);
                    }

                    // Determine trend direction and strength
                    $trend_direction = 'Stable';
                    if ($avg_growth_rate > 0.05) {
                        $trend_direction = 'Upward';
                    } elseif ($avg_growth_rate < -0.05) {
                        $trend_direction = 'Downward';
                    }

                    $trend_strength = 'Weak';
                    if (abs($r_squared) > 0.7) {
                        $trend_strength = 'Strong';
                    } elseif (abs($r_squared) > 0.3) {
                        $trend_strength = 'Moderate';
                    }

                    // Determine if this is an emerging trend
                    $is_emerging = false;
                    if ($trend_direction == 'Upward' && $recent_avg_growth > $avg_growth_rate && $trend_acceleration > 0) {
                        $is_emerging = true;
                    }

                    // Add to trend detection results
                    $trend_detection[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'avg_growth_rate' => $avg_growth_rate * 100, // Convert to percentage
                        'recent_growth_rate' => $recent_avg_growth * 100, // Convert to percentage
                        'trend_acceleration' => $trend_acceleration * 100, // Convert to percentage
                        'trend_direction' => $trend_direction,
                        'trend_strength' => $trend_strength,
                        'r_squared' => $r_squared,
                        'is_emerging' => $is_emerging,
                        'monthly_data' => $monthly_data
                    ];
                }
            }

            // Sort trends by recent growth rate (descending)
            usort($trend_detection, function($a, $b) {
                return $b['recent_growth_rate'] <=> $a['recent_growth_rate'];
            });

            // Seasonality Impact Analysis
            $seasonality_analysis = [];

            // Get overall monthly sales patterns
            $monthly_seasonality = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final');

            if (!empty($start_date) && !empty($end_date)) {
                $monthly_seasonality->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $monthly_seasonality->where('transactions.location_id', $location_id);
            }

            if (!empty($product_ids)) {
                $monthly_seasonality->whereIn('transaction_sell_lines.product_id', $product_ids);
            }

            if ($permitted_locations != 'all') {
                $monthly_seasonality->whereIn('transactions.location_id', $permitted_locations);
            }

            $monthly_seasonality = $monthly_seasonality->select(
                DB::raw('MONTH(transactions.transaction_date) as month'),
                DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity'),
                DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) as total_amount')
            )
            ->groupBy(DB::raw('MONTH(transactions.transaction_date)'))
            ->orderBy(DB::raw('MONTH(transactions.transaction_date)'))
            ->get();

            // Calculate monthly seasonality index
            $total_quantity = $monthly_seasonality->sum('total_quantity');
            $avg_monthly_quantity = $total_quantity / max(1, count($monthly_seasonality));

            $monthly_indices = [];
            foreach ($monthly_seasonality as $month_data) {
                $seasonality_index = $avg_monthly_quantity > 0 ? $month_data->total_quantity / $avg_monthly_quantity : 0;
                $month_name = date('F', mktime(0, 0, 0, $month_data->month, 10));

                $monthly_indices[] = [
                    'month' => $month_data->month,
                    'month_name' => $month_name,
                    'total_quantity' => $month_data->total_quantity,
                    'total_amount' => $month_data->total_amount,
                    'seasonality_index' => $seasonality_index
                ];
            }

            // Get quarterly sales patterns
            $quarterly_seasonality = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final');

            if (!empty($start_date) && !empty($end_date)) {
                $quarterly_seasonality->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $quarterly_seasonality->where('transactions.location_id', $location_id);
            }

            if (!empty($product_ids)) {
                $quarterly_seasonality->whereIn('transaction_sell_lines.product_id', $product_ids);
            }

            if ($permitted_locations != 'all') {
                $quarterly_seasonality->whereIn('transactions.location_id', $permitted_locations);
            }

            $quarterly_seasonality = $quarterly_seasonality->select(
                DB::raw('QUARTER(transactions.transaction_date) as quarter'),
                DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity'),
                DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) as total_amount')
            )
            ->groupBy(DB::raw('QUARTER(transactions.transaction_date)'))
            ->orderBy(DB::raw('QUARTER(transactions.transaction_date)'))
            ->get();

            // Calculate quarterly seasonality index
            $total_quarterly_quantity = $quarterly_seasonality->sum('total_quantity');
            $avg_quarterly_quantity = $total_quarterly_quantity / max(1, count($quarterly_seasonality));

            $quarterly_indices = [];
            foreach ($quarterly_seasonality as $quarter_data) {
                $seasonality_index = $avg_quarterly_quantity > 0 ? $quarter_data->total_quantity / $avg_quarterly_quantity : 0;
                $quarter_name = 'Q' . $quarter_data->quarter;

                $quarterly_indices[] = [
                    'quarter' => $quarter_data->quarter,
                    'quarter_name' => $quarter_name,
                    'total_quantity' => $quarter_data->total_quantity,
                    'total_amount' => $quarter_data->total_amount,
                    'seasonality_index' => $seasonality_index
                ];
            }

            // Get day of week patterns
            $day_of_week_seasonality = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final');

            if (!empty($start_date) && !empty($end_date)) {
                $day_of_week_seasonality->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $day_of_week_seasonality->where('transactions.location_id', $location_id);
            }

            if (!empty($product_ids)) {
                $day_of_week_seasonality->whereIn('transaction_sell_lines.product_id', $product_ids);
            }

            if ($permitted_locations != 'all') {
                $day_of_week_seasonality->whereIn('transactions.location_id', $permitted_locations);
            }

            $day_of_week_seasonality = $day_of_week_seasonality->select(
                DB::raw('DAYOFWEEK(transactions.transaction_date) as day_of_week'),
                DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity'),
                DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) as total_amount')
            )
            ->groupBy(DB::raw('DAYOFWEEK(transactions.transaction_date)'))
            ->orderBy(DB::raw('DAYOFWEEK(transactions.transaction_date)'))
            ->get();

            // Calculate day of week seasonality index
            $total_dow_quantity = $day_of_week_seasonality->sum('total_quantity');
            $avg_dow_quantity = $total_dow_quantity / max(1, count($day_of_week_seasonality));

            $day_of_week_indices = [];
            $day_names = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

            foreach ($day_of_week_seasonality as $dow_data) {
                $seasonality_index = $avg_dow_quantity > 0 ? $dow_data->total_quantity / $avg_dow_quantity : 0;
                $day_name = $day_names[$dow_data->day_of_week - 1];

                $day_of_week_indices[] = [
                    'day_of_week' => $dow_data->day_of_week,
                    'day_name' => $day_name,
                    'total_quantity' => $dow_data->total_quantity,
                    'total_amount' => $dow_data->total_amount,
                    'seasonality_index' => $seasonality_index
                ];
            }

            // Product-specific seasonality
            $product_seasonality = [];

            // Get top products for seasonality analysis
            $top_products_query = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final');

            if (!empty($start_date) && !empty($end_date)) {
                $top_products_query->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $top_products_query->where('transactions.location_id', $location_id);
            }

            if (!empty($product_ids)) {
                $top_products_query->whereIn('transaction_sell_lines.product_id', $product_ids);
            }

            if ($permitted_locations != 'all') {
                $top_products_query->whereIn('transactions.location_id', $permitted_locations);
            }

            $top_products = $top_products_query->select(
                'products.id',
                'products.name',
                DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity')
            )
            ->groupBy('products.id')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->get();

            // For each top product, get monthly sales pattern
            foreach ($top_products as $product) {
                $product_monthly_sales = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell')
                    ->where('transactions.status', 'final')
                    ->where('transaction_sell_lines.product_id', $product->id);

                if (!empty($start_date) && !empty($end_date)) {
                    $product_monthly_sales->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
                }

                if (!empty($location_id)) {
                    $product_monthly_sales->where('transactions.location_id', $location_id);
                }

                if ($permitted_locations != 'all') {
                    $product_monthly_sales->whereIn('transactions.location_id', $permitted_locations);
                }

                $product_monthly_sales = $product_monthly_sales->select(
                    DB::raw('MONTH(transactions.transaction_date) as month'),
                    DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity')
                )
                ->groupBy(DB::raw('MONTH(transactions.transaction_date)'))
                ->orderBy(DB::raw('MONTH(transactions.transaction_date)'))
                ->get();

                // Calculate product's monthly seasonality index
                $product_total_quantity = $product_monthly_sales->sum('total_quantity');
                $product_avg_monthly_quantity = $product_total_quantity / max(1, count($product_monthly_sales));

                $product_monthly_indices = [];
                foreach ($product_monthly_sales as $month_data) {
                    $seasonality_index = $product_avg_monthly_quantity > 0 ? $month_data->total_quantity / $product_avg_monthly_quantity : 0;
                    $month_name = date('F', mktime(0, 0, 0, $month_data->month, 10));

                    $product_monthly_indices[] = [
                        'month' => $month_data->month,
                        'month_name' => $month_name,
                        'total_quantity' => $month_data->total_quantity,
                        'seasonality_index' => $seasonality_index
                    ];
                }

                // Determine if product has strong seasonality
                $seasonality_variance = 0;
                if (count($product_monthly_indices) > 0) {
                    $indices = array_column($product_monthly_indices, 'seasonality_index');
                    $seasonality_variance = $this->standardDeviation($indices);
                }

                $has_strong_seasonality = $seasonality_variance > 0.3; // Threshold for strong seasonality

                // Find peak season (months with index > 1.2)
                $peak_season = array_filter($product_monthly_indices, function($item) {
                    return $item['seasonality_index'] > 1.2;
                });

                // Find low season (months with index < 0.8)
                $low_season = array_filter($product_monthly_indices, function($item) {
                    return $item['seasonality_index'] < 0.8;
                });

                $peak_months = array_column($peak_season, 'month_name');
                $low_months = array_column($low_season, 'month_name');

                $product_seasonality[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'monthly_indices' => $product_monthly_indices,
                    'seasonality_variance' => $seasonality_variance,
                    'has_strong_seasonality' => $has_strong_seasonality,
                    'peak_season' => implode(', ', $peak_months),
                    'low_season' => implode(', ', $low_months)
                ];
            }

            // Combine all seasonality data
            $seasonality_analysis = [
                'monthly_indices' => $monthly_indices,
                'quarterly_indices' => $quarterly_indices,
                'day_of_week_indices' => $day_of_week_indices,
                'product_seasonality' => $product_seasonality
            ];

            // Price Elasticity Analysis
            $price_elasticity = [];

            // Get products with price changes
            $products_with_price_changes = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final');

            if (!empty($start_date) && !empty($end_date)) {
                $products_with_price_changes->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $products_with_price_changes->where('transactions.location_id', $location_id);
            }

            if (!empty($product_ids)) {
                $products_with_price_changes->whereIn('transaction_sell_lines.product_id', $product_ids);
            }

            if ($permitted_locations != 'all') {
                $products_with_price_changes->whereIn('transactions.location_id', $permitted_locations);
            }

            $products_with_price_changes = $products_with_price_changes->select(
                'products.id',
                'products.name',
                DB::raw('COUNT(DISTINCT transaction_sell_lines.unit_price_inc_tax) as price_count')
            )
            ->groupBy('products.id')
            ->having('price_count', '>', 1) // Only products with multiple price points
            ->orderBy('price_count', 'desc')
            ->limit(5)
            ->get();

            foreach ($products_with_price_changes as $product) {
                // Get price points and corresponding sales volumes
                $price_points = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell')
                    ->where('transactions.status', 'final')
                    ->where('transaction_sell_lines.product_id', $product->id);

                if (!empty($start_date) && !empty($end_date)) {
                    $price_points->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
                }

                if (!empty($location_id)) {
                    $price_points->where('transactions.location_id', $location_id);
                }

                if ($permitted_locations != 'all') {
                    $price_points->whereIn('transactions.location_id', $permitted_locations);
                }

                $price_points = $price_points->select(
                    'transaction_sell_lines.unit_price_inc_tax as price',
                    DB::raw('SUM(transaction_sell_lines.quantity) as quantity'),
                    DB::raw('COUNT(DISTINCT transactions.id) as transaction_count')
                )
                ->groupBy('transaction_sell_lines.unit_price_inc_tax')
                ->orderBy('price')
                ->get();

                // Need at least 2 price points to calculate elasticity
                if (count($price_points) >= 2) {
                    $elasticity_data = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'price_points' => [],
                        'elasticity' => null
                    ];

                    // Calculate average daily sales at each price point
                    foreach ($price_points as $point) {
                        $elasticity_data['price_points'][] = [
                            'price' => $point->price,
                            'quantity' => $point->quantity,
                            'transaction_count' => $point->transaction_count
                        ];
                    }

                    // Calculate price elasticity if we have at least 2 price points
                    if (count($elasticity_data['price_points']) >= 2) {
                        $point1 = $elasticity_data['price_points'][0];
                        $point2 = $elasticity_data['price_points'][count($elasticity_data['price_points']) - 1];

                        $price1 = $point1['price'];
                        $price2 = $point2['price'];
                        $quantity1 = $point1['quantity'];
                        $quantity2 = $point2['quantity'];

                        // Avoid division by zero
                        if ($price1 > 0 && $price2 > 0 && $quantity1 > 0 && $quantity2 > 0) {
                            $avg_price = ($price1 + $price2) / 2;
                            $avg_quantity = ($quantity1 + $quantity2) / 2;

                            $price_change_percent = ($price2 - $price1) / $price1;
                            $quantity_change_percent = ($quantity2 - $quantity1) / $quantity1;

                            // Price elasticity formula: % change in quantity / % change in price
                            if ($price_change_percent != 0) {
                                $elasticity = $quantity_change_percent / $price_change_percent;
                                $elasticity_data['elasticity'] = $elasticity;

                                // Interpret elasticity
                                if (abs($elasticity) > 1) {
                                    $elasticity_data['interpretation'] = 'Elastic (Sensitive to price changes)';
                                } elseif (abs($elasticity) < 1) {
                                    $elasticity_data['interpretation'] = 'Inelastic (Less sensitive to price changes)';
                                } else {
                                    $elasticity_data['interpretation'] = 'Unit Elastic';
                                }

                                // Optimal price recommendation (simplified)
                                if ($elasticity < -1) {
                                    // Elastic demand, consider lowering price
                                    $elasticity_data['price_recommendation'] = 'Consider lowering price to increase revenue';
                                } elseif ($elasticity > -1 && $elasticity < 0) {
                                    // Inelastic demand, consider raising price
                                    $elasticity_data['price_recommendation'] = 'Consider raising price to increase revenue';
                                } else {
                                    $elasticity_data['price_recommendation'] = 'Current pricing appears optimal';
                                }
                            }
                        }
                    }

                    $price_elasticity[] = $elasticity_data;
                }
            }

            // Prepare data for the view
            $data = [
                'sales_trends' => $sales_trends,
                'monthly_sales' => $monthly_sales,
                'product_mix' => $product_mix,
                'customer_behavior' => $customer_behavior,
                'cross_sell_products' => $cross_sell_products,
                'cross_sell_recommendations' => $cross_sell_recommendations,
                'product_bundles' => $product_bundles,
                'discount_impact' => $discount_impact,
                'profitability' => $profitability,
                'purchase_trends' => $purchase_trends,
                'monthly_purchases' => $monthly_purchases,
                'supplier_performance' => $supplier_performance,
                'product_purchases' => $product_purchases,
                'demand_supply_gap' => $demand_supply_gap,
                'stock_turnover' => $stock_turnover,
                'category_performance' => $category_performance,
                'brand_performance' => $brand_performance,
                'inventory_aging' => $inventory_aging,
                'seasonal_trends' => $seasonal_trends,
                'location_performance' => $location_performance,
                'sales_forecast' => $sales_forecast,
                'product_forecasts' => $product_forecasts,
                'lifecycle_analysis' => $lifecycle_analysis,
                'trend_detection' => $trend_detection,
                'price_elasticity' => $price_elasticity,
                'seasonality_analysis' => $seasonality_analysis
            ];

            // Return JSON data for dashboard if requested
            if ($return_json) {
                return response()->json($data);
            }

            return view('report.partials.product_advance_analytics_details', compact('data'))->render();
        }

        $business_locations = BusinessLocation::forDropdown($business_id, true);
        $products = Product::where('business_id', $business_id)
                        ->select('id', DB::raw("CONCAT(name, ' - ', sku) as name"))
                        ->orderBy('name')
                        ->pluck('name', 'id');

        return view('report.product_advance_analytics', compact('business_locations', 'products'));
    }
    /**
     * Calculate standard deviation
     *
     * @param array $array
     * @return float
     */
    private function standardDeviation(array $array): float
    {
        $n = count($array);
        if ($n === 0) {
            return 0.0;
        }

        $mean = array_sum($array) / $n;
        $variance = 0.0;

        if (isset($array) && !empty($array)) {
            foreach ($array as $value) {
                $variance += pow(($value - $mean), 2);
            }
        }

        return sqrt($variance / $n);
    }

    /**
     * Calculate correlation coefficient
     *
     * @param array $x
     * @param array $y
     * @return float
     */
    private function correlation(array $x, array $y): float
    {
        $n = count($x);
        if ($n !== count($y) || $n === 0) {
            return 0.0;
        }

        $sum_x = array_sum($x);
        $sum_y = array_sum($y);
        $sum_xy = 0;
        $sum_x2 = 0;
        $sum_y2 = 0;

        if (isset($x) && isset($y) && !empty($x) && !empty($y)) {
            for ($i = 0; $i < $n; $i++) {
                $sum_xy += ($x[$i] * $y[$i]);
                $sum_x2 += ($x[$i] * $x[$i]);
                $sum_y2 += ($y[$i] * $y[$i]);
            }
        }

        $numerator = $n * $sum_xy - $sum_x * $sum_y;
        $denominator = sqrt(($n * $sum_x2 - $sum_x * $sum_x) * ($n * $sum_y2 - $sum_y * $sum_y));

        if ($denominator === 0) {
            return 0.0;
        }

        return $numerator / $denominator;
    }

    /**
     * Interpret correlation coefficient
     *
     * @param float $correlation
     * @return string
     */
    private function interpretCorrelation(float $correlation): string
    {
        $abs = abs($correlation);

        if ($abs >= 0.9) {
            $strength = 'Very strong';
        } elseif ($abs >= 0.7) {
            $strength = 'Strong';
        } elseif ($abs >= 0.5) {
            $strength = 'Moderate';
        } elseif ($abs >= 0.3) {
            $strength = 'Weak';
        } else {
            $strength = 'Very weak or no';
        }

        $direction = $correlation > 0 ? 'positive' : ($correlation < 0 ? 'negative' : 'no');

        return "$strength $direction correlation";
    }

    /**
     * Shows purchase advance analytics for a specific supplier
     *
     * @param int $supplier_id
     * @return \Illuminate\Http\Response
     */
    public function getSupplierPurchaseAdvanceAnalytics($supplier_id)
    {
        if (! auth()->user()->can('profit_loss_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $contact = Contact::where('business_id', $business_id)->find($supplier_id);

        if (!$contact || !in_array($contact->type, ['supplier', 'both'])) {
            abort(404, 'Supplier not found');
        }

        // Set the supplier_id in the request
        request()->merge(['supplier_id' => $supplier_id]);

        // Get the data
        $data = $this->getPurchaseAdvanceAnalyticsData(request());

        return view('contact.partials.purchase_advance_analytics_tab', compact('data', 'contact'));
    }

    /**
     * Get purchase advance analytics data
     *
     * @param Request $request
     * @return array
     */
    private function getPurchaseAdvanceAnalyticsData(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $fy = $this->businessUtil->getCurrentFinancialYear($business_id);

        $location_id = ! empty($request->input('location_id')) ? $request->input('location_id') : null;
        $start_date = ! empty($request->input('start_date')) ? $request->input('start_date') : $fy['start'];
        $end_date = ! empty($request->input('end_date')) ? $request->input('end_date') : $fy['end'];
        $supplier_id = ! empty($request->input('supplier_id')) ? $request->input('supplier_id') : null;

        $permitted_locations = auth()->user()->permitted_locations();

        // Base query for purchases
        $base_query = Transaction::where('transactions.business_id', $business_id)
            ->where('transactions.type', 'purchase')
            ->where('transactions.status', 'received');

        if (!empty($start_date) && !empty($end_date)) {
            $base_query->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
        }

        if (!empty($location_id)) {
            $base_query->where('transactions.location_id', $location_id);
        }

        if (!empty($supplier_id)) {
            $base_query->where('transactions.contact_id', $supplier_id);
        }

        if ($permitted_locations != 'all') {
            $base_query->whereIn('transactions.location_id', $permitted_locations);
        }

        // Monthly purchases
        $monthly_purchases_query = clone $base_query;
        $monthly_purchases = $monthly_purchases_query->select(
            DB::raw('DATE(transactions.transaction_date) as date'),
            DB::raw('MONTH(transactions.transaction_date) as month'),
            DB::raw('YEAR(transactions.transaction_date) as year'),
            DB::raw('SUM(transactions.final_total) as total_purchase'),
            DB::raw('COUNT(transactions.id) as transaction_count')
        )
        ->groupBy(DB::raw('DATE(transactions.transaction_date)'), DB::raw('MONTH(transactions.transaction_date)'), DB::raw('YEAR(transactions.transaction_date)'))
        ->orderBy(DB::raw('DATE(transactions.transaction_date)'))
        ->orderBy(DB::raw('YEAR(transactions.transaction_date)'))
        ->orderBy(DB::raw('MONTH(transactions.transaction_date)'))
        ->get();

        // Quarterly purchases
        $quarterly_purchases_query = clone $base_query;
        $quarterly_purchases = $quarterly_purchases_query->select(
            DB::raw('QUARTER(transactions.transaction_date) as quarter'),
            DB::raw('YEAR(transactions.transaction_date) as year'),
            DB::raw('SUM(transactions.final_total) as total_purchase'),
            DB::raw('COUNT(transactions.id) as transaction_count')
        )
        ->groupBy(DB::raw('QUARTER(transactions.transaction_date)'), DB::raw('YEAR(transactions.transaction_date)'))
        ->orderBy(DB::raw('YEAR(transactions.transaction_date)'))
        ->orderBy(DB::raw('QUARTER(transactions.transaction_date)'))
        ->get();

        // Yearly purchases
        $yearly_purchases_query = clone $base_query;
        $yearly_purchases = $yearly_purchases_query->select(
            DB::raw('YEAR(transactions.transaction_date) as year'),
            DB::raw('SUM(transactions.final_total) as total_purchase'),
            DB::raw('COUNT(transactions.id) as transaction_count')
        )
        ->groupBy(DB::raw('YEAR(transactions.transaction_date)'))
        ->orderBy(DB::raw('YEAR(transactions.transaction_date)'))
        ->get();

        // Top suppliers
        $top_suppliers_query = Contact::leftJoin('transactions as t', function ($join) use ($start_date, $end_date) {
            $join->on('contacts.id', '=', 't.contact_id')
                ->where('t.type', 'purchase')
                ->where('t.status', 'received');

            if (!empty($start_date) && !empty($end_date)) {
                $join->whereBetween(DB::raw('date(t.transaction_date)'), [$start_date, $end_date]);
            }
        })
        ->where('contacts.business_id', $business_id)
        ->whereIn('contacts.type', ['supplier', 'both']);

        if (!empty($location_id)) {
            $top_suppliers_query->where('t.location_id', $location_id);
        }

        if (!empty($supplier_id)) {
            $top_suppliers_query->where('contacts.id', $supplier_id);
        }

        if ($permitted_locations != 'all') {
            $top_suppliers_query->whereIn('t.location_id', $permitted_locations);
        }

        $top_suppliers = $top_suppliers_query->select(
            'contacts.id',
            'contacts.name',
            'contacts.supplier_business_name',
            DB::raw('SUM(t.final_total) as total_purchase'),
            DB::raw('COUNT(t.id) as transaction_count')
        )
        ->groupBy('contacts.id')
        ->orderBy('total_purchase', 'desc')
        ->limit(10)
        ->get();

        // Top purchased products
        $top_products_query = PurchaseLine::join('transactions as t', 'purchase_lines.transaction_id', '=', 't.id')
            ->join('products as p', 'purchase_lines.product_id', '=', 'p.id')
            ->leftJoin('categories as c', 'p.category_id', '=', 'c.id')
            ->where('t.business_id', $business_id)
            ->where('t.type', 'purchase')
            ->where('t.status', 'received');

        if (!empty($start_date) && !empty($end_date)) {
            $top_products_query->whereBetween(DB::raw('date(t.transaction_date)'), [$start_date, $end_date]);
        }

        if (!empty($location_id)) {
            $top_products_query->where('t.location_id', $location_id);
        }

        if (!empty($supplier_id)) {
            $top_products_query->where('t.contact_id', $supplier_id);
        }

        if ($permitted_locations != 'all') {
            $top_products_query->whereIn('t.location_id', $permitted_locations);
        }

        $top_products = $top_products_query->select(
            'p.id',
            'p.name as product_name',
            'c.name as category_name',
            DB::raw('SUM(purchase_lines.quantity) as total_quantity'),
            DB::raw('SUM(purchase_lines.quantity * purchase_lines.purchase_price_inc_tax) as total_amount')
        )
        ->groupBy('p.id')
        ->orderBy('total_amount', 'desc')
        ->limit(10)
        ->get();

        // Payment methods
        $payment_methods_query = TransactionPayment::join('transactions as t', 'transaction_payments.transaction_id', '=', 't.id')
            ->where('t.business_id', $business_id)
            ->where('t.type', 'purchase')
            ->where('t.status', 'received');

        if (!empty($start_date) && !empty($end_date)) {
            $payment_methods_query->whereBetween(DB::raw('date(t.transaction_date)'), [$start_date, $end_date]);
        }

        if (!empty($location_id)) {
            $payment_methods_query->where('t.location_id', $location_id);
        }

        if (!empty($supplier_id)) {
            $payment_methods_query->where('t.contact_id', $supplier_id);
        }

        if ($permitted_locations != 'all') {
            $payment_methods_query->whereIn('t.location_id', $permitted_locations);
        }

        $payment_methods = $payment_methods_query->select(
            'transaction_payments.method',
            DB::raw('COUNT(transaction_payments.id) as count'),
            DB::raw('SUM(transaction_payments.amount) as total_amount')
        )
        ->groupBy('transaction_payments.method')
        ->orderBy('total_amount', 'desc')
        ->get();

        // Purchase returns
        $purchase_returns_query = Transaction::where('business_id', $business_id)
            ->where('type', 'purchase_return');

        if (!empty($start_date) && !empty($end_date)) {
            $purchase_returns_query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
        }

        if (!empty($location_id)) {
            $purchase_returns_query->where('location_id', $location_id);
        }

        if (!empty($supplier_id)) {
            $purchase_returns_query->where('contact_id', $supplier_id);
        }

        if ($permitted_locations != 'all') {
            $purchase_returns_query->whereIn('location_id', $permitted_locations);
        }

        $purchase_returns = $purchase_returns_query->select(
            DB::raw('MONTH(transaction_date) as month'),
            DB::raw('YEAR(transaction_date) as year'),
            DB::raw('SUM(final_total) as total_return'),
            DB::raw('COUNT(id) as return_count')
        )
        ->groupBy(DB::raw('MONTH(transaction_date)'), DB::raw('YEAR(transaction_date)'))
        ->orderBy(DB::raw('YEAR(transaction_date)'))
        ->orderBy(DB::raw('MONTH(transaction_date)'))
        ->get();

        return [
            'monthly_purchases' => $monthly_purchases,
            'quarterly_purchases' => $quarterly_purchases,
            'yearly_purchases' => $yearly_purchases,
            'top_suppliers' => $top_suppliers,
            'top_products' => $top_products,
            'payment_methods' => $payment_methods,
            'purchase_returns' => $purchase_returns
        ];
    }

    /**
     * Shows business advance analytics report
     *
     * @return \Illuminate\Http\Response
     */
    public function getBusinessAdvanceAnalytics(Request $request)
    {
        if (! auth()->user()->can('profit_loss_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $fy = $this->businessUtil->getCurrentFinancialYear($business_id);

            $location_id = ! empty($request->input('location_id')) ? $request->input('location_id') : null;
            $start_date = ! empty($request->input('start_date')) ? $request->input('start_date') : $fy['start'];
            $end_date = ! empty($request->input('end_date')) ? $request->input('end_date') : $fy['end'];

            $permitted_locations = auth()->user()->permitted_locations();

            // Sales Overview Data
            $sales_query = Transaction::where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final');

            if (!empty($start_date) && !empty($end_date)) {
                $sales_query->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $sales_query->where('transactions.location_id', $location_id);
            }

            if ($permitted_locations != 'all') {
                $sales_query->whereIn('transactions.location_id', $permitted_locations);
            }

            // Total Sales
            $total_sales = $sales_query->count();

            // Total Revenue
            $total_revenue = $sales_query->sum('final_total');

            // Average Order Value
            $average_order_value = $total_sales > 0 ? $total_revenue / $total_sales : 0;

            // Total Customers
            $total_customers = Contact::where('business_id', $business_id)
                ->where('type', 'customer')
                ->count();

            // Sales Trend Data
            $sales_trend = $sales_query->select(
                DB::raw('DATE(transactions.transaction_date) as date'),
                DB::raw('SUM(transactions.final_total) as total_sales')
            )
            ->groupBy(DB::raw('DATE(transactions.transaction_date)'))
            ->orderBy(DB::raw('DATE(transactions.transaction_date)'))
            ->get();

            $sales_trend_labels = $sales_trend->pluck('date')->toArray();
            $sales_trend_data = $sales_trend->pluck('total_sales')->toArray();

            // Sales Distribution by Category
            $sales_distribution = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final');

            if (!empty($start_date) && !empty($end_date)) {
                $sales_distribution->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $sales_distribution->where('transactions.location_id', $location_id);
            }

            if ($permitted_locations != 'all') {
                $sales_distribution->whereIn('transactions.location_id', $permitted_locations);
            }

            $sales_distribution = $sales_distribution->select(
                'categories.name as category_name',
                DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) as total_amount')
            )
            ->groupBy('categories.id')
            ->orderBy('total_amount', 'desc')
            ->get();

            $sales_distribution_labels = $sales_distribution->pluck('category_name')->toArray();
            $sales_distribution_data = $sales_distribution->pluck('total_amount')->toArray();

            // Revenue Analysis Data
            // Gross Revenue (same as total_revenue)
            $gross_revenue = $total_revenue;

            // Net Revenue (after discounts)
            $net_revenue = $sales_query->sum(DB::raw('final_total - tax_amount'));

            // Revenue Growth (compared to previous period)
            $previous_period_start = date('Y-m-d', strtotime($start_date . ' -1 year'));
            $previous_period_end = date('Y-m-d', strtotime($end_date . ' -1 year'));

            $previous_revenue = Transaction::where('business_id', $business_id)
                ->where('type', 'sell')
                ->where('status', 'final')
                ->whereBetween(DB::raw('date(transaction_date)'), [$previous_period_start, $previous_period_end]);

            if (!empty($location_id)) {
                $previous_revenue->where('location_id', $location_id);
            }

            if ($permitted_locations != 'all') {
                $previous_revenue->whereIn('location_id', $permitted_locations);
            }

            $previous_revenue = $previous_revenue->sum('final_total');
            $revenue_growth = $previous_revenue > 0 ? (($total_revenue - $previous_revenue) / $previous_revenue) * 100 : 100;

            // Monthly Recurring Revenue
            $monthly_recurring_revenue = $total_revenue / (strtotime($end_date) - strtotime($start_date)) * 30 * 24 * 60 * 60;

            // Profit Margins Data
            // Cost of Goods Sold
            $cogs = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                ->leftJoin('transaction_sell_lines_purchase_lines as tspl', 'transaction_sell_lines.id', '=', 'tspl.sell_line_id')
                ->leftJoin('purchase_lines', 'tspl.purchase_line_id', '=', 'purchase_lines.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final');

            if (!empty($start_date) && !empty($end_date)) {
                $cogs->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $cogs->where('transactions.location_id', $location_id);
            }

            if ($permitted_locations != 'all') {
                $cogs->whereIn('transactions.location_id', $permitted_locations);
            }

            $cogs = $cogs->sum(DB::raw('transaction_sell_lines.quantity * purchase_lines.purchase_price_inc_tax'));

            // Gross Profit
            $gross_profit = $gross_revenue - $cogs;

            // Gross Profit Margin
            $gross_profit_margin = $gross_revenue > 0 ? ($gross_profit / $gross_revenue) * 100 : 0;

            // Expenses
            $expenses = Transaction::where('business_id', $business_id)
                ->where('type', 'expense')
                ->where('payment_status', 'paid');

            if (!empty($start_date) && !empty($end_date)) {
                $expenses->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $expenses->where('location_id', $location_id);
            }

            if ($permitted_locations != 'all') {
                $expenses->whereIn('location_id', $permitted_locations);
            }

            $total_expenses = $expenses->sum('final_total');

            // Net Profit
            $net_profit = $gross_profit - $total_expenses;

            // Net Profit Margin
            $net_profit_margin = $gross_revenue > 0 ? ($net_profit / $gross_revenue) * 100 : 0;

            // Profit Growth
            $previous_profit = Transaction::where('business_id', $business_id)
                ->where('type', 'sell')
                ->where('status', 'final')
                ->whereBetween(DB::raw('date(transaction_date)'), [$previous_period_start, $previous_period_end]);

            if (!empty($location_id)) {
                $previous_profit->where('location_id', $location_id);
            }

            if ($permitted_locations != 'all') {
                $previous_profit->whereIn('location_id', $permitted_locations);
            }

            $previous_profit_amount = $previous_profit->sum('final_total');
            $previous_cogs = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                ->leftJoin('transaction_sell_lines_purchase_lines as tspl', 'transaction_sell_lines.id', '=', 'tspl.sell_line_id')
                ->leftJoin('purchase_lines', 'tspl.purchase_line_id', '=', 'purchase_lines.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final')
                ->whereBetween(DB::raw('date(transactions.transaction_date)'), [$previous_period_start, $previous_period_end]);

            if (!empty($location_id)) {
                $previous_cogs->where('transactions.location_id', $location_id);
            }

            if ($permitted_locations != 'all') {
                $previous_cogs->whereIn('transactions.location_id', $permitted_locations);
            }

            $previous_cogs_amount = $previous_cogs->sum(DB::raw('transaction_sell_lines.quantity * purchase_lines.purchase_price_inc_tax'));
            $previous_gross_profit = $previous_profit_amount - $previous_cogs_amount;

            $previous_expenses = Transaction::where('business_id', $business_id)
                ->where('type', 'expense')
                ->where('payment_status', 'paid')
                ->whereBetween(DB::raw('date(transaction_date)'), [$previous_period_start, $previous_period_end]);

            if (!empty($location_id)) {
                $previous_expenses->where('location_id', $location_id);
            }

            if ($permitted_locations != 'all') {
                $previous_expenses->whereIn('location_id', $permitted_locations);
            }

            $previous_expenses_amount = $previous_expenses->sum('final_total');
            $previous_net_profit = $previous_gross_profit - $previous_expenses_amount;

            $profit_growth = $previous_net_profit > 0 ? (($net_profit - $previous_net_profit) / $previous_net_profit) * 100 : 100;

            // Inventory Performance Data
            // Inventory Value
            $inventory_value = Product::join('variations', 'products.id', '=', 'variations.product_id')
                ->join('variation_location_details', 'variations.id', '=', 'variation_location_details.variation_id')
                ->where('products.business_id', $business_id);

            if (!empty($location_id)) {
                $inventory_value->where('variation_location_details.location_id', $location_id);
            }

            if ($permitted_locations != 'all') {
                $inventory_value->whereIn('variation_location_details.location_id', $permitted_locations);
            }

            $inventory_value = $inventory_value->select(
                DB::raw('SUM(variation_location_details.qty_available * variations.default_purchase_price) as total_value')
            )
            ->first();

            $inventory_value = $inventory_value ? $inventory_value->total_value : 0;

            // Inventory Turnover
            $inventory_turnover = $inventory_value > 0 ? $cogs / $inventory_value : 0;

            // Days in Inventory
            $days_in_inventory = $inventory_turnover > 0 ? 365 / $inventory_turnover : 0;

            // Stock-outs
            $stockouts = Product::join('variations', 'products.id', '=', 'variations.product_id')
                ->join('variation_location_details', 'variations.id', '=', 'variation_location_details.variation_id')
                ->where('products.business_id', $business_id)
                ->where('variation_location_details.qty_available', '<=', 0);

            if (!empty($location_id)) {
                $stockouts->where('variation_location_details.location_id', $location_id);
            }

            if ($permitted_locations != 'all') {
                $stockouts->whereIn('variation_location_details.location_id', $permitted_locations);
            }

            $stockouts = $stockouts->count();

            // Customer Insights Data
            // New Customers (customers created within the selected date range)
            $new_customers = Contact::where('business_id', $business_id)
                ->where('type', 'customer')
                ->whereBetween(DB::raw('date(created_at)'), [$start_date, $end_date])
                ->count();

            // Customer Acquisition Trend
            $customer_acquisition_trend = Contact::where('business_id', $business_id)
                ->where('type', 'customer')
                ->whereBetween(DB::raw('date(created_at)'), [$start_date, $end_date])
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(id) as new_customers')
                )
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy(DB::raw('DATE(created_at)'))
                ->get();

            $customer_acquisition_labels = $customer_acquisition_trend->pluck('date')->toArray();
            $customer_acquisition_data = $customer_acquisition_trend->pluck('new_customers')->toArray();

            // Repeat Purchase Rate
            $total_customers_with_purchases = DB::table('transactions')
                ->where('business_id', $business_id)
                ->where('type', 'sell')
                ->where('status', 'final')
                ->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date])
                ->distinct('contact_id')
                ->count('contact_id');

            $customers_with_multiple_purchases = DB::table('transactions')
                ->where('business_id', $business_id)
                ->where('type', 'sell')
                ->where('status', 'final')
                ->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date])
                ->select('contact_id', DB::raw('COUNT(id) as purchase_count'))
                ->groupBy('contact_id')
                ->havingRaw('COUNT(id) > 1')
                ->count();

            $repeat_purchase_rate = $total_customers_with_purchases > 0 
                ? ($customers_with_multiple_purchases / $total_customers_with_purchases) * 100 
                : 0;

            // Average Customer Value
            $avg_customer_value = $total_customers > 0 ? $total_revenue / $total_customers : 0;

            // Customer Retention Data
            // For simplicity, we'll calculate retention at different time intervals
            $retention_periods = [
                '1 month' => date('Y-m-d', strtotime($end_date . ' -1 month')),
                '3 months' => date('Y-m-d', strtotime($end_date . ' -3 months')),
                '6 months' => date('Y-m-d', strtotime($end_date . ' -6 months')),
                '1 year' => date('Y-m-d', strtotime($end_date . ' -1 year'))
            ];

            $retention_data = [];
            $retention_labels = [];

            foreach ($retention_periods as $label => $period_start) {
                $retention_labels[] = $label;

                // Customers who made a purchase in the period
                $customers_in_period = DB::table('transactions')
                    ->where('business_id', $business_id)
                    ->where('type', 'sell')
                    ->where('status', 'final')
                    ->whereBetween(DB::raw('date(transaction_date)'), [$period_start, $end_date])
                    ->distinct('contact_id')
                    ->pluck('contact_id')
                    ->toArray();

                // Customers who made a purchase in the last month of the selected date range
                $customers_in_last_month = DB::table('transactions')
                    ->where('business_id', $business_id)
                    ->where('type', 'sell')
                    ->where('status', 'final')
                    ->whereBetween(DB::raw('date(transaction_date)'), [date('Y-m-d', strtotime($end_date . ' -1 month')), $end_date])
                    ->distinct('contact_id')
                    ->pluck('contact_id')
                    ->toArray();

                // Calculate retention rate
                $retained_customers = count(array_intersect($customers_in_period, $customers_in_last_month));
                $retention_rate = count($customers_in_period) > 0 
                    ? ($retained_customers / count($customers_in_period)) * 100 
                    : 0;

                $retention_data[] = $retention_rate;
            }

            // Product Performance Data
            // Total Products
            $total_products = Product::where('business_id', $business_id)
                ->count();

            // Best Selling Product
            $best_selling_product = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final')
                ->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);

            if (!empty($location_id)) {
                $best_selling_product->where('transactions.location_id', $location_id);
            }

            if ($permitted_locations != 'all') {
                $best_selling_product->whereIn('transactions.location_id', $permitted_locations);
            }

            $best_selling_product = $best_selling_product->select(
                'products.name as product_name',
                DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity')
            )
            ->groupBy('products.id')
            ->orderBy('total_quantity', 'desc')
            ->first();

            // Top Selling Products
            $top_selling_products = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final')
                ->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);

            if (!empty($location_id)) {
                $top_selling_products->where('transactions.location_id', $location_id);
            }

            if ($permitted_locations != 'all') {
                $top_selling_products->whereIn('transactions.location_id', $permitted_locations);
            }

            $top_selling_products = $top_selling_products->select(
                'products.name as product_name',
                DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity')
            )
            ->groupBy('products.id')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->get();

            $top_products_labels = $top_selling_products->pluck('product_name')->toArray();
            $top_products_data = $top_selling_products->pluck('total_quantity')->toArray();

            // Average Product Margin
            $avg_product_margin = $gross_profit_margin; // Using the same as overall gross profit margin for simplicity

            // Products Sold
            $products_sold = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final')
                ->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);

            if (!empty($location_id)) {
                $products_sold->where('transactions.location_id', $location_id);
            }

            if ($permitted_locations != 'all') {
                $products_sold->whereIn('transactions.location_id', $permitted_locations);
            }

            $products_sold = $products_sold->sum('transaction_sell_lines.quantity');

            // Expense Analysis Data
            // Expense Ratio
            $expense_ratio = $total_revenue > 0 ? ($total_expenses / $total_revenue) * 100 : 0;

            // Expense Growth
            $previous_expenses = Transaction::where('business_id', $business_id)
                ->where('type', 'expense')
                ->where('payment_status', 'paid')
                ->whereBetween(DB::raw('date(transaction_date)'), [$previous_period_start, $previous_period_end]);

            if (!empty($location_id)) {
                $previous_expenses->where('location_id', $location_id);
            }

            if ($permitted_locations != 'all') {
                $previous_expenses->whereIn('location_id', $permitted_locations);
            }

            $previous_expenses_total = $previous_expenses->sum('final_total');
            $expense_growth = $previous_expenses_total > 0 
                ? (($total_expenses - $previous_expenses_total) / $previous_expenses_total) * 100 
                : 0;

            // Monthly Average Expense
            $days_in_period = (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24);
            $months_in_period = $days_in_period / 30;
            $monthly_avg_expense = $months_in_period > 0 ? $total_expenses / $months_in_period : $total_expenses;

            // Expense Trend
            $expense_trend = Transaction::where('business_id', $business_id)
                ->where('type', 'expense')
                ->where('payment_status', 'paid')
                ->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date])
                ->select(
                    DB::raw('DATE(transaction_date) as date'),
                    DB::raw('SUM(final_total) as total_expense')
                )
                ->groupBy(DB::raw('DATE(transaction_date)'))
                ->orderBy(DB::raw('DATE(transaction_date)'))
                ->get();

            $expense_trend_labels = $expense_trend->pluck('date')->toArray();
            $expense_trend_data = $expense_trend->pluck('total_expense')->toArray();

            // Expense by Category
            $expense_categories = Transaction::where('transactions.business_id', $business_id)
                ->where('transactions.type', 'expense')
                ->where('transactions.payment_status', 'paid')
                ->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date])
                ->leftJoin('expense_categories', 'transactions.expense_category_id', '=', 'expense_categories.id');

            if (!empty($location_id)) {
                $expense_categories->where('transactions.location_id', $location_id);
            }

            if ($permitted_locations != 'all') {
                $expense_categories->whereIn('transactions.location_id', $permitted_locations);
            }

            $expense_categories = $expense_categories->select(
                'expense_categories.name as category_name',
                DB::raw('SUM(transactions.final_total) as total_amount')
            )
            ->groupBy('transactions.expense_category_id')
            ->orderBy('total_amount', 'desc')
            ->get();

            $expense_category_labels = $expense_categories->pluck('category_name')->toArray();
            $expense_category_data = $expense_categories->pluck('total_amount')->toArray();

            // Cash Flow Data
            // Cash Inflow (Sales + Other Income)
            $cash_inflow = Transaction::where('business_id', $business_id)
                ->whereIn('type', ['sell', 'sell_return'])
                ->where('status', 'final')
                ->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);

            if (!empty($location_id)) {
                $cash_inflow->where('location_id', $location_id);
            }

            if ($permitted_locations != 'all') {
                $cash_inflow->whereIn('location_id', $permitted_locations);
            }

            $cash_inflow = $cash_inflow->sum('final_total');

            // Cash Outflow (Purchases + Expenses)
            $cash_outflow = Transaction::where('business_id', $business_id)
                ->whereIn('type', ['purchase', 'expense'])
                ->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);

            if (!empty($location_id)) {
                $cash_outflow->where('location_id', $location_id);
            }

            if ($permitted_locations != 'all') {
                $cash_outflow->whereIn('location_id', $permitted_locations);
            }

            $cash_outflow = $cash_outflow->sum('final_total');

            // Net Cash Flow
            $net_cash_flow = $cash_inflow - $cash_outflow;

            // Cash Runway (in months)
            $monthly_cash_outflow = $months_in_period > 0 ? $cash_outflow / $months_in_period : $cash_outflow;
            $cash_runway = $monthly_cash_outflow > 0 ? $net_cash_flow / $monthly_cash_outflow : 0;

            // Cash Flow Trend
            $cash_flow_trend = [];
            $cash_inflow_trend = [];
            $cash_outflow_trend = [];
            $net_cash_flow_trend = [];

            // Group by month for better visualization
            $cash_inflow_by_month = Transaction::where('business_id', $business_id)
                ->whereIn('type', ['sell', 'sell_return'])
                ->where('status', 'final')
                ->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date])
                ->select(
                    DB::raw('YEAR(transaction_date) as year'),
                    DB::raw('MONTH(transaction_date) as month'),
                    DB::raw('SUM(final_total) as total_amount')
                )
                ->groupBy(DB::raw('YEAR(transaction_date)'), DB::raw('MONTH(transaction_date)'))
                ->orderBy(DB::raw('YEAR(transaction_date)'))
                ->orderBy(DB::raw('MONTH(transaction_date)'))
                ->get();

            $cash_outflow_by_month = Transaction::where('business_id', $business_id)
                ->whereIn('type', ['purchase', 'expense'])
                ->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date])
                ->select(
                    DB::raw('YEAR(transaction_date) as year'),
                    DB::raw('MONTH(transaction_date) as month'),
                    DB::raw('SUM(final_total) as total_amount')
                )
                ->groupBy(DB::raw('YEAR(transaction_date)'), DB::raw('MONTH(transaction_date)'))
                ->orderBy(DB::raw('YEAR(transaction_date)'))
                ->orderBy(DB::raw('MONTH(transaction_date)'))
                ->get();

            // Combine the data
            $months = [];
            foreach ($cash_inflow_by_month as $inflow) {
                $month_key = $inflow->year . '-' . str_pad($inflow->month, 2, '0', STR_PAD_LEFT);
                $months[$month_key]['inflow'] = $inflow->total_amount;
            }

            foreach ($cash_outflow_by_month as $outflow) {
                $month_key = $outflow->year . '-' . str_pad($outflow->month, 2, '0', STR_PAD_LEFT);
                $months[$month_key]['outflow'] = $outflow->total_amount;
            }

            // Calculate net flow and prepare data for charts
            $cash_flow_labels = [];
            $cash_inflow_data = [];
            $cash_outflow_data = [];
            $net_cash_flow_data = [];

            foreach ($months as $month => $data) {
                $cash_flow_labels[] = date('M Y', strtotime($month . '-01'));
                $inflow = $data['inflow'] ?? 0;
                $outflow = $data['outflow'] ?? 0;
                $net_flow = $inflow - $outflow;

                $cash_inflow_data[] = $inflow;
                $cash_outflow_data[] = $outflow;
                $net_cash_flow_data[] = $net_flow;
            }

            // Seasonal Trends Data
            // Monthly Sales for Current Year and Previous Year
            $current_year = date('Y', strtotime($end_date));
            $previous_year = $current_year - 1;

            $monthly_sales_current_year = Transaction::where('business_id', $business_id)
                ->where('type', 'sell')
                ->where('status', 'final')
                ->whereYear('transaction_date', $current_year)
                ->select(
                    DB::raw('MONTH(transaction_date) as month'),
                    DB::raw('SUM(final_total) as total_sales')
                )
                ->groupBy(DB::raw('MONTH(transaction_date)'))
                ->orderBy(DB::raw('MONTH(transaction_date)'))
                ->get()
                ->keyBy('month');

            $monthly_sales_previous_year = Transaction::where('business_id', $business_id)
                ->where('type', 'sell')
                ->where('status', 'final')
                ->whereYear('transaction_date', $previous_year)
                ->select(
                    DB::raw('MONTH(transaction_date) as month'),
                    DB::raw('SUM(final_total) as total_sales')
                )
                ->groupBy(DB::raw('MONTH(transaction_date)'))
                ->orderBy(DB::raw('MONTH(transaction_date)'))
                ->get()
                ->keyBy('month');

            // Prepare data for monthly sales trend chart
            $months_array = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
                7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
            ];

            $monthly_sales_labels = array_values($months_array);
            $monthly_sales_current_year_data = [];
            $monthly_sales_previous_year_data = [];

            foreach ($months_array as $month_num => $month_name) {
                $monthly_sales_current_year_data[] = isset($monthly_sales_current_year[$month_num]) 
                    ? $monthly_sales_current_year[$month_num]->total_sales 
                    : 0;

                $monthly_sales_previous_year_data[] = isset($monthly_sales_previous_year[$month_num]) 
                    ? $monthly_sales_previous_year[$month_num]->total_sales 
                    : 0;
            }

            // Identify peak and slow seasons
            $peak_month_index = array_search(max($monthly_sales_current_year_data), $monthly_sales_current_year_data);
            $slow_month_index = array_search(min(array_filter($monthly_sales_current_year_data)), $monthly_sales_current_year_data);

            $peak_season = $monthly_sales_labels[$peak_month_index];
            $slow_season = $monthly_sales_labels[$slow_month_index];

            // Calculate seasonal variance
            $max_sales = max($monthly_sales_current_year_data);
            $min_sales = min(array_filter($monthly_sales_current_year_data, function($value) { return $value > 0; }));
            $seasonal_variance = $max_sales > 0 ? (($max_sales - $min_sales) / $max_sales) * 100 : 0;

            // Peak Season Growth
            $peak_month_num = $peak_month_index + 1;
            $peak_current_year = isset($monthly_sales_current_year[$peak_month_num]) 
                ? $monthly_sales_current_year[$peak_month_num]->total_sales 
                : 0;
            $peak_previous_year = isset($monthly_sales_previous_year[$peak_month_num]) 
                ? $monthly_sales_previous_year[$peak_month_num]->total_sales 
                : 0;

            $peak_season_growth = $peak_previous_year > 0 
                ? (($peak_current_year - $peak_previous_year) / $peak_previous_year) * 100 
                : 0;

            // Quarterly data for comparison
            $quarterly_sales = Transaction::where('business_id', $business_id)
                ->where('type', 'sell')
                ->where('status', 'final')
                ->whereYear('transaction_date', $current_year)
                ->select(
                    DB::raw('QUARTER(transaction_date) as quarter'),
                    DB::raw('SUM(final_total) as total_sales'),
                    DB::raw('SUM(final_total - tax_amount - shipping_charges) - 
                             SUM(COALESCE(
                                (SELECT SUM(tsl.quantity * pl.purchase_price_inc_tax)
                                FROM transaction_sell_lines tsl
                                LEFT JOIN transaction_sell_lines_purchase_lines tspl ON tsl.id = tspl.sell_line_id
                                LEFT JOIN purchase_lines pl ON tspl.purchase_line_id = pl.id
                                WHERE tsl.transaction_id = transactions.id),
                                0
                             )) as gross_profit')
                )
                ->groupBy(DB::raw('QUARTER(transaction_date)'))
                ->orderBy(DB::raw('QUARTER(transaction_date)'))
                ->get();

            $quarterly_labels = ['Q1', 'Q2', 'Q3', 'Q4'];
            $quarterly_sales_data = [0, 0, 0, 0];
            $quarterly_profit_data = [0, 0, 0, 0];

            foreach ($quarterly_sales as $quarter) {
                $quarterly_sales_data[$quarter->quarter - 1] = $quarter->total_sales;
                $quarterly_profit_data[$quarter->quarter - 1] = $quarter->gross_profit;
            }

            // Business Growth Data
            // Customer Growth
            $current_period_customers = Contact::where('business_id', $business_id)
                ->where('type', 'customer')
                ->whereBetween(DB::raw('date(created_at)'), [$start_date, $end_date])
                ->count();

            $previous_period_customers = Contact::where('business_id', $business_id)
                ->where('type', 'customer')
                ->whereBetween(DB::raw('date(created_at)'), [$previous_period_start, $previous_period_end])
                ->count();

            $customer_growth = $previous_period_customers > 0 
                ? (($current_period_customers - $previous_period_customers) / $previous_period_customers) * 100 
                : 0;

            // Order Growth
            $current_period_orders = Transaction::where('business_id', $business_id)
                ->where('type', 'sell')
                ->where('status', 'final')
                ->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date])
                ->count();

            $previous_period_orders = Transaction::where('business_id', $business_id)
                ->where('type', 'sell')
                ->where('status', 'final')
                ->whereBetween(DB::raw('date(transaction_date)'), [$previous_period_start, $previous_period_end])
                ->count();

            $order_growth = $previous_period_orders > 0 
                ? (($current_period_orders - $previous_period_orders) / $previous_period_orders) * 100 
                : 0;

            // Year over Year Growth Data
            // Get data for the last 5 years
            $current_year = date('Y', strtotime($end_date));
            $years = [];
            $yearly_revenue = [];
            $yearly_profit = [];

            for ($i = 4; $i >= 0; $i--) {
                $year = $current_year - $i;
                $years[] = $year;

                $year_revenue = Transaction::where('business_id', $business_id)
                    ->where('type', 'sell')
                    ->where('status', 'final')
                    ->whereYear('transaction_date', $year)
                    ->sum('final_total');

                $yearly_revenue[] = $year_revenue;

                $year_cogs = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
                    ->leftJoin('transaction_sell_lines_purchase_lines as tspl', 'transaction_sell_lines.id', '=', 'tspl.sell_line_id')
                    ->leftJoin('purchase_lines', 'tspl.purchase_line_id', '=', 'purchase_lines.id')
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell')
                    ->where('transactions.status', 'final')
                    ->whereYear('transactions.transaction_date', $year)
                    ->sum(DB::raw('transaction_sell_lines.quantity * purchase_lines.purchase_price_inc_tax'));

                $year_expenses = Transaction::where('business_id', $business_id)
                    ->where('type', 'expense')
                    ->where('payment_status', 'paid')
                    ->whereYear('transaction_date', $year)
                    ->sum('final_total');

                $year_profit = $year_revenue - $year_cogs - $year_expenses;
                $yearly_profit[] = $year_profit;
            }

            // Growth Metrics for Radar Chart
            $growth_metrics_labels = ['Revenue', 'Customers', 'Orders', 'Products', 'Profit', 'Market Share'];
            $current_year_growth = [
                $revenue_growth,
                $customer_growth,
                $order_growth,
                0, // Product growth (placeholder)
                $profit_growth,
                0  // Market share growth (placeholder)
            ];

            // Employee Performance Data
            // Get top performing employees by sales
            $employee_sales = Transaction::where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final')
                ->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date])
                ->join('users', 'transactions.created_by', '=', 'users.id')
                ->select(
                    'users.id',
                    'users.first_name',
                    'users.last_name',
                    DB::raw('SUM(transactions.final_total) as total_sales')
                )
                ->groupBy('users.id')
                ->orderBy('total_sales', 'desc')
                ->limit(5)
                ->get();

            $employee_names = $employee_sales->map(function ($employee) {
                return $employee->first_name . ' ' . $employee->last_name;
            })->toArray();

            $employee_sales_data = $employee_sales->pluck('total_sales')->toArray();

            // Get total employees
            $total_employees = User::where('business_id', $business_id)->count();

            // Top performer
            $top_performer = !empty($employee_names) ? $employee_names[0] : '';

            // Average sales per employee
            $avg_sales_per_employee = $total_employees > 0 ? $total_revenue / $total_employees : 0;

            // Average transaction time (placeholder - would need transaction timestamps)
            $avg_transaction_time = 8.5; // Placeholder value in minutes

            // Home Dashboard Data
            // Total Sell (same as total_revenue)
            $total_sell = $total_revenue;

            // Invoice Due
            $invoice_due_query = clone $sales_query;
            $invoice_due_query->leftJoin('transaction_payments as tp', 'transactions.id', '=', 'tp.transaction_id')
                ->select(
                    DB::raw('SUM(transactions.final_total) as final_total'),
                    DB::raw('SUM(COALESCE(tp.amount, 0)) as total_paid')
                );
            $invoice_due_result = $invoice_due_query->first();
            $invoice_due = $invoice_due_result->final_total - $invoice_due_result->total_paid;

            // Net (Total Sales - Invoice Due - Expense)
            $net = $total_sell - $invoice_due - $total_expenses;

            // Total Sell Return
            $total_sell_return = Transaction::where('business_id', $business_id)
                ->where('type', 'sell_return')
                ->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);

            if (!empty($location_id)) {
                $total_sell_return->where('location_id', $location_id);
            }

            if ($permitted_locations != 'all') {
                $total_sell_return->whereIn('location_id', $permitted_locations);
            }

            $total_sell_return = $total_sell_return->sum('final_total');

            // Total Purchase
            $total_purchase = Transaction::where('business_id', $business_id)
                ->where('type', 'purchase')
                ->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);

            if (!empty($location_id)) {
                $total_purchase->where('location_id', $location_id);
            }

            if ($permitted_locations != 'all') {
                $total_purchase->whereIn('location_id', $permitted_locations);
            }

            $total_purchase = $total_purchase->sum('final_total');

            // Purchase Due
            $purchase_due_query = Transaction::where('transactions.business_id', $business_id)
                ->where('transactions.type', 'purchase')
                ->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);

            if (!empty($location_id)) {
                $purchase_due_query->where('transactions.location_id', $location_id);
            }

            if ($permitted_locations != 'all') {
                $purchase_due_query->whereIn('transactions.location_id', $permitted_locations);
            }

            $purchase_due_query->leftJoin('transaction_payments as tp', 'transactions.id', '=', 'tp.transaction_id')
                ->select(
                    DB::raw('SUM(transactions.final_total) as final_total'),
                    DB::raw('SUM(COALESCE(tp.amount, 0)) as total_paid')
                );
            $purchase_due_result = $purchase_due_query->first();
            $purchase_due = $purchase_due_result->final_total - $purchase_due_result->total_paid;

            // Total Purchase Return
            $total_purchase_return = Transaction::where('business_id', $business_id)
                ->where('type', 'purchase_return')
                ->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);

            if (!empty($location_id)) {
                $total_purchase_return->where('location_id', $location_id);
            }

            if ($permitted_locations != 'all') {
                $total_purchase_return->whereIn('location_id', $permitted_locations);
            }

            $total_purchase_return = $total_purchase_return->sum('final_total');

            // Prepare data for view
            $data = [
                // Home Dashboard
                'total_sell' => $total_sell,
                'net' => $net,
                'invoice_due' => $invoice_due,
                'total_sell_return' => $total_sell_return,
                'total_purchase' => $total_purchase,
                'purchase_due' => $purchase_due,
                'total_purchase_return' => $total_purchase_return,
                'total_expense' => $total_expenses,

                // Sales Overview
                'total_sales' => $total_sales,
                'total_revenue' => $total_revenue,
                'average_order_value' => $average_order_value,
                'total_customers' => $total_customers,
                'sales_trend_labels' => $sales_trend_labels,
                'sales_trend_data' => $sales_trend_data,
                'sales_distribution_labels' => $sales_distribution_labels,
                'sales_distribution_data' => $sales_distribution_data,

                // Revenue Analysis
                'gross_revenue' => $gross_revenue,
                'net_revenue' => $net_revenue,
                'revenue_growth' => $revenue_growth,
                'monthly_recurring_revenue' => $monthly_recurring_revenue,

                // Profit Margins
                'gross_profit' => $gross_profit,
                'gross_profit_margin' => $gross_profit_margin,
                'net_profit' => $net_profit,
                'net_profit_margin' => $net_profit_margin,
                'total_profit' => $net_profit,
                'profit_growth' => $profit_growth,

                // Inventory Performance
                'inventory_value' => $inventory_value,
                'inventory_turnover' => $inventory_turnover,
                'days_in_inventory' => $days_in_inventory,
                'stockouts' => $stockouts,

                // Customer Insights
                'new_customers' => $new_customers,
                'repeat_purchase_rate' => $repeat_purchase_rate,
                'avg_customer_value' => $avg_customer_value,
                'customer_acquisition_labels' => $customer_acquisition_labels,
                'customer_acquisition_data' => $customer_acquisition_data,
                'retention_labels' => $retention_labels,
                'retention_data' => $retention_data,

                // Product Performance
                'total_products' => $total_products,
                'best_selling_product' => $best_selling_product ? $best_selling_product->product_name : '',
                'avg_product_margin' => $avg_product_margin,
                'products_sold' => $products_sold,
                'top_products_labels' => $top_products_labels,
                'top_products_data' => $top_products_data,

                // Expense Analysis
                'total_expenses' => $total_expenses,
                'expense_ratio' => $expense_ratio,
                'expense_growth' => $expense_growth,
                'monthly_avg_expense' => $monthly_avg_expense,
                'expense_trend_labels' => $expense_trend_labels,
                'expense_trend_data' => $expense_trend_data,
                'expense_category_labels' => $expense_category_labels,
                'expense_category_data' => $expense_category_data,

                // Cash Flow
                'cash_inflow' => $cash_inflow,
                'cash_outflow' => $cash_outflow,
                'net_cash_flow' => $net_cash_flow,
                'cash_runway' => $cash_runway,
                'cash_flow_labels' => $cash_flow_labels,
                'cash_inflow_data' => $cash_inflow_data,
                'cash_outflow_data' => $cash_outflow_data,
                'net_cash_flow_data' => $net_cash_flow_data,

                // Seasonal Trends
                'peak_season' => $peak_season,
                'slow_season' => $slow_season,
                'seasonal_variance' => $seasonal_variance,
                'peak_season_growth' => $peak_season_growth,
                'monthly_sales_labels' => $monthly_sales_labels,
                'monthly_sales_current_year_data' => $monthly_sales_current_year_data,
                'monthly_sales_previous_year_data' => $monthly_sales_previous_year_data,
                'quarterly_labels' => $quarterly_labels,
                'quarterly_sales_data' => $quarterly_sales_data,
                'quarterly_profit_data' => $quarterly_profit_data,

                // Business Growth
                'customer_growth' => $customer_growth,
                'order_growth' => $order_growth,
                'years' => $years,
                'yearly_revenue' => $yearly_revenue,
                'yearly_profit' => $yearly_profit,
                'growth_metrics_labels' => $growth_metrics_labels,
                'current_year_growth' => $current_year_growth,

                // Employee Performance
                'total_employees' => $total_employees,
                'top_performer' => $top_performer,
                'avg_sales_per_employee' => $avg_sales_per_employee,
                'avg_transaction_time' => $avg_transaction_time,
                'employee_names' => $employee_names,
                'employee_sales_data' => $employee_sales_data
            ];

            return view('report.partials.business_advance_analytics_details', compact('data'));
        }

        $business_locations = BusinessLocation::forDropdown($business_id);

        return view('report.business_advance_analytics', compact('business_locations'));
    }

    /**
     * Shows purchase advance analytics report
     *
     * @return \Illuminate\Http\Response
     */
    public function getPurchaseAdvanceAnalytics(Request $request)
    {
        if (! auth()->user()->can('profit_loss_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $fy = $this->businessUtil->getCurrentFinancialYear($business_id);

            $location_id = ! empty($request->input('location_id')) ? $request->input('location_id') : null;
            $start_date = ! empty($request->input('start_date')) ? $request->input('start_date') : $fy['start'];
            $end_date = ! empty($request->input('end_date')) ? $request->input('end_date') : $fy['end'];
            $supplier_id = ! empty($request->input('supplier_id')) ? $request->input('supplier_id') : null;

            $permitted_locations = auth()->user()->permitted_locations();

            // Base query for purchases
            $base_query = Transaction::where('business_id', $business_id)
                ->where('type', 'purchase')
                ->where('status', 'received');

            if (!empty($start_date) && !empty($end_date)) {
                $base_query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $base_query->where('transactions.location_id', $location_id);
            }

            if (!empty($supplier_id)) {
                $base_query->where('transactions.contact_id', $supplier_id);
            }

            if ($permitted_locations != 'all') {
                $base_query->whereIn('transactions.location_id', $permitted_locations);
            }

            // Monthly purchases
            $monthly_purchases_query = clone $base_query;
            $monthly_purchases = $monthly_purchases_query->select(
                DB::raw('DATE(transaction_date) as date'),
                DB::raw('MONTH(transaction_date) as month'),
                DB::raw('YEAR(transaction_date) as year'),
                DB::raw('SUM(final_total) as total_purchase'),
                DB::raw('COUNT(transactions.id) as transaction_count')
            )
            ->groupBy(DB::raw('DATE(transaction_date)'), DB::raw('MONTH(transaction_date)'), DB::raw('YEAR(transaction_date)'))
            ->orderBy(DB::raw('DATE(transaction_date)'))
            ->orderBy(DB::raw('YEAR(transaction_date)'))
            ->orderBy(DB::raw('MONTH(transaction_date)'))
            ->get();

            // Quarterly purchases
            $quarterly_purchases_query = clone $base_query;
            $quarterly_purchases = $quarterly_purchases_query->select(
                DB::raw('QUARTER(transaction_date) as quarter'),
                DB::raw('YEAR(transaction_date) as year'),
                DB::raw('SUM(final_total) as total_purchase'),
                DB::raw('COUNT(transactions.id) as transaction_count')
            )
            ->groupBy(DB::raw('QUARTER(transaction_date)'), DB::raw('YEAR(transaction_date)'))
            ->orderBy(DB::raw('YEAR(transaction_date)'))
            ->orderBy(DB::raw('QUARTER(transaction_date)'))
            ->get();

            // Yearly purchases
            $yearly_purchases_query = clone $base_query;
            $yearly_purchases = $yearly_purchases_query->select(
                DB::raw('YEAR(transaction_date) as year'),
                DB::raw('SUM(final_total) as total_purchase'),
                DB::raw('COUNT(transactions.id) as transaction_count')
            )
            ->groupBy(DB::raw('YEAR(transaction_date)'))
            ->orderBy(DB::raw('YEAR(transaction_date)'))
            ->get();

            // Top suppliers
            $top_suppliers_query = Contact::leftJoin('transactions as t', function ($join) use ($start_date, $end_date) {
                $join->on('contacts.id', '=', 't.contact_id')
                    ->where('t.type', 'purchase')
                    ->where('t.status', 'received');

                if (!empty($start_date) && !empty($end_date)) {
                    $join->whereBetween(DB::raw('date(t.transaction_date)'), [$start_date, $end_date]);
                }
            })
            ->where('contacts.business_id', $business_id)
            ->whereIn('contacts.type', ['supplier', 'both']);

            if (!empty($location_id)) {
                $top_suppliers_query->where('t.location_id', $location_id);
            }

            if (!empty($supplier_id)) {
                $top_suppliers_query->where('contacts.id', $supplier_id);
            }

            if ($permitted_locations != 'all') {
                $top_suppliers_query->whereIn('t.location_id', $permitted_locations);
            }

            $top_suppliers = $top_suppliers_query->select(
                'contacts.id',
                'contacts.name',
                'contacts.supplier_business_name',
                DB::raw('SUM(t.final_total) as total_purchase'),
                DB::raw('COUNT(t.id) as transaction_count')
            )
            ->groupBy('contacts.id')
            ->orderBy('total_purchase', 'desc')
            ->limit(10)
            ->get();

            // Top purchased products
            $top_products_query = PurchaseLine::join('transactions as t', 'purchase_lines.transaction_id', '=', 't.id')
                ->join('products as p', 'purchase_lines.product_id', '=', 'p.id')
                ->leftJoin('categories as c', 'p.category_id', '=', 'c.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'purchase')
                ->where('t.status', 'received');

            if (!empty($start_date) && !empty($end_date)) {
                $top_products_query->whereBetween(DB::raw('date(t.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $top_products_query->where('t.location_id', $location_id);
            }

            if (!empty($supplier_id)) {
                $top_products_query->where('t.contact_id', $supplier_id);
            }

            if ($permitted_locations != 'all') {
                $top_products_query->whereIn('t.location_id', $permitted_locations);
            }

            $top_products = $top_products_query->select(
                'p.id',
                'p.name as product_name',
                'c.name as category_name',
                DB::raw('SUM(purchase_lines.quantity) as total_quantity'),
                DB::raw('SUM(purchase_lines.quantity * purchase_lines.purchase_price_inc_tax) as total_amount')
            )
            ->groupBy('p.id')
            ->orderBy('total_amount', 'desc')
            ->limit(10)
            ->get();

            // Payment methods
            $payment_methods_query = TransactionPayment::join('transactions as t', 'transaction_payments.transaction_id', '=', 't.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'purchase')
                ->where('t.status', 'received');

            if (!empty($start_date) && !empty($end_date)) {
                $payment_methods_query->whereBetween(DB::raw('date(t.transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $payment_methods_query->where('t.location_id', $location_id);
            }

            if (!empty($supplier_id)) {
                $payment_methods_query->where('t.contact_id', $supplier_id);
            }

            if ($permitted_locations != 'all') {
                $payment_methods_query->whereIn('t.location_id', $permitted_locations);
            }

            $payment_methods = $payment_methods_query->select(
                'transaction_payments.method',
                DB::raw('COUNT(transaction_payments.id) as count'),
                DB::raw('SUM(transaction_payments.amount) as total_amount')
            )
            ->groupBy('transaction_payments.method')
            ->orderBy('total_amount', 'desc')
            ->get();

            // Purchase returns
            $purchase_returns_query = Transaction::where('business_id', $business_id)
                ->where('type', 'purchase_return');

            if (!empty($start_date) && !empty($end_date)) {
                $purchase_returns_query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            }

            if (!empty($location_id)) {
                $purchase_returns_query->where('location_id', $location_id);
            }

            if (!empty($supplier_id)) {
                $purchase_returns_query->where('contact_id', $supplier_id);
            }

            if ($permitted_locations != 'all') {
                $purchase_returns_query->whereIn('location_id', $permitted_locations);
            }

            $purchase_returns = $purchase_returns_query->select(
                DB::raw('MONTH(transaction_date) as month'),
                DB::raw('YEAR(transaction_date) as year'),
                DB::raw('SUM(final_total) as total_return'),
                DB::raw('COUNT(id) as return_count')
            )
            ->groupBy(DB::raw('MONTH(transaction_date)'), DB::raw('YEAR(transaction_date)'))
            ->orderBy(DB::raw('YEAR(transaction_date)'))
            ->orderBy(DB::raw('MONTH(transaction_date)'))
            ->get();

            $data = [
                'monthly_purchases' => $monthly_purchases,
                'quarterly_purchases' => $quarterly_purchases,
                'yearly_purchases' => $yearly_purchases,
                'top_suppliers' => $top_suppliers,
                'top_products' => $top_products,
                'payment_methods' => $payment_methods,
                'purchase_returns' => $purchase_returns
            ];

            return view('report.partials.purchase_advance_analytics_details', compact('data'))->render();
        }

        $business_locations = BusinessLocation::forDropdown($business_id, true);
        $suppliers = Contact::where('business_id', $business_id)
                        ->whereIn('type', ['supplier', 'both'])
                        ->select('id', DB::raw("IF(supplier_business_name IS NULL OR supplier_business_name='', name, CONCAT(name, ' (', supplier_business_name, ')')) as name"))
                        ->orderBy('name')
                        ->pluck('name', 'id');

        return view('report.purchase_advance_analytics', compact('business_locations', 'suppliers'));
    }
}
