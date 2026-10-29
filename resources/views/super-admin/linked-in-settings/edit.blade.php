@extends('layouts.superadmin_layout')

@php
    $isConfigured = !empty($linkedInSetting->client_id) && !empty($linkedInSetting->client_secret);
    $isEnabled = ($linkedInSetting->status === 'enable');
@endphp

@push('head-script')
<style>
    /* ─── Global & Top-Gap Normalization (15.6" Screen Standard) ─── */
    body {
        overflow-x: hidden !important;
        background-color: #f8fafc !important;
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
    }
    .header-fixed.toolbar-fixed #kt_wrapper,
    .header-fixed #kt_wrapper,
    .toolbar-fixed #kt_wrapper,
    #kt_wrapper {
        padding-top: calc(65px + 8px) !important;
    }
    @media (max-width: 991.98px) {
        .header-fixed.toolbar-fixed #kt_wrapper,
        .header-fixed #kt_wrapper,
        .toolbar-fixed #kt_wrapper,
        #kt_wrapper {
            padding-top: calc(55px + 6px) !important;
        }
    }
    #kt_toolbar {
        display: none !important;
        height: 0 !important;
        min-height: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    #kt_content,
    .content,
    #kt_post,
    .post {
        padding-top: 0 !important;
        margin-top: 0 !important;
    }

    /* ─── Scoped LinkedIn Settings Container (Rule #17 Isolated) ─── */
    .linkedin-page-container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 16px 28px 16px !important;
        margin-top: 0 !important;
        box-sizing: border-box;
    }

    /* Tier 1: Top Header Toolbar Card */
    .linkedin-toolbar-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 16px 22px !important;
        margin-bottom: 14px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
    }

    .linkedin-brand-icon-box {
        width: 44px;
        height: 44px;
        background: #0077b5;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 22px;
        box-shadow: 0 3px 10px rgba(0, 119, 181, 0.28);
        flex-shrink: 0;
    }

    .linkedin-title {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.25;
        margin-bottom: 2px;
    }

    .linkedin-subtitle {
        font-size: 11.5px;
        color: #64748b;
        margin-bottom: 0;
    }

    .linkedin-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 20px;
        line-height: 1.2;
    }

    .linkedin-status-pill.active {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .linkedin-status-pill.inactive {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .linkedin-status-pill.ssl-verified {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #dbeafe;
    }

    .linkedin-status-pill.ssl-advisory {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    /* Tier 2: Sub-Tabs Navigation Card */
    .linkedin-tabs-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 8px 12px !important;
        margin-bottom: 14px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    }

    .linkedin-nav-pills {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .linkedin-tab-btn {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        padding: 8px 16px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        color: #475569 !important;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none !important;
    }

    .linkedin-tab-btn:hover {
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }

    /* Rule #20: Strict Active Highlight */
    .linkedin-tab-btn.active {
        background: #009ef7 !important;
        border-color: #009ef7 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(0, 158, 247, 0.28) !important;
    }

    .linkedin-tab-badge {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 12px;
        line-height: 1.2;
    }

    .linkedin-tab-btn.active .linkedin-tab-badge {
        background: rgba(255, 255, 255, 0.25) !important;
        color: #ffffff !important;
    }

    .linkedin-tab-btn:not(.active) .linkedin-tab-badge {
        background: #f1f5f9;
        color: #475569;
    }

    /* Tier 3: Modular Sub-Cards */
    .linkedin-sub-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
        margin-bottom: 14px !important;
        overflow: hidden;
    }

    .linkedin-subcard-header {
        padding: 14px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #ffffff;
        gap: 12px;
    }

    .linkedin-subcard-title {
        font-size: 13.5px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .linkedin-subcard-body {
        padding: 18px 20px;
    }

    /* Form Controls */
    .linkedin-form-label {
        font-size: 12px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .linkedin-form-control {
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        font-size: 12.5px !important;
        color: #0f172a !important;
        padding: 8px 12px !important;
        background: #ffffff !important;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
        height: 38px;
    }

    .linkedin-form-control:focus {
        border-color: #009ef7 !important;
        box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
        outline: none !important;
    }

    .linkedin-form-control[readonly] {
        background: #f8fafc !important;
        color: #475569 !important;
        cursor: default;
    }

    .input-group-btn-action {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-left: none;
        color: #64748b;
        padding: 0 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
        font-size: 12px;
    }

    .input-group-btn-action:hover {
        background: #f1f5f9;
        color: #009ef7;
    }

    /* Switch Customization */
    .form-check-input.linkedin-switch {
        width: 44px;
        height: 22px;
        cursor: pointer;
    }

    .form-check-input.linkedin-switch:checked {
        background-color: #009ef7;
        border-color: #009ef7;
    }

    /* Metric Box */
    .metric-subbox {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 14px;
        height: 100%;
    }

    .metric-subbox-label {
        font-size: 11px;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .metric-subbox-value {
        font-size: 13.5px;
        font-weight: 700;
        color: #0f172a;
    }

    /* Developer Guide Timeline */
    .guide-step-item {
        position: relative;
        padding-left: 36px;
        padding-bottom: 22px;
    }

    .guide-step-item:last-child {
        padding-bottom: 0;
    }

    .guide-step-item::before {
        content: '';
        position: absolute;
        left: 14px;
        top: 28px;
        bottom: 0;
        width: 2px;
        background: #e2e8f0;
    }

    .guide-step-item:last-child::before {
        display: none;
    }

    .guide-step-badge {
        position: absolute;
        left: 0;
        top: 0;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #eff6ff;
        border: 2px solid #009ef7;
        color: #009ef7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
    }

    .guide-step-title {
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .guide-step-desc {
        font-size: 12px;
        color: #64748b;
        line-height: 1.5;
        margin-bottom: 6px;
    }
</style>
@endpush

@section('content')
<div class="linkedin-page-container">

    <!-- ─── TIER 1: TOP HEADER TOOLBAR CARD ─── -->
    <div class="linkedin-toolbar-card">
        <div class="d-flex align-items-center gap-3">
            <div class="linkedin-brand-icon-box">
                <i class="fab fa-linkedin-in text-white"></i>
            </div>
            <div>
                <h1 class="linkedin-title">LinkedIn Integration &amp; OAuth Hub</h1>
                <p class="linkedin-subtitle">
                    Configure global OAuth 2.0 (OpenID Connect) credentials for candidate "Apply with LinkedIn" and tenant career portal activation.
                </p>
            </div>
        </div>

        <div class="d-flex align-items-center flex-wrap gap-2">
            <!-- Dynamic Status Badge -->
            <div id="toolbar-status-badge">
                @if($isEnabled && $isConfigured)
                    <span class="linkedin-status-pill active">
                        <i class="fas fa-check-circle text-success"></i> Active &amp; Ready
                    </span>
                @elseif($isEnabled && !$isConfigured)
                    <span class="linkedin-status-pill inactive">
                        <i class="fas fa-exclamation-triangle text-warning"></i> Credentials Incomplete
                    </span>
                @else
                    <span class="linkedin-status-pill inactive">
                        <i class="fas fa-pause-circle text-muted"></i> Integration Inactive
                    </span>
                @endif
            </div>

            <!-- SSL Compliance Pill -->
            <span class="linkedin-status-pill {{ $sslData['is_secure'] ? 'ssl-verified' : 'ssl-advisory' }}">
                <i class="fas {{ $sslData['is_secure'] ? 'fa-lock text-primary' : 'fa-info-circle text-warning' }}"></i>
                {{ $sslData['label'] }}
            </span>

            <!-- Top Action Buttons -->
            <button type="button" class="btn btn-sm btn-light-primary fw-bold" id="top-test-btn">
                <i class="fas fa-plug me-1"></i> Test Connection
            </button>
            <button type="button" class="btn btn-sm btn-primary fw-bold" id="top-save-btn">
                <span class="indicator-label"><i class="fas fa-check me-1"></i> Save Changes</span>
                <span class="indicator-progress">Saving... <span class="spinner-border spinner-border-sm align-middle ms-1"></span></span>
            </button>
        </div>
    </div>

    <!-- ─── TIER 2: SUB-TABS NAVIGATION CARD ─── -->
    <div class="linkedin-tabs-card">
        <ul class="linkedin-nav-pills" id="linkedinTab" role="tablist">
            <li>
                <button type="button" class="linkedin-tab-btn active" id="tab-credentials-link" data-bs-toggle="tab" data-bs-target="#tab-credentials" data-target="#tab-credentials" role="tab" aria-selected="true">
                    <i class="fas fa-key"></i>
                    <span>API Credentials &amp; Settings</span>
                    <span class="linkedin-tab-badge">{{ $isConfigured ? 'Configured' : 'Needs Setup' }}</span>
                </button>
            </li>
            <li>
                <button type="button" class="linkedin-tab-btn" id="tab-guide-link" data-bs-toggle="tab" data-bs-target="#tab-guide" data-target="#tab-guide" role="tab" aria-selected="false">
                    <i class="fas fa-book-open"></i>
                    <span>LinkedIn Setup Guide</span>
                    <span class="linkedin-tab-badge">5 Steps</span>
                </button>
            </li>
            <li>
                <button type="button" class="linkedin-tab-btn" id="tab-diagnostics-link" data-bs-toggle="tab" data-bs-target="#tab-diagnostics" data-target="#tab-diagnostics" role="tab" aria-selected="false">
                    <i class="fas fa-shield-alt"></i>
                    <span>Environment &amp; Diagnostics</span>
                    <span class="linkedin-tab-badge">{{ $sslData['scheme'] }}</span>
                </button>
            </li>
        </ul>
    </div>

    <!-- ─── TIER 3: SUB-CARDS CONTENT STREAM ─── -->
    <div class="tab-content" id="linkedinTabContent">

        <!-- ═════════════════════════════════════════════════════════════════════ -->
        <!-- TAB 1: API CREDENTIALS & SETTINGS                                   -->
        <!-- ═════════════════════════════════════════════════════════════════════ -->
        <div class="tab-pane fade show active" id="tab-credentials" role="tabpanel">
            <form id="linkedinSettingsForm" class="ajax-form">
                @csrf
                @method('PUT')

                <!-- Sub-Card 1: Status & Integration Mode -->
                <div class="linkedin-sub-card">
                    <div class="linkedin-subcard-header">
                        <h3 class="linkedin-subcard-title">
                            <i class="fas fa-toggle-on text-primary"></i> Global Activation &amp; Tenant Gatekeeper
                        </h3>
                        <div class="form-check form-switch form-check-custom form-check-solid m-0">
                            <input class="form-check-input linkedin-switch" type="checkbox" name="status" value="enable" id="status"
                                @if($linkedInSetting->status === 'enable') checked @endif>
                            <label class="form-check-label ms-2 fw-bold text-dark fs-7" for="status" id="status-toggle-label">
                                {{ $linkedInSetting->status === 'enable' ? 'Enabled' : 'Disabled' }}
                            </label>
                        </div>
                    </div>
                    <div class="linkedin-subcard-body">
                        <div class="row align-items-center">
                            <div class="col-md-9 col-12">
                                <p class="text-muted fs-7 mb-0">
                                    When enabled globally, tenant organizations (Service Company &amp; Staffing Company) can activate
                                    <strong>"Sign In with LinkedIn"</strong> on their branded career portals, and candidate applicants
                                    can pre-fill applications using their verified LinkedIn profile data.
                                </p>
                            </div>
                            <div class="col-md-3 col-12 text-md-end mt-2 mt-md-0">
                                <span class="badge {{ $linkedInSetting->status === 'enable' ? 'badge-light-success text-success' : 'badge-light-secondary text-muted' }} fw-bold fs-8" id="status-badge-inline">
                                    <i class="fas {{ $linkedInSetting->status === 'enable' ? 'fa-check-circle text-success' : 'fa-pause-circle text-muted' }} me-1"></i>
                                    {{ $linkedInSetting->status === 'enable' ? 'System Active' : 'System Paused' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 2: OAuth 2.0 Credentials -->
                <div class="linkedin-sub-card">
                    <div class="linkedin-subcard-header">
                        <h3 class="linkedin-subcard-title">
                            <i class="fas fa-shield-alt text-primary"></i> OAuth 2.0 Client Credentials
                        </h3>
                        <a href="https://www.linkedin.com/developers/apps" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-light-primary py-1 px-3 fs-8 fw-bold">
                            <i class="fas fa-external-link-alt me-1"></i> Open LinkedIn Developer Portal
                        </a>
                    </div>
                    <div class="linkedin-subcard-body">
                        <div class="row g-4">
                            <!-- Client ID -->
                            <div class="col-md-6 col-12">
                                <div class="form-group mb-0">
                                    <label class="linkedin-form-label" for="client_id">
                                        <span>Client ID (App ID) <span class="text-danger">*</span></span>
                                        <small class="text-muted">From Auth Tab</small>
                                    </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control linkedin-form-control" id="client_id" name="client_id"
                                            value="{{ $linkedInSetting->client_id }}" placeholder="e.g. 78xxxxxxxxxxxxxxx" autocomplete="off">
                                        <button class="btn input-group-btn-action copy-btn" type="button" data-clipboard-target="#client_id" title="Copy Client ID">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                    <span class="text-muted fs-8 mt-1 d-block">Public alphanumeric identifier generated by your LinkedIn Developer App.</span>
                                </div>
                            </div>

                            <!-- Client Secret -->
                            <div class="col-md-6 col-12">
                                <div class="form-group mb-0">
                                    <label class="linkedin-form-label" for="client_secret">
                                        <span>Client Secret <span class="text-danger">*</span></span>
                                        <small class="text-muted">Confidential</small>
                                    </label>
                                    <div class="input-group">
                                        <input type="password" class="form-control linkedin-form-control" id="client_secret" name="client_secret"
                                            value="{{ $linkedInSetting->client_secret ? '••••••••••••••••••••' : '' }}"
                                            placeholder="Enter Client Secret" autocomplete="new-password">
                                        <button class="btn input-group-btn-action" type="button" id="toggle-secret-btn" title="Show/Hide Secret">
                                            <i class="fas fa-eye" id="toggle-secret-icon"></i>
                                        </button>
                                        <button class="btn input-group-btn-action copy-btn" type="button" data-clipboard-target="#client_secret" title="Copy Secret">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                    <span class="text-muted fs-8 mt-1 d-block">
                                        Keep secret. Leave bullets as-is to preserve existing saved secret.
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 3: Authorized Redirect Callback URL -->
                <div class="linkedin-sub-card">
                    <div class="linkedin-subcard-header">
                        <h3 class="linkedin-subcard-title">
                            <i class="fas fa-link text-primary"></i> Authorized Redirect (Callback) URL
                        </h3>
                        <span class="badge badge-light-primary fw-bold fs-8">Strict Route Binding</span>
                    </div>
                    <div class="linkedin-subcard-body">
                        <div class="row align-items-center g-3">
                            <div class="col-lg-8 col-12">
                                <label class="linkedin-form-label" for="callback_url_display">
                                    <span>OAuth 2.0 Redirect URI</span>
                                    <span class="text-primary fs-8">Must match LinkedIn Portal exactly</span>
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control linkedin-form-control" id="callback_url_display" readonly
                                        value="{{ $sslData['callback_url'] }}">
                                    <button class="btn input-group-btn-action copy-btn" type="button" data-clipboard-target="#callback_url_display" title="Copy Callback URL">
                                        <i class="fas fa-copy me-1"></i> Copy URL
                                    </button>
                                    <a href="{{ $sslData['callback_url'] }}" target="_blank" class="btn input-group-btn-action" title="Open Callback URL">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-4 col-12">
                                <div class="p-3 bg-light-primary rounded border border-primary border-opacity-25">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="fas fa-info-circle text-primary mt-1"></i>
                                        <div class="fs-8 text-dark">
                                            Paste this exact URL into your LinkedIn Developer App under:
                                            <div class="fw-bold mt-1 text-primary">Auth &rarr; OAuth 2.0 settings &rarr; Authorized redirect URLs for your app</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Primary Action Bar -->
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mt-4 pt-2">
                    <div class="text-muted fs-8">
                        <i class="fas fa-lock me-1"></i> Changes take effect immediately across all tenant career portals via atomic cache eviction.
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-light-warning fw-bold" id="form-reset-btn">
                            <i class="fas fa-undo me-1"></i> Reset
                        </button>
                        <button type="button" class="btn btn-sm btn-light-primary fw-bold" id="test-connection-btn">
                            <i class="fas fa-plug me-1"></i> Test Connection
                        </button>
                        <button type="button" class="btn btn-sm btn-primary fw-bold" id="save-form">
                            <span class="indicator-label"><i class="fas fa-save me-1"></i> Save &amp; Update Credentials</span>
                            <span class="indicator-progress">Saving... <span class="spinner-border spinner-border-sm align-middle ms-1"></span></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- ═════════════════════════════════════════════════════════════════════ -->
        <!-- TAB 2: LINKEDIN DEVELOPER SETUP GUIDE                               -->
        <!-- ═════════════════════════════════════════════════════════════════════ -->
        <div class="tab-pane fade" id="tab-guide" role="tabpanel">
            <div class="row g-4">
                <!-- Step by step guide -->
                <div class="col-lg-8 col-12">
                    <div class="linkedin-sub-card h-100">
                        <div class="linkedin-subcard-header">
                            <h3 class="linkedin-subcard-title">
                                <i class="fas fa-list-ol text-primary"></i> 5-Step LinkedIn Developer App Configuration
                            </h3>
                            <a href="https://www.linkedin.com/developers/apps/new" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-light-primary fw-bold fs-8">
                                <i class="fas fa-plus me-1"></i> Create App on LinkedIn
                            </a>
                        </div>
                        <div class="linkedin-subcard-body">
                            <!-- Step 1 -->
                            <div class="guide-step-item">
                                <div class="guide-step-badge">1</div>
                                <div class="guide-step-title">Create App on LinkedIn Developer Portal</div>
                                <div class="guide-step-desc">
                                    Navigate to <a href="https://www.linkedin.com/developers/apps" target="_blank" class="fw-bold">LinkedIn Developer Portal</a>. Click <strong>"Create app"</strong>, specify your platform name (e.g., <code>JERO Platform</code>), associate your official LinkedIn Company Page, and upload your platform logo.
                                </div>
                            </div>

                            <!-- Step 2 -->
                            <div class="guide-step-item">
                                <div class="guide-step-badge">2</div>
                                <div class="guide-step-title">Add the "Sign In with LinkedIn" Product</div>
                                <div class="guide-step-desc">
                                    In your app dashboard, go to the <strong>Products</strong> tab. Locate and add:
                                    <div class="d-flex align-items-center gap-2 mt-2">
                                        <span class="badge badge-light-success text-success fw-bold">
                                            <i class="fas fa-check me-1"></i> Sign In with LinkedIn using OpenID Connect
                                        </span>
                                    </div>
                                    <div class="text-muted fs-8 mt-1">
                                        LinkedIn will automatically grant the necessary OpenID Connect scopes (<code>openid</code>, <code>profile</code>, <code>email</code>).
                                    </div>
                                </div>
                            </div>

                            <!-- Step 3 -->
                            <div class="guide-step-item">
                                <div class="guide-step-badge">3</div>
                                <div class="guide-step-title">Register the Authorized Redirect URL</div>
                                <div class="guide-step-desc">
                                    Go to the <strong>Auth</strong> tab &rarr; scroll to <strong>OAuth 2.0 settings</strong> &rarr; click the edit pencil icon next to <strong>Authorized redirect URLs for your app</strong>. Click <strong>"Add redirect URL"</strong> and paste:
                                    <div class="input-group mt-2 mb-1" style="max-width: 580px;">
                                        <input type="text" class="form-control form-control-sm bg-light" readonly value="{{ $sslData['callback_url'] }}">
                                        <button class="btn btn-sm btn-secondary copy-btn" type="button" data-clipboard-text="{{ $sslData['callback_url'] }}">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 4 -->
                            <div class="guide-step-item">
                                <div class="guide-step-badge">4</div>
                                <div class="guide-step-title">Copy Client ID &amp; Client Secret</div>
                                <div class="guide-step-desc">
                                    Still in the <strong>Auth</strong> tab, locate the <strong>Application credentials</strong> section. Copy the <strong>Client ID</strong> and <strong>Primary Client Secret</strong>.
                                </div>
                            </div>

                            <!-- Step 5 -->
                            <div class="guide-step-item">
                                <div class="guide-step-badge">5</div>
                                <div class="guide-step-title">Paste Credentials in JERO &amp; Activate</div>
                                <div class="guide-step-desc">
                                    Return to <a href="#tab-credentials" class="switch-to-credentials fw-bold">Tab 1 (API Credentials)</a>, paste the values into the Client ID and Secret fields, turn on the <strong>Global Activation</strong> switch, and click <strong>"Save &amp; Update Credentials"</strong>.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Scope reference & policies -->
                <div class="col-lg-4 col-12">
                    <div class="linkedin-sub-card mb-4">
                        <div class="linkedin-subcard-header">
                            <h3 class="linkedin-subcard-title">
                                <i class="fas fa-shield-alt text-primary"></i> Required OAuth 2.0 Scopes
                            </h3>
                        </div>
                        <div class="linkedin-subcard-body p-3">
                            <div class="d-flex flex-column gap-2">
                                <div class="p-2 border rounded bg-light-primary border-primary border-opacity-25">
                                    <div class="fw-bold fs-7 text-primary">openid</div>
                                    <div class="fs-8 text-muted">Authenticates candidate identity using OpenID Connect standard.</div>
                                </div>
                                <div class="p-2 border rounded bg-light-info border-info border-opacity-25">
                                    <div class="fw-bold fs-7 text-info">profile</div>
                                    <div class="fs-8 text-muted">Retrieves candidate first name, last name, and profile picture.</div>
                                </div>
                                <div class="p-2 border rounded bg-light-success border-success border-opacity-25">
                                    <div class="fw-bold fs-7 text-success">email</div>
                                    <div class="fs-8 text-muted">Fetches candidate's verified primary LinkedIn email address.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="linkedin-sub-card">
                        <div class="linkedin-subcard-header">
                            <h3 class="linkedin-subcard-title">
                                <i class="fas fa-info-circle text-primary"></i> Token Lifecycle
                            </h3>
                        </div>
                        <div class="linkedin-subcard-body p-3">
                            <ul class="text-muted fs-8 ps-3 mb-0">
                                <li class="mb-2"><strong>Access Token Validity:</strong> 60 days standard lifespan.</li>
                                <li class="mb-2"><strong>Stateless Execution:</strong> JERO candidate OAuth runs via <code>Socialite::driver('linkedin')->stateless()</code> to eliminate cross-domain session cookies.</li>
                                <li><strong>Pre-fill Data:</strong> Injects candidate name, email, and photo into the ATS job application form.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═════════════════════════════════════════════════════════════════════ -->
        <!-- TAB 3: ENVIRONMENT & SSL DIAGNOSTICS                                -->
        <!-- ═════════════════════════════════════════════════════════════════════ -->
        <div class="tab-pane fade" id="tab-diagnostics" role="tabpanel">
            <div class="row g-4">
                <!-- Sub-Card: SSL Security Verification -->
                <div class="col-lg-6 col-12">
                    <div class="linkedin-sub-card h-100">
                        <div class="linkedin-subcard-header">
                            <h3 class="linkedin-subcard-title">
                                <i class="fas fa-lock text-primary"></i> SSL / HTTPS Protocol Status
                            </h3>
                            <span class="badge {{ $sslData['badge_class'] }} fw-bold fs-8">
                                {{ $sslData['status'] === 'verified' ? 'Verified' : 'Advisory' }}
                            </span>
                        </div>
                        <div class="linkedin-subcard-body">
                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <div class="metric-subbox">
                                        <div class="metric-subbox-label">Request Scheme</div>
                                        <div class="metric-subbox-value text-uppercase">
                                            <i class="fas {{ $sslData['is_secure'] ? 'fa-lock text-success' : 'fa-unlock text-warning' }} me-1"></i>
                                            {{ $sslData['scheme'] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="metric-subbox">
                                        <div class="metric-subbox-label">Host Environment</div>
                                        <div class="metric-subbox-value fs-7 text-truncate" title="{{ $sslData['host'] }}">
                                            {{ $sslData['host'] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="metric-subbox">
                                        <div class="metric-subbox-label">Local Dev Mode</div>
                                        <div class="metric-subbox-value">
                                            {{ $sslData['is_local'] ? 'Active (HTTP Allowed)' : 'Production' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="metric-subbox">
                                        <div class="metric-subbox-label">Socialite Driver</div>
                                        <div class="metric-subbox-value text-success">
                                            <i class="fas fa-check-circle me-1"></i> Ready
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($sslData['is_secure'])
                                <div class="alert alert-dismissible bg-light-success border border-success border-opacity-25 d-flex align-items-center p-3 mb-0">
                                    <i class="fas fa-shield-alt text-success fs-3 me-3"></i>
                                    <div class="fs-8 text-dark">
                                        <strong>HTTPS Encryption Active:</strong> This server meets all of LinkedIn's production OAuth 2.0 security specifications for callback URLs.
                                    </div>
                                </div>
                            @elseif($sslData['is_local'])
                                <div class="alert alert-dismissible bg-light-info border border-info border-opacity-25 d-flex align-items-center p-3 mb-0">
                                    <i class="fas fa-laptop-code text-info fs-3 me-3"></i>
                                    <div class="fs-8 text-dark">
                                        <strong>Local Development Detected:</strong> LinkedIn Developer APIs permit <code>http://localhost</code> for local development and sandbox debugging.
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-dismissible bg-light-warning border border-warning border-opacity-25 d-flex align-items-center p-3 mb-0">
                                    <i class="fas fa-exclamation-triangle text-warning fs-3 me-3"></i>
                                    <div class="fs-8 text-dark">
                                        <strong>Production SSL Recommended:</strong> If this application is accessible publicly, LinkedIn will require an HTTPS callback URL to authorize authentication.
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sub-Card: Live API Handshake Tester -->
                <div class="col-lg-6 col-12">
                    <div class="linkedin-sub-card h-100">
                        <div class="linkedin-subcard-header">
                            <h3 class="linkedin-subcard-title">
                                <i class="fas fa-tachometer-alt text-primary"></i> Live LinkedIn API Handshake Tester
                            </h3>
                        </div>
                        <div class="linkedin-subcard-body">
                            <p class="text-muted fs-7 mb-3">
                                Test network connectivity, DNS resolution, and endpoint reachability directly against LinkedIn's OAuth authentication cluster (<code>https://www.linkedin.com/oauth/v2/accessToken</code>).
                            </p>

                            <div class="d-grid gap-2 mb-4">
                                <button type="button" class="btn btn-primary fw-bold py-2" id="run-diagnostic-btn">
                                    <span class="indicator-label">
                                        <i class="fas fa-play-circle me-1"></i> Run Live Diagnostic Test
                                    </span>
                                    <span class="indicator-progress">
                                        Connecting to LinkedIn... <span class="spinner-border spinner-border-sm align-middle ms-1"></span>
                                    </span>
                                </button>
                            </div>

                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="metric-subbox">
                                        <div class="metric-subbox-label">Handshake Status</div>
                                        <div class="metric-subbox-value fs-7" id="diagnostic-status">
                                            <span class="text-muted">Not Tested</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="metric-subbox">
                                        <div class="metric-subbox-label">Round-Trip Latency</div>
                                        <div class="metric-subbox-value fs-7" id="diagnostic-latency">
                                            <span class="text-muted">-- ms</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="metric-subbox">
                                        <div class="metric-subbox-label">Response Details</div>
                                        <div class="metric-subbox-value fs-8 fw-normal text-muted" id="diagnostic-details">
                                            Click "Run Live Diagnostic Test" to execute real-time handshake.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card: Downstream ATS Consumers -->
                <div class="col-12">
                    <div class="linkedin-sub-card mb-0">
                        <div class="linkedin-subcard-header">
                            <h3 class="linkedin-subcard-title">
                                <i class="fas fa-sitemap text-primary"></i> Downstream ATS &amp; Tenant Propagation Matrix
                            </h3>
                        </div>
                        <div class="linkedin-subcard-body p-0">
                            <div class="table-responsive">
                                <table class="table table-row-bordered align-middle gs-4 gy-3 mb-0">
                                    <thead class="bg-light">
                                        <tr class="text-muted fw-bold fs-8 text-uppercase">
                                            <th>Module / Consumer</th>
                                            <th>Target Audience</th>
                                            <th>Integration Role</th>
                                            <th>State</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fs-7">
                                        <tr>
                                            <td class="fw-bold text-dark">
                                                <i class="fas fa-briefcase text-primary me-2"></i> Public Career Portal (FrontJobsController)
                                            </td>
                                            <td>Candidates</td>
                                            <td>"Apply with LinkedIn" / "Sign in with LinkedIn" button</td>
                                            <td>
                                                <span class="badge {{ $isEnabled ? 'badge-light-success text-success' : 'badge-light-secondary text-muted' }} fw-bold">
                                                    {{ $isEnabled ? 'Active' : 'Disabled' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-dark">
                                                <i class="fas fa-building text-info me-2"></i> Service Company Portal
                                            </td>
                                            <td>Corporate Clients</td>
                                            <td>Company-level career portal LinkedIn toggle gate</td>
                                            <td>
                                                <span class="badge {{ $isEnabled ? 'badge-light-success text-success' : 'badge-light-secondary text-muted' }} fw-bold">
                                                    {{ $isEnabled ? 'Permitted' : 'Gated' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-dark">
                                                <i class="fas fa-users-cog text-warning me-2"></i> Admin &amp; Staffing Portal
                                            </td>
                                            <td>Staffing Agencies</td>
                                            <td>Recruiter candidate sourcing &amp; applicant profile sync</td>
                                            <td>
                                                <span class="badge {{ $isEnabled ? 'badge-light-success text-success' : 'badge-light-secondary text-muted' }} fw-bold">
                                                    {{ $isEnabled ? 'Permitted' : 'Gated' }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('footer-script')
<script>
    $(document).ready(function() {

        // ─── 1. Password Eye Toggle ───
        $('#toggle-secret-btn').on('click', function(e) {
            e.preventDefault();
            var $input = $('#client_secret');
            var $icon = $('#toggle-secret-icon');
            if ($input.attr('type') === 'password') {
                $input.attr('type', 'text');
                $icon.removeClass('fa-eye').addClass('fa-eye-slash text-primary');
            } else {
                $input.attr('type', 'password');
                $icon.removeClass('fa-eye-slash text-primary').addClass('fa-eye');
            }
        });

        // ─── 2. 1-Click Clipboard Copy ───
        $('.copy-btn').on('click', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var textToCopy = '';

            if ($btn.data('clipboard-target')) {
                textToCopy = $($btn.data('clipboard-target')).val();
            } else if ($btn.data('clipboard-text')) {
                textToCopy = $btn.data('clipboard-text');
            }

            if (!textToCopy || textToCopy.indexOf('••••') === 0) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        text: 'Cannot copy empty or masked credentials.',
                        icon: 'info',
                        buttonsStyling: false,
                        confirmButtonText: 'OK',
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                }
                return;
            }

            var originalHtml = $btn.html();

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(textToCopy).then(function() {
                    $btn.html('<i class="fas fa-check text-success"></i>');
                    setTimeout(function() { $btn.html(originalHtml); }, 1800);
                }).catch(function() {
                    fallbackCopyText(textToCopy, $btn, originalHtml);
                });
            } else {
                fallbackCopyText(textToCopy, $btn, originalHtml);
            }
        });

        function fallbackCopyText(text, $btn, originalHtml) {
            var $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(text).select();
            document.execCommand('copy');
            $temp.remove();
            $btn.html('<i class="fas fa-check text-success"></i>');
            setTimeout(function() { $btn.html(originalHtml); }, 1800);
        }

        // ─── 3. Switch Change Handler (Optimistic UI + Status Toggle) ───
        $('#status').on('change', function() {
            var isChecked = $(this).is(':checked');
            var targetStatus = isChecked ? 'enable' : 'disable';

            $('#status-toggle-label').text(isChecked ? 'Enabled' : 'Disabled');

            var $badgeInline = $('#status-badge-inline');
            var $toolbarBadge = $('#toolbar-status-badge');

            if (isChecked) {
                $badgeInline.removeClass('badge-light-secondary text-muted').addClass('badge-light-success text-success')
                    .html('<i class="fas fa-check-circle text-success me-1"></i> System Active');
                $toolbarBadge.html('<span class="linkedin-status-pill active"><i class="fas fa-check-circle text-success"></i> Active &amp; Ready</span>');
            } else {
                $badgeInline.removeClass('badge-light-success text-success').addClass('badge-light-secondary text-muted')
                    .html('<i class="fas fa-pause-circle text-muted me-1"></i> System Paused');
                $toolbarBadge.html('<span class="linkedin-status-pill inactive"><i class="fas fa-pause-circle text-muted"></i> Integration Inactive</span>');
            }

            // AJAX call to persist status toggle instantly
            $.ajax({
                url: '{{ route("superadmin.updateStatus", $linkedInSetting->id) }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: targetStatus
                },
                success: function(response) {
                    if (response.status === 'fail') {
                        // Revert checkbox state
                        $('#status').prop('checked', !isChecked);
                        $('#status-toggle-label').text(!isChecked ? 'Enabled' : 'Disabled');
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                text: response.message || 'Error updating status.',
                                icon: 'warning',
                                buttonsStyling: false,
                                confirmButtonText: 'OK',
                                customClass: { confirmButton: 'btn btn-primary' }
                            });
                        }
                    }
                },
                error: function(xhr) {
                    // Revert checkbox state
                    $('#status').prop('checked', !isChecked);
                    $('#status-toggle-label').text(!isChecked ? 'Enabled' : 'Disabled');
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Server error while toggling status.';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            text: msg,
                            icon: 'error',
                            buttonsStyling: false,
                            confirmButtonText: 'OK',
                            customClass: { confirmButton: 'btn btn-primary' }
                        });
                    }
                }
            });
        });

        // ─── 4. Save Form Handler ───
        function submitSettingsForm() {
            var $allSaveButtons = $('#save-form, #top-save-btn');
            $allSaveButtons.attr('data-kt-indicator', 'on').prop('disabled', true);

            var formData = new FormData($('#linkedinSettingsForm')[0]);

            // Ensure status value is present even if switch unchecked
            if (!$('#status').is(':checked')) {
                formData.set('status', 'disable');
            }

            $.ajax({
                url: '{{ route("superadmin.linkedin-settings.update", $linkedInSetting->id) }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    $allSaveButtons.removeAttr('data-kt-indicator').prop('disabled', false);

                    if (response.status === 'success') {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                text: response.message || 'LinkedIn settings updated successfully.',
                                icon: 'success',
                                buttonsStyling: false,
                                confirmButtonText: 'OK',
                                customClass: { confirmButton: 'btn btn-primary' }
                            });
                        } else if (typeof toastr !== 'undefined') {
                            toastr.success(response.message);
                        }
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                text: response.message || 'Failed to update settings.',
                                icon: 'error',
                                buttonsStyling: false,
                                confirmButtonText: 'OK',
                                customClass: { confirmButton: 'btn btn-primary' }
                            });
                        }
                    }
                },
                error: function(xhr) {
                    $allSaveButtons.removeAttr('data-kt-indicator').prop('disabled', false);

                    var msg = 'Failed to update settings.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            html: '<div class="text-start">' + msg + '</div>',
                            icon: 'error',
                            buttonsStyling: false,
                            confirmButtonText: 'OK',
                            customClass: { confirmButton: 'btn btn-primary' }
                        });
                    }
                }
            });
        }

        $('#save-form, #top-save-btn').on('click', function(e) {
            e.preventDefault();
            submitSettingsForm();
        });

        // ─── 5. Reset Form Handler ───
        $('#form-reset-btn').on('click', function(e) {
            e.preventDefault();
            $('#linkedinSettingsForm')[0].reset();
        });

        // ─── 6. Bulletproof Tab Switching Engine ───
        function switchLinkedInTab(targetId) {
            if (!targetId) return;

            // Normalize target ID
            if (targetId.indexOf('#') !== 0) {
                targetId = '#' + targetId;
            }

            // Update Tab button active state
            $('.linkedin-tab-btn').removeClass('active').attr('aria-selected', 'false');
            $('.linkedin-tab-btn[data-target="' + targetId + '"]').addClass('active').attr('aria-selected', 'true');

            // Switch Tab pane visibility with show/active classes
            $('#linkedinTabContent > .tab-pane').removeClass('show active');
            $(targetId).addClass('show active');

            // Gracefully trigger BS5 Tab API if loaded
            try {
                var triggerBtn = document.querySelector('.linkedin-tab-btn[data-target="' + targetId + '"]');
                if (triggerBtn && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                    bootstrap.Tab.getOrCreateInstance(triggerBtn).show();
                }
            } catch (err) {
                // Ignore fallback
            }
        }

        // Tab click event
        $('.linkedin-tab-btn').on('click', function(e) {
            e.preventDefault();
            var target = $(this).attr('data-target') || $(this).data('bs-target') || $(this).attr('href');
            switchLinkedInTab(target);
        });

        // Link to Tab 1 from Setup Guide
        $('.switch-to-credentials').on('click', function(e) {
            e.preventDefault();
            switchLinkedInTab('#tab-credentials');
        });

        // ─── 7. Run Diagnostic Test Connection Handler ───
        function runDiagnostic() {
            var $btn = $('#run-diagnostic-btn');
            $btn.attr('data-kt-indicator', 'on').prop('disabled', true);

            var $status = $('#diagnostic-status');
            var $latency = $('#diagnostic-latency');
            var $details = $('#diagnostic-details');

            $status.html('<span class="text-primary"><span class="spinner-border spinner-border-sm me-1"></span> Testing...</span>');
            $latency.html('<span class="text-muted">-- ms</span>');
            $details.text('Contacting LinkedIn OAuth server...');

            var clientId = $.trim($('#client_id').val());
            var clientSecret = $.trim($('#client_secret').val());

            $.ajax({
                url: '{{ route("superadmin.linkedin-settings.testConnection") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    client_id: clientId,
                    client_secret: clientSecret
                },
                success: function(response) {
                    $btn.removeAttr('data-kt-indicator').prop('disabled', false);

                    var latencyMs = (response.data && response.data.latency_ms) ? response.data.latency_ms : '--';
                    $latency.html('<span class="text-success fw-bold">' + latencyMs + ' ms</span>');

                    if (response.status === 'success') {
                        $status.html('<span class="badge badge-light-success text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Connected</span>');
                        $details.html('<span class="text-success">' + (response.message || 'LinkedIn OAuth cluster reachable and responsive.') + '</span>');
                    } else {
                        $status.html('<span class="badge badge-light-warning text-warning fw-bold"><i class="fas fa-exclamation-triangle me-1"></i> Endpoint Reached</span>');
                        $details.html('<span class="text-danger">' + (response.message || 'Credentials rejected by LinkedIn.') + '</span>');
                    }
                },
                error: function(xhr) {
                    $btn.removeAttr('data-kt-indicator').prop('disabled', false);
                    $status.html('<span class="badge badge-light-danger text-danger fw-bold"><i class="fas fa-times-circle me-1"></i> Failed</span>');
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Connection timed out or network error.';
                    $details.html('<span class="text-danger">' + msg + '</span>');
                }
            });
        }

        $('#run-diagnostic-btn').on('click', function(e) {
            e.preventDefault();
            runDiagnostic();
        });

        // Top button test connection triggers tab switch + run diagnostic
        $('#top-test-btn, #test-connection-btn').on('click', function(e) {
            e.preventDefault();
            switchLinkedInTab('#tab-diagnostics');
            setTimeout(function() {
                runDiagnostic();
            }, 250);
        });

    });
</script>
@endpush