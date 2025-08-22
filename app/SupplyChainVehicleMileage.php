<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupplyChainVehicleMileage extends Model
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
    protected $table = 'supply_chain_vehicle_mileage';

    /**
     * Get the last mileage record for a specific supply chain vehicle
     *
     * @param int $business_id
     * @param int $supply_chain_vehicle_id
     * @return SupplyChainVehicleMileage|null
     */
    public static function getLastMileageRecord($business_id, $supply_chain_vehicle_id)
    {
        return self::where('business_id', $business_id)
                ->where('supply_chain_vehicle_id', $supply_chain_vehicle_id)
                ->latest()
                ->first();
    }

    /**
     * Get the daily travel distance
     *
     * @return int
     */
    public function getDailyTravelDistance()
    {
        return $this->end_mileage - $this->start_mileage;
    }

    /**
     * Get the business that owns the mileage record.
     */
    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }

    /**
     * Get the supply chain vehicle associated with the mileage record.
     */
    public function supplyChainVehicle()
    {
        return $this->belongsTo(\App\SupplyChainVehicle::class, 'supply_chain_vehicle_id');
    }

    /**
     * Get the user who created the mileage record.
     */
    public function createdBy()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }
}