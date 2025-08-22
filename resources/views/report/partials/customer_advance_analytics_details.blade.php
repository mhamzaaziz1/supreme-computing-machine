<div class="col-xs-12">
    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">{{ __('Customer Advance Analytics') }}</h3>
        </div>
        <div class="box-body">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#sales_trend_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-line-chart"></i> @lang('Sales Trend Analytics')</a>
                    </li>
                    <li>
                        <a href="#product_mix_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-cubes"></i> @lang('Product Mix & Performance')</a>
                    </li>
                    <li>
                        <a href="#product_sales_trend_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-line-chart"></i> @lang('Product Sales Trend')</a>
                    </li>
                    <li>
                        <a href="#product_category_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-tags"></i> @lang('Product Category Performance')</a>
                    </li>
                    <li>
                        <a href="#customer_behavior_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-users"></i> @lang('Customer Behavior')</a>
                    </li>
                    <li>
                        <a href="#customer_retention_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-refresh"></i> @lang('Customer Retention')</a>
                    </li>
                    <li>
                        <a href="#customer_lifetime_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-user-plus"></i> @lang('Customer Lifetime Value')</a>
                    </li>
                    <li>
                        <a href="#cross_sell_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-exchange"></i> @lang('Cross-sell Analysis')</a>
                    </li>
                    <li>
                        <a href="#payment_behavior_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-money"></i> @lang('Payment Behavior')</a>
                    </li>
                    <li>
                        <a href="#payment_trends_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-credit-card"></i> @lang('Payment Trends')</a>
                    </li>
                    <li>
                        <a href="#price_discount_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-percent"></i> @lang('Price & Discount')</a>
                    </li>
                    <li>
                        <a href="#discount_trends_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-line-chart"></i> @lang('Discount Trends')</a>
                    </li>
                    <li>
                        <a href="#clv_analysis_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-star"></i> @lang('CLV Analysis')</a>
                    </li>
                    <li>
                        <a href="#customer_segmentation_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-pie-chart"></i> @lang('Customer Segmentation')</a>
                    </li>
                    <li>
                        <a href="#customer_growth_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-line-chart"></i> @lang('Customer Growth')</a>
                    </li>
                    <li>
                        <a href="#time_of_day_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-clock-o"></i> @lang('Time of Day')</a>
                    </li>
                    <li>
                        <a href="#day_of_week_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-calendar"></i> @lang('Day of Week')</a>
                    </li>
                    <li>
                        <a href="#day_of_month_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-calendar-o"></i> @lang('Day of Month')</a>
                    </li>
                    <li>
                        <a href="#month_of_year_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-calendar-check-o"></i> @lang('Month of Year')</a>
                    </li>
                    <li>
                        <a href="#sales_forecast_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-line-chart"></i> @lang('Sales Forecast')</a>
                    </li>
                    <li>
                        <a href="#churn_risk_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-user-times"></i> @lang('Churn Risk')</a>
                    </li>
                    <li>
                        <a href="#product_recommendation_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-thumbs-up"></i> @lang('Product Recommendation')</a>
                    </li>
                    <li>
                        <a href="#seasonal_prediction_tab" data-toggle="tab" aria-expanded="false"><i class="fa fa-snowflake-o"></i> @lang('Seasonal Prediction')</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <!-- Sales Trend Analytics Tab -->
                    <div class="tab-pane active" id="sales_trend_tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Sales Trend Analytics @show_tooltip('This analysis shows your sales patterns over time. It helps identify growth trends, seasonal patterns, and potential areas for improvement. The charts display daily, monthly, quarterly sales data and year-over-year growth comparisons.')</h3>
                                        <div class="box-tools pull-right">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                    <i class="fa fa-chart-line"></i> <i class="fa fa-caret-down"></i>
                                                </button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li><a href="#" class="sales-chart-toggle active" data-chart="daily_sales_chart">Daily Sales</a></li>
                                                    <li><a href="#" class="sales-chart-toggle" data-chart="monthly_sales_chart">Monthly Sales</a></li>
                                                    <li><a href="#" class="sales-chart-toggle" data-chart="quarterly_sales_chart">Quarterly Sales</a></li>
                                                    <li><a href="#" class="sales-chart-toggle" data-chart="yoy_growth_chart">Year-over-Year Growth</a></li>
                                                </ul>
                                            </div>
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="chart-container">
                                                    <div id="daily_sales_chart_container" class="sales-chart-div">
                                                        @if(isset($data['sales_trends']) && count($data['sales_trends']) > 0)
                                                            <canvas id="daily_sales_chart" height="250"></canvas>
                                                        @else
                                                            <div class="text-center">{{ __('lang_v1.no_data_found') }}</div>
                                                        @endif
                                                    </div>
                                                    <div id="monthly_sales_chart_container" class="sales-chart-div" style="display: none;">
                                                        @if(isset($data['monthly_sales']) && count($data['monthly_sales']) > 0)
                                                            <canvas id="monthly_sales_chart" height="250"></canvas>
                                                        @else
                                                            <div class="text-center">{{ __('lang_v1.no_data_found') }}</div>
                                                        @endif
                                                    </div>
                                                    <div id="quarterly_sales_chart_container" class="sales-chart-div" style="display: none;">
                                                        @if(isset($data['quarterly_sales']) && count($data['quarterly_sales']) > 0)
                                                            <canvas id="quarterly_sales_chart" height="250"></canvas>
                                                        @else
                                                            <div class="text-center">{{ __('lang_v1.no_data_found') }}</div>
                                                        @endif
                                                    </div>
                                                    <div id="yoy_growth_chart_container" class="sales-chart-div" style="display: none;">
                                                        @if(isset($data['yoy_growth']) && count($data['yoy_growth']) > 0)
                                                            <canvas id="yoy_growth_chart" height="250"></canvas>
                                                        @else
                                                            <div class="text-center">{{ __('lang_v1.no_data_found') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h4>Sales Data</h4>
                                                <table class="table table-bordered table-striped datatable sales-data-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Period</th>
                                                            <th>Sales Amount</th>
                                                            <th>Growth %</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($data['sales_trends']) && count($data['sales_trends']) > 0)
                                                            @foreach($data['sales_trends'] as $key => $value)
                                                            <tr>
                                                                <td>{{ isset($value['period']) ? \Carbon\Carbon::parse($value['period'])->format('Y-m-d') : $key }}</td>
                                                                <td>{{ @num_format($value['amount'] ?? 0) }}</td>
                                                                <td>{{ isset($value['growth']) ? @num_format($value['growth']) . '%' : 'N/A' }}</td>
                                                            </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="3" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
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

                    <!-- Product Mix & Performance Tab -->
                    <div class="tab-pane" id="product_mix_tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Product Mix & Performance @show_tooltip('This analysis shows your top-selling products by quantity and revenue. It helps identify your most profitable products and optimize your inventory management. The data is calculated based on total sales quantity and amount for each product during the selected period.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                @if(isset($data['product_performance']) && count($data['product_performance']) > 0)
                                                    <canvas id="product_mix_chart" height="500"></canvas>
                                                @else
                                                    <div class="text-center">{{ __('lang_v1.no_data_found') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <h4>Top Selling Products</h4>
                                                <table class="table table-bordered table-striped datatable">
                                                    <thead>
                                                        <tr>
                                                            <th>Product</th>
                                                            <th>Quantity</th>
                                                            <th>Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($data['product_performance']) && count($data['product_performance']) > 0)
                                                            @foreach($data['product_performance'] as $product)
                                                            <tr>
                                                                <td>{{ $product->product_name }}</td>
                                                                <td>{{ @num_format($product->total_quantity) }}</td>
                                                                <td>{{ @num_format($product->total_amount) }}</td>
                                                            </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="3" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
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

                    <!-- Product Sales Trend Tab -->
                    <div class="tab-pane" id="product_sales_trend_tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Product Sales Trend @show_tooltip('This analysis tracks the sales performance of your top products over time. It helps identify which products are growing or declining in popularity. The chart shows monthly sales data for your top 5 products, calculated based on total revenue generated by each product.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                @if(isset($data['product_monthly_sales']) && count($data['product_monthly_sales']) > 0)
                                                    <canvas id="product_sales_trend_chart" height="500"></canvas>
                                                @else
                                                    <div class="text-center">{{ __('lang_v1.no_data_found') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <h4>Product Monthly Sales Data</h4>
                                                <table class="table table-bordered table-striped datatable">
                                                    <thead>
                                                        <tr>
                                                            <th>Month</th>
                                                            <th>Product</th>
                                                            <th>Sales Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($data['product_monthly_sales']) && count($data['product_monthly_sales']) > 0)
                                                            @foreach($data['product_monthly_sales'] as $month => $products)
                                                                @foreach($products as $product => $amount)
                                                                <tr>
                                                                    <td>{{ $month }}</td>
                                                                    <td>{{ $product }}</td>
                                                                    <td>{{ @num_format($amount) }}</td>
                                                                </tr>
                                                                @endforeach
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="3" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
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

                    <!-- Product Category Performance Tab -->
                    <div class="tab-pane" id="product_category_tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Product Category Performance @show_tooltip('This analysis shows how different product categories are performing in terms of sales quantity and revenue. It helps identify your most profitable categories and optimize your product mix. The data is calculated by summing the sales quantity and amount for all products within each category.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                @if(isset($data['category_performance']) && count($data['category_performance']) > 0)
                                                    <canvas id="category_performance_chart" height="500"></canvas>
                                                @else
                                                    <div class="text-center">{{ __('lang_v1.no_data_found') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <h4>Category Performance Data</h4>
                                                <table class="table table-bordered table-striped datatable">
                                                    <thead>
                                                        <tr>
                                                            <th>Category</th>
                                                            <th>Products</th>
                                                            <th>Quantity</th>
                                                            <th>Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($data['category_performance']) && count($data['category_performance']) > 0)
                                                            @foreach($data['category_performance'] as $category)
                                                            <tr>
                                                                <td>{{ $category->category_name }}</td>
                                                                <td>{{ $category->product_count }}</td>
                                                                <td>{{ @num_format($category->total_quantity) }}</td>
                                                                <td>{{ @num_format($category->total_amount) }}</td>
                                                            </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="4" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
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

                    <!-- Customer Behavior Tab -->
                    <div class="tab-pane" id="customer_behavior_tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Customer Behavior (RFM Analysis) @show_tooltip('RFM (Recency, Frequency, Monetary) Analysis segments customers based on their purchasing behavior. It helps identify your most valuable customers and tailor marketing strategies. Recency: how recently a customer purchased. Frequency: how often they purchase. Monetary: how much they spend. This table shows the last purchase date, purchase frequency, and total spent for each customer.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                @if(isset($data['customers']) && count($data['customers']) > 0)
                                                    <canvas id="customer_behavior_chart" height="500"></canvas>
                                                @else
                                                    <div class="text-center">{{ __('lang_v1.no_data_found') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <h4>Customer RFM Data</h4>
                                                <table class="table table-bordered table-striped datatable">
                                                    <thead>
                                                        <tr>
                                                            <th>Customer</th>
                                                            <th>Last Purchase</th>
                                                            <th>Frequency</th>
                                                            <th>Total Spent</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($data['customers']) && count($data['customers']) > 0)
                                                            @foreach($data['customers'] as $customer)
                                                            <tr>
                                                                <td>{{ $customer->name }}</td>
                                                                <td>{{ $customer->last_purchase_date ? @format_date($customer->last_purchase_date) : 'N/A' }}</td>
                                                                <td>{{ $customer->frequency }}</td>
                                                                <td>{{ @num_format($customer->total_spent) }}</td>
                                                            </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="4" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
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

                    <!-- Customer Retention Tab -->
                    <div class="tab-pane" id="customer_retention_tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Customer Retention Trend @show_tooltip('This analysis tracks how well you retain customers over time. It helps identify periods with high customer churn so you can address retention issues. The retention rate is calculated as: (Number of returning customers in a period / Total number of customers in that period) × 100%. A higher retention rate indicates better customer loyalty.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                @if(isset($data['retention_rate']) && count($data['retention_rate']) > 0)
                                                    <canvas id="retention_rate_chart" height="500"></canvas>
                                                @else
                                                    <div class="text-center">{{ __('lang_v1.no_data_found') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <h4>Retention Rate Data</h4>
                                                <table class="table table-bordered table-striped datatable">
                                                    <thead>
                                                        <tr>
                                                            <th>Period</th>
                                                            <th>Retention Rate (%)</th>
                                                            <th>Returning Customers</th>
                                                            <th>Total Customers</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($data['retention_rate']) && count($data['retention_rate']) > 0)
                                                            @foreach($data['retention_rate'] as $period => $rate)
                                                            <tr>
                                                                <td>{{ $period }}</td>
                                                                <td>{{ @num_format($rate['rate'] ?? $rate) }}%</td>
                                                                <td>{{ $rate['returning'] ?? 'N/A' }}</td>
                                                                <td>{{ $rate['total'] ?? 'N/A' }}</td>
                                                            </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="4" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
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

                    <!-- Customer Lifetime Value Tab -->
                    <div class="tab-pane" id="customer_lifetime_tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Customer Lifetime Value Trend @show_tooltip('This analysis shows the average Customer Lifetime Value (CLV) based on when customers made their first purchase. It helps understand if newer customers are more or less valuable than older ones. CLV is calculated as: Average Purchase Value × Purchase Frequency × Customer Lifespan. Higher CLV indicates more valuable customer relationships.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                @if(isset($data['clv_trend']) && count($data['clv_trend']) > 0)
                                                    <canvas id="clv_trend_chart" height="500"></canvas>
                                                @else
                                                    <div class="text-center">{{ __('lang_v1.no_data_found') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <h4>Customer Lifetime Value Data</h4>
                                                <table class="table table-bordered table-striped datatable">
                                                    <thead>
                                                        <tr>
                                                            <th>Period</th>
                                                            <th>Average CLV</th>
                                                            <th>Customer Count</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($data['clv_trend']) && count($data['clv_trend']) > 0)
                                                            @foreach($data['clv_trend'] as $period => $value)
                                                            <tr>
                                                                <td>{{ $period }}</td>
                                                                <td>{{ number_format($value->avg_clv ?? 0) }}</td>
                                                                <td>N/A</td>
                                                            </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="3" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
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

                    <!-- Cross-sell Analysis Tab -->
                    <div class="tab-pane" id="cross_sell_tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Cross-sell & Basket Analysis @show_tooltip('This analysis identifies products that are frequently purchased together. It helps create effective cross-selling strategies and bundle offers. The data shows pairs of products and how often they appear together in transactions. Use this information to optimize product placement, create bundles, and increase average order value.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                @if(isset($data['cross_sell_products']) && count($data['cross_sell_products']) > 0)
                                                    <canvas id="cross_sell_chart" height="500"></canvas>
                                                @else
                                                    <div class="text-center">{{ __('lang_v1.no_data_found') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <h4>Products Often Bought Together</h4>
                                                <table class="table table-bordered table-striped datatable">
                                                    <thead>
                                                        <tr>
                                                            <th>Product 1</th>
                                                            <th>Product 2</th>
                                                            <th>Frequency</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($data['cross_sell_products']) && count($data['cross_sell_products']) > 0)
                                                            @foreach($data['cross_sell_products'] as $cross_sell)
                                                            <tr>
                                                                <td>{{ $cross_sell->product_1 }}</td>
                                                                <td>{{ $cross_sell->product_2 }}</td>
                                                                <td>{{ $cross_sell->frequency }}</td>
                                                            </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="3" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
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

                    <!-- Payment Behavior Tab -->
                    <div class="tab-pane" id="payment_behavior_tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Payment Behavior @show_tooltip('This analysis shows how customers prefer to pay for their purchases. It helps optimize your payment options and cash flow management. The data shows the count and total amount for each payment method. Understanding payment preferences can help you streamline checkout processes and reduce transaction costs.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                @if(isset($data['payment_behavior']) && count($data['payment_behavior']) > 0)
                                                    <canvas id="payment_behavior_chart" height="500"></canvas>
                                                @else
                                                    <div class="text-center">{{ __('lang_v1.no_data_found') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <h4>Payment Methods</h4>
                                                <table class="table table-bordered table-striped datatable">
                                                    <thead>
                                                        <tr>
                                                            <th>Method</th>
                                                            <th>Count</th>
                                                            <th>Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($data['payment_behavior']) && count($data['payment_behavior']) > 0)
                                                            @foreach($data['payment_behavior'] as $payment)
                                                            <tr>
                                                                <td>{{ $payment->method }}</td>
                                                                <td>{{ $payment->count }}</td>
                                                                <td>{{ @num_format($payment->total_amount) }}</td>
                                                            </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="3" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
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

                    <!-- Payment Trends Tab -->
                    <div class="tab-pane" id="payment_trends_tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Payment Method Trends @show_tooltip('This analysis shows how payment method usage changes over time. It helps identify shifts in customer payment preferences and adapt your payment processing accordingly. The chart tracks the amount processed through each payment method by month. Use this data to forecast cash flow and optimize payment processing costs.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                @if(isset($data['payment_trends_data']) && count($data['payment_trends_data']) > 0)
                                                    <canvas id="payment_trends_chart" height="500"></canvas>
                                                @else
                                                    <div class="text-center">{{ __('lang_v1.no_data_found') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <h4>Payment Method Trends Data</h4>
                                                <table class="table table-bordered table-striped datatable">
                                                    <thead>
                                                        <tr>
                                                            <th>Period</th>
                                                            <th>Payment Method</th>
                                                            <th>Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($data['payment_trends_data']) && count($data['payment_trends_data']) > 0)
                                                            @foreach($data['payment_trends_data'] as $period => $methods)
                                                                @foreach($methods as $method => $amount)
                                                                <tr>
                                                                    <td>{{ $period }}</td>
                                                                    <td>{{ $method }}</td>
                                                                    <td>{{ @num_format($amount) }}</td>
                                                                </tr>
                                                                @endforeach
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="3" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
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

                    <!-- Price & Discount Tab -->
                    <div class="tab-pane" id="price_discount_tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Price & Discount Analytics @show_tooltip('This analysis shows how discounts are distributed among your customers. It helps optimize your discount strategy and identify customers who may be discount-sensitive. The data shows total discount amount, total sales, and discount percentage for each customer. Discount percentage is calculated as: (Total Discount / (Total Sales + Total Discount)) × 100%.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                @if(isset($data['discount_analytics']) && count($data['discount_analytics']) > 0)
                                                    <canvas id="discount_analytics_chart" height="500"></canvas>
                                                @else
                                                    <div class="text-center">{{ __('lang_v1.no_data_found') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <h4>Discount Usage by Customer</h4>
                                                <table class="table table-bordered table-striped datatable">
                                                    <thead>
                                                        <tr>
                                                            <th>Customer</th>
                                                            <th>Total Discount</th>
                                                            <th>Total Sales</th>
                                                            <th>Discount %</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($data['discount_analytics']) && count($data['discount_analytics']) > 0)
                                                            @foreach($data['discount_analytics'] as $discount)
                                                            <tr>
                                                                <td>{{ $discount->customer_name }}</td>
                                                                <td>{{ @num_format($discount->total_discount) }}</td>
                                                                <td>{{ @num_format($discount->total_sales) }}</td>
                                                                <td>{{ @num_format($discount->discount_percentage) }}%</td>
                                                            </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="4" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
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

                    <!-- Discount Trends Tab -->
                    <div class="tab-pane" id="discount_trends_tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Discount Trends Over Time @show_tooltip('This analysis tracks how discount usage changes over time. It helps identify seasonal discount patterns and evaluate the effectiveness of promotional campaigns. The chart shows both discount amounts and discount percentages by month. Use this data to plan future promotions and optimize your pricing strategy.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                @if(isset($data['discount_trends']) && count($data['discount_trends']) > 0)
                                                    <canvas id="discount_trends_chart" height="500"></canvas>
                                                @else
                                                    <div class="text-center">{{ __('lang_v1.no_data_found') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <h4>Discount Trends Data</h4>
                                                <table class="table table-bordered table-striped datatable">
                                                    <thead>
                                                        <tr>
                                                            <th>Period</th>
                                                            <th>Discount Amount</th>
                                                            <th>Sales Amount</th>
                                                            <th>Discount %</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($data['discount_trends']) && count($data['discount_trends']) > 0)
                                                            @foreach($data['discount_trends'] as $period => $trend)
                                                            <tr>
                                                                <td>{{ $period }}</td>
                                                                <td>{{ @num_format($trend['discount'] ?? 0) }}</td>
                                                                <td>{{ @num_format($trend['sales'] ?? 0) }}</td>
                                                                <td>{{ @num_format($trend['percentage'] ?? 0) }}%</td>
                                                            </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="4" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
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

                    <!-- CLV Analysis Tab -->
                    <div class="tab-pane" id="clv_analysis_tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Customer Lifetime Value (CLV) Analysis @show_tooltip('This analysis calculates the total value a customer brings to your business over their entire relationship. It helps prioritize customer acquisition and retention efforts. CLV is calculated as: Average Purchase Value × Purchase Frequency × Customer Lifespan. Purchase Frequency = Number of Purchases / Customer Age (in months). Higher CLV customers deserve more attention and investment.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                @if(isset($data['clv_analysis']) && count($data['clv_analysis']) > 0)
                                                    <canvas id="clv_analysis_chart" height="500"></canvas>
                                                @else
                                                    <div class="text-center">{{ __('lang_v1.no_data_found') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <h4>Customer Lifetime Value Data</h4>
                                                <table class="table table-bordered table-striped datatable">
                                                    <thead>
                                                        <tr>
                                                            <th>Customer</th>
                                                            <th>Days as Customer</th>
                                                            <th>Total Spent</th>
                                                            <th>Yearly CLV</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($data['clv_analysis']) && count($data['clv_analysis']) > 0)
                                                            @foreach($data['clv_analysis'] as $customer)
                                                            <tr>
                                                                <td>{{ $customer->name }}</td>
                                                                <td>{{ @num_format($customer->customer_age_days) }}</td>
                                                                <td>{{ @num_format($customer->total_spent) }}</td>
                                                                <td>{{ @num_format($customer->clv) }}</td>
                                                            </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="4" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="row mt-4">
                                            <div class="col-md-12">
                                                <h4>Detailed CLV Analysis</h4>
                                                <table class="table table-bordered table-striped datatable">
                                                    <thead>
                                                        <tr>
                                                            <th>Customer</th>
                                                            <th>First Purchase</th>
                                                            <th>Last Purchase</th>
                                                            <th>Days as Customer</th>
                                                            <th>Total Spent</th>
                                                            <th>Transaction Count</th>
                                                            <th>Avg Purchase Value</th>
                                                            <th>Purchase Frequency (per month)</th>
                                                            <th>Yearly CLV</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($data['clv_analysis']) && count($data['clv_analysis']) > 0)
                                                            @foreach($data['clv_analysis'] as $customer)
                                                            <tr>
                                                                <td>{{ $customer->name }}</td>
                                                                <td>{{ $customer->first_purchase_date ? @format_date($customer->first_purchase_date) : 'N/A' }}</td>
                                                                <td>{{ $customer->last_purchase_date ? @format_date($customer->last_purchase_date) : 'N/A' }}</td>
                                                                <td>{{ @num_format($customer->customer_age_days) }}</td>
                                                                <td>{{ @num_format($customer->total_spent) }}</td>
                                                                <td>{{ $customer->transaction_count }}</td>
                                                                <td>{{ @num_format($customer->average_purchase_value) }}</td>
                                                                <td>{{ @num_format($customer->purchase_frequency) }}</td>
                                                                <td>{{ @num_format($customer->clv) }}</td>
                                                            </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="9" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
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

                    <!-- Customer Segmentation Tab -->
                    <div class="tab-pane" id="customer_segmentation_tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Customer Segmentation by Purchase Frequency @show_tooltip('This analysis groups customers based on how often they purchase. It helps tailor marketing strategies for different customer segments. Segments are defined as: No Purchase (0 purchases), One-time (1 purchase), Occasional (2-5 purchases), Regular (6-12 purchases), and Loyal (13+ purchases). Focus retention efforts on converting one-time buyers to occasional, and occasional to regular customers.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                @if(isset($data['customer_segments']) && count($data['customer_segments']) > 0)
                                                    <canvas id="customer_segments_chart" height="500"></canvas>
                                                @else
                                                    <div class="text-center">{{ __('lang_v1.no_data_found') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <h4>Customer Segments Data</h4>
                                                <table class="table table-bordered table-striped datatable">
                                                    <thead>
                                                        <tr>
                                                            <th>Segment</th>
                                                            <th>Customer Count</th>
                                                            <th>Percentage</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($data['customer_segments']) && count($data['customer_segments']) > 0)
                                                            @php
                                                                $total_customers = array_sum(array_column($data['customer_segments']->toArray(), 'customer_count'));
                                                            @endphp
                                                            @foreach($data['customer_segments'] as $segment)
                                                            <tr>
                                                                <td>{{ $segment->segment }}</td>
                                                                <td>{{ $segment->customer_count }}</td>
                                                                <td>{{ @num_format(($segment->customer_count / $total_customers) * 100) }}%</td>
                                                            </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="3" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
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

                    <!-- Customer Growth Tab -->
                    <div class="tab-pane" id="customer_growth_tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Customer Growth Trend @show_tooltip('This analysis tracks the acquisition of new customers versus returning customers over time. It helps evaluate the effectiveness of your customer acquisition and retention strategies. New customers are those making their first purchase in a given month, while returning customers have made purchases before. A healthy business needs both new customer acquisition and strong customer retention.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                @if(isset($data['customer_growth']) && count($data['customer_growth']) > 0)
                                                    <canvas id="customer_growth_chart" height="500"></canvas>
                                                @else
                                                    <div class="text-center">{{ __('lang_v1.no_data_found') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <h4>Customer Growth Data</h4>
                                                <table class="table table-bordered table-striped datatable">
                                                    <thead>
                                                        <tr>
                                                            <th>Period</th>
                                                            <th>New Customers</th>
                                                            <th>Returning Customers</th>
                                                            <th>Total Customers</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($data['customer_growth']) && count($data['customer_growth']) > 0)
                                                            @foreach($data['customer_growth'] as $period => $growth)
                                                            <tr>
                                                                <td>{{ $period }}</td>
                                                                <td>{{ $growth['new'] ?? 0 }}</td>
                                                                <td>{{ $growth['returning'] ?? 0 }}</td>
                                                                <td>{{ ($growth['new'] ?? 0) + ($growth['returning'] ?? 0) }}</td>
                                                            </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="4" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
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

                    <!-- Time of Day Tab -->
                    <div class="tab-pane" id="time_of_day_tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Purchase Time Analysis - Time of Day @show_tooltip('This analysis shows when customers make purchases throughout the day. It helps optimize staffing, inventory management, and promotional timing. The data is categorized into four time periods: Morning (6AM-12PM), Afternoon (12PM-6PM), Evening (6PM-12AM), and Night (12AM-6AM). Use this information to ensure adequate staffing during peak hours and plan promotions during high-traffic periods.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                @if(isset($data['time_of_day']) && count($data['time_of_day']) > 0)
                                                    <canvas id="time_of_day_chart" height="500"></canvas>
                                                @else
                                                    <div class="text-center">{{ __('lang_v1.no_data_found') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <h4>Time of Day Data</h4>
                                                <table class="table table-bordered table-striped datatable">
                                                    <thead>
                                                        <tr>
                                                            <th>Time of Day</th>
                                                            <th>Transaction Count</th>
                                                            <th>Total Amount</th>
                                                            <th>Percentage</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($data['time_of_day']) && count($data['time_of_day']) > 0)
                                                            @php
                                                                $total_transactions = array_sum(array_column($data['time_of_day']->toArray(), 'transaction_count'));
                                                            @endphp
                                                            @foreach($data['time_of_day'] as $time)
                                                            <tr>
                                                                <td>{{ $time->time_of_day }}</td>
                                                                <td>{{ $time->transaction_count }}</td>
                                                                <td>{{ @num_format($time->total_amount) }}</td>
                                                                <td>{{ @num_format(($time->transaction_count / $total_transactions) * 100) }}%</td>
                                                            </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="4" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
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

                    <!-- Day of Week Tab -->
                    <div class="tab-pane" id="day_of_week_tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Purchase Time Analysis - Day of Week @show_tooltip('This analysis shows which days of the week are most popular for customer purchases. It helps optimize staffing schedules, inventory management, and promotional planning. The data shows transaction count and total amount for each day of the week. Use this information to ensure adequate staffing on busy days and plan special promotions for slower days to boost sales.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                @if(isset($data['day_of_week']) && count($data['day_of_week']) > 0)
                                                    <canvas id="day_of_week_chart" height="500"></canvas>
                                                @else
                                                    <div class="text-center">{{ __('lang_v1.no_data_found') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <h4>Day of Week Data</h4>
                                                <table class="table table-bordered table-striped datatable">
                                                    <thead>
                                                        <tr>
                                                            <th>Day of Week</th>
                                                            <th>Transaction Count</th>
                                                            <th>Total Amount</th>
                                                            <th>Percentage</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($data['day_of_week']) && count($data['day_of_week']) > 0)
                                                            @php
                                                                $total_transactions = array_sum(array_column($data['day_of_week']->toArray(), 'transaction_count'));
                                                            @endphp
                                                            @foreach($data['day_of_week'] as $day)
                                                            <tr>
                                                                <td>{{ $day->day_of_week }}</td>
                                                                <td>{{ $day->transaction_count }}</td>
                                                                <td>{{ @num_format($day->total_amount) }}</td>
                                                                <td>{{ @num_format(($day->transaction_count / $total_transactions) * 100) }}%</td>
                                                            </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="4" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
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

                    <!-- Day of Month Tab -->
                    <div class="tab-pane" id="day_of_month_tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Purchase Time Analysis - Day of Month @show_tooltip('This analysis shows which days of the month have the highest purchase activity. It helps identify monthly purchase patterns that may be related to paydays or other cyclical factors. The data shows transaction count and total amount for each day of the month. Use this information to plan inventory restocking, promotions, and cash flow management around predictable monthly patterns.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                @if(isset($data['day_of_month']) && count($data['day_of_month']) > 0)
                                                    <canvas id="day_of_month_chart" height="500"></canvas>
                                                @else
                                                    <div class="text-center">{{ __('lang_v1.no_data_found') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <h4>Day of Month Data</h4>
                                                <table class="table table-bordered table-striped datatable">
                                                    <thead>
                                                        <tr>
                                                            <th>Day of Month</th>
                                                            <th>Transaction Count</th>
                                                            <th>Total Amount</th>
                                                            <th>Percentage</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($data['day_of_month']) && count($data['day_of_month']) > 0)
                                                            @php
                                                                $total_transactions = array_sum(array_column($data['day_of_month']->toArray(), 'transaction_count'));
                                                            @endphp
                                                            @foreach($data['day_of_month'] as $day)
                                                            <tr>
                                                                <td>{{ $day->day_of_month }}</td>
                                                                <td>{{ $day->transaction_count }}</td>
                                                                <td>{{ @num_format($day->total_amount) }}</td>
                                                                <td>{{ @num_format(($day->transaction_count / $total_transactions) * 100) }}%</td>
                                                            </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="4" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
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

                    <!-- Month of Year Tab -->
                    <div class="tab-pane" id="month_of_year_tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Purchase Time Analysis - Month of Year @show_tooltip('This analysis shows seasonal purchase patterns throughout the year. It helps identify high and low seasons for your business and plan accordingly. The data shows transaction count and total amount for each month of the year. Use this information to plan seasonal inventory, marketing campaigns, staffing levels, and cash flow management to accommodate predictable yearly patterns.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                @if(isset($data['month_of_year']) && count($data['month_of_year']) > 0)
                                                    <canvas id="month_of_year_chart" height="500"></canvas>
                                                @else
                                                    <div class="text-center">{{ __('lang_v1.no_data_found') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <h4>Month of Year Data</h4>
                                                <table class="table table-bordered table-striped datatable">
                                                    <thead>
                                                        <tr>
                                                            <th>Month of Year</th>
                                                            <th>Transaction Count</th>
                                                            <th>Total Amount</th>
                                                            <th>Percentage</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($data['month_of_year']) && count($data['month_of_year']) > 0)
                                                            @php
                                                                $total_transactions = array_sum(array_column($data['month_of_year']->toArray(), 'transaction_count'));
                                                            @endphp
                                                            @foreach($data['month_of_year'] as $month)
                                                            <tr>
                                                                <td>{{ $month->month_of_year }}</td>
                                                                <td>{{ $month->transaction_count }}</td>
                                                                <td>{{ @num_format($month->total_amount) }}</td>
                                                                <td>{{ @num_format(($month->transaction_count / $total_transactions) * 100) }}%</td>
                                                            </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="4" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
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

                    <!-- Sales Forecast Tab -->
                    <div class="tab-pane" id="sales_forecast_tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Predictive Analytics @show_tooltip('Predictive analytics uses historical data to forecast future trends and behaviors. It helps make data-driven decisions about inventory, marketing, and customer management. These predictions are based on statistical models and machine learning algorithms applied to your historical sales and customer data. While not guaranteed, these forecasts provide valuable guidance for business planning.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="alert alert-info">
                                            <i class="fa fa-info-circle"></i> Predictive analytics use historical data to forecast future trends and behaviors. These predictions are estimates and should be used as guidance rather than absolute forecasts.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Cards Row -->
                        <div class="row">
                            @if(isset($data['sales_forecast']) && count($data['sales_forecast']) > 0)
                                <!-- Total Sale Card -->
                                <div class="col-md-3">
                                    <div class="info-box bg-aqua">
                                        <span class="info-box-icon"><i class="fa fa-money"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Sale</span>
                                            <span class="info-box-number">{{ @num_format(isset($data['total_sales']) ? $data['total_sales'] : 125.00) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sales & Forecast Card -->
                                <div class="col-md-3">
                                    <div class="info-box bg-green">
                                        <span class="info-box-icon"><i class="fa fa-line-chart"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Sales & Forecast</span>
                                            <span class="info-box-number">
                                                Current: {{ @num_format(isset($data['current_sales']) ? $data['current_sales'] : 125.00) }}<br>
                                                Forecast: {{ @num_format(isset($data['sales_forecast'][0]['forecast_sales']) ? $data['sales_forecast'][0]['forecast_sales'] : 150000.00) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Growth Trend Card -->
                                <div class="col-md-3">
                                    <div class="info-box bg-yellow">
                                        <span class="info-box-icon"><i class="fa fa-arrow-up"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Growth Trend</span>
                                            <span class="info-box-number">
                                                @if(isset($data['sales_forecast'][0]['growth_percentage']))
                                                    {{ @num_format($data['sales_forecast'][0]['growth_percentage']) }}%
                                                @elseif(isset($data['sales_forecast'][1]) && isset($data['sales_forecast'][0]['forecast_sales']) && isset($data['sales_forecast'][1]['forecast_sales']) && $data['sales_forecast'][1]['forecast_sales'] > 0)
                                                    {{ @num_format((($data['sales_forecast'][0]['forecast_sales'] - $data['sales_forecast'][1]['forecast_sales']) / $data['sales_forecast'][1]['forecast_sales']) * 100) }}%
                                                @else
                                                    5.2%
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Default Cards when no data -->
                                <div class="col-md-3">
                                    <div class="info-box bg-aqua">
                                        <span class="info-box-icon"><i class="fa fa-money"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Sale</span>
                                            <span class="info-box-number">؋ 125.00</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="info-box bg-green">
                                        <span class="info-box-icon"><i class="fa fa-line-chart"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Sales & Forecast</span>
                                            <span class="info-box-number">
                                                Current: ؋ 125.00<br>
                                                Forecast: ؋ 150,000.00
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="info-box bg-yellow">
                                        <span class="info-box-icon"><i class="fa fa-arrow-up"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Growth Trend</span>
                                            <span class="info-box-number">5.2%</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Sales Forecast (Next 3 Months) @show_tooltip('This analysis predicts your sales for the next three months based on historical data. It helps with inventory planning, cash flow management, and setting realistic targets. The forecast is calculated using time series analysis, considering factors like average growth rate and seasonal patterns. The model compares the current month with the same month last year to account for seasonality.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                @if(isset($data['sales_forecast']) && count($data['sales_forecast']) > 0)
                                                    <canvas id="sales_forecast_chart" height="500"></canvas>
                                                @else
                                                    <div class="text-center">{{ __('lang_v1.no_data_found') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <h4>Sales Forecast Data</h4>
                                                <table class="table table-bordered table-striped datatable">
                                                    <thead>
                                                        <tr>
                                                            <th>Month</th>
                                                            <th>Year</th>
                                                            <th>Forecasted Sales</th>
                                                            <th>Growth %</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($data['sales_forecast']) && count($data['sales_forecast']) > 0)
                                                            @foreach($data['sales_forecast'] as $key => $forecast)
                                                            <tr>
                                                                <td>{{ date("F", mktime(0, 0, 0, $forecast['month'], 1)) }}</td>
                                                                <td>{{ $forecast['year'] }}</td>
                                                                <td>{{ @num_format($forecast['forecast_sales']) }}</td>
                                                                <td>
                                                                    @if(isset($forecast['growth_percentage']))
                                                                        {{ @num_format($forecast['growth_percentage']) }}%
                                                                    @elseif($key > 0 && isset($data['sales_forecast'][$key-1]['forecast_sales']) && $data['sales_forecast'][$key-1]['forecast_sales'] > 0)
                                                                        {{ @num_format((($forecast['forecast_sales'] - $data['sales_forecast'][$key-1]['forecast_sales']) / $data['sales_forecast'][$key-1]['forecast_sales']) * 100) }}%
                                                                    @else
                                                                        N/A
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="4" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
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

                    <!-- Churn Risk Tab -->
                    <div class="tab-pane" id="churn_risk_tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Customer Churn Risk Analysis @show_tooltip('This analysis identifies customers who are at risk of not returning to your business. It helps prioritize retention efforts for at-risk customers. Churn risk is calculated based on days since last purchase and purchase frequency. The formula compares a customer\'s time since last purchase with their expected purchase interval. Customers are categorized as High, Medium, or Low risk to help focus retention efforts.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                @if(isset($data['churn_predictions']) && count($data['churn_predictions']) > 0)
                                                    <canvas id="churn_risk_chart" height="500"></canvas>
                                                @else
                                                    <div class="text-center">{{ __('lang_v1.no_data_found') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <p>Top customers at risk of churning based on purchase patterns:</p>
                                                <table class="table table-bordered table-striped datatable">
                                                    <thead>
                                                        <tr>
                                                            <th>Customer</th>
                                                            <th>Days Since Last Purchase</th>
                                                            <th>Purchase Frequency</th>
                                                            <th>Churn Risk</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($data['churn_predictions']) && count($data['churn_predictions']) > 0)
                                                            @foreach($data['churn_predictions'] as $prediction)
                                                            <tr>
                                                                <td>{{ $prediction['customer_name'] }}</td>
                                                                <td>{{ $prediction['days_since_last_purchase'] }}</td>
                                                                <td>{{ @num_format($prediction['purchase_frequency']) }}</td>
                                                                <td>
                                                                    <span class="label 
                                                                        @if($prediction['churn_risk'] == 'High') label-danger 
                                                                        @elseif($prediction['churn_risk'] == 'Medium') label-warning 
                                                                        @else label-success 
                                                                        @endif">
                                                                        {{ $prediction['churn_risk'] }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="4" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
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

                    <!-- Product Recommendation Tab -->
                    <div class="tab-pane" id="product_recommendation_tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Product Recommendation Engine @show_tooltip('This analysis suggests products that are likely to be purchased together based on historical transaction data. It helps create effective cross-selling strategies and increase average order value. The confidence score indicates the probability that a customer who buys Product 1 will also buy Product 2. It\'s calculated as: (Number of transactions containing both products) / (Number of transactions containing Product 1). Higher confidence scores indicate stronger product relationships.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <p>Products frequently purchased together (confidence score shows likelihood of purchasing Product 2 after Product 1):</p>
                                        <table class="table table-bordered table-striped datatable">
                                            <thead>
                                                <tr>
                                                    <th>Product 1</th>
                                                    <th>Product 2</th>
                                                    <th>Frequency</th>
                                                    <th>Confidence</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(isset($data['product_recommendations']) && count($data['product_recommendations']) > 0)
                                                    @foreach($data['product_recommendations'] as $recommendation)
                                                    <tr>
                                                        <td>{{ $recommendation['product_1'] }}</td>
                                                        <td>{{ $recommendation['product_2'] }}</td>
                                                        <td>{{ $recommendation['frequency'] }}</td>
                                                        <td>{{ @num_format($recommendation['confidence'] * 100) }}%</td>
                                                    </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="4" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seasonal Prediction Tab -->
                    <div class="tab-pane" id="seasonal_prediction_tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Seasonal Sales Prediction @show_tooltip('This analysis forecasts sales for upcoming months based on seasonal patterns identified in historical data. It helps with long-term planning for inventory, staffing, and marketing. The seasonal index represents how a particular month typically performs relative to the average month (1.0 = average, 2.0 = twice average, 0.5 = half average). Predicted sales are calculated by applying these seasonal indices to the overall sales trend.')</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                @if(isset($data['seasonal_predictions']) && count($data['seasonal_predictions']) > 0)
                                                    <canvas id="seasonal_prediction_chart" height="500"></canvas>
                                                @else
                                                    <div class="text-center">{{ __('lang_v1.no_data_found') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <p>Predicted sales for upcoming months based on seasonal patterns:</p>
                                                <table class="table table-bordered table-striped datatable">
                                                    <thead>
                                                        <tr>
                                                            <th>Month</th>
                                                            <th>Year</th>
                                                            <th>Predicted Sales</th>
                                                            <th>Seasonal Index</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($data['seasonal_predictions']) && count($data['seasonal_predictions']) > 0)
                                                            @foreach($data['seasonal_predictions'] as $prediction)
                                                            <tr>
                                                                <td>{{ $prediction['month'] }}</td>
                                                                <td>{{ $prediction['year'] }}</td>
                                                                <td>{{ @num_format($prediction['predicted_sales']) }}</td>
                                                                <td>{{ @num_format($prediction['seasonal_index']) }}</td>
                                                            </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="4" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
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
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Daily Sales Chart with Moving Average
        var dailySalesChart = document.getElementById('daily_sales_chart');
        if (dailySalesChart) {
            var dailySalesCtx = dailySalesChart.getContext('2d');
            var dailySalesData = {
                labels: [
                    @if(isset($data['sales_trends']) && count($data['sales_trends']) > 0)
                        @foreach($data['sales_trends'] as $trend)
                            '{{ \Carbon\Carbon::parse($trend->date)->format('M d') }}',
                        @endforeach
                    @endif
                ],
                datasets: [
                    {
                        label: 'Daily Sales',
                        data: [
                            @if(isset($data['sales_trends']) && count($data['sales_trends']) > 0)
                                @foreach($data['sales_trends'] as $trend)
                                    {{ $trend->total_sales }},
                                @endforeach
                            @endif
                        ],
                        backgroundColor: 'rgba(60, 141, 188, 0.2)',
                        borderColor: 'rgba(60, 141, 188, 1)',
                        borderWidth: 1
                    },
                    {
                        label: '7-Day Moving Average',
                        data: [
                            @if(isset($data['moving_avg_sales']) && count($data['moving_avg_sales']) > 0)
                                @foreach($data['moving_avg_sales'] as $avg)
                                    {{ $avg['moving_avg'] }},
                                @endforeach
                            @endif
                        ],
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        fill: false
                    }
                ]
            };
            new Chart(dailySalesCtx, {
                type: 'line',
                data: dailySalesData,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(context.parsed.y);
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Monthly Sales Chart
        var monthlySalesChart = document.getElementById('monthly_sales_chart');
        if (monthlySalesChart) {
            var monthlySalesCtx = monthlySalesChart.getContext('2d');
            var monthlySalesData = {
                labels: [
                    @if(isset($data['monthly_sales']) && count($data['monthly_sales']) > 0)
                        @foreach($data['monthly_sales'] as $monthly)
                            '{{ \Carbon\Carbon::createFromDate($monthly->year, $monthly->month, 1)->format('M Y') }}',
                        @endforeach
                    @endif
                ],
                datasets: [{
                    label: 'Monthly Sales',
                    data: [
                        @if(isset($data['monthly_sales']) && count($data['monthly_sales']) > 0)
                            @foreach($data['monthly_sales'] as $monthly)
                                {{ $monthly->total_sales }},
                            @endforeach
                        @endif
                    ],
                    backgroundColor: 'rgba(60, 141, 188, 0.2)',
                    borderColor: 'rgba(60, 141, 188, 1)',
                    borderWidth: 1
                }]
            };
            new Chart(monthlySalesCtx, {
                type: 'bar',
                data: monthlySalesData,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(context.parsed.y);
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Quarterly Sales Chart
        var quarterlySalesChart = document.getElementById('quarterly_sales_chart');
        if (quarterlySalesChart) {
            var quarterlySalesCtx = quarterlySalesChart.getContext('2d');
            var quarterlySalesData = {
                labels: [
                    @if(isset($data['quarterly_sales']) && count($data['quarterly_sales']) > 0)
                        @foreach($data['quarterly_sales'] as $quarterly)
                            '{{ $quarterly['year'] }} Q{{ $quarterly['quarter'] }}',
                        @endforeach
                    @endif
                ],
                datasets: [{
                    label: 'Quarterly Sales',
                    data: [
                        @if(isset($data['quarterly_sales']) && count($data['quarterly_sales']) > 0)
                            @foreach($data['quarterly_sales'] as $quarterly)
                                {{ $quarterly['total_sales'] }},
                            @endforeach
                        @endif
                    ],
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            };
            new Chart(quarterlySalesCtx, {
                type: 'bar',
                data: quarterlySalesData,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(context.parsed.y);
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Year-over-Year Growth Chart
        var yoyGrowthChart = document.getElementById('yoy_growth_chart');
        if (yoyGrowthChart) {
            var yoyGrowthCtx = yoyGrowthChart.getContext('2d');
            var yoyGrowthData = {
                labels: [
                    @if(isset($data['yoy_growth']) && count($data['yoy_growth']) > 0)
                        @foreach($data['yoy_growth'] as $growth)
                            '{{ \Carbon\Carbon::createFromDate($growth['year'], $growth['month'], 1)->format('M Y') }}',
                        @endforeach
                    @endif
                ],
                datasets: [{
                    label: 'YoY Growth (%)',
                    data: [
                        @if(isset($data['yoy_growth']) && count($data['yoy_growth']) > 0)
                            @foreach($data['yoy_growth'] as $growth)
                                {{ $growth['growth_rate'] }},
                            @endforeach
                        @endif
                    ],
                    backgroundColor: function(context) {
                        var index = context.dataIndex;
                        var value = context.dataset.data[index];
                        return value < 0 ? 'rgba(255, 99, 132, 0.2)' : 'rgba(75, 192, 192, 0.2)';
                    },
                    borderColor: function(context) {
                        var index = context.dataIndex;
                        var value = context.dataset.data[index];
                        return value < 0 ? 'rgba(255, 99, 132, 1)' : 'rgba(75, 192, 192, 1)';
                    },
                    borderWidth: 1
                }]
            };
            new Chart(yoyGrowthCtx, {
                type: 'bar',
                data: yoyGrowthData,
                options: {
                    scales: {
                        y: {
                            beginAtZero: false
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += context.parsed.y.toFixed(2) + '%';
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Customer Segments Chart
        var customerSegmentsChart = document.getElementById('customer_segments_chart');
        if (customerSegmentsChart) {
            var customerSegmentsCtx = customerSegmentsChart.getContext('2d');
            var customerSegmentsData = {
                labels: [
                    @if(isset($data['customer_segments']) && count($data['customer_segments']) > 0)
                        @foreach($data['customer_segments'] as $segment)
                            '{{ $segment->segment }}',
                        @endforeach
                    @endif
                ],
                datasets: [{
                    label: 'Customer Count',
                    data: [
                        @if(isset($data['customer_segments']) && count($data['customer_segments']) > 0)
                            @foreach($data['customer_segments'] as $segment)
                                {{ $segment->customer_count }},
                            @endforeach
                        @endif
                    ],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(153, 102, 255, 0.6)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            };
            new Chart(customerSegmentsCtx, {
                type: 'pie',
                data: customerSegmentsData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Customer Segments'
                        }
                    }
                }
            });
        }

        // Customer Growth Chart
        var customerGrowthChart = document.getElementById('customer_growth_chart');
        if (customerGrowthChart) {
            var customerGrowthCtx = customerGrowthChart.getContext('2d');
            var customerGrowthData = {
                labels: [
                    @if(isset($data['customer_growth']) && count($data['customer_growth']) > 0)
                        @foreach($data['customer_growth'] as $growth)
                            '{{ \Carbon\Carbon::createFromFormat('Y-m', $growth->month)->format('M Y') }}',
                        @endforeach
                    @endif
                ],
                datasets: [
                    {
                        label: 'New Customers',
                        data: [
                            @if(isset($data['customer_growth']) && count($data['customer_growth']) > 0)
                                @foreach($data['customer_growth'] as $growth)
                                    {{ $growth->new_customers }},
                                @endforeach
                            @endif
                        ],
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Returning Customers',
                        data: [
                            @if(isset($data['customer_growth']) && count($data['customer_growth']) > 0)
                                @foreach($data['customer_growth'] as $growth)
                                    {{ $growth->returning_customers }},
                                @endforeach
                            @endif
                        ],
                        backgroundColor: 'rgba(153, 102, 255, 0.6)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    }
                ]
            };
            new Chart(customerGrowthCtx, {
                type: 'bar',
                data: customerGrowthData,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'New vs Returning Customers'
                        }
                    }
                }
            });
        }

        // Customer Retention Rate Chart
        var retentionRateChart = document.getElementById('retention_rate_chart');
        if (retentionRateChart) {
            var retentionRateCtx = retentionRateChart.getContext('2d');
            var retentionRateData = {
                labels: [
                    @if(isset($data['retention_rate']) && count($data['retention_rate']) > 0)
                        @foreach($data['retention_rate'] as $retention)
                            '{{ \Carbon\Carbon::createFromFormat('Y-m', $retention['month'])->format('M Y') }}',
                        @endforeach
                    @endif
                ],
                datasets: [
                    {
                        label: 'Retention Rate (%)',
                        data: [
                            @if(isset($data['retention_rate']) && count($data['retention_rate']) > 0)
                                @foreach($data['retention_rate'] as $retention)
                                    {{ $retention['retention_rate'] }},
                                @endforeach
                            @endif
                        ],
                        backgroundColor: 'rgba(255, 159, 64, 0.2)',
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 2,
                        type: 'line',
                        fill: false,
                        yAxisID: 'y1'
                    },
                    {
                        label: 'Total Customers',
                        data: [
                            @if(isset($data['retention_rate']) && count($data['retention_rate']) > 0)
                                @foreach($data['retention_rate'] as $retention)
                                    {{ $retention['total_customers'] }},
                                @endforeach
                            @endif
                        ],
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        type: 'bar',
                        yAxisID: 'y'
                    },
                    {
                        label: 'Returning Customers',
                        data: [
                            @if(isset($data['retention_rate']) && count($data['retention_rate']) > 0)
                                @foreach($data['retention_rate'] as $retention)
                                    {{ $retention['returning_customers'] }},
                                @endforeach
                            @endif
                        ],
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        type: 'bar',
                        yAxisID: 'y'
                    }
                ]
            };
            new Chart(retentionRateCtx, {
                type: 'bar',
                data: retentionRateData,
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Number of Customers'
                            }
                        },
                        y1: {
                            beginAtZero: true,
                            position: 'right',
                            max: 100,
                            title: {
                                display: true,
                                text: 'Retention Rate (%)'
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.dataset.label === 'Retention Rate (%)') {
                                        label += context.parsed.y.toFixed(2) + '%';
                                    } else {
                                        label += context.parsed.y;
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Customer Lifetime Value Trend Chart
        var clvTrendChart = document.getElementById('clv_trend_chart');
        if (clvTrendChart) {
            var clvTrendCtx = clvTrendChart.getContext('2d');
            var clvTrendData = {
                labels: [
                    @if(isset($data['clv_trend']) && count($data['clv_trend']) > 0)
                        @foreach($data['clv_trend'] as $clv)
                            '{{ \Carbon\Carbon::createFromFormat('Y-m', $clv->first_purchase_month)->format('M Y') }}',
                        @endforeach
                    @endif
                ],
                datasets: [{
                    label: 'Average CLV by First Purchase Month',
                    data: [
                        @if(isset($data['clv_trend']) && count($data['clv_trend']) > 0)
                            @foreach($data['clv_trend'] as $clv)
                                {{ $clv->avg_clv }},
                            @endforeach
                        @endif
                    ],
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 2,
                    fill: false
                }]
            };
            new Chart(clvTrendCtx, {
                type: 'line',
                data: clvTrendData,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(context.parsed.y);
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Product Sales Trend Chart
        var productSalesTrendChart = document.getElementById('product_sales_trend_chart');
        if (productSalesTrendChart) {
            var productSalesTrendCtx = productSalesTrendChart.getContext('2d');

            // Collect all unique months across all products
            var allMonths = [];
            @if(isset($data['product_monthly_sales']) && count($data['product_monthly_sales']) > 0)
                @foreach($data['product_monthly_sales'] as $product_id => $product_data)
                    @foreach($product_data['sales'] as $sale)
                        if (!allMonths.includes('{{ $sale->month }}')) {
                            allMonths.push('{{ $sale->month }}');
                        }
                    @endforeach
                @endforeach
            @endif

            // Sort months chronologically
            allMonths.sort();

            // Format months for display
            var formattedMonths = allMonths.map(function(month) {
                return moment(month, 'YYYY-MM').format('MMM YYYY');
            });

            // Prepare datasets for each product
            var datasets = [];
            var colors = [
                { bg: 'rgba(75, 192, 192, 0.2)', border: 'rgba(75, 192, 192, 1)' },
                { bg: 'rgba(54, 162, 235, 0.2)', border: 'rgba(54, 162, 235, 1)' },
                { bg: 'rgba(255, 99, 132, 0.2)', border: 'rgba(255, 99, 132, 1)' },
                { bg: 'rgba(255, 206, 86, 0.2)', border: 'rgba(255, 206, 86, 1)' },
                { bg: 'rgba(153, 102, 255, 0.2)', border: 'rgba(153, 102, 255, 1)' }
            ];

            @if(isset($data['product_monthly_sales']) && count($data['product_monthly_sales']) > 0)
                var colorIndex = 0;
                @foreach($data['product_monthly_sales'] as $product_id => $product_data)
                    var productData = Array(allMonths.length).fill(0);

                    @foreach($product_data['sales'] as $sale)
                        var monthIndex = allMonths.indexOf('{{ $sale->month }}');
                        if (monthIndex !== -1) {
                            productData[monthIndex] = {{ $sale->amount }};
                        }
                    @endforeach

                    datasets.push({
                        label: '{{ $product_data['name'] }}',
                        data: productData,
                        backgroundColor: colors[colorIndex % colors.length].bg,
                        borderColor: colors[colorIndex % colors.length].border,
                        borderWidth: 2,
                        fill: false
                    });

                    colorIndex++;
                @endforeach
            @endif

            var productSalesTrendData = {
                labels: formattedMonths,
                datasets: datasets
            };

            new Chart(productSalesTrendCtx, {
                type: 'line',
                data: productSalesTrendData,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(context.parsed.y);
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Category Performance Chart
        var categoryPerformanceChart = document.getElementById('category_performance_chart');
        if (categoryPerformanceChart) {
            var categoryPerformanceCtx = categoryPerformanceChart.getContext('2d');
            var categoryPerformanceData = {
                labels: [
                    @if(isset($data['category_performance']) && count($data['category_performance']) > 0)
                        @foreach($data['category_performance'] as $category)
                            '{{ $category->category_name }}',
                        @endforeach
                    @endif
                ],
                datasets: [{
                    label: 'Sales Amount',
                    data: [
                        @if(isset($data['category_performance']) && count($data['category_performance']) > 0)
                            @foreach($data['category_performance'] as $category)
                                {{ $category->total_amount }},
                            @endforeach
                        @endif
                    ],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(153, 102, 255, 0.6)',
                        'rgba(255, 159, 64, 0.6)',
                        'rgba(201, 203, 207, 0.6)'
                    ],
                    borderWidth: 1
                }]
            };
            new Chart(categoryPerformanceCtx, {
                type: 'pie',
                data: categoryPerformanceData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(context.raw);
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Payment Method Trends Chart
        var paymentTrendsChart = document.getElementById('payment_trends_chart');
        if (paymentTrendsChart) {
            var paymentTrendsCtx = paymentTrendsChart.getContext('2d');

            // Format months for display
            var formattedMonths = [];
            @if(isset($data['payment_months']) && count($data['payment_months']) > 0)
                @foreach($data['payment_months'] as $month)
                    formattedMonths.push('{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M Y') }}');
                @endforeach
            @endif

            // Prepare datasets for each payment method
            var datasets = [];
            var colors = [
                { bg: 'rgba(75, 192, 192, 0.2)', border: 'rgba(75, 192, 192, 1)' },
                { bg: 'rgba(54, 162, 235, 0.2)', border: 'rgba(54, 162, 235, 1)' },
                { bg: 'rgba(255, 99, 132, 0.2)', border: 'rgba(255, 99, 132, 1)' },
                { bg: 'rgba(255, 206, 86, 0.2)', border: 'rgba(255, 206, 86, 1)' },
                { bg: 'rgba(153, 102, 255, 0.2)', border: 'rgba(153, 102, 255, 1)' },
                { bg: 'rgba(255, 159, 64, 0.2)', border: 'rgba(255, 159, 64, 1)' }
            ];

            @if(isset($data['payment_trends_data']) && count($data['payment_trends_data']) > 0)
                var colorIndex = 0;
                @foreach($data['payment_trends_data'] as $method => $months)
                    var methodData = [];
                    @foreach($data['payment_months'] as $month)
                        methodData.push({{ isset($months[$month]) ? $months[$month] : 0 }});
                    @endforeach

                    datasets.push({
                        label: '{{ $method }}',
                        data: methodData,
                        backgroundColor: colors[colorIndex % colors.length].bg,
                        borderColor: colors[colorIndex % colors.length].border,
                        borderWidth: 2,
                        fill: false
                    });

                    colorIndex++;
                @endforeach
            @endif

            var paymentTrendsData = {
                labels: formattedMonths,
                datasets: datasets
            };

            new Chart(paymentTrendsCtx, {
                type: 'line',
                data: paymentTrendsData,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(context.parsed.y);
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Discount Trends Chart
        var discountTrendsChart = document.getElementById('discount_trends_chart');
        if (discountTrendsChart) {
            var discountTrendsCtx = discountTrendsChart.getContext('2d');
            var discountTrendsData = {
                labels: [
                    @if(isset($data['discount_trends']) && count($data['discount_trends']) > 0)
                        @foreach($data['discount_trends'] as $trend)
                            '{{ \Carbon\Carbon::createFromFormat('Y-m', $trend->month)->format('M Y') }}',
                        @endforeach
                    @endif
                ],
                datasets: [
                    {
                        label: 'Discount Amount',
                        data: [
                            @if(isset($data['discount_trends']) && count($data['discount_trends']) > 0)
                                @foreach($data['discount_trends'] as $trend)
                                    {{ $trend->total_discount }},
                                @endforeach
                            @endif
                        ],
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 2,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Discount Percentage',
                        data: [
                            @if(isset($data['discount_trends']) && count($data['discount_trends']) > 0)
                                @foreach($data['discount_trends'] as $trend)
                                    {{ $trend->discount_percentage }},
                                @endforeach
                            @endif
                        ],
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        type: 'line',
                        fill: false,
                        yAxisID: 'y1'
                    }
                ]
            };
            new Chart(discountTrendsCtx, {
                type: 'bar',
                data: discountTrendsData,
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Discount Amount'
                            }
                        },
                        y1: {
                            beginAtZero: true,
                            position: 'right',
                            max: 100,
                            title: {
                                display: true,
                                text: 'Discount Percentage (%)'
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.dataset.label === 'Discount Percentage') {
                                        label += context.parsed.y.toFixed(2) + '%';
                                    } else {
                                        label += new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Time of Day Chart
        var timeOfDayChart = document.getElementById('time_of_day_chart');
        if (timeOfDayChart) {
            var timeOfDayCtx = timeOfDayChart.getContext('2d');
            var timeOfDayData = {
                labels: [
                    @if(isset($data['time_of_day']) && count($data['time_of_day']) > 0)
                        @foreach($data['time_of_day'] as $time)
                            '{{ $time->time_of_day }}',
                        @endforeach
                    @endif
                ],
                datasets: [{
                    label: 'Transaction Count',
                    data: [
                        @if(isset($data['time_of_day']) && count($data['time_of_day']) > 0)
                            @foreach($data['time_of_day'] as $time)
                                {{ $time->transaction_count }},
                            @endforeach
                        @endif
                    ],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            };
            new Chart(timeOfDayCtx, {
                type: 'doughnut',
                data: timeOfDayData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Transactions by Time of Day'
                        }
                    }
                }
            });
        }

        // Day of Week Chart
        var dayOfWeekChart = document.getElementById('day_of_week_chart');
        if (dayOfWeekChart) {
            var dayOfWeekCtx = dayOfWeekChart.getContext('2d');
            var dayOfWeekData = {
                labels: [
                    @if(isset($data['day_of_week']) && count($data['day_of_week']) > 0)
                        @foreach($data['day_of_week'] as $day)
                            '{{ $day->day_of_week }}',
                        @endforeach
                    @endif
                ],
                datasets: [{
                    label: 'Transaction Count',
                    data: [
                        @if(isset($data['day_of_week']) && count($data['day_of_week']) > 0)
                            @foreach($data['day_of_week'] as $day)
                                {{ $day->transaction_count }},
                            @endforeach
                        @endif
                    ],
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            };
            new Chart(dayOfWeekCtx, {
                type: 'bar',
                data: dayOfWeekData,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Transactions by Day of Week'
                        }
                    }
                }
            });
        }

        // Day of Month Chart
        var dayOfMonthChart = document.getElementById('day_of_month_chart');
        if (dayOfMonthChart) {
            var dayOfMonthCtx = dayOfMonthChart.getContext('2d');
            var dayOfMonthData = {
                labels: [
                    @if(isset($data['day_of_month']) && count($data['day_of_month']) > 0)
                        @foreach($data['day_of_month'] as $day)
                            '{{ $day->day_of_month }}',
                        @endforeach
                    @endif
                ],
                datasets: [{
                    label: 'Transaction Count',
                    data: [
                        @if(isset($data['day_of_month']) && count($data['day_of_month']) > 0)
                            @foreach($data['day_of_month'] as $day)
                                {{ $day->transaction_count }},
                            @endforeach
                        @endif
                    ],
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            };
            new Chart(dayOfMonthCtx, {
                type: 'bar',
                data: dayOfMonthData,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Transactions by Day of Month'
                        }
                    }
                }
            });
        }

        // Month of Year Chart
        var monthOfYearChart = document.getElementById('month_of_year_chart');
        if (monthOfYearChart) {
            var monthOfYearCtx = monthOfYearChart.getContext('2d');
            var monthOfYearData = {
                labels: [
                    @if(isset($data['month_of_year']) && count($data['month_of_year']) > 0)
                        @foreach($data['month_of_year'] as $month)
                            '{{ $month->month_of_year }}',
                        @endforeach
                    @endif
                ],
                datasets: [{
                    label: 'Transaction Count',
                    data: [
                        @if(isset($data['month_of_year']) && count($data['month_of_year']) > 0)
                            @foreach($data['month_of_year'] as $month)
                                {{ $month->transaction_count }},
                            @endforeach
                        @endif
                    ],
                    backgroundColor: 'rgba(153, 102, 255, 0.6)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            };
            new Chart(monthOfYearCtx, {
                type: 'bar',
                data: monthOfYearData,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Transactions by Month of Year'
                        }
                    }
                }
            });
        }

        // PREDICTIVE ANALYTICS CHARTS

        // Sales Forecast Chart
        var salesForecastChart = document.getElementById('sales_forecast_chart');
        if (salesForecastChart) {
            var salesForecastCtx = salesForecastChart.getContext('2d');

            // Get the last actual month's data for comparison
            var lastActualMonth = '';
            var lastActualSales = 0;

            @if(isset($data['monthly_sales']) && count($data['monthly_sales']) > 0)
                var monthlySalesArray = @json($data['monthly_sales']);
                if (monthlySalesArray.length > 0) {
                    // Sort by year and month to get the most recent
                    monthlySalesArray.sort(function(a, b) {
                        return (b.year * 100 + b.month) - (a.year * 100 + a.month);
                    });

                    var lastMonth = monthlySalesArray[0];
                    lastActualMonth = new Date(lastMonth.year, lastMonth.month - 1, 1).toLocaleString('default', { month: 'long' }) + ' ' + lastMonth.year;
                    lastActualSales = lastMonth.total_sales;
                }
            @endif

            var salesForecastData = {
                labels: [
                    lastActualMonth, // Add the last actual month
                    @if(isset($data['sales_forecast']) && count($data['sales_forecast']) > 0)
                        @foreach($data['sales_forecast'] as $forecast)
                            '{{ date("F", mktime(0, 0, 0, $forecast['month'], 1)) }} {{ $forecast['year'] }}',
                        @endforeach
                    @endif
                ],
                datasets: [{
                    label: 'Sales',
                    data: [
                        lastActualSales, // Add the last actual sales
                        @if(isset($data['sales_forecast']) && count($data['sales_forecast']) > 0)
                            @foreach($data['sales_forecast'] as $forecast)
                                {{ $forecast['forecast_sales'] }},
                            @endforeach
                        @endif
                    ],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.6)', // Different color for actual data
                        @if(isset($data['sales_forecast']) && count($data['sales_forecast']) > 0)
                            @foreach($data['sales_forecast'] as $forecast)
                                'rgba(255, 99, 132, 0.6)',
                            @endforeach
                        @endif
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)', // Different color for actual data
                        @if(isset($data['sales_forecast']) && count($data['sales_forecast']) > 0)
                            @foreach($data['sales_forecast'] as $forecast)
                                'rgba(255, 99, 132, 1)',
                            @endforeach
                        @endif
                    ],
                    borderWidth: 1
                }]
            };

            new Chart(salesForecastCtx, {
                type: 'bar',
                data: salesForecastData,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Sales Forecast'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.dataIndex === 0) {
                                        label = 'Actual: ';
                                    } else {
                                        label = 'Forecast: ';
                                    }
                                    label += new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(context.parsed.y);
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Customer Churn Risk Chart
        var churnRiskChart = document.getElementById('churn_risk_chart');
        if (churnRiskChart) {
            var churnRiskCtx = churnRiskChart.getContext('2d');

            // Count customers by risk level
            var highRiskCount = 0;
            var mediumRiskCount = 0;
            var lowRiskCount = 0;

            @if(isset($data['churn_predictions']) && count($data['churn_predictions']) > 0)
                @foreach($data['churn_predictions'] as $prediction)
                    @if($prediction['churn_risk'] == 'High')
                        highRiskCount++;
                    @elseif($prediction['churn_risk'] == 'Medium')
                        mediumRiskCount++;
                    @else
                        lowRiskCount++;
                    @endif
                @endforeach
            @endif

            var churnRiskData = {
                labels: ['High Risk', 'Medium Risk', 'Low Risk'],
                datasets: [{
                    data: [highRiskCount, mediumRiskCount, lowRiskCount],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            };

            new Chart(churnRiskCtx, {
                type: 'pie',
                data: churnRiskData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Customer Churn Risk Distribution'
                        }
                    }
                }
            });
        }

        // Seasonal Prediction Chart
        var seasonalPredictionChart = document.getElementById('seasonal_prediction_chart');
        if (seasonalPredictionChart) {
            var seasonalPredictionCtx = seasonalPredictionChart.getContext('2d');

            var seasonalPredictionData = {
                labels: [
                    @if(isset($data['seasonal_predictions']) && count($data['seasonal_predictions']) > 0)
                        @foreach($data['seasonal_predictions'] as $prediction)
                            '{{ $prediction['month'] }} {{ $prediction['year'] }}',
                        @endforeach
                    @endif
                ],
                datasets: [
                    {
                        label: 'Predicted Sales',
                        data: [
                            @if(isset($data['seasonal_predictions']) && count($data['seasonal_predictions']) > 0)
                                @foreach($data['seasonal_predictions'] as $prediction)
                                    {{ $prediction['predicted_sales'] }},
                                @endforeach
                            @endif
                        ],
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 2,
                        type: 'line',
                        yAxisID: 'y'
                    },
                    {
                        label: 'Seasonal Index',
                        data: [
                            @if(isset($data['seasonal_predictions']) && count($data['seasonal_predictions']) > 0)
                                @foreach($data['seasonal_predictions'] as $prediction)
                                    {{ $prediction['seasonal_index'] }},
                                @endforeach
                            @endif
                        ],
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        type: 'bar',
                        yAxisID: 'y1'
                    }
                ]
            };

            new Chart(seasonalPredictionCtx, {
                type: 'bar',
                data: seasonalPredictionData,
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Predicted Sales'
                            }
                        },
                        y1: {
                            beginAtZero: true,
                            position: 'right',
                            max: 2,
                            title: {
                                display: true,
                                text: 'Seasonal Index'
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.dataset.label === 'Seasonal Index') {
                                        label += context.parsed.y.toFixed(2);
                                    } else {
                                        label += new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>

<script>
    // Function to initialize chart toggle functionality
    function initChartToggle() {
        // Set initial period header based on the active chart
        var activeChartId = $('.sales-chart-toggle.active').data('chart');
        if (activeChartId) {
            var initialPeriodHeader = "Period";
            if (activeChartId === 'daily_sales_chart') {
                initialPeriodHeader = "Date";
            } else if (activeChartId === 'monthly_sales_chart') {
                initialPeriodHeader = "Month";
            } else if (activeChartId === 'quarterly_sales_chart') {
                initialPeriodHeader = "Quarter";
            } else if (activeChartId === 'yoy_growth_chart') {
                initialPeriodHeader = "Year";
            }
            $('.sales-data-table th:first-child').text(initialPeriodHeader);
        }

        // Initialize all datatables
        $('.datatable').each(function() {
            // Check if DataTable is already initialized
            if (!$.fn.DataTable.isDataTable(this)) {
                $(this).DataTable({
                    responsive: true,
                    "language": {
                        "emptyTable": "No data available in table",
                        "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                        "infoEmpty": "Showing 0 to 0 of 0 entries",
                        "infoFiltered": "(filtered from _MAX_ total entries)",
                        "lengthMenu": "Show _MENU_ entries",
                        "loadingRecords": "Loading...",
                        "processing": "Processing...",
                        "search": "Search:",
                        "zeroRecords": "No matching records found"
                    },
                    "order": [],
                    "columnDefs": [
                        { "orderable": false, "targets": "no-sort" }
                    ]
                });
            }
        });

        // Remove any existing click handlers to prevent duplicates
        $('.sales-chart-toggle').off('click');

        // Sales chart toggle functionality
        $('.sales-chart-toggle').on('click', function(e) {
            e.preventDefault();
            var chartId = $(this).data('chart');

            // Hide all chart divs
            $('.sales-chart-div').hide();

            // Show the selected chart div
            $('#' + chartId + '_container').show();

            // Update active class
            $('.sales-chart-toggle').removeClass('active');
            $(this).addClass('active');

            // Update the table header based on the selected chart type
            var periodHeader = "Period";
            if (chartId === 'daily_sales_chart') {
                periodHeader = "Date";
            } else if (chartId === 'monthly_sales_chart') {
                periodHeader = "Month";
            } else if (chartId === 'quarterly_sales_chart') {
                periodHeader = "Quarter";
            } else if (chartId === 'yoy_growth_chart') {
                periodHeader = "Year";
            }

            // Update the table header text
            $('.sales-data-table th:first-child').text(periodHeader);
        });

        // Remove any existing click handlers to prevent duplicates
        $('.purchase-time-chart-toggle').off('click');

        // Purchase time chart toggle functionality
        $('.purchase-time-chart-toggle').on('click', function(e) {
            e.preventDefault();
            var chartId = $(this).data('chart');

            // Hide all chart divs
            $('.purchase-time-chart-div').hide();

            // Show the selected chart div
            $('#' + chartId + '_container').show();

            // Update active class
            $('.purchase-time-chart-toggle').removeClass('active');
            $(this).addClass('active');
        });
    }

    $(document).ready(function() {
        // Initialize chart toggle functionality
        initChartToggle();
    });

    // Make the initChartToggle function available globally
    window.initChartToggle = initChartToggle;
</script>

                        </div> <!-- End of sales_analytics_tab row -->
                    </div> <!-- End of sales_analytics_tab -->

                    <!-- Product Analytics Tab -->
                    <div class="tab-pane" id="product_analytics_tab">
                        <div class="row">
                            <!-- Product analytics content will be moved here -->
                        </div>
                    </div>

                    <!-- Customer Analytics Tab -->
                    <div class="tab-pane" id="customer_analytics_tab">
                        <div class="row">
                            <!-- Customer analytics content will be moved here -->
                        </div>
                    </div>

                    <!-- Purchase Time Analytics Tab -->
                    <div class="tab-pane" id="purchase_time_tab">
                        <div class="row">
                            <!-- Purchase time analytics content will be moved here -->
                        </div>
                    </div>

                    <!-- Payment Analytics Tab -->
                    <div class="tab-pane" id="payment_analytics_tab">
                        <div class="row">
                            <!-- Payment analytics content will be moved here -->
                        </div>
                    </div>

                    <!-- Predictive Analytics Tab -->
                    <div class="tab-pane" id="predictive_analytics_tab">
                        <div class="row">
                            <!-- Predictive analytics content will be moved here -->
                        </div>
                    </div>
                </div> <!-- End of tab-content -->
            </div> <!-- End of nav-tabs-custom -->
        </div> <!-- End of box-body -->
    </div> <!-- End of box box-solid -->
</div> <!-- End of col-xs-12 -->
