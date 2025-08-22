<?php

namespace App\Utils;

use App\Contact;
use App\CustomerRoute;
use App\GeofenceViolationLog;
use App\RouteOutletSequence;
use App\RouteSellerAssignment;
use App\RouteVisitLog;
use App\RouteZoneRestriction;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GeofenceUtil extends Util
{
    /**
     * Check if a user is allowed to perform an action at a specific location
     *
     * @param int $business_id
     * @param int $user_id
     * @param int $contact_id
     * @param string $action (place_order, record_payment, mark_visit_done, check_in)
     * @param float $latitude
     * @param float $longitude
     * @param float $accuracy
     * @param string $device_id
     * @param bool $is_mock_location
     * @return array ['is_allowed' => bool, 'message' => string, 'violation_type' => string|null]
     */
    public function isActionAllowed($business_id, $user_id, $contact_id, $action, $latitude, $longitude, $accuracy = null, $device_id = null, $is_mock_location = false)
    {
        $user = User::find($user_id);
        $contact = Contact::find($contact_id);

        if (!$user || !$contact) {
            return [
                'is_allowed' => false,
                'message' => __('lang_v1.user_or_contact_not_found'),
                'violation_type' => null
            ];
        }

        // Check if mock location is being used
        if ($is_mock_location) {
            $this->logViolation(
                $business_id,
                $user_id,
                null,
                $contact_id,
                'mock_location',
                $action,
                $latitude,
                $longitude,
                $accuracy,
                null,
                $device_id,
                $is_mock_location,
                'Mock location detected'
            );

            return [
                'is_allowed' => false,
                'message' => __('lang_v1.mock_location_detected'),
                'violation_type' => 'mock_location'
            ];
        }

        // Check if GPS accuracy is too low
        if ($accuracy !== null && $accuracy > config('constants.max_gps_accuracy', 100)) {
            $this->logViolation(
                $business_id,
                $user_id,
                null,
                $contact_id,
                'accuracy_too_low',
                $action,
                $latitude,
                $longitude,
                $accuracy,
                null,
                $device_id,
                $is_mock_location,
                'GPS accuracy too low: ' . $accuracy . ' meters'
            );

            return [
                'is_allowed' => false,
                'message' => __('lang_v1.gps_accuracy_too_low', ['accuracy' => $accuracy]),
                'violation_type' => 'accuracy_too_low'
            ];
        }

        // Get assigned routes for the user
        $assigned_routes = RouteSellerAssignment::where('business_id', $business_id)
            ->where('user_id', $user_id)
            ->where('is_active', 1)
            ->pluck('customer_route_id')
            ->toArray();

        // Check if contact belongs to any of the user's assigned routes
        $contact_route_id = $contact->customer_route_id;
        if (!in_array($contact_route_id, $assigned_routes)) {
            $this->logViolation(
                $business_id,
                $user_id,
                $contact_route_id,
                $contact_id,
                'outside_route',
                $action,
                $latitude,
                $longitude,
                $accuracy,
                null,
                $device_id,
                $is_mock_location,
                'Contact not in assigned routes'
            );

            return [
                'is_allowed' => false,
                'message' => __('lang_v1.contact_not_in_assigned_routes'),
                'violation_type' => 'outside_route'
            ];
        }

        // Check if user is inside the outlet's geofence
        if (!$this->isInsideGeofence($latitude, $longitude, $contact)) {
            // Calculate distance from outlet
            $distance = $this->calculateDistance(
                $latitude,
                $longitude,
                $contact->latitude,
                $contact->longitude
            );

            $this->logViolation(
                $business_id,
                $user_id,
                $contact_route_id,
                $contact_id,
                'outside_outlet',
                $action,
                $latitude,
                $longitude,
                $accuracy,
                $distance,
                $device_id,
                $is_mock_location,
                'Outside outlet geofence, distance: ' . round($distance) . ' meters'
            );

            return [
                'is_allowed' => false,
                'message' => __('lang_v1.outside_outlet_geofence', ['distance' => round($distance)]),
                'violation_type' => 'outside_outlet'
            ];
        }

        // Check if action is allowed based on route restrictions
        $route = CustomerRoute::find($contact_route_id);
        $restrictions = $route->zoneRestrictions;

        if ($restrictions) {
            // Check time restrictions
            if ($restrictions->allowed_start_time && $restrictions->allowed_end_time) {
                $now = Carbon::now();
                $start = Carbon::parse($restrictions->allowed_start_time);
                $end = Carbon::parse($restrictions->allowed_end_time);

                if ($now->lt($start) || $now->gt($end)) {
                    return [
                        'is_allowed' => false,
                        'message' => __('lang_v1.outside_allowed_hours', [
                            'start' => $start->format('h:i A'),
                            'end' => $end->format('h:i A')
                        ]),
                        'violation_type' => 'time_restriction'
                    ];
                }
            }

            // Check day restrictions
            if ($restrictions->allowed_days) {
                $current_day = Carbon::now()->dayOfWeek;
                if (!in_array($current_day, $restrictions->allowed_days)) {
                    return [
                        'is_allowed' => false,
                        'message' => __('lang_v1.not_allowed_on_this_day'),
                        'violation_type' => 'day_restriction'
                    ];
                }
            }

            // Check module restrictions
            if ($action == 'record_payment' && !$restrictions->enable_collections) {
                return [
                    'is_allowed' => false,
                    'message' => __('lang_v1.collections_disabled_for_route'),
                    'violation_type' => 'module_restriction'
                ];
            }
        }

        return [
            'is_allowed' => true,
            'message' => __('lang_v1.action_allowed'),
            'violation_type' => null
        ];
    }

    /**
     * Log a visit to an outlet
     *
     * @param int $business_id
     * @param int $user_id
     * @param int $customer_route_id
     * @param int $contact_id
     * @param string $visit_type (check_in, check_out, visit_start, visit_end)
     * @param float $latitude
     * @param float $longitude
     * @param float $accuracy
     * @param string $notes
     * @param string $photo_url
     * @param string $otp
     * @param string $device_id
     * @param bool $is_mock_location
     * @return \App\RouteVisitLog
     */
    public function logVisit($business_id, $user_id, $customer_route_id, $contact_id, $visit_type, $latitude, $longitude, $accuracy = null, $notes = null, $photo_url = null, $otp = null, $device_id = null, $is_mock_location = false)
    {
        return RouteVisitLog::create([
            'business_id' => $business_id,
            'user_id' => $user_id,
            'customer_route_id' => $customer_route_id,
            'contact_id' => $contact_id,
            'visit_type' => $visit_type,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'accuracy' => $accuracy,
            'visit_time' => Carbon::now(),
            'notes' => $notes,
            'photo_url' => $photo_url,
            'otp' => $otp,
            'device_id' => $device_id,
            'is_mock_location' => $is_mock_location
        ]);
    }

    /**
     * Log a geofence violation
     *
     * @param int $business_id
     * @param int $user_id
     * @param int $customer_route_id
     * @param int $contact_id
     * @param string $violation_type
     * @param string $attempted_action
     * @param float $latitude
     * @param float $longitude
     * @param float $accuracy
     * @param float $distance_from_valid
     * @param string $device_id
     * @param bool $is_mock_location
     * @param string $details
     * @return \App\GeofenceViolationLog
     */
    public function logViolation($business_id, $user_id, $customer_route_id, $contact_id, $violation_type, $attempted_action, $latitude, $longitude, $accuracy = null, $distance_from_valid = null, $device_id = null, $is_mock_location = false, $details = null)
    {
        return GeofenceViolationLog::create([
            'business_id' => $business_id,
            'user_id' => $user_id,
            'customer_route_id' => $customer_route_id,
            'contact_id' => $contact_id,
            'violation_type' => $violation_type,
            'attempted_action' => $attempted_action,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'accuracy' => $accuracy,
            'distance_from_valid' => $distance_from_valid,
            'device_id' => $device_id,
            'is_mock_location' => $is_mock_location,
            'details' => $details
        ]);
    }

    /**
     * Check if a point is inside a geofence
     *
     * @param float $latitude
     * @param float $longitude
     * @param \App\Contact $outlet
     * @return bool
     */
    public function isInsideGeofence($latitude, $longitude, $outlet)
    {
        // If outlet has no geofence, return false
        if (empty($outlet->geofence_type)) {
            return false;
        }

        // Check based on geofence type
        if ($outlet->geofence_type == 'radius') {
            // Calculate distance between user and outlet
            $distance = $this->calculateDistance(
                $latitude, 
                $longitude, 
                $outlet->latitude, 
                $outlet->longitude
            );

            // Check if distance is within radius
            return $distance <= $outlet->geofence_radius;
        } elseif ($outlet->geofence_type == 'polygon') {
            // Check if point is inside polygon
            return $this->isPointInPolygon(
                $latitude, 
                $longitude, 
                $outlet->geofence_polygon
            );
        }

        return false;
    }

    /**
     * Calculate distance between two points in meters
     *
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @return float Distance in meters
     */
    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        // Earth radius in meters
        $earth_radius = 6371000;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earth_radius * $c;

        return $distance;
    }

    /**
     * Check if a point is inside a polygon
     *
     * @param float $lat
     * @param float $lon
     * @param array $polygon Array of [lat, lng] points
     * @return bool
     */
    public function isPointInPolygon($lat, $lon, $polygon)
    {
        if (empty($polygon)) {
            return false;
        }

        $vertices_count = count($polygon);
        if ($vertices_count < 3) {
            return false;
        }

        $inside = false;
        for ($i = 0, $j = $vertices_count - 1; $i < $vertices_count; $j = $i++) {
            $xi = $polygon[$i][0];
            $yi = $polygon[$i][1];
            $xj = $polygon[$j][0];
            $yj = $polygon[$j][1];

            $intersect = (($yi > $lat) != ($yj > $lat)) &&
                ($lon < ($xj - $xi) * ($lat - $yi) / ($yj - $yi) + $xi);

            if ($intersect) {
                $inside = !$inside;
            }
        }

        return $inside;
    }

    /**
     * Get route coverage report
     *
     * @param int $business_id
     * @param int $customer_route_id
     * @param int|null $user_id
     * @param string $date
     * @return array
     */
    public function getRouteCoverageReport($business_id, $customer_route_id, $user_id = null, $date)
    {
        // Get all outlets in the route
        $outlets = RouteOutletSequence::where('business_id', $business_id)
            ->where('customer_route_id', $customer_route_id)
            ->orderBy('sequence_number')
            ->with('contact')
            ->get();

        // Get all visits for the route on the given date
        $query = RouteVisitLog::where('business_id', $business_id)
            ->where('customer_route_id', $customer_route_id)
            ->whereDate('visit_time', $date);

        // Filter by user_id if provided
        if (!is_null($user_id)) {
            $query->where('user_id', $user_id);
        }

        $visits = $query->get();

        $report = [];
        $total_outlets = 0;
        $visited_outlets = 0;
        $skipped_outlets = 0;
        $out_of_sequence = 0;

        foreach ($outlets as $outlet) {
            $total_outlets++;

            $outlet_visits = $visits->where('contact_id', $outlet->contact_id);
            $visit_start = $outlet_visits->where('visit_type', 'visit_start')->first();
            $visit_end = $outlet_visits->where('visit_type', 'visit_end')->first();

            $time_spent = 0;
            if ($visit_start && $visit_end) {
                $time_spent = $visit_start->visit_time->diffInMinutes($visit_end->visit_time);
            }

            $was_visited = $outlet_visits->count() > 0;
            if ($was_visited) {
                $visited_outlets++;
            } else {
                $skipped_outlets++;
            }

            // Check if outlet was visited in sequence
            $in_sequence = true;
            if ($was_visited && $outlet->sequence_number > 1) {
                $prev_outlet = $outlets->where('sequence_number', $outlet->sequence_number - 1)->first();
                if ($prev_outlet) {
                    $prev_outlet_visits = $visits->where('contact_id', $prev_outlet->contact_id);
                    $prev_visited = $prev_outlet_visits->count() > 0;

                    if (!$prev_visited) {
                        $in_sequence = false;
                        $out_of_sequence++;
                    } else {
                        // Check if previous outlet was visited before this one
                        $prev_visit_time = $prev_outlet_visits->max('visit_time');
                        $current_visit_time = $outlet_visits->min('visit_time');

                        if ($prev_visit_time > $current_visit_time) {
                            $in_sequence = false;
                            $out_of_sequence++;
                        }
                    }
                }
            }

            $report[] = [
                'outlet_id' => $outlet->contact_id,
                'outlet_name' => $outlet->contact->name,
                'sequence_number' => $outlet->sequence_number,
                'was_visited' => $was_visited,
                'in_sequence' => $in_sequence,
                'time_spent' => $time_spent,
                'expected_start_time' => $outlet->expected_start_time,
                'expected_end_time' => $outlet->expected_end_time,
                'min_visit_duration' => $outlet->min_visit_duration,
                'actual_visit_time' => $visit_start ? $visit_start->visit_time : null,
                'actual_end_time' => $visit_end ? $visit_end->visit_time : null,
            ];
        }

        return [
            'outlets' => $report,
            'summary' => [
                'total_outlets' => $total_outlets,
                'visited_outlets' => $visited_outlets,
                'skipped_outlets' => $skipped_outlets,
                'out_of_sequence' => $out_of_sequence,
                'coverage_percentage' => $total_outlets > 0 ? round(($visited_outlets / $total_outlets) * 100) : 0,
            ]
        ];
    }
}
