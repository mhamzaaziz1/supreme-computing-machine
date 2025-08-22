<script type="text/javascript">
    $(document).ready(function() {
        // Initialize select2
        $('.select2').select2();

        // Initialize datatable
        vehicles_table = $('#vehicles_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ action([\App\Http\Controllers\CustomerVehicleController::class, 'getAllVehicles']) }}",
                data: function(d) {
                    d.license_plate = $('#license_plate_filter').val();
                    d.make = $('#make_filter').val();
                    d.customer_id = $('#customer_filter').val();
                }
            },
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'customer_name', name: 'contacts.name' },
                { data: 'make', name: 'customer_vehicles.make' },
                { data: 'model', name: 'customer_vehicles.model' },
                { data: 'year', name: 'customer_vehicles.year' },
                { data: 'license_plate', name: 'customer_vehicles.license_plate' },
                { data: 'color', name: 'customer_vehicles.color' },
                { data: 'vin', name: 'customer_vehicles.vin' },
                { data: 'notes', name: 'customer_vehicles.notes' },
                { data: 'created_at', name: 'customer_vehicles.created_at' }
            ],
            "fnDrawCallback": function(oSettings) {
                __currency_convert_recursively($('#vehicles_table'));
            }
        });

        // Apply filter when input changes
        $('#license_plate_filter, #make_filter, #customer_filter').change(function() {
            vehicles_table.ajax.reload();
        });

        // Handle add vehicle button click
        $('#add_vehicle_btn').click(function() {
            var customer_id = $('#customer_filter').val();
            if (customer_id) {
                // Redirect to create page with customer_id
                var create_url = "{{ action([\App\Http\Controllers\CustomerVehicleController::class, 'create'], ['contact_id' => ':contact_id']) }}";
                create_url = create_url.replace(':contact_id', customer_id);
                // Open in modal
                $('div.vehicle_modal').load(create_url, function() {
                    $(this).modal('show');
                });
            } else {
                // Show message to select a customer first
                swal({
                    title: LANG.error,
                    text: LANG.please_select_customer_first,
                    icon: 'error'
                });
            }
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
                        vehicles_table.ajax.reload();
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
                        vehicles_table.ajax.reload();
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
            swal({
                title: LANG.sure,
                text: LANG.confirm_delete_vehicle,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();

                    $.ajax({
                        method: 'DELETE',
                        url: href,
                        dataType: 'json',
                        data: data,
                        success: function(result) {
                            if (result.success == true) {
                                toastr.success(result.msg);
                                vehicles_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error deleting vehicle:', error);
                            toastr.error('An error occurred while deleting the vehicle.');
                        }
                    });
                }
            });
        });
    });
</script>
