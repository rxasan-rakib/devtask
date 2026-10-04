<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        try {
            DB::statement("ALTER TABLE `reminders` MODIFY `type` VARCHAR(50) NOT NULL DEFAULT 'custom'");
        } catch (\Throwable $e) {
            Schema::table('reminders', function (Blueprint $table) {
                $table->string('type', 50)->default('custom')->change();
            });
        }
    }

    public function down(): void
    {
        try {
            DB::statement("ALTER TABLE `reminders` MODIFY `type` VARCHAR(50) NOT NULL DEFAULT 'custom'");
        } catch (\Throwable $e) {
            Schema::table('reminders', function (Blueprint $table) {
                $table->string('type', 50)->default('custom')->change();
            });
        }
    }
};
