@extends('layouts.superadmin_layout')

@php
    use App\Helper\GeneralMethod;
@endphp

@push('head-script')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('assets/plugins/font-awesome/css/font-awesome.min.css') }}">
    <style>
        /* ==========================================================================
           Platform Features & Expiration Policies — Modern Enterprise Card System
           Strictly Optimized for 15.6" Screens (1366x768 & 1920x1080) and Smaller
           GUARANTEED: Zero Horizontal Scrollbar (0% Overflow) Across All Viewports
           ========================================================================== */

        /* ===== Manage Top Gap: collapse Metronic's toolbar-fixed padding and content gap ===== */
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
                padding-top: calc(55px + 8px) !important;
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
        }

        .ft-wrapper {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: hidden !important;
        }

        .ft-wrapper *,
        .ft-wrapper *::before,
        .ft-wrapper *::after,
        #kt_feature_drawer *,
        #kt_feature_drawer *::before,
        #kt_feature_drawer *::after {
            box-sizing: border-box !important;
        }

        /* Ensure FA 4.7 matches both normal and 900 weights if CDN fails */
        @font-face {
            font-family: 'FontAwesome';
            src: url('{{ asset("assets/plugins/font-awesome/fonts/fontawesome-webfont.woff2?v=4.7.0") }}') format('woff2'),
                 url('{{ asset("assets/plugins/font-awesome/fonts/fontawesome-webfont.woff?v=4.7.0") }}') format('woff'),
                 url('{{ asset("assets/plugins/font-awesome/fonts/fontawesome-webfont.ttf?v=4.7.0") }}') format('truetype');
            font-weight: 900;
            font-style: normal;
        }

        /* Protect FontAwesome icon fonts from general typography font-family inheritance */
        .ft-wrapper i,
        .ft-wrapper [class*="fa-"],
        .ft-wrapper [class^="fa-"],
        .ft-wrapper .fa,
        .ft-wrapper .fas,
        .ft-wrapper .fa-solid,
        #kt_feature_drawer i,
        #kt_feature_drawer [class*="fa-"],
        #kt_feature_drawer [class^="fa-"],
        #kt_feature_drawer .fa,
        #kt_feature_drawer .fas,
        #kt_feature_drawer .fa-solid {
            font-family: "Font Awesome 6 Free", "FontAwesome", sans-serif !important;
            font-style: normal !important;
            font-weight: 900 !important;
            display: inline-block !important;
            line-height: 1 !important;
            text-rendering: auto !important;
            -webkit-font-smoothing: antialiased !important;
            -moz-osx-font-smoothing: grayscale !important;
        }

        /* Universal glyph content guarantees: maps every FA6 class to its Unicode glyph */
        .fa-magnifying-glass:before { content: "\f002" !important; }
        .fa-xmark:before            { content: "\f00d" !important; }
        .fa-pen-to-square:before    { content: "\f044" !important; }
        .fa-clock-rotate-left:before{ content: "\f1da" !important; }
        .fa-circle-check:before     { content: "\f058" !important; }
        .fa-circle-pause:before     { content: "\f28b" !important; }
        .fa-circle-info:before      { content: "\f05a" !important; }
        .fa-shield-halved:before    { content: "\f132" !important; }
        .fa-list-check:before       { content: "\f0ae" !important; }
        .fa-rotate-left:before      { content: "\f0e2" !important; }
        .fa-pen-nib:before          { content: "\f040" !important; }
        .fa-signature:before        { content: "\f044" !important; }
        .fa-calendar-day:before     { content: "\f073" !important; }
        .fa-power-off:before        { content: "\f011" !important; }
        .fa-wand-magic-sparkles:before { content: "\f0d0" !important; }
        .fa-triangle-exclamation:before { content: "\f071" !important; }
        .fa-arrow-left:before       { content: "\f060" !important; }
        .fa-sliders:before          { content: "\f1de" !important; }
        .fa-plus:before             { content: "\f067" !important; }
        .fa-check:before            { content: "\f00c" !important; }
        .fa-coins:before            { content: "\f0d6" !important; }
        .fa-database:before         { content: "\f1c0" !important; }
        .fa-globe:before            { content: "\f0ac" !important; }
        .fa-robot:before            { content: "\f085" !important; }
        .fa-layer-group:before      { content: "\f009" !important; }
        .fa-bolt:before             { content: "\f0e7" !important; }
        .fa-briefcase:before        { content: "\f0b1" !important; }
        .fa-handshake:before        { content: "\f2b5" !important; }
        .fa-user-clock:before       { content: "\f017" !important; }
        .fa-store:before            { content: "\f07a" !important; }
        .fa-filter:before           { content: "\f0b0" !important; }

        /* ==========================================================================
           Tier 1: Top Header Toolbar Card
           ========================================================================== */
        .ft-header-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 12px 18px !important;
            margin-bottom: 12px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 14px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
            flex-wrap: wrap !important;
            width: 100% !important;
        }

        .ft-header-left {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            min-width: 0 !important;
            flex-wrap: wrap !important;
        }

        .ft-header-icon {
            width: 40px !important;
            height: 40px !important;
            border-radius: 9px !important;
            background: #eff6ff !important;
            border: 1px solid #bfdbfe !important;
            color: #2563eb !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 18px !important;
            flex-shrink: 0 !important;
        }

        .ft-header-titles {
            display: flex !important;
            flex-direction: column !important;
        }

        .ft-header-title {
            font-size: 15.5px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            line-height: 1.25 !important;
            letter-spacing: -0.2px !important;
        }

        .ft-header-sub {
            font-size: 11.5px !important;
            color: #64748b !important;
            margin-top: 2px !important;
            line-height: 1.2 !important;
        }

        /* Header Metric Chips */
        .ft-header-chips {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            margin-left: 8px !important;
            flex-wrap: wrap !important;
        }

        .ft-kpi-chip {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            padding: 3px 9px !important;
            border-radius: 6px !important;
            line-height: 1.2 !important;
        }

        .ft-kpi-chip.chip-total {
            background: #f1f5f9 !important;
            color: #475569 !important;
            border: 1px solid #e2e8f0 !important;
        }

        .ft-kpi-chip.chip-active {
            background: #ecfdf5 !important;
            color: #059669 !important;
            border: 1px solid #a7f3d0 !important;
        }

        .ft-kpi-chip.chip-inactive {
            background: #f8fafc !important;
            color: #94a3b8 !important;
            border: 1px solid #e2e8f0 !important;
        }

        .ft-kpi-chip.chip-expirations {
            background: #eff6ff !important;
            color: #2563eb !important;
            border: 1px solid #bfdbfe !important;
        }

        .ft-kpi-chip.chip-billing {
            background: #fffbeb !important;
            color: #d97706 !important;
            border: 1px solid #fef08a !important;
        }

        .ft-header-right {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            flex-wrap: wrap !important;
        }

        /* Search Sub-Card inside Top Header Card */
        .ft-search-subcard {
            display: flex !important;
            align-items: center !important;
            gap: 9px !important;
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 0 12px !important;
            height: 36px !important;
            width: 290px !important;
            max-width: 100% !important;
            transition: all 0.15s ease-in-out !important;
        }

        .ft-search-subcard:focus-within {
            background: #ffffff !important;
            border-color: #009ef7 !important;
            box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
        }

        .ft-search-subcard i.fa-magnifying-glass {
            color: #94a3b8 !important;
            font-size: 12.5px !important;
            flex-shrink: 0 !important;
        }

        .ft-search-subcard input {
            border: none !important;
            background: transparent !important;
            outline: none !important;
            box-shadow: none !important;
            font-size: 12px !important;
            color: #0f172a !important;
            width: 100% !important;
            padding: 0 !important;
            line-height: 36px !important;
            font-family: inherit !important;
        }

        .ft-search-subcard input::placeholder {
            color: #94a3b8 !important;
            font-size: 12px !important;
        }

        .ft-search-clear {
            background: none !important;
            border: none !important;
            color: #94a3b8 !important;
            font-size: 12px !important;
            padding: 0 !important;
            cursor: pointer !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .ft-search-clear:hover {
            color: #475569 !important;
        }

        .ft-add-btn {
            height: 36px !important;
            padding: 0 14px !important;
            font-size: 12px !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            border-radius: 8px !important;
            white-space: nowrap !important;
            background: #009ef7 !important;
            border-color: #009ef7 !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            transition: all 0.15s ease !important;
        }

        .ft-add-btn:hover {
            background: #0086d6 !important;
            border-color: #0086d6 !important;
            color: #ffffff !important;
        }

        /* ==========================================================================
           Tier 2: Sub-Tabs Filter Card
           ========================================================================== */
        .ft-tabs-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 8px 12px !important;
            margin-bottom: 14px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 10px !important;
            flex-wrap: wrap !important;
            width: 100% !important;
        }

        .ft-tabs-left {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            flex-wrap: wrap !important;
        }

        .ft-tab-btn {
            border: 1px solid #e2e8f0 !important;
            background: #ffffff !important;
            color: #475569 !important;
            font-size: 11.5px !important;
            font-weight: 600 !important;
            padding: 6px 12px !important;
            border-radius: 8px !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            cursor: pointer !important;
            transition: all 0.15s ease-in-out !important;
            text-decoration: none !important;
            line-height: 1.2 !important;
        }

        .ft-tab-btn:hover {
            background: #f8fafc !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }

        /* Strict Law: Single Active Tab Blue Highlight */
        .ft-tab-btn.active {
            background: #009ef7 !important;
            border-color: #009ef7 !important;
            color: #ffffff !important;
            box-shadow: 0 2px 4px rgba(0, 158, 247, 0.25) !important;
        }

        .ft-tab-badge {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 10.5px !important;
            font-weight: 700 !important;
            padding: 1px 6px !important;
            border-radius: 10px !important;
            background: #f1f5f9 !important;
            color: #475569 !important;
            line-height: 1.2 !important;
            min-width: 18px !important;
        }

        .ft-tab-btn.active .ft-tab-badge {
            background: rgba(255, 255, 255, 0.28) !important;
            color: #ffffff !important;
        }

        .ft-tabs-right {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
        }

        .ft-meta-badge {
            font-size: 11px !important;
            color: #64748b !important;
            font-weight: 500 !important;
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            padding: 4px 10px !important;
            border-radius: 6px !important;
        }

        /* ==========================================================================
           Tier 3: Modern Card Stream & Discrete Sub-Cards
           ========================================================================== */
        .ft-stream {
            display: flex !important;
            flex-direction: column !important;
            gap: 12px !important;
            width: 100% !important;
        }

        .ft-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
            overflow: hidden !important;
            width: 100% !important;
        }

        .ft-card:hover {
            border-color: #cbd5e1 !important;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.06) !important;
            transform: translateY(-1px) !important;
        }

        .ft-card.status-inactive {
            background: #fafafa !important;
            border-color: #f1f5f9 !important;
        }

        .ft-card-grid {
            display: grid !important;
            grid-template-columns: minmax(0, 3fr) minmax(0, 1.8fr) minmax(0, 1.8fr) minmax(0, 1.4fr) !important;
            gap: 12px !important;
            padding: 14px 18px !important;
            align-items: center !important;
            width: 100% !important;
        }

        @media (max-width: 1200px) {
            .ft-card-grid {
                grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) !important;
                gap: 14px !important;
            }
        }

        @media (max-width: 768px) {
            .ft-card-grid {
                grid-template-columns: minmax(0, 1fr) !important;
                gap: 12px !important;
            }
        }

        /* Sub-Card 1: Policy Identity & Scope */
        .ft-subcard-identity {
            display: flex !important;
            align-items: flex-start !important;
            gap: 12px !important;
            min-width: 0 !important;
        }

        .ft-icon-box {
            width: 38px !important;
            height: 38px !important;
            border-radius: 9px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 16px !important;
            flex-shrink: 0 !important;
            margin-top: 2px !important;
        }

        .ft-identity-info {
            display: flex !important;
            flex-direction: column !important;
            min-width: 0 !important;
            gap: 3px !important;
        }

        .ft-meta-row {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            flex-wrap: wrap !important;
        }

        .ft-id-pill {
            font-size: 10.5px !important;
            font-weight: 700 !important;
            background: #f1f5f9 !important;
            color: #475569 !important;
            border: 1px solid #e2e8f0 !important;
            padding: 1px 6px !important;
            border-radius: 5px !important;
            line-height: 1.2 !important;
        }

        .ft-scope-pill {
            font-size: 10px !important;
            font-weight: 600 !important;
            padding: 2px 7px !important;
            border-radius: 5px !important;
            line-height: 1.2 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.3px !important;
        }

        .scope-ats {
            background: #eff6ff !important;
            color: #1d4ed8 !important;
            border: 1px solid #bfdbfe !important;
        }

        .scope-client {
            background: #f0fdf4 !important;
            color: #15803d !important;
            border: 1px solid #bbf7d0 !important;
        }

        .scope-bench {
            background: #faf5ff !important;
            color: #7e22ce !important;
            border: 1px solid #e9d5ff !important;
        }

        .scope-billing {
            background: #fffbeb !important;
            color: #b45309 !important;
            border: 1px solid #fef08a !important;
        }

        .scope-marketplace {
            background: #ecfdf5 !important;
            color: #047857 !important;
            border: 1px solid #a7f3d0 !important;
        }

        .scope-general {
            background: #f8fafc !important;
            color: #475569 !important;
            border: 1px solid #e2e8f0 !important;
        }

        .ft-policy-title {
            font-size: 13.5px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            line-height: 1.3 !important;
            text-decoration: none !important;
            cursor: pointer !important;
            transition: color 0.15s ease !important;
        }

        .ft-policy-title:hover {
            color: #009ef7 !important;
        }

        .ft-policy-desc {
            font-size: 11px !important;
            color: #64748b !important;
            line-height: 1.35 !important;
            margin-top: 1px !important;
        }

        /* Sub-Card 2 & 3: Metric & Governance Sub-Cards */
        .ft-subcard-cell {
            display: flex !important;
            flex-direction: column !important;
            gap: 4px !important;
        }

        .ft-micro-label {
            font-size: 10px !important;
            font-weight: 700 !important;
            color: #94a3b8 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.4px !important;
            line-height: 1.2 !important;
        }

        .ft-duration-pill {
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            padding: 4px 10px !important;
            border-radius: 6px !important;
            font-size: 11.5px !important;
            font-weight: 600 !important;
            line-height: 1.2 !important;
            width: fit-content !important;
        }

        .duration-days {
            background: #eff6ff !important;
            color: #1d4ed8 !important;
            border: 1px solid #bfdbfe !important;
        }

        .duration-immediate {
            background: #fffbeb !important;
            color: #b45309 !important;
            border: 1px solid #fef08a !important;
        }

        .ft-subcard-hint {
            font-size: 10.5px !important;
            color: #64748b !important;
            line-height: 1.2 !important;
        }

        .ft-cron-pill {
            display: inline-flex !important;
            align-items: center !important;
            gap: 5px !important;
            padding: 4px 10px !important;
            border-radius: 6px !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            line-height: 1.2 !important;
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            color: #334155 !important;
            width: fit-content !important;
        }

        /* Sub-Card 4: Actions & Toggle */
        .ft-subcard-actions {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
            gap: 12px !important;
        }

        .ft-switch-wrapper {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
        }

        .ft-switch-label {
            font-size: 11.5px !important;
            font-weight: 600 !important;
            color: #0f172a !important;
            min-width: 50px !important;
        }

        .ft-switch-label.text-inactive {
            color: #94a3b8 !important;
        }

        .ft-edit-btn {
            width: 32px !important;
            height: 32px !important;
            border-radius: 7px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: #f1f5f9 !important;
            border: 1px solid #e2e8f0 !important;
            color: #475569 !important;
            font-size: 12.5px !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
            text-decoration: none !important;
        }

        .ft-edit-btn:hover {
            background: #009ef7 !important;
            border-color: #009ef7 !important;
            color: #ffffff !important;
        }

        /* Sub-Card 5: Bottom Activity Strip */
        .ft-activity-strip {
            background: #f8fafc !important;
            border-top: 1px solid #f1f5f9 !important;
            padding: 7px 18px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            font-size: 10.5px !important;
            color: #64748b !important;
            flex-wrap: wrap !important;
            gap: 8px !important;
        }

        .ft-activity-left {
            display: flex !important;
            align-items: center !important;
            gap: 14px !important;
            flex-wrap: wrap !important;
        }

        .ft-activity-item {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
        }

        /* Empty Search / Filter State */
        .ft-empty-state {
            background: #ffffff !important;
            border: 1px dashed #cbd5e1 !important;
            border-radius: 10px !important;
            padding: 40px 20px !important;
            text-align: center !important;
            width: 100% !important;
        }

        .ft-empty-icon {
            font-size: 32px !important;
            color: #94a3b8 !important;
            margin-bottom: 10px !important;
        }

        .ft-empty-title {
            font-size: 14.5px !important;
            font-weight: 700 !important;
            color: #1e293b !important;
            margin-bottom: 4px !important;
        }

        .ft-empty-sub {
            font-size: 12px !important;
            color: #64748b !important;
        }
    </style>
@endpush

@section('content')
<div class="ft-wrapper px-3 py-0">

    <!-- =========================================================================
         TIER 1: TOP HEADER TOOLBAR CARD
         ========================================================================= -->
    <div class="ft-header-card">
        <div class="ft-header-left">
            <div class="ft-header-icon">
                <i class="fa fa-sliders fa-solid fa-sliders"></i>
            </div>
            <div class="ft-header-titles">
                <span class="ft-header-title">Platform Feature Policies & Expiration Rules</span>
                <span class="ft-header-sub">Configure ATS automated request expirations, validity windows, and platform gating rules.</span>
            </div>
            <div class="ft-header-chips">
                <span class="ft-kpi-chip chip-total" title="Total Platform Feature Policies">
                    <i class="fa fa-th-large fa-solid fa-layer-group"></i> Total: {{ $totalFeaturesCount ?? count($th_features) }}
                </span>
                <span class="ft-kpi-chip chip-active" title="Currently Active Policies">
                    <i class="fa fa-check-circle fa-solid fa-circle-check"></i> Active: {{ $activeFeaturesCount ?? 0 }}
                </span>
                <span class="ft-kpi-chip chip-inactive" title="Inactive Policies">
                    <i class="fa fa-pause-circle fa-solid fa-circle-pause"></i> Inactive: {{ $inactiveFeaturesCount ?? 0 }}
                </span>
                <span class="ft-kpi-chip chip-expirations" title="Automated Expiration Policies">
                    <i class="fa fa-history fa-solid fa-clock-rotate-left"></i> Expirations: {{ $expirationsCount ?? 0 }}
                </span>
                <span class="ft-kpi-chip chip-billing" title="Billing & Marketplace Policies">
                    <i class="fa fa-money fa-coins fa-solid fa-coins"></i> Billing: {{ $billingCount ?? 0 }}
                </span>
            </div>
        </div>

        <div class="ft-header-right">
            <!-- Dynamic Search Sub-Card -->
            <div class="ft-search-subcard">
                <i class="fa fa-search fa-solid fa-magnifying-glass"></i>
                <input type="text" id="ftSearchInput" placeholder="Search policies (title, days, scope)..." autocomplete="off" />
                <button type="button" class="ft-search-clear d-none" id="ftClearSearch" title="Clear Search">
                    <i class="fa fa-times fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Primary Action Button -->
            <a href="{{ route('superadmin.company.featuresListupdate', 0) }}" class="btn ft-add-btn" id="openCreateFeatureBtn">
                <i class="fa fa-plus fa-solid fa-plus"></i> Add Feature Policy
            </a>
        </div>
    </div>

    <!-- =========================================================================
         TIER 2: SUB-TABS FILTER CARD
         ========================================================================= -->
    <div class="ft-tabs-card">
        <div class="ft-tabs-left" id="ftTabsNav">
            <button type="button" class="ft-tab-btn active" data-filter="all">
                <i class="fa fa-tasks fa-solid fa-list-check"></i> All Policies
                <span class="ft-tab-badge" id="badgeAllCount">{{ count($th_features) }}</span>
            </button>
            <button type="button" class="ft-tab-btn" data-filter="active">
                <i class="fa fa-check-circle fa-solid fa-circle-check text-success"></i> Active
                <span class="ft-tab-badge" id="badgeActiveCount">{{ $activeFeaturesCount ?? 0 }}</span>
            </button>
            <button type="button" class="ft-tab-btn" data-filter="inactive">
                <i class="fa fa-pause-circle fa-solid fa-circle-pause text-muted"></i> Inactive
                <span class="ft-tab-badge" id="badgeInactiveCount">{{ $inactiveFeaturesCount ?? 0 }}</span>
            </button>
            <button type="button" class="ft-tab-btn" data-filter="expirations">
                <i class="fa fa-history fa-solid fa-clock-rotate-left text-primary"></i> Request Expirations
                <span class="ft-tab-badge" id="badgeExpirationsCount">{{ $expirationsCount ?? 0 }}</span>
            </button>
            <button type="button" class="ft-tab-btn" data-filter="billing">
                <i class="fa fa-money fa-coins fa-solid fa-coins text-warning"></i> Marketplace & Billing
                <span class="ft-tab-badge" id="badgeBillingCount">{{ $billingCount ?? 0 }}</span>
            </button>
        </div>

        <div class="ft-tabs-right">
            <span class="ft-meta-badge">
                <i class="fa fa-filter fa-solid fa-filter me-1 text-primary"></i> Showing <strong id="ftVisibleCount">{{ count($th_features) }}</strong> of {{ count($th_features) }}
            </span>
        </div>
    </div>

    <!-- =========================================================================
         TIER 3: PRIMARY CARD STREAM
         ========================================================================= -->
    <div id="ftStreamContainer">
        <div class="ft-stream" id="ftCardsStream">
            @forelse ($th_features as $val_tem)
                @php
                    $lowerTitle = strtolower($val_tem->title);
                    $category = 'general';
                    $scopeBadge = 'System Policy';
                    $scopeClass = 'scope-general';
                    $iconClass = 'fa-sliders';
                    $fa4Class = 'fa-sliders';
                    $iconBg = '#eff6ff';
                    $iconBorder = '#bfdbfe';
                    $iconColor = '#2563eb';
                    $description = 'System-wide feature rule governing operational workflow availability.';
                    $governanceNote = 'Evaluated during ATS operations';

                    if (strpos($lowerTitle, 'job request') !== false) {
                        $category = 'expirations';
                        $scopeBadge = 'ATS Job Requests';
                        $scopeClass = 'scope-ats';
                        $iconClass = 'fa-briefcase';
                        $fa4Class = 'fa-briefcase';
                        $iconBg = '#eff6ff';
                        $iconBorder = '#bfdbfe';
                        $iconColor = '#2563eb';
                        $description = 'Automatically expires and closes pending candidate job sharing requests between organizations after ' . $val_tem->days . ' days.';
                        $governanceNote = 'Cron Automated • ATS Pipeline (HomeController:13116)';
                    } elseif (strpos($lowerTitle, 'client request') !== false) {
                        $category = 'expirations';
                        $scopeBadge = 'Client Requisitions';
                        $scopeClass = 'scope-client';
                        $iconClass = 'fa-handshake';
                        $fa4Class = 'fa-handshake-o';
                        $iconBg = '#eff6ff';
                        $iconBorder = '#bfdbfe';
                        $iconColor = '#2563eb';
                        $description = 'Automatically expires client assignment requests if left unaccepted after ' . $val_tem->days . ' days.';
                        $governanceNote = 'Cron Automated • Client Engagements (HomeController:13161)';
                    } elseif (strpos($lowerTitle, 'bench candidate') !== false || strpos($lowerTitle, 'experience') !== false) {
                        $category = 'expirations';
                        $scopeBadge = 'Bench Talent Requests';
                        $scopeClass = 'scope-bench';
                        $iconClass = 'fa-user-clock';
                        $fa4Class = 'fa-user';
                        $iconBg = '#faf5ff';
                        $iconBorder = '#e9d5ff';
                        $iconColor = '#7e22ce';
                        $description = 'Enforces validity expiration window for bench talent allocation and deployment requests.';
                        $governanceNote = 'Cron Automated • Bench Talent Pool (HomeController:13207)';
                    } elseif (strpos($lowerTitle, 'credit deducted') !== false || strpos($lowerTitle, 'resent job') !== false) {
                        $category = 'billing';
                        $scopeBadge = 'Coin Billing Gate';
                        $scopeClass = 'scope-billing';
                        $iconClass = 'fa-coins';
                        $fa4Class = 'fa-money';
                        $iconBg = '#fffbeb';
                        $iconBorder = '#fef08a';
                        $iconColor = '#d97706';
                        $description = 'Governs coin debit policies and wallet gating when resending national or international job requisitions.';
                        $governanceNote = 'Real-time Event Gated • ServiceCompanyJobsController:12225';
                    } elseif (strpos($lowerTitle, 'market place') !== false || strpos($lowerTitle, 'unpublish') !== false) {
                        $category = 'billing';
                        $scopeBadge = 'Talent Marketplace';
                        $scopeClass = 'scope-marketplace';
                        $iconClass = 'fa-store';
                        $fa4Class = 'fa-shopping-cart';
                        $iconBg = '#ecfdf5';
                        $iconBorder = '#a7f3d0';
                        $iconColor = '#059669';
                        $description = 'Controls automated delisting and archival of stagnant marketplace job opportunities.';
                        $governanceNote = 'Cron Automated • Marketplace Engine';
                    }
                @endphp

                <div class="ft-card {{ $val_tem->status == 1 ? 'status-active' : 'status-inactive' }}"
                     data-id="{{ $val_tem->id }}"
                     data-title="{{ strtolower($val_tem->title) }}"
                     data-status="{{ $val_tem->status == 1 ? 'active' : 'inactive' }}"
                     data-category="{{ $category }}"
                     data-days="{{ $val_tem->days }}">

                    <div class="ft-card-grid">
                        <!-- Sub-Card 1: Policy Identity & Scope -->
                        <div class="ft-subcard-identity">
                            <div class="ft-icon-box" style="background: {{ $iconBg }}; border: 1px solid {{ $iconBorder }}; color: {{ $iconColor }};">
                                <i class="fa {{ $fa4Class }} fa-solid {{ $iconClass }}"></i>
                            </div>
                            <div class="ft-identity-info">
                                <div class="ft-meta-row">
                                    <span class="ft-id-pill">#FTR-00{{ $val_tem->id }}</span>
                                    <span class="ft-scope-pill {{ $scopeClass }}">{{ $scopeBadge }}</span>
                                </div>
                                <a href="{{ route('superadmin.company.featuresListupdate', $val_tem->id) }}" class="ft-policy-title">
                                    {{ $val_tem->title }}
                                </a>
                                <span class="ft-policy-desc">{{ $description }}</span>
                            </div>
                        </div>

                        <!-- Sub-Card 2: Duration & Validity Rule -->
                        <div class="ft-subcard-cell">
                            <span class="ft-micro-label">Expiration / Validity Rule</span>
                            @if ((int)$val_tem->days > 0)
                                <span class="ft-duration-pill duration-days">
                                    <i class="fa fa-history fa-solid fa-clock-rotate-left"></i> {{ $val_tem->days }} Days Window
                                </span>
                                <span class="ft-subcard-hint">Auto-closes after {{ $val_tem->days }} inactive days</span>
                            @else
                                <span class="ft-duration-pill duration-immediate">
                                    <i class="fa fa-bolt fa-solid fa-bolt"></i> Immediate (0 Days)
                                </span>
                                <span class="ft-subcard-hint">Real-time event evaluation</span>
                            @endif
                        </div>

                        <!-- Sub-Card 3: System Governance -->
                        <div class="ft-subcard-cell">
                            <span class="ft-micro-label">System Governance</span>
                            <span class="ft-cron-pill">
                                <i class="fa fa-cogs fa-solid fa-robot text-success"></i> Automated Platform Rule
                            </span>
                            <span class="ft-subcard-hint">{{ $governanceNote }}</span>
                        </div>

                        <!-- Sub-Card 4: Actions & Quick Toggle -->
                        <div class="ft-subcard-actions">
                            <div class="ft-switch-wrapper">
                                <span class="ft-switch-label {{ $val_tem->status == 1 ? 'text-success' : 'text-inactive' }}" id="statusLabel_{{ $val_tem->id }}">
                                    {{ $val_tem->status == 1 ? 'Active' : 'Inactive' }}
                                </span>
                                <div class="form-check form-switch form-check-custom form-check-solid">
                                    <input class="form-check-input ft-toggle-switch"
                                           type="checkbox"
                                           role="switch"
                                           data-id="{{ $val_tem->id }}"
                                           {{ $val_tem->status == 1 ? 'checked' : '' }} />
                                </div>
                            </div>
                            <a href="{{ route('superadmin.company.featuresListupdate', $val_tem->id) }}"
                               class="ft-edit-btn"
                               title="Edit Feature Policy">
                                <i class="fa fa-pencil-square-o fa-edit fa-solid fa-pen-to-square"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Sub-Card 5: Bottom Activity Strip -->
                    <div class="ft-activity-strip">
                        <div class="ft-activity-left">
                            <span class="ft-activity-item">
                                <i class="fa fa-database fa-solid fa-database text-primary"></i> Database Record: <strong>#{{ $val_tem->id }}</strong>
                            </span>
                            <span class="ft-activity-item">
                                <i class="fa fa-globe fa-solid fa-globe text-info"></i> Tenancy: <strong>Global Multi-Tenant Platform Scope</strong>
                            </span>
                        </div>
                        <div class="ft-activity-right">
                            <span class="ft-activity-item">
                                <i class="fa fa-shield fa-solid fa-shield-halved text-success"></i> Enforced by JERO Core Governance
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="ft-empty-state">
                    <div class="ft-empty-icon"><i class="fa fa-sliders fa-solid fa-sliders"></i></div>
                    <div class="ft-empty-title">No Platform Feature Policies Found</div>
                    <div class="ft-empty-sub">There are no feature policies configured in the platform.</div>
                </div>
            @endforelse
        </div>

        <!-- Dynamic Empty State when Search or Filter matches zero items -->
        <div class="ft-empty-state d-none" id="ftNoResultsState">
            <div class="ft-empty-icon"><i class="fa fa-search fa-solid fa-magnifying-glass"></i></div>
            <div class="ft-empty-title">No Matching Policies Found</div>
            <div class="ft-empty-sub">No feature policies match your search or filter criteria. Try resetting filters.</div>
            <button type="button" class="btn btn-sm btn-light-primary mt-3" id="ftResetFiltersBtn">
                <i class="fa fa-undo fa-rotate-left fa-solid fa-rotate-left me-1"></i> Reset Filters
            </button>
        </div>
    </div>

    <!-- =========================================================================
         IN-PLACE SLIDE-OVER DRAWER (Metronic 8)
         ========================================================================= -->
    <div id="kt_feature_drawer"
         class="bg-white shadow"
         data-kt-drawer="true"
         data-kt-drawer-name="feature_drawer"
         data-kt-drawer-activate="true"
         data-kt-drawer-overlay="true"
         data-kt-drawer-width="{default:'340px', 'md': '460px', 'lg': '500px'}"
         data-kt-drawer-direction="end"
         data-kt-drawer-toggle="#kt_feature_drawer_toggle"
         data-kt-drawer-close="#kt_feature_drawer_close">
        <div class="card shadow-none rounded-0 w-100 h-100">
            <!-- Drawer Header -->
            <div class="card-header px-6 py-4 border-bottom border-gray-200 d-flex align-items-center justify-content-between" id="kt_feature_drawer_header">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge badge-light-primary p-2 rounded-2">
                        <i class="fa fa-sliders fa-solid fa-sliders text-primary fs-5"></i>
                    </span>
                    <h3 class="card-title fw-bolder text-dark fs-5 mb-0" id="ftDrawerHeaderTitle">
                        Policy Configuration
                    </h3>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary" id="kt_feature_drawer_close" title="Close Drawer">
                        <i class="fa fa-times fa-solid fa-xmark fs-4 text-muted"></i>
                    </button>
                </div>
            </div>

            <!-- Drawer Body (Loaded dynamically via AJAX) -->
            <div class="card-body position-relative p-0 flex-grow-1" id="kt_feature_drawer_body" style="overflow-y: auto;">
                <div class="d-flex align-items-center justify-content-center p-10 text-muted">
                    <span class="spinner-border spinner-border-sm me-2 text-primary"></span> Loading policy details...
                </div>
            </div>
        </div>
    </div>
    <!-- Invisible Trigger Element -->
    <div id="kt_feature_drawer_toggle" class="d-none"></div>

</div>
@endsection

@push('footer-script')
<script>
    (function() {
        'use strict';

        // =========================================================================
        // Phase 5: High-Performance Client-Side JS Controller
        // 10K Concurrency / 0% Server Strain Target (Client RAM First)
        // =========================================================================

        const searchInput = document.getElementById('ftSearchInput');
        const clearBtn = document.getElementById('ftClearSearch');
        const tabsNav = document.getElementById('ftTabsNav');
        const tabBtns = document.querySelectorAll('.ft-tab-btn');
        const visibleCountSpan = document.getElementById('ftVisibleCount');
        const noResultsState = document.getElementById('ftNoResultsState');
        const resetFiltersBtn = document.getElementById('ftResetFiltersBtn');
        const cardsStream = document.getElementById('ftCardsStream');

        // Drawer Elements
        const drawerEl = document.getElementById('kt_feature_drawer');
        const drawerBody = document.getElementById('kt_feature_drawer_body');
        const drawerHeaderTitle = document.getElementById('ftDrawerHeaderTitle');
        const drawerToggleTrigger = document.getElementById('kt_feature_drawer_toggle');
        const drawerCloseBtn = document.getElementById('kt_feature_drawer_close');
        const openCreateBtn = document.getElementById('openCreateFeatureBtn');

        let activeFilter = 'all';
        let searchDebounceTimer = null;
        let featureDrawerInstance = null;

        // Initialize Metronic Drawer instance if available
        function getDrawerInstance() {
            if (!featureDrawerInstance && typeof KTDrawer !== 'undefined' && drawerEl) {
                featureDrawerInstance = KTDrawer.getInstance(drawerEl);
                if (!featureDrawerInstance) {
                    featureDrawerInstance = new KTDrawer(drawerEl);
                }
            }
            return featureDrawerInstance;
        }

        function openDrawer(titleText) {
            if (drawerHeaderTitle && titleText) {
                drawerHeaderTitle.textContent = titleText;
            }
            const drawer = getDrawerInstance();
            if (drawer) {
                drawer.show();
            } else if (drawerToggleTrigger) {
                drawerToggleTrigger.click();
            }
        }

        function closeDrawer() {
            const drawer = getDrawerInstance();
            if (drawer) {
                drawer.hide();
            } else if (drawerCloseBtn) {
                drawerCloseBtn.click();
            }
        }

        // =========================================================================
        // 1. Instant 300ms Debounced Search & Sub-Tab Filtering (In-Memory RAM)
        // =========================================================================

        function applyFiltering() {
            const query = (searchInput ? searchInput.value : '').toLowerCase().trim();
            const cards = document.querySelectorAll('.ft-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const cardTitle = card.getAttribute('data-title') || '';
                const cardStatus = card.getAttribute('data-status') || '';
                const cardCategory = card.getAttribute('data-category') || '';
                const cardDays = parseInt(card.getAttribute('data-days') || '0', 10);
                const cardId = card.getAttribute('data-id') || '';

                // Category filter check
                let matchesCategory = false;
                if (activeFilter === 'all') {
                    matchesCategory = true;
                } else if (activeFilter === 'active') {
                    matchesCategory = (cardStatus === 'active');
                } else if (activeFilter === 'inactive') {
                    matchesCategory = (cardStatus === 'inactive');
                } else if (activeFilter === 'expirations') {
                    matchesCategory = (cardCategory === 'expirations' || cardDays > 0);
                } else if (activeFilter === 'billing') {
                    matchesCategory = (cardCategory === 'billing');
                }

                // Search query check
                let matchesSearch = true;
                if (query.length > 0) {
                    const textContent = (card.textContent || '').toLowerCase();
                    matchesSearch = cardTitle.includes(query) ||
                                    cardId.includes(query) ||
                                    cardDays.toString().includes(query) ||
                                    textContent.includes(query);
                }

                const isVisible = matchesCategory && matchesSearch;
                card.classList.toggle('d-none', !isVisible);

                if (isVisible) {
                    visibleCount++;
                }
            });

            // Update visible counter
            if (visibleCountSpan) {
                visibleCountSpan.textContent = visibleCount;
            }

            // Toggle empty state card
            if (noResultsState) {
                noResultsState.classList.toggle('d-none', visibleCount > 0);
            }
        }

        // Debounced Search Input
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const val = this.value.trim();
                if (clearBtn) {
                    clearBtn.classList.toggle('d-none', val.length === 0);
                }

                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(() => {
                    applyFiltering();
                }, 300);
            });
        }

        // Clear Search Button
        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                if (searchInput) {
                    searchInput.value = '';
                    searchInput.focus();
                }
                clearBtn.classList.add('d-none');
                applyFiltering();
            });
        }

        // Sub-Tabs Switching (Strict Law: Single Active Tab Blue Highlight #009ef7)
        if (tabsNav) {
            tabsNav.addEventListener('click', function(e) {
                const btn = e.target.closest('.ft-tab-btn');
                if (!btn) return;

                tabBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                activeFilter = btn.getAttribute('data-filter') || 'all';

                applyFiltering();
            });
        }

        // Reset Filters Action on Empty State
        if (resetFiltersBtn) {
            resetFiltersBtn.addEventListener('click', function() {
                if (searchInput) {
                    searchInput.value = '';
                }
                if (clearBtn) {
                    clearBtn.classList.add('d-none');
                }
                tabBtns.forEach(b => b.classList.remove('active'));
                const allBtn = document.querySelector('.ft-tab-btn[data-filter="all"]');
                if (allBtn) allBtn.classList.add('active');
                activeFilter = 'all';

                applyFiltering();
            });
        }

        // =========================================================================
        // 2. Optimistic Status Toggle Switch (.ft-toggle-switch)
        // =========================================================================

        document.addEventListener('change', function(e) {
            if (!e.target.matches('.ft-toggle-switch')) return;

            const toggle = e.target;
            const featureId = toggle.getAttribute('data-id');
            const isChecked = toggle.checked;
            const newStatus = isChecked ? 1 : 0;
            const card = toggle.closest('.ft-card');
            const statusLabel = document.getElementById('statusLabel_' + featureId);

            // Optimistic UI updates
            if (statusLabel) {
                statusLabel.textContent = isChecked ? 'Active' : 'Inactive';
                statusLabel.className = 'ft-switch-label ' + (isChecked ? 'text-success' : 'text-inactive');
            }
            if (card) {
                card.setAttribute('data-status', isChecked ? 'active' : 'inactive');
                card.classList.toggle('status-active', isChecked);
                card.classList.toggle('status-inactive', !isChecked);
            }

            // AJAX Mutation
            $.ajax({
                type: 'POST',
                url: "{{ route('superadmin.company.featuresToggleStatus') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: featureId,
                    status: newStatus
                },
                success: function(response) {
                    if (response.status === 'success') {
                        // Toast notification
                        if (typeof Swal !== 'undefined') {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true
                            });
                            Toast.fire({
                                icon: 'success',
                                title: response.message || 'Status updated successfully.'
                            });
                        }
                        updateKpiBadges();
                    } else {
                        revertToggle();
                        showToggleError(response.message);
                    }
                },
                error: function(xhr) {
                    revertToggle();
                    let msg = 'Failed to update feature status.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    showToggleError(msg);
                }
            });

            function revertToggle() {
                toggle.checked = !isChecked;
                if (statusLabel) {
                    statusLabel.textContent = !isChecked ? 'Active' : 'Inactive';
                    statusLabel.className = 'ft-switch-label ' + (!isChecked ? 'text-success' : 'text-inactive');
                }
                if (card) {
                    card.setAttribute('data-status', !isChecked ? 'active' : 'inactive');
                    card.classList.toggle('status-active', !isChecked);
                    card.classList.toggle('status-inactive', isChecked);
                }
            }

            function showToggleError(msg) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Status Update Failed',
                        text: msg,
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                } else {
                    alert(msg);
                }
            }
        });

        // Dynamic KPI badges recount
        function updateKpiBadges() {
            const cards = document.querySelectorAll('.ft-card');
            let active = 0;
            let inactive = 0;

            cards.forEach(c => {
                if (c.getAttribute('data-status') === 'active') active++;
                else inactive++;
            });

            const activeBadge = document.getElementById('badgeActiveCount');
            const inactiveBadge = document.getElementById('badgeInactiveCount');
            if (activeBadge) activeBadge.textContent = active;
            if (inactiveBadge) inactiveBadge.textContent = inactive;
        }

        // =========================================================================
        // 3. In-Place Metronic Slide-Over Drawer Controller (#kt_feature_drawer)
        // =========================================================================

        function loadFeatureDrawer(id) {
            id = parseInt(id, 10) || 0;
            const isNew = id === 0;
            const titleText = isNew ? 'Create New Feature Policy' : 'Edit Feature Policy #' + id;

            openDrawer(titleText);

            if (drawerBody) {
                drawerBody.innerHTML = `
                    <div class="d-flex align-items-center justify-content-center p-12 text-muted">
                        <span class="spinner-border spinner-border-sm me-2 text-primary"></span> Loading policy details...
                    </div>
                `;
            }

            let url = "{{ route('superadmin.company.featuresListupdate', ':id') }}";
            url = url.replace(':id', id);

            $.ajax({
                type: 'GET',
                url: url,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    const html = (response && response.data && response.data.html)
                        ? response.data.html
                        : (response && response.html ? response.html : '');

                    if (html && drawerBody) {
                        drawerBody.innerHTML = html;
                        bindDrawerEvents();
                    } else {
                        if (drawerBody) {
                            drawerBody.innerHTML = `
                                <div class="p-6 text-danger text-center">
                                    <i class="fa fa-warning fa-solid fa-triangle-exclamation fs-2 mb-2"></i>
                                    <div>Could not load policy form. Please try again.</div>
                                </div>
                            `;
                        }
                    }
                },
                error: function() {
                    if (drawerBody) {
                        drawerBody.innerHTML = `
                            <div class="p-6 text-danger text-center">
                                <i class="fa fa-warning fa-solid fa-triangle-exclamation fs-2 mb-2"></i>
                                <div>Server error while loading drawer.</div>
                            </div>
                        `;
                    }
                }
            });
        }

        function bindDrawerEvents() {
            const form = document.getElementById('ftDrawerForm');
            if (!form) return;

            const daysInput = document.getElementById('ftDrawerDays');
            const titleInput = document.getElementById('ftDrawerTitle');
            const statusSwitch = document.getElementById('ftDrawerStatusSwitch');
            const statusHidden = document.getElementById('ftDrawerStatusIs');
            const statusText = document.getElementById('ftDrawerStatusText');
            const impactCard = document.getElementById('ftPolicyImpactCard');
            const impactText = document.getElementById('ftPolicyImpactText');
            const cancelBtn = document.getElementById('ftDrawerCancelBtn');
            const saveBtn = document.getElementById('ftDrawerSaveBtn');

            // Status Switch in Drawer
            if (statusSwitch) {
                statusSwitch.addEventListener('change', function() {
                    const isActive = this.checked;
                    if (statusHidden) statusHidden.value = isActive ? '1' : '0';
                    if (statusText) {
                        statusText.textContent = isActive ? 'Active (ON)' : 'Inactive (OFF)';
                        statusText.className = 'fw-bold fs-7 ' + (isActive ? 'text-success' : 'text-muted');
                    }
                    if (impactText) {
                        impactText.textContent = isActive
                            ? 'When Active: This policy is enforced system-wide across all tenant workspaces. Inactive requests exceeding the duration window will be automatically processed by background crons.'
                            : 'When Inactive: Automated expiration for this rule is paused. Pending workflows remain open indefinitely until manually resolved or re-enabled.';
                    }
                    if (impactCard) {
                        impactCard.style.background = isActive ? '#eff6ff' : '#f8fafc';
                        impactCard.style.borderColor = isActive ? '#bfdbfe' : '#e2e8f0';
                    }
                });
            }

            // Quick Steppers in Drawer
            form.querySelectorAll('.ft-stepper-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (!daysInput) return;
                    let current = parseInt(daysInput.value, 10) || 0;
                    if (this.hasAttribute('data-set')) {
                        current = parseInt(this.getAttribute('data-set'), 10);
                    } else if (this.hasAttribute('data-step')) {
                        current += parseInt(this.getAttribute('data-step'), 10);
                    }
                    if (current < 0) current = 0;
                    if (current > 3650) current = 3650;
                    daysInput.value = current;
                });
            });

            // Suggestion Pills in Drawer
            form.querySelectorAll('.ft-suggest-pill').forEach(pill => {
                pill.addEventListener('click', function() {
                    const sugTitle = this.getAttribute('data-title');
                    const sugDays = this.getAttribute('data-days');
                    if (titleInput && sugTitle) titleInput.value = sugTitle;
                    if (daysInput && sugDays) daysInput.value = sugDays;
                });
            });

            // Cancel Button
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function() {
                    closeDrawer();
                });
            }

            // Save Button in Drawer
            if (saveBtn) {
                saveBtn.addEventListener('click', function(e) {
                    e.preventDefault();

                    const title = titleInput ? titleInput.value.trim() : '';
                    const days = daysInput ? daysInput.value.trim() : '';
                    const statusIs = statusHidden ? statusHidden.value : '1';
                    const tokenId = document.getElementById('ftDrawerTokenId') ? document.getElementById('ftDrawerTokenId').value : '0';

                    if (!title) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Missing Required Field',
                                text: 'Please enter a policy name.',
                                customClass: { confirmButton: 'btn btn-primary' }
                            });
                        } else {
                            alert('Please enter a policy name.');
                        }
                        if (titleInput) titleInput.focus();
                        return;
                    }

                    if (days === '' || isNaN(days) || parseInt(days, 10) < 0) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid Duration',
                                text: 'Please enter a valid non-negative number of days.',
                                customClass: { confirmButton: 'btn btn-primary' }
                            });
                        } else {
                            alert('Please enter a valid number of days.');
                        }
                        if (daysInput) daysInput.focus();
                        return;
                    }

                    const labelEl = saveBtn.querySelector('.indicator-label');
                    const progressEl = saveBtn.querySelector('.indicator-progress');
                    if (labelEl) labelEl.classList.add('d-none');
                    if (progressEl) progressEl.classList.remove('d-none');
                    saveBtn.disabled = true;

                    $.ajax({
                        type: 'POST',
                        url: "{{ route('superadmin.company.addEditfeaturesListupdate') }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            tokenId: tokenId,
                            title: title,
                            days: days,
                            status_is: statusIs
                        },
                        success: function(response) {
                            if (labelEl) labelEl.classList.remove('d-none');
                            if (progressEl) progressEl.classList.add('d-none');
                            saveBtn.disabled = false;

                            if (response.status === 'success') {
                                closeDrawer();

                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Policy Saved!',
                                        text: response.message || 'Feature policy saved successfully.',
                                        timer: 1200,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    window.location.reload();
                                }
                            } else {
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Save Failed',
                                        text: response.message || 'Could not save policy.',
                                        customClass: { confirmButton: 'btn btn-primary' }
                                    });
                                } else {
                                    alert(response.message || 'Save failed.');
                                }
                            }
                        },
                        error: function(xhr) {
                            if (labelEl) labelEl.classList.remove('d-none');
                            if (progressEl) progressEl.classList.add('d-none');
                            saveBtn.disabled = false;

                            let errorMsg = 'An unexpected server error occurred.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: errorMsg,
                                    customClass: { confirmButton: 'btn btn-primary' }
                                });
                            } else {
                                alert(errorMsg);
                            }
                        }
                    });
                });
            }
        }

        // Intercept "Add Feature Policy" Button
        if (openCreateBtn) {
            openCreateBtn.addEventListener('click', function(e) {
                e.preventDefault();
                loadFeatureDrawer(0);
            });
        }

        // Intercept Edit Actions on Cards
        document.addEventListener('click', function(e) {
            const editBtn = e.target.closest('.ft-edit-btn');
            const titleLink = e.target.closest('.ft-policy-title');

            if (editBtn || titleLink) {
                e.preventDefault();
                const card = (editBtn || titleLink).closest('.ft-card');
                if (card) {
                    const id = card.getAttribute('data-id');
                    loadFeatureDrawer(id);
                }
            }
        });

    })();
</script>
@endpush
