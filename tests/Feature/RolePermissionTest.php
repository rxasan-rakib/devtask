<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\ModulePermissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed all module permissions
        $permissions = ModulePermissionService::getAllPermissionNames();
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }
    }

    public function test_super_admin_can_access_roles_index(): void
    {
        $admin = User::factory()->create();
        $adminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $admin->assignRole($adminRole);

        $response = $this->actingAs($admin)->get(route('roles.index'));
        $response->assertStatus(200);
        $response->assertSee('Roles & Permissions Management', false);
        $response->assertSee('Total System Roles');
    }

    public function test_super_admin_can_create_custom_dynamic_role(): void
    {
        $admin = User::factory()->create();
        $adminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $admin->assignRole($adminRole);

        $response = $this->actingAs($admin)->post(route('roles.store'), [
            'name' => 'Project Lead',
            'permissions' => [
                'view dashboard',
                'view tasks',
                'create tasks',
                'edit tasks',
                'view clients',
            ],
        ]);

        $response->assertRedirect(route('roles.index'));
        $this->assertDatabaseHas('roles', ['name' => 'project-lead']);

        $role = Role::findByName('project-lead', 'web');
        $this->assertTrue($role->hasPermissionTo('view tasks'));
        $this->assertTrue($role->hasPermissionTo('create tasks'));
        $this->assertFalse($role->hasPermissionTo('delete tasks'));
    }

    public function test_user_with_custom_role_inherits_permissions(): void
    {
        $customRole = Role::firstOrCreate(['name' => 'support-rep', 'guard_name' => 'web']);
        $customRole->syncPermissions(['view dashboard', 'view clients', 'create clients']);

        $user = User::factory()->create();
        $user->assignRole($customRole);

        $this->assertTrue($user->can('view clients'));
        $this->assertTrue($user->can('create clients'));
        $this->assertFalse($user->can('delete clients'));
        $this->assertFalse($user->can('view roles'));

        // Can access clients
        $this->actingAs($user)->get(route('clients.index'))->assertStatus(200);

        // Forbidden from roles
        $this->actingAs($user)->get(route('roles.index'))->assertStatus(403);
    }

    public function test_super_admin_role_cannot_be_deleted(): void
    {
        $admin = User::factory()->create();
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $admin->assignRole($superAdminRole);

        $response = $this->actingAs($admin)->delete(route('roles.destroy', $superAdminRole));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('roles', ['name' => 'super-admin']);
    }

    public function test_developer_role_can_access_dashboard_without_403(): void
    {
        ModulePermissionService::syncCatalogToDatabase();

        $developerUser = User::factory()->create();
        $developerRole = Role::findByName('developer', 'web');
        $developerUser->assignRole($developerRole);

        $this->assertTrue($developerUser->can('view dashboard'));

        $response = $this->actingAs($developerUser)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertDontSee('Unauthorized access to dashboard.');
    }

    public function test_module_permission_service_includes_dashboard_module(): void
    {
        ModulePermissionService::syncCatalogToDatabase();
        $modules = ModulePermissionService::getModules(true);

        $this->assertArrayHasKey('dashboard', $modules);
        $this->assertEquals('Dashboard & Analytics', $modules['dashboard']['name']);

        $permNames = array_column($modules['dashboard']['permissions'], 'name');
        $this->assertContains('view dashboard', $permNames);
        $this->assertContains('view all tasks stats', $permNames);
    }
}

