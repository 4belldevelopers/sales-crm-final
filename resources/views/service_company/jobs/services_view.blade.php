@php
    /**
     * Hiring Services Drawer View (Fulltime & Contract) — God-Level Modern Card + Sub-Card Design (Rule 20)
     *
     * Intent: Presents the complete commercial configuration (fee model, default pricing, experience bands,
     * replacement terms, billing timeline, and operational countries) in the authoritative 3-tier
     * Card + Sub-Card Enterprise Layout inside the sliding drawer (#kt_activitiesJob).
     *
     * Tenancy: Strictly scoped by auth()->user()->company_id.
     * Screen: Fully responsive for 15.6" laptops (1366×768 & 1920×1080) with zero horizontal overflow.
     *
     * Author: Antigravity AI (Rule 20 / card-subcard / drawer-master)
     * Date: October 31, 2026
     */
    $commercial = @$commericials[0] ?? [];
    $isFulltime = (int) $sectionCheck === 2;
    $rateType = (int) ($commercial['rateType'] ?? $sectionCheck);
    $commercialDetails = collect($commercial['commericial_details'] ?? [])->keyBy('exp');
    $selectedCountryIds = array_map('intval', $commercial['commericial_countries'] ?? []);
    $fallbackDefaultRate = $isFulltime ? (@$default_rates->rates_perecentage ?? 8.33) : (@$default_rates->rates_fixed ?? 0);

    $experienceBandRows = collect($commercialExperienceBands ?? []);
    if ($experienceBandRows->isEmpty()) {
        $experienceBandRows = \Illuminate\Support\Facades\DB::table('commercial_experience_bands')
            ->select(['id', 'code', 'label', 'min_experience', 'max_experience', 'sort_order'])
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->get();
    }
    $firstExperienceIndex = (int) data_get($experienceBandRows->sortBy('sort_order')->first(), 'min_experience', 0);
    $defaultRate = data_get($commercialDetails->get($firstExperienceIndex), 'rate', $fallbackDefaultRate);
    $isFreeModel = $commercialDetails->isNotEmpty() && $commercialDetails->every(function ($detail) {
        return (float) data_get($detail, 'rate', 0) === 0.0;
    });
    $selectedCommercialModel = $isFreeModel ? 0 : $rateType;

    $experienceRanges = $experienceBandRows
        ->map(function ($band) {
            $minimum = (int) $band->min_experience;
            $maximum = (int) $band->max_experience;

            return [
                'label' => (string) $band->label,
                'indexes' => range($minimum, $maximum),
            ];
        })
        ->values();

    $selectedSalaryMode = isset($commercial['sal_mode']) ? (int) $commercial['sal_mode'] : 0;

    $tenantDefaultCurrencyKey = isset($defaultCurrencyKey)
        ? (int) $defaultCurrencyKey
        : \App\Services\CurrencyService::getTenantCurrencyKey($commercial['company_id'] ?? auth()->user()->company_id ?? null);
    $savedCurrency = (isset($commercial['salary_currency']) && $commercial['salary_currency'] !== '' && $commercial['salary_currency'] !== null)
        ? (int) $commercial['salary_currency']
        : null;

    $selectedCurrency = ($savedCurrency === null || ($savedCurrency === 4 && $tenantDefaultCurrencyKey !== 4))
        ? $tenantDefaultCurrencyKey
        : $savedCurrency;

    $currenciesToRender = !empty($currencyList) ? $currencyList : \App\Services\CurrencyService::getSettingCurrenciesList();
    if (empty($currenciesToRender)) {
        $currenciesToRender = !empty($currency) ? $currency : \App\Helper\GeneralMethod::currency();
    }

    $selectedReplacementPeriod = (int) ($commercial['replacement_period_days'] ?? ($isFulltime ? 90 : 30));
    $selectedReplacementType = (string) ($commercial['replacement_type'] ?? 'free_replacement');
    $selectedReplacementEligibility = (string) ($commercial['replacement_eligibility'] ?? 'early_exit');
    $selectedBillingFrequency = (string) ($commercial['billing_frequency'] ?? ($isFulltime ? 'one_time' : 'monthly'));
    $selectedBillingTrigger = (string) ($commercial['billing_trigger'] ?? ($isFulltime ? 'candidate_joined' : 'approved_timesheet'));
    $selectedPaymentTerms = (int) ($commercial['payment_terms_days'] ?? 30);

    $countryRegions = collect($countries ?? [])->groupBy(function ($country) {
        return trim((string) ($country->region ?? '')) ?: 'Other';
    });

    $activeCountryCount = count($selectedCountryIds);
@endphp

<style>
/* ==========================================================================
   HIRING SERVICES DRAWER — MODERN CARD + SUB-CARD ENTERPRISE SYSTEM
   Compliant with Rule 20 / card-subcard / drawer-master
   Screen target: 15.6" laptops (1366x768 & 1920x1080), Poppins, zero horizontal scroll
   ========================================================================== */
#kt_activitiesJob:has(#commercial-editor-v3) > .card > #kt_activities_header {
    display: none !important;
}
#kt_activitiesJob:has(#commercial-editor-v3) > .card > #kt_activitiesJob_body {
    padding: 0 !important;
}

.commercial-editor-v3 {
    box-sizing: border-box;
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
}

.commercial-editor-v3 *,
.commercial-editor-v3 *::before,
.commercial-editor-v3 *::after {
    box-sizing: border-box;
}

.cd-svg-icon {
    width: 14px;
    height: 14px;
    display: inline-block;
    vertical-align: -2px;
    flex-shrink: 0;
}

.commercial-editor-v3 {
    width: 100%;
    min-height: 100%;
    background: #f8fafc;
    color: #0f172a;
    font-size: 12px;
    display: flex;
    flex-direction: column;
    overflow-x: hidden;
}

/* --------------------------------------------------------------------------
   TIER 1: TOP HEADER TOOLBAR CARD
   -------------------------------------------------------------------------- */
.cd-drw-header-card {
    position: sticky;
    top: 0;
    z-index: 30;
    background: #ffffff !important;
    border-bottom: 1px solid #e2e8f0 !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
    padding: 12px 18px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    flex-wrap: wrap !important;
    gap: 12px !important;
}

.cd-drw-header-left {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
    flex: 1 1 auto;
}

.cd-drw-symbol {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 800;
    letter-spacing: 0.02em;
    flex-shrink: 0;
}

.cd-drw-symbol.ft {
    background: #eff6ff;
    color: #0085ff;
    border: 1px solid #bfdbfe;
}

.cd-drw-symbol.ct {
    background: #f5f3ff;
    color: #8b5cf6;
    border: 1px solid #ddd6fe;
}

.cd-drw-title-meta {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
}

.cd-drw-title-row {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.cd-drw-title {
    font-size: 15px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    margin: 0 !important;
    line-height: 1.25 !important;
}

.cd-drw-code-chip {
    font-size: 10px;
    font-weight: 600;
    color: #009ef7;
    background: #eff6ff;
    padding: 2px 7px;
    border-radius: 5px;
    letter-spacing: 0.02em;
}

.cd-drw-scope-pill {
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

.cd-drw-status-badge {
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
}

.cd-drw-status-badge.is-inactive {
    background: #fee2e2 !important;
    color: #b91c1c !important;
}

.cd-drw-subtitle {
    font-size: 11px;
    color: #64748b;
    margin: 0;
    line-height: 1.35;
}

.cd-drw-header-right {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}

.cd-drw-head-btn {
    height: 34px;
    padding: 0 12px;
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

.cd-drw-head-btn:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #009ef7;
}

.cd-drw-close-btn {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    cursor: pointer;
    transition: all 0.15s ease;
}

.cd-drw-close-btn:hover {
    background: #fee2e2;
    border-color: #fecaca;
    color: #ef4444;
}

/* --------------------------------------------------------------------------
   TIER 2: DEDICATED SUB-TABS CARD (In-Memory 0ms Tab Switching)
   -------------------------------------------------------------------------- */
.cd-drw-tabs-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    padding: 7px 12px !important;
    margin: 12px 16px 0 16px !important;
}

.cd-drw-tabs-group {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    min-width: 0;
}

.cd-drw-tab-btn {
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

.cd-drw-tab-btn:hover {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
    color: #0f172a !important;
}

/* Mandatory Single Active Highlight: Metronic Primary Blue (#009ef7) */
.cd-drw-tab-btn.active {
    background: #009ef7 !important;
    border-color: #009ef7 !important;
    color: #ffffff !important;
    box-shadow: 0 2px 4px rgba(0, 158, 247, 0.22) !important;
}

.cd-drw-tab-badge {
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
    background: #eff6ff;
    color: #009ef7;
    transition: all 0.15s ease;
}

.cd-drw-tab-btn.active .cd-drw-tab-badge {
    background: rgba(255, 255, 255, 0.28) !important;
    color: #ffffff !important;
}

/* --------------------------------------------------------------------------
   TIER 3: PRIMARY CARD STREAM & DISCRETE SUB-CARDS
   -------------------------------------------------------------------------- */
.cd-drw-stream {
    display: flex;
    flex-direction: column;
    gap: 14px;
    padding: 12px 16px 80px 16px;
    width: 100%;
}

.cd-drw-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
    padding: 16px 18px !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.cd-drw-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 3px 8px rgba(15, 23, 42, 0.05) !important;
}

.cd-drw-card-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 14px;
    padding-bottom: 12px;
    border-bottom: 1px solid #f1f5f9;
}

.cd-drw-card-title-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.cd-drw-step-num {
    width: 26px;
    height: 26px;
    border-radius: 7px;
    background: #eff6ff;
    color: #009ef7;
    font-size: 11.5px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.cd-drw-card-title {
    font-size: 13.5px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    margin: 0 !important;
    line-height: 1.3 !important;
}

.cd-drw-card-copy {
    font-size: 11px;
    color: #64748b;
    margin: 2px 0 0;
}

/* Sub-Card 1: Model Option Radio Cards */
.cd-model-options-grid {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 12px !important;
    margin-bottom: 12px;
}

.cd-model-card {
    position: relative;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 14px 16px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 9px;
    text-align: left;
    cursor: pointer;
    transition: all 0.15s ease;
    user-select: none;
}

.cd-model-card:hover {
    border-color: #93c5fd;
    background: #f8faff;
}

.cd-model-card.active {
    border-color: #009ef7 !important;
    background: #f1faff !important;
    box-shadow: 0 0 0 2px rgba(0, 158, 247, 0.15) !important;
}

.cd-model-icon-box {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: #eff6ff;
    color: #009ef7;
    font-size: 15px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.cd-model-card.active .cd-model-icon-box {
    background: #009ef7;
    color: #ffffff;
}

.cd-model-copy-wrap {
    display: flex;
    flex-direction: column;
    min-width: 0;
    flex: 1 1 auto;
}

.cd-model-title {
    font-size: 12.5px;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 4px;
}

.cd-model-desc {
    font-size: 10.5px;
    color: #64748b;
    line-height: 1.45;
    margin: 0;
}

.cd-model-radio-dot {
    position: absolute;
    right: 14px;
    top: 14px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 1.5px solid #cbd5e1;
    background: #ffffff;
    transition: all 0.15s ease;
}

.cd-model-card.active .cd-model-radio-dot {
    border-color: #009ef7;
    border-width: 5px;
}

.cd-info-banner {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 8px;
    color: #166534;
    font-size: 11px;
    font-weight: 500;
}

.cd-info-banner.blue {
    background: #eff6ff;
    border-color: #bfdbfe;
    color: #1e40af;
}

/* Default Base Pricing Sub-Card Grid */
.cd-pricing-subcard-grid {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 12px !important;
    padding: 14px 16px;
    background: #f8fafc;
    border: 1px solid #eef2f6;
    border-radius: 8px;
}

.cd-field-group {
    display: flex;
    flex-direction: column;
    gap: 4px;
    min-width: 0;
}

.cd-field-label {
    font-size: 10px;
    font-weight: 700;
    color: #475569;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    margin-bottom: 2px;
}

.cd-field-control {
    width: 100%;
    height: 36px;
    padding: 6px 12px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 7px;
    font-size: 11.5px;
    font-weight: 500;
    color: #0f172a;
    outline: none;
    transition: all 0.15s ease;
}

.cd-field-control:focus {
    border-color: #009ef7;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12);
}

.cd-field-control:disabled {
    background: #f1f5f9;
    color: #94a3b8;
    cursor: not-allowed;
}

.cd-rate-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
}

.cd-rate-input-wrap .cd-field-control {
    padding-right: 32px;
    font-variant-numeric: tabular-nums;
    font-weight: 600;
}

.cd-rate-suffix {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 11px;
    font-weight: 700;
    color: #64748b;
}

.cd-field-help {
    font-size: 10px;
    color: #94a3b8;
    margin-top: 3px;
    line-height: 1.35;
}

/* Free/No fee summary sub-card */
.cd-free-fee-banner {
    display: none;
    grid-column: 1 / -1;
    align-items: center;
    gap: 14px;
    padding: 14px 18px;
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 8px;
}

.commercial-editor-v3.is-free-fee .cd-free-fee-banner {
    display: flex;
}

.commercial-editor-v3.is-free-fee .cd-pricing-subcard-grid > .cd-field-group {
    display: none;
}

/* Experience Matrix Table */
.cd-range-matrix {
    width: 100%;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
    background: #ffffff;
}

.cd-range-head,
.cd-range-row {
    display: grid !important;
    grid-template-columns: minmax(140px, 1.2fr) minmax(110px, 0.8fr) minmax(120px, 0.9fr) minmax(140px, 1fr) 36px !important;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
}

.cd-range-head {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    font-size: 10px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

.cd-range-row {
    border-bottom: 1px solid #f1f5f9;
    min-height: 46px;
    transition: background 0.12s ease;
}

.cd-range-row:last-child {
    border-bottom: none;
}

.cd-range-row:hover {
    background: #f8fafc;
}

.cd-tier-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11.5px;
    font-weight: 700;
    color: #0f172a;
}

.cd-tier-badge i {
    color: #009ef7;
    font-size: 11px;
}

.cd-range-row .cd-field-control {
    height: 32px;
    font-size: 11px;
    padding: 4px 8px;
}

.cd-range-reset-btn {
    width: 32px;
    height: 32px;
    border-radius: 7px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.15s ease;
}

.cd-range-reset-btn:hover {
    background: #eff6ff;
    border-color: #bfdbfe;
    color: #009ef7;
}

/* Replacement Policy & Payment Timeline 3-column sub-card grids */
.cd-policy-grid,
.cd-payment-grid {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 12px !important;
    padding: 14px 16px;
    background: #f8fafc;
    border: 1px solid #eef2f6;
    border-radius: 8px;
}

.cd-policy-alert {
    display: flex;
    align-items: center;
    gap: 10px;
    grid-column: 1 / -1;
    margin-top: 4px;
    padding: 10px 14px;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 7px;
    font-size: 11px;
    color: #1e40af;
}

/* Country Coverage Section */
.cd-country-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 12px;
}

.cd-country-search-box {
    position: relative;
    height: 36px;
    width: 280px;
    max-width: 100%;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    display: flex;
    align-items: center;
    padding: 0 10px;
}

.cd-country-search-box:focus-within {
    border-color: #009ef7;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12);
}

.cd-country-search-box i {
    color: #94a3b8;
    font-size: 11px;
    margin-right: 8px;
}

.cd-country-search-input {
    border: none !important;
    outline: none !important;
    background: transparent !important;
    width: 100%;
    font-size: 11.5px !important;
    color: #0f172a !important;
    padding: 0 !important;
}

.cd-country-btn-group {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}

.cd-country-btn {
    height: 32px;
    padding: 0 10px;
    border-radius: 7px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #334155;
    font-size: 10.5px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
}

.cd-country-btn:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #009ef7;
}

/* Selected Countries Chip Bar */
.cd-selected-chips-bar {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
    min-height: 34px;
    padding: 8px 12px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    margin-bottom: 12px;
}

.cd-country-chip-item {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    height: 24px;
    padding: 0 8px;
    border-radius: 6px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #0f172a;
    font-size: 10.5px;
    font-weight: 600;
}

.cd-chip-code {
    font-size: 9px;
    font-weight: 700;
    color: #009ef7;
    background: #eff6ff;
    padding: 1px 4px;
    border-radius: 4px;
}

.cd-chip-remove {
    border: none;
    background: transparent;
    color: #94a3b8;
    font-size: 11px;
    cursor: pointer;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.cd-chip-remove:hover {
    color: #ef4444;
}

/* Region Filter Tabs */
.cd-region-tabs {
    display: flex;
    gap: 6px;
    overflow-x: auto;
    padding-bottom: 8px;
    margin-bottom: 10px;
    scrollbar-width: none;
}

.cd-region-tabs::-webkit-scrollbar {
    display: none;
}

.cd-region-tab {
    height: 28px;
    padding: 0 10px;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    color: #475569;
    font-size: 10.5px;
    font-weight: 600;
    white-space: nowrap;
    cursor: pointer;
    transition: all 0.15s ease;
}

.cd-region-tab:hover {
    background: #ffffff;
    border-color: #cbd5e1;
    color: #0f172a;
}

.cd-region-tab.active {
    background: #009ef7 !important;
    border-color: #009ef7 !important;
    color: #ffffff !important;
}

/* Country Selection Grid */
.cd-country-grid {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 6px 14px !important;
    max-height: 280px;
    overflow-y: auto;
    padding: 10px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 transparent;
}

.cd-country-option {
    display: flex;
    align-items: center;
    gap: 8px;
    min-height: 28px;
    padding: 4px 6px;
    border-radius: 6px;
    transition: background 0.12s ease;
}

.cd-country-option:hover {
    background: #f8fafc;
}

.cd-country-option[hidden] {
    display: none !important;
}

.cd-country-option input.countries {
    width: 15px;
    height: 15px;
    margin: 0;
    cursor: pointer;
    accent-color: #009ef7;
}

.cd-country-label {
    display: flex;
    align-items: center;
    gap: 6px;
    margin: 0;
    font-size: 11px;
    color: #334155;
    cursor: pointer;
    user-select: none;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.cd-iso-badge {
    font-size: 9px;
    font-weight: 700;
    color: #64748b;
    background: #f1f5f9;
    padding: 1px 4px;
    border-radius: 4px;
    min-width: 22px;
    text-align: center;
}

/* --------------------------------------------------------------------------
   STICKY FOOTER ACTION STRIP
   -------------------------------------------------------------------------- */
.cd-drw-footer {
    position: sticky;
    bottom: 0;
    z-index: 30;
    background: rgba(255, 255, 255, 0.98) !important;
    backdrop-filter: blur(8px) !important;
    border-top: 1px solid #e2e8f0 !important;
    box-shadow: 0 -2px 10px rgba(15, 23, 42, 0.05) !important;
    padding: 12px 20px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 12px !important;
}

.cd-drw-footer-left {
    display: flex;
    align-items: center;
    gap: 8px;
}

.cd-drw-footer-right {
    display: flex;
    align-items: center;
    gap: 10px;
}

.cd-drw-btn {
    height: 38px;
    padding: 0 16px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.15s ease;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #334155;
}

.cd-drw-btn:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #0f172a;
}

.cd-drw-btn.btn-primary {
    background: #009ef7 !important;
    border-color: #009ef7 !important;
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(0, 158, 247, 0.28) !important;
}

.cd-drw-btn.btn-primary:hover {
    background: #008be0 !important;
    border-color: #008be0 !important;
}

.cd-drw-btn.is-saving {
    opacity: 0.85;
    pointer-events: none;
}

.cd-drw-btn .indicator-progress {
    display: none;
    align-items: center;
    gap: 6px;
}

.cd-drw-btn.is-saving .indicator-label {
    display: none;
}

.cd-drw-btn.is-saving .indicator-progress {
    display: inline-flex;
}

/* Dynamic Fixed-Fee and Free Mode Toggles */
.cd-fixed-only {
    display: none;
}
.commercial-editor-v3.is-fixed-fee .cd-fixed-only {
    display: flex !important;
}
.commercial-editor-v3.is-fixed-fee .cd-percentage-only {
    display: none !important;
}
.commercial-editor-v3.is-free-fee .cd-range-matrix,
.commercial-editor-v3.is-free-fee .cd-payment-grid {
    display: none !important;
}

/* Responsive adjustments for 15.6" screens (1366x768) */
@media (max-width: 1200px) {
    .cd-model-options-grid {
        grid-template-columns: 1fr !important;
    }
    .cd-pricing-subcard-grid,
    .cd-policy-grid,
    .cd-payment-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
    .cd-country-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
}

@media (max-width: 767px) {
    .cd-pricing-subcard-grid,
    .cd-policy-grid,
    .cd-payment-grid,
    .cd-country-grid {
        grid-template-columns: 1fr !important;
    }
    .cd-range-head,
    .cd-range-row {
        grid-template-columns: 120px 85px 95px 120px 32px !important;
        min-width: 480px;
    }
    .cd-range-matrix {
        overflow-x: auto;
    }
}
</style>

<!--begin::Hiring Services Commercial Editor (Rule 20 Modern Card + Sub-Card)-->
<div id="commercial-editor-v3" class="commercial-editor-v3">

    <!-- ====================================================================
         TIER 1: TOP HEADER TOOLBAR CARD
         ==================================================================== -->
    <header class="cd-drw-header-card">
        <div class="cd-drw-header-left">
            <div class="cd-drw-symbol {{ $isFulltime ? 'ft' : 'ct' }}" title="{{ $isFulltime ? 'Fulltime Hiring' : 'Contract Staffing' }}">
                {{ $isFulltime ? 'FT' : 'CT' }}
            </div>
            <div class="cd-drw-title-meta">
                <div class="cd-drw-title-row">
                    <h3 class="cd-drw-title">Edit {{ $isFulltime ? 'Fulltime' : 'Contract' }} Hiring Services</h3>
                    <span class="cd-drw-code-chip">#SRV-{{ $isFulltime ? 'FT01' : 'CT02' }}</span>
                    <span class="cd-drw-scope-pill">
                        <svg class="cd-svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg> Direct Client Commercials
                    </span>
                    <span class="cd-drw-status-badge {{ !empty($commercial['status']) ? '' : 'is-inactive' }}">
                        {{ !empty($commercial['status']) ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <p class="cd-drw-subtitle">
                    Configure your placement fee structures, experience bands, guarantees, and operational countries for clients.
                </p>
            </div>
        </div>
        <div class="cd-drw-header-right">
            <button type="button" class="cd-drw-head-btn" data-commercial-reset title="Reset all fields to saved values">
                <svg class="cd-svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Reset
            </button>
            <button type="button"
                    class="cd-drw-close-btn"
                    aria-label="Close drawer"
                    title="Close drawer"
                    onclick="if (window.KTDrawer && KTDrawer.getInstance(document.getElementById('kt_activitiesJob'))) { KTDrawer.getInstance(document.getElementById('kt_activitiesJob')).hide(); } else { $('#kt_activitiesJob > .card > #kt_activities_header #kt_activitiesJob_close').trigger('click'); }">
                ✕
            </button>
        </div>
    </header>

    <!-- ====================================================================
         TIER 2: DEDICATED SUB-TABS CARD (In-Memory 0ms Tab Switching)
         ==================================================================== -->
    <div class="cd-drw-tabs-card">
        <div class="cd-drw-tabs-group">
            <button type="button" class="cd-drw-tab-btn active" data-drawer-tab="all">
                <svg class="cd-svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                <span>All Settings</span>
            </button>
            <button type="button" class="cd-drw-tab-btn" data-drawer-tab="model">
                <svg class="cd-svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Commercial Model & Rates</span>
            </button>
            <button type="button" class="cd-drw-tab-btn" data-drawer-tab="experience">
                <svg class="cd-svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span>Experience Matrix</span>
            </button>
            <button type="button" class="cd-drw-tab-btn" data-drawer-tab="policy">
                <svg class="cd-svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>Replacement & Terms</span>
            </button>
            <button type="button" class="cd-drw-tab-btn" data-drawer-tab="countries">
                <svg class="cd-svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Country Coverage</span>
                <span class="cd-drw-tab-badge" id="drwCountryCountBadge">{{ $activeCountryCount }}</span>
            </button>
        </div>
    </div>

    <!-- Hidden State Inputs for Controller Action Binding -->
    <input class="main_status" type="hidden" value="{{ !empty($commercial['status']) ? 1 : 0 }}">
    <input class="rateTypeClass" type="hidden" value="{{ $rateType }}">
    <input class="updateId" type="hidden" value="{{ $commercial['id'] ?? '' }}">
    <input class="commType" type="hidden" value="{{ $sectionCheck }}">

    <!-- ====================================================================
         TIER 3: PRIMARY CARDS STREAM & DISCRETE SUB-CARDS
         ==================================================================== -->
    <main class="cd-drw-stream">

        <!-- ----------------------------------------------------------------
             CARD 1: COMMERCIAL MODEL
             ---------------------------------------------------------------- -->
        <section class="cd-drw-card" id="drwSectionModel" data-section-category="model">
            <div class="cd-drw-card-head">
                <div class="cd-drw-card-title-group">
                    <span class="cd-drw-step-num">1</span>
                    <div>
                        <h4 class="cd-drw-card-title">Commercial Fee Model</h4>
                        <p class="cd-drw-card-copy">Choose how your agency is compensated for client placements.</p>
                    </div>
                </div>
            </div>

            <input type="hidden" data-commercial-type value="{{ $selectedCommercialModel }}">

            <!-- Discrete Sub-Cards: Model Selection Radios -->
            <div class="cd-model-options-grid" role="radiogroup" aria-label="Commercial fee model">
                <div class="cd-model-card {{ $selectedCommercialModel === 2 ? 'active' : '' }}"
                     data-commercial-model="2"
                     role="radio"
                     aria-checked="{{ $selectedCommercialModel === 2 ? 'true' : 'false' }}">
                    <div class="cd-model-icon-box">%</div>
                    <div class="cd-model-copy-wrap">
                        <h5 class="cd-model-title">Percentage Based</h5>
                        <p class="cd-model-desc">Charge a percentage of candidate's annual CTC or contract bill rate.</p>
                    </div>
                    <span class="cd-model-radio-dot"></span>
                </div>

                <div class="cd-model-card {{ $selectedCommercialModel === 1 ? 'active' : '' }}"
                     data-commercial-model="1"
                     role="radio"
                     aria-checked="{{ $selectedCommercialModel === 1 ? 'true' : 'false' }}">
                    <div class="cd-model-icon-box">₹</div>
                    <div class="cd-model-copy-wrap">
                        <h5 class="cd-model-title">Fixed Placement Fee</h5>
                        <p class="cd-model-desc">Charge a fixed flat amount for each successful hire or billing cycle.</p>
                    </div>
                    <span class="cd-model-radio-dot"></span>
                </div>

                <div class="cd-model-card {{ $selectedCommercialModel === 0 ? 'active' : '' }}"
                     data-commercial-model="0"
                     role="radio"
                     aria-checked="{{ $selectedCommercialModel === 0 ? 'true' : 'false' }}">
                    <div class="cd-model-icon-box">◇</div>
                    <div class="cd-model-copy-wrap">
                        <h5 class="cd-model-title">Free / No Placement Fee</h5>
                        <p class="cd-model-desc">Zero commercial placement fees apply to client engagements.</p>
                    </div>
                    <span class="cd-model-radio-dot"></span>
                </div>
            </div>

            <div class="cd-info-banner">
                <svg class="cd-svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span data-commercial-model-copy>
                    {{ $selectedCommercialModel === 0 ? 'No commercial placement fee will be charged for the selected countries.' : ($selectedCommercialModel === 2 ? "Placement fees are calculated as a percentage of the candidate's Cost to Company (CTC)." : 'Billing is calculated from the configured fixed rate for each experience range.') }}
                </span>
            </div>
        </section>

        <!-- ----------------------------------------------------------------
             CARD 2: DEFAULT BASE PRICING
             ---------------------------------------------------------------- -->
        <section class="cd-drw-card" id="drwSectionPricing" data-section-category="model">
            <div class="cd-drw-card-head">
                <div class="cd-drw-card-title-group">
                    <span class="cd-drw-step-num">2</span>
                    <div>
                        <h4 class="cd-drw-card-title">Default Base Pricing</h4>
                        <p class="cd-drw-card-copy" data-default-pricing-copy>
                            {{ $selectedCommercialModel === 1 ? 'Set the default fixed fee and how it will be charged.' : 'Set the default pricing for all experience ranges.' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Discrete Sub-Cards Grid: Base Controls -->
            <div class="cd-pricing-subcard-grid">

                <!-- Sub-Card: Currency -->
                <div class="cd-field-group">
                    <label class="cd-field-label" for="commercial_salary_currency">Currency</label>
                    <select id="commercial_salary_currency" name="salary_currency" class="cd-field-control" data-pricing-currency>
                        @foreach($currenciesToRender as $cItem)
                            @php
                                if (is_array($cItem)) {
                                    $optCode = $cItem['code'] ?? '';
                                    $optKey = $cItem['key'] ?? (\App\Services\CurrencyService::resolveCurrencyKey($optCode) ?? $loop->index);
                                    $optLabel = $cItem['label'] ?? ($optCode . ' — ' . ($cItem['name'] ?? ''));
                                    $optKeywords = $cItem['keywords'] ?? strtolower($optCode . ' ' . ($cItem['name'] ?? ''));
                                } else {
                                    $optKey = $loop->index;
                                    $optCode = (string) $cItem;
                                    $cMeta = \App\Helper\GeneralMethod::currencyEnterpriseMeta($optCode);
                                    $optLabel = $cMeta['label'] ?? $optCode;
                                    $optKeywords = $cMeta['keywords'] ?? strtolower($optCode);
                                }
                                $isSelected = ($selectedCurrency === (int) $optKey || (string) $selectedCurrency === (string) $optKey);
                            @endphp
                            <option value="{{ $optKey }}" data-code="{{ $optCode }}" data-search="{{ $optKeywords }}" {{ $isSelected ? 'selected' : '' }}>
                                {{ $optLabel }}
                            </option>
                        @endforeach
                    </select>
                    <span class="cd-field-help">Multi-tenant authorized currency</span>
                </div>

                <!-- Sub-Card: Default Rate / Amount -->
                <div class="cd-field-group">
                    <label class="cd-field-label" for="commercial_default_rate" data-rate-label>
                        {{ $selectedCommercialModel === 2 ? 'Rate / Percentage' : 'Fixed Fee Amount' }}
                    </label>
                    <div class="cd-rate-input-wrap">
                        <input type="number"
                               id="commercial_default_rate"
                               class="cd-field-control"
                               data-fallback-rate="{{ $fallbackDefaultRate }}"
                               step="0.01"
                               min="0"
                               value="{{ $defaultRate }}" />
                        <span class="cd-rate-suffix" data-rate-suffix>{{ $selectedCommercialModel === 2 ? '%' : '' }}</span>
                    </div>
                    <span class="cd-field-help" data-rate-help>
                        {{ $selectedCommercialModel === 2 ? 'Changing this rate updates all experience ranges.' : 'Default fixed amount applied per successful hire.' }}
                    </span>
                </div>

                <!-- Sub-Card: Applied On / Billing Mode -->
                <div class="cd-field-group">
                    <label class="cd-field-label" for="commercial_salary_mode">Applied On</label>
                    <select id="commercial_salary_mode" name="sal_mode" class="cd-field-control" data-applied-on>
                        @foreach(($salaryMode ?? []) as $modeKey => $modeLabel)
                            <option value="{{ $modeKey }}" {{ $selectedSalaryMode === (int) $modeKey ? 'selected' : '' }}>
                                {{ $modeLabel }} {{ $isFulltime ? 'CTC' : 'Bill Rate' }}
                            </option>
                        @endforeach
                    </select>
                    <span class="cd-field-help">Commercial billing basis</span>
                </div>

                <!-- Sub-Card: Fixed Trigger (Fixed Fee Only) -->
                <div class="cd-field-group cd-fixed-only">
                    <label class="cd-field-label">Fee Charged On</label>
                    <select class="cd-field-control" data-fixed-fee-trigger>
                        @foreach(($billingTriggers ?? []) as $triggerValue => $triggerLabel)
                            <option value="{{ $triggerValue }}" {{ $selectedBillingTrigger === (string) $triggerValue ? 'selected' : '' }}>
                                {{ $triggerValue === 'candidate_joined' ? 'Per Successful Hire' : $triggerLabel }}
                            </option>
                        @endforeach
                    </select>
                    <span class="cd-field-help">When fee becomes chargeable</span>
                </div>

                <!-- Sub-Card: Fixed Frequency (Fixed Fee Only) -->
                <div class="cd-field-group cd-fixed-only">
                    <label class="cd-field-label">Billing Frequency</label>
                    <select class="cd-field-control" data-fixed-fee-frequency>
                        @foreach(($billingFrequencies ?? []) as $frequencyValue => $frequencyLabel)
                            <option value="{{ $frequencyValue }}" {{ $selectedBillingFrequency === (string) $frequencyValue ? 'selected' : '' }}>
                                {{ $frequencyLabel }}
                            </option>
                        @endforeach
                    </select>
                    <span class="cd-field-help">Invoicing cadence</span>
                </div>

                <!-- Free/No Fee Banner -->
                <div class="cd-free-fee-banner">
                    <svg class="cd-svg-icon text-success" style="width:24px;height:24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <strong class="d-block text-dark fs-6">Zero Commercial Fee Agreement</strong>
                        <span class="text-muted fs-8">This service agreement is configured as Free / No Fee. No candidate placement charges will apply.</span>
                    </div>
                </div>

            </div>
        </section>

        <!-- ----------------------------------------------------------------
             CARD 3: EXPERIENCE-BASED PRICING MATRIX
             ---------------------------------------------------------------- -->
        <section class="cd-drw-card" id="drwSectionExperience" data-section-category="experience">
            <div class="cd-drw-card-head">
                <div class="cd-drw-card-title-group">
                    <span class="cd-drw-step-num">3</span>
                    <div>
                        <h4 class="cd-drw-card-title">Experience-Based Pricing Matrix</h4>
                        <p class="cd-drw-card-copy" data-experience-pricing-copy>
                            {{ $selectedCommercialModel === 1 ? 'Define different fixed fees for different experience bands.' : 'Define pricing tiers using clear candidate experience ranges.' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Tabular Matrix Sub-Card -->
            <div class="cd-range-matrix">
                <div class="cd-range-head">
                    <span>Experience Tier</span>
                    <span>Currency</span>
                    <span data-range-rate-label>{{ $selectedCommercialModel === 2 ? 'Rate / Percentage' : 'Fixed Fee Amount' }}</span>
                    <span>Timing / Basis</span>
                    <span>Reset</span>
                </div>

                @foreach($experienceRanges as $range)
                    @php
                        $firstExperienceIndex = $range['indexes'][0];
                        $rangeRate = data_get($commercialDetails->get($firstExperienceIndex), 'rate', $defaultRate);
                    @endphp
                    <div class="cd-range-row" data-experience-range>
                        <div class="cd-tier-badge">
                            <svg class="cd-svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ $range['label'] }}</span>
                        </div>

                        <div>
                            <select class="cd-field-control" data-range-currency>
                                @foreach($currenciesToRender as $cItem)
                                    @php
                                        if (is_array($cItem)) {
                                            $optCode = $cItem['code'] ?? '';
                                            $optKey = $cItem['key'] ?? (\App\Services\CurrencyService::resolveCurrencyKey($optCode) ?? $loop->index);
                                        } else {
                                            $optKey = $loop->index;
                                            $optCode = (string) $cItem;
                                        }
                                        $isSelected = ($selectedCurrency === (int) $optKey || (string) $selectedCurrency === (string) $optKey);
                                    @endphp
                                    <option value="{{ $optKey }}" {{ $isSelected ? 'selected' : '' }}>{{ $optCode }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="cd-rate-input-wrap">
                            <input type="number"
                                   class="cd-field-control"
                                   data-range-rate
                                   step="0.01"
                                   min="0"
                                   value="{{ $rangeRate }}" />
                            <span class="cd-rate-suffix" data-rate-suffix>{{ $selectedCommercialModel === 2 ? '%' : '' }}</span>
                        </div>

                        <div>
                            <select class="cd-field-control cd-percentage-only" data-range-applied-on>
                                @foreach(($salaryMode ?? []) as $modeKey => $modeLabel)
                                    <option value="{{ $modeKey }}" {{ $selectedSalaryMode === (int) $modeKey ? 'selected' : '' }}>
                                        {{ $modeLabel }} {{ $isFulltime ? 'CTC' : 'Bill Rate' }}
                                    </option>
                                @endforeach
                            </select>
                            <select class="cd-field-control cd-fixed-only" data-range-fixed-trigger>
                                @foreach(($billingTriggers ?? []) as $triggerValue => $triggerLabel)
                                    <option value="{{ $triggerValue }}" {{ $selectedBillingTrigger === (string) $triggerValue ? 'selected' : '' }}>
                                        {{ $triggerValue === 'candidate_joined' ? 'Per Hire' : $triggerLabel }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <button type="button"
                                    class="cd-range-reset-btn"
                                    data-range-reset
                                    title="Reset tier to default rate"
                                    aria-label="Reset {{ $range['label'] }}">
                                ↻
                            </button>
                        </div>

                        <!-- Preserved Hidden Persistence Inputs -->
                        @foreach($range['indexes'] as $experienceIndex)
                            @php $experienceRate = data_get($commercialDetails->get($experienceIndex), 'rate', $defaultRate); @endphp
                            <input type="hidden"
                                   class="{{ $isFulltime ? 'perecentage_val' : 'rate_val' }}"
                                   name="{{ $isFulltime ? 'perecentage[]' : 'rate[]' }}"
                                   data-experience-index="{{ $experienceIndex }}"
                                   value="{{ $experienceRate }}" />
                        @endforeach
                    </div>
                @endforeach
            </div>

            <div class="cd-info-banner blue mt-3">
                <svg class="cd-svg-icon text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span data-experience-info>
                    Each experience band is saved through the existing data structure. Currency and applied billing period remain consistent across the commercial tier.
                </span>
            </div>
        </section>

        <!-- ----------------------------------------------------------------
             CARD 4: REPLACEMENT GUARANTEE POLICY
             ---------------------------------------------------------------- -->
        <section class="cd-drw-card" id="drwSectionReplacement" data-section-category="policy">
            <div class="cd-drw-card-head">
                <div class="cd-drw-card-title-group">
                    <span class="cd-drw-step-num">4</span>
                    <div>
                        <h4 class="cd-drw-card-title">Replacement Guarantee Policy</h4>
                        <p class="cd-drw-card-copy">Define warranty period, remedy clause, and candidate exit conditions for clients.</p>
                    </div>
                </div>
            </div>

            <!-- Discrete Sub-Cards Grid -->
            <div class="cd-policy-grid">
                <div class="cd-field-group">
                    <label class="cd-field-label" for="commercial_replacement_period">Guarantee Period</label>
                    <select id="commercial_replacement_period" class="cd-field-control" data-replacement-period>
                        @foreach(($replacementPeriods ?? []) as $periodValue => $periodLabel)
                            <option value="{{ $periodValue }}" {{ $selectedReplacementPeriod === (int) $periodValue ? 'selected' : '' }}>
                                {{ $periodLabel }}
                            </option>
                        @endforeach
                    </select>
                    <span class="cd-field-help">Warranty window for replacement</span>
                </div>

                <div class="cd-field-group">
                    <label class="cd-field-label" for="commercial_replacement_type">Replacement Type</label>
                    <select id="commercial_replacement_type" class="cd-field-control" data-replacement-type>
                        @foreach(($replacementTypes ?? []) as $typeValue => $typeLabel)
                            <option value="{{ $typeValue }}" {{ $selectedReplacementType === (string) $typeValue ? 'selected' : '' }}>
                                {{ $typeLabel }}
                            </option>
                        @endforeach
                    </select>
                    <span class="cd-field-help">Remedy offered to the client</span>
                </div>

                <div class="cd-field-group">
                    <label class="cd-field-label" for="commercial_replacement_eligibility">Replacement Eligibility</label>
                    <select id="commercial_replacement_eligibility" class="cd-field-control" data-replacement-eligibility>
                        @foreach(($replacementEligibility ?? []) as $eligibilityValue => $eligibilityLabel)
                            <option value="{{ $eligibilityValue }}" {{ $selectedReplacementEligibility === (string) $eligibilityValue ? 'selected' : '' }}>
                                {{ $eligibilityLabel }}
                            </option>
                        @endforeach
                    </select>
                    <span class="cd-field-help">Eligible exit circumstances</span>
                </div>

                <div class="cd-policy-alert">
                    <svg class="cd-svg-icon text-primary" style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <div>
                        A <b data-replacement-summary>{{ strtolower($replacementTypes[$selectedReplacementType] ?? 'free replacement') }}</b> guarantee applies when an eligible candidate exit occurs within <b data-replacement-days>{{ $selectedReplacementPeriod }}</b> days of joining.
                    </div>
                </div>
            </div>
        </section>

        <!-- ----------------------------------------------------------------
             CARD 5: PAYMENT & BILLING TIMELINE
             ---------------------------------------------------------------- -->
        <section class="cd-drw-card" id="drwSectionPayment" data-section-category="policy">
            <div class="cd-drw-card-head">
                <div class="cd-drw-card-title-group">
                    <span class="cd-drw-step-num">5</span>
                    <div>
                        <h4 class="cd-drw-card-title">Invoicing & Payment Timeline</h4>
                        <p class="cd-drw-card-copy">Configure billing cadence, invoice triggers, and payment credit terms.</p>
                    </div>
                </div>
            </div>

            <!-- Discrete Sub-Cards Grid -->
            <div class="cd-payment-grid">
                <div class="cd-field-group">
                    <label class="cd-field-label" for="commercial_billing_frequency">Invoicing Cadence</label>
                    <select id="commercial_billing_frequency" class="cd-field-control" data-billing-frequency>
                        @foreach(($billingFrequencies ?? []) as $frequencyValue => $frequencyLabel)
                            <option value="{{ $frequencyValue }}" {{ $selectedBillingFrequency === (string) $frequencyValue ? 'selected' : '' }}>
                                {{ $frequencyLabel }}
                            </option>
                        @endforeach
                    </select>
                    <span class="cd-field-help">How often invoices are raised</span>
                </div>

                <div class="cd-field-group">
                    <label class="cd-field-label" for="commercial_billing_trigger">Invoice Trigger Event</label>
                    <select id="commercial_billing_trigger" class="cd-field-control" data-billing-trigger>
                        @foreach(($billingTriggers ?? []) as $triggerValue => $triggerLabel)
                            <option value="{{ $triggerValue }}" {{ $selectedBillingTrigger === (string) $triggerValue ? 'selected' : '' }}>
                                {{ $triggerLabel }}
                            </option>
                        @endforeach
                    </select>
                    <span class="cd-field-help">Event that initiates the invoice</span>
                </div>

                <div class="cd-field-group">
                    <label class="cd-field-label" for="commercial_payment_terms">Payment Terms (Credit Window)</label>
                    <select id="commercial_payment_terms" class="cd-field-control" data-payment-terms>
                        @foreach(($paymentTerms ?? []) as $termsValue => $termsLabel)
                            <option value="{{ $termsValue }}" {{ $selectedPaymentTerms === (int) $termsValue ? 'selected' : '' }}>
                                {{ $termsLabel }}
                            </option>
                        @endforeach
                    </select>
                    <span class="cd-field-help">Net days allowed for settlement</span>
                </div>
            </div>
        </section>

        <!-- ----------------------------------------------------------------
             CARD 6: OPERATIONAL COUNTRIES & GEOGRAPHIC COVERAGE
             ---------------------------------------------------------------- -->
        <section class="cd-drw-card" id="drwSectionCountries" data-section-category="countries">
            <div class="cd-drw-card-head">
                <div class="cd-drw-card-title-group">
                    <span class="cd-drw-step-num">6</span>
                    <div>
                        <h4 class="cd-drw-card-title">Operational Countries & Geographic Scope</h4>
                        <p class="cd-drw-card-copy">Select the countries where your company offers {{ $isFulltime ? 'fulltime hiring' : 'contract staffing' }} services.</p>
                    </div>
                </div>
                <div class="cd-drw-scope-pill">
                    <svg class="cd-svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span id="countrySelectedHeaderBadge">{{ $activeCountryCount }} Countries Active</span>
                </div>
            </div>

            <!-- Toolbar Sub-Card -->
            <div class="cd-country-toolbar">
                <div class="cd-country-search-box">
                    <svg class="cd-svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="search"
                           class="cd-country-search-input"
                           placeholder="Search country by name or code..."
                           data-country-search />
                </div>
                <div class="cd-country-btn-group">
                    <button type="button" class="cd-country-btn" data-country-top title="Quick select top global hiring hubs">
                        <svg class="cd-svg-icon text-warning me-1" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg> Top Hiring Hubs
                    </button>
                    <button type="button" class="cd-country-btn" data-country-select-all>
                        <svg class="cd-svg-icon me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7m-14 6l4 4L19 7"/></svg> Select All
                    </button>
                    <button type="button" class="cd-country-btn" data-country-clear>
                        <svg class="cd-svg-icon me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg> Clear All
                    </button>
                </div>
            </div>

            <!-- Selected Countries Chip Bar -->
            <div class="cd-selected-chips-bar" data-selected-countries></div>

            <!-- Region Tabs -->
            <div class="cd-region-tabs">
                <button type="button" class="cd-region-tab active" data-region="all">All Regions</button>
                @foreach($countryRegions as $regionName => $regionCountries)
                    <button type="button" class="cd-region-tab" data-region="{{ strtolower($regionName) }}">
                        {{ $regionName }} ({{ count($regionCountries) }})
                    </button>
                @endforeach
            </div>

            <!-- Country Selection Grid (High Density 3-Column Grid) -->
            <div class="cd-country-grid" data-country-grid>
                @foreach(($countries ?? []) as $country)
                    @php
                        $countryId = (int) $country->id;
                        $countryRegion = strtolower(trim((string) ($country->region ?? '')) ?: 'other');
                        $countryCode = strtoupper(trim((string) ($country->iso2 ?? $country->country_code ?? '')));
                    @endphp
                    <div class="cd-country-option"
                         data-country-item
                         data-name="{{ strtolower(trim($countryCode . ' ' . $country->country_name)) }}"
                         data-region="{{ $countryRegion }}">
                        <input class="countries"
                               type="checkbox"
                               id="commercial_country_{{ $sectionCheck }}_{{ $countryId }}"
                               value="{{ $countryId }}"
                               data-country-name="{{ $country->country_name }}"
                               data-country-code="{{ $countryCode }}"
                               data-initial="{{ in_array($countryId, $selectedCountryIds, true) ? '1' : '0' }}"
                               {{ in_array($countryId, $selectedCountryIds, true) ? 'checked' : '' }} />
                        <label class="cd-country-label" for="commercial_country_{{ $sectionCheck }}_{{ $countryId }}">
                            <span class="cd-iso-badge">{{ $countryCode ?: '—' }}</span>
                            <span>{{ $country->country_name }}</span>
                        </label>
                    </div>
                @endforeach
            </div>
        </section>

    </main>

    <!-- ====================================================================
         STICKY FOOTER ACTION STRIP
         ==================================================================== -->
    <footer class="cd-drw-footer">
        <div class="cd-drw-footer-left">
            <button type="button" class="cd-drw-btn" data-commercial-cancel>
                <svg class="cd-svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg> Cancel
            </button>
            <button type="button" class="cd-drw-btn" data-commercial-reset>
                <svg class="cd-svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Reset to Saved Values
            </button>
        </div>
        <div class="cd-drw-footer-right">
            <button type="button"
                    id="form-submit"
                    class="btn btn-primary fw-bold copy_current_selected_applicant cd-drw-btn btn-primary"
                    aria-live="polite">
                <span class="indicator-label">
                    <svg class="cd-svg-icon me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Save Commercials
                </span>
                <span class="indicator-progress">
                    <span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>
                    Saving Commercials...
                </span>
            </button>
        </div>
    </footer>

</div>

<!-- ========================================================================
     CLIENT-SIDE INTERACTIVE DRAWER CONTROLLER
     ======================================================================== -->
<script>
(function () {
    'use strict';

    var root = document.getElementById('commercial-editor-v3');
    if (!root) return;

    var countries = Array.prototype.slice.call(root.querySelectorAll('.countries'));
    var countryItems = Array.prototype.slice.call(root.querySelectorAll('[data-country-item]'));
    var selectedHost = root.querySelector('[data-selected-countries]');
    var search = root.querySelector('[data-country-search]');
    var appliedOn = root.querySelector('[data-applied-on]');
    var pricingCurrency = root.querySelector('[data-pricing-currency]');
    var commercialType = root.querySelector('[data-commercial-type]');
    var defaultRateInput = root.querySelector('#commercial_default_rate');
    var replacementPeriod = root.querySelector('[data-replacement-period]');
    var replacementType = root.querySelector('[data-replacement-type]');
    var replacementEligibility = root.querySelector('[data-replacement-eligibility]');
    var billingFrequency = root.querySelector('[data-billing-frequency]');
    var billingTrigger = root.querySelector('[data-billing-trigger]');
    var fixedFeeTrigger = root.querySelector('[data-fixed-fee-trigger]');
    var fixedFeeFrequency = root.querySelector('[data-fixed-fee-frequency]');
    var paymentTerms = root.querySelector('[data-payment-terms]');
    var initialState;
    var activeRegion = 'all';

    function updateCommercialType() {
        var modelValue = commercialType.value;
        var isPercentage = modelValue === '2';
        var isFixedFee = modelValue === '1';
        var isFree = modelValue === '0';
        var wasFree = defaultRateInput.disabled;
        var supportedServiceType = root.querySelector('.commType').value === '2' ? '2' : '1';

        root.querySelector('.rateTypeClass').value = isFree ? supportedServiceType : modelValue;

        root.querySelectorAll('[data-commercial-model]').forEach(function (option) {
            var active = option.dataset.commercialModel === modelValue;
            option.classList.toggle('active', active);
            option.setAttribute('aria-checked', active ? 'true' : 'false');
        });

        root.classList.toggle('is-fixed-fee', isFixedFee);
        root.classList.toggle('is-free-fee', isFree);

        if (isFree) {
            defaultRateInput.value = '0';
            defaultRateInput.dispatchEvent(new Event('input'));
        } else if (wasFree && Number(defaultRateInput.value) === 0) {
            defaultRateInput.value = defaultRateInput.dataset.fallbackRate || '8.33';
            defaultRateInput.dispatchEvent(new Event('input'));
        }

        defaultRateInput.disabled = isFree;
        appliedOn.disabled = isFree;
        pricingCurrency.disabled = isFree;

        root.querySelectorAll('[data-range-rate],[data-range-applied-on],[data-range-currency],[data-range-fixed-trigger],[data-range-fixed-frequency],[data-range-reset]').forEach(function (control) {
            control.disabled = isFree;
        });

        root.querySelectorAll('[data-fixed-fee-trigger],[data-fixed-fee-frequency]').forEach(function (control) {
            control.disabled = isFree;
        });

        root.querySelectorAll('[data-rate-suffix]').forEach(function (suffix) {
            suffix.textContent = isPercentage ? '%' : '';
        });

        var rateLabelEl = root.querySelector('[data-rate-label]');
        if (rateLabelEl) rateLabelEl.textContent = isFree ? 'No Fee' : (isPercentage ? 'Rate / Percentage' : 'Fixed Fee Amount');

        var rateHelpEl = root.querySelector('[data-rate-help]');
        if (rateHelpEl) rateHelpEl.textContent = isFree ? 'No commercial fee is charged.' : (isPercentage ? 'Changing this rate updates all experience tiers.' : 'Default fixed amount applied per successful hire.');

        var rangeRateLabelEl = root.querySelector('[data-range-rate-label]');
        if (rangeRateLabelEl) rangeRateLabelEl.textContent = isFree ? 'No Fee' : (isPercentage ? 'Rate / Percentage' : 'Fixed Fee Amount');

        var defaultPricingCopyEl = root.querySelector('[data-default-pricing-copy]');
        if (defaultPricingCopyEl) defaultPricingCopyEl.textContent = isFree ? 'No charge will be applied for this service.' : (isPercentage ? 'Set the default pricing for all experience ranges.' : 'Set the default fixed fee and how it will be charged.');

        var expPricingCopyEl = root.querySelector('[data-experience-pricing-copy]');
        if (expPricingCopyEl) expPricingCopyEl.textContent = isFree ? 'No per-range fee is required for this service.' : (isPercentage ? 'Define pricing tiers using clear candidate experience ranges.' : 'Define different fixed fees for different experience bands.');

        var commModelCopyEl = root.querySelector('[data-commercial-model-copy]');
        if (commModelCopyEl) commModelCopyEl.textContent = isFree ? 'No commercial placement fee will be charged for the selected countries.' : (isPercentage ? "Placement fees are calculated as a percentage of the candidate's Cost to Company (CTC)." : 'Billing is calculated from the configured fixed rate for each experience range.');
    }

    function captureInitialState() {
        initialState = {
            commercialType: commercialType.value,
            defaultRate: defaultRateInput.value,
            appliedOn: appliedOn.value,
            currency: pricingCurrency.value,
            replacementPeriod: replacementPeriod.value,
            replacementType: replacementType.value,
            replacementEligibility: replacementEligibility.value,
            billingFrequency: billingFrequency.value,
            billingTrigger: billingTrigger.value,
            paymentTerms: paymentTerms.value,
            storedRates: Array.prototype.map.call(root.querySelectorAll('.rate_val,.perecentage_val'), function (rate) { return rate.value; }),
            countries: countries.map(function (country) { return country.checked; })
        };
    }

    function restoreInitialState() {
        if (!initialState) return;

        commercialType.value = initialState.commercialType;
        defaultRateInput.value = initialState.defaultRate;
        appliedOn.value = initialState.appliedOn;
        pricingCurrency.value = initialState.currency;
        replacementPeriod.value = initialState.replacementPeriod;
        replacementType.value = initialState.replacementType;
        replacementEligibility.value = initialState.replacementEligibility;
        billingFrequency.value = initialState.billingFrequency;
        billingTrigger.value = initialState.billingTrigger;
        paymentTerms.value = initialState.paymentTerms;

        root.querySelectorAll('.rate_val,.perecentage_val').forEach(function (rate, index) {
            rate.value = initialState.storedRates[index];
        });

        root.querySelectorAll('[data-experience-range]').forEach(function (rangeRow) {
            var hiddenRate = rangeRow.querySelector('.rate_val,.perecentage_val');
            var inputRate = rangeRow.querySelector('[data-range-rate]');
            if (hiddenRate && inputRate) {
                inputRate.value = hiddenRate.value;
            }
        });

        countries.forEach(function (country, index) {
            country.checked = initialState.countries[index];
        });

        syncRangeSelects('[data-range-applied-on]', appliedOn.value);
        syncRangeSelects('[data-range-currency]', pricingCurrency.value);
        syncRangeSelects('[data-fixed-fee-trigger]', billingTrigger.value);
        syncRangeSelects('[data-fixed-fee-frequency]', billingFrequency.value);

        updateCommercialType();
        updateReplacementSummary();
        renderSelectedCountries();
    }

    function updateReplacementSummary() {
        var daysEl = root.querySelector('[data-replacement-days]');
        var summaryEl = root.querySelector('[data-replacement-summary]');
        if (daysEl) daysEl.textContent = replacementPeriod.value;
        if (summaryEl && replacementType.options[replacementType.selectedIndex]) {
            summaryEl.textContent = replacementType.options[replacementType.selectedIndex].text.toLowerCase();
        }
    }

    function syncRangeSelects(selector, value) {
        root.querySelectorAll(selector).forEach(function (select) {
            select.value = value;
        });
    }

    // Range matrix event bindings
    root.querySelectorAll('[data-experience-range]').forEach(function (rangeRow) {
        var rangeRate = rangeRow.querySelector('[data-range-rate]');
        var storedRates = rangeRow.querySelectorAll('.rate_val,.perecentage_val');
        if (rangeRate) {
            rangeRate.addEventListener('input', function () {
                storedRates.forEach(function (rate) {
                    rate.value = rangeRate.value;
                });
            });
        }
        var resetBtn = rangeRow.querySelector('[data-range-reset]');
        if (resetBtn) {
            resetBtn.addEventListener('click', function () {
                if (rangeRate && defaultRateInput) {
                    rangeRate.value = defaultRateInput.value;
                    rangeRate.dispatchEvent(new Event('input'));
                }
            });
        }
    });

    appliedOn.addEventListener('change', function () { syncRangeSelects('[data-range-applied-on]', appliedOn.value); });
    pricingCurrency.addEventListener('change', function () { syncRangeSelects('[data-range-currency]', pricingCurrency.value); });
    billingTrigger.addEventListener('change', function () { syncRangeSelects('[data-fixed-fee-trigger]', billingTrigger.value); });
    billingFrequency.addEventListener('change', function () { syncRangeSelects('[data-fixed-fee-frequency]', billingFrequency.value); });
    replacementPeriod.addEventListener('change', updateReplacementSummary);
    replacementType.addEventListener('change', updateReplacementSummary);

    root.querySelectorAll('[data-commercial-model]').forEach(function (option) {
        option.addEventListener('click', function () {
            commercialType.value = option.dataset.commercialModel;
            updateCommercialType();
        });
    });

    defaultRateInput.addEventListener('input', function () {
        root.querySelectorAll('[data-range-rate]').forEach(function (rangeRate) {
            rangeRate.value = defaultRateInput.value;
            rangeRate.dispatchEvent(new Event('input'));
        });
    });

    root.querySelectorAll('[data-range-applied-on]').forEach(function (select) {
        select.addEventListener('change', function () {
            appliedOn.value = select.value;
            syncRangeSelects('[data-range-applied-on]', select.value);
        });
    });

    root.querySelectorAll('[data-range-currency]').forEach(function (select) {
        select.addEventListener('change', function () {
            pricingCurrency.value = select.value;
            syncRangeSelects('[data-range-currency]', select.value);
        });
    });

    root.querySelectorAll('[data-fixed-fee-trigger]').forEach(function (select) {
        select.addEventListener('change', function () {
            billingTrigger.value = select.value;
            syncRangeSelects('[data-fixed-fee-trigger]', select.value);
        });
    });

    root.querySelectorAll('[data-fixed-fee-frequency]').forEach(function (select) {
        select.addEventListener('change', function () {
            billingFrequency.value = select.value;
            syncRangeSelects('[data-fixed-fee-frequency]', select.value);
        });
    });

    // Country chips rendering & counter synchronization
    function renderSelectedCountries() {
        var selected = countries.filter(function (country) { return country.checked; });
        if (!selectedHost) return;

        selectedHost.innerHTML = '';
        selected.slice(0, 8).forEach(function (country) {
            var chip = document.createElement('span');
            chip.className = 'cd-country-chip-item';
            chip.innerHTML = '<span class="cd-chip-code"></span><span></span><button type="button" class="cd-chip-remove" aria-label="Remove country">✕</button>';
            chip.children[0].textContent = country.dataset.countryCode || '—';
            chip.children[1].textContent = country.dataset.countryName;
            chip.querySelector('button').addEventListener('click', function () {
                country.checked = false;
                renderSelectedCountries();
            });
            selectedHost.appendChild(chip);
        });

        if (selected.length > 8) {
            var more = document.createElement('span');
            more.className = 'cd-country-chip-item';
            more.style.background = '#eff6ff';
            more.style.borderColor = '#bfdbfe';
            more.style.color = '#009ef7';
            more.textContent = '+ ' + (selected.length - 8) + ' more';
            selectedHost.appendChild(more);
        }

        if (!selected.length) {
            var empty = document.createElement('span');
            empty.className = 'text-muted fs-8';
            empty.innerHTML = '<svg class="cd-svg-icon me-1" style="width:13px;height:13px;display:inline-block;vertical-align:-2px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> No countries selected yet';
            selectedHost.appendChild(empty);
        }

        // Synchronize Tier 2 tab badge and section header pill
        var countBadge = document.getElementById('drwCountryCountBadge');
        if (countBadge) countBadge.textContent = selected.length;

        var headerBadge = document.getElementById('countrySelectedHeaderBadge');
        if (headerBadge) headerBadge.textContent = selected.length + (selected.length === 1 ? ' Country Active' : ' Countries Active');
    }

    function filterCountries() {
        var term = (search.value || '').trim().toLowerCase();
        countryItems.forEach(function (item) {
            var matchesRegion = (activeRegion === 'all' || item.dataset.region === activeRegion);
            var matchesSearch = (!term || item.dataset.name.indexOf(term) !== -1);
            item.hidden = !(matchesRegion && matchesSearch);
        });
    }

    countries.forEach(function (country) {
        country.addEventListener('change', renderSelectedCountries);
    });

    if (search) {
        search.addEventListener('input', filterCountries);
    }

    root.querySelectorAll('.cd-region-tab[data-region]').forEach(function (button) {
        button.addEventListener('click', function () {
            activeRegion = button.dataset.region;
            root.querySelectorAll('.cd-region-tab[data-region]').forEach(function (tab) {
                tab.classList.toggle('active', tab === button);
            });
            filterCountries();
        });
    });

    root.querySelectorAll('[data-country-select-all]').forEach(function (button) {
        button.addEventListener('click', function () {
            countryItems.filter(function (item) { return !item.hidden; }).forEach(function (item) {
                var chk = item.querySelector('.countries');
                if (chk) chk.checked = true;
            });
            renderSelectedCountries();
        });
    });

    root.querySelectorAll('[data-country-clear]').forEach(function (button) {
        button.addEventListener('click', function () {
            countries.forEach(function (country) { country.checked = false; });
            renderSelectedCountries();
        });
    });

    var topBtn = root.querySelector('[data-country-top]');
    if (topBtn) {
        topBtn.addEventListener('click', function () {
            var topCountries = ['india', 'united states', 'australia', 'united kingdom', 'united arab emirates', 'singapore', 'germany', 'canada'];
            countries.forEach(function (country) {
                var cName = (country.dataset.countryName || '').toLowerCase();
                country.checked = (topCountries.indexOf(cName) !== -1);
            });
            renderSelectedCountries();
        });
    }

    // Tier 2 In-Memory Sub-Tab Switching (0ms RAM Switching)
    root.querySelectorAll('.cd-drw-tab-btn').forEach(function (tabBtn) {
        tabBtn.addEventListener('click', function () {
            root.querySelectorAll('.cd-drw-tab-btn').forEach(function (btn) { btn.classList.remove('active'); });
            tabBtn.classList.add('active');

            var target = tabBtn.dataset.drawerTab;
            var allSections = root.querySelectorAll('.cd-drw-card');

            if (target === 'all') {
                allSections.forEach(function (sec) { sec.style.display = 'block'; });
            } else {
                allSections.forEach(function (sec) {
                    var matches = (sec.dataset.sectionCategory === target);
                    sec.style.display = matches ? 'block' : 'none';
                });
            }

            // Smooth scroll to top of stream
            var stream = root.querySelector('.cd-drw-stream');
            if (stream) stream.scrollTop = 0;
        });
    });

    root.querySelectorAll('[data-commercial-reset]').forEach(function (button) {
        button.addEventListener('click', restoreInitialState);
    });

    var cancelBtn = root.querySelector('[data-commercial-cancel]');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function () {
            var closeBtn = root.querySelector('.cd-drw-close-btn');
            if (closeBtn) closeBtn.click();
        });
    }

    root.addEventListener('commercial:saved', captureInitialState);

    updateCommercialType();
    updateReplacementSummary();
    captureInitialState();
    renderSelectedCountries();
})();
</script>
