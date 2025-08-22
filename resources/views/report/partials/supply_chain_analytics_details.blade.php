<div class="col-xs-12">
    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">{{ __('Supply Chain Analytics') }}</h3>
        </div>
        <div class="box-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#sales-analytics" aria-controls="sales-analytics" role="tab" data-toggle="tab">1. Sales Analytics</a></li>
                <li role="presentation"><a href="#purchase-analytics" aria-controls="purchase-analytics" role="tab" data-toggle="tab">2. Purchase Analytics</a></li>
                <li role="presentation"><a href="#sales-purchase-alignment" aria-controls="sales-purchase-alignment" role="tab" data-toggle="tab">3. Sales vs. Purchase Alignment</a></li>
                <li role="presentation"><a href="#margin-analytics" aria-controls="margin-analytics" role="tab" data-toggle="tab">4. Margin & Profitability</a></li>
                <li role="presentation"><a href="#predictive-analytics" aria-controls="predictive-analytics" role="tab" data-toggle="tab">5. Predictive Analytics</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <!-- 1. Sales Analytics Tab -->
                <div role="tabpanel" class="tab-pane active" id="sales-analytics">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Sales Analytics</h3>
                            <div class="box-tools pull-right">
                                <div class="dropdown">
                                    <button class="btn btn-default dropdown-toggle" type="button" id="salesAnalyticsDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                        Sales Trends
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="salesAnalyticsDropdown">
                                        <li class="active"><a href="#sales-trends" data-toggle="tab">Sales Trends</a></li>
                                        <li><a href="#product-mix" data-toggle="tab">Product Mix</a></li>
                                        <li><a href="#customer-behavior" data-toggle="tab">Customer Behavior</a></li>
                                        <li><a href="#cross-sell" data-toggle="tab">Cross-sell Analysis</a></li>
                                        <li><a href="#discount-impact" data-toggle="tab">Discount Impact</a></li>
                                        <li><a href="#churn-prediction" data-toggle="tab">Churn Prediction</a></li>
                                        <li><a href="#profitability-product" data-toggle="tab">Profitability by Product</a></li>
                                        <li><a href="#profitability-customer" data-toggle="tab">Profitability by Customer</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="tab-content">
                                <!-- Sales Trends -->
                                <div class="tab-pane active" id="sales-trends">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Sales Trends - Daily <i class="fa fa-info-circle" data-toggle="tooltip" title="Daily sales trends showing revenue patterns over time. Formula: Sum of daily sales amount."></i></h4>
                                                <canvas id="daily_sales_chart" height="250"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Sales Data</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped datatable">
                                                        <thead>
                                                            <tr>
                                                                <th>Date</th>
                                                                <th>Sales Amount</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($data['sales_analytics']) && isset($data['sales_analytics']['sales_trends']))
                                                                @foreach($data['sales_analytics']['sales_trends'] as $trend)
                                                                <tr>
                                                                    <td>{{ \Carbon\Carbon::parse($trend->date)->format('M d, Y') }}</td>
                                                                    <td>{{ @num_format($trend->total_sales) }}</td>
                                                                </tr>
                                                                @endforeach
                                                            @else
                                                                <tr>
                                                                    <td colspan="2" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Sales Trends - Monthly <i class="fa fa-info-circle" data-toggle="tooltip" title="Monthly sales trends showing revenue patterns over time. Formula: Sum of monthly sales amount."></i></h4>
                                                <canvas id="monthly_sales_chart" height="250"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Monthly Sales Data</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped datatable">
                                                        <thead>
                                                            <tr>
                                                                <th>Month</th>
                                                                <th>Sales Amount</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($data['sales_analytics']) && isset($data['sales_analytics']['monthly_sales']))
                                                                @foreach($data['sales_analytics']['monthly_sales'] as $monthly)
                                                                <tr>
                                                                    <td>{{ \Carbon\Carbon::createFromDate($monthly->year, $monthly->month, 1)->format('M Y') }}</td>
                                                                    <td>{{ @num_format($monthly->total_sales) }}</td>
                                                                </tr>
                                                                @endforeach
                                                            @else
                                                                <tr>
                                                                    <td colspan="2" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="info-box">
                                                <h4>Sales Forecast <i class="fa fa-info-circle" data-toggle="tooltip" title="Sales forecast for the next 3 months based on historical data. Formula: Moving average with seasonal adjustment."></i></h4>
                                                <canvas id="sales_forecast_chart" height="250"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Product Mix -->
                                <div class="tab-pane" id="product-mix">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Product Mix - Top-selling Products <i class="fa fa-info-circle" data-toggle="tooltip" title="Analysis of top-selling products by quantity and revenue. Formula: Sum of product sales grouped by product."></i></h4>
                                                <canvas id="product_mix_chart" height="250"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Product Mix Data</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped datatable">
                                                        <thead>
                                                            <tr>
                                                                <th>Product</th>
                                                                <th>Category</th>
                                                                <th>Quantity</th>
                                                                <th>Amount</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($data['sales_analytics']) && isset($data['sales_analytics']['product_mix']))
                                                                @foreach($data['sales_analytics']['product_mix'] as $product)
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
                                        </div>
                                    </div>
                                </div>

                                <!-- Customer Behavior -->
                                <div class="tab-pane" id="customer-behavior">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Customer Behavior (RFM) <i class="fa fa-info-circle" data-toggle="tooltip" title="Recency, Frequency, Monetary analysis of customer purchasing behavior. Formula: Segmentation based on last purchase date, purchase frequency, and total spent."></i></h4>
                                                <canvas id="customer_behavior_chart" height="250"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Customer Behavior Data</h4>
                                                <div class="table-responsive">
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
                                                            @if(isset($data['sales_analytics']) && isset($data['sales_analytics']['customers']))
                                                                @foreach($data['sales_analytics']['customers'] as $customer)
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

                                <!-- Cross-sell Analysis -->
                                <div class="tab-pane" id="cross-sell">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Cross-sell & Bundle Analysis <i class="fa fa-info-circle" data-toggle="tooltip" title="Analysis of products frequently sold together. Formula: Frequency of product pairs appearing in the same transaction."></i></h4>
                                                <canvas id="cross_sell_chart" height="250"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Cross-sell Data</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped datatable">
                                                        <thead>
                                                            <tr>
                                                                <th>Product 1</th>
                                                                <th>Product 2</th>
                                                                <th>Frequency</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($data['sales_analytics']) && isset($data['sales_analytics']['cross_sell_products']))
                                                                @foreach($data['sales_analytics']['cross_sell_products'] as $cross_sell)
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

                                <!-- Discount Impact -->
                                <div class="tab-pane" id="discount-impact">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Discount Impact <i class="fa fa-info-circle" data-toggle="tooltip" title="Analysis of discount impact on sales and margins. Formula: Comparison of sales uplift vs. margin reduction due to discounts."></i></h4>
                                                <canvas id="discount_impact_chart" height="250"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Discount Impact Data</h4>
                                                <div class="table-responsive">
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
                                                            @if(isset($data['sales_analytics']) && isset($data['sales_analytics']['discount_impact']))
                                                                @foreach($data['sales_analytics']['discount_impact'] as $discount)
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

                                <!-- Churn Prediction -->
                                <div class="tab-pane" id="churn-prediction">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Churn Prediction <i class="fa fa-info-circle" data-toggle="tooltip" title="Prediction of customers at risk of churning. Formula: Analysis based on recency, frequency, and monetary value."></i></h4>
                                                <canvas id="churn_prediction_chart" height="250"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Churn Prediction Data</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped datatable">
                                                        <thead>
                                                            <tr>
                                                                <th>Customer</th>
                                                                <th>Last Purchase</th>
                                                                <th>Days Since Last Purchase</th>
                                                                <th>Purchase Count</th>
                                                                <th>Total Spent</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($data['sales_analytics']) && isset($data['sales_analytics']['churn_prediction']))
                                                                @foreach($data['sales_analytics']['churn_prediction'] as $churn)
                                                                <tr>
                                                                    <td>{{ $churn->name }}</td>
                                                                    <td>{{ $churn->last_purchase_date ? @format_date($churn->last_purchase_date) : 'N/A' }}</td>
                                                                    <td>{{ $churn->days_since_last_purchase }}</td>
                                                                    <td>{{ $churn->purchase_count }}</td>
                                                                    <td>{{ @num_format($churn->total_spent) }}</td>
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

                                <!-- Profitability by Product -->
                                <div class="tab-pane" id="profitability-product">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Profitability by Product <i class="fa fa-info-circle" data-toggle="tooltip" title="Analysis of product profitability. Formula: (Sales - Cost) / Sales * 100 for each product."></i></h4>
                                                <canvas id="profitability_product_chart" height="250"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Profitability by Product Data</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped datatable">
                                                        <thead>
                                                            <tr>
                                                                <th>Product</th>
                                                                <th>Quantity</th>
                                                                <th>Total Sales</th>
                                                                <th>Total Cost</th>
                                                                <th>Gross Profit</th>
                                                                <th>Profit Margin %</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($data['sales_analytics']) && isset($data['sales_analytics']['profitability_by_product']))
                                                                @foreach($data['sales_analytics']['profitability_by_product'] as $profit)
                                                                <tr>
                                                                    <td>{{ $profit->product_name }}</td>
                                                                    <td>{{ @num_format($profit->total_quantity) }}</td>
                                                                    <td>{{ @num_format($profit->total_sales) }}</td>
                                                                    <td>{{ @num_format($profit->total_cost) }}</td>
                                                                    <td>{{ @num_format($profit->gross_profit) }}</td>
                                                                    <td>{{ @num_format($profit->profit_margin) }}%</td>
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

                                <!-- Profitability by Customer -->
                                <div class="tab-pane" id="profitability-customer">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Profitability by Customer <i class="fa fa-info-circle" data-toggle="tooltip" title="Analysis of customer profitability. Formula: (Sales - Cost) / Sales * 100 for each customer."></i></h4>
                                                <canvas id="profitability_customer_chart" height="250"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Profitability by Customer Data</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped datatable">
                                                        <thead>
                                                            <tr>
                                                                <th>Customer</th>
                                                                <th>Transaction Count</th>
                                                                <th>Total Sales</th>
                                                                <th>Total Cost</th>
                                                                <th>Gross Profit</th>
                                                                <th>Profit Margin %</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($data['sales_analytics']) && isset($data['sales_analytics']['profitability_by_customer']))
                                                                @foreach($data['sales_analytics']['profitability_by_customer'] as $profit)
                                                                <tr>
                                                                    <td>{{ $profit->customer_name }}</td>
                                                                    <td>{{ $profit->transaction_count }}</td>
                                                                    <td>{{ @num_format($profit->total_sales) }}</td>
                                                                    <td>{{ @num_format($profit->total_cost) }}</td>
                                                                    <td>{{ @num_format($profit->gross_profit) }}</td>
                                                                    <td>{{ @num_format($profit->profit_margin) }}%</td>
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
                    </div>
                </div>

                <!-- 2. Purchase Analytics Tab -->
                <div role="tabpanel" class="tab-pane" id="purchase-analytics">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Purchase Analytics</h3>
                            <div class="box-tools pull-right">
                                <div class="dropdown">
                                    <button class="btn btn-default dropdown-toggle" type="button" id="purchaseAnalyticsDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                        Purchase Trends
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="purchaseAnalyticsDropdown">
                                        <li class="active"><a href="#purchase-trends" data-toggle="tab">Purchase Trends</a></li>
                                        <li><a href="#supplier-performance" data-toggle="tab">Supplier Performance</a></li>
                                        <li><a href="#product-purchases" data-toggle="tab">Product-Level Purchase</a></li>
                                        <li><a href="#payment-terms" data-toggle="tab">Payment Terms & Credit</a></li>
                                        <li><a href="#cost-trends" data-toggle="tab">Cost Trend Analysis</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="tab-content">
                                <!-- Purchase Trends -->
                                <div class="tab-pane active" id="purchase-trends">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Purchase Trends - Daily <i class="fa fa-info-circle" data-toggle="tooltip" title="Daily purchase trends showing spending patterns over time. Formula: Sum of daily purchase amount."></i></h4>
                                                <canvas id="daily_purchase_chart" height="250"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Purchase Data</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped datatable">
                                                        <thead>
                                                            <tr>
                                                                <th>Date</th>
                                                                <th>Purchase Amount</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($data['purchase_analytics']) && isset($data['purchase_analytics']['purchase_trends']))
                                                                @foreach($data['purchase_analytics']['purchase_trends'] as $trend)
                                                                <tr>
                                                                    <td>{{ \Carbon\Carbon::parse($trend->date)->format('M d, Y') }}</td>
                                                                    <td>{{ @num_format($trend->total_purchase) }}</td>
                                                                </tr>
                                                                @endforeach
                                                            @else
                                                                <tr>
                                                                    <td colspan="2" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Purchase Trends - Monthly <i class="fa fa-info-circle" data-toggle="tooltip" title="Monthly purchase trends showing spending patterns over time. Formula: Sum of monthly purchase amount."></i></h4>
                                                <canvas id="monthly_purchase_chart" height="250"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Monthly Purchase Data</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped datatable">
                                                        <thead>
                                                            <tr>
                                                                <th>Month</th>
                                                                <th>Purchase Amount</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($data['purchase_analytics']) && isset($data['purchase_analytics']['monthly_purchases']))
                                                                @foreach($data['purchase_analytics']['monthly_purchases'] as $monthly)
                                                                <tr>
                                                                    <td>{{ \Carbon\Carbon::createFromDate($monthly->year, $monthly->month, 1)->format('M Y') }}</td>
                                                                    <td>{{ @num_format($monthly->total_purchase) }}</td>
                                                                </tr>
                                                                @endforeach
                                                            @else
                                                                <tr>
                                                                    <td colspan="2" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="info-box">
                                                <h4>Purchase Forecast <i class="fa fa-info-circle" data-toggle="tooltip" title="Purchase forecast for the next 3 months based on historical data. Formula: Moving average with seasonal adjustment."></i></h4>
                                                <canvas id="purchase_forecast_chart" height="250"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Supplier Performance -->
                                <div class="tab-pane" id="supplier-performance">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Supplier Performance <i class="fa fa-info-circle" data-toggle="tooltip" title="Analysis of supplier performance based on transaction count, total purchase, and lead time. Formula: Aggregation of supplier metrics."></i></h4>
                                                <canvas id="supplier_performance_chart" height="250"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Supplier Performance Data</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped datatable">
                                                        <thead>
                                                            <tr>
                                                                <th>Supplier</th>
                                                                <th>Transaction Count</th>
                                                                <th>Total Purchase</th>
                                                                <th>Avg Lead Time (days)</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($data['purchase_analytics']) && isset($data['purchase_analytics']['supplier_performance']))
                                                                @foreach($data['purchase_analytics']['supplier_performance'] as $supplier)
                                                                <tr>
                                                                    <td>{{ $supplier->supplier_name }}</td>
                                                                    <td>{{ $supplier->transaction_count }}</td>
                                                                    <td>{{ @num_format($supplier->total_purchase) }}</td>
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
                                        </div>
                                    </div>
                                </div>

                                <!-- Product-Level Purchase -->
                                <div class="tab-pane" id="product-purchases">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Product-Level Purchase <i class="fa fa-info-circle" data-toggle="tooltip" title="Analysis of most purchased products by quantity and amount. Formula: Sum of product purchases grouped by product."></i></h4>
                                                <canvas id="product_purchases_chart" height="250"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Product-Level Purchase Data</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped datatable">
                                                        <thead>
                                                            <tr>
                                                                <th>Product</th>
                                                                <th>Category</th>
                                                                <th>Quantity</th>
                                                                <th>Total Amount</th>
                                                                <th>Avg Price</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($data['purchase_analytics']) && isset($data['purchase_analytics']['product_purchases']))
                                                                @foreach($data['purchase_analytics']['product_purchases'] as $product)
                                                                <tr>
                                                                    <td>{{ $product->product_name }}</td>
                                                                    <td>{{ $product->category_name ?? 'N/A' }}</td>
                                                                    <td>{{ @num_format($product->total_quantity) }}</td>
                                                                    <td>{{ @num_format($product->total_amount) }}</td>
                                                                    <td>{{ @num_format($product->avg_price) }}</td>
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

                                <!-- Payment Terms & Credit -->
                                <div class="tab-pane" id="payment-terms">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Payment Terms & Credit Usage <i class="fa fa-info-circle" data-toggle="tooltip" title="Analysis of payment methods and credit usage. Formula: Aggregation of payment data by method."></i></h4>
                                                <canvas id="payment_terms_chart" height="250"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Payment Terms Data</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped datatable">
                                                        <thead>
                                                            <tr>
                                                                <th>Payment Method</th>
                                                                <th>Count</th>
                                                                <th>Total Amount</th>
                                                                <th>Avg Days to Pay</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($data['purchase_analytics']) && isset($data['purchase_analytics']['payment_terms']))
                                                                @foreach($data['purchase_analytics']['payment_terms'] as $payment)
                                                                <tr>
                                                                    <td>{{ $payment->method }}</td>
                                                                    <td>{{ $payment->count }}</td>
                                                                    <td>{{ @num_format($payment->total_amount) }}</td>
                                                                    <td>{{ @num_format($payment->avg_days_to_pay) }}</td>
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

                                <!-- Cost Trend Analysis -->
                                <div class="tab-pane" id="cost-trends">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Cost Trend Analysis <i class="fa fa-info-circle" data-toggle="tooltip" title="Analysis of products with rising costs. Formula: Percentage change between first and last purchase price."></i></h4>
                                                <canvas id="cost_trends_chart" height="250"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Cost Trend Data</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped datatable">
                                                        <thead>
                                                            <tr>
                                                                <th>Product</th>
                                                                <th>First Purchase Date</th>
                                                                <th>First Purchase Price</th>
                                                                <th>Last Purchase Date</th>
                                                                <th>Last Purchase Price</th>
                                                                <th>Price Change %</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($data['purchase_analytics']) && isset($data['purchase_analytics']['cost_trends']))
                                                                @foreach($data['purchase_analytics']['cost_trends'] as $cost)
                                                                <tr>
                                                                    <td>{{ $cost->product_name }}</td>
                                                                    <td>{{ $cost->first_purchase_date ? @format_date($cost->first_purchase_date) : 'N/A' }}</td>
                                                                    <td>{{ @num_format($cost->first_purchase_price) }}</td>
                                                                    <td>{{ $cost->last_purchase_date ? @format_date($cost->last_purchase_date) : 'N/A' }}</td>
                                                                    <td>{{ @num_format($cost->last_purchase_price) }}</td>
                                                                    <td>{{ @num_format(($cost->last_purchase_price - $cost->first_purchase_price) / $cost->first_purchase_price * 100) }}%</td>
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
                    </div>
                </div>

                <!-- 3. Sales vs. Purchase Alignment Tab -->
                <div role="tabpanel" class="tab-pane" id="sales-purchase-alignment">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Sales vs. Purchase Alignment</h3>
                            <div class="box-tools pull-right">
                                <div class="dropdown">
                                    <button class="btn btn-default dropdown-toggle" type="button" id="alignmentDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                        Demand vs. Supply Gap
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="alignmentDropdown">
                                        <li class="active"><a href="#demand-supply-gap" data-toggle="tab">Demand vs. Supply Gap</a></li>
                                        <li><a href="#stock-turnover" data-toggle="tab">Stock Turnover Rate</a></li>
                                        <li><a href="#purchase-sales-ratio" data-toggle="tab">Purchase-to-Sales Ratio</a></li>
                                        <li><a href="#lead-time-effect" data-toggle="tab">Lead Time Effect</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="tab-content">
                                <!-- Demand vs. Supply Gap -->
                                <div class="tab-pane active" id="demand-supply-gap">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Demand vs. Supply Gap <i class="fa fa-info-circle" data-toggle="tooltip" title="Analysis of overstocking and understocking situations. Formula: Difference between total purchased and total sold quantities."></i></h4>
                                                <canvas id="demand_supply_gap_chart" height="250"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Demand vs. Supply Gap Data</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped datatable">
                                                        <thead>
                                                            <tr>
                                                                <th>Product</th>
                                                                <th>Total Sold</th>
                                                                <th>Total Purchased</th>
                                                                <th>Gap</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($data['sales_purchase_alignment']) && isset($data['sales_purchase_alignment']['demand_supply_gap']))
                                                                @foreach($data['sales_purchase_alignment']['demand_supply_gap'] as $gap)
                                                                <tr>
                                                                    <td>{{ $gap['product_name'] }}</td>
                                                                    <td>{{ @num_format($gap['total_sold']) }}</td>
                                                                    <td>{{ @num_format($gap['total_purchased']) }}</td>
                                                                    <td>{{ @num_format($gap['gap']) }}</td>
                                                                    <td>
                                                                        @if($gap['status'] == 'Overstocked')
                                                                            <span class="label label-warning">{{ $gap['status'] }}</span>
                                                                        @elseif($gap['status'] == 'Understocked')
                                                                            <span class="label label-danger">{{ $gap['status'] }}</span>
                                                                        @else
                                                                            <span class="label label-success">{{ $gap['status'] }}</span>
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

                                <!-- Stock Turnover Rate -->
                                <div class="tab-pane" id="stock-turnover">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Stock Turnover Rate <i class="fa fa-info-circle" data-toggle="tooltip" title="Analysis of fast-moving vs. slow-moving products. Formula: Total sold quantity / Average inventory level."></i></h4>
                                                <canvas id="stock_turnover_chart" height="250"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Stock Turnover Data</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped datatable">
                                                        <thead>
                                                            <tr>
                                                                <th>Product</th>
                                                                <th>Total Sold</th>
                                                                <th>Total Purchased</th>
                                                                <th>Turnover Rate</th>
                                                                <th>Category</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($data['sales_purchase_alignment']) && isset($data['sales_purchase_alignment']['stock_turnover']))
                                                                @foreach($data['sales_purchase_alignment']['stock_turnover'] as $turnover)
                                                                <tr>
                                                                    <td>{{ $turnover['product_name'] }}</td>
                                                                    <td>{{ @num_format($turnover['total_sold']) }}</td>
                                                                    <td>{{ @num_format($turnover['total_purchased']) }}</td>
                                                                    <td>{{ @num_format($turnover['turnover_rate']) }}</td>
                                                                    <td>
                                                                        @if($turnover['category'] == 'Fast-moving')
                                                                            <span class="label label-success">{{ $turnover['category'] }}</span>
                                                                        @elseif($turnover['category'] == 'Medium-moving')
                                                                            <span class="label label-info">{{ $turnover['category'] }}</span>
                                                                        @else
                                                                            <span class="label label-warning">{{ $turnover['category'] }}</span>
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

                                <!-- Purchase-to-Sales Ratio -->
                                <div class="tab-pane" id="purchase-sales-ratio">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Purchase-to-Sales Ratio <i class="fa fa-info-circle" data-toggle="tooltip" title="Analysis of purchase alignment with actual demand. Formula: Total purchased quantity / Total sold quantity."></i></h4>
                                                <canvas id="purchase_sales_ratio_chart" height="250"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Purchase-to-Sales Ratio Data</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped datatable">
                                                        <thead>
                                                            <tr>
                                                                <th>Product</th>
                                                                <th>Total Sold</th>
                                                                <th>Total Purchased</th>
                                                                <th>Ratio</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($data['sales_purchase_alignment']) && isset($data['sales_purchase_alignment']['purchase_sales_ratio']))
                                                                @foreach($data['sales_purchase_alignment']['purchase_sales_ratio'] as $ratio)
                                                                <tr>
                                                                    <td>{{ $ratio['product_name'] }}</td>
                                                                    <td>{{ @num_format($ratio['total_sold']) }}</td>
                                                                    <td>{{ @num_format($ratio['total_purchased']) }}</td>
                                                                    <td>{{ @num_format($ratio['ratio']) }}</td>
                                                                    <td>
                                                                        @if($ratio['status'] == 'Over-purchasing')
                                                                            <span class="label label-warning">{{ $ratio['status'] }}</span>
                                                                        @elseif($ratio['status'] == 'Under-purchasing')
                                                                            <span class="label label-danger">{{ $ratio['status'] }}</span>
                                                                        @else
                                                                            <span class="label label-success">{{ $ratio['status'] }}</span>
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

                                <!-- Lead Time Effect -->
                                <div class="tab-pane" id="lead-time-effect">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Lead Time Effect <i class="fa fa-info-circle" data-toggle="tooltip" title="Analysis of supplier delays impacting sales fulfillment. Formula: Average time between order placement and receipt."></i></h4>
                                                <canvas id="lead_time_effect_chart" height="250"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Lead Time Effect Data</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped datatable">
                                                        <thead>
                                                            <tr>
                                                                <th>Supplier</th>
                                                                <th>Avg Lead Time (days)</th>
                                                                <th>Transaction Count</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($data['sales_purchase_alignment']) && isset($data['sales_purchase_alignment']['lead_time_effect']))
                                                                @foreach($data['sales_purchase_alignment']['lead_time_effect'] as $lead_time)
                                                                <tr>
                                                                    <td>{{ $lead_time->supplier_name }}</td>
                                                                    <td>{{ @num_format($lead_time->avg_lead_time) }}</td>
                                                                    <td>{{ $lead_time->transaction_count }}</td>
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
                    </div>
                </div>

                <!-- 4. Margin & Profitability Analytics Tab -->
                <div role="tabpanel" class="tab-pane" id="margin-analytics">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Margin & Profitability Analytics</h3>
                            <div class="box-tools pull-right">
                                <div class="dropdown">
                                    <button class="btn btn-default dropdown-toggle" type="button" id="marginAnalyticsDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                        Gross Margin
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="marginAnalyticsDropdown">
                                        <li class="active"><a href="#gross-margin" data-toggle="tab">Gross Margin</a></li>
                                        <li><a href="#margin-leakage" data-toggle="tab">Margin Leakage</a></li>
                                        <li><a href="#customer-profitability" data-toggle="tab">Customer Profitability</a></li>
                                        <li><a href="#break-even" data-toggle="tab">Break-even Analysis</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="tab-content">
                                <!-- Gross Margin -->
                                <div class="tab-pane active" id="gross-margin">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Gross Margin by Product <i class="fa fa-info-circle" data-toggle="tooltip" title="Analysis of product gross margins. Formula: (Sales - Cost) / Sales * 100 for each product."></i></h4>
                                                <canvas id="gross_margin_chart" height="250"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Gross Margin Data</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped datatable">
                                                        <thead>
                                                            <tr>
                                                                <th>Product</th>
                                                                <th>Quantity</th>
                                                                <th>Total Sales</th>
                                                                <th>Total Cost</th>
                                                                <th>Gross Margin</th>
                                                                <th>Margin %</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($data['margin_analytics']) && isset($data['margin_analytics']['gross_margin']))
                                                                @foreach($data['margin_analytics']['gross_margin'] as $margin)
                                                                <tr>
                                                                    <td>{{ $margin->product_name }}</td>
                                                                    <td>{{ @num_format($margin->total_quantity) }}</td>
                                                                    <td>{{ @num_format($margin->total_sales) }}</td>
                                                                    <td>{{ @num_format($margin->total_cost) }}</td>
                                                                    <td>{{ @num_format($margin->gross_margin) }}</td>
                                                                    <td>{{ @num_format($margin->margin_percentage) }}%</td>
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

                                <!-- Margin Leakage -->
                                <div class="tab-pane" id="margin-leakage">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Margin Leakage <i class="fa fa-info-circle" data-toggle="tooltip" title="Analysis of discounts reducing margins. Formula: Discount amount as a percentage of total sales."></i></h4>
                                                <canvas id="margin_leakage_chart" height="250"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Margin Leakage Data</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped datatable">
                                                        <thead>
                                                            <tr>
                                                                <th>Invoice No</th>
                                                                <th>Date</th>
                                                                <th>Final Total</th>
                                                                <th>Discount Amount</th>
                                                                <th>Discount %</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($data['margin_analytics']) && isset($data['margin_analytics']['margin_leakage']))
                                                                @foreach($data['margin_analytics']['margin_leakage'] as $leakage)
                                                                <tr>
                                                                    <td>{{ $leakage->invoice_no }}</td>
                                                                    <td>{{ @format_date($leakage->transaction_date) }}</td>
                                                                    <td>{{ @num_format($leakage->final_total) }}</td>
                                                                    <td>{{ @num_format($leakage->discount_amount) }}</td>
                                                                    <td>{{ @num_format($leakage->discount_percentage) }}%</td>
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

                                <!-- Customer Profitability -->
                                <div class="tab-pane" id="customer-profitability">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Customer Profitability <i class="fa fa-info-circle" data-toggle="tooltip" title="Analysis of customer profitability. Formula: (Sales - Cost) / Sales * 100 for each customer."></i></h4>
                                                <canvas id="customer_profitability_chart" height="250"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Customer Profitability Data</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped datatable">
                                                        <thead>
                                                            <tr>
                                                                <th>Customer</th>
                                                                <th>Transaction Count</th>
                                                                <th>Total Sales</th>
                                                                <th>Total Cost</th>
                                                                <th>Gross Profit</th>
                                                                <th>Profit Margin %</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($data['margin_analytics']) && isset($data['margin_analytics']['customer_profitability']))
                                                                @foreach($data['margin_analytics']['customer_profitability'] as $profit)
                                                                <tr>
                                                                    <td>{{ $profit->customer_name }}</td>
                                                                    <td>{{ $profit->transaction_count }}</td>
                                                                    <td>{{ @num_format($profit->total_sales) }}</td>
                                                                    <td>{{ @num_format($profit->total_cost) }}</td>
                                                                    <td>{{ @num_format($profit->gross_profit) }}</td>
                                                                    <td>{{ @num_format($profit->profit_margin) }}%</td>
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

                                <!-- Break-even Analysis -->
                                <div class="tab-pane" id="break-even">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Break-even Analysis <i class="fa fa-info-circle" data-toggle="tooltip" title="Analysis of sales volume required to cover purchase costs. Formula: Fixed costs / (Price - Variable cost per unit)."></i></h4>
                                                <canvas id="break_even_chart" height="250"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Break-even Data</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped datatable">
                                                        <thead>
                                                            <tr>
                                                                <th>Product</th>
                                                                <th>Avg Selling Price</th>
                                                                <th>Avg Purchase Price</th>
                                                                <th>Avg Profit Per Unit</th>
                                                                <th>Fixed Costs</th>
                                                                <th>Break-even Units</th>
                                                                <th>Break-even Revenue</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($data['margin_analytics']) && isset($data['margin_analytics']['break_even']))
                                                                @foreach($data['margin_analytics']['break_even'] as $break_even)
                                                                <tr>
                                                                    <td>{{ $break_even->product_name }}</td>
                                                                    <td>{{ @num_format($break_even->avg_selling_price) }}</td>
                                                                    <td>{{ @num_format($break_even->avg_purchase_price) }}</td>
                                                                    <td>{{ @num_format($break_even->avg_profit_per_unit) }}</td>
                                                                    <td>{{ @num_format($break_even->fixed_costs) }}</td>
                                                                    <td>{{ @num_format($break_even->break_even_units) }}</td>
                                                                    <td>{{ @num_format($break_even->break_even_revenue) }}</td>
                                                                </tr>
                                                                @endforeach
                                                            @else
                                                                <tr>
                                                                    <td colspan="7" class="text-center">{{ __('lang_v1.no_data_found') }}</td>
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

                <!-- 5. Predictive & Prescriptive Analytics Tab -->
                <div role="tabpanel" class="tab-pane" id="predictive-analytics">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Predictive & Prescriptive Analytics</h3>
                            <div class="box-tools pull-right">
                                <div class="dropdown">
                                    <button class="btn btn-default dropdown-toggle" type="button" id="predictiveAnalyticsDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                        Demand Forecasting
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="predictiveAnalyticsDropdown">
                                        <li class="active"><a href="#demand-forecast" data-toggle="tab">Demand Forecasting</a></li>
                                        <li><a href="#purchase-forecast" data-toggle="tab">Purchase Forecasting</a></li>
                                        <li><a href="#reorder-points" data-toggle="tab">Reorder Point Prediction</a></li>
                                        <li><a href="#pricing-simulation" data-toggle="tab">Dynamic Pricing Simulation</a></li>
                                        <li><a href="#retention-triggers" data-toggle="tab">Retention Triggers</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="tab-content">
                                <!-- Demand Forecasting -->
                                <div class="tab-pane active" id="demand-forecast">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Demand Forecasting <i class="fa fa-info-circle" data-toggle="tooltip" title="Prediction of future product demand. Formula: Time series forecasting with seasonal adjustments."></i></h4>
                                                <canvas id="demand_forecast_chart" height="250"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Demand Forecast Data</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped datatable">
                                                        <thead>
                                                            <tr>
                                                                <th>Product</th>
                                                                <th>Category</th>
                                                                <th>Avg Monthly Demand</th>
                                                                <th>Forecast (Next 3 Months)</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($data['predictive_analytics']) && isset($data['predictive_analytics']['demand_forecast']))
                                                                @foreach($data['predictive_analytics']['demand_forecast'] as $forecast)
                                                                <tr>
                                                                    <td>{{ $forecast['product_name'] }}</td>
                                                                    <td>{{ $forecast['category_name'] ?? 'N/A' }}</td>
                                                                    <td>{{ @num_format($forecast['avg_monthly_demand']) }}</td>
                                                                    <td>
                                                                        @if(isset($forecast['forecast_months']))
                                                                            @foreach($forecast['forecast_months'] as $month)
                                                                                {{ date('M Y', strtotime($month['year'].'-'.$month['month'].'-01')) }}: {{ @num_format($month['forecast_quantity']) }}<br>
                                                                            @endforeach
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

                                <!-- Purchase Forecasting -->
                                <div class="tab-pane" id="purchase-forecast">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Purchase Forecasting <i class="fa fa-info-circle" data-toggle="tooltip" title="Estimation of future procurement needs. Formula: Demand forecast adjusted for lead time and safety stock."></i></h4>
                                                <canvas id="purchase_forecast_analysis_chart" height="250"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Purchase Forecast Data</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped datatable">
                                                        <thead>
                                                            <tr>
                                                                <th>Product</th>
                                                                <th>Avg Monthly Demand</th>
                                                                <th>Avg Daily Demand</th>
                                                                <th>Lead Time (days)</th>
                                                                <th>Recommended Order Quantity</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($data['predictive_analytics']) && isset($data['predictive_analytics']['purchase_forecast']))
                                                                @foreach($data['predictive_analytics']['purchase_forecast'] as $forecast)
                                                                <tr>
                                                                    <td>{{ $forecast['product_name'] }}</td>
                                                                    <td>{{ @num_format($forecast['avg_monthly_demand']) }}</td>
                                                                    <td>{{ @num_format($forecast['avg_daily_demand']) }}</td>
                                                                    <td>{{ @num_format($forecast['lead_time_days']) }}</td>
                                                                    <td>{{ @num_format($forecast['recommended_order_quantity']) }}</td>
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

                                <!-- Reorder Point Prediction -->
                                <div class="tab-pane" id="reorder-points">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Reorder Point Prediction <i class="fa fa-info-circle" data-toggle="tooltip" title="Prediction of when to replenish stock. Formula: (Average daily demand × Lead time) + Safety stock."></i></h4>
                                                <canvas id="reorder_points_chart" height="250"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Reorder Point Data</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped datatable">
                                                        <thead>
                                                            <tr>
                                                                <th>Product</th>
                                                                <th>Avg Daily Demand</th>
                                                                <th>Lead Time (days)</th>
                                                                <th>Safety Stock</th>
                                                                <th>Reorder Point</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($data['predictive_analytics']) && isset($data['predictive_analytics']['reorder_points']))
                                                                @foreach($data['predictive_analytics']['reorder_points'] as $reorder)
                                                                <tr>
                                                                    <td>{{ $reorder['product_name'] }}</td>
                                                                    <td>{{ @num_format($reorder['avg_daily_demand']) }}</td>
                                                                    <td>{{ @num_format($reorder['lead_time_days']) }}</td>
                                                                    <td>{{ @num_format($reorder['safety_stock']) }}</td>
                                                                    <td>{{ @num_format($reorder['reorder_point']) }}</td>
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

                                <!-- Dynamic Pricing Simulation -->
                                <div class="tab-pane" id="pricing-simulation">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Dynamic Pricing Simulation <i class="fa fa-info-circle" data-toggle="tooltip" title="Simulation of pricing scenarios based on cost changes. Formula: Various pricing models based on cost, competition, and demand elasticity."></i></h4>
                                                <canvas id="pricing_simulation_chart" height="250"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Pricing Simulation Data</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped datatable">
                                                        <thead>
                                                            <tr>
                                                                <th>Product</th>
                                                                <th>Avg Selling Price</th>
                                                                <th>Avg Purchase Price</th>
                                                                <th>Avg Margin</th>
                                                                <th>Margin %</th>
                                                                <th>Pricing Scenarios</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($data['predictive_analytics']) && isset($data['predictive_analytics']['pricing_simulation']))
                                                                @foreach($data['predictive_analytics']['pricing_simulation'] as $pricing)
                                                                <tr>
                                                                    <td>{{ $pricing->product_name }}</td>
                                                                    <td>{{ @num_format($pricing->avg_selling_price) }}</td>
                                                                    <td>{{ @num_format($pricing->avg_purchase_price) }}</td>
                                                                    <td>{{ @num_format($pricing->avg_margin) }}</td>
                                                                    <td>{{ @num_format($pricing->margin_percentage) }}%</td>
                                                                    <td>
                                                                        @if(isset($pricing->pricing_scenarios))
                                                                            @foreach($pricing->pricing_scenarios as $scenario)
                                                                                <strong>{{ $scenario['scenario'] }}</strong><br>
                                                                                New Purchase Price: {{ @num_format($scenario['new_purchase_price']) }}<br>
                                                                                Price to Maintain Margin: {{ @num_format($scenario['maintain_margin_price']) }}<br>
                                                                                @if(isset($scenario['price_increase_needed']))
                                                                                    Price Increase Needed: {{ @num_format($scenario['price_increase_needed']) }}%<br>
                                                                                @endif
                                                                                @if(isset($scenario['price_decrease_possible']))
                                                                                    Price Decrease Possible: {{ @num_format($scenario['price_decrease_possible']) }}%<br>
                                                                                @endif
                                                                                <hr>
                                                                            @endforeach
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

                                <!-- Retention Triggers -->
                                <div class="tab-pane" id="retention-triggers">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Retention Triggers <i class="fa fa-info-circle" data-toggle="tooltip" title="Prediction of customers at risk of churning. Formula: Analysis based on purchase recency, frequency, and monetary value."></i></h4>
                                                <canvas id="retention_triggers_chart" height="250"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <h4>Retention Triggers Data</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped datatable">
                                                        <thead>
                                                            <tr>
                                                                <th>Customer</th>
                                                                <th>Last Purchase Date</th>
                                                                <th>Days Since Last Purchase</th>
                                                                <th>Purchase Count</th>
                                                                <th>Total Spent</th>
                                                                <th>Avg Purchase Interval (days)</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($data['predictive_analytics']) && isset($data['predictive_analytics']['retention_triggers']))
                                                                @foreach($data['predictive_analytics']['retention_triggers'] as $retention)
                                                                <tr>
                                                                    <td>{{ $retention->name }}</td>
                                                                    <td>{{ $retention->last_purchase_date ? @format_date($retention->last_purchase_date) : 'N/A' }}</td>
                                                                    <td>{{ $retention->days_since_last_purchase }}</td>
                                                                    <td>{{ $retention->purchase_count }}</td>
                                                                    <td>{{ @num_format($retention->total_spent) }}</td>
                                                                    <td>{{ @num_format($retention->avg_purchase_interval) }}</td>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Initialize datatables
    $('.datatable').DataTable({
        responsive: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]]
    });

    // Update dropdown text based on active tab
    $('.dropdown-menu a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var text = $(e.target).text();
        $(this).closest('.dropdown').find('.dropdown-toggle').html(text + ' <span class="caret"></span>');
    });

    // Daily Sales Chart
    var dailySalesCtx = document.getElementById('daily_sales_chart').getContext('2d');
    var dailySalesData = {
        labels: [
            @if(isset($data['sales_analytics']) && isset($data['sales_analytics']['sales_trends']))
                @foreach($data['sales_analytics']['sales_trends'] as $trend)
                    '{{ \Carbon\Carbon::parse($trend->date)->format('M d') }}',
                @endforeach
            @endif
        ],
        datasets: [{
            label: 'Daily Sales',
            data: [
                @if(isset($data['sales_analytics']) && isset($data['sales_analytics']['sales_trends']))
                    @foreach($data['sales_analytics']['sales_trends'] as $trend)
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
            @if(isset($data['sales_analytics']) && isset($data['sales_analytics']['monthly_sales']))
                @foreach($data['sales_analytics']['monthly_sales'] as $monthly)
                    '{{ \Carbon\Carbon::createFromDate($monthly->year, $monthly->month, 1)->format('M Y') }}',
                @endforeach
            @endif
        ],
        datasets: [{
            label: 'Monthly Sales',
            data: [
                @if(isset($data['sales_analytics']) && isset($data['sales_analytics']['monthly_sales']))
                    @foreach($data['sales_analytics']['monthly_sales'] as $monthly)
                        {{ $monthly->total_sales }},
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

    // Sales Forecast Chart
    var salesForecastCtx = document.getElementById('sales_forecast_chart').getContext('2d');
    var salesForecastData = {
        labels: [
            @if(isset($data['sales_analytics']) && isset($data['sales_analytics']['monthly_sales']))
                @foreach($data['sales_analytics']['monthly_sales'] as $monthly)
                    '{{ \Carbon\Carbon::createFromDate($monthly->year, $monthly->month, 1)->format('M Y') }}',
                @endforeach
                // Add 3 months forecast
                @php
                    if(isset($data['sales_analytics']) && isset($data['sales_analytics']['monthly_sales']) && count($data['sales_analytics']['monthly_sales']) > 0) {
                        $lastMonth = $data['sales_analytics']['monthly_sales'][count($data['sales_analytics']['monthly_sales']) - 1];
                        $lastDate = \Carbon\Carbon::createFromDate($lastMonth->year, $lastMonth->month, 1);
                        for($i = 1; $i <= 3; $i++) {
                            $forecastDate = $lastDate->copy()->addMonths($i);
                            echo "'".$forecastDate->format('M Y')."',";
                        }
                    }
                @endphp
            @endif
        ],
        datasets: [{
            label: 'Historical Sales',
            data: [
                @if(isset($data['sales_analytics']) && isset($data['sales_analytics']['monthly_sales']))
                    @foreach($data['sales_analytics']['monthly_sales'] as $monthly)
                        {{ $monthly->total_sales }},
                    @endforeach
                @endif
            ],
            backgroundColor: 'rgba(60, 141, 188, 0.2)',
            borderColor: 'rgba(60, 141, 188, 1)',
            borderWidth: 1
        },
        {
            label: 'Forecast',
            data: [
                @if(isset($data['sales_analytics']) && isset($data['sales_analytics']['monthly_sales']) && count($data['sales_analytics']['monthly_sales']) > 0)
                    @php
                        // Simple forecasting based on average of last 3 months
                        $count = count($data['sales_analytics']['monthly_sales']);
                        $lastThreeMonths = array_slice($data['sales_analytics']['monthly_sales'], max(0, $count - 3), min(3, $count));
                        $sum = 0;
                        foreach($lastThreeMonths as $month) {
                            $sum += $month->total_sales;
                        }
                        $avg = $sum / count($lastThreeMonths);
                        
                        // Fill historical data with null
                        for($i = 0; $i < $count; $i++) {
                            echo "null,";
                        }
                        
                        // Add forecast data
                        for($i = 1; $i <= 3; $i++) {
                            // Add some randomness to forecast
                            $forecast = $avg * (1 + (mt_rand(-10, 10) / 100));
                            echo round($forecast, 2).",";
                        }
                    @endphp
                @endif
            ],
            backgroundColor: 'rgba(255, 193, 7, 0.2)',
            borderColor: 'rgba(255, 193, 7, 1)',
            borderWidth: 1,
            borderDash: [5, 5]
        }]
    };
    var salesForecastChart = new Chart(salesForecastCtx, {
        type: 'line',
        data: salesForecastData,
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
            @if(isset($data['purchase_analytics']) && isset($data['purchase_analytics']['purchase_trends']))
                @foreach($data['purchase_analytics']['purchase_trends'] as $trend)
                    '{{ \Carbon\Carbon::parse($trend->date)->format('M d') }}',
                @endforeach
            @endif
        ],
        datasets: [{
            label: 'Daily Purchases',
            data: [
                @if(isset($data['purchase_analytics']) && isset($data['purchase_analytics']['purchase_trends']))
                    @foreach($data['purchase_analytics']['purchase_trends'] as $trend)
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
            @if(isset($data['purchase_analytics']) && isset($data['purchase_analytics']['monthly_purchases']))
                @foreach($data['purchase_analytics']['monthly_purchases'] as $monthly)
                    '{{ \Carbon\Carbon::createFromDate($monthly->year, $monthly->month, 1)->format('M Y') }}',
                @endforeach
            @endif
        ],
        datasets: [{
            label: 'Monthly Purchases',
            data: [
                @if(isset($data['purchase_analytics']) && isset($data['purchase_analytics']['monthly_purchases']))
                    @foreach($data['purchase_analytics']['monthly_purchases'] as $monthly)
                        {{ $monthly->total_purchase }},
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
});
</script>