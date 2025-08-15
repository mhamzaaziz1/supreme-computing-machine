<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action([\App\Http\Controllers\CustomerRouteController::class, 'store']), 'method' => 'post', 'id' => 'customer_route_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'lang_v1.add_customer_route' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('name', __( 'lang_v1.name' ) . ':*') !!}
        {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'lang_v1.name' ) ]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('parent_id', __( 'lang_v1.parent_route' ) . ':') !!}
        {!! Form::select('parent_id', $parent_routes, null, ['class' => 'form-control', 'placeholder' => __('lang_v1.none')]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('description', __( 'lang_v1.description' ) . ':') !!}
        {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => __( 'lang_v1.description' )]); !!}
      </div>

      <div class="form-group">
        <label>
          {!! Form::checkbox('is_active', 1, true, ['class' => 'input-icheck']); !!} @lang('lang_v1.is_active')
        </label>
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang( 'messages.save' )</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->