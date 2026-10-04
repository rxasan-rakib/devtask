@extends('layouts.app')
@section('title', $reminder->title . ' — Reminder Details')
@section('page-title', 'Reminder Details')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>{{ $reminder->title }}</h2>
        <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;margin-top:.35rem;">
            <span class="badge" style="background:var(--accent-light);color:var(--accent);font-size:.75rem;">
                <i data-lucide="{{ $reminder->type_icon }}" style="width:12px;height:12px;display:inline;margin-right:3px;"></i>
                {{ $reminder->type_label }}
            </span>
            @if($reminder->is_done)
                <span class="badge" style="background:rgba(16,185,129,0.15);color:#10b981;font-size:.75rem;font-weight:600;">✓ Completed</span>
            @elseif($reminder->is_overdue)
                <span class="badge" style="background:rgba(239,68,68,0.15);color:#ef4444;font-size:.75rem;font-weight:700;">⚠️ Overdue</span>
            @else
                <span class="badge" style="background:rgba(245,158,11,0.15);color:#f59e0b;font-size:.75rem;font-weight:600;">⏳ Pending</span>
            @endif
        </div>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('reminders.index') }}" class="btn-ghost">
            <i data-lucide="arrow-left" style="width:14px;height:14px;"></i> All Reminders
        </a>
        <a href="{{ route('reminders.edit', $reminder) }}" class="btn-gradient" style="padding:.5rem 1rem;">
            <i data-lucide="pencil" style="width:14px;height:14px;"></i> Edit
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        {{-- Description --}}
        @if($reminder->description)
        <div class="card-glass mb-3">
            <div class="section-title">Reminder Note / Description</div>
            <div style="color:var(--text-muted);font-size:.875rem;line-height:1.8;background:rgba(99,102,241,.04);border:1px solid rgba(99,102,241,.1);border-radius:10px;padding:1rem;">
                {!! nl2br(e($reminder->description)) !!}
            </div>
        </div>
        @endif

        {{-- Status / Action --}}
        <div class="card-glass mb-3">
            <div class="section-title">Status & Trigger</div>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <div style="font-weight:600;font-size:.9rem;color:var(--text-primary);">
                        Scheduled Time: {{ $reminder->remind_at ? $reminder->remind_at->format('d M Y, h:i A') : 'N/A' }}
                    </div>
                    <div style="font-size:.78rem;color:var(--text-muted);margin-top:.2rem;">
                        {{ $reminder->remind_at ? $reminder->remind_at->diffForHumans() : '' }}
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    @if(!$reminder->is_done)
                        <form method="POST" action="{{ route('reminders.mark-done', $reminder) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-success d-flex align-items-center gap-1" style="padding:.45rem 1rem;font-size:.82rem; border-radius: 8px;">
                                <i data-lucide="check-circle" style="width:15px;height:15px;"></i> Mark as Done
                            </button>
                        </form>
                    @else
                        <span class="text-success fw-bold me-2" style="font-size:.85rem;">
                            <i data-lucide="check" style="width:15px;height:15px;display:inline;"></i> Completed {{ $reminder->completed_at ? $reminder->completed_at->diffForHumans() : '' }}
                        </span>
                        <form method="POST" action="{{ route('reminders.mark-pending', $reminder) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-outline-secondary" style="font-size:.75rem; border-radius: 6px;">
                                <i data-lucide="rotate-ccw" style="width:12px;height:12px;display:inline;"></i> Reopen
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-glass mb-3 card-accent-top">
            <div class="section-title">Details</div>

            <div class="info-row">
                <span class="info-label">Type</span>
                <span class="info-value">{{ $reminder->type_label }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Related Client</span>
                <span class="info-value">
                    @if($reminder->client)
                        <a href="{{ route('clients.show', $reminder->client) }}" style="color:var(--accent);text-decoration:none;">
                            {{ $reminder->client->name }}
                        </a>
                    @else
                        <span style="color:var(--text-muted);">None</span>
                    @endif
                </span>
            </div>

            <div class="info-row">
                <span class="info-label">Related Task</span>
                <span class="info-value">
                    @if($reminder->task)
                        <a href="{{ route('tasks.show', $reminder->task) }}" style="color:var(--accent);text-decoration:none;">
                            {{ Str::limit($reminder->task->title, 25) }}
                        </a>
                    @else
                        <span style="color:var(--text-muted);">None</span>
                    @endif
                </span>
            </div>

            <div class="info-row">
                <span class="info-label">Email Alert</span>
                <span class="info-value">
                    <span class="badge" style="background:{{ $reminder->notify_email ? 'rgba(16,185,129,0.12)' : 'rgba(239,68,68,0.12)' }};color:{{ $reminder->notify_email ? 'var(--success)' : 'var(--danger)' }};font-size:.7rem;">
                        {{ $reminder->notify_email ? ($reminder->email_sent ? 'Sent' : 'Pending Delivery') : 'Disabled' }}
                    </span>
                </span>
            </div>

            <div class="info-row">
                <span class="info-label">Browser Alert</span>
                <span class="info-value">
                    <span class="badge" style="background:{{ $reminder->notify_browser ? 'rgba(16,185,129,0.12)' : 'rgba(239,68,68,0.12)' }};color:{{ $reminder->notify_browser ? 'var(--success)' : 'var(--danger)' }};font-size:.7rem;">
                        {{ $reminder->notify_browser ? 'Enabled' : 'Disabled' }}
                    </span>
                </span>
            </div>

            <div class="info-row">
                <span class="info-label">Created</span>
                <span class="info-value" style="color:var(--text-muted);font-size:.82rem;">
                    {{ $reminder->created_at->format('d M Y, h:i A') }}
                </span>
            </div>
        </div>
    </div>
</div>
@endsection
