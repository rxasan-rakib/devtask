@extends('layouts.app')
@section('title', 'All Tasks')
@section('page-title', 'All Tasks')

@push('styles')
<style>
/* Status Tabs Bar */
.status-tabs-container {
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
    padding-bottom: 0.35rem;
    margin-bottom: 1.25rem;
    scrollbar-width: thin;
}
.status-tabs-container::-webkit-scrollbar {
    height: 4px;
}
.status-tabs-container::-webkit-scrollbar-thumb {
    background: var(--border);
    border-radius: 4px;
}
.status-tab-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.55rem 0.95rem;
    border-radius: 10px;
    background: var(--bg-card);
    border: 1px solid var(--border);
    color: var(--text-muted);
    font-size: 0.82rem;
    font-weight: 600;
    text-decoration: none;
    white-space: nowrap;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.status-tab-btn:hover {
    color: var(--text-primary);
    border-color: rgba(255, 120, 40, 0.4);
    background: var(--bg-hover);
    transform: translateY(-1px);
}
.status-tab-btn .tab-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 22px;
    height: 20px;
    padding: 0 6px;
    border-radius: 12px;
    font-size: 0.72rem;
    font-weight: 700;
    background: rgba(148, 163, 184, 0.15);
    color: var(--text-muted);
    transition: all 0.2s ease;
}

/* Specific Active Tab Styles */
.status-tab-btn.active.tab-pending {
    background: rgba(245, 158, 11, 0.12);
    border-color: #f59e0b;
    color: #f59e0b;
}
.status-tab-btn.active.tab-pending .tab-count {
    background: #f59e0b;
    color: #0f172a;
}

.status-tab-btn.active.tab-in_progress {
    background: rgba(59, 130, 246, 0.12);
    border-color: #3b82f6;
    color: #3b82f6;
}
.status-tab-btn.active.tab-in_progress .tab-count {
    background: #3b82f6;
    color: #ffffff;
}

.status-tab-btn.active.tab-on_hold {
    background: rgba(168, 85, 247, 0.12);
    border-color: #a855f7;
    color: #a855f7;
}
.status-tab-btn.active.tab-on_hold .tab-count {
    background: #a855f7;
    color: #ffffff;
}

.status-tab-btn.active.tab-under_review {
    background: rgba(6, 182, 212, 0.12);
    border-color: #06b6d4;
    color: #06b6d4;
}
.status-tab-btn.active.tab-under_review .tab-count {
    background: #06b6d4;
    color: #ffffff;
}

.status-tab-btn.active.tab-completed {
    background: rgba(16, 185, 129, 0.12);
    border-color: #10b981;
    color: #10b981;
}
.status-tab-btn.active.tab-completed .tab-count {
    background: #10b981;
    color: #ffffff;
}

.status-tab-btn.active.tab-all {
    background: rgba(255, 120, 40, 0.12);
    border-color: #ff7828;
    color: #ff7828;
}
.status-tab-btn.active.tab-all .tab-count {
    background: #ff7828;
    color: #ffffff;
}

/* Quick Date Preset Chips */
.date-preset-chip {
    display: inline-flex;
    align-items: center;
    padding: 2px 9px;
    border-radius: 6px;
    font-size: 0.72rem;
    font-weight: 600;
    text-decoration: none;
    background: var(--bg-hover);
    color: var(--text-muted);
    border: 1px solid var(--border);
    transition: all 0.15s ease;
}
.date-preset-chip:hover {
    color: var(--text-primary);
    border-color: rgba(255, 120, 40, 0.4);
}
.date-preset-chip.active {
    background: rgba(255, 120, 40, 0.15);
    border-color: #ff7828;
    color: #ff7828;
}

/* Status Indicator Dot */
.status-dot-indicator {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-right: 6px;
    vertical-align: middle;
}

/* Searchable Client Dropdown */
.searchable-select-wrap {
    position: relative;
}
.searchable-dropdown-list {
    display: none;
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    max-height: 240px;
    overflow-y: auto;
    z-index: 1060;
    background: var(--dropdown-bg);
    border: 1px solid var(--border);
    border-radius: 8px;
    box-shadow: var(--shadow-dropdown);
    scrollbar-width: thin;
}
.searchable-dropdown-list::-webkit-scrollbar {
    width: 5px;
}
.searchable-dropdown-list::-webkit-scrollbar-thumb {
    background: var(--border);
    border-radius: 4px;
}
.searchable-item {
    padding: 8px 12px;
    cursor: pointer;
    font-size: 0.83rem;
    color: var(--text-primary);
    border-bottom: 1px solid rgba(148, 163, 184, 0.08);
    transition: all 0.15s ease;
}
.searchable-item:last-child {
    border-bottom: none;
}
.searchable-item:hover,
.searchable-item.focused {
    background: var(--bg-hover) !important;
    color: var(--text-primary) !important;
}
.searchable-item.selected {
    background: rgba(99, 102, 241, 0.14) !important;
    color: var(--accent) !important;
    font-weight: 600;
}
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="page-header">
    <div class="page-header-left">
        <h2>All Tasks</h2>
        <p>Manage, track, and monitor all work items across your client systems</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('tasks.kanban') }}" class="btn-ghost">
            <i data-lucide="kanban" style="width:15px;height:15px;"></i> Kanban Board
        </a>
        @can('create tasks')
        <a href="{{ route('tasks.create') }}" class="btn-gradient">
            <i data-lucide="plus" style="width:15px;height:15px;"></i> New Task
        </a>
        @endcan
    </div>
</div>

{{-- Status Filter Buttons / Tabs (Default: Pending) --}}
<div class="status-tabs-container">
    {{-- Pending (Default Tab) --}}
    <a href="{{ route('tasks.index', array_merge(request()->except(['status', 'page']), ['status' => 'pending'])) }}" 
       class="status-tab-btn tab-pending {{ $selectedStatus === 'pending' ? 'active' : '' }}" 
       title="Filter by Pending Tasks">
        <i data-lucide="clock" style="width:14px;height:14px;"></i>
        <span>Pending</span>
        <span class="tab-count">{{ $statusCounts['pending'] ?? 0 }}</span>
    </a>

    {{-- In Progress --}}
    <a href="{{ route('tasks.index', array_merge(request()->except(['status', 'page']), ['status' => 'in_progress'])) }}" 
       class="status-tab-btn tab-in_progress {{ $selectedStatus === 'in_progress' ? 'active' : '' }}" 
       title="Filter by In Progress Tasks">
        <i data-lucide="play-circle" style="width:14px;height:14px;"></i>
        <span>In Progress</span>
        <span class="tab-count">{{ $statusCounts['in_progress'] ?? 0 }}</span>
    </a>

    {{-- On Hold --}}
    <a href="{{ route('tasks.index', array_merge(request()->except(['status', 'page']), ['status' => 'on_hold'])) }}" 
       class="status-tab-btn tab-on_hold {{ $selectedStatus === 'on_hold' ? 'active' : '' }}" 
       title="Filter by On Hold Tasks">
        <i data-lucide="pause-circle" style="width:14px;height:14px;"></i>
        <span>On Hold</span>
        <span class="tab-count">{{ $statusCounts['on_hold'] ?? 0 }}</span>
    </a>

    {{-- Under Review --}}
    <a href="{{ route('tasks.index', array_merge(request()->except(['status', 'page']), ['status' => 'under_review'])) }}" 
       class="status-tab-btn tab-under_review {{ $selectedStatus === 'under_review' ? 'active' : '' }}" 
       title="Filter by Under Review Tasks">
        <i data-lucide="search" style="width:14px;height:14px;"></i>
        <span>Under Review</span>
        <span class="tab-count">{{ $statusCounts['under_review'] ?? 0 }}</span>
    </a>

    {{-- Completed --}}
    <a href="{{ route('tasks.index', array_merge(request()->except(['status', 'page']), ['status' => 'completed'])) }}" 
       class="status-tab-btn tab-completed {{ $selectedStatus === 'completed' ? 'active' : '' }}" 
       title="Filter by Completed Tasks">
        <i data-lucide="check-circle-2" style="width:14px;height:14px;"></i>
        <span>Completed</span>
        <span class="tab-count">{{ $statusCounts['completed'] ?? 0 }}</span>
    </a>

    {{-- All Tasks --}}
    <a href="{{ route('tasks.index', array_merge(request()->except(['status', 'page']), ['status' => 'all'])) }}" 
       class="status-tab-btn tab-all {{ $selectedStatus === 'all' ? 'active' : '' }}" 
       title="Show All Status Tasks">
        <i data-lucide="layers" style="width:14px;height:14px;"></i>
        <span>All Tasks</span>
        <span class="tab-count">{{ $statusCounts['all'] ?? 0 }}</span>
    </a>
</div>

@php
    $activeClient = request('client_id') ? $clients->firstWhere('id', request('client_id')) : null;
@endphp

{{-- Filter Card with Search & Date Range --}}
<div class="card-glass mb-4">
    <form method="GET" action="{{ route('tasks.index') }}" id="taskFilterForm">
        {{-- Preserve Active Status Tab --}}
        <input type="hidden" name="status" value="{{ $selectedStatus }}">

        <div class="row g-2 align-items-end">
            {{-- Search --}}
            <div class="col-12 col-md-6 col-lg-3">
                <label class="form-label-enhanced">Search</label>
                <div style="position:relative;">
                    <i data-lucide="search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:var(--text-muted);pointer-events:none;"></i>
                    <input type="text" name="search" class="form-control form-control-dark" style="padding-left:32px;height:38px;font-size:.85rem;" placeholder="Search title, description..." value="{{ request('search') }}">
                </div>
            </div>

            {{-- Client Filter (Searchable / Typeable) --}}
            <div class="col-12 col-md-6 col-lg-3">
                <label class="form-label-enhanced">Client / Business</label>
                <div class="searchable-select-wrap" id="clientSelectWrapper">
                    <div style="position: relative;">
                        <i data-lucide="building-2" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:var(--text-muted);pointer-events:none;z-index:2;"></i>
                        <input type="text" 
                               id="clientSearchInput" 
                               class="form-control form-control-dark" 
                               style="padding-left:32px;padding-right:32px;height:38px;font-size:.85rem;" 
                               placeholder="All Clients (Type name to search...)" 
                               autocomplete="off"
                               value="{{ $activeClient ? ($activeClient->company_name ?: $activeClient->name) : '' }}">
                        
                        <input type="hidden" name="client_id" id="selectedClientId" value="{{ request('client_id') }}">
                        
                        <button type="button" 
                                id="clientClearBtn" 
                                style="position:absolute;right:8px;top:50%;transform:translateY(-50%);padding:2px 4px;border:none;background:transparent;color:var(--text-muted);display:{{ request('client_id') ? 'block' : 'none' }};z-index:3;cursor:pointer;" 
                                title="Clear client filter">
                            <i data-lucide="x" style="width:13px;height:13px;"></i>
                        </button>
                    </div>

                    {{-- Dropdown list --}}
                    <div id="clientDropdownMenu" class="searchable-dropdown-list">
                        <div class="searchable-item {{ !request('client_id') ? 'selected' : '' }}" data-id="" data-name="" style="font-weight:600; color:var(--text-muted); border-bottom:1px solid var(--border); display:flex; align-items:center; gap:6px;">
                            <i data-lucide="users" style="width:13px;height:13px;"></i> All Clients
                        </div>
                        @foreach($clients as $c)
                            @php 
                                $displayName = $c->company_name ?: $c->name;
                                $subDetail = ($c->company_name && $c->name && $c->name !== $c->company_name) ? $c->name : ($c->phone ?: $c->system_url);
                            @endphp
                            <div class="searchable-item {{ request('client_id') == $c->id ? 'selected' : '' }}" 
                                 data-id="{{ $c->id }}" 
                                 data-name="{{ $displayName }}" 
                                 data-search="{{ strtolower($c->company_name . ' ' . $c->name . ' ' . ($c->phone ?? '') . ' ' . ($c->system_url ?? '')) }}">
                                <div style="font-weight:600; color:var(--text-primary);">{{ $displayName }}</div>
                                @if($subDetail)
                                    <div style="font-size:.70rem; color:var(--text-muted);">{{ $subDetail }}</div>
                                @endif
                            </div>
                        @endforeach
                        <div id="noClientMatch" style="display:none; padding:10px 12px; font-size:.80rem; color:var(--text-muted); text-align:center;">
                            No matching clients found
                        </div>
                    </div>
                </div>
            </div>

            {{-- Task Type --}}
            <div class="col-6 col-md-4 col-lg-2">
                <label class="form-label-enhanced">Type</label>
                <select name="task_type_id" class="form-select form-select-dark" style="height:38px;font-size:.85rem;">
                    <option value="">All Types</option>
                    @foreach($taskTypes as $t)
                        <option value="{{ $t->id }}" {{ (request('task_type_id') == $t->id || request('type') == $t->slug || request('type') == $t->id) ? 'selected' : '' }}>
                            {{ $t->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Priority --}}
            <div class="col-6 col-md-4 col-lg-2">
                <label class="form-label-enhanced">Priority</label>
                <select name="priority" class="form-select form-select-dark" style="height:38px;font-size:.85rem;">
                    <option value="">All Priorities</option>
                    <option value="high" {{ request('priority')=='high'?'selected':'' }}>High</option>
                    <option value="medium" {{ request('priority')=='medium'?'selected':'' }}>Medium</option>
                    <option value="low" {{ request('priority')=='low'?'selected':'' }}>Low</option>
                </select>
            </div>

            {{-- Assigned To (if view all permission) --}}
            @if(Auth::user()->can('view all tasks') || Auth::user()->hasRole('super-admin') || Auth::user()->isDefaultSuperAdmin())
            <div class="col-6 col-md-4 col-lg-2">
                <label class="form-label-enhanced">Assignee</label>
                <select name="assigned_to" class="form-select form-select-dark" style="height:38px;font-size:.85rem;">
                    <option value="">All Assignees</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('assigned_to') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>

        {{-- Date Filter Row --}}
        <div class="row g-2 align-items-end mt-2 pt-2 border-top" style="border-color: var(--border) !important;">
            {{-- Date Type --}}
            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label-enhanced"><i data-lucide="calendar" style="width:12px;height:12px;display:inline-block;vertical-align:middle;color:var(--accent);"></i> Date By</label>
                <select name="date_type" class="form-select form-select-dark" style="height:38px;font-size:.85rem;">
                    <option value="deadline" {{ request('date_type','deadline') == 'deadline' ? 'selected' : '' }}>Deadline / Due Date</option>
                    <option value="created_at" {{ request('date_type') == 'created_at' || request('date_type') == 'created' ? 'selected' : '' }}>Created Date</option>
                    <option value="completed_at" {{ request('date_type') == 'completed_at' || request('date_type') == 'completed' ? 'selected' : '' }}>Completed Date</option>
                </select>
            </div>

            {{-- Quick Date Dropdown --}}
            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label-enhanced"><i data-lucide="clock" style="width:12px;height:12px;display:inline-block;vertical-align:middle;color:var(--accent);"></i> Quick Date</label>
                <select name="date_preset" id="date_preset_select" class="form-select form-select-dark" style="height:38px;font-size:.85rem;" onchange="handleDatePresetChange(this.value)">
                    <option value="">All Dates / Custom</option>
                    <option value="today" {{ request('date_preset') == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ request('date_preset') == 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ request('date_preset') == 'month' ? 'selected' : '' }}>This Month</option>
                    <option value="overdue" {{ request('date_preset') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>

            {{-- Date From --}}
            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label-enhanced">From Date</label>
                <input type="date" name="date_from" id="date_from_input" class="form-control form-control-dark" style="height:38px;font-size:.85rem;" value="{{ request('date_from') }}">
            </div>

            {{-- Date To --}}
            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label-enhanced">To Date</label>
                <input type="date" name="date_to" id="date_to_input" class="form-control form-control-dark" style="height:38px;font-size:.85rem;" value="{{ request('date_to') }}">
            </div>

            {{-- Actions --}}
            <div class="col-12 col-md-12 col-lg-4">
                <label class="form-label-enhanced d-none d-lg-block">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn-gradient flex-fill justify-content-center" style="height:38px;padding:.45rem .8rem;font-size:.85rem;">
                        <i data-lucide="filter" style="width:14px;height:14px;"></i> Apply Filter
                    </button>
                    @if(request()->hasAny(['search','client_id','task_type_id','type','priority','assigned_to','date_from','date_to','date_preset']))
                        <a href="{{ route('tasks.index', ['status' => $selectedStatus]) }}" class="btn-ghost" style="height:38px;padding:.45rem .85rem;" title="Reset Filters">
                            <i data-lucide="rotate-ccw" style="width:14px;height:14px;"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </form>

    {{-- Active Filter Tags --}}
    @if(request()->hasAny(['search', 'client_id', 'task_type_id', 'type', 'priority', 'assigned_to', 'date_from', 'date_to', 'date_preset']))
        <div class="d-flex flex-wrap align-items-center gap-2 pt-3 mt-3 border-top" style="border-color: var(--border) !important;">
            <span style="font-size: .75rem; color: var(--text-muted); font-weight: 600;">Active Filters:</span>

            @if(request('search'))
                <a href="{{ route('tasks.index', array_merge(request()->except('search'), ['status' => $selectedStatus])) }}" class="badge" style="background: var(--bg-hover); color: var(--text-primary); border: 1px solid var(--border); text-decoration: none; padding: 4px 8px; font-size: .72rem;">
                    Search: "{{ request('search') }}" <i data-lucide="x" style="width:11px;height:11px;display:inline;margin-left:3px;"></i>
                </a>
            @endif

            @if(request('client_id'))
                @php $activeClient = $clients->firstWhere('id', request('client_id')); @endphp
                <a href="{{ route('tasks.index', array_merge(request()->except('client_id'), ['status' => $selectedStatus])) }}" class="badge" style="background: var(--bg-hover); color: var(--text-primary); border: 1px solid var(--border); text-decoration: none; padding: 4px 8px; font-size: .72rem;">
                    Client: {{ $activeClient ? ($activeClient->company_name ?? $activeClient->name) : 'Selected' }} <i data-lucide="x" style="width:11px;height:11px;display:inline;margin-left:3px;"></i>
                </a>
            @endif

            @if(request('task_type_id') || request('type'))
                @php
                    $activeTypeId = request('task_type_id') ?: (is_numeric(request('type')) ? request('type') : null);
                    $activeType = $activeTypeId ? $taskTypes->firstWhere('id', $activeTypeId) : $taskTypes->firstWhere('slug', request('type'));
                @endphp
                <a href="{{ route('tasks.index', array_merge(request()->except(['task_type_id', 'type']), ['status' => $selectedStatus])) }}" class="badge" style="background: var(--bg-hover); color: var(--text-primary); border: 1px solid var(--border); text-decoration: none; padding: 4px 8px; font-size: .72rem;">
                    Type: {{ $activeType ? $activeType->name : ucfirst(str_replace('_',' ', request('type', 'Selected'))) }} <i data-lucide="x" style="width:11px;height:11px;display:inline;margin-left:3px;"></i>
                </a>
            @endif

            @if(request('priority'))
                <a href="{{ route('tasks.index', array_merge(request()->except('priority'), ['status' => $selectedStatus])) }}" class="badge" style="background: var(--bg-hover); color: var(--text-primary); border: 1px solid var(--border); text-decoration: none; padding: 4px 8px; font-size: .72rem;">
                    Priority: {{ ucfirst(request('priority')) }} <i data-lucide="x" style="width:11px;height:11px;display:inline;margin-left:3px;"></i>
                </a>
            @endif

            @if(request('date_preset'))
                <a href="{{ route('tasks.index', array_merge(request()->except('date_preset'), ['status' => $selectedStatus])) }}" class="badge" style="background: var(--bg-hover); color: #ff7828; border: 1px solid rgba(255,120,40,0.3); text-decoration: none; padding: 4px 8px; font-size: .72rem;">
                    Date: {{ ucfirst(request('date_preset')) }} <i data-lucide="x" style="width:11px;height:11px;display:inline;margin-left:3px;"></i>
                </a>
            @endif

            @if(request('date_from') || request('date_to'))
                <a href="{{ route('tasks.index', array_merge(request()->except(['date_from', 'date_to']), ['status' => $selectedStatus])) }}" class="badge" style="background: var(--bg-hover); color: var(--text-primary); border: 1px solid var(--border); text-decoration: none; padding: 4px 8px; font-size: .72rem;">
                    Range: {{ request('date_from', 'Start') }} to {{ request('date_to', 'End') }} <i data-lucide="x" style="width:11px;height:11px;display:inline;margin-left:3px;"></i>
                </a>
            @endif

            <a href="{{ route('tasks.index', ['status' => $selectedStatus]) }}" style="font-size: .72rem; color: #ff7828; text-decoration: underline; margin-left: auto;">
                Reset Filter Fields
            </a>
        </div>
    @endif
</div>

{{-- Results Header --}}
<div class="d-flex align-items-center justify-content-between mb-3">
    <div style="font-size: .84rem; color: var(--text-muted); font-weight: 500;">
        Viewing <strong style="color: var(--accent);">{{ $selectedStatus === 'all' ? 'All' : ucfirst(str_replace('_', ' ', $selectedStatus)) }}</strong> tasks: 
        Showing <strong style="color: var(--text-primary);">{{ $tasks->firstItem() ?? 0 }}–{{ $tasks->lastItem() ?? 0 }}</strong> of <strong style="color: var(--text-primary);">{{ $tasks->total() }}</strong>
    </div>
</div>

{{-- Tasks Table Card --}}
<div class="card-glass p-0 overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-dark-custom align-middle mb-0">
            <thead>
                <tr>
                    <th style="padding-left: 1.25rem; min-width: 230px;">Task Title</th>
                    <th style="min-width: 220px;">Customer / Business & URL</th>
                    <th>Type</th>
                    <th>Priority</th>
                    <th style="min-width: 140px;">Status</th>
                    <th>Deadline</th>
                    <th>Assigned To</th>
                    <th style="text-align: right; padding-right: 1.25rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $task)
                @php
                    $canChangeStatus = Auth::user()->can('change task status') && ($isSuperAdmin || (int)$task->assigned_to === (int)Auth::id());
                @endphp
                <tr class="{{ $task->is_overdue ? 'overdue-row' : '' }}" id="task-row-{{ $task->id }}">
                    {{-- Task Title --}}
                    <td style="padding-left: 1.25rem;">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <span class="badge" style="background: rgba(99,102,241,.12); color: var(--accent); font-size: .68rem; font-weight: 700; padding: 2px 6px;">
                                    #{{ $task->id }}
                                </span>
                                @if($task->is_overdue)
                                    <span class="overdue-badge" style="font-size:.62rem; padding:1px 5px;">OVERDUE</span>
                                @endif
                                @if($task->comments->count() > 0)
                                    <span style="font-size: .7rem; color: var(--text-muted); display: inline-flex; align-items: center; gap: 3px;">
                                        <i data-lucide="message-square" style="width: 11px; height: 11px;"></i> {{ $task->comments->count() }}
                                    </span>
                                @endif
                            </div>
                            <a href="{{ route('tasks.show', $task) }}" style="color: var(--text-primary); text-decoration: none; font-weight: 600; font-size: .88rem; line-height: 1.35; display: inline-block;" class="hover-accent">
                                {{ $task->title }}
                            </a>
                        </div>
                    </td>

                    {{-- Customer Business & System URL --}}
                    <td>
                        @if($task->client)
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width: 24px; height: 24px; border-radius: 6px; background: linear-gradient(135deg, #ff7828 0%, #ea580c 100%); display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #fff; font-weight: 800; font-size: .72rem;">
                                        {{ strtoupper(substr($task->client->company_name ?? $task->client->name ?? 'C', 0, 1)) }}
                                    </div>
                                    <a href="{{ route('clients.show', $task->client) }}" style="font-weight: 650; font-size: .84rem; color: var(--text-primary); text-decoration: none;" class="hover-accent" title="View Client Profile">
                                        {{ $task->client->company_name ?? $task->client->name }}
                                    </a>
                                </div>
                                
                                <div class="d-flex flex-wrap align-items-center gap-2 mt-1">
                                    @if($task->client->system_url)
                                        <a href="{{ $task->client->formatted_system_url }}" target="_blank" rel="noopener noreferrer" class="badge" style="background: var(--bg-hover); color: var(--accent); border: 1px solid var(--border); text-decoration: none; padding: 3px 7px; font-size: .70rem; font-weight: 600; display: inline-flex; align-items: center; gap: 3px; max-width: 190px;" title="Open System: {{ $task->client->system_url }}">
                                            <i data-lucide="external-link" style="width: 10px; height: 10px; flex-shrink: 0;"></i>
                                            <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $task->client->system_url }}</span>
                                        </a>
                                    @endif

                                    @if($task->client->name && $task->client->name !== $task->client->company_name)
                                        <span style="font-size: .72rem; color: var(--text-muted); display: inline-flex; align-items: center; gap: 2px;">
                                            <i data-lucide="user" style="width: 10px; height: 10px;"></i> {{ $task->client->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @else
                            <span class="badge" style="background: rgba(148,163,184,.1); color: var(--text-muted); font-size: .72rem; font-weight: 500; padding: 3px 8px; border-radius: 6px;">
                                Internal Task
                            </span>
                        @endif
                    </td>

                    {{-- Type --}}
                    <td>
                        <span class="badge" style="background: var(--bg-hover); color: var(--text-primary); font-size: .72rem; font-weight: 550; padding: 4px 8px; border-radius: 6px; border: 1px solid var(--border);">
                            <i data-lucide="{{ $task->type_icon }}" style="width: 11px; height: 11px; display: inline-block; vertical-align: middle; margin-right: 3px; color: var(--accent);"></i>
                            {{ $task->type_label }}
                        </span>
                    </td>

                    {{-- Priority --}}
                    <td>
                        <span class="badge badge-{{ $task->priority }}" style="font-size: .72rem;">{{ ucfirst($task->priority) }}</span>
                    </td>

                    {{-- Status with Inline Quick Change Action --}}
                    <td>
                        @if($canChangeStatus)
                            <div class="dropdown">
                                <button class="badge status-{{ $task->status }} dropdown-toggle border-0" 
                                        type="button" 
                                        data-bs-toggle="dropdown" 
                                        aria-expanded="false" 
                                        style="cursor: pointer; display: inline-flex; align-items: center; gap: 4px; font-size: .74rem; padding: 4px 8px;"
                                        title="Click to update status">
                                    <span>{{ ucfirst(str_replace('_',' ',$task->status)) }}</span>
                                    <i data-lucide="chevron-down" style="width: 11px; height: 11px;"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-dark" style="font-size: .8rem; min-width: 160px; z-index: 1050; background: var(--dropdown-bg); border-color: var(--border); box-shadow: var(--shadow-dropdown);">
                                    <li class="dropdown-header" style="font-size: .68rem; text-transform: uppercase; letter-spacing: .5px; color: var(--text-muted);">Change Status</li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center justify-content-between {{ $task->status == 'pending' ? 'active' : '' }}" 
                                           href="javascript:void(0);" 
                                           onclick="quickUpdateTaskStatus({{ $task->id }}, 'pending')">
                                            <span><span class="status-dot-indicator" style="background:#f59e0b;"></span> Pending</span>
                                            @if($task->status == 'pending')<i data-lucide="check" style="width:12px;height:12px;"></i>@endif
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center justify-content-between {{ $task->status == 'in_progress' ? 'active' : '' }}" 
                                           href="javascript:void(0);" 
                                           onclick="quickUpdateTaskStatus({{ $task->id }}, 'in_progress')">
                                            <span><span class="status-dot-indicator" style="background:#3b82f6;"></span> In Progress</span>
                                            @if($task->status == 'in_progress')<i data-lucide="check" style="width:12px;height:12px;"></i>@endif
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center justify-content-between {{ $task->status == 'on_hold' ? 'active' : '' }}" 
                                           href="javascript:void(0);" 
                                           onclick="quickUpdateTaskStatus({{ $task->id }}, 'on_hold')">
                                            <span><span class="status-dot-indicator" style="background:#a855f7;"></span> On Hold</span>
                                            @if($task->status == 'on_hold')<i data-lucide="check" style="width:12px;height:12px;"></i>@endif
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center justify-content-between {{ $task->status == 'under_review' ? 'active' : '' }}" 
                                           href="javascript:void(0);" 
                                           onclick="quickUpdateTaskStatus({{ $task->id }}, 'under_review')">
                                            <span><span class="status-dot-indicator" style="background:#06b6d4;"></span> Under Review</span>
                                            @if($task->status == 'under_review')<i data-lucide="check" style="width:12px;height:12px;"></i>@endif
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center justify-content-between {{ $task->status == 'completed' ? 'active' : '' }}" 
                                           href="javascript:void(0);" 
                                           onclick="quickUpdateTaskStatus({{ $task->id }}, 'completed')">
                                            <span><span class="status-dot-indicator" style="background:#10b981;"></span> Completed</span>
                                            @if($task->status == 'completed')<i data-lucide="check" style="width:12px;height:12px;"></i>@endif
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <span class="badge status-{{ $task->status }}" style="font-size: .72rem;">{{ ucfirst(str_replace('_',' ',$task->status)) }}</span>
                        @endif
                    </td>

                    {{-- Deadline --}}
                    <td>
                        @if($task->deadline)
                            <div style="font-size: .80rem; font-weight: 550; color: {{ $task->is_overdue ? 'var(--danger)' : 'var(--text-primary)' }};">
                                {{ $task->deadline->format('d M Y') }}
                            </div>
                            @if(!$task->is_overdue && $task->status != 'completed')
                                <div style="font-size: .68rem; color: var(--text-muted);">{{ $task->deadline->diffForHumans() }}</div>
                            @endif
                        @else
                            <span style="font-size: .76rem; color: var(--text-muted); opacity: .6;">No deadline</span>
                        @endif
                    </td>

                    {{-- Assigned To --}}
                    <td>
                        @if($task->assignedTo)
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <img src="{{ $task->assignedTo->avatar_url }}" class="avatar-sm" style="width: 26px; height: 26px;" alt="{{ $task->assignedTo->name }}">
                                <span style="font-size: .80rem; color: var(--text-primary); font-weight: 500;">{{ Str::limit($task->assignedTo->name, 14) }}</span>
                            </div>
                        @else
                            <span style="font-size: .74rem; color: var(--text-muted); font-style: italic;">Unassigned</span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td style="text-align: right; padding-right: 1.25rem;">
                        <div class="d-inline-flex gap-1 align-items-center">
                            <a href="{{ route('tasks.show', $task) }}" class="btn-icon" style="width: 32px; height: 32px;" title="View Details">
                                <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                            </a>
                            @can('edit tasks')
                            <a href="{{ route('tasks.edit', $task) }}" class="btn-icon" style="width: 32px; height: 32px;" title="Edit Task">
                                <i data-lucide="pencil" style="width: 13px; height: 13px;"></i>
                            </a>
                            @endcan
                            @can('delete tasks')
                            <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Are you sure you want to delete task #{{ $task->id }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon" style="width: 32px; height: 32px; color: #ef4444; border-color: rgba(239,68,68,.2);" title="Delete Task">
                                    <i data-lucide="trash-2" style="width: 13px; height: 13px;"></i>
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
                            <div style="width: 52px; height: 52px; background: rgba(99,102,241,.1); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                                <i data-lucide="list-checks" style="width: 24px; height: 24px; color: var(--accent);"></i>
                            </div>
                            <h5 style="font-weight: 700; color: var(--text-primary); margin-bottom: .35rem;">No Tasks Found</h5>
                            <p style="font-size: .85rem; color: var(--text-muted); max-width: 400px; margin: 0 auto 1.25rem;">
                                @if(request()->hasAny(['search','client_id','type','priority','status','date_from','date_to','date_preset']))
                                    No tasks match the active filter criteria for <strong>{{ $selectedStatus === 'all' ? 'All' : ucfirst(str_replace('_',' ',$selectedStatus)) }}</strong> status.
                                @else
                                    No {{ $selectedStatus === 'all' ? '' : strtolower(str_replace('_',' ',$selectedStatus)) }} tasks found. Create a new task or check other tabs.
                                @endif
                            </p>
                            <div class="d-flex justify-content-center gap-2">
                                @if(request()->hasAny(['search','client_id','type','priority','date_from','date_to','date_preset']))
                                    <a href="{{ route('tasks.index', ['status' => $selectedStatus]) }}" class="btn-ghost" style="padding: .5rem 1rem;">
                                        <i data-lucide="rotate-ccw" style="width: 14px; height: 14px;"></i> Clear Filters
                                    </a>
                                @endif
                                <a href="{{ route('tasks.create') }}" class="btn-gradient" style="padding: .5rem 1.1rem;">
                                    <i data-lucide="plus" style="width: 14px; height: 14px;"></i> Create Task
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
@if($tasks->hasPages())
    <div class="mt-4">
        {{ $tasks->links() }}
    </div>
@endif

@endsection

@push('scripts')
<script>
// Quick Update Task Status from Table Dropdown
function quickUpdateTaskStatus(taskId, newStatus) {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch(`/tasks/${taskId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(async res => {
        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
            throw new Error(data.message || 'Failed to update status');
        }
        return data;
    })
    .then(data => {
        if (window.showToast) {
            const formatted = newStatus.replace('_', ' ').toUpperCase();
            window.showToast(`Task #${taskId} moved to ${formatted}!`, 'success');
        }
        setTimeout(() => {
            window.location.reload();
        }, 400);
    })
    .catch(err => {
        if (window.showToast) {
            window.showToast(err.message || 'Error updating status', 'error');
        } else {
            alert(err.message || 'Error updating status');
        }
    });
}

// Quick Date Preset Change Handler
function handleDatePresetChange(val) {
    const fromInput = document.getElementById('date_from_input');
    const toInput = document.getElementById('date_to_input');
    if (val && val !== '') {
        if (fromInput) fromInput.value = '';
        if (toInput) toInput.value = '';
    }
}

// Searchable Client Dropdown Logic
document.addEventListener('DOMContentLoaded', function() {
    const wrap = document.getElementById('clientSelectWrapper');
    if (!wrap) return;

    const input = document.getElementById('clientSearchInput');
    const hiddenId = document.getElementById('selectedClientId');
    const clearBtn = document.getElementById('clientClearBtn');
    const menu = document.getElementById('clientDropdownMenu');
    const noMatch = document.getElementById('noClientMatch');
    const items = menu ? menu.querySelectorAll('.searchable-item') : [];

    function filterItems(query) {
        let visibleCount = 0;
        const q = query.trim().toLowerCase();

        items.forEach(item => {
            const searchData = item.getAttribute('data-search') || '';
            const itemName = (item.getAttribute('data-name') || '').toLowerCase();
            const isAllOption = item.getAttribute('data-id') === '';

            if (q === '') {
                item.style.display = 'block';
                visibleCount++;
            } else if (isAllOption) {
                item.style.display = 'none';
            } else if (searchData.includes(q) || itemName.includes(q)) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        if (noMatch) {
            noMatch.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    // Open dropdown on focus/click
    input.addEventListener('focus', () => {
        menu.style.display = 'block';
        filterItems(input.value);
    });

    input.addEventListener('click', () => {
        menu.style.display = 'block';
    });

    // Real-time typing in Client field
    input.addEventListener('input', () => {
        menu.style.display = 'block';
        filterItems(input.value);
        if (input.value.trim() === '') {
            hiddenId.value = '';
            clearBtn.style.display = 'none';
        } else {
            clearBtn.style.display = 'block';
        }
    });

    // Client item selection
    items.forEach(item => {
        item.addEventListener('click', () => {
            const id = item.getAttribute('data-id');
            const name = item.getAttribute('data-name');

            hiddenId.value = id;
            input.value = name;

            items.forEach(i => i.classList.remove('selected'));
            item.classList.add('selected');

            if (id) {
                clearBtn.style.display = 'block';
            } else {
                clearBtn.style.display = 'none';
                input.value = '';
            }

            menu.style.display = 'none';
        });
    });

    // Clear Client selection
    clearBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        hiddenId.value = '';
        input.value = '';
        clearBtn.style.display = 'none';
        items.forEach(i => i.classList.remove('selected'));
        const allOption = menu.querySelector('[data-id=""]');
        if (allOption) allOption.classList.add('selected');
        filterItems('');
    });

    // Close when clicking outside
    document.addEventListener('click', (e) => {
        if (!wrap.contains(e.target)) {
            menu.style.display = 'none';
        }
    });

    // Keyboard navigation (Escape to close)
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            menu.style.display = 'none';
        }
    });

    // Clear Date Preset if user selects manual From/To dates
    const fromInput = document.getElementById('date_from_input');
    const toInput = document.getElementById('date_to_input');
    const presetSelect = document.getElementById('date_preset_select');

    [fromInput, toInput].forEach(el => {
        if (el) {
            el.addEventListener('change', () => {
                if (presetSelect && el.value !== '') {
                    presetSelect.value = '';
                }
            });
        }
    });
});
</script>
@endpush
