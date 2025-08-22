<script type="text/javascript">
    $(document).ready(function() {
        // Initialize select2
        $('.select2').select2();

        // Initialize datatable
        supply_chain_vehicles_table = $('#supply_chain_vehicles_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ action([\App\Http\Controllers\SupplyChainVehicleController::class, 'getAllVehicles']) }}",
                data: function(d) {
                    d.license_plate = $('#license_plate').val();
                    d.make = $('#make').val();
                }
            },
            columns: [
                { data: 'make', name: 'supply_chain_vehicles.make' },
                { data: 'model', name: 'supply_chain_vehicles.model' },
                { data: 'year', name: 'supply_chain_vehicles.year' },
                { data: 'license_plate', name: 'supply_chain_vehicles.license_plate' },
                { data: 'color', name: 'supply_chain_vehicles.color' },
                { data: 'vin', name: 'supply_chain_vehicles.vin' },
                { data: 'notes', name: 'supply_chain_vehicles.notes' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            "fnDrawCallback": function(oSettings) {
                __currency_convert_recursively($('#supply_chain_vehicles_table'));

                // Update footer count
                var api = this.api();
                var total = api.data().count();
                $('.footer_vehicle_count').html('<span class="display_currency" data-currency_symbol="false">' + total + '</span>');
            }
        });

        // Apply filters when inputs change
        $('#license_plate, #make').change(function() {
            supply_chain_vehicles_table.ajax.reload();
        });

        // Handle add vehicle button click
        $('#add_vehicle_btn').click(function() {
            var create_url = "{{ url('/route-vehicles/create') }}";

            // Open in modal
            $('div.vehicle_modal').load(create_url, function() {
                $(this).modal('show');
                // Initialize select2 in the modal
                $(this).find('.select2').select2();
            });
        });

        // Handle form submission for adding a vehicle
        $(document).on('submit', 'form#vehicle_add_form', function(e) {
            e.preventDefault();
            var form = $(this);
            var data = form.serialize();

            $.ajax({
                method: 'POST',
                url: $(this).attr('action'),
                dataType: 'json',
                data: data,
                success: function(result) {
                    if (result.success == true) {
                        $('div.vehicle_modal').modal('hide');
                        toastr.success(result.msg);
                        supply_chain_vehicles_table.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error submitting form:', error);
                    toastr.error('An error occurred while saving the vehicle.');
                }
            });
        });

        // Handle form submission for editing a vehicle
        $(document).on('submit', 'form#vehicle_edit_form', function(e) {
            e.preventDefault();
            var form = $(this);
            var data = form.serialize();

            $.ajax({
                method: 'POST',
                url: $(this).attr('action'),
                dataType: 'json',
                data: data,
                success: function(result) {
                    if (result.success == true) {
                        $('div.vehicle_modal').modal('hide');
                        toastr.success(result.msg);
                        supply_chain_vehicles_table.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error updating form:', error);
                    toastr.error('An error occurred while updating the vehicle.');
                }
            });
        });

        // Handle delete button click
        $(document).on('click', '.delete_vehicle_button', function(e) {
            e.preventDefault();
            var url = $(this).data('href');
            swal({
                title: LANG.sure,
                text: LANG.confirm_delete_vehicle,
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
                                supply_chain_vehicles_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });

        // Handle view mileage history button click
        $(document).on('click', '.view_mileage_history', function(e) {
            e.preventDefault();
            var url = $(this).data('href');
            $('div.mileage_history_modal').load(url, function() {
                $(this).modal('show');
            });
        });

        // Handle modal events
        $(document).on('shown.bs.modal', '.vehicle_modal, .mileage_history_modal', function() {
            $(this).find('.select2').select2();
        });

        $(document).on('hidden.bs.modal', '.vehicle_modal', function() {
            supply_chain_vehicles_table.ajax.reload();
        });
    });
</script>
