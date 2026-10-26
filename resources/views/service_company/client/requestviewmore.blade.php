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
    $city_name = $city_name ?? (@$directClientDetail->city_name ?: (@$directClientDetail->city ?: ''));
    $country_name = $country_name ?? (@$directClientDetail->country_name ?: (@$directClientDetail->country ?: ''));

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

    // Workforce Scale Range calculation
    $cFrom = @$directClientDetail->company_size_from;
    $cTo = @$directClientDetail->company_size ?: @$directClientDetail->company_sizes;
    $workforceRange = '51 – 200 Employees';
    $tiersMap = [
      '1-1'         => '1 Employee (Solo)',
      '1-10'        => '1 – 10 Employees',
      '2-10'        => '2 – 10 Employees',
      '11-50'       => '11 – 50 Employees',
      '51-200'      => '51 – 200 Employees',
      '201-500'     => '201 – 500 Employees',
      '501-1000'    => '501 – 1,000 Employees',
      '1001-5000'   => '1,001 – 5,000 Employees',
      '5001-10000'  => '5,001 – 10,000 Employees',
      '10001-50000' => '10,001 – 50,000 Employees',
      '50001-0'     => '50,000+ Employees',
    ];
    $rawCheck = (string)($cTo ?: $cFrom);
    if (str_contains($rawCheck, '-')) {
      $parts = explode('-', $rawCheck);
      $p1 = trim($parts[0]);
      $p2 = trim($parts[1] ?? '');
      if (isset($tiersMap[$p1.'-'.$p2])) {
        $workforceRange = $tiersMap[$p1.'-'.$p2];
      } elseif ($p2 === '' || $p2 === '0') {
        $workforceRange = (is_numeric($p1) ? number_format((float)$p1) : $p1) . '+ Employees';
      } else {
        $f1 = is_numeric($p1) ? number_format((float)$p1) : $p1;
        $f2 = is_numeric($p2) ? number_format((float)$p2) : $p2;
        $workforceRange = "{$f1} – {$f2} Employees";
      }
    } elseif (!empty($cFrom) && !empty($cTo)) {
      $key = (int)$cFrom . '-' . (int)$cTo;
      if (isset($tiersMap[$key])) {
        $workforceRange = $tiersMap[$key];
      } elseif ((int)$cFrom >= 50000 || (int)$cTo == 0) {
        $workforceRange = '50,000+ Employees';
      } else {
        $workforceRange = number_format((float)$cFrom) . ' – ' . number_format((float)$cTo) . ' Employees';
      }
    } elseif (!empty($cTo) && is_numeric($cTo)) {
      $val = (int)$cTo;
      if ($val <= 1) $workforceRange = '1 Employee (Solo)';
      elseif ($val <= 10) $workforceRange = '1 – 10 Employees';
      elseif ($val <= 50) $workforceRange = '11 – 50 Employees';
      elseif ($val <= 200) $workforceRange = '51 – 200 Employees';
      elseif ($val <= 500) $workforceRange = '201 – 500 Employees';
      elseif ($val <= 1000) $workforceRange = '501 – 1,000 Employees';
      elseif ($val <= 5000) $workforceRange = '1,001 – 5,000 Employees';
      elseif ($val <= 10000) $workforceRange = '5,001 – 10,000 Employees';
      elseif ($val <= 50000) $workforceRange = '10,001 – 50,000 Employees';
      else $workforceRange = '50,000+ Employees';
    } elseif (!empty($cTo)) {
      $workforceRange = (string)$cTo;
    }

    $webClean = @$directClientDetail->website ?: '';
    $primaryName = @$directClientDetail->contact_person ?? (@$directClientDetail->name ?? '');
    $primaryDesignation = @$directClientDetail->designation ?? 'Hiring Lead';
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
    .cd-top-actions {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        flex-wrap: wrap !important;
    }
    .cd-top-close-wrap {
        display: flex !important;
        align-items: center !important;
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

          /* ── Client Hero Banner: Option 2 — Linear / Stripe Continuous Header ── */
          .cd-hero-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            margin: 0 14px 12px 14px !important;
            padding: 0 !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
            width: calc(100% - 28px) !important;
            overflow: hidden !important;
          }
          .cd-hero-continuous-main {
            display: flex !important;
            align-items: stretch !important;
            justify-content: space-between !important;
            width: 100% !important;
          }
          @media (max-width: 992px) {
            .cd-hero-continuous-main {
              flex-direction: column !important;
            }
          }

          /* Left: Brand Identity & Status */
          .cd-hero-continuous-left {
            display: flex !important;
            align-items: center !important;
            gap: 14px !important;
            padding: 14px 18px !important;
            flex: 0 0 auto !important;
            min-width: 310px !important;
            max-width: 380px !important;
            border-right: 1px solid #eef2f6 !important;
            background: #ffffff !important;
          }
          @media (max-width: 992px) {
            .cd-hero-continuous-left {
              max-width: 100% !important;
              border-right: none !important;
              border-bottom: 1px solid #eef2f6 !important;
            }
          }
          .cd-hero-avatar-wrap {
            position: relative !important;
            flex-shrink: 0 !important;
          }
          .cd-hero-avatar {
            width: 52px !important;
            height: 52px !important;
            border-radius: 10px !important;
            background: linear-gradient(135deg, #4f46e5, #3b82f6) !important;
            color: #ffffff !important;
            border: 2px solid #ffffff !important;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08) !important;
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
            width: 17px !important;
            height: 17px !important;
            border-radius: 50% !important;
            background: #10b981 !important;
            border: 2px solid #ffffff !important;
            color: #ffffff !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 8px !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.15) !important;
          }
          .cd-hero-identity-body {
            flex: 1 !important;
            min-width: 0 !important;
          }
          .cd-hero-title-row {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            margin-bottom: 2px !important;
          }
          .cd-hero-name {
            font-size: 15px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            line-height: 1.25 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            letter-spacing: -0.2px !important;
            margin: 0 !important;
          }
          .cd-hero-id-badge {
            font-size: 10px !important;
            font-weight: 700 !important;
            color: #2563eb !important;
            background: #eff6ff !important;
            border: 1px solid #bfdbfe !important;
            padding: 1px 5px !important;
            border-radius: 4px !important;
            font-family: monospace !important;
            flex-shrink: 0 !important;
          }
          .cd-hero-subtitle {
            font-size: 11.5px !important;
            color: #64748b !important;
            font-weight: 500 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            margin-bottom: 5px !important;
            line-height: 1.2 !important;
          }
          .cd-hero-badge-row {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            flex-wrap: wrap !important;
          }
          .cd-pill-scope-internal {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            background: #eff6ff !important;
            color: #2563eb !important;
            border: 1px solid #bfdbfe !important;
            font-size: 10.5px !important;
            font-weight: 600 !important;
            padding: 2px 7px !important;
            border-radius: 5px !important;
            line-height: 1.2 !important;
          }
          .cd-pill-scope-direct {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            background: #ecfdf5 !important;
            color: #059669 !important;
            border: 1px solid #a7f3d0 !important;
            font-size: 10.5px !important;
            font-weight: 600 !important;
            padding: 2px 7px !important;
            border-radius: 5px !important;
            line-height: 1.2 !important;
          }
          .cd-status-badge-active {
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            background: #ecfdf5 !important;
            color: #059669 !important;
            border: 1px solid #a7f3d0 !important;
            padding: 2px 7px !important;
            border-radius: 5px !important;
            font-size: 10.5px !important;
            font-weight: 600 !important;
            line-height: 1.2 !important;
          }
          .cd-status-badge-inactive {
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            background: #f8fafc !important;
            color: #64748b !important;
            border: 1px solid #e2e8f0 !important;
            padding: 2px 7px !important;
            border-radius: 5px !important;
            font-size: 10.5px !important;
            font-weight: 600 !important;
            line-height: 1.2 !important;
          }
          .cd-pulse-emerald {
            width: 7px !important;
            height: 7px !important;
            border-radius: 50% !important;
            background: #10b981 !important;
            position: relative !important;
            flex-shrink: 0 !important;
          }
          .cd-pulse-emerald::after {
            content: "" !important;
            position: absolute !important;
            inset: -3px !important;
            border-radius: 50% !important;
            border: 1.5px solid #10b981 !important;
            animation: cdpulse 2s infinite !important;
          }
          .cd-pulse-dot {
            width: 7px !important;
            height: 7px !important;
            border-radius: 50% !important;
            flex-shrink: 0 !important;
          }
          .cd-pulse-grey {
            background: #94a3b8 !important;
          }

          /* ── Client Hero Banner: Streamlined Minimalist Header ── */
          .cd-hero-card.cd-hero-streamlined,
          .cd-hero-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            margin: 0 14px 12px 14px !important;
            padding: 0 !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
            width: calc(100% - 28px) !important;
            overflow: hidden !important;
          }
          .cd-hero-main-row {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding: 12px 18px !important;
            gap: 16px !important;
          }
          @media (max-width: 768px) {
            .cd-hero-main-row {
              flex-direction: column !important;
              align-items: flex-start !important;
              gap: 10px !important;
            }
          }
          .cd-hero-left-flank {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            min-width: 0 !important;
            flex: 1 1 auto !important;
          }
          .cd-hero-avatar-wrap {
            position: relative !important;
            flex-shrink: 0 !important;
          }
          .cd-hero-avatar {
            width: 44px !important;
            height: 44px !important;
            border-radius: 9px !important;
            background: linear-gradient(135deg, #064e3b, #059669) !important;
            color: #ffffff !important;
            border: 2px solid #ffffff !important;
            box-shadow: 0 2px 5px rgba(15, 23, 42, 0.08) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 15px !important;
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
            top: -2px !important;
            right: -2px !important;
            width: 15px !important;
            height: 15px !important;
            border-radius: 50% !important;
            background: #10b981 !important;
            border: 2px solid #ffffff !important;
            color: #ffffff !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 7.5px !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.15) !important;
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
          }
          .cd-hero-name {
            font-size: 15.5px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            line-height: 1.25 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            letter-spacing: -0.2px !important;
            margin: 0 !important;
          }
          .cd-hero-id-badge {
            font-size: 10.5px !important;
            font-weight: 700 !important;
            color: #059669 !important;
            background: #ecfdf5 !important;
            border: 1px solid #a7f3d0 !important;
            padding: 1px 6px !important;
            border-radius: 4px !important;
            font-family: monospace !important;
            flex-shrink: 0 !important;
          }
          .cd-hero-web-wrap {
            display: flex !important;
            align-items: center !important;
            font-size: 12px !important;
          }
          .cd-hero-web-link {
            display: inline-flex !important;
            align-items: center !important;
            gap: 5px !important;
            font-size: 12px !important;
            font-weight: 500 !important;
            color: #059669 !important;
            text-decoration: none !important;
            transition: color 0.15s ease !important;
          }
          .cd-hero-web-link:hover {
            text-decoration: underline !important;
            color: #047857 !important;
          }
          .cd-hero-web-empty {
            font-size: 11.5px !important;
            color: #94a3b8 !important;
          }
          .cd-hero-right-flank {
            display: flex !important;
            flex-direction: column !important;
            align-items: flex-end !important;
            gap: 6px !important;
            flex-shrink: 0 !important;
          }
          @media (max-width: 768px) {
            .cd-hero-right-flank {
              align-items: flex-start !important;
              width: 100% !important;
            }
          }
          .cd-hero-scope-row {
            display: flex !important;
            align-items: center !important;
          }
          .cd-pill-scope-internal {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            background: #eff6ff !important;
            color: #2563eb !important;
            border: 1px solid #bfdbfe !important;
            font-size: 10.5px !important;
            font-weight: 600 !important;
            padding: 2.5px 8px !important;
            border-radius: 5px !important;
            line-height: 1.2 !important;
          }
          .cd-pill-scope-direct {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            background: #ecfdf5 !important;
            color: #059669 !important;
            border: 1px solid #a7f3d0 !important;
            font-size: 10.5px !important;
            font-weight: 600 !important;
            padding: 2.5px 8px !important;
            border-radius: 5px !important;
            line-height: 1.2 !important;
          }
          .cd-hero-status-pos-row {
            display: inline-flex !important;
            align-items: center !important;
            gap: 8px !important;
            font-size: 12px !important;
          }
          .cd-status-badge-active {
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            background: #ecfdf5 !important;
            color: #059669 !important;
            border: 1px solid #a7f3d0 !important;
            padding: 2px 7px !important;
            border-radius: 5px !important;
            font-size: 10.5px !important;
            font-weight: 600 !important;
            line-height: 1.2 !important;
          }
          .cd-status-badge-inactive {
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            background: #f8fafc !important;
            color: #64748b !important;
            border: 1px solid #e2e8f0 !important;
            padding: 2px 7px !important;
            border-radius: 5px !important;
            font-size: 10.5px !important;
            font-weight: 600 !important;
            line-height: 1.2 !important;
          }
          .cd-pulse-emerald {
            width: 7px !important;
            height: 7px !important;
            border-radius: 50% !important;
            background: #10b981 !important;
            position: relative !important;
            flex-shrink: 0 !important;
          }
          .cd-pulse-emerald::after {
            content: "" !important;
            position: absolute !important;
            inset: -3px !important;
            border-radius: 50% !important;
            border: 1.5px solid #10b981 !important;
            animation: cdpulse 2s infinite !important;
          }
          .cd-pulse-dot {
            width: 7px !important;
            height: 7px !important;
            border-radius: 50% !important;
            flex-shrink: 0 !important;
          }
          .cd-pulse-grey {
            background: #94a3b8 !important;
          }
          .cd-status-sep {
            color: #cbd5e1 !important;
            font-weight: 300 !important;
            margin: 0 1px !important;
          }
          .cd-hero-positions-link {
            display: inline-flex !important;
            align-items: center !important;
            font-size: 11.5px !important;
            font-weight: 600 !important;
            color: #0f172a !important;
            text-decoration: none !important;
            cursor: pointer !important;
            padding: 2px 8px !important;
            border-radius: 5px !important;
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            transition: all 0.15s ease !important;
          }
          .cd-hero-positions-link:hover {
            background: #ecfdf5 !important;
            border-color: #a7f3d0 !important;
            color: #059669 !important;
          }
          .cd-hero-bottom-tags-row {
            background: #f8fafc !important;
            border-top: 1px solid #f1f5f9 !important;
            padding: 6px 18px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            font-size: 11.5px !important;
            gap: 8px !important;
            flex-wrap: wrap !important;
          }
          .cd-hero-tags-left {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            flex-wrap: wrap !important;
          }
          .cd-hero-tags-label {
            font-weight: 600 !important;
            color: #64748b !important;
            font-size: 11.5px !important;
            display: inline-flex !important;
            align-items: center !important;
            margin-right: 2px !important;
          }
          .cd-hero-tag-pill {
            display: inline-flex !important;
            align-items: center !important;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            color: #475569 !important;
            font-size: 11px !important;
            font-weight: 500 !important;
            padding: 1px 7px !important;
            border-radius: 4px !important;
            line-height: 1.3 !important;
          }
          .cd-hero-tag-more {
            display: inline-flex !important;
            align-items: center !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            color: #059669 !important;
            margin-left: 2px !important;
          }

          /* Bottom Continuous Activity Strip */
          .cd-hero-continuous-bottom {
            background: #f8fafc !important;
            border-top: 1px solid #eef2f6 !important;
            padding: 7px 18px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 12px !important;
            flex-wrap: wrap !important;
            font-size: 11px !important;
          }
          .cd-strip-left {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            color: #475569 !important;
            flex-wrap: wrap !important;
          }
          .cd-strip-metric {
            display: inline-flex !important;
            align-items: center !important;
            color: #0f172a !important;
            font-weight: 600 !important;
          }
          .cd-strip-sep {
            color: #cbd5e1 !important;
            font-size: 9px !important;
          }
          .cd-strip-item {
            display: inline-flex !important;
            align-items: center !important;
            color: #64748b !important;
          }
          .cd-strip-right {
            display: flex !important;
            align-items: center !important;
            gap: 5px !important;
            flex-wrap: wrap !important;
          }
          .cd-strip-tags-lbl {
            color: #94a3b8 !important;
            font-weight: 500 !important;
            font-size: 10.5px !important;
            margin-right: 2px !important;
          }
          .cd-strip-tag-pill {
            display: inline-block !important;
            background: #eff6ff !important;
            color: #2563eb !important;
            border: 1px solid #dbeafe !important;
            padding: 1.5px 7px !important;
            border-radius: 4px !important;
            font-size: 10.5px !important;
            font-weight: 500 !important;
            line-height: 1.2 !important;
            white-space: nowrap !important;
          }
          .cd-strip-tag-more {
            display: inline-block !important;
            font-size: 10.5px !important;
            font-weight: 600 !important;
            color: #2563eb !important;
            padding: 1px 4px !important;
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
        padding: 8px 10px !important;
        margin: 0 14px 12px 14px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
        width: auto !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
        display: flex !important;
        align-items: center !important;
        min-height: 48px !important;
    }
    .cd-drawer-tabs-list {
        display: flex !important;
        align-items: center !important;
        gap: 5px !important;
        overflow-x: auto !important;
        overflow-y: hidden !important;
        -webkit-overflow-scrolling: touch !important;
        scrollbar-width: thin !important;
        scrollbar-color: #cbd5e1 transparent !important;
        list-style: none !important;
        padding: 0 !important;
        margin: 0 !important;
        flex-wrap: nowrap !important;
        width: 100% !important;
        height: 32px !important;
    }
    .cd-drawer-tabs-list::-webkit-scrollbar {
        height: 3px !important;
    }
    .cd-drawer-tabs-list::-webkit-scrollbar-track {
        background: transparent !important;
    }
    .cd-drawer-tabs-list::-webkit-scrollbar-thumb {
        background: #cbd5e1 !important;
        border-radius: 3px !important;
    }
    .cd-drawer-tabs-list .nav-item {
        margin: 0 !important;
        padding: 0 !important;
        flex-shrink: 0 !important;
        display: flex !important;
        align-items: center !important;
        height: 32px !important;
    }
    .cd-tab-pill-btn {
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 7px !important;
        padding: 0 9.5px !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        color: #475569 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 5px !important;
        height: 32px !important;
        text-decoration: none !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02) !important;
        transition: all 0.15s ease-in-out !important;
        cursor: pointer !important;
        white-space: nowrap !important;
        flex-shrink: 0 !important;
        line-height: 1 !important;
        box-sizing: border-box !important;
    }
    .cd-tab-pill-btn i {
        font-size: 11px !important;
        color: #64748b !important;
        transition: color 0.15s !important;
    }
    .cd-tab-pill-btn:hover {
        background: #f1f5f9 !important;
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
        box-shadow: 0 2px 5px rgba(0, 158, 247, 0.22) !important;
    }
    .cd-tab-pill-btn.active i {
        color: #ffffff !important;
    }
    .cd-tab-badge-pill {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 17px !important;
        height: 16px !important;
        padding: 0 4.5px !important;
        border-radius: 999px !important;
        font-size: 9.5px !important;
        font-weight: 700 !important;
        background: #eff6ff !important;
        color: #2563eb !important;
        line-height: 16px !important;
    }
    .cd-tab-pill-btn.active .cd-tab-badge-pill {
        background: rgba(255, 255, 255, 0.28) !important;
        color: #ffffff !important;
    }
    @media (max-width: 1200px) {
        .cd-tab-pill-btn {
            padding: 0 7.5px !important;
            font-size: 10.5px !important;
            gap: 4px !important;
        }
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

    
          /* ── Client Information: Executive Vertical Listing (God-Level) ── */
          .cd-vertical-info-list {
            display: flex !important;
            flex-direction: column !important;
            width: 100% !important;
          }
          .cd-vinfo-row {
            display: flex !important;
            align-items: flex-start !important;
            justify-content: space-between !important;
            gap: 12px !important;
            padding: 9px 2px !important;
            border-bottom: 1px solid #f1f5f9 !important;
            min-width: 0 !important;
            transition: background-color 0.15s ease !important;
          }
          .cd-vinfo-row:first-child {
            padding-top: 2px !important;
          }
          .cd-vinfo-row:last-child {
            border-bottom: none !important;
            padding-bottom: 2px !important;
          }
          .cd-vinfo-row:hover {
            background-color: #fcfdfe !important;
          }
          .cd-vinfo-key {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            flex: 0 0 132px !important;
            min-width: 110px !important;
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
            font-size: 12.5px !important;
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
          .cd-vinfo-link:hover {
            color: #1d4ed8 !important;
            text-decoration: underline !important;
          }
          .cd-vinfo-link.direct {
            color: #059669 !important;
          }
          .cd-vinfo-link.direct:hover {
            color: #047857 !important;
          }

          .cd-career-pill {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            background: #ecfdf5 !important;
            color: #059669 !important;
            border: 1px solid #a7f3d0 !important;
            padding: 2px 7px !important;
            border-radius: 4px !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            text-decoration: none !important;
            transition: all 0.15s ease !important;
          }
          .cd-career-pill:hover {
            background: #059669 !important;
            color: #ffffff !important;
            border-color: #059669 !important;
          }

          .cd-tax-wrap {
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
          }
          .cd-tax-code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
            font-size: 11.5px !important;
            font-weight: 600 !important;
            background: #f1f5f9 !important;
            color: #0f172a !important;
            padding: 2px 7px !important;
            border-radius: 4px !important;
            border: 1px solid #e2e8f0 !important;
            letter-spacing: 0.3px !important;
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

          /* Enterprise Location Sub-Cards */
          .cd-loc-group {
            display: flex !important;
            flex-direction: column !important;
            gap: 8px !important;
            width: 100% !important;
          }
          .cd-loc-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-left: 3.5px solid #94a3b8 !important;
            border-radius: 7px !important;
            padding: 8px 11px !important;
            text-align: left !important;
            width: 100% !important;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03) !important;
            transition: all 0.2s ease !important;
          }
          .cd-loc-card:hover {
            border-color: #cbd5e1 !important;
            box-shadow: 0 3px 6px rgba(15, 23, 42, 0.05) !important;
          }
          .cd-loc-card.is-primary,
          .cd-loc-card.hq {
            background: #f8fbff !important;
            border-color: #dbeafe !important;
            border-left: 3.5px solid #2563eb !important;
          }
          .cd-loc-card.is-primary:hover,
          .cd-loc-card.hq:hover {
            border-color: #bfdbfe !important;
            box-shadow: 0 3px 8px rgba(37, 99, 235, 0.08) !important;
          }
          .cd-loc-header,
          .cd-loc-top {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 8px !important;
            margin-bottom: 5px !important;
          }
          .cd-loc-header-left {
            display: inline-flex !important;
            align-items: center !important;
            gap: 5px !important;
            flex-shrink: 0 !important;
          }
          .cd-loc-badge,
          .cd-loc-tag {
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
          .cd-loc-badge.loc-primary,
          .cd-loc-tag.hq {
            background: #eff6ff !important;
            color: #1d4ed8 !important;
            border: 1px solid #bfdbfe !important;
          }
          .cd-loc-badge.loc-secondary,
          .cd-loc-tag.branch {
            background: #f1f5f9 !important;
            color: #334155 !important;
            border: 1px solid #cbd5e1 !important;
          }
          .cd-loc-hq-tag {
            display: inline-flex !important;
            align-items: center !important;
            font-size: 8.5px !important;
            font-weight: 800 !important;
            line-height: 1 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.4px !important;
            background: #2563eb !important;
            color: #ffffff !important;
            padding: 2.5px 5px !important;
            border-radius: 3px !important;
            white-space: nowrap !important;
            flex-shrink: 0 !important;
          }
          .cd-loc-country-pill {
            display: inline-flex !important;
            align-items: center !important;
            font-size: 10px !important;
            font-weight: 600 !important;
            line-height: 1 !important;
            color: #475569 !important;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            padding: 2.5px 8px !important;
            border-radius: 12px !important;
            white-space: nowrap !important;
            flex-shrink: 0 !important;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02) !important;
          }
          .cd-loc-city-line {
            font-size: 12px !important;
            line-height: 1.35 !important;
            margin-bottom: 3px !important;
            word-break: break-word !important;
          }
          .cd-loc-city-name,
          .cd-loc-title {
            font-weight: 700 !important;
            color: #0f172a !important;
          }
          .cd-loc-state-name {
            font-weight: 500 !important;
            color: #475569 !important;
          }
          .cd-loc-street-line,
          .cd-loc-street {
            display: flex !important;
            align-items: flex-start !important;
            gap: 5px !important;
            font-size: 11px !important;
            color: #475569 !important;
            line-height: 1.35 !important;
            word-break: break-word !important;
            margin-bottom: 2px !important;
          }
          .cd-loc-meta-line {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            margin-top: 4px !important;
          }
          .cd-loc-meta-chip,
          .cd-loc-postcode {
            display: inline-flex !important;
            align-items: center !important;
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 4px !important;
            padding: 1.5px 6px !important;
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

          .cd-type-badge-pill {
            font-size: 11px !important;
            font-weight: 600 !important;
            padding: 2px 7px !important;
            border-radius: 4px !important;
            background: #f0f9ff !important;
            color: #0284c7 !important;
            border: 1px solid #bae6fd !important;
          }
          .cd-vintage-chip {
            font-size: 10.5px !important;
            color: #64748b !important;
            font-weight: 500 !important;
            margin-left: 4px !important;
          }

          .cd-social-links-row {
            display: inline-flex !important;
            align-items: center !important;
            gap: 5px !important;
            flex-wrap: wrap !important;
            justify-content: flex-end !important;
          }
          .cd-social-btn {
            width: 24px !important;
            height: 24px !important;
            border-radius: 5px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 11px !important;
            color: #ffffff !important;
            text-decoration: none !important;
            transition: opacity 0.15s ease, transform 0.15s ease !important;
          }
          .cd-social-btn:hover {
            opacity: 0.9 !important;
            transform: translateY(-1px) !important;
            color: #ffffff !important;
          }
          .cd-social-btn.linkedin { background: #0a66c2 !important; }
          .cd-social-btn.twitter  { background: #0f172a !important; }
          .cd-social-btn.github   { background: #24292f !important; }
          .cd-social-btn.youtube  { background: #ff0000 !important; }
          .cd-social-btn.facebook { background: #1877f2 !important; }
          .cd-social-btn.instagram{ background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888) !important; }
          .cd-social-btn.custom   { background: #475569 !important; }
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
                <div class="cd-top-actions">
                </div>
                <div class="cd-top-close-wrap">
                    <button type="button" class="cd-hdr-btn-close cd-close-drawer" id="kt_activitiesRequest_close_btn" title="Close Drawer">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- ── HERO MASTER CARD: STREAMLINED MINIMALIST HEADER ── -->
            <div class="cd-hero-card cd-hero-streamlined">
                <div class="cd-hero-main-row">
                    <!-- Left Flank: Company Identity & Website -->
                    <div class="cd-hero-left-flank">
                        <div class="cd-hero-avatar-wrap">
                            <div class="cd-hero-avatar" style="background: linear-gradient(135deg, #064e3b, #059669) !important;">
                                @if (!empty($detailLogo))
                                    <img src="{{ $detailLogo }}" alt="{{ $detailName }}">
                                @else
                                    {{ $detailInitials }}
                                @endif
                            </div>
                            <div class="cd-hero-verified-check" title="Verified Direct Client">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                        <div class="cd-hero-left-meta">
                            <div class="cd-hero-title-wrap">
                                <h2 class="cd-hero-name" title="{{ $detailName }}">{{ $detailName }}</h2>
                                <span class="cd-hero-id-badge" style="color:#059669; background:#ecfdf5; border-color:#a7f3d0;">#CL-{{ @$directClientDetail->client_id }}</span>
                            </div>
                            <div class="cd-hero-web-wrap">
                                @php $webClean = @$directClientDetail->website ?: ''; @endphp
                                @if (!empty($webClean) && $webClean !== '-')
                                    <a href="{{ str_starts_with($webClean, 'http') ? $webClean : 'https://'.$webClean }}" target="_blank" class="cd-hero-web-link" title="Visit Official Website" style="color:#059669;">
                                        <i class="fas fa-globe text-success"></i>
                                        <span>{{ preg_replace('#^https?://(www\.)?#i', '', rtrim($webClean, '/')) }}</span>
                                        <i class="fas fa-arrow-up-right-from-square" style="font-size: 9px;"></i>
                                    </a>
                                @else
                                    <span class="cd-hero-web-empty text-muted">
                                        <i class="fas fa-globe text-muted me-1"></i>No Website Provided
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Right Flank: Scope & Status / Positions -->
                    <div class="cd-hero-right-flank">
                        <div class="cd-hero-scope-row">
                            <span class="cd-pill-scope-direct">
                                <i class="fas fa-handshake me-1"></i> Direct Client Request
                            </span>
                        </div>
                        <div class="cd-hero-status-pos-row">
                            @if ($isClientActive)
                                <span class="cd-status-badge-active" title="Client is actively hiring">
                                    <span class="cd-pulse-emerald"></span> Active
                                </span>
                            @else
                                <span class="cd-status-badge-inactive" title="Reviewing Agreement Terms">
                                    <span class="cd-pulse-dot cd-pulse-grey"></span> Terms Review
                                </span>
                            @endif
                            <span class="cd-status-sep">|</span>
                            <a href="#requisitions_req" class="cd-hero-positions-link" data-bs-toggle="tab" data-toggle="tab" title="View Active Job Positions">
                                <i class="fas fa-bolt text-warning me-1"></i>
                                <strong>{{ $kpiActive }}</strong> Active {{ $kpiActive == 1 ? 'Position' : 'Positions' }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Bottom Row: Tags -->
                <div class="cd-hero-bottom-tags-row">
                    <div class="cd-hero-tags-left">
                        <span class="cd-hero-tags-label"><i class="fas fa-tags me-1"></i>Tags:</span>
                        @if (!empty($tagsArr) && count($tagsArr) > 0)
                            @foreach (array_slice($tagsArr, 0, 5) as $val)
                                <span class="cd-hero-tag-pill" style="background:#ecfdf5; color:#059669; border-color:#a7f3d0;">{{ ucwords($val) }}</span>
                            @endforeach
                            @if (count($tagsArr) > 5)
                                <span class="cd-hero-tag-more" style="color:#059669;">+{{ count($tagsArr) - 5 }}</span>
                            @endif
                        @else
                            <span class="cd-hero-tag-pill" style="background:#ffffff; color:#64748b; border:1px solid #e2e8f0;">Software Development</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- ── Tier 2: Dedicated Pure White Sub-Tabs Card ── -->
            <div class="cd-drawer-tabs-card">
                <div class="cd-drawer-tabs-list" role="tablist">
                    <a class="cd-tab-pill-btn active" data-bs-toggle="tab" data-toggle="tab" href="#overview_req" role="tab">
                        <i class="fas fa-house"></i>
                        <span>Overview</span>
                    </a>
                    @if (!empty($client_contact_info) && count($client_contact_info) > 0)
                    <a class="cd-tab-pill-btn" data-bs-toggle="tab" data-toggle="tab" href="#contacts_req" role="tab">
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

<script>
    $(document).ready(function() {
        $(document).off('click.reqDrawer', '#kt_activitiesDirectClient .cd-tab-pill-btn');
        $(document).on('click.reqDrawer', '#kt_activitiesDirectClient .cd-tab-pill-btn', function(e) {
            var target = $(this).attr('href');
            if (!target) return;
            $('#kt_activitiesDirectClient .cd-tab-pill-btn').removeClass('active');
            $(this).addClass('active');
            $('#kt_activitiesDirectClient .tab-pane').removeClass('active show');
            $(`#kt_activitiesDirectClient ${target}`).addClass('active show');
        });
    });
</script>
