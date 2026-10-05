<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ $appName ?? \App\Models\Setting::get('app_name', 'WorkFlow Pro') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ $appFaviconUrl ?? \App\Models\Setting::getFaviconUrl() }}">
    <link rel="shortcut icon" href="{{ $appFaviconUrl ?? \App\Models\Setting::getFaviconUrl() }}">
    <script>
        (function() {
            const savedTheme = localStorage.getItem('workflow_theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            /* Shared Accent & Status Colors */
            --accent: #6366f1;
            --accent-hover: #4f46e5;
            --accent-light: rgba(99, 102, 241, 0.12);
            --sidebar-orange: #ff7828;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #0ea5e9;
            --sidebar-w: 260px;
        }

        /* ── Dark Theme (Default) ── */
        [data-theme="dark"],
        :root:not([data-theme="light"]) {
            color-scheme: dark;
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --bg-card: #1e293b;
            --bg-hover: #2d3f55;
            --top-header-bg: rgba(30, 41, 59, 0.88);
            --text-primary: #f8fafc;
            --text-muted: #94a3b8;
            --border: rgba(148, 163, 184, 0.16);
            --input-bg: #1e293b;
            --dropdown-bg: #1e293b;
            --shadow-card: 0 4px 20px -2px rgba(0, 0, 0, 0.35);
            --shadow-dropdown: 0 12px 32px rgba(0, 0, 0, 0.5);
            --select-chevron: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");

            /* Sidebar Dark Variables */
            --sidebar-bg: #111827;
            --sidebar-border: rgba(148, 163, 184, 0.12);
            --sidebar-brand-name: #f8fafc;
            --sidebar-brand-sub: #94a3b8;
            --sidebar-search-box-bg: #1f2937;
            --sidebar-search-box-border: rgba(148, 163, 184, 0.18);
            --sidebar-search-text: #f8fafc;
            --sidebar-search-placeholder: #94a3b8;
            --sidebar-nav-link-color: #cbd5e1;
            --sidebar-nav-link-icon: #94a3b8;
        }

        /* ── Light Theme ── */
        [data-theme="light"] {
            color-scheme: light;
            --bg-primary: #f4f6fb;
            --bg-secondary: #ffffff;
            --bg-card: #ffffff;
            --bg-hover: #f8fafc;
            --top-header-bg: rgba(255, 255, 255, 0.92);
            --text-primary: #0f172a;
            --text-muted: #475569;
            --border: rgba(203, 213, 225, 0.85);
            --input-bg: #ffffff;
            --dropdown-bg: #ffffff;
            --shadow-card: 0 2px 10px rgba(15, 23, 42, 0.05), 0 1px 2px rgba(15, 23, 42, 0.03);
            --shadow-dropdown: 0 12px 30px rgba(15, 23, 42, 0.12);
            --select-chevron: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23475569' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");

            /* Sidebar Light Variables */
            --sidebar-bg: #ffffff;
            --sidebar-border: #e2e8f0;
            --sidebar-brand-name: #0f172a;
            --sidebar-brand-sub: #64748b;
            --sidebar-search-box-bg: #f8fafc;
            --sidebar-search-box-border: #e2e8f0;
            --sidebar-search-text: #0f172a;
            --sidebar-search-placeholder: #94a3b8;
            --sidebar-nav-link-color: #334155;
            --sidebar-nav-link-icon: #64748b;
        }

        /* ── Smooth Theme Transitions (Targeted & Hardware Accelerated) ── */
        body,
        .sidebar,
        .top-header,
        .card-glass,
        .stat-card,
        .sidebar-search-box,
        .form-control-dark,
        .form-select-dark,
        .dropdown-menu,
        .modal-content,
        .table-dark-custom {
            transition: background-color 0.2s cubic-bezier(0.4, 0, 0.2, 1),
                        border-color 0.2s cubic-bezier(0.4, 0, 0.2, 1),
                        color 0.15s ease,
                        box-shadow 0.2s ease;
        }

        /* Theme Toggle Icons */
        .theme-toggle-btn #themeIconSun {
            display: none;
            color: #f59e0b;
        }
        .theme-toggle-btn #themeIconMoon {
            display: inline-block;
            color: #6366f1;
        }
        [data-theme="dark"] .theme-toggle-btn #themeIconSun,
        :root:not([data-theme="light"]) .theme-toggle-btn #themeIconSun {
            display: inline-block;
        }
        [data-theme="dark"] .theme-toggle-btn #themeIconMoon,
        :root:not([data-theme="light"]) .theme-toggle-btn #themeIconMoon {
            display: none;
        }
        [data-theme="light"] .theme-toggle-btn #themeIconSun {
            display: none;
        }
        [data-theme="light"] .theme-toggle-btn #themeIconMoon {
            display: inline-block;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }

        html,
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
            -webkit-text-size-adjust: 100%;
            font-feature-settings: 'cv02', 'cv03', 'cv04', 'cv11';
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            margin: 0;
            font-size: 15px;
            line-height: 1.6;
            letter-spacing: -0.01em;
        }

        /* ── Theme-Aware Sidebar ── */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: var(--sidebar-w);
            height: 100vh;
            height: 100dvh;
            max-height: 100dvh;
            background: var(--sidebar-bg) !important;
            border-right: 1px solid var(--sidebar-border);
            display: flex;
            flex-direction: column;
            z-index: 1045;
            transition: transform .3s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.2s cubic-bezier(0.4, 0, 0.2, 1), border-color 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            box-shadow: 2px 0 16px rgba(0, 0, 0, 0.08);
        }

        .sidebar-brand {
            padding: 1.15rem 1.15rem 0.95rem;
            border-bottom: 1px solid var(--sidebar-border);
            display: flex;
            align-items: center;
            gap: .8rem;
            flex-shrink: 0;
            min-height: 72px;
            text-decoration: none;
            background: transparent;
        }

        .sidebar-brand.has-full-logo {
            justify-content: center;
            padding: 0.85rem 1.15rem;
        }

        .sidebar-full-logo-box {
            width: 100%;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-full-logo {
            max-height: 44px;
            max-width: 100%;
            width: auto;
            object-fit: contain;
            transition: transform .2s ease;
        }

        .sidebar-brand:hover .sidebar-full-logo {
            transform: scale(1.02);
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            background: #ff7828;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(255, 120, 40, 0.25);
        }

        .brand-name {
            font-size: 0.98rem;
            font-weight: 750;
            color: var(--sidebar-brand-name) !important;
            line-height: 1.2;
            letter-spacing: -0.015em;
        }

        .brand-sub {
            font-size: .70rem;
            color: var(--sidebar-brand-sub) !important;
            margin-top: 2px;
        }

        /* ── Search Module Box (Top of Sidebar) ── */
        .sidebar-search-wrap {
            padding: 0.75rem 0.85rem 0.3rem;
            flex-shrink: 0;
            background: transparent;
        }

        .sidebar-search-box {
            position: relative;
            display: flex;
            align-items: center;
            background: var(--sidebar-search-box-bg);
            border: 1px solid var(--sidebar-search-box-border);
            border-radius: 8px;
            padding: 0.4rem 0.7rem;
            transition: all 0.2s ease;
        }

        .sidebar-search-box:focus-within {
            border-color: #ff7828;
            box-shadow: 0 0 0 2px rgba(255, 120, 40, 0.2);
        }

        .sidebar-search-icon {
            width: 14px;
            height: 14px;
            color: var(--sidebar-search-placeholder);
            margin-right: 7px;
            flex-shrink: 0;
        }

        .sidebar-search-input {
            background: transparent;
            border: none;
            outline: none;
            color: var(--sidebar-search-text);
            font-size: 0.82rem;
            width: 100%;
            padding: 0;
            font-family: inherit;
        }

        .sidebar-search-input::placeholder {
            color: var(--sidebar-search-placeholder);
            opacity: 0.9;
        }

        /* ── Sidebar Navigation ── */
        .sidebar-nav {
            padding: 0.25rem 0 calc(3.5rem + env(safe-area-inset-bottom, 0px));
            flex: 1 1 auto;
            overflow-y: auto;
            overflow-x: hidden;
            min-height: 0;
            background: transparent;
            scrollbar-width: thin;
            scrollbar-color: rgba(148, 163, 184, 0.25) transparent;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior-y: contain;
            touch-action: pan-y;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.25);
            border-radius: 4px;
        }

        /* ── Section Title (Vivid Orange in Both Light & Dark) ── */
        .nav-section-title {
            font-size: .70rem;
            font-weight: 750;
            color: #ff7828 !important;
            text-transform: uppercase;
            letter-spacing: .08em;
            padding: 0.85rem 1rem 0.3rem;
            margin-top: 0.2rem;
        }

        /* ── Nav Item Links ── */
        .nav-item {
            margin-bottom: 2px;
        }

        .nav-item a {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .58rem .82rem;
            margin: 0 .75rem;
            border-radius: 7px;
            color: var(--sidebar-nav-link-color) !important;
            text-decoration: none !important;
            font-size: 0.88rem;
            font-weight: 500;
            border: 1.5px solid transparent;
            transition: all .18s ease;
            position: relative;
            background: transparent;
        }

        .nav-item a i.nav-icon {
            width: 17px;
            height: 17px;
            color: var(--sidebar-nav-link-icon) !important;
            flex-shrink: 0;
            transition: color .18s ease;
        }

        .nav-item a .nav-text {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ── Hover State (Over) ── */
        .nav-item a:hover {
            color: #ff7828 !important;
            background: rgba(255, 120, 40, 0.12) !important;
            border-color: rgba(255, 120, 40, 0.45) !important;
        }

        .nav-item a:hover i.nav-icon {
            color: #ff7828 !important;
        }

        /* ── Active State ── */
        .nav-item a.active {
            color: #ff7828 !important;
            border: 1.5px solid #ff7828 !important;
            background: rgba(255, 120, 40, 0.12) !important;
            font-weight: 600;
        }

        .nav-item a.active i.nav-icon {
            color: #ff7828 !important;
        }

        .nav-item a.active::before {
            display: none !important;
        }

        /* ── Sidebar Footer ── */
        .sidebar-footer {
            padding: 0.85rem 1rem;
            border-top: 1px solid var(--sidebar-footer-border);
            flex-shrink: 0;
            background: var(--sidebar-footer-bg);
            transition: background-color .3s ease, border-color .3s ease;
        }

        .sidebar-user-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.45rem 0.6rem;
            border-radius: 8px;
            background: var(--sidebar-user-card-bg);
            border: 1px solid var(--sidebar-user-card-border);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .sidebar-user-card:hover {
            border-color: #ff7828;
            background: rgba(255, 120, 40, 0.12);
        }

        .sidebar-status-dot {
            width: 8px;
            height: 8px;
            background: var(--success);
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 6px rgba(16, 185, 129, 0.6);
        }

        /* Main */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .top-header {
            background: var(--top-header-bg);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--border);
            padding: .65rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
            min-height: 64px;
            gap: 1rem;
        }

        .page-title {
            font-size: 1.05rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .btn-icon {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            color: var(--text-muted);
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all .2s;
            position: relative;
        }

        .btn-icon:hover {
            border-color: var(--accent);
            color: var(--accent) !important;
            background: var(--bg-hover);
        }

        .notification-badge {
            position: absolute;
            top: -3px;
            right: -3px;
            background: var(--danger);
            color: #fff;
            font-size: .6rem;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            box-shadow: 0 0 8px rgba(239, 68, 68, .5);
        }

        .user-nav-btn {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 30px;
            padding: 3px 10px 3px 3px;
            display: flex;
            align-items: center;
            gap: .55rem;
            cursor: pointer;
            transition: all .2s;
        }

        .user-nav-btn:hover {
            border-color: var(--accent);
            background: var(--bg-hover);
        }

        .page-content {
            padding: 1.75rem;
            flex: 1;
        }

        /* Cards */
        .card-glass {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.25rem;
        }

        .card-glass:hover {
            border-color: rgba(99, 102, 241, 0.3);
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.25rem;
            transition: all .2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            border-color: rgba(99, 102, 241, 0.4);
        }

        .stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            line-height: 1;
        }

        .stat-label {
            font-size: .8rem;
            color: var(--text-muted);
            margin-top: .25rem;
        }

        /* Badges */
        .badge-high {
            background: rgba(239, 68, 68, .15);
            color: #ef4444;
        }

        .badge-medium {
            background: rgba(245, 158, 11, .15);
            color: #f59e0b;
        }

        .badge-low {
            background: rgba(16, 185, 129, .15);
            color: #10b981;
        }

        .status-pending {
            background: rgba(148, 163, 184, .15);
            color: #94a3b8;
        }

        .status-in_progress {
            background: rgba(99, 102, 241, .15);
            color: #6366f1;
        }

        .status-on_hold {
            background: rgba(245, 158, 11, .15);
            color: #f59e0b;
        }

        .status-under_review {
            background: rgba(14, 165, 233, .15);
            color: #0ea5e9;
        }

        .status-completed {
            background: rgba(16, 185, 129, .15);
            color: #10b981;
        }

        /* Table */
        .table-dark-custom {
            color: var(--text-primary);
            --bs-table-bg: transparent;
            --bs-table-border-color: var(--border);
            --bs-table-hover-bg: var(--bg-hover);
        }

        .table-dark-custom thead th {
            color: var(--text-muted);
            font-size: .75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .05em;
            border-bottom: 1px solid var(--border);
            padding: .75rem 1rem;
            background: transparent;
        }

        .table-dark-custom tbody td {
            padding: .875rem 1rem;
            border-color: var(--border);
            vertical-align: middle;
            font-size: .875rem;
        }

        /* Global Links */
        a {
            text-decoration: none;
            color: inherit;
        }

        a:hover,
        a:focus,
        a:active {
            text-decoration: none !important;
        }

        /* Buttons */
        .btn-primary-custom {
            background: var(--accent);
            border: none;
            color: #fff !important;
            border-radius: 8px;
            padding: .5rem 1.1rem;
            font-size: .875rem;
            font-weight: 500;
            transition: all .2s;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            text-decoration: none !important;
        }

        .btn-primary-custom:hover,
        .btn-primary-custom:focus {
            background: var(--accent-hover);
            color: #fff !important;
            transform: translateY(-1px);
            text-decoration: none !important;
        }

        /* Forms */
        .form-label {
            font-size: .8rem;
            font-weight: 500;
            color: var(--text-muted);
            margin-bottom: .4rem;
        }

        /* Alerts */
        .alert-success-custom {
            background: rgba(16, 185, 129, .12);
            border: 1px solid rgba(16, 185, 129, .3);
            color: var(--success);
            border-radius: 8px;
        }

        .alert-danger-custom {
            background: rgba(239, 68, 68, .12);
            border: 1px solid rgba(239, 68, 68, .3);
            color: var(--danger);
            border-radius: 8px;
        }

        /* ── Floating Toast Alert Notification System ── */
        .app-toast-container {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 100050;
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-width: 440px;
            width: calc(100vw - 48px);
            pointer-events: none;
        }

        .app-toast-alert {
            pointer-events: auto;
            position: relative;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px 16px 16px;
            border-radius: 14px;
            background: var(--bg-card, #1e2235);
            border: 1px solid var(--border, rgba(255, 255, 255, 0.1));
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.45), 0 0 1px rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            color: var(--text-primary, #ffffff);
            overflow: hidden;
            animation: toastSlideInRight 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease, margin 0.3s ease;
        }

        .app-toast-alert.toast-hiding {
            animation: toastSlideOutRight 0.3s cubic-bezier(0.7, 0, 0.84, 0) forwards;
        }

        @keyframes toastSlideInRight {
            from {
                opacity: 0;
                transform: translateX(80px) scale(0.92);
            }
            to {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
        }

        @keyframes toastSlideOutRight {
            from {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
            to {
                opacity: 0;
                transform: translateX(80px) scale(0.92);
            }
        }

        @keyframes toastSlideInTop {
            from {
                opacity: 0;
                transform: translateY(-25px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes toastSlideOutTop {
            from {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
            to {
                opacity: 0;
                transform: translateY(-25px) scale(0.95);
            }
        }

        /* ── Mobile Centered Notification & Header Dropdown Styling ── */
        @keyframes headerDropdownFadeInMobile {
            from {
                opacity: 0;
                transform: translate(-50%, -6px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translate(-50%, 0) scale(1);
            }
        }

        @media (max-width: 768px) {
            .app-toast-container {
                top: 14px;
                left: 50%;
                right: auto;
                transform: translateX(-50%);
                width: calc(100vw - 28px);
                max-width: 420px;
                align-items: stretch;
            }

            .app-toast-alert {
                width: 100%;
                animation-name: toastSlideInTop;
            }

            .app-toast-alert.toast-hiding {
                animation-name: toastSlideOutTop;
            }

            #reminderToastContainer {
                bottom: 14px !important;
                left: 50% !important;
                right: auto !important;
                transform: translateX(-50%) !important;
                width: calc(100vw - 28px) !important;
                max-width: 420px !important;
            }

            /* Center Top Header Dropdowns (Reminders, User Menu, Quick Create) on Mobile */
            .top-header {
                position: sticky;
                top: 0;
            }

            .top-header .dropdown {
                position: static !important;
            }

            .top-header .dropdown-menu {
                position: absolute !important;
                top: calc(100% + 8px) !important;
                left: 50% !important;
                right: auto !important;
                bottom: auto !important;
                transform: translateX(-50%) !important;
                width: calc(100vw - 24px) !important;
                max-width: 360px !important;
                max-height: 80vh !important;
                overflow-y: auto !important;
                margin: 0 !important;
                border-radius: 14px !important;
                box-shadow: 0 16px 40px rgba(0, 0, 0, 0.4), 0 0 1px rgba(255, 255, 255, 0.15) !important;
                z-index: 1050 !important;
            }

            .top-header .dropdown-menu.show {
                display: block !important;
                animation: headerDropdownFadeInMobile 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }
        }

        .toast-indicator {
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
        }

        .app-toast-success {
            border-color: rgba(16, 185, 129, 0.3);
        }
        .app-toast-success .toast-indicator {
            background: linear-gradient(180deg, #10b981, #059669);
        }
        .app-toast-success .toast-icon-wrap {
            color: #10b981;
            background: rgba(16, 185, 129, 0.12);
        }

        .app-toast-danger {
            border-color: rgba(239, 68, 68, 0.3);
        }
        .app-toast-danger .toast-indicator {
            background: linear-gradient(180deg, #ef4444, #dc2626);
        }
        .app-toast-danger .toast-icon-wrap {
            color: #ef4444;
            background: rgba(239, 68, 68, 0.12);
        }

        .app-toast-warning {
            border-color: rgba(245, 158, 11, 0.3);
        }
        .app-toast-warning .toast-indicator {
            background: linear-gradient(180deg, #f59e0b, #d97706);
        }
        .app-toast-warning .toast-icon-wrap {
            color: #f59e0b;
            background: rgba(245, 158, 11, 0.12);
        }

        .app-toast-info {
            border-color: rgba(99, 102, 241, 0.3);
        }
        .app-toast-info .toast-indicator {
            background: linear-gradient(180deg, #6366f1, #4f46e5);
        }
        .app-toast-info .toast-icon-wrap {
            color: #6366f1;
            background: rgba(99, 102, 241, 0.12);
        }

        .toast-icon-wrap {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .toast-content {
            flex: 1;
            min-width: 0;
        }

        .toast-title {
            font-size: 0.86rem;
            font-weight: 700;
            color: var(--text-primary, #ffffff);
            line-height: 1.3;
            margin-bottom: 2px;
        }

        .toast-message {
            font-size: 0.81rem;
            color: var(--text-muted, #94a3b8);
            line-height: 1.45;
            word-break: break-word;
        }

        .toast-error-list {
            margin: 4px 0 0 0;
            padding-left: 18px;
            font-size: 0.79rem;
            color: var(--text-muted, #94a3b8);
        }
        .toast-error-list li {
            margin-bottom: 2px;
        }

        .toast-close {
            background: none;
            border: none;
            padding: 4px;
            color: var(--text-muted, #94a3b8);
            cursor: pointer;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: color 0.15s ease, background 0.15s ease;
            margin-top: -3px;
            margin-right: -4px;
        }
        .toast-close:hover {
            color: var(--text-primary, #ffffff);
            background: var(--bg-hover, rgba(255, 255, 255, 0.08));
        }

        .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: rgba(255, 255, 255, 0.06);
            overflow: hidden;
        }

        .toast-progress-bar {
            height: 100%;
            width: 100%;
            background: currentColor;
            opacity: 0.7;
            transform-origin: left;
            transition: width linear;
        }

        /* Overdue */
        .overdue-row {
            background: rgba(239, 68, 68, .05) !important;
        }

        .overdue-badge {
            background: rgba(239, 68, 68, .15);
            color: #ef4444;
            font-size: .65rem;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 600;
        }

        /* ── Body Scrolling Lock for Mobile Sidebar ── */
        html.sidebar-open,
        body.sidebar-open {
            overflow: hidden !important;
            touch-action: none;
            overscroll-behavior: none !important;
        }

        /* Mobile */
        .sidebar-toggle {
            display: none;
        }

        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
                z-index: 1045;
                box-shadow: 0 0 35px rgba(0, 0, 0, 0.5);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-wrapper {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: flex;
            }

            .sidebar-overlay {
                display: block;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, .6);
                backdrop-filter: blur(4px);
                -webkit-backdrop-filter: blur(4px);
                z-index: 1040;
                opacity: 0;
                visibility: hidden;
                pointer-events: none;
                transition: opacity 0.25s ease, visibility 0.25s ease;
            }

            .sidebar-overlay.open {
                opacity: 1;
                visibility: visible;
                pointer-events: auto;
            }

            .sidebar-nav {
                padding-bottom: calc(5.5rem + env(safe-area-inset-bottom, 0px));
            }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 3px;
        }

        /* Animations */
        .fade-in {
            animation: fadeIn .3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ── Page Header ── */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid var(--border);
            position: relative;
        }

        .page-header::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 60px;
            height: 2px;
            background: linear-gradient(90deg, var(--accent), var(--accent2, #8b5cf6));
            border-radius: 2px;
        }

        .page-header-left h2 {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0 0 .2rem;
        }

        .page-header-left p {
            font-size: .8rem;
            color: var(--text-muted);
            margin: 0;
        }

        .page-header-actions {
            display: flex;
            gap: .6rem;
            align-items: center;
        }

        /* ── Section Title ── */
        .section-title {
            font-size: .7rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .1em;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        /* ── Enhanced Form Controls ── */
        .form-group-enhanced {
            margin-bottom: 1.1rem;
        }

        .form-label-enhanced {
            font-size: .72rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .07em;
            margin-bottom: .45rem;
            display: flex;
            align-items: center;
            gap: .35rem;
        }

        .form-label-enhanced .req {
            color: var(--danger);
        }

        .form-control-dark,
        .form-select-dark,
        .form-control,
        .form-select,
        select.form-select {
            background-color: var(--input-bg) !important;
            border: 1px solid var(--border) !important;
            color: var(--text-primary) !important;
            border-radius: 9px;
            font-size: .875rem;
            transition: border-color .2s, box-shadow .2s, background .2s, color .2s;
        }

        .form-control-dark:focus,
        .form-select-dark:focus,
        .form-select:focus,
        select.form-select:focus {
            border-color: var(--accent) !important;
            box-shadow: 0 0 0 3px var(--accent-light) !important;
            background-color: var(--input-bg) !important;
            color: var(--text-primary) !important;
        }

        .form-control-dark::placeholder,
        .form-control::placeholder {
            color: var(--text-muted) !important;
            opacity: .7;
        }

        .form-control-dark.is-invalid,
        .form-select-dark.is-invalid {
            border-color: var(--danger) !important;
        }

        /* ── Select Dropdowns & Options ── */
        select,
        select.form-select,
        select.form-select-dark {
            background-color: var(--input-bg) !important;
            color: var(--text-primary) !important;
            background-image: var(--select-chevron) !important;
            background-repeat: no-repeat !important;
            background-position: right .75rem center !important;
            background-size: 14px 10px !important;
            max-width: 100% !important;
            width: 100% !important;
            text-overflow: ellipsis !important;
            overflow: hidden !important;
            white-space: nowrap !important;
            padding-right: 2.2rem !important;
        }

        select option,
        select optgroup,
        .form-select option,
        .form-select-dark option {
            background-color: var(--dropdown-bg) !important;
            color: var(--text-primary) !important;
            padding: 8px 12px !important;
            max-width: 100% !important;
            word-wrap: break-word !important;
            white-space: normal !important;
        }

        select optgroup {
            font-weight: 700 !important;
            color: var(--accent) !important;
            padding-top: 6px !important;
            padding-bottom: 4px !important;
        }

        select option:hover,
        select option:focus,
        select option:checked {
            background-color: var(--accent) !important;
            color: #ffffff !important;
        }

        input[type="date"],
        input[type="datetime-local"],
        input[type="time"] {
            color-scheme: inherit !important;
        }

        /* ── Theme Toggle Button ── */
        .theme-toggle-btn {
            position: relative;
            cursor: pointer;
            overflow: hidden;
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }

        .theme-toggle-btn:hover {
            transform: rotate(20deg) scale(1.08);
            border-color: var(--accent) !important;
            color: var(--accent) !important;
        }

        [data-theme="light"] #themeIconSun {
            display: none !important;
        }

        [data-theme="light"] #themeIconMoon {
            display: inline-block !important;
        }

        [data-theme="dark"] #themeIconSun,
        :root:not([data-theme="light"]) #themeIconSun {
            display: inline-block !important;
        }

        [data-theme="dark"] #themeIconMoon,
        :root:not([data-theme="light"]) #themeIconMoon {
            display: none !important;
        }

        /* Textarea */
        textarea.form-control-dark {
            resize: vertical;
            min-height: 90px;
        }

        /* ── Card variants ── */
        .card-glass {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1.35rem;
            transition: border-color .2s;
        }

        .card-glass-hover:hover {
            border-color: rgba(99, 102, 241, .35);
            box-shadow: 0 4px 24px rgba(99, 102, 241, .08);
            transform: translateY(-1px);
        }

        .card-accent-top {
            border-top: 2px solid var(--accent);
        }

        .card-accent-top-success {
            border-top: 2px solid var(--success);
        }

        .card-accent-top-warning {
            border-top: 2px solid var(--warning);
        }

        .card-accent-top-danger {
            border-top: 2px solid var(--danger);
        }

        /* ── Info Row (label + value) ── */
        .info-row {
            display: flex;
            flex-direction: column;
            padding: .65rem 0;
            border-bottom: 1px solid var(--border);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-size: .65rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: .2rem;
        }

        .info-value {
            font-size: .875rem;
            color: var(--text-primary);
        }

        /* ── Timeline ── */
        .timeline {
            position: relative;
            padding-left: 1.5rem;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 7px;
            top: 0;
            bottom: 0;
            width: 1px;
            background: var(--border);
        }

        .timeline-item {
            position: relative;
            margin-bottom: .9rem;
        }

        .timeline-dot {
            position: absolute;
            left: -1.5rem;
            top: .3rem;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--accent);
            border: 2px solid var(--bg-secondary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .2);
        }

        .timeline-body {
            font-size: .8rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .timeline-body strong {
            color: var(--text-primary);
            font-weight: 500;
        }

        .timeline-time {
            font-size: .68rem;
            color: var(--text-muted);
            margin-top: .15rem;
        }

        /* ── Empty State ── */
        .empty-state {
            text-align: center;
            padding: 3.5rem 2rem;
            color: var(--text-muted);
        }

        .empty-state-icon {
            width: 64px;
            height: 64px;
            background: var(--bg-hover);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            opacity: .6;
        }

        .empty-state h5 {
            font-size: .95rem;
            color: var(--text-primary);
            margin-bottom: .5rem;
        }

        .empty-state p {
            font-size: .82rem;
            margin-bottom: 1.25rem;
        }

        /* ── Action Toolbar ── */
        .action-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: .75rem 1.1rem;
            margin-bottom: 1rem;
            gap: .75rem;
            flex-wrap: wrap;
        }

        .toolbar-left,
        .toolbar-right {
            display: flex;
            gap: .6rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .toolbar-count {
            font-size: .8rem;
            color: var(--text-muted);
            background: var(--bg-hover);
            border-radius: 20px;
            padding: .2rem .65rem;
        }

        /* ── Gradient Buttons ── */
        .btn-gradient {
            background: linear-gradient(135deg, var(--accent), #8b5cf6);
            border: none;
            color: #fff !important;
            border-radius: 9px;
            padding: .5rem 1.1rem;
            font-size: .875rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            transition: all .2s;
            text-decoration: none !important;
            box-shadow: 0 4px 15px rgba(99, 102, 241, .3);
        }

        .btn-gradient:hover,
        .btn-gradient:focus {
            opacity: .9;
            transform: translateY(-1px);
            color: #fff !important;
            box-shadow: 0 6px 20px rgba(99, 102, 241, .45);
            text-decoration: none !important;
        }

        .btn-ghost {
            background: var(--bg-hover);
            border: 1px solid var(--border);
            color: var(--text-muted) !important;
            border-radius: 9px;
            padding: .5rem 1.1rem;
            font-size: .875rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            transition: all .2s;
            text-decoration: none !important;
        }

        .btn-ghost:hover,
        .btn-ghost:focus {
            color: var(--text-primary) !important;
            border-color: rgba(99, 102, 241, .4);
            text-decoration: none !important;
        }

        .btn-danger-ghost {
            background: rgba(239, 68, 68, .05);
            border: 1px solid rgba(239, 68, 68, .2);
            color: var(--danger) !important;
            border-radius: 9px;
            padding: .5rem 1.1rem;
            font-size: .875rem;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            transition: all .2s;
            text-decoration: none !important;
        }

        .btn-danger-ghost:hover,
        .btn-danger-ghost:focus {
            background: rgba(239, 68, 68, .12);
            color: var(--danger) !important;
            text-decoration: none !important;
        }

        /* ── Status Pill (larger) ── */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .35rem .8rem;
            border-radius: 20px;
            font-size: .78rem;
            font-weight: 600;
        }

        .status-pill::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        /* ── Avatar group ── */
        .avatar-sm {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 2px solid var(--bg-secondary);
            object-fit: cover;
        }

        .avatar-initial {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), #8b5cf6);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .65rem;
            font-weight: 700;
            color: #fff;
        }

        /* ── Progress bar ── */
        .progress-slim {
            height: 4px;
            background: var(--bg-hover);
            border-radius: 2px;
            overflow: hidden;
        }

        .progress-slim-fill {
            height: 100%;
            border-radius: 2px;
            transition: width .6s ease;
        }

        /* ── Table enhancements ── */
        .table-dark-custom thead tr {
            background: linear-gradient(90deg, rgba(99, 102, 241, .06), transparent);
        }

        .table-dark-custom tbody tr {
            transition: background .15s;
        }

        .table-dark-custom tbody tr:hover {
            background: rgba(99, 102, 241, .04) !important;
        }

        /* ── Separator ── */
        .sep {
            border: none;
            border-top: 1px solid var(--border);
            margin: 1.1rem 0;
        }

        /* Bootstrap overrides */
        .dropdown-menu {
            --bs-dropdown-bg: var(--dropdown-bg) !important;
            --bs-dropdown-color: var(--text-primary) !important;
            --bs-dropdown-link-color: var(--text-muted) !important;
            --bs-dropdown-link-hover-color: var(--text-primary) !important;
            --bs-dropdown-link-hover-bg: var(--bg-hover) !important;
            --bs-dropdown-border-color: var(--border) !important;
            --bs-dropdown-divider-bg: var(--border) !important;
            background-color: var(--dropdown-bg) !important;
            border: 1px solid var(--border) !important;
            border-radius: 12px !important;
            padding: .5rem !important;
            box-shadow: var(--shadow-dropdown) !important;
            color: var(--text-primary) !important;
        }

        .dropdown-item {
            color: var(--text-muted) !important;
            border-radius: 6px !important;
            font-size: .875rem !important;
            transition: all 0.15s ease !important;
        }

        .dropdown-item:hover,
        .dropdown-item:focus {
            background-color: var(--bg-hover) !important;
            color: var(--text-primary) !important;
        }

        .dropdown-item.active,
        .dropdown-item:active {
            background-color: var(--accent) !important;
            color: #ffffff !important;
        }

        .dropdown-header {
            color: var(--text-muted) !important;
        }

        .dropdown-divider {
            border-color: var(--border) !important;
            opacity: 0.8 !important;
        }

        .modal-content {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 14px;
            color: var(--text-primary);
        }

        .modal-header,
        .modal-footer {
            border-color: var(--border);
        }

        .badge {
            font-weight: 500;
            font-size: .72rem;
        }

        /* ── Modern Pagination System ── */
        .pagination-container,
        nav[role="navigation"] {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            margin-top: 1.25rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        nav[role="navigation"] p.text-muted,
        nav[role="navigation"] .small.text-muted {
            font-size: 0.84rem !important;
            color: var(--text-muted) !important;
            margin: 0;
        }

        nav[role="navigation"] p.text-muted span,
        nav[role="navigation"] .small.text-muted span,
        nav[role="navigation"] .fw-semibold,
        nav[role="navigation"] .font-medium {
            color: var(--text-primary) !important;
            font-weight: 700 !important;
        }

        .pagination {
            display: inline-flex !important;
            align-items: center !important;
            gap: 5px !important;
            margin: 0 !important;
            padding: 0 !important;
            list-style: none !important;
            flex-wrap: wrap !important;
        }

        .pagination .page-item {
            margin: 0 !important;
            padding: 0 !important;
        }

        .pagination .page-link {
            min-width: 36px;
            height: 36px;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0 12px !important;
            font-size: 0.83rem !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            border: 1px solid var(--border) !important;
            background: var(--bg-card) !important;
            color: var(--text-primary) !important;
            text-decoration: none !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
            line-height: 1 !important;
        }

        .pagination .page-link:hover {
            background: var(--bg-hover) !important;
            color: var(--accent) !important;
            border-color: rgba(99, 102, 241, 0.45) !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15) !important;
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #6366f1, #4f46e5) !important;
            border-color: transparent !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.4) !important;
            transform: none !important;
        }

        .pagination .page-item.disabled .page-link {
            background: var(--bg-card) !important;
            color: var(--text-muted) !important;
            opacity: 0.4 !important;
            border-color: var(--border) !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
            transform: none !important;
            box-shadow: none !important;
        }

        /* SVG sizing protection across any pagination output */
        .pagination svg,
        nav[role="navigation"] svg {
            width: 15px !important;
            height: 15px !important;
            max-width: 15px !important;
            max-height: 15px !important;
            display: inline-block !important;
            vertical-align: middle !important;
        }
    </style>

    @stack('styles')
</head>

<body>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        @php
            $sidebarLogo = $appLogoUrl ?? \App\Models\Setting::getLogoUrl();
            $pendingReminders = auth()->check()
                ? \App\Models\Reminder::where('user_id', auth()->id())->active()->count()
                : 0;
            $unreadNotificationsCount = auth()->check()
                ? \App\Models\AppNotification::where('user_id', auth()->id())->unread()->count()
                : 0;
            $headerNotifications = auth()->check()
                ? \App\Models\AppNotification::where('user_id', auth()->id())->latest()->take(6)->get()
                : collect();
        @endphp
        <a href="{{ route('dashboard') }}" class="sidebar-brand {{ $sidebarLogo ? 'has-full-logo' : '' }}">
            @if ($sidebarLogo)
                <div class="sidebar-full-logo-box">
                    <img src="{{ $sidebarLogo }}" alt="{{ $appName ?? 'Logo' }}" class="sidebar-full-logo">
                </div>
            @else
                <div class="brand-icon">
                    <i data-lucide="zap" style="color:#fff;width:18px;height:18px;"></i>
                </div>
                <div style="overflow:hidden;min-width:0;">
                    <div class="brand-name" style="text-overflow:ellipsis;overflow:hidden;white-space:nowrap;">
                        {{ $appName ?? \App\Models\Setting::get('app_name', 'WorkFlow Pro') }}</div>
                    <div class="brand-sub" style="text-overflow:ellipsis;overflow:hidden;white-space:nowrap;">
                        {{ $companyName ?? \App\Models\Setting::get('company_name', 'Work Management System') }}</div>
                </div>
            @endif
        </a>

        <!-- Search Module Input (Top of Sidebar) -->
        <div class="sidebar-search-wrap">
            <div class="sidebar-search-box">
                <i data-lucide="search" class="sidebar-search-icon"></i>
                <input type="text" id="sidebarModuleSearch" class="sidebar-search-input" placeholder="Search Module..." autocomplete="off">
            </div>
        </div>

        <nav class="sidebar-nav" id="sidebarNav">
            @can('view dashboard')
            <div class="nav-section-title">Main</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i data-lucide="home" class="nav-icon"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>
            </ul>
            @endcan

            @canany(['view tasks', 'create tasks'])
            <div class="nav-section-title">Tasks & Projects</div>
            <ul class="nav flex-column">
                @can('view tasks')
                <li class="nav-item">
                    <a href="{{ route('tasks.index') }}"
                        class="{{ request()->routeIs('tasks.index', 'tasks.show', 'tasks.edit') ? 'active' : '' }}">
                        <i data-lucide="list-checks" class="nav-icon"></i>
                        <span class="nav-text">All Tasks</span>
                    </a>
                </li>
                @endcan
                @can('create tasks')
                <li class="nav-item">
                    <a href="{{ route('tasks.create') }}"
                        class="{{ request()->routeIs('tasks.create') ? 'active' : '' }}">
                        <i data-lucide="plus-circle" class="nav-icon"></i>
                        <span class="nav-text">New Task</span>
                    </a>
                </li>
                @endcan
                @can('view tasks')
                <li class="nav-item">
                    <a href="{{ route('tasks.kanban') }}"
                        class="{{ request()->routeIs('tasks.kanban') ? 'active' : '' }}">
                        <i data-lucide="kanban" class="nav-icon"></i>
                        <span class="nav-text">Kanban Board</span>
                    </a>
                </li>
                @endcan
            </ul>
            @endcanany

            @canany(['view clients', 'create clients'])
            <div class="nav-section-title">Clients</div>
            <ul class="nav flex-column">
                @can('view clients')
                <li class="nav-item">
                    <a href="{{ route('clients.index') }}"
                        class="{{ request()->routeIs('clients.index', 'clients.show', 'clients.edit') ? 'active' : '' }}">
                        <i data-lucide="users" class="nav-icon"></i>
                        <span class="nav-text">Clients</span>
                    </a>
                </li>
                @endcan
                @can('create clients')
                <li class="nav-item">
                    <a href="{{ route('clients.create') }}"
                        class="{{ request()->routeIs('clients.create') ? 'active' : '' }}">
                        <i data-lucide="user-plus" class="nav-icon"></i>
                        <span class="nav-text">Add Client</span>
                    </a>
                </li>
                @endcan
            </ul>
            @endcanany

            @canany(['view reminders', 'view notifications'])
            <div class="nav-section-title">Reminders & Alerts</div>
            <ul class="nav flex-column">
                @can('view notifications')
                <li class="nav-item">
                    <a href="{{ route('notifications.index') }}"
                        class="{{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                        <i data-lucide="bell" class="nav-icon"></i>
                        <span class="nav-text">Notifications</span>
                        @if ($unreadNotificationsCount > 0)
                            <span class="badge ms-auto" id="sidebarNotifBadge"
                                style="background:#ef4444;color:#ffffff;font-size:.68rem;font-weight:700;border-radius:10px;padding:2px 7px;">{{ $unreadNotificationsCount }}</span>
                        @endif
                    </a>
                </li>
                @endcan
                @can('view reminders')
                <li class="nav-item">
                    <a href="{{ route('reminders.index') }}"
                        class="{{ request()->routeIs('reminders.*') ? 'active' : '' }}">
                        <i data-lucide="alarm-clock" class="nav-icon"></i>
                        <span class="nav-text">Reminders</span>
                        @if ($pendingReminders > 0)
                            <span class="badge ms-auto" id="sidebarReminderBadge"
                                style="background:#ff7828;color:#ffffff;font-size:.68rem;font-weight:700;border-radius:10px;padding:2px 7px;">{{ $pendingReminders }}</span>
                        @endif
                    </a>
                </li>
                @endcan
            </ul>
            @endcanany

            @can('view reports')
            <div class="nav-section-title">Analytics</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="{{ route('reports.index') }}"
                        class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <i data-lucide="bar-chart-2" class="nav-icon"></i>
                        <span class="nav-text">Reports & Analytics</span>
                    </a>
                </li>
            </ul>
            @endcan

            @canany(['view users', 'view roles', 'view settings'])
                <div class="nav-section-title">Management</div>
                <ul class="nav flex-column">
                    @can('view users')
                    <li class="nav-item">
                        <a href="{{ route('users.index') }}"
                            class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <i data-lucide="user-cog" class="nav-icon"></i>
                            <span class="nav-text">Team Members</span>
                        </a>
                    </li>
                    @endcan
                    @can('view roles')
                    <li class="nav-item">
                        <a href="{{ route('roles.index') }}"
                            class="{{ request()->routeIs('roles.*') ? 'active' : '' }}">
                            <i data-lucide="shield-check" class="nav-icon"></i>
                            <span class="nav-text">Roles & Access</span>
                        </a>
                    </li>
                    @endcan
                    @can('view settings')
                    <li class="nav-item">
                        <a href="{{ route('settings.index') }}"
                            class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                            <i data-lucide="settings" class="nav-icon"></i>
                            <span class="nav-text">Settings</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            @endcanany

            {{-- Extra bottom spacing for mobile scrolling comfort --}}
            <div class="sidebar-bottom-spacer" style="height: 3rem; flex-shrink: 0;" aria-hidden="true"></div>
        </nav>
    </aside>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <header class="top-header">
            <div class="d-flex align-items-center gap-3">
                <button class="btn-icon sidebar-toggle" onclick="toggleSidebar()" title="Toggle Navigation">
                    <i data-lucide="menu" style="width:16px;height:16px;"></i>
                </button>
                <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
            </div>

            <!-- Global Search -->
            <div class="d-none d-lg-block" style="flex: 1; max-width: 360px; margin: 0 1rem;">
                <form action="{{ route('tasks.index') }}" method="GET" class="position-relative mb-0">
                    <i data-lucide="search"
                        style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: var(--text-muted);"></i>
                    <input type="text" name="search" id="globalSearchInput" class="form-control-dark w-100"
                        placeholder="Search tasks... (Press '/' to focus)"
                        style="padding-left: 34px; padding-right: 38px; height: 36px; font-size: .8rem; border-radius: 20px;">
                    <kbd
                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: var(--bg-secondary); border: 1px solid var(--border); color: var(--text-muted); font-size: .65rem; padding: 1px 5px; border-radius: 4px; pointer-events: none;">/</kbd>
                </form>
            </div>

            <div class="header-actions">
                <!-- Quick Create Dropdown -->
                <div class="dropdown">
                    <button class="btn-gradient dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"
                        style="padding: .42rem .9rem; font-size: .82rem; border-radius: 8px;">
                        <i data-lucide="plus" style="width:14px;height:14px;"></i>
                        <span class="d-none d-sm-inline">Create</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg"
                        style="min-width: 190px; border-radius: 12px;">
                        <li>
                            <h6 class="dropdown-header text-uppercase"
                                style="font-size:.65rem; color:var(--text-muted); letter-spacing:.05em;">Quick Actions
                            </h6>
                        </li>
                        @can('create tasks')
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                href="{{ route('tasks.create') }}">
                                <i data-lucide="list-plus" style="width:14px;height:14px;color:var(--accent);"></i>
                                New Task
                            </a>
                        </li>
                        @endcan
                        @can('create clients')
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                href="{{ route('clients.create') }}">
                                <i data-lucide="user-plus" style="width:14px;height:14px;color:var(--success);"></i>
                                Add Client
                            </a>
                        </li>
                        @endcan
                        @can('create reminders')
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                href="{{ route('reminders.create') }}">
                                <i data-lucide="bell-plus" style="width:14px;height:14px;color:var(--warning);"></i>
                                Set Reminder
                            </a>
                        </li>
                        @endcan
                        @canany(['create users', 'create roles'])
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            @can('create users')
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                    href="{{ route('users.create') }}">
                                    <i data-lucide="user-check" style="width:14px;height:14px;color:var(--info);"></i> Add
                                    Member
                                </a>
                            </li>
                            @endcan
                            @can('create roles')
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                    href="{{ route('roles.create') }}">
                                    <i data-lucide="shield-plus" style="width:14px;height:14px;color:#ff7828;"></i> Add
                                    Role
                                </a>
                            </li>
                            @endcan
                        @endcanany
                    </ul>
                </div>

                <!-- Theme Toggle Button -->
                <button class="btn-icon theme-toggle-btn" id="themeToggleBtn" onclick="toggleTheme()"
                    title="Switch Light / Dark Theme" aria-label="Toggle Theme">
                    <i data-lucide="sun" id="themeIconSun" style="width:16px;height:16px;"></i>
                    <i data-lucide="moon" id="themeIconMoon" style="width:16px;height:16px;"></i>
                </button>

                <!-- Kanban quick link -->
                <a href="{{ route('tasks.kanban') }}" class="btn-icon d-none d-md-flex" title="Kanban Board">
                    <i data-lucide="kanban" style="width:15px;height:15px;"></i>
                </a>

                <!-- Notifications Dropdown -->
                <div class="dropdown" id="topbarNotificationsDropdown">
                    <button class="btn-icon position-relative" data-bs-toggle="dropdown" title="Notifications" aria-expanded="false" id="notifBellButton">
                        <i data-lucide="bell" style="width:15px;height:15px;"></i>
                        <span class="notification-badge" id="topbarNotifBadge" style="{{ $unreadNotificationsCount > 0 ? '' : 'display:none;' }}">
                            {{ min($unreadNotificationsCount, 9) }}
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end shadow-lg"
                        style="width:340px; max-height:450px; overflow-y:auto; border-radius: 14px; padding: 0; border: 1px solid var(--border);">
                        <div class="d-flex align-items-center justify-content-between p-3 border-bottom"
                            style="border-color: var(--border) !important; background: var(--bg-card);">
                            <div class="d-flex align-items-center gap-2">
                                <span style="font-size:.88rem; font-weight:700; color:var(--text-primary);">Notifications</span>
                                <span class="badge" id="dropdownUnreadBadge"
                                    style="background:rgba(239,68,68,0.15); color:#ef4444; font-size:.65rem; border-radius:12px; font-weight:700; {{ $unreadNotificationsCount > 0 ? '' : 'display:none;' }}">
                                    <span id="dropdownUnreadCount">{{ $unreadNotificationsCount }}</span> New
                                </span>
                            </div>
                            @if($unreadNotificationsCount > 0)
                                <button type="button" onclick="markAllNotificationsRead(event)"
                                    style="background:none; border:none; padding:0; font-size:.75rem; color:var(--accent); cursor:pointer; text-decoration:none; font-weight:600;">
                                    Mark all read
                                </button>
                            @endif
                        </div>
                        <div class="p-2" id="headerNotifList">
                            @forelse($headerNotifications as $notif)
                                @php
                                    $isUnread = !$notif->isRead();
                                    $badgeColor = $notif->type_badge_color;
                                @endphp
                                <a class="dropdown-item d-flex align-items-start gap-2 p-2 rounded-2 mb-1 notif-dropdown-item {{ $isUnread ? 'fw-bold' : '' }}"
                                    href="{{ route('notifications.read-and-redirect', $notif) }}"
                                    style="position:relative; {{ $isUnread ? 'background: rgba(99,102,241,0.04);' : '' }}">
                                    <div
                                        style="width:30px; height:30px; border-radius:8px; background:{{ $badgeColor }}18; color:{{ $badgeColor }}; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:2px;">
                                        <i data-lucide="{{ $notif->default_icon }}"
                                            style="width:14px;height:14px;"></i>
                                    </div>
                                    <div style="flex:1; min-width:0;">
                                        <div class="d-flex align-items-center justify-content-between gap-1">
                                            <span style="font-size:.78rem; font-weight:650; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                                {{ $notif->title }}
                                            </span>
                                            @if($isUnread)
                                                <span style="width:6px; height:6px; border-radius:50%; background:#ef4444; display:inline-block; flex-shrink:0;"></span>
                                            @endif
                                        </div>
                                        @if($notif->message)
                                            <div style="font-size:.72rem; color:var(--text-muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; font-weight:normal;">
                                                {{ $notif->message }}
                                            </div>
                                        @endif
                                        <div
                                            style="font-size:.68rem; color:var(--text-muted); display:flex; align-items:center; gap:3px; margin-top:2px; font-weight:normal;">
                                            <i data-lucide="clock" style="width:9px;height:9px;"></i>
                                            {{ $notif->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div
                                    style="padding:1.75rem 1rem; font-size:.8rem; color:var(--text-muted); text-align:center;">
                                    <i data-lucide="bell-off"
                                        style="width:24px;height:24px; display:block; margin:0 auto .5rem; opacity:.4;"></i>
                                    No notifications yet
                                </div>
                            @endforelse
                        </div>
                        <div class="p-2 border-top text-center"
                            style="border-color: var(--border) !important; background: var(--bg-hover);">
                            <a href="{{ route('notifications.index') }}"
                                style="font-size:.78rem; color:var(--accent); text-decoration:none; font-weight:600;">
                                View all notifications &rarr;
                            </a>
                        </div>
                    </div>
                </div>

                <!-- User Profile Dropdown in Topbar -->
                <div class="dropdown">
                    <button class="user-nav-btn" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ auth()->user()->avatar_url }}" alt="Avatar"
                            style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover; border: 1.5px solid var(--accent);">
                        <span class="d-none d-md-inline font-weight-500"
                            style="font-size: .8rem; color: var(--text-primary); max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ auth()->user()->name }}
                        </span>
                        <i data-lucide="chevron-down"
                            style="width: 13px; height: 13px; color: var(--text-muted);"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg"
                        style="min-width: 220px; border-radius: 12px; padding: .5rem;">
                        <li class="px-3 py-2 border-bottom mb-2" style="border-color: var(--border) !important;">
                            <div style="font-size: .85rem; font-weight: 600; color: var(--text-primary);">
                                {{ auth()->user()->name }}</div>
                            <div style="font-size: .72rem; color: var(--text-muted); text-truncate">
                                {{ auth()->user()->email }}</div>
                            <div class="mt-1">
                                <span class="badge"
                                    style="background: var(--accent-light); color: var(--accent); font-size: .65rem;">
                                    {{ ucfirst(str_replace('-', ' ', auth()->user()->getRoleNames()->first() ?? 'User')) }}
                                </span>
                            </div>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                href="{{ route('profile.edit') }}">
                                <i data-lucide="user" style="width:14px;height:14px;color:var(--text-muted);"></i>
                                Profile Settings
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                href="{{ route('tasks.index', ['assigned_to' => auth()->id()]) }}">
                                <i data-lucide="check-square"
                                    style="width:14px;height:14px;color:var(--text-muted);"></i> My Tasks
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                href="{{ route('reminders.index') }}">
                                <i data-lucide="bell" style="width:14px;height:14px;color:var(--text-muted);"></i> My
                                Reminders
                            </a>
                        </li>
                        @can('view reports')
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                    href="{{ route('reports.index') }}">
                                    <i data-lucide="bar-chart-2" style="width:14px;height:14px;color:#10b981;"></i>
                                    Reports & Analytics
                                </a>
                            </li>
                        @endcan
                        @can('view roles')
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                    href="{{ route('roles.index') }}">
                                    <i data-lucide="shield-check" style="width:14px;height:14px;color:var(--info);"></i>
                                    Roles & Permissions
                                </a>
                            </li>
                        @endcan
                        @can('view settings')
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                    href="{{ route('settings.index') }}">
                                    <i data-lucide="settings" style="width:14px;height:14px;color:var(--accent);"></i>
                                    System Settings
                                </a>
                            </li>
                        @endcan
                        <li>
                            <hr class="dropdown-divider" style="border-color: var(--border) !important;">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger">
                                    <i data-lucide="log-out" style="width:14px;height:14px;"></i> Sign Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

    {{-- ── Floating Action Alert Toast Container ── --}}
    <div id="appToastContainer" class="app-toast-container" aria-live="polite" aria-atomic="true">
        @if (session('success'))
            <div class="app-toast-alert app-toast-success" data-auto-dismiss="5000">
                <div class="toast-indicator"></div>
                <div class="toast-icon-wrap">
                    <i data-lucide="check-circle-2" style="width:20px;height:20px;"></i>
                </div>
                <div class="toast-content">
                    <div class="toast-title">Success</div>
                    <div class="toast-message">{{ session('success') }}</div>
                </div>
                <button type="button" class="toast-close" onclick="dismissToast(this.closest('.app-toast-alert'))" aria-label="Close">
                    <i data-lucide="x" style="width:14px;height:14px;"></i>
                </button>
                <div class="toast-progress"><div class="toast-progress-bar" style="background:#10b981;"></div></div>
            </div>
        @endif

        @if (session('error'))
            <div class="app-toast-alert app-toast-danger" data-auto-dismiss="7000">
                <div class="toast-indicator"></div>
                <div class="toast-icon-wrap">
                    <i data-lucide="alert-circle" style="width:20px;height:20px;"></i>
                </div>
                <div class="toast-content">
                    <div class="toast-title">Error</div>
                    <div class="toast-message">{{ session('error') }}</div>
                </div>
                <button type="button" class="toast-close" onclick="dismissToast(this.closest('.app-toast-alert'))" aria-label="Close">
                    <i data-lucide="x" style="width:14px;height:14px;"></i>
                </button>
                <div class="toast-progress"><div class="toast-progress-bar" style="background:#ef4444;"></div></div>
            </div>
        @endif

        @if (session('warning'))
            <div class="app-toast-alert app-toast-warning" data-auto-dismiss="6000">
                <div class="toast-indicator"></div>
                <div class="toast-icon-wrap">
                    <i data-lucide="alert-triangle" style="width:20px;height:20px;"></i>
                </div>
                <div class="toast-content">
                    <div class="toast-title">Warning</div>
                    <div class="toast-message">{{ session('warning') }}</div>
                </div>
                <button type="button" class="toast-close" onclick="dismissToast(this.closest('.app-toast-alert'))" aria-label="Close">
                    <i data-lucide="x" style="width:14px;height:14px;"></i>
                </button>
                <div class="toast-progress"><div class="toast-progress-bar" style="background:#f59e0b;"></div></div>
            </div>
        @endif

        @if (session('status'))
            @php
                $statusMsg = session('status');
                $isSuccessStatus = in_array($statusMsg, ['profile-updated', 'password-updated', 'verification-link-sent']);
                if ($statusMsg === 'profile-updated') {
                    $statusMsg = 'Profile updated successfully!';
                } elseif ($statusMsg === 'password-updated') {
                    $statusMsg = 'Password updated successfully!';
                } elseif ($statusMsg === 'verification-link-sent') {
                    $statusMsg = 'A new verification link has been sent to your email address.';
                }
            @endphp
            <div class="app-toast-alert {{ $isSuccessStatus ? 'app-toast-success' : 'app-toast-info' }}" data-auto-dismiss="5000">
                <div class="toast-indicator"></div>
                <div class="toast-icon-wrap">
                    <i data-lucide="{{ $isSuccessStatus ? 'check-circle-2' : 'info' }}" style="width:20px;height:20px;"></i>
                </div>
                <div class="toast-content">
                    <div class="toast-title">{{ $isSuccessStatus ? 'Success' : 'Notice' }}</div>
                    <div class="toast-message">{{ $statusMsg }}</div>
                </div>
                <button type="button" class="toast-close" onclick="dismissToast(this.closest('.app-toast-alert'))" aria-label="Close">
                    <i data-lucide="x" style="width:14px;height:14px;"></i>
                </button>
                <div class="toast-progress"><div class="toast-progress-bar" style="background:{{ $isSuccessStatus ? '#10b981' : '#6366f1' }};"></div></div>
            </div>
        @endif

        @if (isset($errors) && $errors->any() && !request()->is('login*') && !request()->is('register*') && !request()->is('password*'))
            <div class="app-toast-alert app-toast-danger" data-auto-dismiss="8000">
                <div class="toast-indicator"></div>
                <div class="toast-icon-wrap">
                    <i data-lucide="alert-octagon" style="width:20px;height:20px;"></i>
                </div>
                <div class="toast-content">
                    <div class="toast-title">Validation Error</div>
                    <ul class="toast-error-list">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="toast-close" onclick="dismissToast(this.closest('.app-toast-alert'))" aria-label="Close">
                    <i data-lucide="x" style="width:14px;height:14px;"></i>
                </button>
                <div class="toast-progress"><div class="toast-progress-bar" style="background:#ef4444;"></div></div>
            </div>
        @endif
    </div>

        <main class="page-content fade-in">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        lucide.createIcons();

        // ── Theme Switcher Logic ──
        function applyTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            document.documentElement.setAttribute('data-bs-theme', theme);
            localStorage.setItem('workflow_theme', theme);

            window.dispatchEvent(new CustomEvent('themeChanged', {
                detail: {
                    theme
                }
            }));
        }

        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
            const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
            applyTheme(nextTheme);
        }

        function openSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (sidebar) sidebar.classList.add('open');
            if (overlay) overlay.classList.add('open');
            document.body.classList.add('sidebar-open');
            document.documentElement.classList.add('sidebar-open');
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (sidebar) sidebar.classList.remove('open');
            if (overlay) overlay.classList.remove('open');
            document.body.classList.remove('sidebar-open');
            document.documentElement.classList.remove('sidebar-open');
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar && sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        }

        // Prevent body scrolling through touch moves on backdrop overlay
        const sidebarOverlayEl = document.getElementById('sidebarOverlay');
        if (sidebarOverlayEl) {
            sidebarOverlayEl.addEventListener('touchmove', function(e) {
                e.preventDefault();
            }, { passive: false });
        }

        // Close sidebar on Escape key or on screen resize beyond mobile breakpoint
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSidebar();
            }
        });

        window.addEventListener('resize', function() {
            if (window.innerWidth > 991) {
                closeSidebar();
            }
        });

        // Close sidebar on mobile when navigating links
        const sidebarNavEl = document.getElementById('sidebarNav');
        if (sidebarNavEl) {
            sidebarNavEl.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (link && window.innerWidth <= 991) {
                    closeSidebar();
                }
            });
        }
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(a => bootstrap.Alert.getOrCreateInstance(a).close());
        }, 4000);

        // Sidebar Module Search Real-time Filter
        const moduleSearchInput = document.getElementById('sidebarModuleSearch');
        if (moduleSearchInput) {
            moduleSearchInput.addEventListener('input', function() {
                const q = this.value.toLowerCase().trim();
                const navSections = document.querySelectorAll('#sidebarNav > .nav-section-title');

                navSections.forEach(title => {
                    const ul = title.nextElementSibling;
                    if (ul && ul.classList.contains('nav')) {
                        const items = ul.querySelectorAll('.nav-item');
                        let visibleCount = 0;
                        items.forEach(item => {
                            const text = item.textContent.toLowerCase();
                            if (text.includes(q)) {
                                item.style.display = '';
                                visibleCount++;
                            } else {
                                item.style.display = 'none';
                            }
                        });
                        if (q !== '' && visibleCount === 0) {
                            title.style.display = 'none';
                            ul.style.display = 'none';
                        } else {
                            title.style.display = '';
                            ul.style.display = '';
                        }
                    }
                });
            });
        }

        // Global Search Shortcut (/ or Ctrl+K)
        document.addEventListener('keydown', (e) => {
            if ((e.key === '/' && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !==
                    'TEXTAREA') ||
                ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k')) {
                e.preventDefault();
                const searchInput = document.getElementById('globalSearchInput');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            }
        });
    </script>

    <!-- Reminder Live Toast Notification Container -->
    <div id="reminderToastContainer" style="position: fixed; bottom: 24px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 12px; max-width: 400px; width: calc(100vw - 32px); pointer-events: none;"></div>

    <style>
        .reminder-toast-card {
            pointer-events: auto;
            background: var(--bg-card, #1e293b);
            border: 1px solid var(--accent, #6366f1);
            box-shadow: 0 10px 30px rgba(0,0,0,0.35), 0 0 20px rgba(99, 102, 241, 0.25);
            border-radius: 12px;
            padding: 16px;
            color: var(--text-primary, #f8fafc);
            animation: slideInUp 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }
        .reminder-toast-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #ff7828, #6366f1);
        }
        @keyframes slideInUp {
            from {
                transform: translateY(40px) scale(0.95);
                opacity: 0;
            }
            to {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }
        .reminder-toast-bell-pulse {
            animation: ringPulse 1.5s infinite;
        }
        @keyframes ringPulse {
            0%, 100% { transform: rotate(0deg); }
            10%, 30% { transform: rotate(-15deg); }
            20%, 40% { transform: rotate(15deg); }
            50% { transform: rotate(0deg); }
        }
    </style>

    <script>
        // Real-Time Notification & Reminder Polling System
        (function() {
            const checkUrl = "{{ route('notifications.check-due') }}";
            let audioCtx = null;

            // Melodic chime sound using Web Audio API
            function playReminderChime() {
                try {
                    if (!audioCtx) {
                        audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    }
                    if (audioCtx.state === 'suspended') {
                        audioCtx.resume();
                    }
                    const now = audioCtx.currentTime;
                    
                    const osc1 = audioCtx.createOscillator();
                    const gain1 = audioCtx.createGain();
                    osc1.type = 'sine';
                    osc1.frequency.setValueAtTime(659.25, now);
                    gain1.gain.setValueAtTime(0.2, now);
                    gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.4);
                    osc1.connect(gain1);
                    gain1.connect(audioCtx.destination);
                    osc1.start(now);
                    osc1.stop(now + 0.4);

                    const osc2 = audioCtx.createOscillator();
                    const gain2 = audioCtx.createGain();
                    osc2.type = 'sine';
                    osc2.frequency.setValueAtTime(880, now + 0.15);
                    gain2.gain.setValueAtTime(0.25, now + 0.15);
                    gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.7);
                    osc2.connect(gain2);
                    gain2.connect(audioCtx.destination);
                    osc2.start(now + 0.15);
                    osc2.stop(now + 0.7);
                } catch (e) {
                    console.warn('Audio playback not permitted or supported yet:', e);
                }
            }

            function escapeHtml(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // Show floating toast alert on the page
            function showLiveToast(alert) {
                const container = document.getElementById('reminderToastContainer');
                if (!container) return;

                const card = document.createElement('div');
                card.className = 'reminder-toast-card';
                card.id = 'live-alert-' + alert.id;
                
                let extraInfo = '';
                if (alert.client_name) {
                    extraInfo += `<div style="font-size: 0.75rem; color: var(--text-muted);"><i data-lucide="briefcase" style="width:12px;height:12px;display:inline;"></i> ${escapeHtml(alert.client_name)}</div>`;
                }
                if (alert.task_title) {
                    extraInfo += `<div style="font-size: 0.75rem; color: var(--text-muted);"><i data-lucide="check-square" style="width:12px;height:12px;display:inline;"></i> ${escapeHtml(alert.task_title)}</div>`;
                }

                const typeColor = alert.type === 'task_deadline' ? '#ef4444' : '#ff7828';
                const badgeBg = alert.type === 'task_deadline' ? 'bg-danger-subtle text-danger border-danger-subtle' : 'bg-primary-subtle text-primary border-primary-subtle';

                card.innerHTML = `
                    <div class="d-flex align-items-start gap-2 mb-2">
                        <div class="reminder-toast-bell-pulse" style="flex-shrink:0; color:${typeColor};">
                            <i data-lucide="bell-ring" style="width:20px;height:20px;"></i>
                        </div>
                        <div class="flex-grow-1" style="min-width:0;">
                            <div class="d-flex align-items-center justify-content-between gap-2">
                                <span class="badge ${badgeBg} border" style="font-size:0.68rem;">${escapeHtml(alert.type_label || 'Alert')}</span>
                                <small style="color:var(--text-muted); font-size:0.72rem;">${escapeHtml(alert.remind_at || 'Just now')}</small>
                            </div>
                            <h6 class="mb-1 mt-1 text-truncate" style="font-size:0.92rem; font-weight:700; color:var(--text-primary);">${escapeHtml(alert.title)}</h6>
                            ${extraInfo}
                        </div>
                        <button type="button" class="btn-close btn-close-white" style="font-size:0.65rem; filter: invert(var(--bs-theme-invert, 0.8));" aria-label="Close" onclick="this.closest('.reminder-toast-card').remove()"></button>
                    </div>
                    ${alert.description ? `<p class="mb-2 text-truncate" style="font-size:0.78rem; color:var(--text-muted);">${escapeHtml(alert.description)}</p>` : ''}
                    <div class="d-flex justify-content-end gap-2 pt-1 border-top" style="border-color: var(--border) !important;">
                        <a href="${alert.url || '{{ route('notifications.index') }}'}" class="btn btn-sm btn-primary py-1 px-3" style="font-size:0.78rem; font-weight:600; border-radius:6px;">
                            <i data-lucide="external-link" style="width:13px;height:13px;display:inline;margin-right:3px;"></i> View Details
                        </a>
                    </div>
                `;

                container.appendChild(card);
                if (window.lucide) {
                    lucide.createIcons();
                }

                // Auto dismiss after 20 seconds
                setTimeout(() => {
                    if (card.parentNode) {
                        card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(20px)';
                        setTimeout(() => card.remove(), 400);
                    }
                }, 20000);
            }

            // Desktop Browser Notification
            function showBrowserNotification(alert) {
                if (!("Notification" in window)) return;

                if (Notification.permission === "granted") {
                    const n = new Notification(alert.title, {
                        body: `${alert.type_label || ''} • ${alert.remind_at || ''}\n${alert.description || ''}`,
                        icon: '{{ $appFaviconUrl ?? \App\Models\Setting::getFaviconUrl() }}'
                    });
                    n.onclick = function() {
                        window.focus();
                        window.location.href = alert.url || '{{ route('notifications.index') }}';
                    };
                } else if (Notification.permission !== "denied") {
                    Notification.requestPermission();
                }
            }

            async function checkDueNotifications() {
                try {
                    const res = await fetch(checkUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    
                    // Update badge counts
                    if (typeof data.unread_count !== 'undefined') {
                        const topbarBadge = document.getElementById('topbarNotifBadge');
                        const sidebarBadge = document.getElementById('sidebarNotifBadge');
                        const dropdownBadge = document.getElementById('dropdownUnreadBadge');
                        const dropdownCount = document.getElementById('dropdownUnreadCount');

                        if (topbarBadge) {
                            if (data.unread_count > 0) {
                                topbarBadge.style.display = '';
                                topbarBadge.textContent = Math.min(data.unread_count, 9);
                            } else {
                                topbarBadge.style.display = 'none';
                            }
                        }
                        if (sidebarBadge) {
                            if (data.unread_count > 0) {
                                sidebarBadge.style.display = '';
                                sidebarBadge.textContent = data.unread_count;
                            } else {
                                sidebarBadge.style.display = 'none';
                            }
                        }
                        if (dropdownBadge && dropdownCount) {
                            if (data.unread_count > 0) {
                                dropdownBadge.style.display = '';
                                dropdownCount.textContent = data.unread_count;
                            } else {
                                dropdownBadge.style.display = 'none';
                            }
                        }
                    }

                    // Process live alerts
                    if (data.alerts && data.alerts.length > 0) {
                        playReminderChime();
                        data.alerts.forEach(alert => {
                            showLiveToast(alert);
                            if (alert.notify_browser) {
                                showBrowserNotification(alert);
                            }
                        });
                    }
                } catch (err) {
                    console.debug('Notification check skipped:', err);
                }
            }

            window.markAllNotificationsRead = async function(e) {
                if (e) e.preventDefault();
                try {
                    const res = await fetch("{{ route('notifications.mark-all-read') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });
                    const data = await res.json();
                    if (data.status === 'success') {
                        const topbarBadge = document.getElementById('topbarNotifBadge');
                        const sidebarBadge = document.getElementById('sidebarNotifBadge');
                        const dropdownBadge = document.getElementById('dropdownUnreadBadge');
                        if (topbarBadge) topbarBadge.style.display = 'none';
                        if (sidebarBadge) sidebarBadge.style.display = 'none';
                        if (dropdownBadge) dropdownBadge.style.display = 'none';
                        document.querySelectorAll('.notif-dropdown-item').forEach(el => {
                            el.classList.remove('fw-bold');
                            el.style.background = '';
                        });
                    }
                } catch (err) {
                    console.error('Failed marking all read:', err);
                }
            };

            // Request notification permission on first user interaction
            document.addEventListener('click', function reqNotif() {
                if ("Notification" in window && Notification.permission === "default") {
                    Notification.requestPermission();
                }
                document.removeEventListener('click', reqNotif);
            }, { once: true });

            // Run check 3s after page load, and poll every 30 seconds
            setTimeout(checkDueNotifications, 3000);
            setInterval(checkDueNotifications, 30000);
        })();

        // ── Unified Floating Toast Alert Helper ──
        (function() {
            function escapeHtml(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            window.dismissToast = function(toastElement) {
                if (!toastElement || toastElement.dataset.dismissing === 'true') return;
                toastElement.dataset.dismissing = 'true';
                toastElement.classList.add('toast-hiding');
                setTimeout(() => {
                    if (toastElement.parentNode) {
                        toastElement.remove();
                    }
                }, 300);
            };

            function setupToastTimer(toast, duration) {
                const progressBar = toast.querySelector('.toast-progress-bar');
                let startTime = Date.now();
                let remaining = duration;
                let timerId = null;
                let animationFrame = null;

                function step() {
                    const elapsed = Date.now() - startTime;
                    const progress = Math.max(0, 1 - (elapsed / duration));
                    if (progressBar) {
                        progressBar.style.width = (progress * 100) + '%';
                    }
                    if (elapsed < duration) {
                        animationFrame = requestAnimationFrame(step);
                    }
                }

                function startTimer() {
                    startTime = Date.now() - (duration - remaining);
                    timerId = setTimeout(() => {
                        window.dismissToast(toast);
                    }, remaining);
                    animationFrame = requestAnimationFrame(step);
                }

                function pauseTimer() {
                    clearTimeout(timerId);
                    cancelAnimationFrame(animationFrame);
                    remaining -= (Date.now() - startTime);
                }

                toast.addEventListener('mouseenter', pauseTimer);
                toast.addEventListener('mouseleave', () => {
                    if (remaining > 0) startTimer();
                });

                startTimer();
            }

            window.showToast = function(message, type = 'success', duration = 5000, customTitle = null) {
                const container = document.getElementById('appToastContainer');
                if (!container) return;

                const meta = {
                    success: { title: 'Success', icon: 'check-circle-2', color: '#10b981', class: 'app-toast-success' },
                    error:   { title: 'Error',   icon: 'alert-circle',    color: '#ef4444', class: 'app-toast-danger' },
                    danger:  { title: 'Error',   icon: 'alert-circle',    color: '#ef4444', class: 'app-toast-danger' },
                    warning: { title: 'Warning', icon: 'alert-triangle',  color: '#f59e0b', class: 'app-toast-warning' },
                    info:    { title: 'Notice',  icon: 'info',            color: '#6366f1', class: 'app-toast-info' },
                }[type] || { title: 'Notice', icon: 'bell', color: '#6366f1', class: 'app-toast-info' };

                const title = customTitle || meta.title;

                const toast = document.createElement('div');
                toast.className = `app-toast-alert ${meta.class}`;
                toast.innerHTML = `
                    <div class="toast-indicator"></div>
                    <div class="toast-icon-wrap">
                        <i data-lucide="${meta.icon}" style="width:20px;height:20px;"></i>
                    </div>
                    <div class="toast-content">
                        <div class="toast-title">${escapeHtml(title)}</div>
                        <div class="toast-message">${message}</div>
                    </div>
                    <button type="button" class="toast-close" onclick="dismissToast(this.closest('.app-toast-alert'))" aria-label="Close">
                        <i data-lucide="x" style="width:14px;height:14px;"></i>
                    </button>
                    <div class="toast-progress"><div class="toast-progress-bar" style="background:${meta.color};"></div></div>
                `;

                container.appendChild(toast);
                if (window.lucide) {
                    lucide.createIcons();
                }

                if (duration > 0) {
                    setupToastTimer(toast, duration);
                }
            };

            // Init timers for existing server-rendered toasts
            document.addEventListener('DOMContentLoaded', function() {
                const existingToasts = document.querySelectorAll('#appToastContainer .app-toast-alert');
                existingToasts.forEach(toast => {
                    const duration = parseInt(toast.dataset.autoDismiss, 10) || 5000;
                    setupToastTimer(toast, duration);
                });
            });
        })();
    </script>
    @stack('scripts')
</body>

</html>
