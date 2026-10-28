@extends('layouts.superadmin_layout')

@push('head-script')
    <link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}">
    <style>
        /* Scoped Modern Card + Sub-Card Enterprise Design System for Global Settings */
        body {
            overflow-x: hidden !important;
        }

        /* ── Top Gap Collapse: eliminate excessive toolbar & wrapper spacing on standard 15.6" viewports ── */
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
            padding-top: calc(65px + 10px) !important;
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

        .cd-settings-container {
            padding: 0 16px 30px 16px !important;
            margin-top: 0 !important;
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
        }

        /* Tier 1: Header Toolbar Card */
        .cd-settings-header-card {
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
            gap: 14px;
        }

        .cd-settings-header-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .cd-settings-icon-box {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: #eff6ff;
            color: #009ef7;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
            border: 1px solid #dbeafe;
        }

        .cd-settings-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.25;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        .cd-settings-subtitle {
            font-size: 12px;
            color: #64748b;
            margin: 2px 0 0 0;
        }

        .cd-settings-header-badges {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .cd-pill-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
            letter-spacing: 0.2px;
        }

        .cd-pill-online {
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
        }

        .cd-pill-maintenance {
            background: #fffbeb;
            color: #d97706;
            border: 1px solid #fde68a;
        }

        .cd-pill-neutral {
            background: #f8fafc;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        /* Tier 2: Domain Sub-Tabs Navigation Card */
        .cd-settings-tabs-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 8px 12px !important;
            margin-bottom: 16px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
        }

        .cd-settings-tabs {
            border-bottom: none !important;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin: 0;
            padding: 0;
        }

        .cd-settings-tab-btn {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 8px 16px !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            color: #475569 !important;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            text-decoration: none !important;
        }

        .cd-settings-tab-btn:hover {
            background: #f8fafc !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }

        .cd-settings-tab-btn.active {
            background: #009ef7 !important;
            border-color: #009ef7 !important;
            color: #ffffff !important;
            box-shadow: 0 2px 4px rgba(0, 158, 247, 0.22) !important;
        }

        .cd-settings-tab-btn.active i {
            color: #ffffff !important;
        }

        /* Tier 3: Modular Sub-Cards */
        .cd-subcard {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 20px 22px !important;
            margin-bottom: 16px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
            transition: box-shadow 0.2s ease;
        }

        .cd-subcard:hover {
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05) !important;
        }

        .cd-subcard-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 12px;
            margin-bottom: 18px;
            gap: 12px;
            flex-wrap: wrap;
        }

        .cd-subcard-header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cd-subcard-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #f8fafc;
            color: #009ef7;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            border: 1px solid #e2e8f0;
        }

        .cd-subcard-title {
            font-size: 13.5px;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        .cd-subcard-subtitle {
            font-size: 11px;
            color: #64748b;
            margin: 2px 0 0 0;
        }

        .cd-form-label {
            font-size: 11.5px !important;
            font-weight: 600 !important;
            color: #334155 !important;
            margin-bottom: 6px !important;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .cd-form-label.required::after {
            content: "*";
            color: #f1416c;
            font-weight: 700;
        }

        .cd-form-control {
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 8px 12px !important;
            font-size: 12.5px !important;
            font-family: 'Poppins', sans-serif !important;
            color: #0f172a !important;
            transition: all 0.15s ease-in-out;
            background-color: #ffffff !important;
        }

        .cd-form-control:focus {
            border-color: #009ef7 !important;
            box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
            outline: none !important;
        }

        .cd-help-text {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 5px;
        }

        .cd-logo-preview-box {
            display: flex;
            align-items: center;
            gap: 16px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 14px;
        }

        .cd-logo-img-wrapper {
            width: 140px;
            height: 50px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6px;
            overflow: hidden;
        }

        .cd-logo-img-wrapper img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        /* Bottom Activity & Action Strip */
        .cd-bottom-action-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 14px 20px !important;
            margin-top: 14px !important;
            margin-bottom: 30px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
        }

        .cd-meta-strip {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            font-size: 11.5px;
            color: #64748b;
        }

        .cd-meta-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .cd-meta-item i {
            color: #94a3b8;
        }

        /* Dropify Overrides for 15.6" screen elegance */
        .dropify-wrapper {
            border: 1px dashed #cbd5e1 !important;
            border-radius: 8px !important;
            height: 130px !important;
            background-color: #fafbfd !important;
        }

        .dropify-wrapper:hover {
            border-color: #009ef7 !important;
            background-color: #f0f9ff !important;
        }

        .dropify-wrapper .dropify-message p {
            font-size: 12.5px !important;
            color: #64748b !important;
            font-family: 'Poppins', sans-serif !important;
        }

        .invalid-feedback {
            font-size: 11px !important;
            color: #f1416c !important;
            margin-top: 4px !important;
            display: block;
        }

        .is-invalid {
            border-color: #f1416c !important;
        }
    </style>
@endpush

@section('content')
    <div class="cd-settings-container container-fluid">
        <!-- Form Container -->
        <form id="globalSettingsForm" class="ajax-form" enctype="multipart/form-data" method="POST" action="{{ route('superadmin.global-settings.update', $global->id) }}">
            @csrf
            @method('PUT')

            <!-- ============================================================== -->
            <!-- TIER 1: TOP HEADER TOOLBAR CARD                               -->
            <!-- ============================================================== -->
            <div class="cd-settings-header-card">
                <div class="cd-settings-header-left">
                    <div class="cd-settings-icon-box">
                        <i class="fas fa-sliders-h"></i>
                    </div>
                    <div>
                        <h1 class="cd-settings-title">@lang('menu.settings') & Organization Hub</h1>
                        <p class="cd-settings-subtitle">Manage core brand identity, regional localization, platform lifecycle policies, and legal invoicing details</p>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <div class="cd-settings-header-badges d-none d-md-flex">
                        <span class="cd-pill-badge {{ $global->system_update == 1 ? 'cd-pill-online' : 'cd-pill-maintenance' }}" id="statusBadge">
                            <i class="fas fa-circle fs-8"></i>
                            <span id="statusBadgeText">{{ $global->system_update == 1 ? 'System Online (Production)' : 'Maintenance Mode' }}</span>
                        </span>
                        <span class="cd-pill-badge cd-pill-neutral">
                            <i class="fas fa-coins text-warning"></i>
                            <span id="headerCurrencyBadgeText">{{ $global->currency->currency_code ?? 'USD' }} ({{ $global->currency->currency_symbol ?? '$' }})</span>
                        </span>
                        <span class="cd-pill-badge cd-pill-neutral">
                            <i class="fas fa-language text-primary"></i>
                            <span id="headerLocaleBadgeText">{{ strtoupper($global->locale ?? 'EN') }}</span>
                        </span>
                    </div>

                    <button type="button" class="btn btn-sm btn-light-secondary px-4 py-2 text-gray-700" id="resetBtn">
                        <i class="fas fa-redo-alt me-1 text-gray-500"></i> @lang('app.reset')
                    </button>

                    <button type="button" class="btn btn-sm btn-primary px-5 py-2 fw-semibold" id="saveTopBtn">
                        <span class="indicator-label">
                            <i class="fas fa-save me-1"></i> @lang('app.save')
                        </span>
                        <span class="indicator-progress">
                            <span class="spinner-border spinner-border-sm align-middle me-2"></span> Saving...
                        </span>
                    </button>
                </div>
            </div>

            <!-- ============================================================== -->
            <!-- TIER 2: DOMAIN SUB-TABS NAVIGATION CARD                       -->
            <!-- ============================================================== -->
            <div class="cd-settings-tabs-card">
                <ul class="nav cd-settings-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link cd-settings-tab-btn active" data-bs-toggle="tab" href="#tab_brand_profile" role="tab" id="tabNavBrand">
                            <i class="fas fa-building text-primary"></i>
                            <span>Brand & Profile</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link cd-settings-tab-btn" data-bs-toggle="tab" href="#tab_localization" role="tab" id="tabNavLocale">
                            <i class="fas fa-globe-americas text-info"></i>
                            <span>Regional & Currency</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link cd-settings-tab-btn" data-bs-toggle="tab" href="#tab_security" role="tab" id="tabNavSecurity">
                            <i class="fas fa-shield-alt text-success"></i>
                            <span>Security & Policies</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link cd-settings-tab-btn" data-bs-toggle="tab" href="#tab_invoicing" role="tab" id="tabNavInvoicing">
                            <i class="fas fa-file-invoice-dollar text-warning"></i>
                            <span>Invoicing & Sold-By Address</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- ============================================================== -->
            <!-- TIER 3: PRIMARY CONFIGURATION STREAM                           -->
            <!-- ============================================================== -->
            <div class="tab-content" id="settingsTabContent">

                <!-- ---------------------------------------------------------- -->
                <!-- TAB 1: BRAND PROFILE & ASSETS                              -->
                <!-- ---------------------------------------------------------- -->
                <div class="tab-pane fade show active" id="tab_brand_profile" role="tabpanel">
                    <div class="row g-4">
                        <!-- Sub-Card 1: Brand & Contact Info -->
                        <div class="col-lg-7">
                            <div class="cd-subcard h-100">
                                <div class="cd-subcard-header">
                                    <div class="cd-subcard-header-left">
                                        <div class="cd-subcard-icon">
                                            <i class="fas fa-id-card"></i>
                                        </div>
                                        <div>
                                            <h3 class="cd-subcard-title">Organization Identity & Contact Details</h3>
                                            <p class="cd-subcard-subtitle">Master platform name, primary contact numbers, and official URL</p>
                                        </div>
                                    </div>
                                    <span class="badge badge-light-primary fw-semibold px-3 py-2">Brand Identity</span>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="cd-form-label required" for="company_name">@lang('modules.accountSettings.companyName')</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-building text-muted"></i></span>
                                                <input type="text" class="form-control cd-form-control border-start-0" id="company_name" name="company_name"
                                                    value="{{ $global->company_name }}" placeholder="e.g. Jeroworks Technology" required>
                                            </div>
                                            <div class="invalid-feedback" id="error_company_name"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="cd-form-label required" for="company_email">@lang('modules.accountSettings.companyEmail')</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                                <input type="email" class="form-control cd-form-control border-start-0" id="company_email" name="company_email"
                                                    value="{{ $global->company_email }}" placeholder="e.g. support@jero.co.in" required>
                                            </div>
                                            <div class="invalid-feedback" id="error_company_email"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="cd-form-label required" for="company_phone">@lang('modules.accountSettings.companyPhone')</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-phone-alt text-muted"></i></span>
                                                <input type="tel" class="form-control cd-form-control border-start-0" id="company_phone" name="company_phone"
                                                    value="{{ $global->company_phone }}" placeholder="e.g. +91 9876543210" required>
                                            </div>
                                            <div class="invalid-feedback" id="error_company_phone"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="cd-form-label" for="website">@lang('modules.accountSettings.companyWebsite')</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-globe text-muted"></i></span>
                                                <input type="text" class="form-control cd-form-control border-start-0" id="website" name="website"
                                                    value="{{ $global->website }}" placeholder="https://jero.works">
                                            </div>
                                            <div class="invalid-feedback" id="error_website"></div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group mb-0">
                                            <label class="cd-form-label" for="address">@lang('modules.accountSettings.companyAddress')</label>
                                            <textarea class="form-control cd-form-control" id="address" rows="3" name="address"
                                                placeholder="Headquarters corporate physical location...">{{ $global->address }}</textarea>
                                            <span class="cd-help-text">Physical address shown on portal footer and system dispatches</span>
                                            <div class="invalid-feedback" id="error_address"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sub-Card 2: Platform Logo Asset Management -->
                        <div class="col-lg-5">
                            <div class="cd-subcard h-100">
                                <div class="cd-subcard-header">
                                    <div class="cd-subcard-header-left">
                                        <div class="cd-subcard-icon">
                                            <i class="fas fa-image"></i>
                                        </div>
                                        <div>
                                            <h3 class="cd-subcard-title">Brand Logo & Visual Identity</h3>
                                            <p class="cd-subcard-subtitle">Global SVG/PNG logo rendered across all navigation headers</p>
                                        </div>
                                    </div>
                                    <span class="badge badge-light-info fw-semibold px-3 py-2">Max 2 MB</span>
                                </div>

                                <!-- Current Active Logo Preview -->
                                <div class="cd-logo-preview-box">
                                    <div class="cd-logo-img-wrapper">
                                        <img src="{{ $global->logo_url }}" alt="{{ $global->company_name }}" id="currentLogoPreview" onerror="this.src='{{ asset('app-logo.png') }}';">
                                    </div>
                                    <div>
                                        <div class="fw-bold fs-7 text-dark">Active System Logo</div>
                                        <div class="text-muted fs-8">Rendered across super admin, tenant workspaces & candidate portals</div>
                                    </div>
                                </div>

                                <!-- Dropify Drag-and-Drop Uploader -->
                                <div class="form-group mb-0">
                                    <label class="cd-form-label" for="logo">Upload New Brand Logo</label>
                                    <input type="file" id="logo" name="logo" class="dropify"
                                        data-max-file-size="2M"
                                        data-allowed-file-extensions="png jpg jpeg webp svg"
                                        data-default-file="{{ $global->logo ? $global->logo_url : '' }}" />
                                    <span class="cd-help-text">Recommended resolution: 200×50 px with transparent background (PNG, SVG, or WebP)</span>
                                    <div class="invalid-feedback" id="error_logo"></div>

                                    @if ($global->logo)
                                        <div class="form-check form-check-custom form-check-solid form-check-sm mt-3 pt-2 border-top">
                                            <input class="form-check-input" type="checkbox" name="remove_logo" value="1" id="remove_logo" />
                                            <label class="form-check-label fs-8 text-gray-700 cursor-pointer" for="remove_logo">
                                                <i class="fas fa-undo-alt text-danger me-1"></i> Reset & revert to default system logo
                                            </label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ---------------------------------------------------------- -->
                <!-- TAB 2: REGIONAL & LOCALIZATION                             -->
                <!-- ---------------------------------------------------------- -->
                <div class="tab-pane fade" id="tab_localization" role="tabpanel">
                    <div class="row g-4">
                        <!-- Sub-Card 3: Currency & Financial Base -->
                        <div class="col-md-6">
                            <div class="cd-subcard h-100">
                                <div class="cd-subcard-header">
                                    <div class="cd-subcard-header-left">
                                        <div class="cd-subcard-icon">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </div>
                                        <div>
                                            <h3 class="cd-subcard-title">Platform Base Currency</h3>
                                            <p class="cd-subcard-subtitle">Global financial ledger currency for packages, billing & marketplace</p>
                                        </div>
                                    </div>
                                    <span class="badge badge-light-warning fw-semibold px-3 py-2">Financial Engine</span>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="cd-form-label required" for="currency_id">@lang('app.currency')</label>
                                    <select name="currency_id" id="currency_id" class="form-control cd-form-control select2" required>
                                        @foreach ($currencies as $item)
                                            <option @if ($item->id == $global->currency_id) selected @endif value="{{ $item->id }}">
                                                {{ $item->currency_code }} — {{ $item->currency_name }} ({{ $item->currency_symbol }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="cd-help-text">All subscription packages and talent coin transactions calculate based on this base currency</span>
                                    <div class="invalid-feedback" id="error_currency_id"></div>
                                </div>

                                <div class="alert alert-light-primary d-flex align-items-center p-3 rounded-3 mb-0">
                                    <i class="fas fa-info-circle fs-4 text-primary me-3"></i>
                                    <div class="fs-7 text-gray-700">
                                        Current Selected: <strong class="text-dark">{{ $global->currency->currency_code ?? 'USD' }} ({{ $global->currency->currency_symbol ?? '$' }})</strong>.
                                        Exchange rates are automatically indexed across multi-currency candidate rate cards.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sub-Card 4: System Language & Multi-Lingual Locale -->
                        <div class="col-md-6">
                            <div class="cd-subcard h-100">
                                <div class="cd-subcard-header">
                                    <div class="cd-subcard-header-left">
                                        <div class="cd-subcard-icon">
                                            <i class="fas fa-language"></i>
                                        </div>
                                        <div>
                                            <h3 class="cd-subcard-title">Default Platform Language</h3>
                                            <p class="cd-subcard-subtitle">Default system translation and UI typography locale</p>
                                        </div>
                                    </div>
                                    <span class="badge badge-light-primary fw-semibold px-3 py-2">i18n Engine</span>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="cd-form-label required" for="locale">@lang('modules.accountSettings.changeLanguage')</label>
                                    <select class="form-control cd-form-control select2" name="locale" id="locale" required>
                                        @foreach (($languageSettings ?? $languages ?? []) as $language)
                                            <option value="{{ $language->language_code }}"
                                                @if ($global->locale == $language->language_code) selected @endif>
                                                {{ $language->language_name }} ({{ strtoupper($language->language_code) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="cd-help-text">Determines default date formatting, numerals, and translation strings</span>
                                    <div class="invalid-feedback" id="error_locale"></div>
                                </div>

                                <div class="alert alert-light-success d-flex align-items-center p-3 rounded-3 mb-0">
                                    <i class="fas fa-check-circle fs-4 text-success me-3"></i>
                                    <div class="fs-7 text-gray-700">
                                        Active Locale: <strong class="text-dark">{{ strtoupper($global->locale ?? 'en') }}</strong>.
                                        Platform translation strings load dynamically with zero page-load latency.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ---------------------------------------------------------- -->
                <!-- TAB 3: SYSTEM SECURITY & POLICIES                          -->
                <!-- ---------------------------------------------------------- -->
                <div class="tab-pane fade" id="tab_security" role="tabpanel">
                    <div class="row g-4">
                        <!-- Sub-Card 5: Maintenance Mode Toggle -->
                        <div class="col-md-6">
                            <div class="cd-subcard h-100">
                                <div class="cd-subcard-header">
                                    <div class="cd-subcard-header-left">
                                        <div class="cd-subcard-icon">
                                            <i class="fas fa-tools"></i>
                                        </div>
                                        <div>
                                            <h3 class="cd-subcard-title">Maintenance & System Updates</h3>
                                            <p class="cd-subcard-subtitle">Control platform operational status during live patch deployments</p>
                                        </div>
                                    </div>
                                    <span class="badge badge-light-danger fw-semibold px-3 py-2">System Critical</span>
                                </div>

                                <div class="d-flex align-items-center justify-content-between p-4 bg-light rounded-3 mb-3 border">
                                    <div>
                                        <div class="fw-bold fs-6 text-dark">Platform Maintenance Mode</div>
                                        <div class="text-muted fs-7">When enabled, tenants see a professional maintenance window</div>
                                    </div>
                                    <div class="form-check form-switch form-check-custom form-check-solid">
                                        <input class="form-check-input h-25px w-45px cursor-pointer" type="checkbox"
                                            name="system_update" id="system_update" value="1"
                                            @if ($global->system_update == 1) checked @endif>
                                    </div>
                                </div>

                                <span class="cd-help-text">
                                    <i class="fas fa-info-circle text-primary me-1"></i>
                                    Leave enabled during standard operations. Toggle off only when running heavy data migrations or offline maintenance.
                                </span>
                            </div>
                        </div>

                        <!-- Sub-Card 6: Account Retention & Auto-Purge Policy -->
                        <div class="col-md-6">
                            <div class="cd-subcard h-100">
                                <div class="cd-subcard-header">
                                    <div class="cd-subcard-header-left">
                                        <div class="cd-subcard-icon">
                                            <i class="fas fa-user-clock"></i>
                                        </div>
                                        <div>
                                            <h3 class="cd-subcard-title">Account Retention & Auto-Purge</h3>
                                            <p class="cd-subcard-subtitle">GDPR-compliant scheduled deletion period for deactivated accounts</p>
                                        </div>
                                    </div>
                                    <span class="badge badge-light-primary fw-semibold px-3 py-2">Lifecycle Policy</span>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="cd-form-label required" for="delete_account_in">
                                        @lang('modules.accountSettings.numberDeleteAccountAutomatically')
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-trash-alt text-muted"></i>
                                        </span>
                                        <input type="number" min="0" max="9999" class="form-control cd-form-control border-start-0"
                                            value="{{ $global->delete_account_in }}" name="delete_account_in" id="delete_account_in"
                                            placeholder="e.g. 30">
                                        <select name="hoursDays" class="form-select cd-form-control" style="max-width: 140px;">
                                            <option @if ($global->delete_account_hour_day == 'hour') selected @endif value="hour">
                                                @lang('app.hours')
                                            </option>
                                            <option @if ($global->delete_account_hour_day == 'day') selected @endif value="day">
                                                @lang('app.days')
                                            </option>
                                        </select>
                                    </div>
                                    <span class="cd-help-text">Accounts scheduled for deletion will be completely purged after this timeframe</span>
                                    <div class="invalid-feedback" id="error_delete_account_in"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Sub-Card 7: Bot & Spam Protection (Google reCAPTCHA) -->
                        <div class="col-12">
                            <div class="cd-subcard mb-0">
                                <div class="cd-subcard-header">
                                    <div class="cd-subcard-header-left">
                                        <div class="cd-subcard-icon">
                                            <i class="fas fa-robot"></i>
                                        </div>
                                        <div>
                                            <h3 class="cd-subcard-title">Google reCAPTCHA v2 / v3 Protection</h3>
                                            <p class="cd-subcard-subtitle">Protects tenant registration, job applications, and contact forms from automated bot spam</p>
                                        </div>
                                    </div>
                                    <span class="badge badge-light-success fw-semibold px-3 py-2">Security Shield</span>
                                </div>

                                <div class="row g-3 align-items-center">
                                    <div class="col-lg-8">
                                        <div class="form-group mb-0">
                                            <label class="cd-form-label" for="google_recaptcha_key">Google reCAPTCHA Site Key</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0">
                                                    <i class="fab fa-google text-muted"></i>
                                                </span>
                                                <input type="password" class="form-control cd-form-control border-start-0 border-end-0"
                                                    id="google_recaptcha_key" name="google_recaptcha_key"
                                                    value="{{ $global->google_recaptcha_key }}" placeholder="Paste your 6L... Site Key here">
                                                <button class="btn btn-light-secondary border border-start-0" type="button" id="toggleRecaptchaKey">
                                                    <i class="fas fa-eye" id="recaptchaEyeIcon"></i>
                                                </button>
                                            </div>
                                            <div class="invalid-feedback" id="error_google_recaptcha_key"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="p-3 bg-light rounded-3 fs-8 text-gray-600 border">
                                            <i class="fas fa-shield-alt text-success me-1"></i>
                                            Leave empty if reCAPTCHA verification is disabled globally. Generate API keys from the <a href="https://www.google.com/recaptcha/admin" target="_blank" class="text-primary fw-semibold">Google reCAPTCHA Console</a>.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ---------------------------------------------------------- -->
                <!-- TAB 4: INVOICING & SOLD-BY ADDRESS                         -->
                <!-- ---------------------------------------------------------- -->
                <div class="tab-pane fade" id="tab_invoicing" role="tabpanel">
                    <div class="cd-subcard">
                        <div class="cd-subcard-header">
                            <div class="cd-subcard-header-left">
                                <div class="cd-subcard-icon">
                                    <i class="fas fa-file-invoice"></i>
                                </div>
                                <div>
                                    <h3 class="cd-subcard-title">Official Invoicing & Sold-By Legal Entity</h3>
                                    <p class="cd-subcard-subtitle">Authoritative seller details, tax registrations, and billing address printed on customer invoices</p>
                                </div>
                            </div>
                            <span class="badge badge-light-warning fw-semibold px-3 py-2">Statutory Invoice Entity</span>
                        </div>

                        <!-- Representative & Legal Entity -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="cd-form-label required" for="first_name">Signatory First Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-user-tie text-muted"></i></span>
                                        <input class="form-control cd-form-control border-start-0" value="{{ $sold_by_address->first_name ?? '' }}"
                                            type="text" id="first_name" name="first_name" placeholder="First Name" required>
                                    </div>
                                    <div class="invalid-feedback" id="error_first_name"></div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="cd-form-label" for="last_name">Signatory Last Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
                                        <input class="form-control cd-form-control border-start-0" value="{{ $sold_by_address->last_name ?? '' }}"
                                            type="text" id="last_name" name="last_name" placeholder="Last Name">
                                    </div>
                                    <div class="invalid-feedback" id="error_last_name"></div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="cd-form-label" for="organization">Registered Legal Entity</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-landmark text-muted"></i></span>
                                        <input class="form-control cd-form-control border-start-0" value="{{ $sold_by_address->organization ?? $global->company_name }}"
                                            type="text" id="organization" name="organization" placeholder="e.g. Jeroworks Technology Pvt Ltd">
                                    </div>
                                    <div class="invalid-feedback" id="error_organization"></div>
                                </div>
                            </div>
                        </div>

                        <hr class="border-gray-200 my-4">

                        <!-- Cascading Location Details -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="cd-form-label required" for="country">Country / Region</label>
                                    <select class="form-control cd-form-control select2" id="country" name="country" required>
                                        <option value="">Select Country</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}" @if ($countrie_id == $country->id) selected @endif>
                                                {{ $country->country_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="error_country"></div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="cd-form-label" for="state">State / Province</label>
                                    <select class="form-control cd-form-control select2" id="state" name="state_id" disabled>
                                        <option value="">Select State</option>
                                    </select>
                                    <div class="invalid-feedback" id="error_state_id"></div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="cd-form-label" for="city">City</label>
                                    <select class="form-control cd-form-control select2" id="city" name="city" disabled>
                                        <option value="">Select City</option>
                                    </select>
                                    <div class="invalid-feedback" id="error_city"></div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="cd-form-label" for="building">Building / Society / Suite</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-building text-muted"></i></span>
                                        <input class="form-control cd-form-control border-start-0" value="{{ $sold_by_address->building ?? '' }}"
                                            type="text" id="building" name="building" placeholder="Suite, Building, Tower">
                                    </div>
                                    <div class="invalid-feedback" id="error_building"></div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="cd-form-label" for="street">Street Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-road text-muted"></i></span>
                                        <input class="form-control cd-form-control border-start-0" value="{{ $sold_by_address->street ?? '' }}"
                                            type="text" id="street" name="street" placeholder="Road, Sector, Locality">
                                    </div>
                                    <div class="invalid-feedback" id="error_street"></div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="cd-form-label" for="postal_code">Postal / PIN Code</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-mail-bulk text-muted"></i></span>
                                        <input class="form-control cd-form-control border-start-0" value="{{ $sold_by_address->postal_code ?? '' }}"
                                            type="text" id="postal_code" name="postal_code" placeholder="e.g. 201301">
                                    </div>
                                    <div class="invalid-feedback" id="error_postal_code"></div>
                                </div>
                            </div>
                        </div>

                        <hr class="border-gray-200 my-4">

                        <!-- Statutory Tax Identifiers -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="cd-form-label" for="gstin" id="gstin_text">GSTIN / TAX ID</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-receipt text-muted"></i>
                                        </span>
                                        <input class="form-control cd-form-control border-start-0" value="{{ $sold_by_address->gstin ?? '' }}"
                                            type="text" id="gstin" name="gstin" placeholder="e.g. 07AAAAA0000A1Z5">
                                    </div>
                                    <span class="cd-help-text" id="gstin_help">Official business tax registration number printed on tax invoices</span>
                                    <div class="invalid-feedback" id="error_gstin"></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="cd-form-label" for="pan_no" id="pan_no_text">PAN No. / National Tax ID</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-id-card text-muted"></i>
                                        </span>
                                        <input class="form-control cd-form-control border-start-0" value="{{ $sold_by_address->pan_no ?? '' }}"
                                            type="text" id="pan_no" name="pan_no" placeholder="e.g. ABCDE1234F">
                                    </div>
                                    <span class="cd-help-text">Corporate PAN / National taxpayer identifier</span>
                                    <div class="invalid-feedback" id="error_pan_no"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ============================================================== -->
            <!-- BOTTOM AUDIT STRIP & STICKY ACTION BAR                         -->
            <!-- ============================================================== -->
            <div class="cd-bottom-action-card">
                <div class="cd-meta-strip">
                    <span class="cd-meta-item">
                        <i class="fas fa-clock"></i>
                        <span>Last Updated: <strong class="text-dark">{{ $global->updated_at ? $global->updated_at->format('M d, Y H:i') : 'Recently' }}</strong></span>
                    </span>
                    <span class="cd-meta-item">
                        <i class="fas fa-database"></i>
                        <span>Tenant Tenancy: <strong class="text-dark">Global Configuration (tenant_id = 0)</strong></span>
                    </span>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-sm btn-light-secondary px-4 py-2 text-gray-700" id="resetBottomBtn">
                        <i class="fas fa-undo me-1 text-gray-500"></i> @lang('app.reset')
                    </button>

                    <button type="button" class="btn btn-sm btn-primary px-5 py-2 fw-semibold" id="saveBottomBtn">
                        <span class="indicator-label">
                            <i class="fas fa-save me-1"></i> @lang('app.save')
                        </span>
                        <span class="indicator-progress">
                            <span class="spinner-border spinner-border-sm align-middle me-2"></span> Saving...
                        </span>
                    </button>
                </div>
            </div>

        </form>
    </div>
@endsection

@push('footer-script')
    <script src="{{ asset('assets/node_modules/dropify/dist/js/dropify.min.js') }}" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            // -----------------------------------------------------------------
            // 1. Initialize Dropify & Select2
            // -----------------------------------------------------------------
            $('.dropify').dropify({
                messages: {
                    default: '@lang("app.dragDrop")',
                    replace: '@lang("app.dragDropReplace")',
                    remove: '@lang("app.remove")',
                    error: '@lang("app.largeFile")'
                }
            });

            if ($.fn.select2) {
                $('.select2').select2({
                    width: '100%'
                });
            }

            // -----------------------------------------------------------------
            // 2. Google reCAPTCHA Key Visibility Eye Toggle
            // -----------------------------------------------------------------
            $('#toggleRecaptchaKey').on('click', function() {
                const keyInput = $('#google_recaptcha_key');
                const eyeIcon = $('#recaptchaEyeIcon');
                if (keyInput.attr('type') === 'password') {
                    keyInput.attr('type', 'text');
                    eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    keyInput.attr('type', 'password');
                    eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // -----------------------------------------------------------------
            // 3. Live Badge Updaters & Client-Side File Pre-Validation
            // -----------------------------------------------------------------
            $('#system_update').on('change', function() {
                const isOnline = $(this).is(':checked');
                const badge = $('#statusBadge');
                const badgeText = $('#statusBadgeText');

                if (isOnline) {
                    badge.removeClass('cd-pill-maintenance').addClass('cd-pill-online');
                    badgeText.text('System Online (Production)');
                } else {
                    badge.removeClass('cd-pill-online').addClass('cd-pill-maintenance');
                    badgeText.text('Maintenance Mode');
                }
            });

            $('#currency_id').on('change', function() {
                const selectedText = $(this).find('option:selected').text().trim();
                const codeMatch = selectedText.match(/^([A-Z]{3})/);
                const symbolMatch = selectedText.match(/\((.*?)\)$/);
                if (codeMatch && symbolMatch) {
                    $('#headerCurrencyBadgeText').text(codeMatch[1] + ' (' + symbolMatch[1] + ')');
                }
            });

            $('#locale').on('change', function() {
                const selectedVal = $(this).val();
                if (selectedVal) {
                    $('#headerLocaleBadgeText').text(selectedVal.toUpperCase());
                }
            });

            $('#logo').on('change', function() {
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'];
                    if (!validTypes.includes(file.type)) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Unsupported Image Type',
                            text: 'Please select a valid image file (PNG, JPG, SVG, or WebP).',
                            confirmButtonColor: '#009ef7'
                        });
                        return false;
                    }
                    if (file.size > 2 * 1024 * 1024) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'File Exceeds 2 MB',
                            text: 'The selected logo exceeds the 2 MB limit. Please compress or select a smaller image.',
                            confirmButtonColor: '#009ef7'
                        });
                        return false;
                    }
                }
            });

            // -----------------------------------------------------------------
            // 4. Robust Cascading Location Engine (Country -> State -> City)
            // -----------------------------------------------------------------
            const targetCountryId = "{{ $countrie_id ?? '' }}";
            const targetStateId = "{{ $state_id ?? '' }}";
            const targetCityId = "{{ $city_id ?? '' }}";

            function updateTaxLabels(countryId) {
                if (countryId == 101) { // India
                    $('#gstin_text').text('GSTIN');
                    $('#gstin').attr('placeholder', 'e.g. 07AAAAA0000A1Z5');
                    $('#gstin_help').text('Goods and Services Tax Identification Number (India)');
                    $('#pan_no_text').text('PAN No.');
                    $('#pan_no').attr('placeholder', 'e.g. ABCDE1234F');
                } else { // Global
                    $('#gstin_text').text('TAX ID / VAT Number');
                    $('#gstin').attr('placeholder', 'e.g. US-TAX-123456');
                    $('#gstin_help').text('Official tax/VAT registration number printed on tax invoices');
                    $('#pan_no_text').text('National Tax ID / Reg No.');
                    $('#pan_no').attr('placeholder', 'e.g. NAT-REG-98765');
                }
            }

            function fetchStates(countryId, stateToSelect, cityToSelect) {
                const stateSelect = $('#state');
                const citySelect = $('#city');

                stateSelect.empty().append('<option value="">Loading States...</option>').prop('disabled', true);
                citySelect.empty().append('<option value="">Select City</option>').prop('disabled', true);

                if (!countryId) {
                    stateSelect.empty().append('<option value="">Select State</option>');
                    return;
                }

                $.ajax({
                    url: '{{ route("get.states") }}',
                    type: 'GET',
                    data: { country: countryId },
                    success: function(response) {
                        stateSelect.empty().append('<option value="">Select State</option>');
                        if (response && response.length) {
                            response.forEach(function(state) {
                                stateSelect.append(new Option(state.name, state.id));
                            });
                            stateSelect.prop('disabled', false);

                            if (stateToSelect) {
                                stateSelect.val(stateToSelect).trigger('change.select2');
                                fetchCities(stateToSelect, cityToSelect);
                            }
                        }
                    },
                    error: function() {
                        stateSelect.empty().append('<option value="">Error loading states</option>');
                    }
                });
            }

            function fetchCities(stateId, cityToSelect) {
                const citySelect = $('#city');
                citySelect.empty().append('<option value="">Loading Cities...</option>').prop('disabled', true);

                if (!stateId) {
                    citySelect.empty().append('<option value="">Select City</option>');
                    return;
                }

                $.ajax({
                    url: '{{ route("get.cities") }}',
                    type: 'GET',
                    data: { state: stateId },
                    success: function(response) {
                        citySelect.empty().append('<option value="">Select City</option>');
                        if (response && response.length) {
                            response.forEach(function(city) {
                                citySelect.append(new Option(city.name, city.id));
                            });
                            citySelect.prop('disabled', false);

                            if (cityToSelect) {
                                citySelect.val(cityToSelect).trigger('change.select2');
                            }
                        }
                    },
                    error: function() {
                        citySelect.empty().append('<option value="">Error loading cities</option>');
                    }
                });
            }

            // User changes country
            $('#country').on('change', function() {
                const countryId = $(this).val();
                updateTaxLabels(countryId);
                fetchStates(countryId, null, null);
            });

            // User changes state
            $('#state').on('change', function() {
                const stateId = $(this).val();
                fetchCities(stateId, null);
            });

            // Initial deterministic load
            if (targetCountryId) {
                updateTaxLabels(targetCountryId);
                fetchStates(targetCountryId, targetStateId, targetCityId);
            }

            // -----------------------------------------------------------------
            // 5. AJAX Form Submission with Validation & SweetAlert2 Feedback
            // -----------------------------------------------------------------
            function clearValidationErrors() {
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
            }

            function displayValidationErrors(errors) {
                clearValidationErrors();
                let firstInvalidElement = null;
                let firstTabId = null;

                $.each(errors, function(field, messages) {
                    const inputElement = $('[name="' + field + '"]');
                    const errorContainer = $('#error_' + field);

                    if (inputElement.length) {
                        inputElement.addClass('is-invalid');
                        if (!firstInvalidElement) {
                            firstInvalidElement = inputElement;
                            const tabPane = inputElement.closest('.tab-pane');
                            if (tabPane.length) {
                                firstTabId = tabPane.attr('id');
                            }
                        }
                    }

                    if (errorContainer.length && messages && messages.length) {
                        errorContainer.text(messages[0]);
                    }
                });

                // Auto-switch to tab containing first error
                if (firstTabId) {
                    const tabTrigger = $('[data-bs-toggle="tab"][href="#' + firstTabId + '"]');
                    if (tabTrigger.length) {
                        tabTrigger.tab('show');
                    }
                }

                if (firstInvalidElement) {
                    $('html, body').animate({
                        scrollTop: firstInvalidElement.offset().top - 120
                    }, 300);
                    firstInvalidElement.focus();
                }
            }

            function setButtonLoading(isLoading) {
                const buttons = $('#saveTopBtn, #saveBottomBtn');
                if (isLoading) {
                    buttons.attr('data-kt-indicator', 'on').prop('disabled', true);
                } else {
                    buttons.removeAttr('data-kt-indicator').prop('disabled', false);
                }
            }

            function saveGlobalSettings() {
                clearValidationErrors();
                setButtonLoading(true);

                const form = document.getElementById('globalSettingsForm');
                const formData = new FormData(form);

                // Ensure maintenance mode checkbox sends explicit boolean/state
                if (!$('#system_update').is(':checked')) {
                    formData.set('system_update', '0');
                } else {
                    formData.set('system_update', '1');
                }

                $.ajax({
                    url: '{{ route("superadmin.global-settings.update", $global->id) }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    success: function(response) {
                        setButtonLoading(false);

                        if (response.status === 'success' || response.status === 'done') {
                            Swal.fire({
                                icon: 'success',
                                title: '@lang("menu.settings") Updated',
                                text: response.message || '@lang("messages.updatedSuccessfully")',
                                confirmButtonColor: '#009ef7'
                            });

                            if (response.data && response.data.logo_url) {
                                $('#currentLogoPreview').attr('src', response.data.logo_url);
                            }
                        } else if (response.status === 'fail' && response.errors) {
                            displayValidationErrors(response.errors);
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Failed',
                                text: 'Please review the highlighted fields and correct the errors.',
                                confirmButtonColor: '#f1416c'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Update Notice',
                                text: response.message || 'Unable to update settings.',
                                confirmButtonColor: '#f1416c'
                            });
                        }
                    },
                    error: function(xhr) {
                        setButtonLoading(false);

                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            displayValidationErrors(xhr.responseJSON.errors);
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: 'Please correct the highlighted form fields.',
                                confirmButtonColor: '#f1416c'
                            });
                        } else {
                            const errorMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'A server error occurred. Please try again.';
                            Swal.fire({
                                icon: 'error',
                                title: 'Server Error',
                                text: errorMsg,
                                confirmButtonColor: '#f1416c'
                            });
                        }
                    }
                });
            }

            $('#saveTopBtn, #saveBottomBtn').on('click', function(e) {
                e.preventDefault();
                saveGlobalSettings();
            });

            $('#resetBtn, #resetBottomBtn').on('click', function() {
                Swal.fire({
                    title: 'Reset Form?',
                    text: 'Are you sure you want to revert uncommitted form edits?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#009ef7',
                    cancelButtonColor: '#e2e8f0',
                    confirmButtonText: 'Yes, Reset',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            });
        });
    </script>
@endpush
