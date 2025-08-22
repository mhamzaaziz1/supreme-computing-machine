@extends('layouts.app')
@section('title', __('lang_v1.vehicle_route_assignments'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('lang_v1.vehicle_route_assignments')
        <small>@lang('lang_v1.manage_vehicle_route_assignments')</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.all_vehicle_route_assignments')])
        @can('customer.create')
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary btn-modal" 
                        data-href="{{action([\App\Http\Controllers\VehicleRouteAssignmentController::class, 'create'])}}" 
                        data-container=".vehicle_route_modal">
                        <i class="fa fa-plus"></i> @lang('messages.add')</button>
                </div>
            @endslot
        @endcan
        @can('customer.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="vehicle_route_assignments_table">
                    <thead>
                        <tr>
                            <th>@lang('lang_v1.vehicle')</th>
                            <th>@lang('lang_v1.license_plate')</th>
                            <th>@lang('lang_v1.route')</th>
                            <th>@lang('lang_v1.date')</th>
                            <th>@lang('lang_v1.start_mileage')</th>
                            <th>@lang('lang_v1.end_mileage')</th>
                            <th>@lang('lang_v1.travel_distance')</th>
                            <th>@lang('messages.action')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcan
    @endcomponent

    <div class="modal fade vehicle_route_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection

@section('javascript')
    @include('vehicle_route_assignment.vehicle_route_assignments_table_javascript')
@endsection
