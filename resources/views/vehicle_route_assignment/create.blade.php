<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action([\App\Http\Controllers\VehicleRouteAssignmentController::class, 'store']), 'method' => 'post', 'id' => 'vehicle_route_assignment_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('lang_v1.assign_route_to_vehicle')</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            {!! Form::label('vehicle_id', __('lang_v1.vehicle') . ':*') !!}
            {!! Form::select('vehicle_id', $formatted_vehicles, null, ['class' => 'form-control select2', 'required', 'placeholder' => __('messages.please_select'), 'id' => 'vehicle_id']); !!}
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group">
            {!! Form::label('customer_route_id', __('lang_v1.route') . ':*') !!}
            {!! Form::select('customer_route_id', $customer_routes, null, ['class' => 'form-control select2', 'required', 'placeholder' => __('messages.please_select')]); !!}
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group">
            {!! Form::label('route_assigned_at', __('lang_v1.date') . ':') !!}
            {!! Form::date('route_assigned_at', date('Y-m-d'), ['class' => 'form-control', 'placeholder' => __('lang_v1.date')]); !!}
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('start_mileage', __('lang_v1.start_mileage') . ':') !!}
            {!! Form::number('start_mileage', 0, ['class' => 'form-control', 'placeholder' => __('lang_v1.start_mileage'), 'min' => 0, 'id' => 'start_mileage']); !!}
            <p class="help-block">@lang('lang_v1.can_be_added_later')</p>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('end_mileage', __('lang_v1.end_mileage') . ':') !!}
            {!! Form::number('end_mileage', 0, ['class' => 'form-control', 'placeholder' => __('lang_v1.end_mileage'), 'min' => 0, 'id' => 'end_mileage']); !!}
            <p class="help-block">@lang('lang_v1.can_be_added_later')</p>
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group">
            {!! Form::label('travel_distance', __('lang_v1.travel_distance') . ':') !!}
            <p class="form-control-static" id="travel_distance">0</p>
          </div>
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
      <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
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

    // Get last mileage record when vehicle is selected
    $('#vehicle_id').on('change', function() {
      var vehicle_id = $(this).val();
      if (vehicle_id) {
        $.ajax({
          url: '/supply-chain-vehicle-mileage/last-record/' + vehicle_id,
          type: 'GET',
          dataType: 'json',
          success: function(data) {
            if (data.last_mileage) {
              $('#start_mileage').val(data.last_mileage);
              // Trigger input event to recalculate travel distance
              $('#start_mileage').trigger('input');
            }
          }
        });
      }
    });

    // Form validation and submission
    $('form#vehicle_route_assignment_add_form').validate({
      rules: {
        vehicle_id: {
          required: true
        },
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
        vehicle_id: {
          required: LANG.required
        },
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
