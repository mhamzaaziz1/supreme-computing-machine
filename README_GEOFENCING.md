# Advanced Geofencing System Documentation

This document provides an overview of the advanced geofencing system implemented in the POS application.

## Table of Contents

1. [Overview](#overview)
2. [Key Features](#key-features)
3. [Database Schema](#database-schema)
4. [Configuration](#configuration)
5. [Usage Guide](#usage-guide)
6. [API Reference](#api-reference)
7. [Troubleshooting](#troubleshooting)

## Overview

The advanced geofencing system enables businesses to manage field sales operations more effectively by:

- Organizing outlets into logical routes
- Enforcing location-based restrictions for sales activities
- Tracking seller attendance and route check-ins
- Preventing fraud through location verification
- Generating comprehensive route coverage reports
- Applying zone/route-based restrictions
- Supporting offline operations

## Key Features

### 1. Route-based Categorization

Routes serve as the core organizational unit, grouping outlets (shops/customers) into logical paths:

- Each route consists of an ordered list of outlets
- Each outlet has its own geofence (radius or polygon)
- Sellers are assigned to one or more routes
- Sellers can only work with outlets in their assigned routes

### 2. Outlet Visit Enforcement

The system enforces that sellers must be physically present at an outlet to:

- Place an order
- Record payment/collection
- Mark a visit as completed

Optional proof of visit can be required:
- Geo-tagged photos
- One-time passwords (OTP) from retailers

### 3. Seller Attendance & Route Check-in

- Sellers must check in at the first outlet or route start point
- Check-in/out logs record seller ID, route ID, time, and coordinates
- The system prevents fake remote check-ins

### 4. Fraud Prevention

- Orders and payments are blocked if the seller's GPS location is outside any assigned outlet
- The system can detect GPS spoofing and mock-location apps
- GPS accuracy is verified (rejecting if accuracy > configured threshold)
- All "outside route attempts" are logged for auditing

### 5. Route Coverage Reporting

Comprehensive reporting includes:
- Planned vs. visited outlets
- Whether sequence was followed or skipped
- Time spent at each outlet
- Missed outlets flagged
- Heatmaps of visits per route

### 6. Zone/Route-based Restrictions

- Modules (returns, collections, etc.) can be enabled/disabled based on route
- Route-specific minimum order values or promotions can be configured
- Sellers choose warehouses/orders, but only within their assigned routes

### 7. Offline Capability

- Routes and outlet geofences are cached on the device
- Validation runs locally when offline
- Visit logs sync when the device comes back online

## Database Schema

The geofencing system uses the following database tables:

1. **contacts** - Extended with geofence data:
   - `latitude` - Outlet latitude
   - `longitude` - Outlet longitude
   - `geofence_type` - Type of geofence (radius or polygon)
   - `geofence_radius` - Radius in meters (for radius type)
   - `geofence_polygon` - JSON array of coordinates (for polygon type)

2. **customer_routes** - Defines routes:
   - Standard fields: id, name, description, etc.
   - Hierarchical structure with parent_id

3. **route_seller_assignments** - Links sellers to routes:
   - `user_id` - Reference to the seller
   - `customer_route_id` - Reference to the route
   - `is_active` - Whether the assignment is active

4. **route_visit_logs** - Records seller visits:
   - `user_id` - Reference to the seller
   - `customer_route_id` - Reference to the route
   - `contact_id` - Reference to the outlet
   - `visit_type` - Type of visit (check_in, check_out, visit_start, visit_end)
   - `latitude`, `longitude` - Coordinates
   - `accuracy` - GPS accuracy in meters
   - `visit_time` - Timestamp
   - `photo_url` - URL to geo-tagged photo (optional)
   - `otp` - One-time password from retailer (optional)

5. **geofence_violation_logs** - Records violations:
   - `user_id` - Reference to the seller
   - `violation_type` - Type of violation
   - `attempted_action` - Action that was attempted
   - `latitude`, `longitude` - Coordinates
   - `distance_from_valid` - Distance from nearest valid location

6. **route_outlet_sequence** - Defines the order of outlets in a route:
   - `customer_route_id` - Reference to the route
   - `contact_id` - Reference to the outlet
   - `sequence_number` - Order in the route
   - `expected_start_time`, `expected_end_time` - Scheduled times
   - `min_visit_duration` - Minimum required visit duration

7. **route_zone_restrictions** - Defines restrictions for routes:
   - `customer_route_id` - Reference to the route
   - Module toggles: `enable_returns`, `enable_collections`, etc.
   - `minimum_order_value`, `maximum_order_value` - Order limits
   - Time restrictions: `allowed_start_time`, `allowed_end_time`, `allowed_days`

## Configuration

Geofencing settings can be configured in `config/constants.php`:

```php
// Geofencing Constants
'max_gps_accuracy' => 100, // Maximum allowed GPS accuracy in meters
'default_geofence_radius' => 100, // Default radius for new geofences in meters
'min_visit_duration' => 5, // Minimum visit duration in minutes
'enable_mock_location_detection' => true, // Whether to detect and block mock locations
'enable_geofence_enforcement' => true, // Whether to enforce geofencing restrictions
'enable_route_sequence_enforcement' => false, // Whether to enforce route sequence
'enable_visit_proof' => false, // Whether to require proof of visit (photo/OTP)
```

## Usage Guide

### Setting Up Routes

1. Navigate to Customer Routes in the admin panel
2. Create a new route with a name and description
3. Add outlets to the route and specify their sequence
4. Set expected visit times and durations if needed

### Setting Up Geofences

1. Edit a contact/outlet
2. Navigate to the Geofence tab
3. Choose geofence type (radius or polygon)
4. For radius: set the radius in meters
5. For polygon: add points to create a boundary

### Assigning Routes to Sellers

1. Navigate to Route Assignments
2. Select a seller and the routes to assign
3. Save the assignments

### Monitoring Route Coverage

1. Navigate to Reports > Route Coverage
2. Select a route, seller, and date range
3. View the report showing planned vs. visited outlets
4. Check time spent at each outlet and sequence adherence

## API Reference

### GeofenceUtil Class

The `GeofenceUtil` class provides the core functionality for geofencing:

```php
// Check if an action is allowed at a specific location
$result = $geofenceUtil->isActionAllowed(
    $business_id, 
    $user_id, 
    $contact_id, 
    'place_order', 
    $latitude, 
    $longitude
);

// Log a visit
$visit = $geofenceUtil->logVisit(
    $business_id,
    $user_id,
    $customer_route_id,
    $contact_id,
    'visit_start',
    $latitude,
    $longitude
);

// Get route coverage report
$report = $geofenceUtil->getRouteCoverageReport(
    $business_id,
    $customer_route_id,
    $user_id,
    $date
);
```

## Troubleshooting

### Common Issues

1. **GPS Accuracy Too Low**
   - Ensure the device has a clear view of the sky
   - Wait for GPS to stabilize before attempting actions
   - Adjust the `max_gps_accuracy` setting if needed

2. **Outside Outlet Geofence**
   - Verify the outlet coordinates are correct
   - Check if the geofence radius is appropriate
   - Ensure the seller is physically at the outlet

3. **Mock Location Detected**
   - Ensure no mock location apps are running
   - Check device developer settings

4. **Offline Sync Issues**
   - Verify network connectivity
   - Check that offline data is being properly stored
   - Manually trigger sync if needed