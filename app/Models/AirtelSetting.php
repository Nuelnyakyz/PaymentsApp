<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirtelSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'environment',
        'is_active',
        'x_reference_id',
        'country',
        'currency',
        'api_base_url',
        'callback_url',
        'api_key',
        'api_secret',
        'public_key',
        'username',
        'password',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'api_key' => 'encrypted',
        'api_secret' => 'encrypted',
        'public_key' => 'encrypted',
        'username' => 'encrypted',
        'password' => 'encrypted',
    ];
}
