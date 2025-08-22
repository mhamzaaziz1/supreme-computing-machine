<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeofenceViolationLog extends Model
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
        'latitude' => 'float',
        'longitude' => 'float',
        'accuracy' => 'float',
        'distance_from_valid' => 'float',
        'is_mock_location' => 'boolean',
    ];

    /**
     * Get the business that owns the violation log.
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the user (seller) associated with the violation log.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the customer route associated with the violation log.
     */
    public function customerRoute()
    {
        return $this->belongsTo(CustomerRoute::class);
    }

    /**
     * Get the contact (outlet) associated with the violation log.
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Scope a query to only include outside route violations.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOutsideRoute($query)
    {
        return $query->where('violation_type', 'outside_route');
    }

    /**
     * Scope a query to only include outside outlet violations.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOutsideOutlet($query)
    {
        return $query->where('violation_type', 'outside_outlet');
    }

    /**
     * Scope a query to only include mock location violations.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMockLocation($query)
    {
        return $query->where('violation_type', 'mock_location');
    }

    /**
     * Scope a query to only include accuracy too low violations.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAccuracyTooLow($query)
    {
        return $query->where('violation_type', 'accuracy_too_low');
    }

    /**
     * Get violation counts by type for a user
     *
     * @param int $business_id
     * @param int $user_id
     * @param string $date
     * @return array
     */
    public static function getViolationCountsByType($business_id, $user_id, $date = null)
    {
        $query = self::where('business_id', $business_id)
            ->where('user_id', $user_id);
            
        if ($date) {
            $query->whereDate('created_at', $date);
        }
        
        return $query->selectRaw('violation_type, count(*) as count')
            ->groupBy('violation_type')
            ->pluck('count', 'violation_type')
            ->toArray();
    }
}