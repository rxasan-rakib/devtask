<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('view users'), 403, 'Unauthorized access to user management.');

        $users = User::with('roles')->orderBy('name')->paginate(20)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        abort_if(Gate::denies('create users'), 403, 'Unauthorized to create user accounts.');

        $roles = Role::withCount('permissions')->get();

        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('create users'), 403, 'Unauthorized to create user accounts.');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'phone' => 'nullable|string',
            'designation' => 'nullable|string',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'designation' => $validated['designation'] ?? null,
            'is_active' => true,
        ]);
        $user->assignRole($validated['role']);

        return redirect()->route('users.index')->with('success', 'Team member added!');
    }

    public function show(User $user)
    {
        abort_if(Gate::denies('view users'), 403, 'Unauthorized access.');

        $user->load('roles.permissions', 'assignedTasks', 'createdTasks');

        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        abort_if(Gate::denies('edit users'), 403, 'Unauthorized to edit user accounts.');

        if ($user->isDefaultSuperAdmin()) {
            return redirect()->route('users.index')->with('error', 'The default Super Admin account is protected and cannot be edited.');
        }

        $roles = Role::withCount('permissions')->get();

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        abort_if(Gate::denies('edit users'), 403, 'Unauthorized to update user accounts.');

        if ($user->isDefaultSuperAdmin()) {
            return redirect()->route('users.index')->with('error', 'The default Super Admin account is protected and cannot be edited.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phone' => 'nullable|string',
            'designation' => 'nullable|string',
            'role' => 'required|exists:roles,name',
            'is_active' => 'boolean',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'designation' => $validated['designation'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if (! empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->syncRoles([$validated['role']]);

        return redirect()->route('users.index')->with('success', 'Team member updated!');
    }

    public function destroy(User $user)
    {
        abort_if(Gate::denies('delete users'), 403, 'Unauthorized to delete users.');

        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        if ($user->isDefaultSuperAdmin()) {
            return back()->with('error', 'The default Super Admin account cannot be deleted!');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User removed.');
    }
}
