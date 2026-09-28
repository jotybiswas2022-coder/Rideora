<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VehicleController as AdminVehicleController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware('admin')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        /*
        |----------------------------------------------------------------------
        | Vehicles
        |----------------------------------------------------------------------
        */
        Route::get('vehicles', [AdminVehicleController::class, 'index'])->name('vehicles.index');
        Route::get('vehicles/create', [AdminVehicleController::class, 'create'])->name('vehicles.create');
        Route::post('vehicles', [AdminVehicleController::class, 'store'])->name('vehicles.store');
        Route::get('vehicles/{vehicle}', [AdminVehicleController::class, 'show'])->name('vehicles.show');
        Route::get('vehicles/{vehicle}/edit', [AdminVehicleController::class, 'edit'])->name('vehicles.edit');
        Route::put('vehicles/{vehicle}', [AdminVehicleController::class, 'update'])->name('vehicles.update');
        Route::delete('vehicles/{vehicle}', [AdminVehicleController::class, 'destroy'])->name('vehicles.destroy');
        Route::post('vehicles/{vehicle}/status', [AdminVehicleController::class, 'toggleStatus'])->name('vehicles.status');
        Route::post('vehicles/{vehicle}/images/{image}/primary', [AdminVehicleController::class, 'makePrimaryImage'])
            ->name('vehicles.image.primary');

        /*
        |----------------------------------------------------------------------
        | Categories
        |----------------------------------------------------------------------
        */
        Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        /*
        |----------------------------------------------------------------------
        | Bookings
        |----------------------------------------------------------------------
        */
        Route::get('bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
        Route::get('bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
        Route::put('bookings/{booking}', [AdminBookingController::class, 'update'])->name('bookings.update');
        Route::post('bookings/{booking}/status', [AdminBookingController::class, 'quickStatus'])->name('bookings.status');
        Route::delete('bookings/{booking}', [AdminBookingController::class, 'destroy'])->name('bookings.destroy');

        /*
        |----------------------------------------------------------------------
        | Payments (manual verification)
        |----------------------------------------------------------------------
        */
        Route::get('payments', [AdminPaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/{payment}', [AdminPaymentController::class, 'show'])->name('payments.show');
        Route::post('payments/{payment}/verify', [AdminPaymentController::class, 'verify'])->name('payments.verify');
        Route::post('payments/{payment}/reject', [AdminPaymentController::class, 'reject'])->name('payments.reject');

        /*
        |----------------------------------------------------------------------
        | Customers
        |----------------------------------------------------------------------
        */
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::post('users/{user}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        /*
        |----------------------------------------------------------------------
        | Reviews
        |----------------------------------------------------------------------
        */
        Route::get('reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::post('reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve');
        Route::post('reviews/{review}/reject', [AdminReviewController::class, 'reject'])->name('reviews.reject');
        Route::delete('reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

        /*
        |----------------------------------------------------------------------
        | Payment methods
        |----------------------------------------------------------------------
        */
        Route::get('payment-methods', [PaymentMethodController::class, 'index'])->name('payment-methods.index');
        Route::get('payment-methods/create', [PaymentMethodController::class, 'create'])->name('payment-methods.create');
        Route::post('payment-methods', [PaymentMethodController::class, 'store'])->name('payment-methods.store');
        Route::get('payment-methods/{payment_method}/edit', [PaymentMethodController::class, 'edit'])->name('payment-methods.edit');
        Route::put('payment-methods/{payment_method}', [PaymentMethodController::class, 'update'])->name('payment-methods.update');
        Route::delete('payment-methods/{payment_method}', [PaymentMethodController::class, 'destroy'])->name('payment-methods.destroy');

        /*
        |----------------------------------------------------------------------
        | Website settings
        |----------------------------------------------------------------------
        */
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    });
