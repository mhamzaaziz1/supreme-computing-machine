<div class="modal-dialog" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action([\App\Http\Controllers\RouteSellerAssignmentController::class, 'update'], [$assignment->id]), 'method' => 'put', 'id' => 'route_assignment_add_form' ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('lang_v1.edit_route_assignment')</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                {!! Form::label('user_id', __('lang_v1.seller') . ':*') !!}
                {!! Form::select('user_id', $sellers, $assignment->user_id, ['class' => 'form-control select2', 'required', 'placeholder' => __('messages.please_select')]); !!}
            </div>
            <div class="form-group">
                {!! Form::label('customer_route_id', __('lang_v1.route') . ':*') !!}
                {!! Form::select('customer_route_id', $routes, $assignment->customer_route_id, ['class' => 'form-control select2', 'required', 'placeholder' => __('messages.please_select')]); !!}
            </div>
            <div class="form-group">
                {!! Form::label('assignment_date', __('lang_v1.assignment_date') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    {!! Form::text('assignment_date', $assignment->assignment_date ? $assignment->assignment_date->format('Y-m-d') : null, ['class' => 'form-control datepicker', 'readonly', 'placeholder' => __('lang_v1.select_a_date')]); !!}
                </div>
                <p class="help-block">@lang('lang_v1.leave_empty_for_permanent_assignment')</p>
            </div>
            <div class="form-group">
                {!! Form::label('is_active', __('lang_v1.status') . ':*') !!}
                {!! Form::select('is_active', ['1' => __('lang_v1.active'), '0' => __('lang_v1.inactive')], $assignment->is_active, ['class' => 'form-control select2', 'required']); !!}
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang('messages.update')</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
        </div>
        {!! Form::close() !!}
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
