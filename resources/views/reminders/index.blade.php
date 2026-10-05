@extends('layouts.app')
@section('title', 'Reminders')
@section('page-title', 'Reminders')

@section('content')
{{-- Page Header --}}
<div class="page-header">
    <div class="page-header-left">
        <h2>Reminders</h2>
        <p>Stay on top of renewals, deadlines & follow-ups</p>
    </div>
    <div class="page-header-actions">
        @can('create reminders')
        <a href="{{ route('reminders.create') }}" class="btn-gradient">
            <i data-lucide="bell-plus" style="width:15px;height:15px;"></i> New Reminder
        </a>
        @endcan
    </div>
</div>

{{-- Quick Status Tabs --}}
<div class="d-flex flex-wrap align-items-center gap-2 mb-3">
    <a href="{{ route('reminders.index', array_merge(request()->except(['status', 'page']))) }}"
       class="btn-ghost {{ !request('status') ? 'border-primary text-primary fw-bold' : '' }}"
       style="padding: .4rem .9rem; font-size: .82rem; border-radius: 20px; {{ !request('status') ? 'background: rgba(99,102,241,0.1);' : '' }}">
        All <span class="badge ms-1" style="background: rgba(148,163,184,0.2); color: inherit;">{{ $totalCount ?? $reminders->total() }}</span>
    </a>
    <a href="{{ route('reminders.index', array_merge(request()->except('page'), ['status' => 'pending'])) }}"
       class="btn-ghost {{ request('status') === 'pending' ? 'border-warning text-warning fw-bold' : '' }}"
       style="padding: .4rem .9rem; font-size: .82rem; border-radius: 20px; {{ request('status') === 'pending' ? 'background: rgba(245,158,11,0.12);' : '' }}">
        ⏳ Pending <span class="badge ms-1" style="background: rgba(245,158,11,0.2); color: #f59e0b;">{{ $pendingCount ?? 0 }}</span>
    </a>
    <a href="{{ route('reminders.index', array_merge(request()->except('page'), ['status' => 'overdue'])) }}"
       class="btn-ghost {{ request('status') === 'overdue' ? 'border-danger text-danger fw-bold' : '' }}"
       style="padding: .4rem .9rem; font-size: .82rem; border-radius: 20px; {{ request('status') === 'overdue' ? 'background: rgba(239,68,68,0.12);' : '' }}">
        ⚠️ Overdue <span class="badge ms-1" style="background: rgba(239,68,68,0.2); color: #ef4444;">{{ $overdueCount ?? 0 }}</span>
    </a>
    <a href="{{ route('reminders.index', array_merge(request()->except('page'), ['status' => 'sent'])) }}"
       class="btn-ghost {{ request('status') === 'sent' ? 'border-success text-success fw-bold' : '' }}"
       style="padding: .4rem .9rem; font-size: .82rem; border-radius: 20px; {{ request('status') === 'sent' ? 'background: rgba(16,185,129,0.12);' : '' }}">
        ✓ Completed <span class="badge ms-1" style="background: rgba(16,185,129,0.2); color: #10b981;">{{ $doneCount ?? 0 }}</span>
    </a>
</div>

{{-- Filter Card --}}
<div class="card-glass mb-4">
    <form method="GET" action="{{ route('reminders.index') }}" id="reminderFilterForm">
        {{-- Preserve Status if clicked from tabs unless changed in dropdown --}}
        @if(request('status') && !request()->has('status_select'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif

        <div class="row g-2 align-items-end">
            {{-- Search --}}
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label-enhanced">Search</label>
                <div style="position:relative;">
                    <i data-lucide="search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:var(--text-muted);"></i>
                    <input type="text" name="search" class="form-control form-control-dark" style="padding-left:32px; height:38px; font-size:.85rem;" placeholder="Title, desc, client..." value="{{ request('search') }}">
                </div>
            </div>

            {{-- Type --}}
            <div class="col-6 col-sm-6 col-lg-2">
                <label class="form-label-enhanced">Type</label>
                <select name="type" class="form-select form-select-dark" style="height:38px; font-size:.85rem;">
                    <option value="">All Types</option>
                    <option value="meeting" {{ request('type')=='meeting'?'selected':'' }}>🤝 Meeting</option>
                    <option value="domain_expiry" {{ request('type')=='domain_expiry'?'selected':'' }}>🌐 Domain Expiry</option>
                    <option value="hosting_renewal" {{ request('type')=='hosting_renewal'?'selected':'' }}>🖥️ Hosting Renewal</option>
                    <option value="task_deadline" {{ request('type')=='task_deadline'?'selected':'' }}>📋 Task Deadline</option>
                    <option value="client_followup" {{ request('type')=='client_followup'?'selected':'' }}>📞 Client Follow-up</option>
                    <option value="system_update" {{ request('type')=='system_update'?'selected':'' }}>🔄 System Update</option>
                    <option value="custom" {{ request('type')=='custom'?'selected':'' }}>📝 Custom</option>
                </select>
            </div>

            {{-- Client --}}
            <div class="col-6 col-sm-6 col-lg-2">
                <label class="form-label-enhanced">Client</label>
                <select name="client_id" class="form-select form-select-dark" style="height:38px; font-size:.85rem;">
                    <option value="">All Clients</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Timeframe / Due --}}
            <div class="col-6 col-sm-6 col-lg-2">
                <label class="form-label-enhanced">Due Time</label>
                <select name="timeframe" class="form-select form-select-dark" style="height:38px; font-size:.85rem;">
                    <option value="">Any Time</option>
                    <option value="today" {{ request('timeframe')=='today'?'selected':'' }}>Due Today</option>
                    <option value="week" {{ request('timeframe')=='week'?'selected':'' }}>This Week</option>
                    <option value="month" {{ request('timeframe')=='month'?'selected':'' }}>This Month</option>
                </select>
            </div>

            {{-- Actions --}}
            <div class="col-12 col-sm-12 col-lg-3">
                <label class="form-label-enhanced d-none d-lg-block">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn-gradient flex-fill justify-content-center" style="height:38px; padding:.45rem .9rem; font-size:.85rem;">
                        <i data-lucide="filter" style="width:14px;height:14px;"></i> Apply Filter
                    </button>
                    @if(request()->hasAny(['search', 'type', 'client_id', 'status', 'timeframe', 'date_from', 'date_to']))
                        <a href="{{ route('reminders.index') }}" class="btn-ghost" style="height:38px; padding:.45rem .85rem;" title="Clear All Filters">
                            <i data-lucide="rotate-ccw" style="width:14px;height:14px;"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </form>

    {{-- Active Filter Tags --}}
    @if(request()->hasAny(['search', 'type', 'client_id', 'status', 'timeframe']))
        <div class="d-flex flex-wrap align-items-center gap-2 pt-3 mt-3 border-top" style="border-color: var(--border) !important;">
            <span style="font-size: .75rem; color: var(--text-muted); font-weight: 600;">Active Filters:</span>

            @if(request('search'))
                <a href="{{ route('reminders.index', request()->except('search')) }}" class="badge" style="background: var(--bg-hover); color: var(--text-primary); border: 1px solid var(--border); text-decoration: none; padding: 4px 8px; font-size: .72rem;">
                    Search: "{{ request('search') }}" <i data-lucide="x" style="width:11px;height:11px;display:inline;margin-left:3px;"></i>
                </a>
            @endif

            @if(request('type'))
                <a href="{{ route('reminders.index', request()->except('type')) }}" class="badge" style="background: var(--bg-hover); color: var(--text-primary); border: 1px solid var(--border); text-decoration: none; padding: 4px 8px; font-size: .72rem;">
                    Type: {{ ucfirst(str_replace('_', ' ', request('type'))) }} <i data-lucide="x" style="width:11px;height:11px;display:inline;margin-left:3px;"></i>
                </a>
            @endif

            @if(request('client_id'))
                @php $selectedClient = $clients->firstWhere('id', request('client_id')); @endphp
                @if($selectedClient)
                    <a href="{{ route('reminders.index', request()->except('client_id')) }}" class="badge" style="background: var(--bg-hover); color: var(--text-primary); border: 1px solid var(--border); text-decoration: none; padding: 4px 8px; font-size: .72rem;">
                        Client: {{ $selectedClient->name }} <i data-lucide="x" style="width:11px;height:11px;display:inline;margin-left:3px;"></i>
                    </a>
                @endif
            @endif

            @if(request('status'))
                <a href="{{ route('reminders.index', request()->except('status')) }}" class="badge" style="background: var(--bg-hover); color: var(--text-primary); border: 1px solid var(--border); text-decoration: none; padding: 4px 8px; font-size: .72rem;">
                    Status: {{ ucfirst(request('status')) }} <i data-lucide="x" style="width:11px;height:11px;display:inline;margin-left:3px;"></i>
                </a>
            @endif

            @if(request('timeframe'))
                <a href="{{ route('reminders.index', request()->except('timeframe')) }}" class="badge" style="background: var(--bg-hover); color: var(--text-primary); border: 1px solid var(--border); text-decoration: none; padding: 4px 8px; font-size: .72rem;">
                    Time: {{ ucfirst(request('timeframe')) }} <i data-lucide="x" style="width:11px;height:11px;display:inline;margin-left:3px;"></i>
                </a>
            @endif

            <a href="{{ route('reminders.index') }}" style="font-size: .72rem; color: #ff7828; text-decoration: underline; margin-left: auto;">
                Reset All
            </a>
        </div>
    @endif
</div>

{{-- Results Bar --}}
<div class="d-flex align-items-center justify-content-between mb-3">
    <div style="font-size: .82rem; color: var(--text-muted); font-weight: 500;">
        Showing <strong style="color: var(--text-primary);">{{ $reminders->firstItem() ?? 0 }}–{{ $reminders->lastItem() ?? 0 }}</strong> of <strong style="color: var(--text-primary);">{{ $reminders->total() }}</strong> reminders
    </div>
</div>

{{-- Reminder Cards --}}
<div class="row g-3">
    @forelse($reminders as $reminder)
    @php
        $isOverdue = $reminder->is_overdue;
        $isDone = $reminder->is_done;
        $colors = [
            'meeting'          => '#ec4899',
            'domain_expiry'    => '#6366f1',
            'hosting_renewal'  => '#0ea5e9',
            'task_deadline'    => '#f59e0b',
            'client_followup'  => '#10b981',
            'system_update'    => '#8b5cf6',
            'custom'           => '#ff7828'
        ];
        $iconColor = $colors[$reminder->type] ?? '#ff7828';
        $cardBorder = $isDone ? 'rgba(16,185,129,.3)' : ($isOverdue ? 'rgba(239,68,68,.35)' : 'var(--border)');
        $cardBg = $isDone ? 'rgba(16,185,129,.02)' : ($isOverdue ? 'rgba(239,68,68,.03)' : 'var(--bg-card)');
    @endphp
    <div class="col-12 col-md-6 col-xl-4">
        <div class="card-glass h-100 d-flex flex-column" style="border-color:{{ $cardBorder }}; background:{{ $cardBg }}; transition: all .2s;">
            {{-- Header --}}
            <div class="d-flex align-items-start gap-3 mb-2">
                <div style="width:42px; height:42px; background:{{ $isOverdue ? 'rgba(239,68,68,.12)' : ($isDone ? 'rgba(16,185,129,.12)' : 'rgba(255,120,40,.1)') }}; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i data-lucide="{{ $reminder->type_icon }}" style="width:18px; height:18px; color:{{ $isOverdue ? '#ef4444' : ($isDone ? '#10b981' : $iconColor) }};"></i>
                </div>
                <div style="flex:1; min-width:0;">
                    <h5 style="font-weight:700; font-size:.92rem; color:var(--text-primary); margin-bottom:.25rem; line-height:1.3; word-break:break-word;">
                        <a href="{{ route('reminders.show', $reminder) }}" style="color:inherit; text-decoration:none;">
                            {{ $reminder->title }}
                        </a>
                    </h5>
                    <span class="badge" style="background:rgba(99,102,241,.1); color:var(--accent); font-size:.65rem; font-weight:600;">
                        {{ $reminder->type_label }}
                    </span>
                </div>
                @if($isDone)
                    <span class="badge" style="background:rgba(16,185,129,.15); color:#10b981; flex-shrink:0; font-weight:600; padding:4px 8px; border-radius:6px;">
                        ✓ Done
                    </span>
                @elseif($isOverdue)
                    <span class="badge" style="background:rgba(239,68,68,.15); color:#ef4444; flex-shrink:0; font-weight:700; padding:4px 8px; border-radius:6px; animation: pulse 2s infinite;">
                        Overdue
                    </span>
                @else
                    <span class="badge" style="background:rgba(245,158,11,.15); color:#f59e0b; flex-shrink:0; font-weight:600; padding:4px 8px; border-radius:6px;">
                        Pending
                    </span>
                @endif
            </div>

            {{-- Description --}}
            @if($reminder->description)
                <p style="font-size:.82rem; color:var(--text-muted); margin-bottom:.85rem; line-height:1.55; flex-grow:1;">
                    {{ Str::limit($reminder->description, 100) }}
                </p>
            @else
                <div style="flex-grow:1;"></div>
            @endif

            {{-- Metadata Info --}}
            <div class="mb-3 pt-2 border-top" style="border-color: var(--border) !important;">
                @if($reminder->client)
                    <div style="font-size:.78rem; color:var(--text-muted); margin-bottom:.4rem; display:flex; align-items:center; gap:6px;">
                        <i data-lucide="user" style="width:13px; height:13px; color:var(--accent); flex-shrink:0;"></i>
                        <span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                            Client: <strong style="color:var(--text-primary);">{{ $reminder->client->name }}</strong>
                        </span>
                    </div>
                @endif

                @if($reminder->task)
                    <div style="font-size:.78rem; color:var(--text-muted); margin-bottom:.4rem; display:flex; align-items:center; gap:6px;">
                        <i data-lucide="check-square" style="width:13px; height:13px; color:#0ea5e9; flex-shrink:0;"></i>
                        <span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                            Task: <strong style="color:var(--text-primary);">{{ $reminder->task->title }}</strong>
                        </span>
                    </div>
                @endif

                <div style="font-size:.78rem; display:flex; align-items:center; gap:6px; color:{{ $isOverdue ? '#ef4444' : 'var(--text-muted)' }};">
                    <i data-lucide="calendar" style="width:13px; height:13px; flex-shrink:0;"></i>
                    <span>
                        {{ $reminder->remind_at->format('d M Y, h:i A') }}
                        <strong style="opacity:.85; font-size:.72rem;">({{ $reminder->remind_at->diffForHumans() }})</strong>
                    </span>
                </div>
            </div>

            {{-- Card Actions --}}
            <div class="d-flex gap-2 align-items-center mt-auto">
                @if(!$isDone)
                    @can('mark reminders sent')
                    <form method="POST" action="{{ route('reminders.mark-done', $reminder) }}" class="flex-grow-1">
                        @csrf @method('PATCH')
                        <button type="submit" class="w-100" style="background:rgba(16,185,129,.08); color:var(--success); border:1px solid rgba(16,185,129,.25); border-radius:8px; font-size:.8rem; font-weight:600; padding:.45rem .6rem; cursor:pointer; font-family:inherit; transition:all .2s; display:inline-flex; align-items:center; justify-content:center; gap:4px;" onmouseover="this.style.background='rgba(16,185,129,.16)'" onmouseout="this.style.background='rgba(16,185,129,.08)'">
                            <i data-lucide="check" style="width:13px; height:13px;"></i> Mark Done
                        </button>
                    </form>
                    @endcan
                @else
                    <form method="POST" action="{{ route('reminders.mark-pending', $reminder) }}" class="flex-grow-1">
                        @csrf @method('PATCH')
                        <button type="submit" class="w-100" style="background:rgba(148,163,184,.08); color:var(--text-muted); border:1px solid rgba(148,163,184,.2); border-radius:8px; font-size:.78rem; font-weight:500; padding:.45rem .6rem; cursor:pointer; font-family:inherit; transition:all .2s; display:inline-flex; align-items:center; justify-content:center; gap:4px;" title="Reopen reminder as pending">
                            <i data-lucide="rotate-ccw" style="width:12px; height:12px;"></i> Reopen
                        </button>
                    </form>
                @endif

                @can('edit reminders')
                <a href="{{ route('reminders.edit', $reminder) }}" class="btn-icon" style="width:36px; height:36px; flex-shrink:0;" title="Edit Reminder">
                    <i data-lucide="pencil" style="width:14px; height:14px;"></i>
                </a>
                @endcan

                @can('delete reminders')
                <form method="POST" action="{{ route('reminders.destroy', $reminder) }}" onsubmit="return confirm('Are you sure you want to delete this reminder?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-icon" style="width:36px; height:36px; color:#ef4444; border-color:rgba(239,68,68,.25);" title="Delete Reminder">
                        <i data-lucide="trash-2" style="width:14px; height:14px;"></i>
                    </button>
                </form>
                @endcan
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card-glass text-center py-5">
            <div style="width:56px; height:56px; background:rgba(255,120,40,.1); border-radius:50%; display:inline-flex; align-items:center; justify-content:center; margin-bottom:1rem;">
                <i data-lucide="bell-off" style="width:26px; height:26px; color:#ff7828;"></i>
            </div>
            <h4 style="font-weight:700; font-size:1.1rem; color:var(--text-primary); margin-bottom:.35rem;">No Reminders Found</h4>
            <p style="font-size:.85rem; color:var(--text-muted); max-width:400px; margin:0 auto 1.25rem;">
                @if(request()->hasAny(['search', 'type', 'client_id', 'status', 'timeframe']))
                    No reminders match your active filter criteria. Try resetting filters.
                @else
                    Stay organized by scheduling reminders for domain renewals, task deadlines, and client follow-ups.
                @endif
            </p>
            <div class="d-flex justify-content-center gap-2">
                @if(request()->hasAny(['search', 'type', 'client_id', 'status', 'timeframe']))
                    <a href="{{ route('reminders.index') }}" class="btn-ghost" style="padding:.5rem 1rem;">
                        <i data-lucide="rotate-ccw" style="width:14px; height:14px;"></i> Clear Filters
                    </a>
                @endif
                <a href="{{ route('reminders.create') }}" class="btn-gradient" style="padding:.5rem 1.1rem;">
                    <i data-lucide="bell-plus" style="width:14px; height:14px;"></i> Create Reminder
                </a>
            </div>
        </div>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($reminders->hasPages())
    <div class="mt-4">
        {{ $reminders->links() }}
    </div>
@endif

@endsection
