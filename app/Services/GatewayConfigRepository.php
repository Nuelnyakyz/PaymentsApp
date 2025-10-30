<?php

namespace App\Services;

use App\Models\MpesaSetting;
use App\Models\AirtelSetting;
use App\Models\VisaSetting;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

class GatewayConfigRepository
{
    public function mpesa(?string $environment = null): array
    {
        $cacheKey = $environment
            ? "gateway:mpesa:".$environment
            : 'gateway:mpesa:active';

        return Cache::remember($cacheKey, 300, function () use ($environment) {
            $query = MpesaSetting::query();

            if ($environment) {
                $query->where('environment', $environment);
            } else {
                $query->where('is_active', true);
            }

            $setting = $query->first();

            if (!$setting) {
                $message = $environment
                    ? "No M-Pesa configuration found for environment [$environment]."
                    : 'No active M-Pesa configuration found. Set one in the admin Services page.';
                throw new RuntimeException($message);
            }

            return $this->mapMpesaSetting($setting);
        });
    }

    public function airtel(string $environment): ?array
    {
        $cacheKey = "gateway:airtel:".$environment;
        return Cache::remember($cacheKey, 300, function () use ($environment) {
            $setting = AirtelSetting::query()
                ->where('environment', $environment)
                ->where('is_active', true)
                ->first();

            if (!$setting) {
                return null;
            }

            return [
                'environment' => $setting->environment,
                'x_reference_id' => $setting->x_reference_id,
                'country' => $setting->country,
                'currency' => $setting->currency,
                'api_base_url' => $setting->api_base_url,
                'callback_url' => $setting->callback_url,
                'api_key' => $setting->api_key,
                'api_secret' => $setting->api_secret,
                'public_key' => $setting->public_key,
                'username' => $setting->username,
                'password' => $setting->password,
            ];
        });
    }

    public function visaCyberSource(string $environment): ?array
    {
        $cacheKey = "gateway:visa:".$environment;
        return Cache::remember($cacheKey, 300, function () use ($environment) {
            $setting = VisaSetting::query()
                ->where('environment', $environment)
                ->where('is_active', true)
                ->first();

            if (!$setting) {
                return null;
            }

            return [
                'environment' => $setting->environment,
                'api_base_url' => $setting->api_base_url,
                'webhook_endpoint' => $setting->webhook_endpoint,
                'merchant_id' => $setting->merchant_id,
                'api_key_id' => $setting->api_key_id,
                'org_id' => $setting->org_id,
                'shared_secret' => $setting->shared_secret,
                'webhook_secret' => $setting->webhook_secret,
            ];
        });
    }

    public function clear(string $provider, ?string $environment = null): void
    {
        if ($environment !== null) {
            Cache::forget("gateway:$provider:$environment");
        }

        Cache::forget("gateway:$provider:active");
    }

    private function mapMpesaSetting(MpesaSetting $setting): array
    {
        return [
            'environment' => $setting->environment,
            'shortcode' => $setting->shortcode,
            'api_base_url' => $setting->api_base_url,
            'callback_url' => $setting->callback_url,
            'timeout_url' => $setting->timeout_url,
            'result_url' => $setting->result_url,
            'consumer_key' => $setting->consumer_key,
            'consumer_secret' => $setting->consumer_secret,
            'passkey' => $setting->passkey,
            'initiator_name' => $setting->initiator_name,
            'initiator_password' => $setting->initiator_password,
            'security_credential' => $setting->security_credential,
        ];
    }
}
