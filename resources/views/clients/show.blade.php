@extends('layouts.app')
@section('title', $client->company_name ?? $client->name)
@section('page-title', 'Client Profile')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>{{ $client->company_name ?? $client->name }}</h2>
        <p>@if($client->name && $client->name !== $client->company_name)Contact Person: {{ $client->name }} &bull; @endif{{ $client->type_label }}</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('clients.index') }}" class="btn-ghost">
            <i data-lucide="arrow-left" style="width:14px;height:14px;"></i> Clients
        </a>
        @can('create tasks')
        <a href="{{ route('tasks.create') }}?client_id={{ $client->id }}" class="btn-ghost">
            <i data-lucide="plus" style="width:14px;height:14px;"></i> Add Task
        </a>
        @endcan
        @can('edit clients')
        <a href="{{ route('clients.edit', $client) }}" class="btn-gradient" style="padding:.5rem 1rem;">
            <i data-lucide="pencil" style="width:14px;height:14px;"></i> Edit
        </a>
        @endcan
    </div>
</div>

<div class="row g-3">
    {{-- Profile Card --}}
    <div class="col-lg-4">
        <div class="card-glass mb-3" style="text-align:center;padding:2rem 1.5rem;">
            <div style="width:72px;height:72px;border-radius:20px;background:linear-gradient(135deg,var(--accent),#8b5cf6);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;box-shadow:0 8px 24px rgba(99,102,241,.35);">
                <span style="color:#fff;font-weight:800;font-size:1.8rem;">{{ strtoupper(substr($client->company_name ?? $client->name ?? 'C',0,1)) }}</span>
            </div>
            <h5 style="font-size:1.15rem;font-weight:700;margin-bottom:.25rem;">{{ $client->company_name ?? $client->name }}</h5>
            @if($client->name && $client->name !== $client->company_name)
            <div style="font-size:.82rem;color:var(--text-muted);margin-bottom:.75rem;">
                <i data-lucide="user" style="width:13px;height:13px;display:inline;margin-right:3px;"></i>{{ $client->name }}
            </div>
            @endif
            <div class="d-flex align-items-center justify-content-center gap-2 mt-2">
                <span class="badge" style="background:rgba(99,102,241,.15);color:var(--accent);">{{ $client->type_label }}</span>
                <span class="badge" style="background:{{ $client->is_active ? 'rgba(16,185,129,.15)' : 'rgba(148,163,184,.15)' }};color:{{ $client->is_active ? '#10b981' : '#94a3b8' }};">
                    {{ $client->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>

        {{-- System URL Card --}}
        @if($client->system_url)
        <div class="card-glass mb-3" style="background:linear-gradient(135deg, rgba(99,102,241,0.06), rgba(139,92,246,0.03));border:1px solid rgba(99,102,241,0.25);">
            <div class="section-title" style="color:var(--accent);">
                <i data-lucide="globe" style="width:14px;height:14px;color:var(--accent);"></i>
                System Portal URL
            </div>
            <div class="mb-2" style="font-size:0.85rem;font-weight:600;word-break:break-all;">
                {{ $client->system_url }}
            </div>
            <a href="{{ $client->formatted_system_url }}" target="_blank" rel="noopener noreferrer" class="btn-primary-custom w-100 justify-content-center mt-2" style="font-size:0.8rem;padding:0.45rem;">
                <i data-lucide="external-link" style="width:14px;height:14px;"></i> Open System URL
            </a>
        </div>
        @endif

        <div class="card-glass mb-3">
            <div class="section-title">Contact Info</div>
            @if($client->phone)
            <div style="display:flex;align-items:center;gap:.6rem;font-size:.875rem;margin-bottom:.75rem;">
                <div style="width:32px;height:32px;background:rgba(99,102,241,.1);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i data-lucide="phone" style="width:14px;height:14px;color:var(--accent);"></i>
                </div>
                <div>
                    <div style="font-size:0.7rem;color:var(--text-muted);">Phone</div>
                    <a href="tel:{{ $client->phone }}" style="color:var(--text-primary);font-weight:500;">{{ $client->phone }}</a>
                </div>
            </div>
            @endif

            @if($client->email)
            <div style="display:flex;align-items:center;gap:.6rem;font-size:.875rem;margin-bottom:.75rem;">
                <div style="width:32px;height:32px;background:rgba(99,102,241,.1);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i data-lucide="mail" style="width:14px;height:14px;color:var(--accent);"></i>
                </div>
                <div style="min-width:0;">
                    <div style="font-size:0.7rem;color:var(--text-muted);">Email</div>
                    <a href="mailto:{{ $client->email }}" class="text-truncate d-block" style="color:var(--text-primary);font-weight:500;">{{ $client->email }}</a>
                </div>
            </div>
            @endif

            @if($client->address)
            <div style="display:flex;align-items:flex-start;gap:.6rem;font-size:.875rem;">
                <div style="width:32px;height:32px;background:rgba(99,102,241,.1);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i data-lucide="map-pin" style="width:14px;height:14px;color:var(--accent);"></i>
                </div>
                <div>
                    <div style="font-size:0.7rem;color:var(--text-muted);">Office / Store Address</div>
                    <span style="color:var(--text-primary);">{{ $client->address }}</span>
                </div>
            </div>
            @endif

            @if(!$client->phone && !$client->email && !$client->address)
            <div style="font-size:0.8rem;color:var(--text-muted);font-style:italic;">No additional contact details provided.</div>
            @endif
        </div>

        {{-- Stats --}}
        <div class="card-glass mb-3">
            <div class="section-title">Quick Stats</div>
            <div class="row g-2">
                <div class="col-6">
                    <div style="background:var(--bg-hover);border-radius:10px;padding:.9rem;text-align:center;">
                        <div style="font-size:1.6rem;font-weight:800;color:var(--text-primary);">{{ $client->tasks->count() }}</div>
                        <div style="font-size:.7rem;color:var(--text-muted);margin-top:.15rem;">Total Tasks</div>
                    </div>
                </div>
                <div class="col-6">
                    <div style="background:rgba(16,185,129,.08);border-radius:10px;padding:.9rem;text-align:center;border:1px solid rgba(16,185,129,.15);">
                        <div style="font-size:1.6rem;font-weight:800;color:var(--success);">{{ $client->tasks->where('status','completed')->count() }}</div>
                        <div style="font-size:.7rem;color:var(--text-muted);margin-top:.15rem;">Completed</div>
                    </div>
                </div>
            </div>
            @php
                $total = $client->tasks->count();
                $done = $client->tasks->where('status','completed')->count();
                $pct = $total > 0 ? round($done/$total*100) : 0;
            @endphp
            @if($total > 0)
            <div style="margin-top:.75rem;">
                <div style="display:flex;justify-content:space-between;font-size:.72rem;color:var(--text-muted);margin-bottom:.35rem;">
                    <span>Completion Rate</span><span>{{ $pct }}%</span>
                </div>
                <div class="progress-slim">
                    <div class="progress-slim-fill" style="width:{{ $pct }}%;background:linear-gradient(90deg,var(--accent),var(--success));"></div>
                </div>
            </div>
            @endif
        </div>


    </div>

    {{-- Tasks --}}
    <div class="col-lg-8">
        <div class="card-glass p-0" style="overflow:hidden;">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:1.1rem 1.35rem;border-bottom:1px solid var(--border);">
                <h6 style="font-size:.95rem;font-weight:600;margin:0;">All Tasks for {{ $client->company_name ?? $client->name }}</h6>
                <a href="{{ route('tasks.create') }}?client_id={{ $client->id }}" class="btn-gradient" style="padding:.35rem .8rem;font-size:.78rem;">
                    <i data-lucide="plus" style="width:13px;height:13px;"></i> New Task
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-dark-custom mb-0">
                    <thead><tr>
                        <th style="padding-left:1.35rem;">Task</th>
                        <th>Type</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Deadline</th>
                        <th style="padding-right:1.35rem;"></th>
                    </tr></thead>
                    <tbody>
                        @forelse($client->tasks as $task)
                        <tr class="{{ $task->is_overdue ? 'overdue-row' : '' }}">
                            <td style="padding-left:1.35rem;">
                                <a href="{{ route('tasks.show', $task) }}" style="color:var(--text-primary);text-decoration:none;font-weight:500;">{{ Str::limit($task->title, 40) }}</a>
                                @if($task->is_overdue)<span class="overdue-badge ms-1">!</span>@endif
                            </td>
                            <td><span class="badge" style="background:var(--bg-hover);color:var(--text-muted);">{{ $task->type_label }}</span></td>
                            <td><span class="badge badge-{{ $task->priority }}">{{ ucfirst($task->priority) }}</span></td>
                            <td><span class="badge status-{{ $task->status }}">{{ ucfirst(str_replace('_',' ',$task->status)) }}</span></td>
                            <td style="font-size:.8rem;color:var(--text-muted);">{{ $task->deadline?->format('d M Y') ?? '—' }}</td>
                            <td style="padding-right:1.35rem;">
                                <a href="{{ route('tasks.show', $task) }}" class="btn-icon" style="width:28px;height:28px;">
                                    <i data-lucide="eye" style="width:13px;height:13px;"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="padding:0;border:none;">
                                <div class="empty-state" style="padding:2.5rem;">
                                    <div class="empty-state-icon">
                                        <i data-lucide="list-checks" style="width:24px;height:24px;color:var(--text-muted);"></i>
                                    </div>
                                    <h5>No tasks yet</h5>
                                    <p>This client has no tasks assigned</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
