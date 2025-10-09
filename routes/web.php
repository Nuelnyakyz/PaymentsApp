<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return view('welcome');
});

// Mirror API initiate endpoint under web routes as well
Route::post('/api/payments/initiate', [PaymentController::class, 'initiate']);

