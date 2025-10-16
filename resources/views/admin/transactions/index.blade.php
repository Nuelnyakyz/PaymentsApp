<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-primary leading-tight">Transactions</h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">

    <div class="mb-6 bg-white shadow rounded">
        <div class="p-4 overflow-x-auto sm:overflow-x-hidden no-scrollbar">
    <form method="GET" id="transactions-filter-form" class="flex items-center gap-4 whitespace-nowrap flex-nowrap sm:flex-wrap sm:whitespace-normal w-full">
        <input class="border rounded p-2 w-auto shrink-0 min-w-[16rem] sm:min-w-0 sm:flex-1" type="text" name="q" value="{{ request('q') }}" placeholder="Search txn/check/merchant id or phone" />
        <select class="border rounded p-2 pr-10 w-auto shrink-0 min-w-[10rem] sm:min-w-0" name="status">
            <option value="">All Status</option>
            @foreach (['initiated','pending','success','failed','cancelled'] as $s)
                <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <input class="border rounded p-2 w-auto shrink-0 min-w-[10rem] sm:min-w-0" type="date" name="from" value="{{ request('from') }}" />
        <input class="border rounded p-2 w-auto shrink-0 min-w-[10rem] sm:min-w-0" type="date" name="to" value="{{ request('to') }}" />
        <input class="border rounded p-2 w-auto shrink-0 min-w-[10rem] sm:min-w-0" type="number" step="0.01" name="min" value="{{ request('min') }}" placeholder="Min amount" />
        <input class="border rounded p-2 w-auto shrink-0 min-w-[10rem] sm:min-w-0" type="number" step="0.01" name="max" value="{{ request('max') }}" placeholder="Max amount" />
    </form>
        </div>
    </div>

    <div class="bg-white shadow rounded">
        <div class="p-6 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-primary border-b">
                    <th class="py-2 pr-4">Txn ID</th>
                    <th class="py-2 pr-4">Merchant Req</th>
                    <th class="py-2 pr-4">Checkout Req</th>
                    <th class="py-2 pr-4">Amount</th>
                    <th class="py-2 pr-4">Phone</th>
                    <th class="py-2 pr-4">Status</th>
                    <th class="py-2 pr-4">Created</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $t)
                    <tr class="border-b">
                        <td class="py-2 pr-4">{{ $t->transaction_id ?? '-' }}</td>
                        <td class="py-2 pr-4">{{ $t->merchant_request_id ?? '-' }}</td>
                        <td class="py-2 pr-4">{{ $t->checkout_request_id ?? '-' }}</td>
                        <td class="py-2 pr-4">{{ number_format($t->amount, 2) }}</td>
                        <td class="py-2 pr-4">{{ $t->phone }}</td>
                        <td class="py-2 pr-4">
                            <span class="{{ in_array(strtolower($t->status), ['success']) ? 'text-green' : (in_array(strtolower($t->status), ['failed','cancelled']) ? 'text-red' : (in_array(strtolower($t->status), ['pending']) ? 'text-orange' : '')) }}">
                                {{ ucfirst($t->status) }}
                            </span>
                        </td>
                        <td class="py-2 pr-4">{{ $t->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr><td class="py-4 text-gray-500" colspan="7">No records.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-4">{{ $transactions->links() }}</div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('#transactions-filter-form');
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
