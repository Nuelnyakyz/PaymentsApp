<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            if (Schema::hasTable('smtp_settings')) {
                $settings = Cache::remember('smtp_settings:first', 60, function () {
                    return DB::table('smtp_settings')->select('username', 'from_address', 'from_name', 'password_encrypted')->first();
                });

                if ($settings) {
                    if (!empty($settings->username)) {
                        Config::set('mail.mailers.smtp.username', $settings->username);
                    }
                    if (!empty($settings->password_encrypted)) {
                        try {
                            $decrypted = Crypt::decryptString($settings->password_encrypted);
                            Config::set('mail.mailers.smtp.password', $decrypted);
                        } catch (\Throwable $e) {
                            // ignore bad decrypt
                        }
                    }
                    if (!empty($settings->from_address)) {
                        Config::set('mail.from.address', $settings->from_address);
                    }
                    if (!empty($settings->from_name)) {
                        Config::set('mail.from.name', $settings->from_name);
                    }
                }
            }
        } catch (\Throwable $e) {
            // ignore during early boot / no DB
        }
    }
}
