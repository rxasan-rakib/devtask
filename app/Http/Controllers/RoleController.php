<?php

namespace App\Http\Controllers;

use App\Services\ModulePermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of all roles and permissions overview.
     */
    public function index()
    {
        abort_if(!auth()->user()->can('view roles'), 403, 'Unauthorized. You do not have permission to view roles.');

        $roles = Role::withCount(['users', 'permissions'])
            ->with('permissions')
            ->orderBy('id', 'asc')
            ->get();

        $modules = ModulePermissionService::getModules();
        $totalPermissions = Permission::count();
        $totalRoles = $roles->count();

        return view('roles.index', compact('roles', 'modules', 'totalPermissions', 'totalRoles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        abort_if(!auth()->user()->can('create roles'), 403, 'Unauthorized. You do not have permission to create roles.');

        $modules = ModulePermissionService::getModules();
        return view('roles.create', compact('modules'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('create roles'), 403, 'Unauthorized. You do not have permission to create roles.');

        $request->validate([
            'name'          => 'required|string|max:60|regex:/^[a-zA-Z0-9\s\-_]+$/|unique:roles,name',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ], [
            'name.regex' => 'The role name may only contain letters, numbers, spaces, hyphens, and underscores.',
        ]);

        $roleSlug = Str::slug($request->input('name'));

        // Check again after slugify if unique
        if (Role::where('name', $roleSlug)->exists()) {
            return back()->withInput()->withErrors(['name' => 'A role with this identifier already exists.']);
        }

        $role = Role::create([
            'name'       => $roleSlug,
            'guard_name' => 'web',
        ]);

        if ($request->has('permissions') && is_array($request->permissions)) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', "Role '{$role->name}' created successfully with " . count($request->permissions ?? []) . " permission(s)!");
    }

    /**
     * Display the specified role details and permission matrix.
     */
    public function show(Role $role)
    {
        abort_if(!auth()->user()->can('view roles'), 403, 'Unauthorized. You do not have permission to view roles.');

        $role->load(['users', 'permissions']);
        $modules = ModulePermissionService::getModules();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        $rolePermissionIds = $role->permissions->pluck('id')->toArray();

        return view('roles.show', compact('role', 'modules', 'rolePermissions', 'rolePermissionIds'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        abort_if(!auth()->user()->can('edit roles'), 403, 'Unauthorized. You do not have permission to edit roles.');

        $modules = ModulePermissionService::getModules();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        $rolePermissionIds = $role->permissions->pluck('id')->toArray();

        return view('roles.edit', compact('role', 'modules', 'rolePermissions', 'rolePermissionIds'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        abort_if(!auth()->user()->can('edit roles'), 403, 'Unauthorized. You do not have permission to edit roles.');

        $isSuperAdmin = ((int) $role->id === 1 || $role->name === 'super-admin');

        if (!$isSuperAdmin) {
            $request->validate([
                'name'          => 'required|string|max:60|regex:/^[a-zA-Z0-9\s\-_]+$/|unique:roles,name,' . $role->id,
                'permissions'   => 'nullable|array',
                'permissions.*' => 'string|exists:permissions,name',
            ], [
                'name.regex' => 'The role name may only contain letters, numbers, spaces, hyphens, and underscores.',
            ]);

            $roleSlug = Str::slug($request->input('name'));

            // Check if slug conflicts with another role
            $existing = Role::where('name', $roleSlug)->where('id', '!=', $role->id)->first();
            if ($existing) {
                return back()->withInput()->withErrors(['name' => 'A role with this slug identifier already exists.']);
            }

            $role->update(['name' => $roleSlug]);
            $role->syncPermissions($request->permissions ?? []);
        } else {
            // Super Admin always maintains all permissions
            $role->syncPermissions(Permission::all());
        }

        return redirect()->route('roles.index')->with('success', "Role '{$role->name}' updated successfully!");
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        abort_if(!auth()->user()->can('delete roles'), 403, 'Unauthorized. You do not have permission to delete roles.');

        if ((int) $role->id === 1 || $role->name === 'super-admin') {
            return back()->with('error', 'The default Super Admin role is protected and cannot be deleted.');
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', "Cannot delete role '{$role->name}' because it is assigned to {$role->users()->count()} team member(s). Please reassign them first.");
        }

        $roleName = $role->name;
        $role->syncPermissions([]);
        $role->delete();

        return redirect()->route('roles.index')->with('success', "Role '{$roleName}' has been removed successfully.");
    }
}
