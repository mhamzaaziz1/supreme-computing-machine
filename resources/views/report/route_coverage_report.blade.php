@extends('layouts.app')
@section('title', __('lang_v1.route_coverage_report'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('lang_v1.route_coverage_report')</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
                <div class="row">
                    <div class="col-md-12">
                        {!! Form::open(['url' => action([\App\Http\Controllers\ReportController::class, 'getRouteCoverageReport']), 'method' => 'get', 'id' => 'route_coverage_report_form']) !!}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('route_id', __('lang_v1.route') . ':') !!}
                                    {!! Form::select('route_id', $routes, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('date', __('messages.date') . ':') !!}
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                        {!! Form::text('date', null, ['class' => 'form-control datepicker', 'readonly', 'required', 'id' => 'date']); !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" style="margin-top: 25px;">
                                    <button type="button" class="btn btn-primary" id="apply_filter_btn" onclick="applyFilter()">
                                        <i class="fa fa-search"></i> @lang('report.apply_filters')
                                    </button>
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.route_coverage_report')])
                <div id="report_content">
                    <p class="text-center">@lang('lang_v1.please_select_route_date')</p>
                </div>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->
@stop

@push('scripts')
<script>
function applyFilter() {
    var form = document.getElementById('route_coverage_report_form');
    var route_id = document.getElementById('route_id').value;
    var date = document.getElementById('date').value;
    var btn = document.getElementById('apply_filter_btn');
    
    if (route_id && date) {
        btn.disabled = true;
        
        $.ajax({
            url: form.action,
            data: $(form).serialize(),
            dataType: 'html',
            success: function(data) {
                document.getElementById('report_content').innerHTML = data;
            },
            error: function() {
                alert('Something went wrong. Please try again.');
            },
            complete: function() {
                btn.disabled = false;
            }
        });
    } else {
        alert('Please select route and date');
    }
}
</script>
@endpush