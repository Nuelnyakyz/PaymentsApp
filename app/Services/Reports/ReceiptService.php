<?php

namespace App\Services\Reports;

use App\Models\Receipt;

class ReceiptService
{
    public function generate($payment, $mpesaReceipt, $amount)
    {
        return Receipt::create([
            'payment_id' => $payment->id,
            'receipt_number' => $mpesaReceipt,
            'amount' => $amount,
            'payer_name' => $payment->payer_name,
            'payer_phone' => $payment->payer_phone,
            'student_full_name' => $payment->student_full_name,
            'course_name' => $payment->course_name,
            'issued_at' => now(),
        ]);
    }
}
