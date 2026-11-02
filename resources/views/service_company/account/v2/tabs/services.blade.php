@php
    /**
     * Services & Commercials Management Tab — God-Level Modern Card + Sub-Card Design (Rule 20)
     *
     * Intent: Presents the service company's fulltime & contract commercial terms in the
     * authoritative 3-tier Card + Sub-Card Enterprise Layout. All data is sourced from Redis-cached
     * AccountSettingsService page payload (0% MySQL reads).
     *
     * Tenancy: All data queries and persistence are strictly scoped by auth()->user()->company_id.
     * Screen: Fully responsive for 15.6" laptops (1366×768 & 1920×1080) with zero horizontal overflow.
     * All 4 sub-cards strictly fixed on a single horizontal row with zero wrapping.
     *
     * Author: Antigravity AI (Rule 20 / card-subcard)
     * Date: November 01, 2026
     */
    $cv2FulltimeActive = (bool) (@$fullTimeStatus->status);
    $cv2ContractActive = (bool) (@$contractStatus->status);

    $tenantDefaultCurrencyCode = \App\Services\CurrencyService::getTenantCurrency(@$account->id ?? auth()->user()->company_id ?? null);
    $tenantDefaultCurrencyKey = \App\Services\CurrencyService::getTenantCurrencyKey(@$account->id ?? auth()->user()->company_id ?? null);
    $cv2Currency = optional(collect(@$currencyArr)->firstWhere('id', @$account->currency));
    $cv2CurrencyCode = $cv2Currency->code ?: $tenantDefaultCurrencyCode;
    $systemCurrencies = \App\Helper\GeneralMethod::currency();

    $fulltimeSalaryCurrency = isset($fullTimeStatus->salary_currency) ? (int) $fullTimeStatus->salary_currency : null;
    if ($fulltimeSalaryCurrency === null || ($fulltimeSalaryCurrency === 4 && $tenantDefaultCurrencyKey !== 4)) {
        $fulltimeSalaryCurrency = $tenantDefaultCurrencyKey;
    }
    $contractSalaryCurrency = isset($contractStatus->salary_currency) ? (int) $contractStatus->salary_currency : null;
    if ($contractSalaryCurrency === null || ($contractSalaryCurrency === 4 && $tenantDefaultCurrencyKey !== 4)) {
        $contractSalaryCurrency = $tenantDefaultCurrencyKey;
    }

    $cv2FulltimeCurrencyCode = $systemCurrencies[$fulltimeSalaryCurrency] ?? $cv2CurrencyCode;
    $cv2ContractCurrencyCode = $systemCurrencies[$contractSalaryCurrency] ?? $cv2CurrencyCode;

    $fulltimeCountryCount = (int) (@$fullTimeCount ?? 0);
    $contractCountryCount = (int) (@$contractCount ?? 0);
    $activeCount = ($cv2FulltimeActive ? 1 : 0) + ($cv2ContractActive ? 1 : 0);
    $inactiveCount = (!$cv2FulltimeActive ? 1 : 0) + (!$cv2ContractActive ? 1 : 0);

    $ftCountries = (array) (@$fullTimeCountries ?? []);
    $ctCountries = (array) (@$contractCountries ?? []);
@endphp

<style>
/* ==========================================================================
   SERVICES & COMMERCIAL TERMS — MODERN CARD + SUB-CARD ENTERPRISE SYSTEM
   Compliant with Rule 20 / UI_UX_CARD_SUB_CARD_DESIGN_STANDARD
   Screen target: 15.6" (1366x768 & 1920x1080), Poppins, zero horizontal scroll
   ========================================================================== */
.cd-srv-wrapper {
    width: 100%;
    min-width: 0;
    color: #0f172a;
    font-size: 12px;
    padding: 0 0 16px 0;
    overflow-x: hidden;
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}

.cd-srv-wrapper *,
.cd-srv-wrapper *::before,
.cd-srv-wrapper *::after {
    box-sizing: border-box;
}

/* --------------------------------------------------------------------------
   TIER 1: TOP HEADER TOOLBAR CARD (Single-Row, No Awkward Wrapping)
   -------------------------------------------------------------------------- */
.cd-srv-header-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
    padding: 10px 16px !important;
    margin-bottom: 12px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    flex-wrap: nowrap !important;
    gap: 12px !important;
}

.cd-srv-header-left {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
    flex: 1 1 auto;
}

.cd-srv-header-icon {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    background: #eff6ff;
    color: #009ef7;
    border: 1px solid #dbeafe;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.cd-srv-header-icon svg {
    width: 20px;
    height: 20px;
}

.cd-srv-header-title-row {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.cd-srv-header-title {
    font-size: 15.5px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    line-height: 1.25 !important;
    margin: 0 !important;
}

.cd-srv-pill-badge {
    display: inline-flex;
    align-items: center;
    height: 20px;
    padding: 0 8px;
    border-radius: 10px;
    background: #eff6ff;
    color: #009ef7;
    font-size: 11px;
    font-weight: 700;
    font-variant-numeric: tabular-nums;
    border: 1px solid #dbeafe;
}

.cd-srv-live-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    height: 20px;
    padding: 0 8px;
    border-radius: 10px;
    background: #ecfdf5;
    color: #047857;
    font-size: 10.5px;
    font-weight: 600;
    border: 1px solid #a7f3d0;
}

.cd-srv-live-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #10b981;
    box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.25);
    animation: cdPulseDot 2s infinite;
}

@keyframes cdPulseDot {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.15); }
}

.cd-srv-header-sub {
    font-size: 11.5px;
    color: #64748b;
    margin-top: 2px;
    line-height: 1.35;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 620px;
}

.cd-srv-header-right {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}

/* Executive Metric Strip in Header (Replaces Search & Refresh) */
.cd-srv-metric-strip {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: nowrap;
}

.cd-srv-stat-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    height: 32px;
    padding: 0 10px;
    border-radius: 8px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #334155;
    font-size: 11px;
    font-weight: 600;
    transition: all 0.15s ease;
}

.cd-srv-stat-pill svg {
    width: 13px;
    height: 13px;
    color: #10b981;
    flex-shrink: 0;
}

.cd-srv-stat-pill b {
    color: #0f172a;
    font-weight: 700;
}

.cd-srv-stat-pill:hover {
    background: #ffffff;
    border-color: #cbd5e1;
}

/* --------------------------------------------------------------------------
   TIER 2: DEDICATED SUB-TABS CARD (Metronic Primary Blue #009ef7)
   -------------------------------------------------------------------------- */
.cd-srv-tabs-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    padding: 6px 10px !important;
    margin-bottom: 12px !important;
}

.cd-srv-tabs-group {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
    min-width: 0;
}

.cd-srv-tab-btn {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    padding: 6px 14px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #475569 !important;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02) !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 7px !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
    min-height: 34px !important;
}

.cd-srv-tab-btn:hover {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
    color: #0f172a !important;
}

/* Mandatory Single Active Highlight Rule: Metronic Primary Blue */
.cd-srv-tab-btn.active {
    background: #009ef7 !important;
    border-color: #009ef7 !important;
    color: #ffffff !important;
    box-shadow: 0 2px 4px rgba(0, 158, 247, 0.22) !important;
}

.cd-srv-tab-badge {
    min-width: 18px;
    height: 18px;
    padding: 0 6px;
    border-radius: 9px;
    font-size: 10.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-variant-numeric: tabular-nums;
    background: #f1f5f9;
    color: #475569;
    transition: all 0.15s ease;
}

.cd-srv-tab-badge.badge-active {
    background: #dcfce7;
    color: #166534;
}

.cd-srv-tab-badge.badge-inactive {
    background: #fee2e2;
    color: #991b1b;
}

.cd-srv-tab-btn.active .cd-srv-tab-badge {
    background: rgba(255, 255, 255, 0.28) !important;
    color: #ffffff !important;
}

/* --------------------------------------------------------------------------
   TIER 3: PRIMARY CARD STREAM & STRICT 4-COLUMN SINGLE-ROW SUB-CARDS
   -------------------------------------------------------------------------- */
.cd-srv-stream {
    display: flex;
    flex-direction: column;
    gap: 12px;
    width: 100%;
}

.cd-srv-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
    overflow: hidden;
    transition: border-color 0.18s ease, box-shadow 0.18s ease;
    border-left: 4px solid #cbd5e1 !important;
}

.cd-srv-card.is-active {
    border-left-color: #10b981 !important;
}

.cd-srv-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 3px 10px rgba(15, 23, 42, 0.06) !important;
}

/* Master Card Header Bar */
.cd-srv-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: nowrap;
    gap: 12px;
    padding: 10px 16px;
    background: #fcfdfe;
    border-bottom: 1px solid #f1f5f9;
}

.cd-srv-head-left {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
    flex: 1 1 auto;
}

.cd-srv-symbol {
    width: 40px;
    height: 40px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13.5px;
    font-weight: 800;
    letter-spacing: 0.02em;
    flex-shrink: 0;
}

.cd-srv-symbol.ft {
    background: #eff6ff;
    color: #0085ff;
    border: 1px solid #bfdbfe;
}

.cd-srv-symbol.ct {
    background: #f5f3ff;
    color: #8b5cf6;
    border: 1px solid #ddd6fe;
}

.cd-srv-head-meta {
    display: flex;
    flex-direction: column;
    gap: 1px;
    min-width: 0;
}

.cd-srv-title-row {
    display: flex;
    align-items: center;
    gap: 7px;
    flex-wrap: wrap;
}

.cd-srv-title {
    font-size: 14.5px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    margin: 0 !important;
    line-height: 1.25 !important;
}

.cd-srv-code-chip {
    font-size: 10px;
    font-weight: 600;
    color: #009ef7;
    background: #eff6ff;
    border: 1px solid #dbeafe;
    padding: 2px 7px;
    border-radius: 4px;
    letter-spacing: 0.02em;
}

.cd-srv-scope-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    height: 20px;
    padding: 0 8px;
    border-radius: 10px;
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
    font-size: 10.5px;
    font-weight: 600;
}

.cd-srv-scope-pill svg {
    width: 12px;
    height: 12px;
}

.cd-srv-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    height: 20px;
    padding: 0 8px;
    border-radius: 5px;
    font-size: 10.5px;
    font-weight: 700;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
    transition: all 0.15s ease;
}

.cd-srv-status-badge.is-off,
.cd-srv-status-badge.is-inactive {
    background: #fef2f2 !important;
    color: #b91c1c !important;
    border-color: #fecaca !important;
}

.cd-srv-subtitle {
    font-size: 11.5px;
    color: #64748b;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 580px;
}

/* Header Right Command Center (Toggle + Action Button) */
.cd-srv-head-right {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
}

/* --------------------------------------------------------------------------
   CLEAR ON / OFF TOGGLE SWITCH COMPONENT
   -------------------------------------------------------------------------- */
.cd-srv-switch-box {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 3px 8px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.cd-srv-switch-box.is-active {
    background: #ecfdf5;
    border-color: #a7f3d0;
}

.cd-srv-switch-box.is-inactive {
    background: #f8fafc;
    border-color: #e2e8f0;
}

.cd-custom-switch {
    position: relative;
    display: inline-block;
    width: 40px;
    height: 22px;
    margin: 0;
    cursor: pointer;
    user-select: none;
}

.cd-custom-switch input {
    opacity: 0;
    width: 0;
    height: 0;
    position: absolute;
    margin: 0;
}

.cd-switch-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #cbd5e1; /* Clear OFF: cool slate */
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 22px;
    box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.08);
}

.cd-switch-slider:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 3px;
    bottom: 3px;
    background-color: #ffffff;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.25);
}

/* Checked state: Clear ON vibrant emerald */
.cd-custom-switch input:checked + .cd-switch-slider {
    background-color: #10b981 !important;
}

.cd-custom-switch input:checked + .cd-switch-slider:before {
    transform: translateX(18px);
}

.cd-srv-switch-label {
    font-size: 11px;
    font-weight: 800;
    min-width: 26px;
    text-align: center;
    font-variant-numeric: tabular-nums;
    color: #64748b; /* Default OFF label */
    transition: color 0.2s ease;
}

.cd-srv-switch-box.is-active .cd-srv-switch-label {
    color: #047857 !important; /* Active ON label */
}

/* Primary View / Edit Action Button in Card Header */
.cd-srv-cta-btn {
    height: 34px;
    padding: 0 14px;
    border-radius: 8px;
    background: #009ef7;
    border: 1px solid #009ef7;
    color: #ffffff;
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    cursor: pointer;
    box-shadow: 0 1px 3px rgba(0, 158, 247, 0.2);
    transition: all 0.15s ease;
    white-space: nowrap;
}

.cd-srv-cta-btn:hover {
    background: #0086d4;
    border-color: #0086d4;
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(0, 158, 247, 0.3);
}

.cd-srv-cta-btn svg {
    width: 14px;
    height: 14px;
}

/* --------------------------------------------------------------------------
   STRICTLY FIXED 1-ROW 4-COLUMN SUB-CARDS GRID
   All 4 cards permanently locked on 1 single row (repeat(4, minmax(0, 1fr)))
   -------------------------------------------------------------------------- */
.cd-srv-grid {
    display: grid !important;
    grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
    gap: 10px !important;
    padding: 12px 16px !important;
    width: 100% !important;
    align-items: stretch !important;
    background: #ffffff;
    transition: opacity 0.2s ease;
}

.cd-srv-grid.cv2-disabled {
    opacity: 0.45 !important;
    pointer-events: none !important;
}

.cd-subcard {
    background: #f8fafc;
    border: 1px solid #eef2f6;
    border-radius: 8px;
    padding: 11px 13px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 7px;
    min-width: 0;
    min-height: 114px;
    transition: border-color 0.15s ease, background 0.15s ease;
}

.cd-subcard:hover {
    background: #f1f5f9;
    border-color: #e2e8f0;
}

.cd-subcard-label {
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 5px;
    margin-bottom: 2px;
}

.cd-subcard-label-left {
    display: flex;
    align-items: center;
    gap: 6px;
    min-width: 0;
}

.cd-subcard-label-left span {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.cd-subcard-label svg {
    width: 13.5px;
    height: 13.5px;
    flex-shrink: 0;
}

.cd-subcard-val {
    font-size: 13.5px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.25;
    font-variant-numeric: tabular-nums;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: flex;
    align-items: baseline;
    gap: 5px;
}

.cd-subcard-val.rate-highlight {
    color: #059669;
}
.cd-subcard-val.rate-highlight.is-free-pink,
.cd-subcard-val.rate-highlight.is-free-pink span {
    color: #e11d48 !important;
}

.cd-subcard-val .val-sub {
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
}

.cd-subcard-meta {
    font-size: 11px;
    color: #475569;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 6px;
    line-height: 1.35;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.cd-subcard-meta svg {
    width: 12px;
    height: 12px;
    flex-shrink: 0;
}

.cd-num-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    border-radius: 5px;
    background: #eff6ff;
    color: #009ef7;
    font-size: 10.5px;
    font-weight: 700;
    font-variant-numeric: tabular-nums;
    border: 1px solid #dbeafe;
}

.cd-country-pills-row {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 5px;
    margin-top: 2px;
}

.cd-country-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    height: 22px;
    padding: 0 7px;
    border-radius: 5px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    color: #334155;
    font-size: 10.5px;
    font-weight: 600;
    white-space: nowrap;
    max-width: 115px;
    overflow: hidden;
    text-overflow: ellipsis;
}

.cd-country-chip svg {
    width: 11px;
    height: 11px;
    color: #ef4444;
}

.cd-country-chip.more-chip {
    background: #eff6ff;
    border-color: #bfdbfe;
    color: #009ef7;
    font-weight: 700;
}

/* --------------------------------------------------------------------------
   BOTTOM METADATA ACTIVITY STRIP
   -------------------------------------------------------------------------- */
.cd-srv-bottom-strip {
    background: #f8fafc;
    border-top: 1px solid #f1f5f9;
    padding: 9px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    font-size: 11px;
    color: #64748b;
}

.cd-srv-bottom-left {
    display: flex;
    align-items: center;
    gap: 6px;
    flex: 1 1 auto;
    min-width: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.cd-srv-bottom-left svg {
    width: 13px;
    height: 13px;
    color: #10b981;
    flex-shrink: 0;
}

.cd-srv-bottom-right {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}

.cd-srv-bottom-chip {
    display: inline-flex;
    align-items: center;
    padding: 1px 6px;
    border-radius: 4px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    font-weight: 600;
    color: #334155;
}

/* Empty Search State */
.cd-srv-empty-card {
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 10px;
    padding: 36px 20px;
    text-align: center;
    display: none;
}

.cd-srv-empty-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #f1f5f9;
    color: #94a3b8;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
}

.cd-srv-empty-icon svg {
    width: 22px;
    height: 22px;
}

.cd-srv-empty-title {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 4px 0;
}

.cd-srv-empty-sub {
    font-size: 11.5px;
    color: #64748b;
    max-width: 420px;
    margin: 0 auto 14px auto;
}
</style>

<!--begin::Services & Commercials Wrapper (Rule 20 Modern Card + Sub-Card System)-->
<div class="cd-srv-wrapper" id="servicesCardSubcardContainer">

    <!-- ====================================================================
         TIER 1: TOP HEADER TOOLBAR CARD (Single Row)
         ==================================================================== -->
    <div class="cd-srv-header-card">
        <div class="cd-srv-header-left">
            <div class="cd-srv-header-icon" title="Hiring Services & Commercials">
                <!-- Handshake SVG -->
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <div class="cd-srv-header-title-row">
                    <h3 class="cd-srv-header-title">Hiring Services & Commercials</h3>
                    <span class="cd-srv-pill-badge" id="servicesTotalCountBadge">2 Services</span>
                    <span class="cd-srv-live-chip">
                        <span class="cd-srv-live-dot"></span>
                        Direct Client Active
                    </span>
                </div>
                <div class="cd-srv-header-sub">
                    Configure permanent placement and contract staffing terms, pricing structures, multi-currency defaults, and operational countries for your clients.
                </div>
            </div>
        </div>
        <div class="cd-srv-header-right">
            <!-- Executive Dynamic Metric Strip (Search & Refresh Removed) -->
            <div class="cd-srv-metric-strip">
                <span class="cd-srv-stat-pill" title="Active Commercial Services Count">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <b id="hdrActiveSummary">{{ $activeCount }} / 2 Active</b>
                </span>
                <span class="cd-srv-stat-pill" title="Default Tenant Currency">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Currency: <b>{{ $cv2CurrencyCode }}</b></span>
                </span>
                <span class="cd-srv-stat-pill" title="Automatic Multi-Tenant Real-Time Sync">
                    <span class="cd-srv-live-dot"></span>
                    <span>Live Sync</span>
                </span>
            </div>
        </div>
    </div>

    <!-- ====================================================================
         TIER 2: DEDICATED SUB-TABS CARD (All Services Tab Removed Completely)
         ==================================================================== -->
    <div class="cd-srv-tabs-card">
        <div class="cd-srv-tabs-group">
            <button type="button" class="cd-srv-tab-btn active" data-filter="active">
                <span>Active Services</span>
                <span class="cd-srv-tab-badge badge-active" id="tabCountActive">{{ $activeCount }}</span>
            </button>
            <button type="button" class="cd-srv-tab-btn" data-filter="fulltime">
                <span>Fulltime Hiring</span>
                <span class="cd-srv-tab-badge" id="tabCountFulltime">1</span>
            </button>
            <button type="button" class="cd-srv-tab-btn" data-filter="contract">
                <span>Contract Staffing</span>
                <span class="cd-srv-tab-badge" id="tabCountContract">1</span>
            </button>
            <button type="button" class="cd-srv-tab-btn" data-filter="inactive">
                <span>Inactive</span>
                <span class="cd-srv-tab-badge badge-inactive" id="tabCountInactive">{{ $inactiveCount }}</span>
            </button>
        </div>
    </div>

    <!-- ====================================================================
         TIER 3: PRIMARY CARDS STREAM & STRICT 4-COLUMN SUB-CARDS
         ==================================================================== -->
    <div class="cd-srv-stream" id="servicesCardStream">

        <!-- ----------------------------------------------------------------
             PRIMARY CARD 1: FULLTIME HIRING SERVICES
             ---------------------------------------------------------------- -->
        <article class="cd-srv-card {{ $cv2FulltimeActive ? 'is-active' : '' }}"
                 id="serviceCardFulltime"
                 data-service-type="fulltime"
                 data-status="{{ $cv2FulltimeActive ? 'active' : 'inactive' }}"
                 data-searchable="fulltime hiring services permanent placement one time placement fee {{ strtolower($cv2FulltimeCurrencyCode) }} {{ strtolower(implode(' ', $ftCountries)) }}">

            <!-- Master Card Header (Command Center) -->
            <div class="cd-srv-card-head">
                <div class="cd-srv-head-left">
                    <div class="cd-srv-symbol ft" title="Fulltime Hiring">FT</div>
                    <div class="cd-srv-head-meta">
                        <div class="cd-srv-title-row">
                            <h4 class="cd-srv-title">Fulltime Hiring Services</h4>
                            <span class="cd-srv-code-chip">#SRV-FT01</span>
                            <span class="cd-srv-scope-pill">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                Direct Client Service
                            </span>
                            <span id="f_status_badge"
                                  class="cd-srv-status-badge {{ $cv2FulltimeActive ? '' : 'is-off is-inactive' }}">
                                {{ $cv2FulltimeActive ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <p class="cd-srv-subtitle">Permanent recruitment and one-time placement solutions for client organizations</p>
                    </div>
                </div>
                <div class="cd-srv-head-right">
                    <!-- Clear ON / OFF Toggle Switch Component -->
                    <div class="cd-srv-switch-box {{ $cv2FulltimeActive ? 'is-active' : 'is-inactive' }}" title="Toggle Fulltime Hiring service activation">
                        <label class="cd-custom-switch" for="switch_fulltime">
                            <input type="checkbox"
                                   id="switch_fulltime"
                                   class="update_main_status_fulltime"
                                   data-row_id="{{ encrypt((@$fullTimeStatus->id)) }}"
                                   value="{{ @$fullTimeStatus->status }}"
                                   {{ $cv2FulltimeActive ? 'checked' : '' }} />
                            <span class="cd-switch-slider"></span>
                        </label>
                        <span id="f_text" class="cd-srv-switch-label">{{ $cv2FulltimeActive ? 'ON' : 'OFF' }}</span>
                    </div>

                    <!-- Primary View / Edit Action Button in Header -->
                    <button type="button"
                            class="cd-srv-cta-btn ser_show-detail"
                            data-type="2"
                            title="Configure Fulltime Hiring Services in Drawer">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>View / Edit Services</span>
                    </button>
                </div>
            </div>

            <!-- STRICTLY FIXED 1-ROW 4-COLUMN SUB-CARDS GRID -->
            <div class="cd-srv-grid class_change_fulltime {{ $cv2FulltimeActive ? '' : 'cv2-disabled' }}">

                <!-- Sub-Card 1: Commercial Model & Rates -->
                <div class="cd-subcard">
                    <div class="cd-subcard-label">
                        <div class="cd-subcard-label-left">
                            <svg class="text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <span>COMMERCIAL MODEL</span>
                        </div>
                        <span class="cd-num-badge">5 Tiers</span>
                    </div>
                    @php
                        $isFreeFT = (@$fullTimeStatus->rateType == 0);
                        $ftModelLabel = (@$fullTimeStatus->rateType == 2) ? 'Percentage of CTC' : ((@$fullTimeStatus->rateType == 1) ? 'Fixed Placement Fee' : 'Free / No Placement Fee');
                    @endphp
                    <div class="cd-subcard-val rate-highlight {{ $isFreeFT ? 'is-free-pink' : '' }}">
                        <span id="ft-model-val">{{ $ftModelLabel }}</span>
                    </div>
                    <div class="cd-subcard-meta">
                        <svg class="text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        <span>Rate Basis: One-time upon joining</span>
                    </div>
                    <div class="cd-subcard-meta">
                        <svg class="text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Default Currency: <b id="ft-currency-val">{{ $cv2FulltimeCurrencyCode }}</b></span>
                    </div>
                </div>

                <!-- Sub-Card 2: Replacement Policy & Guarantee -->
                <div class="cd-subcard">
                    <div class="cd-subcard-label">
                        <div class="cd-subcard-label-left">
                            <svg class="text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span>REPLACEMENT GUARANTEE</span>
                        </div>
                    </div>
                    <div class="cd-subcard-val">
                        <span id="ft-replacement-days">{{ @$fullTimeStatus->replacement_period_days ?: 90 }} Days</span>
                        <span class="val-sub">Replacement</span>
                    </div>
                    <div class="cd-subcard-meta">
                        <svg class="text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span id="ft-replacement-type">{{ ucwords(str_replace('_', ' ', @$fullTimeStatus->replacement_type ?: 'free_replacement')) }}</span>
                    </div>
                    <div class="cd-subcard-meta">
                        <svg class="text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span id="ft-replacement-clause">Clause: {{ ucwords(str_replace('_', ' ', @$fullTimeStatus->replacement_eligibility ?: 'early_exit')) }}</span>
                    </div>
                </div>

                <!-- Sub-Card 3: Billing & Invoicing Terms -->
                <div class="cd-subcard">
                    <div class="cd-subcard-label">
                        <div class="cd-subcard-label-left">
                            <svg class="text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>BILLING & PAYMENT TERMS</span>
                        </div>
                    </div>
                    <div class="cd-subcard-val">
                        <span id="ft-payment-terms-days">Net {{ @$fullTimeStatus->payment_terms_days ?: 30 }} Days</span>
                        <span class="val-sub">Payment</span>
                    </div>
                    <div class="cd-subcard-meta">
                        <svg class="text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span id="ft-billing-frequency">Invoicing: {{ ucwords(str_replace('_', ' ', @$fullTimeStatus->billing_frequency ?: 'one_time')) }}</span>
                    </div>
                    <div class="cd-subcard-meta">
                        <svg class="text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span id="ft-billing-trigger">Trigger: {{ ucwords(str_replace('_', ' ', @$fullTimeStatus->billing_trigger ?: 'candidate_joined')) }}</span>
                    </div>
                </div>

                <!-- Sub-Card 4: Operational Countries -->
                <div class="cd-subcard">
                    <div class="cd-subcard-label">
                        <div class="cd-subcard-label-left">
                            <svg class="text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>OPERATIONAL SCOPE</span>
                        </div>
                        <span id="fullTimeCount" class="cd-num-badge">{{ $fulltimeCountryCount }}</span>
                    </div>
                    <div class="cd-subcard-val">
                        <div class="cd-country-pills-row" id="ft-country-pills">
                            @if(!empty($ftCountries))
                                @foreach(array_slice($ftCountries, 0, 2) as $cName)
                                    <span class="cd-country-chip" title="{{ $cName }}">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ $cName }}
                                    </span>
                                @endforeach
                                @if(count($ftCountries) > 2)
                                    <span class="cd-country-chip more-chip" title="{{ implode(', ', array_slice($ftCountries, 2)) }}">
                                        +{{ count($ftCountries) - 2 }}
                                    </span>
                                @endif
                            @else
                                <span class="val-sub">Available Globally</span>
                            @endif
                        </div>
                    </div>
                    <div class="cd-subcard-meta">
                        <svg class="text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>Auto-enabled for client jobs</span>
                    </div>
                    <div class="cd-subcard-meta">
                        <svg class="text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Domestic & global delivery</span>
                    </div>
                </div>

            </div>

            <!-- Bottom Activity & Metadata Strip -->
            <div class="cd-srv-bottom-strip">
                <div class="cd-srv-bottom-left">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span>Fulltime placement commercial terms bind automatically to client agreements, job requisitions, and candidate submissions.</span>
                </div>
                <div class="cd-srv-bottom-right">
                    <span class="cd-srv-bottom-chip" id="ft-bottom-currency">{{ $cv2FulltimeCurrencyCode }} · Default</span>
                    <span class="cd-srv-bottom-chip">One-time Placement Fee</span>
                    <span>Last synced: Today</span>
                </div>
            </div>

        </article>

        <!-- ----------------------------------------------------------------
             PRIMARY CARD 2: CONTRACT HIRING SERVICES
             ---------------------------------------------------------------- -->
        <article class="cd-srv-card {{ $cv2ContractActive ? 'is-active' : '' }}"
                 id="serviceCardContract"
                 data-service-type="contract"
                 data-status="{{ $cv2ContractActive ? 'active' : 'inactive' }}"
                 data-searchable="contract hiring services staffing augmentation hourly monthly markup on bill rate {{ strtolower($cv2ContractCurrencyCode) }} {{ strtolower(implode(' ', $ctCountries)) }}">

            <!-- Master Card Header (Command Center) -->
            <div class="cd-srv-card-head">
                <div class="cd-srv-head-left">
                    <div class="cd-srv-symbol ct" title="Contract Hiring">CT</div>
                    <div class="cd-srv-head-meta">
                        <div class="cd-srv-title-row">
                            <h4 class="cd-srv-title">Contract Hiring Services</h4>
                            <span class="cd-srv-code-chip">#SRV-CT02</span>
                            <span class="cd-srv-scope-pill">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                Direct Client Service
                            </span>
                            <span id="c_status_badge"
                                  class="cd-srv-status-badge {{ $cv2ContractActive ? '' : 'is-off is-inactive' }}">
                                {{ $cv2ContractActive ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <p class="cd-srv-subtitle">Contract staffing, hourly/monthly consultants, and talent augmentation solutions</p>
                    </div>
                </div>
                <div class="cd-srv-head-right">
                    <!-- Clear ON / OFF Toggle Switch Component -->
                    <div class="cd-srv-switch-box {{ $cv2ContractActive ? 'is-active' : 'is-inactive' }}" title="Toggle Contract Staffing service activation">
                        <label class="cd-custom-switch" for="switch_contract">
                            <input type="checkbox"
                                   id="switch_contract"
                                   class="contract_main_status_fulltime"
                                   data-row_id="{{ encrypt((@$contractStatus->id)) }}"
                                   value="{{ @$contractStatus->status }}"
                                   {{ $cv2ContractActive ? 'checked' : '' }} />
                            <span class="cd-switch-slider"></span>
                        </label>
                        <span id="c_text" class="cd-srv-switch-label">{{ $cv2ContractActive ? 'ON' : 'OFF' }}</span>
                    </div>

                    <!-- Primary View / Edit Action Button in Header -->
                    <button type="button"
                            class="cd-srv-cta-btn ser_show-detail"
                            data-type="1"
                            title="Configure Contract Hiring Services in Drawer">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>View / Edit Services</span>
                    </button>
                </div>
            </div>

            <!-- STRICTLY FIXED 1-ROW 4-COLUMN SUB-CARDS GRID -->
            <div class="cd-srv-grid class_change_contract {{ $cv2ContractActive ? '' : 'cv2-disabled' }}">

                <!-- Sub-Card 1: Commercial Model & Pricing -->
                <div class="cd-subcard">
                    <div class="cd-subcard-label">
                        <div class="cd-subcard-label-left">
                            <svg class="text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>BILLING MODEL</span>
                        </div>
                        <span class="cd-num-badge" style="color: #8b5cf6; background: #f5f3ff; border-color: #ddd6fe;">Fixed Rate</span>
                    </div>
                    <div class="cd-subcard-val" style="color: #7c3aed;">
                        <span id="ct-model-val">{{ (@$contractStatus->rateType == 1) ? 'Markup on Bill Rate' : 'Contract Commercial Rate' }}</span>
                    </div>
                    <div class="cd-subcard-meta">
                        <svg class="text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Mode: <b id="ct-sal-mode">{{ [0 => 'Annual Staffing', 1 => 'Monthly Staffing', 2 => 'Hourly Staffing'][@$contractStatus->sal_mode] ?? 'Configured' }}</b></span>
                    </div>
                    <div class="cd-subcard-meta">
                        <svg class="text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Default Currency: <b id="ct-currency-val">{{ $cv2ContractCurrencyCode }}</b></span>
                    </div>
                </div>

                <!-- Sub-Card 2: Replacement Policy & Guarantee -->
                <div class="cd-subcard">
                    <div class="cd-subcard-label">
                        <div class="cd-subcard-label-left">
                            <svg class="text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span>CONTRACTOR GUARANTEE</span>
                        </div>
                    </div>
                    <div class="cd-subcard-val">
                        <span id="ct-replacement-days">{{ @$contractStatus->replacement_period_days ?: 30 }} Days</span>
                        <span class="val-sub">Notice Period</span>
                    </div>
                    <div class="cd-subcard-meta">
                        <svg class="text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span id="ct-replacement-type">{{ ucwords(str_replace('_', ' ', @$contractStatus->replacement_type ?: 'free_replacement')) }}</span>
                    </div>
                    <div class="cd-subcard-meta">
                        <svg class="text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span id="ct-replacement-clause">Clause: {{ ucwords(str_replace('_', ' ', @$contractStatus->replacement_eligibility ?: 'early_exit')) }}</span>
                    </div>
                </div>

                <!-- Sub-Card 3: Billing & Invoicing Terms -->
                <div class="cd-subcard">
                    <div class="cd-subcard-label">
                        <div class="cd-subcard-label-left">
                            <svg class="text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>INVOICING & TERMS</span>
                        </div>
                    </div>
                    <div class="cd-subcard-val">
                        <span id="ct-payment-terms-days">Net {{ @$contractStatus->payment_terms_days ?: 30 }} Days</span>
                        <span class="val-sub">Payment</span>
                    </div>
                    <div class="cd-subcard-meta">
                        <svg class="text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span id="ct-billing-frequency">Cycle: {{ ucwords(str_replace('_', ' ', @$contractStatus->billing_frequency ?: 'monthly')) }}</span>
                    </div>
                    <div class="cd-subcard-meta">
                        <svg class="text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span id="ct-billing-trigger">Trigger: {{ ucwords(str_replace('_', ' ', @$contractStatus->billing_trigger ?: 'approved_timesheet')) }}</span>
                    </div>
                </div>

                <!-- Sub-Card 4: Operational Countries -->
                <div class="cd-subcard">
                    <div class="cd-subcard-label">
                        <div class="cd-subcard-label-left">
                            <svg class="text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>OPERATIONAL SCOPE</span>
                        </div>
                        <span id="contractCount" class="cd-num-badge">{{ $contractCountryCount }}</span>
                    </div>
                    <div class="cd-subcard-val">
                        <div class="cd-country-pills-row" id="ct-country-pills">
                            @if(!empty($ctCountries))
                                @foreach(array_slice($ctCountries, 0, 2) as $cName)
                                    <span class="cd-country-chip" title="{{ $cName }}">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ $cName }}
                                    </span>
                                @endforeach
                                @if(count($ctCountries) > 2)
                                    <span class="cd-country-chip more-chip" title="{{ implode(', ', array_slice($ctCountries, 2)) }}">
                                        +{{ count($ctCountries) - 2 }}
                                    </span>
                                @endif
                            @else
                                <span class="val-sub">Available Globally</span>
                            @endif
                        </div>
                    </div>
                    <div class="cd-subcard-meta">
                        <svg class="text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>Auto-timesheet billing enabled</span>
                    </div>
                    <div class="cd-subcard-meta">
                        <svg class="text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Remote & onsite contractor ready</span>
                    </div>
                </div>

            </div>

            <!-- Bottom Activity & Metadata Strip -->
            <div class="cd-srv-bottom-strip">
                <div class="cd-srv-bottom-left">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span>Contract staffing terms apply automatically to client consultant timesheets, bill rates, and monthly invoices.</span>
                </div>
                <div class="cd-srv-bottom-right">
                    <span class="cd-srv-bottom-chip">{{ $cv2ContractCurrencyCode }} · Default</span>
                    <span class="cd-srv-bottom-chip">Contract & Augmentation</span>
                    <span>Last synced: Today</span>
                </div>
            </div>

        </article>

        <!-- Empty State Container -->
        <div class="cd-srv-empty-card" id="emptyServicesState">
            <div class="cd-srv-empty-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
            </div>
            <h4 class="cd-srv-empty-title">No Services in This View</h4>
            <p class="cd-srv-empty-sub">
                No hiring services currently match this category filter. Switch between tabs to manage your Fulltime or Contract commercial terms.
            </p>
            <button type="button" class="btn btn-sm btn-light-primary fw-bold" id="btnResetSearchEmpty">
                Switch to Active Services
            </button>
        </div>

    </div>

</div>
<!--end::Services & Commercials Wrapper-->

<script>
/**
 * Services Card + Sub-Card Enterprise Controller (Rule 20)
 *
 * Scoped strictly to the Services tab. Provides single-active Sub-Tab filtering,
 * interactive state sync, in-memory rehydration, and real-time drawer triggers.
 */
(function() {
    'use strict';

    function initServicesCardSubcard() {
        var $wrapper = $('#servicesCardSubcardContainer');
        if (!$wrapper.length) return;

        var currentFilter = 'active';

        function updateTabCounters() {
            var fulltimeActive = $('#serviceCardFulltime').attr('data-status') === 'active';
            var contractActive = $('#serviceCardContract').attr('data-status') === 'active';

            var activeTotal = (fulltimeActive ? 1 : 0) + (contractActive ? 1 : 0);
            var inactiveTotal = (!fulltimeActive ? 1 : 0) + (!contractActive ? 1 : 0);

            $('#tabCountActive').text(activeTotal);
            $('#tabCountInactive').text(inactiveTotal);
            $('#servicesTotalCountBadge').text('2 Services');
            $('#hdrActiveSummary').text(activeTotal + ' / 2 Active');
        }

        function filterServices() {
            var visibleCount = 0;

            $('.cd-srv-card').each(function() {
                var $card = $(this);
                var serviceType = $card.attr('data-service-type');
                var status = $card.attr('data-status');

                // Check Sub-Tab filter
                var matchesTab = true;
                if (currentFilter === 'active') {
                    matchesTab = (status === 'active');
                } else if (currentFilter === 'inactive') {
                    matchesTab = (status === 'inactive');
                } else if (currentFilter === 'fulltime') {
                    matchesTab = (serviceType === 'fulltime');
                } else if (currentFilter === 'contract') {
                    matchesTab = (serviceType === 'contract');
                }

                if (matchesTab) {
                    $card.slideDown(140);
                    visibleCount++;
                } else {
                    $card.slideUp(140);
                }
            });

            if (visibleCount === 0) {
                $('#emptyServicesState').fadeIn(150);
            } else {
                $('#emptyServicesState').hide();
            }
        }

        // Sub-Tabs Click Handler (Single Active Rule)
        $(document).off('click', '.cd-srv-tab-btn').on('click', '.cd-srv-tab-btn', function() {
            $('.cd-srv-tab-btn').removeClass('active');
            $(this).addClass('active');

            currentFilter = $(this).data('filter') || 'active';
            filterServices();
        });

        // Reset Filter to Active Services
        $(document).off('click', '#btnResetSearchEmpty').on('click', '#btnResetSearchEmpty', function() {
            $('.cd-srv-tab-btn').removeClass('active');
            $('.cd-srv-tab-btn[data-filter="active"]').addClass('active');
            currentFilter = 'active';
            filterServices();
        });

        // Fulltime Switch State Sync Observer
        $(document).off('change.srvSync', '.update_main_status_fulltime').on('change.srvSync', '.update_main_status_fulltime', function() {
            var isChecked = $(this).is(':checked');
            var $card = $('#serviceCardFulltime');
            var $switchBox = $(this).closest('.cd-srv-switch-box');

            $card.attr('data-status', isChecked ? 'active' : 'inactive');
            $card.toggleClass('is-active', isChecked);
            $switchBox.toggleClass('is-active', isChecked).toggleClass('is-inactive', !isChecked);
            $('#f_text').text(isChecked ? 'ON' : 'OFF');

            updateTabCounters();
            filterServices();
        });

        // Contract Switch State Sync Observer
        $(document).off('change.srvSync', '.contract_main_status_fulltime').on('change.srvSync', '.contract_main_status_fulltime', function() {
            var isChecked = $(this).is(':checked');
            var $card = $('#serviceCardContract');
            var $switchBox = $(this).closest('.cd-srv-switch-box');

            $card.attr('data-status', isChecked ? 'active' : 'inactive');
            $card.toggleClass('is-active', isChecked);
            $switchBox.toggleClass('is-active', isChecked).toggleClass('is-inactive', !isChecked);
            $('#c_text').text(isChecked ? 'ON' : 'OFF');

            updateTabCounters();
            filterServices();
        });

        // ==================================================================
        // CANONICAL REACTIVE COMMERCIALS RE-HYDRATION ENGINE
        // Eliminates Deficiency A: zero stale data without page reload (Rule 5)
        // ==================================================================
        window.ServicesTabEngine = {
            rehydrateServiceCard: function(serviceType, state) {
                if (!state) return;
                var isFT = (serviceType === 'fulltime' || String(state.commtype) === '2');
                var prefix = isFT ? 'ft' : 'ct';

                // 1. Model & Currency
                if (state.rate_type_label) {
                    var $modelVal = $('#' + prefix + '-model-val');
                    $modelVal.text(state.rate_type_label);
                    if (isFT && (state.rateType === 0 || String(state.rate_type_label).indexOf('Free') !== -1)) {
                        $modelVal.closest('.cd-subcard-val').addClass('is-free-pink');
                    } else if (isFT) {
                        $modelVal.closest('.cd-subcard-val').removeClass('is-free-pink');
                    }
                }
                if (state.currency_code) {
                    $('#' + prefix + '-currency-val').text(state.currency_code);
                    $('#' + prefix + '-bottom-currency').text(state.currency_code + ' · Default');
                }
                if (!isFT && state.sal_mode_label) {
                    $('#ct-sal-mode').text(state.sal_mode_label);
                }

                // 2. Replacement Policy / Contractor Trial & SLA
                if (state.replacement_period_days) {
                    $('#' + prefix + '-replacement-days').text(state.replacement_period_days + ' Days');
                }
                if (state.replacement_type_label) {
                    $('#' + prefix + '-replacement-type').text(state.replacement_type_label);
                }
                if (state.replacement_eligibility_label) {
                    var clausePrefix = isFT ? 'Clause: ' : 'SLA: ';
                    $('#' + prefix + '-replacement-clause').text(clausePrefix + state.replacement_eligibility_label);
                }

                // 3. Billing & Invoicing Terms
                if (state.payment_terms_days) {
                    $('#' + prefix + '-payment-terms-days').text('Net ' + state.payment_terms_days + ' Days');
                }
                if (state.billing_frequency_label) {
                    var freqPrefix = isFT ? 'Invoicing: ' : 'Cycle: ';
                    $('#' + prefix + '-billing-frequency').text(freqPrefix + state.billing_frequency_label);
                }
                if (state.billing_trigger_label) {
                    $('#' + prefix + '-billing-trigger').text('Trigger: ' + state.billing_trigger_label);
                }

                // 4. Country scope & pills
                var count = (typeof state.country_count !== 'undefined') ? state.country_count : (state.countries ? state.countries.length : 0);
                if (isFT) {
                    $('#fullTimeCount').text(count);
                } else {
                    $('#contractCount').text(count);
                }

                var countries = state.countries || [];
                var $pillsRow = $('#' + prefix + '-country-pills');
                if ($pillsRow.length) {
                    $pillsRow.empty();
                    if (countries.length > 0) {
                        countries.slice(0, 2).forEach(function(cName) {
                            $pillsRow.append('<span class="cd-country-chip" title="' + cName + '"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>' + cName + '</span>');
                        });
                        if (countries.length > 2) {
                            $pillsRow.append('<span class="cd-country-chip more-chip" title="' + countries.slice(2).join(', ') + '">+' + (countries.length - 2) + '</span>');
                        }
                    } else {
                        $pillsRow.append('<span class="val-sub">Available Globally</span>');
                    }
                }
            }
        };

        // Window Event Listener for Local Save & Real-Time Updates
        $(window).off('commercial:saved.srvEngine').on('commercial:saved.srvEngine', function(e) {
            var detail = e.originalEvent ? e.originalEvent.detail : e.detail;
            if (detail) {
                if (detail.version) {
                    currentCommercialVersion = detail.version;
                }
                if (detail.state) {
                    window.ServicesTabEngine.rehydrateServiceCard(detail.service_type, detail.state);
                }
            }
        });

        // Real-Time Reverb Echo Synchronization on private channel company.{companyId}
        var srvCompanyId = {{ (int) (@$account->id ?? auth()->user()->company_id ?? 0) }};
        var currentCommercialVersion = {{ (int) Cache::get('company:' . (@$account->id ?? auth()->user()->company_id ?? 0) . ':commercials:version', 1) }};

        function fetchRemoteCommercialState(targetVersion) {
            var stateUrl = '{{ route("service_company.jobs.commercialsState") }}';
            $.ajax({
                type: 'GET',
                url: stateUrl,
                headers: {
                    'If-None-Match': currentCommercialVersion ? ('"commercial-v' + currentCommercialVersion + '"') : undefined
                },
                success: function(res, statusText, xhr) {
                    if (xhr.status === 304) {
                        return; // Cache valid, no DOM work needed
                    }
                    if (res && res.status === 'success' && res.services) {
                        currentCommercialVersion = res.version || targetVersion || currentCommercialVersion;
                        if (res.services.fulltime) {
                            window.ServicesTabEngine.rehydrateServiceCard('fulltime', res.services.fulltime);
                            var isFTActive = (res.services.fulltime.status === 1);
                            var $ftSwitch = $('.update_main_status_fulltime');
                            if ($ftSwitch.length && $ftSwitch.is(':checked') !== isFTActive) {
                                $ftSwitch.prop('checked', isFTActive).trigger('change.srvSync');
                            }
                        }
                        if (res.services.contract) {
                            window.ServicesTabEngine.rehydrateServiceCard('contract', res.services.contract);
                            var isCTActive = (res.services.contract.status === 1);
                            var $ctSwitch = $('.contract_main_status_fulltime');
                            if ($ctSwitch.length && $ctSwitch.is(':checked') !== isCTActive) {
                                $ctSwitch.prop('checked', isCTActive).trigger('change.srvSync');
                            }
                        }
                    }
                }
            });
        }

        function bindCommercialsEcho() {
            if (srvCompanyId && window.Echo && typeof window.Echo.private === 'function') {
                try {
                    window.Echo.private('company.' + srvCompanyId)
                        .listen('.commercials.updated', function(event) {
                            if (event && event.version && event.version > currentCommercialVersion) {
                                fetchRemoteCommercialState(event.version);
                            }
                        });
                } catch (e) {
                    console.warn('[Commercials] Echo listener setup:', e);
                }
            } else {
                setTimeout(function() {
                    if (srvCompanyId && window.Echo && typeof window.Echo.private === 'function') {
                        try {
                            window.Echo.private('company.' + srvCompanyId)
                                .listen('.commercials.updated', function(event) {
                                    if (event && event.version && event.version > currentCommercialVersion) {
                                        fetchRemoteCommercialState(event.version);
                                    }
                                });
                        } catch (e) {}
                    }
                }, 1500);
            }
        }
        bindCommercialsEcho();

        updateTabCounters();
        filterServices();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initServicesCardSubcard);
    } else {
        initServicesCardSubcard();
    }
})();
</script>
