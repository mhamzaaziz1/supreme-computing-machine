<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupplyChainVehicle extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'supply_chain_vehicles';

    /**
     * Return list of vehicles for a customer route
     *
     * @param $business_id int
     * @param $customer_route_id int|null
     * @return array
     */
    public static function forDropdown($business_id, $customer_route_id = null)
    {
        $vehicles = SupplyChainVehicle::where('business_id', $business_id);

        // Filter by customer_route_id only if it's provided
        if (!is_null($customer_route_id)) {
            $vehicles->where('customer_route_id', $customer_route_id);
        }

        $vehicles = $vehicles->select('id', 'make', 'model', 'year', 'license_plate');

        $formatted_vehicles = $vehicles->get()->mapWithKeys(function ($vehicle) {
            $display_name = $vehicle->make . ' ' . $vehicle->model;
            if (!empty($vehicle->year)) {
                $display_name .= ' (' . $vehicle->year . ')';
            }
            if (!empty($vehicle->license_plate)) {
                $display_name .= ' - ' . $vehicle->license_plate;
            }
            return [$vehicle->id => $display_name];
        });

        return $formatted_vehicles;
    }

    /**
     * Get the customer route that owns the vehicle.
     */
    public function customerRoute()
    {
        return $this->belongsTo(\App\CustomerRoute::class, 'customer_route_id');
    }

    /**
     * Get the business that owns the vehicle record.
     */
    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }

    /**
     * Get the user who created the vehicle record.
     */
    public function createdBy()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }
}
