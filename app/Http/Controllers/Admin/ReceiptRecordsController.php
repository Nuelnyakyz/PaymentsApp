<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Receipt;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptRecordsController extends Controller
{
    public function index(Request $request)
    {
        $query = Receipt::query()->with(['payment']);

        if ($request->filled('from')) {
            $query->whereDate('issued_at', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('issued_at', '<=', $request->date('to'));
        }
        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('receipt_number', 'like', "%$q%")
                    ->orWhere('student_full_name', 'like', "%$q%")
                    ->orWhere('payer_name', 'like', "%$q%")
                    ->orWhere('payer_phone', 'like', "%$q%")
                    ->orWhere('course_name', 'like', "%$q%");
            });
        }

        $receipts = $query->latest('issued_at')->paginate(20)->withQueryString();

        return view('admin.receipts.index', [
            'receipts' => $receipts,
        ]);
    }

    public function show(Receipt $receipt)
    {
        $receipt->load(['payment.clientApp']);
        $clientName = optional(optional($receipt->payment)->clientApp)->name;
        return view('admin.receipts.show', [
            'receipt' => $receipt,
            'clientName' => $clientName,
        ]);
    }

    public function pdf(Receipt $receipt)
    {
        $receipt->load(['payment.clientApp']);
        $clientName = optional(optional($receipt->payment)->clientApp)->name;
        $pdf = Pdf::loadView('admin.receipts.pdf', [
            'receipt' => $receipt,
            'clientName' => $clientName,
        ])->setPaper('a4')->setOptions([
            'defaultFont' => 'sans-serif',
            'isRemoteEnabled' => true,
        ]);
        return $pdf->download('receipt-'.$receipt->receipt_number.'.pdf');
    }
}
