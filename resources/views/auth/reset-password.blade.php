<x-guest-layout>
    <x-slot name="header">
        <div class="form-header">
            <h3>Set New Password 🔐</h3>
            <p>Please enter your new password below</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('password.store') }}" id="resetPasswordForm">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="form-group" style="margin-bottom: 1rem;">
            <label for="email">Email Address</label>
            <div class="input-wrap">
                <span class="input-icon">
                    <i data-lucide="mail" style="width: 15px; height: 15px;"></i>
                </span>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email', $request->email) }}"
                    class="form-input {{ $errors->has('email') ? 'is-error' : '' }}"
                    placeholder="user@company.com"
                    required
                    autofocus
                    autocomplete="username"
                >
            </div>
            @error('email')
                <div class="error-msg">{{ $message }}</div>
            @enderror
        </div>

        <!-- New Password -->
        <div class="form-group" style="margin-bottom: 1rem;">
            <label for="password">New Password</label>
            <div class="input-wrap">
                <span class="input-icon">
                    <i data-lucide="lock" style="width: 15px; height: 15px;"></i>
                </span>
                <input
                    id="password"
                    type="password"
                    name="password"
                    class="form-input {{ $errors->has('password') ? 'is-error' : '' }}"
                    placeholder="••••••••"
                    required
                    autocomplete="new-password"
                >
                <button type="button" class="toggle-pw" onclick="togglePassword('password', 'eyeIcon1')">
                    <i data-lucide="eye" id="eyeIcon1" style="width: 15px; height: 15px;"></i>
                </button>
            </div>
            @error('password')
                <div class="error-msg">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="password_confirmation">Confirm Password</label>
            <div class="input-wrap">
                <span class="input-icon">
                    <i data-lucide="lock" style="width: 15px; height: 15px;"></i>
                </span>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    class="form-input {{ $errors->has('password_confirmation') ? 'is-error' : '' }}"
                    placeholder="••••••••"
                    required
                    autocomplete="new-password"
                >
                <button type="button" class="toggle-pw" onclick="togglePassword('password_confirmation', 'eyeIcon2')">
                    <i data-lucide="eye" id="eyeIcon2" style="width: 15px; height: 15px;"></i>
                </button>
            </div>
            @error('password_confirmation')
                <div class="error-msg">{{ $message }}</div>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-submit" id="submitBtn" style="margin-bottom: 0.85rem;">
            <i data-lucide="check-circle" style="width: 16px; height: 16px;"></i>
            <span id="btnText">Reset Password</span>
        </button>

        <!-- Back to Login Button -->
        <a href="{{ route('login') }}" class="btn-back-login">
            <i data-lucide="arrow-left" style="width: 15px; height: 15px;"></i>
            <span>Back to Login</span>
        </a>
    </form>
</x-guest-layout>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) {
            lucide.createIcons();
        }
    });

    function togglePassword(inputId, iconId) {
        const pw = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (pw && icon) {
            if (pw.type === 'password') {
                pw.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                pw.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }
    }

    document.getElementById('resetPasswordForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        const txt = document.getElementById('btnText');
        if (btn && txt) {
            btn.disabled = true;
            txt.textContent = 'Resetting password...';
            btn.style.opacity = '.8';
        }
    });
</script>
