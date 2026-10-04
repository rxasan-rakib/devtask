<?php

namespace App\Console\Commands;

use App\Services\ModulePermissionService;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SyncPermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:sync {--roles : Also reset default roles to standard preset permissions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize all defined module permissions and default roles into the database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Synchronizing system permissions and roles with module catalog...');

        ModulePermissionService::syncCatalogToDatabase();

        $totalPerms = Permission::count();
        $totalRoles = Role::count();

        $this->info("✓ Permissions synchronized successfully! Total permissions in DB: {$totalPerms}");
        $this->info("✓ Default roles checked and updated. Total roles in DB: {$totalRoles}");
        $this->info('✓ Spatie permission cache cleared.');

        return Command::SUCCESS;
    }
}
