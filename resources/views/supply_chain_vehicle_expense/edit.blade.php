<div class="modal-dialog" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action([\App\Http\Controllers\SupplyChainVehicleExpenseController::class, 'update'], [$expense->id]), 'method' => 'put', 'id' => 'vehicle_expense_edit_form', 'files' => true]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('lang_v1.edit_vehicle_expense')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('date', __('lang_v1.date') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            {!! Form::text('date', $expense->date->format('Y-m-d'), ['class' => 'form-control', 'required', 'readonly', 'id' => 'expense_date_edit']) !!}
                        </div>
                    </div>
                    
                    <div class="form-group">
                        {!! Form::label('expense_type', __('lang_v1.expense_type') . ':*') !!}
                        {!! Form::select('expense_type', $expense_types, $expense->expense_type, ['class' => 'form-control select2', 'required', 'style' => 'width:100%']) !!}
                    </div>
                    
                    <div class="form-group">
                        {!! Form::label('amount', __('lang_v1.amount') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            {!! Form::text('amount', $expense->amount, ['class' => 'form-control input_number', 'required', 'placeholder' => __('lang_v1.amount')]) !!}
                        </div>
                    </div>
                    
                    <div class="form-group">
                        {!! Form::label('supply_chain_vehicle_mileage_id', __('lang_v1.mileage_record') . ':') !!}
                        {!! Form::select('supply_chain_vehicle_mileage_id', $mileage_records, $expense->supply_chain_vehicle_mileage_id, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.none'), 'style' => 'width:100%']) !!}
                    </div>
                    
                    <div class="form-group">
                        {!! Form::label('receipt_image', __('lang_v1.receipt_image') . ':') !!}
                        @if(!empty($expense->receipt_image))
                            <div class="existing-image">
                                <a href="{{ asset('storage/' . $expense->receipt_image) }}" class="view_image" data-href="{{ asset('storage/' . $expense->receipt_image) }}">
                                    <img src="{{ asset('storage/' . $expense->receipt_image) }}" alt="Receipt" class="img-thumbnail" style="max-height: 100px;">
                                </a>
                                <p><small>@lang('lang_v1.upload_new_image_to_replace')</small></p>
                            </div>
                        @endif
                        {!! Form::file('receipt_image', ['class' => 'form-control', 'accept' => 'image/*']) !!}
                        <p class="help-block">@lang('lang_v1.receipt_image_help')</p>
                    </div>
                    
                    <div class="form-group">
                        {!! Form::label('description', __('lang_v1.description') . ':') !!}
                        {!! Form::textarea('description', $expense->description, ['class' => 'form-control', 'rows' => 3, 'placeholder' => __('lang_v1.description')]) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
            <button type="submit" class="btn btn-primary">@lang('messages.update')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize date picker
        $('#expense_date_edit').datepicker({
            autoclose: true,
            format: datepicker_date_format
        });
        
        // Initialize select2
        $('.select2').select2();
        
        // Form validation and submission
        $('#vehicle_expense_edit_form').validate({
            submitHandler: function(form) {
                $(form).find('button[type="submit"]').prop('disabled', true);
                var data = new FormData(form);
                $.ajax({
                    method: $(form).attr('method'),
                    url: $(form).attr('action'),
                    data: data,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(result) {
                        if (result.success) {
                            $('div.expense_modal').modal('hide');
                            toastr.success(result.msg);
                            
                            // Refresh tables if they exist
                            if (typeof vehicle_expenses_table !== 'undefined') {
                                vehicle_expenses_table.ajax.reload();
                            }
                            if (typeof expense_history_table !== 'undefined') {
                                expense_history_table.ajax.reload();
                            }
                        } else {
                            toastr.error(result.msg);
                        }
                        $(form).find('button[type="submit"]').prop('disabled', false);
                    },
                    error: function(xhr, status, error) {
                        toastr.error(xhr.responseJSON.message || error);
                        $(form).find('button[type="submit"]').prop('disabled', false);
                    }
                });
                return false;
            }
        });
        
        // Handle view image click
        $(document).on('click', '.view_image', function(e) {
            e.preventDefault();
            var src = $(this).data('href');
            $('#view_image_src').attr('src', src);
            $('.view_image_modal').modal('show');
        });
    });
</script>