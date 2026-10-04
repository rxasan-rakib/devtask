@extends('layouts.app')
@section('title', 'All Tasks')
@section('page-title', 'All Tasks')

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

{{-- Filter Card --}}
<div class="card-glass mb-4">
    <form method="GET" action="{{ route('tasks.index') }}">
        <div class="row g-2 align-items-end">
            {{-- Search --}}
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label-enhanced">Search</label>
                <div style="position:relative;">
                    <i data-lucide="search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:var(--text-muted);pointer-events:none;"></i>
                    <input type="text" name="search" class="form-control form-control-dark" style="padding-left:32px;height:38px;font-size:.85rem;" placeholder="Search task, client, URL..." value="{{ request('search') }}">
                </div>
            </div>

            {{-- Client Filter --}}
            <div class="col-6 col-sm-6 col-lg-3">
                <label class="form-label-enhanced">Client / Business</label>
                <select name="client_id" class="form-select form-select-dark" style="height:38px;font-size:.85rem;">
                    <option value="">All Clients</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->company_name ?? $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Type --}}
            <div class="col-6 col-sm-4 col-lg-2">
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
            <div class="col-6 col-sm-4 col-lg-1">
                <label class="form-label-enhanced">Priority</label>
                <select name="priority" class="form-select form-select-dark" style="height:38px;font-size:.85rem;">
                    <option value="">All</option>
                    <option value="high" {{ request('priority')=='high'?'selected':'' }}>High</option>
                    <option value="medium" {{ request('priority')=='medium'?'selected':'' }}>Medium</option>
                    <option value="low" {{ request('priority')=='low'?'selected':'' }}>Low</option>
                </select>
            </div>

            {{-- Status --}}
            <div class="col-6 col-sm-4 col-lg-1">
                <label class="form-label-enhanced">Status</label>
                <select name="status" class="form-select form-select-dark" style="height:38px;font-size:.85rem;">
                    <option value="">All</option>
                    <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                    <option value="in_progress" {{ request('status')=='in_progress'?'selected':'' }}>In Progress</option>
                    <option value="on_hold" {{ request('status')=='on_hold'?'selected':'' }}>On Hold</option>
                    <option value="under_review" {{ request('status')=='under_review'?'selected':'' }}>Review</option>
                    <option value="completed" {{ request('status')=='completed'?'selected':'' }}>Done</option>
                </select>
            </div>

            {{-- Actions --}}
            <div class="col-12 col-sm-12 col-lg-2">
                <label class="form-label-enhanced d-none d-lg-block">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn-gradient flex-fill justify-content-center" style="height:38px;padding:.45rem .8rem;font-size:.85rem;">
                        <i data-lucide="filter" style="width:14px;height:14px;"></i> Filter
                    </button>
                    @if(request()->hasAny(['search','client_id','task_type_id','type','priority','status','assigned_to']))
                        <a href="{{ route('tasks.index') }}" class="btn-ghost" style="height:38px;padding:.45rem .85rem;" title="Reset Filters">
                            <i data-lucide="rotate-ccw" style="width:14px;height:14px;"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </form>

    {{-- Active Filter Tags --}}
    @if(request()->hasAny(['search', 'client_id', 'task_type_id', 'type', 'priority', 'status']))
        <div class="d-flex flex-wrap align-items-center gap-2 pt-3 mt-3 border-top" style="border-color: var(--border) !important;">
            <span style="font-size: .75rem; color: var(--text-muted); font-weight: 600;">Active Filters:</span>

            @if(request('search'))
                <a href="{{ route('tasks.index', request()->except('search')) }}" class="badge" style="background: var(--bg-hover); color: var(--text-primary); border: 1px solid var(--border); text-decoration: none; padding: 4px 8px; font-size: .72rem;">
                    Search: "{{ request('search') }}" <i data-lucide="x" style="width:11px;height:11px;display:inline;margin-left:3px;"></i>
                </a>
            @endif

            @if(request('client_id'))
                @php $activeClient = $clients->firstWhere('id', request('client_id')); @endphp
                <a href="{{ route('tasks.index', request()->except('client_id')) }}" class="badge" style="background: var(--bg-hover); color: var(--text-primary); border: 1px solid var(--border); text-decoration: none; padding: 4px 8px; font-size: .72rem;">
                    Client: {{ $activeClient ? ($activeClient->company_name ?? $activeClient->name) : 'Selected' }} <i data-lucide="x" style="width:11px;height:11px;display:inline;margin-left:3px;"></i>
                </a>
            @endif

            @if(request('task_type_id') || request('type'))
                @php
                    $activeTypeId = request('task_type_id') ?: (is_numeric(request('type')) ? request('type') : null);
                    $activeType = $activeTypeId ? $taskTypes->firstWhere('id', $activeTypeId) : $taskTypes->firstWhere('slug', request('type'));
                @endphp
                <a href="{{ route('tasks.index', request()->except(['task_type_id', 'type'])) }}" class="badge" style="background: var(--bg-hover); color: var(--text-primary); border: 1px solid var(--border); text-decoration: none; padding: 4px 8px; font-size: .72rem;">
                    Type: {{ $activeType ? $activeType->name : ucfirst(str_replace('_',' ', request('type', 'Selected'))) }} <i data-lucide="x" style="width:11px;height:11px;display:inline;margin-left:3px;"></i>
                </a>
            @endif

            @if(request('priority'))
                <a href="{{ route('tasks.index', request()->except('priority')) }}" class="badge" style="background: var(--bg-hover); color: var(--text-primary); border: 1px solid var(--border); text-decoration: none; padding: 4px 8px; font-size: .72rem;">
                    Priority: {{ ucfirst(request('priority')) }} <i data-lucide="x" style="width:11px;height:11px;display:inline;margin-left:3px;"></i>
                </a>
            @endif

            @if(request('status'))
                <a href="{{ route('tasks.index', request()->except('status')) }}" class="badge" style="background: var(--bg-hover); color: var(--text-primary); border: 1px solid var(--border); text-decoration: none; padding: 4px 8px; font-size: .72rem;">
                    Status: {{ ucfirst(str_replace('_',' ', request('status'))) }} <i data-lucide="x" style="width:11px;height:11px;display:inline;margin-left:3px;"></i>
                </a>
            @endif

            <a href="{{ route('tasks.index') }}" style="font-size: .72rem; color: #ff7828; text-decoration: underline; margin-left: auto;">
                Reset All
            </a>
        </div>
    @endif
</div>

{{-- Results Header --}}
<div class="d-flex align-items-center justify-content-between mb-3">
    <div style="font-size: .82rem; color: var(--text-muted); font-weight: 500;">
        Showing <strong style="color: var(--text-primary);">{{ $tasks->firstItem() ?? 0 }}–{{ $tasks->lastItem() ?? 0 }}</strong> of <strong style="color: var(--text-primary);">{{ $tasks->total() }}</strong> tasks
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
                    <th>Status</th>
                    <th>Deadline</th>
                    <th>Assigned To</th>
                    <th style="text-align: right; padding-right: 1.25rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $task)
                <tr class="{{ $task->is_overdue ? 'overdue-row' : '' }}">
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

                    {{-- Status --}}
                    <td>
                        <span class="badge status-{{ $task->status }}" style="font-size: .72rem;">{{ ucfirst(str_replace('_',' ',$task->status)) }}</span>
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
                                @if(request()->hasAny(['search','client_id','type','priority','status']))
                                    No tasks match the active filter criteria. Try resetting filters.
                                @else
                                    Create a new task to start organizing and tracking your work.
                                @endif
                            </p>
                            <div class="d-flex justify-content-center gap-2">
                                @if(request()->hasAny(['search','client_id','type','priority','status']))
                                    <a href="{{ route('tasks.index') }}" class="btn-ghost" style="padding: .5rem 1rem;">
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
    <div class="mt-4 d-flex justify-content-center">
        {{ $tasks->links() }}
    </div>
@endif

@endsection
