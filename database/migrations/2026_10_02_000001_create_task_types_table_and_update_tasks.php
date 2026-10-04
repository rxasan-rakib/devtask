<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create task_types table if it doesn't exist
        if (!Schema::hasTable('task_types')) {
            Schema::create('task_types', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('icon')->nullable()->default('tag');
                $table->string('color', 30)->nullable()->default('#6366f1');
                $table->text('description')->nullable();
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 2. Seed initial task types
        $defaultTypes = [
            ['name' => 'Software / App Development',  'slug' => 'software_development', 'icon' => 'code',         'color' => '#6366f1', 'order' => 1],
            ['name' => 'Website Development',         'slug' => 'website_development',  'icon' => 'globe',        'color' => '#10b981', 'order' => 2],
            ['name' => 'Bug Fix / Debugging',         'slug' => 'bug_fix',              'icon' => 'bug',          'color' => '#ef4444', 'order' => 3],
            ['name' => 'API / Payment Integration',   'slug' => 'api_integration',      'icon' => 'plug',         'color' => '#8b5cf6', 'order' => 4],
            ['name' => 'POS System Setup',            'slug' => 'pos_setup',            'icon' => 'shopping-bag', 'color' => '#0ea5e9', 'order' => 5],
            ['name' => 'POS Customization',           'slug' => 'pos_customization',    'icon' => 'sliders',      'color' => '#f59e0b', 'order' => 6],
            ['name' => 'System Update',               'slug' => 'system_update',        'icon' => 'refresh-cw',   'color' => '#3b82f6', 'order' => 7],
            ['name' => 'cPanel / Server Maintenance', 'slug' => 'cpanel_maintenance',   'icon' => 'server',       'color' => '#ec4899', 'order' => 8],
            ['name' => 'Other / General',             'slug' => 'other',                'icon' => 'folder',       'color' => '#64748b', 'order' => 9],
        ];

        foreach ($defaultTypes as $dt) {
            $exists = DB::table('task_types')->where('slug', $dt['slug'])->first();
            if (!$exists) {
                DB::table('task_types')->insert(array_merge($dt, [
                    'description' => null,
                    'is_active'   => true,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]));
            }
        }

        // 3. Add task_type_id to tasks table if not present
        if (!Schema::hasColumn('tasks', 'task_type_id')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->foreignId('task_type_id')
                      ->nullable()
                      ->after('client_id')
                      ->constrained('task_types')
                      ->onDelete('set null');
            });
        }

        // 4. Modify existing enum column `type` to varchar or nullable to prevent SQLSTATE[01000] Warning 1265 truncation errors
        try {
            DB::statement("ALTER TABLE `tasks` MODIFY `type` VARCHAR(100) NULL DEFAULT NULL");
        } catch (\Throwable $e) {
            // Ignored if driver is SQLite or already changed
        }

        // 5. Migrate existing tasks data to populate task_type_id
        $allTaskTypes = DB::table('task_types')->get()->keyBy('slug');
        $otherTypeId = $allTaskTypes->get('other')?->id;

        $tasks = DB::table('tasks')->whereNull('task_type_id')->get();
        foreach ($tasks as $task) {
            $typeSlug = $task->type ?? 'other';
            $matchedId = $allTaskTypes->get($typeSlug)?->id ?? $otherTypeId;
            if ($matchedId) {
                DB::table('tasks')->where('id', $task->id)->update([
                    'task_type_id' => $matchedId,
                ]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tasks', 'task_type_id')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropForeign(['task_type_id']);
                $table->dropColumn('task_type_id');
            });
        }

        Schema::dropIfExists('task_types');
    }
};
