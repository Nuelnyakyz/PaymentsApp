<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-primary leading-tight">Receipt</h2>
    </x-slot>

    @push('head')
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
        <style>
            .receipt-page, .receipt-page * { font-family: 'Quicksand', ui-rounded, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important; }
            .receipt-page { font-size: 1.02rem; }
            .receipt-page .value { font-weight: 700; }
            .receipt-page .heading { font-size: 36px; font-weight: 800; }
        </style>
    @endpush

    <div class="receipt-page py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded p-8">
            <style>
                .brand { display: inline-flex; align-items: center; gap: 10px; font-weight: 700; letter-spacing: 0.3px; color: #0b1220; font-size: 20px; }
                .brand img { height: 22px; }
                .heading { font-size: 34px; font-weight: 700; margin: 32px 0 30px; }
                .pair { display: flex; justify-content: space-between; align-items: flex-start; margin: 18px 0 30px; }
                .left { text-align: left; }
                .row-between { display: flex; justify-content: space-between; align-items: flex-start; margin: 18px 0 34px; }
                .row-between .left, .row-between .right { display: flex; flex-direction: column; gap: 2px; }
                .label { color: #6b7280; font-size: 13px; line-height: 1.35; }
                .value { font-size: 16px; font-weight: 600; line-height: 1.35; }
                .muted { color: #6b7280; font-size: 12px; line-height: 1.4; }
                .card { background: #eeeeef; border-radius: 12px; padding: 26px; }
                .item-row { display: flex; justify-content: space-between; align-items: flex-start; gap: 18px; }
                .item-row .stack { display: flex; flex-direction: column; justify-content: flex-start; flex: 1 1 auto; min-width: 0; }
                .item-row .amount { align-self: flex-start; text-align: right; white-space: nowrap; font-size: 16px; font-weight: 700; line-height: 1.35; }
                .align-value { padding-top: 18px; }
                .sub { color: #6b7280; font-size: 11px; margin-top: 2px; }
                .divider { height: 1px; background: #d1d5db; border: 0; margin: 22px 0; }
            </style>

            <div class="topbar">
                <div class="brand">
                    <img src="{{ asset('Snapay.png') }}" alt="Logo">
                    <span>SNAPAY</span>
                </div>
            </div>

            <div class="heading">Receipt</div>

            <div class="pair">
                <div class="left">
                    <div class="value">{{ $clientName ?? 'Snap Learn' }}</div>
                    <div class="muted">P.O Box 999, Nairobi</div>
                    <div class="muted">Mombasa Road, Nairobi</div>
                    <div class="muted">email@support.com</div>
                </div>
                <div class="right">
                    <div class="label">Purchase Date</div>
                    <div class="value">{{ optional($receipt->issued_at)->format('j F Y') }}</div>
                </div>
            </div>

            <div class="row-between">
                <div class="left">
                    <div class="label">Invoice No</div>
                    <div class="value">{{ optional($receipt->payment)->reference ?? '-' }}</div>
                </div>
                <div class="right">
                    <div class="label">Transaction code</div>
                    <div class="value">{{ $receipt->receipt_number }}</div>
                </div>
            </div>

            <div class="card">
                @php
                    $total = (float)($receipt->amount ?? 0);
                    $tax = 0.00;
                    $itemAmount = $total;
                    $phone = $receipt->payer_phone;
                    $masked = $phone ? (strlen($phone) > 4 ? str_repeat('*', max(strlen($phone)-4, 0)) . substr($phone, -4) : $phone) : '-';
                @endphp
                <div class="item-row">
                    <div class="stack">
                        <div class="label">Items</div>
                        <div class="value">{{ $receipt->course_name ?? 'Course' }}</div>
                        <div class="sub">1 course</div>
                    </div>
                    <div class="amount align-value">Ksh {{ number_format($itemAmount, 2) }}</div>
                </div>

                <div style="height:30px"></div>

                <div class="item-row">
                    <div class="stack">
                        <div class="label">Total tax</div>
                        <div class="sub">VAT (16%)</div>
                    </div>
                    <div class="amount">Ksh {{ number_format($tax, 2) }}</div>
                </div>

                <div style="height:30px"></div>

                <div class="item-row">
                    <div class="stack">
                        <div class="label">Payment method</div>
                        <div class="value">{{ strtoupper(optional($receipt->payment)->payment_method ?? 'N/A') }}</div>
                        <div class="value">{{ $masked ? '**** '.substr($masked, -4) : '' }}</div>
                    </div>
                </div>

                <hr class="divider">

                <div class="item-row">
                    <div class="stack">
                        <div class="label">Total</div>
                    </div>
                    <div class="amount">Ksh {{ number_format($total, 2) }}</div>
                </div>
            </div>

            <div class="mt-8 text-gray-600 text-sm">Your purchase is subject to our <u>Terms & Conditions</u>.</div>

            <div class="flex items-center justify-between mt-8">
                <a href="{{ route('admin.receipts.index') }}" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-50">Back to Receipts</a>
                <div class="space-x-2">
                    <a href="{{ route('admin.receipts.pdf', $receipt) }}" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded hover:opacity-90">Download PDF</a>
                    <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border rounded text-gray-700 hover:bg-gray-50">Print</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
