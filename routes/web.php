<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\SiteController;

// Site home and contact page routes
Route::controller(SiteController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/contact', 'contact')->name('contact.page');
});

// Password reset link request form route
Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');


// Authentication routes
Auth::routes();

// Include admin route file
include('admin.php');
