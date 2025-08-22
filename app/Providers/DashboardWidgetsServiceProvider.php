<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Utils\ModuleUtil;

class DashboardWidgetsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(ModuleUtil $moduleUtil)
    {
        $this->registerDashboardWidgets($moduleUtil);
    }

    /**
     * Register dashboard widgets.
     *
     * @param ModuleUtil $moduleUtil
     * @return void
     */
    public function registerDashboardWidgets($moduleUtil)
    {
        // Register dashboard widgets
        $moduleUtil->addDashboardWidget('dashboard_widget', function () {
            $widgets = [];

            // Total Sell Widget
            $widgets['after_sale_purchase_totals'][] = view('widgets.total_sell')->render();
            
            // Net Widget
            $widgets['after_sale_purchase_totals'][] = view('widgets.net')->render();
            
            // Invoice Due Widget
            $widgets['after_sale_purchase_totals'][] = view('widgets.invoice_due')->render();
            
            // Total Sell Return Widget
            $widgets['after_sale_purchase_totals'][] = view('widgets.total_sell_return')->render();
            
            // Total Purchase Widget
            $widgets['after_sale_purchase_totals'][] = view('widgets.total_purchase')->render();
            
            // Purchase Due Widget
            $widgets['after_sale_purchase_totals'][] = view('widgets.purchase_due')->render();
            
            // Total Purchase Return Widget
            $widgets['after_sale_purchase_totals'][] = view('widgets.total_purchase_return')->render();
            
            // Total Expense Widget
            $widgets['after_sale_purchase_totals'][] = view('widgets.total_expense')->render();

            return $widgets;
        });
    }
}