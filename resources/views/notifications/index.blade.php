@extends('layouts.app')

@section('title', 'Notifications')
@section('page-title', 'Notifications Center')

@section('content')
{{-- Page Header --}}
<div class="page-header">
    <div class="page-header-left">
        <h2>Notifications Center</h2>
        <p>Real-time alerts, task deadlines, automated reminders & system logs</p>
    </div>
    <div class="page-header-actions">
        @can('manage notifications')
        @if($unreadCount > 0)
        <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn-gradient" style="font-size:0.82rem;padding:0.45rem 0.95rem; border-radius: 8px;">
                <i data-lucide="check-check" style="width:14px;height:14px;"></i> Mark All Read
            </button>
        </form>
        @endif
        @endcan

        @can('delete notifications')
        @if($totalCount > $unreadCount)
        <form action="{{ route('notifications.clear-read') }}" method="POST" class="d-inline" onsubmit="return confirm('Clear all read notifications?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-ghost" style="font-size:0.82rem;padding:0.45rem 0.95rem;color:var(--text-muted); border-radius: 8px;">
                <i data-lucide="trash-2" style="width:14px;height:14px;"></i> Clear Read
            </button>
        </form>
        @endif
        @endcan
    </div>
</div>

{{-- KPI Metric Summary Cards (Matches Reports Page) --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('notifications.index', array_merge(request()->except('page'), ['filter' => 'all'])) }}" style="text-decoration:none;color:inherit;">
            <div class="stat-card d-flex align-items-center gap-3 {{ $filter === 'all' ? 'active-stat-card' : '' }}" style="border-radius:14px;padding:1.15rem;transition:all .2s;">
                <div class="stat-icon" style="background: rgba(99, 102, 241, 0.15); color: var(--accent);width:46px;height:46px;border-radius:12px;">
                    <i data-lucide="bell" style="width: 22px; height: 22px;"></i>
                </div>
                <div>
                    <div class="stat-value" style="font-size:1.45rem;font-weight:800;">{{ number_format($totalCount) }}</div>
                    <div class="stat-label" style="font-size:0.75rem;">Total Notifications</div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('notifications.index', array_merge(request()->except('page'), ['filter' => 'unread'])) }}" style="text-decoration:none;color:inherit;">
            <div class="stat-card d-flex align-items-center gap-3 {{ $filter === 'unread' ? 'active-stat-card' : '' }}" style="border-radius:14px;padding:1.15rem;transition:all .2s;">
                <div class="stat-icon" style="background: rgba(239, 68, 68, 0.15); color: #ef4444;width:46px;height:46px;border-radius:12px;">
                    <i data-lucide="bell-ring" style="width: 22px; height: 22px;"></i>
                </div>
                <div>
                    <div class="stat-value" style="font-size:1.45rem;font-weight:800;color:{{ $unreadCount > 0 ? '#ef4444' : 'var(--text-primary)' }};">
                        {{ number_format($unreadCount) }}
                    </div>
                    <div class="stat-label" style="font-size:0.75rem;">Unread Alerts</div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('notifications.index', array_merge(request()->except('page'), ['filter' => 'reminders'])) }}" style="text-decoration:none;color:inherit;">
            <div class="stat-card d-flex align-items-center gap-3 {{ $filter === 'reminders' ? 'active-stat-card' : '' }}" style="border-radius:14px;padding:1.15rem;transition:all .2s;">
                <div class="stat-icon" style="background: rgba(255, 120, 40, 0.15); color: #ff7828;width:46px;height:46px;border-radius:12px;">
                    <i data-lucide="alarm-clock" style="width: 22px; height: 22px;"></i>
                </div>
                <div>
                    <div class="stat-value" style="font-size:1.45rem;font-weight:800;color:#ff7828;">{{ number_format($remindersCount) }}</div>
                    <div class="stat-label" style="font-size:0.75rem;">Reminder Alerts</div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('notifications.index', array_merge(request()->except('page'), ['filter' => 'tasks'])) }}" style="text-decoration:none;color:inherit;">
            <div class="stat-card d-flex align-items-center gap-3 {{ $filter === 'tasks' ? 'active-stat-card' : '' }}" style="border-radius:14px;padding:1.15rem;transition:all .2s;">
                <div class="stat-icon" style="background: rgba(14, 165, 233, 0.15); color: #0ea5e9;width:46px;height:46px;border-radius:12px;">
                    <i data-lucide="check-square" style="width: 22px; height: 22px;"></i>
                </div>
                <div>
                    <div class="stat-value" style="font-size:1.45rem;font-weight:800;color:#0ea5e9;">{{ number_format($tasksCount) }}</div>
                    <div class="stat-label" style="font-size:0.75rem;">Task & Project Alerts</div>
                </div>
            </div>
        </a>
    </div>
</div>

{{-- Quick Filter Tabs & Search Card (Glassmorphic) --}}
<div class="card-glass mb-4 p-3" style="border-radius:14px;">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        
        {{-- Filter Tabs --}}
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('notifications.index', array_merge(request()->except(['filter', 'page']))) }}"
               class="btn-ghost {{ $filter === 'all' ? 'border-primary text-primary fw-bold' : '' }}"
               style="padding: .4rem .9rem; font-size: .82rem; border-radius: 20px; {{ $filter === 'all' ? 'background: rgba(99,102,241,0.1);' : '' }}">
                All <span class="badge ms-1" style="background: rgba(148,163,184,0.2); color: inherit;">{{ $totalCount }}</span>
            </a>

            <a href="{{ route('notifications.index', array_merge(request()->except('page'), ['filter' => 'unread'])) }}"
               class="btn-ghost {{ $filter === 'unread' ? 'border-danger text-danger fw-bold' : '' }}"
               style="padding: .4rem .9rem; font-size: .82rem; border-radius: 20px; {{ $filter === 'unread' ? 'background: rgba(239,68,68,0.12);' : '' }}">
                ● Unread <span class="badge ms-1" style="background: rgba(239,68,68,0.2); color: #ef4444;">{{ $unreadCount }}</span>
            </a>

            <a href="{{ route('notifications.index', array_merge(request()->except('page'), ['filter' => 'reminders'])) }}"
               class="btn-ghost {{ $filter === 'reminders' ? 'border-warning text-warning fw-bold' : '' }}"
               style="padding: .4rem .9rem; font-size: .82rem; border-radius: 20px; {{ $filter === 'reminders' ? 'background: rgba(245,158,11,0.12);' : '' }}">
                ⏰ Reminders <span class="badge ms-1" style="background: rgba(245,158,11,0.2); color: #f59e0b;">{{ $remindersCount }}</span>
            </a>

            <a href="{{ route('notifications.index', array_merge(request()->except('page'), ['filter' => 'tasks'])) }}"
               class="btn-ghost {{ $filter === 'tasks' ? 'border-info text-info fw-bold' : '' }}"
               style="padding: .4rem .9rem; font-size: .82rem; border-radius: 20px; {{ $filter === 'tasks' ? 'background: rgba(14,165,233,0.12);' : '' }}">
                📋 Tasks <span class="badge ms-1" style="background: rgba(14,165,233,0.2); color: #0ea5e9;">{{ $tasksCount }}</span>
            </a>
        </div>

        {{-- Search Input --}}
        <form action="{{ route('notifications.index') }}" method="GET" class="d-flex align-items-center" style="min-width: 240px;">
            @if(request('filter'))
                <input type="hidden" name="filter" value="{{ request('filter') }}">
            @endif
            <div style="position:relative; width: 100%;">
                <i data-lucide="search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:var(--text-muted);"></i>
                <input type="text" name="search" class="form-control form-control-dark" style="padding-left:32px; height:36px; font-size:.82rem; border-radius:10px;" placeholder="Search alerts..." value="{{ request('search') }}">
                @if(request('search'))
                    <a href="{{ route('notifications.index', request()->except('search')) }}" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:var(--text-muted);">
                        <i data-lucide="x" style="width:13px;height:13px;"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Notifications Cards List (Styled like Reports & Reminders Cards) --}}
<div class="row g-3">
    @forelse($notifications as $notif)
        @php
            $isUnread = !$notif->isRead();
            $badgeColor = $notif->type_badge_color;
            $cardBorder = $isUnread ? 'rgba(99,102,241,0.35)' : 'var(--border)';
            $cardBg = $isUnread ? 'rgba(99,102,241,0.02)' : 'var(--bg-card)';
        @endphp

        <div class="col-12 col-md-6 col-xl-4">
            <div class="card-glass h-100 d-flex flex-column {{ $isUnread ? 'notif-unread-border' : '' }}"
                 style="border-color:{{ $cardBorder }}; background:{{ $cardBg }}; border-radius:14px; transition:all .2s ease;">
                
                {{-- Card Header --}}
                <div class="d-flex align-items-start gap-3 mb-2">
                    <div style="width:42px; height:42px; background:{{ $badgeColor }}18; color:{{ $badgeColor }}; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <i data-lucide="{{ $notif->default_icon }}" style="width:18px; height:18px;"></i>
                    </div>

                    <div style="flex:1; min-width:0;">
                        <h5 style="font-weight:700; font-size:.92rem; color:var(--text-primary); margin-bottom:.25rem; line-height:1.3; word-break:break-word;">
                            @if($notif->action_url)
                                <a href="{{ route('notifications.read-and-redirect', $notif) }}" style="color:inherit; text-decoration:none;">
                                    {{ $notif->title }}
                                </a>
                            @else
                                {{ $notif->title }}
                            @endif
                        </h5>
                        <span class="badge" style="background:{{ $badgeColor }}15; color:{{ $badgeColor }}; border:1px solid {{ $badgeColor }}30; font-size:.65rem; font-weight:600;">
                            {{ $notif->type_label }}
                        </span>
                    </div>

                    @if($isUnread)
                        <span class="badge" style="background:rgba(239,68,68,0.15); color:#ef4444; flex-shrink:0; font-weight:700; padding:4px 8px; border-radius:6px; animation:pulse 2s infinite;">
                            ● New
                        </span>
                    @else
                        <span class="badge" style="background:rgba(148,163,184,0.12); color:var(--text-muted); flex-shrink:0; font-weight:500; padding:4px 8px; border-radius:6px;">
                            Read
                        </span>
                    @endif
                </div>

                {{-- Description Box --}}
                @if($notif->message)
                    <div style="color:var(--text-muted);font-size:.80rem;line-height:1.6;background:rgba(99,102,241,.03);border:1px solid rgba(99,102,241,.08);border-radius:10px;padding:.7rem .85rem;margin-bottom:.85rem;flex-grow:1;">
                        {{ $notif->message }}
                    </div>
                @else
                    <div style="flex-grow:1;"></div>
                @endif

                {{-- Metadata Info --}}
                <div class="mb-3 pt-2 border-top" style="border-color: var(--border) !important;">
                    @if(!empty($notif->data))
                        @if(isset($notif->data['remind_at']))
                            <div style="font-size:.76rem; color:var(--text-muted); margin-bottom:.35rem; display:flex; align-items:center; gap:6px;">
                                <i data-lucide="calendar" style="width:13px; height:13px; color:#ff7828; flex-shrink:0;"></i>
                                <span>Scheduled: <strong style="color:var(--text-primary);">{{ \Carbon\Carbon::parse($notif->data['remind_at'])->format('d M Y, h:i A') }}</strong></span>
                            </div>
                        @endif
                        @if(isset($notif->data['priority']))
                            <div style="font-size:.76rem; color:var(--text-muted); margin-bottom:.35rem; display:flex; align-items:center; gap:6px;">
                                <i data-lucide="flag" style="width:13px; height:13px; color:var(--accent); flex-shrink:0;"></i>
                                <span>Priority: <strong style="text-transform: capitalize; color:var(--text-primary);">{{ $notif->data['priority'] }}</strong></span>
                            </div>
                        @endif
                    @endif

                    <div style="font-size:.76rem; display:flex; align-items:center; gap:6px; color:var(--text-muted);">
                        <i data-lucide="clock" style="width:13px; height:13px; flex-shrink:0;"></i>
                        <span>
                            {{ $notif->created_at->format('d M Y, h:i A') }}
                            <strong style="opacity:.85; font-size:.70rem;">({{ $notif->created_at->diffForHumans() }})</strong>
                        </span>
                    </div>
                </div>

                {{-- Card Actions Footer --}}
                <div class="d-flex gap-2 align-items-center mt-auto">
                    @if($notif->action_url)
                        <a href="{{ route('notifications.read-and-redirect', $notif) }}"
                           class="flex-grow-1 btn-gradient d-inline-flex align-items-center justify-content-center gap-1"
                           style="font-size:.78rem; font-weight:600; padding:.45rem .6rem; border-radius:8px; text-decoration:none;">
                            <span>View Details</span>
                            <i data-lucide="arrow-right" style="width:13px; height:13px;"></i>
                        </a>
                    @endif

                    @can('manage notifications')
                    @if($isUnread)
                        <form action="{{ route('notifications.read', $notif) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-icon" style="width:36px; height:36px; flex-shrink:0; border-radius:8px;" title="Mark as read">
                                <i data-lucide="check" style="width:14px; height:14px; color:#10b981;"></i>
                            </button>
                        </form>
                    @else
                        <form action="{{ route('notifications.unread', $notif) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-icon" style="width:36px; height:36px; flex-shrink:0; border-radius:8px;" title="Mark as unread">
                                <i data-lucide="rotate-ccw" style="width:13px; height:13px;"></i>
                            </button>
                        </form>
                    @endif
                    @endcan

                    @can('delete notifications')
                    <form action="{{ route('notifications.destroy', $notif) }}" method="POST" onsubmit="return confirm('Delete this notification?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-icon" style="width:36px; height:36px; color:#ef4444; border-color:rgba(239,68,68,.25); border-radius:8px;" title="Delete notification">
                            <i data-lucide="trash-2" style="width:14px; height:14px;"></i>
                        </button>
                    </form>
                    @endcan
                </div>

            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card-glass text-center py-5" style="border-radius:14px;">
                <div style="width:56px; height:56px; background:rgba(99,102,241,.1); border-radius:50%; display:inline-flex; align-items:center; justify-content:center; margin-bottom:1rem; color:var(--accent);">
                    <i data-lucide="bell-off" style="width:26px; height:26px;"></i>
                </div>
                <h4 style="font-weight:700; font-size:1.1rem; color:var(--text-primary); margin-bottom:.35rem;">No Notifications Found</h4>
                <p style="font-size:.85rem; color:var(--text-muted); max-width:400px; margin:0 auto 1.25rem;">
                    @if(request('filter') || request('search'))
                        No notifications match your active search or filter criteria.
                    @else
                        You are all caught up! New reminders, task deadline alerts, and system notifications will appear here.
                    @endif
                </p>
                @if(request('filter') || request('search'))
                    <a href="{{ route('notifications.index') }}" class="btn-ghost" style="font-size:.82rem; padding:.4rem 1rem; border-radius:20px;">
                        Reset Filters
                    </a>
                @endif
            </div>
        </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($notifications->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $notifications->links() }}
    </div>
@endif

<style>
    .active-stat-card {
        border: 1px solid var(--accent) !important;
        box-shadow: 0 0 15px rgba(99, 102, 241, 0.2) !important;
    }
    .notif-unread-border {
        position: relative;
    }
    .notif-unread-border::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, #ff7828, #6366f1);
        border-top-left-radius: 14px;
        border-bottom-left-radius: 14px;
    }
</style>
@endsection
