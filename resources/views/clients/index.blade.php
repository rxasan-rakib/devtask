@extends('layouts.app')
@section('title', 'Clients')
@section('page-title', 'Clients')

@section('content')
{{-- Page Header --}}
<div class="page-header">
    <div class="page-header-left">
        <h2>Clients</h2>
        <p>Manage your client businesses, system portals, and project profiles</p>
    </div>
    <div class="page-header-actions">
        @can('create clients')
        <a href="{{ route('clients.create') }}" class="btn-gradient">
            <i data-lucide="user-plus" style="width:15px;height:15px;"></i> Add Client
        </a>
        @endcan
    </div>
</div>

{{-- Quick Status Tabs --}}
<div class="d-flex flex-wrap align-items-center gap-2 mb-3">
    <a href="{{ route('clients.index', request()->except(['type', 'status', 'page'])) }}"
       class="btn-ghost {{ !request('type') && !request('status') ? 'border-primary text-primary fw-bold' : '' }}"
       style="padding: .4rem .9rem; font-size: .82rem; border-radius: 20px; {{ !request('type') && !request('status') ? 'background: rgba(99,102,241,0.1);' : '' }}">
        All Clients <span class="badge ms-1" style="background: rgba(148,163,184,0.2); color: inherit;">{{ $totalCount ?? $clients->total() }}</span>
    </a>
    <a href="{{ route('clients.index', array_merge(request()->except('page'), ['type' => 'pos'])) }}"
       class="btn-ghost {{ request('type') === 'pos' ? 'border-primary text-primary fw-bold' : '' }}"
       style="padding: .4rem .9rem; font-size: .82rem; border-radius: 20px; {{ request('type') === 'pos' ? 'background: rgba(99,102,241,0.12);' : '' }}">
        🖥️ POS Systems <span class="badge ms-1" style="background: rgba(99,102,241,0.2); color: var(--accent);">{{ $posCount ?? 0 }}</span>
    </a>
    <a href="{{ route('clients.index', array_merge(request()->except('page'), ['type' => 'website'])) }}"
       class="btn-ghost {{ request('type') === 'website' ? 'border-info text-info fw-bold' : '' }}"
       style="padding: .4rem .9rem; font-size: .82rem; border-radius: 20px; {{ request('type') === 'website' ? 'background: rgba(14,165,233,0.12);' : '' }}">
        🌐 Websites <span class="badge ms-1" style="background: rgba(14,165,233,0.2); color: #0ea5e9;">{{ $websiteCount ?? 0 }}</span>
    </a>
    <a href="{{ route('clients.index', array_merge(request()->except('page'), ['type' => 'both'])) }}"
       class="btn-ghost {{ request('type') === 'both' ? 'border-warning text-warning fw-bold' : '' }}"
       style="padding: .4rem .9rem; font-size: .82rem; border-radius: 20px; {{ request('type') === 'both' ? 'background: rgba(245,158,11,0.12);' : '' }}">
        ⚡ POS + Web <span class="badge ms-1" style="background: rgba(245,158,11,0.2); color: #f59e0b;">{{ $bothCount ?? 0 }}</span>
    </a>
</div>

{{-- Filter Card --}}
<div class="card-glass mb-4">
    <form method="GET" action="{{ route('clients.index') }}">
        <div class="row g-2 align-items-end">
            {{-- Search --}}
            <div class="col-12 col-sm-6 col-lg-4">
                <label class="form-label-enhanced">Search</label>
                <div style="position:relative;">
                    <i data-lucide="search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:var(--text-muted);pointer-events:none;"></i>
                    <input type="text" name="search" class="form-control form-control-dark" style="padding-left:32px; height:38px; font-size:.85rem;" placeholder="Search company, URL, name, phone..." value="{{ request('search') }}">
                </div>
            </div>

            {{-- Type --}}
            <div class="col-6 col-sm-6 col-lg-3">
                <label class="form-label-enhanced">System Type</label>
                <select name="type" class="form-select form-select-dark" style="height:38px; font-size:.85rem;">
                    <option value="">All Types</option>
                    <option value="pos" {{ request('type')=='pos'?'selected':'' }}>POS System</option>
                    <option value="website" {{ request('type')=='website'?'selected':'' }}>Website</option>
                    <option value="both" {{ request('type')=='both'?'selected':'' }}>POS + Website</option>
                    <option value="other" {{ request('type')=='other'?'selected':'' }}>Other</option>
                </select>
            </div>

            {{-- Status --}}
            <div class="col-6 col-sm-6 col-lg-2">
                <label class="form-label-enhanced">Status</label>
                <select name="status" class="form-select form-select-dark" style="height:38px; font-size:.85rem;">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
                    <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>Inactive</option>
                </select>
            </div>

            {{-- Actions --}}
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label-enhanced d-none d-lg-block">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn-gradient flex-fill justify-content-center" style="height:38px; padding:.45rem .9rem; font-size:.85rem;">
                        <i data-lucide="filter" style="width:14px;height:14px;"></i> Filter
                    </button>
                    @if(request()->hasAny(['search', 'type', 'status']))
                        <a href="{{ route('clients.index') }}" class="btn-ghost" style="height:38px; padding:.45rem .85rem;" title="Reset Filters">
                            <i data-lucide="rotate-ccw" style="width:14px;height:14px;"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </form>

    {{-- Active Filter Summary --}}
    @if(request()->hasAny(['search', 'type', 'status']))
        <div class="d-flex flex-wrap align-items-center gap-2 pt-3 mt-3 border-top" style="border-color: var(--border) !important;">
            <span style="font-size: .75rem; color: var(--text-muted); font-weight: 600;">Active Filters:</span>

            @if(request('search'))
                <a href="{{ route('clients.index', request()->except('search')) }}" class="badge" style="background: var(--bg-hover); color: var(--text-primary); border: 1px solid var(--border); text-decoration: none; padding: 4px 8px; font-size: .72rem;">
                    Search: "{{ request('search') }}" <i data-lucide="x" style="width:11px;height:11px;display:inline;margin-left:3px;"></i>
                </a>
            @endif

            @if(request('type'))
                <a href="{{ route('clients.index', request()->except('type')) }}" class="badge" style="background: var(--bg-hover); color: var(--text-primary); border: 1px solid var(--border); text-decoration: none; padding: 4px 8px; font-size: .72rem;">
                    Type: {{ strtoupper(request('type')) }} <i data-lucide="x" style="width:11px;height:11px;display:inline;margin-left:3px;"></i>
                </a>
            @endif

            @if(request('status'))
                <a href="{{ route('clients.index', request()->except('status')) }}" class="badge" style="background: var(--bg-hover); color: var(--text-primary); border: 1px solid var(--border); text-decoration: none; padding: 4px 8px; font-size: .72rem;">
                    Status: {{ ucfirst(request('status')) }} <i data-lucide="x" style="width:11px;height:11px;display:inline;margin-left:3px;"></i>
                </a>
            @endif

            <a href="{{ route('clients.index') }}" style="font-size: .72rem; color: #ff7828; text-decoration: underline; margin-left: auto;">
                Reset All
            </a>
        </div>
    @endif
</div>

{{-- Results Header --}}
<div class="d-flex align-items-center justify-content-between mb-3">
    <div style="font-size: .82rem; color: var(--text-muted); font-weight: 500;">
        Showing <strong style="color: var(--text-primary);">{{ $clients->firstItem() ?? 0 }}–{{ $clients->lastItem() ?? 0 }}</strong> of <strong style="color: var(--text-primary);">{{ $clients->total() }}</strong> clients
    </div>
</div>

{{-- Clients Table Card --}}
<div class="card-glass p-0 overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-dark-custom align-middle mb-0">
            <thead>
                <tr>
                    <th style="padding-left: 1.25rem;">Client / Company</th>
                    <th>System URL</th>
                    <th>Contact Info</th>
                    <th>Type</th>
                    <th>Tasks</th>
                    <th>Status</th>
                    <th>Added On</th>
                    <th style="text-align: right; padding-right: 1.25rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                    @php
                        $typeBadges = [
                            'pos'     => ['bg' => 'rgba(99,102,241,.12)', 'color' => '#6366f1', 'icon' => 'monitor', 'label' => 'POS System'],
                            'website' => ['bg' => 'rgba(14,165,233,.12)', 'color' => '#0ea5e9', 'icon' => 'globe', 'label' => 'Website'],
                            'both'    => ['bg' => 'rgba(245,158,11,.12)', 'color' => '#f59e0b', 'icon' => 'layers', 'label' => 'POS + Web'],
                            'other'   => ['bg' => 'rgba(148,163,184,.12)', 'color' => '#94a3b8', 'icon' => 'box', 'label' => 'Other'],
                        ];
                        $typeInfo = $typeBadges[$client->type] ?? $typeBadges['other'];
                        $totalTasks = $client->tasks->count();
                        $completedTasks = $client->tasks->where('status', 'completed')->count();
                    @endphp
                    <tr>
                        {{-- Company Name & Initial --}}
                        <td style="padding-left: 1.25rem;">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width:38px; height:38px; border-radius:10px; background:linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); display:flex; align-items:center; justify-content:center; flex-shrink:0; box-shadow:0 3px 10px rgba(99,102,241,.25);">
                                    <span style="color:#ffffff; font-weight:800; font-size:.95rem;">
                                        {{ strtoupper(substr($client->company_name ?? $client->name ?? 'C', 0, 1)) }}
                                    </span>
                                </div>
                                <div style="min-width:0;">
                                    <a href="{{ route('clients.show', $client) }}" style="font-weight:700; font-size:.9rem; color:var(--text-primary); text-decoration:none; display:inline-block;" class="hover-accent">
                                        {{ $client->company_name ?? $client->name }}
                                    </a>
                                    @if($client->name && $client->name !== $client->company_name)
                                        <div style="font-size:.74rem; color:var(--text-muted); display:flex; align-items:center; gap:3px;">
                                            <i data-lucide="user" style="width:11px; height:11px;"></i> {{ $client->name }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- System URL --}}
                        <td>
                            @if($client->system_url)
                                <a href="{{ $client->formatted_system_url }}" target="_blank" rel="noopener noreferrer" class="badge" style="background:var(--bg-hover); color:var(--accent); border:1px solid var(--border); text-decoration:none; padding:5px 9px; font-size:.75rem; font-weight:600; display:inline-flex; align-items:center; gap:4px; max-width:200px;" title="{{ $client->system_url }}">
                                    <i data-lucide="external-link" style="width:12px; height:12px; flex-shrink:0;"></i>
                                    <span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $client->system_url }}</span>
                                </a>
                            @else
                                <span style="font-size:.78rem; color:var(--text-muted); opacity:.6;">—</span>
                            @endif
                        </td>

                        {{-- Contact Details --}}
                        <td>
                            <div class="d-flex flex-column gap-1">
                                @if($client->phone)
                                    <a href="tel:{{ $client->phone }}" style="font-size:.78rem; color:var(--text-primary); text-decoration:none; display:inline-flex; align-items:center; gap:5px;" class="hover-accent">
                                        <i data-lucide="phone" style="width:12px; height:12px; color:var(--accent); flex-shrink:0;"></i>
                                        <span>{{ $client->phone }}</span>
                                    </a>
                                @endif
                                @if($client->email)
                                    <a href="mailto:{{ $client->email }}" style="font-size:.76rem; color:var(--text-muted); text-decoration:none; display:inline-flex; align-items:center; gap:5px; max-width:180px;" class="hover-accent" title="{{ $client->email }}">
                                        <i data-lucide="mail" style="width:12px; height:12px; color:var(--accent); flex-shrink:0;"></i>
                                        <span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $client->email }}</span>
                                    </a>
                                @endif
                                @if(!$client->phone && !$client->email)
                                    <span style="font-size:.78rem; color:var(--text-muted); opacity:.6;">No contact info</span>
                                @endif
                            </div>
                        </td>

                        {{-- Type --}}
                        <td>
                            <span class="badge" style="background:{{ $typeInfo['bg'] }}; color:{{ $typeInfo['color'] }}; font-size:.72rem; font-weight:600; padding:4px 8px; border-radius:6px; display:inline-flex; align-items:center; gap:4px;">
                                <i data-lucide="{{ $typeInfo['icon'] }}" style="width:12px; height:12px;"></i>
                                {{ $typeInfo['label'] }}
                            </span>
                        </td>

                        {{-- Tasks Progress --}}
                        <td>
                            <div style="min-width: 90px;">
                                <div class="d-flex align-items-center justify-content-between mb-1" style="font-size:.75rem; color:var(--text-muted);">
                                    <span><strong>{{ $completedTasks }}</strong>/{{ $totalTasks }}</span>
                                    @if($totalTasks > 0)
                                        <span style="font-size:.7rem; font-weight:600; color:{{ $completedTasks === $totalTasks ? 'var(--success)' : 'var(--text-muted)' }};">
                                            {{ round(($completedTasks / $totalTasks) * 100) }}%
                                        </span>
                                    @endif
                                </div>
                                @if($totalTasks > 0)
                                    <div class="progress-slim">
                                        <div class="progress-slim-fill" style="width: {{ ($completedTasks / $totalTasks) * 100 }}%; background: {{ $completedTasks === $totalTasks ? 'var(--success)' : 'var(--accent)' }};"></div>
                                    </div>
                                @else
                                    <span style="font-size:.72rem; color:var(--text-muted); opacity:.6;">No tasks</span>
                                @endif
                            </div>
                        </td>

                        {{-- Status --}}
                        <td>
                            @if($client->is_active)
                                <span class="badge" style="background:rgba(16,185,129,.14); color:#10b981; font-size:.7rem; font-weight:600; padding:4px 8px; border-radius:6px; display:inline-flex; align-items:center; gap:4px;">
                                    <span style="width:6px; height:6px; background:#10b981; border-radius:50%;"></span> Active
                                </span>
                            @else
                                <span class="badge" style="background:rgba(148,163,184,.14); color:#94a3b8; font-size:.7rem; font-weight:600; padding:4px 8px; border-radius:6px;">
                                    Inactive
                                </span>
                            @endif
                        </td>

                        {{-- Added Date --}}
                        <td>
                            <div style="font-size:.78rem; color:var(--text-muted);">
                                {{ $client->created_at->format('d M Y') }}
                            </div>
                        </td>

                        {{-- Actions --}}
                        <td style="text-align: right; padding-right: 1.25rem;">
                            <div class="d-inline-flex gap-1 align-items-center">
                                <a href="{{ route('clients.show', $client) }}" class="btn-icon" style="width:32px; height:32px;" title="View Client Profile">
                                    <i data-lucide="eye" style="width:14px; height:14px;"></i>
                                </a>
                                @can('edit clients')
                                <a href="{{ route('clients.edit', $client) }}" class="btn-icon" style="width:32px; height:32px;" title="Edit Client">
                                    <i data-lucide="pencil" style="width:13px; height:13px;"></i>
                                </a>
                                @endcan
                                @can('delete clients')
                                <form method="POST" action="{{ route('clients.destroy', $client) }}" onsubmit="return confirm('Delete client {{ $client->company_name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-icon" style="width:32px; height:32px; color:#ef4444; border-color:rgba(239,68,68,.2);" title="Delete Client">
                                        <i data-lucide="trash-2" style="width:13px; height:13px;"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="empty-state">
                                <div style="width:52px; height:52px; background:rgba(99,102,241,.1); border-radius:50%; display:inline-flex; align-items:center; justify-content:center; margin-bottom:1rem;">
                                    <i data-lucide="users" style="width:24px; height:24px; color:var(--accent);"></i>
                                </div>
                                <h5 style="font-weight:700; color:var(--text-primary); margin-bottom:.35rem;">No Clients Found</h5>
                                <p style="font-size:.85rem; color:var(--text-muted); max-width:400px; margin:0 auto 1.25rem;">
                                    @if(request()->hasAny(['search', 'type', 'status']))
                                        No clients matched your current filter criteria. Try resetting filters.
                                    @else
                                        Start by adding your first client business to the system.
                                    @endif
                                </p>
                                <div class="d-flex justify-content-center gap-2">
                                    @if(request()->hasAny(['search', 'type', 'status']))
                                        <a href="{{ route('clients.index') }}" class="btn-ghost" style="padding:.5rem 1rem;">
                                            <i data-lucide="rotate-ccw" style="width:14px; height:14px;"></i> Clear Filters
                                        </a>
                                    @endif
                                    <a href="{{ route('clients.create') }}" class="btn-gradient" style="padding:.5rem 1.1rem;">
                                        <i data-lucide="user-plus" style="width:14px; height:14px;"></i> Add Client
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
@if($clients->hasPages())
    <div class="mt-4">
        {{ $clients->links() }}
    </div>
@endif

@endsection
