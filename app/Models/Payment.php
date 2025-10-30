<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'client_app_id',
        'reference',
        'student_full_name', 'student_email',
        'payer_name', 'payer_phone',
        'course_name', 'course_id', 'user_id',
        'amount', 'status', 'payment_method', 'paid_at'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    // relationships
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class);
    }

    public function clientApp()
    {
        return $this->belongsTo(ClientApp::class);
    }
}
