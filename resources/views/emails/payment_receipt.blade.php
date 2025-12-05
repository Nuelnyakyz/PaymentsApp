<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>
    <style>
        /* Reset and basic styles */
        body { margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #ffffff; color: #1f2937; }
        table { border-collapse: collapse; width: 100%; }
        img { max-width: 100%; display: block; }
        
        /* Container */
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
        
        /* Header */
        .header { background-color: #037b90; padding: 40px 20px; text-align: center; }
        .header img { height: 50px; display: inline-block; vertical-align: middle; }
        .header-title { color: #ffffff; font-size: 24px; font-weight: bold; display: inline-block; vertical-align: middle; margin-left: 10px; }
        
        /* Content */
        .content { padding: 30px 20px; }
        .greeting { font-size: 20px; font-weight: bold; margin-bottom: 10px; text-align: center; color: #1f2937; }
        .date-info { text-align: center; color: #6b7280; font-size: 14px; margin-bottom: 30px; }
        
        /* Card */
        .card { background-color: #f3f4f6; border-radius: 12px; padding: 24px; margin-bottom: 24px; border-left: 5px solid #ff7f50; }
        .item-table td { padding: 8px 0; vertical-align: top; }
        .item-name { font-weight: 600; font-size: 16px; color: #1f2937; }
        .item-sub { font-size: 13px; color: #6b7280; margin-top: 4px; }
        .item-price { text-align: right; font-weight: 600; font-size: 16px; color: #1f2937; }
        
        .section-title { font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 4px; }
        .section-value { font-size: 16px; color: #1f2937; }
        
        .divider { border: 0; border-top: 1px solid #d1d5db; margin: 20px 0; }
        
        .total-label { font-size: 16px; font-weight: 600; color: #1f2937; }
        .total-amount { font-size: 18px; font-weight: 700; color: #1f2937; text-align: right; }
        
        /* Details below card */
        .details-table { margin-top: 20px; }
        .details-label { color: #6b7280; font-size: 13px; margin-bottom: 2px; }
        .details-value { color: #1f2937; font-size: 15px; font-weight: 500; }
        
        /* Footer */
        .footer { text-align: center; padding: 20px; color: #9ca3af; font-size: 12px; border-top: 2px solid #037b90; margin-top: 20px; }
        .footer a { color: #037b90; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header with OUK Primary Color -->
        <div class="header">
            <div style="text-align: center;">
                <img src="{{ $message->embed(public_path('Snapay.png')) }}" alt="Snapay" style="height: 40px; display: inline-block; vertical-align: middle; margin-right: 5px;">
                <span class="header-title" style="display: inline-block; vertical-align: middle; margin: 0;">Snapay</span>
            </div>
        </div>

        <div class="content">
            <div class="greeting">Hi, <span style="color: #ff7f50;">{{ $payment->student_full_name ?? 'Student' }}</span></div>
            <div style="text-align: center; color: #6b7280; font-size: 16px; margin-bottom: 20px;">
                Your receipt for {{ $clientName ?? 'Snap Learn' }}
            </div>
            
            <div class="date-info">
                {{ optional($receipt->issued_at)->format('F j, Y g:i A') }}
            </div>

            <!-- Grey Card (Spotify Style) -->
            <div class="card">
                <!-- Invoice & Transaction Code at the top of the card -->
                <table class="details-table" style="margin-top: 0; margin-bottom: 20px; border-bottom: 1px solid #e5e7eb; padding-bottom: 15px;">
                    <tr>
                        <td style="width: 50%; padding-right: 10px;">
                            <div class="details-label">Invoice No</div>
                            <div class="details-value">{{ optional($receipt->payment)->reference ?? '-' }}</div>
                        </td>
                        <td style="width: 50%; padding-left: 10px; text-align: right;">
                            <div class="details-label">Transaction Code</div>
                            <div class="details-value">{{ $receipt->receipt_number }}</div>
                        </td>
                    </tr>
                </table>

                <table class="item-table">
                    <tr>
                        <td>
                            <div class="item-name">{{ $receipt->course_name ?? 'Course Subscription' }}</div>
                            <div class="item-sub">1 course</div>
                        </td>
                        <td class="item-price">
                            Ksh {{ number_format($receipt->amount, 2) }}
                        </td>
                    </tr>
                </table>

                <div style="height: 15px;"></div>

                <table class="item-table">
                    <tr>
                        <td>
                            <div class="item-name" style="font-size: 14px;">Total tax</div>
                            <div class="item-sub">VAT (16%)</div>
                        </td>
                        <td class="item-price" style="font-size: 14px;">
                            Ksh 0.00
                        </td>
                    </tr>
                </table>

                <div style="height: 20px;"></div>

                <div class="section-title">Payment Method</div>
                <div class="section-value">
                    {{ strtoupper(optional($receipt->payment)->payment_method ?? 'MPESA') }}
                </div>
                <div class="section-value" style="color: #6b7280; font-size: 14px;">
                    @php
                        $phone = $receipt->payer_phone;
                        $masked = $phone ? (strlen($phone) > 4 ? '**** ' . substr($phone, -4) : $phone) : '****';
                    @endphp
                    {{ $masked }}
                </div>

                <hr class="divider">

                <table>
                    <tr>
                        <td class="total-label">Total</td>
                        <td class="total-amount">Ksh {{ number_format($receipt->amount, 2) }}</td>
                    </tr>
                </table>
            </div>

            <!-- Thank You Message Below Card -->
            <div style="text-align: center; margin-top: 20px; margin-bottom: 30px; color: #1f2937; font-size: 15px;">
                <p>Thank you for your payment. We appreciate your business!</p>
            </div>

            <div style="margin-top: 30px; text-align: center; color: #6b7280; font-size: 14px;">
                <p>If you have any questions, contact us at <a href="mailto:support@snapay.co.ke">support@snapay.co.ke</a></p>
            </div>
        </div>

        <div class="footer" style="border-top: 1px solid #037b90;">
            &copy; {{ date('Y') }} {{ $clientName ?? 'Snapay' }}. All rights reserved.<br>
            <a href="#">Terms</a> | <a href="#">Privacy</a>
        </div>
    </div>
</body>
</html>
