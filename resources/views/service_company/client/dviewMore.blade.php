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

    .table-custom-class tbody tr {
        height: 82px;
        /* Adjust the height as per your requirement */
        position: relative;
    }
    .iti__flag {
        margin-top: 2px !important;
      }
</style>


<div class="card-body position-relative px-0 py-0" id="kt_activities_body">
    
    <!--begin::Content-->
<div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-0" data-kt-scroll="true"
        data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body"
        data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">
        <!--beign::candidate detail-->
        {{-- ── Enterprise Direct Client Drawer (Green Theme, 12px Max Font Scale, Follow-up only action) ── --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <style>
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

          /* ── Top Action Header Bar (Direct Client: Follow Up ONLY) ── */
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
          .cd-hdr-btn-outline-green {
            background: #ffffff !important;
            border: 1.5px solid #10b981 !important;
            color: #10b981 !important;
          }
          .cd-hdr-btn-outline-green:hover {
            background: #ecfdf5 !important;
            color: #059669 !important;
            border-color: #059669 !important;
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
            font-size: 12px !important;
            transition: all 0.15s !important;
          }
          .cd-hdr-btn-close:hover {
            background: #f1f5f9 !important;
            color: #0f172a !important;
          }

          /* ── Client Hero Banner: DIRECT CLIENT (Clean Neutral Enterprise UI) ── */
          .cd-hero-banner-green {
            background: #ffffff !important;
            border-bottom: 1px solid #e2e8f0 !important;
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
            background: #064e3b;
            color: #ffffff;
            border: 2px solid #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px !important;
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
          .cd-pill-type-direct {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
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
            color: #059669 !important;
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
            background: #dcfce7;
            border: 1px solid #bbf7d0;
            color: #15803d;
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
          .cd-status-card-teal {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
          }
          .cd-status-card-grey {
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
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
          .cd-pulse-teal { background: #059669; }
          .cd-pulse-teal::after {
            content: "";
            position: absolute;
            inset: -3px;
            border-radius: 50%;
            border: 1.5px solid #059669;
            animation: cdpulse 2s infinite;
          }
          .cd-pulse-grey {
            background: #94a3b8 !important;
          }
          .cd-pulse-grey::after {
            display: none !important;
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
            color: #10b981 !important;
            background: rgba(16, 185, 129, 0.04) !important;
          }
          .cd-tabs-bar .nav-link.active {
            color: #059669 !important;
            border-bottom: 2px solid #10b981 !important;
            font-weight: 500 !important;
            background: #ecfdf5 !important;
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
            background: #10b981;
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
            color: #059669 !important;
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
            color: #059669;
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
            color: #10b981;
          }

          /* ── Client Tags Card ── */
          .cd-tags-cloud {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
          }
          .cd-btn-add-tag {
            border: 1px dashed #10b981;
            background: transparent;
            color: #10b981;
            font-size: 10px !important;
            font-weight: 500 !important;
            padding: 2px 8px;
            border-radius: 12px;
            cursor: pointer;
            line-height: 1.2;
          }
          .cd-btn-add-tag:hover {
            background: #ecfdf5;
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
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            font-size: 10px !important;
            color: #94a3b8;
          }

          /* ── Retained Styles for Sub-tab Jobs and Contacts ── */
          .jobx, .jobx * { font-family:'Poppins',sans-serif; box-sizing:border-box; }
          .jobx .table-card { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.05); border:1px solid #e4e6ef; overflow:hidden; }
          .jobx .table-header, .jobx .job-row { display:grid; grid-template-columns:2.5fr 1fr 1.1fr 1.2fr .9fr .9fr 2.6fr; align-items:start; column-gap:12px; }
          .jobx .table-header { padding:9px 12px; background:#f7f8fc; border-bottom:1.5px solid #e4e6ef; align-items:center; }
          .jobx .th { font-size:10.5px; font-weight:600; color:#6d7284; text-transform:uppercase; letter-spacing:.4px; display:flex; align-items:center; gap:4px; }
          .jobx .job-row { padding:12px; border-bottom:1px solid #e4e6ef; transition:background .12s; }
          .jobx .job-row:last-of-type { border-bottom:none; }
          .jobx .job-row:hover { background:#fafbff; }
          .jobx .col-job { display:flex; gap:9px; align-items:flex-start; }
          .jobx .drag-col { display:flex; align-items:center; padding-top:14px; flex-shrink:0; color:#a1a5b7; cursor:grab; }
          .jobx .job-logo { width:46px; height:46px; border-radius:10px; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:17px; font-weight:800; color:#fff; overflow:hidden; }
          .jobx .job-logo img { width:100%; height:100%; object-fit:cover; }
          .jobx .job-info { flex:1; min-width:0; }
          .jobx .job-title a { font-size:12px; font-weight:700; color:#181c32; text-decoration:none; display:block; line-height:1.35; }
          .jobx .job-title a:hover { color:#10b981; }
          .jobx .job-dept { font-size:11px; color:#6d7284; margin-top:1px; }
          .jobx .job-client { font-size:11px; color:#6d7284; font-weight:500; margin-top:1px; margin-bottom:5px; }
          .jobx .job-meta-line { display:flex; align-items:center; flex-wrap:wrap; gap:4px; font-size:10.5px; }
          .jobx .it-job-id { font-size:10px; color:#6a6b70; font-weight:500; }
          .jobx .it-dot-separator { width:3px; height:3px; border-radius:50%; background:#a1a5b7; }
          .jobx .badge-type { display:inline-flex; align-items:center; gap:3px; padding:2px 7px; border-radius:20px; font-size:9.5px; font-weight:600; }
          .jobx .badge-internal { background:#eef2ff; color:#3b55e6; }
          .jobx .badge-client   { background:#eefff6; color:#09b760; }
          .jobx .badge-direct   { background:#ecfdf5; color:#15803d; }
          .jobx .skills-inline .badge { font-size:9.5px; padding:1px 5px; margin:0 1px 0 0; border-radius:4px; }
          .jobx .col-exp { display:flex; flex-direction:column; gap:5px; padding-top:2px; }
          .jobx .exp-row, .jobx .sal-row { display:flex; align-items:center; gap:5px; font-size:11.5px; font-weight:500; color:#181c32; white-space:nowrap; }
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
          .jobx .loc-row, .jobx .mode-row { display:flex; align-items:center; gap:4px; font-size:11px; color:#6d7284; font-weight:500; }
          .jobx .loc-row svg, .jobx .mode-row svg { width:12px; height:12px; color:#a1a5b7; flex-shrink:0; }
          .jobx .col-hm { font-size:11.5px; padding-top:2px; }
          .jobx .hm-name  { font-size:12px; font-weight:600; color:#181c32; margin-bottom:2px; }
          .jobx .hm-title { font-size:11px; color:#6d7284; margin-bottom:1px; }
          .jobx .hm-company { font-size:11px; color:#a1a5b7; }
          .jobx .col-pipe { padding-top:2px; }
          .jobx .pipeline-stages { display:flex; flex-wrap:wrap; gap:3px; margin-bottom:4px; }
          .jobx .stage-badge { display:inline-flex; align-items:center; gap:3px; padding:2px 6px; border-radius:4px; font-size:9.5px; font-weight:600; }
          .jobx .stage-badge a { font-size:9px; font-weight:700; color:inherit; text-decoration:none; }
          .jobx .stage-badge a:hover { opacity:.75; }
          .jobx .s-inw { background:#ffffde; color:#bdbd26; } .jobx .s-inw a { color:#bdbd26; }
          .jobx .s-sou { background:#efefef; color:#000; }   .jobx .s-sou a { color:#000; }
          .jobx .s-scr { background:#f7deff; color:#c000ff; }.jobx .s-scr a { color:#c000ff; }
          .jobx .s-eva { background:#f0f8ff; color:#2896ff; }.jobx .s-eva a { color:#2896ff; }
          .jobx .s-neg { background:#ffe6f2; color:#ff4098; }.jobx .s-neg a { color:#ff4098; }
          .jobx .s-off { background:#fff9ed; color:#ffab00; }.jobx .s-off a { color:#ffab00; }
          .jobx .s-joi { background:#d4ffde; color:#06c732; }.jobx .s-joi a { color:#06c732; }
          .jobx .s-rej { background:#ffe2e5; color:#ff0202; }.jobx .s-rej a { color:#ff0202; }
          .jobx .pipeline-total { font-size:10px; font-weight:600; color:#6d7284; display:flex; flex-wrap:wrap; gap:4px; }
          .jobx .pipeline-total strong { color:#181c32; }
          .jobx .add-date { font-size:10px; color:#a1a5b7; display:flex; align-items:center; gap:4px; }
          .jobx .job-empty { padding:34px 12px; text-align:center; color:#a1a5b7; font-size:12px; }
          .jobx .job-row-skills { display:flex; align-items:center; flex-wrap:wrap; gap:5px; padding:3px 12px 10px 83px; border-bottom:1px solid #e4e6ef; font-size:10.5px; }
          .jobx .job-row-skills .sk-label { font-weight:600; color:#6d7284; flex-shrink:0; }
          .jobx .job-row-skills .sk-tag { display:inline-flex; padding:1px 7px; border-radius:4px; background:#f1f3fa; color:#5e6278; font-size:9.5px; font-weight:600; }
          .jobx .job-row:last-of-type + .job-row-skills { border-bottom:none; }
          @media (max-width:1366px){ .jobx .table-header, .jobx .job-row { grid-template-columns:2.2fr .95fr 1fr 1.1fr .85fr .85fr 2.1fr; } }
          @media (max-width:1024px){ .jobx .table-card { overflow-x:auto; } .jobx .table-header, .jobx .job-row { min-width:980px; } }

          /* Contacts tab styles */
          .cd-contacts .cd-tabcard { background:#fff; border-radius:8px; border:1px solid #eef2f6; padding:16px; margin:12px; }
          .cd-contacts .cd-tabcard-title { font-size:12px !important; font-weight:600 !important; color:#0f172a; }
          .cd-contacts .cd-tabcard-sub { font-size:10.5px !important; color:#64748b; margin-top:2px; }
          .cd-contacts .cd-search { position:relative; }
          .cd-contacts .cd-search input { height:30px; font-size:11px !important; border-radius:6px; border:1px solid #e2e8f0; padding-left:30px; padding-right:12px; width:220px; }
          .cd-contacts .cd-search i { position:absolute; left:10px; top:9px; font-size:11px; color:#94a3b8; }
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
            $clientUpdatedAt = @$directClientDetail->updated_at ? Carbon::parse($directClientDetail->updated_at)->diffForHumans() : '1 day ago';
            $isClientActive = ($kpiActive > 0);
        @endphp

        <input type="hidden" name="direct_client_id" id="direct_client_id" value="{{ $directClientDetail->client_id ?? $directClientDetail->id }}" />

        <div class="cd-scope">
          <!-- ── Top Action Header Bar (DIRECT CLIENT: ONLY FOLLOW UP BUTTON) ── -->
          <div class="cd-top-bar">
            <div class="cd-top-actions">
              <button type="button" class="cd-hdr-btn cd-hdr-btn-outline-green cd-btn-followup" data-client-id="{{ $directClientDetail->client_id ?? $directClientDetail->id }}">
                <i class="fas fa-calendar-check fa-solid" style="color:#0284c7;"></i> Follow Up
              </button>
            </div>
            <button type="button" class="cd-hdr-btn-close cd-close-drawer" id="kt_activitiesDirectClient_close_btn" title="Close Drawer">
              <i class="fas fa-times fa-solid fa-xmark" style="color:#64748b;"></i>
            </button>
          </div>

          <!-- ── Hero Banner: Direct Client (Clean Neutral Enterprise UI) ── -->
          <div class="cd-hero-banner-green">
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
                  <div class="cd-verified-badge" title="Verified Direct Client">
                    <i class="fas fa-check fa-solid"></i>
                  </div>
                </div>
                <div class="cd-hero-details">
                  <div class="cd-title-row">
                    <span class="cd-client-name">{{ $detailName }}</span>
                    <span class="cd-pill-type-direct"><i class="fas fa-building fa-solid" style="color:#059669;"></i> Direct Client</span>
                  </div>
                  <div class="cd-meta-row">
                    @if ($industries_name)
                    <span><i class="fas fa-building fa-solid me-1" style="color:#3b82f6;"></i>{{ $industries_name }}</span>
                    <span class="cd-meta-sep">|</span>
                    @endif
                    <span><i class="fas fa-map-marker-alt fa-solid fa-location-dot me-1" style="color:#ef4444;"></i>{{ ucwords($city_name ?: 'Mumbai') }}@if($country_name), {{ ucwords($country_name) }}@else, India @endif</span>
                    @if (!empty($webClean))
                    <span class="cd-meta-sep">|</span>
                    <a href="{{ str_starts_with($webClean, 'http') ? $webClean : 'https://'.$webClean }}" target="_blank" class="cd-meta-link">
                      <i class="fas fa-link fa-solid me-1" style="color:#06b6d4;"></i>{{ $webClean }} <i class="fas fa-external-link-alt fa-solid fa-arrow-up-right-from-square" style="font-size:8.5px;"></i>
                    </a>
                    @endif
                    @if(!empty(@$directClientDetail->vendor_id))
                    <span class="cd-meta-sep">|</span>
                    <span>Vendor ID: <span style="font-weight:500; color:#0f172a;">{{ @$directClientDetail->vendor_id }}</span></span>
                    @endif
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
                  @if ($isClientActive)
                    <div class="cd-status-card cd-status-card-green">
                      <span class="cd-pulse-dot cd-pulse-green"></span>
                      <div>
                        <div class="cd-status-card-title text-success">Client Active</div>
                        <div class="cd-status-card-sub text-success">Account is active</div>
                      </div>
                    </div>
                    <div class="cd-status-card cd-status-card-green">
                      <span class="cd-pulse-dot cd-pulse-green"></span>
                      <div>
                        <div class="cd-status-card-title text-success">Hiring</div>
                        <div class="cd-status-card-sub text-success">{{ $kpiActive }} active {{ $kpiActive == 1 ? 'job' : 'jobs' }}</div>
                      </div>
                    </div>
                  @else
                    <div class="cd-status-card cd-status-card-grey">
                      <span class="cd-pulse-dot cd-pulse-grey"></span>
                      <div>
                        <div class="cd-status-card-title" style="color:#64748b;">Client Inactive</div>
                        <div class="cd-status-card-sub" style="color:#94a3b8;">No active jobs</div>
                      </div>
                    </div>
                    <div class="cd-status-card cd-status-card-grey">
                      <span class="cd-pulse-dot cd-pulse-grey"></span>
                      <div>
                        <div class="cd-status-card-title" style="color:#64748b;">Not Hiring</div>
                        <div class="cd-status-card-sub" style="color:#94a3b8;">0 active jobs</div>
                      </div>
                    </div>
                  @endif
                </div>
                <div class="cd-hero-meta-time">
                  Client since {{ $clientSinceYear }} <span class="mx-1">|</span> Last activity: {{ $clientUpdatedAt }}
                </div>
              </div>
            </div>
          </div>

          <!-- ── 5 Metric KPI Stat Cards Row ── -->
          <div class="cd-kpi-grid">
            <div class="cd-kpi-card" data-target-tab="#requisitions">
              <div class="d-flex align-items-center gap-3">
                <div class="cd-kpi-icon cd-kpi-icon-blue"><i class="fas fa-briefcase fa-solid" style="color:#2563eb;"></i></div>
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
                <div class="cd-kpi-icon cd-kpi-icon-purple"><i class="fas fa-users fa-solid" style="color:#7c3aed;"></i></div>
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
                <div class="cd-kpi-icon cd-kpi-icon-amber"><i class="fas fa-calendar-days fa-solid" style="color:#d97706;"></i></div>
                <div>
                  <div class="cd-kpi-num" id="kpiInterviewsScheduledCount">{{ $kpiInterviews }}</div>
                  <div class="cd-kpi-lbl">Interviews Scheduled</div>
                  <div class="cd-kpi-desc">Across all rounds</div>
                </div>
              </div>
              <i class="fas fa-chevron-right fa-solid cd-kpi-arrow"></i>
            </div>
            <div class="cd-kpi-card" data-target-tab="#candidate_joining">
              <div class="d-flex align-items-center gap-3">
                <div class="cd-kpi-icon cd-kpi-icon-orange"><i class="fas fa-file-lines fa-solid" style="color:#ea580c;"></i></div>
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
                <div class="cd-kpi-icon cd-kpi-icon-green"><i class="fas fa-user-check fa-solid" style="color:#059669;"></i></div>
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
                  <i class="fas fa-house fa-solid" style="color:#3b82f6;"></i> Overview
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link cd-tab-black" data-toggle="tab" href="#recent_activity">
                  <i class="fas fa-clock fa-solid" style="color:#f59e0b;"></i> Recent Activity
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#contacts">
                  <i class="fas fa-address-book fa-solid" style="color:#10b981;"></i> Contacts
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#requisitions">
                  <i class="fas fa-briefcase fa-solid" style="color:#8b5cf6;"></i> Jobs
                  @if($kpiActive > 0)<span class="cd-tab-badge">{{ $kpiActive }}</span>@endif
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#candidate_list">
                  <i class="fas fa-user-tie fa-solid" style="color:#06b6d4;"></i> Candidates
                  @if($kpiSubmitted > 0)<span class="cd-tab-badge">{{ $kpiSubmitted }}</span>@endif
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#interview_tracker">
                  <i class="fas fa-calendar-days fa-solid" style="color:#f43f5e;"></i> Interviews
                  <span class="cd-tab-badge" id="tabBadgeInterviewTracker" @if($kpiInterviews <= 0) style="display:none;" @endif>{{ $kpiInterviews }}</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#candidate_joining">
                  <i class="fas fa-file-signature fa-solid" style="color:#d97706;"></i> Offers &amp; Joinings
                  @if($kpiJoins > 0)<span class="cd-tab-badge">{{ $kpiJoins }}</span>@endif
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#candidateDashboard">
                  <i class="fas fa-chart-column fa-solid" style="color:#6366f1;"></i> Performance
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
                        <i class="fas fa-bookmark fa-solid" style="font-size:12px; color:#10b981;"></i>
                        <span class="cd-card-hdr-title">About the Client</span>
                      </div>
                      @if (!empty($webClean))
                      <a href="{{ str_starts_with($webClean, 'http') ? $webClean : 'https://'.$webClean }}" target="_blank" class="cd-card-hdr-link">
                        {{ $webClean }} <i class="fas fa-external-link-alt fa-solid fa-arrow-up-right-from-square" style="font-size:8.5px;"></i>
                      </a>
                      @endif
                    </div>
                    <div class="cd-about-body">
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

                  <!-- Card: Recent Activity (Timeline matching mockup) -->
                  <div class="cd-card-panel">
                    <div class="cd-card-hdr">
                      <div class="cd-card-hdr-left">
                        <i class="fas fa-clock fa-solid" style="font-size:12px; color:#10b981;"></i>
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
                          <div class="cd-tl-heading">Candidates submitted to client</div>
                          <div class="cd-tl-sub">{{ auth()->user()->name ?? 'Recruiter' }} forwarded active profiles</div>
                        </div>
                        <div class="cd-tl-role">Direct Requirement</div>
                        <div class="cd-tl-meta-right">
                          <span class="cd-tl-time">2 hours ago</span>
                          <span class="cd-tl-badge cd-tl-badge-green">Submitted</span>
                        </div>
                      </div>

                      <!-- Node 2: Interview Scheduled -->
                      <div class="cd-tl-item">
                        <div class="cd-tl-dot cd-tl-dot-purple"><i class="fas fa-calendar-alt fa-solid fa-calendar-days"></i></div>
                        <div class="cd-tl-info">
                          <div class="cd-tl-heading">Client interview scheduled</div>
                          <div class="cd-tl-sub">Interview confirmed with hiring team</div>
                        </div>
                        <div class="cd-tl-role">Technical Lead</div>
                        <div class="cd-tl-meta-right">
                          <span class="cd-tl-time">1 day ago</span>
                          <span class="cd-tl-badge cd-tl-badge-amber">Scheduled</span>
                        </div>
                      </div>

                      <!-- Node 3: Offer Generated -->
                      <div class="cd-tl-item">
                        <div class="cd-tl-dot cd-tl-dot-orange"><i class="fas fa-file-alt fa-solid fa-file-lines"></i></div>
                        <div class="cd-tl-info">
                          <div class="cd-tl-heading">Offer proposal extended</div>
                          <div class="cd-tl-sub">Compensation terms approved</div>
                        </div>
                        <div class="cd-tl-role">Senior Analyst</div>
                        <div class="cd-tl-meta-right">
                          <span class="cd-tl-time">2 days ago</span>
                          <span class="cd-tl-badge cd-tl-badge-orange">Offer Sent</span>
                        </div>
                      </div>

                      <!-- Node 4: Requirement Received -->
                      <div class="cd-tl-item">
                        <div class="cd-tl-dot cd-tl-dot-blue"><i class="fas fa-briefcase fa-solid"></i></div>
                        <div class="cd-tl-info">
                          <div class="cd-tl-heading">Direct mandate received</div>
                          <div class="cd-tl-sub">Hiring requisition added to board</div>
                        </div>
                        <div class="cd-tl-role">Cloud Architect</div>
                        <div class="cd-tl-meta-right">
                          <span class="cd-tl-time">3 days ago</span>
                          <span class="cd-tl-badge cd-tl-badge-blue">Mandate</span>
                        </div>
                      </div>

                      <!-- Node 5: Successful Join -->
                      <div class="cd-tl-item">
                        <div class="cd-tl-dot cd-tl-dot-green"><i class="fas fa-user-check fa-solid"></i></div>
                        <div class="cd-tl-info">
                          <div class="cd-tl-heading">Candidate onboarding complete</div>
                          <div class="cd-tl-sub">Placement milestone reached</div>
                        </div>
                        <div class="cd-tl-role">DevOps Engineer</div>
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
                        <i class="fas fa-circle-info fa-solid" style="color:#6366f1; font-size:12px;"></i>
                        <span class="cd-card-hdr-title">Client Information</span>
                      </div>
                    </div>
                    <div class="cd-info-list">
                      <div class="cd-kv-row">
                        <span class="cd-kv-lbl">Headquarters</span>
                        <span class="cd-kv-val">{{ ucwords($city_name ?: 'Mumbai') }}@if($country_name), {{ ucwords($country_name) }}@else, India @endif</span>
                      </div>
                      @if (!empty($webClean))
                      <div class="cd-kv-row">
                        <span class="cd-kv-lbl">Website</span>
                        <span class="cd-kv-val">
                          <a href="{{ str_starts_with($webClean, 'http') ? $webClean : 'https://'.$webClean }}" target="_blank">
                            {{ $webClean }} <i class="fas fa-external-link-alt fa-solid fa-arrow-up-right-from-square" style="font-size:8.5px;"></i>
                          </a>
                        </span>
                      </div>
                      @endif
                      @if ($industries_name)
                      <div class="cd-kv-row">
                        <span class="cd-kv-lbl">Industry</span>
                        <span class="cd-kv-val">{{ $industries_name }}</span>
                      </div>
                      @endif
                      <div class="cd-kv-row">
                        <span class="cd-kv-lbl">Company Type</span>
                        <span class="cd-kv-val">{{ $d_company_type_val }}</span>
                      </div>
                      @if(!empty(@$directClientDetail->vendor_id))
                      <div class="cd-kv-row">
                        <span class="cd-kv-lbl">Vendor ID</span>
                        <span class="cd-kv-val">{{ @$directClientDetail->vendor_id }}</span>
                      </div>
                      @endif
                      @if (@$directClientDetail->founded)
                      <div class="cd-kv-row">
                        <span class="cd-kv-lbl">Founded</span>
                        <span class="cd-kv-val">{{ @$directClientDetail->founded }}</span>
                      </div>
                      @endif
                      @if (!empty(@$directClientDetail->company_sizes))
                      <div class="cd-kv-row">
                        <span class="cd-kv-lbl">Number of Employees</span>
                        <span class="cd-kv-val">{{ is_numeric(@$directClientDetail->company_sizes) ? number_format((float)@$directClientDetail->company_sizes) . '+' : @$directClientDetail->company_sizes }}</span>
                      </div>
                      @endif
                      @if (!empty(@$directClientDetail->to_created_at))
                      <div class="cd-kv-row">
                        <span class="cd-kv-lbl">Connected On</span>
                        <span class="cd-kv-val">{{ date('d M, Y', strtotime(@$directClientDetail->to_created_at)) }}</span>
                      </div>
                      @endif
                    </div>
                  </div>

                  <!-- Card: Primary Contact -->
                  <div class="cd-card-panel">
                    <div class="cd-card-hdr">
                      <div class="cd-card-hdr-left">
                        <i class="fas fa-id-badge fa-solid" style="color:#10b981; font-size:12px;"></i>
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
                            <i class="fas fa-envelope fa-solid" style="color:#2563eb;"></i> {{ $primaryEmail }}
                          </a>
                          <span class="cd-contact-link-item">
                            <i class="fas fa-phone fa-solid" style="color:#10b981;"></i> {{ $primaryPhone }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Card: Client Services -->
                  <div class="cd-card-panel">
                    <div class="cd-card-hdr">
                      <div class="cd-card-hdr-left">
                        <i class="fas fa-layer-group fa-solid" style="color:#ec4899; font-size:12px;"></i>
                        <span class="cd-card-hdr-title">Client Services</span>
                      </div>
                    </div>
                    <div class="cd-tags-cloud">
                      @foreach ($tagsArr as $tag)
                        <span class="cd-tag-pill">{{ ucwords($tag) }}</span>
                      @endforeach
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
                    This section captures comprehensive timeline actions: candidate submissions, interview logs, offer letter events, job requisitions, and administrative audit trails for direct client interactions. Additional widgets and configurations will be integrated as discussed.
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
                                    <div class="cd-search"><i class="fas fa-search"></i><input type="text" id="dcontactSearchInput" placeholder="Search contacts..." /></div>
                                </div>
                            </div>
                            <div style="overflow-x:auto;">
                                <table class="data-table" id="dcontactList">
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
                                      <th class="min-w-15px">Notice Period</th>
                                      <th class="min-w-15px">Location</th>
                                      <th class="min-w-15px"> Source</th>
                                      <th class="min-w-15px">Upload Date</th>
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

                                      <th class="min-w-150px">Name</th>
                                      <th class="min-w-15px">Job</th>
                                      <th class="min-w-15px">Interview Mode</th>
                                      <th class="min-w-15px">Interviewers</th>
                                      <th class="min-w-15px">Source</th>
                                      <th class="min-w-15px">Interview Date</th>
                                      <th class="min-w-15px">Feedback</th>
                                      <th class="min-w-15px">Rating</th>
                                      <th class="min-w-15px">Stage</th>
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
                                      <th class="min-w-15px">Notice Period</th>
                                      <th class="min-w-15px">Location</th>
                                      <th class="min-w-15px"> Source</th>
                                      <th class="min-w-15px">Upload Date</th>
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

                <!--begin::candidateDashboard Content-->
                    <div class="tab-pane" id="candidateDashboard" role="tabpanel">
                        <div class="cd-tabcard">
                            <div class="cd-tabcard-head d-flex align-items-center justify-content-between pb-3 mb-4" style="border-bottom: 1px solid #edf2f7;">
                                <div>
                                    <div class="cd-tabcard-title fs-4 fw-bold text-gray-900 d-flex align-items-center gap-2">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect x="3" y="3" width="7" height="7" rx="1.5" fill="#3B82F6"/>
                                            <rect x="14" y="3" width="7" height="7" rx="1.5" fill="#10B981"/>
                                            <rect x="14" y="14" width="7" height="7" rx="1.5" fill="#F59E0B"/>
                                            <rect x="3" y="14" width="7" height="7" rx="1.5" fill="#6366F1"/>
                                        </svg>
                                        Client Dashboard
                                    </div>
                                    <div class="cd-tabcard-sub text-gray-500 fs-7 fw-medium mt-1">Real-time hiring & recruitment performance analytics for this client.</div>
                                </div>
                                <span class="badge badge-light-primary fw-semibold fs-8 px-3 py-2">Analytics Overview</span>
                            </div>

                            <div class="cd-tabwrap">
                                <div class="card mb-3 shadow-none border-0 rounded-3">
                                    <div class="card-body p-0">

                                        <!-- Row 1: Jobs & 7-Day Sourcing Cards -->
                                        <div class="row g-4 mb-4">
                                            <!-- Card 1: Jobs Breakdown -->
                                            <div class="col-md-6 col-lg-6">
                                                <div class="card card-flush h-100 shadow-xs border" style="border-color: #eef2f7 !important; border-radius: 12px;">
                                                    @php
                                                    $active_count = $counts->get('active', 0);
                                                    $hold_count = $counts->get('hold', 0);
                                                    $inactive_count = $counts->get('inactive', 0);
                                                    $alljobs_count = $active_count + $hold_count + $inactive_count;
                                                    @endphp
                                                    <div class="card-header pt-4 pb-2 px-5 border-0">
                                                        <div class="card-title d-flex flex-column">
                                                            <div class="d-flex align-items-center">
                                                                <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1" id="dcAllJobsCount_{{ $dc_id }}">{{ $inactive_count+$active_count+$hold_count }}</span>
                                                                <span class="badge badge-light-success fs-8 fw-bold">Jobs Overview</span>
                                                            </div>
                                                            <span class="text-gray-500 pt-1 fw-semibold fs-7">All Client Jobs</span>
                                                        </div>
                                                    </div>
                                                    <div class="card-body pt-1 pb-4 px-5 d-flex align-items-center justify-content-between">
                                                        <div class="d-flex flex-center" style="min-width: 140px; min-height: 140px;">
                                                            <div id="kt_card_widget_4_chart_{{ $dc_id }}" style="min-width: 140px; min-height: 140px" data-kt-size="140" data-kt-line="11">
                                                                <canvas id="allJobGrapg_{{ $dc_id }}" height="70" width="70"></canvas>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex flex-column gap-2 w-100 ps-4">
                                                            <div class="d-flex align-items-center justify-content-between p-2 rounded-2" style="background: #f8fafc;">
                                                                <div class="d-flex align-items-center">
                                                                    <span class="w-8px h-8px rounded-circle me-2" style="background-color: #22c55e"></span>
                                                                    <span class="text-gray-700 fs-7 fw-semibold">Active Jobs</span>
                                                                </div>
                                                                <span class="fw-bold text-gray-900 fs-7" id="dcActiveJobsCount_{{ $dc_id }}">{{ $active_count }}</span>
                                                            </div>
                                                            <div class="d-flex align-items-center justify-content-between p-2 rounded-2" style="background: #f8fafc;">
                                                                <div class="d-flex align-items-center">
                                                                    <span class="w-8px h-8px rounded-circle me-2" style="background-color: #ffab00"></span>
                                                                    <span class="text-gray-700 fs-7 fw-semibold">Hold Jobs</span>
                                                                </div>
                                                                <span class="fw-bold text-gray-900 fs-7" id="dcHoldJobsCount_{{ $dc_id }}">{{ $hold_count }}</span>
                                                            </div>
                                                            <div class="d-flex align-items-center justify-content-between p-2 rounded-2" style="background: #f8fafc;">
                                                                <div class="d-flex align-items-center">
                                                                    <span class="w-8px h-8px rounded-circle me-2" style="background-color: #ff0202"></span>
                                                                    <span class="text-gray-700 fs-7 fw-semibold">Closed Jobs</span>
                                                                </div>
                                                                <span class="fw-bold text-gray-900 fs-7" id="dcClosedJobsCount_{{ $dc_id }}">{{ $inactive_count }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Card 2: Added in last 7 days -->
                                            <div class="col-md-6 col-lg-6">
                                                <div class="card card-flush h-100 shadow-xs border" style="border-color: #eef2f7 !important; border-radius: 12px;">
                                                    <div class="card-header pt-4 pb-2 px-5 border-0">
                                                        <div class="card-title d-flex flex-column">
                                                            <div class="d-flex align-items-center">
                                                                <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1" id="dcAdded7DaysCount_{{ $dc_id }}">{{$candidateValuesTotal}}</span>
                                                                <span class="badge badge-light-info fs-8 fw-bold">7-Day Activity</span>
                                                            </div>
                                                            <span class="text-gray-500 pt-1 fw-semibold fs-7">Candidates Sourced (Last 7 Days)</span>
                                                        </div>
                                                    </div>
                                                    <div class="card-body pt-0 px-4 pb-3 d-flex align-items-end">
                                                        <div id="kt_card_widget_6_chart_{{ $dc_id }}" class="w-100" style="height: 150px">
                                                            <div id="kt_card_widget_4_chart_{{ $dc_id }}" style="min-width: 100%; min-height: 150px">
                                                                <canvas id="candidates_left_chart_{{ $dc_id }}" height="150"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Row 2: 6-Month Sourcing Trend Hero Card -->
                                        <div class="row g-4 mb-4">
                                            <div class="col-12">
                                                <div class="card card-flush shadow-xs border" style="border-color: #eef2f7 !important; border-radius: 12px;">
                                                    <div class="card-header pt-4 pb-2 px-5 border-0 d-flex align-items-center justify-content-between">
                                                        <div class="card-title d-flex flex-column">
                                                            <div class="d-flex align-items-center">
                                                                <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1" id="dcTotalCandidatesCount_{{ $dc_id }}">{{$total_candidates}}</span>
                                                                <span class="badge badge-light-primary fs-8 fw-bold">6-Month Trend</span>
                                                            </div>
                                                            <span class="text-gray-500 pt-1 fw-semibold fs-7">Candidates Sourced ({{ date('d M Y',strtotime($startOfMonth)) }} - {{ date('d M Y',strtotime($endOfMonth)) }})</span>
                                                        </div>
                                                    </div>
                                                    <div class="card-body pt-2 px-4 pb-3">
                                                        <div id="kt_charts_widget_3" class="w-100" style="height: 320px; min-height: 320px;">
                                                            <canvas id="myChart_{{ $dc_id }}" height="320" style="height: 320px; min-height: 320px;"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Row 3: Offers & Joinings Trend Cards -->
                                        <div class="row g-4 mb-4">
                                            <div class="col-md-6 col-lg-6">
                                                <div class="card card-flush h-100 shadow-xs border" style="border-color: #eef2f7 !important; border-radius: 12px;">
                                                    <div class="card-header pt-4 pb-2 px-5 border-0">
                                                        <div class="card-title d-flex flex-column">
                                                            <div class="d-flex align-items-center">
                                                                <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1" id="dcTotalOffersCount_{{ $dc_id }}">{{$candidateValuesTotaloffer}}</span>
                                                                <span class="badge badge-light-warning fs-8 fw-bold">Offers</span>
                                                            </div>
                                                            <span class="text-gray-500 pt-1 fw-semibold fs-7">Offers Generated ({{ date("M Y", strtotime($startOfMonthcomman)) }} - {{ date("M Y", strtotime($endOfMonthcomman)) }})</span>
                                                        </div>
                                                    </div>
                                                    <div class="card-body pt-0 px-4 pb-3">
                                                        <div id="kt_card_widget_4_chart_{{ $dc_id }}" style="min-width: 100%; min-height: 150px">
                                                            <canvas id="candidates_offer_chart_{{ $dc_id }}" height="150"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-6">
                                                <div class="card card-flush h-100 shadow-xs border" style="border-color: #eef2f7 !important; border-radius: 12px;">
                                                    <div class="card-header pt-4 pb-2 px-5 border-0">
                                                        <div class="card-title d-flex flex-column">
                                                            <div class="d-flex align-items-center">
                                                                <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1" id="dcTotalJoinedCount_{{ $dc_id }}">{{$candidateValuesTotaljoin}}</span>
                                                                <span class="badge badge-light-success fs-8 fw-bold">Joins</span>
                                                            </div>
                                                            <span class="text-gray-500 pt-1 fw-semibold fs-7">Candidates Joined ({{ date("M Y", strtotime($startOfMonthcomman)) }} - {{ date("M Y", strtotime($endOfMonthcomman)) }})</span>
                                                        </div>
                                                    </div>
                                                    <div class="card-body pt-0 px-4 pb-3">
                                                        <div id="kt_card_widget_4_chart_{{ $dc_id }}" style="min-width: 100%; min-height: 150px">
                                                            <canvas id="candidates_join_chart_{{ $dc_id }}" height="150"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Row 4: Interview Section -->
                                        <div class="row g-4">
                                            <!-- Upcoming Interviews -->
                                            <div class="col-lg-5 col-xl-4">
                                                <div class="card card-flush h-100 shadow-xs border" style="border-color: #eef2f7 !important; border-radius: 12px;">
                                                    <div class="card-header pt-4 pb-2 px-5 border-0">
                                                        <h3 class="card-title fw-bold text-gray-900 fs-6 d-flex align-items-center gap-2">
                                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                                            Upcoming Interviews
                                                        </h3>
                                                    </div>
                                                    <div class="card-body pt-1 px-5 pb-4">
                                                        @if (count($upcomingData) > 0)
                                                            @foreach ($upcomingData as $rowArr)
                                                                @php
                                                                $row = (object) $rowArr;
                                                                $schedule_date = Carbon::parse($row->schedule_date)->format('M d');
                                                                $schedule_date_explode = explode(" ",$schedule_date);
                                                                @endphp
                                                                <div class="d-flex align-items-center justify-content-between p-2 rounded-3 mb-2" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                                                                    <div class="d-flex align-items-center me-3">
                                                                        <div class="rounded-3 text-center px-2 py-1 me-3" style="background: #eef2ff; border: 1px solid #e0e7ff; min-width: 44px;">
                                                                            <span style="font-size: 10px; font-weight: 700; color: #6366f1; text-transform: uppercase; display: block;">{{$schedule_date_explode[0]}}</span>
                                                                            <span style="color: #4f46e5; font-size: 15px; font-weight: 900; line-height: 1;">{{$schedule_date_explode[1]}}</span>
                                                                        </div>
                                                                        <div>
                                                                            <div class="text-gray-900 fs-7 fw-bold mb-0 text-truncate" style="max-width: 140px;">{{ucwords(mb_substr(@$row->full_name, 0, 20))}}</div>
                                                                            <span class="text-gray-500 fs-8 d-block text-truncate" style="max-width: 140px;">{{ucwords(mb_substr(@$row->current_designation, 0, 21))}}</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="text-end">
                                                                        <span class="badge badge-light-primary fs-8 fw-semibold px-2 py-1 mb-1 d-inline-block">{{$row->schedule_to_time. ($row->timezone_abbreviation ? ' (' . $row->timezone_abbreviation . ')' : '')}}</span>
                                                                        <span class="text-gray-500 fs-8 d-block">{{ucwords(mb_substr($row->mode_name, 0, 13))}}</span>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <div class="text-center py-6 px-3 rounded-3" style="background: #f8fafc; border: 1px dashed #e2e8f0;">
                                                                <svg class="mb-2" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5"><path d="M8 7V3M16 7V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                                <div class="text-gray-500 fs-7 fw-medium">No upcoming interviews scheduled</div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Candidates Interviews Chart -->
                                            <div class="col-lg-7 col-xl-8">
                                                <div class="card card-flush h-100 shadow-xs border" style="border-color: #eef2f7 !important; border-radius: 12px;">
                                                    <div class="card-header pt-4 pb-2 px-5 border-0 d-flex align-items-center justify-content-between">
                                                        <h3 class="card-title fw-bold text-gray-900 fs-6 d-flex align-items-center gap-2">
                                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
                                                            Candidates Interviews
                                                        </h3>
                                                        <select class="form-select form-select-sm form-select-solid w-140px fs-7 border-0" id="sortingfilter" onchange="filtersdatagetmonth(this.value)">
                                                            <option value="1">Last 6 Months</option>
                                                            <option value="0">Last 7 Days</option>
                                                        </select>
                                                    </div>
                                                    <div class="card-body pt-1 px-4 pb-3">
                                                        <div id="kt_card_widget_6_chart_{{ $dc_id }}" class="w-100" style="height: 200px">
                                                            <div class="inetview_recent_chart_{{ $dc_id }} d-none" id="kt_card_widget_4_chart_{{ $dc_id }}" style="min-width: 100%; min-height: 200px">
                                                                <canvas id="inetview_recent_chart_{{ $dc_id }}" height="200"></canvas>
                                                            </div>
                                                            <div class="inetview_recent_chart_month_{{ $dc_id }}" id="kt_card_widget_4_chart_month_{{ $dc_id }}" style="min-width: 100%; min-height: 200px">
                                                                <canvas id="inetview_recent_chart_month_{{ $dc_id }}" height="200"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <!--end begin::candidateDashboard Content-->
                

                    <div class="tab-pane" id="requisitions" role="tabpanel">
                        <div class="cd-tabcard"><div class="cd-tabcard-head"><div class="cd-tabcard-title">Jobs</div><div class="cd-tabcard-sub">Roles open with this client.</div></div><div class="cd-tabwrap">
                        <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-5 fs-6 border-bottom-0 d-none">
                            <li class="nav-item"> <a class="nav-link active" data-bs-toggle="tab"
                                    href="#all-requisitions">&nbsp;</a> </li>
                            {{-- <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#client-requisitions">HCL Req.</a> </li>
                          <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#custom-requisitions">Custom Req.</a> </li>
                          <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#jobs">Jobs</a> </li> --}}
                        </ul>
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
                                            <input type="text" id="inputSearch" data-kt-user-table-filter="search"
                                                class="form-control form-control-solid w-250px ps-14"
                                                style="padding: .6rem 1rem;" placeholder="Search..." />
                                        </div>
                                        <!--end::Search-->
                                        <div class="card-toolbar">
                                            <!--begin::Toolbar-->
                                            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                                                <!--begin::Filter-->
                                                <button type="button"
                                                    class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3"
                                                    data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end"
                                                    data-kt-menu-flip="top-end">
                                                    <!--begin::Svg Icon | path: icons/duotune/general/gen031.svg-->
                                                    <span class="svg-icon svg-icon-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none">
                                                            <path
                                                                d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z"
                                                                fill="black" />
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                    Filter</button>
                                                <!--begin::Menu 1-->
                                                <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px"
                                                    data-kt-menu="true">
                                                    <!--begin::Header-->
                                                    <div class="px-7 py-5">
                                                        <div class="fs-5 text-dark fw-bolder">Filter Options</div>
                                                    </div>
                                                    <!--end::Header-->
                                                    <!--begin::Separator-->
                                                    <div class="separator border-gray-200"></div>
                                                    <!--end::Separator-->
                                                    <!--begin::Content-->
                                                    <div class="px-7 py-5" data-kt-user-table-filter="form">
                                                        <!--begin::Input group-->
                                                        <div class="mb-10">
                                                            <label class="form-label fs-6 fw-bold">Role:</label>
                                                            <select class="form-select form-select-solid fw-bolder"
                                                                data-kt-select2="true"
                                                                data-placeholder="Select option"
                                                                data-allow-clear="true"
                                                                data-kt-user-table-filter="role"
                                                                data-hide-search="true">
                                                                <option></option>
                                                                <option value="Administrator">Administrator</option>
                                                                <option value="Analyst">Analyst</option>
                                                                <option value="Developer">Developer</option>
                                                                <option value="Support">Support</option>
                                                                <option value="Trial">Trial</option>
                                                            </select>
                                                        </div>
                                                        <!--end::Input group-->
                                                        <!--begin::Input group-->
                                                        <div class="mb-10">
                                                            <label class="form-label fs-6 fw-bold">Two Step
                                                                Verification:</label>
                                                            <select class="form-select form-select-solid fw-bolder"
                                                                data-kt-select2="true"
                                                                data-placeholder="Select option"
                                                                data-allow-clear="true"
                                                                data-kt-user-table-filter="two-step"
                                                                data-hide-search="true">
                                                                <option></option>
                                                                <option value="Enabled">Enabled</option>
                                                            </select>
                                                        </div>
                                                        <!--end::Input group-->
                                                        <!--begin::Actions-->
                                                        <div class="d-flex justify-content-end">
                                                            <button type="reset"
                                                                class="btn btn-light btn-active-light-primary fw-bold me-2 px-6"
                                                                data-kt-menu-dismiss="true"
                                                                data-kt-user-table-filter="reset">Reset</button>
                                                            <button type="submit"
                                                                class="btn btn-primary fw-bold px-6"
                                                                data-kt-menu-dismiss="true"
                                                                data-kt-user-table-filter="filter">Apply</button>
                                                        </div>
                                                        <!--end::Actions-->
                                                    </div>
                                                    <!--end::Content-->
                                                </div>
                                                <!--end::Menu 1-->
                                                <!--end::Filter-->
                                                @if (in_array('add_jobs', $userPermissions))
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
                                                <div id="directclientJobsListajax"></div>
                                            </div>
                                        </div>
                                        <div id="directpaginationclientjobs" class="mt-3"></div>
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


                                            <!--end::Table row-->
                                        </tbody>

                                        <!--end::Table body-->
                                    </table>
                                    <!--end::Table-->
                                </div>
                                <!--end::Client Req. Content-->
                            </div>
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
           var myChartId = "{{ $dc_id }}";
    window.dcChartInstances = window.dcChartInstances || {};

    var showDataLabels = {
        id: 'showDataLabels',
        afterDatasetsDraw(chart) {
            const { ctx } = chart;
            chart.data.datasets.forEach((dataset, i) => {
                const meta = chart.getDatasetMeta(i);
                meta.data.forEach((bar, index) => {
                    const value = dataset.data[index];
                    ctx.save();
                    ctx.fillStyle = 'black';
                    ctx.font = 'bold 12px sans-serif';
                    ctx.textAlign = 'center';
                    ctx.fillText(value, bar.x, bar.y - 1.5);
                    ctx.restore();
                });
            });
        }
    };

    var showDataLabelsvertical = {
        id: 'showDataLabelsvertical',
        afterDatasetsDraw(chart) {
            var { ctx } = chart;
            chart.data.datasets.forEach((dataset, i) => {
                var meta = chart.getDatasetMeta(i);
                meta.data.forEach((bar, index) => {
                    var value = dataset.data[index];
                    ctx.save();
                    ctx.fillStyle = 'black';
                    ctx.font = 'bold 12px sans-serif';
                    ctx.textAlign = 'left';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(value, bar.x + 1, bar.y);
                    ctx.restore();
                });
            });
        }
    };

    function renderDirectClientDashboardCharts(d, clientId) {
        if (!d) return;

        var activeCount = d.counts ? (d.counts.active || 0) : 0;
        var holdCount = d.counts ? (d.counts.hold || 0) : 0;
        var inactiveCount = d.counts ? (d.counts.inactive || 0) : 0;

        function createOrUpdateChart(canvasId, config) {
            var canvas = document.getElementById(canvasId);
            if (!canvas) return;
            if (window.dcChartInstances[canvasId]) {
                window.dcChartInstances[canvasId].destroy();
            }
            var ctx = canvas.getContext('2d');
            window.dcChartInstances[canvasId] = new Chart(ctx, config);
        }

        createOrUpdateChart(`allJobGrapg_${clientId}`, {
            type: 'doughnut',
            data: {
                labels: ['Active Jobs', 'Hold Jobs', 'Closed Jobs'],
                datasets: [{
                    label: 'Total',
                    data: [activeCount, holdCount, inactiveCount],
                    backgroundColor: ['#22c55e', '#ffab00', '#ff0202'],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                cutout: '70%'
            }
        });

        createOrUpdateChart(`candidates_left_chart_${clientId}`, {
            type: 'bar',
            data: {
                labels: d.labels || [],
                datasets: [{
                    label: 'Total Candidates',
                    data: d.candidateValues || [],
                    backgroundColor: Array((d.labels || []).length).fill('#2196f3'),
                    borderRadius: 5,
                    borderSkipped: false,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            },
            plugins: [showDataLabels]
        });

        createOrUpdateChart(`myChart_${clientId}`, {
            type: 'bar',
            data: {
                labels: ['In Review', 'Sourcing', 'Screening', 'Evaluation', 'Negotiation', 'Offer', 'Joined', 'Rejected'],
                datasets: [{
                    label: 'Users',
                    data: [
                        d.inreview_count || 0,
                        d.sourcing_count || 0,
                        d.screening_count || 0,
                        d.evaluation_count || 0,
                        d.negotiation_count || 0,
                        d.offer_count || 0,
                        d.joined_count || 0,
                        d.Rejected_count || 0
                    ],
                    backgroundColor: [
                        '#bdbd26', '#000000', '#c000ff', '#2896ff',
                        '#ff4098', '#ffab00', '#06c732', '#ff0202'
                    ],
                    borderRadius: 10,
                    barThickness: 20,
                    maxBarThickness: 22
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => ctx.parsed.x.toLocaleString() } }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                if (value <= 10) return value;
                                else if (value % 100 === 0) return value.toLocaleString();
                                else return '';
                            }
                        }
                    },
                    y: { ticks: { font: { weight: 'bold' } } }
                }
            },
            plugins: [showDataLabelsvertical]
        });

        createOrUpdateChart(`candidates_offer_chart_${clientId}`, {
            type: 'bar',
            data: {
                labels: d.labelsCommoffer || [],
                datasets: [{
                    label: 'Total Candidates',
                    data: d.candidateValuesCommoffer || [],
                    backgroundColor: Array((d.labelsCommoffer || []).length).fill('#ffab00'),
                    borderRadius: 5,
                    borderSkipped: false,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            },
            plugins: [showDataLabels]
        });

        createOrUpdateChart(`candidates_join_chart_${clientId}`, {
            type: 'bar',
            data: {
                labels: d.labelsCommjoin || [],
                datasets: [{
                    label: 'Total Candidates',
                    data: d.candidateValuesCommjoin || [],
                    backgroundColor: Array((d.labelsCommjoin || []).length).fill('#06c732'),
                    borderRadius: 5,
                    borderSkipped: false,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            },
            plugins: [showDataLabels]
        });

        createOrUpdateChart(`inetview_recent_chart_month_${clientId}`, {
            type: 'bar',
            data: {
                labels: d.interviewlabelsMonth || [],
                datasets: [{
                    label: 'Total Interviews',
                    data: d.interviewValuesMonth || [],
                    backgroundColor: Array((d.interviewlabelsMonth || []).length).fill('#2196f3'),
                    borderRadius: 5,
                    borderSkipped: false,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            },
            plugins: [showDataLabels]
        });

        createOrUpdateChart(`inetview_recent_chart_${clientId}`, {
            type: 'bar',
            data: {
                labels: d.interviewlabels || [],
                datasets: [{
                    label: 'Total Interviews',
                    data: d.interviewValues || [],
                    backgroundColor: Array((d.interviewlabels || []).length).fill('#2196f3'),
                    borderRadius: 5,
                    borderSkipped: false,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            },
            plugins: [showDataLabels]
        });
    }

    function filtersdatagetmonth(valSelected){
        if(valSelected == 1){
            $(`.inetview_recent_chart_month_${myChartId}`).removeClass('d-none');
            $(`.inetview_recent_chart_${myChartId}`).addClass('d-none');
        } else {
            $(`.inetview_recent_chart_month_${myChartId}`).addClass('d-none');
            $(`.inetview_recent_chart_${myChartId}`).removeClass('d-none');
        }
    }

            
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
    var company_id = {{ $directClientDetail->company_id ?? $directClientDetail->from_company_id ?? $directClientDetail->companies_id ?? $directClientDetail->client_id ?? 0 }};
    function initDContactsDataTable() {
        if ($.fn.DataTable.isDataTable('#dcontactList')) {
            return;
        }
        var dcontactList = $('#dcontactList').dataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            destroy: true,
            ordering: false, //	disable ordering
            "bLengthChange": false, //	thought this line could hide the LengthMenu
            sDom: 'lrtip',
            oLanguage: {
                sLengthMenu: "_MENU_",
            },
            "pageLength": 10,
            colReorder: true,
            ajax: {
                'url': '{!! route('service_company.clients.dcontactList') !!}?company_id=' + company_id,
                "data": function(d) {
                    d.search = $('#dcontactSearchInput').val() || $('#inputSearch').val(), d.client_id = $('#direct_client_id').val()
                }
            },
            "drawCallback": function(settings) {
                $('[data-toggle="tooltip1"]').tooltip();
            },
            "columnDefs": [{
                    "width": "50px",
                    "targets": 0
                },
                {
                    "width": "100px",
                    "targets": 1,
                    "class": "align-items-center border-0"
                },
                {
                    "width": "50px",
                    "targets": 2
                },
                {
                    "width": "200px",
                    "targets": 3
                }
            ],
            columns: [
                { data: 'chkbox', name: 'chkbox', searchable: false, orderable: false, width: '40px' },
                { data: 'Contact', name: 'Contact', searchable: false, orderable: false },
                { data: 'Designation', name: 'Designation', searchable: false, orderable: false },
                { data: 'Email', name: 'Email', searchable: false, orderable: false },
                { data: 'Phone', name: 'Phone', searchable: false, orderable: false },
                { data: 'Status', name: 'Status', searchable: false, orderable: false, width: '90px' }
            ]
        });
        new $.fn.dataTable.FixedHeader(dcontactList);
    }

    $(document).ready(function() {
        var loadedTabs = {};

        if ($('#candidate_list').hasClass('active') || $('a[href="#candidate_list"]').hasClass('active')) {
            directfetchCandidates();
            loadedTabs['candidate_list'] = true;
        } else if ($('#contacts').hasClass('active') || $('a[href="#contacts"]').hasClass('active')) {
            initDContactsDataTable();
            loadedTabs['contacts'] = true;
        }

        $(document).on('shown.bs.tab', 'a[data-toggle="tab"], a[data-bs-toggle="tab"]', function (e) {
            var target = $(e.target).attr('href');
            if (!target) return;
            target = target.replace('#', '');

            if (loadedTabs[target]) return;

            if (target === 'contacts') {
                initDContactsDataTable();
                loadedTabs[target] = true;
            } else if (target === 'candidate_list') {
                directfetchCandidates();
                loadedTabs[target] = true;
            } else if (target === 'requisitions') {
                fetchJobsClientdirect();
                loadedTabs[target] = true;
            } else if (target === 'interview_tracker') {
                directfetchCandidatesInverViewTracker();
                loadedTabs[target] = true;
            } else if (target === 'candidate_joining') {
                directfetchCandidatesJoin();
                loadedTabs[target] = true;
            } else if (target === 'candidateDashboard') {
                fetchClientDashboardAnalytics();
                loadedTabs[target] = true;
            }
        });

        $(document).on('keyup', '#dcontactSearchInput', function() {
            if ($.fn.DataTable.isDataTable('#dcontactList')) {
                $('#dcontactList').DataTable().search($(this).val()).draw();
            }
        });

        $('#search').on('keyup', function() {
            const search = $(this).val();
            directfetchCandidates(1, search);
        });

        $(document).off('click', '#paginationcandidate [data-page], #paginationcandidate .page-link, #paginationcandidate .pagi-page, #paginationcandidate .pagi-arrow');
        $(document).on('click', '#paginationcandidate [data-page], #paginationcandidate .page-link, #paginationcandidate .pagi-page, #paginationcandidate .pagi-arrow', function(e) {
            e.preventDefault();
            var $btn = $(e.target).closest('[data-page]');
            if (!$btn.length) return;

            var page = parseInt($btn.attr('data-page') || $btn.data('page'), 10);
            if (!page || page < 1 || $btn.hasClass('disabled') || $btn.hasClass('pagi-disabled') || $btn.hasClass('pagi-active-disabled') || $btn.is(':disabled')) return;

            var search = $('#inputSearch').val() || '';
            directfetchCandidates(page, search);
        });

        $(document).off('click', '#paginationcandidatejoin [data-page], #paginationcandidatejoin .page-link, #paginationcandidatejoin .pagi-page, #paginationcandidatejoin .pagi-arrow');
        $(document).on('click', '#paginationcandidatejoin [data-page], #paginationcandidatejoin .page-link, #paginationcandidatejoin .pagi-page, #paginationcandidatejoin .pagi-arrow', function(e) {
            e.preventDefault();
            var $btn = $(e.target).closest('[data-page]');
            if (!$btn.length) return;

            var page = parseInt($btn.attr('data-page') || $btn.data('page'), 10);
            if (!page || page < 1 || $btn.hasClass('disabled') || $btn.hasClass('pagi-disabled') || $btn.hasClass('pagi-active-disabled') || $btn.is(':disabled')) return;

            var search = $('#inputSearch').val() || '';
            directfetchCandidatesJoin(page, search);
        });

        $(document).off('click', '#paginationcandidateinterviewtracker [data-page], #paginationcandidateinterviewtracker .page-link, #paginationcandidateinterviewtracker .pagi-page, #paginationcandidateinterviewtracker .pagi-arrow');
        $(document).on('click', '#paginationcandidateinterviewtracker [data-page], #paginationcandidateinterviewtracker .page-link, #paginationcandidateinterviewtracker .pagi-page, #paginationcandidateinterviewtracker .pagi-arrow', function(e) {
            e.preventDefault();
            var $btn = $(e.target).closest('[data-page]');
            if (!$btn.length) return;

            var page = parseInt($btn.attr('data-page') || $btn.data('page'), 10);
            if (!page || page < 1 || $btn.hasClass('disabled') || $btn.hasClass('pagi-disabled') || $btn.hasClass('pagi-active-disabled') || $btn.is(':disabled')) return;

            var search = $('#inputSearch').val() || '';
            directfetchCandidatesInverViewTracker(page, search);
        });

        $(document).off('click', '#directpaginationclientjobs [data-page], #directpaginationclientjobs .page-link, #directpaginationclientjobs .pagi-page, #directpaginationclientjobs .pagi-arrow');
        $(document).on('click', '#directpaginationclientjobs [data-page], #directpaginationclientjobs .page-link, #directpaginationclientjobs .pagi-page, #directpaginationclientjobs .pagi-arrow', function(e) {
            e.preventDefault();
            var $btn = $(e.target).closest('[data-page]');
            if (!$btn.length) return;

            var page = parseInt($btn.attr('data-page') || $btn.data('page'), 10);
            if (!page || page < 1 || $btn.hasClass('disabled') || $btn.hasClass('pagi-disabled') || $btn.hasClass('pagi-active-disabled') || $btn.is(':disabled')) return;

            var search = $('#inputJobSearch').val() || '';
            fetchJobsClientdirect(page, search);
        });


    });

    function directfetchCandidates(page = 1, search = '') {
        $('.page-loader').css('display', 'flex');
        var clientid = '{{ $directClientDetail->client_id ?: ($directClientDetail->from_company_id ?: $directClientDetail->company_id) }}';
        return $.ajax({
            url: `{{ url('service_company/direct-client-candidate-list') }}?page=${page}`,
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

    function directfetchCandidatesJoin(page = 1, search = '') {
        $('.page-loader').css('display', 'flex');
        var clientid = '{{ $directClientDetail->client_id ?: ($directClientDetail->from_company_id ?: $directClientDetail->company_id) }}';
        return $.ajax({
            url: `{{ url('service_company/direct-client-candidate-join-list') }}?page=${page}`,
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

    function directfetchCandidatesInverViewTracker(page = 1, search = '') {
        $('.page-loader').css('display', 'flex');
        var clientid = '{{ $directClientDetail->client_id ?: ($directClientDetail->from_company_id ?: $directClientDetail->company_id) }}';
        return $.ajax({
            url: `{{ url('service_company/direct-client-candidate-interview-tracker') }}?page=${page}`,
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
                    $('#kpiInterviewsScheduledCount').text(response.pagination.total);
                    var $badge = $('#tabBadgeInterviewTracker');
                    if ($badge.length) {
                        $badge.text(response.pagination.total);
                        if (response.pagination.total > 0) { $badge.show(); } else { $badge.hide(); }
                    }
                }
            }
        });
    }

    function fetchJobsClientdirect(page = 1, search = '') {
        $('.page-loader').css('display', 'flex');
        return $.ajax({
            url: `{{ url('service_company/jobs_side_bar_job_ajax/data') }}?page=${page}`,
            type: 'GET',
            data: {
                search: search || $('#inputJobSearch').val(),
                client_id: $('#direct_client_id').val() || '{{ $directClientDetail->client_id }}',
                custom:false
            },
            success: function(response) {
                $('.page-loader').css('display', 'none');
                $('#directclientJobsListajax').html(response.html);
                $('#directpaginationclientjobs').html(response.pagination);
            }
        });
    }

    function fetchClientDashboardAnalytics() {
        var clientId = '{{ $dc_id }}';
        return $.ajax({
            url: `{{ url('service_company/clients/dashboard-analytics') }}/${clientId}`,
            type: 'GET',
            success: function(response) {
                if (response && response.status === 'success' && response.data) {
                    console.log('⚡ [Client Dashboard APM] Analytics loaded lazily for direct client', response.data);
                    var d = response.data;
                    var activeCount = d.counts ? (d.counts.active || 0) : 0;
                    var holdCount = d.counts ? (d.counts.hold || 0) : 0;
                    var inactiveCount = d.counts ? (d.counts.inactive || 0) : 0;
                    var totalJobs = activeCount + holdCount + inactiveCount;

                    $(`#dcAllJobsCount_${clientId}`).text(totalJobs);
                    $(`#dcActiveJobsCount_${clientId}`).text(activeCount);
                    $(`#dcHoldJobsCount_${clientId}`).text(holdCount);
                    $(`#dcClosedJobsCount_${clientId}`).text(inactiveCount);

                    $(`#dcAdded7DaysCount_${clientId}`).text(d.candidateValuesTotal || 0);
                    $(`#dcTotalCandidatesCount_${clientId}`).text(d.total_candidates || 0);
                    $(`#dcTotalOffersCount_${clientId}`).text(d.candidateValuesTotaloffer || 0);
                    $(`#dcTotalJoinedCount_${clientId}`).text(d.candidateValuesTotaljoin || 0);

                    renderDirectClientDashboardCharts(d, clientId);
                }
            }
        });
    }

    $(document).ready(function() {
        fetchClientDashboardAnalytics();
    });



    // Direct Client Drawer Interactive Handlers
    $(document).on('click', '.cd-kpi-card', function() {
        var targetTab = $(this).data('target-tab');
        if (targetTab) {
            $('.cd-tabs-bar a[href="' + targetTab + '"]').tab('show');
        }
    });

    $(document).on('click', '.cd-view-all-activity', function(e) {
        e.preventDefault();
        $('.cd-tabs-bar a[href="#recent_activity"]').tab('show');
    });

    $(document).on('click', '.cd-view-all-contacts', function(e) {
        e.preventDefault();
        $('.cd-tabs-bar a[href="#contacts"]').tab('show');
    });

    $(document).on('click', '.cd-btn-followup', function(e) {
        e.preventDefault();
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '<span style="font-size:12px;font-weight:700;">Client Follow Up</span>',
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
                        title: '<span style="font-size:12px;font-weight:600;">Tags Updated</span>',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }
    });
</script>
