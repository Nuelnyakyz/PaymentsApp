<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-primary leading-tight text-secondary">Payments</h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">

    <div class="mb-6 bg-white shadow rounded">
        <div class="p-4 overflow-x-auto sm:overflow-x-hidden no-scrollbar">
    @php($selectedStatus = isset($defaultStatus) ? $defaultStatus : (request()->has('status') ? request('status') : 'success'))
    <form method="GET" id="transactions-filter-form" class="flex items-center gap-4 whitespace-nowrap flex-nowrap sm:flex-wrap sm:whitespace-normal w-full border-primary">
        <input class="border border-primary rounded p-2 w-auto shrink-0 min-w-[16rem] sm:min-w-0 sm:flex-1" type="text" name="q" value="{{ request('q') }}" placeholder="Search txn/check/merchant id or phone" />
        <select class="border border-primary rounded p-2 pr-10 w-auto shrink-0 min-w-[10rem] sm:min-w-0" name="status">
            <option value="" @selected($selectedStatus==='')>All Status</option>
            @foreach (["initiated","pending","success","failed","cancelled"] as $s)
                <option value="{{ $s }}" @selected($selectedStatus===$s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <select class="border border-primary rounded p-2 pr-10 w-auto shrink-0 min-w-[10rem] sm:min-w-0" name="method">
            <option value="" @selected(request('method','')==='')>All Methods</option>
            @foreach (($methods ?? []) as $m)
                <option value="{{ $m }}" @selected(request('method')===$m)>{{ strtoupper($m) }}</option>
            @endforeach
        </select>
        <input class="border border-primary rounded p-2 w-auto shrink-0 min-w-[10rem] sm:min-w-0" type="date" name="from" value="{{ request('from') }}" />
        <input class="border border-primary rounded p-2 w-auto shrink-0 min-w-[10rem] sm:min-w-0" type="date" name="to" value="{{ request('to') }}" />
        <input class="border border-primary rounded p-2 w-auto shrink-0 min-w-[10rem] sm:min-w-0" type="number" step="0.01" name="min" value="{{ request('min') }}" placeholder="Min amount" />
        <input class="border border-primary rounded p-2 w-auto shrink-0 min-w-[10rem] sm:min-w-0" type="number" step="0.01" name="max" value="{{ request('max') }}" placeholder="Max amount" />
    </form>
        </div>
    </div>

    <div class="bg-white shadow rounded">
        <div class="p-6 overflow-x-auto">
        <table class="min-w-[1800px] w-full text-sm border-separate border-spacing-x-8 border-spacing-y-1">
            <thead>
                <tr class="text-left text-primary bg-gray-50">
                    <th class="py-2 pr-9 font-semibold min-w-[10rem]">Reference</th>
                    <th class="py-2 pr-9 font-semibold min-w-[12rem]">Student</th>
                    <th class="py-2 pr-9 font-semibold min-w-[12rem]">Payer</th>
                    <th class="py-2 pr-9 font-semibold min-w-[3rem]">Phone</th>
                    <th class="py-2 pr-9 font-semibold min-w-[3rem]">Method</th>
                    <th class="py-2 pr-9 font-semibold min-w-[5rem]">Amount</th>
                    <th class="py-2 pr-9 font-semibold min-w-[3rem]">Status</th>
                    <th class="py-2 pr-9 font-semibold min-w-[8rem]">Created</th>
                    <th class="py-2 pr-9 font-semibold min-w-[5rem]">Txn ID</th>
                    <th class="py-2 pr-9 font-semibold min-w-[3rem]"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($transactions as $t)
                    @php($p = $t->payment)
                    <tr class="whitespace-nowrap hover:bg-gray-50">
                        <td class="py-2 pr-9">{{ $p->reference ?? '-' }}</td>
                        <td class="py-2 pr-9">{{ $p->student_full_name ?? '-' }}</td>
                        <td class="py-2 pr-9">{{ $p->payer_name ?? '-' }}</td>
                        <td class="py-2 pr-9">{{ $p->payer_phone ?? $t->phone ?? '-' }}</td>
                        <td class="py-2 pr-9 uppercase">{{ $p->payment_method ?? '-' }}</td>
                        <td class="py-2 pr-9 text-secondary font-bold">{{ number_format($p->amount ?? $t->amount, 2) }}</td>
                        <td class="py-2 pr-9">
                            @php($status = strtolower($p->status ?? $t->status))
                            <span class="{{ in_array($status, ['success']) ? 'text-green' : (in_array($status, ['failed','cancelled']) ? 'text-red' : (in_array($status, ['pending']) ? 'text-orange' : '')) }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                        <td class="py-2 pr-9 tabular-nums">{{ optional($p->created_at ?? $t->created_at)->format('Y-m-d H:i') }}</td>
                        <td class="py-2 pr-9 font-mono text-xs">{{ $t->transaction_id ?? '-' }}</td>
                        <td class="py-2 pr-9 text-right">
                            @php($status = strtolower($p->status ?? $t->status))
                            <form method="POST" action="{{ route('admin.payments.resend', $p) }}" onsubmit="return confirm('Resend callback to client app?');">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded bg-primary text-white hover:opacity-90 disabled:opacity-40 disabled:cursor-not-allowed" @disabled($status !== 'success')>
                                    Resend Notification
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td class="py-4 pr-12 text-gray-500" colspan="11">No records.</td></tr>
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
