<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\ModulePermissionService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Synchronize all module catalog permissions and default roles
        ModulePermissionService::syncCatalogToDatabase();

        // Create default Super Admin user
        $admin = User::firstOrCreate(
            ['email' => 'support.fastit@gmail.com'],
            [
                'name' => 'FAST IT',
                'password' => Hash::make('fast@@Support'),
                'phone' => '01901166585',
                'designation' => 'System Administrator',
                'is_active' => true,
            ]
        );
        $admin->assignRole('super-admin');

        $this->command->info('✅ Roles, all module permissions, and default admin synchronized!');
        $this->command->info('📧 Email: support.fastit@gmail.com');
        $this->command->info('🔑 Password: fast@@Support');
    }
}
