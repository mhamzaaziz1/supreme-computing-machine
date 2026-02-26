<!-- Left side column. contains the logo and sidebar -->
<aside class="side-bar tw-relative tw-hidden lg:tw-flex tw-flex-col tw-shrink-0 tw-w-72 tw-h-[calc(100vh-2rem)] tw-m-4 tw-rounded-xl tw-bg-white dark:tw-bg-dark-surface tw-border tw-border-gray-200 dark:tw-border-gray-800 tw-transition-all tw-duration-300 tw-overflow-hidden tw-z-30">

    <!-- Brand / Logo -->
    <a href="{{route('home')}}"
        class="tw-flex tw-items-center tw-gap-3 tw-h-20 tw-px-6 tw-border-b tw-border-gray-100 dark:tw-border-gray-800 tw-shrink-0">
        <div class="tw-flex tw-items-center tw-justify-center tw-w-10 tw-h-10 tw-rounded-xl tw-bg-primary-600 tw-text-white tw-shadow-lg tw-shadow-primary-500/30">
            <span class="tw-text-xl tw-font-bold">{{ substr(Session::get('business.name'), 0, 1) }}</span>
        </div>
        <div class="tw-flex tw-flex-col">
            <h1 class="tw-text-base tw-font-bold tw-text-gray-900 dark:tw-text-white tw-tracking-tight tw-leading-tight">
                {{ Session::get('business.name') }}
            </h1>
            <span class="tw-flex tw-items-center tw-gap-1.5 tw-text-[10px] tw-font-medium tw-uppercase tw-tracking-wider tw-text-primary-600 dark:tw-text-primary-400">
                <span class="tw-w-1.5 tw-h-1.5 tw-rounded-full tw-bg-green-500 tw-animate-pulse"></span>
                Online
            </span>
        </div>
    </a>


    @php
        $enabled_modules = !empty(session('business.enabled_modules')) ? session('business.enabled_modules') : [];
        $common_settings = !empty(session('business.common_settings')) ? session('business.common_settings') : [];
        $pos_settings = !empty(session('business.pos_settings')) ? json_decode(session('business.pos_settings'), true) : [];
        $is_admin = auth()->user()->hasRole('Admin#' . session('business.id')) ? true : false;
    @endphp

    <style>
    <style>
        /* Custom Scrollbar */
        .sidebar-menu-container::-webkit-scrollbar { width: 4px; }
        .sidebar-menu-container::-webkit-scrollbar-track { background: transparent; }
        .sidebar-menu-container::-webkit-scrollbar-thumb { background-color: rgba(156, 163, 175, 0.3); border-radius: 20px; }
        
        /* --- Clean Flat Dashboard Styles (New Design) --- */

        /* 1. Icon Box Styling (Common) */
        .sidebar-menu i, .sidebar-menu svg {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            margin-right: 12px;
            background-color: transparent !important; /* No background for inactive */
            color: #6b7280; /* Tailwind gray-500 */
            box-shadow: none !important; /* Flat */
            font-size: 1rem; /* Adjust based on icon size needed */
            transition: all 0.2s ease;
        }

        /* 2. Active State (The Item Row) */
        .sidebar-menu > li.active > a {
            background-color: #3b82f6 !important; /* Tailwind blue-500 */
            color: #ffffff !important; /* White text */
            border-radius: 0.375rem !important; /* Standard rounded */
            box-shadow: none !important; /* Flat */
        }
        
        .dark .sidebar-menu > li.active > a {
            background-color: #2563eb !important; /* Tailwind blue-600 */
        }

        /* 3. Active Icon */
        .sidebar-menu > li.active > a > i, 
        .sidebar-menu > li.active > a > svg {
            color: #ffffff !important; /* White icon */
        }

        /* Hover Effects (Inactive Items) */
        .sidebar-menu > li:not(.active) > a:hover {
            background-color: #f3f4f6; /* Tailwind gray-100 */
            border-radius: 0.375rem;
        }

        .dark .sidebar-menu > li:not(.active) > a:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        /* General Item Spacing */
        .sidebar-menu > li > a {
            margin-bottom: 4px; /* Slight spacing between items */
            padding: 10px 16px;
        }
        
    </style>
    
    <div class="sidebar-menu-container tw-flex-1 tw-overflow-y-auto tw-py-4 tw-px-3">
        <ul class="sidebar-menu tree tw-flex tw-flex-col tw-gap-1.5" data-widget="tree">
        <!-- Home -->
        <li class="{{ request()->segment(1) == 'home' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/5">
            <a href="{{action([\App\Http\Controllers\HomeController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2.5 tw-text-gray-700 dark:tw-text-gray-300 tw-font-medium tw-text-sm tw-rounded-lg group">
                <i class="fa fa-tachometer-alt tw-w-5 tw-h-5 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i> 
                <span>@lang('home.home')</span>
            </a>
        </li>

        <!-- POS V2 (New) -->
        <li class="{{ request()->segment(1) == 'pos' && request()->segment(2) == 'v2' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/5">
            <a href="{{ route('pos.v2') }}" class="tw-flex tw-items-center tw-px-3 tw-py-2.5 tw-text-gray-700 dark:tw-text-gray-300 tw-font-medium tw-text-sm tw-rounded-lg group">
                <i class="fa fa-cash-register tw-w-5 tw-h-5 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i> 
                <span>POS V2</span>
            </a>
        </li>

        <!-- Advanced Dashboard -->
        <li class="{{ request()->segment(2) == 'business-advance-analytics' ? 'active' : '' }}">
            <a href="{{action([\App\Http\Controllers\ReportController::class, 'getBusinessAdvanceAnalytics'])}}">
                <i class="fa fa-chart-line"></i> <span>@lang('Advanced Dashboard')</span>
            </a>
        </li>

        <!-- User Management -->
        @if(auth()->user()->can('user.view') || auth()->user()->can('user.create') || auth()->user()->can('roles.view'))
        <li class="treeview {{ in_array(request()->segment(1), ['users', 'roles', 'sales-commission-agents', 'user-locations']) ? 'active menu-open' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-50 dark:hover:tw-bg-white/5">
            <a href="#" class="tw-flex tw-items-center tw-justify-between tw-px-3 tw-py-2.5 tw-text-gray-700 dark:tw-text-gray-300 tw-font-medium tw-text-sm tw-rounded-lg group">
                <div class="tw-flex tw-items-center">
                    <i class="fa fa-users tw-w-5 tw-h-5 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                    <span>@lang('user.user_management')</span>
                </div>
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right tw-transition-transform tw-duration-200 group-[.menu-open]:tw-rotate-[-90deg]"></i>
                </span>
            </a>
            <ul class="treeview-menu tw-bg-gray-50 dark:tw-bg-dark-bg/50 tw-rounded-lg tw-px-2 tw-py-1 tw-mt-1 tw-mx-2">
                @can('user.view')
                <li class="{{ request()->segment(1) == 'users' ? 'active' : '' }}">
                    <a href="{{action([\App\Http\Controllers\ManageUserController::class, 'index'])}}">
                        <i class="fa fa-user"></i>
                        <span>@lang('user.users')</span>
                    </a>
                </li>
                @endcan
                @can('roles.view')
                <li class="{{ request()->segment(1) == 'roles' ? 'active' : '' }}">
                    <a href="{{action([\App\Http\Controllers\RoleController::class, 'index'])}}">
                        <i class="fa fa-briefcase"></i>
                        <span>@lang('user.roles')</span>
                    </a>
                </li>
                @endcan
                @can('user.create')
                <li class="{{ request()->segment(1) == 'sales-commission-agents' ? 'active' : '' }}">
                    <a href="{{action([\App\Http\Controllers\SalesCommissionAgentController::class, 'index'])}}">
                        <i class="fa fa-handshake"></i>
                        <span>@lang('lang_v1.sales_commission_agents')</span>
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endif

        <!-- Contacts -->
        @if(auth()->user()->can('supplier.view') || auth()->user()->can('customer.view') || auth()->user()->can('supplier.view_own') || auth()->user()->can('customer.view_own'))
        <li class="treeview {{ in_array(request()->segment(1), ['contacts', 'customer-group']) ? 'active menu-open' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-50 dark:hover:tw-bg-white/5">
            <a href="#" class="tw-flex tw-items-center tw-justify-between tw-px-3 tw-py-2.5 tw-text-gray-700 dark:tw-text-gray-300 tw-font-medium tw-text-sm tw-rounded-lg group">
                <div class="tw-flex tw-items-center">
                    <i class="fa fa-address-book tw-w-5 tw-h-5 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                    <span>@lang('contact.contacts')</span>
                </div>
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right tw-transition-transform tw-duration-200 group-[.menu-open]:tw-rotate-[-90deg]"></i>
                </span>
            </a>
            <ul class="treeview-menu tw-bg-gray-50 dark:tw-bg-dark-bg/50 tw-rounded-lg tw-px-2 tw-py-1 tw-mt-1 tw-mx-2">
                @if(auth()->user()->can('supplier.view') || auth()->user()->can('supplier.view_own'))
                <li class="{{ request()->input('type') == 'supplier' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ContactController::class, 'index'], ['type' => 'supplier'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-star tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('report.supplier')</span>
                    </a>
                </li>
                @endif
                @if(auth()->user()->can('customer.view') || auth()->user()->can('customer.view_own'))
                <li class="{{ request()->input('type') == 'customer' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ContactController::class, 'index'], ['type' => 'customer'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-star tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('report.customer')</span>
                    </a>
                </li>
                <li class="{{ request()->segment(1) == 'customer-group' ? 'active' : '' }}">
                    <a href="{{action([\App\Http\Controllers\CustomerGroupController::class, 'index'])}}">
                        <i class="fa fa-users"></i>
                        <span>@lang('lang_v1.customer_groups')</span>
                    </a>
                </li>
                @endif
                @if(auth()->user()->can('supplier.create') || auth()->user()->can('customer.create'))
                <li class="{{ request()->segment(1) == 'contacts' && request()->segment(2) == 'import' ? 'active' : '' }}">
                    <a href="{{action([\App\Http\Controllers\ContactController::class, 'getImportContacts'])}}">
                        <i class="fa fa-download"></i>
                        <span>@lang('lang_v1.import_contacts')</span>
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        <!-- Customer Routes -->
        @if(auth()->user()->can('customer.view') || auth()->user()->can('customer.view_own'))
        <li class="{{ request()->segment(1) == 'customer-route' ? 'active' : '' }}">
            <a href="{{action([\App\Http\Controllers\CustomerRouteController::class, 'index'])}}">
                <i class="fa fa-route"></i>
                <span>@lang('lang_v1.customer_routes')</span>
            </a>
        </li>
        @endif

        <!-- Vehicles -->
        @if(auth()->user()->can('customer.view') || auth()->user()->can('customer.view_own'))
        <li class="{{ request()->segment(1) == 'vehicles' ? 'active' : '' }}">
            <a href="{{action([\App\Http\Controllers\CustomerVehicleController::class, 'index'])}}">
                <i class="fa fa-truck"></i>
                <span>@lang('lang_v1.vehicles')</span>
            </a>
        </li>
        @endif

        <!-- Supply Chain Vehicles -->
        @if(auth()->user()->can('customer.view') || auth()->user()->can('customer.view_own'))
        <li class="treeview {{ in_array(request()->segment(1), ['supply-chain-vehicles', 'vehicle-route-assignments', 'vehicle-expenses']) ? 'active menu-open' : '' }}">
            <a href="#">
                <i class="fa fa-shipping-fast"></i>
                <span>@lang('lang_v1.supply_chain_vehicles')</span>
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu">
                <li class="{{ request()->segment(1) == 'supply-chain-vehicles' && empty(request()->segment(2)) ? 'active' : '' }}">
                    <a href="{{action([\App\Http\Controllers\SupplyChainVehicleController::class, 'index'])}}">
                        <i class="fa fa-truck-moving"></i>
                        <span>@lang('lang_v1.all_vehicles')</span>
                    </a>
                </li>
                <li class="{{ request()->segment(1) == 'vehicle-route-assignments' ? 'active' : '' }}">
                    <a href="{{action([\App\Http\Controllers\VehicleRouteAssignmentController::class, 'index'])}}">
                        <i class="fa fa-map-marked-alt"></i>
                        <span>@lang('lang_v1.assign_route')</span>
                    </a>
                </li>
                <li class="{{ request()->segment(1) == 'vehicle-expenses' ? 'active' : '' }}">
                    <a href="{{action([\App\Http\Controllers\SupplyChainVehicleExpenseController::class, 'index'])}}">
                        <i class="fa fa-money-bill"></i>
                        <span>@lang('lang_v1.vehicle_expenses')</span>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        <!-- Geofencing -->
        @if(auth()->user()->can('customer.view') || auth()->user()->can('customer.view_own'))
        <li class="treeview {{ in_array(request()->segment(1), ['route-assignments', 'visit-logs', 'violation-logs']) || request()->segment(2) == 'route-coverage-report' ? 'active menu-open' : '' }}">
            <a href="#">
                <i class="fa fa-map-marker-alt"></i>
                <span>@lang('lang_v1.geofencing')</span>
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu">
                <li class="{{ request()->segment(1) == 'route-assignments' ? 'active' : '' }}">
                    <a href="{{action([\App\Http\Controllers\RouteSellerAssignmentController::class, 'index'])}}">
                        <i class="fa fa-user-tag"></i>
                        <span>@lang('lang_v1.route_assignments')</span>
                    </a>
                </li>
                <li class="{{ request()->segment(1) == 'visit-logs' ? 'active' : '' }}">
                    <a href="{{action([\App\Http\Controllers\RouteVisitLogController::class, 'index'])}}">
                        <i class="fa fa-clipboard-list"></i>
                        <span>@lang('lang_v1.visit_logs')</span>
                    </a>
                </li>
                <li class="{{ request()->segment(1) == 'violation-logs' ? 'active' : '' }}">
                    <a href="{{action([\App\Http\Controllers\GeofenceViolationLogController::class, 'index'])}}">
                        <i class="fa fa-exclamation-triangle"></i>
                        <span>@lang('lang_v1.violation_logs')</span>
                    </a>
                </li>
                <li class="{{ request()->segment(2) == 'route-coverage-report' ? 'active' : '' }}">
                    <a href="{{action([\App\Http\Controllers\ReportController::class, 'getRouteCoverageReport'])}}">
                        <i class="fa fa-chart-area"></i>
                        <span>@lang('lang_v1.route_coverage_report')</span>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        <!-- Products -->
        @if(auth()->user()->can('product.view') || auth()->user()->can('product.create') || auth()->user()->can('brand.view') || auth()->user()->can('unit.view') || auth()->user()->can('category.view'))
        <li class="treeview {{ in_array(request()->segment(1), ['products', 'brands', 'units', 'taxonomies', 'variation-templates', 'selling-price-group', 'warranties', 'labels']) ? 'active menu-open' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-50 dark:hover:tw-bg-white/5">
            <a href="#" class="tw-flex tw-items-center tw-justify-between tw-px-3 tw-py-2.5 tw-text-gray-700 dark:tw-text-gray-300 tw-font-medium tw-text-sm tw-rounded-lg group">
                <div class="tw-flex tw-items-center">
                    <i class="fa fa-cubes tw-w-5 tw-h-5 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                    <span>@lang('sale.products')</span>
                </div>
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right tw-transition-transform tw-duration-200 group-[.menu-open]:tw-rotate-[-90deg]"></i>
                </span>
            </a>
            <ul class="treeview-menu tw-bg-gray-50 dark:tw-bg-dark-bg/50 tw-rounded-lg tw-px-2 tw-py-1 tw-mt-1 tw-mx-2">
                @can('product.view')
                <li class="{{ request()->segment(1) == 'products' && request()->segment(2) == '' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ProductController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-list tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.list_products')</span>
                    </a>
                </li>
                @endcan
                @can('product.create')
                <li class="{{ request()->segment(1) == 'products' && request()->segment(2) == 'create' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ProductController::class, 'create'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-plus-circle tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('product.add_product')</span>
                    </a>
                </li>
                @endcan
                @can('product.view')
                <li class="{{ request()->segment(1) == 'labels' && request()->segment(2) == 'show' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\LabelsController::class, 'show'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-barcode tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('barcode.print_labels')</span>
                    </a>
                </li>
                @endcan
                @can('product.create')
                <li class="{{ request()->segment(1) == 'variation-templates' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\VariationTemplateController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-circle tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('product.variations')</span>
                    </a>
                </li>
                <li class="{{ request()->segment(1) == 'units' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\UnitController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-balance-scale tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('unit.units')</span>
                    </a>
                </li>
                <li class="{{ request()->segment(1) == 'taxonomies' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\TaxonomyController::class, 'index']) . '?type=product'}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-tags tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('category.categories')</span>
                    </a>
                </li>
                <li class="{{ request()->segment(1) == 'brands' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\BrandController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-gem tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('brand.brands')</span>
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endif

        <!-- Purchases -->
        @if(in_array('purchases', $enabled_modules) && (auth()->user()->can('purchase.view') || auth()->user()->can('purchase.create') || auth()->user()->can('purchase.update')))
        <li class="treeview {{ in_array(request()->segment(1), ['purchases', 'purchase-return', 'purchase-order', 'purchase-requisition']) ? 'active menu-open' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-50 dark:hover:tw-bg-white/5">
            <a href="#" class="tw-flex tw-items-center tw-justify-between tw-px-3 tw-py-2.5 tw-text-gray-700 dark:tw-text-gray-300 tw-font-medium tw-text-sm tw-rounded-lg group">
                <div class="tw-flex tw-items-center">
                    <i class="fa fa-arrow-circle-down tw-w-5 tw-h-5 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                    <span>@lang('purchase.purchases')</span>
                </div>
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right tw-transition-transform tw-duration-200 group-[.menu-open]:tw-rotate-[-90deg]"></i>
                </span>
            </a>
            <ul class="treeview-menu tw-bg-gray-50 dark:tw-bg-dark-bg/50 tw-rounded-lg tw-px-2 tw-py-1 tw-mt-1 tw-mx-2">
                @if(auth()->user()->can('purchase.view') || auth()->user()->can('view_own_purchase'))
                <li class="{{ request()->segment(1) == 'purchases' && request()->segment(2) == null ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\PurchaseController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-list tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('purchase.list_purchase')</span>
                    </a>
                </li>
                @endif
                @if(auth()->user()->can('purchase.create'))
                <li class="{{ request()->segment(1) == 'purchases' && request()->segment(2) == 'create' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\PurchaseController::class, 'create'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-plus-circle tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('purchase.add_purchase')</span>
                    </a>
                </li>
                @endif
                @if(auth()->user()->can('purchase.update'))
                <li class="{{ request()->segment(1) == 'purchase-return' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\PurchaseReturnController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-undo tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.list_purchase_return')</span>
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        <!-- Sell -->
        @if($is_admin || auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'view_own_sell_only']))
        <li class="treeview {{ in_array(request()->segment(1), ['sells', 'pos', 'sell-return', 'shipments', 'discount', 'subscriptions', 'import-sales']) ? 'active menu-open' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-50 dark:hover:tw-bg-white/5">
            <a href="#" class="tw-flex tw-items-center tw-justify-between tw-px-3 tw-py-2.5 tw-text-gray-700 dark:tw-text-gray-300 tw-font-medium tw-text-sm tw-rounded-lg group">
                <div class="tw-flex tw-items-center">
                    <i class="fa fa-arrow-circle-up tw-w-5 tw-h-5 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                    <span>@lang('sale.sale')</span>
                </div>
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right tw-transition-transform tw-duration-200 group-[.menu-open]:tw-rotate-[-90deg]"></i>
                </span>
            </a>
            <ul class="treeview-menu tw-bg-gray-50 dark:tw-bg-dark-bg/50 tw-rounded-lg tw-px-2 tw-py-1 tw-mt-1 tw-mx-2">
                @if($is_admin || auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'view_own_sell_only']))
                <li class="{{ request()->segment(1) == 'sells' && request()->segment(2) == null ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\SellController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-list tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.all_sales')</span>
                    </a>
                </li>
                @endif
                @can('sell.create')
                @if(in_array('pos_sale', $enabled_modules))
                <li class="{{ request()->segment(1) == 'pos' && request()->segment(2) == 'create' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\SellPosController::class, 'create'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-cash-register tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('sale.pos_sale')</span>
                    </a>
                </li>
                @endif
                @endcan
                @if(in_array('add_sale', $enabled_modules) && auth()->user()->can('direct_sell.access'))
                <li class="{{ request()->segment(1) == 'sells' && request()->segment(2) == 'create' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\SellController::class, 'create'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-plus-circle tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('sale.add_sale')</span>
                    </a>
                </li>
                @endif
                @if(auth()->user()->can('access_sell_return') || auth()->user()->can('access_own_sell_return'))
                <li class="{{ request()->segment(1) == 'sell-return' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\SellReturnController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-undo tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.list_sell_return')</span>
                    </a>
                </li>
                @endif
                @if(auth()->user()->can('discount.access'))
                <li class="{{ request()->segment(1) == 'discount' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\DiscountController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-percent tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.discounts')</span>
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        <!-- Stock Transfers -->
        @if(in_array('stock_transfers', $enabled_modules) && (auth()->user()->can('purchase.view') || auth()->user()->can('purchase.create') || auth()->user()->can('view_own_purchase')))
        <li class="treeview {{ in_array(request()->segment(1), ['stock-transfers']) ? 'active menu-open' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-50 dark:hover:tw-bg-white/5">
            <a href="#" class="tw-flex tw-items-center tw-justify-between tw-px-3 tw-py-2.5 tw-text-gray-700 dark:tw-text-gray-300 tw-font-medium tw-text-sm tw-rounded-lg group">
                <div class="tw-flex tw-items-center">
                    <i class="fa fa-truck-loading tw-w-5 tw-h-5 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                    <span>@lang('lang_v1.stock_transfers')</span>
                </div>
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right tw-transition-transform tw-duration-200 group-[.menu-open]:tw-rotate-[-90deg]"></i>
                </span>
            </a>
            <ul class="treeview-menu tw-bg-gray-50 dark:tw-bg-dark-bg/50 tw-rounded-lg tw-px-2 tw-py-1 tw-mt-1 tw-mx-2">
                @if(auth()->user()->can('purchase.view') || auth()->user()->can('view_own_purchase'))
                <li class="{{ request()->segment(1) == 'stock-transfers' && request()->segment(2) == null ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\StockTransferController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-list tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.list_stock_transfers')</span>
                    </a>
                </li>
                @endif
                @if(auth()->user()->can('purchase.create'))
                <li class="{{ request()->segment(1) == 'stock-transfers' && request()->segment(2) == 'create' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\StockTransferController::class, 'create'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-plus-circle tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.add_stock_transfer')</span>
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        <!-- Stock Adjustment -->
        @if(in_array('stock_adjustment', $enabled_modules) && (auth()->user()->can('purchase.view') || auth()->user()->can('purchase.create') || auth()->user()->can('view_own_purchase')))
        <li class="treeview {{ in_array(request()->segment(1), ['stock-adjustments']) ? 'active menu-open' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-50 dark:hover:tw-bg-white/5">
            <a href="#" class="tw-flex tw-items-center tw-justify-between tw-px-3 tw-py-2.5 tw-text-gray-700 dark:tw-text-gray-300 tw-font-medium tw-text-sm tw-rounded-lg group">
                <div class="tw-flex tw-items-center">
                    <i class="fa fa-sliders-h tw-w-5 tw-h-5 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                    <span>@lang('stock_adjustment.stock_adjustment')</span>
                </div>
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right tw-transition-transform tw-duration-200 group-[.menu-open]:tw-rotate-[-90deg]"></i>
                </span>
            </a>
            <ul class="treeview-menu tw-bg-gray-50 dark:tw-bg-dark-bg/50 tw-rounded-lg tw-px-2 tw-py-1 tw-mt-1 tw-mx-2">
                @if(auth()->user()->can('purchase.view') || auth()->user()->can('view_own_purchase'))
                <li class="{{ request()->segment(1) == 'stock-adjustments' && request()->segment(2) == null ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\StockAdjustmentController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-list tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('stock_adjustment.list')</span>
                    </a>
                </li>
                @endif
                @if(auth()->user()->can('purchase.create'))
                <li class="{{ request()->segment(1) == 'stock-adjustments' && request()->segment(2) == 'create' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\StockAdjustmentController::class, 'create'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-plus-circle tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('stock_adjustment.add')</span>
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        <!-- Expenses -->
        @if(in_array('expenses', $enabled_modules) && (auth()->user()->can('all_expense.access') || auth()->user()->can('view_own_expense')))
        <li class="treeview {{ in_array(request()->segment(1), ['expenses', 'expense-categories']) ? 'active menu-open' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-50 dark:hover:tw-bg-white/5">
            <a href="#" class="tw-flex tw-items-center tw-justify-between tw-px-3 tw-py-2.5 tw-text-gray-700 dark:tw-text-gray-300 tw-font-medium tw-text-sm tw-rounded-lg group">
                <div class="tw-flex tw-items-center">
                    <i class="fa fa-minus-circle tw-w-5 tw-h-5 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                    <span>@lang('expense.expenses')</span>
                </div>
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right tw-transition-transform tw-duration-200 group-[.menu-open]:tw-rotate-[-90deg]"></i>
                </span>
            </a>
            <ul class="treeview-menu tw-bg-gray-50 dark:tw-bg-dark-bg/50 tw-rounded-lg tw-px-2 tw-py-1 tw-mt-1 tw-mx-2">
                <li class="{{ request()->segment(1) == 'expenses' && request()->segment(2) == null ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ExpenseController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-list tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.list_expenses')</span>
                    </a>
                </li>
                @can('expense.add')
                <li class="{{ request()->segment(1) == 'expenses' && request()->segment(2) == 'create' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ExpenseController::class, 'create'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-plus-circle tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('expense.add_expense')</span>
                    </a>
                </li>
                @endcan
                @can('expense.add')
                <li class="{{ request()->segment(1) == 'expense-categories' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ExpenseCategoryController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-circle tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('expense.expense_categories')</span>
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endif

        <!-- Accounts -->
        @if(auth()->user()->can('account.access') && in_array('account', $enabled_modules))
        <li class="treeview {{ request()->segment(1) == 'account' ? 'active menu-open' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-50 dark:hover:tw-bg-white/5">
            <a href="#" class="tw-flex tw-items-center tw-justify-between tw-px-3 tw-py-2.5 tw-text-gray-700 dark:tw-text-gray-300 tw-font-medium tw-text-sm tw-rounded-lg group">
                <div class="tw-flex tw-items-center">
                    <i class="fa fa-money-bill-alt tw-w-5 tw-h-5 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                    <span>@lang('lang_v1.payment_accounts')</span>
                </div>
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right tw-transition-transform tw-duration-200 group-[.menu-open]:tw-rotate-[-90deg]"></i>
                </span>
            </a>
            <ul class="treeview-menu tw-bg-gray-50 dark:tw-bg-dark-bg/50 tw-rounded-lg tw-px-2 tw-py-1 tw-mt-1 tw-mx-2">
                <li class="{{ request()->segment(1) == 'account' && request()->segment(2) == 'account' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\AccountController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-list tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('account.list_accounts')</span>
                    </a>
                </li>
                <li class="{{ request()->segment(1) == 'account' && request()->segment(2) == 'balance-sheet' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\AccountReportsController::class, 'balanceSheet'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-book tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('account.balance_sheet')</span>
                    </a>
                </li>
                <li class="{{ request()->segment(1) == 'account' && request()->segment(2) == 'trial-balance' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\AccountReportsController::class, 'trialBalance'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-balance-scale tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('account.trial_balance')</span>
                    </a>
                </li>
                <li class="{{ request()->segment(1) == 'account' && request()->segment(2) == 'cash-flow' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\AccountController::class, 'cashFlow'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-exchange-alt tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.cash_flow')</span>
                    </a>
                </li>
            </ul>
        </li>
        @endif
        
        <!-- Reports -->
        @if(auth()->user()->can('purchase_n_sell_report.view') || auth()->user()->can('contacts_report.view') || auth()->user()->can('stock_report.view') || auth()->user()->can('tax_report.view') || auth()->user()->can('trending_product_report.view') || auth()->user()->can('sales_representative.view') || auth()->user()->can('expense_report.view'))
        <li class="treeview {{ request()->segment(1) == 'reports' ? 'active menu-open' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-50 dark:hover:tw-bg-white/5">
            <a href="#" class="tw-flex tw-items-center tw-justify-between tw-px-3 tw-py-2.5 tw-text-gray-700 dark:tw-text-gray-300 tw-font-medium tw-text-sm tw-rounded-lg group">
                <div class="tw-flex tw-items-center">
                    <i class="fa fa-chart-bar tw-w-5 tw-h-5 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                    <span>@lang('report.reports')</span>
                </div>
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right tw-transition-transform tw-duration-200 group-[.menu-open]:tw-rotate-[-90deg]"></i>
                </span>
            </a>
            <ul class="treeview-menu tw-bg-gray-50 dark:tw-bg-dark-bg/50 tw-rounded-lg tw-px-2 tw-py-1 tw-mt-1 tw-mx-2">
                @can('profit_loss_report.view')
                <li class="{{ request()->segment(2) == 'profit-loss' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ReportController::class, 'getProfitLoss'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-file-invoice tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('report.profit_loss')</span>
                    </a>
                </li>
                @endcan
                @can('purchase_n_sell_report.view')
                <li class="{{ request()->segment(2) == 'purchase-sell' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ReportController::class, 'getPurchaseSell'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-exchange-alt tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('report.purchase_sell_report')</span>
                    </a>
                </li>
                @endcan
                @can('stock_report.view')
                <li class="{{ request()->segment(2) == 'stock-report' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ReportController::class, 'getStockReport'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-boxes tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('report.stock_report')</span>
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endif

        <!-- Booking -->
        @if(in_array('booking', $enabled_modules) && (auth()->user()->can('crud_all_bookings') || auth()->user()->can('crud_own_bookings')))
        <li class="{{ request()->segment(1) == 'bookings' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
            <a href="{{action([\App\Http\Controllers\Restaurant\BookingController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                <i class="fa fa-calendar-check-o tw-w-5 tw-h-5 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                <span>@lang('restaurant.bookings')</span>
            </a>
        </li>
        @endif

        <!-- Kitchen -->
        @if(in_array('kitchen', $enabled_modules))
        <li class="{{ request()->segment(1) == 'modules' && request()->segment(2) == 'kitchen' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
            <a href="{{action([\App\Http\Controllers\Restaurant\KitchenController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                <i class="fa fa-fire tw-w-5 tw-h-5 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                <span>@lang('restaurant.kitchen')</span>
            </a>
        </li>
        @endif

        <!-- Service Staff -->
        @if(in_array('service_staff', $enabled_modules))
        <li class="{{ request()->segment(1) == 'modules' && request()->segment(2) == 'orders' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
            <a href="{{action([\App\Http\Controllers\Restaurant\OrderController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                <i class="fa fa-list-alt tw-w-5 tw-h-5 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                <span>@lang('restaurant.orders')</span>
            </a>
        </li>
        @endif

        <!-- Notification Template -->
        @if(auth()->user()->can('send_notifications'))
        <li class="{{ request()->segment(1) == 'notification-templates' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
            <a href="{{action([\App\Http\Controllers\NotificationTemplateController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                <i class="fa fa-envelope tw-w-5 tw-h-5 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                <span>@lang('lang_v1.notification_templates')</span>
            </a>
        </li>
        @endif

        <!-- Ecommerce -->
        <li class="treeview {{ in_array(request()->segment(1), ['shop', 'ecommerce']) ? 'active menu-open' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-50 dark:hover:tw-bg-white/5">
            <a href="#" class="tw-flex tw-items-center tw-justify-between tw-px-3 tw-py-2.5 tw-text-gray-700 dark:tw-text-gray-300 tw-font-medium tw-text-sm tw-rounded-lg group">
                <div class="tw-flex tw-items-center">
                    <i class="fa fa-shopping-cart tw-w-5 tw-h-5 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                    <span>@lang('ecommerce.ecommerce')</span>
                </div>
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right tw-transition-transform tw-duration-200 group-[.menu-open]:tw-rotate-[-90deg]"></i>
                </span>
            </a>
            <ul class="treeview-menu tw-bg-gray-50 dark:tw-bg-dark-bg/50 tw-rounded-lg tw-px-2 tw-py-1 tw-mt-1 tw-mx-2">
                <li class="{{ request()->segment(1) == '' && empty(request()->segment(2)) ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{ route('ecommerce.home') }}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-tachometer-alt tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('ecommerce.dashboard')</span>
                    </a>
                </li>
                 <li class="{{ request()->segment(1) == 'shop' && request()->segment(2) == 'products' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{ route('ecommerce.products') }}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-cube tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('ecommerce.products')</span>
                    </a>
                </li>
                <li class="{{ request()->segment(1) == 'sells' && request()->input('type') == 'ecommerce' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{ action([\App\Http\Controllers\SellController::class, 'index'], ['type' => 'ecommerce']) }}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-cart-arrow-down tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('ecommerce.orders')</span>
                    </a>
                </li>
                 <li class="{{ request()->segment(1) == 'contacts' && request()->input('source') == 'ecommerce' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{ action([\App\Http\Controllers\ContactController::class, 'index'], ['type' => 'customer', 'source' => 'ecommerce']) }}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-address-book tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('ecommerce.customers')</span>
                    </a>
                </li>
                 <li class="{{ request()->segment(1) == 'business' && request()->segment(2) == 'ecommerce-settings' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{ action([\App\Http\Controllers\BusinessController::class, 'getEcommerceSettings']) }}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-cogs tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('ecommerce.settings')</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Settings -->
        @if(auth()->user()->can('business_settings.access') || auth()->user()->can('barcode_settings.access') || auth()->user()->can('invoice_settings.access') || auth()->user()->can('tax_rate.view') || auth()->user()->can('tax_rate.create') || auth()->user()->can('access_package_subscriptions'))
        <li class="treeview {{ in_array(request()->segment(1), ['business', 'tax-rates', 'barcodes', 'invoice-schemes', 'invoice-layouts', 'printers', 'types-of-service', 'subscription']) ? 'active menu-open' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-50 dark:hover:tw-bg-white/5">
            <a href="#" class="tw-flex tw-items-center tw-justify-between tw-px-3 tw-py-2.5 tw-text-gray-700 dark:tw-text-gray-300 tw-font-medium tw-text-sm tw-rounded-lg group">
                <div class="tw-flex tw-items-center">
                    <i class="fa fa-cogs tw-w-5 tw-h-5 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                    <span>@lang('business.settings')</span>
                </div>
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right tw-transition-transform tw-duration-200 group-[.menu-open]:tw-rotate-[-90deg]"></i>
                </span>
            </a>
            <ul class="treeview-menu tw-bg-gray-50 dark:tw-bg-dark-bg/50 tw-rounded-lg tw-px-2 tw-py-1 tw-mt-1 tw-mx-2">
                @can('business_settings.access')
                <li class="{{ request()->segment(1) == 'business' && request()->segment(2) == 'settings' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\BusinessController::class, 'getBusinessSettings'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-cogs tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('business.business_settings')</span>
                    </a>
                </li>
                <li class="{{ request()->segment(1) == 'business-location' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\BusinessLocationController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-map-marker tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('business.business_locations')</span>
                    </a>
                </li>
                @endcan
                @can('invoice_settings.access')
                <li class="{{ in_array(request()->segment(1), ['invoice-schemes', 'invoice-layouts']) ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\InvoiceSchemeController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-file tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('invoice.invoice_settings')</span>
                    </a>
                </li>
                @endcan
                @can('barcode_settings.access')
                <li class="{{ request()->segment(1) == 'barcodes' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\BarcodeController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-barcode tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('barcode.barcode_settings')</span>
                    </a>
                </li>
                @endcan
                @can('access_printers')
                <li class="{{ request()->segment(1) == 'printers' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\PrinterController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-print tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('printer.receipt_printers')</span>
                    </a>
                </li>
                @endcan
                @if(auth()->user()->can('tax_rate.view') || auth()->user()->can('tax_rate.create'))
                <li class="{{ request()->segment(1) == 'tax-rates' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\TaxRateController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-percent tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('tax_rate.tax_rates')</span>
                    </a>
                </li>
                @endif
                @if(in_array('tables', $enabled_modules) && auth()->user()->can('access_tables'))
                <li class="{{ request()->segment(1) == 'modules' && request()->segment(2) == 'tables' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\Restaurant\TableController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-table tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('restaurant.tables')</span>
                    </a>
                </li>
                @endif
                @if(in_array('modifiers', $enabled_modules) && (auth()->user()->can('product.view') || auth()->user()->can('product.create')))
                <li class="{{ request()->segment(1) == 'modules' && request()->segment(2) == 'modifiers' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\Restaurant\ModifierSetsController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-cubes tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('restaurant.modifiers')</span>
                    </a>
                </li>
                @endif
                @if(in_array('types_of_service', $enabled_modules) && auth()->user()->can('access_types_of_service'))
                <li class="{{ request()->segment(1) == 'types-of-service' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\TypesOfServiceController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-user-circle tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.types_of_service')</span>
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        @can('backup')
        <li class="{{ request()->segment(1) == 'backup' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
            <a href="{{action([\App\Http\Controllers\BackUpController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                <i class="fa fa-hdd-o tw-w-5 tw-h-5 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                <span>@lang('lang_v1.backup')</span>
            </a>
        </li>
        @endcan

        @can('manage_modules')
        <li class="{{ request()->segment(1) == 'manage-modules' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
            <a href="{{action([\App\Http\Controllers\Install\ModulesController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                <i class="fa fa-plug tw-w-5 tw-h-5 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                <span>@lang('lang_v1.modules')</span>
            </a>
        </li>
        @endcan
    </ul>
    </div>

    <!-- User Profile Section (Bottom) -->
    <div class="tw-p-4 tw-mt-auto tw-border-t tw-border-gray-100/50 dark:tw-border-white/5">
        <a href="{{ action([\App\Http\Controllers\UserController::class, 'getProfile']) }}" class="tw-flex tw-items-center tw-gap-3 tw-p-3 tw-rounded-2xl tw-bg-gray-50 dark:tw-bg-white/5 hover:tw-bg-primary-50 dark:hover:tw-bg-white/10 tw-transition-all tw-duration-300 tw-group">
            <div class="tw-relative">
                <div class="tw-flex tw-items-center tw-justify-center tw-w-10 tw-h-10 tw-rounded-full tw-bg-gradient-to-br tw-from-primary-400 tw-to-primary-600 tw-text-white tw-font-bold tw-text-sm tw-shadow-md">
                    {{ strtoupper(substr(auth()->user()->first_name, 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name, 0, 1)) }}
                </div>
                <span class="tw-absolute tw-bottom-0 tw-right-0 tw-w-2.5 tw-h-2.5 tw-bg-green-500 tw-border-2 tw-border-white dark:tw-border-dark-surface tw-rounded-full"></span>
            </div>
            <div class="tw-flex tw-flex-col tw-overflow-hidden">
                <span class="tw-text-sm tw-font-bold tw-text-gray-900 dark:tw-text-white tw-truncate group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors">
                    {{ auth()->user()->first_name }}
                </span>
                <span class="tw-text-xs tw-text-gray-500 dark:tw-text-gray-400 tw-truncate">
                    View Profile
                </span>
            </div>
            <i class="fa fa-chevron-right tw-ml-auto tw-text-xs tw-text-gray-400 group-hover:tw-text-primary-500 tw-transition-transform group-hover:tw-translate-x-1"></i>
        </a>
    </div>

</aside>
