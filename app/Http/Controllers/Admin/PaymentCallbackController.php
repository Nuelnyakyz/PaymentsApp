<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    public function resend(Request $request, Payment $payment)
    {
        if (strtolower((string)$payment->status) !== 'success') {
            return back()->with('status', 'Only successful payments can be resent.');
        }

        $client = $payment->clientApp;
        $callbackUrl = $client?->callback_url;

        $payload = [
            'reference' => (string)$payment->reference,
            'status' => (string)$payment->status,
            'amount' => (float)$payment->amount,
            'client_app_id' => $client?->id ? (string)$client->id : null,
            'course_id' => null,
            'user_id' => null,
            'ts' => time(),
        ];

        // Sign payload if api_secret is available
        if (!empty($client?->api_secret)) {
            $verify = $payload;
            ksort($verify);
            $base = http_build_query($verify, '', '&', PHP_QUERY_RFC3986);
            $signature = hash_hmac('sha256', $base, $client->api_secret);
            $payload['signature'] = $signature;
        }

        if (!empty($callbackUrl)) {
            try {
                $resp = Http::asForm()->post($callbackUrl, $payload);
                Log::info('Admin resent payment callback', [
                    'payment_id' => $payment->id,
                    'url' => $callbackUrl,
                    'status' => $resp->status(),
                ]);
                return back()->with('status', 'Callback sent. HTTP '.$resp->status());
            } catch (\Throwable $e) {
                Log::error('Admin resend callback failed', [
                    'payment_id' => $payment->id,
                    'url' => $callbackUrl,
                    'error' => $e->getMessage(),
                ]);
                return back()->with('status', 'Failed to send callback: '.$e->getMessage());
            }
        }

        return back()->with('status', 'No callback URL configured for this client app.');
    }
}
