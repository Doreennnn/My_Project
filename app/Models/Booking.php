<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Booking extends Model
{
    use Notifiable;

    protected $fillable = [
        'customer_name',
        'email',
        'phone',
        'booking_date',
        'booking_time',
        'party_size',
        'table_preference',
        'special_requests',
        'booking_token',
        'status',
        'cancelled_at',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Boot method to generate unique token
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_token)) {
                $booking->booking_token = Str::random(32);
            }
        });
    }

    /**
     * Scope for active bookings (not cancelled)
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['cancelled']);
    }

    /**
     * Scope for today's bookings
     */
    public function scopeToday($query)
    {
        return $query->whereDate('booking_date', today());
    }

    /**
     * Check if booking can be modified
     */
    public function canBeModified()
    {
        return !in_array($this->status, ['completed', 'cancelled', 'no-show'])
            && $this->booking_date >= today();
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'confirmed' => 'success',
            'seated' => 'info',
            'completed' => 'secondary',
            'cancelled' => 'danger',
            'no-show' => 'dark',
            default => 'primary',
        };
    }

    /**
     * Route notifications for mail channel
     */
    public function routeNotificationForMail()
    {
        return $this->email;
    }
}

