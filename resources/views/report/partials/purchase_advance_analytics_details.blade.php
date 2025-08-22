<div class="row">
    <div class="col-md-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#purchase_trends" data-toggle="tab" aria-expanded="true">{{ __('Purchase Trends') }}</a></li>
                <li><a href="#supplier_analytics" data-toggle="tab">{{ __('Supplier Analytics') }}</a></li>
                <li><a href="#product_level_analytics" data-toggle="tab">{{ __('Product-Level Analytics') }}</a></li>
                <li><a href="#cost_margin_impact" data-toggle="tab">{{ __('Cost & Margin Impact') }}</a></li>
                <li><a href="#inventory_stock_planning" data-toggle="tab">{{ __('Inventory & Stock Planning') }}</a></li>
                <li><a href="#payment_credit_terms" data-toggle="tab">{{ __('Payment & Credit Terms') }}</a></li>
                <li><a href="#predictive_analytics" data-toggle="tab">{{ __('Predictive Analytics') }}</a></li>
            </ul>

            <div class="tab-content">
                <!-- Purchase Trends -->
                <div class="tab-pane active" id="purchase_trends">
                    <div class="row">
                        <div class="col-md-12">
                            <h3>
                                {{ __('Total purchases over time') }}
                                <i class="fa fa-info-circle text-info cursor-pointer" 
                                   data-toggle="tooltip" 
                                   title="{{ __('This analysis shows the total purchase amounts and transaction counts over time, helping you identify trends and patterns in your purchasing behavior.') }}"></i>
                            </h3>

                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">{{ __('Purchase Trends Analysis') }}</h3>
                                    <div class="box-tools pull-right">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                <span id="purchase_trends_view_type">{{ __('Monthly View') }}</span> <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">
                                                <li><a href="#" class="purchase-trends-view" data-view="monthly">{{ __('Monthly View') }}</a></li>
                                                <li><a href="#" class="purchase-trends-view" data-view="quarterly">{{ __('Quarterly View') }}</a></li>
                                                <li><a href="#" class="purchase-trends-view" data-view="yearly">{{ __('Yearly View') }}</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div id="purchase_trends_chart" style="min-height: 250px;"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="purchase-trends-table-container" id="monthly_purchases_table_container">
                                                <table class="table table-bordered table-striped datatable">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('Year') }}</th>
                                                            <th>{{ __('Month') }}</th>
                                                            <th>{{ __('Total Purchase') }}</th>
                                                            <th>{{ __('Transaction Count') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($data['monthly_purchases'] ?? [] as $purchase)
                                                            <tr>
                                                                <td>{{ $purchase->year }}</td>
                                                                <td>{{ $purchase->month }}</td>
                                                                <td class="display_currency">{{ $purchase->total_purchase }}</td>
                                                                <td>{{ $purchase->transaction_count }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="purchase-trends-table-container" id="quarterly_purchases_table_container" style="display: none;">
                                                <table class="table table-bordered table-striped datatable">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('Year') }}</th>
                                                            <th>{{ __('Quarter') }}</th>
                                                            <th>{{ __('Total Purchase') }}</th>
                                                            <th>{{ __('Transaction Count') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($data['quarterly_purchases']))
                                                            @foreach($data['quarterly_purchases'] as $purchase)
                                                                <tr>
                                                                    <td>{{ $purchase->year }}</td>
                                                                    <td>{{ $purchase->quarter }}</td>
                                                                    <td class="display_currency">{{ $purchase->total_purchase }}</td>
                                                                    <td>{{ $purchase->transaction_count }}</td>
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

                                            <div class="purchase-trends-table-container" id="yearly_purchases_table_container" style="display: none;">
                                                <table class="table table-bordered table-striped datatable">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('Year') }}</th>
                                                            <th>{{ __('Total Purchase') }}</th>
                                                            <th>{{ __('Transaction Count') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($data['yearly_purchases']))
                                                            @foreach($data['yearly_purchases'] as $purchase)
                                                                <tr>
                                                                    <td>{{ $purchase->year }}</td>
                                                                    <td class="display_currency">{{ $purchase->total_purchase }}</td>
                                                                    <td>{{ $purchase->transaction_count }}</td>
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

                        <div class="col-md-12">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">{{ __('Quarterly Purchases') }}</h3>
                                </div>
                                <div class="box-body">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Year') }}</th>
                                                <th>{{ __('Quarter') }}</th>
                                                <th>{{ __('Total Purchase') }}</th>
                                                <th>{{ __('Transaction Count') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($data['quarterly_purchases']))
                                                @foreach($data['quarterly_purchases'] as $purchase)
                                                    <tr>
                                                        <td>{{ $purchase->year }}</td>
                                                        <td>{{ $purchase->quarter }}</td>
                                                        <td class="display_currency">{{ $purchase->total_purchase }}</td>
                                                        <td>{{ $purchase->transaction_count }}</td>
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

                        <div class="col-md-12">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">{{ __('Yearly Purchases') }}</h3>
                                </div>
                                <div class="box-body">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Year') }}</th>
                                                <th>{{ __('Total Purchase') }}</th>
                                                <th>{{ __('Transaction Count') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($data['yearly_purchases']))
                                                @foreach($data['yearly_purchases'] as $purchase)
                                                    <tr>
                                                        <td>{{ $purchase->year }}</td>
                                                        <td class="display_currency">{{ $purchase->total_purchase }}</td>
                                                        <td>{{ $purchase->transaction_count }}</td>
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

                        <div class="col-md-12">
                            <h3>
                                {{ __('Seasonality in purchases') }}
                                <i class="fa fa-info-circle text-info cursor-pointer" 
                                   data-toggle="tooltip" 
                                   title="{{ __('This analysis identifies seasonal patterns in your purchasing behavior, helping you anticipate and plan for cyclical demand.') }}"></i>
                            </h3>
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">{{ __('Monthly Purchase Trends') }}</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div id="seasonality_chart" style="min-height: 250px;"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-bordered table-striped datatable">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Month') }}</th>
                                                        <th>{{ __('Total Purchase') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(isset($data['monthly_purchase_trends']))
                                                        @foreach($data['monthly_purchase_trends'] as $trend)
                                                            <tr>
                                                                <td>{{ $trend->month }}</td>
                                                                <td class="display_currency">{{ $trend->total_purchase }}</td>
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
                        </div>

                        <div class="col-md-12">
                            <h3>
                                {{ __('Growth in purchase volume vs. growth in sales volume') }}
                                <i class="fa fa-info-circle text-info cursor-pointer" 
                                   data-toggle="tooltip" 
                                   title="{{ __('This analysis compares the growth rates of purchases and sales, helping you identify if your inventory acquisition is aligned with your sales performance.') }}"></i>
                            </h3>
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">{{ __('Growth Comparison') }}</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div id="growth_comparison_chart" style="min-height: 250px;"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-bordered table-striped datatable">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Month') }}</th>
                                                        <th>{{ __('Purchase Growth Rate (%)') }}</th>
                                                        <th>{{ __('Sales Growth Rate (%)') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(isset($data['purchase_growth']))
                                                        @foreach($data['purchase_growth'] as $month => $growth)
                                                            <tr>
                                                                <td>{{ $month }}</td>
                                                                <td>{{ number_format($growth['growth_rate'], 2) }}%</td>
                                                                <td>
                                                                    @if(isset($data['sales_growth'][$month]))
                                                                        {{ number_format($data['sales_growth'][$month]['growth_rate'], 2) }}%
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </td>
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

                <!-- Supplier Analytics -->
                <div class="tab-pane" id="supplier_analytics">
                    <div class="row">
                        <div class="col-md-12">
                            <h3>
                                {{ __('Top suppliers by spend') }}
                                <i class="fa fa-info-circle text-info cursor-pointer" 
                                   data-toggle="tooltip" 
                                   title="{{ __('This analysis identifies your most important suppliers by purchase volume, helping you prioritize supplier relationships and negotiate better terms.') }}"></i>
                            </h3>
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">{{ __('Top Suppliers') }}</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div id="top_suppliers_chart" style="min-height: 250px;"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-bordered table-striped datatable">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Supplier') }}</th>
                                                        <th>{{ __('Total Purchase') }}</th>
                                                        <th>{{ __('Transaction Count') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(isset($data['top_suppliers']))
                                                        @foreach($data['top_suppliers'] as $supplier)
                                                            <tr>
                                                                <td>{{ $supplier->supplier_name }}</td>
                                                                <td class="display_currency">{{ $supplier->total_purchase }}</td>
                                                                <td>{{ $supplier->transaction_count }}</td>
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

                        <div class="col-md-12">
                            <h3>
                                {{ __('On-time delivery rate') }}
                                <i class="fa fa-info-circle text-info cursor-pointer" 
                                   data-toggle="tooltip" 
                                   title="{{ __('This analysis measures supplier reliability by tracking on-time delivery performance, helping you identify dependable suppliers and those that may cause supply chain disruptions.') }}"></i>
                            </h3>
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">{{ __('Delivery Performance') }}</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div id="delivery_performance_chart" style="min-height: 250px;"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-bordered table-striped datatable">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Supplier') }}</th>
                                                        <th>{{ __('Total Orders') }}</th>
                                                        <th>{{ __('On-time Orders') }}</th>
                                                        <th>{{ __('On-time Delivery Rate (%)') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(isset($data['on_time_delivery']))
                                                        @foreach($data['on_time_delivery'] as $supplier)
                                                            <tr>
                                                                <td>{{ $supplier->supplier_name }}</td>
                                                                <td>{{ $supplier->total_orders }}</td>
                                                                <td>{{ $supplier->on_time_orders }}</td>
                                                                <td>
                                                                    @if($supplier->total_orders > 0)
                                                                        {{ number_format(($supplier->on_time_orders / $supplier->total_orders) * 100, 2) }}%
                                                                    @else
                                                                        -
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

                        <div class="col-md-12">
                            <h3>
                                {{ __('Supplier concentration risk') }}
                                <i class="fa fa-info-circle text-info cursor-pointer" 
                                   data-toggle="tooltip" 
                                   title="{{ __('This analysis evaluates your dependency on specific suppliers, helping you identify and mitigate risks associated with over-reliance on a single source.') }}"></i>
                            </h3>
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">{{ __('Supplier Concentration') }}</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div id="supplier_concentration_chart" style="min-height: 250px;"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-bordered table-striped datatable">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Supplier') }}</th>
                                                        <th>{{ __('Total Purchase') }}</th>
                                                        <th>{{ __('Percentage of Total (%)') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(isset($data['supplier_concentration']))
                                                        @foreach($data['supplier_concentration'] as $supplier)
                                                            <tr>
                                                                <td>{{ $supplier['supplier_name'] }}</td>
                                                                <td class="display_currency">{{ $supplier['total_purchase'] }}</td>
                                                                <td>{{ number_format($supplier['percentage'], 2) }}%</td>
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

                        <div class="col-md-12">
                            <h3>
                                {{ __('Price comparison across suppliers for the same product') }}
                                <i class="fa fa-info-circle text-info cursor-pointer" 
                                   data-toggle="tooltip" 
                                   title="{{ __('This analysis compares pricing from different suppliers for the same products, helping you identify the most cost-effective sources and negotiate better prices.') }}"></i>
                            </h3>
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">{{ __('Price Comparison') }}</h3>
                                    @if(isset($data['price_comparison']) && count($data['price_comparison']) > 0)
                                    <div class="box-tools pull-right">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                <span id="price_comparison_product">{{ array_values($data['price_comparison'])[0]['product_name'] }}</span> <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">
                                                @foreach($data['price_comparison'] as $product_id => $product)
                                                    <li><a href="#" class="price-comparison-product" data-product-id="{{ $product_id }}">{{ $product['product_name'] }}</a></li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <div class="box-body">
                                    @if(isset($data['price_comparison']))
                                        <div class="row">
                                            @foreach($data['price_comparison'] as $product_id => $product)
                                                <div class="price-comparison-container" id="price_comparison_{{ $product_id }}" style="{{ $loop->first ? '' : 'display: none;' }}">
                                                    <div class="col-md-6">
                                                        <div id="price_comparison_chart_{{ $product_id }}" style="min-height: 250px;"></div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <table class="table table-bordered table-striped datatable">
                                                            <thead>
                                                                <tr>
                                                                    <th>{{ __('Supplier') }}</th>
                                                                    <th>{{ __('Average Price') }}</th>
                                                                    <th>{{ __('Min Price') }}</th>
                                                                    <th>{{ __('Max Price') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($product['suppliers'] as $supplier)
                                                                    <tr>
                                                                        <td>{{ $supplier['supplier_name'] }}</td>
                                                                        <td class="display_currency">{{ $supplier['avg_price'] }}</td>
                                                                        <td class="display_currency">{{ $supplier['min_price'] }}</td>
                                                                        <td class="display_currency">{{ $supplier['max_price'] }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-center">{{ __('lang_v1.no_data_found') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product-Level Analytics -->
                <div class="tab-pane" id="product_level_analytics">
                    <div class="row">
                        <div class="col-md-12">
                            <h3>
                                {{ __('Most purchased products (by volume)') }}
                                <i class="fa fa-info-circle text-info cursor-pointer" 
                                   data-toggle="tooltip" 
                                   title="{{ __('This analysis identifies your most frequently purchased products by quantity, helping you understand which items require the most inventory management attention.') }}"></i>
                            </h3>
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">{{ __('Top Products by Volume') }}</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div id="top_products_volume_chart" style="min-height: 250px;"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-bordered table-striped datatable">
                                                <thead>
                                                    <tr>
                                                        <th>{{ is_array(__('Product')) ? 'Product' : __('Product') }}</th>
                                                        <th>{{ __('Total Quantity') }}</th>
                                                        <th>{{ __('Total Amount') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(isset($data['most_purchased_by_volume']))
                                                        @foreach($data['most_purchased_by_volume'] as $product)
                                                            <tr>
                                                                <td>{{ $product->product_name }}</td>
                                                                <td>{{ $product->total_quantity }}</td>
                                                                <td class="display_currency">{{ $product->total_amount }}</td>
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

                        <div class="col-md-12">
                            <h3>
                                {{ __('Most purchased products (by spend)') }}
                                <i class="fa fa-info-circle text-info cursor-pointer" 
                                   data-toggle="tooltip" 
                                   title="{{ __('This analysis identifies your highest-cost products by total spend, helping you focus cost reduction efforts on items with the greatest financial impact.') }}"></i>
                            </h3>
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">{{ __('Top Products by Spend') }}</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div id="top_products_spend_chart" style="min-height: 250px;"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-bordered table-striped datatable">
                                                <thead>
                                                    <tr>
                                                        <th>{{ is_array(__('Product')) ? 'Product' : __('Product') }}</th>
                                                        <th>{{ is_array(__('Total Quantity')) ? 'Total Quantity' : __('Total Quantity') }}</th>
                                                        <th>{{ is_array(__('Total Amount')) ? 'Total Amount' : __('Total Amount') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(isset($data['most_purchased_by_spend']))
                                                        @foreach($data['most_purchased_by_spend'] as $product)
                                                            <tr>
                                                                <td>{{ $product->product_name }}</td>
                                                                <td>{{ $product->total_quantity }}</td>
                                                                <td class="display_currency">{{ $product->total_amount }}</td>
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

                        <div class="col-md-12">
                            <h3>
                                {{ __('Purchase price trend (per product over time)') }}
                                <i class="fa fa-info-circle text-info cursor-pointer" 
                                   data-toggle="tooltip" 
                                   title="{{ __('This analysis tracks how product prices change over time, helping you identify inflation trends, seasonal price fluctuations, and opportunities for strategic purchasing.') }}"></i>
                            </h3>
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">{{ __('Price Trends') }}</h3>
                                    @if(isset($data['price_trends']) && count($data['price_trends']) > 0)
                                    <div class="box-tools pull-right">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                <span id="price_trend_product">{{ array_keys($data['price_trends'])[0] }}</span> <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">
                                                @foreach($data['price_trends'] as $product_name => $trends)
                                                    <li><a href="#" class="price-trend-product" data-product="{{ $product_name }}">{{ $product_name }}</a></li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <div class="box-body">
                                    @if(isset($data['price_trends']))
                                        <div class="row">
                                            @foreach($data['price_trends'] as $product_name => $trends)
                                                <div class="price-trend-container" id="price_trend_{{ str_replace(' ', '_', $product_name) }}" style="{{ $loop->first ? '' : 'display: none;' }}">
                                                    <div class="col-md-6">
                                                        <div id="price_trend_chart_{{ str_replace(' ', '_', $product_name) }}" style="min-height: 250px;"></div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <table class="table table-bordered table-striped datatable">
                                                            <thead>
                                                                <tr>
                                                                    <th>{{ __('Month-Year') }}</th>
                                                                    <th>{{ __('Average Price') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($trends as $trend)
                                                                    <tr>
                                                                        <td>{{ $trend['month_year'] }}</td>
                                                                        <td class="display_currency">{{ $trend['avg_price'] }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-center">{{ __('lang_v1.no_data_found') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <h3>
                                {{ __('Identify volatile cost products') }}
                                <i class="fa fa-info-circle text-info cursor-pointer" 
                                   data-toggle="tooltip" 
                                   title="{{ __('This analysis identifies products with highly variable purchase prices, helping you manage budget uncertainty and negotiate more stable pricing agreements. The coefficient of variation measures price volatility - higher percentages indicate more volatile prices.') }}"></i>
                            </h3>
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">{{ __('Volatile Products') }}</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div id="volatile_products_chart" style="min-height: 250px;"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-bordered table-striped datatable">
                                                <thead>
                                                    <tr>
                                                        <th>{{ is_array(__('Product')) ? 'Product' : __('Product') }}</th>
                                                        <th>{{ is_array(__('Min Price')) ? 'Min Price' : __('Min Price') }}</th>
                                                        <th>{{ is_array(__('Max Price')) ? 'Max Price' : __('Max Price') }}</th>
                                                        <th>{{ is_array(__('Average Price')) ? 'Average Price' : __('Average Price') }}</th>
                                                        <th>{{ is_array(__('Standard Deviation')) ? 'Standard Deviation' : __('Standard Deviation') }}</th>
                                                        <th>{{ is_array(__('Coefficient of Variation (%)')) ? 'Coefficient of Variation (%)' : __('Coefficient of Variation (%)') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(isset($data['volatile_products']))
                                                        @foreach($data['volatile_products'] as $product)
                                                            <tr>
                                                                <td>{{ $product['product_name'] }}</td>
                                                                <td class="display_currency">{{ $product['min_price'] }}</td>
                                                                <td class="display_currency">{{ $product['max_price'] }}</td>
                                                                <td class="display_currency">{{ $product['avg_price'] }}</td>
                                                                <td class="display_currency">{{ $product['std_deviation'] }}</td>
                                                                <td>{{ number_format($product['coefficient_of_variation'], 2) }}%</td>
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

                <!-- Cost & Margin Impact -->
                <div class="tab-pane" id="cost_margin_impact">
                    <div class="row">
                        <div class="col-md-12">
                            <h3>
                                {{ __('Track how purchase cost changes affect sales margins') }}
                                <i class="fa fa-info-circle text-info cursor-pointer" 
                                   data-toggle="tooltip" 
                                   title="{{ __('This analysis shows how changes in purchase costs impact your profit margins over time, helping you understand the relationship between cost fluctuations and profitability.') }}"></i>
                            </h3>
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">{{ __('Margin Impact') }}</h3>
                                    @if(isset($data['margin_impact']) && count($data['margin_impact']) > 0)
                                    <div class="box-tools pull-right">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                <span id="margin_impact_product">{{ array_keys($data['margin_impact'])[0] }}</span> <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">
                                                @foreach($data['margin_impact'] as $product_name => $margins)
                                                    <li><a href="#" class="margin-impact-product" data-product="{{ $product_name }}">{{ $product_name }}</a></li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <div class="box-body">
                                    @if(isset($data['margin_impact']))
                                        <div class="row">
                                            @foreach($data['margin_impact'] as $product_name => $margins)
                                                <div class="margin-impact-container" id="margin_impact_{{ str_replace(' ', '_', $product_name) }}" style="{{ $loop->first ? '' : 'display: none;' }}">
                                                    <div class="col-md-6">
                                                        <div id="margin_impact_chart_{{ str_replace(' ', '_', $product_name) }}" style="min-height: 250px;"></div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <table class="table table-bordered table-striped datatable">
                                                            <thead>
                                                                <tr>
                                                                    <th>{{ __('Month-Year') }}</th>
                                                                    <th>{{ __('Purchase Price') }}</th>
                                                                    <th>{{ __('Selling Price') }}</th>
                                                                    <th>{{ __('Margin') }}</th>
                                                                    <th>{{ __('Margin (%)') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($margins as $margin)
                                                                    <tr>
                                                                        <td>{{ $margin['month_year'] }}</td>
                                                                        <td class="display_currency">{{ $margin['purchase_price'] }}</td>
                                                                        <td class="display_currency">{{ $margin['selling_price'] }}</td>
                                                                        <td class="display_currency">{{ $margin['margin'] }}</td>
                                                                        <td>{{ number_format($margin['margin_percentage'], 2) }}%</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-center">{{ __('lang_v1.no_data_found') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <h3>
                                {{ __('Correlation between supplier price hikes and selling price adjustments') }}
                                <i class="fa fa-info-circle text-info cursor-pointer" 
                                   data-toggle="tooltip" 
                                   title="{{ __('This analysis measures how closely your selling price changes follow supplier price changes. A correlation coefficient close to 1 indicates you effectively pass cost increases to customers, while values close to 0 suggest you absorb supplier price changes.') }}"></i>
                            </h3>
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">{{ __('Price Correlation') }}</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div id="price_correlation_chart" style="min-height: 250px;"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-bordered table-striped datatable">
                                                <thead>
                                                    <tr>
                                                        <th>{{ is_array(__('Product')) ? 'Product' : __('Product') }}</th>
                                                        <th>{{ is_array(__('Correlation Coefficient')) ? 'Correlation Coefficient' : __('Correlation Coefficient') }}</th>
                                                        <th>{{ is_array(__('Interpretation')) ? 'Interpretation' : __('Interpretation') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(isset($data['price_correlation']))
                                                        @foreach($data['price_correlation'] as $correlation)
                                                            <tr>
                                                                <td>{{ $correlation['product_name'] }}</td>
                                                                <td>{{ number_format($correlation['correlation'], 2) }}</td>
                                                                <td>{{ $correlation['interpretation'] }}</td>
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

                        <div class="col-md-12">
                            <h3>
                                {{ __('Gross margin per product after factoring purchase costs') }}
                                <i class="fa fa-info-circle text-info cursor-pointer" 
                                   data-toggle="tooltip" 
                                   title="{{ __('This analysis calculates the actual profit margin for each product by comparing selling prices to purchase costs, helping you identify your most and least profitable products.') }}"></i>
                            </h3>
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">{{ __('Gross Margins') }}</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div id="gross_margins_chart" style="min-height: 250px;"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-bordered table-striped datatable">
                                                <thead>
                                                    <tr>
                                                        <th>{{ is_array(__('Product')) ? 'Product' : __('Product') }}</th>
                                                        <th>{{ is_array(__('Total Selling Price')) ? 'Total Selling Price' : __('Total Selling Price') }}</th>
                                                        <th>{{ is_array(__('Total Purchase Cost')) ? 'Total Purchase Cost' : __('Total Purchase Cost') }}</th>
                                                        <th>{{ is_array(__('Gross Margin')) ? 'Gross Margin' : __('Gross Margin') }}</th>
                                                        <th>{{ is_array(__('Margin (%)')) ? 'Margin (%)' : __('Margin (%)') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(isset($data['gross_margins']))
                                                        @foreach($data['gross_margins'] as $margin)
                                                            <tr>
                                                                <td>{{ $margin->product_name }}</td>
                                                                <td class="display_currency">{{ $margin->total_selling_price }}</td>
                                                                <td class="display_currency">{{ $margin->total_purchase_cost }}</td>
                                                                <td class="display_currency">{{ $margin->gross_margin }}</td>
                                                                <td>{{ number_format($margin->margin_percent, 2) }}%</td>
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

                <!-- Inventory & Stock Planning -->
                <div class="tab-pane" id="inventory_stock_planning">
                    <div class="row">
                        <div class="col-md-12">
                            <h3>
                                {{ __('Purchase vs. Sales Alignment: Are you overbuying stock?') }}
                                <i class="fa fa-info-circle text-info cursor-pointer" 
                                   data-toggle="tooltip" 
                                   title="{{ __('This analysis compares purchase quantities to sales quantities, helping you identify products where you may be buying more than you\'re selling. An alignment ratio close to 1 indicates balanced purchasing, while higher values suggest potential overbuying.') }}"></i>
                            </h3>
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">{{ __('Purchase vs. Sales Alignment') }}</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div id="purchase_sales_alignment_chart" style="min-height: 250px;"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-bordered table-striped datatable">
                                                <thead>
                                                    <tr>
                                                        <th>{{ is_array(__('Product')) ? 'Product' : __('Product') }}</th>
                                                        <th>{{ is_array(__('Total Purchased')) ? 'Total Purchased' : __('Total Purchased') }}</th>
                                                        <th>{{ is_array(__('Total Sold')) ? 'Total Sold' : __('Total Sold') }}</th>
                                                        <th>{{ is_array(__('Difference')) ? 'Difference' : __('Difference') }}</th>
                                                        <th>{{ is_array(__('Alignment Ratio')) ? 'Alignment Ratio' : __('Alignment Ratio') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(isset($data['purchase_vs_sales']))
                                                        @foreach($data['purchase_vs_sales'] as $item)
                                                            <tr>
                                                                <td>{{ $item['product_name'] }}</td>
                                                                <td>{{ $item['total_purchased'] }}</td>
                                                                <td>{{ $item['total_sold'] }}</td>
                                                                <td>{{ $item['difference'] }}</td>
                                                                <td>
                                                                    @if($item['alignment_ratio'] !== null)
                                                                        {{ number_format($item['alignment_ratio'], 2) }}
                                                                    @else
                                                                        -
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

                        <div class="col-md-12">
                            <h3>
                                {{ __('Stock turnover analysis (fast-moving vs. slow-moving products)') }}
                                <i class="fa fa-info-circle text-info cursor-pointer" 
                                   data-toggle="tooltip" 
                                   title="{{ __('This analysis measures how quickly you sell and replace inventory, helping you identify fast-moving products that may need more frequent restocking and slow-moving products that tie up capital. Higher turnover rates indicate faster-selling products.') }}"></i>
                            </h3>
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">{{ __('Stock Turnover') }}</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div id="stock_turnover_chart" style="min-height: 250px;"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-bordered table-striped datatable">
                                                <thead>
                                                    <tr>
                                                        <th>{{ is_array(__('Product')) ? 'Product' : __('Product') }}</th>
                                                        <th>{{ is_array(__('Average Inventory')) ? 'Average Inventory' : __('Average Inventory') }}</th>
                                                        <th>{{ is_array(__('Total Sales')) ? 'Total Sales' : __('Total Sales') }}</th>
                                                        <th>{{ is_array(__('Turnover Rate')) ? 'Turnover Rate' : __('Turnover Rate') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(isset($data['stock_turnover']))
                                                        @foreach($data['stock_turnover'] as $item)
                                                            <tr>
                                                                <td>{{ $item['product_name'] }}</td>
                                                                <td>{{ $item['average_inventory'] }}</td>
                                                                <td>{{ $item['total_sales'] }}</td>
                                                                <td>{{ number_format($item['turnover_rate'], 2) }}</td>
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

                        <div class="col-md-12">
                            <h3>
                                {{ __('Safety stock optimization (based on purchase lead times)') }}
                                <i class="fa fa-info-circle text-info cursor-pointer" 
                                   data-toggle="tooltip" 
                                   title="{{ __('This analysis calculates the optimal safety stock levels based on supplier lead times and your sales rate, helping you maintain sufficient inventory to prevent stockouts while minimizing excess inventory costs. The formula used is: Safety Stock = (Max Lead Time - Avg Lead Time) × Daily Sales Rate.') }}"></i>
                            </h3>
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">{{ __('Safety Stock') }}</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div id="safety_stock_chart" style="min-height: 250px;"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-bordered table-striped datatable">
                                                <thead>
                                                    <tr>
                                                        <th>{{ is_array(__('Product')) ? 'Product' : __('Product') }}</th>
                                                        <th>{{ is_array(__('Average Lead Time (days)')) ? 'Average Lead Time (days)' : __('Average Lead Time (days)') }}</th>
                                                        <th>{{ is_array(__('Max Lead Time (days)')) ? 'Max Lead Time (days)' : __('Max Lead Time (days)') }}</th>
                                                        <th>{{ is_array(__('Daily Sales Rate')) ? 'Daily Sales Rate' : __('Daily Sales Rate') }}</th>
                                                        <th>{{ is_array(__('Recommended Safety Stock')) ? 'Recommended Safety Stock' : __('Recommended Safety Stock') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(isset($data['safety_stock']))
                                                        @foreach($data['safety_stock'] as $item)
                                                            <tr>
                                                                <td>{{ $item['product_name'] }}</td>
                                                                <td>{{ number_format($item['avg_lead_time'], 1) }}</td>
                                                                <td>{{ number_format($item['max_lead_time'], 1) }}</td>
                                                                <td>{{ number_format($item['daily_sales_rate'], 2) }}</td>
                                                                <td>{{ number_format($item['recommended_safety_stock'], 2) }}</td>
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

                <!-- Payment & Credit Terms -->
                <div class="tab-pane" id="payment_credit_terms">
                    <div class="row">
                        <div class="col-md-12">
                            <h3>
                                {{ __('Cash vs. credit purchases split') }}
                                <i class="fa fa-info-circle text-info cursor-pointer" 
                                   data-toggle="tooltip" 
                                   title="{{ __('This analysis shows the distribution of your purchases across different payment methods, helping you understand your cash flow patterns and credit utilization.') }}"></i>
                            </h3>
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">{{ __('Payment Methods') }}</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div id="payment_methods_chart" style="min-height: 250px;"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-bordered table-striped datatable">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Payment Method') }}</th>
                                                        <th>{{ __('Total Amount') }}</th>
                                                        <th>{{ __('Count') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(isset($data['payment_methods']))
                                                        @foreach($data['payment_methods'] as $method)
                                                            <tr>
                                                                <td>{{ $method->method }}</td>
                                                                <td class="display_currency">{{ $method->total_amount }}</td>
                                                                <td>{{ $method->count }}</td>
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

                        <div class="col-md-12">
                            <h3>
                                {{ __('Average payment cycle (Days Payable Outstanding)') }}
                                <i class="fa fa-info-circle text-info cursor-pointer" 
                                   data-toggle="tooltip" 
                                   title="{{ __('This metric measures how long it takes you, on average, to pay your suppliers after receiving goods or services. A longer DPO can improve your cash flow but may strain supplier relationships.') }}"></i>
                            </h3>
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">{{ __('Days Payable Outstanding') }}</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div id="dpo_chart" style="min-height: 250px;"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box bg-aqua">
                                                <span class="info-box-icon"><i class="fa fa-calendar"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">{{ __('Average days to pay') }}</span>
                                                    @if(isset($data['dpo']))
                                                        <span class="info-box-number">{{ number_format($data['dpo']->avg_days_to_pay, 1) }} {{ __('days') }}</span>
                                                    @else
                                                        <span class="info-box-number">{{ __('lang_v1.no_data_found') }}</span>
                                                    @endif
                                                    <div class="progress">
                                                        <div class="progress-bar" style="width: 100%"></div>
                                                    </div>
                                                    <span class="progress-description">
                                                        {{ __('Time between receiving goods and paying suppliers') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <h3>
                                {{ __('Supplier-wise credit terms → who gives better payment flexibility') }}
                                <i class="fa fa-info-circle text-info cursor-pointer" 
                                   data-toggle="tooltip" 
                                   title="{{ __('This analysis compares credit terms offered by different suppliers, helping you identify which suppliers offer the most favorable payment conditions and potentially negotiate better terms with others.') }}"></i>
                            </h3>
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">{{ __('Supplier Credit Terms') }}</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div id="supplier_credit_chart" style="min-height: 250px;"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-bordered table-striped datatable">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Supplier') }}</th>
                                                        <th>{{ __('Average Credit Days') }}</th>
                                                        <th>{{ __('Total Purchase') }}</th>
                                                        <th>{{ __('Transaction Count') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(isset($data['supplier_credit']))
                                                        @foreach($data['supplier_credit'] as $supplier)
                                                            <tr>
                                                                <td>{{ $supplier->supplier_name }}</td>
                                                                <td>{{ number_format($supplier->avg_credit_days, 1) }}</td>
                                                                <td class="display_currency">{{ $supplier->total_purchase }}</td>
                                                                <td>{{ $supplier->transaction_count }}</td>
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

                <!-- Predictive Analytics -->
                <div class="tab-pane" id="predictive_analytics">
                    <div class="row">
                        <div class="col-md-12">
                            <h3>
                                {{ __('Forecast future purchases based on sales demand and lead times') }}
                                <i class="fa fa-info-circle text-info cursor-pointer" 
                                   data-toggle="tooltip" 
                                   title="{{ __('This analysis predicts future purchase requirements based on historical sales patterns and supplier lead times, helping you plan inventory replenishment proactively. The forecast uses time series analysis with seasonal adjustments.') }}"></i>
                            </h3>
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">{{ __('Purchase Forecast') }}</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div id="purchase_forecast_chart" style="min-height: 250px;"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-bordered table-striped datatable">
                                                <thead>
                                                    <tr>
                                                        <th>{{ is_array(__('Product')) ? 'Product' : __('Product') }}</th>
                                                        <th>{{ is_array(__('Forecast Quantity')) ? 'Forecast Quantity' : __('Forecast Quantity') }}</th>
                                                        <th>{{ is_array(__('Average Lead Time (days)')) ? 'Average Lead Time (days)' : __('Average Lead Time (days)') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(isset($data['purchase_forecast']))
                                                        @foreach($data['purchase_forecast'] as $forecast)
                                                            <tr>
                                                                <td>{{ $forecast['product_name'] }}</td>
                                                                <td>{{ number_format($forecast['forecast_quantity'], 2) }}</td>
                                                                <td>{{ number_format($forecast['avg_lead_time'], 1) }}</td>
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

                        <div class="col-md-12">
                            <h3>
                                {{ __('Optimal reorder point prediction for each product') }}
                                <i class="fa fa-info-circle text-info cursor-pointer" 
                                   data-toggle="tooltip" 
                                   title="{{ __('This analysis calculates the optimal inventory level at which you should place a new order, based on lead time, daily usage, and safety stock. The formula used is: Reorder Point = (Lead Time × Daily Usage) + Safety Stock.') }}"></i>
                            </h3>
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">{{ __('Reorder Points') }}</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div id="reorder_points_chart" style="min-height: 250px;"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-bordered table-striped datatable">
                                                <thead>
                                                    <tr>
                                                        <th>{{ is_array(__('Product')) ? 'Product' : __('Product') }}</th>
                                                        <th>{{ is_array(__('Safety Stock')) ? 'Safety Stock' : __('Safety Stock') }}</th>
                                                        <th>{{ is_array(__('Lead Time (days)')) ? 'Lead Time (days)' : __('Lead Time (days)') }}</th>
                                                        <th>{{ is_array(__('Daily Usage')) ? 'Daily Usage' : __('Daily Usage') }}</th>
                                                        <th>{{ is_array(__('Reorder Point')) ? 'Reorder Point' : __('Reorder Point') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(isset($data['reorder_points']))
                                                        @foreach($data['reorder_points'] as $point)
                                                            <tr>
                                                                <td>{{ $point['product_name'] }}</td>
                                                                <td>{{ number_format($point['safety_stock'], 2) }}</td>
                                                                <td>{{ number_format($point['lead_time'], 1) }}</td>
                                                                <td>{{ number_format($point['daily_usage'], 2) }}</td>
                                                                <td>{{ number_format($point['reorder_point'], 2) }}</td>
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

                        <div class="col-md-12">
                            <h3>
                                {{ __('Price forecasting (if suppliers have historical fluctuations)') }}
                                <i class="fa fa-info-circle text-info cursor-pointer" 
                                   data-toggle="tooltip" 
                                   title="{{ __('This analysis predicts future product prices based on historical price trends, helping you anticipate cost changes and plan budgets accordingly. The forecast uses regression analysis with trend identification.') }}"></i>
                            </h3>
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">{{ __('Price Forecasts') }}</h3>
                                    @if(isset($data['price_forecasts']) && count($data['price_forecasts']) > 0)
                                    <div class="box-tools pull-right">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                <span id="price_forecast_product">{{ array_keys($data['price_forecasts'])[0] }}</span> <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">
                                                @foreach($data['price_forecasts'] as $product_name => $forecast)
                                                    <li><a href="#" class="price-forecast-product" data-product="{{ $product_name }}">{{ $product_name }}</a></li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <div class="box-body">
                                    @if(isset($data['price_forecasts']))
                                        <div class="row">
                                            @foreach($data['price_forecasts'] as $product_name => $forecast)
                                                <div class="price-forecast-container" id="price_forecast_{{ str_replace(' ', '_', $product_name) }}" style="{{ $loop->first ? '' : 'display: none;' }}">
                                                    <div class="col-md-6">
                                                        <div id="price_forecast_chart_{{ str_replace(' ', '_', $product_name) }}" style="min-height: 250px;"></div>
                                                        <div class="text-center">
                                                            <span class="label {{ $forecast['trend'] == 'Upward' ? 'label-danger' : ($forecast['trend'] == 'Downward' ? 'label-success' : 'label-warning') }}">
                                                                {{ __('Trend') }}: {{ $forecast['trend'] }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <table class="table table-bordered table-striped datatable">
                                                            <thead>
                                                                <tr>
                                                                    <th>{{ __('Period') }}</th>
                                                                    <th>{{ __('Forecast Price') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($forecast['forecasts'] as $period)
                                                                    <tr>
                                                                        <td>{{ $period['period'] }}</td>
                                                                        <td class="display_currency">{{ $period['forecast_price'] }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-center">{{ __('lang_v1.no_data_found') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
