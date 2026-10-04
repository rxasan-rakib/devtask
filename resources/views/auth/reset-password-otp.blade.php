<x-guest-layout>
    <x-slot name="header">
        <div class="form-header" style="margin-bottom: 0;">
            <h3 style="font-size: 1.35rem; margin-bottom: 0.2rem;">Verify OTP & Reset 🔐</h3>
            <p style="font-size: 0.78rem;">Enter the 6-digit code sent to your email and set a new password</p>
        </div>
    </x-slot>

    <!-- Session Status -->
    @if (session('status'))
        <div class="status-msg" style="display: flex; align-items: center; gap: .4rem; padding: .45rem .75rem; font-size: .78rem; margin-bottom: .6rem;">
            <i data-lucide="check-circle-2" style="width: 14px; height: 14px; color: #34d399; flex-shrink: 0;"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.update.otp') }}" id="otpResetForm" style="display: flex; flex-direction: column; gap: 0.65rem;">
        @csrf

        <!-- Email Address -->
        <div class="form-group" style="gap: 0.25rem;">
            <label for="email" style="font-size: 0.70rem;">Email Address</label>
            <div class="input-wrap">
                <span class="input-icon" style="left: 11px;">
                    <i data-lucide="mail" style="width: 14px; height: 14px;"></i>
                </span>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email', $email ?? request('email')) }}"
                    class="form-input {{ $errors?->has('email') ? 'is-error' : '' }}"
                    placeholder="user@company.com"
                    style="padding: .5rem .75rem .5rem 2.2rem; font-size: .82rem; height: 38px;"
                    required
                    autocomplete="username"
                >
            </div>
            @error('email')
                <div class="error-msg" style="font-size: .70rem;">{{ $message }}</div>
            @enderror
        </div>

        <!-- 6-Digit OTP Code -->
        <div class="form-group" style="gap: 0.25rem;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <label for="otp" style="font-size: 0.70rem;">6-Digit OTP Code <span style="color: var(--error);">*</span></label>
                <span style="font-size: 0.68rem; color: #a5b4fc; font-weight: 500;">Valid for 10 mins</span>
            </div>
            <div class="input-wrap">
                <span class="input-icon" style="left: 11px;">
                    <i data-lucide="key-round" style="width: 14px; height: 14px; color: var(--accent);"></i>
                </span>
                <input
                    id="otp"
                    type="text"
                    name="otp"
                    value="{{ old('otp') }}"
                    class="form-input {{ $errors?->has('otp') ? 'is-error' : '' }}"
                    placeholder="123456"
                    maxlength="6"
                    pattern="[0-9]{6}"
                    inputmode="numeric"
                    style="font-family: 'Courier New', monospace; font-size: 1.1rem; font-weight: 700; letter-spacing: 6px; text-align: center; color: #fff; padding: .45rem .75rem .45rem 2.2rem; height: 38px;"
                    required
                    autofocus
                    autocomplete="one-time-code"
                >
            </div>
            @error('otp')
                <div class="error-msg" style="font-size: .70rem;">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password & Confirm Password Row -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.55rem;">
            <!-- New Password -->
            <div class="form-group" style="gap: 0.25rem;">
                <label for="password" style="font-size: 0.70rem;">New Password</label>
                <div class="input-wrap">
                    <span class="input-icon" style="left: 10px;">
                        <i data-lucide="lock" style="width: 13px; height: 13px;"></i>
                    </span>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        class="form-input {{ $errors?->has('password') ? 'is-error' : '' }}"
                        placeholder="••••••••"
                        style="padding: .5rem 1.8rem .5rem 2rem; font-size: .80rem; height: 38px;"
                        required
                        autocomplete="new-password"
                    >
                    <button type="button" class="toggle-pw" style="right: 8px;" onclick="togglePassword('password', 'eyeIcon1')">
                        <i data-lucide="eye" id="eyeIcon1" style="width: 13px; height: 13px;"></i>
                    </button>
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="form-group" style="gap: 0.25rem;">
                <label for="password_confirmation" style="font-size: 0.70rem;">Confirm Password</label>
                <div class="input-wrap">
                    <span class="input-icon" style="left: 10px;">
                        <i data-lucide="lock" style="width: 13px; height: 13px;"></i>
                    </span>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        class="form-input {{ $errors?->has('password_confirmation') ? 'is-error' : '' }}"
                        placeholder="••••••••"
                        style="padding: .5rem 1.8rem .5rem 2rem; font-size: .80rem; height: 38px;"
                        required
                        autocomplete="new-password"
                    >
                    <button type="button" class="toggle-pw" style="right: 8px;" onclick="togglePassword('password_confirmation', 'eyeIcon2')">
                        <i data-lucide="eye" id="eyeIcon2" style="width: 13px; height: 13px;"></i>
                    </button>
                </div>
            </div>
        </div>

        @if($errors?->has('password') || $errors?->has('password_confirmation'))
            <div class="error-msg" style="font-size: .70rem;">
                {{ $errors->first('password') ?: $errors->first('password_confirmation') }}
            </div>
        @endif

        <!-- Submit Button -->
        <button type="submit" class="btn-submit" id="submitBtn" style="padding: 0.68rem; font-size: 0.85rem; margin-top: 0.2rem;">
            <i data-lucide="shield-check" style="width: 15px; height: 15px;"></i>
            <span id="btnText">Verify & Reset Password</span>
        </button>
    </form>

    <!-- Footer Actions (Resend + Back to Login) -->
    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; padding-top: 0.2rem; border-top: 1px solid rgba(148,163,184,0.1);">
        <form method="POST" action="{{ route('password.otp.resend') }}" id="resendOtpForm" style="margin: 0;">
            @csrf
            <input type="hidden" name="email" value="{{ old('email', $email ?? request('email')) }}" id="resendEmailInput">
            <button type="submit" style="background: none; border: none; color: #a5b4fc; font-size: 0.76rem; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; padding: 0; font-weight: 500; font-family: inherit;">
                <i data-lucide="refresh-cw" style="width: 12px; height: 12px;"></i>
                <span>Resend OTP</span>
            </button>
        </form>

        <a href="{{ route('login') }}" style="color: var(--muted); text-decoration: none; font-size: 0.76rem; display: inline-flex; align-items: center; gap: 4px; transition: color .2s;">
            <i data-lucide="arrow-left" style="width: 12px; height: 12px;"></i>
            <span>Back to Login</span>
        </a>
    </div>
</x-guest-layout>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) {
            lucide.createIcons();
        }

        // Sync email input with resend form
        const emailInput = document.getElementById('email');
        const resendEmailInput = document.getElementById('resendEmailInput');
        if (emailInput && resendEmailInput) {
            emailInput.addEventListener('input', function() {
                resendEmailInput.value = this.value;
            });
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

    document.getElementById('otpResetForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        const txt = document.getElementById('btnText');
        if (btn && txt) {
            btn.disabled = true;
            txt.textContent = 'Updating...';
            btn.style.opacity = '.8';
        }
    });

    document.getElementById('resendOtpForm')?.addEventListener('submit', function() {
        const emailVal = document.getElementById('email')?.value;
        if (emailVal) {
            document.getElementById('resendEmailInput').value = emailVal;
        }
    });
</script>
