<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RouteSellerAssignment extends Model
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
        'is_active' => 'boolean',
        'assignment_date' => 'date',
    ];

    /**
     * Get the business that owns the assignment.
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the user (seller) associated with the assignment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the customer route associated with the assignment.
     */
    public function customerRoute()
    {
        return $this->belongsTo(CustomerRoute::class);
    }

    /**
     * Get the user who created the assignment.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope a query to only include active assignments.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Get all routes assigned to a seller
     *
     * @param int $business_id
     * @param int $user_id
     * @param string|null $date Optional date to filter assignments (format: Y-m-d)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAssignedRoutes($business_id, $user_id, $date = null)
    {
        $query = self::where('business_id', $business_id)
            ->where('user_id', $user_id)
            ->where('is_active', 1);

        // If date is provided, filter by that date
        if (!empty($date)) {
            $query->where('assignment_date', $date);
        } else {
            // If no date is provided, get assignments with no date or today's date
            $query->where(function($q) {
                $q->whereNull('assignment_date')
                  ->orWhere('assignment_date', date('Y-m-d'));
            });
        }

        return $query->with('customerRoute')
            ->get()
            ->pluck('customerRoute');
    }
}
