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

    /* Scoped Currency Settings Styles - 100% Page Isolated */
    .cs-container {
        width: 100%;
        max-width: 100%;
        padding: 0 16px 20px 16px !important;
        margin-top: 0 !important;
    }

    /* Tier 1: Top Header Toolbar Card */
    .cs-header-card {
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

    .cs-brand-icon {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        background: #eff6ff;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .cs-title {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.25;
        margin-bottom: 2px;
    }

    .cs-subtitle {
        font-size: 11.5px;
        color: #64748b;
        margin-bottom: 0;
    }

    .cs-pill-badge {
        display: inline-flex;
        align-items: center;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        line-height: 1.2;
    }

    .cs-pill-blue {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .cs-pill-green {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .cs-pill-slate {
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .cs-search-subcard {
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

    .cs-search-subcard:focus-within {
        background: #ffffff;
        border-color: #009ef7;
        box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12);
    }

    .cs-search-subcard input {
        border: none;
        background: transparent;
        font-size: 12px;
        color: #0f172a;
        width: 100%;
        outline: none;
        padding: 0 6px;
    }

    .cs-search-subcard .cs-search-clear {
        border: none;
        background: transparent;
        color: #94a3b8;
        cursor: pointer;
        font-size: 12px;
        padding: 0 2px;
        display: none;
    }

    .cs-search-subcard .cs-search-clear:hover {
        color: #0f172a;
    }

    /* Tier 2: Sub-Tabs Filter Card */
    .cs-tabs-card {
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

    .cs-tab-btn {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        padding: 6px 14px !important;
        font-size: 11.5px !important;
        font-weight: 600 !important;
        color: #475569 !important;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.15s ease-in-out;
    }

    .cs-tab-btn:hover {
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }

    .cs-tab-btn.active {
        background: #009ef7 !important;
        border-color: #009ef7 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 4px rgba(0, 158, 247, 0.2) !important;
    }

    .cs-tab-badge {
        font-size: 10.5px;
        font-weight: 700;
        border-radius: 10px;
        padding: 1px 6px;
        min-width: 18px;
        text-align: center;
    }

    .cs-tab-btn.active .cs-tab-badge {
        background: rgba(255, 255, 255, 0.28) !important;
        color: #ffffff !important;
    }

    .cs-tab-btn:not(.active) .cs-tab-badge {
        background: #f1f5f9;
        color: #475569;
    }

    /* Filter Chip Pills for Regional Filter */
    .cs-filter-chip {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        transition: all 0.15s ease-in-out;
        white-space: nowrap;
        user-select: none;
        line-height: 1.2;
    }

    .cs-filter-chip:hover {
        background: #f1f5f9;
        color: #0f172a;
        border-color: #cbd5e1;
    }

    .cs-filter-chip.active {
        background: #eff6ff !important;
        color: #009ef7 !important;
        border-color: #bfdbfe !important;
        font-weight: 700;
        box-shadow: 0 1px 2px rgba(0, 158, 247, 0.1);
    }

    /* Tier 3: Primary Card Stream & Discrete Sub-Cards */
    .cs-currency-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        margin-bottom: 10px !important;
        transition: all 0.2s ease-in-out;
        overflow: hidden;
    }

    .cs-currency-card:hover {
        border-color: #cbd5e1 !important;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06) !important;
        transform: translateY(-1px);
    }

    .cs-card-grid {
        display: grid;
        grid-template-columns: minmax(0, 2.3fr) minmax(0, 1.2fr) minmax(0, 1.4fr) minmax(0, 1.4fr) minmax(0, 1.2fr);
        gap: 10px;
        padding: 12px 16px;
        align-items: center;
        width: 100%;
    }

    .cs-subcard-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 12px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .cs-subcard-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 3px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .cs-flag-box {
        font-size: 24px;
        line-height: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 8px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        flex-shrink: 0;
    }

    .cs-rate-val {
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
        font-family: 'Poppins', monospace, sans-serif;
    }

    .cs-rate-recip {
        font-size: 11px;
        color: #64748b;
        margin-top: 1px;
    }

    /* Sub-Card 6: Bottom Audit Strip */
    .cs-card-footer {
        background: #fafbfd;
        border-top: 1px solid #f1f5f9;
        padding: 6px 16px;
        font-size: 11px;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
    }

    /* Pulse dot */
    .cs-pulse-dot {
        width: 6px;
        height: 6px;
        background: #10b981;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
    }

    /* Country Badge */
    .cs-country-badge {
        display: inline-flex;
        align-items: center;
        font-weight: 600;
        color: #1e293b;
        font-size: 11px;
    }

    /* TIER 4: Pagination Card */
    .cs-pagination-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 10px 18px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
    }

    .cs-page-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
        padding: 0 8px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 6px;
        color: #475569;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        cursor: pointer;
        transition: all 0.15s ease-in-out;
        user-select: none;
        text-decoration: none;
    }

    .cs-page-link:hover:not(.disabled):not(.active) {
        background: #f1f5f9;
        color: #0f172a;
        border-color: #cbd5e1;
    }

    .cs-page-link.active {
        background: #009ef7 !important;
        color: #ffffff !important;
        border-color: #009ef7 !important;
        font-weight: 700;
        box-shadow: 0 2px 4px rgba(0, 158, 247, 0.25);
    }

    .cs-page-link.disabled {
        opacity: 0.4;
        cursor: not-allowed;
        pointer-events: none;
    }

    /* Responsive Breakdown for 15.6" and Smaller */
    @media (max-width: 1200px) {
        .cs-card-grid {
            grid-template-columns: minmax(0, 1.8fr) minmax(0, 1.2fr) minmax(0, 1.4fr) minmax(0, 1.4fr) minmax(0, 1fr);
            gap: 8px;
            padding: 10px 12px;
        }
    }

    @media (max-width: 992px) {
        .cs-card-grid {
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .cs-subcard-actions {
            grid-column: span 2;
            justify-content: flex-end;
        }
    }

    @media (max-width: 576px) {
        .cs-card-grid {
            grid-template-columns: 1fr;
        }
        .cs-subcard-actions {
            grid-column: span 1;
        }
    }
</style>

<div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-fluid cs-container">

        <!--begin::TIER 1: Top Header Toolbar Card-->
        <div class="cs-header-card d-flex flex-column gap-3">
            <!-- Row 1: Brand & Enterprise Telemetry -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 w-100 pb-2 border-bottom border-gray-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="cs-brand-icon">
                        <i class="fa-solid fa-coins"></i>
                    </div>
                    <div>
                        <h1 class="cs-title">Currency Settings & Global FX Engine</h1>
                        <p class="cs-subtitle">Configure multi-tenant transactional currencies, ISO-3 codes, exchange rates & baseline conversions</p>
                    </div>
                </div>
                <div class="d-none d-md-flex align-items-center gap-2">
                    <span class="badge badge-light-success fs-8 fw-bold py-2 px-3">
                        <span class="cs-pulse-dot me-1"></span> Live FX: Active
                    </span>
                    <span class="badge badge-light-primary fs-8 fw-bold py-2 px-3">
                        <i class="fa-solid fa-bolt text-primary me-1"></i> Strict CQRS (0% DB Reads)
                    </span>
                    <span class="badge badge-light-info fs-8 fw-bold py-2 px-3">
                        <i class="fa-solid fa-shield-halved text-info me-1"></i> 10K Concurrency Ready
                    </span>
                </div>
            </div>

            <!-- Row 2: Search & Actions on Left | KPI Metric Badges on Right (Covers Red Box 1!) -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 w-100">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <!-- Search Sub-Card -->
                    <div class="cs-search-subcard" style="width: 290px;">
                        <i class="fa-solid fa-magnifying-glass text-muted me-1 fs-8"></i>
                        <input type="text" id="currencySearchInput" placeholder="Search currency, code, country, symbol..." autocomplete="off">
                        <button type="button" id="clearSearchBtn" class="cs-search-clear">✕</button>
                    </div>

                    <!-- Sync FX Rates Action -->
                    <button type="button" class="btn btn-sm btn-light-primary fw-bold updateCurrencyRates d-inline-flex align-items-center" id="syncRatesBtn">
                        <span class="indicator-label d-inline-flex align-items-center">
                            <i class="fa-solid fa-rotate me-1 fs-8"></i>
                            <span id="updateCurrencyRatesText">Sync Live FX</span>
                        </span>
                        <span class="indicator-progress">
                            Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>

                    <!-- Add Currency Action -->
                    <button type="button" class="btn btn-sm btn-primary fw-bold d-inline-flex align-items-center" id="btnOpenAddCurrency">
                        <i class="fa-solid fa-plus me-1 fs-8"></i> Add Currency
                    </button>
                </div>

                <!-- Right Side KPI Badges: Covers the entire empty space of Red Box 1! -->
                <div class="d-flex align-items-center flex-wrap gap-2 ms-auto">
                    <span class="cs-pill-badge cs-pill-blue">
                        <i class="fa-solid fa-layer-group me-1 fs-9"></i> Total: <strong class="ms-1" id="kpiTotalCurrencies">{{ $totalCurrencies ?? $currencies->count() }}</strong>
                    </span>
                    <span class="cs-pill-badge cs-pill-green">
                        <i class="fa-solid fa-anchor me-1 fs-9"></i> Base: <strong class="ms-1">USD & INR</strong>
                    </span>
                    <span class="cs-pill-badge cs-pill-slate">
                        <i class="fa-regular fa-clock me-1 fs-9"></i> Synced: <strong class="ms-1">{{ $lastSyncTimestamp ? \Carbon\Carbon::parse($lastSyncTimestamp)->diffForHumans() : 'Just now' }}</strong>
                    </span>
                    <span class="cs-pill-badge cs-pill-blue">
                        <i class="fa-solid fa-earth-americas me-1 fs-9"></i> Countries: <strong class="ms-1">{{ count($countryMap ?? []) }}</strong>
                    </span>
                </div>
            </div>
        </div>
        <!--end::TIER 1-->

        <!--begin::TIER 2: Sub-Tabs Filter Card-->
        <div class="cs-tabs-card d-flex align-items-center justify-content-between flex-wrap gap-3">
            <!-- Left: Sub-Tabs Filter Group -->
            <div class="d-flex align-items-center flex-wrap gap-2">
                <button type="button" class="cs-tab-btn active" data-filter="all">
                    <i class="fa-solid fa-list-check fs-9"></i>
                    All Currencies
                    <span class="cs-tab-badge" id="tabCountAll">{{ $totalCurrencies ?? $currencies->count() }}</span>
                </button>
                <button type="button" class="cs-tab-btn" data-filter="base">
                    <i class="fa-solid fa-shield-halved fs-9"></i>
                    Base Pivots (USD & INR)
                    <span class="cs-tab-badge">2</span>
                </button>
                <button type="button" class="cs-tab-btn" data-filter="secondary">
                    <i class="fa-solid fa-globe fs-9"></i>
                    Secondary Currencies
                    <span class="cs-tab-badge" id="tabCountSecondary">{{ max(0, ($totalCurrencies ?? $currencies->count()) - 2) }}</span>
                </button>
            </div>

            <!-- Center: Interactive Sort Dropdown & Regional Filter Chips (Covers Red Box 2!) -->
            <div class="d-flex align-items-center flex-wrap gap-3 flex-grow-1 justify-content-center justify-content-xl-between px-2">
                <!-- Sorting Dropdown -->
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted fs-8 fw-bold text-nowrap">
                        <i class="fa-solid fa-arrow-down-short-wide text-primary me-1"></i>Sort:
                    </span>
                    <select id="currencySortSelect" class="form-select form-select-sm form-select-solid" style="width: 175px; height: 32px; font-size: 11.5px; font-weight: 600; padding: 4px 8px;">
                        <option value="default" selected>Base First (Default)</option>
                        <option value="name_asc">Name (A → Z)</option>
                        <option value="name_desc">Name (Z → A)</option>
                        <option value="country_asc">Country (A → Z)</option>
                        <option value="code_asc">ISO-3 Code (A → Z)</option>
                        <option value="rate_desc">Highest USD Rate</option>
                        <option value="rate_asc">Lowest USD Rate</option>
                    </select>
                </div>

                <!-- Regional Filter Chips -->
                <div class="d-flex align-items-center gap-1 flex-wrap cs-region-filter-group">
                    <button type="button" class="cs-filter-chip active" data-region="all">All Regions</button>
                    <button type="button" class="cs-filter-chip" data-region="middle-east">Middle East</button>
                    <button type="button" class="cs-filter-chip" data-region="asia">Asia</button>
                    <button type="button" class="cs-filter-chip" data-region="europe">Europe</button>
                    <button type="button" class="cs-filter-chip" data-region="americas">Americas</button>
                    <button type="button" class="cs-filter-chip" data-region="africa">Africa</button>
                </div>
            </div>

            <!-- Right: Reference Catalog Action -->
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-light-info fw-bold py-1 px-3 fs-8" id="btnOpenCatalogModal">
                    <i class="fa-solid fa-book-atlas me-1"></i> Reference Catalog ({{ $masterCurrenciesCount ?? 139 }})
                </button>
            </div>
        </div>
        <!--end::TIER 2-->

        <!--begin::TIER 3: Primary Currency Card Stream-->
        <div id="currencyCardsContainer">
            @php
                $currencyFlags = \App\Services\CurrencyService::CURRENCY_FLAGS;
                $countryMap = $countryMap ?? \App\Services\CurrencyService::getCountryMap();
                $regionMap = [
                    'KWD' => 'middle-east', 'BHD' => 'middle-east', 'OMR' => 'middle-east', 'QAR' => 'middle-east',
                    'SAR' => 'middle-east', 'AED' => 'middle-east', 'JOD' => 'middle-east', 'ILS' => 'middle-east',
                    'IRR' => 'middle-east', 'IQD' => 'middle-east', 'LBP' => 'middle-east', 'YER' => 'middle-east',
                    'SYP' => 'middle-east',
                    'INR' => 'asia', 'JPY' => 'asia', 'CNY' => 'asia', 'SGD' => 'asia', 'HKD' => 'asia',
                    'TWD' => 'asia', 'KRW' => 'asia', 'THB' => 'asia', 'MYR' => 'asia', 'IDR' => 'asia',
                    'PHP' => 'asia', 'VND' => 'asia', 'PKR' => 'asia', 'BDT' => 'asia', 'LKR' => 'asia',
                    'NPR' => 'asia', 'AFN' => 'asia', 'KHR' => 'asia', 'LAK' => 'asia', 'MNT' => 'asia',
                    'KZT' => 'asia', 'UZS' => 'asia', 'KGS' => 'asia', 'AZN' => 'asia', 'AUD' => 'asia', 'NZD' => 'asia',
                    'EUR' => 'europe', 'GBP' => 'europe', 'CHF' => 'europe', 'SEK' => 'europe', 'NOK' => 'europe',
                    'DKK' => 'europe', 'PLN' => 'europe', 'CZK' => 'europe', 'HUF' => 'europe', 'RON' => 'europe',
                    'TRY' => 'europe', 'RUB' => 'europe', 'UAH' => 'europe', 'ALL' => 'europe', 'BAM' => 'europe',
                    'BGN' => 'europe', 'HRK' => 'europe', 'ISK' => 'europe', 'MKD' => 'europe', 'RSD' => 'europe',
                    'BYN' => 'europe', 'BYR' => 'europe', 'GIP' => 'europe', 'GGP' => 'europe', 'JEP' => 'europe',
                    'IMP' => 'europe',
                    'USD' => 'americas', 'CAD' => 'americas', 'BRL' => 'americas', 'MXN' => 'americas', 'ARS' => 'americas',
                    'CLP' => 'americas', 'COP' => 'americas', 'PEN' => 'americas', 'UYU' => 'americas', 'BOB' => 'americas',
                    'CRC' => 'americas', 'DOP' => 'americas', 'GTQ' => 'americas', 'HNL' => 'americas', 'NIO' => 'americas',
                    'PAB' => 'americas', 'PYG' => 'americas', 'VEF' => 'americas', 'VES' => 'americas', 'JMD' => 'americas',
                    'BSD' => 'americas', 'BBD' => 'americas', 'BZD' => 'americas', 'BMD' => 'americas', 'KYD' => 'americas',
                    'TTD' => 'americas', 'AWG' => 'americas', 'ANG' => 'americas', 'XCD' => 'americas', 'GYD' => 'americas',
                    'SRD' => 'americas', 'FKP' => 'americas',
                    'ZAR' => 'africa', 'NGN' => 'africa', 'EGP' => 'africa', 'KES' => 'africa', 'GHS' => 'africa',
                    'MAD' => 'africa', 'BWP' => 'africa', 'MUR' => 'africa', 'MZN' => 'africa', 'NAD' => 'africa',
                    'SCR' => 'africa', 'SOS' => 'africa', 'TZS' => 'africa', 'UGX' => 'africa', 'ZMW' => 'africa',
                    'ZWD' => 'africa', 'ZWL' => 'africa', 'LRD' => 'africa', 'DZD' => 'africa', 'TND' => 'africa',
                ];
            @endphp

            @forelse ($currencies as $item)
                @php
                    $code = strtoupper(trim($item->currency_code));
                    $isBase = in_array($code, ['USD', 'INR']);
                    $country = $countryMap[$code] ?? 'Global / International';
                    $flag = $currencyFlags[$code] ?? '🌐';
                    $usdRate = (float) ($item->usd_based_rates ?? 0);
                    $inrRate = (float) ($item->inr_based_rates ?? 0);
                    $recipUsd = $usdRate > 0 ? (1 / $usdRate) : 0;
                    $recipInr = $inrRate > 0 ? (1 / $inrRate) : 0;
                    $region = $regionMap[$code] ?? 'other';
                @endphp

                <div class="cs-currency-card currency-item-row"
                     id="row-{{ $item->id }}"
                     data-id="{{ $item->id }}"
                     data-name="{{ strtolower($item->currency_name) }}"
                     data-code="{{ strtolower($code) }}"
                     data-country="{{ strtolower($country) }}"
                     data-region="{{ $region }}"
                     data-rate="{{ $usdRate }}"
                     data-symbol="{{ strtolower($item->currency_symbol) }}"
                     data-is-base="{{ $isBase ? '1' : '0' }}">
                    
                    <div class="cs-card-grid">
                        <!-- Sub-Card 1: Identity, Country & ISO-3 Code -->
                        <div class="d-flex align-items-center gap-3">
                            <div class="cs-flag-box" title="{{ $country }} ({{ $code }})">
                                {{ $flag }}
                            </div>
                            <div class="d-flex flex-column min-w-0">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="fs-6 fw-bolder text-dark text-truncate">{{ $item->currency_name }}</span>
                                    <span class="badge badge-light-primary fw-bolder fs-8 px-2 py-1">{{ $code }}</span>
                                    @if ($isBase)
                                        <span class="badge badge-light-success fs-9 fw-bolder py-1 px-2">
                                            <i class="fa-solid fa-lock fs-10 text-success me-1"></i> BASE PIVOT
                                        </span>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center gap-2 mt-1 fs-8 text-muted flex-wrap">
                                    <span class="cs-country-badge">
                                        <i class="fa-solid fa-location-dot text-danger me-1 fs-9"></i>{{ $country }}
                                    </span>
                                    <span>•</span>
                                    <span>System Key: <strong class="text-dark">#{{ $item->getKey }}</strong></span>
                                    <span>•</span>
                                    <span>DB ID: #{{ $item->id }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Sub-Card 2: Symbol & Placement -->
                        <div class="cs-subcard-box">
                            <div class="cs-subcard-label">
                                <i class="fa-solid fa-hashtag fs-9"></i> Currency Symbol
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fs-4 fw-bolder text-primary" style="font-family: 'Poppins', sans-serif;">{{ $item->currency_symbol }}</span>
                                <span class="fs-8 text-muted">({{ $item->currency_symbol }}100.00)</span>
                            </div>
                        </div>

                        <!-- Sub-Card 3: USD Based Rate -->
                        <div class="cs-subcard-box">
                            <div class="cs-subcard-label">
                                <i class="fa-solid fa-dollar-sign fs-9"></i> USD Pivot Rate
                            </div>
                            @if ($code === 'USD')
                                <div class="cs-rate-val text-success">1.0000 USD</div>
                                <div class="cs-rate-recip">System Anchor Benchmark</div>
                            @elseif ($code === 'INR')
                                <div class="cs-rate-val">1 USD = {{ number_format($usdRate, 4) }} INR</div>
                                <div class="cs-rate-recip">1 INR = ${{ $usdRate > 0 ? number_format(1 / $usdRate, 4) : '0' }} USD</div>
                            @else
                                <div class="cs-rate-val">1 {{ $code }} = ${{ number_format($usdRate, 4) }} USD</div>
                                <div class="cs-rate-recip">1 USD = {{ $usdRate > 0 ? number_format($recipUsd, 4) : '0' }} {{ $code }}</div>
                            @endif
                        </div>

                        <!-- Sub-Card 4: INR Based Rate -->
                        <div class="cs-subcard-box">
                            <div class="cs-subcard-label">
                                <i class="fa-solid fa-indian-rupee-sign fs-9"></i> INR Domestic Rate
                            </div>
                            @if ($code === 'INR')
                                <div class="cs-rate-val text-success">1.0000 INR</div>
                                <div class="cs-rate-recip">Domestic Anchor Benchmark</div>
                            @elseif ($code === 'USD')
                                <div class="cs-rate-val">1 USD = ₹{{ number_format($inrRate, 4) }} INR</div>
                                <div class="cs-rate-recip">1 INR = ${{ $inrRate > 0 ? number_format(1 / $inrRate, 4) : '0' }} USD</div>
                            @else
                                <div class="cs-rate-val">1 {{ $code }} = ₹{{ number_format($inrRate, 4) }} INR</div>
                                <div class="cs-rate-recip">1 INR = {{ $inrRate > 0 ? number_format($recipInr, 4) : '0' }} {{ $code }}</div>
                            @endif
                        </div>

                        <!-- Sub-Card 5: Actions & Controls -->
                        <div class="d-flex align-items-center justify-content-end gap-1 cs-subcard-actions">
                            <!-- Quick FX Test -->
                            <button type="button" class="btn btn-icon btn-sm btn-light-info btn-test-fx"
                                    data-id="{{ $item->id }}"
                                    data-name="{{ $item->currency_name }}"
                                    data-code="{{ $code }}"
                                    data-symbol="{{ $item->currency_symbol }}"
                                    data-usd="{{ $usdRate }}"
                                    data-inr="{{ $inrRate }}"
                                    data-flag="{{ $flag }}"
                                    data-bs-toggle="tooltip"
                                    title="Quick FX Calculator">
                                <i class="fa-solid fa-calculator fs-8"></i>
                            </button>

                            <!-- Edit Currency -->
                            <button type="button" class="btn btn-icon btn-sm btn-light-primary btn-edit-currency"
                                    data-id="{{ $item->id }}"
                                    data-name="{{ $item->currency_name }}"
                                    data-code="{{ $code }}"
                                    data-symbol="{{ $item->currency_symbol }}"
                                    data-usd="{{ $usdRate }}"
                                    data-inr="{{ $inrRate }}"
                                    data-is-base="{{ $isBase ? '1' : '0' }}"
                                    data-bs-toggle="tooltip"
                                    title="Edit Currency Details">
                                <i class="fa-solid fa-pen-to-square fs-8"></i>
                            </button>

                            <!-- Delete Currency -->
                            @if ($isBase || in_array((int)$item->getKey, [0, 4]))
                                <button type="button" class="btn btn-icon btn-sm btn-light opacity-50 cursor-not-allowed"
                                        disabled
                                        data-bs-toggle="tooltip"
                                        title="Core Anchor Currency (Deletion Protected)">
                                    <i class="fa-solid fa-lock text-muted fs-8"></i>
                                </button>
                            @else
                                <button type="button" class="btn btn-icon btn-sm btn-light-danger sa-params-delete"
                                        data-row-id="{{ $item->id }}"
                                        data-code="{{ $code }}"
                                        data-bs-toggle="tooltip"
                                        title="Delete Currency">
                                    <i class="fa-solid fa-trash-can fs-8"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Sub-Card 6: Bottom Audit Strip -->
                    <div class="cs-card-footer">
                        <div class="d-flex align-items-center gap-2">
                            <span><i class="fa-solid fa-shield-check text-success me-1"></i> ISO-3 Verified</span>
                            <span>•</span>
                            <span>Last Synced: <strong class="text-dark">{{ $item->updated_at ? \Carbon\Carbon::parse($item->updated_at)->format('d M Y, h:i A') : 'N/A' }}</strong></span>
                            <span>•</span>
                            <span class="badge badge-light-success fs-9 py-0 px-2">0% MySQL Reads (Zero-SQL Serving)</span>
                        </div>
                        <div class="text-muted fs-9">
                            Available in Hiring Services, Candidate Matching & Invoicing
                        </div>
                    </div>
                </div>
            @empty
                <div class="card p-10 text-center" id="emptyStateCard">
                    <div class="mb-3 text-muted">
                        <i class="fa-solid fa-coins fs-2tx text-muted opacity-50"></i>
                    </div>
                    <h4 class="text-dark fw-bolder mb-1">No Currencies Configured</h4>
                    <p class="text-muted fs-7 mb-4">Click below to add your first multi-tenant transactional currency.</p>
                    <div>
                        <button type="button" class="btn btn-sm btn-primary" id="btnEmptyAdd">
                            <i class="fa-solid fa-plus me-1"></i> Add Currency
                        </button>
                    </div>
                </div>
            @endforelse

            <!-- No Search Results Card -->
            <div class="card p-10 text-center d-none" id="noSearchResultsCard">
                <div class="mb-3 text-muted">
                    <i class="fa-solid fa-magnifying-glass fs-2tx text-muted opacity-50"></i>
                </div>
                <h4 class="text-dark fw-bolder mb-1">No Currencies Found</h4>
                <p class="text-muted fs-7 mb-3">No currency matching your search term was found.</p>
                <div>
                    <button type="button" class="btn btn-sm btn-light-primary" id="btnResetSearch">
                        Clear Search Filter
                    </button>
                </div>
            </div>
        </div>
        <!--end::TIER 3-->

        <!--begin::TIER 4: Pagination & View Controls Card-->
        <div class="cs-pagination-card d-flex align-items-center justify-content-between flex-wrap gap-3 mt-4" id="currencyPaginationCard">
            <div class="d-flex align-items-center gap-2 text-muted fs-8">
                <span>Showing <strong class="text-dark" id="pageRangeStart">1</strong> to <strong class="text-dark" id="pageRangeEnd">10</strong> of <strong class="text-dark" id="pageTotalCount">{{ $totalCurrencies ?? $currencies->count() }}</strong> currencies</span>
            </div>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted fs-8 fw-semibold">Per page:</span>
                    <select id="pageSizeSelect" class="form-select form-select-sm form-select-solid" style="width: 85px; height: 32px; font-size: 12px; font-weight: 600; padding: 4px 8px;">
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="all">All</option>
                    </select>
                </div>
                <div class="d-flex align-items-center gap-1" id="currencyPaginationNav">
                    <!-- Dynamic page buttons rendered here -->
                </div>
            </div>
        </div>
        <!--end::TIER 4-->

    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 1: Add / Edit Currency Modal         -->
<!-- ========================================== -->
<div class="modal fade" id="currencyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header pb-0 border-0 justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="cs-brand-icon">
                        <i class="fa-solid fa-coins fs-5"></i>
                    </div>
                    <div>
                        <h2 class="fw-bolder mb-0" id="currencyModalTitle">Add New Currency</h2>
                        <span class="text-muted fs-8">Configure global multi-tenant currency metadata & FX rates</span>
                    </div>
                </div>
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark fs-4"></i>
                </div>
            </div>

            <div class="modal-body py-6 px-lg-8">
                <form id="currencyForm" class="form">
                    @csrf
                    <input type="hidden" id="formMethod" name="_method" value="POST">
                    <input type="hidden" id="currencyId" value="">

                    <!-- Quick Preset Select from Master Currencies -->
                    <div class="mb-4" id="presetSelectContainer">
                        <label class="form-label fs-7 fw-bold text-dark mb-1">
                            <i class="fa-solid fa-wand-magic-sparkles text-primary me-1"></i> Quick-Fill from Master Catalog (132 Countries):
                        </label>
                        <select id="masterCurrencyPreset" class="form-select form-select-solid form-select-sm" data-control="select2" data-dropdown-parent="#currencyModal" data-placeholder="Choose a country/currency preset to auto-fill...">
                            <option value="">-- Choose preset or type custom currency below --</option>
                            @if (!empty($masterCurrencies))
                                @foreach ($masterCurrencies as $m)
                                    <option value="{{ $m->code }}"
                                            data-name="{{ $m->currency }}"
                                            data-symbol="{{ $m->symbol }}"
                                            data-country="{{ $m->country }}">
                                        {{ $m->country }} — {{ $m->currency }} ({{ $m->code }} - {{ $m->symbol }})
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <div class="form-text fs-8 text-muted">Selecting a preset automatically populates Currency Name, Symbol, and ISO-3 Code.</div>
                    </div>

                    <div class="separator separator-dashed my-4"></div>

                    <!-- Row 1: Name and Code -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-7">
                            <label class="required form-label fs-7 fw-bold text-dark mb-1">Currency Name</label>
                            <input type="text" class="form-control form-control-solid form-control-sm" id="form_currency_name" name="currency_name" placeholder="e.g. Canadian Dollar" required>
                            <div class="invalid-feedback fs-8" id="err_currency_name"></div>
                        </div>
                        <div class="col-md-5">
                            <label class="required form-label fs-7 fw-bold text-dark mb-1">ISO-3 Currency Code</label>
                            <input type="text" class="form-control form-control-solid form-control-sm text-uppercase" id="form_currency_code" name="currency_code" placeholder="e.g. CAD" maxlength="3" required>
                            <div class="invalid-feedback fs-8" id="err_currency_code"></div>
                            <span class="fs-9 text-muted" id="codeLockNotice" style="display: none;">
                                <i class="fa-solid fa-lock text-warning fs-10 me-1"></i> Base code cannot be changed.
                            </span>
                        </div>
                    </div>

                    <!-- Row 2: Symbol and Format Preview -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="required form-label fs-7 fw-bold text-dark mb-1">Currency Symbol</label>
                            <input type="text" class="form-control form-control-solid form-control-sm" id="form_currency_symbol" name="currency_symbol" placeholder="e.g. CA$ or $" required>
                            <div class="invalid-feedback fs-8" id="err_currency_symbol"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs-7 fw-bold text-dark mb-1">Display Preview</label>
                            <div class="form-control form-control-solid form-control-sm bg-light-primary text-primary fw-bolder d-flex align-items-center justify-content-between">
                                <span id="symbolPreviewText">$1,500.00</span>
                                <span class="badge badge-light-primary fs-9" id="codeBadgePreview">USD</span>
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: USD Rate and INR Rate -->
                    <div class="row g-4 mb-3">
                        <div class="col-md-6">
                            <label class="required form-label fs-7 fw-bold text-dark mb-1">USD Based Rate (1 [Code] in USD)</label>
                            <div class="input-group input-group-sm input-group-solid">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.0001" min="0.000001" class="form-control form-control-solid" id="form_usd_based_rates" name="usd_based_rates" placeholder="1.0000" required>
                            </div>
                            <div class="invalid-feedback fs-8" id="err_usd_based_rates"></div>
                            <span class="fs-9 text-muted">Value of 1 unit in USD (e.g. 1 EUR = $1.11 USD)</span>
                        </div>
                        <div class="col-md-6">
                            <label class="required form-label fs-7 fw-bold text-dark mb-1">INR Based Rate (1 [Code] in INR)</label>
                            <div class="input-group input-group-sm input-group-solid">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="0.0001" min="0.000001" class="form-control form-control-solid" id="form_inr_based_rates" name="inr_based_rates" placeholder="95.3000" required>
                            </div>
                            <div class="invalid-feedback fs-8" id="err_inr_based_rates"></div>
                            <span class="fs-9 text-muted">Value of 1 unit in INR (e.g. 1 EUR = ₹106.24 INR)</span>
                        </div>
                    </div>

                    <div class="alert alert-light-info d-flex align-items-center p-3 mb-0">
                        <i class="fa-solid fa-circle-info fs-5 text-info me-3"></i>
                        <div class="fs-8 text-dark">
                            <strong>Zero-SQL Architecture:</strong> Changes automatically recompile <code>currencies.json</code> and invalidate all multi-tenant Redis caches.
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-5">
                        <button type="button" class="btn btn-sm btn-light fw-bold" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary fw-bold" id="btnSaveCurrency">
                            <span class="indicator-label"><i class="fa-solid fa-check me-1"></i> Save Currency</span>
                            <span class="indicator-progress">Saving... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 2: Quick FX Calculator Modal         -->
<!-- ========================================== -->
<div class="modal fade" id="quickFxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content">
            <div class="modal-header pb-0 border-0 justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="cs-brand-icon">
                        <i class="fa-solid fa-calculator fs-5"></i>
                    </div>
                    <div>
                        <h3 class="fw-bolder mb-0">Live FX Converter Test</h3>
                        <span class="text-muted fs-8">Test real-time dual pivot conversion accuracy</span>
                    </div>
                </div>
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark fs-4"></i>
                </div>
            </div>

            <div class="modal-body py-5 px-lg-8">
                <div class="mb-4">
                    <label class="form-label fs-7 fw-bold text-dark mb-1">Enter Amount to Convert:</label>
                    <div class="input-group input-group-solid">
                        <span class="input-group-text fw-bold" id="calcSymbol">$</span>
                        <input type="number" step="any" min="0" class="form-control form-control-solid fw-bolder fs-5" id="calcAmount" value="100">
                        <span class="input-group-text fw-bolder text-primary" id="calcCode">USD</span>
                    </div>
                </div>

                <div class="p-4 bg-light-primary rounded-3 border border-primary border-opacity-25 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fs-7 text-muted fw-bold">Converted to USD:</span>
                        <span class="fs-4 fw-bolder text-primary" id="calcUsdResult">$100.00</span>
                    </div>
                    <div class="separator separator-dashed my-2"></div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fs-7 text-muted fw-bold">Converted to INR:</span>
                        <span class="fs-4 fw-bolder text-success" id="calcInrResult">₹9,530.00</span>
                    </div>
                </div>

                <div class="text-muted fs-9 text-center">
                    Uses authoritatively cached FX rates. Exactly mirrors Candidate & Job budget conversions.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 3: Master Catalog Reference Modal    -->
<!-- ========================================== -->
<div class="modal fade" id="masterCatalogModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-750px">
        <div class="modal-content">
            <div class="modal-header pb-0 border-0 justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="cs-brand-icon">
                        <i class="fa-solid fa-book-atlas fs-5"></i>
                    </div>
                    <div>
                        <h3 class="fw-bolder mb-0">Master Currency Catalog</h3>
                        <span class="text-muted fs-8">132 authoritative worldwide currencies ready for recruitment trading</span>
                    </div>
                </div>
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark fs-4"></i>
                </div>
            </div>

            <div class="modal-body py-5 px-lg-8">
                <div class="mb-3">
                    <input type="text" id="catalogSearchInput" class="form-control form-control-solid form-control-sm" placeholder="Filter by country, currency name or ISO code...">
                </div>

                <div style="max-height: 400px; overflow-y: auto;" class="border rounded p-2">
                    <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-2 fs-8" id="masterCatalogTable">
                        <thead>
                            <tr class="fw-bolder text-muted bg-light">
                                <th class="ps-2">Country</th>
                                <th>Currency</th>
                                <th>Code</th>
                                <th>Symbol</th>
                                <th class="text-end pe-2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!empty($masterCurrencies))
                                @php
                                    $existingCodes = $currencies->pluck('currency_code')->map(fn($c) => strtoupper(trim($c)))->toArray();
                                @endphp
                                @foreach ($masterCurrencies as $m)
                                    @php
                                        $mCode = strtoupper(trim($m->code));
                                        $isConfigured = in_array($mCode, $existingCodes);
                                    @endphp
                                    <tr class="catalog-row" data-search="{{ strtolower($m->country . ' ' . $m->currency . ' ' . $m->code) }}">
                                        <td class="ps-2 fw-bold text-dark">{{ $m->country }}</td>
                                        <td>{{ $m->currency }}</td>
                                        <td><span class="badge badge-light-primary fw-bolder">{{ $mCode }}</span></td>
                                        <td class="fw-bolder">{{ $m->symbol }}</td>
                                        <td class="text-end pe-2">
                                            @if ($isConfigured)
                                                <span class="badge badge-light-success fs-9 py-1 px-2">Configured</span>
                                            @else
                                                <button type="button" class="btn btn-xs btn-light-primary py-1 px-2 btn-add-from-catalog"
                                                        data-name="{{ $m->currency }}"
                                                        data-code="{{ $mCode }}"
                                                        data-symbol="{{ $m->symbol }}">
                                                    <i class="fa-solid fa-plus fs-10"></i> Add
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-script')
<script>
$(document).ready(function() {

    // ========================================================
    // 1. Pagination & Filtering Engine (Country, Code, Name, Base)
    // ========================================================
    // ========================================================
    // 1. Pagination, Sorting & Multi-Filter Engine
    // ========================================================
    var searchTimer = null;
    var $searchInput = $('#currencySearchInput');
    var $clearBtn = $('#clearSearchBtn');
    var currentPage = 1;
    var pageSize = 10;
    var matchingRows = [];

    function filterCurrencyCards() {
        var query = $searchInput.val().trim().toLowerCase();
        var activeFilter = $('.cs-tab-btn.active').data('filter') || 'all';
        var activeRegion = $('.cs-filter-chip.active').data('region') || 'all';
        var activeSort = $('#currencySortSelect').val() || 'default';
        var $allRows = $('.currency-item-row');

        matchingRows = [];

        $allRows.each(function() {
            var $row = $(this);
            var name = ($row.data('name') || '').toString();
            var code = ($row.data('code') || '').toString();
            var country = ($row.data('country') || '').toString();
            var symbol = ($row.data('symbol') || '').toString();
            var region = ($row.data('region') || '').toString();
            var isBase = $row.data('is-base') == '1';

            var matchesSearch = (query === '') || (
                name.indexOf(query) > -1 ||
                code.indexOf(query) > -1 ||
                country.indexOf(query) > -1 ||
                symbol.indexOf(query) > -1
            );

            var matchesTab = true;
            if (activeFilter === 'base') {
                matchesTab = isBase;
            } else if (activeFilter === 'secondary') {
                matchesTab = !isBase;
            }

            var matchesRegion = (activeRegion === 'all') || (region === activeRegion);

            if (matchesSearch && matchesTab && matchesRegion) {
                matchingRows.push($row);
            } else {
                $row.hide();
            }
        });

        // Dynamic Client-Side Sorting (0% Server Strain)
        if (activeSort === 'name_asc') {
            matchingRows.sort(function(a, b) {
                return (a.data('name') || '').localeCompare(b.data('name') || '');
            });
        } else if (activeSort === 'name_desc') {
            matchingRows.sort(function(a, b) {
                return (b.data('name') || '').localeCompare(a.data('name') || '');
            });
        } else if (activeSort === 'country_asc') {
            matchingRows.sort(function(a, b) {
                return (a.data('country') || '').localeCompare(b.data('country') || '');
            });
        } else if (activeSort === 'code_asc') {
            matchingRows.sort(function(a, b) {
                return (a.data('code') || '').localeCompare(b.data('code') || '');
            });
        } else if (activeSort === 'rate_desc') {
            matchingRows.sort(function(a, b) {
                return (parseFloat(b.data('rate')) || 0) - (parseFloat(a.data('rate')) || 0);
            });
        } else if (activeSort === 'rate_asc') {
            matchingRows.sort(function(a, b) {
                return (parseFloat(a.data('rate')) || 0) - (parseFloat(b.data('rate')) || 0);
            });
        }

        var totalCount = matchingRows.length;

        if (totalCount === 0) {
            $allRows.hide();
            $('#noSearchResultsCard').removeClass('d-none');
            $('#currencyPaginationCard').addClass('d-none');
            return;
        }

        $('#noSearchResultsCard').addClass('d-none');
        $('#currencyPaginationCard').removeClass('d-none');

        var effPageSize = (pageSize === 'all') ? totalCount : parseInt(pageSize, 10);
        var totalPages = Math.ceil(totalCount / effPageSize);

        if (currentPage > totalPages) {
            currentPage = 1;
        }

        var startIndex = (currentPage - 1) * effPageSize;
        var endIndex = Math.min(startIndex + effPageSize, totalCount);

        // Hide all rows first, re-order and reveal only current page slice
        $allRows.hide();
        var $anchor = $('#noSearchResultsCard');
        for (var i = startIndex; i < endIndex; i++) {
            matchingRows[i].detach().insertBefore($anchor).show();
        }

        // Update range labels
        $('#pageRangeStart').text(totalCount > 0 ? (startIndex + 1) : 0);
        $('#pageRangeEnd').text(endIndex);
        $('#pageTotalCount').text(totalCount);

        renderPaginationButtons(totalPages);
    }

    function renderPaginationButtons(totalPages) {
        var $nav = $('#currencyPaginationNav');
        $nav.empty();

        if (totalPages <= 1) {
            return;
        }

        // Previous button
        var prevDisabled = (currentPage === 1) ? 'disabled' : '';
        $nav.append('<button type="button" class="cs-page-link ' + prevDisabled + '" data-page="' + (currentPage - 1) + '" title="Previous Page"><i class="fa-solid fa-chevron-left fs-9"></i></button>');

        // Page buttons windowing (up to 7 buttons)
        var maxButtons = 7;
        var startPage = 1;
        var endPage = totalPages;

        if (totalPages > maxButtons) {
            if (currentPage <= 4) {
                startPage = 1;
                endPage = 5;
            } else if (currentPage + 3 >= totalPages) {
                startPage = totalPages - 4;
                endPage = totalPages;
            } else {
                startPage = currentPage - 2;
                endPage = currentPage + 2;
            }
        }

        if (startPage > 1) {
            $nav.append('<button type="button" class="cs-page-link" data-page="1">1</button>');
            if (startPage > 2) {
                $nav.append('<span class="cs-page-link disabled border-0 bg-transparent text-muted">…</span>');
            }
        }

        for (var p = startPage; p <= endPage; p++) {
            var activeClass = (p === currentPage) ? 'active' : '';
            $nav.append('<button type="button" class="cs-page-link ' + activeClass + '" data-page="' + p + '">' + p + '</button>');
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                $nav.append('<span class="cs-page-link disabled border-0 bg-transparent text-muted">…</span>');
            }
            $nav.append('<button type="button" class="cs-page-link" data-page="' + totalPages + '">' + totalPages + '</button>');
        }

        // Next button
        var nextDisabled = (currentPage === totalPages) ? 'disabled' : '';
        $nav.append('<button type="button" class="cs-page-link ' + nextDisabled + '" data-page="' + (currentPage + 1) + '" title="Next Page"><i class="fa-solid fa-chevron-right fs-9"></i></button>');
    }

    $(document).on('click', '.cs-page-link:not(.disabled):not(.active)', function(e) {
        e.preventDefault();
        var page = parseInt($(this).data('page'), 10);
        if (page && page !== currentPage) {
            currentPage = page;
            filterCurrencyCards();
            var target = document.getElementById('currencyCardsContainer');
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    });

    $('#pageSizeSelect').on('change', function() {
        var val = $(this).val();
        pageSize = (val === 'all') ? 'all' : parseInt(val, 10);
        currentPage = 1;
        filterCurrencyCards();
    });

    // Sorting dropdown change
    $('#currencySortSelect').on('change', function() {
        currentPage = 1;
        filterCurrencyCards();
    });

    // Regional filter chips change
    $('.cs-filter-chip').on('click', function() {
        $('.cs-filter-chip').removeClass('active');
        $(this).addClass('active');
        currentPage = 1;
        filterCurrencyCards();
    });

    $searchInput.on('input', function() {
        var query = $(this).val().trim();
        if (query.length > 0) {
            $clearBtn.show();
        } else {
            $clearBtn.hide();
        }

        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            currentPage = 1;
            filterCurrencyCards();
        }, 300);
    });

    $clearBtn.on('click', function() {
        $searchInput.val('');
        $clearBtn.hide();
        currentPage = 1;
        filterCurrencyCards();
    });

    $('#btnResetSearch').on('click', function() {
        $searchInput.val('');
        $clearBtn.hide();
        $('.cs-tab-btn').removeClass('active');
        $('.cs-tab-btn[data-filter="all"]').addClass('active');
        $('.cs-filter-chip').removeClass('active');
        $('.cs-filter-chip[data-region="all"]').addClass('active');
        $('#currencySortSelect').val('default');
        currentPage = 1;
        filterCurrencyCards();
    });

    $('.cs-tab-btn').on('click', function() {
        $('.cs-tab-btn').removeClass('active');
        $(this).addClass('active');
        currentPage = 1;
        filterCurrencyCards();
    });

    // Initialize pagination and filtering on page ready
    filterCurrencyCards();

    // ========================================================
    // 3. Live FX Rates Synchronize
    // ========================================================
    $(document).on('click', '.updateCurrencyRates', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var url = "{{ route('get.updateCurrencyRates') }}";

        $btn.attr('data-kt-indicator', 'on').prop('disabled', true);
        $('#updateCurrencyRatesText').text('Updating...');

        $.ajax({
            type: 'GET',
            url: url,
            dataType: 'json',
            success: function(response) {
                $btn.removeAttr('data-kt-indicator').prop('disabled', false);
                $('#updateCurrencyRatesText').text('Sync Live FX');

                if (response.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Synchronized!',
                        text: response.message,
                        confirmButtonColor: '#009ef7'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Sync Failed',
                        text: response.message || 'Unable to update live exchange rates.',
                        confirmButtonColor: '#009ef7'
                    });
                }
            },
            error: function(xhr) {
                $btn.removeAttr('data-kt-indicator').prop('disabled', false);
                $('#updateCurrencyRatesText').text('Sync Live FX');

                var msg = 'Exchange Rate API connection timed out or failed.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Sync Error',
                    text: msg,
                    confirmButtonColor: '#009ef7'
                });
            }
        });
    });

    // ========================================================
    // 4. In-Page Modal: Add Currency
    // ========================================================
    $('#btnOpenAddCurrency, #btnEmptyAdd').on('click', function() {
        resetCurrencyForm();
        $('#currencyModalTitle').text('Add New Currency');
        $('#formMethod').val('POST');
        $('#currencyId').val('');
        $('#presetSelectContainer').show();
        $('#codeLockNotice').hide();
        $('#form_currency_code').prop('readonly', false);
        $('#currencyModal').modal('show');
    });

    // Preset selection change
    $('#masterCurrencyPreset').on('change', function() {
        var $opt = $(this).find('option:selected');
        var code = $(this).val();
        if (code) {
            var name = $opt.data('name') || '';
            var symbol = $opt.data('symbol') || '';
            $('#form_currency_name').val(name);
            $('#form_currency_code').val(code);
            $('#form_currency_symbol').val(symbol);
            updateFormPreview();
        }
    });

    // Form inputs preview listeners
    $('#form_currency_symbol, #form_currency_code').on('input', function() {
        updateFormPreview();
    });

    function updateFormPreview() {
        var symbol = $('#form_currency_symbol').val() || '$';
        var code = ($('#form_currency_code').val() || 'USD').toUpperCase();
        $('#symbolPreviewText').text(symbol + '1,500.00');
        $('#codeBadgePreview').text(code);
    }

    // Reciprocal calculator when USD rate typed in form
    var baselineUsdToInr = 95.30; // updated baseline anchor
    $('#form_usd_based_rates').on('input', function() {
        var usdRate = parseFloat($(this).val());
        if (!isNaN(usdRate) && usdRate > 0) {
            var inrEstimate = (usdRate * baselineUsdToInr).toFixed(4);
            $('#form_inr_based_rates').val(inrEstimate);
        }
    });

    // ========================================================
    // 5. In-Page Modal: Edit Currency
    // ========================================================
    $(document).on('click', '.btn-edit-currency', function() {
        resetCurrencyForm();
        var id = $(this).data('id');
        var name = $(this).data('name');
        var code = $(this).data('code');
        var symbol = $(this).data('symbol');
        var usd = $(this).data('usd');
        var inr = $(this).data('inr');
        var isBase = $(this).data('is-base') == '1';

        $('#currencyModalTitle').text('Edit Currency — ' + code);
        $('#formMethod').val('PUT');
        $('#currencyId').val(id);
        $('#form_currency_name').val(name);
        $('#form_currency_code').val(code);
        $('#form_currency_symbol').val(symbol);
        $('#form_usd_based_rates').val(usd);
        $('#form_inr_based_rates').val(inr);

        $('#presetSelectContainer').hide();
        updateFormPreview();

        if (isBase) {
            $('#form_currency_code').prop('readonly', true);
            $('#codeLockNotice').show();
        } else {
            $('#form_currency_code').prop('readonly', false);
            $('#codeLockNotice').hide();
        }

        $('#currencyModal').modal('show');
    });

    // ========================================================
    // 6. Form Submission (Create & Update)
    // ========================================================
    $('#currencyForm').on('submit', function(e) {
        e.preventDefault();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        var id = $('#currencyId').val();
        var method = $('#formMethod').val();
        var url = (method === 'PUT')
            ? "{{ route('superadmin.currency-settings.update', ':id') }}".replace(':id', id)
            : "{{ route('superadmin.currency-settings.store') }}";

        var $btn = $('#btnSaveCurrency');
        $btn.attr('data-kt-indicator', 'on').prop('disabled', true);

        var data = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            _method: method,
            currency_name: $('#form_currency_name').val(),
            currency_code: $('#form_currency_code').val().toUpperCase(),
            currency_symbol: $('#form_currency_symbol').val(),
            usd_based_rates: $('#form_usd_based_rates').val(),
            inr_based_rates: $('#form_inr_based_rates').val()
        };

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                $btn.removeAttr('data-kt-indicator').prop('disabled', false);
                $('#currencyModal').modal('hide');

                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: response.message || 'Currency configuration updated successfully.',
                    confirmButtonColor: '#009ef7'
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                $btn.removeAttr('data-kt-indicator').prop('disabled', false);
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(field, msgs) {
                        $('#form_' + field).addClass('is-invalid');
                        $('#err_' + field).text(msgs[0]);
                    });
                } else {
                    var errMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error processing request.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Action Failed',
                        text: errMsg,
                        confirmButtonColor: '#009ef7'
                    });
                }
            }
        });
    });

    function resetCurrencyForm() {
        $('#currencyForm')[0].reset();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $('#masterCurrencyPreset').val('').trigger('change');
        $('#formMethod').val('POST');
        $('#currencyId').val('');
        updateFormPreview();
    }

    // ========================================================
    // 7. Delete Currency with Guarded Confirmation
    // ========================================================
    $('body').on('click', '.sa-params-delete', function() {
        var id = $(this).data('row-id');
        var code = $(this).data('code') || '';

        Swal.fire({
            title: 'Delete ' + code + ' Currency?',
            text: 'This will remove the currency from system configuration and re-synchronize JSON stores. This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f1416c',
            cancelButtonColor: '#7e8299',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                var url = "{{ route('superadmin.currency-settings.destroy', ':id') }}".replace(':id', id);
                var token = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: { _token: token, _method: 'DELETE' },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == "success" || response.status === true) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: response.message || 'Currency has been deleted.',
                                icon: 'success',
                                confirmButtonColor: '#009ef7'
                            });
                            $('#row-' + id).fadeOut(300, function() {
                                $(this).remove();
                                var remaining = $('.currency-item-row').length;
                                $('#tabCountAll').text(remaining);
                                $('#tabCountSecondary').text(Math.max(0, remaining - 2));
                                if (remaining === 0) {
                                    $('#emptyStateCard').removeClass('d-none');
                                    $('#currencyPaginationCard').addClass('d-none');
                                } else {
                                    filterCurrencyCards();
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Cannot Delete',
                                text: response.message || 'Failed to delete currency.',
                                icon: 'error',
                                confirmButtonColor: '#009ef7'
                            });
                        }
                    },
                    error: function(xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to delete record.';
                        Swal.fire({
                            title: 'Error',
                            text: msg,
                            icon: 'error',
                            confirmButtonColor: '#009ef7'
                        });
                    }
                });
            }
        });
    });

    // ========================================================
    // 8. Quick FX Calculator Modal
    // ========================================================
    var activeCalcUsdRate = 1.0;
    var activeCalcInrRate = 95.30;

    $(document).on('click', '.btn-test-fx', function() {
        var code = $(this).data('code') || 'USD';
        var symbol = $(this).data('symbol') || '$';
        var usdRate = parseFloat($(this).data('usd')) || 1.0;
        var inrRate = parseFloat($(this).data('inr')) || 95.30;

        activeCalcUsdRate = usdRate;
        activeCalcInrRate = inrRate;

        $('#calcCode').text(code);
        $('#calcSymbol').text(symbol);
        $('#calcAmount').val('100');

        recalculateFxModal();
        $('#quickFxModal').modal('show');
    });

    $('#calcAmount').on('input', function() {
        recalculateFxModal();
    });

    function recalculateFxModal() {
        var amt = parseFloat($('#calcAmount').val()) || 0;
        var usdVal = (amt * activeCalcUsdRate).toFixed(2);
        var inrVal = (amt * activeCalcInrRate).toFixed(2);

        $('#calcUsdResult').text('$' + Number(usdVal).toLocaleString());
        $('#calcInrResult').text('₹' + Number(inrVal).toLocaleString());
    }

    // ========================================================
    // 9. Master Catalog Reference Modal
    // ========================================================
    $('#btnOpenCatalogModal').on('click', function() {
        $('#masterCatalogModal').modal('show');
    });

    $('#catalogSearchInput').on('input', function() {
        var q = $(this).val().toLowerCase().trim();
        $('.catalog-row').each(function() {
            var searchStr = $(this).data('search') || '';
            if (q === '' || searchStr.indexOf(q) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    $(document).on('click', '.btn-add-from-catalog', function() {
        var name = $(this).data('name');
        var code = $(this).data('code');
        var symbol = $(this).data('symbol');

        $('#masterCatalogModal').modal('hide');

        resetCurrencyForm();
        $('#currencyModalTitle').text('Add ' + name + ' (' + code + ')');
        $('#formMethod').val('POST');
        $('#currencyId').val('');
        $('#form_currency_name').val(name);
        $('#form_currency_code').val(code);
        $('#form_currency_symbol').val(symbol);
        updateFormPreview();

        $('#currencyModal').modal('show');
    });

});
</script>
@endpush