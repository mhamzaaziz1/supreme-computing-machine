<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action([\App\Http\Controllers\CustomerVehicleController::class, 'update'], [$vehicle->id]), 'method' => 'put', 'id' => 'vehicle_edit_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('lang_v1.edit_vehicle')</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('make', __('lang_v1.vehicle_make') . ':*') !!}
            {!! Form::text('make', $vehicle->make, ['class' => 'form-control', 'required', 'placeholder' => __('lang_v1.vehicle_make')]); !!}
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('model', __('lang_v1.vehicle_model') . ':*') !!}
            {!! Form::text('model', $vehicle->model, ['class' => 'form-control', 'required', 'placeholder' => __('lang_v1.vehicle_model')]); !!}
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('year', __('lang_v1.vehicle_year') . ':') !!}
            {!! Form::text('year', $vehicle->year, ['class' => 'form-control', 'placeholder' => __('lang_v1.vehicle_year')]); !!}
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('license_plate', __('lang_v1.license_plate') . ':') !!}
            {!! Form::text('license_plate', $vehicle->license_plate, ['class' => 'form-control', 'placeholder' => __('lang_v1.license_plate')]); !!}
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('color', __('lang_v1.vehicle_color') . ':') !!}
            {!! Form::text('color', $vehicle->color, ['class' => 'form-control', 'placeholder' => __('lang_v1.vehicle_color')]); !!}
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('vin', __('lang_v1.vehicle_vin') . ':') !!}
            {!! Form::text('vin', $vehicle->vin, ['class' => 'form-control', 'placeholder' => __('lang_v1.vehicle_vin')]); !!}
          </div>
        </div>
        
        <div class="col-md-12">
          <div class="form-group">
            {!! Form::label('notes', __('lang_v1.vehicle_notes') . ':') !!}
            {!! Form::textarea('notes', $vehicle->notes, ['class' => 'form-control', 'placeholder' => __('lang_v1.vehicle_notes'), 'rows' => 3]); !!}
          </div>
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang('messages.update')</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->