<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;

class PaymentRecordsController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::query()->with(['receipt']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('method')) {
            $query->where('payment_method', $request->string('method'));
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->date('to'));
        }
        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('reference', 'like', "%$q%")
                    ->orWhere('student_full_name', 'like', "%$q%")
                    ->orWhere('payer_name', 'like', "%$q%")
                    ->orWhere('student_phone', 'like', "%$q%")
                    ->orWhere('payer_phone', 'like', "%$q%")
                    ->orWhere('course_name', 'like', "%$q%");
            });
        }

        $payments = $query->latest('created_at')->paginate(20)->withQueryString();

        return view('admin.payments.index', [
            'payments' => $payments,
        ]);
    }
}
