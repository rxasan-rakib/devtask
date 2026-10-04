@extends('layouts.app')
@section('title', 'Profile Settings')
@section('page-title', 'My Profile')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>Account & Profile Settings</h2>
        <p>Manage your personal profile details, contact information, and security password.</p>
    </div>
</div>

<div class="row g-3">
    {{-- Left: Profile Information --}}
    <div class="col-lg-6">
        <div class="card-glass mb-3">
            <div class="section-title">
                <i data-lucide="user" style="width:16px;height:16px;color:var(--accent);"></i>
                Profile Information
            </div>

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                    <label class="form-label-enhanced">Full Name <span class="req">*</span></label>
                    <input type="text" name="name" class="form-control form-control-dark @error('name') is-invalid @enderror"
                        value="{{ old('name', $user->name) }}" required autocomplete="name">
                    @error('name')
                        <div class="invalid-feedback" style="font-size:.75rem;color:var(--danger);margin-top:.25rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label-enhanced">Email Address <span class="req">*</span></label>
                    <input type="email" name="email" class="form-control form-control-dark @error('email') is-invalid @enderror"
                        value="{{ old('email', $user->email) }}" required autocomplete="email">
                    @error('email')
                        <div class="invalid-feedback" style="font-size:.75rem;color:var(--danger);margin-top:.25rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label-enhanced">Phone Number</label>
                    <input type="text" name="phone" class="form-control form-control-dark"
                        value="{{ old('phone', $user->phone) }}" placeholder="+880 1700-000000">
                </div>

                <div class="mb-3">
                    <label class="form-label-enhanced">Designation / Role</label>
                    <input type="text" name="designation" class="form-control form-control-dark"
                        value="{{ old('designation', $user->designation) }}" placeholder="e.g. Senior Software Engineer">
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="submit" class="btn-gradient">
                        <i data-lucide="save" style="width:14px;height:14px;"></i> Save Profile
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Right: Change Password & Security --}}
    <div class="col-lg-6">
        <div class="card-glass mb-3">
            <div class="section-title">
                <i data-lucide="lock" style="width:16px;height:16px;color:var(--accent);"></i>
                Update Password
            </div>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label-enhanced">Current Password <span class="req">*</span></label>
                    <input type="password" name="current_password" class="form-control form-control-dark @error('current_password', 'updatePassword') is-invalid @enderror"
                        autocomplete="current-password">
                    @error('current_password', 'updatePassword')
                        <div class="invalid-feedback" style="font-size:.75rem;color:var(--danger);margin-top:.25rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label-enhanced">New Password <span class="req">*</span></label>
                    <input type="password" name="password" class="form-control form-control-dark @error('password', 'updatePassword') is-invalid @enderror"
                        autocomplete="new-password">
                    @error('password', 'updatePassword')
                        <div class="invalid-feedback" style="font-size:.75rem;color:var(--danger);margin-top:.25rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label-enhanced">Confirm New Password <span class="req">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control form-control-dark @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                        autocomplete="new-password">
                    @error('password_confirmation', 'updatePassword')
                        <div class="invalid-feedback" style="font-size:.75rem;color:var(--danger);margin-top:.25rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="submit" class="btn-gradient">
                        <i data-lucide="shield-check" style="width:14px;height:14px;"></i> Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
