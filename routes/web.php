<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return view('welcome');
});

// Payments UI and submission
Route::get('/payments', function () {
    return view('payments.index');
})->name('payments.index');

Route::post('/payments/initiate', [PaymentController::class, 'initiate'])
    ->name('payments.initiate');


