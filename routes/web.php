<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MakePaymentController;
use App\Http\Controllers\PaymentSessionController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\TransactionRecordsController as AdminTransactionRecordsController;
use App\Http\Controllers\Admin\ReceiptRecordsController as AdminReceiptRecordsController;
use App\Http\Controllers\ClientAppController;
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
    Route::get('/transactions', [AdminTransactionRecordsController::class, 'index'])->name('transactions.index');
    Route::get('/receipts', [AdminReceiptRecordsController::class, 'index'])->name('receipts.index');
    Route::get('/receipts/{receipt}', [AdminReceiptRecordsController::class, 'show'])->name('receipts.show');
    Route::get('/receipts/{receipt}/pdf', [AdminReceiptRecordsController::class, 'pdf'])->name('receipts.pdf');

    Route::get('/client-apps', [ClientAppController::class, 'index'])->name('client-apps.index');
    Route::post('/client-apps', [ClientAppController::class, 'store'])->name('client-apps.store');
    Route::patch('/client-apps/{clientApp}', [ClientAppController::class, 'update'])->name('client-apps.update');
    Route::post('/client-apps/{clientApp}/regenerate', [ClientAppController::class, 'regenerate'])->name('client-apps.regenerate');
    Route::delete('/client-apps/{clientApp}', [ClientAppController::class, 'destroy'])->name('client-apps.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Public payment form and initiation (accessible by external users)
Route::post('/pay/prepare', [PaymentSessionController::class, 'prepare'])
    ->name('pay.prepare')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

Route::get('/pay', function () {
    return view('make-payment.index');
})->name('pay.index');

Route::post('/pay/initiate', [MakePaymentController::class, 'initiate'])
    ->name('pay.initiate');

Route::get('/pay/status/{payment}', [MakePaymentController::class, 'status'])
    ->name('pay.status');

Route::get('/pay/complete/{payment}', [MakePaymentController::class, 'complete'])
    ->name('pay.complete');

require __DIR__.'/auth.php';
