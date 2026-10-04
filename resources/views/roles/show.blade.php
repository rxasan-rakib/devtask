@extends('layouts.app')
@section('title', 'Role: ' . ucwords(str_replace('-', ' ', $role->name)))
@section('page-title', 'Role Details & Permissions')

@section('content')
@php
    $isSuperAdmin = ((int) $role->id === 1 || $role->name === 'super-admin');
    $roleColor = match($role->name) {
        'super-admin'         => '#ef4444',
        'project-coordinator' => '#6366f1',
        'support-engineer'    => '#10b981',
        'developer'           => '#06b6d4',
        'viewer'              => '#94a3b8',
        default               => '#ff7828'
    };
    $totalAllPerms = count(\App\Services\ModulePermissionService::getAllPermissionNames());
    $activePermCount = $isSuperAdmin ? $totalAllPerms : count($rolePermissions);
    $coveragePct = $totalAllPerms > 0 ? round(($activePermCount / $totalAllPerms) * 100) : 0;
@endphp

<style>
    .role-show-hero {
        background: linear-gradient(135deg, {{ $roleColor }}1a 0%, rgba(30, 41, 59, 0.6) 100%);
        border: 1px solid var(--border);
        border-top: 3px solid {{ $roleColor }};
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        backdrop-filter: blur(12px);
    }

    .role-tab-nav {
        display: flex;
        gap: 8px;
        border-bottom: 1px solid var(--border);
        margin-bottom: 1.5rem;
        padding-bottom: 2px;
    }
    .role-tab-btn {
        background: transparent;
        border: none;
        border-bottom: 2px solid transparent;
        padding: 0.6rem 1.1rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-muted);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        margin-bottom: -1px;
    }
    .role-tab-btn:hover {
        color: var(--text-primary);
    }
    .role-tab-btn.active {
        color: var(--accent);
        border-bottom-color: var(--accent);
    }

    .member-grid-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1rem;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }
    .member-grid-card:hover {
        transform: translateY(-2px);
        border-color: var(--accent);
        box-shadow: 0 6px 18px rgba(0,0,0,0.15);
    }

    .perm-show-card {
        padding: 0.75rem 0.85rem;
        border-radius: 10px;
        border: 1px solid var(--border);
        display: flex;
        align-items: flex-start;
        gap: 0.65rem;
        transition: all 0.15s ease;
        height: 100%;
    }
    .perm-show-card.active-perm {
        background: var(--bg-card);
        border-color: rgba(99, 102, 241, 0.25);
    }
    .perm-show-card.inactive-perm {
        background: rgba(0, 0, 0, 0.08);
        opacity: 0.5;
        border-style: dashed;
    }
</style>

{{-- Hero Identity Banner --}}
<div class="role-show-hero d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div class="d-flex align-items-center gap-3">
        <div style="width:54px;height:54px;border-radius:14px;background:{{ $roleColor }}22;color:{{ $roleColor }};border:1.5px solid {{ $roleColor }}44;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(0,0,0,0.2);">
            @if($isSuperAdmin)
                <i data-lucide="crown" style="width:28px;height:28px;"></i>
            @else
                <i data-lucide="shield-check" style="width:28px;height:28px;"></i>
            @endif
        </div>
        <div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h2 class="mb-0" style="font-size:1.45rem;font-weight:800;color:var(--text-primary);letter-spacing:-0.02em;">
                    {{ ucwords(str_replace('-', ' ', $role->name)) }}
                </h2>
                <span class="badge" style="font-family:monospace;font-size:0.70rem;background:var(--bg-hover);color:var(--text-muted);border:1px solid var(--border);">
                    slug: {{ $role->name }}
                </span>
                @if($isSuperAdmin)
                    <span class="badge" style="background:rgba(239,68,68,0.15);color:#ef4444;font-size:0.70rem;font-weight:700;">
                        👑 Built-in Super Admin
                    </span>
                @endif
            </div>
            <div class="text-muted mt-1" style="font-size:0.82rem;">
                {{ $role->users->count() }} {{ Str::plural('Team Member', $role->users->count()) }} Assigned &bull; {{ $activePermCount }} / {{ $totalAllPerms }} Capabilities Active ({{ $coveragePct }}% System Coverage)
            </div>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2">
        @if(!$isSuperAdmin && auth()->user()->can('edit roles'))
            <a href="{{ route('roles.edit', $role) }}" class="btn-gradient" style="padding:0.55rem 1.1rem;font-size:0.82rem;">
                <i data-lucide="edit-3" style="width:14px;height:14px;"></i> Edit Permissions
            </a>
        @endif
        <a href="{{ route('roles.index') }}" class="btn-ghost" style="padding:0.55rem 1rem;font-size:0.82rem;">
            <i data-lucide="arrow-left" style="width:14px;height:14px;"></i> All Roles
        </a>
    </div>
</div>

{{-- Navigation Tabs --}}
<div class="role-tab-nav">
    <button type="button" class="role-tab-btn active" onclick="switchRoleTab('permissions', this)">
        <i data-lucide="key" style="width:15px;height:15px;"></i> Granted Permissions ({{ $activePermCount }})
    </button>
    <button type="button" class="role-tab-btn" onclick="switchRoleTab('members', this)">
        <i data-lucide="users" style="width:15px;height:15px;"></i> Assigned Members ({{ $role->users->count() }})
    </button>
    <button type="button" class="role-tab-btn" onclick="switchRoleTab('developer', this)">
        <i data-lucide="code" style="width:15px;height:15px;"></i> Code & Gate Reference
    </button>
</div>

{{-- TAB 1: PERMISSIONS MATRIX --}}
<div id="tab-permissions" class="role-tab-content">
    {{-- Search & Filter Toolbar --}}
    <div class="card-glass p-3 mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3" style="border-radius:14px;">
        <div class="position-relative" style="flex:1;max-width:380px;">
            <i data-lucide="search" style="position: absolute; left: 11px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: var(--text-muted);"></i>
            <input type="text" id="showPermSearch" class="form-control form-control-dark" placeholder="Filter permissions..." style="padding-left: 32px; height: 36px; font-size: 0.8rem; border-radius: 8px;">
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge" style="background:rgba(16,185,129,0.15);color:#10b981;padding:4px 8px;font-size:0.72rem;">
                <i data-lucide="check" style="width:11px;height:11px;display:inline;"></i> {{ $activePermCount }} Active
            </span>
            <span class="badge" style="background:var(--bg-hover);color:var(--text-muted);padding:4px 8px;font-size:0.72rem;border:1px solid var(--border);">
                <i data-lucide="minus" style="width:11px;height:11px;display:inline;"></i> {{ $totalAllPerms - $activePermCount }} Inactive
            </span>
        </div>
    </div>

    <div class="row g-3" id="showModulesContainer">
        @foreach($modules as $moduleKey => $module)
            @php
                $modPermNames = array_column($module['permissions'], 'name');
                $modActiveCount = $isSuperAdmin ? count($modPermNames) : count(array_intersect($modPermNames, $rolePermissions));
            @endphp
            <div class="col-12 show-module-card">
                <div class="card-glass" style="border-radius:14px;padding:1.25rem;">
                    <div class="d-flex align-items-center justify-content-between pb-2 border-bottom mb-3" style="border-color:var(--border) !important;">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:32px;height:32px;border-radius:8px;background:rgba(99,102,241,0.12);color:var(--accent);display:flex;align-items:center;justify-content:center;">
                                <i data-lucide="{{ $module['icon'] }}" style="width:16px;height:16px;"></i>
                            </div>
                            <div>
                                <h5 class="mb-0" style="font-size:0.92rem;font-weight:700;color:var(--text-primary);">
                                    {{ $module['name'] }}
                                </h5>
                                <div style="font-size:0.70rem;color:var(--text-muted);">
                                    {{ $module['description'] }}
                                </div>
                            </div>
                        </div>
                        <span class="badge" style="background:{{ $modActiveCount === count($modPermNames) ? 'rgba(16,185,129,0.15)' : ($modActiveCount > 0 ? 'rgba(99,102,241,0.15)' : 'var(--bg-hover)') }};color:{{ $modActiveCount === count($modPermNames) ? '#10b981' : ($modActiveCount > 0 ? 'var(--accent)' : 'var(--text-muted)') }};font-size:0.68rem;padding:3px 7px;">
                            {{ $modActiveCount }} / {{ count($modPermNames) }} Allowed
                        </span>
                    </div>

                    <div class="row g-2">
                        @foreach($module['permissions'] as $perm)
                            @php
                                $hasPerm = $isSuperAdmin || in_array($perm['name'], $rolePermissions) || (isset($perm['id']) && in_array($perm['id'], $rolePermissionIds ?? []));
                                $badge = \App\Services\ModulePermissionService::getActionBadge($perm['action']);
                            @endphp
                            <div class="col-md-6 col-xl-4 show-perm-col" data-name="{{ strtolower($perm['label'] . ' ' . $perm['description'] . ' ' . $perm['name']) }}">
                                <div class="perm-show-card {{ $hasPerm ? 'active-perm' : 'inactive-perm' }}">
                                    <div style="margin-top:2px;">
                                        @if($hasPerm)
                                            <div style="width:20px;height:20px;border-radius:50%;background:rgba(16,185,129,0.15);color:#10b981;display:flex;align-items:center;justify-content:center;">
                                                <i data-lucide="check" style="width:12px;height:12px;stroke-width:2.5;"></i>
                                            </div>
                                        @else
                                            <div style="width:20px;height:20px;border-radius:50%;background:rgba(148,163,184,0.1);color:var(--text-muted);display:flex;align-items:center;justify-content:center;">
                                                <i data-lucide="x" style="width:11px;height:11px;"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div style="flex:1;min-width:0;">
                                        <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                                            <span style="font-size:0.80rem;font-weight:700;color:{{ $hasPerm ? 'var(--text-primary)' : 'var(--text-muted)' }};line-height:1.2;">
                                                {{ $perm['label'] }}
                                            </span>
                                            <span class="badge" style="background:{{ $badge['bg'] }};color:{{ $badge['color'] }};font-size:0.58rem;padding:2px 5px;border-radius:4px;">
                                                {{ $badge['label'] }}
                                            </span>
                                        </div>
                                        <div style="font-size:0.68rem;color:var(--text-muted);line-height:1.3;">
                                            {{ $perm['description'] }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- TAB 2: ASSIGNED TEAM MEMBERS --}}
<div id="tab-members" class="role-tab-content d-none">
    <div class="row g-3">
        @forelse($role->users as $user)
            <div class="col-md-6 col-xl-4">
                <div class="member-grid-card">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" style="width:44px;height:44px;border-radius:50%;object-fit:cover;border:2px solid var(--accent);">
                    <div style="flex:1;min-width:0;">
                        <div class="d-flex align-items-center justify-content-between gap-1">
                            <h6 class="mb-0 text-truncate" style="font-size:0.88rem;font-weight:700;color:var(--text-primary);">
                                {{ $user->name }}
                            </h6>
                            @if($user->is_active)
                                <span class="badge" style="background:rgba(16,185,129,0.15);color:#10b981;font-size:0.60rem;">Active</span>
                            @else
                                <span class="badge" style="background:rgba(239,68,68,0.15);color:#ef4444;font-size:0.60rem;">Inactive</span>
                            @endif
                        </div>
                        <div class="text-muted text-truncate" style="font-size:0.75rem;">
                            {{ $user->email }}
                        </div>
                        <div class="text-muted text-truncate mt-1" style="font-size:0.70rem;">
                            <i data-lucide="badge-check" style="width:11px;height:11px;display:inline;color:var(--accent);"></i>
                            {{ $user->designation ?? 'Team Member' }}
                        </div>
                    </div>
                    @can('edit users')
                        <a href="{{ route('users.edit', $user) }}" class="btn-icon" title="Edit Member Profile" style="width:28px;height:28px;">
                            <i data-lucide="edit-2" style="width:13px;height:13px;"></i>
                        </a>
                    @endcan
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card-glass text-center py-5" style="border-radius:14px;">
                    <i data-lucide="user-x" style="width:40px;height:40px;color:var(--text-muted);margin-bottom:0.75rem;"></i>
                    <h5 class="text-muted">No members assigned to this role</h5>
                    <p style="font-size:0.85rem;color:var(--text-muted);max-width:400px;margin:0 auto 1rem;">
                        Go to the Team Members section to assign members to this role.
                    </p>
                    @can('view users')
                        <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-primary" style="font-size:0.78rem;border-radius:8px;">
                            Manage Team Members
                        </a>
                    @endcan
                </div>
            </div>
        @endforelse
    </div>
</div>

{{-- TAB 3: DEVELOPER / GATE REFERENCE --}}
<div id="tab-developer" class="role-tab-content d-none">
    <div class="card-glass p-4" style="border-radius:14px;">
        <h5 style="font-weight:700;color:var(--text-primary);margin-bottom:1rem;">
            <i data-lucide="terminal" style="width:16px;height:16px;color:var(--accent);display:inline;margin-right:4px;"></i>
            IAM & Gate Code Reference
        </h5>
        <p class="text-muted" style="font-size:0.82rem;margin-bottom:1.5rem;">
            Use standard Laravel authorization directives in Blade views and PHP controllers to verify capabilities for this role dynamically.
        </p>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="p-3 rounded-3" style="background:var(--bg-hover);border:1px solid var(--border);">
                    <div style="font-size:0.75rem;font-weight:700;color:var(--accent);text-transform:uppercase;margin-bottom:0.5rem;">
                        Blade View Directives
                    </div>
                    <pre style="background:rgba(0,0,0,0.3);padding:0.75rem;border-radius:8px;font-size:0.75rem;color:#e2e8f0;margin-bottom:0;overflow-x:auto;"><code>&#64;can('view tasks')
    &lt;!-- Renders if role has 'view tasks' --&gt;
    &lt;a href="&#123;&#123; route('tasks.index') &#125;&#125;"&gt;Tasks&lt;/a&gt;
&#64;endcan

&#64;hasrole('{{ $role->name }}')
    &lt;!-- Specific to {{ $role->name }} role --&gt;
&#64;endhasrole</code></pre>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 rounded-3" style="background:var(--bg-hover);border:1px solid var(--border);">
                    <div style="font-size:0.75rem;font-weight:700;color:#10b981;text-transform:uppercase;margin-bottom:0.5rem;">
                        Controller Authorization
                    </div>
                    <pre style="background:rgba(0,0,0,0.3);padding:0.75rem;border-radius:8px;font-size:0.75rem;color:#e2e8f0;margin-bottom:0;overflow-x:auto;"><code>// Check permission
abort_if(!auth()->user()->can('view tasks'), 403);

// Or via user instance
if ($user->hasPermissionTo('create clients')) {
    // Authorized
}</code></pre>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function switchRoleTab(tabName, btn) {
        document.querySelectorAll('.role-tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.role-tab-content').forEach(c => c.classList.add('d-none'));

        btn.classList.add('active');
        const target = document.getElementById('tab-' + tabName);
        if (target) target.classList.remove('d-none');

        if (window.lucide) lucide.createIcons();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('showPermSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const q = this.value.toLowerCase().trim();
                const cols = document.querySelectorAll('.show-perm-col');
                const moduleCards = document.querySelectorAll('.show-module-card');

                moduleCards.forEach(mod => {
                    let match = 0;
                    mod.querySelectorAll('.show-perm-col').forEach(col => {
                        const name = col.getAttribute('data-name') || '';
                        if (!q || name.includes(q)) {
                            col.style.display = '';
                            match++;
                        } else {
                            col.style.display = 'none';
                        }
                    });
                    mod.style.display = match > 0 ? '' : 'none';
                });
            });
        }
    });
</script>
@endsection
