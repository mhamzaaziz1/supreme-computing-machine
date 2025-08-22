<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RouteZoneRestriction extends Model
{
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
        'enable_returns' => 'boolean',
        'enable_collections' => 'boolean',
        'enable_discounts' => 'boolean',
        'enable_credit_sales' => 'boolean',
        'minimum_order_value' => 'float',
        'maximum_order_value' => 'float',
        'allowed_promotions' => 'array',
        'allowed_start_time' => 'datetime',
        'allowed_end_time' => 'datetime',
        'allowed_days' => 'array',
    ];

    /**
     * Get the business that owns the restriction.
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the customer route associated with the restriction.
     */
    public function customerRoute()
    {
        return $this->belongsTo(CustomerRoute::class);
    }

    /**
     * Check if a specific module is enabled for a route
     *
     * @param int $business_id
     * @param int $customer_route_id
     * @param string $module_name (returns, collections, discounts, credit_sales)
     * @return bool
     */
    public static function isModuleEnabled($business_id, $customer_route_id, $module_name)
    {
        $restriction = self::where('business_id', $business_id)
            ->where('customer_route_id', $customer_route_id)
            ->first();
            
        if (!$restriction) {
            return true; // If no restrictions are set, all modules are enabled by default
        }
        
        $field_name = 'enable_' . $module_name;
        return $restriction->$field_name;
    }

    /**
     * Check if the current time is within allowed hours for a route
     *
     * @param int $business_id
     * @param int $customer_route_id
     * @return bool
     */
    public static function isWithinAllowedHours($business_id, $customer_route_id)
    {
        $restriction = self::where('business_id', $business_id)
            ->where('customer_route_id', $customer_route_id)
            ->first();
            
        if (!$restriction || !$restriction->allowed_start_time || !$restriction->allowed_end_time) {
            return true; // If no time restrictions are set, all times are allowed
        }
        
        $now = now();
        $start_time = $restriction->allowed_start_time;
        $end_time = $restriction->allowed_end_time;
        
        // Convert to time only for comparison
        $current_time = $now->format('H:i:s');
        $start = $start_time->format('H:i:s');
        $end = $end_time->format('H:i:s');
        
        return $current_time >= $start && $current_time <= $end;
    }

    /**
     * Check if the current day is allowed for a route
     *
     * @param int $business_id
     * @param int $customer_route_id
     * @return bool
     */
    public static function isAllowedDay($business_id, $customer_route_id)
    {
        $restriction = self::where('business_id', $business_id)
            ->where('customer_route_id', $customer_route_id)
            ->first();
            
        if (!$restriction || !$restriction->allowed_days) {
            return true; // If no day restrictions are set, all days are allowed
        }
        
        $current_day = now()->dayOfWeek; // 0 (Sunday) to 6 (Saturday)
        return in_array($current_day, $restriction->allowed_days);
    }

    /**
     * Check if an order value is within allowed limits for a route
     *
     * @param int $business_id
     * @param int $customer_route_id
     * @param float $order_value
     * @return bool
     */
    public static function isOrderValueAllowed($business_id, $customer_route_id, $order_value)
    {
        $restriction = self::where('business_id', $business_id)
            ->where('customer_route_id', $customer_route_id)
            ->first();
            
        if (!$restriction) {
            return true; // If no restrictions are set, all order values are allowed
        }
        
        $min_check = true;
        $max_check = true;
        
        if ($restriction->minimum_order_value !== null) {
            $min_check = $order_value >= $restriction->minimum_order_value;
        }
        
        if ($restriction->maximum_order_value !== null) {
            $max_check = $order_value <= $restriction->maximum_order_value;
        }
        
        return $min_check && $max_check;
    }

    /**
     * Check if a promotion is allowed for a route
     *
     * @param int $business_id
     * @param int $customer_route_id
     * @param int $promotion_id
     * @return bool
     */
    public static function isPromotionAllowed($business_id, $customer_route_id, $promotion_id)
    {
        $restriction = self::where('business_id', $business_id)
            ->where('customer_route_id', $customer_route_id)
            ->first();
            
        if (!$restriction || !$restriction->allowed_promotions) {
            return true; // If no promotion restrictions are set, all promotions are allowed
        }
        
        return in_array($promotion_id, $restriction->allowed_promotions);
    }
}