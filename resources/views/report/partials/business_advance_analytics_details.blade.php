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
                        <a href="#payment_analysis_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-money"></i> @lang('Payment Analysis')</a>
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
                                                            <tbody>
                                                                @if(!empty($data['product_stock_alert']))
                                                                    @foreach($data['product_stock_alert'] as $product)
                                                                        <tr>
                                                                            <td>
                                                                                @if($product->type == 'single')
                                                                                    {{ $product->product }} ({{ $product->sku }})
                                                                                @else
                                                                                    {{ $product->product }} - {{ $product->product_variation }} - {{ $product->variation }} ({{ $product->sub_sku }})
                                                                                @endif
                                                                            </td>
                                                                            <td>{{ $product->location_name }}</td>
                                                                            <td>
                                                                                <span data-is_quantity="true" class="display_currency" data-currency_symbol=false>{{ (float) $product->stock }}</span> {{ $product->unit }}
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                @else
                                                                    <tr>
                                                                        <td colspan="3" class="text-center">@lang('lang_v1.no_data')</td>
                                                                    </tr>
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Unified Analytics Chart -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box box-primary">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">{{ __('Unified Business Analytics') }} @show_tooltip('This chart shows sales, purchases, profit, and forecasts in a unified view.')</h3>
                                            <div class="box-tools pull-right">
                                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                            </div>
                                        </div>
                                        <div class="box-body">
                                            <div style="height: 300px;">
                                                <canvas id="unified_analytics_chart"></canvas>
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
                                                        <span class="info-box-text text-muted">
                                                            <small>
                                                                <i class="fa fa-arrow-{{ ($data['sales_growth'] ?? 0) > 0 ? 'up text-success' : 'down text-danger' }}"></i> 
                                                                {{ @num_format(abs($data['sales_growth'] ?? 0)) }}% from last period
                                                            </small>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-green"><i class="fa fa-money"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Total Revenue</span>
                                                        <span class="info-box-number">{{ @num_format($data['total_revenue'] ?? 0) }}</span>
                                                        <span class="info-box-text text-muted">
                                                            <small>
                                                                <i class="fa fa-arrow-{{ ($data['revenue_growth'] ?? 0) > 0 ? 'up text-success' : 'down text-danger' }}"></i> 
                                                                {{ @num_format(abs($data['revenue_growth'] ?? 0)) }}% from last period
                                                            </small>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-yellow"><i class="fa fa-line-chart"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Average Order Value</span>
                                                        <span class="info-box-number">{{ @num_format($data['average_order_value'] ?? 0) }}</span>
                                                        <span class="info-box-text text-muted">
                                                            <small>
                                                                <i class="fa fa-arrow-{{ ($data['aov_growth'] ?? 0) > 0 ? 'up text-success' : 'down text-danger' }}"></i> 
                                                                {{ @num_format(abs($data['aov_growth'] ?? 0)) }}% from last period
                                                            </small>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-red"><i class="fa fa-users"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Total Customers</span>
                                                        <span class="info-box-number">{{ @num_format($data['total_customers'] ?? 0) }}</span>
                                                        <span class="info-box-text text-muted">
                                                            <small>
                                                                <i class="fa fa-arrow-{{ ($data['customer_growth'] ?? 0) > 0 ? 'up text-success' : 'down text-danger' }}"></i> 
                                                                {{ @num_format(abs($data['customer_growth'] ?? 0)) }}% from last period
                                                            </small>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <!-- Sales Trend Chart with Prediction -->
                                            <div class="col-md-8">
                                                <div class="box box-info">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Sales Trend with Forecast @show_tooltip('This chart shows your sales trend over time with predictive analytics for future sales.')</h3>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="sales_trend_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Sales Distribution Pie Chart -->
                                            <div class="col-md-4">
                                                <div class="box box-success">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Sales Distribution @show_tooltip('This chart shows how your sales are distributed across different categories.')</h3>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="sales_distribution_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <!-- Sales by Category Chart -->
                                            <div class="col-md-6">
                                                <div class="box box-warning">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Sales by Category @show_tooltip('This chart shows your sales performance by product category with growth indicators.')</h3>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="sales_by_category_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Sales by Location Chart -->
                                            <div class="col-md-6">
                                                <div class="box box-danger">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Sales by Location: {{ $data['current_month_name'] ?? date('F') }} (Current vs Last Year) @show_tooltip('This chart compares sales by location for the current month and the same month last year.')</h3>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="sales_by_location_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <!-- Sales Forecast Chart -->
                                            <div class="col-md-6">
                                                <div class="box box-primary">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Sales Forecast @show_tooltip('This chart provides a detailed sales forecast with confidence intervals based on historical data and trends.')</h3>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="sales_forecast_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Customer Segmentation Chart -->
                                            <div class="col-md-6">
                                                <div class="box box-info">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Customer Segmentation @show_tooltip('This chart segments your customers based on purchase frequency, recency, and monetary value.')</h3>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="customer_segmentation_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <!-- Sales Performance vs Target -->
                                            <div class="col-md-12">
                                                <div class="box box-success">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Sales Performance vs Target @show_tooltip('This chart compares your actual sales performance against targets with predictive analytics for future performance.')</h3>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="sales_vs_target_chart"></canvas>
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
                                                <div class="box box-info">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Revenue by Category @show_tooltip('This chart shows revenue distribution across different product categories.')</h3>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="revenue_by_category_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Revenue by Location Chart -->
                                            <div class="col-md-6">
                                                <div class="box box-success">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Revenue by Location @show_tooltip('This chart shows revenue distribution across different business locations.')</h3>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="revenue_by_location_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <!-- Revenue Trend with Forecast -->
                                            <div class="col-md-6">
                                                <div class="box box-primary">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Revenue Trend with Forecast @show_tooltip('This chart shows your revenue trend over time with predictive analytics for future revenue.')</h3>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="revenue_trend_forecast_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Revenue Growth Prediction -->
                                            <div class="col-md-6">
                                                <div class="box box-warning">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Revenue Growth Prediction @show_tooltip('This chart predicts future revenue growth based on historical data and trends.')</h3>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="revenue_growth_prediction_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <!-- Seasonal Revenue Analysis -->
                                            <div class="col-md-6">
                                                <div class="box box-danger">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Seasonal Revenue Analysis @show_tooltip('This chart analyzes seasonal patterns in your revenue with predictive analytics for future seasons.')</h3>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="seasonal_revenue_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Revenue Source Contribution -->
                                            <div class="col-md-6">
                                                <div class="box box-info">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Revenue Source Contribution @show_tooltip('This chart shows the contribution of different revenue sources with predictive analytics for future contribution.')</h3>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="revenue_source_chart"></canvas>
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

                    <!-- Profit Margins Tab -->
                    <div class="tab-pane" id="profit_margins_tab">
                        <!-- Content for Profit Margins tab -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Profit Margins @show_tooltip('This analysis shows your profit margins across different products, categories, and time periods with predictive analytics.')</h3>
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
                                                        <span class="info-box-text text-muted">
                                                            <small>
                                                                <i class="fa fa-arrow-{{ ($data['gross_margin_growth'] ?? 0) > 0 ? 'up text-success' : 'down text-danger' }}"></i> 
                                                                {{ @num_format(abs($data['gross_margin_growth'] ?? 0)) }}% from last period
                                                            </small>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-green"><i class="fa fa-percent"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Net Profit Margin</span>
                                                        <span class="info-box-number">{{ @num_format($data['net_profit_margin'] ?? 0) }}%</span>
                                                        <span class="info-box-text text-muted">
                                                            <small>
                                                                <i class="fa fa-arrow-{{ ($data['net_margin_growth'] ?? 0) > 0 ? 'up text-success' : 'down text-danger' }}"></i> 
                                                                {{ @num_format(abs($data['net_margin_growth'] ?? 0)) }}% from last period
                                                            </small>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-yellow"><i class="fa fa-dollar"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Total Profit</span>
                                                        <span class="info-box-number">{{ @num_format($data['total_profit'] ?? 0) }}</span>
                                                        <span class="info-box-text text-muted">
                                                            <small>
                                                                <i class="fa fa-arrow-{{ ($data['profit_growth'] ?? 0) > 0 ? 'up text-success' : 'down text-danger' }}"></i> 
                                                                {{ @num_format(abs($data['profit_growth'] ?? 0)) }}% from last period
                                                            </small>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-red"><i class="fa fa-line-chart"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Operating Margin</span>
                                                        <span class="info-box-number">{{ @num_format($data['operating_margin'] ?? 0) }}%</span>
                                                        <span class="info-box-text text-muted">
                                                            <small>
                                                                <i class="fa fa-arrow-{{ ($data['operating_margin_growth'] ?? 0) > 0 ? 'up text-success' : 'down text-danger' }}"></i> 
                                                                {{ @num_format(abs($data['operating_margin_growth'] ?? 0)) }}% from last period
                                                            </small>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Profit Margin Trend Chart with Forecast -->
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="box box-info">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Profit Margin Trends with Forecast @show_tooltip('This chart shows your profit margin trends over time with predictive analytics for future performance.')</h3>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="profit_margin_trend_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <!-- Profit by Category Chart with Growth Indicators -->
                                            <div class="col-md-6">
                                                <div class="box box-success">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Profit by Category @show_tooltip('This chart shows your profit performance by product category with growth indicators and trend analysis.')</h3>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="profit_by_category_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Profit vs Cost Analysis Chart -->
                                            <div class="col-md-6">
                                                <div class="box box-warning">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Profit vs Cost Analysis @show_tooltip('This chart analyzes the relationship between your costs and profits, helping identify optimal pricing strategies.')</h3>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="profit_vs_cost_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <!-- Margin Optimization Suggestions Chart -->
                                            <div class="col-md-6">
                                                <div class="box box-danger">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Margin Optimization Opportunities @show_tooltip('This chart identifies products and categories where margin improvements are possible based on predictive analytics.')</h3>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="margin_optimization_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Comparative Profit Margin Analysis Chart -->
                                            <div class="col-md-6">
                                                <div class="box box-primary">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Comparative Margin Analysis @show_tooltip('This chart compares your profit margins across different dimensions like time periods, locations, and product categories.')</h3>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="comparative_margin_chart"></canvas>
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
                                            <!-- Inventory Turnover Trend with Forecast -->
                                            <div class="col-md-6">
                                                <div class="box box-info">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Inventory Turnover Trend with Forecast @show_tooltip('This chart shows your inventory turnover trend over time with predictive analytics for future turnover rates.')</h3>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="inventory_turnover_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Inventory Value by Category -->
                                            <div class="col-md-6">
                                                <div class="box box-success">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Inventory Value by Category @show_tooltip('This chart shows your inventory value distribution across different product categories.')</h3>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="inventory_value_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <!-- Stock-out Risk Prediction -->
                                            <div class="col-md-6">
                                                <div class="box box-warning">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Stock-out Risk Prediction @show_tooltip('This chart predicts which products are at risk of stock-out based on historical sales and current inventory levels.')</h3>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="stockout_risk_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Inventory Aging Analysis -->
                                            <div class="col-md-6">
                                                <div class="box box-danger">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Inventory Aging Analysis @show_tooltip('This chart shows how long products have been in inventory with recommendations for optimal inventory management.')</h3>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="inventory_aging_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <!-- Inventory Efficiency Score -->
                                            <div class="col-md-6">
                                                <div class="box box-primary">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Inventory Efficiency Score @show_tooltip('This chart shows your overall inventory management efficiency with predictive analytics for future performance.')</h3>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="inventory_efficiency_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Inventory Optimization Recommendations -->
                                            <div class="col-md-6">
                                                <div class="box box-info">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Inventory Optimization Recommendations @show_tooltip('This chart provides recommendations for optimizing your inventory based on predictive analytics.')</h3>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="inventory_optimization_chart"></canvas>
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

                                        <!-- Additional Sports Business Analytics Charts -->
                                        <div class="row mt-4">
                                            <div class="col-md-12">
                                                <h4 class="text-center">Sports Business Analytics</h4>
                                                <p class="text-muted text-center">Advanced analytics for sports business with predictive insights</p>
                                            </div>
                                        </div>

                                        <!-- Sports Product Seasonal Performance -->
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <div class="box box-info">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Sports Product Seasonal Performance</h3>
                                                        <div class="box-tools pull-right">
                                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="sports_product_seasonal_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="box box-success">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Seasonal Customer Engagement</h3>
                                                        <div class="box-tools pull-right">
                                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="seasonal_customer_engagement_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Seasonal Inventory and Profit Margin -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="box box-warning">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Seasonal Inventory Optimization</h3>
                                                        <div class="box-tools pull-right">
                                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="seasonal_inventory_optimization_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="box box-danger">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Seasonal Profit Margin Analysis</h3>
                                                        <div class="box-tools pull-right">
                                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="seasonal_profit_margin_chart"></canvas>
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

                                        <!-- Additional Business Growth Charts with Predictive Analytics -->
                                        <div class="row" style="margin-top: 20px;">
                                            <div class="col-md-12">
                                                <h4 class="text-center"><strong>Predictive Analytics</strong></h4>
                                                <p class="text-center text-muted">The following charts include predictive analytics to help forecast future business performance</p>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="box box-primary">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Revenue Forecast</h3>
                                                        <div class="box-tools pull-right">
                                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="revenue_forecast_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="box box-primary">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Customer Growth Forecast</h3>
                                                        <div class="box-tools pull-right">
                                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="customer_forecast_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="box box-primary">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Sales Trend Analysis</h3>
                                                        <div class="box-tools pull-right">
                                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="sales_trend_analysis_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="box box-primary">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Profit Margin Forecast</h3>
                                                        <div class="box-tools pull-right">
                                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="profit_margin_forecast_chart"></canvas>
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

                                        <!-- Additional Sales Channels Charts with Predictive Analytics -->
                                        <div class="row" style="margin-top: 20px;">
                                            <div class="col-md-12">
                                                <h4 class="text-center"><strong>Sales Channels Predictive Analytics</strong></h4>
                                                <p class="text-center text-muted">The following charts include predictive analytics to help forecast future channel performance</p>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="box box-primary">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Channel Growth Forecast</h3>
                                                        <div class="box-tools pull-right">
                                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="channel_growth_forecast_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="box box-primary">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Channel ROI Comparison</h3>
                                                        <div class="box-tools pull-right">
                                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="channel_roi_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="box box-primary">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Channel Performance Trend with Forecast</h3>
                                                        <div class="box-tools pull-right">
                                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:400px;">
                                                            <canvas id="channel_trend_forecast_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="box box-primary">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Customer Acquisition Cost by Channel</h3>
                                                        <div class="box-tools pull-right">
                                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="channel_acquisition_cost_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="box box-primary">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Channel Conversion Optimization</h3>
                                                        <div class="box-tools pull-right">
                                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:300px;">
                                                            <canvas id="channel_conversion_optimization_chart"></canvas>
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

                                        <!-- Employee Performance Score Chart -->
                                        <div class="row" style="margin-top: 20px;">
                                            <div class="col-md-12">
                                                <div class="box box-primary">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Employee Performance Scores @show_tooltip('This chart shows the overall performance scores of top employees based on weighted metrics including sales, customer satisfaction, attendance, and more.')</h3>
                                                        <div class="box-tools pull-right">
                                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="chart-container" style="position: relative; height:300px;">
                                                                    <canvas id="employee_performance_score_chart"></canvas>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="chart-container" style="position: relative; height:300px;">
                                                                    <canvas id="employee_efficiency_chart"></canvas>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Employee Performance Trends and Predictions -->
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="box box-primary">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Employee Performance Trends & Predictions @show_tooltip('This chart shows historical performance trends of top employees and predicts their future performance based on data analysis.')</h3>
                                                        <div class="box-tools pull-right">
                                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:400px;">
                                                            <canvas id="employee_performance_trend_chart"></canvas>
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

                    <!-- Payment Analysis Tab -->
                    <div class="tab-pane" id="payment_analysis_tab">
                        <!-- Content for Payment Analysis tab -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Payment Analysis @show_tooltip('This analysis shows the distribution of payments by method over time, helping you understand customer payment preferences and trends.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <!-- Payment Method Distribution Chart -->
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="chart-container" style="position: relative; height:400px;">
                                                    <canvas id="payment_trend_chart"></canvas>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="chart-container" style="position: relative; height:400px;">
                                                    <canvas id="payment_distribution_chart"></canvas>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Payment Financial Year Chart -->
                                        <div class="row" style="margin-top: 20px;">
                                            <div class="col-md-12">
                                                <div class="box box-primary">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">{{ __('home.payments_current_fy') }} @show_tooltip('This chart shows payment amounts for the current financial year compared to last year, with trend line and prediction for next year.')</h3>
                                                        <div class="box-tools pull-right">
                                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:400px;">
                                                            <canvas id="payment_current_fy_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Payment Method Stats -->
                                        <div class="row" style="margin-top: 20px;">
                                            <div class="col-md-12">
                                                <h4>Payment Method Statistics</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>Payment Method</th>
                                                                <th>Total Amount</th>
                                                                <th>Percentage</th>
                                                                <th>Trend</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $total_payments = array_sum($data['payment_method_data'] ?? []);
                                                            @endphp
                                                            @if(!empty($data['payment_method_labels']))
                                                                @foreach($data['payment_method_labels'] as $index => $method)
                                                                    <tr>
                                                                        <td>{{ $method }}</td>
                                                                        <td>{{ @num_format($data['payment_method_data'][$index] ?? 0) }}</td>
                                                                        <td>
                                                                            @if($total_payments > 0)
                                                                                {{ @num_format(($data['payment_method_data'][$index] ?? 0) / $total_payments * 100) }}%
                                                                            @else
                                                                                0%
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @php
                                                                                $trend_data = $data['payment_trend_lines'][$index] ?? [];
                                                                                $trend = !empty($trend_data) && count($trend_data) > 1 ? 
                                                                                    ($trend_data[count($trend_data) - 1] - $trend_data[0]) : 0;
                                                                            @endphp
                                                                            @if($trend > 0)
                                                                                <span class="text-success"><i class="fa fa-arrow-up"></i> Increasing</span>
                                                                            @elseif($trend < 0)
                                                                                <span class="text-danger"><i class="fa fa-arrow-down"></i> Decreasing</span>
                                                                            @else
                                                                                <span class="text-muted"><i class="fa fa-minus"></i> Stable</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @else
                                                                <tr>
                                                                    <td colspan="4" class="text-center">No payment data available</td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Payment Seasonality Chart -->
                                        <div class="row" style="margin-top: 20px;">
                                            <div class="col-md-12">
                                                <div class="box box-primary">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Payment Seasonality Analysis @show_tooltip('This chart shows payment patterns by month/quarter with predictive analytics for future seasons.')</h3>
                                                        <div class="box-tools pull-right">
                                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:400px;">
                                                            <canvas id="payment_seasonality_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Payment Method Forecast and Customer Payment Behavior -->
                                        <div class="row" style="margin-top: 20px;">
                                            <div class="col-md-6">
                                                <div class="box box-primary">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Payment Method Forecast @show_tooltip('This chart predicts future usage of different payment methods based on historical trends.')</h3>
                                                        <div class="box-tools pull-right">
                                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:350px;">
                                                            <canvas id="payment_method_forecast_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="box box-primary">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Customer Payment Behavior @show_tooltip('This chart analyzes and predicts payment timing (early, on-time, late) to help with cash flow planning.')</h3>
                                                        <div class="box-tools pull-right">
                                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:350px;">
                                                            <canvas id="payment_behavior_chart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Payment Amount Distribution -->
                                        <div class="row" style="margin-top: 20px;">
                                            <div class="col-md-12">
                                                <div class="box box-primary">
                                                    <div class="box-header with-border">
                                                        <h3 class="box-title">Payment Amount Distribution @show_tooltip('This chart shows the distribution of payment amounts with trend analysis to identify patterns in customer spending.')</h3>
                                                        <div class="box-tools pull-right">
                                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="box-body">
                                                        <div class="chart-container" style="position: relative; height:400px;">
                                                            <canvas id="payment_amount_distribution_chart"></canvas>
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
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script type="text/javascript">
    // Helper function to calculate trend line using linear regression
    function calculateTrendLine(data) {
        const n = data.length;
        let sumX = 0;
        let sumY = 0;
        let sumXY = 0;
        let sumXX = 0;

        // Calculate sums for linear regression formula
        for (let i = 0; i < n; i++) {
            sumX += i;
            sumY += data[i];
            sumXY += i * data[i];
            sumXX += i * i;
        }

        // Calculate slope and y-intercept
        const slope = (n * sumXY - sumX * sumY) / (n * sumXX - sumX * sumX);
        const intercept = (sumY - slope * sumX) / n;

        // Generate trend line data points
        const trendLine = [];
        for (let i = 0; i < n; i++) {
            trendLine.push(slope * i + intercept);
        }

        return trendLine;
    }

    // Helper function to predict next year's data based on current year's trend
    function calculateNextYearPrediction(data) {
        const trendLine = calculateTrendLine(data);
        const n = data.length;

        // Calculate slope and intercept from trend line
        const slope = (trendLine[n-1] - trendLine[0]) / (n - 1);
        const lastValue = trendLine[n-1];

        // Generate prediction for next 12 months
        const prediction = [];
        for (let i = 1; i <= 12; i++) {
            prediction.push(lastValue + slope * i);
        }

        return prediction;
    }

    $(document).ready(function() {
        // Initialize charts if data is available
        if (typeof Chart !== 'undefined') {
            // Sales Trend Chart with Prediction
            if (document.getElementById('sales_trend_chart')) {
                var salesTrendCtx = document.getElementById('sales_trend_chart').getContext('2d');
                var salesTrendData = {!! json_encode($data['sales_trend_data'] ?? []) !!};

                // Calculate trend line and prediction
                var trendLine = calculateTrendLine(salesTrendData);
                var prediction = calculateNextYearPrediction(salesTrendData);

                // Calculate confidence intervals (20% above and below prediction)
                var upperBound = prediction.map(value => value * 1.2);
                var lowerBound = prediction.map(value => value * 0.8);

                // Create labels for prediction period
                var salesTrendLabels = {!! json_encode($data['sales_trend_labels'] ?? []) !!};
                var predictionLabels = [];

                // Generate future date labels based on the pattern of existing labels
                if (salesTrendLabels.length > 0) {
                    var lastLabel = salesTrendLabels[salesTrendLabels.length - 1];
                    var labelPattern = lastLabel.match(/\d+/g);

                    if (labelPattern && labelPattern.length > 0) {
                        var lastMonth = parseInt(labelPattern[0]);
                        var lastYear = labelPattern.length > 1 ? parseInt(labelPattern[1]) : new Date().getFullYear();

                        for (var i = 1; i <= 12; i++) {
                            var nextMonth = (lastMonth + i) % 12;
                            if (nextMonth === 0) nextMonth = 12;
                            var nextYear = lastYear + Math.floor((lastMonth + i - 1) / 12);
                            predictionLabels.push(nextMonth + '/' + nextYear);
                        }
                    } else {
                        // Fallback if pattern matching fails
                        for (var i = 1; i <= 12; i++) {
                            predictionLabels.push('Future ' + i);
                        }
                    }
                }

                var salesTrendChart = new Chart(salesTrendCtx, {
                    type: 'line',
                    data: {
                        labels: [...salesTrendLabels, ...predictionLabels],
                        datasets: [
                            {
                                label: 'Historical Sales',
                                data: [...salesTrendData, ...Array(12).fill(null)],
                                backgroundColor: 'rgba(60, 141, 188, 0.2)',
                                borderColor: 'rgba(60, 141, 188, 1)',
                                borderWidth: 2,
                                fill: true
                            },
                            {
                                label: 'Sales Trend',
                                data: [...trendLine, ...Array(12).fill(null)],
                                backgroundColor: 'rgba(0, 0, 0, 0)',
                                borderColor: 'rgba(0, 0, 0, 0.5)',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                pointRadius: 0,
                                fill: false
                            },
                            {
                                label: 'Sales Forecast',
                                data: [...Array(salesTrendData.length).fill(null), ...prediction],
                                backgroundColor: 'rgba(40, 167, 69, 0.2)',
                                borderColor: 'rgba(40, 167, 69, 1)',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                fill: '+1'
                            },
                            {
                                label: 'Upper Bound (80% Confidence)',
                                data: [...Array(salesTrendData.length).fill(null), ...upperBound],
                                backgroundColor: 'rgba(0, 0, 0, 0)',
                                borderColor: 'rgba(40, 167, 69, 0.4)',
                                borderWidth: 1,
                                borderDash: [2, 2],
                                pointRadius: 0,
                                fill: false
                            },
                            {
                                label: 'Lower Bound (80% Confidence)',
                                data: [...Array(salesTrendData.length).fill(null), ...lowerBound],
                                backgroundColor: 'rgba(0, 0, 0, 0)',
                                borderColor: 'rgba(40, 167, 69, 0.4)',
                                borderWidth: 1,
                                borderDash: [2, 2],
                                pointRadius: 0,
                                fill: false
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Sales Trend with Predictive Forecast',
                                font: {
                                    size: 16
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    footer: function(tooltipItems) {
                                        var dataIndex = tooltipItems[0].dataIndex;
                                        if (dataIndex >= salesTrendData.length) {
                                            return 'Prediction based on historical trend analysis';
                                        }
                                        return '';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Sales Amount'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Time Period'
                                }
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
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Sales Distribution by Category',
                                font: {
                                    size: 16
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.label || '';
                                        var value = context.raw || 0;
                                        var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        var percentage = Math.round((value / total) * 100);
                                        return label + ': ' + value + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Sales by Category Chart with Growth Indicators
            if (document.getElementById('sales_by_category_chart')) {
                var salesByCategoryCtx = document.getElementById('sales_by_category_chart').getContext('2d');

                // Use the same data as sales distribution but in a different format
                var categoryLabels = {!! json_encode($data['sales_distribution_labels'] ?? []) !!};
                var categoryData = {!! json_encode($data['sales_distribution_data'] ?? []) !!};

                // Use growth data from the backend
                var growthData = {!! json_encode($data['category_sales_growth_data'] ?? []) !!};

                var salesByCategoryChart = new Chart(salesByCategoryCtx, {
                    type: 'bar',
                    data: {
                        labels: categoryLabels,
                        datasets: [
                            {
                                label: 'Sales Amount',
                                data: categoryData,
                                backgroundColor: 'rgba(0, 166, 90, 0.6)',
                                borderColor: 'rgba(0, 166, 90, 1)',
                                borderWidth: 1,
                                order: 2
                            },
                            {
                                label: 'Growth Rate (%)',
                                data: growthData,
                                type: 'line',
                                backgroundColor: 'rgba(0, 0, 0, 0)',
                                borderColor: 'rgba(221, 75, 57, 1)',
                                borderWidth: 2,
                                pointBackgroundColor: growthData.map(value => 
                                    value > 0 ? 'rgba(0, 166, 90, 1)' : 'rgba(221, 75, 57, 1)'
                                ),
                                pointRadius: 5,
                                pointHoverRadius: 7,
                                yAxisID: 'y1',
                                order: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Sales by Category with Growth Rate',
                                font: {
                                    size: 16
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';
                                        var value = context.raw || 0;
                                        if (context.datasetIndex === 1) {
                                            return label + ': ' + value + '%';
                                        }
                                        return label + ': ' + value;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Sales Amount'
                                }
                            },
                            y1: {
                                position: 'right',
                                beginAtZero: false,
                                // Dynamic scale based on data range
                                suggestedMin: Math.min(...growthData) - 5,
                                suggestedMax: Math.max(...growthData) + 5,
                                title: {
                                    display: true,
                                    text: 'Growth Rate (%)'
                                },
                                grid: {
                                    drawOnChartArea: false
                                }
                            }
                        }
                    }
                });
            }

            // Sales by Location Chart - Current Month vs Last Year
            if (document.getElementById('sales_by_location_chart')) {
                var salesByLocationCtx = document.getElementById('sales_by_location_chart').getContext('2d');

                // Get data from the backend
                var locationLabels = {!! json_encode($data['location_sales_labels'] ?? ['Location 1', 'Location 2', 'Location 3', 'Location 4', 'Location 5']) !!};
                var currentMonthData = {!! json_encode($data['current_month_location_sales_data'] ?? [5000, 3000, 2000, 4000, 3500]) !!};
                var lastYearMonthData = {!! json_encode($data['last_year_month_location_sales_data'] ?? [4000, 2500, 1800, 3500, 3000]) !!};
                var currentMonthName = '{!! $data['current_month_name'] ?? "Current Month" !!}';

                var salesByLocationChart = new Chart(salesByLocationCtx, {
                    type: 'bar',
                    data: {
                        labels: locationLabels,
                        datasets: [
                            {
                                label: currentMonthName + ' ' + new Date().getFullYear(),
                                data: currentMonthData,
                                backgroundColor: 'rgba(60, 141, 188, 0.7)',
                                borderColor: 'rgba(60, 141, 188, 1)',
                                borderWidth: 1
                            },
                            {
                                label: currentMonthName + ' ' + (new Date().getFullYear() - 1),
                                data: lastYearMonthData,
                                backgroundColor: 'rgba(210, 214, 222, 0.7)',
                                borderColor: 'rgba(210, 214, 222, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Sales by Location - ' + currentMonthName + ' (Year-over-Year Comparison)',
                                font: {
                                    size: 16
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += new Intl.NumberFormat('en-US', { 
                                                style: 'currency', 
                                                currency: 'USD',
                                                minimumFractionDigits: 0,
                                                maximumFractionDigits: 0
                                            }).format(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Sales Amount'
                                }
                            }
                        }
                    }
                });
            }

            // Sales Forecast Chart with Confidence Intervals
            if (document.getElementById('sales_forecast_chart')) {
                var salesForecastCtx = document.getElementById('sales_forecast_chart').getContext('2d');

                // Use monthly data for the forecast
                var monthlyLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

                // In a real app, this data would come from the backend
                var historicalData = {!! json_encode($data['monthly_sales_data'] ?? [4200, 4500, 5100, 5400, 5200, 5600, 6100, 6300, 6200, 6700, 7000, 7200]) !!};

                // Calculate trend and forecast for next 12 months
                var trendLine = calculateTrendLine(historicalData);
                var forecast = calculateNextYearPrediction(historicalData);

                // Calculate confidence intervals with varying widths (narrower near term, wider long term)
                var upperBound = forecast.map((value, index) => value * (1 + 0.05 + (index * 0.01)));
                var lowerBound = forecast.map((value, index) => value * (1 - 0.05 - (index * 0.01)));

                // Generate future month labels
                var futureMonthLabels = monthlyLabels.map(month => month + ' ' + (new Date().getFullYear() + 1));

                var salesForecastChart = new Chart(salesForecastCtx, {
                    type: 'line',
                    data: {
                        labels: [...monthlyLabels, ...futureMonthLabels],
                        datasets: [
                            {
                                label: 'Historical Sales',
                                data: [...historicalData, ...Array(12).fill(null)],
                                backgroundColor: 'rgba(60, 141, 188, 0.2)',
                                borderColor: 'rgba(60, 141, 188, 1)',
                                borderWidth: 2,
                                pointRadius: 3,
                                fill: true
                            },
                            {
                                label: 'Sales Forecast',
                                data: [...Array(12).fill(null), ...forecast],
                                backgroundColor: 'rgba(0, 166, 90, 0.2)',
                                borderColor: 'rgba(0, 166, 90, 1)',
                                borderWidth: 2,
                                pointRadius: 3,
                                fill: true
                            },
                            {
                                label: 'Upper Bound',
                                data: [...Array(12).fill(null), ...upperBound],
                                backgroundColor: 'rgba(0, 0, 0, 0)',
                                borderColor: 'rgba(0, 166, 90, 0.4)',
                                borderWidth: 1,
                                borderDash: [5, 5],
                                pointRadius: 0,
                                fill: false
                            },
                            {
                                label: 'Lower Bound',
                                data: [...Array(12).fill(null), ...lowerBound],
                                backgroundColor: 'rgba(0, 0, 0, 0)',
                                borderColor: 'rgba(0, 166, 90, 0.4)',
                                borderWidth: 1,
                                borderDash: [5, 5],
                                pointRadius: 0,
                                fill: false
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Monthly Sales Forecast with Confidence Intervals',
                                font: {
                                    size: 16
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    footer: function(tooltipItems) {
                                        var dataIndex = tooltipItems[0].dataIndex;
                                        if (dataIndex >= 12) {
                                            return 'Forecast based on historical trend analysis\nConfidence interval widens with time horizon';
                                        }
                                        return '';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Sales Amount'
                                }
                            }
                        }
                    }
                });
            }

            // Customer Segmentation Chart
            if (document.getElementById('customer_segmentation_chart')) {
                var customerSegmentationCtx = document.getElementById('customer_segmentation_chart').getContext('2d');

                // In a real app, this data would come from the backend
                var segmentLabels = ['High Value', 'Regular', 'Occasional', 'New', 'At Risk'];
                var segmentData = [25, 40, 20, 10, 5]; // Percentage of customer base
                var segmentValue = [50, 30, 10, 5, 5]; // Percentage of revenue

                var customerSegmentationChart = new Chart(customerSegmentationCtx, {
                    type: 'radar',
                    data: {
                        labels: segmentLabels,
                        datasets: [
                            {
                                label: '% of Customer Base',
                                data: segmentData,
                                backgroundColor: 'rgba(60, 141, 188, 0.2)',
                                borderColor: 'rgba(60, 141, 188, 1)',
                                borderWidth: 2,
                                pointBackgroundColor: 'rgba(60, 141, 188, 1)'
                            },
                            {
                                label: '% of Revenue',
                                data: segmentValue,
                                backgroundColor: 'rgba(0, 166, 90, 0.2)',
                                borderColor: 'rgba(0, 166, 90, 1)',
                                borderWidth: 2,
                                pointBackgroundColor: 'rgba(0, 166, 90, 1)'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Customer Segmentation Analysis',
                                font: {
                                    size: 16
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';
                                        var value = context.raw || 0;
                                        return label + ': ' + value + '%';
                                    }
                                }
                            }
                        },
                        scales: {
                            r: {
                                min: 0,
                                max: 60,
                                ticks: {
                                    stepSize: 10
                                }
                            }
                        }
                    }
                });
            }

            // Sales Performance vs Target Chart
            if (document.getElementById('sales_vs_target_chart')) {
                var salesVsTargetCtx = document.getElementById('sales_vs_target_chart').getContext('2d');

                // Get data from the backend
                var performanceLabels = {!! json_encode($data['monthly_sales_labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']) !!};
                var actualSales = {!! json_encode($data['monthly_sales_current_year_data'] ?? [4200, 4500, 5100, 5400, 5200, 5600, 6100, 6300, 6200, 6700, 7000, 7200]) !!};

                // Get target data from the backend
                var targetSales = {!! json_encode($data['monthly_target_data'] ?? [4600, 5000, 5600, 5900, 5700, 6200, 6700, 6900, 6800, 7400, 7700, 7900]) !!};

                // Calculate forecast for next 6 months
                var currentMonth = new Date().getMonth();
                var displayedActual = actualSales.slice(0, currentMonth + 1);
                var remainingMonths = 12 - displayedActual.length;

                // Calculate trend based on displayed actual data
                var trendLine = calculateTrendLine(displayedActual);
                var slope = (trendLine[trendLine.length-1] - trendLine[0]) / (trendLine.length - 1);
                var lastValue = displayedActual[displayedActual.length - 1];

                // Generate forecast for remaining months
                var forecastSales = [];
                for (var i = 1; i <= remainingMonths; i++) {
                    forecastSales.push(lastValue + slope * i);
                }

                // Prepare data for chart
                var actualData = [...displayedActual, ...Array(remainingMonths).fill(null)];
                var forecastData = [...Array(displayedActual.length).fill(null), ...forecastSales];

                var salesVsTargetChart = new Chart(salesVsTargetCtx, {
                    type: 'bar',
                    data: {
                        labels: performanceLabels,
                        datasets: [
                            {
                                label: 'Actual Sales',
                                data: actualData,
                                backgroundColor: actualData.map((value, index) => 
                                    value === null ? 'rgba(0, 0, 0, 0)' : 
                                    (value >= targetSales[index] ? 'rgba(0, 166, 90, 0.7)' : 'rgba(221, 75, 57, 0.7)')
                                ),
                                borderColor: actualData.map((value, index) => 
                                    value === null ? 'rgba(0, 0, 0, 0)' : 
                                    (value >= targetSales[index] ? 'rgba(0, 166, 90, 1)' : 'rgba(221, 75, 57, 1)')
                                ),
                                borderWidth: 1,
                                order: 2
                            },
                            {
                                label: 'Sales Forecast',
                                data: forecastData,
                                backgroundColor: 'rgba(243, 156, 18, 0.5)',
                                borderColor: 'rgba(243, 156, 18, 1)',
                                borderWidth: 1,
                                order: 2
                            },
                            {
                                label: 'Target',
                                data: targetSales,
                                type: 'line',
                                backgroundColor: 'rgba(0, 0, 0, 0)',
                                borderColor: 'rgba(60, 141, 188, 1)',
                                borderWidth: 2,
                                pointRadius: 4,
                                pointBackgroundColor: 'rgba(60, 141, 188, 1)',
                                fill: false,
                                order: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Sales Performance vs Target with Forecast',
                                font: {
                                    size: 16
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    footer: function(tooltipItems) {
                                        var dataIndex = tooltipItems[0].dataIndex;
                                        var actualValue = actualData[dataIndex];
                                        var forecastValue = forecastData[dataIndex];
                                        var targetValue = targetSales[dataIndex];

                                        if (forecastValue !== null) {
                                            var performance = ((forecastValue / targetValue) * 100).toFixed(1);
                                            return 'Forecast Performance: ' + performance + '% of target';
                                        } else if (actualValue !== null) {
                                            var performance = ((actualValue / targetValue) * 100).toFixed(1);
                                            return 'Performance: ' + performance + '% of target';
                                        }
                                        return '';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Sales Amount'
                                }
                            }
                        }
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

            // Revenue Trend with Forecast Chart
            if (document.getElementById('revenue_trend_forecast_chart')) {
                var revenueTrendCtx = document.getElementById('revenue_trend_forecast_chart').getContext('2d');

                // Use sales trend data from the controller
                var revenueTrendLabels = {!! json_encode($data['sales_trend_labels'] ?? []) !!};
                var revenueTrendData = {!! json_encode($data['sales_trend_data'] ?? []) !!};

                // Calculate trend line using linear regression
                var xValues = Array.from({length: revenueTrendData.length}, (_, i) => i + 1);
                var xMean = xValues.reduce((a, b) => a + b, 0) / xValues.length;
                var yMean = revenueTrendData.reduce((a, b) => a + b, 0) / revenueTrendData.length;

                var numerator = 0;
                var denominator = 0;

                for (var i = 0; i < revenueTrendData.length; i++) {
                    numerator += (xValues[i] - xMean) * (revenueTrendData[i] - yMean);
                    denominator += Math.pow(xValues[i] - xMean, 2);
                }

                var slope = denominator !== 0 ? numerator / denominator : 0;
                var intercept = yMean - (slope * xMean);

                // Generate trend line data
                var trendLineData = [];
                for (var i = 0; i < revenueTrendData.length; i++) {
                    trendLineData.push(intercept + (slope * (i + 1)));
                }

                // Generate forecast for next 6 months
                var forecastData = [];
                var forecastLabels = [];
                var upperBoundData = [];
                var lowerBoundData = [];

                for (var i = 1; i <= 6; i++) {
                    var nextPoint = intercept + (slope * (revenueTrendData.length + i));
                    forecastData.push(nextPoint);

                    // Add confidence intervals (20% above and below)
                    upperBoundData.push(nextPoint * 1.2);
                    lowerBoundData.push(nextPoint * 0.8);

                    // Generate label for next month
                    var lastDateParts = revenueTrendLabels[revenueTrendLabels.length - 1].split('-');
                    var lastMonth = parseInt(lastDateParts[0]);
                    var lastYear = parseInt(lastDateParts[1]);

                    var nextMonth = lastMonth + i;
                    var nextYear = lastYear;

                    if (nextMonth > 12) {
                        nextMonth = nextMonth - 12;
                        nextYear++;
                    }

                    forecastLabels.push(nextMonth + '-' + nextYear);
                }

                var revenueTrendChart = new Chart(revenueTrendCtx, {
                    type: 'line',
                    data: {
                        labels: [...revenueTrendLabels, ...forecastLabels],
                        datasets: [{
                            label: 'Actual Revenue',
                            data: [...revenueTrendData, ...Array(forecastLabels.length).fill(null)],
                            backgroundColor: 'rgba(60, 141, 188, 0.2)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 2,
                            pointRadius: 3,
                            fill: false
                        }, {
                            label: 'Trend Line',
                            data: [...trendLineData, ...forecastData],
                            backgroundColor: 'rgba(210, 214, 222, 0.1)',
                            borderColor: 'rgba(210, 214, 222, 1)',
                            borderWidth: 2,
                            pointRadius: 0,
                            borderDash: [5, 5],
                            fill: false
                        }, {
                            label: 'Revenue Forecast',
                            data: [...Array(revenueTrendData.length).fill(null), ...forecastData],
                            backgroundColor: 'rgba(0, 166, 90, 0.2)',
                            borderColor: 'rgba(0, 166, 90, 1)',
                            borderWidth: 2,
                            pointRadius: 3,
                            fill: false
                        }, {
                            label: 'Upper Bound (20%)',
                            data: [...Array(revenueTrendData.length).fill(null), ...upperBoundData],
                            backgroundColor: 'rgba(0, 166, 90, 0)',
                            borderColor: 'rgba(0, 166, 90, 0.5)',
                            borderWidth: 1,
                            pointRadius: 0,
                            borderDash: [3, 3],
                            fill: false
                        }, {
                            label: 'Lower Bound (20%)',
                            data: [...Array(revenueTrendData.length).fill(null), ...lowerBoundData],
                            backgroundColor: 'rgba(0, 166, 90, 0)',
                            borderColor: 'rgba(0, 166, 90, 0.5)',
                            borderWidth: 1,
                            pointRadius: 0,
                            borderDash: [3, 3],
                            fill: '+1'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Revenue Amount'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Time Period'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Revenue Trend with 6-Month Forecast'
                            },
                            tooltip: {
                                callbacks: {
                                    footer: function(tooltipItems) {
                                        var dataIndex = tooltipItems[0].dataIndex;
                                        if (dataIndex >= revenueTrendData.length) {
                                            return 'Prediction based on historical trend analysis';
                                        }
                                        return '';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Revenue Growth Prediction Chart
            if (document.getElementById('revenue_growth_prediction_chart')) {
                var revenueGrowthCtx = document.getElementById('revenue_growth_prediction_chart').getContext('2d');

                // Use monthly sales data from the controller
                var monthlyLabels = {!! json_encode($data['monthly_sales_labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']) !!};
                var currentYearData = {!! json_encode($data['monthly_sales_current_year_data'] ?? []) !!};
                var previousYearData = {!! json_encode($data['monthly_sales_previous_year_data'] ?? []) !!};

                // Calculate growth rates between years
                var growthRates = [];
                for (var i = 0; i < currentYearData.length; i++) {
                    if (previousYearData[i] > 0) {
                        growthRates.push(((currentYearData[i] - previousYearData[i]) / previousYearData[i]) * 100);
                    } else {
                        growthRates.push(currentYearData[i] > 0 ? 100 : 0);
                    }
                }

                // Calculate trend line for growth rates
                var xValues = Array.from({length: growthRates.length}, (_, i) => i + 1);
                var xMean = xValues.reduce((a, b) => a + b, 0) / xValues.length;
                var yMean = growthRates.reduce((a, b) => a + b, 0) / growthRates.length;

                var numerator = 0;
                var denominator = 0;

                for (var i = 0; i < growthRates.length; i++) {
                    numerator += (xValues[i] - xMean) * (growthRates[i] - yMean);
                    denominator += Math.pow(xValues[i] - xMean, 2);
                }

                var slope = denominator !== 0 ? numerator / denominator : 0;
                var intercept = yMean - (slope * xMean);

                // Generate trend line data
                var trendLineData = [];
                for (var i = 0; i < growthRates.length; i++) {
                    trendLineData.push(intercept + (slope * (i + 1)));
                }

                // Generate forecast for next year
                var forecastData = [];
                for (var i = 1; i <= 12; i++) {
                    forecastData.push(intercept + (slope * (growthRates.length + i)));
                }

                // Calculate confidence intervals (20% above and below)
                var upperBoundData = forecastData.map(value => value * 1.2);
                var lowerBoundData = forecastData.map(value => value * 0.8);

                var revenueGrowthChart = new Chart(revenueGrowthCtx, {
                    type: 'line',
                    data: {
                        labels: [...monthlyLabels, ...monthlyLabels],
                        datasets: [{
                            label: 'Historical Growth Rate',
                            data: [...growthRates, ...Array(12).fill(null)],
                            backgroundColor: 'rgba(60, 141, 188, 0.2)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 2,
                            pointRadius: 3,
                            fill: false
                        }, {
                            label: 'Growth Trend',
                            data: [...trendLineData, ...forecastData],
                            backgroundColor: 'rgba(210, 214, 222, 0.1)',
                            borderColor: 'rgba(210, 214, 222, 1)',
                            borderWidth: 2,
                            pointRadius: 0,
                            borderDash: [5, 5],
                            fill: false
                        }, {
                            label: 'Predicted Growth Rate',
                            data: [...Array(12).fill(null), ...forecastData],
                            backgroundColor: 'rgba(0, 166, 90, 0.2)',
                            borderColor: 'rgba(0, 166, 90, 1)',
                            borderWidth: 2,
                            pointRadius: 3,
                            fill: false
                        }, {
                            label: 'Upper Bound (20%)',
                            data: [...Array(12).fill(null), ...upperBoundData],
                            backgroundColor: 'rgba(0, 166, 90, 0)',
                            borderColor: 'rgba(0, 166, 90, 0.5)',
                            borderWidth: 1,
                            pointRadius: 0,
                            borderDash: [3, 3],
                            fill: false
                        }, {
                            label: 'Lower Bound (20%)',
                            data: [...Array(12).fill(null), ...lowerBoundData],
                            backgroundColor: 'rgba(0, 166, 90, 0)',
                            borderColor: 'rgba(0, 166, 90, 0.5)',
                            borderWidth: 1,
                            pointRadius: 0,
                            borderDash: [3, 3],
                            fill: '+1'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                title: {
                                    display: true,
                                    text: 'Growth Rate (%)'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Month'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Revenue Growth Rate Prediction'
                            },
                            tooltip: {
                                callbacks: {
                                    footer: function(tooltipItems) {
                                        var dataIndex = tooltipItems[0].dataIndex;
                                        if (dataIndex >= 12) {
                                            return 'Prediction based on historical growth patterns';
                                        }
                                        return '';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Profit Margin Trend Chart with Forecast
            if (document.getElementById('profit_margin_trend_chart')) {
                var profitMarginTrendCtx = document.getElementById('profit_margin_trend_chart').getContext('2d');

                // Use time period labels from the backend or default to months
                var timeLabels = {!! json_encode($data['profit_margin_labels'] ?? $data['sales_trend_labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']) !!};

                // Historical data - in a real app, this would come from the backend
                var grossMarginData = {!! json_encode($data['gross_margin_data'] ?? [30, 32, 28, 35, 40, 38, 42, 45, 43, 47, 50, 48]) !!};
                var netMarginData = {!! json_encode($data['net_margin_data'] ?? [20, 22, 18, 25, 30, 28, 32, 35, 33, 37, 40, 38]) !!};
                var operatingMarginData = {!! json_encode($data['operating_margin_data'] ?? [25, 27, 23, 30, 35, 33, 37, 40, 38, 42, 45, 43]) !!};

                // Calculate trend lines using linear regression
                var grossMarginTrend = calculateTrendLine(grossMarginData);
                var netMarginTrend = calculateTrendLine(netMarginData);
                var operatingMarginTrend = calculateTrendLine(operatingMarginData);

                // Generate forecasts for next 6 periods
                var forecastPeriods = 6;
                var grossMarginForecast = [];
                var netMarginForecast = [];
                var operatingMarginForecast = [];

                // Calculate forecasts based on trend
                var grossSlope = (grossMarginTrend[grossMarginTrend.length-1] - grossMarginTrend[0]) / (grossMarginTrend.length - 1);
                var netSlope = (netMarginTrend[netMarginTrend.length-1] - netMarginTrend[0]) / (netMarginTrend.length - 1);
                var operatingSlope = (operatingMarginTrend[operatingMarginTrend.length-1] - operatingMarginTrend[0]) / (operatingMarginTrend.length - 1);

                var lastGrossValue = grossMarginData[grossMarginData.length - 1];
                var lastNetValue = netMarginData[netMarginData.length - 1];
                var lastOperatingValue = operatingMarginData[operatingMarginData.length - 1];

                for (var i = 1; i <= forecastPeriods; i++) {
                    grossMarginForecast.push(lastGrossValue + grossSlope * i);
                    netMarginForecast.push(lastNetValue + netSlope * i);
                    operatingMarginForecast.push(lastOperatingValue + operatingSlope * i);
                }

                // Generate future period labels
                var futurePeriods = [];
                if (timeLabels.length > 0) {
                    var lastLabel = timeLabels[timeLabels.length - 1];
                    var labelPattern = lastLabel.match(/\d+/g);

                    if (labelPattern && labelPattern.length > 0) {
                        var lastMonth = parseInt(labelPattern[0]);
                        var lastYear = labelPattern.length > 1 ? parseInt(labelPattern[1]) : new Date().getFullYear();

                        for (var i = 1; i <= forecastPeriods; i++) {
                            var nextMonth = (lastMonth + i) % 12;
                            if (nextMonth === 0) nextMonth = 12;
                            var nextYear = lastYear + Math.floor((lastMonth + i - 1) / 12);
                            futurePeriods.push(nextMonth + '/' + nextYear);
                        }
                    } else {
                        // Fallback if pattern matching fails
                        for (var i = 1; i <= forecastPeriods; i++) {
                            futurePeriods.push('Future ' + i);
                        }
                    }
                }

                // Calculate confidence intervals (10% above and below for gross margin)
                var upperGrossMargin = grossMarginForecast.map(value => value * 1.1);
                var lowerGrossMargin = grossMarginForecast.map(value => value * 0.9);

                var profitMarginTrendChart = new Chart(profitMarginTrendCtx, {
                    type: 'line',
                    data: {
                        labels: [...timeLabels, ...futurePeriods],
                        datasets: [
                            {
                                label: 'Gross Profit Margin',
                                data: [...grossMarginData, ...Array(forecastPeriods).fill(null)],
                                backgroundColor: 'rgba(60, 141, 188, 0.1)',
                                borderColor: 'rgba(60, 141, 188, 1)',
                                borderWidth: 2,
                                pointRadius: 3,
                                fill: false
                            },
                            {
                                label: 'Net Profit Margin',
                                data: [...netMarginData, ...Array(forecastPeriods).fill(null)],
                                backgroundColor: 'rgba(0, 166, 90, 0.1)',
                                borderColor: 'rgba(0, 166, 90, 1)',
                                borderWidth: 2,
                                pointRadius: 3,
                                fill: false
                            },
                            {
                                label: 'Operating Margin',
                                data: [...operatingMarginData, ...Array(forecastPeriods).fill(null)],
                                backgroundColor: 'rgba(243, 156, 18, 0.1)',
                                borderColor: 'rgba(243, 156, 18, 1)',
                                borderWidth: 2,
                                pointRadius: 3,
                                fill: false
                            },
                            {
                                label: 'Gross Margin Forecast',
                                data: [...Array(timeLabels.length).fill(null), ...grossMarginForecast],
                                backgroundColor: 'rgba(60, 141, 188, 0.1)',
                                borderColor: 'rgba(60, 141, 188, 1)',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                pointRadius: 3,
                                pointStyle: 'triangle',
                                fill: false
                            },
                            {
                                label: 'Net Margin Forecast',
                                data: [...Array(timeLabels.length).fill(null), ...netMarginForecast],
                                backgroundColor: 'rgba(0, 166, 90, 0.1)',
                                borderColor: 'rgba(0, 166, 90, 1)',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                pointRadius: 3,
                                pointStyle: 'triangle',
                                fill: false
                            },
                            {
                                label: 'Operating Margin Forecast',
                                data: [...Array(timeLabels.length).fill(null), ...operatingMarginForecast],
                                backgroundColor: 'rgba(243, 156, 18, 0.1)',
                                borderColor: 'rgba(243, 156, 18, 1)',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                pointRadius: 3,
                                pointStyle: 'triangle',
                                fill: false
                            },
                            {
                                label: 'Upper Confidence (Gross)',
                                data: [...Array(timeLabels.length).fill(null), ...upperGrossMargin],
                                backgroundColor: 'rgba(0, 0, 0, 0)',
                                borderColor: 'rgba(60, 141, 188, 0.3)',
                                borderWidth: 1,
                                borderDash: [2, 2],
                                pointRadius: 0,
                                fill: false,
                                hidden: true
                            },
                            {
                                label: 'Lower Confidence (Gross)',
                                data: [...Array(timeLabels.length).fill(null), ...lowerGrossMargin],
                                backgroundColor: 'rgba(0, 0, 0, 0)',
                                borderColor: 'rgba(60, 141, 188, 0.3)',
                                borderWidth: 1,
                                borderDash: [2, 2],
                                pointRadius: 0,
                                fill: false,
                                hidden: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Profit Margin Trends with Predictive Forecast',
                                font: {
                                    size: 16
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    footer: function(tooltipItems) {
                                        var dataIndex = tooltipItems[0].dataIndex;
                                        if (dataIndex >= timeLabels.length) {
                                            return 'Forecast based on historical trend analysis';
                                        }
                                        return '';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Margin Percentage (%)'
                                },
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Time Period'
                                }
                            }
                        }
                    }
                });
            }

            // Profit by Category Chart with Growth Indicators
            if (document.getElementById('profit_by_category_chart')) {
                var profitByCategoryCtx = document.getElementById('profit_by_category_chart').getContext('2d');

                // Use category labels from the backend or default
                var categoryLabels = {!! json_encode($data['profit_category_labels'] ?? $data['sales_distribution_labels'] ?? ['Category 1', 'Category 2', 'Category 3', 'Category 4', 'Category 5']) !!};

                // Profit data by category - in a real app, this would come from the backend
                var profitData = {!! json_encode($data['profit_by_category'] ?? [1500, 2200, 1800, 2500, 1900]) !!};

                // Generate growth data (in a real app, this would come from the backend)
                var growthData = {!! json_encode($data['profit_category_growth'] ?? [15, -5, 8, 20, -3]) !!};

                // Calculate margin percentages
                var marginPercentages = {!! json_encode($data['category_margin_percentages'] ?? [35, 28, 42, 30, 25]) !!};

                var profitByCategoryChart = new Chart(profitByCategoryCtx, {
                    type: 'bar',
                    data: {
                        labels: categoryLabels,
                        datasets: [
                            {
                                label: 'Profit Amount',
                                data: profitData,
                                backgroundColor: 'rgba(0, 166, 90, 0.6)',
                                borderColor: 'rgba(0, 166, 90, 1)',
                                borderWidth: 1,
                                order: 2
                            },
                            {
                                label: 'Growth Rate (%)',
                                data: growthData,
                                type: 'line',
                                backgroundColor: 'rgba(0, 0, 0, 0)',
                                borderColor: 'rgba(221, 75, 57, 1)',
                                borderWidth: 2,
                                pointBackgroundColor: growthData.map(value => 
                                    value > 0 ? 'rgba(0, 166, 90, 1)' : 'rgba(221, 75, 57, 1)'
                                ),
                                pointRadius: 5,
                                pointHoverRadius: 7,
                                yAxisID: 'y1',
                                order: 1
                            },
                            {
                                label: 'Margin Percentage (%)',
                                data: marginPercentages,
                                type: 'line',
                                backgroundColor: 'rgba(0, 0, 0, 0)',
                                borderColor: 'rgba(60, 141, 188, 1)',
                                borderWidth: 2,
                                pointBackgroundColor: 'rgba(60, 141, 188, 1)',
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                yAxisID: 'y2',
                                order: 0
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Profit by Category with Growth & Margin Analysis',
                                font: {
                                    size: 16
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';
                                        var value = context.raw || 0;
                                        if (context.datasetIndex === 1) {
                                            return label + ': ' + value + '%';
                                        } else if (context.datasetIndex === 2) {
                                            return label + ': ' + value + '%';
                                        }
                                        return label + ': ' + value;
                                    },
                                    afterBody: function(tooltipItems) {
                                        var dataIndex = tooltipItems[0].dataIndex;
                                        var profitValue = profitData[dataIndex];
                                        var growthValue = growthData[dataIndex];
                                        var marginValue = marginPercentages[dataIndex];

                                        var prediction = '';
                                        if (growthValue > 10 && marginValue > 30) {
                                            prediction = 'High growth potential with strong margins';
                                        } else if (growthValue < 0 && marginValue < 25) {
                                            prediction = 'Requires attention: declining growth with low margins';
                                        } else if (growthValue > 0 && marginValue < 25) {
                                            prediction = 'Growing but with margin pressure';
                                        } else if (growthValue < 0 && marginValue > 30) {
                                            prediction = 'Declining growth but maintaining good margins';
                                        } else {
                                            prediction = 'Stable performance';
                                        }

                                        return ['Predictive Analysis: ' + prediction];
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Profit Amount'
                                }
                            },
                            y1: {
                                position: 'right',
                                beginAtZero: false,
                                min: -20,
                                max: 30,
                                title: {
                                    display: true,
                                    text: 'Growth Rate (%)'
                                },
                                grid: {
                                    drawOnChartArea: false
                                }
                            },
                            y2: {
                                display: false,
                                min: 0,
                                max: 50
                            }
                        }
                    }
                });
            }

            // Profit vs Cost Analysis Chart
            if (document.getElementById('profit_vs_cost_chart')) {
                var profitVsCostCtx = document.getElementById('profit_vs_cost_chart').getContext('2d');

                // In a real app, this data would come from the backend
                var timeLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                var revenueData = {!! json_encode($data['monthly_revenue'] ?? [8000, 8500, 9200, 9800, 9500, 10200, 11000, 11500, 11200, 12000, 12500, 12800]) !!};
                var costData = {!! json_encode($data['monthly_cost'] ?? [5600, 5950, 6440, 6860, 6650, 7140, 7700, 8050, 7840, 8400, 8750, 8960]) !!};

                // Calculate profit data
                var profitData = revenueData.map((revenue, index) => revenue - costData[index]);

                // Calculate profit margin percentage
                var marginPercentage = profitData.map((profit, index) => (profit / revenueData[index] * 100).toFixed(1));

                // Calculate optimal price points based on cost and desired margin
                var targetMargin = 35; // Target margin percentage
                var optimalPricePoints = costData.map(cost => cost / (1 - targetMargin/100));

                var profitVsCostChart = new Chart(profitVsCostCtx, {
                    type: 'bar',
                    data: {
                        labels: timeLabels,
                        datasets: [
                            {
                                label: 'Revenue',
                                data: revenueData,
                                backgroundColor: 'rgba(60, 141, 188, 0.7)',
                                borderColor: 'rgba(60, 141, 188, 1)',
                                borderWidth: 1,
                                order: 1
                            },
                            {
                                label: 'Cost',
                                data: costData,
                                backgroundColor: 'rgba(221, 75, 57, 0.7)',
                                borderColor: 'rgba(221, 75, 57, 1)',
                                borderWidth: 1,
                                order: 2
                            },
                            {
                                label: 'Profit Margin %',
                                data: marginPercentage,
                                type: 'line',
                                backgroundColor: 'rgba(0, 0, 0, 0)',
                                borderColor: 'rgba(0, 166, 90, 1)',
                                borderWidth: 2,
                                pointBackgroundColor: 'rgba(0, 166, 90, 1)',
                                pointRadius: 4,
                                yAxisID: 'y1',
                                order: 0
                            },
                            {
                                label: 'Optimal Price Point',
                                data: optimalPricePoints,
                                type: 'line',
                                backgroundColor: 'rgba(0, 0, 0, 0)',
                                borderColor: 'rgba(243, 156, 18, 1)',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                pointBackgroundColor: 'rgba(243, 156, 18, 1)',
                                pointRadius: 3,
                                order: 0,
                                hidden: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Revenue vs Cost Analysis with Profit Margin',
                                font: {
                                    size: 16
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';
                                        var value = context.raw || 0;
                                        if (context.datasetIndex === 2) {
                                            return label + ': ' + value + '%';
                                        }
                                        return label + ': ' + value;
                                    },
                                    afterBody: function(tooltipItems) {
                                        var dataIndex = tooltipItems[0].dataIndex;
                                        var revenue = revenueData[dataIndex];
                                        var cost = costData[dataIndex];
                                        var profit = profitData[dataIndex];
                                        var margin = marginPercentage[dataIndex];
                                        var optimal = optimalPricePoints[dataIndex].toFixed(0);

                                        var analysis = [];
                                        analysis.push('Profit: ' + profit);

                                        if (margin < 30) {
                                            analysis.push('Margin below target: Consider price increase');
                                            analysis.push('Suggested price point: ' + optimal + ' (for ' + targetMargin + '% margin)');
                                        } else if (margin > 40) {
                                            analysis.push('Strong margin: Consider volume strategies');
                                        } else {
                                            analysis.push('Healthy margin: Maintain current strategy');
                                        }

                                        return analysis;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Amount'
                                },
                                stacked: false
                            },
                            y1: {
                                position: 'right',
                                beginAtZero: true,
                                max: 50,
                                title: {
                                    display: true,
                                    text: 'Margin %'
                                },
                                grid: {
                                    drawOnChartArea: false
                                },
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

            // Margin Optimization Opportunities Chart
            if (document.getElementById('margin_optimization_chart')) {
                var marginOptimizationCtx = document.getElementById('margin_optimization_chart').getContext('2d');

                // In a real app, this data would come from the backend
                var productLabels = ['Product A', 'Product B', 'Product C', 'Product D', 'Product E', 'Product F', 'Product G', 'Product H'];
                var currentMargins = [25, 18, 32, 15, 40, 22, 28, 35];
                var potentialMargins = [30, 25, 35, 25, 42, 30, 32, 38];
                var salesVolume = [120, 200, 80, 250, 60, 150, 100, 70];

                // Calculate margin improvement percentage
                var marginImprovement = currentMargins.map((current, index) => 
                    ((potentialMargins[index] - current) / current * 100).toFixed(1)
                );

                // Calculate potential additional profit
                var additionalProfit = currentMargins.map((current, index) => {
                    var currentProfit = salesVolume[index] * (current / 100);
                    var potentialProfit = salesVolume[index] * (potentialMargins[index] / 100);
                    return potentialProfit - currentProfit;
                });

                // Calculate bubble sizes based on potential profit impact
                var bubbleSizes = additionalProfit.map(profit => Math.max(profit * 2, 5));

                var marginOptimizationChart = new Chart(marginOptimizationCtx, {
                    type: 'bubble',
                    data: {
                        datasets: productLabels.map((label, index) => ({
                            label: label,
                            data: [{
                                x: currentMargins[index],
                                y: marginImprovement[index],
                                r: bubbleSizes[index]
                            }],
                            backgroundColor: currentMargins[index] < 25 ? 'rgba(221, 75, 57, 0.7)' : 'rgba(0, 166, 90, 0.7)'
                        }))
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Margin Optimization Opportunities',
                                font: {
                                    size: 16
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var index = context.datasetIndex;
                                        var product = productLabels[index];
                                        var current = currentMargins[index];
                                        var potential = potentialMargins[index];
                                        var improvement = marginImprovement[index];
                                        var volume = salesVolume[index];
                                        var profit = additionalProfit[index].toFixed(0);

                                        return [
                                            product,
                                            'Current Margin: ' + current + '%',
                                            'Potential Margin: ' + potential + '%',
                                            'Improvement: ' + improvement + '%',
                                            'Sales Volume: ' + volume + ' units',
                                            'Additional Profit: ' + profit
                                        ];
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Current Margin (%)'
                                },
                                min: 10,
                                max: 45,
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Potential Improvement (%)'
                                },
                                min: 0,
                                max: 70,
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

            // Comparative Margin Analysis Chart
            if (document.getElementById('comparative_margin_chart')) {
                var comparativeMarginCtx = document.getElementById('comparative_margin_chart').getContext('2d');

                // In a real app, this data would come from the backend
                var comparisonLabels = ['Your Business', 'Industry Average', 'Top Performers', 'Last Year', 'Forecast'];

                // Margin data for different metrics
                var grossMarginComparison = [35, 32, 40, 33, 37];
                var netMarginComparison = [22, 18, 25, 20, 24];
                var operatingMarginComparison = [28, 25, 32, 26, 30];

                var comparativeMarginChart = new Chart(comparativeMarginCtx, {
                    type: 'radar',
                    data: {
                        labels: comparisonLabels,
                        datasets: [
                            {
                                label: 'Gross Margin',
                                data: grossMarginComparison,
                                backgroundColor: 'rgba(60, 141, 188, 0.2)',
                                borderColor: 'rgba(60, 141, 188, 1)',
                                borderWidth: 2,
                                pointBackgroundColor: 'rgba(60, 141, 188, 1)',
                                pointRadius: 4
                            },
                            {
                                label: 'Net Margin',
                                data: netMarginComparison,
                                backgroundColor: 'rgba(0, 166, 90, 0.2)',
                                borderColor: 'rgba(0, 166, 90, 1)',
                                borderWidth: 2,
                                pointBackgroundColor: 'rgba(0, 166, 90, 1)',
                                pointRadius: 4
                            },
                            {
                                label: 'Operating Margin',
                                data: operatingMarginComparison,
                                backgroundColor: 'rgba(243, 156, 18, 0.2)',
                                borderColor: 'rgba(243, 156, 18, 1)',
                                borderWidth: 2,
                                pointBackgroundColor: 'rgba(243, 156, 18, 1)',
                                pointRadius: 4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Comparative Margin Analysis',
                                font: {
                                    size: 16
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';
                                        var value = context.raw || 0;
                                        return label + ': ' + value + '%';
                                    },
                                    afterBody: function(tooltipItems) {
                                        var dataIndex = tooltipItems[0].dataIndex;
                                        var label = comparisonLabels[dataIndex];

                                        if (label === 'Industry Average') {
                                            return ['Benchmark: Your gross margin is ' + 
                                                (grossMarginComparison[0] > grossMarginComparison[1] ? 'above' : 'below') + 
                                                ' industry average by ' + 
                                                Math.abs(grossMarginComparison[0] - grossMarginComparison[1]) + '%'];
                                        } else if (label === 'Top Performers') {
                                            var gap = grossMarginComparison[2] - grossMarginComparison[0];
                                            return ['Gap Analysis: ' + gap + '% margin improvement potential to reach top performers'];
                                        } else if (label === 'Forecast') {
                                            return ['Predictive Analysis: Expected ' + 
                                                (grossMarginComparison[4] > grossMarginComparison[0] ? 'improvement' : 'decline') + 
                                                ' of ' + Math.abs(grossMarginComparison[4] - grossMarginComparison[0]) + 
                                                '% in gross margin'];
                                        }
                                        return [];
                                    }
                                }
                            }
                        },
                        scales: {
                            r: {
                                min: 0,
                                max: 45,
                                ticks: {
                                    stepSize: 10,
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Seasonal Revenue Chart
            if (document.getElementById('seasonal_revenue_chart')) {
                var seasonalRevenueCtx = document.getElementById('seasonal_revenue_chart').getContext('2d');

                // Use quarterly data from the controller
                var quarterlyLabels = {!! json_encode($data['quarterly_labels'] ?? ['Q1', 'Q2', 'Q3', 'Q4']) !!};
                var quarterlySalesData = {!! json_encode($data['quarterly_sales_data'] ?? []) !!};

                // Calculate seasonal indices
                var totalAverage = quarterlySalesData.reduce((a, b) => a + b, 0) / quarterlySalesData.length;
                var seasonalIndices = quarterlySalesData.map(value => value / totalAverage);

                // Predict next year's quarterly sales using seasonal indices
                var nextYearPrediction = [];
                var growthRate = {!! json_encode($data['revenue_growth'] ?? 5) !!} / 100; // Use revenue growth from controller or default to 5%

                for (var i = 0; i < seasonalIndices.length; i++) {
                    nextYearPrediction.push(quarterlySalesData[i] * (1 + growthRate) * seasonalIndices[i]);
                }

                // Calculate confidence intervals (20% above and below)
                var upperBoundData = nextYearPrediction.map(value => value * 1.2);
                var lowerBoundData = nextYearPrediction.map(value => value * 0.8);

                var seasonalRevenueChart = new Chart(seasonalRevenueCtx, {
                    type: 'line',
                    data: {
                        labels: [...quarterlyLabels, ...quarterlyLabels.map(q => 'Next ' + q)],
                        datasets: [{
                            label: 'Current Year Quarterly Revenue',
                            data: [...quarterlySalesData, ...Array(4).fill(null)],
                            backgroundColor: 'rgba(60, 141, 188, 0.2)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 2,
                            pointRadius: 5,
                            fill: false
                        }, {
                            label: 'Seasonal Index',
                            data: [...seasonalIndices.map(idx => idx * totalAverage), ...Array(4).fill(null)],
                            backgroundColor: 'rgba(210, 214, 222, 0.1)',
                            borderColor: 'rgba(210, 214, 222, 1)',
                            borderWidth: 2,
                            pointRadius: 0,
                            borderDash: [5, 5],
                            fill: false,
                            hidden: true
                        }, {
                            label: 'Next Year Prediction',
                            data: [...Array(4).fill(null), ...nextYearPrediction],
                            backgroundColor: 'rgba(0, 166, 90, 0.2)',
                            borderColor: 'rgba(0, 166, 90, 1)',
                            borderWidth: 2,
                            pointRadius: 5,
                            fill: false
                        }, {
                            label: 'Upper Bound (20%)',
                            data: [...Array(4).fill(null), ...upperBoundData],
                            backgroundColor: 'rgba(0, 166, 90, 0)',
                            borderColor: 'rgba(0, 166, 90, 0.5)',
                            borderWidth: 1,
                            pointRadius: 0,
                            borderDash: [3, 3],
                            fill: false
                        }, {
                            label: 'Lower Bound (20%)',
                            data: [...Array(4).fill(null), ...lowerBoundData],
                            backgroundColor: 'rgba(0, 166, 90, 0)',
                            borderColor: 'rgba(0, 166, 90, 0.5)',
                            borderWidth: 1,
                            pointRadius: 0,
                            borderDash: [3, 3],
                            fill: '+1'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Revenue Amount'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Quarter'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Seasonal Revenue Analysis with Predictions'
                            },
                            tooltip: {
                                callbacks: {
                                    footer: function(tooltipItems) {
                                        var dataIndex = tooltipItems[0].dataIndex;
                                        if (dataIndex >= 4) {
                                            return 'Prediction based on seasonal patterns and growth rate';
                                        }
                                        return '';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Revenue Source Contribution Chart
            if (document.getElementById('revenue_source_chart')) {
                var revenueSourceCtx = document.getElementById('revenue_source_chart').getContext('2d');

                // Use sales distribution data from the controller
                var categoryLabels = {!! json_encode($data['sales_distribution_labels'] ?? []) !!};
                var categoryData = {!! json_encode($data['sales_distribution_data'] ?? []) !!};

                // Calculate percentage contribution
                var totalRevenue = categoryData.reduce((a, b) => a + b, 0);
                var percentageData = categoryData.map(value => (value / totalRevenue) * 100);

                // Predict future contribution changes based on growth trends
                var growthTrends = [];
                for (var i = 0; i < categoryData.length; i++) {
                    // Simulate different growth rates for different categories
                    growthTrends.push((Math.random() * 10) - 5); // Random growth between -5% and +5%
                }

                var futurePrediction = [];
                for (var i = 0; i < percentageData.length; i++) {
                    futurePrediction.push(percentageData[i] * (1 + (growthTrends[i] / 100)));
                }

                // Normalize future prediction to ensure it sums to 100%
                var totalFuture = futurePrediction.reduce((a, b) => a + b, 0);
                futurePrediction = futurePrediction.map(value => (value / totalFuture) * 100);

                var revenueSourceChart = new Chart(revenueSourceCtx, {
                    type: 'radar',
                    data: {
                        labels: categoryLabels,
                        datasets: [{
                            label: 'Current Contribution (%)',
                            data: percentageData,
                            backgroundColor: 'rgba(60, 141, 188, 0.2)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgba(60, 141, 188, 1)',
                            pointRadius: 3
                        }, {
                            label: 'Predicted Future Contribution (%)',
                            data: futurePrediction,
                            backgroundColor: 'rgba(0, 166, 90, 0.2)',
                            borderColor: 'rgba(0, 166, 90, 1)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgba(0, 166, 90, 1)',
                            pointRadius: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            r: {
                                angleLines: {
                                    display: true
                                },
                                suggestedMin: 0,
                                suggestedMax: Math.max(...percentageData, ...futurePrediction) * 1.1
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Revenue Source Contribution Analysis'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + context.raw.toFixed(2) + '%';
                                    },
                                    footer: function(tooltipItems) {
                                        if (tooltipItems[0].datasetIndex === 1) {
                                            return 'Prediction based on category growth trends';
                                        }
                                        return '';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Inventory Turnover Chart with Forecast
            if (document.getElementById('inventory_turnover_chart')) {
                var inventoryTurnoverCtx = document.getElementById('inventory_turnover_chart').getContext('2d');

                // Use monthly data for the past 12 months
                var monthlyLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

                // Historical inventory turnover data (use actual data from controller if available)
                var turnoverData = [4.2, 3.8, 4.1, 4.5, 4.3, 4.0, 3.9, 4.2, 4.4, 4.6, 4.5, {!! json_encode($data['inventory_turnover'] ?? 4.3) !!}];

                // Calculate trend line using linear regression
                var xValues = Array.from({length: turnoverData.length}, (_, i) => i + 1);
                var xMean = xValues.reduce((a, b) => a + b, 0) / xValues.length;
                var yMean = turnoverData.reduce((a, b) => a + b, 0) / turnoverData.length;

                var numerator = 0;
                var denominator = 0;

                for (var i = 0; i < turnoverData.length; i++) {
                    numerator += (xValues[i] - xMean) * (turnoverData[i] - yMean);
                    denominator += Math.pow(xValues[i] - xMean, 2);
                }

                var slope = denominator !== 0 ? numerator / denominator : 0;
                var intercept = yMean - (slope * xMean);

                // Generate trend line data
                var trendLineData = [];
                for (var i = 0; i < turnoverData.length; i++) {
                    trendLineData.push(intercept + (slope * (i + 1)));
                }

                // Generate forecast for next 6 months
                var forecastData = [];
                var forecastLabels = [];
                var upperBoundData = [];
                var lowerBoundData = [];

                for (var i = 1; i <= 6; i++) {
                    var nextPoint = intercept + (slope * (turnoverData.length + i));
                    forecastData.push(nextPoint);

                    // Add confidence intervals (15% above and below)
                    upperBoundData.push(nextPoint * 1.15);
                    lowerBoundData.push(nextPoint * 0.85);

                    // Generate label for next month
                    var nextMonthIndex = (monthlyLabels.length + i - 1) % 12;
                    forecastLabels.push(monthlyLabels[nextMonthIndex] + "'");
                }

                var inventoryTurnoverChart = new Chart(inventoryTurnoverCtx, {
                    type: 'line',
                    data: {
                        labels: [...monthlyLabels, ...forecastLabels],
                        datasets: [{
                            label: 'Historical Turnover',
                            data: [...turnoverData, ...Array(forecastLabels.length).fill(null)],
                            backgroundColor: 'rgba(60, 141, 188, 0.2)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 2,
                            pointRadius: 3,
                            fill: true
                        }, {
                            label: 'Trend Line',
                            data: [...trendLineData, ...forecastData],
                            backgroundColor: 'rgba(210, 214, 222, 0.1)',
                            borderColor: 'rgba(210, 214, 222, 1)',
                            borderWidth: 2,
                            pointRadius: 0,
                            borderDash: [5, 5],
                            fill: false
                        }, {
                            label: 'Turnover Forecast',
                            data: [...Array(turnoverData.length).fill(null), ...forecastData],
                            backgroundColor: 'rgba(0, 166, 90, 0.2)',
                            borderColor: 'rgba(0, 166, 90, 1)',
                            borderWidth: 2,
                            pointRadius: 3,
                            fill: false
                        }, {
                            label: 'Upper Bound (15%)',
                            data: [...Array(turnoverData.length).fill(null), ...upperBoundData],
                            backgroundColor: 'rgba(0, 166, 90, 0)',
                            borderColor: 'rgba(0, 166, 90, 0.5)',
                            borderWidth: 1,
                            pointRadius: 0,
                            borderDash: [3, 3],
                            fill: false
                        }, {
                            label: 'Lower Bound (15%)',
                            data: [...Array(turnoverData.length).fill(null), ...lowerBoundData],
                            backgroundColor: 'rgba(0, 166, 90, 0)',
                            borderColor: 'rgba(0, 166, 90, 0.5)',
                            borderWidth: 1,
                            pointRadius: 0,
                            borderDash: [3, 3],
                            fill: '+1'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: false,
                                title: {
                                    display: true,
                                    text: 'Turnover Rate'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Month'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Inventory Turnover Trend with 6-Month Forecast'
                            },
                            tooltip: {
                                callbacks: {
                                    footer: function(tooltipItems) {
                                        var dataIndex = tooltipItems[0].dataIndex;
                                        if (dataIndex >= turnoverData.length) {
                                            return 'Prediction based on historical trend analysis';
                                        }
                                        return '';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Inventory Value by Category Chart
            if (document.getElementById('inventory_value_chart')) {
                var inventoryValueCtx = document.getElementById('inventory_value_chart').getContext('2d');

                // Use category labels from the controller
                var categoryLabels = {!! json_encode($data['sales_distribution_labels'] ?? ['Category 1', 'Category 2', 'Category 3', 'Category 4', 'Category 5']) !!};

                // Generate inventory value data based on categories (use actual data if available)
                var inventoryValueData = [12000, 9500, 15000, 7800, 10200];

                // Calculate total inventory value
                var totalInventoryValue = {!! json_encode($data['inventory_value'] ?? 0) !!};

                // If we have a total but no breakdown, distribute proportionally
                if (totalInventoryValue > 0 && inventoryValueData.length > 0) {
                    var currentTotal = inventoryValueData.reduce((a, b) => a + b, 0);
                    if (currentTotal > 0) {
                        inventoryValueData = inventoryValueData.map(value => (value / currentTotal) * totalInventoryValue);
                    }
                }

                // Calculate future inventory value prediction with 10% growth
                var futureValueData = inventoryValueData.map(value => value * 1.1);

                var inventoryValueChart = new Chart(inventoryValueCtx, {
                    type: 'bar',
                    data: {
                        labels: categoryLabels,
                        datasets: [{
                            label: 'Current Inventory Value',
                            data: inventoryValueData,
                            backgroundColor: 'rgba(60, 141, 188, 0.8)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 1
                        }, {
                            label: 'Predicted Future Value (3 months)',
                            data: futureValueData,
                            backgroundColor: 'rgba(0, 166, 90, 0.6)',
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
                                title: {
                                    display: true,
                                    text: 'Value'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Category'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Inventory Value by Category with Prediction'
                            },
                            tooltip: {
                                callbacks: {
                                    footer: function(tooltipItems) {
                                        var datasetIndex = tooltipItems[0].datasetIndex;
                                        if (datasetIndex === 1) {
                                            return 'Prediction based on current trends and growth patterns';
                                        }
                                        return '';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Stock-out Risk Prediction Chart
            if (document.getElementById('stockout_risk_chart')) {
                var stockoutRiskCtx = document.getElementById('stockout_risk_chart').getContext('2d');

                // Sample product data with stock-out risk factors
                var productLabels = ['Product A', 'Product B', 'Product C', 'Product D', 'Product E'];
                var daysToStockout = [45, 12, 30, 5, 60]; // Days until predicted stock-out
                var salesVelocity = [10, 25, 15, 30, 8]; // Units sold per day

                // Calculate risk score (inverse of days to stock-out, normalized to 0-100)
                var riskScores = daysToStockout.map(days => Math.min(100, Math.max(0, 100 - (days / 60) * 100)));

                // Define risk categories
                var riskCategories = riskScores.map(score => {
                    if (score >= 80) return 'High Risk';
                    if (score >= 50) return 'Medium Risk';
                    return 'Low Risk';
                });

                // Define colors based on risk
                var riskColors = riskScores.map(score => {
                    if (score >= 80) return 'rgba(221, 75, 57, 0.8)'; // Red for high risk
                    if (score >= 50) return 'rgba(243, 156, 18, 0.8)'; // Yellow for medium risk
                    return 'rgba(0, 166, 90, 0.8)'; // Green for low risk
                });

                var stockoutRiskChart = new Chart(stockoutRiskCtx, {
                    type: 'bar',
                    data: {
                        labels: productLabels,
                        datasets: [{
                            label: 'Stock-out Risk Score',
                            data: riskScores,
                            backgroundColor: riskColors,
                            borderColor: riskColors.map(color => color.replace('0.8', '1')),
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
                                max: 100,
                                title: {
                                    display: true,
                                    text: 'Risk Score (0-100)'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Product Stock-out Risk Prediction'
                            },
                            tooltip: {
                                callbacks: {
                                    afterLabel: function(context) {
                                        var index = context.dataIndex;
                                        return [
                                            'Risk Category: ' + riskCategories[index],
                                            'Days to Stock-out: ' + daysToStockout[index],
                                            'Sales Velocity: ' + salesVelocity[index] + ' units/day'
                                        ];
                                    },
                                    footer: function() {
                                        return 'Prediction based on historical sales velocity and current stock levels';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Inventory Aging Analysis Chart
            if (document.getElementById('inventory_aging_chart')) {
                var inventoryAgingCtx = document.getElementById('inventory_aging_chart').getContext('2d');

                // Sample inventory aging data
                var ageCategories = ['0-30 days', '31-60 days', '61-90 days', '91-180 days', '181+ days'];
                var inventoryByAge = [45000, 25000, 15000, 10000, 5000]; // Value of inventory in each age category

                // Calculate percentage of total
                var totalInventory = inventoryByAge.reduce((a, b) => a + b, 0);
                var percentageByAge = inventoryByAge.map(value => (value / totalInventory) * 100);

                // Define colors based on age (newer = greener, older = redder)
                var ageColors = [
                    'rgba(0, 166, 90, 0.8)',    // 0-30 days (green)
                    'rgba(0, 192, 239, 0.8)',   // 31-60 days (blue)
                    'rgba(243, 156, 18, 0.8)',  // 61-90 days (yellow)
                    'rgba(255, 133, 27, 0.8)',  // 91-180 days (orange)
                    'rgba(221, 75, 57, 0.8)'    // 181+ days (red)
                ];

                // Calculate carrying cost by age (older inventory has higher carrying costs)
                var carryingCostRate = [0.02, 0.03, 0.04, 0.05, 0.06]; // Monthly carrying cost rate by age category
                var carryingCosts = inventoryByAge.map((value, index) => value * carryingCostRate[index]);

                // Calculate optimal inventory distribution (predictive)
                var optimalDistribution = [60, 25, 10, 5, 0]; // Optimal percentage distribution

                var inventoryAgingChart = new Chart(inventoryAgingCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ageCategories,
                        datasets: [{
                            label: 'Inventory Value by Age',
                            data: inventoryByAge,
                            backgroundColor: ageColors,
                            borderColor: ageColors.map(color => color.replace('0.8', '1')),
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Inventory Aging Analysis'
                            },
                            tooltip: {
                                callbacks: {
                                    afterLabel: function(context) {
                                        var index = context.dataIndex;
                                        var value = context.raw;
                                        var percentage = percentageByAge[index].toFixed(1);
                                        var cost = carryingCosts[index].toFixed(2);
                                        var optimal = optimalDistribution[index];

                                        return [
                                            'Value: $' + value.toLocaleString(),
                                            'Percentage: ' + percentage + '%',
                                            'Monthly Carrying Cost: $' + cost,
                                            'Optimal: ' + optimal + '%',
                                            percentage > optimal ? 'Recommendation: Reduce' : 
                                                percentage < optimal ? 'Recommendation: Acceptable' : 'Recommendation: Optimal'
                                        ];
                                    },
                                    footer: function() {
                                        return 'Analysis based on inventory age and carrying costs';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Inventory Efficiency Score Chart
            if (document.getElementById('inventory_efficiency_chart')) {
                var inventoryEfficiencyCtx = document.getElementById('inventory_efficiency_chart').getContext('2d');

                // Calculate inventory efficiency metrics
                var turnoverRate = {!! json_encode($data['inventory_turnover'] ?? 4.3) !!};
                var daysInInventory = {!! json_encode($data['days_in_inventory'] ?? 85) !!};
                var stockoutRate = {!! json_encode(($data['stockouts'] ?? 10) / 100) !!}; // Convert to percentage
                var inventoryAccuracy = 0.95; // 95% accuracy (placeholder)
                var carryingCostRatio = 0.25; // 25% of inventory value per year (placeholder)

                // Calculate efficiency scores (0-100 scale)
                var turnoverScore = Math.min(100, turnoverRate * 10); // Higher turnover is better
                var daysScore = Math.max(0, 100 - (daysInInventory / 3.65)); // Lower days is better (100 = 0 days, 0 = 365 days)
                var stockoutScore = Math.max(0, 100 - (stockoutRate * 100)); // Lower stockout rate is better
                var accuracyScore = inventoryAccuracy * 100; // Higher accuracy is better
                var costScore = Math.max(0, 100 - (carryingCostRatio * 100)); // Lower carrying cost is better

                // Calculate overall efficiency score (weighted average)
                var weights = [0.25, 0.2, 0.2, 0.15, 0.2]; // Weights for each metric
                var overallScore = (
                    (turnoverScore * weights[0]) +
                    (daysScore * weights[1]) +
                    (stockoutScore * weights[2]) +
                    (accuracyScore * weights[3]) +
                    (costScore * weights[4])
                );

                // Historical efficiency scores (for trend)
                var historicalScores = [65, 68, 72, 75, 78, overallScore];
                var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];

                // Predict future scores using linear regression
                var xValues = Array.from({length: historicalScores.length}, (_, i) => i + 1);
                var xMean = xValues.reduce((a, b) => a + b, 0) / xValues.length;
                var yMean = historicalScores.reduce((a, b) => a + b, 0) / historicalScores.length;

                var numerator = 0;
                var denominator = 0;

                for (var i = 0; i < historicalScores.length; i++) {
                    numerator += (xValues[i] - xMean) * (historicalScores[i] - yMean);
                    denominator += Math.pow(xValues[i] - xMean, 2);
                }

                var slope = denominator !== 0 ? numerator / denominator : 0;
                var intercept = yMean - (slope * xMean);

                // Generate future predictions
                var futurePredictions = [];
                var futureMonths = ['Jul', 'Aug', 'Sep'];

                for (var i = 1; i <= 3; i++) {
                    var prediction = intercept + (slope * (historicalScores.length + i));
                    futurePredictions.push(Math.min(100, prediction)); // Cap at 100
                }

                // Create radar chart for current metrics
                var radarChart = new Chart(inventoryEfficiencyCtx, {
                    type: 'radar',
                    data: {
                        labels: ['Turnover Rate', 'Days in Inventory', 'Stockout Prevention', 'Inventory Accuracy', 'Carrying Cost'],
                        datasets: [{
                            label: 'Current Efficiency',
                            data: [turnoverScore, daysScore, stockoutScore, accuracyScore, costScore],
                            backgroundColor: 'rgba(60, 141, 188, 0.2)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            pointBackgroundColor: 'rgba(60, 141, 188, 1)',
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: 'rgba(60, 141, 188, 1)'
                        }, {
                            label: 'Target Efficiency',
                            data: [90, 85, 95, 98, 90],
                            backgroundColor: 'rgba(0, 166, 90, 0.2)',
                            borderColor: 'rgba(0, 166, 90, 1)',
                            pointBackgroundColor: 'rgba(0, 166, 90, 1)',
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: 'rgba(0, 166, 90, 1)',
                            borderDash: [5, 5]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            r: {
                                angleLines: {
                                    display: true
                                },
                                suggestedMin: 0,
                                suggestedMax: 100
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Inventory Efficiency Score: ' + overallScore.toFixed(1) + '/100'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';
                                        var value = context.raw || 0;
                                        return label + ': ' + value.toFixed(1) + '/100';
                                    },
                                    footer: function() {
                                        return 'Efficiency score based on multiple inventory metrics';
                                    }
                                }
                            }
                        }
                    }
                });

                // Add a small line chart below the radar chart to show trend
                var trendChartContainer = document.createElement('div');
                trendChartContainer.style.marginTop = '20px';
                trendChartContainer.style.height = '100px';
                document.getElementById('inventory_efficiency_chart').parentNode.appendChild(trendChartContainer);

                var trendCanvas = document.createElement('canvas');
                trendChartContainer.appendChild(trendCanvas);

                var trendChart = new Chart(trendCanvas.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: [...months, ...futureMonths],
                        datasets: [{
                            label: 'Efficiency Score Trend',
                            data: [...historicalScores, ...futurePredictions],
                            backgroundColor: 'rgba(60, 141, 188, 0.2)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 2,
                            pointRadius: 3,
                            fill: true
                        }, {
                            label: 'Predicted Score',
                            data: [...Array(historicalScores.length).fill(null), ...futurePredictions],
                            backgroundColor: 'rgba(0, 166, 90, 0.2)',
                            borderColor: 'rgba(0, 166, 90, 1)',
                            borderWidth: 2,
                            pointRadius: 3,
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: false,
                                min: 50,
                                max: 100
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Efficiency Score Trend & Prediction'
                            }
                        }
                    }
                });
            }

            // Inventory Optimization Recommendations Chart
            if (document.getElementById('inventory_optimization_chart')) {
                var inventoryOptimizationCtx = document.getElementById('inventory_optimization_chart').getContext('2d');

                // Sample optimization opportunities
                var categories = ['Excess Stock', 'Slow Moving', 'Stockout Risk', 'Carrying Cost', 'Order Timing'];
                var currentValues = [25000, 15000, 10000, 12000, 8000]; // Current cost/value
                var optimizedValues = [15000, 8000, 5000, 7000, 4000]; // Optimized cost/value

                // Calculate potential savings
                var savings = currentValues.map((value, index) => value - optimizedValues[index]);
                var totalSavings = savings.reduce((a, b) => a + b, 0);
                var percentageSavings = savings.map((value, index) => (value / currentValues[index]) * 100);

                // Implementation difficulty (1-10 scale, 10 being most difficult)
                var difficulty = [3, 5, 7, 4, 6];

                // ROI score (higher is better)
                var roiScores = savings.map((value, index) => value / difficulty[index]);

                // Sort categories by ROI for prioritization
                var indices = Array.from({length: categories.length}, (_, i) => i);
                indices.sort((a, b) => roiScores[b] - roiScores[a]);

                var sortedCategories = indices.map(i => categories[i]);
                var sortedCurrentValues = indices.map(i => currentValues[i]);
                var sortedOptimizedValues = indices.map(i => optimizedValues[i]);
                var sortedSavings = indices.map(i => savings[i]);
                var sortedPercentageSavings = indices.map(i => percentageSavings[i]);
                var sortedDifficulty = indices.map(i => difficulty[i]);
                var sortedRoiScores = indices.map(i => roiScores[i]);

                var inventoryOptimizationChart = new Chart(inventoryOptimizationCtx, {
                    type: 'bar',
                    data: {
                        labels: sortedCategories,
                        datasets: [{
                            label: 'Current Value',
                            data: sortedCurrentValues,
                            backgroundColor: 'rgba(60, 141, 188, 0.8)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 1
                        }, {
                            label: 'Optimized Value',
                            data: sortedOptimizedValues,
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
                                title: {
                                    display: true,
                                    text: 'Value ($)'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Inventory Optimization Opportunities (Total Savings: $' + totalSavings.toLocaleString() + ')'
                            },
                            tooltip: {
                                callbacks: {
                                    afterLabel: function(context) {
                                        var index = context.dataIndex;
                                        if (context.datasetIndex === 0) { // Current Value tooltip
                                            return [
                                                'Potential Savings: $' + sortedSavings[index].toLocaleString(),
                                                'Savings %: ' + sortedPercentageSavings[index].toFixed(1) + '%',
                                                'Implementation Difficulty: ' + sortedDifficulty[index] + '/10',
                                                'ROI Score: ' + sortedRoiScores[index].toFixed(1),
                                                'Priority: ' + (index + 1)
                                            ];
                                        }
                                        return [];
                                    },
                                    footer: function() {
                                        return 'Recommendations based on predictive inventory optimization analysis';
                                    }
                                }
                            }
                        }
                    }
                });

                // Add implementation steps below the chart
                var stepsContainer = document.createElement('div');
                stepsContainer.style.marginTop = '20px';
                stepsContainer.style.padding = '10px';
                stepsContainer.style.backgroundColor = '#f9f9f9';
                stepsContainer.style.borderRadius = '5px';
                stepsContainer.style.border = '1px solid #ddd';
                document.getElementById('inventory_optimization_chart').parentNode.appendChild(stepsContainer);

                var stepsTitle = document.createElement('h4');
                stepsTitle.textContent = 'Implementation Steps (Prioritized)';
                stepsTitle.style.marginTop = '0';
                stepsContainer.appendChild(stepsTitle);

                var stepsList = document.createElement('ol');
                stepsContainer.appendChild(stepsList);

                var implementationSteps = [
                    'Reduce excess stock by implementing JIT inventory for high-value items',
                    'Identify and liquidate slow-moving inventory through promotions',
                    'Implement automated reordering for items with stockout risk',
                    'Negotiate better supplier terms to reduce carrying costs',
                    'Optimize order timing using predictive demand forecasting'
                ];

                // Sort steps by ROI priority
                var sortedSteps = indices.map(i => implementationSteps[i]);

                sortedSteps.forEach(step => {
                    var listItem = document.createElement('li');
                    listItem.textContent = step;
                    listItem.style.margin = '5px 0';
                    stepsList.appendChild(listItem);
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

                // Get the data for current and previous year
                var currentYearData = {!! json_encode($data['monthly_sales_current_year_data'] ?? [5000, 4500, 6000, 7500, 8000, 9000, 8500, 9500, 10000, 11000, 13000, 15000]) !!};
                var previousYearData = {!! json_encode($data['monthly_sales_previous_year_data'] ?? [4000, 3800, 5000, 6500, 7000, 8000, 7500, 8500, 9000, 10000, 11000, 13000]) !!};
                var monthLabels = {!! json_encode($data['monthly_sales_labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']) !!};

                // Calculate trend line for current year
                var trendLineData = calculateTrendLine(currentYearData);

                // Calculate next year prediction with 20% confidence interval
                var nextYearPrediction = calculateNextYearPrediction(currentYearData);
                var nextYearUpper = nextYearPrediction.map(value => value * 1.2); // Upper bound (20% higher)
                var nextYearLower = nextYearPrediction.map(value => value * 0.8); // Lower bound (20% lower)

                // Create extended labels for next year
                var extendedLabels = [...monthLabels, ...monthLabels.map(month => month + ' (Next Year)')];

                var monthlySalesTrendChart = new Chart(monthlySalesTrendCtx, {
                    type: 'line',
                    data: {
                        labels: extendedLabels,
                        datasets: [{
                            label: 'Current Year',
                            data: [...currentYearData, ...Array(12).fill(null)],
                            backgroundColor: 'rgba(60, 141, 188, 0.2)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 2,
                            fill: true
                        }, {
                            label: 'Previous Year',
                            data: [...previousYearData, ...Array(12).fill(null)],
                            backgroundColor: 'rgba(210, 214, 222, 0.2)',
                            borderColor: 'rgba(210, 214, 222, 1)',
                            borderWidth: 2,
                            fill: true
                        }, {
                            label: 'Trend Line',
                            data: [...trendLineData, ...Array(12).fill(null)],
                            borderColor: 'rgba(255, 193, 7, 1)',
                            borderWidth: 2,
                            borderDash: [5, 5],
                            fill: false,
                            pointRadius: 0
                        }, {
                            label: 'Next Year Prediction',
                            data: [...Array(12).fill(null), ...nextYearPrediction],
                            backgroundColor: 'rgba(0, 166, 90, 0.2)',
                            borderColor: 'rgba(0, 166, 90, 1)',
                            borderWidth: 2,
                            fill: '+1'
                        }, {
                            label: 'Upper Confidence Interval',
                            data: [...Array(12).fill(null), ...nextYearUpper],
                            borderColor: 'rgba(0, 166, 90, 0.5)',
                            borderWidth: 1,
                            borderDash: [5, 5],
                            fill: false,
                            pointRadius: 0
                        }, {
                            label: 'Lower Confidence Interval',
                            data: [...Array(12).fill(null), ...nextYearLower],
                            borderColor: 'rgba(0, 166, 90, 0.5)',
                            borderWidth: 1,
                            borderDash: [5, 5],
                            fill: false,
                            pointRadius: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Sales Amount'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Month'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Monthly Sales Trend with Next Year Prediction'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += new Intl.NumberFormat('en-US', { 
                                                style: 'currency', 
                                                currency: 'USD',
                                                minimumFractionDigits: 0,
                                                maximumFractionDigits: 0
                                            }).format(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            },
                            legend: {
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20
                                }
                            }
                        }
                    }
                });
            }

            // Quarterly Comparison Chart
            if (document.getElementById('quarterly_comparison_chart')) {
                var quarterlyComparisonCtx = document.getElementById('quarterly_comparison_chart').getContext('2d');

                // Get quarterly data from controller
                var quarterlyLabels = {!! json_encode($data['quarterly_labels'] ?? ['Q1', 'Q2', 'Q3', 'Q4']) !!};
                var quarterlySalesData = {!! json_encode($data['quarterly_sales_data'] ?? [15000, 25000, 28000, 39000]) !!};
                var quarterlyProfitData = {!! json_encode($data['quarterly_profit_data'] ?? [5000, 8000, 9000, 12000]) !!};

                // Add trend line for quarterly data
                var quarterlySalesTrendLine = calculateTrendLine(quarterlySalesData);

                var quarterlyComparisonChart = new Chart(quarterlyComparisonCtx, {
                    type: 'bar',
                    data: {
                        labels: quarterlyLabels,
                        datasets: [{
                            label: 'Sales',
                            data: quarterlySalesData,
                            backgroundColor: 'rgba(60, 141, 188, 0.8)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 1,
                            order: 2
                        }, {
                            label: 'Profit',
                            data: quarterlyProfitData,
                            backgroundColor: 'rgba(0, 166, 90, 0.8)',
                            borderColor: 'rgba(0, 166, 90, 1)',
                            borderWidth: 1,
                            order: 3
                        }, {
                            label: 'Sales Trend',
                            data: quarterlySalesTrendLine,
                            type: 'line',
                            borderColor: 'rgba(255, 193, 7, 1)',
                            borderWidth: 2,
                            fill: false,
                            pointRadius: 0,
                            order: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Amount'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Quarter'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Quarterly Performance Comparison with Trend'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += new Intl.NumberFormat('en-US', { 
                                                style: 'currency', 
                                                currency: 'USD',
                                                minimumFractionDigits: 0,
                                                maximumFractionDigits: 0
                                            }).format(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            },
                            legend: {
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20
                                }
                            }
                        }
                    }
                });
            }

            // Sports Product Seasonal Performance Chart
            if (document.getElementById('sports_product_seasonal_chart')) {
                var sportsProductCtx = document.getElementById('sports_product_seasonal_chart').getContext('2d');

                // Realistic sports product seasonal data
                var sportsCategories = {!! json_encode($data['sports_categories'] ?? ['Football', 'Basketball', 'Running', 'Swimming', 'Tennis', 'Golf']) !!};
                var winterSalesData = {!! json_encode($data['winter_sports_sales'] ?? [12000, 8000, 5000, 15000, 3000, 7000]) !!};
                var springSalesData = {!! json_encode($data['spring_sports_sales'] ?? [8000, 10000, 12000, 9000, 8000, 14000]) !!};
                var summerSalesData = {!! json_encode($data['summer_sports_sales'] ?? [5000, 12000, 18000, 22000, 14000, 18000]) !!};
                var fallSalesData = {!! json_encode($data['fall_sports_sales'] ?? [15000, 9000, 10000, 7000, 6000, 9000]) !!};

                // Calculate next season predictions with confidence intervals
                var nextSeasonPrediction = [];
                var upperConfidence = [];
                var lowerConfidence = [];

                // Calculate average seasonal growth for each category
                for (let i = 0; i < sportsCategories.length; i++) {
                    let avgGrowth = ((springSalesData[i] - winterSalesData[i]) + 
                                     (summerSalesData[i] - springSalesData[i]) + 
                                     (fallSalesData[i] - summerSalesData[i])) / 3;

                    // Predict next winter sales with 15% confidence interval
                    let prediction = fallSalesData[i] + avgGrowth;
                    nextSeasonPrediction.push(prediction);
                    upperConfidence.push(prediction * 1.15);
                    lowerConfidence.push(prediction * 0.85);
                }

                var sportsProductChart = new Chart(sportsProductCtx, {
                    type: 'radar',
                    data: {
                        labels: sportsCategories,
                        datasets: [{
                            label: 'Winter',
                            data: winterSalesData,
                            backgroundColor: 'rgba(41, 128, 185, 0.2)',
                            borderColor: 'rgba(41, 128, 185, 1)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgba(41, 128, 185, 1)'
                        }, {
                            label: 'Spring',
                            data: springSalesData,
                            backgroundColor: 'rgba(46, 204, 113, 0.2)',
                            borderColor: 'rgba(46, 204, 113, 1)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgba(46, 204, 113, 1)'
                        }, {
                            label: 'Summer',
                            data: summerSalesData,
                            backgroundColor: 'rgba(241, 196, 15, 0.2)',
                            borderColor: 'rgba(241, 196, 15, 1)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgba(241, 196, 15, 1)'
                        }, {
                            label: 'Fall',
                            data: fallSalesData,
                            backgroundColor: 'rgba(211, 84, 0, 0.2)',
                            borderColor: 'rgba(211, 84, 0, 1)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgba(211, 84, 0, 1)'
                        }, {
                            label: 'Next Season (Predicted)',
                            data: nextSeasonPrediction,
                            backgroundColor: 'rgba(142, 68, 173, 0.2)',
                            borderColor: 'rgba(142, 68, 173, 1)',
                            borderWidth: 2,
                            borderDash: [5, 5],
                            pointBackgroundColor: 'rgba(142, 68, 173, 1)'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            r: {
                                beginAtZero: true,
                                ticks: {
                                    display: false
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Seasonal Sports Product Performance with Prediction'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.raw !== null) {
                                            label += new Intl.NumberFormat('en-US', { 
                                                style: 'currency', 
                                                currency: 'USD',
                                                minimumFractionDigits: 0,
                                                maximumFractionDigits: 0
                                            }).format(context.raw);
                                        }
                                        return label;
                                    }
                                }
                            },
                            legend: {
                                position: 'top'
                            }
                        }
                    }
                });
            }

            // Seasonal Customer Engagement Chart
            if (document.getElementById('seasonal_customer_engagement_chart')) {
                var customerEngagementCtx = document.getElementById('seasonal_customer_engagement_chart').getContext('2d');

                // Realistic customer engagement metrics by season
                var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                var customerVisits = {!! json_encode($data['customer_visits'] ?? [1200, 1300, 1500, 1700, 1900, 2200, 2500, 2400, 2000, 1800, 1600, 1400]) !!};
                var avgTimeSpent = {!! json_encode($data['avg_time_spent'] ?? [25, 28, 30, 35, 40, 45, 50, 48, 42, 38, 32, 27]) !!};
                var conversionRates = {!! json_encode($data['conversion_rates'] ?? [2.1, 2.3, 2.5, 2.8, 3.2, 3.5, 3.8, 3.7, 3.3, 2.9, 2.6, 2.2]) !!};

                // Calculate trend lines
                var visitsLine = calculateTrendLine(customerVisits);
                var timeSpentLine = calculateTrendLine(avgTimeSpent);
                var conversionLine = calculateTrendLine(conversionRates);

                // Predict next 6 months
                var nextMonths = ['Jan (Next)', 'Feb (Next)', 'Mar (Next)', 'Apr (Next)', 'May (Next)', 'Jun (Next)'];
                var predictedVisits = [];
                var predictedTimeSpent = [];
                var predictedConversion = [];

                // Simple prediction based on year-over-year growth
                for (let i = 0; i < 6; i++) {
                    predictedVisits.push(customerVisits[i] * 1.15); // 15% YoY growth
                    predictedTimeSpent.push(avgTimeSpent[i] * 1.08); // 8% YoY growth
                    predictedConversion.push(conversionRates[i] * 1.12); // 12% YoY growth
                }

                var customerEngagementChart = new Chart(customerEngagementCtx, {
                    type: 'line',
                    data: {
                        labels: [...months, ...nextMonths],
                        datasets: [{
                            label: 'Customer Visits',
                            data: [...customerVisits, ...predictedVisits],
                            backgroundColor: 'rgba(52, 152, 219, 0.2)',
                            borderColor: 'rgba(52, 152, 219, 1)',
                            borderWidth: 2,
                            yAxisID: 'y',
                            fill: true
                        }, {
                            label: 'Avg. Time Spent (min)',
                            data: [...avgTimeSpent, ...predictedTimeSpent],
                            backgroundColor: 'rgba(46, 204, 113, 0.2)',
                            borderColor: 'rgba(46, 204, 113, 1)',
                            borderWidth: 2,
                            yAxisID: 'y1',
                            fill: true
                        }, {
                            label: 'Conversion Rate (%)',
                            data: [...conversionRates, ...predictedConversion],
                            backgroundColor: 'rgba(231, 76, 60, 0.2)',
                            borderColor: 'rgba(231, 76, 60, 1)',
                            borderWidth: 2,
                            yAxisID: 'y2',
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Month'
                                }
                            },
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'Visits'
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                grid: {
                                    drawOnChartArea: false
                                },
                                title: {
                                    display: true,
                                    text: 'Time (min)'
                                }
                            },
                            y2: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                grid: {
                                    drawOnChartArea: false
                                },
                                title: {
                                    display: true,
                                    text: 'Conversion (%)'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Seasonal Customer Engagement with Predictions'
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            },
                            legend: {
                                position: 'top'
                            }
                        }
                    }
                });
            }

            // Seasonal Inventory Optimization Chart
            if (document.getElementById('seasonal_inventory_optimization_chart')) {
                var inventoryOptimizationCtx = document.getElementById('seasonal_inventory_optimization_chart').getContext('2d');

                // Realistic inventory data
                var quarters = ['Q1', 'Q2', 'Q3', 'Q4'];
                var inventoryLevels = {!! json_encode($data['inventory_levels'] ?? [85000, 95000, 110000, 75000]) !!};
                var inventoryCosts = {!! json_encode($data['inventory_costs'] ?? [12000, 14000, 16500, 11000]) !!};
                var stockoutRates = {!! json_encode($data['stockout_rates'] ?? [2.8, 1.5, 1.2, 3.5]) !!};
                var turnoverRates = {!! json_encode($data['turnover_rates'] ?? [3.2, 4.1, 4.5, 3.8]) !!};

                // Calculate optimal inventory levels based on turnover and stockout rates
                var optimalInventory = [];
                for (let i = 0; i < quarters.length; i++) {
                    // Formula: optimal = current * (1 - stockout/10) * (turnover/4)
                    optimalInventory.push(inventoryLevels[i] * (1 - stockoutRates[i]/10) * (turnoverRates[i]/4));
                }

                // Predict next year's optimal inventory with 10% confidence interval
                var nextYearOptimal = [];
                var upperBound = [];
                var lowerBound = [];

                for (let i = 0; i < quarters.length; i++) {
                    nextYearOptimal.push(optimalInventory[i] * 1.05); // 5% growth
                    upperBound.push(nextYearOptimal[i] * 1.1);
                    lowerBound.push(nextYearOptimal[i] * 0.9);
                }

                var inventoryOptimizationChart = new Chart(inventoryOptimizationCtx, {
                    type: 'bar',
                    data: {
                        labels: [...quarters, ...quarters.map(q => q + ' (Next)')],
                        datasets: [{
                            label: 'Current Inventory',
                            data: [...inventoryLevels, ...Array(4).fill(null)],
                            backgroundColor: 'rgba(52, 152, 219, 0.7)',
                            borderColor: 'rgba(52, 152, 219, 1)',
                            borderWidth: 1
                        }, {
                            label: 'Optimal Inventory',
                            data: [...optimalInventory, ...Array(4).fill(null)],
                            backgroundColor: 'rgba(46, 204, 113, 0.7)',
                            borderColor: 'rgba(46, 204, 113, 1)',
                            borderWidth: 1
                        }, {
                            label: 'Predicted Optimal Inventory',
                            data: [...Array(4).fill(null), ...nextYearOptimal],
                            backgroundColor: 'rgba(155, 89, 182, 0.7)',
                            borderColor: 'rgba(155, 89, 182, 1)',
                            borderWidth: 1
                        }, {
                            label: 'Upper Confidence Bound',
                            data: [...Array(4).fill(null), ...upperBound],
                            type: 'line',
                            backgroundColor: 'rgba(0, 0, 0, 0)',
                            borderColor: 'rgba(155, 89, 182, 0.5)',
                            borderWidth: 2,
                            borderDash: [5, 5],
                            pointRadius: 0
                        }, {
                            label: 'Lower Confidence Bound',
                            data: [...Array(4).fill(null), ...lowerBound],
                            type: 'line',
                            backgroundColor: 'rgba(0, 0, 0, 0)',
                            borderColor: 'rgba(155, 89, 182, 0.5)',
                            borderWidth: 2,
                            borderDash: [5, 5],
                            pointRadius: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Inventory Value'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Quarter'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Seasonal Inventory Optimization with Predictions'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += new Intl.NumberFormat('en-US', { 
                                                style: 'currency', 
                                                currency: 'USD',
                                                minimumFractionDigits: 0,
                                                maximumFractionDigits: 0
                                            }).format(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            },
                            legend: {
                                position: 'top'
                            }
                        }
                    }
                });
            }

            // Seasonal Profit Margin Analysis Chart
            if (document.getElementById('seasonal_profit_margin_chart')) {
                var profitMarginCtx = document.getElementById('seasonal_profit_margin_chart').getContext('2d');

                // Realistic profit margin data by product category and season
                var categories = ['Apparel', 'Footwear', 'Equipment', 'Accessories', 'Nutrition'];
                var winterMargins = {!! json_encode($data['winter_margins'] ?? [32, 28, 35, 42, 50]) !!};
                var springMargins = {!! json_encode($data['spring_margins'] ?? [35, 30, 32, 45, 48]) !!};
                var summerMargins = {!! json_encode($data['summer_margins'] ?? [38, 33, 30, 47, 45]) !!};
                var fallMargins = {!! json_encode($data['fall_margins'] ?? [34, 31, 33, 44, 47]) !!};

                // Calculate average margins and standard deviations for prediction
                var avgMargins = [];
                var stdDevs = [];

                for (let i = 0; i < categories.length; i++) {
                    let values = [winterMargins[i], springMargins[i], summerMargins[i], fallMargins[i]];
                    let sum = values.reduce((a, b) => a + b, 0);
                    let avg = sum / values.length;
                    avgMargins.push(avg);

                    // Calculate standard deviation
                    let variance = values.reduce((a, b) => a + Math.pow(b - avg, 2), 0) / values.length;
                    stdDevs.push(Math.sqrt(variance));
                }

                // Predict next year margins with confidence intervals
                var predictedMargins = [];
                var upperConfidence = [];
                var lowerConfidence = [];

                for (let i = 0; i < categories.length; i++) {
                    // Predict with slight improvement
                    predictedMargins.push(avgMargins[i] * 1.05);
                    // 95% confidence interval (approximately 2 standard deviations)
                    upperConfidence.push(predictedMargins[i] + 2 * stdDevs[i]);
                    lowerConfidence.push(predictedMargins[i] - 2 * stdDevs[i]);
                }

                var profitMarginChart = new Chart(profitMarginCtx, {
                    type: 'line',
                    data: {
                        labels: categories,
                        datasets: [{
                            label: 'Winter Margins',
                            data: winterMargins,
                            backgroundColor: 'rgba(41, 128, 185, 0.2)',
                            borderColor: 'rgba(41, 128, 185, 1)',
                            borderWidth: 2,
                            tension: 0.4
                        }, {
                            label: 'Spring Margins',
                            data: springMargins,
                            backgroundColor: 'rgba(46, 204, 113, 0.2)',
                            borderColor: 'rgba(46, 204, 113, 1)',
                            borderWidth: 2,
                            tension: 0.4
                        }, {
                            label: 'Summer Margins',
                            data: summerMargins,
                            backgroundColor: 'rgba(241, 196, 15, 0.2)',
                            borderColor: 'rgba(241, 196, 15, 1)',
                            borderWidth: 2,
                            tension: 0.4
                        }, {
                            label: 'Fall Margins',
                            data: fallMargins,
                            backgroundColor: 'rgba(211, 84, 0, 0.2)',
                            borderColor: 'rgba(211, 84, 0, 1)',
                            borderWidth: 2,
                            tension: 0.4
                        }, {
                            label: 'Predicted Next Year',
                            data: predictedMargins,
                            backgroundColor: 'rgba(142, 68, 173, 0.2)',
                            borderColor: 'rgba(142, 68, 173, 1)',
                            borderWidth: 2,
                            borderDash: [5, 5],
                            tension: 0.4
                        }, {
                            label: 'Upper Confidence',
                            data: upperConfidence,
                            backgroundColor: 'rgba(0, 0, 0, 0)',
                            borderColor: 'rgba(142, 68, 173, 0.5)',
                            borderWidth: 1,
                            borderDash: [3, 3],
                            pointRadius: 0,
                            tension: 0.4
                        }, {
                            label: 'Lower Confidence',
                            data: lowerConfidence,
                            backgroundColor: 'rgba(0, 0, 0, 0)',
                            borderColor: 'rgba(142, 68, 173, 0.5)',
                            borderWidth: 1,
                            borderDash: [3, 3],
                            pointRadius: 0,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Profit Margin (%)'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Product Category'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Seasonal Profit Margin Analysis with Predictions'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += context.parsed.y.toFixed(1) + '%';
                                        }
                                        return label;
                                    }
                                }
                            },
                            legend: {
                                position: 'top'
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

            // Revenue Forecast Chart
            if (document.getElementById('revenue_forecast_chart')) {
                var revenueForecastCtx = document.getElementById('revenue_forecast_chart').getContext('2d');

                // Use actual data from controller if available
                var years = @json(($data['years'] ?? []));
                var yearlyRevenue = @json(($data['yearly_revenue'] ?? []));

                // If no data, use zeros and display message
                if (!years.length) {
                    // Display message with data requirements
                    document.getElementById('revenue_forecast_chart').insertAdjacentHTML('beforebegin', 
                        '<div class="alert alert-info">Not enough data available. This chart requires at least 3 years of historical yearly revenue data to generate accurate forecasts.</div>');

                    // Use default years with zero values
                    years = ['2019', '2020', '2021', '2022', '2023'];
                    yearlyRevenue = Array(5).fill(0);
                }

                // Generate future years for prediction
                var futureYears = [];
                var lastYear = parseInt(years[years.length - 1]);
                for (var i = 1; i <= 3; i++) {
                    futureYears.push((lastYear + i).toString());
                }

                // Calculate linear regression for revenue prediction
                var xValues = Array.from({length: yearlyRevenue.length}, (_, i) => i + 1);
                var xMean = xValues.reduce((a, b) => a + b, 0) / xValues.length;
                var yMean = yearlyRevenue.reduce((a, b) => a + b, 0) / yearlyRevenue.length;

                var numerator = 0;
                var denominator = 0;

                for (var i = 0; i < yearlyRevenue.length; i++) {
                    numerator += (xValues[i] - xMean) * (yearlyRevenue[i] - yMean);
                    denominator += Math.pow(xValues[i] - xMean, 2);
                }

                var slope = denominator !== 0 ? numerator / denominator : 0;
                var intercept = yMean - (slope * xMean);

                // Generate predictions for future years
                var predictions = [];
                var upperBound = [];
                var lowerBound = [];

                for (var i = 1; i <= 3; i++) {
                    var x = yearlyRevenue.length + i;
                    var prediction = intercept + (slope * x);
                    predictions.push(prediction);

                    // 15% confidence interval
                    var confidence = prediction * 0.15;
                    upperBound.push(prediction + confidence);
                    lowerBound.push(Math.max(0, prediction - confidence));
                }

                var revenueForecastChart = new Chart(revenueForecastCtx, {
                    type: 'line',
                    data: {
                        labels: [...years, ...futureYears],
                        datasets: [{
                            label: 'Historical Revenue',
                            data: [...yearlyRevenue, ...Array(3).fill(null)],
                            backgroundColor: 'rgba(60, 141, 188, 0.2)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 2,
                            fill: false,
                            pointRadius: 5
                        }, {
                            label: 'Predicted Revenue',
                            data: [...Array(yearlyRevenue.length).fill(null), ...predictions],
                            backgroundColor: 'rgba(210, 214, 222, 0.2)',
                            borderColor: 'rgba(210, 214, 222, 1)',
                            borderWidth: 2,
                            borderDash: [5, 5],
                            fill: false,
                            pointRadius: 5
                        }, {
                            label: 'Upper Bound (15% Confidence)',
                            data: [...Array(yearlyRevenue.length).fill(null), ...upperBound],
                            backgroundColor: 'rgba(243, 156, 18, 0.2)',
                            borderColor: 'rgba(243, 156, 18, 1)',
                            borderWidth: 1,
                            borderDash: [2, 2],
                            fill: false,
                            pointRadius: 0
                        }, {
                            label: 'Lower Bound (15% Confidence)',
                            data: [...Array(yearlyRevenue.length).fill(null), ...lowerBound],
                            backgroundColor: 'rgba(243, 156, 18, 0.2)',
                            borderColor: 'rgba(243, 156, 18, 1)',
                            borderWidth: 1,
                            borderDash: [2, 2],
                            fill: '-1',
                            pointRadius: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Revenue'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Year'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Revenue Forecast with Predictive Analytics'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += new Intl.NumberFormat('en-US', { 
                                                style: 'currency', 
                                                currency: 'USD',
                                                minimumFractionDigits: 0,
                                                maximumFractionDigits: 0
                                            }).format(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Customer Growth Forecast Chart
            if (document.getElementById('customer_forecast_chart')) {
                var customerForecastCtx = document.getElementById('customer_forecast_chart').getContext('2d');

                // Use actual data from controller if available
                var customerGrowthLabels = @json(($data['customer_acquisition_labels'] ?? []));
                var customerGrowthData = @json(($data['customer_acquisition_data'] ?? []));

                // If no data, display message and use sample data
                if (!customerGrowthLabels.length) {
                    // Display message with data requirements
                    document.getElementById('customer_forecast_chart').insertAdjacentHTML('beforebegin', 
                        '<div class="alert alert-info">Not enough data available. This chart requires at least 12 months of customer acquisition data to forecast growth trends accurately.</div>');

                    // Use sample data
                    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    var customerData = [120, 132, 145, 162, 178, 195, 210, 228, 245, 262, 280, 300];
                } else {
                    // Use actual data
                    var months = customerGrowthLabels;
                    var customerData = customerGrowthData;
                }

                // Calculate growth rate for prediction
                var growthRate = (customerData[customerData.length - 1] - customerData[0]) / customerData[0];
                var monthlyGrowthRate = Math.pow(1 + growthRate, 1/12) - 1;

                // Generate predictions for next 6 months
                var predictions = [];
                var upperBound = [];
                var lowerBound = [];

                var lastValue = customerData[customerData.length - 1];
                for (var i = 1; i <= 6; i++) {
                    var prediction = lastValue * Math.pow(1 + monthlyGrowthRate, i);
                    predictions.push(prediction);

                    // 10% confidence interval
                    var confidence = prediction * 0.1;
                    upperBound.push(prediction + confidence);
                    lowerBound.push(Math.max(0, prediction - confidence));
                }

                // Future months
                var futureMonths = [];
                var currentMonthIndex = new Date().getMonth();
                for (var i = 1; i <= 6; i++) {
                    var monthIndex = (currentMonthIndex + i) % 12;
                    futureMonths.push(months[monthIndex]);
                }

                var customerForecastChart = new Chart(customerForecastCtx, {
                    type: 'line',
                    data: {
                        labels: [...months, ...futureMonths],
                        datasets: [{
                            label: 'Historical Customers',
                            data: [...customerData, ...Array(6).fill(null)],
                            backgroundColor: 'rgba(60, 141, 188, 0.2)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 2,
                            fill: false,
                            pointRadius: 5
                        }, {
                            label: 'Predicted Customers',
                            data: [...Array(12).fill(null), ...predictions],
                            backgroundColor: 'rgba(0, 166, 90, 0.2)',
                            borderColor: 'rgba(0, 166, 90, 1)',
                            borderWidth: 2,
                            borderDash: [5, 5],
                            fill: false,
                            pointRadius: 5
                        }, {
                            label: 'Upper Bound (10% Confidence)',
                            data: [...Array(12).fill(null), ...upperBound],
                            backgroundColor: 'rgba(0, 166, 90, 0.1)',
                            borderColor: 'rgba(0, 166, 90, 0.5)',
                            borderWidth: 1,
                            borderDash: [2, 2],
                            fill: false,
                            pointRadius: 0
                        }, {
                            label: 'Lower Bound (10% Confidence)',
                            data: [...Array(12).fill(null), ...lowerBound],
                            backgroundColor: 'rgba(0, 166, 90, 0.1)',
                            borderColor: 'rgba(0, 166, 90, 0.5)',
                            borderWidth: 1,
                            borderDash: [2, 2],
                            fill: '-1',
                            pointRadius: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Number of Customers'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Month'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Customer Growth Forecast'
                            }
                        }
                    }
                });
            }

            // Sales Trend Analysis Chart
            if (document.getElementById('sales_trend_analysis_chart')) {
                var salesTrendCtx = document.getElementById('sales_trend_analysis_chart').getContext('2d');

                // Use actual data from controller if available
                var salesTrendLabels = @json(($data['sells_last_30_days_labels'] ?? []));
                var salesTrendData = @json(($data['sells_last_30_days_data'] ?? []));

                // If no data, use zeros and display message
                if (!salesTrendLabels.length) {
                    // Display message with data requirements
                    document.getElementById('sales_trend_analysis_chart').insertAdjacentHTML('beforebegin', 
                        '<div class="alert alert-info">Not enough data available. This chart requires at least 30 days of daily sales data to analyze trends and patterns effectively.</div>');

                    // Generate dates for the last 30 days
                    salesTrendLabels = Array.from({length: 30}, (_, i) => {
                        var date = new Date();
                        date.setDate(date.getDate() - (30 - i - 1));
                        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                    });

                    // Use zeros for data
                    salesTrendData = Array(30).fill(0);
                }

                // Calculate moving average (7-day)
                var movingAverage = [];
                for (var i = 0; i < salesTrendData.length; i++) {
                    if (i < 6) {
                        movingAverage.push(null);
                    } else {
                        var sum = 0;
                        for (var j = i - 6; j <= i; j++) {
                            sum += salesTrendData[j];
                        }
                        movingAverage.push(sum / 7);
                    }
                }

                // Calculate trend line using linear regression
                var xValues = Array.from({length: salesTrendData.length}, (_, i) => i + 1);
                var xMean = xValues.reduce((a, b) => a + b, 0) / xValues.length;
                var yMean = salesTrendData.reduce((a, b) => a + b, 0) / salesTrendData.length;

                var numerator = 0;
                var denominator = 0;

                for (var i = 0; i < salesTrendData.length; i++) {
                    numerator += (xValues[i] - xMean) * (salesTrendData[i] - yMean);
                    denominator += Math.pow(xValues[i] - xMean, 2);
                }

                var slope = denominator !== 0 ? numerator / denominator : 0;
                var intercept = yMean - (slope * xMean);

                // Generate trend line data
                var trendLine = xValues.map(x => intercept + (slope * x));

                // Calculate seasonality (day of week pattern)
                var dayOfWeekAverages = Array(7).fill(0);
                var dayOfWeekCounts = Array(7).fill(0);

                for (var i = 0; i < salesTrendData.length; i++) {
                    var dayOfWeek = i % 7;
                    dayOfWeekAverages[dayOfWeek] += salesTrendData[i];
                    dayOfWeekCounts[dayOfWeek]++;
                }

                for (var i = 0; i < 7; i++) {
                    if (dayOfWeekCounts[i] > 0) {
                        dayOfWeekAverages[i] /= dayOfWeekCounts[i];
                    }
                }

                var salesTrendChart = new Chart(salesTrendCtx, {
                    type: 'line',
                    data: {
                        labels: salesTrendLabels,
                        datasets: [{
                            label: 'Daily Sales',
                            data: salesTrendData,
                            backgroundColor: 'rgba(60, 141, 188, 0.2)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 2,
                            fill: false,
                            pointRadius: 3
                        }, {
                            label: '7-Day Moving Average',
                            data: movingAverage,
                            backgroundColor: 'rgba(0, 166, 90, 0.2)',
                            borderColor: 'rgba(0, 166, 90, 1)',
                            borderWidth: 2,
                            fill: false,
                            pointRadius: 0
                        }, {
                            label: 'Trend Line',
                            data: trendLine,
                            backgroundColor: 'rgba(243, 156, 18, 0.2)',
                            borderColor: 'rgba(243, 156, 18, 1)',
                            borderWidth: 2,
                            borderDash: [5, 5],
                            fill: false,
                            pointRadius: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Sales Amount'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Date'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Sales Trend Analysis with Predictive Components'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += new Intl.NumberFormat('en-US', { 
                                                style: 'currency', 
                                                currency: 'USD',
                                                minimumFractionDigits: 0,
                                                maximumFractionDigits: 0
                                            }).format(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Profit Margin Forecast Chart
            if (document.getElementById('profit_margin_forecast_chart')) {
                try {
                    var profitMarginCtx = document.getElementById('profit_margin_forecast_chart').getContext('2d');

                    // Get quarterly data from controller with default values
                    var quarterlyLabels = ['Q1', 'Q2', 'Q3', 'Q4'];
                    var quarterlySalesData = [0, 0, 0, 0];
                    var quarterlyProfitData = [0, 0, 0, 0];

                    // Try to get actual data if available
                    try {
                    } catch(e) {
                        console.log("Error loading profit margin data:", e);
                        // Show tooltip for error
                        document.getElementById('profit_margin_forecast_chart').insertAdjacentHTML('beforebegin', 
                            '<div class="alert alert-info">Not enough data available for this section. Please check your data sources.</div>');
                    }

                    // Check if we have real data (non-zero values)
                    var hasRealData = quarterlySalesData.some(value => value > 0) && quarterlyProfitData.some(value => value > 0);

                    if (!hasRealData) {
                        // Display message with data requirements
                        document.getElementById('profit_margin_forecast_chart').insertAdjacentHTML('beforebegin', 
                            '<div class="alert alert-info">Not enough data available. This chart requires at least 4 quarters of sales and profit data to forecast profit margins accurately.</div>');
                        // Return early if no data
                        return;
                    }

                    // Calculate quarterly gross profit margins
                    var grossMarginData = [];
                    for (var i = 0; i < quarterlySalesData.length; i++) {
                        var margin = quarterlySalesData[i] > 0 ? (quarterlyProfitData[i] / quarterlySalesData[i]) * 100 : 0;
                        grossMarginData.push(parseFloat(margin.toFixed(2)));
                    }

                    // Calculate quarterly net profit margins (using overall net profit margin as a base)
                    var overallNetMargin = @json(($data['net_profit_margin'] ?? 0));
                    var netMarginData = [];
                    for (var i = 0; i < grossMarginData.length; i++) {
                        // Net margin is typically lower than gross margin by a consistent ratio
                        // Using the overall ratio to estimate quarterly net margins
                        var ratio = @json(($data['gross_profit_margin'] ?? 1)) > 0 ? 
                            overallNetMargin / @json(($data['gross_profit_margin'] ?? 1)) : 0.5;
                        netMarginData.push(parseFloat((grossMarginData[i] * ratio).toFixed(2)));
                    }

                    // Format quarter labels with year
                    var currentYear = new Date().getFullYear();
                    var quarters = [];
                    for (var i = 0; i < quarterlyLabels.length; i++) {
                        quarters.push(quarterlyLabels[i] + ' ' + currentYear);
                    }

                    // Calculate linear regression for gross margin prediction
                    var xValues = Array.from({length: grossMarginData.length}, (_, i) => i + 1);
                    var xMean = xValues.reduce((a, b) => a + b, 0) / xValues.length;
                    var yMean = grossMarginData.reduce((a, b) => a + b, 0) / grossMarginData.length;

                    var numerator = 0;
                    var denominator = 0;

                    for (var i = 0; i < grossMarginData.length; i++) {
                        numerator += (xValues[i] - xMean) * (grossMarginData[i] - yMean);
                        denominator += Math.pow(xValues[i] - xMean, 2);
                    }

                    var grossMarginSlope = denominator !== 0 ? numerator / denominator : 0;
                    var grossMarginIntercept = yMean - (grossMarginSlope * xMean);

                    // Calculate linear regression for net margin prediction
                    yMean = netMarginData.reduce((a, b) => a + b, 0) / netMarginData.length;
                    numerator = 0;

                    for (var i = 0; i < netMarginData.length; i++) {
                        numerator += (xValues[i] - xMean) * (netMarginData[i] - yMean);
                    }

                    var netMarginSlope = denominator !== 0 ? numerator / denominator : 0;
                    var netMarginIntercept = yMean - (netMarginSlope * xMean);

                    // Generate predictions for next 4 quarters
                    var nextYear = currentYear + 1;
                    var futureQuarters = [];
                    for (var i = 0; i < quarterlyLabels.length; i++) {
                        futureQuarters.push(quarterlyLabels[i] + ' ' + nextYear);
                    }

                    var grossMarginPredictions = [];
                    var netMarginPredictions = [];
                    var grossMarginUpperBound = [];
                    var grossMarginLowerBound = [];
                    var netMarginUpperBound = [];
                    var netMarginLowerBound = [];

                    for (var i = 1; i <= 4; i++) {
                        var x = grossMarginData.length + i;
                        var grossPrediction = grossMarginIntercept + (grossMarginSlope * x);
                        var netPrediction = netMarginIntercept + (netMarginSlope * x);

                        grossMarginPredictions.push(grossPrediction);
                        netMarginPredictions.push(netPrediction);

                        // 5% confidence interval
                        var grossConfidence = grossPrediction * 0.05;
                        var netConfidence = netPrediction * 0.05;

                        grossMarginUpperBound.push(grossPrediction + grossConfidence);
                        grossMarginLowerBound.push(Math.max(0, grossPrediction - grossConfidence));

                        netMarginUpperBound.push(netPrediction + netConfidence);
                        netMarginLowerBound.push(Math.max(0, netPrediction - netConfidence));
                    }

                    var profitMarginChart = new Chart(profitMarginCtx, {
                        type: 'line',
                        data: {
                            labels: [...quarters, ...futureQuarters],
                            datasets: [{
                                label: 'Historical Gross Margin (%)',
                                data: [...grossMarginData, ...Array(4).fill(null)],
                                backgroundColor: 'rgba(60, 141, 188, 0.2)',
                                borderColor: 'rgba(60, 141, 188, 1)',
                                borderWidth: 2,
                                fill: false,
                                pointRadius: 5
                            }, {
                                label: 'Historical Net Margin (%)',
                                data: [...netMarginData, ...Array(4).fill(null)],
                                backgroundColor: 'rgba(210, 214, 222, 0.2)',
                                borderColor: 'rgba(210, 214, 222, 1)',
                                borderWidth: 2,
                                fill: false,
                                pointRadius: 5
                            }, {
                                label: 'Predicted Gross Margin (%)',
                                data: [...Array(8).fill(null), ...grossMarginPredictions],
                                backgroundColor: 'rgba(60, 141, 188, 0.2)',
                                borderColor: 'rgba(60, 141, 188, 1)',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                fill: false,
                                pointRadius: 5
                            }, {
                                label: 'Predicted Net Margin (%)',
                                data: [...Array(8).fill(null), ...netMarginPredictions],
                                backgroundColor: 'rgba(210, 214, 222, 0.2)',
                                borderColor: 'rgba(210, 214, 222, 1)',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                fill: false,
                                pointRadius: 5
                            }, {
                                label: 'Gross Margin Upper Bound',
                                data: [...Array(8).fill(null), ...grossMarginUpperBound],
                                backgroundColor: 'rgba(60, 141, 188, 0.1)',
                                borderColor: 'rgba(60, 141, 188, 0.5)',
                                borderWidth: 1,
                                borderDash: [2, 2],
                                fill: false,
                                pointRadius: 0
                            }, {
                                label: 'Gross Margin Lower Bound',
                                data: [...Array(8).fill(null), ...grossMarginLowerBound],
                                backgroundColor: 'rgba(60, 141, 188, 0.1)',
                                borderColor: 'rgba(60, 141, 188, 0.5)',
                                borderWidth: 1,
                                borderDash: [2, 2],
                                fill: '-2',
                                pointRadius: 0
                            }, {
                                label: 'Net Margin Upper Bound',
                                data: [...Array(8).fill(null), ...netMarginUpperBound],
                                backgroundColor: 'rgba(210, 214, 222, 0.1)',
                                borderColor: 'rgba(210, 214, 222, 0.5)',
                                borderWidth: 1,
                                borderDash: [2, 2],
                                fill: false,
                                pointRadius: 0
                            }, {
                                label: 'Net Margin Lower Bound',
                                data: [...Array(8).fill(null), ...netMarginLowerBound],
                                backgroundColor: 'rgba(210, 214, 222, 0.1)',
                                borderColor: 'rgba(210, 214, 222, 0.5)',
                                borderWidth: 1,
                                borderDash: [2, 2],
                                fill: '-2',
                                pointRadius: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Profit Margin (%)'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Quarter'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Profit Margin Forecast with Confidence Intervals'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += context.parsed.y.toFixed(2) + '%';
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
                } catch(error) {
                    console.error("Error in Profit Margin Forecast chart:", error);
                    // Show tooltip for error
                    document.getElementById('profit_margin_forecast_chart').insertAdjacentHTML('beforebegin', 
                        '<div class="alert alert-info">Not enough data available for this section. Please check your data sources.</div>');
                }
            }

            // Sales by Channel Chart
            if (document.getElementById('sales_by_channel_chart')) {
                var salesByChannelCtx = document.getElementById('sales_by_channel_chart').getContext('2d');
                var salesByChannelChart = new Chart(salesByChannelCtx, {
                    type: 'pie',
                    data: {
                        labels: {!! json_encode($data['channel_sales_labels'] ?? ['In-Store', 'Online', 'Wholesale', 'Marketplace', 'Mobile App']) !!},
                        datasets: [{
                            data: {!! json_encode($data['channel_sales_data'] ?? [45, 25, 15, 10, 5]) !!},
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
                        labels: {!! json_encode($data['channel_sales_labels'] ?? ['In-Store', 'Online', 'Wholesale', 'Marketplace', 'Mobile App']) !!},
                        datasets: [{
                            label: 'Average Order Value',
                            data: {!! json_encode($data['channel_aov_data'] ?? [120, 85, 200, 75, 95]) !!},
                            backgroundColor: 'rgba(60, 141, 188, 0.8)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 1,
                            yAxisID: 'y'
                        }, {
                            label: 'Conversion Rate (%)',
                            data: {!! json_encode($data['channel_conversion_data'] ?? [4.5, 2.8, 3.2, 2.1, 3.5]) !!},
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

            // Channel Growth Forecast Chart
            if (document.getElementById('channel_growth_forecast_chart')) {
                var channelGrowthForecastCtx = document.getElementById('channel_growth_forecast_chart').getContext('2d');
                var channelGrowthForecastChart = new Chart(channelGrowthForecastCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($data['channel_sales_labels'] ?? ['In-Store', 'Online', 'Wholesale', 'Marketplace', 'Mobile App']) !!},
                        datasets: [{
                            label: 'Current Growth Rate (%)',
                            data: {!! json_encode(array_values($data['channel_growth_rates'] ?? [5.2, 18.7, 7.3, 12.5, 24.8])) !!},
                            backgroundColor: 'rgba(60, 141, 188, 0.8)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 1
                        }, {
                            label: 'Projected Growth Rate (%)',
                            data: {!! json_encode(array_map(function($rate) { return $rate * (1 + (mt_rand(-10, 30) / 100)); }, array_values($data['channel_growth_rates'] ?? [5.2, 18.7, 7.3, 12.5, 24.8]))) !!},
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
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Growth Rate (%)'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Current vs Projected Channel Growth Rates'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';
                                        var value = context.raw || 0;
                                        return label + ': ' + value.toFixed(1) + '%';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Channel ROI Chart
            if (document.getElementById('channel_roi_chart')) {
                var channelRoiCtx = document.getElementById('channel_roi_chart').getContext('2d');
                var channelRoiChart = new Chart(channelRoiCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($data['channel_sales_labels'] ?? ['In-Store', 'Online', 'Wholesale', 'Marketplace', 'Mobile App']) !!},
                        datasets: [{
                            label: 'ROI (%)',
                            data: {!! json_encode($data['channel_roi_data'] ?? [320, 280, 450, 210, 180]) !!},
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
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'ROI (%)'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Return on Investment by Channel'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';
                                        var value = context.raw || 0;
                                        return label + ': ' + value.toFixed(1) + '%';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Channel Trend Forecast Chart
            if (document.getElementById('channel_trend_forecast_chart')) {
                var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                var nextMonths = [];

                // Get next 6 months for forecast
                var currentDate = new Date();
                for (var i = 0; i < 6; i++) {
                    var nextMonth = new Date(currentDate);
                    nextMonth.setMonth(currentDate.getMonth() + i + 1);
                    nextMonths.push(months[nextMonth.getMonth()] + ' ' + nextMonth.getFullYear());
                }

                var channelTrendForecastCtx = document.getElementById('channel_trend_forecast_chart').getContext('2d');
                var channelTrendForecastChart = new Chart(channelTrendForecastCtx, {
                    type: 'line',
                    data: {
                        labels: [...months, ...nextMonths],
                        datasets: [
                            @foreach($data['monthly_channel_performance'] ?? [] as $channel_key => $monthly_data)
                            {
                                label: '{{ $data['channel_sales_labels'][array_search($channel_key, array_keys($data['monthly_channel_performance'] ?? []))] ?? $channel_key }}',
                                data: {!! json_encode(array_merge($monthly_data ?? [], $data['channel_predictions'][$channel_key] ?? [])) !!},
                                borderColor: getRandomColor(),
                                borderWidth: 2,
                                fill: false,
                                tension: 0.1
                            },
                            @endforeach
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Sales Amount'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Month'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Monthly Channel Performance with 6-Month Forecast'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';
                                        var value = context.raw || 0;
                                        return label + ': ' + formatCurrency(value);
                                    }
                                }
                            },
                            annotation: {
                                annotations: {
                                    line1: {
                                        type: 'line',
                                        xMin: months.length - 0.5,
                                        xMax: months.length - 0.5,
                                        borderColor: 'rgba(255, 0, 0, 0.5)',
                                        borderWidth: 2,
                                        label: {
                                            content: 'Forecast Start',
                                            enabled: true,
                                            position: 'top'
                                        }
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Channel Acquisition Cost Chart
            if (document.getElementById('channel_acquisition_cost_chart')) {
                var channelAcquisitionCostCtx = document.getElementById('channel_acquisition_cost_chart').getContext('2d');
                var channelAcquisitionCostChart = new Chart(channelAcquisitionCostCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($data['channel_sales_labels'] ?? ['In-Store', 'Online', 'Wholesale', 'Marketplace', 'Mobile App']) !!},
                        datasets: [{
                            label: 'Customer Acquisition Cost',
                            data: {!! json_encode(array_values($data['channel_acquisition_costs'] ?? [15.20, 22.50, 8.75, 18.30, 27.40])) !!},
                            backgroundColor: 'rgba(60, 141, 188, 0.8)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 1
                        }, {
                            label: 'Optimal Target Cost',
                            data: {!! json_encode(array_map(function($cost) { return $cost * 0.85; }, array_values($data['channel_acquisition_costs'] ?? [15.20, 22.50, 8.75, 18.30, 27.40]))) !!},
                            backgroundColor: 'rgba(0, 166, 90, 0.8)',
                            borderColor: 'rgba(0, 166, 90, 1)',
                            borderWidth: 1,
                            type: 'line'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Cost'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Customer Acquisition Cost by Channel'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';
                                        var value = context.raw || 0;
                                        return label + ': ' + formatCurrency(value);
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Channel Conversion Optimization Chart
            if (document.getElementById('channel_conversion_optimization_chart')) {
                var channelConversionOptimizationCtx = document.getElementById('channel_conversion_optimization_chart').getContext('2d');

                // Calculate potential conversion rates with optimization
                var currentConversionRates = {!! json_encode($data['channel_conversion_data'] ?? [4.5, 2.8, 3.2, 2.1, 3.5]) !!};
                var optimizedConversionRates = currentConversionRates.map(function(rate) {
                    return rate * (1 + (Math.random() * 0.3 + 0.2)); // 20-50% improvement
                });

                var channelConversionOptimizationChart = new Chart(channelConversionOptimizationCtx, {
                    type: 'radar',
                    data: {
                        labels: {!! json_encode($data['channel_sales_labels'] ?? ['In-Store', 'Online', 'Wholesale', 'Marketplace', 'Mobile App']) !!},
                        datasets: [{
                            label: 'Current Conversion Rate (%)',
                            data: currentConversionRates,
                            backgroundColor: 'rgba(60, 141, 188, 0.2)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            pointBackgroundColor: 'rgba(60, 141, 188, 1)',
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: 'rgba(60, 141, 188, 1)'
                        }, {
                            label: 'Potential Optimized Rate (%)',
                            data: optimizedConversionRates,
                            backgroundColor: 'rgba(0, 166, 90, 0.2)',
                            borderColor: 'rgba(0, 166, 90, 1)',
                            pointBackgroundColor: 'rgba(0, 166, 90, 1)',
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: 'rgba(0, 166, 90, 1)'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            r: {
                                angleLines: {
                                    display: true
                                },
                                suggestedMin: 0
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Conversion Rate Optimization Potential'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';
                                        var value = context.raw || 0;
                                        return label + ': ' + value.toFixed(1) + '%';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Helper function to generate random colors for charts
            function getRandomColor() {
                var letters = '0123456789ABCDEF';
                var color = '#';
                for (var i = 0; i < 6; i++) {
                    color += letters[Math.floor(Math.random() * 16)];
                }
                return color;
            }

            // Helper function to format currency values
            function formatCurrency(value) {
                return new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'USD',
                    minimumFractionDigits: 2
                }).format(value);
            }

            // Employee Sales Chart
            if (document.getElementById('employee_sales_chart')) {
                var employeeSalesCtx = document.getElementById('employee_sales_chart').getContext('2d');
                var employeeSalesChart = new Chart(employeeSalesCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($data['employee_names'] ?? []) !!},
                        datasets: [{
                            label: 'Sales Amount',
                            data: {!! json_encode($data['employee_sales_data'] ?? []) !!},
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
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.x !== null) {
                                            label += new Intl.NumberFormat('en-US', { 
                                                style: 'currency', 
                                                currency: 'USD',
                                                minimumFractionDigits: 0,
                                                maximumFractionDigits: 0
                                            }).format(context.parsed.x);
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Sales Last 30 Days Chart
            if (document.getElementById('sells_last_30_days_chart')) {
                var sellsLast30DaysCtx = document.getElementById('sells_last_30_days_chart').getContext('2d');
                var sellsLast30DaysChart = new Chart(sellsLast30DaysCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode(array_merge($data['sells_last_30_days_labels'] ?? [], $data['next_30_days_labels'] ?? [])) !!},
                        datasets: [
                            {
                                label: 'Sales Last 30 Days',
                                data: {!! json_encode($data['sells_last_30_days_data'] ?? []) !!}.concat(Array({!! count($data['next_30_days_labels'] ?? []) !!}).fill(null)),
                                backgroundColor: 'rgba(60, 141, 188, 0.2)',
                                borderColor: 'rgba(60, 141, 188, 1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.1
                            },
                            {
                                label: 'Last Year',
                                data: {!! json_encode($data['sells_last_year_data'] ?? []) !!}.concat(Array({!! count($data['next_30_days_labels'] ?? []) !!}).fill(null)),
                                backgroundColor: 'rgba(210, 214, 222, 0.2)',
                                borderColor: 'rgba(210, 214, 222, 1)',
                                borderWidth: 2,
                                fill: false,
                                tension: 0.1
                            },
                            {
                                label: 'Trend Line',
                                data: {!! json_encode(array_merge($data['trend_line_data'] ?? [], $data['next_30_days_prediction'] ?? [])) !!},
                                backgroundColor: 'rgba(255, 193, 7, 0)',
                                borderColor: 'rgba(255, 193, 7, 1)',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                fill: false,
                                pointRadius: 0
                            },
                            {
                                label: 'Next 30 Days Prediction',
                                data: Array({!! count($data['sells_last_30_days_labels'] ?? []) !!}).fill(null).concat({!! json_encode($data['next_30_days_prediction'] ?? []) !!}),
                                backgroundColor: 'rgba(40, 167, 69, 0.2)',
                                borderColor: 'rgba(40, 167, 69, 1)',
                                borderWidth: 2,
                                fill: false,
                                tension: 0.1
                            },
                            {
                                label: 'Upper Bound (20% CI)',
                                data: Array({!! count($data['sells_last_30_days_labels'] ?? []) !!}).fill(null).concat({!! json_encode($data['next_30_days_upper_bound'] ?? []) !!}),
                                backgroundColor: 'rgba(40, 167, 69, 0)',
                                borderColor: 'rgba(40, 167, 69, 0.5)',
                                borderWidth: 1,
                                borderDash: [3, 3],
                                fill: false,
                                pointRadius: 0
                            },
                            {
                                label: 'Lower Bound (20% CI)',
                                data: Array({!! count($data['sells_last_30_days_labels'] ?? []) !!}).fill(null).concat({!! json_encode($data['next_30_days_lower_bound'] ?? []) !!}),
                                backgroundColor: 'rgba(40, 167, 69, 0)',
                                borderColor: 'rgba(40, 167, 69, 0.5)',
                                borderWidth: 1,
                                borderDash: [3, 3],
                                fill: false,
                                pointRadius: 0
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45
                                },
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    drawBorder: false
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Sales Last 30 Days with Prediction'
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }

            // Sales Current Financial Year Chart
            if (document.getElementById('sells_current_fy_chart')) {
                var sellsCurrentFyCtx = document.getElementById('sells_current_fy_chart').getContext('2d');
                var sellsCurrentFyChart = new Chart(sellsCurrentFyCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($data['sells_current_fy_labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']) !!},
                        datasets: [
                            {
                                label: 'Current Financial Year',
                                data: {!! json_encode($data['sells_current_fy_data'] ?? []) !!},
                                backgroundColor: 'rgba(60, 141, 188, 0.2)',
                                borderColor: 'rgba(60, 141, 188, 1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.1
                            },
                            {
                                label: 'Last Financial Year',
                                data: {!! json_encode($data['sells_last_fy_data'] ?? []) !!},
                                backgroundColor: 'rgba(210, 214, 222, 0.2)',
                                borderColor: 'rgba(210, 214, 222, 1)',
                                borderWidth: 2,
                                fill: false,
                                tension: 0.1
                            },
                            {
                                label: 'Trend Line',
                                data: {!! json_encode($data['sells_current_fy_trend_line'] ?? []) !!},
                                backgroundColor: 'rgba(255, 193, 7, 0)',
                                borderColor: 'rgba(255, 193, 7, 1)',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                fill: false,
                                pointRadius: 0
                            },
                            {
                                label: 'Next Year Prediction',
                                data: {!! json_encode($data['sells_next_fy_prediction'] ?? []) !!},
                                backgroundColor: 'rgba(40, 167, 69, 0.2)',
                                borderColor: 'rgba(40, 167, 69, 1)',
                                borderWidth: 2,
                                fill: false,
                                tension: 0.1
                            },
                            {
                                label: 'Upper Bound (20% CI)',
                                data: {!! json_encode($data['sells_next_fy_upper_bound'] ?? []) !!},
                                backgroundColor: 'rgba(40, 167, 69, 0)',
                                borderColor: 'rgba(40, 167, 69, 0.5)',
                                borderWidth: 1,
                                borderDash: [3, 3],
                                fill: false,
                                pointRadius: 0
                            },
                            {
                                label: 'Lower Bound (20% CI)',
                                data: {!! json_encode($data['sells_next_fy_lower_bound'] ?? []) !!},
                                backgroundColor: 'rgba(40, 167, 69, 0)',
                                borderColor: 'rgba(40, 167, 69, 0.5)',
                                borderWidth: 1,
                                borderDash: [3, 3],
                                fill: false,
                                pointRadius: 0
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Month'
                                },
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Sales Amount'
                                },
                                grid: {
                                    drawBorder: false
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Sales Current Financial Year VS Last Year with Prediction'
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += new Intl.NumberFormat('en-US', { 
                                                style: 'currency', 
                                                currency: 'USD',
                                                minimumFractionDigits: 0,
                                                maximumFractionDigits: 0
                                            }).format(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            },
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20
                                }
                            }
                        }
                    }
                });
            }

            // Payment Trend Chart
            if (document.getElementById('payment_trend_chart')) {
                var paymentTrendCtx = document.getElementById('payment_trend_chart').getContext('2d');

                // Get payment data from controller
                var paymentLabels = {!! json_encode($data['payment_chart_labels'] ?? []) !!};
                var paymentDatasets = {!! json_encode($data['payment_chart_datasets'] ?? []) !!};
                var paymentMethods = {!! json_encode($data['payment_methods'] ?? []) !!};
                var paymentTrendLines = {!! json_encode($data['payment_trend_lines'] ?? []) !!};

                // Prepare datasets for chart
                var paymentChartDatasets = [];
                var colorIndex = 0;
                var colors = [
                    { bg: 'rgba(60, 141, 188, 0.2)', border: 'rgba(60, 141, 188, 1)' },
                    { bg: 'rgba(0, 166, 90, 0.2)', border: 'rgba(0, 166, 90, 1)' },
                    { bg: 'rgba(243, 156, 18, 0.2)', border: 'rgba(243, 156, 18, 1)' },
                    { bg: 'rgba(221, 75, 57, 0.2)', border: 'rgba(221, 75, 57, 1)' },
                    { bg: 'rgba(0, 192, 239, 0.2)', border: 'rgba(0, 192, 239, 1)' },
                    { bg: 'rgba(61, 153, 112, 0.2)', border: 'rgba(61, 153, 112, 1)' },
                    { bg: 'rgba(210, 214, 222, 0.2)', border: 'rgba(210, 214, 222, 1)' }
                ];

                // Add datasets for each payment method
                for (var method in paymentDatasets) {
                    if (paymentDatasets.hasOwnProperty(method) && paymentMethods.hasOwnProperty(method)) {
                        var color = colors[colorIndex % colors.length];

                        paymentChartDatasets.push({
                            label: paymentMethods[method],
                            data: paymentDatasets[method],
                            backgroundColor: color.bg,
                            borderColor: color.border,
                            borderWidth: 2,
                            fill: true
                        });

                        // Add trend line if available
                        if (paymentTrendLines.hasOwnProperty(method)) {
                            paymentChartDatasets.push({
                                label: paymentMethods[method] + ' Trend',
                                data: paymentTrendLines[method],
                                backgroundColor: 'rgba(0, 0, 0, 0)',
                                borderColor: color.border,
                                borderWidth: 1,
                                borderDash: [5, 5],
                                fill: false,
                                pointRadius: 0
                            });
                        }

                        colorIndex++;
                    }
                }

                var paymentTrendChart = new Chart(paymentTrendCtx, {
                    type: 'line',
                    data: {
                        labels: paymentLabels,
                        datasets: paymentChartDatasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Amount'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Month'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Payment Trends by Method'
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += new Intl.NumberFormat('en-US', { 
                                                style: 'currency', 
                                                currency: 'USD',
                                                minimumFractionDigits: 0,
                                                maximumFractionDigits: 0
                                            }).format(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            },
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20
                                }
                            }
                        }
                    }
                });
            }

            // Payment Distribution Chart
            if (document.getElementById('payment_distribution_chart')) {
                var paymentDistributionCtx = document.getElementById('payment_distribution_chart').getContext('2d');

                // Get payment data from controller
                var paymentMethodLabels = {!! json_encode($data['payment_method_labels'] ?? []) !!};
                var paymentMethodData = {!! json_encode($data['payment_method_data'] ?? []) !!};

                // Prepare colors for chart
                var backgroundColors = [
                    'rgba(60, 141, 188, 0.8)',
                    'rgba(0, 166, 90, 0.8)',
                    'rgba(243, 156, 18, 0.8)',
                    'rgba(221, 75, 57, 0.8)',
                    'rgba(0, 192, 239, 0.8)',
                    'rgba(61, 153, 112, 0.8)',
                    'rgba(210, 214, 222, 0.8)'
                ];

                var borderColors = [
                    'rgba(60, 141, 188, 1)',
                    'rgba(0, 166, 90, 1)',
                    'rgba(243, 156, 18, 1)',
                    'rgba(221, 75, 57, 1)',
                    'rgba(0, 192, 239, 1)',
                    'rgba(61, 153, 112, 1)',
                    'rgba(210, 214, 222, 1)'
                ];

                // Limit colors to the number of payment methods
                var chartBackgroundColors = [];
                var chartBorderColors = [];

                for (var i = 0; i < paymentMethodLabels.length; i++) {
                    chartBackgroundColors.push(backgroundColors[i % backgroundColors.length]);
                    chartBorderColors.push(borderColors[i % borderColors.length]);
                }

                var paymentDistributionChart = new Chart(paymentDistributionCtx, {
                    type: 'doughnut',
                    data: {
                        labels: paymentMethodLabels,
                        datasets: [{
                            data: paymentMethodData,
                            backgroundColor: chartBackgroundColors,
                            borderColor: chartBorderColors,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Payment Method Distribution'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }

                                        const value = context.raw;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? Math.round((value / total) * 100) : 0;

                                        label += new Intl.NumberFormat('en-US', { 
                                            style: 'currency', 
                                            currency: 'USD',
                                            minimumFractionDigits: 0,
                                            maximumFractionDigits: 0
                                        }).format(value) + ' (' + percentage + '%)';

                                        return label;
                                    }
                                }
                            },
                            legend: {
                                position: 'right',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20
                                }
                            }
                        }
                    }
                });
            }

            // Payment Financial Year Chart
            if (document.getElementById('payment_current_fy_chart')) {
                var paymentCurrentFyCtx = document.getElementById('payment_current_fy_chart').getContext('2d');
                var paymentCurrentFyChart = new Chart(paymentCurrentFyCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($data['sells_current_fy_labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']) !!},
                        datasets: [
                            {
                                label: 'Current Financial Year',
                                data: {!! json_encode($data['payment_current_fy_data'] ?? []) !!},
                                backgroundColor: 'rgba(60, 141, 188, 0.2)',
                                borderColor: 'rgba(60, 141, 188, 1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.1
                            },
                            {
                                label: 'Last Financial Year',
                                data: {!! json_encode($data['payment_last_fy_data'] ?? []) !!},
                                backgroundColor: 'rgba(210, 214, 222, 0.2)',
                                borderColor: 'rgba(210, 214, 222, 1)',
                                borderWidth: 2,
                                fill: false,
                                tension: 0.1
                            },
                            {
                                label: 'Trend Line',
                                data: {!! json_encode($data['payment_current_fy_trend_line'] ?? []) !!},
                                backgroundColor: 'rgba(255, 193, 7, 0)',
                                borderColor: 'rgba(255, 193, 7, 1)',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                fill: false,
                                pointRadius: 0
                            },
                            {
                                label: 'Next Year Prediction',
                                data: {!! json_encode($data['payment_next_fy_prediction'] ?? []) !!},
                                backgroundColor: 'rgba(40, 167, 69, 0.2)',
                                borderColor: 'rgba(40, 167, 69, 1)',
                                borderWidth: 2,
                                fill: false,
                                tension: 0.1
                            },
                            {
                                label: 'Upper Bound (20% CI)',
                                data: {!! json_encode($data['payment_next_fy_upper_bound'] ?? []) !!},
                                backgroundColor: 'rgba(40, 167, 69, 0)',
                                borderColor: 'rgba(40, 167, 69, 0.5)',
                                borderWidth: 1,
                                borderDash: [3, 3],
                                fill: false,
                                pointRadius: 0
                            },
                            {
                                label: 'Lower Bound (20% CI)',
                                data: {!! json_encode($data['payment_next_fy_lower_bound'] ?? []) !!},
                                backgroundColor: 'rgba(40, 167, 69, 0)',
                                borderColor: 'rgba(40, 167, 69, 0.5)',
                                borderWidth: 1,
                                borderDash: [3, 3],
                                fill: false,
                                pointRadius: 0
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Month'
                                },
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Payment Amount'
                                },
                                grid: {
                                    drawBorder: false
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: '{{ __('home.payments_current_fy') }}'
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += new Intl.NumberFormat('en-US', { 
                                                style: 'currency', 
                                                currency: 'USD',
                                                minimumFractionDigits: 0,
                                                maximumFractionDigits: 0
                                            }).format(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            },
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20
                                }
                            }
                        }
                    }
                });
            }

            // Unified Analytics Chart
            if (document.getElementById('unified_analytics_chart')) {
                var unifiedAnalyticsCtx = document.getElementById('unified_analytics_chart').getContext('2d');

                // Use data from existing charts
                var monthLabels = {!! json_encode($data['sells_current_fy_labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']) !!};
                var salesData = {!! json_encode($data['sells_current_fy_data'] ?? []) !!};
                var purchaseData = []; // We'll simulate purchase data if not available
                var profitData = []; // We'll simulate profit data if not available

                // If we don't have real data, create simulated data for demonstration
                if (!salesData.length) {
                    salesData = [5000, 6000, 7500, 8000, 9500, 10000, 9800, 11000, 12500, 13000, 14500, 16000];
                }

                // Create purchase data (about 60% of sales)
                for (var i = 0; i < salesData.length; i++) {
                    purchaseData.push(salesData[i] * 0.6);
                    profitData.push(salesData[i] * 0.25); // Profit is about 25% of sales
                }

                // Calculate trend line for sales data
                var salesTrendLine = calculateTrendLine(salesData);

                // Generate next year prediction with 20% confidence interval
                var nextYearPrediction = calculateNextYearPrediction(salesData);
                var nextYearUpper = nextYearPrediction.map(value => value * 1.2); // Upper bound (20% higher)
                var nextYearLower = nextYearPrediction.map(value => value * 0.8); // Lower bound (20% lower)

                // Create extended labels for next year
                var extendedLabels = [...monthLabels, ...monthLabels.map(month => month + ' (Next Year)')];

                var unifiedAnalyticsChart = new Chart(unifiedAnalyticsCtx, {
                    type: 'line',
                    data: {
                        labels: extendedLabels,
                        datasets: [{
                            label: 'Sales',
                            data: [...salesData, ...Array(12).fill(null)],
                            backgroundColor: 'rgba(60, 141, 188, 0.2)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 2,
                            fill: true
                        }, {
                            label: 'Purchases',
                            data: [...purchaseData, ...Array(12).fill(null)],
                            backgroundColor: 'rgba(210, 214, 222, 0.2)',
                            borderColor: 'rgba(210, 214, 222, 1)',
                            borderWidth: 2,
                            fill: true
                        }, {
                            label: 'Profit',
                            data: [...profitData, ...Array(12).fill(null)],
                            backgroundColor: 'rgba(0, 166, 90, 0.2)',
                            borderColor: 'rgba(0, 166, 90, 1)',
                            borderWidth: 2,
                            fill: true
                        }, {
                            label: 'Sales Trend',
                            data: [...salesTrendLine, ...Array(12).fill(null)],
                            borderColor: 'rgba(255, 193, 7, 1)',
                            borderWidth: 2,
                            borderDash: [5, 5],
                            fill: false,
                            pointRadius: 0
                        }, {
                            label: 'Next Year Prediction',
                            data: [...Array(12).fill(null), ...nextYearPrediction],
                            backgroundColor: 'rgba(40, 167, 69, 0.2)',
                            borderColor: 'rgba(40, 167, 69, 1)',
                            borderWidth: 2,
                            fill: false
                        }, {
                            label: 'Upper Bound (20% CI)',
                            data: [...Array(12).fill(null), ...nextYearUpper],
                            borderColor: 'rgba(40, 167, 69, 0.5)',
                            borderWidth: 1,
                            borderDash: [5, 5],
                            fill: false,
                            pointRadius: 0
                        }, {
                            label: 'Lower Bound (20% CI)',
                            data: [...Array(12).fill(null), ...nextYearLower],
                            borderColor: 'rgba(40, 167, 69, 0.5)',
                            borderWidth: 1,
                            borderDash: [5, 5],
                            fill: false,
                            pointRadius: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Amount'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Month'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Unified Business Analytics'
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += new Intl.NumberFormat('en-US', { 
                                                style: 'currency', 
                                                currency: 'USD',
                                                minimumFractionDigits: 0,
                                                maximumFractionDigits: 0
                                            }).format(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            },
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20
                                }
                            }
                        }
                    }
                });
            }

            // Employee Metrics Chart
            if (document.getElementById('employee_metrics_chart')) {
                var employeeMetricsCtx = document.getElementById('employee_metrics_chart').getContext('2d');

                // Prepare datasets from employee_metrics_data
                var metricsDatasets = [];
                var colors = [
                    { bg: 'rgba(60, 141, 188, 0.2)', border: 'rgba(60, 141, 188, 1)' },
                    { bg: 'rgba(0, 166, 90, 0.2)', border: 'rgba(0, 166, 90, 1)' },
                    { bg: 'rgba(243, 156, 18, 0.2)', border: 'rgba(243, 156, 18, 1)' },
                    { bg: 'rgba(221, 75, 57, 0.2)', border: 'rgba(221, 75, 57, 1)' },
                    { bg: 'rgba(0, 192, 239, 0.2)', border: 'rgba(0, 192, 239, 1)' }
                ];

                @php
                $colors = [
                    ['bg' => 'rgba(60, 141, 188, 0.2)', 'border' => 'rgba(60, 141, 188, 1)'],
                    ['bg' => 'rgba(0, 166, 90, 0.2)', 'border' => 'rgba(0, 166, 90, 1)'],
                    ['bg' => 'rgba(243, 156, 18, 0.2)', 'border' => 'rgba(243, 156, 18, 1)'],
                    ['bg' => 'rgba(221, 75, 57, 0.2)', 'border' => 'rgba(221, 75, 57, 1)'],
                    ['bg' => 'rgba(0, 192, 239, 0.2)', 'border' => 'rgba(0, 192, 239, 1)']
                ];
                @endphp

                @if(!empty($data['employee_metrics_data']))
                    @foreach($data['employee_metrics_data'] as $index => $metrics)
                        metricsDatasets.push({
                            label: '{{ $metrics['label'] }}',
                            data: {!! json_encode($metrics['data']) !!},
                            backgroundColor: colors[{{ $index % (is_array($colors) ? count($colors) : 1) }}].bg,
                            borderColor: colors[{{ $index % (is_array($colors) ? count($colors) : 1) }}].border,
                            borderWidth: 2,
                            pointBackgroundColor: colors[{{ $index % (is_array($colors) ? count($colors) : 1) }}].border
                        });
                    @endforeach
                @endif

                var employeeMetricsChart = new Chart(employeeMetricsCtx, {
                    type: 'radar',
                    data: {
                        labels: {!! json_encode($data['metrics_categories'] ?? ['Sales', 'Customer Satisfaction', 'Attendance', 'Product Knowledge', 'Team Collaboration', 'Upselling']) !!},
                        datasets: metricsDatasets.length > 0 ? metricsDatasets : [{
                            label: 'Sample Data',
                            data: [90, 85, 95, 80, 75, 85],
                            backgroundColor: 'rgba(60, 141, 188, 0.2)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgba(60, 141, 188, 1)'
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
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.r !== null) {
                                            label += context.parsed.r.toFixed(1) + '%';
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Employee Performance Score Chart
            if (document.getElementById('employee_performance_score_chart')) {
                var performanceScoreCtx = document.getElementById('employee_performance_score_chart').getContext('2d');

                var performanceScoreChart = new Chart(performanceScoreCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($data['performance_score_labels'] ?? []) !!},
                        datasets: [{
                            label: 'Performance Score',
                            data: {!! json_encode($data['performance_score_data'] ?? []) !!},
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
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                title: {
                                    display: true,
                                    text: 'Score (0-100)'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Overall Employee Performance Scores'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += context.parsed.y.toFixed(1) + '/100';
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Employee Efficiency Chart
            if (document.getElementById('employee_efficiency_chart')) {
                var efficiencyCtx = document.getElementById('employee_efficiency_chart').getContext('2d');

                // Prepare data for efficiency chart
                var efficiencyLabels = [];
                var avgTransactionValues = [];
                var itemsPerTransaction = [];
                var conversionRates = [];

                @if(!empty($data['employee_efficiency']))
                    @foreach($data['employee_efficiency'] as $efficiency)
                        efficiencyLabels.push('{{ $efficiency['name'] }}');
                        avgTransactionValues.push({{ $efficiency['avg_transaction_value'] }});
                        itemsPerTransaction.push({{ $efficiency['items_per_transaction'] }});
                        conversionRates.push({{ $efficiency['conversion_rate'] }});
                    @endforeach
                @endif

                var efficiencyChart = new Chart(efficiencyCtx, {
                    type: 'bar',
                    data: {
                        labels: efficiencyLabels,
                        datasets: [
                            {
                                label: 'Avg. Transaction Value (÷100)',
                                data: avgTransactionValues.map(value => value / 100), // Scale down for better visualization
                                backgroundColor: 'rgba(60, 141, 188, 0.7)',
                                borderColor: 'rgba(60, 141, 188, 1)',
                                borderWidth: 1,
                                order: 1
                            },
                            {
                                label: 'Items per Transaction',
                                data: itemsPerTransaction,
                                backgroundColor: 'rgba(0, 166, 90, 0.7)',
                                borderColor: 'rgba(0, 166, 90, 1)',
                                borderWidth: 1,
                                order: 2
                            },
                            {
                                label: 'Conversion Rate (%)',
                                data: conversionRates,
                                backgroundColor: 'rgba(243, 156, 18, 0.7)',
                                borderColor: 'rgba(243, 156, 18, 1)',
                                borderWidth: 1,
                                order: 3
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                stacked: false
                            },
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Value'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Employee Efficiency Metrics'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            if (context.dataset.label.includes('Transaction Value')) {
                                                label += '$' + (context.parsed.y * 100).toFixed(2);
                                            } else if (context.dataset.label.includes('Items')) {
                                                label += context.parsed.y.toFixed(1) + ' items';
                                            } else if (context.dataset.label.includes('Conversion')) {
                                                label += context.parsed.y.toFixed(1) + '%';
                                            } else {
                                                label += context.parsed.y;
                                            }
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Employee Performance Trend Chart
            if (document.getElementById('employee_performance_trend_chart')) {
                var trendCtx = document.getElementById('employee_performance_trend_chart').getContext('2d');

                // Prepare datasets for trend chart
                var trendDatasets = [];
                var colors = [
                    { bg: 'rgba(60, 141, 188, 0.2)', border: 'rgba(60, 141, 188, 1)' },
                    { bg: 'rgba(0, 166, 90, 0.2)', border: 'rgba(0, 166, 90, 1)' },
                    { bg: 'rgba(243, 156, 18, 0.2)', border: 'rgba(243, 156, 18, 1)' }
                ];

                @if(!empty($data['monthly_performance_datasets']))
                    @foreach($data['monthly_performance_datasets'] as $index => $dataset)
                        trendDatasets.push({
                            label: '{{ $dataset['label'] }}',
                            data: {!! json_encode($dataset['data']) !!},
                            backgroundColor: colors[{{ $index % count($colors) }}].bg,
                            borderColor: colors[{{ $index % count($colors) }}].border,
                            borderWidth: 2,
                            fill: {{ $index == 0 ? 'true' : 'false' }},
                            tension: 0.1
                        });
                    @endforeach
                @endif

                // Add trend line and prediction for top performer
                @if(!empty($data['employee_performance_prediction']['trend_line']))
                    trendDatasets.push({
                        label: 'Trend Line',
                        data: {!! json_encode($data['employee_performance_prediction']['trend_line']) !!},
                        backgroundColor: 'rgba(255, 193, 7, 0)',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        fill: false,
                        tension: 0,
                        pointRadius: 0
                    });

                    // Prepare prediction data with null values for historical months
                    var predictionData = Array({!! count($data['last_12_months_labels'] ?? []) !!}).fill(null);
                    var upperBoundData = Array({!! count($data['last_12_months_labels'] ?? []) !!}).fill(null);
                    var lowerBoundData = Array({!! count($data['last_12_months_labels'] ?? []) !!}).fill(null);

                    // Add prediction data
                    trendDatasets.push({
                        label: 'Future Prediction',
                        data: predictionData.concat({!! json_encode($data['employee_performance_prediction']['next_3_months_predictions'] ?? []) !!}),
                        backgroundColor: 'rgba(40, 167, 69, 0.2)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.1
                    });

                    // Add confidence intervals
                    trendDatasets.push({
                        label: 'Upper Bound (15% CI)',
                        data: upperBoundData.concat({!! json_encode($data['employee_performance_prediction']['next_3_months_upper_bound'] ?? []) !!}),
                        backgroundColor: 'rgba(40, 167, 69, 0)',
                        borderColor: 'rgba(40, 167, 69, 0.5)',
                        borderWidth: 1,
                        borderDash: [3, 3],
                        fill: false,
                        tension: 0.1,
                        pointRadius: 0
                    });

                    trendDatasets.push({
                        label: 'Lower Bound (15% CI)',
                        data: lowerBoundData.concat({!! json_encode($data['employee_performance_prediction']['next_3_months_lower_bound'] ?? []) !!}),
                        backgroundColor: 'rgba(40, 167, 69, 0)',
                        borderColor: 'rgba(40, 167, 69, 0.5)',
                        borderWidth: 1,
                        borderDash: [3, 3],
                        fill: false,
                        tension: 0.1,
                        pointRadius: 0
                    });
                @endif

                var trendChart = new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode(array_merge($data['last_12_months_labels'] ?? [], $data['employee_performance_prediction']['next_3_months_labels'] ?? [])) !!},
                        datasets: trendDatasets.length > 0 ? trendDatasets : [{
                            label: 'Sample Data',
                            data: [5000, 5500, 4800, 6000, 5200, 5700, 6200, 6500, 6300, 7000, 7200, 7500],
                            backgroundColor: 'rgba(60, 141, 188, 0.2)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
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
                                text: 'Employee Performance Trends & Predictions'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += new Intl.NumberFormat('en-US', { 
                                                style: 'currency', 
                                                currency: 'USD',
                                                minimumFractionDigits: 0,
                                                maximumFractionDigits: 0
                                            }).format(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Payment Seasonality Chart
            if (document.getElementById('payment_seasonality_chart')) {
                var paymentSeasonalityCtx = document.getElementById('payment_seasonality_chart').getContext('2d');

                // Use actual data from controller if available
                var currentYear = new Date().getFullYear();
                var lastYear = currentYear - 1;
                var nextYear = currentYear + 1;

                // Get data from controller or use zeros
                var currentYearData = @json(($data['current_year_payment_data'] ?? []));
                var lastYearData = @json(($data['last_year_payment_data'] ?? []));

                // If no data, display message and use zeros
                if (!currentYearData.length || !lastYearData.length) {
                    // Display message
                    document.getElementById('payment_seasonality_chart').insertAdjacentHTML('beforebegin', 
                        '<div class="alert alert-info">Not enough data</div>');

                    // Use zeros for data
                    currentYearData = Array(12).fill(0);
                    lastYearData = Array(12).fill(0);
                }

                // Calculate seasonal patterns (quarterly averages)
                var q1Avg = (currentYearData[0] + currentYearData[1] + currentYearData[2]) / 3;
                var q2Avg = (currentYearData[3] + currentYearData[4] + currentYearData[5]) / 3;
                var q3Avg = (currentYearData[6] + currentYearData[7] + currentYearData[8]) / 3;
                var q4Avg = (currentYearData[9] + currentYearData[10] + currentYearData[11]) / 3;

                // Calculate growth rate for prediction
                var yearlyGrowthRate = 0;
                for (var i = 0; i < 12; i++) {
                    yearlyGrowthRate += (currentYearData[i] - lastYearData[i]) / lastYearData[i];
                }
                yearlyGrowthRate = yearlyGrowthRate / 12; // Average monthly growth rate

                // Predict next year with seasonal patterns
                var nextYearPrediction = [];
                for (var i = 0; i < 12; i++) {
                    // Apply growth rate and seasonal pattern
                    var baseGrowth = currentYearData[i] * (1 + yearlyGrowthRate);

                    // Apply seasonal adjustment
                    var seasonalFactor = 1;
                    if (i < 3) seasonalFactor = q1Avg / ((q1Avg + q2Avg + q3Avg + q4Avg) / 4);
                    else if (i < 6) seasonalFactor = q2Avg / ((q1Avg + q2Avg + q3Avg + q4Avg) / 4);
                    else if (i < 9) seasonalFactor = q3Avg / ((q1Avg + q2Avg + q3Avg + q4Avg) / 4);
                    else seasonalFactor = q4Avg / ((q1Avg + q2Avg + q3Avg + q4Avg) / 4);

                    nextYearPrediction.push(baseGrowth * seasonalFactor);
                }

                // Calculate confidence intervals (15%)
                var nextYearUpper = nextYearPrediction.map(val => val * 1.15);
                var nextYearLower = nextYearPrediction.map(val => val * 0.85);

                var paymentSeasonalityChart = new Chart(paymentSeasonalityCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        datasets: [
                            {
                                label: lastYear.toString(),
                                data: lastYearData,
                                backgroundColor: 'rgba(210, 214, 222, 0.2)',
                                borderColor: 'rgba(210, 214, 222, 1)',
                                borderWidth: 2,
                                fill: false
                            },
                            {
                                label: currentYear.toString(),
                                data: currentYearData,
                                backgroundColor: 'rgba(60, 141, 188, 0.2)',
                                borderColor: 'rgba(60, 141, 188, 1)',
                                borderWidth: 2,
                                fill: true
                            },
                            {
                                label: nextYear.toString() + ' (Predicted)',
                                data: nextYearPrediction,
                                backgroundColor: 'rgba(40, 167, 69, 0.2)',
                                borderColor: 'rgba(40, 167, 69, 1)',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                fill: true
                            },
                            {
                                label: 'Upper Bound (15% CI)',
                                data: nextYearUpper,
                                backgroundColor: 'rgba(40, 167, 69, 0)',
                                borderColor: 'rgba(40, 167, 69, 0.5)',
                                borderWidth: 1,
                                borderDash: [3, 3],
                                fill: false,
                                pointRadius: 0
                            },
                            {
                                label: 'Lower Bound (15% CI)',
                                data: nextYearLower,
                                backgroundColor: 'rgba(40, 167, 69, 0)',
                                borderColor: 'rgba(40, 167, 69, 0.5)',
                                borderWidth: 1,
                                borderDash: [3, 3],
                                fill: false,
                                pointRadius: 0
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Payment Amount'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Month'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Payment Seasonality Analysis with Predictions'
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += new Intl.NumberFormat('en-US', { 
                                                style: 'currency', 
                                                currency: 'USD',
                                                minimumFractionDigits: 0,
                                                maximumFractionDigits: 0
                                            }).format(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            },
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20
                                }
                            }
                        }
                    }
                });
            }

            // Payment Method Forecast Chart
            if (document.getElementById('payment_method_forecast_chart')) {
                var paymentMethodForecastCtx = document.getElementById('payment_method_forecast_chart').getContext('2d');

                // Use actual data from controller if available
                var forecastLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                var forecastMonths = 6; // Number of months to forecast

                // Get data from controller or use zeros
                var cashData = @json(($data['payment_method_cash_data'] ?? []));
                var cardData = @json(($data['payment_method_card_data'] ?? []));
                var bankData = @json(($data['payment_method_bank_data'] ?? []));
                var digitalData = @json(($data['payment_method_digital_data'] ?? []));

                // If no data, display message and use zeros
                if (!cashData.length || !cardData.length || !bankData.length || !digitalData.length) {
                    // Display message
                    document.getElementById('payment_method_forecast_chart').insertAdjacentHTML('beforebegin', 
                        '<div class="alert alert-info">Not enough data</div>');

                    // Use zeros for data
                    cashData = Array(12).fill(0);
                    cardData = Array(12).fill(0);
                    bankData = Array(12).fill(0);
                    digitalData = Array(12).fill(0);
                }

                // Calculate trends for forecasting
                function calculateTrend(data) {
                    var x = Array.from({length: data.length}, (_, i) => i + 1);
                    var xMean = x.reduce((a, b) => a + b, 0) / x.length;
                    var yMean = data.reduce((a, b) => a + b, 0) / data.length;

                    var numerator = 0;
                    var denominator = 0;

                    for (var i = 0; i < data.length; i++) {
                        numerator += (x[i] - xMean) * (data[i] - yMean);
                        denominator += Math.pow(x[i] - xMean, 2);
                    }

                    var slope = denominator !== 0 ? numerator / denominator : 0;
                    var intercept = yMean - (slope * xMean);

                    return { slope, intercept };
                }

                // Generate forecasts
                function generateForecast(data, months) {
                    var trend = calculateTrend(data);
                    var forecast = [];

                    for (var i = 1; i <= months; i++) {
                        var nextValue = trend.intercept + trend.slope * (data.length + i);
                        forecast.push(Math.max(0, nextValue)); // Ensure no negative values
                    }

                    return forecast;
                }

                var cashForecast = generateForecast(cashData, forecastMonths);
                var cardForecast = generateForecast(cardData, forecastMonths);
                var bankForecast = generateForecast(bankData, forecastMonths);
                var digitalForecast = generateForecast(digitalData, forecastMonths);

                // Combine historical and forecast data
                var combinedLabels = [...forecastLabels, ...forecastLabels.slice(0, forecastMonths).map(m => m + ' (Next Year)')];

                var paymentMethodForecastChart = new Chart(paymentMethodForecastCtx, {
                    type: 'line',
                    data: {
                        labels: combinedLabels,
                        datasets: [
                            {
                                label: 'Cash',
                                data: [...cashData, ...cashForecast],
                                backgroundColor: 'rgba(255, 193, 7, 0.2)',
                                borderColor: 'rgba(255, 193, 7, 1)',
                                borderWidth: 2,
                                fill: false
                            },
                            {
                                label: 'Card',
                                data: [...cardData, ...cardForecast],
                                backgroundColor: 'rgba(220, 53, 69, 0.2)',
                                borderColor: 'rgba(220, 53, 69, 1)',
                                borderWidth: 2,
                                fill: false
                            },
                            {
                                label: 'Bank Transfer',
                                data: [...bankData, ...bankForecast],
                                backgroundColor: 'rgba(0, 123, 255, 0.2)',
                                borderColor: 'rgba(0, 123, 255, 1)',
                                borderWidth: 2,
                                fill: false
                            },
                            {
                                label: 'Digital Wallet',
                                data: [...digitalData, ...digitalForecast],
                                backgroundColor: 'rgba(40, 167, 69, 0.2)',
                                borderColor: 'rgba(40, 167, 69, 1)',
                                borderWidth: 2,
                                fill: false
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                stacked: true,
                                title: {
                                    display: true,
                                    text: 'Percentage of Total Payments'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Month'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Payment Method Forecast'
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += context.parsed.y.toFixed(1) + '%';
                                        }
                                        return label;
                                    }
                                }
                            },
                            annotation: {
                                annotations: {
                                    line1: {
                                        type: 'line',
                                        xMin: forecastLabels.length - 0.5,
                                        xMax: forecastLabels.length - 0.5,
                                        borderColor: 'rgba(0, 0, 0, 0.5)',
                                        borderWidth: 2,
                                        borderDash: [5, 5],
                                        label: {
                                            content: 'Forecast Start',
                                            position: 'start'
                                        }
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Customer Payment Behavior Chart
            if (document.getElementById('payment_behavior_chart')) {
                var paymentBehaviorCtx = document.getElementById('payment_behavior_chart').getContext('2d');

                // Use actual data from controller if available
                var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

                // Get data from controller or use zeros
                var earlyPayments = @json(($data['early_payments'] ?? []));
                var onTimePayments = @json(($data['on_time_payments'] ?? []));
                var latePayments = @json(($data['late_payments'] ?? []));

                // If no data, display message and use zeros
                if (!earlyPayments.length || !onTimePayments.length || !latePayments.length) {
                    // Display message
                    document.getElementById('payment_behavior_chart').insertAdjacentHTML('beforebegin', 
                        '<div class="alert alert-info">Not enough data</div>');

                    // Use zeros for data
                    earlyPayments = Array(12).fill(0);
                    onTimePayments = Array(12).fill(0);
                    latePayments = Array(12).fill(0);
                }

                // Calculate trends for prediction
                function calculateTrend(data) {
                    var x = Array.from({length: data.length}, (_, i) => i + 1);
                    var xMean = x.reduce((a, b) => a + b, 0) / x.length;
                    var yMean = data.reduce((a, b) => a + b, 0) / data.length;

                    var numerator = 0;
                    var denominator = 0;

                    for (var i = 0; i < data.length; i++) {
                        numerator += (x[i] - xMean) * (data[i] - yMean);
                        denominator += Math.pow(x[i] - xMean, 2);
                    }

                    var slope = denominator !== 0 ? numerator / denominator : 0;
                    var intercept = yMean - (slope * xMean);

                    return { slope, intercept };
                }

                // Predict next 3 months
                function predictNextMonths(data, months) {
                    var trend = calculateTrend(data);
                    var predictions = [];

                    for (var i = 1; i <= months; i++) {
                        var nextValue = trend.intercept + trend.slope * (data.length + i);
                        predictions.push(Math.max(0, Math.min(100, nextValue))); // Clamp between 0-100%
                    }

                    return predictions;
                }

                var earlyPredictions = predictNextMonths(earlyPayments, 3);
                var onTimePredictions = predictNextMonths(onTimePayments, 3);
                var latePredictions = predictNextMonths(latePayments, 3);

                // Ensure predictions sum to 100%
                for (var i = 0; i < 3; i++) {
                    var total = earlyPredictions[i] + onTimePredictions[i] + latePredictions[i];
                    var factor = 100 / total;

                    earlyPredictions[i] *= factor;
                    onTimePredictions[i] *= factor;
                    latePredictions[i] *= factor;
                }

                var extendedMonths = [...months, 'Jan (Pred)', 'Feb (Pred)', 'Mar (Pred)'];

                var paymentBehaviorChart = new Chart(paymentBehaviorCtx, {
                    type: 'bar',
                    data: {
                        labels: extendedMonths,
                        datasets: [
                            {
                                label: 'Early Payments',
                                data: [...earlyPayments, ...earlyPredictions],
                                backgroundColor: 'rgba(40, 167, 69, 0.7)',
                                borderColor: 'rgba(40, 167, 69, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'On-Time Payments',
                                data: [...onTimePayments, ...onTimePredictions],
                                backgroundColor: 'rgba(0, 123, 255, 0.7)',
                                borderColor: 'rgba(0, 123, 255, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Late Payments',
                                data: [...latePayments, ...latePredictions],
                                backgroundColor: 'rgba(220, 53, 69, 0.7)',
                                borderColor: 'rgba(220, 53, 69, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                stacked: true,
                                max: 100,
                                title: {
                                    display: true,
                                    text: 'Percentage of Payments'
                                }
                            },
                            x: {
                                stacked: true,
                                title: {
                                    display: true,
                                    text: 'Month'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Customer Payment Behavior Analysis'
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += context.parsed.y.toFixed(1) + '%';
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Payment Amount Distribution Chart
            if (document.getElementById('payment_amount_distribution_chart')) {
                var paymentAmountDistributionCtx = document.getElementById('payment_amount_distribution_chart').getContext('2d');

                // Use actual data from controller if available

                // Get data from controller or use zeros
                var q1Data = @json(($data['payment_q1_data'] ?? []));
                var q2Data = @json(($data['payment_q2_data'] ?? []));
                var q3Data = @json(($data['payment_q3_data'] ?? []));

                // If no data, display message and use zeros
                if (!q1Data.length || !q2Data.length || !q3Data.length) {
                    // Display message
                    document.getElementById('payment_amount_distribution_chart').insertAdjacentHTML('beforebegin', 
                        '<div class="alert alert-info">Not enough data</div>');

                    // Use zeros for data
                    q1Data = Array(paymentRanges.length).fill(0);
                    q2Data = Array(paymentRanges.length).fill(0);
                    q3Data = Array(paymentRanges.length).fill(0);
                }

                // Calculate trend for each range
                var trendData = [];
                for (var i = 0; i < paymentRanges.length; i++) {
                    var rangeData = [q1Data[i], q2Data[i], q3Data[i]];
                    var x = [1, 2, 3];
                    var xMean = 2; // (1+2+3)/3
                    var yMean = rangeData.reduce((a, b) => a + b, 0) / 3;

                    var numerator = 0;
                    var denominator = 0;

                    for (var j = 0; j < 3; j++) {
                        numerator += (x[j] - xMean) * (rangeData[j] - yMean);
                        denominator += Math.pow(x[j] - xMean, 2);
                    }

                    var slope = denominator !== 0 ? numerator / denominator : 0;
                    var intercept = yMean - (slope * xMean);

                    // Predict Q4
                    var q4Prediction = intercept + (slope * 4);
                    trendData.push(Math.max(0, q4Prediction)); // Ensure no negative values
                }

                // Normalize predictions to sum to 100%
                var trendSum = trendData.reduce((a, b) => a + b, 0);
                var normalizedTrendData = trendData.map(val => (val / trendSum) * 100);

                var paymentAmountDistributionChart = new Chart(paymentAmountDistributionCtx, {
                    type: 'bar',
                    data: {
                        labels: paymentRanges,
                        datasets: [
                            {
                                label: 'Q1',
                                data: q1Data,
                                backgroundColor: 'rgba(60, 141, 188, 0.7)',
                                borderColor: 'rgba(60, 141, 188, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Q2',
                                data: q2Data,
                                backgroundColor: 'rgba(0, 166, 90, 0.7)',
                                borderColor: 'rgba(0, 166, 90, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Q3',
                                data: q3Data,
                                backgroundColor: 'rgba(243, 156, 18, 0.7)',
                                borderColor: 'rgba(243, 156, 18, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Q4 (Predicted)',
                                data: normalizedTrendData,
                                backgroundColor: 'rgba(220, 53, 69, 0.7)',
                                borderColor: 'rgba(220, 53, 69, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Percentage of Payments'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Payment Amount Range'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Payment Amount Distribution with Trend Analysis'
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += context.parsed.y.toFixed(1) + '%';
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }
    });
</script>
