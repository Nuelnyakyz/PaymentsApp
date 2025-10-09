<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_app_id')->nullable()->constrained(); // the app that initiated payment
            $table->string('reference')->unique(); // internal reference or invoice number
        
            // student info
            $table->string('student_full_name')->nullable();
            $table->string('student_email')->nullable();
            $table->string('student_phone')->nullable();
        
            // payer info (may be same as student)
            $table->string('payer_name')->nullable();
            $table->string('payer_phone')->nullable();
        
            $table->string('course_name')->nullable();
            $table->decimal('amount', 10, 2);
        
            $table->enum('status', ['pending', 'success', 'failed', 'cancelled'])->default('pending');
            $table->string('payment_method')->nullable(); // e.g. mpesa, airtel, card
            $table->timestamp('paid_at')->nullable();
        
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
