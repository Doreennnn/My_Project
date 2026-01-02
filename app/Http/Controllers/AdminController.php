<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\RestaurantSetting;
use App\Models\BlackoutDate;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Admin Dashboard - Daily view of reservations
     */
    public function dashboard(Request $request)
    {
        $date = $request->get('date', Carbon::today()->toDateString());
        
        $bookings = Booking::whereDate('booking_date', $date)
            ->orderBy('booking_time')
            ->get();

        $stats = [
            'total' => $bookings->count(),
            'confirmed' => $bookings->where('status', 'confirmed')->count(),
            'pending' => $bookings->where('status', 'pending')->count(),
            'seated' => $bookings->where('status', 'seated')->count(),
            'completed' => $bookings->where('status', 'completed')->count(),
            'no_show' => $bookings->where('status', 'no-show')->count(),
            'cancelled' => $bookings->where('status', 'cancelled')->count(),
        ];

        return view('admin.dashboard', compact('bookings', 'date', 'stats'));
    }

    /**
     * Show form to create manual booking
     */
    public function createBooking()
    {
        return view('admin.create-booking');
    }

    /**
     * Store manual booking
     */
    public function storeBooking(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255|regex:/^[a-zA-Z ]+$/',
            'email' => 'required|email',
            'phone' => 'required|string|regex:/^[0-9]{10,15}$/',
            'booking_date' => 'required|date',
            'booking_time' => 'required',
            'party_size' => 'required|integer|min:1|max:20',
            'table_preference' => 'nullable|in:indoor,outdoor,window,high-top',
            'special_requests' => 'nullable|string|max:500',
        ]);

        // Check capacity before creating booking
        $setting = RestaurantSetting::getOrCreateSetting(
            $validated['booking_date'],
            $validated['booking_time']
        );

        $bookedGuests = Booking::where('booking_date', $validated['booking_date'])
            ->where('booking_time', $validated['booking_time'])
            ->whereNotIn('status', ['cancelled', 'no-show'])
            ->sum('party_size');

        if (($bookedGuests + $validated['party_size']) > $setting->capacity) {
            return back()->withErrors([
                'party_size' => 'Cannot create booking. This would exceed the maximum capacity of ' . $setting->capacity . ' guests for this time slot. Currently booked: ' . $bookedGuests . ' guests.'
            ])->withInput();
        }

        $validated['booking_token'] = \Str::random(32);
        $validated['status'] = 'confirmed';

        Booking::create($validated);

        return redirect()->route('admin.dashboard')->with('success', 'Booking created successfully!');
    }

    /**
     * Update booking status
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,seated,completed,cancelled,no-show',
        ]);

        $booking->update($validated);

        return back()->with('success', 'Booking status updated successfully!');
    }

    /**
     * Capacity management
     */
    public function capacitySettings(Request $request)
    {
        $query = RestaurantSetting::where('capacity', '!=', 50);
        
        // Apply date filter if provided
        if ($request->has('filter_date') && $request->filter_date) {
            $query->where('date', $request->filter_date);
        }
        
        $settings = $query->orderBy('date', 'desc')
            ->orderBy('time_slot', 'desc')
            ->limit(20)
            ->get();

        return view('admin.capacity-settings', compact('settings'));
    }

    /**
     * Update capacity for multiple time slots
     */
    public function updateCapacity(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'time_slots' => 'required|array|min:1',
            'time_slots.*' => 'required|string',
            'capacity' => 'required|integer|min:1|max:80',
        ]);

        $updated = 0;
        foreach ($validated['time_slots'] as $timeSlot) {
            RestaurantSetting::updateOrCreate(
                [
                    'date' => $validated['date'],
                    'time_slot' => $timeSlot,
                ],
                [
                    'capacity' => $validated['capacity'],
                ]
            );
            $updated++;
        }

        return back()->with('success', "Capacity updated for {$updated} time slot(s) successfully!");
    }

    /**
     * View all bookings
     */
    public function allBookings(Request $request)
    {
        $query = Booking::query();

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('date') && $request->date != '') {
            $query->whereDate('booking_date', $request->date);
        }

        // Handle sorting
        $sortField = $request->get('sort', 'booking_date');
        $sortDirection = $request->get('direction', 'desc');
        
        // Validate sort field to prevent SQL injection
        $allowedSorts = ['booking_date', 'booking_time', 'customer_name', 'status', 'created_at'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('booking_date', 'desc');
        }
        
        // Add secondary sort for consistency
        if ($sortField != 'booking_time') {
            $query->orderBy('booking_time', 'desc');
        }

        $bookings = $query->paginate(20);

        return view('admin.all-bookings', compact('bookings'));
    }

    /**
     * Show blackout dates management
     */
    public function blackoutDates()
    {
        $blackoutDates = BlackoutDate::orderBy('date', 'asc')->get();
        return view('admin.blackout-dates', compact('blackoutDates'));
    }

    /**
     * Store new blackout date
     */
    public function storeBlackoutDate(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date|after:yesterday|unique:blackout_dates,date',
            'reason' => 'nullable|string|max:255',
        ]);

        BlackoutDate::create($validated);

        return back()->with('success', 'Blackout date added successfully!');
    }

    /**
     * Delete blackout date
     */
    public function deleteBlackoutDate(BlackoutDate $blackoutDate)
    {
        $blackoutDate->delete();
        return back()->with('success', 'Blackout date removed successfully!');
    }
}
