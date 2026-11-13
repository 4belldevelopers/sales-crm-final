@extends('layouts.superadmin_layout')

@push('head-script')
<style>
    /* ═════════════════════════════════════════════════════════════════════════════════════════
       SUPER ADMIN PROFILE & IDENTITY HUB — ENTERPRISE CARD + SUB-CARD DESIGN SYSTEM
       Adheres strictly to docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md & docs/UI_UX_STANDARDS.md
       Optimized for 15.6" Displays (1366×768 & 1920×1080) with Zero Horizontal Overflow
       ═════════════════════════════════════════════════════════════════════════════════════════ */

    body {
        overflow-x: hidden !important;
        background-color: #f8fafc !important;
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
    }

    /* ─── Top-Gap Collapse: Eliminates excessive 60px toolbar spacing ─── */
    #kt_header_nav .page-title {
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

    #kt_content,
    .content {
        padding-top: 0 !important;
        margin-top: 0 !important;
    }

    .sa-profile-container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 16px 30px 16px !important;
        margin-top: 0 !important;
    }

    /* ─── Tier 1: Top Header Toolbar Card ─── */
    .sa-profile-toolbar-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 14px 20px !important;
        margin-bottom: 14px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .sa-profile-toolbar-left {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    .sa-profile-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: linear-gradient(135deg, #009ef7 0%, #0077c5 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(0, 158, 247, 0.25);
    }

    .sa-profile-title {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.25;
        margin: 0;
    }

    .sa-profile-subtitle {
        font-size: 12px;
        color: #64748b;
        margin: 2px 0 0 0;
    }

    .sa-profile-toolbar-badges {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .sa-profile-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 600;
        letter-spacing: 0.2px;
    }

    .sa-profile-toolbar-right {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* ─── Tier 2: Sub-Tabs Navigation Card ─── */
    .sa-profile-tabs-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 6px 10px !important;
        margin-bottom: 14px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
    }

    .sa-profile-nav-tabs {
        border-bottom: none !important;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .sa-profile-nav-tabs .nav-link {
        border: 1px solid transparent !important;
        border-radius: 8px !important;
        padding: 8px 16px !important;
        font-size: 12.5px !important;
        font-weight: 600 !important;
        color: #64748b !important;
        background: transparent !important;
        transition: all 0.2s ease !important;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        text-decoration: none !important;
    }

    .sa-profile-nav-tabs .nav-link:hover {
        color: #009ef7 !important;
        background: #f1f5f9 !important;
        text-decoration: none !important;
    }

    .sa-profile-nav-tabs .nav-link.active {
        color: #009ef7 !important;
        background: #eff6ff !important;
        border-color: #bfdbfe !important;
        box-shadow: 0 1px 2px rgba(0, 158, 247, 0.08) !important;
        text-decoration: none !important;
    }

    /* Tab Panes Display Guarantee */
    .tab-content > .tab-pane {
        display: none !important;
    }
    .tab-content > .tab-pane.active,
    .tab-content > .tab-pane.show.active {
        display: block !important;
    }

    /* ─── Tier 3: Modular Sub-Cards ─── */
    .sa-subcard {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 20px !important;
        margin-bottom: 14px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
    }

    .sa-subcard-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 12px;
        margin-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
    }

    .sa-subcard-title {
        font-size: 13.5px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sa-subcard-subtitle {
        font-size: 11.5px;
        color: #64748b;
        margin: 2px 0 0 0;
    }

    /* Avatar Hero Card */
    .sa-avatar-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 10px 0;
    }

    .sa-avatar-box {
        position: relative;
        width: 124px;
        height: 124px;
        border-radius: 50%;
        border: 3px solid #e2e8f0;
        padding: 3px;
        background: #ffffff;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08);
        margin-bottom: 14px;
    }

    .sa-avatar-img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        display: block;
        background: #f1f5f9;
    }

    .sa-avatar-badge-btn {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #009ef7;
        color: #ffffff;
        border: 2px solid #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        transition: transform 0.15s ease, background-color 0.15s ease;
    }

    .sa-avatar-badge-btn:hover {
        background: #0077c5;
        transform: scale(1.08);
    }

    .sa-avatar-hero-name {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }

    .sa-avatar-hero-email {
        font-size: 12px;
        color: #64748b;
        margin: 2px 0 10px 0;
    }

    /* Form Controls */
    .sa-form-label {
        font-size: 12px !important;
        font-weight: 600 !important;
        color: #334155 !important;
        margin-bottom: 5px !important;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .sa-form-label .required-dot {
        color: #ef4444;
        font-size: 14px;
        line-height: 1;
        margin-left: 2px;
    }

    .sa-input-group {
        position: relative;
    }

    .sa-input-icon {
        position: absolute;
        top: 50%;
        left: 14px;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 13px;
        z-index: 4;
        pointer-events: none;
    }

    .sa-input-field {
        height: 40px !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        padding-left: 38px !important;
        padding-right: 14px !important;
        font-size: 12.5px !important;
        font-family: 'Poppins', sans-serif !important;
        color: #0f172a !important;
        background: #ffffff !important;
        transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
    }

    .sa-input-field:focus {
        border-color: #009ef7 !important;
        box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
        outline: none !important;
    }

    .sa-input-field.is-invalid {
        border-color: #ef4444 !important;
        background-image: none !important;
    }

    .sa-input-toggle-btn {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 4px 8px;
        font-size: 13px;
        z-index: 5;
        border-radius: 4px;
    }

    .sa-input-toggle-btn:hover {
        color: #475569;
    }

    .sa-field-note {
        font-size: 11px;
        color: #64748b;
        margin-top: 4px;
        display: block;
    }

    /* Password Strength Meter */
    .sa-strength-wrapper {
        margin-top: 6px;
    }

    .sa-strength-bars {
        display: flex;
        gap: 4px;
        height: 4px;
        margin-bottom: 4px;
    }

    .sa-strength-bar {
        flex: 1;
        height: 100%;
        background: #e2e8f0;
        border-radius: 2px;
        transition: background-color 0.2s ease;
    }

    .sa-strength-text {
        font-size: 10.5px;
        font-weight: 600;
        color: #64748b;
        display: flex;
        justify-content: space-between;
    }

    /* Sticky Bottom Action Strip */
    .sa-sticky-strip {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 12px 20px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 14px;
    }

    .sa-audit-info {
        font-size: 11.5px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    /* Alert Boxes */
    .sa-advisory-box {
        border-radius: 8px;
        padding: 12px 14px;
        font-size: 12px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        line-height: 1.4;
    }

    .sa-advisory-info {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1e40af;
    }

    .sa-advisory-warning {
        background: #fffbeb;
        border: 1px solid #fde68a;
        color: #92400e;
    }

    .sa-param-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px dashed #e2e8f0;
        font-size: 12px;
    }

    .sa-param-row:last-child {
        border-bottom: none;
    }

    .sa-param-key {
        color: #64748b;
        font-weight: 500;
    }

    .sa-param-val {
        color: #0f172a;
        font-weight: 600;
        text-align: right;
    }
</style>
@endpush

@section('content')
<div class="sa-profile-container">

    <!-- ══════════════════════════════════════════════════════════════════════════════
         TIER 1: TOP HEADER TOOLBAR CARD
         ══════════════════════════════════════════════════════════════════════════════ -->
    <div class="sa-profile-toolbar-card">
        <div class="sa-profile-toolbar-left">
            <div class="sa-profile-icon-box">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <div>
                <h1 class="sa-profile-title">Super Admin Profile & Identity Hub</h1>
                <p class="sa-profile-subtitle">Root administrative identity, contact channels, authentication, and platform security</p>
            </div>
            <div class="sa-profile-toolbar-badges">
                <span class="sa-profile-pill bg-light-danger text-danger border border-danger border-opacity-25">
                    <i class="fa-solid fa-crown me-1 text-danger"></i> Root Super Admin
                </span>
                <span class="sa-profile-pill bg-light-success text-success border border-success border-opacity-25">
                    <i class="fa-solid fa-circle-check me-1 text-success"></i> Account Active
                </span>
                <span class="sa-profile-pill bg-light-info text-info border border-info border-opacity-25">
                    <i class="fa-solid fa-clock me-1 text-info"></i> Last Login: {{ $user->last_login ? \Carbon\Carbon::parse($user->last_login)->diffForHumans() : 'Active Now' }}
                </span>
            </div>
        </div>

        <div class="sa-profile-toolbar-right">
            <button type="reset" form="editSettings" class="btn btn-sm btn-light text-muted fw-bold" id="btn-reset-top">
                <i class="fa-solid fa-rotate-left me-1"></i> Discard
            </button>
            <button type="button" class="btn btn-sm btn-primary fw-bold js-btn-save-profile" id="save-form-top">
                <span class="indicator-label"><i class="fa-solid fa-floppy-disk me-1"></i> Save Changes</span>
                <span class="indicator-progress">Saving... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
            </button>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════════════════════════
         TIER 2: SUB-TABS NAVIGATION CARD
         ══════════════════════════════════════════════════════════════════════════════ -->
    <div class="sa-profile-tabs-card">
        <ul class="nav nav-pills sa-profile-nav-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="tab-identity-btn" data-bs-toggle="tab" href="#tab-identity" role="tab" aria-controls="tab-identity" aria-selected="true">
                    <i class="fa-solid fa-id-card"></i> Identity & Contact
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="tab-security-btn" data-bs-toggle="tab" href="#tab-security" role="tab" aria-controls="tab-security" aria-selected="false">
                    <i class="fa-solid fa-shield-halved"></i> Credentials & Password
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="tab-mobile-btn" data-bs-toggle="tab" href="#tab-mobile" role="tab" aria-controls="tab-mobile" aria-selected="false">
                    <i class="fa-solid fa-mobile-screen"></i> Mobile & 2FA
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="tab-audit-btn" data-bs-toggle="tab" href="#tab-audit" role="tab" aria-controls="tab-audit" aria-selected="false">
                    <i class="fa-solid fa-fingerprint"></i> System Privileges & Audit
                </a>
            </li>
        </ul>
    </div>

    <!-- ══════════════════════════════════════════════════════════════════════════════
         TIER 3: MAIN FORM & MODULAR SUB-CARD STREAMS
         ══════════════════════════════════════════════════════════════════════════════ -->
    <form id="editSettings" class="ajax-form" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="remove_image" id="remove_image" value="0">

        <div class="tab-content" id="saProfileTabContent">

            <!-- ─── TAB 1: IDENTITY & CONTACT PROFILE ──────────────────────────── -->
            <div class="tab-pane fade show active" id="tab-identity" role="tabpanel" aria-labelledby="tab-identity-btn">
                <div class="row g-3">
                    <!-- Left: Sub-Card 1A: Executive Profile Hero / Avatar -->
                    <div class="col-xl-4 col-lg-5 col-12">
                        <div class="sa-subcard h-100">
                            <div class="sa-subcard-header">
                                <h3 class="sa-subcard-title">
                                    <i class="fa-solid fa-image text-primary"></i> Profile Photo
                                </h3>
                                <span class="badge badge-light-primary fs-8">Avatar</span>
                            </div>

                            <div class="sa-avatar-wrapper">
                                <div class="sa-avatar-box">
                                    <img src="{{ $user->profile_image_url }}" alt="{{ $user->name }}" class="sa-avatar-img" id="avatar_preview_img">
                                    <label class="sa-avatar-badge-btn" for="avatar_file_input" title="Upload New Photo" data-bs-toggle="tooltip">
                                        <i class="fa-solid fa-camera fs-7"></i>
                                    </label>
                                    <input type="file" name="image" id="avatar_file_input" accept=".png, .jpg, .jpeg, .webp" style="display: none;">
                                </div>

                                <h2 class="sa-avatar-hero-name" id="hero_user_name">{{ ucwords($user->name) }}</h2>
                                <p class="sa-avatar-hero-email" id="hero_user_email">{{ $user->email }}</p>

                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <button type="button" class="btn btn-xs btn-outline btn-outline-primary btn-sm py-1 px-3" onclick="$('#avatar_file_input').trigger('click');">
                                        <i class="fa-solid fa-arrow-up-from-bracket me-1"></i> Upload Photo
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline btn-outline-danger btn-sm py-1 px-3" id="btn_remove_avatar" title="Revert to Default Avatar">
                                        <i class="fa-solid fa-trash-can me-1"></i> Remove
                                    </button>
                                </div>

                                <span class="text-muted fs-8">Accepted: PNG, JPG, JPEG, WEBP (Max 2MB)</span>
                            </div>

                            <div class="border-top border-slate-100 pt-3 mt-3">
                                <div class="sa-param-row">
                                    <span class="sa-param-key">Super Admin ID:</span>
                                    <span class="sa-param-val text-primary">#{{ $user->id }}</span>
                                </div>
                                <div class="sa-param-row">
                                    <span class="sa-param-key">Member Since:</span>
                                    <span class="sa-param-val">{{ $user->created_at ? $user->created_at->format('M d, Y') : 'System Origin' }}</span>
                                </div>
                                <div class="sa-param-row">
                                    <span class="sa-param-key">Role Clearance:</span>
                                    <span class="sa-param-val text-danger fw-bold"><i class="fa-solid fa-shield me-1"></i> Root Administrator</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Sub-Card 1B: Personal & Communication Channels -->
                    <div class="col-xl-8 col-lg-7 col-12">
                        <div class="sa-subcard h-100">
                            <div class="sa-subcard-header">
                                <div>
                                    <h3 class="sa-subcard-title">
                                        <i class="fa-solid fa-address-card text-primary"></i> Personal & Communication Details
                                    </h3>
                                    <p class="sa-subcard-subtitle">Official administrative contact channels for platform notices and identity verification</p>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6 col-12">
                                    <label class="sa-form-label" for="sa_name">
                                        <span>Full Legal Name <span class="required-dot">*</span></span>
                                    </label>
                                    <div class="sa-input-group">
                                        <i class="fa-solid fa-user sa-input-icon"></i>
                                        <input type="text" class="form-control sa-input-field" id="sa_name" name="name" value="{{ ucwords($user->name) }}" required autocomplete="name">
                                    </div>
                                    <span class="sa-field-note">Displayed on audit logs, reports, and master approvals</span>
                                </div>

                                <div class="col-md-6 col-12">
                                    <label class="sa-form-label" for="sa_email">
                                        <span>Administrative Email <span class="required-dot">*</span></span>
                                    </label>
                                    <div class="sa-input-group">
                                        <i class="fa-solid fa-envelope sa-input-icon"></i>
                                        <input type="email" class="form-control sa-input-field" id="sa_email" name="email" value="{{ $user->email }}" required autocomplete="email">
                                    </div>
                                    <span class="sa-field-note">Used for master authentication, OTP verification, and root alerts</span>
                                </div>

                                <div class="col-md-6 col-12">
                                    <label class="sa-form-label" for="sa_whatsapp">
                                        <span>WhatsApp Business Number</span>
                                    </label>
                                    <div class="sa-input-group">
                                        <i class="fa-brands fa-whatsapp sa-input-icon text-success"></i>
                                        <input type="text" class="form-control sa-input-field" id="sa_whatsapp" name="whatsapp_number" value="{{ $user->whatsapp_number ?? '' }}" placeholder="+1234567890">
                                    </div>
                                    <span class="sa-field-note">Optional direct channel for automated high-priority alerts</span>
                                </div>

                                <div class="col-md-6 col-12">
                                    <label class="sa-form-label" for="sa_linkedin">
                                        <span>LinkedIn Profile URL</span>
                                    </label>
                                    <div class="sa-input-group">
                                        <i class="fa-brands fa-linkedin-in sa-input-icon text-primary"></i>
                                        <input type="url" class="form-control sa-input-field" id="sa_linkedin" name="linkedin" value="{{ $user->linkedin ?? '' }}" placeholder="https://linkedin.com/in/username">
                                    </div>
                                    <span class="sa-field-note">Professional executive identity link</span>
                                </div>
                            </div>

                            <div class="sa-advisory-box sa-advisory-info mt-4">
                                <i class="fa-solid fa-circle-info fs-5 mt-1 flex-shrink-0 text-primary"></i>
                                <div>
                                    <strong class="d-block text-primary mb-1">Administrative Identity Scope</strong>
                                    Updates made to your administrative email address will alter your login credentials immediately. Ensure your email remains active to receive critical system updates and security alerts.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── TAB 2: CREDENTIALS & SECURITY ─────────────────────────────── -->
            <div class="tab-pane fade" id="tab-security" role="tabpanel" aria-labelledby="tab-security-btn">
                <div class="row g-3">
                    <!-- Left: Sub-Card 2A: Password Management -->
                    <div class="col-xl-7 col-lg-8 col-12">
                        <div class="sa-subcard h-100">
                            <div class="sa-subcard-header">
                                <div>
                                    <h3 class="sa-subcard-title">
                                        <i class="fa-solid fa-key text-primary"></i> Password Management & Credential Rotation
                                    </h3>
                                    <p class="sa-subcard-subtitle">Maintain strong root credential hygiene to protect platform integrity</p>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="sa-form-label" for="sa_current_password">
                                        <span>Current Password</span>
                                    </label>
                                    <div class="sa-input-group">
                                        <i class="fa-solid fa-lock sa-input-icon"></i>
                                        <input type="password" class="form-control sa-input-field" id="sa_current_password" name="current_password" placeholder="Enter current password to authorize change" autocomplete="current-password">
                                        <button type="button" class="sa-input-toggle-btn js-toggle-pwd" data-target="#sa_current_password" title="Show/Hide Password">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                    <span class="sa-field-note">Required only if you wish to change your password</span>
                                </div>

                                <div class="col-md-6 col-12">
                                    <label class="sa-form-label" for="sa_new_password">
                                        <span>New Password</span>
                                    </label>
                                    <div class="sa-input-group">
                                        <i class="fa-solid fa-shield-keyhole sa-input-icon"></i>
                                        <input type="password" class="form-control sa-input-field" id="sa_new_password" name="password" placeholder="Minimum 8 characters" autocomplete="new-password">
                                        <button type="button" class="sa-input-toggle-btn js-toggle-pwd" data-target="#sa_new_password" title="Show/Hide Password">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                    <!-- Password Strength Meter -->
                                    <div class="sa-strength-wrapper" id="pwd_strength_wrapper">
                                        <div class="sa-strength-bars">
                                            <div class="sa-strength-bar" id="str_bar_1"></div>
                                            <div class="sa-strength-bar" id="str_bar_2"></div>
                                            <div class="sa-strength-bar" id="str_bar_3"></div>
                                            <div class="sa-strength-bar" id="str_bar_4"></div>
                                        </div>
                                        <div class="sa-strength-text">
                                            <span>Password Strength:</span>
                                            <span id="str_text_label" class="text-muted">Not entered</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-12">
                                    <label class="sa-form-label" for="sa_password_confirmation">
                                        <span>Confirm New Password</span>
                                    </label>
                                    <div class="sa-input-group">
                                        <i class="fa-solid fa-check-double sa-input-icon"></i>
                                        <input type="password" class="form-control sa-input-field" id="sa_password_confirmation" name="password_confirmation" placeholder="Re-type new password" autocomplete="new-password">
                                        <button type="button" class="sa-input-toggle-btn js-toggle-pwd" data-target="#sa_password_confirmation" title="Show/Hide Password">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                    <span class="sa-field-note" id="pwd_match_feedback">Must match new password exactly</span>
                                </div>
                            </div>

                            <div class="sa-advisory-box sa-advisory-warning mt-4">
                                <i class="fa-solid fa-shield-halved fs-5 mt-1 flex-shrink-0 text-warning"></i>
                                <div>
                                    <strong class="d-block text-warning mb-1">Root Password Policy</strong>
                                    Leave all password fields blank if you do not wish to update your current password. To ensure master credential integrity, choose a phrase containing upper and lowercase letters, numbers, and symbols.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Sub-Card 2B: Security Best Practices Advisory -->
                    <div class="col-xl-5 col-lg-4 col-12">
                        <div class="sa-subcard h-100">
                            <div class="sa-subcard-header">
                                <h3 class="sa-subcard-title">
                                    <i class="fa-solid fa-lock text-primary"></i> Security Hygiene & Policies
                                </h3>
                            </div>

                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="w-28px h-28px rounded bg-light-primary text-primary d-flex align-items-center justify-content-center flex-shrink-0 mt-1">
                                        <i class="fa-solid fa-check fs-7"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold fs-7 text-dark d-block">Minimum Length</span>
                                        <span class="text-muted fs-8">Passphrases must be at least 8 characters. We recommend 14+ characters for root operations.</span>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start gap-3">
                                    <div class="w-28px h-28px rounded bg-light-primary text-primary d-flex align-items-center justify-content-center flex-shrink-0 mt-1">
                                        <i class="fa-solid fa-check fs-7"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold fs-7 text-dark d-block">Complexity Requirement</span>
                                        <span class="text-muted fs-8">Combine symbols, numbers, and mixed case characters to maximize entropy.</span>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start gap-3">
                                    <div class="w-28px h-28px rounded bg-light-primary text-primary d-flex align-items-center justify-content-center flex-shrink-0 mt-1">
                                        <i class="fa-solid fa-check fs-7"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold fs-7 text-dark d-block">Session Termination</span>
                                        <span class="text-muted fs-8">Rotating your password will preserve your current active session while invalidating external tokens.</span>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start gap-3">
                                    <div class="w-28px h-28px rounded bg-light-primary text-primary d-flex align-items-center justify-content-center flex-shrink-0 mt-1">
                                        <i class="fa-solid fa-check fs-7"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold fs-7 text-dark d-block">Rotation Cadence</span>
                                        <span class="text-muted fs-8">Rotate root credentials periodically (e.g. every 90 days) per enterprise standards.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── TAB 3: MOBILE & 2FA VERIFICATION ──────────────────────────── -->
            <div class="tab-pane fade" id="tab-mobile" role="tabpanel" aria-labelledby="tab-mobile-btn">
                <div class="row g-3">
                    <!-- Left: Sub-Card 3A: Direct Contact / Mobile Number -->
                    <div class="col-xl-6 col-12">
                        <div class="sa-subcard h-100">
                            <div class="sa-subcard-header">
                                <div>
                                    <h3 class="sa-subcard-title">
                                        <i class="fa-solid fa-phone-volume text-primary"></i> Direct Telephone Configuration
                                    </h3>
                                    <p class="sa-subcard-subtitle">Primary mobile number for SMS notifications and emergency platform alerts</p>
                                </div>
                                @if ($user->mobile_verified)
                                    <span class="badge badge-light-success"><i class="fa-solid fa-circle-check me-1 text-success"></i> Verified</span>
                                @else
                                    <span class="badge badge-light-warning"><i class="fa-solid fa-triangle-exclamation me-1 text-warning"></i> Unverified</span>
                                @endif
                            </div>

                            <div class="row g-3">
                                <div class="col-md-5 col-12">
                                    <label class="sa-form-label" for="sa_calling_code">
                                        <span>Dial Code</span>
                                    </label>
                                    <select name="calling_code" id="sa_calling_code" class="form-select form-select-solid" data-control="select2" data-placeholder="Select dial code">
                                        <option value="">Select Dial Code</option>
                                        @foreach ($calling_codes as $code => $value)
                                            <option value="{{ $value['dial_code'] }}"
                                            @if ($user->calling_code)
                                                {{ $user->calling_code == $value['dial_code'] ? 'selected' : '' }}
                                            @endif>
                                                {{ $value['dial_code'] }} ({{ $value['name'] }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-7 col-12">
                                    <label class="sa-form-label" for="sa_mobile">
                                        <span>Mobile Phone Number</span>
                                    </label>
                                    <div class="sa-input-group">
                                        <i class="fa-solid fa-phone sa-input-icon"></i>
                                        <input type="text" class="form-control sa-input-field" id="sa_mobile" name="mobile" value="{{ $user->mobile }}" placeholder="e.g. 9876543210">
                                    </div>
                                    <span class="sa-field-note">Modifying your mobile number resets phone verification</span>
                                </div>
                            </div>

                            <div class="sa-advisory-box sa-advisory-info mt-4">
                                <i class="fa-solid fa-circle-info fs-5 mt-1 flex-shrink-0 text-primary"></i>
                                <div>
                                    <strong class="d-block text-primary mb-1">Phone Verification State</strong>
                                    @if ($user->mobile_verified)
                                        Your primary phone number is verified. You will receive system notifications and security broadcast SMS alerts without restriction.
                                    @else
                                        Your phone number is currently unverified. Use the OTP verification section on the right to complete verification.
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Sub-Card 3B: SMS & Nexmo OTP Verification Gateway -->
                    <div class="col-xl-6 col-12">
                        <div class="sa-subcard h-100">
                            <div class="sa-subcard-header">
                                <div>
                                    <h3 class="sa-subcard-title">
                                        <i class="fa-solid fa-shield-halved text-primary"></i> SMS OTP Verification Gateway
                                    </h3>
                                    <p class="sa-subcard-subtitle">Verify your identity via one-time SMS verification token</p>
                                </div>
                                <span class="badge {{ ($smsSettings && $smsSettings->nexmo_status == 'active') ? 'badge-light-success' : 'badge-light-secondary' }}">
                                    Gateway: {{ ($smsSettings && $smsSettings->nexmo_status == 'active') ? 'Active' : 'Disabled' }}
                                </span>
                            </div>

                            <div id="verify-mobile-container">
                                @if ($smsSettings && $smsSettings->nexmo_status == 'active')
                                    <div id="verify-mobile">
                                        @include('sections.admin_verify_phone')
                                    </div>
                                    <div id="verify-mobile-info" class="mt-2"></div>
                                @else
                                    <div class="p-4 text-center rounded bg-light-primary border border-primary border-opacity-10 my-3">
                                        <div class="w-48px h-48px rounded-circle bg-white text-primary d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm">
                                            <i class="fa-solid fa-comment-sms fs-3"></i>
                                        </div>
                                        <h4 class="fw-bolder text-dark fs-6 mb-1">SMS Verification Gateway Disabled</h4>
                                        <p class="text-muted fs-7 mb-3 px-4">
                                            The automated SMS verification engine (Nexmo / Vonage) is currently deactivated in Global SMS Settings. Phone numbers are verified by root administrative policy.
                                        </p>
                                        <a href="{{ route('superadmin.sms-settings.index') }}" class="btn btn-sm btn-light-primary fw-bold">
                                            <i class="fa-solid fa-gear me-1"></i> Configure SMS Gateway
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── TAB 4: SYSTEM PRIVILEGES & AUDIT ──────────────────────────── -->
            <div class="tab-pane fade" id="tab-audit" role="tabpanel" aria-labelledby="tab-audit-btn">
                <div class="sa-subcard">
                    <div class="sa-subcard-header">
                        <div>
                            <h3 class="sa-subcard-title">
                                <i class="fa-solid fa-fingerprint text-primary"></i> Root Administrative Authority & Platform Security Clearance
                            </h3>
                            <p class="sa-subcard-subtitle">Authoritative system access parameters and operational clearance matrix</p>
                        </div>
                        <span class="badge badge-light-danger fw-bold fs-8">Clearance: Level 0</span>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6 col-12">
                            <h4 class="fw-bold fs-7 text-dark mb-3">System Access Matrix</h4>
                            <div class="sa-param-row">
                                <span class="sa-param-key">Super Administrator Flag:</span>
                                <span class="sa-param-val text-success"><i class="fa-solid fa-circle-check me-1"></i> Active (`is_superadmin = 1`)</span>
                            </div>
                            <div class="sa-param-row">
                                <span class="sa-param-key">Database Schema Authority:</span>
                                <span class="sa-param-val">217 MySQL Tables (Full Read/Write Authority)</span>
                            </div>
                            <div class="sa-param-row">
                                <span class="sa-param-key">Elasticsearch Read Authority:</span>
                                <span class="sa-param-val">60+ Indices (Universal Cluster Scope)</span>
                            </div>
                            <div class="sa-param-row">
                                <span class="sa-param-key">Multi-Tenant Boundary Scope:</span>
                                <span class="sa-param-val">Universal (Cross-Tenant Admin)</span>
                            </div>
                        </div>

                        <div class="col-md-6 col-12">
                            <h4 class="fw-bold fs-7 text-dark mb-3">Authentication & Audit Metrics</h4>
                            <div class="sa-param-row">
                                <span class="sa-param-key">Account Provisioned:</span>
                                <span class="sa-param-val">{{ $user->created_at ? $user->created_at->format('d M Y, h:i A') : 'N/A' }}</span>
                            </div>
                            <div class="sa-param-row">
                                <span class="sa-param-key">Last Credential Rotation:</span>
                                <span class="sa-param-val">{{ $user->updated_at ? $user->updated_at->format('d M Y, h:i A') : 'Initial' }}</span>
                            </div>
                            <div class="sa-param-row">
                                <span class="sa-param-key">Last Authentication Timestamp:</span>
                                <span class="sa-param-val">{{ $user->last_login ? \Carbon\Carbon::parse($user->last_login)->format('d M Y, h:i A') : 'Active Now' }}</span>
                            </div>
                            <div class="sa-param-row">
                                <span class="sa-param-key">Current Active Session ID:</span>
                                <span class="sa-param-val font-monospace fs-8 text-muted">{{ substr(session()->getId(), 0, 16) }}...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- ══════════════════════════════════════════════════════════════════════════════
             SUB-CARD 5: BOTTOM STICKY AUDIT STRIP
             ══════════════════════════════════════════════════════════════════════════════ -->
        <div class="sa-sticky-strip">
            <div class="sa-audit-info">
                <span><i class="fa-solid fa-clock-rotate-left text-muted me-1"></i> Last profile update: <strong class="text-dark">{{ $user->updated_at ? $user->updated_at->diffForHumans() : 'Never' }}</strong></span>
                <span>•</span>
                <span>Super Admin Record: <strong class="text-dark">#{{ $user->id }}</strong></span>
                <span>•</span>
                <span>Tenancy: <strong class="text-primary">Master Global Platform</strong></span>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button type="reset" class="btn btn-sm btn-light text-muted fw-bold" id="btn-reset-bottom">
                    <i class="fa-solid fa-rotate-left me-1"></i> Discard
                </button>
                <button type="button" class="btn btn-sm btn-primary fw-bold js-btn-save-profile" id="save-form-bottom">
                    <span class="indicator-label"><i class="fa-solid fa-floppy-disk me-1"></i> Save Changes</span>
                    <span class="indicator-progress">Saving... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('footer-script')
<script>
    /**
     * Super Admin Profile Management & Client-Side Engine
     * Pure vanilla + jQuery implementation with zero cross-page leakage.
     */
    $(document).ready(function() {

        // ── 0. Bulletproof Manual & Bootstrap Tab Switching ──────────────────────
        $(document).on('click', '.sa-profile-nav-tabs .nav-link', function(e) {
            e.preventDefault();
            var target = $(this).attr('href') || $(this).data('bs-target');
            if (target && target.startsWith('#')) {
                $('.sa-profile-nav-tabs .nav-link').removeClass('active').attr('aria-selected', 'false');
                $(this).addClass('active').attr('aria-selected', 'true');

                $('#saProfileTabContent > .tab-pane').removeClass('show active').css('display', 'none');
                $(target).addClass('show active').css('display', 'block');

                if (typeof $.fn.tab === 'function') {
                    try {
                        $(this).tab('show');
                    } catch (err) {}
                }
            }
        });

        // ── 1. Password Visibility Eye Toggle ────────────────────────────────────
        $(document).on('click', '.js-toggle-pwd', function(e) {
            e.preventDefault();
            var targetSelector = $(this).data('target');
            var $input = $(targetSelector);
            var $icon = $(this).find('i');

            if ($input.attr('type') === 'password') {
                $input.attr('type', 'text');
                $icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                $input.attr('type', 'password');
                $icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // ── 2. Real-Time Password Strength Evaluator ─────────────────────────────
        $('#sa_new_password').on('input', function() {
            var val = $(this).val();
            var strength = 0;

            if (val.length >= 8) strength += 1;
            if (val.match(/[a-z]/) && val.match(/[A-Z]/)) strength += 1;
            if (val.match(/[0-9]/)) strength += 1;
            if (val.match(/[^a-zA-Z0-9]/)) strength += 1;

            var bars = [$('#str_bar_1'), $('#str_bar_2'), $('#str_bar_3'), $('#str_bar_4')];
            var colors = ['#e2e8f0', '#ef4444', '#f59e0b', '#3b82f6', '#10b981'];
            var labels = ['Not entered', 'Weak (Min 8 chars)', 'Fair (Add mixed case)', 'Good (Add numbers/symbols)', 'Strong (Root Quality)'];
            var textColors = ['#64748b', '#ef4444', '#f59e0b', '#3b82f6', '#10b981'];

            bars.forEach(function($bar, idx) {
                if (val.length === 0) {
                    $bar.css('background-color', '#e2e8f0');
                } else if (idx < strength) {
                    $bar.css('background-color', colors[strength]);
                } else {
                    $bar.css('background-color', '#e2e8f0');
                }
            });

            var activeIdx = val.length === 0 ? 0 : strength;
            $('#str_text_label').text(labels[activeIdx]).css('color', textColors[activeIdx]);

            checkPasswordMatch();
        });

        // ── 3. Password Confirmation Match Feedback ──────────────────────────────
        function checkPasswordMatch() {
            var pwd = $('#sa_new_password').val();
            var conf = $('#sa_password_confirmation').val();
            var $feedback = $('#pwd_match_feedback');

            if (!conf && !pwd) {
                $feedback.text('Must match new password exactly').css('color', '#64748b');
                return;
            }

            if (conf === pwd) {
                $feedback.html('<i class="fa-solid fa-circle-check text-success me-1"></i> Passwords match').css('color', '#10b981');
            } else {
                $feedback.html('<i class="fa-solid fa-circle-xmark text-danger me-1"></i> Passwords do not match').css('color', '#ef4444');
            }
        }

        $('#sa_password_confirmation').on('input', checkPasswordMatch);

        // ── 4. Avatar File Input & Instant FileReader Preview ─────────────────────
        var originalAvatarUrl = "{{ $user->profile_image_url }}";

        $('#avatar_file_input').on('change', function(e) {
            var file = this.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire({
                        title: "File Too Large",
                        text: "Profile image must be less than 2MB.",
                        icon: "warning",
                        confirmButtonText: "OK",
                        customClass: { confirmButton: "btn btn-primary fw-bold" }
                    });
                    $(this).val('');
                    return;
                }

                var reader = new FileReader();
                reader.onload = function(evt) {
                    $('#avatar_preview_img').attr('src', evt.target.result);
                    $('#remove_image').val('0');
                };
                reader.readAsDataURL(file);
            }
        });

        // Drag and drop avatar photo
        var $avatarDropZone = $('.sa-avatar-box');
        $avatarDropZone.on('dragover dragenter', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).css('border-color', '#009ef7');
        });

        $avatarDropZone.on('dragleave dragend', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).css('border-color', '#e2e8f0');
        });

        $avatarDropZone.on('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).css('border-color', '#e2e8f0');

            var files = e.originalEvent.dataTransfer.files;
            if (files && files.length > 0) {
                var file = files[0];
                if (file.type.match(/^image\//)) {
                    try {
                        var dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        var fileInput = document.getElementById('avatar_file_input');
                        if (fileInput) {
                            fileInput.files = dataTransfer.files;
                            $('#avatar_file_input').trigger('change');
                        }
                    } catch (err) {
                        // Fallback
                    }
                } else {
                    Swal.fire({
                        title: "Invalid File Type",
                        text: "Please upload an image file (PNG, JPG, JPEG, WEBP).",
                        icon: "warning",
                        confirmButtonText: "OK",
                        customClass: { confirmButton: "btn btn-primary fw-bold" }
                    });
                }
            }
        });

        // ── 4B. Client-Side Draft Recovery (SessionStorage) ──────────────────────
        var DRAFT_STORAGE_KEY = 'sa_profile_draft_{{ $user->id }}';

        function saveProfileDraft() {
            try {
                var draft = {
                    name: $('#sa_name').val(),
                    email: $('#sa_email').val(),
                    whatsapp_number: $('#sa_whatsapp').val(),
                    linkedin: $('#sa_linkedin').val(),
                    calling_code: $('#sa_calling_code').val(),
                    mobile: $('#sa_mobile').val()
                };
                sessionStorage.setItem(DRAFT_STORAGE_KEY, JSON.stringify(draft));
            } catch (e) {}
        }

        function restoreProfileDraft() {
            try {
                var raw = sessionStorage.getItem(DRAFT_STORAGE_KEY);
                if (raw) {
                    var draft = JSON.parse(raw);
                    if (draft && typeof draft === 'object') {
                        if (draft.name) $('#sa_name').val(draft.name);
                        if (draft.email) $('#sa_email').val(draft.email);
                        if (draft.whatsapp_number) $('#sa_whatsapp').val(draft.whatsapp_number);
                        if (draft.linkedin) $('#sa_linkedin').val(draft.linkedin);
                        if (draft.calling_code) $('#sa_calling_code').val(draft.calling_code).trigger('change');
                        if (draft.mobile) $('#sa_mobile').val(draft.mobile);
                    }
                }
            } catch (e) {}
        }

        function clearProfileDraft() {
            try {
                sessionStorage.removeItem(DRAFT_STORAGE_KEY);
            } catch (e) {}
        }

        $('#sa_name, #sa_email, #sa_whatsapp, #sa_linkedin, #sa_mobile').on('input', saveProfileDraft);
        $('#sa_calling_code').on('change', saveProfileDraft);
        restoreProfileDraft();

        // RFC 4122 v4 UUID generator for Two-Tier Idempotency
        function generateUUID() {
            if (typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function') {
                return crypto.randomUUID();
            }
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                var r = Math.random() * 16 | 0, v = c === 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
        }

        // Revert to Default Avatar
        $('#btn_remove_avatar').on('click', function() {
            $('#avatar_file_input').val('');
            $('#remove_image').val('1');
            $('#avatar_preview_img').attr('src', "{{ asset('avatar.png') }}");
        });

        // Discard / Reset Handler
        $('#btn-reset-top, #btn-reset-bottom').on('click', function() {
            clearProfileDraft();
            setTimeout(function() {
                $('#avatar_preview_img').attr('src', originalAvatarUrl);
                $('#remove_image').val('0');
                $('#sa_new_password').trigger('input');
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
            }, 50);
        });

        // ── 5. AJAX Profile Save & Field-Level Error Highlighting ─────────────────
        $(document).on('click', '.js-btn-save-profile', function(e) {
            e.preventDefault();

            var $btn = $(this);
            if ($btn.data('submitting')) {
                return false;
            }

            // Clear previous errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            var form = $('#editSettings')[0];
            var formData = new FormData(form);

            var idempotencyKey = generateUUID();
            formData.append('idempotency_key', idempotencyKey);

            var $saveBtns = $('.js-btn-save-profile');
            $saveBtns.each(function() {
                $(this).data('submitting', true).attr('data-kt-indicator', 'on').prop('disabled', true);
            });

            var url = "{{ route('superadmin.profile.update', $user->id) }}";

            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Idempotency-Key': idempotencyKey
                },
                success: function(response) {
                    $saveBtns.each(function() {
                        $(this).data('submitting', false).removeAttr('data-kt-indicator').prop('disabled', false);
                    });

                    if (response.status === 'success') {
                        clearProfileDraft();

                        // Clear password inputs
                        $('#sa_current_password, #sa_new_password, #sa_password_confirmation').val('');
                        $('#sa_new_password').trigger('input');

                        // Update hero card text
                        if (response.user) {
                            $('#hero_user_name').text(response.user.name);
                            $('#hero_user_email').text(response.user.email);
                            if (response.user.profile_image_url) {
                                originalAvatarUrl = response.user.profile_image_url;
                                $('#avatar_preview_img').attr('src', response.user.profile_image_url);

                                // Sync Topbar Avatar & User Details across Metronic Header
                                $('#kt_header_user_menu_toggle img, .cursor-pointer.symbol img, .symbol-circle img').attr('src', response.user.profile_image_url);
                                if (response.user.name) {
                                    $('#kt_header_user_menu_toggle .fw-bolder').text(response.user.name);
                                }
                                if (response.user.email) {
                                    $('#kt_header_user_menu_toggle a.text-muted.fs-7').text(response.user.email);
                                }
                            }
                        }

                        Swal.fire({
                            title: "Profile Saved!",
                            text: response.message || "Your profile credentials have been updated successfully.",
                            icon: "success",
                            confirmButtonText: "Great, Got It!",
                            customClass: { confirmButton: "btn btn-primary fw-bold" }
                        });
                    } else {
                        Swal.fire({
                            title: "Update Error",
                            text: response.message || "Unable to update profile. Please verify your entries.",
                            icon: "error",
                            confirmButtonText: "OK",
                            customClass: { confirmButton: "btn btn-primary fw-bold" }
                        });
                    }
                },
                error: function(xhr) {
                    $saveBtns.each(function() {
                        $(this).data('submitting', false).removeAttr('data-kt-indicator').prop('disabled', false);
                    });

                    if (xhr.status === 409) {
                        Swal.fire({
                            title: "Request in Progress",
                            text: "Your profile update is currently processing. Please wait a moment.",
                            icon: "warning",
                            confirmButtonText: "OK",
                            customClass: { confirmButton: "btn btn-primary fw-bold" }
                        });
                        return;
                    }

                    if (xhr.status === 422 && xhr.responseJSON) {
                        var errors = xhr.responseJSON.errors || {};
                        var firstErrorField = null;

                        $.each(errors, function(fieldName, errorMessages) {
                            var $input = $('[name="' + fieldName + '"]');
                            if ($input.length) {
                                $input.addClass('is-invalid');
                                var errorMsg = Array.isArray(errorMessages) ? errorMessages[0] : errorMessages;
                                $input.closest('.sa-input-group, .col-md-5, .col-md-7, .col-md-6, .col-12')
                                      .append('<div class="invalid-feedback d-block fs-8 mt-1">' + errorMsg + '</div>');

                                if (!firstErrorField) {
                                    firstErrorField = $input;
                                }
                            }
                        });

                        // Switch to the tab containing the first invalid input
                        if (firstErrorField) {
                            var $tabPane = firstErrorField.closest('.tab-pane');
                            if ($tabPane.length) {
                                var tabId = $tabPane.attr('id');
                                var $tabBtn = $('.sa-profile-nav-tabs .nav-link[href="#' + tabId + '"], [data-bs-target="#' + tabId + '"]');
                                if ($tabBtn.length) {
                                    $tabBtn.trigger('click');
                                }
                            }
                            firstErrorField.focus();
                        }

                        Swal.fire({
                            title: "Validation Error",
                            text: "Please review the highlighted fields and correct the errors.",
                            icon: "error",
                            confirmButtonText: "Review Errors",
                            customClass: { confirmButton: "btn btn-danger fw-bold" }
                        });
                    } else {
                        var errMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "An unexpected error occurred while saving your profile.";
                        Swal.fire({
                            title: "Error",
                            text: errMsg,
                            icon: "error",
                            confirmButtonText: "OK",
                            customClass: { confirmButton: "btn btn-danger fw-bold" }
                        });
                    }
                }
            });
        });

    });
</script>

<!-- Mobile OTP Countdown & Verification Handlers (Route Isolated to superadmin) -->
<script>
    var x = '';

    function clearLocalStorage() {
        localStorage.removeItem('otp_expiry');
        localStorage.removeItem('otp_attempts');
    }

    function checkSessionAndRemove() {
        $.easyAjax({
            url: '{{ route('removeSession') }}',
            type: 'GET',
            data: {'sessions': ['verify:request_id']}
        });
    }

    function startCounter(deadline) {
        x = setInterval(function() {
            var now = new Date().getTime();
            var t = deadline - now;

            var minutes = Math.floor((t % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((t % (1000 * 60)) / 1000);

            $('#demo').html('Time Left: ' + minutes + ":" + (seconds < 10 ? '0' : '') + seconds);
            $('.attempts_left').html(`${localStorage.getItem('otp_attempts')} attempts left`);

            if (t <= 0) {
                clearInterval(x);
                clearLocalStorage();
                checkSessionAndRemove();
                location.href = '{{ route('superadmin.profile.index') }}';
            }
        }, 1000);
    }

    if (localStorage.getItem('otp_expiry') !== null) {
        var localExpiryTime = localStorage.getItem('otp_expiry');
        var now = new Date().getTime();

        if (localExpiryTime - now < 0) {
            clearLocalStorage();
            checkSessionAndRemove();
        } else {
            $('#otp').focus().select();
            startCounter(localStorage.getItem('otp_expiry'));
        }
    }

    function sendOTPRequest() {
        $.easyAjax({
            url: '{{ route('sendOtpCode.account') }}',
            type: 'POST',
            container: '#request-otp-form',
            messagePosition: 'inline',
            data: $('#request-otp-form').serialize(),
            success: function(response) {
                if (response.status == 'success') {
                    localStorage.setItem('otp_attempts', 3);

                    $('#verify-mobile').html(response.view);
                    $('.attempts_left').html(`3 attempts left`);

                    var html = `<div class="alert alert-info mb-0" role="alert">
                        @lang('messages.info.verifyAlert')
                        <a href="{{ route('superadmin.profile.index') }}" class="btn btn-warning btn-sm ms-2">
                            @lang('menu.profile')
                        </a>
                    </div>`;

                    $('#verify-mobile-info').html(html);
                    $('#otp').focus();

                    var now = new Date().getTime();
                    var deadline = new Date(now + parseInt('{{ config('nexmo.settings.pin_expiry') }}') * 1000).getTime();

                    localStorage.setItem('otp_expiry', deadline);
                    startCounter(deadline);
                }
                if (response.status == 'fail') {
                    $('#mobile').focus();
                }
            }
        });
    }

    function sendVerifyRequest() {
        $.easyAjax({
            url: '{{ route('verifyOtpCode.account') }}',
            type: 'POST',
            container: '#verify-otp-form',
            messagePosition: 'inline',
            data: $('#verify-otp-form').serialize(),
            success: function(response) {
                if (response.status == 'success') {
                    clearLocalStorage();

                    $('#verify-mobile').html(response.view);
                    $('#verify-mobile-info').html('');

                    if (typeof $.fn.select2 !== 'undefined') {
                        $('#calling_code').select2();
                    }
                }
                if (response.status == 'fail') {
                    var currentAttempts = localStorage.getItem('otp_attempts');

                    if (currentAttempts == 1) {
                        clearLocalStorage();
                    } else {
                        currentAttempts -= 1;
                        $('.attempts_left').html(`${currentAttempts} attempts left`);
                        $('#otp').focus().select();
                        localStorage.setItem('otp_attempts', currentAttempts);
                    }

                    if (response.data && Object.keys(response.data).length > 0) {
                        $('#verify-mobile').html(response.data.view);
                        clearInterval(x);
                    }
                }
            }
        });
    }

    $('body').on('submit', '#request-otp-form', function(e) {
        e.preventDefault();
        sendOTPRequest();
    });

    $('body').on('click', '#request-otp', function() {
        sendOTPRequest();
    });

    $('body').on('submit', '#verify-otp-form', function(e) {
        e.preventDefault();
        sendVerifyRequest();
    });

    $('body').on('click', '#verify-otp', function() {
        sendVerifyRequest();
    });
</script>
@endpush