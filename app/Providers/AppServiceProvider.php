<?php

namespace App\Providers;

use App\Models\SystemSetting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

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
        // Dynamically override Laravel SMTP configuration from DB system_settings
        try {
            if (Schema::hasTable('system_settings')) {
                $mailHost = SystemSetting::get('mail_host');
                $mailPort = SystemSetting::get('mail_port');
                $mailUsername = SystemSetting::get('mail_username');
                $mailPassword = SystemSetting::get('mail_password');
                $mailEncryption = SystemSetting::get('mail_encryption');
                $mailFromAddress = SystemSetting::get('mail_from_address');
                $mailFromName = SystemSetting::get('mail_from_name');

                if ($mailHost) {
                    Config::set('mail.default', 'smtp');
                    Config::set('mail.mailers.smtp.host', $mailHost);
                }
                if ($mailPort) {
                    Config::set('mail.mailers.smtp.port', (int) $mailPort);
                }
                if (!is_null($mailUsername)) {
                    Config::set('mail.mailers.smtp.username', $mailUsername);
                }
                if (!is_null($mailPassword)) {
                    Config::set('mail.mailers.smtp.password', $mailPassword);
                }
                if (!is_null($mailEncryption)) {
                    $scheme = strtolower($mailEncryption) === 'ssl' ? 'smtps' : (strtolower($mailEncryption) === 'tls' ? 'smtp' : null);
                    Config::set('mail.mailers.smtp.scheme', $scheme);
                }
                if ($mailFromAddress) {
                    Config::set('mail.from.address', $mailFromAddress);
                }
                if ($mailFromName) {
                    Config::set('mail.from.name', $mailFromName);
                }
            }
        } catch (\Exception $e) {
            // DB connection or migration might not be initialized yet
        }
    }
}
