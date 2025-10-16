<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MakePaymentController;
use App\Http\Controllers\WebhookController;

Route::post('/payments/initiate', [MakePaymentController::class, 'initiate']);
Route::post('/webhooks/mpesa', [WebhookController::class, 'mpesa']);
// Route::post('/webhooks/airtel', [WebhookController::class, 'airtel']);
// Route::post('/webhooks/card', [WebhookController::class, 'card']);
// Route::post('/webhooks/ecitizen', [WebhookController::class, 'ecitizen']);