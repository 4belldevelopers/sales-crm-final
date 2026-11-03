@php
    /**
     * Services & Commercials Management Tab — Modern Card + Sub-Card Design (Rule 20)
     *
     * Intent: Presents the service company's fulltime & contract commercial terms in the
     * authoritative 3-tier Card + Sub-Card Enterprise Layout. All data is sourced from Redis-cached
     * CommercialReadModelService projection (0% MySQL reads).
     *
     * Tenancy: All data queries and persistence are strictly scoped by auth()->user()->company_id.
     * Screen: Fully responsive for 15.6" laptops (1366×768 & 1920×1080) with zero horizontal overflow.
     * All 4 sub-cards strictly fixed on a single horizontal row with zero wrapping on standard desktop screens.
     *
     * Author: Antigravity AI (Rule 20 / card-subcard)
     * Date: November 02, 2026
     */
    $cv2FulltimeActive = (bool) (@$fullTimeStatus->status);
    $cv2ContractActive = (bool) (@$contractStatus->status);

    $tenantCompanyId = (int) (@$account->id ?? auth()->user()->company_id ?? 0);
    $tenantDefaultCurrencyCode = \App\Services\CurrencyService::getTenantCurrency($tenantCompanyId);
    $tenantDefaultCurrencyKey = \App\Services\CurrencyService::getTenantCurrencyKey($tenantCompanyId);
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

    $activeCount = ($cv2FulltimeActive ? 1 : 0) + ($cv2ContractActive ? 1 : 0);
    $inactiveCount = (!$cv2FulltimeActive ? 1 : 0) + (!$cv2ContractActive ? 1 : 0);

    // Sourced via Redis-cached CQRS projection (0% MySQL reads per Rule 1)
    $commStateDTO = \App\Services\CommercialReadModelService::getTenantCommercialsState($tenantCompanyId);
    $ftDTO = $commStateDTO->fulltime;
    $ctDTO = $commStateDTO->contract;

    $ftCountries = !empty($fullTimeCountries) ? (array) $fullTimeCountries : ($ftDTO->countryNames ?? []);
    $ctCountries = !empty($contractCountries) ? (array) $contractCountries : ($ctDTO->countryNames ?? []);

    // Requirement: Contract Hiring Services countries auto-selected based on company registrations
    if (empty($ctCountries)) {
        $regIds = \App\Services\CommercialReadModelService::getCompanyRegisteredCountryIds($tenantCompanyId);
        $countryNameMap = \App\Services\CommercialReadModelService::getCountryIdToNameMap();
        $ctCountries = array_values(array_filter(array_map(fn ($cid) => $countryNameMap[$cid] ?? null, $regIds)));
    }
    if (empty($ftCountries)) {
        $regIds = \App\Services\CommercialReadModelService::getCompanyRegisteredCountryIds($tenantCompanyId);
        $countryNameMap = \App\Services\CommercialReadModelService::getCountryIdToNameMap();
        $ftCountries = array_values(array_filter(array_map(fn ($cid) => $countryNameMap[$cid] ?? null, $regIds)));
    }
    $fulltimeCountryCount = count($ftCountries);
    $contractCountryCount = count($ctCountries);

    // Fulltime Model, Header & Default Rate
    $ftRateType = $ftDTO ? $ftDTO->rateType : (int) (@$fullTimeStatus->rateType ?? 2);
    $isFreeFT = ($ftRateType === 0);
    $ftModelLabel = $ftDTO ? $ftDTO->rateTypeLabel : (($ftRateType === 2) ? 'Percentage of CTC' : (($ftRateType === 1) ? 'Fixed Placement Fee' : 'No Fee Applicable (Free Placement)'));
    $ftHeaderLabel = $isFreeFT ? 'FREE PLACEMENT' : ($ftRateType === 1 ? 'FIXED PLACEMENT' : 'PERCENTAGE OF CTC');
    $ftModeBadgeText = $isFreeFT ? '0% Fee' : '5 Tiers';
    $ftFirstRate = ($ftDTO && !empty($ftDTO->rates)) ? (float) data_get(collect($ftDTO->rates)->sortBy('exp')->first(), 'rate', 8.33) : 8.33;
    $ftCurrencySymbol = \App\Services\CurrencyService::getCurrencySymbol($cv2FulltimeCurrencyCode);
    if ($ftRateType === 2) {
        $ftDefaultRateText = number_format($ftFirstRate, ($ftFirstRate == (int) $ftFirstRate ? 0 : 2)) . '%';
    } elseif ($ftRateType === 1) {
        $ftDefaultRateText = $ftCurrencySymbol . ' ' . number_format($ftFirstRate, ($ftFirstRate == (int) $ftFirstRate ? 0 : 2));
    } else {
        $ftDefaultRateText = 'Free (0%)';
    }

    // Contract Model, Header & Default Rate
    $ctRateType = $ctDTO ? $ctDTO->rateType : (int) (@$contractStatus->rateType ?? 1);
    $isFreeCT = ($ctRateType === 0);
    $ctModelLabel = $ctDTO ? $ctDTO->rateTypeLabel : (($ctRateType === 2) ? 'Markup on Pay Rate' : (($ctRateType === 1) ? 'Contract Bill Rate' : 'Zero Margin (Pass-Through)'));
    $ctHeaderLabel = $isFreeCT ? 'ZERO MARGIN' : ($ctRateType === 2 ? 'MARKUP ON PAY' : 'CONTRACT BILL RATE');
    $ctSalMode = $ctDTO ? $ctDTO->salMode : (int) (@$contractStatus->sal_mode ?? 2);
    $ctSalModeLabel = $isFreeCT ? 'At-Cost Wage Pass-Through' : match($ctSalMode) {
        2 => 'Hourly Contract Rate',
        1 => 'Monthly Staffing Rate',
        0 => 'Annual Staffing Rate',
        default => 'Hourly Contract Rate',
    };
    $ctModeBadgeText = $isFreeCT ? '0% Margin' : match($ctSalMode) {
        2 => 'Hourly',
        1 => 'Monthly',
        0 => 'Annual',
        default => 'Hourly',
    };
    $ctCurrencySymbol = \App\Services\CurrencyService::getCurrencySymbol($cv2ContractCurrencyCode);
    $ctFirstRate = ($ctDTO && !empty($ctDTO->rates)) ? (float) data_get(collect($ctDTO->rates)->sortBy('exp')->first(), 'rate', 10.00) : 10.00;
    if ($ctRateType === 2) {
        $ctDefaultRateText = number_format($ctFirstRate, ($ctFirstRate == (int) $ctFirstRate ? 0 : 1)) . '%';
    } elseif ($ctRateType === 1) {
        $ctDefaultRateText = $ctCurrencySymbol . ' ' . number_format($ctFirstRate, ($ctFirstRate == (int) $ctFirstRate ? 0 : 2));
    } else {
        $ctDefaultRateText = 'Pass-Through (0%)';
    }
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
    flex: 1 1 100%;
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
    white-space: normal;
    overflow: hidden;
}

/* --------------------------------------------------------------------------
   THEME 1: FULLTIME HIRING SERVICES (Executive Corporate Blue)
   -------------------------------------------------------------------------- */
.cd-srv-card.theme-fulltime {
    background: #ffffff !important;
    border: 1px solid #dbeafe !important;
    border-left: 4px solid #cbd5e1 !important;
}

.cd-srv-card.theme-fulltime.is-active {
    border-left-color: #009ef7 !important;
    border-color: #bfdbfe !important;
}

.cd-srv-card.theme-fulltime:hover {
    border-color: #93c5fd !important;
    box-shadow: 0 4px 14px rgba(0, 158, 247, 0.08) !important;
}

.cd-srv-card.theme-fulltime .cd-srv-card-head {
    background: #f0f7ff !important; /* Soft Sapphire Blue Tint */
    border-bottom: 1px solid #dbeafe !important;
}

.cd-srv-card.theme-fulltime .cd-srv-grid {
    background: #f8fbff !important; /* Clean Icy-Blue Body Canvas */
}

.cd-srv-card.theme-fulltime .cd-subcard {
    background: #ffffff !important;
    border: 1px solid #dbeafe !important;
    box-shadow: 0 1px 3px rgba(0, 158, 247, 0.04) !important;
}

.cd-srv-card.theme-fulltime .cd-subcard:hover {
    background: #ffffff !important;
    border-color: #93c5fd !important;
    box-shadow: 0 3px 10px rgba(0, 158, 247, 0.10) !important;
}

.cd-srv-card.theme-fulltime .cd-srv-bottom-strip {
    background: #f0f7ff !important;
    border-top: 1px solid #dbeafe !important;
    color: #475569;
}

.cd-srv-card.theme-fulltime .cd-srv-bottom-chip {
    background: #ffffff !important;
    border: 1px solid #dbeafe !important;
    color: #1e40af !important;
}

.cd-srv-card.theme-fulltime .cd-srv-scope-pill {
    background: #eff6ff !important;
    border: 1px solid #bfdbfe !important;
    color: #1e40af !important;
}

.cd-srv-card.theme-fulltime .cd-srv-scope-pill svg {
    color: #009ef7 !important;
}

.cd-srv-card.theme-fulltime .cd-srv-cta-btn {
    background: #009ef7 !important;
    border-color: #009ef7 !important;
    color: #ffffff !important;
    box-shadow: 0 1px 3px rgba(0, 158, 247, 0.25) !important;
}

.cd-srv-card.theme-fulltime .cd-srv-cta-btn:hover {
    background: #0086d4 !important;
    border-color: #0086d4 !important;
    box-shadow: 0 2px 6px rgba(0, 158, 247, 0.35) !important;
}

/* --------------------------------------------------------------------------
   THEME 2: CONTRACT HIRING SERVICES (Enterprise Modern Violet / Purple)
   -------------------------------------------------------------------------- */
.cd-srv-card.theme-contract {
    background: #ffffff !important;
    border: 1px solid #ede9fe !important;
    border-left: 4px solid #cbd5e1 !important;
}

.cd-srv-card.theme-contract.is-active {
    border-left-color: #7c3aed !important;
    border-color: #ddd6fe !important;
}

.cd-srv-card.theme-contract:hover {
    border-color: #c4b5fd !important;
    box-shadow: 0 4px 14px rgba(124, 58, 237, 0.08) !important;
}

.cd-srv-card.theme-contract .cd-srv-card-head {
    background: #f5f0ff !important; /* Soft Lavender Violet Tint */
    border-bottom: 1px solid #ede9fe !important;
}

.cd-srv-card.theme-contract .cd-srv-grid {
    background: #faf7ff !important; /* Clean Soft-Lilac Body Canvas */
}

.cd-srv-card.theme-contract .cd-subcard {
    background: #ffffff !important;
    border: 1px solid #ede9fe !important;
    box-shadow: 0 1px 3px rgba(124, 58, 237, 0.04) !important;
}

.cd-srv-card.theme-contract .cd-subcard:hover {
    background: #ffffff !important;
    border-color: #c4b5fd !important;
    box-shadow: 0 3px 10px rgba(124, 58, 237, 0.10) !important;
}

.cd-srv-card.theme-contract .cd-srv-bottom-strip {
    background: #f5f0ff !important;
    border-top: 1px solid #ede9fe !important;
    color: #475569;
}

.cd-srv-card.theme-contract .cd-srv-bottom-chip {
    background: #ffffff !important;
    border: 1px solid #ede9fe !important;
    color: #6d28d9 !important;
}

.cd-srv-card.theme-contract .cd-srv-scope-pill {
    background: #f5f3ff !important;
    border: 1px solid #ddd6fe !important;
    color: #6d28d9 !important;
}

.cd-srv-card.theme-contract .cd-srv-scope-pill svg {
    color: #7c3aed !important;
}

.cd-srv-card.theme-contract .cd-srv-status-badge:not(.is-off):not(.is-inactive) {
    background: #f5f3ff !important;
    border-color: #ddd6fe !important;
    color: #6d28d9 !important;
}

.cd-srv-card.theme-contract .cd-srv-switch-box.is-active {
    background: #f5f3ff !important;
    border-color: #ddd6fe !important;
}

.cd-srv-card.theme-contract .cd-srv-switch-box.is-active .cd-srv-switch-label {
    color: #6d28d9 !important;
}

.cd-srv-card.theme-contract .cd-srv-cta-btn {
    background: #7c3aed !important;
    border-color: #7c3aed !important;
    color: #ffffff !important;
    box-shadow: 0 1px 3px rgba(124, 58, 237, 0.25) !important;
}

.cd-srv-card.theme-contract .cd-srv-cta-btn:hover {
    background: #6d28d9 !important;
    border-color: #6d28d9 !important;
    box-shadow: 0 2px 6px rgba(124, 58, 237, 0.35) !important;
}

/* Bullet dots for attribute rows */
.cd-bullet-dot {
    width: 4px;
    height: 4px;
    border-radius: 50%;
    background: #009ef7;
    display: inline-block;
    flex-shrink: 0;
}

.cd-bullet-dot.purple {
    background: #8b5cf6;
}

.cd-bullet-dot.emerald {
    background: #10b981;
}

.cd-bullet-dot.sky {
    background: #0284c7;
}

.cd-bullet-dot.amber {
    background: #f59e0b;
}

/* --------------------------------------------------------------------------
   PRIMARY CARD STREAM & STRICT 4-COLUMN SINGLE-ROW SUB-CARDS
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
    container-type: inline-size;
    container-name: srvcard;
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
    gap: 8px;
    flex-wrap: wrap;
}

.cd-srv-title {
    font-size: 14px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    margin: 0 !important;
    line-height: 1.25;
}

.cd-srv-scope-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 10.5px;
    font-weight: 600;
    color: #475569;
    background: #f1f5f9;
    padding: 1px 7px;
    border-radius: 5px;
    border: 1px solid #e2e8f0;
}

.cd-srv-scope-pill svg {
    width: 12px;
    height: 12px;
    color: #10b981;
}

.cd-srv-status-badge {
    display: inline-flex;
    align-items: center;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 1px 7px;
    border-radius: 4px;
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
    font-variant-numeric: tabular-nums;
}

.cd-srv-status-badge.is-off,
.cd-srv-status-badge.is-inactive {
    background: #f1f5f9;
    color: #64748b;
    border-color: #e2e8f0;
}

.cd-srv-subtitle {
    font-size: 11px;
    color: #64748b;
    margin: 0;
    line-height: 1.35;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.cd-srv-head-right {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
}

/* ON / OFF Toggle Switch Component */
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
    background-color: #cbd5e1;
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
    color: #64748b;
    transition: color 0.2s ease;
}

.cd-srv-switch-box.is-active .cd-srv-switch-label {
    color: #047857 !important;
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
   STRICTLY FIXED 1-ROW 4-COLUMN SUB-CARDS GRID (RESPONSIVE CONTAINER SYSTEM)
   Optimized for 15.6" screens (1366x768 & 1920x1080) with dynamic 2-col fallback
   -------------------------------------------------------------------------- */
.cd-srv-grid {
    display: grid !important;
    grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
    gap: 10px !important;
    padding: 12px 14px !important;
    width: 100% !important;
    align-items: stretch !important;
    background: #ffffff;
    transition: opacity 0.2s ease;
}

/* Container query responsiveness based on card width */
@container srvcard (max-width: 980px) {
    .cd-srv-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 10px !important;
        padding: 12px 14px !important;
    }
}

@container srvcard (max-width: 520px) {
    .cd-srv-grid {
        grid-template-columns: 1fr !important;
    }
}

/* Viewport fallback queries */
@media (max-width: 1200px) {
    .cd-srv-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
}

@media (max-width: 680px) {
    .cd-srv-header-card {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 12px !important;
    }
    .cd-srv-card-head {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 12px !important;
    }
    .cd-srv-head-right {
        width: 100% !important;
        justify-content: space-between !important;
    }
    .cd-srv-grid {
        grid-template-columns: 1fr !important;
    }
}

.cd-srv-grid.cv2-disabled {
    opacity: 0.45 !important;
    pointer-events: none !important;
}

.cd-subcard {
    background: #f8fafc;
    border: 1px solid #eef2f6;
    border-radius: 8px;
    padding: 10px 12px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 6px;
    min-width: 0;
    min-height: 122px;
    transition: border-color 0.15s ease, background 0.15s ease;
}

.cd-subcard:hover {
    background: #f1f5f9;
    border-color: #e2e8f0;
}

.cd-subcard-label {
    font-size: 10.5px;
    font-weight: 600;
    color: #64748b;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    margin-bottom: 2px;
}

.cd-subcard-label-left {
    display: flex;
    align-items: center;
    gap: 5px;
    min-width: 0;
    flex: 1 1 auto;
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
    font-size: 15px !important;
    font-weight: 700 !important;
    color: #0f172a;
    line-height: 1.25;
    font-variant-numeric: tabular-nums;
    display: flex;
    align-items: baseline;
    flex-wrap: wrap;
    gap: 2px 6px;
    min-height: 38px;
    min-width: 0;
}

.cd-subcard-val.rate-highlight {
    color: #059669;
}

.cd-subcard-val.rate-highlight-purple {
    color: #7c3aed;
}

.cd-subcard-val.rate-highlight.is-free-pink,
.cd-subcard-val.rate-highlight.is-free-pink span {
    color: #e11d48 !important;
}

.cd-subcard-val.rate-highlight-purple.is-free-amber,
.cd-subcard-val.rate-highlight-purple.is-free-amber span {
    color: #d97706 !important;
}

.cd-num-badge.is-free-pink {
    background: #fff1f2 !important;
    color: #e11d48 !important;
    border-color: #fecdd3 !important;
}

.cd-num-badge.is-free-amber {
    background: #fffbeb !important;
    color: #b45309 !important;
    border-color: #fde68a !important;
}

.cd-subcard-val .val-sub {
    font-size: 11px;
    font-weight: 500;
    color: #64748b;
    white-space: normal;
    line-height: 1.25;
}

.cd-subcard-meta {
    font-size: 11px;
    color: #334155;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 6px;
    line-height: 1.35;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    min-width: 0;
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
    padding: 1px 6px;
    border-radius: 4px;
    background: #eff6ff;
    color: #009ef7;
    font-size: 10px;
    font-weight: 700;
    font-variant-numeric: tabular-nums;
    border: 1px solid #dbeafe;
    flex-shrink: 0;
    white-space: nowrap;
    text-transform: uppercase;
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
</style>

<!--begin::Services & Commercials Wrapper (Rule 20 Modern Card + Sub-Card System)-->
<div class="cd-srv-wrapper" id="servicesCardSubcardContainer">

    <!-- ====================================================================
         TIER 1: TOP HEADER TOOLBAR CARD (Single Row)
         ==================================================================== -->
    <div class="cd-srv-header-card">
        <div class="cd-srv-header-left">
            <div class="cd-srv-header-icon" title="Hiring Services & Commercials">
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
    </div>

    <!-- ====================================================================
         PRIMARY CARDS STREAM (TIER 2 SUB-TABS COMPLETELY ELIMINATED)
         ==================================================================== -->
    <div class="cd-srv-stream" id="servicesCardStream">

        <!-- ----------------------------------------------------------------
             PRIMARY CARD 1: FULLTIME HIRING SERVICES
             ---------------------------------------------------------------- -->
        <article class="cd-srv-card theme-fulltime {{ $cv2FulltimeActive ? 'is-active' : '' }}"
                 id="serviceCardFulltime"
                 data-service-type="fulltime"
                 data-status="{{ $cv2FulltimeActive ? 'active' : 'inactive' }}">

            <!-- Master Card Header (Command Center) -->
            <div class="cd-srv-card-head">
                <div class="cd-srv-head-left">
                    <div class="cd-srv-symbol ft" title="Fulltime Hiring">FT</div>
                    <div class="cd-srv-head-meta">
                        <div class="cd-srv-title-row">
                            <h4 class="cd-srv-title">Fulltime Hiring Services</h4>
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
                        <p class="cd-srv-subtitle">Permanent direct-hire recruitment, contingency search, and senior executive placement</p>
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
                            <!-- Dynamic Subcard Header -->
                            <span id="ft-header-label" title="{{ $ftModelLabel }}">{{ $ftHeaderLabel }}</span>
                        </div>
                        <span class="cd-num-badge {{ $isFreeFT ? 'is-free-pink' : '' }}" id="ft-mode-badge" title="{{ $isFreeFT ? 'Zero Placement Fee' : 'Commercial Bands' }}">{{ $ftModeBadgeText }}</span>
                    </div>
                    <!-- Primary Default Rate Metric -->
                    <div class="cd-subcard-val rate-highlight {{ $isFreeFT ? 'is-free-pink' : '' }}">
                        <span id="ft-default-rate">{{ $ftDefaultRateText }}</span>
                        <span class="val-sub" id="ft-default-rate-sub">{{ $isFreeFT ? 'Zero Placement Fee' : 'Default Rate' }}</span>
                    </div>
                    <!-- Metadata Attributes -->
                    <div class="cd-subcard-meta">
                        <span class="cd-bullet-dot"></span>
                        <span id="ft-sal-mode" title="Billing Basis">{{ $isFreeFT ? 'Direct Candidate Hire' : ($ftRateType === 1 ? 'Per Hire' : 'Annual CTC') }}</span>
                    </div>
                    <div class="cd-subcard-meta">
                        <span class="cd-bullet-dot"></span>
                        <span id="ft-model-val" title="Fee Structure">{{ $isFreeFT ? 'Complimentary Placement' : $ftModelLabel }}</span>
                    </div>
                    <span id="ft-currency-val" style="display:none;">{{ $cv2FulltimeCurrencyCode }}</span>
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
                        <span class="cd-bullet-dot emerald"></span>
                        <span id="ft-replacement-type">{{ ucwords(str_replace('_', ' ', @$fullTimeStatus->replacement_type ?: 'free_replacement')) }}</span>
                    </div>
                    <div class="cd-subcard-meta">
                        <span class="cd-bullet-dot emerald"></span>
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
                            <span id="ft-billing-label">{{ $isFreeFT ? 'BILLING & STATUS' : 'BILLING & PAYMENT TERMS' }}</span>
                        </div>
                    </div>
                    <div class="cd-subcard-val">
                        <span id="ft-payment-terms-days">{{ $isFreeFT ? 'No Invoicing' : ('Net ' . (@$fullTimeStatus->payment_terms_days ?: 30) . ' Days') }}</span>
                        <span class="val-sub" id="ft-payment-terms-sub">{{ $isFreeFT ? 'Zero Liability' : 'Payment' }}</span>
                    </div>
                    <div class="cd-subcard-meta">
                        <span class="cd-bullet-dot sky"></span>
                        <span id="ft-billing-frequency" title="Billing Cadence">{{ $isFreeFT ? 'Billing: Non-Billable Hire' : ('Invoicing: ' . ucwords(str_replace('_', ' ', @$fullTimeStatus->billing_frequency ?: 'one_time'))) }}</span>
                    </div>
                    <div class="cd-subcard-meta">
                        <span class="cd-bullet-dot amber"></span>
                        <span id="ft-billing-trigger" title="Payment Trigger">{{ $isFreeFT ? 'Status: 100% Free Placement' : ('Trigger: ' . ucwords(str_replace('_', ' ', @$fullTimeStatus->billing_trigger ?: 'candidate_joined'))) }}</span>
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
                        <span class="cd-bullet-dot emerald"></span>
                        <span>Auto-enabled for client jobs</span>
                    </div>
                    <div class="cd-subcard-meta">
                        <span class="cd-bullet-dot sky"></span>
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
                    <span id="ft-bottom-note">{{ $isFreeFT ? 'Complimentary fulltime placement enabled. Candidate submissions and hires carry zero agency placement fees.' : 'Fulltime placement commercial terms bind automatically to client agreements, job requisitions, and candidate submissions.' }}</span>
                </div>
                <div class="cd-srv-bottom-right">
                    <span class="cd-srv-bottom-chip" id="ft-bottom-currency">{{ $cv2FulltimeCurrencyCode }} · Default</span>
                    <span class="cd-srv-bottom-chip" id="ft-bottom-model">{{ $isFreeFT ? 'Free Placement Service' : 'One-time Placement Fee' }}</span>
                </div>
            </div>

        </article>

        <!-- ----------------------------------------------------------------
             PRIMARY CARD 2: CONTRACT HIRING SERVICES
             ---------------------------------------------------------------- -->
        <article class="cd-srv-card theme-contract {{ $cv2ContractActive ? 'is-active' : '' }}"
                 id="serviceCardContract"
                 data-service-type="contract"
                 data-status="{{ $cv2ContractActive ? 'active' : 'inactive' }}">

            <!-- Master Card Header (Command Center) -->
            <div class="cd-srv-card-head">
                <div class="cd-srv-head-left">
                    <div class="cd-srv-symbol ct" title="Contract Hiring">CT</div>
                    <div class="cd-srv-head-meta">
                        <div class="cd-srv-title-row">
                            <h4 class="cd-srv-title">Contract Hiring Services</h4>
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
                            <!-- Dynamic Subcard Header -->
                            <span id="ct-header-label" title="{{ $ctModelLabel }}">{{ $ctHeaderLabel }}</span>
                        </div>
                        <span class="cd-num-badge {{ $isFreeCT ? 'is-free-amber' : '' }}" id="ct-mode-badge" style="{{ $isFreeCT ? '' : 'color: #7c3aed; background: #f5f3ff; border-color: #ddd6fe;' }}" title="{{ $isFreeCT ? 'Pass-Through Staffing' : $ctSalModeLabel }}">{{ $ctModeBadgeText }}</span>
                    </div>
                    <!-- Primary Default Rate Metric -->
                    <div class="cd-subcard-val rate-highlight-purple {{ $isFreeCT ? 'is-free-amber' : '' }}">
                        <span id="ct-default-rate">{{ $ctDefaultRateText }}</span>
                        <span class="val-sub" id="ct-default-rate-sub">{{ $isFreeCT ? 'At-Cost Contractor Pay' : 'Default Rate' }}</span>
                    </div>
                    <!-- Metadata Attributes -->
                    <div class="cd-subcard-meta">
                        <span class="cd-bullet-dot purple"></span>
                        <span id="ct-sal-mode" title="Billing Cadence">{{ $isFreeCT ? 'Hourly Wage Pass-Through' : $ctSalModeLabel }}</span>
                    </div>
                    <div class="cd-subcard-meta">
                        <span class="cd-bullet-dot purple"></span>
                        <span id="ct-model-val" title="Fee Structure">{{ $isFreeCT ? 'Zero Agency Staffing Margin' : $ctModelLabel }}</span>
                    </div>
                    <span id="ct-currency-val" style="display:none;">{{ $cv2ContractCurrencyCode }}</span>
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
                        <span class="cd-bullet-dot emerald"></span>
                        <span id="ct-replacement-type">{{ ucwords(str_replace('_', ' ', @$contractStatus->replacement_type ?: 'free_replacement')) }}</span>
                    </div>
                    <div class="cd-subcard-meta">
                        <span class="cd-bullet-dot emerald"></span>
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
                            <span id="ct-billing-label">{{ $isFreeCT ? 'INVOICING TERMS' : 'INVOICING & TERMS' }}</span>
                        </div>
                    </div>
                    <div class="cd-subcard-val">
                        <span id="ct-payment-terms-days">Net {{ @$contractStatus->payment_terms_days ?: 30 }} Days</span>
                        <span class="val-sub" id="ct-payment-terms-sub">{{ $isFreeCT ? 'Wage Reimbursement' : 'Payment' }}</span>
                    </div>
                    <div class="cd-subcard-meta">
                        <span class="cd-bullet-dot sky"></span>
                        <span id="ct-billing-frequency" title="Billing Cadence">{{ $isFreeCT ? 'Cycle: Monthly Wages' : ('Cycle: ' . ucwords(str_replace('_', ' ', @$contractStatus->billing_frequency ?: 'monthly'))) }}</span>
                    </div>
                    <div class="cd-subcard-meta">
                        <span class="cd-bullet-dot amber"></span>
                        <span id="ct-billing-trigger" title="Payment Trigger">{{ $isFreeCT ? 'Trigger: Approved Timesheet' : ('Trigger: ' . ucwords(str_replace('_', ' ', @$contractStatus->billing_trigger ?: 'approved_timesheet'))) }}</span>
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
                        <span class="cd-bullet-dot emerald"></span>
                        <span>Auto-timesheet billing enabled</span>
                    </div>
                    <div class="cd-subcard-meta">
                        <span class="cd-bullet-dot sky"></span>
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
                    <span id="ct-bottom-note">{{ $isFreeCT ? 'Pass-through contract staffing terms apply. Candidate contractor compensation is reimbursed at direct cost with zero agency markup.' : 'Contract staffing terms apply automatically to client consultant timesheets, bill rates, and monthly invoices.' }}</span>
                </div>
                <div class="cd-srv-bottom-right">
                    <span class="cd-srv-bottom-chip" id="ct-bottom-currency">{{ $cv2ContractCurrencyCode }} · Default</span>
                    <span class="cd-srv-bottom-chip" id="ct-bottom-model">{{ $isFreeCT ? 'Pass-Through · Zero Margin' : 'Contract & Augmentation' }}</span>
                </div>
            </div>

        </article>

    </div>

</div>
<!--end::Services & Commercials Wrapper-->

<script>
/**
 * Services Card + Sub-Card Enterprise Controller (Rule 20)
 *
 * Scoped strictly to the Services tab. Provides interactive state sync,
 * in-memory rehydration, and real-time drawer triggers.
 */
(function() {
    'use strict';

    function initServicesCardSubcard() {
        var $wrapper = $('#servicesCardSubcardContainer');
        if (!$wrapper.length) return;

        function updateExecutiveCounters() {
            // Strip metrics removed per user request
        }

        // Fulltime Switch State Sync Observer
        $(document).off('change.srvSync', '.update_main_status_fulltime').on('change.srvSync', '.update_main_status_fulltime', function() {
            var isChecked = $(this).is(':checked');
            var $card = $('#serviceCardFulltime');
            var $switchBox = $(this).closest('.cd-srv-switch-box');

            $card.attr('data-status', isChecked ? 'active' : 'inactive');
            $card.toggleClass('is-active', isChecked);
            $switchBox.toggleClass('is-active', isChecked).toggleClass('is-inactive', !isChecked);
            $('#f_text').text(isChecked ? 'ON' : 'OFF');
            $('#f_status_badge').text(isChecked ? 'Active' : 'Inactive').toggleClass('is-off is-inactive', !isChecked);
            $('.class_change_fulltime').toggleClass('cv2-disabled', !isChecked);

            updateExecutiveCounters();
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
            $('#c_status_badge').text(isChecked ? 'Active' : 'Inactive').toggleClass('is-off is-inactive', !isChecked);
            $('.class_change_contract').toggleClass('cv2-disabled', !isChecked);

            updateExecutiveCounters();
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

                // Currency helper
                var currCode = state.currency_code || (isFT ? '{{ $cv2FulltimeCurrencyCode }}' : '{{ $cv2ContractCurrencyCode }}');
                var currSymbols = { 'USD': '$', 'INR': '₹', 'EUR': '€', 'GBP': '£', 'AED': 'AED', 'SGD': 'S$', 'CAD': 'CA$', 'AUD': 'A$' };
                var currSymbol = currSymbols[currCode] || (currCode ? currCode + ' ' : '$ ');

                // 1. Model, Header Label & Default Rate
                var rateType = (typeof state.rateType !== 'undefined') ? parseInt(state.rateType, 10) : (isFT ? 2 : 1);
                var isFree = (rateType === 0);

                // Default Rate calculation from state.rates or state.commericial_details
                var ratesArr = state.rates || state.commericial_details || [];
                var firstRateVal = 0;
                if (ratesArr && ratesArr.length > 0) {
                    var sortedRates = ratesArr.slice().sort(function(a, b) { return (a.exp || 0) - (b.exp || 0); });
                    firstRateVal = parseFloat(sortedRates[0].rate || 0);
                } else {
                    firstRateVal = isFT ? (rateType === 2 ? 8.33 : 10000) : 10;
                }

                if (isFT) {
                    if (isFree) {
                        $('#ft-header-label').text('FREE PLACEMENT').attr('title', 'Free / No Commercial Placement Fee');
                        $('#ft-mode-badge').text('0% Fee').addClass('is-free-pink').attr('title', 'Zero Placement Fee');
                        $('#ft-default-rate').text('Free (0%)');
                        $('#ft-default-rate-sub').text('Zero Placement Fee');
                        $('#ft-default-rate').closest('.cd-subcard-val').addClass('is-free-pink');
                        $('#ft-sal-mode').text('Direct Candidate Hire');
                        $('#ft-model-val').text('Complimentary Placement');

                        $('#ft-billing-label').text('BILLING & STATUS');
                        $('#ft-payment-terms-days').text('No Invoicing');
                        $('#ft-payment-terms-sub').text('Zero Liability');
                        $('#ft-billing-frequency').text('Billing: Non-Billable Hire');
                        $('#ft-billing-trigger').text('Status: 100% Free Placement');

                        $('#ft-bottom-note').text('Complimentary fulltime placement enabled. Candidate submissions and hires carry zero agency placement fees.');
                        $('#ft-bottom-model').text('Free Placement Service');
                    } else if (rateType === 2) {
                        var ftRateFormatted = (firstRateVal % 1 === 0 ? firstRateVal.toFixed(0) : firstRateVal.toFixed(2)) + '%';
                        $('#ft-header-label').text('PERCENTAGE OF CTC').attr('title', 'Percentage of CTC Placement Fee');
                        $('#ft-mode-badge').text('5 Tiers').removeClass('is-free-pink').attr('title', 'Commercial Bands');
                        $('#ft-default-rate').text(ftRateFormatted);
                        $('#ft-default-rate-sub').text('Default Rate');
                        $('#ft-default-rate').closest('.cd-subcard-val').removeClass('is-free-pink');
                        $('#ft-sal-mode').text('Annual CTC');
                        $('#ft-model-val').text('Percentage of CTC');

                        $('#ft-billing-label').text('BILLING & PAYMENT TERMS');
                        $('#ft-payment-terms-days').text('Net ' + (state.payment_terms_days || 30) + ' Days');
                        $('#ft-payment-terms-sub').text('Payment');
                        $('#ft-billing-frequency').text('Invoicing: ' + (state.billing_frequency_label || 'One Time'));
                        $('#ft-billing-trigger').text('Trigger: ' + (state.billing_trigger_label || 'Candidate Joined'));

                        $('#ft-bottom-note').text('Fulltime placement commercial terms bind automatically to client agreements, job requisitions, and candidate submissions.');
                        $('#ft-bottom-model').text('One-time Placement Fee');
                    } else {
                        var ftFixedFormatted = currSymbol + ' ' + Number(firstRateVal).toLocaleString();
                        $('#ft-header-label').text('FIXED PLACEMENT').attr('title', 'Fixed Placement Fee Per Hire');
                        $('#ft-mode-badge').text('5 Tiers').removeClass('is-free-pink').attr('title', 'Commercial Bands');
                        $('#ft-default-rate').text(ftFixedFormatted);
                        $('#ft-default-rate-sub').text('Default Rate');
                        $('#ft-default-rate').closest('.cd-subcard-val').removeClass('is-free-pink');
                        $('#ft-sal-mode').text('Per Hire');
                        $('#ft-model-val').text('Fixed Placement Fee');

                        $('#ft-billing-label').text('BILLING & PAYMENT TERMS');
                        $('#ft-payment-terms-days').text('Net ' + (state.payment_terms_days || 30) + ' Days');
                        $('#ft-payment-terms-sub').text('Payment');
                        $('#ft-billing-frequency').text('Invoicing: ' + (state.billing_frequency_label || 'One Time'));
                        $('#ft-billing-trigger').text('Trigger: ' + (state.billing_trigger_label || 'Candidate Joined'));

                        $('#ft-bottom-note').text('Fulltime placement commercial terms bind automatically to client agreements, job requisitions, and candidate submissions.');
                        $('#ft-bottom-model').text('Fixed Placement Fee');
                    }
                } else {
                    var salMode = (typeof state.sal_mode !== 'undefined') ? parseInt(state.sal_mode, 10) : 2;
                    var salModeLabel = state.sal_mode_label || (salMode === 2 ? 'Hourly Contract Rate' : (salMode === 1 ? 'Monthly Staffing Rate' : 'Annual Staffing Rate'));
                    var modeBadge = (salMode === 2 ? 'Hourly' : (salMode === 1 ? 'Monthly' : 'Annual'));

                    if (isFree) {
                        $('#ct-header-label').text('ZERO MARGIN').attr('title', 'Pass-Through / Zero Margin Contractor Staffing');
                        $('#ct-mode-badge').text('0% Margin').addClass('is-free-amber').removeAttr('style').attr('title', 'Pass-Through Staffing');
                        $('#ct-default-rate').text('Pass-Through (0%)');
                        $('#ct-default-rate-sub').text('At-Cost Contractor Pay');
                        $('#ct-default-rate').closest('.cd-subcard-val').addClass('is-free-amber');
                        $('#ct-sal-mode').text('Hourly Wage Pass-Through');
                        $('#ct-model-val').text('Zero Agency Staffing Margin');

                        $('#ct-billing-label').text('INVOICING TERMS');
                        $('#ct-payment-terms-days').text('Net ' + (state.payment_terms_days || 30) + ' Days');
                        $('#ct-payment-terms-sub').text('Wage Reimbursement');
                        $('#ct-billing-frequency').text('Cycle: Monthly Wages');
                        $('#ct-billing-trigger').text('Trigger: Approved Timesheet');

                        $('#ct-bottom-note').text('Pass-through contract staffing terms apply. Candidate contractor compensation is reimbursed at direct cost with zero agency markup.');
                        $('#ct-bottom-model').text('Pass-Through · Zero Margin');
                    } else if (rateType === 2) {
                        var ctRateFormatted = (firstRateVal % 1 === 0 ? firstRateVal.toFixed(0) : firstRateVal.toFixed(1)) + '%';
                        $('#ct-header-label').text('MARKUP ON PAY').attr('title', 'Markup on Candidate Base Wage Rate');
                        $('#ct-mode-badge').text(modeBadge).removeClass('is-free-amber').attr('style', 'color: #7c3aed; background: #f5f3ff; border-color: #ddd6fe;').attr('title', salModeLabel);
                        $('#ct-default-rate').text(ctRateFormatted);
                        $('#ct-default-rate-sub').text('Default Rate');
                        $('#ct-default-rate').closest('.cd-subcard-val').removeClass('is-free-amber');
                        $('#ct-sal-mode').text(salModeLabel);
                        $('#ct-model-val').text('Markup on Pay Rate');

                        $('#ct-billing-label').text('INVOICING & TERMS');
                        $('#ct-payment-terms-days').text('Net ' + (state.payment_terms_days || 30) + ' Days');
                        $('#ct-payment-terms-sub').text('Payment');
                        $('#ct-billing-frequency').text('Cycle: ' + (state.billing_frequency_label || 'Monthly'));
                        $('#ct-billing-trigger').text('Trigger: ' + (state.billing_trigger_label || 'Approved Timesheet'));

                        $('#ct-bottom-note').text('Contract staffing terms apply automatically to client consultant timesheets, bill rates, and monthly invoices.');
                        $('#ct-bottom-model').text('Contract & Augmentation');
                    } else {
                        var ctFixedFormatted = currSymbol + ' ' + (firstRateVal % 1 === 0 ? firstRateVal.toFixed(0) : firstRateVal.toFixed(2));
                        $('#ct-header-label').text('CONTRACT BILL RATE').attr('title', 'Direct Fixed Client Bill Rate');
                        $('#ct-mode-badge').text(modeBadge).removeClass('is-free-amber').attr('style', 'color: #7c3aed; background: #f5f3ff; border-color: #ddd6fe;').attr('title', salModeLabel);
                        $('#ct-default-rate').text(ctFixedFormatted);
                        $('#ct-default-rate-sub').text('Default Rate');
                        $('#ct-default-rate').closest('.cd-subcard-val').removeClass('is-free-amber');
                        $('#ct-sal-mode').text(salModeLabel);
                        $('#ct-model-val').text('Contract Bill Rate');

                        $('#ct-billing-label').text('INVOICING & TERMS');
                        $('#ct-payment-terms-days').text('Net ' + (state.payment_terms_days || 30) + ' Days');
                        $('#ct-payment-terms-sub').text('Payment');
                        $('#ct-billing-frequency').text('Cycle: ' + (state.billing_frequency_label || 'Monthly'));
                        $('#ct-billing-trigger').text('Trigger: ' + (state.billing_trigger_label || 'Approved Timesheet'));

                        $('#ct-bottom-note').text('Contract staffing terms apply automatically to client consultant timesheets, bill rates, and monthly invoices.');
                        $('#ct-bottom-model').text('Contract Bill Rate');
                    }
                }

                // 2. Replacement Policy / Contractor Trial & SLA
                if (state.replacement_period_days) {
                    $('#' + prefix + '-replacement-days').text(state.replacement_period_days + ' Days');
                }
                if (state.replacement_type_label) {
                    $('#' + prefix + '-replacement-type').text(state.replacement_type_label);
                }
                if (state.replacement_eligibility_label) {
                    var clausePrefix = 'Clause: ';
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
                var count = (typeof state.country_count !== 'undefined') ? state.country_count : (state.country_names ? state.country_names.length : (state.countries ? state.countries.length : 0));
                if (isFT) {
                    $('#fullTimeCount').text(count);
                } else {
                    $('#contractCount').text(count);
                }

                var countries = state.country_names || state.countries || [];
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

                updateExecutiveCounters();
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

        updateExecutiveCounters();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initServicesCardSubcard);
    } else {
        initServicesCardSubcard();
    }
})();
</script>