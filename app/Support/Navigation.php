<?php

namespace App\Support;

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountReportsController;
use App\Http\Controllers\BackUpController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\BusinessLocationController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomerGroupController;
use App\Http\Controllers\CustomerRouteController;
use App\Http\Controllers\CustomerVehicleController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\GeofenceViolationLogController;
use App\Http\Controllers\ImportOpeningStockController;
use App\Http\Controllers\ImportProductsController;
use App\Http\Controllers\Install\ModulesController;
use App\Http\Controllers\InvoiceSchemeController;
use App\Http\Controllers\LabelsController;
use App\Http\Controllers\ManageUserController;
use App\Http\Controllers\PrinterController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RouteSellerAssignmentController;
use App\Http\Controllers\RouteVisitLogController;
use App\Http\Controllers\SalesCommissionAgentController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\SellPosController;
use App\Http\Controllers\SellReturnController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\SupplyChainAnalyticsController;
use App\Http\Controllers\SupplyChainVehicleController;
use App\Http\Controllers\SupplyChainVehicleExpenseController;
use App\Http\Controllers\TaxonomyController;
use App\Http\Controllers\TaxRateController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\VariationTemplateController;
use App\Http\Controllers\VehicleRouteAssignmentController;
use Illuminate\Support\Facades\Auth;

/**
 * The application's information architecture, in one place.
 *
 * The legacy sidebar grew to 17 top-level groups and ~80 links organised by
 * database table, which meant a single real job (running a delivery route,
 * say) was scattered across five menus. This regroups the same destinations
 * by the job being done, and is the single source of truth for the Inertia
 * shell, the command palette and the legacy Blade sidebar.
 *
 * Every item resolves to a URL that already exists. Items marked
 * `'spa' => true` are served by Inertia; the rest still render Blade and are
 * visited as full page loads until they are migrated.
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
        $enabledModules = session('business.enabled_modules') ?? [];

        $tree = [
            self::today(),
            self::sell(),
            self::routesAndFleet(),
            self::customers(),
            self::stock(),
            self::moneyAndInsight(),
        ];

        if (in_array('ecommerce', $enabledModules, true)) {
            $tree[] = self::onlineStore();
        }

        return array_values(array_filter(array_map(self::prune(...), $tree)));
    }

    /**
     * Settings live in the account menu rather than the sidebar: they are
     * configured a handful of times and then never touched again, so they do
     * not deserve permanent real estate next to daily work.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function settingsForCurrentUser(): array
    {
        $group = [
            'label' => __('business.settings'),
            'icon' => 'settings',
            'items' => [
                self::item(__('business.business_settings'), [BusinessController::class, 'getBusinessSettings'], can: 'business_settings.access'),
                self::item(__('business.business_locations'), [BusinessLocationController::class, 'index'], can: 'business_settings.access'),
                self::item(__('invoice.invoice_settings'), [InvoiceSchemeController::class, 'index'], can: 'invoice_settings.access'),
                self::item(__('barcode.barcode_settings'), [BarcodeController::class, 'index'], can: 'barcode_settings.access'),
                self::item(__('printer.receipt_printers'), [PrinterController::class, 'index'], can: 'access_printers'),
                self::item(__('tax_rate.tax_rates'), [TaxRateController::class, 'index'], can: 'access_printers'),
                self::item(__('user.users'), [ManageUserController::class, 'index'], can: 'user.view'),
                self::item(__('user.roles'), [RoleController::class, 'index'], can: 'roles.view'),
                self::item(__('lang_v1.backup'), [BackUpController::class, 'index'], can: 'backup'),
                self::item(__('lang_v1.modules'), [ModulesController::class, 'index'], can: 'manage_modules'),
            ],
        ];

        $pruned = self::prune($group);

        return $pruned ? $pruned['items'] : [];
    }

    // -----------------------------------------------------------------
    // Groups
    // -----------------------------------------------------------------

    private static function today(): array
    {
        return [
            'label' => __('lang_v1.nav_today'),
            'icon' => 'today',
            'url' => route('today'),
            'spa' => true,
            'items' => [],
        ];
    }

    private static function sell(): array
    {
        return [
            'label' => __('lang_v1.nav_sell'),
            'icon' => 'sell',
            'items' => [
                self::item(__('sale.pos_sale'), [SellPosController::class, 'create'], can: 'sell.create'),
                self::item(__('sale.add_sale'), [SellController::class, 'create'], can: 'sell.create'),
                self::item(__('lang_v1.all_sales'), [SellController::class, 'index']),
                self::item(__('lang_v1.list_sell_return'), [SellReturnController::class, 'index']),
                self::item(__('lang_v1.discounts'), [DiscountController::class, 'index']),
                self::item(__('lang_v1.sales_commission_agents'), [SalesCommissionAgentController::class, 'index'], can: 'user.create'),
            ],
        ];
    }

    /**
     * The route/fleet operation is what makes this a distribution business
     * rather than a shop till. In the legacy menu these nine screens were
     * split across "Contacts", "Vehicles" and "Reports".
     */
    private static function routesAndFleet(): array
    {
        return [
            'label' => __('lang_v1.nav_routes'),
            'icon' => 'route',
            'items' => [
                self::item(__('lang_v1.customer_routes'), [CustomerRouteController::class, 'index']),
                self::item(__('lang_v1.nav_seller_assignments'), [RouteSellerAssignmentController::class, 'index']),
                self::item(__('lang_v1.nav_vehicle_assignments'), [VehicleRouteAssignmentController::class, 'index']),
                self::item(__('lang_v1.visit_logs'), [RouteVisitLogController::class, 'index']),
                self::item(__('lang_v1.violation_logs'), [GeofenceViolationLogController::class, 'index']),
                self::item(__('lang_v1.route_coverage_report'), [ReportController::class, 'getRouteCoverageReport']),
                self::item(__('lang_v1.all_vehicles'), [SupplyChainVehicleController::class, 'index']),
                self::item(__('lang_v1.vehicle_expenses'), [SupplyChainVehicleExpenseController::class, 'index']),
                self::item(__('lang_v1.fleet_analytics'), [SupplyChainAnalyticsController::class, 'index']),
            ],
        ];
    }

    private static function customers(): array
    {
        return [
            'label' => __('lang_v1.nav_customers'),
            'icon' => 'customers',
            'items' => [
                self::item(__('report.customer'), [ContactController::class, 'index'], ['type' => 'customer']),
                self::item(__('report.supplier'), [ContactController::class, 'index'], ['type' => 'supplier']),
                self::item(__('lang_v1.customer_groups'), [CustomerGroupController::class, 'index']),
                self::item(__('lang_v1.vehicles'), [CustomerVehicleController::class, 'index']),
                self::item(__('lang_v1.import_contacts'), [ContactController::class, 'getImportContacts']),
            ],
        ];
    }

    private static function stock(): array
    {
        return [
            'label' => __('lang_v1.nav_stock'),
            'icon' => 'stock',
            'items' => [
                self::item(__('lang_v1.list_products'), [ProductController::class, 'index'], can: 'product.view'),
                self::item(__('product.add_product'), [ProductController::class, 'create'], can: 'product.create'),
                self::item(__('category.categories'), [TaxonomyController::class, 'index']),
                self::item(__('brand.brands'), [BrandController::class, 'index']),
                self::item(__('unit.units'), [UnitController::class, 'index'], can: 'product.create'),
                self::item(__('product.variations'), [VariationTemplateController::class, 'index'], can: 'product.create'),
                self::item(__('barcode.print_labels'), [LabelsController::class, 'show'], can: 'product.view'),
                self::item(__('purchase.list_purchase'), [PurchaseController::class, 'index']),
                self::item(__('purchase.add_purchase'), [PurchaseController::class, 'create']),
                self::item(__('lang_v1.list_purchase_return'), [PurchaseReturnController::class, 'index']),
                self::item(__('lang_v1.list_stock_transfers'), [StockTransferController::class, 'index']),
                self::item(__('stock_adjustment.list'), [StockAdjustmentController::class, 'index']),
                self::item(__('product.import_products'), [ImportProductsController::class, 'index']),
                self::item(__('lang_v1.import_opening_stock'), [ImportOpeningStockController::class, 'index']),
            ],
        ];
    }

    private static function moneyAndInsight(): array
    {
        return [
            'label' => __('lang_v1.nav_money'),
            'icon' => 'insight',
            'items' => [
                self::item(__('lang_v1.list_expenses'), [ExpenseController::class, 'index']),
                self::item(__('expense.add_expense'), [ExpenseController::class, 'create'], can: 'expense.add'),
                self::item(__('expense.expense_categories'), [ExpenseCategoryController::class, 'index'], can: 'expense.add'),
                self::item(__('account.list_accounts'), [AccountController::class, 'index']),
                self::item(__('account.balance_sheet'), [AccountReportsController::class, 'balanceSheet']),
                self::item(__('account.trial_balance'), [AccountReportsController::class, 'trialBalance']),
                self::item(__('lang_v1.cash_flow'), [AccountController::class, 'cashFlow']),
                self::item(__('report.profit_loss'), [ReportController::class, 'getProfitLoss'], can: 'profit_loss_report.view'),
                self::item(__('report.purchase_sell_report'), [ReportController::class, 'getPurchaseSell'], can: 'purchase_n_sell_report.view'),
                self::item(__('report.stock_report'), [ReportController::class, 'getStockReport'], can: 'stock_report.view'),
                self::item(__('Advanced Dashboard'), [ReportController::class, 'getBusinessAdvanceAnalytics']),
            ],
        ];
    }

    private static function onlineStore(): array
    {
        return [
            'label' => __('lang_v1.nav_store'),
            'icon' => 'store',
            'items' => [
                self::item(__('ecommerce.dashboard'), route: route('ecommerce.home')),
                self::item(__('ecommerce.products'), route: route('ecommerce.products')),
                self::item(__('ecommerce.orders'), [SellController::class, 'index'], ['type' => 'ecommerce']),
                self::item(__('ecommerce.customers'), [ContactController::class, 'index'], ['type' => 'customer', 'source' => 'ecommerce']),
                self::item(__('ecommerce.settings'), [BusinessController::class, 'getEcommerceSettings']),
            ],
        ];
    }

    // -----------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------

    /**
     * @param  array{0: class-string, 1: string}|null  $action
     * @param  array<string, mixed>  $params
     */
    private static function item(
        string $label,
        ?array $action = null,
        array $params = [],
        ?string $can = null,
        ?string $route = null,
        bool $spa = false,
    ): array {
        return [
            'label' => $label,
            'url' => $route ?? action($action, $params),
            'can' => $can,
            'spa' => $spa,
        ];
    }

    /**
     * Drop anything the current user cannot reach, then drop groups that end
     * up empty. Keeps the sidebar honest: no links that lead to a 403.
     *
     * @param  array<string, mixed>  $group
     * @return array<string, mixed>|null
     */
    private static function prune(array $group): ?array
    {
        $user = Auth::user();

        $items = array_values(array_filter(
            $group['items'],
            fn (array $item) => empty($item['can']) || ($user && $user->can($item['can'])),
        ));

        // A group with its own URL (like Today) is a link, not a container,
        // so it survives having no children.
        if (empty($items) && empty($group['url'])) {
            return null;
        }

        return [...$group, 'items' => $items];
    }
}
