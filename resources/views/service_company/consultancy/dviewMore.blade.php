
    @php
    use App\Helper\GeneralMethod;
    use Carbon\Carbon;
        $startOfMonthcomman = Carbon::now()->startOfMonth()->subMonths(5);
        $endOfMonthcomman =  Carbon::now()->endOfMonth();
        $endDate = Carbon::today()->endOfDay();
        $startDate = Carbon::today()->subDays(6)->startOfDay();
        $match = collect($directClientDetail->staffing_company)->first(function ($item) use ($company_id) {
            return ($item['from_company_id'] ?? null) == $company_id
                || ($item['to_company_id'] ?? null) == $company_id;
        });
        $from_company_id = ($match['from_company_id'] ?? null) == $company_id ? $match['from_company_id'] : null;
        $to_company_id   = ($match['from_company_id'] ?? null) == $company_id ? $match['to_company_id'] : null;

        $bgTextDecore = $strVal = '';
        $str = trim(@$directClientDetail->name);
        $str = $strVal = strtolower($str[0]);
        $str = ord($str);
        if ($str >= 97 && $str <= 101) {
            $bgTextDecore = 'bg-light-danger text-danger';
        } elseif ($str >= 102 && $str <= 106) {
            $bgTextDecore = 'bg-light-warning text-warning';
        } elseif ($str >= 107 && $str <= 111) {
            $bgTextDecore = 'bg-light-primary text-primary';
        } elseif ($str >= 112 && $str <= 116) {
            $bgTextDecore = 'bg-light-success text-success';
        } elseif ($str >= 117 && $str <= 121) {
            $bgTextDecore = 'bg-dark text-secondary';
        } else {
            $bgTextDecore = 'bg-light-secondary text-dark';
        }

        $background_image = '';
        if (
            !empty(@$directClientDetail->logo_logical_path) &&
            strlen(@$directClientDetail->logo_logical_path) > 10
        ) {
            $background_image = @$directClientDetail->logo_logical_path;
        }

        $logo_path = '';//url('img/client-default.png');
        if (!empty(@$directClientDetail->logo) && strlen(@$directClientDetail->logo) > 10) {
            $logo_path = @$directClientDetail->logo;
        }
        $industries_name = '';
        if (trim($directClientDetail->industry) != '') {
            $industries_name = $industries[@$directClientDetail->industries ? @$directClientDetail->industries : 0];
        }
        
        $tags = @$directClientDetail->tags;
        $tags = explode(',', $tags);

        $Active = $Hold = $Closed = 0;
        $RowJobs = GeneralMethod:: agencyJobsCountAllstatus($directClientDetail->company_id,$company_id);
        $Active = $RowJobs->job_active ?? 0;
        $Hold = $RowJobs->job_hold ?? 0;
        $Closed = $RowJobs->job_inactive ?? 0;

        $all_candidates = $Joined = $offers = $all_interview = 0;
        $candidatesProfiles = GeneralMethod:: agencyCandidatesCountList($directClientDetail->company_id,$company_id);
        $all_candidates = $candidatesProfiles->all_candidates ?? 0;
        $Joined = $candidatesProfiles->Joined ?? 0;
        $offers = $candidatesProfiles->offers ?? 0;
        $all_interview = $candidatesProfiles->all_interview ?? 0;

        $relationget =  GeneralMethod::getRelationData($directClientDetail->company_id,$company_id);
        $from_company_id = $relationget->from_company_id ?? null;
        $recommendation_id = $relationget->to_company_id ?? null;
        $final_status = $relationget->final_status ?? null;
        $to_created_at = $relationget->to_created_at ?? null;
        $created_at = $relationget->created_at ?? null;
        $newDate = date('d M, Y', strtotime($to_created_at));
        $timeAgo = GeneralMethod::timeAgo($to_created_at);


    @endphp

    <style>
      /* ── Jobs tab: card/grid layout (scoped to #tab-jobs) ── */
      #tab-jobs {
        --indigo: #3b55e6;
        --orange: #f4682a;
        --orange-card: #fff4ee;
        --border: #e4e6ef;
        --text-dark: #181c32;
        --text-mid: #6d7284;
        --text-light: #a1a5b7;
        --white: #ffffff;
        --radius: 12px;
        --radius-sm: 8px;
      }
      #tab-jobs .table-card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--border);
        overflow: hidden;
      }
      #tab-jobs .table-header,
      #tab-jobs .job-row {
        display: grid;
        grid-template-columns: 2.6fr 1.05fr 1.15fr 1.3fr 0.95fr 0.95fr 2.55fr;
        align-items: start;
        column-gap: 12px;
      }
      #tab-jobs .table-header {
        padding: 9px 12px;
        background: #f7f8fc;
        border-bottom: 1.5px solid var(--border);
        align-items: center;
      }
      #tab-jobs .th {
        font-size: 10.5px;
        font-weight: 600;
        color: var(--text-mid);
        text-transform: uppercase;
        letter-spacing: 0.4px;
        display: flex;
        align-items: center;
        gap: 4px;
      }
      #tab-jobs .job-row {
        padding: 12px 12px;
        border-bottom: 1px solid var(--border);
        transition: background 0.12s;
      }
      #tab-jobs .job-row:last-of-type { border-bottom: none; }
      #tab-jobs .job-row:hover { background: #fafbff; }
      #tab-jobs .col-job { display: flex; gap: 10px; }
      #tab-jobs .job-logo {
        width: 48px; height: 48px; border-radius: 10px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; font-weight: 800; color: var(--white);
        overflow: hidden; align-self: flex-start;
      }
      #tab-jobs .job-info { flex: 1; min-width: 0; }
      #tab-jobs .job-title { font-size: 12.5px; font-weight: 700; color: var(--text-dark); margin-bottom: 1px; line-height: 1.3; }
      #tab-jobs .job-dept { font-size: 11px; color: var(--text-mid); margin-bottom: 1px; }
      #tab-jobs .job-client { font-size: 11px; color: var(--text-mid); font-weight: 500; margin-bottom: 7px; }
      #tab-jobs .job-meta-line { display: flex; align-items: center; flex-wrap: wrap; gap: 5px; font-size: 10.5px; color: var(--text-light); }
      #tab-jobs .skills-inline { font-size: 10.5px; color: var(--text-light); }
      #tab-jobs .skills-inline b { font-weight: 600; color: var(--text-mid); }
      #tab-jobs .col-exp { display: flex; flex-direction: column; gap: 5px; }
      #tab-jobs .exp-row { display: flex; align-items: center; gap: 5px; font-size: 11.5px; font-weight: 500; color: var(--text-dark); }
      #tab-jobs .exp-row svg, #tab-jobs .sal-row svg { width: 13px; height: 13px; color: var(--text-light); flex-shrink: 0; }
      #tab-jobs .sal-row { display: flex; align-items: center; gap: 5px; font-size: 11.5px; width: 150px; font-weight: 500; color: var(--text-dark); }
      #tab-jobs .badge-type { display: inline-flex; align-items: center; gap: 3px; padding: 2px 9px; border-radius: 20px; font-size: 10px; font-weight: 600; width: fit-content; margin-top: 2px; }
      #tab-jobs .badge-internal { background: #eef2ff; color: var(--indigo); }
      #tab-jobs .badge-client { background: #eefff6; color: #09b760; }
      #tab-jobs .col-hl { display: flex; flex-direction: column; gap: 5px; }
      #tab-jobs .badge-hiring { padding: 2px 9px; border-radius: 20px; font-size: 10.5px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; width: fit-content; }
      #tab-jobs .badge-hiring::before { content: ""; width: 6px; height: 6px; background: #c9c4c4; border-radius: 50%; }
      #tab-jobs .loc-row, #tab-jobs .mode-row { display: flex; align-items: center; gap: 4px; font-size: 11.5px; color: var(--text-mid); font-weight: 500; }
      #tab-jobs .loc-row svg, #tab-jobs .mode-row svg { width: 12px; height: 12px; color: var(--text-light); }
      #tab-jobs .col-hm { font-size: 11.5px; }
      #tab-jobs .hm-name { font-size: 12px; font-weight: 600; color: var(--text-dark); margin-bottom: 2px; }
      #tab-jobs .hm-title { font-size: 11px; color: var(--text-mid); margin-bottom: 1px; }
      #tab-jobs .hm-company { font-size: 11px; color: var(--text-light); }
      #tab-jobs .col-action { display: flex; flex-direction: column; gap: 5px; padding: 0 6px; }
      #tab-jobs .btn-action {
        display: inline-flex; align-items: center; gap: 4px; padding: 4px 6px;
        border: 1.5px dashed var(--border); border-radius: 6px; background: transparent;
        color: var(--text-mid); font-size: 9px; font-weight: 500; cursor: pointer;
        transition: all 0.15s; font-family: "Poppins", sans-serif; white-space: nowrap;
        justify-content: center; min-height: 30px;
      }
      #tab-jobs .btn-action:hover { border-color: var(--indigo); color: var(--indigo); background: #f0f3ff; }
      #tab-jobs .btn-action svg { width: 11px; height: 11px; }
      #tab-jobs .action-count { font-size: 11px; color: var(--text-mid); }
      #tab-jobs .action-more { font-size: 10.5px; color: var(--indigo); cursor: pointer; font-weight: 500; }
      #tab-jobs .col-pipeline { display: flex; flex-direction: column; gap: 4px; padding-left: 10px; border-left: 1px solid #eef0f5; }
      #tab-jobs .pipeline-stages { display: flex; align-items: flex-start; gap: 1px; flex-wrap: nowrap; overflow: hidden; padding-bottom: 2px; }
      #tab-jobs .stage { display: flex; flex-direction: column; align-items: center; gap: 2px; flex: 1; min-width: 0; }
      #tab-jobs .stage-label { font-size: 8.5px; font-weight: 600; color: var(--text-mid); text-transform: uppercase; letter-spacing: 0.3px; }
      #tab-jobs .stage-badge { min-width: 20px; width: 20px; height: 18px; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 700; }
      #tab-jobs .stage-badge a { text-decoration: none; }
      #tab-jobs .s-inw { background: #ffffde; color: #bdbd26; }
      #tab-jobs .s-sou { background: #efefef; color: #000000; }
      #tab-jobs .s-scr { background: #f7deff; color: #c000ff; }
      #tab-jobs .s-eva { background: #f0f8ff; color: #2896ff; }
      #tab-jobs .s-neg { background: #ffe6f2; color: #ff4098; }
      #tab-jobs .s-off { background: #fff9ed; color: #ffab00; }
      #tab-jobs .s-joi { background: #d4ffde; color: #06c732; }
      #tab-jobs .s-rej { background: #ffe2e5; color: #ff0202; }
      #tab-jobs .pipeline-total { font-size: 10px; font-weight: 600; color: var(--text-mid); text-align: right; display: flex; justify-content: flex-end; gap: 6px; flex-wrap: wrap; }
      #tab-jobs .pipeline-total strong { color: var(--text-dark); }
      #tab-jobs .add-date { font-size: 10px; color: var(--text-light); }
      #tab-jobs .drag-col { display: flex; flex-direction: column; align-items: center; padding-top: 2px; flex-shrink: 0; }
      #tab-jobs .drag-handle { color: var(--text-light); cursor: grab; }
      #tab-jobs .it-job-type { display: inline-flex; align-items: center; padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: 600; }
      #tab-jobs .it-job-id { font-size: 10px; color: #6a6b70; font-weight: 500; text-decoration: none; }
      #tab-jobs .it-job-id:hover { text-decoration: underline; }
      #tab-jobs .it-dot-separator { width: 3px; height: 3px; border-radius: 50%; background: var(--text-light); }
      @media (max-width: 1600px) {
        #tab-jobs .table-header, #tab-jobs .job-row { grid-template-columns: 2.3fr 1fr 1fr 1.2fr 0.9fr 0.9fr 2.8fr; }
      }
      @media (max-width: 1366px) {
        #tab-jobs .table-header, #tab-jobs .job-row { grid-template-columns: 2.6fr .95fr .95fr 1fr .85fr .85fr 1.8fr; }
      }
    </style>
    <style>
      *,
      *::before,
      *::after {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }
       
          
      .modal-overlay.show {
        opacity: 1;
        visibility: visible;
      }
      .modal-panel {
        background: #f5f8fa;
        overflow-y: auto;
        transform: translateX(100%);
        transition: transform 0.35s cubic-bezier(0.32, 0.72, 0, 1);
        /* box-shadow: -8px 0 40px rgba(0, 0, 0, 0.15); */
      }
      .modal-overlay.show .modal-panel {
        transform: translateX(0);
      }
      .modal-panel::-webkit-scrollbar {
        width: 6px;
      }
      .modal-panel::-webkit-scrollbar-track {
        background: transparent;
      }
      .modal-panel::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 3px;
      }
      .modal-inner {
        margin: 0 auto;
        padding: 4px 10px 8px;
      }
      .modal-header-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
      }
      .modal-title {
        font-size: 20px;
        font-weight: 700;
        color: #181c32;
      }
      .modal-close {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        border: none;
        background: transparent;
        color: #a1a5b7;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        transition: all 0.2s;
      }
      .modal-close:hover {
        background: #fff;
        color: #5e6278;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      }
      .profile-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #eff2f5;
        padding: 24px;
        margin-bottom: 16px;
      }
      .profile-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
      }
      .profile-left {
        display: flex;
        gap: 18px;
        flex: 1;
        min-width: 0;
      }
      .logo-wrap {
        position: relative;
        flex-shrink: 0;
      }
      .logo-circle {
        width: 88px;
        height: 88px;
        border-radius: 50%;
        background: #f8f9fa;
        border: 1px solid #e4e6ef;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
      }
      .logo-circle svg {
        width: 64px;
        height: 64px;
      }
      .verified-check {
        position: absolute;
        top: -2px;
        right: -2px;
        width: 24px;
        height: 24px;
        background: #50cd89;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2.5px solid #fff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
      }
      .verified-check i {
        color: #fff;
        font-size: 10px;
      }
      .profile-info {
        flex: 1;
        min-width: 0;
      }
      .profile-name-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
        flex-wrap: wrap;
      }
      .profile-name {
        font-size: 20px;
        font-weight: 700;
        color: #181c32;
      }
      .verified-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #e8fff3;
        color: #47be7d;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 50px;
      }
      .verified-badge i {
        font-size: 9px;
      }
      .profile-meta {
        display: flex;
        align-items: center;
        gap: 4px 16px;
        flex-wrap: wrap;
        font-size: 13px;
        color: #a1a5b7;
        margin-bottom: 12px;
      }
      .meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
      }
      .meta-item i {
        font-size: 11px;
        color: #b5b5c3;
      }
      .meta-divider {
        color: #e4e6ef;
        font-size: 10px;
      }
      .meta-link {
        color: #009ef7;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 4px;
      }
      .meta-link:hover {
        text-decoration: underline;
      }
      .meta-link i.fa-external-link-alt {
        font-size: 9px;
      }
      .service-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
      }
      .tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: #f5f8fa;
        border: 1px solid #e4e6ef;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        color: #5e6278;
      }
      .tag i {
        font-size: 11px;
        color: #b5b5c3;
      }
      .tag-more {
        background: #f1faff;
        border-color: #f1faff;
        color: #009ef7;
        cursor: pointer;
        transition: all 0.2s;
      }
      .tag-more:hover {
        background: #009ef7;
        color: #fff;
      }
      .tag.hidden {
        display: none;
      }
      .profile-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 12px;
        flex-shrink: 0;
      }
      .status-row {
        display: flex;
        align-items: center;
        gap: 10px;
      }
      .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #e8fff3;
        color: #47be7d;
        font-size: 13px;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 6px;
        border: 1px solid rgba(80, 205, 137, 0.15);
      }
      .status-dot {
        width: 8px;
        height: 8px;
        background: #50cd89;
        border-radius: 50%;
        position: relative;
      }
      .status-dot::after {
        content: "";
        position: absolute;
        inset: -3px;
        border-radius: 50%;
        border: 1px solid #50cd89;
        animation: pulse 2s infinite;
      }
      @keyframes pulse {
        0%,
        100% {
          transform: scale(1);
          opacity: 1;
        }
        50% {
          transform: scale(1.3);
          opacity: 0;
        }
      }
      .actions-dropdown {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        background: #fff;
        border: 1px solid #e4e6ef;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        color: #5e6278;
        cursor: pointer;
        transition: all 0.2s;
        font-family: "Poppins", sans-serif;
      }
      .actions-dropdown:hover {
        background: #f5f8fa;
        border-color: #b5b5c3;
      }
      .actions-dropdown i {
        font-size: 10px;
      }
      .action-buttons {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: flex-end;
      }
      .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        font-family: "Poppins", sans-serif;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        white-space: nowrap;
      }
      .btn-primary {
        background: #009ef7;
        color: #fff;
      }
      .btn-primary:hover {
        background: #0095e8;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 158, 247, 0.3);
      }
      .btn-outline {
        background: #fff;
        color: #5e6278;
        border: 1px solid #e4e6ef;
      }
      .btn-outline:hover {
        background: #f5f8fa;
        border-color: #b5b5c3;
      }
      .btn i {
        font-size: 11px;
      }
      .kpi-row {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 16px;
        margin-bottom: 16px;
      }
      .kpi-box {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #eff2f5;
        padding: 13px;
        transition: all 0.2s;
      }
      .kpi-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04);
      }
      .kpi-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
      }
      .kpi-icon-box {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
      }
      .kpi-icon-blue {
        background: #f1faff;
        color: #009ef7;
      }
      .kpi-icon-green {
        background: #e8fff3;
        color: #50cd89;
      }
      .kpi-icon-purple {
        background: #f8f5ff;
        color: #7239ea;
      }
      .kpi-icon-orange {
        background: #fff8dd;
        color: #f6a723;
      }
      .kpi-icon-teal {
        background: #f0fdfa;
        color: #14b8a6;
      }
      .kpi-icon-red {
        background: #fff5f8;
        color: #f1416c;
      }
      .kpi-label {
        font-size: 12px;
        font-weight: 600;
        color: #7e8299;
        line-height: 1.3;
      }
      .kpi-number {
        font-size: 28px;
        font-weight: 700;
        color: #181c32;
        line-height: 1;
        margin-bottom: 10px;
      }
      .kpi-trend {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
      }
      .kpi-trend i {
        font-size: 9px;
      }
      .trend-up {
        color: #50cd89;
      }
      .trend-up span {
        font-weight: 700;
      }
      .trend-text {
        color: #b5b5c3;
      }
      .tabs-bar {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #eff2f5;
        margin-bottom: 16px;
        overflow-x: auto;
      }
      .tabs-bar::-webkit-scrollbar {
        display: none;
      }
      .tabs-inner {
        display: flex;
        padding: 0 8px;
      }
      .tab {
        position: relative;
        padding: 14px 20px;
        font-size: 13px;
        font-weight: 500;
        color: #a1a5b7;
        cursor: pointer;
        white-space: nowrap;
        border: none;
        background: none;
        font-family: "Poppins", sans-serif;
        transition: color 0.2s;
      }
      .tab:hover {
        color: #009ef7;
      }
      .tab.active {
        color: #009ef7;
        font-weight: 600;
      }
      .tab.active::after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 20px;
        right: 20px;
        height: 2.5px;
        background: #009ef7;
        border-radius: 2px 2px 0 0;
      }
      .tab-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        background: #f5f8fa;
        color: #7e8299;
        font-size: 10px;
        font-weight: 700;
        border-radius: 50px;
        margin-left: 6px;
      }
      .tab.active .tab-count {
        background: #f1faff;
        color: #009ef7;
      }
      .tab-content {
        display: none;
      }
      .tab-content.active {
        display: block;
      }
      .content-grid {
        display: grid;
        grid-template-columns: 1.2fr 0.8fr;
        gap: 16px;
        margin-bottom: 16px;
        align-items: start;
      }
      .content-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #eff2f5;
        padding: 24px;
      }
      .card-header-side {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
      }
      .card-header-icon {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        background: #f5f8fa;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #a1a5b7;
        font-size: 13px;
      }
      .card-header-title {
        font-size: 15px;
        font-weight: 700;
        color: #181c32;
      }
      .about-text {
        font-size: 13px;
        color: #7e8299;
        line-height: 1.7;
      }
      .about-text p {
        margin-bottom: 14px;
      }
      .about-text p:last-child {
        margin-bottom: 0;
      }
      .about-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 20px;
        padding-top: 16px;
        border-top: 1px solid #eff2f5;
      }
      .read-more {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #009ef7;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        background: none;
        border: none;
        font-family: "Poppins", sans-serif;
        padding: 0;
      }
      .read-more:hover {
        text-decoration: underline;
      }
      .read-more i {
        font-size: 10px;
        transition: transform 0.2s;
      }
      .read-more.expanded i {
        transform: rotate(180deg);
      }
      .word-count {
        font-size: 11px;
        color: #b5b5c3;
      }
      .info-list {
        display: flex;
        flex-direction: column;
      }
      .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px dashed #eff2f5;
        gap: 12px;
      }
      .info-row:last-child {
        border-bottom: none;
      }
      .info-label {
        font-size: 12px;
        color: #a1a5b7;
        font-weight: 500;
        flex-shrink: 0;
      }
      .info-value {
        font-size: 12px;
        color: #3f4254;
        font-weight: 600;
        text-align: right;
        display: flex;
        align-items: center;
        gap: 6px;
        justify-content: flex-end;
        flex-wrap: wrap;
      }
      .info-value a {
        color: #009ef7;
        text-decoration: none;
      }
      .info-value a:hover {
        text-decoration: underline;
      }
      .info-value a i {
        font-size: 9px;
        margin-left: 2px;
      }
      .mini-verified {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        background: #e8fff3;
        color: #47be7d;
        font-size: 10px;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 50px;
      }
      .mini-verified i {
        font-size: 8px;
      }
      .status-text {
        color: #50cd89;
      }
      .status-text .status-dot {
        display: inline-block;
        margin-right: 4px;
      }
      .commercial-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px;
        background: #f5f8fa;
        border-radius: 8px;
        margin-bottom: 10px;
        transition: all 0.2s;
      }
      .commercial-item:last-child {
        margin-bottom: 0;
      }
      .commercial-item:hover {
        background: #f1faff;
      }
      .commercial-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
      }
      .commercial-icon.purple {
        background: #f8f5ff;
        color: #7239ea;
      }
      .commercial-icon.orange {
        background: #fff8dd;
        color: #f6a723;
      }
      .commercial-title {
        font-size: 13px;
        font-weight: 600;
        color: #181c32;
      }
      .commercial-desc {
        font-size: 11px;
        color: #a1a5b7;
        margin-top: 2px;
      }
      .contacts-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #eff2f5;
        padding: 24px;
      }
      .contacts-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
      }
      .contacts-title {
        font-size: 15px;
        font-weight: 700;
        color: #181c32;
      }
      .view-all {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #009ef7;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
      }
      .view-all:hover {
        text-decoration: underline;
      }
      .view-all i {
        font-size: 10px;
      }
      .contacts-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
      }
      .contact-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 16px;
        background: #f5f8fa;
        border-radius: 10px;
        border: 1px solid transparent;
        transition: all 0.2s;
      }
      .contact-item:hover {
        background: #fff;
        border-color: #e4e6ef;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
      }
      .contact-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e4e6ef;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 13px;
        color: #7e8299;
        flex-shrink: 0;
      }
      .contact-item:hover .contact-avatar {
        background: #f1faff;
        color: #009ef7;
      }
      .contact-body {
        flex: 1;
        min-width: 0;
      }
      .contact-name-row {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 2px;
        flex-wrap: wrap;
      }
      .contact-name {
        font-size: 13px;
        font-weight: 600;
        color: #181c32;
      }
      .contact-role {
        font-size: 11px;
        color: #a1a5b7;
        margin-bottom: 8px;
      }
      .contact-email,
      .contact-phone {
        font-size: 11px;
        color: #a1a5b7;
        margin-bottom: 2px;
        word-break: break-all;
      }
      .contact-actions-row {
        display: flex;
        gap: 6px;
        margin-left: auto;
        flex-shrink: 0;
      }
      .contact-btn {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        border: 1px solid #e4e6ef;
        background: #fff;
        color: #b5b5c3;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 11px;
      }
      .contact-btn:hover {
        border-color: #009ef7;
        color: #009ef7;
        background: #f1faff;
      }
      .contact-btn.copied {
        border-color: #50cd89;
        color: #50cd89;
        background: #e8fff3;
      }

      /* CONTACTS TABLE */
      .contacts-table-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #eff2f5;
        overflow: hidden;
      }
      .contacts-table-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 24px;
        border-bottom: 1px solid #eff2f5;
      }
      .contacts-table-title {
        font-size: 16px;
        font-weight: 700;
        color: #181c32;
      }
      .contacts-table-title span {
        color: #a1a5b7;
        font-weight: 500;
      }
      .contacts-table-subtitle {
        font-size: 12px;
        color: #a1a5b7;
        margin-top: 2px;
      }
      .contacts-table-actions {
        display: flex;
        align-items: center;
        gap: 10px;
      }
      .search-box {
        position: relative;
      }
      .search-box input {
        padding: 8px 14px 8px 36px;
        border: 1px solid #e4e6ef;
        border-radius: 8px;
        font-family: "Poppins", sans-serif;
        font-size: 13px;
        outline: none;
        width: 220px;
        transition: all 0.2s;
      }
      .search-box input:focus {
        border-color: #009ef7;
        box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.1);
      }
      .search-box i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #b5b5c3;
        font-size: 12px;
      }
      .table-wrapper {
        overflow-x: auto;
      }
      .table-wrapper::-webkit-scrollbar {
        height: 6px;
      }
      .table-wrapper::-webkit-scrollbar-track {
        background: transparent;
      }
      .table-wrapper::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 3px;
      }
      .data-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1000px;
      }
      .data-table thead tr {
        background: #f8f9fa;
      }
      .data-table th {
        padding: 12px 16px;
        text-align: left;
        font-size: 11px;
        font-weight: 600;
        color: #7e8299;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        white-space: nowrap;
      }
      .data-table th i {
        font-size: 9px;
        margin-left: 4px;
        color: #b5b5c3;
      }
      .data-table td {
        padding: 14px 16px;
        font-size: 13px;
        color: #3f4254;
        border-bottom: 1px solid #f5f8fa;
        vertical-align: middle;
      }
      .data-table tr:hover td {
        background: #fafbfc;
      }
      .data-table tr:last-child td {
        border-bottom: none;
      }
      .data-table th:first-child,
      .data-table td:first-child {
        padding-left: 24px;
        width: 40px;
      }
      .data-table th:last-child,
      .data-table td:last-child {
        padding-right: 24px;
      }
      .data-table th:nth-child(2),
      .data-table td:nth-child(2) {
        min-width: 180px;
      }
      .data-table th:nth-child(3),
      .data-table td:nth-child(3) {
        min-width: 160px;
      }
      .data-table th:nth-child(4),
      .data-table td:nth-child(4) {
        min-width: 100px;
      }
      .data-table th:nth-child(5),
      .data-table td:nth-child(5) {
        min-width: 180px;
      }
      .data-table th:nth-child(6),
      .data-table td:nth-child(6) {
        min-width: 140px;
      }
      .data-table th:nth-child(7),
      .data-table td:nth-child(7) {
        min-width: 120px;
      }
      .data-table th:nth-child(8),
      .data-table td:nth-child(8) {
        min-width: 100px;
      }
      .data-table th:nth-child(9),
      .data-table td:nth-child(9) {
        min-width: 100px;
        text-align: right;
      }
      .table-checkbox {
        width: 16px;
        height: 16px;
        border: 1px solid #e4e6ef;
        border-radius: 4px;
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
      }
      .table-checkbox:checked {
        background: #009ef7;
        border-color: #009ef7;
      }
      .contact-cell {
        display: flex;
        align-items: center;
        gap: 12px;
      }
      .contact-avatar-table {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
        color: #fff;
        flex-shrink: 0;
      }
      .contact-avatar-table.mg {
        background: #c084fc;
      }
      .contact-avatar-table.rk {
        background: #60a5fa;
      }
      .contact-avatar-table.ps {
        background: #fbbf24;
      }
      .contact-avatar-table.as {
        background: #34d399;
      }
      .contact-avatar-table.nk {
        background: #f472b6;
      }
      .contact-info-table {
        line-height: 1.4;
      }
      .contact-name-table {
        font-size: 14px;
        font-weight: 600;
        color: #181c32;
      }
      .contact-primary-badge {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        background: #e8fff3;
        color: #47be7d;
        font-size: 10px;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 50px;
        margin-top: 2px;
      }
      .contact-primary-badge i {
        font-size: 8px;
      }
      .dept-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f5f8fa;
        color: #5e6278;
        font-size: 12px;
        font-weight: 500;
        padding: 4px 10px;
        border-radius: 6px;
      }
      .email-cell {
        color: #3f4254;
        font-size: 13px;
      }
      .email-cell:hover {
        color: #009ef7;
        cursor: pointer;
      }
      .phone-cell {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        font-size: 13px;
        color: #3f4254;
        line-height: 1.5;
      }
      .phone-flag {
        width: 20px;
        height: 14px;
        border-radius: 2px;
        overflow: hidden;
        flex-shrink: 0;
        margin-top: 3px;
      }
      .phone-flag svg {
        width: 100%;
        height: 100%;
      }
      .phone-number {
        display: flex;
        flex-direction: column;
      }
      .phone-prefix {
        font-size: 12px;
        color: #7e8299;
      }
      .phone-digits {
        font-size: 13px;
        color: #3f4254;
        font-weight: 500;
      }
      .status-badge-table {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 50px;
      }
      .status-badge-table.active {
        background: #e8fff3;
        color: #47be7d;
      }
      .status-badge-table.inactive {
        background: #f5f8fa;
        color: #a1a5b7;
      }
      .status-badge-table i {
        font-size: 6px;
      }
      .action-btns {
        display: flex;
        align-items: center;
        gap: 6px;
        justify-content: flex-end;
      }
      .action-btn-table {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: 1px solid #e4e6ef;
        background: #fff;
        color: #7e8299;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 12px;
      }
      .action-btn-table:hover {
        border-color: #009ef7;
        color: #009ef7;
        background: #f1faff;
      }
      .action-btn-table.whatsapp:hover {
        border-color: #25d366;
        color: #25d366;
        background: #e8f5e9;
      }
      .action-btn-table.more {
        border: none;
        background: transparent;
        color: #b5b5c3;
        width: 24px;
      }
      .action-btn-table.more:hover {
        color: #5e6278;
        background: transparent;
      }
      .last-contacted {
        font-size: 13px;
        color: #7e8299;
      }
      .popup-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.35);
        z-index: 2000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        opacity: 0;
        visibility: hidden;
        transition: all 0.25s ease;
      }
      .popup-backdrop.show {
        opacity: 1;
        visibility: visible;
      }
      .popup-box {
        background: #fff;
        border-radius: 16px;
        width: 100%;
        max-width: 400px;
        padding: 32px;
        transform: scale(0.95) translateY(10px);
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
      }
      .popup-backdrop.show .popup-box {
        transform: scale(1) translateY(0);
      }
      .popup-head {
        text-align: center;
        margin-bottom: 24px;
      }
      .popup-icon {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: #f1faff;
        color: #009ef7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        margin: 0 auto 12px;
      }
      .popup-head h3 {
        font-size: 17px;
        font-weight: 700;
        color: #181c32;
        margin-bottom: 4px;
      }
      .popup-head p {
        font-size: 13px;
        color: #a1a5b7;
      }
      .popup-value {
        background: #f5f8fa;
        border-radius: 8px;
        padding: 16px;
        text-align: center;
        margin-bottom: 20px;
        font-size: 14px;
        font-weight: 600;
        color: #181c32;
        word-break: break-all;
      }
      .popup-buttons {
        display: flex;
        gap: 10px;
      }
      .popup-buttons .btn {
        flex: 1;
        justify-content: center;
      }
      .toast-stack {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 3000;
        display: flex;
        flex-direction: column;
        gap: 10px;
      }
      .toast-item {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        padding: 14px 18px;
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 300px;
        border-left: 3px solid #50cd89;
        animation: toastIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
      }
      @keyframes toastIn {
        from {
          opacity: 0;
          transform: translateX(40px);
        }
        to {
          opacity: 1;
          transform: translateX(0);
        }
      }
      .toast-icon-wrap {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #e8fff3;
        color: #50cd89;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        flex-shrink: 0;
      }
      .toast-body {
        flex: 1;
      }
      .toast-body strong {
        font-size: 13px;
        color: #181c32;
        display: block;
      }
      .toast-body span {
        font-size: 12px;
        color: #a1a5b7;
      }
      .toast-close-btn {
        background: none;
        border: none;
        color: #b5b5c3;
        cursor: pointer;
        font-size: 12px;
        padding: 4px;
      }
      .toast-close-btn:hover {
        color: #5e6278;
      }
      @media (max-width: 1200px) {
        .sidebar {
          transform: translateX(-100%);
          transition: transform 0.3s;
        }
        .sidebar.open {
          transform: translateX(0);
        }
        .modal-panel {
          max-width: 100%;
        }
        .content-grid {
          grid-template-columns: 1fr 1fr;
        }
      }
      @media (max-width: 992px) {
        .kpi-row {
          grid-template-columns: repeat(3, 1fr);
        }
        .content-grid {
          grid-template-columns: 1fr;
        }
        .contacts-table-header {
          flex-direction: column;
          align-items: flex-start;
          gap: 12px;
        }
        .data-table {
          overflow-x: auto;
          display: block;
        }
        .data-table thead {
          display: none;
        }
        .data-table tbody tr {
          display: block;
          margin-bottom: 16px;
          border: 1px solid #eff2f5;
          border-radius: 12px;
          padding: 16px;
        }
        .data-table td {
          display: flex;
          justify-content: space-between;
          padding: 8px 0;
          border: none;
        }
        .data-table td::before {
          content: attr(data-label);
          font-weight: 600;
          color: #7e8299;
          font-size: 11px;
        }
        .profile-top {
          flex-direction: column;
        }
        .profile-right {
          align-items: flex-start;
          margin-left: 0;
          width: 100%;
        }
        .action-buttons {
          justify-content: flex-start;
        }
      }
      @media (max-width: 768px) {
        .modal-inner {
          padding: 16px 20px;
        }
        .main-content {
          padding: 16px 20px;
        }
        .kpi-row {
          grid-template-columns: repeat(2, 1fr);
        }
        .profile-left {
          flex-direction: column;
        }
      }
      @media (max-width: 480px) {
        .kpi-row {
          grid-template-columns: 1fr;
        }
        .action-buttons .btn {
          flex: 1;
          justify-content: center;
        }
      }
    </style>


 
    <div class="modal-overlay show" id="agencyModal">
      <div class="modal-panel">
        <div class="modal-inner">
          <input type="hidden" name="hid_client_id" id="hid_client_id" value="{{ $directClientDetail->client_id }}" />
          <div class="profile-card">
            <div class="profile-top">
              <div class="profile-left">
                <div class="logo-wrap">
                  <div class="logo-circle">
                    <svg viewBox="0 0 100 100">
                      <defs>
                        <linearGradient
                          id="hex"
                          x1="0%"
                          y1="0%"
                          x2="100%"
                          y2="100%"
                        >
                          <stop offset="0%" style="stop-color: #1e293b" />
                          <stop offset="100%" style="stop-color: #334155" />
                        </linearGradient>
                        <linearGradient
                          id="bee"
                          x1="0%"
                          y1="0%"
                          x2="100%"
                          y2="100%"
                        >
                          <stop offset="0%" style="stop-color: #fbbf24" />
                          <stop offset="100%" style="stop-color: #f59e0b" />
                        </linearGradient>
                      </defs>
                      <path
                        d="M50 8 L88 28 L88 72 L50 92 L12 72 L12 28 Z"
                        fill="url(#hex)"
                        stroke="#475569"
                        stroke-width="1"
                      />
                      <ellipse
                        cx="50"
                        cy="52"
                        rx="16"
                        ry="20"
                        fill="url(#bee)"
                      />
                      <rect
                        x="37"
                        y="40"
                        width="26"
                        height="3.5"
                        fill="#1e293b"
                        rx="1"
                      />
                      <rect
                        x="35"
                        y="50"
                        width="30"
                        height="3.5"
                        fill="#1e293b"
                        rx="1"
                      />
                      <rect
                        x="37"
                        y="60"
                        width="26"
                        height="3.5"
                        fill="#1e293b"
                        rx="1"
                      />
                      <ellipse
                        cx="36"
                        cy="34"
                        rx="10"
                        ry="7"
                        fill="rgba(255,255,255,0.25)"
                        stroke="rgba(255,255,255,0.4)"
                        stroke-width="0.8"
                      />
                      <ellipse
                        cx="64"
                        cy="34"
                        rx="10"
                        ry="7"
                        fill="rgba(255,255,255,0.25)"
                        stroke="rgba(255,255,255,0.4)"
                        stroke-width="0.8"
                      />
                      <line
                        x1="44"
                        y1="30"
                        x2="40"
                        y2="20"
                        stroke="#1e293b"
                        stroke-width="1.5"
                        stroke-linecap="round"
                      />
                      <line
                        x1="56"
                        y1="30"
                        x2="60"
                        y2="20"
                        stroke="#1e293b"
                        stroke-width="1.5"
                        stroke-linecap="round"
                      />
                      <circle cx="40" cy="20" r="1.8" fill="#1e293b" />
                      <circle cx="60" cy="20" r="1.8" fill="#1e293b" />
                      <text
                        x="50"
                        y="84"
                        text-anchor="middle"
                        fill="white"
                        font-size="7"
                        font-weight="700"
                        font-family="Poppins, sans-serif"
                      >
                        HireBee
                      </text>
                      <text
                        x="50"
                        y="91"
                        text-anchor="middle"
                        fill="rgba(255,255,255,0.6)"
                        font-size="4.5"
                        font-family="Poppins, sans-serif"
                        letter-spacing="0.5"
                      >
                        RESOURCING
                      </text>
                    </svg>
                  </div>
                  <div class="verified-check"><i class="fas fa-check"></i></div>
                </div>
                <div class="profile-info">
                  <div class="profile-name-row">
                    <span class="profile-name">{{ ucwords(@$directClientDetail->name) }}</span>
                    <span class="verified-badge"
                      >Verified <i class="fas fa-check"></i
                    ></span>
                  </div>
                  <div class="profile-meta">
                    <span class="meta-item"
                      ><i class="fas fa-briefcase"></i>{{ $industries_name }}
                    </span>
                    
                    <span class="meta-divider">|</span>
                    <span class="meta-item"
                      ><i class="fas fa-map-marker-alt"></i>{{ ucwords($city_name) }},
                      {{ ucwords($country_name) }}</span>
                    <span class="meta-divider">|</span>
                    <a
                      href="{{ @$directClientDetail->website }}"
                      target="_blank"
                      class="meta-link"
                      ><i class="fas fa-globe"></i>{{ @$directClientDetail->website }}<i
                        class="fas fa-external-link-alt"
                      ></i
                    ></a>
                  </div>
                  <div class="service-tags">
                    @foreach ($tags as $key => $val)
                        <span class="tag">
                            {{ ucwords($val) }}
                        </span>
                    @endforeach
                    
                  </div>
                </div>
              </div>
              <div class="profile-right">
                <div class="status-row">
                  <span class="status-badge"
                    ><span class="status-dot"></span>Active</span
                  >
                </div>
              </div>
            </div>
          </div>
          <div class="kpi-row">
            <div class="kpi-box">
              <div class="kpi-header">
                <div class="kpi-icon-box kpi-icon-blue">
                  <i class="fas fa-file-alt"></i>
                </div>
                <span class="kpi-label">Active Jobs</span>
              </div>
              <div class="kpi-number" id="kpiActiveJobs">{{ $Active }}</div>
            </div>
            <div class="kpi-box">
              <div class="kpi-header">
                <div class="kpi-icon-box kpi-icon-green">
                  <i class="fas fa-id-badge"></i>
                </div>
                <span class="kpi-label">Candidates Submitted</span>
              </div>
              <div class="kpi-number" id="kpiCandidatesSubmitted">{{ $all_candidates }}</div>
            </div>
            <div class="kpi-box">
              <div class="kpi-header">
                <div class="kpi-icon-box kpi-icon-purple">
                  <i class="fas fa-people-arrows"></i>
                </div>
                <span class="kpi-label">Interviews Scheduled</span>
              </div>
              <div class="kpi-number" id="kpiInterviewsScheduled">{{ $all_interview }}</div>
            </div>
            <div class="kpi-box">
              <div class="kpi-header">
                <div class="kpi-icon-box kpi-icon-orange">
                  <i class="fas fa-gift"></i>
                </div>
                <span class="kpi-label">Offers Generated</span>
              </div>
              <div class="kpi-number" id="kpiOffersGenerated">{{ $offers }}</div>
            </div>
            <div class="kpi-box">
              <div class="kpi-header">
                <div class="kpi-icon-box kpi-icon-teal">
                  <i class="fas fa-calendar-check"></i>
                </div>
                <span class="kpi-label">Successful Joins</span>
              </div>
              <div class="kpi-number" id="kpiSuccessfulJoins">{{ $Joined }}</div>
            </div>
          </div>
          <div class="tabs-bar">
            <div class="tabs-inner">
              <button class="tab active" onclick="switchTab(this, 'overview')">
                Overview
              </button>
              <button class="tab" onclick="switchTab(this, 'contacts')">
                Contacts
              </button>
              <button class="tab" onclick="switchTab(this, 'jobs')">
                Jobs <span class="tab-count" id="tabCountJobs">{{ $Active }}</span>
              </button>
              <button class="tab" onclick="switchTab(this, 'candidates')">
                Candidates <span class="tab-count" id="tabCountCandidates">{{ $all_candidates }}</span>
              </button>
              <button class="tab" onclick="switchTab(this, 'interviews')">
                Interview Tracker <span class="tab-count" id="tabCountInterviews">{{ $all_interview }}</span>
              </button>
              <button class="tab" onclick="switchTab(this, 'joinings')">
                Joinings <span class="tab-count" id="tabCountJoinings">{{ $Joined }}</span>
              </button>
              <button class="tab" onclick="switchTab(this, 'reports')">
                Candidate Dashboard
              </button>
            </div>
          </div>
          <div id="tab-overview" class="tab-content active">
            <div class="content-grid">
              <div style="display: flex; flex-direction: column; gap: 16px">
                <div class="content-card">
                  <div class="card-header-side">
                    <div class="card-header-icon">
                      <i class="fas fa-info-circle"></i>
                    </div>
                    <h3 class="card-header-title">About the Agency</h3>
                  </div>
                  <div class="about-text" id="aboutText">
                    @php
                        $description = '';
                        $description = @$directClientDetail->description
                            ? @$directClientDetail->description
                            : @$directClientDetail->companies_description;
                    @endphp
                    {{ $description }}
                  </div>
                </div>
              </div>
              <div style="display: flex; flex-direction: column; gap: 16px">
                <div class="content-card">
                  <div class="card-header-side">
                    <div class="card-header-icon">
                      <i class="fas fa-building"></i>
                    </div>
                    <h3 class="card-header-title">Agency Information</h3>
                  </div>
                  <div class="info-list">
                    <div class="info-row">
                      <span class="info-label">Headquarters</span
                      ><span class="info-value">{{ ucwords($city_name) }}, {{ ucwords($country_name) }}</span>
                    </div>
                    <div class="info-row">
                      <span class="info-label">Website</span>
                      <span class="info-value">
                        <a href="{{ @$directClientDetail->website }}" target="_blank">{{ @$directClientDetail->website }}
                          <i class="fas fa-external-link-alt"></i></a>
                      </span>
                    </div>
                    <div class="info-row">
                      <span class="info-label">Email</span
                      ><span class="info-value"
                        ><a href="mailto:{{ @$directClientDetail->email }}"
                          >{{ @$directClientDetail->email }}</a
                        ></span
                      >
                    </div>
                    <div class="info-row">
                      <span class="info-label">Vendor ID</span
                      ><span class="info-value">VEN-{{  @$directClientDetail->id }}</span>
                    </div>
                    <div class="info-row">
                      <span class="info-label">Founded</span
                      ><span class="info-value">{{ @$directClientDetail->founded }}</span>
                    </div>
                    <div class="info-row">
                      <span class="info-label">Number of Employees</span>
                      @if(@$directClientDetail->company_size_from>=10000)
                        <span class="info-value">{{ number_format(@$directClientDetail->company_size_from) }}+
                        </span>
                      @else
                        <span class="info-value">{{ number_format(@$directClientDetail->company_size_from) }} – {{ number_format(@$directClientDetail->company_sizes) }}
                        </span>
                      @endif
                    </div>
                    <div class="info-row">
                      <span class="info-label">Connected On</span
                      ><span class="info-value">{{ $newDate }}</span>
                    </div>

                  </div>
                </div>

                @php
                   
                    $rates = '';
                    $currency = GeneralMethod::currency();
                    $salaryMode = GeneralMethod::salaryMode();
                    $currencySymbole = GeneralMethod::currencySymbole();
                    $minimumContractRates = DB::table('commericials')
                        ->join('commericial_details', 'commericial_details.commericial_id', '=', 'commericials.id')
                        ->where('commericials.commtype', '=', 1)
                        ->where('commericials.company_id', '=', $directClientDetail->company_id)
                        // Intent: show only commercial rates covered by an active global experience band.
                        ->whereExists(function ($experienceBands) {
                            $experienceBands->selectRaw('1')
                                ->from('commercial_experience_bands as experience_bands')
                                ->where('experience_bands.is_active', 1)
                                ->whereColumn('commericial_details.exp', '>=', 'experience_bands.min_experience')
                                ->whereColumn('commericial_details.exp', '<=', 'experience_bands.max_experience');
                        })
                        ->min('commericial_details.rate');
                    $minimumFulltimeRates = DB::table('commericials')
                        ->join('commericial_details', 'commericial_details.commericial_id', '=', 'commericials.id')
                        ->where('commericials.commtype', '=', 2)
                        ->where('commericials.company_id', '=', $directClientDetail->company_id)
                        // Intent: show only commercial rates covered by an active global experience band.
                        ->whereExists(function ($experienceBands) {
                            $experienceBands->selectRaw('1')
                                ->from('commercial_experience_bands as experience_bands')
                                ->where('experience_bands.is_active', 1)
                                ->whereColumn('commericial_details.exp', '>=', 'experience_bands.min_experience')
                                ->whereColumn('commericial_details.exp', '<=', 'experience_bands.max_experience');
                        })
                        ->min('commericial_details.rate');

                    $minimumContractType = DB::table('commericials')
                        ->select('commericials.*')
                        ->where('commericials.commtype', '=', 1)
                        ->where('commericials.company_id', '=', $directClientDetail->company_id)
                        ->first();
                    $minimumFulltimeType = DB::table('commericials')
                        ->select('commericials.*')
                        ->where('commericials.commtype', '=', 2)
                        ->where('commericials.company_id', '=', $directClientDetail->company_id)
                        ->first();
                    // sal_mode /salary_currency
                    $jobSalaryCurrency = (@$minimumContractType->salary_currency) ? @$minimumContractType->salary_currency : 0;
                    $jobSalaryMode = (@$minimumContractType->sal_mode) ? @$minimumContractType->sal_mode : 0;
                   
                        
                            $current_cur = $currencySymbole[@$jobSalaryCurrency];
                            $current_mode = $salaryMode[@$jobSalaryMode];
                            $currencymain = GeneralMethod::currency(); // Currency code
                    
                @endphp

                
                <div class="content-card">
                  <div class="card-header-side">
                    <div class="card-header-icon">
                      <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h3 class="card-header-title">Commercials</h3>
                  </div>
                  <div class="commercial-item">
                    <div class="commercial-icon purple">
                      <i class="fas fa-user-tie"></i>
                    </div>
                    <div>
                      <div class="commercial-title">Fulltime Hiring Services </div>
                      <div class="commercial-desc">
                        {{ @$minimumFulltimeRates }}% of the CTC * One time fee
                      </div>
                    </div>
                  </div>
                  <div class="commercial-item">
                    <div class="commercial-icon orange">
                      <i class="fas fa-clock"></i>
                    </div>
                    <div>
                      <div class="commercial-title">
                        Contract Hiring Services
                      </div>
                      <div class="commercial-desc">{{ $current_cur }} {{ $minimumContractRates }} / {{ $current_mode }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="contacts-card">
              <div class="contacts-header">
                <h3 class="contacts-title">Key Contacts (3)</h3>
                <a
                  href="#"
                  class="view-all tab"
                  onclick="switchTab(this, 'contacts')"
                  >View All Contacts <i class="fas fa-chevron-right"></i
                ></a>
              </div>
              <div class="contacts-grid">
                <div class="contact-item">
                  <div class="contact-avatar">SJ</div>
                  <div class="contact-body">
                    <div class="contact-name-row">
                      <span class="contact-name">Sarah Jones</span
                      ><span
                        class="badge badge-purple"
                        style="padding: 2px 8px; font-size: 10px"
                        >Primary</span
                      >
                    </div>
                    <div class="contact-role">Account Manager</div>
                    <div class="contact-email">sarah.jones@hirebee.co.in</div>
                    <div class="contact-phone">+91 98765 43210</div>
                  </div>
                  <div class="contact-actions-row">
                    <button
                      class="contact-btn"
                      onclick="
                        openPopup(
                          'email',
                          'sarah.jones@hirebee.co.in',
                          'Sarah Jones',
                        )
                      "
                      title="Email"
                    >
                      <i class="fas fa-envelope"></i>
                    </button>
                    <button
                      class="contact-btn"
                      onclick="
                        openPopup('phone', '+91 98765 43210', 'Sarah Jones')
                      "
                      title="Call"
                    >
                      <i class="fas fa-phone"></i>
                    </button>
                    <button
                      class="contact-btn"
                      onclick="
                        copyToClipboard(this, 'sarah.jones@hirebee.co.in')
                      "
                      title="Copy"
                    >
                      <i class="fas fa-copy"></i>
                    </button>
                  </div>
                </div>
                <div class="contact-item">
                  <div class="contact-avatar">MB</div>
                  <div class="contact-body">
                    <div class="contact-name-row">
                      <span class="contact-name">Michael Brown</span>
                    </div>
                    <div class="contact-role">Delivery Manager</div>
                    <div class="contact-email">michael.brown@hirebee.co.in</div>
                    <div class="contact-phone">+91 98123 45678</div>
                  </div>
                  <div class="contact-actions-row">
                    <button
                      class="contact-btn"
                      onclick="
                        openPopup(
                          'email',
                          'michael.brown@hirebee.co.in',
                          'Michael Brown',
                        )
                      "
                      title="Email"
                    >
                      <i class="fas fa-envelope"></i>
                    </button>
                    <button
                      class="contact-btn"
                      onclick="
                        openPopup('phone', '+91 98123 45678', 'Michael Brown')
                      "
                      title="Call"
                    >
                      <i class="fas fa-phone"></i>
                    </button>
                    <button
                      class="contact-btn"
                      onclick="
                        copyToClipboard(this, 'michael.brown@hirebee.co.in')
                      "
                      title="Copy"
                    >
                      <i class="fas fa-copy"></i>
                    </button>
                  </div>
                </div>
                <div class="contact-item">
                  <div class="contact-avatar">PS</div>
                  <div class="contact-body">
                    <div class="contact-name-row">
                      <span class="contact-name">Priya Sharma</span>
                    </div>
                    <div class="contact-role">Recruitment Lead</div>
                    <div class="contact-email">priya.sharma@hirebee.co.in</div>
                    <div class="contact-phone">+91 91234 56789</div>
                  </div>
                  <div class="contact-actions-row">
                    <button
                      class="contact-btn"
                      onclick="
                        openPopup(
                          'email',
                          'priya.sharma@hirebee.co.in',
                          'Priya Sharma',
                        )
                      "
                      title="Email"
                    >
                      <i class="fas fa-envelope"></i>
                    </button>
                    <button
                      class="contact-btn"
                      onclick="
                        openPopup('phone', '+91 91234 56789', 'Priya Sharma')
                      "
                      title="Call"
                    >
                      <i class="fas fa-phone"></i>
                    </button>
                    <button
                      class="contact-btn"
                      onclick="
                        copyToClipboard(this, 'priya.sharma@hirebee.co.in')
                      "
                      title="Copy"
                    >
                      <i class="fas fa-copy"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div id="tab-contacts" class="tab-content">
            <div class="contacts-table-card">
              <div class="contacts-table-header">
                <div>
                  <div class="contacts-table-title">
                    Contacts <span>(5)</span>
                  </div>
                  <div class="contacts-table-subtitle">
                    Manage and view all contacts associated with this agency.
                  </div>
                </div>
                <div class="contacts-table-actions">
                  <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search contacts..." />
                  </div>
                  <button class="btn btn-outline">
                    <i class="fas fa-filter"></i>Filters
                  </button>
                  <button class="btn btn-primary">
                    <i class="fas fa-plus"></i>Add Contact
                  </button>
                </div>
              </div>
              <div class="table-wrapper">
                <table class="data-table">
                  <thead>
                    <tr>
                      <th><input type="checkbox" class="table-checkbox" /></th>
                      <th>Contact <i class="fas fa-sort"></i></th>
                      <th>Designation <i class="fas fa-sort"></i></th>
                      <th>Department <i class="fas fa-sort"></i></th>
                      <th>Email <i class="fas fa-sort"></i></th>
                      <th>Phone <i class="fas fa-sort"></i></th>
                      <th>Last Contacted <i class="fas fa-sort"></i></th>
                      <th>Status <i class="fas fa-sort"></i></th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><input type="checkbox" class="table-checkbox" /></td>
                      <td>
                        <div class="contact-cell">
                          <div class="contact-avatar-table mg">MG</div>
                          <div class="contact-info-table">
                            <div class="contact-name-table">Mahima Gupta</div>
                            <div class="contact-primary-badge">
                              <i class="fas fa-check"></i>Primary
                            </div>
                          </div>
                        </div>
                      </td>
                      <td>Director - Talent Acquisition</td>
                      <td><span class="dept-badge">HR</span></td>
                      <td class="email-cell">mahima@hirebee.co.in</td>
                      <td>
                        <div class="phone-cell">
                          <div class="phone-flag">
                            <svg viewBox="0 0 36 24">
                              <rect width="36" height="8" fill="#FF9932" />
                              <rect y="8" width="36" height="8" fill="#fff" />
                              <rect
                                y="16"
                                width="36"
                                height="8"
                                fill="#138808"
                              />
                              <circle
                                cx="18"
                                cy="12"
                                r="3"
                                fill="#000080"
                                opacity="0.3"
                              />
                            </svg>
                          </div>
                          <div class="phone-number">
                            <span class="phone-prefix">+91</span>
                            <span class="phone-digits">9818546268</span>
                          </div>
                        </div>
                      </td>
                      <td class="last-contacted">2 days ago</td>
                      <td>
                        <span class="status-badge-table active"
                          ><i class="fas fa-circle"></i>Active</span
                        >
                      </td>
                      <td>
                        <div class="action-btns">
                          <button
                            class="action-btn-table"
                            onclick="
                              openPopup(
                                'phone',
                                '+91 9818546268',
                                'Mahima Gupta',
                              )
                            "
                            title="Call"
                          >
                            <i class="fas fa-phone"></i>
                          </button>
                          <button
                            class="action-btn-table"
                            onclick="
                              openPopup(
                                'email',
                                'mahima@hirebee.co.in',
                                'Mahima Gupta',
                              )
                            "
                            title="Email"
                          >
                            <i class="fas fa-envelope"></i>
                          </button>
                          <button
                            class="action-btn-table whatsapp"
                            onclick="
                              window.open(
                                'https://wa.me/919818546268',
                                '_blank',
                              )
                            "
                            title="WhatsApp"
                          >
                            <i class="fab fa-whatsapp"></i>
                          </button>
                          <button class="action-btn-table more" title="More">
                            <i class="fas fa-ellipsis-v"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td><input type="checkbox" class="table-checkbox" /></td>
                      <td>
                        <div class="contact-cell">
                          <div class="contact-avatar-table rk">RK</div>
                          <div class="contact-info-table">
                            <div class="contact-name-table">Rohit Kumar</div>
                          </div>
                        </div>
                      </td>
                      <td>Senior Manager - Recruitment</td>
                      <td><span class="dept-badge">Recruitment</span></td>
                      <td class="email-cell">rohit.kumar@hirebee.co.in</td>
                      <td>
                        <div class="phone-cell">
                          <div class="phone-flag">
                            <svg viewBox="0 0 36 24">
                              <rect width="36" height="8" fill="#FF9932" />
                              <rect y="8" width="36" height="8" fill="#fff" />
                              <rect
                                y="16"
                                width="36"
                                height="8"
                                fill="#138808"
                              />
                              <circle
                                cx="18"
                                cy="12"
                                r="3"
                                fill="#000080"
                                opacity="0.3"
                              />
                            </svg>
                          </div>
                          <div class="phone-number">
                            <span class="phone-prefix">+91</span>
                            <span class="phone-digits">9876543210</span>
                          </div>
                        </div>
                      </td>
                      <td class="last-contacted">7 days ago</td>
                      <td>
                        <span class="status-badge-table active"
                          ><i class="fas fa-circle"></i>Active</span
                        >
                      </td>
                      <td>
                        <div class="action-btns">
                          <button
                            class="action-btn-table"
                            onclick="
                              openPopup(
                                'phone',
                                '+91 9876543210',
                                'Rohit Kumar',
                              )
                            "
                            title="Call"
                          >
                            <i class="fas fa-phone"></i>
                          </button>
                          <button
                            class="action-btn-table"
                            onclick="
                              openPopup(
                                'email',
                                'rohit.kumar@hirebee.co.in',
                                'Rohit Kumar',
                              )
                            "
                            title="Email"
                          >
                            <i class="fas fa-envelope"></i>
                          </button>
                          <button
                            class="action-btn-table whatsapp"
                            onclick="
                              window.open(
                                'https://wa.me/919876543210',
                                '_blank',
                              )
                            "
                            title="WhatsApp"
                          >
                            <i class="fab fa-whatsapp"></i>
                          </button>
                          <button class="action-btn-table more" title="More">
                            <i class="fas fa-ellipsis-v"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td><input type="checkbox" class="table-checkbox" /></td>
                      <td>
                        <div class="contact-cell">
                          <div class="contact-avatar-table ps">PS</div>
                          <div class="contact-info-table">
                            <div class="contact-name-table">Priya Sharma</div>
                          </div>
                        </div>
                      </td>
                      <td>Recruitment Manager</td>
                      <td><span class="dept-badge">Recruitment</span></td>
                      <td class="email-cell">priya.sharma@hirebee.co.in</td>
                      <td>
                        <div class="phone-cell">
                          <div class="phone-flag">
                            <svg viewBox="0 0 36 24">
                              <rect width="36" height="8" fill="#FF9932" />
                              <rect y="8" width="36" height="8" fill="#fff" />
                              <rect
                                y="16"
                                width="36"
                                height="8"
                                fill="#138808"
                              />
                              <circle
                                cx="18"
                                cy="12"
                                r="3"
                                fill="#000080"
                                opacity="0.3"
                              />
                            </svg>
                          </div>
                          <div class="phone-number">
                            <span class="phone-prefix">+91</span>
                            <span class="phone-digits">9712345678</span>
                          </div>
                        </div>
                      </td>
                      <td class="last-contacted">15 days ago</td>
                      <td>
                        <span class="status-badge-table active"
                          ><i class="fas fa-circle"></i>Active</span
                        >
                      </td>
                      <td>
                        <div class="action-btns">
                          <button
                            class="action-btn-table"
                            onclick="
                              openPopup(
                                'phone',
                                '+91 9712345678',
                                'Priya Sharma',
                              )
                            "
                            title="Call"
                          >
                            <i class="fas fa-phone"></i>
                          </button>
                          <button
                            class="action-btn-table"
                            onclick="
                              openPopup(
                                'email',
                                'priya.sharma@hirebee.co.in',
                                'Priya Sharma',
                              )
                            "
                            title="Email"
                          >
                            <i class="fas fa-envelope"></i>
                          </button>
                          <button
                            class="action-btn-table whatsapp"
                            onclick="
                              window.open(
                                'https://wa.me/919712345678',
                                '_blank',
                              )
                            "
                            title="WhatsApp"
                          >
                            <i class="fab fa-whatsapp"></i>
                          </button>
                          <button class="action-btn-table more" title="More">
                            <i class="fas fa-ellipsis-v"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td><input type="checkbox" class="table-checkbox" /></td>
                      <td>
                        <div class="contact-cell">
                          <div class="contact-avatar-table as">AS</div>
                          <div class="contact-info-table">
                            <div class="contact-name-table">Amit Singh</div>
                          </div>
                        </div>
                      </td>
                      <td>Senior Recruiter</td>
                      <td><span class="dept-badge">Recruitment</span></td>
                      <td class="email-cell">amit.singh@hirebee.co.in</td>
                      <td>
                        <div class="phone-cell">
                          <div class="phone-flag">
                            <svg viewBox="0 0 36 24">
                              <rect width="36" height="8" fill="#FF9932" />
                              <rect y="8" width="36" height="8" fill="#fff" />
                              <rect
                                y="16"
                                width="36"
                                height="8"
                                fill="#138808"
                              />
                              <circle
                                cx="18"
                                cy="12"
                                r="3"
                                fill="#000080"
                                opacity="0.3"
                              />
                            </svg>
                          </div>
                          <div class="phone-number">
                            <span class="phone-prefix">+91</span>
                            <span class="phone-digits">9898989898</span>
                          </div>
                        </div>
                      </td>
                      <td class="last-contacted">1 month ago</td>
                      <td>
                        <span class="status-badge-table inactive"
                          ><i class="fas fa-circle"></i>Inactive</span
                        >
                      </td>
                      <td>
                        <div class="action-btns">
                          <button
                            class="action-btn-table"
                            onclick="
                              openPopup('phone', '+91 9898989898', 'Amit Singh')
                            "
                            title="Call"
                          >
                            <i class="fas fa-phone"></i>
                          </button>
                          <button
                            class="action-btn-table"
                            onclick="
                              openPopup(
                                'email',
                                'amit.singh@hirebee.co.in',
                                'Amit Singh',
                              )
                            "
                            title="Email"
                          >
                            <i class="fas fa-envelope"></i>
                          </button>
                          <button
                            class="action-btn-table whatsapp"
                            onclick="
                              window.open(
                                'https://wa.me/919898989898',
                                '_blank',
                              )
                            "
                            title="WhatsApp"
                          >
                            <i class="fab fa-whatsapp"></i>
                          </button>
                          <button class="action-btn-table more" title="More">
                            <i class="fas fa-ellipsis-v"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td><input type="checkbox" class="table-checkbox" /></td>
                      <td>
                        <div class="contact-cell">
                          <div class="contact-avatar-table nk">NK</div>
                          <div class="contact-info-table">
                            <div class="contact-name-table">Neha Kapoor</div>
                          </div>
                        </div>
                      </td>
                      <td>Recruiter</td>
                      <td><span class="dept-badge">Recruitment</span></td>
                      <td class="email-cell">neha.kapoor@hirebee.co.in</td>
                      <td>
                        <div class="phone-cell">
                          <div class="phone-flag">
                            <svg viewBox="0 0 36 24">
                              <rect width="36" height="8" fill="#FF9932" />
                              <rect y="8" width="36" height="8" fill="#fff" />
                              <rect
                                y="16"
                                width="36"
                                height="8"
                                fill="#138808"
                              />
                              <circle
                                cx="18"
                                cy="12"
                                r="3"
                                fill="#000080"
                                opacity="0.3"
                              />
                            </svg>
                          </div>
                          <div class="phone-number">
                            <span class="phone-prefix">+91</span>
                            <span class="phone-digits">9999999999</span>
                          </div>
                        </div>
                      </td>
                      <td class="last-contacted">2 months ago</td>
                      <td>
                        <span class="status-badge-table active"
                          ><i class="fas fa-circle"></i>Active</span
                        >
                      </td>
                      <td>
                        <div class="action-btns">
                          <button
                            class="action-btn-table"
                            onclick="
                              openPopup(
                                'phone',
                                '+91 9999999999',
                                'Neha Kapoor',
                              )
                            "
                            title="Call"
                          >
                            <i class="fas fa-phone"></i>
                          </button>
                          <button
                            class="action-btn-table"
                            onclick="
                              openPopup(
                                'email',
                                'neha.kapoor@hirebee.co.in',
                                'Neha Kapoor',
                              )
                            "
                            title="Email"
                          >
                            <i class="fas fa-envelope"></i>
                          </button>
                          <button
                            class="action-btn-table whatsapp"
                            onclick="
                              window.open(
                                'https://wa.me/919999999999',
                                '_blank',
                              )
                            "
                            title="WhatsApp"
                          >
                            <i class="fab fa-whatsapp"></i>
                          </button>
                          <button class="action-btn-table more" title="More">
                            <i class="fas fa-ellipsis-v"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <!--begin::Jobs tab-->
          <div id="tab-jobs" class="tab-content">
            @if (in_array('view_jobs', $userPermissions))
            <div class="table-card">
              <!-- Column header -->
              <div class="table-header">
                <div class="th">Job Details</div>
                <div class="th">
                  Experience
                  <svg width="11" height="11" fill="none" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M3 9h18M8 2v4M16 2v4" stroke="currentColor" stroke-width="1.8"/>
                  </svg>
                </div>
                <div class="th">Hiring &amp; Location</div>
                <div class="th">Hiring Manager</div>
                <div class="th">Team</div>
                <div class="th">Agency</div>
                <div class="th">
                  Hiring Pipeline
                  <svg width="11" height="11" fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M12 16v-4M12 8h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                  </svg>
                </div>
              </div>
              <!-- Rows (AJAX) -->
              <div class="loadingMessage loadingMessagestaffingpartnerjob" style="display: none; position: absolute;">Processing...</div>
              <div id="staffingpartnerjob"></div>
            </div>
            <div id="paginationstaffingpartnerjob" style="padding: 12px 0;"></div>
            @endif
          </div>
          <!--end::Jobs tab-->

          <!--begin::Candidates tab-->
          <div id="tab-candidates" class="tab-content">
            <div class="contacts-table-card">
              <div class="contacts-table-header">
                <div>
                  <div class="contacts-table-title">Candidates</div>
                  <div class="contacts-table-subtitle">
                    Candidates submitted by this agency.
                  </div>
                </div>
              </div>
              <div class="table-wrapper" style="padding: 8px 24px 24px;">
                <div class="loadingMessage loadingMessagecandidatetList" style="display: none; position: absolute;">Processing...</div>
                <table class="table align-middle table-row-dashed fs-6 gy-5">
                  <thead>
                    <tr class="fw-bolder text-muted">
                      <th class="min-w-150px">Name</th>
                      <th class="min-w-15px">Exp.</th>
                      <th class="min-w-15px">Salary</th>
                      <th class="min-w-15px">Notice Period</th>
                      <th class="min-w-15px">Location</th>
                      <th class="min-w-15px">Source</th>
                      <th class="min-w-15px">Upload Date</th>
                      <th class="min-w-15px">Jobs</th>
                      <th class="min-w-15px">Stage</th>
                    </tr>
                  </thead>
                  <tbody id="candidatetList"></tbody>
                </table>
                <div id="paginationcandidate"></div>
              </div>
            </div>
          </div>
          <!--end::Candidates tab-->

          <!--begin::Interview Tracker tab-->
          <div id="tab-interviews" class="tab-content">
            <div class="contacts-table-card">
              <div class="contacts-table-header">
                <div>
                  <div class="contacts-table-title">Interview Tracker</div>
                  <div class="contacts-table-subtitle">
                    Track interviews scheduled with this agency's candidates.
                  </div>
                </div>
              </div>
              <div class="table-wrapper" style="padding: 8px 24px 24px;">
                <div class="loadingMessage loadingMessagecandidatetListinterviewtracker" style="display: none; position: absolute;">Processing...</div>
                <table class="table align-middle table-row-dashed fs-6 gy-5">
                  <thead>
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
                  </thead>
                  <tbody id="candidatetListinterviewtracker"></tbody>
                </table>
                <div id="paginationcandidateinterviewtracker"></div>
              </div>
            </div>
          </div>
          <!--end::Interview Tracker tab-->

          <!--begin::Joinings tab-->
          <div id="tab-joinings" class="tab-content">
            <div class="contacts-table-card">
              <div class="contacts-table-header">
                <div>
                  <div class="contacts-table-title">Joinings</div>
                  <div class="contacts-table-subtitle">
                    Candidates who have joined through this agency.
                  </div>
                </div>
              </div>
              <div class="table-wrapper" style="padding: 8px 24px 24px;">
                <div class="loadingMessage loadingMessagecandidatetListJoin" style="display: none; position: absolute;">Processing...</div>
                <table class="table align-middle table-row-dashed fs-6 gy-5">
                  <thead>
                    <tr class="fw-bolder text-muted">
                      <th class="min-w-150px">Name</th>
                      <th class="min-w-15px">Exp.</th>
                      <th class="min-w-15px">Salary</th>
                      <th class="min-w-15px">Notice Period</th>
                      <th class="min-w-15px">Location</th>
                      <th class="min-w-15px">Source</th>
                      <th class="min-w-15px">Upload Date</th>
                      <th class="min-w-15px">Jobs</th>
                      <th class="min-w-15px">Stage</th>
                    </tr>
                  </thead>
                  <tbody id="candidatetListJoin"></tbody>
                </table>
                <div id="paginationcandidatejoin"></div>
              </div>
            </div>
          </div>
          <!--end::Joinings tab-->

          <!--begin::Candidate Dashboard tab-->
          <div id="tab-reports" class="tab-content">
            <style>
              #tab-reports .dash-wrap { display: flex; flex-direction: column; gap: 16px; }
              #tab-reports .dash-grid { display: grid; gap: 16px; }
              #tab-reports .dash-grid-2 { grid-template-columns: 1fr 1fr; }
              #tab-reports .dash-grid-13 { grid-template-columns: 1fr 2fr; }
              #tab-reports .dash-card {
                background: #fff; border: 1px solid #eff2f5; border-radius: 12px;
                padding: 20px; display: flex; flex-direction: column;
              }
              #tab-reports .dash-head { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
              #tab-reports .dash-ic {
                width: 42px; height: 42px; border-radius: 10px; flex-shrink: 0;
                display: flex; align-items: center; justify-content: center; font-size: 16px;
              }
              #tab-reports .ic-blue { background: #f1faff; color: #009ef7; }
              #tab-reports .ic-green { background: #e8fff3; color: #50cd89; }
              #tab-reports .ic-purple { background: #f8f5ff; color: #7239ea; }
              #tab-reports .ic-orange { background: #fff8dd; color: #f6a723; }
              #tab-reports .ic-teal { background: #f0fdfa; color: #14b8a6; }
              #tab-reports .dash-num { font-size: 24px; font-weight: 700; color: #181c32; line-height: 1.1; }
              #tab-reports .dash-sub { font-size: 12px; color: #a1a5b7; font-weight: 500; margin-top: 2px; }
              #tab-reports .dash-body { flex: 1; }
              #tab-reports .chart-box { width: 100%; position: relative; }
              #tab-reports .donut-body { display: flex; align-items: center; gap: 20px; }
              #tab-reports .donut-chart { width: 150px; height: 150px; flex-shrink: 0; position: relative; }
              #tab-reports .donut-legend { flex: 1; display: flex; flex-direction: column; gap: 12px; }
              #tab-reports .leg-row { display: flex; align-items: center; font-size: 13px; }
              #tab-reports .leg-dot { width: 10px; height: 10px; border-radius: 3px; margin-right: 10px; flex-shrink: 0; }
              #tab-reports .leg-label { color: #7e8299; flex: 1; }
              #tab-reports .leg-val { font-weight: 700; color: #3f4254; }
              #tab-reports .dash-select {
                padding: 6px 12px; border: 1px solid #e4e6ef; border-radius: 8px;
                font-size: 12px; font-family: "Poppins", sans-serif; color: #3f4254;
                background: #fff; cursor: pointer; outline: none;
              }
              #tab-reports .iv-item { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 10px 0; border-bottom: 1px dashed #eff2f5; }
              #tab-reports .iv-item:last-child { border-bottom: none; }
              #tab-reports .iv-date {
                width: 44px; height: 44px; border-radius: 10px; background: #f5f8fa; flex-shrink: 0;
                display: flex; flex-direction: column; align-items: center; justify-content: center; line-height: 1;
              }
              #tab-reports .iv-date .m { font-size: 10px; font-weight: 600; color: #7e8299; text-transform: uppercase; }
              #tab-reports .iv-date .d { font-size: 16px; font-weight: 800; color: #5b4dff; }
              #tab-reports .iv-name { font-size: 13px; font-weight: 600; color: #181c32; }
              #tab-reports .iv-role { font-size: 11px; color: #a1a5b7; }
              #tab-reports .iv-time { font-size: 12px; font-weight: 600; color: #3f4254; text-align: right; }
              #tab-reports .iv-mode { font-size: 11px; color: #a1a5b7; font-weight: 500; }
              #tab-reports .iv-empty { text-align: center; color: #a1a5b7; font-size: 13px; padding: 30px 0; }
              @media (max-width: 992px) {
                #tab-reports .dash-grid-2, #tab-reports .dash-grid-13 { grid-template-columns: 1fr; }
                #tab-reports .donut-body { flex-direction: column; align-items: flex-start; }
              }
            </style>
            @php
              $active_count = $counts->get('active', 0);
              $hold_count = $counts->get('hold', 0);
              $inactive_count = $counts->get('inactive', 0);
              $alljobs_count = $active_count + $hold_count + $inactive_count;
              $dcid = @$directClientDetail->client_id;
            @endphp
            <div class="dash-wrap">

              <!-- Row 1: Jobs donut + 7-day bar -->
              <div class="dash-grid dash-grid-2">
                <div class="dash-card">
                  <div class="dash-head">
                    <div class="dash-ic ic-blue"><i class="fas fa-briefcase"></i></div>
                    <div>
                      <div class="dash-num">{{ $alljobs_count }}</div>
                      <div class="dash-sub">All Jobs</div>
                    </div>
                  </div>
                  <div class="dash-body donut-body">
                    <div class="donut-chart">
                      <canvas id="allJobGrapg_{{ $dcid }}"></canvas>
                    </div>
                    <div class="donut-legend">
                      <div class="leg-row"><span class="leg-dot" style="background:#22c55e"></span><span class="leg-label">Active Jobs</span><span class="leg-val">{{ $active_count }}</span></div>
                      <div class="leg-row"><span class="leg-dot" style="background:#ffab00"></span><span class="leg-label">Hold Jobs</span><span class="leg-val">{{ $hold_count }}</span></div>
                      <div class="leg-row"><span class="leg-dot" style="background:#ff0202"></span><span class="leg-label">Closed Jobs</span><span class="leg-val">{{ $inactive_count }}</span></div>
                    </div>
                  </div>
                </div>
                <div class="dash-card">
                  <div class="dash-head">
                    <div class="dash-ic ic-green"><i class="fas fa-user-plus"></i></div>
                    <div>
                      <div class="dash-num">{{ $candidateValuesTotal }}</div>
                      <div class="dash-sub">Candidates added in last 7 days</div>
                    </div>
                  </div>
                  <div class="dash-body">
                    <div class="chart-box" style="height: 200px">
                      <canvas id="candidates_left_chart_{{ $dcid }}"></canvas>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Row 2: Candidate pipeline (full width) -->
              <div class="dash-card">
                <div class="dash-head">
                  <div class="dash-ic ic-purple"><i class="fas fa-chart-bar"></i></div>
                  <div>
                    <div class="dash-num">{{ $total_candidates }}</div>
                    <div class="dash-sub">Candidate pipeline · {{ date('d M y',strtotime($startOfMonth)) }} – {{ date('d M y',strtotime($endOfMonth)) }}</div>
                  </div>
                </div>
                <div class="dash-body">
                  <div class="chart-box">
                    <canvas id="myChart_{{ $dcid }}"></canvas>
                  </div>
                </div>
              </div>

              <!-- Row 3: Offers + Joined -->
              <div class="dash-grid dash-grid-2">
                <div class="dash-card">
                  <div class="dash-head">
                    <div class="dash-ic ic-orange"><i class="fas fa-gift"></i></div>
                    <div>
                      <div class="dash-num">{{ $candidateValuesTotaloffer }}</div>
                      <div class="dash-sub">Offers · {{ date("M Y", strtotime($startOfMonthcomman)) }} – {{ date("M Y", strtotime($endOfMonthcomman)) }}</div>
                    </div>
                  </div>
                  <div class="dash-body">
                    <div class="chart-box" style="height: 200px">
                      <canvas id="candidates_offer_chart_{{ $dcid }}"></canvas>
                    </div>
                  </div>
                </div>
                <div class="dash-card">
                  <div class="dash-head">
                    <div class="dash-ic ic-green"><i class="fas fa-handshake"></i></div>
                    <div>
                      <div class="dash-num">{{ $candidateValuesTotaljoin }}</div>
                      <div class="dash-sub">Joined · {{ date("M Y", strtotime($startOfMonthcomman)) }} – {{ date("M Y", strtotime($endOfMonthcomman)) }}</div>
                    </div>
                  </div>
                  <div class="dash-body">
                    <div class="chart-box" style="height: 200px">
                      <canvas id="candidates_join_chart_{{ $dcid }}"></canvas>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Row 4: Upcoming Interviews + Candidates Interviews -->
              <div class="dash-grid dash-grid-13">
                <div class="dash-card">
                  <div class="dash-head">
                    <div class="dash-ic ic-teal"><i class="fas fa-calendar-day"></i></div>
                    <div>
                      <div class="dash-num" style="font-size:15px;">Upcoming Interviews</div>
                      <div class="dash-sub">Scheduled candidate interviews</div>
                    </div>
                  </div>
                  <div class="dash-body">
                    @if (count($upcomingData) > 0)
                      @foreach ($upcomingData as $rowArr)
                        @php
                          $row = (object) $rowArr;
                          $schedule_date = Carbon::parse($row->schedule_date)->format('M d');
                          $schedule_date_explode = explode(" ",$schedule_date);
                        @endphp
                        <div class="iv-item">
                          <div style="display:flex;align-items:center;gap:10px;min-width:0;">
                            <div class="iv-date">
                              <span class="m">{{ $schedule_date_explode[0] }}</span>
                              <span class="d">{{ $schedule_date_explode[1] }}</span>
                            </div>
                            <div style="min-width:0;">
                              <div class="iv-name">{{ ucwords(mb_substr(@$row->full_name, 0, 20)) }}</div>
                              <div class="iv-role">{{ ucwords(mb_substr(@$row->current_designation, 0, 21)) }}</div>
                            </div>
                          </div>
                          <div>
                            <div class="iv-time">{{ $row->schedule_to_time . ($row->timezone_abbreviation ? ' (' . $row->timezone_abbreviation . ')' : '') }}</div>
                            <div class="iv-mode">{{ ucwords(mb_substr($row->mode_name, 0, 13)) }}</div>
                          </div>
                        </div>
                      @endforeach
                    @else
                      <div class="iv-empty">No records found</div>
                    @endif
                  </div>
                </div>
                <div class="dash-card">
                  <div class="dash-head" style="justify-content: space-between;">
                    <div style="display:flex;align-items:center;gap:12px;">
                      <div class="dash-ic ic-blue"><i class="fas fa-chart-column"></i></div>
                      <div>
                        <div class="dash-num" style="font-size:15px;">Candidates Interviews</div>
                        <div class="dash-sub">Interview trend</div>
                      </div>
                    </div>
                    <select class="dash-select" data-hide-search="true" data-dropdown-css-class="w-150px" onchange="filtersdatagetmonth(this.value)">
                      <option value="1">Last 6 Months</option>
                      <option value="0">Last 7 Days</option>
                    </select>
                  </div>
                  <div class="dash-body">
                    <div class="chart-box" style="height: 230px">
                      <div class="inetview_recent_chart_{{ $dcid }} d-none" style="height: 100%;">
                        <canvas id="inetview_recent_chart_{{ $dcid }}"></canvas>
                      </div>
                      <div class="inetview_recent_chart_month_{{ $dcid }}" style="height: 100%;">
                        <canvas id="inetview_recent_chart_month_{{ $dcid }}"></canvas>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>
          <!--end::Candidate Dashboard tab-->
        </div>
      </div>
    </div>
    <script>
        var myChartId = "<?php echo @$directClientDetail->client_id; ?>";
        var dashboardCharts = {};

        function fetchCandidates(page = 1, search = '', signal, seq) {
            $('.loadingMessagecandidatetList').css({ display: 'block' });
            var clientid = '{{ $directClientDetail->company_id }}';
            return $.ajax({
                url: `{{ url('service_company/staffing-client-candidate-list') }}?page=${page}`,
                type: 'GET',
                data: { search: search, clientid: clientid },
                signal: signal,
                success: function(response) {
                    if (seq && seq !== AgencyDetailTabManager.requestSequence['candidates']) return;
                    $('.loadingMessagecandidatetList').css({ display: 'none' });
                    $('#candidatetList').html(response.html);
                    $('#paginationcandidate').html(response.pagination);
                    if (response.total !== undefined) {
                        $('#tabCountCandidates').text(response.total);
                        $('#kpiCandidatesSubmitted').text(response.total);
                    }
                    AgencyDetailTabManager.initializedTabs['candidates'] = true;
                },
                error: function(xhr, status) {
                    if (status !== 'abort') {
                        $('.loadingMessagecandidatetList').css({ display: 'none' });
                    }
                }
            });
        }

        function fetchCandidatesJoin(page = 1, search = '', signal, seq) {
            $('.loadingMessagecandidatetListJoin').css({ display: 'block' });
            var clientid = '{{ $directClientDetail->company_id }}';
            return $.ajax({
                url: `{{ url('service_company/staffing-client-candidate-join-list') }}?page=${page}`,
                type: 'GET',
                data: { search: search, clientid: clientid },
                signal: signal,
                success: function(response) {
                    if (seq && seq !== AgencyDetailTabManager.requestSequence['joinings']) return;
                    $('.loadingMessagecandidatetListJoin').css({ display: 'none' });
                    $('#candidatetListJoin').html(response.html);
                    $('#paginationcandidatejoin').html(response.pagination);
                    if (response.total !== undefined) {
                        $('#tabCountJoinings').text(response.total);
                        $('#kpiSuccessfulJoins').text(response.total);
                    }
                    AgencyDetailTabManager.initializedTabs['joinings'] = true;
                },
                error: function(xhr, status) {
                    if (status !== 'abort') {
                        $('.loadingMessagecandidatetListJoin').css({ display: 'none' });
                    }
                }
            });
        }

        function fetchCandidatesInverViewTracker(page = 1, search = '', signal, seq) {
            $('.loadingMessagecandidatetListinterviewtracker').css({ display: 'block' });
            var clientid = '{{ $directClientDetail->company_id }}';
            return $.ajax({
                url: `{{ url('service_company/staffing-client-candidate-interview-tracker') }}?page=${page}`,
                type: 'GET',
                data: { search: search, clientid: clientid },
                signal: signal,
                success: function(response) {
                    if (seq && seq !== AgencyDetailTabManager.requestSequence['interviews']) return;
                    $('.loadingMessagecandidatetListinterviewtracker').css({ display: 'none' });
                    $('#candidatetListinterviewtracker').html(response.html);
                    $('#paginationcandidateinterviewtracker').html(response.pagination);
                    if (response.total !== undefined) {
                        $('#tabCountInterviews').text(response.total);
                        $('#kpiInterviewsScheduled').text(response.total);
                    }
                    AgencyDetailTabManager.initializedTabs['interviews'] = true;
                },
                error: function(xhr, status) {
                    if (status !== 'abort') {
                        $('.loadingMessagecandidatetListinterviewtracker').css({ display: 'none' });
                    }
                }
            });
        }

        function fetchstaffingpartnerJobs(page = 1, search = '', signal, seq) {
            $('.loadingMessagestaffingpartnerjob').css({ display: 'block' });
            var clientid = '{{ $directClientDetail->company_id }}';
            return $.ajax({
                url: `{{ route('service_company.consultancy.allreq_ajax') }}?page=${page}`,
                type: 'GET',
                data: {
                    search: search,
                    client_id: clientid,
                    id: clientid
                },
                signal: signal,
                success: function(response) {
                    if (seq && seq !== AgencyDetailTabManager.requestSequence['jobs']) return;
                    $('.loadingMessagestaffingpartnerjob').css({ display: 'none' });
                    $('#staffingpartnerjob').html(response.html);
                    $('#paginationstaffingpartnerjob').html(response.pagination);
                    if (response.total !== undefined) {
                        $('#tabCountJobs').text(response.total);
                        $('#kpiActiveJobs').text(response.total);
                    }
                    AgencyDetailTabManager.initializedTabs['jobs'] = true;
                },
                error: function(xhr, status) {
                    if (status !== 'abort') {
                        $('.loadingMessagestaffingpartnerjob').css({ display: 'none' });
                    }
                }
            });
        }

        function initOrUpdateChart(canvasId, type, dataObj, optionsObj) {
            var canvas = document.getElementById(canvasId);
            if (!canvas) return;
            if (dashboardCharts[canvasId]) {
                dashboardCharts[canvasId].data = dataObj;
                dashboardCharts[canvasId].update();
            } else {
                dashboardCharts[canvasId] = new Chart(canvas.getContext('2d'), {
                    type: type,
                    data: dataObj,
                    options: optionsObj || {}
                });
            }
        }

        function fetchDashboardData(signal, seq) {
            var clientid = myChartId;
            return $.ajax({
                url: `{{ url('service_company_ajax/dashboard') }}/${clientid}`,
                type: 'GET',
                signal: signal,
                success: function(res) {
                    if (seq && seq !== AgencyDetailTabManager.requestSequence['reports']) return;
                    if (res.status === 'success' && res.data) {
                        var d = res.data;

                        $('#tab-reports .dash-num:eq(0)').text(d.alljobs_count);
                        $('#tab-reports .leg-val:eq(0)').text(d.active_count);
                        $('#tab-reports .leg-val:eq(1)').text(d.hold_count);
                        $('#tab-reports .leg-val:eq(2)').text(d.inactive_count);

                        $('#tab-reports .dash-num:eq(1)').text(d.candidateValuesTotal);
                        $('#tab-reports .dash-num:eq(2)').text(d.total_candidates);
                        $('#tab-reports .dash-num:eq(3)').text(d.candidateValuesTotaloffer);
                        $('#tab-reports .dash-num:eq(4)').text(d.candidateValuesTotaljoin);

                        initOrUpdateChart('myChart_' + clientid, 'bar', {
                            labels: ['In Review','Sourcing', 'Screening', 'Evaluation', 'Negotiation', 'Offer','Joined','Rejected'],
                            datasets: [{
                                label: 'Users',
                                data: [d.inreview_count, d.sourcing_count, d.screening_count, d.evaluation_count, d.negotiation_count, d.offer_count, d.joined_count, d.Rejected_count],
                                backgroundColor: ['#bdbd26','#000000','#c000ff','#2896ff','#ff4098','#ffab00','#06c732','#ff0202'],
                                borderRadius: 10,
                                barThickness: 30
                            }]
                        }, { indexAxis: 'y', plugins: { legend: { display: false } } });

                        initOrUpdateChart('allJobGrapg_' + clientid, 'doughnut', {
                            labels: ['Active Jobs', 'Hold Jobs', 'Closed Jobs'],
                            datasets: [{
                                label: 'Total',
                                data: [d.active_count, d.hold_count, d.inactive_count],
                                backgroundColor: ['#22c55e','#ffab00','#ff0202'],
                                hoverOffset: 4
                            }]
                        }, { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, cutout: '70%' });

                        initOrUpdateChart('candidates_left_chart_' + clientid, 'bar', {
                            labels: d.labels,
                            datasets: [{
                                label: 'Total Candidates',
                                data: d.candidateValues,
                                backgroundColor: ['#2196f3','#2196f3','#2196f3','#2196f3','#2196f3','#2196f3','#2196f3'],
                                borderRadius: 5, barThickness: 30
                            }]
                        }, { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } });

                        initOrUpdateChart('candidates_offer_chart_' + clientid, 'bar', {
                            labels: d.labelsCommoffer,
                            datasets: [{
                                label: 'Total Candidates',
                                data: d.candidateValuesCommoffer,
                                backgroundColor: ['#ffab00','#ffab00','#ffab00','#ffab00','#ffab00','#ffab00'],
                                borderRadius: 5, barThickness: 30
                            }]
                        }, { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } });

                        initOrUpdateChart('candidates_join_chart_' + clientid, 'bar', {
                            labels: d.labelsCommjoin,
                            datasets: [{
                                label: 'Total Candidates',
                                data: d.candidateValuesCommjoin,
                                backgroundColor: ['#06c732','#06c732','#06c732','#06c732','#06c732','#06c732'],
                                borderRadius: 5, barThickness: 30
                            }]
                        }, { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } });

                        initOrUpdateChart('inetview_recent_chart_month_' + clientid, 'bar', {
                            labels: d.interviewlabelsMonth,
                            datasets: [{
                                label: 'Total Interviews',
                                data: d.interviewValuesMonth,
                                backgroundColor: ['#2196f3','#2196f3','#2196f3','#2196f3','#2196f3','#2196f3','#2196f3'],
                                borderRadius: 5, barThickness: 30
                            }]
                        }, { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } });

                        initOrUpdateChart('inetview_recent_chart_' + clientid, 'bar', {
                            labels: d.interviewlabels,
                            datasets: [{
                                label: 'Total Interviews',
                                data: d.interviewValues,
                                backgroundColor: ['#2196f3','#2196f3','#2196f3','#2196f3','#2196f3','#2196f3','#2196f3'],
                                borderRadius: 5, barThickness: 30
                            }]
                        }, { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } });

                        AgencyDetailTabManager.initializedTabs['reports'] = true;
                    }
                }
            });
        }

        var AgencyDetailTabManager = {
            currentAgencyId: '{{ $directClientDetail->company_id }}',
            activeTab: 'overview',
            initializedTabs: {
                overview: true,
                contacts: true,
                jobs: false,
                candidates: false,
                interviews: false,
                joinings: false,
                reports: false
            },
            activeControllers: {},
            requestSequence: {},

            resetState: function() {
                this.initializedTabs = {
                    overview: true,
                    contacts: true,
                    jobs: false,
                    candidates: false,
                    interviews: false,
                    joinings: false,
                    reports: false
                };
                this.activeControllers = {};
                this.requestSequence = {};
            },

            switchTab: function(tabElement, tabName) {
                var $scope = tabElement ? $(tabElement).closest('.tabs-bar, .drawer-content, .card, body') : $(document);
                $scope.find('.tab').removeClass('active');
                if (tabElement) $(tabElement).addClass('active');

                var $contentScope = tabElement ? $(tabElement).closest('.drawer-content, .card, body') : $(document);
                $contentScope.find('.tab-content').removeClass('active');
                var targetContainer = document.getElementById("tab-" + tabName);
                if (targetContainer) targetContainer.classList.add("active");

                this.activeTab = tabName;

                if (!this.initializedTabs[tabName]) {
                    this.loadTabData(tabName, 1, $('#search').val());
                }

                showToast(
                    tabName.charAt(0).toUpperCase() + tabName.slice(1),
                    "Switched to " + tabName + " tab"
                );
            },

            loadTabData: function(tabName, page, search) {
                page = page || 1;
                search = (search !== undefined) ? search : $('#search').val();

                if (this.activeControllers[tabName]) {
                    try { this.activeControllers[tabName].abort(); } catch(e) {}
                }

                var controller = new AbortController();
                this.activeControllers[tabName] = controller;

                var seq = (this.requestSequence[tabName] || 0) + 1;
                this.requestSequence[tabName] = seq;

                if (tabName === 'jobs') {
                    fetchstaffingpartnerJobs(page, search, controller.signal, seq);
                } else if (tabName === 'candidates') {
                    fetchCandidates(page, search, controller.signal, seq);
                } else if (tabName === 'interviews') {
                    fetchCandidatesInverViewTracker(page, search, controller.signal, seq);
                } else if (tabName === 'joinings') {
                    fetchCandidatesJoin(page, search, controller.signal, seq);
                } else if (tabName === 'reports') {
                    fetchDashboardData(controller.signal, seq);
                }
            }
        };

        function switchTab(tab, tabName) {
            AgencyDetailTabManager.switchTab(tab, tabName);
        }

        function openAgencyModal() {
            var modal = document.getElementById("agencyModal");
            if (modal) modal.classList.add("show");
            document.body.style.overflow = "hidden";
        }

        function closeAgencyModal() {
            var modal = document.getElementById("agencyModal");
            if (modal) modal.classList.remove("show");
            document.body.style.overflow = "";
        }

        function toggleTags() {
            const t1 = document.getElementById("tag1"),
                t2 = document.getElementById("tag2"),
                btn = document.getElementById("tagBtn");
            if (t1 && t2 && btn) {
                if (t1.classList.contains("hidden")) {
                    t1.classList.remove("hidden");
                    t2.classList.remove("hidden");
                    btn.textContent = "- 2 less";
                } else {
                    t1.classList.add("hidden");
                    t2.classList.add("hidden");
                    btn.textContent = "+ 2 more";
                }
            }
        }

        function toggleReadMore() {
            const text = document.getElementById("aboutText"),
                btn = document.getElementById("readMoreBtn");
            if (text && btn) {
                text.style.maxHeight = text.style.maxHeight === "none" ? "200px" : "none";
                btn.classList.toggle("expanded");
                btn.innerHTML = btn.classList.contains("expanded")
                    ? 'Read Less <i class="fas fa-chevron-down"></i>'
                    : 'Read More <i class="fas fa-chevron-down"></i>';
            }
        }

        function openPopup(type, value, name) {
            const bd = document.getElementById("popupBackdrop"),
                icon = document.getElementById("popupIcon"),
                title = document.getElementById("popupTitle"),
                desc = document.getElementById("popupDesc"),
                val = document.getElementById("popupValue"),
                link = document.getElementById("popupLink");
            if (!bd || !icon || !title || !desc || !val || !link) return;
            if (type === "email") {
                icon.className = "fas fa-envelope";
                title.textContent = "Send Email to " + name;
                desc.textContent = "Click below to open your email client";
                link.href = "mailto:" + value;
                link.innerHTML = '<i class="fas fa-external-link-alt"></i>Open Email';
            } else {
                icon.className = "fas fa-phone";
                title.textContent = "Call " + name;
                desc.textContent = "Click below to initiate the call";
                link.href = "tel:" + value.replace(/\s/g, "");
                link.innerHTML = '<i class="fas fa-phone"></i>Call Now';
            }
            val.textContent = value;
            bd.classList.add("show");
            document.body.style.overflow = "hidden";
        }

        function closePopup() {
            var bd = document.getElementById("popupBackdrop");
            if (bd) bd.classList.remove("show");
            document.body.style.overflow = "";
        }

        var popupBackdrop = document.getElementById("popupBackdrop");
        if (popupBackdrop) {
            popupBackdrop.addEventListener("click", (e) => {
                if (e.target === e.currentTarget) closePopup();
            });
        }

        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") {
                closePopup();
                closeAgencyModal();
            }
        });

        async function copyToClipboard(btn, text) {
            try {
                await navigator.clipboard.writeText(text);
            } catch (e) {
                const ta = document.createElement("textarea");
                ta.value = text;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand("copy");
                document.body.removeChild(ta);
            }
            if (btn) {
                btn.classList.add("copied");
                btn.innerHTML = '<i class="fas fa-check"></i>';
                showToast("Copied!", text + " copied to clipboard");
                setTimeout(() => {
                    btn.classList.remove("copied");
                    btn.innerHTML = '<i class="fas fa-copy"></i>';
                }, 2000);
            }
        }

        function showToast(title, message) {
            const stack = document.getElementById("toastStack");
            if (!stack) return;
            const toast = document.createElement("div");
            toast.className = "toast-item";
            toast.innerHTML =
                '<div class="toast-icon-wrap"><i class="fas fa-check"></i></div><div class="toast-body"><strong>' +
                title +
                "</strong><span>" +
                message +
                '</span></div><button class="toast-close-btn" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>';
            stack.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = "0";
                toast.style.transform = "translateX(40px)";
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        function filtersdatagetmonth(valSelected) {
            if (valSelected == 1) {
                $(`.inetview_recent_chart_month_${myChartId}`).removeClass('d-none');
                $(`.inetview_recent_chart_${myChartId}`).addClass('d-none');
            } else {
                $(`.inetview_recent_chart_month_${myChartId}`).addClass('d-none');
                $(`.inetview_recent_chart_${myChartId}`).removeClass('d-none');
            }
        }

        var searchDebounceTimer = null;

        $(document).ready(function() {
            AgencyDetailTabManager.resetState();

            $('#search').on('keyup', function() {
                clearTimeout(searchDebounceTimer);
                var searchVal = $(this).val();
                searchDebounceTimer = setTimeout(function() {
                    var activeTab = AgencyDetailTabManager.activeTab;
                    if (activeTab === 'jobs' || activeTab === 'candidates' || activeTab === 'interviews' || activeTab === 'joinings') {
                        AgencyDetailTabManager.loadTabData(activeTab, 1, searchVal);
                    } else {
                        AgencyDetailTabManager.switchTab(null, 'candidates');
                    }
                }, 300);
            });

            $(document).on('click', '#paginationcandidate .page-link', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (!page || $(this).hasClass('disabled')) return;
                AgencyDetailTabManager.loadTabData('candidates', page, $('#search').val());
            });

            $(document).on('click', '#paginationcandidatejoin .page-link, #paginationcandidatejoin .pagi-page, #paginationcandidatejoin .pagi-arrow', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (!page || $(this).hasClass('disabled') || $(this).hasClass('pagi-disabled') || $(this).hasClass('pagi-active-disabled')) return;
                AgencyDetailTabManager.loadTabData('joinings', page, $('#search').val());
            });

            $(document).on('click', '#paginationcandidateinterviewtracker .page-link, #paginationcandidateinterviewtracker .pagi-page, #paginationcandidateinterviewtracker .pagi-arrow', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (!page || $(this).hasClass('disabled') || $(this).hasClass('pagi-disabled') || $(this).hasClass('pagi-active-disabled')) return;
                AgencyDetailTabManager.loadTabData('interviews', page, $('#search').val());
            });

            $(document).on('click', '#paginationstaffingpartnerjob .page-link, #paginationstaffingpartnerjob .pagi-page, #paginationstaffingpartnerjob .pagi-arrow', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (!page || $(this).hasClass('disabled') || $(this).hasClass('pagi-disabled') || $(this).hasClass('pagi-active-disabled')) return;
                AgencyDetailTabManager.loadTabData('jobs', page, $('#search').val());
            });
        });
    </script>
    <!--end::Tab AJAX loaders-->
