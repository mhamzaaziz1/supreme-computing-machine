@extends('layouts.app')
@section('title', __('lang_v1.route_assignments'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('lang_v1.route_assignments')</h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.all_route_assignments')])
        @can('customer.create')
            @slot('tool')
                <div class="box-tools">
                    <a class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full btn-modal"
                        data-href="{{ action([\App\Http\Controllers\RouteSellerAssignmentController::class, 'create']) }}"
                        data-container=".route_assignments_modal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg> @lang('messages.add')
                    </a>
                </div>
            @endslot
        @endcan
        @can('customer.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="route_assignments_table">
                    <thead>
                        <tr>
                            <th>@lang('lang_v1.seller')</th>
                            <th>@lang('lang_v1.route')</th>
                            <th>@lang('lang_v1.status')</th>
                            <th>@lang('lang_v1.assignment_date')</th>
                            <th>@lang('messages.date')</th>
                            <th>@lang('messages.action')</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcan
    @endcomponent

    <div class="modal fade route_assignments_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->
@stop

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        // Initialize dataTable
        var route_assignments_table = $('#route_assignments_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{!! action([\App\Http\Controllers\RouteSellerAssignmentController::class, "index"]) !!}',
            },
            columns: [
                { data: 'first_name', name: 'users.first_name' },
                { data: 'route_name', name: 'customer_routes.name' },
                { data: 'is_active', name: 'route_seller_assignments.is_active' },
                { data: 'assignment_date', name: 'route_seller_assignments.assignment_date' },
                { data: 'created_at', name: 'route_seller_assignments.created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });

        // Handle modal events
        $(document).on('shown.bs.modal', '.route_assignments_modal', function() {
            $('.route_assignments_modal').find('.select2').select2();
            
            // Initialize datepicker for assignment_date field
            $('.route_assignments_modal').find('.datepicker').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
            });
        });

        // Handle form submission
        $(document).on('submit', 'form#route_assignment_add_form', function(e) {
            e.preventDefault();
            $(this).find('button[type="submit"]').attr('disabled', true);
            var data = $(this).serialize();

            $.ajax({
                method: $(this).attr('method'),
                url: $(this).attr('action'),
                dataType: 'json',
                data: data,
                success: function(result) {
                    if (result.success == true) {
                        $('div.route_assignments_modal').modal('hide');
                        toastr.success(result.msg);
                        route_assignments_table.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                    $('form#route_assignment_add_form').find('button[type="submit"]').attr('disabled', false);
                }
            });
        });

        // Handle edit button click
        $(document).on('click', '.edit_assignment_button', function(e) {
            e.preventDefault();
            $('div.route_assignments_modal').load($(this).data('href'), function() {
                $(this).modal('show');
            });
        });

        // Handle delete button click
        $(document).on('click', '.delete_assignment_button', function(e) {
            e.preventDefault();
            swal({
                title: LANG.sure,
                text: LANG.confirm_delete_assignment,
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
                                route_assignments_table.ajax.reload();
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
@endsection