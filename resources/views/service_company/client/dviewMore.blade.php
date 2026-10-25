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
<style>
    /* Scoped Interview Tracker Styling (Metronic Theme Aligned) */
    .cit-fb-pill {
        display: inline-flex;
        align-items: center;
        padding: 3px 8px;
        font-size: 11px;
        font-weight: 600;
        border-radius: 4px;
        line-height: 1.4;
        white-space: nowrap;
    }
    .cit-fb-scheduled { background-color: #E8FFF3; color: #50CD89; border: 1px solid #c9f7dd; }
    .cit-fb-rescheduled { background-color: #FFF8DD; color: #F1416C; border: 1px solid #ffe89e; }
    .cit-fb-attended { background-color: #F1FAFF; color: #009EF7; border: 1px solid #c4e9fd; }
    .cit-fb-noshow { background-color: #FFF5F8; color: #F1416C; border: 1px solid #fecdd6; }
    .cit-fb-cancelled { background-color: #F5F8FA; color: #7E8299; border: 1px solid #E4E6EF; }
    .cit-fb-default { background-color: #F5F8FA; color: #5E6278; border: 1px solid #E4E6EF; }

    .cit-stars { display: inline-flex; gap: 2px; }
    .cit-star-fill { color: #FFAD0F; font-size: 11px; }
    .cit-star-empty { color: #E4E6EF; font-size: 11px; }

    /* ===== Scoped Interview Tracker: Card + Sub-Card Proportional Layout ===== */
    .it-main-card {
        width: 100% !important;
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
    }

    /* 1. Top Card Header Toolbar (Heading in Card + Search Bar) */
    .it-toolbar-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 12px 18px !important;
        margin-bottom: 10px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 14px !important;
        flex-wrap: wrap !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    }
    .it-toolbar-left {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        min-width: 0 !important;
    }
    .it-title {
        font-size: 13.5px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        line-height: 1.2 !important;
        letter-spacing: -0.2px !important;
        margin: 0 !important;
    }
    .it-badge-count {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 1.5px 8px !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        color: #2563eb !important;
        background: #eff6ff !important;
        border: 1px solid #bfdbfe !important;
        border-radius: 999px !important;
    }
    .it-subtitle {
        font-size: 11px !important;
        font-weight: 500 !important;
        color: #64748b !important;
        margin-top: 2px !important;
    }
    .it-toolbar-right {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
    }
    .it-search-subcard {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        padding: 0 12px !important;
        height: 36px !important;
        min-width: 260px !important;
        max-width: 380px !important;
        width: 100% !important;
        transition: all 0.15s ease-in-out !important;
    }
    .it-search-subcard:focus-within {
        background: #ffffff !important;
        border-color: #2563eb !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12) !important;
    }
    .it-search-subcard i {
        color: #94a3b8 !important;
        font-size: 12px !important;
        flex-shrink: 0 !important;
    }
    .it-search-subcard input {
        border: none !important;
        background: transparent !important;
        outline: none !important;
        box-shadow: none !important;
        font-size: 12px !important;
        font-family: inherit !important;
        color: #0f172a !important;
        width: 100% !important;
        padding: 0 !important;
        line-height: 36px !important;
    }
    .it-search-subcard input::placeholder {
        color: #94a3b8 !important;
        font-size: 11.5px !important;
    }
    .it-search-clear {
        background: transparent !important;
        border: none !important;
        color: #94a3b8 !important;
        cursor: pointer !important;
        padding: 2px 4px !important;
        font-size: 11px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    .it-search-clear:hover {
        color: #ef4444 !important;
    }

    .it-card-container {
        width: 100% !important;
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
    }

    /* Proportional Header Strip */
    .it-card-header,
    .it-primary-card {
        display: grid !important;
        grid-template-columns: minmax(0, 2.2fr) minmax(0, 2.3fr) minmax(0, 1.75fr) minmax(0, 1.1fr) minmax(0, 1.1fr) minmax(0, 0.75fr) minmax(0, 0.8fr) !important;
        column-gap: 12px !important;
        align-items: center !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }

    .it-card-header {
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        padding: 9px 14px !important;
        margin-bottom: 8px !important;
    }

    .it-col-th {
        font-size: 10px !important;
        font-weight: 700 !important;
        color: #64748b !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        white-space: nowrap !important;
    }

    /* Primary Job Card for Each Interview */
    .it-primary-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 12px 14px !important;
        margin-bottom: 8px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
        transition: all 0.15s ease-in-out !important;
    }

    .it-primary-card:hover {
        border-color: #cbd5e1 !important;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06) !important;
        transform: translateY(-1px) !important;
    }

    /* Logical Sub-Cards Inside the Primary Card */
    .it-subcard {
        min-width: 0 !important;
        display: flex !important;
        flex-direction: column !important;
    }

    /* 1. Candidate Sub-Card */
    .it-subcard-cand {
        flex-direction: row !important;
        align-items: center !important;
        gap: 10px !important;
    }
    .it-cand-avatar {
        width: 36px !important;
        height: 36px !important;
        border-radius: 50% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        color: #ffffff !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        flex-shrink: 0 !important;
        text-transform: uppercase !important;
        box-shadow: 0 1px 2px rgba(0,0,0,0.08) !important;
    }
    .it-avatar-green { background: #10b981 !important; }
    .it-avatar-red { background: #ef4444 !important; }
    .it-avatar-blue { background: #3b82f6 !important; }
    .it-avatar-purple { background: #8b5cf6 !important; }
    .it-avatar-orange { background: #f97316 !important; }
    .it-avatar-gray { background: #6b7280 !important; }

    .it-cand-info {
        min-width: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 1px !important;
    }
    .it-cand-name {
        font-size: 12.5px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        line-height: 1.35 !important;
        word-break: break-word !important;
    }
    .it-cand-role {
        font-size: 11px !important;
        font-weight: 500 !important;
        color: #64748b !important;
        line-height: 1.3 !important;
        word-break: break-word !important;
    }
    .it-cand-meta {
        display: flex !important;
        align-items: center !important;
        gap: 5px !important;
        margin-top: 3px !important;
        flex-wrap: wrap !important;
    }
    .it-meta-tag {
        display: inline-flex !important;
        align-items: center !important;
        gap: 3px !important;
        font-size: 9.5px !important;
        font-weight: 600 !important;
        padding: 1.5px 6px !important;
        border-radius: 4px !important;
    }
    .it-meta-tag.confirmed { color: #10b981 !important; background: #f0fdf4 !important; border: 1px solid #bbf7d0 !important; }
    .it-meta-tag.warning { color: #f59e0b !important; background: #fffbeb !important; border: 1px solid #fde68a !important; }
    .it-meta-tag.danger { color: #ef4444 !important; background: #fef2f2 !important; border: 1px solid #fecaca !important; }
    .it-meta-tag.info { color: #3b82f6 !important; background: #eff6ff !important; border: 1px solid #bfdbfe !important; }
    .it-meta-date {
        font-size: 10px !important;
        color: #94a3b8 !important;
    }

    /* 2. Job Requisition Sub-Card */
    .it-subcard-job {
        gap: 2px !important;
    }
    .it-job-title {
        font-size: 12.5px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        line-height: 1.35 !important;
        word-break: break-word !important;
    }
    .it-job-company {
        font-size: 11px !important;
        font-weight: 500 !important;
        color: #64748b !important;
        line-height: 1.3 !important;
        word-break: break-word !important;
    }
    .it-job-tags {
        display: flex !important;
        align-items: center !important;
        gap: 5px !important;
        margin-top: 2px !important;
        flex-wrap: wrap !important;
    }
    .it-job-type {
        display: inline-flex !important;
        align-items: center !important;
        padding: 1.5px 6px !important;
        border-radius: 4px !important;
        font-size: 9.5px !important;
        font-weight: 600 !important;
    }
    .it-job-type-internal { background: #eef2ff !important; color: #4f46e5 !important; border: 1px solid #e0e7ff !important; }
    .it-job-type-direct { background: #f0fdf4 !important; color: #16a34a !important; border: 1px solid #dcfce7 !important; }
    .it-job-id {
        font-size: 10.5px !important;
        color: #2563eb !important;
        font-weight: 600 !important;
    }
    .it-dot-sep {
        width: 3px !important;
        height: 3px !important;
        border-radius: 50% !important;
        background: #cbd5e1 !important;
    }

    /* 3. Date & Time Sub-Card */
    .it-subcard-datetime {
        gap: 3px !important;
    }
    .it-dt-row {
        display: flex !important;
        align-items: center !important;
        gap: 5px !important;
        font-size: 11.5px !important;
        color: #1e293b !important;
        line-height: 1.3 !important;
    }
    .it-dt-row svg {
        color: #94a3b8 !important;
        flex-shrink: 0 !important;
    }
    .it-dt-date {
        font-weight: 600 !important;
        color: #0f172a !important;
    }
    .it-dt-time {
        font-size: 11px !important;
        font-weight: 500 !important;
        color: #475569 !important;
    }
    .it-dt-mode {
        font-size: 10.5px !important;
        font-weight: 500 !important;
        color: #64748b !important;
    }
    .it-dt-link {
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
        color: #2563eb !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        text-decoration: none !important;
        margin-top: 2px !important;
        cursor: pointer !important;
    }
    .it-dt-link:hover {
        color: #1d4ed8 !important;
        text-decoration: underline !important;
    }
    .it-dt-link svg {
        color: #2563eb !important;
    }

    /* 4. Interviewers Sub-Card */
    .it-subcard-interviewers {
        gap: 2px !important;
    }
    .it-lead-name {
        font-size: 12px !important;
        font-weight: 600 !important;
        color: #0f172a !important;
        line-height: 1.35 !important;
        word-break: break-word !important;
    }
    .it-lead-comp {
        font-size: 10.5px !important;
        font-weight: 600 !important;
        color: #009ef7 !important;
        line-height: 1.3 !important;
        word-break: break-word !important;
    }
    .it-extra-wrap {
        display: flex !important;
        align-items: center !important;
        gap: 4px !important;
        margin-top: 2px !important;
    }
    .it-extra-pill {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 17px !important;
        height: 15px !important;
        padding: 0 4px !important;
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 4px !important;
        font-size: 9px !important;
        font-weight: 600 !important;
        color: #64748b !important;
    }
    .it-extra-lbl {
        font-size: 10px !important;
        color: #64748b !important;
    }

    /* 5. Source Sub-Card */
    .it-subcard-source {
        gap: 2px !important;
    }
    .it-src-name {
        font-size: 12px !important;
        font-weight: 600 !important;
        color: #0f172a !important;
        line-height: 1.35 !important;
        word-break: break-word !important;
    }
    .it-src-comp {
        font-size: 10.5px !important;
        font-weight: 500 !important;
        color: #64748b !important;
        line-height: 1.3 !important;
        word-break: break-word !important;
    }

    /* 6. Stage Sub-Card */
    .it-subcard-stage {
        gap: 3px !important;
        align-items: flex-start !important;
    }
    .it-badge { display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 999px; font-size: 10px; font-weight: 600; width: fit-content; }
    .it-badge-blue   { background: #dbeafe; color: #1e40af; }
    .it-badge-purple { background: #f3e8ff; color: #6b21a8; }
    .it-badge-orange { background: #ffedd5; color: #9a3412; }
    .it-badge-red    { background: #fee2e2; color: #991b1b; }
    .it-badge-green  { background: #d1fae5; color: #065f46; }
    .it-badge-yellow { background: #fef9c3; color: #854d0e; }
    .it-badge-gray   { background: #e2e8f0; color: #374151; }
    .it-badge-pink   { background: #fce7f3; color: #9d174d; }
    .it-stage-sub {
        font-size: 10px !important;
        font-weight: 500 !important;
        color: #94a3b8 !important;
        line-height: 1.2 !important;
    }

    /* 7. Feedback Sub-Card */
    .it-subcard-feedback {
        gap: 3px !important;
        align-items: flex-start !important;
    }
    .it-fb-status {
        font-size: 11px !important;
        font-weight: 600 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
    }
    .it-rec-pill {
        display: inline-flex !important;
        align-items: center !important;
        padding: 1.5px 7px !important;
        border-radius: 50px !important;
        font-size: 9.5px !important;
        font-weight: 600 !important;
        margin-top: 1px !important;
    }

    /* Empty, Loading, and Error State Cards */
    .it-empty-card,
    .it-loading-card,
    .it-error-card {
        background: #ffffff !important;
        border: 1px dashed #cbd5e1 !important;
        border-radius: 10px !important;
        padding: 36px 20px !important;
        text-align: center !important;
        color: #64748b !important;
        font-size: 12.5px !important;
        margin: 8px 0 !important;
    }
    .it-error-card {
        border-color: #fca5a5 !important;
        color: #dc2626 !important;
    }

    /* 4. Enterprise Pagination Card / Footer (Minimum 10 rows) */
    .it-pagination-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 10px 16px !important;
        margin-top: 10px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px !important;
        flex-wrap: wrap !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03) !important;
    }
    .it-pagi-info {
        font-size: 11.5px !important;
        font-weight: 500 !important;
        color: #64748b !important;
    }
    .it-pagi-info strong {
        color: #0f172a !important;
        font-weight: 700 !important;
    }
    .it-pagi-controls {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }
    .it-pagi-btn {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
        height: 32px !important;
        padding: 0 12px !important;
        border-radius: 6px !important;
        font-size: 11.5px !important;
        font-weight: 600 !important;
        border: 1px solid #e2e8f0 !important;
        background: #ffffff !important;
        color: #334155 !important;
        cursor: pointer !important;
        transition: all 0.15s ease-in-out !important;
    }
    .it-pagi-btn:hover:not(.disabled) {
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }
    .it-pagi-btn.disabled {
        opacity: 0.45 !important;
        cursor: not-allowed !important;
        background: #f8fafc !important;
        border-color: #e2e8f0 !important;
        color: #94a3b8 !important;
    }
    .it-pagi-page-indicator {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 10px !important;
        height: 32px !important;
        font-size: 11.5px !important;
        font-weight: 600 !important;
        color: #475569 !important;
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 6px !important;
    }
    .it-pagi-size-select {
        height: 32px !important;
        padding: 0 8px 0 10px !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 6px !important;
        background: #ffffff !important;
        font-size: 11.5px !important;
        font-weight: 500 !important;
        color: #334155 !important;
        outline: none !important;
        cursor: pointer !important;
    }

    @media (max-width: 1280px) {
        .it-card-header,
        .it-primary-card {
            grid-template-columns: minmax(0, 2.1fr) minmax(0, 2.2fr) minmax(0, 1.7fr) minmax(0, 1fr) minmax(0, 1fr) minmax(0, 0.7fr) minmax(0, 0.75fr) !important;
            column-gap: 8px !important;
            padding-left: 10px !important;
            padding-right: 10px !important;
        }
    }

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
            height: 30px !important;
            padding: 0 13px !important;
            border-radius: 6px !important;
            font-size: 12.5px !important;
            font-weight: 500 !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
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
            width: 30px !important;
            height: 30px !important;
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
            font-size: 14px !important;
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
            font-size: 8.5px !important;
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
            font-size: 14px !important;
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
            font-size: 10.5px !important;
            font-weight: 500 !important;
            padding: 2.5px 8px;
            border-radius: 4px;
            line-height: 1.2;
          }
          .cd-meta-row {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 11.5px !important;
            font-weight: 400 !important;
            color: #64748b;
            margin-bottom: 6px;
            flex-wrap: wrap;
          }
          .cd-meta-sep {
            color: #cbd5e1;
            font-size: 10px;
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
            padding: 2.5px 9px;
            background: #dcfce7;
            border: 1px solid #bbf7d0;
            color: #15803d;
            font-size: 10.5px !important;
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
            padding: 6px 12px;
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
            font-size: 12px !important;
            font-weight: 600 !important;
            line-height: 1.2;
          }
          .cd-status-card-sub {
            font-size: 11px !important;
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
            font-size: 11px !important;
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
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13.5px !important;
            flex-shrink: 0;
          }
          .cd-kpi-icon-blue   { background: #eff6ff; color: #2563eb; }
          .cd-kpi-icon-purple { background: #f5f3ff; color: #7c3aed; }
          .cd-kpi-icon-amber  { background: #fffbeb; color: #d97706; }
          .cd-kpi-icon-orange { background: #fff7ed; color: #ea580c; }
          .cd-kpi-icon-green  { background: #f0fdf4; color: #16a34a; }
          .cd-kpi-num {
            font-size: 14px !important;
            font-weight: 700 !important;
            color: #0f172a;
            line-height: 1.1;
          }
          .cd-kpi-lbl {
            font-size: 12px !important;
            font-weight: 500 !important;
            color: #1e293b;
            line-height: 1.2;
            margin-top: 1px;
          }
          .cd-kpi-desc {
            font-size: 11px !important;
            font-weight: 400 !important;
            color: #94a3b8;
            line-height: 1.1;
            margin-top: 1px;
          }
          .cd-kpi-arrow {
            font-size: 11px !important;
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
            padding: 10px 15px !important;
            border: none !important;
            border-bottom: 2px solid transparent !important;
            border-radius: 0 !important;
            font-size: 12.5px !important;
            font-weight: 500 !important;
            color: #64748b !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 7px !important;
            background: transparent !important;
            transition: all 0.15s ease-in-out !important;
            cursor: pointer !important;
          }
          .cd-tabs-bar .nav-link i {
            font-size: 12.5px !important;
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
            min-width: 18px;
            height: 16px;
            padding: 0 5px;
            background: #10b981;
            color: #ffffff;
            font-size: 10px !important;
            font-weight: 600;
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
            font-size: 13.5px !important;
            font-weight: 600 !important;
            color: #0f172a !important;
          }
          .cd-card-hdr-link {
            font-size: 12px !important;
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
            font-size: 12px !important;
            font-weight: 400 !important;
            line-height: 1.65 !important;
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
            font-size: 11.5px !important;
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
            font-size: 12.5px !important;
            font-weight: 600 !important;
            color: #0f172a;
            line-height: 1.25;
          }
          .cd-tl-sub {
            font-size: 11px !important;
            font-weight: 400 !important;
            color: #64748b;
            line-height: 1.25;
            margin-top: 2px;
          }
          .cd-tl-role {
            font-size: 12px !important;
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
            font-size: 11px !important;
            font-weight: 400 !important;
            color: #94a3b8;
            white-space: nowrap;
          }
          .cd-tl-badge {
            font-size: 10.5px !important;
            font-weight: 500 !important;
            padding: 2.5px 8px;
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
            padding: 7px 0;
            border-bottom: 1px dashed #f1f5f9;
            gap: 12px;
          }
          .cd-kv-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
          }
          .cd-kv-lbl {
            font-size: 11.5px !important;
            color: #64748b;
            font-weight: 400 !important;
            flex-shrink: 0;
          }
          .cd-kv-val {
            font-size: 12px !important;
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
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #dcfce7;
            color: #15803d;
            font-size: 13px !important;
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
            font-size: 13px !important;
            font-weight: 600 !important;
            color: #0f172a;
          }
          .cd-contact-primary-tag {
            font-size: 10.5px !important;
            font-weight: 500 !important;
            background: #dcfce7;
            color: #15803d;
            padding: 1.5px 7px;
            border-radius: 10px;
          }
          .cd-contact-title {
            font-size: 11px !important;
            font-weight: 400 !important;
            color: #64748b;
            margin-top: 2px;
          }
          .cd-contact-links {
            display: flex;
            flex-direction: column;
            gap: 3px;
            margin-top: 5px;
            font-size: 11.5px !important;
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

          /* ── Client Services Card ── */
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
            font-size: 10.5px !important;
            font-weight: 500 !important;
            padding: 2.5px 8px;
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

          /* TIER 1: Searchbar & Filter Toolbar Card */
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
            flex-wrap: wrap;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03);
          }
          .cd-contact-toolbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1 1 300px;
          }
          .cd-contact-title-avatar {
            width: 36px;
            height: 36px;
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
          }
          .cd-contact-title {
            font-size: 14.5px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            line-height: 1.2;
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
          }
          .cd-contact-scope-pill {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            border-radius: 6px;
            padding: 1px 7px;
            font-size: 10.5px;
            font-weight: 600;
          }
          .cd-contact-toolbar-right {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
          }

          /* Search Sub-Card */
          .cd-contact-search-subcard {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0 10px 0 12px;
            height: 36px;
            width: 100%;
            max-width: 360px;
            transition: all 0.15s ease-in-out;
            position: relative;
          }
          .cd-contact-search-subcard:focus-within {
            background: #ffffff;
            border-color: #009ef7;
            box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12);
          }
          .cd-contact-search-subcard i.fa-search {
            color: #94a3b8;
            font-size: 12px;
            flex-shrink: 0;
          }
          .cd-contact-search-subcard input {
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
          .cd-contact-search-subcard input::placeholder {
            color: #94a3b8;
            font-weight: 400;
          }
          .cd-contact-search-clear {
            border: none;
            background: transparent;
            color: #94a3b8;
            cursor: pointer;
            padding: 2px 4px;
            font-size: 11px;
            transition: color 0.15s;
          }
          .cd-contact-search-clear:hover {
            color: #ef4444;
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

          /* Discrete Sub-Cards Grid */
          .cd-contact-card-grid {
            display: grid !important;
            grid-template-columns: minmax(0, 2.2fr) minmax(0, 2.2fr) minmax(0, 1.4fr) minmax(0, 1.2fr) minmax(0, 1.4fr) !important;
            gap: 8px !important;
            padding: 10px 12px !important;
            width: 100% !important;
            max-width: 100% !important;
            align-items: stretch !important;
          }

          /* Discrete Sub-Card Container */
          .cd-contact-subcard {
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
          .cd-contact-subcard-hdr {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            margin-bottom: 5px !important;
            gap: 4px !important;
            line-height: 1 !important;
          }
          .cd-contact-subcard-label {
            font-size: 9.5px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.3px !important;
            color: #64748b !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
          }
          .cd-contact-subcard-body {
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            gap: 3px !important;
            flex: 1 1 auto !important;
            min-width: 0 !important;
            overflow: hidden !important;
          }

          /* Sub-Card 1: Profile Elements */
          .cd-contact-profile-row {
            display: flex !important;
            align-items: center !important;
            gap: 9px !important;
            min-width: 0 !important;
          }
          .cd-contact-avatar-box {
            width: 38px !important;
            height: 38px !important;
            min-width: 38px !important;
            border-radius: 8px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 13.5px !important;
            font-weight: 700 !important;
            color: #ffffff !important;
            flex-shrink: 0 !important;
            text-transform: uppercase !important;
          }
          .cd-contact-profile-meta {
            flex: 1 1 auto !important;
            min-width: 0 !important;
            overflow: hidden !important;
          }
          .cd-contact-card-name {
            font-size: 13px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            line-height: 1.25 !important;
            display: block !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
          }
          .cd-contact-card-desig {
            font-size: 11px !important;
            font-weight: 500 !important;
            color: #475569 !important;
            line-height: 1.2 !important;
            margin-top: 2px !important;
            display: block !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
          }
          .cd-badge-primary-spoc {
            display: inline-flex !important;
            align-items: center !important;
            padding: 1px 6px !important;
            border-radius: 4px !important;
            font-size: 9.5px !important;
            font-weight: 700 !important;
            background: #fef3c7 !important;
            color: #92400e !important;
            border: 1px solid #fde68a !important;
          }
          .cd-badge-secondary-contact {
            display: inline-flex !important;
            align-items: center !important;
            padding: 1px 5px !important;
            border-radius: 4px !important;
            font-size: 9.5px !important;
            font-weight: 600 !important;
            background: #f1f5f9 !important;
            color: #475569 !important;
            border: 1px solid #e2e8f0 !important;
          }

          /* Sub-Card 2: Communication Elements */
          .cd-comm-item {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            font-size: 11.5px !important;
            color: #334155 !important;
            min-width: 0 !important;
            line-height: 1.3 !important;
          }
          .cd-comm-item i {
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
            padding: 1px 3px !important;
            font-size: 10.5px !important;
            border-radius: 3px !important;
            transition: all 0.12s ease !important;
            flex-shrink: 0 !important;
          }
          .cd-copy-btn:hover {
            color: #009ef7 !important;
            background: #eff6ff !important;
          }

          /* Sub-Card 3: Org & Scope */
          .cd-org-client {
            font-size: 11.5px !important;
            font-weight: 600 !important;
            color: #1e293b !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
            display: flex !important;
            align-items: center !important;
            gap: 5px !important;
          }
          .cd-org-role-badge {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            padding: 1px 6px !important;
            border-radius: 4px !important;
            font-size: 10px !important;
            font-weight: 600 !important;
            background: #eff6ff !important;
            color: #2563eb !important;
            border: 1px solid #dbeafe !important;
            max-width: 100% !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
          }

          /* Sub-Card 4: Linked Jobs */
          .cd-linked-jobs-stat {
            font-size: 11.5px !important;
            font-weight: 600 !important;
            color: #0f172a !important;
            display: flex !important;
            align-items: center !important;
            gap: 5px !important;
          }
          .cd-view-jobs-link {
            font-size: 10.5px !important;
            color: #009ef7 !important;
            font-weight: 600 !important;
            text-decoration: none !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 3px !important;
            margin-top: 2px !important;
            cursor: pointer !important;
          }
          .cd-view-jobs-link:hover {
            text-decoration: underline !important;
            color: #007bb5 !important;
          }

          /* Sub-Card 5: Actions */
          .cd-contact-actions-row {
            display: flex !important;
            align-items: center !important;
            gap: 5px !important;
            flex-wrap: nowrap !important;
          }
          .cd-contact-btn-call {
            padding: 4px 8px !important;
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
          }
          .cd-contact-btn-call:hover {
            background: #059669 !important;
            color: #ffffff !important;
          }
          .cd-contact-btn-mail {
            padding: 4px 8px !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            border-radius: 6px !important;
            background: #eff6ff !important;
            color: #2563eb !important;
            border: 1px solid #bfdbfe !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            text-decoration: none !important;
            transition: all 0.12s ease !important;
          }
          .cd-contact-btn-mail:hover {
            background: #2563eb !important;
            color: #ffffff !important;
          }
          .cd-contact-btn-wa {
            padding: 4px 7px !important;
            font-size: 11px !important;
            border-radius: 6px !important;
            background: #f0fdf4 !important;
            color: #16a34a !important;
            border: 1px solid #bbf7d0 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            text-decoration: none !important;
            transition: all 0.12s ease !important;
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
            padding: 1px 6px !important;
            border-radius: 4px !important;
            font-size: 9.5px !important;
            font-weight: 700 !important;
            background: #dcfce7 !important;
            color: #15803d !important;
            border: 1px solid #bbf7d0 !important;
          }
          .cd-contact-status-active i {
            font-size: 6px !important;
          }
          .cd-contact-status-inactive {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            padding: 1px 6px !important;
            border-radius: 4px !important;
            font-size: 9.5px !important;
            font-weight: 700 !important;
            background: #f1f5f9 !important;
            color: #64748b !important;
            border: 1px solid #e2e8f0 !important;
          }

          /* Sub-Card 6: Bottom Activity Strip */
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
            gap: 12px !important;
            font-size: 11px !important;
            color: #64748b !important;
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

          @media (max-width: 1200px) {
            .cd-contact-card-grid {
              grid-template-columns: minmax(0, 1.8fr) minmax(0, 1.8fr) minmax(0, 1.4fr) !important;
            }
          }
          @media (max-width: 768px) {
            .cd-contact-card-grid {
              grid-template-columns: 1fr !important;
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
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
          }

          /* Search Sub-Card */
          .cd-cand-search-subcard {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0 10px 0 12px;
            height: 36px;
            width: 100%;
            max-width: 380px;
            transition: all 0.15s ease-in-out;
            position: relative;
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
          .cd-cand-subtab {
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
          .cd-cand-subtab:hover {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #cbd5e1;
          }
          .cd-cand-subtab.active {
            background: #009ef7 !important;
            color: #ffffff !important;
            border-color: #009ef7 !important;
            font-weight: 600 !important;
            box-shadow: 0 2px 6px rgba(0, 158, 247, 0.28) !important;
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
            background: #e2e8f0;
            color: #475569;
          }
          .cd-cand-subtab.active .cd-cand-subtab-badge {
            background: rgba(255, 255, 255, 0.28) !important;
            color: #ffffff !important;
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

          /* Sub-Card 1: Profile */
          .cd-cand-profile-row {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            min-width: 0 !important;
          }
          .cd-cand-avatar-box {
            width: 36px !important;
            height: 36px !important;
            min-width: 36px !important;
            border-radius: 8px !important;
            background: #eff6ff !important;
            color: #2563eb !important;
            border: 1px solid #dbeafe !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 13px !important;
            font-weight: 700 !important;
            flex-shrink: 0 !important;
          }
          .cd-cand-profile-meta {
            flex: 1 1 auto !important;
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
          .cd-job-logo-box {
            width: 26px !important;
            height: 26px !important;
            min-width: 26px !important;
            border-radius: 6px !important;
            background: #eff6ff !important;
            color: #2563eb !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border: 1px solid #dbeafe !important;
            flex-shrink: 0 !important;
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
              grid-template-columns: minmax(0, 1.8fr) minmax(0, 1.3fr) minmax(0, 1.2fr) minmax(0, 2.2fr) minmax(0, 1.45fr) minmax(0, 1.2fr) !important;
              gap: 6px !important;
              padding: 8px 10px !important;
            }
            .cd-cand-subcard {
              padding: 6px 8px !important;
            }
          }
          @media (max-width: 1100px) {
            .cd-cand-card-grid {
              grid-template-columns: minmax(0, 1.7fr) minmax(0, 1.2fr) minmax(0, 1.1fr) minmax(0, 2fr) minmax(0, 1.4fr) minmax(0, 1.1fr) !important;
              gap: 5px !important;
              padding: 6px 8px !important;
            }
            .cd-cand-bottom-strip {
              flex-direction: column !important;
              align-items: flex-start !important;
              gap: 6px !important;
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
            $clientCreatedAt = (!empty(@$directClientDetail->created_at)) ? \Carbon\Carbon::parse(@$directClientDetail->created_at)->format('d M, Y') : ((!empty(@$directClientDetail->to_created_at)) ? \Carbon\Carbon::parse(@$directClientDetail->to_created_at)->format('d M, Y') : date('d M, Y'));
            $clientUpdatedAt = @$directClientDetail->updated_at ? Carbon::parse($directClientDetail->updated_at)->diffForHumans() : '1 day ago';
            $isClientActive = ($kpiActive > 0);
            $userPermissions = (isset($userPermissions) && is_array($userPermissions)) ? $userPermissions : [];
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
                </div>
              </div>
              <i class="fas fa-chevron-right fa-solid cd-kpi-arrow"></i>
            </div>
            <div class="cd-kpi-card" data-target-tab="#candidate_list">
              <div class="d-flex align-items-center gap-3">
                <div class="cd-kpi-icon cd-kpi-icon-purple"><i class="fas fa-users fa-solid" style="color:#7c3aed;"></i></div>
                <div>
                  <div class="cd-kpi-num">{{ $kpiSubmitted }}</div>
                  <div class="cd-kpi-lbl">Candidates</div>
                </div>
              </div>
              <i class="fas fa-chevron-right fa-solid cd-kpi-arrow"></i>
            </div>
            <div class="cd-kpi-card" data-target-tab="#interview_tracker">
              <div class="d-flex align-items-center gap-3">
                <div class="cd-kpi-icon cd-kpi-icon-amber"><i class="fas fa-calendar-days fa-solid" style="color:#d97706;"></i></div>
                <div>
                  <div class="cd-kpi-num" id="kpiInterviewsScheduledCount">{{ $kpiInterviews }}</div>
                  <div class="cd-kpi-lbl">Interviews</div>
                </div>
              </div>
              <i class="fas fa-chevron-right fa-solid cd-kpi-arrow"></i>
            </div>
            <div class="cd-kpi-card" data-target-tab="#candidate_joining" data-segment="offers">
              <div class="d-flex align-items-center gap-3">
                <div class="cd-kpi-icon cd-kpi-icon-orange"><i class="fas fa-file-lines fa-solid" style="color:#ea580c;"></i></div>
                <div>
                  <div class="cd-kpi-num" id="kpiOffersCountDirect">{{ $kpiOffers }}</div>
                  <div class="cd-kpi-lbl">Offers</div>
                </div>
              </div>
              <i class="fas fa-chevron-right fa-solid cd-kpi-arrow"></i>
            </div>
            <div class="cd-kpi-card" data-target-tab="#candidate_joining" data-segment="joined">
              <div class="d-flex align-items-center gap-3">
                <div class="cd-kpi-icon cd-kpi-icon-green"><i class="fas fa-user-check fa-solid" style="color:#059669;"></i></div>
                <div>
                  <div class="cd-kpi-num" id="kpiJoinsCountDirect">{{ $kpiJoins }}</div>
                  <div class="cd-kpi-lbl">Joined</div>
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
                  <span class="cd-tab-badge" id="cdContactNavBadgeDirect" style="{{ (!empty($client_contact_count) && $client_contact_count > 0) ? '' : 'display:none;' }}">{{ $client_contact_count ?? 0 }}</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" data-toggle="tab" href="#requisitions">
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
                  @php $totalOffersJoinsDirect = ($kpiOffers ?? 0) + ($kpiJoins ?? 0); @endphp
                  <span class="cd-tab-badge" id="ojDirectTabBadge" @if($totalOffersJoinsDirect <= 0) style="display:none;" @endif>{{ $totalOffersJoinsDirect }}</span>
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
                      <h4 class="text-white mb-0" style="font-size:13.5px; font-weight:600;">Recent Activity &amp; Audit Logs</h4>
                    </div>
                    <span class="badge bg-secondary text-white" style="font-size:10.5px;">Pending Discussion</span>
                  </div>
                  <p class="text-gray-400 mb-0" style="font-size:12px; line-height:1.6;">
                    This section captures comprehensive timeline actions: candidate submissions, interview logs, offer letter events, job requisitions, and administrative audit trails for direct client interactions. Additional widgets and configurations will be integrated as discussed.
                  </p>
                </div>
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
                                        <div class="d-flex align-items-center gap-2">
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
                                        <span class="cd-contact-subtab-badge" id="contactTabCountAllDirect">0</span>
                                    </button>
                                    <button type="button" class="cd-contact-subtab" data-filter="primary" id="cdContactTabPrimaryDirect">
                                        <span><i class="fas fa-star text-amber-500 me-1" style="font-size:10px;"></i> Primary SPOCs</span>
                                        <span class="cd-contact-subtab-badge" id="contactTabCountPrimaryDirect">0</span>
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
                                        <span class="cd-contact-subtab-badge" id="contactTabCountActiveDirect">0</span>
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

                            <!-- TIER 2: SUB-TABS PIPELINE CARD -->
                            <div class="cd-cand-tabs-card">
                                <div class="cd-cand-tabs-list">
                                    <button type="button" class="cd-cand-subtab-direct active" data-status="">
                                        <span>All Candidates</span>
                                        <span class="cd-cand-subtab-badge" id="candTabCountAllDirect">0</span>
                                    </button>
                                    <button type="button" class="cd-cand-subtab-direct" data-status="2">
                                        <span>Screening</span>
                                        <span class="cd-cand-subtab-badge" id="candTabCountScreeningDirect"></span>
                                    </button>
                                    <button type="button" class="cd-cand-subtab-direct" data-status="3">
                                        <span>Interview</span>
                                        <span class="cd-cand-subtab-badge" id="candTabCountInterviewDirect"></span>
                                    </button>
                                    <button type="button" class="cd-cand-subtab-direct" data-status="4">
                                        <span>Shortlisted</span>
                                        <span class="cd-cand-subtab-badge" id="candTabCountShortlistedDirect"></span>
                                    </button>
                                    <button type="button" class="cd-cand-subtab-direct" data-status="5">
                                        <span>Offer</span>
                                        <span class="cd-cand-subtab-badge" id="candTabCountOfferDirect"></span>
                                    </button>
                                    <button type="button" class="cd-cand-subtab-direct" data-status="6">
                                        <span>Joined</span>
                                        <span class="cd-cand-subtab-badge" id="candTabCountJoinedDirect"></span>
                                    </button>
                                    <button type="button" class="cd-cand-subtab-direct" data-status="7">
                                        <span>On Hold</span>
                                        <span class="cd-cand-subtab-badge" id="candTabCountOnHoldDirect"></span>
                                    </button>
                                    <button type="button" class="cd-cand-subtab-direct" data-status="8">
                                        <span>Rejected</span>
                                        <span class="cd-cand-subtab-badge" id="candTabCountRejectedDirect"></span>
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
                        <div class="it-main-card">
                            <!-- 1. Top Card Header Toolbar (Heading in Card + Search Bar) -->
                            <div class="it-toolbar-card">
                                <div class="it-toolbar-left">
                                    <div class="it-title-wrap">
                                        <div class="d-flex align-items-center gap-2">
                                            <h4 class="it-title">Interview Tracker</h4>
                                            <span class="it-badge-count" id="itTotalCountBadgeDirect">0</span>
                                        </div>
                                        <div class="it-subtitle">Interviews scheduled with this client's candidates.</div>
                                    </div>
                                </div>
                                <div class="it-toolbar-right">
                                    <div class="it-search-subcard">
                                        <i class="fas fa-search"></i>
                                        <input type="text" id="itSearchInputDirect" placeholder="Search candidate, job, interviewer, stage..." autocomplete="off">
                                        <button type="button" class="it-search-clear" id="itSearchClearDirect" style="display: none;" title="Clear search">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. Proportional Column Header Strip & Cards Container -->
                            <div class="it-card-container">
                                <div class="it-card-header">
                                    <div class="it-col-th it-th-cand">Candidate</div>
                                    <div class="it-col-th it-th-job">Job Requisitions</div>
                                    <div class="it-col-th it-th-datetime">Interview Date &amp; Time</div>
                                    <div class="it-col-th it-th-interviewers">Interviewers</div>
                                    <div class="it-col-th it-th-source">Source</div>
                                    <div class="it-col-th it-th-stage">Stage</div>
                                    <div class="it-col-th it-th-feedback">Feedback</div>
                                </div>

                                <!-- Primary Cards List -->
                                <div id="candidatetListinterviewtracker" class="it-cards-list" data-role="interview-list">
                                </div>
                            </div>

                            <!-- 3. Enterprise Pagination Card (Minimum 10 rows) -->
                            <div id="paginationcandidateinterviewtracker" class="it-pagination-card" data-role="interview-pagination">
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
                                            <!-- 2. Sub-Tabs in Card-Based Design with White Background -->
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

                                            <!-- Restored Card-Based Jobs Stream -->
                                            <div id="directclientJobsListajax" class="cd-jobs-stream"></div>
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
        allContacts: [],
        filteredContacts: [],
        activeFilter: 'all',
        searchTerm: '',
        searchDebounceTimer: null,
        isInitialized: false,
        isLoading: false,

        avatarPalette: ['#059669', '#2563eb', '#7c3aed', '#d97706', '#db2777', '#0284c7', '#4f46e5', '#0d9488', '#ea580c'],

        init: function(companyId) {
            if (companyId) this.companyId = companyId;
            if (!this.isInitialized) {
                this.bindEvents();
                this.isInitialized = true;
            }
            this.fetch();
        },

        bindEvents: function() {
            var self = this;

            // 1. Debounced live search
            $(document).off('input', '#cdContactSearchInputDirect');
            $(document).on('input', '#cdContactSearchInputDirect', function() {
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
            $(document).off('click', '#cdContactSearchClearDirect');
            $(document).on('click', '#cdContactSearchClearDirect', function() {
                $('#cdContactSearchInputDirect').val('');
                $(this).hide();
                self.searchTerm = '';
                self.filterAndRender();
            });

            // 3. Reset filters & refresh button
            $(document).off('click', '#cdContactResetBtnDirect, #cdContactClearFilterBtnDirect');
            $(document).on('click', '#cdContactResetBtnDirect, #cdContactClearFilterBtnDirect', function() {
                $('#cdContactSearchInputDirect').val('');
                $('#cdContactSearchClearDirect').hide();
                self.searchTerm = '';
                self.activeFilter = 'all';
                $('.cd-contact-subtab').removeClass('active');
                $('#cdContactTabAllDirect').addClass('active');
                self.fetch();
            });

            // 4. Sub-tab filter buttons
            $(document).off('click', '.cd-contact-tabs-card .cd-contact-subtab');
            $(document).on('click', '.cd-contact-tabs-card .cd-contact-subtab', function() {
                var filter = $(this).data('filter') || 'all';
                $('.cd-contact-tabs-card .cd-contact-subtab').removeClass('active');
                $(this).addClass('active');
                self.activeFilter = filter;
                self.filterAndRender();
            });

            // 5. One-click copy with visual tooltip / checkmark
            $(document).off('click', '.js-copy-data');
            $(document).on('click', '.js-copy-data', function(e) {
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
            $(document).off('click', '.js-contact-view-jobs');
            $(document).on('click', '.js-contact-view-jobs', function(e) {
                e.preventDefault();
                $('a[href="#requisitions"]').tab('show');
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
            $('#cdContactSkeletonLoaderDirect').show();
            $('#cdContactCardsStreamDirect').hide();
            $('#cdContactEmptyStateDirect').hide();

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
                    self.allContacts = [];
                    self.filterAndRender();
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

            // Sub-Card 4: Linked Jobs
            var jobsCountText = linkedJobs > 0 ? `<strong>${linkedJobs}</strong> Active Roles` : `Client Requisitions`;
            var jobsBadgeText = linkedJobs > 0 ? `${linkedJobs} Roles` : 'Direct';

            // Sub-Card 5: Status & Quick Actions
            var statusPillHtml = isActive
                ? `<span class="cd-contact-status-active"><i class="fas fa-circle"></i> Active</span>`
                : `<span class="cd-contact-status-inactive"><i class="fas fa-circle"></i> Inactive</span>`;

            var callBtnHtml = phone
                ? `<a href="tel:${cleanPhoneForTel}" class="cd-contact-btn-call" title="Call ${rawName}"><i class="fas fa-phone-alt"></i> Call</a>`
                : `<button type="button" class="cd-contact-btn-call disabled" style="opacity:0.4; pointer-events:none;"><i class="fas fa-phone-alt"></i> Call</button>`;

            var mailBtnHtml = email
                ? `<a href="mailto:${email}" class="cd-contact-btn-mail" title="Email ${rawName}"><i class="fas fa-envelope"></i> Email</a>`
                : `<button type="button" class="cd-contact-btn-mail disabled" style="opacity:0.4; pointer-events:none;"><i class="fas fa-envelope"></i> Email</button>`;

            var waPhoneClean = cleanPhoneForTel.replace('+', '');
            var waBtnHtml = phone
                ? `<a href="https://wa.me/${waPhoneClean}" target="_blank" class="cd-contact-btn-wa" title="WhatsApp ${rawName}"><i class="fab fa-whatsapp"></i></a>`
                : '';

            return `
                <div class="cd-contact-card" data-contact-id="${cid}">
                    <div class="cd-contact-card-grid">

                        <!-- SUB-CARD 1: Contact Identity & Profile -->
                        <div class="cd-contact-subcard cd-contact-subcard-profile">
                            <div class="cd-contact-subcard-hdr">
                                <span class="cd-contact-subcard-label">CONTACT PROFILE</span>
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
                                        <div class="d-flex align-items-center gap-1 mt-1">
                                            <span class="text-muted" style="font-size: 10px; font-weight: 600;">#C-${cid}</span>
                                            <span class="text-slate-300">•</span>
                                            <span class="text-muted" style="font-size: 10px;">Direct Client</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SUB-CARD 2: Direct Communication Channels -->
                        <div class="cd-contact-subcard cd-contact-subcard-comm">
                            <div class="cd-contact-subcard-hdr">
                                <span class="cd-contact-subcard-label">COMMUNICATION</span>
                                <button type="button" class="cd-copy-btn js-copy-data" data-copy="${copyAllText}" title="Copy email &amp; phone details">
                                    <i class="far fa-clone"></i>
                                </button>
                            </div>
                            <div class="cd-contact-subcard-body">
                                <div class="cd-comm-item" title="${email}">
                                    <i class="far fa-envelope text-primary"></i>
                                    ${emailHtml}
                                    ${emailCopyBtn}
                                </div>
                                <div class="cd-comm-item" title="${phone}">
                                    <i class="fas fa-phone-alt text-success"></i>
                                    ${phoneHtml}
                                    ${phoneCopyBtn}
                                </div>
                            </div>
                        </div>

                        <!-- SUB-CARD 3: Organization & Role Scope -->
                        <div class="cd-contact-subcard cd-contact-subcard-org">
                            <div class="cd-contact-subcard-hdr">
                                <span class="cd-contact-subcard-label">CLIENT &amp; SCOPE</span>
                                <span class="cd-org-role-badge text-truncate" title="${roleBadgeText}">
                                    <i class="fas fa-user-shield me-1"></i>${roleBadgeText}
                                </span>
                            </div>
                            <div class="cd-contact-subcard-body">
                                <div class="cd-org-client text-truncate" title="${clientName}">
                                    <i class="far fa-building text-slate-400"></i>
                                    <span class="text-truncate">${clientName}</span>
                                </div>
                                <div class="d-flex align-items-center gap-1 mt-1">
                                    <span class="badge badge-light-success fw-bold" style="font-size:9.5px; padding:2px 6px;">
                                        Authorized POC
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- SUB-CARD 4: Linked Jobs & Pipeline -->
                        <div class="cd-contact-subcard cd-contact-subcard-jobs">
                            <div class="cd-contact-subcard-hdr">
                                <span class="cd-contact-subcard-label">REQUISITIONS</span>
                                <span class="badge badge-light-info fw-bold" style="font-size:9.5px; padding:2px 5px;">
                                    ${jobsBadgeText}
                                </span>
                            </div>
                            <div class="cd-contact-subcard-body">
                                <div class="cd-linked-jobs-stat text-truncate">
                                    <i class="fas fa-briefcase text-info"></i>
                                    <span>${jobsCountText}</span>
                                </div>
                                <div>
                                    <a href="javascript:void(0);" class="cd-view-jobs-link js-contact-view-jobs" title="View client job requisitions">
                                        View Jobs <i class="fas fa-arrow-right ms-1" style="font-size:8px;"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- SUB-CARD 5: Status & Quick Reach Actions -->
                        <div class="cd-contact-subcard cd-contact-subcard-actions">
                            <div class="cd-contact-subcard-hdr">
                                <span class="cd-contact-subcard-label">STATUS &amp; ACTIONS</span>
                                ${statusPillHtml}
                            </div>
                            <div class="cd-contact-subcard-body">
                                <div class="cd-contact-actions-row">
                                    ${callBtnHtml}
                                    ${mailBtnHtml}
                                    ${waBtnHtml}
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- SUB-CARD 6: Bottom Activity & Metadata Strip -->
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
        window.DirectClientContactsManager.init();
    }
    function initDContactsManager() {
        window.DirectClientContactsManager.init();
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
    $(document).ready(function() {
        var loadedTabs = {};
        var dcClientId = parseInt('{{ $directClientDetail->client_id ?: ($directClientDetail->from_company_id ?: $directClientDetail->company_id) }}', 10);

        if ($('#candidate_list').hasClass('active') || $('a[href="#candidate_list"]').hasClass('active')) {
            window.ClientCandidateStoreDirect.init({{ auth()->user()->company_id }}, dcClientId, 'direct');
        } else if ($('#contacts').hasClass('active') || $('a[href="#contacts"]').hasClass('active')) {
            initDContactsDataTable();
            loadedTabs['contacts'] = true;
        } else if ($('#requisitions').hasClass('active') || $('a[href="#requisitions"]').hasClass('active')) {
            fetchJobsClientdirect();
            loadedTabs['requisitions'] = true;
        }

        $(document).on('click', 'a[href="#requisitions"], .cd-kpi-card[data-target-tab="#requisitions"]', function(e) {
            if (!loadedTabs['requisitions']) {
                fetchJobsClientdirect();
                loadedTabs['requisitions'] = true;
            }
        });

        $(document).on('shown.bs.tab', 'a[data-toggle="tab"], a[data-bs-toggle="tab"]', function (e) {
            var target = $(e.target).attr('href');
            if (!target) return;
            target = target.replace('#', '');

            if (target === 'candidate_list') {
                if (!window.ClientCandidateStoreDirect.companyId || window.ClientCandidateStoreDirect.clientId !== dcClientId || window.ClientCandidateStoreDirect.state === 'IDLE') {
                    window.ClientCandidateStoreDirect.init({{ auth()->user()->company_id }}, dcClientId, 'direct');
                } else {
                    window.ClientCandidateStoreDirect.reconcileVersion();
                }
                return;
            }

            if (loadedTabs[target]) return;

            if (target === 'contacts') {
                initDContactsDataTable();
                loadedTabs[target] = true;
            } else if (target === 'requisitions') {
                fetchJobsClientdirect();
                loadedTabs[target] = true;
            } else if (target === 'interview_tracker') {
                directfetchCandidatesInverViewTracker();
                loadedTabs[target] = true;
            } else if (target === 'candidate_joining') {
                var search = $('#ojSearchInputDirect').val() || '';
                directfetchCandidatesJoin(1, search, window.currentOjSegmentDirect || 'all');
            } else if (target === 'candidateDashboard') {
                initPerformanceDashboard();
                setTimeout(function() {
                    if (window.perfChartInstances) {
                        Object.values(window.perfChartInstances).forEach(function(c) {
                            try { if (c && typeof c.resize === 'function') c.resize(); } catch(err){}
                        });
                    }
                }, 60);
                loadedTabs[target] = true;
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

            var search = $('#ojSearchInputDirect').val() || '';
            var segment = window.currentOjSegmentDirect || 'all';
            directfetchCandidatesJoin(page, search, segment);
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
            this.bindEvents();
            this.subscribeReverb();
            this.fetch(1);
        },

        destroy: function() {
            if (this.reconcileTimer) {
                clearTimeout(this.reconcileTimer);
                this.reconcileTimer = null;
            }
            if (window.Echo && this.companyId && this.clientId) {
                var channel = 'private-company.' + this.companyId + '.client.' + this.clientId + '.candidates';
                try { window.Echo.leave(channel); } catch (e) {}
            }
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

        fetch: function(page, cursor) {
            var self = this;
            this.latestRequestId++;
            var requestId = this.latestRequestId;

            this.state = (this.state === 'READY') ? 'REFRESHING' : 'LOADING';
            this.filters.page = page || 1;
            this.filters.cursor = cursor || null;

            if (this.state === 'LOADING') {
                $('#cdCandSkeletonLoaderDirect').show();
                $('#cdCandidateTableWrapDirect').hide();
                $('#cdCandEmptyStateDirect').hide();
            }

            $.ajax({
                url: '{{ url("service_company/direct-client-candidate-list") }}',
                type: 'GET',
                headers: { 'Accept': 'application/json' },
                data: {
                    clientid: this.clientId,
                    search: this.filters.search,
                    status_id: this.filters.status_id,
                    job_id: this.filters.job_id,
                    page: this.filters.page,
                    cursor: this.filters.cursor,
                    size: 25
                },
                success: function(res) {
                    if (requestId !== self.latestRequestId) return;

                    self.data = res.data || [];
                    self.pagination = res.pagination || {};
                    self.clientCandidateVersion = res.meta?.client_candidate_version || self.clientCandidateVersion;
                    self.queryFingerprint = res.meta?.query_fingerprint || null;

                    self.candidateVersions = {};
                    self.data.forEach(function(cand) {
                        var cid = cand.id || cand.wiseId;
                        if (cid && cand.aggregate_version !== undefined) {
                            self.candidateVersions[cid] = cand.aggregate_version;
                        }
                    });

                    self.state = 'READY';
                    $('#cdCandSkeletonLoaderDirect').hide();
                    $('#cdCandidateTableWrapDirect').show();
                    self.renderRows();
                    self.updateCounts(self.pagination.total, self.pagination.total_relation);
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

                    $('#candidatetList').html(`<tr><td colspan="9" class="text-center py-6 text-danger fw-bold">${msg}</td></tr>`);
                    $('#paginationcandidate').empty();
                }
            });
        },

        updateCounts: function(total, relation) {
            var num = Number(total || 0);
            var formatted = (relation === 'gte') ? (num.toLocaleString() + '+') : num.toLocaleString();
            $('#cdCandidateCountTextDirect').text(formatted);
            $('#tabBadgeCandidates').text(formatted).show();

            var activeTab = $('.cd-cand-subtab-direct.active');
            activeTab.find('.cd-cand-subtab-badge').text(formatted).show();
            if (!this.filters.status_id) {
                $('#candTabCountAllDirect').text(formatted).show();
            }
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

            // 5. Sub-Card 5: Recruiter & Sourcing
            var recruiter = row.posted_by_name || row.posted_by || row.submitted_by_name || row.agency_root_name || 'Recruiter';
            var submitterCompany = row.submitted_by_company_name || row.post_company_name || row.apl_company_name || row.company_name || 'Agency';
            var recruiterFormatted = formatWithAcronyms(limitWords(recruiter, 12));
            var submitterFormatted = formatWithAcronyms(limitWords(submitterCompany, 12));
            var submittedAgo = formatTimeAgo(row.created_at);

            // 6. Sub-Card 6: Pipeline & Stage
            var stageName = row.child_status_name || row.master_status_name || row.name || 'InReview';
            var stageChipStyle = 'background:#fef9c3;color:#854d0e;border:1px solid #fde047;';
            if (stageName.toLowerCase().includes('offer')) {
                stageChipStyle = 'background:#cffafe;color:#0e7490;border:1px solid #a5f3fc;';
            } else if (stageName.toLowerCase().includes('join')) {
                stageChipStyle = 'background:#dcfce7;color:#16a34a;border:1px solid #bbf7d0;';
            } else if (stageName.toLowerCase().includes('reject')) {
                stageChipStyle = 'background:#fee2e2;color:#dc2626;border:1px solid #fecdd3;';
            } else if (stageName.toLowerCase().includes('source') || stageName.toLowerCase().includes('screen')) {
                stageChipStyle = 'background:#e0e7ff;color:#4338ca;border:1px solid #c7d2fe;';
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
                                    <div class="cd-cand-avatar-box">${candInitials}</div>
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
                                    <div class="cd-job-logo-box">${jobInitial}</div>
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

            var searchDebounceTimer = null;
            $(document).off('input', '#cdCandSearchInputDirect');
            $(document).on('input', '#cdCandSearchInputDirect', function() {
                var val = $(this).val();
                if (val && val.length > 0) {
                    $('#cdCandSearchClearDirect').show();
                } else {
                    $('#cdCandSearchClearDirect').hide();
                }
                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(function() {
                    self.filters.search = val;
                    self.filters.page = 1;
                    self.filters.cursor = null;
                    self.fetch(1);
                }, 300);
            });

            $(document).off('click', '#cdCandSearchClearDirect');
            $(document).on('click', '#cdCandSearchClearDirect', function() {
                $('#cdCandSearchInputDirect').val('');
                $(this).hide();
                self.filters.search = '';
                self.filters.page = 1;
                self.filters.cursor = null;
                self.fetch(1);
            });

            $(document).off('click', '.cd-cand-subtab-direct');
            $(document).on('click', '.cd-cand-subtab-direct', function() {
                var status = $(this).data('status');
                $('.cd-cand-subtab-direct').removeClass('active');
                $(this).addClass('active');
                self.filters.status_id = (status !== undefined && status !== null && status !== '') ? String(status) : '';
                self.filters.page = 1;
                self.filters.cursor = null;
                self.fetch(1);
            });

            $(document).off('click', '#cdCandResetBtnDirect');
            $(document).on('click', '#cdCandResetBtnDirect', function() {
                $('#cdCandSearchInputDirect').val('');
                $('#cdCandSearchClearDirect').hide();
                $('.cd-cand-subtab-direct').removeClass('active');
                $('.cd-cand-subtab-direct[data-status=""]').addClass('active');
                self.filters.search = '';
                self.filters.status_id = '';
                self.filters.job_id = '';
                self.filters.page = 1;
                self.filters.cursor = null;
                self.fetch(1);
            });

            $(document).off('wheel', '.cd-cand-bottom-strip, .cd-bottom-skills-scroll');
            $(document).on('wheel', '.cd-cand-bottom-strip, .cd-bottom-skills-scroll', function(e) {
                if (e.originalEvent && e.originalEvent.deltaY !== 0) {
                    this.scrollLeft += e.originalEvent.deltaY;
                    e.preventDefault();
                }
            });

            $(document).off('click', '#cdCandNextBtnDirect');
            $(document).on('click', '#cdCandNextBtnDirect', function() {
                if ($(this).hasClass('disabled')) return;
                var cursor = $(this).data('next-cursor');
                if (cursor) {
                    self.fetch(self.filters.page + 1, cursor);
                } else {
                    self.fetch(self.filters.page + 1);
                }
            });

            $(document).off('click', '#cdCandPrevBtnDirect');
            $(document).on('click', '#cdCandPrevBtnDirect', function() {
                if ($(this).hasClass('disabled')) return;
                var prevPage = Math.max(self.filters.page - 1, 1);
                self.filters.cursor = null;
                self.fetch(prevPage);
            });

            $(document).off('click', '.stage_sub_stage');
            $(document).on('click', '.stage_sub_stage', function(e) {
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
        if (segment === null || segment === undefined) {
            segment = window.currentOjSegmentDirect || 'all';
        } else {
            window.currentOjSegmentDirect = segment;
        }

        if (search === null || search === undefined) {
            search = $('#ojSearchInputDirect').val() || '';
        }

        $('.page-loader').css('display', 'flex');
        var clientid = '{{ $directClientDetail->client_id ?: ($directClientDetail->from_company_id ?: $directClientDetail->company_id) }}';
        return $.ajax({
            url: `{{ url('service_company/direct-client-candidate-join-list') }}?page=${page}`,
            type: 'GET',
            data: {
                search: search,
                segment: segment,
                clientid: clientid
            },
            success: function(response) {
                $('.page-loader').css('display', 'none');
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
    }

    $(document).on('click', '.cd-oj-seg-btn-direct', function() {
        var segment = $(this).data('segment');
        $('.cd-oj-seg-btn-direct').removeClass('active');
        $(this).addClass('active');
        window.currentOjSegmentDirect = segment;
        var search = $('#ojSearchInputDirect').val() || '';
        directfetchCandidatesJoin(1, search, segment);
    });

    var ojSearchTimerDirect = null;
    $(document).on('keyup', '#ojSearchInputDirect', function() {
        clearTimeout(ojSearchTimerDirect);
        var search = $(this).val();
        if (search && search.length > 0) {
            $('#ojSearchClearDirect').show();
        } else {
            $('#ojSearchClearDirect').hide();
        }
        ojSearchTimerDirect = setTimeout(function() {
            directfetchCandidatesJoin(1, search, window.currentOjSegmentDirect || 'all');
        }, 300);
    });

    $(document).on('click', '#ojSearchClearDirect', function() {
        $('#ojSearchInputDirect').val('');
        $(this).hide();
        directfetchCandidatesJoin(1, '', window.currentOjSegmentDirect || 'all');
    });

    $(document).on('click', '#ojResetBtnDirect', function() {
        $('#ojSearchInputDirect').val('');
        $('#ojSearchClearDirect').hide();
        window.currentOjSegmentDirect = 'all';
        $('.cd-oj-seg-btn-direct').removeClass('active');
        $('.cd-oj-seg-btn-direct[data-segment="all"]').addClass('active');
        directfetchCandidatesJoin(1, '', 'all');
    });

    // ══════════════════════════════════════════════════════════════════════════
    // GOD-LEVEL CLIENT-FIRST INTERVIEW STORE (DIRECT CLIENT DRAWER)
    // 0% Server Strain | Multi-Tenant Isolated | Bidirectional Cursor Navigation
    // ══════════════════════════════════════════════════════════════════════════
    const ClientInterviewStoreDirect = (function() {
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

        function renderTableRows(rows) {
            const $list = $('#candidatetListinterviewtracker');
            if (!rows || !rows.length) {
                $list.html(`
                    <div class="it-empty-card">
                        <i class="fas fa-calendar-times fs-2 text-gray-300 d-block mb-2"></i>
                        No interviews scheduled for this client.
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

                // Followup pill
                let followupHtml = '';
                if (fu.label) {
                    followupHtml = `
                        <div class="it-cand-meta">
                            <span class="it-meta-tag ${fu.style_class || 'info'}">
                                ${fu.label}
                            </span>
                            ${fu.formatted_at ? `<span class="it-meta-date">${fu.formatted_at}</span>` : ''}
                        </div>
                    `;
                }

                // Meeting link / Address
                let linkHtml = '';
                if (sch.meeting_link) {
                    if (sch.link_type === 'address') {
                        linkHtml = `
                            <span class="it-dt-link" title="${sch.meeting_link}">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                <span>${sch.link_label || 'View Address'}</span>
                            </span>
                        `;
                    } else if (sch.link_type === 'link') {
                        linkHtml = `
                            <a href="${sch.meeting_link}" target="_blank" rel="noopener noreferrer" class="it-dt-link">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                                <span>${sch.link_label || 'Interview Link'}</span>
                            </a>
                        `;
                    }
                }

                // Mode icon SVG
                let modeSvg = '';
                if (sch.mode_icon_type === 'phone') {
                    modeSvg = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>';
                } else if (sch.mode_icon_type === 'map-marker') {
                    modeSvg = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>';
                } else {
                    modeSvg = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>';
                }

                // Feedback icon SVG
                let fbIconSvg = '';
                if (fb.status_icon === 'check') {
                    fbIconSvg = '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>';
                } else if (fb.status_icon === 'times') {
                    fbIconSvg = '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';
                } else if (fb.status_icon === 'exclamation-triangle') {
                    fbIconSvg = '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>';
                } else if (fb.status_icon === 'exclamation-circle') {
                    fbIconSvg = '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>';
                } else {
                    fbIconSvg = '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>';
                }

                // Recommendation pill
                let recHtml = '';
                if (fb.recommendation) {
                    recHtml = `<span class="it-rec-pill" style="background:${fb.recommendation.bg};color:${fb.recommendation.color};">${fb.recommendation.label}</span>`;
                }

                return `
                    <div class="it-primary-card" data-interview-id="${intv.id}">
                      <!-- 1. Candidate Sub-Card (2.2fr) -->
                      <div class="it-subcard it-subcard-cand">
                        <div class="it-cand-avatar ${cand.avatar_color_class || 'it-avatar-blue'}">
                          ${cand.initial || 'C'}
                        </div>
                        <div class="it-cand-info">
                          <div class="it-cand-name" title="${cand.name || 'Candidate'}">
                            ${cand.name || 'Candidate'}
                          </div>
                          <div class="it-cand-role" title="${cand.designation || '—'}">
                            ${cand.designation || '—'}
                          </div>
                          ${followupHtml}
                        </div>
                      </div>

                      <!-- 2. Job Requisition Sub-Card (2.3fr) -->
                      <div class="it-subcard it-subcard-job">
                        <div class="it-job-title" title="${job.title || 'Job Position'}">
                          ${job.title || 'Job Position'}
                        </div>
                        <div class="it-job-company" title="${job.display_company_name || job.client_name || ''}">
                          ${job.display_company_name || job.client_name || 'Client Account'}
                        </div>
                        <div class="it-job-tags">
                          <span class="it-job-type ${job.job_type_class || 'it-job-type-direct'}">${job.job_type_label || 'Direct Client'}</span>
                          ${job.job_id_display ? `
                            <span class="it-dot-sep"></span>
                            <span class="it-job-id">${job.job_id_display}</span>
                          ` : ''}
                        </div>
                      </div>

                      <!-- 3. Interview Schedule Sub-Card (1.75fr) -->
                      <div class="it-subcard it-subcard-datetime">
                        <div class="it-dt-row it-dt-date">
                          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                          </svg>
                          <span>${sch.date_formatted || '—'}</span>
                        </div>
                        ${sch.time_formatted ? `
                          <div class="it-dt-row it-dt-time">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                              <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                            </svg>
                            <span>${sch.time_formatted}</span>
                          </div>
                        ` : ''}
                        <div class="it-dt-row it-dt-mode">
                          ${modeSvg}
                          <span>${sch.mode_label || 'Video Call'}</span>
                        </div>
                        ${linkHtml}
                      </div>

                      <!-- 4. Interviewers Sub-Card (1.1fr) -->
                      <div class="it-subcard it-subcard-interviewers">
                        <div class="it-lead-name">${intvs.lead_name || 'N/A'}</div>
                        <div class="it-lead-comp">${intvs.lead_company || 'Panel'}</div>
                        ${intvs.extra_count > 0 ? `
                          <div class="it-extra-wrap">
                            <span class="it-extra-pill">${intvs.extra_label}</span>
                            <span class="it-extra-lbl">${intvs.extra_count + 1} Interviewers</span>
                          </div>
                        ` : ''}
                      </div>

                      <!-- 5. Source Sub-Card (1.1fr) -->
                      <div class="it-subcard it-subcard-source">
                        <div class="it-src-name" title="${src.name || '—'}">
                          ${src.name || '—'}
                        </div>
                        <div class="it-src-comp" title="${src.company_name || ''}">
                          ${src.company_name || ''}
                        </div>
                      </div>

                      <!-- 6. Stage Sub-Card (0.75fr) -->
                      <div class="it-subcard it-subcard-stage">
                        <span class="it-badge ${stg.badge_class || 'it-badge-blue'}">
                          ${stg.master_status_name || 'Interview'}
                        </span>
                        ${stg.child_status_name ? `
                          <span class="it-stage-sub">
                            ${stg.child_status_name}
                          </span>
                        ` : ''}
                      </div>

                      <!-- 7. Feedback Sub-Card (0.8fr) -->
                      <div class="it-subcard it-subcard-feedback">
                        <span class="it-fb-status" style="color:${fb.status_color || '#f59e0b'};">
                          ${fbIconSvg}
                          <span>${fb.status_text || 'Pending'}</span>
                        </span>
                        ${recHtml}
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
                <div class="it-pagi-info">
                    Showing <strong>${fromItem} - ${toItem}</strong> of <strong>${totalItems}</strong> interviews
                </div>
                <div class="it-pagi-controls">
                    <div class="d-flex align-items-center gap-2 me-2">
                        <span class="text-muted" style="font-size:11px;">Rows:</span>
                        <select class="it-pagi-size-select" id="itPageSizeSelectDirect">
                            <option value="10" ${pageSize === 10 ? 'selected' : ''}>10 / page</option>
                            <option value="25" ${pageSize === 25 ? 'selected' : ''}>25 / page</option>
                            <option value="50" ${pageSize === 50 ? 'selected' : ''}>50 / page</option>
                        </select>
                    </div>
                    <button type="button" class="it-pagi-btn ${!hasPrev ? 'disabled' : ''}" data-direction="prev">
                        <i class="fas fa-chevron-left" style="font-size:10px;"></i> Previous
                    </button>
                    <span class="it-pagi-page-indicator">Page ${cursorIndex + 1}</span>
                    <button type="button" class="it-pagi-btn ${!hasNext ? 'disabled' : ''}" data-direction="next">
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

        return {
            init: init,
            fetch: fetchInterviews
        };
    })();

    function directfetchCandidatesInverViewTracker(page = 1, search = '') {
        const clientid = parseInt('{{ $directClientDetail->client_id ?: ($directClientDetail->from_company_id ?: $directClientDetail->company_id) }}', 10);
        if (clientid) {
            ClientInterviewStoreDirect.fetch(clientid, null, false, search);
        }
    }

    $(document).ready(function() {
        ClientInterviewStoreDirect.init();
    });

    function fetchJobsClientdirect(page = 1, search = '', status = 'all') {
        $('.page-loader').css('display', 'flex');
        var activeStatus = status || window.currentJobStatusDirect || 'all';
        var clientName = $('.cd-client-name').first().text().trim() || '{{ addslashes($detailName ?? "") }}';
        var clientId = $('#direct_client_id').val() || '{{ $directClientDetail->client_id ?? $directClientDetail->id ?? $dc_id ?? 0 }}';
        var fromCompanyId = '{{ $directClientDetail->company_id ?? $directClientDetail->from_company_id ?? $directClientDetail->client_id ?? $directClientDetail->id ?? $dc_id ?? 0 }}';
        var searchVal = (search !== undefined && search !== null && search !== '') ? search : ($('#requisitions #inputJobSearch').val() || '');

        return $.ajax({
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
    }
    window.fetchJobsClientdirect = fetchJobsClientdirect;

    // Direct Client Jobs Tab: Dynamic Search Bar (Keyup & Clear)
    var directJobSearchTimer = null;
    $(document).on('keyup', '#requisitions #inputJobSearch', function() {
        clearTimeout(directJobSearchTimer);
        var searchVal = $(this).val();
        if (searchVal.length > 0) {
            $('#requisitions #btnJobSearchClear').show();
        } else {
            $('#requisitions #btnJobSearchClear').hide();
        }
        directJobSearchTimer = setTimeout(function() {
            fetchJobsClientdirect(1, searchVal, window.currentJobStatusDirect || 'all');
        }, 300);
    });

    $(document).on('click', '#requisitions #btnJobSearchClear', function() {
        $('#requisitions #inputJobSearch').val('');
        $(this).hide();
        fetchJobsClientdirect(1, '', window.currentJobStatusDirect || 'all');
    });

    // Direct Client Jobs Tab: Filter Button Interaction
    $(document).on('click', '#requisitions .js-job-filter-btn', function() {
        var status = $(this).data('status') || 'all';
        window.currentJobStatusDirect = status;
        $('#requisitions .js-job-filter-btn').removeClass('active');
        $(this).addClass('active');
        fetchJobsClientdirect(1, $('#requisitions #inputJobSearch').val(), status);
    });

    // Direct Client Jobs Tab: Click Routing (Fixing Drawer Click Collision Bug)
    $(document).on('click', '#directclientJobsListajax .js-job-drawer-trigger', function(e) {
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

    $(document).ready(function() {
        if ($('#candidateDashboard').hasClass('active') || $('a[href="#candidateDashboard"]').hasClass('active')) {
            initPerformanceDashboard();
        }
    });

    // Direct Client Drawer Interactive Handlers
    $(document).on('click', '.cd-kpi-card, .cd-nav-tab', function() {
        var targetTab = $(this).data('target-tab') || $(this).data('target');
        var segment = $(this).data('segment');
        if (targetTab) {
            $('.cd-tabs-bar a[href="' + targetTab + '"], a[href="' + targetTab + '"]').tab('show');
            if (targetTab === '#candidate_joining' && segment) {
                window.currentOjSegmentDirect = segment;
                $('.cd-oj-seg-btn-direct').removeClass('active');
                $('.cd-oj-seg-btn-direct[data-segment="' + segment + '"]').addClass('active');
                directfetchCandidatesJoin(1, '', segment);
            }
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

    $(document).on('click', '.cd-btn-add-tag-trigger, .cd-btn-edit-tags', function(e) {
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
