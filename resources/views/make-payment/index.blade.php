<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments</title>
    <link rel="stylesheet" href="{{ asset('css/payments.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    </head>
    <body>
    <div class="top-bar" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; background: #fff; border-bottom: 1px solid #e5e7eb;">
        <div class="logo" style="font-weight: 600; font-size: 18px;">
            <img src="{{ asset('logo.png') }}" alt="Logo" style="height: 30px; width: auto;">
        </div>
        <button onclick="window.history.back()" style="background: #ef4444; border: none; color: white; font-weight: 500; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 5px; padding: 8px 16px; border-radius: 6px; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#dc2626'" onmouseout="this.style.backgroundColor='#ef4444'">
            <i class="bi bi-x-lg"></i>
            <span>Cancel</span>
        </button>
    </div>
    <div class="container">
    <div class="heading">Checkout</div>
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

    @php($prefill = session('pay.prefill', []))

    <div class="confirm-strip" style="display:flex;gap:12px;flex-wrap:wrap;margin:12px 0;">
        <div class="card" style="flex:1 1 200px;border:1px solid #e5e7eb;border-radius:10px;padding:12px;background:#fff;min-width:200px;">
            <div style="font-size:12px;color:#6b7280;">Student Name</div>
            <div style="font-weight:600;">{{ $prefill['student_full_name'] ?? '—' }}</div>
        </div>
        <div class="card" style="flex:1 1 200px;border:1px solid #e5e7eb;border-radius:10px;padding:12px;background:#fff;min-width:200px;">
            <div style="font-size:12px;color:#6b7280;">Student Email</div>
            <div style="font-weight:600;">{{ $prefill['student_email'] ?? '—' }}</div>
        </div>
        <div class="card" style="flex:1 1 200px;border:1px solid #e5e7eb;border-radius:10px;padding:12px;background:#fff;min-width:200px;">
            <div style="font-size:12px;color:#6b7280;">Course</div>
            <div style="font-weight:600;">{{ $prefill['course_name'] ?? '—' }}</div>
        </div>
        <div class="card" style="flex:1 1 200px;border:1px solid #e5e7eb;border-radius:10px;padding:12px;background:#fff;min-width:200px;">
            <div style="font-size:12px;color:#6b7280;">Amount</div>
            <div style="font-weight:600;">{{ isset($prefill['amount']) ? ('KES ' . number_format((float)$prefill['amount'], 2, '.', ',')) : '—' }}</div>
        </div>
    </div>

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
                    @if(!empty($prefill['client_app_id'] ?? null))
                        <input type="hidden" name="client_app_id" value="{{ $prefill['client_app_id'] }}">
                    @endif
                    <div class="row">
                        <div class="field">
                            <label>Phone (M-Pesa)</label>
                            <input name="phone" type="text" placeholder="2547XXXXXXXX" required>
                            <div class="hint">Use international format e.g. 2547XXXXXXXX</div>
                        </div>
                        <div class="field">
                            <label>Amount</label>
                            <input name="amount" type="number" min="1" step="0.01" placeholder="1000" required value="{{ old('amount', $prefill['amount'] ?? '') }}" readonly>
                        </div>
                        <div class="field">
                            <label>Reference</label>
                            <input name="reference" type="text" placeholder="Auto-generated" readonly>
                        </div>
                        <div class="field">
                            <label>Student Full Name</label>
                            <input name="student_full_name" type="text" placeholder="John Doe" required value="{{ old('student_full_name', $prefill['student_full_name'] ?? '') }}" readonly>
                        </div>
                        <div class="field">
                            <label>Student Email</label>
                            <input name="student_email" type="email" placeholder="john.doe@example.com" value="{{ old('student_email', $prefill['student_email'] ?? '') }}" readonly>
                        </div>
                        <div class="field">
                            <label>Course</label>
                            <input name="course_name" type="text" placeholder="e.g. BSc Computer Science" value="{{ old('course_name', $prefill['course_name'] ?? '') }}" readonly>
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
                    @if(!empty($prefill['client_app_id'] ?? null))
                        <input type="hidden" name="client_app_id" value="{{ $prefill['client_app_id'] }}">
                    @endif
                    <div class="row">
                        <div class="field">
                            <label>Phone (Airtel)</label>
                            <input name="phone" type="text" placeholder="2547XXXXXXXX" required>
                        </div>
                        <div class="field">
                            <label>Amount</label>
                            <input name="amount" type="number" min="1" step="0.01" placeholder="1000" required value="{{ old('amount', $prefill['amount'] ?? '') }}" readonly>
                        </div>
                        <div class="field">
                            <label>Reference</label>
                            <input name="reference" type="text" placeholder="Auto-generated" readonly>
                        </div>
                        <div class="field">
                            <label>Student Full Name</label>
                            <input name="student_full_name" type="text" placeholder="John Doe" required value="{{ old('student_full_name', $prefill['student_full_name'] ?? '') }}" readonly>
                        </div>
                        <div class="field">
                            <label>Student Email</label>
                            <input name="student_email" type="email" placeholder="john.doe@example.com" value="{{ old('student_email', $prefill['student_email'] ?? '') }}" readonly>
                        </div>
                        <div class="field">
                            <label>Course</label>
                            <input name="course_name" type="text" placeholder="e.g. BSc Computer Science" value="{{ old('course_name', $prefill['course_name'] ?? '') }}" readonly>
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
                    @if(!empty($prefill['client_app_id'] ?? null))
                        <input type="hidden" name="client_app_id" value="{{ $prefill['client_app_id'] }}">
                    @endif
                    <div class="row">
                        <div class="field">
                            <label>Phone</label>
                            <input name="phone" type="text" placeholder="2547XXXXXXXX" required>
                        </div>
                        <div class="field">
                            <label>Amount</label>
                            <input name="amount" type="number" min="1" step="0.01" placeholder="1000" required value="{{ old('amount', $prefill['amount'] ?? '') }}" readonly>
                        </div>
                        <div class="field">
                            <label>Reference</label>
                            <input name="reference" type="text" placeholder="Auto-generated" readonly>
                        </div>
                        <div class="field">
                            <label>Student Full Name</label>
                            <input name="student_full_name" type="text" placeholder="John Doe" required value="{{ old('student_full_name', $prefill['student_full_name'] ?? '') }}" readonly>
                        </div>
                        <div class="field">
                            <label>Student Email</label>
                            <input name="student_email" type="email" placeholder="john.doe@example.com" value="{{ old('student_email', $prefill['student_email'] ?? '') }}" readonly>
                        </div>
                        <div class="field">
                            <label>Course</label>
                            <input name="course_name" type="text" placeholder="e.g. BSc Computer Science" value="{{ old('course_name', $prefill['course_name'] ?? '') }}" readonly>
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
                    @if(!empty($prefill['client_app_id'] ?? null))
                        <input type="hidden" name="client_app_id" value="{{ $prefill['client_app_id'] }}">
                    @endif
                    <div class="row">
                        <div class="field">
                            <label>Phone (for receipt)</label>
                            <input name="phone" type="text" placeholder="2547XXXXXXXX" required>
                            <div class="hint">Used for receipts/notifications.</div>
                        </div>
                        <div class="field">
                            <label>Amount</label>
                            <input name="amount" type="number" min="1" step="0.01" placeholder="1000" required value="{{ old('amount', $prefill['amount'] ?? '') }}" readonly>
                        </div>
                        <div class="field">
                            <label>Reference</label>
                            <input name="reference" type="text" placeholder="Auto-generated" readonly>
                        </div>
                        <div class="field">
                            <label>Student Full Name</label>
                            <input name="student_full_name" type="text" placeholder="John Doe" required value="{{ old('student_full_name', $prefill['student_full_name'] ?? '') }}" readonly>
                        </div>
                        <div class="field">
                            <label>Student Email</label>
                            <input name="student_email" type="email" placeholder="john.doe@example.com" value="{{ old('student_email', $prefill['student_email'] ?? '') }}" readonly>
                        </div>
                        <div class="field">
                            <label>Course</label>
                            <input name="course_name" type="text" placeholder="e.g. BSc Computer Science" value="{{ old('course_name', $prefill['course_name'] ?? '') }}" readonly>
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

<div id="pay-modal" class="pay-modal" style="display:none;">
  <div class="pay-modal__backdrop"></div>
  <div class="pay-modal__dialog">
    <div class="pay-modal__header">
      <div class="pay-modal__title">Processing Payment</div>
    </div>
    <div class="pay-modal__body">
      <div id="pay-modal-step-init" class="pay-step">
        <div class="pay-step__icon spinner"></div>
        <div class="pay-step__title">Initiating payment…</div>
        <div class="pay-step__desc">Please wait while we start your payment.</div>
      </div>
      <div id="pay-modal-step-wait" class="pay-step" style="display:none;">
        <div class="pay-step__icon phone"></div>
        <div class="pay-step__title">Check your phone</div>
        <div class="pay-step__desc">We sent an STK push. Enter your M-Pesa PIN to approve the payment.</div>
      </div>
      <div id="pay-modal-step-success" class="pay-step" style="display:none;">
        <div class="pay-step__icon success"></div>
        <div class="pay-step__title">Payment approved</div>
        <div class="pay-step__desc">Redirecting you to complete enrollment…</div>
      </div>
      <div id="pay-modal-step-failed" class="pay-step" style="display:none;">
        <div class="pay-step__icon error"></div>
        <div class="pay-step__title">Payment failed</div>
        <div class="pay-step__desc">Your payment did not complete. You can close this dialog and try again.</div>
      </div>
    </div>
    <div class="pay-modal__footer">
      <button id="pay-modal-close" class="btn" type="button" style="display:none;">Close</button>
    </div>
  </div>
  <style>
    .pay-modal{position:fixed;inset:0;z-index:1000}
    .pay-modal__backdrop{position:absolute;inset:0;background:rgba(0,0,0,.5)}
    .pay-modal__dialog{position:relative;max-width:520px;margin:10vh auto;background:#fff;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.2);overflow:hidden}
    .pay-modal__header{padding:16px 20px;border-bottom:1px solid #eee}
    .pay-modal__title{font-weight:600;font-size:18px}
    .pay-modal__body{padding:20px}
    .pay-step{display:flex;flex-direction:column;align-items:center;text-align:center;gap:8px}
    .pay-step__icon{width:46px;height:46px;border-radius:50%;display:inline-block}
    .pay-step__icon.spinner{border:4px solid #e5e7eb;border-top-color:#2563eb;animation:spin 1s linear infinite}
    .pay-step__icon.success{background:#22c55e}
    .pay-step__icon.error{background:#ef4444}
    .pay-step__icon.phone{background:#2563eb}
    .pay-step__title{font-weight:600}
    .pay-step__desc{color:#6b7280}
    .pay-modal__footer{padding:16px 20px;border-top:1px solid #eee;display:flex;justify-content:flex-end}
    @keyframes spin{to{transform:rotate(360deg)}}
  </style>
</div>

<script src="{{ asset('js/payments.js') }}"></script>
</body>
</html>
