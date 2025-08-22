@extends('layouts.app')
@section('title', __('lang_v1.route_followup_report'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('lang_v1.route_followup_report')</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
                {!! Form::open(['url' => action('\App\Http\Controllers\ReportController@getRouteFollowupReport'), 'method' => 'get', 'id' => 'route_followup_report_form']) !!}
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('route_ids', __('lang_v1.routes') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-map-marker"></i>
                            </span>
                            {!! Form::select('route_ids[]', $routes, null, ['class' => 'form-control select2', 'multiple', 'id' => 'route_ids', 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('start_date', __('report.start_date') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            {!! Form::text('start_date', $start_date, ['class' => 'form-control datepicker', 'readonly', 'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('end_date', __('report.end_date') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            {!! Form::text('end_date', $end_date, ['class' => 'form-control datepicker', 'readonly', 'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary pull-right">
                            <i class="fa fa-search"></i> @lang('report.apply_filters')
                        </button>
                    </div>
                </div>
                {!! Form::close() !!}
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.route_followup_report')])
                @if(isset($followed_customers) && isset($not_followed_customers))
                    @include('report.partials.route_followup_details')
                @else
                    <p class="text-center">@lang('lang_v1.please_select_filters')</p>
                @endif
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->
@stop
