@extends('layouts.superadmin_layout')

@section('content')
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

    /* ─── Scoped Payment Settings Styles (100% Page Isolated) ─── */
    .payment-settings-container {
        width: 100%;
        max-width: 100%;
        padding: 0 16px 24px 16px !important;
        margin-top: 0 !important;
        box-sizing: border-box;
    }

    /* Tier 1: Top Header Toolbar Card */
    .ps-header-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 14px 20px !important;
        margin-bottom: 12px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
    }

    .ps-brand-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: #eff6ff;
        color: #009ef7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
        border: 1px solid #dbeafe;
    }

    .ps-title {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.25;
        margin-bottom: 2px;
    }

    .ps-subtitle {
        font-size: 11.5px;
        color: #64748b;
        margin-bottom: 0;
    }

    .ps-metric-pill {
        display: inline-flex;
        align-items: center;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        line-height: 1.2;
    }

    .ps-metric-pill.success {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .ps-metric-pill.info {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #dbeafe;
    }

    .ps-metric-pill.warning {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .ps-search-subcard {
        position: relative;
        height: 38px;
        width: 290px;
        max-width: 100%;
        display: flex;
        align-items: center;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0 10px;
        transition: all 0.2s ease;
    }

    .ps-search-subcard:focus-within {
        background: #ffffff;
        border-color: #009ef7;
        box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12);
    }

    .ps-search-icon {
        color: #94a3b8;
        font-size: 13px;
        margin-right: 8px;
        flex-shrink: 0;
    }

    .ps-search-input {
        border: none;
        background: transparent;
        outline: none;
        font-size: 12px;
        color: #0f172a;
        width: 100%;
        font-family: inherit;
    }

    .ps-search-input::placeholder {
        color: #94a3b8;
    }

    .ps-search-clear {
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        font-size: 13px;
        padding: 2px 6px;
        line-height: 1;
        transition: color 0.15s;
    }

    .ps-search-clear:hover {
        color: #0f172a;
    }

    /* Tier 2: Sub-Tabs Navigation Card */
    .ps-tabs-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 8px 12px !important;
        margin-bottom: 14px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .ps-tab-btn {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        padding: 7px 16px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        color: #475569 !important;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02) !important;
    }

    .ps-tab-btn:hover {
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }

    /* Rule #20: Strict Active Highlight */
    .ps-tab-btn.active {
        background: #009ef7 !important;
        border-color: #009ef7 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(0, 158, 247, 0.28) !important;
    }

    .ps-tab-badge {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 12px;
        line-height: 1.2;
    }

    .ps-tab-btn.active .ps-tab-badge {
        background: rgba(255, 255, 255, 0.25) !important;
        color: #ffffff !important;
    }

    .ps-tab-btn:not(.active) .ps-tab-badge {
        background: #f1f5f9;
        color: #475569;
    }

    .ps-tab-badge.badge-success {
        background: #dcfce7 !important;
        color: #15803d !important;
    }

    .ps-tab-badge.badge-secondary {
        background: #f1f5f9 !important;
        color: #64748b !important;
    }

    /* Tier 3: Primary Gateway Card Stream */
    .ps-gateway-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        margin-bottom: 16px !important;
        box-shadow: 0 1px 4px rgba(15, 23, 42, 0.04) !important;
        transition: border-color 0.2s, box-shadow 0.2s;
        overflow: hidden;
    }

    .ps-gateway-card:hover {
        border-color: #cbd5e1 !important;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06) !important;
    }

    /* Sub-Card A: Provider Header Strip */
    .ps-subcard-header {
        padding: 14px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        background: #ffffff;
    }

    .ps-gateway-identity {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .ps-gateway-logo {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
        border: 1px solid #e2e8f0;
    }

    .ps-gateway-logo.paypal {
        background: #003087;
        color: #ffffff;
        border-color: #00266e;
    }

    .ps-gateway-logo.stripe {
        background: #635bff;
        color: #ffffff;
        border-color: #5348e8;
    }

    .ps-gateway-logo.razorpay {
        background: #0c2340;
        color: #528ff0;
        border-color: #08172c;
    }

    .ps-gateway-title {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 2px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .ps-gateway-desc {
        font-size: 11.5px;
        color: #64748b;
        margin-bottom: 0;
    }

    .ps-header-controls {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    /* Sub-Card B: Credentials Form Grid */
    .ps-subcard-credentials {
        padding: 18px 20px;
        background: #ffffff;
    }

    .ps-field-label {
        font-size: 11.5px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .ps-input-group {
        position: relative;
        display: flex;
        align-items: stretch;
        width: 100%;
    }

    .ps-input-group .form-control,
    .ps-input-group .form-select {
        height: 38px;
        font-size: 12px;
        color: #0f172a;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0 12px;
        transition: all 0.2s ease;
    }

    .ps-input-group .form-control:focus,
    .ps-input-group .form-select:focus {
        background: #ffffff;
        border-color: #009ef7;
        box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12);
    }

    .ps-input-group .btn-action {
        border: 1px solid #e2e8f0;
        background: #f1f5f9;
        color: #64748b;
        padding: 0 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        transition: all 0.15s;
    }

    .ps-input-group .btn-action:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .ps-input-group .form-control:not(:last-child) {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .ps-input-group .btn-action:not(:first-child) {
        border-left: none;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }

    .ps-input-group .btn-action + .btn-action {
        border-radius: 0;
    }

    .ps-input-group .btn-action:last-child {
        border-top-right-radius: 8px;
        border-bottom-right-radius: 8px;
    }

    /* Sub-Card C: Webhook Integration Strip */
    .ps-subcard-webhook {
        padding: 14px 20px;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        border-bottom: 1px solid #f1f5f9;
    }

    .ps-webhook-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #475569;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .ps-webhook-input {
        background: #ffffff !important;
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace !important;
        font-size: 11.5px !important;
        color: #0f172a !important;
        font-weight: 500 !important;
    }

    .ps-webhook-note {
        font-size: 11px;
        color: #64748b;
        margin-top: 6px;
        margin-bottom: 0;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Sub-Card D: Bottom Activity & Audit Strip */
    .ps-subcard-footer {
        padding: 10px 20px;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        font-size: 11px;
        color: #64748b;
    }

    .ps-footer-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* Unified Webhook Hub Card */
    .ps-webhook-hub-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 20px !important;
        box-shadow: 0 1px 4px rgba(15, 23, 42, 0.04) !important;
        margin-bottom: 16px;
    }

    .ps-hub-row {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 14px 16px;
        margin-bottom: 12px;
        transition: all 0.2s ease;
    }

    .ps-hub-row:hover {
        background: #ffffff;
        border-color: #cbd5e1;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
    }

    /* Metronic Native Switch Alignment */
    .form-check.form-switch.form-check-custom {
        padding-left: 0 !important;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 0;
    }

    .form-check.form-switch .form-check-input {
        cursor: pointer;
        height: 22px;
        width: 40px;
        background-color: #cbd5e1;
        border-color: #cbd5e1;
        transition: background-color 0.2s, border-color 0.2s;
    }

    .form-check.form-switch .form-check-input:checked {
        background-color: #10b981 !important;
        border-color: #10b981 !important;
    }

    .ps-credentials-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 14px;
    }

    .ps-collapsed {
        display: none !important;
    }

    /* 15.6" Screen & Mobile Viewport Refinements */
    @media (max-width: 991.98px) {
        .ps-header-card {
            flex-direction: column;
            align-items: flex-start !important;
        }
        .ps-search-subcard {
            width: 100% !important;
        }
    }

    @media (max-width: 767.98px) {
        .ps-header-card {
            padding: 12px 14px !important;
        }
        .ps-tabs-card {
            overflow-x: auto !important;
            flex-wrap: nowrap !important;
            padding-bottom: 8px !important;
            -webkit-overflow-scrolling: touch;
        }
        .ps-tab-btn {
            white-space: nowrap !important;
            flex-shrink: 0 !important;
        }
        .ps-credentials-grid {
            grid-template-columns: 1fr !important;
        }
        .ps-subcard-header {
            flex-direction: column;
            align-items: flex-start !important;
        }
        .ps-header-controls {
            width: 100%;
            justify-content: space-between;
        }
    }
</style>

<div class="payment-settings-container">
    <form id="editPaymentSettings" class="ajax-form" method="POST">
        @csrf
        @method('PUT')

        <!-- ══════════════════════════════════════════════════════════════════════════════
             TIER 1: TOP HEADER TOOLBAR CARD (Pure White #ffffff, Border #e2e8f0, Radius 10px)
             ══════════════════════════════════════════════════════════════════════════════ -->
        <div class="ps-header-card">
            <div class="d-flex align-items-center gap-3">
                <div class="ps-brand-icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div>
                    <h1 class="ps-title">Payment Gateways &amp; Subscription Billing Hub</h1>
                    <p class="ps-subtitle">Centralized payment processors, automated webhooks, API keys, and sandbox/production environments for Super Admin &amp; Tenants</p>
                </div>
            </div>

            <!-- Dynamic Live Metrics & Controls -->
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="ps-metric-pill info">
                    <i class="fas fa-shield-alt me-1"></i> 3 Supported Gateways
                </span>
                <span class="ps-metric-pill success" id="metric_active_count">
                    <i class="fas fa-check-circle me-1"></i> <span id="active_counter_val">{{ $activeGatewaysCount ?? 0 }}</span> Active
                </span>
                <span class="ps-metric-pill warning" id="metric_inactive_count">
                    <i class="fas fa-pause-circle me-1"></i> <span id="inactive_counter_val">{{ $inactiveGatewaysCount ?? 0 }}</span> Inactive
                </span>

                <!-- 300ms Debounced Search Sub-Card -->
                <div class="ps-search-subcard ms-lg-2">
                    <i class="fas fa-search ps-search-icon"></i>
                    <input type="text" id="ps_gateway_search" class="ps-search-input" placeholder="Search gateways, keys, webhooks..." autocomplete="off">
                    <button type="button" id="ps_search_clear" class="ps-search-clear d-none">✕</button>
                </div>

                <!-- Primary Action Button -->
                <button type="button" class="btn btn-sm btn-primary px-4 fw-bold" id="save-form">
                    <span class="indicator-label">
                        <i class="fas fa-save me-1"></i> Save Payment Settings
                    </span>
                    <span class="indicator-progress">
                        Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                </button>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════════════════════
             TIER 2: GATEWAY SELECTOR SUB-TABS CARD (Pure White #ffffff, Border #e2e8f0, Radius 10px)
             ══════════════════════════════════════════════════════════════════════════════ -->
        <div class="ps-tabs-card">
            <button type="button" class="ps-tab-btn active" data-filter="all">
                <i class="fas fa-layer-group"></i> All Gateways
                <span class="ps-tab-badge">3</span>
            </button>
            <button type="button" class="ps-tab-btn" data-filter="paypal">
                <i class="fab fa-paypal text-primary"></i> PayPal
                <span class="ps-tab-badge {{ $credentials->paypal_status == 'active' ? 'badge-success' : 'badge-secondary' }}" id="badge_tab_paypal">
                    {{ $credentials->paypal_status == 'active' ? 'Active' : 'Inactive' }}
                </span>
            </button>
            <button type="button" class="ps-tab-btn" data-filter="stripe">
                <i class="fab fa-stripe-s text-info"></i> Stripe
                <span class="ps-tab-badge {{ $credentials->stripe_status == 'active' ? 'badge-success' : 'badge-secondary' }}" id="badge_tab_stripe">
                    {{ $credentials->stripe_status == 'active' ? 'Active' : 'Inactive' }}
                </span>
            </button>
            <button type="button" class="ps-tab-btn" data-filter="razorpay">
                <i class="fas fa-bolt text-warning"></i> Razorpay
                <span class="ps-tab-badge {{ $credentials->razorpay_status == 'active' ? 'badge-success' : 'badge-secondary' }}" id="badge_tab_razorpay">
                    {{ $credentials->razorpay_status == 'active' ? 'Active' : 'Inactive' }}
                </span>
            </button>
            <button type="button" class="ps-tab-btn" data-filter="webhooks">
                <i class="fas fa-link"></i> Webhook Endpoints Hub
                <span class="ps-tab-badge">3 URLs</span>
            </button>
        </div>

        <!-- ══════════════════════════════════════════════════════════════════════════════
             TIER 3: PRIMARY GATEWAY CARDS STREAM
             ══════════════════════════════════════════════════════════════════════════════ -->
        <div class="ps-card-stream">

            <!-- ────────────────────────────────────────────────────────────────────────
                 GATEWAY 1: PAYPAL BILLING GATEWAY
                 ──────────────────────────────────────────────────────────────────────── -->
            <div class="ps-gateway-card" id="gateway_card_paypal" data-gateway="paypal">
                <!-- Sub-Card A: Provider Identity Strip -->
                <div class="ps-subcard-header">
                    <div class="ps-gateway-identity">
                        <div class="ps-gateway-logo paypal">
                            <i class="fab fa-paypal"></i>
                        </div>
                        <div>
                            <div class="ps-gateway-title">
                                PayPal Subscription &amp; Express Checkout
                                <span class="badge badge-light-{{ $credentials->paypal_mode == 'live' ? 'danger' : 'primary' }} fs-8 fw-bolder" id="paypal_mode_badge">
                                    {{ strtoupper($credentials->paypal_mode ?? 'SANDBOX') }}
                                </span>
                            </div>
                            <p class="ps-gateway-desc">Accept international card payments, PayPal wallet balances, and automated recurring subscription agreements</p>
                        </div>
                    </div>

                    <div class="ps-header-controls">
                        <!-- Diagnostic Test Button -->
                        <button type="button" class="btn btn-sm btn-light-primary btn-test-gateway" data-gateway="paypal">
                            <i class="fas fa-plug me-1"></i> Test Connection
                        </button>

                        <!-- Native Metronic Switch -->
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input gateway-toggle" type="checkbox" id="paypal_status" name="paypal_status" value="active" data-target="#paypal_credentials_body" @if($credentials->paypal_status == 'active') checked @endif>
                            <label class="form-check-label fs-7 fw-bold text-gray-700" for="paypal_status">Active</label>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card B: Credentials Form Grid -->
                <div class="ps-subcard-credentials {{ $credentials->paypal_status == 'active' ? '' : 'ps-collapsed' }}" id="paypal_credentials_body">
                    <div class="ps-credentials-grid">
                        <!-- Client ID -->
                        <div>
                            <label class="ps-field-label" for="paypal_client_id">
                                <span>PayPal Client ID <span class="text-danger">*</span></span>
                            </label>
                            <div class="ps-input-group">
                                <input type="text" class="form-control" id="paypal_client_id" name="paypal_client_id" value="{{ $credentials->paypal_client_id }}" placeholder="Enter PayPal Client ID" autocomplete="off">
                                <button type="button" class="btn btn-action ps-copy-btn" data-target="#paypal_client_id" title="Copy Client ID">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Secret Key (Masked) -->
                        <div>
                            <label class="ps-field-label" for="paypal_secret">
                                <span>PayPal Secret Key <span class="text-danger">*</span></span>
                            </label>
                            <div class="ps-input-group">
                                <input type="password" class="form-control" id="paypal_secret" name="paypal_secret" value="{{ $credentials->paypal_secret }}" placeholder="Enter PayPal Secret" autocomplete="new-password">
                                <button type="button" class="btn btn-action ps-toggle-visibility" data-target="#paypal_secret" title="Show/Hide">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-action ps-copy-btn" data-target="#paypal_secret" title="Copy Secret">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Environment Mode -->
                        <div>
                            <label class="ps-field-label" for="paypal_mode">
                                <span>Environment Mode <span class="text-danger">*</span></span>
                            </label>
                            <div class="ps-input-group">
                                <select class="form-select" id="paypal_mode" name="paypal_mode">
                                    <option value="sandbox" @if($credentials->paypal_mode == 'sandbox') selected @endif>Sandbox (Testing)</option>
                                    <option value="live" @if($credentials->paypal_mode == 'live') selected @endif>Live Production</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card C: Webhook IPN Endpoint -->
                <div class="ps-subcard-webhook">
                    <div class="ps-webhook-label">
                        <i class="fas fa-satellite-dish text-primary"></i> Instant Payment Notification (IPN) Listener
                    </div>
                    <div class="ps-input-group">
                        <input type="text" class="form-control ps-webhook-input" readonly value="{{ route('verify-billing-ipn') }}" id="paypal_webhook_url">
                        <button type="button" class="btn btn-action ps-copy-btn" data-target="#paypal_webhook_url" title="Copy Webhook URL">
                            <i class="fas fa-copy me-1"></i> Copy URL
                        </button>
                    </div>
                    <p class="ps-webhook-note">
                        <i class="fas fa-info-circle text-info"></i> In your PayPal Developer App, set the IPN notification URL above to handle subscription lifecycle events.
                    </p>
                </div>

                <!-- Sub-Card D: Audit & Activity Strip -->
                <div class="ps-subcard-footer">
                    <div class="ps-footer-item">
                        <i class="fas fa-cube text-primary"></i> Powers: Tenant Packages, Custom Subscriptions &amp; Invoices
                    </div>
                    <div class="ps-footer-item">
                        <i class="fas fa-lock text-success"></i> TLS 1.3 / OAuth 2.0 Client Credentials
                    </div>
                </div>
            </div>

            <!-- ────────────────────────────────────────────────────────────────────────
                 GATEWAY 2: STRIPE PAYMENTS (CASHIER / RECURRING)
                 ──────────────────────────────────────────────────────────────────────── -->
            <div class="ps-gateway-card" id="gateway_card_stripe" data-gateway="stripe">
                <!-- Sub-Card A: Provider Identity Strip -->
                <div class="ps-subcard-header">
                    <div class="ps-gateway-identity">
                        <div class="ps-gateway-logo stripe">
                            <i class="fab fa-stripe-s"></i>
                        </div>
                        <div>
                            <div class="ps-gateway-title">
                                Stripe Payments &amp; Laravel Cashier Subscription Engine
                                @php
                                    $isStripeLive = str_starts_with($credentials->api_key ?? '', 'pk_live_');
                                @endphp
                                <span class="badge badge-light-{{ $isStripeLive ? 'danger' : 'primary' }} fs-8 fw-bolder" id="stripe_mode_badge">
                                    {{ $isStripeLive ? 'LIVE PRODUCTION' : 'TEST MODE' }}
                                </span>
                            </div>
                            <p class="ps-gateway-desc">Accept global credit/debit cards, Apple Pay, Google Pay, and automated recurring billing with 3D Secure 2 compliance</p>
                        </div>
                    </div>

                    <div class="ps-header-controls">
                        <!-- Diagnostic Test Button -->
                        <button type="button" class="btn btn-sm btn-light-primary btn-test-gateway" data-gateway="stripe">
                            <i class="fas fa-plug me-1"></i> Test Connection
                        </button>

                        <!-- Native Metronic Switch -->
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input gateway-toggle" type="checkbox" id="stripe_status" name="stripe_status" value="active" data-target="#stripe_credentials_body" @if($credentials->stripe_status == 'active') checked @endif>
                            <label class="form-check-label fs-7 fw-bold text-gray-700" for="stripe_status">Active</label>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card B: Credentials Form Grid -->
                <div class="ps-subcard-credentials {{ $credentials->stripe_status == 'active' ? '' : 'ps-collapsed' }}" id="stripe_credentials_body">
                    <div class="ps-credentials-grid">
                        <!-- Publishable Key -->
                        <div>
                            <label class="ps-field-label" for="api_key">
                                <span>Publishable Key (`pk_...`) <span class="text-danger">*</span></span>
                            </label>
                            <div class="ps-input-group">
                                <input type="text" class="form-control" id="api_key" name="api_key" value="{{ $credentials->api_key }}" placeholder="pk_test_... or pk_live_..." autocomplete="off">
                                <button type="button" class="btn btn-action ps-copy-btn" data-target="#api_key" title="Copy Publishable Key">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Secret Key (Masked) -->
                        <div>
                            <label class="ps-field-label" for="api_secret">
                                <span>Secret Key (`sk_...`) <span class="text-danger">*</span></span>
                            </label>
                            <div class="ps-input-group">
                                <input type="password" class="form-control" id="api_secret" name="api_secret" value="{{ $credentials->api_secret }}" placeholder="sk_test_... or sk_live_..." autocomplete="new-password">
                                <button type="button" class="btn btn-action ps-toggle-visibility" data-target="#api_secret" title="Show/Hide">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-action ps-copy-btn" data-target="#api_secret" title="Copy Secret Key">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Webhook Signing Secret (Masked) -->
                        <div>
                            <label class="ps-field-label" for="webhook_key">
                                <span>Webhook Secret (`whsec_...`) <span class="text-danger">*</span></span>
                            </label>
                            <div class="ps-input-group">
                                <input type="password" class="form-control" id="webhook_key" name="webhook_key" value="{{ $credentials->webhook_key }}" placeholder="whsec_..." autocomplete="new-password">
                                <button type="button" class="btn btn-action ps-toggle-visibility" data-target="#webhook_key" title="Show/Hide">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-action ps-copy-btn" data-target="#webhook_key" title="Copy Webhook Secret">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card C: Webhook IPN Endpoint -->
                <div class="ps-subcard-webhook">
                    <div class="ps-webhook-label">
                        <i class="fas fa-satellite-dish text-primary"></i> Stripe Invoices &amp; Subscriptions Webhook Endpoint
                    </div>
                    <div class="ps-input-group">
                        <input type="text" class="form-control ps-webhook-input" readonly value="{{ route('save_webhook') }}" id="stripe_webhook_url">
                        <button type="button" class="btn btn-action ps-copy-btn" data-target="#stripe_webhook_url" title="Copy Webhook URL">
                            <i class="fas fa-copy me-1"></i> Copy URL
                        </button>
                    </div>
                    <p class="ps-webhook-note">
                        <i class="fas fa-info-circle text-info"></i> Listen for: <code>invoice.payment_succeeded</code>, <code>customer.subscription.updated</code>, <code>customer.subscription.deleted</code>.
                    </p>
                </div>

                <!-- Sub-Card D: Audit & Activity Strip -->
                <div class="ps-subcard-footer">
                    <div class="ps-footer-item">
                        <i class="fas fa-cube text-primary"></i> Powers: Laravel Cashier v16, Invoicing, Custom Plans &amp; Coin Top-Ups
                    </div>
                    <div class="ps-footer-item">
                        <i class="fas fa-lock text-success"></i> PCI-DSS Level 1 / AES-256 Storage
                    </div>
                </div>
            </div>

            <!-- ────────────────────────────────────────────────────────────────────────
                 GATEWAY 3: RAZORPAY BILLING & RECURRING
                 ──────────────────────────────────────────────────────────────────────── -->
            <div class="ps-gateway-card" id="gateway_card_razorpay" data-gateway="razorpay">
                <!-- Sub-Card A: Provider Identity Strip -->
                <div class="ps-subcard-header">
                    <div class="ps-gateway-identity">
                        <div class="ps-gateway-logo razorpay">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div>
                            <div class="ps-gateway-title">
                                Razorpay Payment Gateway &amp; Recurring Auto-Debit
                                @php
                                    $isRazorpayLive = str_starts_with($credentials->razorpay_key ?? '', 'rzp_live_');
                                @endphp
                                <span class="badge badge-light-{{ $isRazorpayLive ? 'danger' : 'primary' }} fs-8 fw-bolder" id="razorpay_mode_badge">
                                    {{ $isRazorpayLive ? 'LIVE PRODUCTION' : 'TEST MODE' }}
                                </span>
                            </div>
                            <p class="ps-gateway-desc">Accept UPI, NetBanking, Domestic/International Cards, and Auto-Debit e-Mandate recurring subscriptions</p>
                        </div>
                    </div>

                    <div class="ps-header-controls">
                        <!-- Diagnostic Test Button -->
                        <button type="button" class="btn btn-sm btn-light-primary btn-test-gateway" data-gateway="razorpay">
                            <i class="fas fa-plug me-1"></i> Test Connection
                        </button>

                        <!-- Native Metronic Switch -->
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input gateway-toggle" type="checkbox" id="razorpay_status" name="razorpay_status" value="active" data-target="#razorpay_credentials_body" @if($credentials->razorpay_status == 'active') checked @endif>
                            <label class="form-check-label fs-7 fw-bold text-gray-700" for="razorpay_status">Active</label>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card B: Credentials Form Grid -->
                <div class="ps-subcard-credentials {{ $credentials->razorpay_status == 'active' ? '' : 'ps-collapsed' }}" id="razorpay_credentials_body">
                    <div class="ps-credentials-grid">
                        <!-- Key ID -->
                        <div>
                            <label class="ps-field-label" for="razorpay_key">
                                <span>Razorpay Key ID (`rzp_...`) <span class="text-danger">*</span></span>
                            </label>
                            <div class="ps-input-group">
                                <input type="text" class="form-control" id="razorpay_key" name="razorpay_key" value="{{ $credentials->razorpay_key }}" placeholder="rzp_test_... or rzp_live_..." autocomplete="off">
                                <button type="button" class="btn btn-action ps-copy-btn" data-target="#razorpay_key" title="Copy Key ID">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Secret Key (Masked) -->
                        <div>
                            <label class="ps-field-label" for="razorpay_secret">
                                <span>Razorpay Key Secret <span class="text-danger">*</span></span>
                            </label>
                            <div class="ps-input-group">
                                <input type="password" class="form-control" id="razorpay_secret" name="razorpay_secret" value="{{ $credentials->razorpay_secret }}" placeholder="Enter Razorpay Secret Key" autocomplete="new-password">
                                <button type="button" class="btn btn-action ps-toggle-visibility" data-target="#razorpay_secret" title="Show/Hide">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-action ps-copy-btn" data-target="#razorpay_secret" title="Copy Key Secret">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Webhook Secret (Masked) -->
                        <div>
                            <label class="ps-field-label" for="razorpay_webhook_secret">
                                <span>Razorpay Webhook Secret <span class="text-danger">*</span></span>
                            </label>
                            <div class="ps-input-group">
                                <input type="password" class="form-control" id="razorpay_webhook_secret" name="razorpay_webhook_secret" value="{{ $credentials->razorpay_webhook_secret }}" placeholder="Enter Razorpay Webhook Secret" autocomplete="new-password">
                                <button type="button" class="btn btn-action ps-toggle-visibility" data-target="#razorpay_webhook_secret" title="Show/Hide">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-action ps-copy-btn" data-target="#razorpay_webhook_secret" title="Copy Webhook Secret">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card C: Webhook IPN Endpoint -->
                <div class="ps-subcard-webhook">
                    <div class="ps-webhook-label">
                        <i class="fas fa-satellite-dish text-primary"></i> Razorpay Subscription Invoices Webhook Endpoint
                    </div>
                    <div class="ps-input-group">
                        <input type="text" class="form-control ps-webhook-input" readonly value="{{ route('save_razorpay-webhook') }}" id="razorpay_webhook_url">
                        <button type="button" class="btn btn-action ps-copy-btn" data-target="#razorpay_webhook_url" title="Copy Webhook URL">
                            <i class="fas fa-copy me-1"></i> Copy URL
                        </button>
                    </div>
                    <p class="ps-webhook-note">
                        <i class="fas fa-info-circle text-info"></i> In Razorpay Dashboard &gt; Settings &gt; Webhooks, add this URL and listen for <code>subscription.charged</code>, <code>payment.authorized</code>.
                    </p>
                </div>

                <!-- Sub-Card D: Audit & Activity Strip -->
                <div class="ps-subcard-footer">
                    <div class="ps-footer-item">
                        <i class="fas fa-cube text-primary"></i> Powers: Indian UPI, Debit/Credit Cards &amp; Recurring Subscriptions
                    </div>
                    <div class="ps-footer-item">
                        <i class="fas fa-lock text-success"></i> HMAC-SHA256 Webhook Signature Verification
                    </div>
                </div>
            </div>

            <!-- ────────────────────────────────────────────────────────────────────────
                 DEDICATED VIEW TAB: UNIFIED WEBHOOK ENDPOINTS HUB
                 ──────────────────────────────────────────────────────────────────────── -->
            <div class="ps-webhook-hub-card d-none" id="webhook_hub_container">
                <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                    <div>
                        <h2 class="fs-5 fw-bolder text-dark mb-1">
                            <i class="fas fa-network-wired text-primary me-2"></i> Unified Webhook Endpoints &amp; IPN Hub
                        </h2>
                        <p class="fs-7 text-muted mb-0">Direct authoritative URLs for incoming asynchronous transaction events across all three gateways</p>
                    </div>
                    <span class="badge badge-light-primary fw-bold">3 Webhooks Configured</span>
                </div>

                <!-- Webhook 1: PayPal -->
                <div class="ps-hub-row">
                    <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge badge-primary fw-bold">PayPal IPN</span>
                            <span class="fs-7 fw-bold text-dark">Recurring Billing &amp; Agreement Handshake</span>
                        </div>
                        <span class="badge badge-light-success fs-8">POST HTTP</span>
                    </div>
                    <div class="ps-input-group mb-2">
                        <input type="text" class="form-control ps-webhook-input" readonly value="{{ route('verify-billing-ipn') }}" id="hub_paypal_url">
                        <button type="button" class="btn btn-action ps-copy-btn" data-target="#hub_paypal_url" title="Copy URL">
                            <i class="fas fa-copy me-1"></i> Copy URL
                        </button>
                    </div>
                    <div class="fs-8 text-muted">
                        • Controller Action: <code>PaypalIPNController@verifyBillingIPN</code> &nbsp;|&nbsp; • CSRF Excluded: Automated IPN Validation
                    </div>
                </div>

                <!-- Webhook 2: Stripe -->
                <div class="ps-hub-row">
                    <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge badge-info fw-bold">Stripe Webhook</span>
                            <span class="fs-7 fw-bold text-dark">Cashier Invoice Settlement &amp; Renewal Engine</span>
                        </div>
                        <span class="badge badge-light-success fs-8">POST HTTP</span>
                    </div>
                    <div class="ps-input-group mb-2">
                        <input type="text" class="form-control ps-webhook-input" readonly value="{{ route('save_webhook') }}" id="hub_stripe_url">
                        <button type="button" class="btn btn-action ps-copy-btn" data-target="#hub_stripe_url" title="Copy URL">
                            <i class="fas fa-copy me-1"></i> Copy URL
                        </button>
                    </div>
                    <div class="fs-8 text-muted">
                        • Controller Action: <code>StripeWebhookController@saveInvoices</code> &nbsp;|&nbsp; • Required Events: <code>invoice.payment_succeeded</code>, <code>customer.subscription.deleted</code>
                    </div>
                </div>

                <!-- Webhook 3: Razorpay -->
                <div class="ps-hub-row mb-0">
                    <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge badge-warning fw-bold">Razorpay Webhook</span>
                            <span class="fs-7 fw-bold text-dark">Invoice Settlement &amp; Subscription Auto-Debit</span>
                        </div>
                        <span class="badge badge-light-success fs-8">POST HTTP</span>
                    </div>
                    <div class="ps-input-group mb-2">
                        <input type="text" class="form-control ps-webhook-input" readonly value="{{ route('save_razorpay-webhook') }}" id="hub_razorpay_url">
                        <button type="button" class="btn btn-action ps-copy-btn" data-target="#hub_razorpay_url" title="Copy URL">
                            <i class="fas fa-copy me-1"></i> Copy URL
                        </button>
                    </div>
                    <div class="fs-8 text-muted">
                        • Controller Action: <code>RazorpayWebhookController@saveInvoices</code> &nbsp;|&nbsp; • Secret Verification: HMAC-SHA256
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection

@push('footer-script')
<script>
    (function () {
        'use strict';

        // ─── 1. Sub-Tabs Filter Logic (Rule #20: Modern 3-Tier Card Standard) ───
        $('.ps-tab-btn').on('click', function () {
            $('.ps-tab-btn').removeClass('active');
            $(this).addClass('active');

            var filter = $(this).data('filter');

            if (filter === 'all') {
                $('.ps-gateway-card').removeClass('d-none');
                $('#webhook_hub_container').addClass('d-none');
            } else if (filter === 'webhooks') {
                $('.ps-gateway-card').addClass('d-none');
                $('#webhook_hub_container').removeClass('d-none');
            } else {
                $('.ps-gateway-card').addClass('d-none');
                $('#webhook_hub_container').addClass('d-none');
                $('#gateway_card_' + filter).removeClass('d-none');
            }
        });

        // ─── 2. Reactive Gateway Accordion & Metric Counter ───
        $('.gateway-toggle').on('change', function () {
            var targetBody = $(this).data('target');
            var isChecked = $(this).is(':checked');

            if (isChecked) {
                $(targetBody).slideDown(220, function () {
                    $(this).removeClass('ps-collapsed');
                    $(this).find('input:visible:first').focus();
                });
            } else {
                $(targetBody).slideUp(220, function () {
                    $(this).addClass('ps-collapsed');
                });
            }

            // Dynamically calculate active / inactive count
            var activeCount = $('.gateway-toggle:checked').length;
            var totalCount = $('.gateway-toggle').length;
            var inactiveCount = totalCount - activeCount;

            $('#active_counter_val').text(activeCount);
            $('#inactive_counter_val').text(inactiveCount);

            // Update sub-tab badges
            var paypalActive = $('#paypal_status').is(':checked');
            $('#badge_tab_paypal')
                .text(paypalActive ? 'Active' : 'Inactive')
                .removeClass('badge-success badge-secondary')
                .addClass(paypalActive ? 'badge-success' : 'badge-secondary');

            var stripeActive = $('#stripe_status').is(':checked');
            $('#badge_tab_stripe')
                .text(stripeActive ? 'Active' : 'Inactive')
                .removeClass('badge-success badge-secondary')
                .addClass(stripeActive ? 'badge-success' : 'badge-secondary');

            var razorpayActive = $('#razorpay_status').is(':checked');
            $('#badge_tab_razorpay')
                .text(razorpayActive ? 'Active' : 'Inactive')
                .removeClass('badge-success badge-secondary')
                .addClass(razorpayActive ? 'badge-success' : 'badge-secondary');
        });

        // ─── 3. Reactive Environment Badges (Sandbox vs Live) ───
        $('#paypal_mode').on('change', function () {
            var mode = $(this).val();
            var badge = $('#paypal_mode_badge');
            if (mode === 'live') {
                badge.text('LIVE PRODUCTION')
                    .removeClass('badge-light-primary')
                    .addClass('badge-light-danger');
            } else {
                badge.text('SANDBOX')
                    .removeClass('badge-light-danger')
                    .addClass('badge-light-primary');
            }
        });

        $('#api_key').on('input', function () {
            var val = $(this).val().trim();
            var badge = $('#stripe_mode_badge');
            if (val.indexOf('pk_live_') === 0) {
                badge.text('LIVE PRODUCTION')
                    .removeClass('badge-light-primary')
                    .addClass('badge-light-danger');
            } else {
                badge.text('TEST MODE')
                    .removeClass('badge-light-danger')
                    .addClass('badge-light-primary');
            }
        });

        $('#razorpay_key').on('input', function () {
            var val = $(this).val().trim();
            var badge = $('#razorpay_mode_badge');
            if (val.indexOf('rzp_live_') === 0) {
                badge.text('LIVE PRODUCTION')
                    .removeClass('badge-light-primary')
                    .addClass('badge-light-danger');
            } else {
                badge.text('TEST MODE')
                    .removeClass('badge-light-danger')
                    .addClass('badge-light-primary');
            }
        });

        // ─── 4. Password Visibility Toggle (Show / Hide) ───
        $(document).on('click', '.ps-toggle-visibility', function () {
            var targetInput = $($(this).data('target'));
            var icon = $(this).find('i');

            if (targetInput.attr('type') === 'password') {
                targetInput.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash text-primary');
            } else {
                targetInput.attr('type', 'password');
                icon.removeClass('fa-eye-slash text-primary').addClass('fa-eye text-muted');
            }
        });

        // ─── 5. High-Performance 1-Click Clipboard Copying ───
        $(document).on('click', '.ps-copy-btn', function () {
            var btn = $(this);
            var targetInput = $($(this).data('target'));
            var textToCopy = targetInput.val();

            if (!textToCopy) {
                return;
            }

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(textToCopy).then(showSuccess, fallbackCopy);
            } else {
                fallbackCopy();
            }

            function fallbackCopy() {
                targetInput.select();
                try {
                    document.execCommand('copy');
                    showSuccess();
                } catch (err) {
                    console.error('Clipboard copy error', err);
                }
            }

            function showSuccess() {
                var originalHtml = btn.html();
                btn.html('<i class="fas fa-check text-success"></i>');
                btn.addClass('btn-light-success');

                setTimeout(function () {
                    btn.html(originalHtml);
                    btn.removeClass('btn-light-success');
                }, 1500);
            }
        });

        // ─── 6. Real-Time 300ms Debounced Search Filter ───
        var searchDebounceTimer = null;
        $('#ps_gateway_search').on('input', function () {
            var query = $(this).val().toLowerCase().trim();

            if (query.length > 0) {
                $('#ps_search_clear').removeClass('d-none');
            } else {
                $('#ps_search_clear').addClass('d-none');
            }

            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(function () {
                if (query === '') {
                    $('.ps-gateway-card').removeClass('d-none');
                    return;
                }

                // If currently viewing webhook hub, switch back to all
                $('#webhook_hub_container').addClass('d-none');
                $('.ps-tab-btn').removeClass('active');
                $('.ps-tab-btn[data-filter="all"]').addClass('active');

                $('.ps-gateway-card').each(function () {
                    var cardText = $(this).text().toLowerCase();
                    if (cardText.indexOf(query) !== -1) {
                        $(this).removeClass('d-none');
                    } else {
                        $(this).addClass('d-none');
                    }
                });
            }, 300);
        });

        $('#ps_search_clear').on('click', function () {
            $('#ps_gateway_search').val('').trigger('input');
        });

        // Clear error highlight dynamically when user types
        $(document).on('input change', '.form-control, .form-select', function () {
            $(this).removeClass('is-invalid');
        });

        // ─── 7. Two-Tier Idempotency, In-Flight Mutex & Multi-Tab Synchronization ───
        var isMutationInFlight = false;
        var isTestingInFlight = false;
        var paymentSyncChannel = null;

        function generateUUID() {
            if (typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function') {
                return crypto.randomUUID();
            }
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
                var r = Math.random() * 16 | 0, v = c === 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
        }

        try {
            if (typeof BroadcastChannel !== 'undefined') {
                paymentSyncChannel = new BroadcastChannel('jero_payment_settings_sync');
                paymentSyncChannel.onmessage = function (e) {
                    if (e && e.data && e.data.type === 'PAYMENT_SETTINGS_SAVED') {
                        console.info('Payment settings synchronized across open tabs at ' + new Date(e.data.timestamp).toLocaleTimeString());
                    }
                };
            }
        } catch (bcEx) {
            console.warn('BroadcastChannel not supported in this environment', bcEx);
        }

        // Draft autosave for non-sensitive configuration values
        var draftAutosaveTimer = null;
        function saveDraft() {
            try {
                var draft = {
                    api_key: $('#api_key').val() || '',
                    paypal_client_id: $('#paypal_client_id').val() || '',
                    paypal_mode: $('#paypal_mode').val() || 'sandbox',
                    razorpay_key: $('#razorpay_key').val() || '',
                    paypal_status: $('#paypal_status').is(':checked'),
                    stripe_status: $('#stripe_status').is(':checked'),
                    razorpay_status: $('#razorpay_status').is(':checked')
                };
                sessionStorage.setItem('jero_superadmin_payment_settings_draft', JSON.stringify(draft));
            } catch (err) {}
        }

        $(document).on('input change', '#api_key, #paypal_client_id, #paypal_mode, #razorpay_key, #paypal_status, #stripe_status, #razorpay_status', function () {
            clearTimeout(draftAutosaveTimer);
            draftAutosaveTimer = setTimeout(saveDraft, 400);
        });

        // ─── 8. Diagnostic Test Connection Engine ───
        $('.btn-test-gateway').on('click', function () {
            if (isTestingInFlight) {
                return;
            }
            isTestingInFlight = true;

            var btn = $(this);
            var gateway = btn.data('gateway');
            var originalHtml = btn.html();

            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Testing...');

            var payload = {
                _token: '{{ csrf_token() }}',
                gateway: gateway
            };

            if (gateway === 'stripe') {
                payload.api_secret = $('#api_secret').val();
            } else if (gateway === 'razorpay') {
                payload.razorpay_key = $('#razorpay_key').val();
                payload.razorpay_secret = $('#razorpay_secret').val();
            } else if (gateway === 'paypal') {
                payload.paypal_client_id = $('#paypal_client_id').val();
                payload.paypal_secret = $('#paypal_secret').val();
                payload.paypal_mode = $('#paypal_mode').val();
            }

            $.ajax({
                url: '{{ route('superadmin.payment-settings.verifyGateway') }}',
                type: 'POST',
                data: payload,
                success: function (res) {
                    isTestingInFlight = false;
                    btn.prop('disabled', false).html(originalHtml);

                    Swal.fire({
                        icon: 'success',
                        title: 'Connection Successful!',
                        html: '<div class="text-start mt-2">' +
                              '<p class="mb-1 fw-bold text-success"><i class="fas fa-check-circle me-1"></i> ' + res.message + '</p>' +
                              (res.environment ? '<p class="mb-1 text-muted fs-7"><b>Environment:</b> ' + res.environment + '</p>' : '') +
                              (res.latency_ms ? '<p class="mb-0 text-muted fs-7"><b>Latency:</b> ' + res.latency_ms + ' ms</p>' : '') +
                              '</div>',
                        confirmButtonText: 'Great!'
                    });
                },
                error: function (xhr) {
                    isTestingInFlight = false;
                    btn.prop('disabled', false).html(originalHtml);
                    var errMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Connection test failed. Check credentials.';

                    Swal.fire({
                        icon: 'error',
                        title: 'Diagnostic Test Failed',
                        text: errMsg,
                        confirmButtonText: 'Review Credentials'
                    });
                }
            });
        });

        // ─── 9. Master AJAX Form Submission with Error Highlights & Idempotency ───
        $('#save-form').on('click', function () {
            if (isMutationInFlight) {
                return;
            }
            isMutationInFlight = true;

            var btn = $(this);
            btn.attr('data-kt-indicator', 'on').prop('disabled', true);
            $('.form-control, .form-select').removeClass('is-invalid');

            var idempotencyKey = generateUUID();
            var formData = $('#editPaymentSettings').serializeArray();
            formData.push({ name: 'idempotency_key', value: idempotencyKey });

            $.ajax({
                url: '{{ route('superadmin.payment-settings.update', $credentials->id ?? 1) }}',
                container: '#editPaymentSettings',
                type: 'POST',
                data: $.param(formData),
                headers: {
                    'X-Idempotency-Key': idempotencyKey
                },
                success: function (response) {
                    isMutationInFlight = false;
                    btn.removeAttr('data-kt-indicator').prop('disabled', false);

                    // Clear draft upon successful save
                    try {
                        sessionStorage.removeItem('jero_superadmin_payment_settings_draft');
                    } catch (err) {}

                    // Broadcast synchronization event to other open tabs
                    try {
                        if (paymentSyncChannel) {
                            paymentSyncChannel.postMessage({
                                type: 'PAYMENT_SETTINGS_SAVED',
                                timestamp: Date.now()
                            });
                        }
                    } catch (err) {}

                    if (response.status === 'success') {
                        Swal.fire({
                            title: response._replayed ? 'Success (Replayed)' : 'Success!',
                            html: response.message || 'Payment settings updated successfully.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            title: 'Warning',
                            html: response.message || 'Settings saved with warnings.',
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function (xhr) {
                    isMutationInFlight = false;
                    btn.removeAttr('data-kt-indicator').prop('disabled', false);

                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        var errors = xhr.responseJSON.errors;
                        var errorList = '<ul class="text-start ps-3 mb-0">';

                        $.each(errors, function (field, msgs) {
                            $('#' + field).addClass('is-invalid');
                            errorList += '<li class="text-danger fs-7 mb-1">' + msgs[0] + '</li>';
                        });

                        errorList += '</ul>';

                        Swal.fire({
                            title: 'Validation Error',
                            html: errorList,
                            icon: 'error',
                            confirmButtonText: 'Review Inputs'
                        });
                    } else {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Something went wrong while saving settings.';
                        Swal.fire({
                            title: 'Error!',
                            text: msg,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });
        });

        // ─── 10. Keyboard Shortcuts (Ctrl+S / Cmd+S to Save, Escape to Clear Search) ───
        $(document).on('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                $('#save-form').trigger('click');
            }
            if (e.key === 'Escape' && $('#ps_gateway_search').is(':focus')) {
                $('#ps_search_clear').trigger('click');
            }
        });

    })();
</script>
@endpush