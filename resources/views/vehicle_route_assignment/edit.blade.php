<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action([\App\Http\Controllers\VehicleRouteAssignmentController::class, 'update'], [$vehicle->id]), 'method' => 'put', 'id' => 'vehicle_route_assignment_edit_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('lang_v1.edit_route_assignment')</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            {!! Form::label('vehicle_display', __('lang_v1.vehicle') . ':') !!}
            <p class="form-control-static">{{ $vehicle_display }}</p>
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group">
            {!! Form::label('customer_route_id', __('lang_v1.route') . ':*') !!}
            {!! Form::select('customer_route_id', $customer_routes, $vehicle->customer_route_id, ['class' => 'form-control select2', 'required', 'placeholder' => __('messages.please_select')]); !!}
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group">
            {!! Form::label('route_assigned_at', __('lang_v1.date') . ':') !!}
            {!! Form::date('route_assigned_at', $vehicle->route_assigned_at ?? date('Y-m-d'), ['class' => 'form-control', 'placeholder' => __('lang_v1.date')]); !!}
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('start_mileage', __('lang_v1.start_mileage') . ':*') !!}
            {!! Form::number('start_mileage', $mileage_record->start_mileage ?? 0, ['class' => 'form-control', 'required', 'placeholder' => __('lang_v1.start_mileage'), 'min' => 0, 'id' => 'start_mileage']); !!}
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('end_mileage', __('lang_v1.end_mileage') . ':*') !!}
            {!! Form::number('end_mileage', $mileage_record->end_mileage ?? 0, ['class' => 'form-control', 'required', 'placeholder' => __('lang_v1.end_mileage'), 'min' => 0, 'id' => 'end_mileage']); !!}
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group">
            {!! Form::label('travel_distance', __('lang_v1.travel_distance') . ':') !!}
            <p class="form-control-static" id="travel_distance">{{ $mileage_record ? $mileage_record->end_mileage - $mileage_record->start_mileage : 0 }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
      <button type="submit" class="btn btn-primary">@lang('messages.update')</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script type="text/javascript">
  $(document).ready(function() {
    // Initialize select2
    $('.select2').select2();

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

    // Form validation and submission
    $('form#vehicle_route_assignment_edit_form').validate({
      rules: {
        customer_route_id: {
          required: true
        },
        route_assigned_at: {
          date: true
        },
        start_mileage: {
          required: true,
          min: 0
        },
        end_mileage: {
          required: true,
          min: 0
        }
      },
      messages: {
        customer_route_id: {
          required: LANG.required
        },
        route_assigned_at: {
          date: LANG.invalid_date
        },
        start_mileage: {
          required: LANG.required,
          min: LANG.min_value
        },
        end_mileage: {
          required: LANG.required,
          min: LANG.min_value
        }
      },
      submitHandler: function(form) {
        $(form).find('button[type="submit"]').prop('disabled', true);
        var data = $(form).serialize();

        $.ajax({
          method: $(form).attr('method'),
          url: $(form).attr('action'),
          dataType: 'json',
          data: data,
          success: function(result) {
            if (result.success == true) {
              $('div.vehicle_route_modal').modal('hide');
              toastr.success(result.msg);
              vehicle_route_assignments_table.ajax.reload();
            } else {
              toastr.error(result.msg);
            }
            $(form).find('button[type="submit"]').prop('disabled', false);
          },
          error: function(xhr, status, error) {
            toastr.error(LANG.something_went_wrong);
            $(form).find('button[type="submit"]').prop('disabled', false);
          }
        });
        return false;
      }
    });
  });
</script>
