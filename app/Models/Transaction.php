<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    //
    use HasFactory;
    
    protected $fillable = [
        'payment_id',
        'transaction_id',
        'merchant_request_id',
        'checkout_request_id',
        'amount',
        'phone',
        'status',
        'raw_response',
    ];

    protected $casts = [
        'raw_response' => 'array', // JSON detected automatically
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
