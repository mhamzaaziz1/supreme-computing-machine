<div class="row">
    <div class="col-md-4">
        <div class="info-box bg-info">
            <span class="info-box-icon"><i class="fa fa-map-marker"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">@lang('lang_v1.total_outlets')</span>
                <span class="info-box-number">{{ $report_data['summary']['total_outlets'] }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-box bg-success">
            <span class="info-box-icon"><i class="fa fa-check"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">@lang('lang_v1.visited_outlets')</span>
                <span class="info-box-number">{{ $report_data['summary']['visited_outlets'] }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-box bg-danger">
            <span class="info-box-icon"><i class="fa fa-times"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">@lang('lang_v1.skipped_outlets')</span>
                <span class="info-box-number">{{ $report_data['summary']['skipped_outlets'] }}</span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="info-box bg-warning">
            <span class="info-box-icon"><i class="fa fa-random"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">@lang('lang_v1.out_of_sequence')</span>
                <span class="info-box-number">{{ $report_data['summary']['out_of_sequence'] }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="info-box bg-primary">
            <span class="info-box-icon"><i class="fa fa-percent"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">@lang('lang_v1.coverage_percentage')</span>
                <span class="info-box-number">{{ $report_data['summary']['coverage_percentage'] }}%</span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>@lang('lang_v1.sequence_number')</th>
                    <th>@lang('contact.contact')</th>
                    <th>@lang('lang_v1.was_visited')</th>
                    <th>@lang('lang_v1.in_sequence')</th>
                    <th>@lang('lang_v1.time_spent')</th>
                    <th>@lang('lang_v1.expected_start_time')</th>
                    <th>@lang('lang_v1.expected_end_time')</th>
                    <th>@lang('lang_v1.actual_visit_time')</th>
                    <th>@lang('lang_v1.actual_end_time')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report_data['outlets'] as $outlet)
                <tr class="{{ $outlet['was_visited'] ? '' : 'danger' }} {{ (!$outlet['in_sequence'] && $outlet['was_visited']) ? 'warning' : '' }}">
                    <td>{{ $outlet['sequence_number'] }}</td>
                    <td>{{ $outlet['outlet_name'] }}</td>
                    <td>{!! $outlet['was_visited'] ? '<span class="label label-success"><i class="fa fa-check"></i></span>' : '<span class="label label-danger"><i class="fa fa-times"></i></span>' !!}</td>
                    <td>{!! $outlet['in_sequence'] ? '<span class="label label-success"><i class="fa fa-check"></i></span>' : '<span class="label label-warning"><i class="fa fa-random"></i></span>' !!}</td>
                    <td>{{ $outlet['time_spent'] }} @lang('lang_v1.minutes')</td>
                    <td>{{ $outlet['expected_start_time'] ? \Carbon\Carbon::parse($outlet['expected_start_time'])->format('h:i A') : '-' }}</td>
                    <td>{{ $outlet['expected_end_time'] ? \Carbon\Carbon::parse($outlet['expected_end_time'])->format('h:i A') : '-' }}</td>
                    <td>{{ $outlet['actual_visit_time'] ? \Carbon\Carbon::parse($outlet['actual_visit_time'])->format('h:i A') : '-' }}</td>
                    <td>{{ $outlet['actual_end_time'] ? \Carbon\Carbon::parse($outlet['actual_end_time'])->format('h:i A') : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>