@extends('layouts.app')
@section('title', __('Customer Advance Analytics'))

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Customer Advance Analytics</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="print_section">
            <h2>{{ session()->get('business.name') }} - Customer Advance Analytics</h2>
        </div>

        <div class="row no-print">
            <div class="col-md-12">
                <form id="customer_analytics_form">
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
                            <label for="customer_ids">{{ __('report.customer') }}:</label>
                            <select class="form-control select2" name="customer_ids[]" id="customer_ids" multiple>
                                @foreach($customers as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group pull-right">
                            <div class="input-group">
                                <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm" id="customer_analytics_date_filter">
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
            <div id="customer_analytics_data_div">
                @include('report.partials.customer_advance_analytics_details', ['data' => $data ?? []])
            </div>
        </div>
    </section>
    <!-- /.content -->
@stop

@section('javascript')
<script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // Initialize date range picker
        if ($('#customer_analytics_date_filter').length == 1) {
            $('#customer_analytics_date_filter').daterangepicker(dateRangeSettings, function(start, end) {
                $('#customer_analytics_date_filter span').html(
                    start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
                );
                updateCustomerAnalytics();
            });
            $('#customer_analytics_date_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('#customer_analytics_date_filter').html(
                    '<i class="fa fa-calendar"></i> ' + LANG.filter_by_date
                );
            });

            // Set default date range
            var start = moment().startOf('month');
            var end = moment().endOf('month');
            $('#customer_analytics_date_filter').data('daterangepicker').setStartDate(start);
            $('#customer_analytics_date_filter').data('daterangepicker').setEndDate(end);
            $('#customer_analytics_date_filter span').html(
                start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            );

            updateCustomerAnalytics();
        }

        // Initialize select2 elements
        $('.select2').select2();

        // On form submit
        $('#customer_analytics_form').on('submit', function(e) {
            e.preventDefault();
            updateCustomerAnalytics();
        });

        // On location or customer change
        $('#location_id, #customer_ids').change(function() {
            updateCustomerAnalytics();
        });
    });

    function updateCustomerAnalytics() {
        var start = $('#customer_analytics_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
        var end = $('#customer_analytics_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
        var location_id = $('#location_id').val();
        var customer_ids = $('#customer_ids').val();

        get_customer_analytics_data(start, end, location_id, customer_ids);
    }

    function get_customer_analytics_data(start_date, end_date, location_id, customer_ids) {
        var loader = '<div class="text-center"><i class="fa fa-refresh fa-spin fa-fw"></i></div>';
        $('#customer_analytics_data_div').html(loader);

        $.ajax({
            url: "{{ action([\App\Http\Controllers\ReportController::class, 'getCustomerAdvanceAnalytics']) }}",
            data: {
                start_date: start_date,
                end_date: end_date,
                location_id: location_id,
                customer_ids: customer_ids
            },
            dataType: 'html',
            success: function(result) {
                $('#customer_analytics_data_div').html(result);
                __currency_convert_recursively($('#customer_analytics_data_div'));

                // Call the initChartToggle function to initialize chart toggle functionality
                if (typeof window.initChartToggle === 'function') {
                    window.initChartToggle();
                }
            }
        });
    }
</script>
@endsection
