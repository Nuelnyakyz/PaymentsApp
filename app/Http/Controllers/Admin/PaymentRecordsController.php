<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Payment;

class PaymentRecordsController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::query()->with(['payment']);

        if ($request->has('status')) {
            $status = (string) $request->input('status');
            if ($status !== '') {
                $query->where('status', $status);
            }
            // else: empty string means "All Status" - no status where clause
        } else {
            // No status param provided - default view shows successful payments
            $query->where('status', 'success');
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
        if ($request->filled('method')) {
            $method = (string) $request->input('method');
            if ($method !== '') {
                $query->whereHas('payment', function ($q) use ($method) {
                    $q->where('payment_method', $method);
                });
            }
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

        // Distinct payment methods for filter dropdown
        $methods = Payment::query()
            ->whereNotNull('payment_method')
            ->distinct()
            ->orderBy('payment_method')
            ->pluck('payment_method');

        return view('admin.payments.index', [
            'transactions' => $transactions,
            'defaultStatus' => $request->has('status') ? $request->input('status') : 'success',
            'methods' => $methods,
        ]);
    }
}
