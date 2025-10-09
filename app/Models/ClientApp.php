<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientApp extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'name', 'api_key', 'api_secret', 'callback_url',
    ];

    protected $casts = [
        'callback_url' => 'url',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
