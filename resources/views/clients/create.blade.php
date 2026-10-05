@extends('layouts.app')
@section('title', 'Add Client')
@section('page-title', 'Add Client')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>Add New Client</h2>
        <p>Enter business credentials, system portal URL, and optional contact details</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('clients.index') }}" class="btn-ghost">
            <i data-lucide="arrow-left" style="width:14px;height:14px;"></i> All Clients
        </a>
    </div>
</div>

<form method="POST" action="{{ route('clients.store') }}">
    @csrf
    <div class="row g-3">
        {{-- Left: Primary Business & System Info --}}
        <div class="col-lg-8">
            <div class="card-glass mb-3">
                <div class="section-title">
                    <i data-lucide="building-2" style="width:15px;height:15px;color:var(--accent);"></i>
                    Business & System Credentials
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label-enhanced">Company / Business Name <span class="req">*</span></label>
                        <input type="text" name="company_name"
                            class="form-control form-control-dark @error('company_name') is-invalid @enderror"
                            placeholder="e.g. Ahmed Super Shop / Zenith Tech" value="{{ old('company_name') }}" required autofocus>
                        @error('company_name')
                            <div class="invalid-feedback" style="font-size:.75rem;color:var(--danger);margin-top:.25rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-enhanced">System URL <span class="req">*</span></label>
                        <div class="position-relative">
                            <input type="text" name="system_url" id="client_system_url"
                                class="form-control form-control-dark @error('system_url') is-invalid @enderror"
                                placeholder="https://pos.clientdomain.com or https://clientwebsite.com"
                                value="{{ old('system_url') }}" required autocomplete="off">
                            <span id="urlCheckSpinner" class="spinner-border spinner-border-sm position-absolute end-0 top-50 translate-middle-y me-3 d-none" style="width: 14px; height: 14px; color: var(--accent);" role="status"></span>
                        </div>
                        <div id="urlCheckAlert" class="alert alert-danger d-none mt-2" style="font-size: 0.8rem; padding: 0.5rem 0.75rem; border-radius: 8px; margin-bottom: 0;">
                            <i data-lucide="alert-circle" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle; margin-right: 4px;"></i>
                            <span id="urlAlertText">This URL already exists.</span>
                        </div>
                        <div id="urlSuccessFeedback" class="text-success d-none mt-1" style="font-size: 0.78rem; font-weight: 500;">
                            <i data-lucide="check" style="width: 13px; height: 13px; display: inline-block; vertical-align: middle;"></i> URL is available.
                        </div>
                        @error('system_url')
                            <div class="invalid-feedback d-block" style="font-size:.75rem;color:var(--danger);margin-top:.25rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-enhanced">Contact Person Name <span style="font-weight:400;text-transform:none;font-size:.7rem;color:var(--text-muted);">(Optional)</span></label>
                        <input type="text" name="name"
                            class="form-control form-control-dark @error('name') is-invalid @enderror"
                            placeholder="e.g. Rahel Ahmed (Manager/Owner)" value="{{ old('name') }}">
                        @error('name')
                            <div class="invalid-feedback" style="font-size:.75rem;color:var(--danger);margin-top:.25rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-enhanced">Client Type</label>
                        <select name="type" class="form-select form-select-dark">
                            <option value="pos" {{ old('type','pos')=='pos'?'selected':'' }}>POS System</option>
                            <option value="website" {{ old('type')=='website'?'selected':'' }}>Website</option>
                            <option value="both" {{ old('type')=='both'?'selected':'' }}>POS + Website</option>
                            <option value="other" {{ old('type')=='other'?'selected':'' }}>Other</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label-enhanced">Office / Store Address <span style="font-weight:400;text-transform:none;font-size:.7rem;color:var(--text-muted);">(Optional)</span></label>
                        <textarea name="address" class="form-control form-control-dark" rows="3" placeholder="Full address of the client's store or office...">{{ old('address') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Contact & Actions --}}
        <div class="col-lg-4">
            <div class="card-glass mb-3">
                <div class="section-title">
                    <i data-lucide="contact" style="width:15px;height:15px;color:var(--accent);"></i>
                    Contact Details <span style="font-weight:400;text-transform:none;font-size:.7rem;color:var(--text-muted);">(Optional)</span>
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label-enhanced">Phone Number</label>
                        <input type="text" name="phone" class="form-control form-control-dark" placeholder="01XXXXXXXXX" value="{{ old('phone') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label-enhanced">Email Address</label>
                        <input type="email" name="email" class="form-control form-control-dark" placeholder="client@email.com" value="{{ old('email') }}">
                    </div>
                </div>
            </div>

            <div class="card-glass">
                <div class="section-title">
                    <i data-lucide="check-circle" style="width:15px;height:15px;color:var(--accent);"></i>
                    Save & Actions
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" id="submitClientBtn" class="btn-gradient justify-content-center py-2">
                        <i data-lucide="user-plus" style="width:16px;height:16px;"></i> Add Client
                    </button>
                    <a href="{{ route('clients.index') }}" class="btn-ghost justify-content-center py-2">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlInput = document.getElementById('client_system_url');
    const spinner = document.getElementById('urlCheckSpinner');
    const alertBox = document.getElementById('urlCheckAlert');
    const alertText = document.getElementById('urlAlertText');
    const successFeedback = document.getElementById('urlSuccessFeedback');
    const submitBtn = document.getElementById('submitClientBtn');
    let debounceTimer = null;

    if (urlInput) {
        urlInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const val = this.value.trim();

            if (!val) {
                if (alertBox) alertBox.classList.add('d-none');
                if (successFeedback) successFeedback.classList.add('d-none');
                urlInput.classList.remove('is-invalid');
                return;
            }

            debounceTimer = setTimeout(() => {
                checkUrlUniqueness(val);
            }, 350);
        });

        urlInput.addEventListener('blur', function() {
            const val = this.value.trim();
            if (val) {
                checkUrlUniqueness(val);
            }
        });
    }

    function checkUrlUniqueness(url) {
        if (spinner) spinner.classList.remove('d-none');

        fetch(`{{ route('clients.check-url') }}?url=${encodeURIComponent(url)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (spinner) spinner.classList.add('d-none');
            if (data.exists) {
                urlInput.classList.add('is-invalid');
                if (successFeedback) successFeedback.classList.add('d-none');
                if (alertBox && alertText) {
                    alertText.textContent = data.message || 'This URL already exists! (Already exist)';
                    alertBox.classList.remove('d-none');
                }
                if (window.lucide) lucide.createIcons();
            } else {
                urlInput.classList.remove('is-invalid');
                if (alertBox) alertBox.classList.add('d-none');
                if (successFeedback) {
                    successFeedback.classList.remove('d-none');
                    if (window.lucide) lucide.createIcons();
                }
            }
        })
        .catch(() => {
            if (spinner) spinner.classList.add('d-none');
        });
    }
});
</script>
@endpush
