@extends('layouts.app')
@section('title', 'Edit Member')
@section('page-title', 'Edit Team Member')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>Edit Team Member</h2>
        <p>{{ $user->name }} ({{ $user->email }})</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('users.index') }}" class="btn-ghost">
            <i data-lucide="arrow-left" style="width:14px;height:14px;"></i> All Members
        </a>
    </div>
</div>

<form method="POST" action="{{ route('users.update', $user) }}">
    @csrf @method('PUT')
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
                            value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback" style="font-size:.75rem;color:var(--danger);margin-top:.25rem;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-enhanced">Email Address <span class="req">*</span></label>
                        <input type="email" name="email" class="form-control form-control-dark @error('email') is-invalid @enderror"
                            value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback" style="font-size:.75rem;color:var(--danger);margin-top:.25rem;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-enhanced">Phone Number</label>
                        <input type="text" name="phone" class="form-control form-control-dark" value="{{ old('phone', $user->phone) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-enhanced">Designation / Job Title</label>
                        <input type="text" name="designation" class="form-control form-control-dark" value="{{ old('designation', $user->designation) }}">
                    </div>
                </div>
            </div>

            <div class="card-glass">
                <div class="section-title">
                    <i data-lucide="lock" style="width:15px;height:15px;color:var(--accent);"></i>
                    Update Password <span style="font-size:.72rem;color:var(--text-muted);font-weight:400;text-transform:none;">(leave blank to keep current)</span>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label-enhanced">New Password</label>
                        <input type="password" name="password" class="form-control form-control-dark @error('password') is-invalid @enderror">
                        @error('password')
                            <div class="invalid-feedback" style="font-size:.75rem;color:var(--danger);margin-top:.25rem;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-enhanced">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control form-control-dark">
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Role & Actions --}}
        <div class="col-lg-4">
            <div class="card-glass mb-3">
                <div class="section-title">
                    <i data-lucide="shield-check" style="width:15px;height:15px;color:var(--accent);"></i>
                    Role & Status
                </div>
                <div class="row g-3">
                    @if($user->isDefaultSuperAdmin())
                        <div class="col-12">
                            <div class="p-2 rounded" style="background: rgba(99,102,241,0.08); border: 1px solid rgba(99,102,241,0.2); font-size: 0.75rem; color: var(--text-muted);">
                                <i data-lucide="shield" style="width: 14px; height: 14px; color: var(--accent); display: inline; vertical-align: middle; margin-right: 4px;"></i>
                                Default Super Admin account role & status are protected.
                            </div>
                            <input type="hidden" name="role" value="super-admin">
                            <input type="hidden" name="is_active" value="1">
                        </div>
                    @endif
                    <div class="col-12">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label class="form-label-enhanced mb-0">System Role <span class="req">*</span></label>
                            @can('view roles')
                                <a href="{{ route('roles.index') }}" target="_blank" style="font-size:0.70rem;color:var(--accent);text-decoration:none;">
                                    View Roles
                                </a>
                            @endcan
                        </div>
                        <select name="{{ $user->isDefaultSuperAdmin() ? 'role_disabled' : 'role' }}" class="form-select form-select-dark" required {{ $user->isDefaultSuperAdmin() ? 'disabled' : '' }}>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ $user->roles->contains('id', $role->id) ? 'selected' : '' }}>
                                    {{ ucwords(str_replace('-',' ',$role->name)) }} ({{ $role->permissions_count ?? $role->permissions->count() }} permissions)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label-enhanced">Account Status</label>
                        <select name="{{ $user->isDefaultSuperAdmin() ? 'is_active_disabled' : 'is_active' }}" class="form-select form-select-dark" {{ $user->isDefaultSuperAdmin() ? 'disabled' : '' }}>
                            <option value="1" {{ $user->is_active?'selected':'' }}>Active</option>
                            <option value="0" {{ !$user->is_active?'selected':'' }}>Inactive</option>
                        </select>
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
                    <a href="{{ route('users.index') }}" class="btn-ghost justify-content-center py-2">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
