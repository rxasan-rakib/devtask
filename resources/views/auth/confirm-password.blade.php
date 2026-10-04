<x-guest-layout>
    <x-slot name="header">
        <div class="form-header">
            <h3>Confirm Password 🔒</h3>
            <p>Please confirm your password to proceed to this secure area</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('password.confirm') }}" id="confirmForm">
        @csrf

        <!-- Password -->
        <div class="form-group" style="margin-bottom: 1.25rem;">
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
                    autocomplete="current-password"
                >
            </div>
            @error('password')
                <div class="error-msg">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-submit" id="submitBtn" style="margin-bottom: 0.85rem;">
            <i data-lucide="shield-check" style="width: 16px; height: 16px;"></i>
            <span id="btnText">Confirm</span>
        </button>

        <a href="{{ url()->previous() }}" class="btn-back-login">
            <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i>
            <span>Go Back</span>
        </a>
    </form>
</x-guest-layout>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) {
            lucide.createIcons();
        }
    });
</script>
