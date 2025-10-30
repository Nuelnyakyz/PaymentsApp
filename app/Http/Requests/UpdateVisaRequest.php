<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
class UpdateVisaRequest extends FormRequest
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
            'api_base_url' => ['nullable', 'url'],
            'webhook_endpoint' => ['nullable', 'url'],
            'merchant_id' => ['nullable', 'string'],
            'api_key_id' => ['nullable', 'string'],
            'org_id' => ['nullable', 'string'],
            // secrets
            'shared_secret' => ['nullable', 'string'],
            'webhook_secret' => ['nullable', 'string'],
        ];
    }
}
