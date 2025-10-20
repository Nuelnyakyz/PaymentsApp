<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-primary leading-tight">Receipt</h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded p-8">
            <div class="flex items-start justify-between mb-8">
                <div>
                    <div class="text-2xl font-bold tracking-wide">OPEN UNIVERSITY OF KENYA</div>
                    <div class="text-gray-600">Official Payment Receipt</div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">Receipt #</div>
                    <div class="text-xl font-semibold">{{ $receipt->receipt_number }}</div>
                    <div class="text-sm text-gray-500">Issued {{ optional($receipt->issued_at)->format('Y-m-d H:i') }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="space-y-1">
                    <div class="text-gray-500 text-sm">Student Name</div>
                    <div class="font-medium">{{ $receipt->student_full_name }}</div>
                </div>
                <div class="space-y-1">
                    <div class="text-gray-500 text-sm">Student Email</div>
                    <div class="font-medium">{{ optional($receipt->payment)->student_email ?? '-' }}</div>
                </div>
                <div class="space-y-1">
                    <div class="text-gray-500 text-sm">Course</div>
                    <div class="font-medium">{{ $receipt->course_name ?? '-' }}</div>
                </div>
                <div class="space-y-1">
                    <div class="text-gray-500 text-sm">Amount Paid</div>
                    <div class="font-semibold text-lg">KES {{ number_format($receipt->amount, 2) }}</div>
                </div>
            </div>

            <div class="border-t pt-6 grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="space-y-1">
                    <div class="text-gray-500 text-sm">Payment Service</div>
                    <div class="font-medium">{{ strtoupper(optional($receipt->payment)->payment_method ?? 'N/A') }}</div>
                </div>
                <div class="space-y-1">
                    <div class="text-gray-500 text-sm">Phone/Card</div>
                    @php
                        $phone = $receipt->payer_phone ?: optional($receipt->payment)->student_phone;
                        $masked = $phone ? (strlen($phone) > 4 ? str_repeat('*', max(strlen($phone)-4, 0)) . substr($phone, -4) : $phone) : '-';
                    @endphp
                    <div class="font-medium">{{ $masked }}</div>
                </div>
                <div class="space-y-1">
                    <div class="text-gray-500 text-sm">Payer</div>
                    <div class="font-medium">{{ $receipt->payer_name }}{{ $receipt->payer_phone ? ' ('.$receipt->payer_phone.')' : '' }}</div>
                </div>
                <div class="space-y-1">
                    <div class="text-gray-500 text-sm">Reference</div>
                    <div class="font-medium">{{ optional($receipt->payment)->reference ?? '-' }}</div>
                </div>
            </div>

            <div class="mb-8">
                <div class="text-gray-600 text-sm">This is an electronically generated receipt for a successful transaction. No signature is required.</div>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('admin.receipts.index') }}" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-50">Back to Receipts</a>
                <div class="space-x-2">
                    <a href="{{ route('admin.receipts.pdf', $receipt) }}" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded hover:opacity-90">Download PDF</a>
                    <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border rounded text-gray-700 hover:bg-gray-50">Print</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
