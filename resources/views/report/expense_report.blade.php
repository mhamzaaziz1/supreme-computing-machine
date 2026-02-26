@extends('layouts.app')
@section('title', __('report.expense_report'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">{{ __('report.expense_report')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row no-print">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
              {!! Form::open(['url' => action([\App\Http\Controllers\ReportController::class, 'getExpenseReport']), 'method' => 'get' ]) !!}
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                        {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('category_id', __('category.category').':') !!}
                        {!! Form::select('category', $categories, null, ['placeholder' =>
                        __('report.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'category_id']); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('trending_product_date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('date_range', null , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'trending_product_date_range', 'readonly']); !!}
                    </div>
                </div>
                <div class="col-sm-12">
                  <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-sm tw-text-white pull-right">@lang('report.apply_filters')</button>
                </div> 
                {!! Form::close() !!}
            @endcomponent
        </div>
    </div>

    <!-- Expense Analytics Tabs -->
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#basic_expense_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-bar-chart"></i> @lang('Basic Expense Analysis')</a>
            </li>
            <li>
                <a href="#category_analysis_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-pie-chart"></i> @lang('Category Analysis')</a>
            </li>
            <li>
                <a href="#vehicle_analysis_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-car"></i> @lang('Vehicle Expense Analysis')</a>
            </li>
            <li>
                <a href="#predictive_analysis_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-line-chart"></i> @lang('Predictive Analytics')</a>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Basic Expense Tab -->
            <div class="tab-pane active" id="basic_expense_tab">
                <div class="row">
                    <div class="col-xs-12">
                        @component('components.widget', ['class' => 'box-primary'])
                            {!! $chart->container() !!}
                        @endcomponent
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                    @component('components.widget', ['class' => 'box-primary'])
                        <table class="table" id="expense_report_table">
                            <thead>
                                <tr>
                                    <th>@lang( 'expense.expense_categories' )</th>
                                    <th>@lang( 'report.total_expense' )</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total_expense = 0;
                                @endphp
                                @foreach($expenses as $expense)
                                    <tr>
                                        <td>{{$expense['category'] ?? __('report.others')}}</td>
                                        <td><span class="display_currency" data-currency_symbol="true">{{$expense['total_expense']}}</span></td>
                                    </tr>
                                    @php
                                        $total_expense += $expense['total_expense'];
                                    @endphp
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td>@lang('sale.total')</td>
                                    <td><span class="display_currency" data-currency_symbol="true">{{$total_expense}}</span></td>
                                </tr>
                            </tfoot>
                        </table>
                    @endcomponent
                    </div>
                </div>
            </div>

            <!-- Category Analysis Tab -->
            <div class="tab-pane" id="category_analysis_tab">
                <div class="row">
                    <!-- Category Trend Chart -->
                    <div class="col-md-6">
                        @component('components.widget', ['class' => 'box-primary', 'title' => __('Category Expense Trends')])
                            <div class="chart-container" style="position: relative; height:300px;">
                                <canvas id="category_expense_trend_chart"></canvas>
                            </div>
                        @endcomponent
                    </div>
                    
                    <!-- Category Distribution Chart -->
                    <div class="col-md-6">
                        @component('components.widget', ['class' => 'box-primary', 'title' => __('Category Distribution')])
                            <div class="chart-container" style="position: relative; height:300px;">
                                <canvas id="category_distribution_chart"></canvas>
                            </div>
                        @endcomponent
                    </div>
                </div>
                
                <div class="row">
                    <!-- Category Growth Rate Chart -->
                    <div class="col-md-6">
                        @component('components.widget', ['class' => 'box-primary', 'title' => __('Category Growth Rate')])
                            <div class="chart-container" style="position: relative; height:300px;">
                                <canvas id="category_growth_chart"></canvas>
                            </div>
                        @endcomponent
                    </div>
                    
                    <!-- Category Comparison Chart -->
                    <div class="col-md-6">
                        @component('components.widget', ['class' => 'box-primary', 'title' => __('Category Comparison')])
                            <div class="chart-container" style="position: relative; height:300px;">
                                <canvas id="category_comparison_chart"></canvas>
                            </div>
                        @endcomponent
                    </div>
                </div>
            </div>

            <!-- Vehicle Analysis Tab -->
            <div class="tab-pane" id="vehicle_analysis_tab">
                <div class="row">
                    <!-- Vehicle Expense Trend Chart -->
                    <div class="col-md-6">
                        @component('components.widget', ['class' => 'box-primary', 'title' => __('Vehicle Expense Trends')])
                            <div class="chart-container" style="position: relative; height:300px;">
                                <canvas id="vehicle_expense_trend_chart"></canvas>
                            </div>
                        @endcomponent
                    </div>
                    
                    <!-- Vehicle Expense by Type Chart -->
                    <div class="col-md-6">
                        @component('components.widget', ['class' => 'box-primary', 'title' => __('Expense by Type')])
                            <div class="chart-container" style="position: relative; height:300px;">
                                <canvas id="vehicle_expense_by_type_chart"></canvas>
                            </div>
                        @endcomponent
                    </div>
                </div>
                
                <div class="row">
                    <!-- Vehicle Comparison Chart -->
                    <div class="col-md-6">
                        @component('components.widget', ['class' => 'box-primary', 'title' => __('Vehicle Expense Comparison')])
                            <div class="chart-container" style="position: relative; height:300px;">
                                <canvas id="vehicle_comparison_chart"></canvas>
                            </div>
                        @endcomponent
                    </div>
                    
                    <!-- Vehicle Maintenance Cost Chart -->
                    <div class="col-md-6">
                        @component('components.widget', ['class' => 'box-primary', 'title' => __('Maintenance Cost Analysis')])
                            <div class="chart-container" style="position: relative; height:300px;">
                                <canvas id="vehicle_maintenance_chart"></canvas>
                            </div>
                        @endcomponent
                    </div>
                </div>
            </div>

            <!-- Predictive Analysis Tab -->
            <div class="tab-pane" id="predictive_analysis_tab">
                <div class="row">
                    <!-- Expense Forecast Chart -->
                    <div class="col-md-6">
                        @component('components.widget', ['class' => 'box-primary', 'title' => __('Expense Forecast (Next 6 Months)')])
                            <div class="chart-container" style="position: relative; height:300px;">
                                <canvas id="expense_forecast_chart"></canvas>
                            </div>
                        @endcomponent
                    </div>
                    
                    <!-- Expense Anomaly Detection Chart -->
                    <div class="col-md-6">
                        @component('components.widget', ['class' => 'box-primary', 'title' => __('Expense Anomaly Detection')])
                            <div class="chart-container" style="position: relative; height:300px;">
                                <canvas id="expense_anomaly_chart"></canvas>
                            </div>
                        @endcomponent
                    </div>
                </div>
                
                <div class="row">
                    <!-- Category Forecast Chart -->
                    <div class="col-md-6">
                        @component('components.widget', ['class' => 'box-primary', 'title' => __('Category Expense Forecast')])
                            <div class="chart-container" style="position: relative; height:300px;">
                                <canvas id="category_forecast_chart"></canvas>
                            </div>
                        @endcomponent
                    </div>
                    
                    <!-- Vehicle Expense Forecast Chart -->
                    <div class="col-md-6">
                        @component('components.widget', ['class' => 'box-primary', 'title' => __('Vehicle Expense Forecast')])
                            <div class="chart-container" style="position: relative; height:300px;">
                                <canvas id="vehicle_forecast_chart"></canvas>
                            </div>
                        @endcomponent
                    </div>
                </div>
                
                <div class="row">
                    <!-- Expense Optimization Recommendations -->
                    <div class="col-md-12">
                        @component('components.widget', ['class' => 'box-primary', 'title' => __('Expense Optimization Recommendations')])
                            <div id="expense_recommendations">
                                <div class="alert alert-info">
                                    <h4><i class="icon fa fa-info"></i> @lang('Expense Insights')</h4>
                                    <ul id="expense_insights_list">
                                        <li>Based on historical data, your highest expense category is typically <strong id="highest_expense_category">Loading...</strong></li>
                                        <li>Your expenses show a <strong id="expense_trend_direction">Loading...</strong> trend over the past 6 months</li>
                                        <li>Vehicle maintenance costs are <strong id="maintenance_cost_status">Loading...</strong> compared to industry averages</li>
                                        <li>Predicted expense growth rate for next quarter: <strong id="predicted_growth_rate">Loading...</strong></li>
                                    </ul>
                                </div>
                                
                                <div class="alert alert-success">
                                    <h4><i class="icon fa fa-lightbulb-o"></i> @lang('Recommendations')</h4>
                                    <ul id="expense_recommendations_list">
                                        <li>Consider reviewing spending in <strong id="review_category">Loading...</strong> category which shows unusual growth</li>
                                        <li>Vehicle <strong id="high_maintenance_vehicle">Loading...</strong> has higher than average maintenance costs</li>
                                        <li>Based on seasonal patterns, consider budgeting more for <strong id="seasonal_category">Loading...</strong> in the coming months</li>
                                        <li>Potential savings of <strong id="potential_savings">Loading...</strong> could be achieved by optimizing <strong id="optimization_area">Loading...</strong></li>
                                    </ul>
                                </div>
                            </div>
                        @endcomponent
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->

@endsection

@section('javascript')
    <script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
    {!! $chart->script() !!}
    
    <script>
        $(document).ready(function() {
            // Sample data for charts - in a real implementation, this would come from the backend
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const currentMonth = new Date().getMonth();
            const last6Months = months.slice(currentMonth - 5 > 0 ? currentMonth - 5 : (currentMonth - 5 + 12), currentMonth + 1)
                .concat(months.slice(0, currentMonth - 5 <= 0 ? Math.abs(currentMonth - 5) : 0));
            const next6Months = months.slice(currentMonth + 1, currentMonth + 7)
                .concat(months.slice(0, Math.max(0, 6 - (months.length - currentMonth - 1))));
            
            // Common chart options
            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    },
                    legend: {
                        position: 'top'
                    }
                }
            };
            
            // Category Analysis Charts
            if (document.getElementById('category_expense_trend_chart')) {
                const categoryTrendCtx = document.getElementById('category_expense_trend_chart').getContext('2d');
                new Chart(categoryTrendCtx, {
                    type: 'line',
                    data: {
                        labels: last6Months,
                        datasets: [{
                            label: 'Office Supplies',
                            data: [1200, 1350, 1100, 1500, 1300, 1450],
                            borderColor: 'rgba(60, 141, 188, 1)',
                            backgroundColor: 'rgba(60, 141, 188, 0.2)',
                            fill: true
                        }, {
                            label: 'Rent',
                            data: [2000, 2000, 2000, 2100, 2100, 2100],
                            borderColor: 'rgba(0, 166, 90, 1)',
                            backgroundColor: 'rgba(0, 166, 90, 0.2)',
                            fill: true
                        }, {
                            label: 'Utilities',
                            data: [800, 850, 900, 950, 1000, 950],
                            borderColor: 'rgba(243, 156, 18, 1)',
                            backgroundColor: 'rgba(243, 156, 18, 0.2)',
                            fill: true
                        }]
                    },
                    options: {
                        ...commonOptions,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Amount'
                                }
                            }
                        },
                        plugins: {
                            ...commonOptions.plugins,
                            title: {
                                display: true,
                                text: 'Expense Trends by Category'
                            }
                        }
                    }
                });
            }
            
            if (document.getElementById('category_distribution_chart')) {
                const categoryDistributionCtx = document.getElementById('category_distribution_chart').getContext('2d');
                new Chart(categoryDistributionCtx, {
                    type: 'pie',
                    data: {
                        labels: ['Office Supplies', 'Rent', 'Utilities', 'Salaries', 'Marketing', 'Travel', 'Other'],
                        datasets: [{
                            data: [15, 25, 10, 30, 10, 5, 5],
                            backgroundColor: [
                                'rgba(60, 141, 188, 0.8)',
                                'rgba(0, 166, 90, 0.8)',
                                'rgba(243, 156, 18, 0.8)',
                                'rgba(221, 75, 57, 0.8)',
                                'rgba(0, 192, 239, 0.8)',
                                'rgba(153, 102, 255, 0.8)',
                                'rgba(149, 165, 166, 0.8)'
                            ]
                        }]
                    },
                    options: {
                        ...commonOptions,
                        plugins: {
                            ...commonOptions.plugins,
                            title: {
                                display: true,
                                text: 'Expense Distribution by Category'
                            }
                        }
                    }
                });
            }
            
            if (document.getElementById('category_growth_chart')) {
                const categoryGrowthCtx = document.getElementById('category_growth_chart').getContext('2d');
                new Chart(categoryGrowthCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Office Supplies', 'Rent', 'Utilities', 'Salaries', 'Marketing', 'Travel', 'Other'],
                        datasets: [{
                            label: 'Growth Rate (%)',
                            data: [5, 2, 8, 3, 12, -4, 1],
                            backgroundColor: [
                                'rgba(60, 141, 188, 0.8)',
                                'rgba(0, 166, 90, 0.8)',
                                'rgba(243, 156, 18, 0.8)',
                                'rgba(221, 75, 57, 0.8)',
                                'rgba(0, 192, 239, 0.8)',
                                'rgba(153, 102, 255, 0.8)',
                                'rgba(149, 165, 166, 0.8)'
                            ]
                        }]
                    },
                    options: {
                        ...commonOptions,
                        scales: {
                            y: {
                                title: {
                                    display: true,
                                    text: 'Growth Rate (%)'
                                }
                            }
                        },
                        plugins: {
                            ...commonOptions.plugins,
                            title: {
                                display: true,
                                text: 'Category Growth Rate (YoY)'
                            }
                        }
                    }
                });
            }
            
            if (document.getElementById('category_comparison_chart')) {
                const categoryComparisonCtx = document.getElementById('category_comparison_chart').getContext('2d');
                new Chart(categoryComparisonCtx, {
                    type: 'radar',
                    data: {
                        labels: ['Office Supplies', 'Rent', 'Utilities', 'Salaries', 'Marketing', 'Travel', 'Other'],
                        datasets: [{
                            label: 'Current Year',
                            data: [65, 75, 70, 80, 60, 55, 40],
                            backgroundColor: 'rgba(60, 141, 188, 0.2)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            pointBackgroundColor: 'rgba(60, 141, 188, 1)'
                        }, {
                            label: 'Previous Year',
                            data: [55, 70, 65, 75, 50, 60, 35],
                            backgroundColor: 'rgba(0, 166, 90, 0.2)',
                            borderColor: 'rgba(0, 166, 90, 1)',
                            pointBackgroundColor: 'rgba(0, 166, 90, 1)'
                        }]
                    },
                    options: {
                        ...commonOptions,
                        scales: {
                            r: {
                                angleLines: {
                                    display: true
                                }
                            }
                        },
                        plugins: {
                            ...commonOptions.plugins,
                            title: {
                                display: true,
                                text: 'Category Comparison (Current vs Previous Year)'
                            }
                        }
                    }
                });
            }
            
            // Vehicle Analysis Charts
            if (document.getElementById('vehicle_expense_trend_chart')) {
                const vehicleTrendCtx = document.getElementById('vehicle_expense_trend_chart').getContext('2d');
                new Chart(vehicleTrendCtx, {
                    type: 'line',
                    data: {
                        labels: last6Months,
                        datasets: [{
                            label: 'Vehicle 1',
                            data: [800, 750, 900, 850, 950, 1000],
                            borderColor: 'rgba(60, 141, 188, 1)',
                            backgroundColor: 'rgba(60, 141, 188, 0.2)',
                            fill: true
                        }, {
                            label: 'Vehicle 2',
                            data: [600, 650, 700, 750, 800, 850],
                            borderColor: 'rgba(0, 166, 90, 1)',
                            backgroundColor: 'rgba(0, 166, 90, 0.2)',
                            fill: true
                        }, {
                            label: 'Vehicle 3',
                            data: [400, 450, 500, 550, 600, 650],
                            borderColor: 'rgba(243, 156, 18, 1)',
                            backgroundColor: 'rgba(243, 156, 18, 0.2)',
                            fill: true
                        }]
                    },
                    options: {
                        ...commonOptions,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Amount'
                                }
                            }
                        },
                        plugins: {
                            ...commonOptions.plugins,
                            title: {
                                display: true,
                                text: 'Vehicle Expense Trends'
                            }
                        }
                    }
                });
            }
            
            if (document.getElementById('vehicle_expense_by_type_chart')) {
                const vehicleTypeCtx = document.getElementById('vehicle_expense_by_type_chart').getContext('2d');
                new Chart(vehicleTypeCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Fuel', 'Maintenance', 'Insurance', 'Repairs', 'Taxes', 'Other'],
                        datasets: [{
                            data: [40, 20, 15, 10, 10, 5],
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
                        ...commonOptions,
                        plugins: {
                            ...commonOptions.plugins,
                            title: {
                                display: true,
                                text: 'Vehicle Expenses by Type'
                            }
                        }
                    }
                });
            }
            
            if (document.getElementById('vehicle_comparison_chart')) {
                const vehicleComparisonCtx = document.getElementById('vehicle_comparison_chart').getContext('2d');
                new Chart(vehicleComparisonCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Vehicle 1', 'Vehicle 2', 'Vehicle 3', 'Vehicle 4', 'Vehicle 5'],
                        datasets: [{
                            label: 'Total Expenses',
                            data: [5200, 4800, 3500, 4200, 3800],
                            backgroundColor: 'rgba(60, 141, 188, 0.8)',
                            borderColor: 'rgba(60, 141, 188, 1)',
                            borderWidth: 1
                        }, {
                            label: 'Cost per Mile',
                            data: [0.42, 0.38, 0.35, 0.40, 0.36],
                            backgroundColor: 'rgba(0, 166, 90, 0.8)',
                            borderColor: 'rgba(0, 166, 90, 1)',
                            borderWidth: 1,
                            yAxisID: 'y1'
                        }]
                    },
                    options: {
                        ...commonOptions,
                        scales: {
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'Total Expenses'
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
                                    text: 'Cost per Mile'
                                }
                            }
                        },
                        plugins: {
                            ...commonOptions.plugins,
                            title: {
                                display: true,
                                text: 'Vehicle Expense Comparison'
                            }
                        }
                    }
                });
            }
            
            if (document.getElementById('vehicle_maintenance_chart')) {
                const maintenanceCtx = document.getElementById('vehicle_maintenance_chart').getContext('2d');
                new Chart(maintenanceCtx, {
                    type: 'line',
                    data: {
                        labels: ['0-10K', '10-20K', '20-30K', '30-40K', '40-50K', '50-60K', '60-70K', '70-80K'],
                        datasets: [{
                            label: 'Maintenance Cost',
                            data: [200, 300, 400, 600, 800, 1200, 1500, 1800],
                            borderColor: 'rgba(221, 75, 57, 1)',
                            backgroundColor: 'rgba(221, 75, 57, 0.2)',
                            fill: true
                        }, {
                            label: 'Industry Average',
                            data: [250, 350, 450, 650, 850, 1100, 1400, 1700],
                            borderColor: 'rgba(0, 166, 90, 1)',
                            backgroundColor: 'rgba(0, 166, 90, 0)',
                            borderDash: [5, 5],
                            fill: false
                        }]
                    },
                    options: {
                        ...commonOptions,
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Mileage (miles)'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Maintenance Cost'
                                }
                            }
                        },
                        plugins: {
                            ...commonOptions.plugins,
                            title: {
                                display: true,
                                text: 'Maintenance Cost vs Mileage'
                            }
                        }
                    }
                });
            }
            
            // Predictive Analysis Charts
            if (document.getElementById('expense_forecast_chart')) {
                const forecastCtx = document.getElementById('expense_forecast_chart').getContext('2d');
                new Chart(forecastCtx, {
                    type: 'line',
                    data: {
                        labels: [...last6Months, ...next6Months],
                        datasets: [{
                            label: 'Historical Expenses',
                            data: [5000, 5200, 4800, 5500, 5300, 5600, null, null, null, null, null, null],
                            borderColor: 'rgba(60, 141, 188, 1)',
                            backgroundColor: 'rgba(60, 141, 188, 0.2)',
                            fill: true
                        }, {
                            label: 'Forecasted Expenses',
                            data: [null, null, null, null, null, 5600, 5800, 6000, 5900, 6200, 6400, 6300],
                            borderColor: 'rgba(243, 156, 18, 1)',
                            backgroundColor: 'rgba(243, 156, 18, 0.2)',
                            fill: true,
                            borderDash: [5, 5]
                        }, {
                            label: 'Upper Bound',
                            data: [null, null, null, null, null, 5600, 6100, 6400, 6300, 6700, 7000, 7000],
                            borderColor: 'rgba(221, 75, 57, 0.5)',
                            backgroundColor: 'rgba(0, 0, 0, 0)',
                            fill: false,
                            borderDash: [2, 2]
                        }, {
                            label: 'Lower Bound',
                            data: [null, null, null, null, null, 5600, 5500, 5600, 5500, 5700, 5800, 5600],
                            borderColor: 'rgba(0, 166, 90, 0.5)',
                            backgroundColor: 'rgba(0, 0, 0, 0)',
                            fill: false,
                            borderDash: [2, 2]
                        }]
                    },
                    options: {
                        ...commonOptions,
                        scales: {
                            y: {
                                beginAtZero: false,
                                title: {
                                    display: true,
                                    text: 'Amount'
                                }
                            }
                        },
                        plugins: {
                            ...commonOptions.plugins,
                            title: {
                                display: true,
                                text: 'Expense Forecast (Next 6 Months)'
                            }
                        }
                    }
                });
            }
            
            if (document.getElementById('expense_anomaly_chart')) {
                const anomalyCtx = document.getElementById('expense_anomaly_chart').getContext('2d');
                new Chart(anomalyCtx, {
                    type: 'scatter',
                    data: {
                        datasets: [{
                            label: 'Normal Expenses',
                            data: [
                                {x: 1, y: 5000}, {x: 2, y: 5200}, {x: 3, y: 4800}, 
                                {x: 4, y: 5500}, {x: 5, y: 5300}, {x: 6, y: 5600},
                                {x: 7, y: 5800}, {x: 8, y: 5900}, {x: 9, y: 6000},
                                {x: 10, y: 5700}, {x: 11, y: 5500}, {x: 12, y: 5800}
                            ],
                            backgroundColor: 'rgba(60, 141, 188, 0.8)',
                            pointRadius: 6
                        }, {
                            label: 'Anomalies',
                            data: [
                                {x: 5.5, y: 7200}, {x: 9.5, y: 3800}
                            ],
                            backgroundColor: 'rgba(221, 75, 57, 0.8)',
                            pointRadius: 8,
                            pointStyle: 'triangle'
                        }]
                    },
                    options: {
                        ...commonOptions,
                        scales: {
                            x: {
                                type: 'linear',
                                position: 'bottom',
                                title: {
                                    display: true,
                                    text: 'Month'
                                },
                                ticks: {
                                    stepSize: 1
                                }
                            },
                            y: {
                                beginAtZero: false,
                                title: {
                                    display: true,
                                    text: 'Amount'
                                }
                            }
                        },
                        plugins: {
                            ...commonOptions.plugins,
                            title: {
                                display: true,
                                text: 'Expense Anomaly Detection'
                            }
                        }
                    }
                });
            }
            
            if (document.getElementById('category_forecast_chart')) {
                const categoryForecastCtx = document.getElementById('category_forecast_chart').getContext('2d');
                new Chart(categoryForecastCtx, {
                    type: 'line',
                    data: {
                        labels: next6Months,
                        datasets: [{
                            label: 'Office Supplies',
                            data: [1500, 1550, 1600, 1650, 1700, 1750],
                            borderColor: 'rgba(60, 141, 188, 1)',
                            backgroundColor: 'rgba(60, 141, 188, 0.2)',
                            fill: false
                        }, {
                            label: 'Rent',
                            data: [2100, 2100, 2100, 2200, 2200, 2200],
                            borderColor: 'rgba(0, 166, 90, 1)',
                            backgroundColor: 'rgba(0, 166, 90, 0.2)',
                            fill: false
                        }, {
                            label: 'Utilities',
                            data: [1000, 1050, 1100, 1150, 1200, 1250],
                            borderColor: 'rgba(243, 156, 18, 1)',
                            backgroundColor: 'rgba(243, 156, 18, 0.2)',
                            fill: false
                        }, {
                            label: 'Marketing',
                            data: [800, 850, 900, 950, 1000, 1050],
                            borderColor: 'rgba(221, 75, 57, 1)',
                            backgroundColor: 'rgba(221, 75, 57, 0.2)',
                            fill: false
                        }]
                    },
                    options: {
                        ...commonOptions,
                        scales: {
                            y: {
                                beginAtZero: false,
                                title: {
                                    display: true,
                                    text: 'Amount'
                                }
                            }
                        },
                        plugins: {
                            ...commonOptions.plugins,
                            title: {
                                display: true,
                                text: 'Category Expense Forecast'
                            }
                        }
                    }
                });
            }
            
            if (document.getElementById('vehicle_forecast_chart')) {
                const vehicleForecastCtx = document.getElementById('vehicle_forecast_chart').getContext('2d');
                new Chart(vehicleForecastCtx, {
                    type: 'line',
                    data: {
                        labels: next6Months,
                        datasets: [{
                            label: 'Vehicle 1',
                            data: [1050, 1100, 1150, 1200, 1250, 1300],
                            borderColor: 'rgba(60, 141, 188, 1)',
                            backgroundColor: 'rgba(60, 141, 188, 0.2)',
                            fill: false
                        }, {
                            label: 'Vehicle 2',
                            data: [900, 950, 1000, 1050, 1100, 1150],
                            borderColor: 'rgba(0, 166, 90, 1)',
                            backgroundColor: 'rgba(0, 166, 90, 0.2)',
                            fill: false
                        }, {
                            label: 'Vehicle 3',
                            data: [700, 750, 800, 850, 900, 950],
                            borderColor: 'rgba(243, 156, 18, 1)',
                            backgroundColor: 'rgba(243, 156, 18, 0.2)',
                            fill: false
                        }]
                    },
                    options: {
                        ...commonOptions,
                        scales: {
                            y: {
                                beginAtZero: false,
                                title: {
                                    display: true,
                                    text: 'Amount'
                                }
                            }
                        },
                        plugins: {
                            ...commonOptions.plugins,
                            title: {
                                display: true,
                                text: 'Vehicle Expense Forecast'
                            }
                        }
                    }
                });
            }
            
            // Populate the expense insights and recommendations
            setTimeout(function() {
                // Simulate data loading
                $('#highest_expense_category').text('Rent (25%)');
                $('#expense_trend_direction').text('slightly increasing (3.5% growth)');
                $('#maintenance_cost_status').text('5.2% higher');
                $('#predicted_growth_rate').text('4.2%');
                
                $('#review_category').text('Marketing');
                $('#high_maintenance_vehicle').text('Vehicle 1');
                $('#seasonal_category').text('Utilities');
                $('#potential_savings').text('8.5%');
                $('#optimization_area').text('fuel expenses');
            }, 1000);
        });
    </script>
@endsection