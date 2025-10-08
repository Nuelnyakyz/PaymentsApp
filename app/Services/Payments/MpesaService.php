<?php

namespace App\Services\Payments;

use Illuminate\Support\Facades\Http;

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
    }
}
