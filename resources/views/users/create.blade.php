@extends('layouts.app')
@section('title', 'Add Team Member')
@section('page-title', 'Add Team Member')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>Add Team Member</h2>
        <p>Invite or create a new user account with assigned role and permissions</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('users.index') }}" class="btn-ghost">
            <i data-lucide="arrow-left" style="width:14px;height:14px;"></i> All Members
        </a>
    </div>
</div>

<form method="POST" action="{{ route('users.store') }}">
    @csrf
    <div class="row g-3">
        {{-- Left: User Info --}}
        <div class="col-lg-8">
            <div class="card-glass mb-3">
                <div class="section-title">
                    <i data-lucide="user" style="width:15px;height:15px;color:var(--accent);"></i>
                    Personal Details
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label-enhanced">Full Name <span class="req">*</span></label>
                        <input type="text" name="name" class="form-control form-control-dark @error('name') is-invalid @enderror"
                            placeholder="e.g. Rakibul Hasan" value="{{ old('name') }}" required autofocus>
                        @error('name')
                            <div class="invalid-feedback" style="font-size:.75rem;color:var(--danger);margin-top:.25rem;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-enhanced">Email Address <span class="req">*</span></label>
                        <input type="email" name="email" class="form-control form-control-dark @error('email') is-invalid @enderror"
                            placeholder="user@company.com" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback" style="font-size:.75rem;color:var(--danger);margin-top:.25rem;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-enhanced">Phone Number</label>
                        <input type="text" name="phone" class="form-control form-control-dark" placeholder="01XXXXXXXXX" value="{{ old('phone') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-enhanced">Designation / Job Title</label>
                        <input type="text" name="designation" class="form-control form-control-dark" placeholder="e.g. Senior POS Engineer" value="{{ old('designation') }}">
                    </div>
                </div>
            </div>

            <div class="card-glass">
                <div class="section-title">
                    <i data-lucide="lock" style="width:15px;height:15px;color:var(--accent);"></i>
                    Security & Credentials
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label-enhanced">Password <span class="req">*</span></label>
                        <input type="password" name="password" class="form-control form-control-dark @error('password') is-invalid @enderror"
                            placeholder="Minimum 8 characters" required>
                        @error('password')
                            <div class="invalid-feedback" style="font-size:.75rem;color:var(--danger);margin-top:.25rem;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-enhanced">Confirm Password <span class="req">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control form-control-dark" required>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Role & Actions --}}
        <div class="col-lg-4">
            <div class="card-glass mb-3">
                <div class="section-title">
                    <i data-lucide="shield-check" style="width:15px;height:15px;color:var(--accent);"></i>
                    Role & Access
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label class="form-label-enhanced mb-0">System Role <span class="req">*</span></label>
                            @can('view roles')
                                <a href="{{ route('roles.create') }}" target="_blank" style="font-size:0.70rem;color:var(--accent);text-decoration:none;">
                                    + Custom Role
                                </a>
                            @endcan
                        </div>
                        <select name="role" class="form-select form-select-dark" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ old('role')==$role->name?'selected':'' }}>
                                    {{ ucwords(str_replace('-',' ',$role->name)) }} ({{ $role->permissions_count ?? $role->permissions->count() }} permissions)
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text mt-1" style="font-size:0.72rem;color:var(--text-muted);">
                            User will inherit all module permissions assigned to the selected role.
                        </div>
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
                        <i data-lucide="user-plus" style="width:16px;height:16px;"></i> Add Member
                    </button>
                    <a href="{{ route('users.index') }}" class="btn-ghost justify-content-center py-2">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
