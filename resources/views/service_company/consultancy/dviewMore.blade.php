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
    $placement_fee = $placement_fee ?? '8.33% of Annual CTC';
    $partner_joined_date = $partner_joined_date ?? date('d M, Y');
    $industries_name = $industries_name ?? 'Information Technology & Services';
    $company_type_name = $company_type_name ?? 'Corporate Entity';
    $agency_phone = $agency_phone ?? '+91 98765 43210';
    $agency_email = $agency_email ?? 'partners@agency.com';

    // ── Pre-hydrated KPI Numbers (Zero MySQL Read Strain) ──
    $kpiActiveJobs = (int)($Active ?? 0);
    $kpiHoldJobs = (int)($Hold ?? 0);
    $kpiClosedJobs = (int)($Closed ?? 0);
    $kpiTotalJobs = (int)($kpiTotalJobs ?? ($kpiActiveJobs + $kpiHoldJobs + $kpiClosedJobs));

    $kpiSubmitted = (int)($all_candidates ?? 0);
    $isAgencyActive = ($kpiActiveJobs > 0 || $kpiSubmitted > 0);

    // Lists
    $contactsList = $client_contact_info ?? collect([]);
    $jobsList = $sharedJobs ?? collect([]);
    $candsList = $candidatesList ?? collect([]);
    $interviewsScheduleList = $interviewsList ?? collect([]);
    $offersJoiningList = $offersJoiningList ?? collect([]);

    // ── Dynamic Subtab Counters (Calculated strictly browser/memory side) ──
    $itCountAll = count($interviewsScheduleList);
    $itCountUpcoming = 0;
    $itCountPendingFeedback = 0;
    $itCountCompleted = 0;
    $itCountCancelled = 0;
    $todayDate = date('Y-m-d');
    foreach ($interviewsScheduleList as $itItem) {
        $st = strtolower($itItem->status ?? 'pending');
        $sDate = !empty($itItem->schedule_date) ? substr($itItem->schedule_date, 0, 10) : '';
        if ($st === 'canceled' || $st === 'rejected') {
            $itCountCancelled++;
        } elseif ($st === 'hired' || $st === 'completed') {
            $itCountCompleted++;
        } elseif (!empty($itItem->feedback) && (int)$itItem->feedback === 1) {
            $itCountCompleted++;
        } elseif (!empty($sDate) && $sDate < $todayDate) {
            $itCountPendingFeedback++;
        } else {
            $itCountUpcoming++;
        }
    }

    $ojCountAll = count($offersJoiningList);
    $ojCountOffers = 0;
    $ojCountJoiningSoon = 0;
    $ojCountJoined = 0;
    foreach ($offersJoiningList as $ojItem) {
        $msId = (int)($ojItem->master_status_id ?? 0);
        $isJoined = ((int)($ojItem->joined ?? 0) === 1 || $msId === 6 || !empty($ojItem->joined_date));
        if ($isJoined) {
            $ojCountJoined++;
        } elseif (!empty($ojItem->offer_acceptance) && (int)$ojItem->offer_acceptance === 1) {
            $ojCountJoiningSoon++;
        } else {
            $ojCountOffers++;
        }
    }

    $kpiInterviews = $itCountAll;
    $kpiOffers = $ojCountOffers;
    $kpiJoins = $ojCountJoined;
    $kpiOffersTotal = $ojCountAll;

    // Helper: Global Compact Currency Formatter (e.g. ₹500K – 900K or $300K – 500K)
    $formatCompactSalary = function($val, $cur = '₹') {
        if ($val === null || $val === '' || !is_numeric($val)) return null;
        $num = (float)$val;
        if ($num <= 0) return null;
        if ($num >= 1000000) {
            $mVal = $num / 1000000;
            return $cur . (($mVal == (int)$mVal) ? (int)$mVal : number_format($mVal, 1)) . 'M';
        }
        if ($num >= 1000) {
            $kVal = $num / 1000;
            return $cur . (($kVal == (int)$kVal) ? (int)$kVal : number_format($kVal, 1)) . 'K';
        }
        return $cur . number_format($num);
    };
@endphp

@include('service_company.client.components.tab-jobs-style')
@include('service_company.client.components.tab-offers-joinings-style')
@include('service_company.client.components.tab-activity-style')
@include('service_company.client.components.tab-interviews-style')

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
}
.cd-scope h1, .cd-scope h2, .cd-scope h3, .cd-scope h4, .cd-scope h5, .cd-scope h6,
.cd-scope p, .cd-scope span, .cd-scope div, .cd-scope a, .cd-scope button,
.cd-scope input, .cd-scope select, .cd-scope textarea, .cd-scope label, .cd-scope td, .cd-scope th {
  font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
}

/* ── Explicit FontAwesome Restoration ── */
.cd-scope i,
.cd-scope [class*="fa-"],
.cd-scope [class*="fa-"]::before,
.cd-scope [class*="fa-"]::after,
.cd-scope [class^="fa-"],
.cd-scope [class^="fa-"]::before,
.cd-scope [class^="fa-"]::after,
.cd-scope .fa,
.cd-scope .fas,
.cd-scope .far,
.cd-scope .fab,
.cd-scope .fa-solid,
.cd-scope .fa-regular,
.cd-scope .fa-brands,
.cd-scope .fa::before,
.cd-scope .fas::before,
.cd-scope .far::before,
.cd-scope .fab::before,
.cd-scope .fa-solid::before,
.cd-scope .fa-regular::before,
.cd-scope .fa-brands::before {
  font-family: "Font Awesome 6 Free", "Font Awesome 5 Free", "FontAwesome" !important;
  font-style: normal !important;
  font-variant: normal !important;
  text-rendering: auto !important;
  line-height: 1 !important;
  display: inline-block !important;
}
.cd-scope .fa-solid,
.cd-scope .fa-solid::before,
.cd-scope .fas,
.cd-scope .fas::before { font-weight: 900 !important; }
.cd-scope .fa-regular,
.cd-scope .fa-regular::before,
.cd-scope .far,
.cd-scope .far::before { font-weight: 400 !important; }
.cd-scope .fa-brands,
.cd-scope .fa-brands::before,
.cd-scope .fab,
.cd-scope .fab::before { font-family: "Font Awesome 6 Brands", "Font Awesome 5 Brands", "FontAwesome" !important; font-weight: 400 !important; }

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
  min-height: 52px !important;
  margin-bottom: 14px !important;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
  overflow: visible !important;
  display: flex !important;
  align-items: center !important;
}
.cd-drawer-tabs-list {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
  overflow-x: auto !important;
  overflow-y: visible !important;
  flex-wrap: nowrap !important;
  margin: 0 !important;
  padding: 4px 2px !important;
  list-style: none !important;
  border: none !important;
  border-bottom: 0 !important;
  width: 100% !important;
}
.cd-drawer-tabs-list::-webkit-scrollbar { display: none; }
.cd-drawer-tabs-list.nav-tabs { border-bottom: 0 !important; }
.cd-drawer-tabs-list .nav-item {
  margin: 0 !important;
  padding: 0 !important;
}
.cd-tab-pill-btn {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 8px !important;
  height: 34px !important;
  padding: 0 14px !important;
  font-size: 11.5px !important;
  font-weight: 600 !important;
  color: #475569 !important;
  text-decoration: none !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  gap: 7px !important;
  white-space: nowrap !important;
  transition: all 0.15s ease !important;
  cursor: pointer !important;
  box-sizing: border-box !important;
  margin: 0 !important;
  margin-bottom: 0 !important;
}
.cd-tab-pill-btn i {
  color: #94a3b8 !important; /* light, visible tab icon */
  font-size: 12px !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  transition: color 0.15s ease !important;
}
.cd-tab-pill-btn:hover {
  background: #f8fafc !important;
  border-color: #cbd5e1 !important;
  color: #0f172a !important;
}
.cd-tab-pill-btn:hover i {
  color: #009ef7 !important;
}
.cd-tab-pill-btn.active {
  background: #009ef7 !important;
  border-color: #009ef7 !important;
  color: #ffffff !important;
  box-shadow: 0 2px 5px rgba(0, 158, 247, 0.25) !important;
}
.cd-tab-pill-btn.active i {
  color: #ffffff !important;
}
.cd-tab-badge-pill {
  font-size: 10px !important;
  font-weight: 700 !important;
  padding: 1px 6px !important;
  border-radius: 10px !important;
  background: #f1f5f9 !important;
  color: #475569 !important;
  line-height: 1.2 !important;
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
  min-height: 44px !important;
  margin-bottom: 12px !important;
  display: flex !important;
  align-items: center !important;
  gap: 6px !important;
  overflow-x: auto !important;
  overflow-y: visible !important;
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

/* ── Vertical Info List (Replicated from Client Drawer) ── */
.cd-vertical-info-list {
  display: flex !important;
  flex-direction: column !important;
  width: 100% !important;
}
.cd-vinfo-row {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  gap: 12px !important;
  padding: 8px 2px !important;
  border-bottom: 1px solid #f1f5f9 !important;
  min-width: 0 !important;
  transition: background-color 0.15s ease !important;
}
.cd-vinfo-row:first-child { padding-top: 2px !important; }
.cd-vinfo-row:last-child { border-bottom: none !important; padding-bottom: 2px !important; }
.cd-vinfo-row:hover { background-color: #f8fafc !important; }
.cd-vinfo-key {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
  flex: 0 0 140px !important;
  min-width: 120px !important;
}
.cd-vinfo-icon {
  width: 26px !important;
  height: 26px !important;
  border-radius: 6px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 11px !important;
  flex-shrink: 0 !important;
}
.cd-vinfo-icon.red     { background: #fef2f2 !important; color: #ef4444 !important; }
.cd-vinfo-icon.blue    { background: #eff6ff !important; color: #2563eb !important; }
.cd-vinfo-icon.green   { background: #ecfdf5 !important; color: #059669 !important; }
.cd-vinfo-icon.amber   { background: #fefce8 !important; color: #ca8a04 !important; }
.cd-vinfo-icon.purple  { background: #f5f3ff !important; color: #7c3aed !important; }
.cd-vinfo-icon.sky     { background: #f0f9ff !important; color: #0284c7 !important; }
.cd-vinfo-icon.emerald { background: #f0fdf4 !important; color: #16a34a !important; }
.cd-vinfo-icon.orange  { background: #fff7ed !important; color: #ea580c !important; }
.cd-vinfo-icon.slate   { background: #f8fafc !important; color: #475569 !important; border: 1px solid #e2e8f0 !important; }
.cd-vinfo-icon.cyan    { background: #ecfeff !important; color: #0891b2 !important; }
.cd-vinfo-icon.indigo  { background: #eef2ff !important; color: #4f46e5 !important; }

.cd-vinfo-label {
  font-size: 11.5px !important;
  font-weight: 600 !important;
  color: #64748b !important;
  letter-spacing: -0.1px !important;
  line-height: 1.3 !important;
}
.cd-vinfo-val {
  flex: 1 !important;
  min-width: 0 !important;
  text-align: right !important;
  display: flex !important;
  flex-direction: column !important;
  align-items: flex-end !important;
  justify-content: center !important;
  font-size: 12px !important;
  font-weight: 500 !important;
  color: #0f172a !important;
  word-break: break-word !important;
}
.cd-vinfo-link {
  color: #2563eb !important;
  font-weight: 600 !important;
  text-decoration: none !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 4px !important;
  transition: color 0.15s ease !important;
}
.cd-vinfo-link:hover { color: #1d4ed8 !important; text-decoration: underline !important; }
.cd-vintage-chip {
  font-size: 10.5px !important;
  color: #64748b !important;
  font-weight: 500 !important;
  margin-left: 4px !important;
}
.cd-type-badge-pill {
  display: inline-flex !important;
  align-items: center !important;
  gap: 4px !important;
  background: #f1f5f9 !important;
  color: #334155 !important;
  border: 1px solid #e2e8f0 !important;
  padding: 2px 7px !important;
  border-radius: 4px !important;
  font-size: 11px !important;
  font-weight: 600 !important;
}
.cd-tax-wrap {
  display: inline-flex !important;
  align-items: center !important;
  gap: 6px !important;
}
.cd-copy-btn {
  background: transparent !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 4px !important;
  color: #64748b !important;
  width: 22px !important;
  height: 22px !important;
  padding: 0 !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 10px !important;
  cursor: pointer !important;
  transition: all 0.15s ease !important;
}
.cd-copy-btn:hover {
  background: #f8fafc !important;
  color: #0f172a !important;
  border-color: #cbd5e1 !important;
}

/* ── 7b. Overview Tab Enterprise Sub-Cards (Contacts, Locations, Commercials) ── */
.cd-contact-list-group {
  display: flex !important;
  flex-direction: column !important;
  gap: 10px !important;
  width: 100% !important;
}
.cd-contact-subcard {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-left: 3.5px solid #6366f1 !important;
  border-radius: 8px !important;
  padding: 10px 14px !important;
  box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03) !important;
  transition: all 0.2s ease !important;
}
.cd-contact-subcard:hover {
  border-color: #cbd5e1 !important;
  box-shadow: 0 3px 6px rgba(15, 23, 42, 0.05) !important;
}
.cd-contact-subcard-top {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  gap: 10px !important;
}
.cd-contact-subcard-person {
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
  min-width: 0 !important;
  flex: 1 1 auto !important;
}
.cd-contact-subcard-avatar {
  width: 34px !important;
  height: 34px !important;
  border-radius: 50% !important;
  background: #eef2ff !important;
  color: #4f46e5 !important;
  font-size: 12px !important;
  font-weight: 700 !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  flex-shrink: 0 !important;
  border: 1px solid #c7d2fe !important;
}
.cd-contact-subcard-name {
  font-size: 13px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
  line-height: 1.25 !important;
  word-break: break-word !important;
}
.cd-contact-subcard-role {
  font-size: 11px !important;
  font-weight: 500 !important;
  color: #64748b !important;
  line-height: 1.25 !important;
  margin-top: 1px !important;
}
.cd-contact-subcard-linkedin {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  width: 26px !important;
  height: 26px !important;
  border-radius: 6px !important;
  background: #0a66c2 !important;
  color: #ffffff !important;
  font-size: 12px !important;
  flex-shrink: 0 !important;
  text-decoration: none !important;
  transition: opacity 0.15s ease !important;
}
.cd-contact-subcard-linkedin:hover {
  opacity: 0.9 !important;
  color: #ffffff !important;
}
.cd-contact-subcard-phone-line {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
  font-size: 11.5px !important;
  font-weight: 600 !important;
  color: #0f172a !important;
  margin-top: 8px !important;
  padding-top: 8px !important;
  border-top: 1px dashed #f1f5f9 !important;
}

/* Enterprise Location Sub-Cards */
.cd-loc-group {
  display: flex !important;
  flex-direction: column !important;
  gap: 10px !important;
  width: 100% !important;
}
.cd-loc-card {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-left: 3.5px solid #94a3b8 !important;
  border-radius: 8px !important;
  padding: 10px 14px !important;
  text-align: left !important;
  width: 100% !important;
  box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03) !important;
  transition: all 0.2s ease !important;
  box-sizing: border-box !important;
}
.cd-loc-card:hover {
  border-color: #cbd5e1 !important;
  box-shadow: 0 3px 6px rgba(15, 23, 42, 0.05) !important;
}
.cd-loc-card.is-primary,
.cd-loc-card.hq {
  background: #ffffff !important;
  border-color: #dbeafe !important;
  border-left: 3.5px solid #2563eb !important;
}
.cd-loc-card.is-primary:hover,
.cd-loc-card.hq:hover {
  border-color: #bfdbfe !important;
  box-shadow: 0 3px 8px rgba(37, 99, 235, 0.08) !important;
}
.cd-loc-header {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  gap: 8px !important;
  margin-bottom: 6px !important;
}
.cd-loc-header-left {
  display: inline-flex !important;
  align-items: center !important;
  gap: 6px !important;
  flex-shrink: 0 !important;
}
.cd-loc-badge {
  display: inline-flex !important;
  align-items: center !important;
  font-size: 10px !important;
  font-weight: 700 !important;
  line-height: 1 !important;
  white-space: nowrap !important;
  flex-shrink: 0 !important;
  padding: 3px 7px !important;
  border-radius: 4px !important;
  letter-spacing: 0.2px !important;
  text-transform: uppercase !important;
}
.cd-loc-badge.loc-primary {
  background: #eff6ff !important;
  color: #1d4ed8 !important;
  border: 1px solid #bfdbfe !important;
}
.cd-loc-badge.loc-secondary {
  background: #f1f5f9 !important;
  color: #334155 !important;
  border: 1px solid #cbd5e1 !important;
}
.cd-loc-hq-tag {
  display: inline-flex !important;
  align-items: center !important;
  font-size: 9px !important;
  font-weight: 800 !important;
  line-height: 1 !important;
  text-transform: uppercase !important;
  letter-spacing: 0.4px !important;
  background: #2563eb !important;
  color: #ffffff !important;
  padding: 2.5px 6px !important;
  border-radius: 4px !important;
  white-space: nowrap !important;
  flex-shrink: 0 !important;
}
.cd-loc-country-pill {
  display: inline-flex !important;
  align-items: center !important;
  font-size: 10.5px !important;
  font-weight: 600 !important;
  line-height: 1 !important;
  color: #475569 !important;
  background: #f8fafc !important;
  border: 1px solid #e2e8f0 !important;
  padding: 3px 9px !important;
  border-radius: 12px !important;
  white-space: nowrap !important;
  flex-shrink: 0 !important;
}
.cd-loc-city-line {
  display: flex !important;
  align-items: center !important;
  gap: 7px !important;
  font-size: 12.5px !important;
  line-height: 1.35 !important;
  margin-bottom: 4px !important;
  word-break: break-word !important;
}
.cd-loc-city-icon {
  width: 14px !important;
  height: 14px !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 11.5px !important;
  line-height: 1 !important;
  flex-shrink: 0 !important;
}
.cd-loc-city-name {
  font-weight: 700 !important;
  color: #0f172a !important;
  font-size: 12.5px !important;
  line-height: 1.35 !important;
}
.cd-loc-street-line {
  display: flex !important;
  align-items: flex-start !important;
  gap: 5px !important;
  font-size: 11.5px !important;
  color: #64748b !important;
  line-height: 1.4 !important;
  word-break: break-word !important;
  margin-bottom: 3px !important;
  padding-left: 21px !important;
}
.cd-loc-meta-line {
  display: flex !important;
  align-items: center !important;
  gap: 6px !important;
  margin-top: 4px !important;
  padding-left: 21px !important;
}
.cd-loc-meta-chip {
  display: inline-flex !important;
  align-items: center !important;
  background: #f8fafc !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 4px !important;
  padding: 2px 7px !important;
  font-size: 10px !important;
  font-weight: 500 !important;
  color: #64748b !important;
  line-height: 1.2 !important;
}
.cd-loc-meta-chip strong {
  color: #0f172a !important;
  font-weight: 700 !important;
  margin-left: 3px !important;
}

/* ── 8. Master Contacts Tab Modern Card + Sub-Card System (Rule 20) ── */
.cd-contact-toolbar-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 10px 14px;
  margin-bottom: 10px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03);
}
.cd-contact-toolbar-left {
  display: flex;
  align-items: center;
  gap: 12px;
  min-width: 0;
  flex: 1 1 auto;
}
.cd-contact-title-avatar {
  width: 38px;
  height: 38px;
  border-radius: 8px;
  background: #eff6ff;
  color: #009ef7;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 15px;
  flex-shrink: 0;
  border: 1px solid #bfdbfe;
}
.cd-contact-title-wrap {
  display: flex;
  flex-direction: column;
  gap: 2px;
  min-width: 0;
}
.cd-contact-title {
  font-size: 15px;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
  line-height: 1.25;
  white-space: nowrap;
}
.cd-contact-subtitle {
  font-size: 11.5px;
  color: #64748b;
  margin: 0;
  line-height: 1.2;
}
.cd-contact-count-badge {
  background: #eff6ff;
  color: #0284c7;
  border: 1px solid #bfdbfe;
  border-radius: 6px;
  padding: 1px 8px;
  font-size: 11px;
  font-weight: 600;
  white-space: nowrap;
}
.cd-contact-scope-pill {
  background: #ecfdf5;
  color: #065f46;
  border: 1px solid #a7f3d0;
  border-radius: 6px;
  padding: 1px 7px;
  font-size: 10.5px;
  font-weight: 600;
  white-space: nowrap;
}
.cd-contact-toolbar-right {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
}
.cd-contact-search-subcard {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
  background: #f8fafc !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 8px !important;
  padding: 0 10px 0 12px !important;
  height: 36px !important;
  width: 320px !important;
  max-width: 100% !important;
  transition: all 0.15s ease-in-out !important;
  position: relative !important;
}
.cd-contact-search-subcard:focus-within {
  background: #ffffff !important;
  border-color: #009ef7 !important;
  box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
}
.cd-contact-search-subcard i.fa-search {
  color: #94a3b8 !important;
  font-size: 12px !important;
  flex-shrink: 0 !important;
}
.cd-contact-search-subcard input {
  border: none !important;
  background: transparent !important;
  outline: none !important;
  box-shadow: none !important;
  font-size: 12px !important;
  color: #0f172a !important;
  width: 100% !important;
  padding: 0 !important;
  line-height: 36px !important;
}
.cd-contact-search-subcard input::placeholder {
  color: #94a3b8 !important;
  font-weight: 400 !important;
}
.cd-contact-search-clear {
  border: none !important;
  background: transparent !important;
  color: #94a3b8 !important;
  cursor: pointer !important;
  padding: 2px 4px !important;
  font-size: 11px !important;
}
.cd-contact-search-clear:hover {
  color: #ef4444 !important;
}
.cd-contact-action-subcard {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  background: #f8fafc;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #64748b;
  cursor: pointer;
  transition: all 0.15s ease;
  padding: 0;
}
.cd-contact-action-subcard:hover {
  background: #eff6ff;
  color: #009ef7;
  border-color: #bfdbfe;
}
.cd-contact-tabs-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 6px 8px;
  margin-bottom: 12px;
  box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02);
}
.cd-contact-tabs-list {
  display: flex;
  align-items: center;
  gap: 6px;
  overflow-x: auto;
  scrollbar-width: none;
}
.cd-contact-tabs-list::-webkit-scrollbar { display: none; }
.cd-contact-subtab {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 7px;
  border: 1px solid #e2e8f0;
  background: #f8fafc;
  color: #475569;
  font-size: 11.5px;
  font-weight: 500;
  cursor: pointer;
  white-space: nowrap;
  transition: all 0.15s ease;
}
.cd-contact-subtab:hover {
  background: #f1f5f9;
  color: #0f172a;
  border-color: #cbd5e1;
}
.cd-contact-subtab.active {
  background: #009ef7 !important;
  color: #ffffff !important;
  border-color: #009ef7 !important;
  font-weight: 600 !important;
  box-shadow: 0 2px 6px rgba(0, 158, 247, 0.28) !important;
}
.cd-contact-subtab-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 18px;
  height: 18px;
  padding: 0 5px;
  border-radius: 10px;
  font-size: 10px;
  font-weight: 700;
  background: #e2e8f0;
  color: #475569;
}
.cd-contact-subtab.active .cd-contact-subtab-badge {
  background: rgba(255, 255, 255, 0.28) !important;
  color: #ffffff !important;
}
.cd-contact-stream-wrap {
  width: 100%;
  overflow-x: hidden;
}
.cd-contact-stream {
  display: flex;
  flex-direction: column;
  gap: 10px;
  width: 100%;
}
.cd-contact-card {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 10px !important;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
  transition: all 0.15s ease-in-out !important;
  overflow: hidden !important;
  width: 100% !important;
}
.cd-contact-card:hover {
  border-color: #cbd5e1 !important;
  box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06) !important;
  transform: translateY(-1px) !important;
}
.cd-contact-card-grid {
  display: grid !important;
  grid-template-columns: minmax(0, 1.25fr) minmax(0, 1.2fr) minmax(0, 1.05fr) !important;
  gap: 10px !important;
  padding: 10px 12px !important;
  width: 100% !important;
  align-items: stretch !important;
}
.cd-contact-subcard {
  background: #f8fafc !important;
  border: 1px solid #edf2f7 !important;
  border-radius: 8px !important;
  padding: 8px 10px !important;
  display: flex !important;
  flex-direction: column !important;
  justify-content: space-between !important;
  min-width: 0 !important;
}
.cd-contact-subcard-hdr {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  margin-bottom: 6px !important;
  gap: 6px !important;
  line-height: 1 !important;
}
.cd-contact-subcard-label {
  font-size: 10px !important;
  font-weight: 700 !important;
  text-transform: uppercase !important;
  letter-spacing: 0.3px !important;
  color: #64748b !important;
  white-space: nowrap !important;
}
.cd-contact-profile-row {
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
  min-width: 0 !important;
}
.cd-contact-avatar-box {
  width: 40px !important;
  height: 40px !important;
  min-width: 40px !important;
  border-radius: 9px !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 14px !important;
  font-weight: 700 !important;
  color: #ffffff !important;
  background: #009ef7 !important;
  border: 1px solid #009ef7 !important;
  flex-shrink: 0 !important;
  text-transform: uppercase !important;
}
.cd-contact-card-name {
  font-size: 13.5px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
  line-height: 1.25 !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
  white-space: nowrap !important;
}
.cd-contact-card-desig {
  font-size: 11.5px !important;
  font-weight: 500 !important;
  color: #475569 !important;
  line-height: 1.2 !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
  white-space: nowrap !important;
}
.cd-badge-primary-spoc {
  display: inline-flex !important;
  align-items: center !important;
  padding: 2px 7px !important;
  border-radius: 5px !important;
  font-size: 9.5px !important;
  font-weight: 700 !important;
  background: #fef3c7 !important;
  color: #92400e !important;
  border: 1px solid #fde68a !important;
}
.cd-badge-secondary-contact {
  display: inline-flex !important;
  align-items: center !important;
  padding: 2px 6px !important;
  border-radius: 5px !important;
  font-size: 9.5px !important;
  font-weight: 600 !important;
  background: #f1f5f9 !important;
  color: #475569 !important;
  border: 1px solid #e2e8f0 !important;
}
.cd-contact-card-bottom {
  border-top: 1px solid #f1f5f9;
  padding: 6px 12px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #fafafa;
  font-size: 11px;
  color: #64748b;
}

/* ── 9. Master Candidates Tab Modern Card + Sub-Card System (Rule 20) ── */
.cd-cand-toolbar-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 10px 14px;
  margin-bottom: 10px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
  box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03);
}
.cd-cand-toolbar-left {
  display: flex;
  align-items: center;
  gap: 12px;
  flex: 1 1 300px;
}
.cd-cand-title-avatar {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  background: #eff6ff;
  color: #009ef7;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 15px;
  flex-shrink: 0;
  border: 1px solid #dbeafe;
}
.cd-cand-title-wrap {
  display: flex;
  flex-direction: column;
  gap: 1px;
}
.cd-cand-title {
  font-size: 14.5px;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
  line-height: 1.2;
}
.cd-cand-subtitle {
  font-size: 11.5px;
  color: #64748b;
  margin: 0;
  line-height: 1.2;
}
.cd-cand-count-badge {
  background: #eff6ff;
  color: #1d4ed8;
  border: 1px solid #dbeafe;
  border-radius: 6px;
  padding: 1px 8px;
  font-size: 11px;
  font-weight: 600;
}
.cd-cand-toolbar-right {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
  flex-shrink: 0 !important;
}
.cd-cand-search-subcard {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
  background: #f8fafc !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 8px !important;
  padding: 0 10px 0 12px !important;
  height: 36px !important;
  width: 280px !important;
  position: relative !important;
}
.cd-cand-search-subcard:focus-within {
  background: #ffffff;
  border-color: #009ef7;
  box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12);
}
.cd-cand-search-subcard i.fa-search {
  color: #94a3b8;
  font-size: 12px;
}
.cd-cand-search-subcard input {
  border: none !important;
  background: transparent !important;
  outline: none !important;
  box-shadow: none !important;
  font-size: 12px !important;
  color: #0f172a !important;
  width: 100%;
  padding: 0 !important;
  line-height: 36px;
}
.cd-cand-search-clear {
  border: none;
  background: transparent;
  color: #94a3b8;
  cursor: pointer;
  padding: 2px 4px;
  font-size: 11px;
}
.cd-cand-search-clear:hover { color: #ef4444; }
.cd-cand-action-subcard {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  background: #f8fafc;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #64748b;
  cursor: pointer;
  transition: all 0.15s ease;
  padding: 0;
}
.cd-cand-action-subcard:hover {
  background: #eff6ff;
  color: #009ef7;
  border-color: #bfdbfe;
}
.cd-cand-tabs-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 6px 8px;
  margin-bottom: 12px;
  box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02);
}
.cd-cand-tabs-list {
  display: flex;
  align-items: center;
  gap: 6px;
  overflow-x: auto;
  scrollbar-width: none;
}
.cd-cand-tabs-list::-webkit-scrollbar { display: none; }
.cd-cand-subtab {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 7px;
  border: 1px solid #e2e8f0;
  background: #ffffff;
  color: #475569;
  font-size: 11.5px;
  font-weight: 600;
  cursor: pointer;
  white-space: nowrap;
  transition: all 0.15s ease;
}
.cd-cand-subtab-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 18px;
  height: 18px;
  padding: 0 5px;
  border-radius: 10px;
  font-size: 10px;
  font-weight: 700;
  background: #f1f5f9;
  color: #475569;
  border: 1px solid #e2e8f0;
}
.cd-cand-subtab.active {
  background: #009ef7 !important;
  color: #ffffff !important;
  border-color: #009ef7 !important;
  box-shadow: 0 2px 6px rgba(0, 158, 247, 0.28) !important;
}
.cd-cand-subtab.active .cd-cand-subtab-badge {
  background: rgba(255, 255, 255, 0.28) !important;
  color: #ffffff !important;
  border-color: transparent !important;
}
.cd-cand-stream-wrap {
  width: 100%;
  overflow-x: hidden;
}
.cd-cand-stream {
  display: flex;
  flex-direction: column;
  gap: 10px;
  width: 100%;
}

/* Individual Candidate Master Card (Card + Sub-Card Enterprise Standard) */
.cd-cand-card {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 10px !important;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
  transition: all 0.15s ease-in-out !important;
  overflow: hidden !important;
  width: 100% !important;
  max-width: 100% !important;
}
.cd-cand-card:hover {
  border-color: #cbd5e1 !important;
  box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06) !important;
  transform: translateY(-1px) !important;
}

/* 6 Discrete Sub-Cards Grid */
.cd-cand-card-grid {
  display: grid !important;
  grid-template-columns: minmax(0, 1.85fr) minmax(0, 1.35fr) minmax(0, 1.25fr) minmax(0, 2.35fr) minmax(0, 1.55fr) minmax(0, 1.25fr) !important;
  gap: 8px !important;
  padding: 10px 12px !important;
  width: 100% !important;
  max-width: 100% !important;
  align-items: stretch !important;
}

/* Discrete Sub-Card Container */
.cd-cand-subcard {
  background: #f8fafc !important;
  border: 1px solid #edf2f7 !important;
  border-radius: 8px !important;
  padding: 7px 9px !important;
  display: flex !important;
  flex-direction: column !important;
  justify-content: space-between !important;
  min-width: 0 !important;
  overflow: hidden !important;
}
.cd-cand-subcard-hdr {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  margin-bottom: 5px !important;
  gap: 4px !important;
  line-height: 1 !important;
}
.cd-cand-subcard-label {
  font-size: 9.5px !important;
  font-weight: 700 !important;
  text-transform: uppercase !important;
  letter-spacing: 0.3px !important;
  color: #64748b !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
}
.cd-cand-subcard-body {
  display: flex !important;
  flex-direction: column !important;
  justify-content: center !important;
  gap: 2.5px !important;
  flex: 1 1 auto !important;
  min-width: 0 !important;
  overflow: hidden !important;
}

/* Sub-Card 1: Profile */
.cd-cand-profile-row {
  display: block !important;
  width: 100% !important;
  min-width: 0 !important;
}
.cd-cand-profile-meta {
  display: block !important;
  width: 100% !important;
  min-width: 0 !important;
  overflow: hidden !important;
}
.cd-cand-name {
  font-size: 13px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
  line-height: 1.25 !important;
  display: block !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
  white-space: nowrap !important;
  text-decoration: none !important;
  cursor: default !important;
  user-select: text !important;
}
.cd-cand-desig {
  font-size: 11px !important;
  font-weight: 500 !important;
  color: #475569 !important;
  line-height: 1.2 !important;
  margin-top: 1px !important;
  display: block !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
  white-space: nowrap !important;
}
.cd-cand-employer {
  font-size: 10.5px !important;
  color: #64748b !important;
  line-height: 1.2 !important;
  margin-top: 2px !important;
  display: block !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
  white-space: nowrap !important;
}

/* Sub-Card 2: Comp & Exp */
.cd-val-salary {
  font-size: 12px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
  line-height: 1.3 !important;
}
.cd-val-exp {
  font-size: 10.5px !important;
  font-weight: 500 !important;
  color: #475569 !important;
  margin-top: 2px !important;
  display: flex !important;
  align-items: center !important;
  gap: 4px !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
  line-height: 1.3 !important;
}
.cd-val-notice {
  font-size: 10.5px !important;
  font-weight: 600 !important;
  margin-top: 2px !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
  line-height: 1.3 !important;
}
.cd-notice-clean {
  font-weight: 600 !important;
  background: transparent !important;
  border: none !important;
  padding: 0 !important;
}
.cd-notice-immediate { color: #16a34a !important; }
.cd-notice-snp { color: #7c3aed !important; }
.cd-notice-days { color: #d97706 !important; }
.cd-notice-default { color: #475569 !important; }

/* Sub-Card 3: Location & Degree */
.cd-loc-val {
  font-size: 11.5px !important;
  font-weight: 600 !important;
  color: #0f172a !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
  line-height: 1.3 !important;
}
.cd-loc-mode {
  font-size: 10.5px !important;
  color: #64748b !important;
  margin-top: 2px !important;
  display: flex !important;
  align-items: center !important;
  gap: 4px !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
  line-height: 1.3 !important;
}
.cd-loc-edu {
  font-size: 10.5px !important;
  color: #64748b !important;
  margin-top: 2px !important;
  display: flex !important;
  align-items: center !important;
  gap: 4px !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
  line-height: 1.3 !important;
}

/* Sub-Card 4: Target Requisition */
.cd-header-badges {
  display: inline-flex !important;
  align-items: center !important;
  gap: 4px !important;
  flex-shrink: 0 !important;
}
.cd-badge-client-type {
  font-size: 8.5px !important;
  font-weight: 700 !important;
  padding: 1px 6px !important;
  border-radius: 4px !important;
  text-transform: uppercase !important;
  letter-spacing: 0.2px !important;
  line-height: 1.3 !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 3px !important;
  white-space: nowrap !important;
  flex-shrink: 0 !important;
}
.cd-badge-client-type.type-direct {
  background: #ecfdf5 !important;
  color: #059669 !important;
  border: 1px solid #a7f3d0 !important;
}
.cd-badge-client-type.type-internal {
  background: #eff6ff !important;
  color: #2563eb !important;
  border: 1px solid #bfdbfe !important;
}
.cd-status-badge {
  font-size: 8.5px !important;
  font-weight: 700 !important;
  padding: 1px 6px !important;
  border-radius: 4px !important;
  text-transform: uppercase !important;
  letter-spacing: 0.2px !important;
  line-height: 1.3 !important;
  flex-shrink: 0 !important;
}
.status-hiring   { background: #dcfce7 !important; color: #15803d !important; }
.status-hold     { background: #fef3c7 !important; color: #b45309 !important; }
.status-closed   { background: #fee2e2 !important; color: #b91c1c !important; }
.status-inactive { background: #f1f5f9 !important; color: #64748b !important; }

.cd-job-row {
  display: flex !important;
  align-items: center !important;
  gap: 7px !important;
  min-width: 0 !important;
}
.cd-job-logo-wrap {
  width: 28px !important;
  height: 28px !important;
  min-width: 28px !important;
  border-radius: 6px !important;
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  overflow: hidden !important;
  flex-shrink: 0 !important;
}
.cd-job-logo-img {
  width: 100% !important;
  height: 100% !important;
  object-fit: contain !important;
  display: block !important;
}
.cd-job-logo-fallback {
  width: 100% !important;
  height: 100% !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  background: #eff6ff !important;
  color: #009ef7 !important;
  font-size: 11px !important;
  font-weight: 700 !important;
}
.cd-job-meta-wrap {
  flex: 1 1 auto !important;
  min-width: 0 !important;
  overflow: hidden !important;
}
.cd-job-title-link {
  font-size: 12px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
  line-height: 1.25 !important;
  display: block !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
  white-space: nowrap !important;
  text-decoration: none !important;
  transition: color 0.12s ease !important;
}
.cd-job-title-link:hover {
  color: #009ef7 !important;
}
.cd-job-submeta {
  font-size: 10px !important;
  color: #64748b !important;
  line-height: 1.2 !important;
  margin-top: 1px !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
}
.cd-job-footer-line {
  font-size: 10px !important;
  color: #64748b !important;
  line-height: 1.2 !important;
  margin-top: 2px !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
}

/* Sub-Card 5: Recruiter & Sourcing */
.cd-recruiter-name {
  font-size: 11.5px !important;
  font-weight: 600 !important;
  color: #0f172a !important;
  line-height: 1.3 !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
}
.cd-recruiter-company {
  font-size: 10.5px !important;
  color: #64748b !important;
  margin-top: 2px !important;
  line-height: 1.3 !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
}
.cd-recruiter-time {
  font-size: 10px !important;
  color: #94a3b8 !important;
  margin-top: 2px !important;
  line-height: 1.3 !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
}

/* Sub-Card 6: Pipeline & Stage */
.cd-stage-chip {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  gap: 4px !important;
  padding: 2px 7px !important;
  border-radius: 6px !important;
  font-size: 10.5px !important;
  font-weight: 600 !important;
  line-height: 1.3 !important;
  text-decoration: none !important;
  cursor: default !important;
  pointer-events: none !important;
  user-select: none !important;
  max-width: 100% !important;
  overflow: hidden !important;
  white-space: nowrap !important;
}
.cd-stage-sub {
  font-size: 10.5px !important;
  color: #64748b !important;
  margin-top: 2px !important;
  line-height: 1.2 !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
}
.cd-stage-action {
  font-size: 10.5px !important;
  line-height: 1.2 !important;
  margin-top: 2px !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
}
.cd-action-link {
  color: #009ef7 !important;
  font-weight: 600 !important;
  text-decoration: none !important;
}
.cd-action-link:hover {
  color: #007bb5 !important;
  text-decoration: underline !important;
}

/* Sub-Card 7: Full-Width Intelligence Strip */
.cd-cand-bottom-strip {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  gap: 10px !important;
  padding: 6px 12px !important;
  background: #ffffff !important;
  border-top: 1px solid #f1f5f9 !important;
  width: 100% !important;
  overflow: hidden !important;
}
.cd-cand-bottom-left {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
  min-width: 0 !important;
  flex: 1 1 auto !important;
  overflow: hidden !important;
}
.cd-cand-bottom-right {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
  flex-shrink: 0 !important;
  white-space: nowrap !important;
}
.cd-bottom-skills-scroll {
  display: flex !important;
  align-items: center !important;
  gap: 4px !important;
  overflow-x: auto !important;
  overflow-y: hidden !important;
  scrollbar-width: none !important;
  -ms-overflow-style: none !important;
  white-space: nowrap !important;
  min-width: 0 !important;
}
.cd-bottom-skills-scroll::-webkit-scrollbar {
  display: none !important;
}

/* CV Tracker Match % & Skills Pill Styles */
.cd-match-badge-cv {
  display: inline-flex !important;
  align-items: center !important;
  gap: 3px !important;
  padding: 1.5px 7px !important;
  border-radius: 4px !important;
  font-size: 10px !important;
  font-weight: 700 !important;
  line-height: 1.3 !important;
  white-space: nowrap !important;
  flex-shrink: 0 !important;
}
.cd-match-green {
  background: #dcfce7 !important;
  color: #15803d !important;
  border: 1px solid #bbf7d0 !important;
}
.cd-match-amber {
  background: #fef3c7 !important;
  color: #b45309 !important;
  border: 1px solid #fde68a !important;
}
.cd-match-red {
  background: #fee2e2 !important;
  color: #dc2626 !important;
  border: 1px solid #fecdd3 !important;
}

.cd-skill-pill-cv {
  display: inline-flex !important;
  align-items: center !important;
  padding: 1px 7px !important;
  border-radius: 4px !important;
  background: #f8fafc !important;
  color: #334155 !important;
  font-size: 10px !important;
  font-weight: 500 !important;
  border: 1px solid #e2e8f0 !important;
  white-space: nowrap !important;
  line-height: 1.4 !important;
  flex-shrink: 0 !important;
}

/* Dynamic LinkedIn & Visa Status Badges */
.cd-cand-linkedin-pill {
  display: inline-flex !important;
  align-items: center !important;
  gap: 4px !important;
  padding: 1.5px 8px !important;
  border-radius: 4px !important;
  background: #eff6ff !important;
  color: #1d4ed8 !important;
  border: 1px solid #bfdbfe !important;
  font-size: 10px !important;
  font-weight: 600 !important;
  text-decoration: none !important;
  line-height: 1.4 !important;
  white-space: nowrap !important;
  flex-shrink: 0 !important;
  transition: all 0.15s ease !important;
}
.cd-cand-linkedin-pill:hover {
  background: #dbeafe !important;
  color: #1e40af !important;
  border-color: #93c5fd !important;
}
.cd-cand-visa-pill {
  display: inline-flex !important;
  align-items: center !important;
  gap: 4px !important;
  padding: 1.5px 8px !important;
  border-radius: 4px !important;
  background: #f8fafc !important;
  color: #334155 !important;
  border: 1px solid #e2e8f0 !important;
  font-size: 10px !important;
  font-weight: 600 !important;
  line-height: 1.4 !important;
  white-space: nowrap !important;
  flex-shrink: 0 !important;
}

/* Responsive Scaling for 15.6" Laptops (1366x768 & 1920x1080) and Smaller */
@media (max-width: 1440px) {
  .cd-cand-card-grid {
    grid-template-columns: minmax(0, 1.9fr) minmax(0, 1.3fr) minmax(0, 1.2fr) minmax(0, 2.2fr) minmax(0, 1.45fr) minmax(0, 1.2fr) !important;
    gap: 6px !important;
    padding: 8px 10px !important;
  }
  .cd-cand-subcard {
    padding: 6px 8px !important;
  }
}
@media (max-width: 1200px) {
  .cd-cand-card-grid {
    grid-template-columns: minmax(0, 1.8fr) minmax(0, 1.25fr) minmax(0, 1.15fr) minmax(0, 2fr) minmax(0, 1.4fr) minmax(0, 1.15fr) !important;
    gap: 6px !important;
    padding: 8px 10px !important;
  }
}
@media (max-width: 992px) {
  .cd-cand-card-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 8px !important;
  }
  .cd-cand-bottom-strip {
    flex-direction: column !important;
    align-items: flex-start !important;
    gap: 6px !important;
  }
}
@media (max-width: 640px) {
  .cd-cand-card-grid {
    grid-template-columns: 1fr !important;
    gap: 8px !important;
  }
}

/* ── 10. Master Performance Tab Modern Dashboard System ── */
.cd-perf-wrap {
  padding: 14px 18px 24px;
  background: #f8fafc;
}
.cd-perf-header {
  background: #ffffff;
  border: 1px solid #eef2f6;
  border-radius: 8px;
  padding: 10px 14px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
  flex-wrap: wrap;
  gap: 10px;
}
.cd-perf-header-left {
  display: flex;
  align-items: center;
  gap: 10px;
}
.cd-perf-icon-box {
  width: 34px;
  height: 34px;
  border-radius: 8px;
  background: #ecfdf5;
  color: #10b981;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px !important;
  flex-shrink: 0;
  border: 1px solid #a7f3d0;
}
.cd-perf-title {
  font-size: 14px !important;
  font-weight: 600 !important;
  color: #0f172a !important;
  line-height: 1.2;
}
.cd-perf-subtitle {
  font-size: 11.5px !important;
  font-weight: 400 !important;
  color: #64748b !important;
  margin-top: 2px;
  line-height: 1.2;
}
.cd-perf-header-right {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}
.cd-perf-select-wrap {
  display: flex;
  align-items: center;
  gap: 6px;
}
.cd-perf-select-lbl {
  font-size: 11.5px !important;
  font-weight: 400 !important;
  color: #64748b;
  white-space: nowrap;
}
.cd-perf-select {
  height: 30px !important;
  padding: 0 24px 0 10px !important;
  font-size: 11.5px !important;
  font-weight: 500 !important;
  color: #1e293b !important;
  background-color: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 6px !important;
  cursor: pointer;
  outline: none;
  transition: border-color 0.15s ease-in-out;
}
.cd-perf-select:focus {
  border-color: #10b981 !important;
}

/* KPI 5-Card Row */
.cd-perf-kpi-grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 10px;
  margin-bottom: 12px;
}
.cd-perf-kpi-card {
  background: #ffffff;
  border: 1px solid #eef2f6;
  border-radius: 8px;
  padding: 10px 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
  transition: all 0.15s ease-in-out;
  cursor: pointer;
}
.cd-perf-kpi-card:hover {
  transform: translateY(-2px);
  border-color: #cbd5e1;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}
.cd-perf-kpi-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.cd-perf-kpi-icon {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px !important;
  flex-shrink: 0;
}
.cd-perf-kpi-icon-green  { background: #ecfdf5; color: #10b981; border: 1.5px solid #d1fae5; }
.cd-perf-kpi-icon-blue   { background: #eff6ff; color: #2563eb; border: 1.5px solid #bfdbfe; }
.cd-perf-kpi-icon-amber  { background: #fffbeb; color: #d97706; border: 1.5px solid #fde68a; }
.cd-perf-kpi-icon-purple { background: #f5f3ff; color: #7c3aed; border: 1.5px solid #ddd6fe; }
.cd-perf-kpi-icon-cyan   { background: #f0f9ff; color: #0284c7; border: 1.5px solid #bae6fd; }

.cd-perf-pill {
  font-size: 10px !important;
  font-weight: 500 !important;
  padding: 2px 7px;
  border-radius: 12px;
  display: inline-flex;
  align-items: center;
  gap: 3px;
  line-height: 1.2;
}
.cd-perf-pill-up   { background: #ecfdf5; color: #10b981; }
.cd-perf-pill-down { background: #fef2f2; color: #ef4444; }
.cd-perf-pill-good { background: #ecfdf5; color: #10b981; }

.cd-perf-kpi-num {
  font-size: 14px !important;
  font-weight: 700 !important;
  color: #0f172a;
  line-height: 1.2;
  margin-top: 6px;
}
.cd-perf-kpi-lbl {
  font-size: 12px !important;
  font-weight: 500 !important;
  color: #1e293b;
  margin-top: 2px;
  line-height: 1.2;
}
.cd-perf-kpi-sub {
  font-size: 11px !important;
  font-weight: 400 !important;
  color: #64748b;
  margin-top: 3px;
  line-height: 1.2;
}
.cd-perf-kpi-comp {
  font-size: 10.5px !important;
  font-weight: 400 !important;
  color: #94a3b8;
  margin-top: 1px;
  line-height: 1.2;
}

/* Row 1, Row 2, Row 3 Layouts */
.cd-perf-row-1 {
  display: grid;
  grid-template-columns: 1fr 1.6fr 1.4fr;
  gap: 12px;
  margin-bottom: 12px;
}
.cd-perf-row-2 {
  display: grid;
  grid-template-columns: 1.3fr 1.3fr 1.4fr;
  gap: 12px;
  margin-bottom: 12px;
}
.cd-perf-row-3 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  margin-bottom: 12px;
}

/* Card Panel Structure */
.cd-perf-card {
  background: #ffffff;
  border: 1px solid #eef2f6;
  border-radius: 8px;
  padding: 12px 14px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
  display: flex;
  flex-direction: column;
}
.cd-perf-card-hdr {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 8px;
  padding-bottom: 6px;
  border-bottom: 1px solid #f8fafc;
  gap: 8px;
}
.cd-perf-card-title {
  font-size: 12.5px !important;
  font-weight: 600 !important;
  color: #0f172a;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}
.cd-perf-card-title i {
  font-size: 12.5px !important;
}
.cd-perf-card-mini-select {
  height: 26px !important;
  padding: 0 18px 0 6px !important;
  font-size: 10.5px !important;
  font-weight: 500 !important;
  color: #475569 !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 4px !important;
  background: #ffffff;
  cursor: pointer;
  outline: none;
}
.cd-perf-legend {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 6px;
  font-size: 10.5px !important;
  font-weight: 400 !important;
  color: #64748b;
  flex-wrap: wrap;
}
.cd-perf-legend-item {
  display: inline-flex;
  align-items: center;
  gap: 4px;
}
.cd-perf-legend-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  display: inline-block;
}
.cd-perf-legend-sq {
  width: 7px;
  height: 7px;
  border-radius: 2px;
  display: inline-block;
}

/* Funnel Components */
.cd-funnel-wrap {
  display: flex;
  align-items: center;
  gap: 14px;
  flex: 1;
  padding: 4px 0;
}
.cd-funnel-svg {
  width: 92px;
  height: 132px;
  flex-shrink: 0;
  filter: drop-shadow(0 2px 4px rgba(0,0,0,0.04));
}
.cd-funnel-list {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.cd-funnel-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 11.5px !important;
  gap: 8px;
}
.cd-funnel-stage {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  color: #475569;
  font-weight: 400;
}
.cd-funnel-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  flex-shrink: 0;
}
.cd-funnel-val {
  font-weight: 600 !important;
  color: #0f172a;
  min-width: 18px;
  font-size: 12px !important;
}
.cd-funnel-pct {
  font-weight: 500 !important;
  color: #64748b;
  min-width: 32px;
  text-align: right;
  font-size: 11.5px !important;
}

/* Donut Job Type Components */
.cd-donut-wrap {
  display: flex;
  align-items: center;
  gap: 14px;
  flex: 1;
  padding: 4px 0;
}
.cd-donut-chart-box {
  position: relative;
  width: 110px;
  height: 110px;
  flex-shrink: 0;
}
.cd-donut-center {
  position: absolute;
  inset: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  pointer-events: none;
}
.cd-donut-center-num {
  font-size: 14px !important;
  font-weight: 700 !important;
  color: #0f172a;
  line-height: 1;
}
.cd-donut-center-lbl {
  font-size: 10px !important;
  font-weight: 400 !important;
  color: #94a3b8;
  line-height: 1;
  margin-top: 2px;
}
.cd-donut-list {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 7px;
}
.cd-donut-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 11.5px !important;
  gap: 6px;
}
.cd-donut-item-left {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  color: #334155;
  font-weight: 400;
}
.cd-donut-item-count {
  font-weight: 600 !important;
  color: #0f172a;
  font-size: 12px !important;
}
.cd-donut-item-pct {
  font-weight: 500 !important;
  color: #64748b;
  min-width: 28px;
  text-align: right;
  font-size: 11.5px !important;
}

/* Tables */
.cd-perf-table {
  width: 100%;
  border-collapse: collapse;
}
.cd-perf-table th {
  font-size: 11px !important;
  font-weight: 500 !important;
  color: #64748b;
  padding: 7px 6px;
  border-bottom: 1.5px solid #f1f5f9;
  text-align: right;
}
.cd-perf-table th:first-child,
.cd-perf-table th:nth-child(2) {
  text-align: left;
}
.cd-perf-table td {
  font-size: 11.5px !important;
  color: #334155;
  padding: 7px 6px;
  border-bottom: 1px dashed #f1f5f9;
  text-align: right;
  vertical-align: middle;
}
.cd-perf-table tr:last-child td {
  border-bottom: none;
}
.cd-perf-table td:first-child,
.cd-perf-table td:nth-child(2) {
  text-align: left;
}
.cd-perf-table td:first-child {
  color: #94a3b8;
  font-weight: 500;
  width: 18px;
}
.cd-perf-table td:nth-child(2) {
  font-weight: 500 !important;
  color: #0f172a;
}
.cd-perf-progress-wrap {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  justify-content: flex-end;
}
.cd-perf-progress-bar {
  width: 48px;
  height: 5px;
  background: #f1f5f9;
  border-radius: 4px;
  overflow: hidden;
  position: relative;
}
.cd-perf-progress-fill {
  height: 100%;
  background: #10b981;
  border-radius: 4px;
}
.cd-recruiter-avatar {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 8.5px !important;
  font-weight: 600 !important;
  margin-right: 6px;
  vertical-align: middle;
}

/* Additional Intelligence & Insights */
.cd-perf-intelligence-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 10px;
  margin-bottom: 12px;
}
.cd-perf-intel-card {
  background: #ffffff;
  border: 1px solid #eef2f6;
  border-radius: 8px;
  padding: 10px 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}
.cd-perf-intel-lbl {
  font-size: 11px !important;
  font-weight: 500 !important;
  color: #64748b;
}
.cd-perf-intel-val {
  font-size: 14px !important;
  font-weight: 600 !important;
  color: #0f172a;
  margin-top: 3px;
}
.cd-perf-intel-sub {
  font-size: 10.5px !important;
  font-weight: 400 !important;
  color: #94a3b8;
  margin-top: 2px;
}

.cd-perf-insights-card {
  background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
  border: 1px solid #a7f3d0;
  border-radius: 8px;
  padding: 12px 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}
.cd-perf-insights-hdr {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12.5px !important;
  font-weight: 600 !important;
  color: #065f46;
  margin-bottom: 8px;
}
.cd-perf-insights-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 6px 16px;
  font-size: 11.5px !important;
  font-weight: 400 !important;
  color: #334155;
  line-height: 1.5;
}

@media (max-width: 1280px) {
  .cd-perf-row-1, .cd-perf-row-2 {
    grid-template-columns: 1fr;
  }
  .cd-perf-row-3 {
    grid-template-columns: 1fr;
  }
  .cd-perf-kpi-grid {
    grid-template-columns: repeat(3, 1fr);
  }
  .cd-perf-intelligence-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .cd-perf-insights-grid {
    grid-template-columns: 1fr;
  }
}
@media (max-width: 768px) {
  .cd-perf-kpi-grid {
    grid-template-columns: 1fr 1fr;
  }
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
                <div class="cd-kpi-num" id="agencyKpiAllJobs">{{ number_format($kpiTotalJobs) }}</div>
                <div class="cd-kpi-lbl">All Shared Jobs</div>
            </div>
        </a>

        <!-- KPI 2: All Active Jobs -->
        <a href="#requisitions" class="cd-kpi-card cd-kpi-nav-trigger" onclick="triggerDrawerTab('requisitions', event)" title="Active jobs currently being sourced">
            <div class="cd-kpi-icon cd-kpi-icon-emerald"><i class="fas fa-bolt"></i></div>
            <div class="cd-kpi-body">
                <div class="cd-kpi-num" id="agencyKpiActiveJobs">{{ number_format($kpiActiveJobs) }}</div>
                <div class="cd-kpi-lbl">All Active Jobs</div>
            </div>
        </a>

        <!-- KPI 3: All Candidates -->
        <a href="#candidate_list" class="cd-kpi-card cd-kpi-nav-trigger" onclick="triggerDrawerTab('candidate_list', event)" title="Total candidate profiles submitted">
            <div class="cd-kpi-icon cd-kpi-icon-purple"><i class="fas fa-users"></i></div>
            <div class="cd-kpi-body">
                <div class="cd-kpi-num" id="agencyKpiCandidates">{{ number_format($kpiSubmitted) }}</div>
                <div class="cd-kpi-lbl">All Candidates</div>
            </div>
        </a>

        <!-- KPI 4: All Interviews -->
        <a href="#interview_tracker" class="cd-kpi-card cd-kpi-nav-trigger" onclick="triggerDrawerTab('interview_tracker', event)" title="Interviews conducted for agency candidates">
            <div class="cd-kpi-icon cd-kpi-icon-amber"><i class="fas fa-calendar-check"></i></div>
            <div class="cd-kpi-body">
                <div class="cd-kpi-num" id="agencyKpiInterviews">{{ number_format($kpiInterviews) }}</div>
                <div class="cd-kpi-lbl">All Interviews</div>
            </div>
        </a>

        <!-- KPI 5: All Offers -->
        <a href="#candidate_joining" class="cd-kpi-card cd-kpi-nav-trigger" onclick="triggerDrawerTab('candidate_joining', event)" title="Offers extended to agency candidates">
            <div class="cd-kpi-icon cd-kpi-icon-rose"><i class="fas fa-file-signature"></i></div>
            <div class="cd-kpi-body">
                <div class="cd-kpi-num" id="agencyKpiOffers">{{ number_format($kpiOffers) }}</div>
                <div class="cd-kpi-lbl">All Offers</div>
            </div>
        </a>

        <!-- KPI 6: All Joined -->
        <a href="#candidate_joining" class="cd-kpi-card cd-kpi-nav-trigger" onclick="triggerDrawerTab('candidate_joining', event)" title="Candidates successfully joined">
            <div class="cd-kpi-icon cd-kpi-icon-teal"><i class="fas fa-handshake"></i></div>
            <div class="cd-kpi-body">
                <div class="cd-kpi-num" id="agencyKpiJoins">{{ number_format($kpiJoins) }}</div>
                <div class="cd-kpi-lbl">All Joined</div>
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
                    <span class="cd-tab-badge-pill" id="agencyTabBadgeJobs">{{ $kpiTotalJobs }}</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="cd-tab-pill-btn nav-link" data-bs-toggle="tab" data-toggle="tab" href="#candidate_list" role="tab" aria-selected="false">
                    <i class="fas fa-users"></i>
                    <span>Candidates</span>
                    <span class="cd-tab-badge-pill" id="agencyTabBadgeCandidates">{{ $kpiSubmitted }}</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="cd-tab-pill-btn nav-link" data-bs-toggle="tab" data-toggle="tab" href="#interview_tracker" role="tab" aria-selected="false">
                    <i class="fas fa-calendar-days"></i>
                    <span>Interviews</span>
                    <span class="cd-tab-badge-pill" id="agencyTabBadgeInterviews">{{ $kpiInterviews }}</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="cd-tab-pill-btn nav-link" data-bs-toggle="tab" data-toggle="tab" href="#candidate_joining" role="tab" aria-selected="false">
                    <i class="fas fa-file-signature"></i>
                    <span>Offers &amp; Joins</span>
                    <span class="cd-tab-badge-pill" id="agencyTabBadgeOffersJoins">{{ $kpiOffersTotal }}</span>
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
             TAB 1: OVERVIEW (Rule 20 Enterprise Card + Sub-Card Standard)
             ══════════════════════════════════════════════════════════════════════ -->
        <div class="tab-pane active show" id="overview" role="tabpanel">
            <div class="cd-overview-grid">
                <!-- Left Column -->
                <div>
                    <!-- Sub-Card 1: About Agency -->
                    <div class="cd-master-subcard">
                        <div class="cd-subcard-header">
                            <div class="cd-subcard-header-left">
                                <div class="cd-subcard-header-icon blue"><i class="fas fa-info-circle"></i></div>
                                <h4 class="cd-subcard-heading">About {{ $detailName }}</h4>
                            </div>
                            @if (!empty($webClean) && $webClean !== '-')
                                @php
                                    $cleanWebLink = preg_replace('#^https?://(www\.)?#i', '', rtrim($webClean, '/'));
                                    $webHref = str_starts_with($webClean, 'http') ? $webClean : 'https://'.$webClean;
                                @endphp
                                <a href="{{ $webHref }}" target="_blank" class="text-primary fw-bold fs-8 text-decoration-none d-inline-flex align-items-center gap-1" title="Visit {{ $detailName }} Website">
                                    {{ $cleanWebLink }} <i class="fas fa-arrow-up-right-from-square fs-9"></i>
                                </a>
                            @endif
                        </div>
                        <div style="font-size: 11.5px; color: #475569; line-height: 1.5; margin-bottom: 0;">
                            @php
                                $agencyDescClean = trim(strip_tags((string)($agency_description ?? ($directClientDetail->description ?? ($directClientDetail->companies_description ?? '')))));
                            @endphp
                            @if(!empty($agencyDescClean))
                                {!! !empty($agency_description) ? $agency_description : ($directClientDetail->description ?? $directClientDetail->companies_description) !!}
                            @else
                                {{ $detailName }} is a verified enterprise talent acquisition partner specialized in sourcing top-tier technology, business operations, and leadership talent. Authorized to submit qualified candidate profiles across shared job requisitions under active SLA guarantees.
                            @endif
                        </div>
                    </div>

                    <!-- Sub-Card 2: Dedicated Agency Contacts (Sub-Card Layout) -->
                    <div class="cd-master-subcard">
                        <div class="cd-subcard-header">
                            <div class="cd-subcard-header-left">
                                <div class="cd-subcard-header-icon purple"><i class="fas fa-address-book"></i></div>
                                <h4 class="cd-subcard-heading">Contacts</h4>
                            </div>
                            @if(isset($agencyContacts) && count($agencyContacts) > 0)
                                <span class="badge bg-light-primary text-primary fw-semibold" style="font-size:10.5px; padding:3px 8px; border-radius:12px;">
                                    <i class="fas fa-users me-1"></i>{{ count($agencyContacts) }} {{ count($agencyContacts) === 1 ? 'Contact' : 'Contacts' }}
                                </span>
                            @endif
                        </div>
                        <div class="cd-contact-list-group">
                            @if(isset($agencyContacts) && count($agencyContacts) > 0)
                                @foreach($agencyContacts as $contact)
                                    <div class="cd-contact-subcard">
                                        <div class="cd-contact-subcard-top">
                                            <div class="cd-contact-subcard-person">
                                                <div class="cd-contact-subcard-avatar">{{ $contact->initials ?? 'AG' }}</div>
                                                <div style="min-width: 0; flex: 1 1 auto;">
                                                    <div class="cd-contact-subcard-name">{{ $contact->name }}</div>
                                                    @if(!empty($contact->designation))
                                                        <div class="cd-contact-subcard-role">{{ $contact->designation }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            @if(!empty($contact->linkedin))
                                                @php
                                                    $linkedInHref = str_starts_with($contact->linkedin, 'http') ? $contact->linkedin : 'https://'.$contact->linkedin;
                                                @endphp
                                                <a href="{{ $linkedInHref }}" target="_blank" class="cd-contact-subcard-linkedin" title="View LinkedIn Profile">
                                                    <i class="fab fa-linkedin-in"></i>
                                                </a>
                                            @endif
                                        </div>
                                        @if(!empty($contact->phone))
                                            <div class="cd-contact-subcard-phone-line">
                                                <i class="fas fa-phone text-success" style="font-size: 11px;"></i>
                                                <span class="text-gray-800 fw-semibold fs-8">{{ $contact->phone }}</span>
                                                <button type="button" class="cd-copy-btn ms-auto" title="Copy Phone" onclick="navigator.clipboard.writeText('{{ $contact->phone }}'); $(this).find('i').removeClass('fa-copy').addClass('fa-check text-success'); setTimeout(() => $(this).find('i').removeClass('fa-check text-success').addClass('fa-copy'), 1500);">
                                                    <i class="far fa-copy"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="text-muted fs-8 py-2 text-center">No additional contact persons registered.</div>
                            @endif
                        </div>
                    </div>

                    <!-- Sub-Card 3: Recent Touchpoint Activity Preview -->
                    <div class="cd-master-subcard">
                        <div class="cd-subcard-header">
                            <div class="cd-subcard-header-left">
                                <div class="cd-subcard-header-icon amber"><i class="fas fa-clock"></i></div>
                                <h4 class="cd-subcard-heading">Recent Touchpoint Activity</h4>
                            </div>
                            <a href="#recent_activity" onclick="triggerDrawerTab('recent_activity', event)" class="text-primary fw-bold fs-8 text-decoration-none d-inline-flex align-items-center gap-1">
                                View Activity Log <i class="fas fa-arrow-right fs-9"></i>
                            </a>
                        </div>
                        <div class="cd-activity-timeline">
                            <div class="cd-act-card" style="padding: 10px 12px; margin-bottom: 8px;">
                                <div class="cd-act-avatar" style="width:30px; height:30px; font-size:12px; background:#ecfdf5; color:#10b981; border:1px solid #a7f3d0;"><i class="fas fa-handshake"></i></div>
                                <div style="min-width:0; flex:1;">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="fw-bold text-dark fs-8">Agency Verified &amp; Connected</span>
                                        <span class="text-muted fs-9">{{ $partner_joined_date ?? date('d M, Y') }}</span>
                                    </div>
                                    <div class="text-muted fs-9">Mutual staffing partnership active with authorized candidate delivery credentials.</div>
                                </div>
                            </div>
                            <div class="cd-act-card" style="padding: 10px 12px; margin-bottom: 8px;">
                                <div class="cd-act-avatar" style="width:30px; height:30px; font-size:12px; background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe;"><i class="fas fa-briefcase"></i></div>
                                <div style="min-width:0; flex:1;">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="fw-bold text-dark fs-8">{{ $kpiTotalJobs }} Shared Job Requisitions</span>
                                        <span class="badge bg-light-primary text-primary fw-bold fs-9">{{ $kpiActiveJobs }} Active</span>
                                    </div>
                                    <div class="text-muted fs-9">Agency actively authorized to source and submit qualified candidate profiles.</div>
                                </div>
                            </div>
                            <div class="cd-act-card" style="padding: 10px 12px;">
                                <div class="cd-act-avatar" style="width:30px; height:30px; font-size:12px; background:#faf5ff; color:#7c3aed; border:1px solid #e9d5ff;"><i class="fas fa-users"></i></div>
                                <div style="min-width:0; flex:1;">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="fw-bold text-dark fs-8">{{ $kpiSubmitted }} Candidates Submitted</span>
                                        <span class="text-muted fs-9">{{ $kpiJoins }} Joined</span>
                                    </div>
                                    <div class="text-muted fs-9">Active delivery pipeline spanning {{ $kpiInterviews }} interview rounds and {{ $kpiOffers }} offers released.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div>
                    <!-- Sub-Card 4: Agency Specifications & Metadata (Enterprise Vertical Info Layout) -->
                    <div class="cd-master-subcard">
                        <div class="cd-subcard-header">
                            <div class="cd-subcard-header-left">
                                <div class="cd-subcard-header-icon purple"><i class="fas fa-building"></i></div>
                                <h4 class="cd-subcard-heading">Agency Specifications &amp; Metadata</h4>
                            </div>
                            <span class="badge bg-light-primary text-primary fw-bold" style="font-size:10px; padding:3px 7px;">
                                <i class="fas fa-id-badge me-1"></i>#AG-{{ $agencyId }}
                            </span>
                        </div>
                        <div class="cd-vertical-info-list">
                            <!-- 1. Legal Entity -->
                            <div class="cd-vinfo-row">
                                <div class="cd-vinfo-key">
                                    <div class="cd-vinfo-icon indigo"><i class="fas fa-building"></i></div>
                                    <span class="cd-vinfo-label">Legal Entity</span>
                                </div>
                                <div class="cd-vinfo-val">
                                    <span class="fw-semibold text-gray-900">{{ $detailName }}</span>
                                </div>
                            </div>

                            <!-- 2. Website (Hidden completely if empty) -->
                            @if (!empty($webClean) && $webClean !== '-')
                            <div class="cd-vinfo-row">
                                <div class="cd-vinfo-key">
                                    <div class="cd-vinfo-icon blue"><i class="fas fa-globe"></i></div>
                                    <span class="cd-vinfo-label">Website</span>
                                </div>
                                <div class="cd-vinfo-val">
                                    <a href="{{ str_starts_with($webClean, 'http') ? $webClean : 'https://'.$webClean }}" target="_blank" class="cd-vinfo-link">
                                        {{ $webClean }} <i class="fas fa-arrow-up-right-from-square" style="font-size:9px;"></i>
                                    </a>
                                </div>
                            </div>
                            @endif

                            <!-- 3. Tax ID / GSTN (Dynamic Visibility: Hidden completely if not available) -->
                            @if(!empty($agency_tax_id))
                            <div class="cd-vinfo-row">
                                <div class="cd-vinfo-key">
                                    <div class="cd-vinfo-icon amber"><i class="fas fa-receipt"></i></div>
                                    <span class="cd-vinfo-label">Tax ID / GSTN</span>
                                </div>
                                <div class="cd-vinfo-val">
                                    <div class="cd-tax-wrap">
                                        <span class="cd-tax-code" style="font-family: monospace; font-weight: 700; color: #1e293b; background: #f8fafc; border: 1px solid #e2e8f0; padding: 2px 7px; border-radius: 4px; font-size: 11px;">{{ $agency_tax_id }}</span>
                                        <button type="button" class="cd-copy-btn" title="Copy Tax ID" onclick="navigator.clipboard.writeText('{{ $agency_tax_id }}'); $(this).find('i').removeClass('fa-copy').addClass('fa-check text-success'); setTimeout(() => $(this).find('i').removeClass('fa-check text-success').addClass('fa-copy'), 1500);">
                                            <i class="far fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- 4. Industry Sector -->
                            <div class="cd-vinfo-row">
                                <div class="cd-vinfo-key">
                                    <div class="cd-vinfo-icon purple"><i class="fas fa-industry"></i></div>
                                    <span class="cd-vinfo-label">Industry Sector</span>
                                </div>
                                <div class="cd-vinfo-val">
                                    <span class="fw-semibold text-gray-800">{{ $industries_name ?? 'Information Technology & Services' }}</span>
                                </div>
                            </div>

                            <!-- 5. Company Type -->
                            <div class="cd-vinfo-row">
                                <div class="cd-vinfo-key">
                                    <div class="cd-vinfo-icon sky"><i class="fas fa-landmark"></i></div>
                                    <span class="cd-vinfo-label">Company Type</span>
                                </div>
                                <div class="cd-vinfo-val">
                                    <span class="cd-type-badge-pill">{{ $company_type_name ?? 'Corporate Entity' }}</span>
                                </div>
                            </div>

                            <!-- 6. Workforce Scale -->
                            <div class="cd-vinfo-row">
                                <div class="cd-vinfo-key">
                                    <div class="cd-vinfo-icon emerald"><i class="fas fa-users"></i></div>
                                    <span class="cd-vinfo-label">Workforce Scale</span>
                                </div>
                                <div class="cd-vinfo-val">
                                    <span class="fw-semibold text-gray-800">{{ $agencyCompanySize ?? '10 - 50 Employees' }}</span>
                                </div>
                            </div>

                            <!-- 7. Founded Year & Vintage -->
                            <div class="cd-vinfo-row">
                                <div class="cd-vinfo-key">
                                    <div class="cd-vinfo-icon orange"><i class="fas fa-calendar-alt"></i></div>
                                    <span class="cd-vinfo-label">Founded</span>
                                </div>
                                <div class="cd-vinfo-val">
                                    @php
                                        $fYear = (int)($founded ?? ($directClientDetail->founded ?? 2019));
                                        $currYr = (int)date('Y');
                                        $vintage = $currYr - $fYear;
                                    @endphp
                                    <div>
                                        <span class="fw-semibold text-gray-800">{{ $fYear }}</span>
                                        @if($vintage > 0)
                                            <span class="cd-vintage-chip">({{ $vintage }} yrs active)</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sub-Card 5: Dedicated Locations Card (Multi-Location Sub-Card Engine) -->
                    <div class="cd-master-subcard">
                        <div class="cd-subcard-header">
                            <div class="cd-subcard-header-left">
                                <div class="cd-subcard-header-icon red"><i class="fas fa-location-dot"></i></div>
                                <h4 class="cd-subcard-heading">Locations</h4>
                            </div>
                            @if(isset($agencyLocations) && count($agencyLocations) > 0)
                                <span class="badge bg-light-primary text-primary fw-semibold" style="font-size:10.5px; padding:3px 8px; border-radius:12px;">
                                    <i class="fas fa-building me-1"></i>{{ count($agencyLocations) > 1 ? count($agencyLocations) . ' Locations' : (count($agencyLocations) === 1 ? '1 Location' : 'Headquarters') }}
                                </span>
                            @endif
                        </div>
                        <div class="cd-loc-group">
                            @if(isset($agencyLocations) && count($agencyLocations) > 0)
                                @foreach($agencyLocations as $idx => $loc)
                                    @php
                                        $isHq = ($idx === 0);
                                        $cityStateParts = array_filter([$loc->city ? ucwords(trim($loc->city)) : '', $loc->state ? ucwords(trim($loc->state)) : '']);
                                        $cityStateText = implode(', ', $cityStateParts);
                                    @endphp
                                    <div class="cd-loc-card {{ $isHq ? 'is-primary hq' : '' }}">
                                        <div class="cd-loc-header">
                                            <div class="cd-loc-header-left">
                                                <span class="cd-loc-badge {{ $isHq ? 'loc-primary' : 'loc-secondary' }}">
                                                    <i class="fas fa-map-marker-alt me-1"></i>{{ $isHq ? 'Headquarters' : 'Location ' . ($idx + 1) }}
                                                </span>
                                                @if($isHq)
                                                    <span class="cd-loc-hq-tag">HQ</span>
                                                @endif
                                            </div>
                                            @if(!empty($loc->country))
                                                <span class="cd-loc-country-pill">
                                                    <i class="fas fa-globe-americas me-1 text-primary" style="font-size:9.5px;"></i>{{ ucwords(trim($loc->country)) }}
                                                </span>
                                            @endif
                                        </div>
                                        @if(!empty($cityStateText))
                                            <div class="cd-loc-city-line">
                                                <span class="cd-loc-city-icon"><i class="fas fa-location-dot text-danger"></i></span>
                                                <span class="cd-loc-city-name">{{ $cityStateText }}</span>
                                            </div>
                                        @endif
                                        @if(!empty($loc->address))
                                            <div class="cd-loc-street-line">
                                                <span>{{ $loc->address }}</span>
                                            </div>
                                        @endif
                                        @if(!empty($loc->postcode))
                                            <div class="cd-loc-meta-line">
                                                <span class="cd-loc-meta-chip">
                                                    <i class="fas fa-hashtag text-muted me-1" style="font-size: 9.5px;"></i>Postal Code: <strong>{{ $loc->postcode }}</strong>
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="text-muted fs-8 py-2 text-center">No location details specified.</div>
                            @endif
                        </div>
                    </div>

                    <!-- Commercial Terms Cards (Dynamic Business Logic: Hidden completely if neither model active) -->
                    @if(!empty($hasActiveCommercials))
                        @if(!empty($hasContractStaffing) && !empty($contractCommercial))
                            <!-- Sub-Card: Contract Staffing – Commercial Terms -->
                            <div class="cd-master-subcard" style="border-top: 3px solid #2563eb;">
                                <div class="cd-subcard-header">
                                    <div class="cd-subcard-header-left">
                                        <div class="cd-subcard-header-icon blue"><i class="fas fa-file-contract"></i></div>
                                        <h4 class="cd-subcard-heading">Contract Staffing – Commercial Terms</h4>
                                    </div>
                                    <span class="badge bg-light-primary text-primary fw-bold" style="font-size:10px; padding:3px 7px;">
                                        <i class="fas fa-check-circle me-1"></i>Active Model
                                    </span>
                                </div>
                                <div class="cd-vertical-info-list">
                                    <!-- 1. Commercial / Billing Rate -->
                                    @if(!empty($contractCommercial->commercial_rate))
                                    <div class="cd-vinfo-row">
                                        <div class="cd-vinfo-key">
                                            <div class="cd-vinfo-icon sky"><i class="fas fa-file-invoice-dollar"></i></div>
                                            <span class="cd-vinfo-label">Commercial / Billing</span>
                                        </div>
                                        <div class="cd-vinfo-val">
                                            <span class="fw-semibold text-gray-800">{{ $contractCommercial->commercial_rate }}</span>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- 2. Billing Cycle -->
                                    @if(!empty($contractCommercial->billing_cycle))
                                    <div class="cd-vinfo-row">
                                        <div class="cd-vinfo-key">
                                            <div class="cd-vinfo-icon blue"><i class="fas fa-calendar-check"></i></div>
                                            <span class="cd-vinfo-label">Billing Cycle</span>
                                        </div>
                                        <div class="cd-vinfo-val">
                                            <span class="fw-semibold text-gray-800">{{ $contractCommercial->billing_cycle }}</span>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- 3. Minimum Contract Duration -->
                                    @if(!empty($contractCommercial->min_duration))
                                    <div class="cd-vinfo-row">
                                        <div class="cd-vinfo-key">
                                            <div class="cd-vinfo-icon purple"><i class="fas fa-clock"></i></div>
                                            <span class="cd-vinfo-label">Contract Duration</span>
                                        </div>
                                        <div class="cd-vinfo-val">
                                            <span class="fw-semibold text-gray-800">{{ $contractCommercial->min_duration }}</span>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- 4. Replacement Terms -->
                                    @if(!empty($contractCommercial->replacement_terms))
                                    <div class="cd-vinfo-row">
                                        <div class="cd-vinfo-key">
                                            <div class="cd-vinfo-icon emerald"><i class="fas fa-shield-halved"></i></div>
                                            <span class="cd-vinfo-label">Replacement Terms</span>
                                        </div>
                                        <div class="cd-vinfo-val">
                                            <span class="fw-semibold text-gray-800">{{ $contractCommercial->replacement_terms }}</span>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- 5. Payment Terms -->
                                    @if(!empty($contractCommercial->payment_terms))
                                    <div class="cd-vinfo-row">
                                        <div class="cd-vinfo-key">
                                            <div class="cd-vinfo-icon amber"><i class="fas fa-credit-card"></i></div>
                                            <span class="cd-vinfo-label">Payment Terms</span>
                                        </div>
                                        <div class="cd-vinfo-val">
                                            <span class="fw-semibold text-gray-800">{{ $contractCommercial->payment_terms }}</span>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- 6. Notice / Termination -->
                                    @if(!empty($contractCommercial->early_termination))
                                    <div class="cd-vinfo-row">
                                        <div class="cd-vinfo-key">
                                            <div class="cd-vinfo-icon red"><i class="fas fa-hand-paper"></i></div>
                                            <span class="cd-vinfo-label">Notice / Termination</span>
                                        </div>
                                        <div class="cd-vinfo-val">
                                            <span class="fw-semibold text-gray-800">{{ $contractCommercial->early_termination }}</span>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- 7. Overtime Policy -->
                                    @if(!empty($contractCommercial->overtime_policy))
                                    <div class="cd-vinfo-row">
                                        <div class="cd-vinfo-key">
                                            <div class="cd-vinfo-icon orange"><i class="fas fa-business-time"></i></div>
                                            <span class="cd-vinfo-label">Overtime Policy</span>
                                        </div>
                                        <div class="cd-vinfo-val">
                                            <span class="fw-semibold text-gray-800">{{ $contractCommercial->overtime_policy }}</span>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- 8. Contract Conditions / Custom Summary -->
                                    @if(!empty($contractCommercial->custom_terms))
                                    <div class="cd-vinfo-row" style="flex-direction: column; align-items: flex-start; gap: 4px;">
                                        <div class="cd-vinfo-key" style="flex: auto;">
                                            <div class="cd-vinfo-icon purple"><i class="fas fa-file-lines"></i></div>
                                            <span class="cd-vinfo-label">Contract Conditions</span>
                                        </div>
                                        <div style="font-size: 11px; color: #475569; line-height: 1.4; padding-left: 34px;">
                                            {!! nl2br(e($contractCommercial->custom_terms)) !!}
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if(!empty($hasPermanentStaffing) && !empty($permanentCommercial))
                            <!-- Sub-Card: Permanent Staffing – Commercial Terms -->
                            <div class="cd-master-subcard" style="border-top: 3px solid #10b981;">
                                <div class="cd-subcard-header">
                                    <div class="cd-subcard-header-left">
                                        <div class="cd-subcard-header-icon emerald"><i class="fas fa-handshake"></i></div>
                                        <h4 class="cd-subcard-heading">Permanent Staffing – Commercial Terms</h4>
                                    </div>
                                    <span class="badge bg-light-success text-success fw-bold" style="font-size:10px; padding:3px 7px;">
                                        <i class="fas fa-check-circle me-1"></i>Active Model
                                    </span>
                                </div>
                                <div class="cd-vertical-info-list">
                                    <!-- 1. Placement Fee -->
                                    @if(!empty($permanentCommercial->placement_fee))
                                    <div class="cd-vinfo-row">
                                        <div class="cd-vinfo-key">
                                            <div class="cd-vinfo-icon amber"><i class="fas fa-percent"></i></div>
                                            <span class="cd-vinfo-label">Placement Fee</span>
                                        </div>
                                        <div class="cd-vinfo-val">
                                            <span class="badge bg-light-warning text-warning fw-bold fs-8" style="padding:4px 8px; border-radius:6px;">{{ $permanentCommercial->placement_fee }}</span>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- 2. Guarantee Period -->
                                    @if(!empty($permanentCommercial->guarantee_period))
                                    <div class="cd-vinfo-row">
                                        <div class="cd-vinfo-key">
                                            <div class="cd-vinfo-icon emerald"><i class="fas fa-shield-halved"></i></div>
                                            <span class="cd-vinfo-label">Guarantee Period</span>
                                        </div>
                                        <div class="cd-vinfo-val">
                                            <span class="fw-semibold text-gray-800">{{ $permanentCommercial->guarantee_period }}</span>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- 3. Payment Terms / Invoice Terms -->
                                    @if(!empty($permanentCommercial->payment_terms))
                                    <div class="cd-vinfo-row">
                                        <div class="cd-vinfo-key">
                                            <div class="cd-vinfo-icon sky"><i class="fas fa-file-invoice-dollar"></i></div>
                                            <span class="cd-vinfo-label">Invoice Terms</span>
                                        </div>
                                        <div class="cd-vinfo-val">
                                            <span class="fw-semibold text-gray-800">{{ $permanentCommercial->payment_terms }}</span>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- 4. Billing Trigger -->
                                    @if(!empty($permanentCommercial->billing_trigger))
                                    <div class="cd-vinfo-row">
                                        <div class="cd-vinfo-key">
                                            <div class="cd-vinfo-icon orange"><i class="fas fa-bolt"></i></div>
                                            <span class="cd-vinfo-label">Billing Trigger</span>
                                        </div>
                                        <div class="cd-vinfo-val">
                                            <span class="fw-semibold text-gray-800">{{ $permanentCommercial->billing_trigger }}</span>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- 5. Permanent Specific Conditions -->
                                    @if(!empty($permanentCommercial->custom_terms))
                                    <div class="cd-vinfo-row" style="flex-direction: column; align-items: flex-start; gap: 4px;">
                                        <div class="cd-vinfo-key" style="flex: auto;">
                                            <div class="cd-vinfo-icon purple"><i class="fas fa-file-lines"></i></div>
                                            <span class="cd-vinfo-label">Permanent Conditions</span>
                                        </div>
                                        <div style="font-size: 11px; color: #475569; line-height: 1.4; padding-left: 34px;">
                                            {!! nl2br(e($permanentCommercial->custom_terms)) !!}
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════════════
             TAB 2: ACTIVITY TIMELINE (Master Shared Direct Client & Agency Activity Engine)
             ══════════════════════════════════════════════════════════════════════ -->
        <div class="tab-pane" id="recent_activity" role="tabpanel">
            <div class="px-3 py-2">
                @include('service_company.client.components.tab-activity', [
                    'clientId' => $agencyId,
                    'clientType' => 'agency',
                    'viewerType' => 'agency',
                    'agencyCompanyId' => $agencyId
                ])
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════════════
             TAB 3: CONTACTS & REPRESENTATIVES (Master Modern Card + Sub-Card)
             ══════════════════════════════════════════════════════════════════════ -->
        <div class="tab-pane" id="contacts" role="tabpanel" style="background:#ffffff; min-height: 520px;">
            <div class="cd-contact-tab-container">
                <!-- TIER 1: TOP HEADER TOOLBAR CARD -->
                <div class="cd-contact-toolbar-card">
                    <div class="cd-contact-toolbar-left">
                        <div class="cd-contact-title-avatar">
                            <i class="fas fa-address-book fa-solid"></i>
                        </div>
                        <div class="cd-contact-title-wrap">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <h4 class="cd-contact-title">Agency Contacts</h4>
                                <span class="cd-contact-count-badge" id="cdAgencyContactCountBadge">
                                    <span id="cdAgencyContactCountText">{{ count($contactsList) > 0 ? count($contactsList) : 1 }}</span> Contacts
                                </span>
                                <span class="cd-contact-scope-pill">
                                    <i class="fas fa-handshake text-success me-1"></i> Agency Partner
                                </span>
                            </div>
                            <div class="cd-contact-subtitle">Authoritative directory of key account leads, recruiters, and primary coordinators for {{ $detailName }}.</div>
                        </div>
                    </div>
                    <div class="cd-contact-toolbar-right">
                        <div class="cd-contact-search-subcard">
                            <i class="fas fa-search fa-solid"></i>
                            <input type="text" id="cdAgencyContactSearchInput" placeholder="Search contacts by name, role, email, phone..." autocomplete="off">
                            <button type="button" class="cd-contact-search-clear" id="cdAgencyContactSearchClear" style="display:none;" title="Clear search">
                                <i class="fas fa-times fa-solid"></i>
                            </button>
                        </div>
                        <button type="button" id="cdAgencyContactResetBtn" class="cd-contact-action-subcard" title="Reset Filters &amp; Refresh">
                            <i class="fas fa-sync-alt fa-solid"></i>
                        </button>
                    </div>
                </div>

                <!-- TIER 2: SUB-TABS FILTER CARD -->
                <div class="cd-contact-tabs-card">
                    <div class="cd-contact-tabs-list">
                        <button type="button" class="cd-contact-subtab active js-agency-contact-tab" data-filter="all">
                            <span>All Contacts</span>
                            <span class="cd-contact-subtab-badge">{{ count($contactsList) > 0 ? count($contactsList) : 1 }}</span>
                        </button>
                        <button type="button" class="cd-contact-subtab js-agency-contact-tab" data-filter="primary">
                            <span><i class="fas fa-star text-amber-500 me-1" style="font-size:10px;"></i> Primary SPOCs</span>
                            <span class="cd-contact-subtab-badge">1</span>
                        </button>
                        <button type="button" class="cd-contact-subtab js-agency-contact-tab" data-filter="lead">
                            <span>Leadership &amp; Leads</span>
                            <span class="cd-contact-subtab-badge">{{ count($contactsList) > 0 ? count($contactsList) : 1 }}</span>
                        </button>
                        <button type="button" class="cd-contact-subtab js-agency-contact-tab" data-filter="recruiter">
                            <span>Recruiters &amp; Coordinators</span>
                            <span class="cd-contact-subtab-badge">0</span>
                        </button>
                        <button type="button" class="cd-contact-subtab js-agency-contact-tab" data-filter="active">
                            <span>Active</span>
                            <span class="cd-contact-subtab-badge">{{ count($contactsList) > 0 ? count($contactsList) : 1 }}</span>
                        </button>
                    </div>
                </div>

                <!-- TIER 3: Modern Contact Cards Stream -->
                <div class="cd-contact-stream-wrap">
                    <div class="cd-contact-stream" id="agencyContactCardsStream">
                        @if (count($contactsList) > 0)
                            @foreach ($contactsList as $contact)
                                @php
                                    $cName = ucwords($contact->name ?? 'Representative');
                                    $cRole = ucwords($contact->display_name ?? ($contact->designation ?? 'Account Manager'));
                                    $cEmail = $contact->email ?? 'partners@agency.com';
                                    $cMobile = $contact->mobile ?? '+91 98765 43210';
                                    $cInitials = strtoupper(substr($cName, 0, 2));
                                    $isPrimary = $loop->first;
                                @endphp
                                <div class="cd-contact-card js-agency-contact-item" data-role="{{ $isPrimary ? 'primary lead' : 'lead' }}">
                                    <div class="cd-contact-card-grid">
                                        <!-- Sub-Card 1: Profile & Identity -->
                                        <div class="cd-contact-subcard">
                                            <div class="cd-contact-subcard-hdr">
                                                <span class="cd-contact-subcard-label">Profile &amp; Role</span>
                                                @if ($isPrimary)
                                                    <span class="cd-badge-primary-spoc"><i class="fas fa-star me-1 text-amber-500"></i> Primary SPOC</span>
                                                @else
                                                    <span class="cd-badge-secondary-contact">Partner Lead</span>
                                                @endif
                                            </div>
                                            <div class="cd-contact-profile-row">
                                                <div class="cd-contact-avatar-box">{{ $cInitials }}</div>
                                                <div class="cd-contact-profile-meta">
                                                    <span class="cd-contact-card-name js-contact-searchable" title="{{ $cName }}">{{ $cName }}</span>
                                                    <span class="cd-contact-card-desig" title="{{ $cRole }}">{{ $cRole }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Sub-Card 2: Communication Channels -->
                                        <div class="cd-contact-subcard">
                                            <div class="cd-contact-subcard-hdr">
                                                <span class="cd-contact-subcard-label">Direct Communication</span>
                                                <button type="button" class="btn btn-link p-0 text-primary fs-9 fw-semibold text-decoration-none" onclick="copyToClipboard('{{ $cName }}\nEmail: {{ $cEmail }}\nPhone: {{ $cMobile }}', this)">
                                                    <i class="fas fa-copy me-1"></i> Copy All
                                                </button>
                                            </div>
                                            <div class="cd-contact-subcard-body">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <a href="mailto:{{ $cEmail }}" class="text-dark fs-8 text-truncate text-decoration-none" title="Email: {{ $cEmail }}">
                                                        <i class="fas fa-envelope text-primary me-1"></i> {{ $cEmail }}
                                                    </a>
                                                    <button type="button" class="cd-copy-btn" onclick="copyToClipboard('{{ $cEmail }}', this)" title="Copy Email"><i class="fas fa-copy"></i></button>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between mt-1">
                                                    <a href="tel:{{ $cMobile }}" class="text-dark fs-8 text-truncate text-decoration-none" title="Phone: {{ $cMobile }}">
                                                        <i class="fas fa-phone text-success me-1"></i> {{ $cMobile }}
                                                    </a>
                                                    <button type="button" class="cd-copy-btn" onclick="copyToClipboard('{{ $cMobile }}', this)" title="Copy Phone"><i class="fas fa-copy"></i></button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Sub-Card 3: Quick Action Buttons -->
                                        <div class="cd-contact-subcard">
                                            <div class="cd-contact-subcard-hdr">
                                                <span class="cd-contact-subcard-label">Quick Actions</span>
                                                <span class="badge badge-light-success text-success fs-9 fw-bold">Active</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-1 mt-2">
                                                <a href="mailto:{{ $cEmail }}" class="btn btn-sm btn-light-primary fw-bold py-1 px-2 fs-8 flex-fill text-center">
                                                    <i class="fas fa-envelope me-1"></i> Email
                                                </a>
                                                <a href="tel:{{ $cMobile }}" class="btn btn-sm btn-light-success fw-bold py-1 px-2 fs-8 flex-fill text-center">
                                                    <i class="fas fa-phone me-1"></i> Call
                                                </a>
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $cMobile) }}" target="_blank" class="btn btn-sm btn-light-success fw-bold py-1 px-2 fs-8 text-center" title="Chat on WhatsApp">
                                                    <i class="fab fa-whatsapp"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="cd-contact-card-bottom">
                                        <div><i class="fas fa-building text-muted me-1"></i> {{ $detailName }} Partner Representative</div>
                                        <div><span class="badge badge-light-primary fs-9 fw-semibold">Direct SPOC</span></div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <!-- Fallback Authoritative Representative Card -->
                            <div class="cd-contact-card js-agency-contact-item" data-role="primary lead">
                                <div class="cd-contact-card-grid">
                                    <div class="cd-contact-subcard">
                                        <div class="cd-contact-subcard-hdr">
                                            <span class="cd-contact-subcard-label">Profile &amp; Role</span>
                                            <span class="cd-badge-primary-spoc"><i class="fas fa-star me-1 text-amber-500"></i> Primary SPOC</span>
                                        </div>
                                        <div class="cd-contact-profile-row">
                                            <div class="cd-contact-avatar-box">{{ $detailInitials }}</div>
                                            <div class="cd-contact-profile-meta">
                                                <span class="cd-contact-card-name js-contact-searchable">{{ $detailName }} Operations Lead</span>
                                                <span class="cd-contact-card-desig">Principal Talent Partner</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="cd-contact-subcard">
                                        <div class="cd-contact-subcard-hdr">
                                            <span class="cd-contact-subcard-label">Direct Communication</span>
                                            <button type="button" class="btn btn-link p-0 text-primary fs-9 fw-semibold text-decoration-none" onclick="copyToClipboard('{{ $agency_email }}\n{{ $agency_phone }}', this)">
                                                <i class="fas fa-copy me-1"></i> Copy All
                                            </button>
                                        </div>
                                        <div class="cd-contact-subcard-body">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <a href="mailto:{{ $agency_email }}" class="text-dark fs-8 text-truncate text-decoration-none">
                                                    <i class="fas fa-envelope text-primary me-1"></i> {{ $agency_email }}
                                                </a>
                                                <button type="button" class="cd-copy-btn" onclick="copyToClipboard('{{ $agency_email }}', this)"><i class="fas fa-copy"></i></button>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-1">
                                                <a href="tel:{{ $agency_phone }}" class="text-dark fs-8 text-truncate text-decoration-none">
                                                    <i class="fas fa-phone text-success me-1"></i> {{ $agency_phone }}
                                                </a>
                                                <button type="button" class="cd-copy-btn" onclick="copyToClipboard('{{ $agency_phone }}', this)"><i class="fas fa-copy"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="cd-contact-subcard">
                                        <div class="cd-contact-subcard-hdr">
                                            <span class="cd-contact-subcard-label">Quick Actions</span>
                                            <span class="badge badge-light-success text-success fs-9 fw-bold">Active</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-1 mt-2">
                                            <a href="mailto:{{ $agency_email }}" class="btn btn-sm btn-light-primary fw-bold py-1 px-2 fs-8 flex-fill text-center"><i class="fas fa-envelope me-1"></i> Email</a>
                                            <a href="tel:{{ $agency_phone }}" class="btn btn-sm btn-light-success fw-bold py-1 px-2 fs-8 flex-fill text-center"><i class="fas fa-phone me-1"></i> Call</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="cd-contact-card-bottom">
                                    <div><i class="fas fa-building text-muted me-1"></i> {{ $detailName }} Primary Agency Representative</div>
                                    <div><span class="badge badge-light-primary fs-9 fw-semibold">Direct SPOC</span></div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════════════
             TAB 4: JOBS / REQUISITIONS (Master Modern Card + Sub-Card)
             ══════════════════════════════════════════════════════════════════════ -->
        <div class="tab-pane" id="requisitions" role="tabpanel">
            <div class="cd-jobs-wrapper">
                <!-- Tier 1: Top Header Card with Dynamic Search Bar -->
                <div class="cd-jobs-header-card">
                    <div class="cd-jobs-header-left">
                        <div class="cd-jobs-header-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="cd-jobs-header-title">Jobs</span>
                                <span class="cd-contact-count-badge" id="agencyJobHeaderBadge">{{ $kpiTotalJobs }} Jobs</span>
                            </div>
                            <div class="cd-jobs-header-sub">Roles open and shared with {{ $detailName }}.</div>
                        </div>
                    </div>
                    <div class="cd-jobs-header-right">
                        <div class="cd-jobs-search-subcard">
                            <i class="fas fa-search"></i>
                            <input type="text" id="agencyJobSearchInput" placeholder="Search jobs by title, skills, ID..." autocomplete="off">
                            <button type="button" class="cd-jobs-search-clear" id="btnAgencyJobSearchClear" style="display:none;" title="Clear search">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <button type="button" id="btnAgencyJobReset" class="cd-cand-action-subcard" title="Reset Filters &amp; Refresh">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>

                <!-- Tier 2: Sub-Tabs Card with White Background -->
                <div class="cd-jobs-tabs-card">
                    <div class="cd-jobs-tabs-group">
                        <button type="button" class="cd-jobs-tab-btn js-agency-job-tab active" data-status="all">
                            <span>All Jobs</span>
                            <span class="cd-jobs-tab-badge" id="agencyJobCountAll">{{ $kpiTotalJobs }}</span>
                        </button>
                        <button type="button" class="cd-jobs-tab-btn js-agency-job-tab" data-status="active">
                            <span>Hiring</span>
                            <span class="cd-jobs-tab-badge badge-hiring" id="agencyJobCountHiring">{{ $kpiActiveJobs }}</span>
                        </button>
                        <button type="button" class="cd-jobs-tab-btn js-agency-job-tab" data-status="hold">
                            <span>On Hold</span>
                            <span class="cd-jobs-tab-badge badge-hold" id="agencyJobCountHold">{{ $kpiHoldJobs }}</span>
                        </button>
                        <button type="button" class="cd-jobs-tab-btn js-agency-job-tab" data-status="closed">
                            <span>Closed</span>
                            <span class="cd-jobs-tab-badge badge-closed" id="agencyJobCountClosed">{{ $kpiClosedJobs }}</span>
                        </button>
                        <button type="button" class="cd-jobs-tab-btn js-agency-job-tab" data-status="inactive">
                            <span>Inactive</span>
                            <span class="cd-jobs-tab-badge badge-inactive">0</span>
                        </button>
                    </div>
                </div>

                <!-- Tier 3: Card-Based Jobs Stream -->
                <div class="cd-jobs-stream" id="agencyJobsListajax">
                    <div class="d-flex flex-column align-items-center justify-content-center py-10" style="min-height:200px;">
                        <div class="spinner-border text-primary mb-3" style="width:2rem;height:2rem;" role="status"></div>
                        <span class="text-gray-600 font-poppins fs-7 fw-semibold">Loading open jobs...</span>
                    </div>
                </div>
                <div id="paginationAgencyJobs" class="mt-3"></div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════════════
             TAB 5: CANDIDATES PIPELINE (Master Modern Card + Sub-Card)
             ══════════════════════════════════════════════════════════════════════ -->
        <div class="tab-pane" id="candidate_list" role="tabpanel" style="background:#ffffff; min-height: 520px;">
            <div class="cd-cand-tab-container">
                <!-- TIER 1: TOP HEADER TOOLBAR CARD -->
                <div class="cd-cand-toolbar-card">
                    <div class="cd-cand-toolbar-left">
                        <div class="cd-cand-title-avatar">
                            <i class="fas fa-user-check fa-solid"></i>
                        </div>
                        <div class="cd-cand-title-wrap">
                            <div class="d-flex align-items-center gap-2">
                                <h4 class="cd-cand-title">Candidates</h4>
                                <span class="cd-cand-count-badge" id="cdAgencyCandCountBadge">
                                    <span id="cdAgencyCandCountText">{{ $kpiSubmitted }}</span> Candidates
                                </span>
                            </div>
                            <div class="cd-cand-subtitle">Pipeline and submitted talent from {{ $detailName }}</div>
                        </div>
                    </div>
                    <div class="cd-cand-toolbar-right">
                        <div class="cd-cand-search-subcard">
                            <i class="fas fa-search fa-solid"></i>
                            <input type="text" id="cdAgencyCandSearchInput" placeholder="Search candidate by name, designation, skills, company..." autocomplete="off">
                            <button type="button" class="cd-cand-search-clear" id="cdAgencyCandSearchClear" style="display:none;" title="Clear search">
                                <i class="fas fa-times fa-solid"></i>
                            </button>
                        </div>
                        <button type="button" id="cdAgencyCandResetBtn" class="cd-cand-action-subcard" title="Reset Filters &amp; Refresh">
                            <i class="fas fa-sync-alt fa-solid"></i>
                        </button>
                    </div>
                </div>

                <!-- TIER 2: SUB-TABS PIPELINE CARD (8 Pipeline Stages) -->
                <div class="cd-cand-tabs-card">
                    <div class="cd-cand-tabs-list" role="tablist">
                        <button type="button" class="cd-cand-subtab cd-cand-subtab-agency active js-agency-cand-stage" data-stage="sourcing" data-status="1" role="tab" id="agencyCandTabSourcing">
                            <span>Sourcing</span>
                            <span class="cd-cand-subtab-badge" id="candTabCountSourcingAgency">{{ $sourcing_count ?? 0 }}</span>
                        </button>
                        <button type="button" class="cd-cand-subtab cd-cand-subtab-agency js-agency-cand-stage" data-stage="screening" data-status="2" role="tab" id="agencyCandTabScreening">
                            <span>Screening</span>
                            <span class="cd-cand-subtab-badge" id="candTabCountScreeningAgency">{{ $screening_count ?? 0 }}</span>
                        </button>
                        <button type="button" class="cd-cand-subtab cd-cand-subtab-agency js-agency-cand-stage" data-stage="evaluation" data-status="3" role="tab" id="agencyCandTabEvaluation">
                            <span>Evaluation</span>
                            <span class="cd-cand-subtab-badge" id="candTabCountEvaluationAgency">{{ $evaluation_count ?? 0 }}</span>
                        </button>
                        <button type="button" class="cd-cand-subtab cd-cand-subtab-agency js-agency-cand-stage" data-stage="negotiations" data-status="4" role="tab" id="agencyCandTabNegotiations">
                            <span>Negotiations</span>
                            <span class="cd-cand-subtab-badge" id="candTabCountNegotiationsAgency">{{ $negotiation_count ?? 0 }}</span>
                        </button>
                        <button type="button" class="cd-cand-subtab cd-cand-subtab-agency js-agency-cand-stage" data-stage="offer" data-status="5" role="tab" id="agencyCandTabOffer">
                            <span>Offer</span>
                            <span class="cd-cand-subtab-badge" id="candTabCountOfferAgency">{{ $kpiOffers }}</span>
                        </button>
                        <button type="button" class="cd-cand-subtab cd-cand-subtab-agency js-agency-cand-stage" data-stage="joined" data-status="6" role="tab" id="agencyCandTabJoined">
                            <span>Joined</span>
                            <span class="cd-cand-subtab-badge" id="candTabCountJoinedAgency">{{ $kpiJoins }}</span>
                        </button>
                        <button type="button" class="cd-cand-subtab cd-cand-subtab-agency js-agency-cand-stage" data-stage="rejected" data-status="7" role="tab" id="agencyCandTabRejected">
                            <span>Rejected</span>
                            <span class="cd-cand-subtab-badge" id="candTabCountRejectedAgency">{{ $Rejected_count ?? 0 }}</span>
                        </button>
                    </div>
                </div>

                <!-- Local Skeleton Loader Cards -->
                <div id="cdCandSkeletonLoaderAgency" style="display:none;" class="py-2">
                    <div class="cd-cand-skeleton-card">
                        <div class="cd-cand-skeleton-pulse" style="width: 25%; height: 24px;"></div>
                        <div class="cd-cand-skeleton-pulse" style="width: 18%; height: 24px;"></div>
                        <div class="cd-cand-skeleton-pulse" style="width: 16%; height: 24px;"></div>
                        <div class="cd-cand-skeleton-pulse" style="width: 28%; height: 50px;"></div>
                        <div class="cd-cand-skeleton-pulse" style="width: 13%; height: 24px;"></div>
                    </div>
                    <div class="cd-cand-skeleton-card">
                        <div class="cd-cand-skeleton-pulse" style="width: 25%; height: 24px;"></div>
                        <div class="cd-cand-skeleton-pulse" style="width: 18%; height: 24px;"></div>
                        <div class="cd-cand-skeleton-pulse" style="width: 16%; height: 24px;"></div>
                        <div class="cd-cand-skeleton-pulse" style="width: 28%; height: 50px;"></div>
                        <div class="cd-cand-skeleton-pulse" style="width: 13%; height: 24px;"></div>
                    </div>
                </div>

                <!-- Empty State Card -->
                <div id="cdCandEmptyStateAgency" class="text-center py-12" style="display:none; background:#ffffff; border-radius:10px; border:1px dashed #cbd5e1; margin:8px 0;">
                    <div class="symbol symbol-50px mx-auto mb-3">
                        <span class="symbol-label bg-light-primary text-primary fs-2">
                            <i class="fas fa-user-slash fa-solid"></i>
                        </span>
                    </div>
                    <div class="text-dark fs-6 fw-bold mb-1">No candidates found</div>
                    <div class="text-gray-400 fs-7">Try adjusting your search query or pipeline filter.</div>
                </div>

                <!-- TIER 3: Candidate Cards Stream -->
                <div class="cd-cand-stream-wrap" id="cdCandidateTableWrapAgency">
                    <div class="cd-cand-stream" id="agencyCandidatesStream">
                    </div>
                    <div id="paginationAgencyCandidates" class="d-flex align-items-center justify-content-between pt-4">
                    </div>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════════════
             TAB 6: INTERVIEWS TRACKER (Master Modern Card + Sub-Card)
             ══════════════════════════════════════════════════════════════════════ -->
        <div class="tab-pane" id="interview_tracker" role="tabpanel" style="background:#ffffff; min-height: 520px;">
            <div class="cd-it-wrapper">
                <!-- Tier 1: Top Card Header Toolbar -->
                <div class="cd-it-header-card">
                    <div class="cd-it-header-left">
                        <div class="cd-it-header-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="cd-it-header-title">Interviews</span>
                                <span class="cd-it-header-badge" id="agencyItHeaderBadge">{{ $kpiInterviews }}</span>
                            </div>
                            <div class="cd-it-header-sub">Scheduled interviews with candidates submitted by {{ $detailName }}.</div>
                        </div>
                    </div>
                    <div class="cd-it-header-right">
                        <div class="cd-it-search-subcard">
                            <i class="fas fa-search"></i>
                            <input type="text" id="agencyItSearchInput" placeholder="Search candidate, job, interviewer..." autocomplete="off">
                            <button type="button" class="cd-it-search-clear" id="agencyItSearchClear" style="display:none;" title="Clear search">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <button type="button" id="agencyItResetBtn" class="cd-it-action-subcard" title="Reset Filters &amp; Refresh">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>

                <!-- Tier 2: Sub-Tabs Filter Card -->
                <div class="cd-it-tabs-card">
                    <button type="button" class="cd-it-tab-btn active js-agency-it-tab" data-filter="all">
                        <span>All Interviews</span>
                        <span class="cd-it-tab-badge" id="agencyItFilterCountAll">{{ $itCountAll }}</span>
                    </button>
                    <button type="button" class="cd-it-tab-btn js-agency-it-tab" data-filter="upcoming">
                        <span class="cd-it-dot dot-primary"></span>
                        <span>Upcoming / Today</span>
                        <span class="cd-it-tab-badge badge-primary" id="agencyItFilterCountUpcoming">{{ $itCountUpcoming }}</span>
                    </button>
                    <button type="button" class="cd-it-tab-btn js-agency-it-tab" data-filter="pending_feedback">
                        <span class="cd-it-dot dot-warning"></span>
                        <span>Feedback Pending</span>
                        <span class="cd-it-tab-badge badge-warning" id="agencyItFilterCountPending">{{ $itCountPendingFeedback }}</span>
                    </button>
                    <button type="button" class="cd-it-tab-btn js-agency-it-tab" data-filter="completed">
                        <span class="cd-it-dot dot-success"></span>
                        <span>Completed / Attended</span>
                        <span class="cd-it-tab-badge badge-success" id="agencyItFilterCountCompleted">{{ $itCountCompleted }}</span>
                    </button>
                    <button type="button" class="cd-it-tab-btn js-agency-it-tab" data-filter="cancelled">
                        <span class="cd-it-dot dot-danger"></span>
                        <span>Cancelled / Rescheduled</span>
                        <span class="cd-it-tab-badge" id="agencyItFilterCountCancelled">{{ $itCountCancelled }}</span>
                    </button>
                </div>

                <!-- Tier 3: Primary Interviews Stream -->
                <div class="cd-it-stream" id="candidatetListinterviewtrackerAgency" data-role="interview-list">
                    <div class="it-loading-card">
                        <span class="spinner-border spinner-border-sm text-primary me-2"></span>
                        <span class="text-gray-600 fs-7">Loading Authoritative Interview Projection...</span>
                    </div>
                </div>

                <!-- Tier 4: Enterprise Pagination Card -->
                <div id="paginationcandidateinterviewtrackerAgency" class="cd-it-pagination-card" data-role="interview-pagination">
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════════════
             TAB 7: OFFERS & JOININGS (Master Modern Card + Sub-Card)
             ══════════════════════════════════════════════════════════════════════ -->
        <div class="tab-pane" id="candidate_joining" role="tabpanel" style="background:#ffffff; min-height: 520px;">
            <div class="cd-oj-wrapper">
                <!-- TIER 1: TOP HEADER TOOLBAR CARD -->
                <div class="cd-oj-header-card">
                    <div class="cd-oj-header-left">
                        <div class="cd-oj-header-icon">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h4 class="cd-oj-header-title">Offer &amp; Joining</h4>
                                <span class="cd-oj-header-badge" id="agencyOjHeaderBadge"><span id="agencyOjCountText">{{ $kpiOffersTotal }}</span> Offers &amp; Joinings</span>
                            </div>
                            <div class="cd-oj-header-sub">Track candidate job offers, joining confirmations and onboardings from {{ $detailName }}.</div>
                        </div>
                    </div>
                    <div class="cd-oj-header-right">
                        <div class="cd-oj-search-subcard">
                            <i class="fas fa-search"></i>
                            <input type="text" id="agencyOjSearchInput" placeholder="Search candidate, job, CTC, status..." autocomplete="off">
                            <button type="button" class="cd-oj-search-clear" id="agencyOjSearchClear" style="display:none;" title="Clear search">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <button type="button" id="agencyOjResetBtn" class="cd-oj-action-subcard" title="Reset Filters &amp; Refresh">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>

                <!-- TIER 2: SUB-TABS CARD WITH WHITE BACKGROUND -->
                <div class="cd-oj-tabs-card">
                    <div class="cd-oj-tabs-group" id="agencyOjSegmentGroup">
                        <button type="button" class="cd-oj-tab-btn active cd-oj-seg-btn-agency js-agency-oj-tab" data-segment="all">
                            <span>All Records</span>
                            <span class="cd-oj-tab-badge" id="agencyOjCountAll">{{ $ojCountAll }}</span>
                        </button>
                        <button type="button" class="cd-oj-tab-btn cd-oj-seg-btn-agency js-agency-oj-tab" data-segment="offers">
                            <span class="cd-oj-dot dot-warning"></span>
                            <span>Offers Extended</span>
                            <span class="cd-oj-tab-badge badge-warning" id="agencyOjCountOffers">{{ $ojCountOffers }}</span>
                        </button>
                        <button type="button" class="cd-oj-tab-btn cd-oj-seg-btn-agency js-agency-oj-tab" data-segment="joining">
                            <span class="cd-oj-dot dot-primary"></span>
                            <span>Joining Soon</span>
                            <span class="cd-oj-tab-badge badge-primary" id="agencyOjCountJoining">{{ $ojCountJoiningSoon }}</span>
                        </button>
                        <button type="button" class="cd-oj-tab-btn cd-oj-seg-btn-agency js-agency-oj-tab" data-segment="joined">
                            <span class="cd-oj-dot dot-success"></span>
                            <span>Joined &amp; Onboarded</span>
                            <span class="cd-oj-tab-badge badge-success" id="agencyOjCountJoined">{{ $ojCountJoined }}</span>
                        </button>
                    </div>
                </div>

                <!-- TIER 3: PRIMARY CARDS LIST STREAM (Populated via AJAX) -->
                <div class="cd-oj-stream-wrap">
                    <div id="candidatetListJoinAgency" class="cd-oj-stream" data-role="offer-joining-list">
                        <div class="d-flex align-items-center justify-content-center py-10">
                            <div class="spinner-border text-primary me-3" role="status"></div>
                            <span class="text-gray-600 fs-7">Loading authoritative offers &amp; joinings...</span>
                        </div>
                    </div>
                </div>
                <div id="paginationcandidatejoinAgency" class="mt-3">
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════════════
             TAB 8: PERFORMANCE DASHBOARD (Master Modern Card + Sub-Card)
             ══════════════════════════════════════════════════════════════════════ -->
        <div class="tab-pane" id="candidateDashboard" role="tabpanel">
            <div class="cd-perf-wrap">

                <!-- ── 1. Performance Header Bar ── -->
                <div class="cd-perf-header">
                    <div class="cd-perf-header-left">
                        <div class="cd-perf-icon-box">
                            <i class="fas fa-chart-column fa-solid" style="color: #10b981;"></i>
                        </div>
                        <div>
                            <div class="cd-perf-title">Performance Dashboard</div>
                            <div class="cd-perf-subtitle">Real-time hiring &amp; recruitment performance analytics for {{ $detailName }}</div>
                        </div>
                    </div>
                    <div class="cd-perf-header-right">
                        <div class="cd-perf-select-wrap">
                            <span class="cd-perf-select-lbl">Date Range:</span>
                            <select class="cd-perf-select" id="perfDateRangeSelect" onchange="cdPerfSyncRange(this.value)">
                                <option value="12M" selected>Last 12 Months</option>
                                <option value="6M">Last 6 Months</option>
                                <option value="3M">Last 3 Months</option>
                                <option value="30D">Last 30 Days</option>
                                <option value="all">All Time</option>
                            </select>
                        </div>
                        <div class="cd-perf-select-wrap">
                            <span class="cd-perf-select-lbl">Compare:</span>
                            <select class="cd-perf-select" id="perfCompareWithSelect" onchange="cdPerfSyncCompare(this.value)">
                                <option value="previous_period" selected>vs. Previous Period</option>
                                <option value="same_period_last_year">vs. Same Period Last Year</option>
                                <option value="none">No Comparison</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-sm btn-icon btn-light" id="btnPerfRefresh" onclick="cdPerfRefresh()" title="Refresh Analytics" style="height:30px; width:30px; border-radius:6px; border:1px solid #e2e8f0;">
                            <i class="fas fa-rotate fa-solid" style="font-size:11px; color:#64748b;"></i>
                        </button>
                    </div>
                </div>

                <!-- ── Dynamic Empty State Banner (Shown conditionally) ── -->
                <div id="perfEmptyStateBanner" style="display:none; margin-bottom:12px;">
                    <div class="alert alert-dismissible bg-light-warning border border-warning border-dashed d-flex flex-column flex-sm-row p-3 rounded-2">
                        <i class="fas fa-circle-info fa-solid fs-5 text-warning me-3 mb-2 mb-sm-0 align-self-center"></i>
                        <div class="d-flex flex-column pe-0 pe-sm-10 justify-content-center" style="font-size:11.5px; color:#475569;">
                            <strong id="perfEmptyStateTitle" style="font-weight:600;"></strong>
                            <span id="perfEmptyStateMessage" style="margin-left:4px;"></span>
                        </div>
                    </div>
                </div>

                <!-- ── 2. Single-Row KPI Section (5 Cards) ── -->
                <div class="cd-perf-kpi-grid">
                    <!-- KPI 1: Total Candidates -->
                    <div class="cd-perf-kpi-card" onclick="triggerDrawerTab('candidate_list', event)">
                        <div class="cd-perf-kpi-top">
                            <div class="cd-perf-kpi-icon cd-perf-kpi-icon-green">
                                <i class="fas fa-users fa-solid"></i>
                            </div>
                            <span class="cd-perf-pill cd-perf-pill-up" id="perfKpiCandPill"><i class="fas fa-arrow-up fa-solid" style="font-size:8px;"></i>0.0%</span>
                        </div>
                        <div class="cd-perf-kpi-num" id="perfKpiCandidates">{{ number_format($kpiSubmitted ?? 0) }}</div>
                        <div class="cd-perf-kpi-lbl">Total Candidates</div>
                        <div class="cd-perf-kpi-sub">Submitted by this agency</div>
                        <div class="cd-perf-kpi-comp" id="perfKpiCandComp">vs. previous period</div>
                    </div>

                    <!-- KPI 2: Interviews Scheduled -->
                    <div class="cd-perf-kpi-card" onclick="triggerDrawerTab('interview_tracker', event)">
                        <div class="cd-perf-kpi-top">
                            <div class="cd-perf-kpi-icon cd-perf-kpi-icon-blue">
                                <i class="fas fa-calendar-alt fa-solid fa-calendar-days"></i>
                            </div>
                            <span class="cd-perf-pill cd-perf-pill-up" id="perfKpiIntPill"><i class="fas fa-arrow-up fa-solid" style="font-size:8px;"></i>0.0%</span>
                        </div>
                        <div class="cd-perf-kpi-num" id="perfKpiInterviews">{{ number_format($kpiInterviews ?? 0) }}</div>
                        <div class="cd-perf-kpi-lbl">Interviews Scheduled</div>
                        <div class="cd-perf-kpi-sub">Across all rounds</div>
                        <div class="cd-perf-kpi-comp" id="perfKpiIntComp">vs. previous period</div>
                    </div>

                    <!-- KPI 3: Offers Generated -->
                    <div class="cd-perf-kpi-card" onclick="triggerDrawerTab('candidate_joining', event)">
                        <div class="cd-perf-kpi-top">
                            <div class="cd-perf-kpi-icon cd-perf-kpi-icon-amber">
                                <i class="fas fa-file-alt fa-solid fa-file-lines"></i>
                            </div>
                            <span class="cd-perf-pill cd-perf-pill-up" id="perfKpiOfferPill"><i class="fas fa-arrow-up fa-solid" style="font-size:8px;"></i>0.0%</span>
                        </div>
                        <div class="cd-perf-kpi-num" id="perfKpiOffers">{{ number_format($kpiOffers ?? 0) }}</div>
                        <div class="cd-perf-kpi-lbl">Offers Generated</div>
                        <div class="cd-perf-kpi-sub">Offer letters sent</div>
                        <div class="cd-perf-kpi-comp" id="perfKpiOfferComp">vs. previous period</div>
                    </div>

                    <!-- KPI 4: Successful Joins -->
                    <div class="cd-perf-kpi-card" onclick="triggerDrawerTab('candidate_joining', event)">
                        <div class="cd-perf-kpi-top">
                            <div class="cd-perf-kpi-icon cd-perf-kpi-icon-purple">
                                <i class="fas fa-user-check fa-solid"></i>
                            </div>
                            <span class="cd-perf-pill cd-perf-pill-up" id="perfKpiJoinPill"><i class="fas fa-arrow-up fa-solid" style="font-size:8px;"></i>0.0%</span>
                        </div>
                        <div class="cd-perf-kpi-num" id="perfKpiJoins">{{ number_format($kpiJoins ?? 0) }}</div>
                        <div class="cd-perf-kpi-lbl">Successful Joins</div>
                        <div class="cd-perf-kpi-sub">Candidates placed</div>
                        <div class="cd-perf-kpi-comp" id="perfKpiJoinComp">vs. previous period</div>
                    </div>

                    <!-- KPI 5: Avg. Time to Hire -->
                    <div class="cd-perf-kpi-card">
                        <div class="cd-perf-kpi-top">
                            <div class="cd-perf-kpi-icon cd-perf-kpi-icon-cyan">
                                <i class="fas fa-clock fa-solid"></i>
                            </div>
                            <span class="cd-perf-pill cd-perf-pill-good" id="perfKpiTimePill"><i class="fas fa-minus fa-solid" style="font-size:8px;"></i>—</span>
                        </div>
                        <div class="cd-perf-kpi-num" id="perfKpiTimeToHire">—</div>
                        <div class="cd-perf-kpi-lbl">Avg. Time to Hire</div>
                        <div class="cd-perf-kpi-sub">From submission to join</div>
                        <div class="cd-perf-kpi-comp" id="perfKpiTimeComp">vs. previous period</div>
                    </div>
                </div>

                <!-- ── 3. Analytics Grid Row 1 (Funnel, Candidates & Interviews, All Jobs Status) ── -->
                <div class="cd-perf-row-1">
                    <!-- Card 1: Hiring Pipeline Funnel -->
                    <div class="cd-perf-card">
                        <div class="cd-perf-card-hdr">
                            <div class="cd-perf-card-title">
                                <i class="fas fa-filter fa-solid text-success"></i> Hiring Pipeline Funnel
                            </div>
                            <select class="cd-perf-card-mini-select perf-sync-select" onchange="cdPerfSyncRange(this.value)">
                                <option value="12M" selected>Last 12 Months</option>
                                <option value="6M">Last 6 Months</option>
                                <option value="3M">Last 3 Months</option>
                                <option value="30D">Last 30 Days</option>
                                <option value="all">All Time</option>
                            </select>
                        </div>
                        <div class="cd-funnel-wrap">
                            <svg class="cd-funnel-svg" viewBox="0 0 100 130" preserveAspectRatio="none">
                                <path d="M 4,4 L 96,4 L 84,32 L 16,32 Z" fill="#3b82f6" />
                                <path d="M 17,36 L 83,36 L 73,64 L 27,64 Z" fill="#6366f1" />
                                <path d="M 28,68 L 72,68 L 63,96 L 37,96 Z" fill="#8b5cf6" />
                                <path d="M 38,100 L 62,100 L 55,116 L 45,116 Z" fill="#f59e0b" />
                                <path d="M 45,118 L 55,118 L 52,128 L 48,128 Z" fill="#10b981" />
                            </svg>
                            <div class="cd-funnel-list">
                                <div class="cd-funnel-item">
                                    <div class="cd-funnel-stage">
                                        <span class="cd-funnel-dot" style="background:#3b82f6;"></span>
                                        <span class="cd-funnel-val" id="funnelCountCand">0</span>
                                        <span>Candidates</span>
                                    </div>
                                    <span class="cd-funnel-pct" id="funnelPctCand">100%</span>
                                </div>
                                <div class="cd-funnel-item">
                                    <div class="cd-funnel-stage">
                                        <span class="cd-funnel-dot" style="background:#6366f1;"></span>
                                        <span class="cd-funnel-val" id="funnelCountScreened">0</span>
                                        <span>Screened</span>
                                    </div>
                                    <span class="cd-funnel-pct" id="funnelPctScreened">0%</span>
                                </div>
                                <div class="cd-funnel-item">
                                    <div class="cd-funnel-stage">
                                        <span class="cd-funnel-dot" style="background:#8b5cf6;"></span>
                                        <span class="cd-funnel-val" id="funnelCountInt">0</span>
                                        <span>Interviews</span>
                                    </div>
                                    <span class="cd-funnel-pct" id="funnelPctInt">0%</span>
                                </div>
                                <div class="cd-funnel-item">
                                    <div class="cd-funnel-stage">
                                        <span class="cd-funnel-dot" style="background:#f59e0b;"></span>
                                        <span class="cd-funnel-val" id="funnelCountOff">0</span>
                                        <span>Offers</span>
                                    </div>
                                    <span class="cd-funnel-pct" id="funnelPctOff">0%</span>
                                </div>
                                <div class="cd-funnel-item">
                                    <div class="cd-funnel-stage">
                                        <span class="cd-funnel-dot" style="background:#10b981;"></span>
                                        <span class="cd-funnel-val" id="funnelCountJoin">0</span>
                                        <span>Joined</span>
                                    </div>
                                    <span class="cd-funnel-pct" id="funnelPctJoin">0%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Candidates & Interviews (Month to Year) -->
                    <div class="cd-perf-card">
                        <div class="cd-perf-card-hdr">
                            <div class="cd-perf-card-title">
                                <i class="fas fa-chart-line fa-solid text-success"></i> Candidates &amp; Interviews (Month to Year)
                            </div>
                            <select class="cd-perf-card-mini-select perf-sync-select" onchange="cdPerfSyncRange(this.value)">
                                <option value="12M" selected>Last 12 Months</option>
                                <option value="6M">Last 6 Months</option>
                                <option value="3M">Last 3 Months</option>
                                <option value="30D">Last 30 Days</option>
                                <option value="all">All Time</option>
                            </select>
                        </div>
                        <div class="cd-perf-legend">
                            <span class="cd-perf-legend-item">
                                <span class="cd-perf-legend-dot" style="background:#10b981;"></span> Candidates Submitted
                            </span>
                            <span class="cd-perf-legend-item">
                                <span class="cd-perf-legend-dot" style="background:#8b5cf6;"></span> Interviewed Candidates
                            </span>
                        </div>
                        <div style="position:relative; height:150px; width:100%;">
                            <canvas id="chartCandidatesInterviews"></canvas>
                        </div>
                    </div>

                    <!-- Card 3: All Jobs Status (Hiring, Hold, Closed) -->
                    <div class="cd-perf-card">
                        <div class="cd-perf-card-hdr">
                            <div class="cd-perf-card-title">
                                <i class="fas fa-briefcase fa-solid text-success"></i> All Jobs Status (Hiring, Hold, Closed)
                            </div>
                            <select class="cd-perf-card-mini-select perf-sync-select" onchange="cdPerfSyncRange(this.value)">
                                <option value="12M" selected>Last 12 Months</option>
                                <option value="6M">Last 6 Months</option>
                                <option value="3M">Last 3 Months</option>
                                <option value="30D">Last 30 Days</option>
                                <option value="all">All Time</option>
                            </select>
                        </div>
                        <div class="cd-perf-legend">
                            <span class="cd-perf-legend-sq" style="background:#10b981;"></span> Hiring (Active)
                            <span class="cd-perf-legend-sq" style="background:#f59e0b;"></span> On Hold
                            <span class="cd-perf-legend-sq" style="background:#ef4444;"></span> Closed
                        </div>
                        <div style="position:relative; height:150px; width:100%;">
                            <canvas id="chartAllJobsStatus"></canvas>
                        </div>
                    </div>
                </div>

                <!-- ── 4. Analytics Grid Row 2 (Offers & Joins, Job Type Performance, Time to Hire) ── -->
                <div class="cd-perf-row-2">
                    <!-- Card 1: Offers & Joinings (Month to Year) -->
                    <div class="cd-perf-card">
                        <div class="cd-perf-card-hdr">
                            <div class="cd-perf-card-title">
                                <i class="fas fa-file-alt fa-solid text-warning"></i> Offers &amp; Joinings (Month to Year)
                            </div>
                            <select class="cd-perf-card-mini-select perf-sync-select" onchange="cdPerfSyncRange(this.value)">
                                <option value="12M" selected>Last 12 Months</option>
                                <option value="6M">Last 6 Months</option>
                                <option value="3M">Last 3 Months</option>
                                <option value="30D">Last 30 Days</option>
                                <option value="all">All Time</option>
                            </select>
                        </div>
                        <div class="cd-perf-legend">
                            <span class="cd-perf-legend-item">
                                <span class="cd-perf-legend-sq" style="background:#f59e0b;"></span> Offers Generated
                            </span>
                            <span class="cd-perf-legend-item">
                                <span class="cd-perf-legend-dot" style="background:#10b981;"></span> Successful Joins
                            </span>
                        </div>
                        <div style="position:relative; height:150px; width:100%;">
                            <canvas id="chartOffersJoinings"></canvas>
                        </div>
                    </div>

                    <!-- Card 2: Job Type Performance -->
                    <div class="cd-perf-card">
                        <div class="cd-perf-card-hdr">
                            <div class="cd-perf-card-title">
                                <i class="fas fa-sliders-h fa-solid text-success"></i> Job Type Performance
                            </div>
                            <select class="cd-perf-card-mini-select perf-sync-select" onchange="cdPerfSyncRange(this.value)">
                                <option value="12M" selected>Last 12 Months</option>
                                <option value="6M">Last 6 Months</option>
                                <option value="3M">Last 3 Months</option>
                                <option value="30D">Last 30 Days</option>
                                <option value="all">All Time</option>
                            </select>
                        </div>
                        <div class="cd-donut-wrap">
                            <div class="cd-donut-chart-box">
                                <canvas id="chartJobType" width="110" height="110"></canvas>
                                <div class="cd-donut-center">
                                    <div class="cd-donut-center-num" id="donutTotalJobs">0</div>
                                    <div class="cd-donut-center-lbl">Active Jobs</div>
                                </div>
                            </div>
                            <div class="cd-donut-list">
                                <div class="cd-donut-item">
                                    <div class="cd-donut-item-left">
                                        <span class="cd-funnel-dot" style="background:#10b981;"></span>
                                        <span>Full-Time</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="cd-donut-item-count" id="donutCountFulltime">0</span>
                                        <span class="cd-donut-item-pct" id="donutPctFulltime">0%</span>
                                    </div>
                                </div>
                                <div class="cd-donut-item">
                                    <div class="cd-donut-item-left">
                                        <span class="cd-funnel-dot" style="background:#8b5cf6;"></span>
                                        <span>Contract</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="cd-donut-item-count" id="donutCountContract">0</span>
                                        <span class="cd-donut-item-pct" id="donutPctContract">0%</span>
                                    </div>
                                </div>
                                <div class="cd-donut-item">
                                    <div class="cd-donut-item-left">
                                        <span class="cd-funnel-dot" style="background:#f59e0b;"></span>
                                        <span>Freelance</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="cd-donut-item-count" id="donutCountFreelance">0</span>
                                        <span class="cd-donut-item-pct" id="donutPctFreelance">0%</span>
                                    </div>
                                </div>
                                <div class="cd-donut-item">
                                    <div class="cd-donut-item-left">
                                        <span class="cd-funnel-dot" style="background:#06b6d4;"></span>
                                        <span>Part-Time</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="cd-donut-item-count" id="donutCountPartTime">0</span>
                                        <span class="cd-donut-item-pct" id="donutPctPartTime">0%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Time to Hire Trend (Days) -->
                    <div class="cd-perf-card">
                        <div class="cd-perf-card-hdr">
                            <div class="cd-perf-card-title">
                                <i class="fas fa-clock fa-solid text-success"></i> Time to Hire Trend (Days)
                            </div>
                            <select class="cd-perf-card-mini-select perf-sync-select" onchange="cdPerfSyncRange(this.value)">
                                <option value="12M" selected>Last 12 Months</option>
                                <option value="6M">Last 6 Months</option>
                                <option value="3M">Last 3 Months</option>
                                <option value="30D">Last 30 Days</option>
                                <option value="all">All Time</option>
                            </select>
                        </div>
                        <div style="position:relative; height:150px; width:100%;">
                            <canvas id="chartTimeToHire"></canvas>
                        </div>
                    </div>
                </div>

                <!-- ── 5. Analytics Grid Row 3 (Top Roles & Recruiter Performance) ── -->
                <div class="cd-perf-row-3">
                    <!-- Card 1: Top Hiring Roles -->
                    <div class="cd-perf-card">
                        <div class="cd-perf-card-hdr">
                            <div class="cd-perf-card-title">
                                <i class="fas fa-briefcase fa-solid text-success"></i> Top Hiring Roles
                            </div>
                            <a href="javascript:void(0);" onclick="triggerDrawerTab('requisitions', event)" class="cd-card-hdr-link">View All Jobs <i class="fas fa-arrow-right fa-solid ms-1" style="font-size:8.5px;"></i></a>
                        </div>
                        <div style="overflow-x:auto;">
                            <table class="cd-perf-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Job Title</th>
                                        <th>Candidates</th>
                                        <th>Interviews</th>
                                        <th>Offers</th>
                                        <th>Joins</th>
                                        <th>Conversion</th>
                                    </tr>
                                </thead>
                                <tbody id="perfTopRolesTbody">
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="fas fa-spinner fa-spin text-success me-2"></i>Loading live requisition metrics...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Card 2: Recruiter Performance -->
                    <div class="cd-perf-card">
                        <div class="cd-perf-card-hdr">
                            <div class="cd-perf-card-title">
                                <i class="fas fa-users fa-solid text-success"></i> Recruiter Performance
                            </div>
                            <a href="javascript:void(0);" onclick="triggerDrawerTab('candidate_list', event)" class="cd-card-hdr-link">View All Recruiters <i class="fas fa-arrow-right fa-solid ms-1" style="font-size:8.5px;"></i></a>
                        </div>
                        <div style="overflow-x:auto;">
                            <table class="cd-perf-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Recruiter</th>
                                        <th>Candidates</th>
                                        <th>Interviews</th>
                                        <th>Offers</th>
                                        <th>Joins</th>
                                        <th>Success Rate</th>
                                    </tr>
                                </thead>
                                <tbody id="perfRecruitersTbody">
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="fas fa-spinner fa-spin text-success me-2"></i>Loading live recruiter performance...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ── 6. Additional Intelligence & Insights ── -->
                <div class="cd-perf-intelligence-grid">
                    <div class="cd-perf-intel-card">
                        <div class="cd-perf-intel-lbl"><i class="fas fa-bolt fa-solid text-warning me-1"></i> Hiring Velocity</div>
                        <div class="cd-perf-intel-val" id="perfIntelVelocity">—</div>
                        <div class="cd-perf-intel-sub" id="perfIntelVelocitySub">Average cycle duration</div>
                    </div>
                    <div class="cd-perf-intel-card">
                        <div class="cd-perf-intel-lbl"><i class="fas fa-filter fa-solid text-success me-1"></i> Interview Conversion</div>
                        <div class="cd-perf-intel-val" id="perfIntelIntConv">—</div>
                        <div class="cd-perf-intel-sub" id="perfIntelIntConvSub">Stage conversion yield</div>
                    </div>
                    <div class="cd-perf-intel-card">
                        <div class="cd-perf-intel-lbl"><i class="fas fa-file-signature fa-solid text-info me-1"></i> Offer Conversion</div>
                        <div class="cd-perf-intel-val" id="perfIntelOfferConv">—</div>
                        <div class="cd-perf-intel-sub" id="perfIntelOfferConvSub">Interview to offer ratio</div>
                    </div>
                    <div class="cd-perf-intel-card">
                        <div class="cd-perf-intel-lbl"><i class="fas fa-handshake fa-solid text-success me-1"></i> Cumulative Yield</div>
                        <div class="cd-perf-intel-val" id="perfIntelJoinRatio">—</div>
                        <div class="cd-perf-intel-sub" id="perfIntelJoinRatioSub">Placements / Submissions</div>
                    </div>
                </div>

                <div class="cd-perf-insights-card">
                    <div class="cd-perf-insights-hdr">
                        <i class="fas fa-lightbulb fa-solid text-warning"></i> Performance Intelligence &amp; Actionable Insights
                    </div>
                    <div class="cd-perf-insights-grid" id="perfInsightsGrid">
                        <div><i class="fas fa-spinner fa-spin text-success me-1"></i> Loading intelligent recruitment heuristics...</div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<script>
    // ══════════════════════════════════════════════════════════════════════════
    // Agency Drawer Reactive Orchestration Engine (Rule 17 Isolated)
    // CQRS Architecture: 100% Elasticsearch Read Path / Zero MySQL Reads
    // ══════════════════════════════════════════════════════════════════════════

    var agencyCompanyId = {{ (int)$agencyId }};
    var currentAuthCompanyId = {{ (int)(auth()->user()->company_id ?? 0) }};

    window.agencyTabLoadedState = window.agencyTabLoadedState || {};
    window.perfChartInstances = window.perfChartInstances || {};
    window.perfClientCache = window.perfClientCache || {};
    window.perfCurrentRange = '12M';
    window.perfCurrentCompare = 'previous_period';
    window.perfIsLoading = false;
    window.agencyPerfClientCache = window.perfClientCache;
    window.agencyPerfChartInstances = window.perfChartInstances;
    window.agencyPerfCurrentRange = window.perfCurrentRange;
    window.agencyPerfCurrentCompare = window.perfCurrentCompare;
    window.agencyPerfIsLoading = window.perfIsLoading;
    window.currentAgencyJobStatus = 'all';
    window.currentAgencyOjSegment = 'all';

    // ── 1. Master Tab Switcher & Orchestration ──
    window.triggerDrawerTab = function(tabId, e) {
        if (e && typeof e.preventDefault === 'function') e.preventDefault();
        tabId = String(tabId || '').replace(/^#/, '');
        if (!tabId) return;

        // 1. Activate tab pill button
        $('.cd-drawer-tabs-list .cd-tab-pill-btn').removeClass('active').attr('aria-selected', 'false');
        $(`.cd-drawer-tabs-list .cd-tab-pill-btn[href="#${tabId}"]`).addClass('active').attr('aria-selected', 'true');

        // 2. Activate matching tab pane
        $('.cd-tab-content-wrapper .tab-pane').removeClass('active show');
        $(`#${tabId}`).addClass('active show');

        // 3. Load tab content via authoritative reactive store
        window.loadAgencyTabContent(tabId, false);
    };

    // Bootstrap nav tab click delegation
    $(document).off('click.agencyTabPill', '.cd-drawer-tabs-list .cd-tab-pill-btn');
    $(document).on('click.agencyTabPill', '.cd-drawer-tabs-list .cd-tab-pill-btn', function(e) {
        var href = $(this).attr('href');
        if (href && href.startsWith('#')) {
            e.preventDefault();
            window.triggerDrawerTab(href);
        }
    });

    // Hero KPI click delegation
    $(document).off('click.agencyKpiNav', '.cd-kpi-nav-trigger');
    $(document).on('click.agencyKpiNav', '.cd-kpi-nav-trigger', function(e) {
        var href = $(this).attr('href');
        if (href && href.startsWith('#')) {
            e.preventDefault();
            window.triggerDrawerTab(href);
        }
    });

    // Master Tab Content Coordinator
    window.loadAgencyTabContent = function(targetTab, force) {
        targetTab = String(targetTab || '').replace(/^#/, '').toLowerCase();
        var cacheKey = agencyCompanyId + '_' + targetTab;

        if (targetTab === 'recent_activity') {
            if (window.ClientActivityManager && typeof window.ClientActivityManager.initClient === 'function') {
                try {
                    window.ClientActivityManager.initClient(agencyCompanyId, false, agencyCompanyId, false, 'agency', agencyCompanyId);
                } catch(e){}
            } else if (window.AgencyActivityManager && typeof window.AgencyActivityManager.loadActivity === 'function') {
                window.AgencyActivityManager.loadActivity(false);
            }
            return;
        }

        if (targetTab === 'requisitions' || targetTab === 'jobs') {
            if (force || !window.agencyTabLoadedState[cacheKey]) {
                window.agencyTabLoadedState[cacheKey] = true;
                fetchAgencyJobs(1, $('#agencyJobSearchInput').val() || '', window.currentAgencyJobStatus || 'all', force);
            }
            return;
        }

        if (targetTab === 'candidate_list' || targetTab === 'candidates') {
            if (window.ClientCandidateStoreAgency) {
                if (window.ClientCandidateStoreAgency.clientId !== agencyCompanyId) {
                    window.ClientCandidateStoreAgency.init(currentAuthCompanyId, agencyCompanyId, 'agency');
                } else if (force || !window.agencyTabLoadedState[cacheKey]) {
                    window.agencyTabLoadedState[cacheKey] = true;
                    window.ClientCandidateStoreAgency.fetch(1);
                }
            }
            return;
        }

        if (targetTab === 'interview_tracker' || targetTab === 'interviews') {
            if (window.ClientInterviewStoreAgency) {
                if (force || !window.agencyTabLoadedState[cacheKey]) {
                    window.agencyTabLoadedState[cacheKey] = true;
                    window.ClientInterviewStoreAgency.fetch(agencyCompanyId, null, force, $('#agencyItSearchInput').val() || '');
                }
            }
            return;
        }

        if (targetTab === 'candidate_joining' || targetTab === 'offers') {
            if (force || !window.agencyTabLoadedState[cacheKey]) {
                window.agencyTabLoadedState[cacheKey] = true;
                agencyfetchCandidatesJoin(1, $('#agencyOjSearchInput').val() || '', window.currentAgencyOjSegment || 'all');
            }
            return;
        }

        if (targetTab === 'candidatedashboard' || targetTab === 'performance') {
            if (force || !window.agencyTabLoadedState[cacheKey]) {
                window.agencyTabLoadedState[cacheKey] = true;
                agencyPerfFetchData(window.agencyPerfCurrentRange, window.agencyPerfCurrentCompare, force);
            }
            return;
        }
    };

    // ── 2. Tab 4: Jobs Reactive Engine ──
    window._activeAgencyJobsXhr = null;
    function fetchAgencyJobs(page = 1, search = '', status = 'all', force = false) {
        var $jobsList = $('#agencyJobsListajax');
        if (!$jobsList.length) return;

        if (window._activeAgencyJobsXhr && window._activeAgencyJobsXhr.readyState !== 4) {
            try { window._activeAgencyJobsXhr.abort(); } catch(e){}
        }

        if (force || $jobsList.children().length === 0) {
            $jobsList.html(`
                <div class="d-flex flex-column align-items-center justify-content-center py-10" style="min-height:200px;">
                    <div class="spinner-border text-primary mb-3" style="width:2rem;height:2rem;" role="status"></div>
                    <span class="text-gray-600 font-poppins fs-7 fw-semibold">Loading open shared jobs...</span>
                </div>
            `);
        }

        window._activeAgencyJobsXhr = $.ajax({
            url: `{{ url('service_company/jobs_side_bar_job_ajax/data') }}`,
            type: 'GET',
            data: {
                search: '',
                status: 'all',
                per_page: 500,
                client_id: agencyCompanyId,
                partner_company_id: agencyCompanyId,
                client_type: 'agency',
                custom: false
            },
            success: function(response) {
                if (!response || !response.html) {
                    $jobsList.html(`
                        <div class="cd-empty-state">
                            <div class="cd-empty-icon"><i class="fas fa-briefcase"></i></div>
                            <div class="cd-empty-title">No Jobs Shared Yet</div>
                            <div class="cd-empty-desc">Share open requisitions with {{ addslashes($detailName) }} to accelerate sourcing.</div>
                        </div>
                    `);
                    return;
                }

                $jobsList.html(response.html);
                $('#paginationAgencyJobs').empty(); // 100% client-side memory virtual pagination

                applyAgencyJobFilter(status || window.currentAgencyJobStatus || 'all');
            },
            error: function(xhr, st) {
                if (st === 'abort') return;
                $jobsList.html(`
                    <div class="cd-jobs-empty text-center py-8">
                        <i class="fas fa-exclamation-circle fa-2x mb-3 text-danger d-block"></i>
                        <div class="fw-bold fs-6 text-gray-700 mb-1">Failed to load shared jobs</div>
                        <div class="text-muted fs-7">Please refresh or try again.</div>
                    </div>
                `);
            }
        });
        return window._activeAgencyJobsXhr;
    }
    window.fetchAgencyJobs = fetchAgencyJobs;

    function applyAgencyJobFilter(overrideStatus) {
        var $pane = $('#requisitions');
        var $container = $('#agencyJobsListajax');
        if (!$container.length) return;

        var rawSearch = ($pane.find('#agencyJobSearchInput').val() || '').trim().toLowerCase();
        var searchWords = rawSearch.length > 0 ? rawSearch.split(/\s+/).filter(Boolean) : [];

        var activeBtn = $pane.find('.js-agency-job-tab.active');
        var targetStatus = (overrideStatus || (activeBtn.length ? activeBtn.data('status') : (window.currentAgencyJobStatus || 'all')) || 'all').toLowerCase();

        var totalCount = 0;
        var hiringCount = 0;
        var holdCount = 0;
        var closedCount = 0;
        var visibleCount = 0;

        $container.find('.cd-job-card').each(function() {
            var $card = $(this);
            var cardStatus = ($card.attr('data-status') || $card.data('status') || 'active').toString().toLowerCase();

            totalCount++;
            if (cardStatus === 'active' || cardStatus === 'hiring') hiringCount++;
            else if (cardStatus === 'hold') holdCount++;
            else if (cardStatus === 'closed' || cardStatus === 'inactive') closedCount++;

            var matchesStatus = false;
            if (targetStatus === 'all') matchesStatus = true;
            else if (targetStatus === 'hiring' || targetStatus === 'active') matchesStatus = (cardStatus === 'active' || cardStatus === 'hiring');
            else if (targetStatus === 'hold') matchesStatus = (cardStatus === 'hold');
            else if (targetStatus === 'closed' || targetStatus === 'inactive') matchesStatus = (cardStatus === 'closed' || cardStatus === 'inactive');

            var matchesSearch = true;
            if (matchesStatus && searchWords.length > 0) {
                var cardText = ($card.text() || '').toLowerCase();
                for (var i = 0; i < searchWords.length; i++) {
                    if (cardText.indexOf(searchWords[i]) === -1) {
                        matchesSearch = false;
                        break;
                    }
                }
            }

            if (matchesStatus && matchesSearch) {
                $card.show();
                visibleCount++;
            } else {
                $card.hide();
            }
        });

        // Sync Subtab Counters
        $('#agencyJobCountAll').text(totalCount);
        $('#agencyJobCountHiring').text(hiringCount);
        $('#agencyJobCountHold').text(holdCount);
        $('#agencyJobCountClosed').text(closedCount);
        $('#agencyJobHeaderBadge').text(totalCount + ' Jobs');
        $('#agencyKpiAllJobs').text(totalCount);
        $('#agencyKpiActiveJobs').text(hiringCount);
        $('#agencyTabBadgeJobs').text(totalCount);

        var $emptyMsg = $container.find('.cd-jobs-client-empty');
        if (visibleCount === 0 && totalCount > 0) {
            if (!$emptyMsg.length) {
                $container.append(`
                    <div class="cd-jobs-client-empty text-center py-8">
                        <i class="fas fa-search fa-2x mb-2 text-muted d-block opacity-50"></i>
                        <div class="fw-semibold fs-7 text-gray-600">No jobs match your filter criteria.</div>
                    </div>
                `);
            } else {
                $emptyMsg.show();
            }
        } else if ($emptyMsg.length) {
            $emptyMsg.hide();
        }
    }

    $(document).off('click.agencyJobTab', '.js-agency-job-tab');
    $(document).on('click.agencyJobTab', '.js-agency-job-tab', function(e) {
        e.preventDefault();
        $('.js-agency-job-tab').removeClass('active');
        $(this).addClass('active');
        var status = $(this).data('status');
        window.currentAgencyJobStatus = status;
        applyAgencyJobFilter(status);
    });

    var agencyJobSearchTimer = null;
    $(document).off('input.agencyJobSearch', '#agencyJobSearchInput');
    $(document).on('input.agencyJobSearch', '#agencyJobSearchInput', function() {
        var val = $(this).val().trim();
        $('#btnAgencyJobSearchClear').toggle(val.length > 0);
        clearTimeout(agencyJobSearchTimer);
        agencyJobSearchTimer = setTimeout(function() {
            applyAgencyJobFilter();
        }, 250);
    });

    $(document).off('click.agencyJobClear', '#btnAgencyJobSearchClear');
    $(document).on('click.agencyJobClear', '#btnAgencyJobSearchClear', function() {
        $('#agencyJobSearchInput').val('');
        $(this).hide();
        applyAgencyJobFilter();
    });

    $(document).off('click.agencyJobReset', '#btnAgencyJobReset');
    $(document).on('click.agencyJobReset', '#btnAgencyJobReset', function() {
        $('#agencyJobSearchInput').val('');
        $('#btnAgencyJobSearchClear').hide();
        window.currentAgencyJobStatus = 'all';
        $('.js-agency-job-tab').removeClass('active');
        $('.js-agency-job-tab[data-status="all"]').addClass('active');
        applyAgencyJobFilter('all');
    });

    // ── 3. Tab 5: Candidates Reactive Engine (ClientCandidateStoreAgency) ──
    window.ClientCandidateStoreAgency = {
        state: 'IDLE',
        companyId: null,
        clientId: null,
        clientType: 'agency',
        allCandidates: [],
        stageCounts: {
            sourcing: 0, screening: 0, evaluation: 0, negotiations: 0,
            offer: 0, joined: 0, rejected: 0
        },
        activeStage: 'sourcing',
        searchTerm: '',
        currentPage: 1,
        pageSize: 25,

        init: function(companyId, clientId, clientType) {
            this.companyId = companyId;
            this.clientId = clientId;
            this.clientType = clientType || 'agency';
            this.allCandidates = [];
            this.activeStage = 'sourcing';
            this.searchTerm = '';
            this.currentPage = 1;
            this.fetch(1);
        },

        fetch: function(page) {
            var self = this;
            this.currentPage = page || 1;
            this.state = 'LOADING';

            $('#cdCandSkeletonLoaderAgency').show();
            $('#cdCandidateTableWrapAgency').hide();
            $('#cdCandEmptyStateAgency').hide();

            $.ajax({
                url: '{{ url("service_company/direct-client-candidate-list") }}',
                type: 'GET',
                headers: { 'Accept': 'application/json' },
                data: {
                    clientid: this.clientId,
                    client_type: 'agency',
                    page: this.currentPage,
                    size: 100
                },
                success: function(res) {
                    self.allCandidates = (res.data || []).filter(function(cand) {
                        var jSid = parseInt(cand.job_master_status_id, 10);
                        var mSid = parseInt(cand.master_status_id, 10);
                        if (jSid === 8 || jSid === 9) return false;
                        if ((isNaN(jSid) || jSid <= 0) && (mSid === 8 || mSid === 9)) return false;
                        return true;
                    });
                    self.calculateStageCounts(self.allCandidates);
                    self.state = 'READY';

                    $('#cdCandSkeletonLoaderAgency').hide();
                    $('#cdCandidateTableWrapAgency').show();

                    self.updateStageBadges();
                    self.filterAndRender();
                },
                error: function(err) {
                    self.state = 'ERROR';
                    $('#cdCandSkeletonLoaderAgency').hide();
                    $('#agencyCandidatesStream').html(`
                        <div class="text-center py-8">
                            <i class="fas fa-exclamation-circle fa-2x mb-3 text-danger d-block"></i>
                            <div class="fw-bold fs-6 text-gray-700 mb-1">Failed to load candidates</div>
                            <div class="text-muted fs-7">Please refresh or try again.</div>
                        </div>
                    `);
                    $('#cdCandidateTableWrapAgency').show();
                }
            });
        },

        getCandidateStage: function(cand) {
            if (!cand) return 'unknown';

            // Authoritative pipeline stage: job_master_status_id first, then master_status_id
            var jSid = parseInt(cand.job_master_status_id, 10);
            var mSid = parseInt(cand.master_status_id, 10);

            // Strict Status 8 (In Review) and 9 (Archived) exclusion guard
            if (jSid === 8 || jSid === 9) return 'excluded';
            if ((isNaN(jSid) || jSid <= 0) && (mSid === 8 || mSid === 9)) return 'excluded';

            var sid = (!isNaN(jSid) && jSid > 0) ? jSid : ((!isNaN(mSid) && mSid > 0) ? mSid : 0);

            if (sid === 1) return 'sourcing';
            if (sid === 2) return 'screening';
            if (sid === 3) return 'evaluation';
            if (sid === 4) return 'negotiations';
            if (sid === 5) return 'offer';
            if (sid === 6) return 'joined';
            if (sid === 7) return 'rejected';
            if (sid === 8 || sid === 9) return 'excluded';

            var sName = String(cand.child_status_name || cand.master_status_name || cand.name || '').toLowerCase().trim();
            if (sName.indexOf('inreview') !== -1 || sName.indexOf('in review') !== -1 || sName.indexOf('archived') !== -1) return 'excluded';
            if (sName.indexOf('sourc') !== -1) return 'sourcing';
            if (sName.indexOf('screen') !== -1) return 'screening';
            if (sName.indexOf('eval') !== -1 || sName.indexOf('interview') !== -1) return 'evaluation';
            if (sName.indexOf('negot') !== -1) return 'negotiations';
            if (sName.indexOf('offer') !== -1) return 'offer';
            if (sName.indexOf('join') !== -1 || sName.indexOf('onboard') !== -1) return 'joined';
            if (sName.indexOf('reject') !== -1) return 'rejected';

            return 'unknown';
        },

        calculateStageCounts: function(candidates) {
            var counts = { sourcing: 0, screening: 0, evaluation: 0, negotiations: 0, offer: 0, joined: 0, rejected: 0 };
            var self = this;
            (candidates || []).forEach(function(cand) {
                var stage = self.getCandidateStage(cand);
                if (stage !== 'excluded' && stage !== 'unknown' && counts.hasOwnProperty(stage)) {
                    counts[stage]++;
                }
            });
            this.stageCounts = counts;
            return counts;
        },

        updateStageBadges: function() {
            var c = this.stageCounts || {};
            var total = (c.sourcing || 0) + (c.screening || 0) + (c.evaluation || 0) + (c.negotiations || 0) + (c.offer || 0) + (c.joined || 0) + (c.rejected || 0);

            $('#candTabCountSourcingAgency').text(c.sourcing || 0);
            $('#candTabCountScreeningAgency').text(c.screening || 0);
            $('#candTabCountEvaluationAgency').text(c.evaluation || 0);
            $('#candTabCountNegotiationsAgency').text(c.negotiations || 0);
            $('#candTabCountOfferAgency').text(c.offer || 0);
            $('#candTabCountJoinedAgency').text(c.joined || 0);
            $('#candTabCountRejectedAgency').text(c.rejected || 0);

            $('#cdAgencyCandCountText').text(total);
            $('#agencyKpiCandidates').text(total);
            $('#perfKpiCandidates').text(total);
            $('#agencyTabBadgeCandidates').text(total);
        },

        filterAndRender: function() {
            var self = this;
            var stage = this.activeStage || 'sourcing';
            var term = (this.searchTerm || '').toLowerCase().trim();

            var stageCandidates = (this.allCandidates || []).filter(function(cand) {
                var candStage = self.getCandidateStage(cand);
                if (candStage === 'excluded' || candStage === 'unknown') return false;
                if (!stage || stage === 'all') return true;
                return candStage === stage;
            });

            var filtered = stageCandidates;
            if (term) {
                filtered = stageCandidates.filter(function(cand) {
                    var skillsStr = '';
                    if (Array.isArray(cand.skills)) {
                        skillsStr = cand.skills.join(' ');
                    } else if (typeof cand.skills === 'string') {
                        skillsStr = cand.skills;
                    }
                    var corpus = [
                        cand.full_name,
                        cand.first_name,
                        cand.last_name,
                        cand.email,
                        cand.phone,
                        cand.current_designation,
                        cand.current_company,
                        cand.cur_company_name,
                        cand.company_name,
                        cand.client_name,
                        cand.title,
                        cand.job_title,
                        cand.city_name,
                        cand.state_name,
                        cand.country_name,
                        cand.location,
                        cand.captain_name,
                        cand.agency_name,
                        cand.submitted_by_display,
                        skillsStr,
                        String(cand.id || ''),
                        String(cand.candidate_id || ''),
                        String(cand.job_id || cand.jobId || '')
                    ].filter(Boolean).join(' ').toLowerCase();

                    return corpus.indexOf(term) !== -1;
                });
            }

            if (filtered.length === 0) {
                $('#agencyCandidatesStream').empty();
                $('#paginationAgencyCandidates').empty();
                var stageLabel = (this.activeStage && this.activeStage !== 'all')
                    ? (this.activeStage.charAt(0).toUpperCase() + this.activeStage.slice(1)) + ' stage'
                    : 'this agency';
                var emptyMsg = this.searchTerm
                    ? 'No candidates match "' + $('<div>').text(this.searchTerm).html() + '" in ' + stageLabel + '.'
                    : 'No candidates currently in ' + stageLabel + '.';
                $('#cdCandEmptyStateAgency .text-dark').text(emptyMsg);
                $('#cdCandEmptyStateAgency').show();
                $('#cdCandidateTableWrapAgency').hide();
                return;
            }

            $('#cdCandEmptyStateAgency').hide();
            $('#cdCandidateTableWrapAgency').show();
            var html = '';
            filtered.forEach(function(cand) {
                html += self.buildCandidateCard(cand);
            });
            $('#agencyCandidatesStream').html(html);
        },

        buildCandidateCard: function(row) {
            var cid = row.id || row.wiseId || row.candidate_id || 0;
            var jobId = row.jobId || row.job_id || 0;

            // Acronym-preserving sentence/title case formatter
            var formatWithAcronyms = function(str) {
                if (!str || typeof str !== 'string') return '';
                var s = str.trim();
                if (!s) return '';
                var acronyms = {
                    'php': 'PHP', 'aws': 'AWS', 'gcp': 'GCP', 'azure': 'Azure', 'api': 'API',
                    'sql': 'SQL', 'mysql': 'MySQL', 'nosql': 'NoSQL', 'ui': 'UI', 'ux': 'UX',
                    'qa': 'QA', 'hr': 'HR', 'ats': 'ATS', 'crm': 'CRM', 'erp': 'ERP',
                    'in': 'IN', 'us': 'US', 'uk': 'UK', 'uae': 'UAE', 'ca': 'CA', 'sg': 'SG', 'au': 'AU',
                    'b.tech': 'B.Tech', 'b.e': 'B.E', 'm.tech': 'M.Tech', 'm.e': 'M.E',
                    'bca': 'BCA', 'mca': 'MCA', 'mba': 'MBA', 'b.sc': 'B.Sc', 'm.sc': 'M.Sc',
                    'wfo': 'WFO', 'wfh': 'WFH', 'it': 'IT'
                };
                return s.split(/(\s+|[/\-+&])/).map(function(token) {
                    var lower = token.toLowerCase();
                    if (acronyms[lower]) return acronyms[lower];
                    if (token.length <= 1) return token.toUpperCase();
                    return token.charAt(0).toUpperCase() + token.slice(1).toLowerCase();
                }).join('');
            };

            // Word limiter helper: allows up to maxWords
            var limitWords = function(str, maxWords) {
                if (!str || typeof str !== 'string') return '';
                var words = str.trim().split(/\s+/);
                if (words.length <= maxWords) return str.trim();
                return words.slice(0, maxWords).join(' ') + '...';
            };

            // Relative time formatted helper
            var formatTimeAgo = function(createdVal) {
                if (!createdVal) return 'Recently';
                var time;
                if (typeof createdVal === 'number') {
                    time = (createdVal > 1e11) ? createdVal : createdVal * 1000;
                } else {
                    time = Date.parse(createdVal);
                }
                if (isNaN(time)) return 'Recently';
                var diff = Math.floor((Date.now() - time) / 1000);
                if (diff < 60) return 'Just now';
                if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
                if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
                if (diff < 2592000) {
                    var days = Math.floor(diff / 86400);
                    return days === 1 ? '1 day ago' : (days + ' days ago');
                }
                var months = Math.floor(diff / 2592000);
                return months === 1 ? '1 mo ago' : (months + ' mos ago');
            };

            // Experience number cleaner
            var cleanExpNum = function(val) {
                if (val === undefined || val === null || val === '') return null;
                var s = String(val).trim().replace(/\.+$/, '');
                var n = parseFloat(s);
                if (isNaN(n)) return s;
                return (n % 1 === 0) ? n.toString() : n.toFixed(1);
            };

            // Compact salary formatting
            var formatCompactSalary = function(val) {
                if (val === null || val === undefined || val === '') return null;
                var num = Number(String(val).replace(/,/g, '').trim());
                if (isNaN(num) || num <= 0) return null;
                if (num >= 1000) {
                    var kVal = num / 1000;
                    var formatted = (kVal % 1 === 0) ? kVal.toString() : kVal.toFixed(1).replace(/\.0$/, '');
                    return formatted + 'K';
                }
                return num.toLocaleString();
            };

            // 1. Sub-Card 1: Candidate Profile
            var rawFullName = (row.full_name || ((row.first_name || '') + ' ' + (row.last_name || ''))).trim() || 'Candidate';
            var rawDesignation = (row.current_designation || '----').trim();
            var currCompany = (row.current_company || '----').trim();

            var displayName = formatWithAcronyms(limitWords(rawFullName, 15));
            var displayDesig = formatWithAcronyms(limitWords(rawDesignation, 15));
            var currCompanyClean = formatWithAcronyms(limitWords(currCompany, 10));

            // 2. Sub-Card 2: Compensation & Experience
            var currencySymbol = row.current_ctc_cur || row.salary_currency || row.job_currency || '₹';
            if (currencySymbol === '0' || !currencySymbol || currencySymbol === 'INR') currencySymbol = '₹';
            if (currencySymbol === 'USD' || currencySymbol === '4') currencySymbol = '$';
            if (currencySymbol === 'EUR' || currencySymbol === '1') currencySymbol = '€';
            if (currencySymbol === 'GBP' || currencySymbol === '3') currencySymbol = '£';
            if (currencySymbol === 'AED' || currencySymbol === '5') currencySymbol = 'AED ';

            var ectcVal = formatCompactSalary(row.ectc_yearly || row.ectc || row.salary);
            var ctcVal = formatCompactSalary(row.ctc_yearly || row.ctc);
            var salaryPrimaryHtml = ectcVal
                ? `<span class="fw-bold text-dark">${currencySymbol}${ectcVal}</span> <span class="text-muted fs-8">/ Yr</span>`
                : (ctcVal ? `<span class="fw-bold text-dark">${currencySymbol}${ctcVal}</span> <span class="text-muted fs-8">/ Yr</span>` : '<span class="text-muted">----</span>');

            var totalExpNum = cleanExpNum(row.total_exp);
            var relExpNum = cleanExpNum(row.rel_exp);
            var totalExpStr = totalExpNum !== null ? (totalExpNum + ' Yrs Total') : '----';
            if (relExpNum !== null && relExpNum !== '0' && relExpNum !== totalExpNum) {
                totalExpStr += ` (${relExpNum} Rel)`;
            }

            var noticeText = 'Immediate';
            var noticeColorClass = 'cd-notice-immediate';
            var rawNotice = String(row.np_id || row.notice || '').trim();

            if (row.serving_notice_period_mode == 1 || (rawNotice && rawNotice.includes('-'))) {
                var cleanDate = rawNotice.replace('SNP LWD', '').trim();
                noticeText = 'SNP LWD ' + cleanDate;
                noticeColorClass = 'cd-notice-snp';
            } else if (rawNotice === 'Immediate' || rawNotice === '0' || rawNotice === '') {
                noticeText = 'Immediate';
                noticeColorClass = 'cd-notice-immediate';
            } else if (!isNaN(Number(rawNotice))) {
                noticeText = rawNotice + ' Days';
                noticeColorClass = 'cd-notice-days';
            } else {
                noticeText = rawNotice;
                noticeColorClass = 'cd-notice-default';
            }

            // 3. Sub-Card 3: Location & Education
            var city = row.city_name || row.city || '----';
            var country = row.country_name || '';
            var locLine1 = city;
            if (country && country !== '----') {
                var cShort = country.length > 2 ? country.substring(0, 2).toUpperCase() : country.toUpperCase();
                locLine1 += ', ' + cShort;
            }
            var locLine1Clean = formatWithAcronyms(locLine1);

            var rawWorkMode = String(row.job_mode || '').toLowerCase();
            var workModeClean = 'Work From Office';
            if (rawWorkMode.includes('remote') || rawWorkMode === '3') {
                workModeClean = 'Remote';
            } else if (rawWorkMode.includes('hybrid') || rawWorkMode === '2') {
                workModeClean = 'Hybrid';
            } else if (rawWorkMode.includes('office') || rawWorkMode.includes('wfo') || rawWorkMode === '1') {
                workModeClean = 'Work From Office';
            }

            var qualification = row.qualification_name || '----';
            var qualificationClean = formatWithAcronyms(limitWords(qualification, 8));

            // 4. Sub-Card 4: Target Job Requisition
            var jobTitle = row.title || row.job_title || ('Job #' + jobId);
            var jobTitleFormatted = formatWithAcronyms(limitWords(jobTitle, 15));
            var companyName = row.cur_company_name || row.client_name || row.company_name || 'Client';
            var companyNameFormatted = formatWithAcronyms(limitWords(companyName, 12));
            var jobUrl = jobId ? `{{ url('service_company/jobs') }}/${jobId}` : 'javascript:void(0);';

            var deptFormatted = formatWithAcronyms(row.industry_name || row.department || 'Technology');
            var jobTypeClean = (row.job_type || 'Full-Time');

            var scopeTag = 'Shared Requisition';
            var scopeCls = 'type-direct';
            if (row.client_type === 'internal') {
                scopeTag = 'Internal Client';
                scopeCls = 'type-internal';
            }

            var rawJobStatus = String(row.job_status || 'active').toLowerCase();
            var statusPillText = 'Hiring';
            var statusChipClass = 'status-hiring';
            if (rawJobStatus === 'active' || rawJobStatus === 'hiring') {
                statusPillText = 'Hiring';
                statusChipClass = 'status-hiring';
            } else if (rawJobStatus === 'hold') {
                statusPillText = 'Hold';
                statusChipClass = 'status-hold';
            } else if (rawJobStatus === 'inactive' || rawJobStatus === 'closed') {
                statusPillText = 'Closed';
                statusChipClass = 'status-closed';
            }

            var companyLogo = row.client_logo || row.company_logo || row.logo_logical_path || row.logo || '';
            var companyInitial = (companyName && companyName.length > 0) ? companyName.charAt(0).toUpperCase() : 'C';
            var logoHtml = '';
            if (companyLogo && companyLogo.length > 5) {
                logoHtml = `
                    <div class="cd-job-logo-wrap" title="${$('<div>').text(companyNameFormatted).html()}">
                        <img src="${companyLogo}" alt="${$('<div>').text(companyNameFormatted).html()}" class="cd-job-logo-img" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                        <div class="cd-job-logo-fallback" style="display:none;">${companyInitial}</div>
                    </div>
                `;
            } else {
                logoHtml = `
                    <div class="cd-job-logo-wrap" title="${$('<div>').text(companyNameFormatted).html()}">
                        <div class="cd-job-logo-fallback"><i class="fas fa-building text-primary"></i></div>
                    </div>
                `;
            }

            // 5. Sub-Card 5: Sourcing & Agency Attribution
            var captain = row.captain_name || (row.submitted_by_display ? row.submitted_by_display.split(' — ')[0] : (row.spokperson_name || 'Agency Lead'));
            var submitterCompany = row.agency_name || '{{ addslashes($detailName) }}';
            var captainFormatted = formatWithAcronyms(limitWords(captain, 12));
            var submitterFormatted = formatWithAcronyms(limitWords(submitterCompany, 12));
            var submittedAgo = formatTimeAgo(row.created_at);

            // 6. Sub-Card 6: Pipeline & Stage
            var canonicalStage = this.getCandidateStage(row);
            var stageLabels = {
                sourcing: 'Sourcing',
                screening: 'Screening',
                evaluation: 'Evaluation',
                negotiations: 'Negotiations',
                offer: 'Offered',
                joined: 'Joined',
                rejected: 'Rejected'
            };
            var stageName = row.master_status_name || stageLabels[canonicalStage] || 'Sourcing';
            var sLower = canonicalStage.toLowerCase();
            var stageChipStyle = 'background:#ecfeff;color:#0891b2;border:1px solid #a5f3fc;';
            if (sLower === 'screening') {
                stageChipStyle = 'background:#faf5ff;color:#9333ea;border:1px solid #e9d5ff;';
            } else if (sLower === 'evaluation') {
                stageChipStyle = 'background:#eef2ff;color:#4f46e5;border:1px solid #c7d2fe;';
            } else if (sLower === 'negotiations') {
                stageChipStyle = 'background:#fdf2f8;color:#db2777;border:1px solid #fbcfe8;';
            } else if (sLower === 'offer') {
                stageChipStyle = 'background:#fffbeb;color:#d97706;border:1px solid #fde68a;';
            } else if (sLower === 'joined') {
                stageChipStyle = 'background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;';
            } else if (sLower === 'rejected') {
                stageChipStyle = 'background:#fef2f2;color:#ef4444;border:1px solid #fecaca;';
            }
            var subStageText = row.child_status_name || 'Active';

            // 7. Sub-Card 7 (Bottom Strip): Skills, CV Match, LinkedIn Verified, Visa Status
            var matchPct = (row.skill_match !== undefined && row.skill_match !== null) ? Math.round(row.skill_match) : 0;
            var matchBadgeClass = 'cd-match-red';
            if (matchPct >= 85) {
                matchBadgeClass = 'cd-match-green';
            } else if (matchPct >= 50) {
                matchBadgeClass = 'cd-match-amber';
            }

            var skillsList = [];
            if (Array.isArray(row.skills)) {
                skillsList = row.skills;
            } else if (typeof row.skills === 'string' && row.skills.trim()) {
                skillsList = row.skills.split(',').map(function(s) { return s.trim(); }).filter(Boolean);
            }

            var skillsPillsHtml = '';
            if (skillsList.length > 0) {
                skillsList.forEach(function(s) {
                    var cleanSkill = $('<div>').text(formatWithAcronyms(s)).html();
                    skillsPillsHtml += `<span class="cd-skill-pill-cv">${cleanSkill}</span>`;
                });
            } else {
                skillsPillsHtml = `<span class="text-muted fs-8">No specific skills tagged</span>`;
            }

            var rawLinkedin = String(row.linkedin || row.ci_linkedin || '').trim();
            var linkedinHtml = '';
            if (rawLinkedin && rawLinkedin !== '0' && rawLinkedin.toLowerCase() !== 'none' && rawLinkedin.toLowerCase() !== 'null' && rawLinkedin !== '----') {
                var linkedinUrl = rawLinkedin;
                if (!linkedinUrl.startsWith('http://') && !linkedinUrl.startsWith('https://')) {
                    if (linkedinUrl.includes('linkedin.com')) {
                        linkedinUrl = 'https://' + linkedinUrl;
                    } else {
                        linkedinUrl = 'https://www.linkedin.com/in/' + linkedinUrl.replace(/^@/, '');
                    }
                }
                linkedinHtml = `
                    <a href="${linkedinUrl}" target="_blank" rel="noopener noreferrer" class="cd-cand-linkedin-pill" title="LinkedIn Profile Verified">
                        <i class="fab fa-linkedin text-primary"></i><span>LinkedIn Verified</span>
                    </a>
                `;
            }

            var rawVisa = String(row.visastatu || row.visa_status || row.visa || '').trim();
            var visaHtml = '';
            if (rawVisa && rawVisa !== '0' && rawVisa.toLowerCase() !== 'none' && rawVisa.toLowerCase() !== 'null' && rawVisa !== '----') {
                var visaCountryShort = '';
                var rawCountry = String(row.country_name || '').trim().toUpperCase();
                if (rawCountry && rawCountry !== '----') {
                    visaCountryShort = rawCountry.length > 2 ? rawCountry.substring(0, 2) : rawCountry;
                }

                var vLower = rawVisa.toLowerCase();
                if (vLower.includes('us') || vLower.includes('h-1b') || vLower.includes('h1b') || vLower.includes('green card') || vLower.includes('gc-ead') || vLower.includes('opt') || vLower.includes('cpt') || vLower.includes('tn permit')) {
                    if (!visaCountryShort) visaCountryShort = 'US';
                } else if (vLower.includes('canada') || vLower.includes('canadian')) {
                    if (!visaCountryShort) visaCountryShort = 'CA';
                } else if (vLower.includes('india') || vLower.includes('indian')) {
                    if (!visaCountryShort) visaCountryShort = 'IN';
                } else if (vLower.includes('uk') || vLower.includes('british')) {
                    if (!visaCountryShort) visaCountryShort = 'UK';
                } else if (vLower.includes('uae') || vLower.includes('dubai')) {
                    if (!visaCountryShort) visaCountryShort = 'UAE';
                } else if (vLower.includes('singapore')) {
                    if (!visaCountryShort) visaCountryShort = 'SG';
                } else if (vLower.includes('australia')) {
                    if (!visaCountryShort) visaCountryShort = 'AU';
                }
                if (!visaCountryShort) visaCountryShort = 'Visa';

                var cleanVisaName = rawVisa
                    .replace(/^(US|United States|Canada|Canadian|India|Indian|UK|UAE|Dubai|Singapore|Australia)\s+/i, '')
                    .trim();
                if (!cleanVisaName) cleanVisaName = rawVisa;
                cleanVisaName = formatWithAcronyms(cleanVisaName);

                var visaDisplay = `(${visaCountryShort}, ${cleanVisaName})`;
                visaHtml = `
                    <span class="cd-cand-visa-pill" title="Visa Status: ${$('<div>').text(rawVisa).html()}">
                        <i class="fas fa-passport text-muted"></i><span>${$('<div>').text(visaDisplay).html()}</span>
                    </span>
                `;
            }

            return `
                <div class="cd-cand-card" data-candidate-id="${cid}">
                    <div class="cd-cand-card-grid">
                        <!-- Sub-Card 1: CANDIDATE INFO -->
                        <div class="cd-cand-subcard cd-cand-subcard-info">
                            <div class="cd-cand-subcard-hdr">
                                <span class="cd-cand-subcard-label">CANDIDATE INFO</span>
                            </div>
                            <div class="cd-cand-subcard-body">
                                <div class="cd-cand-profile-row">
                                    <div class="cd-cand-profile-meta">
                                        <span class="cd-cand-name" title="${$('<div>').text(rawFullName).html()}">
                                            ${displayName}
                                        </span>
                                        <div class="cd-cand-desig" title="${$('<div>').text(rawDesignation).html()}">${displayDesig}</div>
                                        <div class="cd-cand-employer" title="${$('<div>').text(currCompany).html()}">
                                            <i class="fas fa-building text-muted me-1" style="font-size:9.5px;"></i>${currCompanyClean}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sub-Card 2: COMPENSATION & EXP -->
                        <div class="cd-cand-subcard cd-cand-subcard-comp">
                            <div class="cd-cand-subcard-hdr">
                                <span class="cd-cand-subcard-label">COMPENSATION &amp; EXP</span>
                            </div>
                            <div class="cd-cand-subcard-body">
                                <div class="cd-val-salary" title="${ectcVal || ctcVal || ''}">
                                    ${salaryPrimaryHtml}
                                </div>
                                <div class="cd-val-exp" title="${totalExpStr}">
                                    <i class="far fa-clock text-muted me-1" style="font-size:10px;"></i><span>${totalExpStr}</span>
                                </div>
                                <div class="cd-val-notice" title="${noticeText}">
                                    <span class="cd-notice-clean ${noticeColorClass}">${noticeText}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Sub-Card 3: LOCATION & DEGREE -->
                        <div class="cd-cand-subcard cd-cand-subcard-loc">
                            <div class="cd-cand-subcard-hdr">
                                <span class="cd-cand-subcard-label">LOCATION &amp; DEGREE</span>
                            </div>
                            <div class="cd-cand-subcard-body">
                                <div class="cd-loc-val" title="${$('<div>').text(locLine1Clean).html()}">
                                    <i class="fas fa-map-marker-alt text-muted me-1" style="font-size:10px;"></i>${locLine1Clean}
                                </div>
                                <div class="cd-loc-mode" title="${workModeClean}">
                                    <i class="fas fa-building text-muted me-1" style="font-size:9.5px;"></i>${workModeClean}
                                </div>
                                <div class="cd-loc-edu" title="${$('<div>').text(qualification).html()}">
                                    <i class="fas fa-graduation-cap text-muted me-1" style="font-size:10px;"></i>${qualificationClean}
                                </div>
                            </div>
                        </div>

                        <!-- Sub-Card 4: TARGET REQUISITION -->
                        <div class="cd-cand-subcard cd-cand-subcard-job">
                            <div class="cd-cand-subcard-hdr">
                                <span class="cd-cand-subcard-label">TARGET REQUISITION</span>
                                <div class="cd-header-badges">
                                    <span class="cd-badge-client-type ${scopeCls}">${scopeTag}</span>
                                    <span class="cd-status-badge ${statusChipClass}">${statusPillText}</span>
                                </div>
                            </div>
                            <div class="cd-cand-subcard-body">
                                <div class="cd-job-row">
                                    ${logoHtml}
                                    <div class="cd-job-meta-wrap">
                                        <a href="${jobUrl}" target="_blank" class="cd-job-title-link" title="${$('<div>').text(jobTitle).html()}">
                                            ${jobTitleFormatted}
                                        </a>
                                        <div class="cd-job-submeta">
                                            <span class="text-primary fw-bold">#${jobId}</span> &bull; <span>${deptFormatted}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="cd-job-footer-line">
                                    <span class="fw-semibold text-dark">${companyNameFormatted}</span> &bull; <span><i class="fas fa-briefcase text-primary me-1" style="font-size:9px;"></i>${jobTypeClean}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Sub-Card 5: SOURCING & AGENCY ATTRIBUTION -->
                        <div class="cd-cand-subcard cd-cand-subcard-recruiter">
                            <div class="cd-cand-subcard-hdr">
                                <span class="cd-cand-subcard-label"><i class="fa fa-user-tie me-1 text-muted"></i>SUBMITTED BY</span>
                            </div>
                            <div class="cd-cand-subcard-body">
                                <div class="cd-recruiter-name" title="${$('<div>').text(captain).html()}">
                                    <i class="fas fa-user-circle text-primary me-1" style="font-size:10.5px;"></i>${captainFormatted}
                                </div>
                                <div class="cd-recruiter-company" title="${$('<div>').text(submitterCompany).html()}">
                                    <i class="fas fa-building text-muted me-1" style="font-size:9.5px;"></i>${submitterFormatted}
                                </div>
                                <div class="cd-recruiter-time" title="${submittedAgo}">
                                    <i class="far fa-calendar-alt text-muted me-1" style="font-size:9.5px;"></i>Submitted: ${submittedAgo}
                                </div>
                            </div>
                        </div>

                        <!-- Sub-Card 6: PIPELINE & STAGE -->
                        <div class="cd-cand-subcard cd-cand-subcard-stage">
                            <div class="cd-cand-subcard-hdr">
                                <span class="cd-cand-subcard-label">PIPELINE &amp; STAGE</span>
                            </div>
                            <div class="cd-cand-subcard-body">
                                <div>
                                    <span class="cd-stage-chip cd-stage-chip-readonly" style="${stageChipStyle}; cursor: default !important; pointer-events: none !important;" title="Current Stage: ${stageName}">
                                        <span>${stageName}</span>
                                    </span>
                                </div>
                                <div class="cd-stage-sub" title="${subStageText}">
                                    <span class="text-muted">Sub-stage:</span> <span class="fw-medium text-dark">${subStageText}</span>
                                </div>
                                <div class="cd-stage-action">
                                    <a href="javascript:void(0);" class="cd-action-link js-view-profile-readonly" data-row-id="${cid}" title="View Profile">
                                        <i class="far fa-user text-primary me-1" style="font-size:9.5px;"></i>View Profile
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sub-Card 7: Full-Width Intelligence Strip -->
                    <div class="cd-cand-bottom-strip">
                        <div class="cd-cand-bottom-left">
                            <span class="cd-match-badge-cv ${matchBadgeClass}">Match ${matchPct}%</span>
                            <div class="cd-bottom-skills-scroll">
                                ${skillsPillsHtml}
                            </div>
                        </div>
                        <div class="cd-cand-bottom-right">
                            ${linkedinHtml}
                            ${visaHtml}
                        </div>
                    </div>
                </div>
            `;
        }
    };

    $(document).off('click.agencyCandStage', '.js-agency-cand-stage');
    $(document).on('click.agencyCandStage', '.js-agency-cand-stage', function(e) {
        e.preventDefault();
        $('.js-agency-cand-stage').removeClass('active');
        $(this).addClass('active');
        var stage = $(this).data('stage') || 'sourcing';
        window.ClientCandidateStoreAgency.activeStage = stage;
        window.ClientCandidateStoreAgency.filterAndRender();
    });

    var agencyCandSearchTimer = null;
    $(document).off('input.agencyCandSearch', '#cdAgencyCandSearchInput');
    $(document).on('input.agencyCandSearch', '#cdAgencyCandSearchInput', function() {
        var val = $(this).val().trim();
        $('#cdAgencyCandSearchClear').toggle(val.length > 0);
        clearTimeout(agencyCandSearchTimer);
        agencyCandSearchTimer = setTimeout(function() {
            window.ClientCandidateStoreAgency.searchTerm = val;
            window.ClientCandidateStoreAgency.filterAndRender();
        }, 250);
    });

    $(document).off('click.agencyCandClear', '#cdAgencyCandSearchClear');
    $(document).on('click.agencyCandClear', '#cdAgencyCandSearchClear', function() {
        $('#cdAgencyCandSearchInput').val('');
        $(this).hide();
        window.ClientCandidateStoreAgency.searchTerm = '';
        window.ClientCandidateStoreAgency.filterAndRender();
    });

    $(document).off('click.agencyCandReset', '#cdAgencyCandResetBtn');
    $(document).on('click.agencyCandReset', '#cdAgencyCandResetBtn', function() {
        $('#cdAgencyCandSearchInput').val('');
        $('#cdAgencyCandSearchClear').hide();
        window.ClientCandidateStoreAgency.searchTerm = '';
        window.ClientCandidateStoreAgency.activeStage = 'sourcing';
        $('.js-agency-cand-stage').removeClass('active');
        $('.js-agency-cand-stage[data-stage="sourcing"]').addClass('active');
        window.ClientCandidateStoreAgency.fetch(1);
    });

    // ── 4. Tab 6: Interviews Reactive Engine (ClientInterviewStoreAgency) ──
    window.ClientInterviewStoreAgency = (function() {
        const store = new Map();
        let currentSearch = '';
        let activeFilter = 'all';
        let pageSize = 10;
        let activeXhr = null;

        function fetch(clientId, cursor = null, force = false, search = null) {
            if (search !== null) currentSearch = search;
            var $list = $('#candidatetListinterviewtrackerAgency');
            if (!$list.length) return;

            if (activeXhr && activeXhr.readyState !== 4) {
                try { activeXhr.abort(); } catch(e){}
            }

            if (force || $list.children().length === 0) {
                $list.html(`
                    <div class="it-loading-card text-center py-8">
                        <span class="spinner-border spinner-border-sm text-primary me-2"></span>
                        <span class="text-gray-600 fs-7">Loading Authoritative Interview Projection...</span>
                    </div>
                `);
            }

            let url = `{{ url('service_company/direct-client-candidate-interview-tracker') }}?clientid=${clientId}&clienttype=agency&size=${pageSize}`;
            if (cursor) url += `&cursor=${encodeURIComponent(cursor)}`;
            if (currentSearch) url += `&search=${encodeURIComponent(currentSearch)}`;

            activeXhr = $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (res && Array.isArray(res.data)) {
                        renderCards(res.data);
                        renderPagination(res.next_cursor, res.total);
                        updateCounters(res.total, res.counts);
                    } else if (res && res.html) {
                        $list.html(res.html);
                    } else {
                        $list.html(`
                            <div class="cd-it-empty-state text-center py-12 px-4" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; margin: 12px 0;">
                                <div class="mb-3"><i class="fas fa-calendar-xmark text-muted" style="font-size: 38px; opacity: 0.35;"></i></div>
                                <div class="fw-bold text-dark fs-6 mb-1">No Interviews Scheduled</div>
                                <div class="text-muted fs-8" style="max-width: 440px; margin: 0 auto;">
                                    No interview schedules found for candidates submitted by {{ addslashes($detailName) }}.
                                </div>
                            </div>
                        `);
                    }
                },
                error: function(xhr, status) {
                    if (status === 'abort') return;
                    $list.html(`
                        <div class="text-center py-8">
                            <i class="fas fa-exclamation-circle fa-2x mb-3 text-danger d-block"></i>
                            <div class="fw-bold fs-6 text-gray-700 mb-1">Failed to load interviews</div>
                            <div class="text-muted fs-7">Please refresh or try again.</div>
                        </div>
                    `);
                }
            });
        }

        function renderCards(interviews) {
            var $list = $('#candidatetListinterviewtrackerAgency');
            if (!interviews || interviews.length === 0) {
                $list.html(`
                    <div class="cd-it-empty-state text-center py-12 px-4" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; margin: 12px 0;">
                        <div class="mb-3"><i class="fas fa-calendar-xmark text-muted" style="font-size: 38px; opacity: 0.35;"></i></div>
                        <div class="fw-bold text-dark fs-6 mb-1">No Interviews Scheduled</div>
                        <div class="text-muted fs-8" style="max-width: 440px; margin: 0 auto;">
                            No interview schedules found for candidates submitted by {{ addslashes($detailName) }}.
                        </div>
                    </div>
                `);
                return;
            }

            var html = '';
            interviews.forEach(function(it) {
                var cName = it.candidate_name || 'Candidate Profile';
                var initials = cName.split(' ').filter(Boolean).map(function(w){ return w[0]; }).join('').substring(0, 2).toUpperCase() || 'IT';
                var jTitle = it.job_title || 'Shared Position';
                var round = it.interview_stage || 'Round 1';
                var panel = it.interviewers_text || 'Technical Lead';
                var sDate = it.schedule_date ? it.schedule_date.substring(0, 10) : 'Upcoming';
                var sTime = it.schedule_time || 'IST';
                var meetUrl = it.meeting_url || it.url_link || '';

                var rawSt = (it.status || 'pending').toLowerCase();
                var statusLabel = 'SCHEDULED';
                var statusColor = 'warning';
                var cat = 'upcoming';

                if (rawSt === 'canceled' || rawSt === 'rejected') {
                    statusLabel = 'CANCELLED'; statusColor = 'danger'; cat = 'cancelled';
                } else if (rawSt === 'hired' || rawSt === 'completed' || it.feedback == 1) {
                    statusLabel = 'COMPLETED'; statusColor = 'success'; cat = 'completed';
                }

                html += `
                    <div class="cd-it-card js-agency-it-item" data-filter="${cat}">
                        <div class="cd-it-card-grid">
                            <div class="cd-it-subcard">
                                <div class="cd-it-subcard-hdr">
                                    <span class="cd-it-subcard-label">Candidate &amp; Job</span>
                                    <span class="badge badge-light-primary fw-bold fs-9">${round}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="cd-contact-avatar-box" style="width:36px; height:36px; min-width:36px; font-size:12px; background:#eff6ff; color:#009ef7; border:1px solid #bfdbfe;">
                                        ${initials}
                                    </div>
                                    <div style="min-width:0;">
                                        <div class="fw-bold text-dark fs-7 text-truncate js-it-searchable" title="${cName}">${cName}</div>
                                        <div class="text-muted fs-8 text-truncate">${jTitle}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="cd-it-subcard">
                                <div class="cd-it-subcard-hdr"><span class="cd-it-subcard-label">Schedule &amp; Time</span></div>
                                <div>
                                    <div class="fw-bold text-dark fs-8"><i class="far fa-calendar text-primary me-1"></i> ${sDate}</div>
                                    <div class="text-muted fs-9"><i class="far fa-clock text-muted me-1"></i> ${sTime} &bull; 45 Mins</div>
                                    <div class="text-muted fs-9">Virtual Interview</div>
                                </div>
                            </div>

                            <div class="cd-it-subcard">
                                <div class="cd-it-subcard-hdr"><span class="cd-it-subcard-label">Panelists</span></div>
                                <div>
                                    <div class="fw-bold text-dark fs-8 text-truncate">${panel}</div>
                                    <div class="text-muted fs-9 text-truncate">Tech Panelist</div>
                                </div>
                            </div>

                            <div class="cd-it-subcard">
                                <div class="cd-it-subcard-hdr">
                                    <span class="cd-it-subcard-label">Status &amp; Link</span>
                                    <span class="badge badge-light-${statusColor} text-${statusColor} fw-bold fs-9 text-uppercase">${statusLabel}</span>
                                </div>
                                <div class="mt-2">
                                    ${meetUrl && meetUrl !== '#' ? `
                                        <a href="${meetUrl}" target="_blank" class="btn btn-sm btn-primary py-1 px-3 fs-9 fw-bold w-100 text-center">
                                            <i class="fas fa-video me-1"></i> Join Meeting
                                        </a>
                                    ` : `
                                        <span class="btn btn-sm btn-light-primary py-1 px-3 fs-9 fw-semibold w-100 text-center disabled">
                                            <i class="fas fa-video me-1"></i> Virtual Interview
                                        </span>
                                    `}
                                </div>
                            </div>
                        </div>
                        <div class="cd-it-card-bottom">
                            <div><i class="fas fa-user-check text-muted me-1"></i> Sourced by {{ addslashes($detailName) }} &bull; ${round} ${jTitle}</div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge badge-light fs-9 text-muted border">Authoritative Schedule</span>
                            </div>
                        </div>
                    </div>
                `;
            });
            $list.html(html);
        }

        function renderPagination(nextCursor, total) {
            var $pagi = $('#paginationcandidateinterviewtrackerAgency');
            if (!nextCursor && !total) {
                $pagi.empty();
                return;
            }
            $pagi.html(`
                <div class="d-flex align-items-center justify-content-between p-2">
                    <span class="text-muted fs-8">Total: <strong>${total || 0}</strong> interviews</span>
                    ${nextCursor ? `
                        <button type="button" class="btn btn-sm btn-light-primary py-1 px-3 fs-8 fw-semibold" id="btnAgencyItNextCursor" data-cursor="${nextCursor}">
                            Next Page <i class="fas fa-chevron-right ms-1 fs-9"></i>
                        </button>
                    ` : ''}
                </div>
            `);
        }

        function updateCounters(total, counts) {
            var all = total || 0;
            $('#agencyItFilterCountAll').text(all);
            $('#agencyItHeaderBadge').text(all);
            $('#agencyKpiInterviews').text(all);
            $('#agencyTabBadgeInterviews').text(all);

            if (counts) {
                $('#agencyItFilterCountUpcoming').text(counts.upcoming || 0);
                $('#agencyItFilterCountPending').text(counts.pending || 0);
                $('#agencyItFilterCountCompleted').text(counts.completed || 0);
                $('#agencyItFilterCountCancelled').text(counts.cancelled || 0);
            }
        }

        return {
            fetch: fetch
        };
    })();

    $(document).off('click.agencyItNext', '#btnAgencyItNextCursor');
    $(document).on('click.agencyItNext', '#btnAgencyItNextCursor', function(e) {
        e.preventDefault();
        var cursor = $(this).data('cursor');
        window.ClientInterviewStoreAgency.fetch(agencyCompanyId, cursor, false, $('#agencyItSearchInput').val() || '');
    });

    $(document).off('click.agencyItFilter', '.js-agency-it-tab');
    $(document).on('click.agencyItFilter', '.js-agency-it-tab', function(e) {
        e.preventDefault();
        $('.js-agency-it-tab').removeClass('active');
        $(this).addClass('active');
        var filter = $(this).data('filter') || 'all';

        var $cards = $('#candidatetListinterviewtrackerAgency .js-agency-it-item');
        if (filter === 'all') {
            $cards.show();
        } else {
            $cards.each(function() {
                var f = $(this).data('filter');
                $(this).toggle(f === filter);
            });
        }
    });

    var agencyItSearchTimer = null;
    $(document).off('input.agencyItSearch', '#agencyItSearchInput');
    $(document).on('input.agencyItSearch', '#agencyItSearchInput', function() {
        var val = $(this).val().trim();
        $('#agencyItSearchClear').toggle(val.length > 0);
        clearTimeout(agencyItSearchTimer);
        agencyItSearchTimer = setTimeout(function() {
            window.ClientInterviewStoreAgency.fetch(agencyCompanyId, null, true, val);
        }, 250);
    });

    $(document).off('click.agencyItClear', '#agencyItSearchClear');
    $(document).on('click.agencyItClear', '#agencyItSearchClear', function() {
        $('#agencyItSearchInput').val('');
        $(this).hide();
        window.ClientInterviewStoreAgency.fetch(agencyCompanyId, null, true, '');
    });

    $(document).off('click.agencyItReset', '#agencyItResetBtn');
    $(document).on('click.agencyItReset', '#agencyItResetBtn', function() {
        $('#agencyItSearchInput').val('');
        $('#agencyItSearchClear').hide();
        $('.js-agency-it-tab').removeClass('active');
        $('.js-agency-it-tab[data-filter="all"]').addClass('active');
        window.ClientInterviewStoreAgency.fetch(agencyCompanyId, null, true, '');
    });

    // ── 5. Tab 7: Offers & Joins Reactive Engine ──
    window._activeAgencyOjXhr = null;
    function agencyfetchCandidatesJoin(page = 1, search = '', segment = null) {
        if (segment === null || segment === undefined) {
            segment = window.currentAgencyOjSegment || 'all';
        } else {
            window.currentAgencyOjSegment = segment;
        }

        if (search === null || search === undefined) {
            search = $('#agencyOjSearchInput').val() || '';
        }

        if (window._activeAgencyOjXhr && window._activeAgencyOjXhr.readyState !== 4) {
            try { window._activeAgencyOjXhr.abort(); } catch(e){}
        }

        window._activeAgencyOjXhr = $.ajax({
            url: `{{ url('service_company/direct-client-candidate-join-list') }}?page=${page}`,
            type: 'GET',
            data: {
                search: search,
                segment: segment,
                clientid: agencyCompanyId,
                clienttype: 'agency'
            },
            success: function(response) {
                if (response && response.html) {
                    $('#candidatetListJoinAgency').html(response.html);
                    $('#paginationcandidatejoinAgency').html(response.pagination || '');
                } else {
                    $('#candidatetListJoinAgency').html(`
                        <div class="cd-oj-empty-state text-center py-12 px-4" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; margin: 12px 0;">
                            <div class="mb-3"><i class="fas fa-handshake-slash text-muted" style="font-size: 38px; opacity: 0.35;"></i></div>
                            <div class="fw-bold text-dark fs-6 mb-1">No Offers or Joinings</div>
                            <div class="text-muted fs-8" style="max-width: 440px; margin: 0 auto;">
                                No candidate job offers or onboardings recorded yet for {{ addslashes($detailName) }}.
                            </div>
                        </div>
                    `);
                    $('#paginationcandidatejoinAgency').empty();
                }

                if (response && response.counts) {
                    var total = response.counts.all || 0;
                    $('#agencyOjCountAll').text(total);
                    $('#agencyOjCountOffers').text(response.counts.offers || 0);
                    $('#agencyOjCountJoining').text(response.counts.joining || 0);
                    $('#agencyOjCountJoined').text(response.counts.joined || 0);
                    $('#agencyOjCountText').text(total);
                    $('#agencyOjHeaderBadge').text(total + ' Offers & Joinings');
                    $('#agencyKpiOffers').text(response.counts.offers || 0);
                    $('#agencyKpiJoins').text(response.counts.joined || 0);
                    $('#agencyTabBadgeOffersJoins').text(total);
                }
            },
            error: function(xhr, status) {
                if (status === 'abort') return;
                $('#candidatetListJoinAgency').html(`
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-circle fa-2x mb-3 text-danger d-block"></i>
                        <div class="fw-bold fs-6 text-gray-700 mb-1">Failed to load offers &amp; joinings</div>
                        <div class="text-muted fs-7">Please refresh or try again.</div>
                    </div>
                `);
            }
        });
        return window._activeAgencyOjXhr;
    }
    window.agencyfetchCandidatesJoin = agencyfetchCandidatesJoin;

    $(document).off('click.agencyOjSegment', '.cd-oj-seg-btn-agency');
    $(document).on('click.agencyOjSegment', '.cd-oj-seg-btn-agency', function(e) {
        e.preventDefault();
        var segment = $(this).data('segment') || 'all';
        $('.cd-oj-seg-btn-agency').removeClass('active');
        $(this).addClass('active');
        window.currentAgencyOjSegment = segment;
        var search = $('#agencyOjSearchInput').val() || '';
        agencyfetchCandidatesJoin(1, search, segment);
    });

    var agencyOjSearchTimer = null;
    $(document).off('input.agencyOjSearch', '#agencyOjSearchInput');
    $(document).on('input.agencyOjSearch', '#agencyOjSearchInput', function() {
        var val = $(this).val().trim();
        $('#agencyOjSearchClear').toggle(val.length > 0);
        clearTimeout(agencyOjSearchTimer);
        agencyOjSearchTimer = setTimeout(function() {
            agencyfetchCandidatesJoin(1, val, window.currentAgencyOjSegment || 'all');
        }, 250);
    });

    $(document).off('click.agencyOjClear', '#agencyOjSearchClear');
    $(document).on('click.agencyOjClear', '#agencyOjSearchClear', function() {
        $('#agencyOjSearchInput').val('');
        $(this).hide();
        agencyfetchCandidatesJoin(1, '', window.currentAgencyOjSegment || 'all');
    });

    $(document).off('click.agencyOjReset', '#agencyOjResetBtn');
    $(document).on('click.agencyOjReset', '#agencyOjResetBtn', function() {
        $('#agencyOjSearchInput').val('');
        $('#agencyOjSearchClear').hide();
        window.currentAgencyOjSegment = 'all';
        $('.cd-oj-seg-btn-agency').removeClass('active');
        $('.cd-oj-seg-btn-agency[data-segment="all"]').addClass('active');
        agencyfetchCandidatesJoin(1, '', 'all');
    });

    // ── 6. Tab 8: Performance Analytics Engine (Agency Scope) ──
    window.perfChartInstances = window.perfChartInstances || {};
    window.perfClientCache = window.perfClientCache || {};
    window.perfCurrentRange = '12M';
    window.perfCurrentCompare = 'previous_period';
    window.perfIsLoading = false;
    var perfClientId = agencyCompanyId;
    var perfBaseApiUrl = "{{ url('service_company/clients/api/v1/performance-analytics') }}/" + perfClientId;

    function ensureChartJsLoaded(callback) {
        if (typeof Chart !== 'undefined') {
            callback();
            return;
        }
        var script = document.getElementById('chartjs-cdn-script');
        if (!script) {
            script = document.createElement('script');
            script.id = 'chartjs-cdn-script';
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js';
            script.onload = callback;
            document.head.appendChild(script);
        } else {
            var interval = setInterval(function() {
                if (typeof Chart !== 'undefined') {
                    clearInterval(interval);
                    callback();
                }
            }, 50);
        }
    }

    function cdPerfSyncRange(val) {
        if (!val) return;
        window.perfCurrentRange = val;
        window.agencyPerfCurrentRange = val;
        $('#perfDateRangeSelect, .perf-sync-select').val(val);
        cdPerfFetchData(val, window.perfCurrentCompare, false);
    }
    window.cdPerfSyncRange = cdPerfSyncRange;
    window.agencyPerfSyncRange = cdPerfSyncRange;

    function cdPerfSyncCompare(val) {
        if (!val) return;
        window.perfCurrentCompare = val;
        window.agencyPerfCurrentCompare = val;
        $('#perfCompareWithSelect').val(val);
        cdPerfFetchData(window.perfCurrentRange, val, false);
    }
    window.cdPerfSyncCompare = cdPerfSyncCompare;
    window.agencyPerfSyncCompare = cdPerfSyncCompare;

    function cdPerfRefresh() {
        var $btn = $('#btnPerfRefresh i, #btnAgencyPerfRefresh i');
        $btn.addClass('fa-spin');
        var cacheKey = perfClientId + '_' + window.perfCurrentRange + '_' + window.perfCurrentCompare;
        delete window.perfClientCache[cacheKey];
        if (window.agencyPerfClientCache) delete window.agencyPerfClientCache[cacheKey];
        cdPerfFetchData(window.perfCurrentRange, window.perfCurrentCompare, true, function() {
            $btn.removeClass('fa-spin');
        });
    }
    window.cdPerfRefresh = cdPerfRefresh;
    window.agencyPerfRefresh = cdPerfRefresh;

    function cdPerfFetchData(range, compare, force, onComplete) {
        range = range || window.perfCurrentRange || '12M';
        compare = compare || window.perfCurrentCompare || 'previous_period';
        var cacheKey = perfClientId + '_' + range + '_' + compare;

        // 1. Client-Side User RAM Cache Hit (0% Server / 0% DB reads)
        if (!force && window.perfClientCache[cacheKey]) {
            cdPerfRender(window.perfClientCache[cacheKey]);
            if (typeof onComplete === 'function') onComplete();
            return;
        }

        if (window.perfIsLoading) return;
        window.perfIsLoading = true;

        $.ajax({
            url: perfBaseApiUrl,
            type: 'GET',
            dataType: 'json',
            headers: {
                'X-Request-ID': 'req_' + Math.random().toString(36).substring(2, 12),
            },
            data: {
                range: range,
                compare: compare,
                client_type: 'agency',
                timezone: Intl.DateTimeFormat().resolvedOptions().timeZone || 'Asia/Kolkata',
            },
            success: function(res) {
                window.perfIsLoading = false;
                window.perfClientCache[cacheKey] = res;
                if (window.agencyPerfClientCache) window.agencyPerfClientCache[cacheKey] = res;
                cdPerfRender(res);
                if (typeof onComplete === 'function') onComplete();
            },
            error: function(err) {
                window.perfIsLoading = false;
                console.warn('Performance Analytics API error:', err);
                if (typeof onComplete === 'function') onComplete();
            }
        });
    }
    window.cdPerfFetchData = cdPerfFetchData;
    window.agencyPerfFetchData = cdPerfFetchData;

    function cdPerfRender(data) {
        if (!data || !data.summary) return;

        var s = data.summary;

        // 1. Update 5 Hero KPI Cards
        var candLabel = (s.candidates && s.candidates.display_label) ? s.candidates.display_label : (s.candidates ? s.candidates.value.toLocaleString() : '0');
        $('#perfKpiCandidates').text(candLabel);
        cdPerfUpdatePill($('#perfKpiCandPill'), s.candidates ? s.candidates.delta_label : null, s.candidates ? s.candidates.delta_value : null, false);
        $('#perfKpiCandComp').text('vs. ' + (data.meta && data.meta.compare === 'none' ? 'none' : 'prior period'));

        $('#perfKpiInterviews').text(s.interviews_scheduled ? s.interviews_scheduled.value.toLocaleString() : '0');
        cdPerfUpdatePill($('#perfKpiIntPill'), s.interviews_scheduled ? s.interviews_scheduled.delta_label : null, s.interviews_scheduled ? s.interviews_scheduled.delta_value : null, false);
        $('#perfKpiIntComp').text('vs. ' + (data.meta && data.meta.compare === 'none' ? 'none' : 'prior period'));

        $('#perfKpiOffers').text(s.offers ? s.offers.value.toLocaleString() : '0');
        cdPerfUpdatePill($('#perfKpiOfferPill'), s.offers ? s.offers.delta_label : null, s.offers ? s.offers.delta_value : null, false);
        $('#perfKpiOfferComp').text('vs. ' + (data.meta && data.meta.compare === 'none' ? 'none' : 'prior period'));

        $('#perfKpiJoins').text(s.joins ? s.joins.value.toLocaleString() : '0');
        cdPerfUpdatePill($('#perfKpiJoinPill'), s.joins ? s.joins.delta_label : null, s.joins ? s.joins.delta_value : null, false);
        $('#perfKpiJoinComp').text('vs. ' + (data.meta && data.meta.compare === 'none' ? 'none' : 'prior period'));

        var tthFormatted = (s.avg_time_to_hire_days && s.avg_time_to_hire_days.formatted) ? s.avg_time_to_hire_days.formatted : '—';
        $('#perfKpiTimeToHire').text(tthFormatted);
        cdPerfUpdatePill($('#perfKpiTimePill'), s.avg_time_to_hire_days ? s.avg_time_to_hire_days.delta_label : null, s.avg_time_to_hire_days ? s.avg_time_to_hire_days.delta_value : null, true);
        $('#perfKpiTimeComp').text((s.avg_time_to_hire_days && s.avg_time_to_hire_days.placed_count) ? (s.avg_time_to_hire_days.placed_count + ' placements') : 'vs. prior period');

        // 2. Empty State Banner
        var $banner = $('#perfEmptyStateBanner');
        if (data.empty_state) {
            $('#perfEmptyStateTitle').text(data.empty_state.title + ':');
            $('#perfEmptyStateMessage').text(data.empty_state.message);
            $banner.slideDown(150);
        } else {
            $banner.slideUp(150);
        }

        // 3. Funnel Stages (In Review strictly excluded from Performance funnel)
        if (data.funnel && data.funnel.stages) {
            var st = data.funnel.stages;
            if (st[0]) { $('#funnelCountCand').text(st[0].count); $('#funnelPctCand').text(st[0].conversion_pct); }
            if (st[1]) { $('#funnelCountScreened').text(st[1].count); $('#funnelPctScreened').text(st[1].conversion_pct); }
            if (st[2]) { $('#funnelCountInt').text(st[2].count); $('#funnelPctInt').text(st[2].conversion_pct); }
            if (st[3]) { $('#funnelCountOff').text(st[3].count); $('#funnelPctOff').text(st[3].conversion_pct); }
            if (st[4]) { $('#funnelCountJoin').text(st[4].count); $('#funnelPctJoin').text(st[4].conversion_pct); }
        }

        // 4. Intelligence Grid
        var vVal = (s.avg_time_to_hire_days && s.avg_time_to_hire_days.formatted !== '—') ? s.avg_time_to_hire_days.formatted : 'Active';
        $('#perfIntelVelocity').text(vVal);

        var candCount = s.candidates ? s.candidates.value : 0;
        var intCount = s.interviewed ? s.interviewed.value : 0;
        var offCount = s.offers ? s.offers.value : 0;
        var joinCount = s.joins ? s.joins.value : 0;

        var intConv = candCount > 0 ? ((intCount / candCount) * 100).toFixed(1) + '%' : '0.0%';
        $('#perfIntelIntConv').text(intConv);
        $('#perfIntelIntConvSub').text(intCount + ' of ' + candCount + ' candidates');

        var offConv = intCount > 0 ? ((offCount / intCount) * 100).toFixed(1) + '%' : '0.0%';
        $('#perfIntelOfferConv').text(offConv);
        $('#perfIntelOfferConvSub').text(offCount + ' of ' + intCount + ' interviews');

        var yieldVal = data.funnel && data.funnel.overall_yield_pct ? data.funnel.overall_yield_pct : '0.0%';
        $('#perfIntelJoinRatio').text(yieldVal);
        $('#perfIntelJoinRatioSub').text(joinCount + ' placed from ' + candCount + ' submitted');

        // 5. Intelligence Actionable Insights
        if (data.insights && data.insights.length > 0) {
            var insHtml = '';
            data.insights.forEach(function(item) {
                var iconColor = item.type === 'positive' ? '#10b981' : (item.type === 'warning' ? '#d97706' : '#10b981');
                insHtml += '<div style="margin-bottom:4px;"><i class="fas ' + item.icon + ' fa-solid me-1" style="color:' + iconColor + ';"></i> <strong>' + item.title + ':</strong> ' + item.text + '</div>';
            });
            $('#perfInsightsGrid').html(insHtml);
        }

        // 6. Top Hiring Roles Table
        var rolesHtml = '';
        if (data.roles && data.roles.items && data.roles.items.length > 0) {
            data.roles.items.forEach(function(r, idx) {
                var pctVal = parseFloat(r.conversion_pct) || 0;
                rolesHtml += '<tr>' +
                    '<td>' + (idx + 1) + '</td>' +
                    '<td>' + r.title + '</td>' +
                    '<td>' + r.submissions + '</td>' +
                    '<td>' + r.interviews + '</td>' +
                    '<td>' + r.offers + '</td>' +
                    '<td>' + r.joined + '</td>' +
                    '<td><div class="cd-perf-progress-wrap"><span>' + r.conversion_pct + '</span><div class="cd-perf-progress-bar"><div class="cd-perf-progress-fill" style="width:' + Math.min(100, pctVal) + '%;"></div></div></div></td>' +
                    '</tr>';
            });
        } else {
            rolesHtml = '<tr><td colspan="7" class="text-center py-3 text-muted">No active requisitions recorded for this period.</td></tr>';
        }
        $('#perfTopRolesTbody').html(rolesHtml);

        // 7. Recruiter Performance Table
        var recHtml = '';
        if (data.recruiters && data.recruiters.items && data.recruiters.items.length > 0) {
            data.recruiters.items.forEach(function(rec, idx) {
                var initials = rec.name.split(' ').map(function(n){ return n[0]; }).join('').substring(0, 2).toUpperCase();
                var pctVal = parseFloat(rec.join_rate_pct) || 0;
                var candLabel = rec.submissions + (rec.submissions === 1 ? ' Candidate' : ' Candidates');
                var recruiterDisplay = '<strong>' + rec.name + '</strong>' +
                    ' <span class="text-muted fs-8 ms-1 fw-normal">— ' + candLabel + '</span>';
                recHtml += '<tr>' +
                    '<td>' + (idx + 1) + '</td>' +
                    '<td><div class="d-flex align-items-center gap-2"><span class="cd-recruiter-avatar" style="background:#ecfdf5; color:#059669;">' + initials + '</span><div>' + recruiterDisplay + '</div></div></td>' +
                    '<td>' + rec.submissions + '</td>' +
                    '<td>' + rec.interviews + '</td>' +
                    '<td>' + rec.offers + '</td>' +
                    '<td>' + rec.joins + '</td>' +
                    '<td><div class="cd-perf-progress-wrap"><span>' + rec.join_rate_pct + '</span><div class="cd-perf-progress-bar"><div class="cd-perf-progress-fill" style="width:' + Math.min(100, pctVal) + '%;"></div></div></div></td>' +
                    '</tr>';
            });
        } else {
            recHtml = '<tr><td colspan="7" class="text-center py-3 text-muted">No assigned recruiters recorded for this agency.</td></tr>';
        }
        $('#perfRecruitersTbody').html(recHtml);

        // 8. Render Reactive Chart.js Visualizations
        ensureChartJsLoaded(function() {
            cdPerfRenderCharts(data);
        });
    }
    window.cdPerfRender = cdPerfRender;
    window.agencyPerfRender = cdPerfRender;

    function cdPerfUpdatePill($pill, deltaLabel, deltaValue, isLowerBetter) {
        if (!$pill.length) return;

        if (deltaLabel === 'New') {
            $pill.removeClass('cd-perf-pill-up cd-perf-pill-down cd-perf-pill-good')
                 .addClass('cd-perf-pill-up')
                 .css({ 'background': '#f1f5f9', 'color': '#475569' })
                 .html('<i class="fas fa-sparkles fa-solid" style="font-size:8px;"></i> New');
            return;
        }

        var isPositive = deltaValue !== null && deltaValue > 0;
        var isNegative = deltaValue !== null && deltaValue < 0;

        var isGood = isLowerBetter ? isNegative : isPositive;

        $pill.removeClass('cd-perf-pill-up cd-perf-pill-down cd-perf-pill-good');
        if (isGood) {
            $pill.addClass('cd-perf-pill-up').css({ 'background': '#ecfdf5', 'color': '#10b981' });
        } else if (isNegative || isPositive) {
            $pill.addClass('cd-perf-pill-down').css({ 'background': '#fef2f2', 'color': '#ef4444' });
        } else {
            $pill.addClass('cd-perf-pill-up').css({ 'background': '#f8fafc', 'color': '#64748b' });
        }

        var icon = isPositive ? 'fa-arrow-up' : (isNegative ? 'fa-arrow-down' : 'fa-minus');
        $pill.html('<i class="fas ' + icon + ' fa-solid" style="font-size:8px;"></i> ' + (deltaLabel || '0.0%'));
    }
    window.cdPerfUpdatePill = cdPerfUpdatePill;

    function cdPerfRenderCharts(data) {
        function getOrCreateChart(canvasId, config) {
            var canvas = document.getElementById(canvasId);
            if (!canvas) return null;
            try {
                if (typeof Chart.getChart === 'function') {
                    var existing = Chart.getChart(canvas);
                    if (existing) existing.destroy();
                }
                if (window.perfChartInstances[canvasId]) {
                    window.perfChartInstances[canvasId].destroy();
                }
            } catch(e) {}
            var ctx = canvas.getContext('2d');
            var inst = new Chart(ctx, config);
            window.perfChartInstances[canvasId] = inst;
            return inst;
        }

        var v = data.velocity || { labels: [], candidates_submitted: [], interviewed_candidates: [] };
        var j = data.jobs || { active: 0, hold: 0, closed: 0, total_count: 0 };
        var o = data.offers || { labels: [], offers_generated: [], successful_joins: [] };
        var jt = data.job_types || { labels: [], counts: [], percentages: [] };

        // 1. Combo: Candidates & Interviews
        getOrCreateChart('chartCandidatesInterviews', {
            type: 'bar',
            data: {
                labels: v.labels,
                datasets: [
                    {
                        type: 'bar',
                        label: 'Candidates Submitted',
                        data: v.candidates_submitted,
                        backgroundColor: '#10b981',
                        borderRadius: 4,
                        barPercentage: 0.5,
                        order: 2
                    },
                    {
                        type: 'line',
                        label: 'Interviewed Candidates',
                        data: v.interviewed_candidates,
                        borderColor: '#8b5cf6',
                        backgroundColor: '#8b5cf6',
                        borderWidth: 2,
                        pointRadius: 3,
                        pointBackgroundColor: '#8b5cf6',
                        tension: 0.35,
                        order: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 9.5 } } },
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 9.5 }, precision: 0 } }
                }
            }
        });

        // 2. Bar: All Jobs Status
        getOrCreateChart('chartAllJobsStatus', {
            type: 'bar',
            data: {
                labels: ['Active', 'On Hold', 'Closed'],
                datasets: [{
                    label: 'Jobs Count',
                    data: [j.active, j.hold, j.closed],
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                    borderRadius: 4,
                    barPercentage: 0.45
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 9.5 } } },
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 9.5 }, precision: 0 } }
                }
            }
        });

        // 3. Line/Bar: Offers & Joins
        getOrCreateChart('chartOffersJoinings', {
            type: 'bar',
            data: {
                labels: o.labels,
                datasets: [
                    {
                        type: 'bar',
                        label: 'Offers',
                        data: o.offers_generated,
                        backgroundColor: '#f59e0b',
                        borderRadius: 4,
                        barPercentage: 0.45
                    },
                    {
                        type: 'line',
                        label: 'Joins',
                        data: o.successful_joins,
                        borderColor: '#10b981',
                        backgroundColor: '#10b981',
                        borderWidth: 2,
                        pointRadius: 3,
                        tension: 0.35
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 9.5 } } },
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 9.5 }, precision: 0 } }
                }
            }
        });

        // 4. Doughnut: Job Type Performance
        var donutData = jt.counts && jt.counts.length > 0 ? jt.counts : [j.active, j.hold, 0, 0];
        getOrCreateChart('chartJobType', {
            type: 'doughnut',
            data: {
                labels: jt.labels || ['Full-Time', 'Contract', 'Freelance', 'Part-Time'],
                datasets: [{
                    data: donutData,
                    backgroundColor: ['#10b981', '#8b5cf6', '#f59e0b', '#06b6d4', '#94a3b8'],
                    borderWidth: 0,
                    hoverOffset: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: { legend: { display: false } }
            }
        });

        $('#donutTotalJobs').text(j.active || 0);
        if (jt.counts && jt.counts[0] !== undefined) $('#donutCountFulltime').text(jt.counts[0]);
        if (jt.percentages && jt.percentages[0]) $('#donutPctFulltime').text(jt.percentages[0]);
        if (jt.counts && jt.counts[1] !== undefined) $('#donutCountContract').text(jt.counts[1]);
        if (jt.percentages && jt.percentages[1]) $('#donutPctContract').text(jt.percentages[1]);
        if (jt.counts && jt.counts[2] !== undefined) $('#donutCountFreelance').text(jt.counts[2]);
        if (jt.percentages && jt.percentages[2]) $('#donutPctFreelance').text(jt.percentages[2]);
        if (jt.counts && jt.counts[3] !== undefined) $('#donutCountPartTime').text(jt.counts[3]);
        if (jt.percentages && jt.percentages[3]) $('#donutPctPartTime').text(jt.percentages[3]);

        // 5. Line: Time to Hire Trend (Zero-Data Rule: null for months with 0 joins, spanGaps: true)
        var tthData = (data.tth_trend && data.tth_trend.length)
            ? data.tth_trend
            : (v.time_to_hire && v.time_to_hire.length)
                ? v.time_to_hire
                : v.labels.map(function() { return null; });

        getOrCreateChart('chartTimeToHire', {
            type: 'line',
            data: {
                labels: v.labels,
                datasets: [{
                    label: 'Days to Hire',
                    data: tthData,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.08)',
                    borderWidth: 2,
                    fill: true,
                    pointRadius: 2.5,
                    tension: 0.35,
                    spanGaps: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw !== null ? context.raw + ' days' : 'No joins';
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 9.5 } } },
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 9.5 } } }
                }
            }
        });
    }
    window.cdPerfRenderCharts = cdPerfRenderCharts;
    window.initPerformanceDashboard = function() {
        cdPerfFetchData(window.perfCurrentRange, window.perfCurrentCompare, false);
    };
    window.fetchDirectClientDashboardAnalytics = window.initPerformanceDashboard;

    // ── 7. Reverb WebSocket Monotonic State Updates ──
    if (window.Echo && agencyCompanyId) {
        var agencyChannel = 'private-company.' + currentAuthCompanyId + '.agency.' + agencyCompanyId;
        try {
            window.Echo.private(agencyChannel).listen('.AgencyDataUpdated', function(payload) {
                if (!payload) return;
                // Bust cache and revalidate active tab
                var activeTab = $('.cd-drawer-tabs-list .cd-tab-pill-btn.active').attr('href');
                if (activeTab) {
                    window.loadAgencyTabContent(activeTab, true);
                }
            });
        } catch(e) {}
    }

    // ── 8. Mount / Initialization Hook ──
    $(document).ready(function() {
        // Pre-warm candidates store in browser RAM
        if (window.ClientCandidateStoreAgency && typeof window.ClientCandidateStoreAgency.init === 'function') {
            try {
                window.ClientCandidateStoreAgency.init(currentAuthCompanyId, agencyCompanyId, 'agency');
            } catch(e) {}
        }

        // Pre-warm activity manager
        if (window.ClientActivityManager && typeof window.ClientActivityManager.initClient === 'function') {
            try {
                window.ClientActivityManager.initClient(agencyCompanyId, false, agencyCompanyId, false, 'agency', agencyCompanyId);
            } catch(e) {}
        }
    });

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
