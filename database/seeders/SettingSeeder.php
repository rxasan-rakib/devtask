<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // System Settings
            ['key' => 'app_name',              'value' => 'FAST IT WORK FLOW',           'group' => 'system',       'type' => 'text'],
            ['key' => 'company_name',          'value' => 'FAST IT',                     'group' => 'system',       'type' => 'text'],
            ['key' => 'company_email',         'value' => 'support.fastit@gmail.com',     'group' => 'system',       'type' => 'text'],
            ['key' => 'company_phone',         'value' => '01901166585',                 'group' => 'system',       'type' => 'text'],
            ['key' => 'company_address',       'value' => 'Mirpur 10, Dhaka, Bangladesh', 'group' => 'system',       'type' => 'textarea'],
            ['key' => 'timezone',              'value' => 'Asia/Dhaka',                  'group' => 'system',       'type' => 'text'],
            ['key' => 'currency_symbol',       'value' => '$',                           'group' => 'system',       'type' => 'text'],
            ['key' => 'date_format',           'value' => 'd M, Y',                      'group' => 'system',       'type' => 'text'],
            ['key' => 'default_task_priority', 'value' => 'medium',                      'group' => 'system',       'type' => 'select'],

            // SMTP Settings
            ['key' => 'mail_mailer',           'value' => 'smtp',                        'group' => 'smtp',         'type' => 'select'],
            ['key' => 'mail_host',             'value' => 'smtp.gmail.com',              'group' => 'smtp',         'type' => 'text'],
            ['key' => 'mail_port',             'value' => '587',                         'group' => 'smtp',         'type' => 'text'],
            ['key' => 'mail_username',         'value' => 'support.fastit@gmail.com',     'group' => 'smtp',         'type' => 'text'],
            ['key' => 'mail_password',         'value' => 'jyydgyegyidekhxr',            'group' => 'smtp',         'type' => 'text'],
            ['key' => 'mail_encryption',       'value' => 'tls',                         'group' => 'smtp',         'type' => 'select'],
            ['key' => 'mail_from_address',     'value' => 'support.fastit@gmail.com',     'group' => 'smtp',         'type' => 'text'],
            ['key' => 'mail_from_name',        'value' => 'FAST IT WORK FLOW',           'group' => 'smtp',         'type' => 'text'],

            // Notifications
            ['key' => 'notify_task_assigned',       'value' => '1',                       'group' => 'notification', 'type' => 'boolean'],
            ['key' => 'notify_task_status_changed', 'value' => '1',                       'group' => 'notification', 'type' => 'boolean'],
            ['key' => 'notify_task_overdue',        'value' => '1',                       'group' => 'notification', 'type' => 'boolean'],
            ['key' => 'notify_reminder_due',        'value' => '1',                       'group' => 'notification', 'type' => 'boolean'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'group' => $setting['group'],
                    'type'  => $setting['type'],
                ]
            );
        }

        Setting::clearCache();

        $this->command->info('✅ Settings (including configured SMTP) have been successfully seeded!');
    }
}
