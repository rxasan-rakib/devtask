@extends('layouts.app')
@section('title', 'Edit Role: ' . ucwords(str_replace('-', ' ', $role->name)))
@section('page-title', 'Edit Role Permissions')

@section('content')
<style>
    /* ── Ultra Modern Role Builder Styles ── */
    .role-builder-header {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.12) 0%, rgba(255, 120, 40, 0.08) 100%);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
        backdrop-filter: blur(10px);
    }

    .preset-pill-btn {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 0.4rem 0.9rem;
        font-size: 0.76rem;
        font-weight: 600;
        color: var(--text-primary);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .preset-pill-btn:hover {
        border-color: var(--accent);
        background: var(--bg-hover);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
    }

    .perm-card-item {
        background: var(--bg-card);
        border: 1.5px solid var(--border);
        border-radius: 12px;
        padding: 0.85rem;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        user-select: none;
        position: relative;
        height: 100%;
    }
    .perm-card-item:hover {
        border-color: rgba(99, 102, 241, 0.4);
        background: var(--bg-hover);
        transform: translateY(-2px);
    }
    .perm-card-item.selected {
        background: rgba(99, 102, 241, 0.08);
        border-color: var(--accent);
        box-shadow: 0 4px 14px rgba(99, 102, 241, 0.18);
    }

    .perm-checkbox-custom {
        width: 18px;
        height: 18px;
        border-radius: 6px;
        accent-color: var(--accent);
        cursor: pointer;
        margin-top: 2px;
        flex-shrink: 0;
    }

    /* iOS-like Module Switch */
    .ios-switch {
        position: relative;
        display: inline-block;
        width: 36px;
        height: 20px;
        margin-bottom: 0;
    }
    .ios-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .ios-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: rgba(148, 163, 184, 0.3);
        transition: .3s cubic-bezier(0.16, 1, 0.3, 1);
        border-radius: 20px;
    }
    .ios-slider:before {
        position: absolute;
        content: "";
        height: 14px;
        width: 14px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s cubic-bezier(0.16, 1, 0.3, 1);
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .ios-switch input:checked + .ios-slider {
        background-color: var(--accent);
    }
    .ios-switch input:checked + .ios-slider:before {
        transform: translateX(16px);
    }

    /* Sticky HUD Card */
    .sticky-hud-card {
        position: sticky;
        top: 85px;
        z-index: 10;
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.25rem;
        box-shadow: var(--shadow-card);
        backdrop-filter: blur(12px);
    }
</style>

@php
    $isSuperAdmin = ((int) $role->id === 1 || $role->name === 'super-admin');
@endphp

<div class="role-builder-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <a href="{{ route('roles.index') }}" class="text-muted" style="text-decoration:none;font-size:0.75rem;font-weight:600;">
                <i data-lucide="arrow-left" style="width:12px;height:12px;display:inline;"></i> ROLES & ACCESS
            </a>
            <span class="text-muted" style="font-size:0.75rem;">/</span>
            <span class="badge" style="background:rgba(99,102,241,0.15);color:var(--accent);font-size:0.72rem;font-weight:700;">EDITING ROLE</span>
        </div>
        <h2 class="mb-0" style="font-size:1.35rem;font-weight:800;color:var(--text-primary);letter-spacing:-0.02em;">
            Edit Role: {{ ucwords(str_replace('-', ' ', $role->name)) }}
        </h2>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('roles.show', $role) }}" class="btn-ghost" style="font-size:0.82rem;padding:0.45rem 0.9rem;">
            <i data-lucide="eye" style="width:14px;height:14px;"></i> View Details
        </a>
        <a href="{{ route('roles.index') }}" class="btn-ghost" style="font-size:0.82rem;padding:0.45rem 0.9rem;">
            Cancel
        </a>
    </div>
</div>

@if($isSuperAdmin)
    <div class="alert alert-warning d-flex align-items-center gap-3 mb-4" style="background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.25);color:#f59e0b;border-radius:12px;padding:1rem;">
        <i data-lucide="shield-alert" style="width:24px;height:24px;flex-shrink:0;"></i>
        <div style="font-size:0.88rem;">
            <strong>Protected Super Admin Role:</strong> This built-in system role unconditionally retains full administrative access across all present and future modules.
        </div>
    </div>
@endif

<form method="POST" action="{{ route('roles.update', $role) }}" id="roleForm">
    @csrf
    @method('PUT')

    <div class="row g-4">
        {{-- Main Left Area --}}
        <div class="col-lg-8 col-xl-9">
            {{-- 1. Role Basic Details --}}
            <div class="card-glass mb-4" style="border-radius:16px;padding:1.35rem;">
                <div class="section-title mb-3" style="font-size:0.95rem;font-weight:700;">
                    <i data-lucide="shield" style="width:16px;height:16px;color:var(--accent);"></i>
                    Role Identity & Nomenclature
                </div>
                <div class="row g-3">
                    <div class="col-md-7">
                        <label class="form-label-enhanced" style="font-size:0.82rem;font-weight:600;">
                            Role Name / Title <span class="req" style="color:#ef4444;">*</span>
                        </label>
                        <input type="text" name="name" id="roleNameInput" class="form-control form-control-dark @error('name') is-invalid @enderror"
                            placeholder="e.g. Senior Project Manager" value="{{ old('name', ucwords(str_replace('-', ' ', $role->name))) }}"
                            {{ $isSuperAdmin ? 'readonly' : '' }} required autofocus
                            style="font-size:0.92rem;height:42px;border-radius:10px;">
                        <div class="form-text mt-2 d-flex align-items-center gap-2" style="font-size:0.75rem;color:var(--text-muted);">
                            <span>System Identifier Slug:</span>
                            <span id="slugPreview" class="badge" style="background:var(--bg-hover);color:var(--accent);border:1px solid var(--border);font-family:monospace;font-size:0.72rem;">{{ $role->name }}</span>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="p-3 rounded-3 h-100 d-flex align-items-center" style="background:rgba(99,102,241,0.05);border:1px dashed rgba(99,102,241,0.25);">
                            <div style="font-size:0.78rem;color:var(--text-muted);line-height:1.4;">
                                <strong style="color:var(--text-primary);display:block;margin-bottom:2px;">👥 {{ $role->users()->count() }} Members Assigned:</strong>
                                Changes saved here will immediately update permissions for all {{ $role->users()->count() }} users assigned to this role.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(!$isSuperAdmin)
            {{-- 2. Quick Presets Bar --}}
            <div class="card-glass p-3 mb-4 d-flex align-items-center gap-2 flex-wrap" style="border-radius:14px;">
                <div class="d-flex align-items-center gap-1 text-muted me-2" style="font-size:0.75rem;font-weight:700;">
                    <i data-lucide="sparkles" style="width:14px;height:14px;color:#ff7828;"></i> PRESETS:
                </div>
                <button type="button" class="preset-pill-btn" onclick="applyPreset('admin')">
                    👑 Full Admin
                </button>
                <button type="button" class="preset-pill-btn" onclick="applyPreset('manager')">
                    💼 Project Manager
                </button>
                <button type="button" class="preset-pill-btn" onclick="applyPreset('support')">
                    🎧 Support Agent
                </button>
                <button type="button" class="preset-pill-btn" onclick="applyPreset('developer')">
                    🛠️ Developer
                </button>
                <button type="button" class="preset-pill-btn" onclick="applyPreset('viewer')">
                    👁️ Read-Only Viewer
                </button>
            </div>
            @endif

            {{-- 3. Toolbar & Permission Search --}}
            <div class="card-glass p-3 mb-3 d-flex flex-wrap align-items-center justify-content-between gap-3" style="border-radius:14px;">
                <div class="d-flex align-items-center gap-2 flex-wrap" style="flex:1;max-width:400px;">
                    <div class="position-relative w-100">
                        <i data-lucide="search" style="position: absolute; left: 11px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: var(--text-muted);"></i>
                        <input type="text" id="permissionFilterInput" class="form-control form-control-dark" placeholder="Search permissions..." style="padding-left: 32px; height: 36px; font-size: 0.8rem; border-radius: 8px;">
                    </div>
                </div>

                @if(!$isSuperAdmin)
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllBtn" style="font-size:0.75rem;border-radius:8px;padding:0.35rem 0.75rem;">
                        <i data-lucide="check-square" style="width:13px;height:13px;display:inline;"></i> Select All
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllBtn" style="font-size:0.75rem;border-radius:8px;padding:0.35rem 0.75rem;">
                        <i data-lucide="square" style="width:13px;height:13px;display:inline;"></i> Clear All
                    </button>
                </div>
                @endif
            </div>

            {{-- 4. Module Permission Blocks --}}
            <div class="row g-3" id="modulesContainer">
                @foreach($modules as $moduleKey => $module)
                    <div class="col-12 module-block" data-module="{{ $moduleKey }}">
                        <div class="card-glass" style="border-radius:14px;padding:1.25rem;">
                            {{-- Module Header --}}
                            <div class="d-flex align-items-center justify-content-between pb-3 border-bottom mb-3" style="border-color:var(--border) !important;">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width:36px;height:36px;border-radius:10px;background:rgba(99,102,241,0.12);color:var(--accent);display:flex;align-items:center;justify-content:center;box-shadow:0 2px 6px rgba(0,0,0,0.1);">
                                        <i data-lucide="{{ $module['icon'] }}" style="width:18px;height:18px;"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2">
                                            <h5 class="mb-0" style="font-size:0.95rem;font-weight:700;color:var(--text-primary);">
                                                {{ $module['name'] }}
                                            </h5>
                                            <span class="badge module-count-badge" id="badge-{{ $moduleKey }}" style="background:var(--bg-hover);color:var(--text-muted);border:1px solid var(--border);font-size:0.65rem;">
                                                0/{{ count($module['permissions']) }} active
                                            </span>
                                        </div>
                                        <div style="font-size:0.72rem;color:var(--text-muted);margin-top:2px;">
                                            {{ $module['description'] }}
                                        </div>
                                    </div>
                                </div>

                                @if(!$isSuperAdmin)
                                <div class="d-flex align-items-center gap-2">
                                    <span style="font-size:0.72rem;color:var(--text-muted);font-weight:600;">Enable All</span>
                                    <label class="ios-switch" title="Toggle all permissions in {{ $module['name'] }}">
                                        <input type="checkbox" class="module-master-toggle" data-module="{{ $moduleKey }}">
                                        <span class="ios-slider"></span>
                                    </label>
                                </div>
                                @endif
                            </div>

                            {{-- Permission Items Grid --}}
                            <div class="row g-2">
                                @foreach($module['permissions'] as $perm)
                                    @php
                                        $badge = \App\Services\ModulePermissionService::getActionBadge($perm['action']);
                                        $isChecked = $isSuperAdmin || in_array($perm['name'], old('permissions', $rolePermissions)) || (isset($perm['id']) && in_array($perm['id'], old('permissions', $rolePermissionIds ?? [])));
                                    @endphp
                                    <div class="col-md-6 col-xl-4 perm-col" data-name="{{ strtolower($perm['label'] . ' ' . $perm['description'] . ' ' . $perm['name']) }}" data-action="{{ $perm['action'] }}">
                                        <label class="perm-card-item {{ $isChecked ? 'selected' : '' }} {{ $isSuperAdmin ? 'opacity-75' : '' }}">
                                            <input type="checkbox" name="permissions[]" value="{{ $perm['name'] }}"
                                                class="perm-checkbox-custom perm-checkbox module-perm-{{ $moduleKey }}"
                                                {{ $isChecked ? 'checked' : '' }}
                                                {{ $isSuperAdmin ? 'disabled' : '' }}>
                                            <div style="flex:1;min-width:0;">
                                                <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                                                    <span style="font-size:0.82rem;font-weight:700;color:var(--text-primary);line-height:1.2;">
                                                        {{ $perm['label'] }}
                                                    </span>
                                                    <span class="badge" style="background:{{ $badge['bg'] }};color:{{ $badge['color'] }};font-size:0.60rem;padding:2px 5px;border-radius:4px;">
                                                        {{ $badge['label'] }}
                                                    </span>
                                                </div>
                                                <div style="font-size:0.70rem;color:var(--text-muted);line-height:1.3;">
                                                    {{ $perm['description'] }}
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Right Sticky Summary HUD Panel --}}
        <div class="col-lg-4 col-xl-3">
            <div class="sticky-hud-card">
                <div class="d-flex align-items-center justify-content-between pb-3 border-bottom mb-3" style="border-color:var(--border) !important;">
                    <div style="font-size:0.88rem;font-weight:700;color:var(--text-primary);">
                        <i data-lucide="sliders" style="width:14px;height:14px;color:var(--accent);display:inline;margin-right:4px;"></i>
                        Role Summary HUD
                    </div>
                    <span class="badge" id="hudCoverageBadge" style="background:rgba(16,185,129,0.15);color:#10b981;font-weight:700;font-size:0.72rem;">
                        0% Coverage
                    </span>
                </div>

                {{-- Live Counter --}}
                <div class="text-center py-2 mb-3">
                    <div style="font-size:2rem;font-weight:800;color:var(--accent);line-height:1;" id="hudSelectedCount">
                        0
                    </div>
                    <div style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;font-weight:600;margin-top:4px;">
                        of {{ count(\App\Services\ModulePermissionService::getAllPermissionNames()) }} Permissions Active
                    </div>
                    <div class="progress-track-premium mt-2" style="height:6px;">
                        <div class="progress-fill-premium" id="hudProgressBar" style="width:0%;background:linear-gradient(90deg, var(--accent), #10b981);"></div>
                    </div>
                </div>

                {{-- Modules Breakdown Mini-List --}}
                <div class="mb-4">
                    <div class="text-muted mb-2" style="font-size:0.70rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;">
                        Module Matrix Breakdown:
                    </div>
                    <div class="d-flex flex-column gap-2" style="font-size:0.75rem;">
                        @foreach($modules as $k => $m)
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-muted d-flex align-items-center gap-1">
                                    <i data-lucide="{{ $m['icon'] }}" style="width:12px;height:12px;"></i>
                                    {{ $m['name'] }}
                                </span>
                                <span id="hud-mod-{{ $k }}" class="badge" style="background:var(--bg-hover);color:var(--text-muted);font-size:0.65rem;">
                                    0/{{ count($m['permissions']) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-grid gap-2">
                    @if(!$isSuperAdmin)
                        <button type="submit" class="btn-gradient w-100 d-flex align-items-center justify-content-center gap-2 py-2" style="border-radius:10px;font-weight:700;box-shadow:0 4px 14px rgba(99,102,241,0.35);">
                            <i data-lucide="save" style="width:16px;height:16px;"></i> Save Role Changes
                        </button>
                    @endif
                    <a href="{{ route('roles.show', $role) }}" class="btn-ghost text-center py-2" style="border-radius:10px;font-size:0.82rem;">
                        View Role Details
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleNameInput = document.getElementById('roleNameInput');
        const slugPreview = document.getElementById('slugPreview');
        const allCheckboxes = document.querySelectorAll('.perm-checkbox');
        const hudSelectedCount = document.getElementById('hudSelectedCount');
        const hudCoverageBadge = document.getElementById('hudCoverageBadge');
        const hudProgressBar = document.getElementById('hudProgressBar');
        const selectAllBtn = document.getElementById('selectAllBtn');
        const deselectAllBtn = document.getElementById('deselectAllBtn');
        const filterInput = document.getElementById('permissionFilterInput');
        const moduleBlocks = document.querySelectorAll('.module-block');
        const masterToggles = document.querySelectorAll('.module-master-toggle');

        const totalPermissions = {{ count(\App\Services\ModulePermissionService::getAllPermissionNames()) }};
        const isSuperAdmin = {{ $isSuperAdmin ? 'true' : 'false' }};

        function updateSlug() {
            if (isSuperAdmin) return;
            const val = roleNameInput.value.trim();
            if (!val) {
                slugPreview.textContent = '—';
                return;
            }
            const slug = val.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
            slugPreview.textContent = slug || '—';
        }
        if (roleNameInput && !isSuperAdmin) {
            roleNameInput.addEventListener('input', updateSlug);
        }

        function updateStats() {
            let checkedTotal = 0;

            moduleBlocks.forEach(block => {
                const modKey = block.getAttribute('data-module');
                const blockCheckboxes = block.querySelectorAll('.perm-checkbox');
                const blockChecked = isSuperAdmin ? blockCheckboxes.length : block.querySelectorAll('.perm-checkbox:checked').length;
                const totalInBlock = blockCheckboxes.length;

                checkedTotal += blockChecked;

                const badge = document.getElementById('badge-' + modKey);
                if (badge) {
                    badge.textContent = `${blockChecked}/${totalInBlock} active`;
                    if (blockChecked === totalInBlock && totalInBlock > 0) {
                        badge.style.background = 'rgba(16,185,129,0.15)';
                        badge.style.color = '#10b981';
                        badge.style.borderColor = 'rgba(16,185,129,0.3)';
                    } else if (blockChecked > 0) {
                        badge.style.background = 'rgba(99,102,241,0.15)';
                        badge.style.color = 'var(--accent)';
                        badge.style.borderColor = 'rgba(99,102,241,0.3)';
                    } else {
                        badge.style.background = 'var(--bg-hover)';
                        badge.style.color = 'var(--text-muted)';
                        badge.style.borderColor = 'var(--border)';
                    }
                }

                const hudModBadge = document.getElementById('hud-mod-' + modKey);
                if (hudModBadge) {
                    hudModBadge.textContent = `${blockChecked}/${totalInBlock}`;
                    hudModBadge.style.color = blockChecked === totalInBlock ? '#10b981' : (blockChecked > 0 ? 'var(--accent)' : 'var(--text-muted)');
                }

                const masterToggle = block.querySelector('.module-master-toggle');
                if (masterToggle) {
                    masterToggle.checked = blockChecked === totalInBlock && totalInBlock > 0;
                }
            });

            allCheckboxes.forEach(cb => {
                const parent = cb.closest('.perm-card-item');
                if (parent) {
                    if (cb.checked || isSuperAdmin) {
                        parent.classList.add('selected');
                    } else {
                        parent.classList.remove('selected');
                    }
                }
            });

            if (hudSelectedCount) hudSelectedCount.textContent = checkedTotal;
            const pct = totalPermissions > 0 ? Math.round((checkedTotal / totalPermissions) * 100) : 0;
            if (hudProgressBar) hudProgressBar.style.width = pct + '%';
            if (hudCoverageBadge) {
                hudCoverageBadge.textContent = pct + '% Coverage';
                hudCoverageBadge.style.color = pct === 100 ? '#10b981' : (pct > 0 ? 'var(--accent)' : 'var(--text-muted)');
            }
        }

        allCheckboxes.forEach(cb => {
            cb.addEventListener('change', updateStats);
        });

        masterToggles.forEach(toggle => {
            toggle.addEventListener('change', function() {
                const modKey = this.getAttribute('data-module');
                const cbs = document.querySelectorAll('.module-perm-' + modKey);
                cbs.forEach(cb => cb.checked = this.checked);
                updateStats();
            });
        });

        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', () => {
                allCheckboxes.forEach(cb => cb.checked = true);
                updateStats();
            });
        }
        if (deselectAllBtn) {
            deselectAllBtn.addEventListener('click', () => {
                allCheckboxes.forEach(cb => cb.checked = false);
                updateStats();
            });
        }

        if (filterInput) {
            filterInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();

                moduleBlocks.forEach(block => {
                    let visibleCount = 0;
                    const items = block.querySelectorAll('.perm-col');
                    items.forEach(col => {
                        const name = col.getAttribute('data-name') || '';
                        if (!query || name.includes(query)) {
                            col.style.display = '';
                            visibleCount++;
                        } else {
                            col.style.display = 'none';
                        }
                    });

                    block.style.display = (visibleCount > 0) ? '' : 'none';
                });
            });
        }

        updateStats();

        window.applyPreset = function(presetType) {
            if (isSuperAdmin) return;
            allCheckboxes.forEach(cb => cb.checked = false);

            if (presetType === 'admin') {
                allCheckboxes.forEach(cb => cb.checked = true);
            } else if (presetType === 'manager') {
                const managerPerms = [
                    'view dashboard', 'view all tasks stats',
                    'view tasks', 'view all tasks', 'create tasks', 'edit tasks', 'assign tasks', 'change task status', 'add task comments', 'delete task comments',
                    'view clients', 'create clients', 'edit clients',
                    'view reminders', 'create reminders', 'edit reminders', 'mark reminders sent',
                    'view reports', 'export data'
                ];
                allCheckboxes.forEach(cb => {
                    if (managerPerms.includes(cb.value)) cb.checked = true;
                });
            } else if (presetType === 'developer') {
                const devPerms = [
                    'view dashboard',
                    'view tasks', 'create tasks', 'edit tasks', 'change task status', 'add task comments',
                    'view clients',
                    'view reminders', 'create reminders'
                ];
                allCheckboxes.forEach(cb => {
                    if (devPerms.includes(cb.value)) cb.checked = true;
                });
            } else if (presetType === 'support') {
                const supportPerms = [
                    'view dashboard',
                    'view tasks', 'change task status', 'add task comments',
                    'view clients', 'create clients', 'edit clients',
                    'view reminders', 'create reminders', 'mark reminders sent'
                ];
                allCheckboxes.forEach(cb => {
                    if (supportPerms.includes(cb.value)) cb.checked = true;
                });
            } else if (presetType === 'viewer') {
                const viewerPerms = [
                    'view dashboard', 'view tasks', 'view clients', 'view reminders', 'view reports'
                ];
                allCheckboxes.forEach(cb => {
                    if (viewerPerms.includes(cb.value)) cb.checked = true;
                });
            }

            updateStats();
        };
    });
</script>
@endsection
