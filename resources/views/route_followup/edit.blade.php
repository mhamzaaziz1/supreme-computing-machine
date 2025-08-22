<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\App\Http\Controllers\RouteFollowupController@update', [$followup->id]), 'method' => 'put', 'id' => 'route_followup_edit_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">Edit Route Followup</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('customer_route_id', 'Route:*') !!}
            {!! Form::select('customer_route_id', $customer_routes, $followup->customer_route_id, ['class' => 'form-control select2', 'placeholder' => 'Select Route', 'required', 'id' => 'edit_route_id']); !!}
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('contact_id', 'Customer:*') !!}
            {!! Form::select('contact_id', $contacts, $followup->contact_id, ['class' => 'form-control select2', 'placeholder' => 'Select Customer', 'required', 'id' => 'edit_contact_id']); !!}
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('followup_date', 'Date:*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!! Form::text('followup_date', \Carbon\Carbon::parse($followup->followup_date)->format('Y-m-d'), ['class' => 'form-control', 'required', 'readonly', 'id' => 'edit_followup_date']); !!}
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            {!! Form::label('notes', 'Notes:*') !!}
            {!! Form::textarea('notes', $followup->notes, ['class' => 'form-control', 'rows' => 3, 'required']); !!}
          </div>
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      <button type="submit" class="btn btn-primary">Update</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script type="text/javascript">
  $(document).ready(function() {
    // Initialize date picker with the business date format
    $('#edit_followup_date').datepicker({
      autoclose: true,
      format: datepicker_date_format
    });

    // Load contacts for selected route
    $(document).on('change', '#edit_route_id', function() {
      var route_id = $(this).val();
      if (route_id) {
        $.ajax({
          method: 'GET',
          url: '/get-route-contacts',
          data: { route_id: route_id },
          dataType: 'json',
          success: function(result) {
            $('#edit_contact_id').empty().append('<option value="">Select Customer</option>');
            if (result.data.length) {
              result.data.forEach(function(contact) {
                var displayText = contact.name;
                if (contact.supplier_business_name) {
                  displayText = contact.supplier_business_name + ' - ' + contact.name;
                }
                $('#edit_contact_id').append('<option value="' + contact.id + '">' + displayText + '</option>');
              });
            }
          },
        });
      } else {
        $('#edit_contact_id').empty().append('<option value="">Select Customer</option>');
      }
    });

    // Initialize select2
    $('.select2').select2();
  });
</script>
