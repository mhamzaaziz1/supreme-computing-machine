<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleMileageRecord extends Model
{
    use HasFactory;

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
    protected $table = 'vehicle_mileage_records';

    /**
     * Get the last mileage record for a specific vehicle
     *
     * @param int $business_id
     * @param int $vehicle_id
     * @return VehicleMileageRecord|null
     */
    public static function getLastMileageRecord($business_id, $vehicle_id)
    {
        return self::where('business_id', $business_id)
                ->where('vehicle_id', $vehicle_id)
                ->latest()
                ->first();
    }

    /**
     * Get the business that owns the mileage record.
     */
    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }

    /**
     * Get the customer that owns the mileage record.
     */
    public function customer()
    {
        return $this->belongsTo(\App\Contact::class, 'customer_id');
    }

    /**
     * Get the vehicle associated with the mileage record.
     */
    public function vehicle()
    {
        return $this->belongsTo(\App\CustomerVehicle::class, 'vehicle_id');
    }

    /**
     * Get the invoice associated with the mileage record.
     */
    public function invoice()
    {
        return $this->belongsTo(\App\Transaction::class, 'invoice_id');
    }
}
