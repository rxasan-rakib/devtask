<?php

namespace App\Services;

use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

class ModulePermissionService
{
    /**
     * Get all active modules and their database-verified permissions.
     * If a permission does not exist in DB, it is omitted.
     * If a module section has no permissions in DB, the section is completely omitted.
     */
    public static function getModules(bool $onlyExistingInDb = true): array
    {
        $rawModules = self::getRawDefinitions();

        if (!$onlyExistingInDb) {
            return $rawModules;
        }

        $dbPerms = null;
        try {
            if (Schema::hasTable('permissions')) {
                $dbPerms = Permission::all()->keyBy('name');
            }
        } catch (\Throwable $e) {
            $dbPerms = null;
        }

        if ($dbPerms === null) {
            return $rawModules;
        }

        $filteredModules = [];
        foreach ($rawModules as $modKey => $module) {
            $activePerms = [];
            foreach ($module['permissions'] as $perm) {
                if (isset($dbPerms[$perm['name']])) {
                    $dbRecord = $dbPerms[$perm['name']];
                    $perm['id'] = $dbRecord->id;
                    $activePerms[] = $perm;
                }
            }

            // Only show the module section if it contains at least one existing DB permission
            if (!empty($activePerms)) {
                $module['permissions'] = $activePerms;
                $filteredModules[$modKey] = $module;
            }
        }

        return $filteredModules;
    }

    /**
     * Get all raw module and permission definitions.
     */
    public static function getRawDefinitions(): array
    {
        return [
            'dashboard' => [
                'name'        => 'Dashboard & Analytics',
                'icon'        => 'layout-dashboard',
                'description' => 'System metrics, daily task feed, upcoming deadlines, and activity overview.',
                'permissions' => [
                    [
                        'name'        => 'view dashboard',
                        'label'       => 'View Dashboard',
                        'description' => 'Access main dashboard overview and activity stream.',
                        'action'      => 'read',
                    ],
                    [
                        'name'        => 'view all tasks stats',
                        'label'       => 'View All Company Stats',
                        'description' => 'View overall company task metrics across all team members.',
                        'action'      => 'read',
                    ],
                ],
            ],

            'tasks' => [
                'name'        => 'Tasks & Projects',
                'icon'        => 'list-checks',
                'description' => 'Task creation, kanban board, assignments, status tracking, and comments.',
                'permissions' => [
                    [
                        'name'        => 'view tasks',
                        'label'       => 'View Tasks',
                        'description' => 'Access task list, task details, and kanban board view.',
                        'action'      => 'read',
                    ],
                    [
                        'name'        => 'view all tasks',
                        'label'       => 'View All Company Tasks',
                        'description' => 'View all tasks in the company (otherwise only assigned tasks).',
                        'action'      => 'read',
                    ],
                    [
                        'name'        => 'create tasks',
                        'label'       => 'Create Tasks',
                        'description' => 'Create new tasks with deadlines, client links, and priorities.',
                        'action'      => 'create',
                    ],
                    [
                        'name'        => 'edit tasks',
                        'label'       => 'Edit Tasks',
                        'description' => 'Modify task details, deadlines, priorities, and descriptions.',
                        'action'      => 'update',
                    ],
                    [
                        'name'        => 'delete tasks',
                        'label'       => 'Delete Tasks',
                        'description' => 'Permanently delete tasks from the system.',
                        'action'      => 'delete',
                    ],
                    [
                        'name'        => 'assign tasks',
                        'label'       => 'Assign & Reassign Tasks',
                        'description' => 'Assign team members to tasks and change task assignees.',
                        'action'      => 'manage',
                    ],
                    [
                        'name'        => 'change task status',
                        'label'       => 'Change Task Status',
                        'description' => 'Update progress status (Pending, In Progress, Completed, etc.).',
                        'action'      => 'update',
                    ],
                    [
                        'name'        => 'add task comments',
                        'label'       => 'Add Task Comments',
                        'description' => 'Post comments, updates, and notes on task discussion timeline.',
                        'action'      => 'create',
                    ],
                    [
                        'name'        => 'delete task comments',
                        'label'       => 'Delete Task Comments',
                        'description' => 'Delete any comment or discussion note on tasks.',
                        'action'      => 'delete',
                    ],
                ],
            ],

            'clients' => [
                'name'        => 'Client Management',
                'icon'        => 'users',
                'description' => 'Client directory, system URLs, contact records, and client task history.',
                'permissions' => [
                    [
                        'name'        => 'view clients',
                        'label'       => 'View Clients',
                        'description' => 'Access client directory and view client profile details.',
                        'action'      => 'read',
                    ],
                    [
                        'name'        => 'create clients',
                        'label'       => 'Create Clients',
                        'description' => 'Add new client records, system URLs, and contact details.',
                        'action'      => 'create',
                    ],
                    [
                        'name'        => 'edit clients',
                        'label'       => 'Edit Clients',
                        'description' => 'Modify client information, system links, phone, and notes.',
                        'action'      => 'update',
                    ],
                    [
                        'name'        => 'delete clients',
                        'label'       => 'Delete Clients',
                        'description' => 'Remove clients and associated records from the directory.',
                        'action'      => 'delete',
                    ],
                ],
            ],

            'reminders' => [
                'name'        => 'Reminders & Alerts',
                'icon'        => 'bell',
                'description' => 'Automated email & browser alerts for domain renewals, deadlines, and meetings.',
                'permissions' => [
                    [
                        'name'        => 'view reminders',
                        'label'       => 'View Reminders',
                        'description' => 'View reminders list, filters, and scheduled alert alerts.',
                        'action'      => 'read',
                    ],
                    [
                        'name'        => 'create reminders',
                        'label'       => 'Create Reminders',
                        'description' => 'Create new reminders with date, time, recurring, and email alerts.',
                        'action'      => 'create',
                    ],
                    [
                        'name'        => 'edit reminders',
                        'label'       => 'Edit Reminders',
                        'description' => 'Edit reminder schedules, titles, and notification channels.',
                        'action'      => 'update',
                    ],
                    [
                        'name'        => 'delete reminders',
                        'label'       => 'Delete Reminders',
                        'description' => 'Remove scheduled reminders from the system.',
                        'action'      => 'delete',
                    ],
                    [
                        'name'        => 'mark reminders sent',
                        'label'       => 'Mark Sent / Complete',
                        'description' => 'Mark pending reminders as completed or sent.',
                        'action'      => 'update',
                    ],
                ],
            ],

            'notifications' => [
                'name'        => 'Notifications Center',
                'icon'        => 'bell-ring',
                'description' => 'Real-time alert delivery, task deadlines notifications, and alert management.',
                'permissions' => [
                    [
                        'name'        => 'view notifications',
                        'label'       => 'View Notifications',
                        'description' => 'Access notifications center, browse alert history, and view details.',
                        'action'      => 'read',
                    ],
                    [
                        'name'        => 'manage notifications',
                        'label'       => 'Manage Notifications',
                        'description' => 'Mark notifications as read or unread and trigger manual refreshes.',
                        'action'      => 'manage',
                    ],
                    [
                        'name'        => 'delete notifications',
                        'label'       => 'Delete Notifications',
                        'description' => 'Permanently delete notifications or clear read history.',
                        'action'      => 'delete',
                    ],
                ],
            ],

            'users' => [
                'name'        => 'Team Members',
                'icon'        => 'user-cog',
                'description' => 'User accounts, profile management, status control, and role assignments.',
                'permissions' => [
                    [
                        'name'        => 'view users',
                        'label'       => 'View Team Members',
                        'description' => 'Access team members directory and profile summaries.',
                        'action'      => 'read',
                    ],
                    [
                        'name'        => 'create users',
                        'label'       => 'Add Team Members',
                        'description' => 'Create new user accounts and assign designated roles.',
                        'action'      => 'create',
                    ],
                    [
                        'name'        => 'edit users',
                        'label'       => 'Edit Team Members',
                        'description' => 'Edit user profiles, job titles, active status, and roles.',
                        'action'      => 'update',
                    ],
                    [
                        'name'        => 'delete users',
                        'label'       => 'Delete Team Members',
                        'description' => 'Remove user accounts from the organization.',
                        'action'      => 'delete',
                    ],
                ],
            ],

            'roles' => [
                'name'        => 'Roles & Permissions',
                'icon'        => 'shield-check',
                'description' => 'Dynamic role creation, permission matrix management, and access control.',
                'permissions' => [
                    [
                        'name'        => 'view roles',
                        'label'       => 'View Roles',
                        'description' => 'View all system roles and their assigned permission matrix.',
                        'action'      => 'read',
                    ],
                    [
                        'name'        => 'create roles',
                        'label'       => 'Create Roles',
                        'description' => 'Create new dynamic roles and configure module permissions.',
                        'action'      => 'create',
                    ],
                    [
                        'name'        => 'edit roles',
                        'label'       => 'Edit Roles',
                        'description' => 'Modify role details and adjust granular permission checkboxes.',
                        'action'      => 'update',
                    ],
                    [
                        'name'        => 'delete roles',
                        'label'       => 'Delete Roles',
                        'description' => 'Remove custom dynamic roles from the system.',
                        'action'      => 'delete',
                    ],
                ],
            ],

            'settings' => [
                'name'        => 'System & SMTP Settings',
                'icon'        => 'settings',
                'description' => 'App branding, logos, SMTP email configuration, and notification triggers.',
                'permissions' => [
                    [
                        'name'        => 'view settings',
                        'label'       => 'View Settings',
                        'description' => 'Access system configuration, company branding, and SMTP status.',
                        'action'      => 'read',
                    ],
                    [
                        'name'        => 'edit settings',
                        'label'       => 'Edit Settings',
                        'description' => 'Update system branding, logos, SMTP credentials, and alert toggles.',
                        'action'      => 'update',
                    ],
                    [
                        'name'        => 'send test email',
                        'label'       => 'Send Test Email',
                        'description' => 'Trigger live SMTP test emails to verify mail server connection.',
                        'action'      => 'manage',
                    ],
                ],
            ],

            'reports' => [
                'name'        => 'Reports & Analytics',
                'icon'        => 'bar-chart-2',
                'description' => 'Team performance, completion statistics, client histories, and summaries.',
                'permissions' => [
                    [
                        'name'        => 'view reports',
                        'label'       => 'View Reports',
                        'description' => 'Access executive summaries, task completion reports, and audit logs.',
                        'action'      => 'read',
                    ],
                    [
                        'name'        => 'export data',
                        'label'       => 'Export Data',
                        'description' => 'Export system records, tasks, and client reports to CSV/Excel.',
                        'action'      => 'manage',
                    ],
                ],
            ],
        ];
    }

    /**
     * Get a flat list of all permission names defined across all modules.
     */
    public static function getAllPermissionNames(): array
    {
        $permissions = [];
        foreach (self::getRawDefinitions() as $module) {
            foreach ($module['permissions'] as $perm) {
                $permissions[] = $perm['name'];
            }
        }
        return array_unique($permissions);
    }

    /**
     * Get badge styling helper for permission actions.
     */
    public static function getActionBadge(string $action): array
    {
        return match($action) {
            'create' => ['bg' => 'rgba(14, 165, 233, 0.15)', 'color' => '#0ea5e9', 'label' => 'Create'],
            'read'   => ['bg' => 'rgba(16, 185, 129, 0.15)', 'color' => '#10b981', 'label' => 'View'],
            'update' => ['bg' => 'rgba(245, 158, 11, 0.15)', 'color' => '#f59e0b', 'label' => 'Edit'],
            'delete' => ['bg' => 'rgba(239, 68, 68, 0.15)',  'color' => '#ef4444', 'label' => 'Delete'],
            'manage' => ['bg' => 'rgba(168, 85, 247, 0.15)', 'color' => '#a855f7', 'label' => 'Manage'],
            default  => ['bg' => 'rgba(99, 102, 241, 0.15)', 'color' => '#6366f1', 'label' => 'Access'],
        };
    }
}
