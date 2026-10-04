@extends('layouts.app')
@section('title', 'System & SMTP Settings')
@section('page-title', 'System Settings')

@push('styles')
<style>
/* ── Settings Container & Cards ── */
.settings-container {
    max-width: 1100px;
    margin: 0 auto;
}

.settings-section-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 1.75rem;
    box-shadow: var(--shadow-card);
    margin-bottom: 2rem;
    scroll-margin-top: 85px;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
.settings-section-card:hover {
    border-color: rgba(99,102,241,0.28);
}
.settings-section-card:last-child {
    margin-bottom: 0;
}

/* ── Section Header ── */
.settings-section-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    border-bottom: 1px solid var(--border);
    padding-bottom: 1.15rem;
    margin-bottom: 1.5rem;
}
.settings-section-header-left {
    display: flex;
    align-items: flex-start;
    gap: 0.85rem;
}
.settings-section-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.settings-section-icon.general {
    background: rgba(99,102,241,0.12);
    color: var(--accent);
}
.settings-section-icon.smtp {
    background: rgba(16,185,129,0.12);
    color: var(--success);
}
.settings-section-icon.test {
    background: rgba(14,165,233,0.12);
    color: var(--info);
}
.settings-section-icon.notification {
    background: rgba(245,158,11,0.12);
    color: var(--warning);
}
.settings-section-header h3 {
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 0.2rem;
}
.settings-section-header p {
    font-size: 0.82rem;
    color: var(--text-muted);
    margin: 0;
}

/* ── Form Inputs & Labels ── */
.form-label-settings {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.4rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.form-hint {
    font-size: 0.74rem;
    color: var(--text-muted);
    margin-top: 0.35rem;
    line-height: 1.4;
}

/* ── Test Box Banner ── */
.smtp-test-box {
    background: linear-gradient(135deg, rgba(99,102,241,0.06), rgba(139,92,246,0.04));
    border: 1px dashed rgba(99,102,241,0.35);
    border-radius: 12px;
    padding: 1.25rem;
}

/* ── Provider Reference Cards ── */
.provider-guide-card {
    background: var(--bg-hover);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 0.9rem;
    font-size: 0.78rem;
    color: var(--text-muted);
    height: 100%;
}
.provider-guide-card strong {
    color: var(--text-primary);
    display: block;
    margin-bottom: 0.35rem;
}
.provider-guide-card code {
    color: var(--accent);
    background: var(--accent-light);
    padding: 1px 5px;
    border-radius: 4px;
    font-size: 0.75rem;
}

/* ── Switch Setting Rows ── */
.switch-setting-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.1rem 0;
    border-bottom: 1px solid var(--border);
}
.switch-setting-item:first-child {
    padding-top: 0;
}
.switch-setting-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}
.switch-setting-label {
    font-size: 0.88rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}
.switch-setting-desc {
    font-size: 0.78rem;
    color: var(--text-muted);
    margin: 0.2rem 0 0;
    line-height: 1.4;
}

/* ── Action Footer ── */
.section-action-footer {
    margin-top: 1.5rem;
    padding-top: 1.25rem;
    border-top: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.75rem;
}

/* ── Branding Assets Upload ── */
.brand-asset-card {
    background: var(--bg-hover);
    border: 1px dashed var(--border);
    border-radius: 12px;
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    transition: border-color 0.2s ease, background 0.2s ease;
    height: 100%;
}
.brand-asset-card:hover {
    border-color: var(--accent);
    background: rgba(99,102,241,0.03);
}
.brand-asset-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
}
.brand-asset-title {
    font-size: 0.88rem;
    font-weight: 700;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 0.45rem;
}
.brand-asset-badge {
    font-size: 0.68rem;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 6px;
    background: var(--bg-card);
    border: 1px solid var(--border);
    color: var(--text-muted);
    white-space: nowrap;
}
.brand-preview-area {
    display: flex;
    align-items: center;
    gap: 1.15rem;
}
.brand-preview-box {
    width: 72px;
    height: 72px;
    border-radius: 12px;
    background: var(--bg-card);
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
    position: relative;
    box-shadow: var(--shadow-card);
}
.brand-preview-box img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    padding: 4px;
}
.brand-preview-box.favicon-box {
    width: 54px;
    height: 54px;
}
.brand-upload-input-wrap {
    flex: 1;
    min-width: 0;
}

/* Mobile adjustments */
@media (max-width: 767.98px) {
    .settings-section-card {
        padding: 1.25rem;
    }
    .brand-preview-area {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
@endpush

@section('content')

<div class="settings-container">

    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-left">
            <h2>System & SMTP Settings</h2>
            <p>Manage application branding, mail server configurations, and notification preferences all on one unified page</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('dashboard') }}" class="btn-ghost">
                <i data-lucide="arrow-left" style="width:14px;height:14px;"></i> Back to Dashboard
            </a>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         SECTION 1: General & Company Branding
    ══════════════════════════════════════════════════════════════════════ --}}
    <div class="settings-section-card" id="section-general">
        <div class="settings-section-header">
            <div class="settings-section-header-left">
                <div class="settings-section-icon general">
                    <i data-lucide="sliders" style="width:20px;height:20px;"></i>
                </div>
                <div>
                    <h3>General & Company Branding</h3>
                    <p>Configure company details, regional timezone, currency, and default task settings</p>
                </div>
            </div>
            <span class="badge d-none d-sm-inline-block" style="background:var(--accent-light);color:var(--accent);font-size:0.75rem;padding:0.4rem 0.75rem;border-radius:20px;">
                General
            </span>
        </div>

        <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_group" value="system">

            {{-- ── Logo & Favicon Upload Cards ── --}}
            <div class="row g-3 mb-4">
                {{-- Logo Upload --}}
                <div class="col-md-6">
                    <div class="brand-asset-card">
                        <div class="brand-asset-header">
                            <span class="brand-asset-title">
                                <i data-lucide="image" style="width:17px;height:17px;color:var(--accent);"></i>
                                Application & Sidebar Logo
                            </span>
                            <span class="brand-asset-badge">PNG, JPG, SVG, WEBP (Max 2MB)</span>
                        </div>

                        <div class="brand-preview-area">
                            <div class="brand-preview-box" id="logoPreviewContainer">
                                @if(\App\Models\Setting::getLogoUrl())
                                    <img src="{{ \App\Models\Setting::getLogoUrl() }}" id="logoPreviewImg" alt="Logo Preview">
                                @else
                                    <div id="logoPreviewPlaceholder" class="d-flex flex-column align-items-center justify-content-center text-muted">
                                        <i data-lucide="zap" style="width:28px;height:28px;color:var(--accent);"></i>
                                    </div>
                                    <img src="" id="logoPreviewImg" class="d-none" alt="Logo Preview">
                                @endif
                            </div>

                            <div class="brand-upload-input-wrap">
                                <label class="form-label-settings mb-1">Choose Logo File</label>
                                <input type="file" name="app_logo" id="appLogoInput" class="form-control form-control-dark" accept="image/png,image/jpeg,image/svg+xml,image/webp" onchange="previewBrandImage(this, 'logoPreviewImg', 'logoPreviewPlaceholder')">
                                <div class="form-hint">Shown on sidebar top, login screen, and email notifications.</div>

                                @if(\App\Models\Setting::getLogoUrl())
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="remove_app_logo" value="1" id="removeLogoCheck">
                                        <label class="form-check-label" for="removeLogoCheck" style="font-size:0.75rem;color:var(--danger);cursor:pointer;">
                                            <i data-lucide="trash-2" style="width:12px;height:12px;vertical-align:-1px;"></i> Remove Current Logo
                                        </label>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Favicon Upload --}}
                <div class="col-md-6">
                    <div class="brand-asset-card">
                        <div class="brand-asset-header">
                            <span class="brand-asset-title">
                                <i data-lucide="globe" style="width:17px;height:17px;color:var(--info);"></i>
                                Browser Meta Favicon
                            </span>
                            <span class="brand-asset-badge">ICO, PNG, SVG (Max 1MB)</span>
                        </div>

                        <div class="brand-preview-area">
                            <div class="brand-preview-box favicon-box" id="faviconPreviewContainer">
                                @if(\App\Models\Setting::get('app_favicon') && file_exists(public_path(\App\Models\Setting::get('app_favicon'))))
                                    <img src="{{ \App\Models\Setting::getFaviconUrl() }}" id="faviconPreviewImg" alt="Favicon Preview">
                                @else
                                    <div id="faviconPreviewPlaceholder" class="d-flex flex-column align-items-center justify-content-center text-muted">
                                        <i data-lucide="bookmark" style="width:22px;height:22px;color:var(--info);"></i>
                                    </div>
                                    <img src="" id="faviconPreviewImg" class="d-none" alt="Favicon Preview">
                                @endif
                            </div>

                            <div class="brand-upload-input-wrap">
                                <label class="form-label-settings mb-1">Choose Favicon File</label>
                                <input type="file" name="app_favicon" id="appFaviconInput" class="form-control form-control-dark" accept=".ico,image/png,image/svg+xml,image/x-icon,image/vnd.microsoft.icon" onchange="previewBrandImage(this, 'faviconPreviewImg', 'faviconPreviewPlaceholder')">
                                <div class="form-hint">Shown on browser tabs next to page title. Recommended 32×32px.</div>

                                @if(\App\Models\Setting::get('app_favicon') && file_exists(public_path(\App\Models\Setting::get('app_favicon'))))
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="remove_app_favicon" value="1" id="removeFaviconCheck">
                                        <label class="form-check-label" for="removeFaviconCheck" style="font-size:0.75rem;color:var(--danger);cursor:pointer;">
                                            <i data-lucide="trash-2" style="width:12px;height:12px;vertical-align:-1px;"></i> Remove Current Favicon
                                        </label>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label-settings">Application Name <span class="text-danger">*</span></label>
                    <input type="text" name="app_name" class="form-control form-control-dark" value="{{ $allSettings['app_name'] ?? 'WorkFlow Pro' }}" required>
                    <div class="form-hint">Displayed on page headers, title tags, login page, and email footers.</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label-settings">Company / Organization Name</label>
                    <input type="text" name="company_name" class="form-control form-control-dark" value="{{ $allSettings['company_name'] ?? 'WorkFlow Pro Ltd.' }}">
                    <div class="form-hint">Official company name displayed in system reports and invoices.</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label-settings">Support / Company Email</label>
                    <input type="email" name="company_email" class="form-control form-control-dark" value="{{ $allSettings['company_email'] ?? 'support@workflowpro.com' }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label-settings">Contact Phone Number</label>
                    <input type="text" name="company_phone" class="form-control form-control-dark" value="{{ $allSettings['company_phone'] ?? '+880 1700-000000' }}">
                </div>

                <div class="col-12">
                    <label class="form-label-settings">Company Office Address</label>
                    <textarea name="company_address" class="form-control form-control-dark" rows="2">{{ $allSettings['company_address'] ?? 'Dhaka, Bangladesh' }}</textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label-settings">Timezone</label>
                    <select name="timezone" class="form-select form-select-dark">
                        @php
                            $tz = $allSettings['timezone'] ?? 'Asia/Dhaka';
                            $timezones = [
                                'Asia/Dhaka'       => '(GMT+06:00) Dhaka / Bangladesh',
                                'UTC'              => '(GMT+00:00) UTC Universal',
                                'Asia/Kolkata'     => '(GMT+05:30) India / Kolkata',
                                'Asia/Dubai'       => '(GMT+04:00) Dubai / UAE',
                                'Asia/Singapore'   => '(GMT+08:00) Singapore',
                                'Europe/London'    => '(GMT+00:00 / +01:00) London',
                                'America/New_York' => '(GMT-05:00 / -04:00) Eastern Time (US)',
                                'America/Chicago'  => '(GMT-06:00 / -05:00) Central Time (US)',
                                'America/Los_Angeles' => '(GMT-08:00 / -07:00) Pacific Time (US)',
                            ];
                        @endphp
                        @foreach($timezones as $tzVal => $tzLabel)
                            <option value="{{ $tzVal }}" {{ $tz === $tzVal ? 'selected' : '' }}>{{ $tzLabel }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label-settings">Currency Symbol</label>
                    <input type="text" name="currency_symbol" class="form-control form-control-dark" value="{{ $allSettings['currency_symbol'] ?? '$' }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label-settings">Date Format</label>
                    <select name="date_format" class="form-select form-select-dark">
                        @php $df = $allSettings['date_format'] ?? 'd M, Y'; @endphp
                        <option value="d M, Y" {{ $df === 'd M, Y' ? 'selected' : '' }}>30 Sep, 2026 (d M, Y)</option>
                        <option value="Y-m-d" {{ $df === 'Y-m-d' ? 'selected' : '' }}>2026-09-30 (Y-m-d)</option>
                        <option value="d/m/Y" {{ $df === 'd/m/Y' ? 'selected' : '' }}>30/09/2026 (d/m/Y)</option>
                        <option value="m/d/Y" {{ $df === 'm/d/Y' ? 'selected' : '' }}>09/30/2026 (m/d/Y)</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label-settings">Default Task Priority</label>
                    @php $defPri = $allSettings['default_task_priority'] ?? 'medium'; @endphp
                    <select name="default_task_priority" class="form-select form-select-dark">
                        <option value="high" {{ $defPri === 'high' ? 'selected' : '' }}>High Priority</option>
                        <option value="medium" {{ $defPri === 'medium' ? 'selected' : '' }}>Medium Priority</option>
                        <option value="low" {{ $defPri === 'low' ? 'selected' : '' }}>Low Priority</option>
                    </select>
                </div>
            </div>

            <div class="section-action-footer">
                <button type="submit" class="btn-gradient">
                    <i data-lucide="save" style="width:15px;height:15px;"></i> Save General Settings
                </button>
            </div>
        </form>
    </div>


    {{-- ══════════════════════════════════════════════════════════════════════
         SECTION 2: SMTP & Mail Server Configuration
    ══════════════════════════════════════════════════════════════════════ --}}
    <div class="settings-section-card" id="section-smtp">
        <div class="settings-section-header">
            <div class="settings-section-header-left">
                <div class="settings-section-icon smtp">
                    <i data-lucide="mail" style="width:20px;height:20px;"></i>
                </div>
                <div>
                    <h3>SMTP & Mail Server Configuration</h3>
                    <p>Set up your email delivery credentials to send automated alerts and reminders</p>
                </div>
            </div>
            <span class="badge d-none d-sm-inline-block" style="background:rgba(16,185,129,0.12);color:var(--success);font-size:0.75rem;padding:0.4rem 0.75rem;border-radius:20px;">
                Email Delivery
            </span>
        </div>

        <form method="POST" action="{{ route('settings.update') }}" id="smtpForm">
            @csrf
            <input type="hidden" name="_group" value="smtp">

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label-settings">Mail Driver / Mailer <span class="text-danger">*</span></label>
                    @php $mailer = $allSettings['mail_mailer'] ?? 'smtp'; @endphp
                    <select name="mail_mailer" id="mail_mailer" class="form-select form-select-dark" required>
                        <option value="smtp" {{ $mailer === 'smtp' ? 'selected' : '' }}>SMTP (Recommended)</option>
                        <option value="sendmail" {{ $mailer === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                        <option value="log" {{ $mailer === 'log' ? 'selected' : '' }}>Log (Testing Only)</option>
                    </select>
                </div>

                <div class="col-md-5">
                    <label class="form-label-settings">SMTP Host <span class="text-danger">*</span></label>
                    <input type="text" name="mail_host" id="mail_host" class="form-control form-control-dark" value="{{ $allSettings['mail_host'] ?? 'smtp.gmail.com' }}" placeholder="e.g. smtp.gmail.com or mail.domain.com" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label-settings">SMTP Port <span class="text-danger">*</span></label>
                    <input type="number" name="mail_port" id="mail_port" class="form-control form-control-dark" value="{{ $allSettings['mail_port'] ?? '587' }}" placeholder="587 / 465 / 25" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label-settings">SMTP Username / Email</label>
                    <input type="text" name="mail_username" id="mail_username" class="form-control form-control-dark" value="{{ $allSettings['mail_username'] ?? '' }}" placeholder="your-email@gmail.com">
                    <div class="form-hint">For Gmail, use your full Gmail address.</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label-settings">
                        <span>SMTP Password / App Password</span>
                        <a href="javascript:void(0)" onclick="togglePasswordVisibility('mail_password', this)" style="font-size:0.74rem;color:var(--accent);text-decoration:none;font-weight:600;">Show</a>
                    </label>
                    <div class="position-relative">
                        <input type="password" name="mail_password" id="mail_password" class="form-control form-control-dark" value="{{ $allSettings['mail_password'] ?? '' }}" placeholder="Enter SMTP password or App Password">
                    </div>
                    <div class="form-hint">For Gmail, generate a 16-character <strong>App Password</strong> in Google Account Security.</div>
                </div>

                <div class="col-md-4">
                    <label class="form-label-settings">Encryption Type</label>
                    @php $enc = $allSettings['mail_encryption'] ?? 'tls'; @endphp
                    <select name="mail_encryption" id="mail_encryption" class="form-select form-select-dark">
                        <option value="tls" {{ $enc === 'tls' ? 'selected' : '' }}>TLS (Port 587 - Standard)</option>
                        <option value="ssl" {{ $enc === 'ssl' ? 'selected' : '' }}>SSL (Port 465)</option>
                        <option value="null" {{ $enc === 'null' || empty($enc) ? 'selected' : '' }}>None (Port 25)</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label-settings">From Sender Email <span class="text-danger">*</span></label>
                    <input type="email" name="mail_from_address" id="mail_from_address" class="form-control form-control-dark" value="{{ $allSettings['mail_from_address'] ?? 'noreply@workflowpro.com' }}" placeholder="noreply@workflowpro.com" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label-settings">From Sender Name <span class="text-danger">*</span></label>
                    <input type="text" name="mail_from_name" id="mail_from_name" class="form-control form-control-dark" value="{{ $allSettings['mail_from_name'] ?? 'WorkFlow Pro' }}" placeholder="WorkFlow Pro" required>
                </div>
            </div>

            {{-- Provider Reference Cards --}}
            <div class="mt-4">
                <div class="d-flex align-items-center gap-2 mb-2" style="font-size:0.75rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.06em;">
                    <i data-lucide="info" style="width:13px;height:13px;"></i> Common Mail Provider Guides:
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="provider-guide-card">
                            <strong>Gmail / Google Workspace:</strong>
                            Host: <code>smtp.gmail.com</code><br>
                            Port: <code>587</code> (TLS) or <code>465</code> (SSL)<br>
                            Password: <em>16-digit App Password</em>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="provider-guide-card">
                            <strong>cPanel / Custom Domain:</strong>
                            Host: <code>mail.yourdomain.com</code><br>
                            Port: <code>465</code> (SSL) or <code>587</code> (TLS)<br>
                            User: <em>Full email address</em>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="provider-guide-card">
                            <strong>SendGrid / Mailgun / Mailtrap:</strong>
                            Host: <code>smtp.sendgrid.net</code><br>
                            Port: <code>587</code> (TLS)<br>
                            User: <code>apikey</code>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section-action-footer">
                <button type="submit" class="btn-gradient">
                    <i data-lucide="save" style="width:15px;height:15px;"></i> Save SMTP Settings
                </button>
            </div>
        </form>
    </div>


    {{-- ══════════════════════════════════════════════════════════════════════
         SECTION 3: Live SMTP Connection Tester
    ══════════════════════════════════════════════════════════════════════ --}}
    <div class="settings-section-card" id="section-smtp-test">
        <div class="settings-section-header">
            <div class="settings-section-header-left">
                <div class="settings-section-icon test">
                    <i data-lucide="send" style="width:20px;height:20px;"></i>
                </div>
                <div>
                    <h3>Live SMTP Connection Tester</h3>
                    <p>Verify your SMTP server credentials and connectivity by sending an instant live test email</p>
                </div>
            </div>
            <span class="badge d-none d-sm-inline-block" style="background:rgba(14,165,233,0.12);color:var(--info);font-size:0.75rem;padding:0.4rem 0.75rem;border-radius:20px;">
                Connection Check
            </span>
        </div>

        <div class="smtp-test-box">
            <p style="font-size:0.83rem;color:var(--text-muted);margin-bottom:1.15rem;line-height:1.4;">
                Test the configuration before sending notifications to your team. Enter an email address below and click <strong>Send Test Email</strong>.
            </p>

            <form id="testEmailForm" onsubmit="handleSendTestEmail(event)">
                @csrf
                <div class="row g-2 align-items-center">
                    <div class="col-md-8">
                        <input type="email" id="testEmailInput" name="test_email" class="form-control form-control-dark" placeholder="Enter recipient email address (e.g. your email)" value="{{ auth()->user()->email }}" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" id="btnSendTestEmail" class="btn-gradient w-100 justify-content-center">
                            <i data-lucide="send" style="width:14px;height:14px;"></i> Send Test Email
                        </button>
                    </div>
                </div>
            </form>

            {{-- Live Test Status Feedback Area --}}
            <div id="testResultBox" class="mt-3 d-none"></div>
        </div>
    </div>


    {{-- ══════════════════════════════════════════════════════════════════════
         SECTION 4: Notification Preferences
    ══════════════════════════════════════════════════════════════════════ --}}
    <div class="settings-section-card" id="section-notifications">
        <div class="settings-section-header">
            <div class="settings-section-header-left">
                <div class="settings-section-icon notification">
                    <i data-lucide="bell" style="width:20px;height:20px;"></i>
                </div>
                <div>
                    <h3>Automated Notification Preferences</h3>
                    <p>Select which system events trigger automated email delivery to team members and coordinators</p>
                </div>
            </div>
            <span class="badge d-none d-sm-inline-block" style="background:rgba(245,158,11,0.12);color:var(--warning);font-size:0.75rem;padding:0.4rem 0.75rem;border-radius:20px;">
                Email Triggers
            </span>
        </div>

        <form method="POST" action="{{ route('settings.update') }}">
            @csrf
            <input type="hidden" name="_group" value="notification">

            <div class="switch-setting-item">
                <div>
                    <div class="switch-setting-label">Task Assignment Notification</div>
                    <div class="switch-setting-desc">Send instant email notice to team members when a task is assigned or reassigned to them.</div>
                </div>
                <div class="form-check form-switch fs-5 mb-0">
                    <input class="form-check-input" type="checkbox" name="notify_task_assigned" value="1" {{ ($allSettings['notify_task_assigned'] ?? '1') === '1' ? 'checked' : '' }} style="cursor:pointer;">
                </div>
            </div>

            <div class="switch-setting-item">
                <div>
                    <div class="switch-setting-label">Task Status Change Notification</div>
                    <div class="switch-setting-desc">Send email alerts to the task creator and assignee when a task status moves to In Progress, Under Review, or Completed.</div>
                </div>
                <div class="form-check form-switch fs-5 mb-0">
                    <input class="form-check-input" type="checkbox" name="notify_task_status_changed" value="1" {{ ($allSettings['notify_task_status_changed'] ?? '1') === '1' ? 'checked' : '' }} style="cursor:pointer;">
                </div>
            </div>

            <div class="switch-setting-item">
                <div>
                    <div class="switch-setting-label">Overdue Task Alerts</div>
                    <div class="switch-setting-desc">Send automated warning emails when active tasks pass their scheduled due date.</div>
                </div>
                <div class="form-check form-switch fs-5 mb-0">
                    <input class="form-check-input" type="checkbox" name="notify_task_overdue" value="1" {{ ($allSettings['notify_task_overdue'] ?? '1') === '1' ? 'checked' : '' }} style="cursor:pointer;">
                </div>
            </div>

            <div class="switch-setting-item">
                <div>
                    <div class="switch-setting-label">Scheduled Reminder Delivery</div>
                    <div class="switch-setting-desc">Deliver automated reminder notifications via email to team members at the scheduled date and time.</div>
                </div>
                <div class="form-check form-switch fs-5 mb-0">
                    <input class="form-check-input" type="checkbox" name="notify_reminder_due" value="1" {{ ($allSettings['notify_reminder_due'] ?? '1') === '1' ? 'checked' : '' }} style="cursor:pointer;">
                </div>
            </div>

            <div class="section-action-footer">
                <button type="submit" class="btn-gradient">
                    <i data-lucide="save" style="width:15px;height:15px;"></i> Save Notification Preferences
                </button>
            </div>
        </form>
    </div>

</div>

@endsection

@push('scripts')
<script>
// ── Live Image Preview for Logo & Favicon ──
function previewBrandImage(input, imgId, placeholderId) {
    const file = input.files && input.files[0];
    if (!file) return;

    const imgEl = document.getElementById(imgId);
    const placeholderEl = document.getElementById(placeholderId);

    const reader = new FileReader();
    reader.onload = function(e) {
        if (imgEl) {
            imgEl.src = e.target.result;
            imgEl.classList.remove('d-none');
        }
        if (placeholderEl) {
            placeholderEl.classList.add('d-none');
        }
    };
    reader.readAsDataURL(file);
}

// ── Password Visibility Toggle ──
function togglePasswordVisibility(inputId, toggleLink) {
    const input = document.getElementById(inputId);
    if (!input) return;

    if (input.type === 'password') {
        input.type = 'text';
        toggleLink.textContent = 'Hide';
    } else {
        input.type = 'password';
        toggleLink.textContent = 'Show';
    }
}

// ── Smooth Scroll to Anchor if URL contains hash ──
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.hash) {
        const targetId = window.location.hash.substring(1);
        const targetEl = document.getElementById(targetId);
        if (targetEl) {
            setTimeout(() => {
                targetEl.scrollIntoView({ behavior: 'smooth' });
            }, 100);
        }
    }
});

// ── AJAX Test Email Delivery ──
function handleSendTestEmail(e) {
    e.preventDefault();

    const recipient = document.getElementById('testEmailInput').value;
    const btn = document.getElementById('btnSendTestEmail');
    const resultBox = document.getElementById('testResultBox');

    if (!recipient) {
        alert('Please enter a recipient email address.');
        return;
    }

    // Collect current values from SMTP form
    const formData = {
        test_email: recipient,
        mail_mailer: document.getElementById('mail_mailer')?.value,
        mail_host: document.getElementById('mail_host')?.value,
        mail_port: document.getElementById('mail_port')?.value,
        mail_username: document.getElementById('mail_username')?.value,
        mail_password: document.getElementById('mail_password')?.value,
        mail_encryption: document.getElementById('mail_encryption')?.value,
        mail_from_address: document.getElementById('mail_from_address')?.value,
        mail_from_name: document.getElementById('mail_from_name')?.value,
    };

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Sending...';
    resultBox.classList.add('d-none');

    fetch('{{ route("settings.test-email") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(async response => {
        const data = await response.json();
        resultBox.classList.remove('d-none');

        if (response.ok && data.success) {
            resultBox.className = 'alert alert-success-custom p-3 mt-3 fade-in';
            resultBox.innerHTML = `
                <div class="d-flex align-items-center gap-2">
                    <i data-lucide="check-circle" style="width:20px;height:20px;color:var(--success);"></i>
                    <div>
                        <strong>Connection Successful!</strong><br>
                        ${data.message}
                    </div>
                </div>
            `;
        } else {
            resultBox.className = 'alert alert-danger-custom p-3 mt-3 fade-in';
            resultBox.innerHTML = `
                <div class="d-flex align-items-start gap-2">
                    <i data-lucide="alert-triangle" style="width:20px;height:20px;color:var(--danger);flex-shrink:0;margin-top:2px;"></i>
                    <div>
                        <strong>SMTP Connection Failed:</strong><br>
                        <span style="font-size:0.8rem;word-break:break-word;">${data.message || 'Unknown error occurred while connecting to mail server.'}</span>
                    </div>
                </div>
            `;
        }
        if (window.lucide) lucide.createIcons();
    })
    .catch(err => {
        resultBox.classList.remove('d-none');
        resultBox.className = 'alert alert-danger-custom p-3 mt-3 fade-in';
        resultBox.innerHTML = `
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="alert-triangle" style="width:20px;height:20px;color:var(--danger);"></i>
                <div>
                    <strong>Network Error:</strong><br>
                    ${err.message || 'Could not reach the server.'}
                </div>
            </div>
        `;
        if (window.lucide) lucide.createIcons();
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="send" style="width:14px;height:14px;"></i> Send Test Email';
        if (window.lucide) lucide.createIcons();
    });
}
</script>
@endpush
