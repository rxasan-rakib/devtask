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
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create or update all module permissions from catalog
        $allPermissionNames = ModulePermissionService::getAllPermissionNames();
        foreach ($allPermissionNames as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        // 1. Super Admin Role -> All permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        // 2. Project Coordinator
        $coordinator = Role::firstOrCreate(['name' => 'project-coordinator', 'guard_name' => 'web']);
        $coordinator->syncPermissions([
            'view dashboard',
            'view all tasks stats',
            'view tasks',
            'view all tasks',
            'create tasks',
            'edit tasks',
            'delete tasks',
            'assign tasks',
            'change task status',
            'add task comments',
            'delete task comments',
            'view clients',
            'create clients',
            'edit clients',
            'delete clients',
            'view reminders',
            'create reminders',
            'edit reminders',
            'delete reminders',
            'mark reminders sent',
            'view notifications',
            'manage notifications',
            'delete notifications',
            'view users',
            'create users',
            'edit users',
            'view roles',
            'view reports',
        ]);

        // 3. Support Engineer
        $engineer = Role::firstOrCreate(['name' => 'support-engineer', 'guard_name' => 'web']);
        $engineer->syncPermissions([
            'view dashboard',
            'view tasks',
            'create tasks',
            'edit tasks',
            'change task status',
            'add task comments',
            'view clients',
            'create clients',
            'view reminders',
            'create reminders',
            'edit reminders',
            'mark reminders sent',
            'view notifications',
            'manage notifications',
        ]);

        // 4. Developer
        $developer = Role::firstOrCreate(['name' => 'developer', 'guard_name' => 'web']);
        $developer->syncPermissions([
            'view dashboard',
            'view tasks',
            'change task status',
            'add task comments',
            'view clients',
            'view reminders',
            'create reminders',
            'edit reminders',
            'mark reminders sent',
            'view notifications',
            'manage notifications',
        ]);

        // 5. Viewer
        $viewer = Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);
        $viewer->syncPermissions([
            'view dashboard',
            'view tasks',
            'view clients',
            'view notifications',
            'view reports',
        ]);

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
