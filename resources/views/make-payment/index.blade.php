
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Snapay</title>
    <link rel="stylesheet" href="{{ asset('css/payments.css') }}">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@700;800;900&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <div class="checkout-header">
        <div class="checkout-header__inner">
            <div class="brand">
                <img src="{{ asset('Snapay.png') }}" alt="Snapay Logo">
                <span class="text-primary">SNAPAY</span>
            </div>
            <a href="{{ url('/') }}" class="checkout-cancel">Cancel</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="checkout-content">
            <!-- Left Panel: Payment Form -->
            <div class="checkout-left">
                <h1 class="section-title">Checkout</h1>

                <div class="section-header">
                    <h3 class="text-primary">Payment method</h3>
                    <div class="secure-badge">
                        <span>Secure and encrypted</span>
                        <span>🔒</span>
                    </div>
                </div>

                <!-- Flash Messages / Errors -->
                @if ($errors->any())
                    <div class="errors">
                        <strong>There were validation errors:</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                @if (session('status'))
                    <div class="success">{{ session('status') }}</div>
                @endif

                @php($prefill = session('pay.prefill', []))

                <!-- Payment Methods -->
                <div class="payment-methods">
                    <!-- M-Pesa -->
                    <div class="payment-method active" data-method="mpesa">
                        <div class="method-header">
                            <div class="method-radio"></div>
                            <div class="method-info">
                                <img src="{{ asset('mpesa.png') }}" alt="M-Pesa" class="method-icon">
                                <div class="method-name">
                                    M-Pesa
                                    <span class="badge">STK Push</span>
                                </div>
                            </div>
                        </div>
                        <div class="payment-form">
                            <form method="POST" action="{{ route('pay.initiate') }}">
                                @csrf
                                <input type="hidden" name="payment_method" value="mpesa">
                                @if(!empty($prefill['client_app_id'] ?? null))
                                    <input type="hidden" name="client_app_id" value="{{ $prefill['client_app_id'] }}">
                                @endif
                                
                                <!-- Hidden fields: prefilled from session, displayed in summary -->
                                <input type="hidden" name="amount" value="{{ old('amount', $prefill['amount'] ?? '') }}">
                                <input type="hidden" name="student_full_name" value="{{ old('student_full_name', $prefill['student_full_name'] ?? '') }}">
                                <input type="hidden" name="student_email" value="{{ old('student_email', $prefill['student_email'] ?? '') }}">
                                <input type="hidden" name="course_name" value="{{ old('course_name', $prefill['course_name'] ?? '') }}">
                                
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Phone (M-Pesa)</label>
                                        <input type="tel" name="payer_phone" placeholder="2547XXXXXXXX" required>
                                        <div class="form-hint">Use international format e.g. 2547XXXXXXXX</div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Airtel Money -->
                    <div class="payment-method" data-method="airtel">
                        <div class="method-header">
                            <div class="method-radio"></div>
                            <div class="method-info">
                                <img src="{{ asset('airtel-money.png') }}" alt="Airtel Money" class="method-icon">
                                <div class="method-name">
                                    Airtel Money
                                    <span class="badge soon">Coming soon</span>
                                </div>
                            </div>
                        </div>
                        <div class="payment-form">
                            <form method="POST" action="{{ route('pay.initiate') }}">
                                @csrf
                                <input type="hidden" name="payment_method" value="airtel">
                                @if(!empty($prefill['client_app_id'] ?? null))
                                    <input type="hidden" name="client_app_id" value="{{ $prefill['client_app_id'] }}">
                                @endif
                                
                                <!-- Hidden fields: prefilled from session, displayed in summary -->
                                <input type="hidden" name="amount" value="{{ old('amount', $prefill['amount'] ?? '') }}">
                                <input type="hidden" name="student_full_name" value="{{ old('student_full_name', $prefill['student_full_name'] ?? '') }}">
                                <input type="hidden" name="student_email" value="{{ old('student_email', $prefill['student_email'] ?? '') }}">
                                <input type="hidden" name="course_name" value="{{ old('course_name', $prefill['course_name'] ?? '') }}">
                                
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Phone (Airtel)</label>
                                        <input type="tel" name="payer_phone" placeholder="2547XXXXXXXX" disabled>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Debit/Credit Card -->
                    <div class="payment-method" data-method="card">
                        <div class="method-header">
                            <div class="method-radio"></div>
                            <div class="method-info">
                                <img src="{{ asset('card.jpg') }}" alt="Card" class="method-icon">
                                <div class="method-name">
                                    Debit/Credit Card
                                    <span class="badge soon">Coming soon</span>
                                </div>
                            </div>
                        </div>
                        <div class="payment-form">
                            <form method="POST" action="{{ route('pay.initiate') }}">
                                @csrf
                                <input type="hidden" name="payment_method" value="card">
                                @if(!empty($prefill['client_app_id'] ?? null))
                                    <input type="hidden" name="client_app_id" value="{{ $prefill['client_app_id'] }}">
                                @endif
                                
                                <!-- Hidden fields: prefilled from session, displayed in summary -->
                                <input type="hidden" name="amount" value="{{ old('amount', $prefill['amount'] ?? '') }}">
                                <input type="hidden" name="student_full_name" value="{{ old('student_full_name', $prefill['student_full_name'] ?? '') }}">
                                <input type="hidden" name="student_email" value="{{ old('student_email', $prefill['student_email'] ?? '') }}">
                                <input type="hidden" name="course_name" value="{{ old('course_name', $prefill['course_name'] ?? '') }}">
                                
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Card Number</label>
                                        <input type="text" name="card_number" placeholder="4242 4242 4242 4242" disabled>
                                    </div>

                                    <div class="form-group">
                                        <label>Expiry Date</label>
                                        <input type="text" name="card_expiry" placeholder="MM/YY" disabled>
                                    </div>

                                    <div class="form-group">
                                        <label>CVV</label>
                                        <input type="text" name="card_cvv" placeholder="123" disabled>
                                    </div>     
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- eCitizen -->
                    <div class="payment-method" data-method="ecitizen">
                        <div class="method-header">
                            <div class="method-radio"></div>
                            <div class="method-info">
                                <img src="{{ asset('e-citizen.png') }}" alt="eCitizen" class="method-icon">
                                <div class="method-name">
                                    eCitizen
                                    <span class="badge soon">Coming soon</span>
                                </div>
                            </div>
                        </div>
                        <div class="payment-form">
                            <form method="POST" action="{{ route('pay.initiate') }}">
                                @csrf
                                <input type="hidden" name="payment_method" value="ecitizen">
                                @if(!empty($prefill['client_app_id'] ?? null))
                                    <input type="hidden" name="client_app_id" value="{{ $prefill['client_app_id'] }}">
                                @endif
                                
                                <!-- Hidden fields: prefilled from session, displayed in summary -->
                                <input type="hidden" name="amount" value="{{ old('amount', $prefill['amount'] ?? '') }}">
                                <input type="hidden" name="student_full_name" value="{{ old('student_full_name', $prefill['student_full_name'] ?? '') }}">
                                <input type="hidden" name="student_email" value="{{ old('student_email', $prefill['student_email'] ?? '') }}">
                                <input type="hidden" name="course_name" value="{{ old('course_name', $prefill['course_name'] ?? '') }}">
                                
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Phone Number</label>
                                        <input type="tel" name="payer_phone" placeholder="2547XXXXXXXX" disabled>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Order Summary -->
            <div class="checkout-right">
                <div class="summary-card">
                    <h2 class="summary-title text-primary">Student Details</h2>
                    
                    <div class="student-info">
                        <div class="info-row">
                            <span class="label">Student:</span>
                            <span>{{ $prefill['student_full_name'] ?? '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Email:</span>
                            <span>{{ $prefill['student_email'] ?? '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Course:</span>
                            <span>{{ $prefill['course_name'] ?? '—' }}</span>
                        </div>
                    </div>

                    <hr class="summary-divider">

                    <h2 class="summary-title text-primary">Order summary</h2>
                    
                    <div class="summary-row">
                        <span class="label">Original Price:</span>
                        <span>{{ isset($prefill['amount']) ? ('KES ' . number_format((float)$prefill['amount'], 2, '.', ',')) : '—' }}</span>
                    </div>
                    
                    <div class="summary-row">
                        <span class="label">Discounts:</span>
                        <span>- KES 0.00</span>
                    </div>
                    
                    <hr class="summary-divider">
                    
                    <div class="summary-total">
                        <span class="text-primary">Total</span>
                        <span class="text-secondary">{{ isset($prefill['amount']) ? ('KES ' . number_format((float)$prefill['amount'], 2, '.', ',')) : '—' }}</span>
                    </div>
                    
                    <div class="summary-note">
                        By completing your purchase, you agree to these <a href="#">Terms of Use</a>.
                    </div>
                    
                    <button type="button" class="btn-proceed bg-primary">Complete Checkout</button>
                    
                    <div class="guarantee">
                        <div class="guarantee-title">30-Day Money-Back Guarantee</div>
                        <div class="guarantee-text">
                            Not satisfied? Get a full refund within 30 days. Simple and straightforward!
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="pay-modal" class="pay-modal" style="display:none;">
        <div class="pay-modal__backdrop"></div>
        <div class="pay-modal__dialog">
            <div class="pay-modal__header">
                <div class="pay-modal__title">Processing Payment</div>
            </div>
            <div class="pay-modal__body">
                <div id="pay-modal-step-init" class="pay-step">
                    <div class="pay-step__icon spinner"></div>
                    <div class="pay-step__title">Initiating Payment</div>
                    <div class="pay-step__desc">Please wait while we process your request...</div>
                </div>
                <div id="pay-modal-step-wait" class="pay-step" style="display:none;">
                    <div class="pay-step__icon phone"></div>
                    <div class="pay-step__title">Check Your Phone</div>
                    <div class="pay-step__desc">You'll receive an M-Pesa prompt. Enter your PIN to complete the payment.</div>
                </div>
                <div id="pay-modal-step-success" class="pay-step" style="display:none;">
                    <div class="pay-step__icon success"></div>
                    <div class="pay-step__title">Payment Successful!</div>
                    <div class="pay-step__desc">Your payment has been confirmed. Redirecting to client app...</div>
                </div>
                <div id="pay-modal-step-failed" class="pay-step" style="display:none;">
                    <div class="pay-step__icon error"></div>
                    <div class="pay-step__title">Payment Failed</div>
                    <div class="pay-step__desc">Your payment could not be completed. Please try again or contact support.</div>
                </div>
            </div>
            <div class="pay-modal__footer">
                <button id="pay-modal-close" class="btn" type="button" style="display:none;">Close</button>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/payments.js') }}"></script>
    <script>
        // Payment method selection
        document.querySelectorAll('.payment-method').forEach(method => {
            method.querySelector('.method-header').addEventListener('click', function() {
                // Remove active class from all methods
                document.querySelectorAll('.payment-method').forEach(m => {
                    m.classList.remove('active');
                });
                
                // Add active class to clicked method
                method.classList.add('active');
            });
        });

        // Complete Checkout button functionality
        document.querySelector('.btn-proceed').addEventListener('click', function() {
            // Call the global function defined in payments.js
            if (typeof window.triggerPaymentSubmit === 'function') {
                window.triggerPaymentSubmit();
            }
        });
    </script>
</body>
</html>