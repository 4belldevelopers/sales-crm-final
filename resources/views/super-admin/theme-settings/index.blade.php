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

    /* ─── Scoped Theme Settings Styles (100% Page Isolated) ─── */
    .theme-settings-container {
        width: 100%;
        max-width: 100%;
        padding: 0 16px 24px 16px !important;
        margin-top: 0 !important;
        box-sizing: border-box;
    }

    /* Tier 1: Top Header Toolbar Card */
    .theme-header-card {
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

    .theme-brand-icon {
        width: 40px;
        height: 40px;
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

    .theme-title {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.25;
        margin-bottom: 2px;
    }

    .theme-subtitle {
        font-size: 11.5px;
        color: #64748b;
        margin-bottom: 0;
    }

    .theme-pill-badge {
        display: inline-flex;
        align-items: center;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        line-height: 1.2;
    }

    .theme-pill-blue {
        background: #eff6ff;
        color: #009ef7;
        border: 1px solid #bfdbfe;
    }

    .theme-pill-slate {
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .theme-pill-green {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .theme-pill-amber {
        background: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    /* Tier 2: Sub-Tabs Filter Card */
    .theme-subtabs-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 8px 12px !important;
        margin-bottom: 14px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
    }

    .theme-nav-tabs {
        border-bottom: none !important;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .theme-nav-tabs .nav-link {
        border: 1px solid transparent !important;
        border-radius: 8px !important;
        padding: 7px 14px !important;
        font-size: 12.5px !important;
        font-weight: 600 !important;
        color: #64748b !important;
        background: transparent !important;
        transition: all 0.2s ease-in-out;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .theme-nav-tabs .nav-link:hover {
        color: #0f172a !important;
        background: #f1f5f9 !important;
    }

    .theme-nav-tabs .nav-link.active {
        color: #009ef7 !important;
        background: #eff6ff !important;
        border-color: #bfdbfe !important;
    }

    /* Tier 3: Content Cards & Sub-Cards */
    .theme-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 20px !important;
        margin-bottom: 16px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
    }

    .theme-sub-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 18px;
        height: 100%;
        transition: border-color 0.2s ease;
    }

    .theme-sub-card:hover {
        border-color: #cbd5e1;
    }

    .theme-section-title {
        font-size: 13.5px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .theme-section-desc {
        font-size: 11.5px;
        color: #64748b;
        margin-bottom: 16px;
    }

    /* Color Input Controls */
    .theme-color-input-group {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .theme-color-well-wrapper {
        position: relative;
        width: 52px;
        height: 44px;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid #e2e8f0;
        cursor: pointer;
        flex-shrink: 0;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }

    .theme-color-picker-native {
        position: absolute;
        top: -10px;
        left: -10px;
        width: 80px;
        height: 80px;
        cursor: pointer;
        border: none;
        padding: 0;
        opacity: 0;
    }

    .theme-color-preview-box {
        width: 100%;
        height: 100%;
        transition: background-color 0.15s ease;
    }

    .theme-hex-input {
        height: 44px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        padding: 0 14px;
        font-size: 14px;
        font-weight: 600;
        font-family: 'SFMono-Regular', Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        color: #0f172a;
        background: #ffffff;
        width: 140px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .theme-hex-input:focus {
        border-color: #009ef7;
        outline: none;
        box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.15);
    }

    /* Preset Palettes Grid */
    .theme-palettes-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 10px;
    }

    .theme-palette-chip {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .theme-palette-chip:hover {
        border-color: #009ef7;
        background: #f0fdf4;
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    }

    .theme-palette-chip.active {
        border-color: #009ef7 !important;
        background: #eff6ff !important;
        box-shadow: 0 0 0 2px rgba(0, 158, 247, 0.2);
    }

    .theme-palette-dot {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        flex-shrink: 0;
        border: 1px solid rgba(0,0,0,0.1);
    }

    .theme-palette-info {
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .theme-palette-name {
        font-size: 11.5px;
        font-weight: 600;
        color: #0f172a;
        line-height: 1.2;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    .theme-palette-hex {
        font-size: 10.5px;
        color: #64748b;
        font-family: monospace;
    }

    /* Ace Editor Styles */
    .theme-ace-wrapper {
        border: 1px solid #334155;
        border-radius: 8px;
        overflow: hidden;
        background: #141414;
    }

    .theme-ace-toolbar {
        background: #1e1e1e;
        border-bottom: 1px solid #334155;
        padding: 8px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
    }

    .theme-ace-title {
        color: #e2e8f0;
        font-size: 11.5px;
        font-weight: 600;
        font-family: monospace;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .theme-snippet-btn {
        background: #2d3748;
        color: #93c5fd;
        border: 1px solid #4a5568;
        border-radius: 6px;
        padding: 3px 8px;
        font-size: 11px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .theme-snippet-btn:hover {
        background: #3b82f6;
        color: #ffffff;
        border-color: #3b82f6;
    }

    .theme-ace-editor {
        width: 100%;
        height: 420px;
        font-size: 13px !important;
        font-family: 'SFMono-Regular', Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace !important;
    }

    /* Media Upload Zone */
    .theme-upload-zone {
        border: 2px dashed #cbd5e1;
        border-radius: 10px;
        background: #ffffff;
        padding: 24px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }

    .theme-upload-zone:hover,
    .theme-upload-zone.dragover {
        border-color: #009ef7;
        background: #eff6ff;
    }

    .theme-upload-icon {
        font-size: 36px;
        color: #94a3b8;
        margin-bottom: 8px;
    }

    .theme-upload-title {
        font-size: 13px;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .theme-upload-meta {
        font-size: 11.5px;
        color: #64748b;
    }

    .theme-bg-preview-card {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        max-width: 480px;
        margin: 16px auto 0 auto;
        background: #f1f5f9;
    }

    .theme-bg-preview-img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        display: block;
    }

    /* Live Preview Mockup */
    .theme-preview-container {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
    }

    .theme-preview-header {
        background: #1e1e2d;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: #ffffff;
    }

    .theme-preview-body {
        display: flex;
        min-height: 300px;
        background: #f8fafc;
    }

    .theme-preview-sidebar {
        width: 180px;
        background: #ffffff;
        border-right: 1px solid #e2e8f0;
        padding: 14px 10px;
        flex-shrink: 0;
    }

    .theme-preview-menu-item {
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        color: #64748b;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .theme-preview-menu-item.active {
        background: var(--preview-primary-bg, #eff6ff);
        color: var(--preview-primary-color, #009ef7);
        font-weight: 600;
    }

    .theme-preview-content {
        flex: 1;
        padding: 16px;
    }

    .theme-preview-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 14px;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .theme-preview-btn {
        background-color: var(--preview-primary-color, #009ef7);
        color: #ffffff;
        border: none;
        border-radius: 6px;
        padding: 6px 14px;
        font-size: 12px;
        font-weight: 600;
        cursor: default;
        transition: background-color 0.15s ease;
    }

    .theme-preview-front-hero {
        background-size: cover;
        background-position: center;
        padding: 36px 20px;
        text-align: center;
        color: #ffffff;
        border-radius: 8px;
        margin-bottom: 16px;
        transition: background-image 0.25s ease;
    }

    .theme-preview-job-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
        transition: all 0.15s ease;
    }

    .theme-preview-job-card:hover {
        border-color: var(--preview-primary-color, #009ef7);
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    }
</style>

<!-- Hidden legacy toolbar anchor to suppress Metronic default spacing -->
<div id="kt_toolbar" style="display:none!important;"></div>

<div class="theme-settings-container">
    <form id="createForm" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="remove_image" id="remove_image" value="0">
        <textarea name="admin_custom_css" id="admin_custom_css_textarea" style="display:none;">{{ $adminTheme->admin_custom_css }}</textarea>
        <textarea name="front_custom_css" id="front_custom_css_textarea" style="display:none;">{{ $adminTheme->front_custom_css }}</textarea>

        <!-- ─── TIER 1: Top Header Toolbar Card ─── -->
        <div class="theme-header-card">
            <div class="d-flex align-items-center gap-3">
                <div class="theme-brand-icon">
                    <i class="fa-solid fa-palette"></i>
                </div>
                <div>
                    <h1 class="theme-title">@lang('menu.themeSettings') & Enterprise Branding</h1>
                    <p class="theme-subtitle">Configure primary corporate brand color, custom stylesheets, and background imagery</p>
                </div>
            </div>
            <div class="d-flex align-items-center flex-wrap gap-2">
                <span class="theme-pill-badge theme-pill-blue">
                    <i class="fa-solid fa-globe me-1"></i> Super Admin Global Scope
                </span>
                <span class="theme-pill-badge theme-pill-slate" id="current_color_badge">
                    <span class="theme-palette-dot me-1" id="badge_dot" style="background-color: {{ $adminTheme->primary_color ?? '#009ef7' }}; width: 12px; height: 12px;"></span>
                    <span id="badge_hex">{{ strtoupper($adminTheme->primary_color ?? '#009EF7') }}</span>
                </span>
                <span class="theme-pill-badge theme-pill-green" id="contrast_badge">
                    <i class="fa-solid fa-check-circle me-1"></i> WCAG AA Pass
                </span>
                <button type="button" class="btn btn-sm btn-light-danger ms-2" id="btn-reset-theme">
                    <i class="fa-solid fa-arrow-rotate-left me-1"></i> Reset to Default
                </button>
                <button type="button" class="btn btn-sm btn-primary" id="save-form">
                    <span class="indicator-label">
                        <i class="fa-solid fa-cloud-arrow-up me-1"></i> @lang('app.save') Changes
                    </span>
                    <span class="indicator-progress">
                        Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                </button>
            </div>
        </div>

        <!-- ─── TIER 2: Sub-Tabs Filter Card ─── -->
        <div class="theme-subtabs-card">
            <ul class="theme-nav-tabs nav" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#pane-branding" role="tab">
                        <i class="fa-solid fa-swatchbook"></i> Primary Branding & Palettes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#pane-admin-css" role="tab">
                        <i class="fa-solid fa-code"></i> @lang('modules.themeSettings.adminPanelCustomCss')
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#pane-front-css" role="tab">
                        <i class="fa-solid fa-laptop-code"></i> @lang('modules.themeSettings.frontSiteCustomCss')
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#pane-background" role="tab">
                        <i class="fa-solid fa-image"></i> Brand Background Image
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#pane-preview" role="tab">
                        <i class="fa-solid fa-eye"></i> Live ATS Preview
                    </a>
                </li>
            </ul>
            <div class="d-none d-md-flex align-items-center gap-2">
                <span class="text-muted fs-8">100% Client-First Reactive</span>
            </div>
        </div>

        <!-- ─── TIER 3: Tab Contents & Sub-Cards Stream ─── -->
        <div class="tab-content">
            <!-- TAB 1: Primary Branding & Palettes -->
            <div class="tab-pane fade show active" id="pane-branding" role="tabpanel">
                <div class="row g-4">
                    <!-- Sub-Card 1: Custom Color Selector -->
                    <div class="col-xl-5 col-lg-6">
                        <div class="theme-card h-100">
                            <h3 class="theme-section-title">
                                <i class="fa-solid fa-eye-dropper text-primary"></i> Primary Color Selector
                            </h3>
                            <p class="theme-section-desc">Select the primary brand hue used across navigation, buttons, and badges.</p>

                            <div class="theme-color-input-group">
                                <div class="theme-color-well-wrapper" id="color_well_wrapper" title="Click to open color palette wheel">
                                    <div class="theme-color-preview-box" id="color_preview_box" style="background-color: {{ $adminTheme->primary_color ?? '#009ef7' }};"></div>
                                    <input type="color" id="native_color_picker" class="theme-color-picker-native" value="{{ $adminTheme->primary_color ?? '#009ef7' }}">
                                </div>
                                <input type="text" name="primary_color" id="primary_color_input" class="theme-hex-input" value="{{ $adminTheme->primary_color ?? '#009ef7' }}" maxlength="7" autocomplete="off" spellcheck="false">
                                <button type="button" class="btn btn-sm btn-light-primary" id="btn_pick_color" title="Open Native Color Wheel">
                                    <i class="fa-solid fa-pipette me-1"></i> Pick Color
                                </button>
                            </div>

                            <!-- Color Values Card -->
                            <div class="theme-sub-card mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fs-8 fw-bold text-muted text-uppercase">RGB Representation</span>
                                    <span class="fs-8 fw-bold font-monospace text-dark" id="rgb_text">rgb(0, 158, 247)</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fs-8 fw-bold text-muted text-uppercase">HSL Representation</span>
                                    <span class="fs-8 fw-bold font-monospace text-dark" id="hsl_text">hsl(202, 100%, 48%)</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fs-8 fw-bold text-muted text-uppercase">WCAG Contrast Ratio</span>
                                    <span class="fs-8 fw-bold text-success" id="contrast_ratio_text">4.52:1 (AA Normal Text)</span>
                                </div>
                                <div class="p-2 rounded text-center transition-all" id="contrast_sample_box" style="background-color: {{ $adminTheme->primary_color ?? '#009ef7' }}; color: #ffffff; font-size: 11px; font-weight: 600; border: 1px solid rgba(0,0,0,0.08);">
                                    <span id="contrast_sample_text">Sample Button Text (White on Primary)</span>
                                </div>
                            </div>

                            <!-- Quick Primary Swatches -->
                            <div>
                                <label class="fs-8 fw-bold text-muted text-uppercase mb-2 d-block">Quick Monochromatic Shades</label>
                                <div class="d-flex gap-2" id="shade_swatches_container">
                                    <!-- Populated dynamically via JS -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sub-Card 2: Curated Enterprise Palettes -->
                    <div class="col-xl-7 col-lg-6">
                        <div class="theme-card h-100">
                            <h3 class="theme-section-title">
                                <i class="fa-solid fa-layer-group text-primary"></i> Curated Enterprise SaaS Palettes
                            </h3>
                            <p class="theme-section-desc">Click any pre-tested, accessible color scheme to apply it immediately.</p>

                            <div class="theme-palettes-grid">
                                <div class="theme-palette-chip" data-color="#009ef7" data-name="Metronic Blue">
                                    <span class="theme-palette-dot" style="background-color: #009ef7;"></span>
                                    <div class="theme-palette-info">
                                        <span class="theme-palette-name">Metronic Blue</span>
                                        <span class="theme-palette-hex">#009EF7</span>
                                    </div>
                                </div>
                                <div class="theme-palette-chip" data-color="#3699ff" data-name="Royal Indigo">
                                    <span class="theme-palette-dot" style="background-color: #3699ff;"></span>
                                    <div class="theme-palette-info">
                                        <span class="theme-palette-name">Royal Indigo</span>
                                        <span class="theme-palette-hex">#3699FF</span>
                                    </div>
                                </div>
                                <div class="theme-palette-chip" data-color="#10b981" data-name="Emerald Teal">
                                    <span class="theme-palette-dot" style="background-color: #10b981;"></span>
                                    <div class="theme-palette-info">
                                        <span class="theme-palette-name">Emerald Teal</span>
                                        <span class="theme-palette-hex">#10B981</span>
                                    </div>
                                </div>
                                <div class="theme-palette-chip" data-color="#7239ea" data-name="Purple Violet">
                                    <span class="theme-palette-dot" style="background-color: #7239ea;"></span>
                                    <div class="theme-palette-info">
                                        <span class="theme-palette-name">Purple Violet</span>
                                        <span class="theme-palette-hex">#7239EA</span>
                                    </div>
                                </div>
                                <div class="theme-palette-chip" data-color="#f59e0b" data-name="Amber Flame">
                                    <span class="theme-palette-dot" style="background-color: #f59e0b;"></span>
                                    <div class="theme-palette-info">
                                        <span class="theme-palette-name">Amber Flame</span>
                                        <span class="theme-palette-hex">#F59E0B</span>
                                    </div>
                                </div>
                                <div class="theme-palette-chip" data-color="#f1416c" data-name="Crimson Rose">
                                    <span class="theme-palette-dot" style="background-color: #f1416c;"></span>
                                    <div class="theme-palette-info">
                                        <span class="theme-palette-name">Crimson Rose</span>
                                        <span class="theme-palette-hex">#F1416C</span>
                                    </div>
                                </div>
                                <div class="theme-palette-chip" data-color="#1e1e2d" data-name="Dark Slate">
                                    <span class="theme-palette-dot" style="background-color: #1e1e2d;"></span>
                                    <div class="theme-palette-info">
                                        <span class="theme-palette-name">Dark Slate</span>
                                        <span class="theme-palette-hex">#1E1E2D</span>
                                    </div>
                                </div>
                                <div class="theme-palette-chip" data-color="#0dcaf0" data-name="Cyan Modern">
                                    <span class="theme-palette-dot" style="background-color: #0dcaf0;"></span>
                                    <div class="theme-palette-info">
                                        <span class="theme-palette-name">Cyan Modern</span>
                                        <span class="theme-palette-hex">#0DCAF0</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 p-3 bg-light-info rounded border border-info border-dashed">
                                <div class="d-flex align-items-center">
                                    <i class="fa-solid fa-circle-info text-info fs-3 me-3"></i>
                                    <div class="fs-8 text-gray-700">
                                        The selected primary color is automatically compiled into <code>--main-color</code> and CSS variable tokens across all Super Admin views, active sidebar links, primary buttons, and metric widgets.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: Admin Custom CSS -->
            <div class="tab-pane fade" id="pane-admin-css" role="tabpanel">
                <div class="theme-card">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div>
                            <h3 class="theme-section-title mb-1">
                                <i class="fa-solid fa-code text-primary"></i> @lang('modules.themeSettings.adminPanelCustomCss')
                            </h3>
                            <p class="theme-section-desc mb-0">Custom stylesheet overrides applied exclusively to the Super Admin control panel.</p>
                        </div>
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <span class="fs-8 text-muted">Snippets:</span>
                            <button type="button" class="theme-snippet-btn" data-target="admin" data-snippet="/* Custom Slim Scrollbars */&#10;::-webkit-scrollbar { width: 6px; height: 6px; }&#10;::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }">
                                + Slim Scrollbar
                            </button>
                            <button type="button" class="theme-snippet-btn" data-target="admin" data-snippet="/* Compact Table Rows */&#10;.table tbody td { padding-top: 8px !important; padding-bottom: 8px !important; }">
                                + Compact Rows
                            </button>
                            <button type="button" class="theme-snippet-btn" data-target="admin" data-snippet="/* Rounded Card Corners */&#10;.card { border-radius: 12px !important; }">
                                + Rounded Cards
                            </button>
                            <button type="button" class="theme-snippet-btn" data-target="admin" data-snippet="/* High Contrast Sidebar */&#10;.aside-dark { background-color: #111827 !important; }">
                                + Contrast Sidebar
                            </button>
                        </div>
                    </div>

                    <div class="theme-ace-wrapper">
                        <div class="theme-ace-toolbar">
                            <div class="theme-ace-title">
                                <i class="fa-brands fa-css3-alt text-warning"></i> admin_custom.css (Twilight Mode)
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge badge-light-secondary fs-9" id="admin_cursor_text">Ln 1, Col 1</span>
                                <span class="badge badge-light-secondary fs-9" id="admin_stats_text">Lines: 0 | Chars: 0</span>
                                <button type="button" class="btn btn-xs btn-light-primary py-1 px-2 btn-copy-editor" data-target="admin" title="Copy CSS to clipboard">
                                    <i class="fa-solid fa-copy me-1"></i> Copy
                                </button>
                                <button type="button" class="btn btn-xs btn-light-danger py-1 px-2 btn-clear-editor" data-target="admin" title="Clear editor content">
                                    <i class="fa-solid fa-eraser me-1"></i> Clear
                                </button>
                            </div>
                        </div>
                        <div id="admin_css_editor" class="theme-ace-editor"></div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: Front Custom CSS -->
            <div class="tab-pane fade" id="pane-front-css" role="tabpanel">
                <div class="theme-card">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div>
                            <h3 class="theme-section-title mb-1">
                                <i class="fa-solid fa-laptop-code text-primary"></i> @lang('modules.themeSettings.frontSiteCustomCss')
                            </h3>
                            <p class="theme-section-desc mb-0">Custom stylesheet overrides injected into public landing pages, candidate job boards, and applicant portals.</p>
                        </div>
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <span class="fs-8 text-muted">Snippets:</span>
                            <button type="button" class="theme-snippet-btn" data-target="front" data-snippet="/* Smooth Font Rendering */&#10;body { -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }">
                                + Font Smoothing
                            </button>
                            <button type="button" class="theme-snippet-btn" data-target="front" data-snippet="/* Button Smooth Hover */&#10;.btn { transition: all 0.25s ease !important; }&#10;.btn:hover { transform: translateY(-1px); }">
                                + Button Glow
                            </button>
                            <button type="button" class="theme-snippet-btn" data-target="front" data-snippet="/* Clean Header Shadow */&#10;.navbar-header { box-shadow: 0 2px 10px rgba(0,0,0,0.06) !important; }">
                                + Header Shadow
                            </button>
                            <button type="button" class="theme-snippet-btn" data-target="front" data-snippet="/* Hero Section Gradient Overlay */&#10;.hero-section { background: linear-gradient(135deg, rgba(0,158,247,0.08) 0%, rgba(255,255,255,0) 100%) !important; }">
                                + Hero Gradient
                            </button>
                        </div>
                    </div>

                    <div class="theme-ace-wrapper">
                        <div class="theme-ace-toolbar">
                            <div class="theme-ace-title">
                                <i class="fa-brands fa-css3-alt text-warning"></i> front_custom.css (Twilight Mode)
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge badge-light-secondary fs-9" id="front_cursor_text">Ln 1, Col 1</span>
                                <span class="badge badge-light-secondary fs-9" id="front_stats_text">Lines: 0 | Chars: 0</span>
                                <button type="button" class="btn btn-xs btn-light-primary py-1 px-2 btn-copy-editor" data-target="front" title="Copy CSS to clipboard">
                                    <i class="fa-solid fa-copy me-1"></i> Copy
                                </button>
                                <button type="button" class="btn btn-xs btn-light-danger py-1 px-2 btn-clear-editor" data-target="front" title="Clear editor content">
                                    <i class="fa-solid fa-eraser me-1"></i> Clear
                                </button>
                            </div>
                        </div>
                        <div id="front_css_editor" class="theme-ace-editor"></div>
                    </div>
                </div>
            </div>

            <!-- TAB 4: Brand Background Image -->
            <div class="tab-pane fade" id="pane-background" role="tabpanel">
                <div class="theme-card">
                    <h3 class="theme-section-title">
                        <i class="fa-solid fa-image text-primary"></i> Front Site Hero Background Image
                    </h3>
                    <p class="theme-section-desc">Upload a high-resolution background image used on the front landing page and public authentication portals.</p>

                    <div class="theme-upload-zone" id="upload_zone">
                        <input type="file" name="image" id="theme_image_input" accept=".png,.jpg,.jpeg,.webp" style="display: none;">
                        <i class="fa-solid fa-cloud-arrow-up theme-upload-icon"></i>
                        <h4 class="theme-upload-title">Click to browse or drag & drop background image here</h4>
                        <p class="theme-upload-meta mb-0">Recommended dimensions: 1920×1080 px (Max: 4MB). Supported: PNG, JPG, JPEG, WEBP.</p>
                    </div>

                    <div class="theme-bg-preview-card" id="bg_preview_wrapper">
                        <img src="{{ $adminTheme->background_image_url }}" alt="Background Preview" class="theme-bg-preview-img" id="bg_preview_img">
                        <div class="p-3 bg-white d-flex justify-content-between align-items-center border-top flex-wrap gap-2">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="fs-8 fw-bold text-dark">Current Background Image</span>
                                    <span class="badge badge-light-secondary fs-9" id="bg_resolution_badge">Detecting...</span>
                                </div>
                                <span class="fs-9 text-muted d-block" id="bg_status_text">
                                    {{ is_null($adminTheme->home_background_image) ? 'Using System Default Asset (header-bg.jpg)' : 'Custom Uploaded Asset' }}
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ $adminTheme->background_image_url }}" target="_blank" class="btn btn-sm btn-light-primary" id="btn_view_full_image" title="Open full resolution image in new tab">
                                    <i class="fa-solid fa-up-right-from-square me-1"></i> View Full
                                </a>
                                <button type="button" class="btn btn-sm btn-light-danger" id="btn_remove_image" {{ is_null($adminTheme->home_background_image) ? 'style=display:none;' : '' }}>
                                    <i class="fa-solid fa-trash me-1"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 5: Live ATS Preview -->
            <div class="tab-pane fade" id="pane-preview" role="tabpanel">
                <div class="theme-card">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            <h3 class="theme-section-title mb-1">
                                <i class="fa-solid fa-eye text-primary"></i> Real-Time Interactive ATS Preview
                            </h3>
                            <p class="theme-section-desc mb-0">Preview how your brand colors and background image reflect across core Super Admin layout elements and candidate portals.</p>
                        </div>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-sm btn-primary btn-preview-mode active" data-mode="admin">
                                <i class="fa-solid fa-gauge me-1"></i> Admin Console
                            </button>
                            <button type="button" class="btn btn-sm btn-light btn-preview-mode" data-mode="front">
                                <i class="fa-solid fa-globe me-1"></i> Front Career Site
                            </button>
                        </div>
                    </div>

                    <div class="theme-preview-container">
                        <!-- MODE 1: Super Admin Console Preview -->
                        <div id="preview_admin_mode">
                            <!-- Preview Topbar -->
                            <div class="theme-preview-header">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge" style="background-color: var(--preview-primary-color, #009ef7); color: #fff; font-weight: 700; font-size: 11px;">JERO</span>
                                    <span class="fs-8 fw-bold">Super Admin Panel</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fs-9 text-gray-400">admin@jero.works</span>
                                    <span class="theme-palette-dot" style="background-color: var(--preview-primary-color, #009ef7); width: 18px; height: 18px;"></span>
                                </div>
                            </div>

                            <!-- Preview Body -->
                            <div class="theme-preview-body">
                                <!-- Preview Sidebar -->
                                <div class="theme-preview-sidebar">
                                    <div class="theme-preview-menu-item active">
                                        <i class="fa-solid fa-chart-line"></i> Dashboard
                                    </div>
                                    <div class="theme-preview-menu-item">
                                        <i class="fa-solid fa-building"></i> Companies
                                    </div>
                                    <div class="theme-preview-menu-item">
                                        <i class="fa-solid fa-user-tie"></i> Candidates
                                    </div>
                                    <div class="theme-preview-menu-item">
                                        <i class="fa-solid fa-briefcase"></i> Requisitions
                                    </div>
                                    <div class="theme-preview-menu-item">
                                        <i class="fa-solid fa-gear"></i> Settings
                                    </div>
                                </div>

                                <!-- Preview Main Area -->
                                <div class="theme-preview-content">
                                    <div class="row g-3 mb-3">
                                        <div class="col-sm-6">
                                            <div class="theme-preview-stat-card">
                                                <div>
                                                    <span class="fs-9 text-muted d-block text-uppercase fw-bold">Active Tenants</span>
                                                    <span class="fs-5 fw-bolder text-dark">1,482</span>
                                                </div>
                                                <div class="theme-brand-icon" style="background-color: var(--preview-primary-bg, #eff6ff); color: var(--preview-primary-color, #009ef7); border-color: var(--preview-primary-color, #009ef7);">
                                                    <i class="fa-solid fa-building"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="theme-preview-stat-card">
                                                <div>
                                                    <span class="fs-9 text-muted d-block text-uppercase fw-bold">Platform Applications</span>
                                                    <span class="fs-5 fw-bolder text-dark">48,910</span>
                                                </div>
                                                <div class="theme-brand-icon" style="background-color: var(--preview-primary-bg, #eff6ff); color: var(--preview-primary-color, #009ef7); border-color: var(--preview-primary-color, #009ef7);">
                                                    <i class="fa-solid fa-users"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Miniature ATS Table Mockup -->
                                    <div class="bg-white rounded border border-gray-200 p-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fs-8 fw-bold text-dark">Recent Tenant Registrations</span>
                                            <span class="badge" style="background-color: var(--preview-primary-bg, #eff6ff); color: var(--preview-primary-color, #009ef7); font-size: 10px; font-weight: 600;">Live Sync</span>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-sm align-middle fs-9 mb-0">
                                                <thead>
                                                    <tr class="text-muted text-uppercase fw-bold fs-9">
                                                        <th>Company</th>
                                                        <th>Plan</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="fw-bold text-dark">Apex Global Systems</td>
                                                        <td>Enterprise Tier</td>
                                                        <td><span class="badge" style="background-color: var(--preview-primary-color, #009ef7); color: #fff; font-size: 9px;">Active</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-bold text-dark">TechWave Innovations</td>
                                                        <td>Professional Plan</td>
                                                        <td><span class="badge" style="background-color: var(--preview-primary-color, #009ef7); color: #fff; font-size: 9px;">Active</span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="p-3 bg-white rounded border border-gray-200 d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="fs-8 fw-bold text-dark d-block">Enterprise Theme Synchronization</span>
                                            <span class="fs-9 text-muted">Changes saved here apply immediately to all active tenant sessions.</span>
                                        </div>
                                        <button type="button" class="theme-preview-btn">
                                            <i class="fa-solid fa-plus me-1"></i> Add Company
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- MODE 2: Candidate Career Site Preview -->
                        <div id="preview_front_mode" style="display: none; padding: 16px;">
                            <div class="theme-preview-front-hero" id="preview_front_hero" style="background-image: linear-gradient(rgba(15,23,42,0.65), rgba(15,23,42,0.75)), url('{{ $adminTheme->background_image_url }}');">
                                <span class="badge mb-2" style="background-color: var(--preview-primary-color, #009ef7); color: #ffffff; font-size: 11px;">JERO Careers Portal</span>
                                <h3 class="text-white fw-bolder fs-3 mb-2">Build Your Career With Leading Teams</h3>
                                <p class="text-gray-300 fs-8 mb-4 max-w-500px mx-auto">Explore high-impact positions across top enterprise staffing and engineering companies.</p>
                                <div class="d-inline-flex gap-2">
                                    <button type="button" class="theme-preview-btn">
                                        <i class="fa-solid fa-magnifying-glass me-1"></i> Search Open Roles
                                    </button>
                                </div>
                            </div>

                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="theme-preview-job-card">
                                        <div>
                                            <span class="fs-8 fw-bold text-dark d-block">Senior Cloud Architect</span>
                                            <span class="fs-9 text-muted">New York, NY · Full Time</span>
                                        </div>
                                        <button type="button" class="btn btn-xs btn-light-primary py-1 px-3 fs-9" style="color: var(--preview-primary-color, #009ef7); border-color: var(--preview-primary-color, #009ef7);">
                                            Apply
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="theme-preview-job-card">
                                        <div>
                                            <span class="fs-8 fw-bold text-dark d-block">Full Stack Tech Lead</span>
                                            <span class="fs-9 text-muted">San Francisco, CA · Remote</span>
                                        </div>
                                        <button type="button" class="btn btn-xs btn-light-primary py-1 px-3 fs-9" style="color: var(--preview-primary-color, #009ef7); border-color: var(--preview-primary-color, #009ef7);">
                                            Apply
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('footer-script')
    <!-- Local Ace Editor Assets -->
    <script src="{{ asset('assets/ace/ace.js') }}"></script>
    <script src="{{ asset('assets/ace/theme-twilight.js') }}"></script>
    <script src="{{ asset('assets/ace/mode-css.js') }}"></script>

    <script>
    (function () {
        'use strict';

        // ─── State & Editor Instances ───
        var adminEditor = null;
        var frontEditor = null;
        var defaultColor = '#009ef7';

        // ─── Color Helper Functions ───
        function hexToRgb(hex) {
            var cleanHex = hex.replace('#', '').trim();
            if (cleanHex.length === 3) {
                cleanHex = cleanHex.split('').map(function(c) { return c + c; }).join('');
            }
            if (cleanHex.length !== 6) return { r: 0, g: 158, b: 247 };
            var num = parseInt(cleanHex, 16);
            return {
                r: (num >> 16) & 255,
                g: (num >> 8) & 255,
                b: num & 255
            };
        }

        function rgbToHsl(r, g, b) {
            r /= 255; g /= 255; b /= 255;
            var max = Math.max(r, g, b), min = Math.min(r, g, b);
            var h, s, l = (max + min) / 2;

            if (max === min) {
                h = s = 0;
            } else {
                var d = max - min;
                s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
                switch (max) {
                    case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                    case g: h = (b - r) / d + 2; break;
                    case b: h = (r - g) / d + 4; break;
                }
                h /= 6;
            }
            return {
                h: Math.round(h * 360),
                s: Math.round(s * 100),
                l: Math.round(l * 100)
            };
        }

        function getLuminance(r, g, b) {
            var a = [r, g, b].map(function (v) {
                v /= 255;
                return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
            });
            return a[0] * 0.2126 + a[1] * 0.7152 + a[2] * 0.0722;
        }

        function updateColorDisplay(hex, skipNativePicker) {
            if (!hex || !/^#([0-9A-Fa-f]{3}|[0-9A-Fa-f]{6})$/.test(hex)) {
                return;
            }

            // Standardize to 6-digit hex uppercase
            var cleanHex = hex.toUpperCase();
            if (cleanHex.length === 4) {
                cleanHex = '#' + cleanHex[1] + cleanHex[1] + cleanHex[2] + cleanHex[2] + cleanHex[3] + cleanHex[3];
            }

            // Sync visual controls
            document.getElementById('color_preview_box').style.backgroundColor = cleanHex;
            if (!skipNativePicker) {
                document.getElementById('native_color_picker').value = cleanHex;
            }

            // Topbar badges
            document.getElementById('badge_dot').style.backgroundColor = cleanHex;
            document.getElementById('badge_hex').textContent = cleanHex;

            // Calculations
            var rgb = hexToRgb(cleanHex);
            var hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);
            var lum = getLuminance(rgb.r, rgb.g, rgb.b);
            var contrastWhite = (1.05) / (lum + 0.05);
            var contrastDark = (lum + 0.05) / 0.05;

            document.getElementById('rgb_text').textContent = 'rgb(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ')';
            document.getElementById('hsl_text').textContent = 'hsl(' + hsl.h + ', ' + hsl.s + '%, ' + hsl.l + '%)';

            var contrastBadge = document.getElementById('contrast_badge');
            var contrastRatioText = document.getElementById('contrast_ratio_text');

            if (contrastWhite >= 4.5) {
                contrastBadge.className = 'theme-pill-badge theme-pill-green';
                contrastBadge.innerHTML = '<i class="fa-solid fa-check-circle me-1"></i> WCAG AA Pass';
                contrastRatioText.className = 'fs-8 fw-bold text-success';
                contrastRatioText.textContent = contrastWhite.toFixed(2) + ':1 (AA Pass with White)';
            } else if (contrastDark >= 4.5) {
                contrastBadge.className = 'theme-pill-badge theme-pill-blue';
                contrastBadge.innerHTML = '<i class="fa-solid fa-check-circle me-1"></i> Optimal with Dark Text';
                contrastRatioText.className = 'fs-8 fw-bold text-primary';
                contrastRatioText.textContent = contrastDark.toFixed(2) + ':1 (AA Pass with Dark)';
            } else {
                contrastBadge.className = 'theme-pill-badge theme-pill-amber';
                contrastBadge.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-1"></i> Low Contrast Warning';
                contrastRatioText.className = 'fs-8 fw-bold text-warning';
                contrastRatioText.textContent = contrastWhite.toFixed(2) + ':1 (Low Contrast)';
            }

            // Update live preview CSS variables and active page DOM
            document.documentElement.style.setProperty('--preview-primary-color', cleanHex);
            document.documentElement.style.setProperty('--preview-primary-bg', cleanHex + '18');
            document.documentElement.style.setProperty('--main-color', cleanHex);
            document.documentElement.style.setProperty('--kt-primary', cleanHex);

            // Update interactive contrast sample text
            var sampleBox = document.getElementById('contrast_sample_box');
            var sampleText = document.getElementById('contrast_sample_text');
            if (sampleBox && sampleText) {
                sampleBox.style.backgroundColor = cleanHex;
                if (contrastWhite >= contrastDark) {
                    sampleBox.style.color = '#ffffff';
                    sampleText.textContent = 'Preview: White Text on Brand Color (Ratio ' + contrastWhite.toFixed(2) + ':1)';
                } else {
                    sampleBox.style.color = '#0f172a';
                    sampleText.textContent = 'Preview: Dark Text on Brand Color (Ratio ' + contrastDark.toFixed(2) + ':1)';
                }
            }

            // Update active state on preset chips
            document.querySelectorAll('.theme-palette-chip').forEach(function (chip) {
                if (chip.getAttribute('data-color').toUpperCase() === cleanHex) {
                    chip.classList.add('active');
                } else {
                    chip.classList.remove('active');
                }
            });

            // Update monochromatic shade swatches
            renderMonochromeSwatches(cleanHex);
        }

        function renderMonochromeSwatches(baseHex) {
            var container = document.getElementById('shade_swatches_container');
            if (!container) return;
            container.innerHTML = '';

            var shades = [0.2, 0.4, 0.6, 0.8, 1.0];
            var rgb = hexToRgb(baseHex);

            shades.forEach(function (alpha) {
                var solidR = Math.round(rgb.r * alpha + 255 * (1 - alpha));
                var solidG = Math.round(rgb.g * alpha + 255 * (1 - alpha));
                var solidB = Math.round(rgb.b * alpha + 255 * (1 - alpha));
                var solidHex = '#' + [solidR, solidG, solidB].map(function (x) {
                    var h = x.toString(16);
                    return h.length === 1 ? '0' + h : h;
                }).join('').toUpperCase();

                var swatch = document.createElement('div');
                swatch.style.width = '32px';
                swatch.style.height = '32px';
                swatch.style.borderRadius = '6px';
                swatch.style.backgroundColor = solidHex;
                swatch.style.border = '1px solid #cbd5e1';
                swatch.style.cursor = 'pointer';
                swatch.style.transition = 'transform 0.15s ease, box-shadow 0.15s ease';
                swatch.title = 'Click to apply shade: ' + solidHex + ' (' + Math.round(alpha * 100) + '%)';

                swatch.addEventListener('mouseenter', function() {
                    swatch.style.transform = 'scale(1.15)';
                    swatch.style.boxShadow = '0 2px 6px rgba(0,0,0,0.15)';
                });
                swatch.addEventListener('mouseleave', function() {
                    swatch.style.transform = 'scale(1.0)';
                    swatch.style.boxShadow = 'none';
                });
                swatch.addEventListener('click', function() {
                    var primaryInput = document.getElementById('primary_color_input');
                    primaryInput.value = solidHex;
                    updateColorDisplay(solidHex);
                });
                container.appendChild(swatch);
            });
        }

        // ─── Initialize Ace Code Editors ───
        function updateEditorStats(target, editor) {
            var val = editor.getValue();
            var lines = val ? val.split('\n').length : 0;
            var chars = val ? val.length : 0;
            var statsEl = document.getElementById(target + '_stats_text');
            if (statsEl) {
                statsEl.textContent = 'Lines: ' + lines + ' | Chars: ' + chars;
            }
        }

        function updateCursorStats(target, editor) {
            var pos = editor.getCursorPosition();
            var cursorEl = document.getElementById(target + '_cursor_text');
            if (cursorEl) {
                cursorEl.textContent = 'Ln ' + (pos.row + 1) + ', Col ' + (pos.column + 1);
            }
        }

        var DRAFT_STORAGE_KEY = 'jero_superadmin_theme_draft';
        var draftTimer = null;
        var isMutationInFlight = false;
        var syncChannel = ('BroadcastChannel' in window) ? new BroadcastChannel('jero_theme_settings_sync') : null;

        function generateIdempotencyUUID() {
            if (window.crypto && window.crypto.randomUUID) {
                return window.crypto.randomUUID();
            }
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
                var r = Math.random() * 16 | 0, v = c === 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
        }

        function scheduleDraftSave() {
            clearTimeout(draftTimer);
            draftTimer = setTimeout(function () {
                try {
                    var primaryInput = document.getElementById('primary_color_input');
                    var draftData = {
                        primary_color: primaryInput ? primaryInput.value : '',
                        admin_custom_css: adminEditor ? adminEditor.getValue() : '',
                        front_custom_css: frontEditor ? frontEditor.getValue() : '',
                        saved_at: new Date().toISOString()
                    };
                    sessionStorage.setItem(DRAFT_STORAGE_KEY, JSON.stringify(draftData));
                } catch (e) {}
            }, 400);
        }

        function restoreDraft() {
            try {
                var rawDraft = sessionStorage.getItem(DRAFT_STORAGE_KEY);
                if (!rawDraft) return;
                var draft = JSON.parse(rawDraft);
                if (draft) {
                    var primaryInput = document.getElementById('primary_color_input');
                    if (draft.primary_color && primaryInput) {
                        primaryInput.value = draft.primary_color;
                        updateColorDisplay(draft.primary_color);
                    }
                    if (draft.admin_custom_css !== undefined && adminEditor) {
                        adminEditor.setValue(draft.admin_custom_css, -1);
                        var adminTa = document.getElementById('admin_custom_css_textarea');
                        if (adminTa) adminTa.value = draft.admin_custom_css;
                        updateEditorStats('admin', adminEditor);
                    }
                    if (draft.front_custom_css !== undefined && frontEditor) {
                        frontEditor.setValue(draft.front_custom_css, -1);
                        var frontTa = document.getElementById('front_custom_css_textarea');
                        if (frontTa) frontTa.value = draft.front_custom_css;
                        updateEditorStats('front', frontEditor);
                    }
                }
            } catch (e) {}
        }

        function clearDraft() {
            try {
                sessionStorage.removeItem(DRAFT_STORAGE_KEY);
            } catch (e) {}
        }

        function initAceEditors() {
            try {
                if (window.ace) {
                    // Admin CSS Editor
                    adminEditor = ace.edit('admin_css_editor');
                    adminEditor.setTheme('ace/theme/twilight');
                    adminEditor.session.setMode('ace/mode/css');
                    adminEditor.setShowPrintMargin(false);
                    adminEditor.session.setUseWrapMode(true);
                    adminEditor.setValue(document.getElementById('admin_custom_css_textarea').value || '', -1);

                    adminEditor.session.on('change', function () {
                        var val = adminEditor.getValue();
                        document.getElementById('admin_custom_css_textarea').value = val;
                        updateEditorStats('admin', adminEditor);
                        scheduleDraftSave();
                    });
                    adminEditor.selection.on('changeCursor', function () {
                        updateCursorStats('admin', adminEditor);
                    });
                    updateEditorStats('admin', adminEditor);
                    updateCursorStats('admin', adminEditor);

                    // Front CSS Editor
                    frontEditor = ace.edit('front_css_editor');
                    frontEditor.setTheme('ace/theme/twilight');
                    frontEditor.session.setMode('ace/mode/css');
                    frontEditor.setShowPrintMargin(false);
                    frontEditor.session.setUseWrapMode(true);
                    frontEditor.setValue(document.getElementById('front_custom_css_textarea').value || '', -1);

                    frontEditor.session.on('change', function () {
                        var val = frontEditor.getValue();
                        document.getElementById('front_custom_css_textarea').value = val;
                        updateEditorStats('front', frontEditor);
                        scheduleDraftSave();
                    });
                    frontEditor.selection.on('changeCursor', function () {
                        updateCursorStats('front', frontEditor);
                    });
                    updateEditorStats('front', frontEditor);
                    updateCursorStats('front', frontEditor);
                }
            } catch (err) {
                console.warn('Ace Editor Initialization Warning:', err);
            }
        }

        // ─── DOM Ready Bindings ───
        document.addEventListener('DOMContentLoaded', function () {
            var primaryInput = document.getElementById('primary_color_input');
            var nativePicker = document.getElementById('native_color_picker');
            var colorWell = document.getElementById('color_well_wrapper');
            var pickBtn = document.getElementById('btn_pick_color');

            // Listen for cross-tab theme updates
            if (syncChannel) {
                syncChannel.onmessage = function (event) {
                    if (!event.data) return;
                    if (event.data.type === 'THEME_UPDATED') {
                        if (primaryInput && event.data.primary_color) {
                            primaryInput.value = event.data.primary_color;
                            updateColorDisplay(event.data.primary_color);
                        }
                        if (event.data.background_image_url) {
                            var previewImg = document.getElementById('bg_preview_img');
                            var viewFullBtn = document.getElementById('btn_view_full_image');
                            if (previewImg) previewImg.src = event.data.background_image_url;
                            if (viewFullBtn) viewFullBtn.href = event.data.background_image_url;
                            updateImageResolution(event.data.background_image_url);
                        }
                    }
                };
            }

            // Initialize color
            var initialColor = primaryInput.value || defaultColor;
            updateColorDisplay(initialColor);

            // Native color picker change
            nativePicker.addEventListener('input', function () {
                primaryInput.value = this.value.toUpperCase();
                updateColorDisplay(this.value, true);
                scheduleDraftSave();
            });

            // Hex input change & typing
            primaryInput.addEventListener('input', function () {
                var val = this.value.trim();
                if (!val) return;
                if (val.charAt(0) !== '#') {
                    val = '#' + val;
                }
                val = '#' + val.substring(1).replace(/[^0-9A-Fa-f]/g, '').substring(0, 6);
                this.value = val.toUpperCase();
                if (/^#([0-9A-Fa-f]{3}|[0-9A-Fa-f]{6})$/.test(val)) {
                    updateColorDisplay(val);
                    scheduleDraftSave();
                }
            });

            primaryInput.addEventListener('blur', function () {
                var val = this.value.trim().toUpperCase();
                if (val.length === 4 && val.charAt(0) === '#') {
                    val = '#' + val[1] + val[1] + val[2] + val[2] + val[3] + val[3];
                    this.value = val;
                    updateColorDisplay(val);
                    scheduleDraftSave();
                }
            });

            // Color well & button click triggers native picker
            colorWell.addEventListener('click', function () {
                nativePicker.click();
            });
            if (pickBtn) {
                pickBtn.addEventListener('click', function () {
                    nativePicker.click();
                });
            }

            // Preset palette chip clicks
            document.querySelectorAll('.theme-palette-chip').forEach(function (chip) {
                chip.addEventListener('click', function () {
                    var color = this.getAttribute('data-color');
                    primaryInput.value = color.toUpperCase();
                    updateColorDisplay(color);
                    scheduleDraftSave();
                });
            });

            // Initialize Ace Editors
            initAceEditors();

            // Restore draft if present
            restoreDraft();

            // Refresh Ace editors on tab switch to prevent layout clipping
            document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(function (tabEl) {
                tabEl.addEventListener('shown.bs.tab', function () {
                    if (adminEditor) adminEditor.resize();
                    if (frontEditor) frontEditor.resize();
                });
            });

            // Snippet insertion buttons
            document.querySelectorAll('.theme-snippet-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var target = this.getAttribute('data-target');
                    var snippet = this.getAttribute('data-snippet');
                    var editor = (target === 'admin') ? adminEditor : frontEditor;

                    if (editor) {
                        editor.session.insert(editor.getCursorPosition(), '\n' + snippet + '\n');
                        editor.focus();
                    }
                });
            });

            // Copy Editor Content Button
            document.querySelectorAll('.btn-copy-editor').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var target = this.getAttribute('data-target');
                    var editor = (target === 'admin') ? adminEditor : frontEditor;
                    if (editor) {
                        var val = editor.getValue();
                        if (!val) {
                            Swal.fire({
                                title: 'Editor Empty',
                                text: 'There is no CSS code to copy.',
                                icon: 'info',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            return;
                        }
                        navigator.clipboard.writeText(val).then(function () {
                            Swal.fire({
                                title: 'Copied!',
                                text: 'CSS copied to clipboard.',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        });
                    }
                });
            });

            // Clear Editor Content Button
            document.querySelectorAll('.btn-clear-editor').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var target = this.getAttribute('data-target');
                    var editor = (target === 'admin') ? adminEditor : frontEditor;
                    var label = (target === 'admin') ? 'Admin Custom CSS' : 'Front Site Custom CSS';
                    Swal.fire({
                        title: 'Clear ' + label + '?',
                        text: 'This will erase all CSS code inside this editor.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#f1416c',
                        confirmButtonText: 'Yes, Clear Code',
                        cancelButtonText: 'Cancel'
                    }).then(function (res) {
                        if (res.isConfirmed && editor) {
                            editor.setValue('', -1);
                            var textareaId = target + '_custom_css_textarea';
                            document.getElementById(textareaId).value = '';
                            updateEditorStats(target, editor);
                        }
                    });
                });
            });

            // ─── Drag & Drop Image Upload ───
            var uploadZone = document.getElementById('upload_zone');
            var fileInput = document.getElementById('theme_image_input');
            var previewImg = document.getElementById('bg_preview_img');
            var btnRemoveImage = document.getElementById('btn_remove_image');
            var removeImageFlag = document.getElementById('remove_image');

            uploadZone.addEventListener('click', function () {
                fileInput.click();
            });

            ['dragenter', 'dragover'].forEach(function (eventName) {
                uploadZone.addEventListener(eventName, function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    uploadZone.classList.add('dragover');
                }, false);
            });

            ['dragleave', 'drop'].forEach(function (eventName) {
                uploadZone.addEventListener(eventName, function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    uploadZone.classList.remove('dragover');
                }, false);
            });

            uploadZone.addEventListener('drop', function (e) {
                var dt = e.dataTransfer;
                var files = dt.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    handleFilePreview(files[0]);
                }
            });

            fileInput.addEventListener('change', function () {
                if (this.files && this.files[0]) {
                    handleFilePreview(this.files[0]);
                }
            });

            var viewFullBtn = document.getElementById('btn_view_full_image');
            var resBadge = document.getElementById('bg_resolution_badge');

            function syncFrontPreviewHero(src) {
                var frontHero = document.getElementById('preview_front_hero');
                if (frontHero && src) {
                    frontHero.style.backgroundImage = 'linear-gradient(rgba(15,23,42,0.65), rgba(15,23,42,0.75)), url(' + src + ')';
                }
            }

            function updateImageResolution(src) {
                syncFrontPreviewHero(src);
                var img = new Image();
                img.onload = function () {
                    var w = this.naturalWidth;
                    var h = this.naturalHeight;
                    if (resBadge) {
                        resBadge.textContent = w + ' × ' + h + ' px';
                        if (w >= 1920 && h >= 1080) {
                            resBadge.className = 'badge badge-light-success fs-9';
                        } else {
                            resBadge.className = 'badge badge-light-primary fs-9';
                        }
                    }
                };
                img.onerror = function () {
                    if (resBadge) resBadge.textContent = 'Asset';
                };
                img.src = src;
            }

            // Initial image resolution detection
            updateImageResolution(previewImg.src);

            // ─── Dual Preview Mode Switcher (Admin vs Front) ───
            document.querySelectorAll('.btn-preview-mode').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.btn-preview-mode').forEach(function (b) {
                        b.classList.remove('btn-primary', 'active');
                        b.classList.add('btn-light');
                    });
                    this.classList.remove('btn-light');
                    this.classList.add('btn-primary', 'active');

                    var mode = this.getAttribute('data-mode');
                    if (mode === 'admin') {
                        document.getElementById('preview_admin_mode').style.display = 'block';
                        document.getElementById('preview_front_mode').style.display = 'none';
                    } else {
                        document.getElementById('preview_admin_mode').style.display = 'none';
                        document.getElementById('preview_front_mode').style.display = 'block';
                        syncFrontPreviewHero(previewImg.src);
                    }
                });
            });

            function handleFilePreview(file) {
                if (!file.type.match('image.*')) {
                    Swal.fire({
                        title: 'Invalid File',
                        text: 'Please select a valid image file (PNG, JPG, JPEG, WEBP)',
                        icon: 'warning'
                    });
                    return;
                }
                if (file.size > 4 * 1024 * 1024) {
                    Swal.fire({
                        title: 'File Too Large',
                        text: 'Image size exceeds maximum limit of 4MB',
                        icon: 'warning'
                    });
                    return;
                }

                var reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    if (viewFullBtn) viewFullBtn.href = e.target.result;
                    btnRemoveImage.style.display = 'inline-block';
                    removeImageFlag.value = '0';
                    document.getElementById('bg_status_text').textContent = 'Selected: ' + file.name + ' (' + Math.round(file.size / 1024) + ' KB)';
                    updateImageResolution(e.target.result);
                };
                reader.readAsDataURL(file);
            }

            // Remove Image Button
            btnRemoveImage.addEventListener('click', function () {
                removeImageFlag.value = '1';
                fileInput.value = '';
                var defaultSrc = "{{ asset('front/assets/img/header-bg.jpg') }}";
                previewImg.src = defaultSrc;
                if (viewFullBtn) viewFullBtn.href = defaultSrc;
                btnRemoveImage.style.display = 'none';
                document.getElementById('bg_status_text').textContent = 'Default Background Selected';
                updateImageResolution(defaultSrc);
            });

            // ─── Save Form Action ───
            var saveBtn = document.getElementById('save-form');
            saveBtn.addEventListener('click', function (e) {
                e.preventDefault();

                if (isMutationInFlight) {
                    return;
                }

                // Explicitly sync Ace editor values to hidden textareas before submission
                if (adminEditor) {
                    document.getElementById('admin_custom_css_textarea').value = adminEditor.getValue();
                }
                if (frontEditor) {
                    document.getElementById('front_custom_css_textarea').value = frontEditor.getValue();
                }

                isMutationInFlight = true;
                // Show button loading spinner
                saveBtn.setAttribute('data-kt-indicator', 'on');
                saveBtn.disabled = true;

                var form = document.getElementById('createForm');
                var formData = new FormData(form);
                var idempotencyKey = generateIdempotencyUUID();
                formData.append('idempotency_key', idempotencyKey);

                $.ajax({
                    url: "{{ route('superadmin.theme-settings.store') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-Idempotency-Key': idempotencyKey
                    },
                    success: function (response) {
                        isMutationInFlight = false;
                        saveBtn.removeAttribute('data-kt-indicator');
                        saveBtn.disabled = false;
                        clearDraft();

                        if (response.background_image_url) {
                            previewImg.src = response.background_image_url;
                            if (viewFullBtn) viewFullBtn.href = response.background_image_url;
                            if (response.has_background_image) {
                                btnRemoveImage.style.display = 'inline-block';
                                document.getElementById('bg_status_text').textContent = 'Custom Uploaded Asset';
                            } else {
                                btnRemoveImage.style.display = 'none';
                                document.getElementById('bg_status_text').textContent = 'Using System Default Asset (header-bg.jpg)';
                            }
                            updateImageResolution(response.background_image_url);
                        }

                        if (syncChannel) {
                            syncChannel.postMessage({
                                type: 'THEME_UPDATED',
                                primary_color: response.primary_color || primaryInput.value,
                                background_image_url: response.background_image_url,
                                has_background_image: response.has_background_image
                            });
                        }

                        Swal.fire({
                            title: "Success!",
                            text: response.message || "Theme settings updated successfully.",
                            icon: "success",
                            confirmButtonColor: "#009ef7",
                            confirmButtonText: "OK"
                        });
                    },
                    error: function (xhr) {
                        isMutationInFlight = false;
                        saveBtn.removeAttribute('data-kt-indicator');
                        saveBtn.disabled = false;

                        var errorMsg = "An error occurred while saving theme settings.";
                        if (xhr.status === 409) {
                            errorMsg = "A theme settings update is currently in progress. Please wait a moment.";
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            var firstKey = Object.keys(errors)[0];
                            errorMsg = errors[firstKey][0];
                        }

                        Swal.fire({
                            title: "Validation Error",
                            text: errorMsg,
                            icon: "error",
                            confirmButtonColor: "#f1416c"
                        });
                    }
                });
            });

            // ─── Reset Theme to Defaults ───
            var resetBtn = document.getElementById('btn-reset-theme');
            resetBtn.addEventListener('click', function () {
                if (isMutationInFlight) {
                    return;
                }

                Swal.fire({
                    title: "Reset Theme Settings?",
                    text: "This will restore the primary brand color to #009EF7, clear custom CSS stylesheets, and restore the default background image.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#f1416c",
                    cancelButtonColor: "#7e8299",
                    confirmButtonText: "Yes, Reset to Default",
                    cancelButtonText: "Cancel"
                }).then(function (result) {
                    if (result.isConfirmed) {
                        isMutationInFlight = true;
                        resetBtn.disabled = true;
                        var idempotencyKey = generateIdempotencyUUID();

                        $.ajax({
                            url: "{{ route('superadmin.theme-settings.reset') }}",
                            type: "POST",
                            data: {
                                idempotency_key: idempotencyKey
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'X-Idempotency-Key': idempotencyKey
                            },
                            success: function (response) {
                                isMutationInFlight = false;
                                resetBtn.disabled = false;
                                clearDraft();

                                primaryInput.value = defaultColor;
                                updateColorDisplay(defaultColor);

                                if (adminEditor) adminEditor.setValue('', -1);
                                if (frontEditor) frontEditor.setValue('', -1);
                                document.getElementById('admin_custom_css_textarea').value = '';
                                document.getElementById('front_custom_css_textarea').value = '';

                                var defaultSrc = "{{ asset('front/assets/img/header-bg.jpg') }}";
                                previewImg.src = defaultSrc;
                                if (viewFullBtn) viewFullBtn.href = defaultSrc;
                                btnRemoveImage.style.display = 'none';
                                removeImageFlag.value = '0';
                                document.getElementById('bg_status_text').textContent = 'Default Background Selected';
                                updateImageResolution(defaultSrc);

                                if (syncChannel) {
                                    syncChannel.postMessage({
                                        type: 'THEME_UPDATED',
                                        primary_color: defaultColor,
                                        background_image_url: defaultSrc,
                                        has_background_image: false
                                    });
                                }

                                Swal.fire({
                                    title: "Reset Completed",
                                    text: "Theme settings have been reset to factory defaults.",
                                    icon: "success",
                                    confirmButtonColor: "#009ef7"
                                });
                            },
                            error: function (xhr) {
                                isMutationInFlight = false;
                                resetBtn.disabled = false;
                                var errorMsg = "Failed to reset theme settings. Please try again.";
                                if (xhr.status === 409) {
                                    errorMsg = "A reset operation is currently in progress. Please wait a moment.";
                                }
                                Swal.fire({
                                    title: "Error",
                                    text: errorMsg,
                                    icon: "error"
                                });
                            }
                        });
                    }
                });
            });
        });
    })();
    </script>
@endpush