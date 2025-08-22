@if(isset($is_oil_change_point) && $is_oil_change_point)
<div class="col-md-6">
    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-car"></i>
            </span>
            {!! Form::select('customer_vehicle_id', 
                [], null, ['class' => 'form-control mousetrap', 'id' => 'customer_vehicle_id', 'placeholder' => 'Select Vehicle', 'required']); !!}
            <span class="input-group-btn">
                <button type="button" class="btn btn-default bg-white btn-flat add_new_vehicle" data-name="" @if(!auth()->user()->can('customer.create')) disabled @endif><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
            </span>
        </div>
    </div>
</div>

<div class="col-md-12">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-tachometer"></i>
                    </span>
                    {!! Form::number('previous_mileage', null, ['class' => 'form-control', 'id' => 'previous_mileage', 'placeholder' => 'Previous Mileage', 'min' => '0', 'step' => '1', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']); !!}
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-oil-can"></i>
                    </span>
                    {!! Form::number('oil_change_mileage', null, ['class' => 'form-control', 'id' => 'oil_change_mileage', 'placeholder' => 'Oil Change Mileage', 'min' => '0', 'step' => '1', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']); !!}
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-road"></i>
                    </span>
                    {!! Form::number('next_mileage', null, ['class' => 'form-control', 'id' => 'next_mileage', 'placeholder' => 'Next Mileage', 'readonly' => 'readonly']); !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endif
