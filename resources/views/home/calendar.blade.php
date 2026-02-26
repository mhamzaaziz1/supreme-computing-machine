@extends('layouts.app')
@section('title', __('lang_v1.calendar'))

@section('content')

<style>
    /* Custom Calendar Styling */
    .fc-view-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        padding: 20px;
    }
    
    .fc-head .fc-widget-header {
        border: none !important;
        padding: 10px 0;
        text-transform: uppercase;
        font-size: 12px;
        color: #6b7280;
        font-weight: 600;
        background-color: white;
    }
    
    .fc-day-grid-container {
        border-bottom: 1px solid #e5e7eb;
        border-right: 1px solid #e5e7eb;
    }

    .fc td, .fc th {
        border: 1px solid #f3f4f6 !important;
    }

    .fc-day-number {
        color: #9ca3af;
        padding: 8px !important;
        font-size: 14px;
    }

    .fc-today {
        background-color: #f9fafb !important;
    }

    .fc-event {
        border: none !important;
        border-radius: 12px !important;
        padding: 2px 8px !important;
        font-size: 12px !important;
        margin: 2px 0 !important;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }

    .fc-event .fc-content {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: white !important;
    }
    
    /* Custom Controls Styling */
    .calendar-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        background: transparent;
    }

    .calendar-btn-group {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .calendar-btn {
        background: white;
        border: 1px solid #e5e7eb;
        color: #374151;
        padding: 6px 16px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }

    .calendar-btn:hover {
        background-color: #f9fafb;
        border-color: #d1d5db;
    }

    .calendar-btn.active {
        background-color: #1f2937;
        color: white;
        border-color: #1f2937;
    }

    .calendar-title {
        font-size: 24px;
        font-weight: 600;
        color: #111827;
        margin: 0;
    }
    
    .nav-btn {
        padding: 6px 12px;
        color: #6b7280;
    }
    
    .filter-drawer {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        display: none;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
</style>

<!-- Content Header (Page header) -->
<section class="content-header" style="padding-bottom: 0;">
    <!-- <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang( 'lang_v1.calendar' )</h1> -->
</section>

<!-- Main content -->
<section class="content">

    <!-- Custom Controls Header -->
    <div class="calendar-controls">
        <div class="calendar-btn-group">
            <button class="calendar-btn" id="cal-today">today</button>
            <button class="calendar-btn nav-btn" id="cal-prev"><i class="fa fa-chevron-left"></i></button>
            <button class="calendar-btn nav-btn" id="cal-next"><i class="fa fa-chevron-right"></i></button>
        </div>

        <h2 class="calendar-title" id="cal-title">January 2026</h2>

        <div class="calendar-btn-group">
            <div class="btn-group" role="group">
                <button class="calendar-btn active view-btn" data-view="month">month</button>
                <button class="calendar-btn view-btn" data-view="agendaWeek">week</button>
                <button class="calendar-btn view-btn" data-view="agendaDay">day</button>
            </div>
            <button class="calendar-btn" id="toggle-filter">Filter by</button>
        </div>
    </div>

    <!-- Filter Drawer (Hidden by default) -->
    <div class="filter-drawer" id="calendar-filters">
        <div class="row">
            @if(!empty($users))
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('user_id', __('role.user') . ':') !!}
                        {!! Form::select('user_id', $users, auth()->user()->id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'style' => 'width:100%']); !!}
                    </div>
                </div>
            @endif
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('location_id', __('sale.location') . ':') !!}
                    {!! Form::select('location_id', $all_locations, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-6">
                 <div class="form-group">
                    <label>@lang('lang_v1.event_type'):</label>
                    <div class="row">
                    @foreach($event_types as $key => $value)
                        <div class="col-md-4">
                            <label class="checkbox-inline">
                                {!! Form::checkbox('events', $key, true, [ 'class' => 'input-icheck event_check']); !!} 
                                <span style="color: {{$value['color']}}; font-weight: 500;">{{ $value['label'] }}</span>
                            </label>
                        </div>
                    @endforeach
                    </div>
                </div>
            </div>
            
            @if(Module::has('Essentials'))
            <div class="col-md-12 mt-3">
                <button class="tw-dw-btn tw-dw-btn-success tw-text-white tw-dw-btn-sm btn-modal" 
                    data-href="{{action([\Modules\Essentials\Http\Controllers\ToDoController::class, 'create'])}}?from_calendar=true" 
                    data-container="#task_modal">
                    <i class="fa fa-plus"></i> @lang( 'essentials::lang.add_to_do' )</a>
                </button>
            </div>
            @endif
        </div>
    </div>

    <!-- Calendar Container -->
    <div class="row">
        <div class="col-sm-12">
             <div id="calendar"></div>
        </div>
    </div>

</section>
<!-- /.content -->

@endsection

@section('javascript')
    
    <script type="text/javascript">
        $(document).ready(function(){
            var events = [];
            $.each($("input[name='events']:checked"), function(){
                events.push($(this).val());
            });

            // Initialize FullCalendar
            $('#calendar').fullCalendar({
                header: false, // Hide default header
                defaultView: 'month',
                contentHeight: 'auto',
                eventLimit: 3,
                eventSources: [
                    {
                        url: '/calendar', 
                        type: 'get',
                        data: {
                            events: events
                        }
                    }
                ],
                viewRender: function(view, element) {
                    // Update the custom title
                    $('#cal-title').text(view.title);
                    
                    // Update active view button
                    $('.view-btn').removeClass('active');
                    $('.view-btn[data-view="' + view.name + '"]').addClass('active');
                },
                eventRender: function (event, element) {
                    if (event.title_html) {
                        element.find('.fc-title').html(event.title_html);
                    }
                    if (event.event_url) {
                        element.attr('href', event.event_url);
                    }
                    // Apply tooltip or other enhancements here if needed
                }
            });

            // Custom Controls Logic
            $('#cal-prev').click(function() {
                $('#calendar').fullCalendar('prev');
            });

            $('#cal-next').click(function() {
                $('#calendar').fullCalendar('next');
            });

            $('#cal-today').click(function() {
                $('#calendar').fullCalendar('today');
            });

            $('.view-btn').click(function() {
                var viewName = $(this).data('view');
                $('#calendar').fullCalendar('changeView', viewName);
            });

            // Filter Toggle
            $('#toggle-filter').click(function() {
                $('#calendar-filters').slideToggle();
                $(this).toggleClass('active');
            });
        });

        // Filter Logic (Reload Calendar)
        $(document).on('change', '#user_id, #location_id', function(){
            reload_calendar();
        });

        $(document).on('ifChanged', '.event_check', function(){
            reload_calendar();
        });

        function reload_calendar(){
            data = [];
            if($('select#location_id').length) {
                data.location_id = $('select#location_id').val();
            }
            if($('select#user_id').length) {
                data.user_id = $('select#user_id').val();
            }

            var events = [];
            $.each($("input[name='events']:checked"), function(){
                events.push($(this).val());
            });

            data.events = events;

            var events_source = {
                url: '/calendar',
                type: 'get',
                data: data
            }
            $('#calendar').fullCalendar( 'removeEventSource', events_source);
            $('#calendar').fullCalendar( 'addEventSource', events_source);
        }
    </script>
@endsection
