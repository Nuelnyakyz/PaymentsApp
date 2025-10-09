<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Receipt extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'payment_id', 'receipt_number',
        'student_full_name', 'payer_name', 'payer_phone',
        'amount', 'course_name', 'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }


}
