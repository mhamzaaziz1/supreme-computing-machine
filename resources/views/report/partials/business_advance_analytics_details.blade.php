<div class="col-xs-12">
    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">{{ __('Business Advance Analytics') }}</h3>
        </div>
        <div class="box-body">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#sales_overview_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-line-chart"></i> @lang('Sales Overview')</a>
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
                    <!-- Sales Overview Tab -->
                    <div class="tab-pane active" id="sales_overview_tab">
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
                    
                    <!-- Add content for other tabs following the same pattern -->
                    
                </div>
            </div>
        </div>
    </div>
</div>

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
            
            // Initialize other charts similarly
        }
    });
</script>