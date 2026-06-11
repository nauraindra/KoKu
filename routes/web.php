<?php

use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\Tenant\BookingController;
use App\Http\Controllers\Tenant\PaymentHistoryController;
use App\Http\Controllers\Tenant\ProfileController;
use App\Http\Controllers\Tenant\RoomBrowseController;
use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware(['auth', 'track.visit'])->group(function () {

    Route::get('/weather/current', [WeatherController::class, 'current'])
        ->name('weather.current');

    Route::post('/preferences', [PreferenceController::class, 'update'])
        ->name('preferences.update');

    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            Route::get('/dashboard', [DashboardController::class, 'admin'])
                ->name('dashboard');

            Route::resource('/tenants', TenantController::class);

            Route::get('/tenants-search', [TenantController::class, 'search'])
                ->name('tenants.search');

            Route::resource('/rooms', RoomController::class);

            Route::resource('/payments', PaymentController::class);

            Route::patch('/payments/{payment}/paid', [PaymentController::class, 'markAsPaid'])
                ->name('payments.paid');

            Route::patch('/payments/{payment}/reject', [PaymentController::class, 'reject'])
                ->name('payments.reject');
        });

    Route::middleware('role:penyewa')
        ->prefix('tenant')
        ->name('tenant.')
        ->group(function () {

            Route::get('/dashboard', [DashboardController::class, 'tenant'])
                ->name('dashboard');

            Route::get('/rooms', [RoomBrowseController::class, 'index'])
                ->name('rooms.index');

            Route::get('/rooms/{room}', [RoomBrowseController::class, 'show'])
                ->name('rooms.show');

            Route::post('/rooms/{room}/booking', [BookingController::class, 'store'])
                ->name('rooms.booking');

            Route::get('/payments', [PaymentHistoryController::class, 'index'])
                ->name('payments.index');

            Route::post('/payments/{payment}/pay', [PaymentHistoryController::class, 'pay'])
                ->name('payments.pay');

            Route::get('/profile', [ProfileController::class, 'edit'])
                ->name('profile.edit');

            Route::put('/profile', [ProfileController::class, 'update'])
                ->name('profile.update');
        });
});

require __DIR__.'/auth.php';
