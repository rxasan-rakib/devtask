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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('system'); // system, smtp, notification
            $table->string('type')->default('text'); // text, textarea, select, boolean
            $table->timestamps();
        });

        // Seed default initial settings
        $defaults = [
            // System Settings
            ['key' => 'app_name',              'value' => 'WorkFlow Pro',                'group' => 'system',       'type' => 'text'],
            ['key' => 'company_name',          'value' => 'WorkFlow Pro Ltd.',           'group' => 'system',       'type' => 'text'],
            ['key' => 'company_email',         'value' => 'support@workflowpro.com',     'group' => 'system',       'type' => 'text'],
            ['key' => 'company_phone',         'value' => '+880 1700-000000',           'group' => 'system',       'type' => 'text'],
            ['key' => 'company_address',       'value' => 'Dhaka, Bangladesh',           'group' => 'system',       'type' => 'textarea'],
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

        $now = now();
        foreach ($defaults as &$item) {
            $item['created_at'] = $now;
            $item['updated_at'] = $now;
        }

        DB::table('settings')->insert($defaults);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
