<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantSetting extends Model
{
    protected $fillable = [
        'date',
        'time_slot',
        'capacity',
        'booked',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get available capacity for this time slot
     */
    public function getAvailableCapacityAttribute()
    {
        return $this->capacity - $this->booked;
    }

    /**
     * Check if time slot is fully booked
     */
    public function isFullyBooked()
    {
        return $this->booked >= $this->capacity;
    }

    /**
     * Get or create setting for a specific date and time
     */
    public static function getOrCreateSetting($date, $timeSlot, $defaultCapacity = 50)
    {
        return static::firstOrCreate(
            [
                'date' => $date,
                'time_slot' => $timeSlot,
            ],
            [
                'capacity' => $defaultCapacity,
                'booked' => 0,
            ]
        );
    }
}
