@extends('layouts.app')
@section('title', 'Roles & Permissions')
@section('page-title', 'Roles & Access Control')

@section('content')
<style>
    /* ── Roles Custom Ultra-Modern UI Styles ── */
    .role-hero-banner {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.12) 0%, rgba(255, 120, 40, 0.08) 50%, rgba(16, 185, 129, 0.08) 100%);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(12px);
    }
    .role-hero-banner::before {
        content: '';
        position: absolute;
        top: -60px;
        right: -40px;
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.25) 0%, transparent 70%);
        pointer-events: none;
        border-radius: 50%;
    }

    .role-card-premium {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 1.25rem;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
    }
    .role-card-premium:hover {
        transform: translateY(-3px);
        border-color: var(--accent);
        box-shadow: 0 12px 28px -6px rgba(0, 0, 0, 0.35), 0 0 0 1px rgba(99, 102, 241, 0.25);
    }

    .role-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .avatar-stack {
        display: flex;
        align-items: center;
    }
    .avatar-stack-item {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        border: 2px solid var(--bg-card);
        margin-left: -8px;
        object-fit: cover;
        background: var(--bg-hover);
    }
    .avatar-stack-item:first-child {
        margin-left: 0;
    }
    .avatar-stack-more {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: var(--bg-hover);
        border: 2px solid var(--bg-card);
        margin-left: -8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.62rem;
        font-weight: 700;
        color: var(--text-muted);
    }

    .progress-track-premium {
        height: 6px;
        background: var(--bg-hover);
        border-radius: 10px;
        overflow: hidden;
        position: relative;
    }
    .progress-fill-premium {
        height: 100%;
        border-radius: 10px;
        transition: width 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .module-mini-tag {
        font-size: 0.68rem;
        padding: 3px 7px;
        border-radius: 6px;
        background: var(--bg-hover);
        border: 1px solid var(--border);
        color: var(--text-muted);
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-weight: 500;
    }

    /* Matrix Table Styles */
    .matrix-table-wrap {
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
        background: var(--bg-card);
    }
    .matrix-table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }
    .matrix-table th {
        background: var(--bg-hover);
        padding: 0.85rem 1rem;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: var(--text-primary);
        border-bottom: 1px solid var(--border);
        white-space: nowrap;
    }
    .matrix-table td {
        padding: 0.75rem 1rem;
        font-size: 0.82rem;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }
    .matrix-table tr:last-child td {
        border-bottom: none;
    }
    .matrix-table tr:hover td {
        background: rgba(99, 102, 241, 0.03);
    }
    .matrix-module-header {
        background: rgba(99, 102, 241, 0.08) !important;
        font-weight: 700 !important;
        font-size: 0.82rem !important;
        color: var(--accent) !important;
    }

    .view-switch-btn {
        padding: 0.35rem 0.75rem;
        font-size: 0.78rem;
        font-weight: 600;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: var(--bg-secondary);
        color: var(--text-muted);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.15s ease;
    }
    .view-switch-btn.active {
        background: var(--accent);
        color: #ffffff;
        border-color: var(--accent);
        box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
    }
</style>

{{-- Hero Header --}}
<div class="role-hero-banner d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div style="z-index: 1;">
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge" style="background:rgba(99,102,241,0.2);color:var(--accent);font-weight:700;font-size:0.72rem;letter-spacing:0.04em;">
                SECURITY & IAM
            </span>
            <span class="badge" style="background:rgba(16,185,129,0.15);color:#10b981;font-weight:600;font-size:0.72rem;">
                Live Dynamic Matrix
            </span>
        </div>
        <h2 class="mb-1" style="font-size:1.45rem;font-weight:800;color:var(--text-primary);letter-spacing:-0.02em;">
            Roles & Permissions Management
        </h2>
        <p class="mb-0 text-muted" style="font-size:0.85rem;max-width:650px;">
            Configure granular access control, create customized team roles, and manage permissions across all 8 core system modules in real time.
        </p>
    </div>
    <div class="d-flex align-items-center gap-2" style="z-index: 1;">
        @can('create roles')
        <a href="{{ route('roles.create') }}" class="btn-gradient" style="padding:0.6rem 1.2rem;font-size:0.85rem;box-shadow:0 4px 14px rgba(99,102,241,0.35);">
            <i data-lucide="plus-circle" style="width:16px;height:16px;"></i> Create New Role
        </a>
        @endcan
    </div>
</div>

{{-- Stat Metric Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center gap-3" style="border-radius:14px;padding:1.15rem;">
            <div class="stat-icon" style="background: rgba(99, 102, 241, 0.15); color: var(--accent);width:44px;height:44px;border-radius:12px;">
                <i data-lucide="shield-check" style="width: 22px; height: 22px;"></i>
            </div>
            <div>
                <div class="stat-value" style="font-size:1.4rem;font-weight:800;">{{ $totalRoles }}</div>
                <div class="stat-label" style="font-size:0.75rem;">Total System Roles</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center gap-3" style="border-radius:14px;padding:1.15rem;">
            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;width:44px;height:44px;border-radius:12px;">
                <i data-lucide="key" style="width: 22px; height: 22px;"></i>
            </div>
            <div>
                <div class="stat-value" style="font-size:1.4rem;font-weight:800;">{{ $totalPermissions }}</div>
                <div class="stat-label" style="font-size:0.75rem;">Granular Permissions</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center gap-3" style="border-radius:14px;padding:1.15rem;">
            <div class="stat-icon" style="background: rgba(255, 120, 40, 0.15); color: #ff7828;width:44px;height:44px;border-radius:12px;">
                <i data-lucide="grid" style="width: 22px; height: 22px;"></i>
            </div>
            <div>
                <div class="stat-value" style="font-size:1.4rem;font-weight:800;">{{ count($modules) }}</div>
                <div class="stat-label" style="font-size:0.75rem;">Protected Modules</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center gap-3" style="border-radius:14px;padding:1.15rem;">
            <div class="stat-icon" style="background: rgba(14, 165, 233, 0.15); color: #0ea5e9;width:44px;height:44px;border-radius:12px;">
                <i data-lucide="users" style="width: 22px; height: 22px;"></i>
            </div>
            <div>
                <div class="stat-value" style="font-size:1.4rem;font-weight:800;">
                    {{ \App\Models\User::count() }}
                </div>
                <div class="stat-label" style="font-size:0.75rem;">Active Team Members</div>
            </div>
        </div>
    </div>
</div>

{{-- Filter & View Switcher Bar --}}
<div class="card-glass p-3 mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3" style="border-radius:14px;">
    <div class="d-flex align-items-center gap-2 flex-wrap" style="flex:1;max-width:550px;">
        <div class="position-relative" style="flex:1;min-width:220px;">
            <i data-lucide="search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: var(--text-muted);"></i>
            <input type="text" id="roleSearchInput" class="form-control form-control-dark w-100" placeholder="Search roles by name or slug..." style="padding-left: 34px; height: 36px; font-size: 0.8rem; border-radius: 8px;">
        </div>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-sm btn-outline-secondary role-filter-btn active" data-filter="all" style="font-size:0.75rem;padding:0.35rem 0.75rem;">All Roles</button>
            <button type="button" class="btn btn-sm btn-outline-secondary role-filter-btn" data-filter="builtin" style="font-size:0.75rem;padding:0.35rem 0.75rem;">Built-in</button>
            <button type="button" class="btn btn-sm btn-outline-secondary role-filter-btn" data-filter="custom" style="font-size:0.75rem;padding:0.35rem 0.75rem;">Custom</button>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2">
        <span class="text-muted d-none d-sm-inline" style="font-size:0.75rem;font-weight:600;">VIEW:</span>
        <button type="button" class="view-switch-btn active" id="btnViewCards" onclick="switchRoleView('cards')">
            <i data-lucide="layout-grid" style="width:14px;height:14px;"></i> Cards View
        </button>
        <button type="button" class="view-switch-btn" id="btnViewMatrix" onclick="switchRoleView('matrix')">
            <i data-lucide="table" style="width:14px;height:14px;"></i> Matrix Table
        </button>
    </div>
</div>

{{-- 1. CARDS GRID VIEW --}}
<div id="rolesCardsView" class="row g-3">
    @forelse($roles as $role)
        @php
            $isSuperAdmin = ((int) $role->id === 1 || $role->name === 'super-admin');
            $isDefaultRole = in_array($role->name, ['super-admin', 'project-coordinator', 'support-engineer', 'developer', 'viewer']);
            $roleColor = match($role->name) {
                'super-admin'         => '#ef4444',
                'project-coordinator' => '#6366f1',
                'support-engineer'    => '#10b981',
                'developer'           => '#06b6d4',
                'viewer'              => '#94a3b8',
                default               => '#ff7828'
            };
            $percent = $totalPermissions > 0 ? round(($role->permissions_count / $totalPermissions) * 100) : 0;
            if ($isSuperAdmin) $percent = 100;
        @endphp
        <div class="col-md-6 col-xl-4 role-grid-item" data-name="{{ strtolower($role->name . ' ' . str_replace('-', ' ', $role->name)) }}" data-type="{{ $isDefaultRole ? 'builtin' : 'custom' }}">
            <div class="role-card-premium">
                {{-- Role Top Bar --}}
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="role-icon-box" style="background: {{ $roleColor }}1a; color: {{ $roleColor }}; border: 1px solid {{ $roleColor }}33;">
                            @if($isSuperAdmin)
                                <i data-lucide="crown" style="width:22px;height:22px;"></i>
                            @elseif($role->name === 'project-coordinator')
                                <i data-lucide="briefcase" style="width:22px;height:22px;"></i>
                            @elseif($role->name === 'support-engineer')
                                <i data-lucide="headphones" style="width:22px;height:22px;"></i>
                            @elseif($role->name === 'developer')
                                <i data-lucide="code" style="width:22px;height:22px;"></i>
                            @elseif($role->name === 'viewer')
                                <i data-lucide="eye" style="width:22px;height:22px;"></i>
                            @else
                                <i data-lucide="shield" style="width:22px;height:22px;"></i>
                            @endif
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h4 class="mb-0" style="font-size:1.05rem;font-weight:700;color:var(--text-primary);letter-spacing:-0.01em;">
                                    {{ ucwords(str_replace('-', ' ', $role->name)) }}
                                </h4>
                            </div>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span class="badge" style="font-family:monospace;font-size:0.65rem;background:var(--bg-hover);color:var(--text-muted);border:1px solid var(--border);">
                                    {{ $role->name }}
                                </span>
                                @if($isSuperAdmin)
                                    <span class="badge" style="background:rgba(239,68,68,0.15);color:#ef4444;font-size:0.65rem;font-weight:700;">
                                        👑 Super Admin
                                    </span>
                                @elseif($isDefaultRole)
                                    <span class="badge" style="background:rgba(99,102,241,0.12);color:var(--accent);font-size:0.65rem;font-weight:600;">
                                        Standard
                                    </span>
                                @else
                                    <span class="badge" style="background:rgba(255,120,40,0.15);color:#ff7828;font-size:0.65rem;font-weight:600;">
                                        Custom
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="dropdown">
                        <button class="btn-icon" data-bs-toggle="dropdown" aria-expanded="false" style="width:28px;height:28px;">
                            <i data-lucide="more-vertical" style="width:14px;height:14px;"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="min-width: 160px; font-size:0.8rem; border-radius:10px;">
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('roles.show', $role) }}">
                                    <i data-lucide="eye" style="width:13px;height:13px;color:var(--accent);"></i> View Capabilities
                                </a>
                            </li>
                            @can('edit roles')
                            @if(!$isSuperAdmin)
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('roles.edit', $role) }}">
                                        <i data-lucide="edit-3" style="width:13px;height:13px;color:#10b981;"></i> Edit Permissions
                                    </a>
                                </li>
                            @endif
                            @endcan
                            @can('delete roles')
                            @if(!$isSuperAdmin)
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('roles.destroy', $role) }}" onsubmit="return confirm('Are you sure you want to delete role \'{{ $role->name }}\'? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-danger py-2">
                                            <i data-lucide="trash-2" style="width:13px;height:13px;"></i> Delete Role
                                        </button>
                                    </form>
                                </li>
                            @endif
                            @endcan
                        </ul>
                    </div>
                </div>

                {{-- Permission Coverage Progress Bar --}}
                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-1" style="font-size:0.75rem;">
                        <span style="color:var(--text-muted);font-weight:500;">Capability Coverage</span>
                        <span style="font-weight:700;color:{{ $roleColor }};">
                            {{ $isSuperAdmin ? 'Full Access' : ($role->permissions_count . ' / ' . $totalPermissions) }} ({{ $percent }}%)
                        </span>
                    </div>
                    <div class="progress-track-premium">
                        <div class="progress-fill-premium" style="width: {{ $percent }}%; background: linear-gradient(90deg, {{ $roleColor }}, {{ $roleColor }}cc);"></div>
                    </div>
                </div>

                {{-- Module Tags Matrix preview --}}
                <div class="mb-3 flex-grow-1">
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($modules as $modKey => $mod)
                            @php
                                $modPermNames = array_column($mod['permissions'], 'name');
                                $grantedCount = $isSuperAdmin ? count($modPermNames) : $role->permissions->whereIn('name', $modPermNames)->count();
                                $hasAll = $grantedCount === count($modPermNames);
                                $hasSome = $grantedCount > 0 && !$hasAll;
                            @endphp
                            <span class="module-mini-tag" title="{{ $mod['name'] }}: {{ $grantedCount }}/{{ count($modPermNames) }} permissions active" style="{{ $hasAll ? 'background:rgba(16,185,129,0.12);color:#10b981;border-color:rgba(16,185,129,0.25);' : ($hasSome ? 'background:rgba(99,102,241,0.12);color:var(--accent);border-color:rgba(99,102,241,0.25);' : '') }}">
                                <i data-lucide="{{ $mod['icon'] }}" style="width:11px;height:11px;"></i>
                                {{ $mod['name'] }}
                                <strong style="font-size:0.62rem;">{{ $grantedCount }}/{{ count($modPermNames) }}</strong>
                            </span>
                        @endforeach
                    </div>
                </div>

                {{-- Card Footer with Member stack and action button --}}
                <div class="pt-3 border-top d-flex align-items-center justify-content-between mt-auto" style="border-color:var(--border) !important;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar-stack">
                            @forelse($role->users->take(4) as $u)
                                <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}" class="avatar-stack-item" title="{{ $u->name }} ({{ $u->email }})">
                            @empty
                                <span style="font-size:0.72rem;color:var(--text-muted);">No members assigned</span>
                            @endforelse
                            @if($role->users_count > 4)
                                <div class="avatar-stack-more">+{{ $role->users_count - 4 }}</div>
                            @endif
                        </div>
                        @if($role->users_count > 0)
                            <span style="font-size:0.72rem;color:var(--text-muted);font-weight:600;">
                                {{ $role->users_count }} {{ Str::plural('member', $role->users_count) }}
                            </span>
                        @endif
                    </div>

                    <div class="d-flex align-items-center gap-1">
                        @if(!$isSuperAdmin && auth()->user()->can('edit roles'))
                            <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-primary" style="font-size:0.75rem;padding:0.3rem 0.75rem;border-radius:6px;border-color:var(--accent);color:var(--accent);">
                                Edit Matrix
                            </a>
                        @else
                            <a href="{{ route('roles.show', $role) }}" class="btn btn-sm btn-outline-secondary" style="font-size:0.75rem;padding:0.3rem 0.75rem;border-radius:6px;">
                                View Details
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card-glass text-center py-5">
                <i data-lucide="shield-alert" style="width:40px;height:40px;color:var(--text-muted);margin-bottom:0.75rem;"></i>
                <h5 class="text-muted">No roles found</h5>
                <p style="font-size:0.85rem;color:var(--text-muted);">Create your first custom role to get started.</p>
            </div>
        </div>
    @endforelse
</div>

{{-- 2. MATRIX TABLE VIEW (HIDDEN BY DEFAULT, TOGGLED VIA BUTTON) --}}
<div id="rolesMatrixView" class="matrix-table-wrap d-none">
    <div style="overflow-x: auto;">
        <table class="matrix-table">
            <thead>
                <tr>
                    <th style="min-width: 240px; position: sticky; left: 0; z-index: 2; background: var(--bg-hover);">
                        Module & Permission
                    </th>
                    <th style="width: 100px;">Action</th>
                    @foreach($roles as $role)
                        <th class="text-center" style="min-width: 130px;">
                            <div style="font-size:0.82rem;font-weight:700;color:var(--text-primary);">
                                {{ ucwords(str_replace('-', ' ', $role->name)) }}
                            </div>
                            <div style="font-size:0.65rem;color:var(--text-muted);font-weight:normal;">
                                {{ $role->users_count }} members
                            </div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($modules as $modKey => $module)
                    <tr class="matrix-module-header">
                        <td colspan="{{ count($roles) + 2 }}" style="padding:0.6rem 1rem;">
                            <div class="d-flex align-items-center gap-2">
                                <i data-lucide="{{ $module['icon'] }}" style="width:14px;height:14px;"></i>
                                <span>{{ $module['name'] }}</span>
                                <span class="badge" style="background:rgba(99,102,241,0.15);color:var(--accent);font-size:0.65rem;font-weight:600;">
                                    {{ count($module['permissions']) }} Permissions
                                </span>
                            </div>
                        </td>
                    </tr>
                    @foreach($module['permissions'] as $perm)
                        @php
                            $badge = \App\Services\ModulePermissionService::getActionBadge($perm['action']);
                        @endphp
                        <tr>
                            <td style="position: sticky; left: 0; background: var(--bg-card); z-index: 1;">
                                <div style="font-weight:600;color:var(--text-primary);font-size:0.82rem;">
                                    {{ $perm['label'] }}
                                </div>
                                <div style="font-size:0.70rem;color:var(--text-muted);">
                                    {{ $perm['description'] }}
                                </div>
                            </td>
                            <td>
                                <span class="badge" style="background:{{ $badge['bg'] }};color:{{ $badge['color'] }};font-size:0.62rem;padding:2px 6px;">
                                    {{ $badge['label'] }}
                                </span>
                            </td>
                            @foreach($roles as $role)
                                @php
                                    $isSuperAdminRole = ((int) $role->id === 1 || $role->name === 'super-admin');
                                    $hasPerm = $isSuperAdminRole 
                                        || $role->permissions->contains('name', $perm['name']) 
                                        || (isset($perm['id']) && $role->permissions->contains('id', $perm['id']));
                                @endphp
                                <td class="text-center">
                                    @if($hasPerm)
                                        <div style="width:24px;height:24px;border-radius:50%;background:rgba(16,185,129,0.15);color:#10b981;display:inline-flex;align-items:center;justify-content:center;">
                                            <i data-lucide="check" style="width:14px;height:14px;stroke-width:2.5;"></i>
                                        </div>
                                    @else
                                        <div style="width:24px;height:24px;border-radius:50%;background:rgba(148,163,184,0.1);color:var(--text-muted);display:inline-flex;align-items:center;justify-content:center;opacity:0.4;">
                                            <i data-lucide="minus" style="width:12px;height:12px;"></i>
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    function switchRoleView(viewMode) {
        const cardsView = document.getElementById('rolesCardsView');
        const matrixView = document.getElementById('rolesMatrixView');
        const btnCards = document.getElementById('btnViewCards');
        const btnMatrix = document.getElementById('btnViewMatrix');

        if (viewMode === 'cards') {
            cardsView.classList.remove('d-none');
            matrixView.classList.add('d-none');
            btnCards.classList.add('active');
            btnMatrix.classList.remove('active');
        } else {
            cardsView.classList.add('d-none');
            matrixView.classList.remove('d-none');
            btnMatrix.classList.add('active');
            btnCards.classList.remove('active');
        }
        if (window.lucide) lucide.createIcons();
    }

    // Role search & category filtering
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('roleSearchInput');
        const filterBtns = document.querySelectorAll('.role-filter-btn');
        const roleItems = document.querySelectorAll('.role-grid-item');

        let currentFilter = 'all';

        function filterRoles() {
            const query = (searchInput.value || '').toLowerCase().trim();

            roleItems.forEach(item => {
                const name = item.getAttribute('data-name') || '';
                const type = item.getAttribute('data-type') || '';

                const matchesQuery = !query || name.includes(query);
                const matchesType = currentFilter === 'all' || type === currentFilter;

                if (matchesQuery && matchesType) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', filterRoles);
        }

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentFilter = this.getAttribute('data-filter');
                filterRoles();
            });
        });
    });
</script>
@endsection
