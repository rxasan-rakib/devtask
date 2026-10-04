<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null');
            $table->enum('type', [
                'pos_setup',
                'pos_customization',
                'system_update',
                'website_development',
                'cpanel_maintenance',
                'other'
            ])->default('other');
            $table->enum('priority', ['high', 'medium', 'low'])->default('medium');
            $table->enum('status', [
                'pending',
                'in_progress',
                'on_hold',
                'under_review',
                'completed'
            ])->default('pending');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('deadline')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->boolean('is_reminder_sent')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
