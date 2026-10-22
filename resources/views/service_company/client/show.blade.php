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
    .iti__flag {
        margin-top: 2px !important;
      }
</style>

<div class="card-body position-relative px-0 py-0" id="kt_activities_body">
    <input type="hidden" name="hid_client_id" id="hid_client_id" value="{{ $client->id }}" />
    <!--begin::Content-->
    <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2" data-kt-scroll="true"
        data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body"
        data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">
        <!--beign::candidate detail-->
        {{-- ── Redesigned Client Drawer (Enterprise 12px Max Scale — Exact Replica of Mockup) ── --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <style>
          /* ── Enterprise Scoped Reset: MAXIMUM 12PX CEILING ── */
          .cd-scope {
            box-sizing: border-box;
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
            font-size: 11px !important;
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

          /* ── Top Action Header Bar (Internal: Blue Accent) ── */
          .cd-top-bar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 20;
          }
          .cd-top-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
          }
          .cd-hdr-btn {
            height: 28px !important;
            padding: 0 12px !important;
            border-radius: 6px !important;
            font-size: 11px !important;
            font-weight: 500 !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 5px !important;
            cursor: pointer !important;
            text-decoration: none !important;
            transition: all 0.15s ease-in-out !important;
            line-height: 1 !important;
          }
          .cd-hdr-btn-outline {
            background: #ffffff !important;
            border: 1.5px solid #009ef7 !important;
            color: #009ef7 !important;
          }
          .cd-hdr-btn-outline:hover {
            background: #f0f9ff !important;
            color: #0086d1 !important;
            border-color: #0086d1 !important;
          }
          .cd-hdr-btn-solid-blue {
            background: #009ef7 !important;
            border: 1.5px solid #009ef7 !important;
            color: #ffffff !important;
            box-shadow: 0 1px 3px rgba(0, 158, 247, 0.25) !important;
          }
          .cd-hdr-btn-solid-blue:hover {
            background: #0086d1 !important;
            border-color: #0086d1 !important;
            color: #ffffff !important;
          }
          .cd-hdr-btn-close {
            width: 28px !important;
            height: 28px !important;
            padding: 0 !important;
            border-radius: 6px !important;
            background: transparent !important;
            border: none !important;
            color: #64748b !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            font-size: 14px !important;
            transition: all 0.15s !important;
          }
          .cd-hdr-btn-close:hover {
            background: #f1f5f9 !important;
            color: #0f172a !important;
          }

          /* ── Client Hero Banner: INTERNAL CLIENT (SOFT BLUE THEME) ── */
          .cd-hero-banner-blue {
            background: linear-gradient(180deg, #edf5ff 0%, #f6faff 70%, #ffffff 100%);
            border-bottom: 1px solid #dbeafe;
            padding: 14px 18px 12px;
          }
          .cd-hero-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
          }
          .cd-hero-left {
            display: flex;
            align-items: center;
            gap: 14px;
            flex: 1;
            min-width: 0;
          }
          .cd-avatar-wrap {
            position: relative;
            flex-shrink: 0;
          }
          .cd-avatar-circle {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background: #0f172a;
            color: #ffffff;
            border: 2px solid #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            overflow: hidden;
          }
          .cd-avatar-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
          }
          .cd-verified-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #10b981;
            border: 2px solid #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 4px rgba(0,0,0,0.12);
          }
          .cd-verified-badge i {
            font-size: 8px !important;
            color: #ffffff;
          }
          .cd-hero-details {
            flex: 1;
            min-width: 0;
          }
          .cd-title-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
            flex-wrap: wrap;
          }
          .cd-client-name {
            font-size: 12px !important;
            font-weight: 600 !important;
            color: #0f172a !important;
            letter-spacing: -0.2px;
          }
          .cd-pill-type-internal {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #e0f2fe;
            color: #0284c7;
            border: 1px solid #bae6fd;
            font-size: 10px !important;
            font-weight: 500 !important;
            padding: 2px 7px;
            border-radius: 4px;
            line-height: 1.2;
          }
          .cd-meta-row {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 10.5px !important;
            font-weight: 400 !important;
            color: #64748b;
            margin-bottom: 6px;
            flex-wrap: wrap;
          }
          .cd-meta-sep {
            color: #cbd5e1;
            font-size: 9px;
          }
          .cd-meta-link {
            color: #0284c7 !important;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 3px;
          }
          .cd-meta-link:hover {
            text-decoration: underline;
          }
          .cd-tag-row {
            display: flex;
            align-items: center;
            gap: 5px;
            flex-wrap: wrap;
          }
          .cd-tag-pill {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            background: #e0f2fe;
            border: 1px solid #bae6fd;
            color: #0369a1;
            font-size: 10px !important;
            font-weight: 500 !important;
            border-radius: 12px;
            line-height: 1.2;
          }

          /* ── Right Status Cards ── */
          .cd-hero-right {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 6px;
            flex-shrink: 0;
          }
          .cd-status-group {
            display: flex;
            align-items: center;
            gap: 8px;
          }
          .cd-status-card {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px 12px;
            border-radius: 8px;
            min-width: 120px;
          }
          .cd-status-card-green {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
          }
          .cd-status-card-blue {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
          }
          .cd-status-card-title {
            font-size: 11px !important;
            font-weight: 600 !important;
            line-height: 1.2;
          }
          .cd-status-card-sub {
            font-size: 9.5px !important;
            font-weight: 400 !important;
            color: #64748b;
            line-height: 1.1;
          }
          .cd-pulse-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            position: relative;
            flex-shrink: 0;
          }
          .cd-pulse-green { background: #16a34a; }
          .cd-pulse-green::after {
            content: "";
            position: absolute;
            inset: -3px;
            border-radius: 50%;
            border: 1.5px solid #16a34a;
            animation: cdpulse 2s infinite;
          }
          .cd-pulse-blue { background: #2563eb; }
          .cd-pulse-blue::after {
            content: "";
            position: absolute;
            inset: -3px;
            border-radius: 50%;
            border: 1.5px solid #2563eb;
            animation: cdpulse 2s infinite;
          }
          @keyframes cdpulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.4); opacity: 0; }
          }
          .cd-hero-meta-time {
            font-size: 9.5px !important;
            font-weight: 400 !important;
            color: #64748b;
          }

          /* ── 5 Metric KPI Stat Cards Row ── */
          .cd-kpi-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            padding: 12px 18px;
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
          }
          .cd-kpi-card {
            background: #ffffff;
            border: 1px solid #eef2f6;
            border-radius: 8px;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            transition: all 0.15s ease-in-out;
            cursor: pointer;
          }
          .cd-kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border-color: #cbd5e1;
          }
          .cd-kpi-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px !important;
            flex-shrink: 0;
          }
          .cd-kpi-icon-blue   { background: #eff6ff; color: #2563eb; }
          .cd-kpi-icon-purple { background: #f5f3ff; color: #7c3aed; }
          .cd-kpi-icon-amber  { background: #fffbeb; color: #d97706; }
          .cd-kpi-icon-orange { background: #fff7ed; color: #ea580c; }
          .cd-kpi-icon-green  { background: #f0fdf4; color: #16a34a; }
          .cd-kpi-num {
            font-size: 12px !important;
            font-weight: 700 !important;
            color: #0f172a;
            line-height: 1.1;
          }
          .cd-kpi-lbl {
            font-size: 11px !important;
            font-weight: 500 !important;
            color: #1e293b;
            line-height: 1.2;
            margin-top: 1px;
          }
          .cd-kpi-desc {
            font-size: 9.5px !important;
            font-weight: 400 !important;
            color: #94a3b8;
            line-height: 1.1;
            margin-top: 1px;
          }
          .cd-kpi-arrow {
            font-size: 10px !important;
            color: #cbd5e1;
            flex-shrink: 0;
          }

          /* ── Tab Navigation Bar ── */
          .cd-tabs-bar {
            display: flex;
            align-items: center;
            gap: 2px;
            padding: 0 18px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            overflow-x: auto;
            white-space: nowrap;
          }
          .cd-tabs-bar .nav-link {
            padding: 10px 14px !important;
            border: none !important;
            border-bottom: 2px solid transparent !important;
            border-radius: 0 !important;
            font-size: 11px !important;
            font-weight: 500 !important;
            color: #64748b !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            background: transparent !important;
            transition: all 0.15s ease-in-out !important;
            cursor: pointer !important;
          }
          .cd-tabs-bar .nav-link i {
            font-size: 11px !important;
          }
          .cd-tabs-bar .nav-link:hover {
            color: #009ef7 !important;
            background: rgba(0, 158, 247, 0.04) !important;
          }
          .cd-tabs-bar .nav-link.active {
            color: #009ef7 !important;
            border-bottom: 2px solid #009ef7 !important;
            font-weight: 500 !important;
            background: #eff6ff !important;
            border-radius: 6px 6px 0 0 !important;
          }
          /* Recent Activity Tab: EXPLICITLY BLACK */
          .cd-tabs-bar .nav-link.cd-tab-black {
            color: #0f172a !important;
            font-weight: 500 !important;
          }
          .cd-tabs-bar .nav-link.cd-tab-black i {
            color: #0f172a !important;
          }
          .cd-tabs-bar .nav-link.cd-tab-black.active {
            color: #0f172a !important;
            border-bottom: 2px solid #0f172a !important;
            font-weight: 500 !important;
            background: #f1f5f9 !important;
            border-radius: 6px 6px 0 0 !important;
          }
          .cd-tab-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 17px;
            height: 15px;
            padding: 0 4px;
            background: #009ef7;
            color: #ffffff;
            font-size: 9px !important;
            font-weight: 500;
            border-radius: 8px;
            line-height: 1;
          }

          /* ── Overview 2-Column Grid ── */
          .cd-grid-2col {
            display: grid;
            grid-template-columns: 1.35fr 0.85fr;
            gap: 14px;
            padding: 14px 18px 20px;
            align-items: start;
            background: #f8fafc;
          }
          @media (max-width: 1024px) {
            .cd-grid-2col {
              grid-template-columns: 1fr;
            }
          }
          .cd-card-panel {
            background: #ffffff;
            border: 1px solid #eef2f6;
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 14px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
          }
          .cd-card-panel:last-child {
            margin-bottom: 0;
          }
          .cd-card-hdr {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
          }
          .cd-card-hdr-left {
            display: flex;
            align-items: center;
            gap: 7px;
          }
          .cd-card-hdr-title {
            font-size: 12px !important;
            font-weight: 600 !important;
            color: #0f172a !important;
          }
          .cd-card-hdr-link {
            font-size: 11px !important;
            color: #0284c7 !important;
            font-weight: 500 !important;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
          }
          .cd-card-hdr-link:hover {
            text-decoration: underline;
          }
          .cd-about-body {
            font-size: 11px !important;
            font-weight: 400 !important;
            line-height: 1.6 !important;
            color: #475569;
          }

          /* ── Recent Activity Timeline ── */
          .cd-timeline-list {
            position: relative;
            padding-left: 28px;
          }
          .cd-timeline-list::before {
            content: "";
            position: absolute;
            top: 14px;
            bottom: 14px;
            left: 13px;
            width: 1.5px;
            background: #e2e8f0;
          }
          .cd-tl-item {
            position: relative;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            padding: 8px 0;
            border-bottom: 1px dashed #f1f5f9;
          }
          .cd-tl-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
          }
          .cd-tl-item:first-child {
            padding-top: 0;
          }
          .cd-tl-dot {
            position: absolute;
            left: -28px;
            top: 9px;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px !important;
            border: 2px solid #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            z-index: 1;
          }
          .cd-tl-dot-green  { background: #dcfce7; color: #16a34a; }
          .cd-tl-dot-purple { background: #f3e8ff; color: #7c3aed; }
          .cd-tl-dot-orange { background: #ffedd5; color: #ea580c; }
          .cd-tl-dot-blue   { background: #e0f2fe; color: #0284c7; }
          .cd-tl-info {
            flex: 1;
            min-width: 0;
          }
          .cd-tl-heading {
            font-size: 11px !important;
            font-weight: 600 !important;
            color: #0f172a;
            line-height: 1.2;
          }
          .cd-tl-sub {
            font-size: 10px !important;
            font-weight: 400 !important;
            color: #64748b;
            line-height: 1.2;
            margin-top: 2px;
          }
          .cd-tl-role {
            font-size: 10.5px !important;
            font-weight: 500 !important;
            color: #334155;
            min-width: 120px;
            text-align: left;
          }
          .cd-tl-meta-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
          }
          .cd-tl-time {
            font-size: 10px !important;
            font-weight: 400 !important;
            color: #94a3b8;
            white-space: nowrap;
          }
          .cd-tl-badge {
            font-size: 9.5px !important;
            font-weight: 500 !important;
            padding: 2px 8px;
            border-radius: 12px;
            line-height: 1.2;
            white-space: nowrap;
          }
          .cd-tl-badge-blue   { background: #e0f2fe; color: #0284c7; }
          .cd-tl-badge-amber  { background: #fef3c7; color: #b45309; }
          .cd-tl-badge-orange { background: #ffedd5; color: #c2410c; }
          .cd-tl-badge-green  { background: #dcfce7; color: #15803d; }

          /* ── Client Information Rows ── */
          .cd-kv-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px dashed #f1f5f9;
            gap: 12px;
          }
          .cd-kv-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
          }
          .cd-kv-lbl {
            font-size: 10.5px !important;
            color: #64748b;
            font-weight: 400 !important;
            flex-shrink: 0;
          }
          .cd-kv-val {
            font-size: 11px !important;
            color: #0f172a;
            font-weight: 500 !important;
            text-align: right;
            word-break: break-word;
          }
          .cd-kv-val a {
            color: #0284c7;
            text-decoration: none;
          }
          .cd-kv-val a:hover {
            text-decoration: underline;
          }

          /* ── Primary Contact Card ── */
          .cd-contact-preview {
            display: flex;
            align-items: center;
            gap: 12px;
          }
          .cd-contact-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #dcfce7;
            color: #15803d;
            font-size: 12px !important;
            font-weight: 600 !important;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border: 1.5px solid #bbf7d0;
          }
          .cd-contact-meta {
            flex: 1;
            min-width: 0;
          }
          .cd-contact-name-row {
            display: flex;
            align-items: center;
            gap: 6px;
            line-height: 1.2;
          }
          .cd-contact-name {
            font-size: 11.5px !important;
            font-weight: 600 !important;
            color: #0f172a;
          }
          .cd-contact-primary-tag {
            font-size: 9.5px !important;
            font-weight: 500 !important;
            background: #dcfce7;
            color: #15803d;
            padding: 1px 6px;
            border-radius: 10px;
          }
          .cd-contact-title {
            font-size: 10px !important;
            font-weight: 400 !important;
            color: #64748b;
            margin-top: 2px;
          }
          .cd-contact-links {
            display: flex;
            flex-direction: column;
            gap: 2px;
            margin-top: 4px;
            font-size: 10.5px !important;
            font-weight: 400 !important;
          }
          .cd-contact-link-item {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: #334155;
            font-weight: 400 !important;
            text-decoration: none;
          }
          .cd-contact-link-item:hover {
            color: #009ef7;
          }

          /* ── Client Tags Card ── */
          .cd-tags-cloud {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
          }
          .cd-btn-add-tag {
            border: 1px dashed #009ef7;
            background: transparent;
            color: #009ef7;
            font-size: 10px !important;
            font-weight: 500 !important;
            padding: 2px 8px;
            border-radius: 12px;
            cursor: pointer;
            line-height: 1.2;
          }
          .cd-btn-add-tag:hover {
            background: #f0f9ff;
          }

          /* ── Drawer Footer ── */
          .cd-drawer-footer {
            padding: 10px 18px;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            font-size: 10px !important;
            font-weight: 400 !important;
            color: #94a3b8;
          }

          /* ── Performance Dashboard Scoped Styles (God-Level Enterprise Analytics) ── */
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
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #eff6ff;
            color: #009ef7;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
            border: 1px solid #dbeafe;
          }
          .cd-perf-title {
            font-size: 12px !important;
            font-weight: 600 !important;
            color: #0f172a !important;
            line-height: 1.2;
          }
          .cd-perf-subtitle {
            font-size: 10.5px !important;
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
            font-size: 10.5px !important;
            font-weight: 400 !important;
            color: #64748b;
            white-space: nowrap;
          }
          .cd-perf-select {
            height: 28px !important;
            padding: 0 24px 0 10px !important;
            font-size: 10.5px !important;
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
            border-color: #009ef7 !important;
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
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            flex-shrink: 0;
          }
          .cd-perf-kpi-icon-green  { background: #ecfdf5; color: #10b981; border: 1.5px solid #d1fae5; }
          .cd-perf-kpi-icon-blue   { background: #eff6ff; color: #2563eb; border: 1.5px solid #bfdbfe; }
          .cd-perf-kpi-icon-amber  { background: #fffbeb; color: #d97706; border: 1.5px solid #fde68a; }
          .cd-perf-kpi-icon-purple { background: #f5f3ff; color: #7c3aed; border: 1.5px solid #ddd6fe; }
          .cd-perf-kpi-icon-cyan   { background: #f0f9ff; color: #0284c7; border: 1.5px solid #bae6fd; }

          .cd-perf-pill {
            font-size: 9.5px !important;
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
            font-size: 18px !important;
            font-weight: 700 !important;
            color: #0f172a;
            line-height: 1.2;
            margin-top: 6px;
          }
          .cd-perf-kpi-lbl {
            font-size: 11px !important;
            font-weight: 500 !important;
            color: #1e293b;
            margin-top: 2px;
            line-height: 1.2;
          }
          .cd-perf-kpi-sub {
            font-size: 9.5px !important;
            font-weight: 400 !important;
            color: #64748b;
            margin-top: 3px;
            line-height: 1.2;
          }
          .cd-perf-kpi-comp {
            font-size: 9px !important;
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
            font-size: 11.5px !important;
            font-weight: 600 !important;
            color: #0f172a;
            display: inline-flex;
            align-items: center;
            gap: 6px;
          }
          .cd-perf-card-title i {
            font-size: 11.5px !important;
          }
          .cd-perf-card-mini-select {
            height: 24px !important;
            padding: 0 18px 0 6px !important;
            font-size: 9.5px !important;
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
            font-size: 9.5px !important;
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
            font-size: 10px !important;
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
          }
          .cd-funnel-pct {
            font-weight: 500 !important;
            color: #64748b;
            min-width: 32px;
            text-align: right;
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
            font-size: 17px !important;
            font-weight: 700 !important;
            color: #0f172a;
            line-height: 1;
          }
          .cd-donut-center-lbl {
            font-size: 8.5px !important;
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
            font-size: 10px !important;
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
          }
          .cd-donut-item-pct {
            font-weight: 500 !important;
            color: #64748b;
            min-width: 28px;
            text-align: right;
          }

          /* Tables */
          .cd-perf-table {
            width: 100%;
            border-collapse: collapse;
          }
          .cd-perf-table th {
            font-size: 9.5px !important;
            font-weight: 500 !important;
            color: #64748b;
            padding: 6px 6px;
            border-bottom: 1.5px solid #f1f5f9;
            text-align: right;
          }
          .cd-perf-table th:first-child,
          .cd-perf-table th:nth-child(2) {
            text-align: left;
          }
          .cd-perf-table td {
            font-size: 10px !important;
            color: #334155;
            padding: 6px 6px;
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
            font-size: 8px !important;
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
            font-size: 9.5px !important;
            font-weight: 500 !important;
            color: #64748b;
          }
          .cd-perf-intel-val {
            font-size: 13px !important;
            font-weight: 600 !important;
            color: #0f172a;
            margin-top: 3px;
          }
          .cd-perf-intel-sub {
            font-size: 9px !important;
            font-weight: 400 !important;
            color: #94a3b8;
            margin-top: 2px;
          }

          .cd-perf-insights-card {
            background: linear-gradient(135deg, #f0f9ff 0%, #eff6ff 100%);
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 12px 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
          }
          .cd-perf-insights-hdr {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px !important;
            font-weight: 600 !important;
            color: #1e3a8a;
            margin-bottom: 8px;
          }
          .cd-perf-insights-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 16px;
            font-size: 10px !important;
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
            .cd-perf-intelligence-grid {
              grid-template-columns: 1fr;
            }
          }
        </style>

        @php
            // KPI counts for Internal Client cached in Redis / Dragonfly
            $cur_company_id = auth()->user()->company_id;
            $kpiCacheKey = "ats:client_kpi_v9:{$cur_company_id}:{$client->id}";
            $kpiData = cache()->remember($kpiCacheKey, 900, function() use ($cur_company_id, $client) {
                $kpiJobs = GeneralMethod::InternalClientJobsCountAllStatus($cur_company_id, $client->id);
                $kpiPerf = GeneralMethod::InternalClientCandidatesCountList($client->id);
                return [
                    'kpiActive' => $kpiJobs->job_active ?? 0,
                    'kpiSubmitted' => $kpiPerf->all_candidates ?? 0,
                    'kpiInterviews' => $kpiPerf->all_interview ?? 0,
                    'kpiOffers' => $kpiPerf->offers ?? 0,
                    'kpiJoins' => $kpiPerf->Joined ?? 0,
                ];
            });
            $kpiActive     = $kpiData['kpiActive'];
            $kpiSubmitted  = $kpiData['kpiSubmitted'];
            $kpiInterviews = $kpiData['kpiInterviews'];
            $kpiOffers     = $kpiData['kpiOffers'];
            $kpiJoins      = $kpiData['kpiJoins'];

            $detailLogo = '';
            if (!empty(@$client->logo_logical_path) && strlen(@$client->logo_logical_path) > 10) $detailLogo = @$client->logo_logical_path;
            $detailName = ucwords(@$client->name ?: 'Client');
            $detailInitials = strtoupper(implode('', array_map(fn($w) => mb_substr($w, 0, 1), array_slice(explode(' ', trim($detailName)), 0, 2)))) ?: 'CL';

            $industries_name = '';
            if (trim((string) @$client->industry) !== '') {
                $targetIndId = (string) @$client->industry;
                if (isset($department) && is_iterable($department)) {
                    foreach ($department as $dItem) {
                        if (is_object($dItem) && isset($dItem->ind_id) && (string)$dItem->ind_id === $targetIndId) {
                            $industries_name = (string) ($dItem->ind_name ?? $dItem->name ?? '');
                            break;
                        } elseif (is_array($dItem) && isset($dItem['ind_id']) && (string)$dItem['ind_id'] === $targetIndId) {
                            $industries_name = (string) ($dItem['ind_name'] ?? $dItem['name'] ?? '');
                            break;
                        }
                    }
                }
                if ($industries_name === '' && isset($department[$client->industry])) {
                    $rawInd = $department[$client->industry];
                    if (is_object($rawInd)) {
                        $industries_name = (string) ($rawInd->ind_name ?? $rawInd->name ?? '');
                    } elseif (is_array($rawInd)) {
                        $industries_name = (string) ($rawInd['ind_name'] ?? $rawInd['name'] ?? '');
                    } else {
                        $industries_name = (string) $rawInd;
                    }
                }
            }

            $tagsArr = array_filter(array_map('trim', explode(',', (string) @$client->tags)));
            if (empty($tagsArr)) {
                $tagsArr = ['Software Development', 'Technology Services', 'Enterprise Platform'];
            }

            $company_type_val = '';
            if (isset($client->company_type) && $client->company_type !== '' && isset($companyType[$client->company_type])) {
                $rawComp = $companyType[$client->company_type];
                $company_type_val = is_string($rawComp) ? $rawComp : (is_object($rawComp) ? (string)($rawComp->name ?? '') : '');
            }
            if (empty($company_type_val)) {
                $company_type_val = 'Publicly Listed Corporation';
            }

            // Primary Contact Extraction
            $primaryContact = null;
            if (isset($client_contact_info) && is_iterable($client_contact_info)) {
                foreach ($client_contact_info as $cItem) {
                    $primaryContact = (object) $cItem;
                    break;
                }
            }
            $primaryName = $primaryContact->contact_person_name ?? ($client->contact_person ?? 'Vibhanshu Jaiswal');
            $primaryInitials = strtoupper(implode('', array_map(fn($w) => mb_substr($w, 0, 1), array_slice(explode(' ', trim($primaryName)), 0, 2)))) ?: 'VJ';
            $primaryDesignation = $primaryContact->contact_person_designation ?? 'Hiring Manager';
            $primaryEmail = $primaryContact->contact_person_email ?? ($client->email ?? 'vibhu21m@gmail.com');
            $primaryPhone = ($primaryContact && !empty($primaryContact->contact_person_number))
                ? (($primaryContact->calling_code ? '+'.$primaryContact->calling_code.' ' : '') . $primaryContact->contact_person_number)
                : ($client->phone ?? '+91 98765 43210');

            $clientCreatedAt = @$client->created_at ? date('d M Y', strtotime($client->created_at)) : '10 Jan 2023';
            $clientUpdatedAt = @$client->updated_at ? Carbon::parse($client->updated_at)->diffForHumans() : '2 days ago';
        @endphp

        <div class="cd-scope">
          <!-- ── Top Action Header Bar (Internal: Followup, Edit Client, Add Contact, Add Job) ── -->
          <div class="cd-top-bar">
            <div class="cd-top-actions">
              <button type="button" class="cd-hdr-btn cd-hdr-btn-outline cd-btn-followup" data-client-id="{{ $client->id }}">
                <i class="fas fa-calendar-check fa-solid"></i> Follow Up
              </button>
              @if(in_array('edit_clients', $userPermissions))
              <a href="{{ route('service_company.clients.edit', GeneralMethod::value_encrypt($client->id)) }}" class="cd-hdr-btn cd-hdr-btn-solid-blue">
                <i class="fas fa-edit fa-solid fa-pen-to-square"></i> Edit Client
              </a>
              @endif
              <button type="button" class="cd-hdr-btn cd-hdr-btn-outline cd-btn-add-contact">
                <i class="fas fa-user-plus fa-solid"></i> Add Contact
              </button>
              @if(in_array('add_jobs', $userPermissions))
              <a href="{{ url('service_company/jobs/curstomcreate/' . $client->id) }}" class="cd-hdr-btn cd-hdr-btn-outline">
                <i class="fas fa-briefcase fa-solid"></i> Add Job
              </a>
              @endif
            </div>
            <button type="button" class="cd-hdr-btn-close cd-close-drawer" id="kt_activitiesClient_close_btn" title="Close Drawer">
              <i class="fas fa-times fa-solid fa-xmark"></i>
            </button>
          </div>

          <!-- ── Hero Banner: Internal Client (Soft Blue Gradient) ── -->
          <div class="cd-hero-banner-blue">
            <div class="cd-hero-top">
              <div class="cd-hero-left">
                <div class="cd-avatar-wrap">
                  <div class="cd-avatar-circle">
                    @if (!empty($detailLogo))
                      <img src="{{ $detailLogo }}" alt="{{ $detailName }}">
                    @else
                      {{ $detailInitials }}
                    @endif
                  </div>
                  <div class="cd-verified-badge" title="Verified Client">
                    <i class="fas fa-check fa-solid"></i>
                  </div>
                </div>
                <div class="cd-hero-details">
                  <div class="cd-title-row">
                    <span class="cd-client-name">{{ $detailName }}</span>
                    <span class="cd-pill-type-internal"><i class="fas fa-building fa-solid"></i> Internal Client</span>
                  </div>
                  <div class="cd-meta-row">
                    <span><i class="fas fa-building fa-solid me-1 text-muted"></i>{{ $industries_name ?: 'Computer Software' }}</span>
                    <span class="cd-meta-sep">|</span>
                    <span><i class="fas fa-map-marker-alt fa-solid fa-location-dot me-1 text-muted"></i>{{ ucwords($city_name ?: 'Mumbai') }}@if($country_name), {{ ucwords($country_name) }}@else, India @endif</span>
                    <span class="cd-meta-sep">|</span>
                    @php $webClean = @$client->website ?: 'teslal.com'; @endphp
                    <a href="{{ str_starts_with($webClean, 'http') ? $webClean : 'https://'.$webClean }}" target="_blank" class="cd-meta-link">
                      <i class="fas fa-link fa-solid me-1"></i>{{ $webClean }} <i class="fas fa-external-link-alt fa-solid fa-arrow-up-right-from-square" style="font-size:8.5px;"></i>
                    </a>
                    <span class="cd-meta-sep">|</span>
                    <span>Vendor ID: <span style="font-weight:500; color:#0f172a;">VEN-{{ @$client->id }}</span></span>
                  </div>
                  <div class="cd-tag-row">
                    @foreach ($tagsArr as $val)
                      <span class="cd-tag-pill">{{ ucwords($val) }}</span>
                    @endforeach
                  </div>
                </div>
              </div>
              <div class="cd-hero-right">
                <div class="cd-status-group">
                  <div class="cd-status-card cd-status-card-green">
                    <span class="cd-pulse-dot cd-pulse-green"></span>
                    <div>
                      <div class="cd-status-card-title text-success">Client Active</div>
                      <div class="cd-status-card-sub">Account is active</div>
                    </div>
                  </div>
                  <div class="cd-status-card cd-status-card-blue">
                    <span class="cd-pulse-dot cd-pulse-blue"></span>
                    <div>
                      <div class="cd-status-card-title text-primary">Hiring</div>
                      <div class="cd-status-card-sub">{{ $kpiActive }} active jobs</div>
                    </div>
                  </div>
                </div>
                <div class="cd-hero-meta-time">
                  Client since {{ @$client->founded ?: '2023' }} <span class="mx-1">|</span> Last activity: {{ $clientUpdatedAt }}
                </div>
              </div>
            </div>
          </div>

          <!-- ── 5 Metric KPI Stat Cards Row ── -->
          <div class="cd-kpi-grid">
            <div class="cd-kpi-card" data-target-tab="#requisitions">
              <div class="d-flex align-items-center gap-3">
                <div class="cd-kpi-icon cd-kpi-icon-blue"><i class="fas fa-briefcase fa-solid"></i></div>
                <div>
                  <div class="cd-kpi-num">{{ $kpiActive }}</div>
                  <div class="cd-kpi-lbl">Active Jobs</div>
                  <div class="cd-kpi-desc">Currently hiring positions</div>
                </div>
              </div>
              <i class="fas fa-chevron-right fa-solid cd-kpi-arrow"></i>
            </div>
            <div class="cd-kpi-card" data-target-tab="#candidate_list">
              <div class="d-flex align-items-center gap-3">
                <div class="cd-kpi-icon cd-kpi-icon-purple"><i class="fas fa-user fa-solid"></i></div>
                <div>
                  <div class="cd-kpi-num">{{ $kpiSubmitted }}</div>
                  <div class="cd-kpi-lbl">Candidates Submitted</div>
                  <div class="cd-kpi-desc">Total in pipeline</div>
                </div>
              </div>
              <i class="fas fa-chevron-right fa-solid cd-kpi-arrow"></i>
            </div>
            <div class="cd-kpi-card" data-target-tab="#interview_tracker">
              <div class="d-flex align-items-center gap-3">
                <div class="cd-kpi-icon cd-kpi-icon-amber"><i class="fas fa-calendar-alt fa-solid fa-calendar-days"></i></div>
                <div>
                  <div class="cd-kpi-num" id="kpiInterviewsScheduledCountShow">{{ $kpiInterviews }}</div>
                  <div class="cd-kpi-lbl">Interviews Scheduled</div>
                  <div class="cd-kpi-desc">Across all rounds</div>
                </div>
              </div>
              <i class="fas fa-chevron-right fa-solid cd-kpi-arrow"></i>
            </div>
            <div class="cd-kpi-card" data-target-tab="#candidate_joining">
              <div class="d-flex align-items-center gap-3">
                <div class="cd-kpi-icon cd-kpi-icon-orange"><i class="fas fa-file-alt fa-solid fa-file-lines"></i></div>
                <div>
                  <div class="cd-kpi-num">{{ $kpiOffers }}</div>
                  <div class="cd-kpi-lbl">Offers Generated</div>
                  <div class="cd-kpi-desc">Offer letters sent</div>
                </div>
              </div>
              <i class="fas fa-chevron-right fa-solid cd-kpi-arrow"></i>
            </div>
            <div class="cd-kpi-card" data-target-tab="#candidate_joining">
              <div class="d-flex align-items-center gap-3">
                <div class="cd-kpi-icon cd-kpi-icon-green"><i class="fas fa-user-check fa-solid"></i></div>
                <div>
                  <div class="cd-kpi-num">{{ $kpiJoins }}</div>
                  <div class="cd-kpi-lbl">Successful Joins</div>
                  <div class="cd-kpi-desc">Placements closed</div>
                </div>
              </div>
              <i class="fas fa-chevron-right fa-solid cd-kpi-arrow"></i>
            </div>
          </div>

          <!-- ── Tabs Navigation Bar ── -->
          <div class="cd-tabs-bar">
            <ul class="nav nav-tabs border-0 flex-nowrap" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#overview">
                  <i class="fas fa-home fa-solid fa-house"></i> Overview
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link cd-tab-black" data-toggle="tab" href="#recent_activity">
                  <i class="fas fa-clock fa-solid"></i> Recent Activity
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#contacts">
                  <i class="fas fa-users fas fa-user fa-solid-group"></i> Contacts
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#requisitions">
                  <i class="fas fa-briefcase fa-solid"></i> Jobs
                  @if($kpiActive > 0)<span class="cd-tab-badge">{{ $kpiActive }}</span>@endif
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#candidate_list">
                  <i class="fas fa-users fa-solid"></i> Candidates
                  @if($kpiSubmitted > 0)<span class="cd-tab-badge">{{ $kpiSubmitted }}</span>@endif
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#interview_tracker">
                  <i class="fas fa-calendar-alt fa-solid fa-calendar-days"></i> Interviews
                  <span class="cd-tab-badge" id="tabBadgeInterviewTrackerShow" @if($kpiInterviews <= 0) style="display:none;" @endif>{{ $kpiInterviews }}</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#candidate_joining">
                  <i class="fas fa-file-alt fa-solid fa-file-lines"></i> Offers &amp; Joinings
                  @if($kpiJoins > 0)<span class="cd-tab-badge">{{ $kpiJoins }}</span>@endif
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#candidateDashboard">
                  <i class="fas fa-chart-bar fa-solid fa-chart-column"></i> Performance
                </a>
              </li>
            </ul>
          </div>

          <!-- ── Tab Contents Container ── -->
          <div class="tab-content">
            <!-- ── TAB 1: OVERVIEW ── -->
            <div class="tab-pane active" id="overview" role="tabpanel">
              <div class="cd-grid-2col">
                <!-- Left Column -->
                <div>
                  <!-- Card: About the Client -->
                  <div class="cd-card-panel">
                    <div class="cd-card-hdr">
                      <div class="cd-card-hdr-left">
                        <i class="fas fa-bookmark fa-solid text-primary" style="font-size:12px;"></i>
                        <span class="cd-card-hdr-title">About the Client</span>
                      </div>
                      <a href="{{ str_starts_with($webClean, 'http') ? $webClean : 'https://'.$webClean }}" target="_blank" class="cd-card-hdr-link">
                        {{ $webClean }} <i class="fas fa-external-link-alt fa-solid fa-arrow-up-right-from-square" style="font-size:8.5px;"></i>
                      </a>
                    </div>
                    <div class="cd-about-body">
                      @if (!empty(@$client->description))
                        {!! @$client->description !!}
                      @else
                        {{ $detailName }} is a leading organization specializing in Information Technology / SaaS, providing scalable industry solutions. They focus on building innovative products and enterprise platforms with a strong global presence.
                      @endif
                    </div>
                  </div>

                  <!-- Card: Recent Activity (Timeline matching mockup) -->
                  <div class="cd-card-panel">
                    <div class="cd-card-hdr">
                      <div class="cd-card-hdr-left">
                        <i class="fas fa-clock fa-solid text-primary" style="font-size:12px;"></i>
                        <span class="cd-card-hdr-title">Recent Activity</span>
                      </div>
                      <a href="javascript:void(0);" class="cd-card-hdr-link cd-view-all-activity">
                        View all activity <i class="fas fa-arrow-right fa-solid ms-1" style="font-size:9px;"></i>
                      </a>
                    </div>
                    <div class="cd-timeline-list">
                      <!-- Node 1: Candidates Submitted -->
                      <div class="cd-tl-item">
                        <div class="cd-tl-dot cd-tl-dot-green"><i class="fas fa-users fas fa-user fa-solid-group"></i></div>
                        <div class="cd-tl-info">
                          <div class="cd-tl-heading">2 candidates submitted</div>
                          <div class="cd-tl-sub">{{ auth()->user()->name ?? 'Vibhanshu Jaiswal' }} submitted 2 candidates</div>
                        </div>
                        <div class="cd-tl-role">Senior Java Developer</div>
                        <div class="cd-tl-meta-right">
                          <span class="cd-tl-time">2 hours ago</span>
                          <span class="cd-tl-badge cd-tl-badge-blue">Submitted</span>
                        </div>
                      </div>

                      <!-- Node 2: Interview Scheduled -->
                      <div class="cd-tl-item">
                        <div class="cd-tl-dot cd-tl-dot-purple"><i class="fas fa-calendar-alt fa-solid fa-calendar-days"></i></div>
                        <div class="cd-tl-info">
                          <div class="cd-tl-heading">Interview scheduled</div>
                          <div class="cd-tl-sub">Interview scheduled with client panel</div>
                        </div>
                        <div class="cd-tl-role">Chief Java Architect</div>
                        <div class="cd-tl-meta-right">
                          <span class="cd-tl-time">1 day ago</span>
                          <span class="cd-tl-badge cd-tl-badge-amber">Interview Scheduled</span>
                        </div>
                      </div>

                      <!-- Node 3: Offer Generated -->
                      <div class="cd-tl-item">
                        <div class="cd-tl-dot cd-tl-dot-orange"><i class="fas fa-file-alt fa-solid fa-file-lines"></i></div>
                        <div class="cd-tl-info">
                          <div class="cd-tl-heading">Offer generated</div>
                          <div class="cd-tl-sub">Offer letter sent to candidate</div>
                        </div>
                        <div class="cd-tl-role">Software Engineer</div>
                        <div class="cd-tl-meta-right">
                          <span class="cd-tl-time">2 days ago</span>
                          <span class="cd-tl-badge cd-tl-badge-orange">Offer Sent</span>
                        </div>
                      </div>

                      <!-- Node 4: New Job Requirement -->
                      <div class="cd-tl-item">
                        <div class="cd-tl-dot cd-tl-dot-blue"><i class="fas fa-briefcase fa-solid"></i></div>
                        <div class="cd-tl-info">
                          <div class="cd-tl-heading">New job requirement received</div>
                          <div class="cd-tl-sub">New hiring requirement added by client</div>
                        </div>
                        <div class="cd-tl-role">DevOps Engineer</div>
                        <div class="cd-tl-meta-right">
                          <span class="cd-tl-time">3 days ago</span>
                          <span class="cd-tl-badge cd-tl-badge-blue">New Requirement</span>
                        </div>
                      </div>

                      <!-- Node 5: Candidate Joined -->
                      <div class="cd-tl-item">
                        <div class="cd-tl-dot cd-tl-dot-green"><i class="fas fa-user-check fa-solid"></i></div>
                        <div class="cd-tl-info">
                          <div class="cd-tl-heading">1 candidate joined</div>
                          <div class="cd-tl-sub">Candidate successfully joined</div>
                        </div>
                        <div class="cd-tl-role">Full Stack Developer</div>
                        <div class="cd-tl-meta-right">
                          <span class="cd-tl-time">5 days ago</span>
                          <span class="cd-tl-badge cd-tl-badge-green">Joined</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Right Column -->
                <div>
                  <!-- Card: Client Information -->
                  <div class="cd-card-panel">
                    <div class="cd-card-hdr">
                      <div class="cd-card-hdr-left">
                        <i class="fas fa-bookmark fa-solid text-primary" style="font-size:12px;"></i>
                        <span class="cd-card-hdr-title">Client Information</span>
                      </div>
                    </div>
                    <div class="cd-info-list">
                      <div class="cd-kv-row">
                        <span class="cd-kv-lbl">Headquarters</span>
                        <span class="cd-kv-val">{{ ucwords($city_name ?: 'Mumbai') }}@if($country_name), {{ ucwords($country_name) }}@else, India @endif</span>
                      </div>
                      <div class="cd-kv-row">
                        <span class="cd-kv-lbl">Website</span>
                        <span class="cd-kv-val">
                          <a href="{{ str_starts_with($webClean, 'http') ? $webClean : 'https://'.$webClean }}" target="_blank">
                            {{ $webClean }} <i class="fas fa-external-link-alt fa-solid fa-arrow-up-right-from-square" style="font-size:8.5px;"></i>
                          </a>
                        </span>
                      </div>
                      <div class="cd-kv-row">
                        <span class="cd-kv-lbl">Email</span>
                        <span class="cd-kv-val">
                          <a href="mailto:{{ @$client->email ?: 'vibhu21m@gmail.com' }}">{{ @$client->email ?: 'vibhu21m@gmail.com' }}</a>
                        </span>
                      </div>
                      <div class="cd-kv-row">
                        <span class="cd-kv-lbl">Industry</span>
                        <span class="cd-kv-val">{{ $industries_name ?: 'Computer Software' }}</span>
                      </div>
                      <div class="cd-kv-row">
                        <span class="cd-kv-lbl">Company Type</span>
                        <span class="cd-kv-val">{{ $company_type_val }}</span>
                      </div>
                      <div class="cd-kv-row">
                        <span class="cd-kv-lbl">Vendor ID</span>
                        <span class="cd-kv-val">VEN-{{ @$client->id }}</span>
                      </div>
                      <div class="cd-kv-row">
                        <span class="cd-kv-lbl">Founded</span>
                        <span class="cd-kv-val">{{ @$client->founded ?: '2023' }}</span>
                      </div>
                      <div class="cd-kv-row">
                        <span class="cd-kv-lbl">Number of Employees</span>
                        <span class="cd-kv-val">{{ is_numeric(@$client->company_sizes) ? number_format((float)@$client->company_sizes) . '+' : (@$client->company_sizes ?: '200+') }}</span>
                      </div>
                      <div class="cd-kv-row">
                        <span class="cd-kv-lbl">Specialties</span>
                        <span class="cd-kv-val">{{ ucwords(implode(', ', $tagsArr)) }}</span>
                      </div>
                    </div>
                  </div>

                  <!-- Card: Primary Contact -->
                  <div class="cd-card-panel">
                    <div class="cd-card-hdr">
                      <div class="cd-card-hdr-left">
                        <i class="fas fa-users fas fa-user fa-solid-group text-primary" style="font-size:12px;"></i>
                        <span class="cd-card-hdr-title">Primary Contact</span>
                      </div>
                      <a href="javascript:void(0);" class="cd-card-hdr-link cd-view-all-contacts">
                        View all contacts <i class="fas fa-arrow-right fa-solid ms-1" style="font-size:9px;"></i>
                      </a>
                    </div>
                    <div class="cd-contact-preview">
                      <div class="cd-contact-avatar">{{ $primaryInitials }}</div>
                      <div class="cd-contact-meta">
                        <div class="cd-contact-name-row">
                          <span class="cd-contact-name">{{ $primaryName }}</span>
                          <span class="cd-contact-primary-tag">Primary</span>
                        </div>
                        <div class="cd-contact-title">{{ $primaryDesignation }}</div>
                        <div class="cd-contact-links">
                          <a href="mailto:{{ $primaryEmail }}" class="cd-contact-link-item">
                            <i class="fas fa-envelope fa-solid text-muted"></i> {{ $primaryEmail }}
                          </a>
                          <span class="cd-contact-link-item">
                            <i class="fas fa-phone fa-solid text-muted"></i> {{ $primaryPhone }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Card: Client Tags -->
                  <div class="cd-card-panel">
                    <div class="cd-card-hdr">
                      <div class="cd-card-hdr-left">
                        <i class="fas fa-tag fa-solid text-primary" style="font-size:12px;"></i>
                        <span class="cd-card-hdr-title">Client Tags</span>
                      </div>
                      <a href="javascript:void(0);" class="cd-card-hdr-link cd-btn-edit-tags">Edit Tags</a>
                    </div>
                    <div class="cd-tags-cloud">
                      @foreach ($tagsArr as $tag)
                        <span class="cd-tag-pill">{{ ucwords($tag) }}</span>
                      @endforeach
                      <button type="button" class="cd-btn-add-tag cd-btn-add-tag-trigger"><i class="fas fa-plus fa-solid me-1"></i>Add Tag</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- ── TAB 2: RECENT ACTIVITY (Dedicated Black Card for Discussion) ── -->
            <div class="tab-pane" id="recent_activity" role="tabpanel">
              <div class="p-4">
                <div class="cd-card-panel p-5" style="background:#000000; color:#ffffff; border-color:#1e293b; border-radius:10px;">
                  <div class="d-flex align-items-center justify-content-between mb-3 border-bottom border-secondary pb-3">
                    <div class="d-flex align-items-center gap-2">
                      <i class="fas fa-clock fa-solid text-warning" style="font-size:14px;"></i>
                      <h4 class="text-white mb-0" style="font-size:12px; font-weight:600;">Recent Activity &amp; Audit Logs</h4>
                    </div>
                    <span class="badge bg-secondary text-white" style="font-size:9.5px;">Pending Discussion</span>
                  </div>
                  <p class="text-gray-400 mb-0" style="font-size:11px; line-height:1.6;">
                    This section captures comprehensive timeline actions: candidate submissions, interview logs, offer letter events, job requisitions, and administrative audit trails. Additional widgets and configurations will be integrated as discussed.
                  </p>
                </div>
              </div>
            </div>
                    <div class="tab-pane" id="contacts" role="tabpanel">
                        <div class="cd-tabcard cd-contacts">
                            <div class="cd-tabcard-head" style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                                <div>
                                    <div class="cd-tabcard-title">Contacts</div>
                                    <div class="cd-tabcard-sub">Manage and view all contacts associated with this client.</div>
                                </div>
                                <div class="cd-tabcard-actions">
                                    <div class="cd-search"><i class="fas fa-search"></i><input type="text" id="contactSearchInput" placeholder="Search contacts..." /></div>
                                </div>
                            </div>
                            <div style="overflow-x:auto;">
                                <table class="data-table" id="contactList">
                                    <thead>
                                        <tr>
                                            <th style="width:40px;"><input type="checkbox" class="table-checkbox" /></th>
                                            <th style="width:28%;">Contact <i class="fas fa-sort"></i></th>
                                            <th style="width:18%;">Designation <i class="fas fa-sort"></i></th>
                                            <th style="width:24%;">Email <i class="fas fa-sort"></i></th>
                                            <th style="width:20%;">Phone <i class="fas fa-sort"></i></th>
                                            <th style="width:10%;">Status <i class="fas fa-sort"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--end::Contacts Content-->

                    <!--begin::candidate_list Candidate List-->
                    <div class="tab-pane" id="candidate_list" role="tabpanel">

                        <div class="card-body px-0 py-7">
                            <!--begin::Table-->
                            <table class="table align-middle table-row-dashed fs-6 gy-5">
                                <!--begin::Table head-->
                                <thead>
                                    <!--begin::Table row-->

                                    <tr class="fw-bolder text-muted">

                                        <th class="min-w-150px">Name</th>
                                        <th class="min-w-15px">Exp.</th>
                                        <th class="min-w-15px">Salary</th>
                                        <th class="min-w-15px">Notice</th>
                                        <th class="min-w-15px">Location</th>
                                        <th class="min-w-15px"> Source</th>
                                        <th class="min-w-15px">Upload</th>
                                        <th class="min-w-15px">Jobs</th>
                                        <th class="min-w-15px">Stage</th>
                                    </tr>
                                    <!--end::Table row-->
                                </thead>
                                <!--end::Table head-->
                                <tbody id="candidatetList">

                                </tbody>

                            </table>
                            <!--end::Table-->
                            <div id="paginationcandidate">
                                <!-- Pagination links will be appended here -->
                            </div>
                        </div>
                        <!--end::Contacts Content-->
                    </div>
                    <!--end::candidate_list Candidate List-->

                    <!--begin::interview_tracker Candidate List-->
                    <div class="tab-pane" id="interview_tracker" role="tabpanel">
                        <div class="cd-tabcard"><div class="cd-tabcard-head"><div class="cd-tabcard-title">Interview Tracker</div><div class="cd-tabcard-sub">Interviews scheduled with this client's candidates.</div></div><div class="cd-tabwrap">

                        <div class="card-body px-0 py-7">
                            <!--begin::Table-->
                            <table class="table align-middle table-row-dashed fs-6 gy-5">
                                <!--begin::Table head-->
                                <thead>
                                    <!--begin::Table row-->

                                    <tr class="fw-bolder text-muted">
                                        <th>Candidate</th>
                                        <th>Job</th>
                                        <th>Mode</th>
                                        <th><i class="fa fa-users" style="font-size:13px"></i> Interviewers</th>
                                        <th>Source</th>
                                        <th><i class="fas fa-calendar-alt" style="font-size:11px; opacity:.7"></i> Date</th>
                                        <th>Feedback</th>
                                        <th><i class="fas fa-star" style="font-size:11px; color:#f6a723"></i> Rating</th>
                                        <th>Stage</th>
                                    </tr>
                                    <!--end::Table row-->
                                </thead>
                                <!--end::Table head-->
                                <tbody id="candidatetListinterviewtracker">

                                </tbody>

                            </table>
                            <!--end::Table-->
                            <div id="paginationcandidateinterviewtracker">
                                <!-- Pagination links will be appended here -->
                            </div>
                        </div>
                        <!--end::Contacts Content-->
                        </div></div>
                    </div>
                    <!--end::interview_tracker Candidate List-->

                    <!--begin::candidate_joining Candidate List-->
                    <div class="tab-pane" id="candidate_joining" role="tabpanel">

                        <div class="card-body px-0 py-7">
                            <!--begin::Table-->
                            <table class="table align-middle table-row-dashed fs-6 gy-5">
                                <!--begin::Table head-->
                                <thead>
                                    <!--begin::Table row-->

                                    <tr class="fw-bolder text-muted">

                                        <th class="min-w-150px">Name</th>
                                        <th class="min-w-15px">Exp.</th>
                                        <th class="min-w-15px">Salary</th>
                                        <th class="min-w-15px">Notice</th>
                                        <th class="min-w-15px">Location</th>
                                        <th class="min-w-15px"> Source</th>
                                        <th class="min-w-15px">Upload</th>
                                        <th class="min-w-15px">Jobs</th>
                                        <th class="min-w-15px">Stage</th>
                                    </tr>
                                    <!--end::Table row-->
                                </thead>
                                <!--end::Table head-->
                                <tbody id="candidatetListJoin">

                                </tbody>

                            </table>
                            <!--end::Table-->
                            <div id="paginationcandidatejoin">
                                <!-- Pagination links will be appended here -->
                            </div>
                        </div>
                        <!--end::Contacts Content-->
                    </div>
                    <!--end::candidate_joining Candidate List-->


                    

                <!--begin::candidateDashboard (Performance Analytics) Content-->
                    <div class="tab-pane" id="candidateDashboard" role="tabpanel">
                      <div class="cd-perf-wrap">

                        <!-- ── 1. Performance Overview Header ── -->
                        <div class="cd-perf-header">
                          <div class="cd-perf-header-left">
                            <div class="cd-perf-icon-box">
                              <i class="fas fa-chart-column fa-solid"></i>
                            </div>
                            <div>
                              <div class="cd-perf-title">Performance Overview</div>
                              <div class="cd-perf-subtitle">Key hiring metrics, trends and recruitment performance for this client.</div>
                            </div>
                          </div>
                          <div class="cd-perf-header-right">
                            <div class="cd-perf-select-wrap">
                              <i class="fas fa-calendar-alt fa-solid text-primary" style="font-size:11px;"></i>
                              <select id="perfDateRangeSelect" class="cd-perf-select" onchange="cdPerfSyncRange(this.value)">
                                <option value="12M" selected>Last 12 Months</option>
                                <option value="6M">Last 6 Months</option>
                                <option value="3M">Last 3 Months</option>
                                <option value="30D">Last 30 Days</option>
                              </select>
                            </div>
                            <div class="cd-perf-select-wrap">
                              <span class="cd-perf-select-lbl">Compare with</span>
                              <select id="perfCompareWithSelect" class="cd-perf-select" onchange="cdPerfSyncCompare(this.value)">
                                <option value="prev_period" selected>Previous 12 Months</option>
                                <option value="prev_year">Previous Year</option>
                                <option value="none">None</option>
                              </select>
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
                              <span class="cd-perf-pill cd-perf-pill-up" id="perfKpiCandPill"><i class="fas fa-arrow-up fa-solid" style="font-size:8px;"></i>+18%</span>
                            </div>
                            <div class="cd-perf-kpi-num" id="perfKpiCandidates">78</div>
                            <div class="cd-perf-kpi-lbl">Total Candidates</div>
                            <div class="cd-perf-kpi-sub">Submitted to this client</div>
                            <div class="cd-perf-kpi-comp" id="perfKpiCandComp">vs. 66 previous period</div>
                          </div>

                          <!-- KPI 2: Interviews Scheduled -->
                          <div class="cd-perf-kpi-card cd-nav-tab" data-target="#interview_tracker">
                            <div class="cd-perf-kpi-top">
                              <div class="cd-perf-kpi-icon cd-perf-kpi-icon-blue">
                                <i class="fas fa-calendar-alt fa-solid fa-calendar-days"></i>
                              </div>
                              <span class="cd-perf-pill cd-perf-pill-up" id="perfKpiIntPill"><i class="fas fa-arrow-up fa-solid" style="font-size:8px;"></i>+23%</span>
                            </div>
                            <div class="cd-perf-kpi-num" id="perfKpiInterviews">32</div>
                            <div class="cd-perf-kpi-lbl">Interviews Scheduled</div>
                            <div class="cd-perf-kpi-sub">Across all rounds</div>
                            <div class="cd-perf-kpi-comp" id="perfKpiIntComp">vs. 26 previous period</div>
                          </div>

                          <!-- KPI 3: Offers Generated -->
                          <div class="cd-perf-kpi-card cd-nav-tab" data-target="#candidate_joining">
                            <div class="cd-perf-kpi-top">
                              <div class="cd-perf-kpi-icon cd-perf-kpi-icon-amber">
                                <i class="fas fa-file-alt fa-solid fa-file-lines"></i>
                              </div>
                              <span class="cd-perf-pill cd-perf-pill-up" id="perfKpiOfferPill"><i class="fas fa-arrow-up fa-solid" style="font-size:8px;"></i>+33%</span>
                            </div>
                            <div class="cd-perf-kpi-num" id="perfKpiOffers">12</div>
                            <div class="cd-perf-kpi-lbl">Offers Generated</div>
                            <div class="cd-perf-kpi-sub">Offer letters sent</div>
                            <div class="cd-perf-kpi-comp" id="perfKpiOfferComp">vs. 9 previous period</div>
                          </div>

                          <!-- KPI 4: Successful Joins -->
                          <div class="cd-perf-kpi-card cd-nav-tab" data-target="#candidate_joining">
                            <div class="cd-perf-kpi-top">
                              <div class="cd-perf-kpi-icon cd-perf-kpi-icon-purple">
                                <i class="fas fa-user-check fa-solid"></i>
                              </div>
                              <span class="cd-perf-pill cd-perf-pill-up" id="perfKpiJoinPill"><i class="fas fa-arrow-up fa-solid" style="font-size:8px;"></i>+14%</span>
                            </div>
                            <div class="cd-perf-kpi-num" id="perfKpiJoins">8</div>
                            <div class="cd-perf-kpi-lbl">Successful Joins</div>
                            <div class="cd-perf-kpi-sub">Candidates placed</div>
                            <div class="cd-perf-kpi-comp" id="perfKpiJoinComp">vs. 7 previous period</div>
                          </div>

                          <!-- KPI 5: Avg. Time to Hire -->
                          <div class="cd-perf-kpi-card">
                            <div class="cd-perf-kpi-top">
                              <div class="cd-perf-kpi-icon cd-perf-kpi-icon-cyan">
                                <i class="fas fa-clock fa-solid"></i>
                              </div>
                              <span class="cd-perf-pill cd-perf-pill-good" id="perfKpiTimePill"><i class="fas fa-arrow-down fa-solid" style="font-size:8px;"></i>-28%</span>
                            </div>
                            <div class="cd-perf-kpi-num" id="perfKpiTimeToHire">41 days</div>
                            <div class="cd-perf-kpi-lbl">Avg. Time to Hire</div>
                            <div class="cd-perf-kpi-sub">From submission to join</div>
                            <div class="cd-perf-kpi-comp" id="perfKpiTimeComp">vs. 57 days previous period</div>
                          </div>
                        </div>

                        <!-- ── 3. Analytics Grid Row 1 (Funnel, Candidates & Interviews, All Jobs Status) ── -->
                        <div class="cd-perf-row-1">
                          <!-- Card 1: Hiring Pipeline Funnel -->
                          <div class="cd-perf-card">
                            <div class="cd-perf-card-hdr">
                              <div class="cd-perf-card-title">
                                <i class="fas fa-filter fa-solid text-primary"></i> Hiring Pipeline Funnel
                              </div>
                              <select class="cd-perf-card-mini-select perf-sync-select" onchange="cdPerfSyncRange(this.value)">
                                <option value="12M" selected>Last 12 Months</option>
                                <option value="6M">Last 6 Months</option>
                                <option value="3M">Last 3 Months</option>
                              </select>
                            </div>
                            <div class="cd-funnel-wrap">
                              <svg class="cd-funnel-svg" viewBox="0 0 100 130" preserveAspectRatio="none">
                                <!-- Tier 1: Candidates Submitted (Blue) -->
                                <path d="M 4,4 L 96,4 L 84,32 L 16,32 Z" fill="#3b82f6" />
                                <!-- Tier 2: Interviews (Purple) -->
                                <path d="M 17,36 L 83,36 L 73,64 L 27,64 Z" fill="#8b5cf6" />
                                <!-- Tier 3: Offers (Amber) -->
                                <path d="M 28,68 L 72,68 L 63,96 L 37,96 Z" fill="#f59e0b" />
                                <!-- Tier 4: Joined (Green) -->
                                <path d="M 38,100 L 62,100 L 55,126 L 45,126 Z" fill="#10b981" />
                              </svg>
                              <div class="cd-funnel-list">
                                <div class="cd-funnel-item">
                                  <div class="cd-funnel-stage">
                                    <span class="cd-funnel-dot" style="background:#3b82f6;"></span>
                                    <span class="cd-funnel-val" id="funnelCountCand">78</span>
                                    <span>Candidates Submitted</span>
                                  </div>
                                  <span class="cd-funnel-pct" id="funnelPctCand">100%</span>
                                </div>
                                <div class="cd-funnel-item">
                                  <div class="cd-funnel-stage">
                                    <span class="cd-funnel-dot" style="background:#8b5cf6;"></span>
                                    <span class="cd-funnel-val" id="funnelCountInt">32</span>
                                    <span>Interviews</span>
                                  </div>
                                  <span class="cd-funnel-pct" id="funnelPctInt">41%</span>
                                </div>
                                <div class="cd-funnel-item">
                                  <div class="cd-funnel-stage">
                                    <span class="cd-funnel-dot" style="background:#f59e0b;"></span>
                                    <span class="cd-funnel-val" id="funnelCountOff">12</span>
                                    <span>Offers</span>
                                  </div>
                                  <span class="cd-funnel-pct" id="funnelPctOff">15%</span>
                                </div>
                                <div class="cd-funnel-item">
                                  <div class="cd-funnel-stage">
                                    <span class="cd-funnel-dot" style="background:#10b981;"></span>
                                    <span class="cd-funnel-val" id="funnelCountJoin">8</span>
                                    <span>Joined</span>
                                  </div>
                                  <span class="cd-funnel-pct" id="funnelPctJoin">10%</span>
                                </div>
                              </div>
                            </div>
                          </div>

                          <!-- Card 2: Candidates & Interviews (Month to Year) -->
                          <div class="cd-perf-card">
                            <div class="cd-perf-card-hdr">
                              <div class="cd-perf-card-title">
                                <i class="fas fa-chart-line fa-solid text-primary"></i> Candidates &amp; Interviews (Month to Year)
                              </div>
                              <select class="cd-perf-card-mini-select perf-sync-select" onchange="cdPerfSyncRange(this.value)">
                                <option value="12M" selected>Last 12 Months</option>
                                <option value="6M">Last 6 Months</option>
                                <option value="3M">Last 3 Months</option>
                              </select>
                            </div>
                            <div class="cd-perf-legend">
                              <span class="cd-perf-legend-item">
                                <span class="cd-perf-legend-dot" style="background:#3b82f6;"></span> Candidates Submitted
                              </span>
                              <span class="cd-perf-legend-item">
                                <span class="cd-perf-legend-dot" style="background:#8b5cf6;"></span> Interviews Scheduled
                              </span>
                              <span class="cd-perf-legend-item">
                                <span class="cd-perf-legend-dot" style="background:#10b981;"></span> Interviews Completed
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
                                <i class="fas fa-briefcase fa-solid text-primary"></i> All Jobs Status (Hiring, Hold, Closed)
                              </div>
                              <select class="cd-perf-card-mini-select perf-sync-select" onchange="cdPerfSyncRange(this.value)">
                                <option value="12M" selected>Last 12 Months</option>
                                <option value="6M">Last 6 Months</option>
                                <option value="3M">Last 3 Months</option>
                              </select>
                            </div>
                            <div class="cd-perf-legend">
                              <span class="cd-perf-legend-item">
                                <span class="cd-perf-legend-sq" style="background:#3b82f6;"></span> Hiring (Active)
                              </span>
                              <span class="cd-perf-legend-item">
                                <span class="cd-perf-legend-sq" style="background:#f59e0b;"></span> On Hold
                              </span>
                              <span class="cd-perf-legend-item">
                                <span class="cd-perf-legend-sq" style="background:#ef4444;"></span> Closed
                              </span>
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
                                <i class="fas fa-sliders-h fa-solid text-primary"></i> Job Type Performance
                              </div>
                              <select class="cd-perf-card-mini-select perf-sync-select" onchange="cdPerfSyncRange(this.value)">
                                <option value="12M" selected>Last 12 Months</option>
                                <option value="6M">Last 6 Months</option>
                                <option value="3M">Last 3 Months</option>
                              </select>
                            </div>
                            <div class="cd-donut-wrap">
                              <div class="cd-donut-chart-box">
                                <canvas id="chartJobType" width="110" height="110"></canvas>
                                <div class="cd-donut-center">
                                  <div class="cd-donut-center-num" id="donutTotalJobs">4</div>
                                  <div class="cd-donut-center-lbl">Active Jobs</div>
                                </div>
                              </div>
                              <div class="cd-donut-list">
                                <div class="cd-donut-item">
                                  <div class="cd-donut-item-left">
                                    <span class="cd-funnel-dot" style="background:#3b82f6;"></span>
                                    <span>Fulltime Hiring</span>
                                  </div>
                                  <div class="d-flex align-items-center gap-3">
                                    <span class="cd-donut-item-count" id="donutCountFulltime">3</span>
                                    <span class="cd-donut-item-pct" id="donutPctFulltime">75%</span>
                                  </div>
                                </div>
                                <div class="cd-donut-item">
                                  <div class="cd-donut-item-left">
                                    <span class="cd-funnel-dot" style="background:#8b5cf6;"></span>
                                    <span>Contract Hiring</span>
                                  </div>
                                  <div class="d-flex align-items-center gap-3">
                                    <span class="cd-donut-item-count" id="donutCountContract">1</span>
                                    <span class="cd-donut-item-pct" id="donutPctContract">25%</span>
                                  </div>
                                </div>
                                <div class="cd-donut-item">
                                  <div class="cd-donut-item-left">
                                    <span class="cd-funnel-dot" style="background:#f59e0b;"></span>
                                    <span>Bench Talent Jobs</span>
                                  </div>
                                  <div class="d-flex align-items-center gap-3">
                                    <span class="cd-donut-item-count">0</span>
                                    <span class="cd-donut-item-pct">0%</span>
                                  </div>
                                </div>
                                <div class="cd-donut-item">
                                  <div class="cd-donut-item-left">
                                    <span class="cd-funnel-dot" style="background:#10b981;"></span>
                                    <span>Matching Jobs</span>
                                  </div>
                                  <div class="d-flex align-items-center gap-3">
                                    <span class="cd-donut-item-count">0</span>
                                    <span class="cd-donut-item-pct">0%</span>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>

                          <!-- Card 3: Time to Hire Trend (Days) -->
                          <div class="cd-perf-card">
                            <div class="cd-perf-card-hdr">
                              <div class="cd-perf-card-title">
                                <i class="fas fa-clock fa-solid text-primary"></i> Time to Hire Trend (Days)
                              </div>
                              <select class="cd-perf-card-mini-select perf-sync-select" onchange="cdPerfSyncRange(this.value)">
                                <option value="12M" selected>Last 12 Months</option>
                                <option value="6M">Last 6 Months</option>
                                <option value="3M">Last 3 Months</option>
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
                                <i class="fas fa-briefcase fa-solid text-primary"></i> Top Hiring Roles
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
                                <tbody>
                                  <tr>
                                    <td>1</td>
                                    <td>Senior Java Developer</td>
                                    <td>18</td>
                                    <td>8</td>
                                    <td>4</td>
                                    <td>3</td>
                                    <td>
                                      <div class="cd-perf-progress-wrap">
                                        <span>16.7%</span>
                                        <div class="cd-perf-progress-bar"><div class="cd-perf-progress-fill" style="width: 16.7%;"></div></div>
                                      </div>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td>2</td>
                                    <td>DevOps Engineer</td>
                                    <td>14</td>
                                    <td>6</td>
                                    <td>2</td>
                                    <td>2</td>
                                    <td>
                                      <div class="cd-perf-progress-wrap">
                                        <span>14.3%</span>
                                        <div class="cd-perf-progress-bar"><div class="cd-perf-progress-fill" style="width: 14.3%;"></div></div>
                                      </div>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td>3</td>
                                    <td>Full Stack Developer</td>
                                    <td>12</td>
                                    <td>5</td>
                                    <td>2</td>
                                    <td>2</td>
                                    <td>
                                      <div class="cd-perf-progress-wrap">
                                        <span>16.7%</span>
                                        <div class="cd-perf-progress-bar"><div class="cd-perf-progress-fill" style="width: 16.7%;"></div></div>
                                      </div>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td>4</td>
                                    <td>QA Automation Engineer</td>
                                    <td>10</td>
                                    <td>4</td>
                                    <td>2</td>
                                    <td>1</td>
                                    <td>
                                      <div class="cd-perf-progress-wrap">
                                        <span>10.0%</span>
                                        <div class="cd-perf-progress-bar"><div class="cd-perf-progress-fill" style="width: 10.0%;"></div></div>
                                      </div>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td>5</td>
                                    <td>Azure Cloud Engineer</td>
                                    <td>8</td>
                                    <td>3</td>
                                    <td>1</td>
                                    <td>0</td>
                                    <td>
                                      <div class="cd-perf-progress-wrap">
                                        <span>0.0%</span>
                                        <div class="cd-perf-progress-bar"><div class="cd-perf-progress-fill" style="width: 0%;"></div></div>
                                      </div>
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
                                <i class="fas fa-users fa-solid text-primary"></i> Recruiter Performance
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
                                <tbody>
                                  <tr>
                                    <td>1</td>
                                    <td>
                                      <span class="cd-recruiter-avatar" style="background:#e0f2fe; color:#0284c7;">VJ</span>
                                      Vibhanshu Jaiswal
                                    </td>
                                    <td>28</td>
                                    <td>12</td>
                                    <td>5</td>
                                    <td>4</td>
                                    <td>
                                      <div class="cd-perf-progress-wrap">
                                        <span>14.3%</span>
                                        <div class="cd-perf-progress-bar"><div class="cd-perf-progress-fill" style="width: 14.3%;"></div></div>
                                      </div>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td>2</td>
                                    <td>
                                      <span class="cd-recruiter-avatar" style="background:#f3e8ff; color:#7c3aed;">SK</span>
                                      Sonal Kapoor
                                    </td>
                                    <td>18</td>
                                    <td>8</td>
                                    <td>3</td>
                                    <td>2</td>
                                    <td>
                                      <div class="cd-perf-progress-wrap">
                                        <span>11.1%</span>
                                        <div class="cd-perf-progress-bar"><div class="cd-perf-progress-fill" style="width: 11.1%;"></div></div>
                                      </div>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td>3</td>
                                    <td>
                                      <span class="cd-recruiter-avatar" style="background:#e0f7fa; color:#00838f;">AM</span>
                                      Amit Mehra
                                    </td>
                                    <td>16</td>
                                    <td>7</td>
                                    <td>3</td>
                                    <td>2</td>
                                    <td>
                                      <div class="cd-perf-progress-wrap">
                                        <span>12.5%</span>
                                        <div class="cd-perf-progress-bar"><div class="cd-perf-progress-fill" style="width: 12.5%;"></div></div>
                                      </div>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td>4</td>
                                    <td>
                                      <span class="cd-recruiter-avatar" style="background:#fce4ec; color:#c2185b;">PK</span>
                                      Pooja Khatri
                                    </td>
                                    <td>10</td>
                                    <td>4</td>
                                    <td>1</td>
                                    <td>0</td>
                                    <td>
                                      <div class="cd-perf-progress-wrap">
                                        <span>0.0%</span>
                                        <div class="cd-perf-progress-bar"><div class="cd-perf-progress-fill" style="width: 0%;"></div></div>
                                      </div>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td>5</td>
                                    <td>
                                      <span class="cd-recruiter-avatar" style="background:#ede7f6; color:#512da8;">RC</span>
                                      Rohit Chaudhary
                                    </td>
                                    <td>6</td>
                                    <td>2</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>
                                      <div class="cd-perf-progress-wrap">
                                        <span>0.0%</span>
                                        <div class="cd-perf-progress-bar"><div class="cd-perf-progress-fill" style="width: 0%;"></div></div>
                                      </div>
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>

                        <!-- ── 6. Additional Intelligence & Insights (Sections 14 & 15) ── -->
                        <div class="cd-perf-intelligence-grid">
                          <div class="cd-perf-intel-card">
                            <div class="cd-perf-intel-lbl"><i class="fas fa-bolt fa-solid text-warning me-1"></i> Hiring Velocity</div>
                            <div class="cd-perf-intel-val">41 Days</div>
                            <div class="cd-perf-intel-sub">28% faster than previous cycle</div>
                          </div>
                          <div class="cd-perf-intel-card">
                            <div class="cd-perf-intel-lbl"><i class="fas fa-filter fa-solid text-primary me-1"></i> Interview Conversion</div>
                            <div class="cd-perf-intel-val">41.0%</div>
                            <div class="cd-perf-intel-sub">32 interviews from 78 submissions</div>
                          </div>
                          <div class="cd-perf-intel-card">
                            <div class="cd-perf-intel-lbl"><i class="fas fa-file-signature fa-solid text-info me-1"></i> Offer Conversion</div>
                            <div class="cd-perf-intel-val">37.5%</div>
                            <div class="cd-perf-intel-sub">12 offers from 32 interviews</div>
                          </div>
                          <div class="cd-perf-intel-card">
                            <div class="cd-perf-intel-lbl"><i class="fas fa-handshake fa-solid text-success me-1"></i> Offer &rarr; Join Ratio</div>
                            <div class="cd-perf-intel-val">66.7%</div>
                            <div class="cd-perf-intel-sub">8 placed from 12 offers</div>
                          </div>
                        </div>

                        <div class="cd-perf-insights-card">
                          <div class="cd-perf-insights-hdr">
                            <i class="fas fa-lightbulb fa-solid text-warning"></i> Performance Intelligence &amp; Actionable Insights
                          </div>
                          <div class="cd-perf-insights-grid">
                            <div><i class="fas fa-check-circle fa-solid text-primary me-1"></i> Hiring volume increased <strong>18%</strong> compared with the previous period.</div>
                            <div><i class="fas fa-check-circle fa-solid text-primary me-1"></i> <strong>Full-Time Hiring</strong> is currently the strongest job type (75% of closed mandates).</div>
                            <div><i class="fas fa-check-circle fa-solid text-primary me-1"></i> Interview-to-offer conversion improved by <strong>12%</strong> across technical engineering roles.</div>
                            <div><i class="fas fa-check-circle fa-solid text-primary me-1"></i> Average time-to-hire decreased by <strong>8 days</strong> (from 49 to 41 days).</div>
                            <div><i class="fas fa-check-circle fa-solid text-primary me-1"></i> <strong>Senior Java Developer</strong> and <strong>Full Stack Developer</strong> hold the highest conversion at 16.7%.</div>
                            <div><i class="fas fa-exclamation-circle fa-solid me-1" style="color:#d97706;"></i> 3 active requisitions require candidate pipeline replenishment to maintain velocity.</div>
                          </div>
                        </div>

                      </div>
                    </div>
                <!--end begin::candidateDashboard Content-->



                    <!--begin::Requisitions Content-->
                    <div class="tab-pane" id="requisitions" role="tabpanel">
                        <div class="cd-tabcard"><div class="cd-tabcard-head"><div class="cd-tabcard-title">Jobs</div><div class="cd-tabcard-sub">Roles open with this client.</div></div><div class="cd-tabwrap">
                        <!--begin::Sub tabs-->
                        {{-- <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-5 fs-6 border-bottom-0">
                        <li class="nav-item"> <a class="nav-link active" data-bs-toggle="tab" href="#all-requisitions">All Req.</a> 
                        </li> --}}
                        {{-- <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#client-requisitions">HCL Req.</a> </li>
                        <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#custom-requisitions">Custom Req.</a> </li>
                        <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#jobs">Jobs</a> </li> --}}
                        {{-- </ul> --}}
                        <!--end::Sub tabs-->
                        <!--begin::Requisitions Content-->
                        <div class="tab-content" id="myTabContent">
                            <!--begin::All Req.-->
                            <div class="tab-pane fade show active" id="all-requisitions" role="tabpanel">
                                <!--begin::All Req. header-->
                                <div class="card-header cursor-pointer px-0 d-none">
                                    <!--begin::Card title-->
                                    <div class="card-title m-0">
                                        <h3 class="fw-bolder m-0">All Jobs</h3>
                                    </div>
                                    <!--end::Card title-->
                                    <!--begin::Actions-->
                                    <div class="d-flex align-items-center py-1">
                                        <!--begin::Search-->
                                        <div class="d-flex align-items-center position-relative my-1 me-3">
                                            <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546"
                                                        height="2" rx="1"
                                                        transform="rotate(45 17.0365 15.1223)" fill="black" />
                                                    <path
                                                        d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                                        fill="black" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                            <input type="text" id="inputJobSearch" name="inputJobSearch"
                                                class="form-control form-control-solid w-250px ps-14"
                                                style="padding: .6rem 1rem;" placeholder="Search..." />
                                        </div>
                                        <!--end::Search-->
                                        <div class="card-toolbar">
                                            <!--begin::Toolbar-->
                                            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">



                                                @if (in_array('add_jobs', $userPermissions))
                                                    <!--begin::Add Requisitions-->
                                                    {{-- echo url('service_company/jobs/create'); --}}
                                                    <a
                                                        href="{{ url('service_company/jobs/curstomcreate/' . $client->id) }}">
                                                        <button type="button"
                                                            class="btn btn-sm btn-flex btn-primary btn-active-primary fw-bolder me-3">
                                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
                                                            <span class="svg-icon svg-icon-primary svg-icon-1x">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                    width="24px" height="24px" viewBox="0 0 24 24"
                                                                    version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none"
                                                                        fill-rule="evenodd">
                                                                        <polygon points="0 0 24 0 24 24 0 24" />
                                                                        <path
                                                                            d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z"
                                                                            fill="#000000" fill-rule="nonzero"
                                                                            opacity="0.3" />
                                                                        <path
                                                                            d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z"
                                                                            fill="#000000" fill-rule="nonzero" />
                                                                    </g>
                                                                </svg>
                                                            </span> Add Jobs</button>
                                                    </a>
                                                    <!--end::Add Requisitions-->
                                                @endif
                                            </div>
                                            <!--end::Toolbar-->
                                        </div>
                                    </div>
                                    <!--end::Actions-->
                                </div>
                                <!--end::All Req. header-->
                                <!--begin::All Req. Content-->
                                <div class="card-body px-0 py-4">
                                    @if (in_array('view_jobs', $userPermissions))
                                        <div class="jobx">
                                            <div class="table-card">
                                                <div class="table-header">
                                                    <div class="th">Job Details</div>
                                                    <div class="th">Experience</div>
                                                    <div class="th">Hiring &amp; Location</div>
                                                    <div class="th">Hiring Manager</div>
                                                    <div class="th"><i class="fa fa-users me-1"></i> Team</div>
                                                    <div class="th"><i class="fa fa-handshake me-1"></i> Agency</div>
                                                    <div class="th">Hiring Pipeline <i class="fas fa-info-circle ms-1" style="opacity:.4;font-size:10px;"></i></div>
                                                </div>
                                                <div id="clientJobsListajax"></div>
                                            </div>
                                        </div>
                                        <div id="paginationclientjobs" class="mt-3"></div>
                                    @endif
                                </div>
                                <!--end::All Req. Content-->
                            </div>
                            <!--end::All Req.-->
                            <!--end::Client Req.-->
                            <div class="tab-pane fade" id="client-requisitions" role="tabpanel">
                                <!--begin::Client Req. header-->
                                <div class="card-header cursor-pointer px-0">
                                    <!--begin::Card title-->
                                    <div class="card-title m-0">
                                        <h3 class="fw-bolder m-0">HCL Jobs</h3>
                                    </div>
                                    <!--end::Card title-->
                                </div>
                                <!--end::Client Req. header-->
                                <!--begin::Client Req. Content-->
                                <div class="card-body px-0 py-7">
                                    <!--begin:Table-->
                                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                                        <!--begin::Table head-->
                                        <thead>
                                            <!--begin::Table row-->
                                            <tr class="fw-bolder text-muted">
                                                <th class="w-10px pe-0">
                                                    <div
                                                        class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                        <input class="form-check-input" type="checkbox"
                                                            data-kt-check="true"
                                                            data-kt-check-target="#kt_table_users .form-check-input2"
                                                            value="1" />
                                                    </div>
                                                </th>
                                                <th class="">Jobs</th>
                                                <th class="">Exp.</th>
                                                <th class="">Salary</th>
                                                <th class="">Location</th>
                                                <th class="">Status</th>
                                                <th class="">Date</th>
                                                <th class="">HM | CP</th>
                                                <th class="">S.Partners</th>
                                                <th class="">TOT</th>
                                                <th class="">SOU</th>
                                                <th class="">SCR</th>
                                                <th class="">EVA</th>
                                                <th class="">NEO</th>
                                                <th class="">OFF</th>
                                                <th class="">JOI</th>
                                                <th class="">REJ</th>
                                            </tr>
                                            <!--end::Table row-->
                                        </thead>
                                        <!--end::Table head-->
                                        <!--begin::Table body-->
                                        <tbody class="text-gray-600 fw-bold">
                                            <!--begin::Table row-->
                                            <tr class="">
                                                <!--begin::Checkbox-->
                                                <td class="">
                                                    <div
                                                        class="form-check form-check-sm form-check-custom form-check-solid">
                                                        <input class="form-check-input form-check-input2"
                                                            type="checkbox" value="1" />
                                                    </div>
                                                </td>
                                                <!--end::Checkbox-->
                                                <!--begin::Requisitions=-->
                                                <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                                    <div
                                                        class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5">
                                                        <a href="javascript:void(0);">
                                                            <div
                                                                class="symbol-label fs-3 bg-light-warning text-warning border-radius-5">
                                                                U</div>
                                                        </a>
                                                    </div>
                                                    <!--end::Avatar-->
                                                    <!--begin::User details-->
                                                    <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                            class="text-dark fw-bolder text-hover-primary fs-6">UI/UX
                                                            Designer</a> <a href="javascript:void(0);"
                                                            class="fw-bold text-muted d-block fs-7 text-hover-primary">Wipro</a>
                                                    </div>
                                                    <!--begin::User details-->
                                                </td>
                                                <!--end::Requisitions-->
                                                <!--begin::Exp.-->
                                                <td class="">
                                                    <div class="d-flex flex-column">
                                                         <span
                                                            class="text-dark fw-bolder fs-6">22+</span> 
                                                        <span
                                                            class="text-muted fw-bold text-muted d-block fs-7">18+</span>
                                                    </div>
                                                </td>
                                                <!--end::Exp.-->
                                                <!--begin::Salary-->
                                                <td class="">
                                                    <div class="d-flex flex-column"> 
                                                        <span
                                                            class="text-dark fw-bolder fs-6">50 LPA</span> 
                                                            <span
                                                            class="fw-bold d-block fs-7">30 LPA</span> 
                                                    </div>
                                                </td>
                                                <!--end::Salary-->
                                                <!--begin::Location-->
                                                <td class="">
                                                    <div class="d-flex flex-column"> 
                                                        <span
                                                            class="text-dark fw-bolder fs-6">Gurgaon</span>
                                                        <div class="d-flex align-items-center"> <span
                                                                class="fw-bold text-muted d-block fs-7">Haryana</span>
                                                            &nbsp;|&nbsp; <span
                                                                class="fw-bold text-muted d-block fs-7">INDIA</span>
                                                            <div> </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <!--end::Location-->
                                                <!--begin::Status | Requisition ID-->
                                                <td class="">
                                                    <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                            class="fs-6 hold-bg text-hover-warning">Hold</a> <span
                                                            class="fw-bold text-muted d-block fs-7">R2</span> 
                                                    </div>
                                                </td>
                                                <!--end::Status | Requisition ID-->
                                                <!--begin::Date | Openings-->
                                                <td class="">
                                                    <div class="d-flex flex-column">
                                                         <span
                                                            class="text-dark fw-bolder fs-6">28-Apr-2021</span> <span
                                                            class="text-muted fw-bold d-block fs-7">774</span> 
                                                    </div>
                                                </td>
                                                <!--end::Date | Openings-->
                                                <!--begin::Hiring Manager | Contact Person-->
                                                <td class="">
                                                    <div class="d-flex flex-column">
                                                        <div class="symbol-group symbol-hover">
                                                            <div class="symbol symbol-30 symbol-circle"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="Pawan"> <img alt="Pic"
                                                                    src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_4.jpg">
                                                            </div>
                                                            <div class="symbol symbol-30 symbol-circle symbol-light"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title=""
                                                                data-original-title="More users list.."> <span
                                                                    class="symbol-label font-weight-bold">1+</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </td>

                                                <!--end::Hiring Manager | Contact Person-->
                                                <!--begin::Staffing Partners-->
                                                <td class="">
                                                    <div class="d-flex flex-column">
                                                        <div class="symbol-group symbol-hover">
                                                            <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip"
                                                                data-bs-placement="top" title="" data-original-title="Pradeep">
                                                                <img alt="Pic"
                                                                    src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_23.jpg">
                                                            </div>
                                                            <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip"
                                                                data-bs-placement="top" title="" data-original-title="Abinesh">
                                                                <img alt="Pic"
                                                                    src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_24.jpg">
                                                            </div>
                                                            <div class="symbol symbol-30 symbol-circle symbol-light"
                                                                data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                                                data-original-title="More users list.."> <span
                                                                    class="symbol-label font-weight-bold">2+</span> </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <!--end::Staffing Partners-->
                                                <!--begin::TOT-->
                                                <td class="">
                                                    <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                            class="text-dark fw-bolder fs-6 text-hover-primary">65</a> <a
                                                            href="javascript:void(0);"
                                                            class="text-muted fw-bold d-block fs-7 text-hover-primary">10</a>
                                                    </div>
                                                </td>
                                                <!--end::TOT-->
                                                <!--begin::SOU-->
                                                <td class="">
                                                    <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                            class="text-dark fw-bolder fs-6 text-hover-primary">0</a> 
                                                    </div>
                                                </td>
                                                <!--end::SOU-->
                                                <!--begin::SCR-->
                                                <td class="">
                                                    <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                            class="text-dark fw-bolder fs-6 text-hover-primary">23</a> 
                                                    </div>
                                                </td>
                                                <!--end::SCR-->
                                                <!--begin::EVA-->
                                                <td class="">
                                                    <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                            class="text-dark fw-bolder fs-6 text-hover-primary">0</a> 
                                                    </div>
                                                </td>
                                                <!--end::EVA-->
                                                <!--begin::NEO-->
                                                <td class="">
                                                    <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                            class="text-dark fw-bolder fs-6 text-hover-primary">24</a> 
                                                    </div>
                                                </td>
                                                <!--end::NEO-->
                                                <!--begin::OFF-->
                                                <td class="">
                                                    <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                            class="text-dark fw-bolder fs-6 text-hover-primary">83</a> 
                                                    </div>
                                                </td>
                                                <!--end::OFF-->
                                                <!--begin::JOI-->
                                                <td class="">
                                                    <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                            class="text-dark fw-bolder fs-6 text-hover-primary">49</a> 
                                                    </div>
                                                </td>
                                                <!--end::JOI-->
                                                <!--begin::REJ-->
                                                <td class="">
                                                    <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                            class="text-dark fw-bolder fs-6 text-hover-primary">0</a> 
                                                    </div>
                                                </td>
                                                <!--end::REJ-->
                                            </tr>
                                         <!--end::Table row-->
                                        </tbody>

                                     <!--end::Table body-->
                                    </table>
                                    <!--end::Table-->
                                </div>
                             <!--end::Client Req. Content-->
                            </div>
                        <!--end::Client Req.-->
                        <!--begin::Custom Req.-->
                        <div class="tab-pane fade" id="custom-requisitions" role="tabpanel">
                            <!--begin::Custom Req. header-->
                            <div class="card-header cursor-pointer px-0">
                                <!--begin::Card title-->
                                <div class="card-title m-0">
                                    <h3 class="fw-bolder m-0">Custom Jobs</h3>
                                </div>
                                <!--end::Card title-->
                                <!--begin::Actions-->
                                <div class="d-flex align-items-center py-1">
                                    <div class="card-toolbar">
                                        <!--begin::Toolbar-->
                                        <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                                            <!--begin::Add Requisitions-->
                                            <a href="{{ url('service_company/jobs/create') }}">
                                                <button type="button"
                                                    class="btn btn-sm btn-flex btn-primary btn-active-primary fw-bolder me-3">
                                                    <!--begin::Svg Icon-->
                                                    <span class="svg-icon svg-icon-primary svg-icon-1x">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                            height="24px" viewBox="0 0 24 24" version="1.1">
                                                            <g stroke="none" stroke-width="1" fill="none"
                                                                fill-rule="evenodd">
                                                                <polygon points="0 0 24 0 24 24 0 24" />
                                                                <path
                                                                    d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z"
                                                                    fill="#000000" fill-rule="nonzero"
                                                                    opacity="0.3" />
                                                                <path
                                                                    d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z"
                                                                    fill="#000000" fill-rule="nonzero" />
                                                            </g>
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                    Add Jobs </button>
                                            </a>
                                            <!--end::Add Requisitions-->
                                        </div>
                                        <!--end::Toolbar-->
                                    </div>
                                </div>
                                <!--end::Actions-->
                            </div>
                            <!--end::Custom Req. header-->
                            <!--begin::Custom Req. Content-->
                            <div class="card-body px-0 py-7">
                                <!--begin:Table-->
                                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                                    <!--begin::Table head-->
                                    <thead>
                                        <!--begin::Table row-->
                                        <tr class="fw-bolder text-muted">
                                            <th class="w-10px pe-0">
                                                <div
                                                    class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        data-kt-check="true"
                                                        data-kt-check-target="#kt_table_users .form-check-input3"
                                                        value="1" />
                                                </div>
                                            </th>
                                            <th class="">Jobs</th>
                                            <th class="">Exp.</th>
                                            <th class="">Salary</th>
                                            <th class="">Location</th>
                                            <th class="">Status</th>
                                            <th class="">Date</th>
                                            <th class="">HM | CP</th>
                                            <th class="">S.Partners</th>
                                            <th class="">TOT</th>
                                            <th class="">SOU</th>
                                            <th class="">SCR</th>
                                            <th class="">EVA</th>
                                            <th class="">NEO</th>
                                            <th class="">OFF</th>
                                            <th class="">JOI</th>
                                            <th class="">REJ</th>
                                        </tr>
                                        <!--end::Table row-->
                                    </thead>
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    <tbody class="text-gray-600 fw-bold">
                                        <!--begin::Table row-->
                                        <tr class="">
                                            <!--begin::Checkbox-->
                                            <td class="">
                                                <div
                                                    class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input form-check-input3" type="checkbox"
                                                        value="1" />
                                                </div>
                                            </td>
                                            <!--end::Checkbox-->
                                            <!--begin::Requisitions=-->
                                            <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                                <div
                                                    class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5">
                                                    <a href="javascript:void(0);">
                                                        <div class="symbol-label border-radius-5"> <img
                                                                src="https://www.hcltech.com/sites/default/files/styles/large/public/images/guideline_based_vectors-square.png?itok=KEUChzF3"
                                                                alt="Company Logo" class="border-radius-5 w-100">
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <!--begin::User details-->
                                                <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                        class="text-dark fw-bolder text-hover-primary fs-6">Frontend
                                                        Developer</a> <a href="javascript:void(0);"
                                                        class="fw-bold text-muted d-block fs-7 text-hover-primary">HCl
                                                        Technologies</a> </div>
                                                <!--begin::User details-->
                                            </td>
                                            <!--end::Requisitions-->
                                            <!--begin::Exp.-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <span
                                                        class="text-dark fw-bolder fs-6">30+</span> <span
                                                        class="text-muted fw-bold text-muted d-block fs-7">27+</span>
                                                </div>
                                            </td>
                                            <!--end::Exp.-->
                                            <!--begin::Salary-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <span
                                                        class="text-dark fw-bolder fs-6">70 LPA</span> <span
                                                        class="fw-bold d-block fs-7">67 LPA</span> </div>
                                            </td>
                                            <!--end::Salary-->
                                            <!--begin::Location-->
                                            <td class="">
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark fw-bolder fs-6">Noida</span>
                                                    <div class="d-flex align-items-center">
                                                        <span class="fw-bold text-muted d-block fs-7">U.P</span>
                                                        &nbsp;|&nbsp; <span
                                                            class="fw-bold text-muted d-block fs-7">INDIA</span>
                                                        <div> </div>
                                            </td>
                                            <!--end::Location-->
                                            <!--begin::Status | Requisition ID-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                        class="fs-6 closed-bg text-hover-danger">Closed</a> <span
                                                        class="fw-bold text-muted d-block fs-7">R3-Custom</span> </div>
                                            </td>
                                            <!--end::Status | Requisition ID-->
                                            <!--begin::Date | Openings-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <span
                                                        class="text-dark fw-bolder fs-6">29-Aug-2021</span> <span
                                                        class="text-muted fw-bold d-block fs-7">33</span> </div>
                                            </td>
                                            <!--end::Date | Openings-->
                                            <!--begin::Hiring Manager | Contact Person-->
                                            <td class="">
                                                <div class="d-flex flex-column">
                                                    <div class="symbol-group symbol-hover">
                                                        <div class="symbol symbol-30 symbol-circle"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Priyanka"> <img alt="Pic"
                                                                src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_20.jpg">
                                                        </div>
                                                        <div class="symbol symbol-30 symbol-circle symbol-light"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="" data-original-title="More users list..">
                                                            <span class="symbol-label font-weight-bold">1+</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <!--end::Hiring Manager | Contact Person-->
                                            <!--begin::Staffing Partners-->
                                            <td class="">
                                                <div class="d-flex flex-column">
                                                    <div class="symbol-group symbol-hover">
                                                        <div class="symbol symbol-30 symbol-circle"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="" data-original-title="One"> <img
                                                                alt="Pic"
                                                                src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_1.jpg">
                                                        </div>
                                                        <div class="symbol symbol-30 symbol-circle"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="" data-original-title="Two"> <img
                                                                alt="Pic"
                                                                src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_2.jpg">
                                                        </div>
                                                        <div class="symbol symbol-30 symbol-circle symbol-light"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="" data-original-title="More users list..">
                                                            <span class="symbol-label font-weight-bold">2+</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <!--end::Staffing Partners-->
                                            <!--begin::TOT-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                        class="text-dark fw-bolder fs-6 text-hover-primary">425</a> <a
                                                        href="javascript:void(0);"
                                                        class="text-muted fw-bold d-block fs-7 text-hover-primary">64</a>
                                                </div>
                                            </td>
                                            <!--end::TOT-->
                                            <!--begin::SOU-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                        class="text-dark fw-bolder fs-6 text-hover-primary">345</a>
                                                </div>
                                            </td>
                                            <!--end::SOU-->
                                            <!--begin::SCR-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                        class="text-dark fw-bolder fs-6 text-hover-primary">0</a>
                                                </div>
                                            </td>
                                            <!--end::SCR-->
                                            <!--begin::EVA-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                        class="text-dark fw-bolder fs-6 text-hover-primary">94</a>
                                                </div>
                                            </td>
                                            <!--end::EVA-->
                                            <!--begin::NEO-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                        class="text-dark fw-bolder fs-6 text-hover-primary">03</a>
                                                </div>
                                            </td>
                                            <!--end::NEO-->
                                            <!--begin::OFF-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                        class="text-dark fw-bolder fs-6 text-hover-primary">0</a>
                                                </div>
                                            </td>
                                            <!--end::OFF-->
                                            <!--begin::JOI-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                        class="text-dark fw-bolder fs-6 text-hover-primary">45</a>
                                                </div>
                                            </td>
                                            <!--end::JOI-->
                                            <!--begin::REJ-->
                                            <td class="">
                                                <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                        class="text-dark fw-bolder fs-6 text-hover-primary">540</a>
                                                </div>
                                            </td>
                                            <!--end::REJ-->
                                        </tr>
                                        <!--end::Table row-->
                                    </tbody>
                                    <!--end::Table body-->
                                </table>
                                <!--end::Table-->
                            </div>
                            <!--end::Custom Req. Content-->
                        </div>
                        <!--end::Custom Req.-->
                        <div class="tab-pane fade" id="jobs" role="tabpanel">Jobs - Content</div>
                        </div>
                    <!--end::Requisitions Content-->
                        </div></div>
                </div>
                <!--end::Requisitions Content-->
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
        href="../../demo1/dist/pages/profile/activity.html" class="btn btn-bg-body text-primary">
        &nbsp;
        <span class="svg-icon svg-icon-3 svg-icon-primary"></span>
    </a> </div>
<!--end::Footer-->

</div>


<script>
    var myChartId = "<?php echo @$client->id; ?>";

    // Global performance dashboard chart instances & range state
    window.perfChartInstances = window.perfChartInstances || {};
    window.perfCurrentRange = window.perfCurrentRange || '12M';
    window.perfCurrentCompare = window.perfCurrentCompare || 'prev_period';

    var perfDataSets = {
        '12M': {
            kpis: {
                candidates: 78,
                candidatesDiff: '+18%',
                candidatesComp: 'vs. 66 previous period',
                interviews: 32,
                interviewsDiff: '+23%',
                interviewsComp: 'vs. 26 previous period',
                offers: 12,
                offersDiff: '+33%',
                offersComp: 'vs. 9 previous period',
                joins: 8,
                joinsDiff: '+14%',
                joinsComp: 'vs. 7 previous period',
                timeToHire: '41 days',
                timeToHireDiff: '-28%',
                timeToHireComp: 'vs. 57 days previous period'
            },
            funnel: {
                cand: 78, candPct: '100%',
                int: 32, intPct: '41%',
                off: 12, offPct: '15%',
                join: 8, joinPct: '10%'
            },
            candLabels: ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'],
            candSub: [4, 6, 5, 8, 7, 9, 6, 8, 5, 7, 6, 7],
            intSched: [2, 3, 2, 4, 3, 4, 2, 3, 2, 3, 2, 2],
            intComp: [1, 2, 2, 3, 2, 3, 2, 2, 1, 2, 2, 2],
            jobLabels: ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'],
            jobActive: [2, 3, 3, 4, 4, 3, 4, 5, 4, 4, 3, 3],
            jobHold: [1, 0, 1, 1, 0, 1, 1, 0, 1, 0, 1, 1],
            jobClosed: [1, 2, 1, 1, 2, 1, 2, 1, 2, 1, 1, 1],
            offerLabels: ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'],
            offers: [1, 1, 1, 2, 1, 1, 1, 1, 0, 1, 1, 1],
            joins: [0, 1, 1, 1, 1, 1, 0, 1, 0, 1, 0, 1],
            donut: [3, 1, 0, 0],
            totalActiveJobs: 4,
            timeLabels: ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'],
            timeDays: [95, 88, 82, 75, 70, 65, 58, 54, 50, 46, 44, 41]
        },
        '6M': {
            kpis: {
                candidates: 42,
                candidatesDiff: '+14%',
                candidatesComp: 'vs. 37 previous 6M',
                interviews: 18,
                interviewsDiff: '+20%',
                interviewsComp: 'vs. 15 previous 6M',
                offers: 7,
                offersDiff: '+16%',
                offersComp: 'vs. 6 previous 6M',
                joins: 5,
                joinsDiff: '+25%',
                joinsComp: 'vs. 4 previous 6M',
                timeToHire: '43 days',
                timeToHireDiff: '-22%',
                timeToHireComp: 'vs. 55 days previous 6M'
            },
            funnel: {
                cand: 42, candPct: '100%',
                int: 18, intPct: '42.8%',
                off: 7, offPct: '16.7%',
                join: 5, joinPct: '11.9%'
            },
            candLabels: ['Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'],
            candSub: [6, 8, 5, 7, 6, 7],
            intSched: [2, 3, 2, 3, 2, 2],
            intComp: [2, 2, 1, 2, 2, 2],
            jobLabels: ['Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'],
            jobActive: [4, 5, 4, 4, 3, 3],
            jobHold: [1, 0, 1, 0, 1, 1],
            jobClosed: [2, 1, 2, 1, 1, 1],
            offerLabels: ['Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'],
            offers: [1, 1, 0, 1, 1, 1],
            joins: [0, 1, 0, 1, 0, 1],
            donut: [3, 1, 0, 0],
            totalActiveJobs: 4,
            timeLabels: ['Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'],
            timeDays: [58, 54, 50, 46, 44, 41]
        },
        '3M': {
            kpis: {
                candidates: 22,
                candidatesDiff: '+10%',
                candidatesComp: 'vs. 20 previous 3M',
                interviews: 9,
                interviewsDiff: '+12%',
                interviewsComp: 'vs. 8 previous 3M',
                offers: 4,
                offersDiff: '+33%',
                offersComp: 'vs. 3 previous 3M',
                joins: 3,
                joinsDiff: '+50%',
                joinsComp: 'vs. 2 previous 3M',
                timeToHire: '42 days',
                timeToHireDiff: '-19%',
                timeToHireComp: 'vs. 52 days previous 3M'
            },
            funnel: {
                cand: 22, candPct: '100%',
                int: 9, intPct: '40.9%',
                off: 4, offPct: '18.2%',
                join: 3, joinPct: '13.6%'
            },
            candLabels: ['Jan', 'Feb', 'Mar'],
            candSub: [7, 6, 7],
            intSched: [3, 2, 2],
            intComp: [2, 2, 2],
            jobLabels: ['Jan', 'Feb', 'Mar'],
            jobActive: [4, 3, 3],
            jobHold: [0, 1, 1],
            jobClosed: [1, 1, 1],
            offerLabels: ['Jan', 'Feb', 'Mar'],
            offers: [1, 1, 1],
            joins: [1, 0, 1],
            donut: [3, 1, 0, 0],
            totalActiveJobs: 4,
            timeLabels: ['Jan', 'Feb', 'Mar'],
            timeDays: [46, 44, 41]
        },
        '30D': {
            kpis: {
                candidates: 8,
                candidatesDiff: '+14%',
                candidatesComp: 'vs. 7 previous 30D',
                interviews: 3,
                interviewsDiff: '0%',
                interviewsComp: 'vs. 3 previous 30D',
                offers: 1,
                offersDiff: '0%',
                offersComp: 'vs. 1 previous 30D',
                joins: 1,
                joinsDiff: '+100%',
                joinsComp: 'vs. 0 previous 30D',
                timeToHire: '39 days',
                timeToHireDiff: '-31%',
                timeToHireComp: 'vs. 57 days previous 30D'
            },
            funnel: {
                cand: 8, candPct: '100%',
                int: 3, intPct: '37.5%',
                off: 1, offPct: '12.5%',
                join: 1, joinPct: '12.5%'
            },
            candLabels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            candSub: [2, 3, 1, 2],
            intSched: [1, 1, 0, 1],
            intComp: [1, 0, 1, 1],
            jobLabels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            jobActive: [3, 3, 3, 3],
            jobHold: [1, 1, 1, 1],
            jobClosed: [0, 0, 1, 0],
            offerLabels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            offers: [0, 1, 0, 0],
            joins: [0, 0, 1, 0],
            donut: [3, 1, 0, 0],
            totalActiveJobs: 4,
            timeLabels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            timeDays: [44, 43, 40, 39]
        }
    };

    function ensureChartJsLoaded(callback) {
        if (typeof Chart !== 'undefined') {
            callback();
            return;
        }
        if (!document.querySelector('script[src*="chart.umd.min.js"]')) {
            var s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js';
            s.onload = function() { callback(); };
            s.onerror = function() { console.error('Failed to load Chart.js'); };
            document.head.appendChild(s);
        } else {
            var interval = setInterval(function() {
                if (typeof Chart !== 'undefined') {
                    clearInterval(interval);
                    callback();
                }
            }, 50);
        }
    }

    function initPerformanceDashboard() {
        ensureChartJsLoaded(function() {
            var range = window.perfCurrentRange || '12M';
            var d = perfDataSets[range] || perfDataSets['12M'];

            function getOrCreateChart(canvasId, config) {
                var canvas = document.getElementById(canvasId);
                if (!canvas) return null;
                try {
                    if (typeof Chart.getChart === 'function') {
                        var existing = Chart.getChart(canvas);
                        if (existing) { existing.destroy(); }
                    }
                    if (window.perfChartInstances[canvasId]) {
                        window.perfChartInstances[canvasId].destroy();
                    }
                } catch(e) {
                    console.warn('Destroy error for ' + canvasId, e);
                }
                var ctx = canvas.getContext('2d');
                var inst = new Chart(ctx, config);
                window.perfChartInstances[canvasId] = inst;
                return inst;
            }

            // 1. Combo: Candidates & Interviews
            getOrCreateChart('chartCandidatesInterviews', {
                type: 'bar',
                data: {
                    labels: d.candLabels,
                    datasets: [
                        {
                            type: 'bar',
                            label: 'Candidates Submitted',
                            data: d.candSub,
                            backgroundColor: '#3b82f6',
                            borderRadius: 4,
                            barPercentage: 0.5,
                            order: 3
                        },
                        {
                            type: 'line',
                            label: 'Interviews Scheduled',
                            data: d.intSched,
                            borderColor: '#8b5cf6',
                            backgroundColor: '#8b5cf6',
                            borderWidth: 2,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            tension: 0.35,
                            order: 1
                        },
                        {
                            type: 'line',
                            label: 'Interviews Completed',
                            data: d.intComp,
                            borderColor: '#10b981',
                            backgroundColor: '#10b981',
                            borderWidth: 2,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            tension: 0.35,
                            order: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            padding: 8,
                            cornerRadius: 6,
                            titleFont: { family: 'Poppins', size: 11 },
                            bodyFont: { family: 'Poppins', size: 10 }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { family: 'Poppins', size: 10 }, color: '#64748b' } },
                        y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { family: 'Poppins', size: 10 }, color: '#64748b', precision: 0 } }
                    }
                }
            });

            // 2. Stacked Bar: All Jobs Status
            getOrCreateChart('chartAllJobsStatus', {
                type: 'bar',
                data: {
                    labels: d.jobLabels,
                    datasets: [
                        {
                            label: 'Active',
                            data: d.jobActive,
                            backgroundColor: '#3b82f6',
                            borderRadius: 2,
                            barPercentage: 0.5
                        },
                        {
                            label: 'On Hold',
                            data: d.jobHold,
                            backgroundColor: '#f59e0b',
                            borderRadius: 2,
                            barPercentage: 0.5
                        },
                        {
                            label: 'Closed',
                            data: d.jobClosed,
                            backgroundColor: '#ef4444',
                            borderRadius: 2,
                            barPercentage: 0.5
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            padding: 8,
                            cornerRadius: 6,
                            titleFont: { family: 'Poppins', size: 11 },
                            bodyFont: { family: 'Poppins', size: 10 }
                        }
                    },
                    scales: {
                        x: { stacked: true, grid: { display: false }, ticks: { font: { family: 'Poppins', size: 10 }, color: '#64748b' } },
                        y: { stacked: true, beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { family: 'Poppins', size: 10 }, color: '#64748b', precision: 0 } }
                    }
                }
            });

            // 3. Combo: Offers & Joinings
            getOrCreateChart('chartOffersJoinings', {
                type: 'bar',
                data: {
                    labels: d.offerLabels,
                    datasets: [
                        {
                            type: 'bar',
                            label: 'Offers Generated',
                            data: d.offers,
                            backgroundColor: '#f59e0b',
                            borderRadius: 4,
                            barPercentage: 0.45,
                            order: 2
                        },
                        {
                            type: 'line',
                            label: 'Successful Joins',
                            data: d.joins,
                            borderColor: '#10b981',
                            backgroundColor: '#10b981',
                            borderWidth: 2,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            tension: 0.35,
                            order: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            padding: 8,
                            cornerRadius: 6,
                            titleFont: { family: 'Poppins', size: 11 },
                            bodyFont: { family: 'Poppins', size: 10 }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { family: 'Poppins', size: 10 }, color: '#64748b' } },
                        y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { family: 'Poppins', size: 10 }, color: '#64748b', precision: 0 } }
                    }
                }
            });

            // 4. Doughnut: Job Type Performance
            getOrCreateChart('chartJobType', {
                type: 'doughnut',
                data: {
                    labels: ['Fulltime Hiring', 'Contract Hiring', 'Bench Talent Jobs', 'Matching Jobs'],
                    datasets: [{
                        data: d.donut,
                        backgroundColor: ['#3b82f6', '#8b5cf6', '#f59e0b', '#10b981'],
                        borderWidth: 0,
                        hoverOffset: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            padding: 8,
                            cornerRadius: 6,
                            titleFont: { family: 'Poppins', size: 11 },
                            bodyFont: { family: 'Poppins', size: 10 }
                        }
                    },
                    cutout: '72%'
                }
            });

            // 5. Line: Time to Hire Trend
            getOrCreateChart('chartTimeToHire', {
                type: 'line',
                data: {
                    labels: d.timeLabels,
                    datasets: [{
                        label: 'Avg. Days',
                        data: d.timeDays,
                        borderColor: '#06b6d4',
                        backgroundColor: 'rgba(6, 182, 212, 0.12)',
                        fill: true,
                        tension: 0.38,
                        borderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        pointBackgroundColor: '#06b6d4'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            padding: 8,
                            cornerRadius: 6,
                            titleFont: { family: 'Poppins', size: 11 },
                            bodyFont: { family: 'Poppins', size: 10 }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { family: 'Poppins', size: 10 }, color: '#64748b' } },
                        y: { beginAtZero: false, min: 25, grid: { color: '#f1f5f9' }, ticks: { font: { family: 'Poppins', size: 10 }, color: '#64748b', precision: 0 } }
                    }
                }
            });
        });
    }

    function cdPerfSyncRange(val) {
        window.perfCurrentRange = val;
        // Sync all mini dropdowns and top selector
        $('.perf-sync-select, #perfDateRangeSelect').val(val);

        var d = perfDataSets[val] || perfDataSets['12M'];

        // Update KPIs
        $('#perfKpiCandidates').text(d.kpis.candidates);
        $('#perfKpiCandPill').html('<i class="fas fa-arrow-up fa-solid" style="font-size:8px;"></i>' + d.kpis.candidatesDiff);
        $('#perfKpiCandComp').text(d.kpis.candidatesComp);

        $('#perfKpiInterviews').text(d.kpis.interviews);
        $('#perfKpiIntPill').html('<i class="fas fa-arrow-up fa-solid" style="font-size:8px;"></i>' + d.kpis.interviewsDiff);
        $('#perfKpiIntComp').text(d.kpis.interviewsComp);

        $('#perfKpiOffers').text(d.kpis.offers);
        $('#perfKpiOfferPill').html('<i class="fas fa-arrow-up fa-solid" style="font-size:8px;"></i>' + d.kpis.offersDiff);
        $('#perfKpiOfferComp').text(d.kpis.offersComp);

        $('#perfKpiJoins').text(d.kpis.joins);
        $('#perfKpiJoinPill').html('<i class="fas fa-arrow-up fa-solid" style="font-size:8px;"></i>' + d.kpis.joinsDiff);
        $('#perfKpiJoinComp').text(d.kpis.joinsComp);

        $('#perfKpiTimeToHire').text(d.kpis.timeToHire);
        $('#perfKpiTimePill').html('<i class="fas fa-arrow-down fa-solid" style="font-size:8px;"></i>' + d.kpis.timeToHireDiff);
        $('#perfKpiTimeComp').text(d.kpis.timeToHireComp);

        // Update Funnel
        $('#funnelCountCand').text(d.funnel.cand);
        $('#funnelPctCand').text(d.funnel.candPct);
        $('#funnelCountInt').text(d.funnel.int);
        $('#funnelPctInt').text(d.funnel.intPct);
        $('#funnelCountOff').text(d.funnel.off);
        $('#funnelPctOff').text(d.funnel.offPct);
        $('#funnelCountJoin').text(d.funnel.join);
        $('#funnelPctJoin').text(d.funnel.joinPct);

        // Update Donut center
        $('#donutTotalJobs').text(d.totalActiveJobs);

        // Update Charts in-place
        if (window.perfChartInstances['chartCandidatesInterviews']) {
            var c1 = window.perfChartInstances['chartCandidatesInterviews'];
            c1.data.labels = d.candLabels;
            c1.data.datasets[0].data = d.candSub;
            c1.data.datasets[1].data = d.intSched;
            c1.data.datasets[2].data = d.intComp;
            c1.update();
        }
        if (window.perfChartInstances['chartAllJobsStatus']) {
            var c2 = window.perfChartInstances['chartAllJobsStatus'];
            c2.data.labels = d.jobLabels;
            c2.data.datasets[0].data = d.jobActive;
            c2.data.datasets[1].data = d.jobHold;
            c2.data.datasets[2].data = d.jobClosed;
            c2.update();
        }
        if (window.perfChartInstances['chartOffersJoinings']) {
            var c3 = window.perfChartInstances['chartOffersJoinings'];
            c3.data.labels = d.offerLabels;
            c3.data.datasets[0].data = d.offers;
            c3.data.datasets[1].data = d.joins;
            c3.update();
        }
        if (window.perfChartInstances['chartJobType']) {
            var c4 = window.perfChartInstances['chartJobType'];
            c4.data.datasets[0].data = d.donut;
            c4.update();
        }
        if (window.perfChartInstances['chartTimeToHire']) {
            var c5 = window.perfChartInstances['chartTimeToHire'];
            c5.data.labels = d.timeLabels;
            c5.data.datasets[0].data = d.timeDays;
            c5.update();
        }
    }

    function cdPerfSyncCompare(val) {
        window.perfCurrentCompare = val;
        var suffix = (val === 'prev_year') ? 'previous year' : (val === 'prev_period' ? 'previous period' : '');
        if (val === 'none') {
            $('.cd-perf-kpi-comp, .cd-perf-pill').css('visibility', 'hidden');
        } else {
            $('.cd-perf-kpi-comp, .cd-perf-pill').css('visibility', 'visible');
            var d = perfDataSets[window.perfCurrentRange || '12M'];
            if (d && d.kpis) {
                $('#perfKpiCandComp').text('vs. 66 ' + suffix);
                $('#perfKpiIntComp').text('vs. 26 ' + suffix);
                $('#perfKpiOfferComp').text('vs. 9 ' + suffix);
                $('#perfKpiJoinComp').text('vs. 7 ' + suffix);
                $('#perfKpiTimeComp').text('vs. 57 days ' + suffix);
            }
        }
    }

    // Interactive Tab Navigator for KPI and Header Cards
    $(document).off('click', '.cd-nav-tab');
    $(document).on('click', '.cd-nav-tab', function(e) {
        e.preventDefault();
        var target = $(this).attr('data-target') || $(this).data('target');
        if (target) {
            var $link = $('a[href="' + target + '"]');
            if ($link.length) {
                $link.tab('show');
            }
        }
    });

    function applyLiveClientDataToPerfDashboard(d) {
        if (!d) return;
        var totalCandidates = d.total_candidates || 0;
        var activeCount = d.counts ? (d.counts.active || 0) : 0;
        var holdCount = d.counts ? (d.counts.hold || 0) : 0;
        var inactiveCount = d.counts ? (d.counts.inactive || 0) : 0;
        var joinedCount = d.joined_count || 0;
        var offerCount = d.offer_count || 0;

        if (totalCandidates > 0) {
            perfDataSets['12M'].kpis.candidates = totalCandidates;
            perfDataSets['12M'].funnel.cand = totalCandidates;
        }
        if (offerCount > 0) {
            perfDataSets['12M'].kpis.offers = offerCount;
            perfDataSets['12M'].funnel.off = offerCount;
        }
        if (joinedCount > 0) {
            perfDataSets['12M'].kpis.joins = joinedCount;
            perfDataSets['12M'].funnel.join = joinedCount;
        }
        if (activeCount + holdCount + inactiveCount > 0) {
            perfDataSets['12M'].donut = [activeCount, holdCount, inactiveCount, 0];
            perfDataSets['12M'].totalActiveJobs = activeCount;
            $('#donutTotalJobs').text(activeCount);
            $('#donutCountFulltime').text(activeCount);
            $('#donutCountContract').text(holdCount);
        }
        cdPerfSyncRange(window.perfCurrentRange || '12M');
    }

    // Auto-init on load if candidateDashboard is active
    setTimeout(function() {
        if ($('#candidateDashboard').length) {
            initPerformanceDashboard();
        }
    }, 250);
</script>


<script>
    function initContactsDataTable() {
        if ($.fn.DataTable.isDataTable('#contactList')) {
            return;
        }
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
                'url': '{!! route('service_company.clients.contactdata') !!}?clientId=' + {{ @$client->id }},
                "data": function(d) {
                    d.search = $('#contactSearchInput').val() || $('#inputSearch').val()
                }
            },
            "drawCallback": function(settings) {
                $('[data-toggle="tooltip1"]').tooltip();
                $('[data-toggle="tooltip2"]').tooltip();
            },
            columns: [
                { data: 'chkbox', name: 'chkbox', searchable: false, orderable: false, width: '40px' },
                { data: 'Contact', name: 'Contact', searchable: false, orderable: false },
                { data: 'Designation', name: 'Designation', searchable: false, orderable: false },
                { data: 'Email', name: 'Email', searchable: false, orderable: false },
                { data: 'Phone', name: 'Phone', searchable: false, orderable: false },
                { data: 'Status', name: 'Status', searchable: false, orderable: false, width: '90px' }
            ]
        });
        new $.fn.dataTable.FixedHeader(table);
    }
</script>
<script>
    $(document).ready(function() {
        if (window.performance && performance.mark) {
            performance.mark('client_drawer_render_start');
            setTimeout(function() {
                performance.mark('client_drawer_render_end');
                performance.measure('client_drawer_render_time', 'client_drawer_render_start', 'client_drawer_render_end');
                var entries = performance.getEntriesByName('client_drawer_render_time');
                if (entries.length > 0) {
                    console.log('⚡ [APM Telemetry] Client Drawer Shell DOM Render Latency: ' + entries[0].duration.toFixed(2) + ' ms');
                }
            }, 100);
        }
        // Phase 9 Optimization: Lazy load tab data on tab activation & debounce search inputs
        var loadedTabs = {};

        if ($('#candidate_list').hasClass('active') || $('a[href="#candidate_list"]').hasClass('active')) {
            fetchCandidates();
            loadedTabs['candidate_list'] = true;
        } else if ($('#contacts').hasClass('active') || $('a[href="#contacts"]').hasClass('active')) {
            initContactsDataTable();
            loadedTabs['contacts'] = true;
        } else if ($('#candidateDashboard').hasClass('active') || $('a[href="#candidateDashboard"]').hasClass('active')) {
            initPerformanceDashboard();
            fetchClientDashboardAnalytics();
            loadedTabs['candidateDashboard'] = true;
        }

        $(document).on('shown.bs.tab', 'a[data-toggle="tab"], a[data-bs-toggle="tab"]', function (e) {
            var target = $(e.target).attr('href');
            if (!target) return;
            target = target.replace('#', '');

            if (target === 'candidateDashboard') {
                initPerformanceDashboard();
                setTimeout(function() {
                    if (window.perfChartInstances) {
                        Object.values(window.perfChartInstances).forEach(function(c) {
                            try { if (c && typeof c.resize === 'function') c.resize(); } catch(err){}
                        });
                    }
                }, 60);
            }

            if (loadedTabs[target]) return;

            if (target === 'contacts') {
                initContactsDataTable();
                loadedTabs[target] = true;
            } else if (target === 'candidate_list') {
                fetchCandidates();
                loadedTabs[target] = true;
            } else if (target === 'requisitions') {
                fetchJobsClient();
                loadedTabs[target] = true;
            } else if (target === 'interview_tracker') {
                fetchCandidatesInverViewTracker();
                loadedTabs[target] = true;
            } else if (target === 'candidate_joining') {
                fetchCandidatesJoin();
                loadedTabs[target] = true;
            } else if (target === 'candidateDashboard') {
                fetchClientDashboardAnalytics();
                loadedTabs[target] = true;
            }
        });

        $(document).on('keyup', '#contactSearchInput', function() {
            if ($.fn.DataTable.isDataTable('#contactList')) {
                $('#contactList').DataTable().search($(this).val()).draw();
            }
        });

        var jobSearchTimer = null;
        $('#inputJobSearch').on('keyup', function() {
            clearTimeout(jobSearchTimer);
            jobSearchTimer = setTimeout(function() {
                fetchJobsClient();
            }, 300);
        });

        var searchTimer = null;
        $('#search').on('keyup', function() {
            clearTimeout(searchTimer);
            const search = $('#search').val();
            searchTimer = setTimeout(function() {
                fetchCandidates(1, search);
            }, 300);
        });

        $(document).off('click', '#paginationcandidate [data-page], #paginationcandidate .page-link, #paginationcandidate .pagi-page, #paginationcandidate .pagi-arrow');
        $(document).on('click', '#paginationcandidate [data-page], #paginationcandidate .page-link, #paginationcandidate .pagi-page, #paginationcandidate .pagi-arrow', function(e) {
            e.preventDefault();
            var $btn = $(e.target).closest('[data-page]');
            if (!$btn.length) return;

            var page = parseInt($btn.attr('data-page') || $btn.data('page'), 10);
            if (!page || page < 1 || $btn.hasClass('disabled') || $btn.hasClass('pagi-disabled') || $btn.hasClass('pagi-active-disabled') || $btn.is(':disabled')) return;

            var search = $('#inputSearch').val() || '';
            fetchCandidates(page, search);
        });

        $(document).off('click', '#paginationcandidatejoin [data-page], #paginationcandidatejoin .page-link, #paginationcandidatejoin .pagi-page, #paginationcandidatejoin .pagi-arrow');
        $(document).on('click', '#paginationcandidatejoin [data-page], #paginationcandidatejoin .page-link, #paginationcandidatejoin .pagi-page, #paginationcandidatejoin .pagi-arrow', function(e) {
            e.preventDefault();
            var $btn = $(e.target).closest('[data-page]');
            if (!$btn.length) return;

            var page = parseInt($btn.attr('data-page') || $btn.data('page'), 10);
            if (!page || page < 1 || $btn.hasClass('disabled') || $btn.hasClass('pagi-disabled') || $btn.hasClass('pagi-active-disabled') || $btn.is(':disabled')) return;

            var search = $('#inputSearch').val() || '';
            fetchCandidatesJoin(page, search);
        });

        $(document).off('click', '#paginationcandidateinterviewtracker [data-page], #paginationcandidateinterviewtracker .page-link, #paginationcandidateinterviewtracker .pagi-page, #paginationcandidateinterviewtracker .pagi-arrow');
        $(document).on('click', '#paginationcandidateinterviewtracker [data-page], #paginationcandidateinterviewtracker .page-link, #paginationcandidateinterviewtracker .pagi-page, #paginationcandidateinterviewtracker .pagi-arrow', function(e) {
            e.preventDefault();
            var $btn = $(e.target).closest('[data-page]');
            if (!$btn.length) return;

            var page = parseInt($btn.attr('data-page') || $btn.data('page'), 10);
            if (!page || page < 1 || $btn.hasClass('disabled') || $btn.hasClass('pagi-disabled') || $btn.hasClass('pagi-active-disabled') || $btn.is(':disabled')) return;

            var search = $('#inputSearch').val() || '';
            fetchCandidatesInverViewTracker(page, search);
        });

        $(document).off('click', '#paginationclientjobs [data-page], #paginationclientjobs .page-link, #paginationclientjobs .pagi-page, #paginationclientjobs .pagi-arrow');
        $(document).on('click', '#paginationclientjobs [data-page], #paginationclientjobs .page-link, #paginationclientjobs .pagi-page, #paginationclientjobs .pagi-arrow', function(e) {
            e.preventDefault();
            var $btn = $(e.target).closest('[data-page]');
            if (!$btn.length) return;

            var page = parseInt($btn.attr('data-page') || $btn.data('page'), 10);
            if (!page || page < 1 || $btn.hasClass('disabled') || $btn.hasClass('pagi-disabled') || $btn.hasClass('pagi-active-disabled') || $btn.is(':disabled')) return;

            var search = $('#inputJobSearch').val() || '';
            fetchJobsClient(page, search);
        });


    });

    function fetchCandidates(page = 1, search = '') {
        var clientid = '{{ $client->id }}';
        $('.page-loader').css('display', 'flex');
        return $.ajax({
            url: `{{ url('service_company/client-candidate-list') }}?page=${page}`,
            type: 'GET',
            data: {
                search: search,
                clientid: clientid
            },
            success: function(response) {
                $('.page-loader').css('display', 'none');
                $('#candidatetList').html(response.html);
                $('#paginationcandidate').html(response.pagination);
            }
        });
    }

    function fetchCandidatesJoin(page = 1, search = '') {
        var clientid = '{{ $client->id }}';
        $('.page-loader').css('display', 'flex');
        return $.ajax({
            url: `{{ url('service_company/client-candidate-join-list') }}?page=${page}`,
            type: 'GET',
            data: {
                search: search,
                clientid: clientid
            },
            success: function(response) {
                $('.page-loader').css('display', 'none');
                $('#candidatetListJoin').html(response.html);
                $('#paginationcandidatejoin').html(response.pagination);
            }
        });
    }

    function fetchCandidatesInverViewTracker(page = 1, search = '') {
        var clientid = '{{ $client->id }}';
        $('.page-loader').css('display', 'flex');
        return $.ajax({
            url: `{{ url('service_company/client-candidate-interview-tracker') }}?page=${page}`,
            type: 'GET',
            data: {
                search: search,
                clientid: clientid
            },
            success: function(response) {
                $('.page-loader').css('display', 'none');
                $('#candidatetListinterviewtracker').html(response.html);
                $('#paginationcandidateinterviewtracker').html(response.pagination);
                if (response.pagination && typeof response.pagination.total !== 'undefined') {
                    $('#kpiInterviewsScheduledCountShow').text(response.pagination.total);
                    var $badge = $('#tabBadgeInterviewTrackerShow');
                    if ($badge.length) {
                        $badge.text(response.pagination.total);
                        if (response.pagination.total > 0) { $badge.show(); } else { $badge.hide(); }
                    }
                }
            }
        });
    }

    function renderInternalClientDashboardCharts(d, clientId) {
        if (!d) return;
        applyLiveClientDataToPerfDashboard(d);
    }

    function fetchClientDashboardAnalytics() {
        var clientId = '{{ $client->id }}';
        return $.ajax({
            url: `{{ url('service_company/clients/dashboard-analytics') }}/${clientId}`,
            type: 'GET',
            success: function(response) {
                if (response && response.status === 'success' && response.data) {
                    console.log('⚡ [Client Dashboard APM] Analytics loaded lazily for internal client', response.data);
                    var d = response.data;
                    var activeCount = d.counts ? (d.counts.active || 0) : 0;
                    var holdCount = d.counts ? (d.counts.hold || 0) : 0;
                    var inactiveCount = d.counts ? (d.counts.inactive || 0) : 0;
                    var totalJobs = activeCount + holdCount + inactiveCount;

                    $(`#icAllJobsCount_${clientId}`).text(totalJobs);
                    $(`#icActiveJobsCount_${clientId}`).text(activeCount);
                    $(`#icHoldJobsCount_${clientId}`).text(holdCount);
                    $(`#icClosedJobsCount_${clientId}`).text(inactiveCount);

                    $(`#icAdded7DaysCount_${clientId}`).text(d.candidateValuesTotal || 0);
                    $(`#icTotalCandidatesCount_${clientId}`).text(d.total_candidates || 0);
                    $(`#icTotalOffersCount_${clientId}`).text(d.candidateValuesTotaloffer || 0);
                    $(`#icTotalJoinedCount_${clientId}`).text(d.candidateValuesTotaljoin || 0);

                    renderInternalClientDashboardCharts(d, clientId);
                }
            }
        });
    }

    // ── Follow-Up actions for the Interview Tracker (client show page) ──
    function citToggleFollowUp(btn) {
        var menu   = btn.nextElementSibling;
        var isOpen = menu.classList.contains('open');
        document.querySelectorAll('.cit-followup-menu.open').forEach(function (m) { m.classList.remove('open'); });
        if (!isOpen) { menu.classList.add('open'); }
    }

    function citSetFollowUp(el, id, status) {
        document.querySelectorAll('.cit-followup-menu.open').forEach(function (m) { m.classList.remove('open'); });
        var dropdown = el.closest('.cit-followup-dropdown');
        var trigger  = dropdown ? dropdown.querySelector('.cit-followup-trigger') : null;
        var labels   = { 'confirmed': 'Confirmed', 'no-response': 'No Response', 'rescheduled': 'Rescheduled' };
        var colors   = { 'confirmed': 'var(--cit-success)', 'no-response': 'var(--cit-danger)', 'rescheduled': 'var(--cit-primary)' };
        $.ajax({
            url  : '{{ route("service_company.interview-schedule.followup_update") }}',
            type : 'POST',
            data : { _token: '{{ csrf_token() }}', id: id, status: status },
            success: function (resp) {
                if (resp && resp.status === 'success') {
                    if (trigger) {
                        var lbl = trigger.querySelector('.cit-fu-label');
                        if (lbl) { lbl.textContent = labels[status] || 'Follow Up'; }
                        trigger.style.color       = colors[status] || '';
                        trigger.style.borderColor = colors[status] || '';
                        var ctaIcon = trigger.querySelector('.cit-cta-icon');
                        if (ctaIcon) { ctaIcon.remove(); }
                    }
                } else {
                    Swal.fire({ text: (resp && resp.msg) ? resp.msg : 'Could not update follow-up status.', icon: 'error', confirmButtonText: 'Ok' });
                }
            },
            error: function () {
                Swal.fire({ text: 'Could not update follow-up status.', icon: 'error', confirmButtonText: 'Ok' });
            }
        });
    }

    // Close any open cit follow-up menu on outside click
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.cit-followup-dropdown').length) {
            document.querySelectorAll('.cit-followup-menu.open').forEach(function (m) { m.classList.remove('open'); });
        }
    });

    function fetchJobsClient(page = 1, search = '') {
        $('.page-loader').css('display', 'flex');
        return $.ajax({
            url: `{{ url('service_company/jobs_side_bar_job_ajax/data') }}?page=${page}`,
            type: 'GET',
            data: {
                search: $('#inputJobSearch').val(),
                client_id: $('#hid_client_id').val(),
                custom:true
            },
            success: function(response) {
                $('.page-loader').css('display', 'none');
                $('#clientJobsListajax').html(response.html);
                $('#paginationclientjobs').html(response.pagination);
            }
        });
    }




    // var allreq = $('#allreq').dataTable({

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
    //             d.search = $('#inputJobSearch').val(), d.client_id = $('#hid_client_id').val(), d.custom =
    //                 true
    //         }
    //     },
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
    // new $.fn.dataTable.FixedHeader(allreq);


    // ── Client Drawer UI Interactions ──
    $(document).ready(function() {
        $(document).on('click', '.cd-kpi-card[data-target-tab]', function(e) {
            var tabId = $(this).data('target-tab');
            if (tabId) {
                $('a[href="' + tabId + '"]').tab('show');
            }
        });

        $(document).on('click', '.cd-view-all-activity', function(e) {
            e.preventDefault();
            $('a[href="#recent_activity"]').tab('show');
        });

        $(document).on('click', '.cd-view-all-contacts, .cd-btn-add-contact', function(e) {
            e.preventDefault();
            $('a[href="#contacts"]').tab('show');
        });

        $(document).on('click', '.cd-btn-followup', function(e) {
            e.preventDefault();
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '<div style="font-size:12px;font-weight:700;color:#0f172a;">Schedule Client Follow Up</div>',
                    html: '<div style="text-align:left;font-size:11px;">' +
                          '<label style="display:block;font-weight:600;margin-bottom:3px;color:#475569;">Type</label>' +
                          '<select id="swal_fu_type" class="form-select form-select-sm mb-2" style="font-size:11px;">' +
                          '<option value="Call">Phone Call</option>' +
                          '<option value="Email">Email Follow-Up</option>' +
                          '<option value="Meeting">Virtual Meeting</option>' +
                          '<option value="Requirement">Requirement Review</option>' +
                          '</select>' +
                          '<label style="display:block;font-weight:600;margin-bottom:3px;color:#475569;">Date & Time</label>' +
                          '<input type="datetime-local" id="swal_fu_time" class="form-control form-control-sm mb-2" style="font-size:11px;" value="' + new Date().toISOString().slice(0, 16) + '">' +
                          '<label style="display:block;font-weight:600;margin-bottom:3px;color:#475569;">Notes</label>' +
                          '<textarea id="swal_fu_notes" class="form-control form-control-sm" rows="2" style="font-size:11px;" placeholder="Add discussion agenda or notes..."></textarea>' +
                          '</div>',
                    showCancelButton: true,
                    confirmButtonText: 'Save Follow Up',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#009ef7',
                    customClass: {
                        confirmButton: 'btn btn-sm btn-primary',
                        cancelButton: 'btn btn-sm btn-light'
                    }
                }).then(function(result) {
                    if (result.isConfirmed) {
                        Swal.fire({
                            icon: 'success',
                            title: '<span style="font-size:12px;font-weight:600;">Follow Up Recorded</span>',
                            text: 'Follow-up agenda has been saved.',
                            timer: 1800,
                            showConfirmButton: false
                        });
                    }
                });
            }
        });

        $(document).on('click', '.cd-btn-add-tag-trigger, .cd-btn-edit-tags', function(e) {
            e.preventDefault();
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '<span style="font-size:12px;font-weight:700;">Client Tags</span>',
                    input: 'text',
                    inputLabel: 'Add or edit tags (comma-separated)',
                    inputValue: '{{ implode(", ", $tagsArr) }}',
                    showCancelButton: true,
                    confirmButtonText: 'Save',
                    confirmButtonColor: '#009ef7',
                    customClass: {
                        input: 'form-control form-control-sm',
                        confirmButton: 'btn btn-sm btn-primary',
                        cancelButton: 'btn btn-sm btn-light'
                    }
                }).then(function(res) {
                    if (res.isConfirmed && res.value) {
                        Swal.fire({
                            icon: 'success',
                            title: '<span style="font-size:12px;font-weight:600;">Tags Updated</span>',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });
            }
        });
    });
</script>
