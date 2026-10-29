@extends('layouts.superadmin_layout')

@php
    use App\Helper\GeneralMethod;
    $isConfigured = !empty($zoom_setting->client_id) && !empty($zoom_setting->redirect_uri);
    $isActive = (isset($zoom_setting->status) && (int)$zoom_setting->status === 1);
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
    .content {
        padding-top: 0 !important;
        margin-top: 0 !important;
    }
    .zoom-page-container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 16px 30px 16px !important;
        margin-top: 0 !important;
    }

    /* Tier 1: Top Toolbar Card */
    .zoom-toolbar-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        margin-bottom: 20px;
    }

    .zoom-brand-icon-box {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #009ef7 0%, #0077c5 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 22px;
        box-shadow: 0 4px 12px rgba(0, 158, 247, 0.25);
    }

    /* Tier 2: Sub-Tabs Card */
    .zoom-tabs-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 8px 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        margin-bottom: 20px;
    }

    .zoom-nav-pills .nav-link {
        font-size: 13px;
        font-weight: 600;
        color: #5e6278;
        padding: 10px 18px;
        border-radius: 8px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        border: 1px solid transparent;
    }

    .zoom-nav-pills .nav-link:hover {
        color: #009ef7;
        background: #f8fafc;
    }

    .zoom-nav-pills .nav-link.active {
        background-color: #009ef7 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(0, 158, 247, 0.28);
    }

    /* Tier 3: Modular Sub-Cards */
    .zoom-sub-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        margin-bottom: 20px;
        overflow: hidden;
    }

    .zoom-sub-card-header {
        padding: 16px 22px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #ffffff;
    }

    .zoom-sub-card-header h3 {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .zoom-sub-card-body {
        padding: 22px;
    }

    /* Form Inputs and Controls */
    .zoom-form-label {
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .zoom-form-control {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13px;
        color: #1e293b;
        padding: 9px 14px;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .zoom-form-control:focus {
        border-color: #009ef7;
        box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12);
        outline: none;
    }

    .input-group-text-action {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-left: none;
        cursor: pointer;
        color: #64748b;
        transition: all 0.2s ease;
        padding: 0 12px;
    }

    .input-group-text-action:hover {
        background: #f1f5f9;
        color: #009ef7;
    }

    /* Status Badges */
    .badge-status-active {
        background: #e8fff3;
        color: #50cd89;
        border: 1px solid #b7f5d0;
        font-size: 12px;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .badge-status-inactive {
        background: #fff8dd;
        color: #e49e00;
        border: 1px solid #ffe89e;
        font-size: 12px;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .badge-status-unconfigured {
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #cbd5e1;
        font-size: 12px;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* Diagnostic Console */
    .diagnostic-terminal {
        background: #0f172a;
        color: #38bdf8;
        border-radius: 8px;
        padding: 16px;
        font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
        font-size: 12px;
        line-height: 1.6;
        max-height: 280px;
        overflow-y: auto;
        border: 1px solid #1e293b;
    }

    .diagnostic-terminal .text-muted-custom {
        color: #64748b;
    }

    .diagnostic-terminal .text-success-custom {
        color: #4ade80;
    }

    .diagnostic-terminal .text-danger-custom {
        color: #f87171;
    }

    /* Metric Mini-Card */
    .metric-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 14px;
        text-align: center;
    }

    .metric-card-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .metric-card-value {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
    }

    /* Guide Checklist Item */
    .guide-step-number {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #e0f2fe;
        color: #0284c7;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 12px;
        flex-shrink: 0;
    }

    .scope-pill {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 600;
        color: #334155;
        font-family: monospace;
    }
</style>
@endpush

@section('content')
<div class="zoom-page-container container-fluid">

        <!-- ========================================== -->
        <!-- TIER 1: TOP HEADER TOOLBAR CARD            -->
        <!-- ========================================== -->
        <div class="zoom-toolbar-card d-flex flex-stack flex-wrap gap-4 align-items-center">
            <div class="d-flex align-items-center gap-4">
                <div class="zoom-brand-icon-box">
                    <i class="fas fa-video"></i>
                </div>
                <div>
                    <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-0">
                        Zoom Video Meeting Integration Hub
                    </h1>
                    <div class="text-muted fs-7 fw-semibold mt-1">
                        Configure global Server-to-Server OAuth credentials for candidate ATS interview scheduling
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div id="toolbar-status-badge">
                    @if(!$isConfigured)
                        <span class="badge-status-unconfigured">
                            <i class="fas fa-exclamation-circle"></i> Not Configured
                        </span>
                    @elseif($isActive)
                        <span class="badge-status-active">
                            <i class="fas fa-check-circle"></i> Active & Connected
                        </span>
                    @else
                        <span class="badge-status-inactive">
                            <i class="fas fa-pause-circle"></i> Integration Paused
                        </span>
                    @endif
                </div>

                <button type="button" class="btn btn-sm btn-light-primary fw-bold" id="top-test-btn">
                    <span class="indicator-label">
                        <i class="fas fa-bolt me-1"></i> Test Connection
                    </span>
                    <span class="indicator-progress">
                        Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                </button>

                <button type="button" class="btn btn-sm btn-primary fw-bold" id="top-save-btn">
                    <span class="indicator-label">
                        <i class="fas fa-save me-1"></i> Save Settings
                    </span>
                    <span class="indicator-progress">
                        Saving... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                </button>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TIER 2: SUB-TABS NAVIGATION CARD           -->
        <!-- ========================================== -->
        <div class="zoom-tabs-card">
            <ul class="nav zoom-nav-pills d-flex flex-wrap" id="zoomTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-credentials-nav" data-bs-toggle="tab" data-bs-target="#tab-credentials" type="button" role="tab">
                        <i class="fas fa-key"></i> API & Server-to-Server Credentials
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-diagnostic-nav" data-bs-toggle="tab" data-bs-target="#tab-diagnostic" type="button" role="tab">
                        <i class="fas fa-stethoscope"></i> Connection Diagnostic & Live Test
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-guide-nav" data-bs-toggle="tab" data-bs-target="#tab-guide" type="button" role="tab">
                        <i class="fas fa-book-open"></i> Zoom Marketplace Setup Guide & Scopes
                    </button>
                </li>
            </ul>
        </div>

        <!-- ========================================== -->
        <!-- TIER 3: CONFIGURATION STREAM (TAB CONTENT) -->
        <!-- ========================================== -->
        <div class="tab-content" id="zoomTabContent">

            <!-- ---------------------------------------- -->
            <!-- TAB 1: API & CREDENTIALS                 -->
            <!-- ---------------------------------------- -->
            <div class="tab-pane fade show active" id="tab-credentials" role="tabpanel">
                <form id="zoomSettingsForm" class="ajax-form">
                    @csrf
                    <input type="hidden" name="token_id" id="token_id" value="{{ @$zoom_setting->id ?? 1 }}">

                    <div class="row g-5">
                        <!-- Sub-Card A: Integration Profile & Status -->
                        <div class="col-lg-5 col-12">
                            <div class="zoom-sub-card h-100">
                                <div class="zoom-sub-card-header">
                                    <h3><i class="fas fa-sliders-h text-primary"></i> Integration Profile</h3>
                                    <span class="badge badge-light-primary fw-bold fs-8">Server-to-Server</span>
                                </div>
                                <div class="zoom-sub-card-body d-flex flex-column justify-content-between h-100">
                                    <div>
                                        <!-- App Title -->
                                        <div class="mb-5">
                                            <label class="zoom-form-label" for="title">
                                                <span>Application Title <span class="text-danger">*</span></span>
                                            </label>
                                            <input type="text" class="form-control zoom-form-control" id="title" name="title"
                                                value="{{ @$zoom_setting->title ?? 'JERO Official Zoom Integration' }}"
                                                placeholder="e.g. JERO Production Zoom Meeting" required>
                                            <div class="fs-8 text-muted mt-1">Identifier for this video integration profile across the platform.</div>
                                        </div>

                                        <!-- Status Toggle -->
                                        <div class="mb-5 p-4 rounded bg-light-secondary border border-gray-200">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <div class="fw-bold fs-7 text-dark">Zoom Meeting Scheduling Status</div>
                                                    <div class="fs-8 text-muted">Enable or pause Zoom video room creation for ATS interviews.</div>
                                                </div>
                                                <div class="form-check form-switch form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="checkbox" name="status" id="status" value="1"
                                                        {{ $isActive ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Architecture Notice Box -->
                                        <div class="p-4 rounded bg-light-info border border-info border-dashed">
                                            <div class="d-flex gap-3">
                                                <i class="fas fa-info-circle text-info fs-4 mt-1"></i>
                                                <div class="fs-8 text-dark">
                                                    <strong>Automated Background Scheduling:</strong> Using Zoom Server-to-Server OAuth, interview rooms, passwords, and calendar links are provisioned directly in the background without requiring individual recruiters to sign into Zoom.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-6 pt-4 border-top border-gray-100 text-muted fs-8">
                                        <i class="fas fa-server me-1"></i> Target Engine: <strong>JERO ATS Interview Schedules</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sub-Card B: Server-to-Server OAuth Credentials -->
                        <div class="col-lg-7 col-12">
                            <div class="zoom-sub-card">
                                <div class="zoom-sub-card-header">
                                    <h3><i class="fas fa-shield-alt text-primary"></i> Server-to-Server OAuth Credentials</h3>
                                    <a href="https://marketplace.zoom.us/user/build" target="_blank" class="btn btn-sm btn-link text-primary fw-bold p-0">
                                        Zoom Marketplace <i class="fas fa-external-link-alt fs-9 ms-1"></i>
                                    </a>
                                </div>
                                <div class="zoom-sub-card-body">
                                    <!-- Account ID (Stored in DB column: redirect_uri) -->
                                    <div class="mb-5">
                                        <label class="zoom-form-label" for="redirect_uri">
                                            <span>Zoom Account ID <span class="text-danger">*</span></span>
                                            <span class="text-muted fs-8">Zoom Marketplace &rarr; App Credentials</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="text" class="form-control zoom-form-control" id="redirect_uri" name="redirect_uri"
                                                value="{{ @$zoom_setting->redirect_uri ?? '' }}"
                                                placeholder="e.g. 5PdHLPvqR0SY77pwiruTnA" required autocomplete="off">
                                            <button class="input-group-text input-group-text-action copy-btn" type="button" data-target="#redirect_uri" title="Copy Account ID">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                        <div class="fs-8 text-muted mt-1">
                                            Found in your Zoom App Marketplace Server-to-Server OAuth app credentials.
                                        </div>
                                    </div>

                                    <!-- Client ID -->
                                    <div class="mb-5">
                                        <label class="zoom-form-label" for="client_id">
                                            <span>Client ID <span class="text-danger">*</span></span>
                                            <span class="text-muted fs-8">Public App Identifier</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="text" class="form-control zoom-form-control" id="client_id" name="client_id"
                                                value="{{ @$zoom_setting->client_id ?? '' }}"
                                                placeholder="e.g. ma_AqFVMQLyelwJ2hhaKlg" required autocomplete="off">
                                            <button class="input-group-text input-group-text-action copy-btn" type="button" data-target="#client_id" title="Copy Client ID">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                        <div class="fs-8 text-muted mt-1">
                                            Assigned client identifier generated by Zoom Marketplace.
                                        </div>
                                    </div>

                                    <!-- Client Secret -->
                                    <div class="mb-4">
                                        <label class="zoom-form-label" for="client_secret">
                                            <span>Client Secret <span class="text-danger">*</span></span>
                                            <span class="text-muted fs-8">Confidential Token Secret</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="password" class="form-control zoom-form-control" id="client_secret" name="client_secret"
                                                value="{{ @$zoom_setting->client_secret ?? '' }}"
                                                placeholder="Enter Zoom Client Secret" required autocomplete="new-password">
                                            <button class="input-group-text input-group-text-action toggle-password" type="button" data-target="#client_secret" title="Show / Hide Secret">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="input-group-text input-group-text-action copy-btn" type="button" data-target="#client_secret" title="Copy Secret">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                        <div class="fs-8 text-muted mt-1">
                                            Protected credential. Keep confidential; never expose in clear text.
                                        </div>
                                    </div>

                                    <!-- Action Strip inside Sub-Card B -->
                                    <div class="d-flex align-items-center justify-content-between pt-4 mt-4 border-top border-gray-100">
                                        <button type="button" class="btn btn-sm btn-light-primary fw-bold" id="card-test-btn">
                                            <span class="indicator-label">
                                                <i class="fas fa-bolt me-1"></i> Test This Configuration
                                            </span>
                                            <span class="indicator-progress">
                                                Testing... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                            </span>
                                        </button>

                                        <div class="d-flex gap-2">
                                            <button type="reset" class="btn btn-sm btn-light fw-bold">
                                                <i class="fas fa-undo me-1"></i> Reset
                                            </button>
                                            <button type="button" class="btn btn-sm btn-primary fw-bold" id="save-form">
                                                <span class="indicator-label">
                                                    <i class="fas fa-save me-1"></i> Save Changes
                                                </span>
                                                <span class="indicator-progress">
                                                    Saving... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- ---------------------------------------- -->
            <!-- TAB 2: DIAGNOSTIC & LIVE TEST CONSOLE    -->
            <!-- ---------------------------------------- -->
            <div class="tab-pane fade" id="tab-diagnostic" role="tabpanel">
                <div class="row g-5">
                    <!-- Diagnostic Controller Sub-Card -->
                    <div class="col-lg-5 col-12">
                        <div class="zoom-sub-card h-100">
                            <div class="zoom-sub-card-header">
                                <h3><i class="fas fa-tachometer-alt text-primary"></i> Handshake Diagnostic Test</h3>
                            </div>
                            <div class="zoom-sub-card-body">
                                <p class="text-muted fs-7 mb-4">
                                    Execute a real-time OAuth token exchange directly with Zoom's authorization servers. Verifies basic authentication credentials, account routing, and API endpoint readiness.
                                </p>

                                <div class="d-grid gap-2 mb-5">
                                    <button type="button" class="btn btn-primary fw-bold py-3" id="run-diagnostic-btn">
                                        <span class="indicator-label">
                                            <i class="fas fa-play-circle me-2"></i> Run Live Diagnostic Test
                                        </span>
                                        <span class="indicator-progress">
                                            Contacting Zoom API... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                        </span>
                                    </button>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-6">
                                        <div class="metric-card">
                                            <div class="metric-card-label">Handshake Status</div>
                                            <div class="metric-card-value fs-6" id="metric-status">
                                                <span class="text-muted">Idle</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="metric-card">
                                            <div class="metric-card-label">Round-Trip Latency</div>
                                            <div class="metric-card-value fs-6" id="metric-latency">
                                                <span class="text-muted">-- ms</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="metric-card">
                                            <div class="metric-card-label">Account License</div>
                                            <div class="metric-card-value fs-7" id="metric-license">
                                                <span class="text-muted">--</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="metric-card">
                                            <div class="metric-card-label">Token Validity</div>
                                            <div class="metric-card-value fs-7" id="metric-token">
                                                <span class="text-muted">--</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="fs-8 text-muted">
                                    <i class="fas fa-clock me-1"></i> Tests are executed on-demand and do not store tokens in the database.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Diagnostic Terminal & Output Console -->
                    <div class="col-lg-7 col-12">
                        <div class="zoom-sub-card h-100">
                            <div class="zoom-sub-card-header">
                                <h3><i class="fas fa-terminal text-primary"></i> Real-Time Handshake Console</h3>
                                <button type="button" class="btn btn-sm btn-light copy-btn" data-target="#diagnostic-output" title="Copy Console Output">
                                    <i class="fas fa-copy me-1"></i> Copy Log
                                </button>
                            </div>
                            <div class="zoom-sub-card-body">
                                <div class="diagnostic-terminal" id="diagnostic-output">
                                    <span class="text-muted-custom">// JERO Zoom Diagnostic Protocol Console</span><br>
                                    <span class="text-muted-custom">// Ready for handshake test...</span><br>
                                    <span class="text-muted-custom">// Click "Run Live Diagnostic Test" to begin.</span>
                                </div>

                                <div class="mt-4 p-3 rounded bg-light border border-gray-200 fs-8 text-muted">
                                    <strong>OAuth Endpoint:</strong> <code>https://zoom.us/oauth/token</code> (grant_type: account_credentials)<br>
                                    <strong>User Endpoint:</strong> <code>https://api.zoom.us/v2/users/me</code> (GET)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ---------------------------------------- -->
            <!-- TAB 3: ZOOM MARKETPLACE SETUP GUIDE      -->
            <!-- ---------------------------------------- -->
            <div class="tab-pane fade" id="tab-guide" role="tabpanel">
                <div class="zoom-sub-card">
                    <div class="zoom-sub-card-header">
                        <h3><i class="fas fa-graduation-cap text-primary"></i> Zoom App Marketplace Configuration Walkthrough</h3>
                        <a href="https://marketplace.zoom.us" target="_blank" class="btn btn-sm btn-primary fw-bold">
                            Open Zoom Marketplace <i class="fas fa-external-link-alt ms-1 fs-9"></i>
                        </a>
                    </div>
                    <div class="zoom-sub-card-body">
                        <div class="row g-5">
                            <div class="col-lg-6 col-12">
                                <h4 class="fs-6 fw-bold text-dark mb-4">5-Step Server-to-Server App Setup</h4>

                                <div class="d-flex gap-3 mb-4">
                                    <div class="guide-step-number">1</div>
                                    <div>
                                        <div class="fw-bold fs-7 text-dark">Sign In & Build App</div>
                                        <div class="fs-8 text-muted">Sign in to <a href="https://marketplace.zoom.us" target="_blank">marketplace.zoom.us</a> using your Zoom account administrator credentials. Click <strong>Develop &rarr; Build App</strong>.</div>
                                    </div>
                                </div>

                                <div class="d-flex gap-3 mb-4">
                                    <div class="guide-step-number">2</div>
                                    <div>
                                        <div class="fw-bold fs-7 text-dark">Select "Server-to-Server OAuth"</div>
                                        <div class="fs-8 text-muted">Choose <strong>Server-to-Server OAuth</strong> as the app type and give your application a name (e.g. "JERO ATS Integration").</div>
                                    </div>
                                </div>

                                <div class="d-flex gap-3 mb-4">
                                    <div class="guide-step-number">3</div>
                                    <div>
                                        <div class="fw-bold fs-7 text-dark">Copy App Credentials</div>
                                        <div class="fs-8 text-muted">In the <strong>App Credentials</strong> section, copy the <strong>Account ID</strong>, <strong>Client ID</strong>, and <strong>Client Secret</strong> into the fields in Tab 1.</div>
                                    </div>
                                </div>

                                <div class="d-flex gap-3 mb-4">
                                    <div class="guide-step-number">4</div>
                                    <div>
                                        <div class="fw-bold fs-7 text-dark">Add Mandatory Scopes</div>
                                        <div class="fs-8 text-muted">Navigate to the <strong>Scopes</strong> tab and add the permissions listed in the checklist on the right.</div>
                                    </div>
                                </div>

                                <div class="d-flex gap-3">
                                    <div class="guide-step-number">5</div>
                                    <div>
                                        <div class="fw-bold fs-7 text-dark">Activate Application</div>
                                        <div class="fs-8 text-muted">Click <strong>Continue</strong> and activate the app. An unactivated app will be rejected by Zoom during authentication.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-12">
                                <h4 class="fs-6 fw-bold text-dark mb-4">Mandatory Scopes Checklist</h4>

                                <div class="p-4 rounded bg-light-secondary border border-gray-200 mb-4">
                                    <div class="mb-3 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-check-circle text-success fs-6"></i>
                                            <span class="scope-pill">meeting:write:admin</span>
                                        </div>
                                        <span class="badge badge-light-success fw-bold fs-9">Required</span>
                                    </div>
                                    <div class="fs-8 text-muted ms-6">
                                        Allows the ATS system to automatically schedule interview meetings, assign passwords, and generate unique candidate join links.
                                    </div>
                                </div>

                                <div class="p-4 rounded bg-light-secondary border border-gray-200 mb-4">
                                    <div class="mb-3 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-check-circle text-success fs-6"></i>
                                            <span class="scope-pill">meeting:read:admin</span>
                                        </div>
                                        <span class="badge badge-light-success fw-bold fs-9">Required</span>
                                    </div>
                                    <div class="fs-8 text-muted ms-6">
                                        Allows fetching meeting status, start time verification, and cloud recording playback links.
                                    </div>
                                </div>

                                <div class="p-4 rounded bg-light-secondary border border-gray-200 mb-4">
                                    <div class="mb-3 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-check-circle text-success fs-6"></i>
                                            <span class="scope-pill">user:read:admin</span>
                                        </div>
                                        <span class="badge badge-light-primary fw-bold fs-9">Recommended</span>
                                    </div>
                                    <div class="fs-8 text-muted ms-6">
                                        Allows verifying host user email address and account license type during handshake tests.
                                    </div>
                                </div>

                                <div class="p-4 rounded bg-light-warning border border-warning border-dashed">
                                    <div class="d-flex gap-2">
                                        <i class="fas fa-exclamation-triangle text-warning fs-5 mt-1"></i>
                                        <div class="fs-8 text-dark">
                                            <strong>Important Note on JWT Deprecation:</strong> Zoom officially retired legacy JWT apps on June 1, 2023. Server-to-Server OAuth is the only approved and supported method for automated interview scheduling.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- end Tab Content -->

</div>
@endsection

@push('footer-script')
<script>
    $(document).ready(function() {

        // Auto-trim whitespace on blur
        $('#title, #redirect_uri, #client_id, #client_secret').on('blur', function() {
            $(this).val($.trim($(this).val()));
        });

        // 1. Password Visibility Eye Toggle
        $(document).on('click', '.toggle-password', function() {
            var targetSelector = $(this).data('target');
            var $target = $(targetSelector);
            var $icon = $(this).find('i');

            if ($target.attr('type') === 'password') {
                $target.attr('type', 'text');
                $icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                $target.attr('type', 'password');
                $icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // 2. Clipboard Copy with Visual Feedback
        $(document).on('click', '.copy-btn', function() {
            var targetSelector = $(this).data('target');
            var textToCopy = '';

            if ($(targetSelector).is('input, textarea')) {
                textToCopy = $(targetSelector).val();
            } else {
                textToCopy = $(targetSelector).text();
            }

            if (!textToCopy) {
                Swal.fire({
                    text: "Nothing to copy (field is empty).",
                    icon: "info",
                    buttonsStyling: false,
                    confirmButtonText: "Ok",
                    customClass: { confirmButton: "btn btn-primary" }
                });
                return;
            }

            var $btn = $(this);
            var originalHtml = $btn.html();

            navigator.clipboard.writeText(textToCopy).then(function() {
                $btn.html('<i class="fas fa-check text-success"></i>');
                setTimeout(function() {
                    $btn.html(originalHtml);
                }, 1800);
            }).catch(function() {
                // Fallback for older browsers
                var $temp = $('<textarea>');
                $('body').append($temp);
                $temp.val(textToCopy).select();
                document.execCommand('copy');
                $temp.remove();
                $btn.html('<i class="fas fa-check text-success"></i>');
                setTimeout(function() {
                    $btn.html(originalHtml);
                }, 1800);
            });
        });

        // 3. Dynamic Status Badge Live Preview
        $('#status').on('change', function() {
            var isChecked = $(this).is(':checked');
            var hasCredentials = $.trim($('#client_id').val()) && $.trim($('#redirect_uri').val());
            if (!hasCredentials) {
                $('#toolbar-status-badge').html('<span class="badge-status-unconfigured"><i class="fas fa-exclamation-circle"></i> Not Configured</span>');
            } else if (isChecked) {
                $('#toolbar-status-badge').html('<span class="badge-status-active"><i class="fas fa-check-circle"></i> Active & Connected</span>');
            } else {
                $('#toolbar-status-badge').html('<span class="badge-status-inactive"><i class="fas fa-pause-circle"></i> Integration Paused</span>');
            }
        });

        // 4. Save Settings Handler (Form Submit)
        function handleSaveSettings($triggerBtn) {
            var $form = $('#zoomSettingsForm');
            var $allSaveButtons = $('#save-form, #top-save-btn');

            $allSaveButtons.attr('data-kt-indicator', 'on').prop('disabled', true);

            var formData = new FormData($form[0]);

            // Ensure status value is sent if unchecked
            if (!$('#status').is(':checked')) {
                formData.set('status', '0');
            }

            $.ajax({
                url: '{{ route('superadmin.company.settingZoomStore') }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $allSaveButtons.removeAttr('data-kt-indicator').prop('disabled', false);

                    if (response.status === 'success') {
                        // Dynamically update the toolbar badge
                        var isChecked = $('#status').is(':checked');
                        var badgeHtml = isChecked ?
                            '<span class="badge-status-active"><i class="fas fa-check-circle"></i> Active & Connected</span>' :
                            '<span class="badge-status-inactive"><i class="fas fa-pause-circle"></i> Integration Paused</span>';
                        $('#toolbar-status-badge').html(badgeHtml);

                        Swal.fire({
                            text: response.message || "Zoom settings updated successfully.",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: { confirmButton: "btn btn-primary" }
                        });
                    } else {
                        Swal.fire({
                            text: response.message || "Failed to update settings.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: { confirmButton: "btn btn-primary" }
                        });
                    }
                },
                error: function(xhr) {
                    $allSaveButtons.removeAttr('data-kt-indicator').prop('disabled', false);

                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        var errorList = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        Swal.fire({
                            html: '<p class="fw-bold mb-2">Please correct the following errors:</p><div style="color: #f1416c; text-align: left;">' + errorList + '</div>',
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: { confirmButton: "btn btn-primary" }
                        });
                    } else {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "An unexpected server error occurred.";
                        Swal.fire({
                            text: msg,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: { confirmButton: "btn btn-primary" }
                        });
                    }
                }
            });
        }

        $('#save-form, #top-save-btn').on('click', function(e) {
            e.preventDefault();
            handleSaveSettings($(this));
        });

        // 5. Test Connection Diagnostic Handler with Auto-Switch to Terminal
        function handleTestConnection($btn) {
            // Automatically switch to Diagnostic Tab so administrator sees live execution
            var diagnosticTabTrigger = document.querySelector('#tab-diagnostic-nav');
            if (diagnosticTabTrigger && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                var tab = new bootstrap.Tab(diagnosticTabTrigger);
                tab.show();
            } else {
                $('#tab-diagnostic-nav').tab('show');
            }

            var $allTestButtons = $('#top-test-btn, #card-test-btn, #run-diagnostic-btn');
            $allTestButtons.attr('data-kt-indicator', 'on').prop('disabled', true);

            var redirectUri = $.trim($('#redirect_uri').val());
            var clientId = $.trim($('#client_id').val());
            var clientSecret = $.trim($('#client_secret').val());

            var $terminal = $('#diagnostic-output');
            var nowTime = new Date().toLocaleTimeString();

            $terminal.html(
                '<span class="text-muted-custom">[' + nowTime + '] Initializing handshake with Zoom OAuth...</span><br>' +
                '<span class="text-muted-custom">POST https://zoom.us/oauth/token (grant_type=account_credentials)</span><br>' +
                '<span class="text-muted-custom">Account ID: ' + (redirectUri ? redirectUri : '(stored in DB)') + '</span><br>' +
                '<span class="text-muted-custom">Client ID : ' + (clientId ? clientId : '(stored in DB)') + '</span><br>'
            );

            // Reset metric cards
            $('#metric-status').html('<span class="text-primary">Testing...</span>');
            $('#metric-latency').html('<span class="text-primary">--</span>');
            $('#metric-license').html('<span class="text-muted">--</span>');
            $('#metric-token').html('<span class="text-muted">--</span>');

            $.ajax({
                url: '{{ route('superadmin.company.zoomTestConnection') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    redirect_uri: redirectUri,
                    client_id: clientId,
                    client_secret: clientSecret
                },
                success: function(response) {
                    $allTestButtons.removeAttr('data-kt-indicator').prop('disabled', false);

                    var latencyMs = response.latency_ms || 0;
                    var latencyBadge = latencyMs + ' ms';

                    $('#metric-status').html('<span class="text-success-custom fw-bold"><i class="fas fa-check-circle text-success me-1"></i> Verified</span>');
                    $('#metric-latency').html('<span class="text-success-custom fw-bold">' + latencyBadge + '</span>');
                    $('#metric-license').html('<span class="text-dark fw-bold">' + (response.account_type || 'Pro License') + '</span>');
                    $('#metric-token').html('<span class="text-dark fw-bold">' + (response.expires_in || '3600') + 's</span>');

                    var scopesStr = Array.isArray(response.scopes) ? response.scopes.join(', ') : response.scopes;

                    $terminal.append(
                        '<br><span class="text-success-custom">>>> [SUCCESS 200] OAuth Handshake Verified!</span><br>' +
                        '<span class="text-muted-custom">Latency      : ' + latencyMs + ' ms</span><br>' +
                        '<span class="text-muted-custom">Account User : ' + (response.account_email || 'Verified') + '</span><br>' +
                        '<span class="text-muted-custom">License Type : ' + (response.account_type || 'Pro') + '</span><br>' +
                        '<span class="text-muted-custom">Scopes       : ' + scopesStr + '</span><br>' +
                        '<span class="text-success-custom">>>> Zoom ATS meeting scheduling engine is fully operational.</span>'
                    );

                    $terminal.scrollTop($terminal[0].scrollHeight);

                    Swal.fire({
                        html: '<div class="text-start">' +
                              '<div class="d-flex align-items-center mb-3"><i class="fas fa-check-circle text-success fs-2x me-3"></i><h4 class="mb-0">Zoom Connection Verified!</h4></div>' +
                              '<p class="text-muted fs-7 mb-2">OAuth token exchange completed successfully in <strong>' + latencyMs + ' ms</strong>.</p>' +
                              '<div class="p-3 bg-light rounded fs-8 text-dark mb-0">' +
                              '<strong>Connected Account:</strong> ' + (response.account_email || 'Server-to-Server') + '<br>' +
                              '<strong>License Type:</strong> ' + (response.account_type || 'Pro / Licensed') + '<br>' +
                              '<strong>Token Lifetime:</strong> ' + (response.expires_in || 3600) + ' seconds' +
                              '</div>' +
                              '</div>',
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Great, thanks!",
                        customClass: { confirmButton: "btn btn-primary" }
                    });
                },
                error: function(xhr) {
                    $allTestButtons.removeAttr('data-kt-indicator').prop('disabled', false);

                    var resp = xhr.responseJSON || {};
                    var latencyMs = resp.latency_ms || 0;
                    var errorMsg = resp.message || "Failed to authenticate with Zoom.";

                    $('#metric-status').html('<span class="text-danger-custom fw-bold"><i class="fas fa-times-circle text-danger me-1"></i> Failed</span>');
                    $('#metric-latency').html('<span class="text-danger-custom fw-bold">' + latencyMs + ' ms</span>');
                    $('#metric-license').html('<span class="text-danger-custom">Error</span>');
                    $('#metric-token').html('<span class="text-danger-custom">Denied</span>');

                    $terminal.append(
                        '<br><span class="text-danger-custom">>>> [ERROR] Handshake Rejected by Zoom</span><br>' +
                        '<span class="text-danger-custom">Error Code: ' + (resp.error_code || xhr.status) + '</span><br>' +
                        '<span class="text-danger-custom">Message   : ' + errorMsg + '</span><br>' +
                        (resp.raw_error ? '<span class="text-muted-custom">Raw: ' + JSON.stringify(resp.raw_error) + '</span><br>' : '') +
                        '<span class="text-muted-custom">>>> Please check Tab 3 for setup instructions.</span>'
                    );

                    $terminal.scrollTop($terminal[0].scrollHeight);

                    Swal.fire({
                        html: '<div class="text-start">' +
                              '<div class="d-flex align-items-center mb-3"><i class="fas fa-times-circle text-danger fs-2x me-3"></i><h4 class="mb-0">Zoom Handshake Failed</h4></div>' +
                              '<div class="alert alert-danger p-3 fs-8 mb-3">' + errorMsg + '</div>' +
                              '<p class="fs-8 text-muted mb-0">Please verify your Account ID, Client ID, and Client Secret in Zoom Marketplace. Also ensure the app is activated.</p>' +
                              '</div>',
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it",
                        customClass: { confirmButton: "btn btn-primary" }
                    });
                }
            });
        }

        $('#top-test-btn, #card-test-btn, #run-diagnostic-btn').on('click', function(e) {
            e.preventDefault();
            handleTestConnection($(this));
        });

    });
</script>
@endpush
