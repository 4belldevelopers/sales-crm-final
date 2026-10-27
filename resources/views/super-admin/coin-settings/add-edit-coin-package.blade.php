@extends('layouts.superadmin_layout')

@push('head-script')
    <link rel="stylesheet" href="{{ asset('assets/node_modules/switchery/dist/switchery.min.css') }}">
    <style>
        /* ==========================================================================
           JERO Super Admin - Modern Card + Sub-Card Enterprise Form System
           Add / Edit Coin Package Canvas - 15.6" Screen Fit (1366x768 & 1920x1080)
           ========================================================================== */
        /* ==========================================================================
           Top Gap Optimization: Collapse Metronic toolbar-fixed height and wrapper padding
           ========================================================================== */
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
            padding-top: calc(65px + 12px) !important;
        }

        @media (max-width: 991.98px) {
            .header-fixed.toolbar-fixed #kt_wrapper,
            .header-fixed #kt_wrapper,
            .toolbar-fixed #kt_wrapper,
            #kt_wrapper {
                padding-top: calc(55px + 8px) !important;
            }
        }

        #kt_content,
        .content {
            padding-top: 0 !important;
            margin-top: 0 !important;
        }

        .cd-form-module-wrap {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
            color: #0f172a !important;
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: hidden !important;
            padding: 0 16px 30px 16px !important;
            margin-top: 0 !important;
        }

        .cd-form-module-wrap * {
            box-sizing: border-box !important;
        }

        /* Guarantee icon preservation across all FontAwesome and Metronic icons */
        .cd-form-module-wrap i,
        .cd-form-module-wrap [class*="fa-"],
        .cd-form-module-wrap .fa,
        .cd-form-module-wrap .fas,
        .cd-form-module-wrap .far,
        .cd-form-module-wrap .fab {
            font-family: "Font Awesome 6 Free", "FontAwesome", "Font Awesome 5 Free", sans-serif !important;
            font-style: normal !important;
            font-weight: 900 !important;
        }

        /* Tier 1 Header Toolbar Card */
        .cd-form-header-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 12px 18px !important;
            margin-bottom: 16px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 12px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
            flex-wrap: wrap !important;
        }

        .cd-form-header-left {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            min-width: 0 !important;
        }

        .cd-form-header-icon {
            width: 38px !important;
            height: 38px !important;
            border-radius: 8px !important;
            background: #eff6ff !important;
            color: #2563eb !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 16px !important;
            flex-shrink: 0 !important;
        }

        .cd-form-header-title {
            font-size: 15px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            line-height: 1.25 !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            flex-wrap: wrap !important;
        }

        .cd-form-header-sub {
            font-size: 11.5px !important;
            color: #64748b !important;
            margin-top: 2px !important;
            line-height: 1.2 !important;
        }

        .cd-form-header-right {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            flex-wrap: wrap !important;
        }

        /* Form Section Cards */
        .cd-form-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 16px 18px !important;
            margin-bottom: 16px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
        }

        .cd-card-header {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding-bottom: 10px !important;
            margin-bottom: 14px !important;
            border-bottom: 1px solid #f1f5f9 !important;
            flex-wrap: wrap !important;
            gap: 8px !important;
        }

        .cd-card-title {
            font-size: 13.5px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
        }

        .cd-field-label {
            font-size: 11.5px !important;
            font-weight: 600 !important;
            color: #334155 !important;
            margin-bottom: 4px !important;
            display: block !important;
        }

        .cd-field-label.required::after {
            content: " *" !important;
            color: #ef4444 !important;
        }

        .cd-field-control {
            font-size: 12.5px !important;
            border-radius: 7px !important;
            border: 1px solid #cbd5e1 !important;
            padding: 7px 11px !important;
            color: #0f172a !important;
            background: #ffffff !important;
            transition: all 0.15s ease !important;
        }

        .cd-field-control:focus {
            border-color: #009ef7 !important;
            box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
            outline: none !important;
        }

        .cd-field-hint {
            font-size: 10.5px !important;
            color: #64748b !important;
            margin-top: 3px !important;
            display: block !important;
        }

        /* 1-Click Duration Preset Chips */
        .cd-duration-chips-wrap {
            display: flex !important;
            gap: 4px !important;
            margin-top: 5px !important;
            flex-wrap: wrap !important;
        }

        .cd-chip-btn {
            border: 1px solid #cbd5e1 !important;
            background: #f8fafc !important;
            color: #475569 !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            padding: 2px 7px !important;
            border-radius: 5px !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
        }

        .cd-chip-btn:hover {
            background: #e2e8f0 !important;
            color: #0f172a !important;
        }

        .cd-chip-btn.active {
            background: #009ef7 !important;
            color: #ffffff !important;
            border-color: #009ef7 !important;
        }

        /* Currency View Switcher */
        .cd-currency-view-switcher {
            display: flex !important;
            background: #f1f5f9 !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 7px !important;
            padding: 2px !important;
        }

        .cd-curr-view-btn {
            border: none !important;
            background: transparent !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            color: #64748b !important;
            padding: 3px 9px !important;
            border-radius: 5px !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
        }

        .cd-curr-view-btn.active {
            background: #ffffff !important;
            color: #009ef7 !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08) !important;
        }

        /* Discount Presets Bar */
        .cd-discount-chip-bar {
            display: flex !important;
            align-items: center !important;
            gap: 4px !important;
            flex-wrap: wrap !important;
        }

        .cd-discount-chip {
            font-size: 10.5px !important;
            font-weight: 600 !important;
            padding: 2px 8px !important;
            border-radius: 4px !important;
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #475569 !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
        }

        .cd-discount-chip:hover {
            background: #eff6ff !important;
            color: #2563eb !important;
            border-color: #93c5fd !important;
        }

        .cd-discount-chip.active {
            background: #009ef7 !important;
            color: #ffffff !important;
            border-color: #009ef7 !important;
        }

        /* Pricing Sub-Cards */
        .cd-pricing-subcard {
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 14px !important;
            height: 100% !important;
        }

        .cd-pricing-subcard-title {
            font-size: 12.5px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            display: flex !important;
            align-items: center !important;
            margin-bottom: 12px !important;
            padding-bottom: 6px !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }

        /* High-Impact Commercial Pricing Summary Card */
        .cd-pricing-summary-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 10px 12px !important;
            margin-bottom: 10px !important;
        }

        .cd-pricing-summary-main {
            display: flex !important;
            align-items: baseline !important;
            justify-content: space-between !important;
        }

        .cd-pricing-net-val {
            font-size: 18px !important;
            font-weight: 800 !important;
            color: #0f172a !important;
            line-height: 1.2 !important;
        }

        .cd-pricing-strikethrough {
            font-size: 12px !important;
            color: #94a3b8 !important;
            text-decoration: line-through !important;
            margin-left: 6px !important;
        }

        .cd-pricing-savings-pill {
            background: #ecfdf5 !important;
            color: #047857 !important;
            font-size: 10.5px !important;
            font-weight: 700 !important;
            padding: 2px 7px !important;
            border-radius: 4px !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 3px !important;
        }

        .cd-pricing-tile-grid {
            display: grid !important;
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            gap: 6px !important;
        }

        .cd-pricing-tile {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 6px !important;
            padding: 6px 8px !important;
        }

        .cd-pricing-tile-lbl {
            font-size: 9.5px !important;
            font-weight: 600 !important;
            color: #64748b !important;
            text-transform: uppercase !important;
            letter-spacing: 0.2px !important;
        }

        .cd-pricing-tile-val {
            font-size: 12px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            margin-top: 2px !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        /* Feature Points Builder */
        .cd-points-suggestion-bar {
            display: flex !important;
            align-items: center !important;
            gap: 5px !important;
            flex-wrap: wrap !important;
            margin-bottom: 12px !important;
            padding: 8px 10px !important;
            background: #f8fafc !important;
            border: 1px dashed #cbd5e1 !important;
            border-radius: 8px !important;
        }

        .cd-preset-chip {
            font-size: 11px !important;
            font-weight: 600 !important;
            padding: 3px 8px !important;
            border-radius: 5px !important;
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #334155 !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
        }

        .cd-preset-chip:hover {
            background: #eff6ff !important;
            color: #009ef7 !important;
            border-color: #009ef7 !important;
            transform: translateY(-1px) !important;
        }

        .cd-point-row {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 8px 10px !important;
            margin-bottom: 8px !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            transition: all 0.15s ease !important;
        }

        .cd-point-row:hover {
            border-color: #cbd5e1 !important;
            box-shadow: 0 1px 4px rgba(15, 23, 42, 0.04) !important;
        }

        .cd-point-handle {
            color: #94a3b8 !important;
            cursor: grab !important;
            font-size: 13px !important;
            padding: 0 4px !important;
            flex-shrink: 0 !important;
        }

        .cd-point-input-wrap {
            flex: 1 !important;
            min-width: 0 !important;
        }

        .cd-point-status-wrap {
            width: 120px !important;
            flex-shrink: 0 !important;
        }

        .cd-point-action-wrap {
            flex-shrink: 0 !important;
        }

        .cd-point-del-btn {
            width: 32px !important;
            height: 32px !important;
            border-radius: 6px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border: 1px solid #fee2e2 !important;
            background: #fef2f2 !important;
            color: #ef4444 !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
        }

        .cd-point-del-btn:hover {
            background: #ef4444 !important;
            color: #ffffff !important;
            border-color: #ef4444 !important;
        }

        /* ==========================================================================
           Phase 5: Refined Sticky Live Preview Column (15.6" Screen Fit)
           ========================================================================== */
        .cd-sticky-preview {
            position: sticky !important;
            top: 16px !important;
            max-height: calc(100vh - 32px) !important;
            overflow-y: auto !important;
            scrollbar-width: thin !important;
            padding-right: 2px !important;
            z-index: 5 !important;
        }

        .cd-sticky-preview::-webkit-scrollbar {
            width: 4px !important;
        }

        .cd-sticky-preview::-webkit-scrollbar-thumb {
            background: #cbd5e1 !important;
            border-radius: 4px !important;
        }

        .cd-preview-header-wrap {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            margin-bottom: 10px !important;
            gap: 8px !important;
        }

        .cd-preview-badge {
            background: #eff6ff !important;
            color: #2563eb !important;
            font-weight: 700 !important;
            font-size: 11px !important;
            padding: 3px 9px !important;
            border-radius: 6px !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 5px !important;
        }

        /* Preview Currency Toggle */
        .cd-prev-curr-toggle {
            display: flex !important;
            background: #f1f5f9 !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 6px !important;
            padding: 2px !important;
        }

        .cd-prev-curr-btn {
            border: none !important;
            background: transparent !important;
            font-size: 10.5px !important;
            font-weight: 700 !important;
            color: #64748b !important;
            padding: 2px 8px !important;
            border-radius: 4px !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
        }

        .cd-prev-curr-btn.active {
            background: #009ef7 !important;
            color: #ffffff !important;
            box-shadow: 0 1px 2px rgba(0, 158, 247, 0.2) !important;
        }

        /* Master Entity Card WYSIWYG Live Mirror */
        .cd-prev-master-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06) !important;
            overflow: hidden !important;
            margin-bottom: 12px !important;
            transition: all 0.2s ease !important;
        }

        .cd-prev-master-card.tier-gold { border-top: 3.5px solid #f59e0b !important; }
        .cd-prev-master-card.tier-silver { border-top: 3.5px solid #94a3b8 !important; }
        .cd-prev-master-card.tier-platinum { border-top: 3.5px solid #8b5cf6 !important; }
        .cd-prev-master-card.tier-free { border-top: 3.5px solid #10b981 !important; }
        .cd-prev-master-card.tier-custom { border-top: 3.5px solid #009ef7 !important; }

        /* Discrete Sub-Cards inside Master Card */
        .cd-prev-subcard {
            background: #ffffff !important;
            border-bottom: 1px solid #f1f5f9 !important;
            padding: 11px 14px !important;
        }

        .cd-prev-subcard:last-child {
            border-bottom: none !important;
        }

        .cd-prev-subcard-lbl {
            font-size: 9.5px !important;
            font-weight: 700 !important;
            color: #64748b !important;
            text-transform: uppercase !important;
            letter-spacing: 0.3px !important;
            margin-bottom: 6px !important;
        }

        /* Sub-Card 1: Identity */
        .cd-prev-identity-header {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
        }

        .cd-prev-icon-box {
            width: 42px !important;
            height: 42px !important;
            border-radius: 9px !important;
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 19px !important;
            color: #009ef7 !important;
            flex-shrink: 0 !important;
        }

        .cd-prev-identity-meta {
            flex: 1 !important;
            min-width: 0 !important;
        }

        .cd-prev-title-wrap {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 6px !important;
        }

        .cd-prev-title {
            font-size: 14px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .cd-prev-code {
            font-size: 10px !important;
            font-weight: 700 !important;
            color: #64748b !important;
            background: #f1f5f9 !important;
            padding: 2px 6px !important;
            border-radius: 4px !important;
            flex-shrink: 0 !important;
        }

        .cd-prev-pills {
            display: flex !important;
            gap: 4px !important;
            margin-top: 4px !important;
            flex-wrap: wrap !important;
        }

        .cd-prev-pill {
            font-size: 9.5px !important;
            font-weight: 600 !important;
            padding: 1.5px 6px !important;
            border-radius: 4px !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 3px !important;
        }

        .cd-prev-pill-op { background: #e0f2fe !important; color: #0369a1 !important; }
        .cd-prev-pill-sales { background: #fef3c7 !important; color: #b45309 !important; }
        .cd-prev-pill-tier { background: #f1f5f9 !important; color: #475569 !important; }
        .cd-prev-pill-active { background: #ecfdf5 !important; color: #047857 !important; }
        .cd-prev-pill-inactive { background: #fef2f2 !important; color: #b91c1c !important; }
        .cd-prev-pill-vis { background: #eff6ff !important; color: #2563eb !important; }
        .cd-prev-pill-mirror { background: #f8fafc !important; color: #64748b !important; border: 1px dashed #cbd5e1 !important; }

        .cd-prev-discount-badge {
            background: #ecfdf5 !important;
            color: #059669 !important;
            font-size: 10px !important;
            font-weight: 700 !important;
            padding: 1px 6px !important;
            border-radius: 4px !important;
            border: 1px solid #a7f3d0 !important;
        }

        .cd-prev-points-list {
            list-style: none !important;
            padding: 0 !important;
            margin: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 5px !important;
        }

        .cd-prev-point-item {
            font-size: 11px !important;
            color: #334155 !important;
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
        }

        .cd-prev-point-item i {
            color: #10b981 !important;
            font-size: 10px !important;
            flex-shrink: 0 !important;
        }

        .cd-prev-bottom-strip {
            background: #f8fafc !important;
            padding: 8px 14px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            font-size: 10.5px !important;
            color: #64748b !important;
        }
    </style>
@endpush

@section('content')
<div class="cd-form-module-wrap">

    {{-- ==========================================================================
       Tier 1: Header Toolbar Card
       ========================================================================== --}}
    <div class="cd-form-header-card">
        <div class="cd-form-header-left">
            <a href="{{ route('superadmin.company.coinPackages') }}" class="btn btn-sm btn-light font-weight-bolder me-1" title="Back to Coin Packages Catalog">
                <i class="fa fa-arrow-left me-1"></i> Catalog
            </a>
            <div class="cd-form-header-icon">
                <i class="fa fa-coins"></i>
            </div>
            <div>
                <div class="cd-form-header-title">
                    <span>{{ $update ? 'Edit Coin Package: ' . ($packageName ?: 'Package #' . @$coin_packages->id) : 'Create New Coin Package' }}</span>
                    @if ($update)
                        <span class="badge bg-light text-primary fw-bold">#PKG-{{ str_pad(@$coin_packages->id ?? 0, 3, '0', STR_PAD_LEFT) }}</span>
                    @else
                        <span class="badge bg-light-primary text-primary fw-bold">NEW</span>
                    @endif
                </div>
                <div class="cd-form-header-sub">
                    Configure enterprise coin balance allocations, dual-currency commercial pricing rules, and feature entitlements.
                </div>
            </div>
        </div>

        <div class="cd-form-header-right">
            <a href="{{ route('superadmin.company.coinPackages') }}" class="btn btn-sm btn-light font-weight-bolder">
                Cancel
            </a>
            <button type="button" class="btn btn-sm btn-primary font-weight-bolder" id="save-form-top">
                <i class="fa fa-save me-1"></i> {{ $update ? 'Update Package' : 'Save Package' }}
            </button>
        </div>
    </div>

    {{-- ==========================================================================
       Tier 2: 2-Column Split Canvas (Left 65% Form, Right 35% Live Preview)
       ========================================================================== --}}
    <div class="row g-4">
        {{-- Left Column: Configuration Form --}}
        <div class="col-xl-7 col-lg-7">
            <form id="editSettings" class="ajax-form">
                @csrf

                {{-- Hidden Form Identifiers --}}
                <input type="hidden" id="token_id" name="token_id" value="{{ $update && @$coin_packages->id ? $coin_packages->id : '' }}">
                <input type="hidden" id="coin_package_type_id" name="coin_package_type_id" value="{{ $update && @$coin_packages->coin_package_type_id ? $coin_packages->coin_package_type_id : '' }}">

                {{-- ----------------------------------------------------------
                   Card A: Package Identity & Tier Configuration
                   ---------------------------------------------------------- --}}
                <div class="cd-form-card">
                    <div class="cd-card-header">
                        <div class="cd-card-title">
                            <i class="fa fa-id-card text-primary"></i>
                            <span>Package Identity & Tier Configuration</span>
                        </div>
                        <span class="badge bg-light text-muted fw-normal fs-8">Core Specification</span>
                    </div>

                    <div class="row g-3">
                        {{-- Domain / Event Category --}}
                        <div class="col-md-6">
                            <label class="cd-field-label required">Event Domain / Category</label>
                            <select name="event" id="event" class="form-select cd-field-control" required>
                                <option value="operational" {{ ($update && @$coin_packages->event_type == 'operational') || !$update ? 'selected' : '' }}>
                                    Operational (ATS Hiring Workflow)
                                </option>
                                <option value="sales" {{ $update && @$coin_packages->event_type == 'sales' ? 'selected' : '' }}>
                                    Sales & Outreach Campaign
                                </option>
                            </select>
                            <span class="cd-field-hint">Defines whether credits apply to core ATS or talent marketplace trading.</span>
                        </div>

                        {{-- Package Title --}}
                        <div class="col-md-6">
                            <label class="cd-field-label required">Package Title</label>
                            <input type="text" class="form-control cd-field-control" name="coin_package" id="coin_package"
                                   value="{{ $packageName ?? '' }}" placeholder="e.g. Gold Enterprise, Silver Starter" required>
                            <span class="cd-field-hint">Public plan name shown across tenant billing and checkout screens.</span>
                        </div>

                        {{-- Coin Allocation --}}
                        <div class="col-md-4">
                            <label class="cd-field-label required">Total Coins</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-warning border-end-0"><i class="fa fa-coins"></i></span>
                                <input type="number" step="any" min="1" class="form-control cd-field-control border-start-0"
                                       name="total_coins" id="total_coins"
                                       value="{{ $update && @$coin_packages->coins ? $coin_packages->coins : '1000' }}"
                                       placeholder="1000" required>
                            </div>
                            <span class="cd-field-hint">Credits minted to organization balance.</span>
                        </div>

                        {{-- Validity Duration (Days) with 1-Click Preset Chips --}}
                        <div class="col-md-4">
                            <label class="cd-field-label required" id="durations_label">Validity Duration (Days)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-primary border-end-0"><i class="fa fa-calendar-alt"></i></span>
                                <input type="number" min="1" class="form-control cd-field-control border-start-0"
                                       id="durations" name="durations"
                                       value="{{ $update && @$coin_packages->days ? $coin_packages->days : '365' }}" required>
                            </div>
                            <div class="cd-duration-chips-wrap">
                                <button type="button" class="btn cd-chip-btn" data-days="30">30d</button>
                                <button type="button" class="btn cd-chip-btn" data-days="90">90d</button>
                                <button type="button" class="btn cd-chip-btn" data-days="180">180d</button>
                                <button type="button" class="btn cd-chip-btn {{ ($update && @$coin_packages->days == 365) || !$update ? 'active' : '' }}" data-days="365">1 Year (365d)</button>
                            </div>
                        </div>

                        {{-- Catalog Tier Type --}}
                        <div class="col-md-4">
                            <label class="cd-field-label required">Catalog Tier Type</label>
                            <select name="package_type" id="package_type" class="form-select cd-field-control">
                                <option value="0" {{ ($update && @$coin_packages->custome_pck == 0) || !$update ? 'selected' : '' }}>Standard Catalog</option>
                                <option value="1" {{ $update && @$coin_packages->custome_pck == 1 ? 'selected' : '' }}>Custom Negotiated</option>
                                <option value="2" {{ $update && @$coin_packages->custome_pck == 2 ? 'selected' : '' }}>Free / Trial</option>
                            </select>
                            <span class="cd-field-hint">Visual tier badge in catalog stream.</span>
                        </div>

                        {{-- Status & Visibility --}}
                        <div class="col-md-6">
                            <label class="cd-field-label required">Publish Status</label>
                            <select name="status" id="status" class="form-select cd-field-control">
                                <option value="1" {{ ($update && @$coin_packages->status == 1) || !$update ? 'selected' : '' }}>Active (Available for Purchase)</option>
                                <option value="0" {{ $update && @$coin_packages->status == 0 ? 'selected' : '' }}>Inactive (Archived / Hidden)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="cd-field-label required">Billing Emphasis / Visibility</label>
                            <select name="visibility_show" id="visibility_show" class="form-select cd-field-control">
                                <option value="1" {{ ($update && @$coin_packages->visibility_show == 1) || !$update ? 'selected' : '' }}>Yearly (Annual billing emphasis)</option>
                                <option value="2" {{ $update && @$coin_packages->visibility_show == 2 ? 'selected' : '' }}>Monthly (Monthly billing emphasis)</option>
                                <option value="0" {{ $update && @$coin_packages->visibility_show == 0 ? 'selected' : '' }}>Daily (Per-day billing emphasis)</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- ----------------------------------------------------------
                   Card B: Commercial Dual-Currency Pricing Matrix
                   ---------------------------------------------------------- --}}
                <div class="cd-form-card" id="card-pricing-engine">
                    <div class="cd-card-header">
                        <div class="cd-card-title">
                            <i class="fa fa-calculator text-primary"></i>
                            <span>Commercial Dual-Currency Pricing Matrix</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            {{-- Currency View Switcher: Both vs INR vs USD --}}
                            <div class="cd-currency-view-switcher">
                                <button type="button" class="cd-curr-view-btn active" data-view="both">Both Currencies</button>
                                <button type="button" class="cd-curr-view-btn" data-view="inr">₹ INR Only</button>
                                <button type="button" class="cd-curr-view-btn" data-view="usd">$ USD Only</button>
                            </div>
                        </div>
                    </div>

                    {{-- Quick Multi-Currency Discount Presets Bar --}}
                    <div class="d-flex align-items-center justify-content-between p-2 mb-3 bg-light rounded border border-light flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-1 fs-8 text-muted">
                            <i class="fa fa-percent text-primary me-1"></i>
                            <span class="fw-bold text-dark">Quick Discount Presets:</span>
                        </div>
                        <div class="cd-discount-chip-bar">
                            <button type="button" class="btn cd-discount-chip {{ (@$coin_packages->discount == 0) ? 'active' : '' }}" data-discount="0">0% (None)</button>
                            <button type="button" class="btn cd-discount-chip {{ (@$coin_packages->discount == 10) ? 'active' : '' }}" data-discount="10">10% OFF</button>
                            <button type="button" class="btn cd-discount-chip {{ (@$coin_packages->discount == 15) ? 'active' : '' }}" data-discount="15">15% OFF</button>
                            <button type="button" class="btn cd-discount-chip {{ (@$coin_packages->discount == 20) ? 'active' : '' }}" data-discount="20">20% OFF</button>
                            <button type="button" class="btn cd-discount-chip {{ (@$coin_packages->discount == 25) ? 'active' : '' }}" data-discount="25">25% OFF</button>
                            <button type="button" class="btn cd-discount-chip {{ (@$coin_packages->discount == 30) ? 'active' : '' }}" data-discount="30">30% OFF</button>
                            <button type="button" class="btn cd-discount-chip {{ (@$coin_packages->discount == 50) ? 'active' : '' }}" data-discount="50">50% OFF</button>
                        </div>
                    </div>

                    @php
                        $actualPriceInr = 0;
                        if ($update && @$coin_packages->price) {
                            $discountInr = (float) @$coin_packages->discount;
                            $actualPriceInr = ($discountInr > 0 && $discountInr < 100)
                                ? round($coin_packages->price / (1 - ($discountInr / 100)), 2)
                                : $coin_packages->price;
                        }

                        $actualPriceUsd = 0;
                        if ($update && @$coin_packages->price_usd) {
                            $discountUsd = (float) @$coin_packages->discount;
                            $actualPriceUsd = ($discountUsd > 0 && $discountUsd < 100)
                                ? round($coin_packages->price_usd / (1 - ($discountUsd / 100)), 2)
                                : $coin_packages->price_usd;
                        }
                    @endphp

                    <div class="row g-3" id="pricing-columns-row">
                        {{-- INR Pricing Column --}}
                        <div class="col-md-6 cd-pricing-col cd-pricing-col-inr">
                            <div class="cd-pricing-subcard">
                                <div class="cd-pricing-subcard-title justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary text-white fs-8 px-2 py-1">INR (₹)</span>
                                        <span>Domestic Indian Rupee Pricing</span>
                                    </div>
                                    <span class="badge bg-light-primary text-primary fs-9 fw-bold">Domestic Market</span>
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-7">
                                        <label class="cd-field-label required">Base Catalog Price (₹)</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white">₹</span>
                                            <input type="number" step="any" min="0" class="form-control cd-field-control"
                                                   name="coin_price" id="coin_price"
                                                   value="{{ $actualPriceInr }}" placeholder="0.00" required>
                                        </div>
                                        <span class="cd-field-hint">Pre-discount annual list price.</span>
                                    </div>
                                    <div class="col-5">
                                        <label class="cd-field-label">Discount (%)</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="any" min="0" max="100" class="form-control cd-field-control discount"
                                                   name="discount" id="discount"
                                                   value="{{ $update && @$coin_packages->discount ? $coin_packages->discount : '0' }}" placeholder="0">
                                            <span class="input-group-text bg-white">%</span>
                                        </div>
                                        <span class="cd-field-hint">Customer savings rate.</span>
                                    </div>
                                </div>

                                {{-- Commercial Net Yearly Price Card --}}
                                <div class="cd-pricing-summary-card">
                                    <div class="cd-pricing-summary-main">
                                        <div>
                                            <span class="cd-pricing-net-val" id="inr-net-display">₹{{ number_format(@$coin_packages->price ?: 0, 2) }}</span>
                                            <span class="cd-pricing-strikethrough" id="inr-strike-display" style="{{ @$coin_packages->discount > 0 ? '' : 'display: none;' }}">
                                                ₹{{ number_format($actualPriceInr, 2) }}
                                            </span>
                                        </div>
                                        <span class="cd-pricing-savings-pill" id="inr-savings-badge" style="{{ @$coin_packages->discount > 0 ? '' : 'display: none;' }}">
                                            <i class="fa fa-tag"></i> <span id="inr-savings-text">Save {{ @$coin_packages->discount }}%</span>
                                        </span>
                                    </div>
                                    <div class="text-muted fs-9 mt-1">Effective Annual Billed Price (Net Receivable)</div>
                                </div>

                                {{-- Unit & Prorated Rates Tiles --}}
                                <div class="cd-pricing-tile-grid mb-2">
                                    <div class="cd-pricing-tile">
                                        <div class="cd-pricing-tile-lbl">Monthly Rate</div>
                                        <div class="cd-pricing-tile-val text-primary" id="inr-monthly-tile">
                                            ₹{{ number_format(@$coin_packages->total_price_monthly ?: 0, 2) }}
                                        </div>
                                    </div>
                                    <div class="cd-pricing-tile">
                                        <div class="cd-pricing-tile-lbl">Daily Rate</div>
                                        <div class="cd-pricing-tile-val" id="inr-daily-tile">
                                            ₹{{ number_format(@$coin_packages->total_price_days ?: 0, 2) }}
                                        </div>
                                    </div>
                                    <div class="cd-pricing-tile">
                                        <div class="cd-pricing-tile-lbl">Unit / Coin</div>
                                        <div class="cd-pricing-tile-val text-warning" id="inr-rate-tile">
                                            ₹{{ number_format(@$coin_packages->total_price_rate ?: 0, 2) }}
                                        </div>
                                    </div>
                                </div>

                                {{-- Inputs bound for form submission --}}
                                <input type="hidden" name="total_price" id="total_price" value="{{ $update && @$coin_packages->price ? $coin_packages->price : '0' }}">
                                <input type="hidden" name="total_price_monthly" id="total_price_monthly" value="{{ $update && @$coin_packages->total_price_monthly ? $coin_packages->total_price_monthly : '0' }}">
                                <input type="hidden" name="total_price_days" id="total_price_days" value="{{ $update && @$coin_packages->total_price_days ? $coin_packages->total_price_days : '0' }}">
                                <input type="hidden" name="total_price_rate" id="total_price_rate" value="{{ $update && @$coin_packages->total_price_rate ? $coin_packages->total_price_rate : '0' }}">
                            </div>
                        </div>

                        {{-- USD Pricing Column --}}
                        <div class="col-md-6 cd-pricing-col cd-pricing-col-usd">
                            <div class="cd-pricing-subcard">
                                <div class="cd-pricing-subcard-title justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-success text-white fs-8 px-2 py-1">USD ($)</span>
                                        <span>International US Dollar Pricing</span>
                                    </div>
                                    <span class="badge bg-light-success text-success fs-9 fw-bold">Global Market</span>
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-7">
                                        <label class="cd-field-label required">Base Catalog Price ($)</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white">$</span>
                                            <input type="number" step="any" min="0" class="form-control cd-field-control"
                                                   name="coin_price_usd" id="coin_price_usd"
                                                   value="{{ $actualPriceUsd }}" placeholder="0.00">
                                        </div>
                                        <span class="cd-field-hint">Pre-discount annual list price in USD.</span>
                                    </div>
                                    <div class="col-5">
                                        <label class="cd-field-label">Discount (%)</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="any" min="0" max="100" class="form-control cd-field-control"
                                                   name="discount_usd" id="discount_usd"
                                                   value="{{ $update && @$coin_packages->discount ? $coin_packages->discount : '0' }}" placeholder="0">
                                            <span class="input-group-text bg-white">%</span>
                                        </div>
                                        <span class="cd-field-hint">Customer savings rate in USD.</span>
                                    </div>
                                </div>

                                {{-- Commercial Net Yearly Price Card --}}
                                <div class="cd-pricing-summary-card">
                                    <div class="cd-pricing-summary-main">
                                        <div>
                                            <span class="cd-pricing-net-val" id="usd-net-display">${{ number_format(@$coin_packages->price_usd ?: 0, 2) }}</span>
                                            <span class="cd-pricing-strikethrough" id="usd-strike-display" style="{{ @$coin_packages->discount > 0 ? '' : 'display: none;' }}">
                                                ${{ number_format($actualPriceUsd, 2) }}
                                            </span>
                                        </div>
                                        <span class="cd-pricing-savings-pill" id="usd-savings-badge" style="{{ @$coin_packages->discount > 0 ? '' : 'display: none;' }}">
                                            <i class="fa fa-tag"></i> <span id="usd-savings-text">Save {{ @$coin_packages->discount }}%</span>
                                        </span>
                                    </div>
                                    <div class="text-muted fs-9 mt-1">Effective Annual Billed Price (Net USD Receivable)</div>
                                </div>

                                {{-- Unit & Prorated Rates Tiles --}}
                                <div class="cd-pricing-tile-grid mb-2">
                                    <div class="cd-pricing-tile">
                                        <div class="cd-pricing-tile-lbl">Monthly Rate</div>
                                        <div class="cd-pricing-tile-val text-success" id="usd-monthly-tile">
                                            ${{ number_format(@$coin_packages->total_price_usd_monthly ?: 0, 2) }}
                                        </div>
                                    </div>
                                    <div class="cd-pricing-tile">
                                        <div class="cd-pricing-tile-lbl">Daily Rate</div>
                                        <div class="cd-pricing-tile-val" id="usd-daily-tile">
                                            ${{ number_format(@$coin_packages->total_price_usd_days ?: 0, 2) }}
                                        </div>
                                    </div>
                                    <div class="cd-pricing-tile">
                                        <div class="cd-pricing-tile-lbl">Unit / Coin</div>
                                        <div class="cd-pricing-tile-val text-warning" id="usd-rate-tile">
                                            ${{ number_format(@$coin_packages->total_price_usd_rate ?: 0, 2) }}
                                        </div>
                                    </div>
                                </div>

                                {{-- Inputs bound for form submission --}}
                                <input type="hidden" name="total_price_usd" id="total_price_usd" value="{{ $update && @$coin_packages->price_usd ? $coin_packages->price_usd : '0' }}">
                                <input type="hidden" name="total_price_usd_monthly" id="total_price_usd_monthly" value="{{ $update && @$coin_packages->total_price_usd_monthly ? $coin_packages->total_price_usd_monthly : '0' }}">
                                <input type="hidden" name="total_price_usd_days" id="total_price_usd_days" value="{{ $update && @$coin_packages->total_price_usd_days ? $coin_packages->total_price_usd_days : '0' }}">
                                <input type="hidden" name="total_price_usd_rate" id="total_price_usd_rate" value="{{ $update && @$coin_packages->total_price_usd_rate ? $coin_packages->total_price_usd_rate : '0' }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ----------------------------------------------------------
                   Card C: Feature Entitlements & Checklist Manager (Phase 4)
                   ---------------------------------------------------------- --}}
                <div class="cd-form-card">
                    <div class="cd-card-header">
                        <div class="cd-card-title">
                            <i class="fa fa-list-check text-primary"></i>
                            <span>Feature Entitlements & Checklist Manager</span>
                        </div>
                        <button type="button" class="btn btn-xs btn-primary addRowBtnbonus font-weight-bolder">
                            <i class="fa fa-plus me-1"></i> Add Custom Feature Line
                        </button>
                    </div>

                    {{-- 1-Click Feature Suggestion Presets --}}
                    <div class="cd-points-suggestion-bar">
                        <span class="fs-8 fw-bold text-dark me-1"><i class="fa fa-wand-magic-sparkles text-primary me-1"></i> 1-Click Presets:</span>
                        <button type="button" class="btn cd-preset-chip" data-text="Up to 25 Active Recruiter Seats">+ Recruiter Seats</button>
                        <button type="button" class="btn cd-preset-chip" data-text="Monthly Automated Coin Top-ups">+ Monthly Coins</button>
                        <button type="button" class="btn cd-preset-chip" data-text="1,000 Unlocked Resume Downloads">+ Resume Quota</button>
                        <button type="button" class="btn cd-preset-chip" data-text="Full Talent Marketplace Trading Access">+ Marketplace Access</button>
                        <button type="button" class="btn cd-preset-chip" data-text="AI Candidate Screening & Auto-Ranking">+ AI Screening</button>
                        <button type="button" class="btn cd-preset-chip" data-text="Advanced Executive Analytics & Pipeline Reports">+ Pipeline Reports</button>
                        <button type="button" class="btn cd-preset-chip" data-text="Dedicated Senior Account Manager & 24/7 SLA">+ Dedicated Support</button>
                    </div>

                    {{-- Dynamic Feature Rows Container --}}
                    <div id="points-builder-container">
                        @if(count($coin_package_points) > 0)
                            @foreach ($coin_package_points as $key => $point)
                                <div class="cd-point-row remove_row">
                                    <div class="cd-point-handle" title="Feature line"><i class="fa fa-grip-vertical"></i></div>
                                    <div class="cd-point-input-wrap">
                                        <input type="text" class="form-control form-control-sm cd-field-control bonus_points"
                                               name="points[]" value="{{ $point->title }}" placeholder="e.g. Up to 25 Active Recruiter Seats">
                                    </div>
                                    <div class="cd-point-status-wrap">
                                        <select name="status_point[]" class="form-select form-select-sm cd-field-control point_status_select">
                                            <option value="1" {{ $point->status == 1 ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ $point->status == 0 ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                    <div class="cd-point-action-wrap">
                                        <button type="button" class="btn btn-sm cd-point-del-btn remove_point_btn" title="Delete Point">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="cd-point-row remove_row">
                                <div class="cd-point-handle" title="Feature line"><i class="fa fa-grip-vertical"></i></div>
                                <div class="cd-point-input-wrap">
                                    <input type="text" class="form-control form-control-sm cd-field-control bonus_points"
                                           name="points[]" value="" placeholder="e.g. Up to 25 Active Recruiter Seats">
                                </div>
                                <div class="cd-point-status-wrap">
                                    <select name="status_point[]" class="form-select form-select-sm cd-field-control point_status_select">
                                        <option value="1" selected>Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                                <div class="cd-point-action-wrap">
                                    <button type="button" class="btn btn-sm cd-point-del-btn remove_point_btn" title="Delete Point">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Add another button at bottom of list --}}
                    <button type="button" class="btn btn-sm btn-light-primary w-100 mt-2 addRowBtnbonus font-weight-bolder">
                        <i class="fa fa-plus me-1"></i> Add Another Feature Line
                    </button>
                </div>

                {{-- Form Actions Footer --}}
                <div class="d-flex align-items-center justify-content-between pt-2 pb-4">
                    <a href="{{ route('superadmin.company.coinPackages') }}" class="btn btn-light font-weight-bolder">
                        <i class="fa fa-arrow-left me-1"></i> Cancel & Return
                    </a>
                    <button type="button" id="save-form" class="btn btn-primary font-weight-bolder px-5">
                        <i class="fa fa-save me-1"></i> {{ $update ? 'Save Package Changes' : 'Create Coin Package' }}
                    </button>
                </div>
            </form>
        </div>

        {{-- Right Column: Live Master Entity Card Preview (Phase 5 Refined) --}}
        <div class="col-xl-5 col-lg-5">
            <div class="cd-sticky-preview">
                <div class="cd-preview-header-wrap">
                    <div class="cd-preview-badge">
                        <i class="fa fa-eye"></i> LIVE CATALOG PREVIEW
                    </div>
                    {{-- Preview Currency Switcher --}}
                    <div class="cd-prev-curr-toggle" title="Switch preview display currency">
                        <button type="button" class="cd-prev-curr-btn active" data-curr="inr">₹ INR</button>
                        <button type="button" class="cd-prev-curr-btn" data-curr="usd">$ USD</button>
                    </div>
                </div>

                {{-- Live Preview Master Entity Card (Sub-Card Architecture) --}}
                <div class="cd-prev-master-card tier-gold" id="live-card-preview">
                    {{-- Sub-Card 1: Package Identity & Type --}}
                    <div class="cd-prev-subcard cd-prev-subcard-identity">
                        <div class="cd-prev-identity-header">
                            <div class="cd-prev-icon-box" id="prev-icon-box">
                                <i class="fa fa-cube" id="prev-icon"></i>
                            </div>
                            <div class="cd-prev-identity-meta">
                                <div class="cd-prev-title-wrap">
                                    <span class="cd-prev-title" id="prev-pkg-title">{{ $packageName ?: 'Package Name' }}</span>
                                    <span class="cd-prev-code" id="prev-pkg-code">#PKG-{{ str_pad(@$coin_packages->id ?? 0, 3, '0', STR_PAD_LEFT) }}</span>
                                </div>
                                <div class="cd-prev-pills">
                                    <span class="cd-prev-pill cd-prev-pill-op" id="prev-pill-event">
                                        <i class="fa fa-cogs"></i> Operational
                                    </span>
                                    <span class="cd-prev-pill cd-prev-pill-tier" id="prev-pill-tier">Standard</span>
                                    <span class="cd-prev-pill cd-prev-pill-mirror" title="Dynamic Interactive Preview Mirror">
                                        <i class="fa fa-eye text-primary"></i> Mirror
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sub-Card 2: Coin Allocation & Unit Rates --}}
                    <div class="cd-prev-subcard cd-prev-subcard-coins">
                        <div class="cd-prev-subcard-lbl">Coin Allocation & Economics</div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa fa-coins text-warning fs-4"></i>
                                <span class="fs-4 fw-bolder text-dark" id="prev-coins-amount">{{ number_format(@$coin_packages->coins ?: 1000) }}</span>
                                <span class="fs-8 text-muted fw-bold">Coins</span>
                            </div>
                            <div class="text-end">
                                <div class="fs-9 text-muted fw-bold">Unit Rate:</div>
                                <div class="fs-7 fw-bolder text-primary" id="prev-unit-rate">₹0.00 / coin</div>
                            </div>
                        </div>
                    </div>

                    {{-- Sub-Card 3: Pricing & Billing Cycle --}}
                    <div class="cd-prev-subcard cd-prev-subcard-pricing">
                        <div class="cd-prev-subcard-lbl">Commercial Pricing & Billing</div>
                        <div class="d-flex align-items-baseline justify-content-between mb-1">
                            <div class="d-flex align-items-baseline gap-1">
                                <span class="fs-3 fw-bolder text-dark" id="prev-main-price">₹0.00</span>
                                <span class="fs-8 text-muted fw-bold" id="prev-main-term">/ Year</span>
                                <span class="cd-prev-discount-badge" id="prev-discount-tag" style="display: none;">0% OFF</span>
                            </div>
                            <div class="fs-9 text-muted" id="prev-billing-mode-text">Annual Billing</div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between pt-1 border-top border-light fs-8 text-muted">
                            <span>Monthly: <strong class="text-dark" id="prev-monthly-rate">₹0.00</strong></span>
                            <span>•</span>
                            <span>Daily: <strong class="text-dark" id="prev-daily-rate">₹0.00</strong></span>
                        </div>
                    </div>

                    {{-- Sub-Card 4: Duration & Visibility --}}
                    <div class="cd-prev-subcard cd-prev-subcard-validity">
                        <div class="cd-prev-subcard-lbl">Duration & Display Visibility</div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa fa-calendar-alt text-primary"></i>
                                <span class="fw-bolder fs-7 text-dark" id="prev-validity-days">{{ @$coin_packages->days ?: 365 }} Days</span>
                                <span class="fs-8 text-muted" id="prev-validity-approx">({{ round((@$coin_packages->days ?: 365) / 30, 1) }} Mos)</span>
                            </div>
                            <span class="cd-prev-pill cd-prev-pill-vis" id="prev-visibility-pill">
                                <i class="fa fa-eye"></i> Yearly Portal Plan
                            </span>
                        </div>
                    </div>

                    {{-- Sub-Card 5: Feature Access & Points --}}
                    <div class="cd-prev-subcard cd-prev-subcard-features">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="cd-prev-subcard-lbl mb-0">Feature Entitlements</span>
                            <span class="fs-8 text-primary fw-bold" id="prev-features-count"><i class="fa fa-shield-alt me-1"></i> Active Features</span>
                        </div>
                        <ul class="cd-prev-points-list" id="prev-points-container">
                            {{-- Dynamically populated --}}
                        </ul>
                    </div>

                    {{-- Sub-Card 6 / Bottom Activity Strip --}}
                    <div class="cd-prev-bottom-strip">
                        <div class="d-flex align-items-center gap-1 fs-9 text-muted">
                            <i class="fa fa-sync-alt text-primary me-1"></i>
                            <span>Real-Time Sync Active</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="fs-9 text-muted">Status:</span>
                            <span class="cd-prev-pill cd-prev-pill-active" id="prev-pill-status">Active</span>
                        </div>
                    </div>
                </div>

                {{-- Quick Economics Helper Card --}}
                <div class="cd-form-card p-3 mb-0">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold fs-7 text-dark"><i class="fa fa-chart-pie text-primary me-1"></i> Packaging Economics</span>
                        <span class="fs-8 text-muted">Dual Currency Matrix</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom border-light fs-8">
                        <span class="text-muted">INR Base vs Net:</span>
                        <span class="fw-bold" id="quick-inr-summary">₹0.00 Net</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom border-light fs-8">
                        <span class="text-muted">USD Base vs Net:</span>
                        <span class="fw-bold" id="quick-usd-summary">$0.00 Net</span>
                    </div>
                    <div class="d-flex justify-content-between pt-1 fs-8">
                        <span class="text-muted">Per-Coin Margin:</span>
                        <span class="fw-bold text-success" id="quick-margin-summary">Automated</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('footer-script')
    <script src="{{ asset('assets/node_modules/switchery/dist/switchery.min.js') }}"></script>

    <script>
        $(document).ready(function() {

            // Active preview display currency ('inr' or 'usd')
            var currentPreviewCurrency = 'inr';

            // Safe numeric helpers
            function safeNumber(val, defaultVal = 0) {
                let n = parseFloat(val);
                return isNaN(n) ? defaultVal : n;
            }

            function safeDivide(numerator, denominator, decimals = 2) {
                let num = parseFloat(numerator);
                let den = parseFloat(denominator);
                if (isNaN(num) || isNaN(den) || den <= 0) {
                    return (0).toFixed(decimals);
                }
                return (num / den).toFixed(decimals);
            }

            // Top save button triggers main save form
            $('#save-form-top').click(function() {
                $('#save-form').click();
            });

            // Currency View Switcher (Both / INR Only / USD Only)
            $('.cd-curr-view-btn').click(function() {
                $('.cd-curr-view-btn').removeClass('active');
                $(this).addClass('active');
                var view = $(this).data('view');

                if (view === 'inr') {
                    $('.cd-pricing-col-inr').removeClass('col-md-6 d-none').addClass('col-md-12');
                    $('.cd-pricing-col-usd').addClass('d-none');
                } else if (view === 'usd') {
                    $('.cd-pricing-col-usd').removeClass('col-md-6 d-none').addClass('col-md-12');
                    $('.cd-pricing-col-inr').addClass('d-none');
                } else {
                    $('.cd-pricing-col-inr').removeClass('col-md-12 d-none').addClass('col-md-6');
                    $('.cd-pricing-col-usd').removeClass('col-md-12 d-none').addClass('col-md-6');
                }
            });

            // Quick Discount Preset Chips Handler
            $('.cd-discount-chip').click(function() {
                $('.cd-discount-chip').removeClass('active');
                $(this).addClass('active');
                var disc = $(this).data('discount');
                $('#discount').val(disc);
                $('#discount_usd').val(disc);
                updatePriceDetailInr();
                updatePriceDetailUsd();
                updateLivePreview();
            });

            // Preview Currency Switcher (INR vs USD in live card)
            $('.cd-prev-curr-btn').click(function() {
                $('.cd-prev-curr-btn').removeClass('active');
                $(this).addClass('active');
                currentPreviewCurrency = $(this).data('curr');
                updateLivePreview();
            });

            // 1-Click Duration Chips Handler
            $('.cd-chip-btn').click(function() {
                $('.cd-chip-btn').removeClass('active');
                $(this).addClass('active');
                var days = $(this).data('days');
                $('#durations').val(days);
                updatePriceDetailInr();
                updatePriceDetailUsd();
                updateLivePreview();
            });

            $('#durations').on('input keyup change', function() {
                var currentDays = $(this).val();
                $('.cd-chip-btn').removeClass('active');
                $('.cd-chip-btn[data-days="' + currentDays + '"]').addClass('active');
                updatePriceDetailInr();
                updatePriceDetailUsd();
                updateLivePreview();
            });

            // Dual-currency calculation engines
            function updatePriceDetailInr() {
                let basePrice = safeNumber($('#coin_price').val());
                let discount = Math.min(100, Math.max(0, safeNumber($('#discount').val())));
                let coins = safeNumber($('#total_coins').val());

                let finalPrice = basePrice;
                let savings = 0;
                if (discount > 0) {
                    savings = (discount / 100) * basePrice;
                    finalPrice = Math.max(0, basePrice - savings);
                }

                let yearly = finalPrice.toFixed(2);
                let monthly = safeDivide(finalPrice, 12);
                let daily = safeDivide(finalPrice, 365);
                let perCoinRate = coins > 0 ? safeDivide(finalPrice, coins) : '0.00';

                // Update Form inputs
                $('#total_price').val(yearly);
                $('#total_price_monthly').val(monthly);
                $('#total_price_days').val(daily);
                $('#total_price_rate').val(perCoinRate);

                // Update Interactive Sub-Card Displays
                $('#inr-net-display').text('₹' + Number(yearly).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                if (discount > 0) {
                    $('#inr-strike-display').text('₹' + Number(basePrice).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})).show();
                    $('#inr-savings-badge').show();
                    $('#inr-savings-text').text('Save ₹' + Number(savings).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' (' + discount + '%)');
                } else {
                    $('#inr-strike-display').hide();
                    $('#inr-savings-badge').hide();
                }

                $('#inr-monthly-tile').text('₹' + Number(monthly).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $('#inr-daily-tile').text('₹' + Number(daily).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $('#inr-rate-tile').text('₹' + Number(perCoinRate).toFixed(2));
            }

            function updatePriceDetailUsd() {
                let basePrice = safeNumber($('#coin_price_usd').val());
                let discount = Math.min(100, Math.max(0, safeNumber($('#discount_usd').val() || $('#discount').val())));
                let coins = safeNumber($('#total_coins').val());

                let finalPrice = basePrice;
                let savings = 0;
                if (discount > 0) {
                    savings = (discount / 100) * basePrice;
                    finalPrice = Math.max(0, basePrice - savings);
                }

                let yearly = finalPrice.toFixed(2);
                let monthly = safeDivide(finalPrice, 12);
                let daily = safeDivide(finalPrice, 365);
                let perCoinRate = coins > 0 ? safeDivide(finalPrice, coins) : '0.00';

                // Update Form inputs
                $('#total_price_usd').val(yearly);
                $('#total_price_usd_monthly').val(monthly);
                $('#total_price_usd_days').val(daily);
                $('#total_price_usd_rate').val(perCoinRate);

                // Update Interactive Sub-Card Displays
                $('#usd-net-display').text('$' + Number(yearly).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                if (discount > 0) {
                    $('#usd-strike-display').text('$' + Number(basePrice).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})).show();
                    $('#usd-savings-badge').show();
                    $('#usd-savings-text').text('Save $' + Number(savings).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' (' + discount + '%)');
                } else {
                    $('#usd-strike-display').hide();
                    $('#usd-savings-badge').hide();
                }

                $('#usd-monthly-tile').text('$' + Number(monthly).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $('#usd-daily-tile').text('$' + Number(daily).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $('#usd-rate-tile').text('$' + Number(perCoinRate).toFixed(2));
            }

            // Sync discount fields
            $('#discount').on('input keyup change', function() {
                var d = $(this).val();
                $('#discount_usd').val(d);
                syncDiscountChips(d);
                updatePriceDetailInr();
                updatePriceDetailUsd();
                updateLivePreview();
            });

            $('#discount_usd').on('input keyup change', function() {
                var d = $(this).val();
                $('#discount').val(d);
                syncDiscountChips(d);
                updatePriceDetailInr();
                updatePriceDetailUsd();
                updateLivePreview();
            });

            function syncDiscountChips(val) {
                $('.cd-discount-chip').removeClass('active');
                $('.cd-discount-chip[data-discount="' + val + '"]').addClass('active');
            }

            $('#coin_price').on('input keyup change', function() {
                updatePriceDetailInr();
                updateLivePreview();
            });

            $('#coin_price_usd').on('input keyup change', function() {
                updatePriceDetailUsd();
                updateLivePreview();
            });

            $('#total_coins').on('input keyup change', function() {
                updatePriceDetailInr();
                updatePriceDetailUsd();
                updateLivePreview();
            });

            // -------------------------------------------------------------
            // Feature Points Builder Engine
            // -------------------------------------------------------------
            function addFeatureRow(title = '', status = 1) {
                var rowHtml = `
                    <div class="cd-point-row remove_row">
                        <div class="cd-point-handle" title="Feature line"><i class="fa fa-grip-vertical"></i></div>
                        <div class="cd-point-input-wrap">
                            <input type="text" class="form-control form-control-sm cd-field-control bonus_points"
                                   name="points[]" value="${$('<div>').text(title).html()}" placeholder="e.g. Up to 25 Active Recruiter Seats">
                        </div>
                        <div class="cd-point-status-wrap">
                            <select name="status_point[]" class="form-select form-select-sm cd-field-control point_status_select">
                                <option value="1" ${status == 1 ? 'selected' : ''}>Active</option>
                                <option value="0" ${status == 0 ? 'selected' : ''}>Inactive</option>
                            </select>
                        </div>
                        <div class="cd-point-action-wrap">
                            <button type="button" class="btn btn-sm cd-point-del-btn remove_point_btn" title="Delete Point">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                $('#points-builder-container').append(rowHtml);
                updateLivePreview();
            }

            // Click preset suggestion chip
            $('.cd-preset-chip').click(function() {
                var text = $(this).data('text');
                addFeatureRow(text, 1);
            });

            // Add Feature Row Button
            $('.addRowBtnbonus').click(function() {
                addFeatureRow('', 1);
                $('#points-builder-container .cd-point-row:last-child .bonus_points').focus();
            });

            // Feature points row removal
            $(document).on('click', '.remove_point_btn', function() {
                $(this).closest('.remove_row').remove();
                if ($('#points-builder-container .cd-point-row').length === 0) {
                    addFeatureRow('', 1);
                }
                updateLivePreview();
            });

            $(document).on('input keyup change', '.bonus_points, .point_status_select', function() {
                updateLivePreview();
            });

            // Form inputs change triggers live preview
            $('#coin_package, #event, #package_type, #status, #visibility_show').on('input change keyup', function() {
                updateLivePreview();
            });

            // Live Preview Updater
            function updateLivePreview() {
                var title = $('#coin_package').val() || 'Package Name';
                var coins = safeNumber($('#total_coins').val(), 0);
                var days = safeNumber($('#durations').val(), 365);
                var event = $('#event').val();
                var pckType = $('#package_type').val();
                var status = $('#status').val();
                var visibility = $('#visibility_show').val();

                var inrNet = safeNumber($('#total_price').val(), 0);
                var inrMonthly = $('#total_price_monthly').val() || '0.00';
                var inrDaily = $('#total_price_days').val() || '0.00';
                var inrRate = $('#total_price_rate').val() || '0.00';
                var discount = safeNumber($('#discount').val(), 0);

                var usdNet = safeNumber($('#total_price_usd').val(), 0);
                var usdMonthly = $('#total_price_usd_monthly').val() || '0.00';
                var usdDaily = $('#total_price_usd_days').val() || '0.00';
                var usdRate = $('#total_price_usd_rate').val() || '0.00';

                // Update Title
                $('#prev-pkg-title').text(title);

                // Update Coins
                $('#prev-coins-amount').text(Number(coins).toLocaleString());

                // Update Pricing & Rates based on currently selected preview currency
                if (currentPreviewCurrency === 'usd') {
                    $('#prev-unit-rate').text('$' + Number(usdRate).toFixed(2) + ' / coin');
                    $('#prev-main-price').text('$' + Number(usdNet).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    $('#prev-monthly-rate').text('$' + Number(usdMonthly).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    $('#prev-daily-rate').text('$' + Number(usdDaily).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                } else {
                    $('#prev-unit-rate').text('₹' + Number(inrRate).toFixed(2) + ' / coin');
                    $('#prev-main-price').text('₹' + Number(inrNet).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    $('#prev-monthly-rate').text('₹' + Number(inrMonthly).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    $('#prev-daily-rate').text('₹' + Number(inrDaily).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                }

                // Validity days & approx months
                var approxMos = days > 0 ? (days / 30).toFixed(1) + ' Mos' : 'Unlimited';
                $('#prev-validity-days').text(days + ' Days');
                $('#prev-validity-approx').text('(' + approxMos + ')');

                // Discount tag
                if (discount > 0) {
                    $('#prev-discount-tag').text('-' + discount + '%').show();
                } else {
                    $('#prev-discount-tag').hide();
                }

                // Event pill
                if (event === 'operational') {
                    $('#prev-pill-event').html('<i class="fa fa-cogs"></i> Operational').attr('class', 'cd-prev-pill cd-prev-pill-op');
                } else {
                    $('#prev-pill-event').html('<i class="fa fa-bullhorn"></i> Sales').attr('class', 'cd-prev-pill cd-prev-pill-sales');
                }

                // Tier pill & card border theme
                var lower = title.toLowerCase();
                var cardEl = $('#live-card-preview');
                cardEl.removeClass('tier-gold tier-silver tier-platinum tier-free tier-custom');

                if (pckType == '2' || lower.includes('free')) {
                    $('#prev-pill-tier').text('Free Tier');
                    cardEl.addClass('tier-free');
                    $('#prev-icon').attr('class', 'fa fa-gift');
                } else if (pckType == '1' || lower.includes('custom')) {
                    $('#prev-pill-tier').text('Custom');
                    cardEl.addClass('tier-custom');
                    $('#prev-icon').attr('class', 'fa fa-sliders-h');
                } else {
                    if (lower.includes('gold')) {
                        $('#prev-pill-tier').text('Gold Standard');
                        cardEl.addClass('tier-gold');
                        $('#prev-icon').attr('class', 'fa fa-crown');
                    } else if (lower.includes('silver')) {
                        $('#prev-pill-tier').text('Silver Standard');
                        cardEl.addClass('tier-silver');
                        $('#prev-icon').attr('class', 'fa fa-medal');
                    } else if (lower.includes('platinum')) {
                        $('#prev-pill-tier').text('Platinum Standard');
                        cardEl.addClass('tier-platinum');
                        $('#prev-icon').attr('class', 'fa fa-gem');
                    } else {
                        $('#prev-pill-tier').text('Standard');
                        cardEl.addClass('tier-gold');
                        $('#prev-icon').attr('class', 'fa fa-cube');
                    }
                }

                // Status pill
                if (status == '1') {
                    $('#prev-pill-status').text('Active').attr('class', 'cd-prev-pill cd-prev-pill-active');
                } else {
                    $('#prev-pill-status').text('Inactive').attr('class', 'cd-prev-pill cd-prev-pill-inactive');
                }

                // Visibility pill & mode text
                if (visibility == '2') {
                    $('#prev-visibility-pill').html('<i class="fa fa-eye"></i> Monthly Portal Plan');
                    $('#prev-billing-mode-text').text('Monthly Billing Emphasis');
                    $('#prev-main-term').text('/ Month');
                } else if (visibility == '0') {
                    $('#prev-visibility-pill').html('<i class="fa fa-eye"></i> Daily Portal Plan');
                    $('#prev-billing-mode-text').text('Daily Billing Emphasis');
                    $('#prev-main-term').text('/ Day');
                } else {
                    $('#prev-visibility-pill').html('<i class="fa fa-eye"></i> Yearly Portal Plan');
                    $('#prev-billing-mode-text').text('Annual Billing Emphasis');
                    $('#prev-main-term').text('/ Year');
                }

                // Update Points list (Only active points with text)
                var totalPointsCount = 0;
                var activePointsList = [];
                $('#points-builder-container .cd-point-row').each(function() {
                    var val = $(this).find('.bonus_points').val();
                    var st = $(this).find('.point_status_select').val();
                    if (val && val.trim().length > 0) {
                        totalPointsCount++;
                        if (st == '1') {
                            activePointsList.push(val.trim());
                        }
                    }
                });

                $('#prev-features-count').html('<i class="fa fa-shield-alt me-1"></i> ' + activePointsList.length + ' of ' + totalPointsCount + ' Active');

                var container = $('#prev-points-container');
                container.empty();
                if (activePointsList.length > 0) {
                    activePointsList.slice(0, 5).forEach(function(pt) {
                        container.append('<li class="cd-prev-point-item"><i class="fa fa-check-circle"></i><span>' + $('<div>').text(pt).html() + '</span></li>');
                    });
                    if (activePointsList.length > 5) {
                        container.append('<li class="cd-prev-point-item text-muted"><i class="fa fa-ellipsis-h text-muted"></i><span>+' + (activePointsList.length - 5) + ' more features</span></li>');
                    }
                } else {
                    container.append('<li class="cd-prev-point-item text-muted"><i class="fa fa-info-circle text-muted"></i><span>Standard platform points apply.</span></li>');
                }

                // Summary widget
                $('#quick-inr-summary').text('₹' + Number(inrNet).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' (' + discount + '% off)');
                $('#quick-usd-summary').text('$' + Number(usdNet).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' (' + discount + '% off)');
            }

            // AJAX Form Submission
            $('#save-form').click(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = new FormData($('#editSettings')[0]);
                var submitBtn = $('#save-form');
                var topBtn = $('#save-form-top');

                submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Saving...');
                topBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Saving...');

                $.ajax({
                    url: '{{ route('superadmin.company.coinPackageStore') }}',
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        submitBtn.prop('disabled', false).html('<i class="fa fa-save me-1"></i> Save Package');
                        topBtn.prop('disabled', false).html('<i class="fa fa-save me-1"></i> Save Changes');

                        if (response.status == 'success') {
                            Swal.fire({
                                text: response.message,
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            }).then(function() {
                                window.location = response.url;
                            });
                        } else {
                            if (response.errors) {
                                let errorMessages = Object.values(response.errors).flat().join('<br>');
                                Swal.fire({
                                    html: `<p class="fw-bold mb-2">Validation Errors:</p><div style="color: #ef4444; text-align: left; font-size: 13px;">${errorMessages}</div>`,
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Review & Fix",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                });
                            } else {
                                Swal.fire({
                                    text: response.message || "Failed to save package details. Please try again.",
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                });
                            }
                        }
                    },
                    error: function(response) {
                        submitBtn.prop('disabled', false).html('<i class="fa fa-save me-1"></i> Save Package');
                        topBtn.prop('disabled', false).html('<i class="fa fa-save me-1"></i> Save Changes');

                        if (response.responseJSON && response.responseJSON.errors) {
                            let errorMessages = Object.values(response.responseJSON.errors).flat().join('<br>');
                            Swal.fire({
                                html: `<p class="fw-bold mb-2">Validation Errors:</p><div style="color: #ef4444; text-align: left; font-size: 13px;">${errorMessages}</div>`,
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Review & Fix",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                        } else {
                            Swal.fire({
                                text: "An error occurred while saving the coin package. Please try again.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                        }
                    }
                });
            });

            // Initial calculation run and preview render
            updatePriceDetailInr();
            updatePriceDetailUsd();
            updateLivePreview();

        });
    </script>
@endpush
