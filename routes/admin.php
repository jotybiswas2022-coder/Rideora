<?php

use Illuminate\Support\Facades\Route;


Route::prefix('admin')->middleware('admin')->group(function () {

});