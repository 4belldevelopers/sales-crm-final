@extends('layouts.service_company_layout')

@push('head-script')
    <link href="{{ asset('demo/v7/css/plugins.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
    <link href="{{ asset('demo/v7/css/style.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ===== Design Tokens (Aligned with Service Company Form & Rule 20/26/27) ===== */
        :root {
            --prod-primary: #2563eb;
            --prod-primary-hover: #1d4ed8;
            --prod-primary-light: #eff6ff;
            --prod-primary-border: #bfdbfe;
            --prod-bg: #f8fafc;
            --prod-header-bg: #f8fafc;
            --prod-card-bg: #ffffff;
            --prod-border: #e2e8f0;
            --prod-border-focus: #2563eb;
            --prod-text: #0f172a;
            --prod-text-secondary: #475569;
            --prod-text-muted: #64748b;
            --prod-danger: #dc2626;
            --prod-danger-bg: #fef2f2;
            --prod-danger-border: #fecaca;
            --prod-success: #059669;
            --prod-success-bg: #ecfdf5;
            --prod-success-border: #a7f3d0;
            --prod-warning: #d97706;
            --prod-warning-bg: #fffbeb;
            --prod-warning-border: #fde68a;
            --prod-radius: 10px;
            --prod-radius-sm: 6px;
            --prod-radius-lg: 12px;
            --prod-ease: cubic-bezier(0.22, 1, 0.36, 1);
        }

        /* ===== Strict Page Isolation (#prodwrap) ===== */
        #prodwrap *, #prodwrap *::before, #prodwrap *::after { box-sizing: border-box; }
        #prodwrap {
            font-family: 'Poppins', sans-serif;
            color: var(--prod-text);
            font-size: 13px;
            line-height: 1.5;
            max-width: 100%;
            overflow-x: hidden;
        }
        #prodwrap p, #prodwrap h1, #prodwrap h2, #prodwrap h3, #prodwrap h4 { margin: 0; }

        #prodwrap .prod-container {
            padding: 8px 20px 88px;
            max-width: 1440px;
            margin: 0 auto;
        }

        /* ===== Tier 1: Top Header & Section Jump Toolbar (Rule 20) ===== */
        #prodwrap .prod-toolbar-card {
            background: var(--prod-header-bg);
            border: 1px solid var(--prod-border);
            border-radius: var(--prod-radius);
            padding: 16px 20px;
            margin-bottom: 16px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        #prodwrap .prod-title-group {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        #prodwrap .prod-title-icon {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            background: var(--prod-primary-light);
            border: 1px solid var(--prod-primary-border);
            color: var(--prod-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }
        #prodwrap .prod-page-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--prod-text);
        }
        #prodwrap .prod-page-subtitle {
            font-size: 12px;
            color: var(--prod-text-muted);
        }
        #prodwrap .prod-toolbar-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        #prodwrap .prod-type-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            background: #ffffff;
            border: 1px solid var(--prod-border);
            color: var(--prod-text-secondary);
        }
        #prodwrap .prod-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            background: var(--prod-warning-bg);
            border: 1px solid var(--prod-warning-border);
            color: var(--prod-warning);
        }

        /* ===== Tier 2: Section Navigation Strip (Rule 20) ===== */
        #prodwrap .prod-nav-card {
            background: var(--prod-card-bg);
            border: 1px solid var(--prod-border);
            border-radius: var(--prod-radius);
            padding: 8px;
            margin-bottom: 16px;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            position: sticky;
            top: 64px;
            z-index: 20;
        }
        #prodwrap .prod-nav-tab {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 500;
            color: var(--prod-text-secondary);
            background: transparent;
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 180ms var(--prod-ease), color 180ms var(--prod-ease), border-color 180ms var(--prod-ease);
        }
        #prodwrap .prod-nav-tab:hover {
            background: var(--prod-bg);
            color: var(--prod-text);
        }
        #prodwrap .prod-nav-tab.is-active {
            background: var(--prod-primary-light);
            color: var(--prod-primary);
            border-color: var(--prod-primary-border);
            font-weight: 600;
        }
        #prodwrap .prod-nav-num {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--prod-bg);
            border: 1px solid var(--prod-border);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 600;
            font-variant-numeric: tabular-nums;
        }
        #prodwrap .prod-nav-tab.is-active .prod-nav-num {
            background: var(--prod-primary);
            border-color: var(--prod-primary);
            color: #ffffff;
        }

        /* ===== Section Cards with Mandatory Header Background (Rule 20) ===== */
        #prodwrap .prod-card {
            background: var(--prod-card-bg);
            border: 1px solid var(--prod-border);
            border-radius: var(--prod-radius-lg);
            margin-bottom: 16px;
            overflow: hidden;
            scroll-margin-top: 130px;
        }
        #prodwrap .prod-card-header {
            background: var(--prod-header-bg);
            border-bottom: 1px solid var(--prod-border);
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        #prodwrap .prod-card-header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        #prodwrap .prod-card-icon {
            width: 30px;
            height: 30px;
            border-radius: 7px;
            background: #ffffff;
            border: 1px solid var(--prod-border);
            color: var(--prod-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
        }
        #prodwrap .prod-card-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--prod-text);
            letter-spacing: 0.2px;
        }
        #prodwrap .prod-card-desc {
            font-size: 11px;
            color: var(--prod-text-muted);
        }
        #prodwrap .prod-card-body {
            padding: 20px;
        }

        /* ===== Responsive 2-Column & 3-Column Form Grid ===== */
        #prodwrap .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px 20px;
        }
        #prodwrap .form-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px 20px;
        }
        #prodwrap .form-group-cus {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        #prodwrap .form-group-cus.full-width {
            grid-column: 1 / -1;
        }
        #prodwrap .form-label {
            font-size: 12px;
            font-weight: 500;
            color: var(--prod-text);
            display: flex;
            align-items: center;
            gap: 5px;
            margin: 0;
        }
        #prodwrap .required::after {
            content: '*';
            color: var(--prod-danger);
            margin-left: 2px;
            font-weight: 600;
        }
        #prodwrap .form-hint {
            font-size: 11px;
            color: var(--prod-text-muted);
        }
        #prodwrap .form-error {
            font-size: 11px;
            color: var(--prod-danger);
            display: none;
            align-items: center;
            gap: 4px;
            margin-top: 2px;
        }
        #prodwrap .form-group-cus.has-error .form-error {
            display: flex;
        }
        #prodwrap .form-group-cus.has-error .form-input,
        #prodwrap .form-group-cus.has-error .form-select,
        #prodwrap .form-group-cus.has-error .form-textarea {
            border-color: var(--prod-danger);
            background-color: var(--prod-danger-bg);
        }

        #prodwrap .form-input,
        #prodwrap .form-select,
        #prodwrap .form-textarea {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid var(--prod-border);
            border-radius: var(--prod-radius-sm);
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            color: var(--prod-text);
            background-color: #ffffff;
            transition: border-color 160ms var(--prod-ease), box-shadow 160ms var(--prod-ease);
            outline: none;
        }
        #prodwrap .form-input[type="number"] {
            font-variant-numeric: tabular-nums;
        }
        #prodwrap .form-textarea {
            min-height: 84px;
            resize: vertical;
        }
        #prodwrap .form-input:focus,
        #prodwrap .form-select:focus,
        #prodwrap .form-textarea:focus {
            border-color: var(--prod-border-focus);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }
        #prodwrap .form-input[readonly] {
            background-color: var(--prod-bg);
            color: var(--prod-text-secondary);
            cursor: default;
        }

        /* Input group with action button or prefix */
        #prodwrap .input-group-inline {
            display: flex;
            align-items: stretch;
            gap: 8px;
        }
        #prodwrap .btn-inline-action {
            padding: 0 12px;
            border: 1px solid var(--prod-border);
            border-radius: var(--prod-radius-sm);
            background: var(--prod-bg);
            color: var(--prod-text-secondary);
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background-color 160ms var(--prod-ease), border-color 160ms var(--prod-ease);
        }
        #prodwrap .btn-inline-action:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: var(--prod-text);
        }

        /* Product Type Segmented Control */
        #prodwrap .type-segmented {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
        }
        #prodwrap .type-option {
            border: 1px solid var(--prod-border);
            border-radius: var(--prod-radius-sm);
            padding: 9px 10px;
            background: #ffffff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 500;
            color: var(--prod-text-secondary);
            transition: background-color 160ms var(--prod-ease), border-color 160ms var(--prod-ease), color 160ms var(--prod-ease);
        }
        #prodwrap .type-option:hover {
            background: var(--prod-bg);
        }
        #prodwrap .type-option.is-selected {
            background: var(--prod-primary-light);
            border-color: var(--prod-primary);
            color: var(--prod-primary);
            font-weight: 600;
        }

        /* Rich Text Editor (reused from jobs/create) */
        #prodwrap .editor-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 4px;
            padding: 8px 10px;
            background: var(--prod-header-bg);
            border: 1px solid var(--prod-border);
            border-bottom: none;
            border-radius: var(--prod-radius-sm) var(--prod-radius-sm) 0 0;
        }
        #prodwrap .toolbar-btn {
            width: 28px;
            height: 28px;
            border: 1px solid transparent;
            background: transparent;
            border-radius: 5px;
            color: var(--prod-text-secondary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
        }
        #prodwrap .toolbar-btn:hover {
            background: #e2e8f0;
            color: var(--prod-text);
        }
        #prodwrap .toolbar-divider {
            width: 1px;
            height: 18px;
            background: var(--prod-border);
            margin: 0 4px;
        }
        #prodwrap .rich-editor {
            min-height: 120px;
            max-height: 260px;
            overflow-y: auto;
            padding: 12px;
            border: 1px solid var(--prod-border);
            border-radius: 0 0 var(--prod-radius-sm) var(--prod-radius-sm);
            background: #ffffff;
            font-size: 13px;
            outline: none;
        }
        #prodwrap .rich-editor:focus {
            border-color: var(--prod-border-focus);
        }
        #prodwrap .rich-editor:empty::before {
            content: attr(data-placeholder);
            color: var(--prod-text-muted);
        }

        /* Image Upload & Preview */
        #prodwrap .image-upload-grid {
            display: grid;
            grid-template-columns: 160px 1fr;
            gap: 16px;
            align-items: center;
        }
        #prodwrap .image-preview-box {
            width: 160px;
            height: 120px;
            border: 1px dashed #cbd5e1;
            border-radius: var(--prod-radius);
            background: var(--prod-bg);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
            color: var(--prod-text-muted);
            font-size: 11px;
            text-align: center;
            padding: 8px;
        }
        #prodwrap .image-preview-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }
        #prodwrap .image-preview-box.has-image img {
            display: block;
        }
        #prodwrap .image-preview-box.has-image .image-empty-state {
            display: none;
        }
        #prodwrap .image-dropzone {
            border: 1px dashed #cbd5e1;
            border-radius: var(--prod-radius);
            padding: 16px;
            background: var(--prod-bg);
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
            cursor: pointer;
            transition: border-color 160ms var(--prod-ease), background-color 160ms var(--prod-ease);
        }
        #prodwrap .image-dropzone:hover,
        #prodwrap .image-dropzone.is-dragover {
            border-color: var(--prod-primary);
            background: var(--prod-primary-light);
        }

        /* Calculated Pricing Summary Sub-Cards */
        #prodwrap .pricing-summary-strip {
            margin-top: 16px;
            padding: 14px 16px;
            border-radius: var(--prod-radius);
            background: var(--prod-bg);
            border: 1px solid var(--prod-border);
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
        }
        #prodwrap .pricing-metric {
            background: #ffffff;
            border: 1px solid var(--prod-border);
            border-radius: var(--prod-radius-sm);
            padding: 10px 12px;
        }
        #prodwrap .pricing-metric-label {
            font-size: 11px;
            color: var(--prod-text-muted);
            font-weight: 500;
        }
        #prodwrap .pricing-metric-value {
            font-size: 16px;
            font-weight: 600;
            color: var(--prod-text);
            font-variant-numeric: tabular-nums;
            margin-top: 2px;
        }
        #prodwrap .pricing-metric.is-highlight {
            background: var(--prod-primary-light);
            border-color: var(--prod-primary-border);
        }
        #prodwrap .pricing-metric.is-highlight .pricing-metric-value {
            color: var(--prod-primary);
        }
        #prodwrap .pricing-metric.is-profit-positive .pricing-metric-value {
            color: var(--prod-success);
        }
        #prodwrap .pricing-metric.is-profit-negative .pricing-metric-value {
            color: var(--prod-danger);
        }

        /* Conditional Info Banner */
        #prodwrap .conditional-banner {
            display: none;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 16px;
            border-radius: var(--prod-radius-sm);
            background: var(--prod-primary-light);
            border: 1px solid var(--prod-primary-border);
            color: #1e40af;
            font-size: 12px;
            margin-bottom: 14px;
        }
        #prodwrap .conditional-banner.is-visible {
            display: flex;
        }

        /* Specifications Key-Value Builder */
        #prodwrap .spec-row {
            display: grid;
            grid-template-columns: 1fr 1fr 36px;
            gap: 10px;
            margin-bottom: 8px;
            align-items: center;
        }
        #prodwrap .btn-icon-remove {
            width: 34px;
            height: 36px;
            border-radius: var(--prod-radius-sm);
            border: 1px solid var(--prod-border);
            background: #ffffff;
            color: var(--prod-text-muted);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        #prodwrap .btn-icon-remove:hover {
            background: var(--prod-danger-bg);
            border-color: var(--prod-danger-border);
            color: var(--prod-danger);
        }

        /* Product Tags Chip Input */
        #prodwrap .tags-box {
            border: 1px solid var(--prod-border);
            border-radius: var(--prod-radius-sm);
            padding: 8px 10px;
            background: #ffffff;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            align-items: center;
            min-height: 42px;
        }
        #prodwrap .tags-box:focus-within {
            border-color: var(--prod-border-focus);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }
        #prodwrap .tag-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            background: var(--prod-primary-light);
            border: 1px solid var(--prod-primary-border);
            color: var(--prod-primary);
            font-size: 12px;
            font-weight: 500;
        }
        #prodwrap .tag-chip button {
            background: none;
            border: none;
            color: var(--prod-primary);
            cursor: pointer;
            padding: 0;
            font-size: 13px;
            line-height: 1;
        }
        #prodwrap .tag-input-bare {
            border: none;
            outline: none;
            flex: 1;
            min-width: 160px;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            color: var(--prod-text);
        }

        /* Featured Radio Pill */
        #prodwrap .featured-toggle {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        /* Sticky Bottom Action Bar */
        #prodwrap .prod-sticky-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #ffffff;
            border-top: 1px solid var(--prod-border);
            padding: 12px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            z-index: 30;
        }
        #prodwrap .prod-footer-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        #prodwrap .btn-prod-secondary {
            padding: 9px 16px;
            border-radius: var(--prod-radius-sm);
            border: 1px solid var(--prod-border);
            background: #ffffff;
            color: var(--prod-text);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            transition: background-color 160ms var(--prod-ease);
        }
        #prodwrap .btn-prod-secondary:hover {
            background: var(--prod-bg);
        }
        #prodwrap .btn-prod-primary {
            padding: 9px 18px;
            border-radius: var(--prod-radius-sm);
            border: 1px solid var(--prod-primary);
            background: var(--prod-primary);
            color: #ffffff;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            transition: background-color 160ms var(--prod-ease);
        }
        #prodwrap .btn-prod-primary:hover {
            background: var(--prod-primary-hover);
        }

        /* Responsive 15.6" (1366x768) & Mobile */
        @media (max-width: 1199px) {
            #prodwrap .pricing-summary-strip {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width: 768px) {
            #prodwrap .form-grid,
            #prodwrap .form-grid-3,
            #prodwrap .image-upload-grid {
                grid-template-columns: 1fr;
            }
            #prodwrap .prod-nav-card {
                position: static;
            }
            #prodwrap .prod-sticky-footer {
                flex-direction: column;
                align-items: stretch;
            }
        }
        @media (prefers-reduced-motion: reduce) {
            #prodwrap * {
                transition: none !important;
                animation: none !important;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $currencyList = $currency ?? [];
        $defaultCurrencyCode = $product->currency ?? ($currency_code ?? 'INR');
        $isEdit = ($page_event ?? 'ADD') === 'EDIT' && isset($product);
        $categoryList = $categories ?? collect();
        $subcategoryList = $subcategories ?? collect();
    @endphp

    <div id="prodwrap">
        <div class="prod-container">
            {{-- Tier 1: Page Header Card --}}
            <div class="prod-toolbar-card">
                <div class="prod-title-group">
                    <div class="prod-title-icon">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <div>
                        <h1 class="prod-page-title">Add New Product</h1>
                        <p class="prod-page-subtitle">Create a Sales CRM product record for Deals, Quotations, Sales Orders, and Invoices</p>
                    </div>
                </div>
                <div class="prod-toolbar-meta">
                    <span class="prod-type-pill" id="headerTypeBadge">
                        <i class="fas fa-cube text-primary"></i>
                        <span>Type: <strong id="headerTypeLabel">Physical</strong></span>
                    </span>
                    <span class="prod-status-badge" id="headerStatusBadge">
                        <i class="fas fa-circle" style="font-size: 7px;"></i>
                        <span id="headerStatusLabel">Draft</span>
                    </span>
                </div>
            </div>

            {{-- Tier 2: Section Jump Navigation --}}
            <div class="prod-nav-card" id="prodSectionNav">
                <a href="#section-basic" class="prod-nav-tab is-active" data-target="section-basic">
                    <span class="prod-nav-num">1</span>
                    <span>Basic Information</span>
                </a>
                <a href="#section-pricing" class="prod-nav-tab" data-target="section-pricing">
                    <span class="prod-nav-num">2</span>
                    <span>Pricing &amp; Tax</span>
                </a>
                <a href="#section-inventory" class="prod-nav-tab" data-target="section-inventory">
                    <span class="prod-nav-num">3</span>
                    <span>Inventory</span>
                </a>
                <a href="#section-specs" class="prod-nav-tab" data-target="section-specs">
                    <span class="prod-nav-num">4</span>
                    <span>Specifications</span>
                </a>
                <a href="#section-sales" class="prod-nav-tab" data-target="section-sales">
                    <span class="prod-nav-num">5</span>
                    <span>Sales Configuration</span>
                </a>
            </div>

            <form id="productCreateForm" method="POST" action="javascript:void(0);" enctype="multipart/form-data" novalidate>
                @csrf
                <input type="hidden" name="submit_action" id="submit_action" value="active">

                {{-- ================= SECTION 1: BASIC PRODUCT INFORMATION ================= --}}
                <div class="prod-card" id="section-basic">
                    <div class="prod-card-header">
                        <div class="prod-card-header-left">
                            <div class="prod-card-icon"><i class="fas fa-tag"></i></div>
                            <div>
                                <div class="prod-card-title">1. Basic Product Information</div>
                                <div class="prod-card-desc">Core identity, classification, brand, description, and media</div>
                            </div>
                        </div>
                    </div>
                    <div class="prod-card-body">
                        <div class="form-grid">
                            {{-- Product Name * --}}
                            <div class="form-group-cus" data-field="name">
                                <label class="form-label required" for="product_name">Product Name</label>
                                <input type="text" class="form-input" id="product_name" name="name" maxlength="255" placeholder="e.g. Enterprise CRM Annual License" required>
                                <span class="form-error"><i class="fas fa-exclamation-circle"></i> Product Name is required.</span>
                            </div>

                            {{-- Product SKU / Code * --}}
                            <div class="form-group-cus" data-field="sku">
                                <label class="form-label required" for="product_sku">Product SKU / Code</label>
                                <div class="input-group-inline">
                                    <input type="text" class="form-input" id="product_sku" name="sku" maxlength="100" placeholder="e.g. CRM-ENT-001" required style="text-transform: uppercase;">
                                    <button type="button" class="btn-inline-action" id="btnGenerateSku" title="Generate unique SKU">
                                        <i class="fas fa-magic"></i> Generate
                                    </button>
                                </div>
                                <span class="form-hint" id="skuStatusHint">Must be unique within your company catalog.</span>
                                <span class="form-error"><i class="fas fa-exclamation-circle"></i> <span id="skuErrorText">Product SKU / Code is required.</span></span>
                            </div>

                            {{-- Product Type --}}
                            <div class="form-group-cus full-width" data-field="product_type">
                                <label class="form-label required">Product Type</label>
                                <input type="hidden" name="product_type" id="product_type" value="physical">
                                <div class="type-segmented" id="productTypeSelector">
                                    <button type="button" class="type-option is-selected" data-type="physical">
                                        <i class="fas fa-box"></i> Physical Product
                                    </button>
                                    <button type="button" class="type-option" data-type="digital">
                                        <i class="fas fa-cloud-download-alt"></i> Digital Product
                                    </button>
                                    <button type="button" class="type-option" data-type="service">
                                        <i class="fas fa-concierge-bell"></i> Service
                                    </button>
                                </div>
                                <span class="form-hint">Controls applicable inventory and physical specification fields.</span>
                            </div>

                            {{-- Product Category * --}}
                            <div class="form-group-cus" data-field="category_id">
                                <label class="form-label required" for="product_category">Product Category</label>
                                <select class="form-select prod-select2" id="product_category" name="category_id" data-placeholder="Search or select category..." required>
                                    <option value=""></option>
                                    @foreach($categoryList as $cat)
                                        <option value="{{ $cat->id }}" {{ (int) ($product->category_id ?? 0) === (int) $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="form-error"><i class="fas fa-exclamation-circle"></i> Please select a Product Category.</span>
                            </div>

                            {{-- Product Subcategory --}}
                            <div class="form-group-cus" data-field="subcategory_id">
                                <label class="form-label" for="product_subcategory">Product Subcategory</label>
                                <select class="form-select prod-select2" id="product_subcategory" name="subcategory_id" data-placeholder="Select category first...">
                                    <option value=""></option>
                                    @foreach($subcategoryList as $sub)
                                        <option value="{{ $sub->id }}" {{ (int) ($product->subcategory_id ?? 0) === (int) $sub->id ? 'selected' : '' }}>
                                            {{ $sub->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Brand / Manufacturer --}}
                            <div class="form-group-cus" data-field="brand">
                                <label class="form-label" for="product_brand">Brand / Manufacturer</label>
                                <input type="text" class="form-input" id="product_brand" name="brand" maxlength="150" placeholder="e.g. Dell, Adobe, Internal">
                            </div>

                            {{-- Product Image --}}
                            <div class="form-group-cus full-width" data-field="image">
                                <label class="form-label" for="product_image">Product Image</label>
                                <div class="image-upload-grid">
                                    <div class="image-preview-box" id="imagePreviewBox">
                                        <img id="imagePreviewImg" src="" alt="Product Preview">
                                        <div class="image-empty-state">
                                            <i class="fas fa-image fa-2x mb-2" style="color: #94a3b8;"></i>
                                            <div>No image selected</div>
                                        </div>
                                    </div>
                                    <div class="image-dropzone" id="imageDropzone">
                                        <input type="file" id="product_image" name="image" accept="image/png,image/jpeg,image/webp" class="d-none">
                                        <div class="d-flex align-items-center gap-2">
                                            <button type="button" class="btn-inline-action" id="btnChooseImage">
                                                <i class="fas fa-upload"></i> Upload Image
                                            </button>
                                            <button type="button" class="btn-inline-action d-none" id="btnRemoveImage" style="color: var(--prod-danger);">
                                                <i class="fas fa-trash-alt"></i> Remove
                                            </button>
                                        </div>
                                        <span class="form-hint" id="imageFileMeta">Drag &amp; drop or click to upload PNG, JPG, or WEBP (max 2 MB).</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Product Description --}}
                            <div class="form-group-cus full-width" data-field="description">
                                <label class="form-label">Product Description</label>
                                <div class="editor-toolbar">
                                    <button type="button" class="toolbar-btn" data-cmd="bold" title="Bold"><i class="fas fa-bold"></i></button>
                                    <button type="button" class="toolbar-btn" data-cmd="italic" title="Italic"><i class="fas fa-italic"></i></button>
                                    <button type="button" class="toolbar-btn" data-cmd="underline" title="Underline"><i class="fas fa-underline"></i></button>
                                    <div class="toolbar-divider"></div>
                                    <button type="button" class="toolbar-btn" data-cmd="insertUnorderedList" title="Bullet List"><i class="fas fa-list-ul"></i></button>
                                    <button type="button" class="toolbar-btn" data-cmd="insertOrderedList" title="Numbered List"><i class="fas fa-list-ol"></i></button>
                                    <div class="toolbar-divider"></div>
                                    <button type="button" class="toolbar-btn" data-cmd="removeFormat" title="Clear Formatting"><i class="fas fa-eraser"></i></button>
                                </div>
                                <div class="rich-editor" id="productDescriptionEditor" contenteditable="true" data-placeholder="Describe key use cases, deliverables, or product overview..."></div>
                                <textarea name="description" id="product_description" class="d-none"></textarea>
                                <div class="d-flex justify-content-between">
                                    <span class="form-hint">Formatted description will appear on Quotations and Proposals.</span>
                                    <span class="form-hint" id="descCharCount">0 characters</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ================= SECTION 2: PRICING INFORMATION ================= --}}
                <div class="prod-card" id="section-pricing">
                    <div class="prod-card-header">
                        <div class="prod-card-header-left">
                            <div class="prod-card-icon"><i class="fas fa-rupee-sign"></i></div>
                            <div>
                                <div class="prod-card-title">2. Pricing Information</div>
                                <div class="prod-card-desc">Cost, selling price, discounts, GST/tax, and automatic margin calculations</div>
                            </div>
                        </div>
                    </div>
                    <div class="prod-card-body">
                        <div class="form-grid-3">
                            {{-- Currency (from GeneralMethod::currency()) --}}
                            <div class="form-group-cus" data-field="currency">
                                <label class="form-label required" for="product_currency">Currency</label>
                                <select class="form-select prod-select2" id="product_currency" name="currency" required>
                                    @foreach($currencyList as $cKey => $cCode)
                                        @php
                                            $cMeta = \App\Helper\GeneralMethod::currencyEnterpriseMeta($cCode);
                                            $isSelected = strtoupper((string) $cCode) === strtoupper((string) $defaultCurrencyCode);
                                        @endphp
                                        <option value="{{ $cCode }}"
                                                data-symbol="{{ $cMeta['symbol'] ?? $cCode }}"
                                                {{ $isSelected ? 'selected' : '' }}>
                                            {{ $cMeta['label'] ?? $cCode }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Purchase Price --}}
                            <div class="form-group-cus" data-field="purchase_price">
                                <label class="form-label" for="purchase_price">Purchase / Cost Price</label>
                                <input type="number" step="0.01" min="0" class="form-input calc-trigger" id="purchase_price" name="purchase_price" placeholder="0.00">
                                <span class="form-error"><i class="fas fa-exclamation-circle"></i> Cannot be negative.</span>
                            </div>

                            {{-- Selling Price * --}}
                            <div class="form-group-cus" data-field="selling_price">
                                <label class="form-label required" for="selling_price">Selling Price (Base)</label>
                                <input type="number" step="0.01" min="0" class="form-input calc-trigger" id="selling_price" name="selling_price" placeholder="0.00" required>
                                <span class="form-error"><i class="fas fa-exclamation-circle"></i> Valid Selling Price is required.</span>
                            </div>

                            {{-- MRP --}}
                            <div class="form-group-cus" data-field="mrp">
                                <label class="form-label" for="mrp">MRP (Maximum Retail Price)</label>
                                <input type="number" step="0.01" min="0" class="form-input calc-trigger" id="mrp" name="mrp" placeholder="0.00">
                            </div>

                            {{-- Discount Type --}}
                            <div class="form-group-cus" data-field="discount_type">
                                <label class="form-label" for="discount_type">Discount Type</label>
                                <select class="form-select calc-trigger" id="discount_type" name="discount_type">
                                    <option value="percentage">Percentage (%)</option>
                                    <option value="fixed">Fixed Amount</option>
                                </select>
                            </div>

                            {{-- Discount Value --}}
                            <div class="form-group-cus" data-field="discount_value">
                                <label class="form-label" for="discount_value">Discount Value</label>
                                <input type="number" step="0.01" min="0" class="form-input calc-trigger" id="discount_value" name="discount_value" placeholder="0.00" value="0">
                                <span class="form-error"><i class="fas fa-exclamation-circle"></i> Discount cannot exceed 100% or Selling Price.</span>
                            </div>

                            {{-- GST / Tax Percentage --}}
                            <div class="form-group-cus" data-field="tax_percentage">
                                <label class="form-label" for="tax_percentage">GST / Tax Percentage (%)</label>
                                <input type="number" step="0.01" min="0" max="100" class="form-input calc-trigger" id="tax_percentage" name="tax_percentage" placeholder="e.g. 18" value="0">
                            </div>

                            {{-- HSN / SAC Code --}}
                            <div class="form-group-cus" data-field="hsn_sac_code">
                                <label class="form-label" for="hsn_sac_code">HSN / SAC Code</label>
                                <input type="text" class="form-input" id="hsn_sac_code" name="hsn_sac_code" maxlength="30" placeholder="e.g. 8471 or 998314">
                            </div>
                        </div>

                        {{-- Auto-Calculated Summary Sub-Cards --}}
                        <div class="pricing-summary-strip">
                            <div class="pricing-metric">
                                <div class="pricing-metric-label">Discounted Base Price</div>
                                <div class="pricing-metric-value" id="calcDiscountedBase">0.00</div>
                            </div>
                            <div class="pricing-metric">
                                <div class="pricing-metric-label">GST / Tax Amount</div>
                                <div class="pricing-metric-value" id="calcTaxAmount">0.00</div>
                            </div>
                            <div class="pricing-metric is-highlight">
                                <div class="pricing-metric-label">Final Selling Price (Incl. Tax)</div>
                                <div class="pricing-metric-value" id="calcFinalPrice">0.00</div>
                                <input type="hidden" name="final_selling_price" id="final_selling_price" value="0">
                            </div>
                            <div class="pricing-metric" id="marginMetricCard">
                                <div class="pricing-metric-label">Estimated Profit Margin</div>
                                <div class="pricing-metric-value" id="calcProfitMargin">0.00 (0%)</div>
                                <input type="hidden" name="profit_margin" id="profit_margin" value="0">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ================= SECTION 3: INVENTORY INFORMATION ================= --}}
                <div class="prod-card" id="section-inventory">
                    <div class="prod-card-header">
                        <div class="prod-card-header-left">
                            <div class="prod-card-icon"><i class="fas fa-warehouse"></i></div>
                            <div>
                                <div class="prod-card-title">3. Inventory Information</div>
                                <div class="prod-card-desc">Stock quantities, reorder alerts, measurement unit, and warehouse location</div>
                            </div>
                        </div>
                    </div>
                    <div class="prod-card-body">
                        <div class="conditional-banner" id="inventoryTypeBanner">
                            <div>
                                <i class="fas fa-info-circle me-1"></i>
                                <span id="inventoryTypeBannerText">Digital &amp; Service products do not require physical warehouse stock. You may still track capacity or license seats if needed.</span>
                            </div>
                            <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer; font-weight:500;">
                                <input type="checkbox" id="trackInventoryToggle" name="track_inventory" value="1"> Track Capacity / Seats
                            </label>
                        </div>

                        <div class="form-grid-3" id="inventoryFieldsGrid">
                            {{-- Opening Stock --}}
                            <div class="form-group-cus" data-field="opening_stock">
                                <label class="form-label" for="opening_stock">Opening Stock</label>
                                <input type="number" step="1" min="0" class="form-input" id="opening_stock" name="opening_stock" placeholder="0" value="0">
                                <span class="form-error"><i class="fas fa-exclamation-circle"></i> Stock cannot be negative.</span>
                            </div>

                            {{-- Available Quantity --}}
                            <div class="form-group-cus" data-field="available_quantity">
                                <label class="form-label" for="available_quantity">Available Quantity</label>
                                <input type="number" step="1" min="0" class="form-input" id="available_quantity" name="available_quantity" placeholder="0" value="0">
                                <span class="form-error"><i class="fas fa-exclamation-circle"></i> Quantity cannot be negative.</span>
                            </div>

                            {{-- Minimum Stock Alert --}}
                            <div class="form-group-cus" data-field="min_stock_alert">
                                <label class="form-label" for="min_stock_alert">Minimum Stock Alert</label>
                                <input type="number" step="1" min="0" class="form-input" id="min_stock_alert" name="min_stock_alert" placeholder="e.g. 10" value="0">
                            </div>

                            {{-- Unit of Measurement --}}
                            <div class="form-group-cus" data-field="unit">
                                <label class="form-label" for="product_unit">Unit of Measurement</label>
                                <select class="form-select prod-select2" id="product_unit" name="unit">
                                    <option value="piece">Piece (pcs)</option>
                                    <option value="box">Box</option>
                                    <option value="kg">Kilogram (kg)</option>
                                    <option value="litre">Litre (L)</option>
                                    <option value="pack">Pack</option>
                                    <option value="set">Set</option>
                                    <option value="hour">Hour</option>
                                    <option value="license">License / Seat</option>
                                </select>
                            </div>

                            {{-- Warehouse / Location --}}
                            <div class="form-group-cus physical-only-field" data-field="warehouse_location">
                                <label class="form-label" for="warehouse_location">Warehouse / Location</label>
                                <input type="text" class="form-input" id="warehouse_location" name="warehouse_location" maxlength="150" placeholder="e.g. Main Warehouse - Rack A4">
                            </div>

                            {{-- Stock Status --}}
                            <div class="form-group-cus" data-field="stock_status">
                                <label class="form-label" for="stock_status">Stock Status</label>
                                <select class="form-select" id="stock_status" name="stock_status">
                                    <option value="in_stock">In Stock</option>
                                    <option value="low_stock">Low Stock</option>
                                    <option value="out_of_stock">Out of Stock</option>
                                    <option value="not_applicable">Not Applicable</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ================= SECTION 4: PRODUCT SPECIFICATIONS ================= --}}
                <div class="prod-card" id="section-specs">
                    <div class="prod-card-header">
                        <div class="prod-card-header-left">
                            <div class="prod-card-icon"><i class="fas fa-sliders-h"></i></div>
                            <div>
                                <div class="prod-card-title">4. Product Specifications</div>
                                <div class="prod-card-desc">Model identifiers, physical attributes, features, custom specs, and tags</div>
                            </div>
                        </div>
                    </div>
                    <div class="prod-card-body">
                        <div class="form-grid">
                            {{-- Model Number --}}
                            <div class="form-group-cus" data-field="model_number">
                                <label class="form-label" for="model_number">Model Number</label>
                                <input type="text" class="form-input" id="model_number" name="model_number" maxlength="100" placeholder="e.g. MDL-2026-X1">
                            </div>

                            {{-- Barcode --}}
                            <div class="form-group-cus" data-field="barcode">
                                <label class="form-label" for="barcode">Barcode (EAN / UPC)</label>
                                <input type="text" class="form-input" id="barcode" name="barcode" maxlength="100" placeholder="e.g. 8901234567890">
                            </div>

                            {{-- Warranty Period --}}
                            <div class="form-group-cus" data-field="warranty_period">
                                <label class="form-label" for="warranty_period">Warranty Period</label>
                                <input type="text" class="form-input" id="warranty_period" name="warranty_period" maxlength="100" placeholder="e.g. 12 Months / 2 Years On-Site">
                            </div>

                            {{-- Product Weight (Physical Only) --}}
                            <div class="form-group-cus physical-only-field" data-field="weight">
                                <label class="form-label" for="product_weight">Product Weight</label>
                                <input type="text" class="form-input" id="product_weight" name="weight" maxlength="60" placeholder="e.g. 1.25 kg">
                            </div>

                            {{-- Dimensions (Physical Only) --}}
                            <div class="form-group-cus full-width physical-only-field" data-field="dimensions">
                                <label class="form-label" for="product_dimensions">Dimensions (L &times; W &times; H)</label>
                                <input type="text" class="form-input" id="product_dimensions" name="dimensions" maxlength="120" placeholder="e.g. 30 cm x 20 cm x 10 cm">
                            </div>

                            {{-- Product Features --}}
                            <div class="form-group-cus full-width" data-field="features">
                                <label class="form-label" for="product_features">Product Features</label>
                                <textarea class="form-textarea" id="product_features" name="features" placeholder="List key selling points or product highlights (one per line)..."></textarea>
                            </div>

                            {{-- Product Specifications (Dynamic Key-Value) --}}
                            <div class="form-group-cus full-width" data-field="specifications">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label class="form-label">Technical Specifications</label>
                                    <button type="button" class="btn-inline-action" id="btnAddSpecRow">
                                        <i class="fas fa-plus"></i> Add Specification
                                    </button>
                                </div>
                                <div id="specRowsContainer">
                                    <div class="spec-row">
                                        <input type="text" class="form-input spec-key" placeholder="Attribute (e.g. Material, RAM, SLA)">
                                        <input type="text" class="form-input spec-val" placeholder="Value (e.g. Aluminum, 16 GB, 99.9%)">
                                        <button type="button" class="btn-icon-remove" title="Remove row"><i class="fas fa-times"></i></button>
                                    </div>
                                </div>
                                <input type="hidden" name="specifications" id="specifications_json" value="[]">
                            </div>

                            {{-- Product Tags --}}
                            <div class="form-group-cus full-width" data-field="tags">
                                <label class="form-label" for="tagChipInput">Product Tags</label>
                                <div class="tags-box" id="tagsBox">
                                    <input type="text" class="tag-input-bare" id="tagChipInput" placeholder="Type tag and press Enter or comma...">
                                </div>
                                <input type="hidden" name="tags" id="product_tags_json" value="[]">
                                <span class="form-hint">Helps sales reps quickly filter products during quotation &amp; deal creation.</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ================= SECTION 5: SALES CONFIGURATION ================= --}}
                <div class="prod-card" id="section-sales">
                    <div class="prod-card-header">
                        <div class="prod-card-header-left">
                            <div class="prod-card-icon"><i class="fas fa-cogs"></i></div>
                            <div>
                                <div class="prod-card-title">5. Sales Configuration</div>
                                <div class="prod-card-desc">Lifecycle status, CRM visibility, availability, and effective date range</div>
                            </div>
                        </div>
                    </div>
                    <div class="prod-card-body">
                        <div class="form-grid-3">
                            {{-- Product Status --}}
                            <div class="form-group-cus" data-field="status">
                                <label class="form-label required" for="product_status">Product Status</label>
                                <select class="form-select" id="product_status" name="status">
                                    <option value="draft">Draft</option>
                                    <option value="active" selected>Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <span class="form-hint">Inactive products cannot be added to new sales transactions.</span>
                            </div>

                            {{-- Product Visibility --}}
                            <div class="form-group-cus" data-field="visibility">
                                <label class="form-label" for="product_visibility">Product Visibility</label>
                                <select class="form-select" id="product_visibility" name="visibility">
                                    <option value="public">Public (All Sales Teams &amp; Quotes)</option>
                                    <option value="internal">Internal Only</option>
                                    <option value="hidden">Hidden</option>
                                </select>
                            </div>

                            {{-- Product Availability --}}
                            <div class="form-group-cus" data-field="availability">
                                <label class="form-label" for="product_availability">Product Availability</label>
                                <select class="form-select" id="product_availability" name="availability">
                                    <option value="available">Available</option>
                                    <option value="pre_order">Pre-Order</option>
                                    <option value="discontinued">Discontinued</option>
                                </select>
                            </div>

                            {{-- Featured Product (Yes / No) --}}
                            <div class="form-group-cus" data-field="is_featured">
                                <label class="form-label">Featured Product</label>
                                <input type="hidden" name="is_featured" id="is_featured" value="0">
                                <div class="featured-toggle" id="featuredSelector">
                                    <button type="button" class="type-option" data-featured="1">
                                        <i class="fas fa-star"></i> Yes
                                    </button>
                                    <button type="button" class="type-option is-selected" data-featured="0">
                                        No
                                    </button>
                                </div>
                            </div>

                            {{-- Product Start Date --}}
                            <div class="form-group-cus" data-field="start_date">
                                <label class="form-label" for="product_start_date">Product Start Date</label>
                                <input type="date" class="form-input" id="product_start_date" name="start_date">
                            </div>

                            {{-- Product End Date --}}
                            <div class="form-group-cus" data-field="end_date">
                                <label class="form-label" for="product_end_date">Product End Date</label>
                                <input type="date" class="form-input" id="product_end_date" name="end_date">
                                <span class="form-error"><i class="fas fa-exclamation-circle"></i> End Date cannot be earlier than Start Date.</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sticky Footer Actions --}}
                <div class="prod-sticky-footer">
                    <div class="text-muted" style="font-size: 12px;">
                        <span class="required"></span> Indicates required fields
                    </div>
                    <div class="prod-footer-actions">
                        <a href="{{ url('service_company/jobs') }}" class="btn-prod-secondary">
                            <i class="fas fa-arrow-left"></i> Cancel
                        </a>
                        <button type="button" class="btn-prod-secondary" id="btnSaveDraft">
                            <i class="fas fa-save"></i> Save as Draft
                        </button>
                        <button type="submit" class="btn-prod-primary" id="btnSaveProduct">
                            <i class="fas fa-check-circle"></i> Save Product
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('footer-script')
    <script src="{{ asset('demo/v7/js/plugins.bundle.js') }}<?php echo TM_VERSION; ?>"></script>
    <script>
        (function () {
            'use strict';

            // 1. Initialize Searchable Select2 Dropdowns
            if (window.jQuery && $.fn.select2) {
                $('.prod-select2').select2({
                    width: '100%',
                    allowClear: true
                });
            }

            // 2. Section Jump Navigation Active State
            const navTabs = document.querySelectorAll('#prodSectionNav .prod-nav-tab');
            navTabs.forEach(function (tab) {
                tab.addEventListener('click', function () {
                    navTabs.forEach(function (t) { t.classList.remove('is-active'); });
                    tab.classList.add('is-active');
                });
            });

            // 3. SKU Auto-Generator
            const skuInput = document.getElementById('product_sku');
            const nameInput = document.getElementById('product_name');
            document.getElementById('btnGenerateSku').addEventListener('click', function () {
                const rawName = (nameInput.value || 'PRD').replace(/[^a-zA-Z0-9]/g, '').toUpperCase().slice(0, 4) || 'PRD';
                const rand = Math.floor(1000 + Math.random() * 9000);
                skuInput.value = rawName + '-' + rand;
                clearFieldError('sku');
            });

            // 4. Product Type Conditional Logic (Physical / Digital / Service)
            const typeInput = document.getElementById('product_type');
            const typeButtons = document.querySelectorAll('#productTypeSelector .type-option');
            const headerTypeLabel = document.getElementById('headerTypeLabel');
            const inventoryBanner = document.getElementById('inventoryTypeBanner');
            const inventoryGrid = document.getElementById('inventoryFieldsGrid');
            const trackInventoryToggle = document.getElementById('trackInventoryToggle');
            const physicalOnlyFields = document.querySelectorAll('.physical-only-field');

            function applyProductTypeVisibility(type) {
                const labelMap = { physical: 'Physical', digital: 'Digital', service: 'Service' };
                headerTypeLabel.textContent = labelMap[type] || 'Physical';

                if (type === 'physical') {
                    inventoryBanner.classList.remove('is-visible');
                    inventoryGrid.style.display = 'grid';
                    physicalOnlyFields.forEach(function (el) { el.style.display = 'flex'; });
                } else {
                    inventoryBanner.classList.add('is-visible');
                    physicalOnlyFields.forEach(function (el) { el.style.display = 'none'; });
                    inventoryGrid.style.display = trackInventoryToggle.checked ? 'grid' : 'none';
                }
            }

            typeButtons.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    typeButtons.forEach(function (b) { b.classList.remove('is-selected'); });
                    btn.classList.add('is-selected');
                    const selectedType = btn.getAttribute('data-type');
                    typeInput.value = selectedType;
                    applyProductTypeVisibility(selectedType);
                });
            });

            trackInventoryToggle.addEventListener('change', function () {
                applyProductTypeVisibility(typeInput.value);
            });

            // 5. Image Upload & Live Preview
            const imageInput = document.getElementById('product_image');
            const previewBox = document.getElementById('imagePreviewBox');
            const previewImg = document.getElementById('imagePreviewImg');
            const removeImgBtn = document.getElementById('btnRemoveImage');
            const imageFileMeta = document.getElementById('imageFileMeta');
            const dropzone = document.getElementById('imageDropzone');

            document.getElementById('btnChooseImage').addEventListener('click', function (e) {
                e.stopPropagation();
                imageInput.click();
            });
            dropzone.addEventListener('click', function () {
                imageInput.click();
            });
            dropzone.addEventListener('dragover', function (e) {
                e.preventDefault();
                dropzone.classList.add('is-dragover');
            });
            dropzone.addEventListener('dragleave', function () {
                dropzone.classList.remove('is-dragover');
            });
            dropzone.addEventListener('drop', function (e) {
                e.preventDefault();
                dropzone.classList.remove('is-dragover');
                if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                    imageInput.files = e.dataTransfer.files;
                    handleImageFile(e.dataTransfer.files[0]);
                }
            });
            imageInput.addEventListener('change', function () {
                if (imageInput.files && imageInput.files[0]) {
                    handleImageFile(imageInput.files[0]);
                }
            });
            removeImgBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                imageInput.value = '';
                previewImg.src = '';
                previewBox.classList.remove('has-image');
                removeImgBtn.classList.add('d-none');
                imageFileMeta.textContent = 'Drag & drop or click to upload PNG, JPG, or WEBP (max 2 MB).';
            });

            function handleImageFile(file) {
                const reader = new FileReader();
                reader.onload = function (ev) {
                    previewImg.src = ev.target.result;
                    previewBox.classList.add('has-image');
                    removeImgBtn.classList.remove('d-none');
                    imageFileMeta.textContent = file.name + ' (' + Math.round(file.size / 1024) + ' KB)';
                };
                reader.readAsDataURL(file);
            }

            // 6. Rich Description Editor Sync
            const descEditor = document.getElementById('productDescriptionEditor');
            const descHidden = document.getElementById('product_description');
            const descCharCount = document.getElementById('descCharCount');

            document.querySelectorAll('.editor-toolbar .toolbar-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    document.execCommand(btn.getAttribute('data-cmd'), false, null);
                    descEditor.focus();
                });
            });
            descEditor.addEventListener('input', function () {
                descHidden.value = descEditor.innerHTML;
                descCharCount.textContent = (descEditor.textContent || '').trim().length + ' characters';
            });

            // 7. Real-Time Pricing, Discount, GST & Profit Margin Calculations
            const purchaseEl = document.getElementById('purchase_price');
            const sellingEl = document.getElementById('selling_price');
            const discountTypeEl = document.getElementById('discount_type');
            const discountValEl = document.getElementById('discount_value');
            const taxEl = document.getElementById('tax_percentage');
            const currencyEl = document.getElementById('product_currency');

            function getCurrencySymbol() {
                const opt = currencyEl.options[currencyEl.selectedIndex];
                return (opt && opt.getAttribute('data-symbol')) ? opt.getAttribute('data-symbol') + ' ' : '';
            }

            function recalculatePricing() {
                const sym = getCurrencySymbol();
                const purchase = Math.max(0, parseFloat(purchaseEl.value) || 0);
                const selling = Math.max(0, parseFloat(sellingEl.value) || 0);
                const dType = discountTypeEl.value;
                const dVal = Math.max(0, parseFloat(discountValEl.value) || 0);
                const taxPct = Math.max(0, parseFloat(taxEl.value) || 0);

                let discountAmount = dType === 'percentage' ? (selling * Math.min(dVal, 100) / 100) : Math.min(dVal, selling);
                const discountedBase = Math.max(0, selling - discountAmount);
                const taxAmount = discountedBase * (taxPct / 100);
                const finalPrice = discountedBase + taxAmount;

                const profit = discountedBase - purchase;
                const marginPct = discountedBase > 0 ? (profit / discountedBase) * 100 : 0;

                document.getElementById('calcDiscountedBase').textContent = sym + discountedBase.toFixed(2);
                document.getElementById('calcTaxAmount').textContent = sym + taxAmount.toFixed(2);
                document.getElementById('calcFinalPrice').textContent = sym + finalPrice.toFixed(2);
                document.getElementById('final_selling_price').value = finalPrice.toFixed(2);

                const marginEl = document.getElementById('calcProfitMargin');
                const marginCard = document.getElementById('marginMetricCard');
                marginEl.textContent = sym + profit.toFixed(2) + ' (' + marginPct.toFixed(1) + '%)';
                document.getElementById('profit_margin').value = profit.toFixed(2);

                marginCard.classList.remove('is-profit-positive', 'is-profit-negative');
                if (profit > 0) marginCard.classList.add('is-profit-positive');
                else if (profit < 0) marginCard.classList.add('is-profit-negative');
            }

            document.querySelectorAll('.calc-trigger').forEach(function (el) {
                el.addEventListener('input', recalculatePricing);
                el.addEventListener('change', recalculatePricing);
            });
            if (window.jQuery) {
                $('#product_currency').on('change', recalculatePricing);
            }
            recalculatePricing();

            // 8. Opening Stock -> Available Quantity Sync
            const openingStockEl = document.getElementById('opening_stock');
            const availQtyEl = document.getElementById('available_quantity');
            let availEditedManually = false;
            availQtyEl.addEventListener('input', function () { availEditedManually = true; });
            openingStockEl.addEventListener('input', function () {
                if (!availEditedManually) {
                    availQtyEl.value = Math.max(0, parseInt(openingStockEl.value || 0, 10));
                }
            });

            // 9. Dynamic Technical Specifications Rows
            const specContainer = document.getElementById('specRowsContainer');
            const specHidden = document.getElementById('specifications_json');

            function syncSpecsJson() {
                const rows = [];
                specContainer.querySelectorAll('.spec-row').forEach(function (row) {
                    const k = row.querySelector('.spec-key').value.trim();
                    const v = row.querySelector('.spec-val').value.trim();
                    if (k || v) rows.push({ key: k, value: v });
                });
                specHidden.value = JSON.stringify(rows);
            }

            document.getElementById('btnAddSpecRow').addEventListener('click', function () {
                const div = document.createElement('div');
                div.className = 'spec-row';
                div.innerHTML = '<input type="text" class="form-input spec-key" placeholder="Attribute">' +
                    '<input type="text" class="form-input spec-val" placeholder="Value">' +
                    '<button type="button" class="btn-icon-remove" title="Remove row"><i class="fas fa-times"></i></button>';
                specContainer.appendChild(div);
            });
            specContainer.addEventListener('click', function (e) {
                const btn = e.target.closest('.btn-icon-remove');
                if (btn && specContainer.querySelectorAll('.spec-row').length > 1) {
                    btn.closest('.spec-row').remove();
                    syncSpecsJson();
                }
            });
            specContainer.addEventListener('input', syncSpecsJson);

            // 10. Product Tags Chip Input
            const tags = [];
            const tagsBox = document.getElementById('tagsBox');
            const tagInput = document.getElementById('tagChipInput');
            const tagsHidden = document.getElementById('product_tags_json');

            function renderTags() {
                tagsBox.querySelectorAll('.tag-chip').forEach(function (el) { el.remove(); });
                tags.forEach(function (tag, idx) {
                    const chip = document.createElement('span');
                    chip.className = 'tag-chip';
                    chip.innerHTML = '<span></span><button type="button" data-idx="' + idx + '">&times;</button>';
                    chip.querySelector('span').textContent = tag;
                    tagsBox.insertBefore(chip, tagInput);
                });
                tagsHidden.value = JSON.stringify(tags);
            }

            tagInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ',') {
                    e.preventDefault();
                    const val = tagInput.value.replace(/,/g, '').trim();
                    if (val && tags.indexOf(val) === -1) {
                        tags.push(val);
                        renderTags();
                    }
                    tagInput.value = '';
                }
            });
            tagsBox.addEventListener('click', function (e) {
                if (e.target.tagName === 'BUTTON') {
                    const idx = parseInt(e.target.getAttribute('data-idx'), 10);
                    tags.splice(idx, 1);
                    renderTags();
                } else {
                    tagInput.focus();
                }
            });

            // 11. Featured Product & Status Header Badge Sync
            const featuredBtns = document.querySelectorAll('#featuredSelector .type-option');
            const featuredHidden = document.getElementById('is_featured');
            featuredBtns.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    featuredBtns.forEach(function (b) { b.classList.remove('is-selected'); });
                    btn.classList.add('is-selected');
                    featuredHidden.value = btn.getAttribute('data-featured');
                });
            });

            const statusSelect = document.getElementById('product_status');
            const headerStatusLabel = document.getElementById('headerStatusLabel');
            function syncStatusBadge() {
                const map = { draft: 'Draft', active: 'Active', inactive: 'Inactive' };
                headerStatusLabel.textContent = map[statusSelect.value] || 'Active';
            }
            statusSelect.addEventListener('change', syncStatusBadge);
            syncStatusBadge();

            const SUBMIT_URL = @json($isEdit ? route('service_company.products.postUpdate', $product->id) : route('service_company.products.store'));
            const CHECK_SKU_URL = @json(route('service_company.products.checkSku'));
            const SUBCAT_URL_TPL = @json(route('service_company.products.subcategories', ['categoryId' => '__CAT_ID__']));
            const EDIT_PRODUCT_ID = @json($isEdit ? (int) $product->id : 0);
            const CSRF_TOKEN = @json(csrf_token());

            // 11b. Dependent Category -> Subcategory AJAX Loader
            const catSelect = document.getElementById('product_category');
            const subcatSelect = document.getElementById('product_subcategory');

            function loadSubcategories(catId, selectedSubId) {
                subcatSelect.innerHTML = '<option value=""></option>';
                if (!catId) {
                    if (window.jQuery && $.fn.select2) $(subcatSelect).trigger('change.select2');
                    return;
                }
                fetch(SUBCAT_URL_TPL.replace('__CAT_ID__', catId), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    (res.data || []).forEach(function (item) {
                        const opt = document.createElement('option');
                        opt.value = item.id;
                        opt.textContent = item.name;
                        if (selectedSubId && parseInt(selectedSubId, 10) === parseInt(item.id, 10)) {
                            opt.selected = true;
                        }
                        subcatSelect.appendChild(opt);
                    });
                    if (window.jQuery && $.fn.select2) $(subcatSelect).trigger('change.select2');
                })
                .catch(function () {});
            }

            if (window.jQuery) {
                $('#product_category').on('change', function () {
                    clearFieldError('category_id');
                    loadSubcategories(this.value, null);
                });
            } else {
                catSelect.addEventListener('change', function () {
                    clearFieldError('category_id');
                    loadSubcategories(this.value, null);
                });
            }

            // 11c. Real-Time Duplicate SKU Check
            let skuCheckTimer = null;
            let skuIsDuplicate = false;
            const skuHintEl = document.getElementById('skuStatusHint');
            const skuErrText = document.getElementById('skuErrorText');

            function checkSkuAvailability() {
                const val = (skuInput.value || '').trim().toUpperCase();
                if (!val) {
                    skuIsDuplicate = false;
                    return;
                }
                fetch(CHECK_SKU_URL + '?sku=' + encodeURIComponent(val) + '&exclude_id=' + EDIT_PRODUCT_ID, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    if (res.available === false) {
                        skuIsDuplicate = true;
                        skuErrText.textContent = res.message || 'This SKU already exists in your company catalog.';
                        setFieldError('sku');
                        skuHintEl.textContent = '';
                    } else {
                        skuIsDuplicate = false;
                        clearFieldError('sku');
                        skuHintEl.textContent = 'SKU is available.';
                        skuHintEl.style.color = 'var(--prod-success)';
                    }
                })
                .catch(function () {});
            }

            skuInput.addEventListener('input', function () {
                skuHintEl.style.color = '';
                skuHintEl.textContent = 'Checking SKU availability...';
                clearTimeout(skuCheckTimer);
                skuCheckTimer = setTimeout(checkSkuAvailability, 350);
            });
            skuInput.addEventListener('blur', checkSkuAvailability);

            // 11d. Hydrate Edit Mode if $product is present
            @if($isEdit)
                (function hydrateEditProduct() {
                    const p = @json($product);
                    document.querySelector('.prod-page-title').textContent = 'Edit Product';
                    document.getElementById('btnSaveProduct').innerHTML = '<i class="fas fa-check-circle"></i> Update Product';
                    document.getElementById('product_name').value = p.name || '';
                    document.getElementById('product_sku').value = p.sku || '';
                    document.getElementById('product_brand').value = p.brand || '';
                    if (p.description) {
                        descEditor.innerHTML = p.description;
                        descHidden.value = p.description;
                        descCharCount.textContent = (descEditor.textContent || '').trim().length + ' characters';
                    }
                    if (p.image_path) {
                        previewImg.src = '/' + p.image_path;
                        previewBox.classList.add('has-image');
                        removeImgBtn.classList.remove('d-none');
                    }
                    document.getElementById('purchase_price').value = p.purchase_price ?? 0;
                    document.getElementById('selling_price').value = p.selling_price ?? 0;
                    document.getElementById('mrp').value = p.mrp ?? '';
                    document.getElementById('discount_type').value = p.discount_type || 'percentage';
                    document.getElementById('discount_value').value = p.discount_value ?? 0;
                    document.getElementById('tax_percentage').value = p.tax_percentage ?? 0;
                    document.getElementById('hsn_sac_code').value = p.hsn_sac_code || '';

                    document.getElementById('opening_stock').value = p.opening_stock ?? 0;
                    document.getElementById('available_quantity').value = p.available_quantity ?? 0;
                    document.getElementById('min_stock_alert').value = p.min_stock_alert ?? 0;
                    document.getElementById('product_unit').value = p.unit || 'piece';
                    document.getElementById('warehouse_location').value = p.warehouse_location || '';
                    document.getElementById('stock_status').value = p.stock_status || 'in_stock';
                    trackInventoryToggle.checked = !!p.track_inventory;

                    document.getElementById('model_number').value = p.model_number || '';
                    document.getElementById('barcode').value = p.barcode || '';
                    document.getElementById('warranty_period').value = p.warranty_period || '';
                    document.getElementById('product_weight').value = p.weight || '';
                    document.getElementById('product_dimensions').value = p.dimensions || '';
                    document.getElementById('product_features').value = p.features || '';

                    if (Array.isArray(p.specifications) && p.specifications.length) {
                        specContainer.innerHTML = '';
                        p.specifications.forEach(function (row) {
                            const div = document.createElement('div');
                            div.className = 'spec-row';
                            div.innerHTML = '<input type="text" class="form-input spec-key" placeholder="Attribute">' +
                                '<input type="text" class="form-input spec-val" placeholder="Value">' +
                                '<button type="button" class="btn-icon-remove" title="Remove row"><i class="fas fa-times"></i></button>';
                            div.querySelector('.spec-key').value = row.key || '';
                            div.querySelector('.spec-val').value = row.value || '';
                            specContainer.appendChild(div);
                        });
                        syncSpecsJson();
                    }
                    if (Array.isArray(p.tags) && p.tags.length) {
                        p.tags.forEach(function (t) { tags.push(t); });
                        renderTags();
                    }

                    const pType = p.product_type || 'physical';
                    typeInput.value = pType;
                    typeButtons.forEach(function (b) {
                        b.classList.toggle('is-selected', b.getAttribute('data-type') === pType);
                    });
                    applyProductTypeVisibility(pType);

                    statusSelect.value = p.status || 'draft';
                    syncStatusBadge();
                    document.getElementById('product_visibility').value = p.visibility || 'public';
                    document.getElementById('product_availability').value = p.availability || 'available';
                    const featVal = p.is_featured ? '1' : '0';
                    featuredHidden.value = featVal;
                    featuredBtns.forEach(function (b) {
                        b.classList.toggle('is-selected', b.getAttribute('data-featured') === featVal);
                    });
                    if (p.start_date) document.getElementById('product_start_date').value = String(p.start_date).slice(0, 10);
                    if (p.end_date) document.getElementById('product_end_date').value = String(p.end_date).slice(0, 10);

                    recalculatePricing();
                })();
            @endif

            // 12. Validation & Real Backend Submission
            function setFieldError(fieldName) {
                const grp = document.querySelector('.form-group-cus[data-field="' + fieldName + '"]');
                if (grp) grp.classList.add('has-error');
            }
            function clearFieldError(fieldName) {
                const grp = document.querySelector('.form-group-cus[data-field="' + fieldName + '"]');
                if (grp) grp.classList.remove('has-error');
            }

            function validateProductForm(isDraft) {
                let valid = true;
                ['name', 'sku', 'category_id', 'selling_price', 'opening_stock', 'available_quantity', 'end_date'].forEach(clearFieldError);

                if (!nameInput.value.trim()) { setFieldError('name'); valid = false; }
                if (!skuInput.value.trim() || skuIsDuplicate) {
                    if (skuIsDuplicate) skuErrText.textContent = 'This SKU already exists in your company catalog.';
                    else skuErrText.textContent = 'Product SKU / Code is required.';
                    setFieldError('sku');
                    valid = false;
                }

                if (!isDraft) {
                    if (!catSelect.value) { setFieldError('category_id'); valid = false; }
                    const sp = parseFloat(sellingEl.value);
                    if (isNaN(sp) || sp < 0) { setFieldError('selling_price'); valid = false; }
                }

                if ((parseFloat(openingStockEl.value) || 0) < 0) { setFieldError('opening_stock'); valid = false; }
                if ((parseFloat(availQtyEl.value) || 0) < 0) { setFieldError('available_quantity'); valid = false; }

                const startD = document.getElementById('product_start_date').value;
                const endD = document.getElementById('product_end_date').value;
                if (startD && endD && endD < startD) { setFieldError('end_date'); valid = false; }

                if (!valid) {
                    const firstErr = document.querySelector('#prodwrap .has-error');
                    if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return valid;
            }

            function submitProductForm(isDraft) {
                descHidden.value = descEditor.innerHTML;
                syncSpecsJson();
                if (!validateProductForm(isDraft)) return;

                const formEl = document.getElementById('productCreateForm');
                const formData = new FormData(formEl);
                formData.set('status', statusSelect.value);
                if (typeInput.value === 'physical') {
                    formData.set('track_inventory', '1');
                } else {
                    formData.set('track_inventory', trackInventoryToggle.checked ? '1' : '0');
                }

                const btnPrimary = document.getElementById('btnSaveProduct');
                const btnDraft = document.getElementById('btnSaveDraft');
                btnPrimary.disabled = true;
                btnDraft.disabled = true;

                fetch(SUBMIT_URL, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(async function (response) {
                    const data = await response.json().catch(function () { return {}; });
                    if (response.ok && data.status === 'success') {
                        if (typeof toastr !== 'undefined') toastr.success(data.message || 'Product saved.');
                        else alert(data.message || 'Product saved.');
                        if (!EDIT_PRODUCT_ID && data.product && data.product.id) {
                            window.location.href = @json(url('service_company/products')) + '/' + data.product.id + '/edit';
                        }
                    } else if (response.status === 422 && data.errors) {
                        Object.keys(data.errors).forEach(function (key) {
                            setFieldError(key);
                            if (key === 'sku' && data.errors.sku[0]) {
                                skuErrText.textContent = data.errors.sku[0];
                            }
                        });
                        const msg = data.message || Object.values(data.errors)[0][0] || 'Validation failed.';
                        if (typeof toastr !== 'undefined') toastr.error(msg);
                        else alert(msg);
                    } else {
                        const errMsg = data.message || 'Unable to save product.';
                        if (typeof toastr !== 'undefined') toastr.error(errMsg);
                        else alert(errMsg);
                    }
                })
                .catch(function () {
                    if (typeof toastr !== 'undefined') toastr.error('Network error while saving product.');
                    else alert('Network error while saving product.');
                })
                .finally(function () {
                    btnPrimary.disabled = false;
                    btnDraft.disabled = false;
                });
            }

            document.getElementById('btnSaveDraft').addEventListener('click', function () {
                statusSelect.value = 'draft';
                syncStatusBadge();
                document.getElementById('submit_action').value = 'draft';
                submitProductForm(true);
            });

            document.getElementById('productCreateForm').addEventListener('submit', function (e) {
                e.preventDefault();
                document.getElementById('submit_action').value = statusSelect.value;
                submitProductForm(statusSelect.value === 'draft');
            });
        })();
    </script>
@endpush
