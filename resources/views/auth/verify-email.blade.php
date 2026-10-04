<x-guest-layout>
    <x-slot name="header">
        <div class="form-header">
            <h3>Verify Email 📩</h3>
            <p>Please check your inbox for a verification link</p>
        </div>
    </x-slot>

    @if (session('status') == 'verification-link-sent')
        <div class="status-msg" style="display: flex; align-items: center; gap: .5rem; margin-bottom: 1.25rem;">
            <i data-lucide="check-circle-2" style="width: 16px; height: 16px; color: #34d399; flex-shrink: 0;"></i>
            <span>A new verification link has been sent to your email.</span>
        </div>
    @endif

    <div style="font-size: .84rem; color: var(--muted); line-height: 1.6; margin-bottom: 1.5rem;">
        Thanks for joining! Before getting started, please verify your email address by clicking on the link we sent to your inbox.
    </div>

    <form method="POST" action="{{ route('verification.send') }}" style="margin-bottom: 1rem;">
        @csrf
        <button type="submit" class="btn-submit">
            <i data-lucide="refresh-cw" style="width: 15px; height: 15px;"></i>
            <span>Resend Verification Email</span>
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-back-login" style="width: 100%; border: none; cursor: pointer;">
            <i data-lucide="log-out" style="width: 14px; height: 14px;"></i>
            <span>Log Out</span>
        </button>
    </form>
</x-guest-layout>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) {
            lucide.createIcons();
        }
    });
</script>
