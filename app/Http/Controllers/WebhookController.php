<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WebhookLog;
use App\Services\Payments\MpesaService;

class WebhookController extends Controller
{
    public function mpesa(Request $request)
    {
        // 1️⃣ Log raw callback payload
        WebhookLog::create([
            'provider' => 'mpesa',
            'payload' => $request->all(),
        ]);

        // 2️⃣ Forward to the MpesaService for processing
        $mpesaService = new MpesaService();
        $mpesaService->handleCallback($request->all());

        // 3️⃣ Respond quickly to prevent timeout
        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Callback processed successfully',
        ]);
    }

    // public function airtel(Request $request)
    // public function card(Request $request)
    // public function ecitizen(Request $request)
}
