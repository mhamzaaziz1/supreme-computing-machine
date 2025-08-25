<div class="col-xs-12">
    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">{{ __('Business Advance Analytics') }}</h3>
        </div>
        <div class="box-body">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#home_dashboard_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-dashboard"></i> @lang('Home Dashboard')</a>
                    </li>
                    <li>
                        <a href="#sales_overview_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-line-chart"></i> @lang('Sales Overview')</a>
                    </li>
                    <li>
                        <a href="#revenue_analysis_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-money"></i> @lang('Revenue Analysis')</a>
                    </li>
                    <li>
                        <a href="#profit_margins_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-percent"></i> @lang('Profit Margins')</a>
                    </li>
                    <li>
                        <a href="#inventory_performance_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-cubes"></i> @lang('Inventory Performance')</a>
                    </li>
                    <li>
                        <a href="#customer_insights_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-users"></i> @lang('Customer Insights')</a>
                    </li>
                    <li>
                        <a href="#product_performance_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-tags"></i> @lang('Product Performance')</a>
                    </li>
                    <li>
                        <a href="#expense_analysis_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-credit-card"></i> @lang('Expense Analysis')</a>
                    </li>
                    <li>
                        <a href="#cash_flow_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-exchange"></i> @lang('Cash Flow')</a>
                    </li>
                    <li>
                        <a href="#seasonal_trends_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-calendar"></i> @lang('Seasonal Trends')</a>
                    </li>
                    <li>
                        <a href="#business_growth_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-line-chart"></i> @lang('Business Growth')</a>
                    </li>
                    <li>
                        <a href="#sales_channels_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-random"></i> @lang('Sales Channels')</a>
                    </li>
                    <li>
                        <a href="#employee_performance_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-user"></i> @lang('Employee Performance')</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <!-- Home Dashboard Tab -->
                    <div class="tab-pane active" id="home_dashboard_tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Home Dashboard @show_tooltip('This dashboard shows key metrics and charts from the home dashboard.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <!-- First Row of Cards with Modern Styling -->
                                        <div class="tw-grid tw-grid-cols-1 tw-gap-4 tw-mt-6 sm:tw-grid-cols-2 xl:tw-grid-cols-4 sm:tw-gap-5">
                                            <!-- Total Sell Card -->
                                            <div class="tw-transition-all tw-duration-200 tw-bg-white tw-shadow-sm hover:tw-shadow-md tw-rounded-xl tw-ring-1 tw-ring-gray-200">
                                                <div class="tw-p-4 sm:tw-p-5">
                                                    <div class="tw-flex tw-items-center tw-gap-4">
                                                        <div class="tw-inline-flex tw-items-center tw-justify-center tw-w-10 tw-h-10 tw-rounded-full sm:tw-w-12 sm:tw-h-12 tw-shrink-0 tw-bg-sky-100 tw-text-sky-500">
                                                            <svg aria-hidden="true" class="tw-w-6 tw-h-6" xmlns="http://www.w3.org/2000/svg"
                                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                <path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                                                <path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                                                <path d="M17 17h-11v-14h-2" />
                                                                <path d="M6 5l14 1l-1 7h-13" />
                                                            </svg>
                                                        </div>

                                                        <div class="tw-flex-1 tw-min-w-0">
                                                            <p class="tw-text-sm tw-font-medium tw-text-gray-500 tw-truncate tw-whitespace-nowrap">
                                                                {{ __('home.total_sell') }}
                                                            </p>
                                                            <p class="total_sell tw-mt-0.5 tw-text-gray-900 tw-text-xl tw-truncate tw-font-semibold tw-tracking-tight tw-font-mono">
                                                                {{ @num_format($data['total_sell'] ?? 0) }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Net Card -->
                                            <div class="tw-transition-all tw-duration-200 tw-bg-white tw-shadow-sm hover:tw-shadow-md tw-rounded-xl hover:tw--translate-y-0.5 tw-ring-1 tw-ring-gray-200">
                                                <div class="tw-p-4 sm:tw-p-5">
                                                    <div class="tw-flex tw-items-center tw-gap-4">
                                                        <div class="tw-inline-flex tw-items-center tw-justify-center tw-w-10 tw-h-10 tw-text-green-500 tw-bg-green-100 tw-rounded-full sm:tw-w-12 sm:tw-h-12 tw-shrink-0">
                                                            <svg aria-hidden="true" class="tw-w-6 tw-h-6" xmlns="http://www.w3.org/2000/svg"
                                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                <path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2">
                                                                </path>
                                                                <path d="M14.8 8a2 2 0 0 0 -1.8 -1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1 -1.8 -1">
                                                                </path>
                                                                <path d="M12 6v10"></path>
                                                            </svg>
                                                        </div>

                                                        <div class="tw-flex-1 tw-min-w-0">
                                                            <p class="tw-text-sm tw-font-medium tw-text-gray-500 tw-truncate tw-whitespace-nowrap">
                                                                {{ __('lang_v1.net') }} @show_tooltip(__('lang_v1.net_home_tooltip'))
                                                            </p>
                                                            <p class="net tw-mt-0.5 tw-text-gray-900 tw-text-xl tw-truncate tw-font-semibold tw-tracking-tight tw-font-mono">
                                                                {{ @num_format($data['net'] ?? 0) }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Invoice Due Card -->
                                            <div class="tw-transition-all tw-duration-200 tw-bg-white tw-shadow-sm hover:tw-shadow-md tw-rounded-xl hover:tw--translate-y-0.5 tw-ring-1 tw-ring-gray-200">
                                                <div class="tw-p-4 sm:tw-p-5">
                                                    <div class="tw-flex tw-items-center tw-gap-4">
                                                        <div class="tw-inline-flex tw-items-center tw-justify-center tw-w-10 tw-h-10 tw-text-yellow-500 tw-bg-yellow-100 tw-rounded-full sm:tw-w-12 sm:tw-h-12 shrink-0">
                                                            <svg aria-hidden="true" class="tw-w-6 tw-h-6" xmlns="http://www.w3.org/2000/svg"
                                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                                                <path d="M9 7l1 0" />
                                                                <path d="M9 13l6 0" />
                                                                <path d="M13 17l2 0" />
                                                            </svg>
                                                        </div>

                                                        <div class="tw-flex-1 tw-min-w-0">
                                                            <p class="tw-text-sm tw-font-medium tw-text-gray-500 tw-truncate tw-whitespace-nowrap">
                                                                {{ __('home.invoice_due') }}
                                                            </p>
                                                            <p class="invoice_due tw-mt-0.5 tw-text-gray-900 tw-text-xl tw-truncate tw-font-semibold tw-tracking-tight tw-font-mono">
                                                                {{ @num_format($data['invoice_due'] ?? 0) }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Total Sell Return Card -->
                                            <div class="tw-transition-all tw-duration-200 tw-bg-white tw-shadow-sm hover:tw-shadow-md tw-rounded-xl hover:tw--translate-y-0.5 tw-ring-1 tw-ring-gray-200">
                                                <div class="tw-p-4 sm:tw-p-5">
                                                    <div class="tw-flex tw-items-center tw-gap-4">
                                                        <div class="tw-inline-flex tw-items-center tw-justify-center tw-w-10 tw-h-10 tw-text-red-500 tw-bg-red-100 tw-rounded-full sm:tw-w-12 sm:tw-h-12 shrink-0">
                                                            <svg aria-hidden="true" class="tw-w-6 tw-h-6" xmlns="http://www.w3.org/2000/svg"
                                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                <path d="M21 7l-18 0" />
                                                                <path d="M18 10l3 -3l-3 -3" />
                                                                <path d="M6 20l-3 -3l3 -3" />
                                                                <path d="M3 17l18 0" />
                                                            </svg>
                                                        </div>

                                                        <div class="tw-flex-1 tw-min-w-0">
                                                            <p class="tw-text-sm tw-font-medium tw-text-gray-500 tw-truncate tw-whitespace-nowrap">
                                                                {{ __('lang_v1.total_sell_return') }}
                                                                <i class="fa fa-info-circle text-info hover-q no-print" aria-hidden="true" data-container="body"
                                                                data-toggle="popover" data-placement="auto bottom" id="total_srp"
                                                                data-value="{{ __('lang_v1.total_sell_return') }}-{{ __('lang_v1.total_sell_return_paid') }}"
                                                                data-content="" data-html="true" data-trigger="hover"></i>
                                                            </p>
                                                            <p class="total_sell_return tw-mt-0.5 tw-text-gray-900 tw-text-xl tw-truncate tw-font-semibold tw-tracking-tight tw-font-mono">
                                                                {{ @num_format($data['total_sell_return'] ?? 0) }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Second Row of Cards with Modern Styling -->
                                        <div class="tw-grid tw-grid-cols-1 tw-gap-4 tw-mt-6 sm:tw-grid-cols-2 xl:tw-grid-cols-4 sm:tw-gap-5">
                                            <!-- Total Purchase Card -->
                                            <div class="tw-transition-all tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl hover:tw-shadow-md hover:tw--translate-y-0.5 tw-ring-1 tw-ring-gray-200">
                                                <div class="tw-p-4 sm:tw-p-5">
                                                    <div class="tw-flex tw-items-center tw-gap-4">
                                                        <div class="tw-inline-flex tw-items-center tw-justify-center tw-w-10 tw-h-10 tw-rounded-full sm:tw-w-12 sm:tw-h-12 shrink-0 bg-sky-100 tw-text-sky-500">
                                                            <svg aria-hidden="true" class="tw-w-6 tw-h-6"
                                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                                stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                <path d="M12 3v12"></path>
                                                                <path d="M16 11l-4 4l-4 -4"></path>
                                                                <path d="M3 12a9 9 0 0 0 18 0"></path>
                                                            </svg>
                                                        </div>

                                                        <div class="tw-flex-1 tw-min-w-0">
                                                            <p class="tw-text-sm tw-font-medium tw-text-gray-500 tw-truncate tw-whitespace-nowrap">
                                                                {{ __('home.total_purchase') }}
                                                            </p>
                                                            <p class="total_purchase tw-mt-0.5 tw-text-gray-900 tw-text-xl tw-truncate tw-font-semibold tw-tracking-tight tw-font-mono">
                                                                {{ @num_format($data['total_purchase'] ?? 0) }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Purchase Due Card -->
                                            <div class="tw-transition-all tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl hover:tw-shadow-md hover:tw--translate-y-0.5 tw-ring-1 tw-ring-gray-200">
                                                <div class="tw-p-4 sm:tw-p-5">
                                                    <div class="tw-flex tw-items-center tw-gap-4">
                                                        <div class="tw-inline-flex tw-items-center tw-justify-center tw-w-10 tw-h-10 tw-text-yellow-500 tw-bg-yellow-100 tw-rounded-full sm:tw-w-12 sm:tw-h-12 shrink-0">
                                                            <svg aria-hidden="true" class="tw-w-6 tw-h-6"
                                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                                stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                <path d="M12 9v4" />
                                                                <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" />
                                                                <path d="M12 16h.01" />
                                                            </svg>
                                                        </div>

                                                        <div>
                                                            <p class="tw-text-sm tw-font-medium tw-text-gray-500">
                                                                {{ __('home.purchase_due') }}
                                                            </p>
                                                            <p class="purchase_due tw-mt-0.5 tw-text-gray-900 tw-text-xl tw-truncate tw-font-semibold tw-tracking-tight tw-font-mono">
                                                                {{ @num_format($data['purchase_due'] ?? 0) }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Total Purchase Return Card -->
                                            <div class="tw-transition-all tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl hover:tw-shadow-md hover:tw--translate-y-0.5 tw-ring-1 tw-ring-gray-200">
                                                <div class="tw-p-4 sm:tw-p-5">
                                                    <div class="tw-flex tw-items-center tw-gap-4">
                                                        <div class="tw-inline-flex tw-items-center tw-justify-center tw-w-10 tw-h-10 tw-text-red-500 tw-bg-red-100 tw-rounded-full sm:tw-w-12 sm:tw-h-12 shrink-0">
                                                            <svg aria-hidden="true" class="tw-w-6 tw-h-6"
                                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                                stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                <path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2" />
                                                                <path d="M15 14v-2a2 2 0 0 0 -2 -2h-4l2 -2m0 4l-2 -2" />
                                                            </svg>
                                                        </div>

                                                        <div class="tw-flex-1 tw-min-w-0">
                                                            <p class="tw-text-sm tw-font-medium tw-text-gray-500 tw-truncate tw-whitespace-nowrap">
                                                                {{ __('lang_v1.total_purchase_return') }}
                                                                <i class="fa fa-info-circle text-info hover-q no-print" aria-hidden="true" data-container="body"
                                                                data-toggle="popover" data-placement="auto bottom" id="total_prp"
                                                                data-value="{{ __('lang_v1.total_purchase_return') }}-{{ __('lang_v1.total_purchase_return_paid') }}"
                                                                data-content="" data-html="true" data-trigger="hover"></i>
                                                            </p>
                                                            <p class="total_purchase_return tw-mt-0.5 tw-text-gray-900 tw-text-xl tw-truncate tw-font-semibold tw-tracking-tight tw-font-mono">
                                                                {{ @num_format($data['total_purchase_return'] ?? 0) }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Expense Card -->
                                            <div class="tw-transition-all tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl hover:tw-shadow-md hover:tw--translate-y-0.5 tw-ring-1 tw-ring-gray-200">
                                                <div class="tw-p-4 sm:tw-p-5">
                                                    <div class="tw-flex tw-items-center tw-gap-4">
                                                        <div class="tw-inline-flex tw-items-center tw-justify-center tw-w-10 tw-h-10 tw-text-red-500 tw-bg-red-100 tw-rounded-full sm:tw-w-12 sm:tw-h-12 shrink-0">
                                                            <svg aria-hidden="true" class="tw-w-6 tw-h-6"
                                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                                stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                <path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2">
                                                                </path>
                                                                <path d="M14.8 8a2 2 0 0 0 -1.8 -1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1 -1.8 -1">
                                                                </path>
                                                                <path d="M12 6v10"></path>
                                                            </svg>
                                                        </div>

                                                        <div class="tw-flex-1 tw-min-w-0">
                                                            <p class="tw-text-sm tw-font-medium tw-text-gray-500 tw-truncate tw-whitespace-nowrap">
                                                                {{ __('lang_v1.expense') }}
                                                            </p>
                                                            <p class="total_expense tw-mt-0.5 tw-text-gray-900 tw-text-xl tw-truncate tw-font-semibold tw-tracking-tight tw-font-mono">
                                                                {{ @num_format($data['total_expense'] ?? 0) }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Charts with Modern Styling -->
                                        <div class="tw-transition-all lg:tw-col-span-2 xl:tw-col-span-2 tw-duration-200 tw-bg-white tw-shadow-sm tw-rounded-xl tw-ring-1 hover:tw-shadow-md hover:tw--translate-y-0.5 tw-ring-gray-200 tw-mt-6">
                                            <div class="tw-p-4 sm:tw-p-5">
                                                <div class="tw-flex tw-items-center tw-gap-2.5">
                                                    <div class="tw-border-2 tw-flex tw-items-center tw-justify-center tw-rounded-full tw-w-10 tw-h-10">
                                                        <svg aria-hidden="true" class="tw-size-5 tw-text-sky-500 tw-shrink-0"
                                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                            <path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                                            <path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                                            <path d="M17 17h-11v-14h-2"></path>
                                                            <path d="M6 5l14 1l-1 7h-13"></path>
                                                        </svg>
                                                    </div>

                                                    <h3 class="tw-font-bold tw-text-base lg:tw-text-xl">
                                                        {{ __('home.sells_last_30_days') }}
                                                    </h3>
                                                </div>
                                                <div class="tw-mt-5">
                                                    <div class="tw-grid tw-w-full tw-h-100 tw-border tw-border-gray-200 tw-border-dashed tw-rounded-xl tw-bg-gray-50">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="sells_last_30_days_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="box box-primary">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">{{ __('home.sells_current_fy') }}</h3>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="sells_current_fy_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tables -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="box box-primary">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">{{ __('lang_v1.sales_payment_dues') }}</h3>
                                                    </div>
                                                    <div class="box-body">
                                                        <table class="table table-bordered table-striped" id="sales_payment_dues_table">
                                                            <thead>
                                                                <tr>
                                                                    <th>@lang('contact.customer')</th>
                                                                    <th>@lang('sale.invoice_no')</th>
                                                                    <th>@lang('home.due_amount')</th>
                                                                    <th>@lang('messages.action')</th>
                                                                </tr>
                                                            </thead>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="box box-primary">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">{{ __('lang_v1.purchase_payment_dues') }}</h3>
                                                    </div>
                                                    <div class="box-body">
                                                        <table class="table table-bordered table-striped" id="purchase_payment_dues_table">
                                                            <thead>
                                                                <tr>
                                                                    <th>@lang('purchase.supplier')</th>
                                                                    <th>@lang('purchase.ref_no')</th>
                                                                    <th>@lang('home.due_amount')</th>
                                                                    <th>@lang('messages.action')</th>
                                                                </tr>
                                                            </thead>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="box box-primary">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">{{ __('home.product_stock_alert') }}</h3>
                                                    </div>
                                                    <div class="box-body">
                                                        <table class="table table-bordered table-striped" id="stock_alert_table">
                                                            <thead>
                                                                <tr>
                                                                    <th>@lang('sale.product')</th>
                                                                    <th>@lang('business.location')</th>
                                                                    <th>@lang('report.current_stock')</th>
                                                                </tr>
                                                            </thead>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sales Overview Tab -->
                    <div class="tab-pane" id="sales_overview_tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Sales Overview @show_tooltip('This analysis shows your overall sales performance. It helps identify trends, patterns, and potential areas for improvement.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <!-- Sales Summary Widgets -->
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-aqua"><i class="fa fa-shopping-cart"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Total Sales</span>
                                                        <span class="info-box-number">{{ @num_format($data['total_sales'] ?? 0) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-green"><i class="fa fa-money"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Total Revenue</span>
                                                        <span class="info-box-number">{{ @num_format($data['total_revenue'] ?? 0) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-yellow"><i class="fa fa-line-chart"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Average Order Value</span>
                                                        <span class="info-box-number">{{ @num_format($data['average_order_value'] ?? 0) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-red"><i class="fa fa-users"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Total Customers</span>
                                                        <span class="info-box-number">{{ @num_format($data['total_customers'] ?? 0) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <!-- Sales Trend Chart -->
                                            <div class="col-md-8">
                                                <div class="chart-container" style="position: relative; height:300px;">
                                                    <canvas id="sales_trend_chart"></canvas>
                                                </div>
                                            </div>

                                            <!-- Sales Distribution Pie Chart -->
                                            <div class="col-md-4">
                                                <div class="chart-container" style="position: relative; height:300px;">
                                                    <canvas id="sales_distribution_chart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Analysis Tab -->
                    <div class="tab-pane" id="revenue_analysis_tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Revenue Analysis @show_tooltip('This analysis breaks down your revenue streams and helps identify your most profitable business areas.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <!-- Revenue Widgets -->
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-aqua"><i class="fa fa-dollar"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Gross Revenue</span>
                                                        <span class="info-box-number">{{ @num_format($data['gross_revenue'] ?? 0) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-green"><i class="fa fa-dollar"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Net Revenue</span>
                                                        <span class="info-box-number">{{ @num_format($data['net_revenue'] ?? 0) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-yellow"><i class="fa fa-percent"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Revenue Growth</span>
                                                        <span class="info-box-number">{{ @num_format($data['revenue_growth'] ?? 0) }}%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-red"><i class="fa fa-calendar"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Monthly Recurring Revenue</span>
                                                        <span class="info-box-number">{{ @num_format($data['monthly_recurring_revenue'] ?? 0) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <!-- Revenue by Category Chart -->
                                            <div class="col-md-6">
                                                <div class="chart-container" style="position: relative; height:300px;">
                                                    <canvas id="revenue_by_category_chart"></canvas>
                                                </div>
                                            </div>

                                            <!-- Revenue by Location Chart -->
                                            <div class="col-md-6">
                                                <div class="chart-container" style="position: relative; height:300px;">
                                                    <canvas id="revenue_by_location_chart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profit Margins Tab -->
                    <div class="tab-pane" id="profit_margins_tab">
                        <!-- Content for Profit Margins tab -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Profit Margins @show_tooltip('This analysis shows your profit margins across different products, categories, and time periods.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <!-- Profit Margin Widgets -->
                                        <div class="row">
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-aqua"><i class="fa fa-percent"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Gross Profit Margin</span>
                                                        <span class="info-box-number">{{ @num_format($data['gross_profit_margin'] ?? 0) }}%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-green"><i class="fa fa-percent"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Net Profit Margin</span>
                                                        <span class="info-box-number">{{ @num_format($data['net_profit_margin'] ?? 0) }}%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-yellow"><i class="fa fa-dollar"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Total Profit</span>
                                                        <span class="info-box-number">{{ @num_format($data['total_profit'] ?? 0) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-red"><i class="fa fa-line-chart"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Profit Growth</span>
                                                        <span class="info-box-number">{{ @num_format($data['profit_growth'] ?? 0) }}%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Profit Margin Charts -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="chart-container" style="position: relative; height:300px;">
                                                    <canvas id="profit_margin_trend_chart"></canvas>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="chart-container" style="position: relative; height:300px;">
                                                    <canvas id="profit_by_category_chart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Inventory Performance Tab -->
                    <div class="tab-pane" id="inventory_performance_tab">
                        <!-- Content for Inventory Performance tab -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Inventory Performance @show_tooltip('This analysis shows how efficiently your inventory is managed and utilized.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <!-- Inventory Widgets -->
                                        <div class="row">
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-aqua"><i class="fa fa-cubes"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Inventory Value</span>
                                                        <span class="info-box-number">{{ @num_format($data['inventory_value'] ?? 0) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-green"><i class="fa fa-refresh"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Inventory Turnover</span>
                                                        <span class="info-box-number">{{ @num_format($data['inventory_turnover'] ?? 0) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-yellow"><i class="fa fa-calendar"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Days in Inventory</span>
                                                        <span class="info-box-number">{{ @num_format($data['days_in_inventory'] ?? 0) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-red"><i class="fa fa-warning"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Stock-outs</span>
                                                        <span class="info-box-number">{{ @num_format($data['stockouts'] ?? 0) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Inventory Charts -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="chart-container" style="position: relative; height:300px;">
                                                    <canvas id="inventory_turnover_chart"></canvas>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="chart-container" style="position: relative; height:300px;">
                                                    <canvas id="inventory_value_chart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Insights Tab -->
                    <div class="tab-pane" id="customer_insights_tab">
                        <!-- Content for Customer Insights tab -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Customer Insights @show_tooltip('This analysis provides insights into your customer base, including acquisition, retention, and behavior patterns.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <!-- Customer Insights Widgets -->
                                        <div class="row">
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-aqua"><i class="fa fa-users"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Total Customers</span>
                                                        <span class="info-box-number">{{ @num_format($data['total_customers'] ?? 0) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-green"><i class="fa fa-user-plus"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">New Customers</span>
                                                        <span class="info-box-number">{{ @num_format($data['new_customers'] ?? 50) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-yellow"><i class="fa fa-refresh"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Repeat Purchase Rate</span>
                                                        <span class="info-box-number">{{ @num_format($data['repeat_purchase_rate'] ?? 35) }}%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-red"><i class="fa fa-dollar"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Avg. Customer Value</span>
                                                        <span class="info-box-number">{{ @num_format($data['avg_customer_value'] ?? 500) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Customer Insights Charts -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="chart-container" style="position: relative; height:300px;">
                                                    <canvas id="customer_acquisition_chart"></canvas>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="chart-container" style="position: relative; height:300px;">
                                                    <canvas id="customer_retention_chart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Performance Tab -->
                    <div class="tab-pane" id="product_performance_tab">
                        <!-- Content for Product Performance tab -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Product Performance @show_tooltip('This analysis shows how your products are performing in terms of sales, profitability, and popularity.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <!-- Product Performance Widgets -->
                                        <div class="row">
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-aqua"><i class="fa fa-tags"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Total Products</span>
                                                        <span class="info-box-number">{{ @num_format($data['total_products'] ?? 100) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-green"><i class="fa fa-star"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Best Selling Product</span>
                                                        <span class="info-box-number">{{ $data['best_selling_product'] ?? 'Product X' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-yellow"><i class="fa fa-percent"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Avg. Product Margin</span>
                                                        <span class="info-box-number">{{ @num_format($data['avg_product_margin'] ?? 25) }}%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-red"><i class="fa fa-shopping-cart"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Products Sold</span>
                                                        <span class="info-box-number">{{ @num_format($data['products_sold'] ?? 500) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Product Performance Charts -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="chart-container" style="position: relative; height:300px;">
                                                    <canvas id="top_selling_products_chart"></canvas>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="chart-container" style="position: relative; height:300px;">
                                                    <canvas id="product_category_performance_chart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Expense Analysis Tab -->
                    <div class="tab-pane" id="expense_analysis_tab">
                        <!-- Content for Expense Analysis tab -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Expense Analysis @show_tooltip('This analysis helps you understand your business expenses and identify areas for cost optimization.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <!-- Expense Analysis Widgets -->
                                        <div class="row">
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-aqua"><i class="fa fa-credit-card"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Total Expenses</span>
                                                        <span class="info-box-number">{{ @num_format($data['total_expenses'] ?? 5000) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-green"><i class="fa fa-percent"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Expense Ratio</span>
                                                        <span class="info-box-number">{{ @num_format($data['expense_ratio'] ?? 25) }}%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-yellow"><i class="fa fa-line-chart"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Expense Growth</span>
                                                        <span class="info-box-number">{{ @num_format($data['expense_growth'] ?? 5) }}%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-red"><i class="fa fa-calendar"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Monthly Avg. Expense</span>
                                                        <span class="info-box-number">{{ @num_format($data['monthly_avg_expense'] ?? 1200) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Expense Analysis Charts -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="chart-container" style="position: relative; height:300px;">
                                                    <canvas id="expense_trend_chart"></canvas>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="chart-container" style="position: relative; height:300px;">
                                                    <canvas id="expense_by_category_chart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cash Flow Tab -->
                    <div class="tab-pane" id="cash_flow_tab">
                        <!-- Content for Cash Flow tab -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Cash Flow @show_tooltip('This analysis shows your cash inflows and outflows, helping you understand your business liquidity and financial health.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <!-- Cash Flow Widgets -->
                                        <div class="row">
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-aqua"><i class="fa fa-money"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Net Cash Flow</span>
                                                        <span class="info-box-number">{{ @num_format($data['net_cash_flow'] ?? 3000) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-green"><i class="fa fa-arrow-up"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Cash Inflow</span>
                                                        <span class="info-box-number">{{ @num_format($data['cash_inflow'] ?? 8000) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-red"><i class="fa fa-arrow-down"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Cash Outflow</span>
                                                        <span class="info-box-number">{{ @num_format($data['cash_outflow'] ?? 5000) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-yellow"><i class="fa fa-calendar"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Cash Runway</span>
                                                        <span class="info-box-number">{{ @num_format($data['cash_runway'] ?? 6) }} months</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Cash Flow Charts -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="chart-container" style="position: relative; height:300px;">
                                                    <canvas id="cash_flow_trend_chart"></canvas>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="chart-container" style="position: relative; height:300px;">
                                                    <canvas id="cash_flow_breakdown_chart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seasonal Trends Tab -->
                    <div class="tab-pane" id="seasonal_trends_tab">
                        <!-- Content for Seasonal Trends tab -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Seasonal Trends @show_tooltip('This analysis helps you identify seasonal patterns in your business to better plan for peak and slow periods.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <!-- Seasonal Trends Widgets -->
                                        <div class="row">
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-aqua"><i class="fa fa-calendar-check-o"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Peak Season</span>
                                                        <span class="info-box-number">{{ $data['peak_season'] ?? 'Nov-Dec' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-green"><i class="fa fa-line-chart"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Peak Season Growth</span>
                                                        <span class="info-box-number">{{ @num_format($data['peak_season_growth'] ?? 35) }}%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-yellow"><i class="fa fa-calendar-minus-o"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Slow Season</span>
                                                        <span class="info-box-number">{{ $data['slow_season'] ?? 'Jan-Feb' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-red"><i class="fa fa-percent"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Seasonal Variance</span>
                                                        <span class="info-box-number">{{ @num_format($data['seasonal_variance'] ?? 45) }}%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Seasonal Trends Charts -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="chart-container" style="position: relative; height:300px;">
                                                    <canvas id="monthly_sales_trend_chart"></canvas>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="chart-container" style="position: relative; height:300px;">
                                                    <canvas id="quarterly_comparison_chart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Business Growth Tab -->
                    <div class="tab-pane" id="business_growth_tab">
                        <!-- Content for Business Growth tab -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Business Growth @show_tooltip('This analysis shows your business growth over time and helps identify trends and opportunities for expansion.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <!-- Business Growth Widgets -->
                                        <div class="row">
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-aqua"><i class="fa fa-line-chart"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Revenue Growth</span>
                                                        <span class="info-box-number">{{ @num_format($data['revenue_growth'] ?? 25) }}%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-green"><i class="fa fa-users"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Customer Growth</span>
                                                        <span class="info-box-number">{{ @num_format($data['customer_growth'] ?? 15) }}%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-yellow"><i class="fa fa-shopping-cart"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Order Growth</span>
                                                        <span class="info-box-number">{{ @num_format($data['order_growth'] ?? 20) }}%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-red"><i class="fa fa-dollar"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Profit Growth</span>
                                                        <span class="info-box-number">{{ @num_format($data['profit_growth'] ?? 18) }}%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Business Growth Charts -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="chart-container" style="position: relative; height:300px;">
                                                    <canvas id="year_over_year_growth_chart"></canvas>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="chart-container" style="position: relative; height:300px;">
                                                    <canvas id="growth_metrics_chart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sales Channels Tab -->
                    <div class="tab-pane" id="sales_channels_tab">
                        <!-- Content for Sales Channels tab -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Sales Channels @show_tooltip('This analysis shows the performance of different sales channels and helps optimize your multi-channel strategy.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <!-- Sales Channels Widgets -->
                                        <div class="row">
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-aqua"><i class="fa fa-shopping-bag"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Total Channels</span>
                                                        <span class="info-box-number">{{ @num_format($data['total_channels'] ?? 5) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-green"><i class="fa fa-star"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Top Channel</span>
                                                        <span class="info-box-number">{{ $data['top_channel'] ?? 'In-Store' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-yellow"><i class="fa fa-percent"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Online Sales %</span>
                                                        <span class="info-box-number">{{ @num_format($data['online_sales_percentage'] ?? 35) }}%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-red"><i class="fa fa-exchange"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Channel Conversion</span>
                                                        <span class="info-box-number">{{ @num_format($data['channel_conversion'] ?? 3.2) }}%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Sales Channels Charts -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="chart-container" style="position: relative; height:300px;">
                                                    <canvas id="sales_by_channel_chart"></canvas>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="chart-container" style="position: relative; height:300px;">
                                                    <canvas id="channel_performance_chart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Employee Performance Tab -->
                    <div class="tab-pane" id="employee_performance_tab">
                        <!-- Content for Employee Performance tab -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Employee Performance @show_tooltip('This analysis helps you evaluate employee performance and identify top performers and areas for improvement.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <!-- Employee Performance Widgets -->
                                        <div class="row">
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-aqua"><i class="fa fa-users"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Total Employees</span>
                                                        <span class="info-box-number">{{ @num_format($data['total_employees'] ?? 25) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-green"><i class="fa fa-trophy"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Top Performer</span>
                                                        <span class="info-box-number">{{ $data['top_performer'] ?? 'John Doe' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-yellow"><i class="fa fa-dollar"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Avg. Sales per Employee</span>
                                                        <span class="info-box-number">{{ @num_format($data['avg_sales_per_employee'] ?? 12500) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-red"><i class="fa fa-clock-o"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Avg. Transaction Time</span>
                                                        <span class="info-box-number">{{ @num_format($data['avg_transaction_time'] ?? 8.5) }} min</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Employee Performance Charts -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="chart-container" style="position: relative; height:300px;">
                                                    <canvas id="employee_sales_chart"></canvas>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="chart-container" style="position: relative; height:300px;">
                                                    <canvas id="employee_metrics_chart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        // Initialize charts if data is available
        if (typeof Chart !== 'undefined') {
            // Sales Trend Chart
            if (document.getElementById('sales_trend_chart')) {
                var salesTrendCtx = document.getElementById('sales_trend_chart').getContext('2d');
                var salesTrendChart = new Chart(salesTrendCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($data['sales_trend_labels'] ?? []) !!},
                        datasets: [{
                            label: 'Sales',
                            data: {!! json_encode($data['sales_trend_data'] ?? []) !!},
                            backgroundColor: 'rgba(60, 141, 188, 0.2)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 1,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // Sales Distribution Chart
            if (document.getElementById('sales_distribution_chart')) {
                var salesDistributionCtx = document.getElementById('sales_distribution_chart').getContext('2d');
                var salesDistributionChart = new Chart(salesDistributionCtx, {
                    type: 'pie',
                    data: {
                        labels: {!! json_encode($data['sales_distribution_labels'] ?? []) !!},
                        datasets: [{
                            data: {!! json_encode($data['sales_distribution_data'] ?? []) !!},
                            backgroundColor: [
                                'rgba(60, 141, 188, 0.8)',
                                'rgba(0, 166, 90, 0.8)',
                                'rgba(243, 156, 18, 0.8)',
                                'rgba(221, 75, 57, 0.8)',
                                'rgba(0, 192, 239, 0.8)'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }

            // Revenue by Category Chart
            if (document.getElementById('revenue_by_category_chart')) {
                var revenueByCategoryCtx = document.getElementById('revenue_by_category_chart').getContext('2d');
                var revenueByCategoryChart = new Chart(revenueByCategoryCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($data['sales_distribution_labels'] ?? []) !!},
                        datasets: [{
                            label: 'Revenue by Category',
                            data: {!! json_encode($data['sales_distribution_data'] ?? []) !!},
                            backgroundColor: 'rgba(0, 166, 90, 0.8)',
                            borderColor: 'rgba(0, 166, 90, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // Revenue by Location Chart
            if (document.getElementById('revenue_by_location_chart')) {
                var revenueByLocationCtx = document.getElementById('revenue_by_location_chart').getContext('2d');
                var revenueByLocationChart = new Chart(revenueByLocationCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Location 1', 'Location 2', 'Location 3'], // Replace with actual data
                        datasets: [{
                            label: 'Revenue by Location',
                            data: [5000, 3000, 2000], // Replace with actual data
                            backgroundColor: 'rgba(243, 156, 18, 0.8)',
                            borderColor: 'rgba(243, 156, 18, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // Profit Margin Trend Chart
            if (document.getElementById('profit_margin_trend_chart')) {
                var profitMarginTrendCtx = document.getElementById('profit_margin_trend_chart').getContext('2d');
                var profitMarginTrendChart = new Chart(profitMarginTrendCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($data['sales_trend_labels'] ?? []) !!},
                        datasets: [{
                            label: 'Gross Profit Margin',
                            data: [30, 32, 28, 35, 40, 38], // Replace with actual data
                            backgroundColor: 'rgba(60, 141, 188, 0.2)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 1,
                            fill: false
                        }, {
                            label: 'Net Profit Margin',
                            data: [20, 22, 18, 25, 30, 28], // Replace with actual data
                            backgroundColor: 'rgba(0, 166, 90, 0.2)',
                            borderColor: 'rgba(0, 166, 90, 1)',
                            borderWidth: 1,
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // Profit by Category Chart
            if (document.getElementById('profit_by_category_chart')) {
                var profitByCategoryCtx = document.getElementById('profit_by_category_chart').getContext('2d');
                var profitByCategoryChart = new Chart(profitByCategoryCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($data['sales_distribution_labels'] ?? []) !!},
                        datasets: [{
                            label: 'Profit by Category',
                            data: [1000, 1500, 800, 1200, 900], // Replace with actual data
                            backgroundColor: 'rgba(221, 75, 57, 0.8)',
                            borderColor: 'rgba(221, 75, 57, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // Inventory Turnover Chart
            if (document.getElementById('inventory_turnover_chart')) {
                var inventoryTurnoverCtx = document.getElementById('inventory_turnover_chart').getContext('2d');
                var inventoryTurnoverChart = new Chart(inventoryTurnoverCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'], // Replace with actual data
                        datasets: [{
                            label: 'Inventory Turnover',
                            data: [4, 3.8, 4.2, 4.5, 4.3, 4.1], // Replace with actual data
                            backgroundColor: 'rgba(0, 192, 239, 0.2)',
                            borderColor: 'rgba(0, 192, 239, 1)',
                            borderWidth: 1,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // Inventory Value Chart
            if (document.getElementById('inventory_value_chart')) {
                var inventoryValueCtx = document.getElementById('inventory_value_chart').getContext('2d');
                var inventoryValueChart = new Chart(inventoryValueCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($data['sales_distribution_labels'] ?? []) !!},
                        datasets: [{
                            label: 'Inventory Value',
                            data: [10000, 8000, 12000, 9000, 11000], // Replace with actual data
                            backgroundColor: 'rgba(243, 156, 18, 0.8)',
                            borderColor: 'rgba(243, 156, 18, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // Customer Acquisition Chart
            if (document.getElementById('customer_acquisition_chart')) {
                var customerAcquisitionCtx = document.getElementById('customer_acquisition_chart').getContext('2d');
                var customerAcquisitionChart = new Chart(customerAcquisitionCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($data['sales_trend_labels'] ?? []) !!}, // Using sales trend labels as a placeholder
                        datasets: [{
                            label: 'New Customers',
                            data: [15, 20, 25, 18, 30, 22], // Replace with actual data
                            backgroundColor: 'rgba(60, 141, 188, 0.2)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 1,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // Customer Retention Chart
            if (document.getElementById('customer_retention_chart')) {
                var customerRetentionCtx = document.getElementById('customer_retention_chart').getContext('2d');
                var customerRetentionChart = new Chart(customerRetentionCtx, {
                    type: 'bar',
                    data: {
                        labels: ['1 Month', '3 Months', '6 Months', '1 Year'], // Replace with actual data
                        datasets: [{
                            label: 'Customer Retention Rate',
                            data: [90, 75, 60, 45], // Replace with actual data (percentages)
                            backgroundColor: 'rgba(0, 166, 90, 0.8)',
                            borderColor: 'rgba(0, 166, 90, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Top Selling Products Chart
            if (document.getElementById('top_selling_products_chart')) {
                var topSellingProductsCtx = document.getElementById('top_selling_products_chart').getContext('2d');
                var topSellingProductsChart = new Chart(topSellingProductsCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Product A', 'Product B', 'Product C', 'Product D', 'Product E'], // Replace with actual data
                        datasets: [{
                            label: 'Units Sold',
                            data: [150, 120, 100, 80, 50], // Replace with actual data
                            backgroundColor: 'rgba(60, 141, 188, 0.8)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Top Selling Products'
                            }
                        }
                    }
                });
            }

            // Product Category Performance Chart
            if (document.getElementById('product_category_performance_chart')) {
                var productCategoryPerformanceCtx = document.getElementById('product_category_performance_chart').getContext('2d');
                var productCategoryPerformanceChart = new Chart(productCategoryPerformanceCtx, {
                    type: 'pie',
                    data: {
                        labels: {!! json_encode($data['sales_distribution_labels'] ?? ['Category A', 'Category B', 'Category C', 'Category D', 'Category E']) !!},
                        datasets: [{
                            data: {!! json_encode($data['sales_distribution_data'] ?? [30, 25, 20, 15, 10]) !!},
                            backgroundColor: [
                                'rgba(60, 141, 188, 0.8)',
                                'rgba(0, 166, 90, 0.8)',
                                'rgba(243, 156, 18, 0.8)',
                                'rgba(221, 75, 57, 0.8)',
                                'rgba(0, 192, 239, 0.8)'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Sales by Product Category'
                            }
                        }
                    }
                });
            }

            // Expense Trend Chart
            if (document.getElementById('expense_trend_chart')) {
                var expenseTrendCtx = document.getElementById('expense_trend_chart').getContext('2d');
                var expenseTrendChart = new Chart(expenseTrendCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($data['sales_trend_labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']) !!}, // Using sales trend labels as a placeholder
                        datasets: [{
                            label: 'Monthly Expenses',
                            data: [1200, 1350, 1100, 1500, 1300, 1450], // Replace with actual data
                            backgroundColor: 'rgba(221, 75, 57, 0.2)',
                            borderColor: 'rgba(221, 75, 57, 1)',
                            borderWidth: 1,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Monthly Expense Trend'
                            }
                        }
                    }
                });
            }

            // Expense by Category Chart
            if (document.getElementById('expense_by_category_chart')) {
                var expenseByCategoryCtx = document.getElementById('expense_by_category_chart').getContext('2d');
                var expenseByCategoryChart = new Chart(expenseByCategoryCtx, {
                    type: 'pie',
                    data: {
                        labels: ['Rent', 'Utilities', 'Salaries', 'Marketing', 'Supplies', 'Other'], // Replace with actual data
                        datasets: [{
                            data: [30, 15, 35, 10, 5, 5], // Replace with actual data (percentages)
                            backgroundColor: [
                                'rgba(60, 141, 188, 0.8)',
                                'rgba(0, 166, 90, 0.8)',
                                'rgba(243, 156, 18, 0.8)',
                                'rgba(221, 75, 57, 0.8)',
                                'rgba(0, 192, 239, 0.8)',
                                'rgba(153, 102, 255, 0.8)'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Expenses by Category'
                            }
                        }
                    }
                });
            }

            // Cash Flow Trend Chart
            if (document.getElementById('cash_flow_trend_chart')) {
                var cashFlowTrendCtx = document.getElementById('cash_flow_trend_chart').getContext('2d');
                var cashFlowTrendChart = new Chart(cashFlowTrendCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($data['sales_trend_labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']) !!}, // Using sales trend labels as a placeholder
                        datasets: [{
                            label: 'Cash Inflow',
                            data: [8000, 7500, 9000, 8500, 10000, 9500], // Replace with actual data
                            backgroundColor: 'rgba(0, 166, 90, 0.2)',
                            borderColor: 'rgba(0, 166, 90, 1)',
                            borderWidth: 1,
                            fill: false
                        }, {
                            label: 'Cash Outflow',
                            data: [5000, 5200, 4800, 6000, 5500, 6500], // Replace with actual data
                            backgroundColor: 'rgba(221, 75, 57, 0.2)',
                            borderColor: 'rgba(221, 75, 57, 1)',
                            borderWidth: 1,
                            fill: false
                        }, {
                            label: 'Net Cash Flow',
                            data: [3000, 2300, 4200, 2500, 4500, 3000], // Replace with actual data
                            backgroundColor: 'rgba(60, 141, 188, 0.2)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 2,
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Cash Flow Trend'
                            }
                        }
                    }
                });
            }

            // Cash Flow Breakdown Chart
            if (document.getElementById('cash_flow_breakdown_chart')) {
                var cashFlowBreakdownCtx = document.getElementById('cash_flow_breakdown_chart').getContext('2d');
                var cashFlowBreakdownChart = new Chart(cashFlowBreakdownCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Sales', 'Investments', 'Loans', 'Expenses', 'Purchases', 'Taxes'], // Replace with actual data
                        datasets: [{
                            label: 'Cash Flow Components',
                            data: [10000, 2000, 1000, -5000, -3000, -2000], // Replace with actual data (positive for inflows, negative for outflows)
                            backgroundColor: [
                                'rgba(0, 166, 90, 0.8)',
                                'rgba(0, 166, 90, 0.8)',
                                'rgba(0, 166, 90, 0.8)',
                                'rgba(221, 75, 57, 0.8)',
                                'rgba(221, 75, 57, 0.8)',
                                'rgba(221, 75, 57, 0.8)'
                            ],
                            borderColor: [
                                'rgba(0, 166, 90, 1)',
                                'rgba(0, 166, 90, 1)',
                                'rgba(0, 166, 90, 1)',
                                'rgba(221, 75, 57, 1)',
                                'rgba(221, 75, 57, 1)',
                                'rgba(221, 75, 57, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Cash Flow Breakdown'
                            }
                        }
                    }
                });
            }

            // Monthly Sales Trend Chart
            if (document.getElementById('monthly_sales_trend_chart')) {
                var monthlySalesTrendCtx = document.getElementById('monthly_sales_trend_chart').getContext('2d');
                var monthlySalesTrendChart = new Chart(monthlySalesTrendCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], // Months of the year
                        datasets: [{
                            label: 'Current Year',
                            data: [5000, 4500, 6000, 7500, 8000, 9000, 8500, 9500, 10000, 11000, 13000, 15000], // Replace with actual data
                            backgroundColor: 'rgba(60, 141, 188, 0.2)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 2,
                            fill: true
                        }, {
                            label: 'Previous Year',
                            data: [4000, 3800, 5000, 6500, 7000, 8000, 7500, 8500, 9000, 10000, 11000, 13000], // Replace with actual data
                            backgroundColor: 'rgba(210, 214, 222, 0.2)',
                            borderColor: 'rgba(210, 214, 222, 1)',
                            borderWidth: 2,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Monthly Sales Trend'
                            }
                        }
                    }
                });
            }

            // Quarterly Comparison Chart
            if (document.getElementById('quarterly_comparison_chart')) {
                var quarterlyComparisonCtx = document.getElementById('quarterly_comparison_chart').getContext('2d');
                var quarterlyComparisonChart = new Chart(quarterlyComparisonCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Q1', 'Q2', 'Q3', 'Q4'], // Quarters of the year
                        datasets: [{
                            label: 'Sales',
                            data: [15000, 25000, 28000, 39000], // Replace with actual data
                            backgroundColor: 'rgba(60, 141, 188, 0.8)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 1
                        }, {
                            label: 'Profit',
                            data: [5000, 8000, 9000, 12000], // Replace with actual data
                            backgroundColor: 'rgba(0, 166, 90, 0.8)',
                            borderColor: 'rgba(0, 166, 90, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Quarterly Performance Comparison'
                            }
                        }
                    }
                });
            }

            // Year Over Year Growth Chart
            if (document.getElementById('year_over_year_growth_chart')) {
                var yearOverYearGrowthCtx = document.getElementById('year_over_year_growth_chart').getContext('2d');
                var yearOverYearGrowthChart = new Chart(yearOverYearGrowthCtx, {
                    type: 'line',
                    data: {
                        labels: ['2019', '2020', '2021', '2022', '2023'], // Replace with actual years
                        datasets: [{
                            label: 'Revenue',
                            data: [100000, 120000, 150000, 180000, 225000], // Replace with actual data
                            backgroundColor: 'rgba(60, 141, 188, 0.2)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 2,
                            fill: false
                        }, {
                            label: 'Profit',
                            data: [30000, 38000, 45000, 55000, 70000], // Replace with actual data
                            backgroundColor: 'rgba(0, 166, 90, 0.2)',
                            borderColor: 'rgba(0, 166, 90, 1)',
                            borderWidth: 2,
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Year Over Year Growth'
                            }
                        }
                    }
                });
            }

            // Growth Metrics Chart
            if (document.getElementById('growth_metrics_chart')) {
                var growthMetricsCtx = document.getElementById('growth_metrics_chart').getContext('2d');
                var growthMetricsChart = new Chart(growthMetricsCtx, {
                    type: 'radar',
                    data: {
                        labels: ['Revenue', 'Customers', 'Orders', 'Products', 'Profit', 'Market Share'], // Growth metrics
                        datasets: [{
                            label: 'Current Year Growth (%)',
                            data: [25, 15, 20, 10, 18, 12], // Replace with actual data (percentages)
                            backgroundColor: 'rgba(60, 141, 188, 0.2)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgba(60, 141, 188, 1)'
                        }, {
                            label: 'Previous Year Growth (%)',
                            data: [20, 12, 15, 8, 14, 10], // Replace with actual data (percentages)
                            backgroundColor: 'rgba(210, 214, 222, 0.2)',
                            borderColor: 'rgba(210, 214, 222, 1)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgba(210, 214, 222, 1)'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            r: {
                                beginAtZero: true,
                                max: 30 // Adjust based on your data
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Growth Metrics Comparison'
                            }
                        }
                    }
                });
            }

            // Sales by Channel Chart
            if (document.getElementById('sales_by_channel_chart')) {
                var salesByChannelCtx = document.getElementById('sales_by_channel_chart').getContext('2d');
                var salesByChannelChart = new Chart(salesByChannelCtx, {
                    type: 'pie',
                    data: {
                        labels: ['In-Store', 'Online', 'Wholesale', 'Marketplace', 'Mobile App'], // Replace with actual data
                        datasets: [{
                            data: [45, 25, 15, 10, 5], // Replace with actual data (percentages)
                            backgroundColor: [
                                'rgba(60, 141, 188, 0.8)',
                                'rgba(0, 166, 90, 0.8)',
                                'rgba(243, 156, 18, 0.8)',
                                'rgba(221, 75, 57, 0.8)',
                                'rgba(0, 192, 239, 0.8)'
                            ],
                            borderColor: [
                                'rgba(60, 141, 188, 1)',
                                'rgba(0, 166, 90, 1)',
                                'rgba(243, 156, 18, 1)',
                                'rgba(221, 75, 57, 1)',
                                'rgba(0, 192, 239, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Sales Distribution by Channel'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.label || '';
                                        var value = context.raw || 0;
                                        return label + ': ' + value + '%';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Channel Performance Chart
            if (document.getElementById('channel_performance_chart')) {
                var channelPerformanceCtx = document.getElementById('channel_performance_chart').getContext('2d');
                var channelPerformanceChart = new Chart(channelPerformanceCtx, {
                    type: 'bar',
                    data: {
                        labels: ['In-Store', 'Online', 'Wholesale', 'Marketplace', 'Mobile App'], // Replace with actual data
                        datasets: [{
                            label: 'Average Order Value',
                            data: [120, 85, 200, 75, 95], // Replace with actual data
                            backgroundColor: 'rgba(60, 141, 188, 0.8)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 1,
                            yAxisID: 'y'
                        }, {
                            label: 'Conversion Rate (%)',
                            data: [4.5, 2.8, 3.2, 2.1, 3.5], // Replace with actual data
                            backgroundColor: 'rgba(0, 166, 90, 0.8)',
                            borderColor: 'rgba(0, 166, 90, 1)',
                            borderWidth: 1,
                            type: 'line',
                            yAxisID: 'y1'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'Average Order Value'
                                }
                            },
                            y1: {
                                beginAtZero: true,
                                position: 'right',
                                title: {
                                    display: true,
                                    text: 'Conversion Rate (%)'
                                },
                                grid: {
                                    drawOnChartArea: false
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Channel Performance Metrics'
                            }
                        }
                    }
                });
            }

            // Employee Sales Chart
            if (document.getElementById('employee_sales_chart')) {
                var employeeSalesCtx = document.getElementById('employee_sales_chart').getContext('2d');
                var employeeSalesChart = new Chart(employeeSalesCtx, {
                    type: 'bar',
                    data: {
                        labels: ['John Doe', 'Jane Smith', 'Mike Johnson', 'Sarah Williams', 'David Brown'], // Replace with actual data
                        datasets: [{
                            label: 'Sales Amount',
                            data: [18500, 16200, 14800, 13500, 12000], // Replace with actual data
                            backgroundColor: 'rgba(60, 141, 188, 0.8)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y', // Horizontal bar chart
                        scales: {
                            x: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Sales Amount'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Top Performing Employees by Sales'
                            }
                        }
                    }
                });
            }

            // Employee Metrics Chart
            if (document.getElementById('employee_metrics_chart')) {
                var employeeMetricsCtx = document.getElementById('employee_metrics_chart').getContext('2d');
                var employeeMetricsChart = new Chart(employeeMetricsCtx, {
                    type: 'radar',
                    data: {
                        labels: ['Sales', 'Customer Satisfaction', 'Attendance', 'Product Knowledge', 'Team Collaboration', 'Upselling'], // Performance metrics
                        datasets: [{
                            label: 'John Doe',
                            data: [90, 85, 95, 80, 75, 85], // Replace with actual data (percentages)
                            backgroundColor: 'rgba(60, 141, 188, 0.2)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgba(60, 141, 188, 1)'
                        }, {
                            label: 'Jane Smith',
                            data: [80, 95, 85, 90, 85, 75], // Replace with actual data (percentages)
                            backgroundColor: 'rgba(0, 166, 90, 0.2)',
                            borderColor: 'rgba(0, 166, 90, 1)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgba(0, 166, 90, 1)'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            r: {
                                beginAtZero: true,
                                max: 100, // Percentage scale
                                ticks: {
                                    stepSize: 20
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Employee Performance Metrics'
                            }
                        }
                    }
                });
            }
        }
    });
</script>
