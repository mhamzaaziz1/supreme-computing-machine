<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('pa_location_id',  __('purchase.business_location') . ':') !!}
            {!! Form::select('pa_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="pa_date_filter">{{ __('report.date_range') }}:</label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input type="text" id="pa_date_filter" class="form-control" readonly>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs nav-justified">
                <li class="active">
                    <a href="#purchase_trends_tab" data-toggle="tab" aria-expanded="true"><i class="fas fa-chart-line" aria-hidden="true"></i> @lang('lang_v1.purchase_trends')</a>
                </li>
                <li>
                    <a href="#top_products_tab" data-toggle="tab" aria-expanded="true"><i class="fas fa-chart-bar" aria-hidden="true"></i> @lang('lang_v1.top_products')</a>
                </li>
                <li>
                    <a href="#payment_methods_tab" data-toggle="tab" aria-expanded="true"><i class="fas fa-money-bill-alt" aria-hidden="true"></i> @lang('lang_v1.payment_methods')</a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="purchase_trends_tab">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">@lang('lang_v1.monthly_purchases')</h3>
                                </div>
                                <div class="box-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="monthly_purchases_table">
                                            <thead>
                                                <tr>
                                                    <th>@lang('lang_v1.month')</th>
                                                    <th>@lang('lang_v1.year')</th>
                                                    <th>@lang('purchase.total_purchase')</th>
                                                    <th>@lang('lang_v1.transaction_count')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(isset($data['monthly_purchases']) && count($data['monthly_purchases']) > 0)
                                                    @foreach($data['monthly_purchases'] as $purchase)
                                                        <tr>
                                                            <td>{{ \Carbon\Carbon::createFromDate($purchase->year, $purchase->month, 1)->format('F') }}</td>
                                                            <td>{{ $purchase->year }}</td>
                                                            <td class="display_currency">{{ $purchase->total_purchase }}</td>
                                                            <td>{{ $purchase->transaction_count }}</td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="4" class="text-center">@lang('lang_v1.no_data_found')</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">@lang('lang_v1.quarterly_purchases')</h3>
                                </div>
                                <div class="box-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="quarterly_purchases_table">
                                            <thead>
                                                <tr>
                                                    <th>@lang('lang_v1.quarter')</th>
                                                    <th>@lang('lang_v1.year')</th>
                                                    <th>@lang('purchase.total_purchase')</th>
                                                    <th>@lang('lang_v1.transaction_count')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(isset($data['quarterly_purchases']) && count($data['quarterly_purchases']) > 0)
                                                    @foreach($data['quarterly_purchases'] as $purchase)
                                                        <tr>
                                                            <td>Q{{ $purchase->quarter }}</td>
                                                            <td>{{ $purchase->year }}</td>
                                                            <td class="display_currency">{{ $purchase->total_purchase }}</td>
                                                            <td>{{ $purchase->transaction_count }}</td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="4" class="text-center">@lang('lang_v1.no_data_found')</td>
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

                <div class="tab-pane" id="top_products_tab">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">@lang('lang_v1.top_purchased_products')</h3>
                                </div>
                                <div class="box-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="top_products_table">
                                            <thead>
                                                <tr>
                                                    <th>@lang('sale.product')</th>
                                                    <th>@lang('product.category')</th>
                                                    <th>@lang('lang_v1.total_quantity')</th>
                                                    <th>@lang('lang_v1.total_amount')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(isset($data['top_products']) && count($data['top_products']) > 0)
                                                    @foreach($data['top_products'] as $product)
                                                        <tr>
                                                            <td>{{ $product->product_name }}</td>
                                                            <td>{{ $product->category_name ?? 'N/A' }}</td>
                                                            <td>{{ @num_format($product->total_quantity) }}</td>
                                                            <td class="display_currency">{{ $product->total_amount }}</td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="4" class="text-center">@lang('lang_v1.no_data_found')</td>
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

                <div class="tab-pane" id="payment_methods_tab">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">@lang('lang_v1.payment_methods')</h3>
                                </div>
                                <div class="box-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="payment_methods_table">
                                            <thead>
                                                <tr>
                                                    <th>@lang('lang_v1.payment_method')</th>
                                                    <th>@lang('lang_v1.count')</th>
                                                    <th>@lang('lang_v1.total_amount')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(isset($data['payment_methods']) && count($data['payment_methods']) > 0)
                                                    @foreach($data['payment_methods'] as $payment)
                                                        <tr>
                                                            <td>{{ $payment->method }}</td>
                                                            <td>{{ $payment->count }}</td>
                                                            <td class="display_currency">{{ $payment->total_amount }}</td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="3" class="text-center">@lang('lang_v1.no_data_found')</td>
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

<script type="text/javascript">
    $(document).ready(function() {
        // Initialize date range picker
        $('#pa_date_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#pa_date_filter').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            updatePurchaseAnalytics();
        });
        $('#pa_date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#pa_date_filter').val('');
        });
        $('#pa_date_filter').data('daterangepicker').setStartDate(moment().startOf('month'));
        $('#pa_date_filter').data('daterangepicker').setEndDate(moment().endOf('month'));
        $('#pa_date_filter').val(moment().startOf('month').format(moment_date_format) + ' ~ ' + moment().endOf('month').format(moment_date_format));

        // Initialize select2 elements
        $('.select2').select2();

        // On location change
        $('#pa_location_id').change(function() {
            updatePurchaseAnalytics();
        });

        // Initialize DataTables
        $('#monthly_purchases_table, #quarterly_purchases_table, #top_products_table, #payment_methods_table').DataTable({
            "ordering": true,
            "searching": true,
            "paging": true,
            "info": true
        });

        // Convert currency
        __currency_convert_recursively($('#purchase_trends_tab, #top_products_tab, #payment_methods_tab'));
    });

    function updatePurchaseAnalytics() {
        var start = $('#pa_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
        var end = $('#pa_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
        var location_id = $('#pa_location_id').val();
        var supplier_id = '{{ $contact->id }}';

        var loader = '<div class="text-center"><i class="fa fa-refresh fa-spin fa-fw"></i></div>';
        $('#purchase_trends_tab, #top_products_tab, #payment_methods_tab').html(loader);

        $.ajax({
            url: "{{ action([\App\Http\Controllers\ReportController::class, 'getSupplierPurchaseAdvanceAnalytics'], [$contact->id]) }}",
            data: {
                start_date: start,
                end_date: end,
                location_id: location_id
            },
            dataType: 'html',
            success: function(result) {
                $('#purchase_advance_analytics_tab').html(result);
                __currency_convert_recursively($('#purchase_advance_analytics_tab'));
            }
        });
    }
</script>