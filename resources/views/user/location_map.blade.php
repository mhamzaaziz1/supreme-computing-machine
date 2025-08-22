@extends('layouts.app')
@section('title', __('lang_v1.active_users_location'))

@section('content')
    @php
        $api_key = config('services.google_maps.api_key');
    @endphp
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black"> @lang('lang_v1.active_users_location')
    </h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-solid'])
        {!! Form::open(['url' => action([\App\Http\Controllers\UserLocationController::class, 'index']), 'method' => 'get', 'id' => 'user_filter_form']) !!}
            <div class="col-md-6">
                <div class="form-group">
                    <label for="users">@lang('lang_v1.select_users')</label>
                    <select id="users" class="form-control" name="users[]" multiple="">
                    </select>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-primary">@lang('messages.submit')</button>
            </div>
        {!! Form::close() !!}
    @endcomponent
    @component('components.widget', ['class' => 'box-solid'])
        <div class="row">
            <div class="col-md-12">
                <button type="button" class="btn btn-primary pull-right" id="update_location">
                    <i class="fa fa-map-marker"></i> @lang('lang_v1.update_my_location')
                </button>
            </div>
        </div>
        <div class="row mt-15">
            <div class="col-md-12">
                <div id="map" style="height: 450px;"></div>
            </div>
        </div>
    @endcomponent

</section>
<!-- /.content -->
@stop
@section('javascript')
    @if(!empty($api_key))
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{$api_key}}&callback=initMap"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            var users = {!! json_encode($all_users->toArray()) !!};
            var data = $.map(users, function (obj) {
                obj.text = obj.surname + ' ' + obj.first_name + ' ' + (obj.last_name || ''); 
                obj.id = obj.id;
                return obj;
            });
            $('#users').select2({
                data: data,
                templateResult: function (data) { 
                    var template = data.text;
                    return template;
                },
                escapeMarkup: function(markup) {
                    return markup;
                },
            });
            @if(!empty(request()->input('users')))
                $('#users').val([{{implode(',', request()->input('users'))}}]).change();
            @endif

            // Update user's location when button is clicked
            $('#update_location').click(function() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        var latitude = position.coords.latitude;
                        var longitude = position.coords.longitude;
                        
                        $.ajax({
                            url: "{{ action([\App\Http\Controllers\UserLocationController::class, 'updateLocation']) }}",
                            method: 'POST',
                            data: {
                                latitude: latitude,
                                longitude: longitude,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                if (response.success) {
                                    toastr.success("@lang('lang_v1.location_updated_successfully')");
                                    // Refresh the map
                                    fetchActiveUsersLocations();
                                }
                            },
                            error: function(xhr) {
                                toastr.error("@lang('messages.something_went_wrong')");
                            }
                        });
                    }, function(error) {
                        toastr.error("@lang('lang_v1.geolocation_error'): " + error.message);
                    });
                } else {
                    toastr.error("@lang('lang_v1.geolocation_not_supported')");
                }
            });

            // Fetch active users' locations every 30 seconds
            setInterval(fetchActiveUsersLocations, 30000);
        });

        var map;
        var markers = [];

        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 10,
                center: {lat: -33.9, lng: 151.2}
            });

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    initialLocation = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                    map.setCenter(initialLocation);
                });
            }

            fetchActiveUsersLocations();
        }

        function fetchActiveUsersLocations() {
            var selectedUsers = $('#users').val();
            var url = "{{ action([\App\Http\Controllers\UserLocationController::class, 'getActiveUsersLocations']) }}";
            
            if (selectedUsers && selectedUsers.length > 0) {
                url += '?users=' + selectedUsers.join(',');
            }

            $.ajax({
                url: url,
                method: 'GET',
                success: function(response) {
                    // Clear existing markers
                    clearMarkers();
                    
                    // Add new markers
                    for (var i = 0; i < response.length; i++) {
                        var user = response[i];
                        var fullName = user.surname + ' ' + user.first_name + ' ' + (user.last_name || '');
                        var lastUpdated = new Date(user.location_updated_at).toLocaleString();
                        
                        var marker = new google.maps.Marker({
                            position: {lat: parseFloat(user.latitude), lng: parseFloat(user.longitude)},
                            map: map,
                            title: fullName + '\nLast updated: ' + lastUpdated
                        });
                        
                        markers.push(marker);
                    }
                },
                error: function(xhr) {
                    console.error('Error fetching user locations:', xhr);
                }
            });
        }

        function clearMarkers() {
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(null);
            }
            markers = [];
        }
    </script>
    @endif
@endsection