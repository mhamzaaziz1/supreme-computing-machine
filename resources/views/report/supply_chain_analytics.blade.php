@extends('layouts.app')
@section('title', __('Supply Chain Analytics'))

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Supply Chain Analytics</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="print_section">
            <h2>{{ session()->get('business.name') }} - Supply Chain Analytics</h2>
        </div>

        <div class="row no-print">
            <div class="col-md-12">
                <form id="supply_chain_analytics_form">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="location_id">{{ __('purchase.business_location') }}:</label>
                            <select class="form-control select2" name="location_id" id="location_id">
                                @foreach($business_locations as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="supplier_ids">{{ __('purchase.supplier') }}:</label>
                            <select class="form-control select2" name="supplier_ids[]" id="supplier_ids" multiple>
                                @foreach($suppliers as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="customer_ids">{{ __('report.customer') }}:</label>
                            <select class="form-control select2" name="customer_ids[]" id="customer_ids" multiple>
                                @foreach($customers as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="product_ids">{{ __('product.products') }}:</label>
                            <select class="form-control select2" name="product_ids[]" id="product_ids" multiple>
                                @foreach($products as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group pull-right">
                            <div class="input-group">
                                <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm" id="supply_chain_date_filter">
                                    <span>
                                        <i class="fa fa-calendar"></i> {{ __('messages.filter_by_date') }}
                                    </span>
                                    <i class="fa fa-caret-down"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary pull-right">{{ __('report.apply_filters') }}</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div id="supply_chain_analytics_data_div">
                @include('report.partials.supply_chain_analytics_details', ['data' => $data ?? []])
            </div>
        </div>

        <div class="row no-print">
            <div class="col-sm-12 tw-mb-2">
                <button class="tw-dw-btn tw-dw-btn-primary tw-text-white pull-right" aria-label="Print" onclick="window.print();">
                    <i class="fa fa-print"></i> @lang('messages.print')
                </button>
            </div>
        </div>
    </section>
    <!-- /.content -->
@stop

@section('javascript')
<script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // Initialize date range picker
        if ($('#supply_chain_date_filter').length == 1) {
            $('#supply_chain_date_filter').daterangepicker(dateRangeSettings, function(start, end) {
                $('#supply_chain_date_filter span').html(
                    start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
                );
                updateSupplyChainAnalytics();
            });
            $('#supply_chain_date_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('#supply_chain_date_filter').html(
                    '<i class="fa fa-calendar"></i> ' + LANG.filter_by_date
                );
            });
            updateSupplyChainAnalytics();
        }

        // Initialize select2 elements
        $('.select2').select2();

        // On form submit
        $('#supply_chain_analytics_form').on('submit', function(e) {
            e.preventDefault();
            updateSupplyChainAnalytics();
        });

        // On location, supplier, customer, or product change
        $('#location_id, #supplier_ids, #customer_ids, #product_ids').change(function() {
            updateSupplyChainAnalytics();
        });
    });

    function updateSupplyChainAnalytics() {
        var start = $('#supply_chain_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
        var end = $('#supply_chain_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
        var location_id = $('#location_id').val();
        var supplier_ids = $('#supplier_ids').val();
        var customer_ids = $('#customer_ids').val();
        var product_ids = $('#product_ids').val();

        get_supply_chain_analytics_data(start, end, location_id, supplier_ids, customer_ids, product_ids);
    }

    function get_supply_chain_analytics_data(start_date, end_date, location_id, supplier_ids, customer_ids, product_ids) {
        var loader = '<div class="text-center"><i class="fa fa-refresh fa-spin fa-fw"></i></div>';
        $('#supply_chain_analytics_data_div').html(loader);

        $.ajax({
            url: "{{ action([\App\Http\Controllers\SupplyChainAnalyticsController::class, 'index']) }}",
            data: {
                start_date: start_date,
                end_date: end_date,
                location_id: location_id,
                supplier_ids: supplier_ids,
                customer_ids: customer_ids,
                product_ids: product_ids
            },
            dataType: 'html',
            success: function(result) {
                $('#supply_chain_analytics_data_div').html(result);
                __currency_convert_recursively($('#supply_chain_analytics_data_div'));
            }
        });
    }
</script>
@endsection
