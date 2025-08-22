<script type="text/javascript">
    $(document).ready(function() {
        // Initialize dataTable
        vehicle_route_assignments_table = $('#vehicle_route_assignments_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/vehicle-route-assignments/get-all-assignments',
            columnDefs: [
                {
                    targets: 7,
                    orderable: false,
                    searchable: false,
                },
            ],
            columns: [
                {data: 'make', name: 'supply_chain_vehicles.make'},
                {data: 'license_plate', name: 'supply_chain_vehicles.license_plate'},
                {data: 'route_name', name: 'customer_routes.name'},
                {data: 'route_assigned_at', name: 'supply_chain_vehicles.route_assigned_at'},
                {data: 'start_mileage', name: 'supply_chain_vehicle_mileage.start_mileage', className: 'text-right'},
                {data: 'end_mileage', name: 'supply_chain_vehicle_mileage.end_mileage', className: 'text-right'},
                {
                    data: null,
                    name: 'travel_distance',
                    className: 'text-right',
                    render: function(data, type, row) {
                        var start = parseInt(row.start_mileage) || 0;
                        var end = parseInt(row.end_mileage) || 0;
                        return end - start;
                    }
                },
                {data: 'action', name: 'action'}
            ]
        });

        // Handle delete button click
        $(document).on('click', '.delete_assignment_button', function(e) {
            e.preventDefault();
            swal({
                title: LANG.sure,
                text: LANG.confirm_delete_assignment,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();

                    $.ajax({
                        method: "DELETE",
                        url: href,
                        dataType: "json",
                        data: data,
                        success: function(result) {
                            if(result.success == true) {
                                toastr.success(result.msg);
                                vehicle_route_assignments_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });
    });
</script>
