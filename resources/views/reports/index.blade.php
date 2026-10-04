@extends('layouts.app')
@section('title', 'Reports & Analytics')
@section('page-title', 'Business Reports & Performance Analytics')

@section('content')
<style>
    .reports-hero {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.12) 0%, rgba(14, 165, 233, 0.08) 50%, rgba(16, 185, 129, 0.08) 100%);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        backdrop-filter: blur(12px);
    }

    .timeframe-pill {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.35rem 0.8rem;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: var(--bg-card);
        color: var(--text-muted);
        text-decoration: none;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
    }
    .timeframe-pill:hover {
        background: var(--bg-hover);
        color: var(--text-primary);
        border-color: var(--accent);
    }
    .timeframe-pill.active {
        background: var(--accent);
        color: #ffffff;
        border-color: var(--accent);
        box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
    }

    .chart-container-box {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.35rem;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .leaderboard-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 0.85rem 1rem;
        transition: all 0.2s ease;
    }
    .leaderboard-card:hover {
        transform: translateY(-2px);
        border-color: var(--accent);
    }
</style>

{{-- Hero Header --}}
<div class="reports-hero d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge" style="background:rgba(99,102,241,0.2);color:var(--accent);font-weight:700;font-size:0.72rem;letter-spacing:0.04em;">
                ANALYTICS & INSIGHTS
            </span>
            <span class="badge" style="background:rgba(16,185,129,0.15);color:#10b981;font-weight:600;font-size:0.72rem;">
                {{ ucwords(str_replace('_', ' ', $timeframe)) }}
            </span>
        </div>
        <h2 class="mb-1" style="font-size:1.45rem;font-weight:800;color:var(--text-primary);letter-spacing:-0.02em;">
            Reports & Performance Analytics
        </h2>
        <p class="mb-0 text-muted" style="font-size:0.85rem;max-width:650px;">
            Comprehensive operational intelligence, task completion velocity, team productivity rankings, and data export.
        </p>
    </div>

    <div class="d-flex align-items-center gap-2">
        @can('export data')
        <div class="dropdown">
            <button class="btn-gradient dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="padding:0.55rem 1.1rem;font-size:0.82rem;box-shadow:0 4px 14px rgba(99,102,241,0.35);">
                <i data-lucide="download" style="width:15px;height:15px;"></i> Export Data (CSV)
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="min-width: 220px; font-size:0.82rem; border-radius:12px;">
                <li><h6 class="dropdown-header text-uppercase" style="font-size:.65rem;color:var(--text-muted);">Available Datasets</h6></li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('reports.export', array_merge(request()->all(), ['type' => 'tasks'])) }}">
                        <i data-lucide="list-checks" style="width:14px;height:14px;color:var(--accent);"></i>
                        Export Filtered Tasks (.csv)
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('reports.export', ['type' => 'team']) }}">
                        <i data-lucide="users" style="width:14px;height:14px;color:#10b981;"></i>
                        Export Team Productivity (.csv)
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('reports.export', ['type' => 'clients']) }}">
                        <i data-lucide="briefcase" style="width:14px;height:14px;color:#ff7828;"></i>
                        Export Clients Summary (.csv)
                    </a>
                </li>
            </ul>
        </div>
        @endcan
    </div>
</div>

{{-- Timeframe & Filter Bar --}}
<div class="card-glass p-3 mb-4" style="border-radius:14px;">
    {{-- Quick Timeframe Preset Pills --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pb-3 border-bottom mb-3" style="border-color:var(--border) !important;">
        <div class="d-flex align-items-center gap-1 flex-wrap">
            <span class="text-muted me-2" style="font-size:0.75rem;font-weight:700;text-transform:uppercase;">Timeframe:</span>
            <a href="{{ route('reports.index', array_merge(request()->except(['timeframe', 'start_date', 'end_date']), ['timeframe' => 'today'])) }}" class="timeframe-pill {{ $timeframe === 'today' ? 'active' : '' }}">Today</a>
            <a href="{{ route('reports.index', array_merge(request()->except(['timeframe', 'start_date', 'end_date']), ['timeframe' => 'last_7_days'])) }}" class="timeframe-pill {{ $timeframe === 'last_7_days' ? 'active' : '' }}">Last 7 Days</a>
            <a href="{{ route('reports.index', array_merge(request()->except(['timeframe', 'start_date', 'end_date']), ['timeframe' => 'this_month'])) }}" class="timeframe-pill {{ $timeframe === 'this_month' ? 'active' : '' }}">This Month</a>
            <a href="{{ route('reports.index', array_merge(request()->except(['timeframe', 'start_date', 'end_date']), ['timeframe' => 'last_month'])) }}" class="timeframe-pill {{ $timeframe === 'last_month' ? 'active' : '' }}">Last Month</a>
            <a href="{{ route('reports.index', array_merge(request()->except(['timeframe', 'start_date', 'end_date']), ['timeframe' => 'this_year'])) }}" class="timeframe-pill {{ $timeframe === 'this_year' ? 'active' : '' }}">This Year</a>
            <a href="{{ route('reports.index', array_merge(request()->except(['timeframe', 'start_date', 'end_date']), ['timeframe' => 'all_time'])) }}" class="timeframe-pill {{ $timeframe === 'all_time' ? 'active' : '' }}">All Time</a>
        </div>

        <button class="btn btn-sm btn-ghost d-flex align-items-center gap-1" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilters" style="font-size:0.78rem;">
            <i data-lucide="sliders-horizontal" style="width:13px;height:13px;"></i> Custom Filters
        </button>
    </div>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('reports.index') }}" class="collapse {{ request()->hasAny(['client_id', 'assigned_to', 'priority', 'status', 'start_date']) ? 'show' : '' }}" id="advancedFilters">
        <input type="hidden" name="timeframe" value="{{ $timeframe }}">
        <div class="row g-2 align-items-end">
            <div class="col-md-3 col-lg-2">
                <label class="form-label" style="font-size:0.72rem;color:var(--text-muted);font-weight:600;">Custom Start</label>
                <input type="date" name="start_date" class="form-control form-control-dark" style="height:34px;font-size:0.78rem;" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3 col-lg-2">
                <label class="form-label" style="font-size:0.72rem;color:var(--text-muted);font-weight:600;">Custom End</label>
                <input type="date" name="end_date" class="form-control form-control-dark" style="height:34px;font-size:0.78rem;" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-3 col-lg-2">
                <label class="form-label" style="font-size:0.72rem;color:var(--text-muted);font-weight:600;">Client</label>
                <select name="client_id" class="form-select form-control-dark" style="height:34px;font-size:0.78rem;">
                    <option value="">All Clients</option>
                    @foreach($allClients as $c)
                        <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 col-lg-2">
                <label class="form-label" style="font-size:0.72rem;color:var(--text-muted);font-weight:600;">Assignee</label>
                <select name="assigned_to" class="form-select form-control-dark" style="height:34px;font-size:0.78rem;">
                    <option value="">All Members</option>
                    @foreach($allUsers as $u)
                        <option value="{{ $u->id }}" {{ request('assigned_to') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 col-lg-2">
                <label class="form-label" style="font-size:0.72rem;color:var(--text-muted);font-weight:600;">Task Type</label>
                <select name="task_type_id" class="form-select form-control-dark" style="height:34px;font-size:0.78rem;">
                    <option value="">All Types</option>
                    @foreach($allTaskTypes as $tt)
                        <option value="{{ $tt->id }}" {{ (request('task_type_id') == $tt->id || request('type') == $tt->slug || request('type') == $tt->id) ? 'selected' : '' }}>
                            {{ $tt->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 col-lg-2">
                <label class="form-label" style="font-size:0.72rem;color:var(--text-muted);font-weight:600;">Priority</label>
                <select name="priority" class="form-select form-control-dark" style="height:34px;font-size:0.78rem;">
                    <option value="">All Priorities</option>
                    <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                </select>
            </div>
            <div class="col-md-3 col-lg-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary w-100" style="height:34px;font-size:0.78rem;">
                    Apply Filters
                </button>
                <a href="{{ route('reports.index') }}" class="btn btn-sm btn-ghost" style="height:34px;font-size:0.78rem;" title="Reset">
                    Reset
                </a>
            </div>
        </div>
    </form>
</div>

{{-- KPI Metric Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center gap-3" style="border-radius:14px;padding:1.15rem;">
            <div class="stat-icon" style="background: rgba(99, 102, 241, 0.15); color: var(--accent);width:46px;height:46px;border-radius:12px;">
                <i data-lucide="layers" style="width: 22px; height: 22px;"></i>
            </div>
            <div>
                <div class="stat-value" style="font-size:1.45rem;font-weight:800;">{{ number_format($tasksInPeriod) }}</div>
                <div class="stat-label" style="font-size:0.75rem;">Total Tasks in Period</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center gap-3" style="border-radius:14px;padding:1.15rem;">
            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;width:46px;height:46px;border-radius:12px;">
                <i data-lucide="check-circle-2" style="width: 22px; height: 22px;"></i>
            </div>
            <div>
                <div class="stat-value" style="font-size:1.45rem;font-weight:800;color:#10b981;">{{ $completionRate }}%</div>
                <div class="stat-label" style="font-size:0.75rem;">Completion Rate ({{ $completedTasks }}/{{ $tasksInPeriod }})</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center gap-3" style="border-radius:14px;padding:1.15rem;">
            <div class="stat-icon" style="background: rgba(239, 68, 68, 0.15); color: #ef4444;width:46px;height:46px;border-radius:12px;">
                <i data-lucide="clock-alert" style="width: 22px; height: 22px;"></i>
            </div>
            <div>
                <div class="stat-value" style="font-size:1.45rem;font-weight:800;color:{{ $overdueTasks > 0 ? '#ef4444' : 'var(--text-primary)' }};">
                    {{ number_format($overdueTasks) }}
                </div>
                <div class="stat-label" style="font-size:0.75rem;">Overdue Tasks</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center gap-3" style="border-radius:14px;padding:1.15rem;">
            <div class="stat-icon" style="background: rgba(14, 165, 233, 0.15); color: #0ea5e9;width:46px;height:46px;border-radius:12px;">
                <i data-lucide="timer" style="width: 22px; height: 22px;"></i>
            </div>
            <div>
                <div class="stat-value" style="font-size:1.45rem;font-weight:800;">
                    {{ $avgHours > 24 ? round($avgHours / 24, 1) . ' days' : $avgHours . ' hrs' }}
                </div>
                <div class="stat-label" style="font-size:0.75rem;">Avg Resolution Time</div>
            </div>
        </div>
    </div>
</div>

{{-- Charts Row 1: Monthly Trend & Status Distribution --}}
<div class="row g-3 mb-4">
    {{-- Trend Line Chart --}}
    <div class="col-lg-8">
        <div class="chart-container-box">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h5 class="mb-0" style="font-size:0.95rem;font-weight:700;color:var(--text-primary);">
                        <i data-lucide="trending-up" style="width:15px;height:15px;color:var(--accent);display:inline;margin-right:4px;"></i>
                        Task Velocity Trend (Last 6 Months)
                    </h5>
                    <div style="font-size:0.72rem;color:var(--text-muted);">
                        Monthly task creation vs completed task resolution
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3" style="font-size:0.75rem;">
                    <span style="color:var(--accent);font-weight:600;">&bull; Created</span>
                    <span style="color:#10b981;font-weight:600;">&bull; Completed</span>
                </div>
            </div>
            <div style="position: relative; height: 260px; width: 100%;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Status Doughnut Chart --}}
    <div class="col-lg-4">
        <div class="chart-container-box">
            <div class="mb-3">
                <h5 class="mb-0" style="font-size:0.95rem;font-weight:700;color:var(--text-primary);">
                    <i data-lucide="pie-chart" style="width:15px;height:15px;color:#ff7828;display:inline;margin-right:4px;"></i>
                    Task Status Breakdown
                </h5>
                <div style="font-size:0.72rem;color:var(--text-muted);">
                    Current distribution across all workflow stages
                </div>
            </div>
            <div style="position: relative; height: 200px; width: 100%;">
                <canvas id="statusChart"></canvas>
            </div>
            <div class="d-flex justify-content-around mt-3 pt-2 border-top" style="border-color:var(--border) !important;font-size:0.75rem;">
                <div class="text-center">
                    <div style="font-weight:700;color:#10b981;">{{ $completedTasks }}</div>
                    <div class="text-muted" style="font-size:0.68rem;">Completed</div>
                </div>
                <div class="text-center">
                    <div style="font-weight:700;color:var(--accent);">{{ $inProgressTasks }}</div>
                    <div class="text-muted" style="font-size:0.68rem;">In Progress</div>
                </div>
                <div class="text-center">
                    <div style="font-weight:700;color:#f59e0b;">{{ $pendingTasks }}</div>
                    <div class="text-muted" style="font-size:0.68rem;">Pending</div>
                </div>
                <div class="text-center">
                    <div style="font-weight:700;color:#ef4444;">{{ $cancelledTasks }}</div>
                    <div class="text-muted" style="font-size:0.68rem;">Cancelled</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Charts Row 2: Priority Breakdown & Top Clients --}}
<div class="row g-3 mb-4">
    {{-- Priority Distribution Bar Chart --}}
    <div class="col-lg-6">
        <div class="chart-container-box">
            <div class="mb-3">
                <h5 class="mb-0" style="font-size:0.95rem;font-weight:700;color:var(--text-primary);">
                    <i data-lucide="bar-chart-3" style="width:15px;height:15px;color:#ef4444;display:inline;margin-right:4px;"></i>
                    Workload by Priority Level
                </h5>
                <div style="font-size:0.72rem;color:var(--text-muted);">
                    Volume of tasks categorized by urgency
                </div>
            </div>
            <div style="position: relative; height: 220px; width: 100%;">
                <canvas id="priorityChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Top Clients by Volume --}}
    <div class="col-lg-6">
        <div class="chart-container-box">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h5 class="mb-0" style="font-size:0.95rem;font-weight:700;color:var(--text-primary);">
                        <i data-lucide="briefcase" style="width:15px;height:15px;color:var(--accent);display:inline;margin-right:4px;"></i>
                        Top Clients by Task Demand
                    </h5>
                    <div style="font-size:0.72rem;color:var(--text-muted);">
                        Highest task volume client accounts
                    </div>
                </div>
                @can('view clients')
                <a href="{{ route('clients.index') }}" class="btn-ghost" style="font-size:0.75rem;padding:0.25rem 0.6rem;">
                    All Clients &rarr;
                </a>
                @endcan
            </div>

            <div class="d-flex flex-column gap-2">
                @forelse($topClients as $client)
                    <div class="p-2 rounded-3 d-flex align-items-center justify-content-between" style="background:var(--bg-hover);border:1px solid var(--border);">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:32px;height:32px;border-radius:8px;background:rgba(99,102,241,0.12);color:var(--accent);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.8rem;">
                                {{ strtoupper(substr($client->name, 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-size:0.82rem;font-weight:700;color:var(--text-primary);">
                                    {{ $client->name }}
                                </div>
                                <div style="font-size:0.68rem;color:var(--text-muted);">
                                    {{ $client->company_name ?? $client->type_label }}
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge" style="background:rgba(99,102,241,0.15);color:var(--accent);font-size:0.72rem;font-weight:700;">
                                {{ $client->tasks_count }} {{ Str::plural('task', $client->tasks_count) }}
                            </span>
                            @can('view clients')
                            <a href="{{ route('clients.show', $client) }}" class="btn-icon" style="width:24px;height:24px;">
                                <i data-lucide="chevron-right" style="width:12px;height:12px;"></i>
                            </a>
                            @endcan
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted" style="font-size:0.82rem;">
                        No client task data available.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Team Member Productivity Leaderboard --}}
<div class="card-glass p-3 mb-4" style="border-radius:14px;">
    <div class="d-flex align-items-center justify-content-between pb-2 border-bottom mb-3" style="border-color:var(--border) !important;">
        <div>
            <h5 class="mb-0" style="font-size:0.95rem;font-weight:700;color:var(--text-primary);">
                <i data-lucide="award" style="width:16px;height:16px;color:#f59e0b;display:inline;margin-right:4px;"></i>
                Team Member Productivity & Resolution Leaderboard
            </h5>
            <div style="font-size:0.72rem;color:var(--text-muted);">
                Resolution output, active workload, and SLA adherence per team member
            </div>
        </div>
        @can('view users')
        <a href="{{ route('users.index') }}" class="btn-ghost" style="font-size:0.75rem;padding:0.25rem 0.6rem;">
            View Team &rarr;
        </a>
        @endcan
    </div>

    <div class="row g-2">
        @forelse($teamProductivity as $index => $member)
            <div class="col-md-6 col-xl-3">
                <div class="leaderboard-card">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="position-relative">
                            <img src="{{ $member->avatar_url }}" alt="{{ $member->name }}" style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:1.5px solid var(--accent);">
                            @if($index === 0 && $member->completed_tasks > 0)
                                <span class="position-absolute" style="top:-6px;right:-4px;font-size:0.75rem;" title="Top Resolver">🥇</span>
                            @elseif($index === 1 && $member->completed_tasks > 0)
                                <span class="position-absolute" style="top:-6px;right:-4px;font-size:0.75rem;" title="2nd Place">🥈</span>
                            @endif
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="mb-0 text-truncate" style="font-size:0.85rem;font-weight:700;color:var(--text-primary);">
                                    {{ $member->name }}
                                </h6>
                            </div>
                            <div class="text-muted text-truncate" style="font-size:0.68rem;">
                                {{ $member->designation ?? $member->getRoleNames()->first() }}
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between pt-2 border-top" style="border-color:var(--border) !important;font-size:0.72rem;">
                        <div>
                            <span class="text-muted">Completed:</span>
                            <strong style="color:#10b981;">{{ $member->completed_tasks }}</strong>
                        </div>
                        <div>
                            <span class="text-muted">In Progress:</span>
                            <strong style="color:var(--accent);">{{ $member->in_progress_tasks }}</strong>
                        </div>
                        <div>
                            <span class="text-muted">Overdue:</span>
                            <strong style="color:{{ $member->overdue_tasks > 0 ? '#ef4444' : 'var(--text-muted)' }};">{{ $member->overdue_tasks }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-4 text-muted" style="font-size:0.82rem;">
                No team productivity data found for this period.
            </div>
        @endforelse
    </div>
</div>

{{-- Recent Filtered Activity Table --}}
<div class="card-glass p-3" style="border-radius:14px;">
    <div class="d-flex align-items-center justify-content-between pb-2 border-bottom mb-3" style="border-color:var(--border) !important;">
        <div>
            <h5 class="mb-0" style="font-size:0.95rem;font-weight:700;color:var(--text-primary);">
                <i data-lucide="list" style="width:16px;height:16px;color:var(--accent);display:inline;margin-right:4px;"></i>
                Recent Tasks in Selected Period (Sample)
            </h5>
            <div style="font-size:0.72rem;color:var(--text-muted);">
                Displaying latest 10 records matching your analytical scope
            </div>
        </div>
        @can('view tasks')
        <a href="{{ route('tasks.index') }}" class="btn-ghost" style="font-size:0.75rem;padding:0.25rem 0.6rem;">
            Full Tasks List &rarr;
        </a>
        @endcan
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:0.82rem;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border);">
                    <th style="color:var(--text-muted);font-weight:600;">Task</th>
                    <th style="color:var(--text-muted);font-weight:600;">Client</th>
                    <th style="color:var(--text-muted);font-weight:600;">Assignee</th>
                    <th style="color:var(--text-muted);font-weight:600;">Priority</th>
                    <th style="color:var(--text-muted);font-weight:600;">Status</th>
                    <th style="color:var(--text-muted);font-weight:600;">Created</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentFilteredTasks as $t)
                    <tr>
                        <td>
                            <a href="{{ route('tasks.show', $t) }}" style="color:var(--text-primary);font-weight:600;text-decoration:none;">
                                {{ $t->title }}
                            </a>
                        </td>
                        <td>
                            @if($t->client)
                                <span class="badge" style="background:var(--bg-hover);color:var(--text-primary);border:1px solid var(--border);">
                                    {{ $t->client->name }}
                                </span>
                            @else
                                <span class="text-muted" style="font-size:0.75rem;">Internal</span>
                            @endif
                        </td>
                        <td>
                            @if($t->assignedTo)
                                <div class="d-flex align-items-center gap-1">
                                    <img src="{{ $t->assignedTo->avatar_url }}" alt="" style="width:20px;height:20px;border-radius:50%;object-fit:cover;">
                                    <span style="font-size:0.78rem;">{{ $t->assignedTo->name }}</span>
                                </div>
                            @else
                                <span class="text-muted" style="font-size:0.75rem;">Unassigned</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $pColor = match($t->priority) {
                                    'urgent' => '#ef4444',
                                    'high'   => '#f59e0b',
                                    'medium' => '#6366f1',
                                    default  => '#94a3b8'
                                };
                            @endphp
                            <span class="badge" style="background:{{ $pColor }}1a;color:{{ $pColor }};border:1px solid {{ $pColor }}33;font-size:0.68rem;">
                                {{ ucfirst($t->priority) }}
                            </span>
                        </td>
                        <td>
                            @php
                                $sColor = match($t->status) {
                                    'completed'   => '#10b981',
                                    'in_progress' => '#6366f1',
                                    'cancelled'   => '#ef4444',
                                    default       => '#f59e0b'
                                };
                            @endphp
                            <span class="badge" style="background:{{ $sColor }}1a;color:{{ $sColor }};border:1px solid {{ $sColor }}33;font-size:0.68rem;">
                                {{ ucwords(str_replace('_', ' ', $t->status)) }}
                            </span>
                        </td>
                        <td style="color:var(--text-muted);font-size:0.75rem;">
                            {{ $t->created_at ? $t->created_at->format('M d, Y') : '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            No tasks found in this timeframe.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Initialize Chart.js Graphs --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isDark = document.documentElement.getAttribute('data-theme') !== 'light';
        const gridColor = isDark ? 'rgba(148, 163, 184, 0.1)' : 'rgba(0, 0, 0, 0.06)';
        const textColor = isDark ? '#94a3b8' : '#64748b';

        // 1. Monthly Velocity Trend Chart
        const trendCtx = document.getElementById('trendChart');
        if (trendCtx) {
            const monthlyData = @json($monthlyTrend);
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: monthlyData.map(d => d.month),
                    datasets: [
                        {
                            label: 'Created',
                            data: monthlyData.map(d => d.created),
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(99, 102, 241, 0.1)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2,
                            pointRadius: 4,
                            pointBackgroundColor: '#6366f1'
                        },
                        {
                            label: 'Completed',
                            data: monthlyData.map(d => d.completed),
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2,
                            pointRadius: 4,
                            pointBackgroundColor: '#10b981'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            grid: { color: gridColor },
                            ticks: { color: textColor, font: { size: 11 } }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: gridColor },
                            ticks: { color: textColor, font: { size: 11 }, precision: 0 }
                        }
                    }
                }
            });
        }

        // 2. Status Doughnut Chart
        const statusCtx = document.getElementById('statusChart');
        if (statusCtx) {
            const statusData = @json(array_values($statusCounts));
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Completed', 'In Progress', 'Pending', 'Cancelled'],
                    datasets: [{
                        data: statusData,
                        backgroundColor: ['#10b981', '#6366f1', '#f59e0b', '#ef4444'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    cutout: '68%'
                }
            });
        }

        // 3. Priority Bar Chart
        const priorityCtx = document.getElementById('priorityChart');
        if (priorityCtx) {
            const priorityData = @json(array_values($priorityCounts));
            new Chart(priorityCtx, {
                type: 'bar',
                data: {
                    labels: ['Urgent', 'High', 'Medium', 'Low'],
                    datasets: [{
                        label: 'Tasks',
                        data: priorityData,
                        backgroundColor: ['#ef4444', '#f59e0b', '#6366f1', '#94a3b8'],
                        borderRadius: 6,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: textColor, font: { size: 11 } }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: gridColor },
                            ticks: { color: textColor, font: { size: 11 }, precision: 0 }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
