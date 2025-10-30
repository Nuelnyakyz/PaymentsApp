<?php

namespace App\Services\Payments;

use App\Models\Payment;
use App\Models\Transaction;
use App\Services\GatewayConfigRepository;
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
    private $resultUrl;
    private $timeoutUrl;
    private $initiatorName;
    private $initiatorPassword;
    private $securityCredential;
    private $environment;

    public function __construct(GatewayConfigRepository $configRepository, ?string $environment = null)
    {
        $config = $configRepository->mpesa($environment);

        $this->environment = $config['environment'];
        $this->baseUrl = rtrim($config['api_base_url'], '/');
        $this->consumerKey = $config['consumer_key'];
        $this->consumerSecret = $config['consumer_secret'];
        $this->shortcode = $config['shortcode'];
        $this->passkey = $config['passkey'];
        $this->callbackUrl = $config['callback_url'];
        $this->resultUrl = $config['result_url'] ?? null;
        $this->timeoutUrl = $config['timeout_url'] ?? null;
        $this->initiatorName = $config['initiator_name'] ?? null;
        $this->initiatorPassword = $config['initiator_password'] ?? null;
        $this->securityCredential = $config['security_credential'] ?? null;
        $this->assertConfig();
    }

    private function assertConfig(): void
    {
        $missing = [];
        if (empty($this->baseUrl)) $missing[] = 'api_base_url';
        if (empty($this->consumerKey)) $missing[] = 'consumer_key';
        if (empty($this->consumerSecret)) $missing[] = 'consumer_secret';
        if (empty($this->shortcode)) $missing[] = 'shortcode';
        if (empty($this->passkey)) $missing[] = 'passkey';
        if (empty($this->callbackUrl)) $missing[] = 'callback_url';
        if (!empty($missing)) {
            Log::error('Mpesa configuration missing required keys', [ 'missing' => $missing ]);
            throw new \RuntimeException('Missing required M-Pesa configuration fields: ' . implode(', ', $missing));
        }
    }
    private function generateAccessToken()
    {
        $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
            ->acceptJson()
            ->get($this->baseUrl . '/oauth/v1/generate?grant_type=client_credentials');

        if (! $response->successful()) {
            Log::error('Mpesa OAuth token request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('Failed to generate M-Pesa access token: HTTP ' . $response->status());
        }

        $data = $response->json();
        if (!is_array($data) || empty($data['access_token'])) {
            Log::error('Mpesa OAuth token response missing access_token', [
                'response' => $data,
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('M-Pesa token response missing access_token');
        }

        return $data['access_token'];
    }

    public function initiate(array $data)
    {
        $token = $this->generateAccessToken();

        $timestamp = now()->format('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

        $phone = $data['payer_phone'] ?? $data['phone'] ?? null;
        $payload = [
            'BusinessShortCode' => $this->shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $data['amount'],
            'PartyA' => $phone,
            'PartyB' => $this->shortcode,
            'PhoneNumber' => $phone,
            'CallBackURL' => $this->callbackUrl,
            'AccountReference' => $data['reference'],
            'TransactionDesc' => 'Course Payment',
        ];

        $response = Http::withToken($token)
            ->acceptJson()
            ->post($this->baseUrl . '/mpesa/stkpush/v1/processrequest', $payload);

        if (! $response->successful()) {
            Log::error('Mpesa STK push request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'payload' => $payload,
            ]);
            return [
                'error' => true,
                'status' => $response->status(),
                'message' => 'STK push request failed',
                'body' => json_decode($response->body(), true) ?? $response->body(),
            ];
        }

        return $response->json();
    }

    public function handleCallback($callbackData)
    {
        // Parse callback and update payment status in DB
        Log::info('M-Pesa Callback received', $callbackData);

        // TEMP: print full callback payload to terminal/error log without affecting flow
        // This will appear in the terminal when using `php artisan serve` or in the web server error log
        // Remove once you've verified the incoming data
        error_log('MPESA CALLBACK PAYLOAD: ' . json_encode($callbackData));

        $resultCode = $callbackData['Body']['stkCallback']['ResultCode'];
        $resultDesc = $callbackData['Body']['stkCallback']['ResultDesc'];
        $checkoutId = $callbackData['Body']['stkCallback']['CheckoutRequestID'];

        $metadata = $callbackData['Body']['stkCallback']['CallbackMetadata']['Item'] ?? [];
        $amount = collect($metadata)->firstWhere('Name', 'Amount')['Value'] ?? 0;
        $mpesaReceipt = collect($metadata)->firstWhere('Name', 'MpesaReceiptNumber')['Value'] ?? null;
        $phone = collect($metadata)->firstWhere('Name', 'PhoneNumber')['Value'] ?? null;
        // Attempt to assemble payer name if provided (rare for STK callback)
        $firstName = collect($metadata)->firstWhere('Name', 'FirstName')['Value'] ?? null;
        $middleName = collect($metadata)->firstWhere('Name', 'MiddleName')['Value'] ?? null;
        $lastName = collect($metadata)->firstWhere('Name', 'LastName')['Value'] ?? null;
        $composedName = trim(implode(' ', array_filter([$firstName, $middleName, $lastName])));

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

            // Update payment (ensure payer phone/name captured from callback where available)
            $payment->update([
                'status' => 'success',
                'paid_at' => now(),
                'payer_phone' => $phone ?? $payment->payer_phone,
                'payer_name' => $composedName ?: ($payment->payer_name ?? $payment->student_full_name),
            ]);

            // Refresh payment so downstream services see updated payer details
            $payment->refresh();

            // Generate receipt
            $receiptService = new ReceiptService();
            $receiptService->generate($payment, $mpesaReceipt, $amount);

            Log::info("Payment successful and receipt generated for {$mpesaReceipt}");
        } else {
            $transaction->update([
                'status' => 'failed',
                'raw_response' => $callbackData,
            ]);

            // Capture payer phone/name even on failure if present for audit
            $payment->update([
                'status' => 'failed',
                'payer_phone' => $phone ?? $payment->payer_phone,
                'payer_name' => $composedName ?: $payment->payer_name,
            ]);

            Log::info("Payment failed: {$resultDesc}");
        }
    }
}
