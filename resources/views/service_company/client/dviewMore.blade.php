@php
    use App\Helper\GeneralMethod;
    use Carbon\Carbon;
@endphp
  @php
      
        $startOfMonthcomman = Carbon::now()->startOfMonth()->subMonths(5);
        $endOfMonthcomman =  Carbon::now()->endOfMonth();
        
        $endDate = Carbon::today()->endOfDay();
        $startDate = Carbon::today()->subDays(6)->startOfDay();

  @endphp
@include('service_company.client.components.tab-jobs-style')
@include('service_company.client.components.tab-offers-joinings-style')
@include('service_company.client.components.tab-activity-style')
@include('service_company.client.components.tab-interviews-style')
<style>
    @media only screen and (min-width: 900px) and (max-width: 2560px) {

        .table td,
        .table th,
        .table tr {
            width: max-content !important;
        }

        .table> :not(caption)>*>* {
            padding: 0.75em 0rem !important;
        }

        td:nth-child(n+2),
        th:nth-child(n+2) {
            width: revert !important;
            border-bottom: 1px dashed #f5f8fa !important;
        }
    }

    .table-custom-class tbody tr {
        height: 82px;
        /* Adjust the height as per your requirement */
        position: relative;
    }
    .iti__flag {
        margin-top: 2px !important;
      }

    /* ── Performance Dashboard Scoped Styles (God-Level Enterprise Analytics — Max 14px Scale — Direct Client Emerald Theme) ── */
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
</style>


<div class="card-body position-relative px-0 py-0" id="kt_activities_body">
    
    <!--begin::Content-->
<div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-0" data-kt-scroll="true"
        data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body"
        data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">
        <!--beign::candidate detail-->
        {{-- ── Enterprise Direct Client Drawer (Green Theme, 14px Max Font Scale, Follow-up only action) ── --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <style>
          .cd-scope {
            box-sizing: border-box;
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
            font-size: 12px !important;
            color: #334155;
            background: #f8fafc;
          }
          .cd-scope *, .cd-scope *::before, .cd-scope *::after {
            box-sizing: border-box;
          }
          /* ── Explicit FontAwesome Restoration: Ensure icon glyphs render cleanly ── */
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
            display: inline-block;
          }
          .cd-scope .fa-solid,
          .cd-scope .fas {
            font-weight: 900 !important;
          }
          .cd-scope .fa-regular,
          .cd-scope .far {
            font-weight: 400 !important;
          }

          /* ── Modern Card + Sub-Card Design Standard (Direct Client - Metronic Aligned) ── */

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
          .cd-top-right-actions {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            flex-shrink: 0 !important;
          }
          .cd-hdr-action-btn {
            height: 32px !important;
            padding: 0 13px !important;
            border-radius: 6px !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            cursor: pointer !important;
            text-decoration: none !important;
            transition: all 0.15s ease-in-out !important;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04) !important;
          }
          .cd-hdr-action-btn.followup {
            background: #ffffff !important;
            border: 1.5px solid #0284c7 !important;
            color: #0284c7 !important;
          }
          .cd-hdr-action-btn.followup:hover {
            background: #f0f9ff !important;
            border-color: #0369a1 !important;
            color: #0369a1 !important;
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
          .cd-hero-status-pos-row {
            display: inline-flex !important;
            align-items: center !important;
            gap: 8px !important;
            font-size: 12px !important;
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

          /* 5 METRIC KPI SUB-CARDS GRID (Hover Lift, Active Border Transition) */
          .cd-kpi-grid {
            display: grid !important;
            grid-template-columns: repeat(5, 1fr) !important;
            gap: 10px !important;
            margin-bottom: 12px !important;
          }
          @media (max-width: 992px) {
            .cd-kpi-grid {
              grid-template-columns: repeat(3, 1fr) !important;
            }
          }
          @media (max-width: 576px) {
            .cd-kpi-grid {
              grid-template-columns: repeat(2, 1fr) !important;
            }
          }
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
            cursor: pointer !important;
            user-select: none !important;
          }
          .cd-kpi-card:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06) !important;
            border-color: #009ef7 !important;
          }
          .cd-kpi-card-left {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            min-width: 0 !important;
          }
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
          .cd-kpi-icon-square.purple { background: #f5f3ff !important; color: #7c3aed !important; border: 1px solid #ede9fe !important; }
          .cd-kpi-icon-square.amber  { background: #fffbeb !important; color: #d97706 !important; border: 1px solid #fef3c7 !important; }
          .cd-kpi-icon-square.orange { background: #fff7ed !important; color: #ea580c !important; border: 1px solid #ffedd5 !important; }
          .cd-kpi-icon-square.green  { background: #f0fdf4 !important; color: #16a34a !important; border: 1px solid #dcfce7 !important; }

          .cd-kpi-info {
            display: flex !important;
            flex-direction: column !important;
            min-width: 0 !important;
          }
          .cd-kpi-val {
            font-size: 14.5px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            line-height: 1.1 !important;
          }
          .cd-kpi-label {
            font-size: 11.5px !important;
            font-weight: 500 !important;
            color: #64748b !important;
            line-height: 1.2 !important;
            margin-top: 2px !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
          }
          .cd-kpi-arrow-subcard {
            width: 22px !important;
            height: 22px !important;
            border-radius: 5px !important;
            background: #f8fafc !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 9px !important;
            color: #94a3b8 !important;
            flex-shrink: 0 !important;
            transition: all 0.15s !important;
          }
          .cd-kpi-card:hover .cd-kpi-arrow-subcard {
            background: #009ef7 !important;
            color: #ffffff !important;
          }

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

          /* TIER 3: Overview Discrete Sub-Cards Stream */
          .cd-overview-subcards-grid {
            display: grid !important;
            grid-template-columns: 1.35fr 1fr !important;
            gap: 14px !important;
            align-items: start !important;
          }
          @media (max-width: 1024px) {
            .cd-overview-subcards-grid {
              grid-template-columns: 1fr !important;
            }
          }
          .cd-overview-col-left, .cd-overview-col-right {
            display: flex !important;
            flex-direction: column !important;
            gap: 12px !important;
          }
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
          .cd-subcard-header-left {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
          }
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
          .cd-subcard-header-icon.amber  { background: #fffbeb !important; color: #d97706 !important; }
          .cd-subcard-header-icon.indigo { background: #eef2ff !important; color: #4f46e5 !important; }
          .cd-subcard-header-icon.teal   { background: #f0fdf4 !important; color: #059669 !important; }
          .cd-subcard-header-icon.pink   { background: #fdf2f8 !important; color: #db2777 !important; }

          .cd-subcard-title {
            font-size: 13px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            margin: 0 !important;
          }
          .cd-subcard-action-link {
            font-size: 11.5px !important;
            font-weight: 600 !important;
            color: #059669 !important;
            text-decoration: none !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 3px !important;
          }
          .cd-subcard-action-link:hover {
            text-decoration: underline !important;
          }
          .cd-about-text-content {
            font-size: 12px !important;
            line-height: 1.65 !important;
            color: #475569 !important;
          }

          /* Recent Activity Timeline Stream */
          .cd-timeline-subcards-stream {
            display: flex !important;
            flex-direction: column !important;
            gap: 10px !important;
          }
          .cd-timeline-item-subcard {
            display: flex !important;
            align-items: flex-start !important;
            gap: 10px !important;
            background: #f8fafc !important;
            border: 1px solid #eef2f6 !important;
            border-radius: 8px !important;
            padding: 9px 12px !important;
            transition: all 0.15s !important;
          }
          .cd-timeline-item-subcard:hover {
            border-color: #cbd5e1 !important;
            background: #ffffff !important;
          }
          .cd-timeline-dot {
            width: 26px !important;
            height: 26px !important;
            border-radius: 6px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 11px !important;
            flex-shrink: 0 !important;
            margin-top: 2px !important;
          }
          .cd-timeline-dot.green  { background: #dcfce7 !important; color: #16a34a !important; }
          .cd-timeline-dot.purple { background: #f3e8ff !important; color: #7c3aed !important; }
          .cd-timeline-dot.orange { background: #ffedd5 !important; color: #ea580c !important; }
          .cd-timeline-dot.blue   { background: #e0f2fe !important; color: #0284c7 !important; }

          .cd-timeline-body {
            flex: 1 !important;
            min-width: 0 !important;
          }
          .cd-timeline-title {
            font-size: 12px !important;
            font-weight: 600 !important;
            color: #0f172a !important;
            line-height: 1.25 !important;
          }
          .cd-timeline-subtext {
            font-size: 11px !important;
            color: #64748b !important;
            margin-top: 1px !important;
          }
          .cd-timeline-meta-wrap {
            display: flex !important;
            flex-direction: column !important;
            align-items: flex-end !important;
            gap: 3px !important;
            flex-shrink: 0 !important;
          }
          .cd-timeline-badge {
            font-size: 10px !important;
            font-weight: 600 !important;
            padding: 1.5px 6px !important;
            border-radius: 4px !important;
            white-space: nowrap !important;
          }
          .cd-timeline-badge.green  { background: #dcfce7 !important; color: #15803d !important; }
          .cd-timeline-badge.amber  { background: #fef3c7 !important; color: #b45309 !important; }
          .cd-timeline-badge.orange { background: #ffedd5 !important; color: #c2410c !important; }
          .cd-timeline-badge.blue   { background: #e0f2fe !important; color: #0284c7 !important; }
          .cd-timeline-timestamp {
            font-size: 10px !important;
            color: #94a3b8 !important;
            white-space: nowrap !important;
          }

          
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
          /* Client Information Discrete 2-Column Mini Cards */
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
          .cd-mini-label {
            font-size: 10px !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.3px !important;
            color: #94a3b8 !important;
          }
          .cd-mini-value {
            font-size: 12px !important;
            font-weight: 600 !important;
            color: #0f172a !important;
            word-break: break-word !important;
          }
          .cd-mini-value a {
            color: #059669 !important;
            text-decoration: none !important;
          }
          .cd-mini-value a:hover {
            text-decoration: underline !important;
          }

          /* Primary SPOC Master Card */
          .cd-contact-master-subcard {
            background: #f8fafc !important;
            border: 1px solid #eef2f6 !important;
            border-radius: 8px !important;
            padding: 10px 12px !important;
          }
          .cd-contact-master-top {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            margin-bottom: 8px !important;
          }
          .cd-contact-avatar-box {
            width: 38px !important;
            height: 38px !important;
            border-radius: 8px !important;
            background: #dcfce7 !important;
            border: 1px solid #bbf7d0 !important;
            color: #15803d !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 13px !important;
            font-weight: 700 !important;
            flex-shrink: 0 !important;
          }
          .cd-contact-master-info {
            flex: 1 !important;
            min-width: 0 !important;
          }
          .cd-contact-master-name-row {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
          }
          .cd-contact-master-name {
            font-size: 12.5px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
          }
          .cd-contact-primary-tag {
            font-size: 9.5px !important;
            font-weight: 600 !important;
            background: #ecfdf5 !important;
            color: #059669 !important;
            border: 1px solid #a7f3d0 !important;
            padding: 1px 5px !important;
            border-radius: 4px !important;
          }
          .cd-contact-master-role {
            font-size: 11px !important;
            color: #64748b !important;
          }
          .cd-contact-channels-grid {
            display: grid !important;
            grid-template-columns: 1fr !important;
            gap: 5px !important;
            margin-top: 6px !important;
            padding-top: 6px !important;
            border-top: 1px solid #eef2f6 !important;
          }
          .cd-contact-channel-item {
            display: flex !important;
            align-items: center !important;
            gap: 7px !important;
            font-size: 11.5px !important;
            color: #334155 !important;
            text-decoration: none !important;
            padding: 3px 6px !important;
            border-radius: 5px !important;
            transition: background 0.15s !important;
          }
          .cd-contact-channel-item:hover {
            background: #ffffff !important;
            color: #059669 !important;
          }
          .cd-contact-channel-icon {
            width: 20px !important;
            height: 20px !important;
            border-radius: 4px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 10px !important;
            flex-shrink: 0 !important;
          }
          .cd-contact-channel-icon.email { background: #eff6ff !important; color: #2563eb !important; }
          .cd-contact-channel-icon.phone { background: #f0fdf4 !important; color: #16a34a !important; }

          /* Client Services Tag Cloud */
          .cd-tag-cloud-subcard {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 5px !important;
          }
          .cd-service-tag-pill {
            display: inline-flex !important;
            align-items: center !important;
            font-size: 11px !important;
            font-weight: 500 !important;
            color: #15803d !important;
            background: #dcfce7 !important;
            border: 1px solid #bbf7d0 !important;
            padding: 2.5px 8px !important;
            border-radius: 12px !important;
          }

          /* TAB 2: RECENT ACTIVITY - 3-Tier Activity & Audit Log Standard */
          .cd-activity-header-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 14px 18px !important;
            margin-bottom: 12px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
          }
          .cd-activity-header-left {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
          }
          .cd-activity-icon-box {
            width: 38px !important;
            height: 38px !important;
            border-radius: 8px !important;
            background: #fffbeb !important;
            border: 1px solid #fef3c7 !important;
            color: #d97706 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 15px !important;
            flex-shrink: 0 !important;
          }
          .cd-activity-main-title {
            font-size: 14px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            margin: 0 !important;
          }
          .cd-activity-sub-title {
            font-size: 11.5px !important;
            color: #64748b !important;
            margin: 2px 0 0 0 !important;
          }
          .cd-activity-status-chip {
            font-size: 11px !important;
            font-weight: 600 !important;
            color: #15803d !important;
            background: #f0fdf4 !important;
            border: 1px solid #bbf7d0 !important;
            padding: 3px 8px !important;
            border-radius: 6px !important;
          }
          .cd-activity-filter-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 8px 12px !important;
            margin-bottom: 12px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
          }
          .cd-activity-filter-list {
            display: flex !important;
            gap: 6px !important;
            flex-wrap: wrap !important;
          }
          .cd-act-filter-btn {
            background: transparent !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 6px !important;
            padding: 5px 11px !important;
            font-size: 11.5px !important;
            font-weight: 500 !important;
            color: #475569 !important;
            cursor: pointer !important;
            transition: all 0.15s !important;
          }
          .cd-act-filter-btn:hover {
            background: #f8fafc !important;
            border-color: #cbd5e1 !important;
          }
          .cd-act-filter-btn.active {
            background: #009ef7 !important;
            border-color: #009ef7 !important;
            color: #ffffff !important;
            font-weight: 600 !important;
          }
          .cd-timeline-stream-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 16px 20px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 14px !important;
          }
          .cd-stream-node {
            display: flex !important;
            align-items: flex-start !important;
            gap: 12px !important;
            padding-bottom: 12px !important;
            border-bottom: 1px dashed #f1f5f9 !important;
          }
          .cd-stream-node:last-child {
            border-bottom: none !important;
            padding-bottom: 0 !important;
          }
          .cd-stream-node-dot {
            width: 32px !important;
            height: 32px !important;
            border-radius: 8px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 12px !important;
            flex-shrink: 0 !important;
          }
          .cd-stream-node-dot.green  { background: #dcfce7 !important; color: #16a34a !important; }
          .cd-stream-node-dot.purple { background: #f3e8ff !important; color: #7c3aed !important; }
          .cd-stream-node-dot.blue   { background: #e0f2fe !important; color: #0284c7 !important; }
          .cd-stream-node-content {
            flex: 1 !important;
            min-width: 0 !important;
          }
          .cd-stream-node-header {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 8px !important;
          }
          .cd-stream-node-title {
            font-size: 12.5px !important;
            font-weight: 600 !important;
            color: #0f172a !important;
          }
          .cd-stream-node-time {
            font-size: 11px !important;
            color: #94a3b8 !important;
          }
          .cd-stream-node-desc {
            font-size: 11.5px !important;
            color: #64748b !important;
            margin: 3px 0 6px 0 !important;
            line-height: 1.4 !important;
          }
          .cd-stream-node-tags {
            display: flex !important;
            gap: 6px !important;
            flex-wrap: wrap !important;
          }
          .cd-stream-tag {
            font-size: 10px !important;
            font-weight: 600 !important;
            padding: 1px 6px !important;
            border-radius: 4px !important;
          }
          .cd-stream-tag.green { background: #dcfce7 !important; color: #15803d !important; }
          .cd-stream-tag.blue  { background: #e0f2fe !important; color: #0284c7 !important; }
          .cd-stream-tag.purple{ background: #f3e8ff !important; color: #6d28d9 !important; }
          .cd-stream-tag.amber { background: #fef3c7 !important; color: #b45309 !important; }
          /* ── Drawer Footer ── */
          .cd-drawer-footer {
            padding: 10px 18px;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            font-size: 10.5px !important;
            font-weight: 400 !important;
            color: #94a3b8;
          }

          /* ── Retained Styles for Sub-tab Jobs and Contacts ── */
          .jobx, .jobx * { font-family:'Poppins',sans-serif; box-sizing:border-box; }
          .jobx .table-card { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.05); border:1px solid #e4e6ef; overflow:hidden; }
          .jobx .table-header, .jobx .job-row { display:grid; grid-template-columns:2.5fr 1fr 1.1fr 1.2fr .9fr .9fr 2.6fr; align-items:start; column-gap:12px; }
          .jobx .table-header { padding:9px 12px; background:#f7f8fc; border-bottom:1.5px solid #e4e6ef; align-items:center; }
          .jobx .th { font-size:11px; font-weight:600; color:#6d7284; text-transform:uppercase; letter-spacing:.4px; display:flex; align-items:center; gap:4px; }
          .jobx .job-row { padding:12px; border-bottom:1px solid #e4e6ef; transition:background .12s; }
          .jobx .job-row:last-of-type { border-bottom:none; }
          .jobx .job-row:hover { background:#fafbff; }
          .jobx .col-job { display:flex; gap:9px; align-items:flex-start; }
          .jobx .drag-col { display:flex; align-items:center; padding-top:14px; flex-shrink:0; color:#a1a5b7; cursor:grab; }
          .jobx .job-logo { width:46px; height:46px; border-radius:10px; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:700; color:#fff; overflow:hidden; }
          .jobx .job-logo img { width:100%; height:100%; object-fit:cover; }
          .jobx .job-info { flex:1; min-width:0; }
          .jobx .job-title a { font-size:13px; font-weight:600; color:#181c32; text-decoration:none; display:block; line-height:1.35; }
          .jobx .job-title a:hover { color:#10b981; }
          .jobx .job-dept { font-size:11.5px; color:#6d7284; margin-top:1px; }
          .jobx .job-client { font-size:11.5px; color:#6d7284; font-weight:500; margin-top:1px; margin-bottom:5px; }
          .jobx .job-meta-line { display:flex; align-items:center; flex-wrap:wrap; gap:4px; font-size:11px; }
          .jobx .it-job-id { font-size:10.5px; color:#6a6b70; font-weight:500; }
          .jobx .it-dot-separator { width:3px; height:3px; border-radius:50%; background:#a1a5b7; }
          .jobx .badge-type { display:inline-flex; align-items:center; gap:3px; padding:2px 7px; border-radius:20px; font-size:10px; font-weight:600; }
          .jobx .badge-internal { background:#eef2ff; color:#3b55e6; }
          .jobx .badge-client   { background:#eefff6; color:#09b760; }
          .jobx .badge-direct   { background:#ecfdf5; color:#15803d; }
          .jobx .skills-inline .badge { font-size:10px; padding:1px 5px; margin:0 1px 0 0; border-radius:4px; }
          .jobx .col-exp { display:flex; flex-direction:column; gap:5px; padding-top:2px; }
          .jobx .exp-row, .jobx .sal-row { display:flex; align-items:center; gap:5px; font-size:12px; font-weight:500; color:#181c32; white-space:nowrap; }
          .jobx .exp-row svg, .jobx .sal-row svg { width:13px; height:13px; color:#a1a5b7; flex-shrink:0; }
          .jobx .col-hl { display:flex; flex-direction:column; gap:4px; padding-top:2px; }
          .jobx .badge-hiring { display:inline-flex; align-items:center; gap:5px; padding:3px 9px; border-radius:20px; font-size:10.5px; font-weight:600; width:fit-content; cursor:default; }
          .jobx .badge-hiring::before { content:""; width:6px; height:6px; border-radius:50%; }
          .jobx .badge-hiring.active-bg { background:#eafff3; color:#17c653; border:1px solid #b8f0d4; }
          .jobx .badge-hiring.active-bg::before { background:#17c653; }
          .jobx .badge-hiring.hold-bg   { background:#fff8e1; color:#ffa800; border:1px solid #fde4a0; }
          .jobx .badge-hiring.hold-bg::before   { background:#ffa800; }
          .jobx .badge-hiring.closed-bg { background:#ffeef1; color:#f1416c; border:1px solid #ffd5d5; }
          .jobx .badge-hiring.closed-bg::before { background:#f1416c; }
          .jobx .loc-row, .jobx .mode-row { display:flex; align-items:center; gap:4px; font-size:11.5px; color:#6d7284; font-weight:500; }
          .jobx .loc-row svg, .jobx .mode-row svg { width:12px; height:12px; color:#a1a5b7; flex-shrink:0; }
          .jobx .col-hm { font-size:12px; padding-top:2px; }
          .jobx .hm-name  { font-size:12.5px; font-weight:600; color:#181c32; margin-bottom:2px; }
          .jobx .hm-title { font-size:11px; color:#6d7284; margin-bottom:1px; }
          .jobx .hm-company { font-size:11px; color:#a1a5b7; }
          .jobx .col-pipe { padding-top:2px; }
          .jobx .pipeline-stages { display:flex; flex-wrap:wrap; gap:3px; margin-bottom:4px; }
          .jobx .stage-badge { display:inline-flex; align-items:center; gap:3px; padding:2px 6px; border-radius:4px; font-size:10px; font-weight:600; }
          .jobx .stage-badge a { font-size:9.5px; font-weight:700; color:inherit; text-decoration:none; }
          .jobx .stage-badge a:hover { opacity:.75; }
          .jobx .s-inw { background:#ffffde; color:#bdbd26; } .jobx .s-inw a { color:#bdbd26; }
          .jobx .s-sou { background:#efefef; color:#000; }   .jobx .s-sou a { color:#000; }
          .jobx .s-scr { background:#f7deff; color:#c000ff; }.jobx .s-scr a { color:#c000ff; }
          .jobx .s-eva { background:#f0f8ff; color:#2896ff; }.jobx .s-eva a { color:#2896ff; }
          .jobx .s-neg { background:#ffe6f2; color:#ff4098; }.jobx .s-neg a { color:#ff4098; }
          .jobx .s-off { background:#fff9ed; color:#ffab00; }.jobx .s-off a { color:#ffab00; }
          .jobx .s-joi { background:#d4ffde; color:#06c732; }.jobx .s-joi a { color:#06c732; }
          .jobx .s-rej { background:#ffe2e5; color:#ff0202; }.jobx .s-rej a { color:#ff0202; }
          .jobx .pipeline-total { font-size:10.5px; font-weight:600; color:#6d7284; display:flex; flex-wrap:wrap; gap:4px; }
          .jobx .pipeline-total strong { color:#181c32; }
          .jobx .add-date { font-size:10.5px; color:#a1a5b7; display:flex; align-items:center; gap:4px; }
          .jobx .job-empty { padding:34px 12px; text-align:center; color:#a1a5b7; font-size:12px; }
          .jobx .job-row-skills { display:flex; align-items:center; flex-wrap:wrap; gap:5px; padding:3px 12px 10px 83px; border-bottom:1px solid #e4e6ef; font-size:11px; }
          .jobx .job-row-skills .sk-label { font-weight:600; color:#6d7284; flex-shrink:0; }
          .jobx .job-row-skills .sk-tag { display:inline-flex; padding:1px 7px; border-radius:4px; background:#f1f3fa; color:#5e6278; font-size:10px; font-weight:600; }
          .jobx .job-row:last-of-type + .job-row-skills { border-bottom:none; }
          @media (max-width:1366px){ .jobx .table-header, .jobx .job-row { grid-template-columns:2.2fr .95fr 1fr 1.1fr .85fr .85fr 2.1fr; } }
          @media (max-width:1024px){ .jobx .table-card { overflow-x:auto; } .jobx .table-header, .jobx .job-row { min-width:980px; } }

          /* ── Contacts Tab Modern Card + Sub-Card Enterprise Design ── */
          .cd-contact-tab-container {
            background: #ffffff;
            border-radius: 12px;
            padding: 12px 14px 18px;
            margin: 8px 10px 16px;
            border: 1px solid #edf2f7;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.02);
            overflow: hidden;
          }

          /* TIER 1: Top Header Toolbar Card (Single Row) */
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
            background: #ecfdf5;
            color: #059669;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
            border: 1px solid #a7f3d0;
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
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
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

          /* Search Sub-Card (Positioned in Top Header Toolbar) */
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
            font-family: inherit !important;
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
            transition: color 0.15s !important;
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

          /* TIER 2: Sub-Tabs Filter Card */
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
            -ms-overflow-style: none;
            padding: 2px 0;
          }
          .cd-contact-tabs-list::-webkit-scrollbar {
            display: none;
          }
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
            user-select: none;
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

          /* TIER 3: Contact Cards Stream */
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

          /* Individual Contact Master Card */
          .cd-contact-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
            transition: all 0.15s ease-in-out !important;
            overflow: hidden !important;
            width: 100% !important;
            max-width: 100% !important;
          }
          .cd-contact-card:hover {
            border-color: #cbd5e1 !important;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06) !important;
            transform: translateY(-1px) !important;
          }

          /* Discrete Sub-Cards Grid (3 Columns calibrated for 15.6" and smaller screens) */
          .cd-contact-card-grid {
            display: grid !important;
            grid-template-columns: minmax(0, 1.25fr) minmax(0, 1.2fr) minmax(0, 1.05fr) !important;
            gap: 10px !important;
            padding: 10px 12px !important;
            width: 100% !important;
            max-width: 100% !important;
            align-items: stretch !important;
            box-sizing: border-box !important;
          }

          /* Discrete Sub-Card Container */
          .cd-contact-subcard {
            background: #f8fafc !important;
            border: 1px solid #edf2f7 !important;
            border-radius: 8px !important;
            padding: 8px 10px !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: space-between !important;
            min-width: 0 !important;
            overflow: hidden !important;
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
            display: inline-flex !important;
            align-items: center !important;
          }
          .cd-contact-subcard-body {
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            gap: 4px !important;
            flex: 1 1 auto !important;
            min-width: 0 !important;
            overflow: hidden !important;
          }

          /* Sub-Card 1: Profile Elements */
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
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 14px !important;
            font-weight: 700 !important;
            color: #ffffff !important;
            flex-shrink: 0 !important;
            text-transform: uppercase !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08) !important;
          }
          .cd-contact-profile-meta {
            flex: 1 1 auto !important;
            min-width: 0 !important;
            overflow: hidden !important;
          }
          .cd-contact-card-name {
            font-size: 13.5px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            line-height: 1.25 !important;
            display: block !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
          }
          .cd-contact-card-desig {
            font-size: 11.5px !important;
            font-weight: 500 !important;
            color: #475569 !important;
            line-height: 1.2 !important;
            margin-top: 1px !important;
            display: block !important;
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
            white-space: nowrap !important;
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
            white-space: nowrap !important;
          }
          .cd-chip-client {
            font-size: 10px !important;
            font-weight: 600 !important;
            padding: 1px 6px !important;
            border-radius: 4px !important;
            background: #ecfdf5 !important;
            color: #047857 !important;
            border: 1px solid #a7f3d0 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            max-width: 140px !important;
            display: inline-flex !important;
            align-items: center !important;
          }
          .cd-chip-id {
            font-size: 10px !important;
            font-weight: 600 !important;
            padding: 1px 5px !important;
            border-radius: 4px !important;
            background: #f8fafc !important;
            color: #64748b !important;
            border: 1px solid #e2e8f0 !important;
            white-space: nowrap !important;
          }
          .cd-chip-auth {
            font-size: 10px !important;
            font-weight: 600 !important;
            padding: 1px 6px !important;
            border-radius: 4px !important;
            background: #ecfdf5 !important;
            color: #047857 !important;
            border: 1px solid #a7f3d0 !important;
            white-space: nowrap !important;
            display: inline-flex !important;
            align-items: center !important;
          }

          /* Sub-Card 2: Communication Elements */
          .cd-comm-item {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 6px !important;
            font-size: 11.5px !important;
            color: #334155 !important;
            min-width: 0 !important;
            line-height: 1.35 !important;
            background: #ffffff !important;
            border: 1px solid #edf2f7 !important;
            border-radius: 6px !important;
            padding: 3px 8px !important;
          }
          .cd-comm-content {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            min-width: 0 !important;
            flex: 1 1 auto !important;
            overflow: hidden !important;
          }
          .cd-comm-content i {
            font-size: 11px !important;
            width: 14px !important;
            text-align: center !important;
            flex-shrink: 0 !important;
          }
          .cd-comm-link {
            color: #0f172a !important;
            text-decoration: none !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
            font-weight: 500 !important;
            font-size: 11.5px !important;
            transition: color 0.12s ease !important;
          }
          .cd-comm-link:hover {
            color: #009ef7 !important;
            text-decoration: underline !important;
          }
          .cd-copy-btn {
            background: transparent !important;
            border: none !important;
            color: #94a3b8 !important;
            cursor: pointer !important;
            padding: 2px 4px !important;
            font-size: 11px !important;
            border-radius: 4px !important;
            transition: all 0.12s ease !important;
            flex-shrink: 0 !important;
            display: inline-flex !important;
            align-items: center !important;
          }
          .cd-copy-btn:hover {
            color: #009ef7 !important;
            background: #eff6ff !important;
          }
          .cd-copy-all-btn {
            background: transparent !important;
            border: 1px solid #e2e8f0 !important;
            color: #64748b !important;
            cursor: pointer !important;
            padding: 1px 6px !important;
            font-size: 10px !important;
            font-weight: 600 !important;
            border-radius: 4px !important;
            transition: all 0.12s ease !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 3px !important;
          }
          .cd-copy-all-btn:hover {
            color: #009ef7 !important;
            border-color: #bfdbfe !important;
            background: #eff6ff !important;
          }

          /* Sub-Card 3: Requisitions & Actions */
          .cd-linked-jobs-stat {
            font-size: 11.5px !important;
            font-weight: 600 !important;
            color: #0f172a !important;
            display: flex !important;
            align-items: center !important;
            gap: 5px !important;
            white-space: nowrap !important;
          }
          .cd-view-jobs-link {
            font-size: 11px !important;
            color: #009ef7 !important;
            font-weight: 600 !important;
            text-decoration: none !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 3px !important;
            cursor: pointer !important;
            white-space: nowrap !important;
          }
          .cd-view-jobs-link:hover {
            text-decoration: underline !important;
            color: #007bb5 !important;
          }
          .cd-contact-actions-row {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            flex-wrap: nowrap !important;
          }
          .cd-contact-btn-call {
            padding: 4px 9px !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            border-radius: 6px !important;
            background: #ecfdf5 !important;
            color: #059669 !important;
            border: 1px solid #a7f3d0 !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            text-decoration: none !important;
            transition: all 0.12s ease !important;
            white-space: nowrap !important;
          }
          .cd-contact-btn-call:hover {
            background: #059669 !important;
            color: #ffffff !important;
          }
          .cd-contact-btn-mail {
            padding: 4px 9px !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            border-radius: 6px !important;
            background: #eff6ff !important;
            color: #009ef7 !important;
            border: 1px solid #bae6fd !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            text-decoration: none !important;
            transition: all 0.12s ease !important;
            white-space: nowrap !important;
          }
          .cd-contact-btn-mail:hover {
            background: #009ef7 !important;
            color: #ffffff !important;
          }
          .cd-contact-btn-wa {
            padding: 4px 9px !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            border-radius: 6px !important;
            background: #f0fdf4 !important;
            color: #16a34a !important;
            border: 1px solid #bbf7d0 !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            text-decoration: none !important;
            transition: all 0.12s ease !important;
            white-space: nowrap !important;
          }
          .cd-contact-btn-wa:hover {
            background: #16a34a !important;
            color: #ffffff !important;
          }

          /* Status Badges */
          .cd-contact-status-active {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            padding: 2px 7px !important;
            border-radius: 5px !important;
            font-size: 9.5px !important;
            font-weight: 700 !important;
            background: #dcfce7 !important;
            color: #15803d !important;
            border: 1px solid #bbf7d0 !important;
            white-space: nowrap !important;
          }
          .cd-contact-status-active i {
            font-size: 6px !important;
          }
          .cd-contact-status-inactive {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            padding: 2px 7px !important;
            border-radius: 5px !important;
            font-size: 9.5px !important;
            font-weight: 700 !important;
            background: #f1f5f9 !important;
            color: #64748b !important;
            border: 1px solid #e2e8f0 !important;
            white-space: nowrap !important;
          }

          /* Sub-Card 4: Bottom Activity Strip */
          .cd-contact-card-bottom {
            background: #fbfcfe !important;
            border-top: 1px solid #f1f5f9 !important;
            padding: 6px 12px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 10px !important;
            flex-wrap: wrap !important;
          }
          .cd-contact-bottom-left {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            font-size: 11px !important;
            color: #64748b !important;
            flex-wrap: wrap !important;
          }
          .cd-contact-meta-item {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
          }
          .cd-contact-bottom-right {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            flex-wrap: wrap !important;
          }
          .cd-contact-tag-pill {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            padding: 1px 7px !important;
            border-radius: 4px !important;
            font-size: 10px !important;
            font-weight: 500 !important;
            background: #f1f5f9 !important;
            color: #475569 !important;
            border: 1px solid #e2e8f0 !important;
          }

          /* Skeleton Loader */
          .cd-contact-skeleton-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 10px;
            display: flex;
            gap: 10px;
          }
          .cd-contact-skeleton-pulse {
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
            background-size: 200% 100%;
            animation: cdContactPulse 1.4s ease infinite;
            border-radius: 6px;
          }
          @keyframes cdContactPulse {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
          }

          /* Empty State */
          .cd-contact-empty-state {
            background: #ffffff;
            border: 1px dashed #cbd5e1;
            border-radius: 10px;
            padding: 36px 20px;
            text-align: center;
            margin: 10px 0;
          }
          .cd-contact-empty-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #f1f5f9;
            color: #94a3b8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 10px;
          }
          .cd-contact-empty-title {
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 4px;
          }
          .cd-contact-empty-sub {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 12px;
          }

          /* Responsive Breakpoints Calibrated for 15.6" and Smaller Screens */
          @media (max-width: 1180px) {
            .cd-contact-card-grid {
              grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) !important;
              gap: 8px !important;
            }
            .cd-contact-subcard-actions {
              grid-column: span 2 !important;
            }
          }
          @media (max-width: 768px) {
            .cd-contact-card-grid {
              grid-template-columns: 1fr !important;
              gap: 8px !important;
            }
            .cd-contact-subcard-actions {
              grid-column: auto !important;
            }
            .cd-contact-toolbar-card {
              flex-direction: column !important;
              align-items: flex-start !important;
              gap: 10px !important;
            }
            .cd-contact-toolbar-right {
              width: 100% !important;
              justify-content: space-between !important;
            }
          }

          /* ── Candidates Tab God-Level Card & Sub-Card Redesign ── */
          .cd-cand-tab-container {
            background: #ffffff;
            border-radius: 12px;
            padding: 12px 14px 18px;
            margin: 8px 10px 16px;
            border: 1px solid #edf2f7;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.02);
            overflow: hidden;
          }

          /* TIER 1: Searchbar & Filter Toolbar Card */
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
            flex-wrap: nowrap !important;
            flex-shrink: 0 !important;
          }

          /* Search Sub-Card */
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
            max-width: 320px !important;
            min-width: 180px !important;
            flex-shrink: 1 !important;
            transition: all 0.15s ease-in-out !important;
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
            flex-shrink: 0;
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
            font-family: inherit;
          }
          .cd-cand-search-subcard input::placeholder {
            color: #94a3b8;
            font-weight: 400;
          }
          .cd-cand-search-clear {
            border: none;
            background: transparent;
            color: #94a3b8;
            cursor: pointer;
            padding: 2px 4px;
            font-size: 11px;
            transition: color 0.15s;
          }
          .cd-cand-search-clear:hover {
            color: #ef4444;
          }

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

          /* TIER 2: Sub-Tabs Pipeline Card */
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
            -ms-overflow-style: none;
            padding: 2px 0;
          }
          .cd-cand-tabs-list::-webkit-scrollbar {
            display: none;
          }
          .cd-cand-subtab,
          .cd-cand-subtab-direct {
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
            user-select: none;
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
            transition: all 0.15s ease;
          }

          /* ── Client Tracker Hiring Pipeline Color Combinations for Sub-Tabs ── */
          /* 1. Sourcing (Cyan / Teal #0891b2) */
          .cd-cand-subtab[data-stage="sourcing"],
          .cd-cand-subtab-direct[data-stage="sourcing"] {
            background: #f0fdfa !important;
            border-color: #a5f3fc !important;
            color: #0891b2 !important;
          }
          .cd-cand-subtab[data-stage="sourcing"] .cd-cand-subtab-badge,
          .cd-cand-subtab-direct[data-stage="sourcing"] .cd-cand-subtab-badge {
            background: #ecfeff !important;
            color: #0891b2 !important;
            border-color: #a5f3fc !important;
          }
          .cd-cand-subtab[data-stage="sourcing"]:hover,
          .cd-cand-subtab-direct[data-stage="sourcing"]:hover {
            background: #ecfeff !important;
            border-color: #0891b2 !important;
            color: #0e7490 !important;
          }
          .cd-cand-subtab[data-stage="sourcing"].active,
          .cd-cand-subtab-direct[data-stage="sourcing"].active {
            background: #0891b2 !important;
            border-color: #0891b2 !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(8, 145, 178, 0.35) !important;
          }
          .cd-cand-subtab[data-stage="sourcing"].active .cd-cand-subtab-badge,
          .cd-cand-subtab-direct[data-stage="sourcing"].active .cd-cand-subtab-badge {
            background: rgba(255, 255, 255, 0.28) !important;
            color: #ffffff !important;
            border-color: transparent !important;
          }

          /* 2. Screening (Purple / Violet #9333ea) */
          .cd-cand-subtab[data-stage="screening"],
          .cd-cand-subtab-direct[data-stage="screening"] {
            background: #faf5ff !important;
            border-color: #e9d5ff !important;
            color: #9333ea !important;
          }
          .cd-cand-subtab[data-stage="screening"] .cd-cand-subtab-badge,
          .cd-cand-subtab-direct[data-stage="screening"] .cd-cand-subtab-badge {
            background: #faf5ff !important;
            color: #9333ea !important;
            border-color: #e9d5ff !important;
          }
          .cd-cand-subtab[data-stage="screening"]:hover,
          .cd-cand-subtab-direct[data-stage="screening"]:hover {
            background: #f3e8ff !important;
            border-color: #9333ea !important;
            color: #7e22ce !important;
          }
          .cd-cand-subtab[data-stage="screening"].active,
          .cd-cand-subtab-direct[data-stage="screening"].active {
            background: #9333ea !important;
            border-color: #9333ea !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(147, 51, 234, 0.35) !important;
          }
          .cd-cand-subtab[data-stage="screening"].active .cd-cand-subtab-badge,
          .cd-cand-subtab-direct[data-stage="screening"].active .cd-cand-subtab-badge {
            background: rgba(255, 255, 255, 0.28) !important;
            color: #ffffff !important;
            border-color: transparent !important;
          }

          /* 3. Evaluation (Indigo #4f46e5) */
          .cd-cand-subtab[data-stage="evaluation"],
          .cd-cand-subtab-direct[data-stage="evaluation"] {
            background: #eef2ff !important;
            border-color: #c7d2fe !important;
            color: #4f46e5 !important;
          }
          .cd-cand-subtab[data-stage="evaluation"] .cd-cand-subtab-badge,
          .cd-cand-subtab-direct[data-stage="evaluation"] .cd-cand-subtab-badge {
            background: #eef2ff !important;
            color: #4f46e5 !important;
            border-color: #c7d2fe !important;
          }
          .cd-cand-subtab[data-stage="evaluation"]:hover,
          .cd-cand-subtab-direct[data-stage="evaluation"]:hover {
            background: #e0e7ff !important;
            border-color: #4f46e5 !important;
            color: #4338ca !important;
          }
          .cd-cand-subtab[data-stage="evaluation"].active,
          .cd-cand-subtab-direct[data-stage="evaluation"].active {
            background: #4f46e5 !important;
            border-color: #4f46e5 !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(79, 70, 229, 0.35) !important;
          }
          .cd-cand-subtab[data-stage="evaluation"].active .cd-cand-subtab-badge,
          .cd-cand-subtab-direct[data-stage="evaluation"].active .cd-cand-subtab-badge {
            background: rgba(255, 255, 255, 0.28) !important;
            color: #ffffff !important;
            border-color: transparent !important;
          }

          /* 4. Negotiations (Rose / Magenta #db2777) */
          .cd-cand-subtab[data-stage="negotiations"],
          .cd-cand-subtab-direct[data-stage="negotiations"] {
            background: #fdf2f8 !important;
            border-color: #fbcfe8 !important;
            color: #db2777 !important;
          }
          .cd-cand-subtab[data-stage="negotiations"] .cd-cand-subtab-badge,
          .cd-cand-subtab-direct[data-stage="negotiations"] .cd-cand-subtab-badge {
            background: #fdf2f8 !important;
            color: #db2777 !important;
            border-color: #fbcfe8 !important;
          }
          .cd-cand-subtab[data-stage="negotiations"]:hover,
          .cd-cand-subtab-direct[data-stage="negotiations"]:hover {
            background: #fce7f3 !important;
            border-color: #db2777 !important;
            color: #be185d !important;
          }
          .cd-cand-subtab[data-stage="negotiations"].active,
          .cd-cand-subtab-direct[data-stage="negotiations"].active {
            background: #db2777 !important;
            border-color: #db2777 !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(219, 39, 119, 0.35) !important;
          }
          .cd-cand-subtab[data-stage="negotiations"].active .cd-cand-subtab-badge,
          .cd-cand-subtab-direct[data-stage="negotiations"].active .cd-cand-subtab-badge {
            background: rgba(255, 255, 255, 0.28) !important;
            color: #ffffff !important;
            border-color: transparent !important;
          }

          /* 5. Offer (Warm Amber / Gold #d97706) */
          .cd-cand-subtab[data-stage="offer"],
          .cd-cand-subtab-direct[data-stage="offer"] {
            background: #fffbeb !important;
            border-color: #fde68a !important;
            color: #d97706 !important;
          }
          .cd-cand-subtab[data-stage="offer"] .cd-cand-subtab-badge,
          .cd-cand-subtab-direct[data-stage="offer"] .cd-cand-subtab-badge {
            background: #fffbeb !important;
            color: #d97706 !important;
            border-color: #fde68a !important;
          }
          .cd-cand-subtab[data-stage="offer"]:hover,
          .cd-cand-subtab-direct[data-stage="offer"]:hover {
            background: #fef3c7 !important;
            border-color: #d97706 !important;
            color: #b45309 !important;
          }
          .cd-cand-subtab[data-stage="offer"].active,
          .cd-cand-subtab-direct[data-stage="offer"].active {
            background: #d97706 !important;
            border-color: #d97706 !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(217, 119, 6, 0.35) !important;
          }
          .cd-cand-subtab[data-stage="offer"].active .cd-cand-subtab-badge,
          .cd-cand-subtab-direct[data-stage="offer"].active .cd-cand-subtab-badge {
            background: rgba(255, 255, 255, 0.28) !important;
            color: #ffffff !important;
            border-color: transparent !important;
          }

          /* 6. Joined (Success Emerald Green #16a34a) */
          .cd-cand-subtab[data-stage="joined"],
          .cd-cand-subtab-direct[data-stage="joined"] {
            background: #f0fdf4 !important;
            border-color: #bbf7d0 !important;
            color: #16a34a !important;
          }
          .cd-cand-subtab[data-stage="joined"] .cd-cand-subtab-badge,
          .cd-cand-subtab-direct[data-stage="joined"] .cd-cand-subtab-badge {
            background: #f0fdf4 !important;
            color: #16a34a !important;
            border-color: #bbf7d0 !important;
          }
          .cd-cand-subtab[data-stage="joined"]:hover,
          .cd-cand-subtab-direct[data-stage="joined"]:hover {
            background: #dcfce7 !important;
            border-color: #16a34a !important;
            color: #15803d !important;
          }
          .cd-cand-subtab[data-stage="joined"].active,
          .cd-cand-subtab-direct[data-stage="joined"].active {
            background: #16a34a !important;
            border-color: #16a34a !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(22, 163, 74, 0.35) !important;
          }
          .cd-cand-subtab[data-stage="joined"].active .cd-cand-subtab-badge,
          .cd-cand-subtab-direct[data-stage="joined"].active .cd-cand-subtab-badge {
            background: rgba(255, 255, 255, 0.28) !important;
            color: #ffffff !important;
            border-color: transparent !important;
          }

          /* 7. Rejected (Crimson / Rose Red #ef4444) */
          .cd-cand-subtab[data-stage="rejected"],
          .cd-cand-subtab-direct[data-stage="rejected"] {
            background: #fef2f2 !important;
            border-color: #fecaca !important;
            color: #ef4444 !important;
          }
          .cd-cand-subtab[data-stage="rejected"] .cd-cand-subtab-badge,
          .cd-cand-subtab-direct[data-stage="rejected"] .cd-cand-subtab-badge {
            background: #fef2f2 !important;
            color: #ef4444 !important;
            border-color: #fecaca !important;
          }
          .cd-cand-subtab[data-stage="rejected"]:hover,
          .cd-cand-subtab-direct[data-stage="rejected"]:hover {
            background: #fee2e2 !important;
            border-color: #ef4444 !important;
            color: #b91c1c !important;
          }
          .cd-cand-subtab[data-stage="rejected"].active,
          .cd-cand-subtab-direct[data-stage="rejected"].active {
            background: #ef4444 !important;
            border-color: #ef4444 !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(239, 68, 68, 0.35) !important;
          }
          .cd-cand-subtab[data-stage="rejected"].active .cd-cand-subtab-badge,
          .cd-cand-subtab-direct[data-stage="rejected"].active .cd-cand-subtab-badge {
            background: rgba(255, 255, 255, 0.28) !important;
            color: #ffffff !important;
            border-color: transparent !important;
          }

          /* 8. Interviews (Royal Blue #2563eb) */
          .cd-cand-subtab[data-stage="interviews"],
          .cd-cand-subtab-direct[data-stage="interviews"] {
            background: #eff6ff !important;
            border-color: #bfdbfe !important;
            color: #2563eb !important;
          }
          .cd-cand-subtab[data-stage="interviews"] .cd-cand-subtab-badge,
          .cd-cand-subtab-direct[data-stage="interviews"] .cd-cand-subtab-badge {
            background: #eff6ff !important;
            color: #2563eb !important;
            border-color: #bfdbfe !important;
          }
          .cd-cand-subtab[data-stage="interviews"]:hover,
          .cd-cand-subtab-direct[data-stage="interviews"]:hover {
            background: #dbeafe !important;
            border-color: #2563eb !important;
            color: #1d4ed8 !important;
          }
          .cd-cand-subtab[data-stage="interviews"].active,
          .cd-cand-subtab-direct[data-stage="interviews"].active {
            background: #2563eb !important;
            border-color: #2563eb !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.35) !important;
          }
          .cd-cand-subtab[data-stage="interviews"].active .cd-cand-subtab-badge,
          .cd-cand-subtab-direct[data-stage="interviews"].active .cd-cand-subtab-badge {
            background: rgba(255, 255, 255, 0.28) !important;
            color: #ffffff !important;
            border-color: transparent !important;
          }

          /* TIER 3: Candidate Card Stream */
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

          /* Sub-Card 1: Profile - Space Reclaimed (No Avatar Icon) */
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
            transition: color 0.12s ease !important;
          }
          .cd-cand-name:hover {
            color: #009ef7 !important;
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

          /* Sub-Card 2: Comp */
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
            justify-content: space-between !important;
            gap: 4px !important;
            padding: 2px 7px !important;
            border-radius: 6px !important;
            font-size: 10.5px !important;
            font-weight: 600 !important;
            line-height: 1.3 !important;
            text-decoration: none !important;
            cursor: pointer !important;
            transition: filter 0.15s ease !important;
            max-width: 100% !important;
            overflow: hidden !important;
            white-space: nowrap !important;
          }
          .cd-stage-chip:hover {
            filter: brightness(0.95) !important;
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
            .cd-cand-search-subcard {
              width: 240px !important;
            }
          }
          @media (max-width: 1200px) {
            .cd-cand-card-grid {
              grid-template-columns: minmax(0, 1.8fr) minmax(0, 1.25fr) minmax(0, 1.15fr) minmax(0, 2fr) minmax(0, 1.4fr) minmax(0, 1.15fr) !important;
              gap: 6px !important;
              padding: 8px 10px !important;
            }
            .cd-cand-search-subcard {
              width: 200px !important;
            }
            .cd-cand-subtab,
            .cd-cand-subtab-direct {
              padding: 5px 8px !important;
              font-size: 11px !important;
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
            .cd-cand-toolbar-card {
              flex-direction: column !important;
              align-items: stretch !important;
            }
            .cd-cand-toolbar-right {
              width: 100% !important;
              justify-content: space-between !important;
            }
            .cd-cand-search-subcard {
              width: 100% !important;
              max-width: 100% !important;
            }
          }

          /* Skeleton Sub-Cards */
          .cd-cand-skeleton-card {
            background: #ffffff;
            border: 1px solid #eef2f6;
            border-radius: 10px;
            padding: 14px 16px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02);
          }
          .cd-cand-skeleton-pulse {
            background: #f1f5f9;
            border-radius: 6px;
            animation: cdpulse 1.5s infinite;
          }
        </style>

        @php
            $cur_company_id = auth()->user()->company_id;
            $detail_from = @$directClientDetail->from_company_id ?: (@$directClientDetail->companies_id ?: (@$directClientDetail->company_id ?: @$directClientDetail->client_id));
            $dc_id = $detail_from;
            $kpiCacheKey = "ats:direct_client_kpi_v9:{$cur_company_id}:{$detail_from}";
            $kpiData = cache()->remember($kpiCacheKey, 900, function() use ($detail_from, $cur_company_id) {
                $kpiJobs = GeneralMethod::DirectClientJobsCountAllStatus($detail_from, $cur_company_id);
                $kpiPerf = GeneralMethod::DirectClientCandidatesCountList($detail_from, $cur_company_id);
                return [
                    'kpiActive' => $kpiJobs->job_active ?? 0,
                    'kpiSubmitted' => $kpiPerf->all_candidates ?? 0,
                    'kpiInterviews' => $kpiPerf->all_interview ?? 0,
                    'kpiOffers' => $kpiPerf->offers ?? 0,
                    'kpiJoins' => $kpiPerf->Joined ?? 0,
                    'kpiRejected' => $kpiPerf->rejected ?? 0,
                ];
            });
            $kpiActive     = $kpiData['kpiActive'];
            $kpiSubmitted  = $kpiData['kpiSubmitted'];
            $kpiInterviews = $kpiData['kpiInterviews'];
            $kpiOffers     = $kpiData['kpiOffers'];
            $kpiJoins      = $kpiData['kpiJoins'];

            $detailLogo = '';
            if (!empty(@$directClientDetail->logo_logical_path) && strlen(@$directClientDetail->logo_logical_path) > 10) $detailLogo = @$directClientDetail->logo_logical_path;
            if (!empty(@$directClientDetail->logo) && strlen(@$directClientDetail->logo) > 10) $detailLogo = @$directClientDetail->logo;
            $detailName = ucwords(@$directClientDetail->company_name ?: 'Direct Client');
            $detailInitials = strtoupper(implode('', array_map(fn($w) => mb_substr($w, 0, 1), array_slice(explode(' ', trim($detailName)), 0, 2)))) ?: 'DC';

            $industries_name = '';
            if (trim((string) @$directClientDetail->industries) !== '') {
                $targetIndId = (string) @$directClientDetail->industries;
                if (isset($industries) && is_iterable($industries)) {
                    foreach ($industries as $dItem) {
                        if (is_object($dItem) && isset($dItem->ind_id) && (string)$dItem->ind_id === $targetIndId) {
                            $industries_name = (string) ($dItem->ind_name ?? $dItem->name ?? '');
                            break;
                        } elseif (is_array($dItem) && isset($dItem['ind_id']) && (string)$dItem['ind_id'] === $targetIndId) {
                            $industries_name = (string) ($dItem['ind_name'] ?? $dItem['name'] ?? '');
                            break;
                        }
                    }
                }
                if ($industries_name === '' && isset($industries[$directClientDetail->industries])) {
                    $rawInd = $industries[$directClientDetail->industries];
                    if (is_object($rawInd)) {
                        $industries_name = (string) ($rawInd->ind_name ?? $rawInd->name ?? '');
                    } elseif (is_array($rawInd)) {
                        $industries_name = (string) ($rawInd['ind_name'] ?? $rawInd['name'] ?? '');
                    } else {
                        $industries_name = (string) $rawInd;
                    }
                }
            }
            if (empty($industries_name)) {
                $industries_name = 'Other / Diversified Services';
            }

            $tagsArr = array_filter(array_map('trim', explode(',', (string) @$directClientDetail->tags)));
            if (empty($tagsArr)) {
                $tagsArr = ['Enterprise Staffing', 'Talent Marketplace', 'Direct Placement'];
            }

            $d_company_type_val = '';
            if (@$directClientDetail->industries_type > 0 && isset($companyType[@$directClientDetail->industries_type])) {
                $rawDComp = $companyType[@$directClientDetail->industries_type];
                $d_company_type_val = is_string($rawDComp) ? $rawDComp : (is_object($rawDComp) ? (string)($rawDComp->name ?? '') : '');
            }
            if (empty($d_company_type_val)) {
                $d_company_type_val = 'Direct Corporate Client';
            }

            // Workforce Scale Range calculation
            $cFrom = @$directClientDetail->company_size_from;
            $cTo = @$directClientDetail->company_sizes ?: @$directClientDetail->company_size;
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
            $emailClean = @$directClientDetail->email ?: @$directClientDetail->companies_email;
            $phoneClean = @$directClientDetail->phone ?: @$directClientDetail->companies_phone;

            $primaryName = @$directClientDetail->contact_person ?? (@$directClientDetail->name ?? 'Primary Contact');
            $primaryInitials = strtoupper(implode('', array_map(fn($w) => mb_substr($w, 0, 1), array_slice(explode(' ', trim($primaryName)), 0, 2)))) ?: 'PC';
            $primaryDesignation = @$directClientDetail->designation ?? 'Hiring Lead';
            $primaryEmail = $emailClean ?: 'contact@client.com';
            
            // Fix double plus: ensure strictly single '+' before country code and number
            $rawPhone = (string)($phoneClean ?: '+91 98765 43210');
            $primaryPhone = preg_replace('/^\++/', '+', trim($rawPhone));

            // Dynamic Client Since from actual onboarding date
            $clientSinceYear = (!empty(@$directClientDetail->created_at)) ? \Carbon\Carbon::parse(@$directClientDetail->created_at)->format('Y') : ((!empty(@$directClientDetail->to_created_at)) ? \Carbon\Carbon::parse(@$directClientDetail->to_created_at)->format('Y') : date('Y'));
            $clientCreatedAt = (!empty(@$directClientDetail->created_at)) ? \Carbon\Carbon::parse(@$directClientDetail->created_at)->format('d M, Y') : ((!empty(@$directClientDetail->to_created_at)) ? \Carbon\Carbon::parse(@$directClientDetail->to_created_at)->format('d M, Y') : date('d M, Y'));
            $clientUpdatedAt = @$directClientDetail->updated_at ? Carbon::parse($directClientDetail->updated_at)->diffForHumans() : '1 day ago';
            $isClientActive = ($kpiActive > 0);
            $city_name = $city_name ?? (@$directClientDetail->city_name ?: (@$directClientDetail->city ?: ''));
            $country_name = $country_name ?? (@$directClientDetail->country_name ?: (@$directClientDetail->country ?: ''));
            $userPermissions = (isset($userPermissions) && is_array($userPermissions)) ? $userPermissions : [];
        @endphp

        <input type="hidden" name="direct_client_id" id="direct_client_id" value="{{ $directClientDetail->client_id ?? $directClientDetail->id }}" />

        <div class="cd-scope">
          <!-- ── Tier 1: Action Header Toolbar Card ── -->
          <div class="cd-top-bar">
            <div class="cd-top-actions">
              <button type="button" class="cd-hdr-action-btn followup cd-btn-followup" data-client-id="{{ $directClientDetail->client_id ?? $directClientDetail->id }}">
                <i class="fas fa-calendar-check" style="color:#0284c7;"></i> Follow Up
              </button>
            </div>
            <div class="cd-top-close-wrap">
              <button type="button" class="cd-hdr-btn-close cd-close-drawer" id="kt_activitiesDirectClient_close_btn" title="Close Drawer">
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
                    <span class="cd-hero-id-badge" style="color:#059669; background:#ecfdf5; border-color:#a7f3d0;">#CL-{{ $directClientDetail->client_id ?? $directClientDetail->id }}</span>
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
                    <i class="fas fa-handshake me-1"></i> Direct Client
                  </span>
                </div>
                <div class="cd-hero-status-pos-row">
                  @if ($isClientActive)
                    <span class="cd-status-badge-active" title="Client is actively hiring">
                      <span class="cd-pulse-emerald"></span> Active
                    </span>
                  @else
                    <span class="cd-status-badge-inactive" title="No active job requisitions">
                      <span class="cd-pulse-dot cd-pulse-grey"></span> Inactive
                    </span>
                  @endif
                  <span class="cd-status-sep">|</span>
                  <a href="#requisitions" class="cd-hero-positions-link" data-bs-toggle="tab" data-toggle="tab" title="View Active Job Positions">
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
              <a class="cd-tab-pill-btn active" data-bs-toggle="tab" data-toggle="tab" href="#overview" role="tab" title="Overview">
                <i class="fas fa-house"></i>
                <span>Overview</span>
              </a>

              <a class="cd-tab-pill-btn" data-bs-toggle="tab" data-toggle="tab" href="#recent_activity" role="tab" title="Recent Activity">
                <i class="fas fa-clock"></i>
                <span>Activity</span>
              </a>

              <a class="cd-tab-pill-btn" data-bs-toggle="tab" data-toggle="tab" href="#contacts" role="tab" title="Client Contacts">
                <i class="fas fa-address-book"></i>
                <span>Contacts</span>
                <span class="cd-tab-badge-pill" id="cdContactNavBadgeDirect" style="{{ (!empty($client_contact_count) && $client_contact_count > 0) ? '' : 'display:none;' }}">{{ $client_contact_count ?? 0 }}</span>
              </a>

              <a class="cd-tab-pill-btn" data-bs-toggle="tab" data-toggle="tab" href="#requisitions" role="tab" title="Active Jobs">
                <i class="fas fa-briefcase"></i>
                <span>Jobs</span>
                @if($kpiActive > 0)<span class="cd-tab-badge-pill">{{ $kpiActive }}</span>@endif
              </a>

              <a class="cd-tab-pill-btn" data-bs-toggle="tab" data-toggle="tab" href="#candidate_list" role="tab" title="Candidates Pipeline">
                <i class="fas fa-users"></i>
                <span>Candidates</span>
                @if($kpiSubmitted > 0)<span class="cd-tab-badge-pill">{{ $kpiSubmitted }}</span>@endif
              </a>

              <a class="cd-tab-pill-btn" data-bs-toggle="tab" data-toggle="tab" href="#interview_tracker" role="tab" title="Interview Schedule">
                <i class="fas fa-calendar-days"></i>
                <span>Interviews</span>
                <span class="cd-tab-badge-pill" id="tabBadgeInterviewTracker" @if($kpiInterviews <= 0) style="display:none;" @endif>{{ $kpiInterviews }}</span>
              </a>

              <a class="cd-tab-pill-btn" data-bs-toggle="tab" data-toggle="tab" href="#candidate_joining" role="tab" title="Offers & Joinings">
                <i class="fas fa-file-signature"></i>
                <span>Offers &amp; Joins</span>
                @php $totalOffersJoinsDirect = ($kpiOffers ?? 0) + ($kpiJoins ?? 0); @endphp
                <span class="cd-tab-badge-pill" id="ojDirectTabBadge" @if($totalOffersJoinsDirect <= 0) style="display:none;" @endif>{{ $totalOffersJoinsDirect }}</span>
              </a>

              <a class="cd-tab-pill-btn" data-bs-toggle="tab" data-toggle="tab" href="#candidateDashboard" role="tab" title="Performance Analytics">
                <i class="fas fa-chart-column"></i>
                <span>Performance</span>
              </a>
            </div>
          </div>

          <!-- ── Tab Contents Container ── -->
          <div class="tab-content">
            <!-- ── TAB 1: OVERVIEW ── -->
            <div class="tab-pane active" id="overview" role="tabpanel">
              <div class="cd-overview-subcards-grid">
                <!-- Left Column Sub-Cards Stream -->
                <div class="cd-overview-col-left">
                  <!-- Sub-Card: About Client -->
                  <div class="cd-master-subcard">
                    <div class="cd-subcard-header">
                      <div class="cd-subcard-header-left">
                        <div class="cd-subcard-header-icon blue">
                          <i class="fas fa-info-circle"></i>
                        </div>
                        <h4 class="cd-subcard-title">About the Client</h4>
                      </div>
                      @if (!empty($webClean))
                        <a href="{{ str_starts_with($webClean, 'http') ? $webClean : 'https://'.$webClean }}" target="_blank" class="cd-subcard-action-link">
                          {{ $webClean }} <i class="fas fa-external-link-alt ms-1"></i>
                        </a>
                      @endif
                    </div>
                    <div class="cd-about-text-content">
                      @php
                          $description = @$directClientDetail->description ?: @$directClientDetail->companies_description;
                      @endphp
                      @if (!empty($description))
                        {!! $description !!}
                      @else
                        {{ $detailName }} is a verified enterprise client partnering directly for specialized talent acquisition, contract staffing, and executive hiring solutions.
                      @endif
                    </div>
                  </div>

                  <!-- Sub-Card: Recent Activity Timeline Stream -->
                  <div class="cd-master-subcard">
                    <div class="cd-subcard-header">
                      <div class="cd-subcard-header-left">
                        <div class="cd-subcard-header-icon amber">
                          <i class="fas fa-clock"></i>
                        </div>
                        <h4 class="cd-subcard-title">Recent Activity Stream</h4>
                      </div>
                      <a href="javascript:void(0);" class="cd-subcard-action-link cd-view-all-activity">
                        View Audit Log <i class="fas fa-arrow-right ms-1"></i>
                      </a>
                    </div>
                    <div class="cd-timeline-subcards-stream">
                      <!-- Activity 1 -->
                      <div class="cd-timeline-item-subcard">
                        <div class="cd-timeline-dot green"><i class="fas fa-users"></i></div>
                        <div class="cd-timeline-body">
                          <div class="cd-timeline-title">Candidates submitted to client</div>
                          <div class="cd-timeline-subtext">{{ auth()->user()->name ?? 'Recruiter' }} forwarded active candidate profiles</div>
                        </div>
                        <div class="cd-timeline-meta-wrap">
                          <span class="cd-timeline-badge green">Submitted</span>
                          <span class="cd-timeline-timestamp">2 hours ago</span>
                        </div>
                      </div>

                      <!-- Activity 2 -->
                      <div class="cd-timeline-item-subcard">
                        <div class="cd-timeline-dot purple"><i class="fas fa-calendar-check"></i></div>
                        <div class="cd-timeline-body">
                          <div class="cd-timeline-title">Client interview scheduled</div>
                          <div class="cd-timeline-subtext">Interview confirmed with hiring team</div>
                        </div>
                        <div class="cd-timeline-meta-wrap">
                          <span class="cd-timeline-badge amber">Scheduled</span>
                          <span class="cd-timeline-timestamp">1 day ago</span>
                        </div>
                      </div>

                      <!-- Activity 3 -->
                      <div class="cd-timeline-item-subcard">
                        <div class="cd-timeline-dot orange"><i class="fas fa-file-lines"></i></div>
                        <div class="cd-timeline-body">
                          <div class="cd-timeline-title">Offer proposal extended</div>
                          <div class="cd-timeline-subtext">Compensation terms approved</div>
                        </div>
                        <div class="cd-timeline-meta-wrap">
                          <span class="cd-timeline-badge orange">Offer Sent</span>
                          <span class="cd-timeline-timestamp">2 days ago</span>
                        </div>
                      </div>

                      <!-- Activity 4 -->
                      <div class="cd-timeline-item-subcard">
                        <div class="cd-timeline-dot blue"><i class="fas fa-briefcase"></i></div>
                        <div class="cd-timeline-body">
                          <div class="cd-timeline-title">Direct mandate received</div>
                          <div class="cd-timeline-subtext">Hiring requisition added to board</div>
                        </div>
                        <div class="cd-timeline-meta-wrap">
                          <span class="cd-timeline-badge blue">Mandate</span>
                          <span class="cd-timeline-timestamp">3 days ago</span>
                        </div>
                      </div>

                      <!-- Activity 5 -->
                      <div class="cd-timeline-item-subcard">
                        <div class="cd-timeline-dot green"><i class="fas fa-user-check"></i></div>
                        <div class="cd-timeline-body">
                          <div class="cd-timeline-title">Candidate onboarding complete</div>
                          <div class="cd-timeline-subtext">Placement milestone reached</div>
                        </div>
                        <div class="cd-timeline-meta-wrap">
                          <span class="cd-timeline-badge green">Joined</span>
                          <span class="cd-timeline-timestamp">5 days ago</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Right Column Sub-Cards Stream -->
                <div class="cd-overview-col-right">
                  <!-- Sub-Card: Client Information (God-Level Executive Vertical Listing) -->
                  <div class="cd-master-subcard">
                    <div class="cd-subcard-header">
                      <div class="cd-subcard-header-left">
                        <div class="cd-subcard-header-icon indigo">
                          <i class="fas fa-building"></i>
                        </div>
                        <h4 class="cd-subcard-title">Client Information</h4>
                      </div>
                      <span class="badge bg-light-success text-success fw-semibold" style="font-size:10px; padding:3px 7px;">
                        <i class="fas fa-shield-halved me-1"></i>Direct Corporate
                      </span>
                    </div>

                    @php
                      // Extract detail fields for direct client
                      $dRawDetail = $directClientDetail->detail ?? null;
                      $dClientId = $directClientDetail->client_id ?? $directClientDetail->companies_id ?? $directClientDetail->id ?? null;
                      if (empty($dRawDetail) && !empty($dClientId)) {
                        $dRawDetail = cache()->remember('client_detail_' . $dClientId, 3600, function() use ($dClientId) {
                          return \Illuminate\Support\Facades\DB::table('clients')->where('id', $dClientId)->value('detail');
                        });
                      }
                      $cDetail = !empty($dRawDetail) ? (is_array($dRawDetail) ? $dRawDetail : json_decode($dRawDetail, true)) : [];
                      $careerPageUrl = $cDetail['career_page_url'] ?? ($directClientDetail->career_page_link ?? '');
                      $taxId = trim((string)($cDetail['tax_id'] ?? ''));
                      $apEmail = trim((string)($cDetail['ap_email'] ?? ''));
                      $linkedinUrl = trim((string)($cDetail['linkedin_url'] ?? ''));
                      $twitterUrl = trim((string)($cDetail['twitter_url'] ?? ''));
                      $githubUrl = trim((string)($cDetail['github_url'] ?? ''));
                      $youtubeUrl = trim((string)($cDetail['youtube_url'] ?? ''));
                      $facebookUrl = trim((string)($cDetail['facebook_url'] ?? ''));
                      $instagramUrl = trim((string)($cDetail['instagram_url'] ?? ''));
                      $customLink = trim((string)($cDetail['custom_link'] ?? ''));

                      $hasSocialLinks = (!empty($linkedinUrl) || !empty($twitterUrl) || !empty($githubUrl) || !empty($youtubeUrl) || !empty($facebookUrl) || !empty($instagramUrl) || !empty($customLink));

                      // Workforce Scale Range calculation
                      $cFrom = @$directClientDetail->company_size_from;
                      $cTo = @$directClientDetail->company_sizes ?: @$directClientDetail->company_size;
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

                      // Extract multiple locations
                      $allLocations = [];
                      if (!empty($directClientDetail->client_location) && is_iterable($directClientDetail->client_location)) {
                        foreach ($directClientDetail->client_location as $l) {
                          $allLocations[] = (object) $l;
                        }
                      }
                      if (empty($allLocations) && !empty($dClientId)) {
                        $dbLocs = \Illuminate\Support\Facades\DB::table('client_location')
                          ->leftJoin('countries', 'countries.id', '=', 'client_location.country_id')
                          ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                          ->leftJoin('state_list', 'state_list.id', '=', 'client_location.state')
                          ->select('client_location.*', 'countries.country_name', 'city_list.city as city_name_resolved', 'state_list.state as state_name')
                          ->where('client_location.client_id', $dClientId)
                          ->get();
                        foreach ($dbLocs as $l) {
                          $allLocations[] = $l;
                        }
                      }
                      $locCount = count($allLocations);
                    @endphp

                    <div class="cd-vertical-info-list">
                      <!-- 1. Locations & Addresses -->
                      <div class="cd-vinfo-row">
                        <div class="cd-vinfo-key">
                          <div class="cd-vinfo-icon red"><i class="fas fa-location-dot"></i></div>
                          <span class="cd-vinfo-label">{{ $locCount > 1 ? 'Locations ('.$locCount.')' : 'Headquarters' }}</span>
                        </div>
                        <div class="cd-vinfo-val" style="align-items: stretch;">
                          @if ($locCount > 0)
                            <div class="cd-loc-group">
                              @foreach ($allLocations as $idx => $loc)
                                @php
                                  $lCity = !empty($loc->city_name_resolved) ? $loc->city_name_resolved : (!empty($loc->city_name) ? $loc->city_name : ($loc->city ?? ''));
                                  $lState = !empty($loc->state_name) ? $loc->state_name : (!empty($loc->state) ? $loc->state : '');
                                  $lCountry = !empty($loc->country_name) ? $loc->country_name : (!empty($loc->country) ? $loc->country : '');
                                  $lAddr = trim(!empty($loc->address) ? $loc->address : '');
                                  $lPostcode = !empty($loc->postcode) && $loc->postcode !== '0' && $loc->postcode !== 0 ? trim((string)$loc->postcode) : '';
                                  $isHq = ($idx === 0);
                                  $cityStateParts = array_filter([$lCity ? ucwords(trim($lCity)) : '', $lState ? ucwords(trim($lState)) : '']);
                                  $cityStateText = implode(', ', $cityStateParts);
                                @endphp
                                <div class="cd-loc-card {{ $isHq ? 'is-primary hq' : '' }}">
                                  <div class="cd-loc-header">
                                    <div class="cd-loc-header-left">
                                      <span class="cd-loc-badge {{ $isHq ? 'loc-primary' : 'loc-secondary' }}">
                                        <i class="fas fa-map-marker-alt me-1"></i>Location {{ $idx + 1 }}
                                      </span>
                                      @if($isHq)
                                        <span class="cd-loc-hq-tag">HQ</span>
                                      @endif
                                    </div>
                                    @if($lCountry)
                                      <span class="cd-loc-country-pill">
                                        <i class="fas fa-globe-americas me-1 text-primary" style="font-size:9.5px;"></i>{{ ucwords(trim($lCountry)) }}
                                      </span>
                                    @endif
                                  </div>
                                  @if($cityStateText)
                                    <div class="cd-loc-city-line">
                                      <span class="cd-loc-city-name">{{ $cityStateText }}</span>
                                    </div>
                                  @endif
                                  @if($lAddr)
                                    <div class="cd-loc-street-line">
                                      <i class="fas fa-map-pin text-danger me-1.5" style="font-size: 10.5px; margin-top: 2px; flex-shrink: 0;"></i>
                                      <span>{{ $lAddr }}</span>
                                    </div>
                                  @endif
                                  @if($lPostcode)
                                    <div class="cd-loc-meta-line">
                                      <span class="cd-loc-meta-chip">
                                        <i class="fas fa-hashtag text-muted me-1" style="font-size: 9.5px;"></i>Postal Code: <strong>{{ $lPostcode }}</strong>
                                      </span>
                                    </div>
                                  @endif
                                </div>
                              @endforeach
                            </div>
                          @else
                            @php
                              $fallbackCity = $city_name ?: (@$dClient->city_name ?: (@$dClient->city ?: ''));
                              $fallbackState = !empty(@$dClient->state_name) ? @$dClient->state_name : (!empty(@$dClient->state) ? @$dClient->state : '');
                              $fallbackCountry = $country_name ?: (@$dClient->country_name ?: (@$dClient->country ?: ''));
                              $fallbackAddr = trim(!empty(@$client_location->address) ? @$client_location->address : (!empty(@$dClient->address) ? @$dClient->address : ''));
                              $fallbackPostcode = !empty(@$client_location->postcode) && @$client_location->postcode !== '0' ? trim((string)@$client_location->postcode) : '';
                              $fallbackCityState = implode(', ', array_filter([$fallbackCity ? ucwords(trim($fallbackCity)) : '', $fallbackState ? ucwords(trim($fallbackState)) : '']));
                            @endphp
                            <div class="cd-loc-card is-primary hq">
                              <div class="cd-loc-header">
                                <div class="cd-loc-header-left">
                                  <span class="cd-loc-badge loc-primary"><i class="fas fa-map-marker-alt me-1"></i>Location 1</span>
                                  <span class="cd-loc-hq-tag">HQ</span>
                                </div>
                                @if($fallbackCountry)
                                  <span class="cd-loc-country-pill"><i class="fas fa-globe-americas me-1 text-primary" style="font-size:9.5px;"></i>{{ ucwords(trim($fallbackCountry)) }}</span>
                                @endif
                              </div>
                              @if($fallbackCityState)
                                <div class="cd-loc-city-line">
                                  <span class="cd-loc-city-name">{{ $fallbackCityState }}</span>
                                </div>
                              @endif
                              @if($fallbackAddr)
                                <div class="cd-loc-street-line">
                                  <i class="fas fa-map-pin text-danger me-1.5" style="font-size: 10.5px; margin-top: 2px; flex-shrink: 0;"></i>
                                  <span>{{ $fallbackAddr }}</span>
                                </div>
                              @endif
                              @if($fallbackPostcode)
                                <div class="cd-loc-meta-line">
                                  <span class="cd-loc-meta-chip"><i class="fas fa-hashtag text-muted me-1" style="font-size: 9.5px;"></i>Postal Code: <strong>{{ $fallbackPostcode }}</strong></span>
                                </div>
                              @endif
                            </div>
                          @endif
                        </div>
                      </div>

                      <!-- 2. Official Website -->
                      <div class="cd-vinfo-row">
                        <div class="cd-vinfo-key">
                          <div class="cd-vinfo-icon blue"><i class="fas fa-globe"></i></div>
                          <span class="cd-vinfo-label">Website</span>
                        </div>
                        <div class="cd-vinfo-val">
                          @if (!empty($webClean))
                            <a href="{{ str_starts_with($webClean, 'http') ? $webClean : 'https://'.$webClean }}" target="_blank" class="cd-vinfo-link direct">
                              {{ $webClean }} <i class="fas fa-arrow-up-right-from-square" style="font-size:9px;"></i>
                            </a>
                          @else
                            <span class="text-muted">—</span>
                          @endif
                        </div>
                      </div>

                      <!-- 3. Career / Jobs Page -->
                      @if (!empty($careerPageUrl))
                      <div class="cd-vinfo-row">
                        <div class="cd-vinfo-key">
                          <div class="cd-vinfo-icon green"><i class="fas fa-briefcase"></i></div>
                          <span class="cd-vinfo-label">Careers Portal</span>
                        </div>
                        <div class="cd-vinfo-val">
                          @php
                            $careerClean = preg_replace('#^https?://#', '', rtrim($careerPageUrl, '/'));
                          @endphp
                          <a href="{{ str_starts_with($careerPageUrl, 'http') ? $careerPageUrl : 'https://'.$careerPageUrl }}" target="_blank" class="cd-career-pill" title="{{ $careerPageUrl }}">
                            <span>{{ strlen($careerClean) > 28 ? substr($careerClean, 0, 26).'…' : $careerClean }}</span>
                            <i class="fas fa-arrow-up-right-from-square" style="font-size:8px;"></i>
                          </a>
                        </div>
                      </div>
                      @endif

                      <!-- 4. Tax ID / Business Registration No / GSTIN (Hidden if not available) -->
                      @if (!empty($taxId))
                      <div class="cd-vinfo-row">
                        <div class="cd-vinfo-key">
                          <div class="cd-vinfo-icon amber"><i class="fas fa-receipt"></i></div>
                          <span class="cd-vinfo-label">Tax ID / GSTIN</span>
                        </div>
                        <div class="cd-vinfo-val">
                          <div class="cd-tax-wrap">
                            <span class="cd-tax-code">{{ $taxId }}</span>
                            <button type="button" class="cd-copy-btn" title="Copy Tax ID" onclick="navigator.clipboard.writeText('{{ $taxId }}'); $(this).find('i').removeClass('fa-copy').addClass('fa-check text-success'); setTimeout(() => $(this).find('i').removeClass('fa-check text-success').addClass('fa-copy'), 1500);">
                              <i class="far fa-copy"></i>
                            </button>
                          </div>
                        </div>
                      </div>
                      @endif

                      <!-- 5. Industry -->
                      <div class="cd-vinfo-row">
                        <div class="cd-vinfo-key">
                          <div class="cd-vinfo-icon purple"><i class="fas fa-industry"></i></div>
                          <span class="cd-vinfo-label">Industry</span>
                        </div>
                        <div class="cd-vinfo-val">
                          <span class="fw-semibold text-gray-800">{{ $industries_name ?: 'Other / Diversified Services' }}</span>
                        </div>
                      </div>

                      <!-- 6. Company Type -->
                      <div class="cd-vinfo-row">
                        <div class="cd-vinfo-key">
                          <div class="cd-vinfo-icon sky"><i class="fas fa-landmark"></i></div>
                          <span class="cd-vinfo-label">Company Type</span>
                        </div>
                        <div class="cd-vinfo-val">
                          <span class="cd-type-badge-pill">{{ $d_company_type_val ?: 'Direct Corporate Client' }}</span>
                        </div>
                      </div>

                      <!-- 7. Employees / Workforce Scale (Formatted Range) -->
                      <div class="cd-vinfo-row">
                        <div class="cd-vinfo-key">
                          <div class="cd-vinfo-icon emerald"><i class="fas fa-users"></i></div>
                          <span class="cd-vinfo-label">Workforce Scale</span>
                        </div>
                        <div class="cd-vinfo-val">
                          <span class="fw-semibold text-gray-800">{{ $workforceRange }}</span>
                        </div>
                      </div>

                      <!-- 8. Founded Year -->
                      <div class="cd-vinfo-row">
                        <div class="cd-vinfo-key">
                          <div class="cd-vinfo-icon orange"><i class="fas fa-calendar-alt"></i></div>
                          <span class="cd-vinfo-label">Founded</span>
                        </div>
                        <div class="cd-vinfo-val">
                          @php
                            $fYear = @$directClientDetail->founded ?: 2010;
                            $currYr = (int)date('Y');
                            $vintage = ($currYr - (int)$fYear);
                          @endphp
                          <div>
                            <span class="fw-semibold text-gray-800">{{ $fYear }}</span>
                            @if($vintage > 0)
                              <span class="cd-vintage-chip">({{ $vintage }} yrs active)</span>
                            @endif
                          </div>
                        </div>
                      </div>

                      <!-- 9. Vendor ID (if available) -->
                      @if(!empty(@$directClientDetail->vendor_id))
                      <div class="cd-vinfo-row">
                        <div class="cd-vinfo-key">
                          <div class="cd-vinfo-icon slate"><i class="fas fa-id-badge"></i></div>
                          <span class="cd-vinfo-label">Vendor ID</span>
                        </div>
                        <div class="cd-vinfo-val">
                          <span class="cd-tax-code">{{ @$directClientDetail->vendor_id }}</span>
                        </div>
                      </div>
                      @endif

                      <!-- 10. Connected Date -->
                      @if(!empty(@$directClientDetail->to_created_at))
                      <div class="cd-vinfo-row">
                        <div class="cd-vinfo-key">
                          <div class="cd-vinfo-icon cyan"><i class="fas fa-handshake"></i></div>
                          <span class="cd-vinfo-label">Partnership</span>
                        </div>
                        <div class="cd-vinfo-val">
                          <span class="text-gray-800 fw-semibold">{{ date('d M, Y', strtotime(@$directClientDetail->to_created_at)) }}</span>
                        </div>
                      </div>
                      @endif

                      <!-- 11. Accounts Payable / Billing Email (if available) -->
                      @if(!empty($apEmail))
                      <div class="cd-vinfo-row">
                        <div class="cd-vinfo-key">
                          <div class="cd-vinfo-icon cyan"><i class="fas fa-envelope-open-text"></i></div>
                          <span class="cd-vinfo-label">Billing Email</span>
                        </div>
                        <div class="cd-vinfo-val">
                          <a href="mailto:{{ $apEmail }}" class="cd-vinfo-link text-truncate" style="max-width:200px;">
                            {{ $apEmail }}
                          </a>
                        </div>
                      </div>
                      @endif

                      <!-- 12. Social & Online Profiles (Hidden if none available) -->
                      @if($hasSocialLinks)
                      <div class="cd-vinfo-row">
                        <div class="cd-vinfo-key">
                          <div class="cd-vinfo-icon indigo"><i class="fas fa-share-nodes"></i></div>
                          <span class="cd-vinfo-label">Social Channels</span>
                        </div>
                        <div class="cd-vinfo-val">
                          <div class="cd-social-links-row">
                            @if(!empty($linkedinUrl))
                              <a href="{{ str_starts_with($linkedinUrl, 'http') ? $linkedinUrl : 'https://'.$linkedinUrl }}" target="_blank" class="cd-social-btn linkedin" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            @endif
                            @if(!empty($twitterUrl))
                              <a href="{{ str_starts_with($twitterUrl, 'http') ? $twitterUrl : 'https://'.$twitterUrl }}" target="_blank" class="cd-social-btn twitter" title="X (Twitter)"><i class="fab fa-x-twitter"></i></a>
                            @endif
                            @if(!empty($githubUrl))
                              <a href="{{ str_starts_with($githubUrl, 'http') ? $githubUrl : 'https://'.$githubUrl }}" target="_blank" class="cd-social-btn github" title="GitHub"><i class="fab fa-github"></i></a>
                            @endif
                            @if(!empty($youtubeUrl))
                              <a href="{{ str_starts_with($youtubeUrl, 'http') ? $youtubeUrl : 'https://'.$youtubeUrl }}" target="_blank" class="cd-social-btn youtube" title="YouTube"><i class="fab fa-youtube"></i></a>
                            @endif
                            @if(!empty($facebookUrl))
                              <a href="{{ str_starts_with($facebookUrl, 'http') ? $facebookUrl : 'https://'.$facebookUrl }}" target="_blank" class="cd-social-btn facebook" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                            @endif
                            @if(!empty($instagramUrl))
                              <a href="{{ str_starts_with($instagramUrl, 'http') ? $instagramUrl : 'https://'.$instagramUrl }}" target="_blank" class="cd-social-btn instagram" title="Instagram"><i class="fab fa-instagram"></i></a>
                            @endif
                            @if(!empty($customLink))
                              <a href="{{ str_starts_with($customLink, 'http') ? $customLink : 'https://'.$customLink }}" target="_blank" class="cd-social-btn custom" title="Custom Link"><i class="fas fa-link"></i></a>
                            @endif
                          </div>
                        </div>
                      </div>
                      @endif
                    </div>
                  </div>

                  <!-- Sub-Card: Primary SPOC Master Card -->
                  <div class="cd-master-subcard">
                    <div class="cd-subcard-header">
                      <div class="cd-subcard-header-left">
                        <div class="cd-subcard-header-icon teal">
                          <i class="fas fa-id-badge"></i>
                        </div>
                        <h4 class="cd-subcard-title">Primary SPOC</h4>
                      </div>
                      <a href="javascript:void(0);" class="cd-subcard-action-link cd-view-all-contacts">
                        All Contacts <i class="fas fa-arrow-right ms-1"></i>
                      </a>
                    </div>
                    <div class="cd-contact-master-subcard">
                      <div class="cd-contact-master-top">
                        <div class="cd-contact-avatar-box">
                          {{ $primaryInitials }}
                        </div>
                        <div class="cd-contact-master-info">
                          <div class="cd-contact-master-name-row">
                            <span class="cd-contact-master-name">{{ $primaryName }}</span>
                            <span class="cd-contact-primary-tag">Primary</span>
                          </div>
                          <div class="cd-contact-master-role">{{ $primaryDesignation }}</div>
                        </div>
                      </div>
                      <div class="cd-contact-channels-grid">
                        <a href="mailto:{{ $primaryEmail }}" class="cd-contact-channel-item" title="Send Email">
                          <div class="cd-contact-channel-icon email"><i class="fas fa-envelope"></i></div>
                          <span class="cd-contact-channel-text">{{ $primaryEmail }}</span>
                        </a>
                        <a href="tel:{{ $primaryPhone }}" class="cd-contact-channel-item" title="Call Phone">
                          <div class="cd-contact-channel-icon phone"><i class="fas fa-phone"></i></div>
                          <span class="cd-contact-channel-text">{{ $primaryPhone }}</span>
                        </a>
                      </div>
                    </div>
                  </div>

                  <!-- Sub-Card: Client Services / Tags -->
                  <div class="cd-master-subcard">
                    <div class="cd-subcard-header">
                      <div class="cd-subcard-header-left">
                        <div class="cd-subcard-header-icon pink">
                          <i class="fas fa-tags"></i>
                        </div>
                        <h4 class="cd-subcard-title">Client Services &amp; Tags</h4>
                      </div>
                    </div>
                    <div class="cd-tag-cloud-subcard">
                      @foreach ($tagsArr as $tag)
                        <span class="cd-service-tag-pill">{{ ucwords($tag) }}</span>
                      @endforeach
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- ── TAB 2: RECENT ACTIVITY (3-Tier Dynamic Card + Sub-Card Master Standard) ── -->
            <div class="tab-pane" id="recent_activity" role="tabpanel">
              <div class="px-3 py-2">
                @include('service_company.client.components.tab-activity')
              </div>
            </div>
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
                                            <h4 class="cd-contact-title">Client Contacts</h4>
                                            <span class="cd-contact-count-badge" id="cdContactCountBadgeDirect">
                                                <span id="cdContactCountTextDirect">{{ $client_contact_count ?? 0 }}</span> Contacts
                                            </span>
                                            <span class="cd-contact-scope-pill">
                                                <i class="fas fa-building text-success me-1"></i> Direct Client
                                            </span>
                                        </div>
                                        <div class="cd-contact-subtitle">Authoritative directory of key stakeholders, hiring managers, and primary coordinators.</div>
                                    </div>
                                </div>
                                <div class="cd-contact-toolbar-right">
                                    <!-- Search Contacts Sub-Card with Clear Button -->
                                    <div class="cd-contact-search-subcard">
                                        <i class="fas fa-search fa-solid"></i>
                                        <input type="text" id="cdContactSearchInputDirect" placeholder="Search contacts by name, role, email, phone..." autocomplete="off">
                                        <button type="button" class="cd-contact-search-clear" id="cdContactSearchClearDirect" style="display: none;" title="Clear search">
                                            <i class="fas fa-times fa-solid"></i>
                                        </button>
                                    </div>
                                    <!-- Quick Refresh / Reset Button -->
                                    <button type="button" id="cdContactResetBtnDirect" class="cd-contact-action-subcard" title="Reset Filters &amp; Refresh">
                                        <i class="fas fa-sync-alt fa-solid"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- TIER 2: SUB-TABS FILTER CARD -->
                            <div class="cd-contact-tabs-card">
                                <div class="cd-contact-tabs-list">
                                    <button type="button" class="cd-contact-subtab active" data-filter="all" id="cdContactTabAllDirect">
                                        <span>All Contacts</span>
                                        <span class="cd-contact-subtab-badge" id="contactTabCountAllDirect">{{ $client_contact_count ?? 0 }}</span>
                                    </button>
                                    <button type="button" class="cd-contact-subtab" data-filter="primary" id="cdContactTabPrimaryDirect">
                                        <span><i class="fas fa-star text-amber-500 me-1" style="font-size:10px;"></i> Primary SPOCs</span>
                                        <span class="cd-contact-subtab-badge" id="contactTabCountPrimaryDirect">{{ ($client_contact_count ?? 0) > 0 ? 1 : 0 }}</span>
                                    </button>
                                    <button type="button" class="cd-contact-subtab" data-filter="manager" id="cdContactTabManagerDirect">
                                        <span>Hiring Managers &amp; Leads</span>
                                        <span class="cd-contact-subtab-badge" id="contactTabCountManagerDirect">0</span>
                                    </button>
                                    <button type="button" class="cd-contact-subtab" data-filter="hr" id="cdContactTabHrDirect">
                                        <span>Coordinators &amp; HR</span>
                                        <span class="cd-contact-subtab-badge" id="contactTabCountHrDirect">0</span>
                                    </button>
                                    <button type="button" class="cd-contact-subtab" data-filter="active" id="cdContactTabActiveDirect">
                                        <span>Active</span>
                                        <span class="cd-contact-subtab-badge" id="contactTabCountActiveDirect">{{ $client_contact_count ?? 0 }}</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Local Skeleton Loader Cards -->
                            <div id="cdContactSkeletonLoaderDirect" style="display:none;" class="py-2">
                                <div class="cd-contact-skeleton-card">
                                    <div class="cd-contact-skeleton-pulse" style="width: 25%; height: 50px;"></div>
                                    <div class="cd-contact-skeleton-pulse" style="width: 25%; height: 50px;"></div>
                                    <div class="cd-contact-skeleton-pulse" style="width: 20%; height: 50px;"></div>
                                    <div class="cd-contact-skeleton-pulse" style="width: 15%; height: 50px;"></div>
                                    <div class="cd-contact-skeleton-pulse" style="width: 15%; height: 50px;"></div>
                                </div>
                                <div class="cd-contact-skeleton-card">
                                    <div class="cd-contact-skeleton-pulse" style="width: 25%; height: 50px;"></div>
                                    <div class="cd-contact-skeleton-pulse" style="width: 25%; height: 50px;"></div>
                                    <div class="cd-contact-skeleton-pulse" style="width: 20%; height: 50px;"></div>
                                    <div class="cd-contact-skeleton-pulse" style="width: 15%; height: 50px;"></div>
                                    <div class="cd-contact-skeleton-pulse" style="width: 15%; height: 50px;"></div>
                                </div>
                            </div>

                            <!-- TIER 3: Modern Contact Cards Stream -->
                            <div class="cd-contact-stream-wrap">
                                <div id="cdContactCardsStreamDirect" class="cd-contact-stream"></div>
                            </div>

                            <!-- Empty State -->
                            <div id="cdContactEmptyStateDirect" class="cd-contact-empty-state" style="display:none;">
                                <div class="cd-contact-empty-icon">
                                    <i class="fas fa-address-book fa-solid"></i>
                                </div>
                                <div class="cd-contact-empty-title">No Contacts Found</div>
                                <div class="cd-contact-empty-sub">No contacts match the selected criteria or search term.</div>
                                <button type="button" class="btn btn-sm btn-light-primary fw-bold" id="cdContactClearFilterBtnDirect">
                                    <i class="fas fa-undo me-1"></i> Reset Filters
                                </button>
                            </div>

                        </div>
                    </div>
                    <!--begin::candidate_list Candidate List-->
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
                                            <span class="cd-cand-count-badge" id="cdCandidateCountBadgeDirect">
                                                <span id="cdCandidateCountTextDirect">0</span> Candidates
                                            </span>
                                        </div>
                                        <div class="cd-cand-subtitle">Pipeline and submitted talent for this direct client</div>
                                    </div>
                                </div>
                                <div class="cd-cand-toolbar-right">
                                    <!-- Search Candidates Sub-Card with Clear Button -->
                                    <div class="cd-cand-search-subcard">
                                        <i class="fas fa-search fa-solid"></i>
                                        <input type="text" id="cdCandSearchInputDirect" placeholder="Search direct candidates by name, designation, skills, company..." autocomplete="off">
                                        <button type="button" class="cd-cand-search-clear" id="cdCandSearchClearDirect" style="display: none;" title="Clear search">
                                            <i class="fas fa-times fa-solid"></i>
                                        </button>
                                    </div>
                                    <!-- Quick Refresh / Reset Button -->
                                    <button type="button" id="cdCandResetBtnDirect" class="cd-cand-action-subcard" title="Reset Filters &amp; Refresh">
                                        <i class="fas fa-sync-alt fa-solid"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- TIER 2: SUB-TABS PIPELINE CARD (Strict 8 Pipeline Stages) -->
                            <div class="cd-cand-tabs-card">
                                <div class="cd-cand-tabs-list" role="tablist">
                                    <button type="button" class="cd-cand-subtab cd-cand-subtab-direct active" data-stage="sourcing" data-status="1" role="tab" aria-selected="true" title="Sourcing Stage">
                                        <span>Sourcing</span>
                                        <span class="cd-cand-subtab-badge" id="candTabCountSourcingDirect">0</span>
                                    </button>
                                    <button type="button" class="cd-cand-subtab cd-cand-subtab-direct" data-stage="screening" data-status="2" role="tab" aria-selected="false" title="Screening Stage">
                                        <span>Screening</span>
                                        <span class="cd-cand-subtab-badge" id="candTabCountScreeningDirect">0</span>
                                    </button>
                                    <button type="button" class="cd-cand-subtab cd-cand-subtab-direct" data-stage="evaluation" data-status="3" role="tab" aria-selected="false" title="Evaluation Stage">
                                        <span>Evaluation</span>
                                        <span class="cd-cand-subtab-badge" id="candTabCountEvaluationDirect">0</span>
                                    </button>
                                    <button type="button" class="cd-cand-subtab cd-cand-subtab-direct" data-stage="negotiations" data-status="4" role="tab" aria-selected="false" title="Negotiations Stage">
                                        <span>Negotiations</span>
                                        <span class="cd-cand-subtab-badge" id="candTabCountNegotiationsDirect">0</span>
                                    </button>
                                    <button type="button" class="cd-cand-subtab cd-cand-subtab-direct" data-stage="offer" data-status="5" role="tab" aria-selected="false" title="Offer Stage">
                                        <span>Offer</span>
                                        <span class="cd-cand-subtab-badge" id="candTabCountOfferDirect">0</span>
                                    </button>
                                    <button type="button" class="cd-cand-subtab cd-cand-subtab-direct" data-stage="joined" data-status="6" role="tab" aria-selected="false" title="Joined Stage">
                                        <span>Joined</span>
                                        <span class="cd-cand-subtab-badge" id="candTabCountJoinedDirect">0</span>
                                    </button>
                                    <button type="button" class="cd-cand-subtab cd-cand-subtab-direct" data-stage="rejected" data-status="7" role="tab" aria-selected="false" title="Rejected Stage">
                                        <span>Rejected</span>
                                        <span class="cd-cand-subtab-badge" id="candTabCountRejectedDirect">0</span>
                                    </button>
                                    <button type="button" class="cd-cand-subtab cd-cand-subtab-direct" data-stage="interviews" data-status="interview" role="tab" aria-selected="false" title="Interview Activity">
                                        <span>Interviews</span>
                                        <span class="cd-cand-subtab-badge" id="candTabCountInterviewsDirect">0</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Local Skeleton Loader Cards -->
                            <div id="cdCandSkeletonLoaderDirect" style="display:none;" class="py-2">
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
                            <div id="cdCandEmptyStateDirect" class="text-center py-12" style="display:none; background:#ffffff; border-radius:10px; border:1px dashed #cbd5e1; margin:8px 0;">
                                <div class="symbol symbol-50px mx-auto mb-3">
                                    <span class="symbol-label bg-light-primary text-primary fs-2">
                                        <i class="fas fa-user-slash fa-solid"></i>
                                    </span>
                                </div>
                                <div class="text-dark fs-6 fw-bold mb-1">No candidates found</div>
                                <div class="text-gray-400 fs-7">Try adjusting your search query or pipeline filter.</div>
                            </div>

                            <!-- TIER 3: PRIMARY CANDIDATE CARD STREAM -->
                            <div class="cd-cand-stream-wrap" id="cdCandidateTableWrapDirect">
                                <div id="candidatetList" class="cd-cand-stream">
                                    <!-- Dynamic Candidate Cards (.cd-cand-card) rendered by ClientCandidateStoreDirect -->
                                </div>
                                <div id="paginationcandidate" class="d-flex align-items-center justify-content-between pt-4">
                                    <!-- Dynamic Pagination Links -->
                                </div>
                            </div>

                        </div>
                    </div>
                    <!--end::candidate_list Candidate List-->

                    <!--begin::interview_tracker Candidate List-->
                    <div class="tab-pane" id="interview_tracker" role="tabpanel">
                        <div class="cd-it-wrapper">
                            <!-- 1. Tier 1: Top Card Header Toolbar (Heading in Card + Search Bar) -->
                            <div class="cd-it-header-card">
                                <div class="cd-it-header-left">
                                    <div class="cd-it-header-icon">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="cd-it-header-title">Interviews</span>
                                            <span class="cd-it-header-badge" id="itTotalCountBadgeDirect">0</span>
                                        </div>
                                        <div class="cd-it-header-sub">Scheduled interviews with this client's candidates.</div>
                                    </div>
                                </div>
                                <div class="cd-it-header-right">
                                    <div class="cd-it-search-subcard">
                                        <i class="fas fa-search"></i>
                                        <input type="text" id="itSearchInputDirect" placeholder="Search candidate, job, interviewer, stage..." autocomplete="off">
                                        <button type="button" class="cd-it-search-clear" id="itSearchClearDirect" style="display: none;" title="Clear search">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    @if (in_array('add_interview_schedule', $userPermissions ?? []))
                                        <button type="button" class="btn btn-sm btn-primary fw-bold cd-it-add-btn" id="btnOpenScheduleModal">
                                            <i class="fas fa-plus me-1"></i> Schedule Interview
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <!-- 2. Tier 2: Sub-Tabs Filter Card (Single Active Highlight Rule) -->
                            <div class="cd-it-tabs-card">
                                <button type="button" class="cd-it-tab-btn active js-it-filter-btn" data-filter="all">
                                    <span>All Interviews</span>
                                    <span class="cd-it-tab-badge" id="itFilterCountAll">0</span>
                                </button>
                                <button type="button" class="cd-it-tab-btn js-it-filter-btn" data-filter="upcoming">
                                    <span>Upcoming / Today</span>
                                    <span class="cd-it-tab-badge badge-primary" id="itFilterCountUpcoming">0</span>
                                </button>
                                <button type="button" class="cd-it-tab-btn js-it-filter-btn" data-filter="pending_feedback">
                                    <span>Feedback Pending</span>
                                    <span class="cd-it-tab-badge badge-warning" id="itFilterCountPending">0</span>
                                </button>
                                <button type="button" class="cd-it-tab-btn js-it-filter-btn" data-filter="completed">
                                    <span>Completed / Attended</span>
                                    <span class="cd-it-tab-badge badge-success" id="itFilterCountCompleted">0</span>
                                </button>
                                <button type="button" class="cd-it-tab-btn js-it-filter-btn" data-filter="cancelled">
                                    <span>Cancelled / Rescheduled</span>
                                    <span class="cd-it-tab-badge" id="itFilterCountCancelled">0</span>
                                </button>
                            </div>

                            <!-- 3. Tier 3: Primary Cards List -->
                            <div id="candidatetListinterviewtracker" class="cd-it-stream" data-role="interview-list">
                            </div>

                            <!-- 4. Tier 4: Enterprise Pagination Card -->
                            <div id="paginationcandidateinterviewtracker" class="cd-it-pagination-card" data-role="interview-pagination">
                            </div>
                        </div>
                    </div>
                    <!--end::interview_tracker Candidate List-->

                    <!--begin::candidate_joining Candidate List-->
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
                                            <span class="cd-oj-header-badge" id="cdOjCountBadgeDirect">
                                                <span id="cdOjCountTextDirect">{{ ($kpiOffers ?? 0) + ($kpiJoins ?? 0) }}</span> Offers &amp; Joinings
                                            </span>
                                        </div>
                                        <div class="cd-oj-header-sub">Track direct candidate job offers, joining confirmations and onboardings.</div>
                                    </div>
                                </div>
                                <div class="cd-oj-header-right">
                                    <div class="cd-oj-search-subcard">
                                        <i class="fas fa-search"></i>
                                        <input type="text" id="ojSearchInputDirect" placeholder="Search candidate, job, CTC, status..." autocomplete="off">
                                        <button type="button" class="cd-oj-search-clear" id="ojSearchClearDirect" style="display: none;" title="Clear search">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <button type="button" id="ojResetBtnDirect" class="cd-oj-action-subcard" title="Reset Filters &amp; Refresh">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- TIER 2: SUB-TABS CARD WITH WHITE BACKGROUND -->
                            <div class="cd-oj-tabs-card">
                                <div class="cd-oj-tabs-group" id="ojSegmentGroupDirect">
                                    <button type="button" class="cd-oj-tab-btn active cd-oj-seg-btn-direct" data-segment="all">
                                        <span>All Records</span>
                                        <span class="cd-oj-tab-badge" id="ojCountAllDirect">{{ ($kpiOffers ?? 0) + ($kpiJoins ?? 0) }}</span>
                                    </button>
                                    <button type="button" class="cd-oj-tab-btn cd-oj-seg-btn-direct" data-segment="offers">
                                        <span class="cd-oj-dot dot-warning"></span>
                                        <span>Offers Extended</span>
                                        <span class="cd-oj-tab-badge badge-warning" id="ojCountOffersDirect">{{ $kpiOffers ?? 0 }}</span>
                                    </button>
                                    <button type="button" class="cd-oj-tab-btn cd-oj-seg-btn-direct" data-segment="joining">
                                        <span class="cd-oj-dot dot-primary"></span>
                                        <span>Joining Soon</span>
                                        <span class="cd-oj-tab-badge badge-primary" id="ojCountJoiningDirect">0</span>
                                    </button>
                                    <button type="button" class="cd-oj-tab-btn cd-oj-seg-btn-direct" data-segment="joined">
                                        <span class="cd-oj-dot dot-success"></span>
                                        <span>Joined &amp; Onboarded</span>
                                        <span class="cd-oj-tab-badge badge-success" id="ojCountJoinedDirect">{{ $kpiJoins ?? 0 }}</span>
                                    </button>
                                </div>
                            </div>

                            <!-- TIER 3: PRIMARY CARDS LIST STREAM (Populated via AJAX) -->
                            <div class="cd-oj-stream-wrap">
                                <div id="candidatetListJoin" class="cd-oj-stream" data-role="offer-joining-list">
                                </div>
                            </div>
                            <div id="paginationcandidatejoin" class="mt-3">
                            </div>
                        </div>
                    </div>
                    <!--end::candidate_joining Candidate List-->

                <!--begin::candidateDashboard Content-->
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
                              <div class="cd-perf-subtitle">Real-time hiring &amp; recruitment performance analytics for this client</div>
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
                          <div class="cd-perf-kpi-card cd-nav-tab" data-target="#candidate_list">
                            <div class="cd-perf-kpi-top">
                              <div class="cd-perf-kpi-icon cd-perf-kpi-icon-green">
                                <i class="fas fa-users fa-solid"></i>
                              </div>
                              <span class="cd-perf-pill cd-perf-pill-up" id="perfKpiCandPill"><i class="fas fa-arrow-up fa-solid" style="font-size:8px;"></i>0.0%</span>
                            </div>
                            <div class="cd-perf-kpi-num" id="perfKpiCandidates">—</div>
                            <div class="cd-perf-kpi-lbl">Total Candidates</div>
                            <div class="cd-perf-kpi-sub">Submitted to this client</div>
                            <div class="cd-perf-kpi-comp" id="perfKpiCandComp">vs. previous period</div>
                          </div>

                          <!-- KPI 2: Interviews Scheduled -->
                          <div class="cd-perf-kpi-card cd-nav-tab" data-target="#interview_tracker">
                            <div class="cd-perf-kpi-top">
                              <div class="cd-perf-kpi-icon cd-perf-kpi-icon-blue">
                                <i class="fas fa-calendar-alt fa-solid fa-calendar-days"></i>
                              </div>
                              <span class="cd-perf-pill cd-perf-pill-up" id="perfKpiIntPill"><i class="fas fa-arrow-up fa-solid" style="font-size:8px;"></i>0.0%</span>
                            </div>
                            <div class="cd-perf-kpi-num" id="perfKpiInterviews">—</div>
                            <div class="cd-perf-kpi-lbl">Interviews Scheduled</div>
                            <div class="cd-perf-kpi-sub">Across all rounds</div>
                            <div class="cd-perf-kpi-comp" id="perfKpiIntComp">vs. previous period</div>
                          </div>

                          <!-- KPI 3: Offers Generated -->
                          <div class="cd-perf-kpi-card cd-nav-tab" data-target="#candidate_joining">
                            <div class="cd-perf-kpi-top">
                              <div class="cd-perf-kpi-icon cd-perf-kpi-icon-amber">
                                <i class="fas fa-file-alt fa-solid fa-file-lines"></i>
                              </div>
                              <span class="cd-perf-pill cd-perf-pill-up" id="perfKpiOfferPill"><i class="fas fa-arrow-up fa-solid" style="font-size:8px;"></i>0.0%</span>
                            </div>
                            <div class="cd-perf-kpi-num" id="perfKpiOffers">—</div>
                            <div class="cd-perf-kpi-lbl">Offers Generated</div>
                            <div class="cd-perf-kpi-sub">Offer letters sent</div>
                            <div class="cd-perf-kpi-comp" id="perfKpiOfferComp">vs. previous period</div>
                          </div>

                          <!-- KPI 4: Successful Joins -->
                          <div class="cd-perf-kpi-card cd-nav-tab" data-target="#candidate_joining">
                            <div class="cd-perf-kpi-top">
                              <div class="cd-perf-kpi-icon cd-perf-kpi-icon-purple">
                                <i class="fas fa-user-check fa-solid"></i>
                              </div>
                              <span class="cd-perf-pill cd-perf-pill-up" id="perfKpiJoinPill"><i class="fas fa-arrow-up fa-solid" style="font-size:8px;"></i>0.0%</span>
                            </div>
                            <div class="cd-perf-kpi-num" id="perfKpiJoins">—</div>
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
                                <!-- Tier 1: Candidates Submitted (Blue) -->
                                <path d="M 4,4 L 96,4 L 84,32 L 16,32 Z" fill="#3b82f6" />
                                <!-- Tier 2: Screened (Indigo) -->
                                <path d="M 17,36 L 83,36 L 73,64 L 27,64 Z" fill="#6366f1" />
                                <!-- Tier 3: Interviews (Purple) -->
                                <path d="M 28,68 L 72,68 L 63,96 L 37,96 Z" fill="#8b5cf6" />
                                <!-- Tier 4: Offers (Amber) -->
                                <path d="M 38,100 L 62,100 L 55,116 L 45,116 Z" fill="#f59e0b" />
                                <!-- Tier 5: Joined (Green) -->
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
                              <a href="javascript:void(0);" class="cd-card-hdr-link cd-nav-tab" data-target="#requisitions">View All Jobs <i class="fas fa-arrow-right fa-solid ms-1" style="font-size:8.5px;"></i></a>
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
                              <a href="javascript:void(0);" class="cd-card-hdr-link cd-nav-tab" data-target="#candidate_list">View All Recruiters <i class="fas fa-arrow-right fa-solid ms-1" style="font-size:8.5px;"></i></a>
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
                <!--end begin::candidateDashboard Content-->
                

                    <div class="tab-pane" id="requisitions" role="tabpanel">
                        <!-- 1. Top Header Card with Dynamic Search Bar at Right Corner -->
                        <div class="cd-jobs-header-card">
                            <div class="cd-jobs-header-left">
                                <div class="cd-jobs-header-icon">
                                    <i class="fas fa-briefcase"></i>
                                </div>
                                <div>
                                    <div class="cd-jobs-header-title">Jobs</div>
                                    <div class="cd-jobs-header-sub">Roles open with this client.</div>
                                </div>
                            </div>
                            <div class="cd-jobs-header-right">
                                <div class="cd-jobs-search-subcard">
                                    <i class="fas fa-search"></i>
                                    <input type="text" id="inputJobSearch" name="inputJobSearch" placeholder="Search jobs by title, skills, ID..." autocomplete="off">
                                    <button type="button" class="cd-jobs-search-clear" id="btnJobSearchClear" style="display: none;" title="Clear search">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                @if (in_array('add_jobs', $userPermissions))
                                    <a href="{{ url('service_company/jobs/create') }}" class="btn btn-sm btn-primary fw-bold cd-jobs-add-btn">
                                        <i class="fas fa-plus me-1"></i> Add Jobs
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="cd-tabwrap">
                            @if (in_array('view_jobs', $userPermissions))
                                <div class="jobx">
                                    <!-- Tier 2: Sub-Tabs in Card-Based Design with White Background -->
                                    <div class="cd-jobs-tabs-card">
                                        <div class="cd-jobs-tabs-group">
                                            <button type="button" 
                                                    class="cd-jobs-tab-btn js-job-filter-btn active" 
                                                    data-status="all">
                                                <span>All Jobs</span>
                                                <span class="cd-jobs-tab-badge" id="directFilterCountAll">{{ $kpiActive ?? 0 }}</span>
                                            </button>
                                            <button type="button" 
                                                    class="cd-jobs-tab-btn js-job-filter-btn" 
                                                    data-status="hiring">
                                                <span>Hiring</span>
                                                <span class="cd-jobs-tab-badge badge-hiring" id="directFilterCountHiring">{{ $kpiActive ?? 0 }}</span>
                                            </button>
                                            <button type="button" 
                                                    class="cd-jobs-tab-btn js-job-filter-btn" 
                                                    data-status="hold">
                                                <span>On Hold</span>
                                                <span class="cd-jobs-tab-badge badge-hold" id="directFilterCountHold">0</span>
                                            </button>
                                            <button type="button" 
                                                    class="cd-jobs-tab-btn js-job-filter-btn" 
                                                    data-status="closed">
                                                <span>Closed</span>
                                                <span class="cd-jobs-tab-badge badge-closed" id="directFilterCountClosed">0</span>
                                            </button>
                                            <button type="button" 
                                                    class="cd-jobs-tab-btn js-job-filter-btn" 
                                                    data-status="inactive">
                                                <span>Inactive</span>
                                                <span class="cd-jobs-tab-badge badge-inactive" id="directFilterCountInactive">0</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Tier 3: Card-Based Jobs Stream -->
                                    <div id="directclientJobsListajax" class="cd-jobs-stream"></div>
                                </div>
                                <div id="directpaginationclientjobs" class="mt-3"></div>
                            @else
                                <div class="cd-empty-card p-5 text-center text-muted">
                                    <i class="fas fa-lock fs-2 mb-2 text-muted"></i>
                                    <div>You do not have permission to view jobs for this client.</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Body-->
        </div>
        <!--end:Tabs-->
    </div>
    <!--end::Content-->
    <div class="cd-drawer-footer">
        Created on {{ $clientCreatedAt }} <span class="mx-1">|</span> Last updated {{ $clientUpdatedAt }}
    </div>
</div>
<!--end::Body-->
<!--begin::Footer-->
<div class="card-footer py-5 text-center d-none" id="kt_activities_footer"> <a
        href="../../demo1/dist/pages/profile/activity.html" class="btn btn-bg-body text-primary">View All Activities
        <!--begin::Svg Icon | path: icons/duotone/Navigation/Right-2.svg-->
        <span class="svg-icon svg-icon-3 svg-icon-primary">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                height="24px" viewBox="0 0 24 24" version="1.1">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <polygon points="0 0 24 0 24 24 0 24" />
                    <rect fill="#000000" opacity="0.5"
                        transform="translate(8.500000, 12.000000) rotate(-90.000000) translate(-8.500000, -12.000000)"
                        x="7.5" y="7.5" width="2" height="9" rx="1" />
                    <path
                        d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z"
                        fill="#000000" fill-rule="nonzero"
                        transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)" />
                </g>
            </svg>
        </span>
        <!--end::Svg Icon-->
    </a> </div>
<!--end::Footer-->
</div>

<script>
    // ── Global Reactive Client Performance Analytics Engine (Direct Client Scope) ──
    window.perfChartInstances = window.perfChartInstances || {};
    window.perfClientCache = window.perfClientCache || {};
    window.perfCurrentRange = '12M';
    window.perfCurrentCompare = 'previous_period';
    window.perfIsLoading = false;
    var perfClientId = "{{ $dc_id }}";
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
        $('#perfDateRangeSelect, .perf-sync-select').val(val);
        cdPerfFetchData(val, window.perfCurrentCompare, false);
    }

    function cdPerfSyncCompare(val) {
        if (!val) return;
        window.perfCurrentCompare = val;
        $('#perfCompareWithSelect').val(val);
        cdPerfFetchData(window.perfCurrentRange, val, false);
    }

    function cdPerfRefresh() {
        var $btn = $('#btnPerfRefresh i');
        $btn.addClass('fa-spin');
        var cacheKey = perfClientId + '_' + window.perfCurrentRange + '_' + window.perfCurrentCompare;
        delete window.perfClientCache[cacheKey];
        cdPerfFetchData(window.perfCurrentRange, window.perfCurrentCompare, true, function() {
            $btn.removeClass('fa-spin');
        });
    }

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
                client_type: 'direct',
                timezone: Intl.DateTimeFormat().resolvedOptions().timeZone || 'Asia/Kolkata',
            },
            success: function(res) {
                window.perfIsLoading = false;
                window.perfClientCache[cacheKey] = res;
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

    function cdPerfRender(data) {
        if (!data || !data.summary) return;

        var s = data.summary;

        // 1. Update 5 Hero KPI Cards
        $('#perfKpiCandidates').text(s.candidates.value.toLocaleString());
        cdPerfUpdatePill($('#perfKpiCandPill'), s.candidates.delta_label, s.candidates.delta_value, false);
        $('#perfKpiCandComp').text('vs. ' + (data.meta.compare === 'none' ? 'none' : 'prior period'));

        $('#perfKpiInterviews').text(s.interviews_scheduled.value.toLocaleString());
        cdPerfUpdatePill($('#perfKpiIntPill'), s.interviews_scheduled.delta_label, s.interviews_scheduled.delta_value, false);
        $('#perfKpiIntComp').text('vs. ' + (data.meta.compare === 'none' ? 'none' : 'prior period'));

        $('#perfKpiOffers').text(s.offers.value.toLocaleString());
        cdPerfUpdatePill($('#perfKpiOfferPill'), s.offers.delta_label, s.offers.delta_value, false);
        $('#perfKpiOfferComp').text('vs. ' + (data.meta.compare === 'none' ? 'none' : 'prior period'));

        $('#perfKpiJoins').text(s.joins.value.toLocaleString());
        cdPerfUpdatePill($('#perfKpiJoinPill'), s.joins.delta_label, s.joins.delta_value, false);
        $('#perfKpiJoinComp').text('vs. ' + (data.meta.compare === 'none' ? 'none' : 'prior period'));

        var tthFormatted = s.avg_time_to_hire_days.formatted || '—';
        $('#perfKpiTimeToHire').text(tthFormatted);
        cdPerfUpdatePill($('#perfKpiTimePill'), s.avg_time_to_hire_days.delta_label, s.avg_time_to_hire_days.delta_value, true);
        $('#perfKpiTimeComp').text(s.avg_time_to_hire_days.placed_count ? (s.avg_time_to_hire_days.placed_count + ' placements') : 'vs. prior period');

        // 2. Empty State Banner
        var $banner = $('#perfEmptyStateBanner');
        if (data.empty_state) {
            $('#perfEmptyStateTitle').text(data.empty_state.title + ':');
            $('#perfEmptyStateMessage').text(data.empty_state.message);
            $banner.slideDown(150);
        } else {
            $banner.slideUp(150);
        }

        // 3. Funnel Stages
        if (data.funnel && data.funnel.stages) {
            var st = data.funnel.stages;
            if (st[0]) { $('#funnelCountCand').text(st[0].count); $('#funnelPctCand').text(st[0].conversion_pct); }
            if (st[1]) { $('#funnelCountScreened').text(st[1].count); $('#funnelPctScreened').text(st[1].conversion_pct); }
            if (st[2]) { $('#funnelCountInt').text(st[2].count); $('#funnelPctInt').text(st[2].conversion_pct); }
            if (st[3]) { $('#funnelCountOff').text(st[3].count); $('#funnelPctOff').text(st[3].conversion_pct); }
            if (st[4]) { $('#funnelCountJoin').text(st[4].count); $('#funnelPctJoin').text(st[4].conversion_pct); }
        }

        // 4. Intelligence Grid
        var vVal = s.avg_time_to_hire_days.formatted !== '—' ? s.avg_time_to_hire_days.formatted : 'Active';
        $('#perfIntelVelocity').text(vVal);

        var intConv = s.candidates.value > 0 ? ((s.interviewed.value / s.candidates.value) * 100).toFixed(1) + '%' : '0.0%';
        $('#perfIntelIntConv').text(intConv);
        $('#perfIntelIntConvSub').text(s.interviewed.value + ' of ' + s.candidates.value + ' candidates');

        var offConv = s.interviewed.value > 0 ? ((s.offers.value / s.interviewed.value) * 100).toFixed(1) + '%' : '0.0%';
        $('#perfIntelOfferConv').text(offConv);
        $('#perfIntelOfferConvSub').text(s.offers.value + ' of ' + s.interviewed.value + ' interviews');

        var yieldVal = data.funnel && data.funnel.overall_yield_pct ? data.funnel.overall_yield_pct : '0.0%';
        $('#perfIntelJoinRatio').text(yieldVal);
        $('#perfIntelJoinRatioSub').text(s.joins.value + ' placed from ' + s.candidates.value + ' submitted');

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
                recHtml += '<tr>' +
                    '<td>' + (idx + 1) + '</td>' +
                    '<td><span class="cd-recruiter-avatar" style="background:#ecfdf5; color:#059669;">' + initials + '</span>' + rec.name + '</td>' +
                    '<td>' + rec.submissions + '</td>' +
                    '<td>' + rec.interviews + '</td>' +
                    '<td>' + rec.offers + '</td>' +
                    '<td>' + rec.joins + '</td>' +
                    '<td><div class="cd-perf-progress-wrap"><span>' + rec.join_rate_pct + '</span><div class="cd-perf-progress-bar"><div class="cd-perf-progress-fill" style="width:' + Math.min(100, pctVal) + '%;"></div></div></div></td>' +
                    '</tr>';
            });
        } else {
            recHtml = '<tr><td colspan="7" class="text-center py-3 text-muted">No assigned recruiters recorded for this client.</td></tr>';
        }
        $('#perfRecruitersTbody').html(recHtml);

        // 8. Render Reactive Chart.js Visualizations
        ensureChartJsLoaded(function() {
            cdPerfRenderCharts(data);
        });
    }

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

        // 5. Line: Time to Hire Trend
        getOrCreateChart('chartTimeToHire', {
            type: 'line',
            data: {
                labels: v.labels,
                datasets: [{
                    label: 'Days to Hire',
                    data: v.labels.map(function() { return data.summary.avg_time_to_hire_days.value || 30; }),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.08)',
                    borderWidth: 2,
                    fill: true,
                    pointRadius: 2.5,
                    tension: 0.35
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 9.5 } } },
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 9.5 } } }
                }
            }
        });
    }

    function initPerformanceDashboard() {
        cdPerfFetchData(window.perfCurrentRange, window.perfCurrentCompare, false);
    }
    window.initPerformanceDashboard = initPerformanceDashboard;
    window.fetchDirectClientDashboardAnalytics = initPerformanceDashboard;
</script>


    
<script>


   // alert("hi");
    var table = $('#contactList').dataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        destroy: true,
        ordering: false, //disable ordering			
        "bLengthChange": false, //thought this line could hide the LengthMenu
        sDom: 'lrtip',
        oLanguage: {
            sLengthMenu: "_MENU_",
        },
        ajax: {
            'url': '{!! route('service_company.clients.contactdata') !!}?company=' + {{ @$client->id }},
            "data": function(d) {
                d.search = $('#inputSearch').val()
            }
        },
        "drawCallback": function(settings) {
            $('[data-toggle="tooltip1"]').tooltip();
            $('[data-toggle="tooltip2"]').tooltip();
        },
        columns: [
            { data: 'chkbox', name: 'chkbox', searchable: false, orderable: false },
            { data: 'Contact', name: 'Contact', searchable: false, orderable: false },
            { data: 'Designation', name: 'Designation', searchable: false, orderable: false },
            { data: 'Email', name: 'Email', searchable: false, orderable: false },
            { data: 'Phone', name: 'Phone', searchable: false, orderable: false },
            { data: 'Status', name: 'Status', searchable: false, orderable: false },
            { data: 'Actions', name: 'Actions', searchable: false, orderable: false }
        ]
    });
    new $.fn.dataTable.FixedHeader(table);

    

</script>
<script>
    // var table = $('#allreqsss').dataTable({

    //     responsive: true,
    //     processing: true,
    //     serverSide: true,
    //     destroy: true,
    //     ordering: false, //disable ordering
    //     "bLengthChange": false, //thought this line could hide the LengthMenu
    //     sDom: 'lrtip',
    //     oLanguage: {
    //         sLengthMenu: "_MENU_",
    //     },
    //     "pageLength": 10,
    //     colReorder: true,
    //     ajax: {
    //         'url': '{!! route('service_company.jobs.data') !!}',
    //         "data": function(d) {
    //             d.search = $('#inputSearch').val(), d.client_id = $('#direct_client_id').val(), d.custom =
    //                 false
    //         }
    //     },
    //     // ajax			: {'url' : '{!! route('service_company.jobs.all_data') !!}', "data": function ( d ) { d.search =	$('#inputSearch').val(), d.client_id =	$('#direct_client_id').val(), d.sharejob =	'sharejob' } },	
    //     "drawCallback": function(settings) {
    //         $('[data-toggle="tooltip1"]').tooltip();
    //         $('[data-toggle="tooltip2"]').tooltip();
    //         $('[data-toggle="tooltip3"]').tooltip();
    //         $('[data-toggle="tooltip4"]').tooltip();
    //     },
    //     "columnDefs": [{
    //             "width": "50px",
    //             "targets": 0
    //         },
    //         {
    //             "width": "100px",
    //             "targets": 1,
    //             "class": "d-flex align-items-center border-0"
    //         },
    //         {
    //             "width": "50px",
    //             "targets": 2
    //         },
    //         {
    //             "width": "50px",
    //             "targets": 3
    //         },
    //         {
    //             "width": "200px",
    //             "targets": 4
    //         },
    //         {
    //             "width": "50px",
    //             "targets": 5
    //         },
    //         {
    //             "width": "200px",
    //             "targets": 6
    //         },
    //         {
    //             "width": "150px",
    //             "targets": 7
    //         },
    //         {
    //             "width": "150px",
    //             "targets": 8
    //         },

    //         {
    //             "width": "20px",
    //             "targets": 9
    //         },
    //         {
    //             "width": "20px",
    //             "targets": 10
    //         },
    //         {
    //             "width": "20px",
    //             "targets": 11
    //         },

    //         {
    //             "width": "20px",
    //             "targets": 12
    //         },
    //         {
    //             "width": "20px",
    //             "targets": 13
    //         },
    //         {
    //             "width": "20px",
    //             "targets": 14
    //         },
    //         {
    //             "width": "20px",
    //             "targets": 15
    //         },
    //         {
    //             "width": "20px",
    //             "targets": 16
    //         }
    //     ],
    //     columns: [{
    //             data: 'chkbox',
    //             name: 'chkbox',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'Requisition',
    //             name: 'Requisition',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'Exp',
    //             name: 'Exp',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'Salary',
    //             name: 'Salary',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'Location',
    //             name: 'Location',
    //             orderable: false,
    //             searchable: false
    //         },

    //         {
    //             data: 'Status',
    //             name: 'Status',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'Date',
    //             name: 'Date',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'HM',
    //             name: 'HM',
    //             orderable: false,
    //             searchable: false
    //         },

    //         {
    //             data: 'Partners',
    //             name: 'Partners',
    //             orderable: false,
    //             searchable: false
    //         },

    //         {
    //             data: 'TOT',
    //             name: 'TOT',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'SOU',
    //             name: 'SOU',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'SCR',
    //             name: 'SCR',
    //             orderable: false,
    //             searchable: false
    //         },

    //         {
    //             data: 'EVA',
    //             name: 'EVA',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'NEO',
    //             name: 'NEO',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'OFF',
    //             name: 'OFF',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'JOI',
    //             name: 'JOI',
    //             orderable: false,
    //             searchable: false
    //         },
    //         {
    //             data: 'REJ',
    //             name: 'REJ',
    //             orderable: false,
    //             searchable: false
    //         }

    //     ]
    // });
    // new $.fn.dataTable.FixedHeader(table);
</script>
<script>
    /**
     * ── DirectClientContactsManager (Client-First RAM & Card Stream Engine) ──
     * God-Level Modern Card + Sub-Card Enterprise Implementation.
     * 100% Client-First Local Filtering, 300ms Debounce, Copy Actions, Zero Horizontal Overflow.
     */
    window.DirectClientContactsManager = {
        companyId: {{ $directClientDetail->company_id ?? $directClientDetail->from_company_id ?? $directClientDetail->companies_id ?? $directClientDetail->client_id ?? 0 }},
        clientName: '{{ addslashes($clientTitle ?? @$directClientDetail->name ?? "Direct Client") }}',
        allContacts: @json($client_contact_arr ?? []),
        filteredContacts: [],
        activeFilter: 'all',
        searchTerm: '',
        searchDebounceTimer: null,
        isInitialized: false,
        isLoading: false,

        avatarPalette: ['#059669', '#2563eb', '#7c3aed', '#d97706', '#db2777', '#0284c7', '#4f46e5', '#0d9488', '#ea580c'],

        init: function(companyId) {
            if (companyId) {
                var cid = parseInt(companyId, 10);
                if (this.companyId && parseInt(this.companyId, 10) !== cid) {
                    this.companyId = cid;
                    this.allContacts = [];
                } else {
                    this.companyId = cid;
                }
            }
            this.bindEvents();
            this.isInitialized = true;
            if (Array.isArray(this.allContacts) && this.allContacts.length > 0) {
                this.updateCounts(this.allContacts);
                this.filterAndRender();
            } else {
                this.fetch();
            }
        },

        bindEvents: function() {
            var self = this;

            // Clean unbind previous namespaced listeners
            $(document).off('.directDrawerContacts');

            // 1. Debounced live search
            $(document).on('input.directDrawerContacts', '#cdContactSearchInputDirect', function() {
                var val = $(this).val();
                if (val && val.length > 0) {
                    $('#cdContactSearchClearDirect').show();
                } else {
                    $('#cdContactSearchClearDirect').hide();
                }
                clearTimeout(self.searchDebounceTimer);
                self.searchDebounceTimer = setTimeout(function() {
                    self.searchTerm = (val || '').trim().toLowerCase();
                    self.filterAndRender();
                }, 300);
            });

            // 2. Interactive clear search button
            $(document).on('click.directDrawerContacts', '#cdContactSearchClearDirect', function() {
                $('#cdContactSearchInputDirect').val('');
                $(this).hide();
                self.searchTerm = '';
                self.filterAndRender();
            });

            // 3. Reset filters & refresh button
            $(document).on('click.directDrawerContacts', '#cdContactResetBtnDirect, #cdContactClearFilterBtnDirect', function() {
                $('#cdContactSearchInputDirect').val('');
                $('#cdContactSearchClearDirect').hide();
                self.searchTerm = '';
                self.activeFilter = 'all';
                $('.cd-contact-subtab').removeClass('active');
                $('#cdContactTabAllDirect').addClass('active');
                self.fetch();
            });

            // 4. Sub-tab filter buttons
            $(document).on('click.directDrawerContacts', '#kt_activitiesDirectClient .cd-contact-tabs-card .cd-contact-subtab', function(e) {
                if (e && typeof e.preventDefault === 'function') e.preventDefault();
                var filter = $(this).data('filter') || 'all';
                $('#kt_activitiesDirectClient .cd-contact-tabs-card .cd-contact-subtab').removeClass('active');
                $(this).addClass('active');
                self.activeFilter = filter;
                if (window.__activeClientDrawer && window.__activeClientDrawer.coordinator) {
                    window.__activeClientDrawer.coordinator.setFilter('contacts', filter);
                }
                if (Array.isArray(self.allContacts) && self.allContacts.length > 0) {
                    self.filterAndRender();
                } else {
                    self.fetch();
                }
            });

            // 5. One-click copy with visual tooltip / checkmark
            $(document).on('click.directDrawerContacts', '#kt_activitiesDirectClient .js-copy-data', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var text = $(this).data('copy') || $(this).attr('data-copy');
                if (!text) return;
                var $btn = $(this);
                var origHtml = $btn.html();

                var showSuccess = function() {
                    $btn.html('<i class="fas fa-check text-success" style="font-size:10px;"></i>');
                    setTimeout(function() { $btn.html(origHtml); }, 1400);
                };

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text).then(showSuccess).catch(function() {
                        self.fallbackCopy(text, showSuccess);
                    });
                } else {
                    self.fallbackCopy(text, showSuccess);
                }
            });

            // 6. View Requisitions button: switch to #requisitions tab
            $(document).on('click.directDrawerContacts', '#kt_activitiesDirectClient .js-contact-view-jobs', function(e) {
                e.preventDefault();
                if (typeof window.loadDirectTabContent === 'function') {
                    window.loadDirectTabContent('jobs', false);
                } else {
                    $('a[href="#requisitions"]').tab('show');
                }
            });
        },

        fallbackCopy: function(text, cb) {
            var textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-9999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                if (typeof cb === 'function') cb();
            } catch (err) {}
            document.body.removeChild(textArea);
        },

        fetch: function() {
            var self = this;
            this.isLoading = true;
            if (!this.allContacts || this.allContacts.length === 0) {
                $('#cdContactSkeletonLoaderDirect').show();
                $('#cdContactCardsStreamDirect').hide();
                $('#cdContactEmptyStateDirect').hide();
            }

            var companyId = this.companyId;
            $.ajax({
                url: '{!! route('service_company.clients.dcontactList') !!}?company_id=' + companyId,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    self.isLoading = false;
                    $('#cdContactSkeletonLoaderDirect').hide();
                    $('#cdContactCardsStreamDirect').show();

                    var list = (res && res.data && Array.isArray(res.data)) ? res.data : [];
                    self.allContacts = list;
                    self.updateCounts(list);
                    self.filterAndRender();
                },
                error: function() {
                    self.isLoading = false;
                    $('#cdContactSkeletonLoaderDirect').hide();
                    $('#cdContactCardsStreamDirect').show();
                    if (!self.allContacts || self.allContacts.length === 0) {
                        self.allContacts = [];
                        self.updateCounts([]);
                        self.filterAndRender();
                    }
                }
            });
        },

        updateCounts: function(list) {
            var total = list.length;
            var primaryCount = 0;
            var managerCount = 0;
            var hrCount = 0;
            var activeCount = 0;

            var managerKeywords = ['manager', 'lead', 'head', 'vp', 'director', 'chief', 'principal', 'president', 'officer', 'lead'];
            var hrKeywords = ['hr', 'recruiter', 'recruiting', 'talent', 'sourcer', 'coordinator', 'people', 'admin'];

            list.forEach(function(item, idx) {
                var desig = (item.contact_person_designation || item.Designation || item.display_name || '').toLowerCase();
                var isPrimary = (item.is_primary == 1) || (idx === 0);
                if (isPrimary) primaryCount++;

                var isMgr = managerKeywords.some(function(k) { return desig.indexOf(k) !== -1; });
                if (isMgr) managerCount++;

                var isHr = hrKeywords.some(function(k) { return desig.indexOf(k) !== -1; });
                if (isHr) hrCount++;

                var status = (item.status || item.Status || 'active').toLowerCase();
                if (status.indexOf('active') !== -1) activeCount++;
            });

            $('#cdContactCountTextDirect').text(total);
            $('#contactTabCountAllDirect').text(total);
            $('#contactTabCountPrimaryDirect').text(primaryCount);
            $('#contactTabCountManagerDirect').text(managerCount);
            $('#contactTabCountHrDirect').text(hrCount);
            $('#contactTabCountActiveDirect').text(activeCount);

            // Update main tab badge
            var $navBadge = $('#cdContactNavBadgeDirect');
            if ($navBadge.length) {
                $navBadge.text(total);
                if (total > 0) $navBadge.show(); else $navBadge.hide();
            }
        },

        filterAndRender: function() {
            var self = this;
            var list = this.allContacts.slice();

            var managerKeywords = ['manager', 'lead', 'head', 'vp', 'director', 'chief', 'principal', 'president', 'officer'];
            var hrKeywords = ['hr', 'recruiter', 'recruiting', 'talent', 'sourcer', 'coordinator', 'people', 'admin'];

            // 1. Sub-tab filter
            if (this.activeFilter === 'primary') {
                list = list.filter(function(item, idx) {
                    return (item.is_primary == 1) || (idx === 0);
                });
            } else if (this.activeFilter === 'manager') {
                list = list.filter(function(item) {
                    var desig = (item.contact_person_designation || item.Designation || item.display_name || '').toLowerCase();
                    return managerKeywords.some(function(k) { return desig.indexOf(k) !== -1; });
                });
            } else if (this.activeFilter === 'hr') {
                list = list.filter(function(item) {
                    var desig = (item.contact_person_designation || item.Designation || item.display_name || '').toLowerCase();
                    return hrKeywords.some(function(k) { return desig.indexOf(k) !== -1; });
                });
            } else if (this.activeFilter === 'active') {
                list = list.filter(function(item) {
                    var status = (item.status || item.Status || 'active').toLowerCase();
                    return status.indexOf('active') !== -1;
                });
            }

            // 2. Search query filter
            if (this.searchTerm) {
                var q = this.searchTerm;
                list = list.filter(function(item) {
                    var name = (item.contact_person_name || item.name || '').toLowerCase();
                    var desig = (item.contact_person_designation || item.Designation || item.display_name || '').toLowerCase();
                    var email = (item.contact_person_email || item.email || '').toLowerCase();
                    var phone = (item.clean_phone || item.contact_person_number || item.mobile || '').toLowerCase();
                    var client = (item.client_name || self.clientName || '').toLowerCase();
                    return name.indexOf(q) !== -1 || desig.indexOf(q) !== -1 || email.indexOf(q) !== -1 || phone.indexOf(q) !== -1 || client.indexOf(q) !== -1;
                });
            }

            this.filteredContacts = list;

            var $stream = $('#cdContactCardsStreamDirect');
            var $empty = $('#cdContactEmptyStateDirect');

            if (!list || list.length === 0) {
                $stream.empty().hide();
                $empty.show();
                return;
            }

            $empty.hide();
            var html = '';
            list.forEach(function(item, idx) {
                html += self.renderCard(item, idx);
            });
            $stream.html(html).show();
        },

        getInitials: function(name) {
            if (!name) return 'CC';
            var parts = name.trim().split(/\s+/);
            if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
            return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
        },

        getAvatarColor: function(name) {
            var sum = 0;
            var str = name || 'Direct Contact';
            for (var i = 0; i < str.length; i++) {
                sum += str.charCodeAt(i);
            }
            return this.avatarPalette[sum % this.avatarPalette.length];
        },

        renderCard: function(row, index) {
            var self = this;
            var cid = row.id || row.userId || (index + 1);
            var rawName = (row.contact_person_name || row.name || 'Direct Contact').trim();
            var initials = this.getInitials(rawName);
            var avatarBg = this.getAvatarColor(rawName);
            var rawDesig = (row.contact_person_designation || row.Designation || row.display_name || 'Key Stakeholder').trim();
            var email = (row.contact_person_email || row.email || '').trim();
            var phone = (row.clean_phone || (row.calling_code ? (row.calling_code + ' ' + (row.contact_person_number || row.mobile || '')) : (row.contact_person_number || row.mobile || ''))).trim();
            var cleanPhoneForTel = phone.replace(/[^0-9+]/g, '');
            var isPrimary = (row.is_primary == 1) || (index === 0);
            var clientName = (row.client_name || row.company_name || this.clientName || 'Direct Client').trim();
            var linkedJobs = parseInt(row.linked_jobs_count || 0, 10);
            var createdFormatted = row.created_formatted || 'Active Member';
            var statusStr = (row.status || 'active').toLowerCase();
            var isActive = (statusStr.indexOf('active') !== -1);

            // Sub-Card 1: Primary vs Secondary Badge
            var primaryBadgeHtml = isPrimary
                ? `<span class="cd-badge-primary-spoc"><i class="fas fa-star text-amber-500 me-1"></i>Primary SPOC</span>`
                : `<span class="cd-badge-secondary-contact">Contact</span>`;

            // Sub-Card 2: Communication Elements
            var copyAllText = `Name: ${rawName} | Email: ${email || 'N/A'} | Phone: ${phone || 'N/A'} | Client: ${clientName}`;

            var emailHtml = email
                ? `<a href="mailto:${email}" class="cd-comm-link text-truncate" title="Send email to ${email}">${email}</a>`
                : `<span class="text-muted" style="font-size:11px;">No email on file</span>`;

            var emailCopyBtn = email
                ? `<button type="button" class="cd-copy-btn js-copy-data" data-copy="${email}" title="Copy Email"><i class="far fa-copy"></i></button>`
                : '';

            var phoneHtml = phone
                ? `<a href="tel:${cleanPhoneForTel}" class="cd-comm-link text-truncate" title="Call ${phone}">${phone}</a>`
                : `<span class="text-muted" style="font-size:11px;">No phone on file</span>`;

            var phoneCopyBtn = phone
                ? `<button type="button" class="cd-copy-btn js-copy-data" data-copy="${phone}" title="Copy Phone"><i class="far fa-copy"></i></button>`
                : '';

            // Sub-Card 3: Role Scope Classification
            var desigLower = rawDesig.toLowerCase();
            var roleBadgeText = 'Account Coordinator';
            if (desigLower.indexOf('director') !== -1 || desigLower.indexOf('vp') !== -1 || desigLower.indexOf('chief') !== -1) {
                roleBadgeText = 'Executive SPOC';
            } else if (desigLower.indexOf('manager') !== -1 || desigLower.indexOf('lead') !== -1 || desigLower.indexOf('head') !== -1) {
                roleBadgeText = 'Hiring Authority';
            } else if (desigLower.indexOf('hr') !== -1 || desigLower.indexOf('recruit') !== -1 || desigLower.indexOf('talent') !== -1) {
                roleBadgeText = 'Talent Partner';
            }

            // Linked Jobs
            var jobsCountText = linkedJobs > 0 ? `<strong>${linkedJobs}</strong> Active Roles` : `Client Requisitions`;

            // Status Pill
            var statusPillHtml = isActive
                ? `<span class="cd-contact-status-active"><i class="fas fa-circle"></i> Active</span>`
                : `<span class="cd-contact-status-inactive"><i class="fas fa-circle"></i> Inactive</span>`;

            // Action Buttons
            var callBtnHtml = phone
                ? `<a href="tel:${cleanPhoneForTel}" class="cd-contact-btn-call" title="Call ${rawName}"><i class="fas fa-phone-alt"></i> Call</a>`
                : `<button type="button" class="cd-contact-btn-call disabled" style="opacity:0.4; pointer-events:none;"><i class="fas fa-phone-alt"></i> Call</button>`;

            var mailBtnHtml = email
                ? `<a href="mailto:${email}" class="cd-contact-btn-mail" title="Email ${rawName}"><i class="fas fa-envelope"></i> Email</a>`
                : `<button type="button" class="cd-contact-btn-mail disabled" style="opacity:0.4; pointer-events:none;"><i class="fas fa-envelope"></i> Email</button>`;

            var waPhoneClean = cleanPhoneForTel.replace('+', '');
            var waBtnHtml = phone
                ? `<a href="https://wa.me/${waPhoneClean}" target="_blank" class="cd-contact-btn-wa" title="WhatsApp ${rawName}"><i class="fab fa-whatsapp"></i> Chat</a>`
                : '';

            return `
                <div class="cd-contact-card" data-contact-id="${cid}">
                    <div class="cd-contact-card-grid">

                        <!-- SUB-CARD 1: Contact Identity & Scope (All details integrated) -->
                        <div class="cd-contact-subcard cd-contact-subcard-profile">
                            <div class="cd-contact-subcard-hdr">
                                <span class="cd-contact-subcard-label"><i class="fas fa-user-circle me-1 text-primary"></i> PROFILE &amp; ROLE</span>
                                ${primaryBadgeHtml}
                            </div>
                            <div class="cd-contact-subcard-body">
                                <div class="cd-contact-profile-row">
                                    <div class="cd-contact-avatar-box" style="background: ${avatarBg};">
                                        ${initials}
                                    </div>
                                    <div class="cd-contact-profile-meta">
                                        <span class="cd-contact-card-name text-truncate" title="${rawName}">${rawName}</span>
                                        <span class="cd-contact-card-desig text-truncate" title="${rawDesig}">${rawDesig}</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-1 mt-1 flex-wrap">
                                    <span class="cd-chip-client text-truncate" title="${clientName}">
                                        <i class="far fa-building me-1"></i>${clientName}
                                    </span>
                                    <span class="cd-chip-id">#C-${cid}</span>
                                    <span class="cd-chip-auth">
                                        <i class="fas fa-check-circle me-1"></i>Authorized POC
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- SUB-CARD 2: Direct Communication Channels (Wide & Spacious) -->
                        <div class="cd-contact-subcard cd-contact-subcard-comm">
                            <div class="cd-contact-subcard-hdr">
                                <span class="cd-contact-subcard-label"><i class="fas fa-address-card me-1 text-info"></i> COMMUNICATION</span>
                                <button type="button" class="cd-copy-all-btn js-copy-data" data-copy="${copyAllText}" title="Copy all contact details">
                                    <i class="far fa-copy"></i> Copy
                                </button>
                            </div>
                            <div class="cd-contact-subcard-body">
                                <div class="cd-comm-item" title="${email}">
                                    <div class="cd-comm-content">
                                        <i class="far fa-envelope text-primary"></i>
                                        ${emailHtml}
                                    </div>
                                    ${emailCopyBtn}
                                </div>
                                <div class="cd-comm-item" title="${phone}">
                                    <div class="cd-comm-content">
                                        <i class="fas fa-phone-alt text-success"></i>
                                        ${phoneHtml}
                                    </div>
                                    ${phoneCopyBtn}
                                </div>
                            </div>
                        </div>

                        <!-- SUB-CARD 3: Requisitions & Quick Actions (Zero overflow) -->
                        <div class="cd-contact-subcard cd-contact-subcard-actions">
                            <div class="cd-contact-subcard-hdr">
                                <span class="cd-contact-subcard-label"><i class="fas fa-bolt me-1 text-amber-500"></i> REQUISITIONS &amp; ACTIONS</span>
                                ${statusPillHtml}
                            </div>
                            <div class="cd-contact-subcard-body">
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                    <div class="cd-linked-jobs-stat text-truncate">
                                        <i class="fas fa-briefcase text-info"></i>
                                        <span>${jobsCountText}</span>
                                    </div>
                                    <a href="javascript:void(0);" class="cd-view-jobs-link js-contact-view-jobs" title="View client job requisitions">
                                        View Jobs <i class="fas fa-arrow-right ms-1" style="font-size:8px;"></i>
                                    </a>
                                </div>
                                <div class="cd-contact-actions-row">
                                    ${callBtnHtml}
                                    ${mailBtnHtml}
                                    ${waBtnHtml}
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- SUB-CARD 4: Bottom Activity & Metadata Strip -->
                    <div class="cd-contact-card-bottom">
                        <div class="cd-contact-bottom-left">
                            <span class="cd-contact-meta-item">
                                <i class="far fa-calendar-check text-slate-400"></i> Member since ${createdFormatted}
                            </span>
                            <span class="text-slate-300">•</span>
                            <span class="cd-contact-meta-item">
                                <i class="fas fa-check-double text-success"></i> Verified Direct Point of Contact
                            </span>
                        </div>
                        <div class="cd-contact-bottom-right">
                            <span class="cd-contact-tag-pill">
                                <i class="fas fa-id-badge text-primary"></i> Direct Stakeholder
                            </span>
                            <span class="cd-contact-tag-pill">
                                <i class="fas fa-bolt text-amber-500"></i> Quick Reach
                            </span>
                        </div>
                    </div>
                </div>
            `;
        }
    };

    function initDContactsDataTable() {
        var cid = (typeof dcClientId !== 'undefined') ? dcClientId : (window.ClientStateManager && typeof window.ClientStateManager.getSelectedClientId === 'function' ? window.ClientStateManager.getSelectedClientId() : null);
        window.DirectClientContactsManager.init(cid);
    }
    function initDContactsManager() {
        var cid = (typeof dcClientId !== 'undefined') ? dcClientId : (window.ClientStateManager && typeof window.ClientStateManager.getSelectedClientId === 'function' ? window.ClientStateManager.getSelectedClientId() : null);
        window.DirectClientContactsManager.init(cid);
    }
</script>

<!-- Stage & Sub-Stage Modal Shell -->
<div class="modal fade" id="stage_sub_stage" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header flex-stack py-4 px-6">
        <h3 class="fw-bolder mb-0">Candidate Stage Management</h3>
        <button type="button" class="btn btn-sm btn-icon btn-light-danger" data-bs-dismiss="modal" data-dismiss="modal" id="stage_sub_stage_close">
          <i class="fas fa-times fa-solid"></i>
        </button>
      </div>
      <div class="modal-body p-6">
        <div class="stage_body_content" id="stage_body_content"></div>
      </div>
    </div>
  </div>
</div>

<script>
    (function() {
        var dcClientId = parseInt('{{ $directClientDetail->client_id ?: ($directClientDetail->from_company_id ?: $directClientDetail->company_id) }}', 10);
        var currentCompanyId = parseInt('{{ auth()->user()->company_id }}', 10);

        function mapDirectTabIdentity(rawTarget) {
            var clean = (rawTarget || '').replace('#', '').trim();
            var normTab = 'overview';
            var domPaneId = 'overview';

            if (clean === 'requisitions' || clean === 'jobs' || clean === 'job') {
                normTab = 'jobs';
                domPaneId = 'requisitions';
            } else if (clean === 'candidate_list' || clean === 'candidates') {
                normTab = 'candidates';
                domPaneId = 'candidate_list';
            } else if (clean === 'interview_tracker' || clean === 'interviews') {
                normTab = 'interviews';
                domPaneId = 'interview_tracker';
            } else if (clean === 'candidate_joining' || clean === 'offers' || clean === 'joining') {
                normTab = 'offers';
                domPaneId = 'candidate_joining';
            } else if (clean === 'recent_activity' || clean === 'activity') {
                normTab = 'recent_activity';
                domPaneId = 'recent_activity';
            } else if (clean === 'contacts' || clean === 'client_contacts') {
                normTab = 'contacts';
                domPaneId = 'contacts';
            } else if (clean === 'candidateDashboard' || clean === 'performance' || clean === 'dashboard' || clean === 'analytics') {
                normTab = 'performance';
                domPaneId = 'candidateDashboard';
            } else if (clean === 'overview' || clean === 'client_overview') {
                normTab = 'overview';
                domPaneId = 'overview';
            }

            return { normTab: normTab, domPaneId: domPaneId };
        }

        function loadDirectTabContent(target, force, filter) {
            if (!target) return;
            var tabInfo = mapDirectTabIdentity(target);
            var normTab = tabInfo.normTab;
            var domPaneId = tabInfo.domPaneId;

            var activeSelId = window.ClientStateManager && typeof window.ClientStateManager.getSelectedClientId === 'function'
                ? window.ClientStateManager.getSelectedClientId()
                : (window.activeSelectedClientId || null);
            var directCurrentId = parseInt($('#kt_activitiesDirectClient #direct_client_id').val() || dcClientId, 10);
            if (activeSelId && parseInt(activeSelId, 10) !== directCurrentId) {
                console.warn('[Direct Client Drawer Shield] Discarded tab load for inactive client', activeSelId, 'vs current', directCurrentId);
                return;
            }

            // Synchronize DOM Tab Pills and Panes
            var $drawer = $('#kt_activitiesDirectClient');
            $drawer.find('.cd-drawer-tabs-list .nav-link, .cd-tab-pill-btn').removeClass('active');
            var $targetPill = $drawer.find('.cd-tab-pill-btn[href="#' + domPaneId + '"], .cd-drawer-tabs-list a[href="#' + domPaneId + '"]');
            if ($targetPill.length) {
                $targetPill.addClass('active');
            }
            $drawer.find('.tab-pane').removeClass('active show');
            $drawer.find('#' + domPaneId).addClass('active show');

            var coord = (window.__activeClientDrawer && window.__activeClientDrawer.coordinator) ? window.__activeClientDrawer.coordinator : null;

            // FSM Request Deduplication via Coordinator: drop only if already in-flight LOADING with same filter
            if (coord && !force) {
                var st = coord.getState(normTab);
                if (st && st.status === 'LOADING' && (!filter || st.filter === filter)) {
                    return;
                }
            }
            if (coord) {
                coord.transition(normTab, 'LOADING', { filter: filter || 'all' });
            }

            if (normTab === 'performance') {
                if (typeof initPerformanceDashboard === 'function') {
                    initPerformanceDashboard();
                } else if (typeof window.fetchDirectClientDashboardAnalytics === 'function') {
                    window.fetchDirectClientDashboardAnalytics();
                }
                setTimeout(function() {
                    if (window.perfChartInstances) {
                        Object.values(window.perfChartInstances).forEach(function(c) {
                            try { if (c && typeof c.resize === 'function') c.resize(); } catch(err){}
                        });
                    }
                }, 60);
                if (coord) coord.transition(normTab, 'READY');
                if (window.ClientStateManager) window.ClientStateManager.markTabLoaded(dcClientId, target);
                return;
            }

            if (normTab === 'candidates') {
                var activeCandBtn = $('#kt_activitiesDirectClient #candidate_list .cd-cand-subtab-direct.active, #kt_activitiesDirectClient #candidate_list .cd-cand-subtab.active');
                var activeCandStage = activeCandBtn.length ? (activeCandBtn.data('stage') || 'sourcing') : 'sourcing';
                var targetStage = filter ? filter : activeCandStage;

                if (filter) {
                    $('#kt_activitiesDirectClient #candidate_list .cd-cand-subtab, #kt_activitiesDirectClient #candidate_list .cd-cand-subtab-direct').removeClass('active').attr('aria-selected', 'false');
                    var $matchBtn = $('#kt_activitiesDirectClient #candidate_list .cd-cand-subtab[data-stage="' + targetStage + '"], #kt_activitiesDirectClient #candidate_list .cd-cand-subtab-direct[data-stage="' + targetStage + '"]');
                    if ($matchBtn.length) {
                        $matchBtn.addClass('active').attr('aria-selected', 'true');
                    } else {
                        $('#kt_activitiesDirectClient #candidate_list .cd-cand-subtab[data-stage="sourcing"], #kt_activitiesDirectClient #candidate_list .cd-cand-subtab-direct[data-stage="sourcing"]').addClass('active').attr('aria-selected', 'true');
                    }
                }

                if (window.ClientCandidateStoreDirect) {
                    if (!window.ClientCandidateStoreDirect.companyId || window.ClientCandidateStoreDirect.clientId !== dcClientId || window.ClientCandidateStoreDirect.state === 'IDLE') {
                        if (typeof window.ClientCandidateStoreDirect.init === 'function') {
                            window.ClientCandidateStoreDirect.init(currentCompanyId, dcClientId, 'direct');
                        }
                    } else {
                        window.ClientCandidateStoreDirect.activeStage = targetStage;
                        window.ClientCandidateStoreDirect.filterAndRender();
                    }
                }
                if (coord) coord.transition(normTab, 'READY');
                if (window.ClientStateManager) window.ClientStateManager.markTabLoaded(dcClientId, target);
                return;
            }

            if (normTab === 'contacts') {
                var activeContactBtn = $('#kt_activitiesDirectClient .cd-contact-tabs-card .cd-contact-subtab.active');
                var activeContactFilter = activeContactBtn.length ? (activeContactBtn.data('filter') || 'all') : 'all';
                var contactFilter = filter ? filter : activeContactFilter;

                if (filter) {
                    $('#kt_activitiesDirectClient .cd-contact-tabs-card .cd-contact-subtab').removeClass('active');
                    $('#kt_activitiesDirectClient .cd-contact-tabs-card .cd-contact-subtab[data-filter="' + contactFilter + '"]').addClass('active');
                } else if (!activeContactBtn.length) {
                    $('#kt_activitiesDirectClient .cd-contact-tabs-card .cd-contact-subtab[data-filter="all"]').addClass('active');
                }

                if (typeof initDContactsDataTable === 'function') {
                    initDContactsDataTable();
                } else if (window.DirectClientContactsManager) {
                    window.DirectClientContactsManager.init(dcClientId);
                }
                if (window.DirectClientContactsManager) {
                    window.DirectClientContactsManager.activeFilter = contactFilter;
                    if (Array.isArray(window.DirectClientContactsManager.allContacts) && window.DirectClientContactsManager.allContacts.length > 0) {
                        window.DirectClientContactsManager.filterAndRender();
                    } else {
                        window.DirectClientContactsManager.fetch();
                    }
                }
                if (coord) coord.transition(normTab, 'READY', { filter: contactFilter });
                if (window.ClientStateManager) window.ClientStateManager.markTabLoaded(dcClientId, target);
                return;
            }

            if (normTab === 'jobs') {
                var activeJobBtn = $('#kt_activitiesDirectClient #requisitions .js-job-filter-btn.active');
                var currentActiveStatus = activeJobBtn.length ? (activeJobBtn.data('status') || 'all') : (window.currentJobStatusDirect || 'all');
                var jobStatus = filter ? filter : currentActiveStatus;

                if (filter) {
                    $('#kt_activitiesDirectClient #requisitions .js-job-filter-btn').removeClass('active');
                    $('#kt_activitiesDirectClient #requisitions .js-job-filter-btn[data-status="' + jobStatus + '"]').addClass('active');
                } else if (!activeJobBtn.length) {
                    $('#kt_activitiesDirectClient #requisitions .js-job-filter-btn[data-status="' + jobStatus + '"]').addClass('active');
                }
                window.currentJobStatusDirect = jobStatus;

                if (typeof fetchJobsClientdirect === 'function') {
                    var $directJobsList = $('#kt_activitiesDirectClient #directclientJobsListajax');
                    if ($directJobsList.length && $directJobsList.children().length === 0) {
                        $directJobsList.html(`
                            <div class="d-flex flex-column align-items-center justify-content-center py-10" style="min-height:200px;">
                                <div class="spinner-border text-primary mb-3" style="width:2rem;height:2rem;" role="status"></div>
                                <span class="text-gray-600 font-poppins fs-7 fw-semibold">Loading open jobs...</span>
                            </div>
                        `);
                    }
                    fetchJobsClientdirect(1, $('#kt_activitiesDirectClient #requisitions #inputJobSearch').val() || '', jobStatus);
                }
                if (coord) coord.transition(normTab, 'READY', { filter: jobStatus });
                if (window.ClientStateManager) window.ClientStateManager.markTabLoaded(dcClientId, target);
                return;
            }

            if (normTab === 'interviews') {
                if (typeof directfetchCandidatesInverViewTracker === 'function') {
                    directfetchCandidatesInverViewTracker(1, '');
                } else if (window.ClientInterviewStoreDirect && typeof window.ClientInterviewStoreDirect.fetch === 'function') {
                    window.ClientInterviewStoreDirect.fetch(dcClientId, null, false, '');
                }
                if (coord) coord.transition(normTab, 'READY');
                if (window.ClientStateManager) window.ClientStateManager.markTabLoaded(dcClientId, target);
                return;
            }

            if (normTab === 'offers') {
                var activeSegBtn = $('#kt_activitiesDirectClient #candidate_joining .cd-oj-seg-btn-direct.active');
                var currentActiveSeg = activeSegBtn.length ? (activeSegBtn.data('segment') || 'all') : (window.currentOjSegmentDirect || 'all');
                var segStatus = filter ? filter : currentActiveSeg;

                if (filter) {
                    $('#kt_activitiesDirectClient #candidate_joining .cd-oj-seg-btn-direct').removeClass('active');
                    $('#kt_activitiesDirectClient #candidate_joining .cd-oj-seg-btn-direct[data-segment="' + segStatus + '"]').addClass('active');
                } else if (!activeSegBtn.length) {
                    $('#kt_activitiesDirectClient #candidate_joining .cd-oj-seg-btn-direct[data-segment="all"]').addClass('active');
                }
                window.currentOjSegmentDirect = segStatus;

                var search = $('#kt_activitiesDirectClient #ojSearchInputDirect').val() || '';
                if (typeof directfetchCandidatesJoin === 'function') {
                    var $directOjList = $('#kt_activitiesDirectClient #candidatetListJoin');
                    if ($directOjList.length && $directOjList.children().length === 0) {
                        $directOjList.html(`
                            <div class="d-flex flex-column align-items-center justify-content-center py-10" style="min-height:200px;">
                                <div class="spinner-border text-primary mb-3" style="width:2rem;height:2rem;" role="status"></div>
                                <span class="text-gray-600 font-poppins fs-7 fw-semibold">Loading offers &amp; joinings...</span>
                            </div>
                        `);
                    }
                    directfetchCandidatesJoin(1, search, segStatus);
                }
                if (coord) coord.transition(normTab, 'READY', { filter: segStatus });
                if (window.ClientStateManager) window.ClientStateManager.markTabLoaded(dcClientId, target);
                return;
            }

            if (normTab === 'recent_activity') {
                if (window.ClientActivityManager && typeof window.ClientActivityManager.initClient === 'function') {
                    window.ClientActivityManager.initClient(dcClientId, false, dcClientId);
                }
                if (coord) coord.transition(normTab, 'READY');
                if (window.ClientStateManager) window.ClientStateManager.markTabLoaded(dcClientId, target);
                return;
            }

            if (normTab === 'overview') {
                if (coord) coord.transition(normTab, 'READY');
                if (window.ClientStateManager) window.ClientStateManager.markTabLoaded(dcClientId, target);
                return;
            }
        }
        window.loadDirectTabContent = loadDirectTabContent;

        // Pre-warm in-memory direct contacts manager immediately upon drawer mount
        if (window.DirectClientContactsManager) {
            try { window.DirectClientContactsManager.init(dcClientId); } catch(e){}
        }

        // Initialize active tab immediately on drawer mount
        var $initActiveTabDirect = $('#kt_activitiesDirectClient .tab-pane.active');
        var initTargetDirect = $initActiveTabDirect.length ? $initActiveTabDirect.attr('id') : 'overview';
        if (initTargetDirect && initTargetDirect !== 'overview') {
            loadDirectTabContent(initTargetDirect, true);
        }

        // Clean unbind previous namespaced listeners before binding fresh ones
        $(document).off('.directDrawer .directDrawerTab');

        // Scoped Click Handler for Drawer Tab Nav Links
        $(document).on('click.directDrawer', '#kt_activitiesDirectClient .cd-drawer-tabs-list .nav-link, #kt_activitiesDirectClient .cd-tab-pill-btn', function(e) {
            if (e && typeof e.preventDefault === 'function') {
                e.preventDefault();
            }
            var $link = $(this);
            var target = ($link.attr('href') || $link.attr('data-bs-target') || '').replace('#', '').trim();
            if (!target) return;
            loadDirectTabContent(target, false);
        });

        // Scoped Click Handler for Metric KPI Cards
        $(document).on('click.directDrawer', '#kt_activitiesDirectClient .cd-kpi-card', function(e) {
            if (e && typeof e.preventDefault === 'function') {
                e.preventDefault();
            }
            var target = $(this).data('target-tab') || $(this).attr('data-target-tab');
            if (!target) return;
            target = target.replace('#', '').trim();

            var segment = $(this).data('segment');
            if (segment && (target === 'candidate_joining' || target === 'offers')) {
                window.currentOjSegmentDirect = segment;
                $('#kt_activitiesDirectClient .cd-oj-seg-btn-direct').removeClass('active');
                $(`#kt_activitiesDirectClient .cd-oj-seg-btn-direct[data-segment="${segment}"]`).addClass('active');
            }
            loadDirectTabContent(target, true, segment);
        });

        // Tab visibility & idle wake-up listener
        $(document).on('visibilitychange.directDrawer', function() {
            if (document.visibilityState === 'visible') {
                var $drawer = $('#kt_activitiesDirectClient');
                if ($drawer.hasClass('drawer-on') || $drawer.is(':visible')) {
                    var $activePane = $drawer.find('.tab-pane.active');
                    var activeTab = $activePane.length ? $activePane.attr('id') : null;
                    if (activeTab && activeTab !== 'overview') {
                        loadDirectTabContent(activeTab, false);
                    }
                }
            }
        });

        $(document).on('keyup.directDrawer', '#kt_activitiesDirectClient #search', function() {
            const search = $(this).val();
            if (typeof directfetchCandidates === 'function') {
                directfetchCandidates(1, search);
            }
        });

        $(document).on('click.directDrawer', '#kt_activitiesDirectClient #paginationcandidate [data-page], #kt_activitiesDirectClient #paginationcandidate .page-link, #kt_activitiesDirectClient #paginationcandidate .pagi-page, #kt_activitiesDirectClient #paginationcandidate .pagi-arrow', function(e) {
            e.preventDefault();
            var $btn = $(e.target).closest('[data-page]');
            if (!$btn.length) return;

            var page = parseInt($btn.attr('data-page') || $btn.data('page'), 10);
            if (!page || page < 1 || $btn.hasClass('disabled') || $btn.hasClass('pagi-disabled') || $btn.hasClass('pagi-active-disabled') || $btn.is(':disabled')) return;

            var search = $('#kt_activitiesDirectClient #inputSearch').val() || '';
            if (typeof directfetchCandidates === 'function') {
                directfetchCandidates(page, search);
            }
        });

        $(document).on('click.directDrawer', '#kt_activitiesDirectClient #paginationcandidatejoin [data-page], #kt_activitiesDirectClient #paginationcandidatejoin .page-link, #kt_activitiesDirectClient #paginationcandidatejoin .pagi-page, #kt_activitiesDirectClient #paginationcandidatejoin .pagi-arrow', function(e) {
            e.preventDefault();
            var $btn = $(e.target).closest('[data-page]');
            if (!$btn.length) return;

            var page = parseInt($btn.attr('data-page') || $btn.data('page'), 10);
            if (!page || page < 1 || $btn.hasClass('disabled') || $btn.hasClass('pagi-disabled') || $btn.hasClass('pagi-active-disabled') || $btn.is(':disabled')) return;

            var search = $('#kt_activitiesDirectClient #ojSearchInputDirect').val() || '';
            var segment = window.currentOjSegmentDirect || 'all';
            if (typeof directfetchCandidatesJoin === 'function') {
                directfetchCandidatesJoin(page, search, segment);
            }
        });

        $(document).on('click.directDrawer', '#kt_activitiesDirectClient #paginationcandidateinterviewtracker [data-page], #kt_activitiesDirectClient #paginationcandidateinterviewtracker .page-link, #kt_activitiesDirectClient #paginationcandidateinterviewtracker .pagi-page, #kt_activitiesDirectClient #paginationcandidateinterviewtracker .pagi-arrow', function(e) {
            e.preventDefault();
            var $btn = $(e.target).closest('[data-page]');
            if (!$btn.length) return;

            var page = parseInt($btn.attr('data-page') || $btn.data('page'), 10);
            if (!page || page < 1 || $btn.hasClass('disabled') || $btn.hasClass('pagi-disabled') || $btn.hasClass('pagi-active-disabled') || $btn.is(':disabled')) return;

            var search = $('#kt_activitiesDirectClient #inputSearch').val() || '';
            if (typeof directfetchCandidatesInverViewTracker === 'function') {
                directfetchCandidatesInverViewTracker(page, search);
            }
        });

        $(document).on('click.directDrawer', '#kt_activitiesDirectClient #directpaginationclientjobs [data-page], #kt_activitiesDirectClient #directpaginationclientjobs .page-link, #kt_activitiesDirectClient #directpaginationclientjobs .pagi-page, #kt_activitiesDirectClient #directpaginationclientjobs .pagi-arrow', function(e) {
            e.preventDefault();
            var $btn = $(e.target).closest('[data-page]');
            if (!$btn.length) return;

            var page = parseInt($btn.attr('data-page') || $btn.data('page'), 10);
            if (!page || page < 1 || $btn.hasClass('disabled') || $btn.hasClass('pagi-disabled') || $btn.hasClass('pagi-active-disabled') || $btn.is(':disabled')) return;

            var search = $('#kt_activitiesDirectClient #requisitions #inputJobSearch').val() || '';
            if (typeof fetchJobsClientdirect === 'function') {
                fetchJobsClientdirect(page, search);
            }
        });
    })();

    /**
     * ── ClientCandidateStoreDirect (User RAM Virtual State Machine & DOM Engine) ──
     * Dedicated Direct Client Candidate Drawer implementation.
     * Enforces bounded 60ms coalescing, OCC version checks, Reverb real-time sync, and 0% MySQL reads.
     */
    window.ClientCandidateStoreDirect = {
        state: 'IDLE',
        companyId: null,
        clientId: null,
        clientType: 'direct',
        clientCandidateVersion: 0,
        queryFingerprint: null,
        candidateVersions: {},
        processedEventIds: new Set(),
        maxProcessedEventIds: 500,
        latestRequestId: 0,

        allCandidates: [],
        stageCounts: {
            sourcing: 0,
            screening: 0,
            evaluation: 0,
            negotiations: 0,
            offer: 0,
            joined: 0,
            rejected: 0,
            interviews: 0
        },
        activeStage: 'sourcing',
        searchTerm: '',

        filters: {
            search: '',
            status_id: '',
            job_id: '',
            page: 1,
            cursor: null,
            sort: 'created_at:desc,wiseId:desc'
        },
        data: [],
        pagination: {},

        QUERY_DEPENDENCIES: {
            search: { fields: ['full_name', 'current_designation', 'skills', 'email', 'phone'] },
            status_id: { fields: ['master_status_id', 'job_master_status_id'], filter_key: 'status_id' },
            job_id: { fields: ['jobId', 'job_id'], filter_key: 'job_id' },
            sort: { fields: ['created_at', 'wiseId'] },
            visibility: {
                excluded_values: {
                    master_status_id: [9],
                    job_master_status_id: [9]
                }
            }
        },

        reconcileTimer: null,
        coalesceDelayMs: 60,

        markEventProcessed: function(eventId) {
            if (!eventId) return;
            this.processedEventIds.add(eventId);
            if (this.processedEventIds.size > this.maxProcessedEventIds) {
                var oldest = this.processedEventIds.values().next().value;
                this.processedEventIds.delete(oldest);
            }
        },

        init: function(companyId, clientId, clientType) {
            if (this.clientId !== clientId || this.companyId !== companyId) {
                this.destroy();
            }

            this.companyId = companyId;
            this.clientId = clientId;
            this.clientType = clientType || 'direct';
            this.state = 'IDLE';
            this.clientCandidateVersion = 0;
            this.queryFingerprint = null;
            this.candidateVersions = {};
            this.processedEventIds.clear();
            this.allCandidates = [];
            this.activeStage = 'sourcing';
            this.searchTerm = '';
            this.bindEvents();
            this.subscribeReverb();
            this.fetch(1);
        },

        destroy: function() {
            if (this.reconcileTimer) {
                clearTimeout(this.reconcileTimer);
                this.reconcileTimer = null;
            }
            if (this.inFlightXhr && this.inFlightXhr.readyState !== 4) {
                try { this.inFlightXhr.abort(); } catch(e){}
                this.inFlightXhr = null;
            }
            if (window.Echo && this.companyId && this.clientId) {
                var channel = 'private-company.' + this.companyId + '.client.' + this.clientId + '.candidates';
                try { window.Echo.leave(channel); } catch (e) {}
            }
            this.state = 'IDLE';
            this.clientId = null;
            this.allCandidates = [];
            this.data = [];
            $(document).off('.directDrawerCand');
        },

        scheduleReconcile: function(resetToPageOne) {
            var self = this;
            if (resetToPageOne) {
                this.filters.page = 1;
                this.filters.cursor = null;
            }
            if (this.reconcileTimer) {
                clearTimeout(this.reconcileTimer);
            }
            this.reconcileTimer = setTimeout(function() {
                self.reconcileTimer = null;
                self.fetch(self.filters.page, self.filters.cursor);
            }, this.coalesceDelayMs);
        },

        fetch: function(page, cursor, isManualRefresh) {
            var self = this;
            this.latestRequestId++;
            var requestId = this.latestRequestId;

            this.state = (this.state === 'READY') ? 'REFRESHING' : 'LOADING';
            this.filters.page = page || 1;
            this.filters.cursor = cursor || null;

            if (this.state === 'LOADING' && (!this.allCandidates || this.allCandidates.length === 0)) {
                $('#cdCandSkeletonLoaderDirect').show();
                $('#cdCandidateTableWrapDirect').hide();
                $('#cdCandEmptyStateDirect').hide();
            }

            if (this.inFlightXhr && this.inFlightXhr.readyState !== 4) {
                try { this.inFlightXhr.abort(); } catch(e){}
            }

            var xhr = $.ajax({
                url: '{{ url("service_company/direct-client-candidate-list") }}',
                type: 'GET',
                headers: { 'Accept': 'application/json' },
                data: {
                    clientid: this.clientId,
                    search: '',
                    status_id: '',
                    job_id: this.filters.job_id,
                    page: this.filters.page,
                    cursor: this.filters.cursor,
                    size: 100
                },
                success: function(res) {
                    var curSelId = window.ClientStateManager && typeof window.ClientStateManager.getSelectedClientId === 'function'
                        ? window.ClientStateManager.getSelectedClientId()
                        : (window.activeSelectedClientId || null);
                    if (curSelId && parseInt(curSelId, 10) !== parseInt(self.clientId, 10)) {
                        return;
                    }
                    if (requestId !== self.latestRequestId) return;

                    self.allCandidates = res.data || [];
                    self.pagination = res.pagination || {};
                    self.clientCandidateVersion = res.meta?.client_candidate_version || self.clientCandidateVersion;
                    self.queryFingerprint = res.meta?.query_fingerprint || null;

                    self.candidateVersions = {};
                    self.allCandidates.forEach(function(cand) {
                        var cid = cand.id || cand.wiseId;
                        if (cid && cand.aggregate_version !== undefined) {
                            self.candidateVersions[cid] = cand.aggregate_version;
                        }
                    });

                    // Stage counts computation from aggregations or fallback
                    if (res.meta && res.meta.stage_counts) {
                        self.stageCounts = res.meta.stage_counts;
                    } else {
                        self.calculateStageCounts(self.allCandidates);
                    }

                    self.state = 'READY';
                    $('#cdCandSkeletonLoaderDirect').hide();
                    $('#cdCandidateTableWrapDirect').show();

                    self.updateCounts(self.pagination.total, self.pagination.total_relation);
                    self.updateAllStageBadgesDirect();
                    self.filterAndRender();
                },
                error: function(xhr, status) {
                    if (status === 'abort') return;
                    if (requestId !== self.latestRequestId) return;

                    $('#cdCandSkeletonLoaderDirect').hide();
                    $('#cdCandidateTableWrapDirect').show();

                    if (xhr.status === 409) {
                        self.scheduleReconcile(true);
                        return;
                    }

                    self.state = 'ERROR';
                    var msg = (xhr.status === 403)
                        ? 'Access denied to this direct client.'
                        : (xhr.status === 503)
                            ? 'Candidate search service temporarily unavailable. Please retry.'
                            : (xhr.responseJSON?.message || 'Failed to load candidates.');

                    $('#candidatetList').html(`<div class="text-center py-6 text-danger fw-bold">${msg}</div>`);
                    $('#paginationcandidate').empty();
                }
            });

            this.inFlightXhr = xhr;
            return xhr;
        },

        matchesStage: function(cand, stage) {
            if (!stage || stage === 'all') return true;
            var sid = parseInt(cand.master_status_id || cand.job_master_status_id || 0, 10);
            var sName = String(cand.child_status_name || cand.master_status_name || cand.name || '').toLowerCase();
            switch (stage) {
                case 'sourcing':
                    return sid === 1 || sName.includes('sourc') || sName.includes('inreview') || sName.includes('review') || sName.includes('sendmail');
                case 'screening':
                    return sid === 2 || sName.includes('screen');
                case 'evaluation':
                    return sid === 3 || sName.includes('eval') || sName.includes('interview');
                case 'negotiations':
                    return sid === 4 || sName.includes('negot');
                case 'offer':
                    return sid === 5 || sName.includes('offer');
                case 'joined':
                    return sid === 6 || sName.includes('join') || sName.includes('onboard');
                case 'rejected':
                    return sid === 7 || sid === 8 || sName.includes('reject');
                case 'interviews':
                    return sid === 3 || sName.includes('interview') || (cand.interview_count && cand.interview_count > 0);
                default:
                    return true;
            }
        },

        calculateStageCounts: function(list) {
            var counts = {
                sourcing: 0,
                screening: 0,
                evaluation: 0,
                negotiations: 0,
                offer: 0,
                joined: 0,
                rejected: 0,
                interviews: 0
            };
            var self = this;
            (list || []).forEach(function(cand) {
                if (self.matchesStage(cand, 'sourcing')) counts.sourcing++;
                if (self.matchesStage(cand, 'screening')) counts.screening++;
                if (self.matchesStage(cand, 'evaluation')) counts.evaluation++;
                if (self.matchesStage(cand, 'negotiations')) counts.negotiations++;
                if (self.matchesStage(cand, 'offer')) counts.offer++;
                if (self.matchesStage(cand, 'joined')) counts.joined++;
                if (self.matchesStage(cand, 'rejected')) counts.rejected++;
                if (self.matchesStage(cand, 'interviews')) counts.interviews++;
            });
            this.stageCounts = counts;
            return counts;
        },

        updateAllStageBadgesDirect: function() {
            var c = this.stageCounts || {};
            $('#candTabCountSourcingDirect').text(c.sourcing || 0);
            $('#candTabCountScreeningDirect').text(c.screening || 0);
            $('#candTabCountEvaluationDirect').text(c.evaluation || 0);
            $('#candTabCountNegotiationsDirect').text(c.negotiations || 0);
            $('#candTabCountOfferDirect').text(c.offer || 0);
            $('#candTabCountJoinedDirect').text(c.joined || 0);
            $('#candTabCountRejectedDirect').text(c.rejected || 0);
            $('#candTabCountInterviewsDirect').text(c.interviews || 0);
        },

        filterAndRender: function() {
            var self = this;
            var stage = this.activeStage || 'sourcing';
            var term = (this.searchTerm || '').toLowerCase().trim();

            var filtered = (this.allCandidates || []).filter(function(cand) {
                // 1. Stage filter
                if (!self.matchesStage(cand, stage)) return false;

                // 2. Search query filter
                if (term) {
                    var corpus = [
                        cand.full_name, cand.first_name, cand.last_name,
                        cand.current_designation, cand.skills,
                        cand.city_name, cand.country_name,
                        cand.current_company, cand.title, cand.job_title,
                        String(cand.id || cand.wiseId || '')
                    ].filter(Boolean).join(' ').toLowerCase();
                    if (!corpus.includes(term)) return false;
                }
                return true;
            });

            this.data = filtered;
            this.renderRows();
        },

        updateCounts: function(total, relation) {
            var num = Number(total || (this.allCandidates ? this.allCandidates.length : 0));
            var formatted = (relation === 'gte') ? (num.toLocaleString() + '+') : num.toLocaleString();
            $('#cdCandidateCountTextDirect').text(formatted);
            $('#tabBadgeCandidates').text(formatted).show();
        },

        renderRows: function() {
            if (!this.data || this.data.length === 0) {
                $('#candidatetList').empty();
                $('#cdCandEmptyStateDirect').show();
                $('#cdCandidateTableWrapDirect').hide();
                $('#paginationcandidate').empty();
                return;
            }

            $('#cdCandEmptyStateDirect').hide();
            $('#cdCandidateTableWrapDirect').show();

            var html = '';
            var self = this;

            this.data.forEach(function(row) {
                html += self.buildRowHtml(row);
            });

            $('#candidatetList').html(html);
            this.renderPagination();
        },

        buildRowHtml: function(row) {
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

            // Initials generator for candidate avatar monogram
            var getInitials = function(name) {
                if (!name || typeof name !== 'string') return 'C';
                var parts = name.trim().split(/\s+/).filter(Boolean);
                if (parts.length === 0) return 'C';
                if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
                return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase();
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

            // Experience number cleaner without trailing dots
            var cleanExpNum = function(val) {
                if (val === undefined || val === null || val === '') return null;
                var s = String(val).trim().replace(/\.+$/, '');
                var n = parseFloat(s);
                if (isNaN(n)) return s;
                return (n % 1 === 0) ? n.toString() : n.toFixed(1);
            };

            // Compact salary formatting (converts thousands to K: 250,000 -> 250K)
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
            var candInitials = getInitials(rawFullName);

            // 2. Sub-Card 2: Compensation & Experience
            var currencySymbol = row.current_ctc_cur || row.salary_currency || row.job_currency || '₹';
            if (currencySymbol === '0' || !currencySymbol || currencySymbol === 'INR') currencySymbol = '₹';
            if (currencySymbol === 'USD' || currencySymbol === '4') currencySymbol = '$';
            if (currencySymbol === 'EUR' || currencySymbol === '1') currencySymbol = '€';
            if (currencySymbol === 'GBP' || currencySymbol === '3') currencySymbol = '£';
            if (currencySymbol === 'AED' || currencySymbol === '5') currencySymbol = 'AED ';

            var ectcVal = formatCompactSalary(row.ectc_yearly || row.ectc || row.salary);
            var ctcVal = formatCompactSalary(row.ctc_yearly || row.ctc);
            // Single currency symbol formatting (guaranteed no double symbols)
            var salaryPrimaryHtml = ectcVal
                ? `<span class="fw-bold text-dark">${currencySymbol}${ectcVal}</span> <span class="text-muted fs-8">/ Yr</span>`
                : (ctcVal ? `<span class="fw-bold text-dark">${currencySymbol}${ctcVal}</span> <span class="text-muted fs-8">/ Yr</span>` : '<span class="text-muted">----</span>');

            var totalExpNum = cleanExpNum(row.total_exp);
            var relExpNum = cleanExpNum(row.rel_exp);
            var totalExpStr = totalExpNum !== null ? (totalExpNum + ' Yrs Total') : '----';
            if (relExpNum !== null && relExpNum !== '0' && relExpNum !== totalExpNum) {
                totalExpStr += ` (${relExpNum} Rel)`;
            }

            // Notice: Clean colored text ONLY (NO background/pill)
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
            var companyName = row.cur_company_name || row.client_name || '{{ addslashes($detailName ?? "Client") }}';
            var companyNameFormatted = formatWithAcronyms(limitWords(companyName, 12));
            var jobUrl = jobId ? `{{ url('service_company/jobs') }}/${jobId}` : 'javascript:void(0);';

            var jobInitial = (jobTitle && jobTitle.length > 0) ? jobTitle.charAt(0).toUpperCase() : 'J';
            var deptFormatted = formatWithAcronyms(row.industry_name || row.department || 'Technology');
            var jobTypeClean = (row.job_type || 'Full-Time');

            var scopeTag = this.clientType === 'direct' ? 'Direct Client' : 'Direct Client';
            var scopeCls = 'type-direct';
            if (row.client_type === 'internal' || this.clientType === 'internal') {
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

            // Target Requirement Company Branding Logo
            var companyLogo = row.client_logo || row.company_logo || row.logo_logical_path || row.logo || '{{ $detailLogo ?? "" }}';
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

            // 5. Sub-Card 5: Recruiter & Sourcing
            var recruiter = row.posted_by_name || row.posted_by || row.submitted_by_name || row.agency_root_name || 'Recruiter';
            var submitterCompany = row.submitted_by_company_name || row.post_company_name || row.apl_company_name || row.company_name || 'Agency';
            var recruiterFormatted = formatWithAcronyms(limitWords(recruiter, 12));
            var submitterFormatted = formatWithAcronyms(limitWords(submitterCompany, 12));
            var submittedAgo = formatTimeAgo(row.created_at);

            // 6. Sub-Card 6: Pipeline & Stage (Matched with Client Tracker Colors)
            var stageName = row.child_status_name || row.master_status_name || row.name || 'InReview';
            var sLower = stageName.toLowerCase();
            var stageChipStyle = 'background:#ecfeff;color:#0891b2;border:1px solid #a5f3fc;'; // Sourcing / InReview default
            if (sLower.includes('screen')) {
                stageChipStyle = 'background:#faf5ff;color:#9333ea;border:1px solid #e9d5ff;';
            } else if (sLower.includes('eval') || sLower.includes('interview')) {
                stageChipStyle = 'background:#eef2ff;color:#4f46e5;border:1px solid #c7d2fe;';
            } else if (sLower.includes('negot')) {
                stageChipStyle = 'background:#fdf2f8;color:#db2777;border:1px solid #fbcfe8;';
            } else if (sLower.includes('offer')) {
                stageChipStyle = 'background:#fffbeb;color:#d97706;border:1px solid #fde68a;';
            } else if (sLower.includes('join') || sLower.includes('onboard')) {
                stageChipStyle = 'background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;';
            } else if (sLower.includes('reject')) {
                stageChipStyle = 'background:#fef2f2;color:#ef4444;border:1px solid #fecaca;';
            }
            var subStageText = row.childStatus ? (row.child_status_name || 'Private') : 'Private';

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

            // LinkedIn Verified Pill: If LinkedIn ID/URL is available, otherwise hides
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

            // Visa Status Badge: (country short name, Visa Name) - if available, otherwise hides
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
                                        <a href="javascript:void(0);" class="cd-cand-name show-application-detail" data-row-id="${cid}" title="${$('<div>').text(rawFullName).html()}">
                                            ${displayName}
                                        </a>
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

                        <!-- Sub-Card 5: RECRUITER & SOURCING -->
                        <div class="cd-cand-subcard cd-cand-subcard-recruiter">
                            <div class="cd-cand-subcard-hdr">
                                <span class="cd-cand-subcard-label"><i class="fa fa-user-tie me-1 text-muted"></i>RECRUITER &amp; SOURCING</span>
                            </div>
                            <div class="cd-cand-subcard-body">
                                <div class="cd-recruiter-name" title="${$('<div>').text(recruiter).html()}">
                                    <i class="fas fa-user-circle text-primary me-1" style="font-size:10.5px;"></i>${recruiterFormatted}
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
                                    <a href="javascript:void(0);" class="cd-stage-chip stage_sub_stage" data-row-id="${cid}" data-toggle="modal" data-target="#stage_sub_stage" style="${stageChipStyle}" title="${stageName}">
                                        <span>${stageName}</span> <i class="fas fa-caret-down ms-1" style="font-size:9px;"></i>
                                    </a>
                                </div>
                                <div class="cd-stage-sub" title="${subStageText}">
                                    <span class="text-muted">Sub-stage:</span> <span class="fw-medium text-dark">${subStageText}</span>
                                </div>
                                <div class="cd-stage-action">
                                    <a href="javascript:void(0);" class="cd-action-link show-application-detail" data-row-id="${cid}" title="View details">
                                        <i class="far fa-id-badge text-primary me-1" style="font-size:9.5px;"></i>View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sub-Card 7: Full-Width Intelligence Strip (CV Match, Skills, LinkedIn Verified, Visa Status) -->
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
        },

        renderPagination: function() {
            var p = this.pagination;
            if (!p || (!p.has_more && (!p.current_page || p.current_page <= 1))) {
                $('#paginationcandidate').empty();
                return;
            }

            var totalRelation = p.total_relation || 'eq';
            var totalStr = (totalRelation === 'gte') ? (Number(p.total).toLocaleString() + '+') : Number(p.total).toLocaleString();
            var currentPage = p.current_page || 1;
            var hasMore = p.has_more || false;
            var nextCursor = p.next_cursor || null;

            var html = `
                <div class="text-muted fs-7">
                    Showing page <span class="fw-bolder text-dark">${currentPage}</span> of ${totalStr} candidates
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-light ${currentPage <= 1 ? 'disabled' : ''}" id="cdCandPrevBtnDirect" ${currentPage <= 1 ? 'disabled' : ''}>
                        <i class="fas fa-chevron-left fa-solid fs-8 me-1"></i> Previous
                    </button>
                    <button type="button" class="btn btn-sm btn-light-primary ${!hasMore ? 'disabled' : ''}" id="cdCandNextBtnDirect" ${!hasMore ? 'disabled' : ''} data-next-cursor="${nextCursor || ''}">
                        Next <i class="fas fa-chevron-right fa-solid fs-8 ms-1"></i>
                    </button>
                </div>
            `;

            $('#paginationcandidate').html(html);
        },

        bindEvents: function() {
            var self = this;

            $(document).off('.directDrawerCand');

            // 1. Sub-tab clicks (0 HTTP requests!)
            $(document).on('click.directDrawerCand', '#kt_activitiesDirectClient .cd-cand-subtab, #kt_activitiesDirectClient .cd-cand-subtab-direct', function(e) {
                if (e && typeof e.preventDefault === 'function') e.preventDefault();
                var stage = $(this).data('stage') || 'sourcing';
                $('#kt_activitiesDirectClient .cd-cand-subtab, #kt_activitiesDirectClient .cd-cand-subtab-direct').removeClass('active').attr('aria-selected', 'false');
                $(this).addClass('active').attr('aria-selected', 'true');
                self.activeStage = stage;
                self.filterAndRender();
            });

            // 2. Click on Candidates total count badge -> Show All candidates
            $(document).on('click.directDrawerCand', '#cdCandidateCountBadgeDirect', function(e) {
                if (e && typeof e.preventDefault === 'function') e.preventDefault();
                $('#kt_activitiesDirectClient .cd-cand-subtab, #kt_activitiesDirectClient .cd-cand-subtab-direct').removeClass('active').attr('aria-selected', 'false');
                self.activeStage = 'all';
                self.filterAndRender();
            });

            // 3. Search input with 300ms debounce (0 HTTP requests!)
            var searchDebounceTimer = null;
            $(document).on('input.directDrawerCand', '#cdCandSearchInputDirect', function() {
                var val = $(this).val();
                if (val && val.length > 0) {
                    $('#cdCandSearchClearDirect').show();
                } else {
                    $('#cdCandSearchClearDirect').hide();
                }
                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(function() {
                    self.searchTerm = val;
                    self.filterAndRender();
                }, 300);
            });

            // 4. Clear search button (0 HTTP requests!)
            $(document).on('click.directDrawerCand', '#cdCandSearchClearDirect', function() {
                $('#cdCandSearchInputDirect').val('');
                $(this).hide();
                self.searchTerm = '';
                self.filterAndRender();
            });

            // 5. Reset / Refresh button (deduplicated with loading spin)
            $(document).on('click.directDrawerCand', '#cdCandResetBtnDirect', function(e) {
                if (e && typeof e.preventDefault === 'function') e.preventDefault();
                var $btn = $(this);
                if ($btn.hasClass('is-refreshing') || self.state === 'LOADING' || self.state === 'REFRESHING') {
                    return;
                }
                $btn.addClass('is-refreshing');
                $btn.find('i').addClass('fa-spin');
                $('#cdCandSearchInputDirect').val('');
                $('#cdCandSearchClearDirect').hide();
                self.searchTerm = '';
                self.activeStage = 'sourcing';
                $('#kt_activitiesDirectClient .cd-cand-subtab, #kt_activitiesDirectClient .cd-cand-subtab-direct').removeClass('active').attr('aria-selected', 'false');
                $('#kt_activitiesDirectClient .cd-cand-subtab[data-stage="sourcing"], #kt_activitiesDirectClient .cd-cand-subtab-direct[data-stage="sourcing"]').addClass('active').attr('aria-selected', 'true');

                self.fetch(1, null, true).always(function() {
                    $btn.removeClass('is-refreshing');
                    $btn.find('i').removeClass('fa-spin');
                });
            });

            $(document).on('wheel.directDrawerCand', '.cd-cand-bottom-strip, .cd-bottom-skills-scroll', function(e) {
                if (e.originalEvent && e.originalEvent.deltaY !== 0) {
                    this.scrollLeft += e.originalEvent.deltaY;
                    e.preventDefault();
                }
            });

            $(document).on('click.directDrawerCand', '#cdCandNextBtnDirect', function() {
                if ($(this).hasClass('disabled')) return;
                var cursor = $(this).data('next-cursor');
                if (cursor) {
                    self.fetch(self.filters.page + 1, cursor);
                } else {
                    self.fetch(self.filters.page + 1);
                }
            });

            $(document).on('click.directDrawerCand', '#cdCandPrevBtnDirect', function() {
                if ($(this).hasClass('disabled')) return;
                var prevPage = Math.max(self.filters.page - 1, 1);
                self.filters.cursor = null;
                self.fetch(prevPage);
            });

            $(document).on('click.directDrawerCand', '.stage_sub_stage', function(e) {
                e.preventDefault();
                var id = $(this).data('row-id');
                if (!id) return;
                $('#stage_body_content').html('<div class="text-center py-10 text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Loading stage details...</div>');
                var url = '{{ route("service_company.job-applications.stage_body_content", ":id") }}'.replace(':id', id);
                $.ajax({
                    type: 'GET',
                    url: url,
                    success: function(res) {
                        if (res && res.status === 'success') {
                            $('#stage_body_content').html(res.view);
                        } else if (res && res.html) {
                            $('#stage_body_content').html(res.html);
                        }
                    },
                    error: function() {
                        $('#stage_body_content').html('<div class="text-center py-10 text-danger">Failed to load stage configuration.</div>');
                    }
                });
            });
        },

        mutationAffectsCurrentQuery: function(e) {
            var fields = e.changed_fields || {};
            var changedKeys = Object.keys(fields);
            if (changedKeys.length === 0) return false;

            var visibility = this.QUERY_DEPENDENCIES.visibility;
            if (visibility && visibility.excluded_values) {
                for (var visField in visibility.excluded_values) {
                    if (fields[visField] !== undefined) {
                        var excludedArr = visibility.excluded_values[visField];
                        var val = parseInt(fields[visField], 10);
                        if (excludedArr.indexOf(val) !== -1) return true;
                    }
                }
            }

            var isCurrentlyVisible = this.candidateVersions.hasOwnProperty(e.candidate_id);
            if (!isCurrentlyVisible) {
                var entersActiveScope = true;
                for (var depKey in this.QUERY_DEPENDENCIES) {
                    var def = this.QUERY_DEPENDENCIES[depKey];
                    if (def && def.filter_key && this.filters[def.filter_key]) {
                        var activeFilterVal = parseInt(this.filters[def.filter_key], 10);
                        var matchedField = false;
                        for (var f = 0; f < def.fields.length; f++) {
                            var fieldName = def.fields[f];
                            if (fields[fieldName] !== undefined) {
                                if (parseInt(fields[fieldName], 10) === activeFilterVal) {
                                    matchedField = true;
                                    break;
                                }
                            }
                        }
                        if (!matchedField) {
                            entersActiveScope = false;
                            break;
                        }
                    }
                }
                if (entersActiveScope) return true;
            }

            for (var filterKey in this.filters) {
                if (this.filters.hasOwnProperty(filterKey) && this.filters[filterKey] && this.QUERY_DEPENDENCIES[filterKey]) {
                    var depFields = this.QUERY_DEPENDENCIES[filterKey].fields || [];
                    for (var i = 0; i < depFields.length; i++) {
                        if (fields[depFields[i]] !== undefined) return true;
                    }
                }
            }

            if (this.QUERY_DEPENDENCIES.sort && this.QUERY_DEPENDENCIES.sort.fields) {
                var sortFields = this.QUERY_DEPENDENCIES.sort.fields;
                for (var s = 0; s < sortFields.length; s++) {
                    if (fields[sortFields[s]] !== undefined) return true;
                }
            }

            return false;
        },

        patchCandidateRow: function(candidateId, changedFields, newVersion) {
            this.candidateVersions[candidateId] = newVersion;
            var $row = $(`tr[data-candidate-id="${candidateId}"]`);
            if (!$row.length) return;

            if (changedFields.master_status_name || changedFields.child_status_name) {
                var name = changedFields.child_status_name || changedFields.master_status_name;
                var css = changedFields.master_status_css || 'badge-light-primary';
                $row.find('.stage_sub_stage').text(name).removeClass().addClass('badge ' + css + ' fs-8 fw-bolder stage_sub_stage');
            }
        },

        subscribeReverb: function() {
            if (!window.Echo || !this.companyId || !this.clientId) return;
            var self = this;
            var channel = 'private-company.' + this.companyId + '.client.' + this.clientId + '.candidates';

            window.Echo.private(channel)
                .listen('.CandidateStatusChanged', function(e) {
                    if (!e || !e.candidate_id) return;
                    if (e.event_id && self.processedEventIds.has(e.event_id)) return;
                    self.markEventProcessed(e.event_id);

                    if (self.candidateVersions[e.candidate_id] && e.aggregate_version <= self.candidateVersions[e.candidate_id]) {
                        return;
                    }

                    if (self.mutationAffectsCurrentQuery(e)) {
                        self.scheduleReconcile();
                    } else {
                        self.patchCandidateRow(e.candidate_id, e.changed_fields || {}, e.aggregate_version);
                    }
                })
                .listen('.ClientCandidateVersionUpdated', function(e) {
                    if (!e || !e.client_candidate_version) return;
                    if (e.event_id && self.processedEventIds.has(e.event_id)) return;
                    self.markEventProcessed(e.event_id);

                    if (e.client_candidate_version <= self.clientCandidateVersion) {
                        return;
                    }

                    self.clientCandidateVersion = e.client_candidate_version;
                    self.scheduleReconcile();
                });

            if (window.Echo.connector && window.Echo.connector.pusher && window.Echo.connector.pusher.connection) {
                window.Echo.connector.pusher.connection.bind('connected', function() {
                    if (self.state === 'READY' && self.clientId) {
                        self.reconcileVersion();
                    }
                });
            }
        },

        reconcileVersion: function() {
            var self = this;
            $.get('/service_company/clients/' + this.clientId + '/candidate-version', function(res) {
                if (res && res.client_candidate_version > self.clientCandidateVersion) {
                    self.clientCandidateVersion = res.client_candidate_version;
                    self.scheduleReconcile();
                }
            });
        }
    };

    function directfetchCandidates(page = 1, search = '') {
        if (search) {
            window.ClientCandidateStoreDirect.filters.search = search;
        }
        return window.ClientCandidateStoreDirect.fetch(page);
    }

    window.currentOjSegmentDirect = 'all';

    function directfetchCandidatesJoin(page = 1, search = '', segment = null) {
        var activeSelectedId = window.ClientStateManager && typeof window.ClientStateManager.getSelectedClientId === 'function'
            ? window.ClientStateManager.getSelectedClientId()
            : (window.activeSelectedClientId || null);
        var directCurrentId = parseInt('{{ $directClientDetail->client_id ?: ($directClientDetail->from_company_id ?: $directClientDetail->company_id) }}', 10);
        if (activeSelectedId && directCurrentId && parseInt(activeSelectedId, 10) !== directCurrentId) {
            return;
        }

        if (segment === null || segment === undefined) {
            segment = window.currentOjSegmentDirect || 'all';
        } else {
            window.currentOjSegmentDirect = segment;
        }

        if (search === null || search === undefined) {
            search = $('#kt_activitiesDirectClient #ojSearchInputDirect').val() || '';
        }

        if (window._activeOjDirectXhr && window._activeOjDirectXhr.readyState !== 4) {
            try { window._activeOjDirectXhr.abort(); } catch(e){}
        }

        $('.page-loader').css('display', 'flex');
        var clientid = '{{ $directClientDetail->client_id ?: ($directClientDetail->from_company_id ?: $directClientDetail->company_id) }}';
        window._activeOjDirectXhr = $.ajax({
            url: `{{ url('service_company/direct-client-candidate-join-list') }}?page=${page}`,
            type: 'GET',
            data: {
                search: search,
                segment: segment,
                clientid: clientid
            },
            success: function(response) {
                $('.page-loader').css('display', 'none');
                var curSelId = window.ClientStateManager && typeof window.ClientStateManager.getSelectedClientId === 'function'
                    ? window.ClientStateManager.getSelectedClientId()
                    : (window.activeSelectedClientId || null);
                if (curSelId && parseInt(curSelId, 10) !== directCurrentId) {
                    return;
                }
                $('#candidatetListJoin').html(response.html);
                $('#paginationcandidatejoin').html(response.pagination);
                if (response.counts) {
                    $('#ojCountAllDirect').text(response.counts.all || 0);
                    $('#ojCountOffersDirect').text(response.counts.offers || 0);
                    $('#ojCountJoiningDirect').text(response.counts.joining || 0);
                    $('#ojCountJoinedDirect').text(response.counts.joined || 0);
                    if ($('#cdOjCountTextDirect').length) {
                        $('#cdOjCountTextDirect').text(response.counts.all || 0);
                    }
                    if ($('#ojCountAllBadgeDirect').length) {
                        $('#ojCountAllBadgeDirect').text(response.counts.all || 0);
                    }
                    if ($('#ojDirectTabBadge').length) {
                        if (response.counts.all > 0) {
                            $('#ojDirectTabBadge').text(response.counts.all).show();
                        } else {
                            $('#ojDirectTabBadge').hide();
                        }
                    }
                    if ($('#kpiOffersCountDirect').length) {
                        $('#kpiOffersCountDirect').text(response.counts.offers || 0);
                    }
                    if ($('#kpiJoinsCountDirect').length) {
                        $('#kpiJoinsCountDirect').text(response.counts.joined || 0);
                    }
                }
            },
            error: function() {
                $('.page-loader').css('display', 'none');
            }
        });
        return window._activeOjDirectXhr;
    }
    window.directfetchCandidatesJoin = directfetchCandidatesJoin;

    $(document).off('click.directDrawerOffers', '#kt_activitiesDirectClient .cd-oj-seg-btn-direct');
    $(document).on('click.directDrawerOffers', '#kt_activitiesDirectClient .cd-oj-seg-btn-direct', function(e) {
        if (e && typeof e.preventDefault === 'function') e.preventDefault();
        var segment = $(this).data('segment') || 'all';
        $('#kt_activitiesDirectClient .cd-oj-seg-btn-direct').removeClass('active');
        $(this).addClass('active');
        window.currentOjSegmentDirect = segment;
        var search = $('#kt_activitiesDirectClient #ojSearchInputDirect').val() || '';
        if (window.__activeClientDrawer && window.__activeClientDrawer.coordinator) {
            window.__activeClientDrawer.coordinator.setFilter('offers', segment);
        }
        if (typeof directfetchCandidatesJoin === 'function') {
            directfetchCandidatesJoin(1, search, segment);
        }
    });

    var ojSearchTimerDirect = null;
    $(document).off('keyup.directDrawerOffers', '#kt_activitiesDirectClient #ojSearchInputDirect');
    $(document).on('keyup.directDrawerOffers', '#kt_activitiesDirectClient #ojSearchInputDirect', function() {
        clearTimeout(ojSearchTimerDirect);
        var search = $(this).val();
        if (search && search.length > 0) {
            $('#kt_activitiesDirectClient #ojSearchClearDirect').show();
        } else {
            $('#kt_activitiesDirectClient #ojSearchClearDirect').hide();
        }
        ojSearchTimerDirect = setTimeout(function() {
            directfetchCandidatesJoin(1, search, window.currentOjSegmentDirect || 'all');
        }, 300);
    });

    $(document).off('click.directDrawerOffers', '#kt_activitiesDirectClient #ojSearchClearDirect');
    $(document).on('click.directDrawerOffers', '#kt_activitiesDirectClient #ojSearchClearDirect', function() {
        $('#kt_activitiesDirectClient #ojSearchInputDirect').val('');
        $(this).hide();
        directfetchCandidatesJoin(1, '', window.currentOjSegmentDirect || 'all');
    });

    $(document).off('click.directDrawerOffers', '#kt_activitiesDirectClient #ojResetBtnDirect');
    $(document).on('click.directDrawerOffers', '#kt_activitiesDirectClient #ojResetBtnDirect', function() {
        $('#kt_activitiesDirectClient #ojSearchInputDirect').val('');
        $('#kt_activitiesDirectClient #ojSearchClearDirect').hide();
        window.currentOjSegmentDirect = 'all';
        $('#kt_activitiesDirectClient .cd-oj-seg-btn-direct').removeClass('active');
        $('#kt_activitiesDirectClient .cd-oj-seg-btn-direct[data-segment="all"]').addClass('active');
        directfetchCandidatesJoin(1, '', 'all');
    });

    // ══════════════════════════════════════════════════════════════════════════
    // GOD-LEVEL CLIENT-FIRST INTERVIEW STORE (DIRECT CLIENT DRAWER)
    // 0% Server Strain | Multi-Tenant Isolated | Bidirectional Cursor Navigation
    // ══════════════════════════════════════════════════════════════════════════
    window.ClientInterviewStoreDirect = (function() {
        'use strict';

        const store = new Map();
        const subscriptions = new Map();
        const activeRequests = new Map();
        let requestSequence = 0;

        let currentSearch = '';
        let pageSize = 10; // Minimum 10 rows default

        function getCompositeKey(clientId) {
            const companyId = window.authCompanyId || '{{ auth()->user()->company_id ?? 0 }}';
            const sessionId = window.userSessionId || 'default_session';
            return `${companyId}:${sessionId}:direct:${clientId}`;
        }

        function init() {
            const clientId = parseInt('{{ $directClientDetail->client_id ?: ($directClientDetail->from_company_id ?: $directClientDetail->company_id) }}', 10);
            if (!clientId) return;

            // 1. Subscribe Reverb WebSocket Channel
            subscribeChannel(clientId);

            // 2. Bind Reverb Reconnect Resynchronization Hook
            if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
                window.Echo.connector.pusher.connection.bind('connected', function() {
                    markAllStale();
                    revalidateIfActive(clientId);
                });
            }

            // 3. Bidirectional Cursor Click Handler
            $(document)
                .off('click.directInterviewPagination', '#paginationcandidateinterviewtracker [data-direction]')
                .on('click.directInterviewPagination', '#paginationcandidateinterviewtracker [data-direction]', function(e) {
                    e.preventDefault();
                    const $btn = $(e.currentTarget);
                    if ($btn.hasClass('disabled')) return;
                    const direction = $btn.attr('data-direction');
                    navigatePagination(clientId, direction);
                });

            // 4. Page Size Selector Change
            $(document)
                .off('change.directInterviewPageSize', '#itPageSizeSelectDirect')
                .on('change.directInterviewPageSize', '#itPageSizeSelectDirect', function() {
                    pageSize = Math.max(10, parseInt($(this).val(), 10) || 10);
                    const cacheKey = getCompositeKey(clientId);
                    const entry = store.get(cacheKey);
                    if (entry) {
                        entry.cursorStack = [null];
                        entry.cursorIndex = 0;
                    }
                    fetchInterviews(clientId, null, true, currentSearch);
                });

            // 5. Search Bar Input Listener with Debounce
            let searchDebounceTimer = null;
            $('#itSearchInputDirect').off('input').on('input', function() {
                const val = $(this).val().trim();
                $('#itSearchClearDirect').toggle(val.length > 0);
                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(function() {
                    currentSearch = val;
                    const cacheKey = getCompositeKey(clientId);
                    const entry = store.get(cacheKey);
                    if (entry) {
                        entry.cursorStack = [null];
                        entry.cursorIndex = 0;
                    }
                    fetchInterviews(clientId, null, true, currentSearch);
                }, 250);
            });

            // 6. Search Bar Clear Button
            $('#itSearchClearDirect').off('click').on('click', function() {
                $('#itSearchInputDirect').val('');
                $(this).hide();
                currentSearch = '';
                const cacheKey = getCompositeKey(clientId);
                const entry = store.get(cacheKey);
                if (entry) {
                    entry.cursorStack = [null];
                    entry.cursorIndex = 0;
                }
                fetchInterviews(clientId, null, true, '');
            });

            // 7. Sub-Tabs Filter Buttons (Single Active Highlight Rule)
            $(document)
                .off('click.directItFilter', '.js-it-filter-btn')
                .on('click.directItFilter', '.js-it-filter-btn', function() {
                    $('.js-it-filter-btn').removeClass('active');
                    $(this).addClass('active');
                    activeFilter = $(this).data('filter') || 'all';
                    applyCurrentFilter();
                });
        }

        function subscribeChannel(clientId) {
            const companyId = window.authCompanyId || '{{ auth()->user()->company_id ?? 0 }}';
            if (!companyId || !clientId || typeof window.Echo === 'undefined') return;
            const channelName = `private-company.${companyId}.client.${clientId}`;

            if (!subscriptions.has(channelName)) {
                try {
                    const channel = window.Echo.private(channelName);
                    channel.listen('.InterviewUpdated', function(payload) {
                        if (!payload) return;
                        if (payload.relationship_type && payload.relationship_type !== 'DIRECT_CLIENT') {
                            return; // Target only direct client
                        }
                        const cacheKey = getCompositeKey(clientId);
                        const entry = store.get(cacheKey);
                        if (entry) {
                            if (payload.projection_version && payload.projection_version <= entry.version) {
                                return; // Monotonicity guard: Ignore obsolete broadcast
                            }
                            entry.state = 'STALE';
                            entry.version = payload.projection_version || (entry.version + 1);
                            revalidateIfActive(clientId);
                        }
                    });
                    subscriptions.set(channelName, channel);
                } catch (err) {
                    console.warn('Reverb interview channel subscription deferred:', err);
                }
            }
        }

        function markAllStale() {
            store.forEach(function(entry) {
                if (entry.state === 'READY') {
                    entry.state = 'STALE';
                }
            });
        }

        function revalidateIfActive(clientId) {
            const $tabPane = $('#interview_tracker');
            if ($tabPane.hasClass('active') || $tabPane.is(':visible')) {
                fetchInterviews(clientId, null, true);
            }
        }

        function navigatePagination(clientId, direction) {
            const cacheKey = getCompositeKey(clientId);
            const entry = store.get(cacheKey);
            if (!entry) return;

            if (direction === 'prev' && entry.cursorIndex > 0) {
                entry.cursorIndex--;
                const prevCursor = entry.cursorStack[entry.cursorIndex];
                fetchInterviews(clientId, prevCursor, false, currentSearch);
            } else if (direction === 'next') {
                const currentCursorKey = (entry.cursorStack[entry.cursorIndex] || 'initial') + '_' + (currentSearch || '') + '_' + pageSize;
                const currentPage = entry.pages.get(currentCursorKey);
                if (currentPage && currentPage.next_cursor) {
                    entry.cursorIndex++;
                    if (entry.cursorIndex >= entry.cursorStack.length) {
                        entry.cursorStack.push(currentPage.next_cursor);
                    }
                    fetchInterviews(clientId, currentPage.next_cursor, false, currentSearch);
                }
            }
        }

        function fetchInterviews(clientId, cursor = null, forceRevalidate = false, search = null) {
            if (search !== null) {
                currentSearch = search;
            }
            const cacheKey = getCompositeKey(clientId);
            const cursorKey = (cursor || 'initial') + '_' + (currentSearch || '') + '_' + pageSize;
            let entry = store.get(cacheKey);

            // Zero-Latency User RAM Render
            if (entry && entry.state === 'READY' && !forceRevalidate && entry.pages.has(cursorKey)) {
                const pageData = entry.pages.get(cursorKey);
                renderTableRows(pageData.data);
                renderPagination(entry.cursorIndex, entry.cursorStack.length, pageData.next_cursor, entry.total, pageData.data.length);
                updateBadgeAndKPI(entry.total);
                return;
            }

            // Abort previous in-flight request strictly for this drawer
            if (activeRequests.has(cacheKey)) {
                const prev = activeRequests.get(cacheKey);
                try { prev.controller.abort(); } catch (e) {}
            }

            const controller = new AbortController();
            const currentRequestId = ++requestSequence;
            activeRequests.set(cacheKey, { controller: controller, requestId: currentRequestId });

            if (!entry) {
                entry = {
                    state: 'LOADING',
                    version: 1,
                    total: 0,
                    pages: new Map(),
                    cursorStack: [null],
                    cursorIndex: 0
                };
                store.set(cacheKey, entry);
            } else {
                entry.state = 'LOADING';
            }

            const $list = $('#candidatetListinterviewtracker');
            if (!forceRevalidate) {
                $list.html(`
                    <div class="it-loading-card">
                        <span class="spinner-border spinner-border-sm text-primary me-2"></span>
                        <span class="text-gray-600 fs-7">Loading Authoritative Interview Projection...</span>
                    </div>
                `);
            }

            let url = `{{ url('service_company/direct-client-candidate-interview-tracker') }}?clientid=${clientId}&clienttype=direct&size=${pageSize}`;
            if (cursor) url += `&cursor=${encodeURIComponent(cursor)}`;
            if (currentSearch) url += `&search=${encodeURIComponent(currentSearch)}`;

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                signal: controller.signal,
                success: function(res) {
                    const active = activeRequests.get(cacheKey);
                    if (!active || active.requestId !== currentRequestId) return;

                    if (res && Array.isArray(res.data)) {
                        entry.state = 'READY';
                        entry.version = res.version || entry.version;
                        entry.total = res.total;

                        entry.pages.set(cursorKey, {
                            data: res.data,
                            next_cursor: res.next_cursor || null
                        });

                        renderTableRows(res.data);
                        renderPagination(entry.cursorIndex, entry.cursorStack.length, res.next_cursor, res.total, res.data.length);
                        updateBadgeAndKPI(res.total);
                    } else if (res && res.html) {
                        $list.html(res.html);
                        $('#paginationcandidateinterviewtracker').html(res.pagination || '');
                    }
                },
                error: function(xhr, status) {
                    if (status === 'abort') return;
                    const active = activeRequests.get(cacheKey);
                    if (!active || active.requestId !== currentRequestId) return;

                    entry.state = 'ERROR';
                    $list.html(`
                        <div class="it-error-card">
                            <i class="fas fa-exclamation-circle me-1"></i> Failed to retrieve interviews.
                            <button type="button" class="btn btn-sm btn-link text-primary ms-2 p-0" id="btnRetryDirectInterviews">Retry</button>
                        </div>
                    `);
                    $('#btnRetryDirectInterviews').one('click', function() {
                        fetchInterviews(clientId, cursor, true, currentSearch);
                    });
                }
            });
        }

        let currentRawData = [];
        let activeFilter = 'all';

        function updateSubTabCounts(rows) {
            if (!Array.isArray(rows)) return;
            const total = rows.length;
            let upcoming = 0;
            let pending = 0;
            let completed = 0;
            let cancelled = 0;

            rows.forEach(function(r) {
                const att = r.attendance ? r.attendance.code : (r.available_status || 0);
                const fb = r.evaluation ? r.evaluation.code : (r.feedback || 0);
                if (att === 0 || att === 3) upcoming++;
                if (att === 1 && fb === 5) pending++;
                if (att === 1) completed++;
                if (att === 2 || att === 4 || att === 7) cancelled++;
            });

            $('#itFilterCountAll').text(total);
            $('#itFilterCountUpcoming').text(upcoming);
            $('#itFilterCountPending').text(pending);
            $('#itFilterCountCompleted').text(completed);
            $('#itFilterCountCancelled').text(cancelled);
        }

        function applyCurrentFilter() {
            if (!currentRawData || !currentRawData.length) {
                renderFilteredRows([]);
                return;
            }

            let filtered = currentRawData;
            if (activeFilter === 'upcoming') {
                filtered = currentRawData.filter(function(r) {
                    const att = r.attendance ? r.attendance.code : (r.available_status || 0);
                    return att === 0 || att === 3;
                });
            } else if (activeFilter === 'pending_feedback') {
                filtered = currentRawData.filter(function(r) {
                    const att = r.attendance ? r.attendance.code : (r.available_status || 0);
                    const fb = r.evaluation ? r.evaluation.code : (r.feedback || 0);
                    return att === 1 && fb === 5;
                });
            } else if (activeFilter === 'completed') {
                filtered = currentRawData.filter(function(r) {
                    const att = r.attendance ? r.attendance.code : (r.available_status || 0);
                    return att === 1;
                });
            } else if (activeFilter === 'cancelled') {
                filtered = currentRawData.filter(function(r) {
                    const att = r.attendance ? r.attendance.code : (r.available_status || 0);
                    return att === 2 || att === 4 || att === 7;
                });
            }

            renderFilteredRows(filtered);
        }

        function renderTableRows(rows) {
            currentRawData = Array.isArray(rows) ? rows : [];
            updateSubTabCounts(currentRawData);
            applyCurrentFilter();
        }

        function renderFilteredRows(rows) {
            const $list = $('#candidatetListinterviewtracker');
            if (!rows || !rows.length) {
                $list.html(`
                    <div class="cd-it-empty">
                        <i class="fas fa-calendar-times fs-2 text-gray-300 d-block mb-2"></i>
                        No interviews match this filter criteria.
                    </div>
                `);
                return;
            }

            const html = rows.map(function(intv) {
                const cand = intv.candidate || {};
                const fu = intv.followup || {};
                const job = intv.job || {};
                const sch = intv.schedule || {};
                const intvs = intv.interviewers_summary || {};
                const src = intv.source_info || {};
                const stg = intv.stage || {};
                const fb = intv.feedback_view || {};
                const att = intv.attendance || {};
                const evalData = intv.evaluation || {};

                const jobTitleInitial = (job.title || 'J').charAt(0).toUpperCase();

                // Followup pill
                let followupHtml = '';
                if (fu.label) {
                    followupHtml = `<span class="cd-followup-pill ${fu.style_class || 'confirmed'}">${fu.label}</span>`;
                }

                // Meeting link / Address
                let linkHtml = '';
                if (sch.meeting_link) {
                    if (sch.link_type === 'address') {
                        linkHtml = `
                            <span class="cd-it-link-pill" title="${sch.meeting_link}">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                <span>${sch.link_label || 'View Venue Address'}</span>
                            </span>
                        `;
                    } else {
                        linkHtml = `
                            <a href="${sch.meeting_link}" target="_blank" rel="noopener noreferrer" class="cd-it-link-pill">
                                <i class="fas fa-video me-1"></i>
                                <span>${sch.link_label || 'Join Interview Link'}</span>
                            </a>
                        `;
                    }
                }

                // Mode icon
                let modeIcon = '<i class="fas fa-video me-1"></i>';
                if (sch.mode_icon_type === 'phone') {
                    modeIcon = '<i class="fas fa-phone-alt me-1"></i>';
                } else if (sch.mode_icon_type === 'map-marker') {
                    modeIcon = '<i class="fas fa-building me-1"></i>';
                }

                // Rating html
                let ratingHtml = '<span class="text-muted fs-8 font-weight-normal">★ Rating Pending</span>';
                if (evalData.star_rating) {
                    ratingHtml = `★ ${evalData.star_rating} / 5.0 Rating`;
                }

                                // Panelist processing: Support multiple panelists & avatars elegantly
                let panelList = [];
                if (Array.isArray(intv.interviewers) && intv.interviewers.length > 0) {
                    panelList = intv.interviewers.map(function(p, idx) {
                        let pName = typeof p === 'object' && p !== null ? (p.name || p.interviewer_name || '') : String(p);
                        let pComp = typeof p === 'object' && p !== null ? (p.company_name || '') : '';
                        return {
                            name: pName.trim() || 'Interviewer',
                            company: pComp.trim(),
                            is_lead: idx === 0
                        };
                    }).filter(p => !!p.name);
                }

                if (!panelList.length) {
                    const leadFallback = (intvs.lead_name && intvs.lead_name !== 'N/A') ? intvs.lead_name : 'Panel Assigned';
                    panelList.push({
                        name: leadFallback,
                        company: intvs.lead_company || '',
                        is_lead: true
                    });
                    if (intvs.extra_count && intvs.extra_count > 0) {
                        for (let i = 1; i <= intvs.extra_count; i++) {
                            panelList.push({
                                name: `Panelist #${i + 1}`,
                                company: intvs.lead_company || '',
                                is_lead: false
                            });
                        }
                    }
                }

                const totalPanelists = panelList.length;
                const leadPanelist = panelList[0] || { name: 'Panel Assigned', company: '' };
                const leadName = leadPanelist.name || 'Panel Assigned';
                const leadInitial = (leadName && leadName !== 'Panel Assigned') ? leadName.charAt(0).toUpperCase() : '<i class="fas fa-user-tie fs-9"></i>';
                const leadCompany = leadPanelist.company || '';
                const extraPanelistsCount = Math.max(0, totalPanelists - 1);
                const panelBadgeText = totalPanelists === 1 ? '1 Panelist' : `${totalPanelists} Panelists`;

                // Build popover items HTML
                const popoverItemsHtml = panelList.map(function(p) {
                    return `
                        <div class="cd-it-popover-item">
                            <span class="text-truncate" style="max-width: 150px;" title="${p.name}">
                                <i class="fas ${p.is_lead ? 'fa-user-check text-info' : 'fa-user text-muted'} me-1"></i>
                                ${p.name}
                            </span>
                            ${p.is_lead ? '<span class="item-lead-badge">Lead</span>' : (p.company ? `<span class="text-muted fs-9">${p.company}</span>` : '')}
                        </div>
                    `;
                }).join('');

                const panelistTooltipText = panelList.map(p => p.is_lead ? `${p.name} (Lead)` : p.name).join(', ');

                // Schedule Time & Mode formatting (Time directly below Date: Time | Mode)
                const dateText = sch.date_formatted || '—';
                const timeText = sch.time_formatted || '';
                const modeText = sch.mode_label || 'Video Call';
                let timeModeHtml = '';
                if (timeText) {
                    timeModeHtml = `
                        <div class="cd-it-dt-time-mode">
                            <i class="far fa-clock me-1 text-primary"></i>
                            <span class="cd-it-dt-time-val">${timeText}</span>
                            <span class="cd-dot-sep">|</span>
                            ${modeIcon}
                            <span class="cd-it-dt-mode-val">${modeText}</span>
                        </div>
                    `;
                } else {
                    timeModeHtml = `
                        <div class="cd-it-dt-time-mode">
                            ${modeIcon}
                            <span class="cd-it-dt-mode-val">${modeText}</span>
                        </div>
                    `;
                }

                // Recommendation pill
                let recHtml = '';
                if (fb.recommendation && fb.recommendation.label) {
                    recHtml = `<span class="cd-it-rec-pill" style="background:${fb.recommendation.bg};color:${fb.recommendation.color};">${fb.recommendation.label}</span>`;
                } else if (fb.status_text && fb.status_text !== 'Pending') {
                    recHtml = `<span class="cd-it-rec-pill" style="background:#f1f5f9;color:${fb.status_color || '#64748b'};">${fb.status_text}</span>`;
                }

                return `
                    <div class="cd-it-card" data-interview-id="${intv.id || ''}" data-status="${att.code || 0}" data-feedback="${evalData.code || 0}">
                        <!-- Upper Discrete 6 Sub-Cards Grid -->
                        <div class="cd-it-card-grid">

                            <!-- 1. CANDIDATE INFO -->
                            <div class="cd-it-subcard">
                                <div class="cd-subcard-header">
                                    <span class="cd-subcard-label">CANDIDATE INFO</span>
                                    ${followupHtml}
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="cd-avatar-box ${cand.avatar_color_class || 'avatar-blue'}">
                                        ${cand.initial || 'C'}
                                    </div>
                                    <div class="min-w-0 flex-grow-1 overflow-hidden">
                                        <a href="javascript:void(0)" class="cd-it-cand-name client-show-application-detail js-view-candidate" data-row-id="${cand.application_id || cand.id || ''}" data-id="${cand.id || ''}" data-cand-id="${cand.id || ''}" title="${cand.name || 'Candidate'}">
                                            ${cand.name || 'Candidate'}
                                        </a>
                                        <div class="cd-it-cand-role" title="${cand.designation || '—'}">
                                            ${cand.designation || '—'}
                                        </div>
                                        <div class="cd-it-cand-company" title="${cand.current_company || ''}">
                                            <i class="far fa-building me-1 text-gray-400"></i>
                                            <span>${cand.current_company || '88 Technology'}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. TARGET REQUISITION -->
                            <div class="cd-it-subcard">
                                <div class="cd-subcard-header">
                                    <span class="cd-subcard-label">TARGET REQUISITION</span>
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="cd-badge-scope ${job.job_type_class || 'scope-direct'}">${job.job_type_label || 'Direct Client'}</span>
                                        <span class="cd-status-hiring">Hiring</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="cd-avatar-box avatar-purple">
                                        ${jobTitleInitial}
                                    </div>
                                    <div class="min-w-0 flex-grow-1 overflow-hidden">
                                        <a href="/service_company/jobs/${job.id || ''}" target="_blank" class="cd-it-job-title" title="${job.title || 'Job Position'}">
                                            ${job.title || 'Job Position'}
                                        </a>
                                        <div class="cd-it-job-meta">
                                            ${job.job_id_display ? `<span class="cd-it-job-id">${job.job_id_display}</span><span class="cd-dot-sep">•</span>` : ''}
                                            <span>Technology</span>
                                        </div>
                                        <div class="cd-it-job-emp" title="${job.display_company_name || job.client_name || ''}">
                                            ${job.display_company_name || job.client_name || 'Client Account'}
                                            <span class="cd-dot-sep">•</span>
                                            <span>💼 Full-Time</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. SCHEDULE & INTERVIEW LINK -->
                            <div class="cd-it-subcard">
                                <div class="cd-subcard-header">
                                    <span class="cd-subcard-label">SCHEDULE &amp; LINK</span>
                                </div>
                                <div>
                                    <div class="cd-it-dt-date">
                                        <i class="far fa-calendar-alt me-1 text-primary"></i>
                                        <span>${dateText}</span>
                                    </div>
                                    ${timeModeHtml}
                                    ${linkHtml}
                                </div>
                            </div>

                            <!-- 4. RECRUITER INFO -->
                            <div class="cd-it-subcard">
                                <div class="cd-subcard-header">
                                    <span class="cd-subcard-label">RECRUITER INFO</span>
                                </div>
                                <div>
                                    <div class="cd-it-recruiter-name" title="${src.name || '—'}">
                                        ${src.name || '—'}
                                    </div>
                                    <div class="cd-it-recruiter-comp" title="${src.company_name || ''}">
                                        ${src.company_name || 'Internal Team'}
                                    </div>
                                    <div class="cd-it-submitted-at">
                                        <i class="far fa-clock me-1"></i>
                                        <span>Submitted: ${src.submitted_at || 'Recently'}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- 5. DEDICATED PANELISTS CARD -->
                            <div class="cd-it-subcard cd-it-panelist-card">
                                <div class="cd-subcard-header">
                                    <span class="cd-subcard-label">PANELISTS</span>
                                    <span class="cd-it-panel-count-pill">${panelBadgeText}</span>
                                </div>
                                <div class="cd-it-panel-body" title="${panelistTooltipText}">
                                    <div class="cd-panel-avatar-stack">
                                        <div class="cd-panel-avatar" title="${leadName}">
                                            ${leadInitial}
                                        </div>
                                        ${extraPanelistsCount > 0 ? `
                                            <div class="cd-panel-avatar cd-panel-avatar-extra" title="+${extraPanelistsCount} more panelists">
                                                +${extraPanelistsCount}
                                            </div>
                                        ` : ''}
                                    </div>
                                    <div class="min-w-0 flex-grow-1 overflow-hidden">
                                        <div class="cd-it-lead-name" title="${leadName}">
                                            ${leadName}
                                        </div>
                                        <div class="cd-it-lead-sub" title="${leadCompany || 'Interview Panel'}">
                                            ${extraPanelistsCount > 0 ? `+${extraPanelistsCount} more panelist${extraPanelistsCount > 1 ? 's' : ''}` : (leadCompany || 'Interview Panel')}
                                        </div>
                                    </div>

                                    <!-- Clean Hover Popover -->
                                    <div class="cd-it-popover-card">
                                        <div class="cd-it-popover-header">
                                            <span><i class="fas fa-users text-primary me-1"></i> Interview Panel</span>
                                            <span class="badge badge-sm badge-light-primary text-primary">${totalPanelists}</span>
                                        </div>
                                        <div class="cd-it-popover-body">
                                            ${popoverItemsHtml}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 6. STATUS & FEEDBACK -->
                            <div class="cd-it-subcard">
                                <div class="cd-subcard-header">
                                    <span class="cd-subcard-label">STATUS &amp; FEEDBACK</span>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                        <span class="cd-it-att-pill ${att.css_class || 'cit-fb-scheduled'}">
                                            ${att.label || 'Scheduled'}
                                        </span>
                                        ${recHtml}
                                    </div>
                                    <div class="cd-it-rating">
                                        ${ratingHtml}
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- 6. BOTTOM ACTIVITY STRIP (CANDIDATE STAGE & SUBSTAGE) -->
                        <div class="cd-it-card-bottom">
                            <div class="cd-it-bottom-left">
                                <div class="d-flex align-items-center gap-1">
                                    <span class="cd-it-stage-lbl">CANDIDATE STAGE:</span>
                                    <span class="cd-it-stage-badge ${stg.badge_class || 'cd-stage-in-review'}">
                                        ${stg.master_status_name || 'In Review'}
                                    </span>
                                </div>
                                <span class="cd-dot-sep">•</span>
                                <div class="d-flex align-items-center gap-1">
                                    <span class="cd-it-stage-lbl">SUBSTAGE:</span>
                                    <span class="cd-it-substage-badge">
                                        ${stg.child_status_name || 'Private'}
                                    </span>
                                </div>
                                <span class="cd-dot-sep">•</span>
                                <span class="cd-it-source-subtext">
                                    Sourced via <strong>${src.company_name || 'Internal'}</strong>
                                </span>
                            </div>

                            <div class="cd-it-bottom-right">
                                <button type="button" class="btn btn-sm btn-light-primary py-1 px-3 client-show-application-detail js-view-candidate" data-row-id="${cand.application_id || cand.id || ''}" data-id="${cand.id || ''}" data-cand-id="${cand.id || ''}">
                                    Candidate Drawer <i class="fas fa-external-link-alt ms-1 fs-9"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            $list.html(html);
        }

        function renderPagination(cursorIndex, stackLength, nextCursor, total, currentCount) {
            const $pagi = $('#paginationcandidateinterviewtracker');
            const totalItems = typeof total === 'number' ? total : 0;
            const count = typeof currentCount === 'number' ? currentCount : 0;
            const hasPrev = cursorIndex > 0;
            const hasNext = Boolean(nextCursor);

            const fromItem = totalItems === 0 ? 0 : (cursorIndex * pageSize + 1);
            const toItem = totalItems === 0 ? 0 : Math.min(totalItems, cursorIndex * pageSize + count);

            $pagi.html(`
                <div class="cd-it-pagi-info">
                    Showing <strong>${fromItem} - ${toItem}</strong> of <strong>${totalItems}</strong> interviews
                </div>
                <div class="cd-it-pagi-controls">
                    <div class="d-flex align-items-center gap-2 me-2">
                        <span class="text-muted" style="font-size:11px;">Rows:</span>
                        <select class="cd-it-pagi-size-select" id="itPageSizeSelectDirect">
                            <option value="10" ${pageSize === 10 ? 'selected' : ''}>10 / page</option>
                            <option value="25" ${pageSize === 25 ? 'selected' : ''}>25 / page</option>
                            <option value="50" ${pageSize === 50 ? 'selected' : ''}>50 / page</option>
                        </select>
                    </div>
                    <button type="button" class="cd-it-pagi-btn ${!hasPrev ? 'disabled' : ''}" data-direction="prev">
                        <i class="fas fa-chevron-left" style="font-size:10px;"></i> Previous
                    </button>
                    <span class="cd-it-pagi-indicator">Page ${cursorIndex + 1}</span>
                    <button type="button" class="cd-it-pagi-btn ${!hasNext ? 'disabled' : ''}" data-direction="next">
                        Next <i class="fas fa-chevron-right" style="font-size:10px;"></i>
                    </button>
                </div>
            `);
        }

        function updateBadgeAndKPI(total) {
            if (typeof total !== 'undefined') {
                const $badge = $('#tabBadgeInterviewTracker');
                if ($badge.length) {
                    $badge.text(total).toggle(total > 0);
                }
                $('#kpiInterviewsScheduledCount').text(total);
                $('#itTotalCountBadgeDirect').text(total);
            }
        }

        function destroy() {
            activeRequests.forEach(function(req) {
                try {
                    if (req.controller && typeof req.controller.abort === 'function') {
                        req.controller.abort();
                    }
                } catch(e) {}
            });
            activeRequests.clear();
            store.clear();
            if (window.Echo) {
                subscriptions.forEach(function(channelName) {
                    try { window.Echo.leave(channelName); } catch(e) {}
                });
            }
            subscriptions.clear();
            $(document).off('.directInterviewPagination .directInterviewPageSize');
        }

        return {
            init: init,
            fetch: fetchInterviews,
            destroy: destroy
        };
    })();

    function directfetchCandidatesInverViewTracker(page = 1, search = '') {
        var activeSelectedId = window.ClientStateManager && typeof window.ClientStateManager.getSelectedClientId === 'function'
            ? window.ClientStateManager.getSelectedClientId()
            : (window.activeSelectedClientId || null);
        var directCurrentId = parseInt('{{ $directClientDetail->client_id ?: ($directClientDetail->from_company_id ?: $directClientDetail->company_id) }}', 10);
        if (activeSelectedId && directCurrentId && parseInt(activeSelectedId, 10) !== directCurrentId) {
            return;
        }
        if (directCurrentId && window.ClientInterviewStoreDirect) {
            window.ClientInterviewStoreDirect.fetch(directCurrentId, null, false, search);
        }
    }
    window.directfetchCandidatesInverViewTracker = directfetchCandidatesInverViewTracker;

    $(document).ready(function() {
        if (window.ClientInterviewStoreDirect) {
            window.ClientInterviewStoreDirect.init();
        }
    });

    function fetchJobsClientdirect(page = 1, search = '', status = 'all') {
        var activeSelectedId = window.ClientStateManager && typeof window.ClientStateManager.getSelectedClientId === 'function'
            ? window.ClientStateManager.getSelectedClientId()
            : (window.activeSelectedClientId || null);
        var directCurrentId = parseInt('{{ $directClientDetail->client_id ?: ($directClientDetail->from_company_id ?: $directClientDetail->company_id) }}', 10);
        if (activeSelectedId && directCurrentId && parseInt(activeSelectedId, 10) !== directCurrentId) {
            return;
        }

        if (window._activeJobsDirectXhr && window._activeJobsDirectXhr.readyState !== 4) {
            try { window._activeJobsDirectXhr.abort(); } catch(e){}
        }

        $('.page-loader').css('display', 'flex');
        var activeStatus = status || window.currentJobStatusDirect || 'all';
        var clientName = $('.cd-client-name').first().text().trim() || '{{ addslashes($detailName ?? "") }}';
        var clientId = $('#direct_client_id').val() || '{{ $directClientDetail->client_id ?? $directClientDetail->id ?? $dc_id ?? 0 }}';
        var fromCompanyId = '{{ $directClientDetail->company_id ?? $directClientDetail->from_company_id ?? $directClientDetail->client_id ?? $directClientDetail->id ?? $dc_id ?? 0 }}';
        var searchVal = (search !== undefined && search !== null && search !== '') ? search : ($('#kt_activitiesDirectClient #requisitions #inputJobSearch').val() || '');

        window._activeJobsDirectXhr = $.ajax({
            url: `{{ url('service_company/jobs_side_bar_job_ajax/data') }}?page=${page}`,
            type: 'GET',
            data: {
                search: searchVal,
                status: activeStatus,
                client_id: clientId,
                client_name: clientName,
                from_company_id: fromCompanyId,
                custom: false
            },
            success: function(response) {
                $('.page-loader').css('display', 'none');
                var curSelId = window.ClientStateManager && typeof window.ClientStateManager.getSelectedClientId === 'function'
                    ? window.ClientStateManager.getSelectedClientId()
                    : (window.activeSelectedClientId || null);
                if (curSelId && parseInt(curSelId, 10) !== directCurrentId) {
                    return;
                }
                $('#directclientJobsListajax').html(response.html);
                $('#directpaginationclientjobs').html(response.pagination);

                if (response.kpi) {
                    $('.cd-kpi-card[data-target-tab="#requisitions"] .cd-kpi-num').text(response.kpi.hiring);
                    $('a[href="#requisitions"] .cd-tab-badge').text(response.kpi.hiring);
                    $('#directFilterCountAll').text(response.kpi.total);
                    $('#directFilterCountHiring').text(response.kpi.hiring);
                    $('#directFilterCountHold').text(response.kpi.hold);
                    $('#directFilterCountClosed').text(response.kpi.closed);
                    $('#directFilterCountInactive').text(response.kpi.inactive);
                }
            },
            error: function() {
                $('.page-loader').css('display', 'none');
            }
        });
        return window._activeJobsDirectXhr;
    }
    window.fetchJobsClientdirect = fetchJobsClientdirect;

    // Direct Client Jobs Tab: Dynamic Search Bar (Keyup & Clear)
    var directJobSearchTimer = null;
    $(document).off('keyup.directDrawerJobs', '#kt_activitiesDirectClient #requisitions #inputJobSearch');
    $(document).on('keyup.directDrawerJobs', '#kt_activitiesDirectClient #requisitions #inputJobSearch', function() {
        clearTimeout(directJobSearchTimer);
        var searchVal = $(this).val();
        if (searchVal.length > 0) {
            $('#kt_activitiesDirectClient #requisitions #btnJobSearchClear').show();
        } else {
            $('#kt_activitiesDirectClient #requisitions #btnJobSearchClear').hide();
        }
        directJobSearchTimer = setTimeout(function() {
            fetchJobsClientdirect(1, searchVal, window.currentJobStatusDirect || 'all');
        }, 300);
    });

    $(document).off('click.directDrawerJobs', '#kt_activitiesDirectClient #requisitions #btnJobSearchClear');
    $(document).on('click.directDrawerJobs', '#kt_activitiesDirectClient #requisitions #btnJobSearchClear', function() {
        $('#kt_activitiesDirectClient #requisitions #inputJobSearch').val('');
        $(this).hide();
        fetchJobsClientdirect(1, '', window.currentJobStatusDirect || 'all');
    });

    // Direct Client Jobs Tab: Filter Button Interaction
    $(document).off('click.directDrawerJobs', '#kt_activitiesDirectClient #requisitions .js-job-filter-btn');
    $(document).on('click.directDrawerJobs', '#kt_activitiesDirectClient #requisitions .js-job-filter-btn', function(e) {
        if (e && typeof e.preventDefault === 'function') e.preventDefault();
        var status = $(this).data('status') || 'all';
        window.currentJobStatusDirect = status;
        $('#kt_activitiesDirectClient #requisitions .js-job-filter-btn').removeClass('active');
        $(this).addClass('active');
        if (window.__activeClientDrawer && window.__activeClientDrawer.coordinator) {
            window.__activeClientDrawer.coordinator.setFilter('jobs', status);
        }
        if (typeof fetchJobsClientdirect === 'function') {
            fetchJobsClientdirect(1, $('#kt_activitiesDirectClient #requisitions #inputJobSearch').val() || '', status);
        }
    });

    // Direct Client Jobs Tab: Click Routing (Fixing Drawer Click Collision Bug)
    $(document).off('click.directDrawerJobs', '#kt_activitiesDirectClient #directclientJobsListajax .js-job-drawer-trigger');
    $(document).on('click.directDrawerJobs', '#kt_activitiesDirectClient #directclientJobsListajax .js-job-drawer-trigger', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var jobId = $(this).data('job-id');
        if (!jobId) return;

        if (typeof openJobDetailsDrawer === 'function') {
            openJobDetailsDrawer(jobId);
        } else if (typeof JobStateManager !== 'undefined' && JobStateManager.openJobDrawer) {
            JobStateManager.openJobDrawer(jobId);
        } else {
            window.open(`{{ url('service_company/jobs/viewmore') }}/${jobId}`, '_blank');
        }
    });

    $(document).on('click.directDrawer', '#kt_activitiesDirectClient .cd-view-all-activity', function(e) {
        e.preventDefault();
        var $tabBtn = $('#kt_activitiesDirectClient .cd-tab-pill-btn[href="#recent_activity"]');
        if ($tabBtn.length) {
            $tabBtn.trigger('click');
        }
    });

    $(document).on('click.directDrawer', '#kt_activitiesDirectClient .cd-view-all-contacts', function(e) {
        e.preventDefault();
        var $tabBtn = $('#kt_activitiesDirectClient .cd-tab-pill-btn[href="#contacts"]');
        if ($tabBtn.length) {
            $tabBtn.trigger('click');
        }
    });

    $(document).on('click.directDrawer', '#kt_activitiesDirectClient .cd-btn-followup', function(e) {
        e.preventDefault();
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '<span style="font-size:14px;font-weight:600;">Client Follow Up</span>',
                input: 'textarea',
                inputLabel: 'Record follow-up notes, agenda or meeting minutes',
                inputPlaceholder: 'Enter follow-up details...',
                showCancelButton: true,
                confirmButtonText: 'Save Follow Up',
                confirmButtonColor: '#10b981',
                customClass: {
                    input: 'form-control form-control-sm',
                    confirmButton: 'btn btn-sm btn-success',
                    cancelButton: 'btn btn-sm btn-light'
                }
            }).then(function(res) {
                if (res.isConfirmed && res.value) {
                    Swal.fire({
                        icon: 'success',
                        title: '<span style="font-size:13.5px;font-weight:600;">Follow Up Recorded</span>',
                        text: 'Follow-up agenda has been saved.',
                        timer: 1800,
                        showConfirmButton: false
                    });
                }
            });
        }
    });

    $(document).on('click.directDrawer', '#kt_activitiesDirectClient .cd-btn-add-tag-trigger, #kt_activitiesDirectClient .cd-btn-edit-tags', function(e) {
        e.preventDefault();
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '<span style="font-size:14px;font-weight:600;">Client Tags</span>',
                input: 'text',
                inputLabel: 'Add or edit tags (comma-separated)',
                inputValue: '{{ implode(", ", $tagsArr) }}',
                showCancelButton: true,
                confirmButtonText: 'Save',
                confirmButtonColor: '#10b981',
                customClass: {
                    input: 'form-control form-control-sm',
                    confirmButton: 'btn btn-sm btn-success',
                    cancelButton: 'btn btn-sm btn-light'
                }
            }).then(function(res) {
                if (res.isConfirmed && res.value) {
                    Swal.fire({
                        icon: 'success',
                        title: '<span style="font-size:13.5px;font-weight:600;">Tags Updated</span>',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }
    });
</script>
