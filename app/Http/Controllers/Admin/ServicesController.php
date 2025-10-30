<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateMpesaRequest;
use App\Http\Requests\UpdateAirtelRequest;
use App\Http\Requests\UpdateVisaRequest;
use App\Models\MpesaSetting;
use App\Models\AirtelSetting;
use App\Models\VisaSetting;
use App\Services\GatewayConfigRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServicesController extends Controller
{
    public function index(Request $request)
    {
        $env = $request->get('env', 'sandbox');
        $mpesa = MpesaSetting::where('environment', $env)->first();
        $airtel = AirtelSetting::where('environment', $env)->first();
        $visa = VisaSetting::where('environment', $env)->first();

        return view('admin.services.index', [
            'environment' => $env,
            'mpesa' => $mpesa,
            'airtel' => $airtel,
            'visa' => $visa,
        ]);
    }

    public function updateMpesa(UpdateMpesaRequest $request, GatewayConfigRepository $repo): RedirectResponse
    {
        $data = $request->validated();
        $setting = MpesaSetting::firstOrNew(['environment' => $data['environment']]);
        $payload = array_filter($data, fn($k) => $k !== 'environment', ARRAY_FILTER_USE_KEY);
        // skip overwriting secrets if left blank
        foreach (['consumer_key','consumer_secret','passkey','initiator_name','initiator_password','security_credential'] as $secret) {
            if (array_key_exists($secret, $payload) && ($payload[$secret] === null || $payload[$secret] === '')) {
                unset($payload[$secret]);
            }
        }
        $setting->fill($payload);
        $setting->updated_by = Auth::id();
        $setting->save();
        $repo->clear('mpesa', $setting->environment);
        return redirect()->route('admin.services.index', ['env' => $setting->environment])->with('status', 'M-Pesa settings updated');
    }

    public function updateAirtel(UpdateAirtelRequest $request, GatewayConfigRepository $repo): RedirectResponse
    {
        $data = $request->validated();
        $setting = AirtelSetting::firstOrNew(['environment' => $data['environment']]);
        $payload = array_filter($data, fn($k) => $k !== 'environment', ARRAY_FILTER_USE_KEY);
        foreach (['api_key','api_secret','public_key','username','password'] as $secret) {
            if (array_key_exists($secret, $payload) && ($payload[$secret] === null || $payload[$secret] === '')) {
                unset($payload[$secret]);
            }
        }
        $setting->fill($payload);
        $setting->updated_by = Auth::id();
        $setting->save();
        $repo->clear('airtel', $setting->environment);
        return redirect()->route('admin.services.index', ['env' => $setting->environment])->with('status', 'Airtel settings updated');
    }

    public function updateVisa(UpdateVisaRequest $request, GatewayConfigRepository $repo): RedirectResponse
    {
        $data = $request->validated();
        $setting = VisaSetting::firstOrNew(['environment' => $data['environment']]);
        $payload = array_filter($data, fn($k) => $k !== 'environment', ARRAY_FILTER_USE_KEY);
        foreach (['shared_secret','webhook_secret'] as $secret) {
            if (array_key_exists($secret, $payload) && ($payload[$secret] === null || $payload[$secret] === '')) {
                unset($payload[$secret]);
            }
        }
        $setting->fill($payload);
        $setting->updated_by = Auth::id();
        $setting->save();
        $repo->clear('visa', $setting->environment);
        return redirect()->route('admin.services.index', ['env' => $setting->environment])->with('status', 'Visa (CyberSource) settings updated');
    }
}
