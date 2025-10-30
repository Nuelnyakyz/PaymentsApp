<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MpesaSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'environment',
        'is_active',
        'shortcode',
        'api_base_url',
        'callback_url',
        'timeout_url',
        'result_url',
        'consumer_key',
        'consumer_secret',
        'passkey',
        'initiator_name',
        'initiator_password',
        'security_credential',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'consumer_key' => 'encrypted',
        'consumer_secret' => 'encrypted',
        'passkey' => 'encrypted',
        'initiator_password' => 'encrypted',
        'security_credential' => 'encrypted',
    ];
}
