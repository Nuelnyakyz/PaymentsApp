<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateMpesaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->is_admin;
    }

    public function rules(): array
    {
        return [
            'environment' => ['required', 'in:sandbox,live'],
            'is_active' => ['nullable', 'boolean'],
            'shortcode' => ['nullable', 'string'],
            'api_base_url' => ['nullable', 'url'],
            'callback_url' => ['nullable', 'url'],
            'timeout_url' => ['nullable', 'url'],
            'result_url' => ['nullable', 'url'],
            // secrets (allow empty to keep existing)
            'consumer_key' => ['nullable', 'string'],
            'consumer_secret' => ['nullable', 'string'],
            'passkey' => ['nullable', 'string'],
            'initiator_name' => ['nullable', 'string'],
            'initiator_password' => ['nullable', 'string'],
            'security_credential' => ['nullable', 'string'],
        ];
    }
}
