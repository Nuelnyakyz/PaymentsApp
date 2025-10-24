<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt {{ $receipt->receipt_number }}</title>
    <style>
        @page { margin: 24mm 18mm; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; color: #111827; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
        .brand { font-size: 22px; font-weight: 700; letter-spacing: 0.04em; }
        .subtitle { color: #6b7280; font-size: 12px; }
        .box { border: 1px solid #e5e7eb; border-radius: 6px; padding: 16px; margin-bottom: 14px; }
        .row { display: flex; flex-wrap: wrap; gap: 16px; }
        .col { flex: 1 1 45%; }
        .label { color: #6b7280; font-size: 11px; margin-bottom: 4px; }
        .value { font-size: 13px; font-weight: 600; }
        .amount { font-size: 16px; font-weight: 700; }
        .muted { color: #6b7280; font-size: 11px; }
        .right { text-align: right; }
        .title { font-size: 18px; font-weight: 700; margin: 0; }
        .hr { height: 1px; background: #e5e7eb; border: 0; margin: 16px 0; }
        .footer { margin-top: 18px; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="brand">OPEN UNIVERSITY OF KENYA</div>
            <div class="subtitle">Official Payment Receipt</div>
        </div>
        <div class="right">
            <div class="subtitle">Receipt #</div>
            <div class="title">{{ $receipt->receipt_number }}</div>
            <div class="subtitle">Issued {{ optional($receipt->issued_at)->format('Y-m-d H:i') }}</div>
        </div>
    </div>

    <div class="box">
        <div class="row">
            <div class="col">
                <div class="label">Student Name</div>
                <div class="value">{{ $receipt->student_full_name }}</div>
            </div>
            <div class="col">
                <div class="label">Student Email</div>
                <div class="value">{{ optional($receipt->payment)->student_email ?? '-' }}</div>
            </div>
            <div class="col">
                <div class="label">Course</div>
                <div class="value">{{ $receipt->course_name ?? '-' }}</div>
            </div>
            <div class="col">
                <div class="label">Amount Paid</div>
                <div class="amount">KES {{ number_format($receipt->amount, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="row">
            <div class="col">
                <div class="label">Payment Service</div>
                <div class="value">{{ strtoupper(optional($receipt->payment)->payment_method ?? 'N/A') }}</div>
            </div>
            <div class="col">
                <div class="label">Phone/Card</div>
                <?php
                    $phone = $receipt->payer_phone;
                    $masked = $phone ? (strlen($phone) > 4 ? str_repeat('*', max(strlen($phone)-4, 0)) . substr($phone, -4) : $phone) : '-';
                ?>
                <div class="value">{{ $masked }}</div>
            </div>
            <div class="col">
                <div class="label">Payer</div>
                <div class="value">{{ $receipt->payer_name }}{{ $receipt->payer_phone ? ' ('.$receipt->payer_phone.')' : '' }}</div>
            </div>
            <div class="col">
                <div class="label">Reference</div>
                <div class="value">{{ optional($receipt->payment)->reference ?? '-' }}</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="muted">This is an electronically generated receipt for a successful transaction. No signature is required.</div>
    </div>
</body>
</html>
