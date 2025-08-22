@extends('layouts.app')
@section('title', 'Add Route Followup')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Add Route Followup</h1>
</section>

<!-- Main content -->
<section class="content">
    {!! Form::open(['url' => action('\App\Http\Controllers\RouteFollowupController@store'), 'method' => 'post', 'id' => 'route_followup_form' ]) !!}
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('customer_route_id', 'Route:*') !!}
                            {!! Form::select('customer_route_id', $customer_routes, null, ['class' => 'form-control select2', 'placeholder' => 'Select Route', 'required', 'id' => 'route_id']); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('contact_id', 'Customer:*') !!}
                            {!! Form::select('contact_id', $contacts, null, ['class' => 'form-control select2', 'placeholder' => 'Select Customer', 'required', 'id' => 'contact_id']); !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('followup_date', 'Date:*') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                {!! Form::text('followup_date', date('Y-m-d'), ['class' => 'form-control', 'required', 'readonly', 'id' => 'followup_date']); !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('notes', 'Notes:*') !!}
                            {!! Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 3, 'required']); !!}
                        </div>
                    </div>
                </div>
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary pull-right">Save</button>
        </div>
    </div>
    {!! Form::close() !!}
</section>
@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        // Initialize date picker with the business date format
        $('#followup_date').datepicker({
            autoclose: true,
            format: datepicker_date_format
        });

        // Load contacts for selected route
        $(document).on('change', '#route_id', function() {
            var route_id = $(this).val();
            if (route_id) {
                $.ajax({
                    method: 'GET',
                    url: '/get-route-contacts',
                    data: { route_id: route_id },
                    dataType: 'json',
                    success: function(result) {
                        $('#contact_id').empty().append('<option value="">Select Customer</option>');
                        if (result.data.length) {
                            result.data.forEach(function(contact) {
                                var displayText = contact.name;
                                if (contact.supplier_business_name) {
                                    displayText = contact.supplier_business_name + ' - ' + contact.name;
                                }
                                $('#contact_id').append('<option value="' + contact.id + '">' + displayText + '</option>');
                            });
                        }
                    },
                });
            } else {
                $('#contact_id').empty().append('<option value="">Select Customer</option>');
            }
        });
    });
</script>
@endsection
