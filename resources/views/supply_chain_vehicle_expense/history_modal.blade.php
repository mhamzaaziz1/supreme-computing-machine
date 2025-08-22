{{-- supply_chain_vehicle_expense/history_modal.blade.php --}}
<div class="modal fade" id="expense_history_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="@lang('messages.close')">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    @lang('lang_v1.expense_history') -
                    {{ $vehicle->make }} {{ $vehicle->model }}
                    @if(!empty($vehicle->year)) ({{ $vehicle->year }}) @endif
                    @if(!empty($vehicle->license_plate)) - {{ $vehicle->license_plate }} @endif
                </h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary btn-modal pull-right" 
                            data-href="{{ action([\App\Http\Controllers\SupplyChainVehicleExpenseController::class, 'create'], [$vehicle->id]) }}" 
                            data-container=".expense_modal">
                            <i class="fa fa-plus"></i> @lang('lang_v1.add_expense')
                        </button>
                        <br><br>
                        <table class="table table-bordered table-striped" id="expense_history_table" style="width:100%">
                            <thead>
                            <tr>
                                <th>@lang('lang_v1.date')</th>
                                <th>@lang('lang_v1.expense_type')</th>
                                <th>@lang('lang_v1.amount')</th>
                                <th>@lang('lang_v1.mileage_record')</th>
                                <th>@lang('lang_v1.description')</th>
                                <th>@lang('messages.action')</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr class="bg-gray font-17 footer-total text-center">
                                    <td colspan="2"><strong>@lang('sale.total'):</strong></td>
                                    <td class="footer_expense_total"></td>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade expense_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade view_image_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('lang_v1.view_receipt')</h4>
            </div>
            <div class="modal-body">
                <img src="" id="view_image_src" class="img-responsive" style="max-width: 100%;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        /* Optional: nicer processing overlay inside modal */
        #expense_history_table_processing {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%,-50%);
            padding: 10px 16px;
            z-index: 2;
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function () {
            let expense_history_table = null;

            // When modal becomes visible, initialize (or adjust) the table
            $('#expense_history_modal').on('shown.bs.modal', function () {
                if ($.fn.DataTable.isDataTable('#expense_history_table')) {
                    expense_history_table.columns.adjust().draw(false);
                    return;
                }

                expense_history_table = $('#expense_history_table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    autoWidth: false,
                    deferRender: true,
                    pageLength: 10,
                    lengthChange: true,
                    order: [[0, 'desc']], // newest first by date

                    ajax: {
                        url: "{{ action([\App\Http\Controllers\SupplyChainVehicleExpenseController::class, 'getExpenseHistory'], [$vehicle->id]) }}",
                        type: 'GET'
                    },

                    columns: [
                        { data: 'date',              name: 'date' },
                        { data: 'expense_type_text', name: 'expense_type_text' },
                        { data: 'amount',            name: 'amount', className: 'text-right' },
                        { data: 'mileage_record.date', name: 'mileage_record.date', 
                          render: function(data, type, row) {
                              if (row.mileage_record) {
                                  return row.mileage_record.date + ' - ' + 
                                      (row.mileage_record.end_mileage - row.mileage_record.start_mileage) + ' km';
                              }
                              return '';
                          }
                        },
                        { data: 'description',       name: 'description' },
                        { data: 'action',            name: 'action', orderable: false, searchable: false }
                    ],

                    drawCallback: function () {
                        // Format currency
                        __currency_convert_recursively($('#expense_history_table'));
                        
                        // Calculate and display total expenses
                        var api = this.api();
                        var total = api.column(2).data().reduce(function (a, b) {
                            return parseFloat(a) + parseFloat(b.replace(/[^\d.-]/g, ''));
                        }, 0);
                        
                        $('.footer_expense_total').html(
                            '<span class="display_currency" data-currency_symbol="true">' + 
                            total.toFixed(2) + 
                            '</span>'
                        );
                    }
                });
            });

            // Handle view image click
            $(document).on('click', '.view_image', function(e) {
                e.preventDefault();
                var src = $(this).data('href');
                $('#view_image_src').attr('src', src);
                $('.view_image_modal').modal('show');
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
                                    expense_history_table.ajax.reload();
                                } else {
                                    toastr.error(result.msg);
                                }
                            }
                        });
                    }
                });
            });

            // Optional: destroy when hidden (prevents duplicates if you re-open often)
            $('#expense_history_modal').on('hidden.bs.modal', function () {
                if (expense_history_table) {
                    expense_history_table.clear().destroy();
                    expense_history_table = null;
                    $('#expense_history_table tbody').empty();
                }
            });
        })();
    </script>
@endpush