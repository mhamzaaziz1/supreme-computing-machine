<!-- Left side column. contains the logo and sidebar -->
<aside class="side-bar tw-relative tw-hidden lg:tw-flex tw-flex-col tw-shrink-0 tw-w-72 tw-h-[calc(100vh-2rem)] tw-m-4 tw-rounded-2xl tw-bg-white dark:tw-bg-[#1c1f2e] tw-shadow-xl tw-transition-all tw-duration-300 tw-overflow-hidden tw-z-30">

    <!-- Window Controls & Logo Area -->
    <div class="tw-flex tw-flex-col tw-px-6 tw-pt-5 tw-pb-4 tw-shrink-0">
        <!-- Window Controls -->
        <div class="tw-flex tw-gap-2 tw-mb-4">
            <div class="tw-w-3 tw-h-3 tw-rounded-full tw-bg-[#ff5f56]"></div>
            <div class="tw-w-3 tw-h-3 tw-rounded-full tw-bg-[#ffbd2e]"></div>
            <div class="tw-w-3 tw-h-3 tw-rounded-full tw-bg-[#27c93f]"></div>
        </div>

        <!-- Brand / Logo -->
        <a href="{{route('home')}}" class="tw-flex tw-items-center tw-gap-3 tw-mb-4">
            @if(Session::has('business.logo') && !empty(Session::get('business.logo')))
                <img src="{{ asset('uploads/business_logos/' . Session::get('business.logo')) }}" alt="{{ Session::get('business.name') }}" class="tw-w-8 tw-h-8 tw-object-contain tw-rounded-md tw-bg-white">
            @else
                <div class="tw-flex tw-items-center tw-justify-center tw-w-8 tw-h-8">
                    <!-- Sleek logo placeholder -->
                    <div class="tw-flex tw-gap-1">
                        <div class="tw-w-2 tw-h-5 tw-rounded-full tw-bg-[#1a73e8]"></div>
                        <div class="tw-w-2 tw-h-4 tw-rounded-full tw-bg-[#ffbd2e] tw-mt-1"></div>
                        <div class="tw-w-2 tw-h-6 tw-rounded-full tw-bg-[#27c93f] tw-mt-[-4px]"></div>
                    </div>
                </div>
            @endif
            <h1 class="tw-text-lg tw-font-bold tw-text-gray-900 dark:tw-text-white tw-tracking-tight tw-truncate">
                {{ Session::get('business.name') }}
            </h1>
        </a>
    </div>


    @php
        $enabled_modules = !empty(session('business.enabled_modules')) ? session('business.enabled_modules') : [];
        $common_settings = !empty(session('business.common_settings')) ? session('business.common_settings') : [];
        $pos_settings = !empty(session('business.pos_settings')) ? json_decode(session('business.pos_settings'), true) : [];
        $is_admin = auth()->user()->hasRole('Admin#' . session('business.id')) ? true : false;
    @endphp

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
            width: 24px;
            height: 24px;
            margin-right: 12px;
            background-color: transparent !important;
            color: #9ca3af; /* Tailwind gray-400 */
            font-size: 1.1rem;
            transition: all 0.2s ease;
        }

        /* 2. Active State (The Item Row) */
        .sidebar-menu > li.active > a {
            background-color: #1a73e8 !important; /* Bright Blue */
            color: #ffffff !important;
            border-radius: 0.5rem !important; /* Rounded-lg */
            box-shadow: 0 4px 12px rgba(26, 115, 232, 0.3) !important;
            font-weight: 600 !important;
        }
        
        /* 3. Active Icon */
        .sidebar-menu > li.active > a > i, 
        .sidebar-menu > li.active > a > svg {
            color: #ffffff !important;
        }

        /* Hover Effects (Inactive Items) */
        .sidebar-menu > li:not(.active) > a:hover {
            background-color: #f3f4f6; /* gray-100 */
            border-radius: 0.5rem;
            color: #374151 !important; /* gray-700 */
        }
        .sidebar-menu > li:not(.active) > a:hover > i {
            color: #6b7280 !important; /* gray-500 */
        }

        .dark .sidebar-menu > li:not(.active) > a:hover {
            background-color: #282c3f !important;
            color: #f3f4f6 !important;
        }
        .dark .sidebar-menu > li:not(.active) > a:hover > i {
            color: #d1d5db !important;
        }

        /* General Item Spacing */
        .sidebar-menu > li > a {
            margin-bottom: 4px;
            padding: 10px 16px;
            color: #6b7280; /* gray-500 */
            font-weight: 500;
        }
        
        .dark .sidebar-menu > li > a {
            color: #9ca3af;
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


        <!-- Advanced Dashboard -->
        <li class="{{ request()->segment(2) == 'business-advance-analytics' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/5">
            <a href="{{action([\App\Http\Controllers\ReportController::class, 'getBusinessAdvanceAnalytics'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2.5 tw-text-gray-700 dark:tw-text-gray-300 tw-font-medium tw-text-sm tw-rounded-lg group">
                <i class="fa fa-chart-line tw-w-5 tw-h-5 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i> 
                <span>@lang('Advanced Dashboard')</span>
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
                <li class="{{ request()->segment(1) == 'users' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ManageUserController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-user tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('user.users')</span>
                    </a>
                </li>
                @endcan
                @can('roles.view')
                <li class="{{ request()->segment(1) == 'roles' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\RoleController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-briefcase tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('user.roles')</span>
                    </a>
                </li>
                @endcan
                @can('user.create')
                <li class="{{ request()->segment(1) == 'sales-commission-agents' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\SalesCommissionAgentController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-handshake tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.sales_commission_agents')</span>
                    </a>
                </li>
                @endcan
                @can('user.view')
                <li class="{{ request()->segment(1) == 'user-locations' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\UserLocationController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-map-marker-alt tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.active_users_location')</span>
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endif

        <!-- Contacts -->
        @if(auth()->user()->can('supplier.view') || auth()->user()->can('customer.view') || auth()->user()->can('supplier.view_own') || auth()->user()->can('customer.view_own'))
        <li class="treeview {{ in_array(request()->segment(1), ['contacts', 'customer-group', 'route-followups']) ? 'active menu-open' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-50 dark:hover:tw-bg-white/5">
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
                <li class="{{ request()->segment(1) == 'customer-group' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\CustomerGroupController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-users tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.customer_groups')</span>
                    </a>
                </li>
                @endif
                @if(auth()->user()->can('supplier.create') || auth()->user()->can('customer.create'))
                <li class="{{ request()->segment(1) == 'contacts' && request()->segment(2) == 'import' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ContactController::class, 'getImportContacts'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-download tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.import_contacts')</span>
                    </a>
                </li>
                @endif
                @if(!empty(config('services.google_maps.api_key')))
                <li class="{{ request()->segment(1) == 'contacts' && request()->segment(2) == 'map' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ContactController::class, 'contactMap'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-map-marker-alt tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.map')</span>
                    </a>
                </li>
                @endif
                @if(auth()->user()->can('customer.view') || auth()->user()->can('customer.view_own'))
                <li class="{{ request()->segment(1) == 'route-followups' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\RouteFollowupController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-route tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.route_followups')</span>
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
        <li class="treeview {{ in_array(request()->segment(1), ['products', 'brands', 'units', 'taxonomies', 'variation-templates', 'selling-price-group', 'warranties', 'labels', 'import-products', 'import-opening-stock', 'update-product-price']) ? 'active menu-open' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-50 dark:hover:tw-bg-white/5">
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
                <li class="{{ request()->segment(1) == 'update-product-price' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\SellingPriceGroupController::class, 'updateProductPrice'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-edit tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.update_product_price')</span>
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
                <li class="{{ request()->segment(1) == 'import-products' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ImportProductsController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-download tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('product.import_products')</span>
                    </a>
                </li>
                @endcan
                @can('product.opening_stock')
                <li class="{{ request()->segment(1) == 'import-opening-stock' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ImportOpeningStockController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-boxes tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.import_opening_stock')</span>
                    </a>
                </li>
                @endcan
                @can('product.create')
                <li class="{{ request()->segment(1) == 'selling-price-group' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\SellingPriceGroupController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-tag tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.selling_price_group')</span>
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
                <li class="{{ request()->segment(1) == 'warranties' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\WarrantyController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-shield-alt tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.warranties')</span>
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
                @if(!empty($common_settings['enable_purchase_requisition']) && (auth()->user()->can('purchase_requisition.view_all') || auth()->user()->can('purchase_requisition.view_own')))
                <li class="{{ request()->segment(1) == 'purchase-requisition' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\PurchaseRequisitionController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-file-alt tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.purchase_requisition')</span>
                    </a>
                </li>
                @endif
                @if(!empty($common_settings['enable_purchase_order']) && (auth()->user()->can('purchase_order.view_all') || auth()->user()->can('purchase_order.view_own')))
                <li class="{{ request()->segment(1) == 'purchase-order' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\PurchaseOrderController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-list-alt tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.purchase_order')</span>
                    </a>
                </li>
                @endif
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
        @if($is_admin || auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'access_sell_return', 'direct_sell.view', 'direct_sell.update', 'access_own_sell_return']))
        <li class="treeview {{ in_array(request()->segment(1), ['sells', 'pos', 'sell-return', 'shipments', 'discount', 'subscriptions', 'import-sales', 'sales-order']) ? 'active menu-open' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-50 dark:hover:tw-bg-white/5">
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
                @if(!empty($pos_settings['enable_sales_order']) && ($is_admin || auth()->user()->hasAnyPermission(['so.view_own', 'so.view_all', 'so.create'])))
                <li class="{{ request()->segment(1) == 'sales-order' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\SalesOrderController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-file-invoice tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.sales_order')</span>
                    </a>
                </li>
                @endif
                @if($is_admin || auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping']))
                <li class="{{ request()->segment(1) == 'sells' && request()->segment(2) == null ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\SellController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-list tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.all_sales')</span>
                    </a>
                </li>
                @endif
                @if(in_array('add_sale', $enabled_modules) && auth()->user()->can('direct_sell.access'))
                <li class="{{ request()->segment(1) == 'sells' && request()->segment(2) == 'create' && empty(request()->get('status')) ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\SellController::class, 'create'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-plus-circle tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('sale.add_sale')</span>
                    </a>
                </li>
                @endif
                @can('sell.create')
                @if(in_array('pos_sale', $enabled_modules))
                @can('sell.view')
                <li class="{{ request()->segment(1) == 'pos' && request()->segment(2) == null ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\SellPosController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-list-alt tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('sale.list_pos')</span>
                    </a>
                </li>
                @endcan
                <li class="{{ request()->segment(1) == 'pos' && request()->segment(2) == 'create' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\SellPosController::class, 'create'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-cash-register tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('sale.pos_sale')</span>
                    </a>
                </li>
                @endif
                @endcan
                @if(in_array('add_sale', $enabled_modules) && auth()->user()->can('direct_sell.access'))
                <li class="{{ request()->get('status') == 'draft' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\SellController::class, 'create'], ['status' => 'draft'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-file tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.add_draft')</span>
                    </a>
                </li>
                @endif
                @if(in_array('add_sale', $enabled_modules) && ($is_admin || auth()->user()->hasAnyPermission(['draft.view_all', 'draft.view_own'])))
                <li class="{{ request()->segment(1) == 'sells' && request()->segment(2) == 'drafts' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\SellController::class, 'getDrafts'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-pen-square tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.list_drafts')</span>
                    </a>
                </li>
                @endif
                @if(in_array('add_sale', $enabled_modules) && auth()->user()->can('direct_sell.access'))
                <li class="{{ request()->get('status') == 'quotation' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\SellController::class, 'create'], ['status' => 'quotation'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-file-signature tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.add_quotation')</span>
                    </a>
                </li>
                @endif
                @if(in_array('add_sale', $enabled_modules) && ($is_admin || auth()->user()->hasAnyPermission(['quotation.view_all', 'quotation.view_own'])))
                <li class="{{ request()->segment(1) == 'sells' && request()->segment(2) == 'quotations' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\SellController::class, 'getQuotations'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-clipboard-list tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.list_quotations')</span>
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
                @if($is_admin || auth()->user()->hasAnyPermission(['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping']))
                <li class="{{ request()->segment(1) == 'shipments' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\SellController::class, 'shipments'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-truck tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.shipments')</span>
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
                @if(in_array('subscription', $enabled_modules) && auth()->user()->can('direct_sell.access'))
                <li class="{{ request()->segment(1) == 'subscriptions' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\SellPosController::class, 'listSubscriptions'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-sync tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.subscriptions')</span>
                    </a>
                </li>
                @endif
                @if(auth()->user()->can('sell.create'))
                <li class="{{ request()->segment(1) == 'import-sales' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ImportSalesController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-download tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.import_sales')</span>
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
                <li class="{{ request()->segment(1) == 'account' && request()->segment(2) == 'payment-account-report' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\AccountReportsController::class, 'paymentAccountReport'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-file-invoice-dollar tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('account.payment_account_report')</span>
                    </a>
                </li>
            </ul>
        </li>
        @endif
        
        <!-- Reports -->
        @if(auth()->user()->can('purchase_n_sell_report.view') || auth()->user()->can('contacts_report.view') || auth()->user()->can('stock_report.view') || auth()->user()->can('tax_report.view') || auth()->user()->can('trending_product_report.view') || auth()->user()->can('sales_representative.view') || auth()->user()->can('register_report.view') || auth()->user()->can('expense_report.view'))
        <li class="treeview {{ in_array(request()->segment(1), ['reports', 'supply-chain-analytics']) || request()->segment(2) == 'activity-log' ? 'active menu-open' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-50 dark:hover:tw-bg-white/5">
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
                        <i class="fa fa-file-invoice-dollar tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('report.profit_loss')</span>
                    </a>
                </li>
                @endcan

                @if(config('constants.show_report_606') == true)
                <li class="{{ request()->segment(2) == 'purchase-report' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ReportController::class, 'purchaseReport'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-file-alt tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>Report 606 (@lang('lang_v1.purchase'))</span>
                    </a>
                </li>
                @endif

                @if(config('constants.show_report_607') == true)
                <li class="{{ request()->segment(2) == 'sale-report' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ReportController::class, 'saleReport'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-file-alt tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>Report 607 (@lang('business.sale'))</span>
                    </a>
                </li>
                @endif

                @if((in_array('purchases', $enabled_modules) || in_array('add_sale', $enabled_modules) || in_array('pos_sale', $enabled_modules)) && auth()->user()->can('purchase_n_sell_report.view'))
                <li class="{{ request()->segment(2) == 'purchase-sell' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ReportController::class, 'getPurchaseSell'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-exchange-alt tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('report.purchase_sell_report')</span>
                    </a>
                </li>
                @endif

                @can('tax_report.view')
                <li class="{{ request()->segment(2) == 'tax-report' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ReportController::class, 'getTaxReport'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-percent tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('report.tax_report')</span>
                    </a>
                </li>
                @endcan

                @can('contacts_report.view')
                <li class="{{ request()->segment(2) == 'customer-supplier' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ReportController::class, 'getCustomerSuppliers'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-address-book tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('report.contacts')</span>
                    </a>
                </li>
                <li class="{{ request()->segment(2) == 'customer-group' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ReportController::class, 'getCustomerGroup'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-users tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.customer_groups_report')</span>
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
                @if(session('business.enable_product_expiry') == 1)
                <li class="{{ request()->segment(2) == 'stock-expiry' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ReportController::class, 'getStockExpiryReport'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-calendar-times tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('report.stock_expiry_report')</span>
                    </a>
                </li>
                @endif
                @if(session('business.enable_lot_number') == 1)
                <li class="{{ request()->segment(2) == 'lot-report' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ReportController::class, 'getLotReport'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-barcode tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.lot_report')</span>
                    </a>
                </li>
                @endif
                @if(in_array('stock_adjustment', $enabled_modules))
                <li class="{{ request()->segment(2) == 'stock-adjustment-report' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ReportController::class, 'getStockAdjustmentReport'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-sliders-h tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('report.stock_adjustment_report')</span>
                    </a>
                </li>
                @endif
                @endcan

                @can('trending_product_report.view')
                <li class="{{ request()->segment(2) == 'trending-products' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ReportController::class, 'getTrendingProducts'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-chart-line tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('report.trending_products')</span>
                    </a>
                </li>
                @endcan

                @can('purchase_n_sell_report.view')
                <li class="{{ request()->segment(2) == 'items-report' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ReportController::class, 'itemsReport'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-list-ol tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.items_report')</span>
                    </a>
                </li>
                <li class="{{ request()->segment(2) == 'product-purchase-report' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ReportController::class, 'getproductPurchaseReport'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-arrow-circle-down tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.product_purchase_report')</span>
                    </a>
                </li>
                <li class="{{ request()->segment(2) == 'product-sell-report' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ReportController::class, 'getproductSellReport'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-arrow-circle-up tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.product_sell_report')</span>
                    </a>
                </li>
                <li class="{{ request()->segment(2) == 'purchase-payment-report' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ReportController::class, 'purchasePaymentReport'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-money-check-alt tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.purchase_payment_report')</span>
                    </a>
                </li>
                <li class="{{ request()->segment(2) == 'sell-payment-report' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ReportController::class, 'sellPaymentReport'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-cash-register tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.sell_payment_report')</span>
                    </a>
                </li>
                @endcan

                @if(in_array('expenses', $enabled_modules) && auth()->user()->can('expense_report.view'))
                <li class="{{ request()->segment(2) == 'expense-report' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ReportController::class, 'getExpenseReport'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-minus-circle tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('report.expense_report')</span>
                    </a>
                </li>
                @endif

                @can('register_report.view')
                <li class="{{ request()->segment(2) == 'register-report' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ReportController::class, 'getRegisterReport'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-briefcase tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('report.register_report')</span>
                    </a>
                </li>
                @endcan

                @can('sales_representative.view')
                <li class="{{ request()->segment(2) == 'sales-representative-report' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ReportController::class, 'getSalesRepresentativeReport'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-user-tie tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('report.sales_representative')</span>
                    </a>
                </li>
                @endcan

                @if(auth()->user()->can('purchase_n_sell_report.view') && in_array('tables', $enabled_modules))
                <li class="{{ request()->segment(2) == 'table-report' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ReportController::class, 'getTableReport'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-table tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('restaurant.table_report')</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->can('tax_report.view') && !empty(config('constants.enable_gst_report_india')))
                <li class="{{ request()->segment(2) == 'gst-sales-report' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ReportController::class, 'gstSalesReport'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-file-invoice tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.gst_sales_report')</span>
                    </a>
                </li>
                <li class="{{ request()->segment(2) == 'gst-purchase-report' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ReportController::class, 'gstPurchaseReport'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-file-invoice tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.gst_purchase_report')</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->can('sales_representative.view') && in_array('service_staff', $enabled_modules))
                <li class="{{ request()->segment(2) == 'service-staff-report' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ReportController::class, 'getServiceStaffReport'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-user-clock tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('restaurant.service_staff_report')</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->can('customer.view') || auth()->user()->can('customer.view_own'))
                <li class="{{ request()->segment(2) == 'route-followup-report' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ReportController::class, 'getRouteFollowupReport'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-route tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.route_followup_report')</span>
                    </a>
                </li>
                <li class="{{ request()->segment(2) == 'customer-advance-analytics' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ReportController::class, 'getCustomerAdvanceAnalytics'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-chart-pie tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>Customer Advance Analytics</span>
                    </a>
                </li>
                <li class="{{ request()->segment(2) == 'product-advance-analytics' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ReportController::class, 'getProductAdvanceAnalytics'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-cubes tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>Product Advance Analytics</span>
                    </a>
                </li>
                <li class="{{ request()->segment(2) == 'purchase-advance-analytics' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ReportController::class, 'getPurchaseAdvanceAnalytics'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-shopping-bag tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>Purchase Advance Analytics</span>
                    </a>
                </li>
                <li class="{{ request()->segment(1) == 'supply-chain-analytics' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\SupplyChainAnalyticsController::class, 'index'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-truck-loading tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>Supply Chain Analytics</span>
                    </a>
                </li>
                @endif

                @if($is_admin)
                <li class="{{ request()->segment(2) == 'activity-log' ? 'active' : '' }} tw-rounded-lg tw-transition-colors tw-duration-200 hover:tw-bg-gray-100 dark:hover:tw-bg-white/10 tw-mt-0.5">
                    <a href="{{action([\App\Http\Controllers\ReportController::class, 'activityLog'])}}" class="tw-flex tw-items-center tw-px-3 tw-py-2 tw-text-gray-600 dark:tw-text-gray-400 tw-font-medium tw-text-sm tw-rounded-lg group">
                        <i class="fa fa-history tw-w-4 tw-h-4 tw-mr-3 tw-text-gray-400 group-hover:tw-text-primary-600 dark:group-hover:tw-text-primary-400 tw-transition-colors"></i>
                        <span>@lang('lang_v1.activity_log')</span>
                    </a>
                </li>
                @endif
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
    <div class="tw-px-4 tw-py-4 tw-mt-auto tw-shrink-0">
        <div class="tw-flex tw-items-center tw-gap-3 tw-w-full">
            <div class="tw-flex tw-items-center tw-justify-center tw-w-9 tw-h-9 tw-rounded-full tw-bg-[#00bfa5] tw-text-white tw-font-bold tw-text-sm tw-shrink-0">
                {{ strtoupper(substr(auth()->user()->first_name, 0, 1)) }}
            </div>
            <div class="tw-flex tw-flex-col tw-overflow-hidden tw-flex-1">
                <span class="tw-text-sm tw-font-semibold tw-text-gray-900 dark:tw-text-white tw-truncate">
                    {{ auth()->user()->first_name }}
                </span>
                <span class="tw-text-xs tw-text-gray-400 dark:tw-text-gray-500 tw-truncate">
                    {{ auth()->user()->email ?? 'user@example.com' }}
                </span>
            </div>
            <a href="{{ action([\App\Http\Controllers\Auth\LoginController::class, 'logout']) }}" class="tw-text-gray-400 hover:tw-text-gray-600 dark:hover:tw-text-white tw-transition-colors">
                <i class="fa fa-sign-out-alt tw-text-lg"></i>
            </a>
        </div>
    </div>

</aside>
