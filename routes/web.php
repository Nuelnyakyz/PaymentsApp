<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MakePaymentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PaymentRecordsController as AdminPaymentRecordsController;
use App\Http\Controllers\Admin\TransactionRecordsController as AdminTransactionRecordsController;
use App\Http\Controllers\Admin\ReceiptRecordsController as AdminReceiptRecordsController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('login');
});

// Admin area
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);
    Route::get('/payments', [AdminPaymentRecordsController::class, 'index'])->name('payments.index');
    Route::get('/transactions', [AdminTransactionRecordsController::class, 'index'])->name('transactions.index');
    Route::get('/receipts', [AdminReceiptRecordsController::class, 'index'])->name('receipts.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Public payment form and initiation (accessible by external users)
Route::get('/pay', function () {
    return view('make-payment.index');
})->name('pay.index');

Route::post('/pay/initiate', [MakePaymentController::class, 'initiate'])
    ->name('pay.initiate');

require __DIR__.'/auth.php';
