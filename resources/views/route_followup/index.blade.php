@extends('layouts.app')
@section('title', 'Route Followups')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Route Followups
        <small>Manage route followups</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => 'All Route Followups'])
        @can('customer.create')
            @slot('tool')
                <div class="box-tools">
                    <a class="btn btn-block btn-primary" 
                        href="{{action('\App\Http\Controllers\RouteFollowupController@create')}}">
                        <i class="fa fa-plus"></i> Add</a>
                </div>
            @endslot
        @endcan
        @can('customer.view')
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('filter_customer_route_id', __('lang_v1.route') . ':') !!}
                        {!! Form::select('filter_customer_route_id', $customer_routes, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('filter_date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filter_followups">&nbsp;</label>
                        <button type="button" class="btn btn-primary form-control" id="filter_followups">
                            <i class="fa fa-filter"></i> @lang('report.filter')
                        </button>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="route_followups_table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Route</th>
                            <th>Customer</th>
                            <th>Added By</th>
                            <th>Notes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcan
    @endcomponent

    <div class="modal fade" id="route_followup_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection

@section('javascript')
<script type="text/javascript">
$(document).ready(function() {
    // Initialize select2
    $('.select2').select2();

    // Initialize date range picker
    $('#filter_date_range').daterangepicker();

    // Route followups table
    var route_followups_table = $('#route_followups_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/route-followups',
            data: function(d) {
                d.customer_route_id = $('#filter_customer_route_id').val();

                if ($('#filter_date_range').val()) {
                    var start = $('#filter_date_range').data('daterangepicker').startDate.format(moment_date_format);
                    var end = $('#filter_date_range').data('daterangepicker').endDate.format(moment_date_format);
                    d.start_date = start;
                    d.end_date = end;
                }
            }
        },
        columns: [
            { data: 'followup_date', name: 'followup_date' },
            { data: 'customer_route_id', name: 'customer_route_id' },
            { data: 'contact_id', name: 'contact_id' },
            { data: 'user_id', name: 'user_id' },
            { data: 'notes', name: 'notes' },
            { data: 'action', name: 'action' }
        ]
    });

    // Apply filter
    $('#filter_followups').click(function() {
        route_followups_table.ajax.reload();
    });

    // Reset filter when route changes
    $('#filter_customer_route_id').change(function() {
        if ($(this).val() === '') {
            route_followups_table.ajax.reload();
        }
    });
});
</script>
@endsection
