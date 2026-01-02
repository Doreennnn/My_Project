<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Booking;

class BookingConfirmation extends Notification
{
    use Queueable;

    protected $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $manageUrl = route('booking.manage', ['token' => $this->booking->booking_token]);

        return (new MailMessage)
            ->subject('Restaurant Booking Confirmation')
            ->greeting('Hello ' . $this->booking->customer_name . '!')
            ->line('Your table reservation has been confirmed.')
            ->line('**Booking Details:**')
            ->line('Date: ' . $this->booking->booking_date->format('F j, Y'))
            ->line('Time: ' . $this->booking->booking_time)
            ->line('Party Size: ' . $this->booking->party_size . ' guests')
            ->line('Table Preference: ' . ucfirst($this->booking->table_preference ?? 'No preference'))
            ->action('Manage Booking', $manageUrl)
            ->line('You can modify or cancel your booking using the link above.')
            ->line('Thank you for choosing our restaurant!');
    }
}
