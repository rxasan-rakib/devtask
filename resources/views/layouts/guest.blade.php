<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $appName ?? \App\Models\Setting::get('app_name', config('app.name', 'WorkFlow Pro')) }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ $appFaviconUrl ?? \App\Models\Setting::getFaviconUrl() }}">
    <link rel="shortcut icon" href="{{ $appFaviconUrl ?? \App\Models\Setting::getFaviconUrl() }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }

        :root {
            --bg-deep:    #060b18;
            --bg-dark:    #0d1526;
            --accent:     #6366f1;
            --accent2:    #8b5cf6;
            --accent3:    #06b6d4;
            --glass-bg:   rgba(15, 23, 42, 0.78);
            --glass-border: rgba(99, 102, 241, 0.2);
            --text:       #f8fafc;
            --muted:      #94a3b8;
            --error:      #f87171;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
            -webkit-text-size-adjust: 100%;
            font-feature-settings: "cv02", "cv03", "cv04", "cv11";
            background: var(--bg-deep);
            color: var(--text);
            min-height: 100vh;
            font-size: 15px;
            line-height: 1.5;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            overflow-y: auto;
            position: relative;
            padding: 2rem 1rem;
        }

        body::-webkit-scrollbar {
            width: 6px;
        }
        body::-webkit-scrollbar-track {
            background: var(--bg-deep);
        }
        body::-webkit-scrollbar-thumb {
            background: rgba(99, 102, 241, 0.25);
            border-radius: 4px;
        }
        body::-webkit-scrollbar-thumb:hover {
            background: rgba(99, 102, 241, 0.45);
        }

        /* ── Animated background ── */
        .bg-canvas {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
        }

        /* Gradient mesh */
        .bg-canvas::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 20% 10%, rgba(99,102,241,0.18) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 80% 80%, rgba(139,92,246,0.14) 0%, transparent 55%),
                radial-gradient(ellipse 50% 40% at 60% 40%, rgba(6,182,212,0.08) 0%, transparent 50%);
        }

        /* Grid lines */
        .bg-canvas::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(99,102,241,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(99,102,241,0.04) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        /* Floating orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            animation: floatOrb 12s ease-in-out infinite alternate;
            pointer-events: none;
        }
        .orb-1 { width: 400px; height: 400px; background: rgba(99,102,241,0.12); top: -100px; left: -100px; animation-delay: 0s; }
        .orb-2 { width: 500px; height: 500px; background: rgba(139,92,246,0.09); bottom: -150px; right: -100px; animation-delay: -4s; }
        .orb-3 { width: 300px; height: 300px; background: rgba(6,182,212,0.07); top: 40%; left: 55%; animation-delay: -8s; }

        @keyframes floatOrb {
            from { transform: translate(0, 0) scale(1); }
            to   { transform: translate(30px, -30px) scale(1.05); }
        }

        /* ── Floating particles ── */
        .particles { position: fixed; inset: 0; z-index: 0; pointer-events: none; }
        .particle {
            position: absolute;
            width: 2px; height: 2px;
            background: rgba(99,102,241,0.6);
            border-radius: 50%;
            animation: rise linear infinite;
        }
        @keyframes rise {
            0%   { transform: translateY(100vh) scale(0); opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: .5; }
            100% { transform: translateY(-10vh) scale(1); opacity: 0; }
        }

        /* ── Left panel ── */
        .auth-container {
            position: relative;
            z-index: 10;
            display: flex;
            width: 900px;
            max-width: 96vw;
            min-height: 480px;
            margin: auto;
            border-radius: 24px;
            overflow: hidden;
            box-shadow:
                0 0 0 1px rgba(99,102,241,0.15),
                0 40px 100px rgba(0,0,0,0.5),
                0 0 80px rgba(99,102,241,0.08);
        }

        .promo-panel {
            flex: 1;
            background: linear-gradient(135deg, rgba(99,102,241,0.15) 0%, rgba(139,92,246,0.1) 50%, rgba(6,182,212,0.08) 100%);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(99,102,241,0.15);
            padding: 2.5rem 2.25rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .promo-panel::before {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(99,102,241,0.15), transparent 70%);
            top: -50px; right: -80px;
            border-radius: 50%;
        }

        .brand-logo {
            display: flex; align-items: center; gap: .75rem;
        }
        .logo-icon {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 24px rgba(99,102,241,0.4);
            animation: pulse-logo 3s ease-in-out infinite;
        }
        @keyframes pulse-logo {
            0%, 100% { box-shadow: 0 8px 24px rgba(99,102,241,0.4); }
            50% { box-shadow: 0 8px 40px rgba(99,102,241,0.7); }
        }
        .logo-text { font-size: 1.15rem; font-weight: 700; color: var(--text); line-height: 1.2; }
        .logo-sub  { font-size: .7rem; color: var(--muted); }

        .promo-headline {
            position: relative;
        }
        .promo-headline h2 {
            font-size: 1.85rem; font-weight: 800;
            color: var(--text);
            line-height: 1.2;
            margin-bottom: .6rem;
        }
        .promo-headline h2 span {
            background: linear-gradient(90deg, var(--accent), var(--accent2), var(--accent3));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .promo-headline p { font-size: .85rem; color: var(--muted); line-height: 1.6; }

        .feature-list { display: flex; flex-direction: column; gap: .5rem; }
        .feature-item {
            display: flex; align-items: center; gap: .6rem;
            font-size: .78rem; color: var(--muted);
        }
        .feature-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            flex-shrink: 0;
        }

        /* ── Right panel (form) ── */
        .form-panel {
            width: 410px;
            background: var(--glass-bg);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            padding: 2.25rem 2.25rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 1.15rem;
        }

        .form-header h3 {
            font-size: 1.5rem; font-weight: 700;
            color: var(--text); margin-bottom: .25rem;
        }
        .form-header p { font-size: .8rem; color: var(--muted); }

        .form-group { display: flex; flex-direction: column; gap: .4rem; }
        .form-group label {
            font-size: .75rem; font-weight: 600;
            color: var(--muted); text-transform: uppercase; letter-spacing: .06em;
        }
        .input-wrap { position: relative; }
        .input-icon {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            color: var(--muted); pointer-events: none;
            display: flex; align-items: center;
        }
        .form-input {
            width: 100%;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(99,102,241,0.2);
            border-radius: 10px;
            padding: .75rem 1rem .75rem 2.75rem;
            font-size: .875rem; font-family: 'Inter', sans-serif;
            color: var(--text);
            outline: none;
            transition: border-color .2s, box-shadow .2s, background .2s;
        }
        .form-input::placeholder { color: rgba(148,163,184,0.5); }
        .form-input:focus {
            border-color: var(--accent);
            background: rgba(99,102,241,0.06);
            box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
        }
        .form-input.is-error { border-color: var(--error); }
        .form-input.is-error:focus { box-shadow: 0 0 0 3px rgba(248,113,113,0.12); }

        .toggle-pw {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            color: var(--muted); display: flex; align-items: center;
            transition: color .2s;
        }
        .toggle-pw:hover { color: var(--accent); }

        .error-msg { font-size: .72rem; color: var(--error); margin-top: .2rem; }

        /* Remember row */
        .row-between {
            display: flex; align-items: center; justify-content: space-between;
        }
        .checkbox-wrap { display: flex; align-items: center; gap: .5rem; cursor: pointer; }
        .checkbox-wrap input[type="checkbox"] {
            width: 16px; height: 16px; border-radius: 4px;
            accent-color: var(--accent); cursor: pointer;
        }
        .checkbox-wrap span { font-size: .8rem; color: var(--muted); }
        .link-forgot { font-size: .8rem; color: var(--accent); text-decoration: none; transition: color .2s; }
        .link-forgot:hover { color: var(--accent2); }

        /* Submit button */
        .btn-submit {
            width: 100%; padding: .85rem;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border: none; border-radius: 10px;
            color: #fff; font-size: .9rem; font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: .5rem;
            position: relative; overflow: hidden;
            transition: transform .2s, box-shadow .2s;
            box-shadow: 0 8px 24px rgba(99,102,241,0.35);
        }
        .btn-submit::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1), transparent);
            opacity: 0; transition: opacity .2s;
        }
        .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 12px 32px rgba(99,102,241,0.5); }
        .btn-submit:hover::before { opacity: 1; }
        .btn-submit:active { transform: translateY(0); }

        /* Status message */
        .status-msg {
            font-size: .8rem; color: #34d399;
            background: rgba(52,211,153,0.1);
            border: 1px solid rgba(52,211,153,0.2);
            border-radius: 8px; padding: .6rem .85rem;
        }

        /* Back to Login Button */
        .btn-back-login {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            text-decoration: none;
            color: var(--muted);
            font-size: .85rem;
            font-weight: 500;
            padding: .75rem 1rem;
            border-radius: 10px;
            border: 1px solid rgba(148,163,184,0.18);
            background: rgba(15, 23, 42, 0.45);
            transition: all .2s ease;
        }
        .btn-back-login:hover {
            color: var(--text);
            background: rgba(99,102,241,0.1);
            border-color: rgba(99,102,241,0.35);
            transform: translateY(-1px);
        }

        /* Hide promo on small screens */
        @media (max-width: 700px) {
            .promo-panel { display: none; }
            .auth-container { width: 100%; min-height: 100vh; border-radius: 0; }
            .form-panel { width: 100%; padding: 2rem 1.5rem; }
        }
    </style>
</head>
<body>

<!-- Background -->
<div class="bg-canvas">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
</div>

<!-- Particles -->
<div class="particles" id="particles"></div>

<!-- Auth Card -->
<div class="auth-container">
    <!-- Left promo panel -->
    <div class="promo-panel">
        @php
            $guestLogo = $appLogoUrl ?? \App\Models\Setting::getLogoUrl();
        @endphp
        <div class="brand-logo">
            @if($guestLogo)
                <div class="d-flex align-items-center mb-1" style="max-height:56px;max-width:240px;">
                    <img src="{{ $guestLogo }}" alt="{{ $appName ?? 'Logo' }}" style="max-height:52px;max-width:100%;object-fit:contain;">
                </div>
            @else
                <div class="logo-icon">
                    <i data-lucide="zap" style="width:22px;height:22px;color:#fff;"></i>
                </div>
                <div>
                    <div class="logo-text">{{ $appName ?? \App\Models\Setting::get('app_name', 'WorkFlow Pro') }}</div>
                    <div class="logo-sub">{{ $companyName ?? \App\Models\Setting::get('company_name', 'Work Management System') }}</div>
                </div>
            @endif
        </div>

        <div class="promo-headline">
            <h2>Manage Work<br><span>Smarter & Faster</span></h2>
            <p>Track tasks, clients & deadlines in one powerful dashboard built for modern teams.</p>
        </div>

        <div class="feature-list">
            <div class="feature-item"><div class="feature-dot"></div> Smart Kanban Task Board</div>
            <div class="feature-item"><div class="feature-dot"></div> Client & Project Management</div>
            <div class="feature-item"><div class="feature-dot"></div> Automated Reminders</div>
            <div class="feature-item"><div class="feature-dot"></div> Role-Based Access Control</div>
            <div class="feature-item"><div class="feature-dot"></div> Real-Time Activity Logs</div>
        </div>
    </div>

    <!-- Right form panel -->
    <div class="form-panel">
        @if(isset($header))
            {{ $header }}
        @else
            <div class="form-header">
                <h3>Welcome back 👋</h3>
                <p>Sign in to your account to continue</p>
            </div>
        @endif

        {{ $slot }}
    </div>
</div>

<script>
    lucide.createIcons();

    // Generate particles
    const container = document.getElementById('particles');
    for (let i = 0; i < 25; i++) {
        const p = document.createElement('div');
        p.className = 'particle';
        p.style.cssText = `
            left: ${Math.random() * 100}%;
            width: ${Math.random() * 3 + 1}px;
            height: ${Math.random() * 3 + 1}px;
            animation-duration: ${Math.random() * 15 + 10}s;
            animation-delay: ${Math.random() * 15}s;
            opacity: ${Math.random() * 0.6 + 0.2};
        `;
        container.appendChild(p);
    }
</script>
</body>
</html>
