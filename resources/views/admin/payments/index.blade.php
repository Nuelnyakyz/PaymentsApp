<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-primary leading-tight">Payments</h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">

    <div class="mb-6 bg-white shadow rounded">
        <div class="p-4 overflow-x-auto no-scrollbar">
    <form method="GET" id="payments-filter-form" class="flex items-center gap-4 whitespace-nowrap flex-nowrap min-w-max">
        <input class="border rounded p-2 w-auto min-w-64 shrink-0" type="text" name="q" value="{{ request('q') }}" placeholder="Search ref, student, payer, phone, course" />
        <select class="border rounded p-2 pr-10 w-auto min-w-40 shrink-0" name="status">
            <option value="">All Status</option>
            @foreach (['pending','success','failed','cancelled'] as $s)
                <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <select class="border rounded p-2 pr-10 w-auto min-w-40 shrink-0" name="method">
            <option value="">All Methods</option>
            @foreach (['mpesa','airtel','card','ecitizen'] as $m)
                <option value="{{ $m }}" @selected(request('method')===$m)>{{ strtoupper($m) }}</option>
            @endforeach
        </select>
        <input class="border rounded p-2 w-auto min-w-40 shrink-0" type="date" name="from" value="{{ request('from') }}" />
        <input class="border rounded p-2 w-auto min-w-40 shrink-0" type="date" name="to" value="{{ request('to') }}" />
    </form>
        </div>
    </div>

    <div class="bg-white shadow rounded">
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
                @forelse ($payments as $payment)
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
                    <tr><td class="py-4 text-gray-500" colspan="7">No records.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-4">{{ $payments->links() }}</div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('#payments-filter-form');
        if (!form) return;
        let t;
        const autoSubmit = () => form.requestSubmit();
        form.querySelectorAll('select,input[type="date"],input[type="number"]').forEach(el => {
            el.addEventListener('change', autoSubmit);
        });
        const q = form.querySelector('input[name="q"]');
        if (q) {
            q.addEventListener('input', () => {
                clearTimeout(t);
                t = setTimeout(autoSubmit, 400);
            });
        }
    });
    </script>
    </div>
</x-app-layout>
