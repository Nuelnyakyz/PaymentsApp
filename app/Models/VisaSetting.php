<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisaSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'environment',
        'is_active',
        'api_base_url',
        'webhook_endpoint',
        'merchant_id',
        'api_key_id',
        'org_id',
        'shared_secret',
        'webhook_secret',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'shared_secret' => 'encrypted',
        'webhook_secret' => 'encrypted',
    ];
}
