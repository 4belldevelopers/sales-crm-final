@php
    /**
     * Intent: GOD-LEVEL UI/UX Redesign for "Add Agency Drawer" (Agency Distribution Center).
     * Architecture: Isolated Drawer experience for Service Company Jobs Tracker.
     * Enforces Modern Card + Sub-Card Enterprise Design System (Rule 20):
     * - Fixed right-side drawer (92–94vw on desktop, 15.6" screen optimized).
     * - Sticky Tier 1 Job Header Card with Canonical Classification (Rule 25), Metadata & Close controls.
     * - Two-Column independent architecture:
     *     Column 1: Publish to Agency Marketplace (~35% Width) with Live vs Unpublished sub-cards & Settings.
     *     Column 2: Send Requirement Directly to Agencies (~64% Width) with 3-Tier Card + Sub-Card Standard:
     *         Tier 1: Top Toolbar Header Card with Dynamic Search Sub-Card & Filter CTA.
     *         Tier 2: Sub-Tabs Card (White #ffffff, single active blue highlight #009ef7, Select-All toggle).
     *         Tier 3: Primary Agency Cards Stream (No raw <table>, discrete Sub-Cards 1–5, bottom activity strip).
     * - Multi-Agency Outreach Action Card at bottom with real-time selection counter.
     * - Slide-Over Filter Drawer for comprehensive agency discovery.
     * TENANT ISOLATION: Scoped strictly to auth()->user()->company_id. Zero N+1 queries.
     */
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $currency = GeneralMethod::currency();
    $job_status = GeneralMethod::job_status();

    $current_job_id = @$job->id ?: @$job_id;

    // Determine current marketplace publishing state
    $marketStatus = @$job->public_market_status;
    $isPublished = in_array(@$text_change, ['Published', 'Approval Pending']) || ($marketStatus == 2 || $marketStatus == 1);

    // Pre-calculate salary display values
    $min_sal_mode = $max_sal_mode = $max_salary = $min_salary = '';
    $sal_mode = @$job->max_sal_mode;
    $min_salary_currenc = @$job->min_salary_currency;
    $min_sal_mode = strtoupper(mb_substr($salaryMode[$sal_mode] ?? 'M', 0, 1));
    $max_sal_mode = strtoupper(mb_substr($salaryMode[$sal_mode] ?? 'A', 0, 1));
    $salary_currency = $currencySymbole[@$min_salary_currenc] ?? '₹';

    $currencymain = GeneralMethod::currency();
    $currencyCode = $currencymain[$min_salary_currenc] ?? 'INR';
    $convertedAmounEctc = GeneralMethod::conversionRates($currencyCode, @$job->max_salary ?: 0, 0);
    $convertedAmounCtc = GeneralMethod::conversionRates($currencyCode, @$job->min_salary ?: 0, 0);

    $ectc = $convertedAmounEctc[$sal_mode] ?? 0;
    $ctc = $convertedAmounCtc[$sal_mode] ?? 0;

    $max_salary = GeneralMethod::thousandsCurrencyFormat($ectc);
    $min_salary = GeneralMethod::thousandsCurrencyFormat($ctc);

    // Location display text
    $clientLocationText = 'Remote';
    if (@$job->work_type == 'no') {
        $clientLocationText = !empty($jobLocationCountselect->city) ? $jobLocationCountselect->city : 'Multiple Locations';
    } elseif (@$job->work_type == 'any') {
        $clientLocationText = !empty($jobLocationCountselect->city) ? $jobLocationCountselect->city.' (Hybrid)' : 'Hybrid / Remote';
    }

    // Skills array - safely normalize collection or array
    $rawSkills = $skillArr ?? [];
    if ($rawSkills instanceof \Illuminate\Support\Collection) {
        $skillArrList = $rawSkills->all();
    } elseif (is_array($rawSkills)) {
        $skillArrList = $rawSkills;
    } elseif (is_iterable($rawSkills)) {
        $skillArrList = iterator_to_array($rawSkills);
    } else {
        $skillArrList = [];
    }

    // Canonical Classification Resolution (Rule 25)
    $authCompanyId = auth()->check() ? auth()->user()->company_id : 0;
    $isSelfCompany = (@$job->self_company == 1 || @$job->is_self_company == 1 || (empty(@$job->client_id) && $authCompanyId && @$job->company_id == $authCompanyId));
    $isDirectClient = (@$job->is_direct_client == 1 || @$job->direct_client == 1);
@endphp

{{-- =========================================================================
     SCOPED STYLES: MODERN CARD + SUB-CARD AGENCY DISTRIBUTION CENTER
     ========================================================================= --}}
<style>
    /* ── Drawer Dimensions & Overrides ────────────────────────────────────── */
    #kt_activitiesRecruiter {
        width: 92vw !important;
        max-width: 94vw !important;
        border-top-left-radius: 16px !important;
        border-bottom-left-radius: 16px !important;
        overflow: hidden !important;
        box-shadow: -12px 0 40px rgba(0, 0, 0, 0.12) !important;
        background: #f8fafc !important;
    }
    #kt_activitiesRecruiter > .card {
        border-top-left-radius: 16px !important;
        border-bottom-left-radius: 16px !important;
        height: 100vh !important;
        max-height: 100vh !important;
        overflow: hidden !important;
        display: flex !important;
        flex-direction: column !important;
        background: #f8fafc !important;
        border: none !important;
    }
    #kt_activitiesRecruiter > .card > .card-header {
        display: none !important;
    }
    #kt_activitiesRecruiter_body {
        height: 100vh !important;
        max-height: 100vh !important;
        overflow: hidden !important;
        display: flex !important;
        flex-direction: column !important;
        padding: 0 !important;
        background: #f8fafc !important;
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
    }

    /* ── Sticky Header Card (Top Tier 1 Job Card) ────────────────────────── */
    .agency-drawer-header {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 18px;
        margin: 12px 16px 10px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 60px;
        flex-shrink: 0;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }
    .adh-job-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 15px;
        flex-shrink: 0;
    }
    .adh-title {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.25;
    }
    .adh-meta-item {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11.5px;
        color: #64748b;
        font-weight: 500;
    }
    .adh-skill-badge {
        background: #f1f5f9;
        color: #475569;
        font-size: 10.5px;
        font-weight: 500;
        padding: 1px 7px;
        border-radius: 4px;
        border: 1px solid #e2e8f0;
    }

    /* Scope pills (Rule 25 & UI/UX Standard 6A) */
    .scope-pill-myorg {
        background: #fef3c7 !important;
        color: #92400e !important;
        border: 1px solid #fcd34d !important;
        font-size: 10.5px !important;
        font-weight: 700 !important;
        padding: 2px 8px !important;
        border-radius: 6px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
    }
    .scope-pill-internal {
        background: #eff6ff !important;
        color: #2563eb !important;
        border: 1px solid #bfdbfe !important;
        font-size: 10.5px !important;
        font-weight: 700 !important;
        padding: 2px 8px !important;
        border-radius: 6px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
    }
    .scope-pill-direct {
        background: #ecfdf5 !important;
        color: #059669 !important;
        border: 1px solid #a7f3d0 !important;
        font-size: 10.5px !important;
        font-weight: 700 !important;
        padding: 2px 8px !important;
        border-radius: 6px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
    }

    /* ── Drawer Scrollable Body ───────────────────────────────────────────── */
    .agency-drawer-content {
        flex: 1;
        overflow-y: auto;
        padding: 0 16px 16px 16px;
        background: #f8fafc;
    }

    /* ── Two Column Architecture (Rule 20 Layout) ─────────────────────────── */
    .agency-distribution-grid {
        display: grid;
        grid-template-columns: 35% 65%;
        gap: 14px;
        align-items: start;
    }
    @media (max-width: 1200px) {
        .agency-distribution-grid {
            grid-template-columns: 1fr;
        }
        #kt_activitiesRecruiter {
            width: 100vw !important;
            max-width: 100vw !important;
            border-radius: 0 !important;
        }
    }

    /* ── Column Cards ─────────────────────────────────────────────────────── */
    .agency-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px 18px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    }
    .agency-section-banner-blue {
        background: #f0f7ff;
        border: 1px solid #dbeafe;
        border-radius: 8px;
        padding: 10px 14px;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .agency-col-badge {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        color: #ffffff;
        font-weight: 700;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .agency-col-badge.blue { background: #0066ff; }
    .agency-col-badge.green { background: #10b981; }

    .agency-section-heading {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.25;
        margin: 0;
    }
    .agency-section-subtitle {
        font-size: 11px;
        color: #64748b;
        margin: 2px 0 0 0;
        line-height: 1.35;
    }

    /* ── Column 1 Sub-Cards ───────────────────────────────────────────────── */
    .info-panel-blue {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 14px;
        margin-bottom: 14px;
        position: relative;
        overflow: hidden;
    }
    .live-panel-green {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        border-radius: 8px;
        padding: 12px 14px;
        margin-bottom: 14px;
        position: relative;
        overflow: hidden;
    }
    .panel-icon-circle {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .panel-icon-circle.blue {
        background: #ffffff;
        border: 1px solid #bfdbfe;
        color: #2563eb;
    }
    .panel-icon-circle.green {
        border-radius: 50%;
        background: #dcfce7;
        color: #16a34a;
    }

    /* Input select styling */
    .agency-input-control {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 6px;
    }
    .input-icon-box {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .agency-select {
        flex: 1;
        height: 38px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 600;
        color: #1e293b;
        padding: 0 12px;
        background-color: #ffffff;
        outline: none;
        cursor: pointer;
        transition: border-color 0.15s ease;
    }
    .agency-select:focus {
        border-color: #009ef7;
    }
    .agency-helper-text {
        font-size: 10.5px;
        color: #94a3b8;
        margin-top: 3px;
    }

    /* Benefits card */
    .benefits-panel {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 14px;
        margin-top: 14px;
        margin-bottom: 14px;
    }
    .benefit-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11.5px;
        color: #334155;
        font-weight: 500;
        margin-bottom: 6px;
    }
    .benefit-item:last-child {
        margin-bottom: 0;
    }

    /* Action buttons: display flex with NO !important so .d-none wins */
    .btn-publish-primary {
        background: #009ef7;
        color: #ffffff;
        border: none;
        border-radius: 8px;
        padding: 10px 16px;
        width: 100%;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 14px;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(0, 158, 247, 0.25);
        transition: background-color 0.15s ease, transform 0.1s ease;
    }
    .btn-publish-primary:hover {
        background: #0088d4;
    }
    .btn-unpublish-destructive {
        background: #ffffff;
        color: #dc2626;
        border: 1.5px solid #fca5a5;
        border-radius: 8px;
        padding: 10px 16px;
        width: 100%;
        font-size: 13px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 14px;
        cursor: pointer;
        transition: background-color 0.15s ease, color 0.15s ease;
    }
    .btn-unpublish-destructive:hover {
        background: #fef2f2;
        color: #b91c1c;
        border-color: #f87171;
    }

    /* Enforce strict hidden rules for marketplace toggle */
    #sp_list_one.d-none,
    #sp_list_two.d-none,
    #marketplace_unpublished_box.d-none,
    #marketplace_live_box.d-none,
    .agency-hidden {
        display: none !important;
    }

    /* ── Tier 1: Toolbar Header Card (Rule 20) ────────────────────────────── */
    .agency-toolbar-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 10px 16px !important;
        margin-bottom: 10px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        flex-wrap: wrap !important;
        gap: 12px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    }
    .agency-toolbar-left {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
    }
    .agency-toolbar-title {
        font-size: 14px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        line-height: 1.25 !important;
        margin: 0 !important;
    }
    .agency-toolbar-sub {
        font-size: 11px !important;
        color: #64748b !important;
        margin: 2px 0 0 0 !important;
        line-height: 1.35 !important;
    }
    .agency-toolbar-right {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }
    .agency-search-subcard {
        position: relative !important;
        min-width: 240px !important;
    }
    .agency-search-subcard svg {
        position: absolute;
        left: 10px;
        top: 11px;
        color: #94a3b8;
        pointer-events: none;
        z-index: 2;
    }
    .agency-search-subcard .agency-search-input {
        width: 100%;
        height: 36px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding-left: 32px;
        padding-right: 28px;
        font-size: 12px;
        background: #f8fafc;
        color: #1e293b;
        outline: none;
        transition: border-color 0.15s ease, background 0.15s ease;
    }
    .agency-search-subcard .agency-search-input:focus {
        border-color: #009ef7;
        background: #ffffff;
    }
    .agency-search-clear-btn {
        position: absolute !important;
        right: 8px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        background: #e2e8f0 !important;
        border: none !important;
        border-radius: 50% !important;
        width: 18px !important;
        height: 18px !important;
        font-size: 10px !important;
        line-height: 18px !important;
        text-align: center !important;
        color: #64748b !important;
        cursor: pointer !important;
        display: none;
        padding: 0 !important;
        z-index: 3;
    }
    .agency-search-clear-btn:hover {
        background: #cbd5e1 !important;
        color: #0f172a !important;
    }

    /* Tagify support inside search subcard */
    .agency-search-subcard .tagify {
        min-height: 36px !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        background: #f8fafc !important;
        padding: 2px 8px 2px 30px !important;
        display: flex !important;
        align-items: center !important;
        flex-wrap: wrap !important;
        font-family: 'Poppins', sans-serif !important;
    }
    .agency-search-subcard .tagify.tagify--focus {
        border-color: #009ef7 !important;
        background: #ffffff !important;
    }
    .agency-search-subcard .tagify ~ #agency_unified_search {
        display: none !important;
    }
    .agency-search-subcard .tagify__tag {
        background: #eef2ff !important;
        border: 1px solid #e0e7ff !important;
        border-radius: 9999px !important;
        padding: 2px 6px 2px 10px !important;
        margin: 2px 3px 2px 0 !important;
    }
    .agency-search-subcard .tagify__tag > div {
        color: #4338ca !important;
        font-size: 11px !important;
        font-weight: 500 !important;
    }
    .agency-search-subcard .tagify__input {
        font-size: 11.5px !important;
        line-height: 20px !important;
    }

    .btn-filter-trigger {
        height: 36px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        border-radius: 8px;
        padding: 0 12px;
        font-size: 11.5px;
        font-weight: 600;
        color: #334155;
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: background 0.15s ease, border-color 0.15s ease;
    }
    .btn-filter-trigger:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    /* ── Tier 2: Sub-Tabs Card (Rule 20) ─────────────────────────────────── */
    .agency-tabs-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 8px 12px !important;
        margin-bottom: 10px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        flex-wrap: wrap !important;
        gap: 8px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    }
    .agency-tabs-group {
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
    }
    .agency-tab-btn {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        padding: 6px 14px !important;
        font-size: 11.5px !important;
        font-weight: 600 !important;
        color: #475569 !important;
        cursor: pointer !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        transition: all 0.15s ease !important;
        text-decoration: none !important;
    }
    .agency-tab-btn:hover {
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }
    .agency-tab-btn.active {
        background: #009ef7 !important;
        border-color: #009ef7 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 4px rgba(0, 158, 247, 0.25) !important;
    }
    .agency-tab-badge {
        font-size: 10.5px !important;
        font-weight: 700 !important;
        font-variant-numeric: tabular-nums !important;
        color: #64748b !important;
    }
    .agency-tab-btn.active .agency-tab-badge {
        color: #ffffff !important;
    }
    .agency-select-all-subcard {
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
        padding: 5px 10px !important;
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 6px !important;
    }

    /* ── Tier 3: Primary Agency Cards Stream (Rule 20) ──────────────────── */
    .agency-stream-container {
        max-height: calc(100vh - 290px);
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 2px;
    }
    .agency-cards-stream {
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding-bottom: 8px;
    }
    .agency-entity-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        margin-bottom: 4px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease !important;
        position: relative !important;
        overflow: hidden !important;
        width: 100% !important;
    }
    .agency-entity-card:hover {
        border-color: #cbd5e1 !important;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06) !important;
        transform: translateY(-1px) !important;
    }

    /* Discrete Sub-Cards Grid */
    .agency-card-grid {
        display: grid !important;
        grid-template-columns: minmax(0, 2.7fr) minmax(0, 1.15fr) minmax(0, 1.15fr) minmax(0, 1.25fr) minmax(0, 1.35fr) !important;
        gap: 10px !important;
        align-items: center !important;
        padding: 10px 14px !important;
        width: 100% !important;
    }
    @media (max-width: 1400px) {
        .agency-card-grid {
            grid-template-columns: minmax(0, 2.5fr) minmax(0, 1.1fr) minmax(0, 1.1fr) minmax(0, 1.2fr) minmax(0, 1.3fr) !important;
            gap: 8px !important;
            padding: 8px 12px !important;
        }
    }
    @media (max-width: 1100px) {
        .agency-card-grid {
            grid-template-columns: 1fr 1fr !important;
            gap: 10px !important;
        }
    }

    .ag-subcard {
        min-width: 0 !important;
    }
    .ag-subcard-label {
        font-size: 9.5px !important;
        font-weight: 700 !important;
        color: #94a3b8 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.3px !important;
        margin-bottom: 2px !important;
        display: flex !important;
        align-items: center !important;
        white-space: nowrap !important;
    }

    /* Sub-Card 1: Identity */
    .ag-avatar-wrap {
        width: 40px !important;
        height: 40px !important;
        border-radius: 50% !important;
        overflow: hidden !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        border: 1.5px solid #f1f5f9 !important;
        flex-shrink: 0 !important;
    }
    .ag-avatar-img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
    }
    .ag-avatar-initials {
        width: 100% !important;
        height: 100% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 13.5px !important;
        font-weight: 700 !important;
        letter-spacing: -0.5px !important;
    }
    .ag-agency-title {
        font-size: 13px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        text-decoration: none !important;
        line-height: 1.25 !important;
        cursor: pointer !important;
    }
    .ag-agency-title:hover {
        color: #0066ff !important;
    }
    .ag-agency-spec {
        font-size: 11px !important;
        color: #64748b !important;
        margin-top: 1px !important;
    }
    .ag-verified-badge {
        display: inline-flex !important;
        align-items: center !important;
        flex-shrink: 0 !important;
    }
    .ag-check-spacer {
        width: 18px !important;
        height: 18px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    /* Badges */
    .ag-badge {
        font-size: 9.5px !important;
        padding: 1px 6px !important;
        border-radius: 4px !important;
        font-weight: 600 !important;
        white-space: nowrap !important;
        line-height: 1.3 !important;
        display: inline-flex !important;
        align-items: center !important;
    }
    .ag-badge-match-high {
        background: #ecfdf5 !important;
        color: #059669 !important;
        border: 1px solid #a7f3d0 !important;
        font-weight: 700 !important;
    }
    .ag-badge-match-neutral {
        background: #f1f5f9 !important;
        color: #64748b !important;
        border: 1px solid #cbd5e1 !important;
    }
    .ag-badge-additional {
        background: #f8fafc !important;
        color: #64748b !important;
        border: 1px solid #e2e8f0 !important;
    }
    .ag-badge-registered {
        background: #eff6ff !important;
        color: #2563eb !important;
        border: 1px solid #bfdbfe !important;
    }
    .ag-badge-skill {
        background: #f1f5f9 !important;
        color: #475569 !important;
        border: 1px solid #e2e8f0 !important;
        font-weight: 500 !important;
    }
    .ag-badge-skill-more {
        background: #ffffff !important;
        color: #94a3b8 !important;
        border: 1px solid #e2e8f0 !important;
        font-size: 9px !important;
        padding: 1px 4px !important;
    }

    /* Sub-Card 2: Profiles */
    .ag-metric-row {
        display: flex !important;
        align-items: baseline !important;
        gap: 4px !important;
    }
    .ag-metric-num {
        font-size: 15px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        font-variant-numeric: tabular-nums !important;
        line-height: 1.1 !important;
    }
    .ag-metric-unit {
        font-size: 11.5px !important;
        color: #475569 !important;
        font-weight: 500 !important;
    }
    .ag-metric-sub {
        font-size: 10px !important;
        color: #64748b !important;
        margin-top: 2px !important;
        display: flex !important;
        align-items: center !important;
        gap: 4px !important;
    }
    .ag-dot-green {
        width: 6px !important;
        height: 6px !important;
        border-radius: 50% !important;
        background: #10b981 !important;
        display: inline-block !important;
    }

    /* Sub-Card 3: Rates */
    .ag-rate-value {
        font-size: 13px !important;
        font-weight: 700 !important;
        font-variant-numeric: tabular-nums !important;
        line-height: 1.1 !important;
    }
    .text-emerald {
        color: #15803d !important;
    }
    .ag-rate-pill {
        font-size: 9.5px !important;
        font-weight: 600 !important;
        padding: 1px 5px !important;
        border-radius: 4px !important;
    }
    .ag-rate-fulltime {
        background: #dcfce7 !important;
        color: #166534 !important;
        border: 1px solid #bbf7d0 !important;
    }
    .ag-rate-contract {
        background: #e0f2fe !important;
        color: #0369a1 !important;
        border: 1px solid #bae6fd !important;
    }
    .ag-rate-type-text {
        font-size: 10px !important;
        color: #94a3b8 !important;
    }

    /* Sub-Card 4: Location & Team */
    .ag-team-row {
        font-size: 11.5px !important;
        color: #334155 !important;
        display: flex !important;
        align-items: center !important;
        gap: 5px !important;
    }

    /* Sub-Card 5: Actions */
    .ag-btn-send {
        background: #10b981 !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: 8px !important;
        padding: 7px 14px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        box-shadow: 0 1px 3px rgba(16, 185, 129, 0.25) !important;
        cursor: pointer !important;
        white-space: nowrap !important;
        transition: background-color 0.15s ease, transform 0.1s ease !important;
    }
    .ag-btn-send:hover {
        background: #059669 !important;
        color: #ffffff !important;
        transform: translateY(-1px) !important;
    }
    .ag-badge-sent {
        background: #dcfce7 !important;
        color: #15803d !important;
        border: 1px solid #bbf7d0 !important;
        border-radius: 8px !important;
        font-size: 11.5px !important;
        font-weight: 700 !important;
        padding: 6px 12px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 5px !important;
        white-space: nowrap !important;
    }
    .ag-btn-resend {
        background: #009ef7 !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: 8px !important;
        padding: 7px 12px !important;
        font-size: 11.5px !important;
        font-weight: 600 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 5px !important;
        cursor: pointer !important;
        white-space: nowrap !important;
    }
    .ag-btn-resend:hover {
        background: #0088d4 !important;
        color: #ffffff !important;
    }
    .ag-btn-remove {
        background: #fee2e2 !important;
        color: #dc2626 !important;
        border: 1px solid #fecaca !important;
        border-radius: 8px !important;
        padding: 5px 10px !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        cursor: pointer !important;
    }

    /* Bottom Activity Strip (Rule 20 Sub-Card 6) */
    .ag-card-bottom-strip {
        border-top: 1px solid #f8fafc !important;
        padding: 6px 14px !important;
        background: #fafbfc !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        font-size: 11px !important;
    }
    .ag-bottom-partner-tag {
        font-size: 11px !important;
        font-weight: 600 !important;
        color: #475569 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
    }
    .ag-bottom-meta {
        font-size: 10.5px !important;
        color: #94a3b8 !important;
    }
    .ag-bottom-link {
        font-size: 11px !important;
        font-weight: 600 !important;
        color: #0066ff !important;
        text-decoration: none !important;
        display: inline-flex !important;
        align-items: center !important;
    }
    .ag-bottom-link:hover {
        text-decoration: underline !important;
    }

    /* Multiple Agencies Bottom Action Card */
    .bottom-multi-agency-banner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 16px;
        background: #f0f7ff;
        border: 1px solid #bfdbfe;
        border-radius: 10px;
        margin-top: 10px;
    }

    /* Loader styling */
    .table-loader-wrap {
        display: none;
        padding: 30px;
        text-align: center;
    }
    .loader_search {
        display: none;
    }

    /* ── Assigned Agencies Tab Styles ────────────────────────────────── */
    .aa-grid {
        grid-template-columns: minmax(0, 2.5fr) minmax(0, 1.3fr) minmax(0, 2.2fr) minmax(0, 1.1fr) minmax(0, 1.4fr) !important;
    }
    .ag-pipeline-strip {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 4px !important;
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 6px !important;
        padding: 4px 6px !important;
    }
    .ag-pipe-kpi {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        flex: 1 !important;
    }
    .ag-pipe-num {
        font-size: 12px !important;
        font-weight: 700 !important;
        font-variant-numeric: tabular-nums !important;
        color: #0f172a !important;
        line-height: 1.1 !important;
    }
    .ag-pipe-lbl {
        font-size: 8.5px !important;
        color: #64748b !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
    }
    .ag-contact-name {
        font-size: 11.5px !important;
        font-weight: 600 !important;
        color: #0f172a !important;
        line-height: 1.25 !important;
    }
    .ag-contact-btns {
        display: flex !important;
        align-items: center !important;
        gap: 4px !important;
    }
    .ag-contact-icon-btn {
        width: 22px !important;
        height: 22px !important;
        border-radius: 5px !important;
        border: 1px solid #e2e8f0 !important;
        background: #f8fafc !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 10px !important;
        color: #64748b !important;
        cursor: pointer !important;
    }
    .ag-contact-icon-btn:hover {
        background: #e2e8f0 !important;
        color: #0f172a !important;
    }
    .ag-shared-rel {
        font-size: 11.5px !important;
        font-weight: 600 !important;
        color: #0f172a !important;
    }
    .ag-shared-abs {
        font-size: 10px !important;
        color: #94a3b8 !important;
    }
    .aa-badge {
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
        padding: 2px 7px !important;
        border-radius: 6px !important;
        font-size: 10.5px !important;
        font-weight: 600 !important;
    }
    .aa-badge-dot {
        width: 6px !important;
        height: 6px !important;
        border-radius: 50% !important;
        background: currentColor !important;
    }
    .aa-badge-open    { background: #dcfce7 !important; color: #16a34a !important; }
    .aa-badge-closed  { background: #f1f5f9 !important; color: #64748b !important; }
    .aa-badge-paused  { background: #fef3c7 !important; color: #d97706 !important; }
    .aa-badge-expired { background: #fee2e2 !important; color: #dc2626 !important; }

    .ag-terms-btn {
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
        padding: 5px 8px !important;
        border-radius: 6px !important;
        border: 1px solid #e2e8f0 !important;
        background: #ffffff !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        color: #475569 !important;
        cursor: pointer !important;
        white-space: nowrap !important;
    }
    .ag-terms-btn:hover {
        border-color: #009ef7 !important;
        color: #009ef7 !important;
        background: #eff6ff !important;
    }
    .ag-dots-btn {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 26px !important;
        height: 26px !important;
        border-radius: 6px !important;
        border: 1px solid #e2e8f0 !important;
        background: #ffffff !important;
        cursor: pointer !important;
        color: #64748b !important;
        font-size: 12px !important;
    }
    .ag-dots-btn:hover {
        background: #f8fafc !important;
        color: #1e293b !important;
    }
    .aa-ddmenu {
        position: absolute !important;
        right: 0 !important;
        top: calc(100% + 4px) !important;
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08) !important;
        z-index: 100 !important;
        min-width: 155px !important;
        padding: 4px 0 !important;
        display: none !important;
    }
    .aa-ddmenu.open {
        display: block !important;
    }
    .aa-ddi {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        padding: 7px 12px !important;
        font-size: 11.5px !important;
        color: #ef4444 !important;
        cursor: pointer !important;
        border: none !important;
        background: none !important;
        width: 100% !important;
        text-align: left !important;
        white-space: nowrap !important;
    }
    .aa-ddi:hover {
        background: #fef2f2 !important;
    }

    /* Empty state */
    .agency-empty-state-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 48px 20px !important;
        text-align: center !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
    }
    .ag-empty-icon-box {
        width: 52px !important;
        height: 52px !important;
        border-radius: 50% !important;
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin-bottom: 12px !important;
        color: #94a3b8 !important;
    }
    .ag-empty-title {
        font-size: 14px !important;
        font-weight: 700 !important;
        color: #1e293b !important;
        margin-bottom: 4px !important;
    }
    .ag-empty-subtitle {
        font-size: 11.5px !important;
        color: #64748b !important;
        max-width: 320px !important;
        line-height: 1.45 !important;
    }

    /* ── Filter Slide-Over Drawer ── */
    .agency-filter-drawer-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(2px);
        -webkit-backdrop-filter: blur(2px);
        z-index: 1098;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.25s ease, visibility 0.25s ease;
    }
    .agency-filter-drawer-overlay.open {
        opacity: 1;
        visibility: visible;
    }
    .agency-filter-drawer {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        width: 45vw !important;
        max-width: 45vw !important;
        height: 100vh;
        max-height: 100vh;
        background: #ffffff;
        z-index: 1099;
        box-shadow: -10px 0 40px rgba(0, 0, 0, 0.16);
        display: flex;
        flex-direction: column;
        transform: translateX(100%);
        transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.28s ease;
        visibility: hidden;
        overflow: hidden;
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }
    .agency-filter-drawer.open {
        transform: translateX(0);
        visibility: visible;
    }
    @media (max-width: 991px) {
        .agency-filter-drawer {
            width: 85vw !important;
            max-width: 95vw !important;
        }
    }
    .agency-filter-drawer-header {
        padding: 14px 18px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #ffffff;
        flex-shrink: 0;
    }
    .afd-header-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: #eff6ff;
        border: 1px solid #dbeafe;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .afd-title {
        font-size: 14.5px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }
    .afd-subtitle {
        font-size: 11px;
        color: #64748b;
        margin: 2px 0 0 0;
    }
    .afd-close-btn {
        width: 30px;
        height: 30px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        cursor: pointer;
    }
    .afd-close-btn:hover {
        background: #fee2e2;
        color: #ef4444;
    }
    .agency-filter-drawer-body {
        padding: 16px 18px;
        overflow-y: auto;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 16px;
        background: #ffffff;
    }
    .afd-trust-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 12px;
    }
    .afd-trust-item {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 600;
        color: #334155;
    }
    .afd-form-group {
        display: flex;
        flex-direction: column;
    }
    .afd-form-label {
        font-size: 12px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .afd-label-num {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #eff6ff;
        color: #2563eb;
        font-size: 10px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .agency-filter-drawer-footer {
        padding: 12px 18px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f8fafc;
        flex-shrink: 0;
    }
    .afd-reset-btn {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 6px 14px;
        font-size: 11.5px;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
    }
    .afd-reset-btn:hover {
        background: #fee2e2;
        color: #ef4444;
    }
    .afd-cancel-btn {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 6px 14px;
        font-size: 11.5px;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
    }
    .afd-apply-btn {
        background: #009ef7;
        color: #ffffff;
        border: none;
        border-radius: 6px;
        padding: 6px 16px;
        font-size: 11.5px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
    }
    .afd-apply-btn:hover {
        background: #0088d4;
    }
</style>

{{-- Hidden Context Inputs --}}
<input type="hidden" name="current_job_id" id="current_job_id" value="{{ $current_job_id }}" />
<input type="hidden" name="workhiringtype" id="workhiringtype" value="{{ @$job->employment_type }}" />
<input type="hidden" name="country" id="country" value="" />
<input type="hidden" name="min_exp" id="min_exp" value="{{ @$job->min_exp }}" />
<input type="hidden" name="max_exp" id="max_exp" value="{{ @$job->max_exp }}" />
<input type="hidden" name="min_sal" id="min_sal" value="{{ @$job->min_salary }}" />
<input type="hidden" name="max_sal" id="max_sal" value="{{ @$job->max_salary }}" />
<input type="hidden" name="min_salary_currency" id="min_salary_currency" value="{{ @$job->min_salary_currency }}" />
<input type="hidden" name="sal_mode" id="sal_mode" value="{{ @$job->max_sal_mode }}" />
<input type="hidden" name="hiring_type" id="hiring_type" value="2" />
<input type="hidden" name="experience" id="experience" value="" />
<input type="hidden" name="industry" id="industry" value="" />
<input type="hidden" name="country4" id="country4" value="Show All" />
<input type="hidden" name="hiringtype4" id="hiringtype4" value="0" />
<input type="hidden" name="kt_tagify_4" id="kt_tagify_4" value="" />
<input type="hidden" name="kt_tagify_5" id="kt_tagify_5" value="" />
<input type="hidden" name="kt_tagify_location" id="kt_tagify_location" value="" />

{{-- =========================================================================
     STICKY DRAWER HEADER: TIER 1 JOB OVERVIEW CARD
     ========================================================================= --}}
<div class="agency-drawer-header">
    <div class="d-flex align-items-center gap-3">
        {{-- Job Icon Badge --}}
        <div class="adh-job-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="16 18 22 12 16 6"></polyline>
                <polyline points="8 6 2 12 8 18"></polyline>
            </svg>
        </div>

        {{-- Job Title & Canonical Badges --}}
        <div class="d-flex flex-column">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="adh-title text-truncate" style="max-width: 320px;" title="{{ @$job->title }}">
                    {{ ucwords(@$job->title ?: 'Requirement') }}
                </span>

                {{-- Canonical Scope Pill (Rule 25) --}}
                @if($isSelfCompany)
                    <span class="scope-pill-myorg" title="Own company requirement">
                        <i class="fa-solid fa-id-badge fs-9"></i> My Organization
                    </span>
                @elseif($isDirectClient)
                    <span class="scope-pill-direct" title="Marketplace client mandate">
                        <i class="fa-solid fa-shield-alt fs-9"></i> Direct Client
                    </span>
                @else
                    <span class="scope-pill-internal" title="Managed client requirement">
                        <i class="fa-solid fa-building fs-9"></i> Internal Client
                    </span>
                @endif

                {{-- Employment Type Pill --}}
                <span class="badge" style="background:#eff6ff; color:#2563eb; border: 1px solid #bfdbfe; border-radius: 6px; font-size: 10.5px; font-weight: 600; padding: 2px 7px;">
                    {{ @$job->employment_type == 1 ? 'Contract' : 'Full-time' }}
                </span>

                {{-- Status Badge --}}
                <span class="badge" style="background:#dcfce7; color:#15803d; border: 1px solid #bbf7d0; border-radius: 6px; font-size: 10.5px; font-weight: 700; padding: 2px 7px;">
                    HIRING
                </span>
            </div>

            {{-- Compact Metadata Row --}}
            <div class="d-flex align-items-center gap-2 flex-wrap mt-1">
                <span class="adh-meta-item">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                    {{ @$job->min_exp }}–{{ @$job->max_exp }} Years
                </span>
                <span class="text-gray-300">•</span>
                <span class="adh-meta-item">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    {{ $clientLocationText }}
                </span>
                <span class="text-gray-300">•</span>
                <span class="adh-meta-item">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                    @php
                        $isSalConf = (\App\Services\SalaryVisibilityPolicy::normalize(@$job->salary_visibility) === \App\Services\SalaryVisibilityPolicy::FULLY_CONFIDENTIAL)
                            || ((empty($job->min_salary) || (float)$job->min_salary === 0.0) && (empty($job->max_salary) || (float)$job->max_salary === 0.0) && (@$job->salary_visibility === 'confidential'));
                    @endphp
                    @if($isSalConf)
                        <span style="font-weight: 500; color: #dc2626; display: inline-flex; align-items: center; gap: 4px;"><i class="fas fa-lock" style="font-size: 10px; color: #dc2626;"></i> Confidential</span>
                    @else
                        {!! $salary_currency !!} {{ $min_salary }}–{{ $max_salary }} / {{ $max_sal_mode }}
                    @endif
                </span>
                <span class="text-gray-300">•</span>
                <span class="adh-meta-item">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    {{ number_format(@$job->total_positions ?: 1) }} Opening{{ (@$job->total_positions > 1) ? 's' : '' }}
                </span>

                {{-- Skill Pills --}}
                @if(!empty($skillArrList))
                    <span class="text-gray-300">•</span>
                    <div class="d-flex align-items-center gap-1">
                        @foreach(array_slice($skillArrList, 0, 3) as $sk)
                            @php
                                $skName = is_object($sk) ? ($sk->skill_name ?? $sk->name ?? '') : (is_array($sk) ? ($sk['skill_name'] ?? $sk['name'] ?? '') : (string) $sk);
                            @endphp
                            @if(!empty($skName))
                                <span class="adh-skill-badge">{{ ucfirst($skName) }}</span>
                            @endif
                        @endforeach
                        @if(count($skillArrList) > 3)
                            <span class="adh-skill-badge text-muted">+{{ count($skillArrList) - 3 }}</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Right: Close Button --}}
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-sm d-flex align-items-center gap-2 drawer-close-trigger" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 7px 14px; font-size: 12px; font-weight: 600; color: #1e293b; cursor: pointer; transition: background-color 0.15s ease;">
            <span>Close</span>
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>
</div>

{{-- =========================================================================
     DRAWER CONTENT: TWO-COLUMN INDEPENDENT ARCHITECTURE
     ========================================================================= --}}
<div class="agency-drawer-content">
    <div class="agency-distribution-grid">

        {{-- =================================================================
             COLUMN 1: PUBLISH TO AGENCY MARKETPLACE (~35% Width)
             ================================================================= --}}
        <div class="agency-card">
            {{-- Header inside subtle tinted banner --}}
            <div class="agency-section-banner-blue">
                <span class="agency-col-badge blue">1</span>
                <div>
                    <h2 class="agency-section-heading">Publish to Agency Marketplace</h2>
                    <p class="agency-section-subtitle">Make this job visible to verified recruitment agencies on Jero Marketplace.</p>
                </div>
            </div>

            {{-- ── UNPUBLISHED STATE CONTAINER ── --}}
            <div id="marketplace_unpublished_box" class="{{ $isPublished ? 'd-none' : '' }}" style="{{ $isPublished ? 'display: none !important;' : 'display: block;' }}">
                <div class="info-panel-blue">
                    <div class="d-flex align-items-center gap-3">
                        <div class="panel-icon-circle blue">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                        </div>
                        <div style="flex: 1; padding-right: 20px;">
                            <div style="font-size: 13px; font-weight: 700; color: #2563eb;">Open to Agency Applications</div>
                            <div style="font-size: 11px; color: #475569; margin-top: 2px; line-height: 1.35;">Any verified agency can discover this job and submit suitable candidates.</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── LIVE STATE CONTAINER ── --}}
            <div id="marketplace_live_box" class="{{ $isPublished ? '' : 'd-none' }}" style="{{ $isPublished ? 'display: block;' : 'display: none !important;' }}">
                <div class="live-panel-green">
                    <div class="d-flex align-items-center gap-3">
                        <div class="panel-icon-circle green">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="#16a34a"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                        </div>
                        <div style="flex: 1;">
                            <div class="d-flex align-items-center gap-2">
                                <span style="font-size: 12.5px; font-weight: 800; color: #15803d; letter-spacing: 0.3px;">LIVE ON AGENCY MARKETPLACE</span>
                                <span class="badge" style="background: #dcfce7; color: #15803d; border: 1px solid #86efac; border-radius: 10px; font-size: 10px; font-weight: 700; padding: 1px 7px; display: inline-flex; align-items: center; gap: 4px;">
                                    <span style="display:inline-block; width:5px; height:5px; border-radius:50%; background:#16a34a;"></span>Live
                                </span>
                            </div>
                            <div style="font-size: 11px; color: #166534; margin-top: 2px; line-height: 1.35;">This job is active on Jero Marketplace. Verified agencies can discover and submit candidates.</div>
                        </div>
                    </div>
                </div>
            </div>

            @php
                $jobMeta = is_array(@$job->meta_details) ? $job->meta_details : (json_decode(@$job->meta_details ?? '{}', true) ?: []);
                $currentVendorLimit = (int) (@$jobMeta['vendor_limit'] ?? (@$job->vendor_limit ?? (@$vendor_limit ?: 20)));
                if (!in_array($currentVendorLimit, [5, 10, 20, 50], true)) {
                    $currentVendorLimit = 20;
                }

                $currentAutoExpire = (int) (@$jobMeta['auto_expire'] ?? (@$job->auto_expire ?? 0));
                if (!in_array($currentAutoExpire, [7, 14, 30], true)) {
                    if (!empty($job->end_date)) {
                        $daysLeft = (int) ceil(now()->diffInDays(\Carbon\Carbon::parse($job->end_date), false));
                        if ($daysLeft > 14) {
                            $currentAutoExpire = 30;
                        } elseif ($daysLeft > 7) {
                            $currentAutoExpire = 14;
                        } else {
                            $currentAutoExpire = 7;
                        }
                    } else {
                        $currentAutoExpire = 7;
                    }
                }
            @endphp

            {{-- Agency Acceptance Limit --}}
            <div class="mt-3">
                <div class="d-flex align-items-center gap-1">
                    <label class="fw-bold fs-7 text-gray-900 mb-0">Agency Acceptance Limit</label>
                    <i class="fa-regular fa-circle-question ms-1" style="font-size:11px;color:#94a3b8;cursor:help;" title="Maximum agencies that can accept and work on this job requirement."></i>
                </div>
                <div class="agency-input-control">
                    <div class="input-icon-box">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <select class="agency-select" id="marketplace_vendor_limit" data-current="{{ $currentVendorLimit }}">
                        <option value="5" {{ $currentVendorLimit === 5 ? 'selected' : '' }}>5 agencies</option>
                        <option value="10" {{ $currentVendorLimit === 10 ? 'selected' : '' }}>10 agencies</option>
                        <option value="20" {{ $currentVendorLimit === 20 ? 'selected' : '' }}>20 agencies</option>
                        <option value="50" {{ $currentVendorLimit === 50 ? 'selected' : '' }}>50 agencies</option>
                    </select>
                </div>
                <div class="agency-helper-text">Maximum agencies that can accept this job.</div>
            </div>

            {{-- Auto-expire --}}
            <div class="mt-3">
                <div class="d-flex align-items-center gap-1">
                    <label class="fw-bold fs-7 text-gray-900 mb-0">Auto-expire</label>
                    <i class="fa-regular fa-circle-question ms-1" style="font-size:11px;color:#94a3b8;cursor:help;" title="The marketplace listing will automatically close on this date."></i>
                </div>
                <div class="agency-input-control">
                    <div class="input-icon-box">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                    <select class="agency-select" id="marketplace_auto_expire" data-current="{{ $currentAutoExpire }}">
                        <option value="7" {{ $currentAutoExpire === 7 ? 'selected' : '' }}>7 Days ({{ now()->addDays(7)->format('d M Y') }})</option>
                        <option value="14" {{ $currentAutoExpire === 14 ? 'selected' : '' }}>14 Days ({{ now()->addDays(14)->format('d M Y') }})</option>
                        <option value="30" {{ $currentAutoExpire === 30 ? 'selected' : '' }}>30 Days ({{ now()->addDays(30)->format('d M Y') }})</option>
                    </select>
                </div>
                <div class="agency-helper-text">The marketplace listing will automatically close on this date.</div>
            </div>

            {{-- Benefit Checklist Panel --}}
            <div class="benefits-panel">
                <div class="benefit-item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#10b981" class="flex-shrink-0"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                    <span>Any verified agency can apply for this job.</span>
                </div>
                <div class="benefit-item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#10b981" class="flex-shrink-0"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                    <span>You will review and accept candidate submissions.</span>
                </div>
                <div class="benefit-item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#10b981" class="flex-shrink-0"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                    <span>You can unpublish the requirement anytime.</span>
                </div>
            </div>

            {{-- Action CTAs: Mutually exclusive display --}}
            <button type="button" id="sp_list_one" class="btn-publish-primary post_to_public {{ $isPublished ? 'd-none' : '' }}" style="{{ $isPublished ? 'display: none !important;' : 'display: flex;' }}" data-c="0" data-row-id="0" data-matchcount="0" data-row-jobid="{{ @$job->id }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                <span>Publish to Agency Marketplace</span>
            </button>

            <button type="button" id="sp_list_two" class="btn-unpublish-destructive post_to_public {{ $isPublished ? '' : 'd-none' }}" style="{{ $isPublished ? 'display: flex;' : 'display: none !important;' }}" data-c="1" data-row-id="0" data-matchcount="0" data-row-jobid="{{ @$job->id }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                <span>Unpublish from Marketplace</span>
            </button>

            {{-- Terms Secondary Link --}}
            <div class="text-center mt-3">
                <a href="javascript:void(0);" data-row-id="{{ @$job->id }}" data-row-recommendation_relation_id="" class="client-terms-listing d-inline-flex align-items-center gap-1" style="font-size: 11.5px; color: #0066ff; font-weight: 500; text-decoration: none;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0066ff" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    <span>View Agency Marketplace Terms</span>
                </a>
            </div>
        </div>

        {{-- =================================================================
             COLUMN 2: SEND REQUIREMENT DIRECTLY TO AGENCIES (~64% Width)
             Strict Modern Card + Sub-Card Enterprise Standard (Rule 20)
             ================================================================= --}}
        <div>
            {{-- ── TIER 1: TOP HEADER TOOLBAR CARD ── --}}
            <div class="agency-toolbar-card">
                <div class="agency-toolbar-left">
                    <span class="agency-col-badge green">2</span>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h2 class="agency-toolbar-title">Send Requirement Directly to Agencies</h2>
                            <div class="direct-outreach-badge" style="background: #e0f2fe; border: 1px solid #bae6fd; border-radius: 6px; padding: 2px 8px; display: inline-flex; align-items: center; gap: 5px;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M2.5 13.5L21.5 3.5L14.5 21.5L10.5 14.5L2.5 13.5Z" fill="#0284c7"/><path d="M10.5 14.5L21.5 3.5" stroke="#ffffff" stroke-width="1.2" stroke-linecap="round"/></svg>
                                <span style="font-size: 10.5px; font-weight: 700; color: #0369a1;">Direct Outreach • Faster Responses</span>
                            </div>
                        </div>
                        <p class="agency-toolbar-sub">Select verified agencies to notify and ask them to submit matching candidates for this requirement.</p>
                    </div>
                </div>

                {{-- Right: Search Sub-Card & Filter Trigger --}}
                <div class="agency-toolbar-right">
                    <div class="agency-search-subcard">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input type="text" id="agency_unified_search" class="agency-search-input" placeholder="Search by skills..." value="{{ @$default_skill }}" autocomplete="off" />
                        <button type="button" class="agency-search-clear-btn" id="btn_agency_search_clear" title="Clear search">✕</button>
                    </div>
                    <button type="button" class="btn-filter-trigger">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                        <span>Filter</span>
                    </button>
                </div>
            </div>

            {{-- ── TIER 2: SUB-TABS CARD WITH WHITE BACKGROUND (Rule 20) ── --}}
            <div class="agency-tabs-card">
                <div class="agency-tabs-group" role="tablist">
                    <button type="button" class="agency-tab-btn agency-tab-link active" data-tab="recommended" href="#tab_recommended">
                        <i class="fas fa-sparkles me-1"></i>
                        <span>Recommended</span>
                        <span class="agency-tab-badge" id="recommendedTotal"></span>
                    </button>
                    <button type="button" class="agency-tab-btn agency-tab-link" data-tab="registered" href="#tab_registered">
                        <i class="fas fa-building me-1"></i>
                        <span>My Agencies</span>
                        <span class="agency-tab-badge" id="registeredTotal">({{ @$registeredTotalCount ?: 0 }})</span>
                    </button>
                    <button type="button" class="agency-tab-btn agency-tab-link" data-tab="assigned" href="#tab_assigned">
                        <i class="fas fa-user-check me-1"></i>
                        <span>Assigned</span>
                        <span class="agency-tab-badge" id="assignedTotal">({{ @$assignedTotalCount ?: 0 }})</span>
                    </button>
                </div>

                {{-- Right Sub-Card: Select All Agencies Toggle --}}
                <div class="agency-select-all-subcard">
                    <div class="form-check form-check-custom form-check-solid form-check-sm">
                        <input class="form-check-input select-all-agencies-checkbox" type="checkbox" id="select_all_agencies_active" title="Select All Visible Agencies" style="cursor: pointer;" />
                        <label class="form-check-label fs-8 fw-semibold text-gray-700 ms-1" for="select_all_agencies_active" style="cursor: pointer; user-select: none;">
                            Select All
                        </label>
                    </div>
                </div>
            </div>

            {{-- ── TIER 3: PRIMARY AGENCY CARDS STREAM CONTAINER ── --}}
            <div class="tab-content mt-1">

                {{-- ── TAB 1: RECOMMENDED AGENCIES ── --}}
                <div class="tab-pane active show" id="tab_recommended" role="tabpanel" style="display: block;">
                    <div class="agency-stream-container">
                        <div id="recommendedlistBody" class="agency-cards-stream">
                            {{-- Loaded dynamically via tableLoad(1) --}}
                        </div>
                        <div id="recommended_loader" class="table-loader-wrap">
                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                            <span class="ms-2 fs-7 text-muted">Loading recommended agencies...</span>
                        </div>
                        <div id="paginationrecommendedagency" class="mt-2"></div>
                    </div>
                </div>

                {{-- ── TAB 2: MY AGENCIES ── --}}
                <div class="tab-pane" id="tab_registered" role="tabpanel" style="display: none;">
                    <div class="agency-stream-container">
                        <div id="allregisteragency" class="agency-cards-stream">
                            {{-- Loaded dynamically via staffingPartnersRegister(1) --}}
                        </div>
                        <div id="paginationregisteragency" class="mt-2"></div>
                    </div>
                </div>

                {{-- ── TAB 3: ASSIGNED AGENCIES ── --}}
                <div class="tab-pane" id="tab_assigned" role="tabpanel" style="display: none;">
                    <div class="agency-stream-container">
                        <div id="allAssignListagency" class="agency-cards-stream">
                            {{-- Loaded dynamically via recommendedAssignList(1) --}}
                        </div>
                        <div id="paginationAssignListagency" class="mt-2"></div>
                    </div>
                </div>

            </div>

            {{-- ── MULTI-SEND REQUIREMENT ACTION CARD ── --}}
            <div class="bottom-multi-agency-banner">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 34px; height: 34px; border-radius: 8px; background: #ffffff; border: 1px solid #bfdbfe; color: #0066ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 12px; color: #0f172a;">Send to multiple agencies</div>
                        <div style="font-size: 10.5px; color: #64748b;">Select checkboxes above to dispatch this requirement to multiple agencies simultaneously.</div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-send-multi-agencies" id="btn_send_multi_agencies" disabled style="background: #dbeafe; color: #2563eb; font-size: 11.5px; font-weight: 600; border-radius: 6px; padding: 7px 16px; border: none; cursor: not-allowed; display: flex; align-items: center; gap: 6px; transition: all 0.2s ease;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                    <span id="btn_send_multi_text">Send to Multiple Agencies</span>
                </button>
            </div>

        </div>

    </div>
</div>

{{-- =========================================================================
     FILTER DRAWER (FIND TRUSTED HIRING AGENCIES - SLIDE-OVER DRAWER)
     ========================================================================= --}}
<!-- Backdrop Overlay -->
<div id="agency_filter_drawer_overlay" class="agency-filter-drawer-overlay"></div>

<!-- Slide-Over Filter Drawer -->
<div id="apptrackerfilter" class="agency-filter-drawer" tabindex="-1">
    <!-- Header -->
    <div class="agency-filter-drawer-header">
        <div class="d-flex align-items-center gap-3">
            <div class="afd-header-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                </svg>
            </div>
            <div>
                <h3 class="afd-title">Find Trusted Hiring Agencies</h3>
                <p class="afd-subtitle">Discover verified recruitment partners based on requirements.</p>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-icon afd-close-btn" id="apptrackerfilter_close" title="Close Filter">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>

    <!-- Body (Scrollable) -->
    <div class="agency-filter-drawer-body">
        <!-- Trust bar -->
        <div class="afd-trust-bar">
            <div class="afd-trust-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="#10b981"><path d="M12 2L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-3z"/></svg>
                <span>Verified agencies</span>
            </div>
            <div class="afd-trust-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="#f59e0b"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                <span>Quality checked</span>
            </div>
            <div class="afd-trust-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="#2563eb"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                <span>Fast matching</span>
            </div>
        </div>

        <!-- 1. Search Skills / Keywords -->
        <div class="afd-form-group">
            <label class="afd-form-label">
                <span class="afd-label-num">1</span>
                Search Skills / Keywords
            </label>
            <input class="form-control form-control-solid fs-7" name="kt_tagify_users" value="{{ @$default_skill }}" placeholder="Type skill and press Enter..." id="kt_tagify_users" />
            <span class="fs-9 text-muted mt-1 d-block">Press Enter or comma to add a skill keyword</span>
        </div>

        <!-- 2. Hiring Type -->
        <div class="afd-form-group">
            <label class="afd-form-label">
                <span class="afd-label-num">2</span>
                Hiring Type
            </label>
            <div class="d-flex gap-3">
                <div class="p-3 border rounded flex-fill cursor-pointer filter-hiring-box active" data-type="2" style="border-color: #2563eb !important; background: #eff6ff;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <div class="fw-bold fs-7 text-primary">Full-Time Hiring</div>
                        <span class="badge badge-light-primary fs-9 px-2 py-0">Permanent</span>
                    </div>
                    <div class="fs-8 text-muted">Permanent hiring for full-time requirements.</div>
                </div>
                <div class="p-3 border rounded flex-fill cursor-pointer filter-hiring-box" data-type="1" style="border-color: #e2e8f0; background: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <div class="fw-bold fs-7 text-gray-800">Contract Hiring</div>
                        <span class="badge badge-light fs-9 px-2 py-0">On-demand</span>
                    </div>
                    <div class="fs-8 text-muted">On-demand & project-based contract hiring.</div>
                </div>
            </div>
        </div>

        <!-- 3. Additional Filters -->
        <div class="afd-form-group">
            <label class="afd-form-label">
                <span class="afd-label-num">3</span>
                Additional Filters
            </label>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="fs-8 text-gray-700 fw-semibold mb-1 d-block">Industry</label>
                    <select class="form-select form-select-solid fs-7" id="industry_filter">
                        <option value="">All Industries</option>
                        @if(!empty($td_industries))
                            @foreach($td_industries as $ind)
                                <option value="{{ $ind->id }}">{{ $ind->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="fs-8 text-gray-700 fw-semibold mb-1 d-block">Experience</label>
                    <select class="form-select form-select-solid fs-7" id="experience_filter">
                        <option value="">All Experience</option>
                        <option value="0–2 years">0–2 years</option>
                        <option value="3–5 years">3–5 years</option>
                        <option value="5–10 years">5–10 years</option>
                        <option value="10+ years">10+ years</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="fs-8 text-gray-700 fw-semibold mb-1 d-block">Location</label>
                    <select class="form-select form-select-solid fs-7" id="country_filter">
                        <option value="">All Locations</option>
                        @if(!empty($cities))
                            @foreach($cities as $ct)
                                <option value="{{ $ct }}">{{ $ct }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer (Sticky) -->
    <div class="agency-filter-drawer-footer">
        <button type="button" class="btn btn-sm afd-reset-btn" id="apptrackerfilter_reset">Reset</button>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-light afd-cancel-btn" id="apptrackerfilter_cancel">Cancel</button>
            <button type="button" class="btn btn-sm btn-primary changetextupdate afd-apply-btn" id="changetextupdate" onclick="newUpdateJobstatus()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                Apply Filters
            </button>
        </div>
    </div>
</div>

{{-- =========================================================================
     JAVASCRIPT LIFECYCLE & STATE MANAGEMENT
     Immediate Execution Architecture (No $(document).ready wrapper)
     ========================================================================= --}}
<script>
    /**
     * Unified Search Terms Extractor:
     * Reads tags from Tagify instance if active (including active text typing in tag input),
     * or falls back to raw input value. Returns comma-delimited skill/search string.
     */
    function getUnifiedSearchTerms() {
        if (window.agencySearchTagify && window.agencySearchTagify.value && Array.isArray(window.agencySearchTagify.value)) {
            var tags = window.agencySearchTagify.value.map(function(t) {
                return typeof t === 'object' && t !== null ? (t.value || '') : String(t);
            }).filter(Boolean);

            var currentTyping = '';
            if (window.agencySearchTagify.DOM && window.agencySearchTagify.DOM.input) {
                currentTyping = (window.agencySearchTagify.DOM.input.textContent || '').trim();
            }
            if (currentTyping && tags.indexOf(currentTyping) === -1) {
                tags.push(currentTyping);
            }
            return tags.join(',');
        }
        var el = document.querySelector("#agency_unified_search");
        return el ? (el.value || '').trim() : '';
    }

    /**
     * Tab Manager: Scoped per jobId to prevent memory leaks and singleton state bugs.
     */
    var AgencyMarketplaceTabManager = {
        initializedTabs: {},
        activeRequests: {},
        searchTimers: {},

        init: function() {
            var jobId = $('#current_job_id').val();
            if (!jobId) return;
            this.initializedTabs[jobId] = this.initializedTabs[jobId] || {};
        },

        switchTab: function(tabKey) {
            var jobId = $('#current_job_id').val();
            if (!jobId) return;

            this.cancelAllRequests();
            this.initializedTabs[jobId] = this.initializedTabs[jobId] || {};

            var curSearch = getUnifiedSearchTerms();
            if (tabKey === 'registered') {
                staffingPartnersRegister(1, curSearch);
            } else if (tabKey === 'assigned') {
                recommendedAssignList(1, curSearch);
            } else if (tabKey === 'recommended') {
                tableLoad(1);
            }
            this.initializedTabs[jobId][tabKey] = true;
        },

        cancelRequest: function(tabKey) {
            if (this.activeRequests[tabKey] && typeof this.activeRequests[tabKey].abort === 'function') {
                try { this.activeRequests[tabKey].abort(); } catch(e) {}
                delete this.activeRequests[tabKey];
            }
        },

        cancelAllRequests: function() {
            for (var key in this.activeRequests) {
                if (this.activeRequests.hasOwnProperty(key)) {
                    this.cancelRequest(key);
                }
            }
        },

        registerRequest: function(tabKey, jqXHR) {
            this.cancelRequest(tabKey);
            this.activeRequests[tabKey] = jqXHR;
        },

        clearRequest: function(tabKey) {
            delete this.activeRequests[tabKey];
        }
    };

    window.agencyTableCache = window.agencyTableCache || {};
    window.agencyCacheOrder = window.agencyCacheOrder || [];

    function setAgencyTableCache(key, data) {
        window.agencyTableCache = window.agencyTableCache || {};
        window.agencyCacheOrder = window.agencyCacheOrder || [];
        window.agencyTableCache[key] = data;
        var idx = window.agencyCacheOrder.indexOf(key);
        if (idx !== -1) {
            window.agencyCacheOrder.splice(idx, 1);
        }
        window.agencyCacheOrder.push(key);
        if (window.agencyCacheOrder.length > 30) {
            var oldest = window.agencyCacheOrder.shift();
            delete window.agencyTableCache[oldest];
        }
    }

    function getAgencyParamsKey(params) {
        return Object.keys(params).sort().map(function(k) {
            return k + '=' + (params[k] !== undefined && params[k] !== null ? params[k] : '');
        }).join('&');
    }

    /**
     * Bulk Agency Selection State Manager
     */
    var activeJobIdForDrawer = String($('#current_job_id').val() || '');
    if (window.currentActiveDrawerJobId !== activeJobIdForDrawer) {
        window.currentActiveDrawerJobId = activeJobIdForDrawer;
        if (window.selectedAgencyIds) {
            window.selectedAgencyIds.clear();
        }
    }
    window.selectedAgencyIds = window.selectedAgencyIds || new Set();

    function updateMultiSendButtonState() {
        if (!window.selectedAgencyIds) {
            window.selectedAgencyIds = new Set();
        }
        var count = window.selectedAgencyIds.size;
        var $btn = $('#btn_send_multi_agencies');
        var $txt = $('#btn_send_multi_text');

        if (count > 0) {
            $txt.text('Send Requirement to ' + count + ' Agenc' + (count > 1 ? 'ies' : 'y'));
            $btn.prop('disabled', false).removeClass('disabled').css({ 'opacity': '1', 'cursor': 'pointer', 'background': '#009ef7', 'color': '#ffffff' });
        } else {
            $txt.text('Send to Multiple Agencies');
            $btn.prop('disabled', true).addClass('disabled').css({ 'opacity': '0.65', 'cursor': 'not-allowed', 'background': '#dbeafe', 'color': '#2563eb' });
        }

        // Update Select-All checkbox state in Tier 2
        var $activePane = $('#kt_activitiesRecruiter .tab-pane.active');
        var $visibleCheckboxes = $activePane.find('.agency-row-checkbox');
        var totalVisible = $visibleCheckboxes.length;
        var checkedVisible = $visibleCheckboxes.filter(':checked').length;
        var $selectAll = $('#kt_activitiesRecruiter .select-all-agencies-checkbox');

        if (totalVisible > 0 && checkedVisible === totalVisible) {
            $selectAll.prop('checked', true).prop('indeterminate', false);
        } else if (checkedVisible > 0) {
            $selectAll.prop('checked', false).prop('indeterminate', true);
        } else {
            $selectAll.prop('checked', false).prop('indeterminate', false);
        }
    }

    function syncCheckboxSelectionState() {
        if (!window.selectedAgencyIds) {
            window.selectedAgencyIds = new Set();
        }
        $('.agency-row-checkbox').each(function() {
            var id = String($(this).val());
            $(this).prop('checked', window.selectedAgencyIds.has(id));
        });
        updateMultiSendButtonState();
    }

    /**
     * Recommended Agencies Loader
     */
    function tableLoad(page) {
        if (page === 'load' || !page) { page = 1; }

        $('#recommended_loader').show();

        var currentJobId = $('#current_job_id').val();
        var cusSearchVal = getUnifiedSearchTerms() || $('#kt_tagify_users').val() || '';

        var params = {
            jobid: currentJobId,
            hiringtype: $('#workhiringtype').val() || '',
            country: $('#country').val() || '',
            cus_search: cusSearchVal,
            min_exp: $('#min_exp').val() || '',
            max_exp: $('#max_exp').val() || '',
            app_int_outsource: $('#hiring_type').val() || '',
            experience: $('#experience').val() || '',
            industry: $('#industry').val() || '',
            min_sal: $('#min_sal').val() || '',
            max_sal: $('#max_sal').val() || '',
            min_salary_currency: $('#min_salary_currency').val() || '',
            sal_mode: $('#sal_mode').val() || ''
        };

        var ramCacheKey = 'recom_' + currentJobId + '_p' + page + '_' + getAgencyParamsKey(params);
        var hasRep = !!(window.agencyTableCache && window.agencyTableCache[ramCacheKey]);

        var jqXHR = $.ajax({
            url: `{!! route('service_company.jobs.recommendedData') !!}?page=${page}`,
            type: 'GET',
            cache: false,
            headers: {
                'X-Has-Representation': hasRep ? '1' : '0'
            },
            data: params,
            success: function(response) {
                $('#recommended_loader').hide();

                if (response && response.html) {
                    setAgencyTableCache(ramCacheKey, response);
                    $('#recommendedlistBody').html(response.html);
                    if (response.total !== undefined) {
                        $('#recommendedTotal').html('(' + response.total + ')');
                    }
                    if (response.pagination) {
                        $('#paginationrecommendedagency').html(response.pagination);
                    } else {
                        $('#paginationrecommendedagency').html('');
                    }
                    syncCheckboxSelectionState();
                } else if (hasRep && window.agencyTableCache[ramCacheKey]) {
                    var cached = window.agencyTableCache[ramCacheKey];
                    $('#recommendedlistBody').html(cached.html);
                    if (cached.total !== undefined) {
                        $('#recommendedTotal').html('(' + cached.total + ')');
                    }
                    if (cached.pagination) {
                        $('#paginationrecommendedagency').html(cached.pagination);
                    } else {
                        $('#paginationrecommendedagency').html('');
                    }
                    syncCheckboxSelectionState();
                } else {
                    var fallbackParams = $.extend({}, params, { _force: 1 });
                    $.getJSON(`{!! route('service_company.jobs.recommendedData') !!}?page=${page}`, fallbackParams, function(fresh) {
                        if (fresh && fresh.html) {
                            setAgencyTableCache(ramCacheKey, fresh);
                            $('#recommendedlistBody').html(fresh.html);
                            if (fresh.total !== undefined) {
                                $('#recommendedTotal').html('(' + fresh.total + ')');
                            }
                            if (fresh.pagination) {
                                $('#paginationrecommendedagency').html(fresh.pagination);
                            } else {
                                $('#paginationrecommendedagency').html('');
                            }
                            syncCheckboxSelectionState();
                        }
                    });
                }
            },
            error: function() {
                $('#recommended_loader').hide();
                if (hasRep && window.agencyTableCache[ramCacheKey]) {
                    var cached = window.agencyTableCache[ramCacheKey];
                    $('#recommendedlistBody').html(cached.html);
                    if (cached.total !== undefined) {
                        $('#recommendedTotal').html('(' + cached.total + ')');
                    }
                    syncCheckboxSelectionState();
                }
            },
            complete: function() {
                AgencyMarketplaceTabManager.clearRequest('recommended');
            }
        });
        AgencyMarketplaceTabManager.registerRequest('recommended', jqXHR);
        return jqXHR;
    }

    /**
     * My Agencies (Registered) Loader
     */
    function staffingPartnersRegister(page, curSearch) {
        if (page === 'load' || !page) { page = 1; }

        var currentJobId = $('#current_job_id').val();
        var searchTerms = (curSearch !== undefined) ? curSearch : (getUnifiedSearchTerms() || $('#kt_tagify_4').val() || '');

        var params = {
            jobid: currentJobId,
            job_id: currentJobId,
            search: searchTerms,
            cus_search: searchTerms,
            hiringtype: $('#workhiringtype').val() || '',
            country: $('#country4').val() || 'Show All'
        };

        var ramCacheKey = 'reg_' + currentJobId + '_p' + page + '_' + getAgencyParamsKey(params);
        var hasRep = !!(window.agencyTableCache && window.agencyTableCache[ramCacheKey]);

        var jqXHR = $.ajax({
            url: `{!! route('service_company.jobs.staffingPartnersRegister') !!}?page=${page}`,
            type: 'GET',
            cache: false,
            headers: {
                'X-Has-Representation': hasRep ? '1' : '0'
            },
            data: params,
            success: function(response) {
                if (response && response.html) {
                    setAgencyTableCache(ramCacheKey, response);
                    $('#allregisteragency').html(response.html);
                    if (response.total !== undefined) {
                        $('#registeredTotal').html('(' + response.total + ')');
                    }
                    if (response.pagination) {
                        $('#paginationregisteragency').html(response.pagination);
                    } else {
                        $('#paginationregisteragency').html('');
                    }
                    syncCheckboxSelectionState();
                } else if (hasRep && window.agencyTableCache[ramCacheKey]) {
                    var cached = window.agencyTableCache[ramCacheKey];
                    $('#allregisteragency').html(cached.html);
                    if (cached.total !== undefined) {
                        $('#registeredTotal').html('(' + cached.total + ')');
                    }
                    if (cached.pagination) {
                        $('#paginationregisteragency').html(cached.pagination);
                    } else {
                        $('#paginationregisteragency').html('');
                    }
                    syncCheckboxSelectionState();
                } else {
                    var fallbackParams = $.extend({}, params, { _force: 1 });
                    $.getJSON(`{!! route('service_company.jobs.staffingPartnersRegister') !!}?page=${page}`, fallbackParams, function(fresh) {
                        if (fresh && fresh.html) {
                            setAgencyTableCache(ramCacheKey, fresh);
                            $('#allregisteragency').html(fresh.html);
                            if (fresh.total !== undefined) {
                                $('#registeredTotal').html('(' + fresh.total + ')');
                            }
                            if (fresh.pagination) {
                                $('#paginationregisteragency').html(fresh.pagination);
                            }
                            syncCheckboxSelectionState();
                        }
                    });
                }
            },
            complete: function() {
                AgencyMarketplaceTabManager.clearRequest('registered');
            }
        });
        AgencyMarketplaceTabManager.registerRequest('registered', jqXHR);
        return jqXHR;
    }

    /**
     * Assigned Agencies Loader
     */
    function recommendedAssignList(page, curSearch) {
        if (page === 'load' || !page) { page = 1; }

        var currentJobId = $('#current_job_id').val();
        var searchTerms = (curSearch !== undefined) ? curSearch : (getUnifiedSearchTerms() || $('#kt_tagify_5').val() || '');

        var params = {
            jobid: currentJobId,
            job_id: currentJobId,
            search: searchTerms,
            cus_search: searchTerms,
            hiringtype: $('#hiringtype4').val() || '0'
        };

        var ramCacheKey = 'assign_' + currentJobId + '_p' + page + '_' + getAgencyParamsKey(params);
        var hasRep = !!(window.agencyTableCache && window.agencyTableCache[ramCacheKey]);

        var jqXHR = $.ajax({
            url: `{!! route('service_company.jobs.recommendedAssignList') !!}?page=${page}`,
            type: 'GET',
            cache: false,
            headers: {
                'X-Has-Representation': hasRep ? '1' : '0'
            },
            data: params,
            success: function(response) {
                if (response && response.html) {
                    setAgencyTableCache(ramCacheKey, response);
                    $('#allAssignListagency').html(response.html);
                    if (response.total !== undefined) {
                        $('#assignedTotal').html('(' + response.total + ')');
                    }
                    if (response.pagination) {
                        $('#paginationAssignListagency').html(response.pagination);
                    } else {
                        $('#paginationAssignListagency').html('');
                    }
                } else if (hasRep && window.agencyTableCache[ramCacheKey]) {
                    var cached = window.agencyTableCache[ramCacheKey];
                    $('#allAssignListagency').html(cached.html);
                    if (cached.total !== undefined) {
                        $('#assignedTotal').html('(' + cached.total + ')');
                    }
                    if (cached.pagination) {
                        $('#paginationAssignListagency').html(cached.pagination);
                    }
                } else {
                    var fallbackParams = $.extend({}, params, { _force: 1 });
                    $.getJSON(`{!! route('service_company.jobs.recommendedAssignList') !!}?page=${page}`, fallbackParams, function(fresh) {
                        if (fresh && fresh.html) {
                            setAgencyTableCache(ramCacheKey, fresh);
                            $('#allAssignListagency').html(fresh.html);
                            if (fresh.total !== undefined) {
                                $('#assignedTotal').html('(' + fresh.total + ')');
                            }
                            if (fresh.pagination) {
                                $('#paginationAssignListagency').html(fresh.pagination);
                            }
                        }
                    });
                }
            },
            complete: function() {
                AgencyMarketplaceTabManager.clearRequest('assigned');
            }
        });
        AgencyMarketplaceTabManager.registerRequest('assigned', jqXHR);
        return jqXHR;
    }

    /**
     * Filter Drawer: Apply filters callback
     */
    function newUpdateJobstatus() {
        $('#industry').val($('#industry_filter').val());
        $('#experience').val($('#experience_filter').val());
        $('#country').val($('#country_filter').val());
        $('#workhiringtype').val($('#hiring_type').val());

        var filterSkill = '';
        if (window.filterSkillsTagify && window.filterSkillsTagify.value && Array.isArray(window.filterSkillsTagify.value)) {
            filterSkill = window.filterSkillsTagify.value.map(function(t) {
                return typeof t === 'object' && t !== null ? (t.value || '') : String(t);
            }).filter(Boolean).join(',');
        } else {
            filterSkill = $('#kt_tagify_users').val() || '';
        }

        if (filterSkill) {
            if (window.agencySearchTagify) {
                window.agencySearchTagify.removeAllTags();
                window.agencySearchTagify.addTags(filterSkill);
            } else {
                $('#agency_unified_search').val(filterSkill);
            }
        }

        closeAgencyFilterDrawer();
        tableLoad(1);
    }

    function openAgencyFilterDrawer() {
        var terms = getUnifiedSearchTerms();
        if (terms) {
            if (window.filterSkillsTagify) {
                try {
                    window.filterSkillsTagify.removeAllTags();
                    window.filterSkillsTagify.addTags(terms);
                } catch(e) {}
            } else {
                $('#kt_tagify_users').val(terms);
            }
        }
        $('#agency_filter_drawer_overlay, #apptrackerfilter').addClass('open');
    }

    function closeAgencyFilterDrawer() {
        $('#agency_filter_drawer_overlay, #apptrackerfilter').removeClass('open');
    }

    /**
     * IMMEDIATE EXECUTION: Binds all events and initializes state immediately upon drawer injection.
     */
    (function() {
        // Enforce mutually exclusive marketplace state immediately on injection
        function setMarketplaceUiState(isLive) {
            if (isLive) {
                $('#marketplace_unpublished_box').addClass('d-none').attr('style', 'display: none !important;');
                $('#marketplace_live_box').removeClass('d-none').attr('style', 'display: block !important;');
                $('#sp_list_one').addClass('d-none').attr('style', 'display: none !important;');
                $('#sp_list_two').removeClass('d-none').attr('style', 'display: flex !important;');
            } else {
                $('#marketplace_live_box').addClass('d-none').attr('style', 'display: none !important;');
                $('#marketplace_unpublished_box').removeClass('d-none').attr('style', 'display: block !important;');
                $('#sp_list_two').addClass('d-none').attr('style', 'display: none !important;');
                $('#sp_list_one').removeClass('d-none').attr('style', 'display: flex !important;');
            }
        }

        var initialIsLive = {{ $isPublished ? 'true' : 'false' }};
        setMarketplaceUiState(initialIsLive);

        // Initialize Tab Manager
        AgencyMarketplaceTabManager.init();

        // Unified Search Input with Tagify & Debounce (300ms)
        var searchTimeout = null;
        function triggerUnifiedSearch() {
            clearTimeout(searchTimeout);
            var searchVal = getUnifiedSearchTerms();
            if (searchVal.length > 0) {
                $('#btn_agency_search_clear').show();
            } else {
                $('#btn_agency_search_clear').hide();
            }

            searchTimeout = setTimeout(function() {
                var terms = getUnifiedSearchTerms();
                var activeTab = $('#kt_activitiesRecruiter .agency-tab-link.active').data('tab') || 'recommended';
                if (activeTab === 'registered') {
                    $('#kt_tagify_4').val(terms);
                    staffingPartnersRegister(1, terms);
                } else if (activeTab === 'assigned') {
                    $('#kt_tagify_5').val(terms);
                    recommendedAssignList(1, terms);
                } else {
                    tableLoad(1);
                }
            }, 300);
        }

        // Initialize Tagify on #agency_unified_search
        var tagifySearchEl = document.querySelector("#agency_unified_search");
        if (tagifySearchEl && typeof Tagify !== 'undefined') {
            if (window.agencySearchTagify) {
                try { window.agencySearchTagify.destroy(); } catch(e) {}
                window.agencySearchTagify = null;
            }
            if (tagifySearchEl.__tagify) {
                try { tagifySearchEl.__tagify.destroy(); } catch(e) {}
            }
            window.agencySearchTagify = new Tagify(tagifySearchEl, {
                delimiters: ",",
                trim: true,
                placeholder: "Search by skills...",
                dropdown: { enabled: 0 }
            });

            window.agencySearchTagify.on('add', triggerUnifiedSearch);
            window.agencySearchTagify.on('remove', triggerUnifiedSearch);
            window.agencySearchTagify.on('change', triggerUnifiedSearch);
            window.agencySearchTagify.on('input', triggerUnifiedSearch);
        }

        // Trigger initial data load
        tableLoad(1);

        // Clear search button trigger
        $(document).off('click.agencySearchClear', '#btn_agency_search_clear').on('click.agencySearchClear', '#btn_agency_search_clear', function(e) {
            e.preventDefault();
            if (window.agencySearchTagify) {
                window.agencySearchTagify.removeAllTags();
            }
            $('#agency_unified_search').val('');
            $(this).hide();
            triggerUnifiedSearch();
        });

        // Tab Switching Click Listeners (Strictly scoped within #kt_activitiesRecruiter)
        $(document).off('click.agencyTabs', '#kt_activitiesRecruiter .agency-tab-link').on('click.agencyTabs', '#kt_activitiesRecruiter .agency-tab-link', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('#kt_activitiesRecruiter .agency-tab-link').removeClass('active');
            $(this).addClass('active');

            var tabKey = $(this).data('tab');
            var targetPane = $(this).attr('href');

            $('#kt_activitiesRecruiter .tab-pane').removeClass('active show').hide();
            $('#kt_activitiesRecruiter ' + targetPane).addClass('active show').show();

            AgencyMarketplaceTabManager.switchTab(tabKey);
            setTimeout(updateMultiSendButtonState, 50);
        });

        // ── AGENCY ROW SELECTION & MULTI-SEND MANAGEMENT ────────────────────
        $(document).off('change.agencyRowCheck', '.agency-row-checkbox').on('change.agencyRowCheck', '.agency-row-checkbox', function(e) {
            var val = String($(this).val());
            if ($(this).is(':checked')) {
                window.selectedAgencyIds.add(val);
            } else {
                window.selectedAgencyIds.delete(val);
            }
            updateMultiSendButtonState();
        });

        $(document).off('change.selectAllAgencies', '.select-all-agencies-checkbox').on('change.selectAllAgencies', '.select-all-agencies-checkbox', function(e) {
            var isChecked = $(this).is(':checked');
            var $activePane = $(this).closest('.tab-pane');
            if (!$activePane.length) {
                $activePane = $('#kt_activitiesRecruiter .tab-pane.active');
            }

            $activePane.find('.agency-row-checkbox').each(function() {
                var val = String($(this).val());
                $(this).prop('checked', isChecked);
                if (isChecked) {
                    window.selectedAgencyIds.add(val);
                } else {
                    window.selectedAgencyIds.delete(val);
                }
            });
            updateMultiSendButtonState();
        });

        $(document).off('click.sendMultiAgencies', '#btn_send_multi_agencies').on('click.sendMultiAgencies', '#btn_send_multi_agencies', function(e) {
            e.preventDefault();
            var $btn = $(this);
            if ($btn.data('in-flight') || !window.selectedAgencyIds || window.selectedAgencyIds.size === 0) {
                return false;
            }

            var agencyIds = Array.from(window.selectedAgencyIds);
            var currentJobId = $('#current_job_id').val();
            var agencyCount = agencyIds.length;

            Swal.fire({
                title: 'Send Requirement to ' + agencyCount + ' Agenc' + (agencyCount > 1 ? 'ies' : 'y') + '?',
                text: 'This requirement invitation will be sent to the ' + agencyCount + ' selected agencies at once.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Send to All (' + agencyCount + ')',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b'
            }).then(function(result) {
                if (!result.isConfirmed) return;

                $btn.data('in-flight', true).prop('disabled', true).addClass('disabled');
                var originalHtml = $btn.html();
                $btn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Sending...');

                $.ajax({
                    url: "{{ route('service_company.jobs.send_multiple_job_invitations') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-Idempotency-Key': 'bulk-send-' + currentJobId + '-' + Date.now()
                    },
                    data: {
                        job_id: currentJobId,
                        agency_ids: agencyIds
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            var successfulIds = response.invited_agencies || agencyIds;
                            successfulIds.forEach(function(agId) {
                                $('#agency_action_' + agId).html(
                                    '<span class="ag-badge-sent"><svg width="13" height="13" viewBox="0 0 24 24" fill="#15803d"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> <span>Request Sent</span></span>'
                                );
                                $('#agency_row_' + agId + ' .agency-row-checkbox').parent().replaceWith('<div class="ag-check-spacer"><svg width="15" height="15" viewBox="0 0 24 24" fill="#10b981"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg></div>');
                            });

                            window.selectedAgencyIds.clear();
                            $('.select-all-agencies-checkbox').prop('checked', false).prop('indeterminate', false);

                            window.agencyTableCache = {};
                            if (window.jobDrawerCache) {
                                delete window.jobDrawerCache['spartner_' + currentJobId];
                            }

                            Swal.fire({
                                title: "Success!",
                                text: response.message || ("Requirement successfully sent to " + successfulIds.length + " agencies."),
                                icon: "success",
                                confirmButtonColor: "#10b981"
                            });
                        } else {
                            Swal.fire("Notice", response.message || "Failed to send requirement to selected agencies.", "warning");
                        }
                    },
                    error: function(xhr) {
                        var errMsg = "Failed to send requirements. Please try again.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        }
                        Swal.fire("Error", errMsg, "error");
                    },
                    complete: function() {
                        $btn.data('in-flight', false).html(originalHtml);
                        updateMultiSendButtonState();
                    }
                });
            });
        });

        // Fallback input event for non-Tagify scenarios
        $(document).off('input.unifiedSearch', '#agency_unified_search').on('input.unifiedSearch', '#agency_unified_search', triggerUnifiedSearch);

        // Drawer Close Handlers (Back button & Close icon)
        $(document).off('click.drawerCloseTrigger', '.drawer-close-trigger').on('click.drawerCloseTrigger', '.drawer-close-trigger', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var drawerEl = document.getElementById('kt_activitiesRecruiter');
            if (drawerEl && typeof KTDrawer !== 'undefined') {
                var drawerInstance = KTDrawer.getInstance(drawerEl);
                if (drawerInstance) {
                    drawerInstance.hide();
                }
            }
            $('#kt_activitiesRecruiter_close').trigger('click');
            $('body').removeClass('drawer-on');
            $('#kt_activitiesRecruiter').removeClass('drawer-on');
            $('.drawer-overlay').remove();
        });

        // ── Assigned Agency: three-dot menu dropdown ─────────────────────
        $(document).off('click.aaDots', '.aa-dots-btn').on('click.aaDots', '.aa-dots-btn', function(e) {
            e.stopPropagation();
            var $menu = $(this).next('.aa-ddmenu');
            var wasOpen = $menu.hasClass('open');
            $('.aa-ddmenu.open').removeClass('open');
            if (!wasOpen) { $menu.addClass('open'); }
        });
        $(document).off('click.aaDotsClose').on('click.aaDotsClose', function() {
            $('.aa-ddmenu.open').removeClass('open');
        });

        // Initialize Tagify on #kt_tagify_users in Filter Drawer
        var filterTagifyEl = document.querySelector("#kt_tagify_users");
        if (filterTagifyEl && typeof Tagify !== 'undefined') {
            if (window.filterSkillsTagify) {
                try { window.filterSkillsTagify.destroy(); } catch(e) {}
                window.filterSkillsTagify = null;
            }
            if (filterTagifyEl.__tagify) {
                try { filterTagifyEl.__tagify.destroy(); } catch(e) {}
            }
            window.filterSkillsTagify = new Tagify(filterTagifyEl, {
                delimiters: ",",
                trim: true,
                placeholder: "Type skill and press Enter...",
                dropdown: { enabled: 0 }
            });
        }

        // Filter drawer open & close triggers
        $(document).off('click.filterTrigger', '.btn-filter-trigger').on('click.filterTrigger', '.btn-filter-trigger', function(e) {
            e.preventDefault();
            e.stopPropagation();
            openAgencyFilterDrawer();
        });

        $(document).off('click.filterClose', '#apptrackerfilter_close, #apptrackerfilter_cancel, #agency_filter_drawer_overlay, [data-dismiss="modal"], [data-bs-dismiss="modal"]').on('click.filterClose', '#apptrackerfilter_close, #apptrackerfilter_cancel, #agency_filter_drawer_overlay, [data-dismiss="modal"], [data-bs-dismiss="modal"]', function(e) {
            e.preventDefault();
            closeAgencyFilterDrawer();
        });

        // Filter drawer reset button
        $(document).off('click.filterReset', '#apptrackerfilter_reset').on('click.filterReset', '#apptrackerfilter_reset', function(e) {
            e.preventDefault();
            $('#industry_filter').val('');
            $('#experience_filter').val('');
            $('#country_filter').val('');
            $('.filter-hiring-box').removeClass('active').css({ 'border-color': '#e2e8f0', 'background': '#ffffff' });
            $('.filter-hiring-box[data-type="2"]').addClass('active').css({ 'border-color': '#2563eb', 'background': '#eff6ff' });
            $('#hiring_type').val('2');
            if (window.filterSkillsTagify) {
                try { window.filterSkillsTagify.removeAllTags(); } catch(e) {}
            }
            $('#kt_tagify_users').val('');
        });

        // Close on ESC key
        $(document).off('keydown.filterEsc').on('keydown.filterEsc', function(e) {
            if (e.key === 'Escape' && $('#apptrackerfilter').hasClass('open')) {
                closeAgencyFilterDrawer();
            }
        });

        // Filter drawer hiring type toggle
        $(document).off('click.filterHiring', '.filter-hiring-box').on('click.filterHiring', '.filter-hiring-box', function() {
            $('.filter-hiring-box').removeClass('active').css({ 'border-color': '#e2e8f0', 'background': '#ffffff' });
            $(this).addClass('active').css({ 'border-color': '#2563eb', 'background': '#eff6ff' });
            $('#hiring_type').val($(this).data('type'));
        });

        // ── PUBLISH / UNPUBLISH TOGGLE HANDLER ───────────────────────────────
        $('body').off('click.postToPublic', '.post_to_public').on('click.postToPublic', '.post_to_public', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var $this = $(this);
            if ($this.data('in-flight') || $('#kt_activitiesRecruiter').data('market-action-running')) return false;

            var current_jobid = $this.data('row-jobid') || $('#current_job_id').val();
            var job_cancel = parseInt($this.data('c') || 0, 10);
            var id = 0;
            var url = "{{ route('service_company.jobs.auction_send_job_request', ':id') }}".replace(':id', id);

            Swal.fire({
                title: job_cancel == 1 ? 'Unpublish from Marketplace?' : 'Publish to Agency Marketplace?',
                text: job_cancel == 1
                    ? 'This requirement will no longer be visible to agencies on Jero Marketplace.'
                    : 'This requirement will become visible to all verified recruitment agencies on Jero Marketplace.',
                icon: job_cancel == 1 ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonText: job_cancel == 1 ? 'Yes, Unpublish' : 'Yes, Publish Job',
                cancelButtonText: 'Cancel',
                confirmButtonColor: job_cancel == 1 ? '#dc2626' : '#009ef7',
                cancelButtonColor: '#64748b'
            }).then((result) => {
                if (!result.isConfirmed) return;

                $this.data('in-flight', true).prop('disabled', true);
                $('#kt_activitiesRecruiter').data('market-action-running', true);

                var vendorLimit = $('#marketplace_vendor_limit').val() || 20;
                var autoExpire = $('#marketplace_auto_expire').val() || 7;

                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        'current_jobid': current_jobid,
                        'job_cancel': job_cancel,
                        'vendor_limit': vendorLimit,
                        'auto_expire': autoExpire
                    },
                    success: function(response) {
                        if (response.status === 'success' || response.status === true) {
                            if (job_cancel === 0) {
                                setMarketplaceUiState(true);
                            } else {
                                setMarketplaceUiState(false);
                            }

                            window.agencyTableCache = {};
                            if (window.jobDrawerCache) {
                                delete window.jobDrawerCache['spartner_' + current_jobid];
                            }

                            Swal.fire({
                                title: "Success!",
                                text: response.message || (job_cancel == 1 ? "Job successfully unpublished from marketplace." : "Job successfully published to Agency Marketplace."),
                                icon: "success",
                                confirmButtonColor: "#009ef7"
                            });
                        } else {
                            Swal.fire("Notice", response.message || "Failed to update marketplace status.", "warning");
                        }
                    },
                    error: function() {
                        Swal.fire("Error", "Server error while updating marketplace status. Please try again.", "error");
                    },
                    complete: function() {
                        $this.data('in-flight', false).prop('disabled', false);
                        $('#kt_activitiesRecruiter').data('market-action-running', false);
                    }
                });
            });
        });

        // ── VENDOR LIMIT & AUTO-EXPIRE PERSISTENCE ON CHANGE ──────────────────
        $(document).off('change.vendorLimit', '#marketplace_vendor_limit').on('change.vendorLimit', '#marketplace_vendor_limit', function() {
            var $sel = $(this);
            var limitVal = $sel.val();
            var current_jobid = $('#current_job_id').val();
            if (!current_jobid) return;

            var url = "{{ route('service_company.jobs.auction_send_job_request', ':id') }}".replace(':id', 0);
            $.ajax({
                type: 'POST',
                url: url,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: {
                    current_jobid: current_jobid,
                    action: 'update_settings',
                    vendor_limit: limitVal,
                    _token: '{{ csrf_token() }}'
                },
                success: function(resp) {
                    if (resp && resp.status === 'success') {
                        $sel.data('current', limitVal);
                    }
                }
            });
        });

        $(document).off('change.autoExpire', '#marketplace_auto_expire').on('change.autoExpire', '#marketplace_auto_expire', function() {
            var $sel = $(this);
            var expireVal = $sel.val();
            var current_jobid = $('#current_job_id').val();
            if (!current_jobid) return;

            var url = "{{ route('service_company.jobs.auction_send_job_request', ':id') }}".replace(':id', 0);
            $.ajax({
                type: 'POST',
                url: url,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: {
                    current_jobid: current_jobid,
                    action: 'update_settings',
                    auto_expire: expireVal,
                    _token: '{{ csrf_token() }}'
                },
                success: function(resp) {
                    if (resp && resp.status === 'success') {
                        $sel.data('current', expireVal);
                    }
                }
            });
        });

        // ── SINGLE AGENCY SEND / RESEND / REMOVE ─────────────────────────────
        $('body').off('click.sendJobInvitation', '.send_only_job_invitation').on('click.sendJobInvitation', '.send_only_job_invitation', function(e) {
            e.preventDefault();
            var ele = $(this);
            if (ele.data('in-flight')) return false;
            ele.data('in-flight', true).prop('disabled', true).addClass('disabled');

            var fromcompany = ele.data('row-fromcompany');
            var tocompany = ele.data('row-tocompany');
            var id = ele.data('row-jobid') || $('#current_job_id').val();
            var url = "{{ route('service_company.jobs.send_only_job_invitation', ':id') }}".replace(':id', id);

            $.ajax({
                type: 'GET',
                url: url,
                data: {
                    'jobid': id,
                    'fromcompany': fromcompany,
                    'tocompany': tocompany
                },
                success: function(response) {
                    if (response.status == "success") {
                        window.agencyTableCache = {};
                        if (window.jobDrawerCache) {
                            delete window.jobDrawerCache['spartner_' + id];
                        }
                        if (window.selectedAgencyIds && tocompany) {
                            window.selectedAgencyIds.delete(String(tocompany));
                            $('#agency_row_' + tocompany + ' .agency-row-checkbox').parent().replaceWith('<div class="ag-check-spacer"><svg width="15" height="15" viewBox="0 0 24 24" fill="#10b981"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg></div>');
                            updateMultiSendButtonState();
                        }
                        ele.replaceWith('<span class="ag-badge-sent"><svg width="13" height="13" viewBox="0 0 24 24" fill="#15803d"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> <span>Request Sent</span></span>');
                        Swal.fire("Success", response.message || "Requirement sent to agency successfully.", "success");
                    } else {
                        Swal.fire("Notice", response.message || "Could not send requirement.", "warning");
                    }
                },
                error: function() {
                    Swal.fire("Error", "Could not send requirement. Please try again.", "error");
                },
                complete: function() {
                    ele.data('in-flight', false).prop('disabled', false).removeClass('disabled');
                }
            });
        });

        $('body').off('click.sendJobRecom', '.send_job_recommanded_request').on('click.sendJobRecom', '.send_job_recommanded_request', function(e) {
            e.preventDefault();
            var $this = $(this);
            if ($this.data('in-flight')) return false;
            $this.data('in-flight', true).prop('disabled', true).addClass('disabled');

            var from_company_id = $this.data('row-id');
            var current_jobid = $this.data('row-jobid') || $('#current_job_id').val();
            var url = "{{ route('service_company.jobs.send_job_recommanded_request', ':id') }}".replace(':id', from_company_id);

            $.ajax({
                type: 'GET',
                url: url,
                data: {
                    'from_company_id': from_company_id,
                    'current_jobid': current_jobid
                },
                success: function(response) {
                    if (response.status === "success") {
                        window.agencyTableCache = {};
                        if (window.jobDrawerCache) {
                            delete window.jobDrawerCache['spartner_' + current_jobid];
                        }
                        if (window.selectedAgencyIds && from_company_id) {
                            window.selectedAgencyIds.delete(String(from_company_id));
                            $('#agency_row_' + from_company_id + ' .agency-row-checkbox').parent().replaceWith('<div class="ag-check-spacer"><svg width="15" height="15" viewBox="0 0 24 24" fill="#10b981"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg></div>');
                            updateMultiSendButtonState();
                        }
                        $this.replaceWith('<span class="ag-badge-sent"><svg width="13" height="13" viewBox="0 0 24 24" fill="#15803d"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> <span>Request Sent</span></span>');
                        Swal.fire("Success", response.message || "Requirement sent to agency successfully.", "success");
                    } else {
                        Swal.fire("Notice", response.message || "Could not send requirement.", "warning");
                    }
                },
                error: function() {
                    Swal.fire("Error", "Could not send requirement. Please try again.", "error");
                },
                complete: function() {
                    $this.data('in-flight', false).prop('disabled', false).removeClass('disabled');
                }
            });
        });

        $('body').off('click.resendReq', '.resend_job_recommanded_request').on('click.resendReq', '.resend_job_recommanded_request', function(e) {
            e.preventDefault();
            var $this = $(this);
            if ($this.data('in-flight')) return false;
            $this.data('in-flight', true).prop('disabled', true).addClass('disabled');

            var to_company_id = $this.data('row-id');
            var current_jobid = $this.data('row-jobid') || $('#current_job_id').val();
            var ename = $this.data('row-ename');
            var url = "{{ route('service_company.jobs.resend_job_recommanded_request', ':id') }}".replace(':id', to_company_id);

            $.ajax({
                type: 'GET',
                url: url,
                data: {
                    'to_company_id': to_company_id,
                    'current_jobid': current_jobid,
                    'ename': ename
                },
                success: function(response) {
                    window.agencyTableCache = {};
                    if (window.jobDrawerCache) {
                        delete window.jobDrawerCache['spartner_' + current_jobid];
                    }
                    if (window.selectedAgencyIds && to_company_id) {
                        window.selectedAgencyIds.delete(String(to_company_id));
                        updateMultiSendButtonState();
                    }
                    tableLoad('load');
                    if (response.status === "success") {
                        Swal.fire("Success", response.message || "Requirement resent successfully.", "success");
                    }
                },
                complete: function() {
                    $this.data('in-flight', false).prop('disabled', false).removeClass('disabled');
                }
            });
        });

        // ── REMOVE / IGNORE ACTIONS ──────────────────────────────────────────
        $('body').off('click.removeJob', '.remove_job').on('click.removeJob', '.remove_job', function(e) {
            e.preventDefault();
            var $this = $(this);
            if ($this.data('in-flight')) return false;

            var jobid = $this.data('row-jobid') || $('#current_job_id').val();
            var fromcompany = $this.data('row-fromcompany');
            var tocompany = $this.data('row-tocompany');

            Swal.fire({
                title: 'Remove Agency?',
                text: "This agency will be removed from this requirement.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, remove',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#ef4444'
            }).then((result) => {
                if (!result.isConfirmed) return;

                $this.data('in-flight', true).prop('disabled', true);
                var url = "{{ route('service_company.jobs.removeJob') }}";

                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        'jobid': jobid,
                        'fromcompany': fromcompany,
                        'tocompany': tocompany
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            window.agencyTableCache = {};
                            if (window.jobDrawerCache) {
                                delete window.jobDrawerCache['spartner_' + jobid];
                            }
                            tableLoad('load');
                            staffingPartnersRegister('load');
                            recommendedAssignList('load');
                            Swal.fire('Removed', 'Agency removed successfully.', 'success');
                        }
                    },
                    complete: function() {
                        $this.data('in-flight', false).prop('disabled', false);
                    }
                });
            });
        });

        $('body').off('click.removeAssignJob', '.remove_assign_job').on('click.removeAssignJob', '.remove_assign_job', function(e) {
            e.preventDefault();
            var $this = $(this);
            if ($this.data('in-flight')) return false;

            var id = $this.data('row-id');
            var jobid = $this.data('row-jobid') || $('#current_job_id').val();

            Swal.fire({
                title: 'Ignore Agency Request?',
                text: "Selected agency request will be ignored.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, ignore',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#ef4444'
            }).then((result) => {
                if (!result.isConfirmed) return;

                $this.data('in-flight', true).prop('disabled', true);
                var url = "{{ route('service_company.jobs.assignRejected') }}";

                $.ajax({
                    type: 'GET',
                    url: url,
                    data: { 'id': id, 'jobid': jobid },
                    success: function(response) {
                        if (response.status == 'success') {
                            window.agencyTableCache = {};
                            if (window.jobDrawerCache) {
                                delete window.jobDrawerCache['spartner_' + jobid];
                            }
                            recommendedAssignList('load');
                            Swal.fire('Ignored', 'Agency request ignored.', 'success');
                        }
                    },
                    complete: function() {
                        $this.data('in-flight', false).prop('disabled', false);
                    }
                });
            });
        });
    })();
</script>
