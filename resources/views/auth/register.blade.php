<x-guest-layout>
    <x-slot name="header">
        <div class="form-header">
            <h3>Create Account ✨</h3>
            <p>Sign up to start collaborating with your team</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('register') }}" id="registerForm">
        @csrf

        <!-- Name -->
        <div class="form-group" style="margin-bottom: 1rem;">
            <label for="name">Full Name</label>
            <div class="input-wrap">
                <span class="input-icon">
                    <i data-lucide="user" style="width: 15px; height: 15px;"></i>
                </span>
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    class="form-input {{ $errors->has('name') ? 'is-error' : '' }}"
                    placeholder="e.g. John Doe"
                    required
                    autofocus
                    autocomplete="name"
                >
            </div>
            @error('name')
                <div class="error-msg">{{ $message }}</div>
            @enderror
        </div>

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
                    value="{{ old('email') }}"
                    class="form-input {{ $errors->has('email') ? 'is-error' : '' }}"
                    placeholder="user@company.com"
                    required
                    autocomplete="username"
                >
            </div>
            @error('email')
                <div class="error-msg">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group" style="margin-bottom: 1rem;">
            <label for="password">Password</label>
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
            </div>
            @error('password_confirmation')
                <div class="error-msg">{{ $message }}</div>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-submit" id="submitBtn" style="margin-bottom: 0.85rem;">
            <i data-lucide="user-plus" style="width: 16px; height: 16px;"></i>
            <span id="btnText">Create Account</span>
        </button>

        <!-- Back to Login Button -->
        <a href="{{ route('login') }}" class="btn-back-login">
            <i data-lucide="arrow-left" style="width: 15px; height: 15px;"></i>
            <span>Already registered? Sign In</span>
        </a>
    </form>
</x-guest-layout>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) {
            lucide.createIcons();
        }
    });

    document.getElementById('registerForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        const txt = document.getElementById('btnText');
        if (btn && txt) {
            btn.disabled = true;
            txt.textContent = 'Creating account...';
            btn.style.opacity = '.8';
        }
    });
</script>
