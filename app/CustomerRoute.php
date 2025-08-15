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
}