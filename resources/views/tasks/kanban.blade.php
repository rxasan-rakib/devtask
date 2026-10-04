@extends('layouts.app')
@section('title', 'Kanban Board')
@section('page-title', 'Kanban Board')

@push('styles')
<style>
/* ── Container & Global Wrap: strictly fit inside page body without horizontal scrolling ── */
.page-content {
    max-width: 100%;
    overflow-x: hidden;
}
@media (max-width: 767.98px) {
    .page-content {
        padding: 1rem 0.75rem !important;
    }
}

.kanban-main-wrapper {
    width: 100%;
    max-width: 100%;
    overflow-x: hidden;
}

/* ── Kanban Controls & Search Bar ── */
.kanban-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    margin-bottom: 1.15rem;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 0.75rem 1rem;
    width: 100%;
}
.kanban-search-box {
    position: relative;
    min-width: 180px;
    flex: 1 1 220px;
}
.kanban-search-box input {
    width: 100%;
    padding-left: 2.2rem;
    height: 36px;
    font-size: 0.82rem;
}
.kanban-search-box .search-icon {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    width: 14px;
    height: 14px;
    color: var(--text-muted);
    pointer-events: none;
}
.kanban-filter-pills {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    flex-wrap: wrap;
}
.priority-pill {
    padding: 0.3rem 0.65rem;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 500;
    border: 1px solid var(--border);
    background: var(--bg-secondary);
    color: var(--text-muted);
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
    user-select: none;
}
.priority-pill:hover {
    color: var(--text-primary);
    border-color: var(--accent);
}
.priority-pill.active {
    background: var(--accent);
    color: #fff !important;
    border-color: var(--accent);
    box-shadow: 0 2px 8px rgba(99,102,241,0.35);
}

/* ── View Switcher & Status Navigation Tabs (For Mobile & Tablet) ── */
.kanban-status-nav {
    display: flex;
    gap: 0.4rem;
    flex-wrap: wrap;
    margin-bottom: 1.15rem;
    width: 100%;
}
.status-nav-item {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.45rem 0.8rem;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 9px;
    font-size: 0.78rem;
    font-weight: 600;
    color: var(--text-muted);
    cursor: pointer;
    transition: all 0.2s ease;
    user-select: none;
}
.status-nav-item:hover {
    border-color: var(--accent);
    color: var(--text-primary);
}
.status-nav-item.active {
    background: var(--accent-light);
    border-color: var(--accent);
    color: var(--accent);
    box-shadow: 0 2px 8px rgba(99,102,241,0.2);
}
.status-nav-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
}
.status-nav-badge {
    padding: 0.12rem 0.4rem;
    border-radius: 20px;
    font-size: 0.68rem;
    font-weight: 700;
    background: var(--bg-hover);
    color: var(--text-primary);
}
.status-nav-item.active .status-nav-badge {
    background: var(--accent);
    color: #fff;
}

/* ── Kanban Grid: Responsive within page body with NO horizontal scrolling ── */
.kanban-grid {
    display: grid;
    gap: 0.85rem;
    width: 100%;
    align-items: start;
}

/* Desktop (≥ 1024px): 5 equal columns side by side taking 100% width */
@media (min-width: 1024px) {
    .kanban-grid {
        grid-template-columns: repeat(5, minmax(0, 1fr));
    }
}

/* Tablet (768px to 1023px): 2 or 3 responsive columns that wrap down cleanly */
@media (min-width: 768px) and (max-width: 1023.98px) {
    .kanban-grid {
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
    }
}

/* Mobile (< 768px): Columns take full width (100%) and switch cleanly by Tab or Stack */
@media (max-width: 767.98px) {
    .kanban-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    .page-header {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 0.75rem;
    }
    .page-header-actions {
        width: 100%;
        display: flex;
    }
    .page-header-actions .btn-ghost,
    .page-header-actions .btn-gradient {
        flex: 1;
        justify-content: center;
        font-size: 0.8rem;
        padding: 0.45rem 0.6rem;
    }
}

@keyframes kanbanFadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* ── Kanban Column Box ── */
.kanban-col {
    display: flex;
    flex-direction: column;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 0.75rem;
    min-width: 0 !important;
    max-width: 100% !important;
    width: 100%;
    transition: box-shadow 0.2s, border-color 0.2s;
    animation: kanbanFadeIn 0.2s ease;
    box-shadow: var(--shadow-card);
}
.kanban-col.hidden-by-tab {
    display: none !important;
}

/* Column Header */
.kanban-header {
    padding: 0.6rem 0.75rem;
    border-radius: 8px;
    font-size: 0.74rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border: 1px solid transparent;
}
.kanban-header-left {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.kanban-header-left i {
    width: 14px;
    height: 14px;
    flex-shrink: 0;
}
.kanban-header-left span {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.kanban-count {
    padding: 0.15rem 0.5rem;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 700;
    flex-shrink: 0;
}

/* Column Body / Dropzone */
.kanban-body {
    flex: 1;
    min-height: 100px;
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
    border-radius: 8px;
    transition: background-color 0.2s ease, border-color 0.2s ease;
}
.kanban-body.drag-over {
    background: var(--accent-light);
    border: 2px dashed var(--accent);
    border-radius: 8px;
}

/* ── Card Styling (Compact & Clean) ── */
.kanban-card {
    background: var(--bg-secondary);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 0.75rem;
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    position: relative;
    overflow: hidden;
    cursor: grab;
    user-select: none;
    width: 100%;
}
.kanban-card:active {
    cursor: grabbing;
}
.kanban-card.is-dragging {
    opacity: 0.35;
    transform: scale(0.97);
    border: 2px dashed var(--accent);
}
.kanban-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2.5px;
    background: linear-gradient(90deg, var(--accent), #8b5cf6);
    opacity: 0;
    transition: opacity 0.2s ease;
}
.kanban-card:hover {
    border-color: rgba(99,102,241,0.45);
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
}
.kanban-card:hover::before {
    opacity: 1;
}

/* Card Header Meta */
.card-header-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.4rem;
    margin-bottom: 0.45rem;
}
.card-task-type {
    font-size: 0.64rem;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.card-task-title {
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--text-primary);
    line-height: 1.35;
    margin-bottom: 0.45rem;
    word-break: break-word;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.card-client-tag {
    font-size: 0.7rem;
    color: var(--text-muted);
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 4px;
    background: var(--bg-hover);
    padding: 0.2rem 0.45rem;
    border-radius: 5px;
    width: fit-content;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.card-footer-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.4rem;
    margin-top: 0.5rem;
    padding-top: 0.45rem;
    border-top: 1px solid var(--border);
}
.card-deadline {
    font-size: 0.68rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 3px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.card-assignee-avatar {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    object-fit: cover;
    border: 1.5px solid var(--accent);
    flex-shrink: 0;
}
.card-assignee-initials {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--accent), #8b5cf6);
    color: #fff;
    font-size: 0.62rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1.5px solid var(--border);
    flex-shrink: 0;
}

/* Empty Column State */
.kanban-empty {
    text-align: center;
    padding: 1.75rem 0.75rem;
    color: var(--text-muted);
    font-size: 0.76rem;
    border: 1.5px dashed var(--border);
    border-radius: 10px;
    background: rgba(148, 163, 184, 0.03);
}

/* Card Action Button (Quick Shift) */
.card-shift-dropdown {
    opacity: 0.75;
    transition: opacity 0.2s ease;
}
.kanban-card:hover .card-shift-dropdown {
    opacity: 1;
}

/* ── Toast Notification ── */
.kanban-toast {
    position: fixed;
    bottom: 1.5rem;
    right: 1.5rem;
    background: var(--bg-card);
    border: 1px solid var(--accent);
    color: var(--text-primary);
    padding: 0.75rem 1.25rem;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.25);
    display: flex;
    align-items: center;
    gap: 0.6rem;
    font-size: 0.85rem;
    font-weight: 500;
    z-index: 1060;
    opacity: 0;
    visibility: hidden;
    transform: translateY(12px);
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    pointer-events: none;
}
.kanban-toast.show {
    visibility: visible;
    transform: translateY(0);
    opacity: 1;
}
</style>
@endpush

@section('content')
<div class="kanban-main-wrapper">

    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-left">
            <div class="d-flex align-items-center gap-2">
                <h2>Kanban Board</h2>
                @php
                    $totalTasks = 0;
                    foreach($kanban as $taskList) { $totalTasks += $taskList->count(); }
                @endphp
                <span class="badge" style="background:var(--accent-light);color:var(--accent);font-size:0.75rem;padding:0.35rem 0.65rem;border-radius:20px;">
                    {{ $totalTasks }} {{ Str::plural('Task', $totalTasks) }}
                </span>
            </div>
            <p>Visual overview of all tasks organized by status</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('tasks.index') }}" class="btn-ghost">
                <i data-lucide="list" style="width:14px;height:14px;"></i> List View
            </a>
            <a href="{{ route('tasks.create') }}" class="btn-gradient">
                <i data-lucide="plus" style="width:14px;height:14px;"></i> New Task
            </a>
        </div>
    </div>

    {{-- Interactive Filter & Quick Search Bar --}}
    <div class="kanban-toolbar">
        <div class="kanban-search-box">
            <i data-lucide="search" class="search-icon"></i>
            <input type="text" id="kanbanSearchInput" class="form-control form-control-dark" placeholder="Search by title, client, or type...">
        </div>
        <div class="kanban-filter-pills">
            <span class="priority-pill active" data-priority="all">All Priorities</span>
            <span class="priority-pill" data-priority="high">🔴 High</span>
            <span class="priority-pill" data-priority="medium">🟡 Medium</span>
            <span class="priority-pill" data-priority="low">🟢 Low</span>
        </div>
    </div>

    {{-- Status Navigation Tabs (Filter/Toggle view directly in page body) --}}
    @php
        $cols = [
            'pending'      => ['label'=>'Pending',      'icon'=>'clock',        'color'=>'rgba(148,163,184,.12)', 'tc'=>'#94a3b8', 'border'=>'rgba(148,163,184,.25)', 'badge_bg'=>'rgba(148,163,184,.2)'],
            'in_progress'  => ['label'=>'In Progress',  'icon'=>'play-circle',  'color'=>'rgba(99,102,241,.12)',  'tc'=>'#6366f1', 'border'=>'rgba(99,102,241,.3)',  'badge_bg'=>'rgba(99,102,241,.25)'],
            'on_hold'      => ['label'=>'On Hold',      'icon'=>'pause-circle', 'color'=>'rgba(245,158,11,.12)',  'tc'=>'#f59e0b', 'border'=>'rgba(245,158,11,.3)',  'badge_bg'=>'rgba(245,158,11,.25)'],
            'under_review' => ['label'=>'Under Review', 'icon'=>'eye',          'color'=>'rgba(14,165,233,.12)',  'tc'=>'#0ea5e9', 'border'=>'rgba(14,165,233,.3)',  'badge_bg'=>'rgba(14,165,233,.25)'],
            'completed'    => ['label'=>'Completed',    'icon'=>'check-circle', 'color'=>'rgba(16,185,129,.12)',  'tc'=>'#10b981', 'border'=>'rgba(16,185,129,.3)',  'badge_bg'=>'rgba(16,185,129,.25)'],
        ];
    @endphp

    <div class="kanban-status-nav" id="kanbanStatusNav">
        <div class="status-nav-item active" data-tab-status="all" onclick="filterByStatusTab('all')">
            <i data-lucide="layout-grid" style="width:13px;height:13px;"></i>
            <span>All Columns</span>
            <span class="status-nav-badge" id="nav-count-all">{{ $totalTasks }}</span>
        </div>
        @foreach($cols as $status => $col)
        <div class="status-nav-item" data-tab-status="{{ $status }}" onclick="filterByStatusTab('{{ $status }}')">
            <span class="status-nav-dot" style="background:{{ $col['tc'] }};"></span>
            <span>{{ $col['label'] }}</span>
            <span class="status-nav-badge" id="nav-count-{{ $status }}">{{ $kanban[$status]->count() }}</span>
        </div>
        @endforeach
    </div>

    {{-- Kanban Columns Grid (Fits 100% inside page body width, NO left/right scrolling) --}}
    <div class="kanban-grid" id="kanbanGrid">
        @foreach($cols as $status => $col)
        <div class="kanban-col" id="col-wrapper-{{ $status }}" data-status="{{ $status }}">
            
            {{-- Column Header --}}
            <div class="kanban-header" style="background:{{ $col['color'] }};color:{{ $col['tc'] }};border-color:{{ $col['border'] }};">
                <div class="kanban-header-left">
                    <i data-lucide="{{ $col['icon'] }}"></i>
                    <span>{{ $col['label'] }}</span>
                </div>
                <span class="kanban-count" id="header-count-{{ $status }}" style="background:{{ $col['badge_bg'] }};color:{{ $col['tc'] }};">
                    {{ $kanban[$status]->count() }}
                </span>
            </div>

            {{-- Column Body / Dropzone --}}
            <div class="kanban-body" id="col-body-{{ $status }}" data-status="{{ $status }}" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)" ondrop="handleDrop(event, '{{ $status }}')">
                @forelse($kanban[$status] as $task)
                @php
                    $isSuperAdmin = auth()->user()->hasRole('super-admin') || auth()->user()->isDefaultSuperAdmin();
                    $canChangeThisStatus = auth()->user()->can('change task status') && ($isSuperAdmin || (int)$task->assigned_to === (int)auth()->id());
                @endphp
                <div class="kanban-card-wrapper" data-task-id="{{ $task->id }}" data-current-status="{{ $status }}" data-priority="{{ $task->priority }}" data-title="{{ strtolower($task->title) }}" data-client="{{ strtolower(($task->client->company_name ?? '') . ' ' . ($task->client->name ?? '') . ' ' . ($task->client->system_url ?? '')) }}" data-type="{{ strtolower($task->type_label . ' ' . ($task->taskType?->slug ?? $task->type)) }}">
                    <div class="kanban-card" {{ $canChangeThisStatus ? 'draggable=true' : 'draggable=false' }} style="{{ $canChangeThisStatus ? '' : 'cursor:default;' }}" ondragstart="{{ $canChangeThisStatus ? 'handleDragStart(event, '.$task->id.', \''.$status.'\')' : 'return false;' }}" ondragend="handleDragEnd(event)">
                        
                        {{-- Top Card Meta --}}
                        <div class="card-header-meta">
                            <span class="card-task-type">
                                <i data-lucide="{{ $task->type_icon }}" style="width:9px;height:9px;"></i>
                                {{ $task->type_label }}
                            </span>
                            <div class="d-flex align-items-center gap-1">
                                <span class="badge badge-{{ $task->priority }}" style="font-size:0.6rem;padding:0.15rem 0.45rem;border-radius:4px;">
                                    {{ ucfirst($task->priority) }}
                                </span>
                                
                                {{-- Quick Status Shift Dropdown --}}
                                <div class="dropdown card-shift-dropdown">
                                    <button class="btn btn-sm p-0 text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border:none;background:none;line-height:1;padding:2px 3px !important;" title="Actions">
                                        <i data-lucide="more-vertical" style="width:13px;height:13px;"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm card-move-menu" style="font-size:0.8rem;min-width:140px;background:var(--dropdown-bg);border:1px solid var(--border);">
                                        @if($canChangeThisStatus)
                                            <li class="dropdown-header text-muted py-1" style="font-size:0.68rem;">Move to</li>
                                            @foreach($cols as $destStatus => $destCol)
                                                @if($destStatus !== $status)
                                                <li>
                                                    <a class="dropdown-item py-1" href="javascript:void(0)" onclick="quickMoveTask({{ $task->id }}, '{{ $destStatus }}')">
                                                        <span class="status-nav-dot d-inline-block me-1" style="background:{{ $destCol['tc'] }};"></span>
                                                        {{ $destCol['label'] }}
                                                    </a>
                                                </li>
                                                @endif
                                            @endforeach
                                            <li><hr class="dropdown-divider my-1" style="border-color:var(--border);"></li>
                                        @else
                                            <li class="dropdown-header text-muted py-1" style="font-size:0.65rem;">Status Locked (Assigned to {{ $task->assignedTo?->name ?? 'None' }})</li>
                                            <li><hr class="dropdown-divider my-1" style="border-color:var(--border);"></li>
                                        @endif
                                        <li>
                                            <a class="dropdown-item py-1 text-primary" href="{{ route('tasks.show', $task) }}">
                                                <i data-lucide="external-link" style="width:11px;height:11px;" class="me-1"></i> View Details
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Title (Clickable link to Task Details) --}}
                        <a href="{{ route('tasks.show', $task) }}" class="card-task-title d-block" style="text-decoration:none;">
                            {{ $task->title }}
                            @if($task->is_overdue)
                            <span class="overdue-badge ms-1" style="font-size:0.58rem;padding:1px 4px;" title="Overdue Task!">Overdue</span>
                            @endif
                        </a>

                        {{-- Client Business & System URL --}}
                        @if($task->client)
                        <div class="card-client-tag d-flex flex-column align-items-start gap-1 w-100" style="background: rgba(99,102,241,0.06); padding: 4px 6px; border-radius: 6px; border: 1px solid rgba(99,102,241,0.12); margin-bottom: 0.55rem; white-space: normal;">
                            <div class="d-flex align-items-center gap-1 w-100" style="font-weight: 600; font-size: 0.72rem; color: var(--text-primary);">
                                <i data-lucide="building-2" style="width:11px;height:11px;flex-shrink:0;color:#ff7828;"></i>
                                <span class="text-truncate">{{ $task->client->company_name ?? $task->client->name }}</span>
                            </div>
                            @if($task->client->system_url)
                            <div class="d-flex align-items-center w-100">
                                <a href="{{ $task->client->formatted_system_url }}" target="_blank" rel="noopener noreferrer" class="badge text-truncate" style="background: var(--bg-card); color: var(--accent); border: 1px solid var(--border); text-decoration: none; padding: 2px 5px; font-size: 0.62rem; display: inline-flex; align-items: center; gap: 3px; max-width: 100%;" title="Open: {{ $task->client->system_url }}" onclick="event.stopPropagation();">
                                    <i data-lucide="external-link" style="width:9px;height:9px;flex-shrink:0;"></i>
                                    <span class="text-truncate">{{ $task->client->system_url }}</span>
                                </a>
                            </div>
                            @endif
                        </div>
                        @endif

                        {{-- Footer Meta: Deadline & Assignee --}}
                        <div class="card-footer-meta">
                            @if($task->deadline)
                            <span class="card-deadline" style="color:{{ $task->is_overdue ? '#ef4444' : 'var(--text-muted)' }};">
                                <i data-lucide="calendar" style="width:10px;height:10px;"></i>
                                {{ $task->deadline->format('d M') }}
                            </span>
                            @else
                            <span class="card-deadline" style="color:var(--text-muted);opacity:0.6;">
                                <i data-lucide="calendar" style="width:10px;height:10px;"></i>
                                No date
                            </span>
                            @endif

                            @if($task->assignedTo)
                                @if($task->assignedTo->avatar_url)
                                <img src="{{ $task->assignedTo->avatar_url }}" class="card-assignee-avatar" title="Assigned to {{ $task->assignedTo->name }}" alt="{{ $task->assignedTo->name }}">
                                @else
                                <div class="card-assignee-initials" title="Assigned to {{ $task->assignedTo->name }}">
                                    {{ strtoupper(substr($task->assignedTo->name, 0, 2)) }}
                                </div>
                                @endif
                            @else
                            <span style="font-size:0.64rem;color:var(--text-muted);font-style:italic;">Unassigned</span>
                            @endif
                        </div>

                    </div>
                </div>
                @empty
                <div class="kanban-empty" id="empty-state-{{ $status }}">
                    <i data-lucide="inbox" style="width:20px;height:20px;display:block;margin:0 auto 0.35rem;opacity:0.35;"></i>
                    <span>No tasks</span>
                </div>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>

</div>

{{-- Toast Feedback Notification --}}
<div id="kanbanToast" class="kanban-toast">
    <i data-lucide="check-circle" style="width:18px;height:18px;color:var(--success);"></i>
    <span id="kanbanToastMessage">Task updated successfully</span>
</div>

@endsection

@push('scripts')
<script>
const statusColumnsConfig = {
    'pending':      { label: 'Pending',      tc: '#94a3b8' },
    'in_progress':  { label: 'In Progress',  tc: '#6366f1' },
    'on_hold':      { label: 'On Hold',      tc: '#f59e0b' },
    'under_review': { label: 'Under Review', tc: '#0ea5e9' },
    'completed':    { label: 'Completed',    tc: '#10b981' }
};

let draggedTaskId = null;
let sourceStatus = null;
let currentPriorityFilter = 'all';
let currentActiveTab = 'all';

// ── Status Tab Filter (Show Single Column or All Columns within Page Body) ──
function filterByStatusTab(status) {
    currentActiveTab = status;

    // Update active tab buttons
    document.querySelectorAll('.status-nav-item').forEach(item => {
        item.classList.toggle('active', item.getAttribute('data-tab-status') === status);
    });

    const columns = document.querySelectorAll('.kanban-col');
    columns.forEach(col => {
        const colStatus = col.getAttribute('data-status');
        if (status === 'all' || colStatus === status) {
            col.classList.remove('hidden-by-tab');
        } else {
            col.classList.add('hidden-by-tab');
        }
    });

    // Re-adjust grid layout for single column view on small screens
    const grid = document.getElementById('kanbanGrid');
    if (grid) {
        if (status !== 'all' && window.innerWidth < 1024) {
            grid.style.gridTemplateColumns = '1fr';
        } else if (window.innerWidth >= 1024) {
            grid.style.gridTemplateColumns = 'repeat(5, minmax(0, 1fr))';
        } else {
            grid.style.gridTemplateColumns = '';
        }
    }
}

// ── Drag & Drop Event Handlers ──
function handleDragStart(e, taskId, status) {
    draggedTaskId = taskId;
    sourceStatus = status;
    e.dataTransfer.setData('text/plain', taskId);
    e.dataTransfer.effectAllowed = 'move';
    
    const card = e.target.closest('.kanban-card');
    if (card) {
        setTimeout(() => card.classList.add('is-dragging'), 0);
    }
}

function handleDragEnd(e) {
    const card = e.target.closest('.kanban-card');
    if (card) {
        card.classList.remove('is-dragging');
    }
    document.querySelectorAll('.kanban-body').forEach(el => el.classList.remove('drag-over'));
}

function handleDragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    const body = e.currentTarget;
    if (!body.classList.contains('drag-over')) {
        body.classList.add('drag-over');
    }
}

function handleDragLeave(e) {
    const body = e.currentTarget;
    body.classList.remove('drag-over');
}

function handleDrop(e, targetStatus) {
    e.preventDefault();
    const body = e.currentTarget;
    body.classList.remove('drag-over');

    if (!draggedTaskId || sourceStatus === targetStatus) return;

    performStatusUpdate(draggedTaskId, targetStatus);
}

// ── Quick Move (Dropdown click) ──
function quickMoveTask(taskId, targetStatus) {
    performStatusUpdate(taskId, targetStatus);
}

// ── Rebuild Card Dropdown Menu after Move ──
function updateCardMoveMenu(cardWrapper, newStatus, taskId) {
    const menu = cardWrapper.querySelector('.card-move-menu');
    if (!menu) return;

    const taskShowUrl = `/tasks/${taskId}`;
    let itemsHtml = `<li class="dropdown-header text-muted py-1" style="font-size:0.68rem;">Move to</li>`;

    for (const [statusKey, conf] of Object.entries(statusColumnsConfig)) {
        if (statusKey !== newStatus) {
            itemsHtml += `
            <li>
                <a class="dropdown-item py-1" href="javascript:void(0)" onclick="quickMoveTask(${taskId}, '${statusKey}')">
                    <span class="status-nav-dot d-inline-block me-1" style="background:${conf.tc};"></span>
                    ${conf.label}
                </a>
            </li>`;
        }
    }

    itemsHtml += `
        <li><hr class="dropdown-divider my-1" style="border-color:var(--border);"></li>
        <li>
            <a class="dropdown-item py-1 text-primary" href="${taskShowUrl}">
                <i data-lucide="external-link" style="width:11px;height:11px;" class="me-1"></i> View Details
            </a>
        </li>
    `;

    menu.innerHTML = itemsHtml;

    const cardEl = cardWrapper.querySelector('.kanban-card');
    if (cardEl) {
        cardEl.setAttribute('ondragstart', `handleDragStart(event, ${taskId}, '${newStatus}')`);
    }
    cardWrapper.setAttribute('data-current-status', newStatus);
}

// ── Status Update via AJAX ──
function performStatusUpdate(taskId, targetStatus) {
    const cardWrapper = document.querySelector(`.kanban-card-wrapper[data-task-id="${taskId}"]`);
    if (!cardWrapper) return;

    const currentStatus = cardWrapper.getAttribute('data-current-status') || sourceStatus;
    if (currentStatus === targetStatus) return;

    const sourceColumnBody = cardWrapper.parentElement;
    const targetColumnBody = document.getElementById(`col-body-${targetStatus}`);
    if (!targetColumnBody) return;

    // Optimistic UI move
    const emptyTarget = targetColumnBody.querySelector('.kanban-empty');
    if (emptyTarget) emptyTarget.style.display = 'none';

    targetColumnBody.appendChild(cardWrapper);
    updateCardMoveMenu(cardWrapper, targetStatus, taskId);
    updateColumnCounts();

    // Re-bind Lucide icons
    if (window.lucide) lucide.createIcons();

    // Send PATCH request to Laravel backend
    fetch(`/tasks/${taskId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: targetStatus })
    })
    .then(response => {
        if (response.ok) {
            const label = statusColumnsConfig[targetStatus]?.label || targetStatus;
            showKanbanToast(`Moved to ${label}`);
        } else {
            // Rollback if failed
            sourceColumnBody.appendChild(cardWrapper);
            updateCardMoveMenu(cardWrapper, currentStatus, taskId);
            updateColumnCounts();
            showKanbanToast('Failed to update task status', true);
        }
    })
    .catch(() => {
        // Rollback on network error
        sourceColumnBody.appendChild(cardWrapper);
        updateCardMoveMenu(cardWrapper, currentStatus, taskId);
        updateColumnCounts();
        showKanbanToast('Connection error. Please try again.', true);
    });
}

// ── Update Counters in Headers and Tabs ──
function updateColumnCounts() {
    const statuses = ['pending', 'in_progress', 'on_hold', 'under_review', 'completed'];
    let totalVisible = 0;

    statuses.forEach(status => {
        const body = document.getElementById(`col-body-${status}`);
        if (!body) return;

        const visibleCards = Array.from(body.querySelectorAll('.kanban-card-wrapper')).filter(card => card.style.display !== 'none');
        const count = visibleCards.length;
        totalVisible += count;

        // Update Column Header Count
        const headerCount = document.getElementById(`header-count-${status}`);
        if (headerCount) headerCount.textContent = count;

        // Update Nav Tab Count
        const navCount = document.getElementById(`nav-count-${status}`);
        if (navCount) navCount.textContent = count;

        // Toggle Empty state
        let emptyState = body.querySelector('.kanban-empty');
        if (count === 0) {
            if (!emptyState) {
                emptyState = document.createElement('div');
                emptyState.className = 'kanban-empty';
                emptyState.id = `empty-state-${status}`;
                emptyState.innerHTML = '<i data-lucide="inbox" style="width:20px;height:20px;display:block;margin:0 auto 0.35rem;opacity:0.35;"></i><span>No tasks</span>';
                body.appendChild(emptyState);
                if (window.lucide) lucide.createIcons();
            } else {
                emptyState.style.display = 'block';
            }
        } else if (emptyState) {
            emptyState.style.display = 'none';
        }
    });

    const navCountAll = document.getElementById('nav-count-all');
    if (navCountAll) navCountAll.textContent = totalVisible;
}

// ── Search & Filter Logic ──
const searchInput = document.getElementById('kanbanSearchInput');
const priorityPills = document.querySelectorAll('.priority-pill');

function applyFilters() {
    const searchTerm = (searchInput?.value || '').toLowerCase().trim();

    document.querySelectorAll('.kanban-card-wrapper').forEach(card => {
        const title = card.getAttribute('data-title') || '';
        const client = card.getAttribute('data-client') || '';
        const type = card.getAttribute('data-type') || '';
        const priority = card.getAttribute('data-priority') || '';

        const matchesSearch = !searchTerm || title.includes(searchTerm) || client.includes(searchTerm) || type.includes(searchTerm);
        const matchesPriority = (currentPriorityFilter === 'all') || (priority === currentPriorityFilter);

        if (matchesSearch && matchesPriority) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });

    updateColumnCounts();
}

if (searchInput) {
    searchInput.addEventListener('input', applyFilters);
}

priorityPills.forEach(pill => {
    pill.addEventListener('click', function() {
        priorityPills.forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        currentPriorityFilter = this.getAttribute('data-priority');
        applyFilters();
    });
});

// ── Toast Helper ──
let toastTimer;
function showKanbanToast(message, isError = false) {
    const toast = document.getElementById('kanbanToast');
    const toastMsg = document.getElementById('kanbanToastMessage');
    if (!toast || !toastMsg) return;

    toastMsg.textContent = message;
    if (isError) {
        toast.style.borderColor = 'var(--danger)';
        toast.querySelector('i').style.color = 'var(--danger)';
    } else {
        toast.style.borderColor = 'var(--accent)';
        toast.querySelector('i').style.color = 'var(--success)';
    }

    toast.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

// Initial Lucide Icon Rendering
document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide) lucide.createIcons();
});
</script>
@endpush
