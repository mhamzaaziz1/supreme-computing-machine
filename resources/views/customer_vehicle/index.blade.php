@extends('layouts.app')
@section('title', __('lang_v1.vehicles'))

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black"> @lang('lang_v1.vehicles')
            <small class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold">@lang('lang_v1.manage_vehicles')</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('license_plate_filter', __('lang_v1.license_plate') . ':') !!}
                    {!! Form::text('license_plate_filter', null, ['class' => 'form-control', 'placeholder' => __('lang_v1.license_plate')]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('make_filter', __('lang_v1.vehicle_make') . ':') !!}
                    {!! Form::text('make_filter', null, ['class' => 'form-control', 'placeholder' => __('lang_v1.vehicle_make')]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('customer_filter', __('contact.customer') . ':') !!}
                    {!! Form::select('customer_filter', $customers ?? [], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('messages.all')]) !!}
                </div>
            </div>
        @endcomponent

        @component('components.widget', [
            'class' => 'box-primary',
            'title' => __('lang_v1.all_vehicles'),
        ])
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary" id="add_vehicle_btn">
                        <i class="fa fa-plus"></i> @lang('messages.add')
                    </button>
                </div>
            @endslot

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="vehicles_table">
                    <thead>
                        <tr>
                            <th>@lang('messages.action')</th>
                            <th>@lang('contact.customer')</th>
                            <th>@lang('lang_v1.vehicle_make')</th>
                            <th>@lang('lang_v1.vehicle_model')</th>
                            <th>@lang('lang_v1.vehicle_year')</th>
                            <th>@lang('lang_v1.license_plate')</th>
                            <th>@lang('lang_v1.vehicle_color')</th>
                            <th>@lang('lang_v1.vehicle_vin')</th>
                            <th>@lang('lang_v1.vehicle_notes')</th>
                            <th>@lang('lang_v1.added_on')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcomponent

        <div class="modal fade vehicle_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>
        <div class="modal fade mileage_history_modal" tabindex="-1" role="dialog" aria-labelledby="mileageHistoryModalLabel">
        </div>
    </section>
    <!-- /.content -->
@stop
@section('javascript')
    @include('customer_vehicle.vehicles_table_javascript')
@endsection
