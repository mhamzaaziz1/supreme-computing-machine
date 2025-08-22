<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('lang_v1.select_vehicle_for_expense')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('vehicle_id', __('lang_v1.vehicle') . ':*') !!}
                        {!! Form::select('vehicle_id', $vehicles, null, ['class' => 'form-control select2', 'required', 'style' => 'width:100%', 'placeholder' => __('messages.please_select'), 'id' => 'expense_vehicle_id']) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
            <button type="button" class="btn btn-primary" id="continue_btn" disabled>@lang('messages.continue')</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize select2
        $('.select2').select2();

        // Handle vehicle selection
        $('#expense_vehicle_id').change(function() {
            var vehicle_id = $(this).val();
            if (!vehicle_id) {
                $('#continue_btn').prop('disabled', true);
            } else {
                $('#continue_btn').prop('disabled', false);
            }
        });

        // Handle continue button click
        $('#continue_btn').click(function() {
            var vehicle_id = $('#expense_vehicle_id').val();
            if (vehicle_id) {
                // Redirect to the expense creation form with the selected vehicle ID
                window.location.href = "{{ action([\App\Http\Controllers\ExpenseController::class, 'create']) }}?supply_chain_vehicle_id=" + vehicle_id;

                // Close the modal
                $('div.expense_modal').modal('hide');
            }
        });
    });
</script>
