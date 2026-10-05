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
                <div style="color: var(--text-primary); font-size: .88rem; line-height: 1.6; background: var(--bg-hover); border: 1px solid var(--border); border-radius: 10px; padding: 1.15rem; white-space: pre-wrap; word-break: break-word;">{{ $task->description }}</div>
            @else
                <div style="text-align: center; padding: 1.5rem; color: var(--text-muted); font-size: .85rem; font-style: italic; background: var(--bg-hover); border-radius: 8px;">
                    No detailed description provided for this task.
                </div>
            @endif
        </div>

        {{-- 3. Task Images & Attachments Card (Only shown if attachments exist) --}}
        @if($task->attachments->count() > 0)
        <div class="card-glass mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2" style="border-color: var(--border) !important;">
                <div class="section-title mb-0 d-flex align-items-center gap-2">
                    <i data-lucide="images" style="width: 17px; height: 17px; color: #ff7828;"></i>
                    Task Images & Attachments
                    <span class="badge" style="background: rgba(255,120,40,0.12); color: #ff7828; font-size: 0.72rem; padding: 3px 8px; border-radius: 10px;">
                        {{ $task->attachments->count() }}
                    </span>
                </div>
                <button type="button" class="btn-ghost" style="padding: .3rem .75rem; font-size: .76rem;" onclick="document.getElementById('uploadCollapse').classList.toggle('d-none')">
                    <i data-lucide="plus" style="width: 13px; height: 13px;"></i> Add Images
                </button>
            </div>

            {{-- Quick Upload Form (Toggleable) --}}
            <div id="uploadCollapse" class="d-none mb-3 p-3 rounded" style="background: rgba(99,102,241,0.03); border: 1px dashed var(--accent);">
                <form method="POST" action="{{ route('tasks.attachments.store', $task) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="image-upload-dropzone mb-2" id="taskShowDropzone" onclick="document.getElementById('showImagesInput').click()">
                        <input type="file" name="images[]" id="showImagesInput" multiple accept="image/*,.pdf,.doc,.docx,.zip" style="display:none;">
                        <i data-lucide="cloud-upload" style="width: 24px; height: 24px; color: var(--accent); margin-bottom: 4px;"></i>
                        <div style="font-weight: 650; font-size: 0.84rem; color: var(--text-primary);">Click or Drag & Drop to upload images</div>
                        <div style="font-size: 0.70rem; color: var(--text-muted);">Multiple files supported. Images are automatically compressed & optimized.</div>
                    </div>
                    <div id="showImagePreviewContainer" class="preview-cards-grid mb-2" style="display: none;"></div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn-ghost btn-sm" onclick="document.getElementById('uploadCollapse').classList.add('d-none')">Cancel</button>
                        <button type="submit" class="btn-gradient btn-sm" style="padding: .35rem .9rem; font-size: .80rem;">
                            <i data-lucide="upload" style="width: 13px; height: 13px;"></i> Upload Files
                        </button>
                    </div>
                </form>
            </div>

            {{-- Image Gallery Grid --}}
            <div class="task-gallery-grid">
                @foreach($task->attachments as $attachment)
                    <div class="task-gallery-item" id="attach-item-{{ $attachment->id }}">
                        @if($attachment->is_image)
                            <div class="task-gallery-thumb" onclick="openImageLightbox('{{ $attachment->url }}', '{{ addslashes($attachment->file_name) }}', '{{ $attachment->formatted_size }}', '{{ $attachment->user->name ?? 'User' }}', '{{ $attachment->created_at->format('d M Y, h:i A') }}', '{{ $attachment->download_url }}')">
                                <img src="{{ $attachment->url }}" alt="{{ $attachment->file_name }}" loading="lazy">
                                <div class="task-gallery-overlay">
                                    <i data-lucide="maximize-2" style="width: 22px; height: 22px;"></i>
                                </div>
                            </div>
                        @else
                            <a href="{{ $attachment->download_url }}" target="_blank" class="task-gallery-thumb d-flex align-items-center justify-content-center text-decoration-none" style="background: rgba(99,102,241,0.06);">
                                <div class="text-center text-accent">
                                    <i data-lucide="{{ $attachment->icon }}" style="width: 36px; height: 36px; color: var(--accent);"></i>
                                    <div style="font-size: 0.70rem; font-weight: 600; margin-top: 4px; color: var(--text-primary);">Document</div>
                                </div>
                            </a>
                        @endif

                        <div class="task-gallery-footer">
                            <div style="min-width: 0;">
                                <div class="task-gallery-name" title="{{ $attachment->file_name }}">
                                    {{ $attachment->file_name }}
                                </div>
                                <div class="task-gallery-size">
                                    {{ $attachment->formatted_size }}
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <a href="{{ $attachment->download_url }}" class="btn-icon" style="width: 26px; height: 26px;" title="Download">
                                    <i data-lucide="download" style="width: 12px; height: 12px;"></i>
                                </a>
                                @php
                                    $canDeleteAttach = auth()->user()->hasRole('super-admin') || auth()->user()->isDefaultSuperAdmin() || (int)$attachment->user_id === (int)auth()->id() || (int)$task->created_by === (int)auth()->id() || auth()->user()->can('edit tasks');
                                @endphp
                                @if($canDeleteAttach)
                                    <button type="button" class="btn-icon text-danger" style="width: 26px; height: 26px; border-color: rgba(239,68,68,0.2);" title="Delete" onclick="deleteTaskAttachment({{ $task->id }}, {{ $attachment->id }})">
                                        <i data-lucide="trash-2" style="width: 12px; height: 12px;"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- 4. Comments & Discussion Card --}}
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

{{-- Image Lightbox Modal --}}
<div class="modal fade" id="imageLightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content" style="background: rgba(15, 23, 42, 0.96); border: 1px solid var(--border); backdrop-filter: blur(16px); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.75);">
            <div class="modal-header border-bottom" style="border-color: var(--border) !important; padding: 0.85rem 1.25rem;">
                <div class="d-flex align-items-center gap-2" style="min-width: 0;">
                    <i data-lucide="image" style="width: 18px; height: 18px; color: var(--accent); flex-shrink: 0;"></i>
                    <h5 class="modal-title text-truncate" id="lightboxTitle" style="font-size: 0.95rem; font-weight: 700; color: var(--text-primary); margin: 0;">Image Preview</h5>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a id="lightboxDownload" href="#" download class="btn-gradient btn-sm" style="padding: 0.35rem 0.8rem; font-size: 0.78rem;">
                        <i data-lucide="download" style="width: 13px; height: 13px;"></i> Download HD
                    </a>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(0.8);"></button>
                </div>
            </div>
            <div class="modal-body text-center p-2 p-md-3" style="min-height: 250px; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.3);">
                <img id="lightboxImage" src="" alt="Preview" style="max-width: 100%; max-height: 78vh; object-fit: contain; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            </div>
            <div class="modal-footer border-top justify-content-between" style="border-color: var(--border) !important; padding: 0.65rem 1.25rem;">
                <div id="lightboxMeta" style="font-size: 0.76rem; color: var(--text-muted);"></div>
                <button type="button" class="btn-ghost btn-sm" data-bs-dismiss="modal" style="font-size: 0.78rem; padding: 0.3rem 0.8rem;">Close</button>
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

/* Gallery & Upload Dropzone Styles */
.task-gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 0.85rem;
}
.task-gallery-item {
    position: relative;
    border-radius: 10px;
    overflow: hidden;
    background: var(--bg-hover);
    border: 1px solid var(--border);
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
}
.task-gallery-item:hover {
    transform: translateY(-2px);
    border-color: rgba(99, 102, 241, 0.4);
    box-shadow: 0 6px 16px rgba(0,0,0,0.25);
}
.task-gallery-thumb {
    position: relative;
    width: 100%;
    height: 120px;
    overflow: hidden;
    cursor: pointer;
    background: rgba(0,0,0,0.15);
}
.task-gallery-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}
.task-gallery-thumb:hover img {
    transform: scale(1.05);
}
.task-gallery-overlay {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.2s ease;
    color: #ffffff;
}
.task-gallery-thumb:hover .task-gallery-overlay {
    opacity: 1;
}
.task-gallery-footer {
    padding: 7px 9px;
    background: var(--bg-card);
    border-top: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.task-gallery-name {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text-primary);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 95px;
}
.task-gallery-size {
    font-size: 0.66rem;
    color: var(--text-muted);
}

.image-upload-dropzone {
    border: 2px dashed var(--border);
    border-radius: 10px;
    padding: 1.25rem 1rem;
    text-align: center;
    background: rgba(99, 102, 241, 0.02);
    cursor: pointer;
    transition: all 0.25s ease;
    position: relative;
}
.image-upload-dropzone:hover,
.image-upload-dropzone.dragover {
    border-color: var(--accent);
    background: rgba(99, 102, 241, 0.08);
}
.preview-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 0.65rem;
    margin-top: 0.75rem;
}
.preview-card-item {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    background: var(--bg-hover);
    border: 1px solid var(--border);
    padding: 5px;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.preview-card-item img {
    width: 100%;
    height: 80px;
    object-fit: cover;
    border-radius: 6px;
}
.preview-card-item .file-placeholder {
    width: 100%;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(99, 102, 241, 0.08);
    border-radius: 6px;
    color: var(--accent);
}
.preview-card-item .preview-info {
    width: 100%;
    margin-top: 4px;
    text-align: center;
}
.preview-card-item .preview-name {
    font-size: 0.70rem;
    font-weight: 600;
    color: var(--text-primary);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
}
.preview-card-item .preview-size {
    font-size: 0.64rem;
    color: var(--text-muted);
}
.preview-card-item .btn-remove-preview {
    position: absolute;
    top: 5px;
    right: 5px;
    width: 20px;
    height: 20px;
    background: rgba(239, 68, 68, 0.9);
    color: white;
    border-radius: 50%;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 13px;
    font-weight: bold;
    z-index: 5;
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

// Open Lightbox
function openImageLightbox(url, title, size, uploader, date, downloadUrl) {
    const modalEl = document.getElementById('imageLightboxModal');
    if (!modalEl) return;

    document.getElementById('lightboxImage').src = url;
    document.getElementById('lightboxTitle').textContent = title;
    document.getElementById('lightboxDownload').href = downloadUrl || url;
    document.getElementById('lightboxDownload').setAttribute('download', title);
    document.getElementById('lightboxMeta').innerHTML = `<strong>Uploaded by:</strong> ${uploader} • <strong>Size:</strong> ${size} • <strong>Date:</strong> ${date}`;

    const modal = new bootstrap.Modal(modalEl);
    modal.show();
    if (window.lucide) lucide.createIcons();
}

// Delete Attachment via AJAX
function deleteTaskAttachment(taskId, attachmentId) {
    if (!confirm('Are you sure you want to delete this attachment?')) return;

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    fetch(`/tasks/${taskId}/attachments/${attachmentId}`, {
        method: 'DELETE',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token
        }
    })
    .then(async res => {
        const data = await res.json().catch(() => ({}));
        if (!res.ok) throw new Error(data.message || 'Failed to delete attachment');
        return data;
    })
    .then(data => {
        const item = document.getElementById(`attach-item-${attachmentId}`);
        if (item) item.remove();
        if (window.showToast) window.showToast('Attachment deleted successfully', 'success');
    })
    .catch(err => {
        if (window.showToast) window.showToast(err.message || 'Error deleting attachment', 'error');
        else alert(err.message || 'Error deleting attachment');
    });
}

// Multi-Image Upload Manager for Show page
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('showImagesInput');
    const dropzone = document.getElementById('taskShowDropzone');
    const previewContainer = document.getElementById('showImagePreviewContainer');
    if (!fileInput || !dropzone || !previewContainer) return;

    let dt = new DataTransfer();

    function formatBytes(bytes) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    }

    function updatePreviews() {
        previewContainer.innerHTML = '';
        if (dt.items.length === 0) {
            previewContainer.style.display = 'none';
            fileInput.files = dt.files;
            return;
        }

        previewContainer.style.display = 'grid';

        Array.from(dt.files).forEach((file, index) => {
            const card = document.createElement('div');
            card.className = 'preview-card-item';

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn-remove-preview';
            removeBtn.innerHTML = '×';
            removeBtn.title = 'Remove this image';
            removeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                dt.items.remove(index);
                fileInput.files = dt.files;
                updatePreviews();
            });

            const isImg = file.type.startsWith('image/') || /\.(jpe?g|png|webp|gif|bmp)$/i.test(file.name);
            if (isImg) {
                const img = document.createElement('img');
                const reader = new FileReader();
                reader.onload = (e) => {
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
                card.appendChild(img);
            } else {
                const placeholder = document.createElement('div');
                placeholder.className = 'file-placeholder';
                placeholder.innerHTML = '<i data-lucide="file-text" style="width:20px;height:20px;"></i>';
                card.appendChild(placeholder);
            }

            const info = document.createElement('div');
            info.className = 'preview-info';
            info.innerHTML = `
                <span class="preview-name" title="${file.name}">${file.name}</span>
                <span class="preview-size">${formatBytes(file.size)}</span>
            `;

            card.appendChild(removeBtn);
            card.appendChild(info);
            previewContainer.appendChild(card);
        });

        fileInput.files = dt.files;
        if (window.lucide) lucide.createIcons();
    }

    fileInput.addEventListener('change', () => {
        for (let i = 0; i < fileInput.files.length; i++) {
            dt.items.add(fileInput.files[i]);
        }
        updatePreviews();
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.add('dragover');
        });
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.remove('dragover');
        });
    });

    dropzone.addEventListener('drop', (e) => {
        const droppedFiles = e.dataTransfer.files;
        if (droppedFiles.length > 0) {
            for (let i = 0; i < droppedFiles.length; i++) {
                dt.items.add(droppedFiles[i]);
            }
            updatePreviews();
        }
    });
});
</script>
@endpush
