{{-- supply_chain_vehicle_mileage/history_modal.blade.php --}}
<div class="modal fade" id="mileage_history_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="@lang('messages.close')">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    @lang('lang_v1.mileage_history') -
                    {{ $vehicle->make }} {{ $vehicle->model }}
                    @if(!empty($vehicle->year)) ({{ $vehicle->year }}) @endif
                    @if(!empty($vehicle->license_plate)) - {{ $vehicle->license_plate }} @endif
                </h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary btn-modal pull-right" 
                            data-href="{{ action([\App\Http\Controllers\SupplyChainVehicleMileageController::class, 'create'], [$vehicle->id]) }}" 
                            data-container=".mileage_modal">
                            <i class="fa fa-plus"></i> @lang('lang_v1.add_mileage_record')
                        </button>
                        <br><br>
                        <table class="table table-bordered table-striped" id="mileage_history_table" style="width:100%">
                            <thead>
                            <tr>
                                <th>@lang('lang_v1.date')</th>
                                <th>@lang('lang_v1.start_mileage')</th>
                                <th>@lang('lang_v1.end_mileage')</th>
                                <th>@lang('lang_v1.travel_distance')</th>
                                <th>@lang('lang_v1.notes')</th>
                                <th>@lang('messages.action')</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
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

<div class="modal fade mileage_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade view_image_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('lang_v1.view_image')</h4>
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
        #mileage_history_table_processing {
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
            let mileage_history_table = null;

            // When modal becomes visible, initialize (or adjust) the table
            $('#mileage_history_modal').on('shown.bs.modal', function () {
                if ($.fn.DataTable.isDataTable('#mileage_history_table')) {
                    mileage_history_table.columns.adjust().draw(false);
                    return;
                }

                mileage_history_table = $('#mileage_history_table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    autoWidth: false,
                    deferRender: true,
                    pageLength: 10,
                    lengthChange: true,
                    order: [[0, 'desc']], // newest first by date

                    ajax: {
                        url: "{{ action([\App\Http\Controllers\SupplyChainVehicleMileageController::class, 'getMileageHistory'], [$vehicle->id]) }}",
                        type: 'GET'
                    },

                    columns: [
                        { data: 'date',            name: 'date' },
                        { data: 'start_mileage',   name: 'start_mileage',  className: 'text-right' },
                        { data: 'end_mileage',     name: 'end_mileage',    className: 'text-right' },
                        { data: 'travel_distance', name: 'travel_distance',className: 'text-right' },
                        { data: 'notes',           name: 'notes' },
                        { data: 'action',          name: 'action', orderable: false, searchable: false }
                    ],

                    drawCallback: function () {
                        // If you use currency/number formatting
                        if (typeof __currency_convert_recursively === 'function') {
                            __currency_convert_recursively($('#mileage_history_table'));
                        }
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

            // Handle delete mileage record click
            $(document).on('click', '.delete_mileage_record', function(e) {
                e.preventDefault();
                var url = $(this).data('href');
                swal({
                    title: LANG.sure,
                    text: LANG.confirm_delete_mileage_record,
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
                                    mileage_history_table.ajax.reload();
                                } else {
                                    toastr.error(result.msg);
                                }
                            }
                        });
                    }
                });
            });

            // Optional: destroy when hidden (prevents duplicates if you re-open often)
            $('#mileage_history_modal').on('hidden.bs.modal', function () {
                if (mileage_history_table) {
                    mileage_history_table.clear().destroy();
                    mileage_history_table = null;
                    $('#mileage_history_table tbody').empty();
                }
            });
        })();
    </script>
@endpush