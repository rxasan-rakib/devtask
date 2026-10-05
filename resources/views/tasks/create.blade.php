@extends('layouts.app')
@section('title', 'Create New Task')
@section('page-title', 'New Task')

@push('styles')
<style>
.image-upload-dropzone {
    border: 2px dashed var(--border);
    border-radius: 12px;
    padding: 1.6rem 1rem;
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
    transform: translateY(-1px);
}
.preview-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
    gap: 0.75rem;
    margin-top: 1rem;
}
.preview-card-item {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    background: var(--bg-hover);
    border: 1px solid var(--border);
    padding: 6px;
    display: flex;
    flex-direction: column;
    align-items: center;
    transition: all 0.2s ease;
}
.preview-card-item img {
    width: 100%;
    height: 90px;
    object-fit: cover;
    border-radius: 6px;
}
.preview-card-item .file-placeholder {
    width: 100%;
    height: 90px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(99, 102, 241, 0.08);
    border-radius: 6px;
    color: var(--accent);
}
.preview-card-item .preview-info {
    width: 100%;
    margin-top: 5px;
    text-align: center;
}
.preview-card-item .preview-name {
    font-size: 0.72rem;
    font-weight: 600;
    color: var(--text-primary);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
}
.preview-card-item .preview-size {
    font-size: 0.65rem;
    color: var(--text-muted);
}
.preview-card-item .btn-remove-preview {
    position: absolute;
    top: 6px;
    right: 6px;
    width: 22px;
    height: 22px;
    background: rgba(239, 68, 68, 0.9);
    color: white;
    border-radius: 50%;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    transition: transform 0.15s ease;
    z-index: 5;
    line-height: 1;
    font-size: 14px;
    font-weight: bold;
}
.preview-card-item .btn-remove-preview:hover {
    transform: scale(1.15);
    background: #dc2626;
}

/* Searchable Client Dropdown */
.searchable-select-wrap {
    position: relative;
}
.searchable-dropdown-list {
    display: none;
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    max-height: 240px;
    overflow-y: auto;
    z-index: 1060;
    background: var(--dropdown-bg, #1e293b);
    border: 1px solid var(--border);
    border-radius: 8px;
    box-shadow: var(--shadow-dropdown, 0 10px 25px -5px rgba(0, 0, 0, 0.4));
    scrollbar-width: thin;
}
.searchable-dropdown-list.dropup {
    top: auto;
    bottom: calc(100% + 4px);
    box-shadow: 0 -10px 25px -5px rgba(0, 0, 0, 0.4);
}
.searchable-dropdown-list::-webkit-scrollbar {
    width: 5px;
}
.searchable-dropdown-list::-webkit-scrollbar-thumb {
    background: var(--border);
    border-radius: 4px;
}
.searchable-item {
    padding: 8px 12px;
    cursor: pointer;
    font-size: 0.83rem;
    color: var(--text-primary);
    border-bottom: 1px solid rgba(148, 163, 184, 0.08);
    transition: all 0.15s ease;
}
.searchable-item:last-child {
    border-bottom: none;
}
.searchable-item:hover,
.searchable-item.focused {
    background: var(--bg-hover, rgba(255,255,255,0.06)) !important;
    color: var(--text-primary) !important;
}
.searchable-item.selected {
    background: rgba(99, 102, 241, 0.14) !important;
    color: var(--accent, #6366f1) !important;
    font-weight: 600;
}
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>Create New Task</h2>
        <p>Fill in the details to create a new work item and assign it to your team</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('tasks.index') }}" class="btn-ghost">
            <i data-lucide="arrow-left" style="width:14px;height:14px;"></i> All Tasks
        </a>
    </div>
</div>

<form method="POST" action="{{ route('tasks.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="row g-3">
        {{-- Left: Main Details --}}
        <div class="col-lg-8">
            <div class="card-glass mb-3">
                <div class="section-title">
                    <i data-lucide="file-text" style="width:15px;height:15px;color:var(--accent);"></i>
                    Task Details
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label-enhanced">Task Title <span class="req">*</span></label>
                        <input type="text" name="title"
                            class="form-control form-control-dark @error('title') is-invalid @enderror"
                            placeholder="e.g. Fix POS receipt printing issue for ABC Store"
                            value="{{ old('title') }}" required autofocus>
                        @error('title')
                            <div class="invalid-feedback" style="font-size:.75rem;color:var(--danger);margin-top:.3rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-enhanced">Task Type <span class="req">*</span></label>
                        <select name="task_type_id" class="form-select form-select-dark @error('task_type_id') is-invalid @enderror" required>
                            <option value="">Select Task Type</option>
                            @foreach($taskTypes as $type)
                                <option value="{{ $type->id }}" {{ old('task_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('task_type_id')
                            <div class="invalid-feedback" style="font-size:.75rem;color:var(--danger);margin-top:.3rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-enhanced">Associated Client</label>
                        @php
                            $selectedClientId = old('client_id', request('client_id'));
                            $selectedClient = $selectedClientId ? $clients->firstWhere('id', $selectedClientId) : null;
                            $selectedClientName = '';
                            if ($selectedClient) {
                                $cName = $selectedClient->company_name ?: $selectedClient->name;
                                $selectedClientName = $cName . (($selectedClient->name && $selectedClient->name !== $selectedClient->company_name) ? ' (' . $selectedClient->name . ')' : '');
                            }
                        @endphp
                        <div class="searchable-select-wrap d-flex align-items-stretch" id="taskClientSelectWrapper" style="width: 100%; position: relative;">
                            <div style="position: relative; flex: 1; min-width: 0;">
                                <i data-lucide="building-2" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:var(--text-muted);pointer-events:none;z-index:2;"></i>
                                
                                <input type="text" 
                                       id="taskClientSearchInput" 
                                       class="form-control form-control-dark @error('client_id') is-invalid @enderror" 
                                       style="padding-left:34px;padding-right:54px;border-top-right-radius:0;border-bottom-right-radius:0;cursor:pointer;" 
                                       placeholder="No client (Internal task) - Search or select..." 
                                       autocomplete="off"
                                       value="{{ $selectedClientName }}">

                                <input type="hidden" name="client_id" id="task_client_id" value="{{ $selectedClientId }}">

                                <div style="position:absolute;right:8px;top:50%;transform:translateY(-50%);display:flex;align-items:center;gap:4px;z-index:3;">
                                    <button type="button" 
                                            id="taskClientClearBtn" 
                                            style="padding:2px 4px;border:none;background:transparent;color:var(--text-muted);display:{{ $selectedClientId ? 'block' : 'none' }};cursor:pointer;" 
                                            title="Clear client selection">
                                        <i data-lucide="x" style="width:13px;height:13px;"></i>
                                    </button>
                                    <i data-lucide="chevron-down" id="taskClientChevron" style="width:14px;height:14px;color:var(--text-muted);pointer-events:none;transition:transform .2s;"></i>
                                </div>

                                {{-- Dropdown Menu --}}
                                <div id="taskClientDropdownMenu" class="searchable-dropdown-list">
                                    <div class="searchable-item {{ !$selectedClientId ? 'selected' : '' }}" 
                                         data-id="" 
                                         data-name="" 
                                         data-search="no client internal task none" 
                                         style="font-weight:600; color:var(--text-muted); border-bottom:1px solid var(--border); display:flex; align-items:center; gap:6px;">
                                        <i data-lucide="minus-circle" style="width:13px;height:13px;"></i> No client (Internal task)
                                    </div>
                                    @foreach($clients as $client)
                                        @php 
                                            $cName = $client->company_name ?: $client->name;
                                            $cSub = ($client->company_name && $client->name && $client->name !== $client->company_name) ? $client->name : ($client->phone ?: $client->system_url);
                                            $cDisplay = $cName . (($client->name && $client->name !== $client->company_name) ? ' (' . $client->name . ')' : '');
                                            $isSelected = ($selectedClientId == $client->id);
                                            $searchMeta = strtolower(($client->company_name ?? '') . ' ' . ($client->name ?? '') . ' ' . ($client->phone ?? '') . ' ' . ($client->system_url ?? '') . ' ' . ($client->email ?? ''));
                                        @endphp
                                        <div class="searchable-item {{ $isSelected ? 'selected' : '' }}" 
                                             data-id="{{ $client->id }}" 
                                             data-name="{{ $cDisplay }}" 
                                             data-search="{{ $searchMeta }}">
                                            <div style="font-weight:600; color:var(--text-primary);">
                                                {{ $client->company_name ?? $client->name }}
                                                @if($client->name && $client->name !== $client->company_name)
                                                    <span style="font-weight:400; color:var(--text-muted); font-size:0.75rem;">({{ $client->name }})</span>
                                                @endif
                                            </div>
                                            @if($cSub && $cSub !== $client->name)
                                                <div style="font-size:.70rem; color:var(--text-muted);">{{ $cSub }}</div>
                                            @endif
                                        </div>
                                    @endforeach
                                    <div id="noTaskClientMatch" style="display:none; padding:10px 12px; font-size:.80rem; color:var(--text-muted); text-align:center;">
                                        No matching clients found
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#quickAddClientModal" style="flex-shrink: 0; background: rgba(255,120,40,0.15); border: 1px solid var(--border); border-left: none; color: #ff7828; border-top-left-radius: 0; border-bottom-left-radius: 0; border-top-right-radius: 8px; border-bottom-right-radius: 8px; display: flex; align-items: center; justify-content: center; padding: 0 14px; transition: all .2s;" title="Add New Client">
                                <i data-lucide="plus" style="width: 18px; height: 18px; stroke-width: 2.5;"></i>
                            </button>
                        </div>
                        @error('client_id')
                            <div class="invalid-feedback d-block" style="font-size:.75rem;color:var(--danger);margin-top:.3rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label-enhanced">Description & Notes</label>
                        <textarea name="description" class="form-control form-control-dark" rows="6"
                            placeholder="Write a clear description of the task, requirements, steps to reproduce, or any relevant details...">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Images & Attachments Card --}}
            <div class="card-glass mb-3">
                <div class="section-title d-flex align-items-center justify-content-between mb-3">
                    <span class="d-flex align-items-center gap-2">
                        <i data-lucide="images" style="width:16px;height:16px;color:var(--accent);"></i>
                        Task Images & Attachments
                    </span>
                    <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--accent); font-size: 0.70rem; font-weight: 600;">
                        Auto-Compressed & HD Quality
                    </span>
                </div>

                <div class="image-upload-dropzone" id="taskImageDropzone" onclick="document.getElementById('taskImagesInput').click()">
                    <input type="file" name="images[]" id="taskImagesInput" multiple accept="image/*,.pdf,.doc,.docx,.zip" style="display:none;">
                    <div style="width: 44px; height: 44px; margin: 0 auto 8px; background: rgba(99,102,241,0.12); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--accent);">
                        <i data-lucide="cloud-upload" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div style="font-weight: 650; font-size: 0.88rem; color: var(--text-primary); margin-bottom: 3px;">
                        Click to browse or drag & drop images
                    </div>
                    <div style="font-size: 0.74rem; color: var(--text-muted); max-width: 480px; margin: 0 auto;">
                        Upload as many images as you need (JPG, PNG, WebP, GIF, PDF). Large files are automatically compressed & optimized while retaining crystal-clear quality.
                    </div>
                </div>

                {{-- Live Image Previews --}}
                <div id="imagePreviewContainer" class="preview-cards-grid" style="display: none;"></div>
            </div>
        </div>

        {{-- Right: Meta & Actions --}}
        <div class="col-lg-4">
            {{-- Assignment & Scheduling Card --}}
            <div class="card-glass mb-3">
                <div class="section-title">
                    <i data-lucide="sliders" style="width:15px;height:15px;color:var(--accent);"></i>
                    Assignment & Priority
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label-enhanced">Assign To (Developer / Member)</label>
                        <select name="assigned_to" class="form-select form-select-dark">
                            <option value="">Unassigned</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label-enhanced">Priority <span class="req">*</span></label>
                        @php $defaultPriority = \App\Models\Setting::get('default_task_priority', 'medium'); @endphp
                        <select name="priority" class="form-select form-select-dark" required>
                            <option value="medium" {{ old('priority', $defaultPriority) == 'medium' ? 'selected' : '' }}>Medium Priority</option>
                            <option value="high" {{ old('priority', $defaultPriority) == 'high' ? 'selected' : '' }}>High (Urgent)</option>
                            <option value="low" {{ old('priority', $defaultPriority) == 'low' ? 'selected' : '' }}>Low Priority</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label-enhanced">Initial Status <span class="req">*</span></label>
                        <select name="status" class="form-select form-select-dark" required>
                            <option value="pending" {{ old('status','pending')=='pending'?'selected':'' }}>Pending</option>
                            <option value="in_progress" {{ old('status')=='in_progress'?'selected':'' }}>In Progress</option>
                            <option value="on_hold" {{ old('status')=='on_hold'?'selected':'' }}>On Hold</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label-enhanced">Deadline / Due Date</label>
                        <input type="datetime-local" name="deadline" class="form-control form-control-dark" value="{{ old('deadline') }}">
                    </div>
                </div>
            </div>

            {{-- Actions Card --}}
            <div class="card-glass">
                <div class="section-title">
                    <i data-lucide="check-circle" style="width:15px;height:15px;color:var(--accent);"></i>
                    Save & Actions
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn-gradient justify-content-center py-2">
                        <i data-lucide="plus-circle" style="width:16px;height:16px;"></i> Create Task
                    </button>
                    <a href="{{ route('tasks.index') }}" class="btn-ghost justify-content-center py-2">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- Quick Add Client Modal --}}
<div class="modal fade" id="quickAddClientModal" tabindex="-1" aria-labelledby="quickAddClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content card-glass" style="background: var(--bg-card); border: 1px solid var(--border); box-shadow: 0 20px 40px rgba(0,0,0,0.4);">
            <div class="modal-header border-bottom" style="border-color: var(--border) !important; padding: 1rem 1.25rem;">
                <h5 class="modal-title d-flex align-items-center gap-2" id="quickAddClientModalLabel" style="font-weight: 700; font-size: 1rem; color: var(--text-primary);">
                    <i data-lucide="building-2" style="width: 18px; height: 18px; color: #ff7828;"></i>
                    Add New Client
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(0.5);"></button>
            </div>
            <form id="quickAddClientForm">
                @csrf
                <div class="modal-body" style="padding: 1.25rem;">
                    {{-- Alert Box for Errors --}}
                    <div id="quickClientAlert" class="alert alert-danger d-none" style="font-size: 0.82rem; padding: 0.6rem 0.9rem; border-radius: 8px;"></div>

                    <div class="row g-3">
                        {{-- Company / Business Name --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label-enhanced">Company / Business Name <span class="req">*</span></label>
                            <input type="text" name="company_name" id="qc_company_name" class="form-control form-control-dark" placeholder="e.g. Ahmed Super Shop / Zenith Tech" required>
                        </div>

                        {{-- System URL --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label-enhanced">System URL <span class="req">*</span></label>
                            <div class="position-relative">
                                <input type="text" name="system_url" id="qc_system_url" class="form-control form-control-dark" placeholder="https://pos.clientdomain.com or https://clientwebsite.com" required autocomplete="off">
                                <span id="qc_url_spinner" class="spinner-border spinner-border-sm position-absolute end-0 top-50 translate-middle-y me-3 d-none" style="width: 14px; height: 14px; color: var(--accent);" role="status"></span>
                            </div>
                            <div id="qc_url_alert" class="alert alert-danger d-none mt-2" style="font-size: 0.8rem; padding: 0.45rem 0.75rem; border-radius: 6px; margin-bottom: 0;">
                                <i data-lucide="alert-circle" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle; margin-right: 4px;"></i>
                                <span id="qc_url_alert_text">This URL already exists.</span>
                            </div>
                            <div id="qc_url_success" class="text-success d-none mt-1" style="font-size: 0.78rem; font-weight: 500;">
                                <i data-lucide="check" style="width: 13px; height: 13px; display: inline-block; vertical-align: middle;"></i> URL is available.
                            </div>
                        </div>

                        {{-- Contact Person Name --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label-enhanced">Contact Person Name <span style="font-weight:400;text-transform:none;font-size:.7rem;color:var(--text-muted);">(Optional)</span></label>
                            <input type="text" name="name" id="qc_name" class="form-control form-control-dark" placeholder="e.g. Rahel Ahmed (Manager/Owner)">
                        </div>

                        {{-- Client Type --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label-enhanced">Client Type</label>
                            <select name="type" id="qc_type" class="form-select form-select-dark">
                                <option value="pos">POS System</option>
                                <option value="website">Website</option>
                                <option value="both">POS + Website</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        {{-- Phone Number --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label-enhanced">Phone Number <span style="font-weight:400;text-transform:none;font-size:.7rem;color:var(--text-muted);">(Optional)</span></label>
                            <input type="text" name="phone" id="qc_phone" class="form-control form-control-dark" placeholder="01XXXXXXXXX">
                        </div>

                        {{-- Email Address --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label-enhanced">Email Address <span style="font-weight:400;text-transform:none;font-size:.7rem;color:var(--text-muted);">(Optional)</span></label>
                            <input type="email" name="email" id="qc_email" class="form-control form-control-dark" placeholder="client@email.com">
                        </div>

                        {{-- Address --}}
                        <div class="col-12">
                            <label class="form-label-enhanced">Office / Store Address <span style="font-weight:400;text-transform:none;font-size:.7rem;color:var(--text-muted);">(Optional)</span></label>
                            <textarea name="address" id="qc_address" class="form-control form-control-dark" rows="3" placeholder="Full address of the client's store or office..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top" style="border-color: var(--border) !important; padding: 0.85rem 1.25rem;">
                    <button type="button" class="btn-ghost" data-bs-dismiss="modal" style="padding: 0.45rem 1rem;">Cancel</button>
                    <button type="button" id="saveQuickClientBtn" onclick="handleQuickAddClientSubmit(event)" class="btn-gradient" style="padding: 0.45rem 1.25rem;">
                        <span id="saveQuickClientText"><i data-lucide="user-plus" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle;"></i> Add Client</span>
                        <span id="saveQuickClientSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
window.handleQuickAddClientSubmit = function(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }

    const alertBox = document.getElementById('quickClientAlert');
    const submitBtn = document.getElementById('saveQuickClientBtn');
    const submitText = document.getElementById('saveQuickClientText');
    const submitSpinner = document.getElementById('saveQuickClientSpinner');

    const companyInput = document.getElementById('qc_company_name');
    const urlInput = document.getElementById('qc_system_url');
    const nameInput = document.getElementById('qc_name');
    const typeInput = document.getElementById('qc_type');
    const phoneInput = document.getElementById('qc_phone');
    const emailInput = document.getElementById('qc_email');
    const addressInput = document.getElementById('qc_address');

    if (alertBox) {
        alertBox.classList.add('d-none');
        alertBox.innerHTML = '';
    }
    if (urlInput) urlInput.classList.remove('is-invalid');

    const companyVal = companyInput ? companyInput.value.trim() : '';
    const urlVal = urlInput ? urlInput.value.trim() : '';

    if (!companyVal) {
        if (alertBox) {
            alertBox.innerHTML = '<strong>Validation:</strong> Please enter Company / Business Name.';
            alertBox.classList.remove('d-none');
        }
        if (companyInput) companyInput.focus();
        return false;
    }

    if (!urlVal) {
        if (alertBox) {
            alertBox.innerHTML = '<strong>Validation:</strong> Please enter System URL (e.g. https://clientwebsite.com).';
            alertBox.classList.remove('d-none');
        }
        if (urlInput) urlInput.focus();
        return false;
    }

    if (submitBtn) submitBtn.disabled = true;
    if (submitText) submitText.classList.add('d-none');
    if (submitSpinner) submitSpinner.classList.remove('d-none');

    const payload = {
        company_name: companyVal,
        system_url: urlVal,
        name: nameInput ? nameInput.value.trim() : '',
        type: typeInput ? typeInput.value : 'pos',
        phone: phoneInput ? phoneInput.value.trim() : '',
        email: emailInput ? emailInput.value.trim() : '',
        address: addressInput ? addressInput.value.trim() : '',
        _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
    };

    fetch('{{ route("clients.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': payload._token
        },
        body: JSON.stringify(payload)
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
            let errorMsg = data.message || 'Validation error occurred.';
            if (data.errors) {
                const errorList = Object.values(data.errors).flat().join('<br>');
                errorMsg = errorList || errorMsg;
                if (data.errors.system_url && urlInput) {
                    urlInput.classList.add('is-invalid');
                }
            }
            throw new Error(errorMsg);
        }
        return data;
    })
    .then(result => {
        if (result.success && result.client) {
            const clientHidden = document.getElementById('task_client_id');
            const clientInput = document.getElementById('taskClientSearchInput');
            const clientClearBtn = document.getElementById('taskClientClearBtn');
            const dropdownMenu = document.getElementById('taskClientDropdownMenu');
            const dispName = result.client.display_name || result.client.company_name || result.client.name;

            if (dropdownMenu) {
                let existingItem = dropdownMenu.querySelector(`[data-id="${result.client.id}"]`);
                if (!existingItem) {
                    existingItem = document.createElement('div');
                    existingItem.className = 'searchable-item';
                    existingItem.setAttribute('data-id', result.client.id);
                    existingItem.setAttribute('data-name', dispName);
                    const searchStr = `${result.client.company_name || ''} ${result.client.name || ''} ${result.client.phone || ''} ${result.client.system_url || ''} ${result.client.email || ''}`.toLowerCase();
                    existingItem.setAttribute('data-search', searchStr);
                    existingItem.innerHTML = `
                        <div style="font-weight:600; color:var(--text-primary);">${dispName}</div>
                        ${result.client.phone || result.client.system_url ? `<div style="font-size:.70rem; color:var(--text-muted);">${result.client.phone || result.client.system_url}</div>` : ''}
                    `;
                    const noClientItem = dropdownMenu.querySelector('[data-id=""]');
                    if (noClientItem && noClientItem.nextSibling) {
                        dropdownMenu.insertBefore(existingItem, noClientItem.nextSibling);
                    } else {
                        dropdownMenu.appendChild(existingItem);
                    }
                }
            }

            if (window.taskSelectClient) {
                window.taskSelectClient(result.client.id, dispName);
            } else {
                if (clientHidden) clientHidden.value = result.client.id;
                if (clientInput) clientInput.value = dispName;
                if (clientClearBtn) clientClearBtn.style.display = 'block';
            }

            const form = document.getElementById('quickAddClientForm');
            if (form) form.reset();
            const qcAlert = document.getElementById('qc_url_alert');
            const qcSuccess = document.getElementById('qc_url_success');
            if (qcAlert) qcAlert.classList.add('d-none');
            if (qcSuccess) qcSuccess.classList.add('d-none');

            const modalElement = document.getElementById('quickAddClientModal');
            if (modalElement) {
                if (window.bootstrap) {
                    const modalInstance = bootstrap.Modal.getInstance(modalElement) || bootstrap.Modal.getOrCreateInstance(modalElement);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                }
                modalElement.classList.remove('show');
                modalElement.style.display = 'none';
                document.body.classList.remove('modal-open');
                document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
            }

            if (window.showToast) {
                window.showToast(result.message || 'Client added and selected!', 'success');
            }
        }
    })
    .catch(err => {
        if (alertBox) {
            alertBox.innerHTML = '<strong>Alert:</strong> ' + (err.message || 'Something went wrong. Please try again.');
            alertBox.classList.remove('d-none');
        }
    })
    .finally(() => {
        if (submitBtn) submitBtn.disabled = false;
        if (submitText) submitText.classList.remove('d-none');
        if (submitSpinner) submitSpinner.classList.add('d-none');
        if (window.lucide) {
            lucide.createIcons();
        }
    });

    return false;
};

document.addEventListener('DOMContentLoaded', function() {
    // Searchable Client Dropdown Handler
    (function() {
        const wrap = document.getElementById('taskClientSelectWrapper');
        const input = document.getElementById('taskClientSearchInput');
        const hiddenId = document.getElementById('task_client_id');
        const menu = document.getElementById('taskClientDropdownMenu');
        const clearBtn = document.getElementById('taskClientClearBtn');
        const noMatch = document.getElementById('noTaskClientMatch');
        const chevron = document.getElementById('taskClientChevron');

        if (!wrap || !input || !hiddenId || !menu) return;

        let activeIndex = -1;

        function getVisibleItems() {
            return Array.from(menu.querySelectorAll('.searchable-item')).filter(el => el.style.display !== 'none');
        }

        function filterItems(query) {
            const q = query.toLowerCase().trim();
            const items = menu.querySelectorAll('.searchable-item');
            let visibleCount = 0;

            items.forEach(item => {
                const id = item.getAttribute('data-id');
                const searchStr = (item.getAttribute('data-search') || '').toLowerCase();
                const nameStr = (item.getAttribute('data-name') || '').toLowerCase();

                if (!q) {
                    item.style.display = 'block';
                    visibleCount++;
                } else if (id === '') {
                    if (searchStr.includes(q)) {
                        item.style.display = 'block';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                } else if (searchStr.includes(q) || nameStr.includes(q)) {
                    item.style.display = 'block';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            if (noMatch) {
                noMatch.style.display = visibleCount === 0 ? 'block' : 'none';
            }
            activeIndex = -1;
            updateActiveItem();
        }

        function updateActiveItem() {
            const visible = getVisibleItems();
            visible.forEach((el, idx) => {
                if (idx === activeIndex) {
                    el.classList.add('focused');
                    el.scrollIntoView({ block: 'nearest' });
                } else {
                    el.classList.remove('focused');
                }
            });
        }

        function updateDropdownPosition() {
            const rect = wrap.getBoundingClientRect();
            const menuHeight = 250;
            const spaceBelow = window.innerHeight - rect.bottom;
            const spaceAbove = rect.top;

            if (spaceBelow < menuHeight && spaceAbove > spaceBelow) {
                menu.classList.add('dropup');
            } else {
                menu.classList.remove('dropup');
            }
        }

        function openDropdown() {
            menu.style.display = 'block';
            filterItems('');
            updateDropdownPosition();
            if (chevron) chevron.style.transform = 'rotate(180deg)';
            
            const currentId = hiddenId.value;
            if (currentId) {
                const selectedEl = menu.querySelector(`.searchable-item[data-id="${currentId}"]`);
                if (selectedEl) {
                    selectedEl.scrollIntoView({ block: 'nearest' });
                }
            }
        }

        function closeDropdown() {
            menu.style.display = 'none';
            menu.classList.remove('dropup');
            if (chevron) chevron.style.transform = 'rotate(0deg)';
            activeIndex = -1;
            updateActiveItem();
        }

        function selectClient(id, displayName) {
            hiddenId.value = id || '';
            input.value = displayName || '';
            
            const items = menu.querySelectorAll('.searchable-item');
            items.forEach(i => {
                if (i.getAttribute('data-id') == (id || '')) {
                    i.classList.add('selected');
                } else {
                    i.classList.remove('selected');
                }
            });

            if (clearBtn) {
                clearBtn.style.display = id ? 'block' : 'none';
            }

            closeDropdown();
        }

        // Focus / Click opens dropdown and selects text for quick replacement
        input.addEventListener('focus', () => {
            openDropdown();
            setTimeout(() => {
                input.select();
            }, 50);
        });

        input.addEventListener('click', (e) => {
            if (menu.style.display !== 'block') {
                openDropdown();
                input.select();
            }
        });

        // Real-time typing in Client field
        input.addEventListener('input', () => {
            if (menu.style.display !== 'block') {
                menu.style.display = 'block';
                if (chevron) chevron.style.transform = 'rotate(180deg)';
            }
            filterItems(input.value);
            if (input.value.trim() === '') {
                hiddenId.value = '';
                if (clearBtn) clearBtn.style.display = 'none';
            } else {
                if (clearBtn) clearBtn.style.display = 'block';
            }
        });

        // Keyboard navigation
        input.addEventListener('keydown', (e) => {
            const visible = getVisibleItems();
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (menu.style.display !== 'block') {
                    openDropdown();
                    return;
                }
                if (visible.length > 0) {
                    activeIndex = (activeIndex + 1) % visible.length;
                    updateActiveItem();
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (menu.style.display !== 'block') {
                    openDropdown();
                    return;
                }
                if (visible.length > 0) {
                    activeIndex = (activeIndex - 1 + visible.length) % visible.length;
                    updateActiveItem();
                }
            } else if (e.key === 'Enter') {
                if (menu.style.display === 'block' && activeIndex >= 0 && visible[activeIndex]) {
                    e.preventDefault();
                    const item = visible[activeIndex];
                    selectClient(item.getAttribute('data-id'), item.getAttribute('data-name'));
                }
            } else if (e.key === 'Escape') {
                closeDropdown();
            }
        });

        // Client item selection delegation
        menu.addEventListener('click', (e) => {
            const item = e.target.closest('.searchable-item');
            if (item) {
                const id = item.getAttribute('data-id');
                const name = item.getAttribute('data-name');
                selectClient(id, name);
            }
        });

        // Clear Client selection
        if (clearBtn) {
            clearBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                selectClient('', '');
                input.focus();
            });
        }

        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if (!wrap.contains(e.target)) {
                closeDropdown();
                const currentId = hiddenId.value;
                if (currentId) {
                    const selectedItem = menu.querySelector(`.searchable-item[data-id="${currentId}"]`);
                    if (selectedItem) {
                        input.value = selectedItem.getAttribute('data-name') || '';
                    }
                } else if (!hiddenId.value) {
                    input.value = '';
                    if (clearBtn) clearBtn.style.display = 'none';
                }
            }
        });

        // Expose selectClient to window so Quick Add Client Modal can call it
        window.taskSelectClient = selectClient;
    })();

    const quickForm = document.getElementById('quickAddClientForm');
    if (quickForm) {
        quickForm.addEventListener('submit', function(e) {
            e.preventDefault();
            window.handleQuickAddClientSubmit(e);
        });
    }

    const qcUrl = document.getElementById('qc_system_url');
    const qcSpinner = document.getElementById('qc_url_spinner');
    const qcAlert = document.getElementById('qc_url_alert');
    const qcAlertText = document.getElementById('qc_url_alert_text');
    const qcSuccess = document.getElementById('qc_url_success');
    let qcTimer = null;

    if (qcUrl) {
        qcUrl.addEventListener('input', function() {
            clearTimeout(qcTimer);
            const val = this.value.trim();
            if (!val) {
                if (qcAlert) qcAlert.classList.add('d-none');
                if (qcSuccess) qcSuccess.classList.add('d-none');
                qcUrl.classList.remove('is-invalid');
                return;
            }
            qcTimer = setTimeout(() => {
                checkQuickClientUrl(val);
            }, 350);
        });

        qcUrl.addEventListener('blur', function() {
            const val = this.value.trim();
            if (val) checkQuickClientUrl(val);
        });
    }

    function checkQuickClientUrl(url) {
        if (qcSpinner) qcSpinner.classList.remove('d-none');

        fetch(`{{ route('clients.check-url') }}?url=${encodeURIComponent(url)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (qcSpinner) qcSpinner.classList.add('d-none');
            if (data.exists) {
                qcUrl.classList.add('is-invalid');
                if (qcSuccess) qcSuccess.classList.add('d-none');
                if (qcAlert && qcAlertText) {
                    qcAlertText.textContent = data.message || 'This URL already exists! (Already exist)';
                    qcAlert.classList.remove('d-none');
                }
                if (window.lucide) lucide.createIcons();
            } else {
                qcUrl.classList.remove('is-invalid');
                if (qcAlert) qcAlert.classList.add('d-none');
                if (qcSuccess) {
                    qcSuccess.classList.remove('d-none');
                    if (window.lucide) lucide.createIcons();
                }
            }
        })
        .catch(() => {
            if (qcSpinner) qcSpinner.classList.add('d-none');
        });
    }
    // Multi-Image Upload Manager with DataTransfer
    (function() {
        const fileInput = document.getElementById('taskImagesInput');
        const dropzone = document.getElementById('taskImageDropzone');
        const previewContainer = document.getElementById('imagePreviewContainer');
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
                    placeholder.innerHTML = '<i data-lucide="file-text" style="width:24px;height:24px;"></i>';
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
    })();
});
</script>
@endpush
