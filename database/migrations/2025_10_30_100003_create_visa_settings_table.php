<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visa_settings', function (Blueprint $table) {
            $table->id();
            $table->string('environment'); // sandbox | live
            $table->boolean('is_active')->default(false);

            // Common non-secrets
            $table->string('api_base_url')->nullable();
            $table->string('webhook_endpoint')->nullable();

            // CyberSource (choose what you use, others can be null)
            $table->string('merchant_id')->nullable();
            $table->string('api_key_id')->nullable();
            $table->string('org_id')->nullable();

            // Secrets (encrypted via casts on model)
            $table->text('shared_secret')->nullable();
            $table->text('webhook_secret')->nullable();

            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->unique(['environment']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visa_settings');
    }
};
