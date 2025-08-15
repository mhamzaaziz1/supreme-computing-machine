<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action([\App\Http\Controllers\CustomerRouteController::class, 'update'], [$customer_route->id]), 'method' => 'PUT', 'id' => 'customer_route_edit_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'lang_v1.edit_customer_route' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('name', __( 'lang_v1.name' ) . ':*') !!}
        {!! Form::text('name', $customer_route->name, ['class' => 'form-control', 'required', 'placeholder' => __( 'lang_v1.name' ) ]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('parent_id', __( 'lang_v1.parent_route' ) . ':') !!}
        {!! Form::select('parent_id', $parent_routes, $customer_route->parent_id, ['class' => 'form-control', 'placeholder' => __('lang_v1.none')]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('description', __( 'lang_v1.description' ) . ':') !!}
        {!! Form::textarea('description', $customer_route->description, ['class' => 'form-control', 'rows' => 3, 'placeholder' => __( 'lang_v1.description' )]); !!}
      </div>

      <div class="form-group">
        <label>
          {!! Form::checkbox('is_active', 1, $customer_route->is_active, ['class' => 'input-icheck']); !!} @lang('lang_v1.is_active')
        </label>
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang( 'messages.update' )</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->