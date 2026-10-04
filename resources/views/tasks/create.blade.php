@extends('layouts.app')
@section('title', 'Create New Task')
@section('page-title', 'New Task')

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

<form method="POST" action="{{ route('tasks.store') }}">
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
                        <div class="d-flex align-items-stretch" style="width: 100%;">
                            <select name="client_id" id="task_client_id" class="form-select form-select-dark" style="flex: 1; min-width: 0; border-top-right-radius: 0; border-bottom-right-radius: 0;">
                                <option value="">No client (Internal task)</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id', request('client_id'))==$client->id?'selected':'' }}>
                                        {{ $client->company_name ?? $client->name }} @if($client->name && $client->name !== $client->company_name)({{ $client->name }})@endif
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#quickAddClientModal" style="flex-shrink: 0; background: rgba(255,120,40,0.15); border: 1px solid var(--border); border-left: none; color: #ff7828; border-top-left-radius: 0; border-bottom-left-radius: 0; border-top-right-radius: 8px; border-bottom-right-radius: 8px; display: flex; align-items: center; justify-content: center; padding: 0 14px; transition: all .2s;" title="Add New Client">
                                <i data-lucide="plus" style="width: 18px; height: 18px; stroke-width: 2.5;"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label-enhanced">Description & Notes</label>
                        <textarea name="description" class="form-control form-control-dark" rows="7"
                            placeholder="Write a clear description of the task, requirements, steps to reproduce, or any relevant details...">{{ old('description') }}</textarea>
                    </div>
                </div>
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
                            <input type="text" name="system_url" id="qc_system_url" class="form-control form-control-dark" placeholder="https://pos.clientdomain.com or https://clientwebsite.com" required>
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
            }
            throw new Error(errorMsg);
        }
        return data;
    })
    .then(result => {
        if (result.success && result.client) {
            const clientSelect = document.getElementById('task_client_id') || document.querySelector('select[name="client_id"]');
            if (clientSelect) {
                const newOption = document.createElement('option');
                newOption.value = result.client.id;
                newOption.textContent = result.client.display_name;
                newOption.selected = true;

                clientSelect.appendChild(newOption);
                clientSelect.value = result.client.id;
                clientSelect.dispatchEvent(new Event('change'));
            }

            const form = document.getElementById('quickAddClientForm');
            if (form) form.reset();

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
            alertBox.innerHTML = err.message || 'Something went wrong. Please try again.';
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
    const quickForm = document.getElementById('quickAddClientForm');
    if (quickForm) {
        quickForm.addEventListener('submit', function(e) {
            e.preventDefault();
            window.handleQuickAddClientSubmit(e);
        });
    }
});
</script>
@endpush
