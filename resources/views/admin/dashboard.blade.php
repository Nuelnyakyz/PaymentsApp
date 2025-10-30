<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-primary leading-tight text-secondary">Dashboard</h2>
    </x-slot>

    @push('head')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
        <style>
            .mini-chart { height: 90px !important; }
        </style>
    @endpush

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded p-6 mb-10">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <div class="text-2xl font-bold text-gray-800">Welcome back, <span class="text-primary">{{ Auth::user()->name }}</span></div>
                    <div class="text-sm text-gray-500 mt-1">Here's how the numbers are looking.</div>
                </div>
                <div class="hidden md:block">
                    <svg width="240" height="90" viewBox="0 0 240 90" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <rect x="0" y="10" width="70" height="18" rx="4" fill="#E6F4F6"/>
                        <rect x="8" y="16" width="46" height="6" rx="3" fill="#A6D8E0"/>
                        <rect x="160" y="0" width="70" height="18" rx="4" fill="#FFE7DF"/>
                        <rect x="168" y="6" width="46" height="6" rx="3" fill="#FFB199"/>
                        <g transform="translate(120,28)">
                            <rect x="26" y="24" width="46" height="4" rx="2" fill="#E5E7EB"/>
                            <circle cx="20" cy="20" r="20" fill="#F3F4F6"/>
                            <rect x="48" y="12" width="70" height="38" rx="6" fill="#F9FAFB" stroke="#E5E7EB"/>
                            <rect x="56" y="20" width="54" height="6" rx="3" fill="#D1D5DB"/>
                            <rect x="56" y="30" width="44" height="6" rx="3" fill="#E5E7EB"/>
                            <rect x="12" y="42" width="16" height="6" rx="3" fill="#D1D5DB"/>
                        </g>
                        <g transform="translate(95,52)">
                            <rect x="0" y="20" width="120" height="2" fill="#EEF2F7"/>
                        </g>
                    </svg>
                </div>
            </div>
        </div>

    <div class="grid grid-cols-2 lg:grid-cols-3 items-stretch gap-3 mb-8 m-2">
        <div class="p-6 bg-white shadow rounded h-full">
            <div class="text-gray-500">Payments Today</div>
            <div class="text-3xl font-bold text-secondary">{{ $paymentsToday }}</div>
        </div>
        <div class="p-6 bg-white shadow rounded h-full">
            <div class="text-gray-500">Successful Today</div>
            <div class="text-3xl font-bold text-green">{{ $successfulToday }}</div>
        </div>
        <div class="p-6 bg-white shadow rounded h-full col-span-2 lg:col-span-1">
            <div class="text-gray-500">Amount Collected Today</div>
            <div class="text-3xl font-bold text-primary">KSH {{ number_format($amountToday, 2) }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mt-8 mb-8 m-2">
        <div class="bg-white shadow rounded p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="text-gray-600">This Week</div>
                <div class="text-xs font-semibold bg-red-50 text-primary px-2 py-1 rounded">KSH {{ number_format($charts['week']['total'] ?? 0, 2) }}</div>
            </div>
            <canvas id="chart-week" class="mini-chart"></canvas>
            <div class="mt-2 text-xs text-gray-500 flex justify-between"><span>Monday</span><span>Today</span></div>
        </div>
        <div class="bg-white shadow rounded p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="text-gray-600">This Month</div>
                <div class="text-xs font-semibold bg-red-50 text-primary px-2 py-1 rounded">KSH {{ number_format($charts['month']['total'] ?? 0, 2) }}</div>
            </div>
            <canvas id="chart-month" class="mini-chart"></canvas>
            <div class="mt-2 text-xs text-gray-500 flex justify-between"><span>Week1</span><span>Today</span></div>
        </div>
        <div class="bg-white shadow rounded p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="text-gray-600">This Year</div>
                <div class="text-xs font-semibold bg-red-50 text-primary px-2 py-1 rounded">KSH {{ number_format($charts['year']['total'] ?? 0, 2) }}</div>
            </div>
            <canvas id="chart-year" class="mini-chart"></canvas>
            <div class="mt-2 text-xs text-gray-500 flex justify-between"><span>January</span><span>Today</span></div>
        </div>
        <div class="bg-white shadow rounded p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="text-gray-600">All Time</div>
                <div class="text-xs font-semibold bg-red-50 text-primary px-2 py-1 rounded">KSH {{ number_format($charts['all']['total'] ?? 0, 2) }}</div>
            </div>
            <canvas id="chart-all" class="mini-chart"></canvas>
            <div class="mt-2 text-xs text-gray-500 flex justify-between"><span>Start Date</span><span>Today</span></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const theme = {
                primary: getComputedStyle(document.documentElement).getPropertyValue('--tw-color-primary') || '#037b90',
                secondary: '#ff7f50'
            };
            const makeConfig = (labels, data) => ({
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        data,
                        borderColor: theme.secondary,
                        backgroundColor: 'rgba(255,127,80,0.12)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { enabled: false } },
                    scales: { x: { display: false }, y: { display: false } }
                }
            });

            const payload = @json($charts ?? []);
            if (payload.week) new Chart(document.getElementById('chart-week'), makeConfig(payload.week.labels, payload.week.data));
            if (payload.month) new Chart(document.getElementById('chart-month'), makeConfig(payload.month.labels, payload.month.data));
            if (payload.year) new Chart(document.getElementById('chart-year'), makeConfig(payload.year.labels, payload.year.data));
            if (payload.all) new Chart(document.getElementById('chart-all'), makeConfig(payload.all.labels, payload.all.data));
        });
    </script>

    <div class="bg-white shadow rounded">
        <div class="p-6 border-b">
            <h2 class="text-xl font-semibold text-secondary">Recent Payments</h2>
            <div class="text-xs text-gray-500">Showing last 10 successful payments</div>
        </div>
        <div class="p-6 overflow-x-auto">
            <table class="min-w-[1400px] w-full text-sm border-separate border-spacing-x-8 border-spacing-y-1">
                <thead>
                    <tr class="text-left text-primary bg-gray-50">
                        <th class="py-2 pr-9 font-semibold min-w-[10rem]">Reference</th>
                        <th class="py-2 pr-9 font-semibold min-w-[12rem]">Student</th>
                        <th class="py-2 pr-9 font-semibold min-w-[12rem]">Payer</th>
                        <th class="py-2 pr-9 font-semibold min-w-[12rem]">Phone</th>
                        <th class="py-2 pr-9 font-semibold min-w-[6rem]">Method</th>
                        <th class="py-2 pr-9 font-semibold min-w-[6rem]">Amount</th>
                        <th class="py-2 pr-9 font-semibold min-w-[6rem]">Status</th>
                        <th class="py-2 pr-9 font-semibold min-w-[10rem]">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($recentPayments as $payment)
                        <tr class="whitespace-nowrap hover:bg-gray-50">
                            <td class="py-2 pr-9">{{ $payment->reference }}</td>
                            <td class="py-2 pr-9">{{ $payment->student_full_name }}</td>
                            <td class="py-2 pr-9">{{ $payment->payer_name }}</td>
                            <td class="py-2 pr-9">{{ $payment->payer_phone }}</td>
                            <td class="py-2 pr-9 uppercase">{{ $payment->payment_method }}</td>
                            <td class="py-2 pr-9 text-secondary font-bold">{{ number_format($payment->amount, 2) }}</td>
                            <td class="py-2 pr-9">
                                @php($status = strtolower($payment->status))
                                <span class="{{ in_array($status, ['success']) ? 'text-green' : (in_array($status, ['failed','cancelled']) ? 'text-red' : (in_array($status, ['pending']) ? 'text-orange' : '')) }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td class="py-2 pr-9 tabular-nums">{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td class="py-4 pr-9 text-gray-500" colspan="7">No payments yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    </div>
</x-app-layout>
