<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->toDateString();

        $paymentsToday = Payment::whereDate('created_at', $today)->count();
        $successfulToday = Payment::where('status', 'success')
            ->whereDate('updated_at', $today)
            ->count();
        $amountToday = Payment::where('status', 'success')
            ->whereDate('updated_at', $today)
            ->sum('amount');

        $recentPayments = Payment::where('status', 'success')
            ->latest('created_at')
            ->take(10)
            ->get();

        // Helper to build daily sums between dates
        $dateExpr = DB::raw("DATE(COALESCE(paid_at, updated_at, created_at)) as d");

        $now = Carbon::now();

        // This week (Mon..today)
        $startOfWeek = (clone $now)->startOfWeek();
        $weekRange = collect();
        for ($d = $startOfWeek->copy(); $d->lte($now); $d->addDay()) {
            $weekRange->push($d->format('Y-m-d'));
        }
        $weekQuery = Payment::where('status', 'success')
            ->whereBetween(DB::raw('COALESCE(paid_at, updated_at, created_at)'), [$startOfWeek->copy()->startOfDay(), $now->copy()->endOfDay()])
            ->select($dateExpr, DB::raw('SUM(amount) as total'))
            ->groupBy('d')
            ->pluck('total', 'd');
        $weekData = $weekRange->map(fn($d) => (float)($weekQuery[$d] ?? 0))->all();
        $weekTotal = array_sum($weekData);

        // This month (last 30 days from today)
        $start30 = $now->copy()->subDays(29)->startOfDay();
        $monthRange = collect();
        for ($d = $start30->copy(); $d->lte($now); $d->addDay()) {
            $monthRange->push($d->format('Y-m-d'));
        }
        $monthQuery = Payment::where('status', 'success')
            ->whereBetween(DB::raw('COALESCE(paid_at, updated_at, created_at)'), [$start30, $now->copy()->endOfDay()])
            ->select($dateExpr, DB::raw('SUM(amount) as total'))
            ->groupBy('d')
            ->pluck('total', 'd');
        $monthData = $monthRange->map(fn($d) => (float)($monthQuery[$d] ?? 0))->all();
        $monthTotal = array_sum($monthData);

        // This year (monthly sums Jan..current)
        $year = (int)$now->format('Y');
        $months = collect(range(1, (int)$now->format('n')));
        $ymExpr = DB::raw("DATE_FORMAT(COALESCE(paid_at, updated_at, created_at), '%Y-%m') as ym");
        $yearQuery = Payment::where('status', 'success')
            ->whereYear(DB::raw('COALESCE(paid_at, updated_at, created_at)'), $year)
            ->select($ymExpr, DB::raw('SUM(amount) as total'))
            ->groupBy('ym')
            ->pluck('total', 'ym');
        $yearLabels = $months->map(fn($m) => sprintf('%04d-%02d', $year, $m));
        $yearData = $yearLabels->map(fn($ym) => (float)($yearQuery[$ym] ?? 0))->all();
        $yearTotal = array_sum($yearData);

        // All time (monthly sums across entire dataset, capped to last 24 months for performance)
        $allQuery = Payment::where('status', 'success')
            ->select($ymExpr, DB::raw('SUM(amount) as total'))
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('total', 'ym');
        $allLabels = $allQuery->keys()->values();
        $allData = $allQuery->values();
        // Cap to last 24 points if huge
        if ($allLabels->count() > 24) {
            $allLabels = $allLabels->slice(-24)->values();
            $allData = $allData->slice(-24)->values();
        }
        $allTotal = $allData->sum();

        return view('admin.dashboard', [
            'paymentsToday' => $paymentsToday,
            'successfulToday' => $successfulToday,
            'amountToday' => $amountToday,
            'recentPayments' => $recentPayments,
            'charts' => [
                'week' => [
                    'labels' => $weekRange->map(fn($d) => Carbon::parse($d)->format('D'))->all(),
                    'data' => $weekData,
                    'total' => $weekTotal,
                ],
                'month' => [
                    'labels' => $monthRange->map(fn($d) => Carbon::parse($d)->format('d M'))->all(),
                    'data' => $monthData,
                    'total' => $monthTotal,
                ],
                'year' => [
                    'labels' => $yearLabels->map(fn($ym) => Carbon::createFromFormat('Y-m', $ym)->format('M'))->all(),
                    'data' => $yearData,
                    'total' => $yearTotal,
                ],
                'all' => [
                    'labels' => $allLabels->map(function ($ym) { return Carbon::createFromFormat('Y-m', $ym)->format('M y'); })->all(),
                    'data' => $allData->map(fn($v) => (float)$v)->all(),
                    'total' => (float)$allTotal,
                ],
            ],
        ]);
    }
}
