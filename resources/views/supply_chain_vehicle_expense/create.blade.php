@extends('layouts.app')
@section('title', __('lang_v1.add_vehicle_expense'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('lang_v1.add_vehicle_expense')</h1>
</section>

<!-- Main content -->
<section class="content">
    {!! Form::open(['url' => action([\App\Http\Controllers\SupplyChainVehicleExpenseController::class, 'store']), 'method' => 'post', 'id' => 'vehicle_expense_form', 'files' => true ]) !!}
    <div class="box box-solid">
        <div class="box-body">
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('supply_chain_vehicle_id', __('lang_v1.vehicle') . ':*') !!}
                        {!! Form::select('supply_chain_vehicle_id', [$vehicle->id => $vehicle->make . ' ' . $vehicle->model . ($vehicle->year ? ' (' . $vehicle->year . ')' : '') . ($vehicle->license_plate ? ' - ' . $vehicle->license_plate : '')], $vehicle->id, ['class' => 'form-control select2', 'required', 'style' => 'width:100%', 'readonly' => 'readonly']) !!}
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('expense_type', __('lang_v1.expense_type') . ':*') !!}
                        {!! Form::select('expense_type', $expense_types, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width:100%']); !!}
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('supply_chain_vehicle_mileage_id', __('lang_v1.mileage_record') . ':') !!}
                        {!! Form::select('supply_chain_vehicle_mileage_id', $mileage_records, null, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.none'), 'style' => 'width:100%']) !!}
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('date', __('messages.date') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            {!! Form::text('date', date('Y-m-d'), ['class' => 'form-control', 'readonly', 'required', 'id' => 'expense_date']); !!}
                        </div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('amount', __('sale.total_amount') . ':*') !!}
                        {!! Form::text('amount', null, ['class' => 'form-control input_number', 'placeholder' => __('sale.total_amount'), 'required']); !!}
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('receipt_image', __('lang_v1.receipt_image') . ':') !!}
                        {!! Form::file('receipt_image', ['class' => 'form-control', 'accept' => 'image/*']) !!}
                        <p class="help-block">@lang('lang_v1.receipt_image_help')</p>
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="col-sm-12">
                    <div class="form-group">
                        {!! Form::label('description', __('lang_v1.description') . ':') !!}
                        {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => __('lang_v1.description')]) !!}
                    </div>
                </div>
            </div>
        </div>
    </div> <!--box end-->

    <div class="col-sm-12 text-center">
        <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-lg tw-text-white">@lang('messages.save')</button>
    </div>
    {!! Form::close() !!}
</section>
@endsection

@push('scripts')
<script type="text/javascript">
// Initialize date picker
$('#expense_date').datepicker({
    autoclose: true,
    format: datepicker_date_format
});

// Initialize select2
$('.select2').select2();

// Form validation and submission
$('#vehicle_expense_form').validate({
    submitHandler: function(form) {
        $(form).find('button[type="submit"]').prop('disabled', true);
        form.submit();
    }
});
</script>
@endpush