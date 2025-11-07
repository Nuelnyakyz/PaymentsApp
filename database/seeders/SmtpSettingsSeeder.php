<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class SmtpSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $username = env('MAIL_USERNAME');
        $fromAddress = env('MAIL_FROM_ADDRESS');
        $fromName = env('MAIL_FROM_NAME');
        $password = env('MAIL_PASSWORD');

        // Only seed if at least one value exists
        if (!$username && !$fromAddress && !$password && !$fromName) {
            return;
        }

        $existing = DB::table('smtp_settings')->first();
        $payload = [
            'username' => $username ?: ($existing->username ?? null),
            'from_address' => $fromAddress ?: ($existing->from_address ?? null),
            'from_name' => $fromName ?: ($existing->from_name ?? null),
            'password_encrypted' => $existing->password_encrypted ?? null,
            'updated_at' => now(),
        ];
        if ($password) {
            $payload['password_encrypted'] = Crypt::encryptString($password);
        }

        if ($existing) {
            DB::table('smtp_settings')->where('id', $existing->id)->update($payload);
        } else {
            $payload['created_at'] = now();
            DB::table('smtp_settings')->insert($payload);
        }
    }
}
