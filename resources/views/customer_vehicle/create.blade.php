@php
  //Inline styling so the popup matches the POS oil change panel it is opened
  //from, without needing a stylesheet on every screen that loads this modal.
  $v_label = 'display:block;font-size:11px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:#8a94a6;margin-bottom:6px;';
  $v_field = 'width:100%;min-height:40px;border:1px solid #dfe3ec;border-radius:10px;padding:9px 12px;font-size:14px;color:#2d3748;background:#fff;box-shadow:none;';
@endphp
@include('customer_vehicle.form_css')
<div class="modal-dialog" role="document">
  <div class="modal-content vehicle_form_modal" style="border:0;border-radius:16px;overflow:hidden;box-shadow:0 24px 48px rgba(17,17,26,.18);">

    {!! Form::open(['url' => action([\App\Http\Controllers\CustomerVehicleController::class, 'store']), 'method' => 'post', 'id' => 'vehicle_add_form' ]) !!}

    <div class="modal-header" style="border-bottom:0;padding:18px 22px;background:linear-gradient(135deg,#646EE4 0%,#4d57cf 100%);color:#fff;">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;opacity:.85;text-shadow:none;font-size:26px;margin-top:-2px;"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title" style="display:flex;align-items:center;gap:10px;font-size:17px;font-weight:700;margin:0;">
        <i class="fa fa-car"></i> @lang('lang_v1.add_vehicle')
      </h4>
      @php $contact_label = trim($contact->name) !== '' ? $contact->name : $contact->supplier_business_name; @endphp
      @if(!empty($contact_label))
        <small style="display:block;margin-top:4px;opacity:.85;font-size:12px;">{{ $contact_label }}</small>
      @endif
    </div>

    <div class="modal-body" style="padding:22px;background:#fbfcff;">
      <div class="row">
        <input type="hidden" name="contact_id" value="{{ $contact->id }}">

        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('make', __('lang_v1.vehicle_make') . ' *', ['style' => $v_label]) !!}
            {!! Form::text('make', null, ['required', 'placeholder' => __('lang_v1.vehicle_make'), 'style' => $v_field]); !!}
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('model', __('lang_v1.vehicle_model') . ' *', ['style' => $v_label]) !!}
            {!! Form::text('model', null, ['required', 'placeholder' => __('lang_v1.vehicle_model'), 'style' => $v_field]); !!}
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('year', __('lang_v1.vehicle_year'), ['style' => $v_label]) !!}
            {!! Form::text('year', null, ['placeholder' => __('lang_v1.vehicle_year'), 'style' => $v_field]); !!}
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('license_plate', __('lang_v1.license_plate'), ['style' => $v_label]) !!}
            {!! Form::text('license_plate', null, ['placeholder' => __('lang_v1.license_plate'), 'style' => $v_field]); !!}
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('color', __('lang_v1.vehicle_color'), ['style' => $v_label]) !!}
            {!! Form::text('color', null, ['placeholder' => __('lang_v1.vehicle_color'), 'style' => $v_field]); !!}
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('vin', __('lang_v1.vehicle_vin'), ['style' => $v_label]) !!}
            {!! Form::text('vin', null, ['placeholder' => __('lang_v1.vehicle_vin'), 'style' => $v_field]); !!}
          </div>
        </div>

        <div class="col-md-12">
          <div class="form-group" style="margin-bottom:0;">
            {!! Form::label('notes', __('lang_v1.vehicle_notes'), ['style' => $v_label]) !!}
            {!! Form::textarea('notes', null, ['placeholder' => __('lang_v1.vehicle_notes'), 'rows' => 2, 'style' => $v_field]); !!}
          </div>
        </div>
      </div>
    </div>

    <div class="modal-footer" style="border-top:1px solid #eef0f7;padding:14px 22px;background:#fff;">
      <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius:10px;padding:9px 18px;font-weight:600;">@lang('messages.close')</button>
      <button type="submit" class="btn btn-primary" style="border:0;border-radius:10px;padding:9px 22px;font-weight:600;background:#646EE4;">
        <i class="fa fa-check"></i> @lang('messages.save')
      </button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
