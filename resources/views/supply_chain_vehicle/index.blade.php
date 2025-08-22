@extends('layouts.app')
@section('title', __('lang_v1.supply_chain_vehicles'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('lang_v1.supply_chain_vehicles')</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.all_supply_chain_vehicles')])
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary" id="add_vehicle_btn">
                        <i class="fa fa-plus"></i> @lang('messages.add')
                    </button>
                </div>
            @endslot
            @can('customer.view')
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('license_plate', __('lang_v1.license_plate') . ':') !!}
                            {!! Form::text('license_plate', null, ['class' => 'form-control', 'placeholder' => __('lang_v1.license_plate')]); !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('make', __('lang_v1.vehicle_make') . ':') !!}
                            {!! Form::text('make', null, ['class' => 'form-control', 'placeholder' => __('lang_v1.vehicle_make')]); !!}
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="supply_chain_vehicles_table">
                        <thead>
                            <tr>
                                <th>@lang('lang_v1.vehicle_make')</th>
                                <th>@lang('lang_v1.vehicle_model')</th>
                                <th>@lang('lang_v1.vehicle_year')</th>
                                <th>@lang('lang_v1.license_plate')</th>
                                <th>@lang('lang_v1.vehicle_color')</th>
                                <th>@lang('lang_v1.vehicle_vin')</th>
                                <th>@lang('lang_v1.vehicle_notes')</th>
                                <th>@lang('messages.action')</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="bg-gray font-17 footer-total text-center">
                                <td colspan="2"><strong>@lang('sale.total'):</strong></td>
                                <td class="footer_vehicle_count"></td>
                                <td colspan="5"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endcan
        @endcomponent

        <div class="modal fade vehicle_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>
        <div class="modal fade mileage_history_modal" tabindex="-1" role="dialog" aria-labelledby="mileageHistoryModalLabel">
        </div>
    </section>
    <!-- /.content -->
@stop

@section('javascript')
    @include('supply_chain_vehicle.supply_chain_vehicles_table_javascript')
@endsection
