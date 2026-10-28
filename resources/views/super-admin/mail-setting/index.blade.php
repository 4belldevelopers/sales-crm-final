@extends('layouts.superadmin_layout')

@push('head-script')
<style>
/* ==========================================================================
   SUPER ADMIN SMTP MULTI-GATEWAY HUB - 15.6" COMPACT ENTERPRISE STYLES
   Adheres to docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md & docs/UI_UX_STANDARDS.md
   ========================================================================== */

body {
    overflow-x: hidden !important;
}

/* ── Top Gap Collapse: eliminate excessive toolbar & wrapper spacing on standard 15.6" viewports ── */
#kt_header_nav .page-title,
#kt_header_nav .smtp-header-title {
    display: none !important;
}

#kt_toolbar {
    display: none !important;
    height: 0 !important;
    min-height: 0 !important;
    padding: 0 !important;
    margin: 0 !important;
}

.header-fixed.toolbar-fixed #kt_wrapper,
.header-fixed #kt_wrapper,
.toolbar-fixed #kt_wrapper,
#kt_wrapper {
    padding-top: calc(65px + 10px) !important;
}

@media (max-width: 991.98px) {
    .header-fixed.toolbar-fixed #kt_wrapper,
    .header-fixed #kt_wrapper,
    .toolbar-fixed #kt_wrapper,
    #kt_wrapper {
        padding-top: calc(55px + 6px) !important;
    }
}

#kt_content,
.content {
    padding-top: 0 !important;
    margin-top: 0 !important;
}

.smtp-page-container {
    padding: 0 16px 30px 16px !important;
    margin-top: 0 !important;
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
}

/* Tier 1: Header Toolbar Card */
.smtp-toolbar-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 12px 18px !important;
    margin-bottom: 12px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}

.smtp-brand-area {
    display: flex;
    align-items: center;
    gap: 12px;
}

.smtp-icon-box {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    background: #eff6ff;
    color: #009ef7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}

.smtp-title-group h2 {
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.25;
    margin: 0;
}

.smtp-title-group p {
    font-size: 11.5px;
    color: #64748b;
    margin: 2px 0 0 0;
}

.smtp-kpi-badges {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.smtp-kpi-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    line-height: 1;
}

.smtp-kpi-primary {
    background: #eff6ff;
    color: #009ef7;
    border: 1px solid #bfdbfe;
}

.smtp-kpi-success {
    background: #ecfdf5;
    color: #10b981;
    border: 1px solid #a7f3d0;
}

.smtp-kpi-neutral {
    background: #f8fafc;
    color: #475569;
    border: 1px solid #e2e8f0;
}

.smtp-action-area {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

/* Dynamic Debounced Search Sub-Card */
.smtp-search-subcard {
    position: relative;
    width: 250px;
    max-width: 100%;
}

.smtp-search-subcard input {
    height: 36px;
    background: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    font-size: 12px !important;
    padding-left: 32px !important;
    padding-right: 28px !important;
    color: #1e293b !important;
    transition: all 0.2s ease;
}

.smtp-search-subcard input:focus {
    background: #ffffff !important;
    border-color: #009ef7 !important;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
}

.smtp-search-icon {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 12px;
    color: #94a3b8;
    pointer-events: none;
}

.smtp-search-clear {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    color: #94a3b8;
    font-size: 12px;
    cursor: pointer;
    display: none;
    padding: 2px 4px;
}

.smtp-search-clear:hover {
    color: #475569;
}

/* Tier 2: Sub-Tabs Card */
.smtp-tabs-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 8px 12px !important;
    margin-bottom: 14px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
}

.smtp-tabs-nav {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
    margin: 0;
    padding: 0;
    list-style: none;
}

.smtp-tab-btn {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    padding: 6px 14px !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    color: #475569 !important;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
}

.smtp-tab-btn:hover {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
    color: #0f172a !important;
}

.smtp-tab-btn.active {
    background: #009ef7 !important;
    border-color: #009ef7 !important;
    color: #ffffff !important;
    box-shadow: 0 2px 4px rgba(0, 158, 247, 0.25) !important;
}

.smtp-tab-badge {
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #f1f5f9;
    color: #475569;
}

.smtp-tab-btn.active .smtp-tab-badge {
    background: rgba(255, 255, 255, 0.25) !important;
    color: #ffffff !important;
}

/* Tier 3: Primary Gateway Cards */
.smtp-gateway-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    padding: 16px 18px !important;
    margin-bottom: 14px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}

.smtp-gateway-card:hover {
    border-color: #cbd5e1 !important;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06) !important;
}

.smtp-gateway-card.is-inactive {
    opacity: 0.78;
    background: #fcfcfd !important;
}

/* Sub-Card A: Identity & Status Strip */
.smtp-subcard-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    padding-bottom: 12px;
    border-bottom: 1px solid #f1f5f9;
    margin-bottom: 14px;
}

.smtp-subcard-identity {
    display: flex;
    align-items: center;
    gap: 10px;
}

.smtp-gateway-num-pill {
    padding: 3px 8px;
    background: #eff6ff;
    color: #009ef7;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.5px;
    border: 1px solid #bfdbfe;
}

.smtp-subcard-title-group h3 {
    font-size: 14px;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
    line-height: 1.2;
}

.smtp-subcard-title-group span {
    font-size: 11px;
    color: #64748b;
}

.smtp-subcard-controls {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.smtp-daily-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    color: #334155;
}

.smtp-daily-pill input {
    width: 60px;
    height: 24px;
    padding: 0 4px;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-align: center;
    background: #ffffff;
}

/* Sub-Card B & C: Form Fields & Inputs */
.smtp-field-label {
    font-size: 11.5px !important;
    font-weight: 600 !important;
    color: #334155 !important;
    margin-bottom: 4px !important;
    display: block;
}

.smtp-field-label .required-dot {
    color: #ef4444;
}

.smtp-input-solid {
    height: 36px !important;
    background-color: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 7px !important;
    font-size: 12px !important;
    color: #1e293b !important;
    font-weight: 500 !important;
    padding: 0 10px !important;
    transition: all 0.2s ease;
}

.smtp-input-solid:focus {
    background-color: #ffffff !important;
    border-color: #009ef7 !important;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
}

.smtp-input-group {
    position: relative;
    display: flex;
    width: 100%;
}

.smtp-input-group .smtp-input-solid {
    border-top-right-radius: 0 !important;
    border-bottom-right-radius: 0 !important;
}

.smtp-input-group-btn {
    height: 36px !important;
    border: 1px solid #e2e8f0 !important;
    border-left: none !important;
    background: #f8fafc !important;
    border-top-right-radius: 7px !important;
    border-bottom-right-radius: 7px !important;
    padding: 0 10px !important;
    color: #64748b !important;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.smtp-input-group-btn:hover {
    background: #f1f5f9 !important;
    color: #0f172a !important;
}

/* Sub-Card D: Diagnostic Action Strip (Footer) */
.smtp-subcard-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    padding-top: 10px;
    margin-top: 14px;
    border-top: 1px solid #f1f5f9;
}

.smtp-service-tag {
    font-size: 11px;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.smtp-service-tag code {
    background: #f1f5f9;
    padding: 2px 6px;
    border-radius: 4px;
    color: #0f172a;
    font-size: 10.5px;
}

/* Diagnostic Test Email Modal */
.smtp-modal-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 14px 20px;
}

.smtp-modal-body {
    padding: 20px;
}

.smtp-diag-box {
    background: #0f172a;
    color: #38bdf8;
    border-radius: 8px;
    padding: 12px 14px;
    font-family: monospace;
    font-size: 11.5px;
    line-height: 1.5;
    margin-top: 14px;
    display: none;
    max-height: 180px;
    overflow-y: auto;
}

.smtp-diag-success {
    color: #4ade80;
}

.smtp-diag-error {
    color: #f87171;
}

/* Provider Preset Dropdown styling */
.smtp-preset-dropdown .dropdown-item {
    font-size: 11.5px;
    padding: 6px 12px;
}

.smtp-preset-dropdown .dropdown-item:hover {
    background: #eff6ff;
    color: #009ef7;
}

/* Metronic Form Switch Customization */
.form-check.form-switch .form-check-input {
    width: 38px;
    height: 20px;
    cursor: pointer;
}

.form-check.form-switch .form-check-input:checked {
    background-color: #10b981;
    border-color: #10b981;
}

@media (max-width: 991.98px) {
    .smtp-toolbar-card {
        flex-direction: column;
        align-items: flex-start;
    }
    .smtp-action-area {
        width: 100%;
        justify-content: flex-start;
    }
    .smtp-search-subcard {
        width: 100%;
    }
}
</style>
@endpush

@section('content')
<div class="smtp-page-container">

    <!-- =========================================================================
         TIER 1: TOP HEADER TOOLBAR CARD (Pure White #ffffff, Border #e2e8f0, Radius 10px)
         ========================================================================= -->
    <div class="smtp-toolbar-card">
        <!-- Left Branding & KPI Area -->
        <div class="smtp-brand-area">
            <div class="smtp-icon-box">
                <i class="fa-solid fa-server"></i>
            </div>
            <div class="smtp-title-group">
                <h2>SMTP Delivery & Multi-Gateway Hub</h2>
                <p>Global transactional socket configurations and dedicated routing channels</p>
            </div>
            <div class="smtp-kpi-badges ms-md-3">
                <span class="smtp-kpi-badge smtp-kpi-primary">
                    <i class="fa-solid fa-network-wired fs-9"></i>
                    <span id="kpi-total-gateways">{{ $kpiStats['total_gateways'] ?? count($smtpSetting) }}</span> Gateways
                </span>
                <span class="smtp-kpi-badge smtp-kpi-success">
                    <i class="fa-solid fa-circle-check fs-9"></i>
                    <span id="kpi-active-gateways">{{ $kpiStats['active_gateways'] ?? $smtpSetting->where('status', 0)->count() }}</span> Active
                </span>
                <span class="smtp-kpi-badge smtp-kpi-neutral">
                    <i class="fa-solid fa-paper-plane fs-9"></i>
                    <span id="kpi-daily-sent">{{ number_format($kpiStats['total_daily_sent'] ?? $smtpSetting->sum('dailyCount')) }}</span> Sent Today
                </span>
            </div>
        </div>

        <!-- Right Action & Search Area -->
        <div class="smtp-action-area">
            <!-- Dynamic Debounced Search Sub-Card -->
            <div class="smtp-search-subcard">
                <i class="fas fa-search smtp-search-icon"></i>
                <input type="text" id="smtp-search-input" placeholder="Search gateways, host, service..." autocomplete="off">
                <button type="button" id="smtp-search-clear" class="smtp-search-clear" title="Clear Search">✕</button>
            </div>

            <!-- Action Buttons -->
            <button type="button" class="btn btn-sm btn-light-primary fw-bold" id="btn-open-test-modal">
                <i class="fa-solid fa-vial-circle-check me-1"></i> Send Test Email
            </button>
            <button type="button" class="btn btn-sm btn-primary fw-bold" id="btn-save-smtp-settings">
                <span class="indicator-label"><i class="fa-solid fa-floppy-disk me-1"></i> Save All Settings</span>
                <span class="indicator-progress">Saving... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
            </button>
        </div>
    </div>

    <!-- =========================================================================
         TIER 2: GATEWAY SELECTOR SUB-TABS CARD (Pure White #ffffff, Border #e2e8f0)
         ========================================================================= -->
    <div class="smtp-tabs-card">
        <ul class="smtp-tabs-nav">
            <li>
                <button type="button" class="smtp-tab-btn active" data-filter="all">
                    <span>All Gateways</span>
                    <span class="smtp-tab-badge">{{ count($smtpSetting) }}</span>
                </button>
            </li>
            @foreach($smtpSetting as $gateway)
                @php
                    $channelShortName = match((int) $gateway->id) {
                        8 => 'Mass Mail',
                        9 => 'Candidate & Resume',
                        10 => 'Interviews',
                        11 => 'Notifications',
                        12 => 'Accounts & Offers',
                        default => 'Gateway #'.$gateway->id,
                    };
                @endphp
                <li>
                    <button type="button" class="smtp-tab-btn" data-filter="gateway-{{ $gateway->id }}">
                        <span>{{ sprintf('%02d. %s', (int)$gateway->id - 7, $channelShortName) }}</span>
                        <span class="smtp-tab-badge">#{{ $gateway->id }}</span>
                    </button>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- =========================================================================
         TIER 3: PRIMARY GATEWAY CARDS STREAM
         ========================================================================= -->
    <form id="smtpSettingsForm" class="ajax-form">
        @csrf
        @method('PUT')
        <input type="hidden" name="mail_driver" value="smtp">

        <div id="smtp-gateways-container">
            @foreach($smtpSetting as $val)
                @php
                    $gid = (int) $val->id;
                    $isActive = ((int) $val->status === 0);
                    $serviceFile = match($gid) {
                        8 => 'app/Services/MassMailService.php',
                        9 => 'app/Services/CandidatesMailAndResumeForward.php',
                        10 => 'app/Services/InterviewRemainderMail.php',
                        11 => 'app/Services/NotificationMailService.php',
                        12 => 'app/Services/MailService.php',
                        default => 'Global Mail Dispatch',
                    };
                    $purposeDesc = match($gid) {
                        8 => 'Mass Outreach, Campaign Requisitions & Talent Broadcasts',
                        9 => 'Recruiter Direct Emails, Forward Resume PDF Packets & Client Submissions',
                        10 => 'Automated Calendar Invites (.ics) & 1-Hour/15-Minute Interview Reminders',
                        11 => 'Platform Security Alerts, Registration Verification & Team Invitations',
                        12 => 'Formal Offer Letter Releases, Contracts, Invoices & Financial Statements',
                        default => $val->mailtype ?: 'System Transactional Gateway',
                    };
                @endphp

                <div class="smtp-gateway-card {{ !$isActive ? 'is-inactive' : '' }}" 
                     id="gateway-card-{{ $gid }}"
                     data-gateway-id="{{ $gid }}"
                     data-search-text="{{ strtolower($val->gateway_title . ' ' . $val->mail_host . ' ' . $val->mail_from_email . ' ' . $serviceFile . ' ' . $purposeDesc) }}">

                    <!-- Sub-Card A: Header Identity & Status Strip -->
                    <div class="smtp-subcard-header">
                        <div class="smtp-subcard-identity">
                            <span class="smtp-gateway-num-pill">{{ $val->gateway_badge }}</span>
                            <div class="smtp-subcard-title-group">
                                <h3>{{ $val->gateway_title }}</h3>
                                <span>{{ $purposeDesc }}</span>
                            </div>
                        </div>

                        <div class="smtp-subcard-controls">
                            <!-- Daily Quota / Count Tracker -->
                            <div class="smtp-daily-pill" title="Emails dispatched today across this socket">
                                <i class="fa-solid fa-chart-simple text-primary"></i>
                                <span>Daily Sent:</span>
                                <input type="number" 
                                       min="0" 
                                       name="daily_used[{{ $gid }}]" 
                                       id="daily_used_{{ $gid }}" 
                                       value="{{ (int) $val->dailyCount }}"
                                       title="Edit or reset daily count">
                            </div>

                            <!-- Provider Quick-Preset Dropdown -->
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light fw-semibold dropdown-toggle py-1 px-2 fs-8" 
                                        type="button" 
                                        data-bs-toggle="dropdown" 
                                        aria-expanded="false">
                                    <i class="fa-solid fa-wand-magic-sparkles text-warning me-1"></i> Quick Presets
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end smtp-preset-dropdown shadow-sm border-gray-200">
                                    <li><h6 class="dropdown-header text-uppercase fs-9 fw-bolder text-muted">Autofill Socket Preset</h6></li>
                                    <li><a class="dropdown-item preset-option" href="javascript:void(0);" data-gid="{{ $gid }}" data-preset="aws_ses_ssl"><i class="fa-brands fa-aws me-2 text-warning"></i> AWS SES (Port 465 SSL)</a></li>
                                    <li><a class="dropdown-item preset-option" href="javascript:void(0);" data-gid="{{ $gid }}" data-preset="aws_ses_tls"><i class="fa-brands fa-aws me-2 text-warning"></i> AWS SES (Port 587 TLS)</a></li>
                                    <li><a class="dropdown-item preset-option" href="javascript:void(0);" data-gid="{{ $gid }}" data-preset="gmail_ssl"><i class="fa-brands fa-google me-2 text-danger"></i> Google Workspace / Gmail (465 SSL)</a></li>
                                    <li><a class="dropdown-item preset-option" href="javascript:void(0);" data-gid="{{ $gid }}" data-preset="sendgrid_tls"><i class="fa-solid fa-paper-plane me-2 text-primary"></i> SendGrid (Port 587 TLS)</a></li>
                                    <li><a class="dropdown-item preset-option" href="javascript:void(0);" data-gid="{{ $gid }}" data-preset="brevo_tls"><i class="fa-solid fa-bolt me-2 text-info"></i> Brevo / Sendinblue (Port 587 TLS)</a></li>
                                </ul>
                            </div>

                            <!-- Active / Inactive Status Switch -->
                            <div class="form-check form-switch form-check-custom form-check-solid" title="Toggle active status">
                                <input type="hidden" name="status[{{ $gid }}]" id="status_val_{{ $gid }}" value="{{ $val->status }}">
                                <input class="form-check-input gateway-status-toggle" 
                                       type="checkbox" 
                                       role="switch" 
                                       id="status_switch_{{ $gid }}" 
                                       data-gid="{{ $gid }}"
                                       {{ $isActive ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold fs-8 ms-1 status-text-label" 
                                       id="status_text_{{ $gid }}" 
                                       for="status_switch_{{ $gid }}"
                                       style="color: {{ $isActive ? '#10b981' : '#94a3b8' }}">
                                    {{ $isActive ? 'Active' : 'Inactive' }}
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Sub-Card B: Connection & Protocol (Row 1) -->
                    <div class="row g-2 g-md-3 mb-3">
                        <div class="col-12 col-md-3">
                            <label class="smtp-field-label">Mail Driver</label>
                            <input type="text" 
                                   class="form-control smtp-input-solid" 
                                   value="SMTP (Default)" 
                                   readonly 
                                   style="background: #f1f5f9 !important; color: #64748b !important;">
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="smtp-field-label" for="mail_host_{{ $gid }}">
                                SMTP Host <span class="required-dot">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control smtp-input-solid" 
                                   id="mail_host_{{ $gid }}" 
                                   name="mail_host[{{ $gid }}]" 
                                   value="{{ $val->mail_host }}" 
                                   placeholder="e.g. smtp.gmail.com" 
                                   required>
                        </div>

                        <div class="col-6 col-md-2">
                            <label class="smtp-field-label" for="mail_port_{{ $gid }}">
                                Port <span class="required-dot">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control smtp-input-solid" 
                                   id="mail_port_{{ $gid }}" 
                                   name="mail_port[{{ $gid }}]" 
                                   value="{{ $val->mail_port ?: 465 }}" 
                                   min="1" 
                                   max="65535" 
                                   placeholder="465 / 587" 
                                   required>
                        </div>

                        <div class="col-6 col-md-3">
                            <label class="smtp-field-label" for="mail_encryption_{{ $gid }}">
                                Encryption Protocol
                            </label>
                            <select class="form-select smtp-input-solid" 
                                    name="mail_encryption[{{ $gid }}]" 
                                    id="mail_encryption_{{ $gid }}">
                                <option value="ssl" @if(strtolower($val->mail_encryption ?? '') === 'ssl') selected @endif>SSL (Recommended for 465)</option>
                                <option value="tls" @if(strtolower($val->mail_encryption ?? '') === 'tls') selected @endif>TLS / STARTTLS (Port 587)</option>
                                <option value="none" @if(empty($val->mail_encryption) || $val->mail_encryption === 'none') selected @endif>None (Unencrypted)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Sub-Card C: Authentication & Sender Identity (Row 2) -->
                    <div class="row g-2 g-md-3">
                        <div class="col-12 col-md-3">
                            <label class="smtp-field-label" for="mail_username_{{ $gid }}">
                                SMTP Username <span class="required-dot">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control smtp-input-solid" 
                                   id="mail_username_{{ $gid }}" 
                                   name="mail_username[{{ $gid }}]" 
                                   value="{{ $val->mail_username }}" 
                                   placeholder="Account / API Key ID" 
                                   required>
                        </div>

                        <div class="col-12 col-md-3">
                            <label class="smtp-field-label" for="mail_password_{{ $gid }}">
                                SMTP Password <span class="required-dot">*</span>
                            </label>
                            <div class="smtp-input-group">
                                <input type="password" 
                                       class="form-control smtp-input-solid smtp-password-field" 
                                       id="mail_password_{{ $gid }}" 
                                       name="mail_password[{{ $gid }}]" 
                                       value="{{ $val->mail_password }}" 
                                       placeholder="Password / Secret Key" 
                                       required>
                                <button type="button" 
                                        class="smtp-input-group-btn btn-toggle-password" 
                                        data-target="mail_password_{{ $gid }}" 
                                        title="Show / Hide Password">
                                    <i class="fa-solid fa-eye fs-8"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-12 col-md-3">
                            <label class="smtp-field-label" for="from_mail_{{ $gid }}">
                                From Email (Envelope) <span class="required-dot">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control smtp-input-solid" 
                                   id="from_mail_{{ $gid }}" 
                                   name="from_mail[{{ $gid }}]" 
                                   value="{{ $val->mail_from_email ?: $val->email_id }}" 
                                   placeholder="noreply@domain.com" 
                                   required>
                        </div>

                        <div class="col-12 col-md-3">
                            <label class="smtp-field-label" for="mail_from_name_{{ $gid }}">
                                Sender Display Name
                            </label>
                            <input type="text" 
                                   class="form-control smtp-input-solid" 
                                   id="mail_from_name_{{ $gid }}" 
                                   name="mail_from_name[{{ $gid }}]" 
                                   value="{{ $val->mail_from_name ?: 'JERO.WORKS' }}" 
                                   placeholder="e.g. JERO Mass Mailer">
                        </div>
                    </div>

                    <!-- Sub-Card D: Diagnostic Action Strip (Footer) -->
                    <div class="smtp-subcard-footer">
                        <div class="smtp-service-tag">
                            <i class="fa-solid fa-code text-muted"></i>
                            <span>Consuming Service: <code>{{ $serviceFile }}</code></span>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <button type="button" 
                                    class="btn btn-sm btn-light-primary fw-semibold py-1 px-3 fs-8 btn-test-this-gateway" 
                                    data-gid="{{ $gid }}"
                                    data-title="{{ $val->gateway_title }}">
                                <i class="fa-solid fa-bolt me-1"></i> Test This Socket
                            </button>
                            <button type="button" 
                                    class="btn btn-sm btn-light fw-semibold py-1 px-3 fs-8 btn-copy-creds"
                                    data-host="mail_host_{{ $gid }}"
                                    data-port="mail_port_{{ $gid }}"
                                    data-user="mail_username_{{ $gid }}"
                                    title="Copy socket parameters to clipboard">
                                <i class="fa-regular fa-copy me-1"></i> Copy
                            </button>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

        <!-- Sticky Floating Bottom Save Bar for Long Scrolling Ergonomics -->
        <div class="d-flex justify-content-end align-items-center mt-3 pt-3 border-top border-gray-200">
            <span class="text-muted fs-8 me-3">All 5 gateways are saved in a single transactional batch</span>
            <button type="button" class="btn btn-primary fw-bold px-4" id="btn-save-smtp-settings-bottom">
                <span class="indicator-label"><i class="fa-solid fa-floppy-disk me-1"></i> Save All Settings</span>
                <span class="indicator-progress">Saving... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
            </button>
        </div>
    </form>

</div>

<!-- =========================================================================
     LIVE DIAGNOSTIC TEST EMAIL MODAL
     ========================================================================= -->
<div class="modal fade" id="smtpTestEmailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header smtp-modal-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <div class="smtp-icon-box" style="width: 32px; height: 32px; font-size: 13px;">
                        <i class="fa-solid fa-paper-plane"></i>
                    </div>
                    <div>
                        <h4 class="modal-title fw-bold text-gray-900 fs-5 mb-0">Dispatch Live SMTP Diagnostic Test</h4>
                        <span class="text-muted fs-8">Performs genuine socket handshake, latency measurement, and test email delivery</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body smtp-modal-body">
                <form id="testEmailForm">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="smtp-field-label" for="modal_gateway_select">
                                Select Gateway Socket <span class="required-dot">*</span>
                            </label>
                            <select id="modal_gateway_select" class="form-select smtp-input-solid">
                                @foreach($smtpSetting as $gw)
                                    <option value="{{ $gw->id }}" data-host="{{ $gw->mail_host }}">
                                        {{ $gw->gateway_badge }} — {{ $gw->gateway_title }} ({{ $gw->mail_host }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="smtp-field-label" for="modal_test_email">
                                Target Recipient Email <span class="required-dot">*</span>
                            </label>
                            <input type="email" 
                                   id="modal_test_email" 
                                   class="form-control smtp-input-solid" 
                                   placeholder="e.g. admin@yourdomain.com"
                                   value="{{ auth()->user()->email ?? '' }}" 
                                   required>
                        </div>
                    </div>

                    <div class="alert alert-light-primary border-primary d-flex align-items-center p-3 mt-3 mb-0" style="border-radius: 8px;">
                        <i class="fa-solid fa-circle-info fs-4 text-primary me-3"></i>
                        <div class="fs-8 text-gray-700">
                            <strong>Authoritative Diagnostic Test:</strong> JERO will test the TCP socket connection, verify SSL/TLS certificate validity, authenticate with your SMTP credentials, and dispatch a formatted verification template to the recipient.
                        </div>
                    </div>

                    <!-- Live Diagnostic Output Console -->
                    <div id="smtp-diag-console" class="smtp-diag-box">
                        <div id="smtp-diag-text">[Ready] Select gateway and click Dispatch to initiate socket diagnostics...</div>
                    </div>
                </form>
            </div>

            <div class="modal-footer bg-light py-2 px-4 border-top border-gray-200 d-flex justify-content-between">
                <button type="button" class="btn btn-sm btn-light fw-bold" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-primary fw-bold" id="btn-execute-test-email">
                    <span class="indicator-label"><i class="fa-solid fa-bolt me-1"></i> Dispatch Test Email</span>
                    <span class="indicator-progress">Connecting... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-script')
<script>
/**
 * SUPER ADMIN SMTP MULTI-GATEWAY HUB CONTROLLER SCRIPT
 * Completely isolated to resources/views/super-admin/mail-setting/index.blade.php
 */
$(document).ready(function() {
    'use strict';

    // Preset configurations library
    const smtpPresets = {
        'aws_ses_ssl': {
            host: 'email-smtp.ap-south-1.amazonaws.com',
            port: 465,
            encryption: 'ssl'
        },
        'aws_ses_tls': {
            host: 'email-smtp.ap-south-1.amazonaws.com',
            port: 587,
            encryption: 'tls'
        },
        'gmail_ssl': {
            host: 'smtp.gmail.com',
            port: 465,
            encryption: 'ssl'
        },
        'sendgrid_tls': {
            host: 'smtp.sendgrid.net',
            port: 587,
            encryption: 'tls'
        },
        'brevo_tls': {
            host: 'smtp-relay.brevo.com',
            port: 587,
            encryption: 'tls'
        }
    };

    // =========================================================================
    // 1. Password Visibility Toggle
    // =========================================================================
    $(document).on('click', '.btn-toggle-password', function(e) {
        e.preventDefault();
        const targetId = $(this).data('target');
        const input = $('#' + targetId);
        const icon = $(this).find('i');

        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // =========================================================================
    // 2. Active / Inactive Status Switch Handling
    // =========================================================================
    $(document).on('change', '.gateway-status-toggle', function() {
        const gid = $(this).data('gid');
        const isChecked = $(this).is(':checked');
        const hiddenInput = $('#status_val_' + gid);
        const label = $('#status_text_' + gid);
        const card = $('#gateway-card-' + gid);

        if (isChecked) {
            hiddenInput.val('0'); // In JERO schema: 0 = Active
            label.text('Active').css('color', '#10b981');
            card.removeClass('is-inactive');
        } else {
            hiddenInput.val('1'); // In JERO schema: 1 = Inactive
            label.text('Inactive').css('color', '#94a3b8');
            card.addClass('is-inactive');
        }
        recalcActiveKpi();
    });

    function recalcActiveKpi() {
        let activeCount = 0;
        $('.gateway-status-toggle').each(function() {
            if ($(this).is(':checked')) activeCount++;
        });
        $('#kpi-active-gateways').text(activeCount);
    }

    // =========================================================================
    // 3. Provider Quick Presets Autofill
    // =========================================================================
    $(document).on('click', '.preset-option', function(e) {
        e.preventDefault();
        const gid = $(this).data('gid');
        const presetKey = $(this).data('preset');
        const preset = smtpPresets[presetKey];

        if (preset) {
            $('#mail_host_' + gid).val(preset.host);
            $('#mail_port_' + gid).val(preset.port);
            $('#mail_encryption_' + gid).val(preset.encryption);

            // Toast feedback
            if (typeof toastr !== 'undefined') {
                toastr.info('Applied preset to Gateway #' + gid + ' (' + preset.host + ':' + preset.port + ')');
            }
        }
    });

    // =========================================================================
    // 4. Client-Side Copy Socket Credentials
    // =========================================================================
    $(document).on('click', '.btn-copy-creds', function() {
        const host = $('#' + $(this).data('host')).val();
        const port = $('#' + $(this).data('port')).val();
        const user = $('#' + $(this).data('user')).val();
        const copyText = `Host: ${host}\nPort: ${port}\nUser: ${user}`;

        navigator.clipboard.writeText(copyText).then(function() {
            if (typeof toastr !== 'undefined') {
                toastr.success('Socket parameters copied to clipboard!');
            } else {
                alert('Copied:\n' + copyText);
            }
        });
    });

    // =========================================================================
    // 5. Tier 2: Sub-Tabs Client-Side Filtering
    // =========================================================================
    $('.smtp-tab-btn').on('click', function() {
        $('.smtp-tab-btn').removeClass('active');
        $(this).addClass('active');

        const filter = $(this).data('filter');

        if (filter === 'all') {
            $('.smtp-gateway-card').show();
        } else {
            $('.smtp-gateway-card').hide();
            $('#' + filter.replace('gateway-', 'gateway-card-')).show();
        }
    });

    // =========================================================================
    // 6. Dynamic Debounced Real-Time Search (300ms)
    // =========================================================================
    let searchTimeout = null;
    $('#smtp-search-input').on('keyup input', function() {
        const query = $(this).val().toLowerCase().trim();
        const clearBtn = $('#smtp-search-clear');

        if (query.length > 0) {
            clearBtn.show();
        } else {
            clearBtn.hide();
        }

        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            filterGatewaysByQuery(query);
        }, 300);
    });

    $('#smtp-search-clear').on('click', function() {
        $('#smtp-search-input').val('');
        $(this).hide();
        filterGatewaysByQuery('');
    });

    function filterGatewaysByQuery(query) {
        if (!query) {
            // Respect active sub-tab if search is cleared
            const activeFilter = $('.smtp-tab-btn.active').data('filter');
            if (activeFilter === 'all') {
                $('.smtp-gateway-card').show();
            } else {
                $('.smtp-gateway-card').hide();
                $('#' + activeFilter.replace('gateway-', 'gateway-card-')).show();
            }
            return;
        }

        $('.smtp-gateway-card').each(function() {
            const searchText = $(this).data('search-text') || '';
            if (searchText.indexOf(query) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    // =========================================================================
    // 7. Save All Settings via Transactional AJAX
    // =========================================================================
    $('#btn-save-smtp-settings, #btn-save-smtp-settings-bottom').on('click', function(e) {
        e.preventDefault();
        saveAllSmtpSettings();
    });

    function saveAllSmtpSettings() {
        const saveBtns = $('#btn-save-smtp-settings, #btn-save-smtp-settings-bottom');
        const form = $('#smtpSettingsForm');

        saveBtns.attr('data-kt-indicator', 'on').prop('disabled', true);

        $.ajax({
            url: "{{ route('superadmin.smtp-settings.update', 8) }}",
            type: "POST",
            data: form.serialize(),
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                saveBtns.removeAttr('data-kt-indicator').prop('disabled', false);

                if (response.status === 'success') {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Settings Saved!',
                            text: response.message || 'All SMTP gateways updated successfully. Multi-tenant cache cleared.',
                            timer: 2000,
                            showConfirmButton: false,
                            customClass: {
                                popup: 'rounded-3 shadow'
                            }
                        });
                    } else if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'Updated Successfully');
                    } else {
                        alert(response.message || 'Updated Successfully');
                    }
                } else {
                    handleSaveError(response.message || 'Failed to update settings');
                }
            },
            error: function(xhr) {
                saveBtns.removeAttr('data-kt-indicator').prop('disabled', false);
                let errorMsg = 'An unexpected error occurred while saving.';

                if (xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        const errList = [];
                        $.each(xhr.responseJSON.errors, function(k, v) {
                            errList.push(v[0] || v);
                        });
                        errorMsg = errList.join('<br>');
                    } else if (xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                }

                handleSaveError(errorMsg);
            }
        });
    }

    function handleSaveError(msg) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Unable to Save',
                html: msg,
                customClass: {
                    popup: 'rounded-3 shadow'
                }
            });
        } else {
            alert('Error: ' + msg.replace(/<br>/g, '\n'));
        }
    }

    // =========================================================================
    // 8. Test Email Modal & Live Diagnostic Execution
    // =========================================================================
    $('#btn-open-test-modal').on('click', function() {
        $('#smtpTestEmailModal').modal('show');
    });

    $(document).on('click', '.btn-test-this-gateway', function() {
        const gid = $(this).data('gid');
        $('#modal_gateway_select').val(gid);
        $('#smtpTestEmailModal').modal('show');
    });

    $('#btn-execute-test-email').on('click', function(e) {
        e.preventDefault();

        const btn = $(this);
        const gatewayId = $('#modal_gateway_select').val();
        const testEmail = $('#modal_test_email').val().trim();
        const consoleBox = $('#smtp-diag-console');
        const consoleText = $('#smtp-diag-text');

        if (!testEmail) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Recipient',
                    text: 'Please specify a target recipient email address for testing.'
                });
            } else {
                alert('Please enter a test email address.');
            }
            return;
        }

        btn.attr('data-kt-indicator', 'on').prop('disabled', true);
        consoleBox.show();
        consoleText.html(`[Initiating] Connecting to Gateway #${gatewayId} socket...<br>[Handshake] Verifying host and authentication credentials...`);

        $.ajax({
            url: "{{ route('superadmin.smtp-settings.sendTestEmail') }}",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                gateway_id: gatewayId,
                test_email: testEmail
            },
            dataType: "json",
            success: function(response) {
                btn.removeAttr('data-kt-indicator').prop('disabled', false);

                if (response.status === 'success') {
                    const latency = response.latency_ms || response.data?.latency_ms || 'N/A';
                    const gatewayName = response.gateway || response.data?.gateway_title || ('Gateway #' + gatewayId);
                    const host = response.data?.host || '';

                    consoleText.html(
                        `<span class="smtp-diag-success">[SUCCESS] Handshake verified in ${latency} ms!</span><br>` +
                        `[GATEWAY] ${gatewayName} (${host})<br>` +
                        `[DISPATCH] Verification email delivered to ${testEmail}.<br>` +
                        `[STATUS] Live socket delivery confirmed.`
                    );

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Test Email Sent!',
                            html: `Successfully dispatched via <strong>${gatewayName}</strong><br><span class="text-muted fs-8">Handshake Latency: ${latency} ms</span>`,
                            customClass: {
                                popup: 'rounded-3 shadow'
                            }
                        });
                    }
                } else {
                    const errMsg = response.message || 'SMTP Handshake or Dispatch Failed.';
                    consoleText.html(`<span class="smtp-diag-error">[ERROR] ${errMsg}</span>`);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Delivery Failed',
                            html: errMsg,
                            customClass: {
                                popup: 'rounded-3 shadow'
                            }
                        });
                    }
                }
            },
            error: function(xhr) {
                btn.removeAttr('data-kt-indicator').prop('disabled', false);
                let errMsg = 'Connection timed out or failed to reach mail server.';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                }
                consoleText.html(`<span class="smtp-diag-error">[FATAL ERROR] ${errMsg}</span>`);

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Test Dispatch Error',
                        html: errMsg,
                        customClass: {
                            popup: 'rounded-3 shadow'
                        }
                    });
                } else {
                    alert('Error: ' + errMsg);
                }
            }
        });
    });

});
</script>
@endpush