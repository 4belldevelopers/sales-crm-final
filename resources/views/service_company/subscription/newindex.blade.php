@extends('layouts.service_company_layout')

@push('head-script')
    <link rel="stylesheet" href="{{ asset('assets/node_modules/switchery/dist/switchery.min.css') }}<?php echo TM_VERSION; ?>">
    {{--
      JERO ATS — Service Company Subscription & Wallet Center (Rule 20 Card + Sub-Card Design System)
      Intent: Complete enterprise redesign using 3-tier Modern Card + Sub-Card architecture (Tier 1 Toolbar + Live Search,
              Tier 2 Sub-Tabs Card, Tier 3 Primary + Discrete Sub-Cards) with strict CSS isolation (.sub-wrapper)
              and 15.6" screen responsiveness (1366x768 & 1920x1080, zero horizontal overflow).
      Tenancy & Business Logic: Preserves 100% of existing wallet switching, checkout Add-to-Cart (.upgradeselect),
              Contact Sales (.customsubscription / .customsubscription_sal / .btn-contact-sales), and AJAX transaction ledger.
    --}}
    <style>
        body {
            overflow-x: hidden;
        }

        /* ── ISOLATED ROOT SCOPE: .sub-wrapper ── */
        .sub-wrapper {
            width: 100%;
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #0f172a;
            box-sizing: border-box;
            padding: 4px 0 24px;
        }
        .sub-wrapper *, .sub-wrapper *::before, .sub-wrapper *::after {
            box-sizing: border-box;
        }
        .sub-wrapper i, .sub-wrapper [class*="fa-"] {
            font-family: "Font Awesome 6 Free", "Font Awesome 5 Free", "FontAwesome" !important;
            font-style: normal !important;
            line-height: 1 !important;
        }
        .sub-wrapper .fas, .sub-wrapper .fa-solid { font-weight: 900 !important; }
        .sub-wrapper .far, .sub-wrapper .fa-regular { font-weight: 400 !important; }
        .sub-wrapper .sub-num { font-variant-numeric: tabular-nums; }

        /* ── SEMANTIC ICON & BADGE TOKENS ── */
        .sub-icon-blue { background: #eff6ff !important; color: #009ef7 !important; border: 1px solid #dbeafe !important; }
        .sub-icon-blue i { color: #009ef7 !important; }
        .sub-icon-purple { background: #f5f3ff !important; color: #7c3aed !important; border: 1px solid #ddd6fe !important; }
        .sub-icon-purple i { color: #7c3aed !important; }
        .sub-icon-emerald { background: #ecfdf5 !important; color: #059669 !important; border: 1px solid #a7f3d0 !important; }
        .sub-icon-emerald i { color: #059669 !important; }
        .sub-icon-amber { background: #fffbeb !important; color: #d97706 !important; border: 1px solid #fde68a !important; }
        .sub-icon-amber i { color: #d97706 !important; }

        .sub-badge-blue { background: #eff6ff; color: #0284c7; border: 1px solid #bae6fd; }
        .sub-badge-blue i { color: #0284c7 !important; }
        .sub-badge-purple { background: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe; }
        .sub-badge-purple i { color: #6d28d9 !important; }
        .sub-badge-emerald { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
        .sub-badge-emerald i { color: #059669 !important; }

        /* ── TIER 1: PURE WHITE TOOLBAR HEADER CARD ── */
        .sub-toolbar-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
            flex-wrap: wrap;
        }
        .sub-toolbar-left {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }
        .sub-toolbar-icon-box {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            color: #009ef7;
            flex-shrink: 0;
        }
        .sub-toolbar-icon-box i { color: #009ef7 !important; }
        .sub-toolbar-title-row {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .sub-toolbar-title {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            line-height: 1.25;
        }
        .sub-count-pill {
            font-size: 11px;
            font-weight: 600;
            padding: 2px 10px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .sub-toolbar-sub {
            font-size: 12px;
            color: #64748b;
            margin: 3px 0 0 0;
            line-height: 1.35;
        }
        .sub-toolbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .sub-search-box {
            position: relative;
            width: 265px;
            max-width: 100%;
        }
        .sub-search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12.5px;
            color: #94a3b8 !important;
            pointer-events: none;
        }
        .sub-search-input {
            width: 100%;
            height: 36px;
            padding: 0 32px 0 34px;
            font-size: 12px;
            font-family: inherit;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #f8fafc;
            color: #0f172a;
            outline: none;
            transition: all 0.2s ease;
        }
        .sub-search-input:focus {
            background: #ffffff;
            border-color: #009ef7;
            box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12);
        }
        .sub-search-clear {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            color: #94a3b8;
            background: none;
            border: none;
            cursor: pointer;
            display: none;
            padding: 3px;
        }
        .sub-search-clear:hover i { color: #ef4444 !important; }

        .sub-cart-pill {
            height: 36px;
            padding: 0 12px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #0f172a !important;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            text-decoration: none !important;
            transition: all 0.15s ease;
        }
        .sub-cart-pill:hover {
            background: #eff6ff;
            border-color: #bae6fd;
            color: #0284c7 !important;
        }
        .sub-cart-pill i { color: #475569 !important; font-size: 13px; }
        .sub-cart-pill:hover i { color: #0284c7 !important; }
        .sub-cart-badge {
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            border-radius: 10px;
            font-size: 10.5px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #e2e8f0;
            color: #334155;
        }
        .sub-cart-badge.has-items {
            background: #f59e0b;
            color: #ffffff;
        }

        .sub-btn-primary {
            height: 36px;
            padding: 0 16px;
            background: #009ef7;
            color: #ffffff !important;
            font-size: 12.5px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            text-decoration: none !important;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(0, 158, 247, 0.2);
            white-space: nowrap;
        }
        .sub-btn-primary:hover { background: #0086d4; box-shadow: 0 4px 8px rgba(0, 158, 247, 0.28); }
        .sub-btn-primary i { color: #ffffff !important; font-size: 12.5px; }

        /* ── TIER 2: PURE WHITE SUB-TABS CARD ── */
        .sub-subtabs-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 7px 12px;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
            flex-wrap: wrap;
        }
        .sub-subtabs-nav {
            display: flex;
            align-items: center;
            gap: 6px;
            overflow-x: auto;
            scrollbar-width: none;
        }
        .sub-subtabs-nav::-webkit-scrollbar { display: none; }
        .sub-subtab-pill {
            background: transparent;
            border: 1px solid transparent;
            border-radius: 8px;
            padding: 7px 14px;
            font-size: 12px;
            font-weight: 600;
            color: #64748b !important;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            text-decoration: none !important;
            transition: all 0.15s ease;
            white-space: nowrap;
        }
        .sub-subtab-pill i { color: #64748b !important; font-size: 12.5px; }
        .sub-subtab-pill:hover {
            background: #f8fafc;
            border-color: #e2e8f0;
            color: #0f172a !important;
        }
        .sub-subtab-pill:hover i { color: #0f172a !important; }
        .sub-subtab-pill.wt-active {
            background: #f1faff !important;
            border-color: rgba(0, 158, 247, 0.35) !important;
            color: #009ef7 !important;
            font-weight: 600 !important;
            box-shadow: 0 1px 2px rgba(0, 158, 247, 0.08);
        }
        .sub-subtab-pill.wt-active i { color: #009ef7 !important; }
        .sub-subtab-counter {
            font-size: 10.5px;
            padding: 1px 7px;
            border-radius: 10px;
            background: #e2e8f0;
            color: #475569;
            font-weight: 700;
            min-width: 20px;
            text-align: center;
        }
        .sub-subtab-pill.wt-active .sub-subtab-counter {
            background: #009ef7;
            color: #ffffff;
        }
        .sub-security-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11.5px;
            font-weight: 500;
            color: #475569;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 7px;
            padding: 5px 10px;
            white-space: nowrap;
        }
        .sub-security-chip i { color: #10b981 !important; font-size: 12px; }

        /* ── TIER 3: PRIMARY CARDS STREAM ── */
        .sub-stream {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .sub-primary-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
            overflow: hidden;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .sub-primary-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
        }
        .sub-card-header {
            padding: 14px 18px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            background: #ffffff;
            flex-wrap: wrap;
        }
        .sub-card-header-left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }
        .sub-cat-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
        }
        .sub-card-title {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            line-height: 1.25;
        }
        .sub-card-desc {
            font-size: 11.5px;
            color: #64748b;
            margin: 2px 0 0 0;
            line-height: 1.35;
        }
        .sub-card-header-right {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .sub-status-badge {
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }
        .sub-card-body {
            padding: 16px 18px;
        }

        /* ── PRIMARY CARD 1: DUAL WALLET SUB-CARDS ── */
        .sub-wallets-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }
        .sub-wallet-subcard {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-left: 3.5px solid #cbd5e1;
            border-radius: 9px;
            padding: 16px;
            cursor: pointer;
            transition: all 0.18s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            outline: none;
        }
        .sub-wallet-subcard:hover {
            background: #ffffff;
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
        }
        .sub-wallet-subcard.opra-tab-plan {
            background: linear-gradient(180deg, #f5f3ff 0%, #ffffff 60%) !important;
            border-color: #c4b5fd !important;
            border-left: 3.5px solid #6d28d9 !important;
            box-shadow: 0 3px 12px rgba(109, 40, 217, 0.08);
            color: #0f172a !important;
            font-weight: 400 !important;
        }
        .sub-wallet-subcard.salse-tab-plan {
            background: linear-gradient(180deg, #ecfdf5 0%, #ffffff 60%) !important;
            border-color: #6ee7b7 !important;
            border-left: 3.5px solid #059669 !important;
            box-shadow: 0 3px 12px rgba(5, 150, 105, 0.08);
            color: #0f172a !important;
            font-weight: 400 !important;
        }
        .sub-wallet-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
        }
        .sub-wallet-identity {
            display: flex;
            align-items: flex-start;
            gap: 11px;
            min-width: 0;
        }
        .wallet-card-icon {
            width: 38px;
            height: 38px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }
        .sub-wallet-title-group { min-width: 0; }
        .sub-wallet-title-row {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .sub-wallet-name {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
        }
        .sub-status-pill {
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            letter-spacing: 0.3px;
        }
        .sub-status-pill i { font-size: 6px !important; }
        .sub-pill-active { background: #dcfce7; color: #15803d; }
        .sub-pill-active i { color: #16a34a !important; }
        .sub-pill-inactive { background: #fee2e2; color: #b91c1c; }
        .sub-pill-inactive i { color: #ef4444 !important; }

        .sub-wallet-plan-meta {
            font-size: 11.5px;
            color: #64748b;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 5px;
            flex-wrap: wrap;
        }
        .sub-wallet-plan-meta i { font-size: 10.5px; color: #7c3aed !important; }
        .wallet-card-sales .sub-wallet-plan-meta i { color: #059669 !important; }
        .sub-wallet-expiry { color: #64748b; font-size: 11px; }

        .wallet-card-check {
            font-size: 19px;
            color: #cbd5e1;
            flex-shrink: 0;
            transition: color 0.15s ease;
        }
        .sub-wallet-subcard.opra-tab-plan .wallet-card-check { color: #6d28d9 !important; }
        .sub-wallet-subcard.opra-tab-plan .wallet-card-check i { color: #6d28d9 !important; }
        .sub-wallet-subcard.salse-tab-plan .wallet-card-check { color: #059669 !important; }
        .sub-wallet-subcard.salse-tab-plan .wallet-card-check i { color: #059669 !important; }

        .sub-wallet-balance-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 14px;
            gap: 12px;
        }
        .wallet-card-balance {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.15;
        }
        .wallet-card-balance-label {
            font-size: 11px;
            font-weight: 500;
            color: #64748b;
            margin-top: 2px;
        }
        .wallet-ring {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .wallet-ring::before {
            content: '';
            position: absolute;
            inset: 6px;
            background: #ffffff;
            border-radius: 50%;
        }
        .wallet-ring-label {
            position: relative;
            z-index: 1;
            text-align: center;
            line-height: 1.1;
        }
        .wallet-ring-pct {
            font-size: 11.5px;
            font-weight: 800;
            color: #0f172a;
            display: block;
        }
        .wallet-ring-sub {
            font-size: 8.5px;
            color: #64748b;
            display: block;
            font-weight: 600;
        }

        .sub-wallet-metrics-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px dashed #e2e8f0;
        }
        .sub-wallet-metric-box {
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid #e2e8f0;
            border-radius: 7px;
            padding: 8px 10px;
            text-align: center;
        }
        .sub-wallet-metric-val {
            font-size: 13.5px;
            font-weight: 700;
            color: #0f172a;
        }
        .wallet-card-stat-label {
            font-size: 10.5px;
            color: #64748b;
            margin-top: 1px;
        }

        .sub-wallet-info-strip {
            margin-top: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 9px 14px;
            font-size: 11.5px;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .sub-wallet-info-strip i { color: #6d28d9 !important; font-size: 13px; }
        .wallet-center-header.wallet-mode-sales .sub-wallet-info-strip i { color: #059669 !important; }

        /* ── PRIMARY CARD 2: CURRENT PLAN & TELEMETRY SUB-CARDS ── */
        .sub-plan-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
        }
        .sub-plan-subcard {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 13px 14px;
            display: flex;
            align-items: center;
            gap: 11px;
            min-width: 0;
        }
        .sub-plan-subcard-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }
        .sub-plan-subcard-label {
            font-size: 10.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #64748b;
        }
        .sub-plan-subcard-val {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sub-plan-subcard-val.is-purple { color: #6d28d9; }
        .sub-plan-subcard-val.is-emerald { color: #059669; }

        .sub-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-top: 12px;
        }
        .sub-kpi-subcard {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            transition: border-color 0.15s ease;
        }
        .sub-kpi-subcard:hover { border-color: #cbd5e1; }
        .sub-kpi-val {
            font-size: 17px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
        }
        .sub-kpi-label {
            font-size: 11px;
            font-weight: 500;
            color: #64748b;
            margin-top: 2px;
        }
        .sub-kpi-mini-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
        }

        .sub-no-plan-box {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 16px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }
        .sub-no-plan-left {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 12.5px;
            color: #475569;
            font-weight: 500;
        }
        .sub-no-plan-left i { color: #f59e0b !important; font-size: 16px; }
        .sub-link-cta {
            font-size: 12px;
            font-weight: 600;
            color: #009ef7 !important;
            text-decoration: none !important;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .sub-link-cta:hover { text-decoration: underline !important; }

        /* ── PRIMARY CARD 3: COIN PACKAGES SUB-CARDS GRID ── */
        .sub-pkg-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            align-items: stretch;
        }
        .sub-pkg-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-top: 3px solid #cbd5e1;
            border-radius: 10px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.15s ease;
            min-width: 0;
        }
        .sub-pkg-card:hover {
            border-color: #93c5fd;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.06);
        }
        .sub-pkg-card.is-popular-op {
            border-color: #c4b5fd;
            border-top: 3.5px solid #6d28d9;
            box-shadow: 0 4px 14px rgba(109, 40, 217, 0.08);
        }
        .sub-pkg-card.is-popular-sal {
            border-color: #6ee7b7;
            border-top: 3.5px solid #059669;
            box-shadow: 0 4px 14px rgba(5, 150, 105, 0.08);
        }
        .sub-pkg-card.is-current-op {
            border-color: #6d28d9 !important;
            border-top: 3.5px solid #6d28d9 !important;
            box-shadow: 0 4px 16px rgba(109, 40, 217, 0.14) !important;
        }
        .sub-pkg-card.is-current-sal {
            border-color: #059669 !important;
            border-top: 3.5px solid #059669 !important;
            box-shadow: 0 4px 16px rgba(5, 150, 105, 0.14) !important;
        }
        .sub-pkg-badges-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            min-height: 24px;
            margin-bottom: 10px;
        }
        .sub-pkg-tag {
            font-size: 10px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 10px;
            letter-spacing: 0.3px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
        }
        .sub-pkg-tag-current-op { background: #6d28d9; color: #ffffff; }
        .sub-pkg-tag-current-sal { background: #059669; color: #ffffff; }
        .sub-pkg-tag-popular-op { background: #eff6ff; color: #0284c7; border: 1px solid #bae6fd; }
        .sub-pkg-tag-popular-sal { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
        .sub-pkg-tag-discount { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; margin-left: auto; }

        .sub-pkg-head {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }
        .sub-pkg-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
        }
        .sub-pkg-title {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.25;
        }
        .sub-pkg-subtitle {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }

        .sub-pkg-price-subcard {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 12px;
        }
        .sub-pkg-orig-price {
            font-size: 11.5px;
            color: #94a3b8;
            text-decoration: line-through;
            min-height: 17px;
        }
        .sub-pkg-price-line {
            display: flex;
            align-items: baseline;
            gap: 3px;
            margin-top: 2px;
        }
        .sub-pkg-curr {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
        }
        .sub-pkg-val {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
        }
        .sub-pkg-period {
            font-size: 11.5px;
            color: #64748b;
            font-weight: 500;
        }
        .sub-pkg-custom-price {
            font-size: 18px;
            font-weight: 800;
            color: #059669;
            margin: 6px 0 4px;
        }
        .sub-pkg-coins-pill {
            margin-top: 8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11.5px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
        }

        .sub-pkg-actions {
            display: flex;
            flex-direction: column;
            gap: 7px;
            margin-bottom: 12px;
        }
        .sub-pkg-btn {
            width: 100%;
            height: 36px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            text-decoration: none !important;
            cursor: pointer;
            transition: all 0.15s ease;
            border: 1px solid transparent;
        }
        .sub-pkg-btn-solid-op { background: #009ef7; color: #ffffff !important; }
        .sub-pkg-btn-solid-op:hover { background: #0086d4; }
        .sub-pkg-btn-solid-op i { color: #ffffff !important; }
        .sub-pkg-btn-outline-op { background: #ffffff; border-color: #cbd5e1; color: #0f172a !important; }
        .sub-pkg-btn-outline-op:hover { background: #f8fafc; border-color: #009ef7; color: #009ef7 !important; }
        .sub-pkg-btn-solid-sal { background: #059669; color: #ffffff !important; }
        .sub-pkg-btn-solid-sal:hover { background: #047857; }
        .sub-pkg-btn-solid-sal i { color: #ffffff !important; }
        .sub-pkg-btn-outline-sal { background: #ffffff; border-color: #cbd5e1; color: #0f172a !important; }
        .sub-pkg-btn-outline-sal:hover { background: #ecfdf5; border-color: #059669; color: #059669 !important; }
        .sub-pkg-btn-current-op { background: #f5f3ff; border-color: #ddd6fe; color: #6d28d9 !important; cursor: default; }
        .sub-pkg-btn-current-op i { color: #6d28d9 !important; }
        .sub-pkg-btn-current-sal { background: #ecfdf5; border-color: #a7f3d0; color: #059669 !important; cursor: default; }
        .sub-pkg-btn-current-sal i { color: #059669 !important; }
        .sub-pkg-btn-enterprise { background: #0f172a; color: #ffffff !important; }
        .sub-pkg-btn-enterprise:hover { background: #1e293b; }
        .sub-pkg-btn-enterprise i { color: #ffffff !important; }

        .sub-pkg-features-box {
            flex: 1;
            border-top: 1px solid #f1f5f9;
            padding-top: 10px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .sub-pkg-feat-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            font-size: 11.5px;
            color: #334155;
            padding: 3px 0;
        }
        .sub-pkg-feat-row.is-muted { color: #94a3b8; }
        .sub-pkg-feat-val { font-weight: 600; color: #0f172a; }
        .sub-pkg-feat-check { color: #10b981 !important; font-size: 11px; flex-shrink: 0; }
        .sub-pkg-feat-cross { color: #cbd5e1 !important; font-size: 11px; flex-shrink: 0; }

        /* ── PRIMARY CARD 4: GUARANTEES, VALUE & CUSTOM ENTERPRISE ── */
        .sub-sla-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 14px;
        }
        .sub-sla-subcard {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sub-sla-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }
        .sub-sla-title { font-size: 12.5px; font-weight: 700; color: #0f172a; line-height: 1.25; }
        .sub-sla-desc { font-size: 11px; color: #64748b; margin-top: 1px; }

        .sub-value-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }
        .sub-value-subcard {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 9px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .sub-value-title { font-size: 13.5px; font-weight: 700; color: #0f172a; margin: 0 0 10px; }
        .sub-why-mini-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }
        .sub-why-mini-item {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 12px;
            display: flex;
            align-items: flex-start;
            gap: 9px;
        }
        .sub-why-mini-item i { font-size: 14px; margin-top: 2px; flex-shrink: 0; }
        .sub-custom-perks {
            list-style: none;
            padding: 0;
            margin: 10px 0 14px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .sub-custom-perks li {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 5px 10px;
            font-size: 11.5px;
            font-weight: 600;
            color: #334155;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .sub-custom-perks li i { color: #10b981 !important; font-size: 11px; }

        .sub-earn-strip {
            margin-top: 14px;
            background: linear-gradient(90deg, #0f172a, #1e293b);
            border-radius: 9px;
            padding: 14px 18px;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }
        .sub-earn-strip.is-sales {
            background: linear-gradient(90deg, #065f46, #047857);
        }
        .sub-earn-title { font-size: 13.5px; font-weight: 700; color: #ffffff; }
        .sub-earn-sub { font-size: 11.5px; color: rgba(255, 255, 255, 0.8); margin-top: 2px; }
        .sub-earn-cta {
            background: #ffffff;
            color: #0f172a !important;
            border-radius: 7px;
            padding: 7px 14px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none !important;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }
        .sub-earn-cta:hover { background: #f1f5f9; }

        /* ── TRANSACTIONS CARD + SUB-CARD STREAM ── */
        .sub-tx-stream {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .sub-tx-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-left: 3.5px solid #cbd5e1;
            border-radius: 9px;
            padding: 12px 14px;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .sub-tx-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 3px 10px rgba(15, 23, 42, 0.05);
        }
        .sub-tx-accent-success { border-left-color: #10b981; }
        .sub-tx-accent-danger { border-left-color: #ef4444; }
        .sub-tx-accent-warning { border-left-color: #f59e0b; }
        .sub-tx-accent-neutral { border-left-color: #94a3b8; }

        .sub-tx-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(0, 1.55fr) minmax(0, 1fr) minmax(0, 1.15fr) minmax(0, 1.25fr);
            gap: 10px;
            align-items: center;
        }
        .sub-tx-subcard {
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 7px;
            padding: 8px 11px;
            min-width: 0;
        }
        .sub-tx-micro-label {
            display: block;
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: 0.45px;
            color: #94a3b8;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .sub-tx-order-id { font-size: 13px; font-weight: 700; color: #0f172a; }
        .sub-tx-pkg-name {
            font-size: 12.5px;
            font-weight: 700;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sub-tx-coins {
            font-size: 13px;
            font-weight: 700;
            color: #6d28d9;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .sub-tx-coins i { color: #f59e0b !important; font-size: 12px; }
        .sub-tx-amount { font-size: 13.5px; font-weight: 700; color: #0f172a; }
        .sub-tx-meta {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sub-tx-wallet-tag {
            font-size: 10.5px;
            font-weight: 600;
            padding: 1px 7px;
            border-radius: 5px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .sub-tag-purple { background: #f5f3ff; color: #6d28d9; }
        .sub-tag-purple i { color: #6d28d9 !important; font-size: 10px; }
        .sub-tag-emerald { background: #ecfdf5; color: #059669; }
        .sub-tag-emerald i { color: #059669 !important; font-size: 10px; }

        .sub-tx-status-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            flex-wrap: wrap;
        }
        .sub-tx-status-pill {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 6px;
        }
        .sub-tx-pill-success { background: #dcfce7; color: #15803d; }
        .sub-tx-pill-danger { background: #fee2e2; color: #b91c1c; }
        .sub-tx-pill-warning { background: #fef3c7; color: #b45309; }
        .sub-tx-pill-neutral { background: #f1f5f9; color: #475569; }

        .sub-tx-receipt-btn {
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 6px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #0f172a !important;
            text-decoration: none !important;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.15s ease;
        }
        .sub-tx-receipt-btn:hover {
            background: #eff6ff;
            border-color: #009ef7;
            color: #009ef7 !important;
        }
        .sub-tx-receipt-btn i { color: #ef4444 !important; font-size: 11px; }

        .sub-tx-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px solid #f1f5f9;
        }
        .sub-tx-showing { font-size: 12px; color: #64748b; }
        .sub-tx-footer .pagination .page-link {
            font-size: 12px;
            font-weight: 600;
            border-radius: 6px !important;
            margin: 0 2px;
            border: 1px solid #e2e8f0;
            color: #475569;
            padding: 5px 10px;
        }
        .sub-tx-footer .pagination .page-item.active .page-link {
            background: #009ef7;
            border-color: #009ef7;
            color: #ffffff;
        }
        .sub-tx-footer .pagination .page-link:hover {
            background: #eff6ff;
            color: #009ef7;
            border-color: #bae6fd;
        }

        .sub-empty-subcard {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 9px;
            padding: 36px 20px;
            text-align: center;
        }
        .sub-empty-icon {
            width: 46px;
            height: 46px;
            border-radius: 10px;
            background: #eff6ff;
            color: #009ef7;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            margin-bottom: 10px;
        }
        .sub-empty-icon i { color: #009ef7 !important; }
        .sub-empty-title { font-size: 14px; font-weight: 700; color: #0f172a; }
        .sub-empty-desc { font-size: 12px; color: #64748b; margin-top: 4px; }

        /* ── RESPONSIVE BREAKPOINTS (15.6" 1366x768 & 1920x1080 + MOBILE) ── */
        @media (max-width: 1366px) {
            .wallet-card-balance { font-size: 21px; }
            .sub-pkg-val { font-size: 22px; }
            .sub-kpi-val { font-size: 15.5px; }
        }
        @media (max-width: 1199px) {
            .sub-pkg-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .sub-plan-grid, .sub-kpi-grid, .sub-sla-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .sub-tx-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .sub-tx-subcard-action { grid-column: span 2; }
        }
        @media (max-width: 991px) {
            .sub-wallets-grid, .sub-value-grid { grid-template-columns: minmax(0, 1fr); }
        }
        @media (max-width: 767px) {
            .sub-toolbar-card { padding: 12px 14px; }
            .sub-toolbar-right { width: 100%; }
            .sub-search-box { width: 100%; }
            .sub-pkg-grid, .sub-plan-grid, .sub-kpi-grid, .sub-sla-grid, .sub-why-mini-grid, .sub-tx-grid {
                grid-template-columns: minmax(0, 1fr);
            }
            .sub-tx-subcard-action { grid-column: span 1; }
            .sub-security-chip { display: none; }
        }
    </style>
@endpush

@php
    use App\Helper\GeneralMethod;

    $currency = GeneralMethod::convertINRToUSD();
    $currIcon = $currency == 'INR' ? '₹' : '$';

    $opPackagesList = $operational_package ?? [];
    $salPackagesList = $sales_package ?? [];
    $opPkgCount = is_countable($opPackagesList) ? count($opPackagesList) : 0;
    $salPkgCount = is_countable($salPackagesList) ? count($salPackagesList) : 0;

    $opIcons = ['fa-star text-warning', 'fa-crown text-primary', 'fa-gem text-info'];
    $salIcons = ['fa-star text-warning', 'fa-crown text-success', 'fa-gem text-info'];

    $activeOpPackageId = !empty($current_coins_operational) ? ($current_coins_operational[0]['coin_package_id'] ?? null) : null;
    $customOpPkg = collect($opPackagesList)->firstWhere('custome_pck', 2) ?? (!empty($opPackagesList) ? collect($opPackagesList)->first() : null);
    $customOpPkgId = $customOpPkg ? encrypt($customOpPkg['id']) : encrypt(5);

    $activeSlPackageId = !empty($current_coins_sales) ? ($current_coins_sales[0]['coin_package_id'] ?? null) : null;
    $customSalPkg = collect($salPackagesList)->firstWhere('custome_pck', 2) ?? (!empty($salPackagesList) ? collect($salPackagesList)->first() : null);
    $customSalPkgId = $customSalPkg ? encrypt($customSalPkg['id']) : encrypt(10);

    $opCustomPckFlag = $current_coins_operational[0]['custome_pck'] ?? 0;
    $salCustomPckFlag = $current_coins_sales[0]['custome_pck'] ?? 0;
@endphp

@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack px-5">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-2 mb-lg-0">
                <div class="d-flex flex-column">
                    <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
                        <span class="text-gray-800 mb-0">Subscription Package</span>
                    </h5>
                </div>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid pt-2 pb-0" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid px-4">
                <div class="sub-wrapper subscription-card-system wallet-center-header">

                    {{-- ══════════════════════════════════════════════════════════════
                         TIER 1: PURE WHITE ENTERPRISE TOOLBAR HEADER CARD
                         ══════════════════════════════════════════════════════════════ --}}
                    <div class="sub-toolbar-card tier1-toolbar-card">
                        <div class="sub-toolbar-left">
                            <div class="sub-toolbar-icon-box">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div>
                                <div class="sub-toolbar-title-row">
                                    <h1 class="sub-toolbar-title">Subscription &amp; Wallet Center</h1>
                                    <span class="sub-count-pill sub-badge-purple" id="activeWalletBadge">
                                        <i class="fas fa-cogs"></i> Operational Wallet Active
                                    </span>
                                </div>
                                <p class="sub-toolbar-sub">Manage operational &amp; sales coin wallets, active subscriptions, package upgrades, and billing receipts.</p>
                            </div>
                        </div>

                        <div class="sub-toolbar-right">
                            <div class="sub-search-box">
                                <i class="fas fa-search sub-search-icon"></i>
                                <input type="text" id="subscription_search" class="sub-search-input"
                                    placeholder="Search packages, features, orders..." autocomplete="off">
                                <button type="button" id="subscription_search_clear_btn" class="sub-search-clear" title="Clear search">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <a href="{{ route('service_company.subscribe.checkoutSubscribe') }}" class="sub-cart-pill" title="View Subscription Cart">
                                <i class="fas fa-shopping-cart"></i>
                                <span>Cart</span>
                                <span class="sub-cart-badge sub-num {{ ($carts_count ?? 0) > 0 ? 'has-items' : '' }}">{{ $carts_count ?? 0 }}</span>
                            </a>

                            <button type="button" class="sub-btn-primary" id="btnHeaderBuyCoins" onclick="scrollToWalletPackages();return false;">
                                <i class="fas fa-coins"></i>
                                <span id="walletBuyCoinsLabel">Buy Coins</span>
                            </button>
                        </div>
                    </div>

                    {{-- ══════════════════════════════════════════════════════════════
                         TIER 2: PURE WHITE DEDICATED SUB-TABS NAVIGATION CARD
                         ══════════════════════════════════════════════════════════════ --}}
                    <div class="sub-subtabs-card tier2-subtabs-card" id="subscription_subtabs_bar">
                        <ul class="sub-subtabs-nav wallet-tab-nav m-0 p-0 list-unstyled">
                            <li>
                                <a href="javascript:;" class="sub-subtab-pill wallet-tab-link wt-active" data-tab="overview">
                                    <i class="fas fa-th-large"></i>
                                    <span>Overview</span>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:;" class="sub-subtab-pill wallet-tab-link" data-tab="packages">
                                    <i class="fas fa-cubes"></i>
                                    <span>Packages &amp; Pricing</span>
                                    <span class="sub-subtab-counter sub-num" id="pkgTabCountBadge">{{ $opPkgCount }}</span>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:;" class="sub-subtab-pill wallet-tab-link" data-tab="transactions">
                                    <i class="fas fa-exchange-alt"></i>
                                    <span>Transactions</span>
                                    <span class="sub-subtab-counter sub-num" id="txTabCountBadge" style="display:none;">0</span>
                                </a>
                            </li>
                        </ul>

                        <div class="sub-security-chip">
                            <i class="fas fa-shield-alt"></i>
                            <span>Isolated Multi-Tenant Wallets &bull; Instant Coin Activation</span>
                        </div>
                    </div>

                    {{-- ══════════════════════════════════════════════════════════════
                         TIER 3: PRIMARY CARD STREAM
                         ══════════════════════════════════════════════════════════════ --}}
                    <div class="sub-stream tier3-cards-stream" id="kt_pricing">
                        <div id="wallet-pane-overview" class="wallet-tab-pane p-0">
                            <div id="overview-only-section" class="sub-stream">

                                {{-- ────────────────────────────────────────────────────────────
                                     PRIMARY CARD 1: DUAL WALLET SELECTOR & BALANCES
                                     ──────────────────────────────────────────────────────────── --}}
                                <div class="sub-primary-card" id="card_wallet_selector">
                                    <div class="sub-card-header">
                                        <div class="sub-card-header-left">
                                            <div class="sub-cat-icon sub-icon-blue">
                                                <i class="fas fa-layer-group"></i>
                                            </div>
                                            <div>
                                                <h4 class="sub-card-title">Active Coin Wallets</h4>
                                                <p class="sub-card-desc">Select a wallet below to inspect active plans, coin consumption, and tailored upgrade packages.</p>
                                            </div>
                                        </div>
                                        <span class="sub-status-badge sub-badge-blue">
                                            <i class="fas fa-lock"></i> Dual Isolated Ledgers
                                        </span>
                                    </div>
                                    <div class="sub-card-body">
                                        @include('service_company.subscription.partials._wallet_cards')
                                        <div class="sub-wallet-info-strip wallet-info-banner">
                                            <i class="fas fa-shield-alt"></i>
                                            <span>Your wallets are secure and private. All transactions and usage are kept separate for each wallet.</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- ────────────────────────────────────────────────────────────
                                     PRIMARY CARD 2: CURRENT PLAN & COIN TELEMETRY (OPERATIONAL + SALES)
                                     ──────────────────────────────────────────────────────────── --}}
                                <div id="wallet-overview-summary">
                                    {{-- Operational Plan & Telemetry --}}
                                    <div class="sub-primary-card operational-packages" id="card_current_plan_op">
                                        <div class="sub-card-header">
                                            <div class="sub-card-header-left">
                                                <div class="sub-cat-icon sub-icon-purple">
                                                    <i class="fas fa-cogs"></i>
                                                </div>
                                                <div>
                                                    <h4 class="sub-card-title">Current Plan &amp; Coin Telemetry</h4>
                                                    <p class="sub-card-desc">Operational Wallet subscription status and real-time credit utilization.</p>
                                                </div>
                                            </div>
                                            @if(!empty($current_coins_operational))
                                                <span class="sub-status-badge sub-badge-emerald">
                                                    <i class="fas fa-check-circle"></i> Active Plan
                                                </span>
                                            @else
                                                <span class="sub-status-badge sub-badge-purple">
                                                    <i class="fas fa-info-circle"></i> No Active Plan
                                                </span>
                                            @endif
                                        </div>
                                        <div class="sub-card-body">
                                            @if(!empty($current_coins_operational))
                                                <div class="sub-plan-grid">
                                                    <div class="sub-plan-subcard">
                                                        <div class="sub-plan-subcard-icon sub-icon-purple"><i class="fas fa-layer-group"></i></div>
                                                        <div class="min-w-0">
                                                            <div class="sub-plan-subcard-label">Plan Name</div>
                                                            <div class="sub-plan-subcard-val">{{ $current_coins_operational[0]['title'] }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="sub-plan-subcard">
                                                        <div class="sub-plan-subcard-icon sub-icon-blue"><i class="fas fa-calendar-alt"></i></div>
                                                        <div class="min-w-0">
                                                            <div class="sub-plan-subcard-label">Expiration Date</div>
                                                            <div class="sub-plan-subcard-val sub-num">{{ !empty($current_coins_operational[0]['end_date']) ? date('d M Y', strtotime($current_coins_operational[0]['end_date'])) : '—' }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="sub-plan-subcard">
                                                        <div class="sub-plan-subcard-icon sub-icon-amber"><i class="fas fa-coins"></i></div>
                                                        <div class="min-w-0">
                                                            <div class="sub-plan-subcard-label">Credit Coins</div>
                                                            <div class="sub-plan-subcard-val sub-num">{{ number_format($current_coins_operational[0]['actual_coins'] ?? 0) }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="sub-plan-subcard">
                                                        <div class="sub-plan-subcard-icon sub-icon-purple"><i class="fas fa-wallet"></i></div>
                                                        <div class="min-w-0">
                                                            <div class="sub-plan-subcard-label">Available Coins</div>
                                                            <div class="sub-plan-subcard-val is-purple sub-num">{{ number_format($current_coins_operational[0]['coins'] ?? 0) }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="sub-no-plan-box">
                                                    <div class="sub-no-plan-left">
                                                        <i class="fas fa-exclamation-circle"></i>
                                                        <span>No active operational plan found for your organization.</span>
                                                    </div>
                                                    <a href="javascript:;" class="sub-link-cta" onclick="scrollToWalletPackages('operational');return false;">
                                                        Browse Packages <i class="fas fa-arrow-right"></i>
                                                    </a>
                                                </div>
                                            @endif

                                            <div class="sub-kpi-grid">
                                                <div class="sub-kpi-subcard">
                                                    <div>
                                                        <div class="sub-kpi-val sub-num">{{ number_format($op_total_purchased ?? 0) }}</div>
                                                        <div class="sub-kpi-label">Total Coins Purchased</div>
                                                    </div>
                                                    <span class="sub-kpi-mini-icon sub-icon-purple"><i class="fas fa-shopping-bag"></i></span>
                                                </div>
                                                <div class="sub-kpi-subcard">
                                                    <div>
                                                        <div class="sub-kpi-val sub-num">{{ number_format($remain_coins ?? 0) }}</div>
                                                        <div class="sub-kpi-label">Coins Available</div>
                                                    </div>
                                                    <span class="sub-kpi-mini-icon sub-icon-blue"><i class="fas fa-coins"></i></span>
                                                </div>
                                                <div class="sub-kpi-subcard">
                                                    <div>
                                                        <div class="sub-kpi-val sub-num">{{ number_format($op_coins_used ?? 0) }}</div>
                                                        <div class="sub-kpi-label">Coins Used</div>
                                                    </div>
                                                    <span class="sub-kpi-mini-icon sub-icon-amber"><i class="fas fa-chart-line"></i></span>
                                                </div>
                                                <div class="sub-kpi-subcard">
                                                    <div>
                                                        <div class="sub-kpi-val sub-num">{{ $currIcon }} {{ number_format($op_total_spend ?? 0) }}</div>
                                                        <div class="sub-kpi-label">Total Spend</div>
                                                    </div>
                                                    <span class="sub-kpi-mini-icon sub-icon-emerald"><i class="fas fa-receipt"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Sales Plan & Telemetry --}}
                                    <div class="sub-primary-card salse-packages" id="card_current_plan_sal" style="display:none;">
                                        <div class="sub-card-header">
                                            <div class="sub-card-header-left">
                                                <div class="sub-cat-icon sub-icon-emerald">
                                                    <i class="fas fa-briefcase"></i>
                                                </div>
                                                <div>
                                                    <h4 class="sub-card-title">Current Plan &amp; Coin Telemetry</h4>
                                                    <p class="sub-card-desc">Sales Wallet subscription status and marketplace credit utilization.</p>
                                                </div>
                                            </div>
                                            @if(!empty($current_coins_sales))
                                                <span class="sub-status-badge sub-badge-emerald">
                                                    <i class="fas fa-check-circle"></i> Active Plan
                                                </span>
                                            @else
                                                <span class="sub-status-badge sub-badge-emerald">
                                                    <i class="fas fa-info-circle"></i> No Active Plan
                                                </span>
                                            @endif
                                        </div>
                                        <div class="sub-card-body">
                                            @if(!empty($current_coins_sales))
                                                <div class="sub-plan-grid">
                                                    <div class="sub-plan-subcard">
                                                        <div class="sub-plan-subcard-icon sub-icon-emerald"><i class="fas fa-layer-group"></i></div>
                                                        <div class="min-w-0">
                                                            <div class="sub-plan-subcard-label">Plan Name</div>
                                                            <div class="sub-plan-subcard-val">{{ $current_coins_sales[0]['title'] }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="sub-plan-subcard">
                                                        <div class="sub-plan-subcard-icon sub-icon-blue"><i class="fas fa-calendar-alt"></i></div>
                                                        <div class="min-w-0">
                                                            <div class="sub-plan-subcard-label">Expiration Date</div>
                                                            <div class="sub-plan-subcard-val sub-num">{{ !empty($current_coins_sales[0]['end_date']) ? date('d M Y', strtotime($current_coins_sales[0]['end_date'])) : '—' }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="sub-plan-subcard">
                                                        <div class="sub-plan-subcard-icon sub-icon-amber"><i class="fas fa-coins"></i></div>
                                                        <div class="min-w-0">
                                                            <div class="sub-plan-subcard-label">Credit Coins</div>
                                                            <div class="sub-plan-subcard-val sub-num">{{ number_format($current_coins_sales[0]['actual_coins'] ?? 0) }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="sub-plan-subcard">
                                                        <div class="sub-plan-subcard-icon sub-icon-emerald"><i class="fas fa-wallet"></i></div>
                                                        <div class="min-w-0">
                                                            <div class="sub-plan-subcard-label">Available Coins</div>
                                                            <div class="sub-plan-subcard-val is-emerald sub-num">{{ number_format($current_coins_sales[0]['coins'] ?? 0) }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="sub-no-plan-box">
                                                    <div class="sub-no-plan-left">
                                                        <i class="fas fa-exclamation-circle"></i>
                                                        <span>No active sales plan found for your organization.</span>
                                                    </div>
                                                    <a href="javascript:;" class="sub-link-cta" onclick="scrollToWalletPackages('sales');return false;">
                                                        Browse Packages <i class="fas fa-arrow-right"></i>
                                                    </a>
                                                </div>
                                            @endif

                                            <div class="sub-kpi-grid">
                                                <div class="sub-kpi-subcard">
                                                    <div>
                                                        <div class="sub-kpi-val sub-num">{{ number_format($sal_total_purchased ?? 0) }}</div>
                                                        <div class="sub-kpi-label">Total Coins Purchased</div>
                                                    </div>
                                                    <span class="sub-kpi-mini-icon sub-icon-emerald"><i class="fas fa-shopping-bag"></i></span>
                                                </div>
                                                <div class="sub-kpi-subcard">
                                                    <div>
                                                        <div class="sub-kpi-val sub-num">{{ number_format($remain_coins_sales ?? 0) }}</div>
                                                        <div class="sub-kpi-label">Coins Available</div>
                                                    </div>
                                                    <span class="sub-kpi-mini-icon sub-icon-blue"><i class="fas fa-coins"></i></span>
                                                </div>
                                                <div class="sub-kpi-subcard">
                                                    <div>
                                                        <div class="sub-kpi-val sub-num">{{ number_format($sal_coins_used ?? 0) }}</div>
                                                        <div class="sub-kpi-label">Coins Used</div>
                                                    </div>
                                                    <span class="sub-kpi-mini-icon sub-icon-amber"><i class="fas fa-chart-line"></i></span>
                                                </div>
                                                <div class="sub-kpi-subcard">
                                                    <div>
                                                        <div class="sub-kpi-val sub-num">{{ $currIcon }} {{ number_format($sal_total_spend ?? 0) }}</div>
                                                        <div class="sub-kpi-label">Total Spend</div>
                                                    </div>
                                                    <span class="sub-kpi-mini-icon sub-icon-emerald"><i class="fas fa-receipt"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ────────────────────────────────────────────────────────────
                                     PRIMARY CARD 3: RECOMMENDED COIN PACKAGES (OPERATIONAL + SALES)
                                     ──────────────────────────────────────────────────────────── --}}
                                <div id="wallet-packages-section">
                                    {{-- Operational Packages --}}
                                    <div class="sub-primary-card operational-packages" id="wallet-rec-packages-op">
                                        <div class="sub-card-header">
                                            <div class="sub-card-header-left">
                                                <div class="sub-cat-icon sub-icon-purple">
                                                    <i class="fas fa-cubes"></i>
                                                </div>
                                                <div>
                                                    <h4 class="sub-card-title">Operational Coin Packages &amp; Plans</h4>
                                                    <p class="sub-card-desc">Choose a recruitment coin tier to unlock candidate profiles, downloads, and AI matching.</p>
                                                </div>
                                            </div>
                                            <div class="sub-card-header-right">
                                                <span class="sub-status-badge sub-badge-purple sub-num">
                                                    <i class="fas fa-box-open"></i> {{ $opPkgCount }} Plans Available
                                                </span>
                                                <a href="{{ url('payment-terms-and-conditions') }}" class="sub-link-cta" target="_blank">
                                                    <i class="fas fa-file-alt"></i> Pricing Guidelines
                                                </a>
                                            </div>
                                        </div>

                                        <div class="sub-card-body">
                                            <div class="sub-pkg-grid">
                                                @foreach ($opPackagesList as $i => $opr_val)
                                                    @php
                                                        $isCurrentOp = ($activeOpPackageId && $opr_val['id'] == $activeOpPackageId);
                                                        $isPopularOp = (!$isCurrentOp && ($opr_val['custome_pck'] ?? 0) != 2 && $i == 1);

                                                        $months = 12;
                                                        $number = $opr_val['days'] ?? 0;
                                                        if ($number >= 30) {
                                                            $months = floor($number / 30);
                                                        }
                                                        $perMonthCoins = $months > 0 ? round(($opr_val['coins'] ?? 0) / $months) : ($opr_val['coins'] ?? 0);

                                                        $vis = $opr_val['visibility_show'] ?? 1;
                                                        if ($vis == 1) {
                                                            $dispPrice = $currency == 'INR' ? ($opr_val['price'] ?? 0) : ($opr_val['price_usd'] ?? 0);
                                                            $period = 'Year';
                                                        } elseif ($vis == 2) {
                                                            $dispPrice = $currency == 'INR' ? ($opr_val['total_price_monthly'] ?? 0) : ($opr_val['total_price_usd_monthly'] ?? 0);
                                                            $period = 'Month';
                                                        } else {
                                                            $dispPrice = $currency == 'INR' ? ($opr_val['total_price_days'] ?? 0) : ($opr_val['total_price_usd_days'] ?? 0);
                                                            $period = 'Day';
                                                        }
                                                        $discountVal = $opr_val['discount'] ?? 0;
                                                        $origPrice = ($discountVal > 0 && $discountVal < 100) ? round($dispPrice / (1 - $discountVal / 100)) : null;

                                                        $featTitles = collect($opr_val['features'] ?? [])->pluck('title')->implode(' ');
                                                        $pkgSearchScope = strtolower(trim(($opr_val['title'] ?? '') . ' ' . ($opr_val['coins'] ?? '') . ' ' . $dispPrice . ' ' . $period . ' ' . $featTitles));
                                                    @endphp

                                                    <div class="sub-pkg-card {{ $isCurrentOp ? 'is-current-op' : ($isPopularOp ? 'is-popular-op' : '') }}"
                                                        data-search-text="{{ $pkgSearchScope }}">
                                                        <div class="sub-pkg-badges-row">
                                                            @if($isCurrentOp)
                                                                <span class="sub-pkg-tag sub-pkg-tag-current-op"><i class="fas fa-check-circle"></i> CURRENT PLAN</span>
                                                            @elseif($isPopularOp)
                                                                <span class="sub-pkg-tag sub-pkg-tag-popular-op"><i class="fas fa-fire"></i> MOST POPULAR</span>
                                                            @elseif(($opr_val['custome_pck'] ?? 0) == 2)
                                                                <span class="sub-pkg-tag sub-badge-emerald"><i class="fas fa-building"></i> ENTERPRISE</span>
                                                            @endif

                                                            @if($discountVal > 0)
                                                                <span class="sub-pkg-tag sub-pkg-tag-discount sub-num">{{ $discountVal }}% OFF</span>
                                                            @endif
                                                        </div>

                                                        <div class="sub-pkg-head">
                                                            <div class="sub-pkg-icon {{ ($opr_val['custome_pck'] ?? 0) == 2 ? 'sub-icon-emerald' : 'sub-icon-purple' }}">
                                                                <i class="fas {{ ($opr_val['custome_pck'] ?? 0) == 2 ? 'fa-building' : ($i == 1 ? 'fa-crown' : ($i == 2 ? 'fa-gem' : 'fa-star')) }}"></i>
                                                            </div>
                                                            <div class="min-w-0">
                                                                <div class="sub-pkg-title">{{ $opr_val['title'] }}</div>
                                                                <div class="sub-pkg-subtitle">
                                                                    {{ ($opr_val['custome_pck'] ?? 0) == 2 ? 'Best for Large Enterprises' : (!empty($opr_val['days']) ? $opr_val['days'] . ' Days Validity' : 'Custom Duration') }}
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="sub-pkg-price-subcard">
                                                            @if(($opr_val['custome_pck'] ?? 0) == 2)
                                                                <div class="sub-pkg-custom-price">Custom Pricing</div>
                                                                <span class="sub-pkg-coins-pill sub-badge-emerald">
                                                                    <i class="fas fa-infinity"></i> Unlimited Coins
                                                                </span>
                                                            @else
                                                                <div class="sub-pkg-orig-price sub-num">
                                                                    @if($origPrice)
                                                                        {{ $currIcon }}{{ number_format($origPrice) }}
                                                                    @else
                                                                        &nbsp;
                                                                    @endif
                                                                </div>
                                                                <div class="sub-pkg-price-line">
                                                                    <span class="sub-pkg-curr">{{ $currIcon }}</span>
                                                                    <span class="sub-pkg-val sub-num">{{ number_format($dispPrice) }}</span>
                                                                    <span class="sub-pkg-period">/ {{ $period }}</span>
                                                                </div>
                                                                <span class="sub-pkg-coins-pill sub-badge-purple sub-num">
                                                                    <i class="fas fa-coins"></i> {{ number_format($opr_val['coins'] ?? 0) }} Coins
                                                                </span>
                                                            @endif
                                                        </div>

                                                        <div class="sub-pkg-actions">
                                                            @if(($opr_val['custome_pck'] ?? 0) == 2)
                                                                @if($isCurrentOp)
                                                                    <a href="javascript:;" class="sub-pkg-btn sub-pkg-btn-current-op">
                                                                        <i class="fas fa-check-circle"></i> Current Plan
                                                                    </a>
                                                                @else
                                                                    <a href="javascript:;" class="sub-pkg-btn sub-pkg-btn-solid-sal btn-contact-sales customsubscription"
                                                                        data-id="{{ encrypt($opr_val['id']) }}" data-type="operational">
                                                                        <i class="fas fa-headset"></i> Contact Sales
                                                                    </a>
                                                                @endif
                                                            @elseif($isCurrentOp)
                                                                <a href="javascript:;" class="sub-pkg-btn sub-pkg-btn-current-op">
                                                                    <i class="fas fa-check-circle"></i> Current Plan
                                                                </a>
                                                                <a href="javascript:;" data-id="{{ encrypt($opr_val['id']) }}"
                                                                    class="sub-pkg-btn sub-pkg-btn-outline-op upgradeselect">
                                                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                                                </a>
                                                            @else
                                                                <a href="javascript:;" data-id="{{ encrypt($opr_val['id']) }}"
                                                                    class="sub-pkg-btn upgradeselect {{ $isPopularOp ? 'sub-pkg-btn-solid-op' : 'sub-pkg-btn-outline-op' }}">
                                                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                                                </a>
                                                            @endif
                                                        </div>

                                                        <div class="sub-pkg-features-box">
                                                            <div class="sub-pkg-feat-row">
                                                                <span>Monthly Allocation</span>
                                                                <span class="sub-pkg-feat-val sub-num">{{ ($opr_val['custome_pck'] ?? 0) == 2 ? 'Custom' : number_format($perMonthCoins) . ' Coins' }}</span>
                                                            </div>
                                                            <div class="sub-pkg-feat-row">
                                                                <span>Validity</span>
                                                                <span class="sub-pkg-feat-val sub-num">{{ !empty($opr_val['days']) ? $opr_val['days'] . ' Days' : 'Custom' }}</span>
                                                            </div>
                                                            @foreach (($opr_val['features'] ?? []) as $features_val)
                                                                <div class="sub-pkg-feat-row {{ $features_val->status ? '' : 'is-muted' }}">
                                                                    <span>{{ $features_val->title }}</span>
                                                                    @if($features_val->status)
                                                                        <i class="fas fa-check sub-pkg-feat-check"></i>
                                                                    @else
                                                                        <i class="fas fa-times sub-pkg-feat-cross"></i>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <div class="sub-empty-subcard mt-3" id="pkgSearchEmptyOp" style="display:none;">
                                                <div class="sub-empty-icon"><i class="fas fa-search"></i></div>
                                                <div class="sub-empty-title">No operational packages match your search</div>
                                                <div class="sub-empty-desc">Try clearing the search filter to view all {{ $opPkgCount }} operational plans.</div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Sales Packages --}}
                                    <div class="sub-primary-card salse-packages" id="wallet-rec-packages-sal" style="display:none;">
                                        <div class="sub-card-header">
                                            <div class="sub-card-header-left">
                                                <div class="sub-cat-icon sub-icon-emerald">
                                                    <i class="fas fa-cubes"></i>
                                                </div>
                                                <div>
                                                    <h4 class="sub-card-title">Sales Coin Packages &amp; Plans</h4>
                                                    <p class="sub-card-desc">Power marketplace lead generation, client proposals, and agency growth.</p>
                                                </div>
                                            </div>
                                            <div class="sub-card-header-right">
                                                <span class="sub-status-badge sub-badge-emerald sub-num">
                                                    <i class="fas fa-box-open"></i> {{ $salPkgCount }} Plans Available
                                                </span>
                                                <a href="{{ url('payment-terms-and-conditions') }}" class="sub-link-cta" target="_blank">
                                                    <i class="fas fa-file-alt"></i> Pricing Guidelines
                                                </a>
                                            </div>
                                        </div>

                                        <div class="sub-card-body">
                                            <div class="sub-pkg-grid">
                                                @foreach ($salPackagesList as $i => $sales_val)
                                                    @php
                                                        $isCurrentSal = ($activeSlPackageId && $sales_val['id'] == $activeSlPackageId);
                                                        $isPopularSal = (!$isCurrentSal && ($sales_val['custome_pck'] ?? 0) != 2 && $i == 1);

                                                        $months = 12;
                                                        $number = $sales_val['days'] ?? 0;
                                                        if ($number >= 30) {
                                                            $months = floor($number / 30);
                                                        }
                                                        $perMonthCoins = $months > 0 ? round(($sales_val['coins'] ?? 0) / $months) : ($sales_val['coins'] ?? 0);

                                                        $vis = $sales_val['visibility_show'] ?? 1;
                                                        if ($vis == 1) {
                                                            $dispPrice = $currency == 'INR' ? ($sales_val['price'] ?? 0) : ($sales_val['price_usd'] ?? 0);
                                                            $period = 'Year';
                                                        } elseif ($vis == 2) {
                                                            $dispPrice = $currency == 'INR' ? ($sales_val['total_price_monthly'] ?? 0) : ($sales_val['total_price_usd_monthly'] ?? 0);
                                                            $period = 'Month';
                                                        } else {
                                                            $dispPrice = $currency == 'INR' ? ($sales_val['total_price_days'] ?? 0) : ($sales_val['total_price_usd_days'] ?? 0);
                                                            $period = 'Day';
                                                        }
                                                        $discountVal = $sales_val['discount'] ?? 0;
                                                        $origPrice = ($discountVal > 0 && $discountVal < 100) ? round($dispPrice / (1 - $discountVal / 100)) : null;

                                                        $featTitles = collect($sales_val['features'] ?? [])->pluck('title')->implode(' ');
                                                        $pkgSearchScope = strtolower(trim(($sales_val['title'] ?? '') . ' ' . ($sales_val['coins'] ?? '') . ' ' . $dispPrice . ' ' . $period . ' ' . $featTitles));
                                                    @endphp

                                                    <div class="sub-pkg-card {{ $isCurrentSal ? 'is-current-sal' : ($isPopularSal ? 'is-popular-sal' : '') }}"
                                                        data-search-text="{{ $pkgSearchScope }}">
                                                        <div class="sub-pkg-badges-row">
                                                            @if($isCurrentSal)
                                                                <span class="sub-pkg-tag sub-pkg-tag-current-sal"><i class="fas fa-check-circle"></i> CURRENT PLAN</span>
                                                            @elseif($isPopularSal)
                                                                <span class="sub-pkg-tag sub-pkg-tag-popular-sal"><i class="fas fa-fire"></i> MOST POPULAR</span>
                                                            @elseif(($sales_val['custome_pck'] ?? 0) == 2)
                                                                <span class="sub-pkg-tag sub-badge-emerald"><i class="fas fa-building"></i> ENTERPRISE</span>
                                                            @endif

                                                            @if($discountVal > 0)
                                                                <span class="sub-pkg-tag sub-pkg-tag-discount sub-num">{{ $discountVal }}% OFF</span>
                                                            @endif
                                                        </div>

                                                        <div class="sub-pkg-head">
                                                            <div class="sub-pkg-icon sub-icon-emerald">
                                                                <i class="fas {{ ($sales_val['custome_pck'] ?? 0) == 2 ? 'fa-building' : ($i == 1 ? 'fa-crown' : ($i == 2 ? 'fa-gem' : 'fa-star')) }}"></i>
                                                            </div>
                                                            <div class="min-w-0">
                                                                <div class="sub-pkg-title">{{ $sales_val['title'] }}</div>
                                                                <div class="sub-pkg-subtitle">
                                                                    {{ ($sales_val['custome_pck'] ?? 0) == 2 ? 'Best for Large Agencies' : (!empty($sales_val['days']) ? $sales_val['days'] . ' Days Validity' : 'Custom Duration') }}
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="sub-pkg-price-subcard">
                                                            @if(($sales_val['custome_pck'] ?? 0) == 2)
                                                                <div class="sub-pkg-custom-price">Custom Pricing</div>
                                                                <span class="sub-pkg-coins-pill sub-badge-emerald">
                                                                    <i class="fas fa-infinity"></i> Unlimited Coins
                                                                </span>
                                                            @else
                                                                <div class="sub-pkg-orig-price sub-num">
                                                                    @if($origPrice)
                                                                        {{ $currIcon }}{{ number_format($origPrice) }}
                                                                    @else
                                                                        &nbsp;
                                                                    @endif
                                                                </div>
                                                                <div class="sub-pkg-price-line">
                                                                    <span class="sub-pkg-curr">{{ $currIcon }}</span>
                                                                    <span class="sub-pkg-val sub-num">{{ number_format($dispPrice) }}</span>
                                                                    <span class="sub-pkg-period">/ {{ $period }}</span>
                                                                </div>
                                                                <span class="sub-pkg-coins-pill sub-badge-emerald sub-num">
                                                                    <i class="fas fa-coins"></i> {{ number_format($sales_val['coins'] ?? 0) }} Coins
                                                                </span>
                                                            @endif
                                                        </div>

                                                        <div class="sub-pkg-actions">
                                                            @if(($sales_val['custome_pck'] ?? 0) == 2)
                                                                @if($isCurrentSal)
                                                                    <a href="javascript:;" class="sub-pkg-btn sub-pkg-btn-current-sal">
                                                                        <i class="fas fa-check-circle"></i> Current Plan
                                                                    </a>
                                                                @else
                                                                    <a href="javascript:;" class="sub-pkg-btn sub-pkg-btn-solid-sal btn-contact-sales customsubscription_sal"
                                                                        data-id="{{ encrypt($sales_val['id']) }}" data-type="sales">
                                                                        <i class="fas fa-headset"></i> Contact Sales
                                                                    </a>
                                                                @endif
                                                            @elseif($isCurrentSal)
                                                                <a href="javascript:;" class="sub-pkg-btn sub-pkg-btn-current-sal">
                                                                    <i class="fas fa-check-circle"></i> Current Plan
                                                                </a>
                                                                <a href="javascript:;" data-id="{{ encrypt($sales_val['id']) }}"
                                                                    class="sub-pkg-btn sub-pkg-btn-outline-sal upgradeselect">
                                                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                                                </a>
                                                            @else
                                                                <a href="javascript:;" data-id="{{ encrypt($sales_val['id']) }}"
                                                                    class="sub-pkg-btn upgradeselect {{ $isPopularSal ? 'sub-pkg-btn-solid-sal' : 'sub-pkg-btn-outline-sal' }}">
                                                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                                                </a>
                                                            @endif
                                                        </div>

                                                        <div class="sub-pkg-features-box">
                                                            <div class="sub-pkg-feat-row">
                                                                <span>Sales Allocation</span>
                                                                <span class="sub-pkg-feat-val sub-num">{{ ($sales_val['custome_pck'] ?? 0) == 2 ? 'Custom' : number_format($perMonthCoins) . ' Coins' }}</span>
                                                            </div>
                                                            <div class="sub-pkg-feat-row">
                                                                <span>Validity</span>
                                                                <span class="sub-pkg-feat-val sub-num">{{ !empty($sales_val['days']) ? $sales_val['days'] . ' Days' : 'Custom' }}</span>
                                                            </div>
                                                            @foreach (($sales_val['features'] ?? []) as $features_val)
                                                                <div class="sub-pkg-feat-row {{ $features_val->status ? '' : 'is-muted' }}">
                                                                    <span>{{ $features_val->title }}</span>
                                                                    @if($features_val->status)
                                                                        <i class="fas fa-check sub-pkg-feat-check"></i>
                                                                    @else
                                                                        <i class="fas fa-times sub-pkg-feat-cross"></i>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <div class="sub-empty-subcard mt-3" id="pkgSearchEmptySal" style="display:none;">
                                                <div class="sub-empty-icon"><i class="fas fa-search"></i></div>
                                                <div class="sub-empty-title">No sales packages match your search</div>
                                                <div class="sub-empty-desc">Try clearing the search filter to view all {{ $salPkgCount }} sales plans.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ────────────────────────────────────────────────────────────
                                     PRIMARY CARD 4: PLATFORM GUARANTEES & CUSTOM ENTERPRISE PLAN
                                     ──────────────────────────────────────────────────────────── --}}
                                <div id="wallet-enterprise-benefits">
                                    {{-- Operational Guarantees & Value --}}
                                    <div class="sub-primary-card operational-packages">
                                        <div class="sub-card-header">
                                            <div class="sub-card-header-left">
                                                <div class="sub-cat-icon sub-icon-blue">
                                                    <i class="fas fa-award"></i>
                                                </div>
                                                <div>
                                                    <h4 class="sub-card-title">Enterprise Guarantees &amp; Custom Plans</h4>
                                                    <p class="sub-card-desc">Built-in billing security, GST compliance, and tailored enterprise coin volumes.</p>
                                                </div>
                                            </div>
                                            <span class="sub-status-badge sub-badge-blue">
                                                <i class="fas fa-check-shield"></i> 99.9% SLA Billing
                                            </span>
                                        </div>
                                        <div class="sub-card-body">
                                            <div class="sub-sla-grid">
                                                <div class="sub-sla-subcard">
                                                    <div class="sub-sla-icon sub-icon-purple"><i class="fas fa-bolt"></i></div>
                                                    <div>
                                                        <div class="sub-sla-title">Instant Activation</div>
                                                        <div class="sub-sla-desc">Coins credited immediately</div>
                                                    </div>
                                                </div>
                                                <div class="sub-sla-subcard">
                                                    <div class="sub-sla-icon sub-icon-blue"><i class="fas fa-shield-alt"></i></div>
                                                    <div>
                                                        <div class="sub-sla-title">Secure Payments</div>
                                                        <div class="sub-sla-desc">100% encrypted gateway</div>
                                                    </div>
                                                </div>
                                                <div class="sub-sla-subcard">
                                                    <div class="sub-sla-icon sub-icon-emerald"><i class="fas fa-file-invoice"></i></div>
                                                    <div>
                                                        <div class="sub-sla-title">GST Invoices</div>
                                                        <div class="sub-sla-desc">Tax receipt on every order</div>
                                                    </div>
                                                </div>
                                                <div class="sub-sla-subcard">
                                                    <div class="sub-sla-icon sub-icon-amber"><i class="fas fa-headset"></i></div>
                                                    <div>
                                                        <div class="sub-sla-title">24&times;7 Support</div>
                                                        <div class="sub-sla-desc">Dedicated billing desk</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="sub-value-grid">
                                                <div class="sub-value-subcard">
                                                    <h5 class="sub-value-title">Why Choose Jero Operational Coins?</h5>
                                                    <div class="sub-why-mini-grid">
                                                        <div class="sub-why-mini-item">
                                                            <i class="fas fa-search" style="color:#6d28d9;"></i>
                                                            <div>
                                                                <div class="sub-sla-title">AI Resume Search</div>
                                                                <div class="sub-sla-desc">Unlock verified talent profiles</div>
                                                            </div>
                                                        </div>
                                                        <div class="sub-why-mini-item">
                                                            <i class="fas fa-download" style="color:#009ef7;"></i>
                                                            <div>
                                                                <div class="sub-sla-title">Resume Downloads</div>
                                                                <div class="sub-sla-desc">Instant formatted exports</div>
                                                            </div>
                                                        </div>
                                                        <div class="sub-why-mini-item">
                                                            <i class="fas fa-magic" style="color:#7c3aed;"></i>
                                                            <div>
                                                                <div class="sub-sla-title">AI Matching</div>
                                                                <div class="sub-sla-desc">Precision candidate scoring</div>
                                                            </div>
                                                        </div>
                                                        <div class="sub-why-mini-item">
                                                            <i class="fas fa-tasks" style="color:#059669;"></i>
                                                            <div>
                                                                <div class="sub-sla-title">Job Operations</div>
                                                                <div class="sub-sla-desc">Manage requisitions at scale</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="sub-value-subcard">
                                                    <div>
                                                        <h5 class="sub-value-title">Need a Custom Enterprise Plan?</h5>
                                                        <p class="sub-card-desc">Our enterprise solutions team will structure a custom coin package tailored to your hiring volume.</p>
                                                        <ul class="sub-custom-perks">
                                                            <li><i class="fas fa-check-circle"></i> Custom Coin Packages</li>
                                                            <li><i class="fas fa-check-circle"></i> Volume Discounts</li>
                                                            <li><i class="fas fa-check-circle"></i> Dedicated Account Manager</li>
                                                        </ul>
                                                    </div>
                                                    <div>
                                                        <a href="javascript:;" class="sub-btn-primary btn-contact-sales customsubscription"
                                                            data-id="{{ $customOpPkgId }}" data-type="operational">
                                                            <i class="fas fa-phone-alt"></i> Contact Sales Team
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="sub-earn-strip">
                                                <div>
                                                    <div class="sub-earn-title">Earn More. Save More.</div>
                                                    <div class="sub-earn-sub">Unlock volume discounts and bonus coin allocations on annual enterprise packages.</div>
                                                </div>
                                                <a href="javascript:;" class="sub-earn-cta" onclick="scrollToWalletPackages('operational');return false;">
                                                    <i class="fas fa-coins"></i> Buy Coins Now
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Sales Guarantees & Value --}}
                                    <div class="sub-primary-card salse-packages" style="display:none;">
                                        <div class="sub-card-header">
                                            <div class="sub-card-header-left">
                                                <div class="sub-cat-icon sub-icon-emerald">
                                                    <i class="fas fa-award"></i>
                                                </div>
                                                <div>
                                                    <h4 class="sub-card-title">Sales Guarantees &amp; Custom Plans</h4>
                                                    <p class="sub-card-desc">Enterprise commercial billing protection and tailored agency growth bundles.</p>
                                                </div>
                                            </div>
                                            <span class="sub-status-badge sub-badge-emerald">
                                                <i class="fas fa-check-circle"></i> 99.9% SLA Billing
                                            </span>
                                        </div>
                                        <div class="sub-card-body">
                                            <div class="sub-sla-grid">
                                                <div class="sub-sla-subcard">
                                                    <div class="sub-sla-icon sub-icon-emerald"><i class="fas fa-bolt"></i></div>
                                                    <div>
                                                        <div class="sub-sla-title">Instant Activation</div>
                                                        <div class="sub-sla-desc">Coins credited immediately</div>
                                                    </div>
                                                </div>
                                                <div class="sub-sla-subcard">
                                                    <div class="sub-sla-icon sub-icon-emerald"><i class="fas fa-shield-alt"></i></div>
                                                    <div>
                                                        <div class="sub-sla-title">Secure Payments</div>
                                                        <div class="sub-sla-desc">100% encrypted gateway</div>
                                                    </div>
                                                </div>
                                                <div class="sub-sla-subcard">
                                                    <div class="sub-sla-icon sub-icon-emerald"><i class="fas fa-file-invoice"></i></div>
                                                    <div>
                                                        <div class="sub-sla-title">GST Invoices</div>
                                                        <div class="sub-sla-desc">Tax receipt on every order</div>
                                                    </div>
                                                </div>
                                                <div class="sub-sla-subcard">
                                                    <div class="sub-sla-icon sub-icon-emerald"><i class="fas fa-headset"></i></div>
                                                    <div>
                                                        <div class="sub-sla-title">24&times;7 Support</div>
                                                        <div class="sub-sla-desc">Dedicated billing desk</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="sub-value-grid">
                                                <div class="sub-value-subcard">
                                                    <h5 class="sub-value-title">Why Choose Jero Sales Coins?</h5>
                                                    <div class="sub-why-mini-grid">
                                                        <div class="sub-why-mini-item">
                                                            <i class="fas fa-chart-line" style="color:#059669;"></i>
                                                            <div>
                                                                <div class="sub-sla-title">Increase Visibility</div>
                                                                <div class="sub-sla-desc">Priority placement in marketplace</div>
                                                            </div>
                                                        </div>
                                                        <div class="sub-why-mini-item">
                                                            <i class="fas fa-bullhorn" style="color:#059669;"></i>
                                                            <div>
                                                                <div class="sub-sla-title">Generate More Leads</div>
                                                                <div class="sub-sla-desc">Unlock premium client requirements</div>
                                                            </div>
                                                        </div>
                                                        <div class="sub-why-mini-item">
                                                            <i class="fas fa-handshake" style="color:#059669;"></i>
                                                            <div>
                                                                <div class="sub-sla-title">Grow Your Agency</div>
                                                                <div class="sub-sla-desc">Build trust and win enterprise clients</div>
                                                            </div>
                                                        </div>
                                                        <div class="sub-why-mini-item">
                                                            <i class="fas fa-chart-bar" style="color:#059669;"></i>
                                                            <div>
                                                                <div class="sub-sla-title">Advanced Insights</div>
                                                                <div class="sub-sla-desc">Track ROI and optimize conversion</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="sub-value-subcard">
                                                    <div>
                                                        <h5 class="sub-value-title">Need a Custom Sales Plan?</h5>
                                                        <p class="sub-card-desc">Our team will design a custom sales coin package aligned with your agency's expansion goals.</p>
                                                        <ul class="sub-custom-perks">
                                                            <li><i class="fas fa-check-circle"></i> Custom Coin Packages</li>
                                                            <li><i class="fas fa-check-circle"></i> Volume Discounts</li>
                                                            <li><i class="fas fa-check-circle"></i> Dedicated Support</li>
                                                        </ul>
                                                    </div>
                                                    <div>
                                                        <a href="javascript:;" class="sub-pkg-btn sub-pkg-btn-solid-sal btn-contact-sales customsubscription_sal"
                                                            style="width:auto;padding:0 18px;" data-id="{{ $customSalPkgId }}" data-type="sales">
                                                            <i class="fas fa-phone-alt"></i> Contact Sales Team
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="sub-earn-strip is-sales">
                                                <div>
                                                    <div class="sub-earn-title">Earn More. Save More.</div>
                                                    <div class="sub-earn-sub">Get exclusive discounts and bonus sales coins on higher agency tiers.</div>
                                                </div>
                                                <a href="javascript:;" class="sub-earn-cta" onclick="scrollToWalletPackages('sales');return false;">
                                                    <i class="fas fa-coins"></i> Buy Sales Coins Now
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Preserved null-safe legacy top-up hooks --}}
                                <div id="{{ $opCustomPckFlag == 2 ? '' : 'top_up_coin_packages_op' }}" class="d-none"></div>
                                <div id="{{ $salCustomPckFlag == 2 ? '' : 'top_up_coin_packages_sal' }}" class="d-none"></div>

                            </div>{{-- end::overview-only-section --}}

                            <!--begin::wallet-pane-transactions-->
                            <div id="wallet-pane-transactions" class="wallet-tab-pane p-0" style="display:none;">
                                @if(!empty($cart_transaction_history))
                                    @include('service_company.ajax_pages.subscription_transactions', ['cart_transaction_history' => $cart_transaction_history])
                                @else
                                    <div class="sub-primary-card">
                                        <div class="sub-card-body">
                                            <div class="sub-empty-subcard">
                                                <div class="spinner-border text-primary" role="status"></div>
                                                <div class="sub-empty-title mt-3">Loading transactions...</div>
                                                <div class="sub-empty-desc">Fetching your organization's subscription and coin ledger.</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <!--end::wallet-pane-transactions-->

                        </div>{{-- end::wallet-pane-overview --}}
                    </div>{{-- end::sub-stream --}}

                </div>{{-- end::sub-wrapper --}}
            </div>
        </div>
    </div>
    <!--end::Content-->
@endsection

@push('footer-script')
    <script src="https://js.stripe.com/v3/"></script>
    <script src="{{ asset('assets/node_modules/switchery/dist/switchery.min.js') }}<?php echo TM_VERSION; ?>"></script>

    <script>
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

        @if ($errors->has('error_get'))
            Swal.fire("Failed", "{{ $errors->first('error_get') }}", "error");
        @endif

        elems.forEach(function(html) {
            new Switchery(html, { size: 'medium' });
        });

        var opPkgTotalCount = {{ $opPkgCount }};
        var salPkgTotalCount = {{ $salPkgCount }};

        $('.salse-packages').hide();
        $('.package-switch').on('click keydown', function(e) {
            if (e.type === 'keydown' && e.key !== 'Enter' && e.key !== ' ') {
                return;
            }
            if (e.type === 'keydown') {
                e.preventDefault();
            }

            let checked = $(this).data('kt-plan');
            if (checked == 'operational') {
                $('.package-switch').removeClass('salse-tab-plan');
                $(this).addClass('opra-tab-plan');
                $('.salse-packages').hide();
                $('.operational-packages').fadeIn('fast');
                $('.wallet-center-header').removeClass('wallet-mode-sales');
                $('#walletBuyCoinsLabel').text('Buy Coins');
                $('#activeWalletBadge').removeClass('sub-badge-emerald').addClass('sub-badge-purple')
                    .html('<i class="fas fa-cogs"></i> Operational Wallet Active');
                $('#pkgTabCountBadge').text(opPkgTotalCount);
            } else {
                $('.package-switch').removeClass('opra-tab-plan');
                $(this).addClass('salse-tab-plan');
                $('.operational-packages').hide();
                $('.salse-packages').fadeIn('fast');
                $('.wallet-center-header').addClass('wallet-mode-sales');
                $('#walletBuyCoinsLabel').text('Buy Sales Coins');
                $('#activeWalletBadge').removeClass('sub-badge-purple').addClass('sub-badge-emerald')
                    .html('<i class="fas fa-briefcase"></i> Sales Wallet Active');
                $('#pkgTabCountBadge').text(salPkgTotalCount);
            }
            setTimeout(() => {
                $(this).removeClass('active');
                applySubscriptionSearch($('#subscription_search').val() || '');
            }, 80);
        });

        // Smoothly scroll to packages on Overview/Packages without hiding active content.
        window.scrollToWalletPackages = function(type) {
            var currentTab = $('.wallet-tab-link.wt-active').data('tab');
            if (currentTab !== 'overview' && currentTab !== 'packages') {
                switchWalletCenterTab('packages', true);
            }
            var activePlan = type || ($('.package-switch.salse-tab-plan').length ? 'sales' : 'operational');
            var targetId = (activePlan === 'sales') ? '#wallet-rec-packages-sal' : '#wallet-rec-packages-op';
            var $target = $(targetId);
            if ($target.length) {
                $('html, body').animate({ scrollTop: $target.offset().top - 20 }, 250);
            }
        };

        // Live client-side 300ms debounced search across packages and loaded transaction cards.
        function applySubscriptionSearch(rawQuery) {
            var query = $.trim((rawQuery || '').toLowerCase());

            // 1. Filter Operational Packages
            var opVisible = 0;
            $('#wallet-rec-packages-op .sub-pkg-card').each(function() {
                var hay = ($(this).data('search-text') || '') + ' ' + $(this).text().toLowerCase();
                var match = !query || hay.indexOf(query) !== -1;
                $(this).toggle(match);
                if (match) opVisible++;
            });
            $('#pkgSearchEmptyOp').toggle(opVisible === 0 && opPkgTotalCount > 0);

            // 2. Filter Sales Packages
            var salVisible = 0;
            $('#wallet-rec-packages-sal .sub-pkg-card').each(function() {
                var hay = ($(this).data('search-text') || '') + ' ' + $(this).text().toLowerCase();
                var match = !query || hay.indexOf(query) !== -1;
                $(this).toggle(match);
                if (match) salVisible++;
            });
            $('#pkgSearchEmptySal').toggle(salVisible === 0 && salPkgTotalCount > 0);

            // 3. Filter Transaction Cards (if loaded)
            var $txCards = $('#wallet-pane-transactions .sub-tx-card');
            if ($txCards.length > 0) {
                var txVisible = 0;
                $txCards.each(function() {
                    var hay = ($(this).data('search-text') || '') + ' ' + $(this).text().toLowerCase();
                    var match = !query || hay.indexOf(query) !== -1;
                    $(this).toggle(match);
                    if (match) txVisible++;
                });
                $('#txSearchEmptyState').toggle(txVisible === 0);
            }
        }

        var subSearchTimer = null;
        $(document).on('keyup input', '#subscription_search', function() {
            clearTimeout(subSearchTimer);
            var val = $(this).val() || '';
            $('#subscription_search_clear_btn').toggle(val.length > 0);
            subSearchTimer = setTimeout(function() {
                applySubscriptionSearch(val);
            }, 300);
        });

        $(document).on('click', '#subscription_search_clear_btn', function() {
            $('#subscription_search').val('');
            $(this).hide();
            applySubscriptionSearch('');
        });

        // Wallet Center tab switching (overview | packages | transactions).
        var txAjaxUrl = "{{ route('service_company.transactions.subscriptionAjax') }}";
        var txTabLoaded = false;

        function updateTxCountBadge() {
            var $txContainer = $('#wallet-pane-transactions .sub-tx-container');
            if ($txContainer.length) {
                var total = parseInt($txContainer.attr('data-tx-total') || '0', 10);
                $('#txTabCountBadge').text(total).show();
            }
        }

        window.switchWalletCenterTab = function(tab, noScroll) {
            if (tab === 'overview' || tab === 'packages') {
                $('.wallet-tab-pane:not(#wallet-pane-overview)').hide();
                $('#overview-only-section').show();

                if (tab === 'packages') {
                    $('#wallet-overview-summary').hide();
                } else {
                    $('#wallet-overview-summary').show();
                }

                $('.wallet-tab-link').removeClass('wt-active');
                $('.wallet-tab-link[data-tab="' + tab + '"]').addClass('wt-active');

                if (tab === 'packages' && !noScroll) {
                    scrollToWalletPackages();
                }
                applySubscriptionSearch($('#subscription_search').val() || '');
                return;
            }

            $('.wallet-tab-pane:not(#wallet-pane-overview)').hide();
            $('#overview-only-section').hide();
            $('#wallet-pane-' + tab).show();

            if (tab === 'transactions' && (!txTabLoaded || $('#wallet-pane-transactions .sub-tx-stream').length === 0)) {
                txTabLoaded = true;
                $('#wallet-pane-transactions').css('opacity', '0.55');
                $.ajax({
                    url: txAjaxUrl,
                    type: 'GET',
                    data: { page: 1 },
                    success: function(res) {
                        var htmlContent = (res && res.html) ? res.html : res;
                        $('#wallet-pane-transactions').html(htmlContent).css('opacity', '1');
                        updateTxCountBadge();
                        applySubscriptionSearch($('#subscription_search').val() || '');
                    },
                    error: function() {
                        $('#wallet-pane-transactions').css('opacity', '1');
                    }
                });
            }

            $('.wallet-tab-link').removeClass('wt-active');
            $('.wallet-tab-link[data-tab="' + tab + '"]').addClass('wt-active');

            if (!noScroll) {
                var $nav = $('#subscription_subtabs_bar');
                if ($nav.length) {
                    $('html, body').animate({ scrollTop: $nav.offset().top - 16 }, 150);
                }
            }
            applySubscriptionSearch($('#subscription_search').val() || '');
        };

        $(document).on('click', '.wallet-tab-link', function(e) {
            e.preventDefault();
            var tab = $(this).attr('data-tab');
            if (tab) {
                window.location.hash = 'tab=' + tab;
                switchWalletCenterTab(tab);
            }
        });

        function applyHashTab(fromHashChange) {
            var match = /tab=([a-z]+)/.exec(window.location.hash || '');
            var tab = match ? match[1] : 'overview';
            if (tab === 'packages' || tab === 'overview') {
                switchWalletCenterTab(tab, !fromHashChange);
            } else if (match && $('#wallet-pane-' + match[1]).length) {
                switchWalletCenterTab(match[1], !fromHashChange);
            } else {
                switchWalletCenterTab('overview', !fromHashChange);
            }
        }
        applyHashTab(false);
        $(window).on('hashchange', function() { applyHashTab(true); });

        // Contact Sales Team / Custom subscription inquiry workflow.
        $(document).on('click', '.topupcustom,.customsubscription,.customsubscription_sal,.btn-contact-sales', function(e) {
            e.preventDefault();
            var $btn = $(this);
            if ($btn.hasClass('is-busy') || $btn.prop('disabled')) {
                return false;
            }

            var isTopup = $btn.hasClass('topupcustom');
            var suscriptId = $btn.data('id') || $btn.attr('data-id') || ($btn.is('input') ? $btn.val() : null);
            var walletType = $btn.data('type') || ($btn.is('.customsubscription_sal') ? 'sales' : 'operational');

            var defaultOpId = "{{ $customOpPkgId ?? '' }}";
            var defaultSalId = "{{ $customSalPkgId ?? '' }}";

            if (!suscriptId && !isTopup) {
                suscriptId = (walletType === 'sales') ? defaultSalId : defaultOpId;
            }

            var url = "{{ route('service_company.subscribe.customCheckoutSubscribe') }}";
            if (isTopup) {
                url += '?topup=true';
            }

            Swal.fire({
                title: "Contact Sales Team",
                text: "Would you like our sales team to give you a call and discuss the details of the custom subscription package?",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                cancelButtonText: "No",
                confirmButtonText: "Yes, Contact Me"
            }).then((result) => {
                if (result.isConfirmed) {
                    $btn.addClass('is-busy').prop('disabled', true);
                    var originalHtml = $btn.html();
                    if (!$btn.is('input')) {
                        $btn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Sending...');
                    }

                    var token = "{{ csrf_token() }}";

                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: {
                            '_token': token,
                            'id': suscriptId,
                            'type': walletType
                        },
                        success: function(response) {
                            $btn.removeClass('is-busy').prop('disabled', false);
                            if (!$btn.is('input')) {
                                $btn.html(originalHtml);
                            }
                            var successText = (response && response.reference)
                                ? "Your request (" + response.reference + ") has been sent successfully. Our sales team will get in touch with you shortly."
                                : (response && response.message ? response.message : "Your request has been sent successfully. Our sales team will get in touch with you shortly.");
                            Swal.fire({
                                title: "Request Sent!",
                                text: successText,
                                icon: "success"
                            });
                        },
                        error: function(xhr) {
                            $btn.removeClass('is-busy').prop('disabled', false);
                            if (!$btn.is('input')) {
                                $btn.html(originalHtml);
                            }
                            var errorMsg = "Could not send your request at this time. Please try again later.";
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                title: "Submission Failed",
                                text: errorMsg,
                                icon: "error"
                            });
                        }
                    });
                }
            });
        });

        // Add package to Cart & redirect to checkout
        $(document).on('click', '.upgradeselect', function(e) {
            e.preventDefault();
            var $btn = $(this);
            if ($btn.hasClass('is-busy')) {
                return false;
            }
            $btn.addClass('is-busy').prop('disabled', true);
            var originalHtml = $btn.html();
            $btn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Adding...');

            var suscriptId = $btn.data('id');
            var url = "{{ route('service_company.subscribe.checkoutSubscribe') }}";

            if ($btn.hasClass('top-up-cart-op') || $btn.hasClass('top-up-cart-sal')) {
                url += '?topup=true';
            }

            var token = "{{ csrf_token() }}";
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    '_token': token,
                    '_method': 'POST',
                    'id': suscriptId
                },
                success: function() {
                    window.location = "{{ route('service_company.subscribe.checkoutSubscribeIist') }}";
                },
                error: function() {
                    $btn.removeClass('is-busy').prop('disabled', false).html(originalHtml);
                }
            });
        });

        $(document).on('click',
            '#oper_kt_modal_upgrade_plan_btn, #sales_kt_modal_upgrade_plan_btn, .top-up-cart-op, .top-up-cart-sal',
            function(e) {
                e.preventDefault();
                var $button = $(this);
                $button.prop('disabled', true);

                var suscriptId, url;

                if ($(this).is('#oper_kt_modal_upgrade_plan_btn')) {
                    suscriptId = $('input[name="opr_plan"]:checked').val();
                } else if ($(this).is('#sales_kt_modal_upgrade_plan_btn')) {
                    suscriptId = $('input[name="sales_plan"]:checked').val();
                } else if ($(this).hasClass('top-up-cart-op')) {
                    suscriptId = $("input[name='app_int_amount_op']:checked").val();
                } else if ($(this).hasClass('top-up-cart-sal')) {
                    suscriptId = $("input[name='app_int_amount_sal']:checked").val();
                }

                url = "{{ route('service_company.subscribe.checkoutSubscribe') }}";

                if ($(this).hasClass('top-up-cart-op') || $(this).hasClass('top-up-cart-sal')) {
                    url += '?topup=true';
                }

                var token = "{{ csrf_token() }}";

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        '_token': token,
                        '_method': 'POST',
                        'id': suscriptId
                    },
                    success: function() {
                        window.location = "{{ route('service_company.subscribe.checkoutSubscribeIist') }}";
                    },
                    error: function() {
                        $button.prop('disabled', false);
                    }
                });
            });

        $(document).on('click', '.opdownloadinvoice', function() {
            let checked = $(this).data('cart');
            var url = "{{ route('service_company.subscribe.receiptdownload') }}";
            $.ajax({
                type: 'GET',
                url: url,
                data: {
                    '_method': 'GET',
                    'id': checked
                },
                success: function(data, status, xhr) {
                    let filename = xhr.getResponseHeader('Content-Disposition').split('filename=')[1]
                        .replace(/['"]/g, '');
                    let blob = new Blob([data], { type: 'application/pdf' });
                    let link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = filename;
                    link.click();
                }
            });
        });

        // AJAX pagination for the Transactions tab.
        $(document).on('click', '#wallet-pane-transactions .pagination a.page-link', function(e) {
            e.preventDefault();
            var href = $(this).attr('href');
            var page = 1;
            var match = /[?&]page=(\d+)/.exec(href);
            if (match) page = parseInt(match[1], 10);

            var $pane = $('#wallet-pane-transactions');
            $pane.css('opacity', '0.55');

            $.ajax({
                url: txAjaxUrl,
                type: 'GET',
                data: { page: page },
                success: function(res) {
                    var htmlContent = (res && res.html) ? res.html : res;
                    $pane.html(htmlContent).css('opacity', '1');
                    updateTxCountBadge();
                    applySubscriptionSearch($('#subscription_search').val() || '');
                },
                error: function() {
                    $pane.css('opacity', '1');
                }
            });
        });
    </script>
@endpush
