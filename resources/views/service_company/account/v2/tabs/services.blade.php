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
     *
     * Author: Antigravity AI (Rule 20 / card-subcard)
     * Date: October 31, 2026
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
.cd-srv-wrapper,
.cd-srv-wrapper * {
    box-sizing: border-box;
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
}

.cd-srv-wrapper {
    width: 100%;
    min-width: 0;
    color: #0f172a;
    font-size: 12px;
    padding: 0 0 16px 0;
    overflow-x: hidden;
}

/* --------------------------------------------------------------------------
   TIER 1: TOP HEADER TOOLBAR CARD
   -------------------------------------------------------------------------- */
.cd-srv-header-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
    padding: 12px 18px !important;
    margin-bottom: 12px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    flex-wrap: wrap !important;
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
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}

.cd-srv-header-title-row {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.cd-srv-header-title {
    font-size: 15px !important;
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
    font-size: 10.5px;
    font-weight: 700;
    font-variant-numeric: tabular-nums;
}

.cd-srv-live-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    height: 20px;
    padding: 0 8px;
    border-radius: 10px;
    background: #ecfdf5;
    color: #10b981;
    font-size: 10px;
    font-weight: 600;
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
}

.cd-srv-header-right {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
}

.cd-srv-search-subcard {
    position: relative;
    height: 36px;
    width: 280px;
    max-width: 100%;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    display: flex;
    align-items: center;
    padding: 0 10px;
    transition: all 0.15s ease;
}

.cd-srv-search-subcard:focus-within {
    background: #ffffff;
    border-color: #009ef7;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12);
}

.cd-srv-search-subcard i.fa-search {
    color: #94a3b8;
    font-size: 12px;
    margin-right: 8px;
    flex-shrink: 0;
}

.cd-srv-search-input {
    border: none !important;
    outline: none !important;
    background: transparent !important;
    width: 100%;
    font-size: 11.5px !important;
    color: #0f172a !important;
    padding: 0 !important;
}

.cd-srv-search-input::placeholder {
    color: #94a3b8;
    font-size: 11px;
}

.cd-srv-search-clear {
    border: none;
    background: #e2e8f0;
    color: #475569;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 9px;
    cursor: pointer;
    flex-shrink: 0;
    padding: 0;
    transition: all 0.12s ease;
}

.cd-srv-search-clear:hover {
    background: #cbd5e1;
    color: #0f172a;
}

.cd-srv-refresh-btn {
    height: 36px;
    padding: 0 14px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #334155;
    font-size: 11.5px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.15s ease;
}

.cd-srv-refresh-btn:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #009ef7;
}

/* --------------------------------------------------------------------------
   TIER 2: DEDICATED SUB-TABS CARD
   -------------------------------------------------------------------------- */
.cd-srv-tabs-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    padding: 7px 12px !important;
    margin-bottom: 14px !important;
}

.cd-srv-tabs-group {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    min-width: 0;
}

.cd-srv-tab-btn {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    padding: 6px 14px !important;
    font-size: 11.5px !important;
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
    min-width: 20px;
    height: 18px;
    padding: 0 6px;
    border-radius: 10px;
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
   TIER 3: PRIMARY CARD STREAM & DISCRETE SUB-CARDS
   -------------------------------------------------------------------------- */
.cd-srv-stream {
    display: flex;
    flex-direction: column;
    gap: 14px;
    width: 100%;
}

.cd-srv-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
    overflow: hidden;
    transition: border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
    border-left: 4px solid #cbd5e1 !important;
}

.cd-srv-card.is-active {
    border-left-color: #10b981 !important;
}

.cd-srv-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06) !important;
}

/* Master Card Header Bar */
.cd-srv-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    padding: 12px 18px;
    background: #ffffff;
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
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14.5px;
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
    gap: 2px;
    min-width: 0;
}

.cd-srv-title-row {
    display: flex;
    align-items: center;
    gap: 8px;
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
    padding: 2px 7px;
    border-radius: 5px;
    letter-spacing: 0.02em;
}

.cd-srv-scope-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    height: 20px;
    padding: 0 8px;
    border-radius: 10px;
    background: #ecfdf5;
    color: #047857;
    font-size: 10px;
    font-weight: 600;
}

.cd-srv-status-badge {
    display: inline-flex;
    align-items: center;
    height: 20px;
    padding: 0 8px;
    border-radius: 6px;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.02em;
    text-transform: uppercase;
    background: #dcfce7;
    color: #15803d;
    transition: all 0.15s ease;
}

.cd-srv-status-badge.is-off,
.cd-srv-status-badge.is-inactive {
    background: #fee2e2 !important;
    color: #b91c1c !important;
}

.cd-srv-subtitle {
    font-size: 11px;
    color: #64748b;
    margin: 0;
}

.cd-srv-head-right {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-shrink: 0;
}

.cd-srv-switch-box {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 4px 10px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
}

.cd-srv-switch-box .form-check-input {
    width: 36px !important;
    height: 20px !important;
    cursor: pointer !important;
    margin: 0 !important;
}

.cd-srv-switch-label {
    font-size: 11px;
    font-weight: 700;
    color: #0f172a;
    min-width: 26px;
    font-variant-numeric: tabular-nums;
}

/* Discrete Sub-Cards Grid */
.cd-srv-grid {
    display: grid !important;
    grid-template-columns: minmax(0, 1.8fr) minmax(0, 1.4fr) minmax(0, 1.35fr) minmax(0, 1.45fr) minmax(0, 1.5fr) !important;
    gap: 10px !important;
    padding: 14px 16px !important;
    width: 100% !important;
    align-items: stretch !important;
    background: #ffffff;
    transition: opacity 0.2s ease;
}

.cd-srv-grid.cv2-disabled {
    opacity: 0.55 !important;
}

.cd-subcard {
    background: #f8fafc;
    border: 1px solid #eef2f6;
    border-radius: 8px;
    padding: 11px 13px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 8px;
    min-width: 0;
    transition: border-color 0.15s ease, background 0.15s ease;
}

.cd-subcard:hover {
    background: #f1f5f9;
    border-color: #e2e8f0;
}

.cd-subcard-label {
    font-size: 9.8px;
    font-weight: 700;
    color: #64748b;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 2px;
}

.cd-subcard-val {
    font-size: 12.5px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.3;
    font-variant-numeric: tabular-nums;
    word-break: break-word;
}

.cd-subcard-val.rate-highlight {
    color: #059669;
}

.cd-subcard-meta {
    font-size: 11px;
    color: #475569;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 5px;
    line-height: 1.35;
}

.cd-subcard-meta i {
    font-size: 10.5px;
}

.cd-num-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 18px;
    padding: 0 6px;
    border-radius: 6px;
    background: #eff6ff;
    color: #009ef7;
    font-size: 11px;
    font-weight: 700;
    font-variant-numeric: tabular-nums;
}

.cd-country-pills-row {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 5px;
    margin-top: 3px;
}

.cd-country-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    height: 22px;
    padding: 0 8px;
    border-radius: 6px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    color: #334155;
    font-size: 10px;
    font-weight: 600;
    white-space: nowrap;
    max-width: 130px;
    overflow: hidden;
    text-overflow: ellipsis;
}

.cd-country-chip.more-chip {
    background: #eff6ff;
    border-color: #bfdbfe;
    color: #009ef7;
    font-weight: 700;
}

/* Sub-Card 5: Action Button Box */
.cd-subcard-action-box {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: stretch;
    gap: 8px;
}

.cd-cta-btn {
    height: 36px;
    padding: 0 14px;
    border-radius: 8px;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    white-space: nowrap;
    transition: all 0.15s ease;
    border: none;
    cursor: pointer;
}

/* Bottom Activity Strip (Sub-Card 6) */
.cd-srv-bottom-strip {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    padding: 9px 18px;
    background: #ffffff;
    border-top: 1px solid #f1f5f9;
}

.cd-srv-bottom-left {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 11px;
    color: #64748b;
    font-weight: 500;
}

.cd-srv-bottom-left i {
    color: #10b981;
    font-size: 12px;
}

.cd-srv-bottom-right {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 10.5px;
    color: #94a3b8;
}

/* Empty State Card */
.cd-srv-empty-card {
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 10px;
    padding: 42px 24px;
    text-align: center;
    display: none;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.cd-srv-empty-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #f1f5f9;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-bottom: 4px;
}

.cd-srv-empty-title {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}

.cd-srv-empty-sub {
    font-size: 11.5px;
    color: #64748b;
    max-width: 420px;
    margin: 0;
    line-height: 1.45;
}

/* Responsive Rules for 15.6" Laptops (1366x768) and Tablets */
@media (max-width: 1366px) {
    .cd-srv-grid {
        grid-template-columns: minmax(0, 1.7fr) minmax(0, 1.35fr) minmax(0, 1.35fr) minmax(0, 1.4fr) minmax(0, 1.4fr) !important;
        gap: 8px !important;
        padding: 12px 14px !important;
    }
    .cd-subcard {
        padding: 10px 11px;
    }
    .cd-subcard-val {
        font-size: 12px;
    }
}

@media (max-width: 1199px) {
    .cd-srv-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
    .cd-subcard-action-box {
        grid-column: 1 / -1;
    }
}

@media (max-width: 767px) {
    .cd-srv-header-card {
        flex-direction: column;
        align-items: stretch !important;
    }
    .cd-srv-header-right {
        width: 100%;
        flex-direction: column;
        align-items: stretch;
    }
    .cd-srv-search-subcard {
        width: 100%;
    }
    .cd-srv-grid {
        grid-template-columns: 1fr !important;
    }
    .cd-srv-card-head {
        flex-direction: column;
        align-items: flex-start !important;
    }
    .cd-srv-head-right {
        width: 100%;
        justify-content: space-between;
    }
    .cd-srv-bottom-strip {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>

<!--begin::Services & Commercials Wrapper (God-Level Card + Sub-Card System)-->
<div class="cd-srv-wrapper" id="servicesCardSubcardContainer">

    <!-- ====================================================================
         TIER 1: TOP HEADER TOOLBAR CARD
         ==================================================================== -->
    <div class="cd-srv-header-card">
        <div class="cd-srv-header-left">
            <div class="cd-srv-header-icon">
                <i class="fas fa-handshake"></i>
            </div>
            <div>
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
            <!-- Dynamic 300ms Debounced Search Sub-Card -->
            <div class="cd-srv-search-subcard">
                <i class="fas fa-search"></i>
                <input type="text"
                       class="cd-srv-search-input"
                       id="inputServiceSearch"
                       placeholder="Search services by title, model, currency, country..."
                       autocomplete="off" />
                <button type="button"
                        class="cd-srv-search-clear"
                        id="btnServiceSearchClear"
                        style="display: none;"
                        title="Clear search">
                    ✕
                </button>
            </div>
            <!-- Quick Reset / Refresh Button -->
            <button type="button"
                    class="cd-srv-refresh-btn"
                    id="btnRefreshServices"
                    title="Reset search and filters">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>

    <!-- ====================================================================
         TIER 2: DEDICATED SUB-TABS CARD (Single Active Metronic Primary Blue)
         ==================================================================== -->
    <div class="cd-srv-tabs-card">
        <div class="cd-srv-tabs-group">
            <button type="button" class="cd-srv-tab-btn active" data-filter="all">
                <span>All Services</span>
                <span class="cd-srv-tab-badge" id="tabCountAll">2</span>
            </button>
            <button type="button" class="cd-srv-tab-btn" data-filter="active">
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
         TIER 3: PRIMARY CARDS STREAM & DISCRETE SUB-CARDS
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

            <!-- Master Card Header -->
            <div class="cd-srv-card-head">
                <div class="cd-srv-head-left">
                    <div class="cd-srv-symbol ft" title="Fulltime Hiring">FT</div>
                    <div class="cd-srv-head-meta">
                        <div class="cd-srv-title-row">
                            <h4 class="cd-srv-title">Fulltime Hiring Services</h4>
                            <span class="cd-srv-code-chip">#SRV-FT01</span>
                            <span class="cd-srv-scope-pill">
                                <i class="fas fa-shield-alt"></i> Direct Client Service
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
                    <div class="cd-srv-switch-box" title="Toggle service activation">
                        <label class="form-check form-switch form-check-custom form-check-solid mb-0">
                            <input class="form-check-input update_main_status_fulltime"
                                   data-row_id="{{ encrypt((@$fullTimeStatus->id)) }}"
                                   type="checkbox"
                                   value="{{ @$fullTimeStatus->status }}"
                                   {{ $cv2FulltimeActive ? 'checked' : '' }} />
                        </label>
                        <span id="f_text" class="cd-srv-switch-label">{{ $cv2FulltimeActive ? 'ON' : 'OFF' }}</span>
                    </div>
                </div>
            </div>

            <!-- Discrete Sub-Cards Grid -->
            <div class="cd-srv-grid class_change_fulltime {{ $cv2FulltimeActive ? '' : 'cv2-disabled' }}">

                <!-- Sub-Card 1: Commercial Model & Pricing -->
                <div class="cd-subcard">
                    <div class="cd-subcard-label">
                        <i class="fas fa-calculator text-primary"></i> COMMERCIAL MODEL & RATE
                    </div>
                    <div class="cd-subcard-val rate-highlight">
                        {{ (@$fullTimeStatus->rateType == 2) ? 'Percentage of CTC' : 'Fixed Placement Fee' }}
                    </div>
                    <div class="cd-subcard-meta">
                        <i class="fas fa-tag text-primary"></i> Rate Basis: One-time upon joining
                    </div>
                    <div class="cd-subcard-meta">
                        <i class="fas fa-dollar-sign text-primary"></i> Default Currency: <b>{{ $cv2FulltimeCurrencyCode }}</b>
                    </div>
                </div>

                <!-- Sub-Card 2: Replacement Policy & Guarantee -->
                <div class="cd-subcard">
                    <div class="cd-subcard-label">
                        <i class="fas fa-user-shield text-success"></i> REPLACEMENT GUARANTEE
                    </div>
                    <div class="cd-subcard-val">
                        {{ @$fullTimeStatus->replacement_period_days ?: 90 }} Days Replacement
                    </div>
                    <div class="cd-subcard-meta">
                        <i class="fas fa-check-circle text-success"></i> {{ ucwords(str_replace('_', ' ', @$fullTimeStatus->replacement_type ?: 'free_replacement')) }}
                    </div>
                    <div class="cd-subcard-meta">
                        <i class="fas fa-info-circle text-muted"></i> Clause: {{ ucwords(str_replace('_', ' ', @$fullTimeStatus->replacement_eligibility ?: 'early_exit')) }}
                    </div>
                </div>

                <!-- Sub-Card 3: Billing & Invoicing Terms -->
                <div class="cd-subcard">
                    <div class="cd-subcard-label">
                        <i class="fas fa-file-invoice text-info"></i> BILLING & PAYMENT TERMS
                    </div>
                    <div class="cd-subcard-val">
                        Net {{ @$fullTimeStatus->payment_terms_days ?: 30 }} Days Payment
                    </div>
                    <div class="cd-subcard-meta">
                        <i class="fas fa-calendar-alt text-info"></i> Invoicing: {{ ucwords(str_replace('_', ' ', @$fullTimeStatus->billing_frequency ?: 'one_time')) }}
                    </div>
                    <div class="cd-subcard-meta">
                        <i class="fas fa-bolt text-warning"></i> Trigger: {{ ucwords(str_replace('_', ' ', @$fullTimeStatus->billing_trigger ?: 'candidate_joined')) }}
                    </div>
                </div>

                <!-- Sub-Card 4: Operational Countries -->
                <div class="cd-subcard">
                    <div class="cd-subcard-label">
                        <i class="fas fa-globe text-primary"></i> OPERATIONAL COUNTRIES
                    </div>
                    <div class="cd-subcard-val">
                        <span id="fullTimeCount" class="cd-num-badge">{{ $fulltimeCountryCount }}</span>
                        {{ $fulltimeCountryCount === 1 ? 'Country Configured' : 'Countries Configured' }}
                    </div>
                    <div class="cd-country-pills-row">
                        @if(!empty($ftCountries))
                            @foreach(array_slice($ftCountries, 0, 2) as $cName)
                                <span class="cd-country-chip" title="{{ $cName }}">
                                    <i class="fas fa-map-marker-alt text-danger"></i> {{ $cName }}
                                </span>
                            @endforeach
                            @if(count($ftCountries) > 2)
                                <span class="cd-country-chip more-chip" title="{{ implode(', ', array_slice($ftCountries, 2)) }}">
                                    +{{ count($ftCountries) - 2 }} more
                                </span>
                            @endif
                        @else
                            <span class="text-muted fs-8">
                                <i class="fas fa-globe-americas me-1 text-primary"></i> Available globally / client-specific
                            </span>
                        @endif
                    </div>
                    <div class="cd-subcard-meta mt-1">
                        <i class="fas fa-briefcase text-success"></i> Auto-enabled for all job requisitions
                    </div>
                </div>

                <!-- Sub-Card 5: Actions & Drawer Trigger -->
                <div class="cd-subcard cd-subcard-action-box">
                    <div class="cd-subcard-label">
                        <i class="fas fa-sliders-h text-primary"></i> CONFIGURATION
                    </div>
                    <div class="text-muted fs-8 mb-1">
                        Edit placement fee tiers, experience bands, and country rules.
                    </div>
                    <button type="button"
                            class="btn btn-sm btn-primary fw-bold ser_show-detail cd-cta-btn"
                            data-type="2"
                            title="Configure Fulltime Hiring Services">
                        <i class="fas fa-cog"></i> View / Edit Services
                    </button>
                </div>

            </div>

            <!-- Sub-Card 6: Bottom Activity Strip -->
            <div class="cd-srv-bottom-strip">
                <div class="cd-srv-bottom-left">
                    <i class="fas fa-shield-alt"></i>
                    <span>Fulltime placement commercial terms bind automatically to client agreements, job requisitions, and marketplace candidate submissions.</span>
                </div>
                <div class="cd-srv-bottom-right">
                    <span>Currency: <b>{{ $cv2FulltimeCurrencyCode }}</b></span>
                    <span>•</span>
                    <span>Fee Basis: <b>One-time Placement</b></span>
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

            <!-- Master Card Header -->
            <div class="cd-srv-card-head">
                <div class="cd-srv-head-left">
                    <div class="cd-srv-symbol ct" title="Contract Hiring">CT</div>
                    <div class="cd-srv-head-meta">
                        <div class="cd-srv-title-row">
                            <h4 class="cd-srv-title">Contract Hiring Services</h4>
                            <span class="cd-srv-code-chip">#SRV-CT02</span>
                            <span class="cd-srv-scope-pill">
                                <i class="fas fa-shield-alt"></i> Direct Client Service
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
                    <div class="cd-srv-switch-box" title="Toggle service activation">
                        <label class="form-check form-switch form-check-custom form-check-solid mb-0">
                            <input class="form-check-input contract_main_status_fulltime"
                                   data-row_id="{{ encrypt((@$contractStatus->id)) }}"
                                   type="checkbox"
                                   value="{{ @$contractStatus->status }}"
                                   {{ $cv2ContractActive ? 'checked' : '' }} />
                        </label>
                        <span id="c_text" class="cd-srv-switch-label">{{ $cv2ContractActive ? 'ON' : 'OFF' }}</span>
                    </div>
                </div>
            </div>

            <!-- Discrete Sub-Cards Grid -->
            <div class="cd-srv-grid class_change_contract {{ $cv2ContractActive ? '' : 'cv2-disabled' }}">

                <!-- Sub-Card 1: Commercial Model & Pricing -->
                <div class="cd-subcard">
                    <div class="cd-subcard-label">
                        <i class="fas fa-calculator text-primary"></i> COMMERCIAL MODEL & RATE
                    </div>
                    <div class="cd-subcard-val rate-highlight">
                        {{ (@$contractStatus->rateType == 1) ? 'Markup on Bill Rate' : 'Contract Commercial Rate' }}
                    </div>
                    <div class="cd-subcard-meta">
                        <i class="fas fa-clock text-primary"></i> Staffing Mode: <b>{{ [0 => 'Annual Staffing', 1 => 'Monthly Staffing', 2 => 'Hourly Staffing'][@$contractStatus->sal_mode] ?? 'Configured' }}</b>
                    </div>
                    <div class="cd-subcard-meta">
                        <i class="fas fa-dollar-sign text-primary"></i> Default Currency: <b>{{ $cv2ContractCurrencyCode }}</b>
                    </div>
                </div>

                <!-- Sub-Card 2: Replacement Policy & Guarantee -->
                <div class="cd-subcard">
                    <div class="cd-subcard-label">
                        <i class="fas fa-user-shield text-success"></i> REPLACEMENT GUARANTEE
                    </div>
                    <div class="cd-subcard-val">
                        {{ @$contractStatus->replacement_period_days ?: 30 }} Days Replacement
                    </div>
                    <div class="cd-subcard-meta">
                        <i class="fas fa-check-circle text-success"></i> {{ ucwords(str_replace('_', ' ', @$contractStatus->replacement_type ?: 'free_replacement')) }}
                    </div>
                    <div class="cd-subcard-meta">
                        <i class="fas fa-info-circle text-muted"></i> Clause: {{ ucwords(str_replace('_', ' ', @$contractStatus->replacement_eligibility ?: 'early_exit')) }}
                    </div>
                </div>

                <!-- Sub-Card 3: Billing & Invoicing Terms -->
                <div class="cd-subcard">
                    <div class="cd-subcard-label">
                        <i class="fas fa-file-invoice text-info"></i> BILLING & PAYMENT TERMS
                    </div>
                    <div class="cd-subcard-val">
                        Net {{ @$contractStatus->payment_terms_days ?: 30 }} Days Payment
                    </div>
                    <div class="cd-subcard-meta">
                        <i class="fas fa-calendar-alt text-info"></i> Cycle: {{ ucwords(str_replace('_', ' ', @$contractStatus->billing_frequency ?: 'monthly')) }} Invoicing
                    </div>
                    <div class="cd-subcard-meta">
                        <i class="fas fa-bolt text-warning"></i> Trigger: {{ ucwords(str_replace('_', ' ', @$contractStatus->billing_trigger ?: 'approved_timesheet')) }}
                    </div>
                </div>

                <!-- Sub-Card 4: Operational Countries -->
                <div class="cd-subcard">
                    <div class="cd-subcard-label">
                        <i class="fas fa-globe text-primary"></i> OPERATIONAL COUNTRIES
                    </div>
                    <div class="cd-subcard-val">
                        <span id="contractCount" class="cd-num-badge">{{ $contractCountryCount }}</span>
                        {{ $contractCountryCount === 1 ? 'Country Configured' : 'Countries Configured' }}
                    </div>
                    <div class="cd-country-pills-row">
                        @if(!empty($ctCountries))
                            @foreach(array_slice($ctCountries, 0, 2) as $cName)
                                <span class="cd-country-chip" title="{{ $cName }}">
                                    <i class="fas fa-map-marker-alt text-danger"></i> {{ $cName }}
                                </span>
                            @endforeach
                            @if(count($ctCountries) > 2)
                                <span class="cd-country-chip more-chip" title="{{ implode(', ', array_slice($ctCountries, 2)) }}">
                                    +{{ count($ctCountries) - 2 }} more
                                </span>
                            @endif
                        @else
                            <span class="text-muted fs-8">
                                <i class="fas fa-globe-americas me-1 text-primary"></i> Available globally / client-specific
                            </span>
                        @endif
                    </div>
                    <div class="cd-subcard-meta mt-1">
                        <i class="fas fa-briefcase text-success"></i> Auto-enabled for all job requisitions
                    </div>
                </div>

                <!-- Sub-Card 5: Actions & Drawer Trigger -->
                <div class="cd-subcard cd-subcard-action-box">
                    <div class="cd-subcard-label">
                        <i class="fas fa-sliders-h text-primary"></i> CONFIGURATION
                    </div>
                    <div class="text-muted fs-8 mb-1">
                        Edit billing rates, markup structures, and country rules.
                    </div>
                    <button type="button"
                            class="btn btn-sm btn-primary fw-bold ser_show-detail cd-cta-btn"
                            data-type="1"
                            title="Configure Contract Hiring Services">
                        <i class="fas fa-cog"></i> View / Edit Services
                    </button>
                </div>

            </div>

            <!-- Sub-Card 6: Bottom Activity Strip -->
            <div class="cd-srv-bottom-strip">
                <div class="cd-srv-bottom-left">
                    <i class="fas fa-shield-alt"></i>
                    <span>Contract staffing terms apply automatically to client consultant timesheets, bill rates, and monthly invoice generation.</span>
                </div>
                <div class="cd-srv-bottom-right">
                    <span>Currency: <b>{{ $cv2ContractCurrencyCode }}</b></span>
                    <span>•</span>
                    <span>Billing Mode: <b>Contract & Augmentation</b></span>
                </div>
            </div>

        </article>

        <!-- Empty State Container -->
        <div class="cd-srv-empty-card" id="emptyServicesState">
            <div class="cd-srv-empty-icon">
                <i class="fas fa-search"></i>
            </div>
            <h4 class="cd-srv-empty-title">No Matching Services Found</h4>
            <p class="cd-srv-empty-sub">
                No hiring services matched your search filter. Try clearing your search keyword or switching between tabs.
            </p>
            <button type="button" class="btn btn-sm btn-light-primary fw-bold" id="btnResetSearchEmpty">
                <i class="fas fa-undo me-1"></i> Clear Filter & Reset
            </button>
        </div>

    </div>

</div>
<!--end::Services & Commercials Wrapper-->

<script>
/**
 * Services Card + Sub-Card Enterprise Controller (Rule 20)
 *
 * Scoped strictly to the Services tab. Provides 300ms debounced dynamic search,
 * single-active Sub-Tab filtering, interactive state sync, and drawer triggers.
 */
(function() {
    'use strict';

    function initServicesCardSubcard() {
        var $wrapper = $('#servicesCardSubcardContainer');
        if (!$wrapper.length) return;

        var currentFilter = 'all';
        var searchDebounceTimer = null;

        function updateTabCounters() {
            var fulltimeActive = $('#serviceCardFulltime').attr('data-status') === 'active';
            var contractActive = $('#serviceCardContract').attr('data-status') === 'active';

            var activeTotal = (fulltimeActive ? 1 : 0) + (contractActive ? 1 : 0);
            var inactiveTotal = (!fulltimeActive ? 1 : 0) + (!contractActive ? 1 : 0);

            $('#tabCountActive').text(activeTotal);
            $('#tabCountInactive').text(inactiveTotal);
            $('#servicesTotalCountBadge').text('2 Services');
        }

        function filterServices() {
            var searchVal = $.trim($('#inputServiceSearch').val()).toLowerCase();
            var visibleCount = 0;

            $('.cd-srv-card').each(function() {
                var $card = $(this);
                var serviceType = $card.attr('data-service-type');
                var status = $card.attr('data-status');
                var searchable = ($card.attr('data-searchable') || '').toLowerCase();

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

                // Check Dynamic Search
                var matchesSearch = true;
                if (searchVal.length > 0) {
                    matchesSearch = (searchable.indexOf(searchVal) > -1);
                }

                if (matchesTab && matchesSearch) {
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

        // Dynamic 300ms Search Handler
        $(document).off('keyup input', '#inputServiceSearch').on('keyup input', '#inputServiceSearch', function() {
            clearTimeout(searchDebounceTimer);
            var val = $(this).val();

            if (val.length > 0) {
                $('#btnServiceSearchClear').show();
            } else {
                $('#btnServiceSearchClear').hide();
            }

            searchDebounceTimer = setTimeout(function() {
                filterServices();
            }, 300);
        });

        // Search Clear Button
        $(document).off('click', '#btnServiceSearchClear').on('click', '#btnServiceSearchClear', function() {
            $('#inputServiceSearch').val('');
            $(this).hide();
            filterServices();
        });

        // Sub-Tabs Click Handler (Single Active Rule)
        $(document).off('click', '.cd-srv-tab-btn').on('click', '.cd-srv-tab-btn', function() {
            $('.cd-srv-tab-btn').removeClass('active');
            $(this).addClass('active');

            currentFilter = $(this).data('filter') || 'all';
            filterServices();
        });

        // Reset & Refresh Buttons
        $(document).off('click', '#btnRefreshServices, #btnResetSearchEmpty').on('click', '#btnRefreshServices, #btnResetSearchEmpty', function() {
            $('#inputServiceSearch').val('');
            $('#btnServiceSearchClear').hide();
            $('.cd-srv-tab-btn').removeClass('active');
            $('.cd-srv-tab-btn[data-filter="all"]').addClass('active');
            currentFilter = 'all';
            filterServices();
        });

        // Fulltime Switch State Sync Observer
        $(document).off('change.srvSync', '.update_main_status_fulltime').on('change.srvSync', '.update_main_status_fulltime', function() {
            var isChecked = $(this).is(':checked');
            var $card = $('#serviceCardFulltime');
            $card.attr('data-status', isChecked ? 'active' : 'inactive');
            $card.toggleClass('is-active', isChecked);
            updateTabCounters();
            filterServices();
        });

        // Contract Switch State Sync Observer
        $(document).off('change.srvSync', '.contract_main_status_fulltime').on('change.srvSync', '.contract_main_status_fulltime', function() {
            var isChecked = $(this).is(':checked');
            var $card = $('#serviceCardContract');
            $card.attr('data-status', isChecked ? 'active' : 'inactive');
            $card.toggleClass('is-active', isChecked);
            updateTabCounters();
            filterServices();
        });

        updateTabCounters();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initServicesCardSubcard);
    } else {
        initServicesCardSubcard();
    }
})();
</script>
