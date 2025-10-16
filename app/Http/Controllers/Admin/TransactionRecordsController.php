<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;

class TransactionRecordsController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::query()->with(['payment']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->date('to'));
        }
        if ($request->filled('min')) {
            $query->where('amount', '>=', (float) $request->input('min'));
        }
        if ($request->filled('max')) {
            $query->where('amount', '<=', (float) $request->input('max'));
        }
        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('transaction_id', 'like', "%$q%")
                    ->orWhere('merchant_request_id', 'like', "%$q%")
                    ->orWhere('checkout_request_id', 'like', "%$q%")
                    ->orWhere('phone', 'like', "%$q%");
            });
        }

        $transactions = $query->latest('created_at')->paginate(20)->withQueryString();

        return view('admin.transactions.index', [
            'transactions' => $transactions,
        ]);
    }
}
