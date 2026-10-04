@extends('layouts.app')
@section('title', 'Edit Client')
@section('page-title', 'Edit Client')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>Edit Client</h2>
        <p>{{ $client->company_name ?? $client->name }}</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('clients.show', $client) }}" class="btn-ghost">
            <i data-lucide="arrow-left" style="width:14px;height:14px;"></i> View Client
        </a>
    </div>
</div>

<form method="POST" action="{{ route('clients.update', $client) }}">
    @csrf @method('PUT')
    <div class="row g-3">
        {{-- Left: Primary Business & System Details --}}
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
                            value="{{ old('company_name', $client->company_name ?? $client->name) }}" required>
                        @error('company_name')
                            <div class="invalid-feedback" style="font-size:.75rem;color:var(--danger);margin-top:.25rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-enhanced">System URL <span class="req">*</span></label>
                        <input type="text" name="system_url"
                            class="form-control form-control-dark @error('system_url') is-invalid @enderror"
                            placeholder="https://pos.clientdomain.com or https://clientwebsite.com"
                            value="{{ old('system_url', $client->system_url) }}" required>
                        @error('system_url')
                            <div class="invalid-feedback" style="font-size:.75rem;color:var(--danger);margin-top:.25rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-enhanced">Contact Person Name <span style="font-weight:400;text-transform:none;font-size:.7rem;color:var(--text-muted);">(Optional)</span></label>
                        <input type="text" name="name"
                            class="form-control form-control-dark @error('name') is-invalid @enderror"
                            value="{{ old('name', $client->name) }}" placeholder="e.g. Rahel Ahmed">
                        @error('name')
                            <div class="invalid-feedback" style="font-size:.75rem;color:var(--danger);margin-top:.25rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-enhanced">Client Type</label>
                        <select name="type" class="form-select form-select-dark">
                            @foreach(['pos'=>'POS System','website'=>'Website','both'=>'POS + Website','other'=>'Other'] as $v=>$l)
                                <option value="{{ $v }}" {{ old('type', $client->type)==$v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label-enhanced">Office / Store Address <span style="font-weight:400;text-transform:none;font-size:.7rem;color:var(--text-muted);">(Optional)</span></label>
                        <textarea name="address" class="form-control form-control-dark" rows="3">{{ old('address', $client->address) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Contact, Status & Actions --}}
        <div class="col-lg-4">
            <div class="card-glass mb-3">
                <div class="section-title">
                    <i data-lucide="sliders" style="width:15px;height:15px;color:var(--accent);"></i>
                    Contact & Status <span style="font-weight:400;text-transform:none;font-size:.7rem;color:var(--text-muted);">(Optional)</span>
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label-enhanced">Account Status</label>
                        <select name="is_active" class="form-select form-select-dark">
                            <option value="1" {{ old('is_active', $client->is_active) ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ !old('is_active', $client->is_active) ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label-enhanced">Phone Number</label>
                        <input type="text" name="phone" class="form-control form-control-dark" value="{{ old('phone', $client->phone) }}" placeholder="01XXXXXXXXX">
                    </div>
                    <div class="col-12">
                        <label class="form-label-enhanced">Email Address</label>
                        <input type="email" name="email" class="form-control form-control-dark" value="{{ old('email', $client->email) }}" placeholder="client@email.com">
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
                        <i data-lucide="save" style="width:16px;height:16px;"></i> Save Changes
                    </button>
                    <a href="{{ route('clients.show', $client) }}" class="btn-ghost justify-content-center py-2">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
