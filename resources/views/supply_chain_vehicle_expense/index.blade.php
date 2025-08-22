@extends('layouts.app')
@section('title', __('lang_v1.vehicle_expenses'))

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('lang_v1.vehicle_expenses')</h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.filters', ['title' => __('report.filters')])
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('vehicle_id', __('lang_v1.vehicle') . ':') !!}
                    {!! Form::select('vehicle_id', $vehicles, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('expense_category_id', __('expense.expense_category') . ':') !!}
                    {!! Form::select('expense_category_id', $expense_categories, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('expense_type', __('lang_v1.expense_type') . ':') !!}
                    {!! Form::select('expense_type', $expense_types, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                </div>
            </div>
        </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.all_vehicle_expenses')])
        @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn btn-block btn-primary btn-modal" 
                    data-href="{{action([\App\Http\Controllers\SupplyChainVehicleExpenseController::class, 'create'])}}" 
                    data-container=".expense_modal" id="add_expense_btn">
                    <i class="fa fa-plus"></i> @lang('messages.add')
                </button>
            </div>
        @endslot
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="vehicle_expenses_table">
                <thead>
                    <tr>
                        <th>@lang('lang_v1.date')</th>
                        <th>@lang('lang_v1.vehicle')</th>
                        <th>@lang('expense.expense_category')</th>
                        <th>@lang('lang_v1.expense_type')</th>
                        <th>@lang('lang_v1.amount')</th>
                        <th>@lang('lang_v1.description')</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent

    <div class="modal fade expense_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade view_document_modal" tabindex="-1" role="dialog" aria-labelledby="view_document_modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('lang_v1.view_document')</h4>
                </div>
                <div class="modal-body">
                    <div class="document_view_container">
                        <img src="" class="img-responsive" alt="Document View">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
                </div>
            </div>
        </div>
    </div>
</section>
@stop

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        // Initialize select2
        $('.select2').select2();

        // Initialize daterangepicker
        $('#date_range').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            }
        );
        $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#date_range').val('');
        });

        // Initialize datatable
        vehicle_expenses_table = $('#vehicle_expenses_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ action([\App\Http\Controllers\SupplyChainVehicleExpenseController::class, 'getAllExpenses']) }}",
                data: function(d) {
                    var start = '';
                    var end = '';
                    if ($('#date_range').val()) {
                        start = $('input#date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        end = $('input#date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    }
                    d.start_date = start;
                    d.end_date = end;
                    d.vehicle_id = $('#vehicle_id').val();
                    d.expense_category_id = $('#expense_category_id').val();
                    d.expense_type = $('#expense_type').val();
                }
            },
            columns: [
                { data: 'transaction_date', name: 'transaction_date' },
                { data: 'vehicle_name', name: 'scv.make' },
                { data: 'category_name', name: 'ec.name' },
                { data: 'expense_type_text', name: 'expense_type_text' },
                { data: 'final_total', name: 'final_total' },
                { data: 'additional_notes', name: 'additional_notes' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            "fnDrawCallback": function(oSettings) {
                __currency_convert_recursively($('#vehicle_expenses_table'));
            }
        });

        // Apply filters when inputs change
        $('#vehicle_id, #expense_category_id, #expense_type, #date_range').change(function() {
            vehicle_expenses_table.ajax.reload();
        });

        // Handle view document click
        $(document).on('click', '.view_uploaded_document', function(e) {
            e.preventDefault();
            var src = $(this).data('href');
            $('.document_view_container img').attr('src', src);
            $('.view_document_modal').modal('show');
        });

        // Handle delete expense record click
        $(document).on('click', '.delete_expense_record', function(e) {
            e.preventDefault();
            var url = $(this).data('href');
            swal({
                title: LANG.sure,
                text: LANG.confirm_delete_expense_record,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((confirmed) => {
                if (confirmed) {
                    $.ajax({
                        method: 'DELETE',
                        url: url,
                        dataType: 'json',
                        success: function(result) {
                            if (result.success) {
                                toastr.success(result.msg);
                                vehicle_expenses_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });
        // Handle add expense button click
        $(document).on('click', '#add_expense_btn', function(e) {
            e.preventDefault();

            // Check if a vehicle is selected in the filter
            var selectedVehicle = $('#vehicle_id').val();
            if (!selectedVehicle) {
                // No vehicle selected, show alert
                swal({
                    title: LANG.warning,
                    text: "Please select a vehicle first",
                    icon: "warning",
                });
                return;
            }

            // Open the create form with the selected vehicle_id
            var href = "{{action([\App\Http\Controllers\SupplyChainVehicleExpenseController::class, 'create'])}}?vehicle_id=" + selectedVehicle;
            $.ajax({
                url: href,
                dataType: 'html',
                success: function(result) {
                    $('.expense_modal').html(result).modal('show');
                },
            });
        });

        // Handle modal button click
        $(document).on('click', '.btn-modal', function(e) {
            e.preventDefault();
            var href = $(this).data('href');

            // If this is the add expense button and no vehicle_id is in the URL
            if (href === "{{action([\App\Http\Controllers\SupplyChainVehicleExpenseController::class, 'create'])}}" && 
                !href.includes('?vehicle_id=')) {

                // Check if a vehicle is selected in the filter
                var selectedVehicle = $('#vehicle_id').val();
                if (!selectedVehicle) {
                    // No vehicle selected, show alert
                    swal({
                        title: LANG.warning,
                        text: "{{__('lang_v1.please_select_vehicle_first')}}",
                        icon: "warning",
                    });
                    return;
                }

                // Update href with the selected vehicle_id
                href = "{{action([\App\Http\Controllers\SupplyChainVehicleExpenseController::class, 'create'])}}?vehicle_id=" + selectedVehicle;
                $(this).data('href', href);
            }

            var container = $(this).data('container');
            $.ajax({
                url: href,
                dataType: 'html',
                success: function(result) {
                    $(container).html(result).modal('show');
                },
            });
        });
    });
</script>
@endsection
