<?php

namespace App\Http\Controllers;

use App\Brands;
use App\BusinessLocation;
use App\Category;
use App\Charts\CommonChart;
use App\Contact;
use App\Product;
use App\PurchaseLine;
use App\SupplyChainVehicle;
use App\Transaction;
use App\TransactionPayment;
use App\TransactionSellLine;
use App\TransactionSellLinesPurchaseLines;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Variation;
use Datatables;
use DB;
use Illuminate\Http\Request;

class SupplyChainAnalyticsController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $transactionUtil;
    protected $productUtil;
    protected $moduleUtil;
    protected $businessUtil;

    /**
     * Constructor
     *
     * @param TransactionUtil $transactionUtil
     * @param ProductUtil $productUtil
     * @param ModuleUtil $moduleUtil
     * @param BusinessUtil $businessUtil
     * @return void
     */
    public function __construct(
        TransactionUtil $transactionUtil,
        ProductUtil $productUtil,
        ModuleUtil $moduleUtil,
        BusinessUtil $businessUtil
    ) {
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->businessUtil = $businessUtil;
    }

    /**
     * Shows supply chain analytics dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (! auth()->user()->can('profit_loss_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $location_id = $request->get('location_id');
            $product_ids = $request->get('product_ids');
            $supplier_ids = $request->get('supplier_ids');
            $customer_ids = $request->get('customer_ids');

            $fy = $this->businessUtil->getCurrentFinancialYear($business_id);

            $location_id = !empty(request()->input('location_id')) ? request()->input('location_id') : null;
            $start_date = !empty(request()->input('start_date')) ? request()->input('start_date') : $fy['start'];
            $end_date = !empty(request()->input('end_date')) ? request()->input('end_date') : $fy['end'];
            $product_ids = !empty(request()->input('product_ids')) ? request()->input('product_ids') : [];
            $supplier_ids = !empty(request()->input('supplier_ids')) ? request()->input('supplier_ids') : [];
            $customer_ids = !empty(request()->input('customer_ids')) ? request()->input('customer_ids') : [];

            $permitted_locations = auth()->user()->permitted_locations();

            // 1. Sales Analytics
            $data['sales_analytics'] = $this->getSalesAnalytics(
                $business_id, 
                $start_date, 
                $end_date, 
                $location_id, 
                $customer_ids, 
                $product_ids, 
                $permitted_locations
            );

            // 2. Purchase Analytics
            $data['purchase_analytics'] = $this->getPurchaseAnalytics(
                $business_id, 
                $start_date, 
                $end_date, 
                $location_id, 
                $supplier_ids, 
                $product_ids, 
                $permitted_locations
            );

            // 3. Sales vs. Purchase Alignment
            $data['sales_purchase_alignment'] = $this->getSalesPurchaseAlignment(
                $business_id, 
                $start_date, 
                $end_date, 
                $location_id, 
                $product_ids, 
                $permitted_locations
            );

            // 4. Margin & Profitability Analytics
            $data['margin_analytics'] = $this->getMarginAnalytics(
                $business_id, 
                $start_date, 
                $end_date, 
                $location_id, 
                $product_ids, 
                $customer_ids, 
                $permitted_locations
            );

            // 5. Predictive & Prescriptive Analytics
            $data['predictive_analytics'] = $this->getPredictiveAnalytics(
                $business_id, 
                $start_date, 
                $end_date, 
                $location_id, 
                $product_ids, 
                $permitted_locations
            );

            return view('report.partials.supply_chain_analytics_details', compact('data'))->render();
        }

        $business_locations = BusinessLocation::forDropdown($business_id, true);
        $suppliers = Contact::where('business_id', $business_id)
                        ->whereIn('type', ['supplier', 'both'])
                        ->select('id', DB::raw("IF(contacts.contact_id IS NULL OR contacts.contact_id='', name, CONCAT(name, ' - ', contacts.contact_id)) as name"))
                        ->orderBy('name')
                        ->pluck('name', 'id');
        $customers = Contact::where('business_id', $business_id)
                        ->whereIn('type', ['customer', 'both'])
                        ->select('id', DB::raw("IF(contacts.contact_id IS NULL OR contacts.contact_id='', name, CONCAT(name, ' - ', contacts.contact_id)) as name"))
                        ->orderBy('name')
                        ->pluck('name', 'id');
        $products = Product::where('business_id', $business_id)
                        ->select('id', 'name')
                        ->orderBy('name')
                        ->pluck('name', 'id');

        return view('report.supply_chain_analytics', compact('business_locations', 'suppliers', 'customers', 'products'));
    }

    /**
     * Get sales analytics data
     *
     * @param int $business_id
     * @param string $start_date
     * @param string $end_date
     * @param int $location_id
     * @param array $customer_ids
     * @param array $product_ids
     * @param array $permitted_locations
     * @return array
     */
    private function getSalesAnalytics($business_id, $start_date, $end_date, $location_id, $customer_ids, $product_ids, $permitted_locations)
    {
        // Get sales data
        $query = Transaction::where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final');

        if (!empty($start_date) && !empty($end_date)) {
            $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
        }

        if (!empty($location_id)) {
            $query->where('location_id', $location_id);
        }

        if (!empty($customer_ids)) {
            $query->whereIn('contact_id', $customer_ids);
        }

        if ($permitted_locations != 'all') {
            $query->whereIn('location_id', $permitted_locations);
        }

        // Sales Trends - Revenue & volume by month/quarter/year
        $sales_trends = $query->select(
            DB::raw('DATE(transaction_date) as date'),
            DB::raw('SUM(final_total) as total_sales'),
            DB::raw('COUNT(*) as transaction_count')
        )
        ->groupBy(DB::raw('DATE(transaction_date)'))
        ->orderBy('date')
        ->get();

        // Monthly sales
        $monthly_sales = $query->select(
            DB::raw('MONTH(transaction_date) as month'),
            DB::raw('YEAR(transaction_date) as year'),
            DB::raw('SUM(final_total) as total_sales'),
            DB::raw('COUNT(*) as transaction_count')
        )
        ->groupBy(DB::raw('MONTH(transaction_date)'), DB::raw('YEAR(transaction_date)'))
        ->orderBy('year')
        ->orderBy('month')
        ->get();

        // Product Mix - Top-selling lubricants
        $product_mix = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final');

        if (!empty($start_date) && !empty($end_date)) {
            $product_mix->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
        }

        if (!empty($location_id)) {
            $product_mix->where('transactions.location_id', $location_id);
        }

        if (!empty($customer_ids)) {
            $product_mix->whereIn('transactions.contact_id', $customer_ids);
        }

        if (!empty($product_ids)) {
            $product_mix->whereIn('products.id', $product_ids);
        }

        if ($permitted_locations != 'all') {
            $product_mix->whereIn('transactions.location_id', $permitted_locations);
        }

        $product_mix = $product_mix->select(
            'products.id',
            'products.name as product_name',
            'categories.name as category_name',
            DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity'),
            DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) as total_amount')
        )
        ->groupBy('products.id')
        ->orderBy('total_amount', 'desc')
        ->limit(20)
        ->get();

        // Customer Behavior (RFM) - Loyal vs. at-risk vs. lost customers
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

        // Cross-sell & Bundle Analysis - Products sold together
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

        if (!empty($product_ids)) {
            $cross_sell_products->where(function ($query) use ($product_ids) {
                $query->whereIn('tsl1.product_id', $product_ids)
                    ->orWhereIn('tsl2.product_id', $product_ids);
            });
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

        // Discount Impact - Sales uplift vs. margin reduction
        $discount_impact = Transaction::where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final');

        if (!empty($start_date) && !empty($end_date)) {
            $discount_impact->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
        }

        if (!empty($location_id)) {
            $discount_impact->where('location_id', $location_id);
        }

        if (!empty($customer_ids)) {
            $discount_impact->whereIn('contact_id', $customer_ids);
        }

        if ($permitted_locations != 'all') {
            $discount_impact->whereIn('location_id', $permitted_locations);
        }

        $discount_impact = $discount_impact->select(
            'contact_id',
            'contacts.name as customer_name',
            DB::raw('SUM(discount_amount) as total_discount'),
            DB::raw('SUM(final_total) as total_sales'),
            DB::raw('(SUM(discount_amount) / SUM(final_total + discount_amount)) * 100 as discount_percentage')
        )
        ->join('contacts', 'transactions.contact_id', '=', 'contacts.id')
        ->groupBy('contact_id')
        ->orderBy('total_discount', 'desc')
        ->limit(10)
        ->get();

        // Churn Prediction - Decline in purchase frequency or volume
        $current_date = \Carbon\Carbon::now();
        $churn_threshold_days = 90; // Customers who haven't purchased in 90 days are at risk
        
        $churn_prediction = Contact::leftJoin('transactions as t', function ($join) use ($start_date, $end_date) {
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
            $churn_prediction->where('t.location_id', $location_id);
        }

        if (!empty($customer_ids)) {
            $churn_prediction->whereIn('contacts.id', $customer_ids);
        }

        if ($permitted_locations != 'all') {
            $churn_prediction->whereIn('t.location_id', $permitted_locations);
        }

        $churn_prediction = $churn_prediction->select(
            'contacts.id',
            'contacts.name',
            DB::raw('MAX(t.transaction_date) as last_purchase_date'),
            DB::raw('DATEDIFF(NOW(), MAX(t.transaction_date)) as days_since_last_purchase'),
            DB::raw('COUNT(t.id) as purchase_count'),
            DB::raw('SUM(t.final_total) as total_spent')
        )
        ->havingRaw('days_since_last_purchase > ?', [$churn_threshold_days])
        ->groupBy('contacts.id')
        ->orderBy('days_since_last_purchase', 'desc')
        ->limit(10)
        ->get();

        // Profitability by Customer/Product
        $profitability = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
            ->leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final');

        if (!empty($start_date) && !empty($end_date)) {
            $profitability->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
        }

        if (!empty($location_id)) {
            $profitability->where('transactions.location_id', $location_id);
        }

        if (!empty($customer_ids)) {
            $profitability->whereIn('transactions.contact_id', $customer_ids);
        }

        if (!empty($product_ids)) {
            $profitability->whereIn('products.id', $product_ids);
        }

        if ($permitted_locations != 'all') {
            $profitability->whereIn('transactions.location_id', $permitted_locations);
        }

        $profitability_by_product = $profitability->select(
            'products.id',
            'products.name as product_name',
            DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity'),
            DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) as total_sales'),
            DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.purchase_price_inc_tax) as total_cost'),
            DB::raw('SUM(transaction_sell_lines.quantity * (transaction_sell_lines.unit_price_inc_tax - transaction_sell_lines.purchase_price_inc_tax)) as gross_profit'),
            DB::raw('(SUM(transaction_sell_lines.quantity * (transaction_sell_lines.unit_price_inc_tax - transaction_sell_lines.purchase_price_inc_tax)) / SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax)) * 100 as profit_margin')
        )
        ->groupBy('products.id')
        ->orderBy('gross_profit', 'desc')
        ->limit(10)
        ->get();

        $profitability_by_customer = $profitability->select(
            'contacts.id',
            'contacts.name as customer_name',
            DB::raw('COUNT(DISTINCT transactions.id) as transaction_count'),
            DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) as total_sales'),
            DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.purchase_price_inc_tax) as total_cost'),
            DB::raw('SUM(transaction_sell_lines.quantity * (transaction_sell_lines.unit_price_inc_tax - transaction_sell_lines.purchase_price_inc_tax)) as gross_profit'),
            DB::raw('(SUM(transaction_sell_lines.quantity * (transaction_sell_lines.unit_price_inc_tax - transaction_sell_lines.purchase_price_inc_tax)) / SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax)) * 100 as profit_margin')
        )
        ->groupBy('contacts.id')
        ->orderBy('gross_profit', 'desc')
        ->limit(10)
        ->get();

        return [
            'sales_trends' => $sales_trends,
            'monthly_sales' => $monthly_sales,
            'product_mix' => $product_mix,
            'customers' => $customers,
            'cross_sell_products' => $cross_sell_products,
            'discount_impact' => $discount_impact,
            'churn_prediction' => $churn_prediction,
            'profitability_by_product' => $profitability_by_product,
            'profitability_by_customer' => $profitability_by_customer
        ];
    }

    /**
     * Get purchase analytics data
     *
     * @param int $business_id
     * @param string $start_date
     * @param string $end_date
     * @param int $location_id
     * @param array $supplier_ids
     * @param array $product_ids
     * @param array $permitted_locations
     * @return array
     */
    private function getPurchaseAnalytics($business_id, $start_date, $end_date, $location_id, $supplier_ids, $product_ids, $permitted_locations)
    {
        // Get purchase data
        $query = Transaction::where('business_id', $business_id)
            ->where('type', 'purchase')
            ->where('status', 'received');

        if (!empty($start_date) && !empty($end_date)) {
            $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
        }

        if (!empty($location_id)) {
            $query->where('location_id', $location_id);
        }

        if (!empty($supplier_ids)) {
            $query->whereIn('contact_id', $supplier_ids);
        }

        if ($permitted_locations != 'all') {
            $query->whereIn('location_id', $permitted_locations);
        }

        // Purchase Trends - Spend & volume over time
        $purchase_trends = $query->select(
            DB::raw('DATE(transaction_date) as date'),
            DB::raw('SUM(final_total) as total_purchase'),
            DB::raw('COUNT(*) as transaction_count')
        )
        ->groupBy(DB::raw('DATE(transaction_date)'))
        ->orderBy('date')
        ->get();

        // Monthly purchases
        $monthly_purchases = $query->select(
            DB::raw('MONTH(transaction_date) as month'),
            DB::raw('YEAR(transaction_date) as year'),
            DB::raw('SUM(final_total) as total_purchase'),
            DB::raw('COUNT(*) as transaction_count')
        )
        ->groupBy(DB::raw('MONTH(transaction_date)'), DB::raw('YEAR(transaction_date)'))
        ->orderBy('year')
        ->orderBy('month')
        ->get();

        // Supplier Performance - Spend concentration, delivery reliability, price trends
        $supplier_performance = Transaction::where('business_id', $business_id)
            ->where('type', 'purchase')
            ->where('status', 'received')
            ->join('contacts', 'transactions.contact_id', '=', 'contacts.id');

        if (!empty($start_date) && !empty($end_date)) {
            $supplier_performance->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
        }

        if (!empty($location_id)) {
            $supplier_performance->where('transactions.location_id', $location_id);
        }

        if (!empty($supplier_ids)) {
            $supplier_performance->whereIn('transactions.contact_id', $supplier_ids);
        }

        if ($permitted_locations != 'all') {
            $supplier_performance->whereIn('transactions.location_id', $permitted_locations);
        }

        $supplier_performance = $supplier_performance->select(
            'contacts.id',
            'contacts.name as supplier_name',
            DB::raw('COUNT(transactions.id) as transaction_count'),
            DB::raw('SUM(transactions.final_total) as total_purchase'),
            DB::raw('AVG(DATEDIFF(transactions.transaction_date, transactions.created_at)) as avg_lead_time')
        )
        ->groupBy('contacts.id')
        ->orderBy('total_purchase', 'desc')
        ->limit(10)
        ->get();

        // Product-Level Purchase - Most purchased SKUs by spend & volume
        $product_purchases = PurchaseLine::join('transactions', 'purchase_lines.transaction_id', '=', 'transactions.id')
            ->join('products', 'purchase_lines.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'purchase')
            ->where('transactions.status', 'received');

        if (!empty($start_date) && !empty($end_date)) {
            $product_purchases->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
        }

        if (!empty($location_id)) {
            $product_purchases->where('transactions.location_id', $location_id);
        }

        if (!empty($supplier_ids)) {
            $product_purchases->whereIn('transactions.contact_id', $supplier_ids);
        }

        if (!empty($product_ids)) {
            $product_purchases->whereIn('products.id', $product_ids);
        }

        if ($permitted_locations != 'all') {
            $product_purchases->whereIn('transactions.location_id', $permitted_locations);
        }

        $product_purchases = $product_purchases->select(
            'products.id',
            'products.name as product_name',
            'categories.name as category_name',
            DB::raw('SUM(purchase_lines.quantity) as total_quantity'),
            DB::raw('SUM(purchase_lines.quantity * purchase_lines.purchase_price_inc_tax) as total_amount'),
            DB::raw('AVG(purchase_lines.purchase_price_inc_tax) as avg_price')
        )
        ->groupBy('products.id')
        ->orderBy('total_amount', 'desc')
        ->limit(20)
        ->get();

        // Payment Terms & Credit Usage - DPO, cash vs. credit split
        $payment_terms = TransactionPayment::join('transactions as t', 't.id', '=', 'transaction_payments.transaction_id')
            ->where('t.business_id', $business_id)
            ->where('t.type', 'purchase')
            ->where('t.status', 'received');

        if (!empty($start_date) && !empty($end_date)) {
            $payment_terms->whereBetween(DB::raw('date(t.transaction_date)'), [$start_date, $end_date]);
        }

        if (!empty($location_id)) {
            $payment_terms->where('t.location_id', $location_id);
        }

        if (!empty($supplier_ids)) {
            $payment_terms->whereIn('t.contact_id', $supplier_ids);
        }

        if ($permitted_locations != 'all') {
            $payment_terms->whereIn('t.location_id', $permitted_locations);
        }

        $payment_terms = $payment_terms->select(
            'method',
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(amount) as total_amount'),
            DB::raw('AVG(DATEDIFF(transaction_payments.paid_on, t.transaction_date)) as avg_days_to_pay')
        )
        ->groupBy('method')
        ->orderBy('total_amount', 'desc')
        ->get();

        // Cost Trend Analysis - Identify products with rising costs
        $cost_trends = PurchaseLine::join('transactions', 'purchase_lines.transaction_id', '=', 'transactions.id')
            ->join('products', 'purchase_lines.product_id', '=', 'products.id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'purchase')
            ->where('transactions.status', 'received');

        if (!empty($start_date) && !empty($end_date)) {
            $cost_trends->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
        }

        if (!empty($location_id)) {
            $cost_trends->where('transactions.location_id', $location_id);
        }

        if (!empty($supplier_ids)) {
            $cost_trends->whereIn('transactions.contact_id', $supplier_ids);
        }

        if (!empty($product_ids)) {
            $cost_trends->whereIn('products.id', $product_ids);
        }

        if ($permitted_locations != 'all') {
            $cost_trends->whereIn('transactions.location_id', $permitted_locations);
        }

        // Get first and last purchase price for each product
        $cost_trends = $cost_trends->select(
            'products.id',
            'products.name as product_name',
            DB::raw('MIN(transactions.transaction_date) as first_purchase_date'),
            DB::raw('MAX(transactions.transaction_date) as last_purchase_date'),
            DB::raw('(SELECT purchase_price_inc_tax FROM purchase_lines pl JOIN transactions t ON pl.transaction_id = t.id WHERE pl.product_id = products.id AND t.transaction_date = MIN(transactions.transaction_date) LIMIT 1) as first_purchase_price'),
            DB::raw('(SELECT purchase_price_inc_tax FROM purchase_lines pl JOIN transactions t ON pl.transaction_id = t.id WHERE pl.product_id = products.id AND t.transaction_date = MAX(transactions.transaction_date) LIMIT 1) as last_purchase_price'),
            DB::raw('AVG(purchase_lines.purchase_price_inc_tax) as avg_purchase_price')
        )
        ->groupBy('products.id')
        ->havingRaw('last_purchase_price > first_purchase_price')
        ->orderByRaw('(last_purchase_price - first_purchase_price) / first_purchase_price DESC')
        ->limit(10)
        ->get();

        return [
            'purchase_trends' => $purchase_trends,
            'monthly_purchases' => $monthly_purchases,
            'supplier_performance' => $supplier_performance,
            'product_purchases' => $product_purchases,
            'payment_terms' => $payment_terms,
            'cost_trends' => $cost_trends
        ];
    }

    /**
     * Get sales vs purchase alignment data
     *
     * @param int $business_id
     * @param string $start_date
     * @param string $end_date
     * @param int $location_id
     * @param array $product_ids
     * @param array $permitted_locations
     * @return array
     */
    private function getSalesPurchaseAlignment($business_id, $start_date, $end_date, $location_id, $product_ids, $permitted_locations)
    {
        // Demand vs. Supply Gap - Compare sales vs. purchase volumes
        $sales_query = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final');

        $purchase_query = PurchaseLine::join('transactions', 'purchase_lines.transaction_id', '=', 'transactions.id')
            ->join('products', 'purchase_lines.product_id', '=', 'products.id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'purchase')
            ->where('transactions.status', 'received');

        if (!empty($start_date) && !empty($end_date)) {
            $sales_query->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
            $purchase_query->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
        }

        if (!empty($location_id)) {
            $sales_query->where('transactions.location_id', $location_id);
            $purchase_query->where('transactions.location_id', $location_id);
        }

        if (!empty($product_ids)) {
            $sales_query->whereIn('products.id', $product_ids);
            $purchase_query->whereIn('products.id', $product_ids);
        }

        if ($permitted_locations != 'all') {
            $sales_query->whereIn('transactions.location_id', $permitted_locations);
            $purchase_query->whereIn('transactions.location_id', $permitted_locations);
        }

        $sales_data = $sales_query->select(
            'products.id',
            'products.name as product_name',
            DB::raw('SUM(transaction_sell_lines.quantity) as total_sold')
        )
        ->groupBy('products.id')
        ->get()
        ->keyBy('id');

        $purchase_data = $purchase_query->select(
            'products.id',
            'products.name as product_name',
            DB::raw('SUM(purchase_lines.quantity) as total_purchased')
        )
        ->groupBy('products.id')
        ->get()
        ->keyBy('id');

        // Combine sales and purchase data
        $demand_supply_gap = [];
        $all_product_ids = array_unique(array_merge(array_keys($sales_data->toArray()), array_keys($purchase_data->toArray())));

        foreach ($all_product_ids as $product_id) {
            $sales_qty = isset($sales_data[$product_id]) ? $sales_data[$product_id]->total_sold : 0;
            $purchase_qty = isset($purchase_data[$product_id]) ? $purchase_data[$product_id]->total_purchased : 0;
            $gap = $purchase_qty - $sales_qty;
            $product_name = isset($sales_data[$product_id]) ? $sales_data[$product_id]->product_name : $purchase_data[$product_id]->product_name;

            $demand_supply_gap[] = [
                'id' => $product_id,
                'product_name' => $product_name,
                'total_sold' => $sales_qty,
                'total_purchased' => $purchase_qty,
                'gap' => $gap,
                'status' => $gap > 0 ? 'Overstocked' : ($gap < 0 ? 'Understocked' : 'Balanced')
            ];
        }

        // Sort by gap (absolute value) in descending order
        usort($demand_supply_gap, function ($a, $b) {
            return abs($b['gap']) - abs($a['gap']);
        });

        // Limit to top 20
        $demand_supply_gap = array_slice($demand_supply_gap, 0, 20);

        // Stock Turnover Rate - Fast-moving vs. slow-moving SKUs
        $stock_turnover = [];
        foreach ($demand_supply_gap as $item) {
            if ($item['total_purchased'] > 0) {
                $turnover_rate = $item['total_sold'] / $item['total_purchased'];
                $stock_turnover[] = [
                    'id' => $item['id'],
                    'product_name' => $item['product_name'],
                    'total_sold' => $item['total_sold'],
                    'total_purchased' => $item['total_purchased'],
                    'turnover_rate' => $turnover_rate,
                    'category' => $turnover_rate > 1 ? 'Fast-moving' : ($turnover_rate > 0.5 ? 'Medium-moving' : 'Slow-moving')
                ];
            }
        }

        // Sort by turnover rate in descending order
        usort($stock_turnover, function ($a, $b) {
            return $b['turnover_rate'] - $a['turnover_rate'];
        });

        // Purchase-to-Sales Ratio - Are purchases aligned with actual demand?
        $purchase_sales_ratio = [];
        foreach ($demand_supply_gap as $item) {
            if ($item['total_sold'] > 0) {
                $ratio = $item['total_purchased'] / $item['total_sold'];
                $purchase_sales_ratio[] = [
                    'id' => $item['id'],
                    'product_name' => $item['product_name'],
                    'total_sold' => $item['total_sold'],
                    'total_purchased' => $item['total_purchased'],
                    'ratio' => $ratio,
                    'status' => $ratio > 1.2 ? 'Over-purchasing' : ($ratio < 0.8 ? 'Under-purchasing' : 'Balanced')
                ];
            }
        }

        // Sort by ratio (deviation from 1) in descending order
        usort($purchase_sales_ratio, function ($a, $b) {
            return abs($b['ratio'] - 1) - abs($a['ratio'] - 1);
        });

        // Lead Time Effect - Supplier delays impacting sales fulfillment
        $lead_time_effect = Transaction::where('business_id', $business_id)
            ->where('type', 'purchase')
            ->where('status', 'received')
            ->join('contacts', 'transactions.contact_id', '=', 'contacts.id');

        if (!empty($start_date) && !empty($end_date)) {
            $lead_time_effect->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
        }

        if (!empty($location_id)) {
            $lead_time_effect->where('transactions.location_id', $location_id);
        }

        if ($permitted_locations != 'all') {
            $lead_time_effect->whereIn('transactions.location_id', $permitted_locations);
        }

        $lead_time_effect = $lead_time_effect->select(
            'contacts.id',
            'contacts.name as supplier_name',
            DB::raw('AVG(DATEDIFF(transactions.transaction_date, transactions.created_at)) as avg_lead_time'),
            DB::raw('COUNT(transactions.id) as transaction_count')
        )
        ->groupBy('contacts.id')
        ->orderBy('avg_lead_time', 'desc')
        ->limit(10)
        ->get();

        return [
            'demand_supply_gap' => $demand_supply_gap,
            'stock_turnover' => $stock_turnover,
            'purchase_sales_ratio' => $purchase_sales_ratio,
            'lead_time_effect' => $lead_time_effect
        ];
    }

    /**
     * Get margin and profitability analytics data
     *
     * @param int $business_id
     * @param string $start_date
     * @param string $end_date
     * @param int $location_id
     * @param array $product_ids
     * @param array $customer_ids
     * @param array $permitted_locations
     * @return array
     */
    private function getMarginAnalytics($business_id, $start_date, $end_date, $location_id, $product_ids, $customer_ids, $permitted_locations)
    {
        // Gross Margin by Product = Sales price – Purchase cost
        $gross_margin = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final');

        if (!empty($start_date) && !empty($end_date)) {
            $gross_margin->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
        }

        if (!empty($location_id)) {
            $gross_margin->where('transactions.location_id', $location_id);
        }

        if (!empty($customer_ids)) {
            $gross_margin->whereIn('transactions.contact_id', $customer_ids);
        }

        if (!empty($product_ids)) {
            $gross_margin->whereIn('products.id', $product_ids);
        }

        if ($permitted_locations != 'all') {
            $gross_margin->whereIn('transactions.location_id', $permitted_locations);
        }

        $gross_margin = $gross_margin->select(
            'products.id',
            'products.name as product_name',
            DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity'),
            DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) as total_sales'),
            DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.purchase_price_inc_tax) as total_cost'),
            DB::raw('SUM(transaction_sell_lines.quantity * (transaction_sell_lines.unit_price_inc_tax - transaction_sell_lines.purchase_price_inc_tax)) as gross_margin'),
            DB::raw('(SUM(transaction_sell_lines.quantity * (transaction_sell_lines.unit_price_inc_tax - transaction_sell_lines.purchase_price_inc_tax)) / SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax)) * 100 as margin_percentage')
        )
        ->groupBy('products.id')
        ->orderBy('margin_percentage', 'desc')
        ->limit(20)
        ->get();

        // Margin Leakage - Discounts or supplier price hikes reducing margins
        $margin_leakage = Transaction::where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->where('discount_amount', '>', 0);

        if (!empty($start_date) && !empty($end_date)) {
            $margin_leakage->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
        }

        if (!empty($location_id)) {
            $margin_leakage->where('location_id', $location_id);
        }

        if (!empty($customer_ids)) {
            $margin_leakage->whereIn('contact_id', $customer_ids);
        }

        if ($permitted_locations != 'all') {
            $margin_leakage->whereIn('location_id', $permitted_locations);
        }

        $margin_leakage = $margin_leakage->select(
            'id',
            'invoice_no',
            'transaction_date',
            'contact_id',
            'final_total',
            'discount_amount',
            'discount_type',
            DB::raw('(discount_amount / (final_total + discount_amount)) * 100 as discount_percentage')
        )
        ->orderBy('discount_percentage', 'desc')
        ->limit(10)
        ->get();

        // Profitability by Customer - High sales but low margin customers
        $customer_profitability = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
            ->join('contacts', 'transactions.contact_id', '=', 'contacts.id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final');

        if (!empty($start_date) && !empty($end_date)) {
            $customer_profitability->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
        }

        if (!empty($location_id)) {
            $customer_profitability->where('transactions.location_id', $location_id);
        }

        if (!empty($customer_ids)) {
            $customer_profitability->whereIn('transactions.contact_id', $customer_ids);
        }

        if (!empty($product_ids)) {
            $customer_profitability->whereIn('transaction_sell_lines.product_id', $product_ids);
        }

        if ($permitted_locations != 'all') {
            $customer_profitability->whereIn('transactions.location_id', $permitted_locations);
        }

        $customer_profitability = $customer_profitability->select(
            'contacts.id',
            'contacts.name as customer_name',
            DB::raw('COUNT(DISTINCT transactions.id) as transaction_count'),
            DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) as total_sales'),
            DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.purchase_price_inc_tax) as total_cost'),
            DB::raw('SUM(transaction_sell_lines.quantity * (transaction_sell_lines.unit_price_inc_tax - transaction_sell_lines.purchase_price_inc_tax)) as gross_profit'),
            DB::raw('(SUM(transaction_sell_lines.quantity * (transaction_sell_lines.unit_price_inc_tax - transaction_sell_lines.purchase_price_inc_tax)) / SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax)) * 100 as profit_margin')
        )
        ->groupBy('contacts.id')
        ->orderBy('total_sales', 'desc')
        ->limit(10)
        ->get();

        // Break-even Analysis - Sales volume required to cover purchase costs
        $break_even = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final');

        if (!empty($start_date) && !empty($end_date)) {
            $break_even->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
        }

        if (!empty($location_id)) {
            $break_even->where('transactions.location_id', $location_id);
        }

        if (!empty($product_ids)) {
            $break_even->whereIn('products.id', $product_ids);
        }

        if ($permitted_locations != 'all') {
            $break_even->whereIn('transactions.location_id', $permitted_locations);
        }

        $break_even = $break_even->select(
            'products.id',
            'products.name as product_name',
            DB::raw('AVG(transaction_sell_lines.unit_price_inc_tax) as avg_selling_price'),
            DB::raw('AVG(transaction_sell_lines.purchase_price_inc_tax) as avg_purchase_price'),
            DB::raw('AVG(transaction_sell_lines.unit_price_inc_tax - transaction_sell_lines.purchase_price_inc_tax) as avg_profit_per_unit'),
            DB::raw('SUM(transaction_sell_lines.quantity) as total_quantity_sold')
        )
        ->groupBy('products.id')
        ->havingRaw('avg_profit_per_unit > 0')
        ->orderBy('total_quantity_sold', 'desc')
        ->limit(10)
        ->get();

        // Calculate break-even point for each product
        foreach ($break_even as $product) {
            // Assuming fixed costs are 10% of total purchase costs (this is a simplification)
            $fixed_costs = $product->avg_purchase_price * $product->total_quantity_sold * 0.1;
            $product->fixed_costs = $fixed_costs;
            $product->break_even_units = $fixed_costs / $product->avg_profit_per_unit;
            $product->break_even_revenue = $product->break_even_units * $product->avg_selling_price;
        }

        return [
            'gross_margin' => $gross_margin,
            'margin_leakage' => $margin_leakage,
            'customer_profitability' => $customer_profitability,
            'break_even' => $break_even
        ];
    }

    /**
     * Get predictive and prescriptive analytics data
     *
     * @param int $business_id
     * @param string $start_date
     * @param string $end_date
     * @param int $location_id
     * @param array $product_ids
     * @param array $permitted_locations
     * @return array
     */
    private function getPredictiveAnalytics($business_id, $start_date, $end_date, $location_id, $product_ids, $permitted_locations)
    {
        // Demand Forecasting - Predict lubricant demand by segment/product
        // Using simple moving average method for forecasting
        $sales_history = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final');

        if (!empty($start_date) && !empty($end_date)) {
            $sales_history->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
        }

        if (!empty($location_id)) {
            $sales_history->where('transactions.location_id', $location_id);
        }

        if (!empty($product_ids)) {
            $sales_history->whereIn('products.id', $product_ids);
        }

        if ($permitted_locations != 'all') {
            $sales_history->whereIn('transactions.location_id', $permitted_locations);
        }

        $monthly_sales = $sales_history->select(
            'products.id',
            'products.name as product_name',
            'categories.name as category_name',
            DB::raw('YEAR(transactions.transaction_date) as year'),
            DB::raw('MONTH(transactions.transaction_date) as month'),
            DB::raw('SUM(transaction_sell_lines.quantity) as quantity_sold')
        )
        ->groupBy('products.id', 'year', 'month')
        ->orderBy('products.id')
        ->orderBy('year')
        ->orderBy('month')
        ->get();

        // Group by product
        $product_monthly_sales = [];
        foreach ($monthly_sales as $sale) {
            if (!isset($product_monthly_sales[$sale->id])) {
                $product_monthly_sales[$sale->id] = [
                    'product_name' => $sale->product_name,
                    'category_name' => $sale->category_name,
                    'monthly_data' => []
                ];
            }
            $product_monthly_sales[$sale->id]['monthly_data'][] = [
                'year' => $sale->year,
                'month' => $sale->month,
                'quantity_sold' => $sale->quantity_sold
            ];
        }

        // Calculate forecast for next 3 months using simple moving average (last 3 months)
        $demand_forecast = [];
        foreach ($product_monthly_sales as $product_id => $product_data) {
            $monthly_data = $product_data['monthly_data'];
            $count = count($monthly_data);
            
            if ($count >= 3) {
                $last_three_months = array_slice($monthly_data, -3);
                $avg_quantity = array_sum(array_column($last_three_months, 'quantity_sold')) / 3;
                
                // Get the last month and year
                $last_month = $monthly_data[$count - 1]['month'];
                $last_year = $monthly_data[$count - 1]['year'];
                
                // Generate next 3 months
                $forecast_months = [];
                for ($i = 1; $i <= 3; $i++) {
                    $next_month = ($last_month + $i) % 12;
                    $next_month = $next_month == 0 ? 12 : $next_month;
                    $next_year = $last_year + floor(($last_month + $i) / 12);
                    
                    $forecast_months[] = [
                        'year' => $next_year,
                        'month' => $next_month,
                        'forecast_quantity' => $avg_quantity
                    ];
                }
                
                $demand_forecast[] = [
                    'id' => $product_id,
                    'product_name' => $product_data['product_name'],
                    'category_name' => $product_data['category_name'],
                    'avg_monthly_demand' => $avg_quantity,
                    'forecast_months' => $forecast_months
                ];
            }
        }

        // Sort by average monthly demand in descending order
        usort($demand_forecast, function ($a, $b) {
            return $b['avg_monthly_demand'] - $a['avg_monthly_demand'];
        });

        // Limit to top 10 products
        $demand_forecast = array_slice($demand_forecast, 0, 10);

        // Purchase Forecasting - Estimate procurement needs based on demand + lead time
        $purchase_forecast = [];
        
        // Get average lead time for purchases
        $avg_lead_times = Transaction::where('business_id', $business_id)
            ->where('type', 'purchase')
            ->where('status', 'received')
            ->select(
                'contact_id',
                DB::raw('AVG(DATEDIFF(transaction_date, created_at)) as avg_lead_time')
            )
            ->groupBy('contact_id')
            ->get()
            ->pluck('avg_lead_time', 'contact_id')
            ->toArray();
        
        $default_lead_time = !empty($avg_lead_times) ? array_sum($avg_lead_times) / count($avg_lead_times) : 7; // Default to 7 days if no data
        
        foreach ($demand_forecast as $forecast) {
            $monthly_demand = $forecast['avg_monthly_demand'];
            $daily_demand = $monthly_demand / 30;
            
            $purchase_forecast[] = [
                'id' => $forecast['id'],
                'product_name' => $forecast['product_name'],
                'category_name' => $forecast['category_name'],
                'avg_monthly_demand' => $monthly_demand,
                'avg_daily_demand' => $daily_demand,
                'lead_time_days' => $default_lead_time,
                'recommended_order_quantity' => $daily_demand * $default_lead_time * 1.5, // 1.5 safety factor
                'forecast_months' => $forecast['forecast_months']
            ];
        }

        // Reorder Point Prediction - When to replenish stock to avoid shortages
        $reorder_points = [];
        foreach ($purchase_forecast as $forecast) {
            $safety_stock = $forecast['avg_daily_demand'] * $forecast['lead_time_days'] * 0.5; // 50% of lead time demand as safety stock
            $reorder_point = ($forecast['avg_daily_demand'] * $forecast['lead_time_days']) + $safety_stock;
            
            $reorder_points[] = [
                'id' => $forecast['id'],
                'product_name' => $forecast['product_name'],
                'category_name' => $forecast['category_name'],
                'avg_daily_demand' => $forecast['avg_daily_demand'],
                'lead_time_days' => $forecast['lead_time_days'],
                'safety_stock' => $safety_stock,
                'reorder_point' => $reorder_point
            ];
        }

        // Dynamic Pricing Simulation - Impact of supplier cost changes on customer pricing
        $pricing_simulation = TransactionSellLine::join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_sell_lines.product_id', '=', 'products.id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final');

        if (!empty($start_date) && !empty($end_date)) {
            $pricing_simulation->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
        }

        if (!empty($location_id)) {
            $pricing_simulation->where('transactions.location_id', $location_id);
        }

        if (!empty($product_ids)) {
            $pricing_simulation->whereIn('products.id', $product_ids);
        }

        if ($permitted_locations != 'all') {
            $pricing_simulation->whereIn('transactions.location_id', $permitted_locations);
        }

        $pricing_simulation = $pricing_simulation->select(
            'products.id',
            'products.name as product_name',
            DB::raw('AVG(transaction_sell_lines.unit_price_inc_tax) as avg_selling_price'),
            DB::raw('AVG(transaction_sell_lines.purchase_price_inc_tax) as avg_purchase_price'),
            DB::raw('(AVG(transaction_sell_lines.unit_price_inc_tax) - AVG(transaction_sell_lines.purchase_price_inc_tax)) as avg_margin'),
            DB::raw('(AVG(transaction_sell_lines.unit_price_inc_tax) - AVG(transaction_sell_lines.purchase_price_inc_tax)) / AVG(transaction_sell_lines.purchase_price_inc_tax) * 100 as margin_percentage')
        )
        ->groupBy('products.id')
        ->orderBy('margin_percentage', 'desc')
        ->limit(10)
        ->get();

        // Calculate pricing scenarios
        foreach ($pricing_simulation as $product) {
            $scenarios = [];
            
            // Scenario 1: Supplier increases cost by 5%
            $new_purchase_price = $product->avg_purchase_price * 1.05;
            $scenarios[] = [
                'scenario' => 'Supplier increases cost by 5%',
                'new_purchase_price' => $new_purchase_price,
                'maintain_margin_price' => $new_purchase_price * (1 + $product->margin_percentage / 100),
                'maintain_price_margin' => ($product->avg_selling_price - $new_purchase_price) / $new_purchase_price * 100,
                'price_increase_needed' => (($new_purchase_price * (1 + $product->margin_percentage / 100)) - $product->avg_selling_price) / $product->avg_selling_price * 100
            ];
            
            // Scenario 2: Supplier increases cost by 10%
            $new_purchase_price = $product->avg_purchase_price * 1.1;
            $scenarios[] = [
                'scenario' => 'Supplier increases cost by 10%',
                'new_purchase_price' => $new_purchase_price,
                'maintain_margin_price' => $new_purchase_price * (1 + $product->margin_percentage / 100),
                'maintain_price_margin' => ($product->avg_selling_price - $new_purchase_price) / $new_purchase_price * 100,
                'price_increase_needed' => (($new_purchase_price * (1 + $product->margin_percentage / 100)) - $product->avg_selling_price) / $product->avg_selling_price * 100
            ];
            
            // Scenario 3: Supplier decreases cost by 5%
            $new_purchase_price = $product->avg_purchase_price * 0.95;
            $scenarios[] = [
                'scenario' => 'Supplier decreases cost by 5%',
                'new_purchase_price' => $new_purchase_price,
                'maintain_margin_price' => $new_purchase_price * (1 + $product->margin_percentage / 100),
                'maintain_price_margin' => ($product->avg_selling_price - $new_purchase_price) / $new_purchase_price * 100,
                'price_decrease_possible' => ($product->avg_selling_price - ($new_purchase_price * (1 + $product->margin_percentage / 100))) / $product->avg_selling_price * 100
            ];
            
            $product->pricing_scenarios = $scenarios;
        }

        // Retention Triggers - Predict which customers will need follow-up before churn
        $retention_triggers = Contact::leftJoin('transactions as t', function ($join) use ($start_date, $end_date) {
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
            $retention_triggers->where('t.location_id', $location_id);
        }

        if (!empty($customer_ids)) {
            $retention_triggers->whereIn('contacts.id', $customer_ids);
        }

        if ($permitted_locations != 'all') {
            $retention_triggers->whereIn('t.location_id', $permitted_locations);
        }

        $retention_triggers = $retention_triggers->select(
            'contacts.id',
            'contacts.name',
            DB::raw('MAX(t.transaction_date) as last_purchase_date'),
            DB::raw('DATEDIFF(NOW(), MAX(t.transaction_date)) as days_since_last_purchase'),
            DB::raw('COUNT(t.id) as purchase_count'),
            DB::raw('SUM(t.final_total) as total_spent'),
            DB::raw('AVG(DATEDIFF(t.transaction_date, LAG(t.transaction_date) OVER (PARTITION BY t.contact_id ORDER BY t.transaction_date))) as avg_purchase_interval')
        )
        ->groupBy('contacts.id')
        ->havingRaw('days_since_last_purchase > avg_purchase_interval * 0.8') // Approaching their typical purchase interval
        ->havingRaw('purchase_count >= 2') // Must have at least 2 purchases to calculate interval
        ->orderBy('days_since_last_purchase', 'desc')
        ->limit(10)
        ->get();

        return [
            'demand_forecast' => $demand_forecast,
            'purchase_forecast' => $purchase_forecast,
            'reorder_points' => $reorder_points,
            'pricing_simulation' => $pricing_simulation,
            'retention_triggers' => $retention_triggers
        ];
    }
}