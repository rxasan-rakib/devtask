<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Task;
use App\Models\User;
use App\Services\ModulePermissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = ModulePermissionService::getAllPermissionNames();
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }
    }

    public function test_user_with_view_reports_permission_can_access_reports(): void
    {
        $role = Role::firstOrCreate(['name' => 'analyst', 'guard_name' => 'web']);
        $role->syncPermissions(['view reports', 'view dashboard']);

        $user = User::factory()->create();
        $user->assignRole($role);

        $client = Client::create([
            'name'       => 'Test Client Corp',
            'phone'      => '123456789',
            'email'      => 'client@test.com',
            'type'       => 'pos',
            'is_active'  => true,
            'created_by' => $user->id,
        ]);

        Task::create([
            'title'        => 'Setup POS Terminal',
            'description'  => 'Install software and configure DB',
            'client_id'    => $client->id,
            'type'         => 'pos_setup',
            'priority'     => 'high',
            'status'       => 'completed',
            'assigned_to'  => $user->id,
            'created_by'   => $user->id,
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('reports.index'));
        $response->assertStatus(200);
        $response->assertSee('Reports & Performance Analytics');
    }

    public function test_user_without_view_reports_is_forbidden(): void
    {
        $role = Role::firstOrCreate(['name' => 'viewer-only', 'guard_name' => 'web']);
        $role->syncPermissions(['view dashboard']);

        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)->get(route('reports.index'));
        $response->assertStatus(403);
    }

    public function test_user_with_export_permission_can_download_csv(): void
    {
        $role = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $role->syncPermissions(['view reports', 'export data']);

        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)->get(route('reports.export', ['type' => 'tasks']));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}
