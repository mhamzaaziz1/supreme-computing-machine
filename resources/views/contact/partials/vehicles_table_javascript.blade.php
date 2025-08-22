<script type="text/javascript">
$(document).ready(function() {
    // Add event handler for license plate filter
    $(document).on('change', '#vehicle_list_filter_license_plate', function() {
        vehicles_table.ajax.reload();
    });

    // Add event handler for make filter
    $(document).on('change', '#vehicle_list_filter_make', function() {
        vehicles_table.ajax.reload();
    });

    // Initialize dataTable for vehicles
    vehicles_table = $('#vehicles_table').DataTable({
        processing: true,
        serverSide: true,
        fixedHeader: false,
        aaSorting: [[0, 'asc']],
        scrollY: "75vh",
        scrollX: true,
        scrollCollapse: true,
        ajax: {
            url: "{{action([\App\Http\Controllers\CustomerVehicleController::class, 'getVehicles'], [$contact->id])}}",
            "data": function(d) {
                if($('#vehicle_list_filter_license_plate').val()) {
                    d.license_plate = $('#vehicle_list_filter_license_plate').val();
                }

                if($('#vehicle_list_filter_make').val()) {
                    d.make = $('#vehicle_list_filter_make').val();
                }

                d = __datatable_ajax_callback(d);
            }
        },
        columns: [
            { data: 'make', name: 'make' },
            { data: 'model', name: 'model' },
            { data: 'year', name: 'year' },
            { data: 'license_plate', name: 'license_plate' },
            { data: 'color', name: 'color' },
            { data: 'vin', name: 'vin' },
            { data: 'notes', name: 'notes' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        "fnDrawCallback": function(oSettings) {
            __currency_convert_recursively($('#vehicles_table'));
        },
        "footerCallback": function(row, data, start, end, display) {
            // Display total number of vehicles in the footer
            $('.footer_vehicle_count').html(data.length);
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
                    // Hide the modal
                    $('div.vehicle_modal').modal('hide');

                    // Show success alert
                    swal({
                        title: 'Success',
                        text: result.msg,
                        icon: 'success',
                        buttons: false,
                        timer: 2000,
                    });

                    // Also show toastr notification
                    toastr.success(result.msg);

                    // Reload the datatable
                    vehicles_table.ajax.reload();
                } else {
                    toastr.error(result.msg);
                }
            },
            error: function(xhr, status, error) {
                // Log any errors to console for debugging
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
                    // Hide the modal
                    $('div.vehicle_modal').modal('hide');

                    // Show success alert
                    swal({
                        title: 'Success',
                        text: result.msg,
                        icon: 'success',
                        buttons: false,
                        timer: 2000,
                    });

                    // Also show toastr notification
                    toastr.success(result.msg);

                    // Reload the datatable
                    vehicles_table.ajax.reload();
                } else {
                    toastr.error(result.msg);
                }
            },
            error: function(xhr, status, error) {
                // Log any errors to console for debugging
                console.error('Error updating form:', error);
                toastr.error('An error occurred while updating the vehicle.');
            }
        });
    });

    // Handle delete button click
    $(document).on('click', '.delete_vehicle_button', function() {
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
                            // Show success alert
                            swal({
                                title: 'Success',
                                text: result.msg,
                                icon: 'success',
                                buttons: false,
                                timer: 2000,
                            });

                            // Also show toastr notification
                            toastr.success(result.msg);

                            // Reload the datatable
                            vehicles_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Log any errors to console for debugging
                        console.error('Error deleting vehicle:', error);
                        toastr.error('An error occurred while deleting the vehicle.');
                    }
                });
            }
        });
    });
});
</script>
