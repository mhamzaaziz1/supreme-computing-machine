<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerRoute extends Model
{
    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Return list of customer routes for a business
     *
     * @param $business_id int
     * @param $prepend_none = true (boolean)
     * @param $prepend_all = false (boolean)
     * @return array
     */
    public static function forDropdown($business_id, $prepend_none = true, $prepend_all = false)
    {
        $all_routes = CustomerRoute::where('business_id', $business_id)
                                ->where('is_active', 1);
        $all_routes = $all_routes->pluck('name', 'id');

        //Prepend none
        if ($prepend_none) {
            $all_routes = $all_routes->prepend(__('lang_v1.none'), '');
        }

        //Prepend all
        if ($prepend_all) {
            $all_routes = $all_routes->prepend(__('report.all'), '');
        }

        return $all_routes;
    }

    /**
     * Get the parent route
     */
    public function parent()
    {
        return $this->belongsTo(CustomerRoute::class, 'parent_id');
    }

    /**
     * Get the child routes
     */
    public function children()
    {
        return $this->hasMany(CustomerRoute::class, 'parent_id');
    }

    /**
     * Get all customers in this route
     */
    public function customers()
    {
        return $this->hasMany(Contact::class, 'customer_route_id');
    }

    /**
     * Get the outlet sequence for this route
     */
    public function outletSequence()
    {
        return $this->hasMany(RouteOutletSequence::class, 'customer_route_id')
                    ->orderBy('sequence_number');
    }

    /**
     * Get the seller assignments for this route
     */
    public function sellerAssignments()
    {
        return $this->hasMany(RouteSellerAssignment::class, 'customer_route_id');
    }

    /**
     * Get the assigned sellers for this route
     */
    public function assignedSellers()
    {
        return $this->belongsToMany(User::class, 'route_seller_assignments', 'customer_route_id', 'user_id')
                    ->wherePivot('is_active', 1);
    }

    /**
     * Get the visit logs for this route
     */
    public function visitLogs()
    {
        return $this->hasMany(RouteVisitLog::class, 'customer_route_id');
    }

    /**
     * Get the violation logs for this route
     */
    public function violationLogs()
    {
        return $this->hasMany(GeofenceViolationLog::class, 'customer_route_id');
    }

    /**
     * Get the zone restrictions for this route
     */
    public function zoneRestrictions()
    {
        return $this->hasOne(RouteZoneRestriction::class, 'customer_route_id');
    }

    /**
     * Get the followups for this route
     */
    public function followups()
    {
        return $this->hasMany(RouteFollowup::class, 'customer_route_id');
    }
}
