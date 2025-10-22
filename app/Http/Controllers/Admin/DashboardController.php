<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Transaction;

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

        $recentPayments = Payment::latest('created_at')->paginate(20);

        return view('admin.dashboard', [
            'paymentsToday' => $paymentsToday,
            'successfulToday' => $successfulToday,
            'amountToday' => $amountToday,
            'recentPayments' => $recentPayments,
        ]);
    }
}
