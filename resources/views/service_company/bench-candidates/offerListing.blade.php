@extends('layouts.service_company_layout')
@push('head-script')
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript">
    </script>
    <style>
        #send_email .modal-dialog {
            margin: .3rem auto !important;
        }

        #fwd_resume .modal-dialog {
            margin: .3rem auto !important;
        }

        @media only screen and (min-width: 900px) and (max-width: 2560px) {

            .table td,
            .table th,
            .table tr {
                width: max-content !important;
            }

            .table> :not(caption)>*>* {
                padding: 0rem;
            }

            td:nth-child(n+2),
            th:nth-child(n+2) {
                width: revert !important;
            }
        }

        @media only screen and (min-width: 1024px) and (max-width: 1440px) {
            #kt_activitiesBenchApplication {
                width: 1025px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesBenchApplication {
                width: 1200px;
            }
        }

        @media only screen and (min-width: 1024px) and (max-width: 1440px) {
            #kt_activitiesCopy {
                width: 1025px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesCopy {
                width: 1200px;
            }
        }

        /* Shortlisted Bench Candidates hero */
        .bo-hero {
            font-family: 'Poppins', sans-serif;
            background: #fff;
            border: 1px solid #eef1f7;
            border-radius: 14px;
            padding: 18px 20px;
            margin-bottom: 16px;
            width: 100%;
            box-sizing: border-box;
        }

        .bo-hero-head {
            margin-bottom: 16px;
        }

        .bo-hero-title {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .bo-hero-title-ic {
            color: #2b59ff;
            font-size: 14px;
        }

        .bo-hero-title h2 {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
            color: #1f2a44;
        }

        .bo-hero-sub {
            margin: 4px 0 0;
            font-size: 12px;
            font-weight: 400;
            color: #8a93a8;
        }

        .bo-hero-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 12px;
            width: 100%;
        }

        .bo-stat {
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid #eef1f7;
            border-radius: 12px;
            padding: 10px 12px;
            background: #fbfcfe;
            min-width: 0;
            max-width: 100%;
            box-sizing: border-box;
            overflow: hidden;
        }

        .bo-stat .ic {
            width: 40px;
            height: 40px;
            flex: none;
            border-radius: 10px;
            display: grid;
            place-items: center;
            font-size: 16px;
        }

        .bo-stat .txt {
            min-width: 0;
        }

        .bo-stat .num {
            font-size: 16px;
            font-weight: 700;
            color: #1f2a44;
            line-height: 1.25;
        }

        .bo-stat .lbl {
            font-size: 12px;
            font-weight: 600;
            color: #3b4a66;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .bo-stat .sub {
            font-size: 11px;
            font-weight: 400;
            color: #9aa3b2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        @media only screen and (max-width: 576px) {
            .bo-hero-stats {
                grid-template-columns: 1fr;
            }

            .bo-stat .ic {
                width: 34px;
                height: 34px;
                font-size: 14px;
            }
        }

        /* Search / Location / Sort bar */
        .bo-filterbar {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            font-family: 'Poppins', sans-serif;
            margin-bottom: 12px;
        }

        .bo-search {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1 1 320px;
            min-width: 220px;
            height: 40px;
            border: 1px solid #e7ecf4;
            border-radius: 10px;
            padding: 0 14px;
            background: #fff;
            box-sizing: border-box;
        }

        .bo-search:focus-within {
            border-color: #2b59ff;
        }

        .bo-search i {
            flex: none;
            color: #9aa3b2;
            font-size: 13px;
            line-height: 1;
        }

        .bo-search input {
            flex: 1;
            min-width: 0;
            height: 100%;
            border: none;
            outline: none;
            padding: 0;
            font-size: 13px;
            font-weight: 400;
            font-family: 'Poppins', sans-serif;
            color: #1f2a44;
            background: transparent;
        }

        .bo-select-wrap,
        .bo-sort-wrap {
            flex: 0 0 auto;
        }

        .bo-select-wrap select,
        .bo-sort-wrap select {
            height: 40px;
            border: 1px solid #e7ecf4;
            border-radius: 10px;
            padding: 0 30px 0 12px;
            font-size: 13px;
            font-weight: 500;
            font-family: 'Poppins', sans-serif;
            color: #3b4a66;
            background: #fff;
            box-sizing: border-box;
        }

        .bo-sort-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .bo-sort-wrap .bo-sort-lbl {
            font-size: 12px;
            font-weight: 500;
            color: #8a93a8;
            white-space: nowrap;
        }

        .bo-sort-wrap select {
            border-color: #2b59ff;
            color: #1f2a44;
            font-weight: 600;
        }

        /* Status tabs row */
        .bo-tabsbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            font-family: 'Poppins', sans-serif;
            border-bottom: 1px solid #eef1f7;
            margin-bottom: 12px;
        }

        .bo-tabs {
            display: flex;
            align-items: center;
            gap: 4px;
            flex-wrap: wrap;
        }

        .bo-tab {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none;
            background: transparent;
            padding: 10px 12px;
            font-size: 13px;
            font-weight: 500;
            font-family: 'Poppins', sans-serif;
            color: #8a93a8;
            cursor: pointer;
            border-bottom: 2px solid transparent;
        }

        .bo-tab .cnt {
            font-size: 11px;
            font-weight: 600;
            background: #eef1f7;
            color: #6b7488;
            border-radius: 20px;
            padding: 1px 8px;
        }

        .bo-tab.active {
            color: #1f2a44;
            font-weight: 700;
            border-bottom-color: #2b59ff;
        }

        .bo-tab.active .cnt {
            background: #e9f0ff;
            color: #2b59ff;
        }

        .bo-viewtoggle {
            display: flex;
            align-items: center;
            gap: 4px;
            flex: none;
            padding-bottom: 8px;
        }

        .bo-viewtoggle button {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            border: 1px solid #e7ecf4;
            background: #fff;
            color: #8a93a8;
            font-size: 12px;
            display: grid;
            place-items: center;
            cursor: pointer;
        }

        .bo-viewtoggle button.active {
            background: #eef1ff;
            border-color: #2b59ff;
            color: #2b59ff;
        }

        .bo-viewtoggle button:disabled {
            cursor: not-allowed;
            opacity: .55;
        }

        /* Table redesign */
        #myTable {
            font-family: 'Poppins', sans-serif;
        }

        #myTable thead th {
            font-size: 11.5px;
            font-weight: 600;
            color: #8a93a8;
            text-transform: none;
            border-bottom: 1px solid #eef1f7 !important;
            white-space: nowrap;
        }

        #myTable thead th .sort-ic {
            color: #c3cad6;
            cursor: pointer;
            margin-left: 4px;
            font-size: 10px;
        }

        #myTable thead th .sort-ic:hover {
            color: #2b59ff;
        }

        #myTable tbody td {
            vertical-align: middle;
            padding-top: 10px;
            padding-bottom: 10px;
            font-size: 12px;
        }

        .bo-avatar {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: inline-grid;
            place-items: center;
            font-size: 12px;
            font-weight: 700;
            flex: none;
        }

        .bo-avatar.dup {
            box-shadow: 0 0 0 2px #ffb3b3;
        }

        .bo-cell-flex {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .bo-cell-col {
            display: flex;
            flex-direction: column;
            min-width: 0;
            line-height: 1.35;
        }

        .bo-cell-col .t1 {
            font-size: 12px;
            font-weight: 600;
            color: #1f2a44;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 220px;
        }

        .bo-cell-col .t2 {
            font-size: 10.5px;
            font-weight: 400;
            color: #8a93a8;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 220px;
        }

        .bo-cell-col .t3 {
            font-size: 10px;
            font-weight: 400;
            color: #9aa3b2;
        }

        .bo-cell-col .link {
            font-size: 10.5px;
            font-weight: 500;
            color: #2b59ff;
            text-decoration: none;
            cursor: pointer;
        }

        .bo-cell-col .link:hover {
            text-decoration: underline;
        }

        .bo-cell-col .link.accent {
            color: #f5a623;
        }

        .bo-pill {
            display: inline-block;
            font-size: 10px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            white-space: nowrap;
        }

        .bo-pill-green { background: #e6f9ee; color: #16a34a; }
        .bo-pill-red { background: #fdecef; color: #e5484d; }
        .bo-pill-amber { background: #fff3e2; color: #b46b00; }
        .bo-pill-indigo { background: #eef0ff; color: #5b3df5; }
        .bo-pill-blue { background: #e9f0ff; color: #2b59ff; }
        .bo-pill-gray { background: #f3f5f9; color: #6b7488; }

        .bo-pill-outline {
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            border: 1px solid #f5a623;
            color: #f5a623;
            background: #fff;
            white-space: nowrap;
        }

        .bo-joining-rel {
            font-size: 9.5px;
            font-weight: 600;
        }

        .bo-joining-rel.future { color: #f5a623; }
        .bo-joining-rel.past,
        .bo-joining-rel.none { color: #9aa3b2; }

        @media only screen and (max-width: 1366px) {
            .bo-cell-col .t1,
            .bo-cell-col .t2 {
                max-width: 160px;
            }
        }

        @media only screen and (max-width: 992px) {
            .bo-tabsbar {
                overflow-x: auto;
            }
        }

        /* ============================================================
           Change Talent Status drawer — same drawer used on the main
           Inhouse Talents (bench-candidates) page, so clicking Status
           here opens the identical UI. Positioning/overlay/width owned
           by the drawer plugin; below is content styling only.
           ============================================================ */
        #bstatus_drawer, #bstatus_drawer * { font-family: 'Poppins', sans-serif !important; box-sizing: border-box; }
        #bstatus_drawer { z-index: 999999; font-size: 10.5px; }
        #bstatus_drawer .min-w-0 { min-width: 0; }

        .bstatus-topbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 14px 22px; background: #fff; border-bottom: 1px solid #eef0f4; flex-shrink: 0; }
        .bstatus-topbar-left { display: flex; align-items: center; gap: 12px; }
        .bstatus-title-ico { width: 40px; height: 40px; border-radius: 10px; background: #eef3ff; color: #3e97ff; display: flex; align-items: center; justify-content: center; font-size: 15px; }
        .bstatus-title { font-size: 16.5px; font-weight: 700; color: #181c32; line-height: 1.2; }
        .bstatus-topbar-center { display: flex; flex-direction: column; align-items: center; }
        .bstatus-changing { font-size: 10.5px; color: #7e8299; }
        .bstatus-pill { border-radius: 8px; padding: 6px 18px; font-size: 12px; font-weight: 700; }
        .bstatus-pill.cur { background: #eef3ff; color: #3e97ff; border: 1px solid #dce8ff; }
        .bstatus-pill.new { background: #f6f7f9; color: #a1a5b7; border: 1px solid #e4e6ef; }
        .bstatus-pill.new.filled { background: #e8fff3; color: #17c653; border-color: #c8f4dc; }
        .bstatus-arrow { color: #b5b9c8; font-size: 12px; }
        .bstatus-topbar-right { display: flex; align-items: center; gap: 10px; }

        .bstatus-btn-cancel { border: 1px solid #e4e6ef; background: #fff; color: #3f4254; border-radius: 9px; padding: 9px 20px; font-weight: 600; font-size: 11.5px; }
        .bstatus-btn-cancel:hover { border-color: #d5dbe6; background: #f9fafc; }
        .bstatus-btn-confirm { border: none; background: #17c653; color: #fff; border-radius: 9px; padding: 9px 20px; font-weight: 600; font-size: 11.5px; display: inline-flex; align-items: center; gap: 8px; }
        .bstatus-btn-confirm:disabled { background: #c2c9d6; cursor: not-allowed; }
        .bstatus-btn-confirm:not(:disabled):hover { background: #14b04a; }
        .bstatus-close { width: 36px; height: 36px; border-radius: 9px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; color: #7e8299; font-size: 14px; }
        .bstatus-close:hover { background: #f4f5f8; color: #181c32; }

        .bstatus-body { overflow-y: auto; background: #f5f6fa; padding: 16px 22px; flex-grow: 1; }
        .bs-card { background: #fff; border: 1px solid #eef0f4; border-radius: 14px; padding: 18px 22px; }
        .bs-card + .bs-card { margin-top: 14px; }

        .bs-cand-bar { display: flex; align-items: center; gap: 0; }
        .bs-cand-id { display: flex; align-items: center; gap: 14px; padding-right: 26px; }
        .bs-cand-avatar { width: 56px; height: 56px; border-radius: 50%; background: #efeafe; color: #7239ea; display: flex; align-items: center; justify-content: center; font-size: 16.5px; font-weight: 700; flex-shrink: 0; }
        .bs-cand-name { font-size: 16.5px; font-weight: 700; color: #181c32; line-height: 1.2; }
        .bs-cand-role { font-size: 11.5px; color: #7e8299; }
        .bs-cand-stat { flex: 1 1 0; min-width: 0; text-align: center; padding: 0 12px; border-left: 1px solid #eef0f4; }
        .bs-cand-stat .v { font-size: 17.5px; font-weight: 700; color: #181c32; line-height: 1.1; }
        .bs-cand-stat .l { font-size: 10px; color: #7e8299; margin-top: 2px; }

        .bs-sec-head { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }
        .bs-sec-num { width: 26px; height: 26px; border-radius: 50%; background: #3e97ff; color: #fff; font-size: 11.5px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .bs-sec-title { font-size: 14px; font-weight: 700; color: #181c32; }

        .bs-status-row { display: flex; align-items: stretch; gap: 0; }
        .bs-status-cell { flex: 1 1 0; min-width: 0; }
        .bs-status-arrow { display: flex; align-items: center; justify-content: center; color: #c2c9d6; font-size: 12px; padding: 0 8px; flex-shrink: 0; }
        .bs-status-card { border: 1.5px solid #eef0f4; border-radius: 14px; padding: 18px; cursor: pointer; background: #fff; height: 100%; transition: border-color .15s, background .15s, box-shadow .15s; }
        .bs-status-card:hover { border-color: #cdd4e0; }
        .bs-status-card-top { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
        .bs-status-card .ic { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }
        .bs-status-card .nm { font-size: 14px; font-weight: 700; }
        .bs-status-card .sb { font-size: 10.5px; color: #7e8299; line-height: 1.4; }
        .bs-status-card.is-current { border-color: #3e97ff; background: #f5f9ff; }
        .bs-status-card.is-selected { border-color: #17c653; background: #f2fdf7; box-shadow: 0 0 0 1px #17c653 inset; }

        .bs-impact { background: #f2fdf7; border: 1px solid #d5f2e2; border-radius: 12px; padding: 16px 18px; margin-top: 16px; }
        .bs-impact-head { display: flex; align-items: center; gap: 7px; font-size: 11.5px; font-weight: 700; color: #17845a; margin-bottom: 12px; }
        .bs-impact-item { display: flex; align-items: flex-start; gap: 8px; font-size: 11px; color: #3f4254; font-weight: 500; }
        .bs-impact-item i { color: #17c653; font-size: 11.5px; margin-top: 1px; flex-shrink: 0; }

        .bs-job-table { border: 1px solid #eef0f4; border-radius: 12px; overflow: hidden; }
        .bs-job-table table { width: 100%; margin: 0; table-layout: fixed; }
        .bs-job-table thead th { font-size: 10px; color: #a1a5b7; font-weight: 600; padding: 12px 10px; background: #f8f9fb; border-bottom: 1px solid #eef0f4; white-space: nowrap; text-align: left; }
        .bs-job-table td { font-size: 11.5px; padding: 14px 10px; border-bottom: 1px solid #f4f5f8; vertical-align: middle; }
        .bs-job-table th:nth-child(1), .bs-job-table td:nth-child(1) { width: 21%; }
        .bs-job-table th:nth-child(2), .bs-job-table td:nth-child(2) { width: 20%; }
        .bs-job-table th:nth-child(3), .bs-job-table td:nth-child(3) { width: 15%; }
        .bs-job-table th:nth-child(4), .bs-job-table td:nth-child(4) { width: 13%; }
        .bs-job-table th:nth-child(5), .bs-job-table td:nth-child(5) { width: 11%; }
        .bs-job-table th:nth-child(6), .bs-job-table td:nth-child(6) { width: 8%; }
        .bs-job-table th:nth-child(7), .bs-job-table td:nth-child(7) { width: 12%; }
        .bs-job-table tbody tr:last-child td { border-bottom: none; }
        .bs-job-table tbody tr:hover td { background: #fafbfc; }
        .bs-job-table .fs-6, .bs-job-table .fw-bolder { font-size: 11.5px !important; font-weight: 700 !important; color: #181c32 !important; }
        .bs-job-table .fs-7 { font-size: 10px !important; color: #7e8299 !important; font-weight: 500 !important; }
        .bs-job-table .badge { font-size: 10px !important; font-weight: 700 !important; padding: 4px 11px !important; border-radius: 7px !important; }
        .bs-job-cell { display: flex; align-items: center; gap: 10px; min-width: 0; }
        .bs-job-cell .symbol { margin: 0 !important; flex-shrink: 0; }
        .bs-job-cell .d-flex.flex-column { min-width: 0; }
        .bs-exp-row { display: flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 600; color: #181c32; }
        .bs-exp-row + .bs-exp-row { margin-top: 4px; }
        .bs-exp-row i { width: 12px; text-align: center; color: #a1a5b7; font-size: 10px; flex-shrink: 0; }
        .bs-exp-row.client { color: #3e97ff; }
        .bs-exp-row.client .ref { color: #a1a5b7; font-weight: 500; }
        .bs-place-btn { border: 1px solid #cdd9f7; background: #fff; color: #3e97ff; border-radius: 8px; padding: 7px 10px; font-size: 10.5px; font-weight: 600; cursor: pointer; white-space: nowrap; display: inline-flex; align-items: center; gap: 5px; width: 100%; justify-content: center; }
        .bs-place-btn:hover { background: #eef5ff; }
        .bs-place-btn.is-picked { border-color: #3e97ff; background: #3e97ff; color: #fff; }
        .bs-place-btn:disabled { background: #f1f2f6; color: #a1a5b7; border-color: #eef0f4; cursor: not-allowed; }

        @media (max-width: 1100px) {
            .bs-status-row { flex-wrap: wrap; }
            .bs-status-arrow { display: none; }
            .bs-status-cell { flex: 1 1 30%; }
        }
    </style>
@endpush

@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar d-none" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid">
            <input type="hidden" id="inputSearch" name="inputSearch" />
        </div>
    </div>
    <!--end::Toolbar-->
    @php
        $benchOfferHeroStats = $benchOfferHeroStats ?? [
            'total_shortlisted' => 0,
            'joining_this_week' => 0,
            'offer_released' => 0,
            'offer_accepted' => 0,
            'joined' => 0,
        ];
        $benchOfferTabStats = $benchOfferTabStats ?? [
            'all' => 0,
            'pending' => 0,
            'released' => 0,
            'accepted' => 0,
            'joining_soon' => 0,
            'expired' => 0,
        ];
        $benchOfferLocations = $benchOfferLocations ?? [];
    @endphp
    @if (in_array('view_bench', $userPermissions))
        <!--begin::Content-->
        <div class="content d-flex flex-column flex-column-fluid pt-0" id="kt_content" style="margin-top:-42px">
            <div class="post d-flex flex-column-fluid" id="kt_post">
                <div id="kt_content_container" class="container-fluid px-3">

                    <!--begin::Bench offer hero-->
                    <div class="bo-hero">
                        <div class="bo-hero-head">
                            <div class="bo-hero-title">
                                <span class="bo-hero-title-ic"><i class="fa-solid fa-bookmark"></i></span>
                                <h2>Shortlisted Bench Candidates</h2>
                            </div>
                            <p class="bo-hero-sub">Track and manage your bench candidates who are shortlisted/selected with external companies/projects.</p>
                        </div>
                        <div class="bo-hero-stats">
                            <div class="bo-stat">
                                <span class="ic" style="background:#e9f0ff;color:#2b59ff"><i class="fa-solid fa-users"></i></span>
                                <div class="txt">
                                    <div class="num" id="totalBenchShortlist">{{ number_format($benchOfferHeroStats['total_shortlisted']) }}</div>
                                    <div class="lbl">Total Shortlisted</div>
                                    <div class="sub">All Shortlisted Candidates</div>
                                </div>
                            </div>
                            <div class="bo-stat">
                                <span class="ic" style="background:#f1ecff;color:#7c3aed"><i class="fa-solid fa-calendar-days"></i></span>
                                <div class="txt">
                                    <div class="num">{{ number_format($benchOfferHeroStats['joining_this_week']) }}</div>
                                    <div class="lbl">Joining This Week</div>
                                    <div class="sub">Next 7 Days</div>
                                </div>
                            </div>
                            <div class="bo-stat">
                                <span class="ic" style="background:#fff3e2;color:#f5a623"><i class="fa-solid fa-paper-plane"></i></span>
                                <div class="txt">
                                    <div class="num">{{ number_format($benchOfferHeroStats['offer_released']) }}</div>
                                    <div class="lbl">Offer Released</div>
                                    <div class="sub">Waiting for Acceptance</div>
                                </div>
                            </div>
                            <div class="bo-stat">
                                <span class="ic" style="background:#e6f9ee;color:#16a34a"><i class="fa-solid fa-circle-check"></i></span>
                                <div class="txt">
                                    <div class="num">{{ number_format($benchOfferHeroStats['offer_accepted']) }}</div>
                                    <div class="lbl">Offer Accepted</div>
                                    <div class="sub">Candidate Accepted</div>
                                </div>
                            </div>
                            <div class="bo-stat">
                                <span class="ic" style="background:#e6fbf8;color:#14b8a6"><i class="fa-solid fa-check-double"></i></span>
                                <div class="txt">
                                    <div class="num">{{ number_format($benchOfferHeroStats['joined']) }}</div>
                                    <div class="lbl">Joined</div>
                                    <div class="sub">Successfully Joined</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Bench offer hero-->


                    <!--begin::Search / Location / Sort-->
                    <div class="bo-filterbar">
                        <div class="bo-search">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" id="kt_tagify_2" name="inputJobSearch"
                                placeholder="Search by candidate name, job or offer ID..." />
                        </div>
                        <div class="bo-select-wrap">
                            <select id="country" onchange="filtersdataget()">
                                <option value="all" selected>All Locations</option>
                                @foreach ($benchOfferLocations as $loc)
                                    <option value="{{ $loc }}">{{ $loc }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="bo-sort-wrap">
                            <span class="bo-sort-lbl">Sort by:</span>
                            <select id="sortingfilter" onchange="filtersdataget()">
                                <option value="Show All" selected>Latest Updated</option>
                                <option value="10">Newest to Oldest (Offer Date)</option>
                                <option value="11">Oldest to Newest (Offer Date)</option>
                                <option value="12">Newest to Oldest (Join Date)</option>
                                <option value="13">Oldest to Newest (Join Date)</option>
                                <option value="14">Low to High (Offer Amount)</option>
                                <option value="15">High to Low (Offer Amount)</option>
                            </select>
                        </div>
                    </div>
                    <!--end::Search / Location / Sort-->

                    <!--begin::Status tabs-->
                    <div class="bo-tabsbar">
                        <div class="bo-tabs" id="boTabs">
                            <button type="button" class="bo-tab active" data-tab="all">All <span class="cnt">{{ number_format($benchOfferTabStats['all']) }}</span></button>
                            <button type="button" class="bo-tab" data-tab="pending">Pending <span class="cnt">{{ number_format($benchOfferTabStats['pending']) }}</span></button>
                            <button type="button" class="bo-tab" data-tab="released">Offer Released <span class="cnt">{{ number_format($benchOfferTabStats['released']) }}</span></button>
                            <button type="button" class="bo-tab" data-tab="accepted">Accepted <span class="cnt">{{ number_format($benchOfferTabStats['accepted']) }}</span></button>
                            <button type="button" class="bo-tab" data-tab="joining_soon">Joining Soon <span class="cnt">{{ number_format($benchOfferTabStats['joining_soon']) }}</span></button>
                            <button type="button" class="bo-tab" data-tab="expired">Expired <span class="cnt">{{ number_format($benchOfferTabStats['expired']) }}</span></button>
                        </div>
                        <div class="bo-viewtoggle">
                            <button type="button" class="active" title="List view"><i class="fa-solid fa-list"></i></button>
                            <button type="button" disabled title="Grid view (coming soon)"><i class="fa-solid fa-table-cells-large"></i></button>
                        </div>
                    </div>
                    <input type="hidden" id="tabFilter" value="all" />
                    <!--end::Status tabs-->

                    <div class="card">
                        <div class="card-body pt-3 px-5">
                            <div class="loadingMessage loadingMessagebenchCandidateOfferAjax"
                                style="display: none; position: absolute;">Processing...</div>
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="myTable">
                                <thead>
                                    <tr class="fw-bolder text-muted" role="row">
                                        <th class="">Candidate</th>
                                        <th class="">Job</th>
                                        <th class="">Job Status, ID &amp; Client</th>
                                        <th class="">Compensation <i class="fa-solid fa-arrow-up-wide-short sort-ic" data-sort-asc="14" data-sort-desc="15" title="Sort by compensation"></i></th>
                                        <th class="">Offer Status <i class="fa-solid fa-arrow-up-wide-short sort-ic" data-sort-asc="11" data-sort-desc="10" title="Sort by offer date"></i></th>
                                        <th class="">Joining <i class="fa-solid fa-arrow-up-wide-short sort-ic" data-sort-asc="13" data-sort-desc="12" title="Sort by joining date"></i></th>
                                        <th class="">Stage</th>
                                        <th class="">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="benchCandidateOfferAjax"> </tbody>
                            </table>
                            <div id="paginationbenchCandidateOffer"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Content-->
    @endif

    <div id="kt_activitiesHM_toggle"></div>
    <div id="kt_activitiesHM" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
        data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#kt_activitiesHM_toggle" data-kt-drawer-close="#kt_activitiesHM_close">
        <div class="card shadow-none rounded-0 w-100">
            <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                    <h3 class="card-title fw-bolder text-dark">Team</h3>
                    <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5"
                        id="kt_activitiesHM_close"> <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                    fill="#000000">
                                    <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1">
                                    </rect>
                                    <rect fill="#000000" opacity="0.5"
                                        transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                        x="0" y="7" width="16" height="2" rx="1"></rect>
                                </g>
                            </svg>
                        </span> </button>
                </div>
            </div>
            <div class="card-body position-relative px-0 py-0" id="kt_activitiesHM_body"> </div>
        </div>
    </div>


    <div id="kt_activitiesBA_toggle"></div>
    <div id="kt_activitiesBA" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
        data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#kt_activitiesBA_toggle" data-kt-drawer-close="#kt_activitiesBA_close">
        <div class="card shadow-none rounded-0 w-100">
            <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                    <h3 class="card-title fw-bolder text-dark">Inhouse Talent Shortlisted for below Jobs</h3>
                    <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5"
                        id="kt_activitiesBA_close"> <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                    fill="#000000">
                                    <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1">
                                    </rect>
                                    <rect fill="#000000" opacity="0.5"
                                        transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                        x="0" y="7" width="16" height="2" rx="1"></rect>
                                </g>
                            </svg>
                        </span> </button>
                </div>
            </div>
            <div class="card-body position-relative px-0 py-0" id="kt_activitiesBA_body"> </div>
        </div>
    </div>

    <div class="modal fade" id="stage_sub_stage" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header flex-stack">
                    <h2>Inhouse Talent Status</h2>
                    <div class="btn btn-sm btn-icon btn-active-light-danger close" data-dismiss="modal"
                        id="stage_sub_stage_close">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="currentColor">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="stage_body_content" id="stage_body_content"></div>
            </div>
        </div>
    </div>
@endsection

@push('footer-script')
    <script>
        let searchTimer = null;
        $(document).ready(function() {
            $(document).on('keyup', '#inputSearch, #kt_tagify_2', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    fetchBenchBenchCandidatesCandidateList();
                }, 300);
            });
        });

        function filtersdataget() {
            fetchBenchBenchCandidatesCandidateList();
        }

        $(document).on('click', '.hm-listing', function() {
            $('#kt_activitiesHM_toggle').trigger('click');
            var id = $(this).data('row-id');
            var url = "{{ route('service_company.bench-candidates.hmlisting', ':id') }}";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            $.ajax({
                type: 'GET',
                url: url,
                beforeSend: function() {
                    $('#kt_activitiesHM_body').html('<img src="' + baseurl +
                        '" height="35px" width="100%">');
                },
                success: function(response) {
                    setTimeout(() => {
                        if (response.status == "success") {
                            $('#kt_activitiesHM_body').html(response.view);
                        }
                    }, 500);
                }
            });
        });

        $(document).on('click', '.bench_assign_to_job', function() {
            $('#kt_activitiesBA_toggle').trigger('click');
            var id = $(this).data('row-id');
            var url = "{{ route('service_company.bench-candidates.bench_assign_to_job', ':id') }}";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            $.ajax({
                type: 'GET',
                url: url,
                beforeSend: function() {
                    $('#kt_activitiesBA_body').html('<img src="' + baseurl +
                        '" height="35px" width="100%">');
                },
                success: function(response) {
                    setTimeout(() => {
                        if (response.status == "success") {
                            $('#kt_activitiesBA_body').html(response.view);
                        }
                    }, 500);
                }
            });
        });

        $(document).on('click', '.stage_sub_stage', function() {
            $('#stage_body_content').html('');
            var id = $(this).data('row-id');
            var placedID = $(this).data('placedid');
            var url = "{{ route('service_company.bench-candidates.bench_application_status', ':id') }}?placedid=" + placedID;
            url = url.replace(':id', id);
            $.ajax({
                type: 'GET',
                url: url,
                success: function(response) {
                    if (response.status == "success") {
                        $('#stage_body_content').html(response.view);
                    }
                }
            });
        });

        $(document).on('click', '.bo-tab', function() {
            $('.bo-tab').removeClass('active');
            $(this).addClass('active');
            $('#tabFilter').val($(this).data('tab'));
            fetchBenchBenchCandidatesCandidateList();
        });

        $(document).on('click', '.sort-ic', function() {
            var $el = $(this);
            var asc = String($el.data('sort-asc'));
            var desc = String($el.data('sort-desc'));
            var current = $('#sortingfilter').val();
            $('#sortingfilter').val(current === desc ? asc : desc);
            filtersdataget();
        });

        $(document).ready(function() {
            fetchBenchBenchCandidatesCandidateList();
            $(document).on('click', '#paginationbenchCandidateOffer .pagi-page, #paginationbenchCandidateOffer .pagi-arrow', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (!page || $(this).hasClass('pagi-disabled') || $(this).hasClass('pagi-active-disabled')) return;

                $(this).addClass('disabled');

                fetchBenchBenchCandidatesCandidateList(page).always(function() {
                    $('#paginationbenchCandidateOffer .pagi-page, #paginationbenchCandidateOffer .pagi-arrow').removeClass('disabled');
                });
            });

            $(document).on('change', '#paginationbenchCandidateOffer .pagi-perpage', function(e) {
                const selectedPerPage = $(this).val();
                if (selectedPerPage) {
                    localStorage.setItem('candidatePerPage', selectedPerPage);
                    fetchBenchBenchCandidatesCandidateList(1);
                }
            });
        });

        function fetchBenchBenchCandidatesCandidateList(page = 1) {
            $('.loadingMessagebenchCandidateOfferAjax').css({ display: 'block' });
            var perPage = localStorage.getItem('candidatePerPage') || 10;
            return $.ajax({
                url: `{!! route('service_company.bench-candidates.offerListingData_ajax') !!}?page=${page}`,
                type: 'GET',
                data: {
                    search: $('#inputSearch').val(),
                    cus_search: $('#kt_tagify_2').val(),
                    country: $('#country').val(),
                    sortingfilter: $('#sortingfilter').val(),
                    tab: $('#tabFilter').val(),
                    per_page: perPage,
                    size: perPage
                },
                success: function(response) {
                    $('.loadingMessagebenchCandidateOfferAjax').css({ display: 'none' });
                    $('#totalBenchShortlist').html(response.total);
                    $('#benchCandidateOfferAjax').html(response.html);
                    $('#paginationbenchCandidateOffer').html(response.pagination);
                },
                error: function() {
                    $('.loadingMessagebenchCandidateOfferAjax').css({ display: 'none' });
                }
            });
        }
    </script>
@endpush
