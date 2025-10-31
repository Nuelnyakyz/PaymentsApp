<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('primary_admin')->default(false)->after('is_admin');
        });

        $primaryAdminId = DB::table('users')
            ->where('is_admin', true)
            ->orderBy('id')
            ->value('id');

        if ($primaryAdminId) {
            DB::table('users')
                ->where('id', $primaryAdminId)
                ->update(['primary_admin' => true]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('primary_admin');
        });
    }
};
