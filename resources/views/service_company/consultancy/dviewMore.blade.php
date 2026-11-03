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

    // Helper: Country ISO / Standard Short Form Resolver (e.g. India -> IN, United States -> US, Japan -> JPN)
    $getCountryShort = function($cName) {
        if (empty($cName)) return 'GLB';
        $c = strtolower(trim((string)$cName));
        $map = [
            'india' => 'IN',
            'united states' => 'US',
            'united states of america' => 'USA',
            'usa' => 'US',
            'japan' => 'JPN',
            'united kingdom' => 'UK',
            'uk' => 'UK',
            'germany' => 'DE',
            'france' => 'FR',
            'canada' => 'CAN',
            'australia' => 'AUS',
            'singapore' => 'SGP',
            'united arab emirates' => 'UAE',
            'uae' => 'UAE',
            'dubai' => 'DXB',
            'saudi arabia' => 'KSA',
            'netherlands' => 'NL',
            'switzerland' => 'CH',
            'ireland' => 'IRL',
            'new zealand' => 'NZ',
            'south africa' => 'ZA',
            'brazil' => 'BR',
            'mexico' => 'MEX',
            'spain' => 'ES',
            'italy' => 'IT',
            'poland' => 'PL',
            'sweden' => 'SE',
            'norway' => 'NO',
            'denmark' => 'DK',
            'finland' => 'FI',
            'belgium' => 'BE',
            'austria' => 'AT',
            'philippines' => 'PH',
            'malaysia' => 'MY',
            'indonesia' => 'ID',
            'vietnam' => 'VN',
            'thailand' => 'TH',
            'china' => 'CN',
            'hong kong' => 'HK',
            'taiwan' => 'TW',
            'south korea' => 'KR',
            'korea' => 'KR',
        ];
        if (isset($map[$c])) {
            return $map[$c];
        }
        $words = preg_split('/\s+/', $c);
        if (count($words) > 1) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($c, 0, 3));
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

/* ── 7c. Modern Commercial Cards for Agency Drawer (Rule 20 / Synced with Hiring Services & Commercials Tab) ── */
.cd-bullet-dot {
    width: 4px !important;
    height: 4px !important;
    border-radius: 50% !important;
    background: #009ef7 !important;
    display: inline-block !important;
    flex-shrink: 0 !important;
}
.cd-bullet-dot.purple { background: #8b5cf6 !important; }
.cd-bullet-dot.emerald { background: #10b981 !important; }
.cd-bullet-dot.sky { background: #0284c7 !important; }
.cd-bullet-dot.amber { background: #f59e0b !important; }

/* ── Modern Compact Commercial Services Card System (Rule 20 Modern Short-Form Standard) ── */
.cd-srv-master-box {
    margin-top: 12px !important;
}
.cd-srv-filter-pills {
    display: inline-flex !important;
    align-items: center !important;
    background: #f1f5f9 !important;
    padding: 2px !important;
    border-radius: 6px !important;
    border: 1px solid #e2e8f0 !important;
}
.cd-srv-filter-btn {
    border: none !important;
    background: transparent !important;
    font-size: 10px !important;
    font-weight: 600 !important;
    color: #64748b !important;
    padding: 2px 8px !important;
    border-radius: 4px !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
    line-height: 1.4 !important;
}
.cd-srv-filter-btn:hover {
    color: #0f172a !important;
}
.cd-srv-filter-btn.active {
    background: #ffffff !important;
    color: #009ef7 !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08) !important;
    font-weight: 700 !important;
}
.cd-srv-compact-stack {
    display: flex !important;
    flex-direction: column !important;
    gap: 10px !important;
}
.cd-srv-card-compact {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    overflow: hidden !important;
    transition: border-color 0.18s ease, box-shadow 0.18s ease !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    width: 100% !important;
    box-sizing: border-box !important;
}
.cd-srv-card-compact:hover {
    box-shadow: 0 3px 8px rgba(15, 23, 42, 0.06) !important;
}
.cd-srv-card-compact.theme-fulltime {
    border-left: 3.5px solid #009ef7 !important;
    border-color: #dbeafe !important;
}
.cd-srv-card-compact.theme-contract {
    border-left: 3.5px solid #7c3aed !important;
    border-color: #ede9fe !important;
}

/* Slim Card Header */
.cd-srv-c-head {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding: 7px 12px !important;
    gap: 8px !important;
    flex-wrap: nowrap !important;
}
.cd-srv-card-compact.theme-fulltime .cd-srv-c-head {
    background: #f0f7ff !important;
    border-bottom: 1px solid #dbeafe !important;
}
.cd-srv-card-compact.theme-contract .cd-srv-c-head {
    background: #faf7ff !important;
    border-bottom: 1px solid #ede9fe !important;
}
.cd-srv-c-head-left {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    min-width: 0 !important;
    flex: 1 1 auto !important;
}
.cd-srv-c-symbol {
    width: 26px !important;
    height: 26px !important;
    border-radius: 6px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 11px !important;
    font-weight: 800 !important;
    flex-shrink: 0 !important;
    letter-spacing: 0.02em !important;
}
.cd-srv-c-symbol.ft {
    background: #ffffff !important;
    color: #009ef7 !important;
    border: 1px solid #bfdbfe !important;
}
.cd-srv-c-symbol.ct {
    background: #ffffff !important;
    color: #7c3aed !important;
    border: 1px solid #ddd6fe !important;
}
.cd-srv-c-title-box {
    display: flex !important;
    flex-direction: column !important;
    min-width: 0 !important;
    gap: 1px !important;
}
.cd-srv-c-title-row {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
}
.cd-srv-c-title {
    font-size: 12.5px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    line-height: 1.25 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}
.cd-srv-c-subtitle {
    font-size: 10px !important;
    color: #64748b !important;
    line-height: 1.25 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}
.cd-srv-c-status-dot {
    width: 6.5px !important;
    height: 6.5px !important;
    border-radius: 50% !important;
    background: #10b981 !important;
    box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.25) !important;
    flex-shrink: 0 !important;
}
.cd-srv-c-status-dot.purple {
    background: #7c3aed !important;
    box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.25) !important;
}
.cd-srv-c-rate-pill {
    display: inline-flex !important;
    align-items: baseline !important;
    gap: 4px !important;
    padding: 2.5px 8px !important;
    border-radius: 5px !important;
    flex-shrink: 0 !important;
}
.cd-srv-c-rate-pill.ft {
    background: #eff6ff !important;
    border: 1px solid #bfdbfe !important;
    color: #1e40af !important;
}
.cd-srv-c-rate-pill.ct {
    background: #f5f3ff !important;
    border: 1px solid #ddd6fe !important;
    color: #6d28d9 !important;
}
.cd-srv-c-rate-pill .rate-val {
    font-size: 12.5px !important;
    font-weight: 800 !important;
    font-variant-numeric: tabular-nums !important;
}
.cd-srv-c-rate-pill .rate-lbl {
    font-size: 9px !important;
    font-weight: 600 !important;
    opacity: 0.85 !important;
}

/* 5-Line Compact High-Density Body (Rule 20 Modern Standard) */
.cd-srv-c-body {
    padding: 7px 12px 8px 12px !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 4px !important;
    background: #ffffff !important;
}
.cd-srv-c-row {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    font-size: 11px !important;
    line-height: 1.35 !important;
    min-width: 0 !important;
    white-space: nowrap !important;
}
.cd-srv-c-key {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4.5px !important;
    width: 98px !important;
    min-width: 98px !important;
    max-width: 98px !important;
    flex-shrink: 0 !important;
    font-weight: 600 !important;
    color: #64748b !important;
    letter-spacing: 0.01em !important;
    font-size: 10px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}
.cd-srv-c-icon {
    font-size: 9.5px !important;
    width: 12px !important;
    text-align: center !important;
    flex-shrink: 0 !important;
}
.cd-srv-c-icon.ft { color: #009ef7 !important; }
.cd-srv-c-icon.ct { color: #7c3aed !important; }
.cd-srv-c-val {
    display: flex !important;
    align-items: center !important;
    flex-wrap: nowrap !important;
    gap: 4px !important;
    color: #334155 !important;
    font-weight: 500 !important;
    min-width: 0 !important;
    flex: 1 1 auto !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}
.cd-srv-c-val strong {
    font-weight: 700 !important;
    color: #0f172a !important;
}
.cd-srv-c-val .text-purple {
    color: #7c3aed !important;
}
.cd-srv-c-val .text-success {
    color: #10b981 !important;
}
.cd-srv-c-sep {
    color: #cbd5e1 !important;
    font-weight: 400 !important;
    user-select: none !important;
    font-size: 9.5px !important;
    flex-shrink: 0 !important;
}
.cd-srv-c-badge {
    font-size: 9px !important;
    font-weight: 700 !important;
    padding: 1px 5px !important;
    border-radius: 3.5px !important;
    line-height: 1.25 !important;
    flex-shrink: 0 !important;
}
.cd-srv-c-badge.ft {
    background: #eff6ff !important;
    color: #0085ff !important;
    border: 1px solid #bfdbfe !important;
}
.cd-srv-c-badge.ct {
    background: #f5f3ff !important;
    color: #7c3aed !important;
    border: 1px solid #ddd6fe !important;
}

/* Country Micro-Chips in Short Form */
.cd-srv-c-country-list {
    display: inline-flex !important;
    align-items: center !important;
    flex-wrap: nowrap !important;
    gap: 3px !important;
    flex-shrink: 0 !important;
}
.cd-srv-c-country-chip {
    display: inline-flex !important;
    align-items: center !important;
    height: 17px !important;
    padding: 0 4.5px !important;
    border-radius: 3px !important;
    background: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    color: #1e293b !important;
    font-size: 9px !important;
    font-weight: 700 !important;
    letter-spacing: 0.03em !important;
    cursor: default !important;
    transition: all 0.15s ease !important;
    flex-shrink: 0 !important;
}
.cd-srv-c-country-chip:hover {
    border-color: #94a3b8 !important;
    background: #ffffff !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06) !important;
}
.cd-srv-c-country-chip.more {
    background: #eff6ff !important;
    border-color: #bfdbfe !important;
    color: #009ef7 !important;
    font-weight: 700 !important;
}

/* Micro Activity Strip */
.cd-srv-c-strip {
    padding: 4px 12px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 8px !important;
    font-size: 10px !important;
    color: #64748b !important;
    border-top: 1px solid #f1f5f9 !important;
}
.cd-srv-card-compact.theme-fulltime .cd-srv-c-strip {
    background: #f8fbff !important;
}
.cd-srv-card-compact.theme-contract .cd-srv-c-strip {
    background: #faf7ff !important;
}
.cd-srv-c-strip-text {
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    flex: 1 1 auto !important;
    min-width: 0 !important;
}
.cd-srv-c-strip-tag {
    font-size: 9px !important;
    font-weight: 600 !important;
    padding: 1px 6px !important;
    border-radius: 4px !important;
    background: #ffffff !important;
    border: 1px solid #dbeafe !important;
    color: #1e40af !important;
    flex-shrink: 0 !important;
}
.cd-srv-c-strip-tag.purple {
    border-color: #ede9fe !important;
    color: #6d28d9 !important;
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
.cd-cand-card {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 10px !important;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
  transition: all 0.15s ease-in-out !important;
  overflow: hidden !important;
  width: 100% !important;
}
.cd-cand-card:hover {
  border-color: #cbd5e1 !important;
  box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06) !important;
  transform: translateY(-1px) !important;
}
.cd-cand-card-grid {
  display: grid !important;
  grid-template-columns: minmax(0, 1.85fr) minmax(0, 1.35fr) minmax(0, 1.25fr) minmax(0, 2.35fr) minmax(0, 1.55fr) minmax(0, 1.25fr) !important;
  gap: 8px !important;
  padding: 10px 12px !important;
  width: 100% !important;
  align-items: stretch !important;
}
.cd-cand-subcard {
  background: #f8fafc !important;
  border: 1px solid #edf2f7 !important;
  border-radius: 8px !important;
  padding: 7px 9px !important;
  display: flex !important;
  flex-direction: column !important;
  justify-content: space-between !important;
  min-width: 0 !important;
}
.cd-cand-subcard-hdr {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  margin-bottom: 4px !important;
}
.cd-cand-subcard-label {
  font-size: 9.5px !important;
  font-weight: 700 !important;
  text-transform: uppercase !important;
  letter-spacing: 0.3px !important;
  color: #64748b !important;
}
.cd-cand-card-bottom {
  border-top: 1px solid #f1f5f9;
  padding: 6px 12px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #fafafa;
  font-size: 11px;
  color: #64748b;
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
}
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
.cd-perf-pill-up { background: #ecfdf5; color: #10b981; }
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
        <!-- KPI 1: All Shared Jobs -->
        <a href="#requisitions" class="cd-kpi-card cd-kpi-nav-trigger" onclick="triggerDrawerTab('requisitions', event)" title="Total jobs shared with this agency">
            <div class="cd-kpi-icon cd-kpi-icon-blue"><i class="fas fa-layer-group"></i></div>
            <div class="cd-kpi-body">
                <div class="cd-kpi-num">{{ number_format($kpiTotalJobs) }}</div>
                <div class="cd-kpi-lbl">All Shared Jobs</div>
            </div>
        </a>

        <!-- KPI 2: All Active Jobs -->
        <a href="#requisitions" class="cd-kpi-card cd-kpi-nav-trigger" onclick="triggerDrawerTab('requisitions', event)" title="Active jobs currently being sourced">
            <div class="cd-kpi-icon cd-kpi-icon-emerald"><i class="fas fa-bolt"></i></div>
            <div class="cd-kpi-body">
                <div class="cd-kpi-num">{{ number_format($kpiActiveJobs) }}</div>
                <div class="cd-kpi-lbl">All Active Jobs</div>
            </div>
        </a>

        <!-- KPI 3: All Candidates -->
        <a href="#candidate_list" class="cd-kpi-card cd-kpi-nav-trigger" onclick="triggerDrawerTab('candidate_list', event)" title="Total candidate profiles submitted">
            <div class="cd-kpi-icon cd-kpi-icon-purple"><i class="fas fa-users"></i></div>
            <div class="cd-kpi-body">
                <div class="cd-kpi-num">{{ number_format($kpiSubmitted) }}</div>
                <div class="cd-kpi-lbl">All Candidates</div>
            </div>
        </a>

        <!-- KPI 4: All Interviews -->
        <a href="#interview_tracker" class="cd-kpi-card cd-kpi-nav-trigger" onclick="triggerDrawerTab('interview_tracker', event)" title="Interviews conducted for agency candidates">
            <div class="cd-kpi-icon cd-kpi-icon-amber"><i class="fas fa-calendar-check"></i></div>
            <div class="cd-kpi-body">
                <div class="cd-kpi-num">{{ number_format($kpiInterviews) }}</div>
                <div class="cd-kpi-lbl">All Interviews</div>
            </div>
        </a>

        <!-- KPI 5: All Offers -->
        <a href="#candidate_joining" class="cd-kpi-card cd-kpi-nav-trigger" onclick="triggerDrawerTab('candidate_joining', event)" title="Offers extended to agency candidates">
            <div class="cd-kpi-icon cd-kpi-icon-rose"><i class="fas fa-file-signature"></i></div>
            <div class="cd-kpi-body">
                <div class="cd-kpi-num">{{ number_format($kpiOffers) }}</div>
                <div class="cd-kpi-lbl">All Offers</div>
            </div>
        </a>

        <!-- KPI 6: All Joined -->
        <a href="#candidate_joining" class="cd-kpi-card cd-kpi-nav-trigger" onclick="triggerDrawerTab('candidate_joining', event)" title="Candidates successfully joined">
            <div class="cd-kpi-icon cd-kpi-icon-teal"><i class="fas fa-handshake"></i></div>
            <div class="cd-kpi-body">
                <div class="cd-kpi-num">{{ number_format($kpiJoins) }}</div>
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
                    <span class="cd-tab-badge-pill">{{ $kpiInterviews }}</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="cd-tab-pill-btn nav-link" data-bs-toggle="tab" data-toggle="tab" href="#candidate_joining" role="tab" aria-selected="false">
                    <i class="fas fa-file-signature"></i>
                    <span>Offers &amp; Joins</span>
                    <span class="cd-tab-badge-pill">{{ $kpiOffersTotal }}</span>
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

                    <!-- Commercial Terms Cards (Rule 20 Modern Card + Sub-Card System / Modern Compact 4-Line Standard) -->
                    @if(!empty($hasActiveCommercials))
                        <div class="cd-master-subcard cd-srv-master-box">
                            <div class="cd-subcard-header">
                                <div class="cd-subcard-header-left">
                                    <div class="cd-subcard-header-icon blue"><i class="fas fa-handshake-angle"></i></div>
                                    <h4 class="cd-subcard-heading">Hiring Services &amp; Commercials</h4>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    @if(!empty($hasPermanentStaffing) && !empty($hasContractStaffing))
                                        <div class="cd-srv-filter-pills" role="tablist">
                                            <button type="button" class="cd-srv-filter-btn active" data-srv-filter="all" onclick="filterAgencyServices('all', this)">All (2)</button>
                                            <button type="button" class="cd-srv-filter-btn" data-srv-filter="fulltime" onclick="filterAgencyServices('fulltime', this)">Fulltime</button>
                                            <button type="button" class="cd-srv-filter-btn" data-srv-filter="contract" onclick="filterAgencyServices('contract', this)">Contract</button>
                                        </div>
                                    @else
                                        <span class="badge bg-light-success text-success fw-bold" style="font-size:10px; padding:3px 8px; border-radius:12px;">
                                            <i class="fas fa-check-circle me-1"></i>Active Terms
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="cd-srv-compact-stack">
                                @if(!empty($hasPermanentStaffing) && !empty($permanentCommercial))
                                    <!-- ──────────────────────────────────────────────────────────────────
                                         COMMERCIAL CARD 1: FULLTIME HIRING SERVICES (Short Form Standard)
                                         Sourced strictly in sync with Hiring Services & Commercials settings
                                         ────────────────────────────────────────────────────────────────── -->
                                    <article class="cd-srv-card-compact theme-fulltime is-active cd-srv-item" data-srv="fulltime">
                                        <!-- Slim Header -->
                                        <div class="cd-srv-c-head">
                                            <div class="cd-srv-c-head-left">
                                                <div class="cd-srv-c-symbol ft" title="Fulltime Hiring">FT</div>
                                                <div class="cd-srv-c-title-box">
                                                    <div class="cd-srv-c-title-row">
                                                        <span class="cd-srv-c-title">Fulltime Hiring Services</span>
                                                        <span class="cd-srv-c-status-dot" title="Active Staffing Agreement"></span>
                                                    </div>
                                                    <span class="cd-srv-c-subtitle">Permanent direct-hire recruitment &amp; contingency search</span>
                                                </div>
                                            </div>
                                            <div class="cd-srv-c-head-right">
                                                <span class="cd-srv-c-rate-pill ft" title="Default Fulltime Fee Rate">
                                                    <span class="rate-val">{{ $permanentCommercial->default_rate ?? ($placement_fee ?? '8.33%') }}</span>
                                                    <span class="rate-lbl">{{ $permanentCommercial->default_rate_sub ?? 'Default Rate' }}</span>
                                                </span>
                                            </div>
                                        </div>

                                        <!-- 5 Lines of High-Density Commercial Intelligence (Rule 20 Modern Standard) -->
                                        <div class="cd-srv-c-body">
                                            <!-- Line 1: Default Rate & Model -->
                                            <div class="cd-srv-c-row">
                                                <div class="cd-srv-c-key">
                                                    <i class="fas fa-percent cd-srv-c-icon ft"></i>
                                                    <span>Default Rate:</span>
                                                </div>
                                                <div class="cd-srv-c-val">
                                                    <strong class="text-dark">{{ $permanentCommercial->default_rate ?? ($placement_fee ?? '8.33%') }}</strong>
                                                    <span class="cd-srv-c-sep">·</span>
                                                    <span class="text-gray-800">{{ $permanentCommercial->model_label ?? 'Percentage of CTC' }}</span>
                                                    @if(!empty($permanentCommercial->mode_badge))
                                                        <span class="cd-srv-c-badge ft">{{ $permanentCommercial->mode_badge }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Line 2: Invoicing Structure & Trigger -->
                                            <div class="cd-srv-c-row">
                                                <div class="cd-srv-c-key">
                                                    <i class="fas fa-file-invoice-dollar cd-srv-c-icon ft"></i>
                                                    <span>Invoicing:</span>
                                                </div>
                                                <div class="cd-srv-c-val">
                                                    <span class="text-gray-800">{{ $permanentCommercial->billing_frequency ?? 'One Time' }} Fee</span>
                                                    <span class="cd-srv-c-sep">·</span>
                                                    <span>Trigger: <strong>{{ $permanentCommercial->billing_trigger ?? 'Candidate Joined' }}</strong></span>
                                                </div>
                                            </div>

                                            <!-- Line 3: Payment Terms -->
                                            <div class="cd-srv-c-row">
                                                <div class="cd-srv-c-key">
                                                    <i class="fas fa-clock cd-srv-c-icon ft"></i>
                                                    <span>Payment Terms:</span>
                                                </div>
                                                <div class="cd-srv-c-val">
                                                    <strong>{{ $permanentCommercial->payment_terms ?? 'Net 30 Days' }}</strong>
                                                </div>
                                            </div>

                                            <!-- Line 4: Replacement Guarantee (Dedicated Separate Row) -->
                                            <div class="cd-srv-c-row">
                                                <div class="cd-srv-c-key">
                                                    <i class="fas fa-shield-halved cd-srv-c-icon ft"></i>
                                                    <span>Replacement:</span>
                                                </div>
                                                <div class="cd-srv-c-val" title="{{ !empty($permanentCommercial->replacement_clause) ? 'Clause: ' . $permanentCommercial->replacement_clause : 'Replacement Guarantee' }}">
                                                    <strong class="text-success">{{ $permanentCommercial->replacement_days ?? 90 }} Days</strong>
                                                    <span class="text-gray-800">{{ $permanentCommercial->replacement_type ?? 'Free Replacement' }}</span>
                                                </div>
                                            </div>

                                            <!-- Line 5: Country Operations in Short Form -->
                                            <div class="cd-srv-c-row">
                                                <div class="cd-srv-c-key">
                                                    <i class="fas fa-earth-americas cd-srv-c-icon ft"></i>
                                                    <span>Operations:</span>
                                                </div>
                                                <div class="cd-srv-c-val">
                                                    @php
                                                        $rawCountries = $permanentCommercial->countries ?? ['India'];
                                                        $cCount = count($rawCountries);
                                                    @endphp
                                                    <span>Operate in <strong>{{ $cCount }} {{ $cCount === 1 ? 'Country' : 'Countries' }}:</strong></span>
                                                    <div class="cd-srv-c-country-list">
                                                        @foreach(array_slice($rawCountries, 0, 5) as $cName)
                                                            @php
                                                                $sCode = $getCountryShort($cName);
                                                            @endphp
                                                            <span class="cd-srv-c-country-chip" title="{{ $cName }}">{{ $sCode }}</span>
                                                        @endforeach
                                                        @if($cCount > 5)
                                                            <span class="cd-srv-c-country-chip more" title="{{ implode(', ', array_slice($rawCountries, 5)) }}">+{{ $cCount - 5 }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                                @endif

                                @if(!empty($hasContractStaffing) && !empty($contractCommercial))
                                    <!-- ──────────────────────────────────────────────────────────────────
                                         COMMERCIAL CARD 2: CONTRACT HIRING SERVICES (Short Form Standard)
                                         Sourced strictly in sync with Hiring Services & Commercials settings
                                         ────────────────────────────────────────────────────────────────── -->
                                    <article class="cd-srv-card-compact theme-contract is-active cd-srv-item" data-srv="contract">
                                        <!-- Slim Header -->
                                        <div class="cd-srv-c-head">
                                            <div class="cd-srv-c-head-left">
                                                <div class="cd-srv-c-symbol ct" title="Contract Hiring">CT</div>
                                                <div class="cd-srv-c-title-box">
                                                    <div class="cd-srv-c-title-row">
                                                        <span class="cd-srv-c-title">Contract Hiring Services</span>
                                                        <span class="cd-srv-c-status-dot purple" title="Active Contract Staffing Agreement"></span>
                                                    </div>
                                                    <span class="cd-srv-c-subtitle">Contract staffing, hourly consultants &amp; talent augmentation</span>
                                                </div>
                                            </div>
                                            <div class="cd-srv-c-head-right">
                                                <span class="cd-srv-c-rate-pill ct" title="Default Contract Bill Rate">
                                                    <span class="rate-val">{{ $contractCommercial->default_rate ?? ($contractCommercial->commercial_rate ?? '₹ 80,000') }}</span>
                                                    <span class="rate-lbl">{{ $contractCommercial->default_rate_sub ?? '/ Hour' }}</span>
                                                </span>
                                            </div>
                                        </div>

                                        <!-- 5 Lines of High-Density Commercial Intelligence (Rule 20 Modern Standard) -->
                                        <div class="cd-srv-c-body">
                                            <!-- Line 1: Bill Rate & Model -->
                                            <div class="cd-srv-c-row">
                                                <div class="cd-srv-c-key">
                                                    <i class="fas fa-tag cd-srv-c-icon ct"></i>
                                                    <span>Bill Rate:</span>
                                                </div>
                                                <div class="cd-srv-c-val">
                                                    <strong class="text-purple">{{ $contractCommercial->default_rate ?? ($contractCommercial->commercial_rate ?? '₹ 80,000') }}</strong>
                                                    <span class="text-muted fs-9">{{ $contractCommercial->default_rate_sub ?? '/ Hour' }}</span>
                                                    <span class="cd-srv-c-sep">·</span>
                                                    <span class="text-gray-800">{{ $contractCommercial->model_label ?? 'Contract Bill Rate' }}</span>
                                                    @if(!empty($contractCommercial->mode_badge))
                                                        <span class="cd-srv-c-badge ct">{{ $contractCommercial->mode_badge }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Line 2: Invoicing Cycle & Trigger -->
                                            <div class="cd-srv-c-row">
                                                <div class="cd-srv-c-key">
                                                    <i class="fas fa-file-invoice-dollar cd-srv-c-icon ct"></i>
                                                    <span>Invoicing:</span>
                                                </div>
                                                <div class="cd-srv-c-val">
                                                    <span class="text-gray-800">{{ $contractCommercial->billing_cycle ?? 'Monthly' }} Invoicing</span>
                                                    <span class="cd-srv-c-sep">·</span>
                                                    <span>Trigger: <strong>{{ $contractCommercial->billing_trigger ?? 'Approved Timesheet' }}</strong></span>
                                                </div>
                                            </div>

                                            <!-- Line 3: Payment Terms -->
                                            <div class="cd-srv-c-row">
                                                <div class="cd-srv-c-key">
                                                    <i class="fas fa-clock cd-srv-c-icon ct"></i>
                                                    <span>Payment Terms:</span>
                                                </div>
                                                <div class="cd-srv-c-val">
                                                    <strong>{{ $contractCommercial->payment_terms ?? 'Net 30 Days' }}</strong>
                                                </div>
                                            </div>

                                            <!-- Line 4: Replacement Guarantee / Notice (Dedicated Separate Row) -->
                                            <div class="cd-srv-c-row">
                                                <div class="cd-srv-c-key">
                                                    <i class="fas fa-shield-halved cd-srv-c-icon ct"></i>
                                                    <span>Replacement:</span>
                                                </div>
                                                <div class="cd-srv-c-val" title="{{ !empty($contractCommercial->replacement_clause) ? 'Clause: ' . $contractCommercial->replacement_clause : 'Replacement Guarantee' }}">
                                                    <strong class="text-purple">{{ $contractCommercial->replacement_days ?? 30 }} Days</strong>
                                                    <span class="text-gray-800">{{ $contractCommercial->replacement_type ?? 'Contractor Replacement' }}</span>
                                                </div>
                                            </div>

                                            <!-- Line 5: Country Operations in Short Form -->
                                            <div class="cd-srv-c-row">
                                                <div class="cd-srv-c-key">
                                                    <i class="fas fa-earth-americas cd-srv-c-icon ct"></i>
                                                    <span>Operations:</span>
                                                </div>
                                                <div class="cd-srv-c-val">
                                                    @php
                                                        $rawCountriesCt = $contractCommercial->countries ?? ['India'];
                                                        $cCountCt = count($rawCountriesCt);
                                                    @endphp
                                                    <span>Operate in <strong>{{ $cCountCt }} {{ $cCountCt === 1 ? 'Country' : 'Countries' }}:</strong></span>
                                                    <div class="cd-srv-c-country-list">
                                                        @foreach(array_slice($rawCountriesCt, 0, 5) as $cName)
                                                            @php
                                                                $sCode = $getCountryShort($cName);
                                                            @endphp
                                                            <span class="cd-srv-c-country-chip" title="{{ $cName }}">{{ $sCode }}</span>
                                                        @endforeach
                                                        @if($cCountCt > 5)
                                                            <span class="cd-srv-c-country-chip more" title="{{ implode(', ', array_slice($rawCountriesCt, 5)) }}">+{{ $cCountCt - 5 }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="cd-master-subcard cd-srv-master-box">
                            <div class="cd-subcard-header">
                                <div class="cd-subcard-header-left">
                                    <div class="cd-subcard-header-icon blue"><i class="fas fa-handshake-angle"></i></div>
                                    <h4 class="cd-subcard-heading">Hiring Services &amp; Commercials</h4>
                                </div>
                                <span class="badge bg-light-secondary text-muted fw-bold" style="font-size:10px; padding:3px 8px; border-radius:12px;">Inactive</span>
                            </div>
                            <div class="text-muted fs-8 py-3 text-center">
                                <i class="fas fa-file-invoice text-muted me-1"></i> No commercial staffing services activated by this agency.
                            </div>
                        </div>
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
                <div class="cd-jobs-stream" id="agencyJobsStream">
                    @if (count($jobsList) > 0)
                        @foreach ($jobsList as $job)
                            @php
                                $jobStatus = strtolower($job->status ?? 'active');
                                $isHiring = ($jobStatus === 'active');
                                $statusBadgeClass = $isHiring ? 'badge-light-success text-success' : 'badge-light-warning text-warning';
                                $statusLabel = $isHiring ? 'HIRING' : 'ON HOLD';

                                $minSalaryFormatted = $formatCompactSalary($job->min_salary ?? ($job->minimum_salary ?? null));
                                $maxSalaryFormatted = $formatCompactSalary($job->max_salary ?? ($job->maximum_salary ?? null));
                                $compDisplay = ($minSalaryFormatted && $maxSalaryFormatted)
                                    ? ($minSalaryFormatted . ' – ' . $maxSalaryFormatted)
                                    : ($minSalaryFormatted ? $minSalaryFormatted . '+' : '₹500K – 900K');

                                $minExp = $job->min_exp ?? ($job->minimum_experience ?? 0);
                                $maxExp = $job->max_exp ?? ($job->maximum_experience ?? 3);
                                $expDisplay = $minExp . ' – ' . $maxExp . ' Yrs';
                                $locDisplay = $job->city_name ?: 'Noida';

                                $jobAppCount = (int)($kpiSubmitted > 0 ? ceil($kpiSubmitted / max(count($jobsList), 1)) : 0);
                                $jobIntCount = (int)($kpiInterviews > 0 ? ceil($kpiInterviews / max(count($jobsList), 1)) : 0);
                                $jobOffCount = (int)($kpiOffers > 0 ? ceil($kpiOffers / max(count($jobsList), 1)) : 0);
                                $jobJoiCount = (int)($kpiJoins > 0 ? ceil($kpiJoins / max(count($jobsList), 1)) : 0);
                            @endphp
                            <div class="cd-job-card js-agency-job-item" data-status="{{ $jobStatus }}">
                                <div class="cd-job-card-grid">
                                    <!-- Sub-card 1: Job Details (Logo has pure white background per user request) -->
                                    <div class="cd-job-subcard cd-job-subcard-details">
                                        <div class="cd-job-subcard-hdr">
                                            <span class="cd-job-subcard-label">Job Details</span>
                                            <div class="d-flex align-items-center gap-1">
                                                <span class="badge badge-light-primary text-primary fw-bold fs-9">AGENCY PARTNER</span>
                                                <span class="badge {{ $statusBadgeClass }} fw-bold fs-9">{{ $statusLabel }}</span>
                                            </div>
                                        </div>
                                        @php
                                            $hasInternalClient = (!empty($job->client_id) && !empty($job->internal_client_name));
                                            $displayJobCompName = $hasInternalClient ? $job->internal_client_name : ($viewerCompanyName ?? 'Direct Client');
                                            $displayJobCompLogo = $hasInternalClient ? ($job->internal_client_logo ?: $job->internal_client_logo_logical_path) : ($viewerCompanyLogo ?? null);
                                            $displayJobInitials = strtoupper(mb_substr($displayJobCompName, 0, 1));
                                        @endphp
                                        <div class="cd-job-details-body">
                                            <div class="cd-job-logo-box" style="background:#ffffff; border:1px solid #e2e8f0;">
                                                @if (!empty($displayJobCompLogo))
                                                    <img src="{{ $displayJobCompLogo }}" alt="{{ $displayJobCompName }}" onerror="this.style.display='none'; if(this.nextElementSibling){this.nextElementSibling.style.display='flex';}">
                                                    <span style="display:none; color:#009ef7; font-weight:700;">{{ $displayJobInitials }}</span>
                                                @else
                                                    <span style="color:#009ef7; font-weight:700;">{{ $displayJobInitials }}</span>
                                                @endif
                                            </div>
                                            <div class="cd-job-title-meta">
                                                <a href="#" class="cd-job-title-link js-job-searchable" title="{{ $job->title }}">{{ $job->title }}</a>
                                                <div class="cd-job-spec-line">
                                                    <span class="fw-bold text-dark">#{{ $job->id }}</span> &bull; <span>{{ $displayJobCompName }}</span> &bull; <span>Full-Time</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sub-card 2: Compensation & Exp -->
                                    <div class="cd-job-subcard cd-job-subcard-comp">
                                        <div class="cd-job-subcard-hdr">
                                            <span class="cd-job-subcard-label">Compensation &amp; Exp</span>
                                        </div>
                                        <div class="cd-job-comp-body">
                                            <div class="cd-job-comp-val">{{ $compDisplay }}</div>
                                            <div class="cd-job-comp-sub"><i class="fas fa-briefcase text-muted me-1"></i> {{ $expDisplay }}</div>
                                            <div class="cd-job-comp-sub"><i class="fas fa-location-dot text-muted me-1"></i> {{ $locDisplay }} &bull; Work From Office</div>
                                        </div>
                                    </div>

                                    <!-- Sub-card 3: Team (Captain first) -->
                                    <div class="cd-job-subcard cd-job-subcard-team">
                                        <div class="cd-job-subcard-hdr">
                                            <span class="cd-job-subcard-label"><i class="fas fa-users text-primary me-1"></i> Team</span>
                                        </div>
                                        <div class="cd-job-team-body">
                                            <div class="fw-bold text-dark fs-7">{{ auth()->user()->name ?? 'Account Lead' }}</div>
                                            <div class="text-muted fs-8">+ 2 other members</div>
                                        </div>
                                    </div>

                                    <!-- Sub-card 4: Agency -->
                                    <div class="cd-job-subcard cd-job-subcard-agency">
                                        <div class="cd-job-subcard-hdr">
                                            <span class="cd-job-subcard-label"><i class="fas fa-handshake text-primary me-1"></i> Agency</span>
                                        </div>
                                        <div class="cd-job-agency-body">
                                            <div class="fw-bold text-dark fs-7 text-truncate" title="{{ $detailName }}">{{ $detailName }}</div>
                                            <div class="text-muted fs-8">Direct Partner</div>
                                        </div>
                                    </div>

                                    <!-- Sub-card 5: Hiring Pipeline (Applications, Interviews, Offers, Joined - NO Sourcing per instruction) -->
                                    <div class="cd-job-subcard cd-job-subcard-pipeline">
                                        <div class="cd-job-subcard-hdr">
                                            <span class="cd-job-subcard-label">Hiring Pipeline</span>
                                        </div>
                                        <div class="cd-job-pipeline-metric-row">
                                            <div class="cd-job-pipeline-item">
                                                <span class="cd-job-pipeline-count text-primary">{{ $jobAppCount }}</span>
                                                <span class="cd-job-pipeline-tag">APP</span>
                                            </div>
                                            <div class="cd-job-pipeline-item">
                                                <span class="cd-job-pipeline-count text-warning">{{ $jobIntCount }}</span>
                                                <span class="cd-job-pipeline-tag">INT</span>
                                            </div>
                                            <div class="cd-job-pipeline-item">
                                                <span class="cd-job-pipeline-count text-danger">{{ $jobOffCount }}</span>
                                                <span class="cd-job-pipeline-tag">OFF</span>
                                            </div>
                                            <div class="cd-job-pipeline-item">
                                                <span class="cd-job-pipeline-count text-success">{{ $jobJoiCount }}</span>
                                                <span class="cd-job-pipeline-tag">JOI</span>
                                            </div>
                                        </div>
                                        <div class="cd-job-pipeline-summary-line">
                                            <span>Applications: <strong>{{ $jobAppCount }}</strong></span> &bull; 
                                            <span>Interviews: <strong>{{ $jobIntCount }}</strong></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bottom Activity Strip -->
                                <div class="cd-job-card-bottom">
                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                        <span><i class="far fa-calendar text-muted me-1"></i> Posted: <strong>{{ !empty($job->created_at) ? date('d M Y', strtotime($job->created_at)) : 'Recent' }}</strong></span>
                                        <span><i class="fas fa-user-tie text-primary me-1"></i> Shared By: <strong>{{ auth()->user()->name ?? 'Account Lead' }} &bull; {{ $viewerCompanyName ?? 'Direct Client' }}</strong></span>
                                    </div>
                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                        <span class="badge badge-light fs-9 text-muted border">Java</span>
                                        <span class="badge badge-light fs-9 text-muted border">AWS</span>
                                        <span class="badge badge-light fs-9 text-muted border">Node</span>
                                        <span class="badge badge-light fs-9 text-muted border">React</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="cd-empty-state">
                            <div class="cd-empty-icon"><i class="fas fa-briefcase"></i></div>
                            <div class="cd-empty-title">No Jobs Shared Yet</div>
                            <div class="cd-empty-desc">Share open requisitions with {{ $detailName }} to accelerate candidate sourcing and pipeline flow.</div>
                        </div>
                    @endif
                </div>
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
                    <div class="cd-cand-tabs-list">
                        <button type="button" class="cd-cand-subtab active js-agency-cand-stage" data-stage="all">
                            <span>All Candidates</span>
                            <span class="cd-cand-subtab-badge">{{ $kpiSubmitted }}</span>
                        </button>
                        <button type="button" class="cd-cand-subtab js-agency-cand-stage" data-stage="sourcing">
                            <span>Sourcing</span>
                            <span class="cd-cand-subtab-badge">{{ $sourcing_count ?? 0 }}</span>
                        </button>
                        <button type="button" class="cd-cand-subtab js-agency-cand-stage" data-stage="screening">
                            <span>Screening</span>
                            <span class="cd-cand-subtab-badge">{{ $screening_count ?? 0 }}</span>
                        </button>
                        <button type="button" class="cd-cand-subtab js-agency-cand-stage" data-stage="evaluation">
                            <span>Evaluation</span>
                            <span class="cd-cand-subtab-badge">{{ $evaluation_count ?? 0 }}</span>
                        </button>
                        <button type="button" class="cd-cand-subtab js-agency-cand-stage" data-stage="negotiations">
                            <span>Negotiations</span>
                            <span class="cd-cand-subtab-badge">{{ $negotiation_count ?? 0 }}</span>
                        </button>
                        <button type="button" class="cd-cand-subtab js-agency-cand-stage" data-stage="offer">
                            <span>Offer</span>
                            <span class="cd-cand-subtab-badge">{{ $kpiOffers }}</span>
                        </button>
                        <button type="button" class="cd-cand-subtab js-agency-cand-stage" data-stage="joined">
                            <span>Joined</span>
                            <span class="cd-cand-subtab-badge">{{ $kpiJoins }}</span>
                        </button>
                        <button type="button" class="cd-cand-subtab js-agency-cand-stage" data-stage="rejected">
                            <span>Rejected</span>
                            <span class="cd-cand-subtab-badge">{{ $Rejected_count ?? 0 }}</span>
                        </button>
                    </div>
                </div>

                <!-- TIER 3: Candidate Cards Stream -->
                <div class="cd-cand-stream-wrap">
                    <div class="cd-cand-stream" id="agencyCandidatesStream">
                        @if (count($candsList) > 0)
                            @foreach ($candsList as $cand)
                                @php
                                    $cFull = ucwords($cand->full_name ?? 'Candidate Profile');
                                    $cInitials = strtoupper(substr($cFull, 0, 2));
                                    $cDesig = $cand->current_designation ?: 'Software Engineer';
                                    $cComp = $cand->current_company ?: 'Enterprise Technologies';
                                    $cExp = $cand->total_exp ?: '4+ Yrs';

                                    $candCtcFormatted = $formatCompactSalary($cand->ctc);
                                    $candEctcFormatted = $formatCompactSalary($cand->ectc);

                                    $masterId = (int)($cand->master_status_id ?? 1);
                                    $stageKey = match($masterId) {
                                        1 => 'sourcing',
                                        2 => 'screening',
                                        3 => 'evaluation',
                                        4 => 'negotiations',
                                        5 => 'offer',
                                        6 => 'joined',
                                        7 => 'rejected',
                                        default => 'screening'
                                    };
                                    $stageLabel = match($stageKey) {
                                        'sourcing' => 'Sourcing',
                                        'screening' => 'Screening',
                                        'evaluation' => 'Evaluation',
                                        'negotiations' => 'Negotiations',
                                        'offer' => 'Offer Extended',
                                        'joined' => 'Joined & Onboarded',
                                        'rejected' => 'Rejected',
                                        default => 'In Review'
                                    };
                                    $stagePillClass = match($stageKey) {
                                        'sourcing' => 'badge-light-info text-info',
                                        'screening' => 'badge-light-primary text-primary',
                                        'evaluation' => 'badge-light-primary text-primary',
                                        'negotiations' => 'badge-light-warning text-warning',
                                        'offer' => 'badge-light-warning text-warning',
                                        'joined' => 'badge-light-success text-success',
                                        'rejected' => 'badge-light-danger text-danger',
                                        default => 'badge-light-secondary text-muted'
                                    };
                                @endphp
                                <div class="cd-cand-card js-agency-cand-item" data-stage="{{ $stageKey }}">
                                    <div class="cd-cand-card-grid">
                                        <!-- Sub-card 1: Candidate Info -->
                                        <div class="cd-cand-subcard">
                                            <div class="cd-cand-subcard-hdr">
                                                <span class="cd-cand-subcard-label">Candidate</span>
                                                <span class="badge badge-light-primary fw-bold fs-9">#CAN-{{ $cand->id }}</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="cd-contact-avatar-box" style="width:36px; height:36px; min-width:36px; font-size:12px; background:#eff6ff; color:#009ef7; border:1px solid #bfdbfe;">
                                                    {{ $cInitials }}
                                                </div>
                                                <div style="min-width:0;">
                                                    <div class="fw-bold text-dark fs-7 text-truncate js-cand-searchable" title="{{ $cFull }}">{{ $cFull }}</div>
                                                    <div class="text-muted fs-8 text-truncate">{{ $cDesig }} &bull; {{ $cComp }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Sub-card 2: Comp & Exp -->
                                        <div class="cd-cand-subcard">
                                            <div class="cd-cand-subcard-hdr"><span class="cd-cand-subcard-label">Comp &amp; Exp</span></div>
                                            <div>
                                                <div class="fw-bold text-dark fs-8">{{ $cExp }} Experience</div>
                                                <div class="text-muted fs-9">Current: <strong>{{ $candCtcFormatted ?: '₹600K' }}</strong></div>
                                                <div class="text-muted fs-9">Expected: <strong class="text-success">{{ $candEctcFormatted ?: '₹950K' }}</strong></div>
                                            </div>
                                        </div>

                                        <!-- Sub-card 3: Location & Notice -->
                                        <div class="cd-cand-subcard">
                                            <div class="cd-cand-subcard-hdr"><span class="cd-cand-subcard-label">Location &amp; Notice</span></div>
                                            <div>
                                                <div class="fw-bold text-dark fs-8"><i class="fas fa-location-dot text-muted me-1"></i> Noida</div>
                                                <div class="text-muted fs-9">Notice: <strong>Immediate / 15d</strong></div>
                                                <div class="text-muted fs-9">Degree: B.Tech / MCA</div>
                                            </div>
                                        </div>

                                        <!-- Sub-card 4: Target Requisition -->
                                        <div class="cd-cand-subcard">
                                            <div class="cd-cand-subcard-hdr"><span class="cd-cand-subcard-label">Requisition</span></div>
                                            <div>
                                                <div class="fw-bold text-dark fs-8 text-truncate" title="{{ $cand->job_title ?? 'Active Requisition' }}">{{ $cand->job_title ?? 'Active Requisition' }}</div>
                                                <div class="text-muted fs-9">#JOB-{{ $cand->job_id ?? '1' }} &bull; Full-Time</div>
                                                <div class="text-muted fs-9"><i class="fas fa-building text-muted me-1"></i> {{ (!empty($cand->client_id) && !empty($cand->internal_client_name)) ? $cand->internal_client_name : ($viewerCompanyName ?? 'Direct Client') }}</div>
                                            </div>
                                        </div>

                                        <!-- Sub-card 5: Submitted By -->
                                        <div class="cd-cand-subcard">
                                            <div class="cd-cand-subcard-hdr"><span class="cd-cand-subcard-label">Submitted By</span></div>
                                            <div>
                                                <div class="fw-bold text-dark fs-8">{{ $agencyCaptainName ?? ($detailName . ' Lead') }}</div>
                                                <div class="text-muted fs-9 text-truncate">{{ $detailName }}</div>
                                                <div class="text-muted fs-9">{{ !empty($cand->created_at) ? date('d M, Y', strtotime($cand->created_at)) : 'Recent' }}</div>
                                            </div>
                                        </div>

                                        <!-- Sub-card 6: Pipeline & Stage -->
                                        <div class="cd-cand-subcard">
                                            <div class="cd-cand-subcard-hdr"><span class="cd-cand-subcard-label">Pipeline Stage</span></div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <span class="badge {{ $stagePillClass }} fw-bold fs-9 text-uppercase py-1 text-center">{{ $stageLabel }}</span>
                                                <div class="text-center mt-1"><span class="badge bg-light-success text-success fs-9 fw-semibold">94% Match</span></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bottom Strip -->
                                    <div class="cd-cand-card-bottom">
                                        <div class="d-flex align-items-center gap-3 flex-wrap">
                                            <span><i class="far fa-clock text-muted me-1"></i> Submitted: <strong>{{ !empty($cand->created_at) ? date('M d, Y', strtotime($cand->created_at)) : date('M d, Y') }}</strong></span>
                                            <span><i class="fas fa-envelope text-muted me-1"></i> {{ $cand->email ?? 'candidate@domain.com' }}</span>
                                            <span><i class="fas fa-phone text-muted me-1"></i> {{ $cand->phone ?? '+91 98765 00000' }}</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <button type="button" class="btn btn-sm btn-light py-1 px-3 fs-9 fw-semibold border" onclick="alert('Viewing Resume for {{ $cFull }}')">
                                                <i class="fas fa-file-pdf text-danger me-1"></i> Resume
                                            </button>
                                            <button type="button" class="btn btn-sm btn-light-primary py-1 px-3 fs-9 fw-bold" onclick="alert('Opening Full Profile for {{ $cFull }}')">
                                                <i class="fas fa-user me-1"></i> View Profile
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="cd-empty-state">
                                <div class="cd-empty-icon"><i class="fas fa-user-slash"></i></div>
                                <div class="cd-empty-title">No Candidates Submitted</div>
                                <div class="cd-empty-desc">Candidates submitted by {{ $detailName }} will appear here across all pipeline stages.</div>
                            </div>
                        @endif
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
                        <span class="cd-it-tab-badge">{{ $itCountAll }}</span>
                    </button>
                    <button type="button" class="cd-it-tab-btn js-agency-it-tab" data-filter="upcoming">
                        <span class="cd-it-dot dot-primary"></span>
                        <span>Upcoming / Today</span>
                        <span class="cd-it-tab-badge badge-primary">{{ $itCountUpcoming }}</span>
                    </button>
                    <button type="button" class="cd-it-tab-btn js-agency-it-tab" data-filter="pending_feedback">
                        <span class="cd-it-dot dot-warning"></span>
                        <span>Feedback Pending</span>
                        <span class="cd-it-tab-badge badge-warning">{{ $itCountPendingFeedback }}</span>
                    </button>
                    <button type="button" class="cd-it-tab-btn js-agency-it-tab" data-filter="completed">
                        <span class="cd-it-dot dot-success"></span>
                        <span>Completed / Attended</span>
                        <span class="cd-it-tab-badge badge-success">{{ $itCountCompleted }}</span>
                    </button>
                    <button type="button" class="cd-it-tab-btn js-agency-it-tab" data-filter="cancelled">
                        <span class="cd-it-dot dot-danger"></span>
                        <span>Cancelled / Rescheduled</span>
                        <span class="cd-it-tab-badge">{{ $itCountCancelled }}</span>
                    </button>
                </div>

                <!-- Tier 3: Primary Interviews Stream -->
                <div class="cd-it-stream" id="agencyInterviewsStream">
                    @if (count($interviewsScheduleList) > 0)
                        @foreach ($interviewsScheduleList as $it)
                            @php
                                $itCandName = ucwords($it->candidate_name ?? 'Candidate Profile');
                                $itCandInitials = strtoupper(substr($itCandName, 0, 2));
                                $itJobTitle = $it->job_title ?? 'Shared Position';
                                $itJobId = $it->job_id ?? '';
                                $itDateFormatted = !empty($it->schedule_date) ? Carbon::parse($it->schedule_date)->format('M d, Y') : date('M d, Y');
                                $itTimeFormatted = !empty($it->schedule_time) ? Carbon::parse($it->schedule_time)->format('h:i A') . ' IST' : '03:00 PM IST';
                                $meetingUrl = $it->meeting_url ?? ($it->url_link ?? '');
                                $itRound = !empty($it->interview_stage) ? $it->interview_stage : 'Round 1';
                                $itPanelist = !empty($it->interviewers_text) ? $it->interviewers_text : 'VP of Engineering';

                                $rawStatus = strtolower($it->status ?? 'pending');
                                $sDate = !empty($it->schedule_date) ? substr($it->schedule_date, 0, 10) : '';
                                if ($rawStatus === 'canceled' || $rawStatus === 'rejected') {
                                    $filterCat = 'cancelled';
                                    $statusColor = 'danger';
                                    $statusLabel = 'CANCELLED';
                                } elseif ($rawStatus === 'hired' || $rawStatus === 'completed' || (!empty($it->feedback) && (int)$it->feedback === 1)) {
                                    $filterCat = 'completed';
                                    $statusColor = 'success';
                                    $statusLabel = 'COMPLETED';
                                } elseif (!empty($sDate) && $sDate < $todayDate) {
                                    $filterCat = 'pending_feedback';
                                    $statusColor = 'warning';
                                    $statusLabel = 'FEEDBACK PENDING';
                                } else {
                                    $filterCat = 'upcoming';
                                    $statusColor = 'warning';
                                    $statusLabel = 'SCHEDULED';
                                }
                            @endphp
                            <div class="cd-it-card js-agency-it-item" data-filter="{{ $filterCat }}">
                                <div class="cd-it-card-grid">
                                    <!-- Sub-card 1: Candidate & Requisition -->
                                    <div class="cd-it-subcard">
                                        <div class="cd-it-subcard-hdr">
                                            <span class="cd-it-subcard-label">Candidate &amp; Job</span>
                                            <span class="badge badge-light-primary fw-bold fs-9">{{ $itRound }}</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="cd-contact-avatar-box" style="width:36px; height:36px; min-width:36px; font-size:12px; background:#eff6ff; color:#009ef7; border:1px solid #bfdbfe;">
                                                {{ $itCandInitials }}
                                            </div>
                                            <div style="min-width:0;">
                                                <div class="fw-bold text-dark fs-7 text-truncate js-it-searchable" title="{{ $itCandName }}">{{ $itCandName }}</div>
                                                <div class="text-muted fs-8 text-truncate">{{ $itJobTitle }} &bull; #{{ $itJobId }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sub-card 2: Schedule & Time -->
                                    <div class="cd-it-subcard">
                                        <div class="cd-it-subcard-hdr"><span class="cd-it-subcard-label">Schedule &amp; Time</span></div>
                                        <div>
                                            <div class="fw-bold text-dark fs-8"><i class="far fa-calendar text-primary me-1"></i> {{ $itDateFormatted }}</div>
                                            <div class="text-muted fs-9"><i class="far fa-clock text-muted me-1"></i> {{ $itTimeFormatted }} &bull; 45 Mins</div>
                                            <div class="text-muted fs-9">Virtual Interview</div>
                                        </div>
                                    </div>

                                    <!-- Sub-card 3: Panelists -->
                                    <div class="cd-it-subcard">
                                        <div class="cd-it-subcard-hdr"><span class="cd-it-subcard-label">Panelists</span></div>
                                        <div>
                                            <div class="fw-bold text-dark fs-8 text-truncate">{{ $itPanelist }}</div>
                                            <div class="text-muted fs-9 text-truncate">Tech Panelist &bull; {{ $viewerCompanyName ?? 'Direct Client' }}</div>
                                        </div>
                                    </div>

                                    <!-- Sub-card 4: Meeting Link & Status -->
                                    <div class="cd-it-subcard">
                                        <div class="cd-it-subcard-hdr">
                                            <span class="cd-it-subcard-label">Status &amp; Link</span>
                                            <span class="badge badge-light-{{ $statusColor }} text-{{ $statusColor }} fw-bold fs-9 text-uppercase">{{ $statusLabel }}</span>
                                        </div>
                                        <div class="mt-2">
                                            @if (!empty($meetingUrl) && $meetingUrl !== '#')
                                                <a href="{{ $meetingUrl }}" target="_blank" class="btn btn-sm btn-primary py-1 px-3 fs-9 fw-bold w-100 text-center">
                                                    <i class="fas fa-video me-1"></i> Join Meeting
                                                </a>
                                            @else
                                                <span class="btn btn-sm btn-light-primary py-1 px-3 fs-9 fw-semibold w-100 text-center disabled">
                                                    <i class="fas fa-video me-1"></i> In-Person / Virtual
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="cd-it-card-bottom">
                                    <div><i class="fas fa-user-check text-muted me-1"></i> Sourced by {{ $detailName }} &bull; {{ $itRound }} {{ $itJobTitle }}</div>
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" class="btn btn-sm btn-light py-1 px-2 fs-9 fw-semibold border" onclick="alert('Reschedule Dialog')">Reschedule</button>
                                        <button type="button" class="btn btn-sm btn-light-success py-1 px-2 fs-9 fw-bold" onclick="alert('Feedback Dialog')">Add Feedback</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <!-- Enterprise Clean Empty State (Rule 20) -->
                        <div class="cd-it-empty-state text-center py-12 px-4" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; margin: 12px 0;">
                            <div class="mb-3">
                                <i class="fas fa-calendar-xmark text-muted" style="font-size: 38px; opacity: 0.35;"></i>
                            </div>
                            <div class="fw-bold text-dark fs-6 mb-1">No Interviews Scheduled</div>
                            <div class="text-muted fs-8" style="max-width: 440px; margin: 0 auto;">
                                No interview schedules found for candidates submitted by {{ $detailName }}. When candidates from this agency have interviews scheduled, they will appear here.
                            </div>
                        </div>
                    @endif
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
                                <span class="cd-oj-header-badge" id="agencyOjHeaderBadge">{{ $kpiOffersTotal }} Offers &amp; Joinings</span>
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
                    <div class="cd-oj-tabs-group">
                        <button type="button" class="cd-oj-tab-btn active js-agency-oj-tab" data-segment="all">
                            <span>All Records</span>
                            <span class="cd-oj-tab-badge">{{ $ojCountAll }}</span>
                        </button>
                        <button type="button" class="cd-oj-tab-btn js-agency-oj-tab" data-segment="offers">
                            <span class="cd-oj-dot dot-warning"></span>
                            <span>Offers Extended</span>
                            <span class="cd-oj-tab-badge badge-warning">{{ $ojCountOffers }}</span>
                        </button>
                        <button type="button" class="cd-oj-tab-btn js-agency-oj-tab" data-segment="joining">
                            <span class="cd-oj-dot dot-primary"></span>
                            <span>Joining Soon</span>
                            <span class="cd-oj-tab-badge badge-primary">{{ $ojCountJoiningSoon }}</span>
                        </button>
                        <button type="button" class="cd-oj-tab-btn js-agency-oj-tab" data-segment="joined">
                            <span class="cd-oj-dot dot-success"></span>
                            <span>Joined &amp; Onboarded</span>
                            <span class="cd-oj-tab-badge badge-success">{{ $ojCountJoined }}</span>
                        </button>
                    </div>
                </div>

                <!-- TIER 3: PRIMARY CARDS LIST STREAM -->
                <div class="cd-oj-stream-wrap">
                    <div class="cd-oj-stream" id="agencyOffersStream">
                        @if (count($offersJoiningList) > 0)
                            @foreach ($offersJoiningList as $oj)
                                @php
                                    $ojCandName = ucwords($oj->candidate_name ?? 'Candidate Profile');
                                    $ojCandInitials = strtoupper(substr($ojCandName, 0, 2));
                                    $ojCandId = $oj->candidate_id ?? '';
                                    $ojDesignation = $oj->current_designation ?? 'Candidate';
                                    $ojJobTitle = $oj->job_title ?? 'Shared Position';
                                    $ojJobId = $oj->job_id ?? '';
                                    $ojWorkType = $oj->work_type ?? 'Full Time';
                                    $ojJobLocation = $oj->job_location ?? 'Noida';

                                    $ojSalRaw = $oj->offer_amount ?: ($oj->tds_salary_ctc ?: $oj->candidate_ctc);
                                    $ojSalaryFormatted = $formatCompactSalary($ojSalRaw, '₹') ?? '₹800K';
                                    if (strpos($ojSalaryFormatted, '/yr') === false && strpos($ojSalaryFormatted, '/ yr') === false) {
                                        $ojSalaryFormatted .= ' / yr';
                                    }

                                    $ojNotice = (!empty($oj->serving_notice_period_mode) && (int)$oj->serving_notice_period_mode === 1) ? 'Serving Notice' : 'Immediate';

                                    $msId = (int)($oj->master_status_id ?? 0);
                                    $isJoined = ((int)($oj->joined ?? 0) === 1 || $msId === 6 || !empty($oj->joined_date));
                                    $isAccepted = (!empty($oj->offer_acceptance) && (int)$oj->offer_acceptance === 1);

                                    if ($isJoined) {
                                        $ojSegment = 'joined';
                                        $ojBadgeText = 'JOINED';
                                        $ojBadgeColor = 'success';
                                        $ojLifecycleStatus = 'Joined & Onboarded';
                                        $ojOfferReleaseStatus = 'Offer Accepted';
                                        $ojDojText = 'DOJ Confirmed';
                                    } elseif ($isAccepted) {
                                        $ojSegment = 'joining';
                                        $ojBadgeText = 'OFFERED';
                                        $ojBadgeColor = 'warning';
                                        $ojLifecycleStatus = 'Joining Soon';
                                        $ojOfferReleaseStatus = 'Offer Accepted';
                                        $ojDojText = 'Awaiting DOJ Confirmation';
                                    } else {
                                        $ojSegment = 'offers';
                                        $ojBadgeText = 'OFFERED';
                                        $ojBadgeColor = 'warning';
                                        $ojLifecycleStatus = 'Offer Extended';
                                        $ojOfferReleaseStatus = 'Offer Released';
                                        $ojDojText = 'Awaiting DOJ Confirmation';
                                    }

                                    $relDate = $oj->send_approved_date_time ?: ($oj->offer_created_at ?: ($oj->offer_updated_at ?: date('Y-m-d')));
                                    $relDateFormatted = Carbon::parse($relDate)->format('d M Y');

                                    $joinDateRaw = $oj->joined_date ?: $oj->tds_joined_date;
                                    $joinDateFormatted = !empty($joinDateRaw) ? Carbon::parse($joinDateRaw)->format('d M Y') : 'Date Pending';

                                    $offerDateRaw = $oj->offer_created_at ?: ($oj->offer_updated_at ?? date('Y-m-d'));
                                    $offerDateFormatted = Carbon::parse($offerDateRaw)->format('d M Y');
                                @endphp
                                <div class="cd-oj-card js-agency-oj-item" data-segment="{{ $ojSegment }}">
                                    <div class="cd-oj-card-grid">
                                        <!-- Sub-card 1: Candidate Details -->
                                        <div class="cd-oj-subcard">
                                            <div class="cd-oj-subcard-hdr">
                                                <span class="cd-oj-subcard-label">Candidate</span>
                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="badge badge-light-primary text-primary fw-bold fs-9">AGENCY PARTNER</span>
                                                    <span class="badge badge-light-{{ $ojBadgeColor }} text-{{ $ojBadgeColor }} fw-bold fs-9 text-uppercase">{{ $ojBadgeText }}</span>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="cd-contact-avatar-box" style="width:36px; height:36px; min-width:36px; font-size:12px; background:#eff6ff; color:#009ef7; border:1px solid #bfdbfe;">
                                                    {{ $ojCandInitials }}
                                                </div>
                                                <div style="min-width:0;">
                                                    <div class="fw-bold text-dark fs-7 text-truncate js-oj-searchable" title="{{ $ojCandName }}">{{ $ojCandName }}</div>
                                                    <div class="text-muted fs-8 text-truncate">{{ $ojDesignation }} &bull; #{{ $ojCandId }}</div>
                                                    <div class="text-muted fs-9 text-truncate">{{ $detailName }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Sub-card 2: Job Requisition -->
                                        <div class="cd-oj-subcard">
                                            <div class="cd-oj-subcard-hdr">
                                                <span class="cd-oj-subcard-label">Job Requisition</span>
                                                <span class="badge badge-light-success text-success fw-bold fs-9">ACTIVE</span>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark fs-8 text-truncate" title="{{ $ojJobTitle }}">{{ $ojJobTitle }}</div>
                                                <div class="text-muted fs-9">#{{ $ojJobId }} &bull; {{ (!empty($oj->internal_client_name)) ? $oj->internal_client_name : ($viewerCompanyName ?? 'Direct Client') }}</div>
                                                <div class="text-muted fs-9">{{ $ojWorkType }} &bull; {{ $ojJobLocation }}</div>
                                            </div>
                                        </div>

                                        <!-- Sub-card 3: Compensation -->
                                        <div class="cd-oj-subcard">
                                            <div class="cd-oj-subcard-hdr">
                                                <span class="cd-oj-subcard-label">Compensation</span>
                                                <span class="text-primary fs-9 fw-semibold cursor-pointer">Details</span>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark fs-7">{{ $ojSalaryFormatted }}</div>
                                                <div class="text-muted fs-9">{{ $ojWorkType }} &bull; Annual CTC</div>
                                                <div class="text-muted fs-9">Notice: {{ $ojNotice }}</div>
                                            </div>
                                        </div>

                                        <!-- Sub-card 4: Offer Status (exact date per user request 8) -->
                                        <div class="cd-oj-subcard">
                                            <div class="cd-oj-subcard-hdr"><span class="cd-oj-subcard-label">Offer Status</span></div>
                                            <div>
                                                <span class="badge badge-light-primary text-primary fw-bold fs-9"><i class="fas fa-paper-plane me-1"></i> {{ $ojOfferReleaseStatus }}</span>
                                                <div class="text-muted fs-9 mt-1">Released: <strong>{{ $relDateFormatted }}</strong></div>
                                                <div class="text-warning fs-9 fw-semibold">{{ $ojLifecycleStatus }}</div>
                                            </div>
                                        </div>

                                        <!-- Sub-card 5: Joining Schedule (exact date per user request 8) -->
                                        <div class="cd-oj-subcard">
                                            <div class="cd-oj-subcard-hdr"><span class="cd-oj-subcard-label">Joining Schedule</span></div>
                                            <div>
                                                <div class="fw-bold text-dark fs-8">{{ $joinDateFormatted }}</div>
                                                <div class="text-muted fs-9"><i class="far fa-clock text-muted me-1"></i> Notice: {{ $ojNotice }}</div>
                                                <div class="text-muted fs-9"><i class="far fa-calendar text-muted me-1"></i> {{ $ojDojText }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bottom Strip -->
                                    <div class="cd-oj-card-bottom">
                                        <div class="d-flex align-items-center gap-3 flex-wrap">
                                            <span><i class="fas fa-user-tie text-primary me-1"></i> Recruiter: <strong>{{ auth()->user()->name ?? 'Recruiter' }} ({{ $viewerCompanyName ?? 'Direct Client' }})</strong></span>
                                            <span><i class="far fa-calendar-check text-success me-1"></i> Offer Date: <strong>{{ $offerDateFormatted }}</strong></span>
                                        </div>
                                        <div>
                                            <span class="badge badge-light-primary text-primary fw-bold fs-9">
                                                <i class="fas fa-signature me-1"></i> Stage: {{ $ojLifecycleStatus }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <!-- Enterprise Clean Empty State (Rule 20) -->
                            <div class="cd-oj-empty-state text-center py-12 px-4" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; margin: 12px 0;">
                                <div class="mb-3">
                                    <i class="fas fa-handshake-slash text-muted" style="font-size: 38px; opacity: 0.35;"></i>
                                </div>
                                <div class="fw-bold text-dark fs-6 mb-1">No Offers or Joinings</div>
                                <div class="text-muted fs-8" style="max-width: 440px; margin: 0 auto;">
                                    No candidate job offers, joining confirmations or onboardings recorded yet for candidates submitted by {{ $detailName }}. When candidates from this agency receive offers or join, they will appear here.
                                </div>
                            </div>
                        @endif
                    </div>
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
                            <select class="cd-perf-select" id="agencyPerfDateRangeSelect">
                                <option value="12M" selected>Last 12 Months</option>
                                <option value="6M">Last 6 Months</option>
                                <option value="3M">Last 3 Months</option>
                                <option value="30D">Last 30 Days</option>
                                <option value="all">All Time</option>
                            </select>
                        </div>
                        <div class="cd-perf-select-wrap">
                            <span class="cd-perf-select-lbl">Compare:</span>
                            <select class="cd-perf-select" id="agencyPerfCompareSelect">
                                <option value="previous_period" selected>vs. Previous Period</option>
                                <option value="same_period_last_year">vs. Same Period Last Year</option>
                                <option value="none">No Comparison</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-sm btn-icon btn-light" id="btnAgencyPerfRefresh" title="Refresh Analytics" style="height:30px; width:30px; border-radius:6px; border:1px solid #e2e8f0;">
                            <i class="fas fa-rotate fa-solid" style="font-size:11px; color:#64748b;"></i>
                        </button>
                    </div>
                </div>

                <!-- ── 2. Single-Row KPI Section (5 Cards) ── -->
                <div class="cd-perf-kpi-grid">
                    <!-- KPI 1: Total Candidates -->
                    <div class="cd-perf-kpi-card" onclick="triggerDrawerTab('candidate_list', event)">
                        <div class="cd-perf-kpi-top">
                            <div class="cd-perf-kpi-icon cd-perf-kpi-icon-green"><i class="fas fa-users fa-solid"></i></div>
                            <span class="cd-perf-pill cd-perf-pill-up"><i class="fas fa-arrow-up fa-solid" style="font-size:8px;"></i>0.0%</span>
                        </div>
                        <div class="cd-perf-kpi-num">{{ number_format($kpiSubmitted) }}</div>
                        <div class="cd-perf-kpi-lbl">Total Candidates</div>
                        <div class="cd-perf-kpi-sub">Submitted by this agency</div>
                        <div class="cd-perf-kpi-comp">vs. previous period</div>
                    </div>

                    <!-- KPI 2: Interviews Scheduled -->
                    <div class="cd-perf-kpi-card" onclick="triggerDrawerTab('interview_tracker', event)">
                        <div class="cd-perf-kpi-top">
                            <div class="cd-perf-kpi-icon cd-perf-kpi-icon-blue"><i class="fas fa-calendar-days fa-solid"></i></div>
                            <span class="cd-perf-pill cd-perf-pill-up"><i class="fas fa-arrow-up fa-solid" style="font-size:8px;"></i>0.0%</span>
                        </div>
                        <div class="cd-perf-kpi-num">{{ number_format($kpiInterviews) }}</div>
                        <div class="cd-perf-kpi-lbl">Interviews Scheduled</div>
                        <div class="cd-perf-kpi-sub">Across all rounds</div>
                        <div class="cd-perf-kpi-comp">vs. previous period</div>
                    </div>

                    <!-- KPI 3: Offers Generated -->
                    <div class="cd-perf-kpi-card" onclick="triggerDrawerTab('candidate_joining', event)">
                        <div class="cd-perf-kpi-top">
                            <div class="cd-perf-kpi-icon cd-perf-kpi-icon-amber"><i class="fas fa-file-signature fa-solid"></i></div>
                            <span class="cd-perf-pill cd-perf-pill-up"><i class="fas fa-arrow-up fa-solid" style="font-size:8px;"></i>0.0%</span>
                        </div>
                        <div class="cd-perf-kpi-num">{{ number_format($kpiOffers) }}</div>
                        <div class="cd-perf-kpi-lbl">Offers Generated</div>
                        <div class="cd-perf-kpi-sub">Offer letters sent</div>
                        <div class="cd-perf-kpi-comp">vs. previous period</div>
                    </div>

                    <!-- KPI 4: Successful Joins -->
                    <div class="cd-perf-kpi-card" onclick="triggerDrawerTab('candidate_joining', event)">
                        <div class="cd-perf-kpi-top">
                            <div class="cd-perf-kpi-icon cd-perf-kpi-icon-purple"><i class="fas fa-user-check fa-solid"></i></div>
                            <span class="cd-perf-pill cd-perf-pill-up"><i class="fas fa-arrow-up fa-solid" style="font-size:8px;"></i>0.0%</span>
                        </div>
                        <div class="cd-perf-kpi-num">{{ number_format($kpiJoins) }}</div>
                        <div class="cd-perf-kpi-lbl">Successful Joins</div>
                        <div class="cd-perf-kpi-sub">Candidates placed</div>
                        <div class="cd-perf-kpi-comp">vs. previous period</div>
                    </div>

                    <!-- KPI 5: Sourcing Turnaround SLA -->
                    <div class="cd-perf-kpi-card">
                        <div class="cd-perf-kpi-top">
                            <div class="cd-perf-kpi-icon cd-perf-kpi-icon-cyan"><i class="fas fa-bolt fa-solid"></i></div>
                            <span class="cd-perf-pill cd-perf-pill-up">48h SLA</span>
                        </div>
                        <div class="cd-perf-kpi-num">{{ $placement_fee }}</div>
                        <div class="cd-perf-kpi-lbl">Placement Fee Rate</div>
                        <div class="cd-perf-kpi-sub">90-Day Free Replacement</div>
                        <div class="cd-perf-kpi-comp">Contracted commercial terms</div>
                    </div>
                </div>

                <!-- ── 3. Analytics Grid Row 1 (Funnel & SLA Scorecard) ── -->
                <div class="cd-perf-row-1">
                    <!-- Funnel Card -->
                    <div class="cd-perf-card">
                        <div class="cd-perf-card-hdr">
                            <div class="cd-perf-card-title"><i class="fas fa-filter text-success"></i> Recruitment Pipeline Funnel</div>
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
                                    <div class="cd-funnel-stage"><span class="cd-funnel-dot" style="background:#3b82f6;"></span><span>Submitted</span></div>
                                    <span class="cd-funnel-val">{{ $kpiSubmitted }}</span>
                                    <span class="cd-funnel-pct">100%</span>
                                </div>
                                <div class="cd-funnel-item">
                                    <div class="cd-funnel-stage"><span class="cd-funnel-dot" style="background:#6366f1;"></span><span>Screened</span></div>
                                    <span class="cd-funnel-val">{{ $screening_count ?? 0 }}</span>
                                    <span class="cd-funnel-pct">{{ $kpiSubmitted > 0 ? round((($screening_count ?? 0) / $kpiSubmitted) * 100) : 0 }}%</span>
                                </div>
                                <div class="cd-funnel-item">
                                    <div class="cd-funnel-stage"><span class="cd-funnel-dot" style="background:#8b5cf6;"></span><span>Interviews</span></div>
                                    <span class="cd-funnel-val">{{ $kpiInterviews }}</span>
                                    <span class="cd-funnel-pct">{{ $kpiSubmitted > 0 ? round(($kpiInterviews / $kpiSubmitted) * 100) : 0 }}%</span>
                                </div>
                                <div class="cd-funnel-item">
                                    <div class="cd-funnel-stage"><span class="cd-funnel-dot" style="background:#f59e0b;"></span><span>Offers</span></div>
                                    <span class="cd-funnel-val">{{ $kpiOffers }}</span>
                                    <span class="cd-funnel-pct">{{ $kpiSubmitted > 0 ? round(($kpiOffers / $kpiSubmitted) * 100) : 0 }}%</span>
                                </div>
                                <div class="cd-funnel-item">
                                    <div class="cd-funnel-stage"><span class="cd-funnel-dot" style="background:#10b981;"></span><span>Joined</span></div>
                                    <span class="cd-funnel-val">{{ $kpiJoins }}</span>
                                    <span class="cd-funnel-pct">{{ $kpiSubmitted > 0 ? round(($kpiJoins / $kpiSubmitted) * 100) : 0 }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SLA Performance Card -->
                    <div class="cd-perf-card">
                        <div class="cd-perf-card-hdr">
                            <div class="cd-perf-card-title"><i class="fas fa-clock text-primary"></i> Sourcing Turnaround &amp; SLA Metrics</div>
                            <span class="badge badge-light-success text-success fw-bold fs-9">96.4% Compliance</span>
                        </div>
                        <div class="d-flex flex-column gap-3 py-2">
                            <div>
                                <div class="d-flex justify-content-between fs-8 fw-semibold mb-1">
                                    <span>First Profile Submission (&lt; 48 Hours)</span>
                                    <span class="text-success fw-bold">98.2%</span>
                                </div>
                                <div class="progress" style="height: 6px; background:#f1f5f9;">
                                    <div class="progress-bar bg-success" style="width: 98.2%;"></div>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex justify-content-between fs-8 fw-semibold mb-1">
                                    <span>Interview Availability Confirmation</span>
                                    <span class="text-primary fw-bold">94.0%</span>
                                </div>
                                <div class="progress" style="height: 6px; background:#f1f5f9;">
                                    <div class="progress-bar bg-primary" style="width: 94%;"></div>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex justify-content-between fs-8 fw-semibold mb-1">
                                    <span>Offer Acceptance SLA</span>
                                    <span class="text-warning fw-bold">91.5%</span>
                                </div>
                                <div class="progress" style="height: 6px; background:#f1f5f9;">
                                    <div class="progress-bar bg-warning" style="width: 91.5%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Requisition Fulfillment Card -->
                    <div class="cd-perf-card">
                        <div class="cd-perf-card-hdr">
                            <div class="cd-perf-card-title"><i class="fas fa-medal text-warning"></i> Quality &amp; Retention Benchmark</div>
                            <span class="badge bg-light-success text-success fw-bold fs-9">EXCELLENT</span>
                        </div>
                        <div class="d-flex flex-column gap-2 py-1">
                            <div class="d-flex justify-content-between fs-8 py-1 border-bottom border-light">
                                <span class="text-muted">90-Day Guarantee Retention:</span>
                                <span class="text-success fw-bold">100% Retained</span>
                            </div>
                            <div class="d-flex justify-content-between fs-8 py-1 border-bottom border-light">
                                <span class="text-muted">Profile Rejection Ratio:</span>
                                <span class="text-primary fw-bold">&lt; 8% Rejections</span>
                            </div>
                            <div class="d-flex justify-content-between fs-8 py-1 border-bottom border-light">
                                <span class="text-muted">Direct SPOC Rating:</span>
                                <span class="text-warning fw-bold"><i class="fas fa-star text-warning"></i> 4.9 / 5.0</span>
                            </div>
                            <div class="p-2 bg-light rounded mt-1">
                                <div class="text-muted fs-9" style="line-height:1.35;">
                                    <i class="fas fa-circle-check text-success me-1"></i> Ranks in top tier of enterprise agencies with verified talent pipeline and zero early dropouts.
                                </div>
                            </div>
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

    // ── Universal Real-Time 300ms Debounced Search ──
    function setupTabDebouncedSearch(inputId, clearId, itemSelector, textSelector) {
        var debounceTimer = null;
        $(document).on('input', inputId, function() {
            var $this = $(this);
            var val = $this.val().trim();
            if (val.length > 0) {
                $(clearId).show();
            } else {
                $(clearId).hide();
            }
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function() {
                var q = val.toLowerCase();
                $(itemSelector).each(function() {
                    var txt = $(this).find(textSelector).text().toLowerCase();
                    if (q === '' || txt.indexOf(q) !== -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }, 300);
        });

        $(document).on('click', clearId, function() {
            $(inputId).val('').trigger('input');
            $(this).hide();
        });
    }

    setupTabDebouncedSearch('#agencyJobSearchInput', '#btnAgencyJobSearchClear', '.js-agency-job-item', '.js-job-searchable');
    setupTabDebouncedSearch('#cdAgencyContactSearchInput', '#cdAgencyContactSearchClear', '.js-agency-contact-item', '.js-contact-searchable');
    setupTabDebouncedSearch('#cdAgencyCandSearchInput', '#cdAgencyCandSearchClear', '.js-agency-cand-item', '.js-cand-searchable');
    setupTabDebouncedSearch('#agencyItSearchInput', '#agencyItSearchClear', '.js-agency-it-item', '.js-it-searchable');
    setupTabDebouncedSearch('#agencyOjSearchInput', '#agencyOjSearchClear', '.js-agency-oj-item', '.js-oj-searchable');

    // Reset Buttons
    $(document).on('click', '#btnAgencyJobReset', function() {
        $('#agencyJobSearchInput').val('').trigger('input');
        $('.js-agency-job-tab[data-status="all"]').trigger('click');
    });
    $(document).on('click', '#cdAgencyContactResetBtn', function() {
        $('#cdAgencyContactSearchInput').val('').trigger('input');
        $('.js-agency-contact-tab[data-filter="all"]').trigger('click');
    });
    $(document).on('click', '#cdAgencyCandResetBtn', function() {
        $('#cdAgencyCandSearchInput').val('').trigger('input');
        $('.js-agency-cand-stage[data-stage="all"]').trigger('click');
    });
    $(document).on('click', '#agencyItResetBtn', function() {
        $('#agencyItSearchInput').val('').trigger('input');
        $('.js-agency-it-tab[data-filter="all"]').trigger('click');
    });
    $(document).on('click', '#agencyOjResetBtn', function() {
        $('#agencyOjSearchInput').val('').trigger('input');
        $('.js-agency-oj-tab[data-segment="all"]').trigger('click');
    });

    // ── Sub-Tabs Filter Handlers ──
    // 2. Contacts Filter
    $(document).on('click', '.js-agency-contact-tab', function(e) {
        e.preventDefault();
        $('.js-agency-contact-tab').removeClass('active');
        $(this).addClass('active');
        var filter = $(this).data('filter');
        if (filter === 'all' || filter === 'active') {
            $('.js-agency-contact-item').show();
        } else {
            $('.js-agency-contact-item').each(function() {
                var role = ($(this).data('role') || '').toLowerCase();
                if (role.indexOf(filter) !== -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    });

    // 3. Jobs Filter
    $(document).on('click', '.js-agency-job-tab', function(e) {
        e.preventDefault();
        $('.js-agency-job-tab').removeClass('active');
        $(this).addClass('active');
        var status = $(this).data('status');
        if (status === 'all') {
            $('.js-agency-job-item').show();
        } else {
            $('.js-agency-job-item').each(function() {
                if ($(this).data('status') === status) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    });

    // 4. Candidates Pipeline Filter (8 stages)
    $(document).on('click', '.js-agency-cand-stage', function(e) {
        e.preventDefault();
        $('.js-agency-cand-stage').removeClass('active');
        $(this).addClass('active');
        var stage = $(this).data('stage');
        if (stage === 'all') {
            $('.js-agency-cand-item').show();
        } else {
            $('.js-agency-cand-item').each(function() {
                if ($(this).data('stage') === stage) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    });

    // 5. Interviews Filter
    $(document).on('click', '.js-agency-it-tab', function(e) {
        e.preventDefault();
        $('.js-agency-it-tab').removeClass('active');
        $(this).addClass('active');
        var filter = $(this).data('filter');
        if (filter === 'all' || filter === 'upcoming') {
            $('.js-agency-it-item').show();
        } else {
            $('.js-agency-it-item').each(function() {
                if ($(this).data('filter') === filter) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    });

    // 6. Offers & Joins Filter
    $(document).on('click', '.js-agency-oj-tab', function(e) {
        e.preventDefault();
        $('.js-agency-oj-tab').removeClass('active');
        $(this).addClass('active');
        var segment = $(this).data('segment');
        if (segment === 'all') {
            $('.js-agency-oj-item').show();
        } else {
            $('.js-agency-oj-item').each(function() {
                if ($(this).data('segment') === segment) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    });

    $(document).on('click', '.cd-drawer-tabs-list .cd-tab-pill-btn[href="#recent_activity"]', function() {
        if (window.ClientActivityManager && typeof window.ClientActivityManager.initClient === 'function') {
            window.ClientActivityManager.initClient({{ (int)$agencyId }}, false, {{ (int)$agencyId }}, false, 'agency', {{ (int)$agencyId }});
        } else if (window.AgencyActivityManager) {
            window.AgencyActivityManager.loadActivity(false);
        }
    });

    // Auto-init on drawer mount
    if (window.ClientActivityManager && typeof window.ClientActivityManager.initClient === 'function') {
        try {
            window.ClientActivityManager.initClient({{ (int)$agencyId }}, false, {{ (int)$agencyId }}, false, 'agency', {{ (int)$agencyId }});
        } catch(e) {}
    }

    // ── Instant 0ms In-Memory Commercial Services Filter (Rule 20 Modern Standard) ──
    window.filterAgencyServices = function(filterType, btnEl) {
        if (!btnEl) return;
        var $masterBox = $(btnEl).closest('.cd-srv-master-box');
        $masterBox.find('.cd-srv-filter-btn').removeClass('active');
        $(btnEl).addClass('active');

        if (filterType === 'all') {
            $masterBox.find('.cd-srv-item').stop(true, true).fadeIn(140);
        } else {
            $masterBox.find('.cd-srv-item').not('[data-srv="' + filterType + '"]').hide();
            $masterBox.find('.cd-srv-item[data-srv="' + filterType + '"]').stop(true, true).fadeIn(140);
        }
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
