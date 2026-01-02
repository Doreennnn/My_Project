<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\RestaurantSetting;
use App\Models\BlackoutDate;
use App\Notifications\BookingConfirmation;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Show the Restaurant Booking Form page
     */
    public function create()
    {
        $blackoutDates = BlackoutDate::pluck('date')->map(function($date) {
            return $date->format('Y-m-d');
        })->toArray();
        
        return view('bookings.create', compact('blackoutDates'));
    }

    /**
     * Get available time slots for a specific date
     */
    public function getAvailableSlots(Request $request)
    {
        $date = $request->get('date');
        $partySize = $request->get('party_size', 1);

        if (!$date) {
            return response()->json(['error' => 'Date is required'], 400);
        }

        $timeSlots = $this->generateTimeSlots();
        $availableSlots = [];
        $now = now();
        $selectedDate = \Carbon\Carbon::parse($date);
        $isToday = $selectedDate->isToday();

        foreach ($timeSlots as $slot) {
            // If booking for today, check if slot is at least 6 hours from now
            if ($isToday) {
                $slotDateTime = \Carbon\Carbon::parse($date . ' ' . $slot);
                // Skip if the slot is less than 6 hours away or already passed
                if ($now->copy()->addHours(6)->greaterThan($slotDateTime)) {
                    continue;
                }
            }

            $setting = RestaurantSetting::getOrCreateSetting($date, $slot);
            
            // Get current bookings for this slot
            $bookedGuests = Booking::where('booking_date', $date)
                ->where('booking_time', $slot)
                ->whereNotIn('status', ['cancelled', 'no-show'])
                ->sum('party_size');

            $availableCapacity = $setting->capacity - $bookedGuests;

            if ($availableCapacity >= $partySize) {
                $availableSlots[] = [
                    'time' => $slot,
                    'available' => $availableCapacity,
                ];
            }
        }

        return response()->json($availableSlots);
    }

    /**
     * Generate standard time slots for the restaurant
     */
    private function generateTimeSlots()
    {
        return [
            '11:00', '11:30',
            '12:00', '12:30',
            '13:00', '13:30',
            '14:00', '14:30',
            '17:00', '17:30',
            '18:00', '18:30',
            '19:00', '19:30',
            '20:00', '20:30',
            '21:00', '21:30',
        ];
    }

    /**
     * Save the restaurant booking to MySQL
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'customer_name' => 'required|string|max:255|regex:/^[a-zA-Z ]+$/',
            'email' => 'required|email',
            'phone' => 'required|string|regex:/^[0-9]{10,15}$/',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required',
            'party_size' => 'required|integer|min:1|max:8',
            'table_preference' => 'nullable|in:indoor,outdoor,window,high-top',
            'special_requests' => 'nullable|string|max:500',
        ]);

        // Check if booking is at least 6 hours in advance
        $bookingDateTime = \Carbon\Carbon::parse($validatedData['booking_date'] . ' ' . $validatedData['booking_time']);
        $hoursDifference = now()->diffInHours($bookingDateTime, false);
        
        if ($hoursDifference < 6) {
            return back()->withErrors([
                'booking_time' => 'Bookings must be made at least 6 hours in advance. Please select a different time slot.'
            ])->withInput();
        }

        // Check if date is a blackout date
        $isBlackout = BlackoutDate::where('date', $validatedData['booking_date'])->exists();
        if ($isBlackout) {
            return back()->withErrors([
                'booking_date' => 'Sorry, the restaurant is closed on this date. Please select another date.'
            ])->withInput();
        }

        // Check availability
        $setting = RestaurantSetting::getOrCreateSetting(
            $validatedData['booking_date'],
            $validatedData['booking_time']
        );

        $bookedGuests = Booking::where('booking_date', $validatedData['booking_date'])
            ->where('booking_time', $validatedData['booking_time'])
            ->whereNotIn('status', ['cancelled', 'no-show'])
            ->sum('party_size');

        if (($bookedGuests + $validatedData['party_size']) > $setting->capacity) {
            return back()->withErrors([
                'booking_time' => 'Sorry, this time slot is no longer available. Maximum capacity: ' . $setting->capacity . ' guests. Currently booked: ' . $bookedGuests . ' guests. Please choose another time slot.'
            ])->withInput();
        }

        // Create the booking with pending status
        $validatedData['booking_token'] = \Str::random(32);
        $validatedData['status'] = 'pending';
        $booking = Booking::create($validatedData);

        // Send confirmation email
        try {
            $booking->notify(new BookingConfirmation($booking));
        } catch (\Exception $e) {
            // Log error but don't fail the booking
            \Log::error('Failed to send booking confirmation: ' . $e->getMessage());
        }

        return redirect()->route('booking.confirmation', ['token' => $booking->booking_token]);
    }

    /**
     * Show booking confirmation page
     */
    public function confirmation($token)
    {
        $booking = Booking::where('booking_token', $token)->firstOrFail();
        return view('bookings.confirmation', compact('booking'));
    }

    /**
     * Show booking management page (modify/cancel)
     */
    public function manage($token)
    {
        $booking = Booking::where('booking_token', $token)->firstOrFail();
        
        if (!$booking->canBeModified()) {
            return view('bookings.manage', [
                'booking' => $booking,
                'canModify' => false,
                'message' => 'This booking cannot be modified.',
            ]);
        }

        return view('bookings.manage', [
            'booking' => $booking,
            'canModify' => true,
        ]);
    }

    /**
     * Update booking
     */
    public function update(Request $request, $token)
    {
        $booking = Booking::where('booking_token', $token)->firstOrFail();

        if (!$booking->canBeModified()) {
            return back()->withErrors(['error' => 'This booking cannot be modified.']);
        }

        $validatedData = $request->validate([
            'booking_date' => 'required|date|after:today',
            'booking_time' => 'required',
            'party_size' => 'required|integer|min:1|max:8',
            'table_preference' => 'nullable|in:indoor,outdoor,window,high-top',
            'special_requests' => 'nullable|string|max:500',
        ]);

        // Check availability for new date/time
        $setting = RestaurantSetting::getOrCreateSetting(
            $validatedData['booking_date'],
            $validatedData['booking_time']
        );

        $bookedGuests = Booking::where('booking_date', $validatedData['booking_date'])
            ->where('booking_time', $validatedData['booking_time'])
            ->where('id', '!=', $booking->id)
            ->whereNotIn('status', ['cancelled', 'no-show'])
            ->sum('party_size');

        if (($bookedGuests + $validatedData['party_size']) > $setting->capacity) {
            return back()->withErrors([
                'booking_time' => 'Sorry, this time slot is no longer available. Maximum capacity: ' . $setting->capacity . ' guests. Currently booked: ' . $bookedGuests . ' guests. Please choose another time slot.'
            ])->withInput();
        }

        $booking->update($validatedData);

        return redirect()->route('booking.manage', ['token' => $token])
            ->with('success', 'Booking updated successfully!');
    }

    /**
     * Cancel booking
     */
    public function cancel($token)
    {
        $booking = Booking::where('booking_token', $token)->firstOrFail();

        if (!$booking->canBeModified()) {
            return back()->withErrors(['error' => 'This booking cannot be cancelled.']);
        }

        $booking->update([
            'status' => 'cancelled',
        ]);

        return redirect()->route('booking.manage', ['token' => $token])
            ->with('success', 'Booking cancelled successfully.');
    }
}
