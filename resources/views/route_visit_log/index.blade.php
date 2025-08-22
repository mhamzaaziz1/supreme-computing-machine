@extends('layouts.app')
@section('title', __('lang_v1.visit_logs'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('lang_v1.visit_logs')</h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.visit_logs')])
        @can('customer.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="visit_logs_table">
                    <thead>
                        <tr>
                            <th>@lang('lang_v1.seller')</th>
                            <th>@lang('lang_v1.route')</th>
                            <th>@lang('contact.contact')</th>
                            <th>@lang('lang_v1.visit_type')</th>
                            <th>@lang('lang_v1.visit_time')</th>
                            <th>@lang('business.location')</th>
                            <th>@lang('lang_v1.accuracy')</th>
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
        var visit_logs_table = $('#visit_logs_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{!! action([\App\Http\Controllers\RouteVisitLogController::class, "index"]) !!}',
            },
            columns: [
                { data: 'first_name', name: 'users.first_name' },
                { data: 'route_name', name: 'customer_routes.name' },
                { data: 'contact_name', name: 'contacts.name' },
                { data: 'visit_type', name: 'route_visit_logs.visit_type' },
                { data: 'visit_time', name: 'route_visit_logs.visit_time' },
                { 
                    data: null, 
                    render: function(data, type, row) {
                        return row.latitude + ', ' + row.longitude;
                    },
                    orderable: false
                },
                { data: 'accuracy', name: 'route_visit_logs.accuracy' },
                { data: 'created_at', name: 'route_visit_logs.created_at' },
            ],
        });
    });
</script>
@endsection