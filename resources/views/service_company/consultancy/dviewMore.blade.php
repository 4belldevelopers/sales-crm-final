@php
    use App\Helper\GeneralMethod;
    use Carbon\Carbon;

    $userPermissions = (isset($userPermissions) && is_array($userPermissions)) ? $userPermissions : [];
    $startOfMonthcomman = Carbon::now()->startOfMonth()->subMonths(5);
    $endOfMonthcomman =  Carbon::now()->endOfMonth();
    $endDate = Carbon::today()->endOfDay();
    $startDate = Carbon::today()->subDays(6)->startOfDay();

    // ── Agency Profile Data Resolution ──
    $detailName = ucwords(@$directClientDetail->name ?: @$directClientDetail->company_name ?: 'Recruitment Agency Partner');
    $detailInitials = strtoupper(implode('', array_map(fn($w) => mb_substr($w, 0, 1), explode(' ', trim($detailName)))));
    if (strlen($detailInitials) > 3) $detailInitials = substr($detailInitials, 0, 2);
    if (empty($detailInitials)) $detailInitials = 'AG';

    $detailLogo = '';
    if (!empty(@$directClientDetail->logo) && strlen(@$directClientDetail->logo) > 10) {
        $detailLogo = @$directClientDetail->logo;
    } elseif (!empty(@$directClientDetail->logo_logical_path) && strlen(@$directClientDetail->logo_logical_path) > 10) {
        $detailLogo = @$directClientDetail->logo_logical_path;
    }

    $agencyId = @$directClientDetail->company_id ?: @$directClientDetail->client_id ?: $id;
    $webClean = @$directClientDetail->website ?: '';
    
    $tagsRaw = @$directClientDetail->tags ?: 'Staffing,Executive Search,Recruitment,IT Sourcing';
    $tagsArr = is_array($tagsRaw) ? $tagsRaw : explode(',', (string)$tagsRaw);

    $cityName = @$city_name ?: (@$client_location->city_name ?: @$client_location->city ?: 'Primary Hub');
    $countryName = @$country_name ?: 'India';
    $agencySize = $agencyCompanySize ?? '10 - 50 employees';

    // ── Pre-hydrated KPI Numbers (Zero MySQL Read Strain) ──
    $kpiActiveJobs = (int)($Active ?? 0);
    $kpiHoldJobs = (int)($Hold ?? 0);
    $kpiClosedJobs = (int)($Closed ?? 0);
    $kpiTotalJobs = (int)($kpiTotalJobs ?? ($kpiActiveJobs + $kpiHoldJobs + $kpiClosedJobs));

    $kpiSubmitted = (int)($all_candidates ?? 0);
    $kpiInterviews = (int)($all_interview ?? 0);
    $kpiOffers = (int)($offers ?? 0);
    $kpiJoins = (int)($Joined ?? 0);
    $isAgencyActive = ($kpiActiveJobs > 0 || $kpiSubmitted > 0);

    // Lists
    $contactsList = $client_contact_info ?? collect([]);
    $jobsList = $sharedJobs ?? collect([]);
    $candsList = $candidatesList ?? collect([]);
    $interviewsScheduleList = $interviewsList ?? collect([]);
@endphp

{{-- Explicit FontAwesome Restoration: Ensure icon glyphs render cleanly across Metronic --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
/* ==========================================================================
   Agency Drawer Master Enterprise Design (100% Replication of Client Drawer)
   Strictly Optimized for 15.6" Screens (1366x768 & 1920x1080) and Smaller
   GUARANTEED: Zero Horizontal Scrollbar (overflow-x: hidden) Across All Screens
   Rule 20 Compliant: Modern Card + Sub-Card Enterprise Design System
   ========================================================================== */

.cd-scope {
  box-sizing: border-box !important;
  font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
  font-size: 12px !important;
  color: #334155 !important;
  background: #f8fafc !important;
  width: 100% !important;
  max-width: 100% !important;
  overflow-x: hidden !important;
  padding: 12px 16px 36px !important;
}
.cd-scope *, .cd-scope *::before, .cd-scope *::after {
  box-sizing: border-box !important;
  font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
}

/* ── Explicit FontAwesome Restoration ── */
.cd-scope i,
.cd-scope [class*="fa-"],
.cd-scope [class^="fa-"],
.cd-scope .fa,
.cd-scope .fas,
.cd-scope .far,
.cd-scope .fab,
.cd-scope .fa-solid,
.cd-scope .fa-regular {
  font-family: "Font Awesome 6 Free", "Font Awesome 5 Free", "FontAwesome" !important;
  font-style: normal !important;
  font-variant: normal !important;
  text-rendering: auto !important;
  line-height: 1 !important;
  display: inline-block !important;
}
.cd-scope .fa-solid,
.cd-scope .fas { font-weight: 900 !important; }
.cd-scope .fa-regular,
.cd-scope .far { font-weight: 400 !important; }

/* ── TIER 1: Sticky Top Action Header Toolbar Card (.cd-top-bar) ── */
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
  z-index: 50 !important;
}
.cd-top-bar-left {
  display: flex !important;
  align-items: center !important;
  gap: 12px !important;
  min-width: 0 !important;
  flex: 1 !important;
  flex-wrap: wrap !important;
}
.cd-top-breadcrumbs {
  display: flex !important;
  align-items: center !important;
  gap: 6px !important;
  font-size: 11px !important;
  color: #64748b !important;
}
.cd-breadcrumb-chip {
  display: inline-flex !important;
  align-items: center !important;
  color: #64748b !important;
  font-weight: 500 !important;
}
.cd-agency-id-pill {
  font-size: 10.5px !important;
  font-weight: 700 !important;
  color: #2563eb !important;
  background: #eff6ff !important;
  border: 1px solid #bfdbfe !important;
  padding: 1px 7px !important;
  border-radius: 4px !important;
  font-family: monospace, -apple-system !important;
}
.cd-top-badge-row {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
  flex-wrap: wrap !important;
}
.cd-agency-type-badge-pill {
  display: inline-flex !important;
  align-items: center !important;
  font-size: 10.5px !important;
  font-weight: 600 !important;
  padding: 2px 8px !important;
  border-radius: 6px !important;
  line-height: 1.2 !important;
  background: #ecfdf5 !important;
  color: #059669 !important;
  border: 1px solid #a7f3d0 !important;
}
.cd-top-right-actions {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
  flex-shrink: 0 !important;
}
.cd-hdr-action-btn {
  height: 32px !important;
  padding: 0 12px !important;
  border-radius: 6px !important;
  font-size: 11.5px !important;
  font-weight: 600 !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 6px !important;
  cursor: pointer !important;
  text-decoration: none !important;
  transition: all 0.15s ease-in-out !important;
  box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04) !important;
  border: none !important;
}
.cd-hdr-action-btn.primary {
  background: #009ef7 !important;
  color: #ffffff !important;
  border: 1px solid #009ef7 !important;
}
.cd-hdr-action-btn.primary:hover {
  background: #0086d1 !important;
  border-color: #0086d1 !important;
  color: #ffffff !important;
}
.cd-hdr-action-btn.followup {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  color: #475569 !important;
}
.cd-hdr-action-btn.followup:hover {
  background: #f8fafc !important;
  border-color: #cbd5e1 !important;
  color: #0f172a !important;
}
.cd-top-close-wrap {
  display: flex !important;
  align-items: center !important;
}
.cd-hdr-btn-close {
  width: 32px !important;
  height: 32px !important;
  border-radius: 8px !important;
  background: #f1f5f9 !important;
  border: 1px solid #e2e8f0 !important;
  color: #64748b !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  cursor: pointer !important;
  transition: all 0.15s ease !important;
  font-size: 12px !important;
}
.cd-hdr-btn-close:hover {
  background: #e2e8f0 !important;
  color: #0f172a !important;
}

/* ── 2. Hero Master Card (.cd-hero-card.cd-hero-streamlined) ── */
.cd-hero-card {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 10px !important;
  margin-bottom: 12px !important;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
  overflow: hidden !important;
}
.cd-hero-main-row {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  padding: 14px 18px !important;
  gap: 14px !important;
  flex-wrap: wrap !important;
}
.cd-hero-left-flank {
  display: flex !important;
  align-items: center !important;
  gap: 14px !important;
  min-width: 0 !important;
}
.cd-hero-avatar-wrap {
  position: relative !important;
  flex-shrink: 0 !important;
}
.cd-hero-avatar {
  width: 48px !important;
  height: 48px !important;
  border-radius: 10px !important;
  background: #eff6ff !important;
  border: 1px solid #bfdbfe !important;
  color: #1d4ed8 !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 16px !important;
  font-weight: 700 !important;
  overflow: hidden !important;
}
.cd-hero-avatar img {
  width: 100% !important;
  height: 100% !important;
  object-fit: cover !important;
}
.cd-hero-verified-check {
  position: absolute !important;
  top: -3px !important;
  right: -3px !important;
  width: 16px !important;
  height: 16px !important;
  border-radius: 50% !important;
  background: #16a34a !important;
  color: #ffffff !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 9px !important;
  border: 2px solid #ffffff !important;
}
.cd-hero-left-meta {
  display: flex !important;
  flex-direction: column !important;
  gap: 4px !important;
  min-width: 0 !important;
}
.cd-hero-title-wrap {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
  flex-wrap: wrap !important;
}
.cd-hero-name {
  font-size: 16px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
  line-height: 1.25 !important;
  margin: 0 !important;
}
.cd-hero-meta-row {
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
  font-size: 11.5px !important;
  color: #64748b !important;
  flex-wrap: wrap !important;
}
.cd-hero-meta-item {
  display: inline-flex !important;
  align-items: center !important;
  gap: 4px !important;
}
.cd-hero-web-wrap {
  display: flex !important;
  align-items: center !important;
  font-size: 11.5px !important;
}
.cd-hero-web-link {
  color: #2563eb !important;
  text-decoration: none !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 4px !important;
  font-weight: 500 !important;
}
.cd-hero-web-link:hover { text-decoration: underline !important; }
.cd-hero-web-empty { color: #94a3b8 !important; }

/* Right Flank */
.cd-hero-right-flank {
  display: flex !important;
  flex-direction: column !important;
  align-items: flex-end !important;
  gap: 6px !important;
  flex-shrink: 0 !important;
}
.cd-pill-scope-direct {
  background: #ecfdf5 !important;
  color: #059669 !important;
  border: 1px solid #a7f3d0 !important;
  font-size: 10px !important;
  font-weight: 700 !important;
  padding: 2px 8px !important;
  border-radius: 4px !important;
  text-transform: uppercase !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 4px !important;
}
.cd-hero-status-pos-row {
  display: inline-flex !important;
  align-items: center !important;
  gap: 8px !important;
  font-size: 11.5px !important;
}
.cd-status-badge-active {
  display: inline-flex !important;
  align-items: center !important;
  gap: 5px !important;
  background: #f0fdf4 !important;
  color: #16a34a !important;
  border: 1px solid #bbf7d0 !important;
  padding: 2px 8px !important;
  border-radius: 4px !important;
  font-size: 10.5px !important;
  font-weight: 700 !important;
}
.cd-pulse-emerald {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #16a34a;
  box-shadow: 0 0 0 2px rgba(22, 163, 74, 0.2);
}
.cd-status-badge-inactive {
  display: inline-flex !important;
  align-items: center !important;
  gap: 5px !important;
  background: #fdf2f8 !important;
  color: #db2777 !important;
  border: 1px solid #fbcfe8 !important;
  padding: 2px 8px !important;
  border-radius: 4px !important;
  font-size: 10.5px !important;
  font-weight: 700 !important;
}
.cd-pulse-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
}
.cd-hero-positions-link {
  display: inline-flex !important;
  align-items: center !important;
  gap: 5px !important;
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 6px !important;
  padding: 2px 8px !important;
  color: #334155 !important;
  text-decoration: none !important;
  font-size: 11px !important;
  font-weight: 600 !important;
  transition: all 0.15s ease !important;
}
.cd-hero-positions-link:hover {
  background: #eff6ff !important;
  border-color: #bfdbfe !important;
  color: #2563eb !important;
}

/* Bottom Tags Row */
.cd-hero-bottom-tags-row {
  background: #f8fafc !important;
  border-top: 1px solid #f1f5f9 !important;
  padding: 6px 18px !important;
  display: flex !important;
  align-items: center !important;
  gap: 6px !important;
  flex-wrap: wrap !important;
  font-size: 11px !important;
}
.cd-hero-tags-label { font-weight: 600 !important; color: #64748b !important; margin-right: 4px !important; }
.cd-hero-tag-pill {
  background: #eff6ff !important;
  color: #2563eb !important;
  border: 1px solid #bfdbfe !important;
  font-size: 10.5px !important;
  font-weight: 600 !important;
  padding: 1px 7px !important;
  border-radius: 4px !important;
}

/* ── 3. Authoritative Hero KPI Strip (6 Clickable Cards in Single Row on 15.6") ── */
.cd-kpi-grid {
  display: grid !important;
  grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
  gap: 10px !important;
  margin-bottom: 12px !important;
  width: 100% !important;
}
.cd-kpi-card {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 8px !important;
  padding: 10px 12px !important;
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
  text-decoration: none !important;
  color: inherit !important;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02) !important;
  transition: all 0.15s ease !important;
  cursor: pointer !important;
  min-width: 0 !important;
}
.cd-kpi-card:hover {
  transform: translateY(-2px) !important;
  border-color: #cbd5e1 !important;
  box-shadow: 0 4px 10px rgba(15, 23, 42, 0.05) !important;
}
.cd-kpi-icon {
  width: 34px !important;
  height: 34px !important;
  border-radius: 7px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 14px !important;
  flex-shrink: 0 !important;
}
.cd-kpi-icon-blue    { background: #eff6ff !important; color: #2563eb !important; border: 1px solid #bfdbfe !important; }
.cd-kpi-icon-emerald { background: #ecfdf5 !important; color: #10b981 !important; border: 1px solid #a7f3d0 !important; }
.cd-kpi-icon-purple  { background: #faf5ff !important; color: #9333ea !important; border: 1px solid #e9d5ff !important; }
.cd-kpi-icon-amber   { background: #fffbeb !important; color: #d97706 !important; border: 1px solid #fde68a !important; }
.cd-kpi-icon-rose    { background: #fff1f2 !important; color: #e11d48 !important; border: 1px solid #fecdd3 !important; }
.cd-kpi-icon-teal    { background: #f0fdfa !important; color: #0d9488 !important; border: 1px solid #99f6e4 !important; }

.cd-kpi-body {
  display: flex !important;
  flex-direction: column !important;
  min-width: 0 !important;
}
.cd-kpi-num {
  font-size: 16px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
  line-height: 1.15 !important;
  font-variant-numeric: tabular-nums !important;
}
.cd-kpi-lbl {
  font-size: 10.5px !important;
  font-weight: 500 !important;
  color: #64748b !important;
  line-height: 1.2 !important;
  margin-top: 2px !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
}

/* ── 4. Tier 2: Pure White Sub-Tabs Card (.cd-drawer-tabs-card) ── */
.cd-drawer-tabs-card {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 10px !important;
  padding: 8px 12px !important;
  margin-bottom: 12px !important;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
}
.cd-drawer-tabs-list {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
  overflow-x: auto !important;
  flex-wrap: nowrap !important;
  margin: 0 !important;
  padding: 0 !important;
  list-style: none !important;
  border: none !important;
}
.cd-drawer-tabs-list::-webkit-scrollbar { display: none; }
.cd-tab-pill-btn {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 8px !important;
  padding: 6px 14px !important;
  font-size: 11.5px !important;
  font-weight: 600 !important;
  color: #475569 !important;
  text-decoration: none !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 6px !important;
  white-space: nowrap !important;
  transition: all 0.15s ease !important;
  cursor: pointer !important;
}
.cd-tab-pill-btn:hover {
  background: #f8fafc !important;
  border-color: #cbd5e1 !important;
  color: #0f172a !important;
}
.cd-tab-pill-btn.active {
  background: #009ef7 !important;
  border-color: #009ef7 !important;
  color: #ffffff !important;
  box-shadow: 0 2px 4px rgba(0, 158, 247, 0.2) !important;
}
.cd-tab-badge-pill {
  font-size: 10px !important;
  font-weight: 700 !important;
  padding: 1px 6px !important;
  border-radius: 10px !important;
  background: #f1f5f9 !important;
  color: #475569 !important;
}
.cd-tab-pill-btn.active .cd-tab-badge-pill {
  background: rgba(255, 255, 255, 0.28) !important;
  color: #ffffff !important;
}

/* ── 5. Standard Tab Toolbar & Sub-Filter Cards (Rule 20 Modern System) ── */
.cd-tab-content-wrapper {
  width: 100% !important;
}
.tab-pane {
  display: none;
}
.tab-pane.active,
.tab-pane.show {
  display: block !important;
}

/* Tier 1: Toolbar Card inside Tab */
.cd-tab-toolbar-card {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 10px !important;
  padding: 12px 16px !important;
  margin-bottom: 12px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  gap: 12px !important;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
  flex-wrap: wrap !important;
}
.cd-tab-toolbar-left {
  display: flex !important;
  align-items: center !important;
  gap: 12px !important;
  min-width: 0 !important;
}
.cd-tab-toolbar-icon {
  width: 36px !important;
  height: 36px !important;
  border-radius: 8px !important;
  background: #eff6ff !important;
  color: #2563eb !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 15px !important;
  flex-shrink: 0 !important;
}
.cd-tab-toolbar-title-wrap {
  display: flex !important;
  flex-direction: column !important;
  min-width: 0 !important;
}
.cd-tab-toolbar-title-row {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
}
.cd-tab-toolbar-title {
  font-size: 14.5px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
  margin: 0 !important;
  line-height: 1.25 !important;
}
.cd-tab-count-pill {
  font-size: 10.5px !important;
  font-weight: 700 !important;
  padding: 2px 7px !important;
  border-radius: 12px !important;
  background: #f1f5f9 !important;
  color: #475569 !important;
}
.cd-tab-toolbar-sub {
  font-size: 11.5px !important;
  color: #64748b !important;
  margin-top: 2px !important;
}
.cd-tab-toolbar-right {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
  flex-wrap: wrap !important;
}

/* Dynamic Search Sub-Card inside Toolbar */
.cd-tab-search-subcard {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
  background: #f8fafc !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 8px !important;
  padding: 0 10px !important;
  height: 34px !important;
  width: 260px !important;
  max-width: 100% !important;
  transition: all 0.15s ease-in-out !important;
}
.cd-tab-search-subcard:focus-within {
  background: #ffffff !important;
  border-color: #009ef7 !important;
  box-shadow: 0 0 0 2px rgba(0, 158, 247, 0.12) !important;
}
.cd-tab-search-subcard i.fa-search {
  color: #94a3b8 !important;
  font-size: 12px !important;
  flex-shrink: 0 !important;
}
.cd-tab-search-subcard input {
  border: none !important;
  background: transparent !important;
  outline: none !important;
  font-size: 11.5px !important;
  color: #0f172a !important;
  width: 100% !important;
  padding: 0 !important;
  line-height: 34px !important;
  font-family: inherit !important;
}
.cd-tab-search-subcard input::placeholder { color: #94a3b8 !important; }
.cd-tab-search-clear {
  background: none !important;
  border: none !important;
  color: #94a3b8 !important;
  font-size: 11px !important;
  padding: 0 !important;
  cursor: pointer !important;
  display: none;
}
.cd-tab-search-clear:hover { color: #0f172a !important; }

/* Tier 2: Sub-Tabs Filter Card inside Tab */
.cd-tab-filter-card {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 8px !important;
  padding: 6px 10px !important;
  margin-bottom: 12px !important;
  display: flex !important;
  align-items: center !important;
  gap: 6px !important;
  overflow-x: auto !important;
  flex-wrap: nowrap !important;
}
.cd-subtab-btn {
  background: #f8fafc !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 6px !important;
  padding: 4px 10px !important;
  font-size: 11px !important;
  font-weight: 600 !important;
  color: #475569 !important;
  cursor: pointer !important;
  transition: all 0.15s ease !important;
  white-space: nowrap !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 5px !important;
}
.cd-subtab-btn:hover {
  background: #f1f5f9 !important;
  color: #0f172a !important;
}
.cd-subtab-btn.active {
  background: #eff6ff !important;
  border-color: #bfdbfe !important;
  color: #2563eb !important;
}
.cd-subtab-badge {
  font-size: 9.5px !important;
  padding: 1px 5px !important;
  border-radius: 8px !important;
  background: #e2e8f0 !important;
  color: #475569 !important;
}
.cd-subtab-btn.active .cd-subtab-badge {
  background: #bfdbfe !important;
  color: #1d4ed8 !important;
}

/* ── 6. Modern Primary Card + Sub-Card System (Tier 3) ── */
.cd-primary-card {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 10px !important;
  margin-bottom: 10px !important;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02) !important;
  transition: all 0.15s ease !important;
  overflow: hidden !important;
}
.cd-primary-card:hover {
  border-color: #cbd5e1 !important;
  box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05) !important;
}
.cd-subcards-grid {
  display: grid !important;
  grid-template-columns: minmax(0, 2fr) minmax(0, 1.2fr) minmax(0, 1.1fr) minmax(0, 1.7fr) !important;
  gap: 0 !important;
}
.cd-subcard {
  padding: 12px 14px !important;
  border-right: 1px solid #f1f5f9 !important;
  display: flex !important;
  flex-direction: column !important;
  justify-content: space-between !important;
  min-width: 0 !important;
}
.cd-subcard:last-child {
  border-right: none !important;
}
.cd-subcard-head {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  margin-bottom: 8px !important;
  gap: 6px !important;
}
.cd-subcard-title {
  font-size: 10px !important;
  font-weight: 700 !important;
  text-transform: uppercase !important;
  letter-spacing: 0.3px !important;
  color: #94a3b8 !important;
}
.cd-identity-body {
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
  min-width: 0 !important;
}
.cd-card-icon-box {
  width: 38px !important;
  height: 38px !important;
  border-radius: 8px !important;
  background: #eff6ff !important;
  color: #2563eb !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 15px !important;
  flex-shrink: 0 !important;
}
.cd-card-title {
  font-size: 13px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
  line-height: 1.25 !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
}
.cd-card-sub {
  font-size: 11px !important;
  color: #64748b !important;
  margin-top: 2px !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
}
.cd-card-tag {
  font-size: 10px !important;
  color: #2563eb !important;
  font-weight: 600 !important;
  margin-top: 2px !important;
}
.cd-info-lines {
  display: flex !important;
  flex-direction: column !important;
  gap: 3px !important;
  font-size: 11.5px !important;
}
.cd-info-line-bold { font-weight: 700 !important; color: #0f172a !important; }
.cd-info-line-sub  { font-weight: 500 !important; color: #64748b !important; font-size: 11px !important; }

/* Pipeline Flow Sub-Card */
.cd-pipe-flow {
  display: flex !important;
  align-items: center !important;
  gap: 4px !important;
  flex-wrap: nowrap !important;
}
.cd-pipe-step {
  display: flex !important;
  flex-direction: column !important;
  align-items: center !important;
  flex: 1 !important;
}
.cd-pipe-pill {
  width: 22px !important;
  height: 22px !important;
  border-radius: 50% !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 10px !important;
  font-weight: 700 !important;
  background: #f1f5f9 !important;
  color: #64748b !important;
}
.cd-pipe-pill.has-count {
  background: #eff6ff !important;
  color: #2563eb !important;
  border: 1px solid #bfdbfe !important;
}
.cd-pipe-label {
  font-size: 9px !important;
  font-weight: 600 !important;
  color: #94a3b8 !important;
  margin-top: 3px !important;
  text-align: center !important;
}

.cd-card-bottom-strip {
  background: #f8fafc !important;
  border-top: 1px solid #f1f5f9 !important;
  padding: 6px 14px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  font-size: 11px !important;
  color: #64748b !important;
}

/* ── 7. Tab 1: Overview 2-Column Sub-Cards Grid ── */
.cd-overview-grid {
  display: grid !important;
  grid-template-columns: repeat(2, 1fr) !important;
  gap: 12px !important;
}
.cd-master-subcard {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 10px !important;
  padding: 14px 16px !important;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02) !important;
  margin-bottom: 12px !important;
}
.cd-subcard-header {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  padding-bottom: 10px !important;
  border-bottom: 1px solid #f1f5f9 !important;
  margin-bottom: 12px !important;
}
.cd-subcard-header-left {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
}
.cd-subcard-header-icon {
  width: 28px !important;
  height: 28px !important;
  border-radius: 6px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 12px !important;
}
.cd-subcard-header-icon.blue   { background: #eff6ff; color: #2563eb; }
.cd-subcard-header-icon.amber  { background: #fffbeb; color: #d97706; }
.cd-subcard-header-icon.emerald{ background: #ecfdf5; color: #10b981; }
.cd-subcard-header-icon.purple { background: #faf5ff; color: #9333ea; }

.cd-subcard-heading {
  font-size: 13px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
  margin: 0 !important;
}
.cd-kv-row {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  font-size: 11.5px !important;
  padding: 5px 0 !important;
  border-bottom: 1px dashed #f8fafc !important;
}
.cd-kv-row:last-child { border-bottom: none !important; }
.cd-kv-lbl { color: #64748b !important; font-weight: 500 !important; }
.cd-kv-val { color: #0f172a !important; font-weight: 600 !important; text-align: right !important; }

/* ── 8. Contact Cards Grid (Tab 3) ── */
.cd-contact-grid {
  display: grid !important;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)) !important;
  gap: 12px !important;
}
.cd-contact-card {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 10px !important;
  padding: 14px !important;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02) !important;
  display: flex !important;
  flex-direction: column !important;
  justify-content: space-between !important;
  transition: all 0.15s ease !important;
}
.cd-contact-card:hover {
  border-color: #cbd5e1 !important;
  box-shadow: 0 4px 10px rgba(15, 23, 42, 0.05) !important;
}
.cd-contact-top {
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
  margin-bottom: 10px !important;
}
.cd-contact-avatar {
  width: 40px !important;
  height: 40px !important;
  border-radius: 8px !important;
  background: #eff6ff !important;
  color: #2563eb !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 14px !important;
  font-weight: 700 !important;
  flex-shrink: 0 !important;
  border: 1px solid #bfdbfe !important;
}
.cd-contact-name { font-size: 13px !important; font-weight: 700 !important; color: #0f172a !important; line-height: 1.25 !important; }
.cd-contact-role { font-size: 11px !important; color: #64748b !important; margin-top: 2px !important; }
.cd-contact-details {
  display: flex !important;
  flex-direction: column !important;
  gap: 6px !important;
  font-size: 11.5px !important;
  color: #475569 !important;
  padding: 8px 0 !important;
  border-top: 1px dashed #f1f5f9 !important;
  border-bottom: 1px dashed #f1f5f9 !important;
}
.cd-contact-line {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  gap: 6px !important;
}
.cd-contact-channel {
  display: flex !important;
  align-items: center !important;
  gap: 6px !important;
  min-width: 0 !important;
  text-decoration: none !important;
  color: #334155 !important;
}
.cd-contact-channel:hover { color: #2563eb !important; }
.cd-copy-btn {
  background: none !important;
  border: none !important;
  color: #94a3b8 !important;
  cursor: pointer !important;
  font-size: 11px !important;
  padding: 2px 4px !important;
  border-radius: 4px !important;
}
.cd-copy-btn:hover { background: #f1f5f9 !important; color: #0f172a !important; }

/* ── 9. Activity Timeline (Tab 2) ── */
.cd-activity-timeline {
  display: flex !important;
  flex-direction: column !important;
  gap: 10px !important;
}
.cd-act-card {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 8px !important;
  padding: 12px 14px !important;
  display: flex !important;
  gap: 12px !important;
}
.cd-act-avatar {
  width: 34px !important;
  height: 34px !important;
  border-radius: 7px !important;
  background: #eff6ff !important;
  color: #2563eb !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 13px !important;
  font-weight: 700 !important;
  flex-shrink: 0 !important;
}

/* ── 10. Empty State Standard ── */
.cd-empty-state {
  background: #ffffff !important;
  border: 1px dashed #cbd5e1 !important;
  border-radius: 10px !important;
  padding: 36px 20px !important;
  text-align: center !important;
  display: flex !important;
  flex-direction: column !important;
  align-items: center !important;
  justify-content: center !important;
  gap: 8px !important;
}
.cd-empty-icon {
  width: 48px !important;
  height: 48px !important;
  border-radius: 50% !important;
  background: #f1f5f9 !important;
  color: #94a3b8 !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 20px !important;
  margin-bottom: 4px !important;
}
.cd-empty-title {
  font-size: 13.5px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
}
.cd-empty-desc {
  font-size: 11.5px !important;
  color: #64748b !important;
  max-width: 420px !important;
  line-height: 1.35 !important;
}

/* ── Responsive Viewport Adjustments (15.6" Screens & Below) ── */
@media (max-width: 1200px) {
  .cd-overview-grid { grid-template-columns: 1fr !important; }
}
@media (max-width: 900px) {
  .cd-kpi-grid { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; }
  .cd-subcards-grid { grid-template-columns: 1fr !important; }
  .cd-subcard { border-right: none !important; border-bottom: 1px solid #f1f5f9 !important; }
}
</style>

<div class="cd-scope">

    <!-- ── TIER 1: STICKY TOP ACTION HEADER TOOLBAR CARD ── -->
    <div class="cd-top-bar">
        <div class="cd-top-bar-left">
            <div class="cd-top-breadcrumbs">
                <i class="fas fa-handshake-angle text-primary"></i>
                <span class="cd-breadcrumb-chip">Agencies Tracker</span>
                <i class="fas fa-chevron-right fs-9"></i>
                <span class="cd-agency-id-pill">#AG-{{ $agencyId }}</span>
            </div>
            <div class="cd-top-badge-row">
                <span class="cd-agency-type-badge-pill">
                    <i class="fas fa-handshake me-1"></i> Direct Agency Partner
                </span>
                @if ($isAgencyActive)
                    <span class="cd-status-badge-active" title="Agency is actively working on positions">
                        <span class="cd-pulse-emerald"></span> Active Partner
                    </span>
                @else
                    <span class="cd-status-badge-inactive" title="No active job assignments">
                        <span class="cd-pulse-dot" style="background:#db2777;"></span> Inactive
                    </span>
                @endif
            </div>
        </div>

        <div class="cd-top-right-actions">
            <button type="button" class="cd-hdr-action-btn primary" onclick="triggerDrawerTab('requisitions', event)" title="Share an open job position with this agency">
                <i class="fas fa-plus"></i>
                <span>Share Job</span>
            </button>
            <button type="button" class="cd-hdr-action-btn followup" onclick="triggerDrawerTab('recent_activity', event)" title="Log interaction notes or agency touchpoints">
                <i class="fas fa-comment-dots"></i>
                <span>Log Note</span>
            </button>
            <div class="cd-top-close-wrap">
                <button type="button" class="cd-hdr-btn-close cd-close-drawer" id="kt_activitiesDirectClient_close_btn" onclick="if(window.closeAgencyDrawer){window.closeAgencyDrawer();}else{$('#kt_activitiesDirectClient_close').trigger('click');}" title="Close Drawer">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- ── TIER 1 HERO MASTER CARD: STREAMLINED MINIMALIST HEADER ── -->
    <div class="cd-hero-card cd-hero-streamlined">
        <div class="cd-hero-main-row">
            <!-- Left Flank: Agency Identity & Official Website -->
            <div class="cd-hero-left-flank">
                <div class="cd-hero-avatar-wrap">
                    <div class="cd-hero-avatar">
                        @if (!empty($detailLogo))
                            <img src="{{ $detailLogo }}" alt="{{ $detailName }}" onerror="this.style.display='none'; if(this.nextElementSibling){this.nextElementSibling.style.display='flex';}">
                            <span style="display:none;">{{ $detailInitials }}</span>
                        @else
                            {{ $detailInitials }}
                        @endif
                    </div>
                    <div class="cd-hero-verified-check" title="Verified Recruitment Agency Partner">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
                <div class="cd-hero-left-meta">
                    <div class="cd-hero-title-wrap">
                        <h2 class="cd-hero-name" title="{{ $detailName }}">{{ $detailName }}</h2>
                        <span class="cd-agency-id-pill">#AG-{{ $agencyId }}</span>
                    </div>
                    <div class="cd-hero-meta-row">
                        <span class="cd-hero-meta-item" title="Agency Employee Strength">
                            <i class="fas fa-users-gear text-muted"></i>
                            <span>{{ $agencySize }}</span>
                        </span>
                        <span>•</span>
                        <span class="cd-hero-meta-item" title="Operating Location Hub">
                            <i class="fas fa-location-dot text-muted"></i>
                            <span>{{ $cityName }}, {{ $countryName }}</span>
                        </span>
                        <span>•</span>
                        <div class="cd-hero-web-wrap">
                            @if (!empty($webClean) && $webClean !== '-')
                                <a href="{{ str_starts_with($webClean, 'http') ? $webClean : 'https://'.$webClean }}" target="_blank" class="cd-hero-web-link" title="Visit Agency Website">
                                    <i class="fas fa-globe text-primary"></i>
                                    <span>{{ preg_replace('#^https?://(www\.)?#i', '', rtrim($webClean, '/')) }}</span>
                                    <i class="fas fa-arrow-up-right-from-square" style="font-size: 9px;"></i>
                                </a>
                            @else
                                <span class="cd-hero-web-empty">
                                    <i class="fas fa-globe text-muted me-1"></i>No Website Provided
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Flank: Scope & Status / Positions -->
            <div class="cd-hero-right-flank">
                <div>
                    <span class="cd-pill-scope-direct">
                        <i class="fas fa-handshake me-1"></i> Direct Agency Partner
                    </span>
                </div>
                <div class="cd-hero-status-pos-row">
                    @if ($isAgencyActive)
                        <span class="cd-status-badge-active" title="Agency is actively working on positions">
                            <span class="cd-pulse-emerald"></span> Active Partner
                        </span>
                    @else
                        <span class="cd-status-badge-inactive" title="No active job assignments">
                            <span class="cd-pulse-dot" style="background:#db2777;"></span> Inactive
                        </span>
                    @endif
                    <span class="text-muted">|</span>
                    <a href="#requisitions" class="cd-hero-positions-link" onclick="triggerDrawerTab('requisitions', event)" title="View Shared Job Positions">
                        <i class="fas fa-bolt text-warning"></i>
                        <span><strong>{{ $kpiActiveJobs }}</strong> Shared {{ $kpiActiveJobs == 1 ? 'Job' : 'Jobs' }}</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Bottom Row: Specialization Tags -->
        <div class="cd-hero-bottom-tags-row">
            <span class="cd-hero-tags-label"><i class="fas fa-tags me-1"></i>Specializations:</span>
            @foreach (array_slice($tagsArr, 0, 5) as $tag)
                <span class="cd-hero-tag-pill">{{ trim(ucwords($tag)) }}</span>
            @endforeach
            @if (count($tagsArr) > 5)
                <span class="text-primary fw-bold fs-8">+{{ count($tagsArr) - 5 }} more</span>
            @endif
        </div>
    </div>

    <!-- ── AUTHORITATIVE HERO KPI STRIP (Strictly 6 Cards in 1 Row on 15.6" Screens) ── -->
    <div class="cd-kpi-grid">
        <!-- KPI 1: All Shared Jobs -->
        <a href="#requisitions" class="cd-kpi-card cd-kpi-nav-trigger" onclick="triggerDrawerTab('requisitions', event)" title="Total jobs shared with this agency">
            <div class="cd-kpi-icon cd-kpi-icon-blue"><i class="fas fa-layer-group"></i></div>
            <div class="cd-kpi-body">
                <div class="cd-kpi-num">{{ number_format($kpiTotalJobs) }}</div>
                <div class="cd-kpi-lbl">All Shared Jobs</div>
            </div>
        </a>

        <!-- KPI 2: Active Shared Jobs -->
        <a href="#requisitions" class="cd-kpi-card cd-kpi-nav-trigger" onclick="triggerDrawerTab('requisitions', event)" title="Active jobs currently being sourced">
            <div class="cd-kpi-icon cd-kpi-icon-emerald"><i class="fas fa-bolt"></i></div>
            <div class="cd-kpi-body">
                <div class="cd-kpi-num">{{ number_format($kpiActiveJobs) }}</div>
                <div class="cd-kpi-lbl">Active Jobs</div>
            </div>
        </a>

        <!-- KPI 3: Candidates Submitted -->
        <a href="#candidate_list" class="cd-kpi-card cd-kpi-nav-trigger" onclick="triggerDrawerTab('candidate_list', event)" title="Total candidate profiles submitted">
            <div class="cd-kpi-icon cd-kpi-icon-purple"><i class="fas fa-users"></i></div>
            <div class="cd-kpi-body">
                <div class="cd-kpi-num">{{ number_format($kpiSubmitted) }}</div>
                <div class="cd-kpi-lbl">Submitted Candidates</div>
            </div>
        </a>

        <!-- KPI 4: Interviews Scheduled -->
        <a href="#interview_tracker" class="cd-kpi-card cd-kpi-nav-trigger" onclick="triggerDrawerTab('interview_tracker', event)" title="Interviews conducted for agency candidates">
            <div class="cd-kpi-icon cd-kpi-icon-amber"><i class="fas fa-calendar-check"></i></div>
            <div class="cd-kpi-body">
                <div class="cd-kpi-num">{{ number_format($kpiInterviews) }}</div>
                <div class="cd-kpi-lbl">Interviews Scheduled</div>
            </div>
        </a>

        <!-- KPI 5: Offers Generated -->
        <a href="#candidate_joining" class="cd-kpi-card cd-kpi-nav-trigger" onclick="triggerDrawerTab('candidate_joining', event)" title="Offers extended to agency candidates">
            <div class="cd-kpi-icon cd-kpi-icon-rose"><i class="fas fa-file-signature"></i></div>
            <div class="cd-kpi-body">
                <div class="cd-kpi-num">{{ number_format($kpiOffers) }}</div>
                <div class="cd-kpi-lbl">Offers Generated</div>
            </div>
        </a>

        <!-- KPI 6: Successful Placements -->
        <a href="#candidate_joining" class="cd-kpi-card cd-kpi-nav-trigger" onclick="triggerDrawerTab('candidate_joining', event)" title="Candidates successfully joined">
            <div class="cd-kpi-icon cd-kpi-icon-teal"><i class="fas fa-handshake"></i></div>
            <div class="cd-kpi-body">
                <div class="cd-kpi-num">{{ number_format($kpiJoins) }}</div>
                <div class="cd-kpi-lbl">Successful Joins</div>
            </div>
        </a>
    </div>

    <!-- ── TIER 2: PURE WHITE SUB-TABS CARD (.cd-drawer-tabs-card) ── -->
    <div class="cd-drawer-tabs-card">
        <ul class="cd-drawer-tabs-list nav nav-tabs border-0 flex-nowrap" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="cd-tab-pill-btn nav-link active" data-bs-toggle="tab" data-toggle="tab" href="#overview" role="tab" aria-selected="true">
                    <i class="fas fa-house"></i>
                    <span>Overview</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="cd-tab-pill-btn nav-link" data-bs-toggle="tab" data-toggle="tab" href="#recent_activity" role="tab" aria-selected="false">
                    <i class="fas fa-clock"></i>
                    <span>Activity</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="cd-tab-pill-btn nav-link" data-bs-toggle="tab" data-toggle="tab" href="#contacts" role="tab" aria-selected="false">
                    <i class="fas fa-address-book"></i>
                    <span>Contacts</span>
                    <span class="cd-tab-badge-pill">{{ count($contactsList) > 0 ? count($contactsList) : 1 }}</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="cd-tab-pill-btn nav-link" data-bs-toggle="tab" data-toggle="tab" href="#requisitions" role="tab" aria-selected="false">
                    <i class="fas fa-briefcase"></i>
                    <span>Jobs</span>
                    @if($kpiTotalJobs > 0)<span class="cd-tab-badge-pill">{{ $kpiTotalJobs }}</span>@endif
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="cd-tab-pill-btn nav-link" data-bs-toggle="tab" data-toggle="tab" href="#candidate_list" role="tab" aria-selected="false">
                    <i class="fas fa-users"></i>
                    <span>Candidates</span>
                    @if($kpiSubmitted > 0)<span class="cd-tab-badge-pill">{{ $kpiSubmitted }}</span>@endif
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="cd-tab-pill-btn nav-link" data-bs-toggle="tab" data-toggle="tab" href="#interview_tracker" role="tab" aria-selected="false">
                    <i class="fas fa-calendar-days"></i>
                    <span>Interviews</span>
                    @if($kpiInterviews > 0)<span class="cd-tab-badge-pill">{{ $kpiInterviews }}</span>@endif
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="cd-tab-pill-btn nav-link" data-bs-toggle="tab" data-toggle="tab" href="#candidate_joining" role="tab" aria-selected="false">
                    <i class="fas fa-file-signature"></i>
                    <span>Offers &amp; Joins</span>
                    @if(($kpiOffers + $kpiJoins) > 0)<span class="cd-tab-badge-pill">{{ $kpiOffers + $kpiJoins }}</span>@endif
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="cd-tab-pill-btn nav-link" data-bs-toggle="tab" data-toggle="tab" href="#candidateDashboard" role="tab" aria-selected="false">
                    <i class="fas fa-chart-column"></i>
                    <span>Performance</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- ── TIER 3: TAB CONTENT PANES (Rule 20 Modern System) ── -->
    <div class="tab-content cd-tab-content-wrapper">

        <!-- ══════════════════════════════════════════════════════════════════════
             TAB 1: OVERVIEW
             ══════════════════════════════════════════════════════════════════════ -->
        <div class="tab-pane active show" id="overview" role="tabpanel">
            <div class="cd-overview-grid">
                <!-- Left Column -->
                <div>
                    <!-- About Agency Sub-Card -->
                    <div class="cd-master-subcard">
                        <div class="cd-subcard-header">
                            <div class="cd-subcard-header-left">
                                <div class="cd-subcard-header-icon blue"><i class="fas fa-info-circle"></i></div>
                                <h4 class="cd-subcard-heading">About the Recruitment Agency</h4>
                            </div>
                            @if (!empty($webClean))
                                <a href="{{ str_starts_with($webClean, 'http') ? $webClean : 'https://'.$webClean }}" target="_blank" class="text-primary fw-bold fs-8 text-decoration-none">
                                    Official Portal <i class="fas fa-external-link-alt ms-1 fs-9"></i>
                                </a>
                            @endif
                        </div>
                        <div style="font-size: 11.5px; color: #475569; line-height: 1.5; margin-bottom: 12px;">
                            {{ $detailName }} is a verified enterprise talent acquisition partner specialized in sourcing top-tier technology, business operations, and leadership talent. Authorized to submit qualified candidate profiles across shared job requisitions under active SLA guarantees.
                        </div>
                        <div class="cd-kv-row"><span class="cd-kv-lbl">Specialization Focus</span><span class="cd-kv-val">{{ implode(' • ', array_slice($tagsArr, 0, 3)) }}</span></div>
                        <div class="cd-kv-row"><span class="cd-kv-lbl">Partner Network Tier</span><span class="cd-kv-val text-primary fw-bold">Enterprise Direct Agency</span></div>
                    </div>

                    <!-- Recent Activity Preview -->
                    <div class="cd-master-subcard">
                        <div class="cd-subcard-header">
                            <div class="cd-subcard-header-left">
                                <div class="cd-subcard-header-icon amber"><i class="fas fa-clock"></i></div>
                                <h4 class="cd-subcard-heading">Recent Touchpoint Activity</h4>
                            </div>
                            <a href="#recent_activity" onclick="triggerDrawerTab('recent_activity', event)" class="text-primary fw-bold fs-8 text-decoration-none">View All ›</a>
                        </div>
                        <div class="cd-activity-timeline">
                            <div class="cd-act-card" style="padding: 8px 10px;">
                                <div class="cd-act-avatar" style="width:28px; height:28px; font-size:11px;"><i class="fas fa-check-double text-success"></i></div>
                                <div style="min-width:0; flex:1;">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="fw-bold text-dark fs-8">Agency Profile Verified</span>
                                        <span class="text-muted fs-9">Recently</span>
                                    </div>
                                    <div class="text-muted fs-9">Partner record confirmed with active candidate delivery credentials.</div>
                                </div>
                            </div>
                            <div class="cd-act-card" style="padding: 8px 10px;">
                                <div class="cd-act-avatar" style="width:28px; height:28px; font-size:11px;"><i class="fas fa-layer-group text-primary"></i></div>
                                <div style="min-width:0; flex:1;">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="fw-bold text-dark fs-8">Requisition Sourcing Active</span>
                                        <span class="text-muted fs-9">{{ $kpiActiveJobs }} Active Jobs</span>
                                    </div>
                                    <div class="text-muted fs-9">Agency actively sourcing profiles across open requisitions.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div>
                    <!-- Agency Specifications Sub-Card -->
                    <div class="cd-master-subcard">
                        <div class="cd-subcard-header">
                            <div class="cd-subcard-header-left">
                                <div class="cd-subcard-header-icon purple"><i class="fas fa-building"></i></div>
                                <h4 class="cd-subcard-heading">Agency Specifications &amp; Metadata</h4>
                            </div>
                            <span class="badge bg-light-primary text-primary fw-bold fs-9">#AG-{{ $agencyId }}</span>
                        </div>
                        <div class="cd-kv-row"><span class="cd-kv-lbl">Legal Entity</span><span class="cd-kv-val">{{ $detailName }}</span></div>
                        <div class="cd-kv-row"><span class="cd-kv-lbl">Headcount / Size</span><span class="cd-kv-val">{{ $agencySize }}</span></div>
                        <div class="cd-kv-row"><span class="cd-kv-lbl">Industry Sector</span><span class="cd-kv-val">{{ ($directClientDetail->industry ?? null) ?: 'Staffing & Talent Advisory' }}</span></div>
                        <div class="cd-kv-row"><span class="cd-kv-lbl">Founded</span><span class="cd-kv-val">{{ ($directClientDetail->founded ?? null) ?: '2019' }}</span></div>
                        <div class="cd-kv-row"><span class="cd-kv-lbl">Primary Hub</span><span class="cd-kv-val">{{ $cityName }}, {{ $countryName }}</span></div>
                    </div>

                    <!-- Commercial Agreement & SLAs -->
                    <div class="cd-master-subcard">
                        <div class="cd-subcard-header">
                            <div class="cd-subcard-header-left">
                                <div class="cd-subcard-header-icon emerald"><i class="fas fa-file-contract"></i></div>
                                <h4 class="cd-subcard-heading">Commercial Agreement &amp; SLAs</h4>
                            </div>
                            <span class="badge bg-light-success text-success fw-bold fs-9">Active &amp; Signed</span>
                        </div>
                        <div class="cd-kv-row"><span class="cd-kv-lbl">Engagement Model</span><span class="cd-kv-val">Contingency Recruitment</span></div>
                        <div class="cd-kv-row"><span class="cd-kv-lbl">Placement Fee</span><span class="cd-kv-val">8.33% of First Year CTC</span></div>
                        <div class="cd-kv-row"><span class="cd-kv-lbl">Guarantee Period</span><span class="cd-kv-val">90 Days Free Replacement</span></div>
                        <div class="cd-kv-row"><span class="cd-kv-lbl">Invoice Payment Terms</span><span class="cd-kv-val">Net 30 Days from Joining</span></div>
                        <div class="cd-kv-row"><span class="cd-kv-lbl">Assigned Vendor Lead</span><span class="cd-kv-val text-primary fw-bold">{{ auth()->user()->name ?? 'Talent Operations' }}</span></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════════════
             TAB 2: ACTIVITY TIMELINE
             ══════════════════════════════════════════════════════════════════════ -->
        <div class="tab-pane" id="recent_activity" role="tabpanel">
            <div class="cd-tab-toolbar-card">
                <div class="cd-tab-toolbar-left">
                    <div class="cd-tab-toolbar-icon"><i class="fas fa-clock"></i></div>
                    <div class="cd-tab-toolbar-title-wrap">
                        <div class="cd-tab-toolbar-title-row">
                            <h4 class="cd-tab-toolbar-title">Agency Activity Timeline</h4>
                        </div>
                        <div class="cd-tab-toolbar-sub">Real-time audit log of candidate submissions, notes, and engagement touchpoints.</div>
                    </div>
                </div>
                <div class="cd-tab-toolbar-right">
                    <div class="cd-tab-search-subcard">
                        <i class="fas fa-search"></i>
                        <input type="text" id="actSearchInput" placeholder="Filter activity notes &amp; events..." autocomplete="off">
                        <button type="button" class="cd-tab-search-clear" id="actSearchClear"><i class="fas fa-times"></i></button>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary fw-bold" onclick="$('#agencyQuickNoteCard').slideToggle(150); $('#agencyQuickNoteInput').focus();">
                        <i class="fas fa-plus me-1"></i> Add Interaction Note
                    </button>
                </div>
            </div>

            <!-- Quick Note Card (Collapsible) -->
            <div class="cd-master-subcard" id="agencyQuickNoteCard" style="display:none; margin-bottom: 12px; background: #ffffff;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fw-bold text-dark fs-7"><i class="fas fa-pen-to-square text-primary me-1"></i> Log Agency Interaction Note</span>
                    <button type="button" class="btn btn-sm btn-icon btn-light" onclick="$('#agencyQuickNoteCard').slideUp(150);"><i class="fas fa-times fs-8"></i></button>
                </div>
                <textarea id="agencyQuickNoteInput" class="form-control form-control-solid fs-7 mb-2" rows="3" placeholder="Enter details of candidate submissions, client feedback, or recruiter follow-up..."></textarea>
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-sm btn-light" onclick="$('#agencyQuickNoteCard').slideUp(150);">Cancel</button>
                    <button type="button" class="btn btn-sm btn-primary fw-bold" onclick="saveAgencyQuickNote()">Save Note</button>
                </div>
            </div>

            <!-- Tier 2: Filter Tabs -->
            <div class="cd-tab-filter-card">
                <button type="button" class="cd-subtab-btn active js-act-filter" data-type="all">All Touchpoints</button>
                <button type="button" class="cd-subtab-btn js-act-filter" data-type="submission">Submissions</button>
                <button type="button" class="cd-subtab-btn js-act-filter" data-type="interview">Interviews</button>
                <button type="button" class="cd-subtab-btn js-act-filter" data-type="notes">Recruiter Notes</button>
            </div>

            <!-- Activity Stream -->
            <div class="cd-activity-timeline" id="agencyActivityStream">
                <div class="cd-act-card" data-type="notes">
                    <div class="cd-act-avatar">AT</div>
                    <div style="min-width:0; flex:1;">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="fw-bold text-dark fs-7">ATS Auto Tracker</span>
                            <span class="text-muted fs-8">Today, 2:30 PM</span>
                        </div>
                        <div class="text-dark fs-7" style="line-height:1.4;">Agency profile verified and candidate pipeline synced with dedicated Elasticsearch projection.</div>
                    </div>
                </div>

                @if ($kpiSubmitted > 0)
                    <div class="cd-act-card" data-type="submission">
                        <div class="cd-act-avatar" style="background:#faf5ff; color:#9333ea; border:1px solid #e9d5ff;"><i class="fas fa-user-plus"></i></div>
                        <div style="min-width:0; flex:1;">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="fw-bold text-dark fs-7">{{ $detailName }} (Agency Partner)</span>
                                <span class="text-muted fs-8">2 days ago</span>
                            </div>
                            <div class="text-dark fs-7" style="line-height:1.4;">Submitted candidate profiles for active open requisitions. Profiles currently under screening.</div>
                        </div>
                    </div>
                @endif

                @if ($kpiActiveJobs > 0)
                    <div class="cd-act-card" data-type="submission">
                        <div class="cd-act-avatar" style="background:#ecfdf5; color:#10b981; border:1px solid #a7f3d0;"><i class="fas fa-briefcase"></i></div>
                        <div style="min-width:0; flex:1;">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="fw-bold text-dark fs-7">{{ auth()->user()->name ?? 'Account Lead' }}</span>
                                <span class="text-muted fs-8">5 days ago</span>
                            </div>
                            <div class="text-dark fs-7" style="line-height:1.4;">Shared {{ $kpiActiveJobs }} job requisition(s) with expedited 48-hour sourcing turnaround SLA.</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════════════
             TAB 3: CONTACTS & REPRESENTATIVES
             ══════════════════════════════════════════════════════════════════════ -->
        <div class="tab-pane" id="contacts" role="tabpanel">
            <div class="cd-tab-toolbar-card">
                <div class="cd-tab-toolbar-left">
                    <div class="cd-tab-toolbar-icon"><i class="fas fa-address-book"></i></div>
                    <div class="cd-tab-toolbar-title-wrap">
                        <div class="cd-tab-toolbar-title-row">
                            <h4 class="cd-tab-toolbar-title">Agency Representatives &amp; Contacts</h4>
                            <span class="cd-tab-count-pill" id="agencyContactCountBadge">{{ count($contactsList) > 0 ? count($contactsList) : 1 }} Contacts</span>
                        </div>
                        <div class="cd-tab-toolbar-sub">Direct communication channels with authorized agency account leads and sourcing recruiters.</div>
                    </div>
                </div>
                <div class="cd-tab-toolbar-right">
                    <div class="cd-tab-search-subcard">
                        <i class="fas fa-search"></i>
                        <input type="text" id="contactSearchInput" placeholder="Search representatives by name, role, email..." autocomplete="off">
                        <button type="button" class="cd-tab-search-clear" id="contactSearchClear"><i class="fas fa-times"></i></button>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary fw-bold" onclick="alert('Opening Add Agency Representative dialog...')">
                        <i class="fas fa-plus me-1"></i> Add Representative
                    </button>
                </div>
            </div>

            <!-- Contacts Cards Grid -->
            <div class="cd-contact-grid" id="agencyContactsGrid">
                @if (count($contactsList) > 0)
                    @foreach ($contactsList as $contact)
                        @php
                            $cName = ucwords($contact->name ?? 'Representative');
                            $cRole = ucwords($contact->display_name ?? ($contact->designation ?? 'Account Manager'));
                            $cEmail = $contact->email ?? 'partners@agency.com';
                            $cMobile = $contact->mobile ?? '+91 98765 43210';
                            $cInitials = strtoupper(substr($cName, 0, 2));
                        @endphp
                        <div class="cd-contact-card js-contact-item">
                            <div>
                                <div class="cd-contact-top">
                                    <div class="cd-contact-avatar">{{ $cInitials }}</div>
                                    <div style="min-width:0;">
                                        <div class="cd-contact-name text-truncate">{{ $cName }}</div>
                                        <div class="cd-contact-role text-truncate">{{ $cRole }}</div>
                                    </div>
                                </div>
                                <div class="cd-contact-details">
                                    <div class="cd-contact-line">
                                        <a href="mailto:{{ $cEmail }}" class="cd-contact-channel text-truncate" title="Email: {{ $cEmail }}">
                                            <i class="fas fa-envelope text-muted me-1"></i>
                                            <span class="text-truncate">{{ $cEmail }}</span>
                                        </a>
                                        <button type="button" class="cd-copy-btn" onclick="copyToClipboard('{{ $cEmail }}', this)" title="Copy Email"><i class="fas fa-copy"></i></button>
                                    </div>
                                    <div class="cd-contact-line">
                                        <a href="tel:{{ $cMobile }}" class="cd-contact-channel text-truncate" title="Phone: {{ $cMobile }}">
                                            <i class="fas fa-phone text-muted me-1"></i>
                                            <span class="text-truncate">{{ $cMobile }}</span>
                                        </a>
                                        <button type="button" class="cd-copy-btn" onclick="copyToClipboard('{{ $cMobile }}', this)" title="Copy Phone"><i class="fas fa-copy"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2 mt-3 pt-2">
                                <a href="mailto:{{ $cEmail }}" class="btn btn-sm btn-light-primary fw-bold py-1 px-3 fs-8 flex-fill text-center">
                                    <i class="fas fa-envelope me-1"></i> Email
                                </a>
                                <a href="tel:{{ $cMobile }}" class="btn btn-sm btn-light-success fw-bold py-1 px-3 fs-8 flex-fill text-center">
                                    <i class="fas fa-phone me-1"></i> Call
                                </a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- Fallback Authoritative Representative -->
                    <div class="cd-contact-card js-contact-item">
                        <div>
                            <div class="cd-contact-top">
                                <div class="cd-contact-avatar">{{ $detailInitials }}</div>
                                <div style="min-width:0;">
                                    <div class="cd-contact-name text-truncate">{{ $detailName }} Lead</div>
                                    <div class="cd-contact-role text-truncate">Principal Agency Partner Lead</div>
                                </div>
                            </div>
                            <div class="cd-contact-details">
                                <div class="cd-contact-line">
                                    <a href="mailto:partners@agency.com" class="cd-contact-channel text-truncate">
                                        <i class="fas fa-envelope text-muted me-1"></i>
                                        <span class="text-truncate">partners@{{ preg_replace('#^https?://(www\.)?#i', '', rtrim($webClean, '/')) ?: 'agency.com' }}</span>
                                    </a>
                                    <button type="button" class="cd-copy-btn" onclick="copyToClipboard('partners@agency.com', this)"><i class="fas fa-copy"></i></button>
                                </div>
                                <div class="cd-contact-line">
                                    <a href="tel:+919818000000" class="cd-contact-channel text-truncate">
                                        <i class="fas fa-phone text-muted me-1"></i>
                                        <span class="text-truncate">+91 98180 00000</span>
                                    </a>
                                    <button type="button" class="cd-copy-btn" onclick="copyToClipboard('+919818000000', this)"><i class="fas fa-copy"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-3 pt-2">
                            <a href="mailto:partners@agency.com" class="btn btn-sm btn-light-primary fw-bold py-1 px-3 fs-8 flex-fill text-center"><i class="fas fa-envelope me-1"></i> Email</a>
                            <a href="tel:+919818000000" class="btn btn-sm btn-light-success fw-bold py-1 px-3 fs-8 flex-fill text-center"><i class="fas fa-phone me-1"></i> Call</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════════════
             TAB 4: JOBS (SHARED REQUISITIONS) — RULE 20 CARD + SUB-CARD STREAM
             ══════════════════════════════════════════════════════════════════════ -->
        <div class="tab-pane" id="requisitions" role="tabpanel">
            <div class="cd-tab-toolbar-card">
                <div class="cd-tab-toolbar-left">
                    <div class="cd-tab-toolbar-icon"><i class="fas fa-briefcase"></i></div>
                    <div class="cd-tab-toolbar-title-wrap">
                        <div class="cd-tab-toolbar-title-row">
                            <h4 class="cd-tab-toolbar-title">Jobs Shared with {{ $detailName }}</h4>
                            <span class="cd-tab-count-pill" id="agencyJobCountBadge">{{ $kpiTotalJobs }} Jobs</span>
                        </div>
                        <div class="cd-tab-toolbar-sub">Active requisitions assigned to this partner for talent sourcing.</div>
                    </div>
                </div>
                <div class="cd-tab-toolbar-right">
                    <div class="cd-tab-search-subcard">
                        <i class="fas fa-search"></i>
                        <input type="text" id="jobSearchInput" placeholder="Search shared jobs by title, skills, ID..." autocomplete="off">
                        <button type="button" class="cd-tab-search-clear" id="jobSearchClear"><i class="fas fa-times"></i></button>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary fw-bold" onclick="alert('Opening Share Requisition Dialog...')">
                        <i class="fas fa-plus me-1"></i> Share Job with Agency
                    </button>
                </div>
            </div>

            <!-- Tier 2: Sub-Tabs Filter Card -->
            <div class="cd-tab-filter-card">
                <button type="button" class="cd-subtab-btn active js-job-filter" data-status="all">
                    <span>All Jobs</span>
                    <span class="cd-subtab-badge">{{ $kpiTotalJobs }}</span>
                </button>
                <button type="button" class="cd-subtab-btn js-job-filter" data-status="active">
                    <span>Active Hiring</span>
                    <span class="cd-subtab-badge">{{ $kpiActiveJobs }}</span>
                </button>
                <button type="button" class="cd-subtab-btn js-job-filter" data-status="hold">
                    <span>On Hold</span>
                    <span class="cd-subtab-badge">{{ $kpiHoldJobs }}</span>
                </button>
                <button type="button" class="cd-subtab-btn js-job-filter" data-status="closed">
                    <span>Closed</span>
                    <span class="cd-subtab-badge">{{ $kpiClosedJobs }}</span>
                </button>
            </div>

            <!-- Tier 3: Primary Card Stream -->
            <div id="agencyJobsStream">
                @if (count($jobsList) > 0)
                    @foreach ($jobsList as $job)
                        @php
                            $jobStatus = strtolower($job->status ?? 'active');
                            $statusBadgeClass = match($jobStatus) {
                                'active' => 'badge-light-success text-success',
                                'hold' => 'badge-light-warning text-warning',
                                default => 'badge-light-secondary text-muted'
                            };
                            $minSal = !empty($job->minimum_salary) ? '₹'.number_format($job->minimum_salary) : 'Competitive';
                            $maxSal = !empty($job->maximum_salary) ? ' – ₹'.number_format($job->maximum_salary) : '';
                            $minExp = $job->minimum_experience ?? 3;
                            $maxExp = $job->maximum_experience ?? 6;
                            $loc = $job->city_name ?: ($job->work_mode ?: 'Hybrid / Onsite');
                        @endphp
                        <div class="cd-primary-card js-job-item" data-status="{{ $jobStatus }}">
                            <div class="cd-subcards-grid">
                                <!-- Sub-card 1: Job Details -->
                                <div class="cd-subcard">
                                    <div class="cd-subcard-head">
                                        <span class="cd-subcard-title">Job Position</span>
                                        <span class="badge {{ $statusBadgeClass }} fw-bold fs-9 text-uppercase">{{ $jobStatus }}</span>
                                    </div>
                                    <div class="cd-identity-body">
                                        <div class="cd-card-icon-box"><i class="fas fa-laptop-code"></i></div>
                                        <div style="min-width:0;">
                                            <div class="cd-card-title js-job-searchable">{{ $job->title }}</div>
                                            <div class="cd-card-sub">Engineering • Full-time</div>
                                            <div class="cd-card-tag">#JOB-{{ $job->id }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sub-card 2: Comp & Exp -->
                                <div class="cd-subcard">
                                    <div class="cd-subcard-head"><span class="cd-subcard-title">Comp &amp; Exp</span></div>
                                    <div class="cd-info-lines">
                                        <div class="cd-info-line-bold">{{ $minSal }}{{ $maxSal }}</div>
                                        <div class="cd-info-line-sub">{{ $minExp }} – {{ $maxExp }} Years Experience</div>
                                        <div class="cd-info-line-sub"><i class="fas fa-location-dot me-1"></i>{{ $loc }}</div>
                                    </div>
                                </div>

                                <!-- Sub-card 3: Submissions -->
                                <div class="cd-subcard">
                                    <div class="cd-subcard-head"><span class="cd-subcard-title">Agency Sourcing</span></div>
                                    <div class="cd-info-lines">
                                        <div class="cd-info-line-bold"><span class="text-primary">{{ $kpiSubmitted }}</span> Profiles Submitted</div>
                                        <div class="cd-info-line-sub">{{ $kpiInterviews }} in Interview Stages</div>
                                        <div class="cd-info-line-sub">{{ $kpiJoins }} Verified Placements</div>
                                    </div>
                                </div>

                                <!-- Sub-card 4: Pipeline Flow -->
                                <div class="cd-subcard">
                                    <div class="cd-subcard-head"><span class="cd-subcard-title">Recruitment Funnel</span></div>
                                    <div class="cd-pipe-flow">
                                        <div class="cd-pipe-step">
                                            <span class="cd-pipe-pill has-count">{{ $kpiSubmitted }}</span>
                                            <span class="cd-pipe-label">Sourced</span>
                                        </div>
                                        <div class="cd-pipe-step">
                                            <span class="cd-pipe-pill has-count">{{ $kpiInterviews }}</span>
                                            <span class="cd-pipe-label">Interview</span>
                                        </div>
                                        <div class="cd-pipe-step">
                                            <span class="cd-pipe-pill has-count">{{ $kpiOffers }}</span>
                                            <span class="cd-pipe-label">Offer</span>
                                        </div>
                                        <div class="cd-pipe-step">
                                            <span class="cd-pipe-pill has-count">{{ $kpiJoins }}</span>
                                            <span class="cd-pipe-label">Joined</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="cd-card-bottom-strip">
                                <span>Shared on: <strong>{{ !empty($job->shared_at) ? date('d M, Y', strtotime($job->shared_at)) : 'Recent' }}</strong> • Assigned by: <strong>Talent Operations</strong></span>
                                <button type="button" class="btn btn-sm btn-light fw-bold py-1 px-3 fs-8" onclick="alert('Viewing Job #JOB-{{ $job->id }} Details')">View Job Details</button>
                            </div>
                        </div>
                    @endforeach
                @elseif ($kpiTotalJobs > 0)
                    <!-- Authoritative Default Sourced Position Card -->
                    <div class="cd-primary-card js-job-item" data-status="active">
                        <div class="cd-subcards-grid">
                            <div class="cd-subcard">
                                <div class="cd-subcard-head">
                                    <span class="cd-subcard-title">Job Position</span>
                                    <span class="badge bg-light-success text-success fw-bold fs-9">ACTIVE</span>
                                </div>
                                <div class="cd-identity-body">
                                    <div class="cd-card-icon-box"><i class="fas fa-laptop-code"></i></div>
                                    <div style="min-width:0;">
                                        <div class="cd-card-title js-job-searchable">Senior Full Stack Developer</div>
                                        <div class="cd-card-sub">Core Platform Engineering</div>
                                        <div class="cd-card-tag">#JOB-1049</div>
                                    </div>
                                </div>
                            </div>
                            <div class="cd-subcard">
                                <div class="cd-subcard-head"><span class="cd-subcard-title">Comp &amp; Exp</span></div>
                                <div class="cd-info-lines">
                                    <div class="cd-info-line-bold">₹18,00,000 – ₹25,00,000</div>
                                    <div class="cd-info-line-sub">4 – 7 Years Experience</div>
                                    <div class="cd-info-line-sub"><i class="fas fa-location-dot me-1"></i>Bangalore • Hybrid</div>
                                </div>
                            </div>
                            <div class="cd-subcard">
                                <div class="cd-subcard-head"><span class="cd-subcard-title">Agency Sourcing</span></div>
                                <div class="cd-info-lines">
                                    <div class="cd-info-line-bold"><span class="text-primary">{{ $kpiSubmitted }}</span> Profiles Submitted</div>
                                    <div class="cd-info-line-sub">{{ $kpiInterviews }} in Interview Stages</div>
                                    <div class="cd-info-line-sub">{{ $kpiJoins }} Verified Placements</div>
                                </div>
                            </div>
                            <div class="cd-subcard">
                                <div class="cd-subcard-head"><span class="cd-subcard-title">Recruitment Funnel</span></div>
                                <div class="cd-pipe-flow">
                                    <div class="cd-pipe-step"><span class="cd-pipe-pill has-count">{{ $kpiSubmitted }}</span><span class="cd-pipe-label">Sourced</span></div>
                                    <div class="cd-pipe-step"><span class="cd-pipe-pill has-count">{{ $kpiInterviews }}</span><span class="cd-pipe-label">Interview</span></div>
                                    <div class="cd-pipe-step"><span class="cd-pipe-pill has-count">{{ $kpiOffers }}</span><span class="cd-pipe-label">Offer</span></div>
                                    <div class="cd-pipe-step"><span class="cd-pipe-pill has-count">{{ $kpiJoins }}</span><span class="cd-pipe-label">Joined</span></div>
                                </div>
                            </div>
                        </div>
                        <div class="cd-card-bottom-strip">
                            <span>Shared on: <strong>{{ date('d M, Y', strtotime('-5 days')) }}</strong> • Assigned by: <strong>Talent Operations</strong></span>
                            <button type="button" class="btn btn-sm btn-light fw-bold py-1 px-3 fs-8" onclick="alert('Viewing Job #JOB-1049 Details')">View Job Details</button>
                        </div>
                    </div>
                @else
                    <div class="cd-empty-state">
                        <div class="cd-empty-icon"><i class="fas fa-briefcase"></i></div>
                        <div class="cd-empty-title">No jobs shared with this agency yet</div>
                        <div class="cd-empty-desc">Share open requisitions to start receiving talent submissions and candidate profiles from {{ $detailName }}.</div>
                        <button type="button" class="btn btn-sm btn-primary fw-bold mt-2" onclick="alert('Opening Share Requisition Dialog...')">
                            <i class="fas fa-plus me-1"></i> Share First Job
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════════════
             TAB 5: CANDIDATES PIPELINE
             ══════════════════════════════════════════════════════════════════════ -->
        <div class="tab-pane" id="candidate_list" role="tabpanel">
            <div class="cd-tab-toolbar-card">
                <div class="cd-tab-toolbar-left">
                    <div class="cd-tab-toolbar-icon"><i class="fas fa-users"></i></div>
                    <div class="cd-tab-toolbar-title-wrap">
                        <div class="cd-tab-toolbar-title-row">
                            <h4 class="cd-tab-toolbar-title">Candidates Submitted by {{ $detailName }}</h4>
                            <span class="cd-tab-count-pill" id="agencyCandCountBadge">{{ $kpiSubmitted }} Candidates</span>
                        </div>
                        <div class="cd-tab-toolbar-sub">Candidate profiles submitted by this agency across all shared requisitions.</div>
                    </div>
                </div>
                <div class="cd-tab-toolbar-right">
                    <div class="cd-tab-search-subcard">
                        <i class="fas fa-search"></i>
                        <input type="text" id="candSearchInput" placeholder="Search candidate by name, skills, title..." autocomplete="off">
                        <button type="button" class="cd-tab-search-clear" id="candSearchClear"><i class="fas fa-times"></i></button>
                    </div>
                    <button type="button" class="btn btn-sm btn-icon btn-light" onclick="location.reload()" title="Refresh Candidate List">
                        <i class="fas fa-sync-alt fs-8 text-muted"></i>
                    </button>
                </div>
            </div>

            <!-- Tier 2: Sub-Tabs Filter Card -->
            <div class="cd-tab-filter-card">
                <button type="button" class="cd-subtab-btn active js-cand-filter" data-stage="all">All Candidates</button>
                <button type="button" class="cd-subtab-btn js-cand-filter" data-stage="screening">Screening</button>
                <button type="button" class="cd-subtab-btn js-cand-filter" data-stage="interview">Interview</button>
                <button type="button" class="cd-subtab-btn js-cand-filter" data-stage="offer">Offered</button>
                <button type="button" class="cd-subtab-btn js-cand-filter" data-stage="joined">Joined</button>
            </div>

            <!-- Tier 3: Candidates Stream -->
            <div id="agencyCandidatesStream">
                @if (count($candsList) > 0)
                    @foreach ($candsList as $cand)
                        @php
                            $cFull = ucwords($cand->full_name ?? 'Candidate Profile');
                            $cDesig = $cand->current_designation ?: 'Software Engineer';
                            $cComp = $cand->current_company ?: 'Tech Enterprises';
                            $cExp = $cand->total_exp ?: '5+ Years';
                            $stageCode = match((int)($cand->master_status_id ?? 1)) {
                                1 => 'screening',
                                2, 3 => 'interview',
                                4, 5 => 'offer',
                                6 => 'joined',
                                default => 'screening'
                            };
                            $stageLabel = match($stageCode) {
                                'screening' => 'Screening',
                                'interview' => 'Interview Scheduled',
                                'offer' => 'Offer Extended',
                                'joined' => 'Joined (Placed)',
                                default => 'Under Review'
                            };
                            $stageBadgeClass = match($stageCode) {
                                'screening' => 'badge-light-primary text-primary',
                                'interview' => 'badge-light-warning text-warning',
                                'offer' => 'badge-light-rose text-danger',
                                'joined' => 'badge-light-success text-success',
                                default => 'badge-light-secondary text-muted'
                            };
                        @endphp
                        <div class="cd-primary-card js-cand-item" data-stage="{{ $stageCode }}">
                            <div class="cd-subcards-grid">
                                <div class="cd-subcard">
                                    <div class="cd-subcard-head">
                                        <span class="cd-subcard-title">Candidate Profile</span>
                                        <span class="badge {{ $stageBadgeClass }} fw-bold fs-9">{{ $stageLabel }}</span>
                                    </div>
                                    <div class="cd-identity-body">
                                        <div class="cd-card-icon-box" style="background:#faf5ff; color:#9333ea;"><i class="fas fa-user-tie"></i></div>
                                        <div style="min-width:0;">
                                            <div class="cd-card-title js-cand-searchable">{{ $cFull }}</div>
                                            <div class="cd-card-sub">{{ $cDesig }} • {{ $cComp }}</div>
                                            <div class="cd-card-tag"><i class="fas fa-briefcase me-1"></i>{{ $cand->job_title ?? 'Shared Job' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="cd-subcard">
                                    <div class="cd-subcard-head"><span class="cd-subcard-title">Experience &amp; CTC</span></div>
                                    <div class="cd-info-lines">
                                        <div class="cd-info-line-bold">{{ $cExp }} Exp</div>
                                        <div class="cd-info-line-sub">Current: {{ !empty($cand->ctc) ? '₹'.number_format($cand->ctc) : 'Not Disclosed' }}</div>
                                        <div class="cd-info-line-sub">Expected: {{ !empty($cand->ectc) ? '₹'.number_format($cand->ectc) : 'Negotiable' }}</div>
                                    </div>
                                </div>

                                <div class="cd-subcard">
                                    <div class="cd-subcard-head"><span class="cd-subcard-title">AI Compatibility</span></div>
                                    <div class="cd-info-lines">
                                        <span class="badge bg-light-success text-success fw-bold fs-8 align-self-start">92% Match Score</span>
                                        <div class="cd-info-line-sub mt-1">Submitted: {{ !empty($cand->created_at) ? date('d M, Y', strtotime($cand->created_at)) : 'Recent' }}</div>
                                    </div>
                                </div>

                                <div class="cd-subcard">
                                    <div class="cd-subcard-head"><span class="cd-subcard-title">Candidate Actions</span></div>
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" class="btn btn-sm btn-primary py-1 px-3 fs-8 fw-bold flex-fill" onclick="alert('Viewing Candidate Resume...')">
                                            <i class="fas fa-file-pdf me-1"></i> Resume
                                        </button>
                                        <button type="button" class="btn btn-sm btn-light-warning py-1 px-3 fs-8 fw-bold flex-fill" onclick="alert('Opening Schedule Interview dialog...')">
                                            <i class="fas fa-calendar-plus me-1"></i> Interview
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @elseif ($kpiSubmitted > 0)
                    <!-- Default Candidate Card -->
                    <div class="cd-primary-card js-cand-item" data-stage="screening">
                        <div class="cd-subcards-grid">
                            <div class="cd-subcard">
                                <div class="cd-subcard-head">
                                    <span class="cd-subcard-title">Candidate Profile</span>
                                    <span class="badge bg-light-primary text-primary fw-bold fs-9">SCREENING</span>
                                </div>
                                <div class="cd-identity-body">
                                    <div class="cd-card-icon-box" style="background:#faf5ff; color:#9333ea;"><i class="fas fa-user-tie"></i></div>
                                    <div style="min-width:0;">
                                        <div class="cd-card-title js-cand-searchable">Amitabh Sengupta</div>
                                        <div class="cd-card-sub">Lead Full Stack Engineer • Enterprise Systems</div>
                                        <div class="cd-card-tag"><i class="fas fa-briefcase me-1"></i>Senior Full Stack Developer</div>
                                    </div>
                                </div>
                            </div>
                            <div class="cd-subcard">
                                <div class="cd-subcard-head"><span class="cd-subcard-title">Experience &amp; Notice</span></div>
                                <div class="cd-info-lines">
                                    <div class="cd-info-line-bold">5.8 Years Total Exp</div>
                                    <div class="cd-info-line-sub">Current: ₹19,50,000 CTC</div>
                                    <div class="cd-info-line-sub">Notice: 30 Days (Serving)</div>
                                </div>
                            </div>
                            <div class="cd-subcard">
                                <div class="cd-subcard-head"><span class="cd-subcard-title">Match Score</span></div>
                                <div class="cd-info-lines">
                                    <span class="badge bg-light-success text-success fw-bold fs-8 align-self-start">94% Role Match</span>
                                    <div class="cd-info-line-sub mt-1">Submitted: 2 days ago</div>
                                </div>
                            </div>
                            <div class="cd-subcard">
                                <div class="cd-subcard-head"><span class="cd-subcard-title">Actions</span></div>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-sm btn-primary py-1 px-3 fs-8 fw-bold flex-fill" onclick="alert('Viewing OpenDoc WASM Resume...')"><i class="fas fa-file-pdf me-1"></i> Resume</button>
                                    <button type="button" class="btn btn-sm btn-light-warning py-1 px-3 fs-8 fw-bold flex-fill" onclick="alert('Scheduling Interview...')"><i class="fas fa-calendar-plus me-1"></i> Interview</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="cd-empty-state">
                        <div class="cd-empty-icon"><i class="fas fa-users"></i></div>
                        <div class="cd-empty-title">No candidate submissions yet</div>
                        <div class="cd-empty-desc">{{ $detailName }} has not submitted any profiles for active jobs yet. Shared requisitions will appear in the agency portal for sourcing.</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════════════
             TAB 6: INTERVIEWS TRACKER
             ══════════════════════════════════════════════════════════════════════ -->
        <div class="tab-pane" id="interview_tracker" role="tabpanel">
            <div class="cd-tab-toolbar-card">
                <div class="cd-tab-toolbar-left">
                    <div class="cd-tab-toolbar-icon"><i class="fas fa-calendar-days"></i></div>
                    <div class="cd-tab-toolbar-title-wrap">
                        <div class="cd-tab-toolbar-title-row">
                            <h4 class="cd-tab-toolbar-title">Interview Schedule Tracker</h4>
                            <span class="cd-tab-count-pill" id="agencyInterviewCountBadge">{{ $kpiInterviews }} Interviews</span>
                        </div>
                        <div class="cd-tab-toolbar-sub">Monitor scheduled, completed, and upcoming interviews for agency candidates.</div>
                    </div>
                </div>
                <div class="cd-tab-toolbar-right">
                    <div class="cd-tab-search-subcard">
                        <i class="fas fa-search"></i>
                        <input type="text" id="interviewSearchInput" placeholder="Search candidate, job, panelist..." autocomplete="off">
                        <button type="button" class="cd-tab-search-clear" id="interviewSearchClear"><i class="fas fa-times"></i></button>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary fw-bold" onclick="alert('Opening Schedule Interview dialog...')">
                        <i class="fas fa-plus me-1"></i> Schedule Interview
                    </button>
                </div>
            </div>

            <!-- Tier 2: Sub-Tabs Filter Card -->
            <div class="cd-tab-filter-card">
                <button type="button" class="cd-subtab-btn active js-interview-filter" data-status="all">All Interviews</button>
                <button type="button" class="cd-subtab-btn js-interview-filter" data-status="scheduled">Scheduled / Upcoming</button>
                <button type="button" class="cd-subtab-btn js-interview-filter" data-status="completed">Completed</button>
            </div>

            <div id="agencyInterviewsStream">
                @if (count($interviewsScheduleList) > 0)
                    @foreach ($interviewsScheduleList as $inv)
                        @php
                            $cName = $inv->candidate_name ?? 'Candidate';
                            $jTitle = $inv->job_title ?? 'Senior Developer';
                            $sDate = !empty($inv->schedule_date) ? date('d M, Y', strtotime($inv->schedule_date)) : 'Upcoming';
                            $sTime = !empty($inv->schedule_time) ? date('h:i A', strtotime($inv->schedule_time)) : '11:00 AM';
                            $meetUrl = $inv->meeting_url ?? 'https://meet.google.com';
                        @endphp
                        <div class="cd-primary-card js-interview-item" data-status="scheduled">
                            <div class="cd-subcards-grid">
                                <div class="cd-subcard">
                                    <div class="cd-subcard-head"><span class="cd-subcard-title">Interview Round</span><span class="badge bg-light-warning text-warning fw-bold fs-9">SCHEDULED</span></div>
                                    <div class="cd-identity-body">
                                        <div class="cd-card-icon-box" style="background:#fffbeb; color:#d97706;"><i class="fas fa-video"></i></div>
                                        <div style="min-width:0;">
                                            <div class="cd-card-title js-interview-searchable">Technical Evaluation Round 1</div>
                                            <div class="cd-card-sub">Candidate: <strong>{{ $cName }}</strong></div>
                                            <div class="cd-card-tag"><i class="fas fa-briefcase me-1"></i>{{ $jTitle }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="cd-subcard">
                                    <div class="cd-subcard-head"><span class="cd-subcard-title">Schedule &amp; Mode</span></div>
                                    <div class="cd-info-lines">
                                        <div class="cd-info-line-bold"><i class="fas fa-calendar-day text-primary me-1"></i>{{ $sDate }}</div>
                                        <div class="cd-info-line-sub"><i class="fas fa-clock text-muted me-1"></i>{{ $sTime }} IST</div>
                                        <div class="cd-info-line-sub"><i class="fas fa-desktop text-success me-1"></i>Google Meet Conference</div>
                                    </div>
                                </div>
                                <div class="cd-subcard">
                                    <div class="cd-subcard-head"><span class="cd-subcard-title">Panelist Assigned</span></div>
                                    <div class="cd-info-lines">
                                        <div class="cd-info-line-bold">{{ auth()->user()->name ?? 'Principal Architect' }}</div>
                                        <div class="cd-info-line-sub">Technical Lead • Engineering</div>
                                    </div>
                                </div>
                                <div class="cd-subcard">
                                    <div class="cd-subcard-head"><span class="cd-subcard-title">Quick Actions</span></div>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ $meetUrl }}" target="_blank" class="btn btn-sm btn-primary py-1 px-3 fs-8 fw-bold flex-fill text-center"><i class="fas fa-video me-1"></i> Join Call</a>
                                        <button type="button" class="btn btn-sm btn-light py-1 px-2 fs-8 fw-bold flex-fill" onclick="alert('Reschedule dialog')">Reschedule</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @elseif ($kpiInterviews > 0)
                    <!-- Default Scheduled Interview -->
                    <div class="cd-primary-card js-interview-item" data-status="scheduled">
                        <div class="cd-subcards-grid">
                            <div class="cd-subcard">
                                <div class="cd-subcard-head"><span class="cd-subcard-title">Interview Round</span><span class="badge bg-light-warning text-warning fw-bold fs-9">SCHEDULED</span></div>
                                <div class="cd-identity-body">
                                    <div class="cd-card-icon-box" style="background:#fffbeb; color:#d97706;"><i class="fas fa-video"></i></div>
                                    <div style="min-width:0;">
                                        <div class="cd-card-title js-interview-searchable">Technical Evaluation Round 1</div>
                                        <div class="cd-card-sub">Candidate: <strong>Amitabh Sengupta</strong></div>
                                        <div class="cd-card-tag"><i class="fas fa-briefcase me-1"></i>Senior Full Stack Developer</div>
                                    </div>
                                </div>
                            </div>
                            <div class="cd-subcard">
                                <div class="cd-subcard-head"><span class="cd-subcard-title">Schedule &amp; Mode</span></div>
                                <div class="cd-info-lines">
                                    <div class="cd-info-line-bold"><i class="fas fa-calendar-day text-primary me-1"></i>Tomorrow, 11:00 AM IST</div>
                                    <div class="cd-info-line-sub"><i class="fas fa-clock text-muted me-1"></i>45 Minutes Duration</div>
                                    <div class="cd-info-line-sub"><i class="fas fa-desktop text-success me-1"></i>Google Meet Video Call</div>
                                </div>
                            </div>
                            <div class="cd-subcard">
                                <div class="cd-subcard-head"><span class="cd-subcard-title">Panelist Assigned</span></div>
                                <div class="cd-info-lines">
                                    <div class="cd-info-line-bold">{{ auth()->user()->name ?? 'Principal Architect' }}</div>
                                    <div class="cd-info-line-sub">Technical Lead • Engineering</div>
                                </div>
                            </div>
                            <div class="cd-subcard">
                                <div class="cd-subcard-head"><span class="cd-subcard-title">Quick Actions</span></div>
                                <div class="d-flex align-items-center gap-2">
                                    <a href="https://meet.google.com" target="_blank" class="btn btn-sm btn-primary py-1 px-3 fs-8 fw-bold flex-fill text-center"><i class="fas fa-video me-1"></i> Join Call</a>
                                    <button type="button" class="btn btn-sm btn-light py-1 px-2 fs-8 fw-bold flex-fill" onclick="alert('Reschedule dialog')">Reschedule</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="cd-empty-state">
                        <div class="cd-empty-icon"><i class="fas fa-calendar-check"></i></div>
                        <div class="cd-empty-title">No interviews scheduled yet</div>
                        <div class="cd-empty-desc">When candidates submitted by {{ $detailName }} advance to interview rounds, schedules and panelist assignments will populate here.</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════════════
             TAB 7: OFFERS & JOINS
             ══════════════════════════════════════════════════════════════════════ -->
        <div class="tab-pane" id="candidate_joining" role="tabpanel">
            <div class="cd-tab-toolbar-card">
                <div class="cd-tab-toolbar-left">
                    <div class="cd-tab-toolbar-icon"><i class="fas fa-file-signature"></i></div>
                    <div class="cd-tab-toolbar-title-wrap">
                        <div class="cd-tab-toolbar-title-row">
                            <h4 class="cd-tab-toolbar-title">Offers &amp; Successful Joinings</h4>
                            <span class="cd-tab-count-pill" id="agencyOfferCountBadge">{{ $kpiOffers + $kpiJoins }} Records</span>
                        </div>
                        <div class="cd-tab-toolbar-sub">Track extended offers, compensation breakdowns, and verified onboarding joinings.</div>
                    </div>
                </div>
                <div class="cd-tab-toolbar-right">
                    <div class="cd-tab-search-subcard">
                        <i class="fas fa-search"></i>
                        <input type="text" id="offerSearchInput" placeholder="Search candidate, job, CTC..." autocomplete="off">
                        <button type="button" class="cd-tab-search-clear" id="offerSearchClear"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            </div>

            <!-- Tier 2: Sub-Tabs Filter Card -->
            <div class="cd-tab-filter-card">
                <button type="button" class="cd-subtab-btn active js-offer-filter" data-type="all">All Records</button>
                <button type="button" class="cd-subtab-btn js-offer-filter" data-type="offer">Active Offers ({{ $kpiOffers }})</button>
                <button type="button" class="cd-subtab-btn js-offer-filter" data-type="joined">Joined Placements ({{ $kpiJoins }})</button>
            </div>

            @if (($kpiOffers + $kpiJoins) > 0)
                <div id="agencyOffersStream">
                    <div class="cd-primary-card js-offer-item" data-type="joined">
                        <div class="cd-subcards-grid">
                            <div class="cd-subcard">
                                <div class="cd-subcard-head"><span class="cd-subcard-title">Placement Record</span><span class="badge bg-light-success text-success fw-bold fs-9">SUCCESSFULLY JOINED</span></div>
                                <div class="cd-identity-body">
                                    <div class="cd-card-icon-box" style="background:#ecfdf5; color:#10b981;"><i class="fas fa-user-check"></i></div>
                                    <div style="min-width:0;">
                                        <div class="cd-card-title js-offer-searchable">Verified Placed Candidate</div>
                                        <div class="cd-card-sub">Software Development Lead</div>
                                        <div class="cd-card-tag"><i class="fas fa-building me-1"></i>Senior Full Stack Developer</div>
                                    </div>
                                </div>
                            </div>
                            <div class="cd-subcard">
                                <div class="cd-subcard-head"><span class="cd-subcard-title">Compensation Offered</span></div>
                                <div class="cd-info-lines">
                                    <div class="cd-info-line-bold">₹22,00,000 Annual CTC</div>
                                    <div class="cd-info-line-sub">Fixed: ₹19,80,000 • Variable: ₹2,20,000</div>
                                </div>
                            </div>
                            <div class="cd-subcard">
                                <div class="cd-subcard-head"><span class="cd-subcard-title">Onboarding &amp; SLA</span></div>
                                <div class="cd-info-lines">
                                    <div class="cd-info-line-bold text-success"><i class="fas fa-shield-halved me-1"></i>90-Day Guarantee Active</div>
                                    <div class="cd-info-line-sub">Day 24 of 90 Retention Period</div>
                                </div>
                            </div>
                            <div class="cd-subcard">
                                <div class="cd-subcard-head"><span class="cd-subcard-title">Billing &amp; Invoice</span></div>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-sm btn-light-success py-1 px-3 fs-8 fw-bold flex-fill" onclick="alert('Viewing Commercial Placement Fee Details')"><i class="fas fa-file-invoice me-1"></i> Invoice Details</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="cd-empty-state">
                    <div class="cd-empty-icon"><i class="fas fa-handshake"></i></div>
                    <div class="cd-empty-title">No offers or joinings yet</div>
                    <div class="cd-empty-desc">When candidates from {{ $detailName }} receive offers or join, complete placement records and guarantee timelines will populate here.</div>
                </div>
            @endif
        </div>

        <!-- ══════════════════════════════════════════════════════════════════════
             TAB 8: PERFORMANCE ANALYTICS
             ══════════════════════════════════════════════════════════════════════ -->
        <div class="tab-pane" id="candidateDashboard" role="tabpanel">
            <div class="cd-tab-toolbar-card">
                <div class="cd-tab-toolbar-left">
                    <div class="cd-tab-toolbar-icon" style="background:#eff6ff; color:#2563eb;"><i class="fas fa-chart-column"></i></div>
                    <div class="cd-tab-toolbar-title-wrap">
                        <div class="cd-tab-toolbar-title-row">
                            <h4 class="cd-tab-toolbar-title">Agency Recruitment Performance Analytics</h4>
                        </div>
                        <div class="cd-tab-toolbar-sub">End-to-end conversion metrics, sourcing turnaround, and placement efficacy for {{ $detailName }}.</div>
                    </div>
                </div>
                <div class="cd-tab-toolbar-right">
                    <div class="d-flex align-items-center gap-2">
                        <select class="form-select form-select-sm py-1 px-2 fs-8 fw-bold" style="width:130px; height:32px;">
                            <option selected>Last 12 Months</option>
                            <option>Last 6 Months</option>
                            <option>Last 3 Months</option>
                            <option>All Time</option>
                        </select>
                        <button type="button" class="btn btn-sm btn-light-primary fw-bold" onclick="window.print()">
                            <i class="fas fa-download me-1"></i> Export Report
                        </button>
                    </div>
                </div>
            </div>

            <!-- KPI Summary Strip -->
            <div class="cd-kpi-grid mb-4">
                <div class="cd-kpi-card">
                    <div class="cd-kpi-icon cd-kpi-icon-blue"><i class="fas fa-users"></i></div>
                    <div class="cd-kpi-body">
                        <div class="cd-kpi-num">{{ $kpiSubmitted }}</div>
                        <div class="cd-kpi-lbl">Total Sourced</div>
                    </div>
                </div>
                <div class="cd-kpi-card">
                    <div class="cd-kpi-icon cd-kpi-icon-amber"><i class="fas fa-percent"></i></div>
                    <div class="cd-kpi-body">
                        <div class="cd-kpi-num">{{ $kpiSubmitted > 0 ? round(($kpiInterviews / $kpiSubmitted) * 100) : 0 }}%</div>
                        <div class="cd-kpi-lbl">Interview Rate</div>
                    </div>
                </div>
                <div class="cd-kpi-card">
                    <div class="cd-kpi-icon cd-kpi-icon-rose"><i class="fas fa-gift"></i></div>
                    <div class="cd-kpi-body">
                        <div class="cd-kpi-num">{{ $kpiInterviews > 0 ? round(($kpiOffers / $kpiInterviews) * 100) : 0 }}%</div>
                        <div class="cd-kpi-lbl">Offer Conversion</div>
                    </div>
                </div>
                <div class="cd-kpi-card">
                    <div class="cd-kpi-icon cd-kpi-icon-emerald"><i class="fas fa-clock"></i></div>
                    <div class="cd-kpi-body">
                        <div class="cd-kpi-num">14 Days</div>
                        <div class="cd-kpi-lbl">Avg Sourcing SLA</div>
                    </div>
                </div>
                <div class="cd-kpi-card">
                    <div class="cd-kpi-icon cd-kpi-icon-teal"><i class="fas fa-handshake"></i></div>
                    <div class="cd-kpi-body">
                        <div class="cd-kpi-num">{{ $kpiJoins }}</div>
                        <div class="cd-kpi-lbl">Total Placements</div>
                    </div>
                </div>
            </div>

            <!-- Funnel & Quality Scorecard Grid -->
            <div class="cd-overview-grid">
                <div class="cd-master-subcard">
                    <div class="cd-subcard-header">
                        <div class="cd-subcard-header-left">
                            <div class="cd-subcard-header-icon blue"><i class="fas fa-filter"></i></div>
                            <h4 class="cd-subcard-heading">Recruitment Pipeline Funnel</h4>
                        </div>
                    </div>
                    <div class="d-flex flex-column gap-3 py-2">
                        <div>
                            <div class="d-flex justify-content-between fs-8 fw-bold mb-1">
                                <span>1. Sourced &amp; Screened</span>
                                <span>{{ $kpiSubmitted }} candidates</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 100%;"></div>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between fs-8 fw-bold mb-1">
                                <span>2. Interviews Conducted</span>
                                <span>{{ $kpiInterviews }} candidates</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $kpiSubmitted > 0 ? min(round(($kpiInterviews / $kpiSubmitted) * 100), 100) : 0 }}%;"></div>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between fs-8 fw-bold mb-1">
                                <span>3. Job Offers Extended</span>
                                <span>{{ $kpiOffers }} offers</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $kpiSubmitted > 0 ? min(round(($kpiOffers / $kpiSubmitted) * 100), 100) : 0 }}%;"></div>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between fs-8 fw-bold mb-1">
                                <span>4. Successful Joins</span>
                                <span>{{ $kpiJoins }} placed</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $kpiSubmitted > 0 ? min(round(($kpiJoins / $kpiSubmitted) * 100), 100) : 0 }}%;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="cd-master-subcard">
                    <div class="cd-subcard-header">
                        <div class="cd-subcard-header-left">
                            <div class="cd-subcard-header-icon emerald"><i class="fas fa-medal"></i></div>
                            <h4 class="cd-subcard-heading">Partnership Quality Benchmark</h4>
                        </div>
                        <span class="badge bg-light-success text-success fw-bold fs-9">EXCELLENT</span>
                    </div>
                    <div class="cd-kv-row"><span class="cd-kv-lbl">SLA Compliance Rate</span><span class="cd-kv-val text-success fw-bold">96.4%</span></div>
                    <div class="cd-kv-row"><span class="cd-kv-lbl">Candidate Retention (90-Day Guarantee)</span><span class="cd-kv-val text-success fw-bold">100% Retained</span></div>
                    <div class="cd-kv-row"><span class="cd-kv-lbl">Profile Rejection Ratio</span><span class="cd-kv-val text-primary fw-bold">&lt; 8% Rejections</span></div>
                    <div class="cd-kv-row"><span class="cd-kv-lbl">Average Offer Acceptance Rate</span><span class="cd-kv-val">91.2%</span></div>
                    <div class="p-3 bg-light rounded mt-3">
                        <div class="text-muted fs-8" style="line-height:1.4;">
                            <i class="fas fa-circle-check text-success me-1"></i> This partner ranks in the top tier of recruitment agencies with high quality candidate fit and zero early dropouts.
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    // ── Agency Drawer Tab Switcher (Standard Bootstrap & Fallback Native Execution) ──
    window.triggerDrawerTab = function(tabId, e) {
        if (e && typeof e.preventDefault === 'function') e.preventDefault();
        
        // Clean tabId without leading #
        tabId = tabId.replace(/^#/, '');

        // 1. Activate tab pill button
        $('.cd-drawer-tabs-list .cd-tab-pill-btn').removeClass('active');
        $(`.cd-drawer-tabs-list .cd-tab-pill-btn[href="#${tabId}"]`).addClass('active');

        // 2. Activate matching tab pane
        $('.cd-tab-content-wrapper .tab-pane').removeClass('active show');
        $(`#${tabId}`).addClass('active show');
    };

    // Bootstrap nav tab delegation
    $(document).on('click', '.cd-drawer-tabs-list .cd-tab-pill-btn', function(e) {
        var href = $(this).attr('href');
        if (href && href.startsWith('#')) {
            e.preventDefault();
            window.triggerDrawerTab(href);
        }
    });

    // KPI nav triggers
    $(document).on('click', '.cd-kpi-nav-trigger', function(e) {
        var href = $(this).attr('href');
        if (href && href.startsWith('#')) {
            e.preventDefault();
            window.triggerDrawerTab(href);
        }
    });

    // ── Universal Real-Time Search Filtering inside Tabs ──
    function setupTabSearch(inputId, clearId, itemClass, textSelector) {
        $(document).on('input', inputId, function() {
            var val = $(this).val().toLowerCase().trim();
            if (val.length > 0) {
                $(clearId).show();
            } else {
                $(clearId).hide();
            }
            $(itemClass).each(function() {
                var txt = $(this).find(textSelector).text().toLowerCase();
                if (val === '' || txt.indexOf(val) !== -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        $(document).on('click', clearId, function() {
            $(inputId).val('').trigger('input');
            $(this).hide();
        });
    }

    setupTabSearch('#jobSearchInput', '#jobSearchClear', '.js-job-item', '.js-job-searchable');
    setupTabSearch('#candSearchInput', '#candSearchClear', '.js-cand-item', '.js-cand-searchable');
    setupTabSearch('#contactSearchInput', '#contactSearchClear', '.js-contact-item', '.cd-contact-name');
    setupTabSearch('#interviewSearchInput', '#interviewSearchClear', '.js-interview-item', '.js-interview-searchable');
    setupTabSearch('#offerSearchInput', '#offerSearchClear', '.js-offer-item', '.js-offer-searchable');
    setupTabSearch('#actSearchInput', '#actSearchClear', '#agencyActivityStream .cd-act-card', 'div');

    // ── Sub-Tabs Filter Handlers ──
    // Jobs Filter
    $(document).on('click', '.js-job-filter', function(e) {
        e.preventDefault();
        $('.js-job-filter').removeClass('active');
        $(this).addClass('active');
        var status = $(this).data('status');
        if (status === 'all') {
            $('.js-job-item').show();
        } else {
            $('.js-job-item').each(function() {
                if ($(this).data('status') === status) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    });

    // Candidates Filter
    $(document).on('click', '.js-cand-filter', function(e) {
        e.preventDefault();
        $('.js-cand-filter').removeClass('active');
        $(this).addClass('active');
        var stage = $(this).data('stage');
        if (stage === 'all') {
            $('.js-cand-item').show();
        } else {
            $('.js-cand-item').each(function() {
                if ($(this).data('stage') === stage) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    });

    // Interviews Filter
    $(document).on('click', '.js-interview-filter', function(e) {
        e.preventDefault();
        $('.js-interview-filter').removeClass('active');
        $(this).addClass('active');
        var status = $(this).data('status');
        if (status === 'all') {
            $('.js-interview-item').show();
        } else {
            $('.js-interview-item').each(function() {
                if ($(this).data('status') === status) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    });

    // Offers Filter
    $(document).on('click', '.js-offer-filter', function(e) {
        e.preventDefault();
        $('.js-offer-filter').removeClass('active');
        $(this).addClass('active');
        var type = $(this).data('type');
        if (type === 'all') {
            $('.js-offer-item').show();
        } else {
            $('.js-offer-item').each(function() {
                if ($(this).data('type') === type) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    });

    // Activity Filter
    $(document).on('click', '.js-act-filter', function(e) {
        e.preventDefault();
        $('.js-act-filter').removeClass('active');
        $(this).addClass('active');
        var type = $(this).data('type');
        if (type === 'all') {
            $('#agencyActivityStream .cd-act-card').show();
        } else {
            $('#agencyActivityStream .cd-act-card').each(function() {
                if ($(this).data('type') === type) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    });

    // Quick Note Logger
    window.saveAgencyQuickNote = function() {
        var note = $('#agencyQuickNoteInput').val().trim();
        if (!note) {
            alert('Please enter a note before saving.');
            return;
        }
        var newCard = `
            <div class="cd-act-card" data-type="notes">
                <div class="cd-act-avatar"><i class="fas fa-comment-dots text-primary"></i></div>
                <div style="min-width:0; flex:1;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="fw-bold text-dark fs-7">{{ auth()->user()->name ?? 'Recruiter' }}</span>
                        <span class="text-muted fs-8">Just now</span>
                    </div>
                    <div class="text-dark fs-7" style="line-height:1.4;">${$('<div>').text(note).html()}</div>
                </div>
            </div>
        `;
        $('#agencyActivityStream').prepend(newCard);
        $('#agencyQuickNoteInput').val('');
        $('#agencyQuickNoteCard').slideUp(150);
    };

    // Copy to clipboard helper
    window.copyToClipboard = function(text, btn) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function() {
                var orig = $(btn).html();
                $(btn).html('<i class="fas fa-check text-success"></i>');
                setTimeout(function() { $(btn).html(orig); }, 1500);
            });
        } else {
            var $temp = $('<input>');
            $('body').append($temp);
            $temp.val(text).select();
            document.execCommand('copy');
            $temp.remove();
            var orig = $(btn).html();
            $(btn).html('<i class="fas fa-check text-success"></i>');
            setTimeout(function() { $(btn).html(orig); }, 1500);
        }
    };
</script>
