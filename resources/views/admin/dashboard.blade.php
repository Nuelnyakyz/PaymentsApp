<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-primary leading-tight">Admin Dashboard</h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-8 m-2">
        <div class="p-6 bg-white shadow rounded">
            <div class="text-gray-500">Payments Today</div>
            <div class="text-3xl font-bold text-secondary">{{ $paymentsToday }}</div>
        </div>
        <div class="p-6 bg-white shadow rounded">
            <div class="text-gray-500">Successful Today</div>
            <div class="text-3xl font-bold text-green">{{ $successfulToday }}</div>
        </div>
        <div class="p-6 bg-white shadow rounded">
            <div class="text-gray-500">Amount Collected Today</div>
            <div class="text-3xl font-bold text-primary">{{ number_format($amountToday, 2) }}</div>
        </div>
    </div>

    <div class="bg-white shadow rounded">
        <div class="p-6 border-b">
            <h2 class="text-xl font-semibold text-secondary">{{ $recentPayments->currentPage() === 1 ? 'Recent Payments' : 'Payments' }}</h2>
        </div>
        <div class="p-6 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-primary border-b">
                        <th class="py-2 pr-4">Reference</th>
                        <th class="py-2 pr-4">Student</th>
                        <th class="py-2 pr-4">Payer</th>
                        <th class="py-2 pr-4">Method</th>
                        <th class="py-2 pr-4">Amount</th>
                        <th class="py-2 pr-4">Status</th>
                        <th class="py-2 pr-4">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentPayments as $payment)
                        <tr class="border-b">
                            <td class="py-2 pr-4">{{ $payment->reference }}</td>
                            <td class="py-2 pr-4">{{ $payment->student_full_name }}</td>
                            <td class="py-2 pr-4">{{ $payment->payer_name }}</td>
                            <td class="py-2 pr-4 uppercase">{{ $payment->payment_method }}</td>
                            <td class="py-2 pr-4">{{ number_format($payment->amount, 2) }}</td>
                            <td class="py-2 pr-4">
                                <span class="{{ in_array(strtolower($payment->status), ['success']) ? 'text-green' : (in_array(strtolower($payment->status), ['failed','cancelled']) ? 'text-red' : (in_array(strtolower($payment->status), ['pending']) ? 'text-orange' : '')) }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td class="py-2 pr-4">{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td class="py-4 text-gray-500" colspan="7">No payments yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-6 border-t">
            {{ $recentPayments->links() }}
        </div>
    </div>
    </div>
</x-app-layout>
