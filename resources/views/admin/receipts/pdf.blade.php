<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt {{ $receipt->receipt_number }}</title>
    <style>
        @page { margin: 24mm 18mm; }
        /* Use DomPDF-bundled font for reliability */
        body { font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; color: #111827; font-size: 15px; }
        html, body, * { font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif !important; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
        .brand { font-size: 22px; font-weight: 700; letter-spacing: 0.04em; }
        .subtitle { color: #6b7280; font-size: 12px; }
        .box { border: 1px solid #e5e7eb; border-radius: 6px; padding: 16px; margin-bottom: 14px; }
        .row { display: flex; flex-wrap: wrap; gap: 16px; }
        .col { flex: 1 1 45%; }
        .label { color: #6b7280; font-size: 13.5px; line-height: 1.35; }
        .value { font-size: 17px; font-weight: 650; line-height: 1.35; }
        .amount { font-size: 17px; font-weight: 750; text-align: right; line-height: 1.35; }
        .muted { color: #6b7280; font-size: 12px; line-height: 1.4; }
        .right { text-align: right; }
        .title { font-size: 18px; font-weight: 700; margin: 0; }
        .hr { height: 1px; background: #e5e7eb; border: 0; margin: 16px 0; }
        .footer { margin-top: 18px; }
        /* Snapay layout additions */
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; }
        .brand { display: inline-flex; align-items: center; gap: 10px; font-weight: 700; letter-spacing: 0.3px; color: #0b1220; font-size: 20px; font-family: inherit; }
        .brand img { height: 22px; }
        .brand span { display: inline-block; }
        .brand-name { font-size: 18px; font-weight: 700; letter-spacing: 0.02em; }
        .heading { font-size: 36px; font-weight: 800; margin: 32px 0 30px; }
        .pair { display: flex; justify-content: space-between; align-items: flex-start; margin: 18px 0 30px; }
        .left { text-align: left; }
        .row-between { display: flex; justify-content: space-between; align-items: flex-start; margin: 18px 0 34px; }
        .row-between .left, .row-between .right { display: flex; flex-direction: column; gap: 2px; }
        .card { background: #eeeeef; border-radius: 12px; padding: 26px; }
        /* table-based two-column rows for DomPDF reliability */
        .twocol { width: 100%; border-collapse: collapse; }
        .twocol td { vertical-align: top; padding: 0; }
        .amount-cell { text-align: right; white-space: nowrap; }
        .align-value { padding-top: 18px; }
        .item-row { display: flex; justify-content: space-between; align-items: flex-start; gap: 18px; }
        .item-row .label,
        .item-row .value,
        .item-row .sub { margin: 0; }
        .item-row .stack { display: flex; flex-direction: column; justify-content: flex-start; flex: 1 1 auto; min-width: 0; }
        .item-row .amount { align-self: flex-start; text-align: right; white-space: nowrap; }
        .sub { color: #6b7280; font-size: 11px; margin-top: 2px; }
        .divider { height: 1px; background: #d1d5db; border: 0; margin: 22px 0; }
        .total-row { display: flex; justify-content: space-between; align-items: center; font-size: 13px; font-weight: 600; gap: 12px; }
        /* normalize strong/b to available Quicksand weight */
        strong, b { font-weight: 700; font-family: inherit; }
        html, body, * { font-family: 'Quicksand', DejaVu Sans, Arial, Helvetica, sans-serif !important; }
    </style>
</head>
<body>
    <?php
        $logoPath = public_path('Snapay.png');
        $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
    ?>
    <div class="topbar">
        <div class="brand">
            <img src="{{ $logoData ? ('data:image/png;base64,'.$logoData) : public_path('Snapay.png') }}" alt="Logo">
            <span>SNAPAY</span>
        </div>
    </div>

    <div class="heading">Receipt</div>

    <div class="pair">
        <div class="left">
            <div class="value font-size-4xl">{{ $clientName ?? 'Snap Learn' }}</div>
            <div class="muted">P.O Box 999, Nairobi</div>
            <div class="muted">Mombasa Road, Nairobi</div>
            <div class="muted">email@support.com</div>
        </div>
        <div class="right">
            <div class="label">Purchase Date</div>
            <div class="value">{{ optional($receipt->issued_at)->format('j F Y') }}</div>
            <div class="muted">{{ optional($receipt->issued_at)->format('g:i A') }}</div>
        </div>
    </div>

    <table class="twocol" style="margin: 14px 0 28px;">
        <tr>
            <td>
                <div class="label">Invoice No</div>
                <div class="value">{{ optional($receipt->payment)->reference ?? '-' }}</div>
            </td>
            <td class="amount-cell">
                <div class="label">Transaction code</div>
                <div class="value">{{ $receipt->receipt_number }}</div>
            </td>
        </tr>
    </table>

    <div class="card">
        <?php
            $total = (float)($receipt->amount ?? 0);
            $tax = 0.00; // Hardcoded VAT for now
            $itemAmount = $total; // Show full amount as fetched from DB
            $phone = $receipt->payer_phone;
            $masked = $phone ? (strlen($phone) > 4 ? str_repeat('*', max(strlen($phone)-4, 0)) . substr($phone, -4) : $phone) : '-';
        ?>
        <table class="twocol">
            <tr>
                <td><div class="label">Items</div></td>
                <td class="amount-cell"></td>
            </tr>
            <tr>
                <td><div class="value">{{ $receipt->course_name ?? 'Course' }}</div></td>
                <td class="amount-cell amount">Ksh {{ number_format($itemAmount, 2) }}</td>
            </tr>
            <tr>
                <td><div class="sub">1 course</div></td>
                <td class="amount-cell"></td>
            </tr>
        </table>

        <div style="height:20px"></div>

        <table class="twocol" style="margin-top:20px;">
            <tr>
                <td>
                    <div class="label">Total tax</div>
                    <div class="sub">VAT (16%)</div>
                </td>
                <td class="amount-cell amount">Ksh {{ number_format($tax, 2) }}</td>
            </tr>
        </table>

        <div style="height:30px"></div>

        <div class="item-row">
            <div class="stack">
                <div class="label">Payment method</div>
                <div class="value">{{ strtoupper(optional($receipt->payment)->payment_method ?? 'N/A') }}</div>
                <div class="value">{{ $masked ? '**** '.substr($masked, -4) : '' }}</div>
            </div>
        </div>

        <hr class="divider">

        <table class="twocol">
            <tr>
                <td>
                    <div class="label">Total</div>
                </td>
                <td class="amount-cell amount">Ksh {{ number_format($total, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <div class="muted">Your purchase is subject to our <u>Terms & Conditions</u>.</div>
    </div>
</body>
</html>