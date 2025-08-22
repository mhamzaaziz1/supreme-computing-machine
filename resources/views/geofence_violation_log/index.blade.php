@extends('layouts.app')
@section('title', __('lang_v1.violation_logs'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('lang_v1.violation_logs')</h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.violation_logs')])
        @can('customer.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="violation_logs_table">
                    <thead>
                        <tr>
                            <th>@lang('lang_v1.seller')</th>
                            <th>@lang('lang_v1.route')</th>
                            <th>@lang('contact.contact')</th>
                            <th>@lang('lang_v1.violation_type')</th>
                            <th>@lang('lang_v1.attempted_action')</th>
                            <th>@lang('business.location')</th>
                            <th>@lang('lang_v1.accuracy')</th>
                            <th>@lang('lang_v1.distance_from_valid')</th>
                            <th>@lang('lang_v1.mock_location')</th>
                            <th>@lang('lang_v1.details')</th>
                            <th>@lang('messages.date')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcan
    @endcomponent
</section>
<!-- /.content -->
@stop

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        // Initialize dataTable
        var violation_logs_table = $('#violation_logs_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{!! action([\App\Http\Controllers\GeofenceViolationLogController::class, "index"]) !!}'
            },
            columns: [
                { data: 'first_name', name: 'users.first_name' },
                { data: 'route_name', name: 'customer_routes.name' },
                { data: 'contact_name', name: 'contacts.name' },
                { data: 'violation_type', name: 'geofence_violation_logs.violation_type' },
                { data: 'attempted_action', name: 'geofence_violation_logs.attempted_action' },
                { 
                    data: null, 
                    render: function(data, type, row) {
                        return row.latitude + ', ' + row.longitude;
                    },
                    orderable: false
                },
                { data: 'accuracy', name: 'geofence_violation_logs.accuracy' },
                { data: 'distance_from_valid', name: 'geofence_violation_logs.distance_from_valid' },
                { data: 'is_mock_location', name: 'geofence_violation_logs.is_mock_location' },
                { data: 'details', name: 'geofence_violation_logs.details' },
                { data: 'created_at', name: 'geofence_violation_logs.created_at' }
            ]
        });
    });
</script>
@endsection