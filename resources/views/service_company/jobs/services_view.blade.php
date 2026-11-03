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
     * Date: November 02, 2026
     */
    $commercial = @$commericials[0] ?? [];
    $isFulltime = (int) $sectionCheck === 2;
    $rateType = (int) ($commercial['rateType'] ?? $sectionCheck);
    $commercialDetails = collect($commercial['commericial_details'] ?? [])->keyBy('exp');
    $selectedCountryIds = array_map('intval', $commercial['commericial_countries'] ?? []);
    $tenantCompanyId = (int) ($commercial['company_id'] ?? auth()->user()->company_id ?? 0);
    $registeredCountryIds = \App\Services\CommercialReadModelService::getCompanyRegisteredCountryIds($tenantCompanyId);
    // Requirement: Automatically auto-select Services Countries from company registrations if none saved yet
    if (empty($selectedCountryIds)) {
        $selectedCountryIds = $registeredCountryIds;
    }
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

    // Requirement 2 & 3: For Fulltime Fixed Placement Fee, default amount is 10,000 and billing basis is Per Hire (3)
    if ($isFulltime && $selectedCommercialModel === 1) {
        if ((float) $defaultRate <= 100.0 || (float) $defaultRate === 8.33) {
            $defaultRate = 10000.00;
        }
    }

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
    if ($isFulltime && $selectedCommercialModel === 1) {
        $selectedSalaryMode = 3; // Per Hire
    }

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
    $isServiceActive = !empty($commercial['status']);
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
    width: 100%;
    min-height: 100%;
    background: #f8fafc;
    color: #0f172a;
    font-size: 12px;
    display: flex;
    flex-direction: column;
    overflow-x: hidden;
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

/* --------------------------------------------------------------------------
   TIER 1: TOP HEADER TOOLBAR CARD (Sticky #ffffff, Border #e2e8f0, Radius 10px)
   -------------------------------------------------------------------------- */
.cd-drw-header-card {
    position: sticky;
    top: 0;
    z-index: 40;
    background: #ffffff !important;
    border-bottom: 1px solid #e2e8f0 !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
    padding: 12px 20px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 16px !important;
    flex-wrap: nowrap;
}

.cd-drw-header-left {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
    flex: 1 1 auto;
}

.cd-drw-symbol {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13.5px;
    font-weight: 700;
    flex-shrink: 0;
    border: 1px solid;
    transition: all 0.2s ease;
}

.cd-drw-symbol.ft {
    background: #eff6ff;
    color: #009ef7;
    border-color: #bfdbfe;
}

.cd-drw-symbol.ct {
    background: #ecfdf5;
    color: #10b981;
    border-color: #a7f3d0;
}

.cd-drw-title-meta {
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
    letter-spacing: -0.01em;
    margin: 0 !important;
}

.cd-drw-scope-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 10.5px;
    font-weight: 600;
    color: #059669;
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    padding: 1px 8px;
    border-radius: 12px;
}

.cd-drw-subtitle {
    font-size: 11px;
    color: #64748b;
    margin: 2px 0 0;
}

.cd-drw-header-right {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}

/* Header Dynamic Search Sub-Card */
.cd-drw-search-subcard {
    height: 36px;
    width: 250px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    display: flex;
    align-items: center;
    padding: 0 10px;
    gap: 8px;
    transition: all 0.2s ease;
}

.cd-drw-search-subcard:focus-within {
    background: #ffffff;
    border-color: #009ef7;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12);
}

.cd-drw-search-input {
    border: none;
    outline: none;
    background: transparent;
    font-size: 11.5px;
    font-family: inherit;
    color: #0f172a;
    width: 100%;
}

.cd-drw-search-input::placeholder {
    color: #94a3b8;
}

.cd-drw-close-btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.15s ease;
    flex-shrink: 0;
}

.cd-drw-close-btn:hover {
    background: #fee2e2;
    border-color: #fca5a5;
    color: #ef4444;
}

/* --------------------------------------------------------------------------
   PRIMARY CARD STREAM & DISCRETE SUB-CARDS
   -------------------------------------------------------------------------- */
.cd-drw-stream {
    display: flex;
    flex-direction: column;
    gap: 14px;
    padding: 16px 20px 24px 20px;
    width: 100%;
}

.cd-drw-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
    padding: 0 !important;
    overflow: hidden !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.cd-drw-card:hover {
    border-color: #cbd5e1 !important;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.07) !important;
}

.cd-drw-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 11px 18px !important;
    border-bottom: 1px solid #e2e8f0;
    margin-bottom: 0 !important;
}

.cd-drw-card-title-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.cd-drw-step-num {
    width: 24px;
    height: 24px;
    border-radius: 6px;
    font-size: 11.5px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.cd-drw-card-title {
    font-size: 13px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    margin: 0 !important;
    line-height: 1.3 !important;
}

.cd-drw-card-copy {
    font-size: 10.5px;
    color: #64748b;
    margin: 2px 0 0;
}

.cd-drw-card-body {
    padding: 14px 18px 16px 18px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* Distinct Card Header Colored Styling */
.cd-head-model {
    background: #f0f7ff !important;
    border-bottom: 1px solid #dbeafe !important;
    border-left: 4px solid #009ef7 !important;
}
.cd-head-model .cd-drw-step-num {
    background: #009ef7 !important;
    color: #ffffff !important;
}

.cd-head-experience {
    background: #f5f3ff !important;
    border-bottom: 1px solid #ede9fe !important;
    border-left: 4px solid #6366f1 !important;
}
.cd-head-experience .cd-drw-step-num {
    background: #6366f1 !important;
    color: #ffffff !important;
}

.cd-head-replacement {
    background: #ecfdf5 !important;
    border-bottom: 1px solid #d1fae5 !important;
    border-left: 4px solid #10b981 !important;
}
.cd-head-replacement .cd-drw-step-num {
    background: #10b981 !important;
    color: #ffffff !important;
}

.cd-head-invoicing {
    background: #fffbeb !important;
    border-bottom: 1px solid #fef3c7 !important;
    border-left: 4px solid #f59e0b !important;
}
.cd-head-invoicing .cd-drw-step-num {
    background: #f59e0b !important;
    color: #ffffff !important;
}

.cd-head-countries {
    background: #f0fdfa !important;
    border-bottom: 1px solid #ccfbf1 !important;
    border-left: 4px solid #06b6d4 !important;
}
.cd-head-countries .cd-drw-step-num {
    background: #06b6d4 !important;
    color: #ffffff !important;
}

/* Sub-Card: Model Option Radio Cards */
.cd-model-options-grid {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 12px !important;
}

.cd-model-card {
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 14px;
    cursor: pointer;
    background: #ffffff;
    transition: all 0.2s ease;
    display: flex;
    align-items: flex-start;
    gap: 10px;
    position: relative;
}

/* Distinct Fee Type Visual Treatment: Percentage (Blue) */
.cd-model-card[data-commercial-model="2"] {
    background: #f8fbff;
    border-color: #dbeafe;
}
.cd-model-card[data-commercial-model="2"]:hover {
    background: #f0f7ff;
    border-color: #93c5fd;
}
.cd-model-card[data-commercial-model="2"].active {
    background: #eff6ff !important;
    border-color: #009ef7 !important;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.14) !important;
}
.cd-model-card[data-commercial-model="2"] .cd-model-icon-box {
    background: #dbeafe;
    color: #0284c7;
}
.cd-model-card[data-commercial-model="2"].active .cd-model-icon-box {
    background: #009ef7;
    color: #ffffff;
}
.cd-model-card[data-commercial-model="2"].active .cd-model-radio-dot {
    border-color: #009ef7;
    background: #009ef7;
    box-shadow: inset 0 0 0 3px #ffffff;
}

/* Distinct Fee Type Visual Treatment: Fixed Rate (Indigo / Lavender) */
.cd-model-card[data-commercial-model="1"] {
    background: #faf8ff;
    border-color: #ede9fe;
}
.cd-model-card[data-commercial-model="1"]:hover {
    background: #f5f3ff;
    border-color: #c7d2fe;
}
.cd-model-card[data-commercial-model="1"].active {
    background: #f5f3ff !important;
    border-color: #6366f1 !important;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.14) !important;
}
.cd-model-card[data-commercial-model="1"] .cd-model-icon-box {
    background: #ede9fe;
    color: #6366f1;
}
.cd-model-card[data-commercial-model="1"].active .cd-model-icon-box {
    background: #6366f1;
    color: #ffffff;
}
.cd-model-card[data-commercial-model="1"].active .cd-model-radio-dot {
    border-color: #6366f1;
    background: #6366f1;
    box-shadow: inset 0 0 0 3px #ffffff;
}

/* Distinct Fee Type Visual Treatment: Free Commercial Model (Subtle Professional Pink) */
.commercial-editor-v3.is-fulltime-view .cd-model-card[data-commercial-model="0"] {
    background: #fff5f7;
    border-color: #fecdd3;
}
.commercial-editor-v3.is-fulltime-view .cd-model-card[data-commercial-model="0"]:hover {
    background: #ffe4e6;
    border-color: #fda4af;
}
.commercial-editor-v3.is-fulltime-view .cd-model-card[data-commercial-model="0"].active {
    background: #fff1f2 !important;
    border-color: #f43f5e !important;
    box-shadow: 0 0 0 3px rgba(244, 63, 94, 0.15) !important;
}
.commercial-editor-v3.is-fulltime-view .cd-model-card[data-commercial-model="0"] .cd-model-icon-box {
    background: #ffe4e6;
    color: #e11d48;
}
.commercial-editor-v3.is-fulltime-view .cd-model-card[data-commercial-model="0"].active .cd-model-icon-box {
    background: #f43f5e;
    color: #ffffff;
}
.commercial-editor-v3.is-fulltime-view .cd-model-card[data-commercial-model="0"].active .cd-model-radio-dot {
    border-color: #f43f5e;
    background: #f43f5e;
    box-shadow: inset 0 0 0 3px #ffffff;
}

/* Distinct Fee Type Visual Treatment: Zero Margin / Pass-Through (Warm Amber) */
.commercial-editor-v3.is-contract-view .cd-model-card[data-commercial-model="0"] {
    background: #fffdf5;
    border-color: #fef3c7;
}
.commercial-editor-v3.is-contract-view .cd-model-card[data-commercial-model="0"]:hover {
    background: #fffbeb;
    border-color: #fde68a;
}
.commercial-editor-v3.is-contract-view .cd-model-card[data-commercial-model="0"].active {
    background: #fffbeb !important;
    border-color: #f59e0b !important;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.14) !important;
}
.commercial-editor-v3.is-contract-view .cd-model-card[data-commercial-model="0"] .cd-model-icon-box {
    background: #fef3c7;
    color: #d97706;
}
.commercial-editor-v3.is-contract-view .cd-model-card[data-commercial-model="0"].active .cd-model-icon-box {
    background: #f59e0b;
    color: #ffffff;
}
.commercial-editor-v3.is-contract-view .cd-model-card[data-commercial-model="0"].active .cd-model-radio-dot {
    border-color: #f59e0b;
    background: #f59e0b;
    box-shadow: inset 0 0 0 3px #ffffff;
}

.cd-model-icon-box {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13.5px;
    font-weight: 700;
    flex-shrink: 0;
    transition: all 0.2s ease;
}

.cd-model-copy-wrap {
    min-width: 0;
    flex: 1 1 auto;
}

.cd-model-title {
    font-size: 12px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}

.cd-model-desc {
    font-size: 10.5px;
    color: #64748b;
    line-height: 1.35;
    margin: 2px 0 0;
}

.cd-model-radio-dot {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid #cbd5e1;
    margin-left: auto;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

/* Discrete Field Sub-Cards Grid */
.cd-field-grid {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 12px !important;
}

.cd-field-group {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 10px 12px;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.cd-field-label {
    font-size: 10.5px;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    margin: 0;
}

.cd-field-control {
    height: 34px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 0 10px;
    font-size: 12px;
    font-family: inherit;
    color: #0f172a;
    background: #ffffff;
    outline: none;
    transition: border-color 0.15s;
    width: 100%;
}

.cd-field-control:focus {
    border-color: #009ef7;
    box-shadow: 0 0 0 2px rgba(0, 158, 247, 0.12);
}

.cd-field-help {
    font-size: 10px;
    color: #64748b;
}

.cd-rate-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;
}

.cd-rate-input-wrap input {
    width: 100%;
    padding-right: 28px;
    font-variant-numeric: tabular-nums;
    font-weight: 600;
}

.cd-rate-suffix {
    position: absolute;
    right: 10px;
    font-size: 11px;
    font-weight: 700;
    color: #64748b;
    pointer-events: none;
}

/* Discrete Experience Matrix Tier Sub-Cards */
.cd-matrix-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.cd-matrix-tier-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 10px 14px;
    display: grid;
    grid-template-columns: minmax(0, 2fr) minmax(0, 1.1fr) minmax(0, 1.3fr) minmax(0, 1.4fr);
    gap: 12px;
    align-items: center;
    transition: all 0.15s;
}

.cd-matrix-tier-card:hover {
    background: #ffffff;
    border-color: #cbd5e1;
    box-shadow: 0 2px 6px rgba(0,0,0,0.03);
}

.cd-tier-identity {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
}

.cd-tier-pill {
    font-size: 10px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
    background: #eff6ff;
    color: #009ef7;
    border: 1px solid #bfdbfe;
    font-variant-numeric: tabular-nums;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.cd-tier-name {
    font-size: 11.5px;
    font-weight: 600;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Policy & Invoicing Sub-Cards */
.cd-summary-strip {
    border-radius: 8px;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 11px;
    line-height: 1.45;
}

.cd-summary-strip.strip-emerald {
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    color: #065f46;
}

.cd-summary-strip.strip-emerald .cd-svg-icon {
    color: #10b981;
}

.cd-summary-strip.strip-amber {
    background: #fffbeb;
    border: 1px solid #fde68a;
    color: #92400e;
}

.cd-summary-strip.strip-amber .cd-svg-icon {
    color: #f59e0b;
}

/* Country Coverage Sub-Cards */
.cd-country-toolbar {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 12px;
    flex-wrap: wrap;
}

.cd-country-pills-bar {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
    margin-bottom: 12px;
    min-height: 28px;
}

.cd-country-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 2px 8px;
    font-size: 10.5px;
    font-weight: 600;
    color: #0f172a;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
}

.cd-country-chip-remove {
    cursor: pointer;
    color: #94a3b8;
    font-size: 12px;
    line-height: 1;
}

.cd-country-chip-remove:hover {
    color: #ef4444;
}

.cd-country-grid {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 8px !important;
    max-height: 260px;
    overflow-y: auto;
    padding-right: 4px;
}

.cd-country-item {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.15s;
}

.cd-country-item:hover {
    background: #ffffff;
    border-color: #cbd5e1;
}

.cd-country-item.is-selected {
    background: #eff6ff;
    border-color: #bfdbfe;
}

.cd-country-code {
    font-size: 9.5px;
    font-weight: 700;
    color: #64748b;
    background: #e2e8f0;
    padding: 1px 4px;
    border-radius: 4px;
    min-width: 24px;
    text-align: center;
}

.cd-country-item.is-selected .cd-country-code {
    background: #bfdbfe;
    color: #1e40af;
}

.cd-country-name {
    font-size: 11px;
    font-weight: 500;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.cd-country-item input.countries {
    width: 15px;
    height: 15px;
    margin: 0;
    cursor: pointer;
    accent-color: #009ef7;
}

/* Action Buttons */
.cd-drw-btn {
    height: 36px;
    padding: 0 14px;
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
    font-family: inherit;
    white-space: nowrap;
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

/* Dynamic Free Mode Toggle */
.commercial-editor-v3.is-free-fee #drwSectionExperience {
    display: none !important;
}

/* Responsive adjustments for 15.6" screens (1366x768) and smaller devices */
@media (max-width: 1200px) {
    .cd-model-options-grid,
    .cd-field-grid,
    .cd-country-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
}

@media (max-width: 767px) {
    .cd-drw-header-card {
        flex-wrap: wrap !important;
        padding: 10px 14px !important;
        gap: 10px !important;
    }
    .cd-drw-header-left {
        width: 100% !important;
    }
    .cd-drw-header-right {
        width: 100% !important;
        justify-content: flex-end !important;
        flex-wrap: wrap !important;
        gap: 8px !important;
    }
    .cd-model-options-grid,
    .cd-field-grid,
    .cd-country-grid {
        grid-template-columns: 1fr !important;
    }
    .cd-matrix-tier-card {
        grid-template-columns: 1fr 1fr !important;
        gap: 8px !important;
    }
    .cd-tier-identity {
        grid-column: span 2;
    }
    .cd-drw-stream {
        padding: 12px 14px 20px 14px !important;
    }
}
</style>

<!--begin::Hiring Services Commercial Editor (Rule 20 Modern Card + Sub-Card)-->
<div id="commercial-editor-v3"
     class="commercial-editor-v3 {{ $isFulltime ? 'is-fulltime-view' : 'is-contract-view' }}"
     data-default-currency-key="{{ $tenantDefaultCurrencyKey }}"
     data-default-currency-code="{{ \App\Services\CurrencyService::resolveCurrencyCode($tenantDefaultCurrencyKey) }}"
     data-registered-countries="{{ json_encode($registeredCountryIds) }}">

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
                    <span class="cd-drw-scope-pill">
                        <svg class="cd-svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg> Direct Client Scope
                    </span>
                </div>
                <p class="cd-drw-subtitle">
                    Configure {{ $isFulltime ? 'permanent placement fee structures, seniority bands, replacement warranty, and client operational countries.' : 'contractor markup %, hourly/monthly bill rates, satisfaction trial guarantee, and client operational countries.' }}
                </p>
            </div>
        </div>

        <div class="cd-drw-header-right">
            <button type="button" class="cd-drw-btn" data-commercial-cancel>
                Cancel
            </button>
            <button type="button" class="cd-drw-btn" data-commercial-reset title="Reset to saved values">
                <svg class="cd-svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Reset
            </button>
            <button type="button"
                    id="form-submit"
                    class="btn btn-primary fw-bold copy_current_selected_applicant cd-drw-btn btn-primary"
                    aria-live="polite">
                <span class="indicator-label">
                    <svg class="cd-svg-icon me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Save Commercial Terms
                </span>
                <span class="indicator-progress">
                    <span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>
                    Saving Commercials...
                </span>
            </button>
            <button type="button"
                    class="cd-drw-close-btn"
                    aria-label="Close drawer"
                    title="Close drawer"
                    onclick="if (window.KTDrawer && KTDrawer.getInstance(document.getElementById('kt_activitiesJob'))) { KTDrawer.getInstance(document.getElementById('kt_activitiesJob')).hide(); } else { $('#kt_activitiesJob > .card > #kt_activities_header #kt_activitiesJob_close').trigger('click'); }">
                <svg class="cd-svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </header>

    <!-- Hidden State Inputs for Controller Action Binding -->
    <input class="main_status" type="hidden" value="{{ $isServiceActive ? 1 : 0 }}">
    <input class="main_status" type="checkbox" checked style="display:none;" value="{{ $isServiceActive ? 1 : 0 }}">
    <input class="rateTypeClass" type="hidden" value="{{ $rateType }}">
    <input class="rateTypeClass" type="radio" checked style="display:none;" value="{{ $rateType }}">
    <input class="updateId" type="hidden" value="{{ $commercial['id'] ?? '' }}">
    <input class="commType" type="hidden" value="{{ $sectionCheck }}">

    <!-- ====================================================================
         TIER 3: PRIMARY CARDS STREAM & DISCRETE SUB-CARDS
         ==================================================================== -->
    <main class="cd-drw-stream">

        <!-- ----------------------------------------------------------------
             PRIMARY CARD 1: COMMERCIAL FEE MODEL & DEFAULT BASE PRICING
             ---------------------------------------------------------------- -->
        <section class="cd-drw-card" id="drwSectionModel" data-section-category="model">
            <div class="cd-drw-card-head cd-head-model">
                <div class="cd-drw-card-title-group">
                    <span class="cd-drw-step-num">1</span>
                    <div>
                        <h4 class="cd-drw-card-title">{{ $isFulltime ? 'Commercial Placement Model & Base Fee' : 'Contract Staffing Model & Billing Rates' }}</h4>
                        <p class="cd-drw-card-copy">{{ $isFulltime ? 'Select placement fee methodology and default baseline compensation for permanent hires.' : 'Select contractor markup or direct bill rate methodology and default billing cadence.' }}</p>
                    </div>
                </div>
            </div>

            <div class="cd-drw-card-body">
                <input type="hidden" data-commercial-type value="{{ $selectedCommercialModel }}">

                <!-- Discrete Sub-Cards: Model Selection Radios -->
                <div class="cd-model-options-grid" role="radiogroup" aria-label="Commercial fee model">
                    <div class="cd-model-card {{ $selectedCommercialModel === 2 ? 'active' : '' }}"
                         data-commercial-model="2"
                         role="radio"
                         aria-checked="{{ $selectedCommercialModel === 2 ? 'true' : 'false' }}">
                        <div class="cd-model-icon-box">%</div>
                        <div class="cd-model-copy-wrap">
                            <h5 class="cd-model-title">{{ $isFulltime ? 'Percentage of CTC' : 'Markup on Pay Rate (%)' }}</h5>
                            <p class="cd-model-desc">{{ $isFulltime ? 'Standard placement fee calculated as % of candidate annual compensation (CTC).' : 'Charge an agency percentage markup on top of candidate base hourly or monthly wage.' }}</p>
                        </div>
                        <span class="cd-model-radio-dot"></span>
                    </div>

                    <div class="cd-model-card {{ $selectedCommercialModel === 1 ? 'active' : '' }}"
                         data-commercial-model="1"
                         role="radio"
                         aria-checked="{{ $selectedCommercialModel === 1 ? 'true' : 'false' }}">
                        <div class="cd-model-icon-box">{{ $isFulltime ? '₹' : 'Fix' }}</div>
                        <div class="cd-model-copy-wrap">
                            <h5 class="cd-model-title">{{ $isFulltime ? 'Fixed Placement Fee' : 'Fixed Contract Bill Rate' }}</h5>
                            <p class="cd-model-desc">{{ $isFulltime ? 'Charge a flat fixed commercial recruitment fee for every successful candidate joining.' : 'Charge an agreed all-inclusive fixed bill rate per hour or per month directly to client.' }}</p>
                        </div>
                        <span class="cd-model-radio-dot"></span>
                    </div>

                    <div class="cd-model-card {{ $selectedCommercialModel === 0 ? 'active' : '' }}"
                         data-commercial-model="0"
                         role="radio"
                         aria-checked="{{ $selectedCommercialModel === 0 ? 'true' : 'false' }}">
                        <div class="cd-model-icon-box">◇</div>
                        <div class="cd-model-copy-wrap">
                            <h5 class="cd-model-title">{{ $isFulltime ? 'No Fee Applicable (Free Placement)' : 'Zero Margin (Pass-Through)' }}</h5>
                            <p class="cd-model-desc">{{ $isFulltime ? 'Zero commercial placement fee charged to client relationships. Fully complimentary hiring.' : 'Contractor wages passed through at direct cost with zero agency staffing margin.' }}</p>
                        </div>
                        <span class="cd-model-radio-dot"></span>
                    </div>
                </div>

                <!-- Discrete Sub-Cards: Base Configuration Grid -->
                <div class="cd-field-grid">
                    <!-- Sub-Card: Multi-Tenant Currency -->
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

                    <!-- Sub-Card: Default Rate / Fixed Amount -->
                    <div class="cd-field-group">
                        <label class="cd-field-label" for="commercial_default_rate" data-rate-label>
                            {{ $selectedCommercialModel === 2 ? ($isFulltime ? 'Default CTC Percentage' : 'Default Markup Percentage') : ($isFulltime ? 'Fixed Placement Fee' : 'Default Contract Bill Rate') }}
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
                            {{ $selectedCommercialModel === 2 ? ($isFulltime ? 'Changing this rate updates all experience ranges.' : 'Default markup % applied across experience tiers.') : ($isFulltime ? 'Default fixed amount applied per successful hire.' : 'Default billing rate per cycle for contingent consultants.') }}
                        </span>
                    </div>

                    <!-- Sub-Card: Billing Basis -->
                    <div class="cd-field-group">
                        <label class="cd-field-label" for="commercial_salary_mode" data-applied-on-label>
                            {{ $isFulltime ? ($selectedCommercialModel === 1 ? 'Billing Basis' : ($selectedCommercialModel === 0 ? 'Fee Basis' : 'Applied On')) : 'Billing Basis' }}
                        </label>
                        <select id="commercial_salary_mode" name="sal_mode" class="cd-field-control" data-applied-on>
                            @if($isFulltime)
                                @if($selectedCommercialModel === 1)
                                    <option value="3" selected>Per Hire</option>
                                @elseif($selectedCommercialModel === 0)
                                    <option value="0" selected>Not Applicable</option>
                                @else
                                    <option value="0" selected>Annual CTC</option>
                                @endif
                            @else
                                @foreach(($salaryMode ?? []) as $modeKey => $modeLabel)
                                    <option value="{{ $modeKey }}" {{ $selectedSalaryMode === (int) $modeKey ? 'selected' : '' }}>
                                        {{ $modeLabel }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <span class="cd-field-help" data-applied-on-help>
                            {{ $isFulltime ? ($selectedCommercialModel === 1 ? 'Fixed fee charged per successful hire' : ($selectedCommercialModel === 0 ? 'No fee basis required' : 'Commercial placement billing basis')) : 'Contract staffing billing cadence' }}
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ----------------------------------------------------------------
             PRIMARY CARD 2: EXPERIENCE-BASED PRICING MATRIX
             ---------------------------------------------------------------- -->
        <section class="cd-drw-card" id="drwSectionExperience" data-section-category="experience">
            <div class="cd-drw-card-head cd-head-experience">
                <div class="cd-drw-card-title-group">
                    <span class="cd-drw-step-num">2</span>
                    <div>
                        <h4 class="cd-drw-card-title">{{ $isFulltime ? 'Seniority-Based Placement Fee Matrix' : 'Contractor Experience Rate Card' }}</h4>
                        <p class="cd-drw-card-copy" data-experience-pricing-copy>
                            {{ $selectedCommercialModel === 1 ? ($isFulltime ? 'Define different fixed placement fees for different experience bands.' : 'Define different fixed bill rates for different contractor experience bands.') : ($isFulltime ? 'Discrete tiered sub-cards allow bespoke placement pricing across seniority bands.' : 'Discrete tiered sub-cards allow bespoke contractor markup or bill rates.') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="cd-drw-card-body">
                <!-- Discrete Sub-Cards Stream: Seniority Bands -->
                <div class="cd-matrix-list">
                    @foreach($experienceRanges as $range)
                        @php
                            $firstExperienceIndex = $range['indexes'][0];
                            $rangeRate = data_get($commercialDetails->get($firstExperienceIndex), 'rate', $defaultRate);
                            if ($isFulltime && $selectedCommercialModel === 1 && ((float) $rangeRate <= 100.0 || (float) $rangeRate === 8.33)) {
                                $rangeRate = 10000.00;
                            }
                        @endphp
                        <div class="cd-matrix-tier-card" data-experience-range>
                            <div class="cd-tier-identity">
                                <span class="cd-tier-pill">
                                    <svg class="cd-svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $range['label'] }}
                                </span>
                                <span class="cd-tier-name">
                                    {{ $loop->first ? ($isFulltime ? 'Entry / Associate' : 'Junior Consultant') : ($loop->index === 1 ? ($isFulltime ? 'Mid-Level Specialist' : 'Mid-Level Consultant') : ($loop->index === 2 ? ($isFulltime ? 'Senior Professional' : 'Senior Specialist') : ($isFulltime ? 'Leadership / Executive' : 'Principal / Lead Consultant'))) }}
                                </span>
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
                                <select class="cd-field-control" data-range-applied-on>
                                    @if($isFulltime)
                                        @if($selectedCommercialModel === 1)
                                            <option value="3" selected>Per Hire</option>
                                        @elseif($selectedCommercialModel === 0)
                                            <option value="0" selected>Not Applicable</option>
                                        @else
                                            <option value="0" selected>Annual CTC</option>
                                        @endif
                                    @else
                                        @foreach(($salaryMode ?? []) as $modeKey => $modeLabel)
                                            <option value="{{ $modeKey }}" {{ $selectedSalaryMode === (int) $modeKey ? 'selected' : '' }}>
                                                {{ $modeLabel }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <!-- Preserved Hidden Persistence Inputs -->
                            @foreach($range['indexes'] as $experienceIndex)
                                @php
                                    $experienceRate = data_get($commercialDetails->get($experienceIndex), 'rate', $defaultRate);
                                    if ($isFulltime && $selectedCommercialModel === 1 && ((float) $experienceRate <= 100.0 || (float) $experienceRate === 8.33)) {
                                        $experienceRate = 10000.00;
                                    }
                                @endphp
                                <input type="hidden"
                                       class="{{ $isFulltime ? 'perecentage_val' : 'rate_val' }}"
                                       name="{{ $isFulltime ? 'perecentage[]' : 'rate[]' }}"
                                       data-experience-index="{{ $experienceIndex }}"
                                       value="{{ $experienceRate }}" />
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- ----------------------------------------------------------------
             PRIMARY CARD 3: REPLACEMENT / SATISFACTION TRIAL GUARANTEE POLICY
             ---------------------------------------------------------------- -->
        <section class="cd-drw-card" id="drwSectionReplacement" data-section-category="policy">
            <div class="cd-drw-card-head cd-head-replacement">
                <div class="cd-drw-card-title-group">
                    <span class="cd-drw-step-num">3</span>
                    <div>
                        <h4 class="cd-drw-card-title">{{ $isFulltime ? 'Replacement Guarantee Policy' : 'Satisfaction Trial & Guarantee Policy' }}</h4>
                        <p class="cd-drw-card-copy">{{ $isFulltime ? 'Warranty clauses, candidate separation eligibility, and replacement fulfillment rules.' : 'Client satisfaction trial window, contractor roll-off contingency, and backfill SLA.' }}</p>
                    </div>
                </div>
            </div>

            <div class="cd-drw-card-body">
                <!-- Discrete Sub-Cards Grid: 3 Sub-Cards for Policy -->
                <div class="cd-field-grid">
                    <div class="cd-field-group">
                        <label class="cd-field-label" for="commercial_replacement_period">{{ $isFulltime ? 'Guarantee Window' : 'Trial & Notice Window' }}</label>
                        <select id="commercial_replacement_period" class="cd-field-control" data-replacement-period>
                            @foreach(($replacementPeriods ?? []) as $periodValue => $periodLabel)
                                <option value="{{ $periodValue }}" {{ $selectedReplacementPeriod === (int) $periodValue ? 'selected' : '' }}>
                                    {{ $periodLabel }}
                                </option>
                            @endforeach
                        </select>
                        <span class="cd-field-help">{{ $isFulltime ? 'Warranty window for replacement' : 'Trial period or roll-off notice' }}</span>
                    </div>

                    <div class="cd-field-group">
                        <label class="cd-field-label" for="commercial_replacement_type">{{ $isFulltime ? 'Remedy Clause' : 'Staffing Remedy Clause' }}</label>
                        <select id="commercial_replacement_type" class="cd-field-control" data-replacement-type>
                            @foreach(($replacementTypes ?? []) as $typeValue => $typeLabel)
                                <option value="{{ $typeValue }}" {{ $selectedReplacementType === (string) $typeValue ? 'selected' : '' }}>
                                    {{ $typeLabel }}
                                </option>
                            @endforeach
                        </select>
                        <span class="cd-field-help">{{ $isFulltime ? 'Remedy offered to the client' : 'Client satisfaction remedy' }}</span>
                    </div>

                    <div class="cd-field-group">
                        <label class="cd-field-label" for="commercial_replacement_eligibility">{{ $isFulltime ? 'Separation Eligibility' : 'SLA & Contingency' }}</label>
                        <select id="commercial_replacement_eligibility" class="cd-field-control" data-replacement-eligibility>
                            @foreach(($replacementEligibility ?? []) as $eligibilityValue => $eligibilityLabel)
                                <option value="{{ $eligibilityValue }}" {{ $selectedReplacementEligibility === (string) $eligibilityValue ? 'selected' : '' }}>
                                    {{ $eligibilityLabel }}
                                </option>
                            @endforeach
                        </select>
                        <span class="cd-field-help">{{ $isFulltime ? 'Trigger conditions for warranty' : 'Service level agreement rule' }}</span>
                    </div>
                </div>

                <!-- Authoritative Human Summary Strip for Replacement Policy -->
                <div class="cd-summary-strip strip-emerald">
                    <svg class="cd-svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    @if($isFulltime)
                        <span>A <strong data-replacement-summary>{{ strtolower($replacementTypes[$selectedReplacementType] ?? 'free replacement candidate') }}</strong> guarantee applies if candidate departs within <strong data-replacement-days>{{ $selectedReplacementPeriod }}</strong> days of joining (<span data-eligibility-summary>{{ strtolower($replacementEligibility[$selectedReplacementEligibility] ?? 'all standard separations') }}</span>).</span>
                    @else
                        <span>A <strong data-replacement-summary>{{ strtolower($replacementTypes[$selectedReplacementType] ?? 'satisfaction trial guarantee') }}</strong> (<strong data-replacement-days>{{ $selectedReplacementPeriod }}</strong> days) with <strong data-eligibility-summary>{{ strtolower($replacementEligibility[$selectedReplacementEligibility] ?? '48h backfill sla') }}</strong> applies.</span>
                    @endif
                </div>
            </div>
        </section>

        <!-- ----------------------------------------------------------------
             PRIMARY CARD 4: INVOICING CADENCE & PAYMENT TERMS
             ---------------------------------------------------------------- -->
        <section class="cd-drw-card" id="drwSectionInvoicing" data-section-category="invoicing">
            <div class="cd-drw-card-head cd-head-invoicing">
                <div class="cd-drw-card-title-group">
                    <span class="cd-drw-step-num">4</span>
                    <div>
                        <h4 class="cd-drw-card-title">{{ $isFulltime ? 'Billing Trigger & Payment Terms' : 'Timesheet Invoicing Cadence & Payment Terms' }}</h4>
                        <p class="cd-drw-card-copy">{{ $isFulltime ? 'Placement fee invoice milestone, billing schedule, and client settlement credit terms.' : 'Contingent timesheet billing trigger, recurring invoice cadence, and settlement credit terms.' }}</p>
                    </div>
                </div>
            </div>

            <div class="cd-drw-card-body">
                <!-- Discrete Sub-Cards Grid: 3 Sub-Cards for Invoicing & Payment Terms -->
                <div class="cd-field-grid">
                    <div class="cd-field-group">
                        <label class="cd-field-label" for="commercial_billing_trigger">Invoice Trigger Event</label>
                        <select id="commercial_billing_trigger" class="cd-field-control" data-billing-trigger>
                            @foreach(($billingTriggers ?? []) as $triggerValue => $triggerLabel)
                                <option value="{{ $triggerValue }}" {{ $selectedBillingTrigger === (string) $triggerValue ? 'selected' : '' }}>
                                    {{ $triggerLabel }}
                                </option>
                            @endforeach
                        </select>
                        <span class="cd-field-help">{{ $isFulltime ? 'Event that initiates the invoice' : 'Timesheet milestone trigger' }}</span>
                    </div>

                    <div class="cd-field-group">
                        <label class="cd-field-label" for="commercial_billing_frequency">Invoicing Cadence</label>
                        <select id="commercial_billing_frequency" class="cd-field-control" data-billing-frequency>
                            @foreach(($billingFrequencies ?? []) as $frequencyValue => $frequencyLabel)
                                <option value="{{ $frequencyValue }}" {{ $selectedBillingFrequency === (string) $frequencyValue ? 'selected' : '' }}>
                                    {{ $frequencyLabel }}
                                </option>
                            @endforeach
                        </select>
                        <span class="cd-field-help">{{ $isFulltime ? 'One-time invoice schedule' : 'Recurring timesheet cycle' }}</span>
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

                <!-- Authoritative Human Summary Strip for Invoicing & Terms -->
                <div class="cd-summary-strip strip-amber">
                    <svg class="cd-svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                    @if($isFulltime)
                        <span>Invoices are triggered on <strong data-trigger-summary>{{ strtolower($billingTriggers[$selectedBillingTrigger] ?? 'candidate joined') }}</strong> with <strong data-frequency-summary>{{ strtolower($billingFrequencies[$selectedBillingFrequency] ?? 'one-time upon placement') }}</strong> cadence and <strong data-terms-summary>{{ $paymentTerms[$selectedPaymentTerms] ?? 'Net 30 Days' }}</strong> credit window.</span>
                    @else
                        <span>Invoices are generated on <strong data-trigger-summary>{{ strtolower($billingTriggers[$selectedBillingTrigger] ?? 'approved timesheet') }}</strong> on a <strong data-frequency-summary>{{ strtolower($billingFrequencies[$selectedBillingFrequency] ?? 'monthly billing cycle') }}</strong> with <strong data-terms-summary>{{ $paymentTerms[$selectedPaymentTerms] ?? 'Net 30 Days' }}</strong> settlement terms.</span>
                    @endif
                </div>
            </div>
        </section>

        <!-- ----------------------------------------------------------------
             PRIMARY CARD 5: OPERATIONAL COUNTRIES & GEOGRAPHIC COVERAGE
             ---------------------------------------------------------------- -->
        <section class="cd-drw-card" id="drwSectionCountries" data-section-category="countries">
            <div class="cd-drw-card-head cd-head-countries">
                <div class="cd-drw-card-title-group">
                    <span class="cd-drw-step-num">5</span>
                    <div>
                        <h4 class="cd-drw-card-title">Operational Countries & Geographic Scope</h4>
                        <p class="cd-drw-card-copy">Select the countries where your organization offers {{ $isFulltime ? 'fulltime permanent placement' : 'contract staffing' }} services.</p>
                    </div>
                </div>
                <div class="cd-drw-scope-pill">
                    <svg class="cd-svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span id="countrySelectedHeaderBadge">{{ $activeCountryCount }} Countries Enabled</span>
                </div>
            </div>

            <div class="cd-drw-card-body">
                <!-- Toolbar Sub-Card -->
                <div class="cd-country-toolbar">
                    <div class="cd-drw-search-subcard" style="width: 280px;">
                        <svg class="cd-svg-icon text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="search"
                               class="cd-drw-search-input"
                               placeholder="Search country by name or ISO..."
                               data-country-search />
                    </div>
                    <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                        <button type="button" class="cd-drw-btn" data-country-top title="Quick select top global hiring hubs">
                            ⭐ Top Hubs
                        </button>
                        <button type="button" class="cd-drw-btn" data-country-select-all>
                            ✓ Select All
                        </button>
                        <button type="button" class="cd-drw-btn" data-country-clear>
                            ✕ Clear All
                        </button>
                    </div>
                </div>

                <!-- Selected Countries Active Chips -->
                <div class="cd-country-pills-bar" data-selected-countries></div>

                <!-- Country Selection Grid (3-Column Discrete Grid) -->
                <div class="cd-country-grid" data-country-grid>
                    @foreach(($countries ?? []) as $country)
                        @php
                            $countryId = (int) $country->id;
                            $countryRegion = strtolower(trim((string) ($country->region ?? '')) ?: 'other');
                            $countryCode = strtoupper(trim((string) ($country->iso2 ?? $country->country_code ?? '')));
                            $isChecked = in_array($countryId, $selectedCountryIds, true);
                        @endphp
                        <div class="cd-country-item {{ $isChecked ? 'is-selected' : '' }}"
                             data-country-item
                             data-name="{{ strtolower(trim($countryCode . ' ' . $country->country_name)) }}"
                             data-region="{{ $countryRegion }}">
                            <input class="countries"
                                   type="checkbox"
                                   id="commercial_country_{{ $sectionCheck }}_{{ $countryId }}"
                                   value="{{ $countryId }}"
                                   data-country-name="{{ $country->country_name }}"
                                   data-country-code="{{ $countryCode }}"
                                   data-initial="{{ $isChecked ? '1' : '0' }}"
                                   {{ $isChecked ? 'checked' : '' }} />
                            <span class="cd-country-code">{{ $countryCode ?: '—' }}</span>
                            <label class="cd-country-name" for="commercial_country_{{ $sectionCheck }}_{{ $countryId }}">
                                {{ $country->country_name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

    </main>

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
    var paymentTerms = root.querySelector('[data-payment-terms]');
    var mainStatusInput = root.querySelector('.main_status');
    var isFT = (root.querySelector('.commType') ? root.querySelector('.commType').value === '2' : false);
    var initialState;

    function updateCommercialType() {
        var modelValue = commercialType.value;
        var isPercentage = modelValue === '2';
        var isFixedFee = modelValue === '1';
        var isFree = modelValue === '0';
        var wasFree = defaultRateInput.disabled;
        var supportedServiceType = isFT ? '2' : '1';

        root.querySelectorAll('.rateTypeClass').forEach(function (el) {
            el.value = modelValue;
        });

        root.querySelectorAll('[data-commercial-model]').forEach(function (option) {
            var active = option.dataset.commercialModel === modelValue;
            option.classList.toggle('active', active);
            option.setAttribute('aria-checked', active ? 'true' : 'false');
        });

        root.classList.toggle('is-fixed-fee', isFixedFee);
        root.classList.toggle('is-free-fee', isFree);

        if (isFree) {
            defaultRateInput.value = '0';
            root.querySelectorAll('.rate_val,.perecentage_val').forEach(function (rate) {
                rate.value = '0';
            });
            root.querySelectorAll('[data-range-rate]').forEach(function (rangeRate) {
                rangeRate.value = '0';
            });
            defaultRateInput.dispatchEvent(new Event('input'));
        } else if (isFixedFee && isFT) {
            var currRate = Number(defaultRateInput.value);
            if (!currRate || currRate <= 100 || currRate === 8.33) {
                defaultRateInput.value = '10000';
                defaultRateInput.dispatchEvent(new Event('input'));
            }
            root.querySelectorAll('[data-experience-range]').forEach(function (rangeRow) {
                var rangeRate = rangeRow.querySelector('[data-range-rate]');
                var numRate = Number(rangeRate ? rangeRate.value : 0);
                if (!numRate || numRate <= 100 || numRate === 8.33) {
                    if (rangeRate) {
                        rangeRate.value = defaultRateInput.value || '10000';
                        rangeRate.dispatchEvent(new Event('input'));
                    }
                }
            });
        } else if (isPercentage && isFT) {
            var currRate = Number(defaultRateInput.value);
            if (!currRate || currRate > 100 || currRate === 10000) {
                defaultRateInput.value = defaultRateInput.dataset.fallbackRate || '8.33';
                defaultRateInput.dispatchEvent(new Event('input'));
            }
        } else if (wasFree && Number(defaultRateInput.value) === 0) {
            defaultRateInput.value = defaultRateInput.dataset.fallbackRate || (isFT ? '8.33' : '0');
            defaultRateInput.dispatchEvent(new Event('input'));
        }

        defaultRateInput.disabled = isFree;
        appliedOn.disabled = isFree;
        pricingCurrency.disabled = isFree;

        root.querySelectorAll('[data-range-rate],[data-range-applied-on],[data-range-currency]').forEach(function (control) {
            control.disabled = isFree;
        });

        root.querySelectorAll('[data-rate-suffix]').forEach(function (suffix) {
            suffix.textContent = isPercentage ? '%' : '';
        });

        var appliedOnLabel = root.querySelector('[data-applied-on-label]');
        var appliedOnHelp = root.querySelector('[data-applied-on-help]');
        if (isFT) {
            if (isFixedFee) {
                appliedOn.innerHTML = '<option value="3" selected>Per Hire</option>';
                appliedOn.value = '3';
                root.querySelectorAll('[data-range-applied-on]').forEach(function (sel) {
                    sel.innerHTML = '<option value="3" selected>Per Hire</option>';
                    sel.value = '3';
                });
                if (appliedOnLabel) appliedOnLabel.textContent = 'Billing Basis';
                if (appliedOnHelp) appliedOnHelp.textContent = 'Fixed fee charged per successful hire';
            } else if (isPercentage) {
                appliedOn.innerHTML = '<option value="0" selected>Annual CTC</option>';
                appliedOn.value = '0';
                root.querySelectorAll('[data-range-applied-on]').forEach(function (sel) {
                    sel.innerHTML = '<option value="0" selected>Annual CTC</option>';
                    sel.value = '0';
                });
                if (appliedOnLabel) appliedOnLabel.textContent = 'Applied On';
                if (appliedOnHelp) appliedOnHelp.textContent = 'Commercial placement billing basis';
            } else if (isFree) {
                appliedOn.innerHTML = '<option value="0" selected>Not Applicable</option>';
                appliedOn.value = '0';
                root.querySelectorAll('[data-range-applied-on]').forEach(function (sel) {
                    sel.innerHTML = '<option value="0" selected>Not Applicable</option>';
                    sel.value = '0';
                });
                if (appliedOnLabel) appliedOnLabel.textContent = 'Fee Basis';
                if (appliedOnHelp) appliedOnHelp.textContent = 'No fee basis required';
            }
        }

        var rateLabelEl = root.querySelector('[data-rate-label]');
        if (rateLabelEl) {
            rateLabelEl.textContent = isFree
                ? (isFT ? 'No Placement Fee' : 'Pass-Through')
                : (isPercentage ? (isFT ? 'Default CTC Percentage' : 'Default Markup Percentage') : (isFT ? 'Fixed Placement Fee' : 'Default Contract Bill Rate'));
        }

        var rateHelpEl = root.querySelector('[data-rate-help]');
        if (rateHelpEl) {
            rateHelpEl.textContent = isFree
                ? (isFT ? 'No commercial placement fee is charged.' : 'Pass-through wages with zero margin.')
                : (isPercentage ? (isFT ? 'Changing this rate updates all experience tiers.' : 'Default markup % applied across experience tiers.') : (isFT ? 'Default fixed amount applied per successful hire.' : 'Default billing rate per cycle for contingent consultants.'));
        }

        var defaultPricingCopyEl = root.querySelector('[data-default-pricing-copy]');
        if (defaultPricingCopyEl) {
            defaultPricingCopyEl.textContent = isFree
                ? 'No charge will be applied for this service.'
                : (isPercentage ? (isFT ? 'Set the default percentage fee for permanent placements.' : 'Set the default markup percentage on contractor pay.') : (isFT ? 'Set the default fixed placement fee per successful hire.' : 'Set the default contract bill rate per cycle.'));
        }

        var expPricingCopyEl = root.querySelector('[data-experience-pricing-copy]');
        if (expPricingCopyEl) {
            expPricingCopyEl.textContent = isFree
                ? 'No per-range fee is required for this service.'
                : (isPercentage ? (isFT ? 'Discrete tiered sub-cards allow bespoke placement pricing across seniority bands.' : 'Discrete tiered sub-cards allow bespoke markup % across contractor experience levels.') : (isFT ? 'Define different fixed placement fees for different seniority bands.' : 'Define different contract bill rates for different experience bands.'));
        }
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
            mainStatus: mainStatusInput ? mainStatusInput.value : '1',
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

        if (mainStatusInput) {
            mainStatusInput.value = initialState.mainStatus;
            root.querySelectorAll('.main_status').forEach(function (el) {
                if (el.type === 'checkbox') {
                    el.checked = String(initialState.mainStatus) === '1';
                } else {
                    el.value = initialState.mainStatus;
                }
            });
        }

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
            var parentItem = country.closest('[data-country-item]');
            if (parentItem) parentItem.classList.toggle('is-selected', country.checked);
        });

        if (search) {
            search.value = '';
            filterCountries();
        }

        syncRangeSelects('[data-range-applied-on]', appliedOn.value);
        syncRangeSelects('[data-range-currency]', pricingCurrency.value);

        updateCommercialType();
        updateReplacementSummary();
        renderSelectedCountries();

        // Immediate tactile rotation feedback on Reset button
        var resetBtn = root.querySelector('[data-commercial-reset]');
        if (resetBtn) {
            var svgIcon = resetBtn.querySelector('svg');
            if (svgIcon) {
                svgIcon.style.transition = 'transform 0.4s ease';
                svgIcon.style.transform = 'rotate(-360deg)';
                window.setTimeout(function () {
                    svgIcon.style.transition = 'none';
                    svgIcon.style.transform = 'none';
                }, 400);
            }
        }

        // Informative notification confirming reset to saved state
        if (typeof toastr !== 'undefined') {
            toastr.info('Commercial terms reset to saved values.');
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'info',
                title: 'Commercial terms reset to saved values.',
                showConfirmButton: false,
                timer: 2000
            });
        }
    }

    function updateReplacementSummary() {
        var daysEl = root.querySelector('[data-replacement-days]');
        var summaryEl = root.querySelector('[data-replacement-summary]');
        var eligibilityEl = root.querySelector('[data-eligibility-summary]');
        var triggerSummaryEl = root.querySelector('[data-trigger-summary]');
        var frequencySummaryEl = root.querySelector('[data-frequency-summary]');
        var termsSummaryEl = root.querySelector('[data-terms-summary]');

        if (daysEl) daysEl.textContent = replacementPeriod.value;
        if (summaryEl && replacementType.options[replacementType.selectedIndex]) {
            summaryEl.textContent = replacementType.options[replacementType.selectedIndex].text.toLowerCase();
        }
        if (eligibilityEl && replacementEligibility.options[replacementEligibility.selectedIndex]) {
            eligibilityEl.textContent = replacementEligibility.options[replacementEligibility.selectedIndex].text.toLowerCase();
        }
        if (triggerSummaryEl && billingTrigger.options[billingTrigger.selectedIndex]) {
            triggerSummaryEl.textContent = billingTrigger.options[billingTrigger.selectedIndex].text.toLowerCase();
        }
        if (frequencySummaryEl && billingFrequency.options[billingFrequency.selectedIndex]) {
            frequencySummaryEl.textContent = billingFrequency.options[billingFrequency.selectedIndex].text.toLowerCase();
        }
        if (termsSummaryEl && paymentTerms.options[paymentTerms.selectedIndex]) {
            termsSummaryEl.textContent = paymentTerms.options[paymentTerms.selectedIndex].text;
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
    });

    appliedOn.addEventListener('change', function () { syncRangeSelects('[data-range-applied-on]', appliedOn.value); });
    pricingCurrency.addEventListener('change', function () { syncRangeSelects('[data-range-currency]', pricingCurrency.value); });
    billingTrigger.addEventListener('change', updateReplacementSummary);
    billingFrequency.addEventListener('change', updateReplacementSummary);
    replacementPeriod.addEventListener('change', updateReplacementSummary);
    replacementType.addEventListener('change', updateReplacementSummary);
    replacementEligibility.addEventListener('change', updateReplacementSummary);
    paymentTerms.addEventListener('change', updateReplacementSummary);

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

    // Country selection & active chips rendering
    function renderSelectedCountries() {
        var selected = countries.filter(function (country) { return country.checked; });
        if (!selectedHost) return;

        selectedHost.innerHTML = '';
        selected.slice(0, 10).forEach(function (country) {
            var chip = document.createElement('span');
            chip.className = 'cd-country-chip';
            chip.innerHTML = '<span class="cd-country-code"></span><span></span><span class="cd-country-chip-remove" title="Remove country">×</span>';
            chip.children[0].textContent = country.dataset.countryCode || '—';
            chip.children[1].textContent = country.dataset.countryName;
            chip.querySelector('.cd-country-chip-remove').addEventListener('click', function () {
                country.checked = false;
                var pItem = country.closest('[data-country-item]');
                if (pItem) pItem.classList.remove('is-selected');
                renderSelectedCountries();
            });
            selectedHost.appendChild(chip);
        });

        if (selected.length > 10) {
            var more = document.createElement('span');
            more.className = 'cd-country-chip';
            more.style.background = '#eff6ff';
            more.style.borderColor = '#bfdbfe';
            more.style.color = '#009ef7';
            more.textContent = '+ ' + (selected.length - 10) + ' more';
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
        if (headerBadge) headerBadge.textContent = selected.length + (selected.length === 1 ? ' Country Enabled' : ' Countries Enabled');
    }

    function filterCountries() {
        var term = (search.value || '').trim().toLowerCase();
        countryItems.forEach(function (item) {
            var matchesSearch = (!term || item.dataset.name.indexOf(term) !== -1);
            item.hidden = !matchesSearch;
        });
    }

    countries.forEach(function (country) {
        country.addEventListener('change', function () {
            var parentItem = country.closest('[data-country-item]');
            if (parentItem) parentItem.classList.toggle('is-selected', country.checked);
            renderSelectedCountries();
        });
    });

    if (search) {
        search.addEventListener('input', filterCountries);
    }

    root.querySelectorAll('[data-country-select-all]').forEach(function (button) {
        button.addEventListener('click', function () {
            countryItems.filter(function (item) { return !item.hidden; }).forEach(function (item) {
                var chk = item.querySelector('.countries');
                if (chk) {
                    chk.checked = true;
                    item.classList.add('is-selected');
                }
            });
            renderSelectedCountries();
        });
    });

    root.querySelectorAll('[data-country-clear]').forEach(function (button) {
        button.addEventListener('click', function () {
            countries.forEach(function (country) {
                country.checked = false;
                var pItem = country.closest('[data-country-item]');
                if (pItem) pItem.classList.remove('is-selected');
            });
            renderSelectedCountries();
        });
    });

    var topBtn = root.querySelector('[data-country-top]');
    if (topBtn) {
        topBtn.addEventListener('click', function () {
            var topCountries = ['india', 'united states', 'australia', 'united kingdom', 'united arab emirates', 'singapore', 'germany', 'canada'];
            countries.forEach(function (country) {
                var cName = (country.dataset.countryName || '').toLowerCase();
                var shouldCheck = (topCountries.indexOf(cName) !== -1);
                country.checked = shouldCheck;
                var pItem = country.closest('[data-country-item]');
                if (pItem) pItem.classList.toggle('is-selected', shouldCheck);
            });
            renderSelectedCountries();
        });
    }

    root.querySelectorAll('[data-commercial-reset]').forEach(function (button) {
        button.addEventListener('click', restoreInitialState);
    });

    // ── Self-Contained Resilient Save Commercial Terms Handler (Rule 17) ──
    var saveBtn = root.querySelector('#form-submit');
    if (saveBtn) {
        saveBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            if (saveBtn.classList.contains('is-saving') || saveBtn.disabled) {
                return;
            }

            var token = '{{ csrf_token() }}';
            var saveUrl = '{{ route("service_company.jobs.addUpdateJobServices") }}';
            var commTypeVal = root.querySelector('.commType') ? root.querySelector('.commType').value : '{{ $sectionCheck }}';
            var updateIdVal = root.querySelector('.updateId') ? root.querySelector('.updateId').value : '';
            var modelVal = commercialType ? commercialType.value : '2';

            var arrCountry = [];
            countries.forEach(function (chk) {
                if (chk.checked) arrCountry.push(chk.value);
            });

            var rateVal = [];
            var rateInputs = root.querySelectorAll(String(commTypeVal) === '2' ? '.perecentage_val' : '.rate_val');
            rateInputs.forEach(function (inp) {
                rateVal.push(inp.value);
            });

            // Free commercial model forces zero rates
            if (String(modelVal) === '0') {
                rateVal = rateVal.map(function () { return '0'; });
            }

            // Normalize salary mode for fixed placement fee on fulltime permanent hiring
            var salModeVal = appliedOn ? appliedOn.value : '0';
            if (String(commTypeVal) === '2' && String(modelVal) === '1') {
                salModeVal = '3'; // Per Hire
            }

            var payload = {
                '_token': token,
                '_method': 'POST',
                'arrCountry': arrCountry,
                'rate_val': rateVal,
                'rateTypeClass': modelVal,
                'sal_mode': salModeVal,
                'salary_currency': pricingCurrency ? pricingCurrency.value : '4',
                'replacement_period_days': replacementPeriod ? replacementPeriod.value : '90',
                'replacement_type': replacementType ? replacementType.value : 'free_replacement',
                'replacement_eligibility': replacementEligibility ? replacementEligibility.value : 'early_exit',
                'billing_frequency': billingFrequency ? billingFrequency.value : 'one_time',
                'billing_trigger': billingTrigger ? billingTrigger.value : 'candidate_joined',
                'payment_terms_days': paymentTerms ? paymentTerms.value : '30',
                'updateId': updateIdVal,
                'commType': commTypeVal,
                'main_status': mainStatusInput ? mainStatusInput.value : '1'
            };

            // Set loading state immediately with visual indicator
            saveBtn.disabled = true;
            saveBtn.classList.add('is-saving');
            saveBtn.setAttribute('aria-busy', 'true');

            // Fail-safe safety timeout: Guarantees button never remains stuck with a spinner
            var safetyTimer = window.setTimeout(function () {
                if (saveBtn.classList.contains('is-saving')) {
                    saveBtn.disabled = false;
                    saveBtn.classList.remove('is-saving');
                    saveBtn.removeAttribute('aria-busy');
                }
            }, 10000);

            function clearSavingIndicator() {
                window.clearTimeout(safetyTimer);
                saveBtn.disabled = false;
                saveBtn.classList.remove('is-saving');
                saveBtn.removeAttribute('aria-busy');
            }

            $.ajax({
                type: 'POST',
                url: saveUrl,
                data: payload,
                dataType: 'json',
                success: function (response) {
                    clearSavingIndicator();

                    if (response && response.status === 'success') {
                        var countryCount = arrCountry.length;
                        if (String(commTypeVal) === '2') {
                            var ftBadge = document.getElementById('fullTimeCount');
                            if (ftBadge) ftBadge.textContent = countryCount;
                        } else {
                            var ctBadge = document.getElementById('contractCount');
                            if (ctBadge) ctBadge.textContent = countryCount;
                        }

                        // Dispatch authoritative state event across window and drawer
                        var serviceType = response.service_type || (String(commTypeVal) === '2' ? 'fulltime' : 'contract');
                        var eventDetail = {
                            version: response.version || 1,
                            service_type: serviceType,
                            state: response.commercial_state || {}
                        };
                        window.dispatchEvent(new CustomEvent('commercial:saved', { detail: eventDetail }));
                        root.dispatchEvent(new CustomEvent('commercial:saved', { detail: eventDetail }));

                        // Rehydrate listing card instantly if client-first engine is loaded
                        if (window.ServicesTabEngine && typeof window.ServicesTabEngine.rehydrateServiceCard === 'function') {
                            window.ServicesTabEngine.rehydrateServiceCard(serviceType, response.commercial_state);
                        }

                        // Toast confirmation
                        if (typeof toastr !== 'undefined') {
                            toastr.success('Commercial terms saved successfully.');
                        } else if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Commercial terms saved successfully.',
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true
                            });
                        }

                        // Automatically close sliding drawer safely
                        var drawerEl = document.getElementById('kt_activitiesJob');
                        if (window.KTDrawer && drawerEl) {
                            var drwInst = KTDrawer.getInstance(drawerEl);
                            if (drwInst) drwInst.hide();
                        }
                        if (drawerEl) {
                            drawerEl.classList.remove('drawer-on');
                        }
                        var closeBtn = root.querySelector('.cd-drw-close-btn');
                        if (closeBtn) {
                            closeBtn.click();
                        } else {
                            var fallbackClose = document.getElementById('kt_activitiesJob_close');
                            if (fallbackClose) fallbackClose.click();
                        }
                        document.querySelectorAll('.drawer-overlay').forEach(function (overlay) {
                            overlay.remove();
                        });
                        document.body.removeAttribute('data-kt-drawer');
                    } else {
                        var errMsg = (response && response.message) ? response.message : 'Services could not be saved.';
                        if (typeof toastr !== 'undefined') {
                            toastr.error(errMsg);
                        } else if (typeof Swal !== 'undefined') {
                            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: errMsg, showConfirmButton: false, timer: 3200 });
                        }
                    }
                },
                error: function (xhr) {
                    clearSavingIndicator();
                    var msg = 'Services could not be saved. Please try again.';
                    if (xhr && xhr.status === 409 && xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    } else if (xhr && xhr.status === 403) {
                        msg = 'You are not authorized to update commercial terms.';
                    } else if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    if (typeof toastr !== 'undefined') {
                        toastr.error(msg);
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: msg, showConfirmButton: false, timer: 3500 });
                    }
                }
            });
        });
    }

    var cancelBtn = root.querySelector('[data-commercial-cancel]');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function () {
            if (window.KTDrawer && KTDrawer.getInstance(document.getElementById('kt_activitiesJob'))) {
                KTDrawer.getInstance(document.getElementById('kt_activitiesJob')).hide();
            } else {
                var closeBtn = root.querySelector('.cd-drw-close-btn');
                if (closeBtn) {
                    closeBtn.click();
                } else {
                    $('#kt_activitiesJob_close').trigger('click');
                }
            }
        });
    }

    root.addEventListener('commercial:saved', captureInitialState);

    updateCommercialType();
    updateReplacementSummary();
    captureInitialState();
    renderSelectedCountries();
})();
</script>
