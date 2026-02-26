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
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script type="text/javascript">
// Unified Analytics Chart
let unifiedAnalyticsChart;

function initUnifiedAnalyticsChart() {
    const ctx = document.getElementById('unified_analytics_chart').getContext('2d');

    // Initial empty data
    const data = {
        labels: [],
        datasets: [
            {
                label: 'Sales',
                data: [],
                borderColor: 'rgba(220, 53, 69, 1)',
                backgroundColor: 'rgba(220, 53, 69, 0.2)',
                borderWidth: 2,
                pointRadius: 3,
                fill: true
            },
            {
                label: 'Sales Forecast',
                data: [],
                borderColor: 'rgba(220, 53, 69, 0.7)',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                borderWidth: 2,
                borderDash: [5, 5],
                pointRadius: 2,
                fill: true
            },
            {
                label: 'Purchases',
                data: [],
                borderColor: 'rgba(210, 214, 222, 1)',
                backgroundColor: 'rgba(210, 214, 222, 0.2)',
                borderWidth: 2,
                pointRadius: 3,
                fill: true
            },
            {
                label: 'Profit',
                data: [],
                borderColor: 'rgba(0, 166, 90, 1)',
                backgroundColor: 'rgba(0, 166, 90, 0.2)',
                borderWidth: 2,
                pointRadius: 3,
                fill: true
            },
            {
                label: 'Profit Forecast',
                data: [],
                borderColor: 'rgba(0, 166, 90, 0.7)',
                backgroundColor: 'rgba(0, 166, 90, 0.1)',
                borderWidth: 2,
                borderDash: [5, 5],
                pointRadius: 2,
                fill: true
            }
        ]
    };

    unifiedAnalyticsChart = new Chart(ctx, {
        type: 'line',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            scales: {
                y: {
                    beginAtZero: false,
                    title: {
                        display: true,
                        text: 'Amount'
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
                        boxWidth: 12
                    }
                }
            }
        }
    });
}

function updateUnifiedChart(data) {
    if (!unifiedAnalyticsChart) return;

    const months = [];
    const salesData = [];
    const purchaseData = [];
    const profitData = [];
    const salesForecastData = [];
    const profitForecastData = [];

    // Use the sells_current_fy_labels and sells_current_fy_data from the response
    if (data.sells_current_fy_labels && data.sells_current_fy_data) {
        // Add the months
        months.push(...data.sells_current_fy_labels);

        // Add the sales data
        salesData.push(...data.sells_current_fy_data);

        // Add purchase data (if available, otherwise use placeholder)
        if (data.purchase_data) {
            purchaseData.push(...data.purchase_data);
        } else {
            // Generate some placeholder purchase data
            data.sells_current_fy_data.forEach(sale => {
                purchaseData.push(sale * 0.6); // Assume purchases are about 60% of sales
            });
        }

        // Calculate profit data (sales - purchases)
        for (let i = 0; i < salesData.length; i++) {
            profitData.push(salesData[i] - (purchaseData[i] || 0));
        }

        // Add forecast data if available
        if (data.sells_next_fy_prediction) {
            salesForecastData.push(...data.sells_next_fy_prediction);

            // Calculate profit forecast
            data.sells_next_fy_prediction.forEach(sale => {
                profitForecastData.push(sale * 0.4); // Assume profit is about 40% of sales
            });
        }
    }

    // Update chart data
    unifiedAnalyticsChart.data.labels = months;
    unifiedAnalyticsChart.data.datasets[0].data = salesData;
    unifiedAnalyticsChart.data.datasets[1].data = Array(salesData.length).fill(null).concat(salesForecastData);
    unifiedAnalyticsChart.data.datasets[2].data = purchaseData;
    unifiedAnalyticsChart.data.datasets[3].data = profitData;
    unifiedAnalyticsChart.data.datasets[4].data = Array(profitData.length).fill(null).concat(profitForecastData);

    unifiedAnalyticsChart.update();
}

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

            // Initialize DataTables after loading the content
            initializeDataTables();

            // Initialize Unified Analytics Chart if it exists
            if ($('#unified_analytics_chart').length > 0) {
                initUnifiedAnalyticsChart();

                // Make a separate AJAX call to get the data for the chart
                $.ajax({
                    url: "{{ action([\App\Http\Controllers\ReportController::class, 'getBusinessAdvanceAnalytics']) }}",
                    data: {
                        start_date: start_date,
                        end_date: end_date,
                        location_id: location_id,
                        dataType: 'json'
                    },
                    dataType: 'json',
                    success: function(chartData) {
                        updateUnifiedChart(chartData);
                    }
                });
            }
        }
    });
}

function initializeDataTables() {
    try {
        console.log('Initializing DataTables...');

        // Sales Payment Dues DataTable
        if ($('#sales_payment_dues_table').length) {
            console.log('Initializing sales_payment_dues_table...');
            if ($.fn.DataTable.isDataTable('#sales_payment_dues_table')) {
                $('#sales_payment_dues_table').DataTable().destroy();
            }

            sales_payment_dues_table = $('#sales_payment_dues_table').DataTable({
                processing: true,
                serverSide: true,
                ordering: false,
                searching: false,
                scrollY: "75vh",
                scrollX: true,
                scrollCollapse: true,
                fixedHeader: false,
                dom: 'Btirp',
                ajax: {
                    "url": '/home/sales-payment-dues',
                    "data": function(d) {
                        d.location_id = $('#business_analytics_location_filter').val();
                    }
                },
                fnDrawCallback: function(oSettings) {
                    __currency_convert_recursively($('#sales_payment_dues_table'));
                }
            });
        }

        // Purchase Payment Dues DataTable
        if ($('#purchase_payment_dues_table').length) {
            console.log('Initializing purchase_payment_dues_table...');
            if ($.fn.DataTable.isDataTable('#purchase_payment_dues_table')) {
                $('#purchase_payment_dues_table').DataTable().destroy();
            }

            purchase_payment_dues_table = $('#purchase_payment_dues_table').DataTable({
                processing: true,
                serverSide: true,
                ordering: false,
                searching: false,
                scrollY: "75vh",
                scrollX: true,
                scrollCollapse: true,
                fixedHeader: false,
                dom: 'Btirp',
                ajax: {
                    "url": '/home/purchase-payment-dues',
                    "data": function(d) {
                        d.location_id = $('#business_analytics_location_filter').val();
                    }
                },
                fnDrawCallback: function(oSettings) {
                    __currency_convert_recursively($('#purchase_payment_dues_table'));
                }
            });
        }

        // Stock Alert DataTable
        if ($('#stock_alert_table').length) {
            console.log('Initializing stock_alert_table...');
            if ($.fn.DataTable.isDataTable('#stock_alert_table')) {
                $('#stock_alert_table').DataTable().destroy();
            }

            stock_alert_table = $('#stock_alert_table').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                scrollY: "75vh",
                scrollX: true,
                scrollCollapse: true,
                fixedHeader: false,
                dom: 'Btirp',
                ajax: {
                    url: '/home/product-stock-alert',
                    data: function(d) {
                        d.location_id = $('#business_analytics_location_filter').val();
                    }
                },
                fnDrawCallback: function(oSettings) {
                    __currency_convert_recursively($('#stock_alert_table'));
                }
            });
        }

        console.log('DataTables initialization complete.');
    } catch (e) {
        console.error('Error in initializeDataTables:', e);
        alert('Error initializing DataTables. See console for details.');
    }
}
</script>
@endsection
