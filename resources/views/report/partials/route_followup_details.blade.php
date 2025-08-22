<!-- Statistics Section -->
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3 class="box-title">@lang('lang_v1.route_coverage_statistics')</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box bg-aqua">
                            <span class="info-box-icon"><i class="fa fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">@lang('lang_v1.total_assigned')</span>
                                <span class="info-box-number">{{ $stats['total_assigned'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-green">
                            <span class="info-box-icon"><i class="fa fa-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">@lang('lang_v1.followed')</span>
                                <span class="info-box-number">{{ $stats['followed'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-red">
                            <span class="info-box-icon"><i class="fa fa-times"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">@lang('lang_v1.not_followed')</span>
                                <span class="info-box-number">{{ $stats['not_followed'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-yellow">
                            <span class="info-box-icon"><i class="fa fa-percent"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">@lang('lang_v1.coverage_percentage')</span>
                                <span class="info-box-number">{{ $stats['coverage_percentage'] ?? 0 }}%</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if(!empty($stats['route_stats']))
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h4>@lang('lang_v1.route_wise_statistics')</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="route_stats_table">
                                <thead>
                                    <tr>
                                        <th>@lang('lang_v1.route')</th>
                                        <th>@lang('lang_v1.total_assigned')</th>
                                        <th>@lang('lang_v1.followed')</th>
                                        <th>@lang('lang_v1.not_followed')</th>
                                        <th>@lang('lang_v1.coverage_percentage')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['route_stats'] as $route_id => $route_stat)
                                        <tr>
                                            <td>{{ $route_stat['route_name'] }}</td>
                                            <td>{{ $route_stat['total_assigned'] }}</td>
                                            <td>{{ $route_stat['followed'] }}</td>
                                            <td>{{ $route_stat['not_followed'] }}</td>
                                            <td>{{ $route_stat['coverage_percentage'] }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Followed Customers Section -->
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3 class="box-title">@lang('lang_v1.followed_customers')</h3>
            </div>
            <div class="box-body">
                @if(count($followed_customers) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="followed_customers_table">
                            <thead>
                                <tr>
                                    <th>@lang('lang_v1.date')</th>
                                    <th>@lang('lang_v1.route')</th>
                                    <th>@lang('lang_v1.customer')</th>
                                    <th>@lang('lang_v1.address')</th>
                                    <th>@lang('lang_v1.phone')</th>
                                    <th>@lang('lang_v1.followup_details')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($followed_customers as $customer)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($customer->followup_date)->format('Y-m-d') }}</td>
                                        <td>{{ $customer->route_name }}</td>
                                        <td>
                                            @if(!empty($customer->supplier_business_name))
                                                {{ $customer->supplier_business_name }} - 
                                            @endif
                                            {{ $customer->contact_name }}
                                        </td>
                                        <td>
                                            @php
                                                $address_parts = [];
                                                if(!empty($customer->address_line_1)) $address_parts[] = $customer->address_line_1;
                                                if(!empty($customer->address_line_2)) $address_parts[] = $customer->address_line_2;
                                                if(!empty($customer->city)) $address_parts[] = $customer->city;
                                                if(!empty($customer->state)) $address_parts[] = $customer->state;
                                                if(!empty($customer->country)) $address_parts[] = $customer->country;
                                                if(!empty($customer->land_mark)) $address_parts[] = $customer->land_mark;
                                                echo implode(', ', $address_parts);
                                            @endphp
                                        </td>
                                        <td>{{ $customer->mobile }}</td>
                                        <td>{{ $customer->notes }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        @lang('lang_v1.no_followed_customers_found')
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Not Followed Customers Section -->
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3 class="box-title">@lang('lang_v1.not_followed_customers')</h3>
            </div>
            <div class="box-body">
                @if(count($not_followed_customers) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="not_followed_customers_table">
                            <thead>
                                <tr>
                                    <th>@lang('lang_v1.date')</th>
                                    <th>@lang('lang_v1.route')</th>
                                    <th>@lang('lang_v1.customer')</th>
                                    <th>@lang('lang_v1.address')</th>
                                    <th>@lang('lang_v1.phone')</th>
                                    <th>@lang('lang_v1.followup_details')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($not_followed_customers as $customer)
                                    <tr>
                                        <td>{{ isset($start_date) ? $start_date : \Carbon\Carbon::now()->format('Y-m-d') }}</td>
                                        <td>{{ $customer->route_name }}</td>
                                        <td>
                                            @if(!empty($customer->supplier_business_name))
                                                {{ $customer->supplier_business_name }} - 
                                            @endif
                                            {{ $customer->contact_name }}
                                        </td>
                                        <td>
                                            @php
                                                $address_parts = [];
                                                if(!empty($customer->address_line_1)) $address_parts[] = $customer->address_line_1;
                                                if(!empty($customer->address_line_2)) $address_parts[] = $customer->address_line_2;
                                                if(!empty($customer->city)) $address_parts[] = $customer->city;
                                                if(!empty($customer->state)) $address_parts[] = $customer->state;
                                                if(!empty($customer->country)) $address_parts[] = $customer->country;
                                                if(!empty($customer->land_mark)) $address_parts[] = $customer->land_mark;
                                                echo implode(', ', $address_parts);
                                            @endphp
                                        </td>
                                        <td>{{ $customer->mobile }}</td>
                                        <td>@lang('lang_v1.not_followed_up')</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        @lang('lang_v1.no_not_followed_customers_found')
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#route_stats_table, #followed_customers_table, #not_followed_customers_table').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    });
</script>
