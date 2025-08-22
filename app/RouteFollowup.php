<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RouteFollowup extends Model
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
        'followup_date' => 'datetime',
    ];

    /**
     * Get the business that owns the followup.
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the customer route associated with the followup.
     */
    public function customerRoute()
    {
        return $this->belongsTo(CustomerRoute::class);
    }

    /**
     * Get the contact (customer) associated with the followup.
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the user (seller) associated with the followup.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Return list of followups for a business
     *
     * @param int $business_id
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function forBusiness($business_id, $filters = [])
    {
        $query = self::where('business_id', $business_id);

        if (!empty($filters['customer_route_id'])) {
            $query->where('customer_route_id', $filters['customer_route_id']);
        }

        if (!empty($filters['contact_id'])) {
            $query->where('contact_id', $filters['contact_id']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('followup_date', [$filters['start_date'], $filters['end_date']]);
        }

        return $query;
    }
}