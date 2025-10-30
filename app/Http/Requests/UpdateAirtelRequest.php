<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateAirtelRequest extends FormRequest
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
            'x_reference_id' => ['nullable', 'string'],
            'country' => ['nullable', 'string'],
            'currency' => ['nullable', 'string'],
            'api_base_url' => ['nullable', 'url'],
            'callback_url' => ['nullable', 'url'],
            // secrets
            'api_key' => ['nullable', 'string'],
            'api_secret' => ['nullable', 'string'],
            'public_key' => ['nullable', 'string'],
            'username' => ['nullable', 'string'],
            'password' => ['nullable', 'string'],
        ];
    }
}
