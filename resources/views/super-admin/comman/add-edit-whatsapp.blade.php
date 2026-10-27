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

        /* ── TIER 1: Header Toolbar Card ── */
        .wa-edit-header-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 12px 18px !important;
            margin-bottom: 14px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            flex-wrap: wrap !important;
            gap: 12px !important;
        }

        .wa-brand-left {
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

        .wa-breadcrumbs {
            font-size: 11.5px !important;
            color: #64748b !important;
            margin: 2px 0 0 0 !important;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .wa-breadcrumbs a {
            color: #64748b;
            text-decoration: none;
        }
        .wa-breadcrumbs a:hover {
            color: #009ef7;
        }

        .wa-header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-cancel-custom {
            height: 36px !important;
            padding: 0 16px !important;
            border-radius: 8px !important;
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #475569 !important;
            font-weight: 600 !important;
            font-size: 12px !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            text-decoration: none !important;
            transition: all 0.15s ease-in-out !important;
        }
        .btn-cancel-custom:hover {
            background: #f1f5f9 !important;
            color: #0f172a !important;
            border-color: #94a3b8 !important;
        }

        .btn-save-custom {
            height: 36px !important;
            padding: 0 18px !important;
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
            cursor: pointer !important;
            transition: all 0.15s ease-in-out !important;
        }
        .btn-save-custom:hover {
            background: #128c7e !important;
            border-color: #0e7266 !important;
            color: #ffffff !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(18, 140, 126, 0.3) !important;
        }
        .btn-save-custom:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* ── 2-Column Responsive Workspace Grid ── */
        .wa-workspace-row {
            display: grid;
            grid-template-columns: minmax(0, 1.45fr) minmax(0, 1fr);
            gap: 16px;
            align-items: flex-start;
        }

        @media (max-width: 1366px) {
            .wa-edit-header-card {
                padding: 10px 14px !important;
            }
            .wa-workspace-row {
                gap: 12px;
                grid-template-columns: minmax(0, 1.38fr) minmax(0, 1fr);
            }
            .wa-subcard {
                padding: 12px 14px !important;
            }
        }

        @media (max-width: 991px) {
            .wa-workspace-row {
                grid-template-columns: 1fr;
            }
        }

        /* ── Section Sub-Cards ── */
        .wa-subcard {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 14px 18px !important;
            margin-bottom: 14px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
        }

        .wa-subcard-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding-bottom: 10px;
            margin-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .wa-subcard-title {
            font-size: 13px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            margin: 0 !important;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .wa-subcard-title i {
            color: #128c7e;
            font-size: 14px;
        }

        /* ── Form Controls ── */
        .wa-form-label {
            font-size: 11px !important;
            font-weight: 600 !important;
            color: #334155 !important;
            margin-bottom: 4px !important;
            display: block;
        }
        .wa-form-label .req {
            color: #ef4444;
        }

        .wa-form-input {
            width: 100%;
            height: 36px;
            border-radius: 7px;
            border: 1px solid #cbd5e1;
            padding: 6px 12px;
            font-size: 12px;
            font-family: inherit;
            color: #0f172a;
            background: #ffffff;
            transition: all 0.15s ease-in-out;
        }
        .wa-form-input:focus {
            border-color: #009ef7;
            box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12);
            outline: none;
        }

        .wa-form-hint {
            font-size: 10.5px;
            color: #64748b;
            margin-top: 3px;
            display: block;
        }

        /* ── Variables Catalog & Pills ── */
        .wa-var-category-tabs {
            display: flex;
            align-items: center;
            gap: 4px;
            flex-wrap: wrap;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #e2e8f0;
        }

        .wa-var-tab {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 3px 9px;
            font-size: 10.5px;
            font-weight: 600;
            color: #64748b;
            cursor: pointer;
            transition: all 0.12s ease;
        }
        .wa-var-tab:hover {
            background: #e2e8f0;
            color: #0f172a;
        }
        .wa-var-tab.active {
            background: #eff6ff;
            color: #009ef7;
            border-color: #bfdbfe;
        }

        .wa-var-pills-container {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            max-height: 110px;
            overflow-y: auto;
            padding: 4px 2px;
        }

        .wa-token-pill {
            background: #ffffff;
            border: 1px solid #bae6fd;
            border-radius: 6px;
            padding: 4px 9px;
            font-size: 11px;
            font-weight: 600;
            color: #0369a1;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            box-shadow: 0 1px 2px rgba(3, 105, 161, 0.05);
            transition: all 0.12s ease-in-out;
            user-select: none;
        }
        .wa-token-pill:hover {
            background: #0284c7;
            border-color: #0284c7;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 3px 6px rgba(2, 132, 199, 0.2);
        }
        .wa-token-pill i {
            font-size: 9px;
            opacity: 0.8;
        }

        /* ── WhatsApp Message Composer ── */
        .wa-composer-wrap {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            overflow: hidden;
            transition: border-color 0.15s ease-in-out;
            background: #ffffff;
        }
        .wa-composer-wrap:focus-within {
            border-color: #009ef7;
            box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12);
        }

        .wa-composer-toolbar {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 6px 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 6px;
        }

        .wa-tb-left, .wa-tb-right {
            display: flex;
            align-items: center;
            gap: 4px;
            flex-wrap: wrap;
        }

        .wa-tool-btn {
            height: 26px;
            padding: 0 8px;
            border-radius: 5px;
            border: 1px solid transparent;
            background: transparent;
            color: #475569;
            font-size: 11.5px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.1s ease;
        }
        .wa-tool-btn:hover {
            background: #e2e8f0;
            color: #0f172a;
        }
        .wa-tool-btn.active {
            background: #e0f2fe;
            color: #0284c7;
            border-color: #bae6fd;
        }

        .wa-tb-separator {
            width: 1px;
            height: 16px;
            background: #cbd5e1;
            margin: 0 2px;
        }

        .wa-textarea-main {
            width: 100%;
            min-height: 300px;
            border: none;
            outline: none;
            padding: 12px 14px;
            font-size: 12.5px;
            font-family: inherit;
            line-height: 1.6;
            color: #0f172a;
            resize: vertical;
            background: transparent;
        }

        /* Composer Metrics Footer Strip */
        .wa-composer-metrics-bar {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 6px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11px;
            color: #64748b;
        }

        .wa-metric-tag {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-weight: 500;
        }

        /* ── Right Column: Interactive Smartphone Device Simulator ── */
        .wa-simulator-subcard {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 14px 18px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
            position: sticky;
            top: 80px;
        }

        .wa-phone-container {
            width: 100%;
            max-width: 380px;
            margin: 0 auto;
            background: #efeae2;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.16), 0 0 0 8px #1f2937;
            display: flex;
            flex-direction: column;
            border: 1px solid #e5ded8;
        }

        /* Phone Top Notch / Speaker Strip */
        .wa-phone-speaker-strip {
            background: #1f2937;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .wa-phone-speaker {
            width: 48px;
            height: 4px;
            border-radius: 2px;
            background: #374151;
        }

        /* WhatsApp App Header */
        .wa-sim-topbar {
            background: #075e54;
            color: #ffffff;
            padding: 9px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .wa-sim-header-left {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
            min-width: 0;
        }

        .wa-sim-avatar {
            width: 32px;
            height: 32px;
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

        .wa-sim-contact-info {
            line-height: 1.2;
            overflow: hidden;
        }
        .wa-sim-contact-name {
            font-size: 12.5px;
            font-weight: 700;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .wa-sim-contact-status {
            font-size: 9.5px;
            color: #a7f3d0;
        }

        .wa-sim-header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: #ffffff;
        }

        /* WhatsApp Chat Wallpaper Body */
        .wa-sim-chat-canvas {
            padding: 16px 12px;
            min-height: 340px;
            max-height: 440px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
            background: #efeae2;
        }

        .wa-sim-bubble {
            background: #ffffff;
            border-radius: 8px 8px 8px 0;
            padding: 9px 12px 6px 12px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.12);
            font-size: 11.5px;
            line-height: 1.5;
            color: #111827;
            white-space: pre-wrap;
            word-break: break-word;
            max-width: 92%;
            position: relative;
        }

        .wa-sim-bubble-time {
            text-align: right;
            font-size: 9px;
            color: #94a3b8;
            margin-top: 4px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 3px;
        }

        .wa-sim-bubble-time svg {
            color: #3b82f6;
        }

        /* Simulator Mode Toggles Strip */
        .wa-sim-controls-strip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 12px;
            gap: 8px;
            flex-wrap: wrap;
        }

        /* ── 15.6" Laptop (768px height) Viewport Optimizations ── */
        @media (max-height: 800px) {
            .wa-simulator-subcard {
                top: 70px !important;
                padding: 10px 14px !important;
            }
            .wa-sim-chat-canvas {
                min-height: 230px !important;
                max-height: 310px !important;
                padding: 10px 10px !important;
            }
            .wa-textarea-main {
                min-height: 210px !important;
            }
            .wa-var-pills-container {
                max-height: 85px !important;
            }
        }
    </style>
@endpush

@php
    // Helper function to convert DB HTML template to clean WhatsApp Markdown
    if (!function_exists('htmlToWhatsAppText')) {
        function htmlToWhatsAppText($html) {
            if (empty($html)) return '';
            $text = (string) $html;
            // If it doesn't contain HTML tags, return cleanly trimmed
            if (!preg_match('/<[a-z][\s\S]*>/i', $text)) {
                return trim($text);
            }
            // Transform basic HTML into WhatsApp markdown
            $text = preg_replace('/<(strong|b)>(.*?)<\/(strong|b)>/i', '*$2*', $text);
            $text = preg_replace('/<(em|i)>(.*?)<\/(em|i)>/i', '_$2_', $text);
            $text = preg_replace('/<strike>(.*?)<\/strike>/i', '~$1~', $text);
            $text = preg_replace('/<li[^>]*>(.*?)<\/li>/i', "• $1\n", $text);
            $text = preg_replace('/<br\s*\/?>/i', "\n", $text);
            $text = preg_replace('/<\/p>/i', "\n\n", $text);
            $text = strip_tags($text);
            $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            return trim(preg_replace("/\n{3,}/", "\n\n", $text));
        }
    }

    $templateId = (int) ($th_email_template->e_temp_id ?? $th_email_template->id ?? 0);
    $displayName = (string) ($th_email_template->display_name ?? $th_email_template->template_name ?? '');
    $subject = (string) ($th_email_template->template_subject ?? '');
    $rawContent = (string) ($th_email_template->template_description ?? '');
    $cleanMessage = htmlToWhatsAppText($rawContent);
    $status = (int) ($th_email_template->status ?? 1);
    $orderPos = isset($th_email_template->orderByShow) && $th_email_template->orderByShow !== '' && $th_email_template->orderByShow !== null ? (int) $th_email_template->orderByShow : '';
@endphp

@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="container-fluid px-3">

            {{-- ═══════════════════════════════════════════════════════════════
                 TIER 1: HEADER TOOLBAR CARD (White, Border #e2e8f0, Radius 10px)
                 ═══════════════════════════════════════════════════════════════ --}}
            <div class="wa-edit-header-card">
                <div class="wa-brand-left">
                    <div class="wa-brand-icon">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <div class="wa-title-wrap">
                        <h1>
                            @if ($templateId > 0)
                                Edit WhatsApp Template: <span class="text-primary">{{ $displayName ?: '#' . $templateId }}</span>
                            @else
                                Create WhatsApp Template
                            @endif
                            <span class="badge badge-light-success fs-8 fw-bold">Super Admin</span>
                        </h1>
                        <div class="wa-breadcrumbs">
                            <a href="{{ route('superadmin.company.whatsappthemes') }}">WhatsApp Templates</a>
                            <span>&rsaquo;</span>
                            <span>{{ $templateId > 0 ? 'Edit Template #' . $templateId : 'Create New Template' }}</span>
                        </div>
                    </div>
                </div>

                <div class="wa-header-actions">
                    <a href="{{ route('superadmin.company.whatsappthemes') }}" class="btn-cancel-custom">
                        <i class="fas fa-arrow-left fs-9"></i>
                        <span>Back to Templates</span>
                    </a>
                    <button type="button" class="btn-save-custom" id="btnSaveTemplate">
                        <i class="fas fa-save fs-9"></i>
                        <span id="btnSaveText">Save & Sync Template</span>
                        <span id="btnSaveSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════════
                 2-COLUMN RESPONSIVE WORKSPACE (Composer + Live Smartphone Simulator)
                 ═══════════════════════════════════════════════════════════════ --}}
            <form id="waTemplateForm" autocomplete="off">
                @csrf
                <input type="hidden" name="tokenId" id="tokenId" value="{{ $templateId }}">
                <input type="hidden" name="temptype_is" id="temptype_is" value="9">

                <div class="wa-workspace-row">

                    {{-- ── LEFT COLUMN: Configuration, Variable Library & Composer (7 Cols) ── --}}
                    <div class="wa-composer-col">

                        {{-- Sub-Card 1: Metadata Configuration --}}
                        <div class="wa-subcard">
                            <div class="wa-subcard-header">
                                <h2 class="wa-subcard-title">
                                    <i class="fas fa-sliders-h"></i> Template Metadata & Configuration
                                </h2>
                                <span class="badge badge-light-primary fs-8 fw-semibold">Global ATS Scope</span>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="wa-form-label" for="template">
                                        Template Name / Display Title <span class="req">*</span>
                                    </label>
                                    <input type="text" class="wa-form-input" id="template" name="template" 
                                           placeholder="e.g. Candidate didn't answer call | Company Profile" 
                                           value="{{ $displayName }}" required>
                                    <span class="wa-form-hint">Displayed in recruiter template selection dropdowns.</span>
                                </div>

                                <div class="col-md-6">
                                    <label class="wa-form-label" for="subject">
                                        Internal Subject / Reference Label
                                    </label>
                                    <input type="text" class="wa-form-input" id="subject" name="subject" 
                                           placeholder="e.g. Follow-up regarding [{JOB_TITLE}] opening" 
                                           value="{{ $subject }}">
                                    <span class="wa-form-hint">Reference title used for ATS indexing and logging.</span>
                                </div>

                                <div class="col-md-6">
                                    <label class="wa-form-label" for="orderByShow">
                                        Position Order Priority
                                    </label>
                                    <input type="number" class="wa-form-input" id="orderByShow" name="orderByShow" 
                                           placeholder="e.g. 1, 2, 3..." 
                                           value="{{ $orderPos }}" min="0">
                                    <span class="wa-form-hint">Display sort order in recruitment drawers (lower numbers appear first).</span>
                                </div>

                                <div class="col-md-6">
                                    <label class="wa-form-label" for="status_is">
                                        Template Status <span class="req">*</span>
                                    </label>
                                    <select class="wa-form-input" id="status_is" name="status_is" required>
                                        <option value="1" {{ $status === 1 ? 'selected' : '' }}>Active (Available across all organizations)</option>
                                        <option value="0" {{ $status === 0 ? 'selected' : '' }}>Inactive (Hidden from recruiters)</option>
                                    </select>
                                    <span class="wa-form-hint">Inactive templates cannot be selected by service company recruiters.</span>
                                </div>
                            </div>
                        </div>

                        {{-- Sub-Card 2: Dynamic Variable Tags Library --}}
                        <div class="wa-subcard">
                            <div class="wa-subcard-header">
                                <h2 class="wa-subcard-title">
                                    <i class="fas fa-tags"></i> Dynamic Variable Tags Library
                                </h2>
                                <span class="badge badge-light-info fs-8 fw-semibold">Click chip to insert at cursor</span>
                            </div>

                            {{-- Category Sub-Tabs --}}
                            <div class="wa-var-category-tabs">
                                <span class="wa-var-tab active" data-vcat="all">All Variables</span>
                                <span class="wa-var-tab" data-vcat="candidate">Candidate Details</span>
                                <span class="wa-var-tab" data-vcat="job">Job & Company</span>
                                <span class="wa-var-tab" data-vcat="interview">Interview & Availability</span>
                                <span class="wa-var-tab" data-vcat="offer">Offer & Compensation</span>
                                <span class="wa-var-tab" data-vcat="recruiter">Recruiter / Sender</span>
                            </div>

                            {{-- Clickable Tag Pills --}}
                            <div class="wa-var-pills-container" id="waVarPillsContainer">
                                {{-- Candidate Tags --}}
                                <span class="wa-token-pill" data-token="[{CANDIDATE_NAME}]" data-cat="candidate" title="Candidate Full Name">
                                    <i class="fas fa-plus"></i> [{CANDIDATE_NAME}]
                                </span>
                                <span class="wa-token-pill" data-token="[{CANDIDATE_PHONE}]" data-cat="candidate" title="Candidate Phone">
                                    <i class="fas fa-plus"></i> [{CANDIDATE_PHONE}]
                                </span>
                                <span class="wa-token-pill" data-token="[{CANDIDATE_EMAIL}]" data-cat="candidate" title="Candidate Email Address">
                                    <i class="fas fa-plus"></i> [{CANDIDATE_EMAIL}]
                                </span>
                                <span class="wa-token-pill" data-token="[{PRIMARY_SKILLS}]" data-cat="candidate" title="Candidate Skills">
                                    <i class="fas fa-plus"></i> [{PRIMARY_SKILLS}]
                                </span>
                                <span class="wa-token-pill" data-token="[{NOTICE_PERIOD}]" data-cat="candidate" title="Candidate Notice Period">
                                    <i class="fas fa-plus"></i> [{NOTICE_PERIOD}]
                                </span>
                                <span class="wa-token-pill" data-token="[{CURRENT_CTC}]" data-cat="candidate" title="Current Compensation">
                                    <i class="fas fa-plus"></i> [{CURRENT_CTC}]
                                </span>
                                <span class="wa-token-pill" data-token="[{EXPECTED_CTC}]" data-cat="candidate" title="Expected Compensation">
                                    <i class="fas fa-plus"></i> [{EXPECTED_CTC}]
                                </span>

                                {{-- Job & Company Tags --}}
                                <span class="wa-token-pill" data-token="[{JOB_TITLE}]" data-cat="job" title="Target Job Role">
                                    <i class="fas fa-plus"></i> [{JOB_TITLE}]
                                </span>
                                <span class="wa-token-pill" data-token="[{COMPANY_NAME}]" data-cat="job" title="Hiring / Service Company Name">
                                    <i class="fas fa-plus"></i> [{COMPANY_NAME}]
                                </span>
                                <span class="wa-token-pill" data-token="[{CLIENT_NAME}]" data-cat="job" title="End Client Name">
                                    <i class="fas fa-plus"></i> [{CLIENT_NAME}]
                                </span>
                                <span class="wa-token-pill" data-token="[{WORK_LOCATION}]" data-cat="job" title="Job Work Location">
                                    <i class="fas fa-plus"></i> [{WORK_LOCATION}]
                                </span>
                                <span class="wa-token-pill" data-token="[{COMPANY_WEBSITE}]" data-cat="job" title="Company Website URL">
                                    <i class="fas fa-plus"></i> [{COMPANY_WEBSITE}]
                                </span>
                                <span class="wa-token-pill" data-token="[{CLIENT_WEBSITE}]" data-cat="job" title="Client Website URL">
                                    <i class="fas fa-plus"></i> [{CLIENT_WEBSITE}]
                                </span>

                                {{-- Interview Tags --}}
                                <span class="wa-token-pill" data-token="[{INTERVIEW_DATE_TIME}]" data-cat="interview" title="Scheduled Interview Date & Time">
                                    <i class="fas fa-plus"></i> [{INTERVIEW_DATE_TIME}]
                                </span>
                                <span class="wa-token-pill" data-token="[{INTERVIEW_MODE}]" data-cat="interview" title="Video / Telephonic / F2F">
                                    <i class="fas fa-plus"></i> [{INTERVIEW_MODE}]
                                </span>
                                <span class="wa-token-pill" data-token="[{INTERVIEW_LINK_URL}]" data-cat="interview" title="Meeting Join Link (Google Meet / Teams)">
                                    <i class="fas fa-plus"></i> [{INTERVIEW_LINK_URL}]
                                </span>
                                <span class="wa-token-pill" data-token="[{INTERVIEW_QUESTIONS}]" data-cat="interview" title="Screening & Technical Questions">
                                    <i class="fas fa-plus"></i> [{INTERVIEW_QUESTIONS}]
                                </span>

                                {{-- Offer & Compensation Tags --}}
                                <span class="wa-token-pill" data-token="[{JOINING_DATE}]" data-cat="offer" title="Candidate Joining Date">
                                    <i class="fas fa-plus"></i> [{JOINING_DATE}]
                                </span>
                                <span class="wa-token-pill" data-token="[{SALARY_AMOUNT}]" data-cat="offer" title="Offered Salary Amount">
                                    <i class="fas fa-plus"></i> [{SALARY_AMOUNT}]
                                </span>
                                <span class="wa-token-pill" data-token="[{SALARY_MODE}]" data-cat="offer" title="Per Annum / Monthly / Hourly">
                                    <i class="fas fa-plus"></i> [{SALARY_MODE}]
                                </span>
                                <span class="wa-token-pill" data-token="[{CURRENCY}]" data-cat="offer" title="Currency Symbol / ISO">
                                    <i class="fas fa-plus"></i> [{CURRENCY}]
                                </span>
                                <span class="wa-token-pill" data-token="[{JOB_TYPE}]" data-cat="offer" title="Full-time / Contract">
                                    <i class="fas fa-plus"></i> [{JOB_TYPE}]
                                </span>
                                <span class="wa-token-pill" data-token="[{OFFER_LETTER_LINK}]" data-cat="offer" title="Candidate View & Sign Link">
                                    <i class="fas fa-plus"></i> [{OFFER_LETTER_LINK}]
                                </span>

                                {{-- Recruiter / Sender Tags --}}
                                <span class="wa-token-pill" data-token="[{HR_NAME}]" data-cat="recruiter" title="Recruiter Full Name">
                                    <i class="fas fa-plus"></i> [{HR_NAME}]
                                </span>
                                <span class="wa-token-pill" data-token="[{HR_PHONE}]" data-cat="recruiter" title="Recruiter Mobile / WhatsApp">
                                    <i class="fas fa-plus"></i> [{HR_PHONE}]
                                </span>
                                <span class="wa-token-pill" data-token="[{HR_EMAIL}]" data-cat="recruiter" title="Recruiter Email Address">
                                    <i class="fas fa-plus"></i> [{HR_EMAIL}]
                                </span>
                                <span class="wa-token-pill" data-token="[{POSITION}]" data-cat="recruiter" title="Recruiter Designation">
                                    <i class="fas fa-plus"></i> [{POSITION}]
                                </span>
                            </div>
                        </div>

                        {{-- Sub-Card 3: Dedicated WhatsApp Message Composer --}}
                        <div class="wa-subcard">
                            <div class="wa-subcard-header">
                                <h2 class="wa-subcard-title">
                                    <i class="fab fa-whatsapp"></i> WhatsApp Message Content & Markdown Composer <span class="req">*</span>
                                </h2>
                                <span class="badge badge-light-success fs-8 fw-semibold">WhatsApp Markdown Supported</span>
                            </div>

                            <div class="wa-composer-wrap">
                                {{-- WhatsApp Formatting Toolbar Strip --}}
                                <div class="wa-composer-toolbar">
                                    <div class="wa-tb-left">
                                        <button type="button" class="wa-tool-btn" id="btnFmtBold" title="Bold (*text*)">
                                            <strong>B</strong>
                                        </button>
                                        <button type="button" class="wa-tool-btn" id="btnFmtItalic" title="Italic (_text_)">
                                            <em>I</em>
                                        </button>
                                        <button type="button" class="wa-tool-btn" id="btnFmtStrike" title="Strikethrough (~text~)">
                                            <s>S</s>
                                        </button>
                                        <button type="button" class="wa-tool-btn" id="btnFmtMono" title="Monospace (```text```)">
                                            <i class="fas fa-code fs-9"></i> Mono
                                        </button>
                                        <span class="wa-tb-separator"></span>
                                        <button type="button" class="wa-tool-btn" id="btnFmtBullet" title="Bullet Point (•)">
                                            <i class="fas fa-list-ul fs-9"></i> Bullet
                                        </button>

                                        {{-- Quick Emojis Dropdown --}}
                                        <div class="dropdown d-inline-block">
                                            <button type="button" class="wa-tool-btn dropdown-toggle" id="btnEmojiDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Insert Emoji">
                                                <span>😊 Emoji</span>
                                            </button>
                                            <div class="dropdown-menu p-2 fs-6" style="min-width: 220px;" aria-labelledby="btnEmojiDropdown">
                                                <div class="d-flex flex-wrap gap-2 justify-content-center">
                                                    @foreach(['👋','💼','📍','📅','✉️','📞','✅','⚠️','🏢','🎯','🚀','💡','💰','🕒','🤝','👍','🙏','🔥','🎉','⭐'] as $emoji)
                                                        <span class="fs-4 p-1 rounded hover-bg-light cursor-pointer wa-emoji-btn" data-emoji="{{ $emoji }}">{{ $emoji }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="wa-tb-right">
                                        <button type="button" class="wa-tool-btn text-danger" id="btnClearComposer" title="Clear message text">
                                            <i class="far fa-trash-alt fs-9"></i> Clear
                                        </button>
                                    </div>
                                </div>

                                {{-- Main Textarea --}}
                                <textarea class="wa-textarea-main" id="editor1" name="editor1" 
                                          placeholder="Type your WhatsApp message template here... Use *bold*, _italics_, and click variable pills above to insert dynamic tags." required>{{ $cleanMessage }}</textarea>

                                {{-- Metrics Footer Strip --}}
                                <div class="wa-composer-metrics-bar">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="wa-metric-tag" id="waCharCounter">
                                            <i class="fas fa-text-width text-muted fs-9"></i> Characters: 0 / 4,096
                                        </span>
                                        <span class="wa-metric-tag" id="waReadTime">
                                            <i class="far fa-clock text-success fs-9"></i> Est. Read: ~0s
                                        </span>
                                    </div>
                                    <span class="wa-metric-tag text-primary" id="waVarsCount">
                                        <i class="fas fa-tags fs-9"></i> 0 Variables Detected
                                    </span>
                                </div>
                            </div>

                            <span class="wa-form-hint mt-2">
                                Tip: Recruiters can preview this template with candidate information before sending via Evolution API / WhatsApp Business.
                            </span>
                        </div>

                    </div>

                    {{-- ── RIGHT COLUMN: Interactive Smartphone Device Simulator (5 Cols) ── --}}
                    <div class="wa-simulator-col">
                        <div class="wa-simulator-subcard">
                            <div class="wa-subcard-header">
                                <h2 class="wa-subcard-title">
                                    <i class="fas fa-mobile-alt"></i> Live Smartphone Simulator
                                </h2>
                                <span class="badge badge-light-success fs-8 fw-semibold">Real-Time Sync</span>
                            </div>

                            {{-- Phone Frame --}}
                            <div class="wa-phone-container">
                                <div class="wa-phone-speaker-strip">
                                    <div class="wa-phone-speaker"></div>
                                </div>

                                {{-- WhatsApp Topbar --}}
                                <div class="wa-sim-topbar">
                                    <div class="wa-sim-header-left">
                                        <span class="fs-8">&#8592;</span>
                                        <div class="wa-sim-avatar">
                                            <i class="fas fa-user fs-9"></i>
                                        </div>
                                        <div class="wa-sim-contact-info">
                                            <div class="wa-sim-contact-name" id="simContactName">Alex Morgan</div>
                                            <div class="wa-sim-contact-status">online</div>
                                        </div>
                                    </div>
                                    <div class="wa-sim-header-actions">
                                        <i class="fas fa-video"></i>
                                        <i class="fas fa-phone-alt"></i>
                                        <i class="fas fa-ellipsis-v"></i>
                                    </div>
                                </div>

                                {{-- Chat Canvas Body --}}
                                <div class="wa-sim-chat-canvas">
                                    <div class="wa-sim-bubble">
                                        <div id="simMessageText">Type a message on the left to see live preview...</div>
                                        <div class="wa-sim-bubble-time">
                                            <span>{{ now()->format('g:i A') }}</span>
                                            <svg width="13" height="9" viewBox="0 0 16 11" fill="none"><path d="M1 6l3.5 3.5L9 4M6 6l3.5 3.5L15 2" stroke="#3b82f6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Simulator Mode Toggles --}}
                            <div class="wa-sim-controls-strip">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary active" id="btnSimSampleMode">
                                        <i class="fas fa-magic fs-9"></i> Sample Candidate
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btnSimRawMode">
                                        <i class="fas fa-code fs-9"></i> Raw Variables
                                    </button>
                                </div>
                                <button type="button" class="btn btn-sm btn-light-success" id="btnSimCopy">
                                    <i class="far fa-copy fs-9"></i> Copy Text
                                </button>
                            </div>

                            <div class="mt-3 text-center">
                                <small class="text-muted fs-8">
                                    Simulation renders exact WhatsApp styling, line breaks, and emojis.
                                </small>
                            </div>
                        </div>
                    </div>

                </div>
            </form>

        </div>
    </div>
@endsection

@push('footer-script')
    <script>
        $(document).ready(function() {
            var $editor = $('#editor1');
            var samplePreviewActive = true;

            // Sample Dictionary for live preview
            var sampleDictionary = {
                '[{CANDIDATE_NAME}]': 'Alex Morgan',
                '[{CANDIDATE_PHONE}]': '+91 98765 12345',
                '[{CANDIDATE_EMAIL}]': 'alex.morgan@example.com',
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
                '[{OFFER_LETTER_LINK}]': 'https://jero.works/offer/view/token-9982',
                '[{POSITION}]': 'Lead Technical Recruiter'
            };

            // ── Live 2-Way Sync & Metrics Calculation ──
            function updateSimulationAndMetrics() {
                var text = $editor.val() || '';
                var charLen = text.length;

                // 1. Update Character Counter & Limit (WhatsApp standard 4096)
                $('#waCharCounter').html('<i class="fas fa-text-width text-muted fs-9"></i> Characters: <strong>' + charLen + '</strong> / 4,096');
                if (charLen > 4096) {
                    $('#waCharCounter').addClass('text-danger');
                } else {
                    $('#waCharCounter').removeClass('text-danger');
                }

                // 2. Estimated Reading Time
                var words = text.trim() ? text.trim().split(/\s+/).length : 0;
                var secs = Math.max(1, Math.round((words / 200) * 60));
                $('#waReadTime').html('<i class="far fa-clock text-success fs-9"></i> Est. Read: ~' + secs + 's');

                // 3. Count detected variables
                var matches = text.match(/\[\{[A-Z0-9_]+\}\]/gi) || [];
                var uniqueVars = Array.from(new Set(matches));
                $('#waVarsCount').html('<i class="fas fa-tags fs-9"></i> ' + uniqueVars.length + ' Variables Detected');

                // 4. Render to Smartphone Simulator
                var previewText = text;
                if (samplePreviewActive) {
                    Object.keys(sampleDictionary).forEach(function(token) {
                        var escaped = token.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                        previewText = previewText.replace(new RegExp(escaped, 'gi'), sampleDictionary[token]);
                    });
                    $('#simContactName').text('Alex Morgan');
                } else {
                    $('#simContactName').text('Candidate (Raw Tags)');
                }

                $('#simMessageText').text(previewText || 'Type a message on the left to see live preview...');
            }

            $editor.on('input propertychange', updateSimulationAndMetrics);
            updateSimulationAndMetrics();

            // ── Variable Tag Click-to-Insert at Cursor ──
            $('.wa-token-pill').on('click', function() {
                var token = $(this).attr('data-token');
                insertAtCursor($editor[0], token + ' ');
                updateSimulationAndMetrics();
                toastr.options = { "timeOut": "1200", "positionClass": "toastr-bottom-right" };
                toastr.info('Inserted ' + token);
            });

            // Variable Category Filter Tabs
            $('.wa-var-tab').on('click', function() {
                $('.wa-var-tab').removeClass('active');
                $(this).addClass('active');
                var vcat = $(this).attr('data-vcat');

                if (vcat === 'all') {
                    $('.wa-token-pill').show();
                } else {
                    $('.wa-token-pill').each(function() {
                        if ($(this).attr('data-cat') === vcat) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                }
            });

            // ── Formatting Toolbar Helpers ──
            function wrapSelection(openTag, closeTag) {
                var el = $editor[0];
                var start = el.selectionStart;
                var end = el.selectionEnd;
                var sel = el.value.substring(start, end);
                var replacement = openTag + (sel || 'text') + closeTag;
                el.value = el.value.substring(0, start) + replacement + el.value.substring(end);
                el.selectionStart = start + openTag.length;
                el.selectionEnd = start + openTag.length + (sel.length || 4);
                el.focus();
                updateSimulationAndMetrics();
            }

            function insertAtCursor(el, str) {
                var start = el.selectionStart;
                var end = el.selectionEnd;
                el.value = el.value.substring(0, start) + str + el.value.substring(end);
                el.selectionStart = el.selectionEnd = start + str.length;
                el.focus();
            }

            $('#btnFmtBold').on('click', function() { wrapSelection('*', '*'); });
            $('#btnFmtItalic').on('click', function() { wrapSelection('_', '_'); });
            $('#btnFmtStrike').on('click', function() { wrapSelection('~', '~'); });
            $('#btnFmtMono').on('click', function() { wrapSelection('```', '```'); });
            $('#btnFmtBullet').on('click', function() { insertAtCursor($editor[0], '• '); updateSimulationAndMetrics(); });

            $('.wa-emoji-btn').on('click', function() {
                var emoji = $(this).attr('data-emoji');
                insertAtCursor($editor[0], emoji + ' ');
                updateSimulationAndMetrics();
            });

            $('#btnClearComposer').on('click', function() {
                Swal.fire({
                    title: 'Clear Message Body?',
                    text: "This will remove all text from the composer.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, clear it',
                    cancelButtonText: 'Cancel',
                    customClass: { confirmButton: 'btn btn-danger', cancelButton: 'btn btn-light' }
                }).then(function(result) {
                    if (result.isConfirmed) {
                        $editor.val('').focus();
                        updateSimulationAndMetrics();
                    }
                });
            });

            // ── Smartphone Simulation Mode Toggles ──
            $('#btnSimSampleMode').on('click', function() {
                samplePreviewActive = true;
                $('#btnSimSampleMode').addClass('active');
                $('#btnSimRawMode').removeClass('active');
                updateSimulationAndMetrics();
            });

            $('#btnSimRawMode').on('click', function() {
                samplePreviewActive = false;
                $('#btnSimRawMode').addClass('active');
                $('#btnSimSampleMode').removeClass('active');
                updateSimulationAndMetrics();
            });

            $('#btnSimCopy').on('click', function() {
                var textToCopy = $('#simMessageText').text();
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(textToCopy).then(function() {
                        toastr.success('Preview text copied to clipboard!');
                    });
                } else {
                    var $temp = $('<textarea>');
                    $('body').append($temp);
                    $temp.val(textToCopy).select();
                    document.execCommand('copy');
                    $temp.remove();
                    toastr.success('Preview text copied to clipboard!');
                }
            });

            // ── AJAX Form Submission ──
            $('#btnSaveTemplate').on('click', function(e) {
                e.preventDefault();

                var templateName = $('#template').val().trim();
                var messageContent = $editor.val().trim();

                if (!templateName) {
                    toastr.error('Please enter the Template Name.');
                    $('#template').focus();
                    return;
                }

                if (!messageContent) {
                    toastr.error('Please enter the WhatsApp message content.');
                    $editor.focus();
                    return;
                }

                var $btn = $('#btnSaveTemplate');
                var $btnText = $('#btnSaveText');
                var $btnSpinner = $('#btnSaveSpinner');

                $btn.prop('disabled', true);
                $btnText.text('Saving & Syncing...');
                $btnSpinner.removeClass('d-none');

                var formData = $('#waTemplateForm').serialize();

                $.ajax({
                    url: "{{ route('superadmin.company.addEditwhatsappThemesupdate') }}",
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        $btn.prop('disabled', false);
                        $btnText.text('Save & Sync Template');
                        $btnSpinner.addClass('d-none');

                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Template Saved Successfully!',
                                text: response.message || 'WhatsApp template and Elasticsearch indices synchronized.',
                                confirmButtonText: 'Return to Templates',
                                customClass: { confirmButton: 'btn btn-primary' }
                            }).then(function() {
                                window.location.href = "{{ route('superadmin.company.whatsappthemes') }}";
                            });
                        } else {
                            toastr.error(response.message || 'Failed to save template.');
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false);
                        $btnText.text('Save & Sync Template');
                        $btnSpinner.addClass('d-none');

                        var errorMsg = 'Failed to save template. Please review form inputs.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        toastr.error(errorMsg);
                    }
                });
            });

        });
    </script>
@endpush
