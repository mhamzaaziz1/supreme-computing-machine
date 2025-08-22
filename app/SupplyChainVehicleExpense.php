<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupplyChainVehicleExpense extends Model
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
    protected $table = 'supply_chain_vehicle_expenses';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:4',
    ];

    /**
     * Get the supply chain vehicle associated with the expense.
     */
    public function supplyChainVehicle()
    {
        return $this->belongsTo(\App\SupplyChainVehicle::class, 'supply_chain_vehicle_id');
    }

    /**
     * Get the mileage record associated with the expense (if any).
     */
    public function mileageRecord()
    {
        return $this->belongsTo(\App\SupplyChainVehicleMileage::class, 'supply_chain_vehicle_mileage_id');
    }

    /**
     * Get the business that owns the expense record.
     */
    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }

    /**
     * Get the user who created the expense record.
     */
    public function createdBy()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }

    /**
     * Get expense types for dropdown.
     *
     * @return array
     */
    public static function expenseTypes()
    {
        return [
            'fuel' => __('lang_v1.fuel'),
            'maintenance' => __('lang_v1.maintenance'),
            'repair' => __('lang_v1.repair'),
            'toll' => __('lang_v1.toll'),
            'parking' => __('lang_v1.parking'),
            'fine' => __('lang_v1.fine'),
            'insurance' => __('lang_v1.insurance'),
            'tax' => __('lang_v1.tax'),
            'other' => __('lang_v1.other'),
        ];
    }
}