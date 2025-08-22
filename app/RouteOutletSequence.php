<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RouteOutletSequence extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'route_outlet_sequence';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'expected_start_time' => 'datetime',
        'expected_end_time' => 'datetime',
        'min_visit_duration' => 'integer',
    ];

    /**
     * Get the business that owns the sequence.
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the customer route associated with the sequence.
     */
    public function customerRoute()
    {
        return $this->belongsTo(CustomerRoute::class);
    }

    /**
     * Get the contact (outlet) associated with the sequence.
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get all outlets in a route in sequence order
     *
     * @param int $business_id
     * @param int $customer_route_id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getRouteOutlets($business_id, $customer_route_id)
    {
        return self::where('business_id', $business_id)
            ->where('customer_route_id', $customer_route_id)
            ->orderBy('sequence_number')
            ->with('contact')
            ->get();
    }

    /**
     * Get the next outlet in the route sequence
     *
     * @param int $business_id
     * @param int $customer_route_id
     * @param int $current_sequence_number
     * @return \App\RouteOutletSequence|null
     */
    public static function getNextOutlet($business_id, $customer_route_id, $current_sequence_number)
    {
        return self::where('business_id', $business_id)
            ->where('customer_route_id', $customer_route_id)
            ->where('sequence_number', '>', $current_sequence_number)
            ->orderBy('sequence_number')
            ->with('contact')
            ->first();
    }

    /**
     * Get the previous outlet in the route sequence
     *
     * @param int $business_id
     * @param int $customer_route_id
     * @param int $current_sequence_number
     * @return \App\RouteOutletSequence|null
     */
    public static function getPreviousOutlet($business_id, $customer_route_id, $current_sequence_number)
    {
        return self::where('business_id', $business_id)
            ->where('customer_route_id', $customer_route_id)
            ->where('sequence_number', '<', $current_sequence_number)
            ->orderBy('sequence_number', 'desc')
            ->with('contact')
            ->first();
    }

    /**
     * Get the sequence number for a specific outlet in a route
     *
     * @param int $business_id
     * @param int $customer_route_id
     * @param int $contact_id
     * @return int|null
     */
    public static function getOutletSequenceNumber($business_id, $customer_route_id, $contact_id)
    {
        $sequence = self::where('business_id', $business_id)
            ->where('customer_route_id', $customer_route_id)
            ->where('contact_id', $contact_id)
            ->first();
            
        return $sequence ? $sequence->sequence_number : null;
    }
}