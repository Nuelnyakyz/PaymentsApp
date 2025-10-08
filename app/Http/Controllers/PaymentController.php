<?php

namespace App\Http\Controllers;

use App\Services\Payments\MpesaService;
use App\Services\Payments\AirtelService;
use App\Services\Payments\CardService;
use App\Services\Payments\EcitizenService;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function initiate(Request $request)
    {
        $method = $request->get('method'); // e.g. mpesa, airtel, card
        $service = $this->getPaymentService($method);

        $result = $service->initiate($request->all());

        return response()->json($result);
    }

    private function getPaymentService($method)
    {
        return match ($method) {
            'mpesa' => new MpesaService(),
            // 'airtel' => new AirtelService(),
            // 'card' => new CardService(),
            // 'ecitizen' => new EcitizenService(),
            default => throw new \Exception('Unsupported payment method')
        };
    }
}
