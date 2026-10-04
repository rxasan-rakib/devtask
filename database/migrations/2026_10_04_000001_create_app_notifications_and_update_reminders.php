<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create app_notifications table
        if (!Schema::hasTable('app_notifications')) {
            Schema::create('app_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('type')->default('system'); // reminder, task_deadline, task_assigned, task_status, system
                $table->string('title');
                $table->text('message')->nullable();
                $table->string('action_url')->nullable();
                $table->string('icon')->default('bell');
                $table->string('color')->default('#6366f1');
                $table->json('data')->nullable();
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['user_id', 'read_at']);
                $table->index(['user_id', 'type']);
            });
        }

        // 2. Add extra status tracking columns to reminders table
        if (Schema::hasTable('reminders')) {
            Schema::table('reminders', function (Blueprint $table) {
                if (!Schema::hasColumn('reminders', 'is_completed')) {
                    $table->boolean('is_completed')->default(false)->after('is_sent');
                }
                if (!Schema::hasColumn('reminders', 'completed_at')) {
                    $table->timestamp('completed_at')->nullable()->after('sent_at');
                }
                if (!Schema::hasColumn('reminders', 'email_sent')) {
                    $table->boolean('email_sent')->default(false)->after('is_completed');
                }
                if (!Schema::hasColumn('reminders', 'email_sent_at')) {
                    $table->timestamp('email_sent_at')->nullable()->after('completed_at');
                }
                if (!Schema::hasColumn('reminders', 'notified_at')) {
                    $table->timestamp('notified_at')->nullable()->after('email_sent_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_notifications');

        if (Schema::hasTable('reminders')) {
            Schema::table('reminders', function (Blueprint $table) {
                $columns = ['is_completed', 'completed_at', 'email_sent', 'email_sent_at', 'notified_at'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('reminders', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
