@extends('layouts.app')
@section('title', __('Business Advance Analytics'))

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Business Advance Analytics</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="print_section">
            <h2>{{ session()->get('business.name') }} - Business Advance Analytics</h2>
        </div>

        <div class="row no-print">
            <div class="col-md-3 col-md-offset-7 col-xs-6">
                <div class="input-group">
                    <span class="input-group-addon bg-light-blue"><i class="fa fa-map-marker"></i></span>
                    <select class="form-control select2" id="business_analytics_location_filter">
                        @foreach($business_locations as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2 col-xs-6">
                <div class="form-group pull-right">
                    <div class="input-group">
                        <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm" id="business_analytics_date_filter">
                            <span>
                                <i class="fa fa-calendar"></i> {{ __('messages.filter_by_date') }}
                            </span>
                            <i class="fa fa-caret-down"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div id="business_analytics_data_div">
                @include('report.partials.business_advance_analytics_details', ['data' => $data ?? []])
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
    if ($('#business_analytics_date_filter').length == 1) {
        $('#business_analytics_date_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#business_analytics_date_filter span').html(
                start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            );
            get_business_analytics_data();
        });
        $('#business_analytics_date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#business_analytics_date_filter').html(
                '<i class="fa fa-calendar"></i> ' + LANG.filter_by_date
            );
        });
        $('#business_analytics_date_filter').data('daterangepicker').setStartDate(moment().startOf('month'));
        $('#business_analytics_date_filter').data('daterangepicker').setEndDate(moment().endOf('month'));
    }

    // Initialize select2 elements
    $('.select2').select2();

    // Load data on page load
    get_business_analytics_data();

    // Reload data when location changes
    $('#business_analytics_location_filter').change(function() {
        get_business_analytics_data();
    });
});

function get_business_analytics_data() {
    var loader = '<div class="text-center"><i class="fa fa-refresh fa-spin fa-fw"></i></div>';
    $('#business_analytics_data_div').html(loader);

    var start_date = $('#business_analytics_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
    var end_date = $('#business_analytics_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
    var location_id = $('#business_analytics_location_filter').val();

    $.ajax({
        url: "{{ action([\App\Http\Controllers\ReportController::class, 'getBusinessAdvanceAnalytics']) }}",
        data: {
            start_date: start_date,
            end_date: end_date,
            location_id: location_id
        },
        dataType: 'html',
        success: function(result) {
            $('#business_analytics_data_div').html(result);
            __currency_convert_recursively($('#business_analytics_data_div'));
        }
    });
}
</script>
@endsection