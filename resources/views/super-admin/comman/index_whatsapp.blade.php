@extends('layouts.superadmin_layout')

@push('head-script')
    <style>
        body {
            overflow-x: hidden !important;
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif !important;
            background-color: #f8fafc !important;
        }

        /* ── Top Gap Collapse: eliminate excessive toolbar margin on standard 15.6" viewports ── */
        #kt_content {
            padding-top: 10px !important;
        }
        #kt_toolbar {
            display: none !important;
        }
        #kt_wrapper {
            padding-top: calc(65px + 8px) !important;
        }

        /* ── TIER 1: Top Header Toolbar Card ── */
        .wa-header-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 12px 18px !important;
            margin-bottom: 12px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            flex-wrap: wrap !important;
            gap: 12px !important;
        }

        .wa-brand-block {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .wa-brand-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #e8f8f0;
            color: #128c7e;
            border: 1px solid #c7f0db;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(18, 140, 126, 0.15);
        }

        .wa-title-wrap h1 {
            font-size: 15px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            margin: 0 !important;
            line-height: 1.25 !important;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .wa-title-wrap p {
            font-size: 11.5px !important;
            color: #64748b !important;
            margin: 2px 0 0 0 !important;
        }

        .wa-header-kpi-strip {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .wa-kpi-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }
        .wa-kpi-pill.active-pill {
            background: #ecfdf5;
            color: #059669;
            border-color: #a7f3d0;
        }
        .wa-kpi-pill.reminder-pill {
            background: #eff6ff;
            color: #2563eb;
            border-color: #bfdbfe;
        }

        .wa-actions-group {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* Search Sub-Card */
        .wa-search-subcard {
            position: relative;
            width: 280px;
            max-width: 100%;
        }

        .wa-search-subcard input {
            height: 36px;
            width: 100%;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            padding: 6px 32px 6px 34px;
            font-size: 12px;
            font-family: inherit;
            color: #0f172a;
            transition: all 0.15s ease-in-out;
        }

        .wa-search-subcard input:focus {
            background: #ffffff;
            border-color: #009ef7;
            box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12);
            outline: none;
        }

        .wa-search-icon {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 13px;
            pointer-events: none;
        }

        .wa-search-clear {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: #e2e8f0;
            border: none;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #475569;
            cursor: pointer;
            line-height: 1;
        }
        .wa-search-clear:hover {
            background: #cbd5e1;
            color: #0f172a;
        }

        .btn-create-wa {
            height: 36px !important;
            padding: 0 16px !important;
            border-radius: 8px !important;
            background: #25d366 !important;
            border: 1px solid #20ba5a !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            font-size: 12px !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            box-shadow: 0 2px 4px rgba(37, 211, 102, 0.25) !important;
            text-decoration: none !important;
            transition: all 0.15s ease-in-out !important;
        }
        .btn-create-wa:hover {
            background: #128c7e !important;
            border-color: #0e7266 !important;
            color: #ffffff !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(18, 140, 126, 0.3) !important;
        }

        /* ── TIER 2: Sub-Tabs & Filter Card ── */
        .wa-tabs-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 8px 12px !important;
            margin-bottom: 14px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            flex-wrap: wrap !important;
            gap: 10px !important;
        }

        .wa-tab-buttons-group {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .wa-tab-btn {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 6px 12px !important;
            font-size: 11.5px !important;
            font-weight: 600 !important;
            color: #475569 !important;
            cursor: pointer !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            transition: all 0.15s ease-in-out !important;
            line-height: 1 !important;
        }

        .wa-tab-btn:hover {
            background: #f8fafc !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }

        /* Active Tab Highlight Rule: single primary blue highlight */
        .wa-tab-btn.active {
            background: #009ef7 !important;
            border-color: #009ef7 !important;
            color: #ffffff !important;
            box-shadow: 0 2px 4px rgba(0, 158, 247, 0.25) !important;
        }

        .wa-tab-badge {
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            border-radius: 9px;
            font-size: 10.5px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            color: #475569;
        }
        .wa-tab-btn.active .wa-tab-badge {
            background: rgba(255, 255, 255, 0.28) !important;
            color: #ffffff !important;
        }

        .wa-filters-right {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .wa-sort-select {
            height: 32px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            font-size: 11.5px;
            font-weight: 500;
            color: #475569;
            padding: 4px 10px;
            outline: none;
            cursor: pointer;
        }
        .wa-sort-select:focus {
            border-color: #009ef7;
        }

        .wa-layout-toggle-btn {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.15s ease;
        }
        .wa-layout-toggle-btn:hover,
        .wa-layout-toggle-btn.active {
            background: #eff6ff;
            color: #009ef7;
            border-color: #bfdbfe;
        }

        /* ── 15.6" Screen (1366×768) Precision Responsiveness ── */
        @media (max-width: 1366px) {
            .wa-header-card {
                padding: 10px 14px !important;
                gap: 10px !important;
            }
            .wa-search-subcard {
                width: 220px !important;
            }
            .wa-kpi-pill {
                padding: 3px 8px !important;
                font-size: 10.5px !important;
            }
            .wa-brand-icon {
                width: 36px !important;
                height: 36px !important;
                font-size: 18px !important;
            }
            .wa-title-wrap h1 {
                font-size: 14px !important;
            }
        }

        /* ── TIER 3: Primary Card Stream & Enterprise Grid ── */
        .wa-card-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            width: 100%;
        }

        @media (max-width: 1440px) {
            .wa-card-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width: 900px) {
            .wa-card-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-height: 800px) {
            .wa-drawer-phone-wrap {
                padding: 8px 12px !important;
            }
            .wa-phone-chat-area {
                min-height: 240px !important;
                max-height: 340px !important;
                padding: 10px 10px !important;
            }
        }

        /* Stream / List View Override */
        .wa-card-grid.stream-mode {
            grid-template-columns: 1fr !important;
            gap: 10px !important;
        }

        /* Primary Entity Card */
        .wa-primary-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 14px 16px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
            transition: all 0.18s ease-in-out !important;
            display: flex;
            flex-direction: column;
            gap: 10px;
            position: relative;
            border-left: 4px solid #25d366 !important;
        }

        .wa-primary-card:hover {
            border-color: #cbd5e1 !important;
            border-left-color: #128c7e !important;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08) !important;
            transform: translateY(-2px);
        }

        .wa-primary-card.is-inactive {
            border-left-color: #cbd5e1 !important;
            opacity: 0.85;
        }

        /* Sub-Card 1: Header Strip */
        .wa-card-subhead {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid #f1f5f9;
        }

        .wa-card-title-group {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            flex: 1;
            min-width: 0;
        }

        .wa-card-subicon {
            width: 28px;
            height: 28px;
            border-radius: 7px;
            background: #e8f8f0;
            color: #128c7e;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .wa-card-title {
            font-size: 13px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            margin: 0 !important;
            line-height: 1.35 !important;
            word-break: break-word;
        }

        .wa-card-badges {
            display: flex;
            align-items: center;
            gap: 5px;
            flex-shrink: 0;
        }

        .wa-pos-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 6px;
            background: #fffbeb;
            color: #d97706;
            border: 1px solid #fde68a;
            white-space: nowrap;
        }

        .wa-id-badge {
            font-size: 10px;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 6px;
            background: #f1f5f9;
            color: #64748b;
            border: 1px solid #e2e8f0;
            white-space: nowrap;
        }

        /* Sub-Card 2: WhatsApp Chat Bubble Snippet */
        .wa-card-bubble-wrap {
            background: #efeae2;
            border: 1px solid #e5ded8;
            border-radius: 8px;
            padding: 10px 12px;
            position: relative;
            cursor: pointer;
            transition: background 0.15s ease;
        }
        .wa-card-bubble-wrap:hover {
            background: #ece5dc;
        }

        .wa-chat-bubble {
            background: #ffffff;
            border-radius: 7.5px 7.5px 7.5px 0;
            padding: 8px 11px;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.08);
            position: relative;
            font-size: 11.5px;
            line-height: 1.5;
            color: #111827;
            white-space: pre-wrap;
            word-break: break-word;
            max-height: 96px;
            overflow: hidden;
            font-family: inherit;
        }

        .wa-chat-bubble::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 28px;
            background: linear-gradient(to bottom, rgba(255,255,255,0), rgba(255,255,255,0.95));
            pointer-events: none;
        }

        .wa-bubble-tick {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            font-size: 9.5px;
            color: #3b82f6;
            position: absolute;
            right: 8px;
            bottom: 4px;
            z-index: 2;
        }

        /* Variable Highlight Chips */
        .wa-token-chip {
            background: #e0f2fe;
            color: #0369a1;
            padding: 1px 4px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 10px;
            border: 1px solid #bae6fd;
            display: inline-block;
            margin: 1px 0;
        }

        /* Sub-Card 3: Variables & Metrics Strip */
        .wa-card-meta-strip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 6px;
            font-size: 11px;
            color: #64748b;
            padding-top: 2px;
        }

        .wa-vars-list {
            display: flex;
            align-items: center;
            gap: 4px;
            flex-wrap: wrap;
            flex: 1;
        }

        .wa-var-pill {
            font-size: 9.5px;
            font-weight: 600;
            padding: 1px 6px;
            border-radius: 4px;
            background: #f8fafc;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .wa-stats-pill {
            font-size: 10.5px;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
        }

        /* Sub-Card 4: Control Strip & Action Buttons */
        .wa-card-actions-strip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding-top: 8px;
            border-top: 1px solid #f1f5f9;
            margin-top: auto;
        }

        .wa-toggle-wrap {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
            user-select: none;
        }

        .wa-toggle-label {
            font-size: 11px;
            font-weight: 600;
            color: #475569;
            line-height: 1;
        }
        .wa-toggle-label.active-label {
            color: #10b981;
        }

        .wa-btn-group-right {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .wa-mini-btn {
            height: 28px;
            padding: 0 9px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
            border: 1px solid transparent;
            text-decoration: none !important;
            transition: all 0.12s ease-in-out;
            line-height: 1;
        }

        .wa-mini-btn-preview {
            background: #eff6ff;
            color: #2563eb;
            border-color: #dbeafe;
        }
        .wa-mini-btn-preview:hover {
            background: #2563eb;
            color: #ffffff;
            border-color: #2563eb;
        }

        .wa-mini-btn-copy {
            background: #f8fafc;
            color: #475569;
            border-color: #e2e8f0;
        }
        .wa-mini-btn-copy:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        .wa-mini-btn-edit {
            background: #f0fdf4;
            color: #16a34a;
            border-color: #bbf7d0;
        }
        .wa-mini-btn-edit:hover {
            background: #16a34a;
            color: #ffffff;
            border-color: #16a34a;
        }

        /* ── Empty State ── */
        .wa-empty-state {
            background: #ffffff;
            border: 1px dashed #cbd5e1;
            border-radius: 10px;
            padding: 40px 20px;
            text-align: center;
            grid-column: 1 / -1;
        }
        .wa-empty-state i {
            font-size: 42px;
            color: #94a3b8;
            margin-bottom: 12px;
        }
        .wa-empty-state h3 {
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }
        .wa-empty-state p {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 16px;
        }

        /* ── Offcanvas Smartphone Preview Drawer ── */
        #kt_whatsapp_preview_drawer {
            width: 420px !important;
            max-width: 95vw !important;
            display: flex;
            flex-direction: column;
            background: #f8fafc;
        }

        .wa-drawer-header {
            padding: 12px 18px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .wa-drawer-phone-wrap {
            padding: 16px;
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Phone Device Simulation */
        .wa-phone-frame {
            width: 360px;
            max-width: 100%;
            background: #efeae2;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15), 0 0 0 8px #1f2937;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* WhatsApp Smartphone Header */
        .wa-phone-topbar {
            background: #075e54;
            color: #ffffff;
            padding: 10px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .wa-phone-header-left {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
            min-width: 0;
        }

        .wa-phone-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #25d366;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            flex-shrink: 0;
        }

        .wa-phone-contact-info {
            line-height: 1.2;
            overflow: hidden;
        }
        .wa-phone-contact-name {
            font-size: 13px;
            font-weight: 700;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .wa-phone-contact-status {
            font-size: 10px;
            color: #a7f3d0;
        }

        .wa-phone-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            color: #ffffff;
        }

        /* WhatsApp Chat Area */
        .wa-phone-chat-area {
            padding: 16px 12px;
            min-height: 380px;
            max-height: 520px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
            background: #efeae2;
        }

        .wa-phone-bubble {
            background: #ffffff;
            border-radius: 8px 8px 8px 0;
            padding: 9px 12px 6px 12px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.12);
            font-size: 12px;
            line-height: 1.5;
            color: #111827;
            white-space: pre-wrap;
            word-break: break-word;
            max-width: 90%;
            position: relative;
        }

        .wa-phone-bubble-time {
            text-align: right;
            font-size: 9.5px;
            color: #94a3b8;
            margin-top: 4px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 3px;
        }

        .wa-phone-bubble-time svg {
            color: #3b82f6;
        }

        /* Switch Mode Toggle Strip inside Drawer */
        .wa-drawer-mode-strip {
            padding: 10px 18px;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
    </style>
@endpush

@php
    // Helper function to strip HTML while preserving natural paragraph line breaks for WhatsApp
    if (!function_exists('formatWhatsAppPreviewSnippet')) {
        function formatWhatsAppPreviewSnippet($html) {
            $text = preg_replace('/<br\s*\/?>/i', "\n", (string) $html);
            $text = preg_replace('/<\/p>/i', "\n\n", $text);
            $text = preg_replace('/<\/li>/i', "\n", $text);
            $text = strip_tags($text);
            $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            return trim(preg_replace("/\n{3,}/", "\n\n", $text));
        }
    }
@endphp

@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="container-fluid px-3">

            {{-- ═══════════════════════════════════════════════════════════════
                 TIER 1: TOP HEADER TOOLBAR CARD (White, Border #e2e8f0, Radius 10px)
                 ═══════════════════════════════════════════════════════════════ --}}
            <div class="wa-header-card">
                <div class="wa-brand-block">
                    <div class="wa-brand-icon">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <div class="wa-title-wrap">
                        <h1>
                            WhatsApp Templates & Messaging Hub
                            <span class="badge badge-light-success fs-8 fw-bold">Super Admin</span>
                        </h1>
                        <p>Global recruitment messaging, interview invitations, reminders, and candidate reachout templates</p>
                    </div>
                </div>

                {{-- Header KPI Metrics Strip --}}
                <div class="wa-header-kpi-strip d-none d-md-flex">
                    <span class="wa-kpi-pill active-pill">
                        <i class="fas fa-check-circle text-success fs-9"></i> Active: <strong>{{ $kpi['active'] ?? 0 }}</strong>
                    </span>
                    <span class="wa-kpi-pill reminder-pill">
                        <i class="fas fa-bell text-primary fs-9"></i> Reminders: <strong>{{ $kpi['reminders'] ?? 0 }}</strong>
                    </span>
                    <span class="wa-kpi-pill">
                        <i class="fas fa-calendar-check text-info fs-9"></i> Interviews: <strong>{{ $kpi['interview'] ?? 0 }}</strong>
                    </span>
                    <span class="wa-kpi-pill">
                        <i class="fas fa-phone-alt text-warning fs-9"></i> Reachout: <strong>{{ $kpi['call'] ?? 0 }}</strong>
                    </span>
                </div>

                {{-- Actions & Search Sub-Card --}}
                <div class="wa-actions-group">
                    <div class="wa-search-subcard">
                        <i class="fas fa-search wa-search-icon"></i>
                        <input type="text" id="waTemplateSearch" placeholder="Search templates (title, message, pos)..." autocomplete="off">
                        <button type="button" id="waSearchClear" class="wa-search-clear" title="Clear Search">&times;</button>
                    </div>

                    <a href="{{ route('superadmin.company.whatsappThemesupdate', 0) }}" class="btn-create-wa" id="btnCreateTemplate">
                        <i class="fas fa-plus"></i>
                        <span>Create Template</span>
                    </a>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════════
                 TIER 2: SUB-TABS & CATEGORY FILTER CARD
                 ═══════════════════════════════════════════════════════════════ --}}
            <div class="wa-tabs-card">
                <div class="wa-tab-buttons-group">
                    <button type="button" class="wa-tab-btn active" data-category="all">
                        <i class="fas fa-layer-group fs-9"></i> All Templates
                        <span class="wa-tab-badge" id="tabCountAll">{{ $kpi['total'] ?? count($th_email_template ?? []) }}</span>
                    </button>
                    <button type="button" class="wa-tab-btn" data-category="call">
                        <i class="fas fa-phone-alt fs-9"></i> Call Follow-up
                        <span class="wa-tab-badge" id="tabCountCall">{{ $kpi['call'] ?? 0 }}</span>
                    </button>
                    <button type="button" class="wa-tab-btn" data-category="interview">
                        <i class="fas fa-calendar-alt fs-9"></i> Interview Availability
                        <span class="wa-tab-badge" id="tabCountInterview">{{ $kpi['interview'] ?? 0 }}</span>
                    </button>
                    <button type="button" class="wa-tab-btn" data-category="reminders">
                        <i class="fas fa-bell fs-9"></i> Auto Reminders
                        <span class="wa-tab-badge" id="tabCountReminders">{{ $kpi['reminders'] ?? 0 }}</span>
                    </button>
                    <button type="button" class="wa-tab-btn" data-category="offer">
                        <i class="fas fa-file-signature fs-9"></i> Offer & Joining
                        <span class="wa-tab-badge" id="tabCountOffer">{{ $kpi['offer_joining'] ?? 0 }}</span>
                    </button>
                </div>

                <div class="wa-filters-right">
                    {{-- Status Filter --}}
                    <select id="waStatusFilter" class="wa-sort-select">
                        <option value="all">All Statuses (Active & Inactive)</option>
                        <option value="1">Active Only</option>
                        <option value="0">Inactive Only</option>
                    </select>

                    {{-- Sort Selector --}}
                    <select id="waSortSelect" class="wa-sort-select">
                        <option value="pos_asc">Sort: Position Order (Asc)</option>
                        <option value="pos_desc">Sort: Position Order (Desc)</option>
                        <option value="id_desc">Sort: ID (Newest first)</option>
                        <option value="title_asc">Sort: Title (A &rarr; Z)</option>
                    </select>

                    {{-- Grid / Stream View Switcher --}}
                    <button type="button" class="wa-layout-toggle-btn active" id="waViewGridBtn" title="Grid View">
                        <i class="fas fa-th-large"></i>
                    </button>
                    <button type="button" class="wa-layout-toggle-btn" id="waViewStreamBtn" title="Stream View">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════════
                 TIER 3: PRIMARY CARD STREAM / ENTERPRISE GRID
                 ═══════════════════════════════════════════════════════════════ --}}
            <div class="wa-card-grid" id="waCardGrid">
                @if (!empty($th_email_template) && count($th_email_template) > 0)
                    @foreach ($th_email_template as $val_tem)
                        @php
                            $tempId = (int) ($val_tem->e_temp_id ?? $val_tem->id ?? 0);
                            $displayName = (string) ($val_tem->display_name ?? $val_tem->template_name ?? 'Untitled Template');
                            $rawBody = (string) ($val_tem->template_description ?? '');
                            $plainSnippet = formatWhatsAppPreviewSnippet($rawBody);
                            $cleanCharCount = mb_strlen($plainSnippet);
                            $estReadTime = max(5, round(($cleanCharCount / 18) * 1)); // ~18 chars/sec
                            $status = (int) ($val_tem->status ?? 0);
                            $orderPos = isset($val_tem->orderByShow) && $val_tem->orderByShow !== '' && $val_tem->orderByShow !== null ? (int) $val_tem->orderByShow : 9999;
                            $orderPosDisplay = $orderPos < 9999 ? $orderPos : '—';

                            // Categorization for client-side filtering
                            $lowerName = strtolower($displayName);
                            $category = 'custom';
                            if (str_contains($lowerName, 'reminder') || str_contains($lowerName, 'notification automatic')) {
                                $category = 'reminders';
                            } elseif (str_contains($lowerName, 'interview')) {
                                $category = 'interview';
                            } elseif (str_contains($lowerName, 'call') || str_contains($lowerName, 'answer')) {
                                $category = 'call';
                            } elseif (str_contains($lowerName, 'offer') || str_contains($lowerName, 'joining')) {
                                $category = 'offer';
                            }

                            // Extract variable tags
                            preg_match_all('/\[\{[A-Z0-9_]+\}\]/i', $rawBody, $matches);
                            $vars = array_unique($matches[0] ?? []);
                        @endphp

                        <div class="wa-primary-card {{ $status === 0 ? 'is-inactive' : '' }}" 
                             id="waCard_{{ $tempId }}"
                             data-id="{{ $tempId }}"
                             data-title="{{ strtolower($displayName) }}"
                             data-body="{{ strtolower($plainSnippet) }}"
                             data-category="{{ $category }}"
                             data-status="{{ $status }}"
                             data-pos="{{ $orderPos }}"
                             data-raw="{{ htmlspecialchars($rawBody, ENT_QUOTES, 'UTF-8') }}"
                             data-display-name="{{ htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8') }}"
                             data-subject="{{ htmlspecialchars($val_tem->template_subject ?? '', ENT_QUOTES, 'UTF-8') }}"
                             data-pos-val="{{ $orderPosDisplay }}">

                            {{-- Sub-Card 1: Header Strip --}}
                            <div class="wa-card-subhead">
                                <div class="wa-card-title-group">
                                    <div class="wa-card-subicon">
                                        <i class="fab fa-whatsapp"></i>
                                    </div>
                                    <h3 class="wa-card-title" title="{{ $displayName }}">
                                        {{ $displayName }}
                                    </h3>
                                </div>
                                <div class="wa-card-badges">
                                    <span class="wa-pos-badge" title="Position Order">
                                        Pos: {{ $orderPosDisplay }}
                                    </span>
                                    <span class="wa-id-badge" title="Template ID">
                                        #{{ $tempId }}
                                    </span>
                                </div>
                            </div>

                            {{-- Sub-Card 2: WhatsApp Chat Bubble Snippet --}}
                            <div class="wa-card-bubble-wrap btn-open-preview" data-id="{{ $tempId }}" title="Click to view full preview">
                                <div class="wa-chat-bubble">
                                    @php
                                        // Render tokens as clean pill chips in snippet preview
                                        $highlightedSnippet = e(mb_substr($plainSnippet, 0, 240));
                                        $highlightedSnippet = preg_replace('/(\[\{[A-Z0-9_]+\}\])/i', '<span class="wa-token-chip">$1</span>', $highlightedSnippet);
                                    @endphp
                                    {!! $highlightedSnippet !!}
                                    @if(mb_strlen($plainSnippet) > 240)...@endif
                                </div>
                                <span class="wa-bubble-tick">
                                    <span>{{ now()->format('g:i A') }}</span>
                                    <svg width="13" height="9" viewBox="0 0 16 11" fill="none"><path d="M1 6l3.5 3.5L9 4M6 6l3.5 3.5L15 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                            </div>

                            {{-- Sub-Card 3: Variables & Metrics Strip --}}
                            <div class="wa-card-meta-strip">
                                <div class="wa-vars-list">
                                    @if(!empty($vars))
                                        @foreach(array_slice($vars, 0, 3) as $var)
                                            <span class="wa-var-pill">{{ $var }}</span>
                                        @endforeach
                                        @if(count($vars) > 3)
                                            <span class="wa-var-pill">+{{ count($vars) - 3 }} more</span>
                                        @endif
                                    @else
                                        <span class="wa-stats-pill text-muted">No variable tokens</span>
                                    @endif
                                </div>
                                <span class="wa-stats-pill" title="Estimated read length">
                                    <i class="far fa-clock text-muted"></i> ~{{ $estReadTime }}s ({{ $cleanCharCount }} chars)
                                </span>
                            </div>

                            {{-- Sub-Card 4: Control Strip & Action Buttons --}}
                            <div class="wa-card-actions-strip">
                                {{-- Live Status Toggle Switch --}}
                                <div class="form-check form-switch form-check-custom form-check-solid wa-toggle-wrap" title="Toggle Active / Inactive">
                                    <input class="form-check-input h-20px w-30px wa-status-toggle" 
                                           type="checkbox" 
                                           id="statusSwitch_{{ $tempId }}" 
                                           data-id="{{ $tempId }}" 
                                           {{ $status === 1 ? 'checked' : '' }}>
                                    <label class="wa-toggle-label {{ $status === 1 ? 'active-label' : '' }}" 
                                           id="statusLabel_{{ $tempId }}" 
                                           for="statusSwitch_{{ $tempId }}">
                                        {{ $status === 1 ? 'Active' : 'Inactive' }}
                                    </label>
                                </div>

                                {{-- Quick Actions --}}
                                <div class="wa-btn-group-right">
                                    <button type="button" class="wa-mini-btn wa-mini-btn-preview btn-open-preview" data-id="{{ $tempId }}" title="Open Smartphone Preview">
                                        <i class="fas fa-mobile-alt"></i> Preview
                                    </button>
                                    <button type="button" class="wa-mini-btn wa-mini-btn-copy btn-copy-text" data-id="{{ $tempId }}" title="Copy WhatsApp Text">
                                        <i class="far fa-copy"></i> Copy
                                    </button>
                                    <a href="{{ route('superadmin.company.whatsappThemesupdate', $tempId) }}" class="wa-mini-btn wa-mini-btn-edit" title="Edit Template">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </div>
                            </div>

                        </div>
                    @endforeach
                @else
                    <div class="wa-empty-state">
                        <i class="fab fa-whatsapp"></i>
                        <h3>No WhatsApp Templates Found</h3>
                        <p>There are no WhatsApp message templates configured in the system yet.</p>
                        <a href="{{ route('superadmin.company.whatsappThemesupdate', 0) }}" class="btn-create-wa">
                            <i class="fas fa-plus"></i> Create First Template
                        </a>
                    </div>
                @endif
            </div>

            {{-- Empty Search Results Notice --}}
            <div id="waNoSearchResults" class="wa-empty-state d-none">
                <i class="fas fa-search"></i>
                <h3>No Matching WhatsApp Templates</h3>
                <p>No templates matched your search query or selected filter criteria.</p>
                <button type="button" class="btn btn-sm btn-light-primary" id="waResetFiltersBtn">
                    Reset Filters & Search
                </button>
            </div>

        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         INTERACTIVE OFFCANVAS SMARTPHONE PREVIEW DRAWER
         ═══════════════════════════════════════════════════════════════ --}}
    <div id="kt_whatsapp_preview_drawer_toggle" class="d-none"></div>
    <div id="kt_whatsapp_preview_drawer" 
         class="bg-white" 
         data-kt-drawer="true" 
         data-kt-drawer-name="whatsapp_preview" 
         data-kt-drawer-activate="true" 
         data-kt-drawer-overlay="true" 
         data-kt-drawer-width="{default:'380px', 'md': '440px'}" 
         data-kt-drawer-direction="end" 
         data-kt-drawer-toggle="#kt_whatsapp_preview_drawer_toggle" 
         data-kt-drawer-close="#kt_whatsapp_preview_drawer_close">

        {{-- Drawer Header --}}
        <div class="wa-drawer-header">
            <div>
                <h3 class="fw-bolder fs-6 text-dark mb-0">Live Smartphone Preview</h3>
                <small class="text-muted fs-8" id="drawerTemplateTitle">Template Preview</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="#" id="drawerEditBtn" class="btn btn-sm btn-light-primary py-1 px-3 fs-8">
                    <i class="fas fa-edit fs-9"></i> Edit
                </a>
                <button type="button" class="btn btn-sm btn-icon btn-active-light-primary" id="kt_whatsapp_preview_drawer_close">
                    <i class="fas fa-times fs-6"></i>
                </button>
            </div>
        </div>

        {{-- Phone Device Frame --}}
        <div class="wa-drawer-phone-wrap">
            <div class="wa-phone-frame">
                {{-- Phone Top Header --}}
                <div class="wa-phone-topbar">
                    <div class="wa-phone-header-left">
                        <span class="fs-7">&#8592;</span>
                        <div class="wa-phone-avatar">
                            <i class="fas fa-user fs-9"></i>
                        </div>
                        <div class="wa-phone-contact-info">
                            <div class="wa-phone-contact-name" id="drawerContactName">Candidate</div>
                            <div class="wa-phone-contact-status">online</div>
                        </div>
                    </div>
                    <div class="wa-phone-actions">
                        <i class="fas fa-video"></i>
                        <i class="fas fa-phone-alt"></i>
                        <i class="fas fa-ellipsis-v"></i>
                    </div>
                </div>

                {{-- Phone Chat Canvas --}}
                <div class="wa-phone-chat-area">
                    <div class="wa-phone-bubble">
                        <div id="drawerMessageText">Loading message...</div>
                        <div class="wa-phone-bubble-time">
                            <span id="drawerMessageTime">{{ now()->format('g:i A') }}</span>
                            <svg width="13" height="9" viewBox="0 0 16 11" fill="none"><path d="M1 6l3.5 3.5L9 4M6 6l3.5 3.5L15 2" stroke="#3b82f6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Switch Mode & Copy Strip --}}
        <div class="wa-drawer-mode-strip">
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-sm btn-outline-secondary active" id="btnPreviewSampleMode">
                    <i class="fas fa-magic fs-9"></i> Sample Candidate Data
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnPreviewRawMode">
                    <i class="fas fa-code fs-9"></i> Raw Variable Tags
                </button>
            </div>
            <button type="button" class="btn btn-sm btn-light-success" id="btnDrawerCopy">
                <i class="far fa-copy fs-9"></i> Copy Text
            </button>
        </div>

    </div>
@endsection

@push('footer-script')
    <script>
        $(document).ready(function() {
            // ── Client-First State ──
            var currentCategory = 'all';
            var currentStatus = 'all';
            var currentSearch = '';
            var activeDrawerRawText = '';
            var activeDrawerTitle = '';
            var samplePreviewActive = true;

            // Realistic Sample Data for Simulation
            var sampleDictionary = {
                '[{CANDIDATE_NAME}]': 'Alex Morgan',
                '[{JOB_TITLE}]': 'Senior Full Stack Engineer',
                '[{COMPANY_NAME}]': 'Acme Cloud Technologies',
                '[{CLIENT_NAME}]': 'Global Enterprise Inc.',
                '[{WORK_LOCATION}]': 'Bangalore, India (Hybrid)',
                '[{LOCATION}]': 'Bangalore, India',
                '[{COMPANY_WEBSITE}]': 'www.acmecloud.com',
                '[{CLIENT_WEBSITE}]': 'www.globalenterprise.com',
                '[{WEBSITE}]': 'www.acmecloud.com',
                '[{INTERVIEW_DATE_TIME}]': 'Thursday, 30 Oct 2026 at 03:30 PM (IST)',
                '[{INTERVIEW_MODE}]': 'Video Call (Google Meet)',
                '[{INTERVIEW_LINK_URL}]': 'https://meet.google.com/xyz-jero-works',
                '[{INTERVIEW_QUESTIONS}]': '1. Tell us about high-concurrency architecture.\n2. Explain CQRS Elasticsearch indexing.',
                '[{HR_NAME}]': 'Priya Sharma',
                '[{HR_PHONE}]': '+91 98765 43210',
                '[{HR_EMAIL}]': 'priya.sharma@acmecloud.com',
                '[{JOINING_DATE}]': '15 Nov 2026',
                '[{SALARY_AMOUNT}]': '2,400,000',
                '[{SALARY_MODE}]': 'Per Annum',
                '[{CURRENCY}]': 'INR (₹)',
                '[{JOB_TYPE}]': 'Full-Time',
                '[{PRIMARY_SKILLS}]': 'Laravel, Elasticsearch, Redis, Vue.js',
                '[{NOTICE_PERIOD}]': '30 Days',
                '[{CURRENT_CTC}]': '₹18,00,000 PA',
                '[{EXPECTED_CTC}]': '₹24,00,000 PA',
                '[{OFFER_LETTER_LINK}]': 'https://jero.works/offer/view/token-9982'
            };

            function cleanHtmlToPlain(html) {
                var tmp = document.createElement('div');
                tmp.innerHTML = html.replace(/<br\s*\/?>/gi, '\n')
                                    .replace(/<\/p>/gi, '\n\n')
                                    .replace(/<\/li>/gi, '\n');
                return (tmp.textContent || tmp.innerText || '').replace(/ /g, ' ').replace(/\n{3,}/g, '\n\n').trim();
            }

            function applySampleReplacements(text) {
                var replaced = text;
                Object.keys(sampleDictionary).forEach(function(token) {
                    var escaped = token.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                    replaced = replaced.replace(new RegExp(escaped, 'gi'), sampleDictionary[token]);
                });
                return replaced;
            }

            // ── Dynamic Client-First Filtering Engine ──
            function filterCards() {
                var visibleCount = 0;
                var $cards = $('.wa-primary-card');

                $cards.each(function() {
                    var $card = $(this);
                    var cardCat = $card.attr('data-category');
                    var cardStatus = $card.attr('data-status');
                    var cardTitle = $card.attr('data-title');
                    var cardBody = $card.attr('data-body');
                    var cardPos = $card.attr('data-pos-val');

                    // 1. Category Filter
                    var matchCat = (currentCategory === 'all') || (cardCat === currentCategory);

                    // 2. Status Filter
                    var matchStatus = (currentStatus === 'all') || (cardStatus === currentStatus);

                    // 3. Search Filter
                    var matchSearch = true;
                    if (currentSearch.trim() !== '') {
                        var q = currentSearch.toLowerCase();
                        matchSearch = cardTitle.indexOf(q) !== -1 || cardBody.indexOf(q) !== -1 || ('pos: ' + cardPos).indexOf(q) !== -1;
                    }

                    if (matchCat && matchStatus && matchSearch) {
                        $card.show();
                        visibleCount++;
                    } else {
                        $card.hide();
                    }
                });

                if (visibleCount === 0) {
                    $('#waNoSearchResults').removeClass('d-none');
                } else {
                    $('#waNoSearchResults').addClass('d-none');
                }
            }

            // ── Category Tab Click ──
            $('.wa-tab-btn').on('click', function() {
                $('.wa-tab-btn').removeClass('active');
                $(this).addClass('active');
                currentCategory = $(this).attr('data-category');
                filterCards();
            });

            // ── Status Filter Change ──
            $('#waStatusFilter').on('change', function() {
                currentStatus = $(this).val();
                filterCards();
            });

            // ── Search Input (300ms Debounce) ──
            var searchDebounceTimer = null;
            $('#waTemplateSearch').on('input', function() {
                var val = $(this).val();
                if (val.length > 0) {
                    $('#waSearchClear').css('display', 'flex');
                } else {
                    $('#waSearchClear').hide();
                }

                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(function() {
                    currentSearch = val;
                    filterCards();
                }, 250);
            });

            $('#waSearchClear').on('click', function() {
                $('#waTemplateSearch').val('').trigger('input');
            });

            $('#waResetFiltersBtn').on('click', function() {
                currentCategory = 'all';
                currentStatus = 'all';
                currentSearch = '';
                $('#waTemplateSearch').val('');
                $('#waSearchClear').hide();
                $('#waStatusFilter').val('all');
                $('.wa-tab-btn').removeClass('active');
                $('.wa-tab-btn[data-category="all"]').addClass('active');
                filterCards();
            });

            // ── Sort Dropdown ──
            $('#waSortSelect').on('change', function() {
                var sortMode = $(this).val();
                var $grid = $('#waCardGrid');
                var $cards = $grid.children('.wa-primary-card').get();

                $cards.sort(function(a, b) {
                    var posA = parseInt($(a).attr('data-pos'), 10) || 9999;
                    var posB = parseInt($(b).attr('data-pos'), 10) || 9999;
                    var idA = parseInt($(a).attr('data-id'), 10) || 0;
                    var idB = parseInt($(b).attr('data-id'), 10) || 0;
                    var titleA = $(a).attr('data-title') || '';
                    var titleB = $(b).attr('data-title') || '';

                    if (sortMode === 'pos_asc') {
                        return posA - posB || idA - idB;
                    } else if (sortMode === 'pos_desc') {
                        return posB - posA || idB - idA;
                    } else if (sortMode === 'id_desc') {
                        return idB - idA;
                    } else if (sortMode === 'title_asc') {
                        return titleA.localeCompare(titleB);
                    }
                    return 0;
                });

                $.each($cards, function(i, card) {
                    $grid.append(card);
                });
            });

            // ── Grid / Stream View Toggle ──
            $('#waViewGridBtn').on('click', function() {
                $('.wa-layout-toggle-btn').removeClass('active');
                $(this).addClass('active');
                $('#waCardGrid').removeClass('stream-mode');
            });

            $('#waViewStreamBtn').on('click', function() {
                $('.wa-layout-toggle-btn').removeClass('active');
                $(this).addClass('active');
                $('#waCardGrid').addClass('stream-mode');
            });

            // ── Live Status Switch Toggle (AJAX) ──
            $(document).on('change', '.wa-status-toggle', function() {
                var $toggle = $(this);
                var id = $toggle.attr('data-id');
                var newStatus = $toggle.is(':checked') ? 1 : 0;
                var $label = $('#statusLabel_' + id);
                var $card = $('#waCard_' + id);

                // Optimistic UI Update
                if (newStatus === 1) {
                    $label.text('Active').addClass('active-label');
                    $card.removeClass('is-inactive').attr('data-status', '1');
                } else {
                    $label.text('Inactive').removeClass('active-label');
                    $card.addClass('is-inactive').attr('data-status', '0');
                }

                $.ajax({
                    url: "{{ route('superadmin.company.toggleWhatsappTemplateStatus') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id,
                        status: newStatus
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            toastr.options = {
                                "closeButton": true,
                                "progressBar": true,
                                "positionClass": "toastr-top-right",
                                "timeOut": "2000"
                            };
                            toastr.success(response.message || 'Status updated successfully!');
                        } else {
                            revertToggle();
                        }
                    },
                    error: function(xhr) {
                        revertToggle();
                        toastr.error('Failed to update status. Please try again.');
                    }
                });

                function revertToggle() {
                    var reverted = newStatus === 1 ? 0 : 1;
                    $toggle.prop('checked', reverted === 1);
                    if (reverted === 1) {
                        $label.text('Active').addClass('active-label');
                        $card.removeClass('is-inactive').attr('data-status', '1');
                    } else {
                        $label.text('Inactive').removeClass('active-label');
                        $card.addClass('is-inactive').attr('data-status', '0');
                    }
                }
            });

            // ── Interactive Offcanvas Smartphone Preview Drawer ──
            $(document).on('click', '.btn-open-preview', function(e) {
                e.stopPropagation();
                var id = $(this).attr('data-id');
                var $card = $('#waCard_' + id);
                if (!$card.length) return;

                activeDrawerTitle = $card.attr('data-display-name');
                activeDrawerRawText = cleanHtmlToPlain($card.attr('data-raw'));

                $('#drawerTemplateTitle').text(activeDrawerTitle);
                var editUrl = "{{ route('superadmin.company.whatsappThemesupdate', ':id') }}".replace(':id', id);
                $('#drawerEditBtn').attr('href', editUrl);

                renderDrawerText();

                // Show Drawer using Metronic Drawer instance
                var drawerEl = document.querySelector('#kt_whatsapp_preview_drawer');
                var drawer = KTDrawer.getInstance(drawerEl);
                if (drawer) {
                    drawer.show();
                } else {
                    $('#kt_whatsapp_preview_drawer_toggle').trigger('click');
                }
            });

            function renderDrawerText() {
                var content = activeDrawerRawText;
                if (samplePreviewActive) {
                    content = applySampleReplacements(content);
                    $('#drawerContactName').text('Alex Morgan');
                } else {
                    $('#drawerContactName').text('Candidate (Raw Tags)');
                }
                $('#drawerMessageText').text(content);
            }

            $('#btnPreviewSampleMode').on('click', function() {
                samplePreviewActive = true;
                $('#btnPreviewSampleMode').addClass('active');
                $('#btnPreviewRawMode').removeClass('active');
                renderDrawerText();
            });

            $('#btnPreviewRawMode').on('click', function() {
                samplePreviewActive = false;
                $('#btnPreviewRawMode').addClass('active');
                $('#btnPreviewSampleMode').removeClass('active');
                renderDrawerText();
            });

            // ── Copy Message to Clipboard ──
            $(document).on('click', '.btn-copy-text', function(e) {
                e.stopPropagation();
                var id = $(this).attr('data-id');
                var $card = $('#waCard_' + id);
                var rawText = cleanHtmlToPlain($card.attr('data-raw'));

                copyStringToClipboard(rawText, 'WhatsApp template text copied to clipboard!');
            });

            $('#btnDrawerCopy').on('click', function() {
                var textToCopy = $('#drawerMessageText').text();
                copyStringToClipboard(textToCopy, 'Preview message copied to clipboard!');
            });

            function copyStringToClipboard(str, msg) {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(str).then(function() {
                        toastr.success(msg);
                    });
                } else {
                    var $temp = $('<textarea>');
                    $('body').append($temp);
                    $temp.val(str).select();
                    document.execCommand('copy');
                    $temp.remove();
                    toastr.success(msg);
                }
            }
        });
    </script>
@endpush
