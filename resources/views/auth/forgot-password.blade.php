<x-guest-layout>
    <x-slot name="header">
        <div class="form-header">
            <h3>Forgot Password? 🔑</h3>
            <p>Enter your email address to receive a 6-digit verification OTP</p>
        </div>
    </x-slot>

    <!-- Session Status -->
    @if (session('status'))
        <div class="status-msg" style="display: flex; align-items: center; gap: .5rem; margin-bottom: 1.25rem;">
            <i data-lucide="check-circle-2" style="width: 16px; height: 16px; color: #34d399; flex-shrink: 0;"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" id="forgotPasswordForm">
        @csrf

        <!-- Email Address -->
        <div class="form-group" style="margin-bottom: 1.25rem;">
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
                    class="form-input {{ $errors?->has('email') ? 'is-error' : '' }}"
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

        <!-- Submit Button -->
        <button type="submit" class="btn-submit" id="submitBtn" style="margin-bottom: 0.85rem;">
            <i data-lucide="send" style="width: 16px; height: 16px;"></i>
            <span id="btnText">Send OTP Code</span>
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

    document.getElementById('forgotPasswordForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        const txt = document.getElementById('btnText');
        if (btn && txt) {
            btn.disabled = true;
            txt.textContent = 'Sending OTP...';
            btn.style.opacity = '.8';
        }
    });
</script>
