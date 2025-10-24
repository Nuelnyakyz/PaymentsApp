<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\ClientApp;

class PaymentSessionController extends Controller
{
    public function prepare(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'student_full_name' => 'required|string',
            'student_email' => 'nullable|email',
            'course_name' => 'nullable|string',
            'client_app_id' => 'required|integer|exists:client_apps,id',
            'api_key' => 'required|string',
            'course_id' => 'nullable',
            'user_id' => 'nullable',
            'return_url' => 'nullable|url',
            'ts' => 'required|integer',
            'signature' => 'nullable|string',
        ]);

        // Enforce timestamp freshness to prevent replay (default: 5 minutes)
        $now = time();
        $maxSkew = 300; // seconds
        if (abs($now - (int)$data['ts']) > $maxSkew) {
            abort(400, 'Stale request (timestamp too old or too far in future)');
        }

        // Validate client app and api_key
        $client = ClientApp::find($data['client_app_id']);
        if (!$client || $client->api_key !== $data['api_key']) {
            abort(403, 'Invalid client credentials');
        }

        // Optional HMAC signature verification if api_secret exists on server
        if (!empty($client->api_secret) && !empty($data['signature'])) {
            $payloadForSig = $data;
            unset($payloadForSig['signature']);
            ksort($payloadForSig);
            $baseString = http_build_query($payloadForSig, '', '&', PHP_QUERY_RFC3986);
            $expected = hash_hmac('sha256', $baseString, $client->api_secret);
            if (!hash_equals($expected, $data['signature'])) {
                Log::warning('Invalid HMAC for pay.prepare', [
                    'client_app_id' => $data['client_app_id'],
                ]);
                abort(403, 'Invalid signature');
            }
        }

        // Store only the fields we need to prefill in the UI and later submit
        $prefill = [
            'amount' => (string)$data['amount'],
            'student_full_name' => $data['student_full_name'],
            'student_email' => $data['student_email'] ?? '',
            'course_name' => $data['course_name'] ?? '',
            'client_app_id' => (int)$data['client_app_id'],
            // context (not necessarily submitted to initiate)
            'course_id' => $data['course_id'] ?? null,
            'user_id' => $data['user_id'] ?? null,
            'return_url' => $data['return_url'] ?? null,
            'ts' => (int)$data['ts'],
        ];

        $request->session()->put('pay.prefill', $prefill);

        return redirect()->route('pay.index');
    }
}
