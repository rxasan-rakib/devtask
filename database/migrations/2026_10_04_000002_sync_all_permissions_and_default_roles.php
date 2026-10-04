<?php

use App\Services\ModulePermissionService;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Ensures all module catalog permissions exist in DB and default roles (including developer)
     * are granted 'view dashboard' and their required permissions.
     */
    public function up(): void
    {
        ModulePermissionService::syncCatalogToDatabase();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No destructive rollback needed for permissions catalog
    }
};
