<?php

namespace App\Mail;

use App\Models\Payment;
use App\Models\Receipt;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Payment $payment, public Receipt $receipt)
    {
    }

    public function build()
    {
        $subject = 'Payment Receipt - '.($this->payment->course_name ?? 'Your Course').' - '.($this->receipt->receipt_number ?? $this->payment->reference);
        $subject = strtoupper($subject);

        // Load client name similar to Admin Receipt PDF
        $this->payment->loadMissing('clientApp');
        $clientName = optional($this->payment->clientApp)->name;

        // Render PDF from existing Blade view
        $pdf = Pdf::loadView('admin.receipts.pdf', [
            'receipt' => $this->receipt,
            'clientName' => $clientName,
        ])->setPaper('a4')->setOptions([
            'defaultFont' => 'Quicksand',
            'isRemoteEnabled' => true,
        ]);

        $filename = 'receipt-'.($this->receipt->receipt_number ?? $this->payment->reference).'.pdf';

        return $this->subject($subject)
            ->view('emails.payment_receipt', [
                'receipt' => $this->receipt,
                'payment' => $this->payment,
                'clientName' => $clientName,
            ])
            ->attachData($pdf->output(), $filename, [
                'mime' => 'application/pdf',
            ]);
    }
}
