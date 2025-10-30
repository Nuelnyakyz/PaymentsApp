<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mpesa_settings', function (Blueprint $table) {
            $table->id();
            $table->string('environment'); // sandbox | live
            $table->boolean('is_active')->default(false);

            // Non-secrets
            $table->string('shortcode')->nullable();
            $table->string('api_base_url')->nullable();
            $table->string('callback_url')->nullable();
            $table->string('timeout_url')->nullable();
            $table->string('result_url')->nullable();

            // Secrets (encrypted via casts on model)
            $table->text('consumer_key')->nullable();
            $table->text('consumer_secret')->nullable();
            $table->text('passkey')->nullable();
            $table->string('initiator_name')->nullable();
            $table->text('initiator_password')->nullable();
            $table->text('security_credential')->nullable();

            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->unique(['environment']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mpesa_settings');
    }
};
