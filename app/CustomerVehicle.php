<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerVehicle extends Model
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
    protected $table = 'customer_vehicles';

    /**
     * Return list of vehicles for a customer
     *
     * @param $business_id int
     * @param $contact_id int
     * @return array
     */
    public static function forDropdown($business_id, $contact_id)
    {
        $vehicles = CustomerVehicle::where('business_id', $business_id)
                    ->where('contact_id', $contact_id)
                    ->select('id', 'make', 'model', 'year', 'license_plate');

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
     * Get the contact that owns the vehicle.
     */
    public function contact()
    {
        return $this->belongsTo(\App\Contact::class);
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
