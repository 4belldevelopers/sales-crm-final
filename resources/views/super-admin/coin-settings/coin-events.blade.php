@extends('layouts.superadmin_layout')

@php
    use App\Helper\GeneralMethod;
@endphp

@push('head-script')
    <style>
        /* ==========================================================================
           Coin Features & Billing Events — Master Enterprise Card + Sub-Card Design
           Strictly Optimized for 15.6" Screens (1366x768 & 1920x1080) and Smaller
           GUARANTEED: Zero Horizontal Scrollbar (0% Overflow) Across All Viewports
           ========================================================================== */

        /* ===== Manage Top Gap: collapse Metronic's toolbar-fixed padding and content gap ===== */
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
                padding-top: calc(55px + 10px) !important;
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

        .cf-wrapper, .cf-wrapper * {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
            box-sizing: border-box !important;
        }

        .cf-wrapper {
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: hidden !important;
        }

        /* ==========================================================================
           Tier 1: Top Header Toolbar Card
           ========================================================================== */
        .cf-header-card {
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

        .cf-header-left {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            min-width: 0 !important;
            flex-wrap: wrap !important;
        }

        .cf-header-icon {
            width: 40px !important;
            height: 40px !important;
            border-radius: 9px !important;
            background: #fffbeb !important;
            border: 1px solid #fef08a !important;
            color: #d97706 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 18px !important;
            flex-shrink: 0 !important;
        }

        .cf-header-titles {
            display: flex !important;
            flex-direction: column !important;
        }

        .cf-header-title {
            font-size: 15.5px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            line-height: 1.25 !important;
            letter-spacing: -0.2px !important;
        }

        .cf-header-sub {
            font-size: 11.5px !important;
            color: #64748b !important;
            margin-top: 2px !important;
            line-height: 1.2 !important;
        }

        /* Header Metric Chips */
        .cf-header-chips {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            margin-left: 8px !important;
            flex-wrap: wrap !important;
        }

        .cf-kpi-chip {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            padding: 3px 9px !important;
            border-radius: 6px !important;
            line-height: 1.2 !important;
        }

        .cf-kpi-chip.chip-total {
            background: #f1f5f9 !important;
            color: #475569 !important;
            border: 1px solid #e2e8f0 !important;
        }

        .cf-kpi-chip.chip-active {
            background: #ecfdf5 !important;
            color: #059669 !important;
            border: 1px solid #a7f3d0 !important;
        }

        .cf-kpi-chip.chip-operational {
            background: #eff6ff !important;
            color: #2563eb !important;
            border: 1px solid #bfdbfe !important;
        }

        .cf-kpi-chip.chip-sales {
            background: #fff7ed !important;
            color: #ea580c !important;
            border: 1px solid #fed7aa !important;
        }

        .cf-header-right {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            flex-wrap: wrap !important;
        }

        /* Search Sub-Card inside Top Header Card */
        .cf-search-subcard {
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

        .cf-search-subcard:focus-within {
            background: #ffffff !important;
            border-color: #009ef7 !important;
            box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
        }

        .cf-search-subcard i.fa-magnifying-glass {
            color: #94a3b8 !important;
            font-size: 12.5px !important;
            flex-shrink: 0 !important;
        }

        .cf-search-subcard input {
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

        .cf-search-subcard input::placeholder {
            color: #94a3b8 !important;
            font-size: 12px !important;
        }

        .cf-search-clear {
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

        .cf-search-clear:hover {
            color: #475569 !important;
        }

        .cf-add-btn {
            height: 36px !important;
            padding: 0 14px !important;
            font-size: 12px !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            border-radius: 8px !important;
            white-space: nowrap !important;
        }

        /* ==========================================================================
           Tier 2: Sub-Tabs Filter Card
           ========================================================================== */
        .cf-tabs-card {
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

        .cf-tabs-group {
            display: flex !important;
            align-items: center !important;
            gap: 7px !important;
            flex-wrap: wrap !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }

        .cf-tab-btn {
            font-size: 11.5px !important;
            font-weight: 600 !important;
            padding: 6px 13px !important;
            border-radius: 8px !important;
            background: #ffffff !important;
            color: #475569 !important;
            border: 1px solid #e2e8f0 !important;
            transition: all 0.15s ease !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            cursor: pointer !important;
            line-height: 1.3 !important;
            white-space: nowrap !important;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02) !important;
        }

        .cf-tab-btn:hover {
            background: #f8fafc !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }

        .cf-tab-btn.active {
            background: #009ef7 !important;
            color: #ffffff !important;
            border-color: #009ef7 !important;
            box-shadow: 0 2px 4px rgba(0, 158, 247, 0.2) !important;
        }

        .cf-tab-badge {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 20px !important;
            height: 18px !important;
            padding: 0 6px !important;
            border-radius: 10px !important;
            font-size: 10.5px !important;
            font-weight: 700 !important;
            background: #f1f5f9 !important;
            color: #475569 !important;
            line-height: 1 !important;
            transition: all 0.15s ease !important;
        }

        .cf-tab-btn.active .cf-tab-badge {
            background: rgba(255, 255, 255, 0.28) !important;
            color: #ffffff !important;
        }

        /* ==========================================================================
           Tier 3: Primary Card Stream & Discrete Sub-Cards
           ========================================================================== */
        .cf-stream {
            display: flex !important;
            flex-direction: column !important;
            gap: 10px !important;
            width: 100% !important;
        }

        .cf-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
            transition: all 0.15s ease-in-out !important;
            width: 100% !important;
            overflow: hidden !important;
        }

        .cf-card:hover {
            border-color: #cbd5e1 !important;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06) !important;
        }

        /* 4 Discrete Sub-Cards Grid */
        .cf-card-grid {
            display: grid !important;
            grid-template-columns: minmax(0, 2.7fr) minmax(0, 1.6fr) minmax(0, 1.6fr) minmax(0, 1.4fr) !important;
            gap: 12px !important;
            padding: 12px 16px !important;
            align-items: center !important;
            width: 100% !important;
        }

        @media (max-width: 1200px) {
            .cf-card-grid {
                grid-template-columns: minmax(0, 2.2fr) minmax(0, 1.4fr) minmax(0, 1.4fr) minmax(0, 1.4fr) !important;
            }
        }

        @media (max-width: 991px) {
            .cf-card-grid {
                grid-template-columns: 1fr 1fr !important;
                gap: 14px !important;
            }
        }

        @media (max-width: 576px) {
            .cf-card-grid {
                grid-template-columns: 1fr !important;
                gap: 12px !important;
            }
        }

        /* Sub-Card 1: Feature Identity & Scope */
        .cf-subcard-identity {
            display: flex !important;
            align-items: flex-start !important;
            gap: 12px !important;
            min-width: 0 !important;
        }

        .cf-avatar-box {
            width: 38px !important;
            height: 38px !important;
            border-radius: 8px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 15px !important;
            flex-shrink: 0 !important;
            margin-top: 2px !important;
        }

        .cf-avatar-box.avatar-operational {
            background: #eff6ff !important;
            border: 1px solid #bfdbfe !important;
            color: #2563eb !important;
        }

        .cf-avatar-box.avatar-sales {
            background: #fff7ed !important;
            border: 1px solid #fed7aa !important;
            color: #ea580c !important;
        }

        .cf-identity-content {
            flex: 1 !important;
            min-width: 0 !important;
        }

        .cf-title-row {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            flex-wrap: wrap !important;
        }

        .cf-event-title {
            font-size: 13.5px !important;
            font-weight: 600 !important;
            color: #0f172a !important;
            line-height: 1.3 !important;
            text-decoration: none !important;
            word-break: break-word !important;
        }

        .cf-id-badge {
            font-size: 10px !important;
            font-weight: 700 !important;
            background: #f1f5f9 !important;
            color: #475569 !important;
            border: 1px solid #e2e8f0 !important;
            padding: 1px 6px !important;
            border-radius: 4px !important;
            letter-spacing: 0.3px !important;
        }

        .cf-scope-row {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            margin-top: 3px !important;
            flex-wrap: wrap !important;
        }

        .cf-scope-pill {
            font-size: 10.5px !important;
            font-weight: 600 !important;
            padding: 2px 7px !important;
            border-radius: 4px !important;
            line-height: 1.2 !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
        }

        .cf-scope-pill.scope-operational {
            background: #eff6ff !important;
            color: #1d4ed8 !important;
            border: 1px solid #dbeafe !important;
        }

        .cf-scope-pill.scope-sales {
            background: #fff7ed !important;
            color: #c2410c !important;
            border: 1px solid #ffedd5 !important;
        }

        .cf-desc-text {
            font-size: 11px !important;
            color: #64748b !important;
            margin-top: 3px !important;
            line-height: 1.35 !important;
            display: -webkit-box !important;
            -webkit-line-clamp: 2 !important;
            -webkit-box-orient: vertical !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        /* Sub-Card 2: Rate & Deductions */
        .cf-subcard-rate {
            display: flex !important;
            flex-direction: column !important;
            gap: 4px !important;
            min-width: 0 !important;
        }

        .cf-subcard-label {
            font-size: 9.5px !important;
            font-weight: 700 !important;
            color: #94a3b8 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            line-height: 1.2 !important;
        }

        .cf-rate-pill {
            display: inline-flex !important;
            align-items: center !important;
            gap: 5px !important;
            font-size: 12.5px !important;
            font-weight: 700 !important;
            padding: 3px 8px !important;
            border-radius: 6px !important;
            width: fit-content !important;
        }

        .cf-rate-pill.rate-paid {
            background: #fffbeb !important;
            color: #b45309 !important;
            border: 1px solid #fde68a !important;
        }

        .cf-rate-pill.rate-free {
            background: #f0fdf4 !important;
            color: #15803d !important;
            border: 1px solid #bbf7d0 !important;
        }

        .cf-coin-unit {
            font-size: 10.5px !important;
            font-weight: 600 !important;
            color: inherit !important;
            opacity: 0.85 !important;
        }

        .cf-model-pill {
            font-size: 11px !important;
            font-weight: 500 !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
        }

        .cf-model-pill.model-daily {
            color: #0284c7 !important;
        }

        .cf-model-pill.model-onetime {
            color: #64748b !important;
        }

        /* Sub-Card 3: Minimum Wallet Threshold */
        .cf-subcard-wallet {
            display: flex !important;
            flex-direction: column !important;
            gap: 4px !important;
            min-width: 0 !important;
        }

        .cf-threshold-pill {
            display: inline-flex !important;
            align-items: center !important;
            gap: 5px !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            padding: 3px 8px !important;
            border-radius: 6px !important;
            width: fit-content !important;
        }

        .cf-threshold-pill.threshold-gated {
            background: #f0f9ff !important;
            color: #0369a1 !important;
            border: 1px solid #bae6fd !important;
        }

        .cf-threshold-pill.threshold-open {
            background: #f8fafc !important;
            color: #475569 !important;
            border: 1px solid #e2e8f0 !important;
        }

        .cf-threshold-sub {
            font-size: 10.5px !important;
            color: #94a3b8 !important;
            line-height: 1.25 !important;
        }

        /* Sub-Card 4: Live Status & Controls */
        .cf-subcard-controls {
            display: flex !important;
            flex-direction: column !important;
            gap: 6px !important;
            min-width: 0 !important;
        }

        .cf-status-toggle-row {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
        }

        .cf-status-badge {
            font-size: 11px !important;
            font-weight: 700 !important;
            padding: 2px 7px !important;
            border-radius: 4px !important;
            line-height: 1.2 !important;
        }

        .cf-status-badge.active {
            background: #ecfdf5 !important;
            color: #059669 !important;
            border: 1px solid #a7f3d0 !important;
        }

        .cf-status-badge.inactive {
            background: #f1f5f9 !important;
            color: #64748b !important;
            border: 1px solid #e2e8f0 !important;
        }

        .cf-actions-group {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
        }

        .cf-btn-edit {
            font-size: 11px !important;
            font-weight: 600 !important;
            padding: 4px 10px !important;
            height: 28px !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 5px !important;
            border-radius: 6px !important;
        }

        /* Bottom Metadata Strip */
        .cf-card-strip {
            background: #fafafa !important;
            border-top: 1px solid #f1f5f9 !important;
            padding: 6px 16px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            font-size: 10.5px !important;
            color: #94a3b8 !important;
            flex-wrap: wrap !important;
            gap: 8px !important;
        }

        .cf-card-strip-left {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            flex-wrap: wrap !important;
        }

        .cf-strip-item {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
        }

        /* ==========================================================================
           Empty State
           ========================================================================== */
        .cf-empty-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 44px 20px !important;
            text-align: center !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
        }

        .cf-empty-icon {
            width: 52px !important;
            height: 52px !important;
            border-radius: 12px !important;
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            color: #94a3b8 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 24px !important;
            margin-bottom: 12px !important;
        }

        .cf-empty-title {
            font-size: 14.5px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            margin-bottom: 4px !important;
        }

        .cf-empty-sub {
            font-size: 12px !important;
            color: #64748b !important;
            max-width: 380px !important;
            margin: 0 auto 16px auto !important;
            line-height: 1.4 !important;
        }

        /* ==========================================================================
           Slide-Over Drawer Styling (580px Rigid Architecture)
           ========================================================================== */
        #kt_coin_drawer.cf-drawer {
            position: fixed !important;
            top: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            left: auto !important;
            height: 100vh !important;
            max-height: 100vh !important;
            width: 580px !important;
            max-width: 95vw !important;
            background: #ffffff !important;
            box-shadow: -8px 0 32px rgba(15, 23, 42, 0.16) !important;
            z-index: 1055 !important;
            overflow: hidden !important;
            transform: translateX(105%);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        #kt_coin_drawer.cf-drawer.drawer-on {
            transform: translateX(0) !important;
        }

        @media (max-width: 767.98px) {
            #kt_coin_drawer.cf-drawer {
                width: 95vw !important;
            }
        }

        .cf-drawer-card {
            display: flex !important;
            flex-direction: column !important;
            height: 100vh !important;
            max-height: 100vh !important;
            overflow: hidden !important;
            background: #ffffff !important;
            border: none !important;
            position: relative !important;
        }

        .cf-drawer-header {
            padding: 16px 22px !important;
            border-bottom: 1px solid #e2e8f0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            background: #ffffff !important;
            flex-shrink: 0 !important;
            gap: 12px !important;
        }

        .cf-drawer-header-left {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            min-width: 0 !important;
        }

        .cf-drawer-icon-box {
            width: 40px !important;
            height: 40px !important;
            border-radius: 9px !important;
            background: #fffbeb !important;
            border: 1px solid #fef08a !important;
            color: #d97706 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 17px !important;
            flex-shrink: 0 !important;
        }

        .cf-drawer-header-info {
            display: flex !important;
            flex-direction: column !important;
            min-width: 0 !important;
        }

        .cf-drawer-title-row {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            flex-wrap: wrap !important;
        }

        .cf-drawer-title {
            font-size: 15px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            line-height: 1.25 !important;
            margin: 0 !important;
        }

        .cf-drawer-sub {
            font-size: 11px !important;
            color: #64748b !important;
            margin-top: 2px !important;
            line-height: 1.2 !important;
        }

        .cf-drawer-close-btn {
            width: 32px !important;
            height: 32px !important;
            border-radius: 8px !important;
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            color: #64748b !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 14px !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
            flex-shrink: 0 !important;
        }

        .cf-drawer-close-btn:hover {
            background: #f1f5f9 !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
        }

        .cf-drawer-body {
            flex: 1 1 auto !important;
            overflow-y: auto !important;
            padding: 20px 22px !important;
            max-height: calc(100vh - 130px) !important;
            background: #ffffff !important;
        }

        .cf-drawer-section-title {
            font-size: 11px !important;
            font-weight: 700 !important;
            color: #475569 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            margin-bottom: 12px !important;
            padding-bottom: 6px !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }

        .cf-drawer-footer {
            padding: 14px 22px !important;
            border-top: 1px solid #e2e8f0 !important;
            background: #ffffff !important;
            display: flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
            gap: 10px !important;
            position: sticky !important;
            bottom: 0 !important;
            z-index: 10 !important;
            flex-shrink: 0 !important;
        }

        .cf-form-label {
            font-size: 12px !important;
            font-weight: 600 !important;
            color: #334155 !important;
            margin-bottom: 6px !important;
            display: flex !important;
            align-items: center !important;
            gap: 4px !important;
        }

        .cf-input {
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 8px 12px !important;
            font-size: 12.5px !important;
            color: #0f172a !important;
            background: #ffffff !important;
            height: 40px !important;
            width: 100% !important;
            outline: none !important;
            transition: all 0.15s ease !important;
        }

        .cf-input:focus {
            border-color: #009ef7 !important;
            box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
        }

        .cf-input-icon-wrap {
            position: relative !important;
            width: 100% !important;
        }

        .cf-input-icon {
            position: absolute !important;
            left: 12px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            font-size: 13px !important;
            pointer-events: none !important;
        }

        .cf-input-with-icon {
            padding-left: 36px !important;
        }

        .cf-textarea {
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 10px 12px !important;
            font-size: 12.5px !important;
            color: #0f172a !important;
            background: #ffffff !important;
            width: 100% !important;
            outline: none !important;
            transition: all 0.15s ease !important;
            resize: vertical !important;
            min-height: 85px !important;
        }

        .cf-textarea:focus {
            border-color: #009ef7 !important;
            box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
        }

        .cf-status-box {
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 12px 14px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 12px !important;
        }

        .drawer-overlay,
        .cf-drawer-overlay {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            background: rgba(15, 23, 42, 0.35) !important;
            z-index: 1050 !important;
            cursor: pointer !important;
            backdrop-filter: blur(1px) !important;
        }
    </style>
@endpush

@section('content')
    <div class="content d-flex flex-column flex-column-fluid pt-0 px-3 px-lg-6 cf-wrapper" id="kt_content">

        <!-- =====================================================================
             TIER 1: TOP HEADER TOOLBAR CARD (White Background #ffffff, Border #e2e8f0)
             ===================================================================== -->
        <div class="cf-header-card">
            <div class="cf-header-left">
                <div class="cf-header-icon">
                    <i class="fa-solid fa-coins"></i>
                </div>
                <div class="cf-header-titles">
                    <div class="cf-header-title">Coin Features & Billing Events</div>
                    <div class="cf-header-sub">Configure ATS operational & sales coin consumption, rates, and deduction rules.</div>
                </div>

                <!-- KPI Metric Pills -->
                <div class="cf-header-chips d-none d-md-flex">
                    <span class="cf-kpi-chip chip-total">
                        <i class="fa-solid fa-layer-group fs-9"></i> <span id="kpiTotalSpan">{{ $kpiTotal ?? count($coinEvents) }}</span> Features
                    </span>
                    <span class="cf-kpi-chip chip-active">
                        <i class="fa-solid fa-circle-check fs-9"></i> <span id="kpiActiveSpan">{{ $kpiActive ?? 0 }}</span> Active
                    </span>
                    <span class="cf-kpi-chip chip-operational">
                        <i class="fa-solid fa-gear fs-9"></i> <span id="kpiOperationalSpan">{{ $kpiOperational ?? 0 }}</span> Operational
                    </span>
                    <span class="cf-kpi-chip chip-sales">
                        <i class="fa-solid fa-chart-line fs-9"></i> <span id="kpiSalesSpan">{{ $kpiSales ?? 0 }}</span> Sales
                    </span>
                </div>
            </div>

            <div class="cf-header-right">
                <!-- Search Sub-Card -->
                <div class="cf-search-subcard">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="inputCoinSearch" placeholder="Search features by name, ID, type..." autocomplete="off">
                    <button type="button" class="cf-search-clear" id="btnCoinSearchClear" style="display: none;" title="Clear search">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Add Feature Primary Button -->
                <button type="button" class="btn btn-sm btn-primary fw-bold cf-add-btn" id="btnOpenAddDrawer">
                    <i class="fa-solid fa-plus me-1"></i> Add Feature
                </button>
            </div>
        </div>

        <!-- =====================================================================
             TIER 2: SUB-TABS CARD (White Background #ffffff, Border #e2e8f0)
             ===================================================================== -->
        <div class="cf-tabs-card">
            <div class="cf-tabs-group">
                <button type="button" class="cf-tab-btn active js-coin-filter-btn" data-filter="all">
                    <span>All Features</span>
                    <span class="cf-tab-badge" id="tabCountAll">{{ $kpiTotal ?? count($coinEvents) }}</span>
                </button>
                <button type="button" class="cf-tab-btn js-coin-filter-btn" data-filter="operational">
                    <i class="fa-solid fa-gear text-primary fs-8"></i>
                    <span>Operational</span>
                    <span class="cf-tab-badge" id="tabCountOperational">{{ $kpiOperational ?? 0 }}</span>
                </button>
                <button type="button" class="cf-tab-btn js-coin-filter-btn" data-filter="sales">
                    <i class="fa-solid fa-chart-line text-warning fs-8"></i>
                    <span>Sales & Leads</span>
                    <span class="cf-tab-badge" id="tabCountSales">{{ $kpiSales ?? 0 }}</span>
                </button>
                <button type="button" class="cf-tab-btn js-coin-filter-btn" data-filter="daily">
                    <i class="fa-solid fa-repeat text-info fs-8"></i>
                    <span>Daily Recurring</span>
                    <span class="cf-tab-badge" id="tabCountDaily">{{ $kpiDaily ?? 0 }}</span>
                </button>
                <button type="button" class="cf-tab-btn js-coin-filter-btn" data-filter="onetime">
                    <i class="fa-solid fa-bolt text-muted fs-8"></i>
                    <span>One-time Pay-Per-Use</span>
                    <span class="cf-tab-badge" id="tabCountOnetime">{{ $kpiOnetime ?? 0 }}</span>
                </button>
                <button type="button" class="cf-tab-btn js-coin-filter-btn" data-filter="active">
                    <i class="fa-solid fa-circle-check text-success fs-8"></i>
                    <span>Active</span>
                    <span class="cf-tab-badge" id="tabCountActive">{{ $kpiActive ?? 0 }}</span>
                </button>
                <button type="button" class="cf-tab-btn js-coin-filter-btn" data-filter="inactive">
                    <i class="fa-solid fa-circle-pause text-muted fs-8"></i>
                    <span>Inactive</span>
                    <span class="cf-tab-badge" id="tabCountInactive">{{ $kpiInactive ?? 0 }}</span>
                </button>
            </div>
        </div>

        <!-- =====================================================================
             TIER 3: PRIMARY ENTITY CARD STREAM (Hover Elevation, Radius 10px)
             ===================================================================== -->
        <div class="cf-stream" id="coinFeaturesStream">
            @if ($coinEvents && count($coinEvents) > 0)
                @foreach ($coinEvents as $key => $val)
                    <div class="cf-card js-coin-card"
                         id="cf-card-{{ $val->id }}"
                         data-id="{{ $val->id }}"
                         data-type="{{ $val->event_type }}"
                         data-deduction="{{ $val->deduction_type }}"
                         data-status="{{ $val->status }}"
                         data-title="{{ strtolower($val->title) }}"
                         data-desc="{{ strtolower($val->description ?? '') }}"
                         data-rate="{{ $val->default_rate }}"
                         data-min="{{ $val->minimum_required ?? 0 }}">
                        
                        <!-- 4 Discrete Sub-Cards Grid -->
                        <div class="cf-card-grid">

                            <!-- SUB-CARD 1: Feature Identity & Scope -->
                            <div class="cf-subcard-identity">
                                <div class="cf-avatar-box avatar-{{ $val->event_type }}">
                                    @if ($val->event_type == 'sales')
                                        <i class="fa-solid fa-chart-line"></i>
                                    @else
                                        <i class="fa-solid fa-gear"></i>
                                    @endif
                                </div>
                                <div class="cf-identity-content">
                                    <div class="cf-title-row">
                                        <a href="javascript:void(0);" 
                                           class="cf-event-title js-quick-edit-trigger"
                                           data-id="{{ $val->id }}"
                                           data-title="{{ $val->title }}"
                                           data-type="{{ $val->event_type }}"
                                           data-rate="{{ $val->default_rate }}"
                                           data-min="{{ $val->minimum_required ?? 0 }}"
                                           data-deduction="{{ $val->deduction_type }}"
                                           data-desc="{{ $val->description ?? '' }}"
                                           data-status="{{ $val->status }}"
                                           title="Click to edit feature">
                                            {{ $val->title }}
                                        </a>
                                        <span class="cf-id-badge">#CE-{{ str_pad($val->id, 3, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                    <div class="cf-scope-row">
                                        @if ($val->event_type == 'operational')
                                            <span class="cf-scope-pill scope-operational">
                                                <i class="fa-solid fa-layer-group fs-9"></i> Operational Feature
                                            </span>
                                        @else
                                            <span class="cf-scope-pill scope-sales">
                                                <i class="fa-solid fa-trophy fs-9"></i> Sales Lead & Marketplace
                                            </span>
                                        @endif
                                    </div>
                                    <div class="cf-desc-text" title="{{ $val->description ?: 'Platform automated ATS billing event.' }}">
                                        {{ $val->description ?: 'Standard platform billing event for recruitment workflow and talent exchange.' }}
                                    </div>
                                </div>
                            </div>

                            <!-- SUB-CARD 2: Coin Rate & Deduction Model -->
                            <div class="cf-subcard-rate">
                                <div class="cf-subcard-label">Coin Rate & Deduction Model</div>
                                <div>
                                    @if ($val->default_rate > 0)
                                        <span class="cf-rate-pill rate-paid">
                                            <i class="fa-solid fa-coins text-warning"></i>
                                            {{ number_format($val->default_rate, 2) }}
                                            <span class="cf-coin-unit">Coins</span>
                                        </span>
                                    @else
                                        <span class="cf-rate-pill rate-free">
                                            <i class="fa-solid fa-gift text-success"></i> Free (0 Coins)
                                        </span>
                                    @endif
                                </div>
                                <div class="mt-1">
                                    @if ($val->deduction_type == 'daily')
                                        <span class="cf-model-pill model-daily">
                                            <i class="fa-solid fa-repeat"></i> Daily Recurring
                                        </span>
                                    @else
                                        <span class="cf-model-pill model-onetime">
                                            <i class="fa-solid fa-bolt text-warning"></i> One-time Pay-Per-Use
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- SUB-CARD 3: Minimum Wallet Required -->
                            <div class="cf-subcard-wallet">
                                <div class="cf-subcard-label">Minimum Wallet Required</div>
                                <div>
                                    @if ($val->minimum_required > 0)
                                        <span class="cf-threshold-pill threshold-gated">
                                            <i class="fa-solid fa-shield-halved"></i>
                                            Min. {{ number_format($val->minimum_required, 0) }} Coins
                                        </span>
                                        <div class="cf-threshold-sub">Required balance in company wallet to access</div>
                                    @else
                                        <span class="cf-threshold-pill threshold-open">
                                            <i class="fa-solid fa-circle-check text-success"></i> No Gate Limit
                                        </span>
                                        <div class="cf-threshold-sub">Accessible with any active coin balance</div>
                                    @endif
                                </div>
                            </div>

                            <!-- SUB-CARD 4: Live Status & Controls -->
                            <div class="cf-subcard-controls">
                                <div class="cf-subcard-label">Status & Actions</div>
                                <div class="cf-status-toggle-row">
                                    <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid m-0">
                                        <input class="form-check-input js-status-toggle" 
                                               type="checkbox" 
                                               value="{{ $val->id }}" 
                                               id="statusSwitch{{ $val->id }}"
                                               {{ $val->status ? 'checked' : '' }} 
                                               title="Toggle feature status">
                                    </div>
                                    <span class="cf-status-badge {{ $val->status ? 'active' : 'inactive' }}" 
                                          id="statusBadge{{ $val->id }}">
                                        {{ $val->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <div class="cf-actions-group mt-1">
                                    <button type="button" 
                                            class="btn btn-sm btn-light-primary cf-btn-edit js-quick-edit-trigger"
                                            data-id="{{ $val->id }}"
                                            data-title="{{ $val->title }}"
                                            data-type="{{ $val->event_type }}"
                                            data-rate="{{ $val->default_rate }}"
                                            data-min="{{ $val->minimum_required ?? 0 }}"
                                            data-deduction="{{ $val->deduction_type }}"
                                            data-desc="{{ $val->description ?? '' }}"
                                            data-status="{{ $val->status }}"
                                            title="Edit feature settings">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </button>
                                    <a href="{{ route('superadmin.company.addEditcoinFeatutes', encrypt($val->id)) }}" 
                                       class="btn btn-sm btn-icon btn-light-secondary" 
                                       style="width: 28px; height: 28px; border-radius: 6px;"
                                       title="Open full edit page" 
                                       data-bs-toggle="tooltip">
                                        <i class="fa-solid fa-arrow-up-right-from-square fs-8 text-muted"></i>
                                    </a>
                                </div>
                            </div>

                        </div>

                        <!-- Bottom Activity / Metadata Strip -->
                        <div class="cf-card-strip">
                            <div class="cf-card-strip-left">
                                <span class="cf-strip-item">
                                    <i class="fa-regular fa-id-badge fs-9"></i> Event Code: <strong>CE-{{ str_pad($val->id, 4, '0', STR_PAD_LEFT) }}</strong>
                                </span>
                                <span>•</span>
                                <span class="cf-strip-item">
                                    <i class="fa-regular fa-clock fs-9"></i> Cycle: <strong>{{ ucfirst($val->deduction_type) }}</strong>
                                </span>
                                <span>•</span>
                                <span class="cf-strip-item">
                                    <i class="fa-solid fa-shield fs-9"></i> Gating: <strong>{{ $val->minimum_required > 0 ? number_format($val->minimum_required, 0) . ' Coins' : 'Open' }}</strong>
                                </span>
                            </div>
                            <div>
                                <span class="text-muted fs-9">JERO God-Level Multi-Tenant ATS Billing Rule</span>
                            </div>
                        </div>

                    </div>
                @endforeach
            @endif
        </div>

        <!-- Empty State Container -->
        <div class="cf-empty-card" id="cfEmptyState" style="display: none;">
            <div class="cf-empty-icon">
                <i class="fa-solid fa-coins"></i>
            </div>
            <div class="cf-empty-title">No Matching Coin Features Found</div>
            <div class="cf-empty-sub">We couldn't find any coin features matching your current search query or sub-tab filter.</div>
            <button type="button" class="btn btn-sm btn-light-primary fw-bold" id="btnResetFilters">
                <i class="fa-solid fa-rotate-left me-1"></i> Reset All Filters
            </button>
        </div>

        @if ($coinEvents instanceof \Illuminate\Pagination\LengthAwarePaginator && $coinEvents->hasPages())
            <div class="mt-4" id="paginationWrapper">
                <x-custom-pagination :paginator="$coinEvents" :extra="['pagetype' => 'salestab']" />
            </div>
        @endif

    </div>

    <!-- =====================================================================
         METRONIC SLIDE-OVER DRAWER FOR ADD & QUICK EDIT (580px Rigid Flex)
         ===================================================================== -->
    <div id="kt_coin_drawer_toggle" style="display: none;"></div>
    <div id="kt_coin_drawer_close" style="display: none;"></div>
    
    <div id="kt_coin_drawer" 
         class="cf-drawer" 
         data-kt-drawer="true"
         data-kt-drawer-name="coinFeatureDrawer" 
         data-kt-drawer-activate="true" 
         data-kt-drawer-overlay="true"
         data-kt-drawer-width="{default:'95%', 'sm': '580px'}" 
         data-kt-drawer-direction="end"
         data-kt-drawer-toggle="#kt_coin_drawer_toggle" 
         data-kt-drawer-close="#btnDrawerCloseIcon, #btnDrawerCancel, #kt_coin_drawer_close">
        
        <div class="card shadow-none rounded-0 w-100 h-100 cf-drawer-card">
            
            <!-- 1. Sticky Drawer Header -->
            <div class="cf-drawer-header">
                <div class="cf-drawer-header-left">
                    <div class="cf-drawer-icon-box">
                        <i class="fa-solid fa-coins"></i>
                    </div>
                    <div class="cf-drawer-header-info">
                        <div class="cf-drawer-title-row">
                            <h3 class="cf-drawer-title" id="drawerTitle">Create Coin Feature</h3>
                            <span class="badge badge-light-primary fw-bold fs-8 d-none" id="drawerEventCodeBadge">#CE-001</span>
                        </div>
                        <div class="cf-drawer-sub">Configure platform billing rate, deduction cycle, and wallet limits.</div>
                    </div>
                </div>
                <button type="button" class="cf-drawer-close-btn" id="btnDrawerCloseIcon" data-kt-drawer-dismiss="true" title="Close Drawer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- 2. Scrollable Drawer Body Form -->
            <div class="cf-drawer-body">
                <form id="coinFeatureDrawerForm" autocomplete="off">
                    @csrf
                    <input type="hidden" name="token_id" id="drawer_token_id" value="">

                    <!-- Section 1: Scope & Charging Model -->
                    <div class="cf-drawer-section-title">
                        <i class="fa-solid fa-layer-group text-primary fs-8"></i>
                        <span>Scope & Charging Model</span>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <label class="cf-form-label required" for="drawer_event_type">Event Scope Type</label>
                            <select name="event_type" id="drawer_event_type" class="form-select cf-input" required>
                                <option value="operational">Operational Feature</option>
                                <option value="sales">Sales & Marketplace</option>
                            </select>
                            <div class="text-muted fs-8 mt-1">Operational workflows vs sales leads.</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="cf-form-label required" for="drawer_deduction_type">Deduction Cycle Model</label>
                            <select name="deduction_type" id="drawer_deduction_type" class="form-select cf-input" required>
                                <option value="onetime">One-time Pay-Per-Use</option>
                                <option value="daily">Daily Recurring</option>
                            </select>
                            <div class="text-muted fs-8 mt-1">Per-action trigger or daily active debit.</div>
                        </div>
                    </div>

                    <!-- Section 2: Feature Identity -->
                    <div class="cf-drawer-section-title mt-4">
                        <i class="fa-solid fa-tag text-info fs-8"></i>
                        <span>Feature Identity</span>
                    </div>

                    <div class="mb-4">
                        <label class="cf-form-label required" for="drawer_title">Feature / Event Name</label>
                        <input type="text" 
                               class="form-control cf-input" 
                               id="drawer_title" 
                               name="title" 
                               placeholder="e.g. CV Access, Mass Mail, Video Interview" 
                               required>
                        <div class="text-muted fs-8 mt-1">The display name of the billing event shown in ATS logs.</div>
                    </div>

                    <!-- Section 3: Coin Economics & Thresholds -->
                    <div class="cf-drawer-section-title mt-4">
                        <i class="fa-solid fa-coins text-warning fs-8"></i>
                        <span>Coin Economics & Wallet Gating</span>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <label class="cf-form-label required" for="drawer_default_rate">Default Coin Rate</label>
                            <div class="cf-input-icon-wrap">
                                <span class="cf-input-icon text-warning">
                                    <i class="fa-solid fa-coins"></i>
                                </span>
                                <input type="number" 
                                       step="0.01" 
                                       min="0" 
                                       class="form-control cf-input cf-input-with-icon" 
                                       id="drawer_default_rate" 
                                       name="default_rate" 
                                       value="1.00" 
                                       required>
                            </div>
                            <div class="text-muted fs-8 mt-1">Coins charged per execution (0 for Free).</div>
                        </div>

                        <div class="col-sm-6">
                            <label class="cf-form-label required" for="drawer_minimum_required">Min Wallet Required</label>
                            <div class="cf-input-icon-wrap">
                                <span class="cf-input-icon text-primary">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </span>
                                <input type="number" 
                                       step="1" 
                                       min="0" 
                                       class="form-control cf-input cf-input-with-icon" 
                                       id="drawer_minimum_required" 
                                       name="minimum_required" 
                                       value="0" 
                                       required>
                            </div>
                            <div class="text-muted fs-8 mt-1">Min balance to unlock this feature.</div>
                        </div>
                    </div>

                    <!-- Section 4: Operational Description -->
                    <div class="cf-drawer-section-title mt-4">
                        <i class="fa-solid fa-align-left text-primary fs-8"></i>
                        <span>Description</span>
                    </div>

                    <div class="mb-4">
                        <label class="cf-form-label" for="drawer_description">Feature Description</label>
                        <textarea class="form-control cf-textarea" 
                                  id="drawer_description" 
                                  name="description" 
                                  rows="3" 
                                  placeholder="Provide transparent details on how coins are deducted and what exact workflow action is performed..."></textarea>
                    </div>

                    <!-- Section 5: Active Operational Status -->
                    <div class="cf-drawer-section-title mt-4">
                        <i class="fa-solid fa-toggle-on text-success fs-8"></i>
                        <span>Operational Availability</span>
                    </div>

                    <div class="cf-status-box mb-3">
                        <div>
                            <div class="fw-bold text-dark fs-7">Active Operational Status</div>
                            <div class="text-muted fs-8">When disabled, this billing event is paused system-wide.</div>
                        </div>
                        <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid m-0">
                            <input class="form-check-input" type="checkbox" name="status" id="drawer_status" value="1" checked>
                        </div>
                    </div>
                </form>
            </div>

            <!-- 3. Sticky Drawer Footer -->
            <div class="cf-drawer-footer">
                <button type="button" class="btn btn-sm btn-light fw-bold px-5" id="btnDrawerCancel" data-kt-drawer-dismiss="true">Cancel</button>
                <button type="button" class="btn btn-sm btn-primary fw-bold px-6" id="btnSaveDrawerFeature">
                    <span class="indicator-label"><i class="fa-solid fa-floppy-disk me-1"></i> Save Feature</span>
                    <span class="indicator-progress d-none">Saving... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
            </div>

        </div>
    </div>
@endsection

@push('footer-script')
    <script>
        $(document).ready(function() {
            // Setup CSRF headers for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Metronic Drawer Instance helper & Backdrop Lifecycle
            var coinDrawerEl = document.getElementById('kt_coin_drawer');

            function getDrawer() {
                if (coinDrawerEl && typeof KTDrawer !== 'undefined') {
                    var instance = KTDrawer.getInstance(coinDrawerEl);
                    if (!instance) {
                        try {
                            instance = new KTDrawer(coinDrawerEl);
                        } catch (e) {}
                    }
                    return instance;
                }
                return null;
            }

            function cleanupDrawerState() {
                $('#kt_coin_drawer').removeClass('drawer-on');
                $('.drawer-overlay, .cf-drawer-overlay').remove();
                $('body').removeAttr('data-kt-drawer');
                $('body').removeAttr('data-kt-drawer-coinFeatureDrawer');
            }

            var coinDrawer = getDrawer();
            if (coinDrawer) {
                coinDrawer.on('kt.drawer.hide', function() {
                    cleanupDrawerState();
                });
                coinDrawer.on('kt.drawer.after.hidden', function() {
                    cleanupDrawerState();
                });
            }

            // Current state variables for Client-First Execution (User RAM First)
            var currentFilter = 'all';
            var currentSearchQuery = '';
            var searchDebounceTimer = null;

            // =====================================================================
            // 1. Dynamic Client-Side Filtering & Search Engine (0% Server Strain)
            // =====================================================================
            function applyFilters() {
                var visibleCount = 0;
                var normalizedQuery = currentSearchQuery.trim().toLowerCase();

                $('.js-coin-card').each(function() {
                    var $card = $(this);
                    var cardType = $card.attr('data-type');
                    var cardDeduction = $card.attr('data-deduction');
                    var cardStatus = $card.attr('data-status');
                    var cardTitle = $card.attr('data-title') || '';
                    var cardDesc = $card.attr('data-desc') || '';
                    var cardId = ($card.attr('data-id') || '').toString();

                    // Check Tab Category Match
                    var matchesTab = false;
                    if (currentFilter === 'all') {
                        matchesTab = true;
                    } else if (currentFilter === 'operational' && cardType === 'operational') {
                        matchesTab = true;
                    } else if (currentFilter === 'sales' && cardType === 'sales') {
                        matchesTab = true;
                    } else if (currentFilter === 'daily' && cardDeduction === 'daily') {
                        matchesTab = true;
                    } else if (currentFilter === 'onetime' && cardDeduction === 'onetime') {
                        matchesTab = true;
                    } else if (currentFilter === 'active' && cardStatus == '1') {
                        matchesTab = true;
                    } else if (currentFilter === 'inactive' && cardStatus == '0') {
                        matchesTab = true;
                    }

                    // Check Search Query Match
                    var matchesSearch = true;
                    if (normalizedQuery.length > 0) {
                        matchesSearch = (
                            cardTitle.indexOf(normalizedQuery) !== -1 ||
                            cardDesc.indexOf(normalizedQuery) !== -1 ||
                            cardType.indexOf(normalizedQuery) !== -1 ||
                            cardDeduction.indexOf(normalizedQuery) !== -1 ||
                            cardId.indexOf(normalizedQuery) !== -1
                        );
                    }

                    if (matchesTab && matchesSearch) {
                        $card.show();
                        visibleCount++;
                    } else {
                        $card.hide();
                    }
                });

                // Toggle Empty State Card
                if (visibleCount === 0) {
                    $('#cfEmptyState').show();
                    $('#paginationWrapper').hide();
                } else {
                    $('#cfEmptyState').hide();
                    $('#paginationWrapper').show();
                }
            }

            // =====================================================================
            // 2. Debounced Live Search Handler (300ms)
            // =====================================================================
            $('#inputCoinSearch').on('keyup', function() {
                clearTimeout(searchDebounceTimer);
                var val = $(this).val();
                currentSearchQuery = val;

                if (val.length > 0) {
                    $('#btnCoinSearchClear').show();
                } else {
                    $('#btnCoinSearchClear').hide();
                }

                searchDebounceTimer = setTimeout(function() {
                    applyFilters();
                }, 300);
            });

            $('#btnCoinSearchClear').on('click', function() {
                $('#inputCoinSearch').val('');
                currentSearchQuery = '';
                $(this).hide();
                applyFilters();
            });

            // =====================================================================
            // 3. Sub-Tabs Filter Click Handler (Single Active Highlight)
            // =====================================================================
            $('.js-coin-filter-btn').on('click', function() {
                $('.js-coin-filter-btn').removeClass('active');
                $(this).addClass('active');

                currentFilter = $(this).attr('data-filter') || 'all';
                applyFilters();
            });

            $('#btnResetFilters').on('click', function() {
                $('#inputCoinSearch').val('');
                currentSearchQuery = '';
                $('#btnCoinSearchClear').hide();

                $('.js-coin-filter-btn').removeClass('active');
                $('.js-coin-filter-btn[data-filter="all"]').addClass('active');
                currentFilter = 'all';

                applyFilters();
            });

            // =====================================================================
            // 4. Live Operational Status Switch Toggle (AJAX)
            // =====================================================================
            $(document).on('change', '.js-status-toggle', function() {
                var $switch = $(this);
                var id = $switch.val();
                var isChecked = $switch.is(':checked');
                var statusVal = isChecked ? 1 : 0;
                var $card = $('#cf-card-' + id);
                var $badge = $('#statusBadge' + id);

                $.ajax({
                    type: 'POST',
                    url: "{{ route('superadmin.company.coinEventStatus') }}",
                    data: {
                        id: id
                    },
                    beforeSend: function() {
                        $switch.prop('disabled', true);
                    },
                    success: function(response) {
                        $switch.prop('disabled', false);
                        if (response.status === 'success') {
                            // Update badge text and classes
                            if (isChecked) {
                                $badge.removeClass('inactive').addClass('active').text('Active');
                                $card.attr('data-status', '1');
                            } else {
                                $badge.removeClass('active').addClass('inactive').text('Inactive');
                                $card.attr('data-status', '0');
                            }

                            // Update KPI badge numbers dynamically
                            recalculateKpiBadges();

                            // Re-apply active filters if filtering by status
                            if (currentFilter === 'active' || currentFilter === 'inactive') {
                                applyFilters();
                            }

                            // Notification toast
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: response.message || 'Status updated successfully',
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true
                            });
                        } else {
                            $switch.prop('checked', !isChecked);
                            Swal.fire('Error', response.message || 'Failed to update status', 'error');
                        }
                    },
                    error: function() {
                        $switch.prop('disabled', false);
                        $switch.prop('checked', !isChecked);
                        Swal.fire('Error', 'Server communication error occurred.', 'error');
                    }
                });
            });

            function recalculateKpiBadges() {
                var total = $('.js-coin-card').length;
                var active = $('.js-coin-card[data-status="1"]').length;
                var inactive = $('.js-coin-card[data-status="0"]').length;
                var operational = $('.js-coin-card[data-type="operational"]').length;
                var sales = $('.js-coin-card[data-type="sales"]').length;
                var daily = $('.js-coin-card[data-deduction="daily"]').length;
                var onetime = $('.js-coin-card[data-deduction="onetime"]').length;

                $('#kpiTotalSpan, #tabCountAll').text(total);
                $('#kpiActiveSpan, #tabCountActive').text(active);
                $('#kpiOperationalSpan, #tabCountOperational').text(operational);
                $('#kpiSalesSpan, #tabCountSales').text(sales);
                $('#tabCountDaily').text(daily);
                $('#tabCountOnetime').text(onetime);
                $('#tabCountInactive').text(inactive);
            }

            // =====================================================================
            // 5. Metronic Slide-Over Drawer: Add & Quick Edit Handler
            // =====================================================================
            function openDrawer() {
                var d = getDrawer();
                if (d && typeof d.show === 'function') {
                    d.show();
                } else {
                    // Fallback if KTDrawer is not instantiated
                    $('#kt_coin_drawer').addClass('drawer-on');
                    if ($('.cf-drawer-overlay').length === 0) {
                        $('body').append('<div class="cf-drawer-overlay"></div>');
                    }
                }
            }

            function closeDrawer() {
                var d = getDrawer();
                if (d && typeof d.hide === 'function') {
                    d.hide();
                }
                cleanupDrawerState();
            }

            // Click outside on backdrop overlay (Metronic native or fallback) cleanly dismisses
            $(document).on('click', '.drawer-overlay, .cf-drawer-overlay', function() {
                closeDrawer();
            });

            // Escape key closes drawer and removes backdrop
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && ($('#kt_coin_drawer').hasClass('drawer-on') || $('.drawer-overlay, .cf-drawer-overlay').length > 0)) {
                    closeDrawer();
                }
            });

            // Header close icon and sticky footer cancel button
            $('#btnDrawerCloseIcon, #btnDrawerCancel').on('click', function(e) {
                e.preventDefault();
                closeDrawer();
            });

            // Open for Create
            $('#btnOpenAddDrawer').on('click', function() {
                $('#drawerTitle').text('Create Coin Feature');
                $('#drawerEventCodeBadge').addClass('d-none').text('');
                $('#drawer_token_id').val('');
                $('#drawer_title').val('');
                $('#drawer_event_type').val('operational');
                $('#drawer_deduction_type').val('onetime');
                $('#drawer_default_rate').val('1.00');
                $('#drawer_minimum_required').val('0');
                $('#drawer_description').val('');
                $('#drawer_status').prop('checked', true);

                openDrawer();
            });

            // Open for Edit (Quick Edit Trigger)
            $(document).on('click', '.js-quick-edit-trigger', function() {
                var $btn = $(this);
                var id = $btn.attr('data-id');
                var title = $btn.attr('data-title') || '';
                var type = $btn.attr('data-type') || 'operational';
                var deduction = $btn.attr('data-deduction') || 'onetime';
                var rate = $btn.attr('data-rate') || '0';
                var min = $btn.attr('data-min') || '0';
                var desc = $btn.attr('data-desc') || '';
                var status = $btn.attr('data-status') == '1';

                $('#drawerTitle').text('Edit Feature: ' + title);
                $('#drawerEventCodeBadge').removeClass('d-none').text('#CE-' + ('000' + id).slice(-3));
                $('#drawer_token_id').val(id);
                $('#drawer_title').val(title);
                $('#drawer_event_type').val(type);
                $('#drawer_deduction_type').val(deduction);
                $('#drawer_default_rate').val(rate);
                $('#drawer_minimum_required').val(min);
                $('#drawer_description').val(desc);
                $('#drawer_status').prop('checked', status);

                openDrawer();
            });

            // Save Feature Form Submission
            $('#btnSaveDrawerFeature').on('click', function(e) {
                e.preventDefault();

                var $btn = $(this);
                var $form = $('#coinFeatureDrawerForm');

                // Basic client validation
                var title = $.trim($('#drawer_title').val());
                if (!title) {
                    Swal.fire('Validation Required', 'Please enter a Feature / Event Name.', 'warning');
                    $('#drawer_title').focus();
                    return;
                }

                var formData = new FormData($form[0]);

                $btn.find('.indicator-label').addClass('d-none');
                $btn.find('.indicator-progress').removeClass('d-none');
                $btn.prop('disabled', true);

                $.ajax({
                    url: "{{ route('superadmin.company.coinEventsStore') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $btn.find('.indicator-label').removeClass('d-none');
                        $btn.find('.indicator-progress').addClass('d-none');
                        $btn.prop('disabled', false);

                        if (response.status === 'success') {
                            closeDrawer();

                            Swal.fire({
                                text: response.message || 'Coin feature saved successfully!',
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            }).then(function() {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.message || 'Could not save coin feature.', 'error');
                        }
                    },
                    error: function(xhr) {
                        $btn.find('.indicator-label').removeClass('d-none');
                        $btn.find('.indicator-progress').addClass('d-none');
                        $btn.prop('disabled', false);

                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                            Swal.fire({
                                html: '<div class="text-start"><strong class="text-danger">Validation Errors:</strong><br>' + errors + '</div>',
                                icon: 'error',
                                buttonsStyling: false,
                                confirmButtonText: 'Ok, got it!',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                }
                            });
                        } else {
                            Swal.fire('Error', 'An error occurred while saving. Please try again.', 'error');
                        }
                    }
                });
            });

        });
    </script>
@endpush
