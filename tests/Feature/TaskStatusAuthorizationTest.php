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

class TaskStatusAuthorizationTest extends TestCase
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

    public function test_assigned_user_can_change_task_status(): void
    {
        $role = Role::firstOrCreate(['name' => 'developer', 'guard_name' => 'web']);
        $role->syncPermissions(['view tasks', 'change task status']);

        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole($role);

        $client = Client::create([
            'name'       => 'Client One',
            'phone'      => '123456789',
            'type'       => 'pos',
            'created_by' => $user->id,
        ]);

        $task = Task::create([
            'title'       => 'Assigned Task Fix',
            'type'        => 'pos_setup',
            'priority'    => 'medium',
            'status'      => 'pending',
            'assigned_to' => $user->id,
            'created_by'  => $user->id,
        ]);

        $response = $this->actingAs($user)->patch(route('tasks.status', $task), [
            'status' => 'completed',
        ]);

        $response->assertRedirect();
        $this->assertSame('completed', $task->fresh()->status);
        $this->assertNotNull($task->fresh()->completed_at);
    }

    public function test_unassigned_user_cannot_change_task_status(): void
    {
        $role = Role::firstOrCreate(['name' => 'developer', 'guard_name' => 'web']);
        $role->syncPermissions(['view tasks', 'change task status']);

        /** @var User $user1 */
        $user1 = User::factory()->create();
        $user1->assignRole($role);

        /** @var User $user2 */
        $user2 = User::factory()->create();
        $user2->assignRole($role);

        $client = Client::create([
            'name'       => 'Client Two',
            'phone'      => '987654321',
            'type'       => 'website',
            'created_by' => $user1->id,
        ]);

        // Task assigned to User 1
        $task = Task::create([
            'title'       => 'Secret Task',
            'type'        => 'website_development',
            'priority'    => 'high',
            'status'      => 'pending',
            'assigned_to' => $user1->id,
            'created_by'  => $user1->id,
        ]);

        // User 2 tries to change status
        $response = $this->actingAs($user2)->patch(route('tasks.status', $task), [
            'status' => 'completed',
        ]);

        $response->assertStatus(403);
        $this->assertSame('pending', $task->fresh()->status);
    }

    public function test_super_admin_can_change_status_of_any_task(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        /** @var User $otherUser */
        $otherUser = User::factory()->create();

        $client = Client::create([
            'name'       => 'Client Three',
            'phone'      => '555555555',
            'type'       => 'other',
            'created_by' => $otherUser->id,
        ]);

        // Task assigned to otherUser
        $task = Task::create([
            'title'       => 'Other User Task',
            'type'        => 'pos_setup',
            'priority'    => 'medium',
            'status'      => 'pending',
            'assigned_to' => $otherUser->id,
            'created_by'  => $otherUser->id,
        ]);

        $response = $this->actingAs($admin)->patch(route('tasks.status', $task), [
            'status' => 'completed',
        ]);

        $response->assertRedirect();
        $this->assertSame('completed', $task->fresh()->status);
    }

    public function test_unassigned_user_cannot_alter_status_during_full_edit(): void
    {
        $role = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $role->syncPermissions(['view tasks', 'edit tasks', 'change task status']);

        /** @var User $user1 */
        $user1 = User::factory()->create();
        $user1->assignRole($role);

        /** @var User $user2 */
        $user2 = User::factory()->create();
        $user2->assignRole($role);

        $taskType = \App\Models\TaskType::firstOrCreate(['slug' => 'pos_setup'], [
            'name'        => 'POS Setup',
            'badge_color' => '#3b82f6',
            'is_active'   => true,
        ]);

        $task = Task::create([
            'title'        => 'Task For User 1',
            'task_type_id' => $taskType->id,
            'type'         => 'pos_setup',
            'priority'     => 'medium',
            'status'       => 'pending',
            'assigned_to'  => $user1->id,
            'created_by'   => $user1->id,
        ]);

        // User 2 edits the task title and attempts to change status to completed
        $response = $this->actingAs($user2)->put(route('tasks.update', $task), [
            'title'        => 'Updated Task Title',
            'task_type_id' => $taskType->id,
            'type'         => 'pos_setup',
            'priority'     => 'medium',
            'status'       => 'completed', // Not allowed for user2
            'assigned_to'  => $user1->id,
        ]);

        $response->assertSessionHasErrors('status');
        $this->assertSame('pending', $task->fresh()->status);
    }

    public function test_assigned_user_can_view_task_details(): void
    {
        $role = Role::firstOrCreate(['name' => 'developer', 'guard_name' => 'web']);
        $role->syncPermissions(['view tasks', 'change task status']);

        /** @var User $creator */
        $creator = User::factory()->create();
        /** @var User $assignedUser */
        $assignedUser = User::factory()->create();
        $assignedUser->assignRole($role);

        $task = Task::create([
            'title'       => 'Assigned Task Details',
            'type'        => 'pos_setup',
            'priority'    => 'medium',
            'status'      => 'pending',
            'assigned_to' => $assignedUser->id,
            'created_by'  => $creator->id,
        ]);

        $response = $this->actingAs($assignedUser)->get(route('tasks.show', $task));
        $response->assertStatus(200);
        $response->assertSee('Assigned Task Details');
    }

    public function test_unassigned_user_cannot_view_task_details(): void
    {
        $role = Role::firstOrCreate(['name' => 'developer', 'guard_name' => 'web']);
        $role->syncPermissions(['view tasks']);

        /** @var User $creator */
        $creator = User::factory()->create();
        /** @var User $assignedUser */
        $assignedUser = User::factory()->create();
        /** @var User $unassignedUser */
        $unassignedUser = User::factory()->create();
        $unassignedUser->assignRole($role);

        $task = Task::create([
            'title'       => 'Confidential Task Details',
            'type'        => 'pos_setup',
            'priority'    => 'medium',
            'status'      => 'pending',
            'assigned_to' => $assignedUser->id,
            'created_by'  => $creator->id,
        ]);

        $response = $this->actingAs($unassignedUser)->get(route('tasks.show', $task));
        $response->assertStatus(403);
    }

    public function test_creator_can_view_own_created_task_details(): void
    {
        $role = Role::firstOrCreate(['name' => 'developer', 'guard_name' => 'web']);
        $role->syncPermissions(['view tasks', 'create tasks']);

        /** @var User $creator */
        $creator = User::factory()->create();
        $creator->assignRole($role);

        /** @var User $otherUser */
        $otherUser = User::factory()->create();

        $task = Task::create([
            'title'       => 'Created by Developer Task',
            'type'        => 'pos_setup',
            'priority'    => 'medium',
            'status'      => 'pending',
            'assigned_to' => $otherUser->id,
            'created_by'  => $creator->id,
        ]);

        $response = $this->actingAs($creator)->get(route('tasks.show', $task));
        $response->assertStatus(200);
        $response->assertSee('Created by Developer Task');
    }

    public function test_user_with_view_all_tasks_can_view_any_task(): void
    {
        $role = Role::firstOrCreate(['name' => 'project-coordinator', 'guard_name' => 'web']);
        $role->syncPermissions(['view tasks', 'view all tasks']);

        /** @var User $coordinator */
        $coordinator = User::factory()->create();
        $coordinator->assignRole($role);

        /** @var User $otherUser1 */
        $otherUser1 = User::factory()->create();
        /** @var User $otherUser2 */
        $otherUser2 = User::factory()->create();

        $task = Task::create([
            'title'       => 'Team Project Task',
            'type'        => 'pos_setup',
            'priority'    => 'medium',
            'status'      => 'pending',
            'assigned_to' => $otherUser1->id,
            'created_by'  => $otherUser2->id,
        ]);

        $response = $this->actingAs($coordinator)->get(route('tasks.show', $task));
        $response->assertStatus(200);
        $response->assertSee('Team Project Task');
    }
}
