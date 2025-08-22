{{-- vehicles/partials/mileage_history_modal.blade.php --}}
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

                        <table class="table table-bordered table-striped" id="mileage_history_table" style="width:100%">
                            <thead>
                            <tr>
                                <th>@lang('lang_v1.date')</th>
                                <th>@lang('lang_v1.invoice_no')</th>
                                <th>@lang('lang_v1.previous_mileage')</th>
                                <th>@lang('lang_v1.oil_change_mileage')</th>
                                <th>@lang('lang_v1.next_mileage')</th>
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
                        url: "{{ action([\App\Http\Controllers\VehicleMileageController::class, 'getMileageHistory'], [$vehicle->id]) }}",
                        type: 'GET'
                    },

                    columns: [
                        { data: 'created_at',         name: 'created_at' },
                        { data: 'invoice_id',         name: 'invoice_id' },
                        { data: 'previous_mileage',   name: 'previous_mileage',  className: 'text-right' },
                        { data: 'oil_change_mileage', name: 'oil_change_mileage',className: 'text-right' },
                        { data: 'next_mileage',       name: 'next_mileage',      className: 'text-right' }
                    ],

                    drawCallback: function () {
                        // If you use currency/number formatting
                        if (typeof __currency_convert_recursively === 'function') {
                            __currency_convert_recursively($('#mileage_history_table'));
                        }
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
