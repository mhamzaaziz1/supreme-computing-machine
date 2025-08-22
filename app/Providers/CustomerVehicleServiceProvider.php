<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CustomerVehicleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Register the tab menu item
        $this->app->resolving('App\Utils\ModuleUtil', function ($moduleUtil) {
            $this->addVehicleTabToContactView($moduleUtil);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Add vehicle tab to contact view
     *
     * @param  object  $moduleUtil
     * @return void
     */
    private function addVehicleTabToContactView($moduleUtil)
    {
        // Get the original getModuleData method
        $originalGetModuleData = \Closure::bind(function ($function_name, $arguments = null, $get_data_from_modules = []) use ($moduleUtil) {
            return $moduleUtil->getModuleData($function_name, $arguments, $get_data_from_modules);
        }, $this);

        // Create a new method that extends the original
        $extendedGetModuleData = \Closure::bind(function ($function_name, $arguments = null, $get_data_from_modules = []) use ($moduleUtil, $originalGetModuleData) {
            $data = $originalGetModuleData($function_name, $arguments, $get_data_from_modules);

            if ($function_name == 'get_contact_view_tabs') {
                if (in_array(request()->input('type', ''), ['customer', 'both']) || 
                    (isset($arguments) && in_array($arguments->type, ['customer', 'both']))) {

                    $data['customer_vehicles'] = [
                        [
                            'tab_menu_path' => 'contact.partials.vehicles_tab_menu',
                            'tab_content_path' => 'contact.partials.vehicles_tab_content'
                        ]
                    ];
                }
            }

            return $data;
        }, $this);

        // Replace the getModuleData method in the ModuleUtil class
        $moduleUtil->getModuleData = $extendedGetModuleData;
    }
}
