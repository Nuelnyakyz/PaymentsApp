<?php

namespace App\Services\Payments;

use App\Models\Payment;
use App\Models\Transaction;
use App\Services\Reports\ReceiptService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;



class MpesaService
{
    private $baseUrl;
    private $consumerKey;
    private $consumerSecret;
    private $shortcode;
    private $passkey;
    private $callbackUrl;

    public function __construct()
    {
        $this->baseUrl = config('mpesa.base_url');
        $this->consumerKey = config('mpesa.consumer_key');
        $this->consumerSecret = config('mpesa.consumer_secret');
        $this->shortcode = config('mpesa.shortcode');
        $this->passkey = config('mpesa.passkey');
        $this->callbackUrl = config('mpesa.callback_url');
    }
    private function generateAccessToken()
    {
        $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
            ->get($this->baseUrl . '/oauth/v1/generate?grant_type=client_credentials');
        
        return $response->json()['access_token'];
    }

    public function initiate(array $data)
    {
        $token = $this->generateAccessToken();

        $timestamp = now()->format('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

        $payload = [
            'BusinessShortCode' => $this->shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $data['amount'],
            'PartyA' => $data['phone'],
            'PartyB' => $this->shortcode,
            'PhoneNumber' => $data['phone'],
            'CallBackURL' => $this->callbackUrl,
            'AccountReference' => $data['reference'],
            'TransactionDesc' => 'Course Payment',
        ];

        $response = Http::withToken($token)
            ->post($this->baseUrl . '/stkpush/v1/processrequest', $payload);

        return $response->json();
    }

    public function handleCallback($callbackData)
    {
        // Parse callback and update payment status in DB
        Log::info('M-Pesa Callback received', $callbackData);

        $resultCode = $callbackData['Body']['stkCallback']['ResultCode'];
        $resultDesc = $callbackData['Body']['stkCallback']['ResultDesc'];
        $checkoutId = $callbackData['Body']['stkCallback']['CheckoutRequestID'];

        $metadata = $callbackData['Body']['stkCallback']['CallbackMetadata']['Item'] ?? [];
        $amount = collect($metadata)->firstWhere('Name', 'Amount')['Value'] ?? 0;
        $mpesaReceipt = collect($metadata)->firstWhere('Name', 'MpesaReceiptNumber')['Value'] ?? null;
        $phone = collect($metadata)->firstWhere('Name', 'PhoneNumber')['Value'] ?? null;

        // Find the transaction
        $transaction = Transaction::where('checkout_request_id', $checkoutId)->first();

        if (!$transaction) {
            Log::warning("Transaction not found for checkoutId: {$checkoutId}");
            return;
        }

        $payment = $transaction->payment;

        if ($resultCode == 0) {
            // Success
            $transaction->update([
                'status' => 'success',
                'transaction_id' => $mpesaReceipt,
                'raw_response' => $callbackData,
            ]);

            // Update payment   
            $payment->update([
                'status' => 'success',
                'paid_at' => now(),
            ]);

            // Generate receipt
            $receiptService = new ReceiptService();
            $receiptService->generate($payment, $mpesaReceipt, $amount);

            Log::info("Payment successful and receipt generated for {$mpesaReceipt}");
        } else {
            $transaction->update([
                'status' => 'failed',
                'raw_response' => $callbackData,
            ]);

            $payment->update(['status' => 'failed']);

            Log::info("Payment failed: {$resultDesc}");
        }
    }
}
