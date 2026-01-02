<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;

// Public Routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/menu', function () {
    return view('menu');
})->name('menu');

// Customer Booking Routes
Route::get('/book', [BookingController::class, 'create'])->name('booking.create');
Route::post('/book', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/available-slots', [BookingController::class, 'getAvailableSlots'])->name('booking.available-slots');
Route::get('/booking/confirmation/{token}', [BookingController::class, 'confirmation'])->name('booking.confirmation');
Route::get('/booking/manage/{token}', [BookingController::class, 'manage'])->name('booking.manage');
Route::post('/booking/update/{token}', [BookingController::class, 'update'])->name('booking.update');
Route::post('/booking/cancel/{token}', [BookingController::class, 'cancel'])->name('booking.cancel');

// Admin Authentication Routes
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Protected Admin Routes
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/bookings', [AdminController::class, 'allBookings'])->name('admin.bookings');
    Route::get('/bookings/create', [AdminController::class, 'createBooking'])->name('admin.bookings.create');
    Route::post('/bookings/create', [AdminController::class, 'storeBooking'])->name('admin.bookings.store');
    Route::post('/bookings/{booking}/status', [AdminController::class, 'updateStatus'])->name('admin.bookings.status');
    Route::get('/capacity', [AdminController::class, 'capacitySettings'])->name('admin.capacity');
    Route::post('/capacity', [AdminController::class, 'updateCapacity'])->name('admin.capacity.update');
    Route::get('/blackout-dates', [AdminController::class, 'blackoutDates'])->name('admin.blackout');
    Route::post('/blackout-dates', [AdminController::class, 'storeBlackoutDate'])->name('admin.blackout.store');
    Route::delete('/blackout-dates/{blackoutDate}', [AdminController::class, 'deleteBlackoutDate'])->name('admin.blackout.delete');
});
