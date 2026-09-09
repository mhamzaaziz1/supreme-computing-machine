<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;

/**
 * The application's information architecture, in one place.
 *
 * This is the single source of truth for the Blade sidebar, the Inertia shell
 * and the command palette, so the menu is defined once instead of being
 * re-hardcoded per surface.
 *
 * The tree mirrors the legacy sidebar's groups and ordering. An earlier pass
 * regrouped these destinations "by job" and dropped roughly half of them,
 * which lost real screens (the whole Reports section, the restaurant module,
 * drafts, quotations, purchase orders), so the full set is restored here and
 * rendered by the current sidebar design.
 *
 * Gating mirrors the old Blade conditions:
 *   - `can`    a permission, or a list of which any one grants access
 *   - `module` an enabled module, or a list of which any one is enough
 *   - `when`   anything else (business settings, config flags, admin-only)
 * Items the user cannot reach are pruned, then groups left empty are dropped,
 * so the sidebar never shows a link that leads to a 403.
 */
class Navigation
{
    /**
     * Build the permission-filtered navigation tree for the current user.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function forCurrentUser(): array
    {
        $tree = [
            self::home(),
            self::advancedDashboard(),
            self::userManagement(),
            self::contacts(),
            self::customerRoutes(),
            self::customerVehicles(),
            self::supplyChainVehicles(),
            self::geofencing(),
            self::products(),
            self::purchases(),
            self::sales(),
            self::stockTransfers(),
            self::stockAdjustments(),
            self::expenses(),
            self::paymentAccounts(),
            self::reports(),
            self::bookings(),
            self::kitchen(),
            self::serviceStaffOrders(),
            self::notificationTemplates(),
            self::onlineStore(),
        ];

        return array_values(array_filter(array_map(self::prune(...), $tree)));
    }

    /**
     * Settings are pinned to the foot of the sidebar rather than sitting in
     * the scrolling list: they are configured a handful of times and then
     * rarely touched, so they should not push daily work down the page.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function settingsForCurrentUser(): array
    {
        $group = self::group(__('business.settings'), 'settings', [
            self::item(__('business.business_settings'), ['App\Http\Controllers\BusinessController', 'getBusinessSettings'], can: 'business_settings.access'),
            self::item(__('business.business_locations'), ['App\Http\Controllers\BusinessLocationController', 'index'], can: 'business_settings.access'),
            self::item(__('invoice.invoice_settings'), ['App\Http\Controllers\InvoiceSchemeController', 'index'], can: 'invoice_settings.access'),
            self::item(__('barcode.barcode_settings'), ['App\Http\Controllers\BarcodeController', 'index'], can: 'barcode_settings.access'),
            self::item(__('printer.receipt_printers'), ['App\Http\Controllers\PrinterController', 'index'], can: 'access_printers'),
            self::item(__('tax_rate.tax_rates'), ['App\Http\Controllers\TaxRateController', 'index'], can: ['tax_rate.view', 'tax_rate.create']),
            self::item(__('restaurant.tables'), ['App\Http\Controllers\Restaurant\TableController', 'index'], can: 'access_tables', module: 'tables'),
            self::item(__('restaurant.modifiers'), ['App\Http\Controllers\Restaurant\ModifierSetsController', 'index'], can: ['product.view', 'product.create'], module: 'modifiers'),
            self::item(__('lang_v1.types_of_service'), ['App\Http\Controllers\TypesOfServiceController', 'index'], can: 'access_types_of_service', module: 'types_of_service'),
            self::item(__('lang_v1.backup'), ['App\Http\Controllers\BackUpController', 'index'], can: 'backup'),
            self::item(__('lang_v1.modules'), ['App\Http\Controllers\Install\ModulesController', 'index'], can: 'manage_modules'),
        ]);

        $pruned = self::prune($group);

        return $pruned ? $pruned['items'] : [];
    }

    // -----------------------------------------------------------------
    // Groups, in the order the legacy sidebar listed them
    // -----------------------------------------------------------------

    private static function home(): array
    {
        return self::link(__('home.home'), 'home', ['App\Http\Controllers\HomeController', 'index']);
    }

    private static function advancedDashboard(): array
    {
        return self::link(__('Advanced Dashboard'), 'insight', ['App\Http\Controllers\ReportController', 'getBusinessAdvanceAnalytics']);
    }

    private static function userManagement(): array
    {
        return self::group(__('user.user_management'), 'users', [
            self::item(__('user.users'), ['App\Http\Controllers\ManageUserController', 'index'], can: 'user.view'),
            self::item(__('user.roles'), ['App\Http\Controllers\RoleController', 'index'], can: 'roles.view'),
            self::item(__('lang_v1.sales_commission_agents'), ['App\Http\Controllers\SalesCommissionAgentController', 'index'], can: 'user.create'),
            self::item(__('lang_v1.active_users_location'), ['App\Http\Controllers\UserLocationController', 'index'], can: 'user.view'),
        ]);
    }

    private static function contacts(): array
    {
        return self::group(__('contact.contacts'), 'customers', [
            self::item(__('report.supplier'), ['App\Http\Controllers\ContactController', 'index'], ['type' => 'supplier'], can: ['supplier.view', 'supplier.view_own']),
            self::item(__('report.customer'), ['App\Http\Controllers\ContactController', 'index'], ['type' => 'customer'], can: ['customer.view', 'customer.view_own']),
            self::item(__('lang_v1.customer_groups'), ['App\Http\Controllers\CustomerGroupController', 'index'], can: ['customer.view', 'customer.view_own']),
            self::item(__('lang_v1.import_contacts'), ['App\Http\Controllers\ContactController', 'getImportContacts'], can: ['supplier.create', 'customer.create']),
            // The map screen cannot render without a key, which is why the old
            // sidebar hid it too.
            self::item(__('lang_v1.map'), ['App\Http\Controllers\ContactController', 'contactMap'], when: fn () => ! empty(config('services.google_maps.api_key'))),
            self::item(__('lang_v1.route_followups'), ['App\Http\Controllers\RouteFollowupController', 'index'], can: ['customer.view', 'customer.view_own']),
        ]);
    }

    private static function customerRoutes(): array
    {
        return self::link(__('lang_v1.customer_routes'), 'route', ['App\Http\Controllers\CustomerRouteController', 'index'], can: ['customer.view', 'customer.view_own']);
    }

    private static function customerVehicles(): array
    {
        return self::link(__('lang_v1.vehicles'), 'truck', ['App\Http\Controllers\CustomerVehicleController', 'index'], can: ['customer.view', 'customer.view_own']);
    }

    private static function supplyChainVehicles(): array
    {
        return self::group(__('lang_v1.supply_chain_vehicles'), 'truck', [
            self::item(__('lang_v1.all_vehicles'), ['App\Http\Controllers\SupplyChainVehicleController', 'index']),
            self::item(__('lang_v1.assign_route'), ['App\Http\Controllers\VehicleRouteAssignmentController', 'index']),
            self::item(__('lang_v1.vehicle_expenses'), ['App\Http\Controllers\SupplyChainVehicleExpenseController', 'index']),
        ], can: ['customer.view', 'customer.view_own']);
    }

    private static function geofencing(): array
    {
        return self::group(__('lang_v1.geofencing'), 'geofence', [
            self::item(__('lang_v1.route_assignments'), ['App\Http\Controllers\RouteSellerAssignmentController', 'index']),
            self::item(__('lang_v1.visit_logs'), ['App\Http\Controllers\RouteVisitLogController', 'index']),
            self::item(__('lang_v1.violation_logs'), ['App\Http\Controllers\GeofenceViolationLogController', 'index']),
            self::item(__('lang_v1.route_coverage_report'), ['App\Http\Controllers\ReportController', 'getRouteCoverageReport']),
        ], can: ['customer.view', 'customer.view_own']);
    }

    private static function products(): array
    {
        return self::group(__('sale.products'), 'stock', [
            self::item(__('lang_v1.list_products'), ['App\Http\Controllers\ProductController', 'index'], can: 'product.view'),
            self::item(__('product.add_product'), ['App\Http\Controllers\ProductController', 'create'], can: 'product.create'),
            self::item(__('lang_v1.update_product_price'), ['App\Http\Controllers\SellingPriceGroupController', 'updateProductPrice'], can: 'product.create'),
            self::item(__('barcode.print_labels'), ['App\Http\Controllers\LabelsController', 'show'], can: 'product.view'),
            self::item(__('product.variations'), ['App\Http\Controllers\VariationTemplateController', 'index'], can: 'product.create'),
            self::item(__('product.import_products'), ['App\Http\Controllers\ImportProductsController', 'index'], can: 'product.create'),
            self::item(__('lang_v1.import_opening_stock'), ['App\Http\Controllers\ImportOpeningStockController', 'index'], can: 'product.opening_stock'),
            self::item(__('lang_v1.selling_price_group'), ['App\Http\Controllers\SellingPriceGroupController', 'index'], can: 'product.create'),
            self::item(__('unit.units'), ['App\Http\Controllers\UnitController', 'index'], can: 'product.create'),
            self::item(__('category.categories'), ['App\Http\Controllers\TaxonomyController', 'index'], ['type' => 'product'], can: 'product.create'),
            self::item(__('brand.brands'), ['App\Http\Controllers\BrandController', 'index'], can: 'product.create'),
            self::item(__('lang_v1.warranties'), ['App\Http\Controllers\WarrantyController', 'index'], can: 'product.create'),
        ]);
    }

    private static function purchases(): array
    {
        return self::group(__('purchase.purchases'), 'purchase', [
            self::item(__('lang_v1.purchase_requisition'), ['App\Http\Controllers\PurchaseRequisitionController', 'index'], can: ['purchase_requisition.view_all', 'purchase_requisition.view_own'], when: fn () => ! empty(self::commonSettings()['enable_purchase_requisition'])),
            self::item(__('lang_v1.purchase_order'), ['App\Http\Controllers\PurchaseOrderController', 'index'], can: ['purchase_order.view_all', 'purchase_order.view_own'], when: fn () => ! empty(self::commonSettings()['enable_purchase_order'])),
            self::item(__('purchase.list_purchase'), ['App\Http\Controllers\PurchaseController', 'index'], can: ['purchase.view', 'view_own_purchase']),
            self::item(__('purchase.add_purchase'), ['App\Http\Controllers\PurchaseController', 'create'], can: 'purchase.create'),
            self::item(__('lang_v1.list_purchase_return'), ['App\Http\Controllers\PurchaseReturnController', 'index'], can: 'purchase.update'),
        ], module: 'purchases');
    }

    private static function sales(): array
    {
        return self::group(__('sale.sale'), 'sell', [
            self::item(__('lang_v1.sales_order'), ['App\Http\Controllers\SalesOrderController', 'index'], can: ['so.view_own', 'so.view_all', 'so.create'], when: fn () => ! empty(self::posSettings()['enable_sales_order'])),
            // Points at the rebuilt Inertia list; the legacy DataTables screen
            // is still served at /sells for deep links and the reports' feed.
            self::item(__('lang_v1.all_sales'), route: route('sales.index'), can: ['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping'], spa: true),
            self::item(__('sale.add_sale'), route: route('sales.create'), can: 'direct_sell.access', module: 'add_sale', spa: true),
            self::item(__('sale.list_pos'), route: route('sales.pos'), can: 'sell.view', module: 'pos_sale', spa: true, when: fn () => self::isAdmin() || self::userCan('sell.create')),
            self::item(__('sale.pos_sale'), ['App\Http\Controllers\SellPosController', 'create'], can: 'sell.create', module: 'pos_sale'),
            self::item(__('lang_v1.add_draft'), ['App\Http\Controllers\SellController', 'create'], ['status' => 'draft'], can: 'direct_sell.access', module: 'add_sale'),
            self::item(__('lang_v1.list_drafts'), route: route('sales.drafts'), can: ['draft.view_all', 'draft.view_own'], module: 'add_sale', spa: true),
            self::item(__('lang_v1.add_quotation'), ['App\Http\Controllers\SellController', 'create'], ['status' => 'quotation'], can: 'direct_sell.access', module: 'add_sale'),
            self::item(__('lang_v1.list_quotations'), route: route('sales.quotations'), can: ['quotation.view_all', 'quotation.view_own'], module: 'add_sale', spa: true),
            self::item(__('lang_v1.list_sell_return'), ['App\Http\Controllers\SellReturnController', 'index'], can: ['access_sell_return', 'access_own_sell_return']),
            self::item(__('lang_v1.shipments'), ['App\Http\Controllers\SellController', 'shipments'], can: ['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping']),
            self::item(__('lang_v1.discounts'), ['App\Http\Controllers\DiscountController', 'index'], can: 'discount.access'),
            self::item(__('lang_v1.subscriptions'), ['App\Http\Controllers\SellPosController', 'listSubscriptions'], can: 'direct_sell.access', module: 'subscription'),
            self::item(__('lang_v1.import_sales'), ['App\Http\Controllers\ImportSalesController', 'index'], can: 'sell.create'),
        ]);
    }

    private static function stockTransfers(): array
    {
        return self::group(__('lang_v1.stock_transfers'), 'transfer', [
            self::item(__('lang_v1.list_stock_transfers'), ['App\Http\Controllers\StockTransferController', 'index'], can: ['purchase.view', 'view_own_purchase']),
            self::item(__('lang_v1.add_stock_transfer'), ['App\Http\Controllers\StockTransferController', 'create'], can: 'purchase.create'),
        ], module: 'stock_transfers');
    }

    private static function stockAdjustments(): array
    {
        return self::group(__('stock_adjustment.stock_adjustment'), 'adjustment', [
            self::item(__('stock_adjustment.list'), ['App\Http\Controllers\StockAdjustmentController', 'index'], can: ['purchase.view', 'view_own_purchase']),
            self::item(__('stock_adjustment.add'), ['App\Http\Controllers\StockAdjustmentController', 'create'], can: 'purchase.create'),
        ], module: 'stock_adjustment');
    }

    private static function expenses(): array
    {
        return self::group(__('expense.expenses'), 'expense', [
            self::item(__('lang_v1.list_expenses'), ['App\Http\Controllers\ExpenseController', 'index'], can: ['all_expense.access', 'view_own_expense']),
            self::item(__('expense.add_expense'), ['App\Http\Controllers\ExpenseController', 'create'], can: 'expense.add'),
            self::item(__('expense.expense_categories'), ['App\Http\Controllers\ExpenseCategoryController', 'index'], can: 'expense.add'),
        ], module: 'expenses');
    }

    private static function paymentAccounts(): array
    {
        return self::group(__('lang_v1.payment_accounts'), 'account', [
            self::item(__('account.list_accounts'), ['App\Http\Controllers\AccountController', 'index']),
            self::item(__('account.balance_sheet'), ['App\Http\Controllers\AccountReportsController', 'balanceSheet']),
            self::item(__('account.trial_balance'), ['App\Http\Controllers\AccountReportsController', 'trialBalance']),
            self::item(__('lang_v1.cash_flow'), ['App\Http\Controllers\AccountController', 'cashFlow']),
            self::item(__('account.payment_account_report'), ['App\Http\Controllers\AccountReportsController', 'paymentAccountReport']),
        ], can: 'account.access', module: 'account');
    }

    private static function reports(): array
    {
        return self::group(__('report.reports'), 'insight', [
            self::item(__('report.profit_loss'), ['App\Http\Controllers\ReportController', 'getProfitLoss'], can: 'profit_loss_report.view'),
            // Dominican Republic tax filings; off unless the deployment asks.
            self::item('Report 606 ('.__('lang_v1.purchase').')', ['App\Http\Controllers\ReportController', 'purchaseReport'], when: fn () => config('constants.show_report_606') == true),
            self::item('Report 607 ('.__('business.sale').')', ['App\Http\Controllers\ReportController', 'saleReport'], when: fn () => config('constants.show_report_607') == true),
            self::item(__('report.purchase_sell_report'), ['App\Http\Controllers\ReportController', 'getPurchaseSell'], can: 'purchase_n_sell_report.view', module: ['purchases', 'add_sale', 'pos_sale']),
            self::item(__('report.tax_report'), ['App\Http\Controllers\ReportController', 'getTaxReport'], can: 'tax_report.view'),
            self::item(__('report.contacts'), ['App\Http\Controllers\ReportController', 'getCustomerSuppliers'], can: 'contacts_report.view'),
            self::item(__('lang_v1.customer_groups_report'), ['App\Http\Controllers\ReportController', 'getCustomerGroup'], can: 'contacts_report.view'),
            self::item(__('report.stock_report'), ['App\Http\Controllers\ReportController', 'getStockReport'], can: 'stock_report.view'),
            self::item(__('report.stock_expiry_report'), ['App\Http\Controllers\ReportController', 'getStockExpiryReport'], can: 'stock_report.view', when: fn () => session('business.enable_product_expiry') == 1),
            self::item(__('lang_v1.lot_report'), ['App\Http\Controllers\ReportController', 'getLotReport'], can: 'stock_report.view', when: fn () => session('business.enable_lot_number') == 1),
            self::item(__('report.stock_adjustment_report'), ['App\Http\Controllers\ReportController', 'getStockAdjustmentReport'], can: 'stock_report.view', module: 'stock_adjustment'),
            self::item(__('report.trending_products'), ['App\Http\Controllers\ReportController', 'getTrendingProducts'], can: 'trending_product_report.view'),
            self::item(__('lang_v1.items_report'), ['App\Http\Controllers\ReportController', 'itemsReport'], can: 'purchase_n_sell_report.view'),
            self::item(__('lang_v1.product_purchase_report'), ['App\Http\Controllers\ReportController', 'getproductPurchaseReport'], can: 'purchase_n_sell_report.view'),
            self::item(__('lang_v1.product_sell_report'), ['App\Http\Controllers\ReportController', 'getproductSellReport'], can: 'purchase_n_sell_report.view'),
            self::item(__('lang_v1.purchase_payment_report'), ['App\Http\Controllers\ReportController', 'purchasePaymentReport'], can: 'purchase_n_sell_report.view'),
            self::item(__('lang_v1.sell_payment_report'), ['App\Http\Controllers\ReportController', 'sellPaymentReport'], can: 'purchase_n_sell_report.view'),
            self::item(__('report.expense_report'), ['App\Http\Controllers\ReportController', 'getExpenseReport'], can: 'expense_report.view', module: 'expenses'),
            self::item(__('report.register_report'), ['App\Http\Controllers\ReportController', 'getRegisterReport'], can: 'register_report.view'),
            self::item(__('report.sales_representative'), ['App\Http\Controllers\ReportController', 'getSalesRepresentativeReport'], can: 'sales_representative.view'),
            self::item(__('restaurant.table_report'), ['App\Http\Controllers\ReportController', 'getTableReport'], can: 'purchase_n_sell_report.view', module: 'tables'),
            self::item(__('lang_v1.gst_sales_report'), ['App\Http\Controllers\ReportController', 'gstSalesReport'], can: 'tax_report.view', when: fn () => ! empty(config('constants.enable_gst_report_india'))),
            self::item(__('lang_v1.gst_purchase_report'), ['App\Http\Controllers\ReportController', 'gstPurchaseReport'], can: 'tax_report.view', when: fn () => ! empty(config('constants.enable_gst_report_india'))),
            self::item(__('restaurant.service_staff_report'), ['App\Http\Controllers\ReportController', 'getServiceStaffReport'], can: 'sales_representative.view', module: 'service_staff'),
            self::item(__('lang_v1.route_followup_report'), ['App\Http\Controllers\ReportController', 'getRouteFollowupReport'], can: ['customer.view', 'customer.view_own']),
            self::item(__('lang_v1.activity_log'), ['App\Http\Controllers\ReportController', 'activityLog'], when: fn () => self::isAdmin()),
        ], can: ['purchase_n_sell_report.view', 'contacts_report.view', 'stock_report.view', 'tax_report.view', 'trending_product_report.view', 'sales_representative.view', 'register_report.view', 'expense_report.view']);
    }

    private static function bookings(): array
    {
        return self::link(__('restaurant.bookings'), 'calendar', ['App\Http\Controllers\Restaurant\BookingController', 'index'], can: ['crud_all_bookings', 'crud_own_bookings'], module: 'booking');
    }

    private static function kitchen(): array
    {
        return self::link(__('restaurant.kitchen'), 'kitchen', ['App\Http\Controllers\Restaurant\KitchenController', 'index'], module: 'kitchen');
    }

    private static function serviceStaffOrders(): array
    {
        return self::link(__('restaurant.orders'), 'orders', ['App\Http\Controllers\Restaurant\OrderController', 'index'], module: 'service_staff');
    }

    private static function notificationTemplates(): array
    {
        return self::link(__('lang_v1.notification_templates'), 'bell', ['App\Http\Controllers\NotificationTemplateController', 'index'], can: 'send_notifications');
    }

    private static function onlineStore(): array
    {
        return self::group(__('ecommerce.ecommerce'), 'store', [
            self::item(__('ecommerce.dashboard'), route: route('ecommerce.home')),
            self::item(__('ecommerce.products'), route: route('ecommerce.products')),
            self::item(__('ecommerce.orders'), ['App\Http\Controllers\SellController', 'index'], ['type' => 'ecommerce']),
            self::item(__('ecommerce.customers'), ['App\Http\Controllers\ContactController', 'index'], ['type' => 'customer', 'source' => 'ecommerce']),
            self::item(__('ecommerce.settings'), ['App\Http\Controllers\BusinessController', 'getEcommerceSettings']),
        ], module: 'ecommerce');
    }

    // -----------------------------------------------------------------
    // Builders
    // -----------------------------------------------------------------

    /**
     * A collapsible group of links.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @return array<string, mixed>
     */
    private static function group(
        string $label,
        string $icon,
        array $items,
        string|array|null $can = null,
        string|array|null $module = null,
    ): array {
        return [
            'label' => $label,
            'icon' => $icon,
            'items' => $items,
            'can' => $can,
            'module' => $module,
        ];
    }

    /**
     * A top-level entry that is a destination rather than a container. The
     * sidebar renders these as plain links, so they carry no children.
     *
     * @param  array{0: class-string|string, 1: string}  $action
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    private static function link(
        string $label,
        string $icon,
        array $action,
        array $params = [],
        string|array|null $can = null,
        string|array|null $module = null,
        bool $spa = false,
    ): array {
        return [
            'label' => $label,
            'icon' => $icon,
            'url' => action($action, $params),
            'items' => [],
            'can' => $can,
            'module' => $module,
            'spa' => $spa,
        ];
    }

    /**
     * @param  array{0: class-string|string, 1: string}|null  $action
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    private static function item(
        string $label,
        ?array $action = null,
        array $params = [],
        string|array|null $can = null,
        ?string $route = null,
        bool $spa = false,
        string|array|null $module = null,
        ?\Closure $when = null,
    ): array {
        return [
            'label' => $label,
            'url' => $route ?? action($action, $params),
            'can' => $can,
            'module' => $module,
            'when' => $when,
            'spa' => $spa,
        ];
    }

    // -----------------------------------------------------------------
    // Gating
    // -----------------------------------------------------------------

    /**
     * Drop anything the current user cannot reach, then drop groups that end
     * up empty. Keeps the sidebar honest: no links that lead to a 403.
     *
     * @param  array<string, mixed>  $group
     * @return array<string, mixed>|null
     */
    private static function prune(array $group): ?array
    {
        if (! self::allows($group)) {
            return null;
        }

        $items = array_values(array_filter($group['items'], self::allows(...)));

        // A group that is itself a link (like Home) survives having no
        // children; a container with nothing left in it does not.
        if (empty($items) && empty($group['url'])) {
            return null;
        }

        return [...$group, 'items' => $items];
    }

    /**
     * @param  array<string, mixed>  $entry
     */
    private static function allows(array $entry): bool
    {
        $module = $entry['module'] ?? null;
        if ($module !== null && ! array_intersect((array) $module, self::enabledModules())) {
            return false;
        }

        $can = $entry['can'] ?? null;
        if (! empty($can) && ! self::isAdmin()) {
            $granted = false;
            foreach ((array) $can as $permission) {
                if (self::userCan($permission)) {
                    $granted = true;
                    break;
                }
            }

            if (! $granted) {
                return false;
            }
        }

        $when = $entry['when'] ?? null;

        return ! ($when instanceof \Closure) || (bool) $when();
    }

    private static function userCan(string $permission): bool
    {
        $user = Auth::user();

        return $user !== null && $user->can($permission);
    }

    /**
     * The business owner sees everything, matching the legacy sidebar's
     * `$is_admin ||` short-circuit in front of its permission checks.
     */
    private static function isAdmin(): bool
    {
        $user = Auth::user();

        return $user !== null && $user->hasRole('Admin#'.session('business.id'));
    }

    /**
     * @return array<int, string>
     */
    private static function enabledModules(): array
    {
        $modules = session('business.enabled_modules');

        return is_array($modules) ? $modules : [];
    }

    /**
     * @return array<string, mixed>
     */
    private static function commonSettings(): array
    {
        $settings = session('business.common_settings');

        return is_array($settings) ? $settings : [];
    }

    /**
     * Stored as a JSON string on the session, unlike the other settings bags.
     *
     * @return array<string, mixed>
     */
    private static function posSettings(): array
    {
        $settings = session('business.pos_settings');

        if (empty($settings)) {
            return [];
        }

        return is_array($settings) ? $settings : (json_decode($settings, true) ?: []);
    }
}
