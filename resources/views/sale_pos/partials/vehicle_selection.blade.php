@php
    //The panel is always rendered so the cashier can switch to an oil change
    //point without reloading the POS screen. Visibility and the "required"
    //rule follow #select_location_id at runtime; see
    //sale_pos.partials.vehicle_selection_javascript.
    $oil_change_point = !empty($is_oil_change_point);

    $oil_field = 'width:100%;height:40px;border:1px solid #dfe3ec;border-radius:10px;padding:0 12px;font-size:14px;color:#2d3748;background:#fff;box-shadow:none;outline:none;';
    $oil_addon = 'display:flex;align-items:center;justify-content:center;width:40px;height:40px;flex:0 0 40px;border:1px solid #dfe3ec;border-right:0;border-radius:10px 0 0 10px;background:#f4f6fb;color:#646EE4;';
    $oil_label = 'display:block;font-size:11px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:#8a94a6;margin-bottom:6px;';
@endphp

<div class="col-md-12 oil_change_panel" id="oil_change_panel" data-default_is_oil_change_point="{{ $oil_change_point ? 1 : 0 }}" style="{{ $oil_change_point ? '' : 'display:none;' }}">
    <div class="oil_change_card" style="border:1px solid #e6e9f2;border-left:4px solid #646EE4;border-radius:14px;background:linear-gradient(180deg,#fbfcff 0%,#f6f8fd 100%);padding:14px 16px 6px;margin-bottom:12px;">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
            <span style="display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:8px;background:#646EE4;color:#fff;font-size:12px;">
                <i class="fas fa-oil-can"></i>
            </span>
            <strong style="font-size:13px;letter-spacing:.02em;color:#3d4459;">@lang('lang_v1.oil_change_details')</strong>
            <span style="font-size:11px;font-weight:600;color:#fff;background:#646EE4;border-radius:20px;padding:2px 9px;">@lang('lang_v1.required')</span>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group" style="margin-bottom:14px;">
                    <label for="customer_vehicle_id" style="{{ $oil_label }}">@lang('lang_v1.vehicle')</label>
                    <div class="oil_vehicle_field" style="position:relative;display:flex;align-items:stretch;">
                        <span class="oil_field_addon" style="{{ $oil_addon }}"><i class="fa fa-car"></i></span>
                        {!! Form::select('customer_vehicle_id', [], null, [
                            'class' => 'mousetrap',
                            'id' => 'customer_vehicle_id',
                            'placeholder' => __('lang_v1.select_vehicle'),
                            'style' => $oil_field . 'border-radius:0;flex:1 1 auto;min-width:0;',
                            'data-msg-required' => __('lang_v1.vehicle_required_for_oil_change'),
                        ]) !!}
                        <button type="button"
                            class="add_new_vehicle"
                            title="@lang('lang_v1.add_vehicle')"
                            style="flex:0 0 42px;width:42px;height:40px;border:1px solid #646EE4;border-radius:0 10px 10px 0;background:#646EE4;color:#fff;font-size:15px;cursor:pointer;"
                            @if (!auth()->user()->can('customer.create')) disabled @endif>
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-2 col-xs-4">
                <div class="form-group" style="margin-bottom:14px;">
                    <label for="previous_mileage" style="{{ $oil_label }}">@lang('lang_v1.previous_mileage')</label>
                    {!! Form::number('previous_mileage', null, [
                        'id' => 'previous_mileage',
                        'placeholder' => '0',
                        'min' => '0',
                        'step' => '1',
                        'style' => $oil_field,
                        'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57',
                    ]) !!}
                </div>
            </div>

            <div class="col-md-2 col-xs-4">
                <div class="form-group" style="margin-bottom:14px;">
                    <label for="oil_change_mileage" style="{{ $oil_label }}">@lang('lang_v1.oil_change_mileage')</label>
                    {!! Form::number('oil_change_mileage', null, [
                        'id' => 'oil_change_mileage',
                        'placeholder' => '0',
                        'min' => '0',
                        'step' => '1',
                        'style' => $oil_field,
                        'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57',
                    ]) !!}
                </div>
            </div>

            <div class="col-md-2 col-xs-4">
                <div class="form-group" style="margin-bottom:14px;">
                    <label for="next_mileage" style="{{ $oil_label }}">@lang('lang_v1.next_mileage')</label>
                    {!! Form::number('next_mileage', null, [
                        'id' => 'next_mileage',
                        'placeholder' => '0',
                        'readonly' => 'readonly',
                        'style' => $oil_field . 'background:#eef1f8;color:#5a6379;font-weight:700;',
                    ]) !!}
                </div>
            </div>
        </div>
    </div>
</div>
