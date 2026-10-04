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
                            <input type="text" name="system_url"
                                class="form-control form-control-dark @error('system_url') is-invalid @enderror"
                                placeholder="https://pos.clientdomain.com or https://clientwebsite.com"
                                value="{{ old('system_url') }}" required>
                        </div>
                        @error('system_url')
                            <div class="invalid-feedback" style="font-size:.75rem;color:var(--danger);margin-top:.25rem;">{{ $message }}</div>
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
                    <button type="submit" class="btn-gradient justify-content-center py-2">
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
