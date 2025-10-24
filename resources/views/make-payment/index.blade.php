<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments</title>
    <link rel="stylesheet" href="{{ asset('css/payments.css') }}">
    </head>
    <body>
    <div class="container">
    <div class="heading">Make a Payment</div>
    <div class="sub">Choose a payment method below.</div>

    <!-- Flash / Errors -->
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

    <div class="grid" id="payment-accordion">
        <!-- M-Pesa -->
        <div class="service" data-service="mpesa">
            <div class="service-header">
                <div class="service-title">
                    <img src="{{ asset('mpesa.png') }}" alt="M-Pesa" width="22" height="22"/>
                    <span>M-Pesa</span>
                    <span class="badge">STK Push</span>
                </div>
                <svg class="chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
            <div class="form-wrap">
                <form method="POST" action="{{ route('pay.initiate') }}">
                    @csrf
                    <input type="hidden" name="payment_method" value="mpesa">
                    <div class="row">
                        <div class="field">
                            <label>Phone (M-Pesa)</label>
                            <input name="phone" type="text" placeholder="2547XXXXXXXX" required>
                            <div class="hint">Use international format e.g. 2547XXXXXXXX</div>
                        </div>
                        <div class="field">
                            <label>Amount</label>
                            <input name="amount" type="number" min="1" step="0.01" placeholder="1000" required>
                        </div>
                        <div class="field">
                            <label>Reference</label>
                            <input name="reference" type="text" placeholder="Auto-generated" readonly>
                        </div>
                        <div class="field">
                            <label>Student Full Name</label>
                            <input name="student_full_name" type="text" placeholder="John Doe" required>
                        </div>
                        <div class="field">
                            <label>Student Email</label>
                            <input name="student_email" type="email" placeholder="john.doe@example.com">
                        </div>
                        <div class="field">
                            <label>Course</label>
                            <input name="course_name" type="text" placeholder="e.g. BSc Computer Science">
                        </div>
                    </div>
                    <div class="actions">
                        <button class="btn primary" type="submit">Pay with M-Pesa</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Airtel Money -->
        <div class="service" data-service="airtel">
            <div class="service-header">
                <div class="service-title">
                    <img src="{{ asset('airtel-money.png') }}" alt="Airtel Money" width="22" height="22"/>
                    <span>Airtel Money</span>
                    <span class="badge">Coming soon</span>
                </div>
                <svg class="chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
            <div class="form-wrap">
                <form method="POST" action="{{ route('pay.initiate') }}">
                    @csrf
                    <input type="hidden" name="payment_method" value="airtel">
                    <div class="row">
                        <div class="field">
                            <label>Phone (Airtel)</label>
                            <input name="phone" type="text" placeholder="2547XXXXXXXX" required>
                        </div>
                        <div class="field">
                            <label>Amount</label>
                            <input name="amount" type="number" min="1" step="0.01" placeholder="1000" required>
                        </div>
                        <div class="field">
                            <label>Reference</label>
                            <input name="reference" type="text" placeholder="Auto-generated" readonly>
                        </div>
                        <div class="field">
                            <label>Student Full Name</label>
                            <input name="student_full_name" type="text" placeholder="John Doe" required>
                        </div>
                        <div class="field">
                            <label>Student Email</label>
                            <input name="student_email" type="email" placeholder="john.doe@example.com">
                        </div>
                        <div class="field">
                            <label>Course</label>
                            <input name="course_name" type="text" placeholder="e.g. BSc Computer Science">
                        </div>
                    </div>
                    <div class="actions">
                        <button class="btn primary" type="submit">Pay with Airtel</button>
                        <span class="hint">Backend processing for Airtel can be implemented next.</span>
                    </div>
                </form>
            </div>
        </div>

        <!-- eCitizen -->
        <div class="service" data-service="ecitizen">
            <div class="service-header">
                <div class="service-title">
                    <img src="{{ asset('e-citizen.png') }}" alt="eCitizen" width="22" height="22"/>
                    <span>eCitizen</span>
                    <span class="badge">Coming soon</span>
                </div>
                <svg class="chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
            <div class="form-wrap">
                <form method="POST" action="{{ route('pay.initiate') }}">
                    @csrf
                    <input type="hidden" name="payment_method" value="ecitizen">
                    <div class="row">
                        <div class="field">
                            <label>Phone</label>
                            <input name="phone" type="text" placeholder="2547XXXXXXXX" required>
                        </div>
                        <div class="field">
                            <label>Amount</label>
                            <input name="amount" type="number" min="1" step="0.01" placeholder="1000" required>
                        </div>
                        <div class="field">
                            <label>Reference</label>
                            <input name="reference" type="text" placeholder="Auto-generated" readonly>
                        </div>
                        <div class="field">
                            <label>Student Full Name</label>
                            <input name="student_full_name" type="text" placeholder="John Doe" required>
                        </div>
                        <div class="field">
                            <label>Student Email</label>
                            <input name="student_email" type="email" placeholder="john.doe@example.com">
                        </div>
                        <div class="field">
                            <label>Course</label>
                            <input name="course_name" type="text" placeholder="e.g. BSc Computer Science">
                        </div>
                    </div>
                    <div class="actions">
                        <button class="btn primary" type="submit">Pay via eCitizen</button>
                        <span class="hint">Backend processing for eCitizen can be implemented next.</span>
                    </div>
                </form>
            </div>
        </div>

        <!-- Card -->
        <div class="service" data-service="card">
            <div class="service-header">
                <div class="service-title">
                    <img src="{{ asset('card.jpg') }}" alt="Card" width="22" height="22"/>
                    <span>Debit/Credit Card</span>
                    <span class="badge">Coming soon</span>
                </div>
                <svg class="chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
            <div class="form-wrap">
                <form method="POST" action="{{ route('pay.initiate') }}">
                    @csrf
                    <input type="hidden" name="payment_method" value="card">
                    <div class="row">
                        <div class="field">
                            <label>Phone (for receipt)</label>
                            <input name="phone" type="text" placeholder="2547XXXXXXXX" required>
                            <div class="hint">Used for receipts/notifications.</div>
                        </div>
                        <div class="field">
                            <label>Amount</label>
                            <input name="amount" type="number" min="1" step="0.01" placeholder="1000" required>
                        </div>
                        <div class="field">
                            <label>Reference</label>
                            <input name="reference" type="text" placeholder="Auto-generated" readonly>
                        </div>
                        <div class="field">
                            <label>Student Full Name</label>
                            <input name="student_full_name" type="text" placeholder="John Doe" required>
                        </div>
                        <div class="field">
                            <label>Student Email</label>
                            <input name="student_email" type="email" placeholder="john.doe@example.com">
                        </div>
                        <div class="field">
                            <label>Course</label>
                            <input name="course_name" type="text" placeholder="e.g. BSc Computer Science">
                        </div>
                        <div class="field">
                            <label>Card Number</label>
                            <input name="card_number" inputmode="numeric" autocomplete="cc-number" placeholder="4242 4242 4242 4242" />
                        </div>
                        <div class="field">
                            <label>Expiry</label>
                            <input name="card_expiry" placeholder="MM/YY" />
                        </div>
                        <div class="field">
                            <label>CVV</label>
                            <input name="card_cvv" inputmode="numeric" placeholder="123" />
                        </div>
                    </div>
                    <div class="actions">
                        <button class="btn primary" type="submit">Pay with Card</button>
                        <span class="hint">Card processing not yet implemented in backend.</span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/payments.js') }}"></script>
</body>
</html>
