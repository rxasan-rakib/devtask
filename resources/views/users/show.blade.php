@extends('layouts.app')
@section('title', $user->name . ' — Member Profile')
@section('page-title', 'Team Member Profile')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>{{ $user->name }}</h2>
        <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;margin-top:.35rem;">
            <span class="badge" style="background:var(--accent-light);color:var(--accent);font-size:.75rem;">
                {{ ucfirst(str_replace('-',' ', $user->getRoleNames()->first() ?? 'Member')) }}
            </span>
            <span class="badge" style="background:{{ $user->is_active ? 'rgba(16,185,129,0.12)' : 'rgba(239,68,68,0.12)' }};color:{{ $user->is_active ? 'var(--success)' : 'var(--danger)' }};font-size:.75rem;">
                {{ $user->is_active ? 'Active Account' : 'Inactive' }}
            </span>
        </div>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('users.index') }}" class="btn-ghost">
            <i data-lucide="arrow-left" style="width:14px;height:14px;"></i> All Members
        </a>
        <a href="{{ route('users.edit', $user) }}" class="btn-gradient" style="padding:.5rem 1rem;">
            <i data-lucide="pencil" style="width:14px;height:14px;"></i> Edit Member
        </a>
    </div>
</div>

<div class="row g-3">
    {{-- Left Column: Profile Card & Tasks --}}
    <div class="col-lg-4">
        <div class="card-glass mb-3 text-center py-4">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" style="width:84px;height:84px;border-radius:50%;border:3px solid var(--accent);margin:0 auto .75rem;object-fit:cover;">
            <h4 style="font-weight:700;font-size:1.15rem;margin-bottom:.2rem;color:var(--text-primary);">{{ $user->name }}</h4>
            <p style="font-size:.82rem;color:var(--text-muted);margin-bottom:.75rem;">{{ $user->designation ?: 'Team Member' }}</p>
            @if($user->roles->first())
                @can('view roles')
                    <a href="{{ route('roles.show', $user->roles->first()) }}" class="badge" style="background:var(--accent-light);color:var(--accent);font-size:.75rem;padding:.3rem .75rem;border-radius:20px;text-decoration:none;" title="View Role Permissions">
                        <i data-lucide="shield" style="width:12px;height:12px;display:inline;"></i>
                        {{ ucwords(str_replace('-',' ', $user->roles->first()->name)) }} ({{ $user->roles->first()->permissions->count() }} perms)
                    </a>
                @else
                    <span class="badge" style="background:var(--accent-light);color:var(--accent);font-size:.75rem;padding:.3rem .75rem;border-radius:20px;">
                        {{ ucwords(str_replace('-',' ', $user->roles->first()->name)) }}
                    </span>
                @endcan
            @else
                <span class="badge" style="background:var(--bg-hover);color:var(--text-muted);font-size:.75rem;padding:.3rem .75rem;border-radius:20px;">
                    No Role Assigned
                </span>
            @endif
        </div>

        <div class="card-glass mb-3">
            <div class="section-title">
                <i data-lucide="mail" style="width:14px;height:14px;color:var(--accent);"></i> Contact & Info
            </div>
            <div class="info-row">
                <span class="info-label">Email</span>
                <span class="info-value"><a href="mailto:{{ $user->email }}" style="color:var(--accent);text-decoration:none;">{{ $user->email }}</a></span>
            </div>
            <div class="info-row">
                <span class="info-label">Phone</span>
                <span class="info-value">{{ $user->phone ?: 'Not provided' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Designation</span>
                <span class="info-value">{{ $user->designation ?: 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Joined</span>
                <span class="info-value">{{ $user->created_at->format('d M Y') }}</span>
            </div>
        </div>

        <div class="card-glass">
            <div class="section-title">
                <i data-lucide="activity" style="width:14px;height:14px;color:var(--accent);"></i> Task Summary
            </div>
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="border-color:var(--border) !important;">
                <span style="font-size:.85rem;color:var(--text-muted);">Assigned Tasks</span>
                <span class="badge badge-primary" style="font-size:.8rem;">{{ $user->assignedTasks->count() }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="border-color:var(--border) !important;">
                <span style="font-size:.85rem;color:var(--text-muted);">Completed Tasks</span>
                <span class="badge" style="background:rgba(16,185,129,0.12);color:var(--success);font-size:.8rem;">
                    {{ $user->assignedTasks->where('status', 'completed')->count() }}
                </span>
            </div>
            <div class="d-flex justify-content-between align-items-center py-2" style="border-color:var(--border) !important;">
                <span style="font-size:.85rem;color:var(--text-muted);">Pending / In Progress</span>
                <span class="badge" style="background:rgba(245,158,11,0.12);color:var(--warning);font-size:.8rem;">
                    {{ $user->assignedTasks->whereIn('status', ['pending', 'in_progress'])->count() }}
                </span>
            </div>
        </div>
    </div>

    {{-- Right Column: Assigned Tasks List --}}
    <div class="col-lg-8">
        <div class="card-glass">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="section-title mb-0">
                    <i data-lucide="check-square" style="width:15px;height:15px;color:var(--accent);"></i>
                    Assigned Tasks ({{ $user->assignedTasks->count() }})
                </div>
            </div>

            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Task Title</th>
                            <th>Client</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Deadline</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($user->assignedTasks as $task)
                        <tr>
                            <td>
                                <a href="{{ route('tasks.show', $task) }}" style="font-weight:600;color:var(--text-primary);text-decoration:none;font-size:.875rem;">
                                    {{ Str::limit($task->title, 40) }}
                                </a>
                                <div style="font-size:.72rem;color:var(--text-muted);">{{ $task->type_label }}</div>
                            </td>
                            <td>
                                @if($task->client)
                                    <a href="{{ route('clients.show', $task->client) }}" style="color:var(--accent);text-decoration:none;font-size:.8rem;">
                                        {{ $task->client->name }}
                                    </a>
                                @else
                                    <span style="color:var(--text-muted);font-size:.8rem;">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $task->priority }}" style="font-size:.72rem;">{{ ucfirst($task->priority) }}</span>
                            </td>
                            <td>
                                <span class="badge status-{{ $task->status }}" style="font-size:.72rem;">{{ ucfirst(str_replace('_',' ',$task->status)) }}</span>
                            </td>
                            <td>
                                <span style="font-size:.78rem;color:{{ $task->is_overdue ? 'var(--danger)' : 'var(--text-muted)' }};">
                                    {{ $task->deadline ? $task->deadline->format('d M Y') : '—' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('tasks.show', $task) }}" class="btn-action" title="View Task">
                                    <i data-lucide="eye" style="width:14px;height:14px;"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4" style="color:var(--text-muted);font-size:.85rem;">
                                No tasks assigned to this team member yet.
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
