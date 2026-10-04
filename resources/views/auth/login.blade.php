<x-guest-layout>
    <style>
        /* Session status */
        .status-msg {
            font-size: .8rem; color: #34d399;
            background: rgba(52,211,153,0.08);
            border: 1px solid rgba(52,211,153,0.2);
            border-radius: 8px; padding: .65rem .9rem;
            margin-bottom: 1rem;
        }
    </style>

    <!-- Session Status -->
    @if (session('status'))
        <div class="status-msg">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

        <!-- Email -->
        <div class="form-group" style="margin-bottom:1rem;">
            <label for="email">Email Address</label>
            <div class="input-wrap">
                <span class="input-icon">
                    <i data-lucide="mail" style="width:15px;height:15px;"></i>
                </span>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="form-input {{ $errors->has('email') ? 'is-error' : '' }}"
                    placeholder="admin@workflowpro.com"
                    required
                    autofocus
                    autocomplete="username"
                >
            </div>
            @error('email')
                <div class="error-msg">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group" style="margin-bottom:1.25rem;">
            <label for="password">Password</label>
            <div class="input-wrap">
                <span class="input-icon">
                    <i data-lucide="lock" style="width:15px;height:15px;"></i>
                </span>
                <input
                    id="password"
                    type="password"
                    name="password"
                    class="form-input {{ $errors->has('password') ? 'is-error' : '' }}"
                    placeholder="••••••••"
                    required
                    autocomplete="current-password"
                >
                <button type="button" class="toggle-pw" id="togglePw" onclick="togglePassword()">
                    <i data-lucide="eye" id="eyeIcon" style="width:15px;height:15px;"></i>
                </button>
            </div>
            @error('password')
                <div class="error-msg">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember & Forgot -->
        <div class="row-between" style="margin-bottom:1.5rem;">
            <label class="checkbox-wrap" for="remember_me">
                <input type="checkbox" id="remember_me" name="remember">
                <span>Remember me</span>
            </label>
            @if (Route::has('password.request'))
                <a class="link-forgot" href="{{ route('password.request') }}">Forgot password?</a>
            @endif
        </div>

        <!-- Submit -->
        <button type="submit" class="btn-submit" id="submitBtn">
            <i data-lucide="log-in" style="width:16px;height:16px;"></i>
            <span id="btnText">Sign In</span>
        </button>
    </form>
</x-guest-layout>

<script>
    // Re-init icons after slot rendered
    document.addEventListener('DOMContentLoaded', () => lucide.createIcons());

    function togglePassword() {
        const pw = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        if (pw.type === 'password') {
            pw.type = 'text';
            icon.setAttribute('data-lucide', 'eye-off');
        } else {
            pw.type = 'password';
            icon.setAttribute('data-lucide', 'eye');
        }
        lucide.createIcons();
    }

    // Loading state on submit
    document.getElementById('loginForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        const txt = document.getElementById('btnText');
        btn.disabled = true;
        txt.textContent = 'Signing in...';
        btn.style.opacity = '.8';
    });
</script>
