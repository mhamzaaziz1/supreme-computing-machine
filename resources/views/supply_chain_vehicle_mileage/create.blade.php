<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action([\App\Http\Controllers\SupplyChainVehicleMileageController::class, 'store']), 'method' => 'post', 'id' => 'mileage_add_form', 'files' => true ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('lang_v1.add_mileage_record')</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <input type="hidden" name="supply_chain_vehicle_id" value="{{ $vehicle->id }}">
        
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('date', __('lang_v1.date') . ':*') !!}
            {!! Form::date('date', date('Y-m-d'), ['class' => 'form-control', 'required', 'placeholder' => __('lang_v1.date')]); !!}
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('start_mileage', __('lang_v1.start_mileage') . ':*') !!}
            {!! Form::number('start_mileage', $last_mileage, ['class' => 'form-control', 'required', 'placeholder' => __('lang_v1.start_mileage'), 'min' => 0]); !!}
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('end_mileage', __('lang_v1.end_mileage') . ':*') !!}
            {!! Form::number('end_mileage', null, ['class' => 'form-control', 'required', 'placeholder' => __('lang_v1.end_mileage'), 'min' => 0]); !!}
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('travel_distance', __('lang_v1.travel_distance') . ':') !!}
            <p class="form-control-static" id="travel_distance">0</p>
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('start_picture', __('lang_v1.start_picture') . ':') !!}
            {!! Form::file('start_picture', ['class' => 'form-control', 'accept' => 'image/*']); !!}
            <p class="help-block">@lang('lang_v1.start_picture_help')</p>
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('end_picture', __('lang_v1.end_picture') . ':') !!}
            {!! Form::file('end_picture', ['class' => 'form-control', 'accept' => 'image/*']); !!}
            <p class="help-block">@lang('lang_v1.end_picture_help')</p>
          </div>
        </div>
        
        <div class="col-md-12">
          <div class="form-group">
            {!! Form::label('notes', __('lang_v1.notes') . ':') !!}
            {!! Form::textarea('notes', null, ['class' => 'form-control', 'placeholder' => __('lang_v1.notes'), 'rows' => 3]); !!}
          </div>
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script type="text/javascript">
  $(document).ready(function() {
    // Calculate travel distance when start or end mileage changes
    $('#start_mileage, #end_mileage').on('input', function() {
      var start = parseInt($('#start_mileage').val()) || 0;
      var end = parseInt($('#end_mileage').val()) || 0;
      var distance = end - start;
      
      // Display the calculated distance
      $('#travel_distance').text(distance >= 0 ? distance : 0);
      
      // Validate that end mileage is greater than or equal to start mileage
      if (distance < 0) {
        $('#end_mileage').addClass('has-error');
      } else {
        $('#end_mileage').removeClass('has-error');
      }
    });
    
    // Form validation
    $('#mileage_add_form').submit(function(e) {
      var start = parseInt($('#start_mileage').val()) || 0;
      var end = parseInt($('#end_mileage').val()) || 0;
      
      if (end < start) {
        e.preventDefault();
        toastr.error('@lang("lang_v1.end_mileage_less_than_start")');
        return false;
      }
      
      return true;
    });
  });
</script>