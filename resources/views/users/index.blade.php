@extends('layouts.app')
@section('title', 'Team Members')
@section('page-title', 'Team Members')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>Team Members</h2>
        <p>Manage users, assign system roles, and configure access permissions</p>
    </div>
    <div class="page-header-actions">
        @can('create users')
        <a href="{{ route('users.create') }}" class="btn-gradient">
            <i data-lucide="user-plus" style="width:14px;height:14px;"></i> Add Member
        </a>
        @endcan
        @can('view roles')
        <a href="{{ route('roles.index') }}" class="btn-ghost">
            <i data-lucide="shield-check" style="width:14px;height:14px;"></i> Roles & Permissions
        </a>
        @endcan
    </div>
</div>
<div class="card-glass p-0 overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-dark-custom align-middle mb-0">
        <thead><tr><th>Member</th><th>Email</th><th>Role</th><th>Phone</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{ $user->avatar_url }}" style="width:32px;height:32px;border-radius:50%;" alt="">
                        <div>
                            <div style="font-weight:500;font-size:.875rem;">{{ $user->name }}</div>
                            <div style="font-size:.72rem;color:var(--text-muted);">{{ $user->designation ?? '—' }}</div>
                        </div>
                    </div>
                </td>
                <td style="font-size:.82rem;color:var(--text-muted);">{{ $user->email }}</td>
                <td>
                    @foreach($user->roles as $role)
                        @php
                            $roleStyle = match($role->name) {
                                'developer'           => 'background:rgba(6,182,212,.15);color:#06b6d4;',
                                'project-coordinator' => 'background:rgba(99,102,241,.15);color:#6366f1;',
                                'support-engineer'    => 'background:rgba(16,185,129,.15);color:#10b981;',
                                'super-admin'         => 'background:rgba(239,68,68,.15);color:#ef4444;',
                                default               => 'background:rgba(255,120,40,.15);color:#ff7828;'
                            };
                        @endphp
                        @can('view roles')
                            <a href="{{ route('roles.show', $role) }}" class="badge" style="{{ $roleStyle }}; text-decoration: none;" title="View Role Details">
                                {{ ucfirst(str_replace('-',' ',$role->name)) }}
                            </a>
                        @else
                            <span class="badge" style="{{ $roleStyle }}">{{ ucfirst(str_replace('-',' ',$role->name)) }}</span>
                        @endcan
                    @endforeach
                </td>
                <td style="font-size:.82rem;color:var(--text-muted);">{{ $user->phone ?? '—' }}</td>
                <td>
                    @if($user->is_active)
                        <span class="badge" style="background:rgba(16,185,129,.15);color:#10b981;">Active</span>
                    @else
                        <span class="badge" style="background:rgba(239,68,68,.15);color:#ef4444;">Inactive</span>
                    @endif
                </td>
                <td>
                    <div class="d-flex gap-1 align-items-center">
                        <a href="{{ route('users.show', $user) }}" class="btn-icon" style="width:30px;height:30px;" title="View Profile">
                            <i data-lucide="eye" style="width:13px;height:13px;"></i>
                        </a>
                        @if(!$user->isDefaultSuperAdmin())
                            @can('edit users')
                            <a href="{{ route('users.edit', $user) }}" class="btn-icon" style="width:30px;height:30px;" title="Edit Member">
                                <i data-lucide="pencil" style="width:13px;height:13px;"></i>
                            </a>
                            @endcan
                            @if($user->id !== auth()->id())
                                @can('delete users')
                                <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Remove this user?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-icon" style="width:30px;height:30px;color:#ef4444;border-color:rgba(239,68,68,.2);" title="Delete Member">
                                        <i data-lucide="trash-2" style="width:13px;height:13px;"></i>
                                    </button>
                                </form>
                                @endcan
                            @endif
                        @else
                            <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--accent); font-size: 0.72rem; padding: 4px 8px; border: 1px solid rgba(99,102,241,0.25); display: inline-flex; align-items: center; gap: 4px;">
                                <i data-lucide="shield-check" style="width: 12px; height: 12px;"></i> Protected
                            </span>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;padding:2.5rem;color:var(--text-muted);">No team members yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
<div class="mt-3">{{ $users->links() }}</div>
@endsection
