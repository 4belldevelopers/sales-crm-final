@php
    /**
     * Intent: GOD-LEVEL UI/UX Redesign for "Connect With Agencies" (Agency Distribution Center).
     * Architecture: Isolated Drawer experience for Service Company Jobs.
     * Features:
     * - Fixed right-side drawer (92–94vw on desktop, 15.6" screen optimized).
     * - Sticky compact header with Job Title, Metadata & Close controls.
     * - Two-Column independent architecture:
     *     Column 1: Publish to Agency Marketplace (Discovery / Broadcast) with Live vs Unpublished states.
     *     Column 2: Send Requirement Directly to Agencies (Proactive Engagement) with Recommended, My Agencies, Assigned tabs.
     * - Strict 5-Column Agency Table:
     *     1. Agency & Specialization (strong title, verified badge, match %, skill tags)
     *     2. Matching Profiles (count + label)
     *     3. Rates (Rate % / CTC, Full-Time/Contract, Onetime/Recurring)
     *     4. Location & Team (Location pin + City, Country, Recruiter count)
     *     5. Send Requirement (Individual CTA)
     * - Zero row checkboxes, zero N+1 database queries, safe HTTP 304 representation caching in RAM.
     * TENANT ISOLATION: Scoped strictly to auth()->user()->company_id.
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
@endphp

{{-- =========================================================================
     SCOPED STYLES: AGENCY DISTRIBUTION CENTER DRAWER
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

    /* ── Sticky Header Card ──────────────────────────────────────────────── */
    .agency-drawer-header {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 20px;
        margin: 14px 18px 12px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 62px;
        flex-shrink: 0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }
    .adh-job-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: #ffedd5;
        border: 1px solid #fed7aa;
        color: #ea580c;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 16px;
        flex-shrink: 0;
    }
    .adh-title {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.25;
    }
    .adh-meta-item {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
    }
    .adh-skill-badge {
        background: #f1f5f9;
        color: #475569;
        font-size: 11px;
        font-weight: 500;
        padding: 2px 8px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
    }

    /* ── Drawer Scrollable Body ───────────────────────────────────────────── */
    .agency-drawer-content {
        flex: 1;
        overflow-y: auto;
        padding: 0 18px 18px 18px;
        background: #f8fafc;
    }

    /* ── Two Column Architecture ─────────────────────────────────────────── */
    .agency-distribution-grid {
        display: grid;
        grid-template-columns: 35% 65%;
        gap: 16px;
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
        border-radius: 12px;
        padding: 20px 22px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }
    .agency-section-banner-blue {
        background: #f0f7ff;
        border: 1px solid #dbeafe;
        border-radius: 10px;
        padding: 12px 14px;
        margin-bottom: 16px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    .agency-section-banner-green {
        background: #f0fdf9;
        border: 1px solid #ccfbf1;
        border-radius: 10px;
        padding: 12px 16px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .agency-col-badge {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        color: #ffffff;
        font-weight: 700;
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .agency-col-badge.blue { background: #0066ff; }
    .agency-col-badge.green { background: #10b981; }

    .agency-section-heading {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.25;
        margin: 0;
    }
    .agency-section-subtitle {
        font-size: 11.5px;
        color: #64748b;
        margin: 3px 0 0 0;
        line-height: 1.4;
    }

    /* ── Column 1 Panels ──────────────────────────────────────────────────── */
    .info-panel-blue {
        background: #f0f7ff;
        border: 1px solid #dbeafe;
        border-radius: 10px;
        padding: 14px 16px;
        margin-bottom: 16px;
        position: relative;
        overflow: hidden;
    }
    .live-panel-green {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 10px;
        padding: 14px 16px;
        margin-bottom: 16px;
        position: relative;
        overflow: hidden;
    }
    .panel-icon-circle {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .panel-icon-circle.blue {
        background: #ffffff;
        border: 1px solid #dbeafe;
        color: #0066ff;
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
        gap: 10px;
        margin-top: 6px;
    }
    .input-icon-box {
        width: 42px;
        height: 42px;
        border-radius: 8px;
        background: #f0f7ff;
        border: 1px solid #dbeafe;
        color: #0066ff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .agency-select {
        flex: 1;
        height: 42px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
        padding: 0 14px;
        background-color: #ffffff;
        outline: none;
        cursor: pointer;
        transition: border-color 0.15s ease;
    }
    .agency-select:focus {
        border-color: #0066ff;
    }
    .agency-helper-text {
        font-size: 11px;
        color: #64748b;
        margin-top: 4px;
    }

    /* Benefits card */
    .benefits-panel {
        background: #f0fdf9;
        border: 1px solid #ccfbf1;
        border-radius: 10px;
        padding: 14px 16px;
        margin-top: 16px;
        margin-bottom: 16px;
    }
    .benefit-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 12px;
        color: #334155;
        font-weight: 500;
        margin-bottom: 8px;
    }
    .benefit-item:last-child {
        margin-bottom: 0;
    }

    /* Action buttons: display flex with NO !important so .d-none wins */
    .btn-publish-primary {
        background: #0066ff;
        color: #ffffff;
        border: none;
        border-radius: 8px;
        padding: 12px 18px;
        width: 100%;
        font-size: 13.5px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 16px;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(0, 102, 255, 0.25);
        transition: background-color 0.15s ease, transform 0.1s ease;
    }
    .btn-publish-primary:hover {
        background: #0052cc;
    }
    .btn-unpublish-destructive {
        background: #ffffff;
        color: #dc2626;
        border: 1.5px solid #fca5a5;
        border-radius: 8px;
        padding: 12px 18px;
        width: 100%;
        font-size: 13.5px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 16px;
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

    /* ── Column 2 Tabs & Table ────────────────────────────────────────────── */
    .agency-tabs-nav {
        display: flex;
        gap: 22px;
        border-bottom: 1.5px solid #e2e8f0;
        margin-top: 14px;
        padding-bottom: 0;
    }
    .agency-tab-link {
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        padding: 8px 4px 11px;
        text-decoration: none;
        border-bottom: 2.5px solid transparent;
        margin-bottom: -1.5px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .agency-tab-link:hover {
        color: #0066ff;
    }
    .agency-tab-link.active {
        color: #0066ff;
        border-bottom-color: #0066ff;
    }

    /* Agency Search and Filter row */
    .agency-search-filter-row {
        display: flex;
        gap: 10px;
        margin-top: 14px;
        margin-bottom: 12px;
    }
    .agency-search-input-wrap {
        flex: 1;
        position: relative;
    }
    .agency-search-input-wrap svg {
        position: absolute;
        left: 12px;
        top: 13px;
        color: #94a3b8;
        z-index: 5;
        pointer-events: none;
    }
    .agency-search-input {
        width: 100%;
        min-height: 40px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding-left: 36px;
        padding-right: 12px;
        font-size: 12px;
        background: #ffffff;
        color: #1e293b;
        outline: none;
        transition: border-color 0.15s ease;
    }
    .agency-search-input:focus {
        border-color: #2563eb;
    }

    /* ── Tagify Skill Tags in Search Bar ── */
    .agency-search-input-wrap .tagify {
        min-height: 38px !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        background: #ffffff !important;
        padding: 2px 10px 2px 36px !important;
        display: flex !important;
        align-items: center !important;
        flex-wrap: wrap !important;
        box-shadow: none !important;
        cursor: text !important;
        font-family: 'Poppins', sans-serif !important;
        transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
    }
    .agency-search-input-wrap .tagify:hover {
        border-color: #cbd5e1 !important;
    }
    .agency-search-input-wrap .tagify.tagify--focus {
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.15) !important;
    }
    .agency-search-input-wrap .tagify ~ #agency_unified_search {
        display: none !important;
    }
    .agency-search-input-wrap .tagify__tag {
        display: inline-flex !important;
        align-items: center !important;
        background: #eef2ff !important;
        border: 1px solid #e0e7ff !important;
        border-radius: 9999px !important;
        padding: 3px 7px 3px 12px !important;
        margin: 2px 4px 2px 0 !important;
        line-height: 1.3 !important;
        vertical-align: middle !important;
        box-shadow: 0 1px 2px rgba(67, 56, 202, 0.04) !important;
        transition: background 0.15s ease, border-color 0.15s ease !important;
    }
    .agency-search-input-wrap .tagify__tag:hover {
        background: #e0e7ff !important;
        border-color: #c7d2fe !important;
    }
    .agency-search-input-wrap .tagify__tag > div {
        padding: 0 !important;
        margin: 0 !important;
        color: #4338ca !important;
        font-size: 12px !important;
        font-weight: 500 !important;
        font-family: 'Poppins', sans-serif !important;
        order: 1 !important;
        display: inline-flex !important;
        align-items: center !important;
    }
    .agency-search-input-wrap .tagify__tag > div::before {
        display: none !important;
    }
    .agency-search-input-wrap .tagify__tag-text {
        color: #4338ca !important;
        font-size: 12px !important;
        font-weight: 500 !important;
        font-family: 'Poppins', sans-serif !important;
    }
    .agency-search-input-wrap .tagify .tagify__tag .tagify__tag__removeBtn::before,
    .agency-search-input-wrap .tagify__tag__removeBtn::before,
    .agency-search-input-wrap .tagify .tagify__tag .tagify__tag__removeBtn::after,
    .agency-search-input-wrap .tagify__tag__removeBtn::after,
    .agency-search-input-wrap .tagify .tagify__tag .tagify__tag__removeBtn:hover::before,
    .agency-search-input-wrap .tagify__tag__removeBtn:hover::before,
    .agency-search-input-wrap .tagify .tagify__tag .tagify__tag__removeBtn:hover::after,
    .agency-search-input-wrap .tagify__tag__removeBtn:hover::after {
        display: none !important;
        content: none !important;
        width: 0 !important;
        height: 0 !important;
        opacity: 0 !important;
        visibility: hidden !important;
        border: none !important;
        transform: none !important;
    }
    .agency-search-input-wrap .tagify .tagify__tag .tagify__tag__removeBtn,
    .agency-search-input-wrap .tagify:not(.form-control-sm):not(.form-control-lg) .tagify__tag .tagify__tag__removeBtn,
    .agency-search-input-wrap .tagify__tag__removeBtn {
        width: 14px !important;
        height: 14px !important;
        min-width: 14px !important;
        max-width: 14px !important;
        margin: 0px 0.1rem 0px 5px !important;
        padding: 0 !important;
        background-color: #818cf8 !important;
        background-image: url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0naHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmcnIHZpZXdCb3g9JzAgMCAxNiAxNic+PHBhdGggZmlsbD0nbm9uZScgc3Ryb2tlPScjZmZmZmZmJyBzdHJva2Utd2lkdGg9JzInIHN0cm9rZS1saW5lY2FwPSdyb3VuZCcgc3Ryb2tlLWxpbmVqb2luPSdyb3VuZCcgZD0nTTQgNGw4IDhtMC04bC04IDgnLz48L3N2Zz4=") !important;
        background-repeat: no-repeat !important;
        background-position: center center !important;
        background-size: 7px 7px !important;
        border-radius: 50% !important;
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        order: 2 !important;
        cursor: pointer !important;
        position: relative !important;
        transition: background-color 0.15s ease, transform 0.15s ease !important;
        font-size: 0 !important;
        line-height: 0 !important;
        color: transparent !important;
        overflow: hidden !important;
    }
    .agency-search-input-wrap .tagify .tagify__tag .tagify__tag__removeBtn:hover,
    .agency-search-input-wrap .tagify:not(.form-control-sm):not(.form-control-lg) .tagify__tag .tagify__tag__removeBtn:hover,
    .agency-search-input-wrap .tagify__tag__removeBtn:hover {
        background-color: #ef4444 !important;
        background-image: url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0naHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmcnIHZpZXdCb3g9JzAgMCAxNiAxNic+PHBhdGggZmlsbD0nbm9uZScgc3Ryb2tlPScjZmZmZmZmJyBzdHJva2Utd2lkdGg9JzInIHN0cm9rZS1saW5lY2FwPSdyb3VuZCcgc3Ryb2tlLWxpbmVqb2luPSdyb3VuZCcgZD0nTTQgNGw4IDhtMC04bC04IDgnLz48L3N2Zz4=") !important;
        transform: scale(1.12) !important;
    }
    .agency-search-input-wrap .tagify__input {
        font-size: 12px !important;
        color: #1e293b !important;
        font-family: 'Poppins', sans-serif !important;
        margin: 2px 0 !important;
        padding: 0 4px !important;
        line-height: 22px !important;
        min-width: 90px !important;
    }
    .agency-search-input-wrap .tagify__input::before {
        font-size: 12px !important;
        color: #94a3b8 !important;
        font-weight: 400 !important;
        font-family: 'Poppins', sans-serif !important;
        line-height: 22px !important;
    }
    .agency-search-input-wrap .tagify__input:empty::before {
        display: inline-block !important;
        opacity: 1 !important;
    }
    .btn-filter-trigger {
        height: 38px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        border-radius: 8px;
        padding: 0 14px;
        font-size: 12px;
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

    /* ── Agency Filter Slide-Over Drawer ── */
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
        width: 50vw !important;
        max-width: 50vw !important;
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
    @media (max-width: 576px) {
        .agency-filter-drawer {
            width: 96vw !important;
            max-width: 98vw !important;
        }
    }

    .agency-filter-drawer-header {
        padding: 16px 20px;
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
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        line-height: 1.25;
    }
    .afd-subtitle {
        font-size: 11px;
        color: #64748b;
        margin: 2px 0 0 0;
        line-height: 1.25;
    }
    .afd-close-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .afd-close-btn:hover {
        background: #fee2e2;
        color: #ef4444;
        border-color: #fca5a5;
    }

    .agency-filter-drawer-body {
        padding: 18px 20px;
        overflow-y: auto;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 18px;
        background: #ffffff;
    }
    .afd-trust-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 9px 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        flex-wrap: wrap;
    }
    .afd-trust-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 500;
        color: #475569;
    }
    .afd-form-group {
        display: flex;
        flex-direction: column;
    }
    .afd-form-label {
        font-size: 12px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 8px;
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
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .agency-filter-drawer-footer {
        padding: 14px 20px;
        border-top: 1px solid #e2e8f0;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-shrink: 0;
    }
    .afd-reset-btn {
        height: 36px;
        padding: 0 14px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 8px;
        color: #64748b;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .afd-reset-btn:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .afd-cancel-btn {
        height: 36px;
        padding: 0 14px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 8px;
    }
    .afd-apply-btn {
        height: 36px;
        padding: 0 18px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 8px;
        background: #2563eb;
        border-color: #2563eb;
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: background 0.15s ease;
    }
    .afd-apply-btn:hover {
        background: #1d4ed8;
        border-color: #1d4ed8;
        color: #ffffff;
    }

    /* Drawer Tagify specific overrides */
    .agency-filter-drawer .tagify {
        min-height: 40px !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        background: #ffffff !important;
        padding: 2px 8px !important;
        display: flex !important;
        align-items: center !important;
        flex-wrap: wrap !important;
        box-shadow: none !important;
        cursor: text !important;
        font-family: 'Poppins', sans-serif !important;
        transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
    }
    .agency-filter-drawer .tagify:hover {
        border-color: #cbd5e1 !important;
    }
    .agency-filter-drawer .tagify.tagify--focus {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15) !important;
    }
    .agency-filter-drawer .tagify__tag {
        display: inline-flex !important;
        align-items: center !important;
        background: #eef2ff !important;
        border: 1px solid #e0e7ff !important;
        border-radius: 9999px !important;
        padding: 3px 6px 3px 12px !important;
        margin: 2px 4px 2px 0 !important;
        line-height: 1.3 !important;
        vertical-align: middle !important;
    }
    .agency-filter-drawer .tagify__tag > div {
        padding: 0 !important;
        margin: 0 !important;
        color: #4338ca !important;
        font-size: 12px !important;
        font-weight: 500 !important;
        font-family: 'Poppins', sans-serif !important;
        order: 1 !important;
    }
    .agency-filter-drawer .tagify__tag > div::before {
        display: none !important;
    }
    .agency-filter-drawer .tagify__tag-text {
        color: #4338ca !important;
        font-size: 12px !important;
        font-weight: 500 !important;
        font-family: 'Poppins', sans-serif !important;
    }
    .agency-filter-drawer .tagify .tagify__tag .tagify__tag__removeBtn::before,
    .agency-filter-drawer .tagify__tag__removeBtn::before,
    .agency-filter-drawer .tagify .tagify__tag .tagify__tag__removeBtn::after,
    .agency-filter-drawer .tagify__tag__removeBtn::after,
    .agency-filter-drawer .tagify .tagify__tag .tagify__tag__removeBtn:hover::before,
    .agency-filter-drawer .tagify__tag__removeBtn:hover::before,
    .agency-filter-drawer .tagify .tagify__tag .tagify__tag__removeBtn:hover::after,
    .agency-filter-drawer .tagify__tag__removeBtn:hover::after {
        display: none !important;
        content: none !important;
        width: 0 !important;
        height: 0 !important;
        opacity: 0 !important;
        visibility: hidden !important;
        border: none !important;
        transform: none !important;
    }
    .agency-filter-drawer .tagify .tagify__tag .tagify__tag__removeBtn,
    .agency-filter-drawer .tagify__tag__removeBtn {
        width: 14px !important;
        height: 14px !important;
        min-width: 14px !important;
        max-width: 14px !important;
        margin: 0px 0.1rem 0px 5px !important;
        padding: 0 !important;
        background-color: #818cf8 !important;
        background-image: url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0naHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmcnIHZpZXdCb3g9JzAgMCAxNiAxNic+PHBhdGggZmlsbD0nbm9uZScgc3Ryb2tlPScjZmZmZmZmJyBzdHJva2Utd2lkdGg9JzInIHN0cm9rZS1saW5lY2FwPSdyb3VuZCcgc3Ryb2tlLWxpbmVqb2luPSdyb3VuZCcgZD0nTTQgNGw4IDhtMC04bC04IDgnLz48L3N2Zz4=") !important;
        background-repeat: no-repeat !important;
        background-position: center center !important;
        background-size: 7px 7px !important;
        border-radius: 50% !important;
        border: none !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        order: 2 !important;
        cursor: pointer !important;
        font-size: 0 !important;
        line-height: 0 !important;
        color: transparent !important;
        overflow: hidden !important;
    }
    .agency-filter-drawer .tagify .tagify__tag .tagify__tag__removeBtn:hover,
    .agency-filter-drawer .tagify__tag__removeBtn:hover {
        background-color: #ef4444 !important;
    }
    .agency-filter-drawer .tagify__input {
        font-size: 12px !important;
        color: #1e293b !important;
        font-family: 'Poppins', sans-serif !important;
    }

    /* Table headers */
    .agency-data-table {
        width: 100%;
        border-collapse: collapse;
    }
    .agency-data-table th {
        font-size: 10.5px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        padding: 9px 12px;
        border-bottom: 1.5px solid #e2e8f0;
        background: #f8fafc;
    }
    .agency-data-table td {
        vertical-align: middle;
    }

    /* Multiple Agencies Bottom Banner */
    .bottom-multi-agency-banner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-top: 14px;
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
    .aa-header-row {
        display: flex !important;
        align-items: center !important;
        background: #f8fafc !important;
        border-bottom: 1.5px solid #e2e8f0 !important;
        padding: 9px 14px !important;
        margin-bottom: 6px !important;
        border-radius: 8px !important;
        font-family: 'Poppins', sans-serif !important;
    }
    .aa-hcol-agency     { flex: 0 0 22% !important; font-size: 10.5px !important; font-weight: 600 !important; color: #64748b !important; text-transform: uppercase !important; }
    .aa-hcol-contact    { flex: 0 0 15% !important; font-size: 10.5px !important; font-weight: 600 !important; color: #64748b !important; text-transform: uppercase !important; }
    .aa-hcol-perf       { flex: 0 0 29% !important; font-size: 10.5px !important; font-weight: 600 !important; color: #64748b !important; text-transform: uppercase !important; text-align: center !important; }
    .aa-hcol-shared     { flex: 0 0 11% !important; font-size: 10.5px !important; font-weight: 600 !important; color: #64748b !important; text-transform: uppercase !important; }
    .aa-hcol-status     { flex: 0 0 9% !important; font-size: 10.5px !important; font-weight: 600 !important; color: #64748b !important; text-transform: uppercase !important; text-align: center !important; }
    .aa-hcol-actions    { flex: 1 1 auto !important; font-size: 10.5px !important; font-weight: 600 !important; color: #64748b !important; text-transform: uppercase !important; text-align: right !important; }

    .aa-row {
        display: flex !important;
        align-items: center !important;
        background: #ffffff !important;
        border: 1px solid #e8ecf1 !important;
        border-radius: 10px !important;
        padding: 10px 14px !important;
        margin-bottom: 6px !important;
        transition: box-shadow 0.15s ease, border-color 0.15s ease !important;
        gap: 0 !important;
        min-height: 56px !important;
        font-family: 'Poppins', sans-serif !important;
        width: 100% !important;
    }
    .aa-row:hover {
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.05) !important;
        border-color: #cbd5e1 !important;
    }

    .aa-col-agency {
        flex: 0 0 22% !important;
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        min-width: 0 !important;
        padding-right: 8px !important;
    }
    .aa-col-contact {
        flex: 0 0 15% !important;
        min-width: 0 !important;
        padding-right: 8px !important;
    }
    .aa-col-perf {
        flex: 0 0 29% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 0 !important;
        flex-wrap: nowrap !important;
    }
    .aa-col-shared {
        flex: 0 0 11% !important;
        min-width: 0 !important;
        padding-right: 6px !important;
    }
    .aa-col-status {
        flex: 0 0 9% !important;
        text-align: center !important;
    }
    .aa-col-actions {
        flex: 1 1 auto !important;
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        gap: 6px !important;
        position: relative !important;
        min-width: 120px !important;
    }

    /* Agency logo */
    .aa-logo {
        width: 38px !important;
        height: 38px !important;
        min-width: 38px !important;
        min-height: 38px !important;
        max-width: 38px !important;
        max-height: 38px !important;
        border-radius: 50% !important;
        flex-shrink: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-weight: 700 !important;
        font-size: 14px !important;
        color: #ffffff !important;
        overflow: hidden !important;
        border: 1.5px solid #f1f5f9 !important;
    }
    .aa-logo img {
        width: 100% !important;
        height: 100% !important;
        max-width: 38px !important;
        max-height: 38px !important;
        object-fit: cover !important;
        border-radius: 50% !important;
        display: block !important;
    }
    .aa-name {
        font-weight: 600 !important;
        font-size: 12px !important;
        color: #0f172a !important;
        line-height: 1.3 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }
    .aa-cat {
        font-size: 10.5px !important;
        color: #64748b !important;
        margin-top: 1px !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }

    /* Contact details */
    .aa-cnt-name {
        font-size: 11.5px !important;
        font-weight: 600 !important;
        color: #0f172a !important;
        line-height: 1.25 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }
    .aa-cnt-btns {
        display: flex !important;
        align-items: center !important;
        gap: 5px !important;
        margin-top: 3px !important;
    }
    .aa-cnt-btn {
        width: 24px !important;
        height: 24px !important;
        border-radius: 6px !important;
        border: 1px solid #e2e8f0 !important;
        background: #f8fafc !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 11px !important;
        color: #64748b !important;
        cursor: pointer !important;
        transition: background 0.12s, color 0.12s, border-color 0.12s !important;
    }
    .aa-cnt-btn:hover {
        background: #e2e8f0 !important;
        color: #0f172a !important;
        border-color: #cbd5e1 !important;
    }
    .aa-cnt-dash {
        color: #94a3b8 !important;
        font-size: 13px !important;
    }

    /* Performance KPIs */
    .aa-kpi {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        gap: 1px !important;
        flex: 1 1 0 !important;
        min-width: 0 !important;
        padding: 0 2px !important;
        border-right: 1px solid #f1f5f9 !important;
    }
    .aa-kpi:last-child {
        border-right: none !important;
    }
    .aa-kpi-num {
        font-size: 12px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        line-height: 1.1 !important;
    }
    .aa-kpi-label {
        font-size: 8.5px !important;
        color: #64748b !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.2px !important;
        white-space: nowrap !important;
    }

    /* Shared date */
    .aa-shared-rel {
        font-size: 11.5px !important;
        font-weight: 600 !important;
        color: #0f172a !important;
        line-height: 1.2 !important;
    }
    .aa-shared-abs {
        font-size: 10px !important;
        color: #64748b !important;
        margin-top: 1px !important;
    }

    /* Status badges */
    .aa-badge {
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
        padding: 3px 8px !important;
        border-radius: 6px !important;
        font-size: 11px !important;
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

    /* Actions */
    .aa-terms-btn {
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
        padding: 5px 10px !important;
        border-radius: 7px !important;
        border: 1px solid #e2e8f0 !important;
        background: #ffffff !important;
        font-size: 11.5px !important;
        font-weight: 600 !important;
        color: #475569 !important;
        cursor: pointer !important;
        white-space: nowrap !important;
        transition: border-color 0.12s, color 0.12s, background 0.12s !important;
    }
    .aa-terms-btn:hover {
        border-color: #0066ff !important;
        color: #0066ff !important;
        background: #eff6ff !important;
    }
    .aa-dots-btn {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 28px !important;
        height: 28px !important;
        border-radius: 7px !important;
        border: 1px solid #e2e8f0 !important;
        background: #ffffff !important;
        cursor: pointer !important;
        color: #64748b !important;
        font-size: 13px !important;
        transition: background 0.12s, border-color 0.12s !important;
    }
    .aa-dots-btn:hover {
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
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
        transition: background 0.1s !important;
    }
    .aa-ddi:hover {
        background: #fef2f2 !important;
    }

    /* Empty state */
    .aa-empty {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 48px 20px !important;
        text-align: center !important;
    }
    .aa-empty-icon {
        width: 56px !important;
        height: 56px !important;
        border-radius: 50% !important;
        background: #f1f5f9 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin-bottom: 12px !important;
        font-size: 22px !important;
        color: #94a3b8 !important;
    }
    .aa-empty-title {
        font-size: 13.5px !important;
        font-weight: 600 !important;
        color: #1e293b !important;
        margin-bottom: 4px !important;
    }
    .aa-empty-sub {
        font-size: 12px !important;
        color: #94a3b8 !important;
        max-width: 280px !important;
        line-height: 1.5 !important;
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
     STICKY DRAWER HEADER: JERO AGENCY DISTRIBUTION CENTER
     ========================================================================= --}}
<div class="agency-drawer-header">
    <div class="d-flex align-items-center gap-3">
        {{-- Job Icon Badge --}}
        <div class="adh-job-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="16 18 22 12 16 6"></polyline>
                <polyline points="8 6 2 12 8 18"></polyline>
            </svg>
        </div>

        {{-- Job Title & Metadata --}}
        <div class="d-flex flex-column">
            <div class="d-flex align-items-center gap-2">
                <span class="adh-title text-truncate" style="max-width: 320px;" title="{{ @$job->title }}">
                    {{ ucwords(@$job->title ?: 'Requirement') }}
                </span>
                <span class="badge" style="background:#dcfce7; color:#15803d; border: 1px solid #bbf7d0; border-radius: 12px; font-size: 11px; font-weight: 600; padding: 2px 9px;">
                    {{ @$job->employment_type == 1 ? 'Contract' : 'Full-time' }}
                </span>
            </div>

            {{-- Compact Metadata Row --}}
            <div class="d-flex align-items-center gap-3 flex-wrap mt-1">
                <span class="adh-meta-item">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                    {{ @$job->min_exp }}–{{ @$job->max_exp }} Years
                </span>
                <span class="text-gray-300">•</span>
                <span class="adh-meta-item">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    {{ $clientLocationText }}
                </span>
                <span class="text-gray-300">•</span>
                <span class="adh-meta-item">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                    {!! $salary_currency !!} {{ $min_salary }}–{{ $max_salary }} / {{ $max_sal_mode }}
                </span>
                <span class="text-gray-300">•</span>
                <span class="adh-meta-item">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    {{ number_format(@$job->total_positions ?: 1) }} Opening{{ (@$job->total_positions > 1) ? 's' : '' }}
                </span>

                {{-- Skill Pills --}}
                @if(!empty($skillArrList))
                    <span class="text-gray-300">•</span>
                    <div class="d-flex align-items-center gap-1">
                        @foreach(array_slice($skillArrList, 0, 3) as $sk)
                            <span class="adh-skill-badge">{{ ucfirst($sk->skill_name) }}</span>
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
        <button type="button" class="btn btn-sm d-flex align-items-center gap-2 drawer-close-trigger" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 7px 14px; font-size: 12.5px; font-weight: 600; color: #1e293b; cursor: pointer; transition: background-color 0.15s ease;">
            <span>Close</span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
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
                    <p class="agency-section-subtitle">Make this job visible to verified recruitment agencies on Jero Agency Marketplace.</p>
                </div>
            </div>

            {{-- ── UNPUBLISHED STATE CONTAINER ── --}}
            <div id="marketplace_unpublished_box" class="{{ $isPublished ? 'd-none' : '' }}" style="{{ $isPublished ? 'display: none !important;' : 'display: block;' }}">
                <div class="info-panel-blue">
                    <div class="d-flex align-items-center gap-3">
                        <div class="panel-icon-circle blue">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0066ff" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                        </div>
                        <div style="flex: 1; padding-right: 30px;">
                            <div style="font-size: 13.5px; font-weight: 700; color: #0066ff;">Open to Agency Applications</div>
                            <div style="font-size: 11.5px; color: #475569; margin-top: 2px; line-height: 1.4;">Any verified agency can discover this job and submit suitable candidates.</div>
                        </div>
                    </div>
                    <div style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); opacity: 0.18; pointer-events: none;">
                        <svg width="55" height="55" viewBox="0 0 24 24" fill="#93c5fd"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                </div>
            </div>

            {{-- ── LIVE STATE CONTAINER ── --}}
            <div id="marketplace_live_box" class="{{ $isPublished ? '' : 'd-none' }}" style="{{ $isPublished ? 'display: block;' : 'display: none !important;' }}">
                <div class="live-panel-green">
                    <div class="d-flex align-items-center gap-3">
                        <div class="panel-icon-circle green">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="#16a34a"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                        </div>
                        <div style="flex: 1;">
                            <div class="d-flex align-items-center gap-2">
                                <span style="font-size: 13px; font-weight: 800; color: #15803d; letter-spacing: 0.3px;">LIVE ON AGENCY MARKETPLACE</span>
                                <span class="badge" style="background: #dcfce7; color: #15803d; border: 1px solid #86efac; border-radius: 12px; font-size: 10.5px; font-weight: 700; padding: 2px 8px; display: inline-flex; align-items: center; gap: 4px;">
                                    <span style="display:inline-block; width:6px; height:6px; border-radius:50%; background:#16a34a;"></span>Live
                                </span>
                            </div>
                            <div style="font-size: 11.5px; color: #166534; margin-top: 3px; line-height: 1.4;">This job is now visible to verified agencies on Jero. They can discover and submit suitable candidates.</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Agency Acceptance Limit (Maximum agencies that can accept/work on this job) --}}
            <div class="mt-4">
                <div class="d-flex align-items-center gap-1">
                    <label class="fw-bold fs-7 text-gray-900 mb-0">Agency Acceptance Limit</label>
                    <i class="fa-regular fa-circle-question ms-1" style="font-size:12px;color:#94a3b8;cursor:help;" title="Maximum agencies that can accept and work on this job requirement."></i>
                </div>
                <div class="agency-input-control">
                    <div class="input-icon-box">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <select class="agency-select" id="marketplace_vendor_limit">
                        <option value="5">5 agencies</option>
                        <option value="10">10 agencies</option>
                        <option value="20" selected>20 agencies</option>
                        <option value="50">50 agencies</option>
                    </select>
                </div>
                <div class="agency-helper-text">Maximum agencies that can accept this job.</div>
            </div>

            {{-- Auto-expire --}}
            <div class="mt-4">
                <div class="d-flex align-items-center gap-1">
                    <label class="fw-bold fs-7 text-gray-900 mb-0">Auto-expire</label>
                    <i class="fa-regular fa-circle-question ms-1" style="font-size:12px;color:#94a3b8;cursor:help;" title="The marketplace listing will automatically close on this date."></i>
                </div>
                <div class="agency-input-control">
                    <div class="input-icon-box">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                    <select class="agency-select" id="marketplace_auto_expire">
                        <option value="7" selected>7 Days ({{ now()->addDays(7)->format('d M Y') }})</option>
                        <option value="14">14 Days ({{ now()->addDays(14)->format('d M Y') }})</option>
                        <option value="30">30 Days ({{ now()->addDays(30)->format('d M Y') }})</option>
                    </select>
                </div>
                <div class="agency-helper-text">The marketplace listing will automatically close on this date.</div>
            </div>

            {{-- Benefit Checklist Panel --}}
            <div class="benefits-panel">
                <div class="benefit-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="#10b981" class="flex-shrink-0"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                    <span>Any verified agency can apply for this job.</span>
                </div>
                <div class="benefit-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="#10b981" class="flex-shrink-0"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                    <span>You will review and accept candidate submissions.</span>
                </div>
                <div class="benefit-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="#10b981" class="flex-shrink-0"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                    <span>You can unpublish the requirement anytime.</span>
                </div>
            </div>

            {{-- Action CTAs: Mutually exclusive display guaranteed --}}
            <button type="button" id="sp_list_one" class="btn-publish-primary post_to_public {{ $isPublished ? 'd-none' : '' }}" style="{{ $isPublished ? 'display: none !important;' : 'display: flex;' }}" data-c="0" data-row-id="0" data-matchcount="0" data-row-jobid="{{ @$job->id }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                <span>Publish to Agency Marketplace</span>
            </button>

            <button type="button" id="sp_list_two" class="btn-unpublish-destructive post_to_public {{ $isPublished ? '' : 'd-none' }}" style="{{ $isPublished ? 'display: flex;' : 'display: none !important;' }}" data-c="1" data-row-id="0" data-matchcount="0" data-row-jobid="{{ @$job->id }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                <span>Unpublish from Marketplace</span>
            </button>

            {{-- Terms Secondary Link --}}
            <div class="text-center mt-3">
                <a href="javascript:void(0);" data-row-id="{{ @$job->id }}" data-row-recommendation_relation_id="" class="client-terms-listing d-inline-flex align-items-center gap-2" style="font-size: 12px; color: #0066ff; font-weight: 500; text-decoration: none;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#0066ff" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    <span>View Agency Marketplace Terms</span>
                </a>
            </div>
        </div>

        {{-- =================================================================
             COLUMN 2: SEND REQUIREMENT DIRECTLY TO AGENCIES (~64% Width)
             ================================================================= --}}
        <div class="agency-card">
            {{-- Header inside subtle light-mint banner --}}
            <div class="agency-section-banner-green">
                <div class="d-flex align-items-start gap-3">
                    <span class="agency-col-badge green">2</span>
                    <div>
                        <h2 class="agency-section-heading">Send Requirement Directly to Agencies</h2>
                        <p class="agency-section-subtitle">Select agencies you want to notify and ask them to work on this requirement.</p>
                    </div>
                </div>

                {{-- Direct Outreach Badge --}}
                <div class="direct-outreach-badge" style="background: #e0f2fe; border: 1px solid #bae6fd; border-radius: 10px; padding: 7px 14px; display: flex !important; align-items: center; gap: 10px; flex-shrink: 0;">
                    <div style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
                            <path d="M2.5 13.5L21.5 3.5L14.5 21.5L10.5 14.5L2.5 13.5Z" fill="#0284c7"/>
                            <path d="M10.5 14.5L21.5 3.5" stroke="#ffffff" stroke-width="1.2" stroke-linecap="round"/>
                            <path d="M16 4C19 3.5 22 5.5 22.5 7" stroke="#0284c7" stroke-width="1.6" stroke-linecap="round"/>
                            <line x1="2" y1="16" x2="3.5" y2="17.5" stroke="#0284c7" stroke-width="1.5" stroke-linecap="round"/>
                            <line x1="1" y1="13.5" x2="2.5" y2="13.5" stroke="#0284c7" stroke-width="1.5" stroke-linecap="round"/>
                            <line x1="4.5" y1="18.5" x2="4.5" y2="20" stroke="#0284c7" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div style="line-height: 1.2;">
                        <div style="font-size: 11.5px; font-weight: 700; color: #0369a1;">Direct Outreach</div>
                        <div style="font-size: 10px; color: #64748b; margin-top: 1px;">Get faster responses</div>
                    </div>
                </div>
            </div>

            {{-- Tabs --}}
            <div class="agency-tabs-nav" role="tablist">
                <a class="agency-tab-link active" data-tab="recommended" href="#tab_recommended">
                    <span>Recommended</span>
                    <span id="recommendedTotal"></span>
                </a>
                <a class="agency-tab-link" data-tab="registered" href="#tab_registered">
                    <span>My Agencies</span>
                    <span id="registeredTotal">({{ @$registeredTotalCount ?: 0 }})</span>
                </a>
                <a class="agency-tab-link" data-tab="assigned" href="#tab_assigned">
                    <span>Assigned</span>
                    <span id="assignedTotal">({{ @$assignedTotalCount ?: 0 }})</span>
                </a>
            </div>

            {{-- Search & Filter Bar --}}
            <div class="agency-search-filter-row">
                <div class="agency-search-input-wrap">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" id="agency_unified_search" class="agency-search-input" placeholder="Search by skills..." value="{{ @$default_skill }}" />
                </div>
                <button type="button" class="btn-filter-trigger">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                    <span>Filter</span>
                </button>
            </div>

            {{-- Tab Contents --}}
            <div class="tab-content mt-2">

                {{-- ── TAB 1: RECOMMENDED AGENCIES ── --}}
                <div class="tab-pane active show" id="tab_recommended" role="tabpanel" style="display: block;">
                    <div class="table-responsive" style="max-height: calc(100vh - 280px); overflow-y: auto;">
                        <table class="agency-data-table" id="recommendedlist">
                            <thead>
                                <tr>
                                    <th style="width: 32%;">Agency & Specialization</th>
                                    <th style="width: 17%;">Matching Profiles</th>
                                    <th style="width: 15%;">Rates</th>
                                    <th style="width: 18%;">Location & Team</th>
                                    <th style="width: 18%; text-align: right;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="recommendedlistBody">
                                {{-- Loaded dynamically via tableLoad(1) --}}
                            </tbody>
                        </table>
                        <div id="recommended_loader" class="table-loader-wrap">
                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                            <span class="ms-2 fs-7 text-muted">Loading recommended agencies...</span>
                        </div>
                    </div>
                </div>

                {{-- ── TAB 2: MY AGENCIES ── --}}
                <div class="tab-pane" id="tab_registered" role="tabpanel" style="display: none;">
                    <div class="table-responsive" style="max-height: calc(100vh - 280px); overflow-y: auto;">
                        <table class="agency-data-table" id="staffingPartnersRegister">
                            <thead>
                                <tr>
                                    <th style="width: 32%;">Agency & Specialization</th>
                                    <th style="width: 17%;">Matching Profiles</th>
                                    <th style="width: 15%;">Rates</th>
                                    <th style="width: 18%;">Location & Team</th>
                                    <th style="width: 18%; text-align: right;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="allregisteragency">
                                {{-- Loaded dynamically via staffingPartnersRegister(1) --}}
                            </tbody>
                        </table>
                        <div id="paginationregisteragency" class="mt-2"></div>
                    </div>
                </div>

                {{-- ── TAB 3: ASSIGNED AGENCIES ── --}}
                <div class="tab-pane" id="tab_assigned" role="tabpanel" style="display: none;">
                    <div class="table-responsive" style="max-height: calc(100vh - 280px); overflow-y: auto;">
                        <div class="aa-header-row d-none d-lg-flex" style="background:#f8fafc; border-bottom:1.5px solid #e2e8f0; padding:8px 12px; margin-bottom:8px;">
                            <div class="aa-hcol-agency" style="flex:0 0 20%; font-size:10.5px; font-weight:600; color:#64748b; text-transform:uppercase;">Agency</div>
                            <div class="aa-hcol-contact" style="flex:0 0 15%; font-size:10.5px; font-weight:600; color:#64748b; text-transform:uppercase;">Contact</div>
                            <div class="aa-hcol-perf" style="flex:0 0 32%; font-size:10.5px; font-weight:600; color:#64748b; text-transform:uppercase; text-align:center;">Performance</div>
                            <div class="aa-hcol-shared" style="flex:0 0 12%; font-size:10.5px; font-weight:600; color:#64748b; text-transform:uppercase;">Shared</div>
                            <div class="aa-hcol-status" style="flex:0 0 10%; font-size:10.5px; font-weight:600; color:#64748b; text-transform:uppercase; text-align:center;">Status</div>
                            <div class="aa-hcol-actions" style="flex:1; font-size:10.5px; font-weight:600; color:#64748b; text-transform:uppercase; text-align:right;">Actions</div>
                        </div>
                        <div id="allAssignListagency">
                            {{-- Loaded dynamically via recommendedAssignList(1) --}}
                        </div>
                        <div id="paginationAssignListagency" class="mt-2"></div>
                    </div>
                </div>
            </div>

            {{-- Bottom Banner: Send to Multiple Agencies --}}
            <div class="bottom-multi-agency-banner" style="background: #f0f7ff; border: 1px solid #dbeafe; border-radius: 10px; padding: 12px 18px; margin-top: 16px; display: flex; align-items: center; justify-content: space-between;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 36px; height: 36px; border-radius: 8px; background: #ffffff; border: 1px solid #bfdbfe; color: #0066ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 12.5px; color: #0f172a;">Send to multiple agencies</div>
                        <div style="font-size: 11px; color: #64748b;">You can select and send this requirement to multiple agencies at once.</div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm" disabled style="background: #dbeafe; color: #2563eb; font-size: 12px; font-weight: 600; border-radius: 6px; padding: 7px 16px; border: none; cursor: default; display: flex; align-items: center; gap: 6px;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                    Send to 0 Agencies
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
     * Recommended Agencies Table Loader:
     * Two-Tier Safe Representation: Client RAM cache + X-Has-Representation + _force=1 Recovery
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
                } else if (hasRep && window.agencyTableCache[ramCacheKey]) {
                    var cached = window.agencyTableCache[ramCacheKey];
                    $('#recommendedlistBody').html(cached.html);
                    if (cached.total !== undefined) {
                        $('#recommendedTotal').html('(' + cached.total + ')');
                    }
                } else {
                    // Safe representation recovery: _force=1 guarantees fresh HTML from Redis
                    var fallbackParams = $.extend({}, params, { _force: 1 });
                    $.getJSON(`{!! route('service_company.jobs.recommendedData') !!}?page=${page}`, fallbackParams, function(fresh) {
                        if (fresh && fresh.html) {
                            setAgencyTableCache(ramCacheKey, fresh);
                            $('#recommendedlistBody').html(fresh.html);
                            if (fresh.total !== undefined) {
                                $('#recommendedTotal').html('(' + fresh.total + ')');
                            }
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
    function staffingPartnersRegister(page = 1, search = null) {
        if (page === 'load' || !page) { page = 1; }
        var jobid = $('#current_job_id').val();
        var cus_search = (search !== null && search !== undefined) ? search : (getUnifiedSearchTerms() || '');

        var params = {
            page: page,
            jobid: jobid,
            hiringtype: $('#hiringtype4').val() || '',
            country: $('#country4').val() || '',
            cus_search: cus_search
        };
        var ramCacheKey = 'registered_' + jobid + '_p' + page + '_' + getAgencyParamsKey(params);
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
                    }
                } else if (hasRep && window.agencyTableCache[ramCacheKey]) {
                    var cached = window.agencyTableCache[ramCacheKey];
                    $('#allregisteragency').html(cached.html);
                    if (cached.total !== undefined) {
                        $('#registeredTotal').html('(' + cached.total + ')');
                    }
                    if (cached.pagination) {
                        $('#paginationregisteragency').html(cached.pagination);
                    }
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
    function recommendedAssignList(page = 1, search = null) {
        if (page === 'load' || !page) { page = 1; }
        var jobid = $('#current_job_id').val();
        var cus_search = (search !== null && search !== undefined) ? search : (getUnifiedSearchTerms() || '');

        var params = {
            page: page,
            jobid: jobid,
            cus_search: cus_search
        };
        var ramCacheKey = 'assigned_' + jobid + '_p' + page + '_' + getAgencyParamsKey(params);
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

        // Initialize Tab Manager and Load Initial Data
        AgencyMarketplaceTabManager.init();

        // Unified Search Input with Tagify & Debounce (300ms)
        var searchTimeout = null;
        function triggerUnifiedSearch() {
            clearTimeout(searchTimeout);
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
            var $this = $(this);
            if ($this.data('in-flight')) return false;

            var current_jobid = $this.data('row-jobid') || $('#current_job_id').val();
            var job_cancel = $this.data('c');
            var id = 0;
            var url = "{{ route('service_company.jobs.auction_send_job_request', ':id') }}".replace(':id', id);

            Swal.fire({
                title: job_cancel == 1 ? 'Unpublish from Marketplace?' : 'Publish to Agency Marketplace?',
                text: job_cancel == 1 
                    ? 'This job will no longer be visible to agencies on Jero Agency Marketplace.'
                    : 'This job will become LIVE on Jero Agency Marketplace. Verified agencies can discover and submit candidates.',
                icon: job_cancel == 1 ? 'warning' : 'info',
                showCancelButton: true,
                confirmButtonText: job_cancel == 1 ? 'Yes, unpublish' : 'Yes, publish now',
                cancelButtonText: 'Cancel',
                confirmButtonColor: job_cancel == 1 ? '#ef4444' : '#2563eb',
                cancelButtonColor: '#64748b'
            }).then((result) => {
                if (!result.isConfirmed) return;

                $this.data('in-flight', true).prop('disabled', true);

                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        'current_jobid': current_jobid,
                        'job_cancel': job_cancel
                    },
                    success: function(response) {
                        if (response && response.status === 'closeJob' && job_cancel != 1) {
                            Swal.fire("Notice", response.message || "Please update the job status from 'Closed/Hold' to 'Hiring' to enable posting.", "warning");
                            return;
                        }

                        if (job_cancel == 1) {
                            // Swapped to UNPUBLISHED state
                            setMarketplaceUiState(false);
                            Swal.fire("Unpublished", "This job requirement is no longer live on the marketplace.", "success");
                        } else {
                            // Swapped to LIVE state
                            setMarketplaceUiState(true);
                            Swal.fire("Published Live!", "This job is now live on Jero Agency Marketplace.", "success");
                        }

                        // Invalidate RAM cache so fresh states persist
                        window.agencyTableCache = {};
                        if (window.jobDrawerCache) {
                            delete window.jobDrawerCache['spartner_' + current_jobid];
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
                    },
                    complete: function() {
                        $this.data('in-flight', false).prop('disabled', false);
                    }
                });
            });
        });

        // ── SEND REQUIREMENT (INVITATION) ACTIONS ────────────────────────────
        $('body').off('click.sendOnlyInvite', '.send_only_job_invitation').on('click.sendOnlyInvite', '.send_only_job_invitation', function(e) {
            e.preventDefault();
            var ele = $(this);
            if (ele.data('in-flight')) return false;
            ele.data('in-flight', true).prop('disabled', true).addClass('disabled');

            var id = ele.data('row-jobid') || $('#current_job_id').val();
            var fromcompany = ele.data('row-fromcompany');
            var tocompany = ele.data('row-tocompany');
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
                        ele.replaceWith('<span class="badge py-2 px-3 fw-bold fs-7" style="background:#dcfce7; color:#15803d; border-radius:6px; display:inline-flex; align-items:center; gap:4px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="#15803d"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> Request Sent</span>');
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
                        $this.replaceWith('<span class="badge py-2 px-3 fw-bold fs-7" style="background:#dcfce7; color:#15803d; border-radius:6px; display:inline-flex; align-items:center; gap:4px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="#15803d"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> Request Sent</span>');
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

        // Pagination handlers for Assigned tab
        $(document).off('click.pagiAssign', '#paginationAssignListagency .pagi-page, #paginationAssignListagency .pagi-arrow')
                   .on('click.pagiAssign', '#paginationAssignListagency .pagi-page, #paginationAssignListagency .pagi-arrow', function(e) {
            e.preventDefault();
            var page = $(this).attr('data-page') || $(this).data('page');
            if (!page || $(this).hasClass('disabled')) return;
            recommendedAssignList(page);
        });

        // Pagination handlers for Registered tab
        $(document).off('click.pagiRegister', '#paginationregisteragency .pagi-page, #paginationregisteragency .pagi-arrow')
                   .on('click.pagiRegister', '#paginationregisteragency .pagi-page, #paginationregisteragency .pagi-arrow', function(e) {
            e.preventDefault();
            var page = $(this).attr('data-page') || $(this).data('page');
            if (!page || $(this).hasClass('disabled')) return;
            staffingPartnersRegister(page);
        });
    })();
</script>
