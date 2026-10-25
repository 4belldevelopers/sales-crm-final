@php
    /**
     * Intent: Direct Client Request Details Drawer UI component.
     * Aligned strictly with Modern Card + Sub-Card Enterprise Design Standard (Rule 20).
     */
    use App\Helper\GeneralMethod;
    use Carbon\Carbon;

    $startOfMonthcomman = Carbon::now()->startOfMonth()->subMonths(5);
    $endOfMonthcomman = Carbon::now()->endOfMonth();
    $endDate = Carbon::today()->endOfDay();
    $startDate = Carbon::today()->subDays(6)->startOfDay();

    // Data extraction with safe fallbacks
    $cur_company_id = auth()->user()->company_id ?? 0;
    $detail_from = @$directClientDetail->from_company_id ?: @$directClientDetail->company_id;

    $kpiActive = $active_requisition ?? 0;
    $kpiSubmitted = 0;
    $kpiInterviews = 0;
    $kpiOffers = 0;
    $kpiJoins = 0;

    if ($detail_from) {
        try {
            $kpiJobs = GeneralMethod::DirectClientJobsCountAllStatus($detail_from, $cur_company_id);
            $kpiPerf = GeneralMethod::DirectClientCandidatesCountList($detail_from, $cur_company_id);
            if (isset($kpiJobs->job_active)) $kpiActive = $kpiJobs->job_active;
            if (isset($kpiPerf->all_candidates)) $kpiSubmitted = $kpiPerf->all_candidates;
            if (isset($kpiPerf->all_interview)) $kpiInterviews = $kpiPerf->all_interview;
            if (isset($kpiPerf->offers)) $kpiOffers = $kpiPerf->offers;
            if (isset($kpiPerf->Joined)) $kpiJoins = $kpiPerf->Joined;
        } catch (\Throwable $e) {
            // Keep default KPI active count fallback
        }
    }

    $detailLogo = '';
    if (!empty(@$directClientDetail->logo_logical_path) && strlen(@$directClientDetail->logo_logical_path) > 10) {
        $detailLogo = url('public/' . @$directClientDetail->logo_logical_path);
    } elseif (!empty(@$directClientDetail->logo) && strlen(@$directClientDetail->logo) > 10) {
        $detailLogo = @$directClientDetail->logo;
    }

    $detailName = ucwords(@$directClientDetail->name);
    if (empty($detailName) && !empty(@$directClientDetail->company_name)) {
        $detailName = ucwords(@$directClientDetail->company_name);
    }
    $detailInitials = strtoupper(implode('', array_map(fn($w) => mb_substr($w, 0, 1), array_slice(explode(' ', trim($detailName ?: 'DC')), 0, 2)))) ?: 'DC';

    $industries_name = '';
    $lookupInd = function ($source, $indId) {
        if (empty($indId) || empty($source)) return '';
        $targetId = (string) $indId;
        if (is_iterable($source)) {
            foreach ($source as $item) {
                if (is_object($item) && isset($item->ind_id) && (string) $item->ind_id === $targetId) {
                    return (string) ($item->ind_name ?? $item->name ?? '');
                } elseif (is_array($item) && isset($item['ind_id']) && (string) $item['ind_id'] === $targetId) {
                    return (string) ($item['ind_name'] ?? $item['name'] ?? '');
                }
            }
        }
        if (isset($source[$indId])) {
            $val = $source[$indId];
            if (is_object($val)) return (string) ($val->ind_name ?? $val->name ?? '');
            if (is_array($val)) return (string) ($val['ind_name'] ?? $val['name'] ?? '');
            return (string) $val;
        }
        return '';
    };

    if (!empty(@$directClientDetail->industry)) {
        $industries_name = $lookupInd($department ?? null, @$directClientDetail->industry);
    }
    if (empty($industries_name) && !empty(@$directClientDetail->industries)) {
        $industries_name = $lookupInd($industries ?? null, @$directClientDetail->industries);
    }

    $tagsArr = array_filter(array_map('trim', explode(',', (string) @$directClientDetail->tags)));

    $rc_company_type_val = '';
    if (isset($directClientDetail->company_type) && $directClientDetail->company_type !== '' && isset($companyType[$directClientDetail->company_type])) {
        $rawRcComp = $companyType[$directClientDetail->company_type];
        $rc_company_type_val = is_string($rawRcComp) ? $rawRcComp : (is_object($rawRcComp) ? (string)($rawRcComp->name ?? '') : '');
    }
    if (empty($rc_company_type_val)) {
        $rc_company_type_val = 'Direct Corporate Client';
    }

    $webClean = @$directClientDetail->website ?: '';
    $clientSinceYear = (!empty(@$directClientDetail->created_at)) ? \Carbon\Carbon::parse(@$directClientDetail->created_at)->format('Y') : ((!empty(@$directClientDetail->to_created_at)) ? \Carbon\Carbon::parse(@$directClientDetail->to_created_at)->format('Y') : date('Y'));
    $clientUpdatedAt = @$directClientDetail->updated_at ? Carbon::parse($directClientDetail->updated_at)->diffForHumans() : 'Recent';
    $isClientActive = ($kpiActive > 0);
@endphp

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
    .cd-req-scope {
        box-sizing: border-box;
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 12px !important;
        color: #334155;
        background: #f8fafc;
        padding: 0;
    }
    .cd-req-scope *, .cd-req-scope *::before, .cd-req-scope *::after {
        box-sizing: border-box;
    }

    /* TIER 1: Top Action Header Toolbar Card */
    .cd-top-bar {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 10px 16px !important;
        margin: 0 0 12px 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
        position: sticky !important;
        top: 0 !important;
        z-index: 25 !important;
    }
    .cd-top-bar-left {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        min-width: 0 !important;
        flex: 1 !important;
    }
    .cd-top-title-wrap {
        display: flex !important;
        flex-direction: column !important;
        gap: 3px !important;
        min-width: 0 !important;
    }
    .cd-top-breadcrumbs {
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
        font-size: 11px !important;
        color: #64748b !important;
        flex-wrap: wrap !important;
    }
    .cd-breadcrumb-chip {
        display: inline-flex !important;
        align-items: center !important;
        color: #64748b !important;
        font-weight: 500 !important;
    }
    .cd-client-id-pill {
        font-size: 10px !important;
        font-weight: 700 !important;
        color: #059669 !important;
        background: #ecfdf5 !important;
        border: 1px solid #a7f3d0 !important;
        padding: 1px 6px !important;
        border-radius: 4px !important;
        font-family: monospace !important;
    }
    .cd-top-badge-row {
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
        flex-wrap: wrap !important;
    }
    .cd-client-type-badge-pill {
        display: inline-flex !important;
        align-items: center !important;
        font-size: 10.5px !important;
        font-weight: 600 !important;
        padding: 2px 8px !important;
        border-radius: 6px !important;
        line-height: 1.2 !important;
    }
    .cd-client-type-badge-pill.direct {
        background: #ecfdf5 !important;
        color: #059669 !important;
        border: 1px solid #a7f3d0 !important;
    }
    .cd-param-badge-pill {
        display: inline-flex !important;
        align-items: center !important;
        font-size: 10.5px !important;
        font-weight: 500 !important;
        color: #475569 !important;
        background: #f1f5f9 !important;
        border: 1px solid #e2e8f0 !important;
        padding: 2px 7px !important;
        border-radius: 5px !important;
    }
    .cd-hdr-btn-close {
        width: 32px !important;
        height: 32px !important;
        padding: 0 !important;
        border-radius: 6px !important;
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        color: #64748b !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        font-size: 13px !important;
        transition: all 0.15s !important;
    }
    .cd-hdr-btn-close:hover {
        background: #f1f5f9 !important;
        color: #0f172a !important;
        border-color: #cbd5e1 !important;
    }

    /* HERO MASTER CARD: 3 Discrete Upper Sub-Cards + Discrete Bottom Activity Strip */
    .cd-hero-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 14px 16px 10px !important;
        margin-bottom: 12px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    }
    .cd-hero-subcards-grid {
        display: grid !important;
        grid-template-columns: 1.35fr 1.65fr 1fr !important;
        gap: 12px !important;
        align-items: center !important;
    }
    @media (max-width: 1200px) {
        .cd-hero-subcards-grid { grid-template-columns: 1fr 1fr !important; }
    }
    @media (max-width: 768px) {
        .cd-hero-subcards-grid { grid-template-columns: 1fr !important; }
    }

    .cd-hero-subcard-identity {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        padding: 10px 12px !important;
        min-width: 0 !important;
    }
    .cd-avatar-box {
        position: relative !important;
        width: 48px !important;
        height: 48px !important;
        border-radius: 8px !important;
        background: #064e3b !important;
        color: #ffffff !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 14px !important;
        font-weight: 700 !important;
        flex-shrink: 0 !important;
        overflow: visible !important;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06) !important;
    }
    .cd-avatar-box img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        border-radius: 8px !important;
    }
    .cd-avatar-initials {
        font-size: 14px !important;
        font-weight: 700 !important;
        color: #ffffff !important;
    }
    .cd-verified-seal {
        position: absolute !important;
        top: -4px !important;
        right: -4px !important;
        width: 16px !important;
        height: 16px !important;
        border-radius: 50% !important;
        background: #10b981 !important;
        border: 2px solid #ffffff !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        color: #ffffff !important;
        font-size: 8px !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.15) !important;
    }
    .cd-identity-meta { min-width: 0 !important; flex: 1 !important; }
    .cd-identity-title-row {
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
        margin-bottom: 2px !important;
        flex-wrap: wrap !important;
    }
    .cd-identity-name {
        font-size: 14px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        margin: 0 !important;
        line-height: 1.2 !important;
        letter-spacing: -0.2px !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }
    .cd-type-badge-tag {
        font-size: 9.5px !important;
        font-weight: 600 !important;
        padding: 1.5px 6px !important;
        border-radius: 4px !important;
        line-height: 1.2 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.3px !important;
    }
    .cd-type-badge-tag.direct {
        background: #ecfdf5 !important;
        color: #059669 !important;
        border: 1px solid #a7f3d0 !important;
    }
    .cd-identity-subtitle { font-size: 11px !important; color: #64748b !important; line-height: 1.2 !important; }

    .cd-hero-subcard-params {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 6px !important;
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        padding: 10px 12px !important;
    }
    .cd-hero-param-chip {
        display: inline-flex !important;
        align-items: center !important;
        gap: 5px !important;
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 6px !important;
        padding: 3px 8px !important;
        font-size: 11px !important;
        color: #334155 !important;
        font-weight: 500 !important;
    }
    .cd-hero-param-chip.link {
        color: #059669 !important;
        text-decoration: none !important;
        cursor: pointer !important;
    }
    .cd-hero-param-chip.link:hover {
        border-color: #059669 !important;
        text-decoration: underline !important;
    }

    .cd-hero-subcard-activity {
        display: flex !important;
        flex-direction: column !important;
        gap: 6px !important;
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        padding: 10px 12px !important;
    }
    .cd-status-pulse-badge {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        border-radius: 6px !important;
        padding: 4px 8px !important;
    }
    .cd-status-pulse-badge.active { background: #f0fdf4 !important; border: 1px solid #bbf7d0 !important; }
    .cd-status-pulse-badge.inactive { background: #f8fafc !important; border: 1px solid #e2e8f0 !important; }
    .cd-pulse-indicator {
        width: 7px !important;
        height: 7px !important;
        border-radius: 50% !important;
        position: relative !important;
        flex-shrink: 0 !important;
    }
    .cd-pulse-indicator.pulse-green { background: #16a34a !important; }
    .cd-pulse-indicator.pulse-green::after {
        content: "" !important;
        position: absolute !important;
        inset: -3px !important;
        border-radius: 50% !important;
        border: 1.5px solid #16a34a !important;
        animation: cdpulse 2s infinite !important;
    }
    @keyframes cdpulse { 0%, 100% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.4); opacity: 0; } }
    .cd-status-main-text { font-size: 11.5px !important; font-weight: 700 !important; color: #15803d !important; }
    .cd-status-sub-text { font-size: 10px !important; color: #16a34a !important; font-weight: 500 !important; }

    .cd-hero-activity-strip {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px !important;
        margin-top: 10px !important;
        padding-top: 8px !important;
        border-top: 1px solid #f1f5f9 !important;
        font-size: 11px !important;
        color: #64748b !important;
    }

    /* 5 METRIC KPI SUB-CARDS GRID */
    .cd-kpi-grid {
        display: grid !important;
        grid-template-columns: repeat(5, 1fr) !important;
        gap: 10px !important;
        margin-bottom: 12px !important;
    }
    @media (max-width: 992px) { .cd-kpi-grid { grid-template-columns: repeat(3, 1fr) !important; } }
    @media (max-width: 576px) { .cd-kpi-grid { grid-template-columns: repeat(2, 1fr) !important; } }
    .cd-kpi-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 11px 13px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 8px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }
    .cd-kpi-card:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06) !important;
        border-color: #009ef7 !important;
    }
    .cd-kpi-card-left { display: flex !important; align-items: center !important; gap: 10px !important; min-width: 0 !important; }
    .cd-kpi-icon-square {
        width: 36px !important;
        height: 36px !important;
        border-radius: 8px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 13.5px !important;
        flex-shrink: 0 !important;
    }
    .cd-kpi-icon-square.blue   { background: #eff6ff !important; color: #2563eb !important; border: 1px solid #dbeafe !important; }
    .cd-kpi-icon-square.green  { background: #f0fdf4 !important; color: #16a34a !important; border: 1px solid #dcfce7 !important; }
    .cd-kpi-icon-square.purple { background: #f5f3ff !important; color: #7c3aed !important; border: 1px solid #ede9fe !important; }
    .cd-kpi-icon-square.orange { background: #fff7ed !important; color: #ea580c !important; border: 1px solid #ffedd5 !important; }
    .cd-kpi-icon-square.teal   { background: #f0fdfa !important; color: #0d9488 !important; border: 1px solid #ccfbf1 !important; }
    .cd-kpi-val { font-size: 14.5px !important; font-weight: 700 !important; color: #0f172a !important; line-height: 1.1 !important; }
    .cd-kpi-label { font-size: 11px !important; font-weight: 500 !important; color: #64748b !important; line-height: 1.2 !important; margin-top: 2px !important; }

    /* TIER 2: Dedicated Pure White Sub-Tabs Card */
    .cd-drawer-tabs-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 6px 10px !important;
        margin-bottom: 12px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    }
    .cd-drawer-tabs-list {
        display: flex !important;
        align-items: center !important;
        gap: 4px !important;
        overflow-x: auto !important;
        white-space: nowrap !important;
        padding: 2px !important;
    }
    .cd-tab-pill-btn {
        display: inline-flex !important;
        align-items: center !important;
        gap: 7px !important;
        padding: 7px 13px !important;
        border-radius: 7px !important;
        font-size: 12px !important;
        font-weight: 500 !important;
        color: #64748b !important;
        text-decoration: none !important;
        transition: all 0.15s ease-in-out !important;
        border: 1px solid transparent !important;
        cursor: pointer !important;
        background: transparent !important;
    }
    .cd-tab-pill-btn:hover { color: #009ef7 !important; background: rgba(0, 158, 247, 0.04) !important; }
    .cd-tab-pill-btn.active {
        color: #009ef7 !important;
        background: rgba(0, 158, 247, 0.08) !important;
        border-color: rgba(0, 158, 247, 0.25) !important;
        font-weight: 600 !important;
    }
    .cd-tab-badge-pill {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 10px !important;
        font-weight: 700 !important;
        padding: 1.5px 6px !important;
        border-radius: 999px !important;
        background: #eff6ff !important;
        color: #2563eb !important;
        border: 1px solid #bfdbfe !important;
        line-height: 1 !important;
    }

    /* TIER 3: Overview Sub-Cards Grid */
    .cd-overview-subcards-grid {
        display: grid !important;
        grid-template-columns: 1.35fr 1fr !important;
        gap: 14px !important;
        align-items: start !important;
    }
    @media (max-width: 1024px) { .cd-overview-subcards-grid { grid-template-columns: 1fr !important; } }
    .cd-master-subcard {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 14px 16px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    }
    .cd-subcard-header {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        margin-bottom: 12px !important;
        padding-bottom: 8px !important;
        border-bottom: 1px solid #f1f5f9 !important;
    }
    .cd-subcard-header-left { display: flex !important; align-items: center !important; gap: 8px !important; }
    .cd-subcard-header-icon {
        width: 26px !important;
        height: 26px !important;
        border-radius: 6px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 11px !important;
    }
    .cd-subcard-header-icon.blue   { background: #eff6ff !important; color: #2563eb !important; }
    .cd-subcard-header-icon.indigo { background: #eef2ff !important; color: #4f46e5 !important; }
    .cd-subcard-title { font-size: 13px !important; font-weight: 700 !important; color: #0f172a !important; margin: 0 !important; }

    .cd-info-subcards-grid {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        gap: 8px !important;
    }
    .cd-info-subcard-mini {
        background: #f8fafc !important;
        border: 1px solid #eef2f6 !important;
        border-radius: 7px !important;
        padding: 7px 10px !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 2px !important;
    }
    .cd-mini-label { font-size: 10px !important; font-weight: 600 !important; text-transform: uppercase !important; letter-spacing: 0.3px !important; color: #94a3b8 !important; }
    .cd-mini-value { font-size: 12px !important; font-weight: 600 !important; color: #0f172a !important; word-break: break-word !important; }

    /* Contacts Tab: Modern Card Stream (No Raw Table) */
    .cd-contacts-stream {
        display: flex !important;
        flex-direction: column !important;
        gap: 10px !important;
    }
    .cd-contact-card-subcard {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 12px 16px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 14px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02) !important;
        transition: all 0.15s ease-in-out !important;
    }
    .cd-contact-card-subcard:hover {
        border-color: #009ef7 !important;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05) !important;
        transform: translateY(-1px) !important;
    }
    .cd-contact-card-left {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        min-width: 0 !important;
        flex: 1 !important;
    }
    .cd-contact-avatar-round {
        width: 40px !important;
        height: 40px !important;
        border-radius: 8px !important;
        background: #eff6ff !important;
        color: #2563eb !important;
        border: 1px solid #bfdbfe !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-weight: 700 !important;
        font-size: 14px !important;
        flex-shrink: 0 !important;
    }
    .cd-contact-name-title-wrap {
        display: flex !important;
        flex-direction: column !important;
        min-width: 0 !important;
    }
    .cd-contact-fullname {
        font-size: 13px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
    }
    .cd-contact-designation {
        font-size: 11px !important;
        color: #64748b !important;
    }
    .cd-contact-channels-wrap {
        display: flex !important;
        align-items: center !important;
        gap: 14px !important;
        flex-wrap: wrap !important;
    }
    .cd-channel-badge {
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        font-size: 11.5px !important;
        color: #334155 !important;
        text-decoration: none !important;
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        padding: 4px 10px !important;
        border-radius: 6px !important;
        transition: all 0.15s !important;
    }
    .cd-channel-badge:hover {
        background: #ffffff !important;
        border-color: #009ef7 !important;
        color: #009ef7 !important;
    }
</style>

<div class="card-body position-relative px-0 py-0" id="kt_activities_body">
    <input type="hidden" name="hid_client_id" id="hid_client_id" value="{{ @$directClientDetail->client_id }}" />

    <!--begin::Content-->
    <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2 px-3" data-kt-scroll="true"
        data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body"
        data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">

        <div class="cd-req-scope">
            <!-- ── Tier 1: Action Header Toolbar Card ── -->
            <div class="cd-top-bar">
                <div class="cd-top-bar-left">
                    <div class="cd-top-title-wrap">
                        <div class="cd-top-breadcrumbs">
                            <span class="cd-breadcrumb-chip"><i class="fas fa-building text-primary me-1"></i> Clients</span>
                            <i class="fas fa-chevron-right text-muted" style="font-size: 8px;"></i>
                            <span class="cd-breadcrumb-chip fw-bold text-dark">{{ $detailName }}</span>
                            <span class="cd-client-id-pill">#{{ @$directClientDetail->client_id }}</span>
                        </div>
                        <div class="cd-top-badge-row">
                            <span class="cd-client-type-badge-pill direct">
                                <i class="fas fa-handshake me-1" style="color: #059669;"></i> Direct Client Request
                            </span>
                            @if (!empty($industries_name))
                                <span class="cd-param-badge-pill">
                                    <i class="fas fa-industry text-primary me-1"></i> {{ $industries_name }}
                                </span>
                            @endif
                            @if (!empty($city_name))
                                <span class="cd-param-badge-pill">
                                    <i class="fas fa-location-dot text-danger me-1"></i> {{ ucwords($city_name) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="cd-top-right-actions">
                    <button type="button" class="cd-hdr-btn-close cd-close-drawer" id="kt_activitiesRequest_close_btn" title="Close Drawer">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- ── Master Card: Client Identity & Quick Heuristics ── -->
            <div class="cd-hero-card">
                <div class="cd-hero-subcards-grid">
                    <!-- Sub-Card 1: Identity & Brand -->
                    <div class="cd-hero-subcard-identity">
                        <div class="cd-avatar-box">
                            @if (!empty($detailLogo))
                                <img src="{{ $detailLogo }}" alt="{{ $detailName }}">
                            @else
                                <span class="cd-avatar-initials">{{ $detailInitials }}</span>
                            @endif
                            <div class="cd-verified-seal" title="Verified Direct Client">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                        <div class="cd-identity-meta">
                            <div class="cd-identity-title-row">
                                <h2 class="cd-identity-name">{{ $detailName }}</h2>
                                <span class="cd-type-badge-tag direct">Direct Client</span>
                            </div>
                            <div class="cd-identity-subtitle">
                                {{ $rc_company_type_val }}
                            </div>
                        </div>
                    </div>

                    <!-- Sub-Card 2: Discrete Parameter Chips -->
                    <div class="cd-hero-subcard-params">
                        @if ($industries_name)
                            <div class="cd-hero-param-chip">
                                <i class="fas fa-building text-primary"></i>
                                <span>{{ $industries_name }}</span>
                            </div>
                        @endif
                        <div class="cd-hero-param-chip">
                            <i class="fas fa-map-marker-alt text-danger"></i>
                            <span>{{ ucwords($city_name) }}@if($country_name), {{ ucwords($country_name) }}@endif</span>
                        </div>
                        @if (!empty($webClean))
                            <a href="{{ str_starts_with($webClean, 'http') ? $webClean : 'https://'.$webClean }}" target="_blank" class="cd-hero-param-chip link">
                                <i class="fas fa-globe text-info"></i>
                                <span class="text-truncate" style="max-width: 140px;">{{ $webClean }}</span>
                                <i class="fas fa-external-link-alt ms-1" style="font-size: 8px;"></i>
                            </a>
                        @endif
                        <div class="cd-hero-param-chip">
                            <i class="fas fa-id-badge text-warning"></i>
                            <span>VID: <strong class="text-dark">VEN-{{ @$directClientDetail->client_id }}</strong></span>
                        </div>
                    </div>

                    <!-- Sub-Card 3: Activity & Pulse Status -->
                    <div class="cd-hero-subcard-activity">
                        <div class="cd-status-pulse-badge {{ $isClientActive ? 'active' : 'inactive' }}">
                            <span class="cd-pulse-indicator {{ $isClientActive ? 'pulse-green' : 'pulse-grey' }}"></span>
                            <div class="cd-status-text-wrap">
                                <div class="cd-status-main-text">{{ $isClientActive ? 'Client Active' : 'Terms Review' }}</div>
                                <div class="cd-status-sub-text">{{ $kpiActive }} active {{ $kpiActive == 1 ? 'job' : 'jobs' }}</div>
                            </div>
                        </div>
                        <div class="cd-onboarding-chip">
                            <i class="fas fa-history text-muted me-1"></i>
                            <span>Since <strong>{{ $clientSinceYear }}</strong> <span class="mx-1">•</span> {{ $clientUpdatedAt }}</span>
                        </div>
                    </div>
                </div>

                <!-- Discrete Bottom Activity Strip -->
                <div class="cd-hero-activity-strip">
                    <div>
                        <i class="fas fa-file-contract text-primary me-1"></i> Direct Client Terms &amp; Master Agreement Request
                    </div>
                    <div>
                        <span class="badge bg-light-success text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Verified Partnership</span>
                    </div>
                </div>
            </div>

            <!-- ── 5 Metric KPI Sub-Cards Row ── -->
            <div class="cd-kpi-grid">
                <div class="cd-kpi-card">
                    <div class="cd-kpi-card-left">
                        <div class="cd-kpi-icon-square blue"><i class="fas fa-briefcase"></i></div>
                        <div>
                            <div class="cd-kpi-val">{{ $kpiActive }}</div>
                            <div class="cd-kpi-label">Active Jobs</div>
                        </div>
                    </div>
                </div>
                <div class="cd-kpi-card">
                    <div class="cd-kpi-card-left">
                        <div class="cd-kpi-icon-square green"><i class="fas fa-users"></i></div>
                        <div>
                            <div class="cd-kpi-val">{{ $kpiSubmitted }}</div>
                            <div class="cd-kpi-label">Submitted</div>
                        </div>
                    </div>
                </div>
                <div class="cd-kpi-card">
                    <div class="cd-kpi-card-left">
                        <div class="cd-kpi-icon-square purple"><i class="fas fa-calendar-check"></i></div>
                        <div>
                            <div class="cd-kpi-val">{{ $kpiInterviews }}</div>
                            <div class="cd-kpi-label">Interviews</div>
                        </div>
                    </div>
                </div>
                <div class="cd-kpi-card">
                    <div class="cd-kpi-card-left">
                        <div class="cd-kpi-icon-square orange"><i class="fas fa-file-signature"></i></div>
                        <div>
                            <div class="cd-kpi-val">{{ $kpiOffers }}</div>
                            <div class="cd-kpi-label">Offers</div>
                        </div>
                    </div>
                </div>
                <div class="cd-kpi-card">
                    <div class="cd-kpi-card-left">
                        <div class="cd-kpi-icon-square teal"><i class="fas fa-user-check"></i></div>
                        <div>
                            <div class="cd-kpi-val">{{ $kpiJoins }}</div>
                            <div class="cd-kpi-label">Joined</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Tier 2: Dedicated Pure White Sub-Tabs Card ── -->
            <div class="cd-drawer-tabs-card">
                <div class="cd-drawer-tabs-list" role="tablist">
                    <a class="cd-tab-pill-btn active" data-toggle="tab" href="#overview_req" role="tab">
                        <i class="fas fa-house"></i>
                        <span>Overview</span>
                    </a>
                    @if (!empty($client_contact_info) && count($client_contact_info) > 0)
                    <a class="cd-tab-pill-btn" data-toggle="tab" href="#contacts_req" role="tab">
                        <i class="fas fa-address-book"></i>
                        <span>Contacts</span>
                        <span class="cd-tab-badge-pill">{{ count($client_contact_info) }}</span>
                    </a>
                    @endif
                </div>
            </div>

            <!-- ── Tab Contents Container ── -->
            <div class="tab-content">
                <!-- Overview Tab Pane -->
                <div class="tab-pane active" id="overview_req" role="tabpanel">
                    <div class="cd-overview-subcards-grid py-2">
                        <!-- Left Column -->
                        <div class="cd-master-subcard">
                            <div class="cd-subcard-header">
                                <div class="cd-subcard-header-left">
                                    <div class="cd-subcard-header-icon blue"><i class="fas fa-info-circle"></i></div>
                                    <h4 class="cd-subcard-title">About the Client</h4>
                                </div>
                                @if (!empty($webClean))
                                <a href="{{ str_starts_with($webClean, 'http') ? $webClean : 'https://'.$webClean }}" target="_blank" class="cd-subcard-action-link">
                                    {{ $webClean }} <i class="fas fa-external-link-alt ms-1"></i>
                                </a>
                                @endif
                            </div>
                            <div style="font-size: 12px; line-height: 1.65; color: #475569;">
                                @if (!empty(@$directClientDetail->description))
                                    {!! @$directClientDetail->description !!}
                                @else
                                    {{ $detailName }} is an authorized enterprise client engaging through direct contractual agreement. All hiring requisitions and terms are authoritative.
                                @endif
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="cd-master-subcard">
                            <div class="cd-subcard-header">
                                <div class="cd-subcard-header-left">
                                    <div class="cd-subcard-header-icon indigo"><i class="fas fa-building"></i></div>
                                    <h4 class="cd-subcard-title">Client Information</h4>
                                </div>
                            </div>
                            <div class="cd-info-subcards-grid">
                                <div class="cd-info-subcard-mini">
                                    <span class="cd-mini-label">Headquarters</span>
                                    <span class="cd-mini-value">{{ ucwords($city_name) }}@if($country_name), {{ ucwords($country_name) }}@endif</span>
                                </div>
                                @if (!empty($webClean))
                                <div class="cd-info-subcard-mini">
                                    <span class="cd-mini-label">Website</span>
                                    <span class="cd-mini-value">
                                        <a href="{{ str_starts_with($webClean, 'http') ? $webClean : 'https://'.$webClean }}" target="_blank" class="text-truncate d-inline-block" style="max-width:140px;">
                                            {{ $webClean }} <i class="fas fa-external-link-alt ms-1" style="font-size:8px;"></i>
                                        </a>
                                    </span>
                                </div>
                                @endif
                                @if ($industries_name)
                                <div class="cd-info-subcard-mini">
                                    <span class="cd-mini-label">Industry</span>
                                    <span class="cd-mini-value">{{ $industries_name }}</span>
                                </div>
                                @endif
                                <div class="cd-info-subcard-mini">
                                    <span class="cd-mini-label">Company Type</span>
                                    <span class="cd-mini-value">{{ $rc_company_type_val }}</span>
                                </div>
                                <div class="cd-info-subcard-mini">
                                    <span class="cd-mini-label">Vendor ID</span>
                                    <span class="cd-mini-value fw-bold text-dark">VEN-{{ @$directClientDetail->client_id }}</span>
                                </div>
                                @if (@$directClientDetail->founded)
                                <div class="cd-info-subcard-mini">
                                    <span class="cd-mini-label">Founded</span>
                                    <span class="cd-mini-value">
                                        @php
                                            $currentYr = date('Y');
                                            $foundedYr = @$directClientDetail->founded;
                                            $diffYr = $currentYr - $foundedYr;
                                            echo $diffYr > 0 ? $diffYr . ' Years ago' : $foundedYr;
                                        @endphp
                                    </span>
                                </div>
                                @endif
                                @if (@$directClientDetail->company_size)
                                <div class="cd-info-subcard-mini">
                                    <span class="cd-mini-label">Employees</span>
                                    <span class="cd-mini-value">{{ is_numeric(@$directClientDetail->company_size) ? number_format((float)@$directClientDetail->company_size) . '+' : @$directClientDetail->company_size }}</span>
                                </div>
                                @endif
                                @if (!empty(@$directClientDetail->to_created_at))
                                <div class="cd-info-subcard-mini">
                                    <span class="cd-mini-label">Connected On</span>
                                    <span class="cd-mini-value">{{ date('d M, Y', strtotime(@$directClientDetail->to_created_at)) }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contacts Tab Pane -->
                @if (!empty($client_contact_info) && count($client_contact_info) > 0)
                <div class="tab-pane" id="contacts_req" role="tabpanel">
                    <div class="cd-contacts-stream my-3">
                        @foreach($client_contact_info as $cci)
                        <div class="cd-contact-card-subcard">
                            <div class="cd-contact-card-left">
                                <div class="cd-contact-avatar-round">
                                    {{ strtoupper(mb_substr(@$cci->contact_person_name ?: 'C', 0, 1)) }}
                                </div>
                                <div class="cd-contact-name-title-wrap">
                                    <span class="cd-contact-fullname">{{ @$cci->contact_person_name }}</span>
                                    <span class="cd-contact-designation">{{ @$cci->designation ?: 'Client Stakeholder' }}</span>
                                </div>
                            </div>
                            <div class="cd-contact-channels-wrap">
                                @if(!empty(@$cci->email))
                                <a href="mailto:{{ @$cci->email }}" class="cd-channel-badge" title="Send Email">
                                    <i class="fas fa-envelope text-primary"></i>
                                    <span>{{ @$cci->email }}</span>
                                </a>
                                @endif
                                @if(!empty(@$cci->phone))
                                <a href="tel:{{ @$cci->phone }}" class="cd-channel-badge" title="Call Phone">
                                    <i class="fas fa-phone text-success"></i>
                                    <span>{{ @$cci->phone }}</span>
                                </a>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>
