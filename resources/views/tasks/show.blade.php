@extends('layouts.app')
@section('title', $task->title)
@section('page-title', 'Task Details')

@section('content')

{{-- Breadcrumb & Quick Nav --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2" style="font-size: .84rem; color: var(--text-muted);">
        <a href="{{ route('tasks.index') }}" style="color: var(--text-muted); text-decoration: none;" class="hover-accent">Tasks</a>
        <span>/</span>
        <span style="color: var(--accent); font-weight: 600;">#TASK-{{ $task->id }}</span>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('tasks.index') }}" class="btn-ghost" style="padding: .4rem .85rem; font-size: .82rem;">
            <i data-lucide="arrow-left" style="width:14px;height:14px;"></i> All Tasks
        </a>
        <a href="{{ route('tasks.kanban') }}" class="btn-ghost" style="padding: .4rem .85rem; font-size: .82rem;">
            <i data-lucide="kanban" style="width:14px;height:14px;"></i> Kanban
        </a>
        @can('edit tasks')
        <a href="{{ route('tasks.edit', $task) }}" class="btn-gradient" style="padding: .4rem 1rem; font-size: .82rem;">
            <i data-lucide="pencil" style="width:14px;height:14px;"></i> Edit Task
        </a>
        @endcan
    </div>
</div>

{{-- Top Task Banner Card --}}
<div class="card-glass mb-4" style="background: linear-gradient(135deg, rgba(99,102,241,0.06) 0%, rgba(255,120,40,0.06) 100%);">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                <span class="badge" style="background: rgba(99,102,241,.15); color: var(--accent); font-size: .75rem; font-weight: 700; padding: 4px 8px;">
                    #TASK-{{ $task->id }}
                </span>
                <span class="badge status-{{ $task->status }}" style="font-size: .75rem; padding: 4px 10px;">
                    {{ ucfirst(str_replace('_',' ',$task->status)) }}
                </span>
                <span class="badge badge-{{ $task->priority }}" style="font-size: .75rem; padding: 4px 9px;">
                    {{ ucfirst($task->priority) }} Priority
                </span>
                <span class="badge" style="background: var(--bg-hover); color: var(--text-primary); border: 1px solid var(--border); font-size: .75rem; padding: 4px 9px;">
                    <i data-lucide="{{ $task->type_icon }}" style="width: 12px; height: 12px; display: inline-block; vertical-align: middle; margin-right: 3px; color: var(--accent);"></i>
                    {{ $task->type_label }}
                </span>
                @if($task->is_overdue)
                    <span class="overdue-badge" style="font-size: .75rem; padding: 4px 9px;">⚠️ OVERDUE</span>
                @endif
            </div>

            <h2 style="font-size: 1.35rem; font-weight: 750; color: var(--text-primary); line-height: 1.35; margin: 0;">
                {{ $task->title }}
            </h2>
        </div>

        {{-- Quick Status Changer --}}
        @php
            $isSuperAdmin = auth()->user()->hasRole('super-admin') || auth()->user()->isDefaultSuperAdmin();
            $canChangeThisStatus = auth()->user()->can('change task status') && ($isSuperAdmin || (int)$task->assigned_to === (int)auth()->id());
        @endphp
        @if($canChangeThisStatus)
        <div class="d-flex flex-column align-items-lg-end gap-1">
            <span style="font-size: .74rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: .03em;">Update Status</span>
            <form method="POST" action="{{ route('tasks.status', $task) }}" class="d-flex gap-1 flex-wrap">
                @csrf @method('PATCH')
                @foreach([
                    'pending'      => ['label' => 'Pending',     'icon' => 'clock'],
                    'in_progress'  => ['label' => 'In Progress', 'icon' => 'play'],
                    'on_hold'      => ['label' => 'On Hold',     'icon' => 'pause'],
                    'under_review' => ['label' => 'Review',      'icon' => 'eye'],
                    'completed'    => ['label' => 'Done',        'icon' => 'check-circle'],
                ] as $s => $meta)
                <button type="submit" name="status" value="{{ $s }}"
                    class="badge status-{{ $s }}"
                    style="border: none; cursor: pointer; padding: .42rem .75rem; font-size: .74rem; border-radius: 6px; transition: all .2s; {{ $task->status==$s ? 'outline: 2px solid #ff7828; outline-offset: 1px; font-weight: 700; transform: scale(1.05); box-shadow: 0 2px 8px rgba(0,0,0,0.15);' : 'opacity: .65;' }}"
                    title="Mark as {{ $meta['label'] }}">
                    {{ $meta['label'] }}
                </button>
                @endforeach
            </form>
        </div>
        @else
        <div class="d-flex flex-column align-items-lg-end gap-1">
            <span style="font-size: .74rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: .03em;">Status</span>
            <div class="d-flex align-items-center gap-1">
                <span class="badge status-{{ $task->status }}" style="font-size: .80rem; padding: 5px 12px; border-radius: 6px;">
                    {{ ucfirst(str_replace('_',' ',$task->status)) }}
                </span>
            </div>
            <small style="font-size: 0.68rem; color: var(--text-muted); font-style: italic;">
                (Status can only be updated by the assigned member or Super Admin)
            </small>
        </div>
        @endif
    </div>
</div>

<div class="row g-4">
    {{-- Left Main Column (col-lg-8) --}}
    <div class="col-lg-8">

        {{-- 1. Customer & System Information Card --}}
        <div class="card-glass mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2" style="border-color: var(--border) !important;">
                <div class="section-title mb-0 d-flex align-items-center gap-2">
                    <i data-lucide="building-2" style="width: 17px; height: 17px; color: #ff7828;"></i>
                    Customer & System Information
                </div>
                @if($task->client)
                    <a href="{{ route('clients.show', $task->client) }}" class="btn-ghost" style="padding: .3rem .75rem; font-size: .76rem;">
                        View Client Profile <i data-lucide="arrow-right" style="width: 12px; height: 12px; margin-left: 2px;"></i>
                    </a>
                @endif
            </div>

            @if($task->client)
                <div class="p-3 mb-3 rounded" style="background: rgba(99,102,241,0.04); border: 1px solid rgba(99,102,241,0.12);">
                    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                        {{-- Company & Avatar --}}
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 46px; height: 46px; border-radius: 12px; background: linear-gradient(135deg, #ff7828 0%, #ea580c 100%); display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 12px rgba(255,120,40,.25);">
                                <span style="color: #ffffff; font-weight: 800; font-size: 1.15rem;">
                                    {{ strtoupper(substr($task->client->company_name ?? $task->client->name ?? 'C', 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <h4 style="font-weight: 750; font-size: 1.05rem; color: var(--text-primary); margin: 0 0 2px 0;">
                                    <a href="{{ route('clients.show', $task->client) }}" style="color: inherit; text-decoration: none;" class="hover-accent">
                                        {{ $task->client->company_name ?? $task->client->name }}
                                    </a>
                                </h4>
                                @if($task->client->name && $task->client->name !== $task->client->company_name)
                                    <div style="font-size: .80rem; color: var(--text-muted); display: flex; align-items: center; gap: 4px;">
                                        <i data-lucide="user" style="width: 12px; height: 12px;"></i> Contact: <strong>{{ $task->client->name }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- System Type & Active Status --}}
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge" style="background: rgba(99,102,241,.12); color: var(--accent); font-size: .75rem; font-weight: 600; padding: 5px 10px; border-radius: 6px;">
                                {{ $task->client->type_label }}
                            </span>
                            @if($task->client->is_active)
                                <span class="badge" style="background: rgba(16,185,129,.14); color: #10b981; font-size: .75rem; font-weight: 600; padding: 5px 10px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;">
                                    <span style="width: 6px; height: 6px; background: #10b981; border-radius: 50%;"></span> Active
                                </span>
                            @else
                                <span class="badge" style="background: rgba(148,163,184,.14); color: #94a3b8; font-size: .75rem; font-weight: 600; padding: 5px 10px; border-radius: 6px;">
                                    Inactive
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Contact & Portal Details Grid --}}
                <div class="row g-3">
                    {{-- System Portal URL --}}
                    <div class="col-12 col-md-6">
                        <div class="p-3 rounded h-100" style="background: var(--bg-hover); border: 1px solid var(--border);">
                            <div style="font-size: .72rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; margin-bottom: 5px;">
                                <i data-lucide="globe" style="width: 12px; height: 12px; display: inline; margin-right: 3px;"></i> System / Web URL
                            </div>
                            @if($task->client->system_url)
                                <a href="{{ $task->client->formatted_system_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm w-100 text-start d-flex align-items-center justify-content-between mt-1" style="background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.25); color: var(--accent); font-size: .82rem; font-weight: 600; padding: .45rem .75rem;">
                                    <span class="text-truncate" style="max-width: 85%;">{{ $task->client->system_url }}</span>
                                    <i data-lucide="external-link" style="width: 13px; height: 13px; flex-shrink: 0;"></i>
                                </a>
                            @else
                                <div style="font-size: .82rem; color: var(--text-muted); font-style: italic;">No URL configured</div>
                            @endif
                        </div>
                    </div>

                    {{-- Phone Number --}}
                    <div class="col-12 col-md-6">
                        <div class="p-3 rounded h-100" style="background: var(--bg-hover); border: 1px solid var(--border);">
                            <div style="font-size: .72rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; margin-bottom: 5px;">
                                <i data-lucide="phone" style="width: 12px; height: 12px; display: inline; margin-right: 3px;"></i> Phone / Mobile
                            </div>
                            @if($task->client->phone)
                                <a href="tel:{{ $task->client->phone }}" style="font-size: .88rem; font-weight: 600; color: var(--text-primary); text-decoration: none; display: inline-flex; align-items: center; gap: 6px;" class="hover-accent">
                                    <i data-lucide="phone-call" style="width: 13px; height: 13px; color: #10b981;"></i>
                                    {{ $task->client->phone }}
                                </a>
                            @else
                                <div style="font-size: .82rem; color: var(--text-muted); font-style: italic;">No phone recorded</div>
                            @endif
                        </div>
                    </div>

                    {{-- Email Address --}}
                    <div class="col-12 col-md-6">
                        <div class="p-3 rounded h-100" style="background: var(--bg-hover); border: 1px solid var(--border);">
                            <div style="font-size: .72rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; margin-bottom: 5px;">
                                <i data-lucide="mail" style="width: 12px; height: 12px; display: inline; margin-right: 3px;"></i> Email Address
                            </div>
                            @if($task->client->email)
                                <a href="mailto:{{ $task->client->email }}" style="font-size: .84rem; font-weight: 550; color: var(--text-primary); text-decoration: none; display: inline-flex; align-items: center; gap: 6px; word-break: break-all;" class="hover-accent">
                                    <i data-lucide="send" style="width: 13px; height: 13px; color: var(--accent);"></i>
                                    {{ $task->client->email }}
                                </a>
                            @else
                                <div style="font-size: .82rem; color: var(--text-muted); font-style: italic;">No email recorded</div>
                            @endif
                        </div>
                    </div>

                    {{-- Address / Location --}}
                    <div class="col-12 col-md-6">
                        <div class="p-3 rounded h-100" style="background: var(--bg-hover); border: 1px solid var(--border);">
                            <div style="font-size: .72rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; margin-bottom: 5px;">
                                <i data-lucide="map-pin" style="width: 12px; height: 12px; display: inline; margin-right: 3px;"></i> Location / Address
                            </div>
                            @if($task->client->address)
                                <div style="font-size: .82rem; color: var(--text-primary); line-height: 1.4;">
                                    {{ $task->client->address }}
                                </div>
                            @else
                                <div style="font-size: .82rem; color: var(--text-muted); font-style: italic;">No address recorded</div>
                            @endif
                        </div>
                    </div>

                    {{-- Special Notes (if any) --}}
                    @if($task->client->notes)
                        <div class="col-12">
                            <div class="p-3 rounded" style="background: rgba(245,158,11,0.06); border: 1px solid rgba(245,158,11,0.2);">
                                <div style="font-size: .72rem; color: #f59e0b; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">
                                    <i data-lucide="info" style="width: 12px; height: 12px; display: inline; margin-right: 3px;"></i> Client Notes
                                </div>
                                <div style="font-size: .82rem; color: var(--text-muted); line-height: 1.5;">
                                    {!! nl2br(e($task->client->notes)) !!}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                {{-- No Client Attached --}}
                <div class="text-center py-4 px-3" style="background: var(--bg-hover); border-radius: 8px; border: 1px dashed var(--border);">
                    <i data-lucide="layers" style="width: 32px; height: 32px; color: var(--text-muted); opacity: .4; margin-bottom: .5rem;"></i>
                    <div style="font-weight: 600; font-size: .88rem; color: var(--text-primary);">Internal Task</div>
                    <p style="font-size: .80rem; color: var(--text-muted); max-width: 380px; margin: 0 auto;">
                        This task is an internal or general work item and is not attached to any specific client business profile.
                    </p>
                </div>
            @endif
        </div>

        {{-- 2. Task Description Card --}}
        <div class="card-glass mb-4">
            <div class="section-title d-flex align-items-center gap-2 mb-3">
                <i data-lucide="file-text" style="width: 17px; height: 17px; color: var(--accent);"></i>
                Description & Task Scope
            </div>
            @if($task->description)
                <div style="color: var(--text-primary); font-size: .88rem; line-height: 1.8; background: var(--bg-hover); border: 1px solid var(--border); border-radius: 10px; padding: 1.15rem; white-space: pre-line;">
                    {!! nl2br(e($task->description)) !!}
                </div>
            @else
                <div style="text-align: center; padding: 1.5rem; color: var(--text-muted); font-size: .85rem; font-style: italic; background: var(--bg-hover); border-radius: 8px;">
                    No detailed description provided for this task.
                </div>
            @endif
        </div>

        {{-- 3. Comments & Discussion Card --}}
        <div class="card-glass">
            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2" style="border-color: var(--border) !important;">
                <div class="section-title mb-0 d-flex align-items-center gap-2">
                    <i data-lucide="messages-square" style="width: 17px; height: 17px; color: #0ea5e9;"></i>
                    Discussion & Notes ({{ $task->comments->count() }})
                </div>
            </div>

            {{-- Comment List --}}
            <div class="comments-stream mb-4">
                @forelse($task->comments as $comment)
                    <div class="d-flex gap-3 mb-3 p-3 rounded" style="background: var(--bg-hover); border: 1px solid var(--border); transition: background .2s;">
                        <img src="{{ $comment->user->avatar_url }}" class="avatar-sm" style="width: 34px; height: 34px; border-radius: 50%; border: 1.5px solid var(--accent); flex-shrink: 0;" alt="{{ $comment->user->name }}">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span style="font-size: .84rem; font-weight: 700; color: var(--text-primary);">{{ $comment->user->name }}</span>
                                    <span class="badge" style="background: rgba(99,102,241,0.1); color: var(--accent); font-size: .65rem; padding: 2px 5px;">
                                        {{ ucfirst(str_replace('-',' ', $comment->user->getRoleNames()->first() ?? 'Member')) }}
                                    </span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span style="font-size: .72rem; color: var(--text-muted);" title="{{ $comment->created_at->format('d M Y, h:i A') }}">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </span>
                                    @if($comment->user_id === auth()->id() || auth()->user()->can('delete task comments'))
                                        <form method="POST" action="{{ route('tasks.comments.destroy', [$task, $comment]) }}" onsubmit="return confirm('Delete this comment?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-link p-0 text-danger" style="font-size: .75rem; text-decoration: none;" title="Delete comment">
                                                <i data-lucide="trash-2" style="width: 12px; height: 12px;"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                            <div style="font-size: .84rem; color: var(--text-muted); line-height: 1.6; white-space: pre-line;">
                                {{ $comment->comment }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 2rem 1rem; color: var(--text-muted); font-size: .84rem;">
                        <i data-lucide="message-square" style="width: 32px; height: 32px; display: block; margin: 0 auto .5rem; opacity: .35;"></i>
                        No discussion comments yet. Post an update or note below.
                    </div>
                @endforelse
            </div>

            {{-- Post Comment Form --}}
            <form method="POST" action="{{ route('tasks.comments.store', $task) }}" class="p-3 rounded" style="background: rgba(99,102,241,0.03); border: 1px solid rgba(99,102,241,0.15);">
                @csrf
                <div class="d-flex gap-3">
                    <img src="{{ auth()->user()->avatar_url }}" class="avatar-sm" style="width: 34px; height: 34px; border-radius: 50%; flex-shrink: 0;" alt="{{ auth()->user()->name }}">
                    <div class="flex-grow-1">
                        <label class="form-label-enhanced" style="font-size: .75rem;">Add a note or progress update</label>
                        <textarea name="comment" class="form-control form-control-dark mb-2" rows="2" style="font-size: .84rem;" placeholder="Write a comment or status update..." required></textarea>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn-gradient" style="padding: .4rem 1rem; font-size: .82rem;">
                                <i data-lucide="send" style="width: 13px; height: 13px;"></i> Post Comment
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>

    {{-- Right Sidebar Column (col-lg-4) --}}
    <div class="col-lg-4">

        {{-- Task Metadata Card --}}
        <div class="card-glass mb-4 card-accent-top">
            <div class="section-title d-flex align-items-center gap-2 mb-3">
                <i data-lucide="sliders" style="width: 17px; height: 17px; color: var(--accent);"></i>
                Task Metadata
            </div>

            {{-- Assigned Developer / Member --}}
            <div class="info-row">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="info-label mb-0">Assigned Developer</span>
                    @can('assign tasks')
                    <a href="javascript:void(0)" onclick="toggleAssignForm()" style="font-size: 0.72rem; color: #ff7828; text-decoration: none; font-weight: 600;">
                        <i data-lucide="user-plus" style="width: 11px; height: 11px; display: inline;"></i> Change
                    </a>
                    @endcan
                </div>
                <span class="info-value">
                    @if($task->assignedTo)
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <img src="{{ $task->assignedTo->avatar_url }}" class="avatar-sm" style="width: 30px; height: 30px; border-radius: 50%; border: 1.5px solid #ff7828;" alt="{{ $task->assignedTo->name }}">
                            <div>
                                <div style="font-weight: 650; font-size: 0.86rem; color: var(--text-primary);">{{ $task->assignedTo->name }}</div>
                                <div style="font-size: 0.72rem; color: var(--text-muted); display: flex; align-items: center; gap: 4px;">
                                    <span>{{ $task->assignedTo->designation ?: 'Team Member' }}</span>
                                    <span class="badge" style="background: rgba(99,102,241,0.1); color: var(--accent); font-size: 0.60rem; padding: 1px 5px;">
                                        {{ ucfirst(str_replace('-',' ', $task->assignedTo->getRoleNames()->first() ?? 'User')) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @else
                        <span style="color: var(--text-muted); font-style: italic; font-size: .82rem;">Unassigned</span>
                    @endif
                </span>

                @can('assign tasks')
                <form method="POST" action="{{ route('tasks.assign', $task) }}" id="quickAssignForm" class="d-none mt-2 pt-2 border-top" style="border-color: var(--border) !important;">
                    @csrf @method('PATCH')
                    <label class="form-label-enhanced" style="font-size: 0.70rem;">Select Developer</label>
                    <div class="d-flex gap-2">
                        <select name="assigned_to" class="form-select form-select-dark form-select-sm" style="font-size: 0.78rem; height: 34px;">
                            <option value="">Unassigned</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ $task->assigned_to == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn-gradient btn-sm" style="padding: 0.25rem 0.65rem; font-size: 0.75rem; height: 34px;">Save</button>
                    </div>
                </form>
                @endcan
            </div>

            {{-- Priority --}}
            <div class="info-row">
                <span class="info-label">Priority Level</span>
                <span class="info-value">
                    <span class="badge badge-{{ $task->priority }}" style="font-size: .75rem; padding: 3px 8px;">
                        {{ ucfirst($task->priority) }} Priority
                    </span>
                </span>
            </div>

            {{-- Task Type --}}
            <div class="info-row">
                <span class="info-label">Task Category</span>
                <span class="info-value" style="font-weight: 600; font-size: .82rem; color: var(--text-primary);">
                    <i data-lucide="{{ $task->type_icon }}" style="width: 12px; height: 12px; display: inline; margin-right: 3px; color: var(--accent);"></i>
                    {{ $task->type_label }}
                </span>
            </div>

            {{-- Deadline --}}
            <div class="info-row">
                <span class="info-label">Deadline & Due Date</span>
                <span class="info-value">
                    @if($task->deadline)
                        <div style="font-size: .84rem; font-weight: 650; color: {{ $task->is_overdue ? 'var(--danger)' : 'var(--text-primary)' }};">
                            <i data-lucide="calendar" style="width: 12px; height: 12px; display: inline; margin-right: 3px;"></i>
                            {{ $task->deadline->format('d M Y, h:i A') }}
                        </div>
                        @if($task->status != 'completed')
                            <div style="font-size: .72rem; color: {{ $task->is_overdue ? 'var(--danger)' : 'var(--text-muted)' }}; margin-top: 2px;">
                                {{ $task->deadline->diffForHumans() }}
                            </div>
                        @endif
                    @else
                        <span style="color: var(--text-muted); font-size: .82rem;">No deadline set</span>
                    @endif
                </span>
            </div>

            {{-- Completed Timestamp --}}
            @if($task->completed_at)
                <div class="info-row">
                    <span class="info-label">Completed On</span>
                    <span class="info-value" style="color: #10b981; font-weight: 600; font-size: .82rem;">
                        <i data-lucide="check-circle-2" style="width: 12px; height: 12px; display: inline; margin-right: 3px;"></i>
                        {{ $task->completed_at->format('d M Y, h:i A') }}
                    </span>
                </div>
            @endif

            {{-- Created By --}}
            <div class="info-row">
                <span class="info-label">Created By</span>
                <span class="info-value">
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <img src="{{ $task->createdBy->avatar_url }}" class="avatar-sm" style="width: 22px; height: 22px; border-radius: 50%;" alt="{{ $task->createdBy->name }}">
                        <span style="font-size: .82rem; color: var(--text-primary); font-weight: 550;">{{ $task->createdBy->name }}</span>
                    </div>
                </span>
            </div>

            {{-- Created Date --}}
            <div class="info-row">
                <span class="info-label">Created Date</span>
                <span class="info-value" style="color: var(--text-muted); font-size: .80rem;">
                    {{ $task->created_at->format('d M Y, h:i A') }}
                </span>
            </div>

            {{-- Last Updated --}}
            <div class="info-row" style="border-bottom: none; margin-bottom: 0; padding-bottom: 0;">
                <span class="info-label">Last Updated</span>
                <span class="info-value" style="color: var(--text-muted); font-size: .80rem;">
                    {{ $task->updated_at->diffForHumans() }}
                </span>
            </div>
        </div>

        {{-- Activity & Audit Log Card --}}
        <div class="card-glass">
            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2" style="border-color: var(--border) !important;">
                <div class="section-title mb-0 d-flex align-items-center gap-2">
                    <i data-lucide="activity" style="width: 17px; height: 17px; color: #ff7828;"></i>
                    Activity & Audit Trail ({{ $task->logs->count() }})
                </div>
            </div>

            <div class="activity-scroll-container" style="max-height: 380px; overflow-y: auto; overflow-x: hidden; padding-right: 6px;">
                @forelse($task->logs as $log)
                    @php
                        $icon = match($log->action) {
                            'created'        => 'plus-circle',
                            'status_changed' => 'refresh-cw',
                            'assigned'       => 'user-check',
                            'comment_added'  => 'message-square',
                            default          => 'clock',
                        };
                        $iconColor = match($log->action) {
                            'created'        => '#10b981',
                            'status_changed' => '#ff7828',
                            'assigned'       => '#6366f1',
                            'comment_added'  => '#0ea5e9',
                            default          => '#94a3b8',
                        };
                    @endphp
                    <div class="d-flex gap-3 mb-3 position-relative" style="padding-bottom: .25rem;">
                        <div style="width: 28px; height: 28px; border-radius: 50%; background: {{ $iconColor }}15; border: 1.5px solid {{ $iconColor }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: {{ $iconColor }}; z-index: 1;">
                            <i data-lucide="{{ $icon }}" style="width: 13px; height: 13px;"></i>
                        </div>
                        <div class="flex-grow-1" style="min-width: 0;">
                            <div style="font-size: .80rem; color: var(--text-primary); line-height: 1.35;">
                                <strong>{{ $log->user->name }}</strong>
                                <span style="color: var(--text-muted);"> — {{ $log->description }}</span>
                            </div>
                            <div style="font-size: .70rem; color: var(--text-muted); opacity: .8; margin-top: 2px;">
                                {{ $log->created_at->diffForHumans() }} ({{ $log->created_at->format('d M, h:i A') }})
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="font-size: .82rem; color: var(--text-muted); text-align: center; padding: 1.5rem 0;">
                        <i data-lucide="history" style="width: 24px; height: 24px; opacity: .4; display: block; margin: 0 auto .4rem;"></i>
                        No activity logs recorded yet.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

@endsection

@push('styles')
<style>
.activity-scroll-container::-webkit-scrollbar {
    width: 5px;
}
.activity-scroll-container::-webkit-scrollbar-track {
    background: transparent;
}
.activity-scroll-container::-webkit-scrollbar-thumb {
    background: rgba(148, 163, 184, 0.25);
    border-radius: 4px;
}
.activity-scroll-container::-webkit-scrollbar-thumb:hover {
    background: rgba(148, 163, 184, 0.45);
}
</style>
@endpush

@push('scripts')
<script>
function toggleAssignForm() {
    const form = document.getElementById('quickAssignForm');
    if (form) {
        form.classList.toggle('d-none');
    }
}
</script>
@endpush
