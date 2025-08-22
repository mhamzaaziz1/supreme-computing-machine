<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action([\App\Http\Controllers\VehicleRouteAssignmentController::class, 'updateMileage'], [$vehicle->id]), 'method' => 'post', 'id' => 'update_mileage_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('lang_v1.update_mileage')</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            {!! Form::label('vehicle_info', __('lang_v1.vehicle') . ':') !!}
            <p class="form-control-static">{{ $vehicle_display }}</p>
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group">
            {!! Form::label('route_info', __('lang_v1.route') . ':') !!}
            <p class="form-control-static">{{ $vehicle->customerRoute->name ?? __('lang_v1.no_route_assigned') }}</p>
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group">
            {!! Form::label('assignment_date', __('lang_v1.date') . ':') !!}
            <p class="form-control-static">{{ \Carbon\Carbon::parse($vehicle->route_assigned_at)->format('m/d/Y') }}</p>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('start_mileage', __('lang_v1.start_mileage') . ':*') !!}
            @php
              $start_mileage_value = $mileage_record->start_mileage ?? 0;
              $start_mileage_disabled = $start_mileage_value > 0 ? true : false;
            @endphp
            {!! Form::number('start_mileage', $start_mileage_value, ['class' => 'form-control', 'required', 'placeholder' => __('lang_v1.start_mileage'), 'min' => 0, 'id' => 'start_mileage', 'disabled' => $start_mileage_disabled]); !!}
            @if($start_mileage_disabled)
              <p class="help-block text-danger">@lang('lang_v1.value_greater_than_zero_not_editable')</p>
              {!! Form::hidden('start_mileage', $start_mileage_value) !!}
            @endif
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('end_mileage', __('lang_v1.end_mileage') . ':*') !!}
            @php
              $end_mileage_value = $mileage_record->end_mileage ?? 0;
              $end_mileage_disabled = $end_mileage_value > 0 ? true : false;
            @endphp
            {!! Form::number('end_mileage', $end_mileage_value, ['class' => 'form-control', 'required', 'placeholder' => __('lang_v1.end_mileage'), 'min' => 0, 'id' => 'end_mileage', 'disabled' => $end_mileage_disabled]); !!}
            @if($end_mileage_disabled)
              <p class="help-block text-danger">@lang('lang_v1.value_greater_than_zero_not_editable')</p>
              {!! Form::hidden('end_mileage', $end_mileage_value) !!}
            @endif
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group">
            {!! Form::label('travel_distance', __('lang_v1.travel_distance') . ':') !!}
            <p class="form-control-static" id="travel_distance">{{ ($mileage_record->end_mileage ?? 0) - ($mileage_record->start_mileage ?? 0) }}</p>
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group">
            {!! Form::label('notes', __('lang_v1.notes') . ':') !!}
            {!! Form::textarea('notes', $mileage_record->notes ?? null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => __('lang_v1.notes')]); !!}
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
    $('form#update_mileage_form').validate({
      rules: {
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
