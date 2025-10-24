<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('payments', 'student_phone')) {
            // Backfill payer_phone for older rows that only had student_phone
            DB::table('payments')
                ->whereNull('payer_phone')
                ->update(['payer_phone' => DB::raw('student_phone')]);

            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn('student_phone');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('payments', 'student_phone')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('student_phone')->nullable();
            });
        }
    }
};
