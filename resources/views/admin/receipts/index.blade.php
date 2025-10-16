<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-primary leading-tight">Receipts</h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">

    <div class="mb-6 bg-white shadow rounded">
        <div class="p-4 overflow-x-auto no-scrollbar">
    <form method="GET" id="receipts-filter-form" class="flex items-center gap-4 whitespace-nowrap flex-nowrap min-w-max">
        <input class="border rounded p-2 w-auto shrink-0 min-w-[16rem]" type="text" name="q" value="{{ request('q') }}" placeholder="Search receipt no, student, payer, phone, course" />
        <input class="border rounded p-2 w-auto shrink-0 min-w-[10rem]" type="date" name="from" value="{{ request('from') }}" />
        <input class="border rounded p-2 w-auto shrink-0 min-w-[10rem]" type="date" name="to" value="{{ request('to') }}" />
    </form>
        </div>
    </div>

    <div class="bg-white shadow rounded">
        <div class="p-6 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-primary border-b">
                    <th class="py-2 pr-4">Receipt #</th>
                    <th class="py-2 pr-4">Student</th>
                    <th class="py-2 pr-4">Payer</th>
                    <th class="py-2 pr-4">Amount</th>
                    <th class="py-2 pr-4">Course</th>
                    <th class="py-2 pr-4">Issued At</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($receipts as $r)
                    <tr class="border-b">
                        <td class="py-2 pr-4">{{ $r->receipt_number }}</td>
                        <td class="py-2 pr-4">{{ $r->student_full_name }}</td>
                        <td class="py-2 pr-4">{{ $r->payer_name }} ({{ $r->payer_phone }})</td>
                        <td class="py-2 pr-4">{{ number_format($r->amount, 2) }}</td>
                        <td class="py-2 pr-4">{{ $r->course_name ?? '-' }}</td>
                        <td class="py-2 pr-4">{{ optional($r->issued_at)->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr><td class="py-4 text-gray-500" colspan="6">No records.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-4">{{ $receipts->links() }}</div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('#receipts-filter-form');
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
