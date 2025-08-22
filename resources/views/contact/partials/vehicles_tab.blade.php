<div class="row">
    <div class="col-md-12">
        @component('components.widget')
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('vehicle_list_filter_license_plate', __('lang_v1.license_plate') . ':') !!}
                        {!! Form::text('vehicle_list_filter_license_plate', null, ['class' => 'form-control', 'placeholder' => __('lang_v1.license_plate')]); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('vehicle_list_filter_make', __('lang_v1.vehicle_make') . ':') !!}
                        {!! Form::text('vehicle_list_filter_make', null, ['class' => 'form-control', 'placeholder' => __('lang_v1.vehicle_make')]); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <button type="button" class="btn btn-primary btn-modal pull-right" 
                            data-href="{{action([\App\Http\Controllers\CustomerVehicleController::class, 'create'], [$contact->id])}}" 
                            data-container=".vehicle_modal" style="margin-top: 23px;">
                            <i class="fa fa-plus"></i> @lang('lang_v1.add_vehicle')
                        </button>
                    </div>
                </div>
            </div>
        @endcomponent
    </div>
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="vehicles_table" width="100%">
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
    </div>
</div>
<div class="modal fade vehicle_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade mileage_history_modal" tabindex="-1" role="dialog" aria-labelledby="mileageHistoryModalLabel">
</div>
