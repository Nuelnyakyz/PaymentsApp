<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('airtel_settings', function (Blueprint $table) {
            $table->id();
            $table->string('environment'); // sandbox | live
            $table->boolean('is_active')->default(false);

            // Non-secrets
            $table->string('x_reference_id')->nullable();
            $table->string('country')->nullable();
            $table->string('currency')->nullable();
            $table->string('api_base_url')->nullable();
            $table->string('callback_url')->nullable();

            // Secrets (encrypted via casts on model)
            $table->text('api_key')->nullable();
            $table->text('api_secret')->nullable();
            $table->text('public_key')->nullable();
            $table->text('username')->nullable();
            $table->text('password')->nullable();

            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->unique(['environment']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('airtel_settings');
    }
};
