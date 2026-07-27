<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Brevo SMTP
            ['key' => 'mail_host', 'value' => 'smtp-relay.brevo.com', 'group' => 'smtp', 'is_secret' => false],
            ['key' => 'mail_port', 'value' => '587', 'group' => 'smtp', 'is_secret' => false],
            ['key' => 'mail_username', 'value' => '', 'group' => 'smtp', 'is_secret' => false],
            ['key' => 'mail_password', 'value' => '', 'group' => 'smtp', 'is_secret' => true],
            ['key' => 'mail_encryption', 'value' => 'tls', 'group' => 'smtp', 'is_secret' => false],
            ['key' => 'mail_from_address', 'value' => 'careers@munchify.co.ke', 'group' => 'smtp', 'is_secret' => false],
            ['key' => 'mail_from_name', 'value' => 'Munchify Careers', 'group' => 'smtp', 'is_secret' => false],

            // Hostpinnacle SMS
            ['key' => 'sms_hostpinnacle_url', 'value' => 'https://smsportal.hostpinnacle.co.ke/SMSApi/send', 'group' => 'sms', 'is_secret' => false],
            ['key' => 'sms_hostpinnacle_api_key', 'value' => '', 'group' => 'sms', 'is_secret' => true],
            ['key' => 'sms_hostpinnacle_partner_id', 'value' => '', 'group' => 'sms', 'is_secret' => false],
            ['key' => 'sms_hostpinnacle_sender_id', 'value' => 'MUNCHIFY', 'group' => 'sms', 'is_secret' => false],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
