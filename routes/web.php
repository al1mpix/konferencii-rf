<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware(['auth'])
    ->name('logout.user');

// Handles logout for the admin session, which does not use the standard user guard.
Route::post('/admin/logout', [AuthController::class, 'logout'])
    ->middleware('admin')
    ->name('logout.admin');

Route::middleware('auth')->group(function () {
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::post('/bookings/{booking}/review', [BookingController::class, 'review'])->name('bookings.review');
});

Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    Route::get('/bookings', [AdminController::class, 'index'])->name('index');
    Route::patch('/bookings/{booking}/status', [AdminController::class, 'updateStatus'])->name('status.update');
});
