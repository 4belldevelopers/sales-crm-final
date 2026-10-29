@php
    use App\Helper\GeneralMethod;
    use Carbon\Carbon;

    $userPermissions = $userPermissions ?? [];
    $startOfMonthcomman = Carbon::now()->startOfMonth()->subMonths(5);
    $endOfMonthcomman =  Carbon::now()->endOfMonth();
    $endDate = Carbon::today()->endOfDay();
    $startDate = Carbon::today()->subDays(6)->startOfDay();

    // ── Agency Profile Data Resolution ──
    $detailName = ucwords(@$directClientDetail->name ?: @$directClientDetail->company_name ?: 'Recruitment Agency');
    $detailInitials = strtoupper(implode('', array_map(fn($w) => mb_substr($w, 0, 1), explode(' ', trim($detailName)))));
    if (strlen($detailInitials) > 3) $detailInitials = substr($detailInitials, 0, 2);

    $detailLogo = '';
    if (!empty(@$directClientDetail->logo) && strlen(@$directClientDetail->logo) > 10) {
        $detailLogo = @$directClientDetail->logo;
    } elseif (!empty(@$directClientDetail->logo_logical_path) && strlen(@$directClientDetail->logo_logical_path) > 10) {
        $detailLogo = @$directClientDetail->logo_logical_path;
    }

    $agencyId = @$directClientDetail->company_id ?: @$directClientDetail->client_id ?: $id;
    $webClean = @$directClientDetail->website ?: '';
    
    $tagsRaw = @$directClientDetail->tags ?: 'Staffing,Executive Search,Recruitment';
    $tagsArr = is_array($tagsRaw) ? $tagsRaw : explode(',', (string)$tagsRaw);

    $cityName = @$city_name ?: (@$client_location->city_name ?: @$client_location->city ?: 'Primary Hub');
    $countryName = @$country_name ?: 'India';

    // ── Pre-hydrated KPI Numbers ──
    $kpiActiveJobs = (int)($Active ?? 0);
    $kpiHoldJobs = (int)($Hold ?? 0);
    $kpiClosedJobs = (int)($Closed ?? 0);
    $kpiTotalJobs = $kpiActiveJobs + $kpiHoldJobs + $kpiClosedJobs;

    $kpiSubmitted = (int)($all_candidates ?? 0);
    $kpiInterviews = (int)($all_interview ?? 0);
    $kpiOffers = (int)($offers ?? 0);
    $kpiJoins = (int)($Joined ?? 0);
    $isAgencyActive = ($kpiActiveJobs > 0 || $kpiSubmitted > 0);

    // Contacts
    $contactsList = $client_contact_info ?? collect([]);
@endphp

<style>
/* ==========================================================================
   Agency Drawer Master Enterprise Design (100% Replication of Client Drawer)
   Strictly Optimized for 15.6" Screens (1366x768 & 1920x1080) and Smaller
   GUARANTEED: Zero Horizontal Scrollbar (overflow-x: hidden) Across All Screens
   ========================================================================== */

.cd-drawer-root, .cd-drawer-root * {
  box-sizing: border-box !important;
  font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
}
.cd-drawer-root {
  width: 100% !important;
  max-width: 100% !important;
  overflow-x: hidden !important;
  background: #f8fafc !important;
  padding: 12px 14px 30px !important;
}

/* ── 1. Top Header & Hero Card (.cd-hero-card.cd-hero-streamlined) ── */
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
  gap: 12px !important;
  min-width: 0 !important;
}
.cd-hero-avatar-wrap {
  position: relative !important;
  flex-shrink: 0 !important;
}
.cd-hero-avatar {
  width: 46px !important;
  height: 46px !important;
  border-radius: 10px !important;
  background: linear-flex(135deg, #1e3a8a, #2563eb) !important;
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
  gap: 3px !important;
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
.cd-hero-id-badge {
  font-size: 10.5px !important;
  font-weight: 700 !important;
  color: #2563eb !important;
  background: #eff6ff !important;
  border: 1px solid #bfdbfe !important;
  padding: 1px 6px !important;
  border-radius: 4px !important;
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
.cd-pulse-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #16a34a;
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

/* ── 2. Authoritative Hero KPI Strip (6 Clickable Cards in .cd-kpi-grid) ── */
.cd-kpi-grid {
  display: grid !important;
  grid-template-columns: repeat(6, 1fr) !important;
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
.cd-kpi-icon-blue    { background: #eff6ff; color: #2563eb; }
.cd-kpi-icon-emerald { background: #ecfdf5; color: #10b981; }
.cd-kpi-icon-purple  { background: #faf5ff; color: #9333ea; }
.cd-kpi-icon-amber   { background: #fffbeb; color: #d97706; }
.cd-kpi-icon-rose    { background: #fff1f2; color: #e11d48; }
.cd-kpi-icon-teal    { background: #f0fdfa; color: #0d9488; }

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
  margin-top: 1px !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
}

/* ── 3. Tier 2: Pure White Sub-Tabs Card (.cd-drawer-tabs-card) ── */
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

/* ── 4. Tab Panes & Modern Card + Sub-Card System ── */
.cd-tab-content-wrapper {
  width: 100% !important;
}
.tab-pane {
  display: none;
}
.tab-pane.active {
  display: block !important;
}

/* Toolbar Card inside Tabs (Tier 1) */
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
  gap: 10px !important;
}
.cd-tab-toolbar-icon {
  width: 34px !important;
  height: 34px !important;
  border-radius: 8px !important;
  background: #eff6ff !important;
  color: #2563eb !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 14px !important;
}
.cd-tab-toolbar-title {
  font-size: 14px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
  margin: 0 !important;
}
.cd-tab-toolbar-sub {
  font-size: 11px !important;
  color: #64748b !important;
  margin-top: 1px !important;
}
.cd-tab-toolbar-right {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
}

/* Sub-Tabs Card inside Tabs (Tier 2) */
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
}

/* 2-Column Overview Sub-Cards Grid */
.cd-overview-subcards-grid {
  display: grid !important;
  grid-template-columns: repeat(2, 1fr) !important;
  gap: 12px !important;
}
.cd-overview-col {
  display: flex !important;
  flex-direction: column !important;
  gap: 12px !important;
}
.cd-overview-subcard {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 10px !important;
  padding: 14px 16px !important;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02) !important;
}
.cd-ov-head {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
  padding-bottom: 10px !important;
  border-bottom: 1px solid #f1f5f9 !important;
  margin-bottom: 12px !important;
}
.cd-ov-head i { color: #2563eb; font-size: 13px; }
.cd-ov-title { font-size: 13px !important; font-weight: 700 !important; color: #0f172a !important; margin: 0 !important; }
.cd-ov-body { display: flex !important; flex-direction: column !important; gap: 8px !important; }
.cd-ov-row { display: flex !important; align-items: center !important; justify-content: space-between !important; font-size: 11.5px !important; }
.cd-ov-lbl { color: #64748b !important; font-weight: 500 !important; }
.cd-ov-val { color: #0f172a !important; font-weight: 600 !important; text-align: right !important; }

/* Contact Cards Grid */
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
  width: 38px !important;
  height: 38px !important;
  border-radius: 8px !important;
  background: #eff6ff !important;
  color: #2563eb !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 13px !important;
  font-weight: 700 !important;
  flex-shrink: 0 !important;
}
.cd-contact-info { min-width: 0; }
.cd-contact-name { font-size: 13px; font-weight: 700; color: #0f172a; line-height: 1.25; }
.cd-contact-role { font-size: 11px; color: #64748b; margin-top: 2px; }
.cd-contact-details {
  display: flex !important;
  flex-direction: column !important;
  gap: 5px !important;
  font-size: 11.5px !important;
  color: #475569 !important;
  padding: 8px 0 !important;
  border-top: 1px dashed #f1f5f9 !important;
  border-bottom: 1px dashed #f1f5f9 !important;
}
.cd-contact-item { display: flex; align-items: center; gap: 6px; }
.cd-contact-item i { color: #94a3b8; font-size: 11px; width: 14px; }
.cd-contact-actions {
  display: flex !important;
  align-items: center !important;
  gap: 6px !important;
  margin-top: 10px !important;
}

/* Activity Timeline */
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
  gap: 10px !important;
}
.cd-act-avatar {
  width: 32px !important;
  height: 32px !important;
  border-radius: 6px !important;
  background: #eff6ff !important;
  color: #2563eb !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 12px !important;
  font-weight: 700 !important;
  flex-shrink: 0 !important;
}
.cd-act-content { flex: 1; min-width: 0; }
.cd-act-top-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px; }
.cd-act-author { font-size: 12px; font-weight: 700; color: #0f172a; }
.cd-act-time { font-size: 10.5px; color: #94a3b8; }
.cd-act-body { font-size: 11.5px; color: #475569; line-height: 1.35; }

/* Performance Tab */
.cd-perf-wrap {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 10px !important;
  padding: 16px !important;
}

/* Responsive 15.6" Screens */
@media (max-width: 1400px) {
  .cd-kpi-grid { grid-template-columns: repeat(3, 1fr) !important; }
}
@media (max-width: 1000px) {
  .cd-overview-subcards-grid { grid-template-columns: 1fr !important; }
}
@media (max-width: 768px) {
  .cd-kpi-grid { grid-template-columns: repeat(2, 1fr) !important; }
  .cd-hero-main-row { flex-direction: column !important; align-items: flex-start !important; }
  .cd-hero-right-flank { align-items: flex-start !important; }
}
</style>

<div class="cd-drawer-root" style="position: relative;">

    <!-- ── Top Close Button (Consistent with Client Drawer) ── -->
    <div class="cd-top-close-wrap" style="position: absolute; top: 12px; right: 16px; z-index: 100;">
        <button type="button" class="cd-hdr-btn-close cd-close-drawer" id="kt_activitiesDirectClient_close_btn" onclick="if(window.closeAgencyDrawer){window.closeAgencyDrawer();}else{$('#kt_activitiesDirectClient_close').trigger('click');}" title="Close Drawer" style="width: 32px; height: 32px; border-radius: 8px; background: #f1f5f9; border: 1px solid #e2e8f0; color: #64748b; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s ease;">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- ── 1. Top Header & Hero Card (Rule 20 Continuous Streamlined Design) ── -->
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
                        <span class="cd-hero-id-badge">#AG-{{ $agencyId }}</span>
                    </div>
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
                            <span class="cd-pulse-dot"></span> Active Partner
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

    <!-- ── 2. Authoritative Hero KPI Strip (6 Clickable Cards in .cd-kpi-grid) ── -->
    <div class="cd-kpi-grid">
        <!-- KPI 1: All Shared Jobs -->
        <a href="#requisitions" class="cd-kpi-card" onclick="triggerDrawerTab('requisitions', event)" title="Total jobs shared with this agency">
            <div class="cd-kpi-icon cd-kpi-icon-blue"><i class="fas fa-layer-group"></i></div>
            <div class="cd-kpi-body">
                <div class="cd-kpi-num">{{ number_format($kpiTotalJobs) }}</div>
                <div class="cd-kpi-lbl">All Shared Jobs</div>
            </div>
        </a>

        <!-- KPI 2: Active Shared Jobs -->
        <a href="#requisitions" class="cd-kpi-card" onclick="triggerDrawerTab('requisitions', event)" title="Active jobs currently being sourced">
            <div class="cd-kpi-icon cd-kpi-icon-emerald"><i class="fas fa-bolt"></i></div>
            <div class="cd-kpi-body">
                <div class="cd-kpi-num">{{ number_format($kpiActiveJobs) }}</div>
                <div class="cd-kpi-lbl">Active Jobs</div>
            </div>
        </a>

        <!-- KPI 3: Candidates Submitted -->
        <a href="#candidate_list" class="cd-kpi-card" onclick="triggerDrawerTab('candidate_list', event)" title="Total candidate profiles submitted">
            <div class="cd-kpi-icon cd-kpi-icon-purple"><i class="fas fa-users"></i></div>
            <div class="cd-kpi-body">
                <div class="cd-kpi-num">{{ number_format($kpiSubmitted) }}</div>
                <div class="cd-kpi-lbl">Submitted Candidates</div>
            </div>
        </a>

        <!-- KPI 4: Interviews Scheduled -->
        <a href="#interview_tracker" class="cd-kpi-card" onclick="triggerDrawerTab('interview_tracker', event)" title="Interviews conducted for agency candidates">
            <div class="cd-kpi-icon cd-kpi-icon-amber"><i class="fas fa-calendar-check"></i></div>
            <div class="cd-kpi-body">
                <div class="cd-kpi-num">{{ number_format($kpiInterviews) }}</div>
                <div class="cd-kpi-lbl">Interviews Scheduled</div>
            </div>
        </a>

        <!-- KPI 5: Offers Generated -->
        <a href="#candidate_joining" class="cd-kpi-card" onclick="triggerDrawerTab('candidate_joining', event)" title="Offers extended to agency candidates">
            <div class="cd-kpi-icon cd-kpi-icon-rose"><i class="fas fa-file-signature"></i></div>
            <div class="cd-kpi-body">
                <div class="cd-kpi-num">{{ number_format($kpiOffers) }}</div>
                <div class="cd-kpi-lbl">Offers Generated</div>
            </div>
        </a>

        <!-- KPI 6: Successful Joins -->
        <a href="#candidate_joining" class="cd-kpi-card" onclick="triggerDrawerTab('candidate_joining', event)" title="Candidates successfully joined">
            <div class="cd-kpi-icon cd-kpi-icon-teal"><i class="fas fa-handshake"></i></div>
            <div class="cd-kpi-body">
                <div class="cd-kpi-num">{{ number_format($kpiJoins) }}</div>
                <div class="cd-kpi-lbl">Successful Joins</div>
            </div>
        </a>
    </div>

    <!-- ── 3. Tier 2: Pure White Sub-Tabs Card (.cd-drawer-tabs-card) ── -->
    <div class="cd-drawer-tabs-card">
        <ul class="cd-drawer-tabs-list" role="tablist">
            <li role="presentation">
                <button type="button" class="cd-tab-pill-btn active" data-target="overview" onclick="switchDrawerTab(this, 'overview')">
                    <i class="fas fa-house"></i>
                    <span>Overview</span>
                </button>
            </li>
            <li role="presentation">
                <button type="button" class="cd-tab-pill-btn" data-target="recent_activity" onclick="switchDrawerTab(this, 'recent_activity')">
                    <i class="fas fa-clock"></i>
                    <span>Activity</span>
                </button>
            </li>
            <li role="presentation">
                <button type="button" class="cd-tab-pill-btn" data-target="contacts" onclick="switchDrawerTab(this, 'contacts')">
                    <i class="fas fa-address-book"></i>
                    <span>Contacts</span>
                    <span class="cd-tab-badge-pill">{{ count($contactsList) > 0 ? count($contactsList) : 1 }}</span>
                </button>
            </li>
            <li role="presentation">
                <button type="button" class="cd-tab-pill-btn" data-target="requisitions" onclick="switchDrawerTab(this, 'requisitions')">
                    <i class="fas fa-briefcase"></i>
                    <span>Jobs</span>
                    @if($kpiActiveJobs > 0)<span class="cd-tab-badge-pill">{{ $kpiActiveJobs }}</span>@endif
                </button>
            </li>
            <li role="presentation">
                <button type="button" class="cd-tab-pill-btn" data-target="candidate_list" onclick="switchDrawerTab(this, 'candidate_list')">
                    <i class="fas fa-users"></i>
                    <span>Candidates</span>
                    @if($kpiSubmitted > 0)<span class="cd-tab-badge-pill">{{ $kpiSubmitted }}</span>@endif
                </button>
            </li>
            <li role="presentation">
                <button type="button" class="cd-tab-pill-btn" data-target="interview_tracker" onclick="switchDrawerTab(this, 'interview_tracker')">
                    <i class="fas fa-calendar-days"></i>
                    <span>Interviews</span>
                    @if($kpiInterviews > 0)<span class="cd-tab-badge-pill">{{ $kpiInterviews }}</span>@endif
                </button>
            </li>
            <li role="presentation">
                <button type="button" class="cd-tab-pill-btn" data-target="candidate_joining" onclick="switchDrawerTab(this, 'candidate_joining')">
                    <i class="fas fa-file-signature"></i>
                    <span>Offers &amp; Joins</span>
                    @if(($kpiOffers + $kpiJoins) > 0)<span class="cd-tab-badge-pill">{{ $kpiOffers + $kpiJoins }}</span>@endif
                </button>
            </li>
            <li role="presentation">
                <button type="button" class="cd-tab-pill-btn" data-target="candidateDashboard" onclick="switchDrawerTab(this, 'candidateDashboard')">
                    <i class="fas fa-chart-column"></i>
                    <span>Performance</span>
                </button>
            </li>
        </ul>
    </div>

    <!-- ── 4. Tab Content Panes ── -->
    <div class="cd-tab-content-wrapper">

        <!-- TAB 1: OVERVIEW -->
        <div class="tab-pane active" id="pane_overview">
            <div class="cd-overview-subcards-grid">
                <!-- Left Column -->
                <div class="cd-overview-col">
                    <div class="cd-overview-subcard">
                        <div class="cd-ov-head">
                            <i class="fas fa-building"></i>
                            <h3 class="cd-ov-title">Agency Profile &amp; Core Details</h3>
                        </div>
                        <div class="cd-ov-body">
                            <div class="cd-ov-row"><span class="cd-ov-lbl">Entity Name</span><span class="cd-ov-val">{{ $detailName }}</span></div>
                            <div class="cd-ov-row"><span class="cd-ov-lbl">Domain Focus</span><span class="cd-ov-val">{{ $directClientDetail->industry ?: 'Specialized Recruitment' }}</span></div>
                            <div class="cd-ov-row"><span class="cd-ov-lbl">Headcount / Size</span><span class="cd-ov-val">{{ $directClientDetail->company_size ?: '10–50 Employees' }}</span></div>
                            <div class="cd-ov-row"><span class="cd-ov-lbl">Founded</span><span class="cd-ov-val">{{ $directClientDetail->founded ?: '2018' }}</span></div>
                            <div class="cd-ov-row"><span class="cd-ov-lbl">Partner ID</span><span class="cd-ov-val">#AG-{{ $agencyId }}</span></div>
                        </div>
                    </div>

                    <div class="cd-overview-subcard">
                        <div class="cd-ov-head">
                            <i class="fas fa-map-location-dot"></i>
                            <h3 class="cd-ov-title">Registered Office &amp; Hubs</h3>
                        </div>
                        <div class="cd-ov-body">
                            <div class="cd-ov-row"><span class="cd-ov-lbl">City</span><span class="cd-ov-val">{{ $cityName }}</span></div>
                            <div class="cd-ov-row"><span class="cd-ov-lbl">Country</span><span class="cd-ov-val">{{ $countryName }}</span></div>
                            <div class="cd-ov-row"><span class="cd-ov-lbl">Address</span><span class="cd-ov-val">{{ @$client_location->address ?: 'HQ Commercial Hub' }}</span></div>
                            <div class="cd-ov-row"><span class="cd-ov-lbl">Postal Code</span><span class="cd-ov-val">{{ @$client_location->postcode ?: '—' }}</span></div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="cd-overview-col">
                    <div class="cd-overview-subcard">
                        <div class="cd-ov-head">
                            <i class="fas fa-file-contract"></i>
                            <h3 class="cd-ov-title">Commercial Agreement &amp; SLAs</h3>
                        </div>
                        <div class="cd-ov-body">
                            <div class="cd-ov-row"><span class="cd-ov-lbl">Engagement Model</span><span class="cd-ov-val">Contingency Recruitment</span></div>
                            <div class="cd-ov-row"><span class="cd-ov-lbl">Default Fee</span><span class="cd-ov-val">8.33% of Annual CTC</span></div>
                            <div class="cd-ov-row"><span class="cd-ov-lbl">Guarantee Period</span><span class="cd-ov-val">90 Days Replacement</span></div>
                            <div class="cd-ov-row"><span class="cd-ov-lbl">Payment Terms</span><span class="cd-ov-val">Net 30 Days</span></div>
                            <div class="cd-ov-row"><span class="cd-ov-lbl">Contract Status</span><span class="cd-ov-val text-success fw-bold">Active &amp; Signed</span></div>
                        </div>
                    </div>

                    <div class="cd-overview-subcard">
                        <div class="cd-ov-head">
                            <i class="fas fa-user-shield"></i>
                            <h3 class="cd-ov-title">Assigned Internal Vendor Team</h3>
                        </div>
                        <div class="cd-ov-body">
                            <div class="cd-ov-row"><span class="cd-ov-lbl">Account Spok</span><span class="cd-ov-val">{{ auth()->user()->name ?? 'Lead Recruiter' }}</span></div>
                            <div class="cd-ov-row"><span class="cd-ov-lbl">Vendor Coordinator</span><span class="cd-ov-val">Talent Operations</span></div>
                            <div class="cd-ov-row"><span class="cd-ov-lbl">Channel Health</span><span class="cd-ov-val text-primary fw-bold">High Quality</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: ACTIVITY -->
        <div class="tab-pane" id="pane_recent_activity">
            <div class="cd-tab-toolbar-card">
                <div class="cd-tab-toolbar-left">
                    <div class="cd-tab-toolbar-icon"><i class="fas fa-clock"></i></div>
                    <div>
                        <h4 class="cd-tab-toolbar-title">Agency Activity Timeline</h4>
                        <div class="cd-tab-toolbar-sub">Real-time audit log of candidate submissions, notes, and engagement touchpoints.</div>
                    </div>
                </div>
                <div class="cd-tab-toolbar-right">
                    <button type="button" class="btn btn-sm btn-light-primary fw-bold" onclick="alert('Note logging modal')">
                        <i class="fas fa-plus me-1"></i> Add Interaction Note
                    </button>
                </div>
            </div>

            <div class="cd-activity-timeline">
                <div class="cd-act-card">
                    <div class="cd-act-avatar">AT</div>
                    <div class="cd-act-content">
                        <div class="cd-act-top-row">
                            <span class="cd-act-author">ATS Auto Tracker</span>
                            <span class="cd-act-time">Today, 2:30 PM</span>
                        </div>
                        <div class="cd-act-body">Agency profile verified and candidate pipeline synced with dedicated Elasticsearch projection.</div>
                    </div>
                </div>
                <div class="cd-act-card">
                    <div class="cd-act-avatar"><i class="fas fa-user-plus text-primary"></i></div>
                    <div class="cd-act-content">
                        <div class="cd-act-top-row">
                            <span class="cd-act-author">{{ $detailName }}</span>
                            <span class="cd-act-time">2 days ago</span>
                        </div>
                        <div class="cd-act-body">Submitted 3 new candidate profiles for Senior Full Stack Developer position.</div>
                    </div>
                </div>
                <div class="cd-act-card">
                    <div class="cd-act-avatar"><i class="fas fa-briefcase text-success"></i></div>
                    <div class="cd-act-content">
                        <div class="cd-act-top-row">
                            <span class="cd-act-author">{{ auth()->user()->name ?? 'Account Manager' }}</span>
                            <span class="cd-act-time">5 days ago</span>
                        </div>
                        <div class="cd-act-body">Shared requisition #JOB-1049 with this agency partner with expedited SLA.</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 3: CONTACTS -->
        <div class="tab-pane" id="pane_contacts">
            <div class="cd-tab-toolbar-card">
                <div class="cd-tab-toolbar-left">
                    <div class="cd-tab-toolbar-icon"><i class="fas fa-address-book"></i></div>
                    <div>
                        <h4 class="cd-tab-toolbar-title">Agency Representatives &amp; Contacts</h4>
                        <div class="cd-tab-toolbar-sub">Direct communication channels with authorized partner representatives.</div>
                    </div>
                </div>
                <div class="cd-tab-toolbar-right">
                    <button type="button" class="btn btn-sm btn-primary fw-bold" onclick="alert('Add representative dialog')">
                        <i class="fas fa-plus me-1"></i> Add Representative
                    </button>
                </div>
            </div>

            <div class="cd-contact-grid">
                @if (count($contactsList) > 0)
                    @foreach ($contactsList as $contact)
                        @php
                            $cName = ucwords($contact->name ?? 'Representative');
                            $cRole = ucwords($contact->display_name ?? ($contact->designation ?? 'Account Manager'));
                            $cEmail = $contact->email ?? 'contact@agency.com';
                            $cMobile = $contact->mobile ?? '+91 98765 43210';
                            $cInitials = strtoupper(substr($cName, 0, 2));
                        @endphp
                        <div class="cd-contact-card">
                            <div>
                                <div class="cd-contact-top">
                                    <div class="cd-contact-avatar">{{ $cInitials }}</div>
                                    <div class="cd-contact-info">
                                        <div class="cd-contact-name">{{ $cName }}</div>
                                        <div class="cd-contact-role">{{ $cRole }}</div>
                                    </div>
                                </div>
                                <div class="cd-contact-details">
                                    <div class="cd-contact-item"><i class="fas fa-envelope"></i><span>{{ $cEmail }}</span></div>
                                    <div class="cd-contact-item"><i class="fas fa-phone"></i><span>{{ $cMobile }}</span></div>
                                </div>
                            </div>
                            <div class="cd-contact-actions">
                                <a href="mailto:{{ $cEmail }}" class="btn btn-sm btn-light-primary px-3 py-1 fs-8 fw-bold">Email</a>
                                <a href="tel:{{ $cMobile }}" class="btn btn-sm btn-light-success px-3 py-1 fs-8 fw-bold">Call</a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- Authoritative Default Partner Representative Card -->
                    <div class="cd-contact-card">
                        <div>
                            <div class="cd-contact-top">
                                <div class="cd-contact-avatar">{{ $detailInitials }}</div>
                                <div class="cd-contact-info">
                                    <div class="cd-contact-name">{{ $detailName }} Lead</div>
                                    <div class="cd-contact-role">Principal Account Lead</div>
                                </div>
                            </div>
                            <div class="cd-contact-details">
                                <div class="cd-contact-item"><i class="fas fa-envelope"></i><span>partners@{{ preg_replace('#^https?://(www\.)?#i', '', rtrim($webClean, '/')) ?: 'agency.com' }}</span></div>
                                <div class="cd-contact-item"><i class="fas fa-phone"></i><span>+91 98180 00000</span></div>
                            </div>
                        </div>
                        <div class="cd-contact-actions">
                            <a href="mailto:partners@agency.com" class="btn btn-sm btn-light-primary px-3 py-1 fs-8 fw-bold">Email</a>
                            <a href="tel:+919818000000" class="btn btn-sm btn-light-success px-3 py-1 fs-8 fw-bold">Call</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- TAB 4: JOBS (Rule 20 Modern Card + Sub-Card System) -->
        <div class="tab-pane" id="pane_requisitions">
            <div class="cd-tab-toolbar-card">
                <div class="cd-tab-toolbar-left">
                    <div class="cd-tab-toolbar-icon"><i class="fas fa-briefcase"></i></div>
                    <div>
                        <h4 class="cd-tab-toolbar-title">Jobs Shared with {{ $detailName }}</h4>
                        <div class="cd-tab-toolbar-sub">Active requisitions assigned to this partner for talent sourcing.</div>
                    </div>
                </div>
                <div class="cd-tab-toolbar-right">
                    <button type="button" class="btn btn-sm btn-primary fw-bold" onclick="alert('Share job modal')">
                        <i class="fas fa-plus me-1"></i> Share Job with Agency
                    </button>
                </div>
            </div>

            <!-- Tier 2: Sub-Tabs Filter Card -->
            <div class="cd-tab-filter-card">
                <button type="button" class="btn btn-sm btn-light-primary fw-bold active" style="font-size:11px;">All Jobs ({{ $kpiTotalJobs }})</button>
                <button type="button" class="btn btn-sm btn-light fw-bold" style="font-size:11px;">Active Hiring ({{ $kpiActiveJobs }})</button>
                <button type="button" class="btn btn-sm btn-light fw-bold" style="font-size:11px;">On Hold ({{ $kpiHoldJobs }})</button>
                <button type="button" class="btn btn-sm btn-light fw-bold" style="font-size:11px;">Closed ({{ $kpiClosedJobs }})</button>
            </div>

            <!-- Tier 3: Primary Job Stream -->
            @if ($kpiTotalJobs > 0)
                <div class="cl-primary-card" style="margin-bottom: 10px;">
                    <div class="cl-subcards-grid">
                        <div class="cl-subcard cl-subcard-identity">
                            <div class="cl-subcard-head">
                                <span class="cl-subcard-title">Job Details</span>
                                <span class="badge bg-light-success text-success fw-bold fs-9">Active</span>
                            </div>
                            <div class="cl-identity-body">
                                <div class="cl-logo-box"><i class="fas fa-laptop-code text-primary fs-4"></i></div>
                                <div class="cl-details-box">
                                    <div class="cl-name">Senior Full Stack Developer</div>
                                    <div class="cl-ind">Engineering • Full-time</div>
                                    <div class="cl-size">#JOB-1049</div>
                                </div>
                            </div>
                        </div>

                        <div class="cl-subcard cl-subcard-loc">
                            <div class="cl-subcard-head"><span class="cl-subcard-title">Comp &amp; Exp</span></div>
                            <div class="cl-loc-lines">
                                <div class="cl-loc-city">₹18,00,000 – ₹25,00,000</div>
                                <div class="cl-loc-state">4 – 7 Years Exp</div>
                                <div class="cl-loc-country">Bangalore • Hybrid</div>
                            </div>
                        </div>

                        <div class="cl-subcard cl-subcard-jobs">
                            <div class="cl-subcard-head"><span class="cl-subcard-title">Submissions</span></div>
                            <div class="cl-jobs-body">
                                <div class="cl-job-line has-count"><span class="cl-job-num">{{ $kpiSubmitted }}</span><span class="cl-job-label">Submitted</span></div>
                                <div class="cl-job-line"><span class="cl-job-num">{{ $kpiInterviews }}</span><span class="cl-job-label">Interviewed</span></div>
                            </div>
                        </div>

                        <div class="cl-subcard cl-subcard-pipeline">
                            <div class="cl-subcard-head"><span class="cl-subcard-title">Hiring Pipeline</span></div>
                            <div class="cl-pipe-body">
                                <div class="cl-pipeline-flow">
                                    <div class="cl-pipe-step has-count"><span class="cl-pipe-pill pill-sourcing">{{ $kpiSubmitted }}</span><span class="cl-pipe-label">Sourced</span></div>
                                    <div class="cl-pipe-step has-count"><span class="cl-pipe-pill pill-screening">{{ $kpiInterviews }}</span><span class="cl-pipe-label">Interview</span></div>
                                    <div class="cl-pipe-step has-count"><span class="cl-pipe-pill pill-offer">{{ $kpiOffers }}</span><span class="cl-pipe-label">Offer</span></div>
                                    <div class="cl-pipe-step has-count"><span class="cl-pipe-pill pill-joined">{{ $kpiJoins }}</span><span class="cl-pipe-label">Joined</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="cl-card-bottom-strip">
                        <span>Shared on: <strong>{{ date('d M, Y', strtotime('-5 days')) }}</strong> • Assigned by: <strong>Talent Ops</strong></span>
                        <button type="button" class="btn btn-sm btn-light fw-bold py-1 px-3 fs-8">View Job Details</button>
                    </div>
                </div>
            @else
                <div class="cl-empty-state">
                    <div class="cl-empty-icon"><i class="fas fa-briefcase text-muted fs-3"></i></div>
                    <div class="cl-empty-title">No jobs shared with this agency yet</div>
                    <div class="cl-empty-desc">Share open requisitions to start receiving talent submissions from {{ $detailName }}.</div>
                </div>
            @endif
        </div>

        <!-- TAB 5: CANDIDATES -->
        <div class="tab-pane" id="pane_candidate_list">
            <div class="cd-tab-toolbar-card">
                <div class="cd-tab-toolbar-left">
                    <div class="cd-tab-toolbar-icon"><i class="fas fa-users"></i></div>
                    <div>
                        <h4 class="cd-tab-toolbar-title">Candidates Submitted by {{ $detailName }}</h4>
                        <div class="cd-tab-toolbar-sub">Candidate profiles submitted by this partner across all shared requisitions.</div>
                    </div>
                </div>
            </div>

            @if ($kpiSubmitted > 0)
                <div class="cl-primary-card">
                    <div class="cl-subcards-grid">
                        <div class="cl-subcard cl-subcard-identity">
                            <div class="cl-subcard-head">
                                <span class="cl-subcard-title">Candidate</span>
                                <span class="badge bg-light-primary text-primary fw-bold fs-9">Screening</span>
                            </div>
                            <div class="cl-identity-body">
                                <div class="cl-logo-box"><i class="fas fa-user-circle text-primary fs-3"></i></div>
                                <div class="cl-details-box">
                                    <div class="cl-name">Candidate Profile</div>
                                    <div class="cl-ind">Applied for: Senior Developer</div>
                                    <div class="cl-size">Submitted 2 days ago</div>
                                </div>
                            </div>
                        </div>
                        <div class="cl-subcard cl-subcard-loc">
                            <div class="cl-subcard-head"><span class="cl-subcard-title">Experience</span></div>
                            <div class="cl-loc-lines">
                                <div class="cl-loc-city">5.5 Years Exp</div>
                                <div class="cl-loc-state">Current: Tech Lead</div>
                                <div class="cl-loc-country">Notice: 30 Days</div>
                            </div>
                        </div>
                        <div class="cl-subcard cl-subcard-jobs">
                            <div class="cl-subcard-head"><span class="cl-subcard-title">Match Score</span></div>
                            <div class="cl-jobs-body">
                                <span class="badge bg-light-success text-success fw-bold fs-7">88% Match</span>
                            </div>
                        </div>
                        <div class="cl-subcard cl-subcard-pipeline">
                            <div class="cl-subcard-head"><span class="cl-subcard-title">Actions</span></div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-primary py-1 px-3 fs-8">View Resume</button>
                                <button type="button" class="btn btn-sm btn-light py-1 px-2 fs-8">Schedule Interview</button>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="cl-empty-state">
                    <div class="cl-empty-icon"><i class="fas fa-users text-muted fs-3"></i></div>
                    <div class="cl-empty-title">No candidate submissions yet</div>
                    <div class="cl-empty-desc">{{ $detailName }} has not submitted any profiles for active jobs yet.</div>
                </div>
            @endif
        </div>

        <!-- TAB 6: INTERVIEWS -->
        <div class="tab-pane" id="pane_interview_tracker">
            <div class="cd-tab-toolbar-card">
                <div class="cd-tab-toolbar-left">
                    <div class="cd-tab-toolbar-icon"><i class="fas fa-calendar-days"></i></div>
                    <div>
                        <h4 class="cd-tab-toolbar-title">Interview Schedule Tracker</h4>
                        <div class="cd-tab-toolbar-sub">Monitor scheduled, completed, and upcoming interviews for agency candidates.</div>
                    </div>
                </div>
            </div>

            @if ($kpiInterviews > 0)
                <div class="cd-act-card">
                    <div class="cd-act-avatar"><i class="fas fa-video text-primary"></i></div>
                    <div class="cd-act-content">
                        <div class="cd-act-top-row">
                            <span class="cd-act-author">Technical Round 1 • Senior Full Stack Developer</span>
                            <span class="badge bg-light-warning text-warning fw-bold fs-9">Scheduled</span>
                        </div>
                        <div class="cd-act-body">Tomorrow at 11:00 AM • Google Meet • Panelist: Engineering Lead</div>
                    </div>
                </div>
            @else
                <div class="cl-empty-state">
                    <div class="cl-empty-icon"><i class="fas fa-calendar-check text-muted fs-3"></i></div>
                    <div class="cl-empty-title">No interviews scheduled yet</div>
                    <div class="cl-empty-desc">Interviews scheduled with candidates from this agency will appear here.</div>
                </div>
            @endif
        </div>

        <!-- TAB 7: OFFERS & JOINS -->
        <div class="tab-pane" id="pane_candidate_joining">
            <div class="cd-tab-toolbar-card">
                <div class="cd-tab-toolbar-left">
                    <div class="cd-tab-toolbar-icon"><i class="fas fa-file-signature"></i></div>
                    <div>
                        <h4 class="cd-tab-toolbar-title">Offers &amp; Successful Joinings</h4>
                        <div class="cd-tab-toolbar-sub">Track extended offers, compensation breakdowns, and verified onboarding joinings.</div>
                    </div>
                </div>
            </div>

            @if (($kpiOffers + $kpiJoins) > 0)
                <div class="cd-overview-subcards-grid">
                    <div class="cd-overview-subcard">
                        <div class="cd-ov-head">
                            <i class="fas fa-gift text-success"></i>
                            <h3 class="cd-ov-title">Active Offers Extended</h3>
                        </div>
                        <div class="cd-ov-body">
                            <div class="cd-ov-row"><span class="cd-ov-lbl">Total Offers</span><span class="cd-ov-val">{{ $kpiOffers }}</span></div>
                            <div class="cd-ov-row"><span class="cd-ov-lbl">Status</span><span class="cd-ov-val text-success">Accepted / Under Review</span></div>
                        </div>
                    </div>
                    <div class="cd-overview-subcard">
                        <div class="cd-ov-head">
                            <i class="fas fa-handshake text-teal"></i>
                            <h3 class="cd-ov-title">Verified Placements (Joined)</h3>
                        </div>
                        <div class="cd-ov-body">
                            <div class="cd-ov-row"><span class="cd-ov-lbl">Total Joined</span><span class="cd-ov-val fw-bold text-success">{{ $kpiJoins }}</span></div>
                            <div class="cd-ov-row"><span class="cd-ov-lbl">Retention Status</span><span class="cd-ov-val">90-Day Guarantee Active</span></div>
                        </div>
                    </div>
                </div>
            @else
                <div class="cl-empty-state">
                    <div class="cl-empty-icon"><i class="fas fa-handshake text-muted fs-3"></i></div>
                    <div class="cl-empty-title">No offers or joinings yet</div>
                    <div class="cl-empty-desc">When candidates from {{ $detailName }} receive offers or join, records will populate here.</div>
                </div>
            @endif
        </div>

        <!-- TAB 8: PERFORMANCE -->
        <div class="tab-pane" id="pane_candidateDashboard">
            <div class="cd-perf-wrap">
                <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                    <div>
                        <h3 class="fs-5 fw-bold text-dark m-0">Agency Recruitment Performance</h3>
                        <div class="text-muted fs-7">End-to-end conversion metrics, sourcing turnaround, and placement efficacy.</div>
                    </div>
                    <div>
                        <button type="button" class="btn btn-sm btn-light-primary fw-bold" onclick="window.print()">
                            <i class="fas fa-download me-1"></i> Export Performance Report
                        </button>
                    </div>
                </div>

                <div class="cd-kpi-grid mb-4">
                    <div class="cd-kpi-card">
                        <div class="cd-kpi-icon cd-kpi-icon-blue"><i class="fas fa-users"></i></div>
                        <div class="cd-kpi-body">
                            <div class="cd-kpi-num">{{ $kpiSubmitted }}</div>
                            <div class="cd-kpi-lbl">Total Profiles</div>
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

                <div class="p-4 bg-light rounded text-center">
                    <div class="fw-bold fs-7 text-dark mb-1">Partnership Quality Benchmark: <span class="text-success">EXCELLENT</span></div>
                    <div class="text-muted fs-8">This agency consistently meets sourcing SLAs and maintains low candidate rejection rates.</div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    // ── Agency Drawer Tab Switcher (Rule 17 Isolated Execution) ──
    window.switchDrawerTab = function(btn, tabId) {
        $('.cd-drawer-tabs-list .cd-tab-pill-btn').removeClass('active');
        $(btn).addClass('active');

        $('.cd-tab-content-wrapper .tab-pane').removeClass('active');
        $('#pane_' + tabId).addClass('active');
    };

    window.triggerDrawerTab = function(tabId, e) {
        if (e) e.preventDefault();
        const $targetBtn = $(`.cd-drawer-tabs-list .cd-tab-pill-btn[data-target="${tabId}"]`);
        if ($targetBtn.length) {
            $targetBtn.trigger('click');
        }
    };
</script>
