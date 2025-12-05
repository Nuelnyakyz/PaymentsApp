<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MakePaymentController;
use App\Http\Controllers\PaymentSessionController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PaymentRecordsController as AdminPaymentRecordsController;
use App\Http\Controllers\Admin\ReceiptRecordsController as AdminReceiptRecordsController;
use App\Http\Controllers\Admin\PaymentCallbackController as AdminPaymentCallbackController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
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
    Route::get('/payments', [AdminPaymentRecordsController::class, 'index'])->name('payments.index');
    Route::post('/payments/{payment}/resend-callback', [AdminPaymentCallbackController::class, 'resend'])->name('payments.resend');
    Route::get('/receipts', [AdminReceiptRecordsController::class, 'index'])->name(name: 'receipts.index');
    Route::get('/receipts/{receipt}', [AdminReceiptRecordsController::class, 'show'])->name('receipts.show');
    Route::get('/receipts/{receipt}/pdf', [AdminReceiptRecordsController::class, 'pdf'])->name('receipts.pdf');

    // Services (Gateway settings)
    Route::get('/services', [\App\Http\Controllers\Admin\ServicesController::class, 'index'])->name('services.index');
    Route::post('/services/mpesa', [\App\Http\Controllers\Admin\ServicesController::class, 'updateMpesa'])->name('services.mpesa.update');
    Route::post('/services/airtel', [\App\Http\Controllers\Admin\ServicesController::class, 'updateAirtel'])->name('services.airtel.update');
    Route::post('/services/visa', [\App\Http\Controllers\Admin\ServicesController::class, 'updateVisa'])->name('services.visa.update');
    // SMTP settings
    Route::get('/smtp', [\App\Http\Controllers\Admin\SmtpSettingsController::class, 'index'])->name('smtp.index');
    Route::post('/smtp', [\App\Http\Controllers\Admin\SmtpSettingsController::class, 'update'])->name('smtp.update');
    // Services (Gateway settings)
    Route::get('/client-apps', [ClientAppController::class, 'index'])->name('client-apps.index');
    Route::post('/client-apps', [ClientAppController::class, 'store'])->name('client-apps.store');
    Route::patch('/client-apps/{clientApp}', [ClientAppController::class, 'update'])->name('client-apps.update');
    Route::post('/client-apps/{clientApp}/regenerate', [ClientAppController::class, 'regenerate'])->name('client-apps.regenerate');
    Route::delete('/client-apps/{clientApp}', [ClientAppController::class, 'destroy'])->name('client-apps.destroy');

    // User management
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::patch('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
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

// Temporary route for previewing the email template
Route::get('/test-email', function () {
    $payment = new \App\Models\Payment();
    $payment->reference = 'INV-2023-001';
    $payment->payment_method = 'mpesa';
    $payment->student_full_name = 'John Doe';
    
    $receipt = new \App\Models\Receipt();
    $receipt->receipt_number = 'RCT-12345678';
    $receipt->issued_at = now();
    $receipt->amount = 1.00;
    $receipt->course_name = 'How to Develop Beautiful Landscape Photos';
    $receipt->payer_phone = '254712345678';
    $receipt->setRelation('payment', $payment);

    return new \App\Mail\PaymentReceiptMail($payment, $receipt);
});
