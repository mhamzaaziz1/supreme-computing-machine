<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RouteVisitLog extends Model
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
        'visit_time' => 'datetime',
        'is_mock_location' => 'boolean',
    ];

    /**
     * Get the business that owns the visit log.
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the user (seller) associated with the visit log.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the customer route associated with the visit log.
     */
    public function customerRoute()
    {
        return $this->belongsTo(CustomerRoute::class);
    }

    /**
     * Get the contact (outlet) associated with the visit log.
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Scope a query to only include check-ins.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCheckIns($query)
    {
        return $query->where('visit_type', 'check_in');
    }

    /**
     * Scope a query to only include check-outs.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCheckOuts($query)
    {
        return $query->where('visit_type', 'check_out');
    }

    /**
     * Scope a query to only include visit starts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisitStarts($query)
    {
        return $query->where('visit_type', 'visit_start');
    }

    /**
     * Scope a query to only include visit ends.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisitEnds($query)
    {
        return $query->where('visit_type', 'visit_end');
    }

    /**
     * Get the time spent at an outlet
     *
     * @param int $business_id
     * @param int $user_id
     * @param int $contact_id
     * @param string $date
     * @return int Time in minutes
     */
    public static function getTimeSpentAtOutlet($business_id, $user_id, $contact_id, $date)
    {
        $visits = self::where('business_id', $business_id)
            ->where('user_id', $user_id)
            ->where('contact_id', $contact_id)
            ->whereDate('visit_time', $date)
            ->orderBy('visit_time')
            ->get();

        $total_minutes = 0;
        $start_time = null;

        foreach ($visits as $visit) {
            if ($visit->visit_type == 'visit_start') {
                $start_time = $visit->visit_time;
            } elseif ($visit->visit_type == 'visit_end' && $start_time) {
                $total_minutes += $start_time->diffInMinutes($visit->visit_time);
                $start_time = null;
            }
        }

        return $total_minutes;
    }
}