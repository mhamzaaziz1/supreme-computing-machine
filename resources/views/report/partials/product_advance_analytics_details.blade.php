<div class="col-xs-12">
    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">{{ __('Product Advance Analytics') }}</h3>
        </div>
        <div class="box-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#sales_analytics_tab" aria-controls="sales_analytics_tab" role="tab" data-toggle="tab">
                        <i class="fa fa-line-chart"></i> Sales Analytics
                    </a>
                </li>
                <li role="presentation">
                    <a href="#purchase_analytics_tab" aria-controls="purchase_analytics_tab" role="tab" data-toggle="tab">
                        <i class="fa fa-shopping-cart"></i> Purchase Analytics
                    </a>
                </li>
                <li role="presentation">
                    <a href="#sales_vs_purchase_tab" aria-controls="sales_vs_purchase_tab" role="tab" data-toggle="tab">
                        <i class="fa fa-balance-scale"></i> Sales vs. Purchase
                    </a>
                </li>
                <li role="presentation">
                    <a href="#margin_profitability_tab" aria-controls="margin_profitability_tab" role="tab" data-toggle="tab">
                        <i class="fa fa-money"></i> Margin & Profitability
                    </a>
                </li>
                <li role="presentation">
                    <a href="#category_brand_tab" aria-controls="category_brand_tab" role="tab" data-toggle="tab">
                        <i class="fa fa-tags"></i> Category & Brand
                    </a>
                </li>
                <li role="presentation">
                    <a href="#inventory_aging_tab" aria-controls="inventory_aging_tab" role="tab" data-toggle="tab">
                        <i class="fa fa-hourglass-half"></i> Inventory Aging
                    </a>
                </li>
                <li role="presentation">
                    <a href="#seasonal_trends_tab" aria-controls="seasonal_trends_tab" role="tab" data-toggle="tab">
                        <i class="fa fa-calendar"></i> Seasonal Trends
                    </a>
                </li>
                <li role="presentation">
                    <a href="#location_performance_tab" aria-controls="location_performance_tab" role="tab" data-toggle="tab">
                        <i class="fa fa-map-marker"></i> Location Performance
                    </a>
                </li>
                <li role="presentation">
                    <a href="#predictive_analytics_tab" aria-controls="predictive_analytics_tab" role="tab" data-toggle="tab">
                        <i class="fa fa-crystal-ball"></i> Predictive Analytics
                    </a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <!-- Sales Analytics Tab -->
                <div role="tabpanel" class="tab-pane active" id="sales_analytics_tab">
                    <div class="row">
                <!-- 1. Sales Analytics -->
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">1. Sales Analytics</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="pull-right">
                                        <div class="dropdown">
                                            <button class="btn btn-default dropdown-toggle" type="button" id="salesTrendsDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                Select Graph
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="salesTrendsDropdown">
                                                <li><a href="#" data-target="daily_sales_chart_container">Daily Sales Trends</a></li>
                                                <li><a href="#" data-target="monthly_sales_chart_container">Monthly Sales</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <h4>
                                        Sales Trends
                                        <i class="fa fa-info-circle text-info" data-toggle="tooltip" title="Shows the trend of sales over time. Daily trends show day-to-day fluctuations, while monthly trends show longer-term patterns."></i>
                                    </h4>
                                </div>
                            </div>

                            <div class="row" id="daily_sales_chart_container">
                                <div class="col-md-7">
                                    <canvas id="daily_sales_chart" height="250"></canvas>
                                </div>
                                <div class="col-md-5">
                                    <div class="box box-solid">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">Sales Trends Data</h3>
                                        </div>
                                        <div class="box-body">
                                            <p><strong>Formula:</strong> Sum of final_total grouped by transaction date</p>
                                            <p><strong>Analysis:</strong> This graph shows daily sales patterns, helping identify peak sales days and trends over time.</p>
                                            <p><strong>Insights:</strong> Use this data to optimize inventory levels and staffing based on daily sales patterns.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="monthly_sales_chart_container" style="display: none;">
                                <div class="col-md-7">
                                    <canvas id="monthly_sales_chart" height="250"></canvas>
                                </div>
                                <div class="col-md-5">
                                    <div class="box box-solid">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">Monthly Sales Data</h3>
                                        </div>
                                        <div class="box-body">
                                            <p><strong>Formula:</strong> Sum of final_total grouped by month and year</p>
                                            <p><strong>Analysis:</strong> This graph shows monthly sales trends, helping identify seasonal patterns and year-over-year growth.</p>
                                            <p><strong>Insights:</strong> Use this data for long-term planning, budgeting, and identifying seasonal trends.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Product Mix -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>
                                        Product Mix - Top-selling Products
                                        <i class="fa fa-info-circle text-info" data-toggle="tooltip" title="Shows the best-selling products by quantity and revenue. Use this to identify your most profitable products."></i>
                                    </h4>
                                    <table class="table table-bordered table-striped datatable">
                                        <thead>
                                            <tr>
                                                <th>Product <i class="fa fa-info-circle text-info" data-toggle="tooltip" title="Name of the product"></i></th>
                                                <th>Category <i class="fa fa-info-circle text-info" data-toggle="tooltip" title="Product category"></i></th>
                                                <th>Quantity <i class="fa fa-info-circle text-info" data-toggle="tooltip" title="Total quantity sold during the selected period"></i></th>
                                                <th>Amount <i class="fa fa-info-circle text-info" data-toggle="tooltip" title="Total revenue generated from this product during the selected period"></i></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($data['product_mix']))
                                                @foreach($data['product_mix'] as $product)
                                                <tr>
                                                    <td>{{ $product->product_name }}</td>
                                                    <td>{{ $product->category_name ?? 'N/A' }}</td>
                                                    <td>{{ @num_format($product->total_quantity) }}</td>
                                                    <td>{{ @num_format($product->total_amount) }}</td>
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

                            <!-- Customer Behavior (RFM) -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>
                                        Customer Behavior (RFM) - Loyal vs. at-risk vs. lost customers
                                        <i class="fa fa-info-circle text-info" data-toggle="tooltip" title="RFM (Recency, Frequency, Monetary) analysis helps identify your most valuable customers based on their purchase patterns."></i>
                                    </h4>
                                    <table class="table table-bordered table-striped datatable">
                                        <thead>
                                            <tr>
                                                <th>Customer <i class="fa fa-info-circle text-info" data-toggle="tooltip" title="Name of the customer"></i></th>
                                                <th>Last Purchase <i class="fa fa-info-circle text-info" data-toggle="tooltip" title="Date of the customer's most recent purchase (Recency)"></i></th>
                                                <th>Frequency <i class="fa fa-info-circle text-info" data-toggle="tooltip" title="Number of purchases made by the customer during the selected period"></i></th>
                                                <th>Total Spent <i class="fa fa-info-circle text-info" data-toggle="tooltip" title="Total amount spent by the customer during the selected period (Monetary)"></i></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($data['customer_behavior']))
                                                @foreach($data['customer_behavior'] as $customer)
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

                            <!-- Cross-sell & Bundle Analysis -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Cross-sell & Bundle Analysis - Products sold together</h4>

                                    <!-- Product Bundles -->
                                    <div class="col-md-6">
                                        <h5><strong>Recommended Product Bundles</strong></h5>
                                        <p>Groups of products frequently purchased together that could be bundled for promotions.</p>

                                        @if(isset($data['product_bundles']) && !empty($data['product_bundles']))
                                            <div class="nav-tabs-custom">
                                                <ul class="nav nav-tabs">
                                                    @foreach($data['product_bundles'] as $index => $bundle)
                                                        <li class="{{ $index == 0 ? 'active' : '' }}">
                                                            <a href="#bundle_{{ $index }}" data-toggle="tab">Bundle {{ $index + 1 }}</a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                                <div class="tab-content">
                                                    @foreach($data['product_bundles'] as $index => $bundle)
                                                        <div class="tab-pane {{ $index == 0 ? 'active' : '' }}" id="bundle_{{ $index }}">
                                                            <div class="box box-solid box-primary">
                                                                <div class="box-header with-border">
                                                                    <h3 class="box-title">Bundle Details</h3>
                                                                </div>
                                                                <div class="box-body">
                                                                    <p><strong>Products in Bundle:</strong></p>
                                                                    <ul>
                                                                        @foreach($bundle['products'] as $product)
                                                                            <li>{{ $product['name'] }}</li>
                                                                        @endforeach
                                                                    </ul>
                                                                    <p><strong>Purchased Together:</strong> {{ $bundle['frequency'] }} times</p>
                                                                    <p><strong>Average Confidence:</strong> {{ @num_format($bundle['avg_confidence']) }}%</p>
                                                                    <div class="alert alert-info">
                                                                        <i class="fa fa-lightbulb-o"></i> <strong>Recommendation:</strong> 
                                                                        Consider creating a product bundle with a {{ count($bundle['products']) >= 3 ? '5-10%' : '3-5%' }} discount to increase sales.
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-info">
                                                <i class="fa fa-info-circle"></i> No product bundles identified. This may be due to insufficient transaction data or low product co-occurrence.
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Cross-sell Recommendations -->
                                    <div class="col-md-6">
                                        <h5>
                                            <strong>Top Cross-sell Recommendations</strong>
                                            <i class="fa fa-info-circle text-info" data-toggle="tooltip" title="Products that are frequently purchased together, ranked by their association strength."></i>
                                        </h5>
                                        <p>Products with strong associations that are likely to be purchased together.</p>

                                        <table class="table table-bordered table-striped datatable">
                                            <thead>
                                                <tr>
                                                    <th>Product <i class="fa fa-info-circle text-info" data-toggle="tooltip" title="The primary product"></i></th>
                                                    <th>Recommended With <i class="fa fa-info-circle text-info" data-toggle="tooltip" title="The product that is frequently purchased with the primary product"></i></th>
                                                    <th>Lift <i class="fa fa-info-circle text-info" data-toggle="tooltip" title="Measure of how much more likely products are purchased together compared to if they were purchased independently. Higher values indicate stronger relationships."></i></th>
                                                    <th>Confidence <i class="fa fa-info-circle text-info" data-toggle="tooltip" title="Percentage of transactions containing the primary product that also contain the recommended product"></i></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(isset($data['cross_sell_recommendations']) && !$data['cross_sell_recommendations']->isEmpty())
                                                    @foreach($data['cross_sell_recommendations'] as $recommendation)
                                                    <tr>
                                                        <td>{{ $recommendation->product_1 }}</td>
                                                        <td>{{ $recommendation->product_2 }}</td>
                                                        <td>
                                                            {{ @num_format($recommendation->lift) }}
                                                            @if($recommendation->lift > 3)
                                                                <span class="label label-success">Strong</span>
                                                            @elseif($recommendation->lift > 1.5)
                                                                <span class="label label-info">Good</span>
                                                            @else
                                                                <span class="label label-default">Weak</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ @num_format($recommendation->confidence_percentage) }}%</td>
                                                    </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="4" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>

                                        <div class="alert alert-success">
                                            <i class="fa fa-info-circle"></i> <strong>What is Lift?</strong> 
                                            Lift measures how much more likely products are purchased together compared to if they were purchased independently. 
                                            A lift > 1 indicates a positive association, with higher values suggesting stronger relationships.
                                        </div>
                                    </div>

                                    <!-- Market Basket Visualization -->
                                    <div class="col-md-12">
                                        <h5><strong>Market Basket Visualization</strong></h5>
                                        <p>Visual representation of product relationships based on co-purchase patterns.</p>

                                        <div id="market_basket_chart" style="height: 400px;"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Discount Impact -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>
                                        Discount Impact - Sales uplift vs. margin reduction
                                        <i class="fa fa-info-circle text-info" data-toggle="tooltip" title="Analyzes how discounts affect sales volume and profit margins. Use this to optimize your discount strategy."></i>
                                    </h4>
                                    <table class="table table-bordered table-striped datatable">
                                        <thead>
                                            <tr>
                                                <th>Total Sales <i class="fa fa-info-circle text-info" data-toggle="tooltip" title="Total revenue after discounts during the selected period"></i></th>
                                                <th>Total Discount <i class="fa fa-info-circle text-info" data-toggle="tooltip" title="Total amount of discounts applied during the selected period"></i></th>
                                                <th>Discount Percentage <i class="fa fa-info-circle text-info" data-toggle="tooltip" title="Percentage of total potential revenue that was discounted"></i></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($data['discount_impact']))
                                                <tr>
                                                    <td>{{ @num_format($data['discount_impact']->total_sales) }}</td>
                                                    <td>{{ @num_format($data['discount_impact']->total_discount) }}</td>
                                                    <td>{{ @num_format($data['discount_impact']->discount_percentage) }}%</td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td colspan="3" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Profitability by Product -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>
                                        Profitability by Product - Gross margin after factoring purchase cost
                                        <i class="fa fa-info-circle text-info" data-toggle="tooltip" title="Analyzes the profitability of each product by comparing sales revenue to purchase costs. Use this to identify your most profitable products."></i>
                                    </h4>
                                    <table class="table table-bordered table-striped datatable">
                                        <thead>
                                            <tr>
                                                <th>Product <i class="fa fa-info-circle text-info" data-toggle="tooltip" title="Name of the product"></i></th>
                                                <th>Total Sales <i class="fa fa-info-circle text-info" data-toggle="tooltip" title="Total revenue generated from this product during the selected period"></i></th>
                                                <th>Total Cost <i class="fa fa-info-circle text-info" data-toggle="tooltip" title="Total purchase cost of this product during the selected period"></i></th>
                                                <th>Gross Profit <i class="fa fa-info-circle text-info" data-toggle="tooltip" title="Total Sales minus Total Cost"></i></th>
                                                <th>Profit Margin % <i class="fa fa-info-circle text-info" data-toggle="tooltip" title="Gross Profit divided by Total Sales, expressed as a percentage"></i></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($data['profitability']))
                                                @foreach($data['profitability'] as $profit)
                                                <tr>
                                                    <td>{{ $profit->product_name }}</td>
                                                    <td>{{ @num_format($profit->total_sales) }}</td>
                                                    <td>{{ @num_format($profit->total_cost) }}</td>
                                                    <td>{{ @num_format($profit->gross_profit) }}</td>
                                                    <td>{{ @num_format($profit->profit_margin) }}%</td>
                                                </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
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
                    <!-- End of Sales Analytics Tab -->

                    <!-- Purchase Analytics Tab -->
                    <div role="tabpanel" class="tab-pane" id="purchase_analytics_tab">
                        <div class="row">
                    <!-- 2. Purchase Analytics -->
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">2. Purchase Analytics</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="pull-right">
                                        <div class="dropdown">
                                            <button class="btn btn-default dropdown-toggle" type="button" id="purchaseTrendsDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                Select Graph
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="purchaseTrendsDropdown">
                                                <li><a href="#" data-target="daily_purchase_chart_container">Daily Purchase Trends</a></li>
                                                <li><a href="#" data-target="monthly_purchase_chart_container">Monthly Purchases</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <h4>
                                        Purchase Trends
                                        <i class="fa fa-info-circle text-info" data-toggle="tooltip" title="Shows the trend of purchases over time. Daily trends show day-to-day fluctuations, while monthly trends show longer-term patterns."></i>
                                    </h4>
                                </div>
                            </div>

                            <div class="row" id="daily_purchase_chart_container">
                                <div class="col-md-7">
                                    <canvas id="daily_purchase_chart" height="250"></canvas>
                                </div>
                                <div class="col-md-5">
                                    <div class="box box-solid">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">Purchase Trends Data</h3>
                                        </div>
                                        <div class="box-body">
                                            <p><strong>Formula:</strong> Sum of final_total grouped by transaction date for purchase transactions</p>
                                            <p><strong>Analysis:</strong> This graph shows daily purchase patterns, helping identify peak purchasing days and trends over time.</p>
                                            <p><strong>Insights:</strong> Use this data to optimize purchasing schedules and identify supplier delivery patterns.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="monthly_purchase_chart_container" style="display: none;">
                                <div class="col-md-7">
                                    <canvas id="monthly_purchase_chart" height="250"></canvas>
                                </div>
                                <div class="col-md-5">
                                    <div class="box box-solid">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">Monthly Purchases Data</h3>
                                        </div>
                                        <div class="box-body">
                                            <p><strong>Formula:</strong> Sum of final_total grouped by month and year for purchase transactions</p>
                                            <p><strong>Analysis:</strong> This graph shows monthly purchase trends, helping identify seasonal patterns and year-over-year changes.</p>
                                            <p><strong>Insights:</strong> Use this data for long-term inventory planning, budgeting, and identifying seasonal purchasing needs.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Supplier Performance -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Supplier Performance</h4>
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Supplier</th>
                                                <th>Total Purchase</th>
                                                <th>Transaction Count</th>
                                                <th>Avg. Lead Time (days)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($data['supplier_performance']))
                                                @foreach($data['supplier_performance'] as $supplier)
                                                <tr>
                                                    <td>{{ $supplier->supplier_name }}</td>
                                                    <td>{{ @num_format($supplier->total_purchase) }}</td>
                                                    <td>{{ $supplier->transaction_count }}</td>
                                                    <td>{{ @num_format($supplier->avg_lead_time) }}</td>
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

                            <!-- Product-Level Purchase -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Product-Level Purchase - Most purchased SKUs by spend & volume</h4>
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Quantity</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($data['product_purchases']))
                                                @foreach($data['product_purchases'] as $purchase)
                                                <tr>
                                                    <td>{{ $purchase->product_name }}</td>
                                                    <td>{{ @num_format($purchase->total_quantity) }}</td>
                                                    <td>{{ @num_format($purchase->total_amount) }}</td>
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
                                <!-- End of Purchase Analytics Tab -->

                                <!-- Sales vs Purchase Tab -->
                                <div role="tabpanel" class="tab-pane" id="sales_vs_purchase_tab">
                                    <div class="row">
                                <!-- 3. Sales vs. Purchase Alignment -->
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">3. Sales vs. Purchase Alignment</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            <!-- Demand vs. Supply Gap -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Demand vs. Supply Gap - Compare sales vs. purchase volumes</h4>
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Total Sales</th>
                                                <th>Total Purchases</th>
                                                <th>Gap (Purchases - Sales)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($data['demand_supply_gap']))
                                                @foreach($data['demand_supply_gap'] as $gap)
                                                <tr>
                                                    <td>{{ $gap['product_name'] }}</td>
                                                    <td>{{ @num_format($gap['total_sales']) }}</td>
                                                    <td>{{ @num_format($gap['total_purchases']) }}</td>
                                                    <td>{{ @num_format($gap['gap']) }}</td>
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

                            <!-- Stock Turnover Rate -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Stock Turnover Rate - Fast-moving vs. slow-moving SKUs</h4>
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Average Inventory</th>
                                                <th>Total Sales</th>
                                                <th>Turnover Rate</th>
                                                <th>Movement Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($data['stock_turnover']))
                                                @foreach($data['stock_turnover'] as $turnover)
                                                <tr>
                                                    <td>{{ $turnover['product_name'] }}</td>
                                                    <td>{{ @num_format($turnover['average_inventory']) }}</td>
                                                    <td>{{ @num_format($turnover['total_sales']) }}</td>
                                                    <td>{{ @num_format($turnover['turnover_rate']) }}</td>
                                                    <td>
                                                        @if($turnover['turnover_rate'] > 3)
                                                            <span class="label label-success">Fast Moving</span>
                                                        @else
                                                            <span class="label label-warning">Slow Moving</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
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
                                <!-- End of Sales vs Purchase Tab -->

                                <!-- Margin & Profitability Tab -->
                                <div role="tabpanel" class="tab-pane" id="margin_profitability_tab">
                                    <div class="row">
                                <!-- 4. Margin & Profitability Analytics -->
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">4. Margin & Profitability Analytics</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <p>This section is covered in the "Profitability by Product" table in the Sales Analytics section above.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                                    </div>
                                </div>
                                <!-- End of Margin & Profitability Tab -->

                                <!-- Category & Brand Tab -->
                                <div role="tabpanel" class="tab-pane" id="category_brand_tab">
                                    <div class="row">
                                <!-- 5. Category & Brand Performance -->
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">5. Category & Brand Performance</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            <!-- Sales by Category -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Sales by Category</h4>
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Category</th>
                                                <th>Total Sales</th>
                                                <th>Total Quantity</th>
                                                <th>Product Count</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($data['category_performance']) && !$data['category_performance']->isEmpty())
                                                @foreach($data['category_performance'] as $category)
                                                <tr>
                                                    <td>{{ $category->category_name ?? 'Uncategorized' }}</td>
                                                    <td>{{ @num_format($category->total_amount) }}</td>
                                                    <td>{{ @num_format($category->total_quantity) }}</td>
                                                    <td>{{ $category->product_count }}</td>
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

                            <!-- Sales by Brand -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Sales by Brand</h4>
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Brand</th>
                                                <th>Total Sales</th>
                                                <th>Total Quantity</th>
                                                <th>Product Count</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($data['brand_performance']) && !$data['brand_performance']->isEmpty())
                                                @foreach($data['brand_performance'] as $brand)
                                                <tr>
                                                    <td>{{ $brand->brand_name ?? 'No Brand' }}</td>
                                                    <td>{{ @num_format($brand->total_amount) }}</td>
                                                    <td>{{ @num_format($brand->total_quantity) }}</td>
                                                    <td>{{ $brand->product_count }}</td>
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
                                <!-- End of Category & Brand Tab -->

                                <!-- Inventory Aging Tab -->
                                <div role="tabpanel" class="tab-pane" id="inventory_aging_tab">
                                    <div class="row">
                                <!-- 6. Inventory Aging Analysis -->
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">6. Inventory Aging Analysis</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Inventory Age - How long products have been in stock</h4>
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Variation</th>
                                                <th>Quantity in Stock</th>
                                                <th>Last Purchase Date</th>
                                                <th>Days in Inventory</th>
                                                <th>Days to Stock Out</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($data['inventory_aging']) && !$data['inventory_aging']->isEmpty())
                                                @foreach($data['inventory_aging'] as $item)
                                                <tr>
                                                    <td>{{ $item->product_name }}</td>
                                                    <td>{{ $item->variation_name == 'DUMMY' ? 'Default' : $item->variation_name }}</td>
                                                    <td>{{ @num_format($item->qty_available) }}</td>
                                                    <td>{{ $item->last_purchased_date ? @format_date($item->last_purchased_date) : 'N/A' }}</td>
                                                    <td>
                                                        @if($item->days_in_inventory)
                                                            {{ $item->days_in_inventory }}
                                                            @if($item->days_in_inventory > 90)
                                                                <span class="label label-danger">Slow Moving</span>
                                                            @elseif($item->days_in_inventory > 30)
                                                                <span class="label label-warning">Moderate</span>
                                                            @else
                                                                <span class="label label-success">Fast Moving</span>
                                                            @endif
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($item->days_to_stock_out)
                                                            {{ $item->days_to_stock_out }}
                                                            @if($item->days_to_stock_out < 7)
                                                                <span class="label label-danger">Critical</span>
                                                            @elseif($item->days_to_stock_out < 30)
                                                                <span class="label label-warning">Low</span>
                                                            @else
                                                                <span class="label label-success">Adequate</span>
                                                            @endif
                                                        @else
                                                            <span class="label label-default">No Sales Data</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="6" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
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
                                <!-- End of Inventory Aging Tab -->

                                <!-- Seasonal Trends Tab -->
                                <div role="tabpanel" class="tab-pane" id="seasonal_trends_tab">
                                    <div class="row">
                                <!-- 7. Seasonal Trends Analysis -->
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">7. Seasonal Trends Analysis</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Monthly Sales Trends</h4>
                                    <canvas id="seasonal_trends_chart" height="300"></canvas>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Monthly Sales Data</h4>
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Month/Year</th>
                                                <th>Total Quantity</th>
                                                <th>Total Sales</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($data['seasonal_trends']) && !$data['seasonal_trends']->isEmpty())
                                                @foreach($data['seasonal_trends'] as $trend)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::createFromDate(is_array($trend) ? $trend['year'] : $trend->year, is_array($trend) ? $trend['month'] : $trend->month, 1)->format('M Y') }}</td>
                                                    <td>{{ @num_format(is_array($trend) ? $trend['total_quantity'] : $trend->total_quantity) }}</td>
                                                    <td>{{ @num_format(is_array($trend) ? $trend['total_amount'] : $trend->total_amount) }}</td>
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
                                <!-- End of Seasonal Trends Tab -->

                                <!-- Location Performance Tab -->
                                <div role="tabpanel" class="tab-pane" id="location_performance_tab">
                                    <div class="row">
                                <!-- 8. Product Performance by Location -->
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">8. Product Performance by Location</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Sales Performance Across Locations</h4>
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Location</th>
                                                <th>Total Sales</th>
                                                <th>Total Quantity</th>
                                                <th>Product Count</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($data['location_performance']) && !$data['location_performance']->isEmpty())
                                                @foreach($data['location_performance'] as $location)
                                                <tr>
                                                    <td>{{ $location->location_name }}</td>
                                                    <td>{{ @num_format($location->total_amount) }}</td>
                                                    <td>{{ @num_format($location->total_quantity) }}</td>
                                                    <td>{{ $location->product_count }}</td>
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
                                <!-- End of Location Performance Tab -->

                                <!-- Predictive Analytics Tab -->
                                <div role="tabpanel" class="tab-pane" id="predictive_analytics_tab">
                                    <div class="row">
                                <!-- 9. Predictive & Prescriptive Analytics -->
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">9. Predictive & Prescriptive Analytics</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            <!-- Sales Forecasting -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Sales Forecasting</h4>
                                    <p>Predictions for future sales based on historical trends and growth patterns.</p>
                                </div>

                                <!-- Overall Sales Forecast -->
                                <div class="col-md-6">
                                    <h5><strong>Overall Sales Forecast (Next 3 Months)</strong></h5>
                                    <canvas id="sales_forecast_chart" height="300"></canvas>

                                    <table class="table table-bordered table-striped mt-3">
                                        <thead>
                                            <tr>
                                                <th>Month</th>
                                                <th>Forecasted Sales</th>
                                                <th>Growth Rate</th>
                                                <th>Confidence Range</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($data['sales_forecast']) && !empty($data['sales_forecast']))
                                                @foreach($data['sales_forecast'] as $forecast)
                                                <tr>
                                                    <td>{{ $forecast['month'] }}</td>
                                                    <td>{{ @num_format($forecast['forecasted_sales']) }}</td>
                                                    <td>{{ @num_format($forecast['growth_rate']) }}%</td>
                                                    <td>
                                                        @if(isset($forecast['lower_bound']) && isset($forecast['upper_bound']))
                                                            {{ @num_format($forecast['lower_bound']) }} - {{ @num_format($forecast['upper_bound']) }}
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

                                <!-- Product-Level Forecasts -->
                                <div class="col-md-6">
                                    <h5><strong>Product-Level Sales Forecasts</strong></h5>
                                    <div class="nav-tabs-custom">
                                        <ul class="nav nav-tabs">
                                            @if(isset($data['product_forecasts']) && !empty($data['product_forecasts']))
                                                @foreach($data['product_forecasts'] as $index => $product_forecast)
                                                    <li class="{{ $index == 0 ? 'active' : '' }}">
                                                        <a href="#product_forecast_{{ $product_forecast['product_id'] }}" data-toggle="tab">
                                                            {{ \Str::limit($product_forecast['product_name'], 15) }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            @endif
                                        </ul>
                                        <div class="tab-content">
                                            @if(isset($data['product_forecasts']) && !empty($data['product_forecasts']))
                                                @foreach($data['product_forecasts'] as $index => $product_forecast)
                                                    <div class="tab-pane {{ $index == 0 ? 'active' : '' }}" id="product_forecast_{{ $product_forecast['product_id'] }}">
                                                        <h6><strong>{{ $product_forecast['product_name'] }}</strong></h6>
                                                        <table class="table table-bordered table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th>Month</th>
                                                                    <th>Forecasted Quantity</th>
                                                                    <th>Growth Rate</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($product_forecast['forecasts'] as $forecast)
                                                                <tr>
                                                                    <td>{{ $forecast['month'] }}</td>
                                                                    <td>{{ @num_format($forecast['forecasted_quantity']) }}</td>
                                                                    <td>{{ @num_format($forecast['growth_rate']) }}%</td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="tab-pane active">
                                                    <p class="text-center">{{ __('lang_v1.no_data_found') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Product Lifecycle Analysis -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Product Lifecycle Analysis</h4>
                                    <p>Identifies which stage of the product lifecycle each product is in based on sales patterns.</p>

                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>First Sale</th>
                                                <th>Age (Months)</th>
                                                <th>Total Quantity</th>
                                                <th>Avg Growth Rate</th>
                                                <th>Recent Growth</th>
                                                <th>Lifecycle Stage</th>
                                                <th>Recommendation</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($data['lifecycle_analysis']) && !empty($data['lifecycle_analysis']))
                                                @foreach($data['lifecycle_analysis'] as $lifecycle)
                                                <tr>
                                                    <td>{{ $lifecycle['product_name'] }}</td>
                                                    <td>{{ @format_date($lifecycle['first_sale_date']) }}</td>
                                                    <td>{{ $lifecycle['product_age_months'] }}</td>
                                                    <td>{{ @num_format($lifecycle['total_quantity']) }}</td>
                                                    <td>{{ @num_format($lifecycle['avg_growth_rate']) }}%</td>
                                                    <td>{{ @num_format($lifecycle['recent_growth_rate']) }}%</td>
                                                    <td>
                                                        @php
                                                            $badge_class = 'default';
                                                            if($lifecycle['lifecycle_stage'] == 'Introduction') {
                                                                $badge_class = 'info';
                                                            } elseif($lifecycle['lifecycle_stage'] == 'Growth') {
                                                                $badge_class = 'success';
                                                            } elseif($lifecycle['lifecycle_stage'] == 'Maturity') {
                                                                $badge_class = 'primary';
                                                            } elseif($lifecycle['lifecycle_stage'] == 'Decline') {
                                                                $badge_class = 'danger';
                                                            }
                                                        @endphp
                                                        <span class="label label-{{ $badge_class }}">{{ $lifecycle['lifecycle_stage'] }}</span>
                                                    </td>
                                                    <td>
                                                        @if($lifecycle['lifecycle_stage'] == 'Introduction')
                                                            Invest in marketing to increase awareness
                                                        @elseif($lifecycle['lifecycle_stage'] == 'Growth')
                                                            Ensure adequate inventory to meet growing demand
                                                        @elseif($lifecycle['lifecycle_stage'] == 'Maturity')
                                                            Consider product variations or promotions
                                                        @elseif($lifecycle['lifecycle_stage'] == 'Decline')
                                                            Reduce inventory or consider discounting
                                                        @else
                                                            Insufficient data for recommendation
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="8" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Price Elasticity Analysis -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Price Elasticity Analysis</h4>
                                    <p>Analyzes how price changes affect demand for products and provides pricing recommendations.</p>

                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Price Points</th>
                                                <th>Elasticity</th>
                                                <th>Interpretation</th>
                                                <th>Price Recommendation</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($data['price_elasticity']) && !empty($data['price_elasticity']))
                                                @foreach($data['price_elasticity'] as $elasticity)
                                                <tr>
                                                    <td>{{ $elasticity['product_name'] }}</td>
                                                    <td>
                                                        @if(isset($elasticity['price_points']) && !empty($elasticity['price_points']))
                                                            @foreach($elasticity['price_points'] as $point)
                                                                {{ @num_format($point['price']) }} (Qty: {{ @num_format($point['quantity']) }})<br>
                                                            @endforeach
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(isset($elasticity['elasticity']))
                                                            {{ @num_format($elasticity['elasticity']) }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(isset($elasticity['interpretation']))
                                                            {{ $elasticity['interpretation'] }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(isset($elasticity['price_recommendation']))
                                                            {{ $elasticity['price_recommendation'] }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Trend Detection -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Trend Detection & Emerging Products</h4>
                                    <p>Identifies emerging trends and products with significant growth or decline patterns.</p>

                                    <div class="col-md-12">
                                        @if(isset($data['trend_detection']) && !empty($data['trend_detection']))
                                            <!-- Emerging Trends Alert -->
                                            @php
                                                $emerging_trends = array_filter($data['trend_detection'], function($item) {
                                                    return $item['is_emerging'] == true;
                                                });
                                                $upward_trends = array_filter($data['trend_detection'], function($item) {
                                                    return $item['trend_direction'] == 'Upward' && $item['trend_strength'] != 'Weak';
                                                });
                                                $downward_trends = array_filter($data['trend_detection'], function($item) {
                                                    return $item['trend_direction'] == 'Downward' && $item['trend_strength'] != 'Weak';
                                                });
                                            @endphp

                                            @if(count($emerging_trends) > 0)
                                                <div class="alert alert-success">
                                                    <h4><i class="icon fa fa-line-chart"></i> Emerging Trends Detected!</h4>
                                                    <p>The following products show strong signs of emerging growth trends:</p>
                                                    <ul>
                                                        @foreach($emerging_trends as $trend)
                                                            <li>
                                                                <strong>{{ $trend['product_name'] }}</strong> - 
                                                                Recent growth: {{ @num_format($trend['recent_growth_rate']) }}%, 
                                                                Acceleration: {{ @num_format($trend['trend_acceleration']) }}%
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                    <p><strong>Recommendation:</strong> Consider increasing inventory and marketing focus for these emerging products.</p>
                                                </div>
                                            @endif

                                            <!-- Trend Analysis Table -->
                                            <div class="nav-tabs-custom">
                                                <ul class="nav nav-tabs">
                                                    <li class="active"><a href="#all_trends" data-toggle="tab">All Trends</a></li>
                                                    @if(count($upward_trends) > 0)
                                                        <li><a href="#upward_trends" data-toggle="tab">Upward Trends ({{ count($upward_trends) }})</a></li>
                                                    @endif
                                                    @if(count($downward_trends) > 0)
                                                        <li><a href="#downward_trends" data-toggle="tab">Downward Trends ({{ count($downward_trends) }})</a></li>
                                                    @endif
                                                </ul>
                                                <div class="tab-content">
                                                    <!-- All Trends Tab -->
                                                    <div class="tab-pane active" id="all_trends">
                                                        <table class="table table-bordered table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th>Product</th>
                                                                    <th>Trend Direction</th>
                                                                    <th>Strength</th>
                                                                    <th>Avg Growth</th>
                                                                    <th>Recent Growth</th>
                                                                    <th>Acceleration</th>
                                                                    <th>Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($data['trend_detection'] as $trend)
                                                                <tr>
                                                                    <td>{{ $trend['product_name'] }}</td>
                                                                    <td>
                                                                        @if($trend['trend_direction'] == 'Upward')
                                                                            <span class="text-success"><i class="fa fa-arrow-up"></i> {{ $trend['trend_direction'] }}</span>
                                                                        @elseif($trend['trend_direction'] == 'Downward')
                                                                            <span class="text-danger"><i class="fa fa-arrow-down"></i> {{ $trend['trend_direction'] }}</span>
                                                                        @else
                                                                            <span class="text-muted"><i class="fa fa-arrows-h"></i> {{ $trend['trend_direction'] }}</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if($trend['trend_strength'] == 'Strong')
                                                                            <span class="label label-success">{{ $trend['trend_strength'] }}</span>
                                                                        @elseif($trend['trend_strength'] == 'Moderate')
                                                                            <span class="label label-info">{{ $trend['trend_strength'] }}</span>
                                                                        @else
                                                                            <span class="label label-default">{{ $trend['trend_strength'] }}</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ @num_format($trend['avg_growth_rate']) }}%</td>
                                                                    <td>{{ @num_format($trend['recent_growth_rate']) }}%</td>
                                                                    <td>
                                                                        {{ @num_format($trend['trend_acceleration']) }}%
                                                                        @if($trend['trend_acceleration'] > 5)
                                                                            <i class="fa fa-rocket text-success" title="Accelerating"></i>
                                                                        @elseif($trend['trend_acceleration'] < -5)
                                                                            <i class="fa fa-brake text-danger" title="Decelerating"></i>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if($trend['is_emerging'])
                                                                            <span class="label label-success">Emerging Trend</span>
                                                                        @elseif($trend['trend_direction'] == 'Upward' && $trend['trend_strength'] == 'Strong')
                                                                            <span class="label label-info">Strong Growth</span>
                                                                        @elseif($trend['trend_direction'] == 'Downward' && $trend['trend_strength'] == 'Strong')
                                                                            <span class="label label-danger">Strong Decline</span>
                                                                        @else
                                                                            <span class="label label-default">Stable</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    <!-- Upward Trends Tab -->
                                                    @if(count($upward_trends) > 0)
                                                    <div class="tab-pane" id="upward_trends">
                                                        <table class="table table-bordered table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th>Product</th>
                                                                    <th>Strength</th>
                                                                    <th>Avg Growth</th>
                                                                    <th>Recent Growth</th>
                                                                    <th>Acceleration</th>
                                                                    <th>Recommendation</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($upward_trends as $trend)
                                                                <tr>
                                                                    <td>{{ $trend['product_name'] }}</td>
                                                                    <td>
                                                                        @if($trend['trend_strength'] == 'Strong')
                                                                            <span class="label label-success">{{ $trend['trend_strength'] }}</span>
                                                                        @elseif($trend['trend_strength'] == 'Moderate')
                                                                            <span class="label label-info">{{ $trend['trend_strength'] }}</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ @num_format($trend['avg_growth_rate']) }}%</td>
                                                                    <td>{{ @num_format($trend['recent_growth_rate']) }}%</td>
                                                                    <td>{{ @num_format($trend['trend_acceleration']) }}%</td>
                                                                    <td>
                                                                        @if($trend['is_emerging'])
                                                                            Increase inventory and marketing focus
                                                                        @elseif($trend['trend_strength'] == 'Strong')
                                                                            Ensure adequate inventory levels
                                                                        @else
                                                                            Monitor growth pattern
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    @endif

                                                    <!-- Downward Trends Tab -->
                                                    @if(count($downward_trends) > 0)
                                                    <div class="tab-pane" id="downward_trends">
                                                        <table class="table table-bordered table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th>Product</th>
                                                                    <th>Strength</th>
                                                                    <th>Avg Decline</th>
                                                                    <th>Recent Decline</th>
                                                                    <th>Acceleration</th>
                                                                    <th>Recommendation</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($downward_trends as $trend)
                                                                <tr>
                                                                    <td>{{ $trend['product_name'] }}</td>
                                                                    <td>
                                                                        @if($trend['trend_strength'] == 'Strong')
                                                                            <span class="label label-danger">{{ $trend['trend_strength'] }}</span>
                                                                        @elseif($trend['trend_strength'] == 'Moderate')
                                                                            <span class="label label-warning">{{ $trend['trend_strength'] }}</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ @num_format($trend['avg_growth_rate']) }}%</td>
                                                                    <td>{{ @num_format($trend['recent_growth_rate']) }}%</td>
                                                                    <td>{{ @num_format($trend['trend_acceleration']) }}%</td>
                                                                    <td>
                                                                        @if($trend['trend_strength'] == 'Strong' && $trend['recent_growth_rate'] < -15)
                                                                            Consider clearance or discontinuation
                                                                        @elseif($trend['trend_strength'] == 'Strong')
                                                                            Reduce inventory and consider promotions
                                                                        @else
                                                                            Monitor decline and adjust ordering
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Trend Visualization -->
                                            <div class="col-md-12">
                                                <h5><strong>Trend Visualization</strong></h5>
                                                <p>Select a product to view its sales trend over time:</p>

                                                <div class="form-group">
                                                    <select id="trend_product_selector" class="form-control">
                                                        @foreach($data['trend_detection'] as $index => $trend)
                                                            <option value="{{ $index }}" {{ $index == 0 ? 'selected' : '' }}>
                                                                {{ $trend['product_name'] }} 
                                                                ({{ $trend['trend_direction'] }} - {{ $trend['trend_strength'] }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div id="product_trend_chart" style="height: 300px;"></div>
                                            </div>
                                        @else
                                            <div class="alert alert-info">
                                                <i class="fa fa-info-circle"></i> Insufficient data for trend analysis. Trend detection requires at least 3 months of sales data for multiple products.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Inventory Optimization -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Inventory Optimization Recommendations</h4>
                                    <p>Recommendations for optimal inventory levels based on sales patterns and stock levels.</p>

                                    <div class="col-md-12">
                                        <div class="alert alert-info">
                                            <h4><i class="icon fa fa-info"></i> Inventory Insights</h4>
                                            <ul>
                                                @if(isset($data['stock_turnover']) && !empty($data['stock_turnover']))
                                                    @php
                                                        $fast_moving = array_filter($data['stock_turnover'], function($item) {
                                                            return $item['turnover_rate'] > 3;
                                                        });
                                                        $slow_moving = array_filter($data['stock_turnover'], function($item) {
                                                            return $item['turnover_rate'] <= 3 && $item['turnover_rate'] > 0;
                                                        });
                                                        $no_movement = array_filter($data['stock_turnover'], function($item) {
                                                            return $item['turnover_rate'] == 0 && $item['average_inventory'] > 0;
                                                        });
                                                    @endphp

                                                    @if(count($fast_moving) > 0)
                                                        <li><strong>Fast-moving products:</strong> {{ count($fast_moving) }} products have high turnover rates. Consider increasing stock levels to prevent stockouts.</li>
                                                    @endif

                                                    @if(count($slow_moving) > 0)
                                                        <li><strong>Slow-moving products:</strong> {{ count($slow_moving) }} products have low turnover rates. Consider reducing order quantities or running promotions.</li>
                                                    @endif

                                                    @if(count($no_movement) > 0)
                                                        <li><strong>No movement:</strong> {{ count($no_movement) }} products show no sales despite having inventory. Consider clearance sales or discontinuation.</li>
                                                    @endif
                                                @endif

                                                @if(isset($data['inventory_aging']) && !empty($data['inventory_aging']))
                                                    @php
                                                        $critical_stock = array_filter($data['inventory_aging']->toArray(), function($item) {
                                                            return isset($item->days_to_stock_out) && $item->days_to_stock_out < 7;
                                                        });
                                                        $old_inventory = array_filter($data['inventory_aging']->toArray(), function($item) {
                                                            return isset($item->days_in_inventory) && $item->days_in_inventory > 90;
                                                        });
                                                    @endphp

                                                    @if(count($critical_stock) > 0)
                                                        <li><strong>Critical stock levels:</strong> {{ count($critical_stock) }} products will run out of stock within a week based on current sales rates. Reorder immediately.</li>
                                                    @endif

                                                    @if(count($old_inventory) > 0)
                                                        <li><strong>Aging inventory:</strong> {{ count($old_inventory) }} products have been in stock for over 90 days. Consider promotions to move this inventory.</li>
                                                    @endif
                                                @endif
                                            </ul>
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

<!-- Include vis.js for network visualization -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.css" rel="stylesheet" type="text/css" />

<script>
    $(document).ready(function() {
        // Daily Sales Chart
        var dailySalesCtx = document.getElementById('daily_sales_chart').getContext('2d');
        var dailySalesData = {
            labels: [
                @if(isset($data['sales_trends']))
                    @foreach($data['sales_trends'] as $trend)
                        '{{ \Carbon\Carbon::parse($trend->date)->format('M d') }}',
                    @endforeach
                @endif
            ],
            datasets: [{
                label: 'Daily Sales',
                data: [
                    @if(isset($data['sales_trends']))
                        @foreach($data['sales_trends'] as $trend)
                            {{ $trend->total_sales }},
                        @endforeach
                    @endif
                ],
                backgroundColor: 'rgba(60, 141, 188, 0.2)',
                borderColor: 'rgba(60, 141, 188, 1)',
                borderWidth: 1
            }]
        };
        var dailySalesChart = new Chart(dailySalesCtx, {
            type: 'line',
            data: dailySalesData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Monthly Sales Chart
        var monthlySalesCtx = document.getElementById('monthly_sales_chart').getContext('2d');
        var monthlySalesData = {
            labels: [
                @if(isset($data['monthly_sales']))
                    @foreach($data['monthly_sales'] as $monthly)
                        '{{ \Carbon\Carbon::createFromDate(is_array($monthly) ? $monthly['year'] : $monthly->year, is_array($monthly) ? $monthly['month'] : $monthly->month, 1)->format('M Y') }}',
                    @endforeach
                @endif
            ],
            datasets: [{
                label: 'Monthly Sales',
                data: [
                    @if(isset($data['monthly_sales']))
                        @foreach($data['monthly_sales'] as $monthly)
                            {{ is_array($monthly) ? $monthly['total_sales'] : $monthly->total_sales }},
                        @endforeach
                    @endif
                ],
                backgroundColor: 'rgba(60, 141, 188, 0.2)',
                borderColor: 'rgba(60, 141, 188, 1)',
                borderWidth: 1
            }]
        };
        var monthlySalesChart = new Chart(monthlySalesCtx, {
            type: 'bar',
            data: monthlySalesData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Daily Purchase Chart
        var dailyPurchaseCtx = document.getElementById('daily_purchase_chart').getContext('2d');
        var dailyPurchaseData = {
            labels: [
                @if(isset($data['purchase_trends']))
                    @foreach($data['purchase_trends'] as $trend)
                        '{{ \Carbon\Carbon::parse($trend->date)->format('M d') }}',
                    @endforeach
                @endif
            ],
            datasets: [{
                label: 'Daily Purchases',
                data: [
                    @if(isset($data['purchase_trends']))
                        @foreach($data['purchase_trends'] as $trend)
                            {{ $trend->total_purchase }},
                        @endforeach
                    @endif
                ],
                backgroundColor: 'rgba(210, 214, 222, 0.2)',
                borderColor: 'rgba(210, 214, 222, 1)',
                borderWidth: 1
            }]
        };
        var dailyPurchaseChart = new Chart(dailyPurchaseCtx, {
            type: 'line',
            data: dailyPurchaseData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Monthly Purchase Chart
        var monthlyPurchaseCtx = document.getElementById('monthly_purchase_chart').getContext('2d');
        var monthlyPurchaseData = {
            labels: [
                @if(isset($data['monthly_purchases']))
                    @foreach($data['monthly_purchases'] as $monthly)
                        '{{ \Carbon\Carbon::createFromDate(is_array($monthly) ? $monthly['year'] : $monthly->year, is_array($monthly) ? $monthly['month'] : $monthly->month, 1)->format('M Y') }}',
                    @endforeach
                @endif
            ],
            datasets: [{
                label: 'Monthly Purchases',
                data: [
                    @if(isset($data['monthly_purchases']))
                        @foreach($data['monthly_purchases'] as $monthly)
                            {{ is_array($monthly) ? $monthly['total_purchase'] : $monthly->total_purchase }},
                        @endforeach
                    @endif
                ],
                backgroundColor: 'rgba(210, 214, 222, 0.2)',
                borderColor: 'rgba(210, 214, 222, 1)',
                borderWidth: 1
            }]
        };
        var monthlyPurchaseChart = new Chart(monthlyPurchaseCtx, {
            type: 'bar',
            data: monthlyPurchaseData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Seasonal Trends Chart
        var seasonalTrendsCtx = document.getElementById('seasonal_trends_chart').getContext('2d');
        var seasonalTrendsData = {
            labels: [
                @if(isset($data['seasonal_trends']))
                    @foreach($data['seasonal_trends'] as $trend)
                        '{{ \Carbon\Carbon::createFromDate(is_array($trend) ? $trend['year'] : $trend->year, is_array($trend) ? $trend['month'] : $trend->month, 1)->format('M Y') }}',
                    @endforeach
                @endif
            ],
            datasets: [{
                label: 'Monthly Sales',
                data: [
                    @if(isset($data['seasonal_trends']))
                        @foreach($data['seasonal_trends'] as $trend)
                            {{ is_array($trend) ? $trend['total_amount'] : $trend->total_amount }},
                        @endforeach
                    @endif
                ],
                backgroundColor: 'rgba(60, 141, 188, 0.2)',
                borderColor: 'rgba(60, 141, 188, 1)',
                borderWidth: 1
            }]
        };
        var seasonalTrendsChart = new Chart(seasonalTrendsCtx, {
            type: 'line',
            data: seasonalTrendsData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Product Trend Visualization
        var trendProductSelector = document.getElementById('trend_product_selector');
        var productTrendChart = document.getElementById('product_trend_chart');

        if (trendProductSelector && productTrendChart) {
            @if(isset($data['trend_detection']) && !empty($data['trend_detection']))
                // Prepare trend data
                var trendData = @json($data['trend_detection']);

                // Function to render the chart for a selected product
                function renderTrendChart(productIndex) {
                    var selectedTrend = trendData[productIndex];
                    var monthlyData = selectedTrend.monthly_data;

                    // Extract months and quantities
                    var months = [];
                    var quantities = [];
                    var amounts = [];

                    // Sort months chronologically
                    var sortedMonths = Object.keys(monthlyData).sort();

                    sortedMonths.forEach(function(month) {
                        months.push(month);
                        quantities.push(monthlyData[month].quantity);
                        amounts.push(monthlyData[month].amount);
                    });

                    // Calculate trend line using linear regression
                    var n = months.length;
                    var xValues = Array.from({length: n}, (_, i) => i);
                    var sum_x = xValues.reduce((a, b) => a + b, 0);
                    var sum_y = quantities.reduce((a, b) => a + b, 0);
                    var sum_xy = 0;
                    var sum_xx = 0;

                    for (var i = 0; i < n; i++) {
                        sum_xy += xValues[i] * quantities[i];
                        sum_xx += xValues[i] * xValues[i];
                    }

                    var slope = 0;
                    var intercept = 0;

                    if ((n * sum_xx - sum_x * sum_x) !== 0) {
                        slope = (n * sum_xy - sum_x * sum_y) / (n * sum_xx - sum_x * sum_x);
                        intercept = (sum_y - slope * sum_x) / n;
                    }

                    // Generate trend line points
                    var trendLine = [];
                    for (var i = 0; i < n; i++) {
                        trendLine.push(intercept + slope * i);
                    }

                    // Format months for display
                    var displayMonths = months.map(function(month) {
                        var parts = month.split('-');
                        var year = parts[0];
                        var monthNum = parseInt(parts[1]);
                        var monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                        return monthNames[monthNum - 1] + ' ' + year;
                    });

                    // Create chart
                    var ctx = productTrendChart.getContext('2d');

                    // Clear previous chart if exists
                    if (window.productTrendChartInstance) {
                        window.productTrendChartInstance.destroy();
                    }

                    window.productTrendChartInstance = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: displayMonths,
                            datasets: [
                                {
                                    label: 'Sales Quantity',
                                    data: quantities,
                                    backgroundColor: 'rgba(60, 141, 188, 0.2)',
                                    borderColor: 'rgba(60, 141, 188, 1)',
                                    borderWidth: 2,
                                    pointRadius: 4,
                                    fill: true
                                },
                                {
                                    label: 'Trend Line',
                                    data: trendLine,
                                    backgroundColor: 'transparent',
                                    borderColor: selectedTrend.trend_direction === 'Upward' ? 'rgba(0, 166, 90, 1)' : 
                                                (selectedTrend.trend_direction === 'Downward' ? 'rgba(221, 75, 57, 1)' : 'rgba(243, 156, 18, 1)'),
                                    borderWidth: 2,
                                    borderDash: [5, 5],
                                    pointRadius: 0,
                                    fill: false
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: false,
                                    title: {
                                        display: true,
                                        text: 'Quantity Sold'
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
                                tooltip: {
                                    mode: 'index',
                                    intersect: false
                                },
                                legend: {
                                    position: 'top'
                                },
                                title: {
                                    display: true,
                                    text: selectedTrend.product_name + ' - Sales Trend',
                                    font: {
                                        size: 16
                                    }
                                },
                                subtitle: {
                                    display: true,
                                    text: 'Direction: ' + selectedTrend.trend_direction + 
                                          ' | Strength: ' + selectedTrend.trend_strength + 
                                          ' | Avg Growth: ' + selectedTrend.avg_growth_rate.toFixed(2) + '%',
                                    font: {
                                        size: 14
                                    },
                                    color: selectedTrend.trend_direction === 'Upward' ? 'rgba(0, 166, 90, 1)' : 
                                           (selectedTrend.trend_direction === 'Downward' ? 'rgba(221, 75, 57, 1)' : 'rgba(243, 156, 18, 1)')
                                }
                            }
                        }
                    });

                    // Add annotation for emerging trend if applicable
                    if (selectedTrend.is_emerging) {
                        var chartContainer = productTrendChart.parentNode;
                        var emergingAlert = document.createElement('div');
                        emergingAlert.className = 'alert alert-success mt-2';
                        emergingAlert.innerHTML = '<i class="fa fa-line-chart"></i> <strong>Emerging Trend Detected!</strong> ' +
                                                 'This product shows accelerating growth with recent growth rate of ' + 
                                                 selectedTrend.recent_growth_rate.toFixed(2) + '%.';

                        // Remove any existing alert
                        var existingAlert = chartContainer.querySelector('.alert');
                        if (existingAlert) {
                            chartContainer.removeChild(existingAlert);
                        }

                        chartContainer.appendChild(emergingAlert);
                    } else {
                        // Remove any existing alert
                        var chartContainer = productTrendChart.parentNode;
                        var existingAlert = chartContainer.querySelector('.alert');
                        if (existingAlert) {
                            chartContainer.removeChild(existingAlert);
                        }
                    }
                }

                // Initialize chart with first product
                if (trendData.length > 0) {
                    renderTrendChart(0);
                }

                // Update chart when product selection changes
                trendProductSelector.addEventListener('change', function() {
                    renderTrendChart(this.value);
                });
            @else
                productTrendChart.innerHTML = '<div class="alert alert-info text-center">No trend data available</div>';
            @endif
        }

        // Market Basket Visualization
        var marketBasketChart = document.getElementById('market_basket_chart');
        if (marketBasketChart) {
            // Check if we have cross-sell data
            @if(isset($data['cross_sell_products']) && !$data['cross_sell_products']->isEmpty())
                // Prepare data for network graph
                var nodes = [];
                var edges = [];
                var nodeMap = {};

                // Create nodes from products
                @foreach($data['cross_sell_products'] as $index => $pair)
                    // Add first product if not already in nodes
                    if (!nodeMap['{{ $pair->product_1_id }}']) {
                        nodeMap['{{ $pair->product_1_id }}'] = nodes.length;
                        nodes.push({
                            id: '{{ $pair->product_1_id }}',
                            label: '{{ str_replace("'", "\\'", $pair->product_1) }}',
                            value: {{ $pair->product1_count ?? 1 }},
                            color: '#3c8dbc'
                        });
                    }

                    // Add second product if not already in nodes
                    if (!nodeMap['{{ $pair->product_2_id }}']) {
                        nodeMap['{{ $pair->product_2_id }}'] = nodes.length;
                        nodes.push({
                            id: '{{ $pair->product_2_id }}',
                            label: '{{ str_replace("'", "\\'", $pair->product_2) }}',
                            value: {{ $pair->product2_count ?? 1 }},
                            color: '#3c8dbc'
                        });
                    }

                    // Only add edges for strong relationships (lift > 1)
                    @if(isset($pair->lift) && $pair->lift > 1)
                        edges.push({
                            from: '{{ $pair->product_1_id }}',
                            to: '{{ $pair->product_2_id }}',
                            value: {{ $pair->frequency }},
                            title: 'Frequency: {{ $pair->frequency }}, Lift: {{ @num_format($pair->lift) }}',
                            // Color based on lift strength
                            color: {
                                color: {{ $pair->lift > 3 ? "'#00a65a'" : ($pair->lift > 1.5 ? "'#3c8dbc'" : "'#d2d6de'") }},
                                highlight: {{ $pair->lift > 3 ? "'#00a65a'" : ($pair->lift > 1.5 ? "'#3c8dbc'" : "'#d2d6de'") }}
                            },
                            width: {{ min(max($pair->frequency / 2, 1), 10) }} // Scale width based on frequency
                        });
                    @endif
                @endforeach

                // Limit to top 15 nodes for better visualization
                if (nodes.length > 15) {
                    // Sort nodes by value (frequency)
                    nodes.sort(function(a, b) {
                        return b.value - a.value;
                    });

                    // Keep only top 15 nodes
                    var topNodes = nodes.slice(0, 15);
                    var topNodeIds = topNodes.map(function(node) { return node.id; });

                    // Filter edges to only include connections between top nodes
                    edges = edges.filter(function(edge) {
                        return topNodeIds.includes(edge.from) && topNodeIds.includes(edge.to);
                    });

                    // Update nodes
                    nodes = topNodes;
                }

                // Create network visualization
                var container = document.getElementById('market_basket_chart');
                var data = {
                    nodes: new vis.DataSet(nodes),
                    edges: new vis.DataSet(edges)
                };
                var options = {
                    nodes: {
                        shape: 'dot',
                        scaling: {
                            min: 10,
                            max: 30,
                            label: {
                                min: 8,
                                max: 16,
                                drawThreshold: 8,
                                maxVisible: 20
                            }
                        },
                        font: {
                            size: 12,
                            face: 'Tahoma'
                        }
                    },
                    edges: {
                        width: 0.15,
                        color: { inherit: 'from' },
                        smooth: {
                            type: 'continuous'
                        }
                    },
                    physics: {
                        stabilization: false,
                        barnesHut: {
                            gravitationalConstant: -80000,
                            springConstant: 0.001,
                            springLength: 200
                        }
                    },
                    interaction: {
                        tooltipDelay: 200,
                        hideEdgesOnDrag: true
                    }
                };

                // Initialize network
                var network = new vis.Network(container, data, options);

                // Add event listener for when the network is stabilized
                network.on("stabilizationIterationsDone", function () {
                    network.setOptions({ physics: false });
                });
            @else
                // No data available
                document.getElementById('market_basket_chart').innerHTML = '<div class="alert alert-info text-center">No sufficient data available for visualization</div>';
            @endif
        }

        // Sales Forecast Chart
        var salesForecastCtx = document.getElementById('sales_forecast_chart');
        if (salesForecastCtx) {
            salesForecastCtx = salesForecastCtx.getContext('2d');

            // Historical data (last few months) + forecast
            var historicalMonths = [];
            var historicalValues = [];

            @if(isset($data['monthly_sales']) && !$data['monthly_sales']->isEmpty())
                // Get the last 6 months of historical data
                @php
                    $monthly_sales_array = $data['monthly_sales']->toArray();
                    $last_months = array_slice($monthly_sales_array, -6);
                @endphp

                @foreach($last_months as $sale)
                    historicalMonths.push('{{ \Carbon\Carbon::createFromDate(is_array($sale) ? $sale['year'] : $sale->year, is_array($sale) ? $sale['month'] : $sale->month, 1)->format('M Y') }}');
                    historicalValues.push({{ is_array($sale) ? $sale['total_sales'] : $sale->total_sales }});
                @endforeach
            @endif

            // Add forecast data
            var forecastMonths = [];
            var forecastValues = [];
            var lowerBounds = [];
            var upperBounds = [];

            @if(isset($data['sales_forecast']) && !empty($data['sales_forecast']))
                @foreach($data['sales_forecast'] as $forecast)
                    forecastMonths.push('{{ $forecast['month'] }}');
                    forecastValues.push({{ $forecast['forecasted_sales'] }});
                    @if(isset($forecast['lower_bound']) && isset($forecast['upper_bound']))
                        lowerBounds.push({{ $forecast['lower_bound'] }});
                        upperBounds.push({{ $forecast['upper_bound'] }});
                    @else
                        lowerBounds.push(null);
                        upperBounds.push(null);
                    @endif
                @endforeach
            @endif

            // Combine historical and forecast data
            var allMonths = historicalMonths.concat(forecastMonths);

            var salesForecastData = {
                labels: allMonths,
                datasets: [
                    {
                        label: 'Historical Sales',
                        data: historicalValues.concat(Array(forecastMonths.length).fill(null)),
                        borderColor: 'rgba(60, 141, 188, 1)',
                        backgroundColor: 'rgba(60, 141, 188, 0.2)',
                        borderWidth: 2,
                        pointRadius: 4
                    },
                    {
                        label: 'Forecasted Sales',
                        data: Array(historicalMonths.length).fill(null).concat(forecastValues),
                        borderColor: 'rgba(210, 214, 222, 1)',
                        backgroundColor: 'rgba(210, 214, 222, 0.2)',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        pointRadius: 4
                    },
                    {
                        label: 'Upper Bound',
                        data: Array(historicalMonths.length).fill(null).concat(upperBounds),
                        borderColor: 'rgba(0, 166, 90, 0.5)',
                        backgroundColor: 'transparent',
                        borderWidth: 1,
                        borderDash: [2, 2],
                        pointRadius: 0,
                        fill: false
                    },
                    {
                        label: 'Lower Bound',
                        data: Array(historicalMonths.length).fill(null).concat(lowerBounds),
                        borderColor: 'rgba(221, 75, 57, 0.5)',
                        backgroundColor: 'transparent',
                        borderWidth: 1,
                        borderDash: [2, 2],
                        pointRadius: 0,
                        fill: false
                    }
                ]
            };

            var salesForecastChart = new Chart(salesForecastCtx, {
                type: 'line',
                data: salesForecastData,
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: false,
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
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        },
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true
                            }
                        },
                        annotation: {
                            annotations: {
                                line1: {
                                    type: 'line',
                                    xMin: historicalMonths.length - 0.5,
                                    xMax: historicalMonths.length - 0.5,
                                    borderColor: 'rgba(0, 0, 0, 0.3)',
                                    borderWidth: 2,
                                    borderDash: [5, 5],
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

        // Initialize DataTables for tables with datatable class
        $('.datatable').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Handle graph dropdown toggles
        $('.dropdown-menu a').click(function(e) {
            e.preventDefault();
            var targetId = $(this).data('target');

            // Hide all containers in the same section
            $(this).closest('.row').parent().find('[id$="_container"]').hide();

            // Show the selected container
            $('#' + targetId).show();

            // Update dropdown button text
            $(this).closest('.dropdown').find('.dropdown-toggle').html($(this).text() + ' <span class="caret"></span>');
        });
    });
</script>
                    </div>
                </div>
                <!-- End of Predictive Analytics Tab -->
            </div>
            <!-- End of Tab Content -->
