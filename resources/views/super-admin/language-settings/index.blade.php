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

    /* ─── Scoped Language Settings Styles (100% Page Isolated) ─── */
    .lang-container {
        width: 100%;
        max-width: 100%;
        padding: 0 16px 20px 16px !important;
        margin-top: 0 !important;
    }

    /* Tier 1: Top Header Toolbar Card */
    .lang-header-card {
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

    .lang-brand-icon {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        background: #eff6ff;
        color: #009ef7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .lang-title {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.25;
        margin-bottom: 2px;
    }

    .lang-subtitle {
        font-size: 11.5px;
        color: #64748b;
        margin-bottom: 0;
    }

    .lang-pill-badge {
        display: inline-flex;
        align-items: center;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        line-height: 1.2;
    }

    .lang-pill-blue {
        background: #eff6ff;
        color: #009ef7;
        border: 1px solid #bfdbfe;
    }

    .lang-pill-green {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .lang-pill-slate {
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .lang-pill-amber {
        background: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .lang-search-box {
        height: 36px;
        width: 270px;
        max-width: 100%;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        display: flex;
        align-items: center;
        padding: 0 10px;
        transition: all 0.2s ease;
    }

    .lang-search-box:focus-within {
        background: #ffffff;
        border-color: #009ef7;
        box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12);
    }

    .lang-search-box input {
        border: none;
        background: transparent;
        font-size: 12px;
        color: #0f172a;
        width: 100%;
        outline: none;
        padding: 0 6px;
    }

    .lang-search-box .lang-search-clear {
        border: none;
        background: transparent;
        color: #94a3b8;
        cursor: pointer;
        font-size: 12px;
        padding: 0 2px;
        display: none;
    }

    .lang-search-box .lang-search-clear:hover {
        color: #0f172a;
    }

    /* Tier 2: Sub-Tabs Filter Card */
    .lang-tabs-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 8px 12px !important;
        margin-bottom: 14px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
    }

    .lang-tab-btn {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        padding: 6px 14px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        color: #475569 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        cursor: pointer !important;
        transition: all 0.15s ease !important;
        line-height: 1.3 !important;
    }

    .lang-tab-btn:hover {
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }

    .lang-tab-btn.active {
        background: #eff6ff !important;
        border-color: #009ef7 !important;
        color: #009ef7 !important;
        box-shadow: 0 1px 2px rgba(0, 158, 247, 0.1) !important;
    }

    .lang-tab-counter {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 10.5px;
        font-weight: 700;
        padding: 1px 7px;
        border-radius: 12px;
        background: #f1f5f9;
        color: #64748b;
    }

    .lang-tab-btn.active .lang-tab-counter {
        background: #009ef7;
        color: #ffffff;
    }

    .lang-view-toggle {
        display: flex;
        align-items: center;
        background: #f1f5f9;
        border-radius: 6px;
        padding: 2px;
        gap: 2px;
    }

    .lang-view-btn {
        border: none;
        background: transparent;
        padding: 5px 9px;
        border-radius: 5px;
        color: #64748b;
        font-size: 11.5px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 4px;
        transition: all 0.15s ease;
    }

    .lang-view-btn.active {
        background: #ffffff;
        color: #009ef7;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06);
    }

    /* Tier 3: High-Density Sub-Card Styling */
    .lang-subcard {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 14px 16px !important;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
        position: relative;
    }

    .lang-subcard:hover {
        border-color: #cbd5e1 !important;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06) !important;
        transform: translateY(-1px);
    }

    .lang-subcard.is-default {
        border-color: #fde68a !important;
        background: linear-gradient(180deg, #ffffff 0%, #fffdf7 100%) !important;
    }

    .lang-flag-avatar {
        width: 44px;
        height: 44px;
        border-radius: 9px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-weight: 800;
        font-size: 13.5px;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: #ffffff !important;
        box-shadow: 0 2px 4px rgba(15, 23, 42, 0.08);
        position: relative;
    }

    .lang-avatar-iso {
        font-weight: 800;
        line-height: 1;
        font-size: 13.5px;
        letter-spacing: 0.5px;
        color: #ffffff;
    }

    .lang-avatar-sub {
        font-size: 8px;
        line-height: 1;
        margin-top: 2px;
        opacity: 0.85;
        color: #ffffff;
    }

    .lang-mini-avatar {
        width: 32px;
        height: 32px;
        border-radius: 7px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 800;
        color: #ffffff !important;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08);
        flex-shrink: 0;
    }

    .lang-card-title {
        font-size: 13.5px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 2px;
        line-height: 1.25;
    }

    .lang-card-code-pill {
        display: inline-flex;
        align-items: center;
        font-size: 10.5px;
        font-weight: 700;
        padding: 1px 6px;
        border-radius: 4px;
        background: #eff6ff;
        color: #009ef7;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        border: 1px solid #dbeafe;
        transition: all 0.15s ease;
    }

    .lang-card-code-pill.copy-code-btn:hover {
        background: #dbeafe;
        border-color: #93c5fd;
        color: #1d4ed8;
    }

    .lang-action-btn {
        width: 32px;
        height: 32px;
        border-radius: 7px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #475569;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.15s ease;
        padding: 0;
        text-decoration: none;
    }

    .lang-action-btn:hover {
        background: #eff6ff;
        border-color: #bfdbfe;
        color: #009ef7;
    }

    .lang-action-btn.delete-btn:hover {
        background: #fef2f2;
        border-color: #fecaca;
        color: #dc2626;
    }

    .lang-action-btn.default-btn:hover {
        background: #fffbeb;
        border-color: #fde68a;
        color: #b45309;
    }

    /* Table View Styling */
    .lang-table-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
        overflow: hidden;
    }

    .lang-table-card table thead th {
        background: #f8fafc !important;
        color: #475569 !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        border-bottom: 1px solid #e2e8f0 !important;
        padding: 12px 16px !important;
    }

    .lang-table-card table tbody td {
        padding: 12px 16px !important;
        vertical-align: middle !important;
        border-bottom: 1px solid #f1f5f9 !important;
        font-size: 12.5px !important;
    }

    .lang-table-card table tbody tr:last-child td {
        border-bottom: none !important;
    }

    .lang-table-card table tbody tr:hover td {
        background-color: #fbfcfe !important;
    }

    /* Empty state */
    .lang-empty-state {
        background: #ffffff;
        border: 1px dashed #cbd5e1;
        border-radius: 10px;
        padding: 40px 20px;
        text-align: center;
    }
</style>

<div class="lang-container">
    {{-- TIER 1: Top Header Toolbar Card --}}
    <div class="lang-header-card">
        <div class="d-flex align-items-center gap-3">
            <div class="lang-brand-icon">
                <i class="fa-solid fa-globe fs-2 text-primary"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h1 class="lang-title">Language & Localization Settings</h1>
                    <span class="lang-pill-badge lang-pill-blue">
                        <i class="fa-solid fa-layer-group fs-8 me-1"></i>Total: {{ $totalLanguages }}
                    </span>
                    <span class="lang-pill-badge lang-pill-green">
                        <i class="fa-solid fa-circle-check fs-8 me-1"></i>Active: <span id="kpi-enabled-count">{{ $enabledCount }}</span>
                    </span>
                    <span class="lang-pill-badge lang-pill-slate">
                        <i class="fa-solid fa-circle-xmark fs-8 me-1"></i>Disabled: <span id="kpi-disabled-count">{{ $disabledCount }}</span>
                    </span>
                    <span class="lang-pill-badge lang-pill-amber">
                        <i class="fa-solid fa-star fs-8 me-1"></i>Default: {{ strtoupper($defaultLocale) }}
                    </span>
                </div>
                <p class="lang-subtitle">Configure ATS platform languages, system default locale, and manage dictionary translation keys</p>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- Instant Search Bar --}}
            <div class="lang-search-box">
                <i class="fa-solid fa-magnifying-glass fs-6 text-gray-500"></i>
                <input type="text" id="lang-search-input" placeholder="Search language or code..." autocomplete="off">
                <button type="button" id="lang-search-clear" class="lang-search-clear" title="Clear search">✕</button>
            </div>

            {{-- Manage Translations Button --}}
            <a href="{{ url('/translations') }}" target="_blank" class="btn btn-sm btn-light-primary fw-bold d-flex align-items-center gap-1">
                <i class="fa-solid fa-book-open fs-6"></i>
                <span>Translations</span>
                <i class="fa-solid fa-arrow-up-right-from-square fs-8 ms-1"></i>
            </a>

            {{-- Add Language Button --}}
            <button type="button" id="btn-add-language" class="btn btn-sm btn-primary fw-bold d-flex align-items-center gap-1">
                <i class="fa-solid fa-plus fs-6 me-1"></i>
                <span>Add Language</span>
            </button>
        </div>
    </div>

    {{-- TIER 2: Sub-Tabs Filter Card --}}
    <div class="lang-tabs-card">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="lang-tab-btn active" data-filter="all">
                <i class="fa-solid fa-list fs-7 me-1"></i>
                <span>All Languages</span>
                <span class="lang-tab-counter" id="tab-count-all">{{ $totalLanguages }}</span>
            </button>
            <button type="button" class="lang-tab-btn" data-filter="enabled">
                <i class="fa-solid fa-circle-check fs-7 text-success me-1"></i>
                <span>Active</span>
                <span class="lang-tab-counter" id="tab-count-enabled">{{ $enabledCount }}</span>
            </button>
            <button type="button" class="lang-tab-btn" data-filter="disabled">
                <i class="fa-solid fa-circle-xmark fs-7 text-danger me-1"></i>
                <span>Disabled</span>
                <span class="lang-tab-counter" id="tab-count-disabled">{{ $disabledCount }}</span>
            </button>
            <button type="button" class="lang-tab-btn" data-filter="default">
                <i class="fa-solid fa-star fs-7 text-warning me-1"></i>
                <span>System Default</span>
                <span class="lang-tab-counter">1</span>
            </button>
        </div>

        <div class="d-flex align-items-center gap-3">
            <span class="text-muted fs-8 d-none d-sm-inline">
                <i class="fa-solid fa-language fs-6 me-1 text-primary"></i>ISO Catalog: <strong>{{ $masterLanguagesCount }}</strong> languages
            </span>

            {{-- View Mode Toggle --}}
            <div class="lang-view-toggle">
                <button type="button" class="lang-view-btn active" data-view="cards" title="High-Density Card Stream">
                    <i class="fa-solid fa-grip fs-6 me-1"></i>
                    <span>Cards</span>
                </button>
                <button type="button" class="lang-view-btn" data-view="table" title="Compact Table View">
                    <i class="fa-solid fa-table-list fs-6 me-1"></i>
                    <span>Table</span>
                </button>
            </div>
        </div>
    </div>

    @php
        $getLangBg = function($code) {
            $code = strtolower(trim((string)$code));
            $colors = [
                'en' => 'linear-gradient(135deg, #009ef7 0%, #0077c5 100%)',
                'ar' => 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
                'es' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
                'fr' => 'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)',
                'de' => 'linear-gradient(135deg, #475569 0%, #1e293b 100%)',
                'ru' => 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)',
                'ja' => 'linear-gradient(135deg, #f43f5e 0%, #be123c 100%)',
                'zh' => 'linear-gradient(135deg, #e11d48 0%, #9f1239 100%)',
                'zh-cn' => 'linear-gradient(135deg, #e11d48 0%, #9f1239 100%)',
                'zh-tw' => 'linear-gradient(135deg, #be123c 0%, #881337 100%)',
                'hi' => 'linear-gradient(135deg, #f97316 0%, #c2410c 100%)',
                'pt' => 'linear-gradient(135deg, #06b6d4 0%, #0891b2 100%)',
                'it' => 'linear-gradient(135deg, #14b8a6 0%, #0f766e 100%)',
                'pl' => 'linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%)',
                'nl' => 'linear-gradient(135deg, #ea580c 0%, #9a3412 100%)',
                'fa' => 'linear-gradient(135deg, #0d9488 0%, #115e59 100%)',
                'el' => 'linear-gradient(135deg, #0284c7 0%, #0369a1 100%)',
                'et' => 'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)',
            ];
            if (isset($colors[$code])) return $colors[$code];
            $palette = [
                'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)',
                'linear-gradient(135deg, #10b981 0%, #047857 100%)',
                'linear-gradient(135deg, #f59e0b 0%, #b45309 100%)',
                'linear-gradient(135deg, #8b5cf6 0%, #5b21b6 100%)',
                'linear-gradient(135deg, #ec4899 0%, #be185d 100%)',
                'linear-gradient(135deg, #06b6d4 0%, #0e7490 100%)',
                'linear-gradient(135deg, #64748b 0%, #334155 100%)',
            ];
            $hash = crc32($code);
            return $palette[abs($hash) % count($palette)];
        };
    @endphp

    {{-- TIER 3: Primary Stream — High-Density Sub-Cards View --}}
    <div id="lang-cards-view">
        <div class="row g-3 g-xl-4" id="lang-cards-grid">
            @forelse($languages as $item)
                @php
                    $isDefault = ($defaultLocale === $item->language_code);
                    $isEnglish = ($item->language_code === 'en');
                    $langDir = base_path('resources/lang/' . strtolower($item->language_code));
                    $fileCount = is_dir($langDir) ? count(\Illuminate\Support\Facades\File::glob($langDir . '/*.php')) : 0;
                @endphp
                <div class="col-12 col-md-6 col-xl-4 col-xxl-3 lang-item-col"
                     data-id="{{ $item->id }}"
                     data-name="{{ strtolower($item->language_name) }}"
                     data-code="{{ strtolower($item->language_code) }}"
                     data-status="{{ $item->status }}"
                     data-default="{{ $isDefault ? '1' : '0' }}">
                    <div class="lang-subcard {{ $isDefault ? 'is-default' : '' }}">
                        {{-- Upper Section --}}
                        <div>
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="lang-flag-avatar" style="background: {{ $getLangBg($item->language_code) }};" title="{{ $item->language_name }} ({{ strtoupper($item->language_code) }})">
                                        <span class="lang-avatar-iso">{{ strtoupper(substr($item->language_code, 0, 2)) }}</span>
                                        <span class="lang-avatar-sub"><i class="fa-solid fa-globe"></i></span>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2">
                                            <h3 class="lang-card-title">{{ ucfirst($item->language_name) }}</h3>
                                            <span class="lang-card-code-pill copy-code-btn cursor-pointer" data-code="{{ strtolower($item->language_code) }}" data-bs-toggle="tooltip" title="Click to copy '{{ strtolower($item->language_code) }}'">
                                                {{ strtoupper($item->language_code) }}
                                                <i class="fa-solid fa-copy fs-9 ms-1"></i>
                                            </span>
                                        </div>
                                        <span class="text-muted fs-8 d-block">
                                            Locale: <code class="fs-8 text-gray-700">{{ strtolower($item->language_code) }}</code>
                                        </span>
                                    </div>
                                </div>

                                {{-- Default Star Badge --}}
                                @if($isDefault)
                                    <span class="badge badge-light-warning fw-bolder fs-8 px-2 py-1" data-bs-toggle="tooltip" title="Current Platform Default Locale">
                                        <i class="fa-solid fa-star fs-7 text-warning me-1"></i>Default
                                    </span>
                                @endif
                            </div>

                            {{-- Middle Section: Status Row & Directory --}}
                            <div class="p-2 rounded bg-light border border-dashed border-gray-300 mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="fs-8 fw-semibold text-gray-600">Portal Availability:</span>
                                    <div class="form-check form-switch form-check-custom form-check-solid">
                                        <input class="form-check-input h-18px w-32px change-language-setting cursor-pointer"
                                               type="checkbox"
                                               {{ $item->status === 'enabled' ? 'checked' : '' }}
                                               {{ ($isDefault || $isEnglish) ? 'disabled' : '' }}
                                               data-lang-id="{{ $item->id }}"
                                               title="{{ ($isDefault || $isEnglish) ? 'English and the system default language cannot be disabled.' : 'Toggle availability' }}">
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="fs-8 text-gray-500">Dictionary:</span>
                                    <span class="badge badge-light fs-8 text-gray-700 py-0 px-2" title="{{ $fileCount }} dictionary files">
                                        <i class="fa-solid fa-file-code fs-8 me-1 text-primary"></i>{{ $fileCount }} files
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Lower Section: Action Buttons Strip --}}
                        <div class="d-flex align-items-center justify-content-between pt-2 border-top border-gray-200">
                            <div class="d-flex align-items-center gap-1">
                                {{-- Edit Button --}}
                                <button type="button" class="lang-action-btn edit-language" data-row-id="{{ $item->id }}" data-bs-toggle="tooltip" title="Edit language details">
                                    <i class="fa-solid fa-pen-to-square text-primary"></i>
                                </button>

                                {{-- Set as Default Button --}}
                                @if(!$isDefault && $item->status === 'enabled')
                                    <button type="button" class="lang-action-btn default-btn set-default-language" data-row-id="{{ $item->id }}" data-lang-name="{{ $item->language_name }}" data-bs-toggle="tooltip" title="Set as System Default">
                                        <i class="fa-solid fa-star text-warning"></i>
                                    </button>
                                @endif

                                {{-- Translations Link --}}
                                <a href="{{ url('/translations?group=&locale=' . strtolower($item->language_code)) }}" target="_blank" class="lang-action-btn" data-bs-toggle="tooltip" title="Manage translations dictionary">
                                    <i class="fa-solid fa-book-open text-info"></i>
                                </a>
                            </div>

                            <div>
                                {{-- Delete Button (Guarded for English and Default) --}}
                                @if(!$isEnglish && !$isDefault)
                                    <button type="button" class="lang-action-btn delete-btn delete-language-row" data-row-id="{{ $item->id }}" data-lang-name="{{ $item->language_name }}" data-bs-toggle="tooltip" title="Delete language">
                                        <i class="fa-solid fa-trash-can text-danger"></i>
                                    </button>
                                @else
                                    <span class="badge badge-light fs-8 text-muted px-2 py-1" data-bs-toggle="tooltip" title="Core platform locale cannot be deleted">
                                        <i class="fa-solid fa-lock fs-9 me-1 text-muted"></i>Locked
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="lang-empty-state">
                        <i class="fa-solid fa-globe fs-3x text-muted mb-2"></i>
                        <h4 class="fw-bold text-gray-800">No Languages Configured</h4>
                        <p class="text-muted fs-7 mb-4">Click "Add Language" below to set up your first localization locale.</p>
                        <button type="button" class="btn btn-sm btn-primary fw-bold" id="btn-empty-add-lang">
                            <i class="fa-solid fa-plus me-1"></i>Add Language
                        </button>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- TIER 3 Alternative: Modern Metronic Table View --}}
    <div id="lang-table-view" class="lang-table-card" style="display: none;">
        <div class="table-responsive">
            <table class="table align-middle gs-0 gy-3 mb-0" id="lang-native-table">
                <thead>
                    <tr>
                        <th class="ps-4 w-50px">#</th>
                        <th class="min-w-180px">Language</th>
                        <th class="min-w-90px">ISO Code</th>
                        <th class="min-w-130px text-center">System Default</th>
                        <th class="min-w-120px text-center">Status</th>
                        <th class="pe-4 text-end min-w-140px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($languages as $idx => $item)
                        @php
                            $isDefault = ($defaultLocale === $item->language_code);
                            $isEnglish = ($item->language_code === 'en');
                            $langDir = base_path('resources/lang/' . strtolower($item->language_code));
                            $fileCount = is_dir($langDir) ? count(\Illuminate\Support\Facades\File::glob($langDir . '/*.php')) : 0;
                        @endphp
                        <tr class="lang-table-row"
                            data-id="{{ $item->id }}"
                            data-name="{{ strtolower($item->language_name) }}"
                            data-code="{{ strtolower($item->language_code) }}"
                            data-status="{{ $item->status }}"
                            data-default="{{ $isDefault ? '1' : '0' }}">
                            <td class="ps-4 text-gray-600 fw-bold fs-7">{{ $idx + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="lang-mini-avatar me-3" style="background: {{ $getLangBg($item->language_code) }};">
                                        {{ strtoupper(substr($item->language_code, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="text-gray-900 fw-bold fs-6">{{ ucfirst($item->language_name) }}</span>
                                            <span class="badge badge-light fs-8 text-gray-600 px-2 py-0">
                                                <i class="fa-solid fa-file-code fs-9 me-1 text-primary"></i>{{ $fileCount }} files
                                            </span>
                                        </div>
                                        <span class="text-muted fs-8">resources/lang/{{ strtolower($item->language_code) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="lang-card-code-pill copy-code-btn cursor-pointer" data-code="{{ strtolower($item->language_code) }}" data-bs-toggle="tooltip" title="Click to copy '{{ strtolower($item->language_code) }}'">
                                    {{ strtoupper($item->language_code) }}
                                    <i class="fa-solid fa-copy fs-9 ms-1"></i>
                                </span>
                            </td>
                            <td class="text-center">
                                @if($isDefault)
                                    <span class="badge badge-light-warning fw-bolder fs-8 px-2 py-1">
                                        <i class="fa-solid fa-star fs-7 text-warning me-1"></i>Default Locale
                                    </span>
                                @else
                                    <span class="text-muted fs-8">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="form-check form-switch form-check-custom form-check-solid justify-content-center">
                                    <input class="form-check-input h-18px w-32px change-language-setting cursor-pointer"
                                           type="checkbox"
                                           {{ $item->status === 'enabled' ? 'checked' : '' }}
                                           {{ ($isDefault || $isEnglish) ? 'disabled' : '' }}
                                           data-lang-id="{{ $item->id }}"
                                           title="{{ ($isDefault || $isEnglish) ? 'Default language cannot be disabled.' : 'Toggle availability' }}">
                                </div>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex align-items-center justify-content-end gap-1">
                                    <button type="button" class="lang-action-btn edit-language" data-row-id="{{ $item->id }}" data-bs-toggle="tooltip" title="Edit language">
                                        <i class="fa-solid fa-pen-to-square text-primary"></i>
                                    </button>

                                    @if(!$isDefault && $item->status === 'enabled')
                                        <button type="button" class="lang-action-btn default-btn set-default-language" data-row-id="{{ $item->id }}" data-lang-name="{{ $item->language_name }}" data-bs-toggle="tooltip" title="Set as System Default">
                                            <i class="fa-solid fa-star text-warning"></i>
                                        </button>
                                    @endif

                                    <a href="{{ url('/translations?group=&locale=' . strtolower($item->language_code)) }}" target="_blank" class="lang-action-btn" data-bs-toggle="tooltip" title="Translations">
                                        <i class="fa-solid fa-book-open text-info"></i>
                                    </a>

                                    @if(!$isEnglish && !$isDefault)
                                        <button type="button" class="lang-action-btn delete-btn delete-language-row" data-row-id="{{ $item->id }}" data-lang-name="{{ $item->language_name }}" data-bs-toggle="tooltip" title="Delete">
                                            <i class="fa-solid fa-trash-can text-danger"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Instant Filter No Results Placeholder --}}
    <div id="lang-no-results" class="lang-empty-state mt-3" style="display: none;">
        <i class="fa-solid fa-magnifying-glass fs-2tx text-muted mb-2"></i>
        <h5 class="fw-bold text-gray-800">No Matching Languages Found</h5>
        <p class="text-muted fs-7 mb-2">Try adjusting your search query or switching active tab filter.</p>
        <button type="button" class="btn btn-sm btn-light-primary fw-bold" id="btn-reset-filters">
            <i class="fa-solid fa-rotate-right me-1"></i>Reset Filters
        </button>
    </div>
</div>

{{-- Global Application Modal for Add / Edit Language --}}
<div class="modal fade" id="application-md-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-600px">
        <div class="modal-content border-0 shadow-lg rounded-3">
            {{-- Dynamically loaded via create-modal.blade.php / edit-modal.blade.php --}}
        </div>
    </div>
</div>

{{-- Native Metronic 8 Slide-Over Drawer: Add Language --}}
<div id="kt_drawer_language"
     class="bg-body"
     data-kt-drawer="true"
     data-kt-drawer-name="languageDrawer"
     data-kt-drawer-activate="true"
     data-kt-drawer-overlay="true"
     data-kt-drawer-width="{default:'100%', 'sm': '500px', 'md': '540px'}"
     data-kt-drawer-direction="end"
     data-kt-drawer-close="#kt_drawer_language_close"
     style="display: none; z-index: 1095;">

    <div class="card shadow-none rounded-0 w-100">
        {{-- Drawer Header --}}
        <div class="card-header border-0 pt-6 pb-4" id="kt_drawer_language_header">
            <div class="card-title m-0">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-40px me-3">
                        <span class="symbol-label bg-light-primary text-primary">
                            <i class="fa-solid fa-circle-plus fs-2 text-primary"></i>
                        </span>
                    </div>
                    <div>
                        <h3 class="fw-bolder fs-4 text-gray-900 mb-0">Add New Language</h3>
                        <span class="text-muted fs-8">Configure language locale, ISO code, and base dictionary</span>
                    </div>
                </div>
            </div>
            <div class="card-toolbar">
                <button type="button" class="btn btn-sm btn-icon btn-active-light-primary" id="kt_drawer_language_close" title="Close Drawer">
                    <i class="fa-solid fa-xmark fs-2"></i>
                </button>
            </div>
        </div>

        {{-- Drawer Body --}}
        <div class="card-body position-relative py-4 px-6 scroll-y" id="kt_drawer_language_body" style="max-height: calc(100vh - 160px);">
            <form id="createLangDrawerForm">
                @csrf

                {{-- Quick Master ISO Preset Selector --}}
                <div class="mb-5 p-3 rounded bg-light-primary border border-primary border-dashed">
                    <label class="form-label fs-7 fw-bolder text-primary mb-1">
                        <i class="fa-solid fa-filter fs-7 me-1 text-primary"></i>Quick Preset from Master ISO Catalog (135 Languages)
                    </label>
                    <select id="drawer_master_language_preset" class="form-select form-select-sm form-select-solid" data-control="select2">
                        <option value="">-- Or choose from ISO Catalog to auto-fill --</option>
                        @foreach($masterLanguages as $mlang)
                            <option value="{{ strtolower($mlang->code) }}" data-name="{{ $mlang->name }}">
                                {{ $mlang->name }} ({{ strtoupper($mlang->code) }})
                            </option>
                        @endforeach
                    </select>
                    <div class="text-muted fs-8 mt-1">Selecting a language will automatically pre-fill the name and ISO code below.</div>
                </div>

                <div class="fv-row mb-5">
                    <label class="required form-label fs-6 fw-bold text-gray-800 mb-1">Language Name</label>
                    <input type="text" name="language_name" id="drawer_language_name" class="form-control form-control-solid" placeholder="e.g. French, Japanese, Hindi" required>
                </div>

                <div class="fv-row mb-5">
                    <label class="required form-label fs-6 fw-bold text-gray-800 mb-1">Language Code (ISO-639-1)</label>
                    <div class="input-group">
                        <input type="text" name="language_code" id="drawer_language_code" class="form-control form-control-solid text-lowercase" placeholder="e.g. fr, ja, hi" maxlength="20" required>
                        <span class="input-group-text bg-light-info text-info fw-bolder text-uppercase" id="drawer_code_preview">--</span>
                    </div>
                    <div class="text-muted fs-8 mt-1">Standard 2-letter or BCP-47 locale code used for translation directories.</div>
                </div>

                <div class="fv-row mb-5">
                    <label class="required form-label fs-6 fw-bold text-gray-800 mb-1">Status</label>
                    <select name="status" id="drawer_language_status" class="form-select form-select-solid">
                        <option value="enabled" selected>Enabled (Available to all users and applicants)</option>
                        <option value="disabled">Disabled (Hidden from public dropdowns)</option>
                    </select>
                </div>

                <div class="form-check form-check-custom form-check-solid mb-4">
                    <input class="form-check-input" type="checkbox" name="scaffold_dictionary" value="1" id="drawer_scaffold_dictionary" checked>
                    <label class="form-check-label fs-7 text-gray-700" for="drawer_scaffold_dictionary">
                        Automatically scaffold base translation files from English (<code class="fs-8">resources/lang/en</code>)
                    </label>
                </div>
            </form>
        </div>

        {{-- Drawer Footer --}}
        <div class="card-footer border-0 d-flex justify-content-end py-4 px-6 bg-light" id="kt_drawer_language_footer">
            <button type="button" class="btn btn-sm btn-light me-3" id="btn-cancel-drawer-lang">Cancel</button>
            <button type="button" id="btn-submit-drawer-lang" class="btn btn-sm btn-primary">
                <span class="indicator-label">
                    <i class="fa-solid fa-check fs-5 me-1"></i>Save Language
                </span>
                <span class="indicator-progress" style="display: none;">
                    Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                </span>
            </button>
        </div>
    </div>
</div>
@endsection

@push('footer-script')
<script>
    $(document).ready(function() {
        var currentFilter = 'all';
        var currentSearch = '';
        var searchDebounceTimer = null;

        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

        // ─── Client-First Filtering Engine (10K Concurrency / 0% Server Strain) ───
        function applyFilters() {
            var visibleCount = 0;
            var searchLower = currentSearch.trim().toLowerCase();

            // Filter High-Density Cards
            $('.lang-item-col').each(function() {
                var $col = $(this);
                var name = $col.data('name') || '';
                var code = $col.data('code') || '';
                var status = $col.data('status') || '';
                var isDefault = String($col.data('default')) === '1';

                var matchesTab = false;
                if (currentFilter === 'all') {
                    matchesTab = true;
                } else if (currentFilter === 'enabled' && status === 'enabled') {
                    matchesTab = true;
                } else if (currentFilter === 'disabled' && status === 'disabled') {
                    matchesTab = true;
                } else if (currentFilter === 'default' && isDefault) {
                    matchesTab = true;
                }

                var matchesSearch = true;
                if (searchLower) {
                    matchesSearch = (name.indexOf(searchLower) > -1 || code.indexOf(searchLower) > -1);
                }

                if (matchesTab && matchesSearch) {
                    $col.show();
                    visibleCount++;
                } else {
                    $col.hide();
                }
            });

            // Filter Table Rows
            $('.lang-table-row').each(function() {
                var $row = $(this);
                var name = $row.data('name') || '';
                var code = $row.data('code') || '';
                var status = $row.data('status') || '';
                var isDefault = String($row.data('default')) === '1';

                var matchesTab = false;
                if (currentFilter === 'all') {
                    matchesTab = true;
                } else if (currentFilter === 'enabled' && status === 'enabled') {
                    matchesTab = true;
                } else if (currentFilter === 'disabled' && status === 'disabled') {
                    matchesTab = true;
                } else if (currentFilter === 'default' && isDefault) {
                    matchesTab = true;
                }

                var matchesSearch = true;
                if (searchLower) {
                    matchesSearch = (name.indexOf(searchLower) > -1 || code.indexOf(searchLower) > -1);
                }

                if (matchesTab && matchesSearch) {
                    $row.show();
                } else {
                    $row.hide();
                }
            });

            // Empty state display
            if (visibleCount === 0) {
                $('#lang-no-results').show();
            } else {
                $('#lang-no-results').hide();
            }
        }

        // Sub-Tab Filter Click Handler
        $('.lang-tab-btn').on('click', function() {
            $('.lang-tab-btn').removeClass('active');
            $(this).addClass('active');
            currentFilter = $(this).data('filter');
            applyFilters();
        });

        // 300ms Debounced Search Input
        $('#lang-search-input').on('input', function() {
            var val = $(this).val();
            if (val.length > 0) {
                $('#lang-search-clear').show();
            } else {
                $('#lang-search-clear').hide();
            }

            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(function() {
                currentSearch = val;
                applyFilters();
            }, 300);
        });

        // Search Clear Button
        $('#lang-search-clear').on('click', function() {
            $('#lang-search-input').val('');
            $(this).hide();
            currentSearch = '';
            applyFilters();
        });

        // Reset Filters Button in Empty State
        $('#btn-reset-filters').on('click', function() {
            $('#lang-search-input').val('');
            $('#lang-search-clear').hide();
            currentSearch = '';
            $('.lang-tab-btn[data-filter="all"]').click();
        });

        // View Mode Switcher (Cards vs Table)
        $('.lang-view-btn').on('click', function() {
            var mode = $(this).data('view');
            $('.lang-view-btn').removeClass('active');
            $(this).addClass('active');

            if (mode === 'table') {
                $('#lang-cards-view').hide();
                $('#lang-table-view').show();
            } else {
                $('#lang-table-view').hide();
                $('#lang-cards-view').show();
            }

            try {
                localStorage.setItem('jero_lang_view_mode', mode);
            } catch (e) {}
        });

        // Restore View Mode from LocalStorage
        try {
            var savedView = localStorage.getItem('jero_lang_view_mode');
            if (savedView === 'table') {
                $('.lang-view-btn[data-view="table"]').click();
            }
        } catch (e) {}

        // Copy ISO Code to Clipboard
        $('body').on('click', '.copy-code-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var code = $(this).data('code');
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(code).then(function() {
                    toastr.info("Copied '" + code + "' to clipboard!");
                });
            } else {
                var temp = $('<input>');
                $('body').append(temp);
                temp.val(code).select();
                document.execCommand('copy');
                temp.remove();
                toastr.info("Copied '" + code + "' to clipboard!");
            }
        });

        // ─── Universal Bootstrap 5 AJAX Modal Engine ───
        window.$.ajaxModal = function(selector, url, onLoad) {
            var modalEl = document.querySelector(selector);
            if (!modalEl) return;
            var $modal = $(selector);
            $modal.find('.modal-content').html(
                '<div class="p-10 text-center">' +
                '  <div class="spinner-border text-primary" role="status"></div>' +
                '  <div class="mt-3 text-muted fw-bold fs-7">Loading language details...</div>' +
                '</div>'
            );
            var bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
            bsModal.show();

            $.ajax({
                url: url,
                type: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(html) {
                    $modal.find('.modal-content').html(html);
                    if (typeof onLoad === 'function') onLoad();
                },
                error: function() {
                    $modal.find('.modal-content').html(
                        '<div class="modal-header border-0 pb-0 justify-content-end">' +
                        '  <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal"><i class="fa-solid fa-xmark fs-3"></i></button>' +
                        '</div>' +
                        '<div class="modal-body text-center py-8">' +
                        '  <i class="fa-solid fa-triangle-exclamation text-danger fs-1 mb-3"></i>' +
                        '  <h5 class="text-gray-800 fw-bolder">Failed to load content</h5>' +
                        '  <p class="text-muted fs-7">An error occurred while loading. Please try again.</p>' +
                        '</div>'
                    );
                }
            });
        };

        // Open Add Language Modal
        function openAddModal() {
            var url = "{{ route('superadmin.language-settings.create') }}";
            window.$.ajaxModal('#application-md-modal', url);
        }

        // Open Edit Language Modal
        function openEditModal(id) {
            var url = "{{ route('superadmin.language-settings.edit', ':id') }}".replace(':id', id);
            window.$.ajaxModal('#application-md-modal', url);
        }

        // Trigger Add Language Modal
        $('#btn-add-language, #btn-empty-add-lang').on('click', function(e) {
            e.preventDefault();
            openAddModal();
        });

        // Trigger Edit Language Modal
        $('body').on('click', '.edit-language', function(e) {
            e.preventDefault();
            var id = $(this).data('row-id');
            openEditModal(id);
        });

        // ─── Native Slide-Over Drawer Fallback Engine ───
        function openLanguageDrawer() {
            var drawerEl = document.getElementById('kt_drawer_language');
            if (drawerEl) {
                drawerEl.style.display = 'block';
                if (typeof KTDrawer !== 'undefined') {
                    var inst = KTDrawer.getInstance(drawerEl);
                    if (!inst && typeof KTDrawer === 'function') {
                        inst = new KTDrawer(drawerEl);
                    }
                    if (inst && typeof inst.show === 'function') {
                        inst.show();
                        return;
                    }
                }
                drawerEl.classList.add('drawer-on');
                if (!$('.drawer-overlay').length) {
                    $('body').append('<div class="drawer-overlay" style="z-index: 1090;"></div>');
                }
            }
        }

        function closeLanguageDrawer() {
            var drawerEl = document.getElementById('kt_drawer_language');
            if (drawerEl) {
                if (typeof KTDrawer !== 'undefined') {
                    var inst = KTDrawer.getInstance(drawerEl);
                    if (!inst && typeof KTDrawer === 'function') {
                        inst = new KTDrawer(drawerEl);
                    }
                    if (inst && typeof inst.hide === 'function') {
                        inst.hide();
                    }
                }
                drawerEl.classList.remove('drawer-on');
                $('.drawer-overlay').remove();
                drawerEl.style.display = 'none';
            }
        }

        // Close Drawer Handlers
        $('#kt_drawer_language_close, #btn-cancel-drawer-lang').on('click', function() {
            closeLanguageDrawer();
        });

        $('body').on('click', '.drawer-overlay', function() {
            closeLanguageDrawer();
        });

        // Drawer Preset Selector
        $('#drawer_master_language_preset').on('change', function() {
            var code = $(this).val();
            var name = $(this).find('option:selected').data('name');
            if (code && name) {
                $('#drawer_language_name').val(name);
                $('#drawer_language_code').val(code).trigger('input');
            }
        });

        // Drawer Code Preview
        $('#drawer_language_code').on('input', function() {
            var val = $(this).val().trim().toUpperCase();
            $('#drawer_code_preview').text(val ? val : '--');
        });

        // Drawer Submit Form
        $('#btn-submit-drawer-lang').on('click', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var form = $('#createLangDrawerForm')[0];

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            var formData = new FormData(form);
            $btn.attr('disabled', true);
            $btn.find('.indicator-label').hide();
            $btn.find('.indicator-progress').show();

            $.ajax({
                url: "{{ route('superadmin.language-settings.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $btn.attr('disabled', false);
                    $btn.find('.indicator-label').show();
                    $btn.find('.indicator-progress').hide();

                    if (response.status === 'success') {
                        closeLanguageDrawer();
                        Swal.fire({
                            title: "Created!",
                            text: response.message,
                            icon: "success",
                            confirmButtonText: "OK",
                            customClass: { confirmButton: 'btn btn-primary' }
                        }).then(function() {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: response.message || "Failed to create language.",
                            icon: "error",
                            confirmButtonText: "OK",
                            customClass: { confirmButton: 'btn btn-danger' }
                        });
                    }
                },
                error: function(xhr) {
                    $btn.attr('disabled', false);
                    $btn.find('.indicator-label').show();
                    $btn.find('.indicator-progress').hide();

                    var errorMsg = "An error occurred while saving the language.";
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        var errors = xhr.responseJSON.errors;
                        errorMsg = Object.values(errors).flat().join('<br>');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        title: "Validation Error",
                        html: errorMsg,
                        icon: "error",
                        confirmButtonText: "OK",
                        customClass: { confirmButton: 'btn btn-danger' }
                    });
                }
            });
        });

        // ─── Status Toggle AJAX Handler ───
        $('body').on('change', '.change-language-setting', function() {
            var $switch = $(this);
            var id = $switch.data('lang-id');
            var isChecked = $switch.is(':checked');
            var status = isChecked ? 'enabled' : 'disabled';
            var url = "{{ route('superadmin.language-settings.changeStatus', ':id') }}".replace(':id', id);

            $.ajax({
                url: url,
                type: "POST",
                data: {
                    'id': id,
                    'status': status,
                    '_method': 'PUT',
                    '_token': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        // Update DOM data attributes
                        $('.lang-item-col[data-id="' + id + '"]').attr('data-status', status).data('status', status);
                        $('.lang-table-row[data-id="' + id + '"]').attr('data-status', status).data('status', status);

                        // Sync both switch instances (card and table)
                        $('.change-language-setting[data-lang-id="' + id + '"]').prop('checked', isChecked);

                        // Recalculate KPIs
                        var enabled = $('.lang-item-col[data-status="enabled"]').length;
                        var disabled = $('.lang-item-col[data-status="disabled"]').length;
                        $('#kpi-enabled-count').text(enabled);
                        $('#kpi-disabled-count').text(disabled);
                        $('#tab-count-enabled').text(enabled);
                        $('#tab-count-disabled').text(disabled);

                        toastr.success(response.message || "Status updated successfully.");
                    } else {
                        // Revert switch on rejection
                        $switch.prop('checked', !isChecked);
                        Swal.fire({
                            title: "Action Restricted",
                            text: response.message,
                            icon: "warning",
                            confirmButtonText: "OK",
                            customClass: { confirmButton: 'btn btn-primary' }
                        });
                    }
                },
                error: function(xhr) {
                    $switch.prop('checked', !isChecked);
                    Swal.fire({
                        title: "Error",
                        text: "Failed to update language status.",
                        icon: "error",
                        confirmButtonText: "OK",
                        customClass: { confirmButton: 'btn btn-danger' }
                    });
                }
            });
        });

        // ─── Set as System Default Handler ───
        $('body').on('click', '.set-default-language', function() {
            var id = $(this).data('row-id');
            var name = $(this).data('lang-name') || 'this language';
            var url = "{{ route('superadmin.language-settings.setDefault', ':id') }}".replace(':id', id);

            Swal.fire({
                title: "Set Default Language?",
                text: "Do you want to set " + name + " as the primary system-wide default language for all tenant portals?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Yes, Set as Default",
                cancelButtonText: "Cancel",
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-light'
                }
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: {
                            '_method': 'PUT',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    title: "Default Updated!",
                                    text: response.message,
                                    icon: "success",
                                    confirmButtonText: "OK",
                                    customClass: { confirmButton: 'btn btn-primary' }
                                }).then(function() {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: "Error",
                                    text: response.message,
                                    icon: "error",
                                    confirmButtonText: "OK"
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: "Error",
                                text: "Failed to set default language.",
                                icon: "error",
                                confirmButtonText: "OK"
                            });
                        }
                    });
                }
            });
        });

        // ─── Delete Language Handler ───
        $('body').on('click', '.delete-language-row', function() {
            var id = $(this).data('row-id');
            var name = $(this).data('lang-name') || 'this language';
            var url = "{{ route('superadmin.language-settings.destroy', ':id') }}".replace(':id', id);

            Swal.fire({
                title: "Delete Language?",
                text: "Are you sure you want to permanently delete " + name + "? All custom translations for this locale will be removed.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                confirmButtonText: "Yes, Delete It",
                cancelButtonText: "Cancel",
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-light'
                }
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: {
                            '_token': '{{ csrf_token() }}',
                            '_method': 'DELETE'
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    title: "Deleted!",
                                    text: response.message,
                                    icon: "success",
                                    confirmButtonText: "OK",
                                    customClass: { confirmButton: 'btn btn-primary' }
                                }).then(function() {
                                    // Remove elements smoothly from DOM
                                    $('.lang-item-col[data-id="' + id + '"]').fadeOut(250, function() {
                                        $(this).remove();
                                        applyFilters();
                                    });
                                    $('.lang-table-row[data-id="' + id + '"]').fadeOut(250, function() {
                                        $(this).remove();
                                    });

                                    // Decrement counters
                                    var remaining = $('.lang-item-col').length - 1;
                                    $('#tab-count-all').text(remaining);
                                });
                            } else {
                                Swal.fire({
                                    title: "Action Blocked",
                                    text: response.message,
                                    icon: "error",
                                    confirmButtonText: "OK",
                                    customClass: { confirmButton: 'btn btn-danger' }
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: "Error",
                                text: "Failed to delete language record.",
                                icon: "error",
                                confirmButtonText: "OK",
                                customClass: { confirmButton: 'btn btn-danger' }
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@endpush